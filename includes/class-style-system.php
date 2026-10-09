<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Style_System {

    /** Versioni dello stile: option, quante se ne tengono, finestra di accorpamento (s dall'ultimo salvataggio). */
    const SNAPSHOT_OPT     = 'olobuild_design_preset_snapshots';
    const SNAPSHOT_MAX     = 10;
    const SNAPSHOT_ACCORPA = 900;

    /** Motivi delle istantanee di un import: la più recente non esce mai dall'elenco. */
    const SNAPSHOT_IMPORT = [ 'import_tema', 'import_sito' ];

    private static $instance = null;

    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {}

    /**
     * Default style values.
     */
    public function get_defaults() {
        return [
            'colors' => [
                'primary'            => '#e1474f',
                'primary_contrast'   => '#FFFFFF',
                'secondary'          => '#16263d',
                'secondary_contrast' => '#FFFFFF',
                'muted'              => '#F3F4F6',
                'muted_contrast'     => '#374151',
                'success'            => '#10B981',
                'warning'            => '#F59E0B',
                'danger'             => '#EF4444',
                'text'               => '#374151',
                'text_muted'         => '#9CA3AF',
                'background'         => '#FFFFFF',
                'border'             => '#E5E7EB',
                'link'               => '#e1474f',
            ],
            'typography' => [
                'font_family'              => '',
                'font_family_heading'      => '',
                'font_family_mono'         => '',
                'font_size_base'           => '16px',
                'font_size_h1'             => '2.5rem',
                'font_size_h2'             => '2rem',
                'font_size_h3'             => '1.75rem',
                'font_size_h4'             => '1.5rem',
                'font_size_h5'             => '1.25rem',
                'font_size_h6'             => '1rem',
                'line_height'              => '1.6',
                'font_weight_heading'      => '700',
                'letter_spacing'           => '0',
                'font_weight_body'         => '400',
                'font_size_h1_tablet'      => '',
                'font_size_h2_tablet'      => '',
                'font_size_h3_tablet'      => '',
                'font_size_h1_mobile'      => '',
                'font_size_h2_mobile'      => '',
                'font_size_h3_mobile'      => '',
                'heading_line_height'      => '1.3',
                'heading_letter_spacing'   => '0',
                'heading_text_transform'   => 'none',
            ],
            'layout' => [
                'border_radius'        => '4px',
                'border_radius_large'  => '8px',
                'container_max_width'  => '1200px',
                'container_narrow'     => '720px',
                'container_wide'       => '1440px',
            ],
            'spacing' => [
                'xs'  => '4px',
                'sm'  => '8px',
                'md'  => '16px',
                'lg'  => '24px',
                'xl'  => '32px',
                '2xl' => '48px',
                '3xl' => '64px',
                '4xl' => '96px',
            ],
            'section_padding' => [
                'compact'  => 'lg',
                'default'  => 'xl',
                'spacious' => '2xl',
                'between'  => 'md',
            ],
            'gutter' => [
                'desktop'      => 32,
                'tablet'       => 24,
                'mobile'       => 16,
                'side_desktop' => 32,
                'side_mobile'  => 16,
            ],
            'fluid_scaling' => [
                'enabled' => false,
                'tablet'  => 0.85,
                'mobile'  => 0.65,
            ],
            // Grain / noise overlay site-wide (texture fine sopra tutta la pagina).
            'grain' => [
                'enabled' => false,
                'opacity' => 6,   // percentuale (0-30 sensata)
                'scale'   => 180, // px del tile di rumore
            ],
            'buttons' => [
                'font_size'        => '14px',
                'font_weight'      => '600',
                'padding_x'        => '24px',
                'padding_y'        => '10px',
                'border_radius'    => '4px',
                'text_transform'   => 'none',
                'letter_spacing'   => '0',
                'hover_brightness' => '90',
            ],
            'forms' => [
                'field_bg'            => '#ffffff',
                'field_border_color'  => '#d1d5db',
                'field_border_width'  => '1',
                'field_border_radius' => '4px',
                'field_padding'       => '10px 14px',
                'field_font_size'     => '14px',
                'focus_border_color'  => '#6366F1',
                'focus_shadow'        => '0 0 0 3px rgba(99,102,241,0.15)',
                'label_font_size'     => '14px',
                'label_font_weight'   => '500',
                'label_margin_bottom' => '4px',
            ],
            'links' => [
                'color'            => '',
                'hover_color'      => '',
                'decoration'       => 'none',
                'hover_decoration' => 'underline',
            ],
            'dark_colors' => [
                'primary'            => '#818CF8',
                'primary_contrast'   => '#FFFFFF',
                'secondary'          => '#312E81',
                'secondary_contrast' => '#E0E7FF',
                'muted'              => '#1E293B',
                'muted_contrast'     => '#CBD5E1',
                'success'            => '#34D399',
                'warning'            => '#FBBF24',
                'danger'             => '#F87171',
                'text'               => '#E2E8F0',
                'text_muted'         => '#94A3B8',
                'background'         => '#0F172A',
                'border'             => '#334155',
                'link'               => '#818CF8',
            ],
            'google_fonts' => [],
            // Scala neutri + meta dark mode — gestiti dalla pagina admin "Palette colori".
            'neutrals' => [
                'mode'  => 'auto',
                'tint'  => 'zinc',
                'scale' => [ '#FAFAFA', '#F4F4F5', '#E4E4E7', '#A1A1AA', '#52525B', '#27272A', '#09090B' ],
            ],
            'dark_mode' => [
                'enabled'  => true,
                'strategy' => 'auto',
            ],
        ];
    }

    /**
     * Get saved styles merged with defaults.
     */
    public function get_styles() {
        $saved    = get_option( 'olobuild_styles', [] );
        $defaults = $this->get_defaults();

        return [
            'colors'          => wp_parse_args( $saved['colors'] ?? [], $defaults['colors'] ),
            'typography'      => wp_parse_args( $saved['typography'] ?? [], $defaults['typography'] ),
            'layout'          => wp_parse_args( $saved['layout'] ?? [], $defaults['layout'] ),
            'buttons'         => wp_parse_args( $saved['buttons'] ?? [], $defaults['buttons'] ),
            'forms'           => wp_parse_args( $saved['forms'] ?? [], $defaults['forms'] ),
            'links'           => wp_parse_args( $saved['links'] ?? [], $defaults['links'] ),
            'dark_colors'     => wp_parse_args( $saved['dark_colors'] ?? [], $defaults['dark_colors'] ),
            'google_fonts'    => $saved['google_fonts'] ?? $defaults['google_fonts'],
            'spacing'         => wp_parse_args( $saved['spacing'] ?? [], $defaults['spacing'] ),
            'section_padding' => wp_parse_args( $saved['section_padding'] ?? [], $defaults['section_padding'] ),
            'gutter'          => wp_parse_args( $saved['gutter'] ?? [], $defaults['gutter'] ),
            'fluid_scaling'   => wp_parse_args( $saved['fluid_scaling'] ?? [], $defaults['fluid_scaling'] ),
            'grain'           => wp_parse_args( $saved['grain'] ?? [], $defaults['grain'] ),
            'neutrals'        => wp_parse_args( $saved['neutrals'] ?? [], $defaults['neutrals'] ),
            'dark_mode'       => wp_parse_args( $saved['dark_mode'] ?? [], $defaults['dark_mode'] ),
        ];
    }

    /**
     * Save styles to wp_options.
     *
     * Merge con i valori già salvati per blocco: un PUT parziale (es. solo fluid_scaling)
     * non deve cancellare gli altri blocchi (colors, typography, spacing, ecc.).
     */
    public function save_styles( $styles ) {
        $sanitized = $this->sanitize_styles( $styles );
        $existing  = get_option( 'olobuild_styles', [] );
        if ( ! is_array( $existing ) ) $existing = [];
        $merged    = array_replace( $existing, $sanitized );
        update_option( 'olobuild_styles', $merged, false );

        // Allinea i global color dei ruoli core al valore appena salvato (vedi sync_global_palette).
        if ( isset( $sanitized['colors'] ) && is_array( $sanitized['colors'] ) ) {
            $this->sync_global_palette( $sanitized['colors'] );

            // Un ruolo brand cambiato nel pannello deve cambiare anche in modalità scura:
            // il blocco `html.olo-dark-mode .olo-template` ha specificità maggiore di
            // `.olo-template`, quindi con dark_colors fermi ai valori del tema importato le
            // modifiche ai Colori del brand non si vedevano affatto sui siti in dark mode.
            // Propago SOLO i ruoli toccati adesso, così una palette scura personalizzata
            // sugli altri ruoli resta intatta.
            $before  = ( isset( $existing['colors'] ) && is_array( $existing['colors'] ) ) ? $existing['colors'] : [];
            $touched = [];
            foreach ( [ 'primary', 'primary_contrast', 'secondary', 'secondary_contrast', 'link' ] as $role ) {
                if ( isset( $sanitized['colors'][ $role ] )
                    && ( $before[ $role ] ?? null ) !== $sanitized['colors'][ $role ] ) {
                    $touched[] = $role;
                }
            }
            if ( $touched ) {
                $this->sync_dark_palette( $sanitized['colors'], $touched );
                $merged = get_option( 'olobuild_styles', $merged );
            }
        }

        return $merged;
    }

    /**
     * Allinea olo_global_colors ai colori del tema/palette (ruoli core).
     *
     * In generate_css i olo_global_colors[id core] sono emessi DOPO olo_styles.colors e VINCONO
     * nel CSS: se restano placeholder (es. import tema che scrive solo olo_styles) SOVRASCRIVONO
     * la palette del cliente. Questo li tiene allineati, qualunque sia il flusso (UI, API, import).
     * accent/accent-2/accent_2 (senza equivalente diretto in colors) seguono primary/secondary.
     * Un id con «_» (text_muted, primary_contrast) non si allinea: esce come
     * --olo-color-text_muted, una variabile diversa da quella del ruolo, che non copre niente.
     * È un colore a sé, modificabile fra gli extra di Configurazione › Palette: riallinearlo al
     * ruolo riscriveva a ogni salvataggio della palette il valore scelto lì. Stessa regola di
     * copreRuolo() in ColorsTab.vue. Idempotente.
     *
     * @param array $colors  blocco olo_styles['colors'] (primary/secondary/...).
     */
    public function sync_global_palette( $colors ) {
        if ( ! is_array( $colors ) || empty( $colors ) ) {
            return;
        }
        $fallbacks = self::accenti_della_palette( $colors );
        $gc = get_option( 'olobuild_global_colors', [] );
        if ( ! is_array( $gc ) || ! $gc ) {
            return;
        }
        $changed = false;
        foreach ( $gc as &$g ) {
            $id = ( is_array( $g ) && isset( $g['id'] ) && is_scalar( $g['id'] ) ) ? (string) $g['id'] : '';
            if ( '' === $id ) {
                continue;
            }
            $del_ruolo = false === strpos( $id, '_' ) ? ( $colors[ $id ] ?? null ) : null;
            $val       = $del_ruolo ?? ( $fallbacks[ $id ] ?? null );
            if ( null !== $val && ( ! isset( $g['value'] ) || $g['value'] !== $val ) ) {
                $g['value'] = $val;
                $changed    = true;
            }
        }
        unset( $g );
        if ( $changed ) {
            update_option( 'olobuild_global_colors', $gc, false );
        }
    }

    /**
     * I colori globali che seguono la palette senza esserne una chiave: accent
     * segue primary, accent-2 / accent_2 seguono secondary (se lo stile non ne
     * ha di suoi). null = nessun valore da dare.
     *
     * @param array $colors blocco olobuild_styles['colors'].
     * @return array [ id => valore|null ]
     */
    private static function accenti_della_palette( $colors ) {
        $colors = is_array( $colors ) ? $colors : [];
        return [
            'accent'   => $colors['accent']   ?? ( $colors['primary']   ?? null ),
            'accent-2' => $colors['accent-2'] ?? ( $colors['secondary'] ?? null ),
            'accent_2' => $colors['accent_2'] ?? ( $colors['secondary'] ?? null ),
        ];
    }

    /**
     * Allinea i ruoli BRAND della palette in modalità scura (olo_styles['dark_colors']) ai
     * colori del tema. I default generici (indaco/slate) farebbero rendere il primario, gli
     * accenti e i link in indaco quando html.olo-dark-mode è attivo, ignorando il tema. Tocca
     * SOLO i ruoli brand: i neutri dark (background/text/border) restano (dark mode resta scuro).
     * Usato dall'IMPORT di un tema (tutti i ruoli brand) e da save_styles() con $only = i soli
     * ruoli appena modificati nel pannello. Idempotente.
     *
     * @param array      $colors blocco olo_styles['colors'].
     * @param array|null $only   sottoinsieme di ruoli brand da propagare; null = tutti.
     */
    public function sync_dark_palette( $colors, $only = null ) {
        if ( ! is_array( $colors ) || empty( $colors ) ) {
            return;
        }
        $st = get_option( 'olobuild_styles', [] );
        if ( ! is_array( $st ) ) {
            return;
        }
        $dc    = ( isset( $st['dark_colors'] ) && is_array( $st['dark_colors'] ) ) ? $st['dark_colors'] : [];
        $brand = [ 'primary', 'primary_contrast', 'secondary', 'secondary_contrast', 'link' ];
        if ( is_array( $only ) && $only ) {
            $brand = array_values( array_intersect( $brand, $only ) );
        }
        $changed = false;
        foreach ( $brand as $k ) {
            if ( isset( $colors[ $k ] ) && ( ! isset( $dc[ $k ] ) || $dc[ $k ] !== $colors[ $k ] ) ) {
                $dc[ $k ]  = $colors[ $k ];
                $changed   = true;
            }
        }
        if ( $changed ) {
            $st['dark_colors'] = $dc;
            update_option( 'olobuild_styles', $st, false );
        }
    }

    /**
     * Reset to defaults.
     *
     * Lo stile di prima va fra le versioni (se il ripristino cambia qualcosa):
     * senza, il ripristino ai valori predefiniti cancellava olobuild_styles e non
     * si tornava indietro. delete_option non svuota la cache delle pagine (si
     * aggancia a update_option): la si svuota qui.
     */
    public function reset_styles() {
        $prima = $this->stato_stile();
        delete_option( 'olobuild_styles' );
        $this->take_snapshot( 'ripristino_stili', '', [], $prima );
        self::svuota_cache_pagine();
        return $this->get_defaults();
    }

    /* ── Versioni dello stile (istantanee) ──────────────────────────────────
     *
     * Import di un tema, salvataggio degli stili, ripristino dei predefiniti e
     * import del sito cambiano lo stile di TUTTO il sito in un colpo: colori,
     * tipografia, set tipografici e font, header, footer e 404 attivi, cursore e
     * mirino, pagina iniziale. Prima di farlo si
     * copia com'era (al massimo 10 copie, in olobuild_design_preset_snapshots,
     * autoload no; la copia dell'ultimo import non esce mai), e «Ripristina» lo
     * rimette.
     */

    /**
     * Le option che quei flussi cambiano per tutto il sito. I set tipografici e
     * i font caricati li sostituisce l'import del sito (import_global_options).
     */
    private static function snapshot_keys() {
        return [
            'olobuild_styles',
            'olobuild_global_colors',
            'olobuild_global_typography',
            'olobuild_custom_fonts',
            'olobuild_active_header',
            'olobuild_active_footer',
            'olobuild_active_404',
            'olobuild_magnetic_cursor',
            'olobuild_cursor_hud',
            'show_on_front',
            'page_on_front',
            'page_for_posts',
        ];
    }

    /** L'elenco salvato, tollerante: un'option rovinata vale «nessuna istantanea». */
    private function snapshot_list() {
        $list = get_option( self::SNAPSHOT_OPT, [] );
        if ( ! is_array( $list ) ) {
            return [];
        }
        return array_values( array_filter( $list, function ( $v ) {
            return is_array( $v ) && ! empty( $v['id'] ) && is_scalar( $v['id'] );
        } ) );
    }

    /**
     * Com'è adesso una pagina che un import sta per riusare: template, titolo, stato
     * e header/footer assegnati a lei (meta `_olo_header_id` / `_olo_footer_id`).
     *
     * @return array|null null se la pagina non esiste.
     */
    public static function stato_pagina( $page_id ) {
        $page_id = (int) $page_id;
        $post    = $page_id > 0 ? get_post( $page_id ) : null;
        if ( ! $post ) {
            return null;
        }
        $vuoto = function ( $v ) {
            return '' === $v || false === $v || null === $v;
        };
        $tpl    = get_post_meta( $page_id, '_olo_template_id', true );
        $header = get_post_meta( $page_id, '_olo_header_id', true );
        $footer = get_post_meta( $page_id, '_olo_footer_id', true );
        return [
            'tpl'    => $vuoto( $tpl ) ? null : (string) $tpl,
            'title'  => (string) $post->post_title,
            'status' => (string) $post->post_status,
            'header' => $vuoto( $header ) ? null : (string) $header,
            'footer' => $vuoto( $footer ) ? null : (string) $footer,
        ];
    }

    private static function pulisci_pagine( $pagine ) {
        $out = [];
        foreach ( is_array( $pagine ) ? $pagine : [] as $pid => $stato ) {
            $pid = (int) $pid;
            if ( $pid <= 0 || ! is_array( $stato ) ) {
                continue;
            }
            $out[ $pid ] = [
                'tpl'    => ( isset( $stato['tpl'] ) && is_scalar( $stato['tpl'] ) ) ? (string) $stato['tpl'] : null,
                'title'  => (string) ( $stato['title'] ?? '' ),
                'status' => (string) ( $stato['status'] ?? '' ),
            ];
            // Header e footer della pagina: le istantanee di prima non li hanno, e allora
            // il ripristino non li tocca (chiave assente ≠ «nessuno»).
            foreach ( [ 'header', 'footer' ] as $zona ) {
                if ( array_key_exists( $zona, $stato ) ) {
                    $out[ $pid ][ $zona ] = ( isset( $stato[ $zona ] ) && is_scalar( $stato[ $zona ] ) ) ? (string) $stato[ $zona ] : null;
                }
            }
        }
        return $out;
    }

    /**
     * Lo stile del sito com'è adesso: le option di snapshot_keys(), quelle che
     * non esistono e un hash per confrontare due stati.
     *
     * @return array [ 'options' => [], 'absent' => [], 'hash' => string ]
     */
    public function stato_stile() {
        $assente = new stdClass();
        $options = [];
        $absent  = [];
        foreach ( self::snapshot_keys() as $k ) {
            $v = get_option( $k, $assente );
            if ( $v === $assente ) {
                $absent[] = $k;
            } else {
                $options[ $k ] = $v;
            }
        }
        return [
            'options' => $options,
            'absent'  => $absent,
            'hash'    => md5( maybe_serialize( [ $options, $absent ] ) ),
        ];
    }

    /**
     * Copia lo stile del sito com'è adesso e la mette in testa all'elenco.
     *
     * Nessuna voce nuova se lo stato è identico all'ultima istantanea con lo
     * stesso motivo, o se è un altro salvataggio degli stili dello stesso utente
     * entro 15 minuti dal suo salvataggio precedente (la voce accorpata ricorda
     * l'ora dell'ultimo in 'ultimo', la copia resta quella di prima): una
     * sessione di ritocchi, anche lunga, è una voce sola, con la copia di PRIMA
     * della sessione.
     *
     * Oltre il tetto (SNAPSHOT_MAX, filtro olobuild_style_snapshots_max) escono
     * le voci più vecchie, mai la nuova, l'istantanea di import (tema o sito)
     * più recente né $conserva: una serie di salvataggi non spinge fuori la
     * copia di prima dell'import, e ripristinare la voce più vecchia non la fa
     * sparire dall'elenco.
     *
     * Con $prima (stato_stile() preso PRIMA di un'operazione già fatta) si
     * registra quello, e solo se l'operazione ha cambiato qualcosa: un
     * salvataggio degli stili senza modifiche non aggiunge voci.
     *
     * @param string     $motivo    import_tema | stili_salvati | ripristino_stili | prima_del_ripristino | import_sito
     * @param string     $dettaglio Per esempio il nome del tema.
     * @param array      $pagine    [ page_id => stato_pagina() ] delle pagine da rimettere.
     * @param array|null $prima     stato_stile() di prima dell'operazione.
     * @param string     $conserva  Id di un'istantanea che il taglio non deve togliere (quella che si sta ripristinando).
     * @return string Id dell'istantanea (quella nuova, o quella che già vale); '' se niente è cambiato.
     */
    public function take_snapshot( $motivo, $dettaglio = '', $pagine = [], $prima = null, $conserva = '' ) {
        $motivo = sanitize_key( (string) $motivo );
        $pagine = self::pulisci_pagine( $pagine );
        if ( is_array( $prima ) && isset( $prima['hash'], $prima['options'], $prima['absent'] ) ) {
            if ( $this->stato_stile()['hash'] === $prima['hash'] ) {
                return '';
            }
            $stato = $prima;
        } else {
            $stato = $this->stato_stile();
        }
        $options = $stato['options'];
        $absent  = $stato['absent'];
        $hash    = $stato['hash'];
        $utente  = get_current_user_id();
        $ora     = time();
        $list    = $this->snapshot_list();

        if ( $list ) {
            $ultima        = $list[0];
            $stesso_motivo = ( $ultima['motivo'] ?? '' ) === $motivo;
            if ( $stesso_motivo && ! $pagine && empty( $ultima['pagine'] ) && ( $ultima['hash'] ?? '' ) === $hash ) {
                return (string) $ultima['id'];
            }
            // Dall'ultima attività, non dall'ora della voce: con un salvataggio ogni
            // 10 minuti nasceva una voce ogni 20 e in qualche ora l'elenco si riempiva.
            if ( 'stili_salvati' === $motivo && $stesso_motivo
                && (int) ( $ultima['utente'] ?? 0 ) === $utente
                && ( $ora - (int) ( $ultima['ultimo'] ?? ( $ultima['ora'] ?? 0 ) ) ) < self::SNAPSHOT_ACCORPA ) {
                $list[0]['ultimo'] = $ora;
                update_option( self::SNAPSHOT_OPT, $list, false );
                return (string) $ultima['id'];
            }
        }

        $voce = [
            'id'        => str_replace( '-', '', wp_generate_uuid4() ),
            'motivo'    => $motivo,
            'dettaglio' => sanitize_text_field( (string) $dettaglio ),
            'utente'    => $utente,
            'ora'       => $ora,
            'hash'      => $hash,
            'options'   => $options,
            'absent'    => $absent,
            'pagine'    => $pagine,
        ];
        array_unshift( $list, $voce );
        $max  = max( 1, (int) apply_filters( 'olobuild_style_snapshots_max', self::SNAPSHOT_MAX ) );
        $list = self::taglia_istantanee( $list, $max, [ $voce['id'], (string) $conserva ] );
        update_option( self::SNAPSHOT_OPT, $list, false );
        return $voce['id'];
    }

    /**
     * Porta l'elenco (dalla più nuova alla più vecchia) a $max voci togliendo
     * le più vecchie, tranne quelle in $conserva e l'istantanea di import (tema
     * o sito, SNAPSHOT_IMPORT) più recente. Se restano solo voci protette
     * l'elenco può superare $max (succede solo con un tetto sotto 3).
     *
     * @param array    $list     Elenco delle istantanee.
     * @param int      $max      Quante tenerne.
     * @param string[] $conserva Id da non togliere.
     * @return array
     */
    private static function taglia_istantanee( $list, $max, $conserva ) {
        $protette = [];
        foreach ( (array) $conserva as $id ) {
            if ( '' !== (string) $id ) {
                $protette[ (string) $id ] = true;
            }
        }
        foreach ( $list as $voce ) {
            if ( in_array( (string) ( $voce['motivo'] ?? '' ), self::SNAPSHOT_IMPORT, true ) ) {
                $protette[ (string) $voce['id'] ] = true;
                break;
            }
        }
        $list = array_values( $list );
        for ( $i = count( $list ) - 1; $i >= 0 && count( $list ) > $max; $i-- ) {
            if ( ! isset( $protette[ (string) $list[ $i ]['id'] ] ) ) {
                array_splice( $list, $i, 1 );
            }
        }
        return $list;
    }

    /**
     * Aggiunge a un'istantanea le pagine riusate da un import (com'erano prima):
     * si sa quali sono solo mentre l'import le tocca. Una pagina già registrata
     * resta com'era.
     */
    public function amend_snapshot_pages( $id, $pagine ) {
        $pagine = self::pulisci_pagine( $pagine );
        if ( ! $pagine ) {
            return;
        }
        $list = $this->snapshot_list();
        foreach ( $list as $i => $voce ) {
            if ( (string) $voce['id'] !== (string) $id ) {
                continue;
            }
            $gia                  = is_array( $voce['pagine'] ?? null ) ? $voce['pagine'] : [];
            $list[ $i ]['pagine'] = $gia + $pagine;
            update_option( self::SNAPSHOT_OPT, $list, false );
            return;
        }
    }

    /**
     * L'elenco da mostrare: solo i metadati, mai le copie.
     */
    public function list_snapshots() {
        $out = [];
        foreach ( $this->snapshot_list() as $voce ) {
            $opt      = is_array( $voce['options'] ?? null ) ? $voce['options'] : [];
            $styles   = is_array( $opt['olobuild_styles'] ?? null ) ? $opt['olobuild_styles'] : [];
            $primario = '';
            // Nel CSS il global color «primary» vince su styles.colors.primary.
            foreach ( is_array( $opt['olobuild_global_colors'] ?? null ) ? $opt['olobuild_global_colors'] : [] as $gc ) {
                if ( is_array( $gc ) && 'primary' === ( $gc['id'] ?? '' ) && ! empty( $gc['value'] ) && is_string( $gc['value'] ) ) {
                    $primario = $gc['value'];
                }
            }
            if ( '' === $primario && is_string( $styles['colors']['primary'] ?? null ) ) {
                $primario = $styles['colors']['primary'];
            }
            $font   = $styles['typography']['font_family_heading'] ?? '';
            $utente = get_userdata( (int) ( $voce['utente'] ?? 0 ) );
            $out[]  = [
                'id'          => (string) $voce['id'],
                'motivo'      => (string) ( $voce['motivo'] ?? '' ),
                'dettaglio'   => (string) ( $voce['dettaglio'] ?? '' ),
                'utente'      => $utente ? (string) $utente->display_name : '',
                'ora'         => (int) ( $voce['ora'] ?? 0 ),
                'primario'    => sanitize_text_field( $primario ),
                'font_titoli' => is_string( $font ) ? sanitize_text_field( $font ) : '',
                'pagine'      => is_array( $voce['pagine'] ?? null ) ? count( $voce['pagine'] ) : 0,
            ];
        }
        return $out;
    }

    /**
     * Rimette lo stile del sito com'era nell'istantanea $id.
     *
     * Prima copia lo stato attuale («prima del ripristino»): anche il ripristino
     * si annulla. Le option si riscrivono come erano (quelle che allora non
     * esistevano si cancellano), tranne colori globali, set tipografici e font
     * caricati: tornano quelli dell'istantanea e restano, in coda, quelli nati
     * dopo, che le tile possono già usare (var(--olo-color-<id>),
     * var(--olo-font-<id>-*)). Non restano i colori nati dopo con l'id di una
     * chiave dei colori dello stile (primary, text-muted…), che nel CSS escono
     * dopo lo stile appena rimesso e lo coprirebbero, né quelli con l'id di un
     * ruolo globale delle tile (accent, dark, light, RUOLI_GLOBALI_TILE), che
     * ricolorerebbero le tile che li usano (accent, dark e light anche dai loro
     * default, RUOLI_LETTI_DAI_DEFAULT). Nemmeno quelli con l'id di un alias
     * fisso (error, info, surface…, id_colori_dello_stile()): nel CSS l'alias
     * esce dopo e vince, quindi non agiscono, e fra gli extra mostrerebbero un
     * valore diverso da quello reso. Un header, footer, 404, una pagina (anche
     * iniziale o degli articoli, anche nel cestino) o il template di una
     * pagina eliminati nel frattempo non si rimettono (il sito resterebbe
     * senza): restano quelli attuali e la risposta li elenca in 'saltati' (le
     * pagine come 'pagina:<id>', 'template_pagina:<id>', 'header_pagina:<id>' e
     * 'footer_pagina:<id>' (header e footer assegnati alla pagina), col titolo in
     * 'titoli'); senza la pagina iniziale non si riscrive nemmeno
     * show_on_front. Un font caricato i cui file sono stati cancellati non
     * torna: 'font:<id>', col nome in 'font'.
     *
     * @return array|WP_Error [ 'ok' => true, 'prima' => id, 'saltati' => [ … ], 'titoli' => [ page_id => titolo ], 'font' => [ font_id => nome ] ]
     */
    public function restore_snapshot( $id ) {
        $voce = null;
        foreach ( $this->snapshot_list() as $v ) {
            if ( (string) $v['id'] === (string) $id ) {
                $voce = $v;
                break;
            }
        }
        if ( ! $voce ) {
            return new WP_Error( 'olobuild_snapshot_not_found', __( 'Versione dello stile non trovata.', 'olobuild' ), [ 'status' => 404 ] );
        }

        $pagine     = is_array( $voce['pagine'] ?? null ) ? $voce['pagine'] : [];
        $pagine_ora = [];
        foreach ( array_keys( $pagine ) as $pid ) {
            $stato = self::stato_pagina( $pid );
            if ( $stato ) {
                $pagine_ora[ $pid ] = $stato;
            }
        }
        // Con l'elenco pieno il taglio toglieva proprio la voce più vecchia: se è
        // quella che si ripristina, resta.
        $prima = $this->take_snapshot( 'prima_del_ripristino', '', $pagine_ora, null, (string) $voce['id'] );

        $opzioni = is_array( $voce['options'] ?? null ) ? $voce['options'] : [];
        $assenti = is_array( $voce['absent'] ?? null ) ? $voce['absent'] : [];
        $zone    = [ 'olobuild_active_header', 'olobuild_active_footer', 'olobuild_active_404' ];
        $db      = class_exists( 'Olobuild_Database' ) ? new Olobuild_Database() : null;
        $saltati = [];
        $titoli  = [];
        $font    = [];

        // Colori dello stile che si sta rimettendo (con i predefiniti, come get_styles()).
        $stile_snap = ( is_array( $opzioni['olobuild_styles'] ?? null ) && is_array( $opzioni['olobuild_styles']['colors'] ?? null ) )
            ? $opzioni['olobuild_styles']['colors']
            : [];

        // La pagina iniziale di allora non si può rimettere: show_on_front resta
        // com'è. Riscritto a «page» senza pagina (WordPress, eliminandola o
        // cestinandola, l'aveva riportato agli articoli), la home diventava una
        // pagina statica senza pagina, o una pagina nel cestino: 404.
        $home_saltata = ! in_array( 'page_on_front', $assenti, true )
            && (int) ( $opzioni['page_on_front'] ?? 0 ) > 0
            && ! self::pagina_rimettibile( (int) $opzioni['page_on_front'], $pagine );

        foreach ( self::snapshot_keys() as $k ) {
            if ( 'olobuild_global_colors' === $k ) {
                $escludi = self::id_colori_dello_stile( $stile_snap ) + array_fill_keys( self::RUOLI_GLOBALI_TILE, true );
                $fusi    = self::fondi_per_id( $opzioni[ $k ] ?? [], get_option( $k, [] ), $escludi );
                // Assente allora e niente da tenere: si torna ad assente, non a [].
                if ( in_array( $k, $assenti, true ) && ! $fusi ) {
                    delete_option( $k );
                } else {
                    update_option( $k, $fusi, false );
                }
                continue;
            }
            if ( in_array( $k, [ 'olobuild_global_typography', 'olobuild_custom_fonts' ], true ) ) {
                $attuali = get_option( $k, [] );
                if ( in_array( $k, $assenti, true ) && ( ! is_array( $attuali ) || ! $attuali ) ) {
                    delete_option( $k );
                } else {
                    $dallo_snap = $opzioni[ $k ] ?? [];
                    if ( 'olobuild_custom_fonts' === $k ) {
                        $dallo_snap = self::font_con_file( $dallo_snap, $attuali, $saltati, $font );
                    }
                    // Autoload come li salvano i loro pannelli: i set tipografici no, i font caricati invariato.
                    update_option( $k, self::fondi_per_id( $dallo_snap, $attuali ), 'olobuild_global_typography' === $k ? false : null );
                }
                continue;
            }
            if ( in_array( $k, $assenti, true ) ) {
                delete_option( $k );
                continue;
            }
            if ( ! array_key_exists( $k, $opzioni ) ) {
                continue;
            }
            $v = $opzioni[ $k ];
            if ( 'show_on_front' === $k && $home_saltata && 'page' === $v ) {
                continue;
            }
            if ( in_array( $k, $zone, true ) && (int) $v > 0 && ( ! $db || ! $db->get_template( (int) $v ) ) ) {
                $saltati[] = $k;
                continue;
            }
            if ( in_array( $k, [ 'page_on_front', 'page_for_posts' ], true ) && (int) $v > 0 && ! self::pagina_rimettibile( (int) $v, $pagine ) ) {
                // Con la home sugli articoli quella pagina non era in uso (WordPress ne
                // conserva l'id): resta il valore attuale, senza segnalare una perdita.
                if ( 'page' === ( $opzioni['show_on_front'] ?? '' ) ) {
                    $saltati[] = $k;
                }
                continue;
            }
            update_option( $k, $v );
        }

        foreach ( $pagine as $pid => $stato ) {
            $pid = (int) $pid;
            if ( $pid <= 0 || ! is_array( $stato ) ) {
                continue;
            }
            $post = get_post( $pid );
            if ( ! $post ) {
                $saltati[]      = 'pagina:' . $pid;
                $titoli[ $pid ] = (string) ( $stato['title'] ?? '' );
                continue;
            }
            if ( ! isset( $stato['tpl'] ) || null === $stato['tpl'] ) {
                delete_post_meta( $pid, '_olo_template_id' );
            } elseif ( (int) $stato['tpl'] > 0 && ( ! $db || ! $db->get_template( (int) $stato['tpl'] ) ) ) {
                // Il template di allora non c'è più: la pagina tiene quello attuale.
                $saltati[]      = 'template_pagina:' . $pid;
                $titoli[ $pid ] = (string) ( $stato['title'] ?? '' );
            } else {
                update_post_meta( $pid, '_olo_template_id', $stato['tpl'] );
            }
            // Header e footer assegnati alla pagina (l'import li toglie perché valgano
            // quelli del tema). Un template eliminato nel frattempo non si rimette: la
            // pagina resterebbe senza header (render_header() non trova niente).
            foreach ( [ 'header', 'footer' ] as $zona ) {
                if ( ! array_key_exists( $zona, $stato ) ) {
                    continue;
                }
                $meta = '_olo_' . $zona . '_id';
                if ( null === $stato[ $zona ] || (int) $stato[ $zona ] <= 0 ) {
                    delete_post_meta( $pid, $meta );
                } elseif ( ! $db || ! $db->get_template( (int) $stato[ $zona ] ) ) {
                    $saltati[]      = $zona . '_pagina:' . $pid;
                    $titoli[ $pid ] = (string) ( $stato['title'] ?? '' );
                } else {
                    update_post_meta( $pid, $meta, $stato[ $zona ] );
                }
            }
            $modifica = [];
            if ( '' !== (string) ( $stato['title'] ?? '' ) && $post->post_title !== $stato['title'] ) {
                $modifica['post_title'] = $stato['title'];
            }
            if ( '' !== (string) ( $stato['status'] ?? '' ) && $post->post_status !== $stato['status'] ) {
                $modifica['post_status'] = $stato['status'];
            }
            if ( $modifica ) {
                $modifica['ID'] = $pid;
                wp_update_post( wp_slash( $modifica ) );
            }
        }

        // La cache delle pagine si svuota da sé solo su update_option di stili,
        // colori globali, set tipografici, header e footer: non su delete_option,
        // 404, cursori, home.
        self::svuota_cache_pagine();

        return [
            'ok'      => true,
            'prima'   => $prima,
            'saltati' => $saltati,
            'titoli'  => (object) $titoli,
            'font'    => (object) $font,
        ];
    }

    /** Toglie un'istantanea dall'elenco. */
    public function delete_snapshot( $id ) {
        $list = $this->snapshot_list();
        $dopo = array_values( array_filter( $list, function ( $v ) use ( $id ) {
            return (string) $v['id'] !== (string) $id;
        } ) );
        if ( count( $dopo ) === count( $list ) ) {
            return new WP_Error( 'olobuild_snapshot_not_found', __( 'Versione dello stile non trovata.', 'olobuild' ), [ 'status' => 404 ] );
        }
        update_option( self::SNAPSHOT_OPT, $dopo, false );
        return [ 'ok' => true ];
    }

    /**
     * Elenchi con un id (colori globali, set tipografici, font caricati) al
     * ripristino: le voci dell'istantanea come erano (coi loro valori e il loro
     * `hidden`), più in coda quelle nate dopo. Riscriverla identica toglieva dal
     * CSS i colori e i set creati dopo, e le tile che li usano li perdevano in
     * silenzio. Le voci nate dopo con un id in $escludi non restano.
     */
    private static function fondi_per_id( $snap, $attuali, $escludi = [] ) {
        $out = [];
        $ids = [];
        foreach ( is_array( $snap ) ? $snap : [] as $c ) {
            if ( ! is_array( $c ) ) {
                continue;
            }
            $out[] = $c;
            if ( ! empty( $c['id'] ) && is_scalar( $c['id'] ) ) {
                $ids[ (string) $c['id'] ] = true;
            }
        }
        foreach ( is_array( $attuali ) ? $attuali : [] as $c ) {
            if ( is_array( $c ) && ! empty( $c['id'] ) && is_scalar( $c['id'] )
                && ! isset( $ids[ (string) $c['id'] ] ) && ! isset( $escludi[ (string) $c['id'] ] ) ) {
                $out[] = $c;
            }
        }
        return $out;
    }

    /**
     * Una pagina che il ripristino può rimettere come pagina iniziale o degli
     * articoli: esiste e non è nel cestino, oppure è nel cestino ma è fra le
     * pagine dell'istantanea con un altro stato, che il ripristino le rende.
     *
     * @param int   $pid    Id della pagina.
     * @param array $pagine Le pagine dell'istantanea [ page_id => stato_pagina() ].
     */
    private static function pagina_rimettibile( $pid, $pagine ) {
        $post = $pid > 0 ? get_post( $pid ) : null;
        if ( ! $post ) {
            return false;
        }
        if ( 'trash' !== $post->post_status ) {
            return true;
        }
        $stato = ( isset( $pagine[ $pid ] ) && is_array( $pagine[ $pid ] ) ) ? (string) ( $pagine[ $pid ]['status'] ?? '' ) : '';
        return '' !== $stato && 'trash' !== $stato;
    }

    /**
     * I font caricati di un'istantanea, senza le varianti il cui file, nella
     * cartella del gestore font di questo sito, non c'è più:
     * Olobuild_Custom_Fonts::delete_font() cancella i file, e la voce rimessa
     * farebbe emettere un @font-face che dà 404 su ogni pagina. Un URL fuori da
     * quella cartella non si giudica. Un font rimasto senza varianti non torna:
     * resta quello attuale con lo stesso id, se c'è, altrimenti va in $saltati
     * come 'font:<id>', col nome in $nomi.
     *
     * @param array $fonts   olobuild_custom_fonts dell'istantanea.
     * @param array $attuali olobuild_custom_fonts di adesso.
     * @return array I font dell'istantanea da rimettere.
     */
    private static function font_con_file( $fonts, $attuali, &$saltati, &$nomi ) {
        if ( ! is_array( $fonts ) || ! class_exists( 'Olobuild_Custom_Fonts' ) ) {
            return $fonts;
        }
        $schema  = '#^https?:#i';
        $base    = preg_replace( $schema, '', Olobuild_Custom_Fonts::get_upload_url() ) . '/';
        $dir     = Olobuild_Custom_Fonts::get_upload_dir() . '/';
        $ci_sono = [];
        foreach ( is_array( $attuali ) ? $attuali : [] as $f ) {
            if ( is_array( $f ) && ! empty( $f['id'] ) && is_scalar( $f['id'] ) ) {
                $ci_sono[ (string) $f['id'] ] = true;
            }
        }
        $out = [];
        foreach ( $fonts as $f ) {
            if ( ! is_array( $f ) || empty( $f['variants'] ) || ! is_array( $f['variants'] ) ) {
                $out[] = $f;
                continue;
            }
            $varianti = [];
            foreach ( $f['variants'] as $v ) {
                $file = ( is_array( $v ) && isset( $v['file'] ) && is_string( $v['file'] ) ) ? $v['file'] : '';
                if ( '' !== $file && 0 === strpos( preg_replace( $schema, '', $file ), $base )
                    && ! file_exists( $dir . basename( $file ) ) ) {
                    continue;
                }
                $varianti[] = $v;
            }
            if ( count( $varianti ) === count( $f['variants'] ) ) {
                $out[] = $f;
                continue;
            }
            if ( $varianti ) {
                $f['variants'] = $varianti;
                $out[]         = $f;
                continue;
            }
            $id = ( isset( $f['id'] ) && is_scalar( $f['id'] ) ) ? (string) $f['id'] : '';
            if ( '' !== $id && ! isset( $ci_sono[ $id ] ) ) {
                $saltati[]   = 'font:' . $id;
                $nomi[ $id ] = ( isset( $f['name'] ) && is_scalar( $f['name'] ) && '' !== (string) $f['name'] ) ? (string) $f['name'] : $id;
            }
        }
        return $out;
    }

    private static function svuota_cache_pagine() {
        if ( class_exists( 'Olobuild_FullPage_Cache' ) ) {
            Olobuild_FullPage_Cache::purge_all();
        }
    }

    /**
     * Convert border-radius value (number or {tl,tr,br,bl} object) to CSS string.
     */
    private function css_border_radius( $val, $fallback = '4px' ) {
        if ( is_array( $val ) ) {
            return sprintf( '%dpx %dpx %dpx %dpx',
                intval( $val['tl'] ?? 0 ), intval( $val['tr'] ?? 0 ),
                intval( $val['br'] ?? 0 ), intval( $val['bl'] ?? 0 ) );
        }
        $s = strval( $val );
        if ( str_contains( $s, 'px' ) ) return $s;
        if ( $s !== '' && $s !== '0' ) return $s . 'px';
        return $fallback;
    }

    /**
     * Sanitize all style values.
     */
    public function sanitize_styles( $styles ) {
        $sanitized = [];

        // Colors
        if ( isset( $styles['colors'] ) && is_array( $styles['colors'] ) ) {
            $sanitized['colors'] = [];
            foreach ( $styles['colors'] as $key => $value ) {
                $clean = sanitize_hex_color( $value );
                if ( $clean ) {
                    $sanitized['colors'][ sanitize_key( $key ) ] = $clean;
                }
            }
        }

        // Typography
        if ( isset( $styles['typography'] ) && is_array( $styles['typography'] ) ) {
            $sanitized['typography'] = [];
            foreach ( $styles['typography'] as $key => $value ) {
                $sanitized['typography'][ sanitize_key( $key ) ] = sanitize_text_field( $value );
            }
        }

        // Layout
        if ( isset( $styles['layout'] ) && is_array( $styles['layout'] ) ) {
            $sanitized['layout'] = [];
            foreach ( $styles['layout'] as $key => $value ) {
                $skey = sanitize_key( $key );
                if ( is_array( $value ) ) {
                    // border_radius as {tl, tr, br, bl}
                    $sanitized['layout'][ $skey ] = array_map( 'absint', $value );
                } else {
                    $sanitized['layout'][ $skey ] = sanitize_text_field( $value );
                }
            }
        }

        // Buttons
        if ( isset( $styles['buttons'] ) && is_array( $styles['buttons'] ) ) {
            $sanitized['buttons'] = [];
            foreach ( $styles['buttons'] as $key => $value ) {
                $sanitized['buttons'][ sanitize_key( $key ) ] = sanitize_text_field( $value );
            }
        }

        // Forms
        if ( isset( $styles['forms'] ) && is_array( $styles['forms'] ) ) {
            $sanitized['forms'] = [];
            foreach ( $styles['forms'] as $key => $value ) {
                $sanitized['forms'][ sanitize_key( $key ) ] = sanitize_text_field( $value );
            }
        }

        // Links
        if ( isset( $styles['links'] ) && is_array( $styles['links'] ) ) {
            $sanitized['links'] = [];
            foreach ( $styles['links'] as $key => $value ) {
                $skey = sanitize_key( $key );
                $clean = sanitize_hex_color( $value );
                if ( $clean ) {
                    $sanitized['links'][ $skey ] = $clean;
                } else {
                    $sanitized['links'][ $skey ] = sanitize_text_field( $value );
                }
            }
        }

        // Dark Colors
        if ( isset( $styles['dark_colors'] ) && is_array( $styles['dark_colors'] ) ) {
            $sanitized['dark_colors'] = [];
            foreach ( $styles['dark_colors'] as $key => $value ) {
                $clean = sanitize_hex_color( $value );
                if ( $clean ) {
                    $sanitized['dark_colors'][ sanitize_key( $key ) ] = $clean;
                }
            }
        }

        // Google Fonts
        if ( isset( $styles['google_fonts'] ) && is_array( $styles['google_fonts'] ) ) {
            $sanitized['google_fonts'] = array_map( 'sanitize_text_field', $styles['google_fonts'] );
            $sanitized['google_fonts'] = array_values( array_unique( $sanitized['google_fonts'] ) );
        }

        // Spacing scale (xs..4xl) — pass-through with sanitize_text_field
        if ( isset( $styles['spacing'] ) && is_array( $styles['spacing'] ) ) {
            $sanitized['spacing'] = [];
            foreach ( $styles['spacing'] as $key => $value ) {
                $skey = preg_replace( '/[^a-z0-9_]/', '', strtolower( (string) $key ) );
                if ( $skey !== '' ) {
                    $sanitized['spacing'][ $skey ] = sanitize_text_field( $value );
                }
            }
        }

        // Border radius scale
        if ( isset( $styles['border_radius_scale'] ) && is_array( $styles['border_radius_scale'] ) ) {
            $sanitized['border_radius_scale'] = [];
            foreach ( $styles['border_radius_scale'] as $key => $value ) {
                $sanitized['border_radius_scale'][ sanitize_key( $key ) ] = sanitize_text_field( $value );
            }
        }

        // Shadows
        if ( isset( $styles['shadows'] ) && is_array( $styles['shadows'] ) ) {
            $sanitized['shadows'] = [];
            foreach ( $styles['shadows'] as $key => $value ) {
                $sanitized['shadows'][ sanitize_key( $key ) ] = sanitize_text_field( $value );
            }
        }

        // Section padding (chiavi mappano a spacing scale: xs..4xl)
        if ( isset( $styles['section_padding'] ) && is_array( $styles['section_padding'] ) ) {
            $allowed_tokens = [ 'xs', 'sm', 'md', 'lg', 'xl', '2xl', '3xl', '4xl' ];
            $sanitized['section_padding'] = [];
            foreach ( $styles['section_padding'] as $key => $value ) {
                $skey = sanitize_key( $key );
                $sval = sanitize_text_field( $value );
                if ( in_array( $sval, $allowed_tokens, true ) ) {
                    $sanitized['section_padding'][ $skey ] = $sval;
                }
            }
        }

        // Gutter responsive (numeric px)
        if ( isset( $styles['gutter'] ) && is_array( $styles['gutter'] ) ) {
            $sanitized['gutter'] = [];
            foreach ( $styles['gutter'] as $key => $value ) {
                $skey = sanitize_key( $key );
                $sanitized['gutter'][ $skey ] = max( 0, min( 120, absint( $value ) ) );
            }
        }

        // Fluid scaling
        if ( isset( $styles['fluid_scaling'] ) && is_array( $styles['fluid_scaling'] ) ) {
            $fs = $styles['fluid_scaling'];
            $sanitized['fluid_scaling'] = [
                'enabled' => ! empty( $fs['enabled'] ),
                'tablet'  => max( 0.3, min( 1.0, (float) ( $fs['tablet'] ?? 0.85 ) ) ),
                'mobile'  => max( 0.3, min( 1.0, (float) ( $fs['mobile'] ?? 0.65 ) ) ),
            ];
        }

        // Grain / noise overlay
        if ( isset( $styles['grain'] ) && is_array( $styles['grain'] ) ) {
            $gr = $styles['grain'];
            $sanitized['grain'] = [
                'enabled' => ! empty( $gr['enabled'] ),
                'opacity' => max( 0, min( 30, absint( $gr['opacity'] ?? 6 ) ) ),
                'scale'   => max( 60, min( 400, absint( $gr['scale'] ?? 180 ) ) ),
            ];
        }

        // Neutrals (scala grigi + modalità/tinta) — pagina admin "Palette colori"
        if ( isset( $styles['neutrals'] ) && is_array( $styles['neutrals'] ) ) {
            $nz    = $styles['neutrals'];
            $clean = [];
            if ( isset( $nz['mode'] ) ) {
                $clean['mode'] = in_array( $nz['mode'], [ 'auto', 'manual' ], true ) ? $nz['mode'] : 'auto';
            }
            if ( isset( $nz['tint'] ) ) {
                $tints         = [ 'slate', 'gray', 'zinc', 'neutral', 'stone' ];
                $clean['tint'] = in_array( $nz['tint'], $tints, true ) ? $nz['tint'] : 'zinc';
            }
            if ( isset( $nz['scale'] ) && is_array( $nz['scale'] ) ) {
                $scale = [];
                foreach ( $nz['scale'] as $hex ) {
                    $h = sanitize_hex_color( $hex );
                    if ( $h ) {
                        $scale[] = $h;
                    }
                }
                if ( $scale ) {
                    $clean['scale'] = $scale;
                }
            }
            if ( $clean ) {
                $sanitized['neutrals'] = $clean;
            }
        }

        // Dark mode meta (enabled + strategy) — pagina admin "Palette colori"
        if ( isset( $styles['dark_mode'] ) && is_array( $styles['dark_mode'] ) ) {
            $dm         = $styles['dark_mode'];
            $strategies = [ 'auto', 'manual', 'luminance' ];
            $strategy   = sanitize_text_field( $dm['strategy'] ?? 'auto' );
            $sanitized['dark_mode'] = [
                'enabled'  => ! empty( $dm['enabled'] ),
                'strategy' => in_array( $strategy, $strategies, true ) ? $strategy : 'auto',
            ];
        }

        return $sanitized;
    }

    /**
     * Get global colors from wp_options.
     */
    public function get_global_colors() {
        $colors = get_option( 'olobuild_global_colors', [] );
        return is_array( $colors ) ? $colors : [];
    }

    /**
     * Alias che generate_css() emette SEMPRE nella regola .olo-template, in
     * quest'ordine: i nomi usati dalle tile, mappati sui token del tema (vedi
     * _olo-tokens.scss). Fonte unica anche per id_colori_dello_stile().
     */
    const ALIAS_COLORI = [
        'on-primary'  => 'var(--olo-color-primary-contrast, #ffffff)',
        'text-soft'   => 'var(--olo-color-text-muted, #6b7280)',
        'text-faint'  => 'var(--olo-color-text-muted, #94a3b8)',
        'surface'     => 'var(--olo-color-background, #ffffff)',
        'surface-alt' => 'var(--olo-color-muted, #f6f7f9)',
        'error'       => 'var(--olo-color-danger, #b42318)',
        'info'        => '#2563eb',
    ];

    /**
     * I ruoli GLOBALI delle tile che lo stile non emette: accent, dark, light
     * (GLOBAL in oloTileDefaults.js, TOKEN_MAPPING) e accent-2 / accent_2 (che
     * sync_global_palette tiene allineati). I default delle tile li usano con
     * la riserva, var(--olo-color-accent, #f4a23b): un colore globale con uno di
     * questi id ricolora tutte le tile che non hanno scelto un colore.
     */
    const RUOLI_GLOBALI_TILE = [ 'accent', 'accent-2', 'accent_2', 'dark', 'light' ];

    /**
     * I ruoli che i default delle tile leggono DAVVERO senza salvarli:
     * var(--olo-color-accent, …), var(--olo-color-dark, …) e
     * var(--olo-color-light, …) in decine di renderer di includes/tiles e nei
     * gemelli in src/ (tile Vue, oloTileDefaults.js, preset). Un colore globale
     * con uno di questi id colora ogni tile che non ne ha scelto uno, anche se
     * nessun template lo nomina (uso_colore(): 'ruolo_tile'). accent-2 e
     * accent_2 non li legge nessun default: --olo-color-accent-2 e
     * --olo-color-accent_2 non compaiono nel codice delle tile (25 set 2026).
     */
    const RUOLI_LETTI_DAI_DEFAULT = [ 'accent', 'dark', 'light' ];

    /**
     * Gli stessi tre ruoli quando il sito NON li ha fra i colori globali (nessuno degli 8 siti li
     * aveva): li ricava dalla palette, così le ~100 tile che li leggono seguono il cliente invece
     * delle riserve fisse (blu notte, quasi-bianco, ambra). Scelta dell'utente del 5 ott 2026.
     * Accento = primario (come sync_global_palette() per un accento esistente); Scuro e Chiaro
     * sono un quasi-nero e un quasi-bianco tinti dal primario, scuro e chiaro in ogni palette,
     * anche in quelle a fondo scuro. Un colore globale con lo stesso id vince sempre.
     * Gemello: RUOLI_DERIVATI in src/stores/styles.js.
     */
    const RUOLI_DERIVATI = [
        'accent' => 'var(--olo-color-primary)',
        'dark'   => 'color-mix(in srgb, var(--olo-color-primary) 12%, #14161c)',
        'light'  => 'color-mix(in srgb, var(--olo-color-primary) 5%, #fdfcfa)',
    ];

    /**
     * I RUOLI_DERIVATI da scrivere: quelli che né i colori dello stile né i colori globali
     * definiscono.
     *
     * @param array $colors        blocco colori dello stile.
     * @param array $global_colors olobuild_global_colors.
     * @return array [ ruolo => valore ]
     */
    public static function ruoli_derivati_mancanti( $colors, $global_colors ) {
        $presenti = [];
        foreach ( (array) $global_colors as $gc ) {
            if ( is_array( $gc ) && ! empty( $gc['id'] ) && ! empty( $gc['value'] ) ) {
                $presenti[ sanitize_html_class( $gc['id'] ) ] = true;
            }
        }
        foreach ( array_keys( (array) $colors ) as $k ) {
            $presenti[ str_replace( '_', '-', (string) $k ) ] = true;
        }
        return array_diff_key( self::RUOLI_DERIVATI, $presenti );
    }

    /**
     * Gli id dei colori globali la cui variabile il CSS emette GIÀ dallo stile:
     * le chiavi dei colori dello stile passato più i predefiniti, che
     * get_styles() aggiunge sempre, col nome che prendono nella variabile
     * (text_muted esce --olo-color-text-muted), più gli alias fissi
     * (ALIAS_COLORI: error, info, surface…). Nella regola .olo-template
     * generate_css() (e il gemello in src/stores/styles.js) scrive i colori
     * dello stile, poi i colori globali, poi gli alias. Un colore globale con
     * l'id di una chiave dello stile esce DOPO e la copre. Uno con l'id di un
     * alias esce PRIMA e non agisce (vince l'alias): tenerlo non serve, e fra
     * i colori extra mostrerebbe un valore diverso da quello reso. La forma con
     * «_» (text_muted) non è né l'una né l'altra: un colore globale esce col
     * suo id com'è (--olo-color-text_muted), una variabile diversa che non
     * copre niente.
     *
     * @param array $colors blocco olobuild_styles['colors'].
     * @return array [ id => true ]
     */
    public static function id_colori_dello_stile( $colors ) {
        $defaults = self::instance()->get_defaults();
        $chiavi   = array_merge( array_keys( $defaults['colors'] ), is_array( $colors ) ? array_keys( $colors ) : [] );
        $ids      = [];
        foreach ( $chiavi as $k ) {
            $ids[ str_replace( '_', '-', (string) $k ) ] = true;
        }
        foreach ( array_keys( self::ALIAS_COLORI ) as $alias ) {
            $ids[ $alias ] = true;
        }
        return $ids;
    }

    /**
     * Import (sito): i colori globali del pacchetto più quelli del sito che il
     * pacchetto non porta, NASCOSTI. Le tile e i template già qui che usano
     * var(--olo-color-<id>) non perdono il colore: tolti dalla lista, sparivano
     * dal CSS in silenzio. Non restano:
     * - quelli la cui variabile il CSS emette già dallo stile
     *   (id_colori_dello_stile()): con l'id di una chiave dei colori dello
     *   stile (primary, text-muted…) coprirebbero i colori appena importati;
     *   con quello di un alias fisso (error, info, surface…) non agirebbero
     *   (l'alias esce dopo e vince) e fra gli extra mostrerebbero un valore
     *   diverso da quello reso;
     * - i ruoli globali delle tile (RUOLI_GLOBALI_TILE: accent, accent-2,
     *   accent_2, dark, light) che il contenuto PROPRIO del sito non nomina:
     *   uso_colore() senza i template e i global widget appena importati
     *   (template, widget globali, test A/B, sezioni salvate, stili globali;
     *   gli stili solo se l'import non li ha sostituiti, $stili_importati).
     *   Un colore nascosto resta nel CSS e i default delle tile lo leggono
     *   (var(--olo-color-dark, #16263d), con riserve diverse da tile a tile): i
     *   template importati non renderebbero più come sul sito d'origine, dove
     *   quel colore non c'era e valeva la riserva. Il compromesso: una tile del
     *   sito che il ruolo lo prendeva solo dal suo default, senza nominarlo,
     *   torna alla sua riserva, come faceva l'import prima che tenesse i
     *   colori del sito e come fa il ripristino di una versione dello stile.
     * Un ruolo che il sito nomina resta, nascosto: accent, accent-2 e accent_2
     * col valore che sync_global_palette() darebbe loro coi colori importati
     * (il primary e il secondary del pacchetto: col valore di prima le tile che
     * li usano mostrerebbero la palette vecchia), dark e light col loro. Gli
     * altri colori del sito restano, nascosti, come sono. I colori del
     * pacchetto restano come arrivano. Da chiamare DOPO aver scritto gli stili e
     * i template importati e PRIMA di scrivere la lista.
     *
     * Gli stili globali si leggono come sono in quel momento. Se il pacchetto
     * ne porta, sono già i suoi e quelli del sito non ci sono più (restano
     * solo nell'istantanea dell'import): con $stili_importati non contano,
     * come i template appena importati. Se non ne porta, restano quelli del
     * sito e contano.
     *
     * @param array $importati          Colori globali del pacchetto (già ripuliti).
     * @param int[] $template_importati Id dei template appena creati dall'import.
     * @param int[] $widget_importati   Id dei global widget appena creati dall'import.
     * @param bool  $stili_importati    true = l'import ha appena sostituito gli stili globali.
     * @return array La lista da salvare.
     */
    public function con_colori_del_sito( $importati, $template_importati = [], $widget_importati = [], $stili_importati = false ) {
        $out           = array_values( is_array( $importati ) ? $importati : [] );
        $nel_pacchetto = [];
        foreach ( $out as $c ) {
            if ( is_array( $c ) && ! empty( $c['id'] ) && is_scalar( $c['id'] ) ) {
                $nel_pacchetto[ (string) $c['id'] ] = true;
            }
        }
        $colori  = $this->get_styles()['colors'];
        $ruoli   = self::id_colori_dello_stile( $colori );
        $accenti = self::accenti_della_palette( $colori );
        $attuali = get_option( 'olobuild_global_colors', [] );
        foreach ( is_array( $attuali ) ? $attuali : [] as $c ) {
            if ( ! is_array( $c ) || empty( $c['id'] ) || ! is_scalar( $c['id'] ) ) {
                continue;
            }
            $id = (string) $c['id'];
            if ( isset( $nel_pacchetto[ $id ] ) || isset( $ruoli[ $id ] ) ) {
                continue;
            }
            if ( in_array( $id, self::RUOLI_GLOBALI_TILE, true )
                && ! self::nominato_dal_sito( self::uso_colore( $id, $template_importati, $widget_importati, false ), ! $stili_importati ) ) {
                continue;
            }
            if ( isset( $accenti[ $id ] ) ) {
                $c['value'] = $accenti[ $id ];
            }
            $c['hidden'] = true;
            $out[]       = $c;
        }
        return $out;
    }

    /**
     * Un colore che il contenuto del sito nomina esplicitamente: template,
     * widget globali, test A/B, sezioni salvate o (con $con_stili) stili
     * globali. Non conta 'ruolo_tile' (i default delle tile) né le revisioni.
     *
     * @param array $uso       Risultato di uso_colore().
     * @param bool  $con_stili false = gli stili globali non contano (sono quelli appena importati).
     */
    private static function nominato_dal_sito( $uso, $con_stili = true ) {
        return ( (int) $uso['template_count'] + (int) $uso['widgets'] + (int) $uso['ab_tests'] + (int) $uso['sezioni'] ) > 0
            || ( $con_stili && ! empty( $uso['styles'] ) );
    }

    /**
     * Dove è usato il colore globale $id. Fonte unica della rotta
     * GET global-colors/<id>/usage («Elimina» in Configurazione › Palette) e
     * dell'import del sito (con_colori_del_sito()).
     *
     * Una tile salva il colore scelto come var(--olo-color-<id>): eliminare il
     * colore toglie la variabile dal CSS e quelle tile perdono il colore, in
     * silenzio, su pagine che nessuno sta guardando. Il token si cerca nei
     * template (contenuto e impostazioni di pagina; header, footer e popup sono
     * template anche loro), nei global widget, nei test A/B, nelle sezioni
     * salvate della libreria (option olobuild_user_templates) e negli stili
     * globali. Le revisioni si contano a parte (non sono in 'total'):
     * ripristinarne una riporta il token. 'ruolo_tile' dice che i default delle
     * tile leggono quel colore anche dove nessuno lo nomina
     * (RUOLI_LETTI_DAI_DEFAULT): conta 1 in 'total'.
     *
     * Il LIKE trova anche gli id che iniziano allo stesso modo (accent in
     * accent-2, c1 in c10): il confine dell'id si controlla dopo, sul testo.
     *
     * @param string $id                   Id del colore globale.
     * @param int[]  $escludi_template_ids Template da non contare (l'import: quelli appena creati).
     * @param int[]  $escludi_widget_ids   Global widget da non contare (l'import: quelli appena creati).
     * @param bool   $con_revisioni        false = le revisioni non si cercano ('revisions' resta 0).
     * @return array [ 'id', 'total', 'template_count', 'templates' (al massimo 10: id, title, type),
     *                 'widgets', 'ab_tests', 'sezioni', 'styles', 'revisions', 'ruolo_tile' ]
     */
    public static function uso_colore( $id, $escludi_template_ids = [], $escludi_widget_ids = [], $con_revisioni = true ) {
        global $wpdb;

        $id  = sanitize_key( (string) $id );
        $uso = [
            'id'             => $id,
            'total'          => 0,
            'template_count' => 0,
            'templates'      => [],
            'widgets'        => 0,
            'ab_tests'       => 0,
            'sezioni'        => 0,
            'styles'         => false,
            'revisions'      => 0,
            'ruolo_tile'     => in_array( $id, self::RUOLI_LETTI_DAI_DEFAULT, true ),
        ];
        if ( '' === $id ) {
            return $uso;
        }

        $token     = '--olo-color-' . $id;
        $like      = '%' . $wpdb->esc_like( $token ) . '%';
        $confine   = '/' . preg_quote( $token, '/' ) . '(?![A-Za-z0-9_-])/';
        $escludi_t = array_fill_keys( array_map( 'intval', (array) $escludi_template_ids ), true );
        $escludi_w = array_fill_keys( array_map( 'intval', (array) $escludi_widget_ids ), true );

        $t_templates = Olobuild_Database::table( 'templates' );
        $t_widgets   = Olobuild_Database::table( 'global_widgets' );
        $t_revisions = Olobuild_Database::table( 'revisions' );
        $t_ab        = Olobuild_Database::table( 'ab_tests' );

        // Tabelle custom del plugin ({prefix}olobuild_*); nessun equivalente WP_Query; conteggio al
        // momento (non cacheabile). Solo nomi tabella da $wpdb->prefix interpolati, il token passa da prepare.
        // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
        $righe = $wpdb->get_results(
            $wpdb->prepare( "SELECT id, title, type, content, settings FROM {$t_templates} WHERE content LIKE %s OR settings LIKE %s ORDER BY title", $like, $like ),
            ARRAY_A
        );
        $templates = [];
        foreach ( (array) $righe as $r ) {
            if ( isset( $escludi_t[ (int) $r['id'] ] ) ) {
                continue;
            }
            if ( preg_match( $confine, (string) $r['content'] ) || preg_match( $confine, (string) $r['settings'] ) ) {
                $templates[] = [
                    'id'    => (int) $r['id'],
                    'title' => (string) $r['title'],
                    'type'  => (string) $r['type'],
                ];
            }
        }

        $righe = $wpdb->get_results( $wpdb->prepare( "SELECT id, tile_data FROM {$t_widgets} WHERE tile_data LIKE %s", $like ), ARRAY_A );
        foreach ( (array) $righe as $r ) {
            if ( ! isset( $escludi_w[ (int) $r['id'] ] ) && preg_match( $confine, (string) $r['tile_data'] ) ) {
                $uso['widgets']++;
            }
        }

        // La tabella dei test A/B nasce al primo test: se non c'è, non si interroga.
        if ( strtolower( (string) $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $t_ab ) ) ) ) === strtolower( $t_ab ) ) {
            $righe = $wpdb->get_col( $wpdb->prepare( "SELECT variants FROM {$t_ab} WHERE variants LIKE %s", $like ) );
            foreach ( (array) $righe as $dati ) {
                if ( preg_match( $confine, (string) $dati ) ) {
                    $uso['ab_tests']++;
                }
            }
        }

        // Revisioni: solo un conteggio (possono essere molte e grandi). Il token
        // seguito da ')' ',' o spazio è il confine di un var() scritto da una tile.
        if ( $con_revisioni ) {
            $uso['revisions'] = (int) $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT COUNT(*) FROM {$t_revisions} WHERE content LIKE %s OR content LIKE %s OR content LIKE %s",
                    '%' . $wpdb->esc_like( $token . ')' ) . '%',
                    '%' . $wpdb->esc_like( $token . ',' ) . '%',
                    '%' . $wpdb->esc_like( $token . ' ' ) . '%'
                )
            );
        }
        // phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter

        // Sezioni salvate nella libreria («Salva come template» di una sezione, fra i
        // «Personali»: Olobuild_Template_Library::save_user_template): stanno in
        // un'option, col contenuto com'è arrivato dal builder (un array di sezioni).
        $sezioni = get_option( 'olobuild_user_templates', [] );
        foreach ( is_array( $sezioni ) ? $sezioni : [] as $sezione ) {
            $contenuto = is_array( $sezione ) ? ( $sezione['content'] ?? null ) : null;
            if ( null !== $contenuto && preg_match( $confine, is_string( $contenuto ) ? $contenuto : (string) wp_json_encode( $contenuto ) ) ) {
                $uso['sezioni']++;
            }
        }

        $uso['styles']         = (bool) preg_match( $confine, (string) wp_json_encode( get_option( 'olobuild_styles', [] ) ) );
        $uso['template_count'] = count( $templates );
        $uso['templates']      = array_slice( $templates, 0, 10 );
        $uso['total']          = $uso['template_count'] + $uso['widgets'] + $uso['ab_tests'] + $uso['sezioni']
            + ( $uso['styles'] ? 1 : 0 ) + ( $uso['ruolo_tile'] ? 1 : 0 );
        return $uso;
    }

    /**
     * Get global typography sets from wp_options.
     */
    public function get_global_typography() {
        $sets = get_option( 'olobuild_global_typography', [] );
        return is_array( $sets ) ? $sets : [];
    }

    /**
     * Generate CSS with custom properties and UIkit overrides.
     */
    public function generate_css() {
        // Memoizzazione per-request: nello stesso page-load il CSS globale viene richiesto
        // piu' volte (renderer + footer + search-results integration). Il ricalcolo e'
        // costoso (font import, risoluzione colori, override UIkit) -> lo si fa una volta sola.
        static $memo = null;
        if ( $memo !== null ) {
            return $memo;
        }

        $s = $this->get_styles();
        $c = $s['colors'];
        $t = $s['typography'];
        $l = $s['layout'];
        $b = $s['buttons'];
        $f = $s['forms'];
        $lk = $s['links'];

        $css = '';

        // Google Fonts import. Tre elenchi, ognuno passato da famiglie_google():
        // quello scelto a mano (google_fonts), le famiglie dei set tipografici e
        // quelle della tipografia base (testo/titoli/mono, che il pannello cfg →
        // Typography salva solo nel blocco typography).
        $existing_fonts = self::famiglie_google( $s['google_fonts'] ?? [] );
        $fonts_import = $this->generate_google_fonts_import( $existing_fonts );
        if ( $fonts_import ) {
            $css .= $fonts_import . "\n";
        }

        // Global typography Google Fonts import
        $global_typo = $this->get_global_typography();
        $global_font_families = self::famiglie_google( array_column( $global_typo, 'family' ) );
        // Filter out families already in main google_fonts
        $extra_families = array_diff( $global_font_families, $existing_fonts );
        if ( ! empty( $extra_families ) ) {
            $extra_import = $this->generate_google_fonts_import( array_values( $extra_families ) );
            if ( $extra_import ) {
                $css .= $extra_import . "\n";
            }
        }

        $typo_families = self::famiglie_google( [ $t['font_family'] ?? '', $t['font_family_heading'] ?? '', $t['font_family_mono'] ?? '' ] );
        $typo_extra = array_diff( $typo_families, $existing_fonts, $global_font_families );
        if ( ! empty( $typo_extra ) ) {
            $typo_import = $this->generate_google_fonts_import( array_values( $typo_extra ) );
            if ( $typo_import ) {
                $css .= $typo_import . "\n";
            }
        }

        // Custom properties
        $css .= ".olo-template {\n";
        // Colors
        foreach ( $c as $key => $value ) {
            $prop = str_replace( '_', '-', $key );
            $css .= "  --olo-color-{$prop}: {$value};\n";
        }
        // Global custom colors (user-defined swatches)
        $global_colors = $this->get_global_colors();
        // Scuro, Chiaro e Accento dalla palette quando il sito non li ha (RUOLI_DERIVATI)
        foreach ( self::ruoli_derivati_mancanti( $c, $global_colors ) as $ruolo => $valore ) {
            $css .= "  --olo-color-{$ruolo}: {$valore};\n";
        }
        foreach ( $global_colors as $gc ) {
            if ( ! empty( $gc['id'] ) && ! empty( $gc['value'] ) ) {
                $css .= "  --olo-color-" . sanitize_html_class( $gc['id'] ) . ": " . esc_attr( $gc['value'] ) . ";\n";
            }
        }
        // Alias di compatibilità: i nomi-pacchetto usati dalle tile mappano sui
        // token del tema (così seguono la palette del cliente). Vedi _olo-tokens.scss.
        foreach ( self::ALIAS_COLORI as $alias => $valore ) {
            $css .= "  --olo-color-{$alias}: {$valore};\n";
        }
        // Typography
        if ( ! empty( $t['font_family'] ) ) {
            $css .= "  --olo-font-family: {$t['font_family']};\n";
        }
        if ( ! empty( $t['font_family_heading'] ) ) {
            $css .= "  --olo-font-family-heading: {$t['font_family_heading']};\n";
        }
        // Ruolo mono: referenziato dalle tile (var(--olo-font-family-mono, fallback))
        // — emesso solo se personalizzato, altrimenti vale il fallback per-tile.
        if ( ! empty( $t['font_family_mono'] ) ) {
            $css .= "  --olo-font-family-mono: {$t['font_family_mono']};\n";
        }
        $css .= "  --olo-font-size-base: {$t['font_size_base']};\n";
        for ( $i = 1; $i <= 6; $i++ ) {
            $css .= "  --olo-font-size-h{$i}: {$t['font_size_h' . $i]};\n";
        }
        $css .= "  --olo-line-height: {$t['line_height']};\n";
        $css .= "  --olo-font-weight-heading: {$t['font_weight_heading']};\n";
        // Typography enhancements
        $css .= "  --olo-letter-spacing: {$t['letter_spacing']};\n";
        $css .= "  --olo-font-weight-body: {$t['font_weight_body']};\n";
        $css .= "  --olo-heading-line-height: {$t['heading_line_height']};\n";
        $css .= "  --olo-heading-letter-spacing: {$t['heading_letter_spacing']};\n";
        $css .= "  --olo-heading-text-transform: {$t['heading_text_transform']};\n";
        // Responsive typography vars (for media queries)
        if ( ! empty( $t['font_size_h1_tablet'] ) ) {
            $css .= "  --olo-font-size-h1-tablet: {$t['font_size_h1_tablet']};\n";
        }
        if ( ! empty( $t['font_size_h2_tablet'] ) ) {
            $css .= "  --olo-font-size-h2-tablet: {$t['font_size_h2_tablet']};\n";
        }
        if ( ! empty( $t['font_size_h3_tablet'] ) ) {
            $css .= "  --olo-font-size-h3-tablet: {$t['font_size_h3_tablet']};\n";
        }
        if ( ! empty( $t['font_size_h1_mobile'] ) ) {
            $css .= "  --olo-font-size-h1-mobile: {$t['font_size_h1_mobile']};\n";
        }
        if ( ! empty( $t['font_size_h2_mobile'] ) ) {
            $css .= "  --olo-font-size-h2-mobile: {$t['font_size_h2_mobile']};\n";
        }
        if ( ! empty( $t['font_size_h3_mobile'] ) ) {
            $css .= "  --olo-font-size-h3-mobile: {$t['font_size_h3_mobile']};\n";
        }
        // Layout
        $css .= "  --olo-border-radius: " . $this->css_border_radius( $l['border_radius'], '4px' ) . ";\n";
        $css .= "  --olo-border-radius-large: " . $this->css_border_radius( $l['border_radius_large'], '8px' ) . ";\n";
        $css .= "  --olo-container-max-width: {$l['container_max_width']};\n";
        $css .= "  --olo-container-narrow: " . ( $l['container_narrow'] ?? '720px' ) . ";\n";
        $css .= "  --olo-container-wide: " . ( $l['container_wide'] ?? '1440px' ) . ";\n";

        // Spacing scale (xs..4xl)
        $sp = $s['spacing'] ?? [];
        $sp_defaults = [ 'xs' => '4px', 'sm' => '8px', 'md' => '16px', 'lg' => '24px', 'xl' => '32px', '2xl' => '48px', '3xl' => '64px', '4xl' => '96px' ];
        foreach ( $sp_defaults as $sk => $sv ) {
            $val = ! empty( $sp[ $sk ] ) ? $sp[ $sk ] : $sv;
            $css .= "  --olo-space-{$sk}: {$val};\n";
        }

        // Section padding (alias verso i token spacing)
        $secp = $s['section_padding'] ?? [];
        $secp_defaults = [ 'compact' => 'lg', 'default' => 'xl', 'spacious' => '2xl', 'between' => 'md' ];
        foreach ( $secp_defaults as $sk => $sv ) {
            $token = ! empty( $secp[ $sk ] ) ? $secp[ $sk ] : $sv;
            $css .= "  --olo-section-pad-y-{$sk}: var(--olo-space-{$token});\n";
        }

        // Gutter (gap colonne + padding orizzontale container)
        $g = $s['gutter'] ?? [];
        $css .= "  --olo-gutter: " . absint( $g['desktop'] ?? 32 ) . "px;\n";
        $css .= "  --olo-gutter-side: " . absint( $g['side_desktop'] ?? 32 ) . "px;\n";
        // Shadows
        $css .= "  --olo-shadow-small: 0 1px 2px 0 rgba(0,0,0,0.05);\n";
        $css .= "  --olo-shadow-medium: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -2px rgba(0,0,0,0.1);\n";
        $css .= "  --olo-shadow-large: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -4px rgba(0,0,0,0.1);\n";

        // Button custom properties
        $css .= "  /* Button tokens */\n";
        $css .= "  --olo-btn-font-size: {$b['font_size']};\n";
        $css .= "  --olo-btn-font-weight: {$b['font_weight']};\n";
        $css .= "  --olo-btn-padding: {$b['padding_y']} {$b['padding_x']};\n";
        $css .= "  --olo-btn-border-radius: " . $this->css_border_radius( $b['border_radius'], '4px' ) . ";\n";
        $css .= "  --olo-btn-text-transform: {$b['text_transform']};\n";
        $css .= "  --olo-btn-letter-spacing: {$b['letter_spacing']};\n";
        $css .= "  --olo-btn-hover-brightness: {$b['hover_brightness']}%;\n";

        // Form custom properties
        $css .= "  /* Form tokens */\n";
        $css .= "  --olo-form-field-bg: {$f['field_bg']};\n";
        $css .= "  --olo-form-field-border: {$f['field_border_width']}px solid {$f['field_border_color']};\n";
        $css .= "  --olo-form-field-radius: " . $this->css_border_radius( $f['field_border_radius'], '4px' ) . ";\n";
        $css .= "  --olo-form-field-padding: {$f['field_padding']};\n";
        $css .= "  --olo-form-field-font-size: {$f['field_font_size']};\n";
        $css .= "  --olo-form-focus-border: {$f['focus_border_color']};\n";
        $css .= "  --olo-form-focus-shadow: {$f['focus_shadow']};\n";
        $css .= "  --olo-form-label-font-size: {$f['label_font_size']};\n";
        $css .= "  --olo-form-label-font-weight: {$f['label_font_weight']};\n";
        $css .= "  --olo-form-label-margin-bottom: {$f['label_margin_bottom']};\n";

        // Link custom properties
        if ( ! empty( $lk['color'] ) ) {
            $css .= "  --olo-color-link-custom: {$lk['color']};\n";
        }
        if ( ! empty( $lk['hover_color'] ) ) {
            $css .= "  --olo-color-link-hover: {$lk['hover_color']};\n";
        }
        $css .= "  --olo-link-decoration: {$lk['decoration']};\n";
        $css .= "  --olo-link-hover-decoration: {$lk['hover_decoration']};\n";

        // Global Colors
        $global_colors = $this->get_global_colors();
        if ( ! empty( $global_colors ) ) {
            $css .= "  /* Global Color Palette */\n";
            foreach ( $global_colors as $gc ) {
                $id    = sanitize_key( $gc['id'] ?? '' );
                $value = sanitize_text_field( $gc['value'] ?? '' );
                if ( $id && $value ) {
                    $css .= "  --olo-color-{$id}: {$value};\n";
                }
            }
        }

        // Global Typography
        if ( ! empty( $global_typo ) ) {
            $css .= "  /* Global Typography Sets */\n";
            foreach ( $global_typo as $gt ) {
                $id = sanitize_key( $gt['id'] ?? '' );
                if ( ! $id ) continue;
                $family = sanitize_text_field( $gt['family'] ?? '' );
                $weight = sanitize_text_field( $gt['weight'] ?? '400' );
                $transform = sanitize_text_field( $gt['transform'] ?? 'none' );
                $line_height = sanitize_text_field( $gt['line_height'] ?? '1.5' );
                $letter_spacing = sanitize_text_field( $gt['letter_spacing'] ?? '0' );

                if ( $family ) {
                    // Il valore puo essere un var() di ruolo o uno stack web-safe:
                    // virgolettarlo sempre ne faceva il nome di un font inesistente.
                    $family_css = $family;
                    if ( strpos( $family, 'var(' ) !== 0 && strpos( $family, ',' ) === false
                        && strpos( $family, "'" ) === false && strpos( $family, '"' ) === false ) {
                        $family_css = "'{$family}', sans-serif";
                    }
                    $css .= "  --olo-font-{$id}-family: {$family_css};\n";
                }
                $css .= "  --olo-font-{$id}-weight: {$weight};\n";
                $css .= "  --olo-font-{$id}-transform: {$transform};\n";
                $css .= "  --olo-font-{$id}-line-height: {$line_height};\n";
                // Gli helper restituiscono gia l'unita: "0.5px" + px = dichiarazione scartata.
                $ls_css = preg_match( '/^-?[0-9.]+$/', $letter_spacing ) ? $letter_spacing . 'px' : $letter_spacing;
                $css .= "  --olo-font-{$id}-letter-spacing: {$ls_css};\n";
            }
        }

        $css .= "}\n\n";

        // Regole del preset tipografico: la classe `olo-typo-<id>` sul wrapper di
        // una tile porta lo stile ai suoi discendenti. Prima le cinque proprieta'
        // stavano inline sul wrapper e scendevano per eredita', ma l'eredita' perde
        // contro QUALSIASI regola che tocchi il figlio — e un titolo e' sempre preso
        // da una regola del tema o di UIkit: si sceglieva uno stile e non cambiava
        // niente. La classe e' ripetuta per alzare la specificita' sopra le regole
        // del tema; i valori inline che la tile scrive per le proprieta' scelte
        // dall'utente restano sopra a tutto. `strong`, `b` e `th` non sono
        // nell'elenco: il loro grassetto e' semantico, non tipografia della tile.
        if ( ! empty( $global_typo ) ) {
            $css .= "/* Preset tipografici — applicazione */\n";
            foreach ( $global_typo as $gt ) {
                $id = sanitize_key( $gt['id'] ?? '' );
                if ( ! $id ) continue;
                $sel = ".olo-typo-{$id}.olo-typo-{$id}";
                $css .= "{$sel},\n{$sel} :is(h1,h2,h3,h4,h5,h6,p,li,a,span,em,i,blockquote,figcaption,label,dt,dd,td,button,input,textarea,select) {\n";
                $css .= "  font-family: var(--olo-font-{$id}-family, inherit);\n";
                $css .= "  font-weight: var(--olo-font-{$id}-weight, inherit);\n";
                $css .= "  text-transform: var(--olo-font-{$id}-transform, none);\n";
                $css .= "  line-height: var(--olo-font-{$id}-line-height, inherit);\n";
                $css .= "  letter-spacing: var(--olo-font-{$id}-letter-spacing, normal);\n";
                $css .= "}\n\n";
                // Le tile scrivono la tipografia del proprio titolo INLINE, e
                // l'inline batte qualsiasi regola. Ma la scrivono attraverso le
                // variabili di ruolo: ridefinendole qui sul wrapper, la
                // dichiarazione inline della tile si risolve nel font del preset
                // senza toccare duecento renderer. Il ruolo mono resta fuori: il
                // codice deve restare monospaziato.
                $css .= "{$sel} {\n";
                $css .= "  --olo-font-family: var(--olo-font-{$id}-family, var(--olo-font-family));\n";
                $css .= "  --olo-font-family-heading: var(--olo-font-{$id}-family, var(--olo-font-family-heading));\n";
                $css .= "}\n\n";
            }
        }

        // UIkit overrides - Sections
        // background-color WITHOUT !important so inline styles (custom bg) can override
        // color WITH !important to ensure text contrast with section style
        $css .= "/* UIkit Section overrides */\n";
        // Section default: TRASPARENTE (eredita bg da .olo-template / page_bg).
        // UIkit base imposta uk-section-default { background: #fff } che copre il page_bg.
        // Senza questa regola, ogni section default sovrascrive il bg pagina con bianco.
        $css .= ".olo-template .uk-section-default { background-color: transparent; }\n";
        $css .= ".olo-template .uk-section-primary { background-color: var(--olo-color-primary); color: var(--olo-color-primary-contrast) !important; }\n";
        $css .= ".olo-template .uk-section-primary :where(a) { color: var(--olo-color-primary-contrast) !important; }\n";
        $css .= ".olo-template .uk-section-secondary { background-color: var(--olo-color-secondary); color: var(--olo-color-secondary-contrast) !important; }\n";
        $css .= ".olo-template .uk-section-secondary :where(a) { color: var(--olo-color-secondary-contrast) !important; }\n";
        $css .= ".olo-template .uk-section-muted { background-color: var(--olo-color-muted); color: var(--olo-color-muted-contrast) !important; }\n";

        // Typography
        $css .= "\n/* Typography overrides */\n";
        $css .= ".olo-template { background-color: var(--olo-color-background); font-size: var(--olo-font-size-base); line-height: var(--olo-line-height); color: var(--olo-color-text); letter-spacing: var(--olo-letter-spacing); font-weight: var(--olo-font-weight-body); }\n";
        if ( ! empty( $t['font_family'] ) ) {
            $css .= ".olo-template { font-family: var(--olo-font-family); }\n";
        }
        $css .= ".olo-template h1, .olo-template .uk-h1 { font-size: var(--olo-font-size-h1); }\n";
        $css .= ".olo-template h2, .olo-template .uk-h2 { font-size: var(--olo-font-size-h2); }\n";
        $css .= ".olo-template h3, .olo-template .uk-h3 { font-size: var(--olo-font-size-h3); }\n";
        $css .= ".olo-template h4, .olo-template .uk-h4 { font-size: var(--olo-font-size-h4); }\n";
        $css .= ".olo-template h5, .olo-template .uk-h5 { font-size: var(--olo-font-size-h5); }\n";
        $css .= ".olo-template h6, .olo-template .uk-h6 { font-size: var(--olo-font-size-h6); }\n";
        $css .= ".olo-template h1, .olo-template h2, .olo-template h3, .olo-template h4, .olo-template h5, .olo-template h6 { font-weight: var(--olo-font-weight-heading); line-height: var(--olo-heading-line-height); letter-spacing: var(--olo-heading-letter-spacing); text-transform: var(--olo-heading-text-transform); }\n";
        if ( ! empty( $t['font_family_heading'] ) ) {
            $css .= ".olo-template h1, .olo-template h2, .olo-template h3, .olo-template h4, .olo-template h5, .olo-template h6 { font-family: var(--olo-font-family-heading); }\n";
        }

        // Responsive heading typography
        $has_tablet = ! empty( $t['font_size_h1_tablet'] ) || ! empty( $t['font_size_h2_tablet'] ) || ! empty( $t['font_size_h3_tablet'] );
        $has_mobile = ! empty( $t['font_size_h1_mobile'] ) || ! empty( $t['font_size_h2_mobile'] ) || ! empty( $t['font_size_h3_mobile'] );

        if ( $has_tablet ) {
            $css .= "\n/* Responsive typography — tablet */\n";
            $css .= "@media (max-width: 960px) {\n";
            if ( ! empty( $t['font_size_h1_tablet'] ) ) {
                $css .= "  .olo-template h1, .olo-template .uk-h1 { font-size: var(--olo-font-size-h1-tablet); }\n";
            }
            if ( ! empty( $t['font_size_h2_tablet'] ) ) {
                $css .= "  .olo-template h2, .olo-template .uk-h2 { font-size: var(--olo-font-size-h2-tablet); }\n";
            }
            if ( ! empty( $t['font_size_h3_tablet'] ) ) {
                $css .= "  .olo-template h3, .olo-template .uk-h3 { font-size: var(--olo-font-size-h3-tablet); }\n";
            }
            $css .= "}\n";
        }

        if ( $has_mobile ) {
            $css .= "\n/* Responsive typography — mobile */\n";
            $css .= "@media (max-width: 640px) {\n";
            if ( ! empty( $t['font_size_h1_mobile'] ) ) {
                $css .= "  .olo-template h1, .olo-template .uk-h1 { font-size: var(--olo-font-size-h1-mobile); }\n";
            }
            if ( ! empty( $t['font_size_h2_mobile'] ) ) {
                $css .= "  .olo-template h2, .olo-template .uk-h2 { font-size: var(--olo-font-size-h2-mobile); }\n";
            }
            if ( ! empty( $t['font_size_h3_mobile'] ) ) {
                $css .= "  .olo-template h3, .olo-template .uk-h3 { font-size: var(--olo-font-size-h3-mobile); }\n";
            }
            $css .= "}\n";
        }

        // Links
        $css .= "\n/* Link overrides */\n";
        if ( ! empty( $lk['color'] ) ) {
            $css .= ".olo-template a { color: var(--olo-color-link-custom); text-decoration: var(--olo-link-decoration); }\n";
        } else {
            $css .= ".olo-template a { color: var(--olo-color-link); text-decoration: var(--olo-link-decoration); }\n";
        }
        if ( ! empty( $lk['hover_color'] ) ) {
            $css .= ".olo-template a:hover { color: var(--olo-color-link-hover); text-decoration: var(--olo-link-hover-decoration); }\n";
        } else {
            $css .= ".olo-template a:hover { text-decoration: var(--olo-link-hover-decoration); }\n";
        }
        $css .= ".olo-template .uk-text-muted { color: var(--olo-color-text-muted) !important; }\n";
        // UIkit colora em (rosa #f0506e, un quarto «primario») e ins (fondo giallo, i prezzi
        // scontati di Woo): nel template seguono il testo. :where() = una classe sola, quindi una
        // tile che colora il suo corsivo (accento del titolo…) vince sempre. Gemello in styles.js.
        $css .= ".olo-template :where(em) { color: inherit; }\n";
        $css .= ".olo-template :where(:not(pre) > code) { color: inherit; }\n"; // anche il code in linea è rosa in UIkit
        $css .= ".olo-template :where(ins) { background: none; color: inherit; text-decoration: none; }\n";

        // Buttons
        $css .= "\n/* Button overrides */\n";
        $css .= ".olo-template .uk-button { font-size: var(--olo-btn-font-size); font-weight: var(--olo-btn-font-weight); padding: var(--olo-btn-padding); border-radius: var(--olo-btn-border-radius); text-transform: var(--olo-btn-text-transform); letter-spacing: var(--olo-btn-letter-spacing); }\n";
        $css .= ".olo-template .uk-button:hover { filter: brightness(var(--olo-btn-hover-brightness)); }\n";
        $css .= ".olo-template .uk-button-primary { background-color: var(--olo-color-primary) !important; color: var(--olo-color-primary-contrast) !important; }\n";
        $css .= ".olo-template .uk-button-secondary { background-color: var(--olo-color-secondary) !important; color: var(--olo-color-secondary-contrast) !important; }\n";
        $css .= ".olo-template .uk-button-danger { background-color: var(--olo-color-danger) !important; color: #fff !important; }\n";
        $css .= ".olo-template .uk-button-default { border-radius: var(--olo-btn-border-radius); }\n";

        // Form fields
        $css .= "\n/* Form field overrides */\n";
        // :where(): specificità di una classe. Lo stile dei Moduli vale per i campi che la tile
        // non stila; quelli di una tile (Form, Login, Newsletter…) seguono i suoi controlli.
        // Prima input[type=…] (0,2,1) batteva la tile (0,2,0) mentre textarea e select (0,1,1)
        // perdevano: lo stesso modulo usciva con due stili e i controlli dei campi non agivano.
        $css .= ".olo-template :where(input[type=\"text\"], input[type=\"email\"], input[type=\"tel\"], input[type=\"number\"], input[type=\"password\"], input[type=\"url\"], input[type=\"date\"], input[type=\"time\"], textarea, select) {\n";
        $css .= "  background: var(--olo-form-field-bg);\n";
        $css .= "  border: var(--olo-form-field-border);\n";
        $css .= "  border-radius: var(--olo-form-field-radius);\n";
        $css .= "  padding: var(--olo-form-field-padding);\n";
        $css .= "  font-size: var(--olo-form-field-font-size);\n";
        $css .= "}\n";
        $css .= ".olo-template input:focus, .olo-template textarea:focus, .olo-template select:focus {\n";
        $css .= "  border-color: var(--olo-form-focus-border);\n";
        $css .= "  box-shadow: var(--olo-form-focus-shadow);\n";
        $css .= "  outline: none;\n";
        $css .= "}\n";
        // Un campo «annegato» in una casella che lo disegna (la pillola della ricerca con lente e
        // pulsante, la barra del Trip Finder…) è trasparente e senza bordo: l'anello dei Moduli e il
        // contorno di accessibilità finivano su di lui e diventavano un rettangolo attaccato ai bordi
        // della pillola. La casella porta la classe olo-casella e mostra il focus da sé
        // (:focus-within, col suo raggio); il campo dentro resta nudo. (0,3,1): vince su entrambe.
        // Un campo di ricerca che si disegna da sé (barra mobile, ricerca a scomparsa del menu) tiene
        // l'anello attorno a sé, ma nel colore del sito: quello dei Moduli è indaco di serie.
        $css .= ".olo-template input[type=\"search\"]:focus { border-color: var(--olo-color-primary, #e1474f); box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 25%, transparent); outline: none; }\n";
        $css .= ".olo-template .olo-casella :is(input, select, textarea):is(:focus, :focus-visible) { box-shadow: none; outline: none; }\n";
        $css .= ".olo-template label {\n";
        $css .= "  font-size: var(--olo-form-label-font-size);\n";
        $css .= "  font-weight: var(--olo-form-label-font-weight);\n";
        $css .= "  margin-bottom: var(--olo-form-label-margin-bottom);\n";
        $css .= "}\n";

        // Alerts
        $css .= "\n/* Alert overrides */\n";
        // primary = l'avviso «Info» della tile Avviso: UIkit lo fa blu #1e87f0 su #d8eafc
        $css .= ".olo-template .uk-alert-primary { background: color-mix(in srgb, var(--olo-color-info) 10%, transparent); color: var(--olo-color-info); }\n";
        $css .= ".olo-template .uk-alert-success { color: var(--olo-color-success); }\n";
        $css .= ".olo-template .uk-alert-warning { color: var(--olo-color-warning); }\n";
        $css .= ".olo-template .uk-alert-danger { color: var(--olo-color-danger); }\n";

        // Borders & Shadows
        $css .= "\n/* Border & Shadow overrides */\n";
        $css .= ".olo-template .uk-card { border-radius: var(--olo-border-radius-large); }\n";
        $css .= ".olo-template .uk-card-default { border-color: var(--olo-color-border); }\n";
        $css .= ".olo-template .uk-box-shadow-small { box-shadow: var(--olo-shadow-small) !important; }\n";
        $css .= ".olo-template .uk-box-shadow-medium { box-shadow: var(--olo-shadow-medium) !important; }\n";
        $css .= ".olo-template .uk-box-shadow-large { box-shadow: var(--olo-shadow-large) !important; }\n";

        // Container max-width
        $css .= "\n/* Container overrides */\n";
        $css .= ".olo-template .uk-container:not(.uk-container-expand) { max-width: var(--olo-container-max-width); padding-left: var(--olo-gutter-side); padding-right: var(--olo-gutter-side); }\n";
        $css .= ".olo-template .olo-container-narrow { max-width: var(--olo-container-narrow); margin-left: auto; margin-right: auto; }\n";
        $css .= ".olo-template .olo-container-wide { max-width: var(--olo-container-wide); margin-left: auto; margin-right: auto; }\n";
        $css .= ".olo-template .olo-container-full { max-width: 100%; }\n";

        // Section rhythm helpers
        $css .= ".olo-template .olo-section-pad-compact  { padding-top: var(--olo-section-pad-y-compact);  padding-bottom: var(--olo-section-pad-y-compact); }\n";
        $css .= ".olo-template .olo-section-pad-default  { padding-top: var(--olo-section-pad-y-default);  padding-bottom: var(--olo-section-pad-y-default); }\n";
        $css .= ".olo-template .olo-section-pad-spacious { padding-top: var(--olo-section-pad-y-spacious); padding-bottom: var(--olo-section-pad-y-spacious); }\n";
        $css .= ".olo-template .olo-section + .olo-section { margin-top: var(--olo-section-pad-y-between, 0); }\n";

        // Gutter responsive (tablet/mobile media queries)
        $g_desk      = absint( $g['desktop']      ?? 32 );
        $g_tab       = absint( $g['tablet']       ?? 24 );
        $g_mob       = absint( $g['mobile']       ?? 16 );
        $g_side_desk = absint( $g['side_desktop'] ?? 32 );
        $g_side_mob  = absint( $g['side_mobile']  ?? 16 );
        if ( $g_tab !== $g_desk || $g_side_desk !== $g_side_mob ) {
            $css .= "\n/* Gutter responsive — tablet */\n";
            $css .= "@media (max-width: 960px) {\n";
            $css .= "  .olo-template { --olo-gutter: {$g_tab}px; }\n";
            $css .= "}\n";
        }
        if ( $g_mob !== $g_desk || $g_side_mob !== $g_side_desk ) {
            $css .= "\n/* Gutter responsive — mobile */\n";
            $css .= "@media (max-width: 640px) {\n";
            $css .= "  .olo-template { --olo-gutter: {$g_mob}px; --olo-gutter-side: {$g_side_mob}px; }\n";
            $css .= "}\n";
        }

        // Fluid scaling — riscala tutti i token spacing su tablet/mobile
        $fs = $s['fluid_scaling'] ?? [];
        if ( ! empty( $fs['enabled'] ) ) {
            $tab_factor = max( 0.3, min( 1.0, (float) ( $fs['tablet'] ?? 0.85 ) ) );
            $mob_factor = max( 0.3, min( 1.0, (float) ( $fs['mobile'] ?? 0.65 ) ) );
            $css .= "\n/* Fluid scaling — tablet */\n";
            $css .= "@media (max-width: 960px) {\n  .olo-template {\n";
            foreach ( $sp_defaults as $sk => $sv ) {
                $val_raw = ! empty( $sp[ $sk ] ) ? $sp[ $sk ] : $sv;
                $num     = (float) preg_replace( '/[^0-9.]/', '', $val_raw );
                $scaled  = round( $num * $tab_factor, 2 );
                $css    .= "    --olo-space-{$sk}: {$scaled}px;\n";
            }
            $css .= "  }\n}\n";
            $css .= "\n/* Fluid scaling — mobile */\n";
            $css .= "@media (max-width: 640px) {\n  .olo-template {\n";
            foreach ( $sp_defaults as $sk => $sv ) {
                $val_raw = ! empty( $sp[ $sk ] ) ? $sp[ $sk ] : $sv;
                $num     = (float) preg_replace( '/[^0-9.]/', '', $val_raw );
                $scaled  = round( $num * $mob_factor, 2 );
                $css    .= "    --olo-space-{$sk}: {$scaled}px;\n";
            }
            $css .= "  }\n}\n";
        }

        // Dark Mode — override color variables when html.olo-dark-mode is active
        $dc = $s['dark_colors'] ?? [];
        $has_dark = false;
        foreach ( $dc as $v ) {
            if ( ! empty( $v ) ) { $has_dark = true; break; }
        }
        if ( $has_dark ) {
            $css .= "\n/* Dark Mode color overrides */\n";
            $css .= "html.olo-dark-mode .olo-template {\n";
            foreach ( $dc as $key => $value ) {
                if ( empty( $value ) ) continue;
                $prop = str_replace( '_', '-', $key );
                $css .= "  --olo-color-{$prop}: {$value};\n";
            }
            $css .= "}\n";
        }

        // Grain / noise overlay (site-wide). SVG fractalNoise inline come data-URI,
        // sopra tutto il contenuto con mix-blend-mode:overlay. Solo se abilitato.
        $grain = $s['grain'] ?? [];
        if ( ! empty( $grain['enabled'] ) ) {
            $g_op    = max( 0, min( 30, absint( $grain['opacity'] ?? 6 ) ) ) / 100;
            $g_scale = max( 60, min( 400, absint( $grain['scale'] ?? 180 ) ) );
            // data-URI: SVG con feTurbulence (fractalNoise). %23 = '#' url-encoded.
            $noise = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='120' height='120'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='2' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E";
            $css .= "\n/* Grain / noise overlay */\n";
            $css .= ".olo-template { position: relative; }\n";
            $css .= ".olo-template::after { content: \"\"; position: fixed; inset: 0; z-index: 9999; pointer-events: none; mix-blend-mode: overlay; opacity: {$g_op}; background-image: url(\"{$noise}\"); background-size: {$g_scale}px {$g_scale}px; }\n";
        }

        $memo = $css;
        return $memo;
    }

    /**
     * Le famiglie da chiedere a Google, ricavate dai valori salvati.
     *
     * Google rifiuta l'INTERA richiesta css2 (400) se anche una sola famiglia
     * non esiste, e con lei saltano i font veri chiesti insieme. Nei campi
     * Famiglia però finiscono cose che Google non ha: i ruoli del tema
     * (`var(--olo-font-family-heading)`), gli stack di sistema («Arial,
     * Helvetica, sans-serif»), i font caricati dall'utente e, nei temi
     * importati, la sintassi delle variabili di Google
     * («Hedvig+Letters+Serif:opsz@12..24»). Di ogni valore resta il nome di una
     * famiglia che Google può avere, una volta sola.
     *
     * @param array $valori Valori salvati (nomi, stack, var()).
     * @return string[]
     */
    public static function famiglie_google( $valori ) {
        static $di_sistema = [
            'system-ui', '-apple-system', 'blinkmacsystemfont', 'segoe ui', 'ui-sans-serif', 'ui-serif', 'ui-monospace',
            'sans-serif', 'serif', 'monospace', 'cursive', 'fantasy', 'inherit', 'initial', 'unset',
            'arial', 'helvetica', 'helvetica neue', 'georgia', 'verdana', 'tahoma', 'trebuchet ms', 'times', 'times new roman',
            'courier', 'courier new', 'consolas', 'menlo', 'monaco', 'lucida console',
        ];
        $personali = [];
        if ( class_exists( 'Olobuild_Custom_Fonts' ) ) {
            foreach ( (array) Olobuild_Custom_Fonts::get_fonts() as $cf ) {
                if ( is_array( $cf ) && ! empty( $cf['name'] ) ) {
                    $personali[] = strtolower( trim( (string) $cf['name'] ) );
                }
            }
        }
        $famiglie = [];
        foreach ( (array) $valori as $valore ) {
            $valore = trim( (string) $valore );
            if ( $valore === '' || strpos( $valore, 'var(' ) !== false ) {
                continue;
            }
            $nome = trim( explode( ',', $valore )[0], " '\"" );
            $nome = trim( str_replace( '+', ' ', explode( ':', $nome )[0] ) );
            $chiave = strtolower( $nome );
            if ( $nome === '' || in_array( $chiave, $di_sistema, true ) || in_array( $chiave, $personali, true ) ) {
                continue;
            }
            if ( ! isset( $famiglie[ $chiave ] ) ) {
                $famiglie[ $chiave ] = $nome;
            }
        }
        return array_values( $famiglie );
    }

    /**
     * Generate self-hosted @font-face CSS for the given Google Font families.
     *
     * I file vengono scaricati una sola volta e serviti da /uploads (vedi
     * Olobuild_Font_Host): nessuna richiesta del visitatore a Google.
     */
    public function generate_google_fonts_import( $fonts = [] ) {
        if ( empty( $fonts ) ) {
            return '';
        }
        if ( class_exists( 'Olobuild_Font_Host' ) ) {
            // I temi possono richiedere pesi extra (es. Big Shoulders 800/900) via
            // olo_styles.google_fonts_weights, formato css2 "300;400;...;900".
            $weights = '300;400;500;600;700';
            $styles  = get_option( 'olobuild_styles', [] );
            if ( is_array( $styles ) && ! empty( $styles['google_fonts_weights'] )
                && preg_match( '/^[0-9;]+$/', (string) $styles['google_fonts_weights'] ) ) {
                $weights = (string) $styles['google_fonts_weights'];
            }
            // Le famiglie di cui servono i corsivi veri (i temi li chiedono per i titoli con le
            // parole d'accento in corsivo): olo_styles.google_fonts_italic, elenco di nomi.
            $italic = ( is_array( $styles ) && ! empty( $styles['google_fonts_italic'] ) && is_array( $styles['google_fonts_italic'] ) )
                ? array_map( 'sanitize_text_field', $styles['google_fonts_italic'] ) : [];
            return Olobuild_Font_Host::get_font_face_css( $fonts, $weights, $italic );
        }
        return '';
    }

    /**
     * Get built-in presets.
     */
    public function get_presets() {
        $defaults = $this->get_defaults();
        $base_typography = $defaults['typography'];
        $base_layout     = $defaults['layout'];

        return [
            'default' => [
                'name' => 'Default',
                'colors' => $defaults['colors'],
                'typography' => $base_typography,
                'layout' => $base_layout,
            ],
            'corporate' => [
                'name' => 'Corporate',
                'colors' => [
                    'primary'            => '#1E40AF',
                    'primary_contrast'   => '#FFFFFF',
                    'secondary'          => '#0F172A',
                    'secondary_contrast' => '#FFFFFF',
                    'muted'              => '#F1F5F9',
                    'muted_contrast'     => '#1E293B',
                    'success'            => '#059669',
                    'warning'            => '#D97706',
                    'danger'             => '#DC2626',
                    'text'               => '#1E293B',
                    'text_muted'         => '#64748B',
                    'background'         => '#FFFFFF',
                    'border'             => '#CBD5E1',
                    'link'               => '#1E40AF',
                ],
                'typography' => $base_typography,
                'layout' => $base_layout,
            ],
            'creative' => [
                'name' => 'Creative',
                'colors' => [
                    'primary'            => '#EC4899',
                    'primary_contrast'   => '#FFFFFF',
                    'secondary'          => '#7C3AED',
                    'secondary_contrast' => '#FFFFFF',
                    'muted'              => '#FDF4FF',
                    'muted_contrast'     => '#1F2937',
                    'success'            => '#10B981',
                    'warning'            => '#F59E0B',
                    'danger'             => '#EF4444',
                    'text'               => '#1F2937',
                    'text_muted'         => '#9CA3AF',
                    'background'         => '#FFFBEB',
                    'border'             => '#E9D5FF',
                    'link'               => '#EC4899',
                ],
                'typography' => $base_typography,
                'layout' => $base_layout,
            ],
            'minimal' => [
                'name' => 'Minimal',
                'colors' => [
                    'primary'            => '#000000',
                    'primary_contrast'   => '#FFFFFF',
                    'secondary'          => '#525252',
                    'secondary_contrast' => '#FFFFFF',
                    'muted'              => '#F5F5F5',
                    'muted_contrast'     => '#171717',
                    'success'            => '#22C55E',
                    'warning'            => '#EAB308',
                    'danger'             => '#EF4444',
                    'text'               => '#171717',
                    'text_muted'         => '#737373',
                    'background'         => '#FFFFFF',
                    'border'             => '#E5E5E5',
                    'link'               => '#000000',
                ],
                'typography' => $base_typography,
                'layout' => $base_layout,
            ],
            'dark' => [
                'name' => 'Dark',
                'colors' => [
                    'primary'            => '#818CF8',
                    'primary_contrast'   => '#FFFFFF',
                    'secondary'          => '#312E81',
                    'secondary_contrast' => '#E0E7FF',
                    'muted'              => '#1E293B',
                    'muted_contrast'     => '#CBD5E1',
                    'success'            => '#34D399',
                    'warning'            => '#FBBF24',
                    'danger'             => '#F87171',
                    'text'               => '#E2E8F0',
                    'text_muted'         => '#94A3B8',
                    'background'         => '#0F172A',
                    'border'             => '#334155',
                    'link'               => '#818CF8',
                ],
                'typography' => $base_typography,
                'layout' => $base_layout,
            ],
            'nature' => [
                'name' => 'Nature',
                'colors' => [
                    'primary'            => '#059669',
                    'primary_contrast'   => '#FFFFFF',
                    'secondary'          => '#064E3B',
                    'secondary_contrast' => '#D1FAE5',
                    'muted'              => '#ECFDF5',
                    'muted_contrast'     => '#1F2937',
                    'success'            => '#10B981',
                    'warning'            => '#F59E0B',
                    'danger'             => '#EF4444',
                    'text'               => '#1F2937',
                    'text_muted'         => '#6B7280',
                    'background'         => '#F0FDF4',
                    'border'             => '#A7F3D0',
                    'link'               => '#059669',
                ],
                'typography' => $base_typography,
                'layout' => $base_layout,
            ],
            'ocean' => [
                'name' => 'Ocean',
                'colors' => [
                    'primary'            => '#0891B2',
                    'primary_contrast'   => '#FFFFFF',
                    'secondary'          => '#164E63',
                    'secondary_contrast' => '#ECFEFF',
                    'muted'              => '#ECFEFF',
                    'muted_contrast'     => '#164E63',
                    'success'            => '#14B8A6',
                    'warning'            => '#F59E0B',
                    'danger'             => '#EF4444',
                    'text'               => '#0F172A',
                    'text_muted'         => '#64748B',
                    'background'         => '#F0FDFA',
                    'border'             => '#99F6E4',
                    'link'               => '#0891B2',
                ],
                'typography' => $base_typography,
                'layout' => $base_layout,
            ],
            'portfolio' => [
                'name' => 'Portfolio',
                'colors' => [
                    'primary'            => '#F97316',
                    'primary_contrast'   => '#FFFFFF',
                    'secondary'          => '#1E293B',
                    'secondary_contrast' => '#F8FAFC',
                    'muted'              => '#F8FAFC',
                    'muted_contrast'     => '#0F172A',
                    'success'            => '#22C55E',
                    'warning'            => '#FBBF24',
                    'danger'             => '#EF4444',
                    'text'               => '#0F172A',
                    'text_muted'         => '#64748B',
                    'background'         => '#FFFFFF',
                    'border'             => '#E2E8F0',
                    'link'               => '#F97316',
                ],
                'typography' => $base_typography,
                'layout' => $base_layout,
            ],
            'blog' => [
                'name' => 'Blog',
                'colors' => [
                    'primary'            => '#8B5CF6',
                    'primary_contrast'   => '#FFFFFF',
                    'secondary'          => '#6D28D9',
                    'secondary_contrast' => '#F5F3FF',
                    'muted'              => '#FAFAF9',
                    'muted_contrast'     => '#1C1917',
                    'success'            => '#10B981',
                    'warning'            => '#F59E0B',
                    'danger'             => '#EF4444',
                    'text'               => '#292524',
                    'text_muted'         => '#78716C',
                    'background'         => '#FFFBEB',
                    'border'             => '#E7E5E4',
                    'link'               => '#7C3AED',
                ],
                'typography' => $base_typography,
                'layout' => $base_layout,
            ],
            'landing' => [
                'name' => 'Landing Page',
                'colors' => [
                    'primary'            => '#2563EB',
                    'primary_contrast'   => '#FFFFFF',
                    'secondary'          => '#7C3AED',
                    'secondary_contrast' => '#FFFFFF',
                    'muted'              => '#EFF6FF',
                    'muted_contrast'     => '#1E3A5F',
                    'success'            => '#16A34A',
                    'warning'            => '#EAB308',
                    'danger'             => '#DC2626',
                    'text'               => '#111827',
                    'text_muted'         => '#6B7280',
                    'background'         => '#FFFFFF',
                    'border'             => '#DBEAFE',
                    'link'               => '#2563EB',
                ],
                'typography' => $base_typography,
                'layout' => $base_layout,
            ],
            'ecommerce' => [
                'name' => 'E-commerce',
                'colors' => [
                    'primary'            => '#E11D48',
                    'primary_contrast'   => '#FFFFFF',
                    'secondary'          => '#0F172A',
                    'secondary_contrast' => '#F1F5F9',
                    'muted'              => '#F9FAFB',
                    'muted_contrast'     => '#111827',
                    'success'            => '#059669',
                    'warning'            => '#D97706',
                    'danger'             => '#DC2626',
                    'text'               => '#111827',
                    'text_muted'         => '#6B7280',
                    'background'         => '#FFFFFF',
                    'border'             => '#E5E7EB',
                    'link'               => '#E11D48',
                ],
                'typography' => $base_typography,
                'layout' => $base_layout,
            ],
            'luxury' => [
                'name' => 'Luxury',
                'colors' => [
                    'primary'            => '#B45309',
                    'primary_contrast'   => '#FFFFFF',
                    'secondary'          => '#1C1917',
                    'secondary_contrast' => '#FEF3C7',
                    'muted'              => '#1C1917',
                    'muted_contrast'     => '#D6D3D1',
                    'success'            => '#15803D',
                    'warning'            => '#CA8A04',
                    'danger'             => '#B91C1C',
                    'text'               => '#F5F5F4',
                    'text_muted'         => '#A8A29E',
                    'background'         => '#0C0A09',
                    'border'             => '#44403C',
                    'link'               => '#D97706',
                ],
                'typography' => $base_typography,
                'layout' => $base_layout,
            ],
        ];
    }
}
