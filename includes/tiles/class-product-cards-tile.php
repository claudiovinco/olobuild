<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Product Cards — griglia di card prodotto stile editoriale (split top/bottom).
 * Allineato agli standard Olobuild:
 *   - border-radius 4 angoli + hover via withHover
 *   - background creativo unificato (Olo_CSS_Builder::get_bg_inline_css) per
 *     card e per la metà superiore di ogni item
 *   - rich text per la descrizione (safe_richtext_content)
 *   - inline editing su tutti i testi (data-olo-editable)
 */
class Olo_ProductCards_Tile extends Olo_Tile_Base {

    protected $type     = 'product-cards';
    protected $name     = 'Product Cards';
    protected $icon     = 'dashicons-portfolio';
    protected $category = 'layout';
    protected $defaults = [
        'columns' => 5,
        'gap'     => 24,

        'items' => [
            [ 'letter' => 'C', 'letter_color' => '#b3261e', 'top_bg' => [ 'type' => 'gradient', 'gradient_from' => '#fdf2f2', 'gradient_to' => '#fbe1e1', 'gradient_angle' => 180 ], 'screenshot_label' => 'SCREENSHOT · EDITOR LIVE', 'brand_label' => 'OLOBUILD',   'brand_color' => '#b3261e', 'show_badge' => true,  'badge_text' => 'GRATIS', 'badge_bg' => '#0f172a', 'badge_color' => '#ffffff', 'title' => 'Co',  'title_accent' => 'struisci', 'title_accent_italic' => true,  'description' => 'Page builder olonico. 187 tile, motion design, border-radius animato all\'hover. Alla pari dei top builder commerciali.', 'cta_text' => 'SCOPRI OLOBUILD',   'cta_url' => '#' ],
            [ 'letter' => 'T', 'letter_color' => '#c2185b', 'top_bg' => [ 'type' => 'gradient', 'gradient_from' => '#fce4ec', 'gradient_to' => '#f8bbd0', 'gradient_angle' => 180 ], 'screenshot_label' => 'SCREENSHOT · MULTILINGUA', 'brand_label' => 'OLOLANG',    'brand_color' => '#c2185b', 'show_badge' => false, 'badge_text' => '',       'badge_bg' => '#0f172a', 'badge_color' => '#ffffff', 'title' => 'Tra', 'title_accent' => 'duci',     'title_accent_italic' => true,  'description' => 'Multilingua nativo. Traduzioni IA + editing umano. SEO per ogni lingua. 28 idiomi inclusi.',                                'cta_text' => 'SCOPRI OLOLANG',    'cta_url' => '#' ],
            [ 'letter' => 'P', 'letter_color' => '#1976d2', 'top_bg' => [ 'type' => 'gradient', 'gradient_from' => '#e3f2fd', 'gradient_to' => '#bbdefb', 'gradient_angle' => 180 ], 'screenshot_label' => 'SCREENSHOT · CALENDARIO', 'brand_label' => 'OLOBOOKING', 'brand_color' => '#1976d2', 'show_badge' => false, 'badge_text' => '',       'badge_bg' => '#0f172a', 'badge_color' => '#ffffff', 'title' => 'Pre', 'title_accent' => 'nota',     'title_accent_italic' => true,  'description' => 'Motore di prenotazione multi-verticale. Strutture, appuntamenti, eventi, immobili, noleggi, ristoranti.',                   'cta_text' => 'SCOPRI OLOBOOKING', 'cta_url' => '#' ],
            [ 'letter' => 'M', 'letter_color' => '#e65100', 'top_bg' => [ 'type' => 'gradient', 'gradient_from' => '#fff3e0', 'gradient_to' => '#ffe0b2', 'gradient_angle' => 180 ], 'screenshot_label' => 'SCREENSHOT · TOUR 360°', 'brand_label' => 'OLOTOUR',    'brand_color' => '#e65100', 'show_badge' => false, 'badge_text' => '',       'badge_bg' => '#0f172a', 'badge_color' => '#ffffff', 'title' => 'Mo',  'title_accent' => 'stra',     'title_accent_italic' => true,  'description' => 'Percorsi guidati a 360°. Foto e video sferici con hot-spot interattivi, multi-stanza, supporto VR.',                       'cta_text' => 'SCOPRI OLOTOUR',    'cta_url' => '#' ],
            [ 'letter' => 'I', 'letter_color' => '#2e7d32', 'top_bg' => [ 'type' => 'gradient', 'gradient_from' => '#e8f5e9', 'gradient_to' => '#c8e6c9', 'gradient_angle' => 180 ], 'screenshot_label' => 'SCREENSHOT · AREA CORSI', 'brand_label' => 'OLOTUTOR',   'brand_color' => '#2e7d32', 'show_badge' => false, 'badge_text' => '',       'badge_bg' => '#0f172a', 'badge_color' => '#ffffff', 'title' => 'In',  'title_accent' => 'segna',    'title_accent_italic' => true,  'description' => 'E-learning completo. Corsi, lezioni, esercizi, certificazioni, area allievi. WordPress-native.',                          'cta_text' => 'SCOPRI OLOTUTOR',   'cta_url' => '#' ],
        ],

        'card_bg'                    => [ 'type' => 'solid', 'color' => '#ffffff' ],
        'card_color'                 => '',
        'card_radius'                => [ 'tl' => 24, 'tr' => 24, 'br' => 24, 'bl' => 24, 'linked' => true ],
        'card_radius_hover'          => [ 'tl' => 24, 'tr' => 24, 'br' => 24, 'bl' => 24, 'linked' => true ],
        'card_radius_hover_duration' => 400,
        'card_shadow'                => 'sm',
        'card_padding'               => 28,

        'top_aspect_ratio'      => '3/4',
        'top_padding'           => 24,
        'letter_font_family'    => 'serif',
        'letter_size'           => 140,
        'letter_italic'         => true,
        'letter_align'          => 'center',
        'logo_height'           => 52,
        'show_screenshot_label' => true,
        'screenshot_label_color' => '',

        'brand_size'           => 13,
        'brand_letter_spacing' => 0.08,
        'title_font_family'    => 'serif',
        'title_size'           => 30,
        'title_weight'         => '500',
        'description_size'     => 15,
        'cta_size'             => 12,
        'cta_arrow'            => true,

        'card_hover_effect'    => 'lift',
    ];

    public function get_controls() { return []; }

    private function _radius_hover_diff( $base, $hover ) {
        if ( ! is_array( $base ) || ! is_array( $hover ) ) return '';
        foreach ( [ 'tl', 'tr', 'br', 'bl' ] as $c ) {
            if ( intval( $base[ $c ] ?? 0 ) !== intval( $hover[ $c ] ?? 0 ) ) {
                return $this->build_border_radius_css( $hover );
            }
        }
        return '';
    }

    private function _bg_inline_css( $bg ) {
        if ( ! is_array( $bg ) || ( $bg['type'] ?? 'none' ) === 'none' ) return '';
        if ( ! class_exists( 'Olo_CSS_Builder' ) ) return '';
        $cssb = new Olo_CSS_Builder();
        return $cssb->get_bg_inline_css( $bg );
    }

    public function render( $settings, $style = [] ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'olo-pcards-' . wp_rand( 10000, 99999 );

        $serif = "var(--olo-font-family-heading, 'Playfair Display','Cormorant Garamond',Georgia,'Times New Roman',serif)";
        $sans  = "var(--olo-font-family, 'Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif)";
        $mono  = "ui-monospace,'SF Mono',Menlo,Consolas,monospace";
        $fmap  = [ 'serif' => $serif, 'sans-serif' => $sans, 'mono' => $mono ];

        $letter_family = $fmap[ $s['letter_font_family'] ] ?? $serif;
        $title_family  = $fmap[ $s['title_font_family'] ]  ?? $serif;

        $cols = max( 1, min( 6, absint( $s['columns'] ) ) );
        $gap  = max( 0, min( 60, absint( $s['gap'] ) ) );

        $card_radius   = $this->build_border_radius_css( $s['card_radius'] ?? [] );
        $card_radius_h = $this->_radius_hover_diff( $s['card_radius'] ?? [], $s['card_radius_hover'] ?? [] );
        $card_rdur     = max( 50, intval( $s['card_radius_hover_duration'] ?? 400 ) );

        $shadow_map = [
            'none' => '',
            'sm'   => '0 1px 2px rgba(16,24,40,.06), 0 6px 16px -10px rgba(16,24,40,.18)',
            'md'   => '0 2px 4px rgba(16,24,40,.06), 0 14px 28px -12px rgba(22,38,61,.28)',
            'lg'   => '0 8px 24px -6px rgba(16,24,40,.18), 0 18px 40px -12px rgba(22,38,61,.30)',
            'xl'   => '0 12px 32px -8px rgba(16,24,40,.20), 0 28px 56px -14px rgba(22,38,61,.34)',
        ];
        $card_shadow = $shadow_map[ $s['card_shadow'] ?? 'sm' ] ?? '';

        $card_bg_css = $this->_bg_inline_css( $s['card_bg'] ?? [ 'type' => 'solid', 'color' => '#ffffff' ] );
        if ( ! $card_bg_css ) $card_bg_css = 'background:#ffffff';

        $card_color    = $this->safe_color_css( $s['card_color'] ) ?: 'var(--olo-color-text, #374151)';
        $card_padding  = max( 12, min( 60, absint( $s['card_padding'] ) ) );

        $top_aspect    = in_array( $s['top_aspect_ratio'] ?? '3/4', [ '1/1', '4/5', '3/4', '2/3', '3/2' ], true ) ? $s['top_aspect_ratio'] : '3/4';
        $top_padding   = max( 0, min( 60, absint( $s['top_padding'] ) ) );
        $letter_size   = max( 40, min( 280, absint( $s['letter_size'] ) ) );
        $letter_italic = ! empty( $s['letter_italic'] ) ? 'italic' : 'normal';
        $letter_align  = in_array( $s['letter_align'] ?? 'center', [ 'left', 'center', 'right' ], true ) ? $s['letter_align'] : 'center';
        $logo_height   = max( 16, min( 160, absint( $s['logo_height'] ?? 52 ) ) );

        $sl_color = $this->safe_color_css( $s['screenshot_label_color'] ) ?: 'var(--olo-color-text-faint, #9ca3af)';

        $brand_size = max( 10, min( 22, absint( $s['brand_size'] ) ) );
        $brand_ls   = max( 0, min( 0.3, floatval( $s['brand_letter_spacing'] ) ) );
        $title_size = max( 16, min( 80, absint( $s['title_size'] ) ) );
        $title_w    = preg_match( '/^\d+$/', (string) $s['title_weight'] ) ? $s['title_weight'] : '500';
        $desc_size  = max( 11, min( 22, absint( $s['description_size'] ) ) );
        $cta_size   = max( 9, min( 16, absint( $s['cta_size'] ) ) );

        $hover_effect = in_array( $s['card_hover_effect'] ?? 'lift', [ 'none', 'lift', 'scale', 'tilt' ], true ) ? $s['card_hover_effect'] : 'lift';

        $grid_style = sprintf( 'display:grid;grid-template-columns:repeat(%d,1fr);gap:%dpx;', $cols, $gap );

        $items = is_array( $s['items'] ) ? $s['items'] : [];

        ob_start();
        ?>
        <div class="olo-pcards <?php echo esc_attr( $uid ); ?> olo-pcards-hover-<?php echo esc_attr( $hover_effect ); ?>" style="<?php echo esc_attr( $grid_style ); ?>">
            <?php foreach ( $items as $idx => $it ) :
                $letter      = $it['letter'] ?? '';
                $logo        = $it['logo_image'] ?? '';
                $letter_clr  = $this->safe_color_css( $it['letter_color'] ?? '' ) ?: '#0f172a';
                $top_bg_css  = $this->_bg_inline_css( $it['top_bg'] ?? [ 'type' => 'solid', 'color' => '#f5f5f5' ] );
                if ( ! $top_bg_css ) $top_bg_css = 'background:#f5f5f5';

                $screen_lbl  = $it['screenshot_label'] ?? '';
                $brand_lbl   = $it['brand_label'] ?? '';
                $brand_clr   = $this->safe_color_css( $it['brand_color'] ?? '' ) ?: '#0f172a';
                $show_badge  = ! empty( $it['show_badge'] );
                $badge_txt   = $it['badge_text'] ?? '';
                $badge_bg    = $this->safe_color_css( $it['badge_bg'] ?? '' ) ?: '#0f172a';
                $badge_clr   = $this->safe_color_css( $it['badge_color'] ?? '' ) ?: 'var(--olo-color-on-primary, #ffffff)';

                $title       = $it['title'] ?? '';
                $title_acc   = $it['title_accent'] ?? '';
                $acc_italic  = ! empty( $it['title_accent_italic'] );

                $desc_raw    = $it['description'] ?? '';
                $desc        = preg_match( '/<[a-z!\/][^>]*>/i', $desc_raw ) ? $this->safe_richtext_content( $desc_raw ) : nl2br( esc_html( $desc_raw ) );

                $cta_text    = $it['cta_text'] ?? '';
                $cta_url     = $it['cta_url'] ?? '#';
            ?>
                <div class="olo-pcards__card" style="<?php echo esc_attr( $card_bg_css ); ?>;color:<?php echo esc_attr( $card_color ); ?>;<?php if ( $card_radius ) echo 'border-radius:' . esc_attr( $card_radius ) . ';'; ?><?php if ( $card_shadow ) echo 'box-shadow:' . esc_attr( $card_shadow ) . ';'; ?>overflow:hidden;display:flex;flex-direction:column;transition:transform .3s ease,box-shadow .3s ease<?php if ( $card_radius_h ) echo ',border-radius ' . $card_rdur . 'ms ease'; ?>">

                    <!-- TOP HALF: gradient + letter + screenshot label -->
                    <div class="olo-pcards__top" style="<?php echo esc_attr( $top_bg_css ); ?>;aspect-ratio:<?php echo esc_attr( $top_aspect ); ?>;padding:<?php echo $top_padding; ?>px;position:relative;display:flex;align-items:center;justify-content:<?php echo $letter_align === 'left' ? 'flex-start' : ( $letter_align === 'right' ? 'flex-end' : 'center' ); ?>">
                        <?php
                        $ov_op = intval( $it['top_bg']['overlay_opacity'] ?? 0 );
                        if ( ( $it['top_bg']['type'] ?? 'none' ) !== 'none' && $ov_op > 0 ) :
                            $ov_clr = $this->safe_color_css( $it['top_bg']['overlay_color'] ?? '#000000' ) ?: '#000000';
                        ?>
                            <div class="olo-pcards__overlay" style="position:absolute;inset:0;background-color:<?php echo esc_attr( $ov_clr ); ?>;opacity:<?php echo esc_attr( min( 100, $ov_op ) / 100 ); ?>;pointer-events:none;z-index:1" aria-hidden="true"></div>
                        <?php endif; ?>
                        <?php if ( $logo !== '' ) : ?>
                            <img class="olo-pcards__logo" src="<?php echo esc_url( $logo ); ?>" alt="<?php echo esc_attr( $brand_lbl ?: $title ); ?>" style="position:relative;z-index:2;max-height:<?php echo $logo_height; ?>px;max-width:78%;width:auto;height:auto;object-fit:contain;display:block" />
                        <?php elseif ( $letter !== '' ) : ?>
                            <span class="olo-pcards__letter" style="position:relative;z-index:2;font-family:<?php echo esc_attr( $letter_family ); ?>;font-size:<?php echo $letter_size; ?>px;font-style:<?php echo esc_attr( $letter_italic ); ?>;color:<?php echo esc_attr( $letter_clr ); ?>;line-height:1;font-weight:500" data-olo-editable="<?php echo 'items.' . intval( $idx ) . '.letter'; ?>"><?php echo esc_html( $letter ); ?></span>
                        <?php endif; ?>

                        <?php if ( ! empty( $s['show_screenshot_label'] ) && $screen_lbl ) : ?>
                            <div class="olo-pcards__screen-label" style="position:absolute;z-index:2;left:<?php echo $top_padding; ?>px;right:<?php echo $top_padding; ?>px;bottom:<?php echo $top_padding; ?>px;border:1px dashed color-mix(in srgb, <?php echo esc_attr( $sl_color ); ?> 40%, transparent);border-radius:6px;padding:8px 12px;text-align:center;font-family:<?php echo esc_attr( $mono ); ?>;font-size:10px;letter-spacing:0.1em;text-transform:uppercase;color:<?php echo esc_attr( $sl_color ); ?>" data-olo-editable="<?php echo 'items.' . intval( $idx ) . '.screenshot_label'; ?>"><?php echo esc_html( $screen_lbl ); ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- BOTTOM HALF: brand + badge + title + desc + cta -->
                    <div class="olo-pcards__bottom" style="padding:<?php echo $card_padding; ?>px;flex:1;display:flex;flex-direction:column;gap:14px">
                        <?php if ( $brand_lbl !== '' || ( $show_badge && $badge_txt !== '' ) ) : ?>
                            <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
                                <?php if ( $brand_lbl !== '' ) : ?>
                                    <span style="font-family:<?php echo esc_attr( $mono ); ?>;font-size:<?php echo $brand_size; ?>px;letter-spacing:<?php echo $brand_ls; ?>em;text-transform:uppercase;color:<?php echo esc_attr( $brand_clr ); ?>;font-weight:600" data-olo-editable="<?php echo 'items.' . intval( $idx ) . '.brand_label'; ?>"><?php echo esc_html( $brand_lbl ); ?></span>
                                <?php endif; ?>
                                <?php if ( $show_badge && $badge_txt !== '' ) : ?>
                                    <span style="display:inline-flex;align-items:center;padding:3px 10px;background:<?php echo esc_attr( $badge_bg ); ?>;color:<?php echo esc_attr( $badge_clr ); ?>;font-family:<?php echo esc_attr( $mono ); ?>;font-size:11px;letter-spacing:0.06em;text-transform:uppercase;border-radius:4px;font-weight:600" data-olo-editable="<?php echo 'items.' . intval( $idx ) . '.badge_text'; ?>"><?php echo esc_html( $badge_txt ); ?></span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <?php if ( $title !== '' || $title_acc !== '' ) : ?>
                            <h3 style="font-family:<?php echo esc_attr( $title_family ); ?>;font-size:<?php echo $title_size; ?>px;font-weight:<?php echo esc_attr( $title_w ); ?>;color:<?php echo esc_attr( $card_color ); ?>;margin:0;line-height:1.1;letter-spacing:-0.01em">
                                <?php if ( $title !== '' ) : ?><span data-olo-editable="<?php echo 'items.' . intval( $idx ) . '.title'; ?>"><?php echo esc_html( $title ); ?></span><?php endif; ?><?php if ( $title_acc !== '' ) : ?><span style="<?php if ( $acc_italic ) echo 'font-style:italic;'; ?>" data-olo-editable="<?php echo 'items.' . intval( $idx ) . '.title_accent'; ?>"><?php echo esc_html( $title_acc ); ?></span><?php endif; ?>
                            </h3>
                        <?php endif; ?>

                        <?php if ( $desc !== '' ) : ?>
                            <div style="font-family:<?php echo esc_attr( $sans ); ?>;font-size:<?php echo $desc_size; ?>px;line-height:1.55;color:<?php echo esc_attr( $card_color ); ?>;flex:1" data-olo-editable="<?php echo 'items.' . intval( $idx ) . '.description'; ?>" data-olo-richtext><?php echo $desc; ?></div>
                        <?php endif; ?>

                        <?php if ( $cta_text !== '' ) : ?>
                            <a class="olo-pcards__cta" href="<?php echo esc_url( $cta_url ?: '#' ); ?>" style="font-family:<?php echo esc_attr( $mono ); ?>;font-size:<?php echo $cta_size; ?>px;letter-spacing:0.08em;text-transform:uppercase;color:<?php echo esc_attr( $brand_clr ); ?>;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:6px;margin-top:auto" data-olo-editable="<?php echo 'items.' . intval( $idx ) . '.cta_text'; ?>"><?php echo esc_html( $cta_text ); ?><?php if ( ! empty( $s['cta_arrow'] ) ) echo ' →'; ?></a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <style>
            <?php switch ( $hover_effect ) :
                case 'lift' : ?>
                    .<?php echo $uid; ?> .olo-pcards__card:hover { transform: translateY(-6px); box-shadow: 0 14px 36px rgba(0,0,0,0.15); }
                    <?php break;
                case 'scale' : ?>
                    .<?php echo $uid; ?> .olo-pcards__card:hover { transform: scale(1.03); z-index: 2; }
                    <?php break;
                case 'tilt' : ?>
                    .<?php echo $uid; ?> .olo-pcards__card { transform-style: preserve-3d; }
                    .<?php echo $uid; ?> .olo-pcards__card:hover { transform: perspective(800px) rotateX(2deg) rotateY(-2deg) scale(1.02); }
                    <?php break;
            endswitch; ?>
            <?php if ( $card_radius_h ) : ?>
            .<?php echo $uid; ?> .olo-pcards__card:hover { border-radius: <?php echo $card_radius_h; ?> !important; }
            <?php endif; ?>
            .<?php echo $uid; ?> .olo-pcards__cta:focus-visible { outline: none; border-radius: 3px; box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent); }
            @media (max-width: 1100px) { .<?php echo $uid; ?> { grid-template-columns: repeat(<?php echo min( $cols, 3 ); ?>, 1fr) !important; } }
            @media (max-width: 700px)  { .<?php echo $uid; ?> { grid-template-columns: 1fr !important; } }
        </style>
        <?php

        return ob_get_clean();
    }
}
