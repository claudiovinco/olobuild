<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Info Cards — griglia parametrica di card riusabile.
 *   - background creativo unificato per container e ogni card
 *   - toggle granulari (icon, counter, counter_label, arrow, footer, divider)
 *   - 5 effetti hover (lift/scale/glow/tilt/none)
 */
class Olo_InfoCards_Tile extends Olo_Tile_Base {

    protected $type     = 'info-cards';
    protected $name     = 'Info Cards';
    protected $icon     = 'dashicons-grid-view';
    protected $category = 'layout';
    protected $defaults = [
        'container_bg'                       => [ 'type' => 'solid', 'color' => '#0f172a' ],
        'container_radius'                   => [ 'tl' => 24, 'tr' => 24, 'br' => 24, 'bl' => 24, 'linked' => true ],
        'container_radius_hover'             => [ 'tl' => 24, 'tr' => 24, 'br' => 24, 'bl' => 24, 'linked' => true ],
        'container_radius_hover_duration'    => 400,
        'container_padding'                  => 12,
        'container_gap'                      => 0,

        'columns'   => 3,
        'items_gap' => 0,

        'items' => [
            [ 'counter' => '01', 'counter_label' => 'Carta',         'title' => 'Zero',    'title_accent' => '',   'title_accent_italic' => true,  'description' => 'Niente <strong>carta di credito</strong> per scaricare e provare. Niente trial scaduto, niente sblocchi nascosti.', 'icon' => '', 'footer_dot_color' => '#10b981', 'footer_text' => '', 'link_url' => '', 'media_image' => '', 'media_label' => 'SCREENSHOT · 01' ],
            [ 'counter' => '02', 'counter_label' => 'Registrazione', 'title' => 'Niente',  'title_accent' => '',   'title_accent_italic' => true,  'description' => 'Nessuna <strong>registrazione obbligatoria</strong>. Scarichi, installi, lavori. L\'account lo crei solo se vuoi.', 'icon' => '', 'footer_dot_color' => '#10b981', 'footer_text' => '', 'link_url' => '', 'media_image' => '', 'media_label' => 'SCREENSHOT · 02' ],
            [ 'counter' => '03', 'counter_label' => 'Pro',           'title' => '30',      'title_accent' => 'gg', 'title_accent_italic' => false, 'description' => '<strong>Soddisfatti o rimborsati</strong> su OLObuild Pro. 30 giorni pieni, nessuna domanda, zero ostacoli.', 'icon' => '', 'footer_dot_color' => '#10b981', 'footer_text' => '', 'link_url' => '', 'media_image' => '', 'media_label' => 'SCREENSHOT · 03' ],
        ],

        'card_bg'           => [ 'type' => 'solid', 'color' => '#0f172a' ],
        'card_color'        => '#e5e7eb',
        'card_accent_color' => '#b3261e',
        'card_radius'                  => [ 'tl' => 18, 'tr' => 18, 'br' => 18, 'bl' => 18, 'linked' => true ],
        'card_radius_hover'            => [ 'tl' => 18, 'tr' => 18, 'br' => 18, 'bl' => 18, 'linked' => true ],
        'card_radius_hover_duration'   => 400,
        'card_padding'                 => 40,
        'card_border'                  => '',

        'show_icon'          => false,
        'show_counter'       => true,
        'show_counter_label' => true,
        'show_arrow'         => true,
        'show_footer'        => false,
        'show_divider'       => false,
        'show_media'                  => false,
        'media_aspect_ratio'          => '4/3',
        'media_radius'                => [ 'tl' => 18, 'tr' => 18, 'br' => 18, 'bl' => 18, 'linked' => true ],
        'media_radius_hover'          => [ 'tl' => 18, 'tr' => 18, 'br' => 18, 'bl' => 18, 'linked' => true ],
        'media_radius_hover_duration' => 400,
        'media_position'              => 'top',

        'title_font_family' => 'serif',
        'title_size'        => 72,
        'title_weight'      => '500',
        'title_italic'      => true,
        'counter_size'      => 11,
        'description_size'  => 15,
        'footer_size'       => 10,

        'card_hover_effect' => 'none',
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

    public function render( $settings, $style = [] ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'olo-icards-' . wp_rand( 10000, 99999 );

        $serif = "'Playfair Display','Cormorant Garamond',Georgia,'Times New Roman',serif";
        $sans  = "'Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif";
        $mono  = "ui-monospace,'SF Mono',Menlo,Consolas,monospace";
        $fmap  = [ 'serif' => $serif, 'sans-serif' => $sans, 'mono' => $mono ];
        $tfam  = $fmap[ $s['title_font_family'] ] ?? $serif;

        $cols       = max( 1, min( 6, absint( $s['columns'] ) ) );
        $items_gap  = max( 0, min( 60, absint( $s['items_gap'] ) ) );
        $c_pad      = max( 0, min( 80, absint( $s['container_padding'] ) ) );
        $c_gap      = max( 0, min( 40, absint( $s['container_gap'] ) ) );
        $card_pad   = max( 10, min( 80, absint( $s['card_padding'] ) ) );
        // Border-radius standard Olobuild (4 angoli + hover)
        $c_radius      = $this->build_border_radius_css( $s['container_radius'] ?? [] );
        $c_radius_h    = $this->_radius_hover_diff( $s['container_radius'] ?? [], $s['container_radius_hover'] ?? [] );
        $c_rdur        = max( 50, intval( $s['container_radius_hover_duration'] ?? 400 ) );
        $card_radius   = $this->build_border_radius_css( $s['card_radius'] ?? [] );
        $card_radius_h = $this->_radius_hover_diff( $s['card_radius'] ?? [], $s['card_radius_hover'] ?? [] );
        $card_rdur     = max( 50, intval( $s['card_radius_hover_duration'] ?? 400 ) );
        $media_radius   = $this->build_border_radius_css( $s['media_radius'] ?? [] );
        $media_radius_h = $this->_radius_hover_diff( $s['media_radius'] ?? [], $s['media_radius_hover'] ?? [] );
        $media_rdur     = max( 50, intval( $s['media_radius_hover_duration'] ?? 400 ) );
        $aspect_allow  = [ '16/9', '4/3', '3/2', '1/1', '21/9' ];
        $media_aspect  = in_array( $s['media_aspect_ratio'] ?? '4/3', $aspect_allow, true ) ? $s['media_aspect_ratio'] : '4/3';

        $title_size  = max( 18, min( 160, absint( $s['title_size'] ) ) );
        $title_weight = preg_match( '/^\d+$/', (string) $s['title_weight'] ) ? $s['title_weight'] : '500';
        $counter_size = max( 9, min( 22, absint( $s['counter_size'] ) ) );
        $desc_size    = max( 11, min( 22, absint( $s['description_size'] ) ) );
        $footer_size  = max( 9, min( 16, absint( $s['footer_size'] ) ) );

        $card_color   = $this->safe_color_css( $s['card_color'] ) ?: '#e5e7eb';
        $accent_color = $this->safe_color_css( $s['card_accent_color'] ) ?: '#b3261e';
        $card_border  = $this->safe_color_css( $s['card_border'] ) ?: '';

        // Container bg
        $container_bg_css = '';
        $cbg = $s['container_bg'] ?? [ 'type' => 'none' ];
        if ( is_array( $cbg ) && ( $cbg['type'] ?? 'none' ) !== 'none' && class_exists( 'Olo_CSS_Builder' ) ) {
            $cssb = new Olo_CSS_Builder();
            $container_bg_css = $cssb->get_bg_inline_css( $cbg );
        }

        // Card bg (applicato a ogni card; se ogni card vuole bg singolo, si fa per item)
        $card_bg_css_default = '';
        $cardbg = $s['card_bg'] ?? [ 'type' => 'none' ];
        if ( is_array( $cardbg ) && ( $cardbg['type'] ?? 'none' ) !== 'none' && class_exists( 'Olo_CSS_Builder' ) ) {
            $cssb = new Olo_CSS_Builder();
            $card_bg_css_default = $cssb->get_bg_inline_css( $cardbg );
        }

        $items = is_array( $s['items'] ) ? $s['items'] : [];
        $hover_effect = in_array( $s['card_hover_effect'] ?? 'none', [ 'none', 'lift', 'scale', 'glow', 'tilt' ], true ) ? $s['card_hover_effect'] : 'none';

        $container_style = $container_bg_css . ';' . ( $c_radius ? 'border-radius:' . $c_radius . ';' : '' ) . 'padding:' . $c_pad . 'px;' . ( $c_radius_h ? 'transition:border-radius ' . $c_rdur . 'ms ease;' : '' );
        $grid_style = 'display:grid;grid-template-columns:repeat(' . $cols . ',1fr);gap:' . $items_gap . 'px;';

        // Card style template
        $card_style_base = $card_bg_css_default . ';color:' . $card_color . ';' . ( $card_radius ? 'border-radius:' . $card_radius . ';' : '' ) . 'padding:' . $card_pad . 'px;position:relative;display:flex;flex-direction:column;min-height:280px;transition:transform .3s ease,box-shadow .3s ease,border-color .3s ease' . ( $card_radius_h ? ',border-radius ' . $card_rdur . 'ms ease' : '' ) . ';';
        if ( $card_border ) $card_style_base .= 'border:1px solid ' . $card_border . ';';

        ob_start();
        ?>
        <div class="olo-icards <?php echo esc_attr( $uid ); ?> olo-icards-hover-<?php echo esc_attr( $hover_effect ); ?>" style="<?php echo esc_attr( $container_style ); ?>">
            <div class="olo-icards__grid" style="<?php echo esc_attr( $grid_style ); ?>">
                <?php foreach ( $items as $idx => $it ) :
                    $counter        = $it['counter'] ?? '';
                    $counter_label  = $it['counter_label'] ?? '';
                    $title          = $it['title'] ?? '';
                    $title_accent   = $it['title_accent'] ?? '';
                    $accent_italic  = ! empty( $it['title_accent_italic'] );
                    $desc_raw       = $it['description'] ?? '';
                    $desc           = preg_match( '/<[a-z!\/][^>]*>/i', $desc_raw ) ? $this->safe_richtext_content( $desc_raw ) : nl2br( esc_html( $desc_raw ) );
                    $icon_name      = $it['icon'] ?? '';
                    $foot_dot       = $this->safe_color_css( $it['footer_dot_color'] ?? '' ) ?: '#10b981';
                    $foot_text      = $it['footer_text'] ?? '';
                    $link_url       = $it['link_url'] ?? '';
                    $is_link        = ! empty( $link_url );
                    $tag            = $is_link ? 'a' : 'div';
                    $tag_attrs      = $is_link ? ' href="' . esc_url( $link_url ) . '" style="text-decoration:none;color:inherit;"' : '';
                ?>
                    <<?php echo $tag . $tag_attrs; ?> class="olo-icards__card olo-icards__card--<?php echo $idx; ?>" style="<?php echo esc_attr( $card_style_base ); ?>">

                        <!-- MEDIA (top, opzionale) -->
                        <?php if ( ! empty( $s['show_media'] ) ) :
                            $media_img = $it['media_image'] ?? '';
                            $media_lbl = $it['media_label'] ?? '';
                            $media_inner_style = 'width:100%;aspect-ratio:' . esc_attr( $media_aspect ) . ';' . ( $media_radius ? 'border-radius:' . esc_attr( $media_radius ) . ';' : '' ) . 'overflow:hidden;background:' . esc_attr( $card_color ) . '14;border:1px solid ' . esc_attr( $card_color ) . '22;display:flex;align-items:center;justify-content:center;margin-bottom:28px;' . ( $media_radius_h ? 'transition:border-radius ' . $media_rdur . 'ms ease;' : '' );
                        ?>
                            <div class="olo-icards__media" style="<?php echo esc_attr( $media_inner_style ); ?>">
                                <?php if ( $media_img ) : ?>
                                    <img src="<?php echo esc_url( $media_img ); ?>" alt="" loading="lazy" style="width:100%;height:100%;object-fit:cover;display:block" />
                                <?php elseif ( $media_lbl ) : ?>
                                    <span style="font-family:<?php echo esc_attr( $mono ); ?>;font-size:11px;letter-spacing:0.12em;color:<?php echo esc_attr( $card_color ); ?>;opacity:.45;text-transform:uppercase" data-olo-editable="<?php echo 'items.' . intval( $idx ) . '.media_label'; ?>"><?php echo esc_html( $media_lbl ); ?></span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <!-- TOP ROW: icon ←→ counter/arrow -->
                        <?php if ( ! empty( $s['show_icon'] ) || ! empty( $s['show_counter'] ) || ! empty( $s['show_arrow'] ) ) : ?>
                            <div class="olo-icards__top" style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:48px;min-height:36px">
                                <div style="display:flex;align-items:center;gap:10px;flex:1">
                                    <?php if ( ! empty( $s['show_icon'] ) && $icon_name ) : ?>
                                        <span style="font-size:1.8em;line-height:1;color:<?php echo esc_attr( $card_color ); ?>">
                                            <?php echo $this->render_icon_html( $icon_name, 1.8 ); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <div style="display:flex;align-items:center;gap:14px">
                                    <?php if ( ! empty( $s['show_counter'] ) && $counter ) : ?>
                                        <span style="font-family:<?php echo esc_attr( $mono ); ?>;font-size:<?php echo $counter_size; ?>px;letter-spacing:0.08em;text-transform:uppercase;color:<?php echo esc_attr( $card_color ); ?>;opacity:.6"><span data-olo-editable="<?php echo 'items.' . intval( $idx ) . '.counter'; ?>"><?php echo esc_html( $counter ); ?></span><?php if ( ! empty( $s['show_counter_label'] ) && $counter_label ) : ?> / <span data-olo-editable="<?php echo 'items.' . intval( $idx ) . '.counter_label'; ?>"><?php echo esc_html( $counter_label ); ?></span><?php endif; ?></span>
                                    <?php endif; ?>
                                    <?php if ( ! empty( $s['show_arrow'] ) ) : ?>
                                        <span style="width:34px;height:34px;border-radius:50%;border:1px solid <?php echo esc_attr( $card_color ); ?>33;display:inline-flex;align-items:center;justify-content:center;color:<?php echo esc_attr( $card_color ); ?>;font-size:14px;opacity:.7">→</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- TITLE -->
                        <?php if ( $title !== '' || $title_accent !== '' ) :
                            $title_italic_css = ! empty( $s['title_italic'] ) ? 'font-style:italic;' : '';
                        ?>
                            <div class="olo-icards__title" style="font-family:<?php echo esc_attr( $tfam ); ?>;font-size:<?php echo $title_size; ?>px;line-height:1.05;font-weight:<?php echo esc_attr( $title_weight ); ?>;color:<?php echo esc_attr( $accent_color ); ?>;<?php echo $title_italic_css; ?>letter-spacing:-0.02em;margin-bottom:20px">
                                <?php if ( $title !== '' ) : ?><span data-olo-editable="<?php echo 'items.' . intval( $idx ) . '.title'; ?>"><?php echo esc_html( $title ); ?></span><?php endif; ?><?php if ( $title_accent !== '' ) : ?><span style="font-size:.45em;vertical-align:baseline;margin-left:0.05em;<?php if ( $accent_italic ) echo 'font-style:italic;'; ?>" data-olo-editable="<?php echo 'items.' . intval( $idx ) . '.title_accent'; ?>"><?php echo esc_html( $title_accent ); ?></span><?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <!-- DESCRIPTION -->
                        <?php if ( $desc ) : ?>
                            <div class="olo-icards__desc" style="font-family:<?php echo esc_attr( $sans ); ?>;font-size:<?php echo $desc_size; ?>px;line-height:1.55;color:<?php echo esc_attr( $card_color ); ?>;flex:1" data-olo-editable="<?php echo 'items.' . intval( $idx ) . '.description'; ?>" data-olo-richtext><?php echo $desc; ?></div>
                        <?php endif; ?>

                        <?php if ( ! empty( $s['show_divider'] ) ) : ?>
                            <div style="height:1px;background:<?php echo esc_attr( $card_color ); ?>1a;margin:24px 0 18px"></div>
                        <?php endif; ?>

                        <!-- FOOTER -->
                        <?php if ( ! empty( $s['show_footer'] ) && $foot_text ) : ?>
                            <div class="olo-icards__footer" style="display:inline-flex;align-items:center;gap:10px;margin-top:<?php echo empty( $s['show_divider'] ) ? '24px' : '0'; ?>;font-family:<?php echo esc_attr( $mono ); ?>;font-size:<?php echo $footer_size; ?>px;letter-spacing:0.08em;text-transform:uppercase;color:<?php echo esc_attr( $card_color ); ?>;opacity:.7">
                                <span style="width:8px;height:8px;border-radius:50%;background:<?php echo esc_attr( $foot_dot ); ?>"></span>
                                <span data-olo-editable="<?php echo 'items.' . intval( $idx ) . '.footer_text'; ?>"><?php echo esc_html( $foot_text ); ?></span>
                            </div>
                        <?php endif; ?>
                    </<?php echo $tag; ?>>
                <?php endforeach; ?>
            </div>
        </div>

        <style>
            <?php if ( $c_gap > 0 ) : ?>
            .<?php echo $uid; ?> .olo-icards__card { margin: <?php echo $c_gap; ?>px; }
            <?php endif; ?>
            <?php if ( $c_radius_h ) : ?>
            .<?php echo $uid; ?>:hover { border-radius: <?php echo $c_radius_h; ?> !important; }
            <?php endif; ?>
            <?php if ( $card_radius_h ) : ?>
            .<?php echo $uid; ?> .olo-icards__card:hover { border-radius: <?php echo $card_radius_h; ?> !important; }
            <?php endif; ?>
            <?php if ( $media_radius_h ) : ?>
            .<?php echo $uid; ?> .olo-icards__card:hover .olo-icards__media { border-radius: <?php echo $media_radius_h; ?> !important; }
            <?php endif; ?>
            <?php switch ( $hover_effect ) :
                case 'lift' : ?>
                    .<?php echo $uid; ?> .olo-icards__card:hover { transform: translateY(-6px); box-shadow: 0 14px 36px rgba(0,0,0,0.15); }
                    <?php break;
                case 'scale' : ?>
                    .<?php echo $uid; ?> .olo-icards__card:hover { transform: scale(1.03); z-index: 2; }
                    <?php break;
                case 'glow' : ?>
                    .<?php echo $uid; ?> .olo-icards__card:hover { box-shadow: 0 0 0 1px <?php echo $accent_color; ?>, 0 0 30px <?php echo $accent_color; ?>33; }
                    <?php break;
                case 'tilt' : ?>
                    .<?php echo $uid; ?> .olo-icards__card { transform-style: preserve-3d; }
                    .<?php echo $uid; ?> .olo-icards__card:hover { transform: perspective(800px) rotateX(2deg) rotateY(-2deg) scale(1.02); }
                    <?php break;
            endswitch; ?>
            @media (max-width: 900px) {
                .<?php echo $uid; ?> .olo-icards__grid { grid-template-columns: 1fr !important; }
            }
            @media (min-width: 901px) and (max-width: 1200px) {
                .<?php echo $uid; ?> .olo-icards__grid { grid-template-columns: repeat(<?php echo min( $cols, 3 ); ?>, 1fr) !important; }
            }
        </style>
        <?php
        return ob_get_clean();
    }
}
