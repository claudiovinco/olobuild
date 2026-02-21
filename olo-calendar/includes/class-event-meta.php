<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Event_Meta {

    private $meta_keys = [
        '_olo_event_start_date',
        '_olo_event_start_time',
        '_olo_event_end_date',
        '_olo_event_end_time',
        '_olo_event_all_day',
        '_olo_event_location',
        '_olo_event_location_url',
        '_olo_event_color',
        '_olo_event_url',
    ];

    public function init() {
        add_action( 'add_meta_boxes', [ $this, 'add_meta_box' ] );
        add_action( 'save_post_olo_event', [ $this, 'save_meta' ], 10, 2 );

        // Admin styles/scripts
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin' ] );
    }

    public function add_meta_box() {
        add_meta_box(
            'olo_event_details',
            'Dettagli Evento',
            [ $this, 'render_meta_box' ],
            'olo_event',
            'normal',
            'high'
        );
    }

    public function render_meta_box( $post ) {
        wp_nonce_field( 'olo_event_meta', '_olo_event_nonce' );

        $start_date = get_post_meta( $post->ID, '_olo_event_start_date', true );
        $start_time = get_post_meta( $post->ID, '_olo_event_start_time', true );
        $end_date   = get_post_meta( $post->ID, '_olo_event_end_date', true );
        $end_time   = get_post_meta( $post->ID, '_olo_event_end_time', true );
        $all_day    = get_post_meta( $post->ID, '_olo_event_all_day', true );
        $location   = get_post_meta( $post->ID, '_olo_event_location', true );
        $loc_url    = get_post_meta( $post->ID, '_olo_event_location_url', true );
        $color      = get_post_meta( $post->ID, '_olo_event_color', true );
        $url        = get_post_meta( $post->ID, '_olo_event_url', true );

        // Defaults
        if ( ! $start_date ) $start_date = wp_date( 'Y-m-d' );
        if ( ! $start_time ) $start_time = '09:00';
        if ( ! $end_time )   $end_time   = '10:00';
        ?>
        <div class="olo-event-meta-wrap">
            <div class="olo-em-row">
                <div class="olo-em-field">
                    <label for="olo_start_date">Data inizio</label>
                    <input type="date" id="olo_start_date" name="_olo_event_start_date"
                           value="<?php echo esc_attr( $start_date ); ?>" required />
                </div>
                <div class="olo-em-field olo-em-time" <?php echo $all_day ? 'style="opacity:0.3;pointer-events:none"' : ''; ?>>
                    <label for="olo_start_time">Ora inizio</label>
                    <input type="time" id="olo_start_time" name="_olo_event_start_time"
                           value="<?php echo esc_attr( $start_time ); ?>" />
                </div>
                <div class="olo-em-field">
                    <label for="olo_end_date">Data fine</label>
                    <input type="date" id="olo_end_date" name="_olo_event_end_date"
                           value="<?php echo esc_attr( $end_date ); ?>" />
                </div>
                <div class="olo-em-field olo-em-time" <?php echo $all_day ? 'style="opacity:0.3;pointer-events:none"' : ''; ?>>
                    <label for="olo_end_time">Ora fine</label>
                    <input type="time" id="olo_end_time" name="_olo_event_end_time"
                           value="<?php echo esc_attr( $end_time ); ?>" />
                </div>
                <div class="olo-em-field olo-em-allday">
                    <label>
                        <input type="checkbox" id="olo_all_day" name="_olo_event_all_day" value="1"
                               <?php checked( $all_day, '1' ); ?> />
                        Tutto il giorno
                    </label>
                </div>
            </div>

            <div class="olo-em-row">
                <div class="olo-em-field olo-em-wide">
                    <label for="olo_location">Luogo</label>
                    <input type="text" id="olo_location" name="_olo_event_location"
                           value="<?php echo esc_attr( $location ); ?>"
                           placeholder="es. Centro Congressi, Roma" />
                </div>
                <div class="olo-em-field">
                    <label for="olo_location_url">URL mappa</label>
                    <input type="url" id="olo_location_url" name="_olo_event_location_url"
                           value="<?php echo esc_attr( $loc_url ); ?>"
                           placeholder="Link Google Maps" />
                </div>
            </div>

            <div class="olo-em-row">
                <div class="olo-em-field">
                    <label for="olo_event_color">Colore evento</label>
                    <input type="color" id="olo_event_color" name="_olo_event_color"
                           value="<?php echo esc_attr( $color ?: '#6366F1' ); ?>" />
                    <span class="description">Sovrascrive il colore della categoria</span>
                </div>
                <div class="olo-em-field olo-em-wide">
                    <label for="olo_event_url">URL esterno</label>
                    <input type="url" id="olo_event_url" name="_olo_event_url"
                           value="<?php echo esc_attr( $url ); ?>"
                           placeholder="Link esterno (opzionale)" />
                </div>
            </div>
        </div>
        <?php
    }

    public function save_meta( $post_id, $post ) {
        if ( ! isset( $_POST['_olo_event_nonce'] ) || ! wp_verify_nonce( $_POST['_olo_event_nonce'], 'olo_event_meta' ) ) {
            return;
        }
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
        if ( ! current_user_can( 'edit_post', $post_id ) ) return;

        // Text fields
        $text_fields = [
            '_olo_event_start_date',
            '_olo_event_start_time',
            '_olo_event_end_date',
            '_olo_event_end_time',
            '_olo_event_location',
        ];
        foreach ( $text_fields as $key ) {
            if ( isset( $_POST[ $key ] ) ) {
                update_post_meta( $post_id, $key, sanitize_text_field( $_POST[ $key ] ) );
            }
        }

        // URL fields
        $url_fields = [ '_olo_event_location_url', '_olo_event_url' ];
        foreach ( $url_fields as $key ) {
            if ( isset( $_POST[ $key ] ) ) {
                update_post_meta( $post_id, $key, esc_url_raw( $_POST[ $key ] ) );
            }
        }

        // Color
        if ( isset( $_POST['_olo_event_color'] ) ) {
            update_post_meta( $post_id, '_olo_event_color', sanitize_hex_color( $_POST['_olo_event_color'] ) );
        }

        // Checkbox
        update_post_meta( $post_id, '_olo_event_all_day', isset( $_POST['_olo_event_all_day'] ) ? '1' : '' );
    }

    public function enqueue_admin( $hook ) {
        global $post_type;
        if ( $post_type !== 'olo_event' ) return;
        if ( ! in_array( $hook, [ 'post.php', 'post-new.php' ], true ) ) return;

        wp_enqueue_style(
            'olo-calendar-admin',
            OLO_CAL_URL . 'assets/css/admin.css',
            [],
            OLO_CAL_VERSION
        );
        wp_enqueue_script(
            'olo-calendar-admin-js',
            OLO_CAL_URL . 'assets/js/admin-event.js',
            [],
            OLO_CAL_VERSION,
            true
        );
    }
}
