<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Tile Before / After — griglia di card "prova": coppia media affiancati con
 * etichette Prima/Dopo + didascalia. Estratta dai blueprint OLOthemes (BeforeAfter:
 * cadence). Statica (nessun JS). Render == Vue (BeforeAfterTile.vue).
 */
class Olo_BeforeAfter_Tile extends Olo_Tile_Base {

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
        'object_position'    => 'center center',
        'accent'             => '',
        'before_label_color' => '#ffffff',
        'after_label_color'  => '#ffffff',
        'title_color'        => '',
        'text_color'         => '',
        'card_bg'            => '',
        'radius'             => 12,

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
        if ( is_array( $bg_obj ) && ! empty( $bg_obj['type'] ) && $bg_obj['type'] !== 'none' && class_exists( 'Olo_CSS_Builder' ) ) {
            $bg_decl = ( new Olo_CSS_Builder() )->get_bg_inline_css( $bg_obj );
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

        ob_start();
        ?>
        <?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above: every colour via the safe_color_css() whitelist (with fixed var() fallbacks), columns/gap/radii/padding via intval() clamps, aspect ratio via preg_replace() character whitelist, fixed font-stack literals, kit decorations via the Olo_CSS_Builder/Olo_Tile_Base shared helpers (sanitized internally); $uid is internally generated. ?>
        <style>
            .<?php echo $uid; ?>{font-family:<?php echo $sans; ?>;display:grid;grid-template-columns:repeat(<?php echo $cols; ?>,1fr);gap:<?php echo $gap; ?>;<?php echo $kit_decl; ?>}
            .<?php echo $uid; ?> .oba-card{background:<?php echo $cbg; ?>;border-radius:<?php echo $card_rad_css; ?>;overflow:hidden;}
            .<?php echo $uid; ?> .oba-pair{position:relative;display:grid;grid-template-columns:1fr 1fr;gap:2px;}
            .<?php echo $uid; ?> .oba-media{position:relative;aspect-ratio:<?php echo $asp; ?>;background:<?php echo $mbg; ?>;background-size:cover;background-position:<?php echo esc_attr( $obj_pos ); ?>;}
            .<?php echo $uid; ?> .oba-lab{position:absolute;top:10px;font-size:10.5px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;padding:4px 10px;border-radius:<?php echo $lab_rad_css; ?>;}
            .<?php echo $uid; ?> .oba-lab--b{left:10px;background:rgba(0,0,0,.55);color:<?php echo $blc; ?>;}
            .<?php echo $uid; ?> .oba-lab--a{right:10px;background:<?php echo $accent; ?>;color:<?php echo $alc; ?>;}
            .<?php echo $uid; ?> .oba-cap{padding:<?php echo $cap_pad_css; ?>;}
            .<?php echo $uid; ?> .oba-t{font-family:<?php echo $serif; ?>;font-size:19px;line-height:1.25;margin:0;color:<?php echo $tc; ?>;}
            .<?php echo $uid; ?> .oba-x{font-size:14px;line-height:1.55;margin:8px 0 0;color:<?php echo $xc; ?>;}
            @media (max-width:780px){.<?php echo $uid; ?>{grid-template-columns:1fr;}}
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <div class="olo-beforeafter <?php echo esc_attr( $uid ); ?>">
            <?php foreach ( $items as $it ) :
                $bimg = isset( $it['before_image'] ) ? trim( $it['before_image'] ) : '';
                $aimg = isset( $it['after_image'] ) ? trim( $it['after_image'] ) : '';
                $bsty = $bimg !== '' ? ' style="background-image:url(' . esc_url( $bimg ) . ')"' : '';
                $asty = $aimg !== '' ? ' style="background-image:url(' . esc_url( $aimg ) . ')"' : '';
                $it_title = ! empty( $it['title'] ) ? trim( $it['title'] ) : '';
                $b_lab    = ! empty( $it['before_label'] ) ? trim( $it['before_label'] ) : olo_t( 'Prima', 'olobuilder' );
                $a_lab    = ! empty( $it['after_label'] ) ? trim( $it['after_label'] ) : olo_t( 'Dopo', 'olobuilder' );
                $b_aria   = $it_title !== '' ? $b_lab . ' – ' . $it_title : $b_lab;
                $a_aria   = $it_title !== '' ? $a_lab . ' – ' . $it_title : $a_lab;
            ?>
                <div class="oba-card">
                    <div class="oba-pair">
                        <div class="oba-media" role="img" aria-label="<?php echo esc_attr( $b_aria ); ?>"<?php echo $bsty; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- style attribute assembled above from fixed literals + esc_url()'d image ?>><?php if ( ! empty( $it['before_label'] ) ) : ?><span class="oba-lab oba-lab--b" aria-hidden="true"><?php echo esc_html( $it['before_label'] ); ?></span><?php endif; ?></div>
                        <div class="oba-media" role="img" aria-label="<?php echo esc_attr( $a_aria ); ?>"<?php echo $asty; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- style attribute assembled above from fixed literals + esc_url()'d image ?>><?php if ( ! empty( $it['after_label'] ) ) : ?><span class="oba-lab oba-lab--a" aria-hidden="true"><?php echo esc_html( $it['after_label'] ); ?></span><?php endif; ?></div>
                    </div>
                    <?php if ( ! empty( $it['title'] ) || ! empty( $it['text'] ) ) : ?>
                        <div class="oba-cap">
                            <?php if ( ! empty( $it['title'] ) ) : ?><h3 class="oba-t"><?php echo esc_html( $it['title'] ); ?></h3><?php endif; ?>
                            <?php if ( ! empty( $it['text'] ) ) : ?><p class="oba-x"><?php echo esc_html( $it['text'] ); ?></p><?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
        // Bordo hover + effetti (neon/gradiente…) — vuoti coi default.
        if ( $border_hover_css !== '' || $border_effect_css !== '' ) {
            echo '<style>' . $border_hover_css . $border_effect_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olo_Tile_Base::build_border_hover_css()/build_border_effect_css() from sanitized settings
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
