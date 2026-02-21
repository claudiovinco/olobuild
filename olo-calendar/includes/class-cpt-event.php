<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_CPT_Event {

    public function init() {
        add_action( 'init', [ __CLASS__, 'register_post_type' ] );
        add_action( 'init', [ __CLASS__, 'register_taxonomy' ] );

        // Admin columns
        add_filter( 'manage_olo_event_posts_columns', [ $this, 'admin_columns' ] );
        add_action( 'manage_olo_event_posts_custom_column', [ $this, 'admin_column_content' ], 10, 2 );
        add_filter( 'manage_edit-olo_event_sortable_columns', [ $this, 'sortable_columns' ] );
        add_action( 'pre_get_posts', [ $this, 'sort_by_event_date' ] );

        // Taxonomy color field
        add_action( 'olo_event_cat_add_form_fields', [ $this, 'cat_add_color_field' ] );
        add_action( 'olo_event_cat_edit_form_fields', [ $this, 'cat_edit_color_field' ] );
        add_action( 'created_olo_event_cat', [ $this, 'save_cat_color' ] );
        add_action( 'edited_olo_event_cat', [ $this, 'save_cat_color' ] );
    }

    public static function register_post_type() {
        $labels = [
            'name'               => 'Eventi',
            'singular_name'      => 'Evento',
            'add_new'            => 'Aggiungi evento',
            'add_new_item'       => 'Aggiungi nuovo evento',
            'edit_item'          => 'Modifica evento',
            'new_item'           => 'Nuovo evento',
            'view_item'          => 'Visualizza evento',
            'search_items'       => 'Cerca eventi',
            'not_found'          => 'Nessun evento trovato',
            'not_found_in_trash' => 'Nessun evento nel cestino',
            'all_items'          => 'Tutti gli eventi',
            'menu_name'          => 'Olo Calendar',
        ];

        register_post_type( 'olo_event', [
            'labels'       => $labels,
            'public'       => true,
            'has_archive'  => true,
            'rewrite'      => [ 'slug' => 'eventi', 'with_front' => false ],
            'menu_icon'    => 'dashicons-calendar-alt',
            'menu_position' => 31,
            'supports'     => [ 'title', 'editor', 'thumbnail', 'excerpt' ],
            'show_in_rest' => true,
            'taxonomies'   => [ 'olo_event_cat' ],
        ] );
    }

    public static function register_taxonomy() {
        $labels = [
            'name'          => 'Categorie Evento',
            'singular_name' => 'Categoria Evento',
            'search_items'  => 'Cerca categorie',
            'all_items'     => 'Tutte le categorie',
            'edit_item'     => 'Modifica categoria',
            'add_new_item'  => 'Aggiungi categoria',
            'new_item_name' => 'Nome nuova categoria',
            'menu_name'     => 'Categorie',
        ];

        register_taxonomy( 'olo_event_cat', 'olo_event', [
            'labels'       => $labels,
            'hierarchical' => true,
            'public'       => true,
            'rewrite'      => [ 'slug' => 'categoria-evento' ],
            'show_in_rest' => true,
            'show_admin_column' => true,
        ] );
    }

    /* ── Admin columns ── */

    public function admin_columns( $columns ) {
        $new = [];
        foreach ( $columns as $key => $val ) {
            $new[ $key ] = $val;
            if ( $key === 'title' ) {
                $new['event_date']     = 'Data evento';
                $new['event_time']     = 'Orario';
                $new['event_location'] = 'Luogo';
            }
        }
        return $new;
    }

    public function admin_column_content( $column, $post_id ) {
        switch ( $column ) {
            case 'event_date':
                $start = get_post_meta( $post_id, '_olo_event_start_date', true );
                $end   = get_post_meta( $post_id, '_olo_event_end_date', true );
                if ( $start ) {
                    echo esc_html( date_i18n( 'd/m/Y', strtotime( $start ) ) );
                    if ( $end && $end !== $start ) {
                        echo ' &mdash; ' . esc_html( date_i18n( 'd/m/Y', strtotime( $end ) ) );
                    }
                }
                break;
            case 'event_time':
                $all_day = get_post_meta( $post_id, '_olo_event_all_day', true );
                if ( $all_day ) {
                    echo '<em>Tutto il giorno</em>';
                } else {
                    $start_t = get_post_meta( $post_id, '_olo_event_start_time', true );
                    $end_t   = get_post_meta( $post_id, '_olo_event_end_time', true );
                    if ( $start_t ) {
                        echo esc_html( $start_t );
                        if ( $end_t ) echo ' &ndash; ' . esc_html( $end_t );
                    }
                }
                break;
            case 'event_location':
                echo esc_html( get_post_meta( $post_id, '_olo_event_location', true ) );
                break;
        }
    }

    public function sortable_columns( $columns ) {
        $columns['event_date'] = 'event_date';
        return $columns;
    }

    public function sort_by_event_date( $query ) {
        if ( ! is_admin() || ! $query->is_main_query() ) return;
        if ( $query->get( 'orderby' ) === 'event_date' ) {
            $query->set( 'meta_key', '_olo_event_start_date' );
            $query->set( 'orderby', 'meta_value' );
        }
    }

    /* ── Taxonomy color ── */

    public function cat_add_color_field() {
        ?>
        <div class="form-field">
            <label for="event_cat_color">Colore</label>
            <input type="color" name="event_cat_color" id="event_cat_color" value="#6366F1" />
            <p>Colore per gli eventi di questa categoria nel calendario.</p>
        </div>
        <?php
    }

    public function cat_edit_color_field( $term ) {
        $color = get_term_meta( $term->term_id, '_event_color', true ) ?: '#6366F1';
        ?>
        <tr class="form-field">
            <th><label for="event_cat_color">Colore</label></th>
            <td>
                <input type="color" name="event_cat_color" id="event_cat_color" value="<?php echo esc_attr( $color ); ?>" />
                <p class="description">Colore per gli eventi di questa categoria nel calendario.</p>
            </td>
        </tr>
        <?php
    }

    public function save_cat_color( $term_id ) {
        if ( isset( $_POST['event_cat_color'] ) ) {
            update_term_meta( $term_id, '_event_color', sanitize_hex_color( $_POST['event_cat_color'] ) );
        }
    }
}
