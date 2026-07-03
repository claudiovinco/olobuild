<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Tile Hero — Studio (Editorial + OLOmap) : hero "studio editoriale" su fondo inchiostro.
 * Riga meta mono (EST. / coordinate / divisione), eyebrow lime, H1 display industriale
 * gigante su 2 righe — riga 1 con entrata lettere una-a-una (CSS, reduced-motion safe),
 * riga 2 in outline che si RIEMPIE di accento allo scroll (--fill) — sub con <b>, 2 CTA
 * (fill + ghost) e a destra l'OLOmap: infografica SVG animata della mappa del sistema
 * (camera dive su 4 livelli, breadcrumb readout sulla didascalia, indicatore L1/L4).
 * Parallax interno leggero su meta e media. Runtime JS per-istanza scoped al proprio
 * $uid (nessun `&&`/`||` negli script inline). Render == Vue (StudioHeroTile.vue).
 * Estratta dal blueprint "Clod — Evoluzione (supertemplate) v2".
 *
 * Contratto effetti mouse (runtime esterno, qui solo marker inerti):
 * - box media (olomap / immagine)  → data-olo-tilt-child
 * - CTA principale                 → data-olo-cta
 * - heading principale             → data-olo-wave
 */
class Olobuild_StudioHero_Tile extends Olobuild_Tile_Base {

    protected $type     = 'studiohero';
    protected $name     = 'Hero — Studio (Editorial + OLOmap)';
    protected $icon     = 'dashicons-networking';
    protected $category = 'marketing';
    protected $defaults = [
        'media_bg'           => [ 'type' => 'none' ],
        'eyebrow'            => 'R&S · divisione idee',
        'eyebrow_color'      => '',
        'title_line1'        => 'Visual',
        'title_line2'        => 'studio',
        'title_color'        => '',
        'title_size_min'     => 74,
        'title_size_max'     => 210,
        'line2_stroke_width' => 1.4,
        'line2_stroke_color' => '',
        'line2_scroll_fill'  => true,
        'line2_fill_color'   => '',
        'letters_entrance'   => true,
        'subtitle'           => 'Aiuto le aziende a <b>farsi vedere</b>: strategia, produzione media e identità visiva, con contenuti originali che lavorano davvero.',
        'subtitle_color'     => '',
        'show_meta'          => true,
        'meta_items'         => [
            [ 'strong' => 'EST.', 'text' => 'Trento — Italia' ],
            [ 'strong' => '46.07°N', 'text' => '11.12°E' ],
            [ 'strong' => 'R&S', 'text' => 'divisione idee' ],
            [ 'strong' => '2026', 'text' => '— project media manager' ],
        ],
        'cta1_text'          => 'Progettiamo assieme',
        'cta1_url'           => '#contatto',
        'cta1_show_arrow'    => true,
        'cta2_text'          => 'Selezione progetti',
        'cta2_url'           => '#lavori',
        'accent_color'       => '',
        'media_mode'         => 'olomap',
        'media_image'        => '',
        'media_object_position' => 'center center',
        'media_label'        => 'Visual studio — still',
        'cap_text'           => 'OLObuild · sistema',
        'map_label'          => 'Mappa del sistema',
        'map_root'           => 'OLObuild',
        'map_l1'             => 'Forge,Prisma*,Saffron,Soundwave,+46\ntemi',
        'map_l2'             => 'Hero*,Galleria,Griglia,CTA',
        'map_l3'             => 'Spazi,Bordi,Ombra,Colore*',
        'map_tokens'         => [
            [ 'label' => 'Primario', 'color' => '#e1474f' ],
            [ 'label' => 'Accento', 'color' => '#f4a23b' ],
            [ 'label' => 'Lime', 'color' => '#C6F24E' ],
            [ 'label' => 'Scuro', 'color' => '#16263d' ],
        ],
        'map_duration'       => 21,
        'parallax_internal'  => true,
        'bg_color'           => '',

        // Spaziatura (gated): padding di base clamp(40px,7vw,84px) 0 clamp(44px,6vw,72px).
        // Override attivo SOLO se pad_custom=true → no-op coi default.
        'pad_custom'         => false,
        'content_padding'    => [ 'top' => 84, 'right' => 0, 'bottom' => 72, 'left' => 0 ],

        // KIT standard OLObuild — additivi, no-op coi default (sfondo none, ombra none, bordo 0)
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
        $uid = 'osth-' . wp_rand( 10000, 99999 );

        // ── Font (ruoli tema, fallback blueprint Clod) ──
        $disp = "var(--olo-font-family-heading, 'Big Shoulders Display', sans-serif)";
        $sans = "var(--olo-font-family, 'Hanken Grotesk', sans-serif)";
        $mono = "var(--olo-font-family-mono, 'Space Mono', ui-monospace, monospace)";

        // ── Colori token-first (neutri blueprint come fallback nei var()) ──
        $bgcol  = $this->safe_color_css( $s['bg_color'] ) ?: 'var(--olo-color-background, #0b0c0f)';
        $txt    = $this->safe_color_css( $s['title_color'] ) ?: 'var(--olo-color-text, #ECEAE3)';
        $acc    = $this->safe_color_css( $s['accent_color'] ) ?: 'var(--olo-color-primary, #C6F24E)';
        $onacc  = 'var(--olo-color-on-primary, #0b0c0f)';
        $eyebrw = $this->safe_color_css( $s['eyebrow_color'] ) ?: $acc;
        $fillc  = $this->safe_color_css( $s['line2_fill_color'] ) ?: $acc;
        $subc   = $this->safe_color_css( $s['subtitle_color'] ) ?: 'var(--olo-color-text-soft, #a0a298)';
        $line   = 'var(--olo-color-border, rgba(236,234,227,.10))';
        $line2  = 'color-mix(in srgb, var(--olo-color-text, #ECEAE3) 20%, transparent)';
        $stroke = $this->safe_color_css( $s['line2_stroke_color'] ) ?: $line2;
        $faint  = 'var(--olo-color-text-faint, #6a6c64)';
        $tsoft  = 'var(--olo-color-text-soft, #a0a298)';
        $ink3   = 'var(--olo-color-muted, #161922)';

        // ── Dimensioni titolo / stroke ──
        $tmin = intval( $s['title_size_min'] );
        if ( $tmin <= 0 ) { $tmin = 74; }
        $tmax = intval( $s['title_size_max'] );
        if ( $tmax <= 0 ) { $tmax = 210; }
        if ( $tmax < $tmin ) { $tmax = $tmin; }
        $sw = floatval( $s['line2_stroke_width'] );
        if ( $sw <= 0 ) { $sw = 1.4; }
        $sw = min( 10, $sw );

        // ── Flag ──
        $has_media = ( $s['media_mode'] !== 'none' );
        $map_on    = ( $s['media_mode'] === 'olomap' );
        $obj_pos   = trim( (string) ( $s['media_object_position'] ?? 'center center' ) );
        if ( $obj_pos === '' ) { $obj_pos = 'center center'; }

        // ── Media hero unificato: pannello media_bg (immagine/video/gradiente/…) con
        // precedenza sull'immagine legacy media_image (tenuta come fallback). ──
        $mb     = $this->bg_media_parts( $s['media_bg'] ?? null, $uid );
        $has_mb = $mb['has'];
        $letters   = ! empty( $s['letters_entrance'] );
        $fill_on   = ! empty( $s['line2_scroll_fill'] ) && (string) $s['title_line2'] !== '';
        $parallax  = ! empty( $s['parallax_internal'] );
        $grid_cols = $has_media ? '1.15fr .85fr' : '1fr';

        // ── Spaziatura (gated): default = padding verticale responsivo del blueprint ──
        $vpad = 'clamp(40px,7vw,84px) 0 clamp(44px,6vw,72px)';
        if ( ! empty( $s['pad_custom'] ) && is_array( $s['content_padding'] ?? null ) ) {
            $cp   = $s['content_padding'];
            $pt   = intval( $cp['top'] ?? 0 );
            $pr   = intval( $cp['right'] ?? 0 );
            $pb   = intval( $cp['bottom'] ?? 0 );
            $pl   = intval( $cp['left'] ?? 0 );
            $vpad = "{$pt}px {$pr}px {$pb}px {$pl}px";
        }

        // ── Riga 1: split lettere (entrata una-a-una via CSS, delay i*55ms) ──
        $line1      = (string) $s['title_line1'];
        $line1_html = '';
        if ( $letters && $line1 !== '' ) {
            $chars = function_exists( 'mb_str_split' ) ? mb_str_split( $line1 ) : str_split( $line1 );
            $i = 0;
            foreach ( $chars as $ch ) {
                if ( trim( $ch ) === '' ) {
                    $line1_html .= esc_html( $ch );
                } else {
                    $line1_html .= '<span class="fx-lt" style="--i:' . $i . '">' . esc_html( $ch ) . '</span>';
                }
                $i++;
            }
        } else {
            $line1_html = esc_html( $line1 );
        }

        // ── Sottotitolo: HTML inline (es. <b>) via wp_kses_post, plain → nl2br ──
        $sub_raw = (string) $s['subtitle'];
        if ( preg_match( '/<[a-z!\/][^>]*>/i', $sub_raw ) ) {
            $sub_html = wp_kses_post( $sub_raw );
        } else {
            $sub_html = nl2br( esc_html( $sub_raw ) );
        }

        // ── OLOmap: parse livelli CSV + token + breadcrumb (config JSON per il runtime) ──
        $map_cfg  = null;
        $map_aria = '';
        if ( $map_on ) {
            $l1 = $this->parse_map_level( $s['map_l1'] );
            if ( empty( $l1 ) ) { $l1 = $this->parse_map_level( $this->defaults['map_l1'] ); }
            $l2 = $this->parse_map_level( $s['map_l2'] );
            if ( empty( $l2 ) ) { $l2 = $this->parse_map_level( $this->defaults['map_l2'] ); }
            $l3 = $this->parse_map_level( $s['map_l3'] );
            if ( empty( $l3 ) ) { $l3 = $this->parse_map_level( $this->defaults['map_l3'] ); }

            $tokens = [];
            foreach ( (array) ( $s['map_tokens'] ?? [] ) as $tk ) {
                if ( ! is_array( $tk ) ) { continue; }
                $lab = isset( $tk['label'] ) ? (string) $tk['label'] : '';
                $col = $this->safe_color_css( $tk['color'] ?? '' );
                if ( $col === '' ) { $col = $acc; }
                $tokens[] = [ 'label' => $lab, 'color' => $col ];
            }

            $dur_s = intval( $s['map_duration'] );
            if ( $dur_s <= 0 ) { $dur_s = 21; }
            if ( $dur_s < 4 ) { $dur_s = 4; }

            $fidx = function ( $lvl ) {
                foreach ( $lvl as $i => $n ) {
                    if ( ! empty( $n['focus'] ) ) { return $i; }
                }
                return 0;
            };
            $nice = function ( $lvl, $idx ) {
                return str_replace( "\n", ' ', $lvl[ $idx ]['label'] );
            };
            $f1 = $nice( $l1, $fidx( $l1 ) );
            $f2 = $nice( $l2, $fidx( $l2 ) );
            $f3 = $nice( $l3, $fidx( $l3 ) );

            $root_label = (string) $s['map_root'];
            if ( $root_label === '' ) { $root_label = 'OLObuild'; }
            $cap_txt = (string) $s['cap_text'];
            $p1 = ( $cap_txt !== '' ) ? $cap_txt : $root_label;
            $p2 = $root_label . ' / ' . $f1;
            $p3 = $p2 . ' / ' . $f2;
            $p4 = $p3 . ' / ' . $f3;

            $map_cfg = [
                'root'   => $root_label,
                'l1'     => $l1,
                'l2'     => $l2,
                'l3'     => $l3,
                'tokens' => $tokens,
                'dur'    => $dur_s * 1000,
                'paths'  => [ $p1, $p2, $p3, $p4 ],
            ];
            $map_lbl  = (string) $s['map_label'];
            $map_aria = ( $map_lbl !== '' ? $map_lbl . ': ' : '' ) . $p4;
        }

        // ── KIT standard: sfondo completo (override del bg base SOLO se valorizzato) ──
        $bg_block = 'background:' . $bgcol . ';';
        $bg_obj   = $s['bg'] ?? null;
        if ( is_array( $bg_obj ) && ! empty( $bg_obj['type'] ) && $bg_obj['type'] !== 'none' && class_exists( 'Olobuild_CSS_Builder' ) ) {
            $bg_decl = ( new Olobuild_CSS_Builder() )->get_bg_inline_css( $bg_obj );
            if ( $bg_decl !== '' ) {
                $bg_block = rtrim( trim( $bg_decl ), ';' ) . ';';
            }
        }

        // ── KIT standard: ombra + bordo (sul contenitore principale .$uid) ──
        $shadow_css        = $this->build_shadow_decl( $s );
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
        $kit_decl          = $border_css;
        if ( $shadow_css !== '' ) {
            $kit_decl .= 'box-shadow:' . $shadow_css . ';';
        }

        $meta = is_array( $s['meta_items'] ?? null ) ? $s['meta_items'] : [];

        ob_start();
        ?>
        <?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above: every colour via the safe_color_css() whitelist or fixed var()/color-mix() literals, sizes via intval()/floatval() with clamps, fixed font-stack literals, background/shadow/border via the Olobuild_CSS_Builder/Olobuild_Tile_Base shared helpers (sanitized internally); $uid is internally generated. ?>
        <style>
            .<?php echo $uid; ?>{position:relative;padding:<?php echo $vpad; ?>;color:<?php echo $txt; ?>;font-family:<?php echo $sans; ?>;line-height:1.55;<?php echo $bg_block; ?><?php echo $kit_decl; ?>}
            .<?php echo $uid; ?> .sth-wrap{max-width:1280px;margin:0 auto;padding-left:clamp(20px,5vw,72px);padding-right:clamp(20px,5vw,72px);}
            .<?php echo $uid; ?> .sth-meta{display:flex;gap:26px;flex-wrap:wrap;border-bottom:1px solid <?php echo $line; ?>;padding-bottom:20px;margin-bottom:clamp(28px,4vw,46px);}
            .<?php echo $uid; ?> .sth-meta span{font-family:<?php echo $mono; ?>;font-size:12px;letter-spacing:.05em;color:<?php echo $faint; ?>;}
            .<?php echo $uid; ?> .sth-meta span b{color:<?php echo $tsoft; ?>;font-weight:400;}
            .<?php echo $uid; ?> .sth-grid{display:grid;grid-template-columns:<?php echo $grid_cols; ?>;gap:clamp(28px,4vw,56px);align-items:end;}
            @media(max-width:880px){.<?php echo $uid; ?> .sth-grid{grid-template-columns:1fr;}}
            .<?php echo $uid; ?> .sth-eyebrow{margin:0;font-family:<?php echo $mono; ?>;font-size:12.5px;letter-spacing:.18em;text-transform:uppercase;color:<?php echo $eyebrw; ?>;}
            .<?php echo $uid; ?> .sth-h{margin:0;font-family:<?php echo $disp; ?>;font-weight:800;line-height:.92;letter-spacing:-.01em;text-transform:uppercase;font-size:clamp(<?php echo $tmin; ?>px,15vw,<?php echo $tmax; ?>px);color:<?php echo $txt; ?>;}
            .<?php echo $uid; ?> .sth-h .fx-lt{display:inline-block;}
            @media(prefers-reduced-motion:no-preference){.<?php echo $uid; ?> .sth-h .fx-lt{animation:<?php echo $uid; ?>-lt .8s cubic-bezier(.16,.8,.26,1) both;animation-delay:calc(var(--i)*55ms);}}
            @keyframes <?php echo $uid; ?>-lt{from{opacity:0;transform:translateY(.32em) rotate(3deg);filter:blur(7px);}to{opacity:1;transform:none;filter:none;}}
            .<?php echo $uid; ?> .sth-l2{display:block;color:<?php echo $bgcol; ?>;-webkit-text-stroke:<?php echo $sw; ?>px <?php echo $stroke; ?>;background:linear-gradient(<?php echo $fillc; ?>,<?php echo $fillc; ?>) bottom/100% var(--fill,0%) no-repeat;-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent;transition:--fill .1s linear;}
            .<?php echo $uid; ?> .sth-sub{margin:26px 0 0;max-width:30ch;font-size:clamp(17px,2vw,21px);line-height:1.5;color:<?php echo $subc; ?>;}
            .<?php echo $uid; ?> .sth-sub b{color:var(--olo-color-text, #ECEAE3);font-weight:600;}
            .<?php echo $uid; ?> .sth-cta{display:flex;gap:14px;margin-top:30px;flex-wrap:wrap;}
            .<?php echo $uid; ?> .sth-btn{display:inline-flex;align-items:center;gap:9px;font-weight:700;font-size:14.5px;padding:13px 22px;border-radius:8px;text-decoration:none;cursor:pointer;transition:transform .14s,background .15s,border-color .15s,color .15s;}
            .<?php echo $uid; ?> .sth-btn svg{width:16px;height:16px;}
            .<?php echo $uid; ?> .sth-btn--fill{background:<?php echo $acc; ?>;color:<?php echo $onacc; ?>;}
            .<?php echo $uid; ?> .sth-btn--fill:hover{background:color-mix(in srgb, <?php echo $acc; ?> 85%, #000);transform:translateY(-2px);}
            .<?php echo $uid; ?> .sth-btn--ghost{border:1px solid <?php echo $line2; ?>;color:<?php echo $txt; ?>;}
            .<?php echo $uid; ?> .sth-btn--ghost:hover{border-color:<?php echo $acc; ?>;color:<?php echo $acc; ?>;}
            .<?php echo $uid; ?> .sth-btn:focus-visible{outline:none;box-shadow:0 0 0 3px color-mix(in srgb, <?php echo $acc; ?> 30%, transparent);}
            .<?php echo $uid; ?> .sth-media{position:relative;perspective:1100px;}
            .<?php echo $uid; ?> .sth-olomap{position:relative;width:100%;height:clamp(320px,42vw,500px);overflow:hidden;border:1px solid <?php echo $line2; ?>;background:radial-gradient(120% 90% at 70% 30%,color-mix(in srgb, <?php echo $acc; ?> 5%, transparent),transparent 60%),radial-gradient(100% 100% at 50% 50%,<?php echo $ink3; ?>,<?php echo $bgcol; ?> 78%);transition:transform .25s cubic-bezier(.2,.7,.3,1);transform-style:preserve-3d;will-change:transform;}
            .<?php echo $uid; ?> .sth-olomap::before{content:"";position:absolute;inset:0;pointer-events:none;z-index:3;background:linear-gradient(color-mix(in srgb, var(--olo-color-text, #ECEAE3) 2.5%, transparent) 1px,transparent 1px) 0 0/100% 26px,linear-gradient(90deg,color-mix(in srgb, var(--olo-color-text, #ECEAE3) 2.5%, transparent) 1px,transparent 1px) 0 0/26px 100%;-webkit-mask:radial-gradient(120% 120% at 50% 50%,#000,transparent 75%);mask:radial-gradient(120% 120% at 50% 50%,#000,transparent 75%);}
            .<?php echo $uid; ?> .sth-olomap::after{content:"";position:absolute;inset:0;pointer-events:none;z-index:4;box-shadow:inset 0 0 60px 8px rgba(8,9,12,.55);}
            .<?php echo $uid; ?> .sth-map-svg{position:absolute;inset:0;width:100%;height:100%;display:block;z-index:2;}
            .<?php echo $uid; ?> .sth-map-lab{position:absolute;left:14px;top:13px;z-index:5;font-family:<?php echo $mono; ?>;font-size:10px;font-weight:700;letter-spacing:.16em;text-transform:uppercase;color:<?php echo $tsoft; ?>;}
            .<?php echo $uid; ?> .sth-map-depth{position:absolute;right:14px;top:13px;z-index:5;display:flex;align-items:center;gap:7px;font-family:<?php echo $mono; ?>;font-size:10px;font-weight:700;letter-spacing:.08em;color:<?php echo $acc; ?>;}
            .<?php echo $uid; ?> .sth-map-depth i{width:6px;height:6px;border-radius:50%;background:<?php echo $acc; ?>;box-shadow:0 0 8px 1px color-mix(in srgb, <?php echo $acc; ?> 60%, transparent);}
            .<?php echo $uid; ?> .sth-olomap .nd circle{fill:<?php echo $ink3; ?>;stroke:<?php echo $line2; ?>;stroke-width:1.4;vector-effect:non-scaling-stroke;}
            .<?php echo $uid; ?> .sth-olomap .nd text{fill:<?php echo $txt; ?>;font-family:<?php echo $disp; ?>;font-weight:700;text-transform:uppercase;text-anchor:middle;dominant-baseline:middle;letter-spacing:-.01em;}
            .<?php echo $uid; ?> .sth-olomap .nd.is-focus circle{stroke:<?php echo $acc; ?>;stroke-width:2.2;vector-effect:non-scaling-stroke;}
            .<?php echo $uid; ?> .sth-olomap .nd.is-root circle{fill:<?php echo $acc; ?>;stroke:none;}
            .<?php echo $uid; ?> .sth-olomap .nd.is-root text{fill:<?php echo $onacc; ?>;}
            .<?php echo $uid; ?> .sth-olomap .lk{stroke:<?php echo $line2; ?>;stroke-width:1.2;fill:none;vector-effect:non-scaling-stroke;}
            .<?php echo $uid; ?> .sth-olomap .lk.is-focus{stroke:<?php echo $acc; ?>;stroke-width:1.6;vector-effect:non-scaling-stroke;}
            .<?php echo $uid; ?> .sth-imgbox{position:relative;width:100%;height:clamp(320px,42vw,500px);overflow:hidden;border:1px solid <?php echo $line2; ?>;background:<?php echo $ink3; ?>;transition:transform .25s cubic-bezier(.2,.7,.3,1);transform-style:preserve-3d;will-change:transform;}
            .<?php echo $uid; ?> .sth-imgbox img{width:100%;height:100%;object-fit:cover;display:block;}
            .<?php echo $uid; ?> .sth-imgbox .sth-imgbox__bg{position:absolute;inset:0;background-size:cover;background-position:center;background-repeat:no-repeat;}
            .<?php echo $uid; ?> .sth-imgbox .sth-imgbox__bg video{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;}
            .<?php echo $uid; ?> .sth-imgbox .sth-ph{position:absolute;inset:0;display:flex;align-items:center;justify-content:center;font-family:<?php echo $mono; ?>;font-size:12px;letter-spacing:.08em;text-transform:uppercase;color:<?php echo $tsoft; ?>;}
            .<?php echo $uid; ?> .sth-cap{position:absolute;left:14px;bottom:14px;font-family:<?php echo $mono; ?>;font-size:11px;letter-spacing:.06em;text-transform:uppercase;color:var(--olo-color-text, #ECEAE3);background:color-mix(in srgb, var(--olo-color-background, #0b0c0f) 60%, transparent);-webkit-backdrop-filter:blur(6px);backdrop-filter:blur(6px);padding:7px 11px;border-radius:6px;border:1px solid <?php echo $line2; ?>;}
            @media(prefers-reduced-motion:reduce){.<?php echo $uid; ?> .sth-olomap{transition:none;}.<?php echo $uid; ?> .sth-imgbox{transition:none;}}
            <?php echo $border_hover_css; ?><?php echo $border_effect_css; ?>
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <section class="olo-studiohero <?php echo esc_attr( $uid ); ?>">
            <div class="sth-wrap">
                <?php if ( ! empty( $s['show_meta'] ) && ! empty( $meta ) ) : ?>
                <div class="sth-meta" data-sth-parallax="0.05">
                    <?php foreach ( $meta as $m ) {
                        if ( ! is_array( $m ) ) { continue; }
                        $m_strong = isset( $m['strong'] ) ? (string) $m['strong'] : '';
                        $m_text   = isset( $m['text'] ) ? (string) $m['text'] : '';
                        if ( $m_strong === '' && $m_text === '' ) { continue; }
                        echo '<span>';
                        if ( $m_strong !== '' ) { echo '<b>' . esc_html( $m_strong ) . '</b>'; }
                        if ( $m_text !== '' ) {
                            if ( $m_strong !== '' ) { echo ' '; }
                            echo esc_html( $m_text );
                        }
                        echo '</span>';
                    } ?>
                </div>
                <?php endif; ?>
                <div class="sth-grid">
                    <div class="sth-copy">
                        <?php if ( (string) $s['eyebrow'] !== '' ) : ?><p class="sth-eyebrow"><?php echo esc_html( $s['eyebrow'] ); ?></p><?php endif; ?>
                        <?php if ( $line1 !== '' || (string) $s['title_line2'] !== '' ) : ?>
                        <h1 class="sth-h" data-olo-wave><?php echo $line1_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built above from esc_html()-escaped characters wrapped in fixed spans ?><?php if ( (string) $s['title_line2'] !== '' ) : ?><span class="sth-l2" data-sth-l2><?php echo esc_html( $s['title_line2'] ); ?></span><?php endif; ?></h1>
                        <?php endif; ?>
                        <?php if ( $sub_raw !== '' ) : ?><p class="sth-sub"><?php echo $sub_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- sanitized above via wp_kses_post() (inline HTML) or nl2br(esc_html()) (plain text) ?></p><?php endif; ?>
                        <?php if ( (string) $s['cta1_text'] !== '' || (string) $s['cta2_text'] !== '' ) : ?>
                        <div class="sth-cta">
                            <?php if ( (string) $s['cta1_text'] !== '' ) : ?><a class="sth-btn sth-btn--fill" data-olo-cta href="<?php echo esc_url( $s['cta1_url'] ?: '#' ); ?>"><?php echo esc_html( $s['cta1_text'] ); ?><?php if ( ! empty( $s['cta1_show_arrow'] ) ) : ?> <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg><?php endif; ?></a><?php endif; ?>
                            <?php if ( (string) $s['cta2_text'] !== '' ) : ?><a class="sth-btn sth-btn--ghost" href="<?php echo esc_url( $s['cta2_url'] ?: '#' ); ?>"><?php echo esc_html( $s['cta2_text'] ); ?></a><?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php if ( $has_media ) : ?>
                    <div class="sth-media" data-sth-parallax="0.08">
                        <?php if ( $map_on ) : ?>
                        <div class="sth-olomap" data-olo-tilt-child role="img" aria-label="<?php echo esc_attr( $map_aria ); ?>">
                            <?php if ( (string) $s['map_label'] !== '' ) : ?><span class="sth-map-lab"><?php echo esc_html( $s['map_label'] ); ?></span><?php endif; ?>
                            <span class="sth-map-depth" data-sth-depth><i></i>L1 / L4</span>
                            <svg class="sth-map-svg" data-sth-map viewBox="0 0 1000 1000" preserveAspectRatio="xMidYMid slice" aria-hidden="true"></svg>
                        </div>
                        <?php else : ?>
                        <div class="sth-imgbox" data-olo-tilt-child>
                            <?php if ( $has_mb ) : ?>
                            <div class="sth-imgbox__bg"<?php echo $mb['css'] !== '' ? ' style="' . esc_attr( $mb['css'] ) . '"' : ''; ?>><?php echo $mb['markup']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- markup generato da Olobuild_CSS_Builder::get_bg_html_markup() (auto-escapato); $mb['css'] passato in esc_attr() ?></div>
                            <?php elseif ( (string) $s['media_image'] !== '' ) : ?>
                            <img src="<?php echo esc_url( $s['media_image'] ); ?>" alt="<?php echo esc_attr( $s['media_label'] ); ?>" style="object-position:<?php echo esc_attr( $obj_pos ); ?>;" />
                            <?php elseif ( (string) $s['media_label'] !== '' ) : ?>
                            <span class="sth-ph"><?php echo esc_html( $s['media_label'] ); ?></span>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                        <?php if ( (string) $s['cap_text'] !== '' ) : ?><span class="sth-cap" data-sth-cap><?php echo esc_html( $s['cap_text'] ); ?></span><?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
        <?php if ( $fill_on || $parallax || $map_on ) : ?>
        <script>
        (function(){
            var root=document.currentScript.previousElementSibling;
            if(!root){return;}
            var reduce=window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            <?php if ( $fill_on ) : ?>
            (function(){
                var l2=root.querySelector('[data-sth-l2]');
                if(!l2){return;}
                var tk=false,lastQ=-1;
                function upd(){
                    tk=false;
                    var r=l2.getBoundingClientRect();
                    var sy=window.scrollY;
                    if(sy===undefined){sy=window.pageYOffset;}
                    if(!sy){sy=0;}
                    var docTop=r.top+sy;
                    var span=Math.max(150,(docTop-80)*0.5);
                    var p=Math.max(0,Math.min(1,sy/span));
                    /* Quantizzato al 2%: background-clip:text su un H1 gigante è un repaint
                       costoso — aggiornarlo a ogni frame fa scattare lo scroll su mobile. */
                    var q=Math.round(p*50)*2;
                    if(q===lastQ){return;}
                    lastQ=q;
                    l2.style.setProperty('--fill',q+'%');
                }
                addEventListener('scroll',function(){ if(!tk){ tk=true; requestAnimationFrame(upd); } },{passive:true});
                addEventListener('resize',upd);
                upd();
            })();
            <?php endif; ?>
            <?php if ( $parallax ) : ?>
            (function(){
                if(reduce){return;}
                var parx=root.querySelectorAll('[data-sth-parallax]');
                if(!parx.length){return;}
                var vh=innerHeight, ticking=false;
                function frame(){
                    ticking=false;
                    for(var i=0;i<parx.length;i++){
                        var el=parx[i], r=el.getBoundingClientRect();
                        if(r.bottom<-200){continue;}
                        if(r.top>vh+200){continue;}
                        var c=(r.top+r.height/2)-vh/2;
                        var sp=parseFloat(el.getAttribute('data-sth-parallax'));
                        if(!sp){sp=0;}
                        el.style.transform='translate3d(0,'+(-c*sp).toFixed(1)+'px,0)';
                    }
                }
                addEventListener('scroll',function(){ if(!ticking){ ticking=true; requestAnimationFrame(frame); } },{passive:true});
                addEventListener('resize',function(){ vh=innerHeight; frame(); });
                frame();
            })();
            <?php endif; ?>
            <?php if ( $map_on && $map_cfg ) : ?>
            (function(){
                var svg=root.querySelector('[data-sth-map]');
                if(!svg){return;}
                var cfg=<?php echo wp_json_encode( $map_cfg, JSON_HEX_TAG | JSON_HEX_AMP ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- inline JSON in <script> built by wp_json_encode() from sanitized labels and safe_color_css()-whitelisted colours ?>;
                var NS='http://www.w3.org/2000/svg';
                var cap=root.querySelector('[data-sth-cap]');
                var depthEl=root.querySelector('[data-sth-depth]');
                function el(t,attrs){ var e=document.createElementNS(NS,t); if(attrs){ for(var k in attrs){ e.setAttribute(k,attrs[k]); } } return e; }
                function pol(c,r,deg){ var a=deg*Math.PI/180; return {x:c.x+r*Math.cos(a), y:c.y+r*Math.sin(a)}; }
                var D=[[-110,-32,38,110,175],[-150,-60,30,120],[-140,-50,40,130],[-135,-45,45,135]];
                function angles(n,li){ var a=D[li]; if(n===a.length){return a;} var out=[],i; for(i=0;i<n;i++){ out.push(a[0]+i*360/n); } return out; }
                function focusIdx(items){ var i; for(i=0;i<items.length;i++){ if(items[i].focus){return i;} } return 0; }
                var C0={x:500,y:500}, R1=300, R2=64, R3=15, R4=3.4;
                var a1=angles(cfg.l1.length,0), a2=angles(cfg.l2.length,1), a3=angles(cfg.l3.length,2), a4=angles(cfg.tokens.length,3);
                var F1=pol(C0,R1,a1[focusIdx(cfg.l1)]);
                var F2=pol(F1,R2,a2[focusIdx(cfg.l2)]);
                var F3=pol(F2,R3,a3[focusIdx(cfg.l3)]);
                var gZoom=el('g',{'class':'zoom'}); svg.appendChild(gZoom);
                function addLink(layer,a,b,focus){
                    var cls='lk'; if(focus){cls='lk is-focus';}
                    layer.appendChild(el('path',{'class':cls,d:'M'+a.x+' '+a.y+'L'+b.x+' '+b.y}));
                }
                function addText(layer,x,y,label,fs){
                    var lines=String(label).split('\n');
                    var t=el('text',{x:x,y:y,'font-size':fs});
                    var start=-(lines.length-1)*0.5*fs;
                    lines.forEach(function(ln,i){
                        var dy=fs; if(i===0){dy=start;}
                        var ts=el('tspan',{x:x,dy:dy}); ts.textContent=ln; t.appendChild(ts);
                    });
                    layer.appendChild(t);
                }
                function addNode(layer,c,r,label,fs,opt){
                    var cls='nd'; if(opt.focus){cls+=' is-focus';} if(opt.root){cls+=' is-root';}
                    var g=el('g',{'class':cls});
                    if(opt.chip){
                        g.appendChild(el('circle',{'class':'chip',cx:c.x,cy:c.y,r:r,fill:opt.chip}));
                        addText(g,c.x,c.y+r*2.5,label,fs);
                    }else{
                        g.appendChild(el('circle',{cx:c.x,cy:c.y,r:r}));
                        addText(g,c.x,c.y,label,fs);
                    }
                    layer.appendChild(g);
                    return g;
                }
                function mkLayer(nat){ var g=el('g',{'class':'layer'}); g._nat=nat; gZoom.appendChild(g); return g; }
                var L1=mkLayer(1), L2=mkLayer(5.15), L3=mkLayer(22), L4=mkLayer(97), L0=mkLayer(0.85);
                cfg.l1.forEach(function(n,i){ var p=pol(C0,R1,a1[i]); addLink(L1,C0,p,n.focus); addNode(L1,p,54,n.label,19,{focus:n.focus}); });
                cfg.l2.forEach(function(n,i){ var p=pol(F1,R2,a2[i]); addLink(L2,F1,p,n.focus); addNode(L2,p,11,n.label,4.0,{focus:n.focus}); });
                cfg.l3.forEach(function(n,i){ var p=pol(F2,R3,a3[i]); addLink(L3,F2,p,n.focus); addNode(L3,p,2.55,n.label,0.98,{focus:n.focus}); });
                cfg.tokens.forEach(function(n,i){ var p=pol(F3,R4,a4[i]); addLink(L4,F3,p,false); addNode(L4,p,0.62,n.label,0.2,{chip:n.color}); });
                addNode(L0,C0,58,cfg.root,16,{root:true});
                var kf=[
                    {t:0.00,c:C0,s:1.00},
                    {t:0.13,c:C0,s:1.07},
                    {t:0.30,c:F1,s:5.15},
                    {t:0.45,c:F1,s:5.45},
                    {t:0.60,c:F2,s:22},
                    {t:0.72,c:F2,s:23.2},
                    {t:0.84,c:F3,s:97},
                    {t:0.90,c:F3,s:100},
                    {t:1.00,c:C0,s:1.00}
                ];
                function smooth(u){ if(u<0){return 0;} if(u>1){return 1;} return u*u*(3-2*u); }
                function camAt(tt){
                    var a=kf[0], b=kf[kf.length-1], i;
                    for(i=0;i<kf.length-1;i++){ if(tt>=kf[i].t){ if(tt<=kf[i+1].t){ a=kf[i]; b=kf[i+1]; break; } } }
                    var den=b.t-a.t; if(!den){den=1;}
                    var u=(tt-a.t)/den, e=smooth(u);
                    var sc=Math.exp(Math.log(a.s)+(Math.log(b.s)-Math.log(a.s))*e);
                    return {x:a.c.x+(b.c.x-a.c.x)*e, y:a.c.y+(b.c.y-a.c.y)*e, s:sc};
                }
                function lvlOp(sc,nat){
                    var lr=Math.log(sc/nat);
                    if(lr<-1.6){return 0;}
                    if(lr>1.75){return 0;}
                    if(lr<-0.55){return (lr+1.6)/1.05;}
                    if(lr<=0.9){return 1;}
                    return 1-(lr-0.9)/0.85;
                }
                var layers=[L0,L1,L2,L3,L4];
                var lastPath='';
                function setReadout(sc){
                    var d;
                    if(sc<2.6){d=1;}else if(sc<11){d=2;}else if(sc<50){d=3;}else{d=4;}
                    var path=cfg.paths[d-1];
                    if(path!==lastPath){
                        lastPath=path;
                        if(cap){cap.textContent=path;}
                        if(depthEl){depthEl.innerHTML='<i></i>L'+d+' / L4';}
                    }
                }
                function applyCam(cam){
                    gZoom.setAttribute('transform','translate('+(500-cam.s*cam.x).toFixed(2)+','+(500-cam.s*cam.y).toFixed(2)+') scale('+cam.s.toFixed(4)+')');
                    var i;
                    for(i=0;i<layers.length;i++){ layers[i].setAttribute('opacity',lvlOp(cam.s,layers[i]._nat).toFixed(3)); }
                    setReadout(cam.s);
                }
                if(reduce){
                    applyCam({x:C0.x,y:C0.y,s:1.0});
                    return;
                }
                var DUR=cfg.dur, t0=performance.now();
                function frame(now){
                    var tt=((now-t0)%DUR)/DUR;
                    applyCam(camAt(tt));
                    requestAnimationFrame(frame);
                }
                requestAnimationFrame(frame);
            })();
            <?php endif; ?>
        })();
        </script>
        <?php endif; ?>
        <?php
        return ob_get_clean();
    }

    /**
     * Parse di un livello OLOmap da CSV: 'Forge,Prisma*,Saffron' →
     * [ {label,focus}, … ]. '*' finale = nodo focus del dive; il letterale
     * '\n' nel label diventa un a-capo nel nodo. Se nessun nodo è marcato,
     * il primo diventa focus (parità col motore Vue).
     */
    private function parse_map_level( $csv ) {
        $out = [];
        foreach ( explode( ',', (string) $csv ) as $part ) {
            $label = trim( $part );
            if ( $label === '' ) { continue; }
            $focus = false;
            if ( substr( $label, -1 ) === '*' ) {
                $focus = true;
                $label = rtrim( substr( $label, 0, -1 ) );
            }
            $label = str_replace( '\n', "\n", $label );
            $out[] = [ 'label' => $label, 'focus' => $focus ];
        }
        if ( ! empty( $out ) ) {
            $has = false;
            foreach ( $out as $n ) {
                if ( $n['focus'] ) { $has = true; break; }
            }
            if ( ! $has ) { $out[0]['focus'] = true; }
        }
        return $out;
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
