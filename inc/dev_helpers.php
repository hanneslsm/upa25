<?php

/**
 * Plugin Name: UPA25 Utility Classes Panel
 * Description: Adds a “Utility classes” panel to the block editor Inspector › Settings, with an active indicator.
 * Version:     1.0.2
 * Requires at least: WP 6.3, PHP 8.1
 */

declare(strict_types=1);

add_action('enqueue_block_editor_assets', 'upa25_enqueue_editor_assets');

function upa25_enqueue_editor_assets(): void
{
	wp_register_script(
		'upa25-class-panel',
		'',
		[
			'wp-block-editor',
			'wp-components',
			'wp-data',
			'wp-element',
			'wp-hooks',
		],
		null,
		true
	);
	wp_enqueue_script('upa25-class-panel');

	wp_add_inline_script(
		'upa25-class-panel',
		'window.UPA25_ITEMS = ' . wp_json_encode(upa25_collect_items(), JSON_THROW_ON_ERROR) . ';',
		'before'
	);

	wp_add_inline_script(
		'upa25-class-panel',
		upa25_inline_js(),
		'after'
	);
}

function upa25_collect_items(): array
{
	static $cache = null;
	if ($cache !== null) {
		return $cache;
	}

	$file = get_theme_file_path('src/scss/utilities/helpers.scss');
	if (! is_readable($file)) {
		return $cache = [];
	}

	$lines       = file($file);
	$items       = [];
	$suffixes    = [];
	$breakpoints = [];
	$rangeMin    = 1;
	$rangeMax    = 6;

	$flushPending = static function (array &$items, ?string &$title, ?string &$desc): void {
		if ($title !== null || $desc !== null) {
			$items[] = [
				'type'  => 'heading',
				'label' => $title ?? '',
				'desc'  => $desc  ?? '',
			];
			$title = $desc = null;
		}
	};

	$inDoc    = false;
	$docTitle = $docDesc = null;

	foreach ($lines as $line) {
		if (! $inDoc && str_starts_with($line, '/**')) {
			$inDoc    = true;
			$docTitle = $docDesc = null;
			continue;
		}
		if ($inDoc) {
			if (preg_match('/\*\s*Title:\s*(.+)/', $line, $m)) {
				$docTitle = trim($m[1]);
			} elseif (preg_match('/\*\s*Description:\s*(.+)/', $line, $m)) {
				$docDesc = trim($m[1]);
			} elseif (str_contains($line, '*/')) {
				$inDoc = false;
			}
			continue;
		}
		if (preg_match('/@for\s+\$i\s+from\s+(\d+)\s+through\s+(\d+)/', $line, $m)) {
			$rangeMin = (int) $m[1];
			$rangeMax = (int) $m[2];
			continue;
		}
		if (preg_match('/@include\s+responsive-styles\([^,]+,\s*"([^"]+)"\s*\)/', $line, $m)) {
			$prefix = trim($m[1]);
			if ($docTitle !== null || $docDesc !== null) {
				$breakpoints[$prefix] = [
					'label' => $docTitle ?? strtoupper(str_replace('-', ' ', $prefix)),
					'desc'  => $docDesc  ?? '',
				];
				$docTitle = $docDesc = null;
			} else {
				$breakpoints[$prefix] = [
					'label' => strtoupper(str_replace('-', ' ', $prefix)),
					'desc'  => '',
				];
			}
			continue;
		}
		if (preg_match('/\./', $line)) {
			$flushPending($items, $docTitle, $docDesc);
		}
		if (preg_match('/\.#\{\$prefix\}-([\w-]+)-#\{\$i\}/', $line, $m)) {
			$suffixes[] = $m[1] . '-';
			continue;
		}
		if (preg_match('/\.#\{\$prefix\}-([\w-]+)/', $line, $m)) {
			$suffixes[] = $m[1];
			continue;
		}
		if (preg_match_all('/\.([\w-]+)/', $line, $m)) {
			foreach ($m[1] as $cls) {
				if (ctype_digit($cls)) {
					continue;
				}
				$items[] = ['type' => 'class', 'name' => $cls];
			}
		}
	}

	$flushPending($items, $docTitle, $docDesc);
	$suffixes = array_unique($suffixes);

	foreach ($breakpoints as $prefix => $meta) {
		$items[] = [
			'type'  => 'heading',
			'label' => $meta['label'],
			'desc'  => $meta['desc'],
		];
		foreach ($suffixes as $suf) {
			if (str_ends_with($suf, '-')) {
				for ($i = $rangeMin; $i <= $rangeMax; $i++) {
					$items[] = ['type' => 'class', 'name' => "$prefix-$suf$i"];
				}
			} else {
				$items[] = ['type' => 'class', 'name' => "$prefix-$suf"];
			}
		}
	}

	return $cache = $items;
}

function upa25_inline_js(): string
{
	return <<<'JS'
(() => {
	const { createElement: el, Fragment, useState } = wp.element;
	const { PanelBody, ToggleControl, SearchControl, BaseControl } = wp.components;
	const { InspectorControls } = wp.blockEditor;
	const { addFilter } = wp.hooks;
	const { useSelect, useDispatch } = wp.data;
	const ITEMS = Array.isArray(window.UPA25_ITEMS) ? window.UPA25_ITEMS : [];

	const SectionLabel = ({ title, desc }) =>
		el(BaseControl, {
			label: title,
			help: desc || null,
			className: 'upa25-section-label',
			__nextHasNoMarginBottom: true,
		});

	const ClassPanel = ({ clientId }) => {
		const { className = '' } = useSelect((sel) =>
			sel('core/block-editor').getBlockAttributes(clientId)
		);
		const { updateBlockAttributes } = useDispatch('core/block-editor');
		const [query, setQuery] = useState('');
		const active = new Set(className.split(/\s+/).filter(Boolean));
		const toggle = (cls) => {
			active.has(cls) ? active.delete(cls) : active.add(cls);
			updateBlockAttributes(clientId, { className: [...active].join(' ') });
		};
		const q = query.trim().toLowerCase();
		const shown = q
			? ITEMS.filter((it) => it.type === 'class' && it.name.toLowerCase().includes(q))
			: ITEMS;

		const title = el('span', {
			style: { display: 'flex', alignItems: 'center', gap: '0.4em' }
		}, [
			'Utility classes',
			active.size > 0 ? el('span', {
				style: {
					display: 'inline-block',
					width: '0.5em',
					height: '0.5em',
					borderRadius: '999px',
					backgroundColor: 'var(--wp-admin-theme-color, #007cba)',
				}
			}) : null
		]);

		return el(
			InspectorControls,
			{ group: 'settings' },
			el(
				PanelBody,
				{ title, initialOpen: false },
				el(SearchControl, {
					value: query,
					placeholder: 'Search classes…',
					onChange: setQuery,
					__nextHasNoMarginBottom: true,
				}),
				shown.length
					? shown.map((it) =>
							it.type === 'heading'
								? SectionLabel({ title: it.label, desc: it.desc })
								: el(ToggleControl, {
										key: it.name,
										label: it.name,
										checked: active.has(it.name),
										onChange: () => toggle(it.name),
								  })
					  )
					: el('p', { style: { opacity: 0.6 } }, 'No matches')
			)
		);
	};

	const withPanel = (BlockEdit) => (props) =>
		props.clientId
			? el(Fragment, null, el(BlockEdit, props), el(ClassPanel, { clientId: props.clientId }))
			: el(BlockEdit, props);

	addFilter('editor.BlockEdit', 'upa25/add-class-panel', withPanel, 20);
})();
JS;
}
