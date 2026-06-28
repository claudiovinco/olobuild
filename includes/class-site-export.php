<?php
if (!defined('ABSPATH')) exit;

class Olo_Site_Export {

    /**
     * Export all Olobuild data as a JSON package.
     * Includes: templates, global widgets, styles, options, custom fonts.
     */
    public static function export_site() {
        global $wpdb;

        $data = [
            'version'     => OLO_VERSION,
            'exported_at' => current_time('mysql'),
            'site_url'    => get_site_url(),
        ];

        // All templates
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- tabella custom del plugin (olo_templates); nessun equivalente WP_Query; export una tantum, risultato non cacheabile.
        $templates = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}olo_templates", ARRAY_A);
        $data['templates'] = $templates ?: [];

        // Global widgets
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- tabella custom del plugin (olo_global_widgets); nessun equivalente WP_Query; export una tantum, risultato non cacheabile.
        $widgets = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}olo_global_widgets", ARRAY_A);
        $data['global_widgets'] = $widgets ?: [];

        // Styles
        $data['styles']             = get_option('olo_styles', []);
        $data['global_colors']      = get_option('olo_global_colors', []);
        $data['global_typography']  = get_option('olo_global_typography', []);

        // Options
        $data['options'] = [
            'active_header'         => get_option('olo_active_header', ''),
            'active_footer'         => get_option('olo_active_footer', ''),
            'custom_fonts'          => get_option('olo_custom_fonts', []),
            'custom_code_head'      => get_option('olo_custom_code_head', ''),
            'custom_code_body'      => get_option('olo_custom_code_body', ''),
            'custom_code_footer'    => get_option('olo_custom_code_footer', ''),
            'cookie_consent_enabled'=> get_option('olo_cookie_consent_enabled', false),
        ];

        // Active singles
        $post_types = get_post_types(['public' => true], 'names');
        foreach ($post_types as $pt) {
            $val = get_option("olo_active_single_{$pt}", '');
            if ($val) $data['options']["active_single_{$pt}"] = $val;
        }

        return $data;
    }

    /**
     * Import site data from JSON package.
     */
    public static function import_site($data) {
        global $wpdb;
        $table = $wpdb->prefix . 'olo_templates';

        if (empty($data['templates'])) return ['imported' => 0];

        $id_map = []; // old_id => new_id
        $count  = 0;

        // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- tabelle custom del plugin (olo_templates, olo_global_widgets); nessun equivalente WP_Query; insert di import una tantum, nessuna cache da invalidare.
        foreach ($data['templates'] as $tpl) {
            $old_id = $tpl['id'];
            unset($tpl['id']); // Let DB auto-increment

            $wpdb->insert($table, [
                'title'      => $tpl['title'] ?? 'Imported',
                'type'       => $tpl['type'] ?? 'page',
                'content'    => $tpl['content'] ?? '[]',
                'settings'   => $tpl['settings'] ?? '{}',
                'thumbnail'  => $tpl['thumbnail'] ?? '',
                'status'     => $tpl['status'] ?? 'draft',
                'author_id'  => get_current_user_id(),
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql'),
            ]);

            $new_id = $wpdb->insert_id;
            if ($new_id) {
                $id_map[$old_id] = $new_id;
                $count++;
            }
        }

        // Global widgets
        if (!empty($data['global_widgets'])) {
            $gw_table = $wpdb->prefix . 'olo_global_widgets';
            foreach ($data['global_widgets'] as $gw) {
                $wpdb->insert($gw_table, [
                    'name'       => $gw['name'] ?? 'Widget',
                    'tile_data'  => $gw['tile_data'] ?? '{}',
                    'created_at' => current_time('mysql'),
                    'updated_at' => current_time('mysql'),
                ]);
            }
        }
        // phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching

        // Styles
        if (isset($data['styles'])) update_option('olo_styles', $data['styles']);
        if (isset($data['global_colors'])) update_option('olo_global_colors', $data['global_colors']);
        if (isset($data['global_typography'])) update_option('olo_global_typography', $data['global_typography']);

        // Options with ID remapping
        if (isset($data['options'])) {
            $opts = $data['options'];
            if (isset($opts['active_header'])) {
                if (isset($id_map[$opts['active_header']])) {
                    update_option('olo_active_header', $id_map[$opts['active_header']]);
                }
            }
            if (isset($opts['active_footer'])) {
                if (isset($id_map[$opts['active_footer']])) {
                    update_option('olo_active_footer', $id_map[$opts['active_footer']]);
                }
            }
            if (isset($opts['custom_fonts'])) update_option('olo_custom_fonts', $opts['custom_fonts']);
            if (isset($opts['custom_code_head'])) update_option('olo_custom_code_head', $opts['custom_code_head']);
            if (isset($opts['custom_code_body'])) update_option('olo_custom_code_body', $opts['custom_code_body']);
            if (isset($opts['custom_code_footer'])) update_option('olo_custom_code_footer', $opts['custom_code_footer']);
        }

        return ['imported' => $count, 'id_map' => $id_map];
    }
}
