<?php
/**
 * Tile Tracciamento spedizione — il campo per il numero della spedizione che apre il portale del corriere.
 *
 * L'indirizzo è un modello col segnaposto {codice} (https://traccia.esempio.it/?n={codice}): il numero scritto si
 * ripulisce (via gli spazi, maiuscolo se richiesto), si controlla la lunghezza minima e prende il posto di
 * {codice}, codificato; senza segnaposto va in fondo. Un indirizzo di pagina che ha già un «?» (le demo dei temi:
 * …/traccia/?palette=x) riceve il resto con «&». Si apre in una nuova scheda (window.open, noopener) o nella
 * stessa (location.assign). Con due o più operatori, prima del campo c'è la scelta del corriere, ognuno col suo
 * modello. «Valore dall'indirizzo» compila il campo dal parametro della pagina, nel browser (la pagina d'arrivo).
 * Il messaggio d'errore sta in una regione role="alert"; nel builder il collegamento non si apre: il messaggio dice
 * dove porterebbe. Il pulsante segue lo stile pulsanti del tema (--olo-btn-*), come Form e Banner CTA.
 * Nata per i temi industriali della fase 3 (14 ott 2026). Config: src/config/elements/tracciamento.js.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Tracciamento_Tile extends Olobuild_Tile_Base {

    protected $type     = 'tracciamento';
    protected $name     = 'Tracciamento spedizione';
    protected $icon     = 'dashicons-car';
    protected $category = 'interactive';

    protected $defaults = [
        // contenuto
        'indirizzo'                  => '',
        'operatori'                  => [],
        'etichetta'                  => 'Numero di spedizione',
        'mostra_etichetta'           => true,
        'segnaposto'                 => 'Es. 1Z999AA10123456784',
        'testo_pulsante'             => 'Traccia',
        'icona_pulsante'             => 'arrow-right',
        'icona_campo'                => 'package',
        'nota'                       => '',
        'nuova_scheda'               => true,
        'maiuscolo'                  => true,
        'lunghezza_minima'           => 4,
        'testo_errore'               => 'Scrivi il numero di spedizione completo.',
        'parametro'                  => '',
        // stile
        'sfondo_campo'               => '',
        'colore_campo'               => '',
        'bordo_campo'                => [],
        'colore_etichetta'           => '',
        'colore_nota'                => '',
        'colore_errore'              => '',
        'sfondo_pulsante'            => '',
        'colore_pulsante'            => '',
        'ombra'                      => 'none',
        'ombra_h'                    => '0',
        'ombra_v'                    => '4',
        'ombra_blur'                 => '10',
        'ombra_spread'               => '0',
        'ombra_color'                => 'rgba(0,0,0,0.15)',
        'ombra_inset'                => false,
        'etichetta_font_family'      => '',
        'etichetta_font_size'        => 14,
        'etichetta_font_size_tablet' => '',
        'etichetta_font_size_mobile' => '',
        'etichetta_font_weight'      => '600',
        'campo_font_family'          => '',
        'campo_font_size'            => 17,
        'campo_font_size_tablet'     => '',
        'campo_font_size_mobile'     => '',
        'campo_font_weight'          => '500',
        'pulsante_font_family'       => '',
        'pulsante_font_size'         => 16,
        'pulsante_font_size_tablet'  => '',
        'pulsante_font_size_mobile'  => '',
        'pulsante_font_weight'       => '600',
        'pulsante_text_transform'    => '',
        'pulsante_letter_spacing'    => '',
        'nota_font_family'           => '',
        'nota_font_size'             => 13,
        'nota_font_size_tablet'      => '',
        'nota_font_size_mobile'      => '',
        'nota_font_weight'           => '',
        'raggio_campo'               => '',
        'raggio_pulsante'            => '',
        'altezza'                    => 56,
        'larghezza_max'              => 640,
        'disposizione'               => 'riga',
        'allineamento'               => 'left',
    ];

    public function get_controls() {
        return [];
    }

    /**
     * Il modello d'indirizzo: http(s), relativo o «//…»; '' per gli altri schemi (javascript:, data:…), con spazi o
     * con caratteri che non stanno in un indirizzo. Le graffe di {codice} restano (esc_url le toglierebbe).
     */
    private static function modello( $v ) {
        if ( is_array( $v ) ) {
            $v = $v['url'] ?? '';
        }
        $v = trim( (string) $v );
        if ( '' === $v || preg_match( '/[\s<>"\'\\\\`]/', $v ) ) {
            return '';
        }
        if ( preg_match( '/^([a-z][a-z0-9+.\-]*):/i', $v, $m ) ) {
            if ( ! in_array( strtolower( $m[1] ), [ 'http', 'https' ], true ) ) {
                return '';
            }
        }
        return substr( $v, 0, 2000 );
    }

    /** Il nome di un parametro dell'indirizzo: lettere, cifre, - e _, o '' (come «Valore dall'indirizzo» del Form). */
    private static function nome_param( $v ) {
        return substr( (string) preg_replace( '/[^A-Za-z0-9_-]/', '', (string) $v ), 0, 40 );
    }

    /**
     * Il raggio scelto nel controllo a 4 angoli, anche a zero (angoli vivi); '' se non è stato scelto
     * (vuoto, «Azzera»): allora vale il raggio dei pulsanti del tema.
     */
    private static function raggio( $v ) {
        if ( ! is_array( $v ) ) {
            $n = trim( (string) $v );
            return is_numeric( $n ) ? max( 0, (int) $n ) . 'px' : '';
        }
        $scelto = false;
        foreach ( [ 'tl', 'tr', 'br', 'bl' ] as $k ) {
            if ( isset( $v[ $k ] ) && '' !== $v[ $k ] ) {
                $scelto = true;
            }
        }
        if ( ! $scelto ) {
            return '';
        }
        return absint( $v['tl'] ?? 0 ) . 'px ' . absint( $v['tr'] ?? 0 ) . 'px ' . absint( $v['br'] ?? 0 ) . 'px ' . absint( $v['bl'] ?? 0 ) . 'px';
    }

    /**
     * CSS di un testo dal controllo tipografia: famiglia, misura (anche per tablet e telefono, mai sotto $min),
     * peso e colore (già risolto da chi chiama).
     */
    private function css_testo( $s, $k, $selettore, $colore = '', $min = 0 ) {
        $d   = '';
        $fam = $this->resolve_font_family( $s[ $k . '_font_family' ] ?? '' );
        $fs  = (float) ( $s[ $k . '_font_size' ] ?? 0 );
        $fw  = $this->font_weight_css( $s[ $k . '_font_weight' ] ?? '' );
        if ( '' !== $fam ) {
            $d .= 'font-family:' . $fam . ';';
        }
        if ( $fs > 0 ) {
            $d .= 'font-size:' . max( $min, $fs ) . 'px;';
        }
        if ( $fw ) {
            $d .= 'font-weight:' . $fw . ';';
        }
        if ( '' !== $colore ) {
            $d .= 'color:' . $colore . ';';
        }
        $css = '' !== $d ? "{$selettore}{{$d}}" : '';
        foreach ( [ 'tablet' => 960, 'mobile' => 480 ] as $disp => $px ) {
            $v = (float) ( $s[ $k . '_font_size_' . $disp ] ?? 0 );
            if ( $v > 0 ) {
                $css .= '@media (max-width:' . $px . 'px){' . $selettore . '{font-size:' . max( $min, $v ) . 'px}}';
            }
        }
        return $css;
    }

    public function render( $settings ) {
        $s       = wp_parse_args( $settings, $this->defaults );
        $uid     = 'otr-' . $this->chiave_stabile( $settings );
        $sel     = '#' . $uid;
        $builder = ! empty( $s['_builder_mode'] );

        // ── Dove porta: il modello principale e, facoltativi, gli operatori (ognuno col suo, o col principale) ──
        $modello   = self::modello( $s['indirizzo'] );
        $operatori = [];
        foreach ( (array) $s['operatori'] as $op ) {
            if ( ! is_array( $op ) ) {
                continue;
            }
            $nome = trim( (string) ( $op['nome'] ?? '' ) );
            $mod  = self::modello( $op['indirizzo'] ?? '' );
            if ( '' === $mod ) {
                $mod = $modello;
            }
            if ( '' === $nome || '' === $mod ) {
                continue;
            }
            $operatori[] = [ 'nome' => $nome, 'modello' => $mod ];
        }
        if ( '' === $modello && 1 === count( $operatori ) ) {
            $modello = $operatori[0]['modello'];
        }
        $scelta  = count( $operatori ) > 1;
        $usabile = '' !== $modello || $scelta;
        if ( ! $usabile && ! $builder ) {
            return '';
        }

        // ── Testi e comportamento ──
        $etichetta  = trim( (string) $s['etichetta'] );
        $etichetta  = '' !== $etichetta ? $etichetta : __( 'Numero di spedizione', 'olobuilder' );
        $mostra_et  = ! empty( $s['mostra_etichetta'] );
        $segnaposto = trim( (string) $s['segnaposto'] );
        $testo_p    = trim( (string) $s['testo_pulsante'] );
        $ico_p      = sanitize_text_field( (string) $s['icona_pulsante'] );
        $ico_c      = sanitize_text_field( (string) $s['icona_campo'] );
        $nota       = trim( (string) $s['nota'] );
        $maiuscolo  = ! empty( $s['maiuscolo'] );
        $param      = self::nome_param( $s['parametro'] );
        $minimo     = max( 1, min( 30, (int) $s['lunghezza_minima'] ) );
        $errore     = trim( (string) $s['testo_errore'] );
        $errore     = '' !== $errore ? $errore : __( 'Scrivi il numero di spedizione completo.', 'olobuilder' );

        // ── Stile: tutto dai token del tema, salvo ciò che è scelto ──
        $acc_s = $this->safe_color_css( $s['sfondo_pulsante'] );
        $pbg   = '' !== $acc_s ? $acc_s : 'var(--olo-color-primary)';
        $pfg   = $this->safe_color_css( $s['colore_pulsante'] ) ?: $this->safe_color_css( $s['pulsante_color'] ?? '' );
        if ( '' === $pfg ) {
            $pfg = '' !== $acc_s
                ? ( Olobuild_Tile_Utils::testo_su_fondo( $acc_s, 'var(--olo-color-light, #ffffff)' ) ?: 'var(--olo-color-light, #ffffff)' )
                : 'var(--olo-color-primary-contrast, #ffffff)';
        }
        $cbg_s = $this->safe_color_css( $s['sfondo_campo'] );
        $cbg   = '' !== $cbg_s ? $cbg_s : 'var(--olo-color-background, #ffffff)';
        $cfg_c = $this->safe_color_css( $s['colore_campo'] ) ?: $this->safe_color_css( $s['campo_color'] ?? '' );
        if ( '' === $cfg_c ) {
            $cfg_c = '' !== $cbg_s
                ? ( Olobuild_Tile_Utils::testo_su_fondo( $cbg_s, 'var(--olo-color-text, #374151)' ) ?: 'var(--olo-color-text, #374151)' )
                : 'var(--olo-color-text, #374151)';
        }
        $err    = $this->safe_color_css( $s['colore_errore'] ) ?: 'var(--olo-color-danger, #b42318)';
        $c_et   = $this->safe_color_css( $s['colore_etichetta'] ) ?: $this->safe_color_css( $s['etichetta_color'] ?? '' );
        $c_nota = $this->safe_color_css( $s['colore_nota'] ) ?: ( $this->safe_color_css( $s['nota_color'] ?? '' ) ?: 'color-mix(in srgb, currentColor 70%, transparent)' );

        // Bordo del campo: quello scelto (senza colore, il colore dei bordi del tema; a zero = nessun bordo).
        $bordo = 'border:1px solid var(--olo-color-border, #e5e7eb);';
        $b     = $s['bordo_campo'] ?? null;
        if ( is_array( $b ) && Olobuild_Tile_Utils::border_is_set( $b ) ) {
            if ( '' === trim( (string) ( $b['color'] ?? '' ) ) ) {
                $b['color'] = 'var(--olo-color-border, #e5e7eb)';
            }
            $bordo = Olobuild_Tile_Utils::border_css( $b ) ?: 'border:0;';
        } elseif ( is_string( $b ) && '' !== trim( $b ) ) {
            $c_b = Olobuild_Tile_Utils::border_color( $b );
            if ( '' !== $c_b ) {
                $bordo = 'border:1px solid ' . $c_b . ';';
            }
        }

        $rc     = self::raggio( $s['raggio_campo'] ) ?: 'var(--olo-btn-border-radius, .5rem)';
        $rp     = self::raggio( $s['raggio_pulsante'] ) ?: 'var(--olo-btn-border-radius, .5rem)';
        $ombra  = Olobuild_Tile_Utils::shadow_value( $s, 'ombra' );
        $om     = 'none' !== $ombra ? $ombra : '';
        $anello = ( '' !== $om ? $om . ',' : '' ) . '0 0 0 3px color-mix(in srgb, var(--otr-acc) 30%, transparent)';
        $alto   = max( 44, min( 72, (int) $s['altezza'] ) );
        $largo  = max( 320, min( 960, (int) $s['larghezza_max'] ) );
        $colonna = 'colonna' === (string) $s['disposizione'];
        $centro  = 'center' === (string) $s['allineamento'];
        $tt      = in_array( (string) $s['pulsante_text_transform'], [ 'none', 'uppercase', 'lowercase', 'capitalize' ], true ) ? (string) $s['pulsante_text_transform'] : 'var(--olo-btn-text-transform, none)';
        $ls      = is_numeric( $s['pulsante_letter_spacing'] ) ? (float) $s['pulsante_letter_spacing'] . 'px' : 'var(--olo-btn-letter-spacing, normal)';

        $classi = 'otr otr--' . ( $colonna ? 'colonna' : 'riga' ) . ( $centro ? ' otr--centro' : '' ) . ( $maiuscolo ? ' otr--maiuscolo' : '' );

        ob_start();
        // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS da colori passati da safe_color_css() e dagli helper di Olobuild_Tile_Utils, numeri forzati, valori da elenchi chiusi e selettori generati qui
        echo '<style>';
        echo "{$sel}{--otr-h:{$alto}px;--otr-max:{$largo}px;--otr-rc:{$rc};--otr-rp:{$rp};--otr-acc:{$pbg};--otr-pbg:{$pbg};--otr-pfg:{$pfg};--otr-cbg:{$cbg};--otr-cfg:{$cfg_c};--otr-err:{$err};--otr-nota:{$c_nota}}";
        echo "{$sel} .otr-form{margin:0;max-width:var(--otr-max)}";
        if ( $centro ) {
            echo "{$sel} .otr-form{margin-left:auto;margin-right:auto;text-align:center}";
        }
        echo "{$sel} .otr-etichetta{display:block;margin:0 0 .5rem;line-height:1.3}";
        echo "{$sel} .otr-sr{position:absolute!important;width:1px;height:1px;margin:-1px;padding:0;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0}";
        // In riga: il campo prende lo spazio, il pulsante quanto gli serve; se non ci stanno, il pulsante va sotto, largo.
        echo "{$sel} .otr-riga{display:flex;flex-wrap:wrap;align-items:stretch;gap:.5rem;text-align:left}";
        echo "{$sel} .otr-scelta{position:relative;display:flex;flex:1 1 10rem;max-width:16rem;min-width:0}";
        echo "{$sel} .otr-campo{position:relative;display:flex;flex:999 1 14rem;min-width:0}";
        echo "{$sel} .otr-input,{$sel} .otr-select{box-sizing:border-box;width:100%;height:var(--otr-h);margin:0;padding:0 1.1em;{$bordo}border-radius:var(--otr-rc);background:var(--otr-cbg);color:var(--otr-cfg);font:inherit;line-height:1.2;outline:none;-webkit-appearance:none;appearance:none;box-shadow:" . ( '' !== $om ? $om : 'none' ) . ';transition:border-color .2s ease,box-shadow .2s ease}';
        echo "{$sel} .otr-input::placeholder{color:currentColor;opacity:.5;text-transform:none;letter-spacing:normal;font-weight:400}";
        echo "{$sel} .otr-input:focus,{$sel} .otr-select:focus{border-color:var(--otr-acc);box-shadow:{$anello}}";
        echo "{$sel} .otr-input[aria-invalid=\"true\"]{border-color:var(--otr-err)}";
        if ( $maiuscolo ) {
            echo "{$sel} .otr-input{text-transform:uppercase;letter-spacing:.04em}";
        }
        echo "{$sel} .otr-campo--icona .otr-input{padding-left:2.9em}";
        echo "{$sel} .otr-icona{position:absolute;left:1em;top:50%;z-index:1;transform:translateY(-50%);display:flex;color:var(--otr-cfg);opacity:.55;pointer-events:none}";
        echo "{$sel} .otr-icona svg,{$sel} .otr-icona .olo-lucide-icon{width:1.2em;height:1.2em}";
        echo "{$sel} .otr-select{padding-right:2.6em;cursor:pointer;text-overflow:ellipsis;white-space:nowrap}";
        echo "{$sel} .otr-freccia{position:absolute;right:.95em;top:50%;transform:translateY(-50%);display:flex;color:var(--otr-cfg);opacity:.6;pointer-events:none}";
        echo "{$sel} .otr-freccia svg,{$sel} .otr-freccia .olo-lucide-icon{width:1em;height:1em}";
        echo "{$sel} .otr-pulsante{box-sizing:border-box;flex:1 0 auto;display:inline-flex;align-items:center;justify-content:center;gap:.55em;min-height:var(--otr-h);margin:0;padding:0 1.6em;border:0;border-radius:var(--otr-rp);background:var(--otr-pbg);color:var(--otr-pfg);font:inherit;font-weight:600;line-height:1.1;text-transform:{$tt};letter-spacing:{$ls};white-space:nowrap;cursor:pointer;-webkit-appearance:none;appearance:none;box-shadow:" . ( '' !== $om ? $om : 'none' ) . ';transition:background-color .2s ease,transform .15s ease}';
        echo "{$sel} .otr-pulsante:hover{background:color-mix(in srgb, var(--otr-pbg) 86%, var(--otr-pfg));color:var(--otr-pfg)}";
        echo "{$sel} .otr-pulsante:active{transform:translateY(1px)}";
        echo "{$sel} .otr-pulsante:focus{outline:none}";
        echo "{$sel} .otr-pulsante:focus-visible{outline:2px solid var(--otr-acc);outline-offset:3px}";
        echo "{$sel} .otr-pulsante svg,{$sel} .otr-pulsante .olo-lucide-icon{width:1.1em;height:1.1em;flex:none}";
        echo "{$sel} .otr-nota{margin:.625rem 0 0;line-height:1.5;color:var(--otr-nota)}";
        echo "{$sel} .otr-errore{margin:.625rem 0 0;font-size:14px;font-weight:600;line-height:1.45;color:var(--otr-err)}";
        echo "{$sel} .otr-errore:empty{margin:0}";
        echo "{$sel} .otr-errore.is-info{color:inherit;font-weight:500;opacity:.8;overflow-wrap:anywhere}";
        if ( $builder ) {
            echo "{$sel} .otr-avviso{margin:.75rem 0 0;padding:.75rem 1rem;font-size:14px;line-height:1.45;opacity:.75;border:1px dashed color-mix(in srgb, currentColor 30%, transparent);border-radius:.5rem}";
        }
        // In colonna, e sempre sul telefono: campo e pulsante uno sotto l'altro, larghi uguali.
        $pila = "{$sel} .otr-riga{display:grid;grid-template-columns:minmax(0,1fr)}{$sel} .otr-scelta{max-width:none}{$sel} .otr-pulsante{width:100%}";
        echo $colonna ? $pila : '@media (max-width:639px){' . $pila . '}';
        echo '@media (prefers-reduced-motion:reduce){' . "{$sel} .otr-input,{$sel} .otr-select,{$sel} .otr-pulsante{transition:none}" . '}';
        echo $this->css_testo( $s, 'etichetta', "{$sel} .otr-etichetta", $c_et );
        echo $this->css_testo( $s, 'campo', "{$sel} .otr-input,{$sel} .otr-select", '', 16 ); // mai sotto 16 px: il telefono ingrandirebbe la pagina al tocco
        echo $this->css_testo( $s, 'pulsante', "{$sel} .otr-pulsante" );
        echo $this->css_testo( $s, 'nota', "{$sel} .otr-nota" );
        echo '</style>';

        $conf = [
            'modello'   => $modello,
            'operatori' => $operatori,
            'nuova'     => ! empty( $s['nuova_scheda'] ),
            'maiuscolo' => $maiuscolo,
            'min'       => $minimo,
            'errore'    => $errore,
            'param'     => $param,
            'builder'   => $builder,
        ];
        if ( $builder ) {
            /* translators: %s: l'indirizzo che si aprirebbe sul sito */
            $conf['anteprima'] = __( 'Nel builder il collegamento non si apre: sul sito porterebbe a %s', 'olobuilder' );
        }
        $descr = ( '' !== $nota ? $uid . '-n ' : '' ) . $uid . '-e';

        echo '<div id="' . esc_attr( $uid ) . '" class="' . esc_attr( $classi ) . '" data-otr="' . esc_attr( wp_json_encode( $conf ) ) . '">';
        echo '<form class="otr-form" role="search" novalidate aria-labelledby="' . esc_attr( $uid ) . '-et">';
        echo '<label class="otr-etichetta' . ( $mostra_et ? '' : ' otr-sr' ) . '" id="' . esc_attr( $uid ) . '-et" for="' . esc_attr( $uid ) . '-c"' . ( $builder && $mostra_et ? ' data-olo-editable="etichetta"' : '' ) . '>' . esc_html( $etichetta ) . '</label>';
        echo '<div class="otr-riga">';
        if ( $scelta ) {
            echo '<div class="otr-scelta"><select class="otr-select" id="' . esc_attr( $uid ) . '-o" aria-label="' . esc_attr__( 'Corriere', 'olobuilder' ) . '">';
            foreach ( $operatori as $i => $op ) {
                echo '<option value="' . (int) $i . '">' . esc_html( $op['nome'] ) . '</option>';
            }
            echo '</select><span class="otr-freccia" aria-hidden="true">' . $this->render_icon_html( 'chevron-down', 0.8, 'aria-hidden="true"' ) . '</span></div>';
        }
        echo '<div class="otr-campo' . ( '' !== $ico_c ? ' otr-campo--icona' : '' ) . '">';
        if ( '' !== $ico_c ) {
            echo '<span class="otr-icona" aria-hidden="true">' . $this->render_icon_html( $ico_c, 1, 'aria-hidden="true"' ) . '</span>';
        }
        echo '<input class="otr-input" id="' . esc_attr( $uid ) . '-c" type="text" name="' . esc_attr( '' !== $param ? $param : 'codice' ) . '" maxlength="80" autocomplete="off" autocorrect="off" spellcheck="false" autocapitalize="' . ( $maiuscolo ? 'characters' : 'off' ) . '" enterkeyhint="search"'
            . ( '' !== $segnaposto ? ' placeholder="' . esc_attr( $segnaposto ) . '"' : '' )
            . ' aria-describedby="' . esc_attr( $descr ) . '" aria-invalid="false">';
        echo '</div>';
        echo '<button class="otr-pulsante" type="submit" data-olo-interactive' . ( '' === $testo_p ? ' aria-label="' . esc_attr__( 'Traccia la spedizione', 'olobuilder' ) . '"' : '' ) . '>';
        if ( '' !== $testo_p ) {
            echo '<span class="otr-pulsante-testo"' . ( $builder ? ' data-olo-editable="testo_pulsante"' : '' ) . '>' . esc_html( $testo_p ) . '</span>';
        }
        if ( '' !== $ico_p ) {
            echo $this->render_icon_html( $ico_p, 0.9, 'aria-hidden="true"' );
        }
        echo '</button>';
        echo '</div>';
        if ( '' !== $nota ) {
            echo '<p class="otr-nota" id="' . esc_attr( $uid ) . '-n"' . ( $builder ? ' data-olo-editable="nota"' : '' ) . '>' . esc_html( $nota ) . '</p>';
        }
        echo '<p class="otr-errore" id="' . esc_attr( $uid ) . '-e" role="alert"></p>';
        if ( $builder && ! $usabile ) {
            echo '<p class="otr-avviso">' . esc_html__( 'Scrivi nel Contenuto l\'indirizzo del tracciamento: finché manca, sul sito la tile non si vede.', 'olobuilder' ) . '</p>';
        }
        echo '</form></div>';
        echo '<script>' . $this->script() . '[].forEach.call(document.querySelectorAll("[data-otr]"),function(x){window.oloTracciamento(x);});</script>';
        // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
        return ob_get_clean();
    }

    /**
     * La funzione del campo: ripulisce e controlla il numero, compone l'indirizzo e lo apre. Va in OGNI istanza
     * (definita una volta sola, «if(!window…)»), così una tile in una sezione caricata dopo non resta senza.
     * Niente «&» nello script (wptexturize lo riscrive fra un «<» e un «>»): condizioni annidate e la «&» da
     * String.fromCharCode(38). Ogni istanza si avvia una volta (data-otr-pronto).
     */
    private function script() {
        return <<<'JS'
if(!window.oloTracciamento){window.oloTracciamento=function(el){
if(!el){return;}
if(el.getAttribute("data-otr-pronto")==="1"){return;}
el.setAttribute("data-otr-pronto","1");
var C={};try{C=JSON.parse(el.getAttribute("data-otr")||"{}");}catch(e){}
var form=el.querySelector(".otr-form"),campo=el.querySelector(".otr-input"),scelta=el.querySelector(".otr-select"),msg=el.querySelector(".otr-errore");
if(!form){return;}if(!campo){return;}
var E=String.fromCharCode(38);
function pulisci(v){v=String(v||"").replace(/\s+/g,"");if(C.maiuscolo){v=v.toUpperCase();}return v;}
function avvisa(t,info){if(!msg){return;}msg.textContent=t||"";if(info){msg.classList.add("is-info");}else{msg.classList.remove("is-info");}}
function errore(){campo.setAttribute("aria-invalid","true");avvisa(C.errore||"",false);try{campo.focus();}catch(x){}}
function costruisci(modello,codice){
var c=encodeURIComponent(codice),u=String(modello||"");
if(/\{codice\}/i.test(u)){u=u.replace(/\{codice\}/gi,function(){return c;});}
else if(/%7Bcodice%7D/i.test(u)){u=u.replace(/%7Bcodice%7D/gi,function(){return c;});}
else{u=u+c;}
var p=u.split("?");if(p.length>2){u=p.shift()+"?"+p.join(E);}
return u;
}
function sicuro(u){var a=document.createElement("a");a.href=u;var pr=String(a.protocol||"").toLowerCase();return pr==="http:"||pr==="https:";}
if(C.param){if(!campo.value){var q=null;try{q=new URLSearchParams(window.location.search).get(C.param);}catch(x){q=null;}if(q!==null){campo.value=pulisci(q).slice(0,80);}}}
campo.addEventListener("input",function(){if(campo.getAttribute("aria-invalid")==="true"){campo.setAttribute("aria-invalid","false");avvisa("",false);}});
form.addEventListener("submit",function(e){
e.preventDefault();
var v=pulisci(campo.value);campo.value=v;
var min=Math.max(1,+C.min||1);
if(v.length<min){errore();return;}
var modello=C.modello||"";
if(scelta){var op=(C.operatori||[])[+scelta.value];if(op){if(op.modello){modello=op.modello;}}}
if(!modello){errore();return;}
var u=costruisci(modello,v);
if(!sicuro(u)){errore();return;}
campo.setAttribute("aria-invalid","false");
if(C.builder){avvisa(String(C.anteprima||"").replace("%s",function(){return u;}),true);return;}
avvisa("",false);
if(C.nuova){window.open(u,"_blank","noopener");}else{window.location.assign(u);}
});
};}
JS;
    }
}
