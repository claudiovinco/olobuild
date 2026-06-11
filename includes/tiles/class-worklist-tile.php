<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Work List — indice di progetti/voci a righe con hover-list (indent + shift + freccia).
 * Generica e token-first: il titolo eredita il font heading del tema; meta in monospace.
 */
class Olo_WorkList_Tile extends Olo_Tile_Base {

    protected $type     = 'worklist';
    protected $name     = 'Work List';
    protected $icon     = 'dashicons-menu-alt';
    protected $category = 'layout';
    protected $defaults = [
        'items' => [
            [ 'number' => '01', 'title' => 'Marisol',     'category' => 'Brand identity',     'year' => '2026', 'link_url' => '' ],
            [ 'number' => '02', 'title' => 'Atlas Press',  'category' => 'Editorial · web',    'year' => '2025', 'link_url' => '' ],
            [ 'number' => '03', 'title' => 'Field Museum', 'category' => 'Wayfinding',         'year' => '2025', 'link_url' => '' ],
            [ 'number' => '04', 'title' => 'Cobalt',       'category' => 'Identity · product', 'year' => '2024', 'link_url' => '' ],
        ],
        'divider_color'     => '#d7d1c2',
        'row_padding_y'     => 26,
        'row_hover_bg'      => '#e8e3d7',
        'hover_indent'      => 24,
        'number_color'      => '#8d8a82',
        'number_size'       => 13,
        'title_font_family' => 'heading',
        'title_color'       => '#18181a',
        'title_size'        => 40,
        'title_weight'      => '500',
        'show_category'     => true,
        'category_color'    => '#8d8a82',
        'category_size'     => 12,
        'show_year'         => true,
        'year_color'        => '#18181a',
        'year_size'         => 13,
        'show_arrow'        => true,
        'arrow_color'       => '#18181a',
        'mono_font_family'  => '',
    ];

    public function get_controls() { return []; }

    public function render( $settings, $style = [] ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'olo-worklist-' . wp_rand( 10000, 99999 );

        $heading = "var(--olo-font-family-heading, 'DM Sans',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif)";
        $body    = "var(--olo-font-family, 'Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif)";
        $mono_fb = "ui-monospace,'SF Mono',Menlo,Consolas,monospace";
        $mono_fam = $this->resolve_font_family( $s['mono_font_family'] ?? '' );
        // Nome font puro (legacy campo text) → wrap con lo stack mono di fallback storico.
        if ( $mono_fam !== '' && preg_match( '/^[A-Za-z0-9 \-]+$/', $mono_fam ) ) {
            $mono_fam = "'" . $mono_fam . "'," . $mono_fb;
        }
        $mono    = $mono_fam !== '' ? $mono_fam : $mono_fb;
        $tfam    = $this->resolve_font_family( $s['title_font_family'] ?? '', [ 'heading' => $heading, 'body' => $body, 'mono' => $mono ] ) ?: $heading;

        $pad_y       = max( 8, min( 60, absint( $s['row_padding_y'] ) ) );
        $indent      = max( 0, min( 48, absint( $s['hover_indent'] ) ) );
        $number_size = max( 10, min( 20, absint( $s['number_size'] ) ) );
        $title_size  = max( 20, min( 72, absint( $s['title_size'] ) ) );
        $title_wt    = preg_match( '/^\d+$/', (string) $s['title_weight'] ) ? $s['title_weight'] : '500';
        $cat_size    = max( 10, min( 18, absint( $s['category_size'] ) ) );
        $year_size   = max( 10, min( 20, absint( $s['year_size'] ) ) );

        $line     = $this->safe_color_css( $s['divider_color'] ) ?: '#d7d1c2';
        $hover_bg = $this->safe_color_css( $s['row_hover_bg'] ) ?: 'transparent';
        $num_c    = $this->safe_color_css( $s['number_color'] ) ?: '#8d8a82';
        $title_c  = $this->safe_color_css( $s['title_color'] ) ?: '#18181a';
        $cat_c    = $this->safe_color_css( $s['category_color'] ) ?: '#8d8a82';
        $year_c   = $this->safe_color_css( $s['year_color'] ) ?: '#18181a';
        $arrow_c  = $this->safe_color_css( $s['arrow_color'] ) ?: '#18181a';

        $show_cat   = ! empty( $s['show_category'] );
        $show_year  = ! empty( $s['show_year'] );
        $show_arrow = ! empty( $s['show_arrow'] );

        // grid columns: numero + titolo + [categoria] + [anno] + [freccia]
        $cols = '48px 1fr';
        if ( $show_cat )   $cols .= ' auto';
        if ( $show_year )  $cols .= ' auto';
        if ( $show_arrow ) $cols .= ' auto';

        $items    = is_array( $s['items'] ) ? $s['items'] : [];
        $row_base = 'display:grid;grid-template-columns:' . $cols . ';gap:24px;align-items:center;padding:' . $pad_y . 'px 8px;border-bottom:1px solid ' . $line . ';';

        ob_start();
        ?>
        <div class="olo-worklist <?php echo esc_attr( $uid ); ?>" style="border-top:1px solid <?php echo esc_attr( $line ); ?>;">
            <?php foreach ( $items as $idx => $it ) :
                $number   = $it['number'] ?? '';
                $title    = $it['title'] ?? '';
                $category = $it['category'] ?? '';
                $year     = $it['year'] ?? '';
                $link     = $it['link_url'] ?? '';
                $tag      = $link ? 'a' : 'div';
                $attrs    = $link ? ' href="' . esc_url( $link ) . '"' : '';
            ?>
                <<?php echo $tag . $attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $tag is a fixed 'a'/'div' literal; $attrs assembled above from a fixed literal + esc_url()'d link ?> class="olo-worklist__row" style="<?php echo esc_attr( $row_base ); ?>">
                    <span class="olo-worklist__n" style="font-family:<?php echo esc_attr( $mono ); ?>;font-size:<?php echo (int) $number_size; ?>px;color:<?php echo esc_attr( $num_c ); ?>;" data-olo-editable="<?php echo 'items.' . intval( $idx ) . '.number'; ?>"><?php echo esc_html( $number ); ?></span>
                    <span class="olo-worklist__t" style="font-family:<?php echo esc_attr( $tfam ); ?>;font-weight:<?php echo esc_attr( $title_wt ); ?>;font-size:<?php echo (int) $title_size; ?>px;line-height:1;letter-spacing:-0.03em;color:<?php echo esc_attr( $title_c ); ?>;" data-olo-editable="<?php echo 'items.' . intval( $idx ) . '.title'; ?>"><?php echo esc_html( $title ); ?></span>
                    <?php if ( $show_cat ) : ?>
                        <span class="olo-worklist__cat" style="font-family:<?php echo esc_attr( $mono ); ?>;font-size:<?php echo (int) $cat_size; ?>px;text-transform:uppercase;letter-spacing:0.02em;color:<?php echo esc_attr( $cat_c ); ?>;" data-olo-editable="<?php echo 'items.' . intval( $idx ) . '.category'; ?>"><?php echo esc_html( $category ); ?></span>
                    <?php endif; ?>
                    <?php if ( $show_year ) : ?>
                        <span class="olo-worklist__yr" style="font-family:<?php echo esc_attr( $mono ); ?>;font-size:<?php echo (int) $year_size; ?>px;color:<?php echo esc_attr( $year_c ); ?>;" data-olo-editable="<?php echo 'items.' . intval( $idx ) . '.year'; ?>"><?php echo esc_html( $year ); ?></span>
                    <?php endif; ?>
                    <?php if ( $show_arrow ) : ?>
                        <svg class="olo-worklist__arrow" viewBox="0 0 24 24" fill="none" stroke="<?php echo esc_attr( $arrow_c ); ?>" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 17 17 7M9 7h8v8"/></svg>
                    <?php endif; ?>
                </<?php echo $tag; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed 'a'/'div' literal from the ternary above ?>>
            <?php endforeach; ?>
        </div>
        <?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above: colours via the safe_color_css() whitelist, indent via absint() with min()/max() clamps, cursor a fixed-literal ternary; $uid is internally generated. ?>
        <style>
            .<?php echo $uid; ?> .olo-worklist__row { cursor: <?php echo $items && ! empty( $items[0]['link_url'] ) ? 'pointer' : 'default'; ?>; transition: padding .25s ease, background .25s ease; color: inherit; text-decoration: none; }
            .<?php echo $uid; ?> .olo-worklist__row:hover { padding-left: <?php echo $indent; ?>px; padding-right: <?php echo $indent; ?>px; background: <?php echo $hover_bg; ?>; }
            .<?php echo $uid; ?> .olo-worklist__t { transition: transform .25s ease; }
            .<?php echo $uid; ?> .olo-worklist__row:hover .olo-worklist__t { transform: translateX(6px); }
            .<?php echo $uid; ?> .olo-worklist__arrow { width: 20px; height: 20px; opacity: 0; transform: translateX(-6px); transition: opacity .25s ease, transform .25s ease; justify-self: end; }
            .<?php echo $uid; ?> .olo-worklist__row:hover .olo-worklist__arrow { opacity: 1; transform: none; }
            .<?php echo $uid; ?> a.olo-worklist__row:focus-visible { outline: 2px solid <?php echo $arrow_c; ?>; outline-offset: -2px; }
            @media (max-width: 640px) {
                .<?php echo $uid; ?> .olo-worklist__row { grid-template-columns: 32px 1fr auto; gap: 14px; }
                .<?php echo $uid; ?> .olo-worklist__cat { display: none; }
            }
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <?php
        return ob_get_clean();
    }
}
