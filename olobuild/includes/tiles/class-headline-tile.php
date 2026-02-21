<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Headline_Tile extends Olo_Tile_Base {

    protected $type     = 'headline';
    protected $name     = 'Titolo';
    protected $icon     = 'dashicons-heading';
    protected $category = 'content';
    protected $defaults = [
        'heading'          => 'Section Headline',
        'subtitle'         => 'A brief subtitle goes here',
        'tag'              => 'h2',
        'alignment'        => 'center',
        'decoration'       => 'line',
        'decoration_color' => '#6366F1',
        'heading_color'    => '#F3F4F6',
        'subtitle_color'   => '#9CA3AF',
        'heading_size'     => 'lg',
    ];

    public function get_controls() {
        return [
            [ 'key' => 'heading',          'type' => 'text',   'label' => 'Heading' ],
            [ 'key' => 'subtitle',         'type' => 'text',   'label' => 'Subtitle' ],
            [ 'key' => 'tag',              'type' => 'select', 'label' => 'Tag' ],
            [ 'key' => 'alignment',        'type' => 'select', 'label' => 'Alignment' ],
            [ 'key' => 'decoration',       'type' => 'select', 'label' => 'Decoration' ],
            [ 'key' => 'decoration_color', 'type' => 'color',  'label' => 'Decoration Color' ],
            [ 'key' => 'heading_color',    'type' => 'color',  'label' => 'Heading Color' ],
            [ 'key' => 'subtitle_color',   'type' => 'color',  'label' => 'Subtitle Color' ],
            [ 'key' => 'heading_size',     'type' => 'select', 'label' => 'Heading Size' ],
        ];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        $size_map = [
            'sm' => 'uk-heading-small',
            'md' => 'uk-heading-medium',
            'lg' => 'uk-heading-large',
            'xl' => 'uk-heading-xlarge',
        ];
        $heading_class = $size_map[ $s['heading_size'] ] ?? 'uk-heading-large';

        $align_class = 'uk-text-' . esc_attr( $s['alignment'] );

        $allowed_tags = [ 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p' ];
        $tag = in_array( $s['tag'], $allowed_tags, true ) ? $s['tag'] : 'h2';

        $deco_class = '';
        if ( $s['decoration'] === 'line' ) {
            $deco_class = 'uk-heading-line';
        } elseif ( $s['decoration'] === 'divider' ) {
            $deco_class = 'uk-heading-divider';
        }

        ob_start();
        ?>
        <?php
            $hd_clr  = $this->safe_color( $s['heading_color'] );
            $dec_clr = $this->safe_color( $s['decoration_color'] );
            $sub_clr = $this->safe_color( $s['subtitle_color'] );
        ?>
        <div class="olo-headline <?php echo $align_class; ?>" style="padding:24px 16px;">
            <?php if ( $s['decoration'] === 'line' ) : ?>
                <<?php echo $tag; ?> class="<?php echo esc_attr( $heading_class ); ?> uk-heading-line" style="margin:0;<?php if ( $hd_clr ) echo 'color:' . $hd_clr . ';'; ?>">
                    <span><?php echo wp_kses_post( $s['heading'] ); ?></span>
                </<?php echo $tag; ?>>
            <?php elseif ( $s['decoration'] === 'divider' ) : ?>
                <<?php echo $tag; ?> class="<?php echo esc_attr( $heading_class ); ?> uk-heading-divider" style="margin:0;<?php if ( $hd_clr ) echo 'color:' . $hd_clr . ';'; ?><?php if ( $dec_clr ) echo 'border-color:' . $dec_clr . ';'; ?>">
                    <?php echo wp_kses_post( $s['heading'] ); ?>
                </<?php echo $tag; ?>>
            <?php else : ?>
                <?php
                $deco = '';
                if ( $s['decoration'] === 'dot' ) {
                    $deco = '<span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:' . ( $dec_clr ?: '#6366F1' ) . ';margin-bottom:12px;"></span><br>';
                } elseif ( $s['decoration'] === 'star' ) {
                    $deco = '<span style="font-size:1.5em;' . ( $dec_clr ? 'color:' . $dec_clr . ';' : '' ) . 'display:block;margin-bottom:8px;">&#x2605;</span>';
                }
                echo $deco;
                ?>
                <<?php echo $tag; ?> class="<?php echo esc_attr( $heading_class ); ?>" style="margin:0;<?php if ( $hd_clr ) echo 'color:' . $hd_clr . ';'; ?>">
                    <?php echo wp_kses_post( $s['heading'] ); ?>
                </<?php echo $tag; ?>>
            <?php endif; ?>

            <?php if ( ! empty( $s['subtitle'] ) ) : ?>
                <p style="margin:12px 0 0;font-size:1em;<?php if ( $sub_clr ) echo 'color:' . $sub_clr . ';'; ?>line-height:1.5;"><?php echo wp_kses_post( $s['subtitle'] ); ?></p>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }
}
