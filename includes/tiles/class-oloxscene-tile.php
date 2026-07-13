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
 * Markup scene riusato da Olobuild_OloxPanel_Tile (fonte unica); runtime in
 * assets/js/olox.js (modulo "scene" = alias del modulo "home" senza rail).
 */
class Olobuild_OloxScene_Tile extends Olobuild_Olox_Base_Tile {

    protected $type     = 'oloxscene';
    protected $name     = 'OLOX — Scena minigioco';
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
        // Default delle scene showcase hero-* (fonte unica: tile oloxhero).
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
            // Scene showcase delle pagine prodotto: fonte unica = tile oloxhero.
            $hero = new Olobuild_OloxHero_Tile();
            if ( ! empty( $s['show_deco'] ) ) {
                echo Olobuild_OloxPanel_Tile::scene_deco( $scene, $s['coord'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- helper interno già escapato.
            }
            echo $hero->olox_scene( $s, substr( $scene, 5 ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- helper interno già escapato.
        } else {
            if ( ! empty( $s['show_deco'] ) ) {
                echo Olobuild_OloxPanel_Tile::scene_deco( $scene, $s['coord'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- helper interno già escapato.
            }
            echo Olobuild_OloxPanel_Tile::scene_markup( $scene ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- helper interno già escapato.
        }
        echo '</div>';
        echo $this->olox_close(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        return ob_get_clean();
    }
}
