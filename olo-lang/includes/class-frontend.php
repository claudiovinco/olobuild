<?php
/**
 * Olo Lang — Integrazione frontend completa.
 *
 * Due motori di traduzione:
 *   1. Filtro 'olo_template_content' — traduce il JSON dei tile Olobuild (preciso, per campo)
 *   2. Output Buffering — traduce l'HTML finale dell'intera pagina (cattura tutto: plugin, tema, ecc.)
 *
 * Inoltre: JS localization per tradurre le stringhe JavaScript runtime.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Lang_Frontend {

    private $translation_cache = [];
    private $global_string_map = null;

    public function init() {
        // --- Motore 1: traduzione JSON dei tile Olobuild (preciso, per campo) ---
        add_filter( 'olo_template_content', [ $this, 'translate_content' ], 10, 2 );
        add_filter( 'olo_template_settings', [ $this, 'translate_page_settings' ], 10, 2 );

        // --- Motore 2: output buffering per tradurre TUTTO l'HTML della pagina ---
        add_action( 'template_redirect', [ $this, 'start_output_buffer' ] );

        // --- Motore 3: traduzione menu WordPress ---
        add_filter( 'wp_get_nav_menu_items', [ $this, 'translate_menu_items' ], 20 );

        // --- JS localization: passa stringhe tradotte al JavaScript ---
        add_action( 'wp_enqueue_scripts', [ $this, 'localize_js_strings' ], 99 );

        // --- Selettore lingua frontend ---
        add_action( 'wp_footer', [ $this, 'render_language_switcher' ] );
        add_shortcode( 'olo_lang_switcher', [ $this, 'switcher_shortcode' ] );

        // --- Tag hreflang per SEO ---
        add_action( 'wp_head', [ $this, 'output_hreflang_tags' ] );

        // --- Routing URL per modalita' path ---
        // Approccio professionale: strip del prefisso lingua da REQUEST_URI
        // cosi' WordPress gestisce TUTTI i tipi di URL nativamente (pages, posts, CPT, archivi, search)
        $config = Olo_Lang_Language::get_config();
        if ( $config['url_mode'] === 'path' ) {
            $this->strip_lang_prefix();
            add_filter( 'redirect_canonical', [ $this, 'prevent_lang_redirect' ], 10, 2 );
        }

        // --- Riscrittura automatica di TUTTI i permalink con prefisso lingua ---
        // Senza questo, link interni (PostGrid, breadcrumb, logo, contenuti) perdono la lingua
        add_filter( 'post_link',         [ $this, 'rewrite_permalink' ], 20, 2 );
        add_filter( 'page_link',         [ $this, 'rewrite_permalink' ], 20, 2 );
        add_filter( 'post_type_link',    [ $this, 'rewrite_permalink' ], 20, 2 );
        add_filter( 'term_link',         [ $this, 'rewrite_term_link' ], 20, 2 );

        // --- CSS/JS frontend ---
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_frontend_assets' ] );
    }

    // =========================================================================
    // MOTORE 1: Traduzione JSON tile Olobuild (filtro olo_template_content)
    // =========================================================================

    public function translate_content( $tiles, $template_id ) {
        $lang    = Olo_Lang_Language::detect_current_lang();
        $default = Olo_Lang_Language::get_default_lang();

        if ( $lang === $default ) {
            return $tiles;
        }

        $map = $this->get_cached_translations( $template_id, $lang );
        if ( empty( $map ) ) {
            return $tiles;
        }

        return $this->translate_nodes( $tiles, $map );
    }

    private function translate_nodes( $nodes, $map ) {
        foreach ( $nodes as &$node ) {
            $tile_id = $node['id'] ?? '';

            if ( $tile_id && ! empty( $node['settings'] ) ) {
                $node['settings'] = $this->apply_translations( $node['settings'], $tile_id, '', $map );
            }

            if ( ! empty( $node['children'] ) && is_array( $node['children'] ) ) {
                $node['children'] = $this->translate_nodes( $node['children'], $map );
            }
        }

        return $nodes;
    }

    private function apply_translations( $settings, $tile_id, $prefix, $map ) {
        foreach ( $settings as $key => &$value ) {
            $field_path = $prefix ? $prefix . '.' . $key : $key;

            if ( is_array( $value ) && self::is_items_key( $key ) ) {
                foreach ( $value as $idx => &$item ) {
                    if ( is_array( $item ) ) {
                        $item = $this->apply_translations( $item, $tile_id, $field_path . '[' . $idx . ']', $map );
                    }
                }
                continue;
            }

            if ( is_string( $value ) ) {
                $map_key = $tile_id . '::' . $field_path;
                if ( isset( $map[ $map_key ] ) ) {
                    $row = $map[ $map_key ];
                    if ( ! empty( $row['translation'] ) && $row['status'] !== 'bozza' ) {
                        $value = $row['translation'];
                    }
                }
            }
        }

        return $settings;
    }

    private static function is_items_key( $key ) {
        return in_array( $key, [ 'items', 'panels', 'slides', 'markers', 'cells', 'features', 'steps', 'members', 'tabs', 'options', 'links', 'buttons' ], true );
    }

    private function get_cached_translations( $template_id, $lang ) {
        $cache_key = $template_id . '_' . $lang;
        if ( ! isset( $this->translation_cache[ $cache_key ] ) ) {
            $db = new Olo_Lang_Database();
            $this->translation_cache[ $cache_key ] = $db->get_translation_map( $template_id, $lang );
        }
        return $this->translation_cache[ $cache_key ];
    }

    public function translate_page_settings( $settings, $template_id ) {
        return $settings;
    }

    // =========================================================================
    // MOTORE 3: Traduzione Menu WordPress
    // =========================================================================

    /**
     * Traduce i titoli delle voci di menu e riscrive gli URL interni
     * aggiungendo il prefisso lingua (/en/, /de/).
     * Filtro: wp_get_nav_menu_items — intercetta PRIMA che il menu venga renderizzato
     * (funziona con wp_nav_menu, Olobuild NavMenu tile, MegaMenu tile, ecc.)
     */
    public function translate_menu_items( $items ) {
        $lang    = Olo_Lang_Language::detect_current_lang();
        $default = Olo_Lang_Language::get_default_lang();

        if ( $lang === $default || ! is_array( $items ) ) {
            return $items;
        }

        $map      = $this->get_global_string_map();
        $config   = Olo_Lang_Language::get_config();
        $home_url = home_url();

        foreach ( $items as &$item ) {
            // Traduci il titolo della voce
            if ( ! empty( $item->title ) && isset( $map[ $item->title ] ) ) {
                $item->title = $map[ $item->title ];
            }

            // Riscrivi URL interni aggiungendo prefisso lingua
            if ( $config['url_mode'] === 'path' && ! empty( $item->url ) ) {
                // Solo link interni (stesso dominio)
                if ( strpos( $item->url, $home_url ) === 0 || strpos( $item->url, '/' ) === 0 ) {
                    $item->url = Olo_Lang_Language::get_language_url( $lang, $item->url );
                }
            }
        }

        return $items;
    }

    // =========================================================================
    // Riscrittura automatica permalink — mantiene la lingua nell'URL
    // =========================================================================

    /**
     * Riscrive i permalink di post/page/CPT con il prefisso lingua corrente.
     * Aggancia: post_link, page_link, post_type_link.
     */
    public function rewrite_permalink( $url, $post = null ) {
        if ( is_admin() ) {
            return $url;
        }

        $lang    = Olo_Lang_Language::detect_current_lang();
        $default = Olo_Lang_Language::get_default_lang();

        if ( $lang === $default ) {
            return $url;
        }

        return Olo_Lang_Language::get_language_url( $lang, $url );
    }

    /**
     * Riscrive i link delle tassonomie con il prefisso lingua corrente.
     */
    public function rewrite_term_link( $url, $term = null ) {
        if ( is_admin() ) {
            return $url;
        }

        $lang    = Olo_Lang_Language::detect_current_lang();
        $default = Olo_Lang_Language::get_default_lang();

        if ( $lang === $default ) {
            return $url;
        }

        return Olo_Lang_Language::get_language_url( $lang, $url );
    }

    // =========================================================================
    // MOTORE 2: Output Buffering — traduce l'intero HTML della pagina
    // =========================================================================

    /**
     * Avvia il buffer di output quando la lingua non e' quella default.
     * Alla fine del rendering, process_output_buffer() sostituisce le stringhe.
     */
    public function start_output_buffer() {
        if ( is_admin() ) {
            return;
        }

        $lang    = Olo_Lang_Language::detect_current_lang();
        $default = Olo_Lang_Language::get_default_lang();

        if ( $lang === $default ) {
            return;
        }

        ob_start( [ $this, 'process_output_buffer' ] );
    }

    /**
     * Callback del buffer: sostituisce tutte le stringhe globali nell'HTML.
     * Usa strtr() che non ri-sostituisce testo gia' matchato (evita cascading).
     * Aggiunge anche varianti HTML-encoded (es. &#8230; per ...).
     */
    public function process_output_buffer( $html ) {
        $map = $this->get_global_string_map();

        if ( empty( $map ) ) {
            return $html;
        }

        // Aggiungi varianti HTML-encoded delle chiavi
        // (WordPress/wptexturize converte ... in &#8230;, ← in &#8592;, ecc.)
        $expanded = [];
        foreach ( $map as $search => $replace ) {
            $expanded[ $search ] = $replace;

            // Variante con htmlspecialchars (& → &amp;, < → &lt;, ecc.)
            $encoded = htmlspecialchars( $search, ENT_QUOTES, 'UTF-8' );
            if ( $encoded !== $search && ! isset( $expanded[ $encoded ] ) ) {
                $expanded[ $encoded ] = $replace;
            }

            // Variante con ellipsis HTML entity (... → &#8230; e …)
            if ( strpos( $search, '...' ) !== false ) {
                $v1 = str_replace( '...', '&#8230;', $search );
                if ( ! isset( $expanded[ $v1 ] ) ) {
                    $expanded[ $v1 ] = $replace;
                }
                $v2 = str_replace( '...', "\xE2\x80\xA6", $search ); // UTF-8 ellipsis
                if ( ! isset( $expanded[ $v2 ] ) ) {
                    $expanded[ $v2 ] = $replace;
                }
            }

            // Variante con Unicode → numeric HTML entities (← → &#8592; ecc.)
            $numeric = preg_replace_callback( '/[^\x00-\x7F]/u', function ( $m ) {
                return '&#' . mb_ord( $m[0], 'UTF-8' ) . ';';
            }, $search );
            if ( $numeric !== $search && ! isset( $expanded[ $numeric ] ) ) {
                $expanded[ $numeric ] = $replace;
            }

            // Variante HTML-decoded (&#8592; → ←, &amp; → &, ecc.)
            $decoded = html_entity_decode( $search, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
            if ( $decoded !== $search && ! isset( $expanded[ $decoded ] ) ) {
                $expanded[ $decoded ] = $replace;
            }
        }

        // strtr() non ri-sostituisce testo gia' matchato (no cascading)
        // e gestisce automaticamente la priorita' per lunghezza
        return strtr( $html, $expanded );
    }

    /**
     * Costruisce la mappa globale di stringhe: originale → traduzione.
     * Usa template_id = 0 per le stringhe "globali" (non legate a template).
     */
    private function get_global_string_map() {
        if ( $this->global_string_map !== null ) {
            return $this->global_string_map;
        }

        $lang = Olo_Lang_Language::detect_current_lang();
        $db   = new Olo_Lang_Database();

        // Carica tutte le traduzioni globali (template_id = 0) per la lingua corrente
        $raw_map = $db->get_translation_map( 0, $lang );

        $this->global_string_map = [];
        foreach ( $raw_map as $key => $row ) {
            if ( ! empty( $row['translation'] ) && $row['status'] !== 'bozza' ) {
                $original = $row['original'] ?? '';
                $translation = trim( $row['translation'] );
                // Protezione: ignora stringhe troppo corte (< 4 char) — rischio match falsi positivi in URL/HTML
                if ( mb_strlen( $original ) < 4 ) {
                    continue;
                }
                if ( $original !== '' && $translation !== '' && $original !== $translation ) {
                    $this->global_string_map[ $original ] = $translation;
                }
            }
        }

        return $this->global_string_map;
    }

    // =========================================================================
    // JS Localization — passa stringhe tradotte al JavaScript
    // =========================================================================

    /**
     * Se olo-booking e' accodato, inietta le stringhe tradotte via wp_localize_script.
     */
    public function localize_js_strings() {
        $lang    = Olo_Lang_Language::detect_current_lang();
        $default = Olo_Lang_Language::get_default_lang();

        if ( $lang === $default ) {
            return;
        }

        // Prepara mappa stringhe JS da DB (template_id=0, tipo js)
        $map = $this->get_global_string_map();
        if ( empty( $map ) ) {
            return;
        }

        // Olo Booking: se lo script e' accodato, inietta le traduzioni
        if ( wp_script_is( 'olo-book-front', 'enqueued' ) || wp_script_is( 'olo-book-front', 'registered' ) ) {
            wp_localize_script( 'olo-book-front', 'oloLangStrings', $map );
        }

        // Inietta anche come variabile globale per qualsiasi script
        wp_add_inline_script( 'olo-lang-frontend', 'window.oloLangStrings = ' . wp_json_encode( $map ) . ';', 'before' );
    }

    // =========================================================================
    // Selettore lingua + hreflang
    // =========================================================================

    public function render_language_switcher() {
        $config    = Olo_Lang_Language::get_config();
        $languages = Olo_Lang_Language::get_active_languages();

        if ( empty( $config['show_switcher'] ) || count( $languages ) < 2 ) {
            return;
        }

        $current  = Olo_Lang_Language::detect_current_lang();
        $style    = $config['switcher_style'] ?? 'dropdown';
        $position = $config['switcher_position'] ?? 'bottom-right';

        echo '<div class="olo-lang-switcher olo-lang-switcher--' . esc_attr( $style ) . ' olo-lang-pos--' . esc_attr( $position ) . '">';

        if ( $style === 'inline' ) {
            foreach ( $languages as $lang ) {
                $active = $lang['code'] === $current ? ' olo-lang-active' : '';
                $url    = Olo_Lang_Language::get_language_url( $lang['code'] );
                echo '<a href="' . esc_url( $url ) . '" class="olo-lang-link' . $active . '" hreflang="' . esc_attr( $lang['code'] ) . '">';
                echo '<span class="olo-lang-code">' . esc_html( strtoupper( $lang['code'] ) ) . '</span>';
                echo '</a>';
            }
        } else {
            $current_lang = null;
            foreach ( $languages as $lang ) {
                if ( $lang['code'] === $current ) {
                    $current_lang = $lang;
                    break;
                }
            }
            if ( ! $current_lang ) {
                $current_lang = $languages[0];
            }

            echo '<div class="olo-lang-dropdown">';
            echo '<button class="olo-lang-current" type="button">';
            echo '<span class="olo-lang-code">' . esc_html( strtoupper( $current_lang['code'] ) ) . '</span>';
            echo '<span class="olo-lang-name">' . esc_html( $current_lang['name'] ) . '</span>';
            echo '<svg width="12" height="12" viewBox="0 0 12 12"><path d="M2 4l4 4 4-4" fill="none" stroke="currentColor" stroke-width="1.5"/></svg>';
            echo '</button>';
            echo '<div class="olo-lang-menu">';
            foreach ( $languages as $lang ) {
                if ( $lang['code'] === $current ) {
                    continue;
                }
                $url = Olo_Lang_Language::get_language_url( $lang['code'] );
                echo '<a href="' . esc_url( $url ) . '" class="olo-lang-option" hreflang="' . esc_attr( $lang['code'] ) . '">';
                echo '<span class="olo-lang-code">' . esc_html( strtoupper( $lang['code'] ) ) . '</span>';
                echo '<span class="olo-lang-name">' . esc_html( $lang['name'] ) . '</span>';
                echo '</a>';
            }
            echo '</div></div>';
        }

        echo '</div>';
    }

    public function switcher_shortcode( $atts ) {
        ob_start();
        $this->render_language_switcher();
        return ob_get_clean();
    }

    public function output_hreflang_tags() {
        $languages = Olo_Lang_Language::get_active_languages();
        if ( count( $languages ) < 2 ) {
            return;
        }

        foreach ( $languages as $lang ) {
            $url = Olo_Lang_Language::get_language_url( $lang['code'] );
            echo '<link rel="alternate" hreflang="' . esc_attr( $lang['code'] ) . '" href="' . esc_url( $url ) . '">' . "\n";
        }
        $default_url = Olo_Lang_Language::get_language_url( Olo_Lang_Language::get_default_lang() );
        echo '<link rel="alternate" hreflang="x-default" href="' . esc_url( $default_url ) . '">' . "\n";
    }

    // =========================================================================
    // Routing: strip prefisso lingua da REQUEST_URI (approccio TranslatePress-like)
    // =========================================================================

    /**
     * Strip del prefisso lingua da REQUEST_URI.
     * Deve girare PRIMA che WordPress faccia il parse della richiesta.
     * Cosi' /en/baite diventa /baite e WP lo gestisce nativamente
     * (funziona per pages, posts, CPT, archivi, search, ecc.)
     */
    private function strip_lang_prefix() {
        $codes   = Olo_Lang_Language::get_language_codes();
        $default = Olo_Lang_Language::get_default_lang();
        $uri     = $_SERVER['REQUEST_URI'] ?? '/';
        $path    = wp_parse_url( $uri, PHP_URL_PATH ) ?: '/';

        foreach ( $codes as $code ) {
            if ( $code === $default ) {
                continue;
            }

            // Matcha /en, /en/, /en/qualcosa
            if ( preg_match( '#^/' . preg_quote( $code, '#' ) . '(/|$)#', $path ) ) {
                // Salva URI originale per riferimento (hreflang, ecc.)
                $_SERVER['OLO_ORIGINAL_URI'] = $uri;

                // Rimuovi il prefisso lingua dal path
                $new_path = preg_replace( '#^/' . preg_quote( $code, '#' ) . '#', '', $path );
                if ( $new_path === '' ) {
                    $new_path = '/';
                }

                // Ricostruisci l'URI con query string se presente
                $query = wp_parse_url( $uri, PHP_URL_QUERY );
                $_SERVER['REQUEST_URI'] = $new_path . ( $query ? '?' . $query : '' );

                // Salva la lingua rilevata per detect_current_lang()
                if ( ! defined( 'OLO_LANG_DETECTED' ) ) {
                    define( 'OLO_LANG_DETECTED', $code );
                }

                break;
            }
        }
    }

    /**
     * Previene il redirect canonico di WordPress per URL con prefisso lingua.
     * Dopo lo strip, il REDIRECT_URL originale (usato da redirect_canonical)
     * potrebbe ancora contenere il prefisso — blocchiamo il redirect.
     */
    public function prevent_lang_redirect( $redirect_url, $requested_url ) {
        // Se abbiamo rilevato una lingua via strip, non fare redirect
        if ( defined( 'OLO_LANG_DETECTED' ) ) {
            return false;
        }

        return $redirect_url;
    }

    // =========================================================================
    // Assets
    // =========================================================================

    public function enqueue_frontend_assets() {
        $languages = Olo_Lang_Language::get_active_languages();
        if ( count( $languages ) < 2 ) {
            return;
        }

        wp_enqueue_style(
            'olo-lang-frontend',
            OLO_LANG_URL . 'assets/css/frontend.css',
            [],
            OLO_LANG_VERSION
        );

        wp_enqueue_script(
            'olo-lang-frontend',
            OLO_LANG_URL . 'assets/js/frontend.js',
            [],
            OLO_LANG_VERSION,
            true
        );
    }
}
