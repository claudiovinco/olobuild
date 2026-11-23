<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * OLOX Scena minigioco — la SOLA parte custom della Home Experience ricostruita
 * con tile classiche: una scena interattiva (forza-4, acchiappa-imprevisti,
 * indovina-lingua, radar, oblò 360°, quiz XP, mad-lib) da mettere in una colonna
 * di una sezione normale. Il resto della fermata (kicker, titolo, testo, badge,
 * pulsanti) si compone con le tile standard di olobuild; lo scorrimento
 * orizzontale lo fa la sezione nativa (Stile → Sticky → "Cover orizzontale").
 *
 * Le scene (decorazioni e markup) stanno qui: le tile OLOX Pannello e Hero che le condividevano sono state tolte il
 * 23 nov 2026; runtime in
 * assets/js/olox.js (modulo "scene" = alias del modulo "home" senza rail).
 */
class Olobuild_OloxScene_Tile extends Olobuild_Olox_Base_Tile {

    protected $type     = 'oloxscene';
    protected $name     = 'Scena minigioco';
    protected $icon     = 'dashicons-games';
    protected $category = 'interactive';
    protected $defaults = [
        'scene'     => 'wall', // wall | cal | lang | radar | pano | course | madlib
        'color'     => 'build',
        'coord'     => 'grid · 44×44 · lot 187',
        'show_deco' => true,
    ];

    public function __construct() {
        // Default mad-lib da fonte unica (scena "madlib").
        $this->defaults = array_merge( $this->defaults, self::madlib_defaults() );
        // Default delle scene showcase hero-* (erano della tile OLOX Hero, tolta il 23 nov 2026).
        $this->defaults = array_merge( $this->defaults, [
            'wall_count'    => 187,
            'wall_label'    => 'tile / 187',
            'clock_label'   => 'lo scroll muove le lancette',
            'console_title' => 'translator',
            'console_sub'   => '· dashboard · batch in corso',
            'console_rows'  => [
                [ 'lc' => 'EN', 'w' => 100 ], [ 'lc' => 'DE', 'w' => 100 ], [ 'lc' => 'FR', 'w' => 96 ],
                [ 'lc' => 'ES', 'w' => 92 ], [ 'lc' => 'PT', 'w' => 84 ], [ 'lc' => 'NL', 'w' => 78 ],
                [ 'lc' => 'JA', 'w' => 64 ], [ 'lc' => '+21', 'w' => 52, 'pc' => '…' ],
            ],
            'term_title'    => 'sentinel',
            'term_sub'      => '· boot sequence',
            'term_lines'    => [],
            'medal_top'     => 'livello',
            'medal_big'     => '1',
            'medal_bot'     => 'studente',
        ] );
    }

    public function render( $settings, $style = [] ) {
        $s = wp_parse_args( $settings, $this->defaults );
        $this->olox_assets();
        $accent = $this->olox_color( $s['color'] ?? 'olo' );
        $scene  = (string) ( $s['scene'] ?? 'wall' );

        ob_start();
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        // background:transparent — dentro le sezioni classiche lo sfondo (halo
        // glow compreso) lo porta la sezione, non il design system .oloxp.
        // contain:inline-size — le scene hanno track/parole width:max-content
        // (langflow ~3000px): senza contain la larghezza intrinseca si propaga
        // ai contenitori fit-content (sezioni flex) e su mobile la fermata
        // non va più a capo (stesso gotcha del marquee).
        echo $this->olox_open( 'oloxp-home oloxp-scene', '--c:' . $accent . '; position:relative; background:transparent; contain:inline-size;' );
        echo '<div data-olox="scene">';
        if ( 'madlib' === $scene ) {
            echo $this->olox_madlib( $s ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- helper interno già escapato.
        } elseif ( 0 === strpos( $scene, 'hero-' ) ) {
            // Scene showcase delle pagine prodotto.
            if ( ! empty( $s['show_deco'] ) ) {
                echo self::scene_deco( $scene, $s['coord'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- helper interno già escapato.
            }
            echo $this->scena_prodotto( $s, substr( $scene, 5 ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- helper interno già escapato.
        } else {
            if ( ! empty( $s['show_deco'] ) ) {
                echo self::scene_deco( $scene, $s['coord'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- helper interno già escapato.
            }
            echo self::scene_markup( $scene ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- helper interno già escapato.
        }
        echo '</div>';
        echo $this->olox_close(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        return ob_get_clean();
    }

    /** Decorazioni vettoriali per scena (posizioni fedeli al sorgente). */
    public static function scene_deco( $scene, $coord ) {
        $c = esc_html( $coord );
        switch ( $scene ) {
            case 'wall':
                return '<div class="deco d-ring c spin" style="width:46vh; height:46vh; right:-10vh; top:-16vh;"></div>'
                    . '<div class="deco d-cross" style="left:6%; bottom:18%;"></div>'
                    . '<div class="deco d-coord" style="left:6%; bottom:13%;">' . $c . '</div>'
                    . '<div class="deco d-bar" style="left:6%; top:14%; width:120px;"></div>';
            case 'cal':
                return '<div class="deco d-ring c" style="width:34vh; height:34vh; left:-8vh; bottom:-10vh;"></div>'
                    . '<div class="deco d-dots" style="right:8%; top:12%; width:130px; height:74px;"></div>'
                    . '<div class="deco d-coord" style="right:8%; top:9%;">' . $c . '</div>';
            case 'lang':
                return '<div class="deco d-ring c spin" style="width:52vh; height:52vh; left:38%; top:-22vh; animation-duration:90s;"></div>'
                    . '<div class="deco d-cross" style="right:7%; bottom:16%;"></div>'
                    . '<div class="deco d-coord" style="right:7%; bottom:11%;">' . $c . '</div>';
            case 'radar':
                return '<div class="deco d-dots" style="left:5%; top:14%; width:110px; height:110px;"></div>'
                    . '<div class="deco d-coord" style="left:5%; top:10%;">' . $c . '</div>'
                    . '<div class="deco d-ring c" style="width:30vh; height:30vh; right:-6vh; bottom:-8vh;"></div>';
            case 'pano':
                return '<div class="deco d-ring c spin" style="width:60vh; height:60vh; right:-18vh; bottom:-24vh; animation-duration:120s;"></div>'
                    . '<div class="deco d-cross" style="left:6%; top:16%;"></div>'
                    . '<div class="deco d-coord" style="left:6%; top:11%;">' . $c . '</div>';
            case 'course':
                return '<div class="deco d-ring c" style="width:38vh; height:38vh; left:-10vh; top:-10vh;"></div>'
                    . '<div class="deco d-dots" style="right:6%; bottom:14%; width:120px; height:80px;"></div>'
                    . '<div class="deco d-coord" style="right:6%; bottom:10%;">' . $c . '</div>';
        }
        return '';
    }

    /** Scene-minigioco (markup fedele; runtime in olox.js modulo "home"). */
    public static function scene_markup( $scene ) {
        switch ( $scene ) {
            case 'wall':
                return '<div class="scene" data-fx="wall">'
                    . '<div style="position:relative; width:min(100%,560px);">'
                    . '<div class="crane"></div>'
                    . '<div class="wall"></div>'
                    . '<div class="wfoot">'
                    . '<div class="wstat"><b>Costruisci il sito perfetto</b> · posiziona le tile · 4 in fila vince</div>'
                    . '<button class="wreset" type="button">↺ nuova partita</button>'
                    . '</div></div></div>';
            case 'cal':
                return '<div class="scene" data-fx="cal">'
                    . '<div style="position:relative; width:min(100%,520px);">'
                    . '<div class="cal">'
                    . '<div class="head"><span>TURNO DI PROVA · <b>imprevisti in arrivo</b></span><span class="ox-gtimer">02:00</span></div>'
                    . '<div class="arena"></div>'
                    . '<div class="foot"><span>gestiti dal motore <b class="win ox-hit">0</b></span><span>sfuggiti <b class="ox-miss">0</b></span></div>'
                    . '</div>'
                    . '<div class="stub">Prenotato<b>Tavolo 12 · h 20:30</b></div>'
                    . '</div></div>';
            case 'lang':
                return '<div class="scene" data-fx="lang">'
                    . '<div class="langbox">'
                    . '<div class="lcode">che lingua è? · punti <b class="ox-lscore">0</b> · <b class="ox-ltime">01:00</b></div>'
                    . '<div class="hello">«<span class="cur ox-hello">Benvenuto</span>»</div>'
                    . '<div class="langflow"><span class="in">Welcome <em>·</em> Willkommen <em>·</em> Bienvenue <em>·</em> Bienvenido <em>·</em> Bem-vindo <em>·</em> Welkom <em>·</em> Καλώς ήρθες <em>·</em> Добро пожаловать <em>·</em> ようこそ <em>·</em> 欢迎 <em>·</em> Welcome <em>·</em> Willkommen <em>·</em> Bienvenue <em>·</em> Bienvenido <em>·</em> Bem-vindo <em>·</em> Welkom <em>·</em> Καλώς ήρθες <em>·</em> Добро пожаловать <em>·</em> ようこそ <em>·</em> 欢迎 <em>·</em></span></div>'
                    . '<div class="langflow rev"><span class="in">hreflang <em>·</em> /en/ <em>·</em> /de/ <em>·</em> /fr/ <em>·</em> sitemap.xml <em>·</em> glossario <em>·</em> memoria di traduzione <em>·</em> dashboard translator <em>·</em> hreflang <em>·</em> /en/ <em>·</em> /de/ <em>·</em> /fr/ <em>·</em> sitemap.xml <em>·</em> glossario <em>·</em> memoria di traduzione <em>·</em> dashboard translator <em>·</em></span></div>'
                    . '<div class="langpicks"></div>'
                    . '</div></div>';
            case 'radar':
                return '<div class="scene" data-fx="radar">'
                    . '<div class="radarwrap">'
                    . '<div class="radar">'
                    . '<div class="shieldring"></div>'
                    . '<div class="cross"></div>'
                    . '<div class="sweep"></div>'
                    . '<div class="radhud">02:00 · intercettati <b>0</b> · violazioni <b>0</b></div>'
                    . '</div>'
                    . '<div class="seclog">'
                    . '<div style="--d:.05s"><span class="cy">[waf]</span> SQLi da 185.220.•.• <span class="bad">BLOCCATO</span></div>'
                    . '<div style="--d:.2s"><span class="cy">[waf]</span> XSS payload <span class="bad">BLOCCATO</span></div>'
                    . '<div style="--d:.35s"><span class="cy">[2fa]</span> login admin +TOTP <span class="ok">OK</span></div>'
                    . '<div style="--d:.5s"><span class="cy">[bot]</span> finto Googlebot (FCrDNS) <span class="bad">RESPINTO</span></div>'
                    . '<div style="--d:.65s"><span class="cy">[scan]</span> core checksum 100% <span class="ok">INTEGRO</span></div>'
                    . '<div style="--d:.8s"><span class="cy">[cve]</span> feed firme aggiornato <span class="ok">SYNC</span></div>'
                    . '<div style="--d:.95s"><span class="cy">[geo]</span> rate-limit attivo <span class="ok">ARMATO</span></div>'
                    . '</div></div></div>';
            case 'pano':
                return '<div class="scene" data-fx="pano">'
                    . '<div style="position:relative;">'
                    . '<div class="porthole">'
                    . '<div class="vista sky"></div>'
                    . '<div class="vista far"></div>'
                    . '<div class="vista near"></div>'
                    . '<span class="spot ox-spot-a" style="top:38%; left:30%;"></span>'
                    . '<span class="spot ox-spot-b" style="top:58%; left:66%; animation-delay:.9s;"></span>'
                    . '</div>'
                    . '<div class="compass">N ─ E ─ S ─ O</div>'
                    . '</div></div>';
            case 'course':
                return '<div class="scene" data-fx="course">'
                    . '<div class="course" style="position:relative;">'
                    . '<div class="badge">livello<b class="ox-tqlvl">01</b>studente</div>'
                    . '<div class="xphead"><span>corso · conosci OLOtheme</span><b><span class="ox-xp">0</span> XP</b></div>'
                    . '<div class="xpbar"><i></i></div>'
                    . '<div class="tquiz">'
                    . '<div class="tq-q">… <span class="tq-slot">trascina qui</span></div>'
                    . '<div class="tq-chips"></div>'
                    . '<div class="tq-stat">trascina la risposta giusta nello spazio · +60 xp</div>'
                    . '</div></div></div>';
        }
        return '<div class="scene"></div>';
    }

    /**
     * Markup delle scene showcase (wall/clock/console/term/porthole/medal).
     * Erano della tile OLOX Hero, tolta il 23 nov 2026: restano qui, unico posto che le usa.
     */
    private function scena_prodotto( $s, $scene ) {
        if ( 'none' === $scene ) { return '<div></div>'; }
        ob_start();
        ?>
        <div style="display:flex; justify-content:center;<?php echo 'clock' === $scene ? ' padding-bottom:50px;' : ''; ?>">
        <?php
        switch ( $scene ) {
            case 'wall':
                ?>
                <div data-olox="hwall" style="width:min(100%,520px);">
                    <div class="ox-hwall" data-cells="84">
                        <div class="count"><b data-count="<?php echo (int) $s['wall_count']; ?>">0</b><?php echo esc_html( $s['wall_label'] ); ?></div>
                    </div>
                </div>
                <?php
                break;
            case 'clock':
                ?>
                <div class="clockface">
                    <div class="hand h2"></div>
                    <div class="hand hm"></div>
                    <div class="pin"></div>
                    <div class="clocklbl"><b>08:00</b><?php echo esc_html( $s['clock_label'] ); ?></div>
                </div>
                <?php
                break;
            case 'console':
                ?>
                <div class="console" data-olox="consolego">
                    <div class="cbar"><b><?php echo esc_html( $s['console_title'] ); ?></b><span><?php echo esc_html( $s['console_sub'] ); ?></span></div>
                    <div class="rows">
                        <?php $d = 0.1; foreach ( $this->olox_items( $s, 'console_rows' ) as $r ) :
                            $w  = max( 0, min( 100, intval( $r['w'] ?? 0 ) ) );
                            $pc = isset( $r['pc'] ) && '' !== $r['pc'] ? $r['pc'] : $w . '%';
                            ?>
                        <div class="crow"><span class="lc"><?php echo esc_html( $r['lc'] ?? '' ); ?></span><span class="bar"><i style="--w:<?php echo (int) $w; ?>%; --d:<?php echo esc_attr( number_format( $d, 2, '.', '' ) ); ?>s"></i></span><span class="pc"><?php echo esc_html( $pc ); ?></span></div>
                        <?php $d += 0.15; endforeach; ?>
                    </div>
                </div>
                <?php
                break;
            case 'term':
                $lines = [];
                foreach ( $this->olox_items( $s, 'term_lines' ) as $l ) {
                    $lines[] = [ (string) ( $l['cls'] ?? 'cy' ), (string) ( $l['text'] ?? '' ) ];
                }
                ?>
                <div class="term" data-olox="term" data-lines="<?php echo esc_attr( wp_json_encode( $lines ) ); ?>">
                    <div class="tbar"><b><?php echo esc_html( $s['term_title'] ); ?></b><span><?php echo esc_html( $s['term_sub'] ); ?></span></div>
                    <pre></pre>
                </div>
                <?php
                break;
            case 'porthole':
                ?>
                <div class="ox-porthole">
                    <div class="pano"></div>
                    <span class="spot" style="top:38%; left:30%;"></span>
                    <span class="spot" style="top:58%; left:66%; animation-delay:.9s;"></span>
                </div>
                <?php
                break;
            case 'medal':
                ?>
                <div class="medal">
                    <div class="orbit"><i></i></div>
                    <div class="inner"><?php echo esc_html( $s['medal_top'] ); ?><b><?php echo esc_html( $s['medal_big'] ); ?></b><?php echo esc_html( $s['medal_bot'] ); ?></div>
                </div>
                <?php
                break;
        }
        ?>
        </div>
        <?php
        return ob_get_clean();
    }
}
