<?php
/**
 * Olo Booking — Service Meta Box (struttura baita/alloggio)
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Service_Meta {

    public function init() {
        add_action( 'add_meta_boxes', [ $this, 'add_meta_boxes' ] );
        add_action( 'save_post_olo_service', [ $this, 'save_meta' ], 10, 2 );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
    }

    public function add_meta_boxes() {
        add_meta_box(
            'olo_service_settings',
            'Informazioni Struttura',
            [ $this, 'render_settings_box' ],
            'olo_service',
            'normal',
            'high'
        );
        add_meta_box(
            'olo_service_seasons',
            'Stagioni e Tariffe',
            [ $this, 'render_seasons_box' ],
            'olo_service',
            'normal',
            'default'
        );
        add_meta_box(
            'olo_service_closures',
            'Chiusure e Blocchi',
            [ $this, 'render_closures_box' ],
            'olo_service',
            'normal',
            'default'
        );
    }

    public function enqueue_assets( $hook ) {
        global $post_type;
        if ( $post_type !== 'olo_service' ) return;
        if ( ! in_array( $hook, [ 'post.php', 'post-new.php' ], true ) ) return;

        wp_enqueue_media();

        wp_enqueue_style(
            'olo-book-admin-service',
            OLO_BOOK_URL . 'assets/css/admin-service.css',
            [],
            OLO_BOOK_VERSION
        );
        wp_enqueue_script(
            'olo-book-admin-service',
            OLO_BOOK_URL . 'assets/js/admin-service.js',
            [ 'jquery' ],
            OLO_BOOK_VERSION,
            true
        );
    }

    /* =========================================================================
     *  META BOX 1 — Informazioni Struttura
     * ======================================================================= */

    public function render_settings_box( $post ) {
        wp_nonce_field( 'olo_service_meta', 'olo_service_nonce' );

        $id = $post->ID;

        // Dati struttura
        $price_base  = get_post_meta( $id, '_olo_service_price', true ) ?: '';
        $capacity    = get_post_meta( $id, '_olo_service_capacity', true ) ?: '';
        $bedrooms    = get_post_meta( $id, '_olo_service_bedrooms', true ) ?: '';
        $beds        = get_post_meta( $id, '_olo_service_beds', true ) ?: '';
        $bathrooms   = get_post_meta( $id, '_olo_service_bathrooms', true ) ?: '';
        $sqm         = get_post_meta( $id, '_olo_service_sqm', true ) ?: '';

        // Check-in / Check-out
        $checkin_time  = get_post_meta( $id, '_olo_service_checkin_time', true ) ?: '15:00';
        $checkout_time = get_post_meta( $id, '_olo_service_checkout_time', true ) ?: '10:00';
        $checkin_day   = get_post_meta( $id, '_olo_service_checkin_day', true ) ?: '';

        // Gestione
        $service_type = get_post_meta( $id, '_olo_service_type', true ) ?: 'service';
        $manager_id = get_post_meta( $id, '_olo_service_manager', true ) ?: '';
        $color      = get_post_meta( $id, '_olo_service_color', true ) ?: '#6366F1';
        $opening    = get_post_meta( $id, '_olo_service_opening', true ) ?: '';
        $altitude   = get_post_meta( $id, '_olo_service_altitude', true ) ?: '';
        $mushrooms  = get_post_meta( $id, '_olo_service_mushrooms', true ) ?: '';
        $cipat      = get_post_meta( $id, '_olo_service_cipat', true ) ?: '';
        $address    = get_post_meta( $id, '_olo_service_address', true ) ?: '';
        $directions = get_post_meta( $id, '_olo_service_directions', true ) ?: '';
        $rules      = get_post_meta( $id, '_olo_service_rules', true ) ?: '';

        // Video
        $video_1 = get_post_meta( $id, '_olo_service_video_1', true ) ?: '';
        $video_2 = get_post_meta( $id, '_olo_service_video_2', true ) ?: '';
        $video_3 = get_post_meta( $id, '_olo_service_video_3', true ) ?: '';

        // Gallery
        $gallery_ids = get_post_meta( $id, '_olo_service_gallery', true );
        if ( is_string( $gallery_ids ) ) {
            $gallery_ids = maybe_unserialize( $gallery_ids );
        }
        if ( ! is_array( $gallery_ids ) ) {
            $gallery_ids = [];
        }

        $users = get_users( [
            'role__in' => [ 'administrator', 'olo_supervisor', 'olo_manager' ],
            'orderby'  => 'display_name',
        ] );

        $days_of_week = [
            ''    => '— Qualsiasi giorno —',
            'mon' => 'Lunedì',
            'tue' => 'Martedì',
            'wed' => 'Mercoledì',
            'thu' => 'Giovedì',
            'fri' => 'Venerdì',
            'sat' => 'Sabato',
            'sun' => 'Domenica',
        ];
        ?>
        <div class="olo-svc-settings">
            <!-- Sezione: Struttura -->
            <h4 class="olo-svc-section-title">🏠 Struttura e Capienza</h4>
            <div class="olo-svc-row">
                <div class="olo-svc-field">
                    <label>Prezzo base a notte (€)</label>
                    <input type="text" name="olo_service_price" value="<?php echo esc_attr( $price_base ); ?>" placeholder="es. 120.00" />
                </div>
                <div class="olo-svc-field">
                    <label>Capienza (max ospiti)</label>
                    <input type="number" name="olo_service_capacity" value="<?php echo esc_attr( $capacity ); ?>" min="1" max="50" />
                </div>
                <div class="olo-svc-field">
                    <label>Camere da letto</label>
                    <input type="number" name="olo_service_bedrooms" value="<?php echo esc_attr( $bedrooms ); ?>" min="0" max="20" />
                </div>
                <div class="olo-svc-field">
                    <label>Posti letto</label>
                    <input type="number" name="olo_service_beds" value="<?php echo esc_attr( $beds ); ?>" min="0" max="50" />
                </div>
            </div>
            <div class="olo-svc-row">
                <div class="olo-svc-field">
                    <label>Bagni</label>
                    <input type="number" name="olo_service_bathrooms" value="<?php echo esc_attr( $bathrooms ); ?>" min="0" max="10" />
                </div>
                <div class="olo-svc-field">
                    <label>Superficie (m²)</label>
                    <input type="number" name="olo_service_sqm" value="<?php echo esc_attr( $sqm ); ?>" min="0" max="1000" />
                </div>
                <div class="olo-svc-field">
                    <label>Altitudine (m s.l.m.)</label>
                    <input type="number" name="olo_service_altitude" value="<?php echo esc_attr( $altitude ); ?>" min="0" max="5000" />
                </div>
                <div class="olo-svc-field">
                    <label>Apertura</label>
                    <select name="olo_service_opening">
                        <option value="">— Non specificato —</option>
                        <option value="Apertura annuale" <?php selected( $opening, 'Apertura annuale' ); ?>>Apertura annuale</option>
                        <option value="Apertura stagionale" <?php selected( $opening, 'Apertura stagionale' ); ?>>Apertura stagionale</option>
                    </select>
                </div>
                <div class="olo-svc-field">
                    <label>Classificazione funghi</label>
                    <select name="olo_service_mushrooms">
                        <option value="">— Non classificata —</option>
                        <?php for ( $i = 1; $i <= 5; $i++ ) : ?>
                            <option value="<?php echo $i; ?>" <?php selected( $mushrooms, (string) $i ); ?>><?php echo str_repeat( '🍄', $i ); ?> (<?php echo $i; ?>)</option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>

            <!-- Sezione: Check-in / Check-out -->
            <h4 class="olo-svc-section-title">🔑 Check-in / Check-out</h4>
            <div class="olo-svc-row">
                <div class="olo-svc-field">
                    <label>Orario check-in (dalle)</label>
                    <input type="time" name="olo_service_checkin_time" value="<?php echo esc_attr( $checkin_time ); ?>" />
                </div>
                <div class="olo-svc-field">
                    <label>Orario check-out (entro le)</label>
                    <input type="time" name="olo_service_checkout_time" value="<?php echo esc_attr( $checkout_time ); ?>" />
                </div>
                <div class="olo-svc-field">
                    <label>Giorno preferito check-in</label>
                    <select name="olo_service_checkin_day">
                        <?php foreach ( $days_of_week as $val => $label ) : ?>
                            <option value="<?php echo esc_attr( $val ); ?>" <?php selected( $checkin_day, $val ); ?>><?php echo esc_html( $label ); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <p class="description">Se impostato, nelle stagioni con "solo settimane intere" il check-in sarà possibile solo in questo giorno.</p>
                </div>
            </div>

            <!-- Sezione: Gestione -->
            <h4 class="olo-svc-section-title">⚙️ Gestione</h4>
            <div class="olo-svc-row">
                <div class="olo-svc-field">
                    <label>Tipo struttura</label>
                    <select name="olo_service_type">
                        <option value="accommodation" <?php selected( $service_type, 'accommodation' ); ?>>Baita / Alloggio</option>
                        <option value="service" <?php selected( $service_type, 'service' ); ?>>Servizio generico</option>
                    </select>
                </div>
                <div class="olo-svc-field">
                    <label>Gestore assegnato</label>
                    <select name="olo_service_manager">
                        <option value="">— Nessuno —</option>
                        <?php foreach ( $users as $u ) : ?>
                            <option value="<?php echo esc_attr( $u->ID ); ?>" <?php selected( $manager_id, $u->ID ); ?>>
                                <?php echo esc_html( $u->display_name ); ?>
                                (<?php echo esc_html( implode( ', ', $u->roles ) ); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="olo-svc-field">
                    <label>Colore calendario</label>
                    <input type="color" name="olo_service_color" value="<?php echo esc_attr( $color ); ?>" />
                </div>
                <div class="olo-svc-field">
                    <label>Codice CIPAT</label>
                    <input type="text" name="olo_service_cipat" value="<?php echo esc_attr( $cipat ); ?>" placeholder="es. 022059-AT-050978" />
                </div>
            </div>

            <!-- Sezione: Indirizzo e info -->
            <h4 class="olo-svc-section-title">📍 Posizione e Informazioni</h4>
            <div class="olo-svc-row">
                <div class="olo-svc-field" style="flex:1">
                    <label>Indirizzo completo</label>
                    <input type="text" name="olo_service_address" value="<?php echo esc_attr( $address ); ?>" placeholder="Via Roma 12, 38032 Canazei (TN)" style="width:100%" />
                </div>
            </div>
            <div class="olo-svc-row">
                <div class="olo-svc-field" style="flex:1">
                    <label>Come arrivare</label>
                    <textarea name="olo_service_directions" rows="4" style="width:100%" placeholder="Indicazioni stradali, mezzi pubblici, punti di riferimento..."><?php echo esc_textarea( $directions ); ?></textarea>
                </div>
            </div>
            <div class="olo-svc-row">
                <div class="olo-svc-field" style="flex:1">
                    <label>Regole della struttura</label>
                    <textarea name="olo_service_rules" rows="4" style="width:100%" placeholder="Orari silenziosi, animali, fumo, cauzione..."><?php echo esc_textarea( $rules ); ?></textarea>
                </div>
            </div>

            <!-- Sezione: Video -->
            <h4 class="olo-svc-section-title">🎬 Video</h4>
            <div class="olo-svc-row">
                <div class="olo-svc-field" style="flex:1">
                    <label>Video 1 (URL MP4 / YouTube / Vimeo)</label>
                    <input type="url" name="olo_service_video_1" value="<?php echo esc_attr( $video_1 ); ?>" placeholder="https://..." style="width:100%" />
                </div>
                <div class="olo-svc-field" style="flex:1">
                    <label>Video 2</label>
                    <input type="url" name="olo_service_video_2" value="<?php echo esc_attr( $video_2 ); ?>" placeholder="https://..." style="width:100%" />
                </div>
                <div class="olo-svc-field" style="flex:1">
                    <label>Video 3</label>
                    <input type="url" name="olo_service_video_3" value="<?php echo esc_attr( $video_3 ); ?>" placeholder="https://..." style="width:100%" />
                </div>
            </div>

            <!-- Sezione: Galleria -->
            <div class="olo-svc-gallery-section">
                <h4 class="olo-svc-section-title" style="border:0;padding-top:0;margin-top:4px">📷 Galleria foto</h4>
                <div class="olo-svc-gallery-grid" id="olo-svc-gallery-grid">
                    <?php foreach ( $gallery_ids as $att_id ) :
                        $thumb = wp_get_attachment_image_url( $att_id, 'thumbnail' );
                        if ( ! $thumb ) continue;
                    ?>
                    <div class="olo-svc-gallery-item" data-id="<?php echo (int) $att_id; ?>">
                        <img src="<?php echo esc_url( $thumb ); ?>" alt="" />
                        <button type="button" class="olo-svc-gallery-remove" title="Rimuovi">&times;</button>
                        <input type="hidden" name="olo_service_gallery[]" value="<?php echo (int) $att_id; ?>" />
                    </div>
                    <?php endforeach; ?>
                </div>
                <button type="button" class="button" id="olo-svc-gallery-add">+ Aggiungi foto</button>
                <p class="description" style="margin-top:4px">Trascina per riordinare. Le foto verranno mostrate nel tile Galleria Servizio.</p>
            </div>
        </div>
        <?php
    }

    /* =========================================================================
     *  META BOX 2 — Stagioni e Tariffe
     * ======================================================================= */

    public function render_seasons_box( $post ) {
        $seasons = get_post_meta( $post->ID, '_olo_service_seasons', true );
        if ( ! $seasons || ! is_array( $seasons ) ) {
            $seasons = self::default_seasons();
        }
        ?>
        <div class="olo-svc-seasons">
            <p class="description">Definisci i periodi stagionali con le relative tariffe e vincoli di soggiorno. Le date non coperte da nessuna stagione useranno il prezzo base.</p>
            <div id="olo-svc-seasons-list">
                <?php foreach ( $seasons as $i => $season ) : ?>
                    <?php $this->render_season_row( $i, $season ); ?>
                <?php endforeach; ?>
            </div>
            <button type="button" class="button" id="olo-svc-add-season" style="margin-top:10px">+ Aggiungi stagione</button>
        </div>
        <?php
    }

    private function render_season_row( $index, $season ) {
        $s = wp_parse_args( $season, [
            'name'         => '',
            'date_from'    => '',
            'date_to'      => '',
            'price_night'  => '',
            'min_nights'   => '1',
            'week_only'    => '0',
            'note'         => '',
        ] );
        $prefix = 'olo_seasons[' . (int) $index . ']';
        ?>
        <div class="olo-svc-season-row" data-index="<?php echo (int) $index; ?>">
            <div class="olo-svc-season-header">
                <span class="olo-svc-season-drag" title="Trascina per riordinare">☰</span>
                <strong class="olo-svc-season-name-preview"><?php echo esc_html( $s['name'] ?: 'Nuova stagione' ); ?></strong>
                <span class="olo-svc-season-dates-preview">
                    <?php if ( $s['date_from'] && $s['date_to'] ) echo esc_html( $s['date_from'] . ' → ' . $s['date_to'] ); ?>
                </span>
                <button type="button" class="button button-small olo-svc-season-toggle">▼</button>
                <button type="button" class="button button-small olo-svc-remove-season" title="Rimuovi stagione">&times;</button>
            </div>
            <div class="olo-svc-season-body">
                <div class="olo-svc-row">
                    <div class="olo-svc-field">
                        <label>Nome stagione</label>
                        <input type="text" name="<?php echo $prefix; ?>[name]" value="<?php echo esc_attr( $s['name'] ); ?>" placeholder="es. Alta stagione" class="olo-svc-season-name-input" />
                    </div>
                    <div class="olo-svc-field">
                        <label>Dal</label>
                        <input type="date" name="<?php echo $prefix; ?>[date_from]" value="<?php echo esc_attr( $s['date_from'] ); ?>" />
                    </div>
                    <div class="olo-svc-field">
                        <label>Al</label>
                        <input type="date" name="<?php echo $prefix; ?>[date_to]" value="<?php echo esc_attr( $s['date_to'] ); ?>" />
                    </div>
                </div>
                <div class="olo-svc-row">
                    <div class="olo-svc-field">
                        <label>Prezzo a notte (€)</label>
                        <input type="text" name="<?php echo $prefix; ?>[price_night]" value="<?php echo esc_attr( $s['price_night'] ); ?>" placeholder="es. 150.00" />
                        <p class="description">Lascia vuoto per usare il prezzo base.</p>
                    </div>
                    <div class="olo-svc-field">
                        <label>Soggiorno minimo (notti)</label>
                        <input type="number" name="<?php echo $prefix; ?>[min_nights]" value="<?php echo esc_attr( $s['min_nights'] ); ?>" min="1" max="30" />
                    </div>
                    <div class="olo-svc-field">
                        <label>Solo settimane intere</label>
                        <label class="olo-svc-checkbox-label">
                            <input type="checkbox" name="<?php echo $prefix; ?>[week_only]" value="1" <?php checked( $s['week_only'], '1' ); ?> />
                            Obbligatorio soggiorno di 7 notti (sabato-sabato o giorno configurato)
                        </label>
                    </div>
                </div>
                <div class="olo-svc-row">
                    <div class="olo-svc-field" style="flex:1">
                        <label>Note (opzionale)</label>
                        <input type="text" name="<?php echo $prefix; ?>[note]" value="<?php echo esc_attr( $s['note'] ); ?>" placeholder="es. Capodanno minimo 3 notti" style="width:100%" />
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    public static function default_seasons() {
        return [
            [
                'name'        => 'Bassa stagione',
                'date_from'   => '',
                'date_to'     => '',
                'price_night' => '',
                'min_nights'  => '2',
                'week_only'   => '0',
                'note'        => '',
            ],
            [
                'name'        => 'Alta stagione',
                'date_from'   => '',
                'date_to'     => '',
                'price_night' => '',
                'min_nights'  => '7',
                'week_only'   => '1',
                'note'        => '',
            ],
        ];
    }

    /* =========================================================================
     *  META BOX 3 — Chiusure e Blocchi
     * ======================================================================= */

    public function render_closures_box( $post ) {
        $closures = get_post_meta( $post->ID, '_olo_service_closures', true );
        if ( ! $closures || ! is_array( $closures ) ) {
            $closures = [];
        }
        ?>
        <div class="olo-svc-closures">
            <p class="description">Periodi in cui la struttura non è disponibile per prenotazioni (manutenzione, uso privato, chiusura stagionale, ecc.).</p>
            <div id="olo-svc-closures-list">
                <?php foreach ( $closures as $i => $closure ) : ?>
                    <?php $this->render_closure_row( $i, $closure ); ?>
                <?php endforeach; ?>
            </div>
            <button type="button" class="button" id="olo-svc-add-closure" style="margin-top:10px">+ Aggiungi periodo chiusura</button>
        </div>
        <?php
    }

    private function render_closure_row( $index, $closure ) {
        $c = wp_parse_args( $closure, [
            'date_from' => '',
            'date_to'   => '',
            'reason'    => '',
        ] );
        $prefix = 'olo_closures[' . (int) $index . ']';
        ?>
        <div class="olo-svc-closure-row">
            <input type="date" name="<?php echo $prefix; ?>[date_from]" value="<?php echo esc_attr( $c['date_from'] ); ?>" />
            <span>→</span>
            <input type="date" name="<?php echo $prefix; ?>[date_to]" value="<?php echo esc_attr( $c['date_to'] ); ?>" />
            <input type="text" name="<?php echo $prefix; ?>[reason]" value="<?php echo esc_attr( $c['reason'] ); ?>" placeholder="Motivo (opzionale)" style="flex:1" />
            <button type="button" class="button button-small olo-svc-remove-closure" title="Rimuovi">&times;</button>
        </div>
        <?php
    }

    /* =========================================================================
     *  SAVE
     * ======================================================================= */

    public function save_meta( $post_id, $post ) {
        if ( ! isset( $_POST['olo_service_nonce'] ) || ! wp_verify_nonce( $_POST['olo_service_nonce'], 'olo_service_meta' ) ) return;
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

        // Campi semplici (sanitize_text_field)
        $text_fields = [
            'olo_service_price'          => '_olo_service_price',
            'olo_service_capacity'       => '_olo_service_capacity',
            'olo_service_bedrooms'       => '_olo_service_bedrooms',
            'olo_service_beds'           => '_olo_service_beds',
            'olo_service_bathrooms'      => '_olo_service_bathrooms',
            'olo_service_sqm'            => '_olo_service_sqm',
            'olo_service_checkin_time'   => '_olo_service_checkin_time',
            'olo_service_checkout_time'  => '_olo_service_checkout_time',
            'olo_service_checkin_day'    => '_olo_service_checkin_day',
            'olo_service_type'           => '_olo_service_type',
            'olo_service_manager'        => '_olo_service_manager',
            'olo_service_color'          => '_olo_service_color',
            'olo_service_opening'        => '_olo_service_opening',
            'olo_service_altitude'       => '_olo_service_altitude',
            'olo_service_mushrooms'      => '_olo_service_mushrooms',
            'olo_service_cipat'          => '_olo_service_cipat',
            'olo_service_address'        => '_olo_service_address',
            'olo_service_video_1'        => '_olo_service_video_1',
            'olo_service_video_2'        => '_olo_service_video_2',
            'olo_service_video_3'        => '_olo_service_video_3',
        ];
        foreach ( $text_fields as $input => $meta ) {
            if ( isset( $_POST[ $input ] ) ) {
                update_post_meta( $post_id, $meta, sanitize_text_field( wp_unslash( $_POST[ $input ] ) ) );
            }
        }

        // Textarea (wp_kses_post)
        $textarea_fields = [
            'olo_service_directions' => '_olo_service_directions',
            'olo_service_rules'      => '_olo_service_rules',
        ];
        foreach ( $textarea_fields as $input => $meta ) {
            if ( isset( $_POST[ $input ] ) ) {
                update_post_meta( $post_id, $meta, wp_kses_post( wp_unslash( $_POST[ $input ] ) ) );
            }
        }

        // Gallery
        if ( isset( $_POST['olo_service_gallery'] ) && is_array( $_POST['olo_service_gallery'] ) ) {
            $gallery = array_map( 'absint', $_POST['olo_service_gallery'] );
            $gallery = array_filter( $gallery );
            update_post_meta( $post_id, '_olo_service_gallery', $gallery );
        } else {
            update_post_meta( $post_id, '_olo_service_gallery', [] );
        }

        // Stagioni
        $seasons = [];
        if ( isset( $_POST['olo_seasons'] ) && is_array( $_POST['olo_seasons'] ) ) {
            foreach ( $_POST['olo_seasons'] as $season_data ) {
                if ( empty( $season_data['name'] ) && empty( $season_data['date_from'] ) ) continue;
                $seasons[] = [
                    'name'        => sanitize_text_field( $season_data['name'] ?? '' ),
                    'date_from'   => sanitize_text_field( $season_data['date_from'] ?? '' ),
                    'date_to'     => sanitize_text_field( $season_data['date_to'] ?? '' ),
                    'price_night' => sanitize_text_field( $season_data['price_night'] ?? '' ),
                    'min_nights'  => sanitize_text_field( $season_data['min_nights'] ?? '1' ),
                    'week_only'   => ! empty( $season_data['week_only'] ) ? '1' : '0',
                    'note'        => sanitize_text_field( $season_data['note'] ?? '' ),
                ];
            }
        }
        update_post_meta( $post_id, '_olo_service_seasons', $seasons );

        // Chiusure
        $closures = [];
        if ( isset( $_POST['olo_closures'] ) && is_array( $_POST['olo_closures'] ) ) {
            foreach ( $_POST['olo_closures'] as $closure_data ) {
                if ( empty( $closure_data['date_from'] ) ) continue;
                $closures[] = [
                    'date_from' => sanitize_text_field( $closure_data['date_from'] ?? '' ),
                    'date_to'   => sanitize_text_field( $closure_data['date_to'] ?? '' ),
                    'reason'    => sanitize_text_field( $closure_data['reason'] ?? '' ),
                ];
            }
        }
        update_post_meta( $post_id, '_olo_service_closures', $closures );
    }
}
