<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Progresstracker_Tile extends Olo_Tile_Base {

    protected $type     = 'progresstracker';
    protected $name     = 'Progress tracker';
    protected $icon     = 'dashicons-editor-ol';
    protected $category = 'content';
    protected $defaults = [
        'preset' => 'custom',
        'items' => [
            [ 'title' => 'Ordine ricevuto', 'description' => 'Il tuo ordine è stato confermato.', 'icon' => 'check', 'status' => 'completed' ],
            [ 'title' => 'In preparazione', 'description' => 'Stiamo preparando il tuo ordine.', 'icon' => 'settings', 'status' => 'active' ],
            [ 'title' => 'Spedito', 'description' => 'Il pacco è in viaggio.', 'icon' => 'cart', 'status' => 'pending' ],
            [ 'title' => 'Consegnato', 'description' => 'Consegna completata.', 'icon' => 'home', 'status' => 'pending' ],
        ],
        'layout'           => 'horizontal',
        'connector_style'  => 'line',
        'connector_color'  => '#e5e7eb',
        'completed_color'  => '#10b981',
        'active_color'     => '#3b82f6',
        'pending_color'    => '#9ca3af',
        'text_color'       => '#F3F4F6',
        'show_description' => true,
        'show_numbers'     => true,
        'circle_size'      => '40',
        'font_size'        => '14',
        'gap'              => '0',
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

    public function render( $settings ) {
        $s     = wp_parse_args( $settings, $this->defaults );
        $items = $this->parse_items( $s['items'] );
        $count = count( $items );

        if ( $count === 0 ) {
            return '';
        }

        $uid = 'olo-pt-' . wp_rand( 10000, 99999 );

        $layout     = $s['layout'] === 'vertical' ? 'vertical' : 'horizontal';
        $conn_style = in_array( $s['connector_style'], [ 'line', 'dashed', 'dotted' ] ) ? $s['connector_style'] : 'line';
        $conn_css   = $conn_style === 'line' ? 'solid' : $conn_style;
        $conn_clr   = $this->safe_color_css( $s['connector_color'] ) ?: '#e5e7eb';
        $comp_clr   = $this->safe_color_css( $s['completed_color'] ) ?: '#10b981';
        $act_clr    = $this->safe_color_css( $s['active_color'] ) ?: '#3b82f6';
        $pend_clr   = $this->safe_color_css( $s['pending_color'] ) ?: '#9ca3af';
        $text_clr   = $this->safe_color_css( $s['text_color'] ) ?: '#F3F4F6';
        $show_desc  = filter_var( $s['show_description'], FILTER_VALIDATE_BOOLEAN );
        $show_nums  = filter_var( $s['show_numbers'], FILTER_VALIDATE_BOOLEAN );
        $c_size     = max( 24, intval( $s['circle_size'] ) );
        $f_size     = intval( $s['font_size'] ) ?: 14;
        $icon_size  = intval( round( $c_size * 0.45 ) );

        ob_start();
        ?>
        <style>
            /* === Container === */
            .<?php echo $uid; ?> {
                padding: 16px;
            }

            /* === Status colors === */
            .<?php echo $uid; ?> .olo-pt-circle--completed {
                background: <?php echo $comp_clr; ?>;
                color: var(--olo-color-primary-contrast, #FFFFFF);
            }
            .<?php echo $uid; ?> .olo-pt-circle--active {
                background: <?php echo $act_clr; ?>;
                color: var(--olo-color-primary-contrast, #FFFFFF);
                box-shadow: 0 0 0 4px <?php echo $act_clr; ?>40;
                animation: olo-pt-pulse-<?php echo $uid; ?> 2s ease-in-out infinite;
            }
            .<?php echo $uid; ?> .olo-pt-circle--pending {
                background: transparent;
                color: <?php echo $pend_clr; ?>;
                border: 3px solid <?php echo $pend_clr; ?>;
            }

            @keyframes olo-pt-pulse-<?php echo $uid; ?> {
                0%, 100% { box-shadow: 0 0 0 4px <?php echo $act_clr; ?>40; }
                50% { box-shadow: 0 0 0 8px <?php echo $act_clr; ?>20; }
            }

            .<?php echo $uid; ?> .olo-pt-circle {
                width: <?php echo $c_size; ?>px;
                height: <?php echo $c_size; ?>px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
                font-weight: 700;
                font-size: <?php echo intval( $c_size * 0.38 ); ?>px;
                position: relative;
                z-index: 2;
                transition: all 0.3s ease;
            }
            .<?php echo $uid; ?> .olo-pt-circle svg {
                width: <?php echo $icon_size; ?>px;
                height: <?php echo $icon_size; ?>px;
            }

            .<?php echo $uid; ?> .olo-pt-title {
                color: <?php echo $text_clr; ?>;
                font-size: <?php echo $f_size; ?>px;
                font-weight: 600;
                line-height: 1.3;
            }
            .<?php echo $uid; ?> .olo-pt-desc {
                color: <?php echo $text_clr; ?>;
                font-size: <?php echo max( 11, $f_size - 2 ); ?>px;
                opacity: 0.7;
                margin-top: 2px;
                line-height: 1.4;
            }

            <?php if ( $layout === 'horizontal' ) : ?>
            /* === Horizontal layout === */
            .<?php echo $uid; ?> .olo-pt-h-wrap {
                display: flex;
                align-items: flex-start;
                width: 100%;
            }
            .<?php echo $uid; ?> .olo-pt-h-step {
                flex: 1;
                display: flex;
                flex-direction: column;
                align-items: center;
                position: relative;
            }
            .<?php echo $uid; ?> .olo-pt-h-circle-row {
                display: flex;
                align-items: center;
                width: 100%;
            }
            .<?php echo $uid; ?> .olo-pt-h-conn {
                flex: 1;
                height: 0;
                border-top: 2px <?php echo $conn_css; ?> <?php echo $conn_clr; ?>;
                align-self: center;
            }
            .<?php echo $uid; ?> .olo-pt-h-label {
                text-align: center;
                margin-top: 8px;
                padding: 0 4px;
            }

            @media (max-width: 640px) {
                .<?php echo $uid; ?> .olo-pt-h-wrap {
                    flex-direction: column;
                    align-items: flex-start;
                }
                .<?php echo $uid; ?> .olo-pt-h-step {
                    flex-direction: row;
                    align-items: flex-start;
                    gap: 12px;
                    width: 100%;
                }
                .<?php echo $uid; ?> .olo-pt-h-circle-row {
                    width: auto;
                    flex-direction: column;
                }
                .<?php echo $uid; ?> .olo-pt-h-conn {
                    width: 0;
                    height: auto;
                    min-height: 16px;
                    border-top: none;
                    border-left: 2px <?php echo $conn_css; ?> <?php echo $conn_clr; ?>;
                    flex: 1;
                    margin: 4px 0 4px <?php echo intval( $c_size / 2 ) - 1; ?>px;
                }
                .<?php echo $uid; ?> .olo-pt-h-label {
                    text-align: left;
                    margin-top: 0;
                    padding: <?php echo intval( $c_size / 2 - 8 ); ?>px 0 16px;
                }
            }
            <?php else : ?>
            /* === Vertical layout === */
            .<?php echo $uid; ?> .olo-pt-v-step {
                display: flex;
                gap: 16px;
                position: relative;
            }
            .<?php echo $uid; ?> .olo-pt-v-circle-col {
                display: flex;
                flex-direction: column;
                align-items: center;
                flex-shrink: 0;
            }
            .<?php echo $uid; ?> .olo-pt-v-conn {
                width: 0;
                flex: 1;
                min-height: 24px;
                border-left: 2px <?php echo $conn_css; ?> <?php echo $conn_clr; ?>;
                margin: 4px 0 4px 0;
            }
            .<?php echo $uid; ?> .olo-pt-v-content {
                padding-bottom: 24px;
                padding-top: <?php echo max( 0, intval( $c_size / 2 - 8 ) ); ?>px;
            }
            <?php endif; ?>
        </style>
        <div class="olo-progresstracker <?php echo esc_attr( $uid ); ?> olo-pt-preset-<?php echo esc_attr( sanitize_key( $s['preset'] ?? 'custom' ) ); ?>">
        <?php if ( $layout === 'horizontal' ) : ?>
            <div class="olo-pt-h-wrap">
                <?php foreach ( $items as $i => $item ) :
                    $status    = $item['status'] ?: 'pending';
                    $circle_cl = 'olo-pt-circle olo-pt-circle--' . esc_attr( $status );
                ?>
                <div class="olo-pt-h-step">
                    <div class="olo-pt-h-circle-row">
                        <?php if ( $i > 0 ) : ?>
                        <div class="olo-pt-h-conn"></div>
                        <?php endif; ?>

                        <div class="<?php echo $circle_cl; ?>">
                            <?php if ( $status === 'completed' ) : ?>
                                <svg viewBox="0 0 24 24" fill="none">
                                    <path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            <?php elseif ( $show_nums ) : ?>
                                <?php echo $i + 1; ?>
                            <?php elseif ( ! empty( $item['icon'] ) ) : ?>
                                <span uk-icon="icon: <?php echo esc_attr( $item['icon'] ); ?>; ratio: <?php echo round( $icon_size / 20, 2 ); ?>"></span>
                            <?php endif; ?>
                        </div>

                        <?php if ( $i < $count - 1 ) : ?>
                        <div class="olo-pt-h-conn"></div>
                        <?php endif; ?>
                    </div>
                    <div class="olo-pt-h-label">
                        <?php list( $ptt_cls, $ptt_data ) = $this->tfx_attrs( $s, "title", $item["title"] ); ?><div class="olo-pt-title<?php echo $ptt_cls; ?>"<?php echo $ptt_data; ?>><?php echo esc_html( $item["title"] ); ?></div>
                        <?php if ( $show_desc ) : ?>
                            <?php if ( ! empty( $item['description'] ) ) : ?>
                            <?php list( $ptd_cls, $ptd_data ) = $this->tfx_attrs( $s, "description", $item["description"] ); ?><div class="olo-pt-desc<?php echo $ptd_cls; ?>"<?php echo $ptd_data; ?>><?php echo esc_html( $item["description"] ); ?></div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php else : ?>
            <?php foreach ( $items as $i => $item ) :
                $status    = $item['status'] ?: 'pending';
                $circle_cl = 'olo-pt-circle olo-pt-circle--' . esc_attr( $status );
            ?>
            <div class="olo-pt-v-step">
                <div class="olo-pt-v-circle-col">
                    <div class="<?php echo $circle_cl; ?>">
                        <?php if ( $status === 'completed' ) : ?>
                            <svg viewBox="0 0 24 24" fill="none">
                                <path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        <?php elseif ( $show_nums ) : ?>
                            <?php echo $i + 1; ?>
                        <?php elseif ( ! empty( $item['icon'] ) ) : ?>
                            <span uk-icon="icon: <?php echo esc_attr( $item['icon'] ); ?>; ratio: <?php echo round( $icon_size / 20, 2 ); ?>"></span>
                        <?php endif; ?>
                    </div>
                    <?php if ( $i < $count - 1 ) : ?>
                    <div class="olo-pt-v-conn"></div>
                    <?php endif; ?>
                </div>
                <div class="olo-pt-v-content">
                    <?php list( $ptt_cls, $ptt_data ) = $this->tfx_attrs( $s, "title", $item["title"] ); ?><div class="olo-pt-title<?php echo $ptt_cls; ?>"<?php echo $ptt_data; ?>><?php echo esc_html( $item["title"] ); ?></div>
                    <?php if ( $show_desc ) : ?>
                        <?php if ( ! empty( $item['description'] ) ) : ?>
                        <?php list( $ptd_cls, $ptd_data ) = $this->tfx_attrs( $s, "description", $item["description"] ); ?><div class="olo-pt-desc<?php echo $ptd_cls; ?>"<?php echo $ptd_data; ?>><?php echo esc_html( $item["description"] ); ?></div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
        </div>
        <?php
        $tfx_css = $this->tfx_css( $s, '.' . $uid );
        if ( $tfx_css ) echo '<style>' . $tfx_css . '</style>';
        $this->tfx_print_script();
                // Border system
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) echo ".{$uid}{{$border_css}}";
            echo $border_hover_css . $border_effect_css . '</style>';
        }
        return ob_get_clean();
    }

    private function parse_items( $raw ) {
        if ( ! is_array( $raw ) ) {
            return [];
        }
        $items = [];
        foreach ( $raw as $item ) {
            if ( is_array( $item ) ) {
                $items[] = [
                    'title'       => $item['title'] ?? '',
                    'description' => $item['description'] ?? '',
                    'icon'        => $item['icon'] ?? 'check',
                    'status'      => $item['status'] ?? 'pending',
                ];
            }
        }
        return $items;
    }
}
