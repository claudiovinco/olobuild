<?php
/**
 * Olo Lang — Gestione lingue e rilevamento lingua corrente.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Lang_Language {

    private static $current_lang = null;

    /**
     * Configurazione lingue.
     */
    public static function get_config() {
        return get_option( 'olo_lang_config', [
            'default_lang'      => 'it',
            'languages'         => [
                [ 'code' => 'it', 'name' => 'Italiano', 'locale' => 'it_IT', 'active' => true ],
            ],
            'url_mode'          => 'parameter',
            'show_switcher'     => true,
            'switcher_style'    => 'dropdown',
            'switcher_position' => 'bottom-right',
        ] );
    }

    public static function save_config( $config ) {
        return update_option( 'olo_lang_config', $config, false );
    }

    public static function get_default_lang() {
        $config = self::get_config();
        return $config['default_lang'] ?? 'it';
    }

    public static function get_active_languages() {
        $config = self::get_config();
        return array_values( array_filter( $config['languages'] ?? [], function ( $l ) {
            return ! empty( $l['active'] );
        } ) );
    }

    public static function get_language_codes() {
        return array_column( self::get_active_languages(), 'code' );
    }

    /**
     * Rileva la lingua corrente dalla richiesta HTTP.
     */
    public static function detect_current_lang() {
        if ( self::$current_lang !== null ) {
            return self::$current_lang;
        }

        $config      = self::get_config();
        $valid_codes = self::get_language_codes();
        $detected    = $config['default_lang'];

        // 1. Parametro URL ?lang=xx (modalita' parameter)
        if ( ! empty( $_GET['lang'] ) ) {
            $param = sanitize_text_field( wp_unslash( $_GET['lang'] ) );
            if ( in_array( $param, $valid_codes, true ) ) {
                $detected = $param;
            }
        }

        // 2. Prefisso path /xx/ (modalita' path)
        if ( $config['url_mode'] === 'path' ) {
            // Prima: costante impostata dallo strip del prefisso in class-frontend.php
            if ( defined( 'OLO_LANG_DETECTED' ) && in_array( OLO_LANG_DETECTED, $valid_codes, true ) ) {
                $detected = OLO_LANG_DETECTED;
            } else {
                // Fallback: legge direttamente dall'URI originale (prima dello strip)
                $original_uri = $_SERVER['OLO_ORIGINAL_URI'] ?? $_SERVER['REQUEST_URI'] ?? '';
                $path  = trim( wp_parse_url( $original_uri, PHP_URL_PATH ), '/' );
                $parts = explode( '/', $path );
                if ( ! empty( $parts[0] ) && in_array( $parts[0], $valid_codes, true ) ) {
                    $detected = $parts[0];
                }
            }
        }

        // 3. Integrazione Polylang
        if ( $config['url_mode'] === 'polylang' && function_exists( 'pll_current_language' ) ) {
            $pll_lang = pll_current_language( 'slug' );
            if ( $pll_lang && in_array( $pll_lang, $valid_codes, true ) ) {
                $detected = $pll_lang;
            }
        }

        self::$current_lang = apply_filters( 'olo_lang_current', $detected );
        return self::$current_lang;
    }

    public static function is_translated() {
        return self::detect_current_lang() !== self::get_default_lang();
    }

    /**
     * Costruisci URL per una lingua specifica.
     */
    public static function get_language_url( $lang_code, $url = '' ) {
        if ( ! $url ) {
            // Usa URI originale (prima dello strip del prefisso lingua)
            $uri = $_SERVER['OLO_ORIGINAL_URI'] ?? $_SERVER['REQUEST_URI'] ?? '/';
            $url = home_url( $uri );
        }

        $config = self::get_config();

        if ( $lang_code === $config['default_lang'] ) {
            $url = remove_query_arg( 'lang', $url );
            foreach ( self::get_language_codes() as $code ) {
                $url = preg_replace( '#/' . preg_quote( $code, '#' ) . '(/|$)#', '/', $url );
            }
            return $url;
        }

        switch ( $config['url_mode'] ) {
            case 'path':
                $parsed = wp_parse_url( $url );
                $path   = $parsed['path'] ?? '/';
                foreach ( self::get_language_codes() as $code ) {
                    $path = preg_replace( '#^/' . preg_quote( $code, '#' ) . '(/|$)#', '/', $path );
                }
                $path = '/' . $lang_code . $path;
                // Se l'URL non ha host (relativo), usa il dominio del sito
                $scheme = $parsed['scheme'] ?? '';
                $host   = $parsed['host'] ?? '';
                if ( ! $host ) {
                    $home   = wp_parse_url( get_option( 'home' ) );
                    $scheme = $home['scheme'] ?? 'https';
                    $host   = $home['host'] ?? '';
                }
                $new = $scheme . '://' . $host . $path;
                if ( ! empty( $parsed['query'] ) ) {
                    $new .= '?' . $parsed['query'];
                }
                return $new;

            case 'parameter':
            default:
                return add_query_arg( 'lang', $lang_code, $url );
        }
    }

    /**
     * Catalogo lingue disponibili per l'aggiunta.
     */
    public static function get_available_languages() {
        return [
            [ 'code' => 'it', 'name' => 'Italiano',    'locale' => 'it_IT', 'native' => 'Italiano' ],
            [ 'code' => 'en', 'name' => 'Inglese',     'locale' => 'en_US', 'native' => 'English' ],
            [ 'code' => 'de', 'name' => 'Tedesco',     'locale' => 'de_DE', 'native' => 'Deutsch' ],
            [ 'code' => 'fr', 'name' => 'Francese',    'locale' => 'fr_FR', 'native' => 'Fran&ccedil;ais' ],
            [ 'code' => 'es', 'name' => 'Spagnolo',    'locale' => 'es_ES', 'native' => 'Espa&ntilde;ol' ],
            [ 'code' => 'pt', 'name' => 'Portoghese',  'locale' => 'pt_PT', 'native' => 'Portugu&ecirc;s' ],
            [ 'code' => 'nl', 'name' => 'Olandese',    'locale' => 'nl_NL', 'native' => 'Nederlands' ],
            [ 'code' => 'ru', 'name' => 'Russo',       'locale' => 'ru_RU', 'native' => 'Русский' ],
            [ 'code' => 'zh', 'name' => 'Cinese',      'locale' => 'zh_CN', 'native' => '中文' ],
            [ 'code' => 'ja', 'name' => 'Giapponese',  'locale' => 'ja',    'native' => '日本語' ],
            [ 'code' => 'ko', 'name' => 'Coreano',     'locale' => 'ko_KR', 'native' => '한국어' ],
            [ 'code' => 'ar', 'name' => 'Arabo',       'locale' => 'ar',    'native' => 'العربية' ],
            [ 'code' => 'pl', 'name' => 'Polacco',     'locale' => 'pl_PL', 'native' => 'Polski' ],
            [ 'code' => 'ro', 'name' => 'Romeno',      'locale' => 'ro_RO', 'native' => 'Român&#259;' ],
            [ 'code' => 'cs', 'name' => 'Ceco',        'locale' => 'cs_CZ', 'native' => '&#268;e&scaron;tina' ],
            [ 'code' => 'hr', 'name' => 'Croato',      'locale' => 'hr',    'native' => 'Hrvatski' ],
            [ 'code' => 'sl', 'name' => 'Sloveno',     'locale' => 'sl_SI', 'native' => 'Sloven&scaron;&#269;ina' ],
            [ 'code' => 'sv', 'name' => 'Svedese',     'locale' => 'sv_SE', 'native' => 'Svenska' ],
            [ 'code' => 'da', 'name' => 'Danese',      'locale' => 'da_DK', 'native' => 'Dansk' ],
            [ 'code' => 'fi', 'name' => 'Finlandese',  'locale' => 'fi',    'native' => 'Suomi' ],
            [ 'code' => 'el', 'name' => 'Greco',       'locale' => 'el',    'native' => 'Ελληνικά' ],
            [ 'code' => 'hu', 'name' => 'Ungherese',   'locale' => 'hu_HU', 'native' => 'Magyar' ],
            [ 'code' => 'tr', 'name' => 'Turco',       'locale' => 'tr_TR', 'native' => 'T&uuml;rk&ccedil;e' ],
            [ 'code' => 'uk', 'name' => 'Ucraino',     'locale' => 'uk',    'native' => 'Українська' ],
            [ 'code' => 'bg', 'name' => 'Bulgaro',     'locale' => 'bg_BG', 'native' => 'Български' ],
            [ 'code' => 'sk', 'name' => 'Slovacco',    'locale' => 'sk_SK', 'native' => 'Sloven&#269;ina' ],
        ];
    }

    public static function reset() {
        self::$current_lang = null;
    }
}
