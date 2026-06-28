<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_LiveSearch_Tile extends Olobuild_Tile_Base {

    protected $type     = 'livesearch';
    protected $name     = 'Ricerca Live';
    protected $icon     = 'dashicons-search';
    protected $category = 'navigation';

    protected $defaults = [
        'preset' => 'custom',
        'placeholder'          => 'Cerca...',
        'mode'                 => 'expanded',
        'min_chars'            => '2',
        'debounce_ms'          => '300',
        'max_results'          => '10',
        'results_columns'      => '1',
        'show_all_url'         => '',
        'show_all_text'        => 'Vedi tutti i risultati',
        'show_thumbnail'       => true,
        'show_excerpt'         => true,
        'title_only'           => false,
        'no_results_text'      => 'Nessun risultato trovato',
        'post_types'           => 'post,page',
        'taxonomy_filter'      => '',
        'taxonomy_terms'       => '',
        'exclude_terms'        => '',
        'input_bg'             => '#ffffff',
        'input_color'          => '',
        'icon_color'           => '',
        'input_font_size'      => '14',
        'input_height'         => '44',
        'input_border_color'   => '#e5e7eb',
        'input_border_radius'  => '8',
        'focus_border_color'   => '',
        'results_bg'           => '#ffffff',
        'results_border_color' => '',
        'results_border_radius'=> '10',
        'item_hover_bg'        => '',
        'title_color'          => '',
        'excerpt_color'        => '#6b7280',
        'results_max_height'   => '400',
        'thumb_width'          => '48',
        'thumb_height'         => '48',
        'thumb_radius'         => '6',
        'modal_width'          => '560',
        'backdrop_color'       => 'rgba(0,0,0,0.5)',
        'animated_placeholder' => false,
        'placeholder_words'    => '',
            'border'                  => [],
        'border_hover'            => [],
        'border_hover_duration'   => 300,
        'border_effect'           => 'none',
        'border_effect_intensity' => 'medium',
        'border_effect_color2'    => '',
        'border_effect_angle'     => 135,
        'border_effect_speed'     => 4,
    ];

    public function get_controls() {
        return [];
    }

    /**
     * Register REST routes for live search.
     */
    public static function register_rest_routes() {
        register_rest_route( 'olo/v1', '/livesearch', [
            'methods'             => 'GET',
            'callback'            => [ __CLASS__, 'handle_search' ],
            'permission_callback' => '__return_true',
            'args'                => [
                'q' => [
                    'required'          => true,
                    'sanitize_callback' => 'sanitize_text_field',
                ],
                'post_types' => [
                    'default'           => 'post,page',
                    'sanitize_callback' => 'sanitize_text_field',
                ],
                'max' => [
                    'default'           => 10,
                    'sanitize_callback' => 'absint',
                ],
                'title_only' => [
                    'default'           => 0,
                    'sanitize_callback' => 'absint',
                ],
                'taxonomy' => [
                    'default'           => '',
                    'sanitize_callback' => 'sanitize_text_field',
                ],
                'terms' => [
                    'default'           => '',
                    'sanitize_callback' => 'sanitize_text_field',
                ],
                // phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude -- esclusione post necessaria alla funzione del tile; query a volume limitato
                'exclude' => [
                    'default'           => '',
                    'sanitize_callback' => 'sanitize_text_field',
                ],
                'thumb' => [
                    'default'           => 1,
                    'sanitize_callback' => 'absint',
                ],
            ],
        ] );
    }

    /**
     * Handle the AJAX live search request.
     */
    public static function handle_search( $request ) {
        // Rate limiting: max 30 requests per minute per IP
        $ip  = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : 'unknown';
        $key = 'olo_ls_rl_' . md5( $ip );
        $count = (int) get_transient( $key );
        if ( $count > 30 ) {
            return new WP_REST_Response( array( 'error' => 'Rate limit exceeded' ), 429 );
        }
        set_transient( $key, $count + 1, MINUTE_IN_SECONDS );

        $query_string = trim( $request->get_param( 'q' ) );
        if ( empty( $query_string ) ) {
            return new WP_REST_Response( [ 'results' => [], 'total' => 0 ], 200 );
        }

        // Parse post types
        $raw_types  = $request->get_param( 'post_types' );
        $post_types = array_filter( array_map( 'trim', explode( ',', $raw_types ) ) );
        if ( empty( $post_types ) ) {
            $post_types = [ 'post', 'page' ];
        }
        // Validate against actual registered post types
        $post_types = array_values( array_filter( $post_types, 'post_type_exists' ) );
        if ( empty( $post_types ) ) {
            return new WP_REST_Response( [ 'results' => [], 'total' => 0 ], 200 );
        }

        $max        = max( 1, min( 100, (int) $request->get_param( 'max' ) ) );
        $title_only = (bool) $request->get_param( 'title_only' );
        $taxonomy   = $request->get_param( 'taxonomy' );
        $terms_raw  = $request->get_param( 'terms' );
        $exclude    = $request->get_param( 'exclude' );
        $show_thumb = (bool) $request->get_param( 'thumb' );

        // Build WP_Query args
        $args = [
            'post_type'      => $post_types,
            'post_status'    => 'publish',
            'posts_per_page' => $max,
            'orderby'        => 'relevance',
            'order'          => 'DESC',
        ];

        if ( $title_only ) {
            // Use title-only filter
            $args['_olo_livesearch_title'] = $query_string;
        } else {
            $args['s'] = $query_string;
        }

        // Taxonomy filter
        if ( ! empty( $taxonomy ) && taxonomy_exists( $taxonomy ) && ! empty( $terms_raw ) ) {
            $term_slugs = array_filter( array_map( 'trim', explode( ',', $terms_raw ) ) );
            if ( ! empty( $term_slugs ) ) {
                $args['tax_query'] = [ // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- query per filtro tassonomia della ricerca live; tax query necessaria alla funzione, volume limitato (max 100 risultati)
                    [
                        'taxonomy' => $taxonomy,
                        'field'    => 'slug',
                        'terms'    => $term_slugs,
                    ],
                ];
            }
        }

        // Title-only filter hook
        if ( $title_only ) {
            add_filter( 'posts_where', [ __CLASS__, 'title_filter' ], 10, 2 );
        }

        $wp_query = new WP_Query( $args );

        if ( $title_only ) {
            remove_filter( 'posts_where', [ __CLASS__, 'title_filter' ], 10 );
        }

        // Exclude terms post-filter
        $exclude_words = [];
        if ( ! empty( $exclude ) ) {
            $exclude_words = array_filter( array_map( 'trim', explode( ',', mb_strtolower( $exclude ) ) ) );
        }

        $results = [];
        $total   = $wp_query->found_posts;

        foreach ( $wp_query->posts as $p ) {
            // Post-filter: skip if title contains any excluded word
            if ( ! empty( $exclude_words ) ) {
                $title_lower = mb_strtolower( $p->post_title );
                $skip = false;
                foreach ( $exclude_words as $ew ) {
                    if ( str_contains( $title_lower, $ew ) ) {
                        $skip = true;
                        break;
                    }
                }
                if ( $skip ) {
                    $total--;
                    continue;
                }
            }

            $item = [
                'id'    => $p->ID,
                'title' => $p->post_title,
                'url'   => get_permalink( $p->ID ),
                'type'  => $p->post_type,
            ];

            // Excerpt
            $excerpt = $p->post_excerpt;
            if ( empty( $excerpt ) ) {
                $excerpt = wp_trim_words( wp_strip_all_tags( $p->post_content ), 20, '...' );
            }
            $item['excerpt'] = $excerpt;

            // Thumbnail
            if ( $show_thumb ) {
                $thumb_id = get_post_thumbnail_id( $p->ID );
                if ( $thumb_id ) {
                    $thumb_url = wp_get_attachment_image_url( $thumb_id, 'thumbnail' );
                    $item['thumbnail'] = $thumb_url ?: '';
                } else {
                    $item['thumbnail'] = '';
                }
            }

            $results[] = $item;
        }

        return new WP_REST_Response( [
            'results' => $results,
            'total'   => $total,
        ], 200 );
    }

    /**
     * WP_Query filter: search only in post_title.
     */
    public static function title_filter( $where, $query ) {
        global $wpdb;
        $search_term = $query->get( '_olo_livesearch_title' );
        if ( ! empty( $search_term ) ) {
            $like  = '%' . $wpdb->esc_like( $search_term ) . '%';
            $where .= $wpdb->prepare( " AND {$wpdb->posts}.post_title LIKE %s", $like );
        }
        return $where;
    }

    /**
     * Render frontend HTML.
     */
    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        $element_id  = 'olo-livesearch-' . wp_unique_id();
        $valid_modes = [ 'compact', 'expanded', 'modal', 'inline' ];
        $mode        = in_array( $s['mode'], $valid_modes, true ) ? $s['mode'] : 'expanded';
        $height      = max( 32, min( 72, intval( $s['input_height'] ) ) );
        $font_size   = max( 12, min( 24, intval( $s['input_font_size'] ) ) );
        $modal_width = max( 400, min( 800, intval( $s['modal_width'] ?? 560 ) ) );
        $input_radius = Olobuild_Tile_Utils::border_radius( $s['input_border_radius'] ?? 8 );
        $input_radius_hover_css = Olobuild_Tile_Utils::radius_force_css( $s['input_border_radius_hover'] ?? null );
        $results_radius = Olobuild_Tile_Utils::border_radius( $s['results_border_radius'] ?? 10 );
        $results_radius_hover_css = Olobuild_Tile_Utils::radius_force_css( $s['results_border_radius_hover'] ?? null );

        // Animated placeholder
        $anim_ph = ! empty( $s['animated_placeholder'] );
        $ph_words = [];
        if ( $anim_ph ) {
            $raw = is_array( $s['placeholder_words'] ) ? implode( "\n", $s['placeholder_words'] ) : (string) $s['placeholder_words'];
            $ph_words = array_filter( array_map( 'trim', explode( "\n", $raw ) ) );
        }

        // Config JSON for frontend JS
        $config = [
            'mode'        => $mode,
            'minChars'    => max( 1, min( 3, intval( $s['min_chars'] ) ) ),
            'debounce'    => max( 100, min( 800, intval( $s['debounce_ms'] ) ) ),
            'maxResults'  => max( 3, min( 100, intval( $s['max_results'] ) ) ),
            'columns'     => max( 1, min( 4, intval( $s['results_columns'] ?? 1 ) ) ),
            'postTypes'   => $s['post_types'],
            'titleOnly'   => ! empty( $s['title_only'] ),
            'taxonomy'    => $s['taxonomy_filter'],
            'terms'       => $s['taxonomy_terms'],
            // phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude -- esclusione post necessaria alla funzione del tile; query a volume limitato
            'exclude'     => $s['exclude_terms'],
            'showThumb'   => ! empty( $s['show_thumbnail'] ),
            'showExcerpt' => ! empty( $s['show_excerpt'] ),
            'noResultsText' => $s['no_results_text'],
            'showAllUrl'  => $s['show_all_url'],
            'showAllText' => $s['show_all_text'],
            'restUrl'     => esc_url_raw( rest_url( 'olo/v1/livesearch' ) ),
            'modalWidth'  => $modal_width,
        ];

        if ( $anim_ph && ! empty( $ph_words ) ) {
            $config['animPh'] = array_values( $ph_words );
        }

        $config_b64 = base64_encode( wp_json_encode( $config ) );

        // Inline CSS custom properties
        $css_vars_arr = [
            '--ls-input-bg'           => $this->safe_color_css( $s['input_bg'] ),
            '--ls-input-color'        => $this->safe_color_css( $s['input_color'] ) ?: 'var(--olo-color-text, #374151)',
            '--ls-icon-color'         => $this->safe_color_css( $s['icon_color'] ) ?: 'var(--olo-color-text-muted, #9CA3AF)',
            '--ls-input-height'       => $height . 'px',
            '--ls-input-font-size'    => $font_size . 'px',
            '--ls-input-border-color' => $this->safe_color_css( $s['input_border_color'] ?? '' ) ?: 'var(--olo-color-border, #E5E7EB)',
            '--ls-input-radius'       => $input_radius,
            '--ls-focus-border-color' => $this->safe_color_css( $s['focus_border_color'] ?? '' ) ?: 'var(--olo-color-primary, #e1474f)',
            '--ls-results-bg'         => $this->safe_color_css( $s['results_bg'] ),
            '--ls-results-border'     => $this->safe_color_css( $s['results_border_color'] ) ?: 'var(--olo-color-border, #E5E7EB)',
            '--ls-results-radius'     => $results_radius,
            '--ls-item-hover-bg'      => $this->safe_color_css( $s['item_hover_bg'] ) ?: 'var(--olo-color-muted, #F3F4F6)',
            '--ls-title-color'        => $this->safe_color_css( $s['title_color'] ),
            '--ls-excerpt-color'      => $this->safe_color_css( $s['excerpt_color'] ),
            '--ls-results-max-h'      => max( 200, min( 800, intval( $s['results_max_height'] ) ) ) . 'px',
            '--ls-thumb-width'        => max( 32, min( 120, intval( $s['thumb_width'] ) ) ) . 'px',
            '--ls-thumb-height'       => max( 32, min( 120, intval( $s['thumb_height'] ) ) ) . 'px',
            '--ls-thumb-radius'       => Olobuild_Tile_Utils::border_radius( $s['thumb_radius'] ?? 0 ),
            '--ls-columns'            => max( 1, min( 4, intval( $s['results_columns'] ?? 1 ) ) ),
        ];
        if ( $mode === 'modal' ) {
            $css_vars_arr['--ls-modal-width']    = $modal_width . 'px';
            $css_vars_arr['--ls-backdrop-color']  = $this->safe_color_css( $s['backdrop_color'] ?? 'rgba(0,0,0,0.5)' );
        }
        $css_vars = $this->build_style( $css_vars_arr );

        // SVG search icon
        $icon_svg = '<svg class="olo-ls-icon" width="18" height="18" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="8.5" cy="8.5" r="5.5"/><line x1="13" y1="13" x2="17" y2="17"/></svg>';

        // Clear button SVG
        $clear_svg = '<svg width="14" height="14" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="5" y1="5" x2="15" y2="15"/><line x1="15" y1="5" x2="5" y2="15"/></svg>';

        ob_start();
        ?>
        <?php if ( $input_radius_hover_css !== '' || $results_radius_hover_css !== '' ) : ?>
        <?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above: esc_attr()'d element id and Olobuild_Tile_Utils::radius_force_css() absint-built radii. ?>
        <style>
            #<?php echo esc_attr( $element_id ); ?> .olo-ls-input{transition:border-radius 400ms cubic-bezier(.4,0,.2,1),border-color 0.2s}
            #<?php echo esc_attr( $element_id ); ?> .olo-ls-results{transition:border-radius 400ms cubic-bezier(.4,0,.2,1)}
            <?php if ( $input_radius_hover_css !== '' ) : ?>#<?php echo esc_attr( $element_id ); ?> .olo-ls-input:hover{border-radius:<?php echo $input_radius_hover_css; ?> !important}<?php endif; ?>
            <?php if ( $results_radius_hover_css !== '' ) : ?>#<?php echo esc_attr( $element_id ); ?> .olo-ls-results:hover{border-radius:<?php echo $results_radius_hover_css; ?> !important}<?php endif; ?>
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <?php endif; ?>
        <div id="<?php echo esc_attr( $element_id ); ?>"
             class="olo-livesearch olo-livesearch--<?php echo esc_attr( $mode ); ?> olo-ls-preset-<?php echo esc_attr( sanitize_key( $s['preset'] ?? 'custom' ) ); ?>"
             data-livesearch="<?php echo esc_attr( $config_b64 ); ?>"
             style="<?php echo $css_vars; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built by Olobuild_Tile_Base::build_style() which esc_attr()s every value ?>">

            <?php if ( $mode === 'modal' ) : ?>
                <!-- Trigger: solo icona -->
                <button class="olo-ls-trigger" type="button" aria-label="<?php esc_attr_e( 'Apri ricerca', 'olobuild' ); ?>">
                    <?php echo $icon_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG markup defined above ?>
                </button>

                <!-- Overlay modale (verr&agrave; spostato in <body> dal JS) -->
                <div class="olo-ls-overlay" style="<?php echo $css_vars; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built by Olobuild_Tile_Base::build_style() which esc_attr()s every value ?>">
                    <div class="olo-ls-backdrop"></div>
                    <div class="olo-ls-modal">
                        <div class="olo-ls-field">
                            <span class="olo-ls-field-icon"><?php echo $icon_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG markup defined above ?></span>
                            <input type="search"
                                   class="olo-ls-input"
                                   placeholder="<?php echo esc_attr( $s['placeholder'] ?: 'Cerca...' ); ?>"
                                   autocomplete="off"
                                   aria-label="<?php esc_attr_e( 'Cerca', 'olobuild' ); ?>">
                            <button class="olo-ls-clear" type="button" aria-label="<?php esc_attr_e( 'Cancella', 'olobuild' ); ?>" hidden>
                                <?php echo $clear_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG markup defined above ?>
                            </button>
                        </div>

                        <div class="olo-ls-dropdown" role="listbox" aria-label="<?php esc_attr_e( 'Risultati ricerca', 'olobuild' ); ?>" hidden>
                            <div class="olo-ls-results"></div>
                            <div class="olo-ls-footer" hidden></div>
                            <div class="olo-ls-empty" hidden>
                                <?php echo esc_html( $s['no_results_text'] ?: 'Nessun risultato trovato' ); ?>
                            </div>
                        </div>

                        <div class="olo-ls-modal-hint">
                            Premi <kbd>ESC</kbd> per chiudere
                        </div>
                    </div>
                </div>

            <?php else : ?>
                <div class="olo-ls-input-wrap">
                    <?php if ( $mode === 'compact' ) : ?>
                        <button class="olo-ls-trigger" type="button" aria-label="<?php esc_attr_e( 'Apri ricerca', 'olobuild' ); ?>">
                            <?php echo $icon_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG markup defined above ?>
                        </button>
                    <?php endif; ?>

                    <div class="olo-ls-field<?php echo esc_attr( $mode === 'compact' ? ' olo-ls-field--hidden' : '' ); ?>">
                        <span class="olo-ls-field-icon"><?php echo $icon_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG markup defined above ?></span>
                        <input type="search"
                               class="olo-ls-input"
                               placeholder="<?php echo esc_attr( $s['placeholder'] ?: 'Cerca...' ); ?>"
                               autocomplete="off"
                               aria-label="<?php esc_attr_e( 'Cerca', 'olobuild' ); ?>">
                        <button class="olo-ls-clear" type="button" aria-label="<?php esc_attr_e( 'Cancella', 'olobuild' ); ?>" hidden>
                            <?php echo $clear_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG markup defined above ?>
                        </button>
                    </div>
                </div>

                <div class="olo-ls-dropdown" role="listbox" aria-label="<?php esc_attr_e( 'Risultati ricerca', 'olobuild' ); ?>" hidden>
                    <div class="olo-ls-results"></div>
                    <div class="olo-ls-footer" hidden></div>
                    <div class="olo-ls-empty" hidden>
                        <?php echo esc_html( $s['no_results_text'] ?: 'Nessun risultato trovato' ); ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <?php
                // Border system
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$element_id}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$element_id}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) echo ".{$element_id}{{$border_css}}"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_css() from sanitized settings; $element_id is internally generated
            echo $border_hover_css . $border_effect_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_hover_css()/build_border_effect_css() from sanitized settings
        }
        return ob_get_clean();
    }
}
