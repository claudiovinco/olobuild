<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Breadcrumbs_Tile extends Olo_Tile_Base {

    protected $type     = 'breadcrumbs';
    protected $name     = 'Breadcrumbs';
    protected $icon     = 'dashicons-arrow-right-alt2';
    protected $category = 'navigation';
    protected $defaults = [
        'preset' => 'custom',
        'separator'    => '/',
        'home_label'   => 'Home',
        'show_home'    => true,
        'show_current' => true,
    ];

    public function get_controls() {
        return [
            [ 'key' => 'separator',    'type' => 'text',   'label' => 'Separatore' ],
            [ 'key' => 'home_label',   'type' => 'text',   'label' => 'Etichetta Home' ],
            [ 'key' => 'show_home',    'type' => 'toggle', 'label' => 'Mostra Home' ],
            [ 'key' => 'show_current', 'type' => 'toggle', 'label' => 'Mostra pagina corrente' ],
        ];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        global $post;

        $separator  = esc_html( $s['separator'] ?: '/' );
        $home_label = esc_html( $s['home_label'] ?: 'Home' );
        $show_home  = ! empty( $s['show_home'] );
        $show_curr  = ! empty( $s['show_current'] );

        $items = [];

        // Home link
        if ( $show_home ) {
            $items[] = '<li><a href="' . esc_url( home_url( '/' ) ) . '">' . $home_label . '</a></li>';
        }

        if ( $post && ! is_front_page() ) {
            // Get ancestors
            $ancestors = get_post_ancestors( $post );
            $ancestors = array_reverse( $ancestors );

            foreach ( $ancestors as $ancestor_id ) {
                $items[] = '<li><a href="' . esc_url( get_permalink( $ancestor_id ) ) . '">' . esc_html( get_the_title( $ancestor_id ) ) . '</a></li>';
            }

            // Current page
            if ( $show_curr ) {
                $items[] = '<li><span aria-current="page">' . esc_html( get_the_title( $post->ID ) ) . '</span></li>';
            }
        } elseif ( is_category() ) {
            $items[] = '<li><span aria-current="page">' . esc_html( single_cat_title( '', false ) ) . '</span></li>';
        } elseif ( is_tag() ) {
            $items[] = '<li><span aria-current="page">' . esc_html( single_tag_title( '', false ) ) . '</span></li>';
        } elseif ( is_search() ) {
            $items[] = '<li><span aria-current="page">' . esc_html__( 'Risultati ricerca', 'olobuild' ) . '</span></li>';
        } elseif ( is_404() ) {
            $items[] = '<li><span aria-current="page">' . esc_html__( 'Pagina non trovata', 'olobuild' ) . '</span></li>';
        }

        if ( empty( $items ) ) {
            return '';
        }

        ob_start();
        ?>
        <nav class="olo-breadcrumbs olo-bc-preset-<?php echo esc_attr( sanitize_key( $s['preset'] ?? 'custom' ) ); ?>" aria-label="<?php echo esc_attr( olo_t( 'Breadcrumb' ) ); ?>">
            <ul class="uk-breadcrumb">
                <?php echo implode( "\n", $items ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- each list item is assembled above exclusively from esc_url()/esc_html()/esc_html__() output and literal markup. ?>
            </ul>
        </nav>
        <style>
            /* a11y tastiera: anello di focus visibile sui link del breadcrumb */
            .olo-breadcrumbs a:focus-visible {
                outline: none;
                box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent);
                border-radius: 3px;
            }
        </style>
        <?php
        return ob_get_clean();
    }
}
