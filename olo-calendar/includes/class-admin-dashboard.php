<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Admin_Dashboard {

    public function init() {
        add_action( 'admin_menu', [ $this, 'add_dashboard_page' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
    }

    public function add_dashboard_page() {
        // Add Dashboard as first submenu under the CPT menu
        add_submenu_page(
            'edit.php?post_type=olo_event',
            'Dashboard Calendario',
            'Dashboard',
            'edit_posts',
            'olo-calendar-dashboard',
            [ $this, 'render_page' ]
        );

        // Move Dashboard to top of submenu
        global $submenu;
        $menu_key = 'edit.php?post_type=olo_event';
        if ( isset( $submenu[ $menu_key ] ) ) {
            $dashboard_item = null;
            $dashboard_index = null;
            foreach ( $submenu[ $menu_key ] as $i => $item ) {
                if ( $item[2] === 'olo-calendar-dashboard' ) {
                    $dashboard_item = $item;
                    $dashboard_index = $i;
                    break;
                }
            }
            if ( $dashboard_item !== null ) {
                unset( $submenu[ $menu_key ][ $dashboard_index ] );
                array_unshift( $submenu[ $menu_key ], $dashboard_item );
            }
        }
    }

    public function enqueue_assets( $hook ) {
        if ( $hook !== 'olo_event_page_olo-calendar-dashboard' ) return;

        // FullCalendar
        wp_enqueue_script(
            'fullcalendar',
            'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js',
            [],
            '6.1.15',
            true
        );

        wp_enqueue_style(
            'olo-cal-admin-dash',
            OLO_CAL_URL . 'assets/css/admin-dashboard.css',
            [],
            OLO_CAL_VERSION
        );

        wp_enqueue_script(
            'olo-cal-admin-dash',
            OLO_CAL_URL . 'assets/js/admin-dashboard.js',
            [ 'fullcalendar' ],
            OLO_CAL_VERSION,
            true
        );

        // Get categories for dropdown
        $terms = get_terms( [ 'taxonomy' => 'olo_event_cat', 'hide_empty' => false ] );
        $cats  = [];
        if ( ! is_wp_error( $terms ) ) {
            foreach ( $terms as $t ) {
                $cats[] = [
                    'id'    => $t->term_id,
                    'name'  => $t->name,
                    'slug'  => $t->slug,
                    'color' => get_term_meta( $t->term_id, '_event_color', true ) ?: '#6366F1',
                ];
            }
        }

        wp_localize_script( 'olo-cal-admin-dash', 'oloCalAdmin', [
            'restUrl'    => esc_url_raw( rest_url( 'olo-calendar/v1' ) ),
            'nonce'      => wp_create_nonce( 'wp_rest' ),
            'categories' => $cats,
        ] );
    }

    public function render_page() {
        ?>
        <div class="wrap olo-dash-wrap">
            <div class="olo-dash-header">
                <div class="olo-dash-header-left">
                    <h1 class="olo-dash-title">
                        <span class="dashicons dashicons-calendar-alt"></span>
                        Olo Calendar
                    </h1>
                    <span class="olo-dash-version">v<?php echo OLO_CAL_VERSION; ?></span>
                </div>
                <div class="olo-dash-header-right">
                    <button type="button" class="button button-primary olo-dash-add-btn" id="olo-dash-add-event">
                        <span class="dashicons dashicons-plus-alt2"></span>
                        Nuovo evento
                    </button>
                </div>
            </div>

            <!-- Stats bar -->
            <div class="olo-dash-stats" id="olo-dash-stats"></div>

            <!-- Calendar -->
            <div class="olo-dash-calendar-wrap">
                <div id="olo-dash-calendar"></div>
            </div>

            <!-- Event Modal -->
            <div class="olo-dash-modal" id="olo-dash-modal" style="display:none">
                <div class="olo-dash-modal-backdrop"></div>
                <div class="olo-dash-modal-dialog">
                    <div class="olo-dash-modal-header">
                        <h2 class="olo-dash-modal-title">Nuovo evento</h2>
                        <button type="button" class="olo-dash-modal-close">&times;</button>
                    </div>
                    <form id="olo-dash-event-form" class="olo-dash-modal-body">
                        <input type="hidden" name="event_id" value="" />

                        <div class="olo-dash-field">
                            <label for="olo-ev-title">Titolo *</label>
                            <input type="text" id="olo-ev-title" name="title" required placeholder="Nome evento" />
                        </div>

                        <div class="olo-dash-row">
                            <div class="olo-dash-field olo-dash-field-flex">
                                <label for="olo-ev-start-date">Data inizio *</label>
                                <input type="date" id="olo-ev-start-date" name="start_date" required />
                            </div>
                            <div class="olo-dash-field olo-dash-field-flex olo-ev-time-field">
                                <label for="olo-ev-start-time">Ora inizio</label>
                                <input type="time" id="olo-ev-start-time" name="start_time" value="09:00" />
                            </div>
                            <div class="olo-dash-field olo-dash-field-flex">
                                <label for="olo-ev-end-date">Data fine</label>
                                <input type="date" id="olo-ev-end-date" name="end_date" />
                            </div>
                            <div class="olo-dash-field olo-dash-field-flex olo-ev-time-field">
                                <label for="olo-ev-end-time">Ora fine</label>
                                <input type="time" id="olo-ev-end-time" name="end_time" value="10:00" />
                            </div>
                        </div>

                        <div class="olo-dash-field-inline">
                            <label class="olo-dash-checkbox">
                                <input type="checkbox" id="olo-ev-allday" name="all_day" />
                                <span>Tutto il giorno</span>
                            </label>
                        </div>

                        <div class="olo-dash-row">
                            <div class="olo-dash-field" style="flex:2">
                                <label for="olo-ev-location">Luogo</label>
                                <input type="text" id="olo-ev-location" name="location" placeholder="es. Centro Congressi, Roma" />
                            </div>
                            <div class="olo-dash-field" style="flex:1">
                                <label for="olo-ev-category">Categoria</label>
                                <select id="olo-ev-category" name="category_id">
                                    <option value="0">Nessuna</option>
                                </select>
                            </div>
                        </div>

                        <div class="olo-dash-row">
                            <div class="olo-dash-field" style="flex:2">
                                <label for="olo-ev-url">URL esterno</label>
                                <input type="url" id="olo-ev-url" name="event_url" placeholder="Link opzionale" />
                            </div>
                            <div class="olo-dash-field" style="flex:1">
                                <label for="olo-ev-color">Colore</label>
                                <div class="olo-dash-color-wrap">
                                    <input type="color" id="olo-ev-color" name="color" value="#6366F1" />
                                    <button type="button" class="olo-dash-color-reset" title="Usa colore categoria">Auto</button>
                                </div>
                            </div>
                        </div>

                        <div class="olo-dash-field">
                            <label for="olo-ev-excerpt">Descrizione breve</label>
                            <textarea id="olo-ev-excerpt" name="excerpt" rows="3" placeholder="Descrizione visibile nella modale evento"></textarea>
                        </div>
                    </form>
                    <div class="olo-dash-modal-footer">
                        <div class="olo-dash-modal-footer-left">
                            <button type="button" class="button olo-dash-delete-btn" id="olo-dash-delete" style="display:none">
                                <span class="dashicons dashicons-trash"></span> Elimina
                            </button>
                        </div>
                        <div class="olo-dash-modal-footer-right">
                            <button type="button" class="button" id="olo-dash-cancel">Annulla</button>
                            <button type="button" class="button button-primary" id="olo-dash-save">
                                <span class="dashicons dashicons-saved"></span> Salva evento
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Confirm Delete -->
            <div class="olo-dash-confirm" id="olo-dash-confirm" style="display:none">
                <div class="olo-dash-modal-backdrop"></div>
                <div class="olo-dash-confirm-dialog">
                    <p><strong>Eliminare questo evento?</strong></p>
                    <p class="olo-dash-confirm-name"></p>
                    <div class="olo-dash-confirm-actions">
                        <button type="button" class="button" id="olo-dash-confirm-no">Annulla</button>
                        <button type="button" class="button olo-dash-delete-btn" id="olo-dash-confirm-yes">
                            <span class="dashicons dashicons-trash"></span> Elimina
                        </button>
                    </div>
                </div>
            </div>

            <!-- Toast notification -->
            <div class="olo-dash-toast" id="olo-dash-toast" style="display:none"></div>
        </div>
        <?php
    }
}
