<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Hover List — righe con pastiglia colore + nome + sotto-etichetta, indentazione al hover
 * e anteprima flottante (peek) che segue il cursore. Per shade finder / liste curate.
 *
 * Estensioni "sala di regia" (blueprint Clod Evoluzione):
 *   lead_mode 'number'  → numero progressivo mono (01, 02…) al posto della pastiglia,
 *                         riga a griglia 64px 1fr auto con descrizione a destra.
 *   peek_mode 'monitor' → il box che segue il cursore diventa un monitor di regia
 *                         (viewfinder + ● STILL + barra label) invece dell'immagine.
 * Default invariati = resa storica identica.
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
        'lead_mode'          => 'swatch',
        'swatch_size'        => 26,
        'swatch_shape'       => 'circle',
        'number_color'       => '',
        'number_hover_color' => '',
        'name_font_family'   => 'heading',
        'name_color'         => '#f6e9ec',
        'name_size'          => 22,
        'name_uppercase'     => false,
        'sub_color'          => '#9c7e8c',
        'sub_size'           => 12,
        'sub_uppercase'      => true,
        'desc_color'         => '',
        'desc_size'          => 14,
        'row_padding_y'      => 20,
        'hover_indent'       => 20,
        'hover_bg'           => '#4d2f40',
        'line_color'         => 'rgba(246,233,236,.13)',
        'peek'               => true,
        'peek_mode'          => 'image',
        'peek_width'         => 170,
        'peek_ratio'         => '4/5',
        'mono_font_family'   => '',
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

        $lead    = ( ( $s['lead_mode'] ?? 'swatch' ) === 'number' ) ? 'number' : 'swatch';
        $sw_size = max( 14, min( 44, absint( $s['swatch_size'] ) ) );
        $sw_rad  = ( ( $s['swatch_shape'] ?? 'circle' ) === 'square' ) ? '7px' : '50%';
        $name_sz = max( 14, min( 56, absint( $s['name_size'] ) ) );
        $name_up = ! empty( $s['name_uppercase'] );
        $sub_sz  = max( 10, min( 18, absint( $s['sub_size'] ) ) );
        $pad_y   = max( 8, min( 40, absint( $s['row_padding_y'] ) ) );
        $indent  = max( 0, min( 40, absint( $s['hover_indent'] ) ) );

        $name_c  = $this->safe_color_css( $s['name_color'] ) ?: '#f6e9ec';
        $sub_c   = $this->safe_color_css( $s['sub_color'] ) ?: '#9c7e8c';
        $hbg     = $this->safe_color_css( $s['hover_bg'] ) ?: 'rgba(255,255,255,.05)';
        $line    = $this->safe_color_css( $s['line_color'] ) ?: 'rgba(255,255,255,.13)';
        $upper   = ! empty( $s['sub_uppercase'] );

        // Numero progressivo (lead_mode 'number') — token-first.
        $num_c   = $this->safe_color_css( $s['number_color'] ?? '' ) ?: 'var(--olo-color-text-faint, #6a6c64)';
        $num_hc  = $this->safe_color_css( $s['number_hover_color'] ?? '' ) ?: 'var(--olo-color-primary, #C6F24E)';
        // Descrizione (colonna destra, layout numerato). Il type 'number' preserva '' → default 14.
        $desc_c   = $this->safe_color_css( $s['desc_color'] ?? '' ) ?: 'var(--olo-color-text-soft, #a0a298)';
        $desc_raw = $s['desc_size'] ?? 14;
        $desc_sz  = ( $desc_raw === '' || $desc_raw === null ) ? 14 : max( 10, min( 24, absint( $desc_raw ) ) );

        $peek      = ! empty( $s['peek'] );
        $peek_mode = ( ( $s['peek_mode'] ?? 'image' ) === 'monitor' ) ? 'monitor' : 'image';
        $peek_w    = max( 100, min( 320, absint( $s['peek_width'] ?? 170 ) ) );
        $peek_r    = preg_replace( '/[^0-9.\/]/', '', $s['peek_ratio'] ?? '4/5' ) ?: '4/5';
        $peek_ph   = 'var(--olo-color-muted, #2b2b2b)';

        // Monitor "sala di regia" — palette token-first (ink-3/ink-2/bone/signal del blueprint come fallback).
        $acc        = 'var(--olo-color-primary, #C6F24E)';
        $mon_border = 'var(--olo-color-border, rgba(236,234,227,.2))';
        $mon_scr_bg = 'var(--olo-color-muted, #161922)';
        $mon_lab_bg = 'color-mix(in srgb, var(--olo-color-background, #0b0c0f) 55%, var(--olo-color-muted, #161922))';
        $mon_stripe = 'color-mix(in srgb, var(--olo-color-text, #ECEAE3) 5%, transparent)';
        $mon_text   = 'var(--olo-color-text, #ECEAE3)';

        $items = is_array( $s['items'] ) ? $s['items'] : [];
        if ( $lead === 'number' ) {
            // Riga "sala di regia": griglia 64px 1fr auto (blueprint .srv__row).
            $row_base = 'display:grid;grid-template-columns:64px 1fr auto;gap:24px;align-items:center;padding:clamp(20px,2.6vw,32px) 4px;border-bottom:1px solid ' . $line . ';color:inherit;text-decoration:none;position:relative;transition:background .2s ease,padding .2s ease;';
        } else {
            $row_base = 'display:flex;align-items:center;gap:18px;padding:' . $pad_y . 'px 8px;border-bottom:1px solid ' . $line . ';color:inherit;text-decoration:none;transition:padding .25s ease,background .2s ease;';
        }

        ob_start();
        ?>
        <?php $rowbg_rules = []; ?>
        <div class="olo-hoverlist <?php echo esc_attr( $uid ); ?>" style="position:relative;border-top:1px solid <?php echo esc_attr( $line ); ?>;">
            <?php foreach ( $items as $idx => $it ) :
                $color = $this->safe_color_css( $it['color'] ?? '' ) ?: '#999';
                $name  = $it['name'] ?? '';
                $sub   = $it['sub'] ?? '';
                $desc  = trim( (string) ( $it['desc'] ?? '' ) );
                $link  = $it['link_url'] ?? '';
                $pimg  = trim( (string) ( $it['image'] ?? '' ) );
                $num   = trim( (string) ( $it['number'] ?? '' ) );
                if ( $num === '' ) {
                    $num = str_pad( (string) ( $idx + 1 ), 2, '0', STR_PAD_LEFT );
                }
                $tag   = $link ? 'a' : 'div';
                $attrs = $link ? ' href="' . esc_url( $link ) . '"' : '';
                // Sfondo per-voce (row_bg: solid/gradient/image): emesso come regola CSS
                // di classe — non inline — così la regola :hover generica resta efficace
                // sulle voci senza sfondo, e quelle con sfondo lo mantengono in hover.
                $rowbg_cls = '';
                $row_bg    = $it['row_bg'] ?? null;
                if ( is_array( $row_bg ) && ! empty( $row_bg['type'] ) && $row_bg['type'] !== 'none' && class_exists( 'Olo_CSS_Builder' ) ) {
                    $rowbg_decl = trim( ( new Olo_CSS_Builder() )->get_bg_inline_css( $row_bg ) );
                    if ( $rowbg_decl !== '' ) {
                        $rowbg_cls = ' olo-hoverlist__row--' . intval( $idx );
                        $rowbg_rules[ intval( $idx ) ] = rtrim( $rowbg_decl, ';' ) . ';';
                    }
                }
            ?>
                <<?php echo $tag . $attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $tag is a fixed 'a'/'div' literal from the ternary above; $attrs is empty or built with esc_url() ?> class="olo-hoverlist__row<?php echo esc_attr( $rowbg_cls ); ?>" data-color="<?php echo esc_attr( $color ); ?>" data-name="<?php echo esc_attr( $name ); ?>" data-number="<?php echo esc_attr( $num ); ?>" data-image="<?php echo esc_url( $pimg ); ?>" style="<?php echo esc_attr( $row_base ); ?>">
                    <?php if ( $lead === 'number' ) : ?>
                        <span class="olo-hoverlist__num" style="font-family:<?php echo esc_attr( $mono ); ?>;font-size:13px;color:<?php echo esc_attr( $num_c ); ?>;transition:color .2s ease;"><?php echo esc_html( $num ); ?></span>
                        <span class="olo-hoverlist__nm" style="font-family:<?php echo esc_attr( $nfam ); ?>;font-weight:700;font-size:clamp(26px,3.4vw,<?php echo (int) $name_sz; ?>px);color:<?php echo esc_attr( $name_c ); ?>;line-height:1.1;<?php echo $name_up ? 'text-transform:uppercase;' : ''; ?>" data-olo-editable="<?php echo 'items.' . intval( $idx ) . '.name'; ?>"><?php echo esc_html( $name ); ?></span>
                        <?php if ( $desc !== '' ) : ?>
                            <span class="olo-hoverlist__desc" style="justify-self:end;font-size:<?php echo (int) $desc_sz; ?>px;color:<?php echo esc_attr( $desc_c ); ?>;max-width:30ch;text-align:right;" data-olo-editable="<?php echo 'items.' . intval( $idx ) . '.desc'; ?>"><?php echo esc_html( $desc ); ?></span>
                        <?php elseif ( $sub !== '' ) : ?>
                            <span class="olo-hoverlist__sub olo-hoverlist__desc" style="justify-self:end;font-family:<?php echo esc_attr( $mono ); ?>;font-size:<?php echo (int) $sub_sz; ?>px;letter-spacing:0.06em;color:<?php echo esc_attr( $sub_c ); ?>;text-align:right;<?php echo $upper ? 'text-transform:uppercase;' : ''; ?>" data-olo-editable="<?php echo 'items.' . intval( $idx ) . '.sub'; ?>"><?php echo esc_html( $sub ); ?></span>
                        <?php endif; ?>
                    <?php else : ?>
                        <span class="olo-hoverlist__sw" style="width:<?php echo (int) $sw_size; ?>px;height:<?php echo (int) $sw_size; ?>px;border-radius:<?php echo esc_attr( $sw_rad ); ?>;flex:none;background:<?php echo esc_attr( $color ); ?>;box-shadow:inset 0 0 0 1.5px rgba(246,233,236,.3);"></span>
                        <span class="olo-hoverlist__nm" style="font-family:<?php echo esc_attr( $nfam ); ?>;font-size:<?php echo (int) $name_sz; ?>px;color:<?php echo esc_attr( $name_c ); ?>;line-height:1.1;<?php echo $name_up ? 'text-transform:uppercase;' : ''; ?>" data-olo-editable="<?php echo 'items.' . intval( $idx ) . '.name'; ?>"><?php echo esc_html( $name ); ?></span>
                        <?php if ( $sub !== '' ) : ?>
                            <span class="olo-hoverlist__sub" style="margin-left:auto;font-family:<?php echo esc_attr( $mono ); ?>;font-size:<?php echo (int) $sub_sz; ?>px;letter-spacing:0.06em;color:<?php echo esc_attr( $sub_c ); ?>;<?php echo $upper ? 'text-transform:uppercase;' : ''; ?>" data-olo-editable="<?php echo 'items.' . intval( $idx ) . '.sub'; ?>"><?php echo esc_html( $sub ); ?></span>
                        <?php endif; ?>
                    <?php endif; ?>
                </<?php echo $tag; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed 'a'/'div' literal from the ternary above ?>>
            <?php endforeach; ?>
            <?php if ( $peek && $peek_mode === 'monitor' ) : ?>
            <div class="olo-hoverlist__peek olo-hoverlist__peek--mon" aria-hidden="true" style="position:fixed;z-index:90;pointer-events:none;opacity:0;width:200px;transform:translate(-50%,-112%) scale(.92);transition:opacity .16s ease,transform .16s ease;">
                <span class="olo-hl-mon-scr" style="position:relative;display:block;height:118px;border:1px solid <?php echo esc_attr( $mon_border ); ?>;background:repeating-linear-gradient(-45deg,<?php echo esc_attr( $mon_stripe ); ?> 0 9px,transparent 9px 18px),<?php echo esc_attr( $mon_scr_bg ); ?>;">
                    <i style="position:absolute;left:8px;top:8px;width:13px;height:13px;border:2px solid <?php echo esc_attr( $acc ); ?>;border-right:0;border-bottom:0;"></i>
                    <i style="position:absolute;right:8px;top:8px;width:13px;height:13px;border:2px solid <?php echo esc_attr( $acc ); ?>;border-left:0;border-bottom:0;"></i>
                    <i style="position:absolute;left:8px;bottom:8px;width:13px;height:13px;border:2px solid <?php echo esc_attr( $acc ); ?>;border-right:0;border-top:0;"></i>
                    <i style="position:absolute;right:8px;bottom:8px;width:13px;height:13px;border:2px solid <?php echo esc_attr( $acc ); ?>;border-left:0;border-top:0;"></i>
                    <span class="olo-hl-mon-rec" style="position:absolute;left:50%;top:50%;transform:translate(-50%,-50%);font-family:<?php echo esc_attr( $mono ); ?>;font-size:9.5px;font-weight:700;letter-spacing:.14em;color:<?php echo esc_attr( $acc ); ?>;white-space:nowrap;">&#9679; STILL</span>
                </span>
                <span class="olo-hl-mon-lab" style="display:flex;justify-content:space-between;gap:8px;background:<?php echo esc_attr( $mon_lab_bg ); ?>;border:1px solid <?php echo esc_attr( $line ); ?>;border-top:0;padding:7px 10px;font-family:<?php echo esc_attr( $mono ); ?>;font-size:10px;letter-spacing:.08em;text-transform:uppercase;color:<?php echo esc_attr( $mon_text ); ?>;">
                    <b class="olo-hl-mon-n" style="color:<?php echo esc_attr( $acc ); ?>;font-weight:700;">01</b><span class="olo-hl-mon-nm"></span>
                </span>
            </div>
            <?php elseif ( $peek ) : ?>
            <div class="olo-hoverlist__peek" aria-hidden="true" style="position:fixed;z-index:90;pointer-events:none;opacity:0;transform:translate(16px,-50%);transition:opacity .18s ease;">
                <span class="olo-hl-peek-img" style="display:block;width:<?php echo (int) $peek_w; ?>px;aspect-ratio:<?php echo esc_attr( $peek_r ); ?>;border-radius:14px;overflow:hidden;background:<?php echo esc_attr( $peek_ph ); ?>;background-size:cover;background-position:center;background-image:repeating-linear-gradient(135deg,rgba(255,255,255,.06) 0 16px,transparent 16px 32px);box-shadow:0 18px 50px rgba(0,0,0,.45);"></span>
            </div>
            <?php endif; ?>
        </div>
        <?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above: safe_color_css() whitelist for the colours, absint()/max()/min() clamps for the indent, and the internally generated $uid. ?>
        <style>
            <?php if ( $lead === 'number' ) : ?>
            .<?php echo $uid; ?> .olo-hoverlist__row:hover { padding-left: 18px; background: var(--olo-color-surface-alt, #101218); }
            .<?php echo $uid; ?> .olo-hoverlist__row:hover .olo-hoverlist__num { color: <?php echo $num_hc; ?>; }
            @media (max-width: 680px) {
                .<?php echo $uid; ?> .olo-hoverlist__row { grid-template-columns: 44px 1fr; }
                .<?php echo $uid; ?> .olo-hoverlist__desc { display: none; }
            }
            <?php else : ?>
            .<?php echo $uid; ?> .olo-hoverlist__row:hover { padding-left: <?php echo $indent; ?>px; background: <?php echo $hbg; ?>; }
            <?php endif; ?>
            .<?php echo $uid; ?> a.olo-hoverlist__row:focus-visible { outline: 2px solid <?php echo $name_c; ?>; outline-offset: -2px; }
            <?php foreach ( $rowbg_rules as $ri => $rdecl ) : ?>
            /* sfondo per-voce: vale a riposo E in hover (regola dopo la :hover generica) */
            .<?php echo $uid; ?> .olo-hoverlist__row--<?php echo (int) $ri; ?>,
            .<?php echo $uid; ?> .olo-hoverlist__row--<?php echo (int) $ri; ?>:hover { <?php echo $rdecl; ?> }
            <?php endforeach; ?>
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <?php if ( $peek ) : ?>
        <script>(function(){
            var root = document.querySelector('.<?php echo $uid; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- internally generated 'olo-hl-' . wp_rand() uid ?>');
            if (!root) { return; }
            var peek = root.querySelector('.olo-hoverlist__peek');
            if (!peek) { return; }
            // Sposta il peek nel <body>: position:fixed si rompe se un antenato ha transform
            // (entrance/reveal animations del frontend) — nel builder non c'è quel transform, da cui
            // "funziona solo nel builder". Nel body il fixed segue sempre il viewport.
            // Fuori da .olo-template le var --olo-color-* non risolvono (scatterebbero i
            // fallback hardcoded): copiamo sul peek i valori risolti dal root della tile.
            (function(){
                var cs = getComputedStyle(root);
                ['--olo-color-primary','--olo-color-border','--olo-color-muted','--olo-color-background','--olo-color-text'].forEach(function(v){
                    var val = cs.getPropertyValue(v);
                    if (val) { peek.style.setProperty(v, val); }
                });
            })();
            document.body.appendChild(peek);
            var rows = root.querySelectorAll('.olo-hoverlist__row');
            var isMon = peek.classList.contains('olo-hoverlist__peek--mon');
            if (isMon) {
                var labN = peek.querySelector('.olo-hl-mon-n');
                var labNm = peek.querySelector('.olo-hl-mon-nm');
                rows.forEach(function (r) {
                    r.addEventListener('mouseenter', function () {
                        if (labN) { labN.textContent = r.getAttribute('data-number') || ''; }
                        if (labNm) { labNm.textContent = r.getAttribute('data-name') || ''; }
                        peek.style.opacity = '1';
                        peek.style.transform = 'translate(-50%,-112%) scale(1)';
                    });
                    r.addEventListener('mousemove', function (e) {
                        peek.style.left = e.clientX + 'px';
                        peek.style.top = e.clientY + 'px';
                    });
                    r.addEventListener('mouseleave', function () {
                        peek.style.opacity = '0';
                        peek.style.transform = 'translate(-50%,-112%) scale(.92)';
                    });
                });
            } else {
                var img = peek.querySelector('.olo-hl-peek-img');
                var ph = 'repeating-linear-gradient(135deg,rgba(255,255,255,.06) 0 16px,transparent 16px 32px)';
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
            }
        })();</script>
        <?php endif; ?>
        <?php
        return ob_get_clean();
    }
}
