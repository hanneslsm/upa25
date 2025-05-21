<?php
/**
 * --------------------------------------------------------------------
 *  Block-editor “Utility classes” panel  ·  slug: upa25
 * --------------------------------------------------------------------
 *  • scans  src/scss/utilities/helpers.scss
 *  • understands three kinds of doc-blocks
 *      1.  Ahead of a whole utility section
 *          /**
 *           * Title: Spacing
 *           * Description: Margin & padding helpers
 *           *\/
 *
 *      2.  Immediately before  @include responsive-styles(...,"on-mobile")
 *          …becomes the label/description for that breakpoint heading.
 *
 *      3.  Inside the responsive mixin
 *          /**
 *           * Description: Alignment
 *           *\/
 *          …creates a sub-heading (“Alignment”) under the current breakpoint.
 *
 *  • responsive variants are expanded dynamically:
 *      .#{$prefix}-grid-order-#{$i}  +   @for $i from 1 through N
 *
 *  • panel lives in Inspector › Settings
 *  • search filter, WP-native label & help styling
 * --------------------------------------------------------------------
 *  Requires WordPress 6.3+ (Gutenberg UI) and PHP 8.1+ (str_* helpers)
 * ------------------------------------------------------------------ */

add_action( 'enqueue_block_editor_assets', 'upa25_enqueue_editor_assets' );

/* ====================================================================
 * 1.  Build an ordered list of headings / help texts / class names
 * ================================================================== */
function upa25_collect_items(): array
{
    static $cache = null;
    if ( $cache !== null ) {
        return $cache;
    }

    $file = get_theme_file_path( 'src/scss/utilities/helpers.scss' );
    if ( ! is_readable( $file ) ) {
        return $cache = [];
    }

    $lines        = file( $file );
    $items        = [];
    $suffixes     = [];            // mixin suffixes (flex-column, grid-order-, …)
    $breakpoints  = [];            // prefix  ⇒  [label, desc]
    $rangeMin     = 1;
    $rangeMax     = 6;

    /* ---------- helpers ------------------------------------------- */
    $flushPending = static function ( array &$items, ?string &$title, ?string &$desc ): void {
        if ( $title !== null || $desc !== null ) {
            $items[] = [
                'type'  => 'heading',
                'label' => $title ?? '',
                'desc'  => $desc  ?? '',
            ];
            $title = $desc = null;
        }
    };

    /* ---------- scan the file ------------------------------------- */
    $inDoc    = false;
    $docTitle = $docDesc = null;

    foreach ( $lines as $line ) {

        /* ---------- (1) start / end of a /** … *\/ doc-block ------ */
        if ( ! $inDoc && str_starts_with( $line, '/**' ) ) {
            $inDoc    = true;
            $docTitle = $docDesc = null;
            continue;
        }

        if ( $inDoc ) {
            if ( preg_match( '/\*\s*Title:\s*(.+)/', $line, $m ) ) {
                $docTitle = trim( $m[1] );
            } elseif ( preg_match( '/\*\s*Description:\s*(.+)/', $line, $m ) ) {
                $docDesc  = trim( $m[1] );
            } elseif ( str_contains( $line, '*/' ) ) {           // end block
                $inDoc = false;
                /* keep title/desc in temp variables – decide later
                   whether they are section-, breakpoint- or sub-headings */
            }
            continue;
        }

        /* ---------- (2) detect @for loop range -------------------- */
        if ( preg_match(
                '/@for\s+\$i\s+from\s+(\d+)\s+through\s+(\d+)/',
                $line,
                $m
            ) ) {
            $rangeMin = (int) $m[1];
            $rangeMax = (int) $m[2];
            continue;
        }

        /* ---------- (3) responsive-styles include ----------------- */
        if ( preg_match(
                '/@include\s+responsive-styles\([^,]+,\s*"([^"]+)"\s*\)/',
                $line,
                $m
            ) ) {
            $prefix = trim( $m[1] );
            /* doc-block immediately above? → becomes label/desc for this prefix */
            if ( $docTitle !== null || $docDesc !== null ) {
                $breakpoints[ $prefix ] = [
                    'label' => $docTitle ?? strtoupper( str_replace( '-', ' ', $prefix ) ),
                    'desc'  => $docDesc ?? '',
                ];
                $docTitle = $docDesc = null;
            } else {
                $breakpoints[ $prefix ] = [
                    'label' => strtoupper( str_replace( '-', ' ', $prefix ) ),
                    'desc'  => '',
                ];
            }
            continue;
        }

        /* ---------- (4) flush pending doc-block BEFORE first rule -- */
        if ( preg_match( '/\./', $line ) ) {     // first selector after a doc-block
            $flushPending( $items, $docTitle, $docDesc );
        }

        /* ---------- (5) mixin suffix patterns --------------------- */
        if ( preg_match(
                '/\.#\{\$prefix\}-([\w-]+)-#\{\$i\}/',
                $line,
                $m
            ) ) {
            $suffixes[] = $m[1] . '-';           // trailing dash marks “numeric list”
            continue;
        }

        if ( preg_match(
                '/\.#\{\$prefix\}-([\w-]+)/',
                $line,
                $m
            ) ) {
            $suffixes[] = $m[1];
            continue;
        }

        /* ---------- (6) plain selectors outside the mixin --------- */
        if ( preg_match_all( '/\.([\w-]+)/', $line, $m ) ) {
            foreach ( $m[1] as $cls ) {
                if ( ctype_digit( $cls ) ) {          // drop “0”, “2”, …
                    continue;
                }
                $items[] = [ 'type' => 'class', 'name' => $cls ];
            }
        }
    }

    /* anything still pending at EOF? */
    $flushPending( $items, $docTitle, $docDesc );

    $suffixes = array_unique( $suffixes );

    /* ---------- expand breakpoint variants ----------------------- */
    foreach ( $breakpoints as $prefix => $meta ) {

        $items[] = [
            'type'  => 'heading',
            'label' => $meta['label'],
            'desc'  => $meta['desc'],
        ];

        /* sub-heading doc-blocks that appeared INSIDE the mixin
           were flushed as normal headings in order – so nothing else
           to do here, just add the class toggles                       */

        foreach ( $suffixes as $suf ) {
            if ( str_ends_with( $suf, '-' ) ) {       // needs numeric expansion
                for ( $i = $rangeMin; $i <= $rangeMax; $i++ ) {
                    $items[] = [
                        'type' => 'class',
                        'name' => "$prefix-$suf$i",
                    ];
                }
            } else {
                $items[] = [
                    'type' => 'class',
                    'name' => "$prefix-$suf",
                ];
            }
        }
    }

    return $cache = $items;
}

/* ====================================================================
 * 2.  Enqueue script + pass PHP data
 * ================================================================== */
function upa25_enqueue_editor_assets(): void
{
    $handle = 'upa25-class-panel';

    wp_register_script(
        $handle,
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
    wp_enqueue_script( $handle );

    wp_add_inline_script(
        $handle,
        'window.UPA25_ITEMS = ' . wp_json_encode( upa25_collect_items(), JSON_THROW_ON_ERROR ) . ';',
        'before'
    );
    wp_add_inline_script( $handle, upa25_inline_js(), 'after' );
}

/* ====================================================================
 * 3.  Inline JavaScript – Settings-tab panel
 * ================================================================== */
function upa25_inline_js(): string
{
    return <<<'JS'
(() => {
    const { createElement: el, Fragment, useState } = wp.element;
    const {
        PanelBody,
        ToggleControl,
        SearchControl,
        BaseControl,
    } = wp.components;
    const { InspectorControls }  = wp.blockEditor;
    const { addFilter }          = wp.hooks;
    const { useSelect, useDispatch } = wp.data;

    const ITEMS = Array.isArray( window.UPA25_ITEMS ) ? window.UPA25_ITEMS : [];

    /* ---------- heading / help component ------------------------- */
    const SectionLabel = ( { title, desc } ) =>
        el( BaseControl, {
            label: title,
            help : desc || null,
            className: 'upa25-section-label',
            __nextHasNoMarginBottom: true,
        } );

    /* ---------- main panel --------------------------------------- */
    const ClassPanel = ( { clientId } ) => {
        const { className = '' } = useSelect( sel =>
            sel( 'core/block-editor' ).getBlockAttributes( clientId )
        );
        const { updateBlockAttributes } = useDispatch( 'core/block-editor' );

        const [ query, setQuery ] = useState( '' );
        const active = new Set( className.split( /\s+/ ).filter( Boolean ) );

        const toggle = cls => {
            active.has( cls ) ? active.delete( cls ) : active.add( cls );
            updateBlockAttributes( clientId, { className: [ ...active ].join( ' ' ) } );
        };

        const q      = query.trim().toLowerCase();
        const shown  = q
            ? ITEMS.filter( it => it.type === 'class' && it.name.toLowerCase().includes( q ) )
            : ITEMS;

        return el(
            InspectorControls,
            { group: 'settings' },
            el(
                PanelBody,
                { title: 'Utility classes', initialOpen: false },
                el( SearchControl, {
                    value: query,
                    placeholder: 'Search classes…',
                    onChange: setQuery,
                    __nextHasNoMarginBottom: true,
                } ),
                shown.length
                    ? shown.map( it =>
                        it.type === 'heading'
                            ? SectionLabel( { title: it.label, desc: it.desc } )
                            : el( ToggleControl, {
                                key     : it.name,
                                label   : it.name,
                                checked : active.has( it.name ),
                                onChange: () => toggle( it.name ),
                            } )
                      )
                    : el( 'p', { style: { opacity: 0.6 } }, 'No matches' )
            )
        );
    };

    /* ---------- HOC wraps every BlockEdit ------------------------ */
    const withPanel = BlockEdit => props =>
        props.clientId
            ? el( Fragment, null,
                el( BlockEdit, props ),
                el( ClassPanel, { clientId: props.clientId } )
              )
            : el( BlockEdit, props );

    addFilter( 'editor.BlockEdit', 'upa25/add-class-panel', withPanel, 20 );
})();
JS;
}
