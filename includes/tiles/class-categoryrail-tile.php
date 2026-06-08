<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Tile Category Rail — rail orizzontale drag-scroll di tessere categoria/prodotto.
 * Estratta dai blueprint OLOthemes (CategoryRail/ProductRail data-hscroll). Render ==
 * Vue (CategoryRailTile.vue). Runtime drag inline scoped per istanza (no '&&').
 */
class Olo_CategoryRail_Tile extends Olo_Tile_Base {

    protected $type     = 'categoryrail';
    protected $name     = 'Category Rail';
    protected $icon     = 'dashicons-grid-view';
    protected $category = 'media';
    protected $defaults = [
        'items' => [
            [ 'image' => '', 'title' => 'Ceramics', 'subtitle' => '', 'link' => '#' ],
            [ 'image' => '', 'title' => 'Art & prints', 'subtitle' => '', 'link' => '#' ],
            [ 'image' => '', 'title' => 'Jewellery', 'subtitle' => '', 'link' => '#' ],
            [ 'image' => '', 'title' => 'Homeware', 'subtitle' => '', 'link' => '#' ],
            [ 'image' => '', 'title' => 'Vintage', 'subtitle' => '', 'link' => '#' ],
            [ 'image' => '', 'title' => 'Stationery', 'subtitle' => '', 'link' => '#' ],
        ],
        'card_width'     => 260,
        'card_aspect'    => '4/5',
        'gap'            => 16,
        'media_bg'       => '',
        'overlay_color'  => 'rgba(16,16,21,0.5)',
        'title_color'    => '#ffffff',
        'subtitle_color' => 'rgba(255,255,255,0.8)',
        'radius'         => 14,
        'show_hint'      => true,
        'hint_text'      => '← drag →',
        'hint_color'     => '',

        // Controlli additivi (no-op): padding caption + raggio tessera.
        // Default = resa attuale (caption 16px 18px, card 14px). Usati come
        // override SOLO se modificati rispetto al default (gating nel render).
        'cap_padding'    => [ 'top' => 16, 'right' => 18, 'bottom' => 16, 'left' => 18 ],
        'card_radius'    => [ 'tl' => 14, 'tr' => 14, 'br' => 14, 'bl' => 14 ],

        // KIT standard OLObuild — sfondo completo + ombra + bordo sul contenitore.
        // Default no-op: bg none / shadow none / border 0 → render invariato.
        'bg'                      => [ 'type' => 'none' ],
        'shadow'                  => 'none',
        'border'                  => [],
        'border_hover'            => [],
        'border_hover_duration'   => 300,
        'border_effect'           => 'none',
        'border_effect_intensity' => 'medium',
        'border_effect_color2'    => '',
        'border_effect_angle'     => 135,
        'border_effect_speed'     => 4,
    ];

    public function get_controls() { return []; }

    public function render( $settings, $style = [] ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'ocr-' . wp_rand( 10000, 99999 );

        $w     = max( 140, min( 480, intval( $s['card_width'] ) ) ) . 'px';
        $asp   = preg_replace( '/[^0-9\/]/', '', $s['card_aspect'] ?: '4/5' ) ?: '4/5';
        $gap   = intval( $s['gap'] ) . 'px';
        $mbg   = $this->safe_color_css( $s['media_bg'] ?? '' ) ?: 'var(--olo-color-surface-alt, #eceff3)';
        $ov    = $this->safe_color_css( $s['overlay_color'] ?? '' ) ?: 'rgba(16,16,21,0.5)';
        $tc    = $this->safe_color_css( $s['title_color'] ?? '' ) ?: '#ffffff';
        $sc    = $this->safe_color_css( $s['subtitle_color'] ?? '' ) ?: 'rgba(255,255,255,0.8)';
        $rad   = intval( $s['radius'] ) . 'px';
        $hint  = $this->safe_color_css( $s['hint_color'] ?? '' ) ?: 'var(--olo-color-text-muted, #6b7280)';
        $serif = "var(--olo-font-family-heading, 'Playfair Display',Georgia,serif)";
        $sans  = "var(--olo-font-family, 'Inter',-apple-system,sans-serif)";
        $show_hint = ! empty( $s['show_hint'] );

        $items = is_array( $s['items'] ) ? array_values( $s['items'] ) : [];
        if ( empty( $items ) ) return '';

        // ── KIT standard OLObuild: sfondo completo + ombra + bordo sul contenitore ──
        // Sfondo completo (override SOLO se valorizzato → default invariato).
        $bg_obj  = $s['bg'] ?? null;
        $bg_decl = '';
        if ( is_array( $bg_obj ) && ! empty( $bg_obj['type'] ) && $bg_obj['type'] !== 'none' && class_exists( 'Olo_CSS_Builder' ) ) {
            $bg_decl = ( new Olo_CSS_Builder() )->get_bg_inline_css( $bg_obj );
        }
        // Ombra (preset/custom). '' se none.
        $shadow_css = $this->build_shadow_decl( $s );
        // Bordo (come la coda di particlefx render).
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );

        // ── Override additivi gated (no-op coi default → byte-identici) ──
        // Raggio tessera: il raggio base resta $rad (es. "14px"). Se card_radius
        // è stato modificato rispetto al default {14,14,14,14}, sovrascrive.
        $card_radius_css = '';
        $crv = is_array( $s['card_radius'] ?? null ) ? $s['card_radius'] : [];
        $cr_tl = intval( $crv['tl'] ?? 14 );
        $cr_tr = intval( $crv['tr'] ?? 14 );
        $cr_br = intval( $crv['br'] ?? 14 );
        $cr_bl = intval( $crv['bl'] ?? 14 );
        $cr_default = ( $cr_tl === 14 ) ? true : false;
        if ( $cr_default ) { if ( $cr_tr !== 14 ) { $cr_default = false; } }
        if ( $cr_default ) { if ( $cr_br !== 14 ) { $cr_default = false; } }
        if ( $cr_default ) { if ( $cr_bl !== 14 ) { $cr_default = false; } }
        if ( ! $cr_default ) {
            $card_radius_css = $this->build_border_radius_css( $crv );
        }

        // Padding didascalia: base "16px 18px". Se cap_padding è stato modificato
        // rispetto al default {16,18,16,18}, sovrascrive con i 4 valori.
        $cap_pad_css = '';
        $cpv = is_array( $s['cap_padding'] ?? null ) ? $s['cap_padding'] : [];
        $cp_t = max( 0, intval( $cpv['top']    ?? 16 ) );
        $cp_r = max( 0, intval( $cpv['right']  ?? 18 ) );
        $cp_b = max( 0, intval( $cpv['bottom'] ?? 16 ) );
        $cp_l = max( 0, intval( $cpv['left']   ?? 18 ) );
        $cp_default = ( $cp_t === 16 ) ? true : false;
        if ( $cp_default ) { if ( $cp_r !== 18 ) { $cp_default = false; } }
        if ( $cp_default ) { if ( $cp_b !== 16 ) { $cp_default = false; } }
        if ( $cp_default ) { if ( $cp_l !== 18 ) { $cp_default = false; } }
        if ( ! $cp_default ) {
            $cap_pad_css = "{$cp_t}px {$cp_r}px {$cp_b}px {$cp_l}px";
        }

        // Decorazioni inline per la regola del contenitore .$uid (no-op coi default).
        $box_decl = '';
        if ( $bg_decl )    { $box_decl .= $bg_decl . ';'; }
        if ( $border_css ) { $box_decl .= $border_css; }
        if ( $shadow_css ) { $box_decl .= 'box-shadow:' . $shadow_css . ';'; }
        // position:relative serve agli effetti bordo (come in particlefx).
        if ( $box_decl || $border_effect_css ) { $box_decl .= 'position:relative;'; }

        ob_start();
        ?>
        <style>
            .<?php echo $uid; ?>{font-family:<?php echo $sans; ?>;<?php echo $box_decl; ?>}
            .<?php echo $uid; ?> .ocr-head{display:flex;justify-content:flex-end;margin-bottom:12px;}
            .<?php echo $uid; ?> .ocr-hint{font-size:11px;font-weight:600;letter-spacing:.16em;text-transform:uppercase;color:<?php echo $hint; ?>;}
            .<?php echo $uid; ?> .ocr-track{display:flex;gap:<?php echo $gap; ?>;overflow-x:auto;scroll-snap-type:x proximity;cursor:grab;padding-bottom:6px;-ms-overflow-style:none;scrollbar-width:none;}
            .<?php echo $uid; ?> .ocr-track::-webkit-scrollbar{display:none;}
            .<?php echo $uid; ?> .ocr-track.dragging{cursor:grabbing;}
            .<?php echo $uid; ?> .ocr-card{flex:0 0 <?php echo $w; ?>;width:<?php echo $w; ?>;aspect-ratio:<?php echo $asp; ?>;scroll-snap-align:start;position:relative;border-radius:<?php echo $rad; ?>;<?php if ( $card_radius_css ) { echo 'border-radius:' . $card_radius_css . ';'; } ?>overflow:hidden;text-decoration:none;display:block;background:<?php echo $mbg; ?>;}
            .<?php echo $uid; ?> .ocr-media{position:absolute;inset:0;background-size:cover;background-position:center;transition:transform .5s ease;}
            .<?php echo $uid; ?> .ocr-card:hover .ocr-media{transform:scale(1.05);}
            .<?php echo $uid; ?> .ocr-ov{position:absolute;inset:0;background:linear-gradient(to top, <?php echo $ov; ?>, transparent 62%);}
            .<?php echo $uid; ?> .ocr-cap{position:absolute;left:0;right:0;bottom:0;padding:16px 18px;<?php if ( $cap_pad_css ) { echo 'padding:' . $cap_pad_css . ';'; } ?>}
            .<?php echo $uid; ?> .ocr-t{font-family:<?php echo $serif; ?>;font-size:19px;line-height:1.2;margin:0;color:<?php echo $tc; ?>;}
            .<?php echo $uid; ?> .ocr-s{font-size:12.5px;margin:4px 0 0;color:<?php echo $sc; ?>;}
            .<?php echo $uid; ?> .ocr-card:focus-visible{outline:2px solid var(--olo-color-primary,#e1474f);outline-offset:3px;}
        </style>
        <div class="olo-categoryrail <?php echo esc_attr( $uid ); ?>">
            <?php if ( $show_hint && $s['hint_text'] !== '' ) : ?><div class="ocr-head"><span class="ocr-hint"><?php echo esc_html( $s['hint_text'] ); ?></span></div><?php endif; ?>
            <div class="ocr-track" data-ocr-track>
                <?php foreach ( $items as $it ) :
                    $img = isset( $it['image'] ) ? trim( $it['image'] ) : '';
                    $isty = $img !== '' ? ' style="background-image:url(' . esc_url( $img ) . ')"' : '';
                    $href = ! empty( $it['link'] ) ? $it['link'] : '#';
                ?>
                    <a class="ocr-card" href="<?php echo esc_url( $href ); ?>">
                        <span class="ocr-media"<?php echo $isty; ?>></span>
                        <span class="ocr-ov"></span>
                        <span class="ocr-cap">
                            <?php if ( ! empty( $it['title'] ) ) : ?><h3 class="ocr-t"><?php echo esc_html( $it['title'] ); ?></h3><?php endif; ?>
                            <?php if ( ! empty( $it['subtitle'] ) ) : ?><p class="ocr-s"><?php echo esc_html( $it['subtitle'] ); ?></p><?php endif; ?>
                        </span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        <script>
        (function(){
            var t=document.querySelector('.<?php echo $uid; ?> [data-ocr-track]'); if(!t){return;}
            var down=false,sx=0,sl=0,moved=0;
            t.addEventListener('pointerdown',function(e){down=true;sx=e.pageX;sl=t.scrollLeft;moved=0;t.classList.add('dragging');});
            t.addEventListener('pointermove',function(e){ if(down){ var dx=e.pageX-sx; moved=Math.max(moved,Math.abs(dx)); t.scrollLeft=sl-dx; } });
            function up(){ down=false; t.classList.remove('dragging'); }
            t.addEventListener('pointerup',up);
            t.addEventListener('pointerleave',up);
            t.addEventListener('click',function(e){ if(Math.max(0,moved-6)){ e.preventDefault(); } }, true);
        })();
        </script>
        <?php
        // ── Sistema bordi standard: hover + effetto (come particlefx) ──────
        if ( $border_hover_css || $border_effect_css ) {
            echo '<style>' . $border_hover_css . $border_effect_css . '</style>';
        }
        return ob_get_clean();
    }

    /**
     * Restituisce la dichiarazione box-shadow (valore, senza "box-shadow:")
     * dal setting shadow (preset sm/md/lg/xl o custom). '' se none.
     * Copiato dal pattern standard OLObuild (cfr. Olo_Particlefx_Tile).
     */
    private function build_shadow_decl( $s ) {
        $preset = $s['shadow'] ?? 'none';
        if ( $preset === 'none' || $preset === '' ) {
            return '';
        }
        if ( $preset === 'custom' ) {
            $h      = intval( $s['shadow_h'] ?? 0 );
            $v      = intval( $s['shadow_v'] ?? 4 );
            $blur   = max( 0, intval( $s['shadow_blur'] ?? 10 ) );
            $spread = intval( $s['shadow_spread'] ?? 0 );
            $color  = $this->safe_color_css( $s['shadow_color'] ?? '' ) ?: 'rgba(0,0,0,0.15)';
            $inset  = ! empty( $s['shadow_inset'] ) ? 'inset ' : '';
            return "{$inset}{$h}px {$v}px {$blur}px {$spread}px {$color}";
        }
        $map = [
            'sm' => '0 1px 2px rgba(16,24,40,.06), 0 6px 16px -10px rgba(16,24,40,.18)',
            'md' => '0 2px 4px rgba(16,24,40,.06), 0 14px 28px -12px rgba(22,38,61,.28)',
            'lg' => '0 8px 24px -6px rgba(16,24,40,.18), 0 18px 40px -12px rgba(22,38,61,.30)',
            'xl' => '0 12px 32px -8px rgba(16,24,40,.20), 0 28px 56px -14px rgba(22,38,61,.34)',
        ];
        return $map[ $preset ] ?? '';
    }
}
