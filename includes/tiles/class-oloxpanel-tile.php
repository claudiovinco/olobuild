<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * OLOX Panel — UNA fermata della Home Experience, da usare una per sezione:
 * nel builder la home è composta da tante sezioni quante sono le fermate
 * (intro, un prodotto per pannello coi minigiochi, finale mad-lib) e la tile
 * OLOX Rail le raccoglie a runtime nel binario orizzontale.
 *
 * Varianti: intro (headline gigante + marquee), product (copy + scena-minigioco),
 * outro (capolinea + form mad-lib). Le scene/decorazioni sono statiche qui e
 * riusate anche dalla tile monolitica oloxhome.
 */
class Olobuild_OloxPanel_Tile extends Olobuild_Olox_Base_Tile {

    protected $type     = 'oloxpanel';
    protected $name     = 'OLOX — Fermata Experience (pannello)';
    protected $icon     = 'dashicons-slides';
    protected $category = 'marketing';
    protected $defaults = [
        'variant'      => 'product',
        'label'        => 'OLObuild',
        // intro
        'intro_kicker' => 'OLOtheme · suite WordPress',
        'olw_text'     => 'olonica',
        'intro_title'  => 'Un telaio. Sei prodotti. <em>Nessuna catena.</em>',
        'intro_sub'    => 'Niente SaaS, niente lock-in, niente cloud altrui: tutto vive <strong>sul tuo hosting</strong>, in GPL, scritto a Trento. Scorri: ogni fermata è un prodotto.',
        'intro_cta1'   => 'Inizia il viaggio →',
        'intro_cta2'   => 'Contatti',
        'marquee_items' => [
            [ 'text' => 'no SaaS' ], [ 'text' => 'GPL' ], [ 'text' => '187 tile' ], [ 'text' => '28 lingue' ],
            [ 'text' => '6 verticali booking' ], [ 'text' => '100% locale' ], [ 'text' => 'made in Trento' ],
        ],
        // product
        'color'        => 'build',
        'logo'         => '',
        'kicker'       => 'Il telaio · page builder',
        'title_html'   => 'Costruisce come un <em>cantiere</em>',
        'sub_html'     => 'Mattone su mattone: <strong>187 tile in 12 famiglie</strong>, tutti auto-discovered.',
        'tags'         => '€0 free · 100+ tile|36 animazioni|Woo nativo|dark mode',
        'cta_text'     => 'Entra nel cantiere',
        'cta_url'      => '/olobuild/',
        'scene'        => 'wall',
        'coord'        => 'grid · 44×44 · lot 187',
        // outro
        'outro_kicker' => 'Capolinea · si scende',
        'outro_title'  => 'Tutto questo, <em>sul tuo hosting</em>',
        'outro_sub'    => 'GPL · niente SaaS · GDPR in casa · 30 giorni di rimborso su OLObuild Pro. Ogni fermata ha la sua pagina di approfondimento.',
        'outro_links'  => [
            [ 'label' => 'OLObuild', 'url' => '/olobuild/', 'color' => 'build' ],
            [ 'label' => 'OLObooking', 'url' => '/olobooking/', 'color' => 'booking' ],
            [ 'label' => 'OLOlang', 'url' => '/ololang/', 'color' => 'lang' ],
            [ 'label' => 'OLOsecurity', 'url' => '/olosecurity/', 'color' => 'secur' ],
            [ 'label' => 'OLOtour', 'url' => '/olotour/', 'color' => 'tour' ],
            [ 'label' => 'OLOtutor', 'url' => '/olotutor/', 'color' => 'tutor' ],
        ],
        'outro_fine'   => 'OLOtheme · made in Trento · no SaaS · nessuna catena',
    ];

    public function __construct() {
        // Default mad-lib da fonte unica (condivisi con la tile oloxscene).
        $this->defaults = array_merge( $this->defaults, self::madlib_defaults() );
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

    public function render( $settings, $style = [] ) {
        $s = wp_parse_args( $settings, $this->defaults );
        $this->olox_assets();
        $variant = in_array( $s['variant'], [ 'intro', 'product', 'outro' ], true ) ? $s['variant'] : 'product';

        ob_start();
        // Il wrapper porta anche il marker olox-panel-src: la tile OLOX Rail
        // raccoglie i .panel a runtime e li monta nel binario orizzontale.
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo $this->olox_open( 'oloxp-home olox-panel-src' );
        if ( 'intro' === $variant ) :
            ?>
            <section class="panel intro" data-screen-label="<?php echo esc_attr( $s['label'] ?: 'Intro' ); ?>" style="--c:var(--olo)">
                <div class="inner">
                    <div>
                        <div class="k"><?php echo esc_html( $s['intro_kicker'] ); ?> <button class="olw" type="button"><?php echo esc_html( $s['olw_text'] ); ?></button></div>
                        <h1><?php echo $this->olox_rich( $s['intro_title'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h1>
                        <p class="sub" style="max-width:52ch;"><?php echo $this->olox_rich( $s['intro_sub'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
                        <div style="display:flex; gap:12px; flex-wrap:wrap;">
                            <a class="cta" href="#" data-go="1"><?php echo esc_html( $s['intro_cta1'] ); ?></a>
                            <a class="cta ghost" href="#" data-go="-1"><?php echo esc_html( $s['intro_cta2'] ); ?></a>
                        </div>
                    </div>
                </div>
                <div class="marq"><span class="in"><?php
                    $mline = '';
                    foreach ( $this->olox_items( $s, 'marquee_items' ) as $mi ) {
                        $tt = trim( (string) ( $mi['text'] ?? '' ) );
                        if ( $tt !== '' ) { $mline .= esc_html( $tt ) . ' <b>●</b> '; }
                    }
                    echo $mline . $mline; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escapato sopra.
                ?></span></div>
            </section>
        <?php elseif ( 'outro' === $variant ) : ?>
            <section class="panel outro" data-screen-label="<?php echo esc_attr( $s['label'] ?: 'Finale' ); ?>" style="--c:var(--olo)">
                <div class="inner outro-grid">
                    <div>
                        <div class="k"><?php echo esc_html( $s['outro_kicker'] ); ?></div>
                        <h2 style="font-size:clamp(36px,4.4vw,72px); max-width:14ch;"><?php echo $this->olox_rich( $s['outro_title'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h2>
                        <p class="sub" style="max-width:44ch;"><?php echo $this->olox_rich( $s['outro_sub'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
                        <div class="outro-links" style="justify-content:flex-start;">
                            <?php foreach ( $this->olox_items( $s, 'outro_links' ) as $l ) : ?>
                            <a href="<?php echo esc_url( $l['url'] ?? '#' ); ?>" style="--c:<?php echo esc_attr( $this->olox_color( $l['color'] ?? 'olo' ) ); ?>"><?php echo esc_html( $l['label'] ?? '' ); ?></a>
                            <?php endforeach; ?>
                        </div>
                        <div class="fine" style="margin-top:40px;"><?php echo esc_html( $s['outro_fine'] ); ?></div>
                    </div>
                    <?php echo $this->olox_madlib( $s ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- helper interno già escapato. ?>
                </div>
            </section>
        <?php else :
            $pc = $this->olox_color( $s['color'] ?? 'olo' );
            ?>
            <section class="panel" data-screen-label="<?php echo esc_attr( $s['label'] ?? '' ); ?>" style="--c:<?php echo esc_attr( $pc ); ?>">
                <span class="idx"></span><span class="bigno"></span>
                <?php echo self::scene_deco( $s['scene'], $s['coord'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                <div class="inner">
                    <div class="rise">
                        <?php if ( ! empty( $s['logo'] ) ) : ?><img class="plogo" src="<?php echo esc_url( $s['logo'] ); ?>" alt="<?php echo esc_attr( $s['label'] ?? '' ); ?>" /><?php endif; ?>
                        <div class="k"><?php echo esc_html( $s['kicker'] ?? '' ); ?></div>
                        <h2><?php echo $this->olox_rich( $s['title_html'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h2>
                        <p class="sub"><?php echo $this->olox_rich( $s['sub_html'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
                        <div class="tags"><?php
                            $tags = array_filter( array_map( 'trim', explode( '|', (string) ( $s['tags'] ?? '' ) ) ) );
                            $ti   = 0;
                            foreach ( $tags as $tg ) {
                                echo '<span' . ( 0 === $ti ? ' class="hot"' : '' ) . '>' . esc_html( $tg ) . '</span>';
                                $ti++;
                            }
                        ?></div>
                        <?php if ( ! empty( $s['cta_text'] ) ) : ?><a class="cta" href="<?php echo esc_url( $s['cta_url'] ?? '#' ); ?>"><?php echo esc_html( $s['cta_text'] ); ?></a><?php endif; ?>
                    </div>
                    <?php echo self::scene_markup( $s['scene'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                </div>
            </section>
        <?php endif;
        echo $this->olox_close(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        return ob_get_clean();
    }
}
