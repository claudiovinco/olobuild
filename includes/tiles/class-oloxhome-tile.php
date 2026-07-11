<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * OLOX Home — la home "Experience" di olotheme.com: one-page a scorrimento
 * ORIZZONTALE (rail sticky, lo scroll verticale trascina i pannelli a destra)
 * con 8 fermate: intro (headline gigante + marquee), 6 pannelli prodotto con
 * scena-minigioco dedicata (forza-4 del cantiere, acchiappa-imprevisti,
 * indovina-la-lingua, radar di difesa, oblò 360° con indovinelli e lucchetto,
 * quiz a trascinamento con XP) e finale con form mad-lib timbrato (mailto).
 * Chrome fisso: logo, pallini di salto colorati, progress bar, hint, alone
 * colorato che segue la fermata, modale "olonica", credits.
 * Su mobile torna verticale. Tutto il runtime vive in assets/js/olox.js.
 */
class Olobuild_OloxHome_Tile extends Olobuild_Olox_Base_Tile {

    protected $type     = 'oloxhome';
    protected $name     = 'OLOX — Home Experience (rail + giochi)';
    protected $icon     = 'dashicons-games';
    protected $category = 'marketing';
    protected $defaults = [
        'logo'          => '',
        'langs'         => null, // null = default IT/EN/FR/DE/ES (vedi olox_default_langs)
        // Intro
        'intro_kicker'  => 'OLOtheme · suite WordPress',
        'olw_text'      => 'olonica',
        'intro_title'   => 'Un telaio. Sei prodotti. <em>Nessuna catena.</em>',
        'intro_sub'     => 'Niente SaaS, niente lock-in, niente cloud altrui: tutto vive <strong>sul tuo hosting</strong>, in GPL, scritto a Trento. Scorri: ogni fermata è un prodotto.',
        'intro_cta1'    => 'Inizia il viaggio →',
        'intro_cta2'    => 'Contatti',
        'marquee_items' => [
            [ 'text' => 'no SaaS' ], [ 'text' => 'GPL' ], [ 'text' => '187 tile' ], [ 'text' => '28 lingue' ],
            [ 'text' => '6 verticali booking' ], [ 'text' => '100% locale' ], [ 'text' => 'made in Trento' ],
        ],
        // Modale "olonica"
        'op_kicker'     => 'olos · intero e parte',
        'op_title'      => 'La cellula <em>olonica</em>',
        'op_p1'         => 'Un <strong>olone</strong> è qualcosa che è insieme <strong>un tutto e una parte</strong>: completo da solo, più forte dentro un organismo. OLOtheme è costruito così, ogni prodotto è una cellula autonoma che funziona da sola, ma condivide telaio, dati e lingua con le altre.',
        'op_p2'         => 'Niente monolite: <strong>i prodotti si uniscono a seconda della battaglia</strong> da affrontare, e si sciolgono quando non servono.',
        'battles'       => [
            [ 'q' => 'Aprire un B&B', 'chips' => 'build,booking,lang' ],
            [ 'q' => 'Respingere un attacco', 'chips' => 'secur' ],
            [ 'q' => 'Vendere all’estero', 'chips' => 'build,lang' ],
            [ 'q' => 'Far visitare un immobile a distanza', 'chips' => 'tour,booking' ],
            [ 'q' => 'Portare i corsi online', 'chips' => 'tutor,booking,lang' ],
        ],
        'battle_names'  => [ 'build' => 'build', 'booking' => 'booking', 'lang' => 'lang', 'secur' => 'security', 'tour' => 'tour', 'tutor' => 'tutor' ],
        // Pannelli prodotto
        'panels'        => [
            [ 'color' => 'build', 'label' => 'OLObuild', 'logo' => '', 'kicker' => 'Il telaio · page builder', 'title_html' => 'Costruisce come un <em>cantiere</em>', 'sub_html' => 'Mattone su mattone: <strong>187 tile in 12 famiglie</strong>, tutti auto-discovered, con animazioni ed effetti di serie. <strong>La Free (100+ tile) vale quanto i builder Pro a pagamento della concorrenza</strong>; Pro sblocca l’intera libreria.', 'tags' => '€0 free · 100+ tile|36 animazioni|Woo nativo|dark mode', 'cta_text' => 'Entra nel cantiere', 'cta_url' => 'olobuild', 'scene' => 'wall', 'coord' => 'grid · 44×44 · lot 187' ],
            [ 'color' => 'booking', 'label' => 'OLObooking', 'logo' => '', 'kicker' => 'Prenotazioni · 6 verticali', 'title_html' => 'Un motore che riempie <em>l’agenda</em>', 'sub_html' => 'Camere, tavoli, appuntamenti, eventi, noleggi, immobili: <strong>una sola configurazione</strong> e il motore diventa il tuo mestiere. Con caparra anti no-show e zero commissioni.', 'tags' => '6 verticali|anti no-show|QR access|0% commissioni', 'cta_text' => 'Apri il calendario', 'cta_url' => 'olobooking', 'scene' => 'cal', 'coord' => 'occupancy feed · live' ],
            [ 'color' => 'lang', 'label' => 'OLOlang', 'logo' => '', 'kicker' => 'Multilingua nativo', 'title_html' => 'Lo stesso sito, <em>28 voci</em>', 'sub_html' => 'DeepL + traduttore IA, glossario e memoria di traduzione. Contenuti, menu e stringhe tradotti <strong>via database</strong>, con hreflang, URL localizzati e sitemap per ogni lingua.', 'tags' => '28 lingue|DeepL + IA|SEO hreflang|a vita con Pro', 'cta_text' => 'Cambia lingua', 'cta_url' => 'ololang', 'scene' => 'lang', 'coord' => 'hreflang × 28' ],
            [ 'color' => 'secur', 'label' => 'OLOsecurity', 'logo' => '', 'kicker' => 'Sicurezza · 100% locale', 'title_html' => 'Un radar che non <em>dorme mai</em>', 'sub_html' => 'Firewall OWASP, 2FA, scanner anti-webshell e bonifica guidata dal pannello <strong>Sentinel</strong>. Tutto elaborato <strong>sul tuo server</strong>: il traffico non finisce in nessun cloud altrui.', 'tags' => '100% locale|mini-WAF|TOTP 2FA|Plugin Check 0/0', 'cta_text' => 'Accendi il radar', 'cta_url' => 'olosecurity', 'scene' => 'radar', 'coord' => 'perimetro · armato' ],
            [ 'color' => 'tour', 'label' => 'OLOtour', 'logo' => '', 'kicker' => 'Tour virtuali · in arrivo', 'title_html' => 'Guarda dentro, <em>prima di entrare</em>', 'sub_html' => 'Panorami sferici e HDRI (Polyhaven, Street View), <strong>hot-spot cliccabili</strong>, ambienti collegati, fruizione VR. Il sopralluogo diventa parte del sito, e finisce sul bottone “prenota”.', 'tags' => '360°|hot-spot|multi-stanza|VR ready', 'cta_text' => 'Affaccia lo sguardo', 'cta_url' => 'olotour', 'scene' => 'pano', 'coord' => 'lat 46.07 · lon 11.12 · trento' ],
            [ 'color' => 'tutor', 'label' => 'OLOtutor', 'logo' => '', 'kicker' => 'Formazione · in arrivo', 'title_html' => 'Sali di livello, <em>lezione dopo lezione</em>', 'sub_html' => 'Corsi, quiz, punti e badge, registro voti e certificati, dentro il tuo WordPress. <strong>Gli allievi restano tuoi</strong>, non di un marketplace che ti mette in fila coi concorrenti.', 'tags' => 'LMS|quiz & badge|certificati|area allievi', 'cta_text' => 'Iscriviti all’idea', 'cta_url' => 'olotutor', 'scene' => 'course', 'coord' => 'syllabus · v1 · 4 lezioni' ],
        ],
        // Outro
        'outro_kicker'  => 'Capolinea · si scende',
        'outro_title'   => 'Tutto questo, <em>sul tuo hosting</em>',
        'outro_sub'     => 'GPL · niente SaaS · GDPR in casa · 30 giorni di rimborso su OLObuild Pro. Ogni fermata ha la sua pagina di approfondimento.',
        'outro_fine'    => 'OLOtheme · made in Trento · no SaaS · nessuna catena',
        // Mad-lib
        'mad_doc'       => 'modulo · OLO-CNT-07',
        'mad_line'      => 'linea diretta · Trento',
        'mad_intro'     => 'Ciao, mi chiamo',
        'mad_nome_ph'   => 'nome e cognome',
        'mad_mid'       => 'e il mio sito sogna di diventare',
        'mad_picks'     => [
            [ 'label' => 'cantiere', 'value' => 'un cantiere', 'color' => 'build' ],
            [ 'label' => 'agenda piena', 'value' => 'un’agenda piena', 'color' => 'booking' ],
            [ 'label' => 'poliglotta', 'value' => 'poliglotta', 'color' => 'lang' ],
            [ 'label' => 'fortezza', 'value' => 'una fortezza', 'color' => 'secur' ],
            [ 'label' => 'tour 360°', 'value' => 'un tour 360°', 'color' => 'tour' ],
            [ 'label' => 'aula', 'value' => 'un’aula', 'color' => 'tutor' ],
        ],
        'mad_pre_mail'  => 'Scrivetemi a',
        'mad_mail_ph'   => 'nome@dominio.it',
        'mad_end'       => ', promesso, niente catene.',
        'mad_btn'       => 'Timbra e invia ▾',
        'mad_note'      => 'il timbro apre la tua mail già compilata',
        'mad_stamp'     => 'Ricevuto ◦ OLOtheme',
        'mad_mailto'    => 'info@olotheme.com',
        // Chrome
        'hint_desktop'  => 'Scrolla in basso',
        'hint_desktop2' => 'si va a destra',
        'hint_mobile'   => 'Scorri',
        'hint_mobile2'  => 'una fermata alla volta',
        'credits_html'  => 'OLOtheme by <a href="https://clod.eu" target="_blank" rel="noopener">clod.eu</a> | @2026 | sito introduttivo | <a href="mailto:info@olotheme.com">info@olotheme.com</a>',
    ];

    public function render( $settings, $style = [] ) {
        $s = wp_parse_args( $settings, $this->defaults );
        $this->olox_assets();
        $panels  = $this->olox_items( $s, 'panels' );
        $npanels = count( $panels ) + 2;

        ob_start();
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo $this->olox_open( 'oloxp-home', '--panels:' . (int) $npanels );
        echo '<div data-olox="home">';
        ?>
        <div class="chrome">
            <a class="logo" href="#"><?php if ( ! empty( $s['logo'] ) ) : ?><img src="<?php echo esc_url( $s['logo'] ); ?>" alt="OLOtheme" /><?php endif; ?></a>
            <?php echo $this->olox_langsw( $s['langs'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- helper interno già escapato. ?>
            <div class="jump"></div>
        </div>
        <div class="progress"><i></i></div>
        <div class="credits"><?php echo $this->olox_rich( $s['credits_html'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
        <div class="hint"><span class="hd"><?php echo esc_html( $s['hint_desktop'] ); ?><b>→</b><?php echo esc_html( $s['hint_desktop2'] ); ?></span><span class="hm"><?php echo esc_html( $s['hint_mobile'] ); ?><b>↓</b><?php echo esc_html( $s['hint_mobile2'] ); ?></span></div>
        <div class="ox-halo"></div>

        <div class="opb">
            <div class="opcard">
                <button class="opclose" type="button">✕</button>
                <div class="k"><?php echo esc_html( $s['op_kicker'] ); ?></div>
                <h3><?php echo $this->olox_rich( $s['op_title'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h3>
                <p><?php echo $this->olox_rich( $s['op_p1'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
                <p><?php echo $this->olox_rich( $s['op_p2'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
                <div class="battles">
                    <?php foreach ( $this->olox_items( $s, 'battles' ) as $b ) : ?>
                    <div class="battle"><span class="bq"><?php echo esc_html( $b['q'] ?? '' ); ?></span><span class="bc"><?php
                        foreach ( array_filter( array_map( 'trim', explode( ',', (string) ( $b['chips'] ?? '' ) ) ) ) as $chip ) {
                            $names = is_array( $s['battle_names'] ) ? $s['battle_names'] : [];
                            $nm    = $names[ $chip ] ?? $chip;
                            echo '<i style="--pc:' . esc_attr( $this->olox_color( $chip ) ) . '">' . esc_html( $nm ) . '</i>';
                        }
                    ?></span></div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="ox-rail">
        <div class="ox-view">
        <div class="ox-track">

            <section class="panel intro" data-screen-label="Intro" style="--c:var(--olo)">
                <div class="inner">
                    <div>
                        <div class="k"><?php echo esc_html( $s['intro_kicker'] ); ?> <button class="olw" type="button"><?php echo esc_html( $s['olw_text'] ); ?></button></div>
                        <h1><?php echo $this->olox_rich( $s['intro_title'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h1>
                        <p class="sub" style="max-width:52ch;"><?php echo $this->olox_rich( $s['intro_sub'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
                        <div style="display:flex; gap:12px; flex-wrap:wrap;">
                            <a class="cta" href="#" data-go="1"><?php echo esc_html( $s['intro_cta1'] ); ?></a>
                            <a class="cta ghost" href="#" data-go="<?php echo (int) ( $npanels - 1 ); ?>"><?php echo esc_html( $s['intro_cta2'] ); ?></a>
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

            <?php $i = 0; foreach ( $panels as $p ) : $i++;
                $pc    = $this->olox_color( $p['color'] ?? 'olo' );
                $scene = $p['scene'] ?? 'none';
                $idx   = str_pad( (string) $i, 2, '0', STR_PAD_LEFT );
                $tot   = str_pad( (string) count( $panels ), 2, '0', STR_PAD_LEFT );
                ?>
            <section class="panel" data-screen-label="<?php echo esc_attr( $p['label'] ?? '' ); ?>" style="--c:<?php echo esc_attr( $pc ); ?>">
                <span class="idx"><?php echo esc_html( $idx . ' / ' . $tot ); ?></span><span class="bigno"><?php echo esc_html( $idx ); ?></span>
                <?php echo $this->olox_deco( $scene, $p['coord'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                <div class="inner">
                    <div class="rise">
                        <?php if ( ! empty( $p['logo'] ) ) : ?><img class="plogo" src="<?php echo esc_url( $p['logo'] ); ?>" alt="<?php echo esc_attr( $p['label'] ?? '' ); ?>" /><?php endif; ?>
                        <div class="k"><?php echo esc_html( $p['kicker'] ?? '' ); ?></div>
                        <h2><?php echo $this->olox_rich( $p['title_html'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h2>
                        <p class="sub"><?php echo $this->olox_rich( $p['sub_html'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
                        <div class="tags"><?php
                            $tags = array_filter( array_map( 'trim', explode( '|', (string) ( $p['tags'] ?? '' ) ) ) );
                            $ti   = 0;
                            foreach ( $tags as $tg ) {
                                echo '<span' . ( 0 === $ti ? ' class="hot"' : '' ) . '>' . esc_html( $tg ) . '</span>';
                                $ti++;
                            }
                        ?></div>
                        <?php if ( ! empty( $p['cta_text'] ) ) : ?><a class="cta" href="<?php echo esc_url( $p['cta_url'] ?? '#' ); ?>"><?php echo esc_html( $p['cta_text'] ); ?></a><?php endif; ?>
                    </div>
                    <?php echo $this->olox_scene( $scene ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                </div>
            </section>
            <?php endforeach; ?>

            <section class="panel outro" data-screen-label="Finale" style="--c:var(--olo)">
                <div class="inner outro-grid">
                    <div>
                        <div class="k"><?php echo esc_html( $s['outro_kicker'] ); ?></div>
                        <h2 style="font-size:clamp(36px,4.4vw,72px); max-width:14ch;"><?php echo $this->olox_rich( $s['outro_title'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h2>
                        <p class="sub" style="max-width:44ch;"><?php echo $this->olox_rich( $s['outro_sub'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
                        <div class="outro-links" style="justify-content:flex-start;">
                            <?php foreach ( $panels as $p ) : ?>
                            <a href="<?php echo esc_url( $p['cta_url'] ?? '#' ); ?>" style="--c:<?php echo esc_attr( $this->olox_color( $p['color'] ?? 'olo' ) ); ?>"><?php echo esc_html( $p['label'] ?? '' ); ?></a>
                            <?php endforeach; ?>
                        </div>
                        <div class="fine" style="margin-top:40px;"><?php echo esc_html( $s['outro_fine'] ); ?></div>
                    </div>
                    <div class="madwrap">
                        <div class="madcard" data-mailto="<?php echo esc_attr( $s['mad_mailto'] ); ?>">
                            <div class="madhead"><span><?php echo esc_html( $s['mad_doc'] ); ?></span><span class="blinkdot"></span><span><?php echo esc_html( $s['mad_line'] ); ?></span></div>
                            <div class="madlib">
                                <?php echo esc_html( $s['mad_intro'] ); ?>
                                <input class="ox-f-nome" type="text" placeholder="<?php echo esc_attr( $s['mad_nome_ph'] ); ?>" size="14" autocomplete="name" />
                                <?php echo esc_html( $s['mad_mid'] ); ?>
                                <span class="pick"><?php
                                    foreach ( $this->olox_items( $s, 'mad_picks' ) as $pk ) {
                                        echo '<button type="button" data-v="' . esc_attr( $pk['value'] ?? '' ) . '" style="--c:' . esc_attr( $this->olox_color( $pk['color'] ?? 'olo' ) ) . '">' . esc_html( $pk['label'] ?? '' ) . '</button>';
                                    }
                                ?></span>.
                                <?php echo esc_html( $s['mad_pre_mail'] ); ?>
                                <input class="ox-f-mail" type="email" placeholder="<?php echo esc_attr( $s['mad_mail_ph'] ); ?>" size="16" autocomplete="email" />
                                <?php echo esc_html( $s['mad_end'] ); ?>
                            </div>
                            <div class="madfoot">
                                <button class="cta ox-stamp" type="button"><?php echo esc_html( $s['mad_btn'] ); ?></button>
                                <span class="madnote"><?php echo esc_html( $s['mad_note'] ); ?></span>
                            </div>
                            <div class="bigstamp"><?php echo esc_html( $s['mad_stamp'] ); ?></div>
                        </div>
                    </div>
                </div>
            </section>

        </div>
        </div>
        </div>
        <?php
        echo '</div>';
        echo $this->olox_close(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        return ob_get_clean();
    }

    /** Decorazioni per scena: fonte unica in Olobuild_OloxPanel_Tile. */
    private function olox_deco( $scene, $coord ) {
        return Olobuild_OloxPanel_Tile::scene_deco( $scene, $coord );
    }

    /** Scene-minigioco: fonte unica in Olobuild_OloxPanel_Tile. */
    private function olox_scene( $scene ) {
        return Olobuild_OloxPanel_Tile::scene_markup( $scene );
    }
}
