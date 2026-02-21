<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_ServiceClub_Tile extends Olo_Tile_Base {

    protected $type     = 'serviceclub';
    protected $name     = 'Club di Prodotto';
    protected $icon     = 'dashicons-awards';
    protected $category = 'booking';
    protected $defaults = [
        'meta_prefix'    => '_olo_service_',
        'layout'         => 'horizontal',
        'logo_url'       => '',
        'logo_width'     => 80,
        'show_group'     => false,
        'show_category'  => true,
        'text_size'      => 14,
        'text_color'     => '#374151',
        'text_weight'    => '600',
        'group_size'     => 12,
        'group_color'    => '#9CA3AF',
        'gap'            => 12,
        'align'          => 'left',
        'bg_color'       => '',
        'border_color'   => '#E5E7EB',
        'border_radius'  => 10,
        'padding'        => 12,
    ];

    public function get_controls() {
        return [];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        global $post;
        if ( ! $post || ! is_singular() ) {
            return '<div style="padding:24px;text-align:center;color:#9ca3af;background:#f9fafb;border-radius:8px">'
                 . '<p style="margin:0">Inserisci in un template single.</p></div>';
        }

        $pid = $post->ID;
        $pfx = rtrim( $s['meta_prefix'], '_' ) . '_';

        $group    = get_post_meta( $pid, $pfx . 'club_group', true ) ?: '';
        $category = get_post_meta( $pid, $pfx . 'club_category', true ) ?: '';

        if ( ! $group && ! $category ) {
            return '';
        }

        $uid = 'olo-sclub-' . wp_rand( 10000, 99999 );

        $is_horiz   = $s['layout'] === 'horizontal';
        $align_map  = [ 'left' => 'flex-start', 'center' => 'center', 'right' => 'flex-end' ];
        $align      = $align_map[ $s['align'] ] ?? 'flex-start';

        $wrap_style = 'display:flex;';
        $wrap_style .= $is_horiz ? 'flex-direction:row;align-items:center;' : 'flex-direction:column;align-items:' . $align . ';';
        $wrap_style .= 'gap:' . absint( $s['gap'] ) . 'px;';
        if ( $s['bg_color'] )      $wrap_style .= 'background:' . $this->safe_color( $s['bg_color'] ) . ';';
        if ( $s['border_color'] )  $wrap_style .= 'border:1px solid ' . $this->safe_color( $s['border_color'] ) . ';';
        if ( $s['border_radius'] ) $wrap_style .= 'border-radius:' . absint( $s['border_radius'] ) . 'px;';
        if ( $s['padding'] )       $wrap_style .= 'padding:' . absint( $s['padding'] ) . 'px;';

        ob_start();
        ?>
        <div class="<?php echo esc_attr( $uid ); ?>" style="<?php echo $wrap_style; ?>">
            <?php if ( ! empty( $s['logo_url'] ) ) : ?>
                <img src="<?php echo esc_url( $s['logo_url'] ); ?>" alt="<?php echo esc_attr( $group ); ?>"
                     style="width:<?php echo absint( $s['logo_width'] ); ?>px;height:auto;flex-shrink:0" />
            <?php endif; ?>

            <div style="display:flex;flex-direction:column;gap:2px">
                <?php if ( $s['show_category'] && $category ) : ?>
                    <span style="font-size:<?php echo absint( $s['text_size'] ); ?>px;font-weight:<?php echo esc_attr( $s['text_weight'] ); ?>;color:<?php echo esc_attr( $s['text_color'] ); ?>">
                        <?php echo esc_html( $category ); ?>
                    </span>
                <?php endif; ?>

                <?php if ( $s['show_group'] && $group ) : ?>
                    <span style="font-size:<?php echo absint( $s['group_size'] ); ?>px;color:<?php echo esc_attr( $s['group_color'] ); ?>">
                        <?php echo esc_html( $group ); ?>
                    </span>
                <?php endif; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}
