<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Olo_Custom_Code
 *
 * Output custom HTML/JS/CSS snippets in <head>, after <body>, or before </body>.
 * Values stored in wp_options: olo_custom_code_head, olo_custom_code_body, olo_custom_code_footer.
 */
class Olo_Custom_Code {

    public static function init() {
        add_action( 'wp_head',      [ __CLASS__, 'output_head_code' ], 99 );
        add_action( 'wp_body_open', [ __CLASS__, 'output_body_open_code' ], 1 );
        add_action( 'wp_footer',    [ __CLASS__, 'output_footer_code' ], 99 );
    }

    public static function output_head_code() {
        $code = get_option( 'olo_custom_code_head', '' );
        if ( $code ) {
            echo "<!-- Olobuild Custom Head -->\n" . $code . "\n";
        }
    }

    public static function output_body_open_code() {
        $code = get_option( 'olo_custom_code_body', '' );
        if ( $code ) {
            echo "<!-- Olobuild Custom Body -->\n" . $code . "\n";
        }
    }

    public static function output_footer_code() {
        $code = get_option( 'olo_custom_code_footer', '' );
        if ( $code ) {
            echo "<!-- Olobuild Custom Footer -->\n" . $code . "\n";
        }
    }
}
