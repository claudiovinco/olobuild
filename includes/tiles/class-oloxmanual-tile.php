<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * OLOX Manual — pagina manuale tecnico OLOtheme completa:
 * testata documento (codici doc, logo prodotto, H1, sub), layout a due colonne
 * con TOC sticky scrollspy a sinistra e capitoli §n a destra (titolo Fraunces
 * con <em>, corpo HTML con liste dash / notice / tabelle definizione),
 * chiusura "Scheda tecnica" in cornice colorata con tabella e CTA.
 */
class Olobuild_OloxManual_Tile extends Olobuild_Olox_Base_Tile {

    protected $type     = 'oloxmanual';
    protected $name     = 'OLOX — Manuale tecnico';
    protected $icon     = 'dashicons-media-document';
    protected $category = 'marketing';
    protected $defaults = [
        'accent'     => 'build',
        'doc_codes'  => [
            [ 'html' => 'doc <b>OLO-BLD-M01</b>' ],
            [ 'html' => 'manuale base' ],
            [ 'html' => '+ scheda tecnica' ],
        ],
        'logo'       => '',
        'title_html' => 'Manuale <em>base</em>',
        'sub_html'   => 'Cos’è OLObuild, come è costruito e perché regge 187 tile con un motore solo. Cinque capitoli, poi la scheda tecnica.',
        'chapters'   => [
            [ 'anchor' => 'c1', 'no' => '§1', 'title_html' => 'Cos’è <em>OLObuild</em>', 'body_html' => '<p>…</p>' ],
        ],
        'toc_spec'   => 'Scheda tecnica',
        'spec_title' => 'Scheda <em>tecnica</em>',
        'spec_name'  => 'OLObuild',
        'spec_sub'   => 'page builder · GPL',
        'spec_rows'  => [
            [ 'f' => 'Tipo', 'text_html' => 'Page builder WordPress tile-based, render server-side' ],
        ],
        'spec_cta1'  => '← Torna alla scheda prodotto',
        'spec_url1'  => 'olobuild',
        'spec_cta2'  => 'Il viaggio OLOtheme',
        'spec_url2'  => './',
    ];

    /** Kses ampio per il corpo capitolo (liste dash, notice, tabelle, codice). */
    private function olox_block( $html ) {
        return wp_kses( (string) $html, [
            'p'      => [ 'class' => true, 'style' => true ],
            'em'     => [ 'style' => true ], 'strong' => [], 'b' => [], 'i' => [ 'style' => true ], 'br' => [],
            'code'   => [], 'span' => [ 'class' => true, 'style' => true ],
            'a'      => [ 'href' => true, 'target' => true, 'rel' => true, 'class' => true ],
            'ul'     => [ 'class' => true ], 'li' => [],
            'h3'     => [], 'div' => [ 'class' => true ],
            'table'  => [ 'class' => true ], 'tbody' => [], 'tr' => [], 'td' => [ 'class' => true ],
        ] );
    }

    public function render( $settings, $style = [] ) {
        $s = wp_parse_args( $settings, $this->defaults );
        $this->olox_assets();
        $accent   = $this->olox_color( $s['accent'] );
        $chapters = $this->olox_items( $s, 'chapters' );

        ob_start();
        echo $this->olox_open( '', '--c:' . $accent ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        ?>
        <header class="man-head">
            <div class="wrap">
                <div class="doc"><?php foreach ( $this->olox_items( $s, 'doc_codes' ) as $d ) : ?><span><?php echo $this->olox_rich( $d['html'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span><?php endforeach; ?></div>
                <?php if ( ! empty( $s['logo'] ) ) : ?><img class="plogo" src="<?php echo esc_url( $s['logo'] ); ?>" alt="" /><?php endif; ?>
                <h1 class="d"><?php echo $this->olox_rich( $s['title_html'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h1>
                <p class="sub" style="margin-bottom:0;"><?php echo $this->olox_rich( $s['sub_html'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
            </div>
        </header>
        <div class="wrap man-layout" data-olox="toc reveal">
            <aside class="toc">
                <?php foreach ( $chapters as $ch ) : ?>
                <a href="#<?php echo esc_attr( $ch['anchor'] ?? '' ); ?>"><?php echo esc_html( ( $ch['no'] ?? '' ) . ' · ' . wp_strip_all_tags( $ch['title_html'] ?? '' ) ); ?></a>
                <?php endforeach; ?>
                <a class="tspec" href="#spec"><?php echo esc_html( $s['toc_spec'] ); ?></a>
            </aside>
            <main>
                <?php foreach ( $chapters as $ch ) : ?>
                <section class="ch" id="<?php echo esc_attr( $ch['anchor'] ?? '' ); ?>">
                    <span class="chno"><?php echo esc_html( $ch['no'] ?? '' ); ?></span>
                    <h2><?php echo $this->olox_rich( $ch['title_html'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h2>
                    <?php echo $this->olox_block( $ch['body_html'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- kses ampio nel helper. ?>
                </section>
                <?php endforeach; ?>
                <section class="spec-close rv" id="spec">
                    <div class="sc-head">
                        <h2><?php echo $this->olox_rich( $s['spec_title'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h2>
                        <div class="vv"><b><?php echo esc_html( $s['spec_name'] ); ?></b><?php echo esc_html( $s['spec_sub'] ); ?></div>
                    </div>
                    <div class="sc-body">
                        <table class="dtab">
                            <tbody>
                            <?php foreach ( $this->olox_items( $s, 'spec_rows' ) as $r ) : ?>
                            <tr><td class="f"><?php echo esc_html( $r['f'] ?? '' ); ?></td><td><?php echo $this->olox_rich( $r['text_html'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td></tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                        <div class="sc-foot">
                            <?php if ( ! empty( $s['spec_cta1'] ) ) : ?><a class="cta" href="<?php echo esc_url( $s['spec_url1'] ?: '#' ); ?>"><?php echo esc_html( $s['spec_cta1'] ); ?></a><?php endif; ?>
                            <?php if ( ! empty( $s['spec_cta2'] ) ) : ?><a class="cta ghost" href="<?php echo esc_url( $s['spec_url2'] ?: '#' ); ?>"><?php echo esc_html( $s['spec_cta2'] ); ?></a><?php endif; ?>
                        </div>
                    </div>
                </section>
            </main>
        </div>
        <?php
        echo $this->olox_close(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        return ob_get_clean();
    }
}
