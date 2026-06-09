<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Tile Finder — "zona interattiva" one-tap recommender: chip opzione → result card.
 * Estratto dai demo OLOthemes (setupFinder). Token-first: un colore `zone_accent`.
 * Render == Vue (FinderTile.vue). Runtime inline scoped per istanza (no '&&').
 *
 * ⚠️ Usato in 18 temi: le chiavi storiche NON cambiano. L'upgrade "tile perfetta"
 * aggiunge solo chiavi additive con default che riproducono l'aspetto storico
 * (preset 'custom', shadow 'none', card_radius 16, border 0, chip_radius 999…),
 * così i temi esistenti rendono identici.
 */
class Olo_Finder_Tile extends Olo_Tile_Base {

    protected $type     = 'finder';
    protected $name     = 'Finder';
    protected $icon     = 'dashicons-search';
    protected $category = 'interactive';
    protected $defaults = [
        'preset'  => 'custom',
        // storiche (INVARIATE)
        'eyebrow' => 'Trova il tuo',
        'heading' => 'Da dove vuoi partire?',
        'intro'   => '',
        'items'   => [
            [ 'option' => 'Opzione A', 'title' => 'Risultato A', 'text' => 'Descrizione del risultato.', 'meta' => '', 'cta_text' => '', 'cta_url' => '#', 'icon' => '' ],
            [ 'option' => 'Opzione B', 'title' => 'Risultato B', 'text' => 'Descrizione del risultato.', 'meta' => '', 'cta_text' => '', 'cta_url' => '#', 'icon' => '' ],
            [ 'option' => 'Opzione C', 'title' => 'Risultato C', 'text' => 'Descrizione del risultato.', 'meta' => '', 'cta_text' => '', 'cta_url' => '#', 'icon' => '' ],
        ],
        'zone_accent' => '',
        'zone_on'     => '#ffffff',
        'card_bg'     => '',
        'card_border' => '',
        'media_bg'    => '',
        'align'       => 'center',
        // additive (default = aspetto storico)
        'default_index'     => '0',
        'typography_preset' => '',
        'chip_bg'           => '',
        'chip_radius'       => '999',
        'card_radius'       => '16',
        'card_padding'      => [ 'top' => 34, 'right' => 38, 'bottom' => 34, 'left' => 38 ],
        'card_max_width'    => '680',
        'tile_padding'      => [ 'top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0 ],
        'shadow'            => 'none',
        'border'                  => [],
        'border_hover'            => [],
        'border_hover_duration'   => 300,
        'border_effect'           => 'none',
        'border_effect_intensity' => 'medium',
        'border_effect_color2'    => '',
        'border_effect_angle'     => 135,
        'border_effect_speed'     => 4,
        'text_effect'         => 'none',
        'text_effect_target'  => 'heading',
        'text_effect_speed'   => '50',
        'text_effect_delay'   => '0',
        'text_effect_loop'    => false,
        'text_effect_cursor'  => true,
        'text_effect_cursor_char' => '|',
        'text_effect_color'   => '',
        'text_effect_color_to'=> '',
        'text_effect_phrases' => '',
        'text_effect_pause'   => '1500',
        'wow_disable'           => false,
        'wow_backdrop_blur'     => 0,
        'wow_backdrop_saturate' => 100,
        'wow_border_style'      => 'solid',
        'wow_font_family'       => 'inherit',
        'wow_rotation'          => 0,
        'wow_perspective'       => 0,
        'wow_tilt_x'            => 0,
        'wow_glow_pulse'        => false,
        'wow_title_glow'        => false,
        'wow_scanlines'         => false,
        'wow_terminal_prompt'   => false,
    ];

    public function get_controls() { return []; }

    public function render( $settings, $style = [] ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'ofn-' . wp_rand( 10000, 99999 );

        $accent = $this->safe_color_css( $s['zone_accent'] ) ?: 'var(--olo-color-primary, #e1474f)';
        $on     = $this->safe_color_css( $s['zone_on'] ?? '' ) ?: '#ffffff';
        $cardbg = $this->safe_color_css( $s['card_bg'] ?? '' ) ?: 'var(--olo-color-surface-alt, #f6f7f9)';
        $cardbd = $this->safe_color_css( $s['card_border'] ?? '' ) ?: 'var(--olo-color-border, #e5e7eb)';
        $chipbg = $this->safe_color_css( $s['chip_bg'] ?? '' ) ?: 'transparent';
        $media_bg = $this->safe_color_css( $s['media_bg'] ?? '' ) ?: 'var(--olo-color-surface-alt, #1e1e1e)';
        $center = ( ( $s['align'] ?? 'center' ) === 'center' );
        $serif  = "var(--olo-font-family-heading, 'Playfair Display',Georgia,serif)";
        $sans   = "var(--olo-font-family, 'Inter',-apple-system,sans-serif)";
        $preset = sanitize_key( $s['preset'] ?? 'custom' );

        $items = is_array( $s['items'] ) ? array_values( $s['items'] ) : [];
        if ( empty( $items ) ) return '';
        $def_idx = max( 0, min( intval( $s['default_index'] ?? 0 ), count( $items ) - 1 ) );

        // ── box-model ──
        $chip_r = max( 0, min( 999, intval( $s['chip_radius'] ?? 999 ) ) );
        $cr     = $this->build_border_radius_css( $s['card_radius'] ?? '16' );
        $card_radius_css = 'border-radius:' . ( $cr ?: '16px' ) . ';';
        $cp     = is_array( $s['card_padding'] ?? null ) ? $s['card_padding'] : [];
        $card_pad = intval( $cp['top'] ?? 34 ) . 'px ' . intval( $cp['right'] ?? 38 ) . 'px ' . intval( $cp['bottom'] ?? 34 ) . 'px ' . intval( $cp['left'] ?? 38 ) . 'px';
        $card_mw  = max( 0, intval( $s['card_max_width'] ?? 680 ) ) ?: 680;
        $tpd      = is_array( $s['tile_padding'] ?? null ) ? $s['tile_padding'] : [];
        $tp_sum   = intval( $tpd['top'] ?? 0 ) + intval( $tpd['right'] ?? 0 ) + intval( $tpd['bottom'] ?? 0 ) + intval( $tpd['left'] ?? 0 );
        $tile_pad_css = $tp_sum > 0 ? 'padding:' . intval( $tpd['top'] ?? 0 ) . 'px ' . intval( $tpd['right'] ?? 0 ) . 'px ' . intval( $tpd['bottom'] ?? 0 ) . 'px ' . intval( $tpd['left'] ?? 0 ) . 'px;' : '';

        $shadow_val = Olo_Tile_Utils::shadow_value( $s, 'shadow' );
        $shadow_css = ( $shadow_val && $shadow_val !== 'none' ) ? 'box-shadow:' . $shadow_val . ';' : '';

        // tipografia globale (opzionale)
        $tp_key  = sanitize_key( $s['typography_preset'] ?? '' );
        $typo_css = $tp_key ? "font-family:var(--olo-font-{$tp_key}-family);font-weight:var(--olo-font-{$tp_key}-weight);letter-spacing:var(--olo-font-{$tp_key}-letter-spacing);" : '';

        // bordo card: 'border' avanzato (se impostato) sostituisce il card_border semplice
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $card_border_decl  = $border_css ? $border_css : ( 'border:1px solid ' . $cardbd . ';' );
        $card_sel          = ".{$uid} .ofn-res";
        $border_hover_css  = $this->build_border_hover_css( $card_sel, $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( $card_sel, $s['border'] ?? [], $s );

        // text effects
        list( $h_cls, $h_data ) = $this->tfx_attrs( $s, 'heading', wp_strip_all_tags( $s['heading'] ?? '' ) );

        ob_start();
        ?>
        <style>
            .<?php echo $uid; ?>{ --fn-accent:<?php echo $accent; ?>; --fn-on:<?php echo $on; ?>; font-family:<?php echo $sans; ?>; <?php echo $typo_css; ?><?php if ( $center ) echo 'text-align:center;'; ?><?php echo $tile_pad_css; ?> }
            .<?php echo $uid; ?> .ofn-eyebrow{font-size:12px;font-weight:700;letter-spacing:.16em;text-transform:uppercase;color:var(--fn-accent);display:block;margin-bottom:10px;}
            .<?php echo $uid; ?> .ofn-h{font-family:<?php echo $serif; ?>;font-size:clamp(26px,3.6vw,42px);line-height:1.12;margin:0;color:var(--olo-color-text,#111827);}
            .<?php echo $uid; ?> .ofn-h em{font-style:italic;color:var(--fn-accent);}
            .<?php echo $uid; ?> .ofn-intro{font-size:15.5px;line-height:1.6;opacity:.8;margin:14px auto 0;max-width:560px;<?php echo $center ? '' : 'margin-left:0;'; ?>}
            .<?php echo $uid; ?> .ofn-chips{display:flex;flex-wrap:wrap;gap:10px;margin:26px 0 24px;<?php echo $center ? 'justify-content:center;' : ''; ?>}
            .<?php echo $uid; ?> .ofn-chip{font-family:<?php echo $sans; ?>;font-weight:600;font-size:13.5px;color:var(--olo-color-text,#111827);background:<?php echo $chipbg; ?>;border:1px solid var(--olo-color-border,#e5e7eb);border-radius:<?php echo $chip_r; ?>px;padding:10px 18px;cursor:pointer;transition:all .18s;display:inline-flex;align-items:center;gap:8px;}
            .<?php echo $uid; ?> .ofn-chip:hover{border-color:var(--fn-accent);color:var(--fn-accent);}
            .<?php echo $uid; ?> .ofn-chip.on{background:var(--fn-accent);border-color:var(--fn-accent);color:var(--fn-on);}
            .<?php echo $uid; ?> .ofn-chip:focus-visible{outline:2px solid var(--fn-accent);outline-offset:3px;}
            .<?php echo $uid; ?> .ofn-chip .ofn-ic{width:16px;height:16px;display:inline-flex;}
            .<?php echo $uid; ?> .ofn-chip .ofn-ic svg{width:100%;height:100%;}
            .<?php echo $uid; ?> .ofn-res{display:none;background:<?php echo $cardbg; ?>;<?php echo $card_border_decl; ?><?php echo $card_radius_css; ?>padding:<?php echo $card_pad; ?>;text-align:left;max-width:<?php echo $card_mw; ?>px;<?php echo $center ? 'margin:0 auto;' : ''; ?><?php echo $shadow_css; ?>}
            .<?php echo $uid; ?> .ofn-res.show{display:block;animation:ofnfade .35s ease;}
            @keyframes ofnfade{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:none}}
            .<?php echo $uid; ?> .ofn-res__meta{font-size:12px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--fn-accent);}
            .<?php echo $uid; ?> .ofn-res--media{gap:32px;align-items:center;}
            .<?php echo $uid; ?> .ofn-res--media.show{display:flex;}
            .<?php echo $uid; ?> .ofn-media{width:190px;flex:0 0 auto;aspect-ratio:190/240;border-radius:2px;overflow:hidden;position:relative;background:<?php echo $media_bg; ?>;background-size:cover;background-position:center;background-image:repeating-linear-gradient(135deg, rgba(255,255,255,.06) 0 16px, transparent 16px 32px);}
            .<?php echo $uid; ?> .ofn-media__lbl{position:absolute;left:12px;bottom:10px;font-size:10px;letter-spacing:.04em;text-transform:uppercase;color:rgba(255,255,255,.4);}
            .<?php echo $uid; ?> .ofn-res__body{flex:1;min-width:0;}
            .<?php echo $uid; ?> .ofn-kicker{display:block;font-size:10.5px;font-weight:700;letter-spacing:.18em;text-transform:uppercase;color:var(--fn-accent);margin-bottom:6px;}
            .<?php echo $uid; ?> .ofn-res--media .ofn-res__meta{margin-top:14px;}
            @media(max-width:600px){.<?php echo $uid; ?> .ofn-res--media{flex-direction:column;}.<?php echo $uid; ?> .ofn-media{width:100%;aspect-ratio:auto;height:240px;}}
            .<?php echo $uid; ?> .ofn-res__t{font-family:<?php echo $serif; ?>;font-size:clamp(22px,3vw,30px);line-height:1.15;margin:8px 0 0;color:var(--olo-color-text,#111827);}
            .<?php echo $uid; ?> .ofn-res__x{font-size:15.5px;line-height:1.6;opacity:.8;margin:12px 0 0;}
            .<?php echo $uid; ?> .ofn-res__cta{display:inline-flex;align-items:center;gap:8px;margin-top:20px;font-weight:600;font-size:14px;color:var(--fn-on);background:var(--fn-accent);padding:11px 22px;border-radius:999px;text-decoration:none;transition:transform .18s;}
            .<?php echo $uid; ?> .ofn-res__cta:hover{transform:translateY(-1px);}
            .<?php echo $uid; ?> .ofn-res__cta:focus-visible{outline:2px solid var(--fn-accent);outline-offset:3px;}
            @media(max-width:640px){.<?php echo $uid; ?> .ofn-res{padding:22px;}}
        </style>
        <?php
        $wow_css = $this->build_wow_effects_css( $s, $card_sel, '.ofn-res__t' );
        if ( $border_hover_css || $border_effect_css || $wow_css ) {
            echo '<style>' . $border_hover_css . $border_effect_css . $wow_css . '</style>';
        }
        ?>
        <div class="olo-finder olo-finder-preset-<?php echo esc_attr( $preset ); ?> <?php echo esc_attr( $uid ); ?>" data-finder>
            <?php if ( $s['eyebrow'] !== '' ) : ?><span class="ofn-eyebrow" data-olo-editable="eyebrow"><?php echo esc_html( $s['eyebrow'] ); ?></span><?php endif; ?>
            <?php if ( $s['heading'] !== '' ) : ?><h2 class="ofn-h<?php echo $h_cls; ?>"<?php echo $h_data; ?> data-olo-editable="heading"><?php echo wp_kses_post( $s['heading'] ); ?></h2><?php endif; ?>
            <?php if ( $s['intro'] !== '' ) : ?><p class="ofn-intro" data-olo-editable="intro"><?php echo esc_html( $s['intro'] ); ?></p><?php endif; ?>
            <div class="ofn-chips" role="tablist">
                <?php foreach ( $items as $i => $it ) :
                    $icon = '';
                    if ( ! empty( $it['icon'] ) ) { $icon = $this->render_icon_html( $it['icon'], 1 ); }
                ?>
                    <button class="ofn-chip<?php echo $i === $def_idx ? ' on' : ''; ?>" type="button" role="tab" aria-selected="<?php echo $i === $def_idx ? 'true' : 'false'; ?>" data-fn-opt="<?php echo intval( $i ); ?>"><?php if ( $icon ) : ?><span class="ofn-ic"><?php echo $icon; ?></span><?php endif; ?><?php echo esc_html( $it['option'] ?? ( 'Opzione ' . ( $i + 1 ) ) ); ?></button>
                <?php endforeach; ?>
            </div>
            <?php foreach ( $items as $i => $it ) :
                list( $t_cls, $t_data ) = $this->tfx_attrs( $s, 'title', wp_strip_all_tags( $it['title'] ?? '' ) );
                $f_img    = isset( $it['image'] ) ? trim( $it['image'] ) : '';
                $f_mlabel = $it['media_label'] ?? '';
                $f_kicker = $it['kicker'] ?? '';
                $f_mb     = $this->bg_media_parts( $it['media_bg'] ?? null, $uid . '-i' . $i );
                $f_media  = ( $f_img !== '' || $f_mlabel !== '' || $f_mb['has'] );
                if ( $f_mb['has'] ) {
                    $f_mstyle = $f_mb['css'] !== '' ? ' style="' . esc_attr( $f_mb['css'] ) . '"' : '';
                } else {
                    $f_mstyle = $f_img !== '' ? ' style="background-image:url(' . esc_url( $f_img ) . ')"' : '';
                }
            ?>
                <div class="ofn-res<?php echo $f_media ? ' ofn-res--media' : ''; ?><?php echo $i === $def_idx ? ' show' : ''; ?>" data-fn-res="<?php echo intval( $i ); ?>" role="tabpanel">
                    <?php if ( $f_media ) : ?>
                        <div class="ofn-media"<?php echo $f_mstyle; ?>><?php if ( $f_mb['has'] && $f_mb['markup'] !== '' ) { echo $f_mb['markup']; } ?><?php if ( ! $f_mb['has'] && $f_img === '' && $f_mlabel !== '' ) : ?><span class="ofn-media__lbl"><?php echo esc_html( $f_mlabel ); ?></span><?php endif; ?></div>
                        <div class="ofn-res__body">
                            <?php if ( $f_kicker !== '' ) : ?><span class="ofn-kicker"><?php echo esc_html( $f_kicker ); ?></span><?php endif; ?>
                            <?php if ( ! empty( $it['title'] ) ) : ?><h3 class="ofn-res__t<?php echo $t_cls; ?>"<?php echo $t_data; ?>><?php echo esc_html( $it['title'] ); ?></h3><?php endif; ?>
                            <?php if ( ! empty( $it['text'] ) ) : ?><p class="ofn-res__x"><?php echo esc_html( $it['text'] ); ?></p><?php endif; ?>
                            <?php if ( ! empty( $it['meta'] ) ) : ?><div class="ofn-res__meta"><?php echo esc_html( $it['meta'] ); ?></div><?php endif; ?>
                            <?php if ( ! empty( $it['cta_text'] ) ) : ?><a class="ofn-res__cta" href="<?php echo esc_url( $it['cta_url'] ?: '#' ); ?>"><?php echo esc_html( $it['cta_text'] ); ?></a><?php endif; ?>
                        </div>
                    <?php else : ?>
                        <?php if ( $f_kicker !== '' ) : ?><span class="ofn-kicker"><?php echo esc_html( $f_kicker ); ?></span><?php endif; ?>
                        <?php if ( ! empty( $it['meta'] ) ) : ?><div class="ofn-res__meta"><?php echo esc_html( $it['meta'] ); ?></div><?php endif; ?>
                        <?php if ( ! empty( $it['title'] ) ) : ?><h3 class="ofn-res__t<?php echo $t_cls; ?>"<?php echo $t_data; ?>><?php echo esc_html( $it['title'] ); ?></h3><?php endif; ?>
                        <?php if ( ! empty( $it['text'] ) ) : ?><p class="ofn-res__x"><?php echo esc_html( $it['text'] ); ?></p><?php endif; ?>
                        <?php if ( ! empty( $it['cta_text'] ) ) : ?><a class="ofn-res__cta" href="<?php echo esc_url( $it['cta_url'] ?: '#' ); ?>"><?php echo esc_html( $it['cta_text'] ); ?></a><?php endif; ?>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
        <script>
        (function(){
            var root=document.querySelector('.<?php echo $uid; ?>[data-finder]'); if(!root){return;}
            if(root._oloInit){return;} root._oloInit=1;
            var chips=root.querySelectorAll('[data-fn-opt]');
            var panels=root.querySelectorAll('[data-fn-res]');
            function show(idx){
                var k=String(idx);
                for(var i=0;i<chips.length;i++){ var ca=chips[i].getAttribute('data-fn-opt')===k; chips[i].classList.toggle('on', ca); chips[i].setAttribute('aria-selected', ca?'true':'false'); }
                for(var j=0;j<panels.length;j++){ panels[j].classList.toggle('show', panels[j].getAttribute('data-fn-res')===k); }
            }
            for(var n=0;n<chips.length;n++){ (function(c){ c.addEventListener('click', function(){ show(c.getAttribute('data-fn-opt')); }); })(chips[n]); }
        })();
        </script>
        <?php
        $tfx_css = $this->tfx_css( $s, '.' . $uid );
        if ( $tfx_css ) echo '<style>' . $tfx_css . '</style>';
        $this->tfx_print_script();
        return ob_get_clean();
    }
}
