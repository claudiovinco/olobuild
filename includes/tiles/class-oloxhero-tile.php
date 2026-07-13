<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * OLOX Hero — hero delle pagine prodotto OLOtheme (design .dhero .grid2):
 * colonna testo (logo prodotto, kicker con lineetta, H1 Fraunces con <em> e
 * opzionale effetto "drop" a blocchi o parola "scramble", sub, tag pill, 2 CTA)
 * + scena destra per prodotto:
 * wall (muro 84 mattoni + counter), clock (orologio mosso dallo scroll),
 * console (dashboard translator con barre), term (terminale boot typing),
 * porthole (oblò 360 con spot), medal (medaglia livello con orbita), none.
 */
class Olobuild_OloxHero_Tile extends Olobuild_Olox_Base_Tile {

    protected $type     = 'oloxhero';
    protected $name     = 'OLOX — Hero prodotto';
    protected $icon     = 'dashicons-superhero';
    protected $category = 'marketing';
    protected $defaults = [
        'accent'      => 'build',
        'bg_variant'  => 'build',
        'logo'        => '',
        'kicker'      => 'Il telaio · page builder olonico',
        'title_html'  => 'Mattone su <em>mattone.</em>',
        'title_fx'    => 'drop',
        'scramble_words' => [
            [ 'text' => 'Benvenuto' ], [ 'text' => 'Welcome' ], [ 'text' => 'Willkommen' ],
            [ 'text' => 'Bienvenue' ], [ 'text' => 'Bienvenido' ], [ 'text' => 'Bem-vindo' ],
            [ 'text' => 'Welkom' ], [ 'text' => 'Välkommen' ], [ 'text' => 'ようこそ' ], [ 'text' => '欢迎' ],
        ],
        'sub_html'    => '<strong>187 tile in 12 famiglie</strong>, auto-discovered, con animazioni ed effetti di serie.',
        'tags'        => [
            [ 'text' => '€0 free · 100+ tile', 'hot' => true ],
            [ 'text' => '36 animazioni', 'hot' => false ],
            [ 'text' => '11 effetti testo', 'hot' => false ],
            [ 'text' => 'Woo nativo', 'hot' => false ],
        ],
        'cta1_text'   => 'Guarda il cantiere ↓',
        'cta1_url'    => '#cantiere',
        'cta2_text'   => 'Free vs Pro',
        'cta2_url'    => '#prezzi',
        'scene'       => 'wall',
        // wall
        'wall_count'  => 187,
        'wall_label'  => 'tile / 187',
        // clock
        'clock_label' => 'lo scroll muove le lancette',
        // console
        'console_title' => 'translator',
        'console_sub'   => '· dashboard · batch in corso',
        'console_rows'  => [
            [ 'lc' => 'EN', 'w' => 100 ], [ 'lc' => 'DE', 'w' => 100 ], [ 'lc' => 'FR', 'w' => 96 ],
            [ 'lc' => 'ES', 'w' => 92 ], [ 'lc' => 'PT', 'w' => 84 ], [ 'lc' => 'NL', 'w' => 78 ],
            [ 'lc' => 'JA', 'w' => 64 ], [ 'lc' => '+21', 'w' => 52, 'pc' => '…' ],
        ],
        // term
        'term_title'  => 'sentinel',
        'term_sub'    => '· boot sequence',
        'term_lines'  => [
            [ 'cls' => 'cy', 'text' => '[sentinel] avvio pannello v1.2.0 …' ],
            [ 'cls' => 'ok', 'text' => '[waf]      regole OWASP caricate (4 famiglie)' ],
            [ 'cls' => 'ok', 'text' => '[geo]      blocco IPv4/IPv6 + rate limit ARMATO' ],
            [ 'cls' => 'bad', 'text' => '[waf]      SQLi da 185.220.•.•  → BLOCCATO' ],
            [ 'cls' => 'ok', 'text' => '[2fa]      TOTP attivo · codici recupero ok' ],
            [ 'cls' => 'bad', 'text' => '[bot]      finto Googlebot (FCrDNS) → RESPINTO' ],
            [ 'cls' => 'ok', 'text' => '[scan]     checksum core 100% · 0 webshell' ],
            [ 'cls' => 'ok', 'text' => '[cve]      feed firme sincronizzato' ],
            [ 'cls' => 'cy', 'text' => '[sentinel] tutto sotto controllo. resto in ascolto…' ],
        ],
        // medal
        'medal_top'   => 'livello',
        'medal_big'   => '1',
        'medal_bot'   => 'studente',
        'pad_top'     => 0,
    ];

    public function render( $settings, $style = [] ) {
        $s = wp_parse_args( $settings, $this->defaults );
        $this->olox_assets();
        $accent  = $this->olox_color( $s['accent'] );
        $bg      = in_array( $s['bg_variant'], [ 'build', 'booking', 'lang', 'secur', 'tutor', 'none' ], true ) ? $s['bg_variant'] : 'none';
        $scene   = $s['scene'] ?? 'none';
        $pad_top = intval( $s['pad_top'] );

        ob_start();
        echo $this->olox_open( '', '--c:' . $accent ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        ?>
        <header class="dhero<?php echo 'none' !== $bg ? ' oxbg-' . esc_attr( $bg ) : ''; ?>"<?php echo $pad_top ? ' style="padding-top:' . (int) $pad_top . 'px"' : ''; ?>>
            <div class="wrap grid2">
                <div>
                    <?php if ( ! empty( $s['logo'] ) ) : ?><img class="plogo" src="<?php echo esc_url( $s['logo'] ); ?>" alt="" /><?php endif; ?>
                    <?php if ( ! empty( $s['kicker'] ) ) : ?><div class="k"><?php echo esc_html( $s['kicker'] ); ?></div><?php endif; ?>
                    <h1 class="d<?php echo 'drop' === $s['title_fx'] ? ' drop' : ''; ?>"><?php echo $this->olox_title( $s ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h1>
                    <?php if ( ! empty( $s['sub_html'] ) ) : ?><p class="sub"><?php echo $this->olox_rich( $s['sub_html'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p><?php endif; ?>
                    <?php $tags = $this->olox_items( $s, 'tags' ); if ( $tags ) : ?>
                    <div class="tags"><?php foreach ( $tags as $t ) : ?><span<?php echo ! empty( $t['hot'] ) ? ' class="hot"' : ''; ?>><?php echo esc_html( $t['text'] ?? '' ); ?></span><?php endforeach; ?></div>
                    <?php endif; ?>
                    <?php if ( ! empty( $s['cta1_text'] ) || ! empty( $s['cta2_text'] ) ) : ?>
                    <div style="display:flex; gap:12px; flex-wrap:wrap;">
                        <?php if ( ! empty( $s['cta1_text'] ) ) : ?><a class="cta" href="<?php echo esc_url( $s['cta1_url'] ?: '#' ); ?>"><?php echo esc_html( $s['cta1_text'] ); ?></a><?php endif; ?>
                        <?php if ( ! empty( $s['cta2_text'] ) ) : ?><a class="cta ghost" href="<?php echo esc_url( $s['cta2_url'] ?: '#' ); ?>"><?php echo esc_html( $s['cta2_text'] ); ?></a><?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
                <?php echo $this->olox_scene( $s, $scene ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            </div>
        </header>
        <?php
        echo $this->olox_close(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        return ob_get_clean();
    }

    /** Titolo con effetto drop (ogni parola in <span> con delay) o scramble. */
    private function olox_title( $s ) {
        $raw = (string) $s['title_html'];
        if ( 'scramble' === $s['title_fx'] ) {
            $words = [];
            foreach ( $this->olox_items( $s, 'scramble_words' ) as $w ) {
                $t = trim( (string) ( $w['text'] ?? '' ) );
                if ( $t !== '' ) { $words[] = $t; }
            }
            $first = $words ? $words[0] : '';
            $span  = '<span class="scramble" data-olox="scramble" data-words="' . esc_attr( wp_json_encode( $words ) ) . '">' . esc_html( $first ) . '</span>';
            return str_replace( '{scramble}', $span, $this->olox_rich( $raw ) );
        }
        if ( 'drop' === $s['title_fx'] ) {
            // Divide in parole preservando i tag <em>: ogni parola in uno span con delay crescente.
            $clean = $this->olox_rich( $raw );
            $parts = preg_split( '/\s+/', $clean );
            $out   = [];
            $d     = 0.05;
            foreach ( $parts as $p ) {
                if ( '' === $p ) { continue; }
                $out[] = '<span style="--d:' . number_format( $d, 2, '.', '' ) . 's">' . $p . '</span>';
                $d    += 0.125;
            }
            return implode( ' ', $out );
        }
        return $this->olox_rich( $raw );
    }

    /** Scena destra per prodotto. */
    /**
     * Markup delle scene showcase (wall/clock/console/term/porthole/medal).
     * Pubblico: fonte unica riusata dalla tile oloxscene (scene "hero-*").
     */
    public function olox_scene( $s, $scene ) {
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
