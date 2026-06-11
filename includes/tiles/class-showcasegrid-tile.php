<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Tile Showcase Grid — card-immagine grandi linkate: media + velo, kicker + titolo in basso,
 * freccia circolare in alto a dx che si anima all'hover (cerchio → accento, freccia ruota).
 * Render == Vue. Nessun JS. Valori 1:1 dal blueprint OLOthemes (.teams-grid/.team).
 */
class Olo_ShowcaseGrid_Tile extends Olo_Tile_Base {

    protected $type     = 'showcasegrid';
    protected $name     = 'Showcase Grid';
    protected $icon     = 'dashicons-grid-view';
    protected $category = 'media';
    protected $defaults = [
        'items' => [
            [ 'image' => '', 'media_label' => "Men's squad", 'kicker' => '3 squads', 'title' => 'Men', 'link' => '#' ],
        ],
        'columns'           => 3,
        'gap'               => 18,
        'aspect'            => '3/3.5',
        'radius'            => 20,
        'media_bg'          => '#0f3a2a',
        'veil_color'        => '#0a2a1e',
        'kicker_color'      => '',
        'title_color'       => '#ffffff',
        'arrow_bg'          => 'rgba(255,255,255,0.14)',
        'arrow_color'       => '#ffffff',
        'arrow_hover_bg'    => '',
        'arrow_hover_color' => '#0a2a1e',
        'show_arrow'        => true,
        'title_size'        => 34,
        'title_weight'      => '900',
        'title_uppercase'   => true,
        'kicker_size'       => 12,

        // Spaziatura interna card — default = padding attuale (26px) → no-op.
        'card_padding'      => [ 'top' => 26, 'right' => 26, 'bottom' => 26, 'left' => 26 ],
        // Raggio card a 4 angoli (override gated) — default {0,0,0,0} → usa il legacy 'radius' → no-op.
        'card_radius'       => [ 'tl' => 0, 'tr' => 0, 'br' => 0, 'bl' => 0 ],

        // KIT standard OLObuild (contenitore) — default no-op (render invariato).
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

    /** "#rrggbb" → "r,g,b". */
    private function hex_rgb( $hex, $fallback = '10,42,30' ) {
        $hex = ltrim( (string) $hex, '#' );
        if ( strlen( $hex ) === 3 ) { $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2]; }
        if ( ! preg_match( '/^[0-9a-fA-F]{6}$/', $hex ) ) { return $fallback; }
        return hexdec( substr($hex,0,2) ) . ',' . hexdec( substr($hex,2,2) ) . ',' . hexdec( substr($hex,4,2) );
    }

    public function render( $settings, $style = [] ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'ocg-' . wp_rand( 10000, 99999 );

        $cols   = max( 1, min( 4, intval( $s['columns'] ) ) );
        $gap    = intval( $s['gap'] ) . 'px';
        $asp    = preg_replace( '/[^0-9.\/]/', '', $s['aspect'] ?: '3/3.5' ) ?: '3/3.5';
        $rad    = intval( $s['radius'] ) . 'px';

        // Raggio card a 4 angoli: OVERRIDE solo se valorizzato (default {0,0,0,0} → usa $rad legacy → no-op).
        $card_radius_css = $this->build_border_radius_css( $s['card_radius'] ?? [] );
        $card_rad = ( $card_radius_css !== '' ) ? $card_radius_css : $rad;

        // Padding interno card: default 26px su tutti i lati (= valore attuale → no-op).
        $cp  = is_array( $s['card_padding'] ?? null ) ? $s['card_padding'] : [];
        $cpt = intval( $cp['top']    ?? 26 );
        $cpr = intval( $cp['right']  ?? 26 );
        $cpb = intval( $cp['bottom'] ?? 26 );
        $cpl = intval( $cp['left']   ?? 26 );
        // Shorthand a valore unico se i 4 lati sono uguali (byte-identico al default '26px').
        $card_pad = ( $cpt === $cpr && $cpr === $cpb && $cpb === $cpl )
            ? "{$cpt}px"
            : "{$cpt}px {$cpr}px {$cpb}px {$cpl}px";
        $mbg    = $this->safe_color_css( $s['media_bg'] ?? '' ) ?: '#0f3a2a';
        $veilrgb= $this->hex_rgb( $s['veil_color'] ?? '#0a2a1e' );
        $kick   = $this->safe_color_css( $s['kicker_color'] ?? '' ) ?: 'var(--olo-color-primary, #c8ff3c)';
        $tcol   = $this->safe_color_css( $s['title_color'] ?? '' ) ?: '#ffffff';
        $arrbg  = $this->safe_color_css( $s['arrow_bg'] ?? '' ) ?: 'rgba(255,255,255,0.14)';
        $arrcol = $this->safe_color_css( $s['arrow_color'] ?? '' ) ?: '#ffffff';
        $arrhbg = $this->safe_color_css( $s['arrow_hover_bg'] ?? '' ) ?: 'var(--olo-color-primary, #c8ff3c)';
        $arrhc  = $this->safe_color_css( $s['arrow_hover_color'] ?? '' ) ?: '#0a2a1e';
        $disp   = "var(--olo-font-family-heading, 'Archivo',-apple-system,sans-serif)";
        $sans   = "var(--olo-font-family, 'Work Sans',-apple-system,sans-serif)";

        $show_arrow  = ! empty( $s['show_arrow'] );
        $title_size  = max( 10, intval( $s['title_size'] ?? 34 ) );
        $title_wt    = in_array( (string) ( $s['title_weight'] ?? '900' ), [ '400', '500', '600', '700', '900' ], true ) ? (string) $s['title_weight'] : '900';
        $title_tt    = ! empty( $s['title_uppercase'] ) ? 'uppercase' : 'none';
        $kicker_size = max( 8, intval( $s['kicker_size'] ?? 12 ) );

        $items = is_array( $s['items'] ) ? array_values( $s['items'] ) : [];
        if ( empty( $items ) ) return '';

        // Modalità editoriale asimmetrica: attiva se almeno un item ha span > 0.
        $has_spans = false;
        foreach ( $items as $it_chk ) { if ( intval( $it_chk['span'] ?? 0 ) > 0 ) { $has_spans = true; break; } }
        $grid_tpl   = $has_spans ? 'repeat(12,1fr)' : "repeat({$cols},1fr)";
        $grid_align = $has_spans ? 'align-items:end;' : '';

        // ── KIT standard OLObuild (contenitore) ───────────────────────────
        // Sfondo completo opzionale: override SOLO se valorizzato (default none → invariato).
        $bg_obj  = $s['bg'] ?? null;
        $bg_decl = '';
        if ( is_array( $bg_obj ) && ! empty( $bg_obj['type'] ) && $bg_obj['type'] !== 'none' && class_exists( 'Olo_CSS_Builder' ) ) {
            $bg_decl = ( new Olo_CSS_Builder() )->get_bg_inline_css( $bg_obj );
        }
        // Ombra (preset/custom). '' se none.
        $shadow_css = $this->build_shadow_decl( $s );
        // Bordo standard.
        $border_css = $this->build_border_css( $s['border'] ?? [] );

        // Dichiarazioni extra sul contenitore .$uid (sfondo/ombra/bordo).
        // position:relative serve agli effetti bordo (come particlefx).
        $kit_decl = '';
        if ( $bg_decl !== '' )    { $kit_decl .= $bg_decl . ';'; }
        if ( $shadow_css !== '' ) { $kit_decl .= 'box-shadow:' . $shadow_css . ';'; }
        if ( $border_css !== '' ) { $kit_decl .= $border_css; }
        $kit_pos = ( $kit_decl !== '' ) ? 'position:relative;' : '';

        ob_start();
        ?>
        <?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above (safe_color_css/intval/whitelists/hex_rgb/Olo_CSS_Builder which escapes internally/generated uid). ?>
        <style>
            .<?php echo $uid; ?>{display:grid;grid-template-columns:<?php echo $grid_tpl; ?>;gap:<?php echo $gap; ?>;<?php echo $grid_align; ?>font-family:<?php echo $sans; ?>;<?php echo $kit_pos . $kit_decl; ?>}
            .<?php echo $uid; ?> .ocg-card{position:relative;border-radius:<?php echo $card_rad; ?>;overflow:hidden;aspect-ratio:<?php echo $asp; ?>;display:flex;flex-direction:column;justify-content:flex-end;padding:<?php echo $card_pad; ?>;color:#fff;text-decoration:none;background:<?php echo $mbg; ?>;}
            .<?php echo $uid; ?> .ocg-media{position:absolute;inset:0;z-index:0;background:<?php echo $mbg; ?>;background-size:cover;background-position:center;background-image:repeating-linear-gradient(135deg, rgba(255,255,255,.05) 0 18px, rgba(255,255,255,0) 18px 36px);}
            .<?php echo $uid; ?> .ocg-medialabel{position:absolute;left:14px;bottom:12px;font-size:11px;letter-spacing:.04em;text-transform:uppercase;font-weight:600;color:rgba(255,255,255,.4);z-index:1;}
            .<?php echo $uid; ?> .ocg-veil{position:absolute;inset:0;z-index:1;background:linear-gradient(180deg, rgba(<?php echo $veilrgb; ?>,.05) 30%, rgba(<?php echo $veilrgb; ?>,.9) 100%);}
            .<?php echo $uid; ?> .ocg-k{position:relative;z-index:2;font-weight:700;font-size:<?php echo (int) $kicker_size; ?>px;letter-spacing:.12em;text-transform:uppercase;color:<?php echo $kick; ?>;}
            .<?php echo $uid; ?> .ocg-t{position:relative;z-index:2;font-family:<?php echo $disp; ?>;font-weight:<?php echo $title_wt; ?>;font-size:<?php echo (int) $title_size; ?>px;text-transform:<?php echo $title_tt; ?>;margin-top:6px;color:<?php echo $tcol; ?>;line-height:1;}
            .<?php echo $uid; ?> .ocg-arr{position:absolute;z-index:2;top:24px;right:24px;width:44px;height:44px;border-radius:50%;background:<?php echo $arrbg; ?>;display:grid;place-items:center;transition:background .25s, transform .25s;}
            .<?php echo $uid; ?> .ocg-arr svg{width:19px;height:19px;color:<?php echo $arrcol; ?>;transition:color .25s;}
            .<?php echo $uid; ?> .ocg-card:hover .ocg-arr{background:<?php echo $arrhbg; ?>;transform:rotate(-45deg);}
            .<?php echo $uid; ?> .ocg-card:hover .ocg-arr svg{color:<?php echo $arrhc; ?>;}
            .<?php echo $uid; ?> .ocg-card:hover .ocg-media{transform:scale(1.04);}
            .<?php echo $uid; ?> .ocg-media{transition:transform .5s ease;}
            .<?php echo $uid; ?> .ocg-card:focus-visible{outline:2px solid <?php echo $arrhbg; ?>;outline-offset:3px;}
            @media(max-width:880px){.<?php echo $uid; ?>{grid-template-columns:1fr;}}
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <div class="olo-showcasegrid <?php echo esc_attr( $uid ); ?>">
            <?php foreach ( $items as $ci => $it ) :
                $img = isset( $it['image'] ) ? trim( $it['image'] ) : '';
                $href = ! empty( $it['link'] ) ? $it['link'] : '#';
                $mb  = $this->bg_media_parts( $it['media_bg'] ?? null, $uid . '-i' . $ci );
                if ( $mb['has'] ) {
                    $msty = $mb['css'] !== '' ? ' style="' . esc_attr( $mb['css'] ) . '"' : '';
                } else {
                    $msty = $img !== '' ? ' style="background-image:url(' . esc_url( $img ) . ')"' : '';
                }
                $card_inline = '';
                if ( $has_spans ) {
                    $sp = intval( $it['span'] ?? 0 ); $sp = $sp > 0 ? min( 12, $sp ) : 4;
                    $it_asp = ! empty( $it['aspect'] ) ? preg_replace( '/[^0-9.\/]/', '', $it['aspect'] ) : $asp;
                    $card_inline = ' style="grid-column:span ' . $sp . ';aspect-ratio:' . $it_asp . '"';
                }
            ?>
                <a class="ocg-card" href="<?php echo esc_url( $href ); ?>"<?php echo $card_inline; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- style attr built from intval span and aspect filtered to [0-9./] via preg_replace above ?>>
                    <span class="ocg-media"<?php echo $msty; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- style attr escaped via esc_attr/esc_url above ?>></span>
                    <?php if ( $mb['has'] && $mb['markup'] !== '' ) { echo $mb['markup']; } // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- media markup generated by Olo_CSS_Builder::get_bg_html_markup() (escapes internally) ?>
                    <?php if ( ! $mb['has'] && $img === '' && ! empty( $it['media_label'] ) ) : ?><span class="ocg-medialabel"><?php echo esc_html( $it['media_label'] ); ?></span><?php endif; ?>
                    <span class="ocg-veil"></span>
                    <?php if ( $show_arrow ) : ?><span class="ocg-arr"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M7 17 17 7M9 7h8v8"/></svg></span><?php endif; ?>
                    <?php if ( ! empty( $it['kicker'] ) ) : ?><span class="ocg-k"><?php echo esc_html( $it['kicker'] ); ?></span><?php endif; ?>
                    <?php if ( ! empty( $it['title'] ) ) : ?><span class="ocg-t"><?php echo esc_html( $it['title'] ); ?></span><?php endif; ?>
                </a>
            <?php endforeach; ?>
        </div>
        <?php
        // ── Sistema bordi standard: hover + effetti (come particlefx) ──────
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
        if ( $border_hover_css || $border_effect_css ) {
            echo '<style>' . $border_hover_css . $border_effect_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS built by Olo_Tile_Base border helpers from sanitized values
        }
        return ob_get_clean();
    }

    /**
     * Restituisce la dichiarazione box-shadow (valore, senza "box-shadow:")
     * dal setting shadow (preset sm/md/lg/xl o custom). '' se none.
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
