<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Olobuild_AI_Assistant
 *
 * Gestisce le chiamate API AI (Anthropic Claude) per generazione testo,
 * miglioramento testo, traduzione, layout, stile, alt text e CSS.
 * La generazione immagini usa OpenAI DALL-E (chiave separata, opzionale).
 */
class Olobuild_AI_Assistant {

    private static $namespace = 'olobuild/v1';

    /**
     * Auto-init su plugins_loaded
     */
    public static function init() {
        add_action( 'rest_api_init', [ __CLASS__, 'register_routes' ] );
    }

    /**
     * Registra tutti gli endpoint REST AI
     */
    // ──────────────────────────────────────────────────
    //  MODELLI — fonte unica: la scheda AI della Configurazione li legge da
    //  GET /ai/settings e il salvataggio accetta solo questi. Prima la tendina
    //  offriva modelli che il salvataggio scartava in silenzio (restava sempre
    //  quello di prima) e OpenAI/Mistral non venivano mai chiamati.
    //  Aggiornati al 1 ott 2026 dalle pagine ufficiali dei tre fornitori.
    // ──────────────────────────────────────────────────

    /**
     * Modelli di testo per fornitore: id => [ etichetta, $ input/MTok, $ output/MTok, temperatura ].
     * Il primo di ogni elenco è il predefinito. Prezzi dai listini ufficiali (1 ott 2026):
     * servono alla spesa stimata e quindi al budget mensile. «temperatura» = il modello
     * la accetta: Claude Sonnet/Opus 5.5 e Fable 5.1 la rifiutano con un 400, di GPT-6
     * non è documentata; la scheda la mostra solo dove agisce.
     */
    const MODELLI = [
        'anthropic' => [
            'claude-sonnet-5-5'         => [ 'Claude Sonnet 5.5 · equilibrato (consigliato)', 2, 10, false ],
            'claude-opus-5-5'           => [ 'Claude Opus 5.5 · qualità alta', 4, 20, false ],
            'claude-fable-5-1'          => [ 'Claude Fable 5.1 · ragionamento più profondo, costo alto', 10, 50, false ],
            'claude-haiku-4-5-20251001' => [ 'Claude Haiku 4.5 · veloce, economico', 1, 5, true ],
        ],
        'openai'    => [
            'gpt-6.1-sol' => [ 'GPT-6.1 Sol · equilibrato (consigliato)', 2, 10, false ],
            'gpt-6-astra' => [ 'GPT-6 Astra · qualità massima, costo alto', 10, 50, false ],
            'gpt-6-luna'  => [ 'GPT-6 Luna · veloce, economico', 0.1, 0.5, false ],
        ],
        'mistral'   => [
            // Alias «-latest»: seguono da soli il modello corrente di ogni fascia.
            'mistral-large-latest'  => [ 'Mistral Large 3 · equilibrato (consigliato)', 0.5, 1.5, true ],
            'mistral-medium-latest' => [ 'Mistral Medium 3.5 · qualità più alta', 1.5, 7.5, true ],
            'mistral-small-latest'  => [ 'Mistral Small 4 · veloce, economico', 0.15, 0.6, true ],
        ],
    ];

    /** Generazioni precedenti ancora servite: chi le ha salvate continua a usarle. */
    const MODELLI_PRECEDENTI = [
        'anthropic' => [
            'claude-sonnet-4-6' => [ 'Claude Sonnet 4.6', 3, 15, false ],
            'claude-opus-4-6'   => [ 'Claude Opus 4.6', 5, 25, false ],
        ],
    ];

    /** GPT Image: prezzi a token ($/MTok) del testo in ingresso e dell'immagine in uscita. */
    const PREZZO_IMMAGINE = [ 'testo' => 5, 'immagine' => 30 ];

    /** Comportamento (scheda AI → Comportamento): valori ammessi e predefiniti. */
    const LINGUE = [ 'it' => 'italiano', 'en' => 'inglese', 'de' => 'tedesco', 'fr' => 'francese', 'es' => 'spagnolo' ];
    const TONI   = [
        'neutral'   => '',
        'warm'      => 'caldo, accogliente e vicino a chi legge',
        'technical' => 'tecnico, preciso e documentato',
        'editorial' => 'editoriale, curato e narrativo',
    ];
    const COMPORTAMENTO = [ 'budget' => 50, 'language' => 'it', 'tone' => 'neutral', 'temperature' => 0.35, 'system_prompt' => '' ];

    /** Claude senza il parametro effort (Haiku 4.5): gli altri lo ricevono basso. */
    const SENZA_EFFORT = [ 'claude-haiku-4-5-20251001' ];

    /**
     * Modelli immagine (OpenAI). DALL·E 2 e 3 sono spenti dal 12 maggio 2026: un
     * valore salvato con quei nomi passa al predefinito.
     */
    const MODELLI_IMMAGINE = [
        'gpt-image-2.5-flare'    => 'GPT Image 2.5 Flare · veloce (consigliato)',
        'gpt-image-2.5-sunburst' => 'GPT Image 2.5 Sunburst · qualità massima',
    ];

    const NOMI_FORNITORE = [ 'anthropic' => 'Anthropic', 'openai' => 'OpenAI', 'mistral' => 'Mistral' ];

    /** Cambio indicativo per la spesa stimata in euro (i listini sono in dollari). */
    const EUR_PER_USD = 0.92;

    /** Fornitore scelto (chi non l'ha mai salvato usa Anthropic, come prima). */
    public static function fornitore() {
        $p = get_option( 'olobuild_ai_provider', 'anthropic' );
        return isset( self::MODELLI[ $p ] ) ? $p : 'anthropic';
    }

    private static function info_modello( $fornitore, $modello ) {
        if ( isset( self::MODELLI[ $fornitore ][ $modello ] ) ) {
            return self::MODELLI[ $fornitore ][ $modello ];
        }
        if ( isset( self::MODELLI_PRECEDENTI[ $fornitore ][ $modello ] ) ) {
            return self::MODELLI_PRECEDENTI[ $fornitore ][ $modello ];
        }
        return null;
    }

    /** Modello salvato se appartiene al fornitore, altrimenti il predefinito del fornitore. */
    public static function modello( $fornitore = null ) {
        $fornitore = $fornitore ?: self::fornitore();
        $m = get_option( 'olobuild_ai_model', '' );
        if ( self::info_modello( $fornitore, $m ) ) {
            return $m;
        }
        $ids = array_keys( self::MODELLI[ $fornitore ] );
        return $ids[0];
    }

    public static function modello_immagine() {
        $m = get_option( 'olobuild_ai_image_model', '' );
        if ( isset( self::MODELLI_IMMAGINE[ $m ] ) ) {
            return $m;
        }
        $ids = array_keys( self::MODELLI_IMMAGINE );
        return $ids[0];
    }

    private static function chiave( $fornitore ) {
        return (string) get_option( 'olobuild_ai_' . $fornitore . '_key', '' );
    }

    /** Un valore del Comportamento, salvato o predefinito. */
    public static function comportamento( $chiave ) {
        $v = get_option( 'olobuild_ai_' . $chiave, null );
        return null === $v ? self::COMPORTAMENTO[ $chiave ] : $v;
    }

    /** Lingua predefinita, con «auto» risolta sulla lingua del sito (ripiego inglese). */
    public static function lingua_predefinita() {
        $l = (string) self::comportamento( 'language' );
        if ( 'auto' === $l ) {
            $l = substr( (string) get_locale(), 0, 2 );
        }
        return isset( self::LINGUE[ $l ] ) ? $l : ( 'auto' === self::comportamento( 'language' ) ? 'en' : 'it' );
    }

    /** Tono di base e istruzioni del sito, aggiunti a ogni istruzione di sistema. */
    private static function contesto_sito() {
        $out  = '';
        $tono = self::TONI[ (string) self::comportamento( 'tone' ) ] ?? '';
        if ( '' !== $tono ) {
            $out .= "\n\nTono di voce di base del sito (quando il compito non ne chiede un altro): " . $tono . '.';
        }
        $istr = trim( (string) self::comportamento( 'system_prompt' ) );
        if ( '' !== $istr ) {
            $out .= "\n\nContesto del sito, indicato dal proprietario (valido per ogni testo che scrivi):\n" . $istr;
        }
        return $out;
    }

    /** Inizio del mese corrente (fuso del sito). */
    private static function inizio_mese() {
        return ( new DateTimeImmutable( 'first day of this month 00:00:00', wp_timezone() ) )->getTimestamp();
    }

    /** Chiamate, token, spesa stimata (€) e latenza totale dal 1° del mese. */
    public static function utilizzo_mese() {
        $log  = get_option( 'olobuild_ai_usage', [] );
        $da   = self::inizio_mese();
        $out  = [ 'calls' => 0, 'tokens' => 0, 'cost' => 0.0, 'ms' => 0 ];
        foreach ( is_array( $log ) ? $log : [] as $e ) {
            if ( ! is_array( $e ) || ( $e['ts'] ?? 0 ) < $da ) {
                continue;
            }
            $out['calls']++;
            $out['tokens'] += (int) ( $e['tokens'] ?? 0 );
            $out['cost']   += (float) ( $e['cost'] ?? 0 );
            $out['ms']     += (int) ( $e['ms'] ?? 0 );
        }
        return $out;
    }

    /** Budget mensile superato: le funzioni AI si fermano fino al mese dopo (0 = nessun limite). */
    private static function controlla_budget() {
        $budget = (float) self::comportamento( 'budget' );
        if ( $budget <= 0 ) {
            return true;
        }
        $speso = self::utilizzo_mese()['cost'];
        if ( $speso >= $budget ) {
            return new WP_Error(
                'olo_ai_budget',
                sprintf(
                    'Budget mensile AI raggiunto: spesa stimata %s € su %s €. Le funzioni AI ripartono il 1° del mese, oppure alza il budget nella Configurazione → AI Assistant.',
                    number_format_i18n( $speso, 2 ),
                    number_format_i18n( $budget, 2 )
                ),
                [ 'status' => 429 ]
            );
        }
        return true;
    }

    /** C'è la chiave del fornitore scelto (il builder mostra l'assistente solo così). */
    public static function ha_chiave() {
        return '' !== self::chiave( self::fornitore() );
    }

    /**
     * Una chiamata di testo (con un'immagine facoltativa) al fornitore scelto.
     *
     * @param string     $system   Istruzioni di sistema.
     * @param string     $testo    Messaggio dell'utente.
     * @param array|null $immagine [ 'mime' => 'image/png', 'b64' => '...' ] oppure null.
     * @param int        $massimo  Limite dei token in uscita, ragionamento compreso.
     * @return string|WP_Error
     */
    private static function chiama_modello( $system, $testo, $immagine = null, $massimo = 4096 ) {
        $fornitore = self::fornitore();
        $nome      = self::NOMI_FORNITORE[ $fornitore ];
        $api_key   = self::chiave( $fornitore );
        if ( '' === $api_key ) {
            return new WP_Error( 'no_api_key', 'Chiave API ' . $nome . ' non configurata. Vai nelle impostazioni AI.', [ 'status' => 400 ] );
        }
        $budget = self::controlla_budget();
        if ( is_wp_error( $budget ) ) {
            return $budget;
        }
        $modello = self::modello( $fornitore );
        $info    = self::info_modello( $fornitore, $modello );
        $system .= self::contesto_sito();

        if ( 'anthropic' === $fornitore ) {
            $contenuto = [];
            if ( $immagine ) {
                $contenuto[] = [ 'type' => 'image', 'source' => [ 'type' => 'base64', 'media_type' => $immagine['mime'], 'data' => $immagine['b64'] ] ];
            }
            $contenuto[] = [ 'type' => 'text', 'text' => $testo ];
            $corpo = [
                'model'      => $modello,
                'max_tokens' => $massimo,
                'system'     => $system,
                'messages'   => [ [ 'role' => 'user', 'content' => $contenuto ] ],
            ];
            // Testi brevi: poco ragionamento. Sui modelli col ragionamento sempre acceso
            // (Opus 5.5, Fable 5.1) il ragionamento consuma max_tokens.
            if ( ! in_array( $modello, self::SENZA_EFFORT, true ) ) {
                $corpo['output_config'] = [ 'effort' => 'low' ];
            }
            if ( $info && ! empty( $info[3] ) ) {
                $corpo['temperature'] = (float) self::comportamento( 'temperature' );
            }
            $url   = 'https://api.anthropic.com/v1/messages';
            $intes = [ 'x-api-key' => $api_key, 'anthropic-version' => '2023-06-01' ];
        } else {
            // OpenAI e Mistral: stessa forma «chat completions».
            $parti = [ [ 'type' => 'text', 'text' => $testo ] ];
            if ( $immagine ) {
                $dati    = 'data:' . $immagine['mime'] . ';base64,' . $immagine['b64'];
                $parti[] = 'openai' === $fornitore
                    ? [ 'type' => 'image_url', 'image_url' => [ 'url' => $dati ] ]
                    : [ 'type' => 'image_url', 'image_url' => $dati ];
            }
            $corpo = [
                'model'    => $modello,
                'messages' => [
                    [ 'role' => 'system', 'content' => $system ],
                    [ 'role' => 'user', 'content' => $immagine ? $parti : $testo ],
                ],
            ];
            if ( 'openai' === $fornitore ) {
                // max_completion_tokens comprende il ragionamento; «low» vale per tutti i GPT-6.
                $corpo['max_completion_tokens'] = $massimo;
                $corpo['reasoning_effort']      = 'low';
                $url = 'https://api.openai.com/v1/chat/completions';
            } else {
                $corpo['max_tokens'] = $massimo;
                if ( $info && ! empty( $info[3] ) ) {
                    $corpo['temperature'] = (float) self::comportamento( 'temperature' );
                }
                $url = 'https://api.mistral.ai/v1/chat/completions';
            }
            $intes = [ 'Authorization' => 'Bearer ' . $api_key ];
        }

        $started  = microtime( true );
        $response = wp_remote_post( $url, [
            'timeout' => 90,
            'headers' => array_merge( [ 'Content-Type' => 'application/json' ], $intes ),
            'body'    => wp_json_encode( $corpo ),
        ] );
        $elapsed_ms = (int) ( ( microtime( true ) - $started ) * 1000 );

        if ( is_wp_error( $response ) ) {
            return new WP_Error( 'api_error', 'Errore nella chiamata API: ' . $response->get_error_message(), [ 'status' => 500 ] );
        }
        $status_code = (int) wp_remote_retrieve_response_code( $response );
        $resp_body   = json_decode( wp_remote_retrieve_body( $response ), true );
        if ( 200 !== $status_code ) {
            $msg = isset( $resp_body['error']['message'] ) ? $resp_body['error']['message']
                : ( isset( $resp_body['message'] ) && is_string( $resp_body['message'] ) ? $resp_body['message']
                : 'Errore dall\'API ' . $nome . ' (HTTP ' . $status_code . ')' );
            return new WP_Error( 'api_error', $msg, [ 'status' => $status_code ] );
        }

        $risposta = '';
        if ( 'anthropic' === $fornitore ) {
            // Con il ragionamento il primo blocco può essere «thinking»: si prende il testo.
            foreach ( (array) ( $resp_body['content'] ?? [] ) as $blocco ) {
                if ( isset( $blocco['type'], $blocco['text'] ) && 'text' === $blocco['type'] ) {
                    $risposta .= $blocco['text'];
                }
            }
            $in_tok  = (int) ( $resp_body['usage']['input_tokens'] ?? 0 );
            $out_tok = (int) ( $resp_body['usage']['output_tokens'] ?? 0 );
        } else {
            $c = $resp_body['choices'][0]['message']['content'] ?? '';
            if ( is_array( $c ) ) {
                // Mistral con ragionamento: pezzi tipizzati, si tiene il testo.
                foreach ( $c as $pezzo ) {
                    if ( isset( $pezzo['type'], $pezzo['text'] ) && 'text' === $pezzo['type'] ) {
                        $risposta .= $pezzo['text'];
                    }
                }
            } else {
                $risposta = (string) $c;
            }
            $in_tok  = (int) ( $resp_body['usage']['prompt_tokens'] ?? 0 );
            $out_tok = (int) ( $resp_body['usage']['completion_tokens'] ?? 0 );
        }

        $cost = ( $info && null !== $info[1] )
            ? ( ( $in_tok * $info[1] + $out_tok * $info[2] ) / 1000000 ) * self::EUR_PER_USD
            : 0.0;
        self::log_usage( $in_tok + $out_tok, $cost, $elapsed_ms );

        $risposta = trim( $risposta );
        if ( '' === $risposta ) {
            return new WP_Error( 'empty_response', 'L\'API non ha restituito alcun contenuto.', [ 'status' => 500 ] );
        }
        return $risposta;
    }

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
                'permission_callback' => [ __CLASS__, 'check_settings_permission' ],
            ],
        ] );
    }

    /**
     * Permessi: solo utenti che possono edit_pages.
     * Inoltre applica un rate-limit per-utente: gli endpoint AI chiamano provider
     * a pagamento, quindi un account compromesso non deve poter generare costi
     * illimitati. log_usage() e' solo contabilita' a posteriori, non un freno.
     */
    public static function check_permission( $request = null ) {
        if ( ! current_user_can( 'edit_pages' ) ) {
            return false;
        }
        // Default volutamente ampio (200/10min): gli endpoint di testo costano pochissimo e
        // una sessione di editing intensa ne genera molti; serve solo a fermare l'abuso runaway.
        // Il vero costo (generazione immagini) ha un cap dedicato e piu' stretto in generate_image().
        return self::rate_limit_guard(
            'all',
            apply_filters( 'olobuild_ai_rate_limit', 200 ),
            apply_filters( 'olobuild_ai_rate_window', 600 )
        );
    }

    /**
     * Rate-limit per-utente anti-abuso/costi (finestra fissa via transient: il contatore
     * parte dalla prima richiesta e si azzera dopo $window secondi).
     * Ritorna true oppure un WP_Error 429 (Too Many Requests). Con limit <= 0 e' disattivato.
     */
    private static function rate_limit_guard( $bucket, $limit, $window ) {
        $limit = (int) $limit;
        if ( $limit <= 0 ) {
            return true;
        }
        $key   = 'olo_ai_rl_' . $bucket . '_' . get_current_user_id();
        $count = (int) get_transient( $key );
        if ( $count >= $limit ) {
            return new WP_Error(
                'olo_ai_rate_limited',
                __( 'Hai raggiunto il limite di richieste AI. Riprova tra qualche minuto.', 'olobuild' ),
                [ 'status' => 429 ]
            );
        }
        set_transient( $key, $count + 1, (int) $window );
        return true;
    }

    /**
     * Scrittura impostazioni AI (chiavi API): solo admin, allineata agli altri
     * endpoint chiavi (manage_options). Un Editor poteva sovrascrivere/cancellare
     * le chiavi del sito. La lettura resta a edit_pages: le chiavi sono mascherate.
     */
    public static function check_settings_permission() {
        return current_user_can( 'manage_options' );
    }

    // ──────────────────────────────────────────────────
    //  GENERATE TEXT
    // ──────────────────────────────────────────────────

    public static function generate_text( $request ) {
        $prompt     = sanitize_textarea_field( $request->get_param( 'prompt' ) );
        $type       = sanitize_text_field( $request->get_param( 'type' ) ?: 'paragraph' );
        $tone       = sanitize_text_field( $request->get_param( 'tone' ) ?: 'professionale' );
        $language   = sanitize_text_field( $request->get_param( 'language' ) ?: self::lingua_predefinita() );
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
        // Cap dedicato: la generazione immagini e' l'endpoint a costo unitario piu' alto.
        $img_guard = self::rate_limit_guard(
            'img',
            apply_filters( 'olobuild_ai_image_rate_limit', 10 ),
            apply_filters( 'olobuild_ai_image_rate_window', 3600 )
        );
        if ( is_wp_error( $img_guard ) ) {
            return $img_guard;
        }

        $budget = self::controlla_budget();
        if ( is_wp_error( $budget ) ) {
            return $budget;
        }

        $prompt = sanitize_textarea_field( $request->get_param( 'prompt' ) );
        $size   = sanitize_text_field( $request->get_param( 'size' ) ?: '1024x1024' );
        $style  = sanitize_text_field( $request->get_param( 'style' ) ?: 'vivid' );

        if ( empty( $prompt ) ) {
            return new WP_Error( 'missing_prompt', 'Il prompt è obbligatorio.', [ 'status' => 400 ] );
        }

        // Formati di GPT Image; quelli di DALL·E (1792) passano al più vicino.
        $vecchi = [ '1792x1024' => '1536x1024', '1024x1792' => '1024x1536' ];
        if ( isset( $vecchi[ $size ] ) ) {
            $size = $vecchi[ $size ];
        }
        $allowed_sizes = [ '1024x1024', '1536x1024', '1024x1536' ];
        if ( ! in_array( $size, $allowed_sizes, true ) ) {
            $size = '1024x1024';
        }

        $allowed_styles = [ 'vivid', 'natural' ];
        if ( ! in_array( $style, $allowed_styles, true ) ) {
            $style = 'vivid';
        }

        $api_key = get_option( 'olobuild_ai_openai_key', '' );
        if ( empty( $api_key ) ) {
            return new WP_Error( 'no_api_key', 'Chiave API OpenAI non configurata. Vai nelle impostazioni AI.', [ 'status' => 400 ] );
        }

        $image_model = self::modello_immagine();

        // GPT Image non ha il parametro «style» di DALL·E: lo stile scelto entra nel prompt.
        $stili = [
            'vivid'   => 'Stile: colori vividi, contrasto deciso, resa d\'impatto.',
            'natural' => 'Stile: naturale e fotografico, colori realistici.',
        ];
        $body = [
            'model'         => $image_model,
            'prompt'        => $prompt . "\n\n" . $stili[ $style ],
            'n'             => 1,
            'size'          => $size,
            'output_format' => 'png',
        ];

        $started = microtime( true );
        $response = wp_remote_post( 'https://api.openai.com/v1/images/generations', [
            'timeout' => 120,
            'headers' => [
                'Content-Type'  => 'application/json',
                'Authorization' => 'Bearer ' . $api_key,
            ],
            'body' => wp_json_encode( $body ),
        ] );
        $elapsed_ms = (int) ( ( microtime( true ) - $started ) * 1000 );

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

        // GPT Image restituisce l'immagine in base64 (b64_json); un url resta accettato.
        $b64       = $resp_body['data'][0]['b64_json'] ?? '';
        $image_url = $resp_body['data'][0]['url'] ?? '';
        if ( '' === $b64 && '' === $image_url ) {
            return new WP_Error( 'no_image', 'Nessuna immagine generata.', [ 'status' => 500 ] );
        }

        // Costo dai token che GPT Image dichiara (testo in ingresso, immagine in uscita);
        // senza «usage» una stima fissa per formato.
        $u_in  = (int) ( $resp_body['usage']['input_tokens'] ?? 0 );
        $u_out = (int) ( $resp_body['usage']['output_tokens'] ?? 0 );
        if ( $u_in || $u_out ) {
            $img_cost = ( ( $u_in * self::PREZZO_IMMAGINE['testo'] + $u_out * self::PREZZO_IMMAGINE['immagine'] ) / 1000000 ) * self::EUR_PER_USD;
        } else {
            $img_cost = ( $size === '1024x1024' ) ? 0.04 : 0.06;
        }
        self::log_usage( $u_in + $u_out, $img_cost, $elapsed_ms );

        // Salva nella Media Library WP
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';

        if ( '' !== $b64 ) {
            $png      = base64_decode( $b64, true );
            $tmp_file = wp_tempnam( 'olo-ai-img' );
            if ( false === $png || ! $tmp_file || false === file_put_contents( $tmp_file, $png ) ) {
                return new WP_Error( 'download_error', 'Impossibile leggere l\'immagine generata.', [ 'status' => 500 ] );
            }
        } else {
            $tmp_file = download_url( $image_url );
            if ( is_wp_error( $tmp_file ) ) {
                return new WP_Error( 'download_error', 'Impossibile scaricare l\'immagine generata.', [ 'status' => 500 ] );
            }
        }

        $file_array = [
            'name'     => 'ai-generated-' . time() . '.png',
            'tmp_name' => $tmp_file,
        ];

        $attachment_id = media_handle_sideload( $file_array, 0, 'Immagine AI: ' . wp_trim_words( $prompt, 10 ) );

        if ( is_wp_error( $attachment_id ) ) {
            wp_delete_file( $tmp_file );
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
        $language  = sanitize_text_field( $request->get_param( 'language' ) ?: self::lingua_predefinita() );

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

        if ( ! self::ha_chiave() ) {
            return new WP_Error( 'no_api_key', 'Chiave API ' . self::NOMI_FORNITORE[ self::fornitore() ] . ' non configurata.', [ 'status' => 400 ] );
        }

        // Validate URL to prevent SSRF (no internal IPs, only http/https)
        if ( ! wp_http_validate_url( $image_url ) ) {
            return new WP_Error( 'invalid_url', 'URL immagine non valido.', [ 'status' => 400 ] );
        }

        // Scarica l'immagine e convertila in base64 (tutti e tre i fornitori la leggono così)
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
        if ( str_contains( $content_type, 'png' ) ) {
            $media_type = 'image/png';
        } elseif ( str_contains( $content_type, 'gif' ) ) {
            $media_type = 'image/gif';
        } elseif ( str_contains( $content_type, 'webp' ) ) {
            $media_type = 'image/webp';
        }

        $base64_img = base64_encode( $img_body );

        $system_prompt = "Sei un esperto SEO. Genera un alt text descrittivo e SEO-friendly per l'immagine fornita. "
                       . "Scrivi in {$lang}. L'alt text deve essere conciso (max 125 caratteri), descrittivo e ottimizzato per i motori di ricerca. "
                       . "Rispondi SOLO con l'alt text, senza virgolette.";

        $alt_text = self::chiama_modello(
            $system_prompt,
            'Genera un alt text SEO per questa immagine.',
            [ 'mime' => $media_type, 'b64' => $base64_img ],
            1024
        );
        if ( is_wp_error( $alt_text ) ) {
            return $alt_text;
        }

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

    private static function maschera( $chiave ) {
        $chiave = (string) $chiave;
        return '' === $chiave ? '' : str_repeat( '*', max( 0, strlen( $chiave ) - 4 ) ) . substr( $chiave, -4 );
    }

    /** Elenchi per la tendina: [ { value, label } ], col modello salvato se di una generazione precedente. */
    private static function elenchi_modelli() {
        $out = [];
        foreach ( self::MODELLI as $fornitore => $modelli ) {
            $out[ $fornitore ] = [];
            foreach ( $modelli as $id => $info ) {
                $out[ $fornitore ][] = [ 'value' => $id, 'label' => $info[0], 'temperatura' => ! empty( $info[3] ) ];
            }
        }
        $salvato = get_option( 'olobuild_ai_model', '' );
        foreach ( self::MODELLI_PRECEDENTI as $fornitore => $modelli ) {
            if ( isset( $modelli[ $salvato ] ) ) {
                $out[ $fornitore ][] = [ 'value' => $salvato, 'label' => $modelli[ $salvato ][0] . ' · versione precedente', 'temperatura' => ! empty( $modelli[ $salvato ][3] ) ];
            }
        }
        return $out;
    }

    public static function get_settings( $request ) {
        $fornitore = self::fornitore();
        $immagini  = [];
        foreach ( self::MODELLI_IMMAGINE as $id => $label ) {
            $immagini[] = [ 'value' => $id, 'label' => $label ];
        }
        return rest_ensure_response( [
            'provider'         => $fornitore,
            'anthropic_key'    => self::maschera( self::chiave( 'anthropic' ) ),
            'openai_key'       => self::maschera( self::chiave( 'openai' ) ),
            'mistral_key'      => self::maschera( self::chiave( 'mistral' ) ),
            'has_key'          => self::ha_chiave(),
            'has_openai_key'   => '' !== self::chiave( 'openai' ),
            'model'            => self::modello( $fornitore ),
            'image_model'      => self::modello_immagine(),
            'modelli'          => self::elenchi_modelli(),
            'modelli_immagine' => $immagini,
            'budget'           => (float) self::comportamento( 'budget' ),
            'language'         => (string) self::comportamento( 'language' ),
            'tone'             => (string) self::comportamento( 'tone' ),
            'temperature'      => (float) self::comportamento( 'temperature' ),
            'system_prompt'    => (string) self::comportamento( 'system_prompt' ),
            'spesa_mese'       => round( self::utilizzo_mese()['cost'], 2 ),
        ] );
    }

    public static function save_settings( $request ) {
        // Chiavi: una nuova si salva, una vuota si cancella, una mascherata (***) resta.
        foreach ( [ 'anthropic', 'openai', 'mistral' ] as $f ) {
            $k = $request->get_param( $f . '_key' );
            if ( null === $k ) {
                continue; // non inviata: resta com'è
            }
            if ( '' === $k ) {
                delete_option( 'olobuild_ai_' . $f . '_key' );
            } elseif ( false === strpos( $k, '*' ) ) {
                update_option( 'olobuild_ai_' . $f . '_key', sanitize_text_field( $k ) );
            }
        }

        $fornitore = sanitize_key( (string) $request->get_param( 'provider' ) );
        if ( ! isset( self::MODELLI[ $fornitore ] ) ) {
            $fornitore = self::fornitore();
        }
        update_option( 'olobuild_ai_provider', $fornitore );

        // Il modello deve essere del fornitore scelto, se no il suo predefinito.
        $model = sanitize_text_field( (string) $request->get_param( 'model' ) );
        if ( ! self::info_modello( $fornitore, $model ) ) {
            $ids   = array_keys( self::MODELLI[ $fornitore ] );
            $model = $ids[0];
        }
        update_option( 'olobuild_ai_model', $model );

        $image_model = sanitize_text_field( (string) $request->get_param( 'image_model' ) );
        if ( isset( self::MODELLI_IMMAGINE[ $image_model ] ) ) {
            update_option( 'olobuild_ai_image_model', $image_model );
        }

        // Comportamento: solo i valori ammessi; un parametro non inviato resta com'è.
        // Un budget negativo si scarta: arrotondato a 0 diventerebbe «nessun limite».
        $b = $request->get_param( 'budget' );
        if ( null !== $b && is_numeric( $b ) && (float) $b >= 0 ) {
            update_option( 'olobuild_ai_budget', round( min( 100000, (float) $b ), 2 ) );
        }
        $l = $request->get_param( 'language' );
        if ( null !== $l && ( isset( self::LINGUE[ $l ] ) || 'auto' === $l ) ) {
            update_option( 'olobuild_ai_language', $l );
        }
        $tn = $request->get_param( 'tone' );
        if ( null !== $tn && isset( self::TONI[ $tn ] ) ) {
            update_option( 'olobuild_ai_tone', $tn );
        }
        $tp = $request->get_param( 'temperature' );
        if ( null !== $tp && is_numeric( $tp ) ) {
            update_option( 'olobuild_ai_temperature', round( min( 1, max( 0, (float) $tp ) ), 2 ) );
        }
        $sp = $request->get_param( 'system_prompt' );
        if ( null !== $sp ) {
            $sp = sanitize_textarea_field( (string) $sp );
            update_option( 'olobuild_ai_system_prompt', function_exists( 'mb_substr' ) ? mb_substr( $sp, 0, 500 ) : substr( $sp, 0, 500 ) );
        }

        return rest_ensure_response( [
            'success'     => true,
            'provider'    => $fornitore,
            'model'       => $model,
            'image_model' => self::modello_immagine(),
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
     * Testo dal fornitore scelto (vedi chiama_modello()).
     */
    private static function call_chat_api( $system_prompt, $user_message ) {
        return self::chiama_modello( $system_prompt, $user_message, null, 4096 );
    }

    /**
     * Log a singola chiamata AI per stats in admin (endpoint /ai/usage).
     *
     * @param int   $tokens  Token consumati (input+output).
     * @param float $cost    Costo stimato in EUR.
     * @param int   $ms      Latenza in millisecondi.
     */
    public static function log_usage( $tokens = 0, $cost = 0.0, $ms = 0 ) {
        $log = get_option( 'olobuild_ai_usage', [] );
        if ( ! is_array( $log ) ) $log = [];

        $log[] = [
            'ts'     => time(),
            'tokens' => (int) $tokens,
            'cost'   => (float) $cost,
            'ms'     => (int) $ms,
        ];

        // Prune entries older than 60 days e cap a 1000 entries per evitare bloat option
        $cutoff = time() - 60 * DAY_IN_SECONDS;
        $log = array_values( array_filter( $log, function ( $e ) use ( $cutoff ) {
            return is_array( $e ) && ( $e['ts'] ?? 0 ) >= $cutoff;
        } ) );
        if ( count( $log ) > 1000 ) {
            $log = array_slice( $log, -1000 );
        }

        update_option( 'olobuild_ai_usage', $log, false );
    }
}

// Auto-init: la classe si registra autonomamente quando il file viene incluso
add_action( 'plugins_loaded', [ 'Olobuild_AI_Assistant', 'init' ], 20 );
