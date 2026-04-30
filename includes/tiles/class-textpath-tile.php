<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Textpath_Tile extends Olo_Tile_Base {

    protected $type     = 'textpath';
    protected $name     = 'Testo su Tracciato';
    protected $icon     = 'dashicons-editor-textcolor';
    protected $category = 'text';
    protected $defaults = [
        'text'            => 'Testo che segue un tracciato curvo',
        'path_preset'     => 'arc',
        'custom_path'     => '',
        'font_size'       => '24',
        'text_color'      => '',
        'letter_spacing'  => '2',
        'animation'       => 'none',
        'animation_speed' => '10',
    ];

    public function get_controls() {
        return [];
    }

    public function render( $settings ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'olo-textpath-' . wp_unique_id();

        // Settings
        $text    = esc_html( $s['text'] );
        $fsize   = max( 12, min( 72, intval( $s['font_size'] ) ) );
        $color   = $this->safe_color_css( $s['text_color'] ) ?: 'var(--olo-color-text, #374151)';
        $spacing = max( 0, min( 20, intval( $s['letter_spacing'] ) ) );
        $anim    = in_array( $s['animation'], [ 'none', 'scroll', 'continuous' ], true ) ? $s['animation'] : 'none';
        $speed   = max( 1, min( 20, intval( $s['animation_speed'] ) ) );

        // Path presets
        $presets = [
            'arc'    => 'M 10 80 Q 150 10 290 80',
            'wave'   => 'M 0 50 Q 75 0 150 50 Q 225 100 300 50',
            'circle' => 'M 150,10 A 140,140 0 1,1 149.99,10',
            'spiral' => 'M 150,75 C 150,30 200,10 220,50 C 240,90 200,120 160,100 C 120,80 110,40 150,20 C 190,0 250,30 260,75 C 270,120 220,160 160,140',
        ];

        $preset = $s['path_preset'];
        if ( $preset === 'custom' ) {
            // Sanitize custom path: allow only SVG path commands
            $path_d = preg_replace( '/[^A-Za-z0-9.,\s\-]/', '', $s['custom_path'] );
            if ( empty( $path_d ) ) {
                $path_d = $presets['arc'];
            }
        } else {
            $path_d = isset( $presets[ $preset ] ) ? $presets[ $preset ] : $presets['arc'];
        }

        // ViewBox based on preset
        $viewbox = ( $preset === 'circle' ) ? '0 0 300 300' : '0 0 300 100';

        $path_id = $uid . '-path';

        ob_start();
        ?>
        <style>
            .<?php echo $uid; ?> {
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .<?php echo $uid; ?> svg {
                width: 100%;
                height: auto;
                overflow: visible;
            }

            <?php if ( $anim === 'continuous' ) : ?>
            @keyframes <?php echo $uid; ?>-offset {
                0%   { /* start */ }
                100% { /* end */ }
            }
            <?php endif; ?>
        </style>

        <div class="olo-textpath <?php echo esc_attr( $uid ); ?>" id="<?php echo esc_attr( $uid ); ?>">
            <svg viewBox="<?php echo esc_attr( $viewbox ); ?>" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <path id="<?php echo esc_attr( $path_id ); ?>" d="<?php echo esc_attr( $path_d ); ?>" fill="none" />
                </defs>
                <text
                    fill="<?php echo $color; ?>"
                    font-size="<?php echo $fsize; ?>"
                    letter-spacing="<?php echo $spacing; ?>"
                    font-family="inherit"
                >
                    <?php list( $tp_cls, $tp_data ) = $this->tfx_attrs( $s, 'text', wp_strip_all_tags( $s['text'] ?? '' ) ); ?>
                    <textPath
                        href="#<?php echo esc_attr( $path_id ); ?>"
                        startOffset="0%"
                        dominant-baseline="middle"
                        id="<?php echo esc_attr( $uid ); ?>-tp"
                        class="<?php echo trim( $tp_cls ); ?>"
                        <?php echo $tp_data; ?>
                    ><?php echo $text; ?></textPath>
                </text>
            </svg>
        </div>

        <?php if ( $anim !== 'none' ) : ?>
        <script>
        (function(){
            var tp = document.getElementById('<?php echo $uid; ?>-tp');
            if (!tp) { return; }
            var offset = 0;
            var speed = <?php echo $speed; ?>;
            var step = 50 / (speed * 60);
            var isContinuous = <?php echo $anim === 'continuous' ? 'true' : 'false'; ?>;
            var raf;

            function tick() {
                offset += step;
                if (isContinuous) {
                    if (offset > 100) {
                        offset = -50;
                    }
                } else {
                    if (offset > 100) {
                        tp.setAttribute('startOffset', '100%');
                        return;
                    }
                }
                tp.setAttribute('startOffset', offset + '%');
                raf = requestAnimationFrame(tick);
            }

            raf = requestAnimationFrame(tick);
        })();
        </script>
        <?php endif; ?>

        <?php
        $tfx_css = $this->tfx_css( $s, '#' . $uid );
        if ( $tfx_css ) echo '<style>' . $tfx_css . '</style>';
        $this->tfx_print_script();
        return ob_get_clean();
    }
}
