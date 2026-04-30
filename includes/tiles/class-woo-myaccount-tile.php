<?php
/**
 * WooCommerce My Account tile — customizable account dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Woo_Myaccount_Tile extends Olo_Tile_Base {

    protected $type     = 'woo_myaccount';
    protected $name     = 'WC My Account';
    protected $icon     = 'dashicons-admin-users';
    protected $category = 'woocommerce';
    protected $defaults = [
        'layout'             => 'default',
        'sidebar_bg'         => '#F9FAFB',
        'sidebar_active_bg'  => '#4f46e5',
        'sidebar_active_color' => '#FFFFFF',
        'sidebar_color'      => '#374151',
        'content_bg'         => '#FFFFFF',
        'heading_color'      => '',
        'text_color'         => '#374151',
        'link_color'         => '#4f46e5',
        'button_bg'          => '#4f46e5',
        'button_color'       => '#FFFFFF',
        'border_color'       => '#E5E7EB',
        'border_radius'      => '8',
        'avatar_size'        => '64',
        'show_avatar'        => true,
        'show_greeting'      => true,
    ];

    public function get_controls() {
        return [];
    }

    public function render( $settings ) {
        if ( ! class_exists( 'WooCommerce' ) ) {
            return '<p style="color:var(--olo-color-text-muted, #9CA3AF);text-align:center;padding:40px">WooCommerce non attivo</p>';
        }

        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'olo-wma-' . wp_rand( 10000, 99999 );

        $layout    = in_array( $s['layout'], [ 'default', 'tabs', 'sidebar' ], true ) ? $s['layout'] : 'default';
        $sb_bg     = $this->safe_color_css( $s['sidebar_bg'] ) ?: '#F9FAFB';
        $sb_act_bg = $this->safe_color_css( $s['sidebar_active_bg'] ) ?: '#4f46e5';
        $sb_act_c  = $this->safe_color_css( $s['sidebar_active_color'] ) ?: '#FFFFFF';
        $sb_c      = $this->safe_color_css( $s['sidebar_color'] ) ?: '#374151';
        $cnt_bg    = $this->safe_color_css( $s['content_bg'] ) ?: '#FFFFFF';
        $h_color   = $this->safe_color_css( $s['heading_color'] ) ?: 'var(--olo-color-text, #374151)';
        $t_color   = $this->safe_color_css( $s['text_color'] ) ?: '#374151';
        $l_color   = $this->safe_color_css( $s['link_color'] ) ?: '#4f46e5';
        $btn_bg    = $this->safe_color_css( $s['button_bg'] ) ?: '#4f46e5';
        $btn_c     = $this->safe_color_css( $s['button_color'] ) ?: '#FFFFFF';
        $bdr_c     = $this->safe_color_css( $s['border_color'] ) ?: '#E5E7EB';
        $radius_raw = max( 0, Olo_Tile_Utils::radius_int( $s['border_radius'] ) );
        $radius    = Olo_Tile_Utils::border_radius( $s['border_radius'] ?? 0 );
        $radius_hover_css = Olo_Tile_Utils::radius_force_css( $s['border_radius_hover'] ?? null );
        $avatar_sz = max( 32, intval( $s['avatar_size'] ) );

        ob_start();
        ?>
        <style>
        #<?php echo $uid; ?> .woocommerce-MyAccount-navigation{background:<?php echo $sb_bg; ?>;border-radius:<?php echo $radius; ?>;padding:20px}
        #<?php echo $uid; ?> .woocommerce-MyAccount-navigation ul{list-style:none;margin:0;padding:0}
        #<?php echo $uid; ?> .woocommerce-MyAccount-navigation li{margin:0}
        #<?php echo $uid; ?> .woocommerce-MyAccount-navigation li a{display:block;padding:10px 16px;color:<?php echo $sb_c; ?>;text-decoration:none;border-radius:<?php echo max(0,$radius_raw-4); ?>px;transition:all .2s}
        #<?php echo $uid; ?> .woocommerce-MyAccount-navigation li.is-active a,
        #<?php echo $uid; ?> .woocommerce-MyAccount-navigation li a:hover{background:<?php echo $sb_act_bg; ?>;color:<?php echo $sb_act_c; ?>}
        #<?php echo $uid; ?> .woocommerce-MyAccount-content{background:<?php echo $cnt_bg; ?>;border-radius:<?php echo $radius; ?>;padding:24px;border:1px solid <?php echo $bdr_c; ?>}
        #<?php echo $uid; ?> .woocommerce-MyAccount-content h2,
        #<?php echo $uid; ?> .woocommerce-MyAccount-content h3{color:<?php echo $h_color; ?>}
        #<?php echo $uid; ?> .woocommerce-MyAccount-content{color:<?php echo $t_color; ?>}
        #<?php echo $uid; ?> .woocommerce-MyAccount-content a{color:<?php echo $l_color; ?>}
        #<?php echo $uid; ?> .woocommerce-MyAccount-content .button,
        #<?php echo $uid; ?> .woocommerce-MyAccount-content button[type="submit"]{background:<?php echo $btn_bg; ?>;color:<?php echo $btn_c; ?>;border:none;border-radius:<?php echo max(0,$radius_raw-2); ?>px;padding:10px 24px;cursor:pointer;transition:opacity .2s}
        #<?php echo $uid; ?> .woocommerce-MyAccount-content .button:hover{opacity:.85}
        <?php if ( $layout === 'sidebar' ) : ?>
        #<?php echo $uid; ?> .woocommerce{display:grid;grid-template-columns:260px 1fr;gap:24px;align-items:start}
        @media(max-width:768px){#<?php echo $uid; ?> .woocommerce{grid-template-columns:1fr}}
        <?php endif; ?>
        <?php if ( $layout === 'tabs' ) : ?>
        #<?php echo $uid; ?> .woocommerce-MyAccount-navigation{background:transparent;padding:0;border-bottom:2px solid <?php echo $bdr_c; ?>;margin-bottom:20px;border-radius:0}
        #<?php echo $uid; ?> .woocommerce-MyAccount-navigation ul{display:flex;gap:4px;flex-wrap:wrap}
        #<?php echo $uid; ?> .woocommerce-MyAccount-navigation li a{border-radius:<?php echo $radius; ?>;border-bottom-left-radius:0;border-bottom-right-radius:0;padding:10px 20px}
        <?php endif; ?>
        <?php if ( $radius_hover_css !== '' ) : ?>
        #<?php echo $uid; ?> .woocommerce-MyAccount-navigation,#<?php echo $uid; ?> .woocommerce-MyAccount-content{transition:border-radius 400ms cubic-bezier(.4,0,.2,1)}
        #<?php echo $uid; ?> .woocommerce-MyAccount-navigation:hover,#<?php echo $uid; ?> .woocommerce-MyAccount-content:hover{border-radius:<?php echo $radius_hover_css; ?> !important}
        <?php endif; ?>
        </style>

        <div id="<?php echo esc_attr( $uid ); ?>" class="olo-woo-myaccount">
            <?php
            if ( ! empty( $s['show_greeting'] ) ) {
                $user = wp_get_current_user();
                if ( $user->ID ) {
                    echo '<div style="display:flex;align-items:center;gap:16px;margin-bottom:24px">';
                    if ( ! empty( $s['show_avatar'] ) ) {
                        echo get_avatar( $user->ID, $avatar_sz, '', '', [ 'style' => 'border-radius:50%' ] );
                    }
                    echo '<div>';
                    echo '<p style="margin:0;font-size:18px;font-weight:600;color:' . $h_color . '">Ciao, ' . esc_html( $user->display_name ) . '</p>';
                    echo '<p style="margin:4px 0 0;font-size:14px;color:' . $t_color . ';opacity:.7">' . esc_html( $user->user_email ) . '</p>';
                    echo '</div></div>';
                }
            }

            echo do_shortcode( '[woocommerce_my_account]' );
            ?>
        </div>
        <?php
        return ob_get_clean();
    }
}
