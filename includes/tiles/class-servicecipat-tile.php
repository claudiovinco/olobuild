<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_ServiceCipat_Tile extends Olo_Tile_Base {

    protected $type     = 'servicecipat';
    protected $name     = 'Codice CIPAT';
    protected $icon     = 'dashicons-id';
    protected $category = 'booking';
    protected $defaults = [
        'meta_prefix'    => '_olo_service_',
        'layout'         => 'inline',
        'show_icon'      => true,
        'show_label'     => true,
        'label_text'     => 'Codice CIPAT',
        // Stile
        'font_size'      => 14,
        'code_weight'    => '600',
        'label_color'    => '#6B7280',
        'code_color'     => '',
        'icon_color'     => '#6366F1',
        // Container
        'bg_color'       => '',
        'border_color'   => '',
        'border_radius'  => 8,
        'tile_padding' => ['top' => 12, 'right' => 12, 'bottom' => 12, 'left' => 12],
        'align'          => 'left',
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

        $pid   = $post->ID;
        $pfx   = rtrim( $s['meta_prefix'], '_' ) . '_';
        $cipat = get_post_meta( $pid, $pfx . 'cipat', true );

        if ( empty( $cipat ) ) {
            return '';
        }

        $uid       = 'olo-scipat-' . wp_rand( 10000, 99999 );
        $font_size = max( 10, min( 24, absint( $s['font_size'] ) ) );
        $radius    = Olo_Tile_Utils::border_radius( $s['border_radius'] ?? 0 );
        $radius_hover_css = Olo_Tile_Utils::radius_force_css( $s['border_radius_hover'] ?? null );
        $padding   = absint( $s['padding'] );
        $is_block  = ( $s['layout'] === 'block' );

        $align_map = [ 'left' => 'flex-start', 'center' => 'center', 'right' => 'flex-end' ];
        $justify   = $align_map[ $s['align'] ] ?? 'flex-start';

        // SVG icon: certificate / document
        $icon_svg = '<svg width="' . ( $font_size + 2 ) . '" height="' . ( $font_size + 2 ) . '" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">'
            . '<rect x="3" y="3" width="18" height="18" rx="3" stroke="' . $this->safe_color_css( $s['icon_color'] ) . '" stroke-width="1.8"/>'
            . '<path d="M7 8h10M7 12h6" stroke="' . $this->safe_color_css( $s['icon_color'] ) . '" stroke-width="1.5" stroke-linecap="round"/>'
            . '<circle cx="16.5" cy="15.5" r="2.5" stroke="' . $this->safe_color_css( $s['icon_color'] ) . '" stroke-width="1.5"/>'
            . '<path d="M15.5 17.5L15 20l1.5-1 1.5 1-.5-2.5" stroke="' . $this->safe_color_css( $s['icon_color'] ) . '" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>'
            . '</svg>';

        $wrap_styles = [];
        if ( $s['bg_color'] )     $wrap_styles[] = 'background:' . $this->safe_color_css( $s['bg_color'] );
        if ( $s['border_color'] ) $wrap_styles[] = 'border:1px solid ' . $this->safe_color_css( $s['border_color'] );
        if ( $radius && $radius !== '0px' ) $wrap_styles[] = 'border-radius:' . $radius;
        if ( $padding )           $wrap_styles[] = 'padding:' . $padding . 'px';
        if ( $radius_hover_css !== '' ) $wrap_styles[] = 'transition:border-radius 400ms cubic-bezier(.4,0,.2,1)';

        ob_start();
        ?>
        <?php if ( $radius_hover_css !== '' ) : ?>
        <style>.<?php echo esc_attr( $uid ); ?>:hover{border-radius:<?php echo $radius_hover_css; ?> !important}</style>
        <?php endif; ?>
        <div class="<?php echo esc_attr( $uid ); ?>" style="display:flex;<?php echo $is_block ? 'flex-direction:column;align-items:' . $justify : 'align-items:center;justify-content:' . $justify; ?>;gap:<?php echo $is_block ? '4' : '8'; ?>px;<?php echo implode( ';', $wrap_styles ); ?>">
            <?php if ( ! empty( $s['show_icon'] ) ) : ?>
                <span style="display:flex;align-items:center;flex-shrink:0"><?php echo $icon_svg; ?></span>
            <?php endif; ?>
            <?php if ( $is_block ) : ?>
                <?php if ( ! empty( $s['show_label'] ) ) : ?>
                    <span style="font-size:<?php echo max( 10, $font_size - 2 ); ?>px;color:<?php echo $this->safe_color_css( $s['label_color'] ); ?>;font-weight:500;text-transform:uppercase;letter-spacing:0.5px"><?php echo esc_html( $s['label_text'] ); ?></span>
                <?php endif; ?>
                <span style="font-size:<?php echo $font_size; ?>px;font-weight:<?php echo esc_attr( $s['code_weight'] ); ?>;color:<?php echo $this->safe_color_css( $s['code_color'] ); ?>;letter-spacing:0.3px"><?php echo esc_html( $cipat ); ?></span>
            <?php else : ?>
                <span style="display:flex;flex-direction:column;gap:1px">
                    <?php if ( ! empty( $s['show_label'] ) ) : ?>
                        <span style="font-size:<?php echo max( 10, $font_size - 3 ); ?>px;color:<?php echo $this->safe_color_css( $s['label_color'] ); ?>;font-weight:500;text-transform:uppercase;letter-spacing:0.5px;line-height:1.2"><?php echo esc_html( $s['label_text'] ); ?></span>
                    <?php endif; ?>
                    <span style="font-size:<?php echo $font_size; ?>px;font-weight:<?php echo esc_attr( $s['code_weight'] ); ?>;color:<?php echo $this->safe_color_css( $s['code_color'] ); ?>;letter-spacing:0.3px;line-height:1.3"><?php echo esc_html( $cipat ); ?></span>
                </span>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }
}
