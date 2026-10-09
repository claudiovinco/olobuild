<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Tile Before / After — griglia di card "prova": per ogni card il prima e il dopo +
 * didascalia. Estratta dai blueprint OLOthemes (BeforeAfter: cadence).
 * Modalità (`mode`): 'slider' (predefinita dal 9 ott 2026) = le due foto una sopra
 * l'altra con la maniglia da trascinare, lo stesso confronto della tile `imgcompare`
 * (utente: «il vero funzionamento di un prima/dopo»); 'split' = le due foto affiancate,
 * la resa di prima, che resta da scegliere.
 */
class Olobuild_BeforeAfter_Tile extends Olobuild_Tile_Base {

    protected $type     = 'beforeafter';
    protected $name     = 'Before / After';
    protected $icon     = 'dashicons-images-alt2';
    protected $category = 'media';
    protected $defaults = [
        'items' => [
            [ 'before_image' => '', 'after_image' => '', 'before_label' => 'Before', 'after_label' => 'After', 'title' => 'Marcus · 16 weeks', 'text' => 'Down 11kg, first-ever pull-up, and a deadlift PB he never thought he’d hit.' ],
            [ 'before_image' => '', 'after_image' => '', 'before_label' => 'Before', 'after_label' => 'After', 'title' => 'Priya · 6 months', 'text' => 'Built real strength postpartum, pain-free and back to running.' ],
            [ 'before_image' => '', 'after_image' => '', 'before_label' => 'Before', 'after_label' => 'After', 'title' => 'Sam · 1 year', 'text' => 'From couch to first powerlifting meet — and stayed for the community.' ],
        ],
        'columns'            => 3,
        'gap'                => 24,
        'media_bg'           => '',
        'media_aspect'       => '1/1',
        'media_fit'          => 'cover',
        'object_position'    => 'center center',
        'accent'             => '',
        'before_label_color' => 'var(--olo-color-light, #ffffff)',
        'after_label_color'  => 'var(--olo-color-light, #ffffff)',
        'title_color'        => '',
        'text_color'         => '',
        'card_bg'            => '',
        'radius'             => 12,

        // Confronto a slider (come imgcompare): maniglia, linea, orientamento, passaggio automatico.
        'mode'               => 'slider',
        'start_position'     => 50,
        'orientation'        => 'horizontal',
        'handle_color'       => '',
        'handle_size'        => 40,
        'handle_border'      => 3,
        'line_width'         => 3,
        'autoplay'           => false,
        'autoplay_delay'     => 3,
        'autoplay_speed'     => 2,

        // Spaziatura / Forma — additivi e no-op coi default (parità Vue)
        'cap_padding'        => [ 'top' => 16, 'right' => 4, 'bottom' => 4, 'left' => 4 ],
        'card_radius'        => [ 'tl' => 12, 'tr' => 12, 'br' => 12, 'bl' => 12 ],
        'label_radius'       => [ 'tl' => 999, 'tr' => 999, 'br' => 999, 'bl' => 999 ],

        // Kit standard OLObuild — sfondo completo + ombra + bordo (no-op coi default)
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
        $uid = 'oba-' . wp_rand( 10000, 99999 );

        $cols   = max( 1, min( 4, intval( $s['columns'] ) ) );
        $gap    = intval( $s['gap'] ) . 'px';
        $mbg    = $this->safe_color_css( $s['media_bg'] ?? '' ) ?: 'var(--olo-color-surface-alt, #eceff3)';
        $asp    = preg_replace( '/[^0-9\/]/', '', $s['media_aspect'] ?: '1/1' ) ?: '1/1';
        // Le due foto sono background-image, non <img>: l'adattamento scelto
        // nell'inspector si traduce nei valori corrispondenti di `background-size`.
        // 'cover' e' il default ed e' esattamente il valore che era cablato qui.
        $fit_map = [ 'cover' => 'cover', 'contain' => 'contain', 'fill' => '100% 100%', 'none' => 'auto' ];
        $mfit    = $fit_map[ (string) ( $s['media_fit'] ?? 'cover' ) ] ?? 'cover';
        $obj_pos = trim( (string) ( $s['object_position'] ?? 'center center' ) );
        if ( $obj_pos === '' ) { $obj_pos = 'center center'; }
        $accent = $this->safe_color_css( $s['accent'] ) ?: 'var(--olo-color-primary, #e1474f)';
        $blc    = $this->safe_color_css( $s['before_label_color'] ?? '' ) ?: '#ffffff';
        $alc    = $this->safe_color_css( $s['after_label_color'] ?? '' ) ?: '#ffffff';
        $tc     = $this->safe_color_css( $s['title_color'] ?? '' ) ?: 'var(--olo-color-text, #111827)';
        $xc     = $this->safe_color_css( $s['text_color'] ?? '' ) ?: 'var(--olo-color-text-muted, #6b7280)';
        $cbg    = $this->safe_color_css( $s['card_bg'] ?? '' ) ?: 'transparent';
        // Dual-format: numero legacy ("12") E oggetto {tl,tr,br,bl} dal type 'border-radius'.
        $rad    = $this->build_border_radius_css( $s['radius'] ) ?: '0px';
        $serif  = "var(--olo-font-family-heading, 'Playfair Display',Georgia,serif)";
        $sans   = "var(--olo-font-family, 'Inter',-apple-system,sans-serif)";

        $items = is_array( $s['items'] ) ? array_values( $s['items'] ) : [];
        if ( empty( $items ) ) return '';

        // ── Kit standard OLObuild — sfondo completo + ombra + bordo ─────────
        // Sfondo completo (override SOLO se valorizzato → default invariato).
        $bg_obj  = $s['bg'] ?? null;
        $bg_decl = '';
        if ( is_array( $bg_obj ) && ! empty( $bg_obj['type'] ) && $bg_obj['type'] !== 'none' && class_exists( 'Olobuild_CSS_Builder' ) ) {
            $bg_decl = ( new Olobuild_CSS_Builder() )->get_bg_inline_css( $bg_obj );
        }
        // Ombra (preset/custom). '' coi default.
        $shadow_css = $this->build_shadow_decl( $s );
        // Bordo (base + hover + effetti). '' coi default.
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );

        // Dichiarazioni extra da appendere alla regola del contenitore .$uid.
        // position:relative serve agli effetti bordo (come particlefx).
        $kit_decl = '';
        if ( $bg_decl !== '' )    { $kit_decl .= rtrim( $bg_decl, ';' ) . ';'; }
        if ( $shadow_css !== '' ) { $kit_decl .= "box-shadow:{$shadow_css};"; }
        if ( $border_css !== '' ) { $kit_decl .= $border_css; }
        if ( $border_effect_css !== '' ) { $kit_decl .= 'position:relative;'; }

        // ── Override additivi gated (no-op coi default → byte-identici) ──
        // Padding didascalia: base "16px 4px 4px". Se cap_padding è stato
        // modificato rispetto al default {16,4,4,4}, sovrascrive i 4 valori.
        $cap_pad_css = '16px 4px 4px';
        $cpv = is_array( $s['cap_padding'] ?? null ) ? $s['cap_padding'] : [];
        $cp_t = max( 0, intval( $cpv['top']    ?? 16 ) );
        $cp_r = max( 0, intval( $cpv['right']  ?? 4 ) );
        $cp_b = max( 0, intval( $cpv['bottom'] ?? 4 ) );
        $cp_l = max( 0, intval( $cpv['left']   ?? 4 ) );
        $cp_default = ( $cp_t === 16 ) ? true : false;
        if ( $cp_default ) { if ( $cp_r !== 4 ) { $cp_default = false; } }
        if ( $cp_default ) { if ( $cp_b !== 4 ) { $cp_default = false; } }
        if ( $cp_default ) { if ( $cp_l !== 4 ) { $cp_default = false; } }
        if ( ! $cp_default ) {
            $cap_pad_css = "{$cp_t}px {$cp_r}px {$cp_b}px {$cp_l}px";
        }

        // Raggio card: base $rad (es. "12px"). Se card_radius è stato modificato
        // rispetto al default {12,12,12,12}, sovrascrive con i 4 angoli.
        $card_rad_css = $rad;
        $crv = is_array( $s['card_radius'] ?? null ) ? $s['card_radius'] : [];
        $cr_tl = intval( $crv['tl'] ?? 12 );
        $cr_tr = intval( $crv['tr'] ?? 12 );
        $cr_br = intval( $crv['br'] ?? 12 );
        $cr_bl = intval( $crv['bl'] ?? 12 );
        $cr_default = ( $cr_tl === 12 ) ? true : false;
        if ( $cr_default ) { if ( $cr_tr !== 12 ) { $cr_default = false; } }
        if ( $cr_default ) { if ( $cr_br !== 12 ) { $cr_default = false; } }
        if ( $cr_default ) { if ( $cr_bl !== 12 ) { $cr_default = false; } }
        if ( ! $cr_default ) {
            $built = $this->build_border_radius_css( $crv );
            if ( $built !== '' ) { $card_rad_css = $built; }
        }

        // Raggio etichette (pill): base "999px". Se label_radius è stato
        // modificato rispetto al default {999,999,999,999}, sovrascrive.
        $lab_rad_css = '999px';
        $lrv = is_array( $s['label_radius'] ?? null ) ? $s['label_radius'] : [];
        $lr_tl = intval( $lrv['tl'] ?? 999 );
        $lr_tr = intval( $lrv['tr'] ?? 999 );
        $lr_br = intval( $lrv['br'] ?? 999 );
        $lr_bl = intval( $lrv['bl'] ?? 999 );
        $lr_default = ( $lr_tl === 999 ) ? true : false;
        if ( $lr_default ) { if ( $lr_tr !== 999 ) { $lr_default = false; } }
        if ( $lr_default ) { if ( $lr_br !== 999 ) { $lr_default = false; } }
        if ( $lr_default ) { if ( $lr_bl !== 999 ) { $lr_default = false; } }
        if ( ! $lr_default ) {
            $lab_rad_css = "{$lr_tl}px {$lr_tr}px {$lr_br}px {$lr_bl}px";
        }

        // ── Confronto a slider (mode 'slider', il predefinito) ──
        // Le due foto stanno nella STESSA cornice, una sopra l'altra: il «Prima» è ritagliato
        // con clip-path fino alla maniglia. La posizione vive in una variabile CSS della card
        // (--oba-pos), così lo script cambia un solo valore e ritaglio, linea e maniglia seguono.
        $slider   = ( $s['mode'] ?? 'slider' ) !== 'split';
        $vert     = ( $s['orientation'] ?? 'horizontal' ) === 'vertical';
        $start    = max( 0, min( 100, intval( $s['start_position'] ?? 50 ) ) );
        $hc       = $this->safe_color_css( $s['handle_color'] ?? '' ) ?: 'var(--olo-color-light, #ffffff)';
        $hsz      = max( 24, min( 72, intval( $s['handle_size'] ?? 40 ) ?: 40 ) );
        $hbw      = max( 0, min( 8, intval( $s['handle_border'] ?? 3 ) ) );
        $lw       = max( 1, min( 8, intval( $s['line_width'] ?? 3 ) ?: 3 ) );
        $autoplay = ! empty( $s['autoplay'] );
        $ap_delay = max( 1, min( 15, intval( $s['autoplay_delay'] ?? 3 ) ) );
        $ap_speed = max( 1, min( 10, intval( $s['autoplay_speed'] ?? 2 ) ) );
        $orient   = $vert ? 'vertical' : 'horizontal';

        ob_start();
        ?>
        <?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above: every colour via the safe_color_css() whitelist (with fixed var() fallbacks), columns/gap/radii/padding via intval() clamps, aspect ratio via preg_replace() character whitelist, background-size picked from a fixed map, fixed font-stack literals, kit decorations via the Olobuild_CSS_Builder/Olobuild_Tile_Base shared helpers (sanitized internally); $uid is internally generated. ?>
        <style>
            <?php echo Olobuild_CSS_Builder::pattern_layer_css( $bg_decl, '.' . $uid ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- regole fisse di pattern_layer_css(): il selettore è l'uid della tile, i valori sono variabili CSS ?>
            .<?php echo $uid; ?>{font-family:<?php echo $sans; ?>;display:grid;grid-template-columns:repeat(<?php echo $cols; ?>,1fr);gap:<?php echo $gap; ?>;<?php echo $kit_decl; ?>}
            .<?php echo $uid; ?> .oba-card{background:<?php echo $cbg; ?>;border-radius:<?php echo $card_rad_css; ?>;overflow:hidden;}
            .<?php echo $uid; ?> .oba-pair{position:relative;display:grid;grid-template-columns:1fr 1fr;gap:2px;}
            <?php // `background:` e' la shorthand: riazzera background-repeat al valore iniziale `repeat`. Con 'cover' non si notava (la foto riempie comunque), ma «Contieni» e «Dimensione originale» disegnerebbero la stessa foto a mosaico. Va DOPO la shorthand, altrimenti la shorthand se lo rimangia. ?>
            .<?php echo $uid; ?> .oba-media{position:relative;aspect-ratio:<?php echo $asp; ?>;background:<?php echo $mbg; ?>;background-size:<?php echo $mfit; ?>;background-repeat:no-repeat;background-position:<?php echo esc_attr( $obj_pos ); ?>;}
            .<?php echo $uid; ?> .oba-lab{position:absolute;top:10px;font-size:10.5px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;padding:4px 10px;border-radius:<?php echo $lab_rad_css; ?>;}
            .<?php echo $uid; ?> .oba-lab--b{left:10px;background:rgba(0,0,0,.55);color:<?php echo $blc; ?>;}
            .<?php echo $uid; ?> .oba-lab--a{right:10px;background:<?php echo $accent; ?>;color:<?php echo $alc; ?>;}
            .<?php echo $uid; ?> .oba-cap{padding:<?php echo $cap_pad_css; ?>;}
            .<?php echo $uid; ?> .oba-t{font-family:<?php echo $serif; ?>;font-size:19px;line-height:1.25;margin:0;color:<?php echo $tc; ?>;}
            .<?php echo $uid; ?> .oba-x{font-size:14px;line-height:1.55;margin:8px 0 0;color:<?php echo $xc; ?>;}
            <?php if ( $slider ) : ?>
            <?php // pan-y: sul telefono il dito che scorre in verticale muove ancora la pagina; solo il gesto orizzontale sposta la maniglia (in verticale il contrario). ?>
            .<?php echo $uid; ?> .oba-cmp{--oba-pos:50%;position:relative;aspect-ratio:<?php echo $asp; ?>;overflow:hidden;background:<?php echo $mbg; ?>;cursor:col-resize;user-select:none;-webkit-user-select:none;touch-action:pan-y;}
            .<?php echo $uid; ?> .oba-cmp[data-orientation="vertical"]{cursor:row-resize;touch-action:pan-x;}
            .<?php echo $uid; ?> .oba-cmp .oba-media{position:absolute;inset:0;aspect-ratio:auto;pointer-events:none;}
            .<?php echo $uid; ?> .oba-cmp .oba-media--b{clip-path:inset(0 calc(100% - var(--oba-pos)) 0 0);}
            .<?php echo $uid; ?> .oba-cmp[data-orientation="vertical"] .oba-media--b{clip-path:inset(0 0 calc(100% - var(--oba-pos)) 0);}
            .<?php echo $uid; ?> .oba-line{position:absolute;z-index:2;top:0;bottom:0;left:var(--oba-pos);width:<?php echo $lw; ?>px;transform:translateX(-50%);background:<?php echo $hc; ?>;pointer-events:none;}
            .<?php echo $uid; ?> .oba-cmp[data-orientation="vertical"] .oba-line{top:var(--oba-pos);bottom:auto;left:0;right:0;width:auto;height:<?php echo $lw; ?>px;transform:translateY(-50%);}
            .<?php echo $uid; ?> .oba-handle{position:absolute;z-index:3;top:50%;left:var(--oba-pos);width:<?php echo $hsz; ?>px;height:<?php echo $hsz; ?>px;box-sizing:border-box;transform:translate(-50%,-50%);border-radius:50%;border:<?php echo $hbw; ?>px solid <?php echo $hc; ?>;background:rgba(0,0,0,.3);-webkit-backdrop-filter:blur(4px);backdrop-filter:blur(4px);display:flex;align-items:center;justify-content:center;pointer-events:none;transition:transform .2s ease;}
            .<?php echo $uid; ?> .oba-cmp[data-orientation="vertical"] .oba-handle{top:var(--oba-pos);left:50%;}
            .<?php echo $uid; ?> .oba-cmp.is-drag .oba-handle{transform:translate(-50%,-50%) scale(1.1);}
            .<?php echo $uid; ?> .oba-handle svg{width:<?php echo (int) round( $hsz * 0.45 ); ?>px;height:<?php echo (int) round( $hsz * 0.45 ); ?>px;fill:none;stroke:<?php echo $hc; ?>;stroke-width:2.5;stroke-linecap:round;stroke-linejoin:round;}
            .<?php echo $uid; ?> .oba-cmp .oba-lab{z-index:2;pointer-events:none;}
            .<?php echo $uid; ?> .oba-cmp[data-orientation="vertical"] .oba-lab--a{top:auto;bottom:10px;}
            <?php // Il range resta per tastiera e lettori di schermo: invisibile e senza puntatore, il trascinamento lo fa lo script sulla cornice. ?>
            .<?php echo $uid; ?> .oba-range{position:absolute;inset:0;z-index:4;width:100%;height:100%;margin:0;padding:0;opacity:0;pointer-events:none;}
            .<?php echo $uid; ?> .oba-cmp:focus-within .oba-handle{outline:3px solid var(--olo-color-primary, #e1474f);outline-offset:3px;}
            <?php endif; ?>
            @media (max-width:780px){.<?php echo $uid; ?>{grid-template-columns:1fr;}}
            <?php echo $this->css_per_dispositivo( $s, 'columns', '.' . $uid, $this->decl_colonne( 4 ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- colonne per dispositivo (interi limitati) ?>
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <div class="olo-beforeafter <?php echo esc_attr( $uid ); ?>">
            <?php foreach ( $items as $it ) :
                $bimg = isset( $it['before_image'] ) ? trim( $it['before_image'] ) : '';
                $aimg = isset( $it['after_image'] ) ? trim( $it['after_image'] ) : '';
                $bsty = $bimg !== '' ? ' style="background-image:url(' . esc_url( $bimg ) . ')"' : '';
                $asty = $aimg !== '' ? ' style="background-image:url(' . esc_url( $aimg ) . ')"' : '';
                $it_title = ! empty( $it['title'] ) ? trim( $it['title'] ) : '';
                $b_lab    = ! empty( $it['before_label'] ) ? trim( $it['before_label'] ) : olobuild_t( 'Prima' );
                $a_lab    = ! empty( $it['after_label'] ) ? trim( $it['after_label'] ) : olobuild_t( 'Dopo' );
                $b_aria   = $it_title !== '' ? $b_lab . ' – ' . $it_title : $b_lab;
                $a_aria   = $it_title !== '' ? $a_lab . ' – ' . $it_title : $a_lab;
            ?>
                <div class="oba-card">
                    <?php if ( $slider ) :
                        $cmp_aria = $it_title !== '' ? olobuild_t( 'Confronto immagini' ) . ' – ' . $it_title : olobuild_t( 'Confronto immagini' );
                    ?>
                    <div class="oba-cmp" data-orientation="<?php echo esc_attr( $orient ); ?>" data-olo-own-drag style="--oba-pos:<?php echo (int) $start; ?>%">
                        <div class="oba-media oba-media--a" role="img" aria-label="<?php echo esc_attr( $a_aria ); ?>"<?php echo $asty; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- style attribute assembled above from fixed literals + esc_url()'d image ?>></div>
                        <div class="oba-media oba-media--b" role="img" aria-label="<?php echo esc_attr( $b_aria ); ?>"<?php echo $bsty; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- style attribute assembled above from fixed literals + esc_url()'d image ?>></div>
                        <div class="oba-line" aria-hidden="true"></div>
                        <div class="oba-handle" aria-hidden="true"><?php if ( $vert ) : ?><svg viewBox="0 0 24 24"><polyline points="6 9 12 3 18 9"/><polyline points="6 15 12 21 18 15"/></svg><?php else : ?><svg viewBox="0 0 24 24"><polyline points="9 6 3 12 9 18"/><polyline points="15 6 21 12 15 18"/></svg><?php endif; ?></div>
                        <?php if ( ! empty( $it['before_label'] ) ) : ?><span class="oba-lab oba-lab--b" aria-hidden="true"><?php echo esc_html( $it['before_label'] ); ?></span><?php endif; ?>
                        <?php if ( ! empty( $it['after_label'] ) ) : ?><span class="oba-lab oba-lab--a" aria-hidden="true"><?php echo esc_html( $it['after_label'] ); ?></span><?php endif; ?>
                        <input type="range" class="oba-range" min="0" max="100" step="1" value="<?php echo (int) $start; ?>" aria-label="<?php echo esc_attr( $cmp_aria ); ?>"<?php echo $vert ? ' aria-orientation="vertical"' : ''; ?> />
                    </div>
                    <?php else : ?>
                    <div class="oba-pair">
                        <div class="oba-media" role="img" aria-label="<?php echo esc_attr( $b_aria ); ?>"<?php echo $bsty; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- style attribute assembled above from fixed literals + esc_url()'d image ?>><?php if ( ! empty( $it['before_label'] ) ) : ?><span class="oba-lab oba-lab--b" aria-hidden="true"><?php echo esc_html( $it['before_label'] ); ?></span><?php endif; ?></div>
                        <div class="oba-media" role="img" aria-label="<?php echo esc_attr( $a_aria ); ?>"<?php echo $asty; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- style attribute assembled above from fixed literals + esc_url()'d image ?>><?php if ( ! empty( $it['after_label'] ) ) : ?><span class="oba-lab oba-lab--a" aria-hidden="true"><?php echo esc_html( $it['after_label'] ); ?></span><?php endif; ?></div>
                    </div>
                    <?php endif; ?>
                    <?php if ( ! empty( $it['title'] ) || ! empty( $it['text'] ) ) : ?>
                        <div class="oba-cap">
                            <?php if ( ! empty( $it['title'] ) ) : ?><h3 class="oba-t"><?php echo esc_html( $it['title'] ); ?></h3><?php endif; ?>
                            <?php if ( ! empty( $it['text'] ) ) : ?><p class="oba-x"><?php echo esc_html( $it['text'] ); ?></p><?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
        <?php if ( $slider ) : ?>
        <?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline JS below only interpolates the internally generated $uid, a fixed true/false literal and intval()/max()/min()-clamped integers. ?>
        <script>
        (function(){
            var root = document.querySelector('.<?php echo $uid; ?>');
            if (!root) return;
            var ridotto = window.matchMedia ? window.matchMedia('(prefers-reduced-motion: reduce)').matches : false;
            var AUTO = <?php echo $autoplay ? 'true' : 'false'; ?>, ATTESA = <?php echo (int) $ap_delay; ?> * 1000, DURATA = <?php echo (int) $ap_speed; ?> * 1000;
            [].forEach.call(root.querySelectorAll('.oba-cmp'), function(cmp){
                var vert = cmp.getAttribute('data-orientation') === 'vertical';
                var range = cmp.querySelector('.oba-range');
                var pos = parseFloat(range.value);
                if (isNaN(pos)) pos = 50;
                var timer = null, anim = null, verso = 1, trascina = false;
                function metti(p) {
                    p = Math.max(0, Math.min(100, p));
                    pos = p;
                    cmp.style.setProperty('--oba-pos', p + '%');
                    range.value = Math.round(p);
                }
                function ferma() {
                    if (anim) { cancelAnimationFrame(anim); anim = null; }
                    if (timer) { clearTimeout(timer); timer = null; }
                }
                // Passaggio automatico: va e viene fra il 5% e il 95%, la durata in proporzione al tratto.
                function giro() {
                    // Nel canvas la tile si rifà a ogni modifica: la copia staccata dalla pagina si ferma.
                    if (!cmp.isConnected) return;
                    var da = pos, a = verso > 0 ? 95 : 5, t0 = null;
                    var dur = Math.max(300, Math.abs(a - da) / 90 * DURATA);
                    function passo(ts) {
                        if (!cmp.isConnected) { anim = null; return; }
                        if (t0 === null) t0 = ts;
                        var k = Math.min(1, (ts - t0) / dur);
                        var e = k < 0.5 ? 2 * k * k : 1 - Math.pow(2 - 2 * k, 2) / 2;
                        metti(da + (a - da) * e);
                        if (k < 1) { anim = requestAnimationFrame(passo); return; }
                        anim = null; verso = -verso; timer = setTimeout(giro, 400);
                    }
                    anim = requestAnimationFrame(passo);
                }
                function riparti() {
                    ferma();
                    if (AUTO) { if (!ridotto) timer = setTimeout(giro, ATTESA); }
                }
                function daPuntatore(e) {
                    var r = cmp.getBoundingClientRect();
                    metti(vert ? (e.clientY - r.top) / r.height * 100 : (e.clientX - r.left) / r.width * 100);
                }
                cmp.addEventListener('pointerdown', function(e) {
                    if (e.button > 0) return;
                    trascina = true; ferma(); cmp.classList.add('is-drag');
                    try { cmp.setPointerCapture(e.pointerId); } catch (x) {}
                    daPuntatore(e);
                });
                cmp.addEventListener('pointermove', function(e) { if (trascina) daPuntatore(e); });
                function fine() { if (!trascina) return; trascina = false; cmp.classList.remove('is-drag'); riparti(); }
                cmp.addEventListener('pointerup', fine);
                cmp.addEventListener('pointercancel', fine);
                cmp.addEventListener('lostpointercapture', fine);
                range.addEventListener('input', function() { ferma(); metti(parseFloat(range.value)); riparti(); });
                // In verticale la freccia giù abbassa la maniglia (il range nativo, orizzontale, farebbe il contrario).
                if (vert) range.addEventListener('keydown', function(e) {
                    var d = e.key === 'ArrowDown' ? 2 : (e.key === 'ArrowUp' ? -2 : 0);
                    if (!d) return;
                    e.preventDefault(); ferma(); metti(pos + d); riparti();
                });
                riparti();
            });
        })();
        </script>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <?php endif; ?>
        <?php
        // Bordo hover + effetti (neon/gradiente…) — vuoti coi default.
        if ( $border_hover_css !== '' || $border_effect_css !== '' ) {
            echo '<style>' . $border_hover_css . $border_effect_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_hover_css()/build_border_effect_css() from sanitized settings
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
