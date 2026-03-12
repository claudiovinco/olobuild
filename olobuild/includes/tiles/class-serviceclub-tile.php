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
            return '<div style="padding:24px;text-align:center;color:var(--olo-color-text-muted, #9CA3AF);background:var(--olo-color-muted, #F3F4F6);border-radius:8px">'
                 . '<p style="margin:0">Inserisci in un template single.</p></div>';
        }

        $pid = $post->ID;
        $pfx = rtrim( $s['meta_prefix'], '_' ) . '_';

        // Try new multi-club array first
        $clubs_raw = get_post_meta( $pid, $pfx . 'clubs', true );
        $clubs = [];

        if ( is_array( $clubs_raw ) && ! empty( $clubs_raw ) ) {
            foreach ( $clubs_raw as $c ) {
                $logo_id  = absint( $c['logo_id'] ?? 0 );
                $logo_url = $logo_id ? wp_get_attachment_image_url( $logo_id, 'medium' ) : '';
                $clubs[] = [
                    'name'     => $c['name'] ?? '',
                    'category' => $c['category'] ?? '',
                    'logo_url' => $logo_url ?: ( $c['logo_url'] ?? '' ),
                    'url'      => $c['url'] ?? '',
                ];
            }
        } else {
            // Fallback: legacy single fields
            $group    = get_post_meta( $pid, $pfx . 'club_group', true ) ?: '';
            $category = get_post_meta( $pid, $pfx . 'club_category', true ) ?: '';
            if ( $group || $category ) {
                $clubs[] = [
                    'name'     => $group,
                    'category' => $category,
                    'logo_url' => '',
                    'url'      => '',
                ];
            }
        }

        if ( empty( $clubs ) ) {
            return '';
        }

        $uid = 'olo-sclub-' . wp_rand( 10000, 99999 );

        $is_horiz   = $s['layout'] === 'horizontal';
        $align_map  = [ 'left' => 'flex-start', 'center' => 'center', 'right' => 'flex-end' ];
        $align      = $align_map[ $s['align'] ] ?? 'flex-start';

        // Override logo_url from settings only if no per-club logos and a global one is set
        $global_logo = ! empty( $s['logo_url'] ) ? $s['logo_url'] : '';

        ob_start();

        foreach ( $clubs as $club ) {
            $logo = $club['logo_url'] ?: $global_logo;

            $wrap_style = 'display:flex;';
            $wrap_style .= $is_horiz ? 'flex-direction:row;align-items:center;' : 'flex-direction:column;align-items:' . $align . ';';
            $wrap_style .= 'gap:' . absint( $s['gap'] ) . 'px;';
            if ( $s['bg_color'] )      $wrap_style .= 'background:' . $this->safe_color_css( $s['bg_color'] ) . ';';
            if ( $s['border_color'] )  $wrap_style .= 'border:1px solid ' . $this->safe_color_css( $s['border_color'] ) . ';';
            if ( $s['border_radius'] ) $wrap_style .= 'border-radius:' . Olo_Tile_Utils::border_radius( $s['border_radius'] ) . ';';
            if ( $s['padding'] )       $wrap_style .= 'padding:' . absint( $s['padding'] ) . 'px;';

            $has_url = ! empty( $club['url'] );
            $tag     = $has_url ? 'a' : 'div';
            $href    = $has_url ? ' href="' . esc_url( $club['url'] ) . '" target="_blank" rel="noopener noreferrer"' : '';
            $link_s  = $has_url ? 'text-decoration:none;color:inherit;display:flex;' : '';
            ?>
            <<?php echo $tag; ?> class="<?php echo esc_attr( $uid ); ?>"<?php echo $href; ?> style="<?php echo $link_s . $wrap_style; ?>">
                <?php if ( $logo ) : ?>
                    <img src="<?php echo esc_url( $logo ); ?>" alt="<?php echo esc_attr( $club['name'] ); ?>"
                         style="width:<?php echo absint( $s['logo_width'] ); ?>px;height:auto;flex-shrink:0" loading="lazy" />
                <?php endif; ?>

                <div style="display:flex;flex-direction:column;gap:2px">
                    <?php if ( $s['show_category'] && $club['category'] ) : ?>
                        <span style="font-size:<?php echo absint( $s['text_size'] ); ?>px;font-weight:<?php echo esc_attr( $s['text_weight'] ); ?>;color:<?php echo $this->safe_color_css( $s['text_color'] ); ?>">
                            <?php echo esc_html( $club['category'] ); ?>
                        </span>
                    <?php endif; ?>

                    <?php if ( $s['show_group'] && $club['name'] ) : ?>
                        <span style="font-size:<?php echo absint( $s['group_size'] ); ?>px;color:<?php echo $this->safe_color_css( $s['group_color'] ); ?>">
                            <?php echo esc_html( $club['name'] ); ?>
                        </span>
                    <?php endif; ?>
                </div>
            </<?php echo $tag; ?>>
            <?php
        }

        return ob_get_clean();
    }
}
