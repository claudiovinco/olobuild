<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_CPT_Service {

    public function init() {
        add_action( 'init', [ __CLASS__, 'register_post_type' ] );
        add_action( 'init', [ __CLASS__, 'register_taxonomy' ] );
        add_filter( 'manage_olo_service_posts_columns', [ $this, 'admin_columns' ] );
        add_action( 'manage_olo_service_posts_custom_column', [ $this, 'admin_column_content' ], 10, 2 );

        // Forza editor classico per olo_service (le metabox non funzionano con Gutenberg)
        add_filter( 'use_block_editor_for_post_type', function ( $use, $post_type ) {
            return $post_type === 'olo_service' ? false : $use;
        }, 10, 2 );
    }

    public static function register_post_type() {
        register_post_type( 'olo_service', [
            'labels' => [
                'name'               => 'Servizi',
                'singular_name'      => 'Servizio',
                'add_new'            => 'Aggiungi servizio',
                'add_new_item'       => 'Aggiungi nuovo servizio',
                'edit_item'          => 'Modifica servizio',
                'new_item'           => 'Nuovo servizio',
                'view_item'          => 'Visualizza servizio',
                'search_items'       => 'Cerca servizi',
                'not_found'          => 'Nessun servizio trovato',
                'not_found_in_trash' => 'Nessun servizio nel cestino',
                'menu_name'          => 'Olo Booking',
            ],
            'public'          => true,
            'show_ui'         => true,
            'show_in_menu'    => true,
            'menu_position'   => 27,
            'menu_icon'       => 'dashicons-clipboard',
            'supports'        => [ 'title', 'editor', 'thumbnail', 'excerpt' ],
            'has_archive'     => false,
            'rewrite'         => [ 'slug' => 'servizio' ],
            'show_in_rest'    => true,
            'capability_type' => 'post',
        ] );
    }

    public static function register_taxonomy() {
        register_taxonomy( 'olo_service_cat', 'olo_service', [
            'labels' => [
                'name'          => 'Categorie Servizi',
                'singular_name' => 'Categoria',
                'add_new_item'  => 'Aggiungi categoria',
                'search_items'  => 'Cerca categorie',
            ],
            'hierarchical' => true,
            'show_ui'      => true,
            'show_in_rest' => true,
            'rewrite'      => false,
        ] );
    }

    public function admin_columns( $columns ) {
        $new = [];
        foreach ( $columns as $key => $val ) {
            $new[ $key ] = $val;
            if ( $key === 'title' ) {
                $new['service_duration'] = 'Durata';
                $new['service_price']    = 'Prezzo';
                $new['service_manager']  = 'Gestore';
            }
        }
        return $new;
    }

    public function admin_column_content( $column, $post_id ) {
        switch ( $column ) {
            case 'service_duration':
                $d = get_post_meta( $post_id, '_olo_service_duration', true );
                echo $d ? esc_html( $d ) . ' min' : '—';
                break;
            case 'service_price':
                $p = get_post_meta( $post_id, '_olo_service_price', true );
                echo $p ? '€ ' . esc_html( number_format( (float) $p, 2, ',', '.' ) ) : '—';
                break;
            case 'service_manager':
                $uid = get_post_meta( $post_id, '_olo_service_manager', true );
                if ( $uid ) {
                    $user = get_userdata( $uid );
                    echo $user ? esc_html( $user->display_name ) : '—';
                } else {
                    echo '—';
                }
                break;
        }
    }
}
