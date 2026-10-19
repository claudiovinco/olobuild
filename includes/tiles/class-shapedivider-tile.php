<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Shapedivider_Tile extends Olobuild_Tile_Base {

    /** Il riquadro dei tracciati: 1200 × 120, il pieno della forma sta in basso. */
    const W = 1200;
    const H = 120;

    protected $type     = 'shapedivider';
    protected $name     = 'Shape Divider';
    protected $icon     = 'dashicons-editor-contract';
    protected $category = 'layout';
    protected $defaults = [
        'shape'                      => 'wave',
        'position'                   => 'bottom',
        'flip_horizontal'            => false,
        'flip_vertical'              => false,
        'width'                      => '100',
        'height'                     => '80',
        'color'                      => '', // come il config: '' = token della superficie (prima una banda bianca su ogni sfondo)
        'z_index'                    => '1',
        // Storiche: l'altezza per tablet e telefono prima del selettore del dispositivo (vedi render()).
        'responsive_height_tablet'   => '',
        'responsive_height_mobile'   => '',
        // Livelli: copie della forma dietro quella piena, sfalsate verso l'esterno e via via più trasparenti.
        'layers'                     => '1',
        'layer_gap'                  => 15,
        'layer2_color'               => '',
        'layer3_color'               => '',
        // Sfumatura: dal colore di ogni livello al «Colore finale» ('' = lo stesso colore che svanisce).
        'gradient'                   => false,
        'color_end'                  => '',
        'gradient_angle'             => 90,
        // Forma «Dinamica»: onde che si muovono da sole.
        'wave_count'                 => 3,
        'wave_amplitude'             => 70,
        'wave_duration'              => 10,
        // Ingresso dei livelli quando il divisore arriva nello schermo.
        'layers_anim'                => 'none',
        'layers_anim_duration'       => 1000,
        'layers_anim_delay'          => 200,
        'border'                  => [],
        'border_hover'            => [],
        'border_hover_duration'   => 300,
        'border_effect'           => 'none',
        'border_effect_intensity' => 'medium',
        'border_effect_color2'    => '',
        'border_effect_angle'     => 135,
        'border_effect_speed'     => 4,
    ];

    public function get_controls() {
        return [
            [ 'key' => 'shape',            'type' => 'select', 'label' => 'Shape' ],
            [ 'key' => 'position',         'type' => 'select', 'label' => 'Position' ],
            [ 'key' => 'color',            'type' => 'color',  'label' => 'Color' ],
            [ 'key' => 'gradient',         'type' => 'toggle', 'label' => 'Gradient' ],
            [ 'key' => 'color_end',        'type' => 'color',  'label' => 'Gradient end color' ],
            [ 'key' => 'gradient_angle',   'type' => 'range',  'label' => 'Gradient direction' ],
            [ 'key' => 'layers',           'type' => 'select', 'label' => 'Layers' ],
            [ 'key' => 'layer_gap',        'type' => 'range',  'label' => 'Layer distance (%)' ],
            [ 'key' => 'layer2_color',     'type' => 'color',  'label' => 'Layer 2 color' ],
            [ 'key' => 'layer3_color',     'type' => 'color',  'label' => 'Layer 3 color' ],
            [ 'key' => 'height',           'type' => 'range',  'label' => 'Height (px)' ],
            [ 'key' => 'width',            'type' => 'range',  'label' => 'Width (%)' ],
            [ 'key' => 'flip_horizontal',  'type' => 'toggle', 'label' => 'Flip Horizontal' ],
            [ 'key' => 'flip_vertical',    'type' => 'toggle', 'label' => 'Flip Vertical' ],
            [ 'key' => 'z_index',          'type' => 'range',  'label' => 'Z-Index' ],
            [ 'key' => 'wave_count',       'type' => 'range',  'label' => 'Waves' ],
            [ 'key' => 'wave_amplitude',   'type' => 'range',  'label' => 'Wave amplitude (%)' ],
            [ 'key' => 'wave_duration',    'type' => 'range',  'label' => 'Wave cycle (s)' ],
            [ 'key' => 'layers_anim',      'type' => 'select', 'label' => 'Layers entrance' ],
            [ 'key' => 'layers_anim_duration', 'type' => 'range', 'label' => 'Entrance duration (ms)' ],
            [ 'key' => 'layers_anim_delay',    'type' => 'range', 'label' => 'Delay between layers (ms)' ],
        ];
    }

    /**
     * Le forme: tracciati nel riquadro 0 0 1200 120 col pieno in basso. Stanno in un file solo, che
     * legge anche l'inspector per le miniature del selettore (assets/data/shapedivider-shapes.json).
     * «dinamica» lì è solo la miniatura: le onde vive le disegna onda() qui sotto.
     */
    public static function forme() {
        static $forme = null;
        if ( null === $forme ) {
            $file  = OLOBUILD_PATH . 'assets/data/shapedivider-shapes.json';
            $dati  = is_readable( $file ) ? json_decode( (string) file_get_contents( $file ), true ) : null; // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- file del plugin, letto una volta per richiesta
            $forme = is_array( $dati ) ? $dati : [ 'wave' => 'M0,0 C300,100 900,0 1200,80 L1200,120 L0,120 Z' ];
        }
        return $forme;
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );
        // Prima di tutto (un template annidato cambierebbe il nodo in resa): il seme delle onde dinamiche
        // resta lo stesso fra un caricamento e l'altro, e due divisori della stessa pagina non si muovono uguali.
        $seme = hexdec( substr( $this->chiave_stabile( $settings ), 0, 6 ) );

        $forme   = self::forme();
        $shape   = ( is_string( $s['shape'] ) && isset( $forme[ $s['shape'] ] ) ) ? $s['shape'] : 'wave';
        $position = ( $s['position'] === 'top' ) ? 'top' : 'bottom';
        $color    = $this->safe_color_css( $s['color'] ) ?: 'var(--olo-color-surface, #ffffff)';
        $height   = max( 10, intval( $s['height'] ) );
        $width    = max( 100, min( 300, intval( $s['width'] ) ) );
        $flip_h   = ! empty( $s['flip_horizontal'] );
        $flip_v   = ! empty( $s['flip_vertical'] );
        $z_index  = max( 0, min( 99, intval( $s['z_index'] ) ) );
        $livelli  = max( 1, min( 3, intval( $s['layers'] ) ) );

        // Build CSS transforms
        //
        // I tracciati riempiono la parte BASSA del viewBox: la forma piena sta
        // contro il lato inferiore del disegno. In «Alto» il divisore esce sopra
        // la sezione e quel lato tocca la sezione: giusto cosi'. In «Basso» esce
        // sotto, il lato che tocca la sezione e' quello alto, e senza ribaltarlo
        // la forma restava staccata dalla sua sezione, a testa in giu'. In
        // «Basso» il ribaltamento e' quindi la posizione normale.
        //
        // «Specchia verticale» ribalta il divisore attorno alla linea fra le due
        // sezioni: invece di sporgere nella sezione vicina entra nella sua, sullo
        // stesso bordo, e la parte piena resta contro la linea (dentro, in
        // «Basso» la forma piena torna in basso, in «Alto» va in alto). Prima
        // ribaltava solo il disegno e lo lasciava fuori: la parte piena finiva
        // lontano dalla linea, sopra il contenuto della sezione vicina (il logo
        // del piè di pagina coperto, 19 ott 2026).
        $dentro     = $flip_v;
        $flip_y     = ( $position === 'bottom' ) ? ! $flip_v : $flip_v;
        $transforms = [];
        if ( $flip_h ) {
            $transforms[] = 'scaleX(-1)';
        }
        if ( $flip_y ) {
            $transforms[] = 'scaleY(-1)';
        }
        $transform_css = ! empty( $transforms ) ? 'transform:' . implode( ' ', $transforms ) . ';' : '';

        // Bordo sinistro SEMPRE dichiarato: senza, un divisore largo 100% partiva
        // dalla sua posizione statica (dentro il padding del genitore) e
        // sporgeva a destra lasciando un gradino a sinistra. Oltre il 100% si
        // centra.
        $left_offset = 'left:0;';
        if ( $width > 100 ) {
            $offset_pct = -( $width - 100 ) / 2;
            $left_offset = 'left:' . $offset_pct . '%;';
        }

        $uid = 'olo-sd-' . wp_rand( 100000, 999999 );

        // Ingresso dei livelli: solo con più di un livello (la forma piena resta ferma, è il bordo della sezione).
        $anim = ( $livelli > 1 && in_array( $s['layers_anim'], [ 'fade', 'fade-up', 'fade-down', 'fade-left', 'fade-right' ], true ) ) ? $s['layers_anim'] : '';

        $svg = $this->disegno( $s, $uid, $forme, $shape, $livelli, $color, $flip_h, $flip_y, $anim, $seme );

        // Altezza per dispositivo (selettore del dispositivo: height_tablet_landscape … height_mobile). Le misure
        // salvate prima nei campi «Altezza tablet» (fino a 959 px) e «Altezza mobile» (fino a 639 px) valgono
        // finché la chiave nuova del loro dispositivo è vuota: l'inspector le mostra lì e, scrivendo, le svuota.
        // In ordine di larghezza decrescente, perché fra due regole valide vinca la più stretta.
        $per_disp = [];
        $leggi    = function ( $k ) use ( $s ) {
            return isset( $s[ $k ] ) ? trim( (string) $s[ $k ] ) : '';
        };
        if ( '' !== $leggi( 'height_tablet_landscape' ) ) {
            $per_disp[] = [ 1200, $leggi( 'height_tablet_landscape' ) ];
        }
        if ( '' !== $leggi( 'height_tablet' ) ) {
            $per_disp[] = [ 960, $leggi( 'height_tablet' ) ];
        } elseif ( '' !== $leggi( 'responsive_height_tablet' ) ) {
            $per_disp[] = [ 959, $leggi( 'responsive_height_tablet' ) ];
        }
        if ( '' !== $leggi( 'height_mobile_landscape' ) ) {
            $per_disp[] = [ 640, $leggi( 'height_mobile_landscape' ) ];
        } elseif ( '' !== $leggi( 'responsive_height_mobile' ) ) {
            $per_disp[] = [ 639, $leggi( 'responsive_height_mobile' ) ];
        }
        if ( '' !== $leggi( 'height_mobile' ) ) {
            $per_disp[] = [ 480, $leggi( 'height_mobile' ) ];
        }

        ob_start();
        ?>
        <?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above: intval()-clamped sizes, the 'top'/'bottom' position ternary, fixed scaleX/scaleY transform literals, a float offset computed from the clamped width, whitelisted animation keyframes and the internally generated $uid. ?>
        <style>
            .<?php echo $uid; ?> {
                position: absolute;
                <?php echo $position; ?>: 0;
                <?php echo $left_offset; ?>
                width: <?php echo (int) $width; ?>%;
                z-index: <?php echo (int) $z_index; ?>;
                line-height: 0;
                pointer-events: none;
                overflow: hidden;
            }
            .<?php echo $uid; ?> svg {
                display: block;
                width: 100%;
                height: <?php echo (int) $height; ?>px;
                <?php echo $transform_css; ?>
            }
            <?php
            /*
             * UN PIXEL DI SOVRAPPOSIZIONE, e non e' un ritocco estetico.
             *
             * Con lo spostamento pieno il bordo del divisore e il bordo della
             * sua sezione cadono sulla stessa coordinata. Quella coordinata e'
             * quasi sempre frazionaria, perche' dipende dall'altezza di tutto
             * quello che c'e' sopra: il browser arrotonda le due cose per
             * conto proprio, e resta una riga di pixel che non dipinge ne' il
             * divisore ne' la sezione. Li' si vede quello che c'e' sotto, che
             * di solito e' il bianco del body.
             *
             * Misurato il 7 settembre 2026 su olotutor.clod.eu: cucitura a
             * 697,11 pixel, sezione sopra trasparente, riga bianca larga
             * quanto la pagina.
             *
             * Un pixel non si vede (il divisore e' alto ottanta) e la cucitura
             * sparisce, perche' i due elementi si sovrappongono invece di
             * sfiorarsi. Specchiato, il divisore sta dentro la sua sezione e il
             * pixel sconfina nella vicina.
             */
            ?>
            <?php if ( $position === 'bottom' ) : ?>
            .<?php echo $uid; ?> {
                transform: translateY(<?php echo $dentro ? '1px' : 'calc(100% - 1px)'; ?>);
            }
            <?php else : ?>
            .<?php echo $uid; ?> {
                transform: translateY(<?php echo $dentro ? '-1px' : 'calc(-100% + 1px)'; ?>);
            }
            <?php endif; ?>
            <?php foreach ( $per_disp as $disp ) : ?>
            @media (max-width: <?php echo (int) $disp[0]; ?>px) {
                .<?php echo $uid; ?> svg {
                    height: <?php echo max( 10, intval( $disp[1] ) ); ?>px;
                }
            }
            <?php endforeach; ?>
            <?php echo $this->css_ingresso( $s, $uid, $anim, $flip_h, $flip_y ); ?>
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <div class="olo-shapedivider <?php echo esc_attr( $uid ); ?>" aria-hidden="true"<?php echo '' !== $anim ? ' uk-scrollspy="target: .olo-sd-anim; cls: olo-sd-in"' : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed literal attribute ?>>
            <?php echo $svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG built by disegno(): path data from the plugin's own shapes file or computed numbers, colours through safe_color_css() + esc_attr(), internal ids ?>
        </div>
        <?php
        if ( 'dinamica' === $shape ) {
            // Le onde vive (SMIL) si fermano per chi chiede meno movimento al sistema e quando il divisore
            // è fuori dallo schermo. Niente && né confronti: WordPress li rovina negli script in linea.
            echo '<script>(function(){var w=document.querySelector(".' . esc_js( $uid ) . '");if(!w){return;}var v=w.querySelector("svg");if(!v){return;}if(!v.pauseAnimations){return;}'
                . 'if(window.matchMedia){if(matchMedia("(prefers-reduced-motion: reduce)").matches){v.pauseAnimations();return;}}'
                . 'if(!("IntersectionObserver" in window)){return;}'
                . 'new IntersectionObserver(function(es){es.forEach(function(e){if(e.isIntersecting){v.unpauseAnimations();}else{v.pauseAnimations();}});}).observe(w);'
                . '})();</script>';
        }
                // Border system
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) echo ".{$uid}{{$border_css}}"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_css() (integer-forced widths) for the internally generated uid
            echo $border_hover_css . $border_effect_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_hover_css()/build_border_effect_css() shared helpers
        }
        return ob_get_clean();
    }

    /**
     * Il disegno: dal livello più arretrato a quello pieno. I livelli in più sono la stessa forma,
     * schiacciata nella parte bassa del riquadro per fare posto e spostata verso l'esterno di un passo
     * per livello; sotto ognuno un rettangolo la porta fino al bordo della sezione, così dietro la
     * forma piena non resta uno spiraglio. Con «Dinamica» ogni livello è un'onda sua che si muove.
     */
    private function disegno( $s, $uid, $forme, $shape, $livelli, $color, $flip_h, $flip_y, $anim, $seme ) {
        $gap   = max( 5, min( 30, floatval( $s['layer_gap'] ) ) );
        // Il passo fra un livello e l'altro, nel riquadro alto 120: in tutto al massimo il 60% dell'altezza.
        $passo = $livelli > 1 ? min( self::H * $gap / 100, self::H * 0.6 / ( $livelli - 1 ) ) : 0;

        // Sfumatura. Il divisore si ribalta con CSS («Basso» lo capovolge, «Specchia» lo gira): la direzione
        // si corregge perché sullo schermo resti quella scelta (90° = da sinistra a destra, come in CSS).
        $sfuma  = ! empty( $s['gradient'] );
        $fine   = $this->safe_color_css( $s['color_end'] );
        $angolo = intval( $s['gradient_angle'] );
        if ( $flip_y ) {
            $angolo = 180 - $angolo;
        }
        if ( $flip_h ) {
            $angolo = 360 - $angolo;
        }
        $ruota = ( ( ( $angolo - 90 ) % 360 ) + 360 ) % 360;
        $defs  = '';
        $tinta = function ( $c, $n ) use ( &$defs, $sfuma, $fine, $uid, $ruota ) {
            if ( ! $sfuma ) {
                return $c;
            }
            $id = $uid . '-s' . $n;
            // Senza colore finale il colore svanisce: la fermata finale è lo stesso colore trasparente
            // (verso «transparent», cioè nero trasparente, a metà strada si vedrebbe del grigio).
            $stop2   = '' !== $fine ? 'stop-color:' . $fine : 'stop-color:' . $c . ';stop-opacity:0';
            $defs   .= '<linearGradient id="' . esc_attr( $id ) . '" x1="0" y1="0" x2="1" y2="0"'
                . ( $ruota ? ' gradientTransform="rotate(' . $ruota . ' .5 .5)"' : '' ) . '>'
                . '<stop offset="0" style="' . esc_attr( 'stop-color:' . $c ) . '"/>'
                . '<stop offset="1" style="' . esc_attr( $stop2 ) . '"/></linearGradient>';
            return 'url(#' . $id . ')';
        };

        $sopra  = ( $livelli - 1 ) * $passo;          // lo spazio lasciato ai livelli in più
        $scala  = ( self::H - $sopra ) / self::H;
        $pieno  = $tinta( $color, 1 );
        $strati = '';
        for ( $k = $livelli; $k >= 1; $k-- ) {
            $proprio = 1 === $k ? '' : $this->safe_color_css( $s[ 'layer' . $k . '_color' ] ?? '' );
            $pittura = 1 === $k ? $pieno : ( '' !== $proprio ? $tinta( $proprio, $k ) : $pieno );
            // Senza un colore suo, il livello è la forma piena trasparente al 60% (2) e al 30% (3).
            $opacita = ( 1 === $k || '' !== $proprio ) ? '' : ( 2 === $k ? ' opacity="0.6"' : ' opacity="0.3"' );
            if ( 'dinamica' === $shape ) {
                $corpo = $this->onda_viva( $s, $k, $livelli, $passo, $seme );
            } else {
                $sposta = ( $k - 1 ) * $passo;
                $dy     = $sopra - $sposta;
                $tr     = ( abs( $dy ) > 0.001 || abs( $scala - 1 ) > 0.0001 )
                    ? ' transform="translate(0 ' . self::num( $dy ) . ') scale(1 ' . self::num( $scala, 4 ) . ')"' : '';
                $corpo  = '<path d="' . esc_attr( $forme[ $shape ] ) . '"' . $tr . '/>';
                if ( $sposta > 0 ) {
                    $corpo .= '<rect x="0" y="' . self::num( self::H - $sposta - 1 ) . '" width="' . self::W . '" height="' . self::num( $sposta + 1 ) . '"/>';
                }
            }
            $g = '<g style="' . esc_attr( 'fill:' . $pittura ) . '"' . $opacita . '>' . $corpo . '</g>';
            // Il gruppo che entra animato ne avvolge un altro: l'animazione usa transform, che in SVG
            // sostituirebbe lo spostamento del livello se stessero sullo stesso elemento.
            $strati .= ( $k > 1 && '' !== $anim ) ? '<g class="olo-sd-anim olo-sd-a' . $k . '">' . $g . '</g>' : $g;
        }

        return '<svg preserveAspectRatio="none" viewBox="0 0 ' . self::W . ' ' . self::H . '" xmlns="http://www.w3.org/2000/svg" focusable="false">'
            . ( '' !== $defs ? '<defs>' . $defs . '</defs>' : '' ) . $strati . '</svg>';
    }

    /**
     * Le regole dell'ingresso animato dei livelli. UIkit (scrollspy, sempre caricato con le pagine
     * Olobuild) li nasconde finché il divisore non arriva nello schermo, poi aggiunge olo-sd-in: senza
     * il suo script i livelli restano semplicemente visibili. Le direzioni sono quelle dello schermo:
     * col divisore ribaltato (Basso, Specchia) si invertono nel disegno.
     */
    private function css_ingresso( $s, $uid, $anim, $flip_h, $flip_y ) {
        if ( '' === $anim ) {
            return '';
        }
        if ( $flip_y ) {
            $anim = strtr( $anim, [ 'fade-up' => 'fade-down', 'fade-down' => 'fade-up' ] );
        }
        if ( $flip_h ) {
            $anim = strtr( $anim, [ 'fade-left' => 'fade-right', 'fade-right' => 'fade-left' ] );
        }
        // Nel riquadro 1200 × 120: un quarto dell'altezza in verticale, un decimo della larghezza in orizzontale.
        $da = [
            'fade'       => '',
            'fade-up'    => 'transform:translateY(30px);',
            'fade-down'  => 'transform:translateY(-30px);',
            'fade-left'  => 'transform:translateX(-120px);',
            'fade-right' => 'transform:translateX(120px);',
        ][ $anim ];
        $durata  = max( 200, min( 4000, intval( $s['layers_anim_duration'] ) ) );
        $ritardo = max( 0, min( 2000, intval( $s['layers_anim_delay'] ) ) );
        return '@keyframes ' . $uid . '-in{from{opacity:0;' . $da . '}to{opacity:1;transform:none}}'
            . '.' . $uid . ' .olo-sd-in{animation:' . $uid . '-in ' . $durata . 'ms cubic-bezier(.22,.61,.36,1) both}'
            . '.' . $uid . ' .olo-sd-a2.olo-sd-in{animation-delay:' . $ritardo . 'ms}'
            . '.' . $uid . ' .olo-sd-a3.olo-sd-in{animation-delay:' . ( 2 * $ritardo ) . 'ms}'
            . '@media (prefers-reduced-motion:reduce){.' . $uid . ' .olo-sd-in{animation:none}}';
    }

    /**
     * Un livello della forma «Dinamica»: quattro onde diverse fra cui il tracciato si muove senza fine
     * (SMIL, nessuno script). I livelli dietro hanno un ciclo più lungo, così non si muovono mai insieme.
     */
    private function onda_viva( $s, $k, $livelli, $passo, $seme ) {
        $onde   = max( 1, min( 6, intval( $s['wave_count'] ) ) );
        $amp    = max( 10, min( 100, intval( $s['wave_amplitude'] ) ) );
        $durata = max( 3, min( 40, floatval( $s['wave_duration'] ) ) );
        $fot    = [];
        for ( $f = 0; $f < 4; $f++ ) {
            $fot[] = self::onda( $k, $livelli, $onde, $amp, $passo, $seme, $f );
        }
        $fot[]  = $fot[0];
        $lenti  = [ 1, 1.27, 1.61 ];
        $spline = implode( ';', array_fill( 0, 4, '0.45 0 0.55 1' ) );
        return '<path d="' . $fot[0] . '"><animate attributeName="d" dur="' . self::num( $durata * $lenti[ $k - 1 ] ) . 's" repeatCount="indefinite"'
            . ' calcMode="spline" keyTimes="0;0.25;0.5;0.75;1" keySplines="' . $spline . '" values="' . implode( ';', $fot ) . '"/></path>';
    }

    /**
     * Un fotogramma di un'onda: 2 × onde segmenti con tangenti orizzontali nei punti, così ogni punto
     * è un colmo o un cavo. Ogni punto resta colmo o cavo (i livelli si sfalsano di uno), ma da un
     * fotogramma all'altro cambia altezza e scivola un po' di lato: le creste si spostano invece di
     * pulsare sul posto. Il livello pieno sta più in basso, gli altri salgono di un passo ciascuno; in
     * fondo resta sempre una fascia piena (6 su 120) che copre il bordo della sezione.
     */
    private static function onda( $k, $livelli, $onde, $amp, $passo, $seme, $f ) {
        $n     = 2 * $onde;
        $base1 = ( self::H - 6 + ( $livelli - 1 ) * $passo ) / 2;
        $amax  = ( self::H - 6 - ( $livelli - 1 ) * $passo ) / 2;
        $a     = $amax * $amp / 100;
        $base  = $base1 - ( $k - 1 ) * $passo;
        $punti = [];
        for ( $i = 0; $i <= $n; $i++ ) {
            $segno   = ( ( $i + $k ) % 2 ) ? 1 : -1;
            $v       = $segno * ( 0.3 + 0.7 * self::caso( $i, $f, $k, $seme ) );
            $dx      = ( $i > 0 && $i < $n ) ? ( self::caso( $i + 50, $f, $k, $seme ) - 0.5 ) * 0.55 * ( self::W / $n ) : 0;
            $punti[] = [ self::W * $i / $n + $dx, $base + $a * $v ];
        }
        $d = 'M0,' . self::num( $punti[0][1] );
        for ( $i = 1; $i <= $n; $i++ ) {
            $x0 = $punti[ $i - 1 ][0];
            $y0 = $punti[ $i - 1 ][1];
            $x1 = $punti[ $i ][0];
            $y1 = $punti[ $i ][1];
            $m  = ( $x1 - $x0 ) / 2;
            $d .= ' C' . self::num( $x0 + $m ) . ',' . self::num( $y0 ) . ' ' . self::num( $x1 - $m ) . ',' . self::num( $y1 ) . ' ' . self::num( $x1 ) . ',' . self::num( $y1 );
        }
        return $d . ' L' . self::W . ',' . self::H . ' L0,' . self::H . ' Z';
    }

    /** Un numero «a caso» ma sempre lo stesso per gli stessi argomenti (0 ≤ x < 1). */
    private static function caso( $i, $f, $k, $seme ) {
        $x = sin( $i * 12.9898 + $f * 78.233 + $k * 37.719 + $seme * 0.0137 ) * 43758.5453;
        return $x - floor( $x );
    }

    /** Un numero corto per i tracciati: al massimo $cifre decimali, senza zeri in coda. */
    private static function num( $n, $cifre = 2 ) {
        $t = number_format( round( (float) $n, $cifre ), $cifre, '.', '' );
        $t = rtrim( rtrim( $t, '0' ), '.' );
        return ( '' === $t || '-0' === $t ) ? '0' : $t;
    }
}
