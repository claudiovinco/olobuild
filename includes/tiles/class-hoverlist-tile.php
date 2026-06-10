<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Hover List — righe con pastiglia colore + nome + sotto-etichetta, indentazione al hover
 * e anteprima flottante (peek) che segue il cursore. Per shade finder / liste curate.
 */
class Olo_HoverList_Tile extends Olo_Tile_Base {

    protected $type     = 'hoverlist';
    protected $name     = 'Hover List';
    protected $icon     = 'dashicons-art';
    protected $category = 'layout';
    protected $defaults = [
        'items' => [
            [ 'color' => '#9a3b52', 'name' => 'Rosewood',   'sub' => 'Cool · matte', 'link_url' => '' ],
            [ 'color' => '#c77a6a', 'name' => 'Terracotta',  'sub' => 'Warm · matte', 'link_url' => '' ],
            [ 'color' => '#e79aa6', 'name' => 'Peony',       'sub' => 'Cool · blush', 'link_url' => '' ],
            [ 'color' => '#e6a17e', 'name' => 'Apricot',     'sub' => 'Warm · blush', 'link_url' => '' ],
            [ 'color' => '#7d2e3e', 'name' => 'Merlot',      'sub' => 'Deep · matte', 'link_url' => '' ],
        ],
        'swatch_size'      => 26,
        'swatch_shape'     => 'circle',
        'name_font_family' => 'heading',
        'name_color'       => '#f6e9ec',
        'name_size'        => 22,
        'sub_color'        => '#9c7e8c',
        'sub_size'         => 12,
        'sub_uppercase'    => true,
        'row_padding_y'    => 20,
        'hover_indent'     => 20,
        'hover_bg'         => '#4d2f40',
        'line_color'       => 'rgba(246,233,236,.13)',
        'peek'             => true,
        'peek_width'       => 170,
        'peek_ratio'       => '4/5',
        'mono_font_family' => '',
    ];

    public function get_controls() { return []; }

    public function render( $settings, $style = [] ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'olo-hl-' . wp_rand( 10000, 99999 );

        $heading = "var(--olo-font-family-heading, 'DM Sans',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif)";
        $body    = "var(--olo-font-family, 'Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif)";
        $mono_fb = "ui-monospace,'SF Mono',Menlo,Consolas,monospace";
        $mono_fam = $this->resolve_font_family( $s['mono_font_family'] ?? '' );
        // Nome font puro (legacy campo text) → wrap con lo stack mono di fallback storico.
        if ( $mono_fam !== '' && preg_match( '/^[A-Za-z0-9 \-]+$/', $mono_fam ) ) {
            $mono_fam = "'" . $mono_fam . "'," . $mono_fb;
        }
        $mono    = $mono_fam !== '' ? $mono_fam : $mono_fb;
        $nfam    = $this->resolve_font_family( $s['name_font_family'] ?? '', [ 'heading' => $heading, 'body' => $body, 'mono' => $mono ] ) ?: $heading;

        $sw_size = max( 14, min( 44, absint( $s['swatch_size'] ) ) );
        $sw_rad  = ( ( $s['swatch_shape'] ?? 'circle' ) === 'square' ) ? '7px' : '50%';
        $name_sz = max( 14, min( 36, absint( $s['name_size'] ) ) );
        $sub_sz  = max( 10, min( 18, absint( $s['sub_size'] ) ) );
        $pad_y   = max( 8, min( 40, absint( $s['row_padding_y'] ) ) );
        $indent  = max( 0, min( 40, absint( $s['hover_indent'] ) ) );

        $name_c  = $this->safe_color_css( $s['name_color'] ) ?: '#f6e9ec';
        $sub_c   = $this->safe_color_css( $s['sub_color'] ) ?: '#9c7e8c';
        $hbg     = $this->safe_color_css( $s['hover_bg'] ) ?: 'rgba(255,255,255,.05)';
        $line    = $this->safe_color_css( $s['line_color'] ) ?: 'rgba(255,255,255,.13)';
        $upper   = ! empty( $s['sub_uppercase'] );
        $peek    = ! empty( $s['peek'] );
        $peek_w  = max( 100, min( 320, absint( $s['peek_width'] ?? 170 ) ) );
        $peek_r  = preg_replace( '/[^0-9.\/]/', '', $s['peek_ratio'] ?? '4/5' ) ?: '4/5';
        $peek_ph = 'var(--olo-color-muted, #2b2b2b)';

        $items   = is_array( $s['items'] ) ? $s['items'] : [];
        $row_base = 'display:flex;align-items:center;gap:18px;padding:' . $pad_y . 'px 8px;border-bottom:1px solid ' . $line . ';color:inherit;text-decoration:none;transition:padding .25s ease,background .2s ease;';

        ob_start();
        ?>
        <div class="olo-hoverlist <?php echo esc_attr( $uid ); ?>" style="position:relative;border-top:1px solid <?php echo esc_attr( $line ); ?>;">
            <?php foreach ( $items as $idx => $it ) :
                $color = $this->safe_color_css( $it['color'] ?? '' ) ?: '#999';
                $name  = $it['name'] ?? '';
                $sub   = $it['sub'] ?? '';
                $link  = $it['link_url'] ?? '';
                $pimg  = trim( (string) ( $it['image'] ?? '' ) );
                $tag   = $link ? 'a' : 'div';
                $attrs = $link ? ' href="' . esc_url( $link ) . '"' : '';
            ?>
                <<?php echo $tag . $attrs; ?> class="olo-hoverlist__row" data-color="<?php echo esc_attr( $color ); ?>" data-name="<?php echo esc_attr( $name ); ?>" data-image="<?php echo esc_url( $pimg ); ?>" style="<?php echo esc_attr( $row_base ); ?>">
                    <span class="olo-hoverlist__sw" style="width:<?php echo $sw_size; ?>px;height:<?php echo $sw_size; ?>px;border-radius:<?php echo $sw_rad; ?>;flex:none;background:<?php echo esc_attr( $color ); ?>;box-shadow:inset 0 0 0 1.5px rgba(246,233,236,.3);"></span>
                    <span class="olo-hoverlist__nm" style="font-family:<?php echo esc_attr( $nfam ); ?>;font-size:<?php echo $name_sz; ?>px;color:<?php echo esc_attr( $name_c ); ?>;line-height:1.1;" data-olo-editable="<?php echo 'items.' . intval( $idx ) . '.name'; ?>"><?php echo esc_html( $name ); ?></span>
                    <?php if ( $sub !== '' ) : ?>
                        <span class="olo-hoverlist__sub" style="margin-left:auto;font-family:<?php echo esc_attr( $mono ); ?>;font-size:<?php echo $sub_sz; ?>px;letter-spacing:0.06em;color:<?php echo esc_attr( $sub_c ); ?>;<?php echo $upper ? 'text-transform:uppercase;' : ''; ?>" data-olo-editable="<?php echo 'items.' . intval( $idx ) . '.sub'; ?>"><?php echo esc_html( $sub ); ?></span>
                    <?php endif; ?>
                </<?php echo $tag; ?>>
            <?php endforeach; ?>
            <?php if ( $peek ) : ?>
            <div class="olo-hoverlist__peek" aria-hidden="true" style="position:fixed;z-index:90;pointer-events:none;opacity:0;transform:translate(16px,-50%);transition:opacity .18s ease;">
                <span class="olo-hl-peek-img" style="display:block;width:<?php echo $peek_w; ?>px;aspect-ratio:<?php echo $peek_r; ?>;border-radius:14px;overflow:hidden;background:<?php echo $peek_ph; ?>;background-size:cover;background-position:center;background-image:repeating-linear-gradient(135deg,rgba(255,255,255,.06) 0 16px,transparent 16px 32px);box-shadow:0 18px 50px rgba(0,0,0,.45);"></span>
            </div>
            <?php endif; ?>
        </div>
        <style>
            .<?php echo $uid; ?> .olo-hoverlist__row:hover { padding-left: <?php echo $indent; ?>px; background: <?php echo $hbg; ?>; }
            .<?php echo $uid; ?> a.olo-hoverlist__row:focus-visible { outline: 2px solid <?php echo $name_c; ?>; outline-offset: -2px; }
        </style>
        <?php if ( $peek ) : ?>
        <script>(function(){
            var root = document.querySelector('.<?php echo $uid; ?>');
            if (!root) { return; }
            var peek = root.querySelector('.olo-hoverlist__peek');
            if (!peek) { return; }
            // Sposta il peek nel <body>: position:fixed si rompe se un antenato ha transform
            // (entrance/reveal animations del frontend) — nel builder non c'è quel transform, da cui
            // "funziona solo nel builder". Nel body il fixed segue sempre il viewport.
            document.body.appendChild(peek);
            var img = peek.querySelector('.olo-hl-peek-img');
            var ph = 'repeating-linear-gradient(135deg,rgba(255,255,255,.06) 0 16px,transparent 16px 32px)';
            var rows = root.querySelectorAll('.olo-hoverlist__row');
            rows.forEach(function (r) {
                r.addEventListener('mouseenter', function () {
                    var src = r.getAttribute('data-image') || '';
                    img.style.backgroundImage = src ? ('url(' + src + ')') : ph;
                    peek.style.opacity = '1';
                });
                r.addEventListener('mousemove', function (e) {
                    peek.style.left = e.clientX + 'px';
                    peek.style.top = e.clientY + 'px';
                });
                r.addEventListener('mouseleave', function () { peek.style.opacity = '0'; });
            });
        })();</script>
        <?php endif; ?>
        <?php
        return ob_get_clean();
    }
}
