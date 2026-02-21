<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Calendar_Frontend {

    public function init() {
        add_shortcode( 'olo_calendar', [ $this, 'render_shortcode' ] );
    }

    /**
     * [olo_calendar view="dayGridMonth" height="auto" categories="" toolbar="1" filter="1"]
     */
    public function render_shortcode( $atts ) {
        $atts = shortcode_atts( [
            'view'       => 'dayGridMonth',
            'height'     => 'auto',
            'categories' => '',
            'toolbar'    => '1',
            'filter'     => '1',
            'locale'     => 'it',
        ], $atts, 'olo_calendar' );

        $config = [
            'defaultView'       => sanitize_text_field( $atts['view'] ),
            'height'            => $atts['height'] === 'auto' ? 'auto' : absint( $atts['height'] ),
            'showToolbar'       => (bool) $atts['toolbar'],
            'categories'        => sanitize_text_field( $atts['categories'] ),
            'locale'            => sanitize_text_field( $atts['locale'] ),
            'restUrl'           => esc_url_raw( rest_url( 'olo-calendar/v1/events' ) ),
            'showCategoryFilter' => (bool) $atts['filter'],
            'filterStyle'       => 'pills',
            'showToday'         => true,
            'showMonth'         => true,
            'showWeek'          => true,
            'showDay'           => true,
            'showList'          => true,
        ];

        return $this->render_calendar( $config );
    }

    /**
     * Render calendar programmatically (used by Olobuild tile).
     */
    public function render_calendar( $config = [] ) {
        $this->enqueue_assets();

        $uid = 'olo-cal-' . wp_rand( 10000, 99999 );

        $defaults = [
            'defaultView'        => 'dayGridMonth',
            'height'             => 'auto',
            'showToolbar'        => true,
            'categories'         => '',
            'locale'             => 'it',
            'restUrl'            => esc_url_raw( rest_url( 'olo-calendar/v1/events' ) ),
            'showCategoryFilter' => true,
            'filterStyle'        => 'pills',
            'filterPosition'     => 'above',
            'showToday'          => true,
            'showMonth'          => true,
            'showWeek'           => true,
            'showDay'            => true,
            'showList'           => true,
        ];

        $config = wp_parse_args( $config, $defaults );

        // Get categories for filter
        $show_filter = ! empty( $config['showCategoryFilter'] );
        $categories  = $show_filter ? $this->get_event_categories() : [];
        $filter_style = $config['filterStyle'] ?? 'pills';
        $filter_pos   = $config['filterPosition'] ?? 'above';

        ob_start();
        ?>
        <div id="<?php echo esc_attr( $uid ); ?>" class="olo-calendar-wrap" data-config='<?php echo wp_json_encode( $config ); ?>'>
            <?php
            // Category filter above calendar
            if ( $show_filter && ! empty( $categories ) && $filter_pos === 'above' ) {
                echo $this->render_category_filter( $categories, $filter_style );
            }
            ?>
            <div class="olo-calendar-container"></div>
        </div>
        <?php
        // Only render modal once per page
        static $modal_rendered = false;
        if ( ! $modal_rendered ) {
            echo $this->render_modal_template();
            $modal_rendered = true;
        }

        return ob_get_clean();
    }

    private function enqueue_assets() {
        // FullCalendar 6.x from CDN
        if ( ! wp_script_is( 'fullcalendar', 'enqueued' ) ) {
            wp_enqueue_script(
                'fullcalendar',
                'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js',
                [],
                '6.1.15',
                true
            );
        }

        wp_enqueue_style(
            'olo-calendar-front',
            OLO_CAL_URL . 'assets/css/calendar.css',
            [],
            OLO_CAL_VERSION
        );

        wp_enqueue_script(
            'olo-calendar-front',
            OLO_CAL_URL . 'assets/js/calendar-front.js',
            [ 'fullcalendar' ],
            OLO_CAL_VERSION,
            true
        );
    }

    /**
     * Get all event categories with colors.
     */
    private function get_event_categories() {
        $terms = get_terms( [
            'taxonomy'   => 'olo_event_cat',
            'hide_empty' => true,
        ] );

        if ( is_wp_error( $terms ) || empty( $terms ) ) {
            return [];
        }

        $cats = [];
        foreach ( $terms as $term ) {
            $cats[] = [
                'slug'  => $term->slug,
                'name'  => $term->name,
                'color' => get_term_meta( $term->term_id, '_event_color', true ) ?: '',
                'count' => $term->count,
            ];
        }
        return $cats;
    }

    /**
     * Render category filter checkboxes/pills.
     */
    private function render_category_filter( $categories, $style = 'pills' ) {
        if ( empty( $categories ) ) return '';

        ob_start();
        ?>
        <div class="olo-cal-filters olo-cal-filters--<?php echo esc_attr( $style ); ?>">
            <label class="olo-cal-filter-item olo-cal-filter-all active" data-cat="__all">
                <?php if ( $style === 'checkboxes' ) : ?>
                    <input type="checkbox" checked />
                <?php endif; ?>
                <span class="olo-cal-filter-label">Tutti</span>
                <?php if ( $style === 'pills' ) : ?>
                    <span class="olo-cal-filter-dot" style="background:var(--olo-cal-primary,var(--olo-color-primary,#6366F1))"></span>
                <?php endif; ?>
            </label>
            <?php foreach ( $categories as $cat ) :
                $cat_color = $cat['color'] ?: 'var(--olo-cal-primary,var(--olo-color-primary,#6366F1))';
            ?>
                <label class="olo-cal-filter-item active" data-cat="<?php echo esc_attr( $cat['slug'] ); ?>">
                    <?php if ( $style === 'checkboxes' ) : ?>
                        <input type="checkbox" checked data-cat="<?php echo esc_attr( $cat['slug'] ); ?>"
                               style="accent-color:<?php echo esc_attr( $cat_color ); ?>" />
                    <?php endif; ?>
                    <?php if ( $style === 'pills' || $style === 'minimal' ) : ?>
                        <span class="olo-cal-filter-dot" style="background:<?php echo esc_attr( $cat_color ); ?>"></span>
                    <?php endif; ?>
                    <span class="olo-cal-filter-label"><?php echo esc_html( $cat['name'] ); ?></span>
                    <span class="olo-cal-filter-count"><?php echo absint( $cat['count'] ); ?></span>
                </label>
            <?php endforeach; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    private function render_modal_template() {
        ob_start();
        ?>
        <!-- Olo Calendar Event Modal -->
        <div id="olo-cal-modal" class="olo-cal-modal" style="display:none" role="dialog" aria-modal="true">
            <div class="olo-cal-modal-backdrop"></div>
            <div class="olo-cal-modal-dialog">
                <button class="olo-cal-modal-close" aria-label="Chiudi">&times;</button>
                <div class="olo-cal-modal-image" style="display:none">
                    <img src="" alt="" />
                </div>
                <div class="olo-cal-modal-body">
                    <div class="olo-cal-modal-cats"></div>
                    <h2 class="olo-cal-modal-title"></h2>
                    <div class="olo-cal-modal-meta">
                        <span class="olo-cal-modal-date">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            <span class="olo-cal-modal-date-text"></span>
                        </span>
                        <span class="olo-cal-modal-time" style="display:none">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            <span class="olo-cal-modal-time-text"></span>
                        </span>
                        <span class="olo-cal-modal-location" style="display:none">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            <span class="olo-cal-modal-location-text"></span>
                        </span>
                    </div>
                    <div class="olo-cal-modal-excerpt"></div>
                    <div class="olo-cal-modal-actions">
                        <a class="olo-cal-modal-link" href="#" style="display:none">Dettagli evento</a>
                        <a class="olo-cal-modal-extlink" href="#" target="_blank" rel="noopener" style="display:none">Link esterno</a>
                    </div>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}
