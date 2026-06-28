<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Olobuild_Custom_Code
 *
 * Output custom HTML/JS/CSS snippets in <head>, after <body>, or before </body>.
 * Values stored in wp_options: olo_custom_code_head, olo_custom_code_body, olo_custom_code_footer.
 */
class Olobuild_Custom_Code {

    public static function init() {
        add_action( 'wp_head',      [ __CLASS__, 'output_head_code' ], 99 );
        add_action( 'wp_body_open', [ __CLASS__, 'output_body_open_code' ], 1 );
        add_action( 'wp_footer',    [ __CLASS__, 'output_footer_code' ], 99 );
    }

    public static function output_head_code() {
        if ( ! current_user_can( 'unfiltered_html' ) && ! self::is_admin_saved() ) {
            return;
        }
        $code = get_option( 'olo_custom_code_head', '' );
        if ( $code ) {
            // Custom code is saved only by users with unfiltered_html capability
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- raw output by design: snippets can only be saved by users with the unfiltered_html capability (gated in Olobuild_Rest_Api::save_custom_code) and printing is capability-gated above.
            echo "<!-- Olobuild Custom Head -->\n" . $code . "\n";
        }
    }

    public static function output_body_open_code() {
        if ( ! current_user_can( 'unfiltered_html' ) && ! self::is_admin_saved() ) {
            return;
        }
        $code = get_option( 'olo_custom_code_body', '' );
        if ( $code ) {
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- raw output by design: snippets can only be saved by users with the unfiltered_html capability (gated in Olobuild_Rest_Api::save_custom_code) and printing is capability-gated above.
            echo "<!-- Olobuild Custom Body -->\n" . $code . "\n";
        }
    }

    public static function output_footer_code() {
        if ( ! current_user_can( 'unfiltered_html' ) && ! self::is_admin_saved() ) {
            return;
        }
        $code = get_option( 'olo_custom_code_footer', '' );
        if ( $code ) {
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- raw output by design: snippets can only be saved by users with the unfiltered_html capability (gated in Olobuild_Rest_Api::save_custom_code) and printing is capability-gated above.
            echo "<!-- Olobuild Custom Footer -->\n" . $code . "\n";
        }
    }

    /**
     * Check if the custom code options were saved by an admin with unfiltered_html.
     * On frontend output, the current user is anonymous, so we rely on the fact
     * that only users with unfiltered_html can save the options (enforced in REST API).
     */
    private static function is_admin_saved() {
        return ! is_user_logged_in() || is_admin();
    }
}
