<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_ServiceMushrooms_Tile extends Olo_Tile_Base {

    protected $type     = 'servicemushrooms';
    protected $name     = 'Funghi Baita';
    protected $icon     = 'dashicons-star-filled';
    protected $category = 'booking';
    protected $defaults = [
        'meta_prefix'    => '_olo_service_',
        'style'          => 'inline',
        'size'           => 28,
        'gap'            => 4,
        'active_color'   => '#D97706',
        'inactive_color' => '#E5E7EB',
        'show_label'     => true,
        'label_text'     => 'Classificazione comfort',
        'label_size'     => 13,
        'label_color'    => '#6B7280',
        'label_position' => 'top',
        'align'          => 'left',
        'bg_color'       => '',
        'border_radius'  => 0,
        'tile_padding' => ['top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0],
    ];

    public function get_controls() {
        return [];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        global $post;
        if ( ! $post || ! is_singular() ) {
            return '<div style="padding:24px;text-align:center;color:var(--olo-color-text-muted, #9CA3AF);background:var(--olo-color-muted, #F3F4F6);border-radius:8px">'
                 . '<p style="margin:0">Inserisci in un template single.</p></div>';
        }

        $pid = $post->ID;
        $pfx = rtrim( $s['meta_prefix'], '_' ) . '_';

        $mushrooms = (int) get_post_meta( $pid, $pfx . 'mushrooms', true );
        if ( $mushrooms < 1 ) {
            return '';
        }
        $mushrooms = min( 5, $mushrooms );

        $uid  = 'olo-smush-' . wp_rand( 10000, 99999 );
        $size = absint( $s['size'] ) ?: 28;
        $gap  = absint( $s['gap'] );

        $align_map = [ 'left' => 'flex-start', 'center' => 'center', 'right' => 'flex-end' ];
        $justify   = $align_map[ $s['align'] ] ?? 'flex-start';

        $wrap_style = "display:flex;flex-direction:column;align-items:{$justify};";
        if ( $s['bg_color'] )      $wrap_style .= "background:{$this->safe_color_css($s['bg_color'])};";
        if ( $s['border_radius'] ) $wrap_style .= "border-radius:{$s['border_radius']}px;";
        if ( $s['padding'] )       $wrap_style .= "padding:{$s['padding']}px;";

        ob_start();
        ?>
        <div class="<?php echo esc_attr( $uid ); ?>" style="<?php echo $wrap_style; ?>">
            <?php if ( ! empty( $s['show_label'] ) && $s['label_position'] === 'top' ) : ?>
                <div style="font-size:<?php echo absint( $s['label_size'] ); ?>px;color:<?php echo $this->safe_color_css( $s['label_color'] ); ?>;font-weight:600;margin-bottom:6px">
                    <?php echo esc_html( $s['label_text'] ); ?>
                </div>
            <?php endif; ?>

            <div style="display:flex;align-items:center;gap:<?php echo $gap; ?>px">
                <?php for ( $i = 1; $i <= 5; $i++ ) :
                    $active = $i <= $mushrooms;
                    $color  = $active ? $this->safe_color_css( $s['active_color'] ) : $this->safe_color_css( $s['inactive_color'] );
                ?>
                    <svg width="<?php echo $size; ?>" height="<?php echo $size; ?>" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2C8 2 4 5.5 4 9.5C4 13 7 14.5 7 14.5L7.5 20C7.5 20.5 8 21 8.5 21H15.5C16 21 16.5 20.5 16.5 20L17 14.5C17 14.5 20 13 20 9.5C20 5.5 16 2 12 2Z" fill="<?php echo $color; ?>" />
                        <path d="M10 14.5H14" stroke="<?php echo $active ? '#fff' : '#D1D5DB'; ?>" stroke-width="1" stroke-linecap="round" opacity="0.5" />
                        <path d="M10.5 17H13.5" stroke="<?php echo $active ? '#fff' : '#D1D5DB'; ?>" stroke-width="1" stroke-linecap="round" opacity="0.3" />
                    </svg>
                <?php endfor; ?>

                <?php if ( $s['style'] === 'with-number' ) : ?>
                    <span style="font-size:<?php echo $size; ?>px;font-weight:700;color:<?php echo $this->safe_color_css( $s['active_color'] ); ?>;margin-left:4px"><?php echo $mushrooms; ?>/5</span>
                <?php endif; ?>
            </div>

            <?php if ( ! empty( $s['show_label'] ) && $s['label_position'] === 'bottom' ) : ?>
                <div style="font-size:<?php echo absint( $s['label_size'] ); ?>px;color:<?php echo $this->safe_color_css( $s['label_color'] ); ?>;font-weight:600;margin-top:6px">
                    <?php echo esc_html( $s['label_text'] ); ?>
                </div>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }
}
