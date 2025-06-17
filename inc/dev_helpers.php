<?php
/**
 * Plugin Name:  UPA25 Utility Classes Panel
 * Description:  Gutenberg-Inspector panel for helper classes.
 *               – “Default” tab for global helpers.
 *               – One tab per uncommented @include line (with-mobile, with-medium …).
 *               – “with-” becomes “…” in labels.
 *               – Horizontally-scrollable tab bar with extra bottom padding.
 *               – Blue dot after the main title and every tab that owns ≥ 1 active class.
 * Version:      2.2.0
 * Requires at least: WP 6.3, PHP 8.1
 */

declare(strict_types=1);

namespace UPA25;

/* -------------------------------------------------------------------------
 * Assets
 * ---------------------------------------------------------------------- */
add_action(
	'enqueue_block_editor_assets',
	static function (): void {
		wp_register_script(
			'upa25-class-panel',
			'',
			[ 'wp-block-editor', 'wp-components', 'wp-data', 'wp-element', 'wp-hooks' ],
			null,
			true
		);
		wp_enqueue_script( 'upa25-class-panel' );

		wp_add_inline_script(
			'upa25-class-panel',
			'window.UPA25_ITEMS = ' . wp_json_encode( collect_items(), JSON_THROW_ON_ERROR ),
			'before'
		);
		wp_add_inline_script( 'upa25-class-panel', inline_js(), 'after' );
	}
);

/* -------------------------------------------------------------------------
 * helpers.scss  →  ITEMS
 * ---------------------------------------------------------------------- */
function collect_items(): array {
    $src = get_theme_file_path( 'src/scss/utilities/helpers.scss' );
    if ( ! is_readable( $src ) ) {
        return [];
    }

    $lines        = file( $src );
    $items        = [];                 // default-tab content
    $mixinHeads   = [];                 // array keyed by heading label (order kept)
    $suffixes     = [];
    $breakpoints  = [];

    $inDoc       = false;
    $docT        = $docD = null;
    $insideMixin = false;
    $braceDepth  = 0;

    $flush_doc = static function (
        bool    $inMixin,
        array  &$items,
        array  &$mixinHeads,
        ?string &$t,
        ?string &$d
    ): void {
        if ( $t === null && $d === null ) {
            return;
        }
        $label = trim( $t ?? '' );
        $h     = [ 'type' => 'heading', 'label' => $label, 'desc' => trim( $d ?? '' ) ];

        if ( $inMixin ) {
            // keep first occurrence only, preserve order
            if ( ! isset( $mixinHeads[ $label ] ) ) {
                $mixinHeads[ $label ] = $h;
            }
        } else {
            $items[] = $h;
        }
        $t = $d = null;
    };

    foreach ( $lines as $line ) {
        $trim = ltrim( $line );

        if ( str_starts_with( $trim, '//' ) ) {
            continue;                               // skip full-line comments
        }

        // are we inside the mixin?
        if ( preg_match( '/@mixin\s+responsive-styles\(/', $trim ) ) {
            $insideMixin = true;
            $braceDepth  = substr_count( $trim, '{' ) - substr_count( $trim, '}' );
        } elseif ( $insideMixin ) {
            $braceDepth += substr_count( $trim, '{' ) - substr_count( $trim, '}' );
            if ( $braceDepth <= 0 ) {
                $insideMixin = false;
            }
        }

        // doc-block capture
        if ( ! $inDoc && str_starts_with( $trim, '/**' ) ) {
            $inDoc = true;
            $docT = $docD = null;
            continue;
        }
        if ( $inDoc ) {
            if ( preg_match( '/\*\s*Title:\s*(.+)/', $trim, $m ) ) {
                $docT = $m[1];
            } elseif ( preg_match( '/\*\s*Description:\s*(.+)/', $trim, $m ) ) {
                $docD = $m[1];
            } elseif ( str_contains( $trim, '*/' ) ) {
                $inDoc = false;
            }
            continue;
        }

        // uncommented @include → breakpoint
        if ( preg_match( '/@include\s+responsive-styles\([^,]+,\s*"?(with-[\w-]+)"?/', $trim, $m ) ) {
            $prefix               = $m[1];
            $breakpoints[$prefix] = str_replace( 'with-', '', $prefix );
            continue;
        }

        // flush heading before first selector
        if ( str_contains( $trim, '.' ) ) {
            $flush_doc( $insideMixin, $items, $mixinHeads, $docT, $docD );
        }

        // suffixes in mixin
        if ( $insideMixin && preg_match( '/\.#\{\$prefix}-([\w-]+)/', $trim, $m ) ) {
            $suffixes[] = $m[1];
            continue;
        }

        // default helpers outside mixin
        if ( ! $insideMixin && preg_match_all( '/\.([\w-]+)/', $trim, $m ) ) {
            foreach ( $m[1] as $cls ) {
                if ( ! ctype_digit( $cls ) ) {
                    $items[] = [ 'type' => 'class', 'name' => $cls ];
                }
            }
        }
    }
    $flush_doc( $insideMixin, $items, $mixinHeads, $docT, $docD );

    $suffixes   = array_unique( $suffixes );
    $mixinHeads = array_values( $mixinHeads );      // ordered, de-duped headings

    /* breakpoint tabs ---------------------------------------------------- */
    foreach ( $breakpoints as $prefix => $label ) {
        $items[] = [ 'type' => 'heading', 'label' => $label, 'prefix' => $prefix, 'bp' => true ];
        foreach ( $mixinHeads as $h ) {
            $items[] = $h;                           // Display / Order / Alignment in order
        }
        foreach ( $suffixes as $s ) {
            $items[] = [ 'type' => 'class', 'name' => "{$prefix}-{$s}" ];
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

	const ITEMS = Array.isArray( window.UPA25_ITEMS ) ? window.UPA25_ITEMS : [];

	/* one-time CSS ------------------------------------------------------- */
	if ( ! document.getElementById( 'upa25-style' ) ) {
		const s = document.createElement( 'style' );
		s.id = 'upa25-style';
		s.textContent = `
			.upa25-tabs .components-tab-panel__tabs{
				display:flex;flex-wrap:nowrap;gap:4px;
				overflow-x:auto;scrollbar-width:thin;margin-bottom:14px;
			}
			.upa25-tabs .components-tab-panel__tabs::-webkit-scrollbar{height:6px}
			.upa25-tabs .components-tab-panel__tabs button{
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
			className: 'upa25-section-label',
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
				el(TabPanel,{className:'upa25-tabs',tabs},renderTab)
			)
		);
	};

	/* HOC --------------------------------------------------------------- */
	addFilter(
		'editor.BlockEdit','upa25/utility-panel',
		BlockEdit => props =>
			props.clientId
				? el(Fragment,null,el(BlockEdit,props),el(ClassPanel,{clientId:props.clientId}))
				: el(BlockEdit,props),
		20
	);
})();
JS;
}
