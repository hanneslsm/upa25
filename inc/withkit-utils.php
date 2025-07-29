<?php
/**
 * Plugin Name:  withkit Utility Classes Panel
 * Description:  Gutenberg-Inspector panel for helper classes.
 *               – “Default” tab for global helpers.
 *               – One tab per uncommented @include line (with-mobile, with-medium …).
 *               – “with-” becomes “…” in labels.
 *               – Horizontally-scrollable tab bar with extra bottom padding.
 *               – Blue dot after the main title and every tab that owns ≥ 1 active class.
 * Requires at least: WP 6.3, PHP 8.1
 *
 * @version 2.3.0
 */

declare(strict_types=1);

namespace withkit;

/* -------------------------------------------------------------------------
 * Assets
 * ---------------------------------------------------------------------- */
add_action(
	'enqueue_block_editor_assets',
	static function (): void {
		wp_register_script(
			'withkit-class-panel',
			'',
			[ 'wp-block-editor', 'wp-components', 'wp-data', 'wp-element', 'wp-hooks' ],
			null,
			true
		);
		wp_enqueue_script( 'withkit-class-panel' );

		wp_add_inline_script(
			'withkit-class-panel',
			'window.withkit_ITEMS = ' . wp_json_encode( collect_items(), JSON_THROW_ON_ERROR ),
			'before'
		);
		wp_add_inline_script( 'withkit-class-panel', inline_js(), 'after' );
	}
);

/* -------------------------------------------------------------------------
 * helpers.scss → ITEMS
 * ---------------------------------------------------------------------- */
/**
 * Parse helpers.scss and return the flat ITEM list the panel needs.
 * - “Default” tab = all helpers defined outside the responsive mixin.
 * - Every uncommented  @include responsive-styles(...)  becomes a tab.
 * - Inside each tab, the 3 mixin sections (Display / Order / Alignment)
 *   appear in the order they occur in the SCSS, each followed by its own
 *   classes.
 */
function collect_items(): array {
	$src = get_theme_file_path( 'src/scss/utilities/withkit-helpers.scss' );
	if ( ! is_readable( $src ) ) {
		return [];
	}

	$lines         = file( $src );
	$defaultItems  = [];
	$breakpoints   = [];                 // with-mobile → mobile
	$sections      = [];                 // label → [ 'desc'=>, 'suffixes'=>[] ]
	$sectionOrder  = [];                 // preserve definition order
	$curSection    = null;

	$inDoc       = false;
	$docT        = $docD = null;
	$insideMixin = false;
	$braceDepth  = 0;

	$flush_doc = static function () use (
		&$inDoc, &$docT, &$docD, &$insideMixin,
		&$defaultItems, &$sections, &$sectionOrder, &$curSection
	): void {
		if ( $docT === null && $docD === null ) {
			return;
		}
		if ( $insideMixin ) {
			$label = trim( $docT ?? '' );
			if ( $label !== '' ) {
				$sections[ $label ] = [
					'desc'     => trim( $docD ?? '' ),
					'suffixes' => [],
				];
				$sectionOrder[] = $label;
				$curSection = $label;
			}
		} else {
			$defaultItems[] = [
				'type'  => 'heading',
				'label' => trim( $docT ?? '' ),
				'desc'  => trim( $docD ?? '' ),
			];
		}
		$docT = $docD = null;
	};

	foreach ( $lines as $raw ) {
		$line = ltrim( $raw );

		/* skip fully commented-out lines -------------------------------- */
		if ( str_starts_with( $line, '//' ) ) {
			continue;
		}

		/* enter / leave mixin ------------------------------------------- */
		if ( preg_match( '/@mixin\s+responsive-styles\(/', $line ) ) {
			$insideMixin = true;
			$braceDepth  = substr_count( $line, '{' ) - substr_count( $line, '}' );
		} elseif ( $insideMixin ) {
			$braceDepth += substr_count( $line, '{' ) - substr_count( $line, '}' );
			if ( $braceDepth <= 0 ) {
				$insideMixin = false;
				$curSection  = null;
			}
		}

		/* doc-block capture --------------------------------------------- */
		if ( ! $inDoc && str_starts_with( $line, '/**' ) ) {
			$inDoc = true;
			$docT = $docD = null;
			continue;
		}
		if ( $inDoc ) {
			if ( preg_match( '/\*\s*Title:\s*(.+)/', $line, $m ) ) {
				$docT = $m[1];
			} elseif ( preg_match( '/\*\s*Description:\s*(.+)/', $line, $m ) ) {
				$docD = $m[1];
			} elseif ( str_contains( $line, '*/' ) ) {
				$inDoc = false;
			}
			continue;
		}

		/* breakpoint include (only if not commented-out) ----------------- */
		if ( preg_match( '/@include\s+responsive-styles\([^,]+,\s*"?(with-[\w-]+)"?/', $line, $m ) ) {
			$prefix               = $m[1];
			$breakpoints[$prefix] = str_replace( 'with-', '', $prefix );
			continue;
		}

		/* flush a heading just before its first selector ----------------- */
		if ( str_contains( $line, '.' ) ) {
			$flush_doc();
		}

		/* suffixes inside the mixin ------------------------------------- */
		if ( $insideMixin && $curSection && preg_match( '/\.#\{\$prefix}-([\w-]+)/', $line, $m ) ) {
			$sections[ $curSection ]['suffixes'][] = $m[1];
			continue;
		}

		/* plain helpers (default tab) ----------------------------------- */
		if ( ! $insideMixin && preg_match_all( '/\.([\w-]+)/', $line, $m ) ) {
			foreach ( $m[1] as $cls ) {
				if ( ! ctype_digit( $cls ) ) {
					$defaultItems[] = [ 'type' => 'class', 'name' => $cls ];
				}
			}
		}
	}
	$flush_doc(); // in case file ends inside a doc-block

	/* assemble ITEM list ------------------------------------------------- */
	$items = $defaultItems;

	foreach ( $breakpoints as $prefix => $label ) {
		$items[] = [ 'type' => 'heading', 'label' => $label, 'prefix' => $prefix, 'bp' => true ];

		foreach ( $sectionOrder as $lbl ) {
			$meta = $sections[ $lbl ];
			$items[] = [ 'type' => 'heading', 'label' => $lbl, 'desc' => $meta['desc'] ];

			foreach ( $meta['suffixes'] as $suf ) {
				$items[] = [ 'type' => 'class', 'name' => "{$prefix}-{$suf}" ];
			}
		}
	}

	return $items;
}


/* -------------------------------------------------------------------------
 * React (inline)
 * ---------------------------------------------------------------------- */
function inline_js(): string {
	return <<<'JS'
(() => {
	const { createElement: el, Fragment, useState } = wp.element;
	const {
		PanelBody, ToggleControl, SearchControl, TabPanel, BaseControl
	} = wp.components;
	const { InspectorControls } = wp.blockEditor;
	const { useSelect, useDispatch } = wp.data;
	const { addFilter } = wp.hooks;

	const ITEMS = Array.isArray( window.withkit_ITEMS ) ? window.withkit_ITEMS : [];

	/* one-time CSS ------------------------------------------------------- */
	if ( ! document.getElementById( 'withkit-style' ) ) {
		const s = document.createElement( 'style' );
		s.id = 'withkit-style';
		s.textContent = `
			.withkit-tabs .components-tab-panel__tabs{
				display:flex;flex-wrap:nowrap;gap:4px;
				overflow-x:auto;scrollbar-width:thin;margin-bottom:14px;
			}
			.withkit-tabs .components-tab-panel__tabs::-webkit-scrollbar{height:6px}
			.withkit-tabs .components-tab-panel__tabs button{
				white-space:nowrap;word-break:keep-all;
			}`;
		document.head.appendChild( s );
	}

	/* build tabsMap ------------------------------------------------------ */
	const tabsMap = new Map( [ [ 'default', { title:'Default', items:[] } ] ] );
	let current = 'default';

	for ( const it of ITEMS ) {
		if ( it.bp ) {
			current = it.prefix;
			tabsMap.set( current, { title:it.label, items:[] } );
			continue;
		}
		tabsMap.get( current ).items.push( it );
	}

	/* helpers ------------------------------------------------------------ */
	const Dot = () => el('span',{style:{
		display:'inline-block',width:'0.45em',height:'0.45em',
		borderRadius:'50%',background:'var(--wp-admin-theme-color,#007cba)'
	}});

	const Section = ({ title, desc, uniq }) =>
		el( BaseControl, {
			key: uniq,
			label: title,
			help:  desc || null,
			className: 'withkit-section-label',
			__nextHasNoMarginBottom: true,
		});

	const shortLabel = (cls, tab) =>
		cls.startsWith('with-')
			? ( tab !== 'default' && cls.startsWith(tab + '-') )
				? '…' + cls.slice( tab.length + 1 )
				: '…' + cls.slice(5)
			: cls;

	/* component ---------------------------------------------------------- */
	const ClassPanel = ({ clientId }) => {
		const { className = '' } = useSelect( sel =>
			sel('core/block-editor').getBlockAttributes( clientId ) );
		const { updateBlockAttributes } = useDispatch('core/block-editor');

		const activeSet   = new Set( className.split(/\s+/).filter(Boolean) );
		const [q, setQ]   = useState('');

		const toggle = cls => {
			activeSet.has(cls) ? activeSet.delete(cls) : activeSet.add(cls);
			updateBlockAttributes( clientId, { className:[...activeSet].join(' ') } );
		};

		const tabs = Array.from( tabsMap, ([ name, obj ]) => {
			const has = obj.items.some(it => it.type==='class' && activeSet.has(it.name));
			return {
				name,
				title: el('span',{
					style:{display:'flex',alignItems:'center',gap:'0.25em'}
				},[ obj.title, has ? el(Dot) : null ]),
			};
		});

		const header = el('span',{
			style:{display:'flex',alignItems:'center',gap:'0.4em'}
		},[ 'Utility classes', activeSet.size ? el(Dot) : null ]);

		const renderTab = tab => {
			const pool = tabsMap.get(tab.name).items;
			const list = q
				? pool.filter(it =>
						it.type==='class'
							? it.name.toLowerCase().includes(q.toLowerCase())
							: it.label.toLowerCase().includes(q.toLowerCase()))
				: pool;

			return el(Fragment,null,
				list.length
					? list.map(it =>
							it.type==='heading'
								? Section({ title:it.label, desc:it.desc, uniq:it.label + tab.name })
								: el(ToggleControl,{
										key:it.name,
										label:shortLabel(it.name,tab.name),
										checked:activeSet.has(it.name),
										onChange:()=>toggle(it.name),
								  }))
					: el('p',{style:{opacity:0.6}},'No matches')
			);
		};

		return el(InspectorControls,{group:'settings'},
			el(PanelBody,{title:header,initialOpen:false},
				el(SearchControl,{
					value:q,
					placeholder:'Search classes…',
					onChange:setQ,
					__nextHasNoMarginBottom:true,
				}),
				el(TabPanel,{className:'withkit-tabs',tabs},renderTab)
			)
		);
	};

	/* HOC --------------------------------------------------------------- */
	addFilter(
		'editor.BlockEdit','withkit/utility-panel',
		BlockEdit => props =>
			props.clientId
				? el(Fragment,null,el(BlockEdit,props),el(ClassPanel,{clientId:props.clientId}))
				: el(BlockEdit,props),
		20
	);
})();
JS;
}
