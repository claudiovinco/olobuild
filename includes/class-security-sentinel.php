<?php
/**
 * Olo_Security_Sentinel — cuore di OLOsecurity (la barriera di sicurezza di Olobuild).
 *
 * Non è un antimalware completo: è una "prima barriera" integrata e mirata ai vettori
 * tipici di un page builder. Lavora insieme agli altri moduli OLOsecurity:
 *   - Olo_Security_Audit          → registro attività (chi/cosa/quando)
 *   - Olo_Security_Config_Monitor → integrità di opzioni critiche e utenti admin
 *   - Olo_Security_Login          → anti brute-force + stop user-enumeration
 *
 * Questa classe copre:
 *   1. Codice personalizzato (head/body/footer): pattern di offuscamento/miner/iframe
 *      nascosti/script verso domini ignoti, alla scrittura e con un cron giornaliero.
 *   2. Integrità & webshell: baseline SHA-256 di .php/.js/.css del plugin + wp-config;
 *      scansione di tutta uploads (PHP eseguibili, doppie estensioni) e dei mu-plugins,
 *      con euristica di firme webshell.
 *   3. Risposta: quarantena/ripristino dei file sospetti e pulizia del custom-code.
 *   4. UI unica (Olobuild → OLOsecurity) con sezioni Stato / Registro / Impostazioni.
 *
 * Option usate (autoload off):
 *   - olo_sentinel_baseline      : ['version','created','hashes'=>[relpath=>sha256]]
 *   - olo_sentinel_status        : ['last_scan','level','content','files','config']
 *   - olo_sentinel_email_alerts  : '1' | '0'  (default attivo)
 *   - olo_sec_customcode_backup  : backup dell'ultimo custom-code ripulito
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Security_Sentinel {

    const CRON_HOOK   = 'olo_sentinel_scan';
    const PAGE_SLUG   = 'olo-security';
    const OPT_BASE    = 'olo_sentinel_baseline';
    const OPT_STATUS  = 'olo_sentinel_status';
    const OPT_EMAIL   = 'olo_sentinel_email_alerts';

    /** Estensioni tracciate nella baseline d'integrità del plugin. */
    private static $baseline_ext = [ 'php', 'js', 'css' ];

    /** Estensioni PHP-eseguibili: non dovrebbero MAI comparire in uploads. */
    private static $php_exec_ext = [ 'php', 'php3', 'php4', 'php5', 'php7', 'php8', 'phtml', 'pht', 'phar', 'phps', 'phpt' ];

    /** Numero massimo di file esaminati in uploads (backstop anti-runaway). */
    const UPLOADS_FILE_CAP = 300000;

    /** Numero massimo di finding "webshell" elencati prima di riassumere. */
    const FINDINGS_CAP = 60;

    /** Directory escluse dal calcolo integrità (assenti nel plugin distribuito). */
    private static $skip_dirs = [
        'node_modules', '.git', 'src', 'docs', 'scripts',
        'regoletiles1', 'handoff-tile-speciali', 'tmp_deploy',
        'tmp_try_build', 'tmp_try_ref', 'vendor',
    ];

    /**
     * Suffissi di host considerati attendibili per gli <script src>.
     * Tracking, CDN, social, ESP e gateway di pagamento più comuni: riduce i falsi positivi.
     */
    private static $trusted_hosts = [
        'googletagmanager.com', 'google-analytics.com', 'googleapis.com', 'gstatic.com',
        'google.com', 'googleadservices.com', 'googlesyndication.com', 'doubleclick.net',
        'recaptcha.net', 'youtube.com', 'youtube-nocookie.com', 'ytimg.com', 'vimeo.com',
        'facebook.net', 'facebook.com', 'fbcdn.net', 'cloudflare.com', 'cloudflareinsights.com',
        'jsdelivr.net', 'unpkg.com', 'cdnjs.cloudflare.com', 'jquery.com', 'bootstrapcdn.com',
        'fontawesome.com', 'hotjar.com', 'clarity.ms', 'hs-scripts.com', 'hubspot.com',
        'hsforms.com', 'hs-analytics.net', 'linkedin.com', 'licdn.com', 'twitter.com', 'x.com',
        'tiktok.com', 'snapchat.com', 'pinterest.com', 'bing.com', 'stripe.com', 'paypal.com',
        'paypalobjects.com', 'wp.com', 'gravatar.com', 'mailchimp.com', 'list-manage.com',
        'brevo.com', 'sendinblue.com', 'activecampaign.com', 'convertkit.com', 'klaviyo.com',
        'intercom.io', 'crisp.chat', 'tawk.to', 'zdassets.com', 'calendly.com', 'typeform.com',
        'usercentrics.eu', 'cookiebot.com', 'iubenda.com', 'onetrust.com', 'cookieyes.com',
        'addtoany.com', 'disqus.com', 'soundcloud.com', 'spotify.com', 'instagram.com',
        'cdninstagram.com', 'olotheme.com', 'clod.eu',
    ];

    public static function init() {
        // Scansione automatica quando il codice personalizzato cambia (qualunque sia la via).
        foreach ( [ 'head', 'body', 'footer' ] as $slot ) {
            add_action( 'update_option_olo_custom_code_' . $slot, [ __CLASS__, 'on_custom_code_change' ], 10, 0 );
            add_action( 'add_option_olo_custom_code_' . $slot,    [ __CLASS__, 'on_custom_code_change' ], 10, 0 );
        }

        // Cron giornaliero (contenuti + integrità file + configurazione).
        add_action( self::CRON_HOOK, [ __CLASS__, 'run_scan' ] );
        if ( ! wp_next_scheduled( self::CRON_HOOK ) ) {
            wp_schedule_event( time() + HOUR_IN_SECONDS, 'daily', self::CRON_HOOK );
        }

        // UI e avvisi (solo lato admin).
        add_action( 'admin_menu', [ __CLASS__, 'register_page' ], 60 );
        add_action( 'admin_notices', [ __CLASS__, 'maybe_admin_notice' ] );
    }

    // ──────────────────────────────────────────────────────────────────────
    //  Trigger
    // ──────────────────────────────────────────────────────────────────────

    /** Il codice personalizzato è cambiato: rivaluta subito solo i contenuti. */
    public static function on_custom_code_change() {
        $content = self::scan_content();
        $status  = get_option( self::OPT_STATUS, [] );
        if ( ! is_array( $status ) ) {
            $status = [];
        }
        $prev_content = isset( $status['content'] ) && is_array( $status['content'] ) ? $status['content'] : [];

        $status['content']   = $content;
        $status['last_scan'] = time();
        $status['level']     = self::level_from( array_merge( $content, $status['files'] ?? [], $status['config'] ?? [], $status['components'] ?? [] ) );
        update_option( self::OPT_STATUS, $status, false );

        self::maybe_alert( $content, $prev_content );
    }

    /** Scansione completa (cron + pulsante "Scansiona ora"). */
    public static function run_scan() {
        $prev     = get_option( self::OPT_STATUS, [] );
        $prev     = is_array( $prev ) ? $prev : [];
        $prev_all = array_merge(
            isset( $prev['content'] ) && is_array( $prev['content'] ) ? $prev['content'] : [],
            isset( $prev['files'] ) && is_array( $prev['files'] ) ? $prev['files'] : [],
            isset( $prev['config'] ) && is_array( $prev['config'] ) ? $prev['config'] : [],
            isset( $prev['components'] ) && is_array( $prev['components'] ) ? $prev['components'] : []
        );

        $content    = self::scan_content();
        $files      = self::scan_files();
        $config     = class_exists( 'Olo_Security_Config_Monitor' ) ? Olo_Security_Config_Monitor::scan() : [];
        $components = class_exists( 'Olo_Security_Components' ) ? Olo_Security_Components::scan() : [];

        $status = [
            'last_scan'  => time(),
            'content'    => $content,
            'files'      => $files,
            'config'     => $config,
            'components' => $components,
            'level'      => self::level_from( array_merge( $content, $files, $config, $components ) ),
        ];
        update_option( self::OPT_STATUS, $status, false );

        // Manutenzione del registro attività.
        if ( class_exists( 'Olo_Security_Audit' ) ) {
            Olo_Security_Audit::cleanup( 90 );
        }

        // Alert su nuovi finding ad alta severità (contenuti, file, configurazione, componenti).
        self::maybe_alert( array_merge( $content, $files, $config, $components ), $prev_all );
        return $status;
    }

    // ──────────────────────────────────────────────────────────────────────
    //  Scansione contenuti (custom code head/body/footer)
    // ──────────────────────────────────────────────────────────────────────

    /** @return array Lista di finding: ['slot','severity','label','snippet']. */
    public static function scan_content() {
        $findings = [];
        $slots = [
            'head'   => __( 'Codice personalizzato — Head', 'olobuild' ),
            'body'   => __( 'Codice personalizzato — Body', 'olobuild' ),
            'footer' => __( 'Codice personalizzato — Footer', 'olobuild' ),
        ];

        foreach ( $slots as $slot => $label ) {
            $code = (string) get_option( 'olo_custom_code_' . $slot, '' );
            if ( $code === '' ) {
                continue;
            }
            foreach ( self::analyze_code( $code ) as $f ) {
                $f['slot'] = $label;
                $findings[] = $f;
            }
        }
        return $findings;
    }

    /**
     * Analizza una stringa di codice e ritorna i pattern sospetti trovati.
     * Pattern ad alto segnale / basso rumore: l'obiettivo è non gridare "al lupo"
     * sul codice di tracking legittimo (GTM, GA4, Meta Pixel, ecc.).
     */
    public static function analyze_code( $code ) {
        $found = [];

        $patterns = [
            // severità alta: offuscamento / esecuzione dinamica / miner
            [ 'high', __( 'eval() con payload base64 (eval(atob(…)))', 'olobuild' ), '/eval\s*\(\s*atob\s*\(/i' ],
            [ 'high', __( 'Packer JavaScript offuscato (eval(function(p,a,c,k,e…))', 'olobuild' ), '/eval\s*\(\s*function\s*\(\s*p\s*,\s*a\s*,\s*c\s*,\s*k\s*,\s*e/i' ],
            [ 'high', __( 'Esecuzione dinamica via new Function()', 'olobuild' ), '/\bnew\s+Function\s*\(/i' ],
            [ 'high', __( 'document.write(unescape(…)) — tecnica di offuscamento', 'olobuild' ), '/document\s*\.\s*write\s*\(\s*unescape\s*\(/i' ],
            [ 'high', __( 'String.fromCharCode in serie — offuscamento', 'olobuild' ), '/(?:String\s*\.\s*fromCharCode\s*\([^)]*\)\s*\+?\s*){3,}/i' ],
            [ 'high', __( 'Sequenza esadecimale offuscata (\\xNN ripetuto)', 'olobuild' ), '/(?:\\\\x[0-9a-f]{2}){12,}/i' ],
            [ 'high', __( 'Sequenza unicode offuscata (\\uNNNN ripetuto)', 'olobuild' ), '/(?:\\\\u[0-9a-f]{4}){10,}/i' ],
            [ 'high', __( 'Riferimento a crypto-miner nel browser', 'olobuild' ), '/coinhive|coin-hive|cryptonight|webminepool|crypto-loot|deepminer|minero\.cc/i' ],
            [ 'high', __( 'iframe nascosto (display:none / 0×0)', 'olobuild' ), '/<iframe[^>]*(?:display\s*:\s*none|visibility\s*:\s*hidden|width\s*=\s*["\']?0|height\s*=\s*["\']?0|width\s*:\s*0|height\s*:\s*0)/i' ],
            [ 'high', __( 'eval() su stringa', 'olobuild' ), '/\beval\s*\(\s*[\'"]/' ],
        ];

        foreach ( $patterns as $p ) {
            if ( preg_match( $p[2], $code, $m ) ) {
                $found[] = [
                    'severity' => $p[0],
                    'label'    => $p[1],
                    'snippet'  => self::snippet( $m[0] ),
                ];
            }
        }

        // <script src> verso host non riconosciuti (severità media).
        if ( preg_match_all( '/<script\b[^>]*\bsrc\s*=\s*(["\'])(.*?)\1/i', $code, $sm ) ) {
            $site_host = wp_parse_url( home_url(), PHP_URL_HOST );
            $reported  = [];
            foreach ( $sm[2] as $src ) {
                $host = wp_parse_url( $src, PHP_URL_HOST );
                if ( ! $host ) {
                    continue; // relativo / protocol-relative senza host → ignora
                }
                $host = strtolower( $host );
                if ( $site_host && ( $host === strtolower( $site_host ) || self::host_matches( $host, strtolower( $site_host ) ) ) ) {
                    continue;
                }
                if ( self::is_trusted_host( $host ) ) {
                    continue;
                }
                if ( isset( $reported[ $host ] ) ) {
                    continue;
                }
                $reported[ $host ] = true;
                $found[] = [
                    'severity' => 'medium',
                    /* translators: %s: hostname */
                    'label'    => sprintf( __( 'Script esterno da dominio non riconosciuto: %s', 'olobuild' ), $host ),
                    'snippet'  => self::snippet( $src ),
                ];
            }
        }

        // Blob base64 molto lungo (dopo aver rimosso i data-URI legittimi di immagini/font).
        $clean = preg_replace( '#data:[a-z0-9.+\-]+/[a-z0-9.+\-]+;base64,[A-Za-z0-9+/=\s]+#i', 'data:[ok]', $code );
        if ( preg_match( '/[\'"][A-Za-z0-9+\/]{800,}={0,2}[\'"]/', (string) $clean, $bm ) ) {
            $found[] = [
                'severity' => 'medium',
                'label'    => __( 'Blob base64 molto lungo incorporato (possibile payload offuscato)', 'olobuild' ),
                'snippet'  => self::snippet( $bm[0] ),
            ];
        }

        return $found;
    }

    // ──────────────────────────────────────────────────────────────────────
    //  Integrità file + webshell
    // ──────────────────────────────────────────────────────────────────────

    public static function build_baseline() {
        $hashes = self::hash_baseline_files();
        $theme  = wp_get_theme();
        update_option( self::OPT_BASE, [
            'version'       => defined( 'OLO_VERSION' ) ? OLO_VERSION : '0',
            'theme_version' => $theme ? ( $theme->get_stylesheet() . '@' . $theme->get( 'Version' ) ) : '',
            'created'       => time(),
            'hashes'        => $hashes,
            'sig'           => self::baseline_sig( $hashes ),
        ], false );
        return count( $hashes );
    }

    /** Firma HMAC degli hash della baseline: rivela manomissioni dell'option via DB. */
    private static function baseline_sig( $hashes ) {
        return hash_hmac( 'sha256', (string) wp_json_encode( $hashes ), wp_salt( 'auth' ) );
    }

    /** @return array Lista di finding file: ['severity','label','path','detail'?,'abs'?]. */
    public static function scan_files() {
        $findings = [];
        $baseline = get_option( self::OPT_BASE, [] );
        $cur_ver  = defined( 'OLO_VERSION' ) ? OLO_VERSION : '0';
        $theme    = wp_get_theme();
        $cur_tv   = $theme ? ( $theme->get_stylesheet() . '@' . $theme->get( 'Version' ) ) : '';

        // Rigenera senza allarmare se è cambiata la versione del plugin O del tema.
        if ( ! is_array( $baseline ) || empty( $baseline['hashes'] ) || ( $baseline['version'] ?? '' ) !== $cur_ver || ( $baseline['theme_version'] ?? '' ) !== $cur_tv ) {
            self::build_baseline();
        } else {
            $current = self::hash_baseline_files();
            $base    = $baseline['hashes'];

            // Tamper-evidence: se la firma della baseline non torna, l'option è stata manomessa.
            if ( ! empty( $baseline['sig'] ) && ! hash_equals( $baseline['sig'], self::baseline_sig( $base ) ) ) {
                $findings[] = [
                    'severity' => 'high',
                    'label'    => __( 'Baseline di integrità manomessa (firma non valida) — rigenerala dopo aver verificato i file', 'olobuild' ),
                    'path'     => self::OPT_BASE,
                ];
            }

            foreach ( $current as $rel => $hash ) {
                $is_core = ( strpos( $rel, '[core] ' ) === 0 );
                if ( ! isset( $base[ $rel ] ) ) {
                    $findings[] = [
                        'severity' => 'high',
                        'label'    => __( 'File nuovo non presente nella baseline (possibile iniezione)', 'olobuild' ),
                        'path'     => $rel,
                        'detail'   => self::describe_file_signatures( self::resolve_baseline_path( $rel ) ),
                    ];
                } elseif ( ! hash_equals( $base[ $rel ], $hash ) ) {
                    if ( $is_core ) {
                        $findings[] = [
                            'severity' => 'medium',
                            'label'    => __( 'wp-config.php modificato — verifica di averlo fatto tu', 'olobuild' ),
                            'path'     => $rel,
                        ];
                    } else {
                        $findings[] = [
                            'severity' => 'high',
                            'label'    => __( 'File del plugin modificato rispetto alla baseline', 'olobuild' ),
                            'path'     => $rel,
                            'detail'   => self::describe_file_signatures( self::resolve_baseline_path( $rel ) ),
                        ];
                    }
                }
            }

            foreach ( $base as $rel => $hash ) {
                if ( ! isset( $current[ $rel ] ) ) {
                    $findings[] = [
                        'severity' => 'medium',
                        'label'    => __( 'File della baseline non più presente (rimosso o spostato)', 'olobuild' ),
                        'path'     => $rel,
                    ];
                }
            }
        }

        $findings = array_merge( $findings, self::scan_uploads_executables(), self::scan_mu_plugins() );

        if ( count( $findings ) > self::FINDINGS_CAP ) {
            $extra    = count( $findings ) - self::FINDINGS_CAP;
            $findings = array_slice( $findings, 0, self::FINDINGS_CAP );
            $findings[] = [
                'severity' => 'high',
                /* translators: %d: number of additional findings not listed */
                'label'    => sprintf( __( 'Elenco troncato: altri %d file sospetti non mostrati.', 'olobuild' ), $extra ),
                'path'     => '—',
            ];
        }

        return $findings;
    }

    /** @return array relpath => sha256 (plugin .php/.js/.css + wp-config + tema attivo). */
    private static function hash_baseline_files() {
        $hashes = [];

        // File del plugin.
        $root = defined( 'OLO_PATH' ) ? OLO_PATH : plugin_dir_path( __FILE__ ) . '../';
        self::hash_tree( $root, '', $hashes );

        // wp-config.php.
        $config = self::wp_config_path();
        if ( $config ) {
            $hash = @hash_file( 'sha256', $config );
            if ( $hash ) {
                $hashes['[core] wp-config.php'] = $hash;
            }
        }

        // Tema attivo (parent + eventuale child): functions.php & co. sono un vettore classico.
        $theme_dirs = array_unique( [ get_template_directory(), get_stylesheet_directory() ] );
        foreach ( $theme_dirs as $td ) {
            if ( $td && is_dir( $td ) ) {
                self::hash_tree( $td, '[theme:' . basename( $td ) . '] ', $hashes );
            }
        }

        ksort( $hashes );
        return $hashes;
    }

    /** Hash ricorsivo dei file .php/.js/.css sotto $root, con prefisso di chiave. */
    private static function hash_tree( $root, $prefix, &$hashes ) {
        $root = trailingslashit( wp_normalize_path( $root ) );
        try {
            $dir_it = new RecursiveDirectoryIterator( $root, FilesystemIterator::SKIP_DOTS );
            $filter = new RecursiveCallbackFilterIterator( $dir_it, function ( $current ) {
                $name = $current->getFilename();
                if ( $current->isDir() ) {
                    return ! in_array( $name, self::$skip_dirs, true );
                }
                if ( substr( $name, -4 ) === '.map' ) {
                    return false;
                }
                return in_array( strtolower( $current->getExtension() ), self::$baseline_ext, true );
            } );
            $it = new RecursiveIteratorIterator( $filter );
            foreach ( $it as $file ) {
                $path = wp_normalize_path( $file->getPathname() );
                $rel  = ltrim( str_replace( $root, '', $path ), '/' );
                $hash = @hash_file( 'sha256', $path );
                if ( $hash ) {
                    $hashes[ $prefix . $rel ] = $hash;
                }
            }
        } catch ( Exception $e ) {
            // Filesystem inaccessibile: non blocchiamo nulla.
        }
    }

    private static function resolve_baseline_path( $rel ) {
        if ( $rel === '[core] wp-config.php' ) {
            return self::wp_config_path();
        }
        if ( strpos( $rel, '[theme:' ) === 0 && preg_match( '/^\[theme:([^\]]+)\]\s(.+)$/', $rel, $m ) ) {
            return wp_normalize_path( trailingslashit( get_theme_root() ) . $m[1] . '/' . $m[2] );
        }
        $root = defined( 'OLO_PATH' ) ? OLO_PATH : plugin_dir_path( __FILE__ ) . '../';
        return wp_normalize_path( $root ) . $rel;
    }

    private static function wp_config_path() {
        if ( file_exists( ABSPATH . 'wp-config.php' ) ) {
            return ABSPATH . 'wp-config.php';
        }
        $up = dirname( ABSPATH ) . '/wp-config.php';
        if ( @file_exists( $up ) && ! @file_exists( dirname( ABSPATH ) . '/wp-settings.php' ) ) {
            return $up;
        }
        return '';
    }

    private static function scan_uploads_executables() {
        $findings = [];
        $upload   = wp_upload_dir();
        if ( empty( $upload['basedir'] ) || ! is_dir( $upload['basedir'] ) ) {
            return $findings;
        }
        $base = wp_normalize_path( $upload['basedir'] );
        $seen = 0;
        $truncated = false;

        try {
            $it = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator( $base, FilesystemIterator::SKIP_DOTS ),
                RecursiveIteratorIterator::LEAVES_ONLY,
                RecursiveIteratorIterator::CATCH_GET_CHILD
            );
            foreach ( $it as $file ) {
                if ( ++$seen > self::UPLOADS_FILE_CAP ) {
                    $truncated = true;
                    break;
                }
                if ( ! $file->isFile() ) {
                    continue;
                }
                $name = $file->getFilename();
                $exec = self::is_executable_php_name( $name );
                $mask = self::is_masked_double_ext( $name );
                if ( ! $exec && ! $mask ) {
                    continue;
                }

                $path = wp_normalize_path( $file->getPathname() );
                if ( self::is_benign_index_guard( $path ) ) {
                    continue;
                }
                $rel = ltrim( str_replace( $base, '', $path ), '/' );

                $findings[] = [
                    'severity' => 'high',
                    'label'    => $mask
                        ? __( 'File con doppia estensione mascherata in uploads (sospetta webshell)', 'olobuild' )
                        : __( 'File PHP eseguibile nella cartella uploads (sospetta webshell)', 'olobuild' ),
                    'path'     => 'uploads/' . $rel,
                    'detail'   => self::describe_file_signatures( $path ),
                    'abs'      => $path,
                ];
            }
        } catch ( Exception $e ) {
            // Filesystem inaccessibile.
        }

        if ( $truncated ) {
            $findings[] = [
                'severity' => 'medium',
                /* translators: %d: number of files scanned before stopping */
                'label'    => sprintf( __( 'Scansione uploads interrotta dopo %d file (cartella molto grande).', 'olobuild' ), self::UPLOADS_FILE_CAP ),
                'path'     => 'uploads/',
            ];
        }
        return $findings;
    }

    private static function scan_mu_plugins() {
        $findings = [];
        $dir = defined( 'WPMU_PLUGIN_DIR' ) ? WPMU_PLUGIN_DIR : WP_CONTENT_DIR . '/mu-plugins';
        if ( ! is_dir( $dir ) ) {
            return $findings;
        }
        $files = glob( trailingslashit( $dir ) . '*.php' );
        if ( ! is_array( $files ) ) {
            return $findings;
        }
        foreach ( $files as $file ) {
            $sigs = self::file_signatures( $file );
            if ( empty( $sigs ) ) {
                continue;
            }
            $strong = false;
            foreach ( $sigs as $s ) {
                if ( $s['strong'] ) { $strong = true; break; }
            }
            $findings[] = [
                'severity' => $strong ? 'high' : 'medium',
                'label'    => __( 'mu-plugin con firme di codice potenzialmente malevolo', 'olobuild' ),
                'path'     => 'mu-plugins/' . basename( $file ),
                'detail'   => self::signatures_to_text( $sigs ),
                'abs'      => wp_normalize_path( $file ),
            ];
        }
        return $findings;
    }

    /**
     * "index.php" di protezione directory innocui: i placeholder che WordPress e i
     * plugin (WPForms, MainWP, WooCommerce, …) lasciano in uploads per impedire il
     * listing della cartella. Possono essere vuoti, "Silence is golden", oppure un
     * piccolo header 404/403. Sono benigni SOLO se piccoli e privi di qualunque firma
     * di codice eseguibile/offuscato e di input utente verso sink pericolosi: così un
     * eventuale webshell mascherato da index.php continua a essere segnalato.
     */
    private static function is_benign_index_guard( $path ) {
        if ( strtolower( basename( $path ) ) !== 'index.php' ) {
            return false;
        }
        $size = @filesize( $path );
        if ( $size === false || $size > 1024 ) {
            return false; // un guard di directory non supera mai ~1 KB
        }
        if ( $size === 0 ) {
            return true; // index.php vuoto = solo anti directory-listing
        }
        $content = (string) @file_get_contents( $path );
        if ( trim( $content ) === '' ) {
            return true;
        }
        // Qualunque firma malware/offuscamento (eval, system, base64, callback da
        // input, webshell note, …) → NON benigno: lascialo nei finding.
        if ( ! empty( self::file_signatures( $path ) ) ) {
            return false;
        }
        // Input utente, inclusioni dinamiche, function variabile o backtick (shell):
        // vettori tipici di LFI / shell → NON benigno. ($_SERVER è ammesso: WPForms lo
        // usa solo per comporre l'header 404, e i sink su $_SERVER sono già coperti
        // sopra da file_signatures.)
        if ( preg_match( '/\$_(GET|POST|REQUEST|COOKIE|FILES)\b/i', $content )
            || preg_match( '/\b(include|require)(_once)?\b/i', $content )
            || preg_match( '/\$[a-z_][a-z0-9_]*\s*\(/i', $content )
            || strpos( $content, '`' ) !== false ) {
            return false;
        }
        return true;
    }

    private static function is_executable_php_name( $name ) {
        $ext = strtolower( pathinfo( $name, PATHINFO_EXTENSION ) );
        return in_array( $ext, self::$php_exec_ext, true );
    }

    private static function is_masked_double_ext( $name ) {
        return (bool) preg_match(
            '/\.(jpe?g|png|gif|webp|svg|bmp|ico|pdf|txt|csv|zip|gz|doc|xls|mp4|mp3)\.(php\d?|phtml|phar|pht|phps)$/i',
            $name
        );
    }

    /** @return array<int,array{label:string,strong:bool}> */
    private static function file_signatures( $path ) {
        $size = @filesize( $path );
        if ( $size === false || $size === 0 || $size > 2 * 1024 * 1024 ) {
            return [];
        }
        $code = (string) @file_get_contents( $path, false, null, 0, 524288 );
        if ( $code === '' ) {
            return [];
        }

        $sigs = [
            [ 'strong' => true,  'label' => 'eval()',                                    're' => '/\beval\s*\(/i' ],
            [ 'strong' => true,  'label' => 'assert()',                                  're' => '/\bassert\s*\(/i' ],
            [ 'strong' => true,  'label' => 'system/shell_exec/passthru/exec/proc_open', 're' => '/\b(system|shell_exec|passthru|popen|proc_open|exec)\s*\(/i' ],
            [ 'strong' => true,  'label' => 'payload compresso (gzinflate/gzuncompress)', 're' => '/\bgz(inflate|uncompress)\s*\(/i' ],
            [ 'strong' => true,  'label' => 'create_function()',                         're' => '/\bcreate_function\s*\(/i' ],
            [ 'strong' => true,  'label' => 'preg_replace con modificatore /e',          're' => '/preg_replace\s*\([^)]*\/[a-z]*e[a-z]*[\'"]/i' ],
            [ 'strong' => true,  'label' => 'callback da input ($_POST/$_GET/$_REQUEST)', 're' => '/\$_(POST|GET|REQUEST|COOKIE|SERVER)\s*\[[^\]]*\]\s*\(/i' ],
            [ 'strong' => true,  'label' => 'nome di webshell nota',                     're' => '/\b(c99|r57|wso|b374k|filesman|phpspy|aspydir|weevely)\b/i' ],
            [ 'strong' => false, 'label' => 'base64_decode()',                           're' => '/\bbase64_decode\s*\(/i' ],
            [ 'strong' => false, 'label' => 'str_rot13()',                               're' => '/\bstr_rot13\s*\(/i' ],
            [ 'strong' => false, 'label' => 'move_uploaded_file()',                      're' => '/\bmove_uploaded_file\s*\(/i' ],
        ];

        $found = [];
        foreach ( $sigs as $s ) {
            if ( preg_match( $s['re'], $code ) ) {
                $found[] = [ 'label' => $s['label'], 'strong' => $s['strong'] ];
            }
        }
        return $found;
    }

    private static function describe_file_signatures( $path ) {
        if ( ! $path || ! @is_file( $path ) ) {
            return '';
        }
        return self::signatures_to_text( self::file_signatures( $path ) );
    }

    private static function signatures_to_text( $sigs ) {
        if ( empty( $sigs ) ) {
            return '';
        }
        $labels = array_slice( array_map( function ( $s ) { return $s['label']; }, $sigs ), 0, 5 );
        return __( 'Firme:', 'olobuild' ) . ' ' . implode( ', ', $labels );
    }

    // ──────────────────────────────────────────────────────────────────────
    //  Risposta: quarantena / ripristino / pulizia custom-code
    // ──────────────────────────────────────────────────────────────────────

    /** Radici in cui è consentito mettere in quarantena (NON il plugin: lì si usa la baseline). */
    private static function quarantine_roots() {
        $roots  = [];
        $upload = wp_upload_dir();
        if ( ! empty( $upload['basedir'] ) && is_dir( $upload['basedir'] ) ) {
            $r = realpath( $upload['basedir'] );
            if ( $r ) { $roots[] = wp_normalize_path( $r ); }
        }
        $mu = defined( 'WPMU_PLUGIN_DIR' ) ? WPMU_PLUGIN_DIR : WP_CONTENT_DIR . '/mu-plugins';
        if ( is_dir( $mu ) ) {
            $r = realpath( $mu );
            if ( $r ) { $roots[] = wp_normalize_path( $r ); }
        }
        return $roots;
    }

    /** Anti path-traversal: il file deve risolvere DENTRO una radice consentita. */
    private static function is_within_quarantine_roots( $abs ) {
        $real = realpath( $abs );
        if ( ! $real ) {
            return false;
        }
        $real = wp_normalize_path( $real );
        foreach ( self::quarantine_roots() as $root ) {
            if ( $root !== '' && strpos( $real, $root . '/' ) === 0 ) {
                return true;
            }
        }
        return false;
    }

    public static function quarantine_file( $abs ) {
        if ( ! self::is_within_quarantine_roots( $abs ) ) {
            return false;
        }
        $real = realpath( $abs );
        if ( ! $real || ! is_file( $real ) ) {
            return false;
        }
        $dest = $real . '.oloquarantine';
        if ( @rename( $real, $dest ) ) {
            @chmod( $dest, 0000 );
            if ( class_exists( 'Olo_Security_Audit' ) ) {
                Olo_Security_Audit::log( 'quarantine', sprintf( __( 'File messo in quarantena: %s', 'olobuild' ), $real ), 'high' );
            }
            return true;
        }
        return false;
    }

    public static function restore_quarantine( $abs ) {
        if ( substr( (string) $abs, -14 ) !== '.oloquarantine' ) {
            return false;
        }
        if ( ! self::is_within_quarantine_roots( $abs ) ) {
            return false;
        }
        $real = realpath( $abs );
        if ( ! $real || ! is_file( $real ) ) {
            return false;
        }
        $orig = substr( $real, 0, -14 );
        @chmod( $real, 0644 );
        if ( @rename( $real, $orig ) ) {
            if ( class_exists( 'Olo_Security_Audit' ) ) {
                Olo_Security_Audit::log( 'restore', sprintf( __( 'File ripristinato dalla quarantena: %s', 'olobuild' ), $orig ), 'warn' );
            }
            return true;
        }
        return false;
    }

    public static function delete_quarantine( $abs ) {
        if ( substr( (string) $abs, -14 ) !== '.oloquarantine' ) {
            return false;
        }
        if ( ! self::is_within_quarantine_roots( $abs ) ) {
            return false;
        }
        $real = realpath( $abs );
        if ( ! $real || ! is_file( $real ) ) {
            return false;
        }
        @chmod( $real, 0644 );
        if ( @unlink( $real ) ) {
            if ( class_exists( 'Olo_Security_Audit' ) ) {
                Olo_Security_Audit::log( 'delete', sprintf( __( 'File in quarantena eliminato: %s', 'olobuild' ), $real ), 'warn' );
            }
            return true;
        }
        return false;
    }

    /** Elenco dei file attualmente in quarantena (per la UI). */
    public static function list_quarantined() {
        $out = [];
        foreach ( self::quarantine_roots() as $root ) {
            try {
                $it = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator( $root, FilesystemIterator::SKIP_DOTS ),
                    RecursiveIteratorIterator::LEAVES_ONLY,
                    RecursiveIteratorIterator::CATCH_GET_CHILD
                );
                foreach ( $it as $file ) {
                    if ( $file->isFile() && substr( $file->getFilename(), -14 ) === '.oloquarantine' ) {
                        $out[] = wp_normalize_path( $file->getPathname() );
                    }
                }
            } catch ( Exception $e ) {
                continue;
            }
        }
        return $out;
    }

    /** Svuota il custom-code salvandone un backup ripristinabile. */
    public static function clean_custom_code() {
        $backup = [];
        foreach ( [ 'head', 'body', 'footer' ] as $slot ) {
            $backup[ $slot ] = get_option( 'olo_custom_code_' . $slot, '' );
        }
        update_option( 'olo_sec_customcode_backup', [ 'ts' => time(), 'data' => $backup ], false );
        foreach ( [ 'head', 'body', 'footer' ] as $slot ) {
            update_option( 'olo_custom_code_' . $slot, '', false );
        }
        if ( class_exists( 'Olo_Security_Audit' ) ) {
            Olo_Security_Audit::log( 'clean_customcode', __( 'Codice personalizzato svuotato (backup creato)', 'olobuild' ), 'high' );
        }
        self::run_scan();
    }

    public static function restore_custom_code() {
        $bk = get_option( 'olo_sec_customcode_backup', [] );
        if ( empty( $bk['data'] ) || ! is_array( $bk['data'] ) ) {
            return false;
        }
        foreach ( [ 'head', 'body', 'footer' ] as $slot ) {
            if ( isset( $bk['data'][ $slot ] ) ) {
                update_option( 'olo_custom_code_' . $slot, $bk['data'][ $slot ], false );
            }
        }
        if ( class_exists( 'Olo_Security_Audit' ) ) {
            Olo_Security_Audit::log( 'restore_customcode', __( 'Codice personalizzato ripristinato dal backup', 'olobuild' ), 'warn' );
        }
        self::run_scan();
        return true;
    }

    // ──────────────────────────────────────────────────────────────────────
    //  Avvisi
    // ──────────────────────────────────────────────────────────────────────

    private static function level_from( $findings ) {
        $level = 'ok';
        foreach ( (array) $findings as $f ) {
            if ( ( $f['severity'] ?? '' ) === 'high' ) {
                return 'high';
            }
            if ( in_array( $f['severity'] ?? '', [ 'medium', 'warn' ], true ) ) {
                $level = 'warn';
            }
        }
        return $level;
    }

    private static function finding_key( $f ) {
        return ( $f['label'] ?? '' ) . '|' . ( $f['slot'] ?? ( $f['path'] ?? '' ) );
    }

    private static function maybe_alert( $findings, $prev_findings ) {
        if ( get_option( self::OPT_EMAIL, '1' ) !== '1' ) {
            return;
        }
        $high = array_filter( (array) $findings, function ( $f ) {
            return ( $f['severity'] ?? '' ) === 'high';
        } );
        if ( empty( $high ) ) {
            return;
        }
        $prev_keys = array_map( [ __CLASS__, 'finding_key' ], (array) $prev_findings );
        $new = array_filter( $high, function ( $f ) use ( $prev_keys ) {
            return ! in_array( self::finding_key( $f ), $prev_keys, true );
        } );
        if ( empty( $new ) ) {
            return;
        }
        if ( get_transient( 'olo_sentinel_alert_sent' ) ) {
            return;
        }
        set_transient( 'olo_sentinel_alert_sent', 1, 12 * HOUR_IN_SECONDS );

        $site  = get_bloginfo( 'name' );
        $lines = [];
        /* translators: %s: site name */
        $lines[] = sprintf( __( 'OLOsecurity ha rilevato elementi sospetti su "%s".', 'olobuild' ), $site );
        $lines[] = '';
        foreach ( $high as $f ) {
            $where = $f['slot'] ?? ( $f['path'] ?? '' );
            $line  = '• ' . ( $f['label'] ?? '' ) . ( $where ? ' [' . $where . ']' : '' );
            if ( ! empty( $f['detail'] ) ) {
                $line .= ' — ' . $f['detail'];
            }
            $lines[] = $line;
        }
        $lines[] = '';
        $lines[] = __( 'Apri Olobuild → OLOsecurity per i dettagli. Se non hai apportato tu queste modifiche, il sito potrebbe essere compromesso.', 'olobuild' );
        $lines[] = admin_url( 'admin.php?page=' . self::PAGE_SLUG );

        wp_mail(
            get_option( 'admin_email' ),
            '[' . $site . '] ' . __( '⚠️ OLOsecurity: rilevati elementi sospetti', 'olobuild' ),
            implode( "\n", $lines )
        );

        self::send_webhook( $site, $high );
    }

    /** Corpo JSON dell'avviso webhook (pubblico per i test). */
    public static function build_webhook_payload( $site, $high ) {
        $lines = [];
        foreach ( (array) $high as $f ) {
            $where   = $f['slot'] ?? ( $f['path'] ?? '' );
            $lines[] = '• ' . ( $f['label'] ?? '' ) . ( $where ? ' [' . $where . ']' : '' );
        }
        $text = sprintf( __( '⚠️ OLOsecurity su %s — rilevati elementi sospetti:', 'olobuild' ), $site )
            . "\n" . implode( "\n", $lines )
            . "\n" . admin_url( 'admin.php?page=' . self::PAGE_SLUG );
        // 'text' per Slack/Mattermost, 'content' per Discord.
        return (string) wp_json_encode( [ 'text' => $text, 'content' => $text ] );
    }

    /** Invio fire-and-forget dell'avviso a un webhook configurato. */
    private static function send_webhook( $site, $high ) {
        $w = get_option( 'olo_sec_webhook', [] );
        if ( empty( $w['enabled'] ) || empty( $w['url'] ) ) {
            return;
        }
        $url = esc_url_raw( $w['url'] );
        if ( ! $url ) {
            return;
        }
        wp_remote_post( $url, [
            'headers'  => [ 'Content-Type' => 'application/json' ],
            'body'     => self::build_webhook_payload( $site, $high ),
            'timeout'  => 8,
            'blocking' => false,
        ] );
    }

    public static function maybe_admin_notice() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        $status = get_option( self::OPT_STATUS, [] );
        if ( ! is_array( $status ) || ( $status['level'] ?? 'ok' ) !== 'high' ) {
            return;
        }
        $url = esc_url( admin_url( 'admin.php?page=' . self::PAGE_SLUG ) );
        echo '<div class="notice notice-error"><p><strong>OLOsecurity:</strong> '
            . esc_html__( 'rilevati elementi sospetti (codice, file, configurazione o possibili webshell).', 'olobuild' )
            . ' <a href="' . $url . '">' . esc_html__( 'Apri il report', 'olobuild' ) . '</a></p></div>';
    }

    // ──────────────────────────────────────────────────────────────────────
    //  Pagina admin (Olobuild → OLOsecurity)
    // ──────────────────────────────────────────────────────────────────────

    public static function register_page() {
        add_submenu_page(
            'olobuild',
            __( 'OLOsecurity', 'olobuild' ),
            __( 'OLOsecurity', 'olobuild' ),
            'manage_options',
            self::PAGE_SLUG,
            [ __CLASS__, 'render_page' ]
        );
    }

    public static function render_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'Permessi insufficienti.', 'olobuild' ) );
        }

        // Export CSV del log: deve avvenire PRIMA di qualsiasi output.
        if ( isset( $_POST['olo_sentinel_action'] ) && $_POST['olo_sentinel_action'] === 'export_log' ) {
            check_admin_referer( 'olo_sentinel' );
            if ( class_exists( 'Olo_Security_Audit' ) ) {
                Olo_Security_Audit::export_csv();
            }
        }

        $notice = self::handle_action();

        $view = isset( $_GET['view'] ) ? sanitize_key( wp_unslash( $_GET['view'] ) ) : 'stato';
        if ( ! in_array( $view, [ 'stato', 'log', 'settings' ], true ) ) {
            $view = 'stato';
        }

        $status = get_option( self::OPT_STATUS, [] );
        $status = is_array( $status ) ? $status : [];
        $level  = $status['level'] ?? 'ok';

        $badge = [
            'ok'   => [ '#46b450', __( 'Nessun problema rilevato', 'olobuild' ) ],
            'warn' => [ '#dba617', __( 'Elementi da verificare', 'olobuild' ) ],
            'high' => [ '#dc3232', __( 'Rilevati elementi sospetti', 'olobuild' ) ],
        ];
        $bd  = $badge[ $level ] ?? $badge['ok'];
        $base_url = admin_url( 'admin.php?page=' . self::PAGE_SLUG );
        $tabs = [
            'stato'    => __( 'Stato', 'olobuild' ),
            'log'      => __( 'Registro attività', 'olobuild' ),
            'settings' => __( 'Impostazioni', 'olobuild' ),
        ];
        ?>
        <div class="wrap">
            <h1>OLOsecurity <span style="font-size:13px;color:#888;font-weight:400">— <?php esc_html_e( 'Sicurezza Olobuild', 'olobuild' ); ?></span></h1>

            <?php if ( $notice ) : ?>
                <div class="notice notice-success is-dismissible"><p><?php echo esc_html( $notice ); ?></p></div>
            <?php endif; ?>

            <p style="margin:14px 0">
                <span style="display:inline-block;padding:6px 14px;border-radius:999px;background:<?php echo esc_attr( $bd[0] ); ?>;color:#fff;font-weight:600"><?php echo esc_html( $bd[1] ); ?></span>
                <?php if ( ! empty( $status['last_scan'] ) ) : ?>
                    <span style="color:#666;margin-left:10px"><?php echo esc_html( sprintf( __( 'Ultima scansione: %s', 'olobuild' ), wp_date( 'd/m/Y H:i', (int) $status['last_scan'] ) ) ); ?></span>
                <?php endif; ?>
            </p>

            <h2 class="nav-tab-wrapper">
                <?php foreach ( $tabs as $k => $lbl ) : ?>
                    <a href="<?php echo esc_url( add_query_arg( 'view', $k, $base_url ) ); ?>" class="nav-tab <?php echo $view === $k ? 'nav-tab-active' : ''; ?>"><?php echo esc_html( $lbl ); ?></a>
                <?php endforeach; ?>
            </h2>

            <?php
            if ( $view === 'log' ) {
                self::render_view_log();
            } elseif ( $view === 'settings' ) {
                self::render_view_settings();
            } else {
                self::render_view_stato( $status );
            }
            ?>
        </div>
        <?php
    }

    /** Gestisce le azioni POST (eccetto export). @return string messaggio di esito. */
    private static function handle_action() {
        if ( empty( $_POST['olo_sentinel_action'] ) ) {
            return '';
        }
        check_admin_referer( 'olo_sentinel' );
        $action = sanitize_key( wp_unslash( $_POST['olo_sentinel_action'] ) );

        switch ( $action ) {
            case 'scan':
                self::run_scan();
                return __( 'Scansione completata.', 'olobuild' );

            case 'rebaseline':
                $n = self::build_baseline();
                self::run_scan();
                return sprintf( __( 'Baseline file rigenerata su %d elementi.', 'olobuild' ), (int) $n );

            case 'rebaseline_config':
                if ( class_exists( 'Olo_Security_Config_Monitor' ) ) {
                    Olo_Security_Config_Monitor::build_baseline();
                }
                self::run_scan();
                return __( 'Stato di configurazione confermato come sicuro.', 'olobuild' );

            case 'rebuild_components':
                if ( class_exists( 'Olo_Security_Components' ) ) {
                    Olo_Security_Components::build_baseline();
                }
                self::run_scan();
                return __( 'Stato di plugin e temi confermato come sicuro.', 'olobuild' );

            case 'quarantine':
                $ok = self::quarantine_file( wp_unslash( $_POST['file'] ?? '' ) );
                self::run_scan();
                return $ok ? __( 'File messo in quarantena.', 'olobuild' ) : __( 'Impossibile mettere in quarantena il file.', 'olobuild' );

            case 'restore_quarantine':
                $ok = self::restore_quarantine( wp_unslash( $_POST['file'] ?? '' ) );
                self::run_scan();
                return $ok ? __( 'File ripristinato.', 'olobuild' ) : __( 'Ripristino non riuscito.', 'olobuild' );

            case 'delete_quarantine':
                $ok = self::delete_quarantine( wp_unslash( $_POST['file'] ?? '' ) );
                return $ok ? __( 'File eliminato definitivamente.', 'olobuild' ) : __( 'Eliminazione non riuscita.', 'olobuild' );

            case 'clean_customcode':
                self::clean_custom_code();
                return __( 'Codice personalizzato svuotato (backup creato).', 'olobuild' );

            case 'restore_customcode':
                self::restore_custom_code();
                return __( 'Codice personalizzato ripristinato dal backup.', 'olobuild' );

            case 'clear_lockouts':
                if ( class_exists( 'Olo_Security_Login' ) ) {
                    Olo_Security_Login::clear_lockouts();
                }
                return __( 'Blocchi di accesso azzerati.', 'olobuild' );

            case 'clear_csp':
                if ( class_exists( 'Olo_Security_Hardening' ) ) {
                    Olo_Security_Hardening::clear_reports();
                }
                return __( 'Report CSP svuotati.', 'olobuild' );

            case 'save_settings':
                if ( class_exists( 'Olo_Security_Login' ) ) {
                    Olo_Security_Login::save_settings( $_POST );
                }
                if ( class_exists( 'Olo_Security_Hardening' ) ) {
                    Olo_Security_Hardening::save_settings( $_POST );
                }
                update_option( self::OPT_EMAIL, empty( $_POST['olo_sentinel_email'] ) ? '0' : '1', false );
                update_option( 'olo_sec_webhook', [
                    'enabled' => empty( $_POST['olo_sec_webhook_enabled'] ) ? 0 : 1,
                    'url'     => esc_url_raw( wp_unslash( $_POST['olo_sec_webhook_url'] ?? '' ) ),
                ], false );
                return __( 'Impostazioni salvate.', 'olobuild' );
        }
        return '';
    }

    private static function render_view_stato( $status ) {
        $content    = isset( $status['content'] ) && is_array( $status['content'] ) ? $status['content'] : [];
        $files      = isset( $status['files'] ) && is_array( $status['files'] ) ? $status['files'] : [];
        $config     = isset( $status['config'] ) && is_array( $status['config'] ) ? $status['config'] : [];
        $components = isset( $status['components'] ) && is_array( $status['components'] ) ? $status['components'] : [];
        $baseline = get_option( self::OPT_BASE, [] );
        $has_backup = (bool) get_option( 'olo_sec_customcode_backup', false );
        $quarantined = self::list_quarantined();
        ?>
        <form method="post" style="margin:18px 0">
            <?php wp_nonce_field( 'olo_sentinel' ); ?>
            <input type="hidden" name="olo_sentinel_action" value="scan">
            <button type="submit" class="button button-primary"><?php esc_html_e( 'Scansiona ora', 'olobuild' ); ?></button>
        </form>

        <h3><?php esc_html_e( 'Codice personalizzato', 'olobuild' ); ?></h3>
        <?php if ( empty( $content ) ) : ?>
            <p style="color:#46b450">✓ <?php esc_html_e( 'Nessun pattern sospetto nel codice head/body/footer.', 'olobuild' ); ?></p>
        <?php else : ?>
            <?php self::render_findings_table( $content, true ); ?>
            <form method="post" style="margin:8px 0 4px">
                <?php wp_nonce_field( 'olo_sentinel' ); ?>
                <input type="hidden" name="olo_sentinel_action" value="clean_customcode">
                <button type="submit" class="button" onclick="return confirm('<?php echo esc_js( __( 'Svuoto il codice personalizzato (con backup). Continuare?', 'olobuild' ) ); ?>');"><?php esc_html_e( 'Pulisci codice personalizzato (con backup)', 'olobuild' ); ?></button>
            </form>
        <?php endif; ?>
        <?php if ( $has_backup ) : ?>
            <form method="post" style="margin:4px 0">
                <?php wp_nonce_field( 'olo_sentinel' ); ?>
                <input type="hidden" name="olo_sentinel_action" value="restore_customcode">
                <button type="submit" class="button-link"><?php esc_html_e( 'Ripristina il codice personalizzato dal backup', 'olobuild' ); ?></button>
            </form>
        <?php endif; ?>

        <h3 style="margin-top:26px"><?php esc_html_e( 'Configurazione & utenti', 'olobuild' ); ?></h3>
        <?php if ( empty( $config ) ) : ?>
            <p style="color:#46b450">✓ <?php esc_html_e( 'Opzioni critiche e amministratori invariati rispetto alla baseline.', 'olobuild' ); ?></p>
        <?php else : ?>
            <?php self::render_findings_table( $config, false ); ?>
            <form method="post" style="margin:8px 0">
                <?php wp_nonce_field( 'olo_sentinel' ); ?>
                <input type="hidden" name="olo_sentinel_action" value="rebaseline_config">
                <button type="submit" class="button" onclick="return confirm('<?php echo esc_js( __( 'Confermo lo stato attuale di configurazione/utenti come sicuro?', 'olobuild' ) ); ?>');"><?php esc_html_e( 'Conferma stato attuale come sicuro', 'olobuild' ); ?></button>
            </form>
        <?php endif; ?>

        <h3 style="margin-top:26px"><?php esc_html_e( 'Integrità file & webshell', 'olobuild' ); ?></h3>
        <?php if ( is_array( $baseline ) && ! empty( $baseline['created'] ) ) : ?>
            <p style="color:#666">
                <?php echo esc_html( sprintf(
                    /* translators: 1: count, 2: date, 3: version */
                    __( 'Baseline: %1$d file (.php/.js/.css del plugin + wp-config), creata il %2$s (versione %3$s).', 'olobuild' ),
                    is_array( $baseline['hashes'] ?? null ) ? count( $baseline['hashes'] ) : 0,
                    wp_date( 'd/m/Y H:i', (int) $baseline['created'] ),
                    $baseline['version'] ?? '—'
                ) ); ?>
            </p>
        <?php endif; ?>
        <?php if ( empty( $files ) ) : ?>
            <p style="color:#46b450">✓ <?php esc_html_e( 'Nessuna modifica sospetta, nessuna webshell rilevata.', 'olobuild' ); ?></p>
        <?php else : ?>
            <?php self::render_findings_table( $files, false, true ); ?>
            <form method="post" style="margin:8px 0">
                <?php wp_nonce_field( 'olo_sentinel' ); ?>
                <input type="hidden" name="olo_sentinel_action" value="rebaseline">
                <button type="submit" class="button" onclick="return confirm('<?php echo esc_js( __( 'Rigenera la baseline solo se le modifiche ai file sono legittime. Continuare?', 'olobuild' ) ); ?>');"><?php esc_html_e( 'Rigenera baseline (modifiche legittime)', 'olobuild' ); ?></button>
            </form>
        <?php endif; ?>

        <h3 style="margin-top:26px"><?php esc_html_e( 'Plugin & temi (integrità)', 'olobuild' ); ?></h3>
        <?php if ( empty( $components ) ) : ?>
            <p style="color:#46b450">✓ <?php esc_html_e( 'Nessun plugin o tema modificato senza un aggiornamento di versione.', 'olobuild' ); ?></p>
        <?php else : ?>
            <?php self::render_findings_table( $components, false ); ?>
            <form method="post" style="margin:8px 0">
                <?php wp_nonce_field( 'olo_sentinel' ); ?>
                <input type="hidden" name="olo_sentinel_action" value="rebuild_components">
                <button type="submit" class="button" onclick="return confirm('<?php echo esc_js( __( 'Confermo lo stato attuale di plugin e temi come sicuro?', 'olobuild' ) ); ?>');"><?php esc_html_e( 'Conferma stato attuale come sicuro', 'olobuild' ); ?></button>
            </form>
        <?php endif; ?>

        <?php if ( ! empty( $quarantined ) ) : ?>
            <h3 style="margin-top:26px"><?php esc_html_e( 'In quarantena', 'olobuild' ); ?></h3>
            <table class="widefat striped" style="max-width:900px">
                <tbody>
                <?php foreach ( $quarantined as $q ) : ?>
                    <tr>
                        <td><code style="font-size:11px;word-break:break-all"><?php echo esc_html( $q ); ?></code></td>
                        <td style="white-space:nowrap;text-align:right">
                            <form method="post" style="display:inline">
                                <?php wp_nonce_field( 'olo_sentinel' ); ?>
                                <input type="hidden" name="olo_sentinel_action" value="restore_quarantine">
                                <input type="hidden" name="file" value="<?php echo esc_attr( $q ); ?>">
                                <button type="submit" class="button-link"><?php esc_html_e( 'Ripristina', 'olobuild' ); ?></button>
                            </form>
                            &nbsp;|&nbsp;
                            <form method="post" style="display:inline">
                                <?php wp_nonce_field( 'olo_sentinel' ); ?>
                                <input type="hidden" name="olo_sentinel_action" value="delete_quarantine">
                                <input type="hidden" name="file" value="<?php echo esc_attr( $q ); ?>">
                                <button type="submit" class="button-link" style="color:#b32d2e" onclick="return confirm('<?php echo esc_js( __( 'Eliminare definitivamente questo file?', 'olobuild' ) ); ?>');"><?php esc_html_e( 'Elimina', 'olobuild' ); ?></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        <?php
    }

    /**
     * Tabella di finding riutilizzabile.
     *
     * @param array $rows           Finding.
     * @param bool  $with_snippet   Mostra la colonna "Estratto" (per il codice).
     * @param bool  $with_actions   Mostra il bottone Quarantena per i file ammessi.
     */
    private static function render_findings_table( $rows, $with_snippet = false, $with_actions = false ) {
        ?>
        <table class="widefat striped" style="max-width:980px">
            <thead><tr>
                <th style="width:70px"><?php esc_html_e( 'Gravità', 'olobuild' ); ?></th>
                <?php if ( $with_snippet ) : ?><th><?php esc_html_e( 'Posizione', 'olobuild' ); ?></th><?php endif; ?>
                <th><?php esc_html_e( 'Rilevamento', 'olobuild' ); ?></th>
                <th><?php echo $with_snippet ? esc_html__( 'Estratto', 'olobuild' ) : esc_html__( 'Dettaglio', 'olobuild' ); ?></th>
                <?php if ( $with_actions ) : ?><th style="width:110px"><?php esc_html_e( 'Azione', 'olobuild' ); ?></th><?php endif; ?>
            </tr></thead>
            <tbody>
            <?php foreach ( $rows as $f ) : ?>
                <tr>
                    <td><?php echo self::sev_badge( $f['severity'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- output controllato ?></td>
                    <?php if ( $with_snippet ) : ?><td><?php echo esc_html( $f['slot'] ?? '' ); ?></td><?php endif; ?>
                    <td>
                        <?php echo esc_html( $f['label'] ?? '' ); ?>
                        <?php if ( ! $with_snippet && ! empty( $f['path'] ) ) : ?>
                            <br><code style="font-size:11px;word-break:break-all"><?php echo esc_html( $f['path'] ); ?></code>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ( $with_snippet ) : ?>
                            <code style="font-size:11px;word-break:break-all"><?php echo esc_html( $f['snippet'] ?? '' ); ?></code>
                        <?php else : ?>
                            <span style="font-size:11px;color:#b32d2e"><?php echo esc_html( $f['detail'] ?? '' ); ?></span>
                        <?php endif; ?>
                    </td>
                    <?php if ( $with_actions ) : ?>
                        <td>
                            <?php if ( ! empty( $f['abs'] ) && self::is_within_quarantine_roots( $f['abs'] ) ) : ?>
                                <form method="post">
                                    <?php wp_nonce_field( 'olo_sentinel' ); ?>
                                    <input type="hidden" name="olo_sentinel_action" value="quarantine">
                                    <input type="hidden" name="file" value="<?php echo esc_attr( $f['abs'] ); ?>">
                                    <button type="submit" class="button button-small" onclick="return confirm('<?php echo esc_js( __( 'Mettere in quarantena questo file?', 'olobuild' ) ); ?>');"><?php esc_html_e( 'Quarantena', 'olobuild' ); ?></button>
                                </form>
                            <?php else : ?>
                                <span style="color:#999">—</span>
                            <?php endif; ?>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php
    }

    private static function render_view_log() {
        if ( ! class_exists( 'Olo_Security_Audit' ) ) {
            echo '<p>' . esc_html__( 'Registro non disponibile.', 'olobuild' ) . '</p>';
            return;
        }
        $type   = isset( $_GET['etype'] ) ? sanitize_key( wp_unslash( $_GET['etype'] ) ) : '';
        $events = Olo_Security_Audit::get_events( 200, $type );
        $types  = Olo_Security_Audit::get_types();
        $base_url = admin_url( 'admin.php?page=' . self::PAGE_SLUG . '&view=log' );
        ?>
        <form method="get" style="margin:16px 0;display:flex;gap:8px;align-items:center">
            <input type="hidden" name="page" value="<?php echo esc_attr( self::PAGE_SLUG ); ?>">
            <input type="hidden" name="view" value="log">
            <label><?php esc_html_e( 'Filtra per tipo:', 'olobuild' ); ?></label>
            <select name="etype" onchange="this.form.submit()">
                <option value=""><?php esc_html_e( 'Tutti', 'olobuild' ); ?></option>
                <?php foreach ( $types as $t ) : ?>
                    <option value="<?php echo esc_attr( $t ); ?>" <?php selected( $type, $t ); ?>><?php echo esc_html( $t ); ?></option>
                <?php endforeach; ?>
            </select>
        </form>

        <form method="post" style="margin:0 0 14px">
            <?php wp_nonce_field( 'olo_sentinel' ); ?>
            <input type="hidden" name="olo_sentinel_action" value="export_log">
            <button type="submit" class="button"><?php esc_html_e( 'Esporta CSV', 'olobuild' ); ?></button>
        </form>

        <?php if ( empty( $events ) ) : ?>
            <p style="color:#666"><?php esc_html_e( 'Nessun evento registrato.', 'olobuild' ); ?></p>
        <?php else : ?>
            <table class="widefat striped" style="max-width:1100px">
                <thead><tr>
                    <th><?php esc_html_e( 'Data', 'olobuild' ); ?></th>
                    <th><?php esc_html_e( 'Tipo', 'olobuild' ); ?></th>
                    <th><?php esc_html_e( 'Utente', 'olobuild' ); ?></th>
                    <th><?php esc_html_e( 'IP', 'olobuild' ); ?></th>
                    <th><?php esc_html_e( 'Evento', 'olobuild' ); ?></th>
                </tr></thead>
                <tbody>
                <?php foreach ( $events as $e ) : ?>
                    <tr>
                        <td style="white-space:nowrap"><?php echo esc_html( $e['created_at'] ); ?></td>
                        <td><span style="display:inline-block;padding:1px 7px;border-radius:4px;background:<?php echo esc_attr( Olo_Security_Audit::sev_color( $e['severity'] ) ); ?>;color:#fff;font-size:11px"><?php echo esc_html( $e['event_type'] ); ?></span></td>
                        <td><?php echo esc_html( $e['user_login'] ?: ( $e['user_id'] ? '#' . $e['user_id'] : '—' ) ); ?></td>
                        <td><code style="font-size:11px"><?php echo esc_html( $e['ip'] ); ?></code></td>
                        <td><?php echo esc_html( $e['message'] ); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <p style="color:#999;font-size:12px"><?php esc_html_e( 'Mostrati gli ultimi 200 eventi. Conservazione: 90 giorni.', 'olobuild' ); ?></p>
        <?php endif; ?>
        <?php
    }

    private static function render_view_settings() {
        $email_on = get_option( self::OPT_EMAIL, '1' ) === '1';
        $wh       = get_option( 'olo_sec_webhook', [] );
        $wh       = is_array( $wh ) ? $wh : [];
        ?>
        <form method="post" style="margin-top:16px">
            <?php wp_nonce_field( 'olo_sentinel' ); ?>
            <input type="hidden" name="olo_sentinel_action" value="save_settings">

            <h3><?php esc_html_e( 'Accesso (anti brute-force)', 'olobuild' ); ?></h3>
            <?php
            if ( class_exists( 'Olo_Security_Login' ) ) {
                Olo_Security_Login::render_settings_fields();
            }
            ?>

            <h3 style="margin-top:18px"><?php esc_html_e( 'Notifiche', 'olobuild' ); ?></h3>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><?php esc_html_e( 'Email di avviso', 'olobuild' ); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="olo_sentinel_email" value="1" <?php checked( $email_on ); ?>>
                            <?php echo esc_html( sprintf( __( 'Invia una email a %s quando viene rilevato qualcosa di sospetto', 'olobuild' ), get_option( 'admin_email' ) ) ); ?>
                        </label>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( 'Webhook', 'olobuild' ); ?></th>
                    <td>
                        <label style="display:block;margin-bottom:6px">
                            <input type="checkbox" name="olo_sec_webhook_enabled" value="1" <?php checked( ! empty( $wh['enabled'] ) ); ?>>
                            <?php esc_html_e( 'Invia gli avvisi anche a un webhook (Slack / Discord / Mattermost)', 'olobuild' ); ?>
                        </label>
                        <input type="url" name="olo_sec_webhook_url" value="<?php echo esc_attr( $wh['url'] ?? '' ); ?>" class="regular-text" placeholder="https://hooks.slack.com/services/...">
                    </td>
                </tr>
            </table>

            <h3 style="margin-top:18px"><?php esc_html_e( 'Protezioni del sito (opzionali)', 'olobuild' ); ?></h3>
            <p style="color:#666;max-width:760px;margin-top:0"><?php esc_html_e( 'Disattivate di default: attivale solo dopo aver verificato che non interferiscano con il tuo sito.', 'olobuild' ); ?></p>
            <?php
            if ( class_exists( 'Olo_Security_Hardening' ) ) {
                Olo_Security_Hardening::render_settings_fields();
            }
            ?>

            <p><button type="submit" class="button button-primary"><?php esc_html_e( 'Salva impostazioni', 'olobuild' ); ?></button></p>
        </form>

        <hr style="margin:22px 0">
        <h3><?php esc_html_e( 'Manutenzione', 'olobuild' ); ?></h3>
        <form method="post">
            <?php wp_nonce_field( 'olo_sentinel' ); ?>
            <input type="hidden" name="olo_sentinel_action" value="clear_lockouts">
            <button type="submit" class="button"><?php esc_html_e( 'Azzera i blocchi di accesso (sblocca tutti gli IP)', 'olobuild' ); ?></button>
        </form>
        <?php
        if ( class_exists( 'Olo_Security_Hardening' ) ) {
            Olo_Security_Hardening::render_csp_reports();
        }
    }

    // ──────────────────────────────────────────────────────────────────────
    //  Helper
    // ──────────────────────────────────────────────────────────────────────

    private static function snippet( $s ) {
        $s = preg_replace( '/\s+/', ' ', (string) $s );
        if ( function_exists( 'mb_substr' ) ) {
            return mb_substr( $s, 0, 120 );
        }
        return substr( $s, 0, 120 );
    }

    private static function sev_badge( $sev ) {
        $map = [
            'high'   => [ '#dc3232', __( 'Alta', 'olobuild' ) ],
            'medium' => [ '#dba617', __( 'Media', 'olobuild' ) ],
            'warn'   => [ '#dba617', __( 'Media', 'olobuild' ) ],
        ];
        $b = $map[ $sev ] ?? [ '#999', $sev ];
        return '<span style="display:inline-block;padding:2px 8px;border-radius:4px;background:'
            . esc_attr( $b[0] ) . ';color:#fff;font-size:11px;font-weight:600">' . esc_html( $b[1] ) . '</span>';
    }

    private static function host_matches( $host, $suffix ) {
        return $host === $suffix || ( strlen( $host ) > strlen( $suffix ) && substr( $host, - ( strlen( $suffix ) + 1 ) ) === '.' . $suffix );
    }

    private static function is_trusted_host( $host ) {
        foreach ( self::$trusted_hosts as $suffix ) {
            if ( self::host_matches( $host, $suffix ) ) {
                return true;
            }
        }
        return false;
    }

    /** Suffissi host attendibili (riusati da Olo_Security_Hardening per costruire la CSP). */
    public static function trusted_hosts() {
        return self::$trusted_hosts;
    }
}
