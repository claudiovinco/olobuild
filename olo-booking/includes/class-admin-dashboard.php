<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Booking_Dashboard {

    public function init() {
        add_action( 'admin_menu', [ $this, 'add_pages' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
    }

    public function add_pages() {
        add_submenu_page(
            'edit.php?post_type=olo_service',
            'Dashboard Prenotazioni',
            'Dashboard',
            'manage_olo_bookings',
            'olo-booking-dashboard',
            [ $this, 'render_page' ]
        );

        // Move Dashboard to top
        global $submenu;
        $key = 'edit.php?post_type=olo_service';
        if ( isset( $submenu[ $key ] ) ) {
            $item = null;
            $idx  = null;
            foreach ( $submenu[ $key ] as $i => $sub ) {
                if ( $sub[2] === 'olo-booking-dashboard' ) {
                    $item = $sub;
                    $idx  = $i;
                    break;
                }
            }
            if ( $item !== null ) {
                unset( $submenu[ $key ][ $idx ] );
                array_unshift( $submenu[ $key ], $item );
            }
        }
    }

    public function enqueue_assets( $hook ) {
        if ( $hook !== 'olo_service_page_olo-booking-dashboard' ) return;

        wp_enqueue_style(
            'olo-book-dash',
            OLO_BOOK_URL . 'assets/css/admin-dashboard.css',
            [],
            OLO_BOOK_VERSION
        );

        wp_enqueue_script(
            'olo-book-dash',
            OLO_BOOK_URL . 'assets/js/admin-dashboard.js',
            [],
            OLO_BOOK_VERSION,
            true
        );

        // Get accessible services
        $service_ids = Olo_Role_Manager::get_user_service_ids();
        $args = [
            'post_type'      => 'olo_service',
            'post_status'    => 'publish',
            'posts_per_page' => 100,
            'orderby'        => 'title',
            'order'          => 'ASC',
        ];
        if ( is_array( $service_ids ) ) {
            $args['post__in'] = empty( $service_ids ) ? [ 0 ] : $service_ids;
        }

        $query    = new WP_Query( $args );
        $services = [];
        foreach ( $query->posts as $p ) {
            $services[] = [
                'id'       => $p->ID,
                'title'    => $p->post_title,
                'duration' => (int) ( get_post_meta( $p->ID, '_olo_service_duration', true ) ?: 60 ),
                'color'    => get_post_meta( $p->ID, '_olo_service_color', true ) ?: '#6366F1',
                'type'     => get_post_meta( $p->ID, '_olo_service_type', true ) ?: 'accommodation',
            ];
        }

        wp_localize_script( 'olo-book-dash', 'oloBookAdmin', [
            'restUrl'  => esc_url_raw( rest_url( 'olo-booking/v1' ) ),
            'nonce'    => wp_create_nonce( 'wp_rest' ),
            'services' => $services,
            'isAdmin'  => current_user_can( 'manage_options' ),
        ] );
    }

    public function render_page() {
        ?>
        <div class="wrap olob-dash-wrap">
            <!-- Header -->
            <div class="olob-dash-header">
                <div class="olob-dash-header-left">
                    <h1 class="olob-dash-title">
                        <span class="dashicons dashicons-clipboard"></span>
                        Olo Booking
                    </h1>
                    <span class="olob-dash-version">v<?php echo esc_html( OLO_BOOK_VERSION ); ?></span>
                </div>
                <div class="olob-dash-header-right">
                    <select id="olob-service-filter" class="olob-service-select">
                        <option value="">Tutti i servizi</option>
                    </select>
                    <button type="button" class="button button-primary olob-add-btn" id="olob-add-booking">
                        <span class="dashicons dashicons-plus-alt2"></span> Nuova prenotazione
                    </button>
                </div>
            </div>

            <!-- Stats -->
            <div class="olob-dash-stats" id="olob-dash-stats"></div>

            <!-- Gantt Timeline -->
            <div class="olob-tl-wrap">
                <!-- Navigation -->
                <div class="olob-tl-nav">
                    <button type="button" class="button olob-tl-arrow" id="olob-prev">&lsaquo;</button>
                    <h3 class="olob-tl-title" id="olob-tl-title"></h3>
                    <button type="button" class="button olob-tl-arrow" id="olob-next">&rsaquo;</button>
                    <button type="button" class="button" id="olob-today">Oggi</button>
                </div>

                <div class="olob-tl-body">
                    <!-- Sidebar -->
                    <div class="olob-tl-sidebar" id="olob-tl-sidebar">
                        <div class="olob-tl-corner">Strutture</div>
                    </div>
                    <!-- Scrollable grid -->
                    <div class="olob-tl-scroll" id="olob-tl-scroll">
                        <div class="olob-tl-grid" id="olob-tl-grid"></div>
                    </div>
                </div>

                <!-- Legend -->
                <div class="olob-tl-legend">
                    <span class="olob-legend-item"><span class="olob-legend-dot olob-dot-confirmed"></span> Confermata</span>
                    <span class="olob-legend-item"><span class="olob-legend-dot olob-dot-pending"></span> In attesa</span>
                    <span class="olob-legend-item"><span class="olob-legend-dot" style="background:#D1D5DB"></span> Annullata</span>
                </div>
            </div>

            <!-- Popup (positioned absolute) -->
            <div class="olob-tl-popup" id="olob-popup" style="display:none"></div>

            <!-- Booking Modal -->
            <div class="olob-modal" id="olob-modal" style="display:none">
                <div class="olob-modal-backdrop"></div>
                <div class="olob-modal-dialog">
                    <div class="olob-modal-header">
                        <h2 class="olob-modal-title">Nuova prenotazione</h2>
                        <button type="button" class="olob-modal-close">&times;</button>
                    </div>
                    <form id="olob-form" class="olob-modal-body">
                        <input type="hidden" name="booking_id" value="" />

                        <div class="olob-row">
                            <div class="olob-field" style="flex:2">
                                <label for="olob-service">Servizio *</label>
                                <select id="olob-service" name="service_id" required></select>
                            </div>
                            <div class="olob-field" style="flex:1">
                                <label for="olob-status">Stato</label>
                                <select id="olob-status" name="status">
                                    <option value="pending">In attesa</option>
                                    <option value="confirmed">Confermata</option>
                                    <option value="cancelled">Annullata</option>
                                </select>
                            </div>
                        </div>

                        <div class="olob-section-title">Cliente</div>
                        <div class="olob-row">
                            <div class="olob-field" style="flex:2">
                                <label for="olob-name">Nome *</label>
                                <input type="text" id="olob-name" name="guest_name" required placeholder="Nome e cognome" />
                            </div>
                            <div class="olob-field">
                                <label for="olob-email">Email</label>
                                <input type="email" id="olob-email" name="guest_email" placeholder="email@esempio.it" />
                            </div>
                            <div class="olob-field">
                                <label for="olob-phone">Telefono</label>
                                <input type="tel" id="olob-phone" name="guest_phone" placeholder="+39 ..." />
                            </div>
                        </div>

                        <div class="olob-section-title">Date e Ospiti</div>
                        <div class="olob-row">
                            <div class="olob-field">
                                <label for="olob-checkin">Check-in *</label>
                                <input type="date" id="olob-checkin" name="checkin_date" required />
                            </div>
                            <div class="olob-field">
                                <label for="olob-checkout">Check-out *</label>
                                <input type="date" id="olob-checkout" name="checkout_date" required />
                            </div>
                            <div class="olob-field">
                                <label for="olob-guests">Ospiti</label>
                                <input type="number" id="olob-guests" name="guest_count" min="1" value="1" />
                            </div>
                            <div class="olob-field">
                                <label for="olob-price">Totale (&euro;)</label>
                                <input type="number" id="olob-price" name="price_total" min="0" step="0.01" value="0" />
                            </div>
                        </div>

                        <div class="olob-field">
                            <label for="olob-notes">Note</label>
                            <textarea id="olob-notes" name="notes" rows="2" placeholder="Note interne"></textarea>
                        </div>
                    </form>
                    <div class="olob-modal-footer">
                        <div class="olob-modal-footer-left">
                            <button type="button" class="button olob-delete-btn" id="olob-delete" style="display:none">
                                <span class="dashicons dashicons-trash"></span> Elimina
                            </button>
                        </div>
                        <div class="olob-modal-footer-right">
                            <button type="button" class="button" id="olob-cancel">Annulla</button>
                            <button type="button" class="button button-primary" id="olob-save">
                                <span class="dashicons dashicons-saved"></span> Salva
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Confirm Delete -->
            <div class="olob-confirm" id="olob-confirm" style="display:none">
                <div class="olob-modal-backdrop"></div>
                <div class="olob-confirm-dialog">
                    <p><strong>Eliminare questa prenotazione?</strong></p>
                    <p class="olob-confirm-name"></p>
                    <div class="olob-confirm-actions">
                        <button type="button" class="button" id="olob-confirm-no">Annulla</button>
                        <button type="button" class="button olob-delete-btn" id="olob-confirm-yes">
                            <span class="dashicons dashicons-trash"></span> Elimina
                        </button>
                    </div>
                </div>
            </div>

            <!-- Toast -->
            <div class="olob-toast" id="olob-toast" style="display:none"></div>
        </div>
        <?php
    }
}
