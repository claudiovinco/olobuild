<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Olo_AI_Assistant
 *
 * Gestisce le chiamate API AI (Anthropic Claude) per generazione testo,
 * miglioramento testo, traduzione, layout, stile, alt text e CSS.
 * La generazione immagini usa OpenAI DALL-E (chiave separata, opzionale).
 */
class Olo_AI_Assistant {

    private static $namespace = 'olo/v1';

    /**
     * Auto-init su plugins_loaded
     */
    public static function init() {
        add_action( 'rest_api_init', [ __CLASS__, 'register_routes' ] );
    }

    /**
     * Registra tutti gli endpoint REST AI
     */
    public static function register_routes() {
        // Genera testo
        register_rest_route( self::$namespace, '/ai/generate-text', [
            'methods'             => 'POST',
            'callback'            => [ __CLASS__, 'generate_text' ],
            'permission_callback' => [ __CLASS__, 'check_permission' ],
        ] );

        // Migliora testo
        register_rest_route( self::$namespace, '/ai/improve-text', [
            'methods'             => 'POST',
            'callback'            => [ __CLASS__, 'improve_text' ],
            'permission_callback' => [ __CLASS__, 'check_permission' ],
        ] );

        // Traduci testo
        register_rest_route( self::$namespace, '/ai/translate-text', [
            'methods'             => 'POST',
            'callback'            => [ __CLASS__, 'translate_text' ],
            'permission_callback' => [ __CLASS__, 'check_permission' ],
        ] );

        // Genera immagine
        register_rest_route( self::$namespace, '/ai/generate-image', [
            'methods'             => 'POST',
            'callback'            => [ __CLASS__, 'generate_image' ],
            'permission_callback' => [ __CLASS__, 'check_permission' ],
        ] );

        // Genera layout
        register_rest_route( self::$namespace, '/ai/generate-layout', [
            'methods'             => 'POST',
            'callback'            => [ __CLASS__, 'generate_layout' ],
            'permission_callback' => [ __CLASS__, 'check_permission' ],
        ] );

        // Suggerisci stile
        register_rest_route( self::$namespace, '/ai/suggest-style', [
            'methods'             => 'POST',
            'callback'            => [ __CLASS__, 'suggest_style' ],
            'permission_callback' => [ __CLASS__, 'check_permission' ],
        ] );

        // Genera alt text SEO
        register_rest_route( self::$namespace, '/ai/generate-alt', [
            'methods'             => 'POST',
            'callback'            => [ __CLASS__, 'generate_alt' ],
            'permission_callback' => [ __CLASS__, 'check_permission' ],
        ] );

        // Genera CSS
        register_rest_route( self::$namespace, '/ai/generate-css', [
            'methods'             => 'POST',
            'callback'            => [ __CLASS__, 'generate_css' ],
            'permission_callback' => [ __CLASS__, 'check_permission' ],
        ] );

        // Impostazioni AI (GET + PUT)
        register_rest_route( self::$namespace, '/ai/settings', [
            [
                'methods'             => 'GET',
                'callback'            => [ __CLASS__, 'get_settings' ],
                'permission_callback' => [ __CLASS__, 'check_permission' ],
            ],
            [
                'methods'             => 'PUT',
                'callback'            => [ __CLASS__, 'save_settings' ],
                'permission_callback' => [ __CLASS__, 'check_permission' ],
            ],
        ] );
    }

    /**
     * Permessi: solo utenti che possono edit_pages
     */
    public static function check_permission() {
        return current_user_can( 'edit_pages' );
    }

    // ──────────────────────────────────────────────────
    //  GENERATE TEXT
    // ──────────────────────────────────────────────────

    public static function generate_text( $request ) {
        $prompt     = sanitize_textarea_field( $request->get_param( 'prompt' ) );
        $type       = sanitize_text_field( $request->get_param( 'type' ) ?: 'paragraph' );
        $tone       = sanitize_text_field( $request->get_param( 'tone' ) ?: 'professionale' );
        $language   = sanitize_text_field( $request->get_param( 'language' ) ?: 'it' );
        $max_length = absint( $request->get_param( 'max_length' ) ?: 150 );

        if ( empty( $prompt ) ) {
            return new WP_Error( 'missing_prompt', 'Il prompt è obbligatorio.', [ 'status' => 400 ] );
        }

        $system = self::build_system_prompt( $type, $tone, $language, $max_length );

        $result = self::call_chat_api( $system, $prompt );
        if ( is_wp_error( $result ) ) {
            return $result;
        }

        return rest_ensure_response( [
            'text'   => $result,
            'type'   => $type,
            'tone'   => $tone,
            'language' => $language,
        ] );
    }

    // ──────────────────────────────────────────────────
    //  IMPROVE TEXT
    // ──────────────────────────────────────────────────

    public static function improve_text( $request ) {
        $text   = sanitize_textarea_field( $request->get_param( 'text' ) );
        $action = sanitize_text_field( $request->get_param( 'action' ) ?: 'rephrase' );

        if ( empty( $text ) ) {
            return new WP_Error( 'missing_text', 'Il testo è obbligatorio.', [ 'status' => 400 ] );
        }

        $instructions = [
            'rephrase'          => 'Riformula il seguente testo mantenendo il significato originale ma usando parole e strutture diverse. Rispondi solo con il testo riformulato, senza commenti aggiuntivi.',
            'shorten'           => 'Accorcia il seguente testo mantenendo i concetti chiave. Riduci la lunghezza di almeno il 30%. Rispondi solo con il testo accorciato.',
            'expand'            => 'Espandi il seguente testo aggiungendo dettagli, esempi o approfondimenti. Aumenta la lunghezza di almeno il 50%. Rispondi solo con il testo espanso.',
            'fix_grammar'       => 'Correggi eventuali errori grammaticali, ortografici e di punteggiatura nel seguente testo. Rispondi solo con il testo corretto.',
            'make_professional' => 'Riscrivi il seguente testo con un tono professionale e formale, adatto a comunicazioni aziendali. Rispondi solo con il testo riscritto.',
            'make_casual'       => 'Riscrivi il seguente testo con un tono informale e amichevole, come se stessi parlando con un amico. Rispondi solo con il testo riscritto.',
        ];

        $system = isset( $instructions[ $action ] )
            ? $instructions[ $action ]
            : $instructions['rephrase'];

        $result = self::call_chat_api( $system, $text );
        if ( is_wp_error( $result ) ) {
            return $result;
        }

        return rest_ensure_response( [
            'text'   => $result,
            'action' => $action,
        ] );
    }

    // ──────────────────────────────────────────────────
    //  TRANSLATE TEXT
    // ──────────────────────────────────────────────────

    public static function translate_text( $request ) {
        $text            = sanitize_textarea_field( $request->get_param( 'text' ) );
        $target_language = sanitize_text_field( $request->get_param( 'target_language' ) ?: 'en' );

        if ( empty( $text ) ) {
            return new WP_Error( 'missing_text', 'Il testo è obbligatorio.', [ 'status' => 400 ] );
        }

        $lang_names = [
            'it' => 'italiano',
            'en' => 'inglese',
            'de' => 'tedesco',
            'fr' => 'francese',
            'es' => 'spagnolo',
        ];

        $lang_name = isset( $lang_names[ $target_language ] )
            ? $lang_names[ $target_language ]
            : $target_language;

        $system = "Sei un traduttore professionista. Traduci il seguente testo in {$lang_name}. "
                . "Mantieni il formato originale (se è HTML conserva i tag). "
                . "Rispondi SOLO con la traduzione, senza commenti o note aggiuntive.";

        $result = self::call_chat_api( $system, $text );
        if ( is_wp_error( $result ) ) {
            return $result;
        }

        return rest_ensure_response( [
            'text'            => $result,
            'target_language' => $target_language,
        ] );
    }

    // ──────────────────────────────────────────────────
    //  GENERATE IMAGE
    // ──────────────────────────────────────────────────

    public static function generate_image( $request ) {
        $prompt = sanitize_textarea_field( $request->get_param( 'prompt' ) );
        $size   = sanitize_text_field( $request->get_param( 'size' ) ?: '1024x1024' );
        $style  = sanitize_text_field( $request->get_param( 'style' ) ?: 'vivid' );

        if ( empty( $prompt ) ) {
            return new WP_Error( 'missing_prompt', 'Il prompt è obbligatorio.', [ 'status' => 400 ] );
        }

        $allowed_sizes = [ '1024x1024', '1792x1024', '1024x1792' ];
        if ( ! in_array( $size, $allowed_sizes, true ) ) {
            $size = '1024x1024';
        }

        $allowed_styles = [ 'vivid', 'natural' ];
        if ( ! in_array( $style, $allowed_styles, true ) ) {
            $style = 'vivid';
        }

        $api_key = get_option( 'olo_ai_openai_key', '' );
        if ( empty( $api_key ) ) {
            return new WP_Error( 'no_api_key', 'Chiave API OpenAI non configurata. Vai nelle impostazioni AI.', [ 'status' => 400 ] );
        }

        $image_model = get_option( 'olo_ai_image_model', 'dall-e-3' );

        $body = [
            'model'  => $image_model,
            'prompt' => $prompt,
            'n'      => 1,
            'size'   => $size,
        ];

        // DALL-E 3 supporta lo stile, DALL-E 2 no
        if ( $image_model === 'dall-e-3' ) {
            $body['style'] = $style;
        }

        $response = wp_remote_post( 'https://api.openai.com/v1/images/generations', [
            'timeout' => 120,
            'headers' => [
                'Content-Type'  => 'application/json',
                'Authorization' => 'Bearer ' . $api_key,
            ],
            'body' => wp_json_encode( $body ),
        ] );

        if ( is_wp_error( $response ) ) {
            return new WP_Error( 'api_error', 'Errore nella chiamata API: ' . $response->get_error_message(), [ 'status' => 500 ] );
        }

        $status_code = wp_remote_retrieve_response_code( $response );
        $resp_body   = json_decode( wp_remote_retrieve_body( $response ), true );

        if ( $status_code !== 200 ) {
            $error_msg = isset( $resp_body['error']['message'] )
                ? $resp_body['error']['message']
                : 'Errore sconosciuto dall\'API OpenAI (HTTP ' . $status_code . ')';
            return new WP_Error( 'api_error', $error_msg, [ 'status' => $status_code ] );
        }

        if ( empty( $resp_body['data'][0]['url'] ) ) {
            return new WP_Error( 'no_image', 'Nessuna immagine generata.', [ 'status' => 500 ] );
        }

        $image_url = $resp_body['data'][0]['url'];

        // Scarica e salva nella Media Library WP
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';

        // Scarica il file temporaneo
        $tmp_file = download_url( $image_url );
        if ( is_wp_error( $tmp_file ) ) {
            return new WP_Error( 'download_error', 'Impossibile scaricare l\'immagine generata.', [ 'status' => 500 ] );
        }

        $file_array = [
            'name'     => 'ai-generated-' . time() . '.png',
            'tmp_name' => $tmp_file,
        ];

        $attachment_id = media_handle_sideload( $file_array, 0, 'Immagine AI: ' . wp_trim_words( $prompt, 10 ) );

        if ( is_wp_error( $attachment_id ) ) {
            @unlink( $tmp_file );
            return new WP_Error( 'sideload_error', 'Impossibile salvare l\'immagine nella Media Library.', [ 'status' => 500 ] );
        }

        $saved_url = wp_get_attachment_url( $attachment_id );

        return rest_ensure_response( [
            'url'           => $saved_url,
            'attachment_id' => $attachment_id,
            'prompt'        => $prompt,
        ] );
    }

    // ──────────────────────────────────────────────────
    //  GENERATE LAYOUT
    // ──────────────────────────────────────────────────

    public static function generate_layout( $request ) {
        $prompt  = sanitize_textarea_field( $request->get_param( 'prompt' ) );
        $style   = sanitize_text_field( $request->get_param( 'style' ) ?: 'corporate' );
        $columns = absint( $request->get_param( 'columns' ) ?: 2 );

        if ( empty( $prompt ) ) {
            return new WP_Error( 'missing_prompt', 'Il prompt è obbligatorio.', [ 'status' => 400 ] );
        }

        $system = 'Sei un esperto web designer. Genera una struttura layout per un page builder in formato JSON. '
                . 'La struttura deve essere un array di nodi. Ogni nodo ha: "type" (section, row, column, headline, content, image, button), '
                . '"settings" (oggetto con proprietà come "text", "content", "title", "image_url", "link", "style"), '
                . '"children" (array di nodi figli, solo per section/row/column). '
                . 'Stile richiesto: ' . $style . '. Colonne: ' . $columns . '. '
                . 'Rispondi SOLO con il JSON valido, senza commenti, senza markdown, senza ```json. '
                . 'Esempio minimo: [{"type":"section","settings":{"style":"default"},"children":[{"type":"row","settings":{},"children":[{"type":"column","settings":{},"children":[{"type":"headline","settings":{"text":"Titolo"}}]}]}]}]';

        $result = self::call_chat_api( $system, $prompt );
        if ( is_wp_error( $result ) ) {
            return $result;
        }

        // Prova a decodificare il JSON
        $structure = json_decode( $result, true );
        if ( ! is_array( $structure ) ) {
            // Tenta di estrarre JSON dalla risposta
            if ( preg_match( '/\[[\s\S]*\]/', $result, $matches ) ) {
                $structure = json_decode( $matches[0], true );
            }
            if ( ! is_array( $structure ) ) {
                return new WP_Error( 'invalid_json', 'L\'AI non ha generato un JSON valido. Riprova.', [ 'status' => 500 ] );
            }
        }

        return rest_ensure_response( [
            'structure' => $structure,
        ] );
    }

    // ──────────────────────────────────────────────────
    //  SUGGEST STYLE
    // ──────────────────────────────────────────────────

    public static function suggest_style( $request ) {
        $palette        = sanitize_text_field( $request->get_param( 'palette' ) ?: 'auto' );
        $current_colors = $request->get_param( 'current_colors' );

        if ( ! is_array( $current_colors ) ) {
            $current_colors = [];
        }
        $current_colors = array_map( 'sanitize_hex_color', $current_colors );

        $colors_desc = ! empty( $current_colors )
            ? 'I colori attuali del sito sono: ' . implode( ', ', array_filter( $current_colors ) ) . '. '
            : 'Non ci sono colori attuali definiti. ';

        $palette_desc = [
            'auto'    => 'Analizza i colori attuali e suggerisci palette complementari e armoniche.',
            'warm'    => 'Genera palette con toni caldi (rossi, arancioni, gialli, marroni).',
            'cool'    => 'Genera palette con toni freddi (blu, azzurri, verdi, viola).',
            'pastel'  => 'Genera palette con colori pastello morbidi e delicati.',
            'dark'    => 'Genera palette adatte a un tema dark mode con sfondi scuri.',
            'vibrant' => 'Genera palette con colori vivaci, saturi e impattanti.',
        ];

        $palette_text = isset( $palette_desc[ $palette ] ) ? $palette_desc[ $palette ] : $palette_desc['auto'];

        $system = 'Sei un esperto di design e color theory. ' . $colors_desc . $palette_text . ' '
                . 'Rispondi SOLO in JSON valido con questa struttura (senza markdown, senza ```): '
                . '{"suggestions":[{"name":"Nome Palette","colors":["#hex1","#hex2","#hex3","#hex4","#hex5"],"fonts":["Font1","Font2"]}]} '
                . 'Genera esattamente 3 suggerimenti diversi. Ogni palette ha 5 colori: primary, secondary, text, background, accent. '
                . 'I font devono essere disponibili su Google Fonts.';

        $result = self::call_chat_api( $system, 'Suggerisci 3 palette di colori per il mio sito web.' );
        if ( is_wp_error( $result ) ) {
            return $result;
        }

        $data = json_decode( $result, true );
        if ( ! is_array( $data ) || ! isset( $data['suggestions'] ) ) {
            // Tenta di estrarre JSON
            if ( preg_match( '/\{[\s\S]*\}/', $result, $matches ) ) {
                $data = json_decode( $matches[0], true );
            }
            if ( ! is_array( $data ) || ! isset( $data['suggestions'] ) ) {
                return new WP_Error( 'invalid_json', 'L\'AI non ha generato un JSON valido. Riprova.', [ 'status' => 500 ] );
            }
        }

        return rest_ensure_response( $data );
    }

    // ──────────────────────────────────────────────────
    //  GENERATE ALT TEXT
    // ──────────────────────────────────────────────────

    public static function generate_alt( $request ) {
        $image_url = esc_url_raw( $request->get_param( 'image_url' ) );
        $language  = sanitize_text_field( $request->get_param( 'language' ) ?: 'it' );

        if ( empty( $image_url ) ) {
            return new WP_Error( 'missing_url', 'L\'URL dell\'immagine è obbligatorio.', [ 'status' => 400 ] );
        }

        $lang_names = [
            'it' => 'italiano',
            'en' => 'inglese',
            'de' => 'tedesco',
            'fr' => 'francese',
            'es' => 'spagnolo',
        ];
        $lang = isset( $lang_names[ $language ] ) ? $lang_names[ $language ] : 'italiano';

        $api_key = get_option( 'olo_ai_anthropic_key', '' );
        if ( empty( $api_key ) ) {
            return new WP_Error( 'no_api_key', 'Chiave API Anthropic non configurata.', [ 'status' => 400 ] );
        }

        $model = get_option( 'olo_ai_model', 'claude-sonnet-4-6' );

        // Scarica l'immagine e convertila in base64 per Claude Vision
        $img_response = wp_remote_get( $image_url, [ 'timeout' => 30 ] );
        if ( is_wp_error( $img_response ) ) {
            return new WP_Error( 'download_error', 'Impossibile scaricare l\'immagine: ' . $img_response->get_error_message(), [ 'status' => 500 ] );
        }

        $img_body = wp_remote_retrieve_body( $img_response );
        $content_type = wp_remote_retrieve_header( $img_response, 'content-type' );
        if ( empty( $img_body ) ) {
            return new WP_Error( 'download_error', 'Immagine vuota o non accessibile.', [ 'status' => 500 ] );
        }

        // Determina il media type
        $media_type = 'image/jpeg';
        if ( strpos( $content_type, 'png' ) !== false ) {
            $media_type = 'image/png';
        } elseif ( strpos( $content_type, 'gif' ) !== false ) {
            $media_type = 'image/gif';
        } elseif ( strpos( $content_type, 'webp' ) !== false ) {
            $media_type = 'image/webp';
        }

        $base64_img = base64_encode( $img_body );

        $system_prompt = "Sei un esperto SEO. Genera un alt text descrittivo e SEO-friendly per l'immagine fornita. "
                       . "Scrivi in {$lang}. L'alt text deve essere conciso (max 125 caratteri), descrittivo e ottimizzato per i motori di ricerca. "
                       . "Rispondi SOLO con l'alt text, senza virgolette.";

        $response = wp_remote_post( 'https://api.anthropic.com/v1/messages', [
            'timeout' => 60,
            'headers' => [
                'Content-Type'      => 'application/json',
                'x-api-key'         => $api_key,
                'anthropic-version'  => '2023-06-01',
            ],
            'body' => wp_json_encode( [
                'model'      => $model,
                'max_tokens' => 200,
                'system'     => $system_prompt,
                'messages'   => [
                    [
                        'role'    => 'user',
                        'content' => [
                            [
                                'type'   => 'image',
                                'source' => [
                                    'type'         => 'base64',
                                    'media_type'   => $media_type,
                                    'data'         => $base64_img,
                                ],
                            ],
                            [
                                'type' => 'text',
                                'text' => 'Genera un alt text SEO per questa immagine.',
                            ],
                        ],
                    ],
                ],
            ] ),
        ] );

        if ( is_wp_error( $response ) ) {
            return new WP_Error( 'api_error', 'Errore nella chiamata API: ' . $response->get_error_message(), [ 'status' => 500 ] );
        }

        $status_code = wp_remote_retrieve_response_code( $response );
        $resp_body   = json_decode( wp_remote_retrieve_body( $response ), true );

        if ( $status_code !== 200 ) {
            $error_msg = isset( $resp_body['error']['message'] )
                ? $resp_body['error']['message']
                : 'Errore API Anthropic (HTTP ' . $status_code . ')';
            return new WP_Error( 'api_error', $error_msg, [ 'status' => $status_code ] );
        }

        if ( empty( $resp_body['content'][0]['text'] ) ) {
            return new WP_Error( 'empty_response', 'L\'API non ha restituito alcun contenuto.', [ 'status' => 500 ] );
        }

        $alt_text = trim( $resp_body['content'][0]['text'] );
        $alt_text = trim( $alt_text, "\"'" );

        return rest_ensure_response( [
            'text'      => $alt_text,
            'image_url' => $image_url,
        ] );
    }

    // ──────────────────────────────────────────────────
    //  GENERATE CSS
    // ──────────────────────────────────────────────────

    public static function generate_css( $request ) {
        $prompt   = sanitize_textarea_field( $request->get_param( 'prompt' ) );
        $selector = sanitize_text_field( $request->get_param( 'selector' ) );

        if ( empty( $prompt ) ) {
            return new WP_Error( 'missing_prompt', 'La descrizione dello stile è obbligatoria.', [ 'status' => 400 ] );
        }

        $selector_text = ! empty( $selector )
            ? "Usa il selettore CSS: {$selector}. "
            : 'Usa un selettore generico tipo .custom-style. ';

        $system = 'Sei un esperto CSS developer. Genera codice CSS pulito e moderno basato sulla descrizione dell\'utente. '
                . $selector_text
                . 'Usa proprietà CSS moderne (flexbox, grid, custom properties, etc.) quando appropriato. '
                . 'Rispondi SOLO con il codice CSS puro, senza commenti, senza spiegazioni, senza markdown, senza ```. '
                . 'Il CSS deve essere valido e pronto per essere incollato in un foglio di stile.';

        $result = self::call_chat_api( $system, $prompt );
        if ( is_wp_error( $result ) ) {
            return $result;
        }

        // Pulisci eventuali backtick markdown dalla risposta
        $css = trim( $result );
        $css = preg_replace( '/^```(?:css)?\s*/i', '', $css );
        $css = preg_replace( '/\s*```$/', '', $css );

        return rest_ensure_response( [
            'css'    => $css,
            'prompt' => $prompt,
        ] );
    }

    // ──────────────────────────────────────────────────
    //  SETTINGS
    // ──────────────────────────────────────────────────

    public static function get_settings( $request ) {
        $anthropic_key = get_option( 'olo_ai_anthropic_key', '' );
        $openai_key    = get_option( 'olo_ai_openai_key', '' );
        $model         = get_option( 'olo_ai_model', 'claude-sonnet-4-6' );
        $image_model   = get_option( 'olo_ai_image_model', 'dall-e-3' );

        // Maschera le key: mostra solo gli ultimi 4 caratteri
        $masked_anthropic = '';
        if ( ! empty( $anthropic_key ) ) {
            $masked_anthropic = str_repeat( '*', max( 0, strlen( $anthropic_key ) - 4 ) ) . substr( $anthropic_key, -4 );
        }
        $masked_openai = '';
        if ( ! empty( $openai_key ) ) {
            $masked_openai = str_repeat( '*', max( 0, strlen( $openai_key ) - 4 ) ) . substr( $openai_key, -4 );
        }

        return rest_ensure_response( [
            'anthropic_key' => $masked_anthropic,
            'openai_key'    => $masked_openai,
            'has_key'       => ! empty( $anthropic_key ),
            'has_openai_key' => ! empty( $openai_key ),
            'model'         => $model,
            'image_model'   => $image_model,
        ] );
    }

    public static function save_settings( $request ) {
        $anthropic_key = $request->get_param( 'anthropic_key' );
        $openai_key    = $request->get_param( 'openai_key' );
        $model         = sanitize_text_field( $request->get_param( 'model' ) ?: 'claude-sonnet-4-6' );
        $image_model   = sanitize_text_field( $request->get_param( 'image_model' ) ?: 'dall-e-3' );

        // Chiave Anthropic: salva se nuova, cancella se vuota
        if ( empty( $anthropic_key ) ) {
            delete_option( 'olo_ai_anthropic_key' );
        } elseif ( strpos( $anthropic_key, '*' ) === false ) {
            update_option( 'olo_ai_anthropic_key', sanitize_text_field( $anthropic_key ) );
        }

        // Chiave OpenAI: salva se nuova, cancella se vuota
        if ( empty( $openai_key ) ) {
            delete_option( 'olo_ai_openai_key' );
        } elseif ( strpos( $openai_key, '*' ) === false ) {
            update_option( 'olo_ai_openai_key', sanitize_text_field( $openai_key ) );
        }

        $allowed_models = [ 'claude-sonnet-4-6', 'claude-haiku-4-5-20251001', 'claude-opus-4-6' ];
        if ( in_array( $model, $allowed_models, true ) ) {
            update_option( 'olo_ai_model', $model );
        }

        $allowed_image_models = [ 'dall-e-3', 'dall-e-2' ];
        if ( in_array( $image_model, $allowed_image_models, true ) ) {
            update_option( 'olo_ai_image_model', $image_model );
        }

        return rest_ensure_response( [
            'success'     => true,
            'model'       => get_option( 'olo_ai_model', 'claude-sonnet-4-6' ),
            'image_model' => get_option( 'olo_ai_image_model', 'dall-e-3' ),
        ] );
    }

    // ──────────────────────────────────────────────────
    //  HELPERS
    // ──────────────────────────────────────────────────

    /**
     * Costruisce il system prompt per la generazione testo
     */
    private static function build_system_prompt( $type, $tone, $language, $max_length ) {
        $lang_names = [
            'it' => 'italiano',
            'en' => 'inglese',
            'de' => 'tedesco',
            'fr' => 'francese',
            'es' => 'spagnolo',
        ];
        $lang = isset( $lang_names[ $language ] ) ? $lang_names[ $language ] : 'italiano';

        $tone_desc = [
            'professionale' => 'professionale e autorevole',
            'creativo'      => 'creativo e originale',
            'informale'     => 'informale e colloquiale',
            'formale'       => 'formale e istituzionale',
        ];
        $tone_text = isset( $tone_desc[ $tone ] ) ? $tone_desc[ $tone ] : 'professionale e autorevole';

        $type_instructions = [
            'headline'        => "Genera un titolo accattivante per un sito web. Il titolo deve essere breve, d'impatto e ottimizzato per catturare l'attenzione. Non usare virgolette intorno al titolo. Rispondi solo con il titolo.",
            'paragraph'       => "Scrivi un paragrafo persuasivo per un sito web. Il testo deve essere coinvolgente e informativo. Rispondi solo con il paragrafo, senza titoli.",
            'list'            => "Genera una lista di punti per un sito web. Ogni punto deve iniziare con un trattino (-). Rispondi solo con la lista.",
            'cta'             => "Scrivi una call-to-action efficace per un sito web. Deve essere breve, diretta e motivare all'azione. Rispondi solo con il testo della CTA.",
            'seo_description' => "Scrivi una meta description SEO ottimizzata (massimo 160 caratteri). Deve includere la keyword principale e invogliare al click. Rispondi solo con la meta description.",
        ];

        $type_instruction = isset( $type_instructions[ $type ] )
            ? $type_instructions[ $type ]
            : $type_instructions['paragraph'];

        return "Sei un copywriter esperto per siti web. "
             . $type_instruction . " "
             . "Usa un tono {$tone_text}. "
             . "Scrivi in {$lang}. "
             . "Lunghezza massima: circa {$max_length} parole.";
    }

    /**
     * Chiama l'API Messages di Anthropic (Claude)
     */
    private static function call_chat_api( $system_prompt, $user_message ) {
        $api_key = get_option( 'olo_ai_anthropic_key', '' );
        if ( empty( $api_key ) ) {
            return new WP_Error( 'no_api_key', 'Chiave API Anthropic non configurata. Vai nelle impostazioni AI.', [ 'status' => 400 ] );
        }

        $model = get_option( 'olo_ai_model', 'claude-sonnet-4-6' );

        $response = wp_remote_post( 'https://api.anthropic.com/v1/messages', [
            'timeout' => 60,
            'headers' => [
                'Content-Type'      => 'application/json',
                'x-api-key'         => $api_key,
                'anthropic-version'  => '2023-06-01',
            ],
            'body' => wp_json_encode( [
                'model'      => $model,
                'max_tokens' => 2000,
                'system'     => $system_prompt,
                'messages'   => [
                    [ 'role' => 'user', 'content' => $user_message ],
                ],
            ] ),
        ] );

        if ( is_wp_error( $response ) ) {
            return new WP_Error( 'api_error', 'Errore nella chiamata API: ' . $response->get_error_message(), [ 'status' => 500 ] );
        }

        $status_code = wp_remote_retrieve_response_code( $response );
        $resp_body   = json_decode( wp_remote_retrieve_body( $response ), true );

        if ( $status_code !== 200 ) {
            $error_msg = isset( $resp_body['error']['message'] )
                ? $resp_body['error']['message']
                : 'Errore dall\'API Anthropic (HTTP ' . $status_code . ')';
            return new WP_Error( 'api_error', $error_msg, [ 'status' => $status_code ] );
        }

        if ( empty( $resp_body['content'][0]['text'] ) ) {
            return new WP_Error( 'empty_response', 'L\'API non ha restituito alcun contenuto.', [ 'status' => 500 ] );
        }

        return trim( $resp_body['content'][0]['text'] );
    }
}

// Auto-init: la classe si registra autonomamente quando il file viene incluso
add_action( 'plugins_loaded', [ 'Olo_AI_Assistant', 'init' ], 20 );
