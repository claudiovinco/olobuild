<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_ProGallery_Tile extends Olobuild_Tile_Base {

    /** Le parti della scheda che vanno sulla foto (coi testi sotto la foto), scritte da scheda_html(). */
    private $sk_foto = '';

    protected $type     = 'progallery';
    protected $name     = 'Pro Gallery';
    protected $icon     = 'dashicons-images-alt2';
    protected $category = 'media';

    protected $defaults = [
        'preset' => 'custom',
        'images'              => [],
        'video_preview'       => 'poster',
        'video_pausa'         => false,
        'video_pausa_angolo'  => '',
        'layout'              => 'grid',
        'layout_family'       => 'classic',
        'puzzle_style'        => 'classic',
        'columns'             => 3,
        'gap'                 => 8,
        'img_height'          => '250px',
        'aspect_ratio'        => 'auto',
        'aspect_ratio_custom' => '16/9',
        'object_fit'          => 'cover',
        'object_position'     => 'center center',
        'thumb_radius'        => 8,
        'rows'                => 0,
        'mobile_columns'      => 2,
        'expand_ratio'        => 4,
        'expand_shrink'       => 0.5,
        'expand_speed'        => 500,
        'parallax_height'     => 1500,
        'parallax_intensity'  => 50,
        // Drift
        'drift_height'        => 1200,
        'drift_intensity'     => 60,
        'drift_rotation'      => 12,
        // Cascade
        'cascade_spread'      => 60,
        'cascade_overlap'     => 40,
        'cascade_rotation'    => 8,
        // Metro
        'metro_cell_height'   => 200,
        'filmstrip_item_width'  => 280,
        'filmstrip_center_zoom' => 1.15,
        'filmstrip_side_tilt'   => 35,
        'filmstrip_autoplay'    => false,
        'filmstrip_speed'       => 4,
        'filmstrip_dots'        => 'dots',
        'filmstrip_dots_color'  => '',
        // Strip (nastro)
        'strip_arrows'          => false,
        'strip_arrows_style'    => 'chevron',
        'strip_arrows_size'     => 36,
        'strip_arrows_color'    => '#ffffff',
        'strip_arrows_bg'       => 'rgba(0,0,0,0.4)',
        'strip_height'          => 280,
        'strip_item_width'      => 300,
        'strip_rows'            => 2,
        'strip_speed'           => 30,
        'strip_pause_hover'     => true,
        'strip_direction'       => 'left',
        'strip_fade_edges'      => true,
        'entrance'            => 'none',
        'entrance_stagger'    => 120,
        'entrance_duration'   => 600,
        'hover_effect'        => 'zoom',
        'hover_zoom_scale'    => 1.08,
        'hover_tilt_angle'    => 10,
        'hover_magnetic_strength' => 24,
        'hover_glow_color'    => '',
        'hover_glow_spread'   => 20,
        'hover_frame_in'      => false,
        'hover_caption'       => 'none',
        'hover_caption_bg'    => 'rgba(0,0,0,0.6)',
        'hover_caption_color' => '#ffffff',
        'hover_caption_weight' => '700',
        'hover_frame_inset'   => 10,
        'continuous'          => '',
        'continuous_speed'    => 20,
        'filter'              => 'none',
        'filter_hover_restore' => false,
        'duotone_dark'        => '#1a1a2e',
        'duotone_light'       => '#e94560',
        'duotone_intensity'   => 80,
        'frame'               => 'none',
        'frame_color'         => '#ffffff',
        'frame_inset_padding' => 10,
        'anim_border'         => 'none',
        'anim_border_color'   => '#ffffff',
        'anim_border_thickness' => 2,
        'anim_border_inset'   => 20,
        'anim_border_speed'   => 3,
        'lightbox'            => true,
        'lightbox_animation'  => 'slide',
        'lightbox_thumbs'     => 'none',
        'lightbox_thumbs_rows' => '1',
        'show_caption'        => false,
        // Testi sulle foto, elenco a righe e filtri (23 nov 2026): coi default la galleria è com'era.
        'scheda'              => '',
        'scheda_quando'       => 'sempre',
        'scheda_categoria'    => true,
        'scheda_numero'       => false,
        'scheda_titolo'       => true,
        'scheda_sottotitolo'  => true,
        'scheda_testo'        => false,
        'scheda_icona'        => 'piu',
        'vista_rapida'        => false,
        'scheda_titolo_size'  => '',
        'scheda_colore'       => '',
        'scheda_font'         => '',
        'scheda_peso'         => '',
        'scheda_accento'      => '',
        'scheda_velo'         => 55,
        'scheda_velo_alto'    => '',
        'scheda_sfondo'       => '',
        'scheda_padding'      => 18,
        'elenco_miniatura'    => true,
        'filtri'              => false,
        'filtri_tutti'        => '',
        'filtri_conteggi'     => true,
        'filtri_stile'        => 'pillole',
        'filtri_allinea'      => 'left',
        'filtri_posizione'    => '',
        'filtri_colore'       => '',
        'filtri_link'         => false,
        'filtri_multipli'     => false,
        'mescola'             => false,
        'cerca'               => false,
        'cerca_testo'         => '',
        'ordina'              => false,
        'piu_foto'            => '',
        'per_pagina'          => 8,
        'scheda_pulsante'     => '',
        'tablet_columns'      => '',
        'large_columns'       => '',
        'small_columns'       => '',
        'scheda_anima'        => '',
        'scheda_numero_formato' => '',
        'scheda_numero_grande' => false,
        'scheda_colore_sottotitolo' => '',
        'scheda_colore_testo' => '',
        'scheda_stile_categoria' => '',
        'scheda_stile_numero' => '',
        'scheda_sottotitolo_maiuscolo' => false,
        'scheda_durata'       => false,
        'zona_categoria'      => '',
        'zona_numero'         => '',
        'zona_titolo'         => '',
        'zona_sottotitolo'    => '',
        'zona_testo'          => '',
        'zona_pulsante'       => '',
        'zona_icona'          => '',
        // Le griglie di Essential Grid rifatte una per una (23 nov 2026): coi default la galleria resta com'era
        'zona_nota'           => '',
        'scheda_nota_stile'   => '',
        'scheda_etichetta_angolo' => 'as',
        'scheda_separatore'   => '',
        'scheda_numero_prefisso' => '',
        'scheda_numero_suffisso' => '',
        'scheda_numero_rovescio' => false,
        'scheda_numero_icona' => '',
        'scheda_cornice_meta' => false,
        'scheda_icona_stile'  => '',
        'scheda_icona_colore' => '',
        'scheda_corsivo_colore' => '',
        'scheda_linea_piede'  => false,
        'scheda_linea_sotto'  => false,
        'scheda_riquadro_grandi' => false,
        'scheda_riquadro_stretto' => false,
        'scheda_campione'     => false,
        'scheda_sfondo_voce'  => false,
        'scheda_pulsante_stile' => '',
        'scheda_barra'        => false,
        'scheda_durata_stile' => '',
        'scheda_titolo_adatta' => false,
        'scheda_punto_testo'  => false,
        'scheda_categoria_punto' => false,
        'scheda_nota_colore'  => '',
        'filtri_forma'        => '',
        'filtri_conteggi_stile' => '',
        'zona_durata'         => '',
        'scheda_categoria_gruppo' => '',
        'scheda_sot_grandi'   => false,
        'filtri_colore_testo' => '',
        'filtri_colore_bordo' => '',
        'filtri_punti'        => false,
        'filtri_linea'        => false,
        'filtri_riga_sopra'   => false,
        'cerca_posizione'     => '',
        'frame_spessore'      => '',
        'pellicola_scritte'   => true,
        'pellicola_rullino'   => 'R1',
        'pellicola_iso'       => '400',
        'pellicola_primo'     => 1,
        'pellicola_passo'     => 1,
        'pellicola_colore_scritte' => '',
        'strip_avanzamento'   => false,
        'griglia_linee'       => false,
        'griglia_linee_colore' => '',
        'testo_sfondo'        => '',
        'social'              => '',
        'social_fonte'        => '',
        'social_quante'       => 12,
        'social_link'         => false,
        'more_bg'             => 'rgba(0,0,0,0.55)',
        'more_color'          => '#ffffff',
        'more_size'           => 28,
        'shadow'              => 'none',
        'border_width'        => '0',
        'border_color'        => '',
        'border_radius'       => [ 'tl' => 0, 'tr' => 0, 'br' => 0, 'bl' => 0 ],
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
        return [];
    }

    private function seeded_random( $seed ) {
        $x = sin( $seed * 127.1 + 311.7 ) * 43758.5453123;
        return $x - floor( $x );
    }

    private static $script_output = false;

    /*
     * La Vista rapida dei prodotti (23 nov 2026, utente: «non si può mettere una sorta di "anteprima del prodotto"?»):
     * sulle voci che sono prodotti di WooCommerce l'icona della card apre la finestra della tile Quick View WC (foto,
     * prezzo, varianti, quantità, carrello); il resto della card porta alla scheda. Nel builder l'icona resta un segno.
     */
    private $qv_on      = false;
    private $qv_attivo  = false;
    private $qv_id      = 0;
    private $qv_usata   = false;
    private static $qv_finestra = false;

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        $this->qv_on     = ! empty( $s['vista_rapida'] ) && class_exists( 'WooCommerce' ) && class_exists( 'Olobuild_Woo_Quickview_Tile' );
        $this->qv_attivo = $this->qv_on && empty( $settings['_builder_mode'] );
        $this->qv_id     = 0;
        $this->qv_usata  = false;

        // Le foto da un social (YouTube, Vimeo, Flickr, Pinterest, Instagram, RSS) al posto di quelle di «Media»
        $social = $this->feed_social( $s );
        if ( null !== $social ) {
            if ( ! $social ) {
                return current_user_can( 'edit_posts' )
                    ? '<div style="padding:2.5rem;text-align:center;color:var(--olo-color-text-muted, #9CA3AF)">' . esc_html( olobuild_t( 'Nessun contenuto dalla fonte social: controlla il canale o l’indirizzo. Questo avviso lo vede solo chi modifica il sito.' ) ) . '</div>'
                    : '';
            }
            $s['images'] = $social;
        }

        $images = is_array( $s['images'] ) ? $s['images'] : [];
        $images = array_filter( $images, function( $img ) {
            if ( is_array( $img ) && ( $img['type'] ?? '' ) === 'testo' ) {
                return '' !== trim( (string) ( $img['caption'] ?? '' ) . (string) ( $img['subtitle'] ?? '' ) . (string) ( $img['text'] ?? '' ) );
            }
            if ( is_array( $img ) && ( $img['type'] ?? '' ) === 'video' ) {
                return ! empty( $img['url'] ) || ! empty( $img['embed'] );
            }
            return is_array( $img ) ? ! empty( $img['url'] ) : ! empty( $img );
        });
        $images = array_values( $images );

        if ( empty( $images ) ) {
            return '<div style="padding:40px;text-align:center;color:var(--olo-color-text-muted, #9CA3AF)">' . esc_html( olobuild_t( 'Aggiungi immagini alla Pro Gallery' ) ) . '</div>';
        }

        $layout       = $s['layout'] ?: 'grid';
        // Backward compat: filmstrip → strip_coverflow
        if ( $layout === 'filmstrip' ) { $layout = 'strip_coverflow'; }
        // Le card di solo testo stanno solo negli schemi a griglia; negli altri si saltano
        if ( ! in_array( $layout, self::SCHEMI_TESTO, true ) ) {
            $images = array_values( array_filter( $images, function ( $img ) {
                return ! $this->is_testo_item( $img );
            } ) );
            if ( empty( $images ) ) {
                return '<div style="padding:2.5rem;text-align:center;color:var(--olo-color-text-muted, #9CA3AF)">' . esc_html( olobuild_t( 'Aggiungi immagini alla Pro Gallery' ) ) . '</div>';
            }
        }
        $ha_testi = (bool) array_filter( $images, [ $this, 'is_testo_item' ] );
        $cols         = max( 2, min( 6, absint( $s['columns'] ) ) );
        $gap          = max( 0, min( 24, absint( $s['gap'] ) ) );
        $radius       = Olobuild_Tile_Utils::border_radius( $s['thumb_radius'] ?? 0 );
        $radius_hover_css = Olobuild_Tile_Utils::radius_force_css( $s['thumb_radius_hover'] ?? null );
        $radius_raw   = is_array( $s["thumb_radius"] ?? 0 ) ? intval( $s["thumb_radius"]["tl"] ?? 0 ) : Olobuild_Tile_Utils::radius_int( $s['thumb_radius'] ?? 0 );
        $radius_css   = $this->build_border_radius_css( $s["thumb_radius"] ?? 0 );
        $radius_css_hover_css = Olobuild_Tile_Utils::radius_force_css( $s['thumb_radius_hover'] ?? null );
        $img_height   = esc_attr( $s['img_height'] ?: '250px' );
        // Proporzioni (cornice): quando c'e' una proporzione e' lei a dare la forma
        // all'item, al posto dell'altezza fissa. Col default 'auto' — obbligatorio — la
        // stringa e' identica a prima, quindi tutte le gallerie pubblicate restano com'erano.
        // Si applica ai due schemi in cui l'item e' un rettangolo largo quanto la colonna
        // (Griglia e Diagonale); gli altri hanno una geometria propria (righe della griglia,
        // posizioni assolute, nastri ad altezza fissa) e per questo il campo li' non compare.
        $ar_decl      = Olobuild_Tile_Utils::image_frame( $s, 'aspect', [ 'ratio' => 'auto' ] )['contenitore'];
        $item_box     = $ar_decl !== '' ? rtrim( $ar_decl, ';' ) : "height:{$img_height}";
        $object_fit   = esc_attr( $s['object_fit'] ?: 'cover' );
        // Punto focale GLOBALE (object-position) applicato a TUTTE le immagini/video.
        // '' o assente → 'center center' = resa identica a prima.
        $obj_pos      = trim( (string) ( $s['object_position'] ?? 'center center' ) );
        if ( $obj_pos === '' ) { $obj_pos = 'center center'; }
        $obj_pos      = esc_attr( $obj_pos );
        $rows         = absint( $s['rows'] );
        $mob_cols     = max( 1, min( 4, absint( $s['mobile_columns'] ) ) );
        $exp_ratio    = max( 2.0, min( 6.0, floatval( $s['expand_ratio'] ) ) );
        $exp_shrink   = max( 0.2, min( 1.0, floatval( $s['expand_shrink'] ) ) );
        $exp_speed    = max( 200, min( 1000, absint( $s['expand_speed'] ) ) );
        $plx_height   = max( 800, min( 3000, absint( $s['parallax_height'] ) ) );
        $plx_intensity = max( 10, min( 100, absint( $s['parallax_intensity'] ) ) );
        // Drift
        $drift_height    = max( 600, min( 2500, absint( $s['drift_height'] ) ) );
        $drift_intensity = max( 10, min( 100, absint( $s['drift_intensity'] ) ) );
        $drift_rotation  = max( 0, min( 25, absint( $s['drift_rotation'] ) ) );
        // Cascade
        $cascade_spread   = max( 20, min( 100, absint( $s['cascade_spread'] ) ) );
        $cascade_overlap  = max( 10, min( 80, absint( $s['cascade_overlap'] ) ) );
        $cascade_rotation = max( 0, min( 20, absint( $s['cascade_rotation'] ) ) );
        // Metro
        $metro_cell_h     = max( 100, min( 400, absint( $s['metro_cell_height'] ) ) );
        $film_width   = max( 180, min( 450, absint( $s['filmstrip_item_width'] ) ) );
        $film_zoom    = max( 1.0, min( 1.5, floatval( $s['filmstrip_center_zoom'] ) ) );
        $film_tilt    = max( 0, min( 60, absint( $s['filmstrip_side_tilt'] ) ) );
        $film_auto    = ! empty( $s['filmstrip_autoplay'] );
        $film_speed   = max( 2.0, min( 8.0, floatval( $s['filmstrip_speed'] ) ) );
        $film_dots    = in_array( $s['filmstrip_dots'] ?? '', [ 'dots', 'lines', 'progress', 'fraction', 'none' ], true ) ? ( $s['filmstrip_dots'] ?? '' ) : 'dots';
        $film_dots_c  = $this->safe_color_css( $s['filmstrip_dots_color'] ?? '' ) ?: '';
        // Padding verticale per compensare zoom 3D del coverflow
        $film_pad     = min( 80, max( 25, (int) ceil( intval( $img_height ?: 250 ) * ( $film_zoom - 1 ) / 2 ) + 15 ) );
        // Strip settings
        $strip_height   = max( 150, min( 500, absint( $s['strip_height'] ) ) );
        $strip_item_w   = max( 150, min( 500, absint( $s['strip_item_width'] ) ) );
        $strip_rows_n   = max( 2, min( 3, absint( $s['strip_rows'] ) ) );
        $strip_speed    = max( 10, min( 60, absint( $s['strip_speed'] ) ) );
        $strip_pause    = ! empty( $s['strip_pause_hover'] );
        $strip_dir      = ( $s['strip_direction'] === 'right' ) ? 'right' : 'left';
        $strip_fade     = ! empty( $s['strip_fade_edges'] );
        $video_preview  = in_array( $s['video_preview'] ?? '', [ 'poster', 'autoplay' ], true ) ? ( $s['video_preview'] ?? '' ) : 'poster';
        $is_strip       = ( str_starts_with( $layout, 'strip' ) );
        $is_strip_drag  = in_array( $layout, [ 'strip', 'strip_collage', 'strip_multi' ], true );
        $is_strip_auto  = in_array( $layout, [ 'strip_marquee', 'strip_split' ], true );
        $is_coverflow   = ( $layout === 'strip_coverflow' );
        $strip_arrows   = $is_strip && ! $is_coverflow && ! empty( $s['strip_arrows'] );
        $sa_style       = in_array( $s['strip_arrows_style'] ?? '', [ 'chevron', 'arrow', 'circle', 'square', 'pill', 'minimal' ], true ) ? ( $s['strip_arrows_style'] ?? '' ) : 'chevron';
        $sa_size        = max( 24, min( 60, absint( $s['strip_arrows_size'] ) ) );
        $sa_color       = $this->safe_color_css( $s['strip_arrows_color'] ) ?: 'var(--olo-color-light, #ffffff)';
        $sa_bg          = $this->safe_color_css( $s['strip_arrows_bg'] ) ?: 'rgba(0,0,0,0.4)';
        $uid            = 'olo-pg-' . wp_unique_id();

        // Visible images
        $total       = count( $images );
        $max_visible = ( $rows > 0 ) ? $cols * $rows : $total;
        $extra       = max( 0, $total - $max_visible );

        // Testi sulle foto, filtri e misure senza buchi (23 nov 2026)
        $sk        = $this->scheda_opzioni( $s, $layout );
        $sk['totale'] = min( $max_visible, $total );
        $filtri_on = ! empty( $s['filtri'] ) && in_array( $layout, self::SCHEMI_FILTRI, true );
        $f_cats    = [];
        $f_conti   = [];
        $f_gruppi  = [];
        if ( $filtri_on ) {
            // «Tipo: Guida» va nel gruppo «Tipo»: un valore per gruppo, i gruppi si combinano
            $f_gruppi = $this->gruppi_filtri( array_slice( $images, 0, $max_visible ) );
            foreach ( $f_gruppi as $gr ) {
                foreach ( $gr['voci'] as $slug => $nome ) {
                    $f_cats[ $slug ]  = $nome;
                    $f_conti[ $slug ] = $gr['conti'][ $slug ];
                }
            }
            $filtri_on = ! empty( $f_cats );
        }
        $bar      = $this->barra_opzioni( $s, $layout, $filtri_on );
        $barra_on = $bar['filtri'] || $bar['cerca'] || $bar['ordina'];
        // Filtri in colonna a sinistra (le «faccette»): solo sugli schemi che non sono nastri
        $filtri_lato = $bar['filtri'] && 'sinistra' === ( $s['filtri_posizione'] ?? '' ) && 0 !== strpos( (string) $layout, 'strip' ) && 'filmstrip' !== $layout;
        $dati_on  = $barra_on || '' !== $bar['piu'] || $bar['mescola'];
        $piastre_d = [];
        $piastre_m = [];
        if ( in_array( $layout, [ 'metro', 'mosaic', 'collage' ], true ) ) {
            $vis_pz    = array_slice( $images, 0, $max_visible );
            $desid     = $this->misure_desiderate( $layout, $vis_pz );
            $piastre_d = $this->piastre( $desid, $cols );
            $piastre_m = $this->piastre( $desid, $mob_cols );
            // Le foto grandi (almeno 2×2): il riquadro e il sottotitolo possono stare solo su quelle
            foreach ( $piastre_d as $k => $pz ) {
                if ( (int) $pz[0] >= 2 && (int) $pz[1] >= 2 ) {
                    $sk['grandi'][] = $k + 1;
                }
            }
        }
        $ha_segni = (bool) array_filter( $images, function ( $im ) {
            return is_array( $im ) && in_array( (string) ( $im['segno'] ?? '' ), [ 'riquadro', 'cerchio', 'matita' ], true );
        } );
        $ha_pausa     = false;
        $ha_etichette = (bool) array_filter( $images, function ( $im ) {
            return is_array( $im ) && '' !== trim( (string) ( $im['etichetta'] ?? '' ) );
        } );
        // Le scritte sul bordo della pellicola («R1 · 400» = rullino 1, sensibilità ISO 400)
        $pell_bordo = '';
        $pell_primo = max( 0, min( 99, (int) ( $s['pellicola_primo'] ?? 1 ) ) );
        $pell_passo = max( 1, min( 9, (int) ( $s['pellicola_passo'] ?? 1 ) ) );
        if ( 'pellicola' === ( $s['frame'] ?? '' ) && ! empty( $s['pellicola_scritte'] ) ) {
            $sigla      = implode( ' · ', array_filter( [ trim( (string) ( $s['pellicola_rullino'] ?? '' ) ), trim( (string) ( $s['pellicola_iso'] ?? '' ) ) ], 'strlen' ) );
            $pell_bordo = '<span>' . esc_html( $sigla ) . '</span>';
        }
        $va_c = [ 'filtri' => $filtri_on, 'dati' => $dati_on, 'piu' => $bar['piu'], 'per' => $bar['per'], 'pd' => $piastre_d, 'pm' => $piastre_m ];

        // Scattered grid dimensions (pre-computed for CSS aspect-ratio + item positioning)
        if ( $layout === 'scattered' ) {
            $total_vis = min( $max_visible, $total );
            $cols_s    = max( 2, (int) ceil( sqrt( $total_vis ) ) );
            $rows_s    = (int) ceil( $total_vis / $cols_s );
        }

        // Parallax grid dimensions
        if ( $layout === 'parallax' ) {
            $total_vis_p = min( $max_visible, $total );
            $cols_p      = min( 4, max( 2, (int) ceil( sqrt( $total_vis_p ) ) ) );
            $rows_p      = (int) ceil( $total_vis_p / $cols_p );
        }

        // Drift grid dimensions
        if ( $layout === 'drift' ) {
            $total_vis_d = min( $max_visible, $total );
            $cols_d      = min( 4, max( 2, (int) ceil( sqrt( $total_vis_d ) ) ) );
            $rows_d      = (int) ceil( $total_vis_d / $cols_d );
        }

        // Cascade dimensions
        if ( $layout === 'cascade' ) {
            $total_vis_c = min( $max_visible, $total );
        }

        // Metro pattern: compute grid areas
        if ( $layout === 'metro' ) {
            $total_vis_m = min( $max_visible, $total );
        }

        // Entrance
        $entrance    = $s['entrance'] ?: 'none';
        $ent_stagger = max( 80, min( 400, absint( $s['entrance_stagger'] ) ) );
        $ent_dur     = max( 300, min( 1200, absint( $s['entrance_duration'] ) ) );

        // Hover
        $hover       = $s['hover_effect'] ?: 'none';
        $hz_scale    = max( 1.05, min( 1.30, floatval( $s['hover_zoom_scale'] ) ) );
        $tilt_angle  = max( 5, min( 20, absint( $s['hover_tilt_angle'] ) ) );
        $mag_str     = max( 8, min( 60, absint( $s['hover_magnetic_strength'] ) ) );
        $glow_color  = $this->safe_color_css( $s['hover_glow_color'] ) ?: 'var(--olo-color-primary, #e1474f)';
        $glow_spread = max( 8, min( 50, absint( $s['hover_glow_spread'] ) ) );
        $hcaption    = $s['hover_caption'] ?: 'none';
        $hcap_bg     = $this->safe_color_css( $s['hover_caption_bg'] ) ?: 'rgba(0,0,0,0.6)';
        $hcap_color  = $this->safe_color_css( $s['hover_caption_color'] ) ?: 'var(--olo-color-light, #ffffff)';
        $frame_inset = max( 4, min( 40, absint( $s['hover_frame_inset'] ) ) );

        // Continuous (multi — comma-separated string 'float,drift' or legacy array)
        $cont_raw = $s['continuous'] ?: '';
        if ( is_string( $cont_raw ) && $cont_raw !== '' && $cont_raw !== 'none' ) {
            $cont_raw = array_map( 'trim', explode( ',', $cont_raw ) );
        } elseif ( ! is_array( $cont_raw ) ) {
            $cont_raw = [];
        }
        $cont_effects = array_values( array_filter( $cont_raw, function( $v ) {
            return $v && $v !== 'none';
        }));
        $cont_speed  = max( 10, min( 40, absint( $s['continuous_speed'] ) ) );

        // Filter
        $filter      = $s['filter'] ?: 'none';
        $filter_hover = ! empty( $s['filter_hover_restore'] );
        $duo_dark    = $this->safe_color_css( $s['duotone_dark'] ) ?: '#1a1a2e';
        $duo_light   = $this->safe_color_css( $s['duotone_light'] ) ?: '#e94560';
        // Duotone SVG float values (0-1)
        $dr = $dg = $db = $lr = $lg = $lb = '0.000';
        $duo_k2 = '0.800'; $duo_k3 = '0.200';
        if ( $filter === 'duotone' ) {
            // la matrice SVG vuole numeri: un token della Palette si risolve qui (prima hexdec di «var(»)
            $duo_dark  = Olobuild_Tile_Utils::colore_hex( $duo_dark ) ?: '#1a1a2e';
            $duo_light = Olobuild_Tile_Utils::colore_hex( $duo_light ) ?: '#e94560';
            $dr = number_format( hexdec( substr( $duo_dark, 1, 2 ) ) / 255, 3, '.', '' );
            $dg = number_format( hexdec( substr( $duo_dark, 3, 2 ) ) / 255, 3, '.', '' );
            $db = number_format( hexdec( substr( $duo_dark, 5, 2 ) ) / 255, 3, '.', '' );
            $lr = number_format( hexdec( substr( $duo_light, 1, 2 ) ) / 255, 3, '.', '' );
            $lg = number_format( hexdec( substr( $duo_light, 3, 2 ) ) / 255, 3, '.', '' );
            $lb = number_format( hexdec( substr( $duo_light, 5, 2 ) ) / 255, 3, '.', '' );
            $intensity = max( 0, min( 100, absint( $s['duotone_intensity'] ) ) ) / 100;
            $duo_k2 = number_format( $intensity, 3, '.', '' );
            $duo_k3 = number_format( 1 - $intensity, 3, '.', '' );
        }

        // Frame
        $frame       = $s['frame'] ?: 'none';
        $frame_color = $this->safe_color_css( $s['frame_color'] ) ?: 'var(--olo-color-background, #ffffff)';

        // Animated border (backward compat: old hover_frame_in toggle → frame-in)
        $anim_border = $s['anim_border'] ?: 'none';
        if ( $anim_border === 'none' && ! empty( $s['hover_frame_in'] ) ) {
            $anim_border = 'frame-in';
        }
        $ab_color    = $this->safe_color_css( $s['anim_border_color'] ) ?: 'var(--olo-color-border, #ffffff)';
        $ab_thick    = max( 1, min( 6, absint( $s['anim_border_thickness'] ) ) );
        $ab_inset    = max( 4, min( 50, absint( $s['anim_border_inset'] ) ) );
        $ab_speed    = max( 1, min( 10, floatval( $s['anim_border_speed'] ) ) );

        // Lightbox
        $lightbox    = ! empty( $s['lightbox'] );
        $lb_anim     = esc_attr( $s['lightbox_animation'] ?? 'slide' );
        $lb_thumbs   = $s['lightbox_thumbs'] ?? 'none';
        $lb_rows     = $s['lightbox_thumbs_rows'] ?? '1';
        $lb_custom   = $lightbox && in_array( $lb_thumbs, [ 'bottom', 'right', 'left' ], true );
        $show_cap    = ! empty( $s['show_caption'] );

        // +N
        $more_bg     = $this->safe_color_css( $s['more_bg'] ?: 'rgba(0,0,0,0.55)' );
        $more_color  = $this->safe_color_css( $s['more_color'] ?: 'var(--olo-color-light, #ffffff)' );
        $more_size   = max( 16, min( 48, absint( $s['more_size'] ) ) );

        // Shadow / border
        $shadow = Olobuild_Tile_Utils::shadow_value( $s, 'shadow' );
        $bw     = max( 0, min( 10, absint( $s['border_width'] ) ) );
        $bc     = $this->safe_color_css( $s['border_color'] ) ?: 'var(--olo-color-border, #E5E7EB)';

        // CSS filter map
        $css_filter = '';
        $filter_map = [
            'grayscale'     => 'grayscale(100%)',
            'sepia'         => 'sepia(80%)',
            'high-contrast' => 'contrast(140%) saturate(120%)',
            'warm'          => 'sepia(25%) saturate(130%) hue-rotate(-10deg)',
            'cool'          => 'saturate(80%) hue-rotate(20deg) brightness(105%)',
            'vintage'       => 'sepia(40%) contrast(90%) brightness(95%) saturate(80%)',
        ];
        if ( $filter !== 'none' && isset( $filter_map[ $filter ] ) ) {
            $css_filter = $filter_map[ $filter ];
        }

        ob_start();

        // ─── STYLE ───
        // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above: absint/intval/floatval with min/max clamps, number_format(), the safe_color_css() whitelist, esc_attr()'d strings, fixed string maps/ternaries, the aspect-ratio built by Olobuild_Tile_Utils::image_frame() behind its own regex whitelist, and a generated unique id.
        echo '<style>';

        // Punto focale della GALLERIA, che ora è il RIPIEGO: una foto con
        // un'inquadratura sua scrive --olo-pg-fp sul proprio item e la scavalca.
        // Una variabile CSS e non uno stile sull'<img> perché così la regola resta
        // UNA per tutti i venti layout, invece di ventidue tag da tenere allineati.
        // Chi non ha --olo-pg-fp prende il valore di ripiego: le gallerie salvate
        // prima di questa versione non si spostano di un pixel.
        echo ".{$uid} .olo-pg-item img,.{$uid} .olo-pg-item video{object-position:var(--olo-pg-fp,{$obj_pos})}";

        // Hover radius (applies to all layouts — items always have border-radius:$radius)
        if ( $radius_hover_css !== '' ) {
            echo ".{$uid} .olo-pg-item{transition:border-radius 400ms cubic-bezier(.4,0,.2,1)}";
            echo ".{$uid} .olo-pg-item:hover{border-radius:{$radius_hover_css} !important}";
        }

        // Base layout
        if ( $layout === 'justified' ) {
            echo ".{$uid}{display:flex;flex-wrap:wrap;gap:{$gap}px}";
            echo ".{$uid} .olo-pg-item{position:relative;overflow:hidden;border-radius:{$radius};height:{$img_height};flex-grow:1;min-width:120px}";
            echo ".{$uid} .olo-pg-item img{width:100%;height:100%;object-fit:cover;display:block}";
        } elseif ( $layout === 'masonry' ) {
            echo ".{$uid}{column-count:{$cols};column-gap:{$gap}px}";
            echo ".{$uid} .olo-pg-item{break-inside:avoid;display:inline-block;width:100%;margin-bottom:{$gap}px;position:relative;overflow:hidden;border-radius:{$radius}}";
            echo ".{$uid} .olo-pg-item img{width:100%;display:block;object-fit:cover}";
        } elseif ( $layout === 'scattered' ) {
            // Bordi animati con box-shadow: contenere l'overflow e aggiungere padding per edge items
            $scatter_uses_shadow = in_array( $anim_border, [ 'neon', 'pulse', 'radar' ], true );
            if ( $scatter_uses_shadow ) {
                $scatter_pad = max( 15, $ab_thick * 4 );
                echo ".{$uid}{position:relative;aspect-ratio:{$cols_s}/{$rows_s};overflow:hidden;padding:{$scatter_pad}px}";
            } else {
                echo ".{$uid}{position:relative;aspect-ratio:{$cols_s}/{$rows_s};overflow:visible}";
            }
            echo ".{$uid} .olo-pg-item{position:absolute;overflow:hidden;border-radius:{$radius};box-shadow:0 4px 12px rgba(0,0,0,.15)}";
            echo ".{$uid} .olo-pg-item img{width:100%;height:100%;object-fit:{$object_fit};display:block}";
        } elseif ( $layout === 'parallax' ) {
            echo ".{$uid}{position:relative;height:{$plx_height}px;overflow:visible}";
            echo ".{$uid} .olo-pg-item{position:absolute;overflow:hidden;border-radius:{$radius};will-change:transform;transition:transform 0.1s linear}";
            echo ".{$uid} .olo-pg-item img{width:100%;height:auto;display:block}";
        } elseif ( $layout === 'drift' ) {
            echo ".{$uid}{position:relative;height:{$drift_height}px;overflow:visible}";
            echo ".{$uid} .olo-pg-item{position:absolute;overflow:hidden;border-radius:{$radius};will-change:transform;transition:transform 0.15s linear}";
            echo ".{$uid} .olo-pg-item img{width:100%;height:auto;display:block}";
        } elseif ( $layout === 'cascade' ) {
            $cascade_h = max( 400, $total_vis_c * 80 + 300 );
            echo ".{$uid}{position:relative;height:{$cascade_h}px;overflow:visible;perspective:1200px}";
            echo ".{$uid} .olo-pg-item{position:absolute;overflow:hidden;border-radius:{$radius};will-change:transform;transition:transform 0.2s ease-out;box-shadow:0 8px 30px rgba(0,0,0,.2)}";
            echo ".{$uid} .olo-pg-item img{width:100%;height:100%;object-fit:{$object_fit};display:block}";
        } elseif ( $layout === 'metro' ) {
            echo ".{$uid}{display:grid;grid-template-columns:repeat({$cols},1fr);grid-auto-rows:{$metro_cell_h}px;grid-auto-flow:dense;gap:{$gap}px}";
            echo ".{$uid} .olo-pg-item{position:relative;overflow:hidden;border-radius:{$radius}}";
            echo ".{$uid} .olo-pg-item img{width:100%;height:100%;object-fit:{$object_fit};display:block}";
            // Le misure (2×2, 2×1, 1×2 o scelte a mano) le porta ogni foto: piastre() le sceglie senza buchi
            echo ".{$uid} .olo-pg-item{grid-column:span var(--pw,1);grid-row:span var(--ph,1)}";
        } elseif ( $is_coverflow ) {
            // COVERFLOW: la foto al centro di fronte, le altre ai lati tutte con lo stesso angolo
            // («Rotazione 3D laterali»), rivolte verso di lei, impilate a passo regolare e via via
            // più lontane, sotto UNA prospettiva comune, quella del nastro. Prima ogni foto aveva una
            // prospettiva sua, restava al suo posto nella fila piatta e l'angolo cresceva a ogni
            // passo: la vicina finiva dietro la centrale, le altre restavano lontane, un ventaglio
            // irregolare (9 ott 2026). Il nastro scorre davvero (tocco, trackpad, aggancio al
            // centro), ma il posto di ogni foto lo decide lo script: misura larghezza, passo,
            // testa e coda (variabili --cf-*) e a ogni scorrimento sposta, ruota e allontana le foto.
            echo ".{$uid}-wrap{position:relative}";
            $cf_persp = (int) round( max( 700, $film_width * 3.6 ) );
            echo ".{$uid}{display:flex;gap:{$gap}px;overflow-x:auto;scroll-snap-type:x mandatory;scroll-behavior:smooth;-webkit-overflow-scrolling:touch;padding:{$film_pad}px 0;scrollbar-width:none;-ms-overflow-style:none;position:relative;perspective:{$cf_persp}px;perspective-origin:50% 50%}";
            // Testa e coda: anche la prima e l'ultima foto arrivano al centro.
            echo ".{$uid}::before,.{$uid}::after{content:'';flex:0 0 var(--cf-pad,0px)}";
            echo ".{$uid}::-webkit-scrollbar{height:0;background:transparent}";
            echo ".{$uid}::-webkit-scrollbar-thumb{background:transparent}";
            echo ".{$uid}::-webkit-scrollbar-track{background:transparent}";
            echo ".{$uid} .olo-pg-item{flex:0 0 auto;width:var(--cf-w,{$film_width}px);margin-right:var(--cf-mr,0px);height:{$img_height};position:relative;overflow:hidden;border-radius:{$radius};scroll-snap-align:center;will-change:transform,opacity}";
            echo ".{$uid} .olo-pg-item:last-child{margin-right:0}";
            // Con lo script attivo ci si aggancia a segnaposto fermi, uno per foto (lo scroll-snap misura il
            // riquadro trasformato, e le foto si spostano a ogni passo: il nastro si fermava a metà).
            echo ".{$uid}.olo-cf-on .olo-pg-item{scroll-snap-align:none}";
            echo ".{$uid} .olo-cf-snap{position:absolute;top:0;width:2px;height:1px;scroll-snap-align:center;pointer-events:none}";
            // Le foto in fondo alla pila sfumano sul bordo del nastro invece di essere tagliate di netto
            echo ".{$uid}{-webkit-mask-image:linear-gradient(90deg,transparent 0,#000 6%,#000 94%,transparent 100%);mask-image:linear-gradient(90deg,transparent 0,#000 6%,#000 94%,transparent 100%)}";
            echo "@media(prefers-reduced-motion:reduce){.{$uid}{scroll-behavior:auto}}";
            echo ".{$uid} .olo-pg-item img{width:100%;height:100%;object-fit:{$object_fit};display:block;-webkit-user-drag:none;user-select:none}";
            echo ".{$uid} .olo-pg-item{-webkit-user-drag:none;user-select:none}";
            // Frecce (z-index 200 — sopra item coverflow che hanno z-index fino a 100)
            echo ".{$uid}-prev,.{$uid}-next{position:absolute;top:50%;transform:translateY(-50%);width:40px;height:40px;border-radius:50%;background:rgba(0,0,0,.45);color:#fff;border:none;font-size:22px;line-height:1;cursor:pointer;z-index:200;display:flex;align-items:center;justify-content:center;opacity:0;transition:opacity .3s ease,background .2s ease;backdrop-filter:blur(4px)}";
            echo ".{$uid}-prev{left:8px}";
            echo ".{$uid}-next{right:8px}";
            echo ".{$uid}-wrap:hover .{$uid}-prev,.{$uid}-wrap:hover .{$uid}-next{opacity:1}";
            echo ".{$uid}-prev:hover,.{$uid}-next:hover{background:rgba(0,0,0,.7)}";
            // Da tastiera le frecce compaiono col fuoco, non solo al passaggio del mouse.
            echo ".{$uid}-prev:focus-visible,.{$uid}-next:focus-visible{opacity:1;outline:2px solid var(--olo-color-primary,#e1474f);outline-offset:2px}";
            // Indicatore posizione. I pallini e le lineette li crea lo script come <button>
            // (prima le regole cercavano degli <span>, e appena lo script li trovava uscivano
            // come pulsanti di sistema): `>*` li prende tutti e due, il reset toglie il pulsante.
            $dot_reset = 'border:0;padding:0;margin:0;-webkit-appearance:none;appearance:none;flex:0 0 auto;';
            $dot_focus = ".{$uid}-dots>*:focus-visible{outline:2px solid var(--olo-color-primary,#e1474f);outline-offset:3px}";
            $dot_clr = $film_dots_c ?: '';
            if ( $film_dots === 'dots' ) {
                $da = $dot_clr ?: 'rgba(0,0,0,.65)';
                $di = $dot_clr ?: 'rgba(0,0,0,.22)';
                $di_op = $dot_clr ? 'opacity:.35;' : '';
                echo ".{$uid}-dots{display:flex;justify-content:center;gap:10px;padding:12px 0;width:80%;max-width:600px;margin:0 auto;position:relative;z-index:11}";
                echo ".{$uid}-dots>*{{$dot_reset}width:12px;height:12px;border-radius:50%;background:{$di};{$di_op}cursor:pointer;transition:transform .2s ease,background .2s ease,opacity .2s ease}";
                echo ".{$uid}-dots>.active{background:{$da};opacity:1;transform:scale(1.25)}";
                echo $dot_focus;
            } elseif ( $film_dots === 'lines' ) {
                $da = $dot_clr ?: 'rgba(0,0,0,.65)';
                $di = $dot_clr ?: 'rgba(0,0,0,.22)';
                $di_op = $dot_clr ? 'opacity:.35;' : '';
                echo ".{$uid}-dots{display:flex;justify-content:center;gap:6px;padding:12px 0;width:80%;max-width:600px;margin:0 auto;position:relative;z-index:11;align-items:center}";
                echo ".{$uid}-dots>*{{$dot_reset}width:20px;height:3px;border-radius:2px;background:{$di};{$di_op}cursor:pointer;transition:width .3s ease,background .3s ease,opacity .3s ease}";
                echo ".{$uid}-dots>.active{width:36px;background:{$da};opacity:1}";
                echo $dot_focus;
            } elseif ( $film_dots === 'progress' ) {
                $da = $dot_clr ?: 'rgba(0,0,0,.55)';
                $di = $dot_clr ?: 'rgba(0,0,0,.15)';
                $di_op = $dot_clr ? 'opacity:.25;' : '';
                echo ".{$uid}-dots{padding:10px 0;width:60%;max-width:400px;margin:0 auto;position:relative;z-index:11}";
                echo ".{$uid}-dots .pg-prog-track{height:3px;border-radius:2px;background:{$di};{$di_op}overflow:hidden;position:relative;cursor:pointer}";
                echo ".{$uid}-dots .pg-prog-fill{height:100%;border-radius:2px;background:{$da};transition:width .25s ease;width:0}";
            } elseif ( $film_dots === 'fraction' ) {
                echo ".{$uid}-dots{text-align:center;padding:10px 0;font-size:14px;font-weight:600;color:" . ( $dot_clr ?: 'rgba(0,0,0,.55)' ) . ";font-variant-numeric:tabular-nums;z-index:11;position:relative;letter-spacing:0.05em}";
            }
            // Mobile: frecce nascoste (la larghezza delle foto la adatta lo script al nastro)
            echo "@media(max-width:640px){.{$uid}-prev,.{$uid}-next{display:none}}";
            echo "@media(hover:none){.{$uid}-prev,.{$uid}-next{display:none}}";
        } elseif ( $layout === 'honeycomb' ) {
            echo ".{$uid}{display:flex;flex-wrap:wrap;gap:{$gap}px;justify-content:center}";
            echo ".{$uid} .olo-pg-item{width:180px;height:200px;clip-path:polygon(50% 0%,100% 25%,100% 75%,50% 100%,0% 75%,0% 25%);overflow:hidden;position:relative}";
            echo "@media(max-width:640px){.{$uid} .olo-pg-item{width:calc(50% - {$gap}px);height:auto;aspect-ratio:9/10}}";
            echo ".{$uid} .olo-pg-item img{width:100%;height:100%;object-fit:cover;display:block}";
        } elseif ( $layout === 'hexgrid' ) {
            // Tessellating hex grid — positions computed inline per item
            echo ".{$uid}{position:relative;width:100%;overflow:visible}";
            echo ".{$uid} .olo-pg-item{position:absolute;clip-path:polygon(50% 0%,100% 25%,100% 75%,50% 100%,0% 75%,0% 25%);overflow:hidden}";
            echo ".{$uid} .olo-pg-item img{width:100%;height:100%;object-fit:cover;display:block}";
        } elseif ( $layout === 'puzzle' ) {
            $p_h_px = intval( $img_height ) ?: 250;
            $p_rows_total = (int) ceil( min( $max_visible, $total ) / $cols );
            $p_container_h = $p_rows_total * $p_h_px;
            echo ".{$uid}{position:relative;width:100%;height:{$p_container_h}px;overflow:visible}";
            echo ".{$uid} .olo-pg-item{position:absolute}";
            echo ".{$uid} .olo-pg-item img{position:absolute;top:0;left:0;width:100%;height:100%;object-fit:cover;display:block}";
            $p_mob_h = max( 120, (int) round( $p_h_px * 0.6 ) );
            echo "@media(max-width:640px){.{$uid}{height:auto!important;display:grid;grid-template-columns:repeat({$mob_cols},1fr);gap:{$gap}px}.{$uid} .olo-pg-item{position:relative!important;left:auto!important;top:auto!important;width:auto!important;height:{$p_mob_h}px!important;clip-path:none!important;border-radius:{$radius};overflow:hidden}}";
        } elseif ( $layout === 'collage' ) {
            echo ".{$uid}{display:grid;grid-template-columns:repeat({$cols},1fr);grid-auto-rows:{$img_height};grid-auto-flow:dense;gap:{$gap}px}";
            echo ".{$uid} .olo-pg-item{position:relative;overflow:hidden;border-radius:{$radius};grid-column:span var(--pw,1);grid-row:span var(--ph,1)}";
            echo ".{$uid} .olo-pg-item img{width:100%;height:100%;object-fit:{$object_fit};display:block}";
        } elseif ( $layout === 'mosaic' ) {
            echo ".{$uid}{display:grid;grid-template-columns:repeat({$cols},1fr);grid-auto-rows:{$img_height};grid-auto-flow:dense;gap:{$gap}px}";
            echo ".{$uid} .olo-pg-item{position:relative;overflow:hidden;border-radius:{$radius};grid-column:span var(--pw,1);grid-row:span var(--ph,1)}";
            echo ".{$uid} .olo-pg-item img{width:100%;height:100%;object-fit:{$object_fit};display:block}";
        } elseif ( $layout === 'diagonal' ) {
            echo ".{$uid}{display:grid;grid-template-columns:repeat({$cols},1fr);gap:{$gap}px}";
            echo ".{$uid} .olo-pg-item{position:relative;overflow:hidden;border-radius:{$radius};{$item_box}}";
            echo ".{$uid} .olo-pg-item:nth-child(odd){clip-path:polygon(0 0,100% 8%,100% 100%,0 92%)}";
            echo ".{$uid} .olo-pg-item:nth-child(even){clip-path:polygon(0 8%,100% 0,100% 92%,0 100%)}";
            echo ".{$uid} .olo-pg-item img{width:100%;height:100%;object-fit:{$object_fit};display:block}";
        } elseif ( $layout === 'expand' ) {
            $exp_rows_count = (int) ceil( min( $max_visible, $total ) / $cols );
            $exp_h_px       = intval( $img_height ) ?: 250;
            $exp_total_h    = $exp_rows_count * $exp_h_px + ( $exp_rows_count - 1 ) * $gap;
            $exp_speed_s    = number_format( $exp_speed / 1000, 2 );
            echo ".{$uid}{display:grid;grid-template-columns:repeat({$cols},1fr);grid-template-rows:repeat({$exp_rows_count},1fr);gap:{$gap}px;height:{$exp_total_h}px;transition:grid-template-columns {$exp_speed_s}s cubic-bezier(.25,.46,.45,.94),grid-template-rows {$exp_speed_s}s cubic-bezier(.25,.46,.45,.94)}";
            echo ".{$uid} .olo-pg-item{position:relative;overflow:hidden;border-radius:{$radius};min-width:0;min-height:0}";
            echo ".{$uid} .olo-pg-item img{width:100%;height:100%;object-fit:cover;display:block}";
        } elseif ( $layout === 'strip' || $layout === 'strip_collage' ) {
            // Nastro orizzontale (riga singola) — drag-to-scroll con momentum
            $fade_css = $strip_fade ? "mask-image:linear-gradient(to right,transparent,black var(--pg-fs,6%),black calc(100% - var(--pg-fe,6%)),transparent);-webkit-mask-image:linear-gradient(to right,transparent,black var(--pg-fs,6%),black calc(100% - var(--pg-fe,6%)),transparent);" : '';
            echo ".{$uid}{display:flex;gap:{$gap}px;overflow-x:auto;align-items:center;scrollbar-width:none;-ms-overflow-style:none;{$fade_css}}";
            echo ".{$uid}::-webkit-scrollbar{display:none}";
            echo ".{$uid} .olo-pg-item{flex:0 0 auto;width:{$strip_item_w}px;height:{$strip_height}px;position:relative;overflow:hidden;border-radius:{$radius}}";
            echo ".{$uid} .olo-pg-item img{width:100%;height:100%;object-fit:{$object_fit};display:block;-webkit-user-drag:none;user-select:none}";
        } elseif ( $layout === 'strip_multi' ) {
            // Nastro multi-riga — CSS Grid con N righe, scrollabile
            $fade_css = $strip_fade ? "mask-image:linear-gradient(to right,transparent,black var(--pg-fs,6%),black calc(100% - var(--pg-fe,6%)),transparent);-webkit-mask-image:linear-gradient(to right,transparent,black var(--pg-fs,6%),black calc(100% - var(--pg-fe,6%)),transparent);" : '';
            echo ".{$uid}{display:grid;grid-template-rows:repeat({$strip_rows_n},1fr);grid-auto-flow:column;grid-auto-columns:{$strip_item_w}px;gap:{$gap}px;overflow-x:auto;height:{$strip_height}px;scrollbar-width:none;-ms-overflow-style:none;{$fade_css}}";
            echo ".{$uid}::-webkit-scrollbar{display:none}";
            echo ".{$uid} .olo-pg-item{position:relative;overflow:hidden;border-radius:{$radius}}";
            echo ".{$uid} .olo-pg-item img{width:100%;height:100%;object-fit:{$object_fit};display:block;-webkit-user-drag:none;user-select:none}";
        } elseif ( $layout === 'strip_marquee' ) {
            // Nastro automatico — CSS animation loop infinito
            $fade_css = $strip_fade ? "mask-image:linear-gradient(to right,transparent,black var(--pg-fs,6%),black calc(100% - var(--pg-fe,6%)),transparent);-webkit-mask-image:linear-gradient(to right,transparent,black var(--pg-fs,6%),black calc(100% - var(--pg-fe,6%)),transparent);" : '';
            $dir_css = ( $strip_dir === 'right' ) ? 'animation-direction:reverse;' : '';
            echo ".{$uid}{overflow:hidden;{$fade_css}}";
            // padding-right = gap: il nastro (voci + copie) misura 2×N×(voce+gap) e il -50% del giro
            // cade esattamente sulla prima copia. Senza, mancava mezzo gap e il nastro saltava a ogni giro.
            echo ".{$uid}-track{display:flex;gap:{$gap}px;padding-right:{$gap}px;width:max-content;animation:{$uid}-marquee {$strip_speed}s linear infinite;{$dir_css}}";
            if ( $strip_pause ) {
                echo ".{$uid}:hover .{$uid}-track{animation-play-state:paused}";
            }
            echo ".{$uid} .olo-pg-item{flex:0 0 auto;width:{$strip_item_w}px;height:{$strip_height}px;position:relative;overflow:hidden;border-radius:{$radius}}";
            echo ".{$uid} .olo-pg-item img{width:100%;height:100%;object-fit:{$object_fit};display:block}";
            if ( $lightbox ) {
                echo ".{$uid} .olo-pg-dup{cursor:pointer}";
            } else {
                echo ".{$uid} .olo-pg-dup{pointer-events:none}";
            }
            echo "@keyframes {$uid}-marquee{to{transform:translateX(-50%)}}";
        } elseif ( $layout === 'strip_split' ) {
            // Nastro doppio — due righe in direzioni opposte
            $split_row_h = max( 80, (int) round( ( $strip_height - $gap ) / 2 ) );
            $fade_css = $strip_fade ? "mask-image:linear-gradient(to right,transparent,black var(--pg-fs,6%),black calc(100% - var(--pg-fe,6%)),transparent);-webkit-mask-image:linear-gradient(to right,transparent,black var(--pg-fs,6%),black calc(100% - var(--pg-fe,6%)),transparent);" : '';
            echo ".{$uid}{display:flex;flex-direction:column;gap:{$gap}px}";
            echo ".{$uid}-row{overflow:hidden;{$fade_css}}";
            echo ".{$uid}-track{display:flex;gap:{$gap}px;padding-right:{$gap}px;width:max-content;animation:{$uid}-marquee {$strip_speed}s linear infinite}";
            echo ".{$uid}-track-rev{animation-direction:reverse}";
            if ( $strip_pause ) {
                echo ".{$uid}:hover .{$uid}-track{animation-play-state:paused}";
            }
            echo ".{$uid} .olo-pg-item{flex:0 0 auto;width:{$strip_item_w}px;height:{$split_row_h}px;position:relative;overflow:hidden;border-radius:{$radius}}";
            echo ".{$uid} .olo-pg-item img{width:100%;height:100%;object-fit:{$object_fit};display:block}";
            if ( $lightbox ) {
                echo ".{$uid} .olo-pg-dup{cursor:pointer}";
            } else {
                echo ".{$uid} .olo-pg-dup{pointer-events:none}";
            }
            echo "@keyframes {$uid}-marquee{to{transform:translateX(-50%)}}";
        } elseif ( $layout === 'elenco' ) {
            // Elenco a righe: righe, colonne e miniatura le scrive schede_css()
            echo '';
        } else {
            // Default: grid
            echo ".{$uid}{display:grid;grid-template-columns:repeat({$cols},1fr);gap:{$gap}px}";
            echo ".{$uid} .olo-pg-item{position:relative;overflow:hidden;border-radius:{$radius};{$item_box}}";
            echo ".{$uid} .olo-pg-item img{width:100%;height:100%;object-fit:{$object_fit};display:block;transition:transform .5s cubic-bezier(.25,.46,.45,.94),filter .5s ease}";
        }

        // ─── CSS Filter ───
        if ( $css_filter ) {
            echo ".{$uid} .olo-pg-item img,.{$uid} .olo-pg-item video{filter:{$css_filter}}";
            if ( $filter_hover ) {
                echo ".{$uid} .olo-pg-item:hover img,.{$uid} .olo-pg-item:hover video{filter:none}";
            }
        }

        // ─── Duotone (SVG feComponentTransfer) ───
        if ( $filter === 'duotone' ) {
            echo ".{$uid} .olo-pg-item img,.{$uid} .olo-pg-item video{filter:url(#duo-{$uid});transition:filter .4s ease}";
            if ( $filter_hover ) {
                echo ".{$uid} .olo-pg-item:hover img,.{$uid} .olo-pg-item:hover video{filter:none}";
            }
        }

        // ─── Hover effects ───
        if ( $hover === 'zoom' ) {
            echo ".{$uid} .olo-pg-item img,.{$uid} .olo-pg-item video{transition:transform .5s cubic-bezier(.25,.46,.45,.94)}";
            echo ".{$uid} .olo-pg-item:hover img,.{$uid} .olo-pg-item:hover video{transform:scale({$hz_scale})}";
        } elseif ( $hover === 'lift' ) {
            // Niente `position` qui (né nel luccichio e nelle cornici più sotto): ogni schema la dà
            // già all'item, e un `position:relative` scritto DOPO scavalcava l'`absolute` di Sparso,
            // Parallasse, Deriva, Cascata, Esagoni e Puzzle. Gli item tornavano in colonna con le
            // misure in % pensate per stare sovrapposti: lo Sparso arrivava a migliaia di px.
            // L'overflow visibile (per non tagliare sollevamento e ombra) solo fuori dai nastri: scritto
            // dopo lo schema toglieva il taglio del Nastro automatico (il binario usciva dalla colonna
            // su tutta la pagina) e lo scorrimento di Coverflow e Nastro (frecce, pallini e trascinamento
            // non facevano più niente, le foto uscivano dalla colonna).
            if ( ! $is_strip ) {
                echo ".{$uid}{overflow:visible}";
            }
            if ( $is_coverflow ) {
                // Nel coverflow il transform della cornice è il suo posto nel giro, riscritto a ogni passo
                // dello scorrimento: il sollevamento usa la proprietà `translate`, che si somma (una
                // transizione sul transform farebbe arrivare ogni passo in ritardo di 0,4 s).
                echo ".{$uid} .olo-pg-item{transition:translate .4s ease,box-shadow .4s ease}";
                echo ".{$uid} .olo-pg-item:hover{translate:0 -8px;box-shadow:0 14px 32px rgba(0,0,0,.25)}";
            } else {
                echo ".{$uid} .olo-pg-item{transition:transform .4s ease,box-shadow .4s ease;z-index:0}";
                echo ".{$uid} .olo-pg-item:hover{transform:translateY(-8px);box-shadow:0 14px 32px rgba(0,0,0,.25);z-index:2}";
            }
        } elseif ( $hover === 'tilt3d' ) {
            // Nel coverflow si inclina la foto dentro la cornice (lo script), non la cornice
            if ( ! $is_coverflow ) {
                echo ".{$uid} .olo-pg-item{perspective:600px;transform-style:preserve-3d;transition:transform .4s ease}";
            }
            echo ".{$uid} .olo-pg-item img,.{$uid} .olo-pg-item video{transition:transform .4s ease}";
            // JS tilt handled by shared script
        } elseif ( $hover === 'glow' ) {
            // Convert hex to rgba for glow (un token della Palette si risolve nel suo colore)
            $glow_hex = Olobuild_Tile_Utils::colore_hex( $glow_color ) ?: '#e1474f';
            $gr = hexdec( substr( $glow_hex, 1, 2 ) );
            $gg = hexdec( substr( $glow_hex, 3, 2 ) );
            $gb = hexdec( substr( $glow_hex, 5, 2 ) );
            echo ".{$uid} .olo-pg-item{box-shadow:0 0 0 0 rgba({$gr},{$gg},{$gb},0);transition:box-shadow .4s ease}";
            echo ".{$uid} .olo-pg-item:hover{box-shadow:0 0 {$glow_spread}px 4px rgba({$gr},{$gg},{$gb},.45)}";
        } elseif ( $hover === 'blur-peers' ) {
            echo ".{$uid} .olo-pg-item{filter:blur(0);transition:filter .4s ease}";
            echo ".{$uid}:hover .olo-pg-item{filter:blur(3px)!important}";
            echo ".{$uid}:hover .olo-pg-item:hover{filter:blur(0)!important}";
            if ( $css_filter ) {
                echo ".{$uid}:hover .olo-pg-item:hover img{filter:none}";
            }
        } elseif ( $hover === 'magnetic' ) {
            echo $is_coverflow ? ".{$uid} .olo-pg-item img,.{$uid} .olo-pg-item video{transition:transform .15s ease-out}" : ".{$uid} .olo-pg-item{transition:transform .15s ease-out}";
            // JS magnetic handled by shared script
        }

        // (hover_frame_in migrata in bordi animati)

        // ─── Hover caption ───
        $hcap_weight = max( 100, min( 900, absint( $s['hover_caption_weight'] ?? 700 ) ) );
        if ( $hcaption !== 'none' ) {
            if ( $hcaption === 'centered' ) {
                echo ".{$uid} .olo-pg-cap{position:absolute;inset:0;display:flex;align-items:center;justify-content:center;text-align:center;color:{$hcap_color};font-size:16px;font-weight:{$hcap_weight};letter-spacing:.03em;text-shadow:0 1px 6px rgba(0,0,0,.7);background:transparent;opacity:0;transition:opacity .35s ease;z-index:3;pointer-events:none;padding:12px}";
                echo ".{$uid} .olo-pg-item:hover .olo-pg-cap{opacity:1}";
            } else {
                echo ".{$uid} .olo-pg-cap{position:absolute;left:0;right:0;bottom:0;padding:8px 12px;font-size:13px;line-height:1.3;color:{$hcap_color};background:{$hcap_bg};z-index:3;pointer-events:none}";
                if ( $hcaption === 'slide-up' ) {
                    echo ".{$uid} .olo-pg-cap{transform:translateY(100%);transition:transform .35s ease}";
                    echo ".{$uid} .olo-pg-item:hover .olo-pg-cap{transform:translateY(0)}";
                } elseif ( $hcaption === 'fade' ) {
                    echo ".{$uid} .olo-pg-cap{opacity:0;transition:opacity .35s ease}";
                    echo ".{$uid} .olo-pg-item:hover .olo-pg-cap{opacity:1}";
                } elseif ( $hcaption === 'overlay' ) {
                    echo ".{$uid} .olo-pg-cap{inset:0;display:flex;align-items:center;justify-content:center;text-align:center;opacity:0;transition:opacity .35s ease}";
                    echo ".{$uid} .olo-pg-item:hover .olo-pg-cap{opacity:1}";
                } elseif ( $hcaption === 'frame' ) {
                    echo ".{$uid} .olo-pg-cap{position:absolute;inset:{$frame_inset}px;display:flex;align-items:center;justify-content:center;text-align:center;border:2px solid rgba(255,255,255,.85);background:transparent;color:{$hcap_color};font-size:14px;font-weight:600;letter-spacing:.05em;opacity:0;transition:opacity .4s ease;z-index:3;pointer-events:none;padding:8px}";
                    echo ".{$uid} .olo-pg-item:hover .olo-pg-cap{opacity:1}";
                }

                /*
                 * LE TRE RIGHE, quando ci sono.
                 *
                 * Sta fuori dai rami qui sopra perché vale per tutti e cinque
                 * i modi di mostrare la didascalia: cambia dove sta il
                 * riquadro, non come si impilano le righe dentro.
                 *
                 * `flex-direction` serve ai modi che usano flex (centered,
                 * overlay, frame) e gli altri lo ignorano; `display:block`
                 * sulle span serve a quelli che flex non lo usano (slide-up,
                 * fade), dove altrimenti le tre righe finirebbero in fila.
                 */
                echo ".{$uid} .olo-pg-cap{flex-direction:column;gap:3px}";
                echo ".{$uid} .olo-pg-cap>span{display:block}";
                echo ".{$uid} .olo-pg-cap-s{font-size:.82em;font-weight:400;opacity:.85}";
                echo ".{$uid} .olo-pg-cap-x{font-size:.78em;font-weight:400;line-height:1.35;opacity:.72}";
            }
        }

        // ─── Continuous animation keyframes (multi-effect) ───
        // Categories: item-transform (float|drift), img-transform (breathe|rotate-slow|kenburns), shimmer (::after)
        // One per category max — first wins if conflict
        // NOTE: coverflow uses inline transforms on .olo-pg-item — skip item-level animations
        $skip_item_anim = ( $is_coverflow || $is_strip );
        if ( ! empty( $cont_effects ) ) {
            $item_anim = '';  // animation on .olo-pg-item
            $img_anim  = '';  // animation on .olo-pg-item img
            $has_shimmer = false;

            foreach ( $cont_effects as $ce ) {
                if ( $ce === 'float' && ! $item_anim && ! $skip_item_anim ) {
                    echo "@keyframes {$uid}-float{0%,100%{transform:translateY(0)}50%{transform:translateY(-8px)}}";
                    $item_anim = "{$uid}-float {$cont_speed}s ease-in-out infinite";
                } elseif ( $ce === 'drift' && ! $item_anim && ! $skip_item_anim ) {
                    echo "@keyframes {$uid}-drift{0%{transform:translate(0,0)}25%{transform:translate(4px,-3px)}50%{transform:translate(-3px,2px)}75%{transform:translate(2px,4px)}100%{transform:translate(0,0)}}";
                    $item_anim = "{$uid}-drift {$cont_speed}s ease-in-out infinite";
                } elseif ( $ce === 'breathe' && ! $img_anim ) {
                    echo "@keyframes {$uid}-breathe{0%,100%{transform:scale(1)}50%{transform:scale(1.03)}}";
                    $img_anim = "{$uid}-breathe {$cont_speed}s ease-in-out infinite";
                } elseif ( $ce === 'rotate-slow' && ! $img_anim ) {
                    echo "@keyframes {$uid}-rotslow{from{transform:rotate(0deg)}to{transform:rotate(360deg)}}";
                    $img_anim = "{$uid}-rotslow {$cont_speed}s linear infinite";
                } elseif ( $ce === 'kenburns' && ! $img_anim ) {
                    echo "@keyframes {$uid}-kb{0%{transform:scale(1) translate(0,0)}33%{transform:scale(1.12) translate(-2%,-1%)}66%{transform:scale(1.08) translate(1%,0.5%)}100%{transform:scale(1) translate(0,0)}}";
                    $img_anim = "{$uid}-kb {$cont_speed}s ease-in-out infinite";
                } elseif ( $ce === 'shimmer' && ! $has_shimmer ) {
                    echo "@keyframes {$uid}-shimmer{0%{transform:translateX(-100%) rotate(25deg)}35%{transform:translateX(200%) rotate(25deg)}100%{transform:translateX(200%) rotate(25deg)}}";
                    // L'item è già posizionato dallo schema (vedi «lift»): qui basta tagliare il riflesso.
                    echo ".{$uid} .olo-pg-item{overflow:hidden}";
                    echo ".{$uid} .olo-pg-item::after{content:'';position:absolute;inset:-50%;z-index:2;background:linear-gradient(90deg,transparent 30%,rgba(255,255,255,.12) 50%,transparent 70%);animation:{$uid}-shimmer {$cont_speed}s ease-in-out infinite;pointer-events:none}";
                    $has_shimmer = true;
                }
            }

            if ( $item_anim ) {
                echo ".{$uid} .olo-pg-item{animation:{$item_anim}}";
            }
            if ( $img_anim ) {
                echo ".{$uid} .olo-pg-item img,.{$uid} .olo-pg-item video{animation:{$img_anim}}";
            }

            // nth-child delay stagger for variety
            for ( $d = 1; $d <= 6; $d++ ) {
                $delay = round( $cont_speed / 6 * $d, 1 );
                echo ".{$uid} .olo-pg-item:nth-child({$d}n) img,.{$uid} .olo-pg-item:nth-child({$d}n) video,.{$uid} .olo-pg-item:nth-child({$d}n),.{$uid} .olo-pg-item:nth-child({$d}n)::after{animation-delay:-{$delay}s}";
            }
        }

        // ─── Frame styles ───
        if ( $frame === 'polaroid' ) {
            echo ".{$uid} .olo-pg-item{background:var(--pg-c,{$frame_color});padding:8px 8px 32px 8px;border-radius:2px;box-shadow:0 2px 8px rgba(0,0,0,.12)}";
            echo ".{$uid} .olo-pg-item img,.{$uid} .olo-pg-item video{border-radius:0}";
        } elseif ( $frame === 'rounded' ) {
            echo ".{$uid} .olo-pg-item{border-radius:50%;overflow:hidden}";
            echo ".{$uid} .olo-pg-item img,.{$uid} .olo-pg-item video{border-radius:50%}";
        } elseif ( $frame === 'shadow-box' ) {
            echo ".{$uid} .olo-pg-item{background:var(--pg-c,{$frame_color});padding:6px;box-shadow:0 4px 16px rgba(0,0,0,.18),inset 0 1px 0 rgba(255,255,255,.5);border-radius:" . ( $radius_raw + 2 ) . "px}";
        } elseif ( $frame === 'torn' ) {
            echo ".{$uid} .olo-pg-item{clip-path:polygon(0 2%,4% 0,10% 3%,18% 0,24% 2%,32% 0,40% 1%,48% 0,56% 2%,64% 0,72% 1%,80% 0,88% 2%,94% 0,100% 3%,97% 10%,100% 18%,97% 26%,100% 34%,97% 42%,100% 50%,97% 58%,100% 66%,97% 74%,100% 82%,97% 90%,100% 97%,96% 100%,90% 97%,82% 100%,76% 97%,68% 100%,60% 99%,52% 100%,44% 97%,36% 100%,28% 99%,20% 100%,12% 97%,6% 100%,0 97%,3% 90%,0 82%,3% 74%,0 66%,3% 58%,0 50%,3% 42%,0 34%,3% 26%,0 18%,3% 10%)}";
        } elseif ( $frame === 'tape' ) {
            // Nastro e cornice interna: l'item è già posizionato dallo schema (vedi «lift»).
            echo ".{$uid} .olo-pg-item::after{content:'';position:absolute;top:-6px;left:50%;transform:translateX(-50%);width:50px;height:18px;background:rgba(255,255,200,.7);border-radius:2px;z-index:4;box-shadow:0 1px 3px rgba(0,0,0,.1)}";
        } elseif ( $frame === 'pellicola' ) {
            // Pellicola: il fondo scuro (o il colore della cornice / della voce) con due file di fori VERI (una
            // maschera: dai fori si vede quello che c'è dietro), le bande alte e, sopra la foto, le scritte del bordo.
            // I fori si adattano alla larghezza di ogni fotogramma (round): nel nastro i fotogrammi si toccano e la
            // pellicola continua senza giunture. Coi testi sotto la foto la pellicola avvolge solo la foto.
            $fc    = $this->safe_color_css( $s['frame_color'] ?? '' );
            $base  = ( $fc && ! in_array( strtolower( $fc ), [ '#fff', '#ffffff' ], true ) ) ? $fc : 'var(--olo-color-dark)';
            $sotto = 'sotto' === ( $s['scheda'] ?? '' ) && in_array( $layout, [ 'grid', 'masonry', 'strip' ], true );
            $tgt   = $sotto ? ".{$uid} .olo-pg-media" : ".{$uid} .olo-pg-item";
            $foro  = "url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 40 18' preserveAspectRatio='none'%3E%3Crect x='12.5' y='0' width='15' height='18' rx='3.6' fill='%23000'/%3E%3C/svg%3E\")";
            $alto  = '' !== $pell_bordo ? '4.2rem' : '3.6rem';
            $maschera = "{$foro} 0 .8rem/2.5rem 1.15rem round no-repeat,{$foro} 0 calc(100% - .8rem)/2.5rem 1.15rem round no-repeat,linear-gradient(#000 0 0)";
            echo "{$tgt}{position:relative;box-sizing:border-box;background:var(--pg-c,{$base});padding:{$alto} .55rem 3.6rem;border-radius:0;-webkit-mask:{$maschera};-webkit-mask-composite:xor;mask:{$maschera};mask-composite:exclude}";
            echo "{$tgt} img,{$tgt} video{border-radius:0}";
            if ( $sotto ) {
                echo ".{$uid} .olo-pg-media img,.{$uid} .olo-pg-media video{position:relative;height:100%}";
                // I testi partono dove parte la foto, non dal bordo della pellicola
                echo ".{$uid} .olo-pg-sk{padding-left:.65rem;padding-right:.65rem}";
            }
            if ( 'strip' === $layout ) {
                // Nel nastro i fotogrammi si toccano: una sola striscia
                echo ".{$uid}{gap:0!important}";
            }
            if ( '' !== $pell_bordo ) {
                $cs = $this->safe_color_css( $s['pellicola_colore_scritte'] ?? '' ) ?: 'color-mix(in srgb,var(--olo-color-warning) 88%,#fff)';
                echo ".{$uid} .olo-pg-bordo{position:absolute;left:.65rem;right:.65rem;top:2.45rem;z-index:4;display:flex;align-items:baseline;gap:0;font-family:" . $this->resolve_font_family( 'mono' ) . ";font-size:10px;line-height:1;letter-spacing:.1em;color:{$cs};pointer-events:none;white-space:nowrap;overflow:hidden}";
                echo ".{$uid} .olo-pg-bordo b{font-weight:600}.{$uid} .olo-pg-bordo i{font-style:normal;margin-left:2.4em;opacity:.85}.{$uid} .olo-pg-bordo span{margin-left:auto;opacity:.85}";
            }
        } elseif ( $frame === 'filo' ) {
            // Filo: una linea sottile dentro la foto, nel colore della voce (le carte da collezione)
            $sp = max( 1, min( 6, absint( $s['frame_spessore'] ?? 1 ) ?: 1 ) );
            $fc = $this->safe_color_css( $s['frame_color'] ?? '' ) ?: 'color-mix(in srgb,#fff 55%,transparent)';
            echo ".{$uid} .olo-pg-item::before{content:'';position:absolute;inset:.5rem;border:{$sp}px solid var(--pg-c,{$fc});border-radius:" . max( 2, $radius_raw - 6 ) . "px;z-index:5;pointer-events:none}";
        } elseif ( $frame === 'bordo' ) {
            // Bordo pieno attorno alla foto e ai suoi testi, nel colore della voce (ogni ballerino il suo)
            $sp = max( 1, min( 16, absint( $s['frame_spessore'] ?? 6 ) ?: 6 ) );
            $fc = $this->safe_color_css( $s['frame_color'] ?? '' ) ?: 'var(--olo-color-primary)';
            echo ".{$uid} .olo-pg-item{border:{$sp}px solid var(--pg-c,{$fc});box-sizing:border-box}";
        } elseif ( $frame === 'inset' ) {
            $inset_pad = max( 3, min( 40, absint( $s['frame_inset_padding'] ) ) );
            echo ".{$uid} .olo-pg-item::before{content:'';position:absolute;inset:{$inset_pad}px;border:1px solid var(--pg-c,{$frame_color});border-radius:" . max( 0, $radius_raw - $inset_pad ) . "px;z-index:5;pointer-events:none}";
        }

        // ─── Ombra delle foto ───
        // Il controllo «Ombra» si leggeva e non si disegnava: un controllo che non faceva niente (3 nov 2026). Va su
        // ogni foto, dopo le cornici, così quella scelta vince sull'ombra di serie di Polaroid, Riquadro ombra, Sparso
        // e Cascata; parallasse e deriva la scrivono in linea (cambia con la profondità) e restano come sono. Nei
        // nastri, che tagliano quello che esce dalla riga, sopra e sotto si lascia lo spazio per l'ombra.
        if ( 'none' !== $shadow ) {
            // Coi testi sotto la foto e senza fondo, l'ombra è della foto: i testi stanno sulla pagina
            $ombra_foto = 'sotto' === $sk['modo'] && '' === (string) ( $s['scheda_sfondo'] ?? '' ) && empty( $s['scheda_sfondo_voce'] );
            echo $ombra_foto ? ".{$uid} .olo-pg-media{box-shadow:{$shadow}}" : ".{$uid} .olo-pg-item{box-shadow:{$shadow}}";
            if ( $is_strip && ! $is_coverflow ) {
                echo ".{$uid}" . ( 'strip_split' === $layout ? '-row' : '' ) . "{padding-top:.875rem;padding-bottom:2.375rem}";
            }
        }

        // ─── Bordi animati ───
        if ( $anim_border === 'frame-in' ) {
            // Cornice entrante — bordo che appare al hover dall'esterno verso l'interno
            echo ".{$uid} .olo-pg-item::before{content:'';position:absolute;inset:0;border:2px solid transparent;z-index:4;pointer-events:none;transition:inset .4s ease,border-color .3s ease;border-radius:{$radius}}";
            echo ".{$uid} .olo-pg-item:hover::before{inset:{$ab_inset}px;border-color:{$ab_color};border-radius:" . max( 0, $radius_raw - $ab_inset ) . "px}";
        } elseif ( $anim_border === 'neon' ) {
            // Bagliore neon pulsante via box-shadow
            echo "@keyframes {$uid}-neon{0%,100%{box-shadow:0 0 {$ab_thick}px {$ab_color},0 0 " . ( $ab_thick * 4 ) . "px {$ab_color},0 0 " . ( $ab_thick * 8 ) . "px {$ab_color}}50%{box-shadow:0 0 " . ( $ab_thick * 2 ) . "px {$ab_color},0 0 " . ( $ab_thick * 6 ) . "px {$ab_color},0 0 " . ( $ab_thick * 12 ) . "px {$ab_color}}}";
            echo ".{$uid} .olo-pg-item{animation:{$uid}-neon {$ab_speed}s ease-in-out infinite}";
        } elseif ( $anim_border === 'ants' ) {
            // Formiche (marching ants) — padding crea spazio, gradient sui 4 lati dell'item
            $dLen = $ab_thick * 6;
            echo "@keyframes {$uid}-ants{100%{background-position:{$dLen}px 0,calc(100% - {$dLen}px) 100%,0 calc(100% - {$dLen}px),100% {$dLen}px}}";
            echo ".{$uid} .olo-pg-item{padding:{$ab_thick}px;background-image:linear-gradient(90deg,{$ab_color} 50%,transparent 50%),linear-gradient(90deg,{$ab_color} 50%,transparent 50%),linear-gradient(0deg,{$ab_color} 50%,transparent 50%),linear-gradient(0deg,{$ab_color} 50%,transparent 50%);background-size:{$dLen}px {$ab_thick}px,{$dLen}px {$ab_thick}px,{$ab_thick}px {$dLen}px,{$ab_thick}px {$dLen}px;background-position:0 0,100% 100%,0 100%,100% 0;background-repeat:repeat-x,repeat-x,repeat-y,repeat-y;animation:{$uid}-ants {$ab_speed}s linear infinite}";
        } elseif ( $anim_border === 'corners' ) {
            // Angoli — padding crea spazio, 8 gradient per 4 angoli L
            $cLen = 20 + $ab_thick * 4;
            echo ".{$uid} .olo-pg-item{padding:{$ab_thick}px;background-image:linear-gradient({$ab_color},{$ab_color}),linear-gradient({$ab_color},{$ab_color}),linear-gradient({$ab_color},{$ab_color}),linear-gradient({$ab_color},{$ab_color}),linear-gradient({$ab_color},{$ab_color}),linear-gradient({$ab_color},{$ab_color}),linear-gradient({$ab_color},{$ab_color}),linear-gradient({$ab_color},{$ab_color});background-size:0 {$ab_thick}px,{$ab_thick}px 0,0 {$ab_thick}px,{$ab_thick}px 0,0 {$ab_thick}px,{$ab_thick}px 0,0 {$ab_thick}px,{$ab_thick}px 0;background-position:0 0,0 0,100% 0,100% 0,100% 100%,100% 100%,0 100%,0 100%;background-repeat:no-repeat;transition:background-size .4s ease}";
            echo ".{$uid} .olo-pg-item:hover{background-size:{$cLen}px {$ab_thick}px,{$ab_thick}px {$cLen}px,{$cLen}px {$ab_thick}px,{$ab_thick}px {$cLen}px,{$cLen}px {$ab_thick}px,{$ab_thick}px {$cLen}px,{$cLen}px {$ab_thick}px,{$ab_thick}px {$cLen}px}";
        } elseif ( $anim_border === 'pulse' ) {
            // Pulsazione — bordo che appare e scompare
            echo "@keyframes {$uid}-pulse{0%,100%{box-shadow:inset 0 0 0 0 transparent}50%{box-shadow:inset 0 0 0 {$ab_thick}px {$ab_color}}}";
            echo ".{$uid} .olo-pg-item{animation:{$uid}-pulse {$ab_speed}s ease-in-out infinite}";
        } elseif ( $anim_border === 'radar' ) {
            // Radar — onda box-shadow che si espande dall'item
            $radar_spread = $ab_thick * 3;
            echo "@keyframes {$uid}-radar{0%{box-shadow:0 0 0 0 {$ab_color}}100%{box-shadow:0 0 0 {$radar_spread}px transparent}}";
            echo ".{$uid} .olo-pg-item{animation:{$uid}-radar {$ab_speed}s ease-out infinite}";
        }

        // ─── "+N" overlay ───
        echo ".{$uid} .olo-pg-more{position:absolute;inset:0;z-index:5;display:flex;align-items:center;justify-content:center;background:{$more_bg};color:{$more_color};font-size:{$more_size}px;font-weight:700;pointer-events:none;border-radius:{$radius}}";

        // ─── Hidden lightbox items ───
        echo ".{$uid} .olo-pg-hidden{position:absolute;width:0;height:0;overflow:hidden;pointer-events:none;opacity:0}";

        // ─── Entrance: overflow visible per permettere animazioni off-screen ───
        if ( $entrance !== 'none' && ! $is_coverflow && ! $is_strip ) {
            echo ".{$uid}{overflow:visible}";
            // Dopo l'animazione, ripristina overflow hidden per evitare scrollbar; con l'ombra resta aperta, se no
            // l'ombra delle foto sul bordo finiva tagliata (un'ombra non fa comparire barre di scorrimento)
            echo ".{$uid}.olo-pg-visible{overflow:" . ( 'none' !== $shadow ? 'visible' : 'hidden' ) . '}';
        }

        // ─── Entrance animation ───
        if ( $entrance !== 'none' ) {
            $dur_s = number_format( $ent_dur / 1000, 2 );
            $initial_transform = 'translateY(24px)';
            $initial_extra = '';

            $is_special_entrance = in_array( $entrance, [ 'split-sides', 'fall', 'wind', 'zoom-center', 'land' ], true );

            if ( $entrance === 'fade-up' ) {
                $initial_transform = 'translateY(100vh)';
            } elseif ( $entrance === 'fade-scale' ) {
                $initial_transform = 'translateY(60vh) scale(.7)';
            } elseif ( $entrance === 'flip' ) {
                $initial_transform = 'perspective(600px) rotateX(90deg) translateY(40vh)';
                $initial_extra = 'transform-origin:center bottom;';
            } elseif ( $entrance === 'slide-in' ) {
                $initial_transform = 'translateX(-100vw)';
            } elseif ( $entrance === 'blur-in' ) {
                $initial_transform = 'translateY(60vh)';
                $initial_extra = 'filter:blur(12px);';
            } elseif ( $entrance === 'split-sides' ) {
                $initial_transform = 'translateX(-100vw) rotate(-8deg)';
            } elseif ( $entrance === 'fall' ) {
                $initial_transform = 'translateY(-100vh) rotate(12deg) scale(.8)';
            } elseif ( $entrance === 'wind' ) {
                $initial_transform = 'translateX(100vw) rotate(18deg) scale(.85)';
                $initial_extra = 'filter:blur(6px);';
            } elseif ( $entrance === 'zoom-center' ) {
                $initial_transform = 'scale(.08)';
                $initial_extra = 'filter:blur(8px);';
            } elseif ( $entrance === 'land' ) {
                $initial_transform = 'perspective(800px) translateZ(600px) translateY(-60vh) scale(1.8)';
                $initial_extra = 'filter:blur(4px);';
            }

            // Coverflow / Strip: entrance solo con opacity (transform gestito dal JS o dal CSS animation)
            if ( $is_coverflow || $is_strip ) {
                echo ".{$uid} .olo-pg-item{opacity:0;transition:opacity {$dur_s}s ease}";
                echo ".{$uid}.olo-pg-visible .olo-pg-item{opacity:1}";
            } elseif ( $is_special_entrance ) {
                // ─── Split sides ───
                if ( $entrance === 'split-sides' ) {
                    echo ".{$uid} .olo-pg-item{opacity:0;transition:opacity {$dur_s}s ease,transform {$dur_s}s cubic-bezier(.25,.46,.45,.94)}";
                    // Dispari: entrano da sinistra fuori schermo
                    echo ".{$uid} .olo-pg-item:nth-child(odd){transform:translateX(-100vw) rotate(-8deg)}";
                    // Pari: entrano da destra fuori schermo
                    echo ".{$uid} .olo-pg-item:nth-child(even){transform:translateX(100vw) rotate(8deg)}";
                    echo ".{$uid}.olo-pg-visible .olo-pg-item{opacity:1;transform:translateX(0) rotate(0deg)}";
                }
                // ─── Fall (caduta da fuori schermo con bounce) ───
                if ( $entrance === 'fall' ) {
                    $bounce_dur = number_format( $ent_dur / 1000 * 1.5, 2 );
                    echo "@keyframes {$uid}-fall{0%{opacity:0;transform:translateY(-100vh) rotate(12deg) scale(.8)}45%{opacity:1;transform:translateY(12px) rotate(-2deg) scale(1.03)}65%{transform:translateY(-6px) rotate(1deg) scale(1)}80%{transform:translateY(3px) rotate(0deg)}100%{opacity:1;transform:translateY(0) rotate(0deg) scale(1)}}";
                    echo ".{$uid} .olo-pg-item{opacity:0;transform:translateY(-100vh) rotate(12deg) scale(.8)}";
                    echo ".{$uid}.olo-pg-visible .olo-pg-item{animation:{$uid}-fall {$bounce_dur}s cubic-bezier(.22,.68,.36,1) forwards}";
                }
                // ─── Wind (soffiate dal vento da fuori schermo) ───
                if ( $entrance === 'wind' ) {
                    $wind_dur = number_format( $ent_dur / 1000 * 1.4, 2 );
                    echo "@keyframes {$uid}-wind{0%{opacity:0;transform:translateX(100vw) rotate(18deg) scale(.85);filter:blur(6px)}55%{opacity:1;transform:translateX(-12px) rotate(-3deg) scale(1.02);filter:blur(0)}75%{transform:translateX(5px) rotate(1deg) scale(1)}100%{opacity:1;transform:translateX(0) rotate(0deg) scale(1);filter:blur(0)}}";
                    echo ".{$uid} .olo-pg-item{opacity:0;transform:translateX(100vw) rotate(18deg) scale(.85);filter:blur(6px)}";
                    echo ".{$uid}.olo-pg-visible .olo-pg-item{animation:{$uid}-wind {$wind_dur}s cubic-bezier(.25,.46,.45,.94) forwards}";
                }
                // ─── Zoom center (arrivano da un punto distante al centro) ───
                if ( $entrance === 'zoom-center' ) {
                    $zc_dur = number_format( $ent_dur / 1000 * 1.3, 2 );
                    // Ogni item converge dal centro assoluto della galleria verso la sua posizione finale
                    // Usiamo scale molto piccola + translate che porta al centro
                    echo "@keyframes {$uid}-zc{0%{opacity:0;transform:scale(.05);filter:blur(10px)}40%{opacity:.6;filter:blur(3px)}70%{opacity:1;transform:scale(1.04);filter:blur(0)}85%{transform:scale(.98)}100%{opacity:1;transform:scale(1);filter:blur(0)}}";
                    echo ".{$uid} .olo-pg-item{opacity:0;transform:scale(.05);filter:blur(10px);transform-origin:center center}";
                    echo ".{$uid}.olo-pg-visible .olo-pg-item{animation:{$uid}-zc {$zc_dur}s cubic-bezier(.16,1,.3,1) forwards}";
                }
                // ─── Land (atterrano sullo schermo da fuori, con prospettiva 3D) ───
                if ( $entrance === 'land' ) {
                    $land_dur = number_format( $ent_dur / 1000 * 1.6, 2 );
                    echo "@keyframes {$uid}-land{0%{opacity:0;transform:perspective(800px) translateZ(600px) translateY(-80vh) rotateX(-15deg) scale(1.6);filter:blur(6px)}35%{opacity:.8;filter:blur(2px)}55%{opacity:1;transform:perspective(800px) translateZ(0) translateY(6px) rotateX(2deg) scale(1.02);filter:blur(0)}72%{transform:perspective(800px) translateZ(0) translateY(-3px) rotateX(-1deg) scale(1)}86%{transform:perspective(800px) translateZ(0) translateY(1px) rotateX(0) scale(1)}100%{opacity:1;transform:perspective(800px) translateZ(0) translateY(0) rotateX(0) scale(1);filter:blur(0)}}";
                    echo ".{$uid} .olo-pg-item{opacity:0;transform:perspective(800px) translateZ(600px) translateY(-80vh) rotateX(-15deg) scale(1.6);filter:blur(6px)}";
                    echo ".{$uid}.olo-pg-visible .olo-pg-item{animation:{$uid}-land {$land_dur}s cubic-bezier(.22,.68,.36,1) forwards}";
                }
            } else {
                echo ".{$uid} .olo-pg-item{opacity:0;transform:{$initial_transform};{$initial_extra}transition:opacity {$dur_s}s ease,transform {$dur_s}s ease,filter {$dur_s}s ease,box-shadow {$dur_s}s ease}";
                echo ".{$uid}.olo-pg-visible .olo-pg-item{opacity:1;filter:none}";

                // Hover effects that set transform on the item need :not(:hover) so hover transform wins
                $hover_uses_item_transform = in_array( $hover, [ 'lift', 'tilt3d', 'magnetic' ], true );
                if ( $hover_uses_item_transform ) {
                    echo ".{$uid}.olo-pg-visible .olo-pg-item:not(:hover){transform:none}";
                } else {
                    echo ".{$uid}.olo-pg-visible .olo-pg-item{transform:none}";
                }
            }

            // Stagger delays
            $vis_count = min( $max_visible, $total );
            for ( $i = 0; $i < $vis_count && $i < 30; $i++ ) {
                $delay_s = number_format( ( $ent_stagger * $i ) / 1000, 2 );
                // Per fall e wind: animation-delay invece di transition-delay
                if ( in_array( $entrance, [ 'fall', 'wind', 'zoom-center', 'land' ], true ) ) {
                    echo ".{$uid} .olo-pg-item:nth-child(" . ( $i + 1 ) . "){animation-delay:{$delay_s}s}";
                } else {
                    echo ".{$uid} .olo-pg-item:nth-child(" . ( $i + 1 ) . "){transition-delay:{$delay_s}s}";
                }
            }

            // Restore hover transitions after entrance completes (override entrance slow transition)
            if ( $hover === 'lift' ) {
                echo ".{$uid}.olo-pg-visible .olo-pg-item{transition:transform .4s ease,box-shadow .4s ease}";
            } elseif ( $hover === 'tilt3d' ) {
                echo ".{$uid}.olo-pg-visible .olo-pg-item{transition:transform .4s ease}";
            } elseif ( $hover === 'magnetic' ) {
                echo ".{$uid}.olo-pg-visible .olo-pg-item{transition:transform .15s ease-out}";
            } elseif ( $hover === 'glow' ) {
                echo ".{$uid}.olo-pg-visible .olo-pg-item{transition:box-shadow .4s ease}";
            } elseif ( $hover === 'blur-peers' ) {
                echo ".{$uid}.olo-pg-visible .olo-pg-item{transition:filter .4s ease}";
            }
        }

        // ─── Mobile ───
        if ( $layout === 'masonry' ) {
            echo "@media(max-width:640px){.{$uid}{column-count:{$mob_cols}}}";
        } elseif ( $layout === 'expand' ) {
            echo "@media(max-width:640px){.{$uid}{grid-template-columns:repeat({$mob_cols},1fr);grid-template-rows:none;height:auto;transition:none}.{$uid} .olo-pg-item{height:{$img_height}}}";
        } elseif ( $layout === 'justified' ) {
            echo "@media(max-width:640px){.{$uid} .olo-pg-item{min-width:80px;height:" . ( intval( $img_height ) > 0 ? max( 100, intval( $img_height ) - 60 ) . 'px' : '140px' ) . "}}";
        } elseif ( ! $is_coverflow && $layout !== 'scattered' && $layout !== 'parallax' && $layout !== 'drift' && $layout !== 'cascade' && $layout !== 'honeycomb' && $layout !== 'hexgrid' && $layout !== 'puzzle' && ! $is_strip ) {
            echo "@media(max-width:640px){.{$uid}{grid-template-columns:repeat({$mob_cols},1fr)}}";
        }

        // ─── Parallax mobile ───
        if ( $layout === 'parallax' ) {
            $mob_plx_h = max( 600, (int) round( $plx_height * 0.55 ) );
            echo "@media(max-width:640px){.{$uid}{height:{$mob_plx_h}px}}";
        }

        // ─── Drift mobile ───
        if ( $layout === 'drift' ) {
            $mob_drift_h = max( 500, (int) round( $drift_height * 0.55 ) );
            echo "@media(max-width:640px){.{$uid}{height:{$mob_drift_h}px}.{$uid} .olo-pg-item{width:min(calc(var(--dw,20%) * 2),62%)!important}}";
        }

        // ─── Cascade mobile ───
        if ( $layout === 'cascade' ) {
            echo "@media(max-width:640px){.{$uid}{height:auto!important;perspective:none}.{$uid} .olo-pg-item{position:relative!important;width:88%!important;height:auto!important;aspect-ratio:4/3;left:auto!important;top:auto!important;margin:.9rem auto!important;transform:none!important}.{$uid} .olo-pg-item:nth-child(odd){transform:rotate(-1.5deg)!important}.{$uid} .olo-pg-item:nth-child(even){transform:rotate(1.5deg)!important}}";
        }

        // ─── Strip mobile ─── (non il coverflow: la larghezza delle sue foto la decide lo script)
        if ( $is_strip && ! $is_coverflow ) {
            $mob_strip_w = max( 150, $strip_item_w - 80 );
            echo "@media(max-width:640px){.{$uid} .olo-pg-item{width:{$mob_strip_w}px}}";
        }

        // ─── Reduced motion ───
        echo "@media(prefers-reduced-motion:reduce){";
        echo ".{$uid},.{$uid} .olo-pg-item,.{$uid} .olo-pg-item img,.{$uid} .olo-pg-item video,.{$uid}-track{animation:none!important;transition:none!important}";
        if ( $entrance !== 'none' ) {
            echo ".{$uid} .olo-pg-item{opacity:1!important;transform:none!important;filter:none!important}";
        }
        if ( $is_strip_auto ) {
            echo ".{$uid}-track{transform:none!important}";
        }
        if ( $layout === 'parallax' || $layout === 'drift' || $layout === 'cascade' ) {
            echo ".{$uid} .olo-pg-item{will-change:auto!important}";
        }
        echo "}";

        // ─── Video styles ───
        echo ".{$uid} .olo-pg-item video{width:100%;height:100%;object-fit:{$object_fit};display:block}";
        echo ".{$uid} .olo-pg-play{position:absolute;inset:0;display:flex;align-items:center;justify-content:center;pointer-events:none;z-index:2}";
        echo ".{$uid} .olo-pg-play::before{content:'';width:44px;height:44px;border-radius:50%;background:rgba(0,0,0,.5) url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='white'%3E%3Cpolygon points='9,5 20,12 9,19'/%3E%3C/svg%3E\") center/20px 20px no-repeat}";
        echo ".{$uid} .olo-pg-no-poster{width:100%;height:100%;background:#1F2937;display:flex;align-items:center;justify-content:center}";

        // Metro, Mosaico, Collage al telefono: le misure calcolate per le colonne del telefono
        if ( $piastre_m ) {
            echo "@media(max-width:640px){.{$uid} .olo-pg-item{grid-column:span var(--pwm,1);grid-row:span var(--phm,1)}}";
        }
        echo $this->schede_css( $uid, $sk, $s, $radius, 'strip' === $layout ? "height:{$strip_height}px" : $item_box, $layout );
        if ( '' !== $sk['modo'] ) {
            echo $this->rifiniture_css( $uid, $sk, $s, $radius, $layout );
        } elseif ( ! empty( $s['griglia_linee'] ) || $ha_etichette ) {
            echo $this->rifiniture_css( $uid, $sk, $s, $radius, $layout );
        }
        if ( 'strip' === $layout && 'sotto' === $sk['modo'] ) {
            // Testi sotto i fotogrammi: le voci in alto, le frecce a metà della foto
            echo ".{$uid}{align-items:flex-start}.{$uid}-sa-btn{top:" . (int) round( $strip_height / 2 ) . 'px}';
        }
        if ( $ha_testi ) {
            echo $this->testi_css( $uid, $s, $radius, $item_box, $layout, $sk );
        }
        if ( $ha_segni ) {
            // Il segno: una linea a mano libera (maschera SVG) nel colore della voce o del sito
            // Riquadro: il pennarello pulito attorno al fotogramma, come sui provini; matita e cerchio a mano libera
            $forme = [
                'matita'  => "M3 4.5 L97 2.5 L98 96.5 L2.5 97.5 Z M6 3 L95 5.5",
                'cerchio' => "M52 4 C82 3 98 24 97 51 C96 81 74 97 47 96 C19 95 3 76 4 49 C5 23 24 6 57 7",
            ];
            $ins = 'pellicola' === $frame ? ( '' !== $pell_bordo ? '3.95rem .3rem 3.35rem' : '3.35rem .3rem' ) : '.4rem';
            echo ".{$uid} .olo-pg-segno{position:absolute;inset:{$ins};z-index:5;pointer-events:none;background:var(--pg-c,var(--olo-color-primary))}";
            echo ".{$uid} .olo-pg-segno-riquadro{background:none;border:3px solid var(--pg-c,var(--olo-color-primary));border-radius:.5rem;box-shadow:0 0 0 1px color-mix(in srgb,var(--pg-c,var(--olo-color-primary)) 35%,transparent)}";
            foreach ( $forme as $f => $d ) {
                $m = "url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100' preserveAspectRatio='none'%3E%3Cpath d='{$d}' fill='none' stroke='black' stroke-width='3.5' stroke-linecap='round' stroke-linejoin='round' vector-effect='non-scaling-stroke'/%3E%3C/svg%3E\")";
                echo ".{$uid} .olo-pg-segno-{$f}{-webkit-mask:{$m} center/100% 100% no-repeat;mask:{$m} center/100% 100% no-repeat}";
            }
        }
        if ( $is_strip_drag ) {
            // Le frecce si spengono all'inizio e alla fine del nastro; la barretta segue lo scorrimento
            echo ".{$uid}-sa-wrap .olo-pg-sa-off{opacity:0!important;pointer-events:none}";
            echo ".{$uid}+.olo-pg-avanza{display:block;width:4rem;height:.1875rem;margin:1.5rem auto 0;background:color-mix(in srgb,currentColor 18%,transparent);border-radius:2px;overflow:hidden}";
            echo ".{$uid}+.olo-pg-avanza span{display:block;width:30%;height:100%;background:currentColor;border-radius:inherit}";
        }
        echo $this->anima_css( $uid, $s, $sk );
        if ( $filtri_on && 'menu' !== $bar['filtri_stile'] ) {
            echo $this->filtri_css( $uid, $s );
        }
        if ( $dati_on ) {
            echo $this->barra_css( $uid, $s, $bar );
            if ( $filtri_lato ) {
                echo $this->lato_css( $uid ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS fisso costruito sull'uid interno.
            }
            // Classe ripetuta: il biglietto mette le card in display:grid!important (.uid .olo-pg-item:not(...)),
            // che batteva .uid .olo-pg-via e il filtro non nascondeva niente (23 nov 2026, «qui il filtro non va»)
            echo ".{$uid}.{$uid} .olo-pg-item.olo-pg-via,.{$uid}.{$uid} .olo-pg-via{display:none!important}";
        }
        // Colonne del tablet (Griglia, Diagonale, Masonry): fra il telefono e il computer
        // Schermi grandi (da 1400 px) e telefoni piccoli (sotto i 480 px): cinque misure in tutto
        foreach ( [ [ 'large_columns', '(min-width:1400px)' ], [ 'small_columns', '(max-width:479px)' ] ] as $mq ) {
            $nc = absint( $s[ $mq[0] ] ?? 0 );
            if ( $nc >= 1 && $nc <= 8 ) {
                if ( in_array( $layout, [ 'grid', 'diagonal' ], true ) ) {
                    echo "@media{$mq[1]}{.{$uid}{grid-template-columns:repeat({$nc},1fr)}}";
                } elseif ( 'masonry' === $layout ) {
                    echo "@media{$mq[1]}{.{$uid}{column-count:{$nc}}}";
                }
            }
        }
        $tab_cols = absint( $s['tablet_columns'] ?? 0 );
        if ( $tab_cols >= 1 && $tab_cols <= 6 ) {
            if ( in_array( $layout, [ 'grid', 'diagonal' ], true ) ) {
                echo "@media(min-width:640px) and (max-width:1199px){.{$uid}{grid-template-columns:repeat({$tab_cols},1fr)}}";
            } elseif ( 'masonry' === $layout ) {
                echo "@media(min-width:640px) and (max-width:1199px){.{$uid}{column-count:{$tab_cols}}}";
            }
        }

        echo '</style>';
        // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped

        // ─── Shared JS (once per page) ───
        $needs_tilt3d   = ( $hover === 'tilt3d' );
        $needs_magnetic = ( $hover === 'magnetic' );
        $needs_entrance = ( $entrance !== 'none' );
        $needs_filmstrip = $is_coverflow;
        $needs_expand   = ( $layout === 'expand' );
        $needs_strip    = $is_strip_drag;
        $needs_parallax = ( $layout === 'parallax' );
        $needs_drift    = ( $layout === 'drift' );
        $needs_cascade  = ( $layout === 'cascade' );
        self::maybe_output_script( $needs_tilt3d, $needs_magnetic, $needs_entrance, $needs_filmstrip, $needs_expand, $needs_strip, $needs_parallax, $needs_drift, $needs_cascade );

        // ─── Container attrs ───
        $container_class = esc_attr( $uid ) . ' olo-pgal-preset-' . esc_attr( sanitize_key( $s['preset'] ?? 'custom' ) );
        $anima_on        = in_array( $s['scheda_anima'] ?? '', [ 'sale', 'lato', 'maschera', 'scala', 'sfuma' ], true );
        if ( $anima_on ) {
            $container_class .= ' olo-pg-anima';
        }
        // Le parti attorno e dentro la galleria (cornice del coverflow, frecce, indicatore, righe e
        // nastri che scorrono) prendono la classe dall'uid DA SOLO. Costruite da $container_class
        // ricevevano «olo-pg-N olo-pgal-preset-custom-track»: l'uid intero, che le trattava tutte da
        // galleria (cornice e frecce in fila nel flex del coverflow, il nastro doppio impilato in
        // colonna), e un «-track» attaccato al preset, che nessuna regola .olo-pg-N-track trovava
        // (nastro automatico fermo, script del coverflow senza frecce né indicatore).
        $uid_attr = esc_attr( $uid );
        $data_attrs = '';
        if ( $needs_entrance ) {
            $data_attrs .= ' data-pg-reveal="1"';
        }
        if ( $needs_tilt3d ) {
            $data_attrs .= ' data-pg-tilt="' . $tilt_angle . '"';
        }
        if ( $needs_magnetic ) {
            $data_attrs .= ' data-pg-magnetic="' . $mag_str . '"';
        }
        if ( $needs_filmstrip ) {
            $data_attrs .= ' data-pg-filmstrip="1"';
            $data_attrs .= ' data-pg-film-zoom="' . $film_zoom . '"';
            $data_attrs .= ' data-pg-film-tilt="' . $film_tilt . '"';
            $data_attrs .= ' data-pg-film-width="' . $film_width . '"';
            $data_attrs .= ' data-pg-film-dots="' . esc_attr( $film_dots ) . '"';
            if ( $film_auto ) {
                $data_attrs .= ' data-pg-film-auto="' . $film_speed . '"';
            }
        }
        if ( $needs_expand ) {
            $data_attrs .= ' data-pg-expand="1" data-pg-expand-ratio="' . $exp_ratio . '" data-pg-expand-shrink="' . $exp_shrink . '"';
        }
        if ( $is_strip_drag ) {
            $data_attrs .= ' data-pg-strip="1"';
            if ( ! empty( $s['strip_avanzamento'] ) ) {
                $data_attrs .= ' data-pg-strip-av="1"';
            }
            if ( $strip_fade ) {
                $data_attrs .= ' data-pg-strip-fade="1"';
            }
        }
        if ( $needs_parallax ) {
            $data_attrs .= ' data-pg-parallax="1" data-pg-plx-intensity="' . $plx_intensity . '"';
        }
        if ( $needs_drift ) {
            $data_attrs .= ' data-pg-drift="1" data-pg-drift-intensity="' . $drift_intensity . '" data-pg-drift-rotation="' . $drift_rotation . '"';
        }
        if ( $needs_cascade ) {
            $data_attrs .= ' data-pg-cascade="1" data-pg-cascade-spread="' . $cascade_spread . '"';
        }

        // Lightbox wrapper
        if ( $lb_custom ) {
            $lb_json = wp_json_encode( [ 'position' => $lb_thumbs, 'rows' => $lb_rows ] );
            $lb_attr = ' data-olo-lb="' . esc_attr( $lb_json ) . '"';
        } elseif ( $lightbox ) {
            $lb_attr = ' uk-lightbox="animation: ' . $lb_anim . '; toggle: a:not(.olo-pg-link)"';
        } else {
            $lb_attr = '';
        }

        // Hexgrid container needs padding-bottom for aspect ratio
        $hex_inline = '';
        if ( $layout === 'hexgrid' ) {
            $hex_w     = 100 / ( $cols + 0.5 );
            $hex_h     = $hex_w * 1.1547;
            $row_step  = $hex_h * 0.75;
            $hex_rows  = (int) ceil( min( $max_visible, $total ) / $cols );
            $container_h = round( $row_step * ( $hex_rows - 1 ) + $hex_h, 2 );
            $hex_inline = ' style="padding-bottom:' . $container_h . '%"';
        }

        // ─── Duotone SVG filter ───
        if ( $filter === 'duotone' ) {
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG filter markup built only from number_format() floats and a generated unique id.
            echo '<svg style="position:absolute;width:0;height:0;overflow:hidden"><filter id="duo-' . $uid . '" color-interpolation-filters="sRGB"><feColorMatrix type="saturate" values="0" in="SourceGraphic" result="gray"/><feComponentTransfer in="gray" result="duotone"><feFuncR type="table" tableValues="' . $dr . ' ' . $lr . '"/><feFuncG type="table" tableValues="' . $dg . ' ' . $lg . '"/><feFuncB type="table" tableValues="' . $db . ' ' . $lb . '"/></feComponentTransfer><feBlend in="duotone" in2="SourceGraphic" mode="multiply" result="blended"/><feComposite in="blended" in2="SourceGraphic" operator="arithmetic" k1="0" k2="' . $duo_k2 . '" k3="' . $duo_k3 . '" k4="0"/></filter></svg>';
        }

        // Coverflow wrapper (outside lightbox container)
        if ( $is_coverflow ) {
            echo '<div class="' . $uid_attr . '-wrap" tabindex="0">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $uid_attr is esc_attr()'d above.
        }

        // ─── Strip arrows wrapper ───
        // La barra (filtri, ricerca) va sopra il nastro, fuori dal riquadro delle frecce
        $barra_fatta = false;
        if ( $strip_arrows && $barra_on ) {
            echo $this->barra_html( $uid, $bar, $f_cats, $f_conti, min( $max_visible, $total ), $f_gruppi ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- barra_html() escapa ogni testo e attributo.
            $barra_fatta = true;
        }
        if ( $strip_arrows ) {
            echo '<div class="' . esc_attr( $uid ) . '-sa-wrap" style="position:relative">';
        }

        // ─── Strip Marquee / Split: rendering speciale con duplicazione ───
        if ( $is_strip_auto ) {
            $visible_imgs = array_slice( $images, 0, $max_visible );
            $hidden_imgs  = array_slice( $images, $max_visible );

            // Helper per output singolo item
            $that = $this;
            $render_strip_item = function( $img, $idx, $is_dup = false ) use ( $that, $lightbox, $lb_custom, $show_cap, $hcaption, $layout, $strip_height ) {
                $is_vid  = $that->is_video_item( $img );
                $is_emb  = $that->is_embed_video( $img );
                $url     = is_array( $img ) ? ( $img['url'] ?? '' ) : $img;
                $alt     = is_array( $img ) ? ( $img['alt'] ?? '' ) : '';
                $caption = is_array( $img ) ? ( $img['caption'] ?? '' ) : '';
                $att_id  = is_array( $img ) ? absint( $img['id'] ?? 0 ) : 0;
                $poster  = is_array( $img ) ? ( $img['poster'] ?? '' ) : '';
                $embed   = is_array( $img ) ? ( $img['embed'] ?? '' ) : '';
                $poster_id = is_array( $img ) ? absint( $img['poster_id'] ?? 0 ) : 0;

                if ( ! $is_vid ) {
                    if ( ! $url ) return;
                } else {
                    if ( ! $url && ! $embed ) return;
                }

                $caption_attr = '';
                if ( $lightbox ) {
                    if ( $show_cap ) {
                        if ( ! empty( $caption ) ) {
                            $caption_attr = ' data-caption="' . esc_attr( $caption ) . '"';
                        }
                    }
                }

                $thumb_attr = '';
                if ( $lb_custom ) {
                    if ( $is_vid ) {
                        if ( $poster ) {
                            $thumb_attr = ' data-thumb="' . esc_url( $poster ) . '"';
                        }
                    } elseif ( $att_id ) {
                        $thumb_url = wp_get_attachment_image_url( $att_id, 'thumbnail' );
                        if ( $thumb_url ) {
                            $thumb_attr = ' data-thumb="' . esc_url( $thumb_url ) . '"';
                        }
                    }
                }

                // Video data attrs for lightbox
                $video_attrs = '';
                if ( $is_vid ) {
                    if ( $is_emb ) {
                        $embed_url = $that->get_gallery_embed_url( $embed );
                        $video_attrs = ' data-type="video" data-video-src="' . esc_url( $embed_url ) . '" data-poster="' . esc_url( $poster ) . '"';
                    } else {
                        $video_attrs = ' data-type="video" data-video-src="' . esc_url( $url ) . '" data-poster="' . esc_url( $poster ) . '"';
                    }
                }

                // Collage height variation per item
                $dichiarazioni = [];
                if ( $layout === 'strip_collage' ) {
                    $seed = $idx + 42;
                    $x = sin( $seed * 127.1 + 311.7 ) * 43758.5453123;
                    $rand = $x - floor( $x );
                    $h = round( $strip_height - 50 + $rand * 100 );
                    $dichiarazioni[] = 'height:' . $h . 'px';
                }
                $fp = $that->fuoco_css( $img );
                if ( '' !== $fp ) {
                    $dichiarazioni[] = '--olo-pg-fp:' . $fp;
                }
                $inline_style = $dichiarazioni ? ' style="' . esc_attr( implode( ';', $dichiarazioni ) ) . '"' : '';

                // Determine href for lightbox
                $lb_href = $url;
                if ( $is_vid ) {
                    if ( $is_emb ) {
                        $lb_href = $that->get_gallery_embed_url( $embed );
                    } else {
                        $lb_href = $url;
                    }
                }

                if ( $is_dup ) {
                    $dup_data = $lightbox ? ' data-href="' . esc_url( $lb_href ) . '"' : '';
                    echo '<div class="olo-pg-item olo-pg-dup' . ( $is_vid ? ' olo-pg-video' : '' ) . '" aria-hidden="true"' . $dup_data . $video_attrs . $inline_style . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- attributes are esc_url()-escaped or numeric-built above.
                    // Duplicati marquee: sempre poster per video
                    if ( $is_vid ) {
                        if ( $poster ) {
                            echo '<img src="' . esc_url( $poster ) . '" alt="' . esc_attr( $alt ) . '" />';
                        } else {
                            echo '<div class="olo-pg-no-poster"></div>';
                        }
                        echo '<div class="olo-pg-play"></div>';
                    } else {
                        echo Olobuild_Tile_Utils::img_srcset( $att_id, $url, esc_attr( $alt ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- img_srcset() returns an <img> tag escaped internally (esc_url/esc_attr/intval).
                    }
                    echo '</div>';
                } else {
                    $tag  = $lightbox ? 'a' : 'div';
                    $href = $lightbox ? ' href="' . esc_url( $lb_href ) . '"' : '';
                    echo '<' . $tag . ' class="olo-pg-item' . ( $is_vid ? ' olo-pg-video' : '' ) . '"' . $href . $caption_attr . $thumb_attr . $video_attrs . $inline_style . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $tag is a fixed literal ('a'/'div'); attributes are esc_url()/esc_attr()-escaped or numeric-built above.
                    // Strip marquee/split: video sempre in poster mode
                    if ( $is_vid ) {
                        if ( $poster ) {
                            echo '<img src="' . esc_url( $poster ) . '" alt="' . esc_attr( $alt ) . '" />';
                        } else {
                            echo '<div class="olo-pg-no-poster"></div>';
                        }
                        echo '<div class="olo-pg-play"></div>';
                    } else {
                        echo Olobuild_Tile_Utils::img_srcset( $att_id, $url, esc_attr( $alt ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- img_srcset() returns an <img> tag escaped internally (esc_url/esc_attr/intval).
                    }
                    if ( $hcaption !== 'none' ) {
                        echo $that->caption_html( $img ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- caption_html() esc_html() ogni riga e esc_attr() ogni classe.
                    }
                    echo '</' . esc_html( $tag ) . '>';
                }
            };

            if ( $layout === 'strip_marquee' ) {
                // Marquee: container → track → items + duplicati
                echo '<div class="' . $container_class . '"' . $lb_attr . $data_attrs . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $container_class/$lb_attr are esc_attr()-built above; $data_attrs holds numeric clamped values only.
                echo '<div class="' . $uid_attr . '-track">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $uid_attr is esc_attr()'d above.
                foreach ( $visible_imgs as $k => $img ) {
                    $render_strip_item( $img, $k, false );
                }
                // Duplicati per loop seamless
                foreach ( $visible_imgs as $k => $img ) {
                    $render_strip_item( $img, $k, true );
                }
                echo '</div>'; // track
                // Hidden items per lightbox
                if ( $lightbox ) {
                    foreach ( $hidden_imgs as $img ) {
                        if ( $this->is_video_item( $img ) ) {
                            $h_embed = $img['embed'] ?? '';
                            $h_url = $h_embed ? $this->get_gallery_embed_url( $h_embed ) : ( $img['url'] ?? '' );
                            $h_poster = $img['poster'] ?? '';
                            if ( $h_url ) {
                                echo '<a class="olo-pg-hidden" href="' . esc_url( $h_url ) . '" data-type="video" data-video-src="' . esc_url( $h_url ) . '" data-poster="' . esc_url( $h_poster ) . '"></a>';
                            }
                        } else {
                            $url = is_array( $img ) ? ( $img['url'] ?? '' ) : $img;
                            if ( $url ) {
                                echo '<a class="olo-pg-hidden" href="' . esc_url( $url ) . '"></a>';
                            }
                        }
                    }
                }
                echo '</div>'; // container

            } elseif ( $layout === 'strip_split' ) {
                // Split: container → 2 righe con track + items + duplicati
                $row1 = []; $row2 = [];
                foreach ( $visible_imgs as $k => $img ) {
                    if ( $k % 2 === 0 ) { $row1[] = [ 'img' => $img, 'idx' => $k ]; }
                    else                 { $row2[] = [ 'img' => $img, 'idx' => $k ]; }
                }

                echo '<div class="' . $container_class . '"' . $lb_attr . $data_attrs . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $container_class/$lb_attr are esc_attr()-built above; $data_attrs holds numeric clamped values only.
                // Row 1 (scorre a sinistra)
                echo '<div class="' . $uid_attr . '-row">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $uid_attr is esc_attr()'d above.
                echo '<div class="' . $uid_attr . '-track">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $uid_attr is esc_attr()'d above.
                foreach ( $row1 as $item ) { $render_strip_item( $item['img'], $item['idx'], false ); }
                foreach ( $row1 as $item ) { $render_strip_item( $item['img'], $item['idx'], true ); }
                echo '</div></div>';
                // Row 2 (scorre a destra — reverse)
                echo '<div class="' . $uid_attr . '-row">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $uid_attr is esc_attr()'d above.
                echo '<div class="' . $uid_attr . '-track ' . $uid_attr . '-track-rev">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $uid_attr is esc_attr()'d above.
                foreach ( $row2 as $item ) { $render_strip_item( $item['img'], $item['idx'], false ); }
                foreach ( $row2 as $item ) { $render_strip_item( $item['img'], $item['idx'], true ); }
                echo '</div></div>';
                // Hidden items per lightbox
                if ( $lightbox ) {
                    foreach ( $hidden_imgs as $img ) {
                        if ( $this->is_video_item( $img ) ) {
                            $h_embed = $img['embed'] ?? '';
                            $h_url = $h_embed ? $this->get_gallery_embed_url( $h_embed ) : ( $img['url'] ?? '' );
                            $h_poster = $img['poster'] ?? '';
                            if ( $h_url ) {
                                echo '<a class="olo-pg-hidden" href="' . esc_url( $h_url ) . '" data-type="video" data-video-src="' . esc_url( $h_url ) . '" data-poster="' . esc_url( $h_poster ) . '"></a>';
                            }
                        } else {
                            $url = is_array( $img ) ? ( $img['url'] ?? '' ) : $img;
                            if ( $url ) {
                                echo '<a class="olo-pg-hidden" href="' . esc_url( $url ) . '"></a>';
                            }
                        }
                    }
                }
                echo '</div>'; // container
            }

            // Strip arrows (auto strip)
            if ( $strip_arrows ) {
                echo $this->strip_arrows_html( $uid, $sa_style, $sa_size, $sa_color, $sa_bg ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- helper builds its HTML/CSS from clamped ints, safe_color_css() colors, a whitelisted style enum and esc_attr()'d labels.
                echo '</div>'; // sa-wrap
            }

            // Lightbox click delegation for duplicate items in marquee/split
            if ( $lightbox ) {
                echo '<script>(function(){';
                echo 'if(window._oloPGdup)return;window._oloPGdup=1;';
                echo 'document.addEventListener("click",function(e){';
                echo 'var dup=e.target.closest(".olo-pg-dup[data-href]");';
                echo 'if(!dup)return;';
                echo 'var ct=dup.closest("[uk-lightbox],[data-olo-lb]");';
                echo 'if(!ct)return;';
                echo 'var href=dup.getAttribute("data-href");';
                echo 'if(!href)return;';
                // Trova l'originale <a> con lo stesso href (evita quote escaping nel selector)
                echo 'var links=ct.querySelectorAll("a.olo-pg-item");';
                echo 'for(var i=0;i<links.length;i++){';
                echo 'if(links[i].getAttribute("href")===href){e.preventDefault();e.stopPropagation();links[i].click();break}';
                echo '}';
                echo '})';
                echo '})();</script>';
            }

            echo $this->stile_bordo( $s, '.' . $uid ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS da Olobuild_Tile_Base::stile_bordo() (impostazioni sanificate, uid interno)
            return ob_get_clean();
        }

        $filtri_lato = $filtri_lato && $barra_on && ! $barra_fatta;
        if ( $filtri_lato ) {
            echo '<div class="olo-pg-lato ' . esc_attr( $uid ) . '-lato"><div class="olo-pg-lato-filtri">';
        }
        if ( $barra_on && ! $barra_fatta ) {
            echo $this->barra_html( $uid, $bar, $f_cats, $f_conti, min( $max_visible, $total ), $f_gruppi ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- barra_html() escapa ogni testo e attributo.
        }
        if ( $filtri_lato ) {
            echo '</div><div class="olo-pg-lato-g">';
        }
        if ( $dati_on ) {
            $data_attrs .= ' data-pg-id="' . esc_attr( $uid ) . '"';
            if ( '' !== $bar['piu'] ) {
                $data_attrs .= ' data-pg-piu="' . esc_attr( $bar['piu'] ) . '" data-pg-per="' . (int) $bar['per'] . '"';
            }
            if ( $bar['link'] ) {
                $data_attrs .= ' data-pg-link="1"';
            }
            if ( $bar['multi'] ) {
                $data_attrs .= ' data-pg-multi="1"';
            }
            if ( $bar['mescola'] ) {
                $data_attrs .= ' data-pg-mescola="1"';
            }
            $data_attrs .= ' data-pg-testi="' . esc_attr( wp_json_encode( [
                'uno'   => olobuild_t( '1 foto' ),
                'molti' => olobuild_t( '%d foto' ),
                'altre' => olobuild_t( 'Mostra altre (%d)' ),
            ] ) ) . '"';
        }
        echo '<div class="' . $container_class . '"' . $lb_attr . $data_attrs . $hex_inline . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $container_class/$lb_attr are esc_attr()-built above; $data_attrs/$hex_inline hold numeric clamped values only.

        // ─── Render items ───
        $i = 0;
        foreach ( $images as $img ) {
            if ( $this->is_testo_item( $img ) ) {
                $i++;
                if ( $i <= $max_visible ) {
                    echo $this->card_testo_html( $img, $i, $sk, $this->voce_attr( $img, $i, $va_c ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- card_testo_html() escapa ogni testo e attributo.
                }
                continue;
            }
            $is_vid  = $this->is_video_item( $img );
            $is_emb  = $this->is_embed_video( $img );
            $url     = is_array( $img ) ? ( $img['url'] ?? '' ) : $img;
            $alt     = is_array( $img ) ? ( $img['alt'] ?? '' ) : '';
            $caption = is_array( $img ) ? ( $img['caption'] ?? '' ) : '';
            $att_id  = is_array( $img ) ? absint( $img['id'] ?? 0 ) : 0;
            $poster  = is_array( $img ) ? ( $img['poster'] ?? '' ) : '';
            $embed   = is_array( $img ) ? ( $img['embed'] ?? '' ) : '';
            $poster_id = is_array( $img ) ? absint( $img['poster_id'] ?? 0 ) : 0;

            if ( $is_vid ) {
                if ( ! $url && ! $embed ) continue;
            } else {
                if ( ! $url ) continue;
            }
            $i++;

            $is_visible  = ( $i <= $max_visible );
            $is_last_vis = ( $i === $max_visible && $extra > 0 );
            $caption_attr = '';
            if ( $lightbox ) {
                if ( $show_cap && ! empty( $caption ) ) {
                    $caption_attr = ' data-caption="' . esc_attr( str_replace( '*', '', $caption ) ) . '"';
                } elseif ( $extra > 0 ) {
                    $caption_attr = ' data-caption="' . $i . '/' . $total . '"';
                }
            }

            // Scattered layout: inline position styles
            $inline_style = '';
            $plx_item_data = '';
            if ( $layout === 'scattered' && $is_visible ) {
                $col_i   = ( $i - 1 ) % $cols_s;
                $row_i   = (int) floor( ( $i - 1 ) / $cols_s );
                $cw      = 100 / $cols_s;
                $ch      = 100 / $rows_s;
                $ox      = ( $this->seeded_random( $i ) - 0.5 ) * 10;
                $oy      = ( $this->seeded_random( $i + 13 ) - 0.5 ) * 10;
                $rot     = ( $this->seeded_random( $i + 7 ) - 0.5 ) * 12;
                $left    = round( $col_i * $cw + $ox, 1 );
                $top     = round( $row_i * $ch + $oy, 1 );
                $w       = round( $cw * 0.8, 1 );
                $h_s     = round( $ch * 0.8, 1 );
                $inline_style = "left:{$left}%;top:{$top}%;width:{$w}%;height:{$h_s}%;transform:rotate({$rot}deg)";
            }

            // Parallax layout: inline position + depth styles
            if ( $layout === 'parallax' && $is_visible ) {
                $col_i   = ( $i - 1 ) % $cols_p;
                $row_i   = (int) floor( ( $i - 1 ) / $cols_p );
                $cw      = 100 / $cols_p;
                $ch      = 100 / $rows_p;
                $depth   = $this->seeded_random( $i + 3 );
                $size    = 0.50 + $depth * 0.45;
                $ox      = ( $this->seeded_random( $i + 7 ) - 0.5 ) * 15;
                $oy      = ( $this->seeded_random( $i + 13 ) - 0.5 ) * 15;
                $rot     = ( $this->seeded_random( $i + 21 ) - 0.5 ) * 8;
                $left    = round( $col_i * $cw + $ox, 1 );
                $top     = round( $row_i * $ch + $oy, 1 );
                $w       = round( $cw * $size, 1 );
                $shBlur  = (int) round( 4 + $depth * 20 );
                $shHalf  = (int) round( $shBlur / 2 );
                $shAlpha = round( 0.1 + $depth * 0.2, 2 );
                $op      = round( 0.7 + $depth * 0.3, 2 );
                $zi      = (int) round( $depth * 20 );
                $speed   = round( ( $depth - 0.33 ) * ( $plx_intensity / 100 ), 3 );
                $rot_r   = round( $rot, 1 );
                $inline_style = "left:{$left}%;top:{$top}%;width:{$w}%;transform:rotate({$rot_r}deg);box-shadow:0 {$shHalf}px {$shBlur}px rgba(0,0,0,{$shAlpha});opacity:{$op};z-index:{$zi}";
                $plx_item_data = ' data-speed="' . $speed . '" data-base-transform="rotate(' . $rot_r . 'deg)"';
            }

            // Drift layout: multi-directional parallax with X, Y, rotation
            if ( $layout === 'drift' && $is_visible ) {
                $col_i   = ( $i - 1 ) % $cols_d;
                $row_i   = (int) floor( ( $i - 1 ) / $cols_d );
                $cw      = 100 / $cols_d;
                $ch      = 100 / $rows_d;
                $depth   = $this->seeded_random( $i + 5 );
                $size    = 0.45 + $depth * 0.50;
                $ox      = ( $this->seeded_random( $i + 11 ) - 0.5 ) * 18;
                $oy      = ( $this->seeded_random( $i + 17 ) - 0.5 ) * 18;
                $rot     = ( $this->seeded_random( $i + 23 ) - 0.5 ) * 10;
                $w       = round( $cw * $size, 1 );
                $left    = round( max( 1, min( 99 - $w, $col_i * $cw + $ox ) ), 1 );
                $top     = round( max( 1, min( 90, $row_i * $ch + $oy ) ), 1 );
                $shBlur  = (int) round( 4 + $depth * 18 );
                $shHalf  = (int) round( $shBlur / 2 );
                $shAlpha = round( 0.1 + $depth * 0.18, 2 );
                $op      = round( 0.75 + $depth * 0.25, 2 );
                $zi      = (int) round( $depth * 20 );
                // Velocità X e Y diverse per ogni item + direzione casuale
                $speed_y = round( ( $depth - 0.4 ) * ( $drift_intensity / 100 ), 3 );
                $speed_x = round( ( $this->seeded_random( $i + 31 ) - 0.5 ) * ( $drift_intensity / 100 ) * 0.7, 3 );
                $speed_r = round( ( $this->seeded_random( $i + 37 ) - 0.5 ) * ( $drift_rotation / 10 ), 3 );
                $rot_r   = round( $rot, 1 );
                $inline_style = "--dw:{$w}%;left:{$left}%;top:{$top}%;width:{$w}%;transform:rotate({$rot_r}deg);box-shadow:0 {$shHalf}px {$shBlur}px rgba(0,0,0,{$shAlpha});opacity:{$op};z-index:{$zi}";
                $plx_item_data = ' data-drift-sx="' . $speed_x . '" data-drift-sy="' . $speed_y . '" data-drift-sr="' . $speed_r . '" data-base-transform="rotate(' . $rot_r . 'deg)"';
            }

            // Cascade layout: stacked cards that spread on scroll
            if ( $layout === 'cascade' && $is_visible ) {
                $stack_offset = ( $i - 1 ) * ( $cascade_overlap / $total_vis_c );
                $rot_c = round( ( $this->seeded_random( $i + 9 ) - 0.5 ) * $cascade_rotation * 2, 1 );
                $shift_x = round( ( $this->seeded_random( $i + 15 ) - 0.5 ) * 30, 1 );
                $zi = $total_vis_c - $i + 1;
                $w_c = max( 35, 60 - ( $total_vis_c > 6 ? ( $i - 1 ) * 2 : 0 ) );
                $h_c = max( 200, 350 - ( $total_vis_c > 6 ? ( $i - 1 ) * 15 : 0 ) );
                $left_c = round( 50 - $w_c / 2 + $shift_x, 1 );
                $top_c = round( $stack_offset, 1 );
                $inline_style = "left:{$left_c}%;top:{$top_c}%;width:{$w_c}%;height:{$h_c}px;z-index:{$zi};transform:rotate({$rot_c}deg)";
                $plx_item_data = ' data-cascade-idx="' . ( $i - 1 ) . '" data-cascade-rot="' . $rot_c . '" data-cascade-sx="' . $shift_x . '"';
            }

            // Strip collage: varying height per item
            if ( $layout === 'strip_collage' && $is_visible ) {
                $rand = $this->seeded_random( $i + 42 );
                $collage_h = round( $strip_height - 50 + $rand * 100 );
                $inline_style = "height:{$collage_h}px";
            }

            // Hex grid: compute absolute position for tessellating hexagons
            if ( $layout === 'hexgrid' && $is_visible ) {
                $hex_w    = 100 / ( $cols + 0.5 );
                $hex_h    = $hex_w * 1.1547;
                $row_step = $hex_h * 0.75;
                $hex_row  = (int) floor( ( $i - 1 ) / $cols );
                $hex_col  = ( $i - 1 ) % $cols;
                $offset_x = ( $hex_row % 2 ) ? $hex_w * 0.5 : 0;
                $hex_rows_total = (int) ceil( min( $max_visible, $total ) / $cols );
                $container_h    = $row_step * ( $hex_rows_total - 1 ) + $hex_h;
                $l = round( $hex_col * $hex_w + $offset_x, 2 );
                $t = round( $hex_row * $row_step / $container_h * 100, 2 );
                $w = round( $hex_w, 2 );
                $h = round( $hex_h / $container_h * 100, 2 );
                $inline_style = "left:{$l}%;top:{$t}%;width:{$w}%;height:{$h}%";
            }

            // Puzzle: compute position + clip-path per piece (jigsaw interlocking)
            // Each piece's bounding box is 126% of cell (13% padding per side) so pieces overlap.
            // All clip-path coords remapped: pz(v) = (v + 13) / 1.26
            $puzzle_clip = '';
            if ( $layout === 'puzzle' && $is_visible ) {
                $puzzle_style = $s['puzzle_style'] ?: 'classic';

                // PZ_PAD dinamico in base allo stile
                if ( in_array( $puzzle_style, [ 'zigzag', 'wave', 'castle' ], true ) ) {
                    $PZ_PAD = 14;
                } elseif ( $puzzle_style === 'fir' ) {
                    $PZ_PAD = 20;
                } else {
                    $PZ_PAD = 23;
                }
                $PZ_SC = 100 + 2 * $PZ_PAD;
                $pz = function( $v ) use ( $PZ_PAD, $PZ_SC ) {
                    return round( ( $v + $PZ_PAD ) / $PZ_SC * 100, 1 );
                };

                $p_h_px  = intval( $img_height ) ?: 250;
                $p_row   = (int) floor( ( $i - 1 ) / $cols );
                $p_col   = ( $i - 1 ) % $cols;
                $p_rows  = (int) ceil( min( $max_visible, $total ) / $cols );
                $cellW   = 100 / $cols;
                $padW    = $cellW * $PZ_PAD / 100;
                $padH    = $p_h_px * $PZ_PAD / 100;
                $p_left  = round( $p_col * $cellW - $padW, 2 );
                $p_top   = round( $p_row * $p_h_px - $padH, 1 );
                $p_w     = round( $cellW + 2 * $padW, 2 );
                $p_ht    = round( $p_h_px + 2 * $padH, 1 );
                $inline_style = "left:{$p_left}%;top:{$p_top}px;width:{$p_w}%;height:{$p_ht}px";

                $isEven  = ( $p_row + $p_col ) % 2 === 0;
                $e_top    = $p_row === 0           ? 'flat' : ( $isEven ? 'blank' : 'tab' );
                $e_right  = $p_col === $cols - 1   ? 'flat' : ( $isEven ? 'tab' : 'blank' );
                $e_bottom = $p_row >= $p_rows - 1  ? 'flat' : ( $isEven ? 'blank' : 'tab' );
                $e_left   = $p_col === 0           ? 'flat' : ( $isEven ? 'tab' : 'blank' );

                $pts = [ $pz(0).'% '.$pz(0).'%' ];

                // ── Classic: knob rotondi jigsaw ──
                $classic_edge = function( $edge, $type ) use ( $pz ) {
                    $d  = $type === 'tab' ? 1 : -1;
                    $R  = 9; $CY = 13; $NW = 5.5; $NH = 6; $N = 16;
                    $arc = [];
                    for ( $k = 0; $k <= $N; $k++ ) {
                        $rad = deg2rad( 225 - $k * 270 / $N );
                        $arc[] = [ 50 + $R * cos( $rad ), $CY + $R * sin( $rad ) ];
                    }
                    if ( $edge === 'top' ) {
                        $pts = [ $pz(30).'% '.$pz(0).'%', $pz(36).'% '.$pz(0).'%' ];
                        $pts[] = $pz(50-$NW).'% '.$pz(-$d*1).'%';
                        $pts[] = $pz(50-$NW).'% '.$pz(-$d*$NH).'%';
                        foreach ( $arc as list($ax,$ay) ) { $pts[] = $pz($ax).'% '.$pz(-$d*$ay).'%'; }
                        $pts[] = $pz(50+$NW).'% '.$pz(-$d*$NH).'%';
                        $pts[] = $pz(50+$NW).'% '.$pz(-$d*1).'%';
                        $pts[] = $pz(64).'% '.$pz(0).'%';
                        $pts[] = $pz(70).'% '.$pz(0).'%';
                        return $pts;
                    }
                    if ( $edge === 'right' ) {
                        $pts = [ $pz(100).'% '.$pz(30).'%', $pz(100).'% '.$pz(36).'%' ];
                        $pts[] = $pz(100+$d*1).'% '.$pz(50-$NW).'%';
                        $pts[] = $pz(100+$d*$NH).'% '.$pz(50-$NW).'%';
                        foreach ( array_reverse( $arc ) as list($ax,$ay) ) { $pts[] = $pz(100+$d*$ay).'% '.$pz(100-$ax).'%'; }
                        $pts[] = $pz(100+$d*$NH).'% '.$pz(50+$NW).'%';
                        $pts[] = $pz(100+$d*1).'% '.$pz(50+$NW).'%';
                        $pts[] = $pz(100).'% '.$pz(64).'%';
                        $pts[] = $pz(100).'% '.$pz(70).'%';
                        return $pts;
                    }
                    if ( $edge === 'bottom' ) {
                        $pts = [ $pz(70).'% '.$pz(100).'%', $pz(64).'% '.$pz(100).'%' ];
                        $pts[] = $pz(50+$NW).'% '.$pz(100+$d*1).'%';
                        $pts[] = $pz(50+$NW).'% '.$pz(100+$d*$NH).'%';
                        foreach ( $arc as list($ax,$ay) ) { $pts[] = $pz(100-$ax).'% '.$pz(100+$d*$ay).'%'; }
                        $pts[] = $pz(50-$NW).'% '.$pz(100+$d*$NH).'%';
                        $pts[] = $pz(50-$NW).'% '.$pz(100+$d*1).'%';
                        $pts[] = $pz(36).'% '.$pz(100).'%';
                        $pts[] = $pz(30).'% '.$pz(100).'%';
                        return $pts;
                    }
                    if ( $edge === 'left' ) {
                        $pts = [ $pz(0).'% '.$pz(70).'%', $pz(0).'% '.$pz(64).'%' ];
                        $pts[] = $pz(-$d*1).'% '.$pz(50+$NW).'%';
                        $pts[] = $pz(-$d*$NH).'% '.$pz(50+$NW).'%';
                        foreach ( $arc as list($ax,$ay) ) { $pts[] = $pz(-$d*$ay).'% '.$pz(100-$ax).'%'; }
                        $pts[] = $pz(-$d*$NH).'% '.$pz(50-$NW).'%';
                        $pts[] = $pz(-$d*1).'% '.$pz(50-$NW).'%';
                        $pts[] = $pz(0).'% '.$pz(36).'%';
                        $pts[] = $pz(0).'% '.$pz(30).'%';
                        return $pts;
                    }
                    return [];
                };

                // ── Zigzag: 4 denti triangolari per lato ──
                $zigzag_edge = function( $edge, $type ) use ( $pz ) {
                    $d = $type === 'tab' ? 1 : -1;
                    $amp = 12; $teeth = 4;
                    $start = 25; $end = 75;
                    $span = $end - $start;
                    $step = $span / $teeth;
                    if ( $edge === 'top' ) {
                        $pts = [ $pz($start).'% '.$pz(0).'%' ];
                        for ( $t = 0; $t < $teeth; $t++ ) {
                            $x0 = $start + $t * $step;
                            $pts[] = $pz($x0 + $step/2).'% '.$pz(-$d * $amp).'%';
                            $pts[] = $pz($x0 + $step).'% '.$pz(0).'%';
                        }
                        return $pts;
                    }
                    if ( $edge === 'right' ) {
                        $pts = [ $pz(100).'% '.$pz($start).'%' ];
                        for ( $t = 0; $t < $teeth; $t++ ) {
                            $y0 = $start + $t * $step;
                            $pts[] = $pz(100 + $d * $amp).'% '.$pz($y0 + $step/2).'%';
                            $pts[] = $pz(100).'% '.$pz($y0 + $step).'%';
                        }
                        return $pts;
                    }
                    if ( $edge === 'bottom' ) {
                        $pts = [ $pz($end).'% '.$pz(100).'%' ];
                        for ( $t = 0; $t < $teeth; $t++ ) {
                            $x0 = $end - $t * $step;
                            $pts[] = $pz($x0 - $step/2).'% '.$pz(100 + $d * $amp).'%';
                            $pts[] = $pz($x0 - $step).'% '.$pz(100).'%';
                        }
                        return $pts;
                    }
                    if ( $edge === 'left' ) {
                        $pts = [ $pz(0).'% '.$pz($end).'%' ];
                        for ( $t = 0; $t < $teeth; $t++ ) {
                            $y0 = $end - $t * $step;
                            $pts[] = $pz(-$d * $amp).'% '.$pz($y0 - $step/2).'%';
                            $pts[] = $pz(0).'% '.$pz($y0 - $step).'%';
                        }
                        return $pts;
                    }
                    return [];
                };

                // ── Wave: 2 cicli sinusoidali ──
                $wave_edge = function( $edge, $type ) use ( $pz ) {
                    $d = $type === 'tab' ? 1 : -1;
                    $amp = 10; $N = 32;
                    $start = 20; $end = 80;
                    $span = $end - $start;
                    if ( $edge === 'top' ) {
                        $pts = [];
                        for ( $j = 0; $j <= $N; $j++ ) {
                            $t = $j / $N;
                            $x = $start + $t * $span;
                            $perp = $d * $amp * sin( $t * 4 * M_PI );
                            $pts[] = $pz($x).'% '.$pz(-$perp).'%';
                        }
                        return $pts;
                    }
                    if ( $edge === 'right' ) {
                        $pts = [];
                        for ( $j = 0; $j <= $N; $j++ ) {
                            $t = $j / $N;
                            $y = $start + $t * $span;
                            $perp = $d * $amp * sin( $t * 4 * M_PI );
                            $pts[] = $pz(100 + $perp).'% '.$pz($y).'%';
                        }
                        return $pts;
                    }
                    if ( $edge === 'bottom' ) {
                        $pts = [];
                        for ( $j = 0; $j <= $N; $j++ ) {
                            $t = $j / $N;
                            $x = $end - $t * $span;
                            $perp = -$d * $amp * sin( $t * 4 * M_PI );
                            $pts[] = $pz($x).'% '.$pz(100 + $perp).'%';
                        }
                        return $pts;
                    }
                    if ( $edge === 'left' ) {
                        $pts = [];
                        for ( $j = 0; $j <= $N; $j++ ) {
                            $t = $j / $N;
                            $y = $end - $t * $span;
                            $perp = -$d * $amp * sin( $t * 4 * M_PI );
                            $pts[] = $pz(-$perp).'% '.$pz($y).'%';
                        }
                        return $pts;
                    }
                    return [];
                };

                // ── Castle: 3 merli rettangolari ──
                $castle_edge = function( $edge, $type ) use ( $pz ) {
                    $d = $type === 'tab' ? 1 : -1;
                    $h = 10; $w = 8; $merlons = 3;
                    $start = 25; $end = 75;
                    $span = $end - $start;
                    $merlonSpan = $span / $merlons;
                    $gap = ( $merlonSpan - $w ) / 2;
                    if ( $edge === 'top' ) {
                        $pts = [ $pz($start).'% '.$pz(0).'%' ];
                        for ( $m = 0; $m < $merlons; $m++ ) {
                            $x0 = $start + $m * $merlonSpan + $gap;
                            $pts[] = $pz($x0).'% '.$pz(0).'%';
                            $pts[] = $pz($x0).'% '.$pz(-$d * $h).'%';
                            $pts[] = $pz($x0 + $w).'% '.$pz(-$d * $h).'%';
                            $pts[] = $pz($x0 + $w).'% '.$pz(0).'%';
                        }
                        $pts[] = $pz($end).'% '.$pz(0).'%';
                        return $pts;
                    }
                    if ( $edge === 'right' ) {
                        $pts = [ $pz(100).'% '.$pz($start).'%' ];
                        for ( $m = 0; $m < $merlons; $m++ ) {
                            $y0 = $start + $m * $merlonSpan + $gap;
                            $pts[] = $pz(100).'% '.$pz($y0).'%';
                            $pts[] = $pz(100 + $d * $h).'% '.$pz($y0).'%';
                            $pts[] = $pz(100 + $d * $h).'% '.$pz($y0 + $w).'%';
                            $pts[] = $pz(100).'% '.$pz($y0 + $w).'%';
                        }
                        $pts[] = $pz(100).'% '.$pz($end).'%';
                        return $pts;
                    }
                    if ( $edge === 'bottom' ) {
                        $pts = [ $pz($end).'% '.$pz(100).'%' ];
                        for ( $m = 0; $m < $merlons; $m++ ) {
                            $x0 = $end - $m * $merlonSpan - $gap;
                            $pts[] = $pz($x0).'% '.$pz(100).'%';
                            $pts[] = $pz($x0).'% '.$pz(100 + $d * $h).'%';
                            $pts[] = $pz($x0 - $w).'% '.$pz(100 + $d * $h).'%';
                            $pts[] = $pz($x0 - $w).'% '.$pz(100).'%';
                        }
                        $pts[] = $pz($start).'% '.$pz(100).'%';
                        return $pts;
                    }
                    if ( $edge === 'left' ) {
                        $pts = [ $pz(0).'% '.$pz($end).'%' ];
                        for ( $m = 0; $m < $merlons; $m++ ) {
                            $y0 = $end - $m * $merlonSpan - $gap;
                            $pts[] = $pz(0).'% '.$pz($y0).'%';
                            $pts[] = $pz(-$d * $h).'% '.$pz($y0).'%';
                            $pts[] = $pz(-$d * $h).'% '.$pz($y0 - $w).'%';
                            $pts[] = $pz(0).'% '.$pz($y0 - $w).'%';
                        }
                        $pts[] = $pz(0).'% '.$pz($start).'%';
                        return $pts;
                    }
                    return [];
                };

                // ── Fir: 2 abeti a 3 livelli per lato ──
                $fir_edge = function( $edge, $type ) use ( $pz ) {
                    $d = $type === 'tab' ? 1 : -1;
                    $tw = 1.5; $th = 3.75;
                    $t1w = 8.25; $t1pw = 3; $t1h = 8.25;
                    $t2w = 6; $t2pw = 2.25; $t2h = 12.75;
                    $t3w = 4.5; $peakH = 18;
                    $P = [
                        [-$tw,0],[-$tw,$th],[-$t1w,$th],[-$t1pw,$t1h],[-$t2w,$t1h],[-$t2pw,$t2h],[-$t3w,$t2h],
                        [0,$peakH],
                        [$t3w,$t2h],[$t2pw,$t2h],[$t2w,$t1h],[$t1pw,$t1h],[$t1w,$th],[$tw,$th],[$tw,0]
                    ];
                    if ( $edge === 'top' ) {
                        $pts = [ $pz(25).'% '.$pz(0).'%' ];
                        foreach ( [37.5, 62.5] as $cx ) {
                            foreach ( $P as list($ao, $po) ) {
                                $pts[] = $pz($cx + $ao).'% '.$pz(-$d * $po).'%';
                            }
                        }
                        $pts[] = $pz(75).'% '.$pz(0).'%';
                        return $pts;
                    }
                    if ( $edge === 'right' ) {
                        $pts = [ $pz(100).'% '.$pz(25).'%' ];
                        foreach ( [37.5, 62.5] as $cy ) {
                            foreach ( $P as list($ao, $po) ) {
                                $pts[] = $pz(100 + $d * $po).'% '.$pz($cy + $ao).'%';
                            }
                        }
                        $pts[] = $pz(100).'% '.$pz(75).'%';
                        return $pts;
                    }
                    if ( $edge === 'bottom' ) {
                        $pts = [ $pz(75).'% '.$pz(100).'%' ];
                        foreach ( [62.5, 37.5] as $cx ) {
                            foreach ( $P as list($ao, $po) ) {
                                $pts[] = $pz($cx - $ao).'% '.$pz(100 + $d * $po).'%';
                            }
                        }
                        $pts[] = $pz(25).'% '.$pz(100).'%';
                        return $pts;
                    }
                    if ( $edge === 'left' ) {
                        $pts = [ $pz(0).'% '.$pz(75).'%' ];
                        foreach ( [62.5, 37.5] as $cy ) {
                            foreach ( $P as list($ao, $po) ) {
                                $pts[] = $pz(-$d * $po).'% '.$pz($cy - $ao).'%';
                            }
                        }
                        $pts[] = $pz(0).'% '.$pz(25).'%';
                        return $pts;
                    }
                    return [];
                };

                // Dispatcher
                $knob = $classic_edge;
                if ( $puzzle_style === 'zigzag' ) $knob = $zigzag_edge;
                elseif ( $puzzle_style === 'wave' ) $knob = $wave_edge;
                elseif ( $puzzle_style === 'castle' ) $knob = $castle_edge;
                elseif ( $puzzle_style === 'fir' ) $knob = $fir_edge;

                // Top
                if ( $e_top === 'flat' ) { $pts[] = $pz(100).'% '.$pz(0).'%'; }
                else { $pts = array_merge( $pts, $knob( 'top', $e_top ), [ $pz(100).'% '.$pz(0).'%' ] ); }
                // Right
                if ( $e_right === 'flat' ) { $pts[] = $pz(100).'% '.$pz(100).'%'; }
                else { $pts = array_merge( $pts, $knob( 'right', $e_right ), [ $pz(100).'% '.$pz(100).'%' ] ); }
                // Bottom
                if ( $e_bottom === 'flat' ) { $pts[] = $pz(0).'% '.$pz(100).'%'; }
                else { $pts = array_merge( $pts, $knob( 'bottom', $e_bottom ), [ $pz(0).'% '.$pz(100).'%' ] ); }
                // Left
                if ( $e_left !== 'flat' ) { $pts = array_merge( $pts, $knob( 'left', $e_left ) ); }

                $puzzle_clip = 'clip-path:polygon(' . implode( ',', $pts ) . ')';
            }

            // Tag: <a> if lightbox, <div> otherwise
            $tag = $lightbox ? 'a' : 'div';

            // Determine href for lightbox
            $lb_href = $url;
            if ( $is_vid ) {
                if ( $is_emb ) {
                    $lb_href = $this->get_gallery_embed_url( $embed );
                }
            }
            $href = $lightbox ? ' href="' . esc_url( $lb_href ) . '"' : '';

            // Thumb URL for custom lightbox
            $thumb_attr = '';
            if ( $lb_custom ) {
                if ( $is_vid ) {
                    if ( $poster ) {
                        $thumb_attr = ' data-thumb="' . esc_url( $poster ) . '"';
                    }
                } elseif ( $att_id ) {
                    $thumb_url = wp_get_attachment_image_url( $att_id, 'thumbnail' );
                    if ( $thumb_url ) {
                        $thumb_attr = ' data-thumb="' . esc_url( $thumb_url ) . '"';
                    }
                }
            }

            // Video data attrs
            $video_attrs = '';
            $item_class = 'olo-pg-item';
            if ( $is_vid ) {
                $item_class .= ' olo-pg-video';
                $vid_src = $is_emb ? $this->get_gallery_embed_url( $embed ) : $url;
                $video_attrs = ' data-type="video" data-video-src="' . esc_url( $vid_src ) . '" data-poster="' . esc_url( $poster ) . '"';
            }

            // Should use autoplay <video> tag or embed iframe?
            $use_autoplay = false;
            $use_embed_autoplay = false;
            if ( $is_vid && $video_preview === 'autoplay' && ! $is_coverflow && ! $is_strip_auto ) {
                if ( $is_emb ) {
                    $use_embed_autoplay = true;
                } else {
                    $use_autoplay = true;
                }
            }

            // Il link della foto: porta lì invece di aprire la lightbox
            $link_voce = is_array( $img ) ? esc_url( trim( (string) ( $img['link'] ?? '' ) ) ) : '';
            if ( $link_voce ) {
                $tag          = 'a';
                $href         = ' href="' . $link_voce . '"';
                $item_class  .= ' olo-pg-link';
                $caption_attr = '';
                $thumb_attr   = '';
            }
            // La Vista rapida: se la voce è un prodotto di WooCommerce, l'icona della card apre la finestra del prodotto
            $this->qv_id = $this->qv_on ? $this->prodotto_della_voce( $img ) : 0;
            // Categorie (filtri), testi per ricerca e ordinamento, misure senza buchi, colore della voce
            $va          = $this->voce_attr( $img, $i, $va_c );
            $cat_attr    = $va['attr'];
            $item_class .= $va['classe'];
            $pz_vars     = $va['vars'];

            if ( $is_visible ) {
                $fp = $this->fuoco_css( $img );
                $combined_style = implode( ';', array_filter( [
                    trim( $inline_style ),
                    $puzzle_clip ? trim( $puzzle_clip ) : '',
                    '' !== $fp ? '--olo-pg-fp:' . $fp : '',
                    $pz_vars,
                ] ) );
                $style_attr = $combined_style ? ' style="' . esc_attr( $combined_style ) . '"' : '';
                echo '<' . $tag . ' class="' . $item_class . '"' . $href . $caption_attr . $thumb_attr . $video_attrs . $plx_item_data . $cat_attr . $style_attr . '>';
                if ( in_array( $sk['modo'], [ 'sotto', 'biglietto' ], true ) ) {
                    echo '<div class="olo-pg-media">';
                } // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $tag/$item_class are fixed literals; attributes are esc_url()/esc_attr()-escaped or numeric-built above.

                if ( $is_vid ) {
                    if ( $use_autoplay ) {
                        $mime = $this->get_video_mime( $url );
                        $poster_attr = $poster ? ' poster="' . esc_url( $poster ) . '"' : '';
                        echo '<video muted autoplay loop playsinline' . $poster_attr . '><source src="' . esc_url( $url ) . '" type="' . esc_attr( $mime ) . '"></video>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $poster_attr is esc_url()-built above; the rest is escaped inline.
                        if ( ! empty( $s['video_pausa'] ) ) {
                            // Il pulsante per fermare il video che parte da solo (chi visita deve poter fermare ciò che si muove).
                            // Sta dentro la foto, che può essere un link della lightbox: lo script ferma il clic lì.
                            $angolo = in_array( $s['video_pausa_angolo'] ?? '', [ 'ad', 'bs', 'bd' ], true ) ? ' olo-pg-pausa-' . $s['video_pausa_angolo'] : '';
                            echo '<span class="olo-pg-pausa' . $angolo . '" role="button" tabindex="0" aria-pressed="false" aria-label="' . esc_attr( olobuild_t( 'Metti in pausa il video' ) ) . '" data-pausa="' . esc_attr( olobuild_t( 'Metti in pausa il video' ) ) . '" data-riprendi="' . esc_attr( olobuild_t( 'Riprendi il video' ) ) . '"></span>';
                            $ha_pausa = true;
                        }
                    } elseif ( $use_embed_autoplay ) {
                        $embed_auto_url = $this->get_gallery_embed_url( $embed, true );
                        $iframe_title = $caption !== '' ? $caption : ( $alt !== '' ? $alt : olobuild_t( 'Anteprima video' ) );
                        echo '<iframe src="' . esc_url( $embed_auto_url ) . '" title="' . esc_attr( $iframe_title ) . '" style="position:absolute;inset:0;width:100%;height:100%;border:none" allow="autoplay;encrypted-media" allowfullscreen loading="lazy"></iframe>';
                    } else {
                        if ( $poster ) {
                            echo Olobuild_Tile_Utils::img_srcset( $poster_id, $poster, esc_attr( $alt ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- img_srcset() returns an <img> tag escaped internally (esc_url/esc_attr/intval).
                        } else {
                            echo '<div class="olo-pg-no-poster"></div>';
                        }
                        echo '<div class="olo-pg-play"></div>';
                    }
                } else {
                    echo Olobuild_Tile_Utils::img_srcset( $att_id, $url, esc_attr( $alt ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- img_srcset() returns an <img> tag escaped internally (esc_url/esc_attr/intval).
                }

                // Il segno a matita attorno alla foto (Stile › Colore e segno delle voci)
                $segno = is_array( $img ) ? (string) ( $img['segno'] ?? '' ) : '';
                if ( in_array( $segno, [ 'riquadro', 'cerchio', 'matita' ], true ) ) {
                    echo '<span class="olo-pg-segno olo-pg-segno-' . esc_attr( $segno ) . '" aria-hidden="true"></span>';
                    $ha_segni = true;
                }
                // Le scritte sul bordo della pellicola: il fotogramma con la freccia, il mezzo fotogramma (03A), il
                // rullino e la sensibilità, come sui provini a contatto
                if ( '' !== $pell_bordo ) {
                    $fn = max( 0, $pell_primo + ( $i - 1 ) * $pell_passo );
                    $ff = str_pad( (string) $fn, 2, '0', STR_PAD_LEFT );
                    echo '<span class="olo-pg-bordo" aria-hidden="true"><b>&#9656; ' . esc_html( $ff ) . '</b><i>' . esc_html( $ff ) . 'A</i>' . $pell_bordo . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $pell_bordo è costruito con esc_html() sopra.
                }
                // L'etichetta sulla foto («Il servizio di copertina», «Nuovo») e la barretta nel colore della voce
                $etich = is_array( $img ) ? trim( (string) ( $img['etichetta'] ?? '' ) ) : '';
                if ( '' !== $etich ) {
                    echo '<span class="olo-pg-etichetta olo-pg-et-' . esc_attr( $sk['angolo_et'] ?? 'as' ) . '">' . esc_html( $etich ) . '</span>';
                }
                if ( ! empty( $sk['barra'] ) ) {
                    echo '<span class="olo-pg-barretta" aria-hidden="true"></span>';
                }
                // I testi sulle foto (o le colonne dell'elenco): si scrivono prima di chiudere la foto, perché coi testi
                // sotto qualche parte può stare sulla foto
                $sk_html = '' !== $sk['modo'] ? $this->scheda_html( $img, $i, $sk ) : '';
                if ( in_array( $sk['modo'], [ 'sotto', 'biglietto' ], true ) ) {
                    echo $this->sk_foto . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- sk_foto è scritto da scheda_html(), che escapa ogni testo.
                }
                if ( '' !== $sk_html ) {
                    echo $sk_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- scheda_html() escapa ogni testo.
                }
                if ( $this->qv_id && '' === $sk['modo'] ) {
                    // Senza testi sulle foto l'icona della Vista rapida sta nell'angolo della foto
                    echo '<span class="olo-pg-qv-angolo">' . $this->qv_icona( 'piu', is_array( $img ) ? (string) ( $img['caption'] ?? '' ) : '' ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- qv_icona() escapa l'etichetta, l'SVG è fisso.
                }
                // Hover caption, fino a tre righe (titolo, sottotitolo, testo).
                if ( '' === $sk['modo'] && $hcaption !== 'none' ) {
                    echo $this->caption_html( $img ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- caption_html() esc_html() ogni riga e esc_attr() ogni classe.
                }

                // +N overlay
                if ( $is_last_vis ) {
                    echo '<div class="olo-pg-more">+' . (int) $extra . '</div>';
                }

                echo '</' . esc_html( $tag ) . '>';
            } else {
                // Hidden items for lightbox
                if ( $lightbox ) {
                    if ( $is_vid ) {
                        $vid_src = $is_emb ? $this->get_gallery_embed_url( $embed ) : $url;
                        echo '<a class="olo-pg-hidden" href="' . esc_url( $vid_src ) . '"' . $caption_attr . $thumb_attr . ' data-type="video" data-video-src="' . esc_url( $vid_src ) . '" data-poster="' . esc_url( $poster ) . '"></a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $caption_attr/$thumb_attr are esc_attr()/esc_url()-built above; the rest is escaped inline.
                    } else {
                        echo '<a class="olo-pg-hidden" href="' . esc_url( $url ) . '"' . $caption_attr . $thumb_attr . '></a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $caption_attr/$thumb_attr are esc_attr()/esc_url()-built above.
                    }
                }
            }
        }

        echo '</div>';
        if ( $is_strip_drag ? ! empty( $s['strip_avanzamento'] ) : false ) {
            // La barretta che dice a che punto del nastro si è
            echo '<div class="olo-pg-avanza" aria-hidden="true"><span></span></div>';
        }
        if ( $dati_on ) {
            echo $this->piu_html( $uid, $bar, min( $max_visible, $total ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- piu_html() escapa ogni testo.
            echo $this->barra_js(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- script fisso, nessun dato.
        }
        if ( $filtri_lato ) {
            echo '</div></div>'; // la galleria e la colonna dei filtri
        }

        if ( $anima_on ) {
            echo $this->anima_js(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- script fisso, nessun dato.
        }
        if ( $ha_pausa ) {
            echo $this->pausa_js(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- script e stile fissi, nessun dato.
        }

        // Coverflow: frecce + indicatore + chiusura wrapper
        if ( $is_coverflow ) {
            // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- $uid_attr is esc_attr()'d above; button labels are escaped inline.
            // data-olo-interactive: nell'anteprima del builder frecce e indicatore sfogliano davvero
            echo '<button type="button" class="' . $uid_attr . '-prev" data-olo-interactive aria-label="' . esc_attr( olobuild_t( 'Precedente' ) ) . '">&#8249;</button>';
            echo '<button type="button" class="' . $uid_attr . '-next" data-olo-interactive aria-label="' . esc_attr( olobuild_t( 'Successivo' ) ) . '">&#8250;</button>';
            if ( $film_dots !== 'none' ) {
                if ( $film_dots === 'progress' ) {
                    echo '<div class="' . $uid_attr . '-dots"><div class="pg-prog-track" data-olo-interactive tabindex="0" role="slider" aria-label="' . esc_attr( olobuild_t( 'Avanzamento galleria' ) ) . '" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="pg-prog-fill"></div></div></div>';
                } else {
                    echo '<div class="' . $uid_attr . '-dots"></div>';
                }
            }
            // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
            echo '</div>'; // close -wrap
        }

        // Strip arrows (drag strip)
        if ( $strip_arrows ) {
            echo $this->strip_arrows_html( $uid, $sa_style, $sa_size, $sa_color, $sa_bg ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- helper builds its HTML/CSS from clamped ints, safe_color_css() colors, a whitelisted style enum and esc_attr()'d labels.
            echo '</div>'; // sa-wrap
        }

                // Border system
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) echo ".{$uid}{{$border_css}}"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by the shared Olobuild_Tile_Base::build_border_css() helper (sizes int-cast); $uid is a generated unique id.
            echo $border_hover_css . $border_effect_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by the shared Olobuild_Tile_Base border helpers (build_border_hover_css/build_border_effect_css, sizes int-cast) on a generated uid selector.
        }
        echo $this->qv_finestra( $uid ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS fisso sull'uid interno + resa della tile Quick View WC.
        return ob_get_clean();
    }

    /** Il prodotto di WooCommerce di una voce: dalla Sorgente dinamica (id «dyn-123») o dal link a mano a un prodotto del sito. */
    private function prodotto_della_voce( $img ) {
        if ( ! is_array( $img ) ) {
            return 0;
        }
        $id  = (string) ( $img['id'] ?? '' );
        $pid = 0 === strpos( $id, 'dyn-' ) ? absint( substr( $id, 4 ) ) : 0;
        if ( ! $pid ) {
            $link = trim( (string) ( $img['link'] ?? '' ) );
            if ( '' !== $link ) {
                if ( 0 === strpos( $link, '/' ) || 0 === strpos( $link, home_url() ) ) {
                    $pid = url_to_postid( $link );
                }
            }
        }
        return ( $pid && 'product' === get_post_type( $pid ) ) ? $pid : 0;
    }

    /** L'icona della card che apre la Vista rapida del prodotto (sul sito un pulsante; nel builder solo il segno). */
    private function qv_icona( $nome, $titolo ) {
        $ico = $this->scheda_icona( $nome ? $nome : 'piu' );
        if ( '' === $ico ) {
            $ico = $this->scheda_icona( 'piu' );
        }
        $this->qv_usata = true;
        if ( ! $this->qv_attivo ) {
            return $ico;
        }
        $titolo = trim( wp_strip_all_tags( str_replace( '*', '', $titolo ) ) );
        $et     = '' !== $titolo ? olobuild_t( 'Vista rapida' ) . ': ' . $titolo : olobuild_t( 'Vista rapida' );
        $ico    = preg_replace( '/ aria-hidden="true"/', '', $ico, 1 );
        return str_replace( '<span class="olo-pg-sk-ico', '<span role="button" tabindex="0" aria-haspopup="dialog" aria-label="' . esc_attr( $et ) . '" title="' . esc_attr( olobuild_t( 'Vista rapida' ) ) . '" data-olo-qv-id="' . intval( $this->qv_id ) . '" class="olo-pg-qv olo-pg-sk-ico', $ico );
    }

    /**
     * Il CSS dell'icona della Vista rapida e, una volta per pagina, la finestra (la tile Quick View WC «su richiesta») con
     * lo script che la apre dal clic o da Invio/Spazio sull'icona: prima del link della card, che così non parte.
     */
    private function qv_finestra( $uid ) {
        if ( ! $this->qv_usata ) {
            return '';
        }
        $u = '.' . $uid;
        $h = '<style>' . "{$u} .olo-pg-qv{cursor:pointer;pointer-events:auto}{$u} .olo-pg-qv:focus-visible{outline:2px solid currentColor;outline-offset:3px}"
            . "{$u} .olo-pg-qv-angolo{position:absolute;top:.75rem;right:.75rem;z-index:4;color:var(--olo-color-text,#1f2937)}"
            . "{$u} .olo-pg-qv-angolo .olo-pg-sk-ico{width:34px;height:34px;border-radius:50%;border:0;display:flex;align-items:center;justify-content:center;background:var(--olo-color-background,#fff);box-shadow:0 2px 10px rgb(0 0 0 / .2);transition:transform .35s ease}"
            . "{$u} .olo-pg-item:hover .olo-pg-qv-angolo .olo-pg-ico-piu{transform:rotate(90deg)}"
            . "@media (prefers-reduced-motion:reduce){{$u} .olo-pg-qv-angolo .olo-pg-sk-ico{transition:none}}</style>";
        if ( $this->qv_attivo && ! self::$qv_finestra ) {
            self::$qv_finestra = true;
            $qv = class_exists( 'Olobuild_Tile_Manager' ) ? Olobuild_Tile_Manager::instance()->get_tile( 'woo_quickview' ) : null;
            if ( ! $qv ) {
                $qv = new Olobuild_Woo_Quickview_Tile();
            }
            $h .= $qv->render( [ '_solo_su_richiesta' => true ] );
            // Niente e commerciale doppia negli script in linea (WordPress la riscrive in entità): condizioni annidate
            $h .= '<script>(function(){if(window.oloPgQv)return;window.oloPgQv=1;'
                . 'function apri(e){var b=e.target.closest?e.target.closest(".olo-pg-qv[data-olo-qv-id]"):null;if(!b)return;'
                . 'if(e.type==="keydown"){if(e.key!=="Enter"){if(e.key!==" ")return}}'
                . 'if(!window.oloQvApri)return;e.preventDefault();e.stopPropagation();window.oloQvApri(b.getAttribute("data-olo-qv-id"),b)}'
                . 'document.addEventListener("click",apri,true);document.addEventListener("keydown",apri,true)})();</script>';
        }
        return $h;
    }

    /**
     * LA DIDASCALIA, FINO A TRE RIGHE.
     *
     * La galleria ha sempre avuto una riga sola, `caption`. Le altre due
     * arrivano da `subtitle` e `text`, che sono gli stessi nomi che usano gli
     * item dello ScrollScrub: lo stesso contenuto si sposta da un tile
     * all'altro senza riscriverlo, ed e' l'unica ragione per cui i campi si
     * chiamano così invece che `caption2` e `caption3`.
     *
     * ⚠️ Chi non le compila non se ne accorge. Senza quei due campi esce
     * esattamente il markup di prima, un div con dentro il testo: le tre
     * righe nascono solo quando c'e' qualcosa da metterci.
     */
    private function caption_html( $img ) {
        if ( ! is_array( $img ) ) {
            return '';
        }

        $righe = array(
            'olo-pg-cap-t' => trim( (string) ( $img['caption'] ?? '' ) ),
            'olo-pg-cap-s' => trim( (string) ( $img['subtitle'] ?? '' ) ),
            'olo-pg-cap-x' => trim( (string) ( $img['text'] ?? '' ) ),
        );

        $dentro = '';
        foreach ( $righe as $classe => $testo ) {
            if ( '' !== $testo ) {
                $dentro .= '<span class="' . esc_attr( $classe ) . '">' . esc_html( $testo ) . '</span>';
            }
        }

        return '' === $dentro ? '' : '<div class="olo-pg-cap">' . $dentro . '</div>';
    }

    /**
     * Check if gallery item is a video.
     */
    /**
     * L'inquadratura di UNA foto, ripulita, o '' se non ne ha una.
     *
     * ⚠️ Il valore finisce dentro un attributo `style`, quindi non basta
     * escaparlo: si accetta SOLO quello che può essere una object-position, cioè
     * una o due parole fra left/right/top/bottom/center oppure due misure in
     * percentuale o pixel. Tutto il resto diventa '', e la foto torna a seguire
     * la galleria. Un template importato da fuori è un pezzo di JSON come un
     * altro, e questa è la sola porta da cui quel JSON entra nel CSS.
     *
     * @param array|string $img  la voce della galleria
     * @return string  valore CSS valido, o '' per «segui la galleria»
     */
    private function fuoco_css( $img ) {
        if ( ! is_array( $img ) || empty( $img['focal'] ) ) {
            return '';
        }
        $v = strtolower( trim( (string) $img['focal'] ) );
        $pezzi = preg_split( '/\\s+/', $v, -1, PREG_SPLIT_NO_EMPTY );
        if ( ! $pezzi || count( $pezzi ) > 2 ) {
            return '';
        }
        foreach ( $pezzi as $p ) {
            $parola  = in_array( $p, [ 'left', 'right', 'top', 'bottom', 'center' ], true );
            $misura  = (bool) preg_match( '/^-?\\d+(\\.\\d+)?(%|px)$/', $p );
            if ( ! $parola && ! $misura ) {
                return '';
            }
        }
        return implode( ' ', $pezzi );
    }

    private function is_video_item( $img ) {
        return is_array( $img ) && ( ( $img['type'] ?? '' ) === 'video' );
    }

    /**
     * Check if gallery item is an embed video (YouTube/Vimeo).
     */
    private function is_embed_video( $img ) {
        return is_array( $img ) && ! empty( $img['embed'] );
    }

    /**
     * Get embed URL for YouTube/Vimeo.
     */
    private function get_gallery_embed_url( $url, $muted = false ) {
        if ( empty( $url ) ) return '';

        // YouTube
        if ( preg_match( '/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $url, $matches ) ) {
            $params = 'autoplay=1';
            if ( $muted ) {
                $params .= '&mute=1&loop=1&playlist=' . $matches[1];
            }
            return 'https://www.youtube.com/embed/' . $matches[1] . '?' . $params;
        }

        // Vimeo
        if ( preg_match( '/vimeo\.com\/(\d+)/', $url, $matches ) ) {
            $params = 'autoplay=1';
            if ( $muted ) {
                $params .= '&muted=1&loop=1&background=1';
            }
            return 'https://player.vimeo.com/video/' . $matches[1] . '?' . $params;
        }

        return '';
    }

    /**
     * Get video MIME type from URL extension.
     */
    private function get_video_mime( $url ) {
        $ext = strtolower( pathinfo( wp_parse_url( $url, PHP_URL_PATH ) ?: '', PATHINFO_EXTENSION ) );
        $map = [ 'mp4' => 'video/mp4', 'webm' => 'video/webm', 'ogg' => 'video/ogg', 'ogv' => 'video/ogg' ];
        return $map[ $ext ] ?? 'video/mp4';
    }

    /**
     * Render strip navigation arrows HTML + inline CSS.
     */
    private function strip_arrows_html( $uid, $style, $size, $color, $bg ) {
        $half = (int) round( $size / 2 );
        // SVG icons per stile
        $svg_left = $svg_right = '';
        $icon_vb = '0 0 24 24';
        $stroke_w = 2;
        if ( $style === 'chevron' ) {
            $svg_left  = '<polyline points="15 18 9 12 15 6" fill="none" stroke="' . $color . '" stroke-width="' . $stroke_w . '" stroke-linecap="round" stroke-linejoin="round"/>';
            $svg_right = '<polyline points="9 18 15 12 9 6" fill="none" stroke="' . $color . '" stroke-width="' . $stroke_w . '" stroke-linecap="round" stroke-linejoin="round"/>';
        } elseif ( $style === 'arrow' ) {
            $svg_left  = '<line x1="19" y1="12" x2="5" y2="12" stroke="' . $color . '" stroke-width="' . $stroke_w . '" stroke-linecap="round"/><polyline points="12 19 5 12 12 5" fill="none" stroke="' . $color . '" stroke-width="' . $stroke_w . '" stroke-linecap="round" stroke-linejoin="round"/>';
            $svg_right = '<line x1="5" y1="12" x2="19" y2="12" stroke="' . $color . '" stroke-width="' . $stroke_w . '" stroke-linecap="round"/><polyline points="12 5 19 12 12 19" fill="none" stroke="' . $color . '" stroke-width="' . $stroke_w . '" stroke-linecap="round" stroke-linejoin="round"/>';
        } elseif ( $style === 'minimal' ) {
            $stroke_w = 1.5;
            $svg_left  = '<polyline points="14 17 9 12 14 7" fill="none" stroke="' . $color . '" stroke-width="' . $stroke_w . '" stroke-linecap="round" stroke-linejoin="round"/>';
            $svg_right = '<polyline points="10 7 15 12 10 17" fill="none" stroke="' . $color . '" stroke-width="' . $stroke_w . '" stroke-linecap="round" stroke-linejoin="round"/>';
        } else {
            // circle, square, pill — chevron inside
            $svg_left  = '<polyline points="14 17 9 12 14 7" fill="none" stroke="' . $color . '" stroke-width="' . $stroke_w . '" stroke-linecap="round" stroke-linejoin="round"/>';
            $svg_right = '<polyline points="10 7 15 12 10 17" fill="none" stroke="' . $color . '" stroke-width="' . $stroke_w . '" stroke-linecap="round" stroke-linejoin="round"/>';
        }

        // Border-radius per stile
        $btn_radius = '4px';
        if ( $style === 'circle' ) { $btn_radius = '50%'; }
        elseif ( $style === 'pill' )   { $btn_radius = $size . 'px'; }
        elseif ( $style === 'minimal' ) { $btn_radius = '0'; }

        // Larghezza: pill è più largo
        $btn_w = $size;
        $btn_h = $size;
        if ( $style === 'pill' ) { $btn_w = (int) round( $size * 0.6 ); $btn_h = (int) round( $size * 1.4 ); }

        // Background: minimale non ha sfondo
        $btn_bg = ( $style === 'minimal' ) ? 'transparent' : $bg;
        $btn_shadow = ( $style === 'minimal' ) ? 'none' : '0 2px 8px rgba(0,0,0,.25)';

        $html = '<style>';
        $html .= ".{$uid}-sa-wrap{position:relative}";
        $html .= ".{$uid}-sa-btn{position:absolute;top:50%;transform:translateY(-50%);z-index:6;border:none;padding:0;cursor:pointer;display:flex;align-items:center;justify-content:center;";
        $html .= "width:{$btn_w}px;height:{$btn_h}px;background:{$btn_bg};border-radius:{$btn_radius};box-shadow:{$btn_shadow};opacity:.7;transition:opacity .25s ease}";
        $html .= ".{$uid}-sa-btn:hover{opacity:1}";
        $html .= ".{$uid}-sa-btn svg{width:{$half}px;height:{$half}px}";
        $html .= ".{$uid}-sa-prev{left:8px}";
        $html .= ".{$uid}-sa-next{right:8px}";
        $html .= "@media(max-width:640px){.{$uid}-sa-btn{width:" . max( 28, $btn_w - 8 ) . "px;height:" . max( 28, $btn_h - 8 ) . "px}.{$uid}-sa-prev{left:4px}.{$uid}-sa-next{right:4px}}";
        $html .= '</style>';
        $html .= '<button class="' . $uid . '-sa-btn ' . $uid . '-sa-prev" aria-label="' . esc_attr( olobuild_t( 'Precedente' ) ) . '" data-sa-dir="-1"><svg viewBox="' . $icon_vb . '">' . $svg_left . '</svg></button>';
        $html .= '<button class="' . $uid . '-sa-btn ' . $uid . '-sa-next" aria-label="' . esc_attr( olobuild_t( 'Successivo' ) ) . '" data-sa-dir="1"><svg viewBox="' . $icon_vb . '">' . $svg_right . '</svg></button>';
        return $html;
    }

    /**
     * Output shared JS once per page for: tilt3d, magnetic, entrance reveal, filmstrip, expand, strip kinetic.
     */
    private static function maybe_output_script( $tilt, $magnetic, $entrance, $filmstrip, $expand = false, $strip = false, $parallax = false, $drift = false, $cascade = false ) {
        if ( self::$script_output ) return;
        if ( ! $tilt && ! $magnetic && ! $entrance && ! $filmstrip && ! $expand && ! $strip && ! $parallax && ! $drift && ! $cascade ) return;
        self::$script_output = true;

        echo '<script data-olo-pg>';
        echo '(function(){';
        echo 'if(window._oloPGscript)return;window._oloPGscript=1;';
        echo 'var rm=window.matchMedia("(prefers-reduced-motion:reduce)").matches;';

        // ── Entrance reveal (IntersectionObserver) ──
        echo 'function initReveal(r){';
        echo 'var els=(r||document).querySelectorAll("[data-pg-reveal]");';
        echo 'if(!els.length)return;';
        echo 'if(rm){els.forEach(function(el){el.classList.add("olo-pg-visible")});return}';
        echo 'var obs=new IntersectionObserver(function(entries){';
        echo 'entries.forEach(function(e){if(e.isIntersecting){e.target.classList.add("olo-pg-visible");obs.unobserve(e.target)}});';
        echo '},{threshold:0.1});';
        echo 'els.forEach(function(el){obs.observe(el)});';
        echo '}';

        // ── Tilt 3D (mousemove) ──
        echo 'function initTilt(r){';
        echo 'if(rm)return;';
        echo '(r||document).querySelectorAll("[data-pg-tilt]").forEach(function(container){';
        echo 'var angle=parseInt(container.dataset.pgTilt)||10;';
        // Nel coverflow il transform della cornice è il suo posto nel giro: si inclina la foto dentro
        echo 'var cf=container.hasAttribute("data-pg-filmstrip");';
        echo 'container.querySelectorAll(".olo-pg-item").forEach(function(item){';
        echo 'var t=cf?(item.querySelector("img,video")||item):item;';
        echo 'item.addEventListener("mousemove",function(e){';
        echo 'var r=item.getBoundingClientRect();';
        echo 'var x=(e.clientX-r.left)/r.width-.5;';
        echo 'var y=(e.clientY-r.top)/r.height-.5;';
        echo 't.style.transform="perspective(600px) rotateY("+(x*angle)+"deg) rotateX("+(-y*angle)+"deg) scale("+(cf?1.14:1.02)+")";';
        echo '});';
        echo 'item.addEventListener("mouseleave",function(){t.style.transform=""});';
        echo '});});';
        echo '}';

        // ── Magnetic (mousemove) ──
        echo 'function initMagnetic(r){';
        echo 'if(rm)return;';
        echo '(r||document).querySelectorAll("[data-pg-magnetic]").forEach(function(container){';
        echo 'var str=parseInt(container.dataset.pgMagnetic)||24;';
        // Nel coverflow si sposta la foto dentro la cornice, ingrandita quanto basta a non scoprirne i bordi
        echo 'var cf=container.hasAttribute("data-pg-filmstrip");';
        echo 'container.querySelectorAll(".olo-pg-item").forEach(function(item){';
        echo 'var t=cf?(item.querySelector("img,video")||item):item;';
        echo 'item.addEventListener("mousemove",function(e){';
        echo 'var r=item.getBoundingClientRect();';
        echo 'var x=(e.clientX-r.left)/r.width-.5;';
        echo 'var y=(e.clientY-r.top)/r.height-.5;';
        echo 'var sc=cf?1+str/150:1+str/600;';
        echo 't.style.transform="translate("+(x*str)+"px,"+(y*str)+"px) scale("+sc+")";';
        echo '});';
        echo 'item.addEventListener("mouseleave",function(){t.style.transform=""});';
        echo '});});';
        echo '}';

        // ── Coverflow ──
        // Il nastro scorre davvero (scroll nativo con aggancio al centro): lo script misura la fila e
        // a ogni scorrimento mette ogni foto al suo posto. n = distanza dal centro, in passi:
        //   |n| ≤ 1 → la foto gira da 0° all'angolo, si allontana, passa dallo zoom del centro a 1
        //   |n| > 1 → stesso angolo, impilata a passo regolare, un filo più lontana a ogni passo
        // Le laterali guardano verso il centro (lato interno indietro). La correzione orizzontale dx
        // porta la fila piatta (passo S) al giro (prima vicina a D1 dal centro): oltre la prima è
        // costante, quindi scorrendo non scatta. La prospettiva è UNA, quella del nastro.
        echo 'function initFilmstrip(r){';
        echo '(r||document).querySelectorAll("[data-pg-filmstrip]").forEach(function(el){';
        echo 'if(el.dataset.pgFilmPronto)return;';
        echo 'var wrap=el.parentElement;if(!wrap)return;';
        echo 'var items=el.querySelectorAll(".olo-pg-item");if(!items.length)return;';
        echo 'el.dataset.pgFilmPronto="1";';
        echo 'var zoom=parseFloat(el.dataset.pgFilmZoom)||1.15;';
        echo 'var ang=parseFloat(el.dataset.pgFilmTilt);if(isNaN(ang))ang=35;';
        echo 'var wSet=parseFloat(el.dataset.pgFilmWidth)||280;';
        echo 'var autoSec=parseFloat(el.dataset.pgFilmAuto)||0;';
        echo 'var cid=el.className.split(" ")[0];';
        echo 'var dotStyle=el.dataset.pgFilmDots||"dots";';
        // Il «Gap» della tile si legge una volta: poi il passo lo danno i margini delle foto
        echo 'var gapSet=parseFloat(getComputedStyle(el).columnGap)||0;';
        echo 'var cosA=Math.cos(ang*Math.PI/180);';
        echo 'var W=wSet,S=100,D1=200,dep1=150,depN=15,centri=[],ancore=[],pronto=false;';
        // Larghezza (al massimo due terzi del nastro: sul telefono le laterali restano in vista),
        // passo, prima vicina, profondità e prospettiva
        echo 'function misura(){';
        echo 'var cw=el.clientWidth;if(cw<80)return false;';
        echo 'W=Math.round(Math.min(wSet,cw*0.66));';
        echo 'var P=Math.max(700,W*3.6);';
        echo 'dep1=W*0.55;depN=W*0.06;';
        echo 'S=Math.max(28,W*cosA*0.5+gapSet*0.5);';
        echo 'D1=(W*zoom/2+W*cosA*0.22+gapSet)*(P+dep1)/P;';
        echo 'el.style.gap="0px";el.style.perspective=Math.round(P)+"px";';
        echo 'el.style.setProperty("--cf-w",W+"px");';
        echo 'el.style.setProperty("--cf-mr",(S-W).toFixed(2)+"px");';
        echo 'el.style.setProperty("--cf-pad",Math.max(0,(cw-W)/2).toFixed(2)+"px");';
        echo 'items[items.length-1].style.marginRight="0px";';
        echo 'centri=[];for(var i=0;i<items.length;i++){centri.push(items[i].offsetLeft+items[i].offsetWidth/2)}';
        // Punti d'aggancio fermi, uno per foto, nel suo punto della fila piatta: lo scroll-snap misura il
        // riquadro TRASFORMATO dell'elemento, e qui le foto si spostano e ruotano a ogni passo (il nastro si
        // fermava a metà fra due foto). Le foto smettono di essere punti d'aggancio (classe olo-cf-on).
        echo 'if(!ancore.length){for(var j=0;j<items.length;j++){var an=document.createElement("span");an.className="olo-cf-snap";an.setAttribute("aria-hidden","true");el.appendChild(an);ancore.push(an)}el.classList.add("olo-cf-on")}';
        echo 'for(var k=0;k<ancore.length;k++){ancore[k].style.left=(centri[k]-1).toFixed(2)+"px"}';
        echo 'return true}';
        echo 'function vicino(){var ctr=el.scrollLeft+el.clientWidth/2,c=0,m=Infinity;for(var i=0;i<centri.length;i++){var d=Math.abs(centri[i]-ctr);if(d<m){m=d;c=i}}return c}';
        echo 'function salta(i){var sb=el.style.scrollBehavior;el.style.scrollBehavior="auto";el.scrollLeft=centri[i]-el.clientWidth/2;el.style.scrollBehavior=sb}';
        echo 'function vai(i){if(!pronto)return;if(i<0)i=0;if(i>items.length-1)i=items.length-1;el.scrollTo({left:centri[i]-el.clientWidth/2,behavior:rm?"auto":"smooth"})}';
        echo 'function aggiorna(){';
        echo 'if(!pronto)return;';
        echo 'var ctr=el.scrollLeft+el.clientWidth/2;';
        echo 'for(var i=0;i<items.length;i++){';
        echo 'var n=(centri[i]-ctr)/S,aN=Math.abs(n),a=aN<1?aN:1,s=n<0?-1:1;';
        echo 'var dx=s*a*(D1-S);';
        echo 'var tz=-(dep1*a+depN*(aN>1?aN-1:0));';
        echo 'var sc=1+(zoom-1)*(1-a);';
        echo 'items[i].style.transform="translate3d("+dx.toFixed(1)+"px,0,"+tz.toFixed(1)+"px) rotateY("+(-s*ang*a).toFixed(2)+"deg) scale("+sc.toFixed(3)+")";';
        echo 'items[i].style.zIndex=String(1000-Math.round(aN*20));';
        echo '}';
        // Indicatore
        echo 'var dotEl=wrap.querySelector("."+cid+"-dots");';
        echo 'if(dotEl){';
        echo 'var closest=vicino();';
        echo 'if(dotStyle==="dots"||dotStyle==="lines"){';
        echo 'dotEl.querySelectorAll("button,span").forEach(function(sp,idx){var on=idx===closest;sp.classList.toggle("active",on);if(on){sp.setAttribute("aria-current","true")}else{sp.removeAttribute("aria-current")}});';
        echo '}';
        echo 'if(dotStyle==="progress"){';
        echo 'var fill=dotEl.querySelector(".pg-prog-fill");';
        echo 'if(fill){var pct=items.length>1?(closest/(items.length-1))*100:100;fill.style.width=pct+"%";var trk=dotEl.querySelector(".pg-prog-track");if(trk){trk.setAttribute("aria-valuenow",Math.round(pct))}}';
        echo '}';
        echo 'if(dotStyle==="fraction"){';
        echo 'dotEl.textContent=(closest+1)+" / "+items.length;';
        echo '}';
        echo '}';
        echo '}';
        echo 'var raf=0;';
        echo 'el.addEventListener("scroll",function(){if(!raf){raf=requestAnimationFrame(function(){aggiorna();raf=0})}},{passive:true});';
        // Trascinamento col mouse — NIENTE "&&": WordPress lo converte in &#038;
        // pointerdown registra la posizione, pointermove trascina oltre 5px, pointerup sgancia.
        echo 'var mDown=false,mDrag=false,wasDrag=false,dStartX=0,dScrollL=0;';
        echo 'el.style.cursor="grab";';
        echo 'el.addEventListener("pointerdown",function(e){if(e.pointerType==="mouse"){if(e.button===0){mDown=true;mDrag=false;wasDrag=false;dStartX=e.clientX;dScrollL=el.scrollLeft}}});';
        echo 'el.addEventListener("pointermove",function(e){if(!mDown)return;var dx=e.clientX-dStartX;if(!mDrag){if(Math.abs(dx)>5){mDrag=true;el.setPointerCapture(e.pointerId);el.style.scrollSnapType="none";el.style.scrollBehavior="auto";el.style.cursor="grabbing"}else{return}}e.preventDefault();el.scrollLeft=dScrollL-dx});';
        echo 'el.addEventListener("pointerup",function(e){var eraDrag=mDrag;if(mDrag){el.releasePointerCapture(e.pointerId);wasDrag=true}mDown=false;mDrag=false;el.style.scrollSnapType="";el.style.scrollBehavior="";el.style.cursor="grab";if(eraDrag){vai(vicino())}});';
        echo 'el.addEventListener("pointercancel",function(){mDown=false;mDrag=false;el.style.scrollSnapType="";el.style.scrollBehavior="";el.style.cursor="grab"});';
        // Dopo un trascinamento il clic non apre la lightbox; il clic su una foto laterale la porta
        // al centro, quello sulla foto al centro apre la lightbox
        echo 'el.addEventListener("click",function(e){';
        echo 'if(wasDrag){wasDrag=false;e.preventDefault();e.stopPropagation();return}';
        echo 'var it=e.target.closest?e.target.closest(".olo-pg-item"):null;if(!it)return;';
        echo 'var ii=Array.prototype.indexOf.call(items,it);if(ii<0)return;';
        echo 'if(ii!==vicino()){e.preventDefault();e.stopPropagation();vai(ii)}';
        echo '},true);';
        // Indicatore: pallini e lineette come pulsanti
        echo 'var dotC=wrap.querySelector("."+cid+"-dots");';
        echo 'if(dotC){';
        echo 'if(dotStyle==="dots"||dotStyle==="lines"){';
        echo 'var dotLbl=' . wp_json_encode( olobuild_t( 'Vai alla foto' ) ) . ';';
        echo 'for(var di=0;di<items.length;di++){';
        echo 'var sp=document.createElement("button");';
        echo 'sp.type="button";';
        echo 'sp.setAttribute("aria-label",dotLbl+" "+(di+1));';
        echo 'sp.setAttribute("data-olo-interactive","");';
        echo 'if(dotStyle==="dots"){if(items.length>20){sp.style.width="9px";sp.style.height="9px"}}';
        echo 'if(dotStyle==="lines"){if(items.length>20){sp.style.width="12px"}}';
        echo '(function(idx){sp.addEventListener("click",function(){vai(idx)})})(di);';
        echo 'dotC.appendChild(sp);';
        echo '}';
        echo '}';
        echo 'if(dotStyle==="progress"){';
        echo 'var track=dotC.querySelector(".pg-prog-track");';
        echo 'if(track){track.addEventListener("click",function(e){';
        echo 'var rr=track.getBoundingClientRect();vai(Math.round((e.clientX-rr.left)/rr.width*(items.length-1)));';
        echo '});';
        echo 'track.addEventListener("keydown",function(e){';
        echo 'var ci=vicino(),ni=ci;';
        echo 'if(e.key==="ArrowLeft"||e.key==="ArrowDown"){ni=ci-1}';
        echo 'else if(e.key==="ArrowRight"||e.key==="ArrowUp"){ni=ci+1}';
        echo 'else if(e.key==="Home"){ni=0}';
        echo 'else if(e.key==="End"){ni=items.length-1}';
        echo 'else{return}';
        echo 'e.preventDefault();e.stopPropagation();vai(ni);';
        echo '})}';
        echo '}';
        echo '}';
        // Frecce e tastiera: una foto alla volta
        echo 'var prevBtn=wrap.querySelector("."+cid+"-prev");';
        echo 'var nextBtn=wrap.querySelector("."+cid+"-next");';
        echo 'if(prevBtn)prevBtn.addEventListener("click",function(e){e.stopPropagation();e.preventDefault();vai(vicino()-1)});';
        echo 'if(nextBtn)nextBtn.addEventListener("click",function(e){e.stopPropagation();e.preventDefault();vai(vicino()+1)});';
        echo 'wrap.addEventListener("keydown",function(e){';
        echo 'if(e.key==="ArrowLeft"){e.preventDefault();vai(vicino()-1)}';
        echo 'if(e.key==="ArrowRight"){e.preventDefault();vai(vicino()+1)}';
        echo '});';
        // Avanzamento automatico: in fondo torna alla prima
        echo 'if(autoSec>0){';
        echo 'var avanti=function(){var c=vicino();if(c>=items.length-1){vai(0)}else{vai(c+1)}};';
        echo 'var autoId=setInterval(avanti,autoSec*1000);';
        echo 'wrap.addEventListener("mouseenter",function(){clearInterval(autoId)});';
        echo 'wrap.addEventListener("mouseleave",function(){clearInterval(autoId);autoId=setInterval(avanti,autoSec*1000)});';
        echo 'el.addEventListener("touchstart",function(){clearInterval(autoId)},{passive:true});';
        echo 'el.addEventListener("touchend",function(){clearInterval(autoId);autoId=setInterval(avanti,autoSec*1000)},{passive:true});';
        echo '}';
        // Si parte dalla foto di mezzo; se il nastro è nascosto (scheda, fisarmonica) si misura
        // quando compare. Cambiando larghezza resta al centro la stessa foto.
        echo 'if(misura()){pronto=true;salta(Math.floor(items.length/2))}';
        echo 'aggiorna();';
        echo 'if(window.ResizeObserver){var lw=el.clientWidth;new ResizeObserver(function(){var w=el.clientWidth;if(Math.abs(w-lw)<1)return;lw=w;var c=pronto?vicino():Math.floor(items.length/2);if(misura()){pronto=true;salta(c);aggiorna()}}).observe(el)}';
        echo '});';
        echo '}';

        // ── Expand spotlight ──
        echo 'function initExpand(r){';
        echo 'if(rm||window.innerWidth<=640)return;';
        echo '(r||document).querySelectorAll("[data-pg-expand]").forEach(function(container){';
        echo 'var ratio=parseFloat(container.dataset.pgExpandRatio)||4;';
        echo 'var shrink=parseFloat(container.dataset.pgExpandShrink)||0.5;';
        echo 'var items=container.querySelectorAll(".olo-pg-item");';
        echo 'var cs=getComputedStyle(container);';
        echo 'var colCount=cs.gridTemplateColumns.split(" ").length;';
        echo 'var rowCount=cs.gridTemplateRows.split(" ").length;';
        echo 'items.forEach(function(item,idx){';
        echo 'item.addEventListener("mouseenter",function(){';
        echo 'var col=idx%colCount;var row=Math.floor(idx/colCount);';
        echo 'var cols=[];for(var c=0;c<colCount;c++)cols.push(c===col?ratio+"fr":shrink+"fr");';
        echo 'var rows=[];for(var r=0;r<rowCount;r++)rows.push(r===row?ratio+"fr":shrink+"fr");';
        echo 'container.style.gridTemplateColumns=cols.join(" ");';
        echo 'container.style.gridTemplateRows=rows.join(" ");';
        echo '});';
        echo '});';
        echo 'container.addEventListener("mouseleave",function(){';
        echo 'container.style.gridTemplateColumns="";';
        echo 'container.style.gridTemplateRows="";';
        echo '});';
        echo '});';
        echo '}';

        // ── Strip kinetic drag-to-scroll con momentum ──
        echo 'function initStrip(r){';
        echo 'if(rm)return;';
        echo '(r||document).querySelectorAll("[data-pg-strip]").forEach(function(el){';
        echo 'var pressed=false,startX=0,startScroll=0,vel=0,amp=0,frame=0,ts=0,tgt=0,tick=0,raf=0,wasDrag=false;';
        echo 'var TC=325;';
        echo 'function track(){var now=Date.now();var elapsed=now-ts;var delta=el.scrollLeft-frame;ts=now;frame=el.scrollLeft;var v=1000*delta/(1+elapsed);vel=0.8*v+0.2*vel}';
        echo 'function momentum(){if(amp){var elapsed=Date.now()-ts;var delta=-amp*Math.exp(-elapsed/TC);if(Math.abs(delta)>0.5){el.scrollLeft=tgt+delta;raf=requestAnimationFrame(momentum)}else{el.scrollLeft=tgt}}}';
        echo 'function onMove(e){if(!pressed)return;var dx=e.clientX-startX;if(Math.abs(dx)>5){wasDrag=true;el.style.cursor="grabbing"}el.scrollLeft=startScroll-dx;e.preventDefault()}';
        echo 'function onUp(){if(!pressed)return;pressed=false;document.removeEventListener("pointermove",onMove);document.removeEventListener("pointerup",onUp);clearInterval(tick);el.style.cursor="grab";if(Math.abs(vel)>10){amp=0.8*vel;tgt=Math.round(el.scrollLeft+amp);ts=Date.now();raf=requestAnimationFrame(momentum)}}';
        echo 'el.style.cursor="grab";';
        echo 'el.addEventListener("pointerdown",function(e){if(e.button!==0)return;pressed=true;wasDrag=false;startX=e.clientX;startScroll=el.scrollLeft;vel=0;amp=0;frame=el.scrollLeft;ts=Date.now();if(raf){cancelAnimationFrame(raf);raf=0}clearInterval(tick);tick=setInterval(track,100);document.addEventListener("pointermove",onMove);document.addEventListener("pointerup",onUp)});';
        echo 'el.addEventListener("click",function(e){if(wasDrag){wasDrag=false;e.preventDefault();e.stopPropagation()}},true);';
        echo 'el.addEventListener("wheel",function(e){if(Math.abs(e.deltaY)>Math.abs(e.deltaX)){e.preventDefault();el.scrollLeft+=e.deltaY}},{passive:false});';
        echo '});';
        echo '}';

        // ── Strip arrows click ──
        echo 'function initStripArrows(r){';
        echo '(r||document).querySelectorAll("[data-sa-dir]").forEach(function(btn){';
        echo 'btn.addEventListener("click",function(e){';
        echo 'e.preventDefault();';
        echo 'var wrap=btn.closest("[style]");if(!wrap)return;';
        echo 'var el=wrap.querySelector("[data-pg-strip]");';
        // Per marquee/split senza data-pg-strip, cerchiamo il primo figlio scrollabile
        echo 'if(!el){var ch=wrap.children;for(var i=0;i<ch.length;i++){if(ch[i].tagName!=="BUTTON"){if(ch[i].tagName!=="STYLE"){el=ch[i];break}}}}';
        echo 'if(!el)return;';
        echo 'var dir=parseInt(btn.getAttribute("data-sa-dir"));';
        echo 'var step=el.clientWidth*0.7;';
        echo 'el.scrollBy({left:dir*step,behavior:"smooth"});';
        echo '});});';
        echo '}';

        // ── Parallax (scroll-driven depth) ──
        echo 'function initParallax(r){';
        echo 'if(rm)return;';
        echo '(r||document).querySelectorAll("[data-pg-parallax]").forEach(function(container){';
        echo 'var intensity=parseFloat(container.dataset.pgPlxIntensity)||50;';
        echo 'var items=container.querySelectorAll(".olo-pg-item[data-speed]");';
        echo 'if(!items.length)return;';
        // Salva il baseTransform iniziale
        echo 'items.forEach(function(it){if(!it.dataset.baseTransform){it.dataset.baseTransform=it.style.transform||""}});';
        echo 'var raf=0;';
        echo 'function update(){';
        echo 'var rect=container.getBoundingClientRect();';
        echo 'var ctrY=rect.top+rect.height/2;';
        echo 'var vpCtr=window.innerHeight/2;';
        echo 'var delta=ctrY-vpCtr;';
        echo 'items.forEach(function(it){';
        echo 'var sp=parseFloat(it.dataset.speed)||0;';
        echo 'var ty=Math.round(delta*sp);';
        echo 'var base=it.dataset.baseTransform||"";';
        echo 'it.style.transform=base+" translateY("+ty+"px)";';
        echo '});';
        echo '}';
        echo 'window.addEventListener("scroll",function(){if(!raf){raf=requestAnimationFrame(function(){update();raf=0})}},{passive:true});';
        echo 'update();';
        echo '});';
        echo '}';

        // ── Drift (multi-directional scroll parallax) ──
        echo 'function initDrift(r){';
        echo 'if(rm)return;';
        echo '(r||document).querySelectorAll("[data-pg-drift]").forEach(function(container){';
        echo 'var items=container.querySelectorAll(".olo-pg-item[data-drift-sx]");';
        echo 'if(!items.length)return;';
        echo 'items.forEach(function(it){if(!it.dataset.baseTransform){it.dataset.baseTransform=it.style.transform||""}});';
        echo 'var raf=0;';
        echo 'function update(){';
        echo 'var rect=container.getBoundingClientRect();';
        echo 'var ctrY=rect.top+rect.height/2;';
        echo 'var vpCtr=window.innerHeight/2;';
        echo 'var delta=ctrY-vpCtr;';
        echo 'items.forEach(function(it){';
        echo 'var sx=parseFloat(it.dataset.driftSx)||0;';
        echo 'var sy=parseFloat(it.dataset.driftSy)||0;';
        echo 'var sr=parseFloat(it.dataset.driftSr)||0;';
        echo 'var tx=Math.round(delta*sx);';
        echo 'var ty=Math.round(delta*sy);';
        echo 'var r=Math.round(delta*sr*0.02*10)/10;';
        echo 'var base=it.dataset.baseTransform||"";';
        echo 'it.style.transform="translate("+tx+"px,"+ty+"px) rotate("+r+"deg)";';
        echo '});';
        echo '}';
        echo 'window.addEventListener("scroll",function(){if(!raf){raf=requestAnimationFrame(function(){update();raf=0})}},{passive:true});';
        echo 'update();';
        echo '});';
        echo '}';

        // ── Cascade (stacked cards that spread on scroll) ──
        echo 'function initCascade(r){';
        echo 'if(rm)return;';
        echo '(r||document).querySelectorAll("[data-pg-cascade]").forEach(function(container){';
        echo 'var spread=parseFloat(container.dataset.pgCascadeSpread)||60;';
        echo 'var items=container.querySelectorAll(".olo-pg-item[data-cascade-idx]");';
        echo 'if(!items.length)return;';
        echo 'var total=items.length;';
        echo 'var raf=0;';
        echo 'function update(){';
        echo 'var rect=container.getBoundingClientRect();';
        echo 'var vpH=window.innerHeight;';
        // progress: 0 = top of container at bottom of viewport, 1 = bottom at top
        echo 'var progress=Math.max(0,Math.min(1,(vpH-rect.top)/(vpH+rect.height)));';
        echo 'items.forEach(function(it){';
        echo 'var idx=parseInt(it.dataset.cascadeIdx)||0;';
        echo 'var baseRot=parseFloat(it.dataset.cascadeRot)||0;';
        echo 'var baseSx=parseFloat(it.dataset.cascadeSx)||0;';
        // Ogni carta si sparge in direzione diversa con il progresso dello scroll
        echo 'var spreadY=progress*spread*idx;';
        echo 'var spreadX=progress*baseSx*spread/15;';
        echo 'var rot=baseRot*(0.3+progress*0.7);';
        echo 'it.style.transform="translate("+Math.round(spreadX)+"px,"+Math.round(spreadY)+"px) rotate("+rot.toFixed(1)+"deg)";';
        echo '});';
        echo '}';
        echo 'window.addEventListener("scroll",function(){if(!raf){raf=requestAnimationFrame(function(){update();raf=0})}},{passive:true});';
        echo 'update();';
        echo '});';
        echo '}';

        // ── Init ──
        // ── Nastro: frecce spente agli estremi, sfumatura solo dove c'è altro, barretta di avanzamento ──
        echo 'function initStripStato(r){(r||document).querySelectorAll("[data-pg-strip]").forEach(function(el){if(el.__pgSt)return;el.__pgSt=1;';
        echo 'var bar=el.nextElementSibling;if(bar?!bar.classList.contains("olo-pg-avanza"):true)bar=null;';
        echo 'var wrap=el.parentNode,pv=wrap?wrap.querySelector("[data-sa-dir=\\"-1\\"]"):null,nx=wrap?wrap.querySelector("[data-sa-dir=\\"1\\"]"):null;';
        echo 'function up(){var max=el.scrollWidth-el.clientWidth,x=el.scrollLeft,ini=x<4,fin=x>max-4;';
        echo 'if(el.hasAttribute("data-pg-strip-fade")){el.style.setProperty("--pg-fs",ini?"0%":"6%");el.style.setProperty("--pg-fe",fin?"0%":"6%")}';
        echo 'if(pv)pv.classList.toggle("olo-pg-sa-off",ini);if(nx)nx.classList.toggle("olo-pg-sa-off",fin);';
        echo 'if(bar){var t=bar.firstElementChild,w=max>0?el.clientWidth/el.scrollWidth:1;t.style.width=(w*100)+"%";t.style.marginLeft=(max>0?(x/max)*(1-w)*100:0)+"%"}}';
        echo 'el.addEventListener("scroll",up,{passive:true});window.addEventListener("resize",up);up();setTimeout(up,600)})}';
        echo 'function init(r){initReveal(r);initTilt(r);initMagnetic(r);initFilmstrip(r);initExpand(r);initStrip(r);initStripArrows(r);initStripStato(r);initParallax(r);initDrift(r);initCascade(r)}';
        // init(r) vuole un contenitore: passato direttamente, DOMContentLoaded le dava l'evento e
        // (r||document).querySelectorAll andava in errore → le gallerie sopra la piega senza effetti.
        echo 'if(document.readyState==="loading"){document.addEventListener("DOMContentLoaded",function(){init()})}else{init()}';
        // Lo script si scrive una volta sola per pagina: le gallerie che nascono DOPO dal
        // caricamento pigro (il <template> di una tile sotto la piega) non le avvia nessuno.
        // All'idratazione si avviano quelle del blocco appena nato; se il blocco contiene
        // questo stesso script, e' lui ad avviarle (niente doppio avvio).
        echo 'document.addEventListener("olo:lazy-hydrated",function(ev){var t=ev.detail?ev.detail.target:null;if(!t)return;if(t.querySelector("script[data-olo-pg]"))return;init(t)});';
        echo '})();';
        echo '</script>';
    }

    // ═══════════════════════════════════════════════════════════════════════════════════════════════
    // TESTI SULLE FOTO, ELENCO A RIGHE, FILTRI E MISURE SENZA BUCHI (23 nov 2026)
    //
    // Per fare con la Pro Gallery quello che fanno le griglie dei portfolio e delle riviste: su ogni foto
    // categoria, numero, titolo e icona (o i testi sotto la foto), le foto in un elenco a righe, i filtri
    // per categoria con la galleria che si riordina, e negli schemi a misure miste (Metro, Mosaico,
    // Collage) le misure scelte in modo che la griglia si riempia senza buchi, anche al telefono.
    // ═══════════════════════════════════════════════════════════════════════════════════════════════

    /** Gli schemi che reggono i testi sulle foto (gemello SCHEMI_SCHEDE in progallery.js). */
    const SCHEMI_SCHEDE = [ 'grid', 'justified', 'masonry', 'metro', 'mosaic', 'collage', 'diagonal', 'expand', 'elenco', 'strip' ];
    /** Gli schemi che reggono i filtri (gemello SCHEMI_FILTRI in progallery.js). */
    const SCHEMI_FILTRI = [ 'grid', 'justified', 'masonry', 'metro', 'mosaic', 'collage', 'diagonal', 'elenco', 'strip' ];

    /**
     * Le categorie di una foto, dalla riga «Categoria» (più categorie separate da virgole): [ slug => nome ].
     *
     * @param array|string $img la voce della galleria
     * @return array
     */
    private function categorie_voce( $img ) {
        $out = [];
        if ( ! is_array( $img ) ) {
            return $out;
        }
        foreach ( explode( ',', (string) ( $img['category'] ?? '' ) ) as $c ) {
            $c = trim( $c );
            if ( '' !== $c ) {
                $p            = $this->categoria_parti( $c );
                $out[ $p[2] ] = $p[3];
            }
        }
        return $out;
    }

    /**
     * La misura scelta a mano per una foto nello schema Metro: «2x1» → [ 2, 1 ]; senza → null.
     *
     * @param array|string $img la voce della galleria
     * @return array|null
     */
    private function misura_voce( $img ) {
        $m = is_array( $img ) ? (string) ( $img['misura'] ?? '' ) : '';
        if ( preg_match( '/^([1-4])x([1-4])$/', $m, $x ) ) {
            return [ (int) $x[1], (int) $x[2] ];
        }
        return null;
    }

    /**
     * Le misure che ogni foto vorrebbe nello schema (prima di togliere i buchi). Metro: una foto grande ogni
     * sette, una larga e una alta, oppure la misura scelta a mano; Mosaico: una grande e una larga ogni cinque;
     * Collage: la prima grande.
     *
     * @param string $layout schema
     * @param array  $imgs   le foto visibili
     * @return array [ [ colonne, righe ], … ]
     */
    private function misure_desiderate( $layout, $imgs ) {
        $out = [];
        foreach ( array_values( $imgs ) as $k => $img ) {
            $m = [ 1, 1 ];
            if ( 'metro' === $layout ) {
                $q = $k % 7;
                $m = 0 === $q ? [ 2, 2 ] : ( 3 === $q ? [ 2, 1 ] : ( 5 === $q ? [ 1, 2 ] : [ 1, 1 ] ) );
                $scelta = $this->misura_voce( $img );
                if ( $scelta ) {
                    $m = $scelta;
                }
            } elseif ( 'mosaic' === $layout ) {
                $q = $k % 5;
                $m = 0 === $q ? [ 2, 2 ] : ( 3 === $q ? [ 2, 1 ] : [ 1, 1 ] );
            } elseif ( 'collage' === $layout ) {
                $m = 0 === $k ? [ 2, 2 ] : [ 1, 1 ];
            }
            $out[] = $m;
        }
        return $out;
    }

    /**
     * Posa le piastre come la griglia «dense» del CSS (grid-auto-flow: row dense): ognuna al primo posto libero,
     * riga per riga e da sinistra. Restituisce [ posizioni, occupazione, righe ].
     */
    private function piastre_posa( $mis, $cols ) {
        $occ   = [];
        $pos   = [];
        $righe = 0;
        foreach ( $mis as $k => $m ) {
            $w = $m[0];
            $h = $m[1];
            $r = 0;
            $c = 0;
            for ( $r = 0; $r < 400; $r++ ) {
                $posto = false;
                for ( $c = 0; $c + $w <= $cols; $c++ ) {
                    $libero = true;
                    for ( $y = $r; $y < $r + $h && $libero; $y++ ) {
                        for ( $x = $c; $x < $c + $w; $x++ ) {
                            if ( isset( $occ[ $y ][ $x ] ) ) {
                                $libero = false;
                                break;
                            }
                        }
                    }
                    if ( $libero ) {
                        $posto = true;
                        break;
                    }
                }
                if ( $posto ) {
                    break;
                }
            }
            for ( $y = $r; $y < $r + $h; $y++ ) {
                for ( $x = $c; $x < $c + $w; $x++ ) {
                    $occ[ $y ][ $x ] = $k;
                }
            }
            $pos[ $k ] = [ $r, $c ];
            $righe     = max( $righe, $r + $h );
        }
        return [ $pos, $occ, $righe ];
    }

    /** Quante caselle restano vuote fino all'ultima riga. */
    private function piastre_buchi( $mis, $cols ) {
        list( , $occ, $righe ) = $this->piastre_posa( $mis, $cols );
        $b = 0;
        for ( $r = 0; $r < $righe; $r++ ) {
            for ( $c = 0; $c < $cols; $c++ ) {
                if ( ! isset( $occ[ $r ][ $c ] ) ) {
                    $b++;
                }
            }
        }
        return $b;
    }

    /**
     * LE MISURE SENZA BUCHI.
     *
     * Metro, Mosaico e Collage davano le misure con regole fisse («la settima foto grande»): con un numero di
     * foto qualsiasi, o al telefono con meno colonne, restavano caselle vuote in mezzo e in fondo. Qui si
     * posano le foto come le posa il CSS e, finché c'è un buco, si allarga la foto alla sua sinistra o si
     * allunga quella sopra; se non basta si rimpicciolisce l'ultima foto grande. Il CSS riceve solo le misure
     * (span), e la griglia dense le mette negli stessi posti calcolati qui.
     *
     * @param array $desiderate [ [ colonne, righe ], … ]
     * @param int   $cols       colonne della griglia
     * @return array le misure finali, nello stesso ordine
     */
    private function piastre( $desiderate, $cols ) {
        $n   = count( $desiderate );
        $mis = [];
        foreach ( $desiderate as $d ) {
            $mis[] = [ max( 1, min( $cols, (int) $d[0] ) ), max( 1, min( 4, (int) $d[1] ) ) ];
        }
        if ( 1 === $cols ) {
            // Una colonna sola: ogni foto è larga quanto la colonna, le «alte» restano alte due righe.
            foreach ( $mis as $k => $m ) {
                $mis[ $k ] = [ 1, min( 2, $m[1] ) ];
            }
            return $mis;
        }
        for ( $giro = 0; $giro < 120; $giro++ ) {
            list( $pos, $occ, $righe ) = $this->piastre_posa( $mis, $cols );
            $buco = null;
            for ( $r = 0; $r < $righe && ! $buco; $r++ ) {
                for ( $c = 0; $c < $cols; $c++ ) {
                    if ( ! isset( $occ[ $r ][ $c ] ) ) {
                        $buco = [ $r, $c ];
                        break;
                    }
                }
            }
            if ( ! $buco ) {
                return $mis;
            }
            list( $r, $c ) = $buco;
            $prima = $this->piastre_buchi( $mis, $cols );
            $fatto = false;
            // 1) la foto a sinistra del buco, alta una riga che comincia lì: si allarga di una colonna
            if ( $c > 0 && isset( $occ[ $r ][ $c - 1 ] ) ) {
                $k = $occ[ $r ][ $c - 1 ];
                if ( $pos[ $k ][0] === $r && 1 === $mis[ $k ][1] ) {
                    $prova        = $mis;
                    $prova[ $k ][0]++;
                    if ( $this->piastre_buchi( $prova, $cols ) < $prima ) {
                        $mis   = $prova;
                        $fatto = true;
                    }
                }
            }
            // 2) la foto sopra il buco, larga una colonna che comincia lì: si allunga di una riga
            if ( ! $fatto && $r > 0 && isset( $occ[ $r - 1 ][ $c ] ) ) {
                $k = $occ[ $r - 1 ][ $c ];
                if ( $pos[ $k ][1] === $c && 1 === $mis[ $k ][0] && $mis[ $k ][1] < 4 ) {
                    $prova        = $mis;
                    $prova[ $k ][1]++;
                    if ( $this->piastre_buchi( $prova, $cols ) < $prima ) {
                        $mis   = $prova;
                        $fatto = true;
                    }
                }
            }
            // 3) altrimenti l'ultima foto grande si rimpicciolisce
            if ( ! $fatto ) {
                for ( $k = $n - 1; $k >= 0; $k-- ) {
                    if ( $mis[ $k ][1] > 1 ) {
                        $mis[ $k ][1]--;
                        $fatto = true;
                        break;
                    }
                    if ( $mis[ $k ][0] > 1 ) {
                        $mis[ $k ][0]--;
                        $fatto = true;
                        break;
                    }
                }
            }
            if ( ! $fatto ) {
                break;
            }
        }
        return $mis;
    }

    /** Le opzioni dei testi sulle foto, già pulite. */
    private function scheda_opzioni( $s, $layout ) {
        $scheda = in_array( $s['scheda'] ?? '', [ 'sopra', 'sotto', 'biglietto', 'riquadro' ], true ) ? $s['scheda'] : '';
        if ( 'elenco' === $layout ) {
            $scheda = 'elenco';
        } elseif ( ! in_array( $layout, self::SCHEMI_SCHEDE, true ) ) {
            $scheda = '';
        } elseif ( ( 'sotto' === $scheda && ! in_array( $layout, [ 'grid', 'masonry', 'strip' ], true ) ) || ( 'biglietto' === $scheda && ! in_array( $layout, [ 'grid', 'masonry' ], true ) ) ) {
            $scheda = 'sopra';
        }
        return [
            'modo'        => $scheda,
            'passaggio'   => in_array( $scheda, [ 'sopra', 'riquadro' ], true ) && 'passaggio' === ( $s['scheda_quando'] ?? '' ),
            'categoria'   => ! empty( $s['scheda_categoria'] ),
            'numero'      => ! empty( $s['scheda_numero'] ),
            'titolo'      => ! empty( $s['scheda_titolo'] ),
            'sottotitolo' => ! empty( $s['scheda_sottotitolo'] ),
            'testo'       => ! empty( $s['scheda_testo'] ),
            'icona'       => in_array( $s['scheda_icona'] ?? '', [ 'piu', 'freccia', 'lente', 'play', 'linea', 'destra' ], true ) ? $s['scheda_icona'] : '',
            'nota_stile'  => in_array( $s['scheda_nota_stile'] ?? '', [ 'grande', 'etichetta', 'linea' ], true ) ? $s['scheda_nota_stile'] : '',
            'angolo_et'   => in_array( $s['scheda_etichetta_angolo'] ?? '', [ 'as', 'ad', 'bs', 'bd' ], true ) ? $s['scheda_etichetta_angolo'] : 'as',
            'prefisso'    => (string) ( $s['scheda_numero_prefisso'] ?? '' ),
            'suffisso'    => (string) ( $s['scheda_numero_suffisso'] ?? '' ),
            'rovescio'    => ! empty( $s['scheda_numero_rovescio'] ),
            'num_icona'   => trim( (string) ( $s['scheda_numero_icona'] ?? '' ) ),
            'cornice_meta' => ! empty( $s['scheda_cornice_meta'] ),
            'campione'    => ! empty( $s['scheda_campione'] ),
            'barra'       => ! empty( $s['scheda_barra'] ),
            'punto_testo' => ! empty( $s['scheda_punto_testo'] ),
            'durata_et'   => 'etichetta' === ( $s['scheda_durata_stile'] ?? '' ),
            'riq_grandi'  => 'riquadro' === $scheda && ! empty( $s['scheda_riquadro_grandi'] ),
            'sot_grandi'  => ! empty( $s['scheda_sot_grandi'] ),
            'cat_gruppo'  => sanitize_title( (string) ( $s['scheda_categoria_gruppo'] ?? '' ) ),
            'grandi'      => [],
            'miniatura'   => ! empty( $s['elenco_miniatura'] ),
            'pulsante'    => trim( (string) ( $s['scheda_pulsante'] ?? '' ) ),
            'num_grande'  => ! empty( $s['scheda_numero_grande'] ),
            'durata'      => ! empty( $s['scheda_durata'] ),
            'numero_fmt'  => in_array( $s['scheda_numero_formato'] ?? '', [ 'su', 'n', 'semplice', 'tre' ], true ) ? $s['scheda_numero_formato'] : '',
            'zone'        => $this->zone_scheda( $s ),
            'totale'      => 0,
        ];
    }

    /** Dove va ogni parte della card, se è stata scelta (Stile › Testi sulle foto): [ parte => zona ]. */
    private function zone_scheda( $s ) {
        $z = [];
        // Oltre alle nove zone: il piede (una riga in fondo, a sinistra o a destra) e, per le parti piccole, le
        // posizioni legate al titolo (prima, dopo nella stessa riga, sotto)
        $legate = [ 'pt', 'tt', 'dt' ];
        foreach ( [ 'cat' => 'zona_categoria', 'num' => 'zona_numero', 'tit' => 'zona_titolo', 'sot' => 'zona_sottotitolo', 'txt' => 'zona_testo', 'btn' => 'zona_pulsante', 'ico' => 'zona_icona', 'nota' => 'zona_nota', 'dur' => 'zona_durata' ] as $parte => $k ) {
            $v = (string) ( $s[ $k ] ?? '' );
            if ( in_array( $v, [ 'as', 'ac', 'ad', 'cs', 'cc', 'cd', 'bs', 'bc', 'bd', 'ps', 'pd', 'fas', 'fad', 'fbs', 'fbd' ], true ) || ( 'tit' !== $parte && in_array( $v, $legate, true ) ) ) {
                $z[ $parte ] = $v;
            }
        }
        return $z;
    }

    /**
     * La durata di un video: quella scritta nella voce («7:40»), se no quella dei metadati della libreria (anche se
     * la voce ha solo l'indirizzo del file). Per le foto, niente.
     */
    private function durata_voce( $img ) {
        $d = is_array( $img ) ? trim( (string) ( $img['durata'] ?? '' ) ) : '';
        if ( '' !== $d ) {
            // Scritta a mano vale anche per una foto: la copertina di un video che sta altrove
            return $d;
        }
        if ( ! $this->is_video_item( $img ) ) {
            return '';
        }
        $id = absint( $img['id'] ?? 0 );
        if ( ! $id ) {
            $url = (string) ( $img['url'] ?? '' );
            if ( '' === $url || false === strpos( $url, (string) wp_parse_url( home_url(), PHP_URL_HOST ) ) ) {
                return '';
            }
            $id = (int) attachment_url_to_postid( $url );
        }
        $m = $id ? wp_get_attachment_metadata( $id ) : [];
        $l = is_array( $m ) ? (int) ( $m['length'] ?? 0 ) : 0;
        if ( $l > 0 ) {
            return $this->durata_testo( $l );
        }
        return is_array( $m ) ? (string) ( $m['length_formatted'] ?? '' ) : '';
    }

    /** I secondi come «7:40» (o «1:02:05»). */
    private function durata_testo( $sec ) {
        $sec = max( 0, (int) $sec );
        $h   = intdiv( $sec, 3600 );
        $m   = intdiv( $sec % 3600, 60 );
        $x   = $sec % 60;
        return $h ? sprintf( '%d:%02d:%02d', $h, $m, $x ) : sprintf( '%d:%02d', $m, $x );
    }

    /** Il numero della voce nel formato scelto: «01», «01 / 12», «N. 01 / 12» o «1». */
    private function numero_scheda( $n, $o ) {
        return wp_strip_all_tags( html_entity_decode( $this->numero_html( $n, $o ), ENT_QUOTES, 'UTF-8' ) );
    }

    /**
     * Il numero della voce già escapato, col prefisso e il suffisso scelti («Q01», «(01)», «L·01») e il totale
     * attenuato («No. 01 / 12»). Al contrario conta all'indietro, dal più alto (gli indici dei progetti).
     */
    private function numero_html( $n, $o ) {
        $tot = (int) ( $o['totale'] ?? 0 );
        if ( ! empty( $o['rovescio'] ) && $tot >= $n ) {
            $n = $tot - $n + 1;
        }
        $nn  = str_pad( (string) $n, 2, '0', STR_PAD_LEFT );
        $tt  = str_pad( (string) $tot, 2, '0', STR_PAD_LEFT );
        $su  = function ( $x ) {
            return '<span class="olo-pg-num-tot">/ ' . esc_html( $x ) . '</span>';
        };
        switch ( $o['numero_fmt'] ?? '' ) {
            case 'su':
                $h = esc_html( $nn ) . ( $tot ? ' ' . $su( $tt ) : '' );
                break;
            case 'n':
                $h = esc_html( olobuild_t( 'N.' ) . ' ' . $nn ) . ( $tot ? ' ' . $su( $tt ) : '' );
                break;
            case 'semplice':
                $h = esc_html( (string) $n );
                break;
            case 'tre':
                $h = esc_html( str_pad( (string) $n, 3, '0', STR_PAD_LEFT ) );
                break;
            default:
                $h = esc_html( $nn );
        }
        $pre = (string) ( $o['prefisso'] ?? '' );
        $suf = (string) ( $o['suffisso'] ?? '' );
        $ico = ! empty( $o['num_icona'] ) ? $this->render_icon_html( $o['num_icona'], .6, 'class="olo-pg-num-ico" aria-hidden="true"' ) : '';
        return $ico . ( '' !== $pre ? esc_html( $pre ) : '' ) . $h . ( '' !== $suf ? esc_html( $suf ) : '' );
    }

    /**
     * Le parti col testo in corsivo: «Dressed for *the long light.*» → il pezzo fra asterischi in corsivo (nel
     * colore scelto per il corsivo), come i titoli delle riviste. Senza asterischi il testo resta com'è.
     */
    private function corsivo_html( $t ) {
        $h = esc_html( $t );
        if ( false === strpos( $t, '*' ) ) {
            return $h;
        }
        return preg_replace( '/\*([^*]+)\*/u', '<em class="olo-pg-em">$1</em>', $h );
    }

    /** L'icona della scheda (set SVG, mai emoji). */
    private function scheda_icona( $nome ) {
        $d = [
            'piu'     => '<path d="M12 6v12M6 12h12"/>',
            'freccia' => '<path d="M8 16 16 8M9 8h7v7"/>',
            'lente'   => '<circle cx="11" cy="11" r="5.5"/><path d="m15.5 15.5 3.5 3.5"/>',
            'play'    => '<path d="M9 7.5v9l7.5-4.5z" fill="currentColor"/>',
            'destra'  => '<path d="M6 12h12M13 7l5 5-5 5"/>',
        ];
        if ( 'linea' === $nome ) {
            // La freccia lunga: una linea sottile con la punta piena (le pareti di video)
            return '<span class="olo-pg-sk-ico olo-pg-ico-linea" aria-hidden="true"><svg viewBox="0 0 40 12" width="40" height="12" fill="none" stroke="currentColor" stroke-width="1.4"><path d="M1 6h31"/><path d="M31 1.5 39 6l-8 4.5z" fill="currentColor" stroke="none"/></svg></span>';
        }
        if ( empty( $d[ $nome ] ) ) {
            return '';
        }
        return '<span class="olo-pg-sk-ico olo-pg-ico-' . esc_attr( $nome ) . '" aria-hidden="true"><svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">' . $d[ $nome ] . '</svg></span>';
    }

    /**
     * I testi di una foto: categoria e numero in alto, titolo, sottotitolo e testo in basso con l'icona.
     * Nell'elenco a righe le stesse parti diventano le colonne della riga.
     *
     * @param array|string $img la voce
     * @param int          $n   la posizione (1, 2, …)
     * @param array        $o   scheda_opzioni()
     * @return string HTML già escapato
     */
    private function scheda_html( $img, $n, $o ) {
        $this->sk_foto = '';
        $v    = is_array( $img ) ? $img : [];
        $tit0 = trim( (string) ( $v['caption'] ?? '' ) );
        $tit  = $this->corsivo_html( $tit0 );
        $sot  = trim( (string) ( $v['subtitle'] ?? '' ) );
        $txt  = trim( (string) ( $v['text'] ?? '' ) );
        $nota = trim( (string) ( $v['nota'] ?? '' ) );
        $cats = $this->categorie_voce( $v );
        if ( '' !== ( $o['cat_gruppo'] ?? '' ) ) {
            // Solo i valori del gruppo scelto: con «Tipo: Guida, Livello: Base» e il gruppo «Tipo» resta «Guida»
            $pre = $o['cat_gruppo'] . '--';
            $del = function ( $pre ) use ( $cats ) {
                return array_filter( $cats, function ( $kk ) use ( $pre ) {
                    return 0 === strpos( (string) $kk, $pre );
                }, ARRAY_FILTER_USE_KEY );
            };
            $sel = $del( $pre );
            if ( ! $sel && $cats ) {
                // Il gruppo scelto non c'è nella voce (nella pagina tradotta «Tipo» è diventato «Type», o il gruppo è
                // stato rinominato): vale il PRIMO gruppo della voce, invece di togliere la categoria
                $k0  = (string) array_key_first( $cats );
                $cut = strpos( $k0, '--' );
                $sel = false !== $cut ? $del( substr( $k0, 0, $cut + 2 ) ) : [];
            }
            $cats = $sel;
        }
        $cat    = implode( ' · ', $cats );
        $num    = $this->numero_html( $n, $o );
        $grande = in_array( $n, $o['grandi'] ?? [], true );
        if ( 'elenco' === $o['modo'] ) {
            $h  = $o['numero'] ? '<span class="olo-pg-el-num">' . $num . '</span>' : '';
            $h .= '<span class="olo-pg-el-tit">' . ( $o['titolo'] ? $tit : '' ) . '</span>';
            $h .= '<span class="olo-pg-el-sot">' . ( $o['sottotitolo'] ? esc_html( $sot ) : '' ) . '</span>';
            $h .= '<span class="olo-pg-el-cat">' . ( $o['categoria'] ? esc_html( $cat ) : '' ) . '</span>';
            $h .= '<span class="olo-pg-el-txt">' . ( $o['testo'] ? esc_html( $txt ) : '' ) . '</span>';
            return $h;
        }
        // Ogni parte porta il suo indice (--k): l'«Entrata dei testi» le fa arrivare una alla volta
        $k = 0;
        $a = function ( $classe, $dentro ) use ( &$k ) {
            return '<span class="' . $classe . ' olo-pg-a" style="--k:' . ( $k++ ) . '">' . $dentro . '</span>';
        };
        $dur     = ! empty( $o['durata'] ) ? $this->durata_voce( $v ) : '';
        $cat_ico = ! empty( $v['icona'] ) ? $this->render_icon_html( (string) $v['icona'], .55, 'class="olo-pg-cat-ico" aria-hidden="true"' ) : '';
        $cat_h   = ( $o['categoria'] && '' !== $cat ) ? $cat_ico . esc_html( $cat ) : '';
        $dur_h   = '' !== $dur ? ( ! empty( $o['durata_et'] ) ? '<svg viewBox="0 0 10 10" width="7" height="7" aria-hidden="true"><path d="M2 1v8l7-4z" fill="currentColor"/></svg>' : '' ) . esc_html( $dur ) : '';
        $nota_h  = '';
        if ( '' !== $nota ) {
            // «Grande»: la prima parola grande e il resto piccolo accanto («€3.40 330 ml»)
            // Una parola sola (il prezzo di un prodotto, «€14,00»): grande tutta. Prima restava piccola, e «Grande» non faceva
            // niente proprio sui prezzi che arrivano dal negozio (23 nov 2026).
            if ( 'grande' === $o['nota_stile'] && preg_match( '/^(\S+)\s+(.+)$/u', $nota, $mm ) ) {
                $nota_h = '<b>' . esc_html( $mm[1] ) . '</b><small>' . esc_html( $mm[2] ) . '</small>';
            } elseif ( 'grande' === $o['nota_stile'] ) {
                $nota_h = '<b>' . esc_html( $nota ) . '</b>';
            } else {
                $nota_h = esc_html( $nota );
            }
        }
        $nota_cl = 'olo-pg-sk-nota' . ( '' !== $o['nota_stile'] ? ' olo-pg-nota-' . $o['nota_stile'] : '' );
        $camp    = ! empty( $o['campione'] ) ? '<span class="olo-pg-sk-campione olo-pg-a" style="--k:0" aria-hidden="true"></span>' : '';
        // Il riquadro solo sulle foto grandi; sulle piccole niente sottotitolo (e niente piede, col riquadro)
        $riq     = 'riquadro' === $o['modo'] && ( empty( $o['riq_grandi'] ) || $grande );
        $piccola = ( ! empty( $o['sot_grandi'] ) || ! empty( $o['riq_grandi'] ) ) && ! $grande;
        if ( $piccola ) {
            $sot = '';
        }
        $alto = '';
        if ( ! empty( $o['num_grande'] ) && $o['numero'] && in_array( $o['modo'], [ 'sopra', 'riquadro' ], true ) ) {
            // Numero grande in alto a sinistra, e sotto categoria · durata (come le pareti di video)
            $meta  = ( '' !== $cat_h ? '<span class="olo-pg-sk-cat">' . $cat_h . '</span>' : '' )
                . ( '' !== $dur_h ? '<span class="olo-pg-sk-dur' . ( ! empty( $o['durata_et'] ) ? ' olo-pg-dur-et' : '' ) . '">' . $dur_h . '</span>' : '' );
            $alto .= $a( 'olo-pg-sk-num', $num ) . ( '' !== $meta ? $a( 'olo-pg-sk-meta', $meta ) : '' );
        } else {
            if ( '' !== $cat_h && 'biglietto' !== $o['modo'] ) {
                $alto .= $a( 'olo-pg-sk-cat', $cat_h );
            }
            if ( '' !== $dur_h ) {
                $alto .= $a( 'olo-pg-sk-dur' . ( ! empty( $o['durata_et'] ) ? ' olo-pg-dur-et' : '' ), $dur_h );
            }
            if ( $o['numero'] ) {
                $alto .= $a( 'olo-pg-sk-num', $num );
            }
            $alto .= $camp;
        }
        // Biglietto: il testo diventa la scritta grande (il primo pezzo grande, gli altri in colonna accanto)
        $grande_bg = '';
        if ( 'biglietto' === $o['modo'] && $o['testo'] && '' !== $txt ) {
            if ( preg_match( '/^(\S+)\s+(.+)$/u', $txt, $mm ) ) {
                $righe = '';
                foreach ( preg_split( '/\s+/u', trim( $mm[2] ) ) as $w ) {
                    $righe .= '<span>' . esc_html( $w ) . '</span>';
                }
                $grande_bg = esc_html( $mm[1] ) . '<small>' . $righe . '</small>';
            } else {
                $grande_bg = esc_html( $txt );
            }
            $grande_bg = '<div class="olo-pg-bg-grande olo-pg-a" style="--k:' . ( $k++ ) . '">' . $grande_bg . '</div>';
        }
        $testi = '';
        if ( $o['titolo'] && '' !== $tit0 ) {
            $testi .= $a( 'olo-pg-sk-tit', $tit );
        }
        if ( $o['sottotitolo'] && '' !== $sot ) {
            $testi .= $a( 'olo-pg-sk-sot', esc_html( $sot ) );
        }
        if ( 'biglietto' !== $o['modo'] && $o['testo'] && '' !== $txt ) {
            $testi .= $a( 'olo-pg-sk-txt', esc_html( $txt ) );
        }
        if ( '' !== $nota_h && empty( $o['zone'] ) ) {
            $testi .= $a( $nota_cl, $nota_h );
        }
        $btn = '' !== $o['pulsante'] ? $a( 'olo-pg-sk-btn', esc_html( $o['pulsante'] ) ) : '';
        $ico = $this->qv_id ? $this->qv_icona( $o['icona'], $tit0 ) : $this->scheda_icona( $o['icona'] );
        if ( 'biglietto' === $o['modo'] ) {
            // La categoria va sulla foto, in un'etichetta; sul biglietto la data grande, i testi e il piede
            if ( '' !== $cat_h ) {
                $this->sk_foto = '<div class="olo-pg-fz olo-pg-fz-as"><span class="olo-pg-sk-cat olo-pg-a" style="--k:0">' . $cat_h . '</span></div>';
            }
            return '<div class="olo-pg-sk olo-pg-bg">' . ( '' !== $alto ? '<div class="olo-pg-bg-alto">' . $alto . '</div>' : '' ) . $grande_bg
                . ( '' !== $testi ? '<div class="olo-pg-sk-testi">' . $testi . '</div>' : '' )
                . ( '' !== $btn . $ico ? '<div class="olo-pg-bg-piede">' . $btn . $ico . '</div>' : '' ) . '</div>';
        }
        if ( ! empty( $o['zone'] ) && in_array( $o['modo'], [ 'sopra', 'riquadro', 'sotto' ], true ) ) {
            // Posizione libera: ogni parte nella sua zona, le altre dove le mette lo schema
            $k     = 0;
            $sotto = 'sotto' === $o['modo'];
            $dove  = array_merge( [ 'cat' => 'as', 'num' => 'ad', 'tit' => 'bs', 'sot' => 'bs', 'txt' => 'bs', 'btn' => 'bs', 'ico' => 'bd', 'nota' => 'bs' ], $o['zone'] );
            if ( ! isset( $o['zone']['dur'] ) ) {
                $dove['dur'] = $dove['cat'];
            }
            foreach ( $dove as $p => $z ) {
                // «Sulla foto» ha senso solo coi testi sotto la foto: sulla foto è già l'alto o il basso
                if ( ! $sotto && 'f' === $z[0] ) {
                    $dove[ $p ] = substr( $z, 1 );
                }
            }
            if ( $riq && ! empty( $o['riq_grandi'] ) && 'dt' === $dove['cat'] ) {
                // Nelle foto grandi col riquadro la categoria sale in alto a destra, in un'etichetta
                $dove['cat'] = 'ad';
            }
            $T     = $dove['tit'];
            $parti = [];
            $leg   = [ 'pt' => [], 'tt' => [], 'dt' => [] ];
            $metti = function ( $parte, $html ) use ( &$parti, &$leg, $dove ) {
                $z = $dove[ $parte ];
                if ( isset( $leg[ $z ] ) ) {
                    $leg[ $z ][] = $html;
                } else {
                    $parti[ $z ][] = $html;
                }
            };
            $con_box = ! empty( $o['cornice_meta'] ) && $o['numero'] && '' !== $cat_h && $dove['num'] === $dove['cat'];
            if ( $con_box ) {
                // Numero e categoria nello stesso riquadro (le schede della squadra)
                $metti( 'num', $a( 'olo-pg-sk-box', '<span class="olo-pg-sk-num">' . $num . '</span><span class="olo-pg-sk-cat">' . $cat_h . '</span>' ) );
            } else {
                if ( $o['numero'] ) {
                    $metti( 'num', $a( 'olo-pg-sk-num', $num ) );
                }
                if ( '' !== $cat_h ) {
                    $metti( 'cat', $a( 'olo-pg-sk-cat', $cat_h ) );
                }
            }
            if ( '' !== $dur_h ) {
                $metti( 'dur', $a( 'olo-pg-sk-dur' . ( ! empty( $o['durata_et'] ) ? ' olo-pg-dur-et' : '' ), $dur_h ) );
            }
            if ( '' !== $nota_h && $dove['nota'] !== $T ) {
                $metti( 'nota', $a( $nota_cl, $nota_h ) );
            }
            if ( '' !== $camp ) {
                $parti['ad'][] = $camp;
            }
            // Le parti legate al titolo si scrivono quando si conosce il resto: qui solo il posto
            $parti[ $T ][] = '%TITOLO%';
            $sot_h = ( $o['sottotitolo'] && '' !== $sot ) ? $a( 'olo-pg-sk-sot', esc_html( $sot ) ) : '';
            if ( '' !== $sot_h && $dove['sot'] !== $T ) {
                $metti( 'sot', $sot_h );
                $sot_h = '';
            }
            if ( $o['testo'] && '' !== $txt ) {
                $metti( 'txt', $a( 'olo-pg-sk-txt' . ( ! empty( $o['punto_testo'] ) ? ' olo-pg-punto' : '' ), esc_html( $txt ) ) );
            }
            if ( '' !== $nota_h && $dove['nota'] === $T ) {
                $metti( 'nota', $a( $nota_cl, $nota_h ) );
            }
            if ( '' !== $o['pulsante'] ) {
                $metti( 'btn', $a( 'olo-pg-sk-btn', esc_html( $o['pulsante'] ) ) );
            }
            if ( '' !== $ico ) {
                $metti( 'ico', $ico );
            }
            // Il titolo, con le parti che gli stanno davanti, dopo (nella stessa riga) e sotto
            $tit_h = '';
            if ( $o['titolo'] && '' !== $tit0 ) {
                $tit_h = '<span class="olo-pg-sk-tit olo-pg-a" style="--k:' . ( $k++ ) . '">' . $tit
                    . ( $leg['tt'] ? '<span class="olo-pg-sk-dopo">' . implode( '', $leg['tt'] ) . '</span>' : '' ) . '</span>';
            }
            $gruppo = $tit_h . implode( '', $leg['dt'] );
            if ( $leg['pt'] ) {
                $gruppo = '<div class="olo-pg-sk-tr">' . implode( '', $leg['pt'] ) . '<div class="olo-pg-sk-tcol">' . $gruppo . $sot_h . '</div></div>';
                $sot_h  = '';
            }
            $gruppo .= $sot_h;
            foreach ( $parti[ $T ] as $ii => $x ) {
                if ( '%TITOLO%' === $x ) {
                    $parti[ $T ][ $ii ] = $gruppo;
                }
            }
            if ( ! $riq && ! empty( $o['riq_grandi'] ) ) {
                // Le foto piccole, col riquadro solo sulle grandi, non hanno il piede
                unset( $parti['ps'], $parti['pd'] );
            }
            $riga = function ( $r, $cols ) use ( $parti ) {
                $dentro = '';
                foreach ( $cols as $c ) {
                    if ( ! empty( $parti[ $r . $c ] ) ) {
                        $x = implode( '', $parti[ $r . $c ] );
                        if ( '' !== $x ) {
                            $dentro .= '<div class="olo-pg-z olo-pg-z-' . $c . '">' . $x . '</div>';
                        }
                    }
                }
                return $dentro;
            };
            $h  = '<div class="olo-pg-zr olo-pg-zr-a">' . $riga( 'a', [ 's', 'c', 'd' ] ) . '</div>';
            $h .= '<div class="olo-pg-zr olo-pg-zr-c">' . $riga( 'c', [ 's', 'c', 'd' ] ) . '</div>';
            $pie = $riga( 'p', [ 's', 'd' ] );
            $h  .= '<div class="olo-pg-zb"><div class="olo-pg-zr olo-pg-zr-b">' . $riga( 'b', [ 's', 'c', 'd' ] ) . '</div>'
                . ( '' !== $pie ? '<div class="olo-pg-zr olo-pg-zr-p">' . $pie . '</div>' : '' ) . '</div>';
            // Sulla foto (coi testi sotto la foto): le parti nei quattro angoli
            foreach ( [ 'fas', 'fad', 'fbs', 'fbd' ] as $z ) {
                if ( ! empty( $parti[ $z ] ) ) {
                    $this->sk_foto .= '<div class="olo-pg-fz olo-pg-fz-' . substr( $z, 1 ) . '">' . implode( '', $parti[ $z ] ) . '</div>';
                }
            }
            return '<div class="olo-pg-sk olo-pg-sk-zone' . ( $riq ? ' olo-pg-sk-riq' : '' ) . '">' . $h . '</div>';
        }
        $testi .= $btn;
        $h      = '' !== $alto ? '<div class="olo-pg-sk-alto' . ( ! empty( $o['num_grande'] ) ? ' olo-pg-sk-alto-num' : '' ) . '">' . $alto . '</div>' : '';
        if ( '' !== $testi || '' !== $ico ) {
            $h .= '<div class="olo-pg-sk-basso"><div class="olo-pg-sk-testi">' . $testi . '</div>' . $ico . '</div>';
        }
        return '' === $h ? '' : '<div class="olo-pg-sk' . ( $riq ? ' olo-pg-sk-riq' : '' ) . '">' . $h . '</div>';
    }

    /**
     * Il CSS dei testi sulle foto e dell'elenco a righe.
     *
     * @param string $uid      classe della galleria
     * @param array  $o        scheda_opzioni()
     * @param array  $s        impostazioni
     * @param string $radius   raggio delle foto
     * @param string $item_box altezza o proporzione delle foto nella griglia
     * @param string $layout   schema
     * @return string
     */
    private function schede_css( $uid, $o, $s, $radius, $item_box, $layout ) {
        if ( '' === $o['modo'] ) {
            return '';
        }
        $col    = $this->safe_color_css( $s['scheda_colore'] ?? '' );
        $acc    = $this->safe_color_css( $s['scheda_accento'] ?? '' );
        $sfondo = $this->safe_color_css( $s['scheda_sfondo'] ?? '' );
        $lati   = Olobuild_Tile_Utils::spacing_sides( $s['scheda_padding'] ?? 18, [], [ 18, 18, 18, 18 ] );
        $pad    = Olobuild_Tile_Utils::sides_css( $lati );
        $velo   = max( 0, min( 100, absint( $s['scheda_velo'] ?? 55 ) ) ) / 100;
        // Velo in alto: vuoto = com'era (metà del velo, sfumato sul primo quarto della foto)
        $velo_a = isset( $s['scheda_velo_alto'] ) && '' !== $s['scheda_velo_alto'] && is_numeric( $s['scheda_velo_alto'] )
            ? max( 0, min( 100, absint( $s['scheda_velo_alto'] ) ) ) / 100 : null;
        $size   = absint( $s['scheda_titolo_size'] ?? 0 );
        $peso   = preg_match( '/^[1-9]00$/', (string) ( $s['scheda_peso'] ?? '' ) ) ? $s['scheda_peso'] : '';
        $font   = $this->resolve_font_family( $s['scheda_font'] ?? '' );
        $tit    = ( $size ? 'font-size:' . min( 72, max( 12, $size ) ) . 'px;' : '' ) . ( $peso ? 'font-weight:' . $peso . ';' : '' ) . ( $font ? 'font-family:' . $font . ';' : '' );
        $u      = '.' . $uid;
        $css    = '';
        if ( 'elenco' === $o['modo'] ) {
            $line  = 'color-mix(in srgb,currentColor 14%,transparent)';
            $cols  = $o['miniatura'] ? '3.2em 96px minmax(0,1.5fr) minmax(0,1fr) minmax(0,.8fr) auto' : '3.2em minmax(0,1.5fr) minmax(0,1fr) minmax(0,.8fr) auto';
            $aree  = $o['miniatura'] ? '"num img tit sot cat txt"' : '"num tit sot cat txt"';
            // In cima una riga piena, fra le righe linee sottili (gli indici dei progetti)
            $css  .= "{$u}{display:flex;flex-direction:column;border-top:1px solid currentColor}";
            $css  .= "{$u} .olo-pg-item{display:grid;grid-template-columns:{$cols};grid-template-areas:{$aree};align-items:center;gap:0 1.5rem;padding:{$pad};border-bottom:1px solid {$line};color:inherit;text-decoration:none;position:relative;transition:background-color .25s ease}";
            $css  .= "{$u} .olo-pg-item:hover,{$u} .olo-pg-item:focus-visible{background:color-mix(in srgb,currentColor 5%,transparent)}";
            $css  .= "{$u} .olo-pg-item:hover,{$u} .olo-pg-item:focus{text-decoration:none;color:inherit}";
            $css  .= "{$u} .olo-pg-item:focus-visible{outline:2px solid var(--olo-color-primary);outline-offset:2px}";
            $css  .= $o['miniatura']
                ? "{$u} .olo-pg-item img,{$u} .olo-pg-item video,{$u} .olo-pg-item .olo-pg-no-poster{grid-area:img;width:96px;height:64px;object-fit:cover;border-radius:{$radius};display:block}"
                : "{$u} .olo-pg-item img,{$u} .olo-pg-item video,{$u} .olo-pg-item .olo-pg-no-poster,{$u} .olo-pg-item .olo-pg-play{display:none}";
            $css  .= "{$u} .olo-pg-el-num{grid-area:num;font-size:.78em;font-variant-numeric:tabular-nums;opacity:.6" . ( $acc ? ";color:{$acc};opacity:1" : '' ) . "}";
            $css  .= "{$u} .olo-pg-el-tit{grid-area:tit;font-size:clamp(18px,2vw,26px);line-height:1.2;{$tit}" . ( $col ? "color:{$col};" : '' ) . "}";
            $css  .= "{$u} .olo-pg-el-sot{grid-area:sot;font-size:.85em;opacity:.7}";
            $css  .= "{$u} .olo-pg-el-cat{grid-area:cat;font-size:.72em;letter-spacing:.12em;text-transform:uppercase;opacity:.75" . ( $acc ? ";color:{$acc};opacity:1" : '' ) . "}";
            $css  .= "{$u} .olo-pg-el-txt{grid-area:txt;font-size:.85em;font-variant-numeric:tabular-nums;opacity:.7;text-align:right}";
            $css  .= "{$u} .olo-pg-item:hover .olo-pg-el-tit{text-decoration:underline;text-underline-offset:.2em;text-decoration-thickness:1px}";
            $mob   = $o['miniatura'] ? '2.4em 64px minmax(0,1fr)' : '2.4em minmax(0,1fr)';
            $aree_m = $o['miniatura'] ? '"num img tit" "num img sot" "num img cat"' : '"num tit" "num sot" "num cat"';
            $css  .= "@media(max-width:640px){{$u} .olo-pg-item{grid-template-columns:{$mob};grid-template-areas:{$aree_m};gap:.15rem .9rem}{$u} .olo-pg-item img,{$u} .olo-pg-item video{width:64px;height:64px}{$u} .olo-pg-el-txt{display:none}{$u} .olo-pg-el-tit{font-size:18px}}";
            return $css;
        }
        // Sulla foto: i testi stanno su un velo che scurisce il basso (e l'alto, se c'è la categoria)
        $bianco = '#fff';
        $css   .= "{$u} .olo-pg-sk{position:absolute;inset:0;z-index:3;display:flex;flex-direction:column;justify-content:space-between;gap:.6rem;padding:{$pad};color:" . ( $col ?: $bianco ) . ";pointer-events:none;text-align:left}";
        $css   .= "{$u} .olo-pg-sk-alto{display:flex;justify-content:space-between;align-items:flex-start;gap:.5rem}";
        $css   .= "{$u} .olo-pg-sk-basso{display:flex;justify-content:space-between;align-items:flex-end;gap:.75rem;margin-top:auto}";
        $css   .= "{$u} .olo-pg-sk-testi{display:flex;flex-direction:column;gap:.25rem;min-width:0}";
        // Colori e stili delle singole parti (sottotitolo, testo, categoria, numero)
        $c_sot = $this->safe_color_css( $s['scheda_colore_sottotitolo'] ?? '' );
        $c_txt = $this->safe_color_css( $s['scheda_colore_testo'] ?? '' );
        $pieno = 'var(--pg-c,' . ( $acc ? $acc : 'var(--olo-color-primary)' ) . ')';
        if ( $c_sot ) {
            $css .= "{$u} .olo-pg-sk-sot{color:{$c_sot};opacity:1}";
        }
        if ( $c_txt ) {
            $css .= "{$u} .olo-pg-sk-txt{color:{$c_txt};opacity:1}";
        }
        if ( ! empty( $s['scheda_sottotitolo_maiuscolo'] ) ) {
            $css .= "{$u} .olo-pg-sk-sot{font-size:10px;font-weight:600;letter-spacing:.14em;text-transform:uppercase;line-height:1.4}";
        }
        $st_cat = (string) ( $s['scheda_stile_categoria'] ?? '' );
        if ( 'testo' === $st_cat ) {
            $css .= "{$u} .olo-pg-sk .olo-pg-sk-cat{border:0;padding:0;border-radius:0}";
        } elseif ( 'pieno' === $st_cat ) {
            $css .= "{$u} .olo-pg-sk .olo-pg-sk-cat{border:0;padding:.45em .6em;border-radius:.2rem;background:{$pieno};color:var(--pg-cf,#fff)}";
        }
        if ( 'pieno' === ( $s['scheda_stile_numero'] ?? '' ) ) {
            $css .= "{$u} .olo-pg-sk .olo-pg-sk-num{background:{$pieno};color:var(--pg-cf,#fff);padding:.4em .5em;border-radius:.2rem;line-height:1;font-size:10px;font-weight:700;letter-spacing:.08em;opacity:1}";
        }
        // Durata dei video: testo piccolo accanto alla categoria
        $css   .= "{$u} .olo-pg-sk-dur{font-size:10px;font-weight:600;letter-spacing:.14em;text-transform:uppercase;line-height:1;opacity:.85;font-variant-numeric:tabular-nums;align-self:center}";
        if ( ! empty( $o['num_grande'] ) ) {
            $nc   = $acc ? "var(--pg-c,{$acc})" : 'var(--pg-c,currentColor)';
            $css .= "{$u} .olo-pg-sk-alto-num{flex-direction:column;align-items:flex-start;gap:.45rem}";
            $css .= "{$u} .olo-pg-sk .olo-pg-sk-num{font-family:" . $this->resolve_font_family( 'heading' ) . ";font-size:clamp(26px,2.6vw,40px);font-weight:700;line-height:.95;letter-spacing:-.01em;margin-left:0;opacity:1;color:{$nc}}";
            $css .= "{$u} .olo-pg-sk-meta{display:flex;flex-wrap:wrap;align-items:center;gap:.25rem .5rem}";
            $css .= "{$u} .olo-pg-sk-meta .olo-pg-sk-cat{border:0;padding:0;color:inherit}";
            $sep  = [ 'barra' => '/', 'trattino' => '—', 'riga' => '|' ][ $s['scheda_separatore'] ?? '' ] ?? '·';
            $css .= "{$u} .olo-pg-sk-meta .olo-pg-sk-cat+.olo-pg-sk-dur::before{content:'{$sep}';margin-right:.5rem;opacity:.7}";
        }
        // Titoli lunghi (i video di un canale): al massimo tre righe, coi puntini, senza spingere fuori la data
        $css   .= "{$u} .olo-pg-sk-tit{font-size:clamp(16px,1.6vw,24px);font-weight:600;line-height:1.15;letter-spacing:-.01em;display:-webkit-box;-webkit-box-orient:vertical;-webkit-line-clamp:3;overflow:hidden;{$tit}}";
        $css   .= "{$u} .olo-pg-sk-sot{font-size:13px;line-height:1.35;opacity:.85}";
        $css   .= "{$u} .olo-pg-sk-txt{font-size:12px;line-height:1.45;opacity:.75}";
        $css   .= "{$u} .olo-pg-sk-cat{font-size:10px;font-weight:600;letter-spacing:.14em;text-transform:uppercase;line-height:1;padding:.45em .6em;border:1px solid currentColor;border-radius:.2rem;color:var(--pg-c," . ( $acc ? $acc : 'currentColor' ) . ")}";
        $css   .= "{$u} .olo-pg-sk-num{font-size:12px;font-variant-numeric:tabular-nums;letter-spacing:.06em;opacity:.85;margin-left:auto" . ( $acc ? ";color:var(--pg-c,{$acc});opacity:1" : ';color:var(--pg-c,currentColor)' ) . "}";
        $css   .= "{$u} .olo-pg-sk-ico{flex:0 0 auto;width:32px;height:32px;border-radius:50%;border:1.5px solid currentColor;display:flex;align-items:center;justify-content:center;transition:transform .35s ease,background-color .35s ease}";
        $css   .= "{$u} .olo-pg-item:hover .olo-pg-ico-piu{transform:rotate(90deg)}{$u} .olo-pg-item:hover .olo-pg-sk-ico:not(.olo-pg-ico-piu){transform:translateX(3px)}";
        $css   .= "{$u} .olo-pg-sk-btn{align-self:flex-start;margin-top:.5rem;font-size:12px;font-weight:600;letter-spacing:.04em;line-height:1;padding:.7em 1.2em;border-radius:100vmax;background:var(--pg-c," . ( $acc ?: 'var(--olo-color-primary)' ) . ");color:var(--pg-cf,#fff);transition:transform .3s ease}";
        $css   .= "{$u} .olo-pg-item:hover .olo-pg-sk-btn,{$u} .olo-pg-item:focus-visible .olo-pg-sk-btn{transform:translateY(-2px)}";
        // Coi testi sulla foto il «play» dei video va nell'angolo in basso a destra: al centro copriva i titoli
        $css   .= "{$u} .olo-pg-video .olo-pg-play{inset:auto .9rem .9rem auto}{$u} .olo-pg-video .olo-pg-play::before{width:34px;height:34px;background-size:15px 15px}";
        if ( ! empty( $o['zone'] ) && in_array( $o['modo'], [ 'sopra', 'riquadro', 'sotto' ], true ) ) {
            $css .= "{$u} .olo-pg-sk-zone{justify-content:space-between}";
            $css .= "{$u} .olo-pg-zr{display:flex;gap:.75rem;min-width:0}{$u} .olo-pg-zr-a{align-items:flex-start}{$u} .olo-pg-zr-c{flex:1 1 auto;align-items:center}{$u} .olo-pg-zr-b{align-items:flex-end}";
            $css .= "{$u} .olo-pg-z{display:flex;flex-direction:column;gap:.25rem;min-width:0}{$u} .olo-pg-z-s{margin-right:auto;align-items:flex-start}";
            $css .= "{$u} .olo-pg-z-c{margin:0 auto;align-items:center;text-align:center}{$u} .olo-pg-z-d{margin-left:auto;align-items:flex-end;text-align:right;flex:0 0 auto;max-width:60%}";
            $css .= "{$u} .olo-pg-z .olo-pg-sk-num{margin-left:0}{$u} .olo-pg-z .olo-pg-sk-btn{align-self:auto}";
            $css .= "{$u} .olo-pg-zr-a .olo-pg-z,{$u} .olo-pg-zr-p .olo-pg-z{flex-direction:row;flex-wrap:wrap;align-items:center;gap:.5rem}";
            // Il basso (titoli e piede) sta in fondo; il piede è una riga sola, a sinistra e a destra
            $css .= "{$u} .olo-pg-zb{display:flex;flex-direction:column;gap:.6rem;min-width:0;margin-top:auto}";
            $css .= "{$u} .olo-pg-zb:not(:has(.olo-pg-z)){display:none}";
            $css .= "{$u} .olo-pg-zr-p{align-items:center;justify-content:space-between}";
            if ( ! empty( $s['scheda_linea_piede'] ) ) {
                $css .= "{$u} .olo-pg-zr-p{border-top:1px solid color-mix(in srgb,currentColor 22%,transparent);padding-top:.7rem}";
            }
            if ( 'sotto' === $o['modo'] ) {
                $css .= "{$u} .olo-pg-sk-zone .olo-pg-zr-c{flex:0 0 auto}{$u} .olo-pg-sk-zone .olo-pg-zr:empty{display:none}{$u} .olo-pg-zb{margin-top:0}";
            }
        }
        if ( 'biglietto' === $o['modo'] ) {
            // Accanto alla foto, un biglietto: la foto a sinistra, la linea tratteggiata con le tacche, i testi a destra
            $tinta = $acc ? $acc : 'var(--olo-color-primary)';
            $tb    = ! empty( $s['scheda_sfondo_voce'] ) ? "var(--pg-c,{$tinta})" : ( $sfondo ? $sfondo : "color-mix(in srgb,var(--pg-c,{$tinta}) 10%,var(--olo-color-background,#fff))" );
            $nume  = "var(--pg-c,{$tinta})";
            // Il bordo del biglietto perforato: una fila di tacche lungo lo strappo, e accanto la linea tratteggiata
            $tacca = 'radial-gradient(circle .32rem at 0 50%,transparent 94%,black) 0 0/100% .8rem repeat-y';
            $tratt = 'repeating-linear-gradient(to bottom,color-mix(in srgb,currentColor 32%,transparent) 0 .25rem,transparent .25rem .5rem) .7rem 0/1px 100% no-repeat';
            $voce  = ! empty( $s['scheda_sfondo_voce'] );
            $riga  = $acc ? $acc : 'color-mix(in srgb,currentColor 40%,transparent)';
            $css  .= "{$u} .olo-pg-item:not(.olo-pg-testo){display:grid!important;grid-template-columns:minmax(0,1.1fr) minmax(0,1fr);color:" . ( $col ?: 'var(--olo-color-text,inherit)' ) . ';text-decoration:none' . ( 'masonry' === $layout ? ';min-height:15rem' : '' ) . '}';
            $css  .= "{$u} .olo-pg-item:hover,{$u} .olo-pg-item:focus{text-decoration:none}";
            $css  .= "{$u} .olo-pg-media{position:relative;overflow:hidden;border-radius:{$radius} 0 0 {$radius};min-height:100%}";
            $css  .= "{$u} .olo-pg-media img,{$u} .olo-pg-media video,{$u} .olo-pg-media .olo-pg-no-poster{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;display:block}";
            $css  .= "{$u} .olo-pg-sk.olo-pg-bg{position:relative;inset:auto;z-index:1;display:flex;flex-direction:column;justify-content:flex-start;gap:.4rem;padding:{$pad};padding-left:1.5rem;background:{$tratt},{$tb};color:inherit;border-radius:0 {$radius} {$radius} 0;pointer-events:auto;-webkit-mask:{$tacca};mask:{$tacca}}";
            $css  .= "{$u} .olo-pg-bg-alto{display:flex;justify-content:space-between;align-items:center;gap:.5rem}";
            $css  .= "{$u} .olo-pg-bg .olo-pg-sk-cat{border:0;padding:0;color:{$nume}}";
            $css  .= "{$u} .olo-pg-bg .olo-pg-sk-num{color:inherit;opacity:.6}";
            // La data grande, e accanto, oltre una lineetta, mese e giorno uno sotto l'altro
            $css  .= "{$u} .olo-pg-bg-grande{display:flex;align-items:center;gap:.6rem;margin:.15rem 0 .1rem;font-family:" . $this->resolve_font_family( 'heading' ) . ";font-size:clamp(30px,3.2vw,46px);font-weight:500;line-height:.9;color:" . ( $voce ? 'inherit' : $nume ) . ";font-variant-numeric:tabular-nums}";
            $css  .= "{$u} .olo-pg-bg-grande small{display:flex;flex-direction:column;gap:.2em;padding-left:.6rem;border-left:1.5px solid {$riga};font-family:" . $this->resolve_font_family( 'mono' ) . ";font-size:10px;font-weight:500;letter-spacing:.1em;text-transform:uppercase;line-height:1.15;color:inherit;opacity:.75}";
            $css  .= "{$u} .olo-pg-bg .olo-pg-sk-tit{font-size:clamp(15px,1.35vw,19px);font-weight:600;line-height:1.2;{$tit}}";
            $css  .= "{$u} .olo-pg-bg .olo-pg-sk-sot{font-size:12px;opacity:.75}";
            $css  .= "{$u} .olo-pg-bg-piede{display:flex;justify-content:flex-start;align-items:center;gap:.55rem;margin-top:auto;padding-top:.4rem}";
            $css  .= "{$u} .olo-pg-bg .olo-pg-sk-btn{margin:0;padding:0;background:none;color:inherit;border-radius:0;font-family:" . $this->resolve_font_family( 'mono' ) . ";font-size:10px;font-weight:600;letter-spacing:.14em;text-transform:uppercase}";
            $css  .= "{$u} .olo-pg-bg .olo-pg-sk-ico{width:26px;height:26px;border-color:color-mix(in srgb,currentColor 35%,transparent)}";
            return $css;
        }
        if ( 'sotto' === $o['modo'] ) {
            // Sotto la foto: la foto tiene l'altezza della griglia, i testi le stanno sotto nel colore della pagina
            $css .= "{$u} .olo-pg-item{height:auto!important;aspect-ratio:auto!important;overflow:visible!important;display:flex;flex-direction:column;color:inherit;text-decoration:none;" . ( $sfondo ? "background:{$sfondo};border-radius:{$radius};" : 'background:transparent;' ) . "}";
            // Le foto sono link (lightbox): i testi non devono prendere colore e sottolineatura dei link del tema
            $css .= "{$u} .olo-pg-item,{$u} .olo-pg-item:hover,{$u} .olo-pg-item:focus,{$u} .olo-pg-item .olo-pg-sk *{text-decoration:none}";
            $css .= "{$u} .olo-pg-media{position:relative;overflow:hidden;border-radius:{$radius};" . ( in_array( $layout, [ 'grid', 'strip' ], true ) ? $item_box . ';' : '' ) . "}";
            $css .= "{$u} .olo-pg-media img,{$u} .olo-pg-media video{width:100%;height:100%;object-fit:cover;display:block}";
            $css .= "{$u} .olo-pg-sk{position:static;padding:" . ( $sfondo ? $pad : '.9rem .1rem .25rem' ) . ";color:" . ( $col ?: 'var(--olo-color-text,inherit)' ) . ";gap:.4rem;justify-content:flex-start;pointer-events:auto}";
            $css .= "{$u} .olo-pg-item:hover .olo-pg-sk-tit{text-decoration:underline;text-underline-offset:.18em;text-decoration-thickness:1px}";
            $css .= "{$u} .olo-pg-sk-alto{order:-1}";
            $css .= "{$u} .olo-pg-sk-cat{border:0;padding:0;color:var(--pg-c," . ( $acc ?: 'var(--olo-color-primary)' ) . ")}";
            $css .= "{$u} .olo-pg-sk-tit{font-size:clamp(18px,1.8vw,26px);{$tit}}";
            $css .= "{$u} .olo-pg-sk-ico{width:28px;height:28px}";
            return $css;
        }
        $riquadro = 'riquadro' === $o['modo'];
        if ( $riquadro ) {
            // Sulla foto, in un riquadro: titolo, sottotitolo, testo e pulsante in un pannello chiaro in basso
            $pan  = $sfondo ? $sfondo : 'color-mix(in srgb,var(--olo-color-background,#fff) 88%,transparent)';
            $sel  = "{$u} .olo-pg-sk-riq .olo-pg-sk-basso,{$u} .olo-pg-sk-riq .olo-pg-zb";
            $css .= "{$sel}{background:{$pan};-webkit-backdrop-filter:blur(10px);backdrop-filter:blur(10px);color:" . ( $col ? $col : 'var(--olo-color-text,inherit)' ) . ';padding:.85rem 1rem;border-radius:.5rem;box-shadow:0 8px 24px rgb(0 0 0 / .18)}';
            $css .= "{$u} .olo-pg-sk-riq .olo-pg-sk-sot,{$u} .olo-pg-sk-riq .olo-pg-sk-txt{opacity:.75}";
            if ( ! empty( $s['scheda_riquadro_stretto'] ) ) {
                // Il riquadro piccolo in un angolo, largo quanto i suoi testi (i campionari)
                $css .= "{$u} .olo-pg-sk-riq .olo-pg-sk-basso,{$u} .olo-pg-sk-riq .olo-pg-zb{align-self:flex-start;max-width:min(100%,20rem);padding:.6rem .8rem;border-radius:.125rem;box-shadow:none}";
            }
        }
        if ( null !== $velo_a ) {
            // Il velo in alto segue i testi che ci stanno (numero grande, categoria, nota): copre la loro riga fino al
            // bordo della foto e sfuma 2,5rem più sotto, qualunque sia l'altezza della riga. Senza, su una foto chiara in
            // alto le scritte piccole sparivano (il velo storico copre solo il primo quarto, a metà intensità).
            if ( $velo > 0 && ! $riquadro ) {
                $css .= "{$u} .olo-pg-sk::before{content:'';position:absolute;inset:0;z-index:-1;background:linear-gradient(to top,rgb(0 0 0 / " . round( $velo, 2 ) . ') 0%,rgb(0 0 0 / ' . round( $velo * .45, 2 ) . ') 34%,transparent 62%)}';
            }
            if ( $velo_a > 0 ) {
                $va   = round( $velo_a, 2 );
                $css .= "{$u} .olo-pg-sk-alto,{$u} .olo-pg-zr-a{position:relative}";
                $css .= "{$u} .olo-pg-sk-alto::before,{$u} .olo-pg-zr-a:not(:empty)::before{content:'';position:absolute;z-index:-1;pointer-events:none;top:-" . intval( $lati['top'] ) . 'px;left:-' . intval( $lati['left'] ) . 'px;right:-' . intval( $lati['right'] ) . "px;bottom:-2.5rem;background:linear-gradient(to top,transparent 0,rgb(0 0 0 / {$va}) 2.5rem)}";
            }
        } elseif ( $velo > 0 ) {
            $v1 = round( $velo, 2 );
            $v2 = round( $velo * .45, 2 );
            $v3 = round( $velo * .5, 2 );
            $css .= $riquadro
                ? "{$u} .olo-pg-sk::before{content:'';position:absolute;inset:0;z-index:-1;background:linear-gradient(to bottom,rgb(0 0 0 / {$v3}) 0%,transparent 26%)}"
                : "{$u} .olo-pg-sk::before{content:'';position:absolute;inset:0;z-index:-1;background:linear-gradient(to top,rgb(0 0 0 / {$v1}) 0%,rgb(0 0 0 / {$v2}) 34%,transparent 62%),linear-gradient(to bottom,rgb(0 0 0 / {$v3}) 0%,transparent 26%)}";
        }
        if ( $o['passaggio'] ) {
            $css .= "{$u} .olo-pg-sk{opacity:0;transition:opacity .4s ease}";
            $css .= "{$u} .olo-pg-sk-basso{transform:translateY(10px);transition:transform .45s cubic-bezier(.2,.8,.2,1)}";
            $css .= "{$u} .olo-pg-item:hover .olo-pg-sk,{$u} .olo-pg-item:focus-visible .olo-pg-sk{opacity:1}";
            $css .= "{$u} .olo-pg-item:hover .olo-pg-sk-basso,{$u} .olo-pg-item:focus-visible .olo-pg-sk-basso{transform:none}";
            // Sui telefoni non c'è il passaggio del mouse: i testi restano visibili
            $css .= "@media(hover:none){{$u} .olo-pg-sk{opacity:1}{$u} .olo-pg-sk-basso{transform:none}}";
        }
        return $css;
    }

    /**
     * LE RIFINITURE DELLE CARD (23 nov 2026), dopo il CSS dello schema così vincono: il carattere di ogni parte, il
     * corsivo, gli stili della categoria, della nota, della durata e del pulsante, le icone, il piede, l'etichetta
     * e le parti sulla foto, la barretta, il quadratino del colore, il fondo nel colore della voce.
     *
     * @param string $uid    classe della galleria
     * @param array  $o      scheda_opzioni()
     * @param array  $s      impostazioni
     * @param string $radius raggio delle foto
     * @param string $layout schema
     * @return string
     */
    private function rifiniture_css( $uid, $o, $s, $radius, $layout ) {
        $u    = '.' . $uid . ' .olo-pg-item';
        $acc  = $this->safe_color_css( $s['scheda_accento'] ?? '' );
        $tinta = 'var(--pg-c,' . ( $acc ? $acc : 'var(--olo-color-primary)' ) . ')';
        $mono = $this->resolve_font_family( 'mono' );
        $css  = '';
        // L'etichetta sulla foto, la barretta nel colore della voce e le parti sulla foto (coi testi sotto)
        $css .= "{$u} .olo-pg-etichetta{position:absolute;z-index:4;font-family:{$mono};font-size:9px;font-weight:600;letter-spacing:.14em;text-transform:uppercase;line-height:1;padding:.65em .8em;background:color-mix(in srgb,var(--olo-color-background,#fff) 92%,transparent);color:var(--olo-color-text,#222);border-radius:.125rem;pointer-events:none}";
        $css .= "{$u} .olo-pg-et-as{top:.85rem;left:.85rem}{$u} .olo-pg-et-ad{top:.85rem;right:.85rem}{$u} .olo-pg-et-bs{bottom:.85rem;left:.85rem}{$u} .olo-pg-et-bd{bottom:.85rem;right:.85rem}";
        $css .= "{$u} .olo-pg-barretta{position:absolute;left:0;right:0;bottom:0;height:.375rem;background:{$tinta};z-index:4;pointer-events:none}";
        $css .= "{$u} .olo-pg-fz{position:absolute;z-index:4;display:flex;align-items:center;gap:.4rem;pointer-events:none;color:#fff}";
        $css .= "{$u} .olo-pg-fz-as{top:.85rem;left:.85rem}{$u} .olo-pg-fz-ad{top:.85rem;right:.85rem}{$u} .olo-pg-fz-bs{bottom:.85rem;left:.85rem}{$u} .olo-pg-fz-bd{bottom:.85rem;right:.85rem}";
        $css .= "{$u} .olo-pg-fz .olo-pg-sk-ico{pointer-events:auto}";
        // Il carattere di ogni parte (Stile › Testi sulle foto): solo le proprietà scelte
        $tt = $this->tipografia_css( $s, 'scheda_titolo', [ 'family' => 'scheda_titolo__f', 'size' => 'scheda_titolo__s', 'weight' => 'scheda_titolo__w', 'color' => 'scheda_titolo__c' ] );
        if ( '' !== $tt ) {
            $css .= "{$u} .olo-pg-sk-tit,{$u} .olo-pg-el-tit,{$u} .olo-pg-tx-tit{{$tt}}";
        }
        foreach ( [
            'scheda_sot'  => [ '.olo-pg-sk-sot,{U} .olo-pg-el-sot', [ 'color' => 'scheda_colore_sottotitolo' ] ],
            'scheda_txt'  => [ '.olo-pg-sk-txt,{U} .olo-pg-el-txt', [ 'color' => 'scheda_colore_testo' ] ],
            'scheda_meta' => [ '.olo-pg-sk-cat,' . ( empty( $o['num_grande'] ) ? '{U} .olo-pg-sk-num,' : '' ) . '{U} .olo-pg-sk-dur,{U} .olo-pg-sk-box,{U} .olo-pg-el-num,{U} .olo-pg-el-cat', [] ],
            'scheda_nota' => [ '.olo-pg-sk-nota', [] ],
        ] as $pre => $x ) {
            $d = $this->tipografia_css( $s, $pre, $x[1] );
            if ( '' !== $d ) {
                $css .= "{$u} " . str_replace( '{U}', $u, $x[0] ) . "{{$d}}";
            }
        }
        // Il corsivo fra asterischi, nel colore scelto
        $cc   = $this->safe_color_css( $s['scheda_corsivo_colore'] ?? '' );
        $css .= "{$u} .olo-pg-em{font-style:italic" . ( $cc ? ";color:{$cc}" : '' ) . '}';
        // Gli stili della categoria: chiaro, scuro (i chip sulle foto) e il pallino nel colore della voce
        $sc  = (string) ( $s['scheda_stile_categoria'] ?? '' );
        // Col riquadro solo sulle grandi, l'etichetta della categoria è delle foto grandi: sulle piccole resta scritta
        $cat = ! empty( $o['riq_grandi'] ) ? "{$u} .olo-pg-sk-riq .olo-pg-sk-cat" : "{$u} .olo-pg-sk-cat";
        if ( ! empty( $o['riq_grandi'] ) && in_array( $sc, [ 'chiaro', 'scuro' ], true ) ) {
            // Sulle foto piccole la categoria resta una scritta
            $css .= "{$u} .olo-pg-sk-zone:not(.olo-pg-sk-riq) .olo-pg-sk-cat{border:0;padding:0;background:none;border-radius:0}";
        }
        if ( 'chiaro' === $sc ) {
            $css .= "{$cat}{display:inline-flex;align-items:center;gap:.45em;border:1px solid color-mix(in srgb,var(--olo-color-text,#222) 12%,transparent);padding:.5em .65em;border-radius:.25rem;background:color-mix(in srgb,var(--olo-color-background,#fff) 94%,transparent);color:var(--olo-color-text,#222)}";
        } elseif ( 'scuro' === $sc ) {
            $css .= "{$cat}{display:inline-flex;align-items:center;gap:.45em;border:1px solid rgb(255 255 255 / .22);padding:.5em .8em;border-radius:100vmax;background:rgb(0 0 0 / .72);color:#fff;-webkit-backdrop-filter:blur(6px);backdrop-filter:blur(6px)}";
        }
        if ( ! empty( $s['scheda_categoria_punto'] ) ) {
            $css .= "{$cat}::before{content:'';flex:0 0 auto;display:inline-block;width:.6em;height:.6em;border-radius:50%;background:{$tinta};margin-right:.5em;vertical-align:.02em}";
            $css .= "{$cat}{display:inline-flex;align-items:center}";
        }
        $css .= "{$u} .olo-pg-cat-ico{display:inline-flex;margin-right:.4em}{$u} .olo-pg-cat-ico svg{width:1.1em;height:1.1em}";
        // Il numero: il totale attenuato, l'icona davanti, il riquadro con la categoria
        $css .= "{$u} .olo-pg-num-tot{opacity:.72}{$u} .olo-pg-num-ico{display:inline-flex;vertical-align:-.15em;margin-right:.45em;color:{$tinta}}{$u} .olo-pg-num-ico svg{width:1.15em;height:1.15em}";
        $css .= "{$u} .olo-pg-sk-box{display:inline-flex;align-items:center;gap:.6em;padding:.3em .55em .3em .3em;border:1px solid color-mix(in srgb,currentColor 28%,transparent);border-radius:.2rem;font-family:{$mono};font-size:10px;letter-spacing:.12em;text-transform:uppercase;line-height:1}";
        $css .= "{$u} .olo-pg-sk-box .olo-pg-sk-num{margin:0;padding:.35em .4em;border-radius:.15rem;background:{$tinta};color:var(--pg-cf,#fff);font-weight:700;opacity:1;font-size:inherit;letter-spacing:.04em}";
        $css .= "{$u} .olo-pg-sk-box .olo-pg-sk-cat{border:0;padding:0;background:none;color:inherit;font-size:inherit;letter-spacing:inherit}";
        // Il separatore fra le parti piccole nella stessa zona («Residential · 2026», «ART DIRECTION / 2026»)
        $sep = [ 'punto' => '·', 'barra' => '/', 'trattino' => '—', 'riga' => '|' ][ $s['scheda_separatore'] ?? '' ] ?? '';
        if ( '' !== $sep ) {
            $css .= "{$u} .olo-pg-zr-a .olo-pg-z>.olo-pg-a:not(:last-child):not(.olo-pg-sk-ico):not(.olo-pg-sk-campione):not(:has(+.olo-pg-sk-ico))::after,{$u} .olo-pg-zr-p .olo-pg-z>.olo-pg-a:not(:last-child):not(.olo-pg-sk-ico):not(:has(+.olo-pg-sk-ico))::after{content:'{$sep}';margin-left:.5rem;opacity:.7}";
        }
        // La durata dei video in un'etichetta scura col triangolo del play
        $css .= "{$u} .olo-pg-dur-et{display:inline-flex;align-items:center;gap:.4em;padding:.4em .55em;border-radius:.25rem;background:rgb(0 0 0 / .55);color:#fff;opacity:1;font-family:{$mono}}";
        // La nota: grande (un prezzo), un'etichetta (nel colore della voce o in quello scelto) o con la lineetta
        $nc   = $this->safe_color_css( $s['scheda_nota_colore'] ?? '' );
        $css .= "{$u} .olo-pg-sk-nota{font-size:12px;line-height:1.3;opacity:.9;font-variant-numeric:tabular-nums}";
        $css .= "{$u} .olo-pg-nota-grande{display:inline-flex;align-items:baseline;gap:.45em;opacity:1}{$u} .olo-pg-nota-grande b{font-size:clamp(18px,1.7vw,24px);font-weight:800;line-height:1;letter-spacing:-.01em}{$u} .olo-pg-nota-grande small{font-family:{$mono};font-size:10px;opacity:.75}";
        $css .= "{$u} .olo-pg-nota-etichetta{display:inline-flex;align-items:center;padding:.45em .75em;border-radius:100vmax;font-family:{$mono};font-size:9px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;line-height:1;opacity:1;"
            . ( $nc ? "background:{$nc};color:" . $this->testo_su( $nc ) . ';border-radius:.125rem' : "background:var(--pg-c,rgb(255 255 255 / .12));color:var(--pg-cf,currentColor);border:1px solid color-mix(in srgb,currentColor 25%,transparent)" ) . '}';
        $css .= "{$u} .olo-pg-nota-linea::before{content:'';display:inline-block;width:1.1em;height:1px;background:currentColor;vertical-align:middle;margin-right:.5em;opacity:.7}";
        $css .= "{$u} .olo-pg-nota-linea{font-family:{$mono}}";
        // Il riflesso olografico (una voce sì, le altre no): colori cangianti che scorrono piano, fermi con «riduci movimento»
        $holo = 'linear-gradient(110deg,#ffc6ec,#c4ebff 28%,#d6ffd0 52%,#fff0ae 74%,#e9ccff 90%,#ffc6ec)';
        $css .= "{$u}.olo-pg-holo .olo-pg-nota-etichetta{background:{$holo} 0 0/260% 100%;color:#2a2238;border-color:rgb(255 255 255 / .6);animation:olo-pg-holo 6s linear infinite}";
        $css .= "{$u}.olo-pg-holo::before{border-color:transparent!important;background:{$holo} border-box 0 0/260% 100%;-webkit-mask:linear-gradient(#000 0 0) padding-box,linear-gradient(#000 0 0);-webkit-mask-composite:xor;mask:linear-gradient(#000 0 0) padding-box exclude,linear-gradient(#000 0 0);animation:olo-pg-holo 6s linear infinite}";
        $css .= '@keyframes olo-pg-holo{to{background-position:260% 0}}';
        $css .= "@media(prefers-reduced-motion:reduce){{$u}.olo-pg-holo .olo-pg-nota-etichetta,{$u}.olo-pg-holo::before{animation:none}}";
        // Il quadratino nel colore della voce (i campionari)
        $css .= "{$u} .olo-pg-sk-campione{flex:0 0 auto;display:inline-block;width:.85rem;height:.85rem;background:{$tinta};border:1.5px solid #fff;box-shadow:0 0 0 1px rgb(0 0 0 / .18)}";
        // Il pallino davanti al testo (le specifiche di una lampada)
        $css .= "{$u} .olo-pg-punto::before{content:'';display:inline-block;width:.6em;height:.6em;border-radius:50%;background:var(--pg-c,var(--olo-color-warning));margin-right:.55em;vertical-align:.02em}";
        // Le icone: quadrata, piena, nuda; il colore; quella dopo il titolo; la freccia lunga
        $ic   = $this->safe_color_css( $s['scheda_icona_colore'] ?? '' );
        $ist  = (string) ( $s['scheda_icona_stile'] ?? '' );
        $ico  = "{$u} .olo-pg-sk-ico";
        if ( $ic ) {
            $css .= "{$ico}{color:{$ic}}";
        }
        if ( 'quadrato' === $ist ) {
            $css .= "{$ico}{border-radius:.15rem;width:28px;height:28px;border-width:1px}";
        } elseif ( 'pieno' === $ist ) {
            $css .= "{$ico}{width:24px;height:24px;border:0;background:" . ( $ic ? $ic : 'var(--olo-color-dark)' ) . ";color:#fff}{$ico} svg{width:13px;height:13px}";
        } elseif ( 'nudo' === $ist ) {
            $css .= "{$ico}{width:auto;height:auto;border:0;border-radius:0;background:none}";
        }
        $css .= "{$u} .olo-pg-ico-linea{width:auto;height:auto;border:0;border-radius:0;background:none;" . ( $ic ? '' : "color:{$tinta};" ) . '}';
        $css .= "{$u} .olo-pg-sk-dopo{display:inline-flex;vertical-align:-.05em;margin-left:.35em}{$u} .olo-pg-sk-dopo .olo-pg-sk-ico{width:auto;height:auto;border:0;background:none;" . ( $ic ? '' : "color:{$tinta};" ) . "}{$u} .olo-pg-sk-dopo svg{width:.8em;height:.8em}";
        // Davanti al titolo (un codice): in riga col titolo, il sottotitolo sotto il titolo
        $css .= "{$u} .olo-pg-sk-tr{display:flex;align-items:baseline;gap:.75rem;min-width:0}{$u} .olo-pg-sk-tcol{display:flex;flex-direction:column;gap:.2rem;min-width:0}";
        // Il pulsante: a contorno o come una scritta sottolineata
        $ps = (string) ( $s['scheda_pulsante_stile'] ?? '' );
        if ( 'contorno' === $ps ) {
            $css .= "{$u} .olo-pg-sk-btn{background:transparent;color:inherit;border:1.5px solid currentColor;padding:.6em 1.1em}";
        } elseif ( 'testo' === $ps ) {
            $css .= "{$u} .olo-pg-sk-btn{background:none;color:inherit;border-radius:0;padding:0 0 .2em;border-bottom:1px solid currentColor;font-family:{$mono};font-size:10px;letter-spacing:.14em;text-transform:uppercase}";
        }
        // La linea sotto ogni card (le riviste)
        if ( ! empty( $s['scheda_linea_sotto'] ) ) {
            $css .= "{$u} .olo-pg-sk{border-bottom:1px solid color-mix(in srgb,currentColor 20%,transparent);padding-bottom:1.1rem}";
        }
        // Coi testi sotto la foto, il fondo nel colore della voce (le card colorate di un negozio)
        if ( 'sotto' === $o['modo'] && ! empty( $s['scheda_sfondo_voce'] ) ) {
            $cs   = $this->safe_color_css( $s['scheda_colore'] ?? '' );
            $pad  = Olobuild_Tile_Utils::sides_css( Olobuild_Tile_Utils::spacing_sides( $s['scheda_padding'] ?? 18, [], [ 18, 18, 18, 18 ] ) );
            $css .= "{$u}{background:var(--pg-c,transparent)!important}{$u} .olo-pg-sk{padding:{$pad};color:" . ( $cs ? $cs : 'var(--pg-cf,inherit)' ) . '}';
            $css .= "{$u} .olo-pg-sk .olo-pg-sk-num,{$u} .olo-pg-sk .olo-pg-sk-cat{color:inherit;opacity:.85}";
        }
        // Card coi testi sotto e un fondo: la foto tiene gli angoli solo in alto
        if ( 'sotto' === $o['modo'] && ( '' !== (string) ( $s['scheda_sfondo'] ?? '' ) || ! empty( $s['scheda_sfondo_voce'] ) ) ) {
            $css .= "{$u} .olo-pg-media{border-radius:{$radius} {$radius} 0 0}{$u}{border-radius:{$radius};overflow:hidden!important}";
        }
        // Il titolo cresce con la foto: grande sulle foto grandi, piccolo sulle piccole
        if ( ! empty( $s['scheda_titolo_adatta'] ) ) {
            // (le misure miste danno a ogni foto quante colonne occupa: --pw)
            $base = max( 12, min( 40, absint( $s['scheda_titolo_size'] ?? 0 ) ?: 18 ) );
            $css .= "{$u} .olo-pg-sk-tit{font-size:clamp(14px,calc({$base}px * var(--pw,1)),56px);line-height:1.05}";
            $css .= "@media(max-width:640px){{$u} .olo-pg-sk-tit{font-size:clamp(15px,calc({$base}px * var(--pwm,1) * .85),34px)}}";
        }
        // Le linee fra le celle (le pareti di loghi): la griglia senza spazi, una linea sottile fra una cella e l'altra
        if ( ! empty( $s['griglia_linee'] ) && in_array( $layout, [ 'grid', 'metro', 'mosaic' ], true ) ) {
            $lc   = $this->safe_color_css( $s['griglia_linee_colore'] ?? '' ) ?: 'color-mix(in srgb,currentColor 12%,transparent)';
            $css .= ".{$uid}{gap:1px!important;background:{$lc};border:1px solid {$lc};border-radius:{$radius};overflow:hidden}";
            $css .= "{$u}{border-radius:0!important;background:var(--olo-color-background,#fff)}{$u} img{border-radius:0!important}";
            if ( 'contain' === ( $s['object_fit'] ?? '' ) ) {
                // I loghi piccoli, in mezzo alla cella
                $css .= "{$u} img{padding:20% 24%;box-sizing:border-box}";
            }
        }
        return $css;
    }

    /**
     * La barra dei filtri: «Tutti» e un pulsante per categoria, coi conteggi.
     *
     * @param string $uid       id della galleria
     * @param array  $cats      [ slug => nome ]
     * @param array  $conti     [ slug => quante foto ]
     * @param int    $tot       foto in tutto
     * @param string $tutti     testo di «Tutti» ('' = niente «Tutti»: coi gruppi in linea ce n'è uno solo, davanti)
     * @param bool   $con_conti mostra i conteggi
     * @param array  $colori    [ slug => colore ] per i pallini
     * @return string
     */
    private function filtri_html( $uid, $cats, $conti, $tot, $tutti, $con_conti, $gruppo = null, $nome_gruppo = '', $colori = [] ) {
        $sup = function ( $n ) use ( $con_conti ) {
            return $con_conti ? '<sup>' . (int) $n . '</sup>' : '';
        };
        $et = '' !== $nome_gruppo ? $nome_gruppo : olobuild_t( 'Filtra per categoria' );
        $h  = '<div class="olo-pg-filtri ' . esc_attr( $uid ) . '-filtri" data-per="' . esc_attr( $uid ) . '"' . ( null !== $gruppo ? ' data-gruppo="' . esc_attr( $gruppo ) . '"' : '' ) . ' role="group" aria-label="' . esc_attr( $et ) . '">';
        if ( '' !== $nome_gruppo ) {
            $h .= '<span class="olo-pg-filtri-nome" aria-hidden="true">' . esc_html( $nome_gruppo ) . '</span>';
        }
        if ( '' !== $tutti ) {
            $h .= '<button type="button" class="attivo" aria-pressed="true" data-filtro="*">' . esc_html( $tutti ) . $sup( $tot ) . '</button>';
        }
        foreach ( $cats as $slug => $nome ) {
            $punto = ! empty( $colori[ $slug ] ) ? '<span class="olo-pg-fpunto" style="--fp:' . esc_attr( $colori[ $slug ] ) . '" aria-hidden="true"></span>' : '';
            $h    .= '<button type="button" aria-pressed="false" data-filtro="' . esc_attr( $slug ) . '">' . $punto . esc_html( $nome ) . $sup( $conti[ $slug ] ?? 0 ) . '</button>';
        }
        return $h . '</div>';
    }

    /** I filtri come menu a tendina (uno per gruppo). */
    private function filtri_menu( $uid, $cats, $conti, $tot, $tutti, $con_conti, $gruppo = null, $nome = '' ) {
        $sup = function ( $n ) use ( $con_conti ) {
            return $con_conti ? ' (' . (int) $n . ')' : '';
        };
        $h  = '<select class="olo-pg-filtri-menu" data-per="' . esc_attr( $uid ) . '"' . ( null !== $gruppo ? ' data-gruppo="' . esc_attr( $gruppo ) . '"' : '' ) . ' aria-label="' . esc_attr( '' !== $nome ? $nome : olobuild_t( 'Filtra per categoria' ) ) . '">';
        $h .= '<option value="*">' . esc_html( $tutti . $sup( $tot ) ) . '</option>';
        foreach ( $cats as $slug => $n ) {
            $h .= '<option value="' . esc_attr( $slug ) . '">' . esc_html( $n . $sup( $conti[ $slug ] ?? 0 ) ) . '</option>';
        }
        return $h . '</select>';
    }

    /**
     * Il CSS della barra dei filtri: lo stile (pillole, bordate, semplici, caselle, contorno, sottolineato), la forma,
     * i conteggi (in apice o accanto), i pallini delle categorie e il carattere scelto.
     */
    private function filtri_css( $uid, $s ) {
        $u     = '.' . $uid . '-filtri';
        $stile = in_array( $s['filtri_stile'] ?? '', [ 'pillole', 'contorno', 'sottolineato', 'bordate', 'semplice', 'caselle' ], true ) ? $s['filtri_stile'] : 'pillole';
        $all   = [ 'left' => 'flex-start', 'center' => 'center', 'right' => 'flex-end' ][ $s['filtri_allinea'] ?? 'left' ] ?? 'flex-start';
        $acc   = $this->safe_color_css( $s['filtri_colore'] ?? '' ) ?: 'var(--olo-color-primary)';
        $acf   = $this->safe_color_css( $s['filtri_colore_testo'] ?? '' ) ?: $this->testo_su_dich( $acc );
        $linea = 'color-mix(in srgb,currentColor 22%,transparent)';
        $css   = "{$u}{display:flex;flex-wrap:wrap;gap:.5rem;justify-content:{$all};align-items:center;margin:0 0 1.25rem}";
        $css  .= "{$u} button{font:inherit;font-size:13px;line-height:1;cursor:pointer;padding:.6em 1em;border-radius:100vmax;border:1px solid transparent;background:transparent;color:inherit;display:inline-flex;align-items:center;transition:background-color .2s ease,color .2s ease,border-color .2s ease;-webkit-appearance:none;appearance:none}";
        $css  .= "{$u} button sup{font-size:.7em;margin-left:.3em;opacity:.85;vertical-align:super;line-height:0}";
        $css  .= "{$u} button:focus-visible{outline:2px solid {$acc};outline-offset:2px}";
        if ( 'pillole' === $stile ) {
            $css .= "{$u} button{background:color-mix(in srgb,currentColor 7%,transparent)}";
            $css .= "{$u} button:hover{background:color-mix(in srgb,currentColor 13%,transparent)}";
            $css .= "{$u} button.attivo{background:{$acc};color:{$acf}}";
        } elseif ( 'contorno' === $stile ) {
            $css .= "{$u} button{border-color:{$linea};border-radius:.25rem;text-transform:uppercase;letter-spacing:.1em;font-size:11px}";
            $css .= "{$u} button:hover{border-color:currentColor}";
            $css .= "{$u} button.attivo{border-color:{$acc};background:{$acc};color:{$acf}}";
        } elseif ( 'bordate' === $stile ) {
            // Bordate: il contorno sottile, la scelta piena
            $css .= "{$u} button{border-color:{$linea};font-weight:500}";
            $css .= "{$u} button:hover{border-color:color-mix(in srgb,currentColor 50%,transparent)}";
            $css .= "{$u} button.attivo{border-color:{$acc};background:{$acc};color:{$acf}}";
        } elseif ( 'semplice' === $stile ) {
            // Semplici: solo la scritta, la scelta piena
            $css .= "{$u}{gap:.25rem}";
            $css .= "{$u} button{border-radius:.4rem;opacity:.8}";
            $css .= "{$u} button:hover{opacity:1;background:color-mix(in srgb,currentColor 6%,transparent)}";
            $css .= "{$u} button.attivo{opacity:1;background:{$acc};color:{$acf}}";
        } elseif ( 'caselle' === $stile ) {
            // Caselle: ogni valore ha la sua casellina da spuntare (si combinano), «Tutti» pieno
            $spunta = "url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12'%3E%3Cpath d='M2.5 6.2 5 8.6 9.6 3.6' fill='none' stroke='white' stroke-width='1.8' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E\")";
            $css .= "{$u} button{gap:.55em;border-radius:.4rem;padding:.6em .8em;background:var(--olo-color-background,#fff);color:var(--olo-color-text,inherit);border-color:color-mix(in srgb,currentColor 14%,transparent)}";
            $css .= "{$u} button[data-filtro]:not([data-filtro=\"*\"])::before{content:'';flex:0 0 auto;width:.9em;height:.9em;border-radius:.2em;border:1px solid color-mix(in srgb,currentColor 40%,transparent);background:center/90% no-repeat;transition:background-color .2s ease,border-color .2s ease}";
            $css .= "{$u} button:hover{border-color:color-mix(in srgb,currentColor 35%,transparent)}";
            $css .= "{$u} button.attivo:not([data-filtro=\"*\"]){border-color:color-mix(in srgb,{$acc} 55%,transparent)}";
            $css .= "{$u} button.attivo:not([data-filtro=\"*\"])::before{background-color:{$acc};border-color:{$acc};background-image:{$spunta}}";
            $css .= "{$u} button.attivo[data-filtro=\"*\"],.{$uid}-barra .olo-pg-tutti.attivo{background:{$acc};border-color:{$acc};color:{$acf}}";
            $css .= "{$u} button sup,.{$uid}-barra .olo-pg-tutti sup{color:inherit}";
        } else {
            $css .= "{$u}{gap:.25rem 1.4rem;border-bottom:1px solid color-mix(in srgb,currentColor 14%,transparent);padding-bottom:0}";
            $css .= "{$u} button{border-radius:0;padding:.7em 0;margin-bottom:-1px;border-bottom:2px solid transparent}";
            $css .= "{$u} button:hover{border-bottom-color:color-mix(in srgb,currentColor 35%,transparent)}";
            $css .= "{$u} button.attivo{border-bottom-color:{$acc};color:{$acc}}";
            if ( ! empty( $s['filtri_riga_sopra'] ) ) {
                // La riga spessa sopra i filtri, come la testata di una rivista
                $css .= "{$u}{border-top:2px solid currentColor;padding-top:.35rem}";
            }
        }
        $cb = $this->safe_color_css( $s['filtri_colore_bordo'] ?? '' );
        if ( $cb ) {
            // Il bordo dei filtri nel colore scelto, un po' più spesso (le card dei negozi)
            $css .= "{$u} button:not(.attivo){border-color:{$cb};border-width:1.5px}";
        }
        $forma = [ 'pillola' => '100vmax', 'arrotondata' => '.4rem', 'squadrata' => '.125rem' ][ $s['filtri_forma'] ?? '' ] ?? '';
        if ( '' !== $forma ) {
            $css .= "{$u} button,.{$uid}-barra .olo-pg-tutti{border-radius:{$forma}}";
        }
        $cs = (string) ( $s['filtri_conteggi_stile'] ?? '' );
        if ( in_array( $cs, [ 'accanto', 'punto' ], true ) ) {
            // I conteggi accanto al nome, piccoli e nel carattere a spaziatura fissa («Guida 10»)
            $css .= "{$u} button sup,.{$uid}-barra .olo-pg-tutti sup{position:static;top:auto;vertical-align:baseline;line-height:inherit;font-size:.8em;margin-left:.6em;opacity:.85;font-family:" . $this->resolve_font_family( 'mono' ) . ";font-variant-numeric:tabular-nums}";
            $css .= "{$u} button.attivo sup,.{$uid}-barra .olo-pg-tutti.attivo sup{opacity:.95}";
            if ( 'punto' === $cs ) {
                $css .= "{$u} button sup::before,.{$uid}-barra .olo-pg-tutti sup::before{content:'·';margin-right:.6em}";
            }
        }
        // Il pallino nel colore della categoria (il colore della prima voce che ce l'ha)
        $css .= "{$u} .olo-pg-fpunto{flex:0 0 auto;width:.5em;height:.5em;border-radius:50%;background:var(--fp);margin-right:.5em}";
        // Il carattere scelto (Stile › Filtri › Testo dei filtri): vince sullo stile
        $tipo = $this->tipografia_css( $s, 'filtri', [ 'color' => 'filtri__senza_colore', 'italic' => 'filtri__senza_corsivo' ] );
        if ( '' !== $tipo ) {
            $css .= "{$u} button,.{$uid}-barra .olo-pg-tutti{{$tipo}}";
        }
        // Le foto tolte dal filtro
        $css .= '.' . $uid . '.' . $uid . ' .olo-pg-item.olo-pg-via,.' . $uid . '.' . $uid . ' .olo-pg-via{display:none!important}';
        return $css;
    }

    /**
     * LA BARRA SOPRA LA GALLERIA: filtri (pulsanti o menu), ricerca e ordinamento, come le griglie dei portfolio.
     *
     * @param string $uid  classe della galleria
     * @param array  $b    opzioni della barra (barra_opzioni())
     * @param array  $cats [ slug => nome ]
     * @param array  $conti [ slug => quante foto ]
     * @param int    $tot  foto in tutto
     * @return string HTML già escapato
     */
    private function barra_html( $uid, $b, $cats, $conti, $tot, $gruppi = [] ) {
        $h        = '';
        $testo_c  = str_replace( '%d', (string) (int) $tot, $b['cerca_testo'] );
        $lente    = '<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="6.5"/><path d="m16 16 4 4"/></svg>';
        $cerca_in = '<input type="search" class="olo-pg-cerca" data-per="' . esc_attr( $uid ) . '" placeholder="' . esc_attr( $testo_c ) . '" aria-label="' . esc_attr( $testo_c ) . '">';
        $sopra    = $b['cerca'] && 'sopra' === $b['cerca_pos'];
        if ( $sopra ) {
            // La ricerca larga in cima, con la lente, e i filtri sotto
            $h .= '<div class="olo-pg-cerca-sopra">' . $lente . $cerca_in . '</div>';
        }
        $colori = [];
        if ( $b['punti'] ) {
            foreach ( $gruppi as $gr ) {
                $colori = array_merge( $colori, $gr['colori'] ?? [] );
            }
        }
        if ( $b['filtri'] ) {
            $a_gruppi = count( $gruppi ) > 1 || ( 1 === count( $gruppi ) && '' !== (string) array_key_first( $gruppi ) );
            $menu     = 'menu' === $b['filtri_stile'];
            $sup      = function ( $n ) use ( $b ) {
                return $b['conteggi'] ? '<sup>' . (int) $n . '</sup>' : '';
            };
            if ( $a_gruppi ? ( $b['linea'] ? ! $menu : false ) : false ) {
                // I gruppi in una riga sola: un «Tutti» davanti (azzera tutto), poi ogni gruppo col suo nome
                $h .= '<div class="olo-pg-gruppi olo-pg-gruppi-linea">';
                $h .= '<button type="button" class="olo-pg-azzera olo-pg-tutti attivo" data-per="' . esc_attr( $uid ) . '" aria-pressed="true">' . esc_html( $b['tutti'] ) . $sup( $tot ) . '</button>';
                foreach ( $gruppi as $gs => $gr ) {
                    $nome = '' === (string) $gs ? olobuild_t( 'Categoria' ) : $gr['nome'];
                    $h   .= $this->filtri_html( $uid, $gr['voci'], $gr['conti'], $tot, '', $b['conteggi'], (string) $gs, $nome, $colori );
                }
                $h .= '</div>';
            } elseif ( $a_gruppi ) {
                // Più gruppi (Tipo, Argomento, Livello…): una fila per gruppo, un valore per fila, e «Azzera i filtri»
                $h .= '<div class="olo-pg-gruppi' . ( $menu ? ' olo-pg-gruppi-menu' : '' ) . '">';
                foreach ( $gruppi as $gs => $gr ) {
                    $nome  = '' === (string) $gs ? olobuild_t( 'Categoria' ) : $gr['nome'];
                    $tutti = '' === (string) $gs ? $b['tutti'] : olobuild_t( 'Tutti' );
                    if ( $menu ) {
                        $h .= '<label class="olo-pg-gruppo-menu"><span class="olo-pg-filtri-nome">' . esc_html( $nome ) . '</span>' . $this->filtri_menu( $uid, $gr['voci'], $gr['conti'], $tot, $tutti, $b['conteggi'], (string) $gs, $nome ) . '</label>';
                    } else {
                        $h .= $this->filtri_html( $uid, $gr['voci'], $gr['conti'], $tot, $tutti, $b['conteggi'], (string) $gs, $nome, $colori );
                    }
                }
                $h .= '<button type="button" class="olo-pg-azzera" data-per="' . esc_attr( $uid ) . '" hidden>' . esc_html( olobuild_t( 'Azzera i filtri' ) ) . '</button></div>';
            } elseif ( $menu ) {
                $h .= $this->filtri_menu( $uid, $cats, $conti, $tot, $b['tutti'], $b['conteggi'] );
            } else {
                $h .= $this->filtri_html( $uid, $cats, $conti, $tot, $b['tutti'], $b['conteggi'], null, '', $colori );
            }
        }
        $destra = '';
        if ( $b['cerca'] ? ! $sopra : false ) {
            $destra .= $cerca_in;
        }
        if ( $b['ordina'] ) {
            $destra .= '<select class="olo-pg-ordina" data-per="' . esc_attr( $uid ) . '" aria-label="' . esc_attr( olobuild_t( 'Ordina' ) ) . '">'
                . '<option value="">' . esc_html( olobuild_t( 'Ordine della galleria' ) ) . '</option>'
                . '<option value="titolo">' . esc_html( olobuild_t( 'Titolo dalla A alla Z' ) ) . '</option>'
                . '<option value="-titolo">' . esc_html( olobuild_t( 'Titolo dalla Z alla A' ) ) . '</option>'
                . '<option value="testo">' . esc_html( olobuild_t( 'Testo crescente (anni, prezzi…)' ) ) . '</option>'
                . '<option value="-testo">' . esc_html( olobuild_t( 'Testo decrescente' ) ) . '</option>'
                . '<option value="caso">' . esc_html( olobuild_t( 'Ordine casuale' ) ) . '</option>'
                . '</select>';
        }
        if ( '' !== $destra ) {
            $h .= '<div class="olo-pg-barra-destra">' . $destra . '</div>';
        }
        return '' === $h ? '' : '<div class="olo-pg-barra ' . esc_attr( $uid ) . '-barra">' . $h . '</div>';
    }

    /**
     * I filtri in colonna a sinistra della galleria (le «faccette» dei negozi e degli archivi): dai 960 px in su i gruppi,
     * la ricerca e l'ordinamento stanno uno sotto l'altro, ogni filtro su una riga con il conteggio in fondo, e la colonna
     * resta ferma mentre la galleria scorre. Sotto i 960 px la barra torna sopra la galleria, com'era.
     */
    private function lato_css( $uid ) {
        $u   = '.' . $uid;
        $css = '@media(min-width:960px){';
        $css .= "{$u}-lato{display:grid;grid-template-columns:minmax(11rem,15rem) minmax(0,1fr);gap:2.5rem;align-items:start}";
        $css .= "{$u}-lato>.olo-pg-lato-filtri{position:sticky;top:6rem}";
        $css .= "{$u}-lato {$u}-barra{flex-direction:column;flex-wrap:nowrap;align-items:stretch;justify-content:flex-start;gap:1.6rem;margin:0}";
        $css .= "{$u}-lato {$u}-barra .olo-pg-gruppi,{$u}-lato {$u}-barra .olo-pg-gruppi-linea{flex-direction:column;flex-wrap:nowrap;align-items:stretch;gap:1.6rem}";
        $css .= "{$u}-lato {$u}-filtri{flex-direction:column;flex-wrap:nowrap;align-items:stretch;gap:.3rem;margin:0;border:0;padding:0}";
        $css .= "{$u}-lato {$u}-filtri .olo-pg-filtri-nome{min-width:0;margin:0 0 .4rem;align-self:flex-start}";
        $css .= "{$u}-lato {$u}-filtri button,{$u}-lato {$u}-barra .olo-pg-tutti{width:100%;justify-content:flex-start;text-align:left}";
        $css .= "{$u}-lato {$u}-filtri button sup,{$u}-lato {$u}-barra .olo-pg-tutti sup{margin-left:auto;padding-left:.75em}";
        $css .= "{$u}-lato {$u}-barra .olo-pg-azzera:not(.olo-pg-tutti){align-self:flex-start}";
        $css .= "{$u}-lato {$u}-barra .olo-pg-barra-destra{margin-left:0;width:100%;flex-direction:column;align-items:stretch}";
        $css .= "{$u}-lato {$u}-barra .olo-pg-cerca{width:100%;min-width:0}{$u}-lato {$u}-barra .olo-pg-cerca-sopra{max-width:none}";
        return $css . '}';
    }

    /** Le opzioni della barra e del «carica altri», già pulite. */
    private function barra_opzioni( $s, $layout, $filtri_on ) {
        $ok    = in_array( $layout, self::SCHEMI_FILTRI, true );
        $piu   = $ok && in_array( $s['piu_foto'] ?? '', [ 'carica', 'infinito', 'pagine' ], true ) ? $s['piu_foto'] : '';
        $tutti = trim( (string) ( $s['filtri_tutti'] ?? '' ) );
        $cerca = trim( (string) ( $s['cerca_testo'] ?? '' ) );
        return [
            'filtri'       => $filtri_on,
            'filtri_stile' => (string) ( $s['filtri_stile'] ?? 'pillole' ),
            'tutti'        => '' !== $tutti ? $tutti : olobuild_t( 'Tutti' ),
            'conteggi'     => ! empty( $s['filtri_conteggi'] ),
            'linea'        => ! empty( $s['filtri_linea'] ),
            'punti'        => ! empty( $s['filtri_punti'] ),
            'cerca'        => $ok && ! empty( $s['cerca'] ),
            'cerca_testo'  => '' !== $cerca ? $cerca : olobuild_t( 'Cerca…' ),
            'cerca_pos'    => 'sopra' === ( $s['cerca_posizione'] ?? '' ) ? 'sopra' : '',
            'ordina'       => $ok && ! empty( $s['ordina'] ),
            'piu'          => $piu,
            'per'          => max( 2, min( 60, absint( $s['per_pagina'] ?? 8 ) ) ),
            'link'         => $filtri_on && ! empty( $s['filtri_link'] ),
            'multi'        => $filtri_on && ( ! empty( $s['filtri_multipli'] ) || 'caselle' === ( $s['filtri_stile'] ?? '' ) ),
            'mescola'      => $ok && ! empty( $s['mescola'] ) && in_array( $layout, [ 'grid', 'masonry', 'justified', 'diagonal', 'elenco' ], true ),
        ];
    }

    /** «Mostra altre» o i numeri delle pagine, e l'annuncio dei risultati per i lettori di schermo. */
    private function piu_html( $uid, $b, $n ) {
        $h = '';
        if ( in_array( $b['piu'], [ 'carica', 'infinito' ], true ) && $n > $b['per'] ) {
            $h .= '<div class="olo-pg-piu ' . esc_attr( $uid ) . '-piu" data-per="' . esc_attr( $uid ) . '"><button type="button" class="olo-pg-piu-btn"><span>'
                . esc_html( sprintf( olobuild_t( 'Mostra altre (%d)' ), $n - $b['per'] ) ) . '</span><svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 5v14M6 13l6 6 6-6"/></svg></button></div>';
        } elseif ( 'pagine' === $b['piu'] && $n > $b['per'] ) {
            $h .= '<nav class="olo-pg-piu ' . esc_attr( $uid ) . '-piu" data-per="' . esc_attr( $uid ) . '" aria-label="' . esc_attr( olobuild_t( 'Pagine della galleria' ) ) . '">';
            $pagine = (int) ceil( $n / $b['per'] );
            for ( $p = 1; $p <= $pagine; $p++ ) {
                $h .= '<button type="button" data-pagina="' . $p . '"' . ( 1 === $p ? ' aria-current="page"' : '' ) . '>' . $p . '</button>';
            }
            $h .= '</nav>';
        }
        $h .= '<p class="olo-pg-esito" data-per="' . esc_attr( $uid ) . '" aria-live="polite"></p>';
        return $h;
    }

    /** Il CSS di barra, ricerca, ordinamento, menu dei filtri e «carica altri». */
    private function barra_css( $uid, $s, $b ) {
        $acc = $this->safe_color_css( $s['filtri_colore'] ?? '' ) ?: 'var(--olo-color-primary)';
        $u   = '.' . $uid;
        $css = "{$u}-barra{display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:.75rem 1.25rem;margin:0 0 1.25rem}";
        $css .= "{$u}-barra .olo-pg-filtri{margin:0;flex:1 1 auto}";
        $css .= "{$u}-barra .olo-pg-barra-destra{display:flex;flex-wrap:wrap;gap:.5rem;align-items:center;margin-left:auto}";
        $campo = 'font:inherit;font-size:13px;line-height:1.2;color:inherit;background:transparent;border:1px solid color-mix(in srgb,currentColor 22%,transparent);border-radius:100vmax;padding:.6em 1em';
        $css .= "{$u}-barra .olo-pg-cerca,{$u}-barra select{{$campo}}";
        $css .= "{$u}-barra .olo-pg-cerca{min-width:12em}";
        $css .= "{$u}-barra select{padding-right:2em;-webkit-appearance:none;appearance:none;background-image:linear-gradient(45deg,transparent 50%,currentColor 50%),linear-gradient(135deg,currentColor 50%,transparent 50%);background-position:calc(100% - 1.1em) 52%,calc(100% - .8em) 52%;background-size:.3em .3em;background-repeat:no-repeat;cursor:pointer}";
        $css .= "{$u}-barra .olo-pg-cerca:focus-visible,{$u}-barra select:focus-visible{outline:2px solid {$acc};outline-offset:2px}";
        // La ricerca larga in cima, con la lente nel colore del filtro attivo
        $css .= "{$u}-barra .olo-pg-cerca-sopra{position:relative;flex:1 1 100%;max-width:34rem;display:flex;align-items:center}";
        $css .= "{$u}-barra .olo-pg-cerca-sopra svg{position:absolute;left:1.1em;color:{$acc};pointer-events:none}";
        $css .= "{$u}-barra .olo-pg-cerca-sopra .olo-pg-cerca{width:100%;min-width:0;font-size:14px;padding:.95em 1em .95em 2.9em;border-radius:.5rem;background:var(--olo-color-background,transparent);color:var(--olo-color-text,inherit);border-color:color-mix(in srgb,currentColor 14%,transparent)}";
        $css .= "{$u}{$u} .olo-pg-item.olo-pg-pag,{$u}{$u} .olo-pg-pag{display:none!important}";
        $css .= "{$u}-piu{display:flex;justify-content:center;flex-wrap:wrap;gap:.4rem;margin:1.5rem 0 0}";
        $css .= "{$u}-piu button{font:inherit;font-size:13px;cursor:pointer;color:inherit;background:transparent;border:1px solid color-mix(in srgb,currentColor 22%,transparent);border-radius:100vmax;padding:.7em 1.4em;min-width:2.8em;display:inline-flex;align-items:center;justify-content:center;gap:.5em;transition:background-color .2s ease,color .2s ease,border-color .2s ease}";
        $css .= "{$u}-piu button svg{transition:transform .25s ease}{$u}-piu button:hover svg{transform:translateY(2px)}";
        $css .= "{$u}-piu button:hover{border-color:currentColor}";
        $css .= "{$u}-piu button[aria-current]{background:{$acc};border-color:{$acc};color:#fff}";
        $css .= "{$u}-piu button:focus-visible{outline:2px solid {$acc};outline-offset:2px}";
        $css .= ".olo-pg-esito{position:absolute;width:1px;height:1px;overflow:hidden;clip:rect(0 0 0 0);white-space:nowrap}";
        $css .= "@media(max-width:640px){{$u}-barra .olo-pg-barra-destra{margin-left:0;width:100%}{$u}-barra .olo-pg-cerca{flex:1 1 auto;min-width:0}}";
        // Gruppi di filtri
        $css .= "{$u}-barra .olo-pg-gruppi{display:flex;flex-direction:column;gap:.6rem;flex:1 1 100%;min-width:0}";
        $css .= "{$u}-barra .olo-pg-gruppi.olo-pg-gruppi-menu{flex-direction:row;flex-wrap:wrap;align-items:center;gap:.6rem 1.2rem}";
        $css .= "{$u}-barra .olo-pg-gruppi .olo-pg-filtri{margin:0}";
        $css .= "{$u}-barra .olo-pg-filtri-nome{font-size:11px;font-weight:600;letter-spacing:.12em;text-transform:uppercase;opacity:.78;min-width:7.5em;align-self:center}";
        $css .= "{$u}-barra .olo-pg-gruppo-menu{display:inline-flex;align-items:center;gap:.5rem}{$u}-barra .olo-pg-gruppo-menu .olo-pg-filtri-nome{min-width:0}";
        $css .= "{$u}-barra .olo-pg-azzera{align-self:flex-start;font:inherit;font-size:12px;background:none;border:0;padding:.2em 0;color:inherit;text-decoration:underline;text-underline-offset:.2em;cursor:pointer;opacity:.75}";
        $css .= "{$u}-barra .olo-pg-azzera:hover{opacity:1}{$u}-barra .olo-pg-azzera:focus-visible{outline:2px solid {$acc};outline-offset:2px}";
        // I gruppi in una riga: «Tutti» pieno davanti, ogni gruppo col suo nome piccolo, a capo se non ci stanno
        $css .= "{$u}-barra .olo-pg-gruppi-linea{flex-direction:row;flex-wrap:wrap;align-items:center;gap:.5rem 1.4rem}";
        $css .= "{$u}-barra .olo-pg-gruppi-linea .olo-pg-filtri{flex:0 1 auto}";
        $css .= "{$u}-barra .olo-pg-gruppi-linea .olo-pg-filtri-nome{min-width:0;margin-right:.35rem;font-family:" . $this->resolve_font_family( 'mono' ) . ";font-size:10px;font-weight:500}";
        $css .= "{$u}-barra .olo-pg-azzera.olo-pg-tutti{align-self:center;text-decoration:none;opacity:1;font-size:13px;font-weight:600;line-height:1;padding:.6em 1em;border:1px solid color-mix(in srgb,currentColor 22%,transparent);border-radius:.4rem;display:inline-flex;align-items:center}";
        $css .= "{$u}-barra .olo-pg-azzera.olo-pg-tutti.attivo{background:{$acc};border-color:{$acc};color:" . ( $this->safe_color_css( $s['filtri_colore_testo'] ?? '' ) ?: $this->testo_su_dich( $acc ) ) . "}";
        $css .= "@media(max-width:640px){{$u}-barra .olo-pg-filtri-nome{min-width:100%}{$u}-barra .olo-pg-gruppi-linea .olo-pg-filtri-nome{min-width:0}}";
        return $css;
    }

    /**
     * Lo script di barra e «carica altri», una volta per pagina: tiene le foto che passano tutti i filtri scelti (un
     * valore per gruppo: «Tipo» E «Livello») e contengono il testo cercato, le mette nell'ordine scelto, mostra la
     * pagina giusta e fa scivolare le foto al posto nuovo (FLIP: si misura prima e dopo, poi si anima la differenza).
     * Con «Link ai filtri» l'indirizzo ricorda i filtri (#filtro=a,b) e un link così apre la galleria già filtrata.
     * Niente doppia «e commerciale» qui: WordPress la guasta negli script in linea.
     */
    private function barra_js() {
        static $fatto = false;
        if ( $fatto ) {
            return '';
        }
        $fatto = true;
        return '<script>(function(){if(window.oloPgBarra)return;window.oloPgBarra=1;'
            . 'var ST=new WeakMap();'
            . 'function G(id){return document.querySelector("[data-pg-id=\""+id+"\"]")}'
            . 'function S(g){var s=ST.get(g);if(!s){s={sel:{},q:"",ord:"",pag:1,orig:[].slice.call(g.querySelectorAll(".olo-pg-item"))};ST.set(g,s)}return s}'
            . 'function N(t){return (t||"").toLowerCase().normalize("NFD").replace(/[̀-ͯ]/g,"")}'
            . 'function NUM(t){var m=(t||"").replace(/\./g,"").replace(",",".").match(/-?\d+(\.\d+)?/);return m?parseFloat(m[0]):NaN}'
            . 'function cmp(o){var k=o.replace("-",""),d=o.charAt(0)==="-"?-1:1;return function(a,b){var x,y;'
            . 'if(k==="testo"){x=NUM(a.getAttribute("data-testo3"));y=NUM(b.getAttribute("data-testo3"));if(!(isNaN(x)||isNaN(y)))return (x-y)*d;x=a.getAttribute("data-testo3")||"";y=b.getAttribute("data-testo3")||""}'
            . 'else{x=a.getAttribute("data-titolo")||"";y=b.getAttribute("data-titolo")||""}return x.localeCompare(y,undefined,{numeric:true,sensitivity:"base"})*d}}'
            . 'function T(g,k,n){var t={};try{t=JSON.parse(g.getAttribute("data-pg-testi")||"{}")}catch(e){}return (t[k]||"").replace("%d",n)}'
            . 'function attivi(s){var a=[];for(var k in s.sel){a=a.concat(s.sel[k])}return a}'
            . 'function passa(x,s,q){var c=" "+(x.getAttribute("data-cat")||"")+" ";for(var k in s.sel){var v=s.sel[k];if(v.length){var hit=false;v.forEach(function(w){if(c.indexOf(" "+w+" ")>-1)hit=true});if(!hit)return false}}'
            . 'if(!q)return true;return N(x.getAttribute("data-cerca")).indexOf(q)>-1}'
            . 'function aggiorna(g,anima){var s=S(g),id=g.getAttribute("data-pg-id"),piu=g.getAttribute("data-pg-piu")||"",per=parseInt(g.getAttribute("data-pg-per")||"8",10);'
            . 'var tutti=s.orig.slice();if(s.ord==="caso"){if(!s.caso){s.caso=s.orig.slice();for(var r1=s.caso.length-1;r1>0;r1--){var r2=Math.floor(Math.random()*(r1+1)),r3=s.caso[r1];s.caso[r1]=s.caso[r2];s.caso[r2]=r3}}tutti=s.caso.slice()}else if(s.ord){tutti.sort(cmp(s.ord))}'
            . 'var prima=new Map();if(anima)tutti.forEach(function(x){if(x.offsetParent)prima.set(x,x.getBoundingClientRect())});'
            . 'var q=N(s.q),ok=tutti.filter(function(x){return passa(x,s,q)});'
            . 'var da=0,a=ok.length;if(piu==="carica"||piu==="infinito")a=Math.min(ok.length,s.pag*per);if(piu==="pagine"){da=(s.pag-1)*per;a=Math.min(ok.length,da+per)}'
            . 'tutti.forEach(function(x){g.appendChild(x);var i=ok.indexOf(x);x.classList.toggle("olo-pg-via",i<0);x.classList.toggle("olo-pg-pag",i>-1?(i<da||i>=a):false)});'
            . 'var p=document.querySelector(".olo-pg-piu[data-per=\""+id+"\"]");'
            . 'if(p?(piu==="carica"||piu==="infinito"):false){var b=p.querySelector("button");var resta=ok.length-a;b.hidden=resta<1;(b.querySelector("span")||b).textContent=T(g,"altre",resta)}'
            . 'if(p?piu==="pagine":false){var P=Math.max(1,Math.ceil(ok.length/per)),h="";for(var k=1;k<=P;k++)h+="<button type=\"button\" data-pagina=\""+k+"\""+(k===s.pag?" aria-current=\"page\"":"")+">"+k+"</button>";p.innerHTML=P>1?h:""}'
            . 'var az=document.querySelector(".olo-pg-azzera[data-per=\""+id+"\"]");if(az){var nes=attivi(s).length<1;if(az.classList.contains("olo-pg-tutti")){az.classList.toggle("attivo",nes);az.setAttribute("aria-pressed",nes?"true":"false")}else{az.hidden=nes}}'
            . 'var es=document.querySelector(".olo-pg-esito[data-per=\""+id+"\"]");if(es)es.textContent=T(g,ok.length===1?"uno":"molti",ok.length);'
            . 'if(!anima)return;if(window.matchMedia("(prefers-reduced-motion: reduce)").matches)return;'
            . 'tutti.forEach(function(x){if(!x.offsetParent)return;var r=x.getBoundingClientRect(),o=prima.get(x);x.style.transition="none";'
            . 'if(o){x.style.transform="translate("+(o.left-r.left)+"px,"+(o.top-r.top)+"px)"}else{x.style.opacity="0";x.style.transform="scale(.94)"}});'
            . 'g.offsetWidth;tutti.forEach(function(x){if(!x.offsetParent)return;x.style.transition="transform .5s cubic-bezier(.2,.8,.2,1),opacity .4s ease";x.style.transform="";x.style.opacity="";'
            . 'setTimeout(function(){x.style.transition=""},550)})}'
            . 'function segna(id,gr,f){document.querySelectorAll(".olo-pg-filtri[data-per=\""+id+"\"]").forEach(function(box){if((box.getAttribute("data-gruppo")||"")!==gr)return;'
            . 'box.querySelectorAll("button").forEach(function(x){var v=x.getAttribute("data-filtro"),on=f.length?f.indexOf(v)>-1:v==="*";x.classList.toggle("attivo",on);x.setAttribute("aria-pressed",on?"true":"false")})});'
            . 'document.querySelectorAll(".olo-pg-filtri-menu[data-per=\""+id+"\"]").forEach(function(m){if((m.getAttribute("data-gruppo")||"")===gr)m.value=f.length?f[0]:"*"})}'
            . 'function link(g){if(!g.hasAttribute("data-pg-link"))return;if(!history.replaceState)return;var a=attivi(S(g));history.replaceState(null,"",a.length?"#filtro="+a.join(","):location.pathname+location.search)}'
            . 'function filtra(id,f,gr){var g=G(id);if(!g)return;var s=S(g);gr=gr||"";var cur=s.sel[gr]||[];'
            . 'if(f==="*"){cur=[]}else if(g.hasAttribute("data-pg-multi")){var p0=cur.indexOf(f);cur=cur.slice();if(p0>-1){cur.splice(p0,1)}else{cur.push(f)}}else{cur=[f]}'
            . 's.sel[gr]=cur;s.pag=1;segna(id,gr,cur);link(g);aggiorna(g,true)}'
            . 'function gruppoDi(id,sl){var b=document.querySelector(".olo-pg-filtri[data-per=\""+id+"\"] [data-filtro=\""+sl+"\"]");if(b)return b.parentNode.getAttribute("data-gruppo")||"";'
            . 'var o=document.querySelector(".olo-pg-filtri-menu[data-per=\""+id+"\"] option[value=\""+sl+"\"]");if(o)return o.parentNode.getAttribute("data-gruppo")||"";return null}'
            . 'function daHash(g){var m=location.hash.match(/^#filtro=([\w,-]+)/);if(!m)return false;var id=g.getAttribute("data-pg-id"),s=S(g),fatto=false,multi=g.hasAttribute("data-pg-multi");'
            . 'Object.keys(s.sel).forEach(function(k){segna(id,k,[])});s.sel={};'
            . 'm[1].split(",").forEach(function(sl){var gr=gruppoDi(id,sl);if(gr===null)return;var cur=s.sel[gr]||[];if(multi){if(cur.indexOf(sl)<0)cur=cur.concat([sl])}else{cur=[sl]}s.sel[gr]=cur;segna(id,gr,cur);fatto=true});'
            . 'if(fatto){s.pag=1;aggiorna(g,true)}return fatto}'
            . 'document.addEventListener("click",function(e){var b=e.target.closest(".olo-pg-filtri button");if(b){var box=b.closest(".olo-pg-filtri");filtra(box.getAttribute("data-per"),b.getAttribute("data-filtro"),box.getAttribute("data-gruppo")||"");return}'
            . 'var z=e.target.closest(".olo-pg-azzera");if(z){var zi=z.getAttribute("data-per"),zg=G(zi);if(!zg)return;var zs=S(zg);Object.keys(zs.sel).forEach(function(k){zs.sel[k]=[];segna(zi,k,[])});zs.pag=1;link(zg);aggiorna(zg,true);return}'
            . 'var p=e.target.closest(".olo-pg-piu button");if(!p)return;var g=G(p.parentNode.getAttribute("data-per"));if(!g)return;var s=S(g);'
            . 's.pag=p.hasAttribute("data-pagina")?parseInt(p.getAttribute("data-pagina"),10):s.pag+1;aggiorna(g,true);'
            . 'if(p.hasAttribute("data-pagina"))g.scrollIntoView({behavior:"smooth",block:"start"})});'
            . 'document.addEventListener("change",function(e){var t=e.target;if(t.classList.contains("olo-pg-filtri-menu"))filtra(t.getAttribute("data-per"),t.value,t.getAttribute("data-gruppo")||"");'
            . 'if(t.classList.contains("olo-pg-ordina")){var g=G(t.getAttribute("data-per"));if(g){var so=S(g);so.ord=t.value;so.caso=null;aggiorna(g,true)}}});'
            . 'var tmr;document.addEventListener("input",function(e){var t=e.target;if(!t.classList.contains("olo-pg-cerca"))return;clearTimeout(tmr);'
            . 'tmr=setTimeout(function(){var g=G(t.getAttribute("data-per"));if(g){var s=S(g);s.q=t.value;s.pag=1;aggiorna(g,true)}},160)});'
            . 'function scorrendo(g,id){if(!window.IntersectionObserver)return;var bt=document.querySelector(".olo-pg-piu[data-per=\""+id+"\"] button");if(!bt)return;'
            . 'var io=new IntersectionObserver(function(en){en.forEach(function(x){if(!x.isIntersecting)return;if(bt.hidden)return;var s=S(g);s.pag++;aggiorna(g,true);'
            . 'setTimeout(function(){io.unobserve(bt);io.observe(bt)},450)})},{rootMargin:"0px 0px 240px 0px"});io.observe(bt)}'
            . 'function avvia(r){(r||document).querySelectorAll("[data-pg-id]").forEach(function(g){if(g.__pgB)return;g.__pgB=1;var id=g.getAttribute("data-pg-id");'
            . 'if(g.hasAttribute("data-pg-mescola")){S(g).ord="caso";var om=document.querySelector(".olo-pg-ordina[data-per=\""+id+"\"]");if(om)om.value="caso"}'
            . 'var dalLink=g.hasAttribute("data-pg-link")?daHash(g):false;if(!dalLink)aggiorna(g,false);'
            . 'if(g.getAttribute("data-pg-piu")==="infinito")scorrendo(g,id)})}'
            . 'if(document.readyState==="loading")document.addEventListener("DOMContentLoaded",function(){avvia()});else avvia();'
            . 'document.addEventListener("olo:lazy-hydrated",function(e){avvia(e.detail?e.detail.target:null)});'
            . 'window.addEventListener("hashchange",function(){document.querySelectorAll("[data-pg-link]").forEach(function(g){daHash(g)})});'
            . '})();</script>';
    }

    /** Gli schemi in cui trovano posto le card di solo testo (le altre disposizioni le saltano). */
    const SCHEMI_TESTO = [ 'grid', 'masonry', 'metro', 'mosaic', 'collage', 'diagonal', 'elenco' ];

    /** Una voce di solo testo (citazione, annuncio): niente foto, i testi su un fondo colorato. */
    private function is_testo_item( $img ) {
        return is_array( $img ) && ( $img['type'] ?? '' ) === 'testo';
    }

    /**
     * Gruppo e valore di una categoria. «Tipo: Guida» appartiene al gruppo «Tipo» (filtri che si combinano),
     * «Guida» al gruppo senza nome, come prima.
     *
     * @param string $c la categoria scritta sotto la foto
     * @return array [ slug del gruppo, nome del gruppo, slug, nome ]
     */
    private function categoria_parti( $c ) {
        if ( preg_match( '/^([^:]{1,40}):\s*(.+)$/u', $c, $m ) ) {
            $gn   = trim( $m[1] );
            $nome = trim( $m[2] );
            $gs   = sanitize_title( $gn );
            if ( '' !== $gs && '' !== $nome ) {
                return [ $gs, $gn, $gs . '--' . sanitize_title( $nome ), $nome ];
            }
        }
        return [ '', '', sanitize_title( $c ), $c ];
    }

    /**
     * I gruppi di filtri delle foto: [ slug del gruppo => [ nome, voci [ slug => nome ], conti [ slug => n ] ] ].
     * Le categorie senza gruppo vengono per prime.
     */
    private function gruppi_filtri( $imgs ) {
        $g = [];
        foreach ( $imgs as $img ) {
            if ( ! is_array( $img ) ) {
                continue;
            }
            foreach ( array_unique( array_map( 'trim', explode( ',', (string) ( $img['category'] ?? '' ) ) ) ) as $c ) {
                if ( '' === $c ) {
                    continue;
                }
                list( $gs, $gn, $slug, $nome ) = $this->categoria_parti( $c );
                if ( ! isset( $g[ $gs ] ) ) {
                    $g[ $gs ] = [ 'nome' => $gn, 'voci' => [], 'conti' => [], 'colori' => [] ];
                }
                $g[ $gs ]['voci'][ $slug ]  = $nome;
                $g[ $gs ]['conti'][ $slug ] = ( $g[ $gs ]['conti'][ $slug ] ?? 0 ) + 1;
                $col = $this->safe_color_css( (string) ( $img['colore'] ?? '' ) );
                if ( $col ? empty( $g[ $gs ]['colori'][ $slug ] ) : false ) {
                    $g[ $gs ]['colori'][ $slug ] = $col;
                }
            }
        }
        if ( isset( $g[''] ) ) {
            $g = [ '' => $g[''] ] + $g;
        }
        return $g;
    }

    /**
     * Gli attributi di una voce: categorie per i filtri, testi per ricerca e ordinamento, pagina iniziale, misura
     * nelle griglie senza buchi e colore della voce.
     *
     * @param array|string $img la voce
     * @param int          $i   la posizione (1, 2, …)
     * @param array        $c   [ filtri, dati, piu, per, pd, pm ]
     * @return array [ attr, classe, vars ]
     */
    private function voce_attr( $img, $i, $c ) {
        $attr   = '';
        $classe = '';
        $vars   = [];
        if ( $c['filtri'] ) {
            $attr .= ' data-cat="' . esc_attr( implode( ' ', array_keys( $this->categorie_voce( $img ) ) ) ) . '"';
        }
        if ( $c['dati'] && is_array( $img ) ) {
            $tit   = trim( str_replace( '*', '', (string) ( $img['caption'] ?? '' ) ) );
            $txt   = trim( (string) ( $img['text'] ?? '' ) );
            $cerca = implode( ' ', array_filter( [ $tit, (string) ( $img['subtitle'] ?? '' ), $txt, (string) ( $img['nota'] ?? '' ), (string) ( $img['category'] ?? '' ), (string) ( $img['alt'] ?? '' ) ] ) );
            $attr .= ' data-titolo="' . esc_attr( $tit ) . '" data-testo3="' . esc_attr( $txt ) . '" data-cerca="' . esc_attr( $cerca ) . '"';
        }
        if ( '' !== $c['piu'] && $i > $c['per'] ) {
            $classe .= ' olo-pg-pag';
        }
        if ( $c['pd'] && isset( $c['pd'][ $i - 1 ] ) ) {
            $vars[] = '--pw:' . (int) $c['pd'][ $i - 1 ][0] . ';--ph:' . (int) $c['pd'][ $i - 1 ][1]
                . ';--pwm:' . (int) $c['pm'][ $i - 1 ][0] . ';--phm:' . (int) $c['pm'][ $i - 1 ][1];
        }
        $col = is_array( $img ) ? $this->safe_color_css( (string) ( $img['colore'] ?? '' ) ) : '';
        if ( $col ) {
            $vars[] = '--pg-c:' . $col . ';--pg-cf:' . $this->testo_su( $col );
        }
        if ( is_array( $img ) && ! empty( $img['riflesso'] ) ) {
            // La carta olografica: l'etichetta della nota e il filo prendono i colori cangianti
            $classe .= ' olo-pg-holo';
        }
        return [ 'attr' => $attr, 'classe' => $classe, 'vars' => implode( ';', $vars ) ];
    }

    /** Il colore del testo sopra un fondo: chiaro sui fondi scuri, scuro sui fondi chiari (i token restano chiari). */
    private function testo_su( $col ) {
        if ( preg_match( '/^#([0-9a-f]{3}|[0-9a-f]{6})$/i', (string) $col, $m ) ) {
            $h = 3 === strlen( $m[1] ) ? preg_replace( '/(.)/', '$1$1', $m[1] ) : $m[1];
            $l = ( 0.2126 * hexdec( substr( $h, 0, 2 ) ) + 0.7152 * hexdec( substr( $h, 2, 2 ) ) + 0.0722 * hexdec( substr( $h, 4, 2 ) ) ) / 255;
            return $l > 0.62 ? 'var(--olo-color-dark)' : '#fff';
        }
        return '#fff';
    }

    /**
     * Come testo_su(), da mettere dopo «color:» in fondo a una regola. Con un token del tema (var(--olo-color-text), il verde
     * chiaro di un'aurora) il PHP non sa se il colore è chiaro: il bianco resta per i browser vecchi, poi il colore relativo
     * sceglie da sé nero o bianco dalla luminosità vera (24 nov 2026: il filtro attivo «Tutta la tavolozza» era chiaro su chiaro).
     */
    private function testo_su_dich( $col ) {
        if ( preg_match( '/^#([0-9a-f]{3}|[0-9a-f]{6})$/i', (string) $col ) ) {
            return $this->testo_su( $col );
        }
        return '#fff;color:oklch(from ' . $col . ' clamp(0,(0.7 - l) * 100,1) 0 0)';
    }

    /**
     * LA CARD DI SOLO TESTO: categoria e numero in alto, il testo grande (una citazione, un annuncio), la firma e una
     * riga piccola. Con un link porta lì, con la freccia; senza resta un riquadro fra le foto.
     */
    private function card_testo_html( $img, $i, $sk, $va ) {
        $tit  = trim( (string) ( $img['caption'] ?? '' ) );
        $sot  = trim( (string) ( $img['subtitle'] ?? '' ) );
        $txt  = trim( (string) ( $img['text'] ?? '' ) );
        $cat  = implode( ' · ', $this->categorie_voce( $img ) );
        $link = esc_url( trim( (string) ( $img['link'] ?? '' ) ) );
        $tag  = $link ? 'a' : 'div';
        $cl   = 'olo-pg-item olo-pg-testo' . ( $link ? ' olo-pg-link' : '' ) . $va['classe'];
        $st   = '' !== $va['vars'] ? ' style="' . esc_attr( $va['vars'] ) . '"' : '';
        $h    = '<' . $tag . ' class="' . esc_attr( $cl ) . '"' . ( $link ? ' href="' . $link . '"' : '' ) . $va['attr'] . $st . '>';
        if ( 'elenco' === $sk['modo'] ) {
            return $h . $this->scheda_html( $img, $i, $sk ) . '</' . $tag . '>';
        }
        $k    = 0;
        $alto = '';
        if ( '' !== $cat ) {
            $alto .= '<span class="olo-pg-tx-cat olo-pg-a" style="--k:' . ( $k++ ) . '">' . esc_html( $cat ) . '</span>';
        }
        if ( ! empty( $sk['numero'] ) ) {
            $alto .= '<span class="olo-pg-tx-num olo-pg-a" style="--k:' . ( $k++ ) . '">' . esc_html( $this->numero_scheda( $i, $sk ) ) . '</span>';
        }
        $corpo = '';
        if ( '' !== $tit ) {
            $corpo .= '<p class="olo-pg-tx-tit olo-pg-a" style="--k:' . ( $k++ ) . '">' . $this->corsivo_html( $tit ) . '</p>';
        }
        if ( '' !== $sot ) {
            $corpo .= '<span class="olo-pg-tx-sot olo-pg-a" style="--k:' . ( $k++ ) . '">' . esc_html( $sot ) . '</span>';
        }
        if ( '' !== $txt ) {
            $corpo .= '<span class="olo-pg-tx-txt olo-pg-a" style="--k:' . ( $k++ ) . '">' . esc_html( $txt ) . '</span>';
        }
        $ico = $link ? $this->scheda_icona( $sk['icona'] ? $sk['icona'] : 'freccia' ) : '';
        return $h . '<div class="olo-pg-tx">' . ( '' !== $alto ? '<div class="olo-pg-tx-alto">' . $alto . '</div>' : '' )
            . '<div class="olo-pg-tx-corpo">' . $corpo . '</div>' . $ico . '</div></' . $tag . '>';
    }

    /** Il CSS delle card di solo testo: il fondo della card o della voce, il testo grande nel carattere dei titoli. */
    private function testi_css( $uid, $s, $radius, $item_box, $layout, $sk ) {
        if ( 'elenco' === $layout ) {
            return '';
        }
        $u    = '.' . $uid;
        $bgc  = $this->safe_color_css( $s['testo_sfondo'] ?? '' );
        $bg   = $bgc ? $bgc : 'var(--olo-color-primary)';
        $cf   = $bgc ? $this->testo_su( $bgc ) : '#fff';
        $font = $this->resolve_font_family( ( $s['scheda_font'] ?? '' ) ? $s['scheda_font'] : 'heading' );
        $css  = "{$u} .olo-pg-item.olo-pg-testo{position:relative;display:flex;overflow:hidden;border-radius:{$radius};background:var(--pg-c,{$bg});color:var(--pg-cf,{$cf});text-decoration:none;padding:0}";
        $css .= "{$u} .olo-pg-testo:hover,{$u} .olo-pg-testo:focus{color:var(--pg-cf,{$cf});text-decoration:none}";
        $css .= "{$u} .olo-pg-testo:focus-visible{outline:2px solid currentColor;outline-offset:-.3rem}";
        $css .= "{$u} .olo-pg-tx{display:flex;flex-direction:column;gap:.75rem;width:100%;padding:clamp(1rem,2.2vw,1.75rem)}";
        $css .= "{$u} .olo-pg-tx-alto{display:flex;justify-content:space-between;align-items:flex-start;gap:.5rem;font-size:10px;font-weight:600;letter-spacing:.14em;text-transform:uppercase}";
        $css .= "{$u} .olo-pg-tx-num{margin-left:auto;font-variant-numeric:tabular-nums;opacity:.8}";
        $css .= "{$u} .olo-pg-tx-corpo{display:flex;flex-direction:column;gap:.5rem;margin:auto 0}";
        $peso = preg_match( '/^[1-9]00$/', (string) ( $s['scheda_peso'] ?? '' ) ) ? $s['scheda_peso'] : '600';
        $css .= "{$u} .olo-pg-tx-tit{margin:0;font-family:{$font};font-size:clamp(20px,2.1vw,30px);line-height:1.15;font-weight:{$peso};letter-spacing:-.01em;text-wrap:balance}";
        $css .= "{$u} .olo-pg-tx-sot{font-size:13px;line-height:1.4;opacity:.85}";
        $css .= "{$u} .olo-pg-tx-txt{font-size:12px;line-height:1.45;opacity:.7}";
        $css .= "{$u} .olo-pg-testo .olo-pg-sk-ico{align-self:flex-end}";
        $css .= "{$u} .olo-pg-testo::before{display:none}";
        if ( in_array( $layout, [ 'grid', 'diagonal' ], true ) && ! in_array( $sk['modo'], [ 'sotto', 'biglietto' ], true ) ) {
            $css .= "{$u} .olo-pg-item.olo-pg-testo{" . $item_box . '}';
        } else {
            $css .= "{$u} .olo-pg-item.olo-pg-testo{height:auto!important;aspect-ratio:auto!important;min-height:12rem}";
        }
        return $css;
    }

    /**
     * L'ENTRATA DEI TESTI, uno alla volta: ogni parte della scheda (categoria, numero, titolo, sottotitolo, testo,
     * pulsante) arriva col suo ritardo quando la foto entra nello schermo, o al passaggio se i testi compaiono così.
     * Senza script i testi restano visibili; con «riduci movimento» non si muove niente.
     */
    private function anima_css( $uid, $s, $sk ) {
        $a = in_array( $s['scheda_anima'] ?? '', [ 'sale', 'lato', 'maschera', 'scala', 'sfuma' ], true ) ? $s['scheda_anima'] : '';
        if ( '' === $a ) {
            return '';
        }
        $u    = '.' . $uid;
        $kf   = [
            'sale'     => 'from{opacity:0;transform:translateY(1.1em)}',
            'lato'     => 'from{opacity:0;transform:translateX(-1.4em)}',
            'maschera' => 'from{clip-path:inset(0 0 100% 0);transform:translateY(.5em)}',
            'scala'    => 'from{opacity:0;transform:scale(.82)}',
            'sfuma'    => 'from{opacity:0}',
        ];
        $nome = 'olo-pg-a-' . $a;
        $anim = "animation:{$nome} .7s cubic-bezier(.2,.8,.2,1) backwards;animation-delay:calc(var(--k,0) * 90ms + 80ms)";
        $nasc = 'maschera' === $a ? 'clip-path:inset(0 0 100% 0)' : 'opacity:0';
        $css  = "@keyframes {$nome}{" . $kf[ $a ] . '}@media (prefers-reduced-motion:no-preference){';
        if ( $sk['passaggio'] ) {
            $css .= "@media (hover:hover){{$u} .olo-pg-item:hover .olo-pg-a,{$u} .olo-pg-item:focus-visible .olo-pg-a{{$anim}}}";
            $css .= "@media (hover:none){{$u}.olo-pg-anima-pronto .olo-pg-item:not(.olo-pg-in) .olo-pg-a{{$nasc}}{$u}.olo-pg-anima-pronto .olo-pg-in .olo-pg-a{{$anim}}}";
        } else {
            $css .= "{$u}.olo-pg-anima-pronto .olo-pg-item:not(.olo-pg-in) .olo-pg-a{{$nasc}}{$u}.olo-pg-anima-pronto .olo-pg-in .olo-pg-a{{$anim}}";
        }
        return $css . '}';
    }

    /** Lo script dell'entrata dei testi, una volta per pagina: segna le voci che entrano nello schermo. */
    /**
     * Il pulsante di pausa dei video che partono da soli: ferma e fa ripartire il video della sua foto, senza aprire la
     * lightbox (la foto può essere un link). Con «riduci movimento» del sistema il video parte già fermo. Fermato a mano
     * perde data-olo-autoplay, così olo-lazy-video.js non lo riavvia quando rientra nello schermo. Una volta per pagina.
     */
    private function pausa_js() {
        static $fatto = false;
        if ( $fatto ) {
            return '';
        }
        $fatto = true;
        return '<style>.olo-pg-pausa{position:absolute;top:.85rem;left:.85rem;z-index:6;width:2.25rem;height:2.25rem;border-radius:50%;'
            . 'border:2px solid #fff;background:rgb(0 0 0 / .45);cursor:pointer;display:flex;align-items:center;justify-content:center;'
            . '-webkit-backdrop-filter:blur(4px);backdrop-filter:blur(4px);transition:transform .2s ease}'
            . '.olo-pg-pausa-ad{left:auto;right:.85rem}.olo-pg-pausa-bs{top:auto;bottom:.85rem}.olo-pg-pausa-bd{top:auto;bottom:.85rem;left:auto;right:.85rem}'
            . '.olo-pg-pausa:hover{transform:scale(1.06)}.olo-pg-pausa:focus-visible{outline:2px solid #fff;outline-offset:3px}'
            . '.olo-pg-pausa::before{content:"";width:.7rem;height:.8rem;border-left:.22rem solid #fff;border-right:.22rem solid #fff;box-sizing:border-box}'
            . '.olo-pg-pausa[aria-pressed="true"]::before{width:0;height:0;border:0;border-left:.62rem solid #fff;border-top:.4rem solid transparent;border-bottom:.4rem solid transparent;margin-left:.15rem}</style>'
            . '<script>(function(){if(window.oloPgPausa)return;window.oloPgPausa=1;'
            . 'function video(b){var it=b.closest(".olo-pg-item");return it?it.querySelector("video"):null}'
            . 'function segna(b,ferma){b.setAttribute("aria-pressed",ferma?"true":"false");b.setAttribute("aria-label",b.getAttribute(ferma?"data-riprendi":"data-pausa"))}'
            . 'function premi(e){var b=e.target.closest(".olo-pg-pausa");if(!b)return;e.preventDefault();e.stopPropagation();var v=video(b);if(!v)return;'
            . 'if(v.paused){v.setAttribute("data-olo-autoplay","");v.play();segna(b,false)}else{v.removeAttribute("data-olo-autoplay");v.pause();segna(b,true)}}'
            . 'document.addEventListener("click",premi,true);'
            . 'function segui(e){var v=e.target;if(v.tagName!=="VIDEO")return;var it=v.closest(".olo-pg-item");var b=it?it.querySelector(".olo-pg-pausa"):null;if(b)segna(b,v.paused)}'
            . 'document.addEventListener("play",segui,true);document.addEventListener("pause",segui,true);'
            . 'document.addEventListener("keydown",function(e){if(e.key!=="Enter"?e.key!==" ":false)return;premi(e)},true);'
            . 'function fermi(r){var tutti=(r||document).querySelectorAll(".olo-pg-pausa");'
            . 'if(window.matchMedia("(prefers-reduced-motion: reduce)").matches){tutti.forEach(function(b){var v=video(b);if(v){v.removeAttribute("data-olo-autoplay");v.pause();segna(b,true)}});return}'
            . 'setTimeout(function(){tutti.forEach(function(b){var v=video(b);if(v)segna(b,v.paused)})},1500)}'
            . 'if(document.readyState==="loading")document.addEventListener("DOMContentLoaded",function(){fermi()});else fermi();'
            . 'document.addEventListener("olo:lazy-hydrated",function(e){fermi(e.detail?e.detail.target:null)});'
            . '})();</script>';
    }

    private function anima_js() {
        static $fatto = false;
        if ( $fatto ) {
            return '';
        }
        $fatto = true;
        return '<script>(function(){if(window.oloPgAnima)return;window.oloPgAnima=1;'
            . 'function via(r){if(!window.IntersectionObserver)return;(r||document).querySelectorAll(".olo-pg-anima").forEach(function(g){if(g.__pgA)return;g.__pgA=1;g.classList.add("olo-pg-anima-pronto");'
            . 'var io=new IntersectionObserver(function(en){en.forEach(function(x){if(!x.isIntersecting)return;x.target.classList.add("olo-pg-in");io.unobserve(x.target)})},{threshold:.2});'
            . 'g.querySelectorAll(".olo-pg-item").forEach(function(it){io.observe(it)})})}'
            . 'if(document.readyState==="loading")document.addEventListener("DOMContentLoaded",function(){via()});else via();'
            . 'document.addEventListener("olo:lazy-hydrated",function(e){via(e.detail?e.detail.target:null)});})();</script>';
    }

    /**
     * LE FOTO DA UN SOCIAL. YouTube (canale, @nome o playlist), Vimeo (utente o canale), Flickr (utente o tag),
     * Pinterest (profilo o bacheca) e i feed RSS si leggono dai loro feed pubblici, senza chiavi; Instagram passa
     * dall'account collegato in OLOsocial. Il risultato si tiene un'ora; se la fonte non risponde resta l'ultimo.
     *
     * @param array $s impostazioni
     * @return array|null null = nessuna fonte social; [] = la fonte non ha dato niente
     */
    private function feed_social( $s ) {
        $fonte = in_array( $s['social'] ?? '', [ 'youtube', 'vimeo', 'flickr', 'pinterest', 'instagram', 'facebook', 'rss' ], true ) ? $s['social'] : '';
        $id    = trim( (string) ( $s['social_fonte'] ?? '' ) );
        if ( '' === $fonte ) {
            return null;
        }
        if ( '' === $id && ! in_array( $fonte, [ 'instagram', 'facebook' ], true ) ) {
            return [];
        }
        $n    = absint( $s['social_quante'] ?? 12 );
        $n    = max( 1, min( 50, $n ? $n : 12 ) );
        $link = ! empty( $s['social_link'] );
        $key  = 'olo_pg_social_' . md5( $fonte . '|' . $id . '|' . $n . '|' . ( $link ? 1 : 0 ) );
        $c    = get_transient( $key );
        if ( is_array( $c ) ) {
            return $c;
        }
        try {
            $voci = call_user_func( [ $this, 'social_' . $fonte ], $id, $n );
        } catch ( \Throwable $e ) {
            $voci = [];
        }
        if ( $voci ) {
            foreach ( $voci as $k => $v ) {
                if ( ! $link ) {
                    unset( $voci[ $k ]['link'] );
                }
            }
            $voci = array_values( array_slice( $voci, 0, $n ) );
            set_transient( $key, $voci, (int) apply_filters( 'olobuild_social_durata', HOUR_IN_SECONDS, $fonte ) );
            update_option( $key . '_ultimo', $voci, false );
            return $voci;
        }
        $voci = get_option( $key . '_ultimo', [] );
        $voci = is_array( $voci ) ? $voci : [];
        set_transient( $key, $voci, 15 * MINUTE_IN_SECONDS );
        return $voci;
    }

    /** Una pagina pubblica (solo indirizzi esterni, wp_safe_remote_get). */
    private function social_get( $url ) {
        $h = [ 'Accept-Language' => 'it,en;q=0.8' ];
        // Dai server europei YouTube risponde con la pagina del consenso: il cookie SOCS=CAI dice «solo i cookie
        // necessari» e porta alla pagina vera (serve per trovare il canale di un @nome).
        if ( false !== strpos( (string) wp_parse_url( $url, PHP_URL_HOST ), 'youtube.com' ) ) {
            $h['Cookie'] = 'SOCS=CAI';
        }
        $r = wp_safe_remote_get( $url, [ 'timeout' => 8, 'redirection' => 3, 'user-agent' => 'Mozilla/5.0 (compatible; OLObuild)', 'headers' => $h ] );
        if ( is_wp_error( $r ) || 200 !== (int) wp_remote_retrieve_response_code( $r ) ) {
            return '';
        }
        return (string) wp_remote_retrieve_body( $r );
    }

    /** Un feed XML letto senza rete né entità esterne. */
    private function social_xml( $body ) {
        if ( '' === $body || ! function_exists( 'simplexml_load_string' ) ) {
            return null;
        }
        $prima = libxml_use_internal_errors( true );
        $x     = simplexml_load_string( $body, 'SimpleXMLElement', LIBXML_NONET | LIBXML_NOCDATA );
        libxml_clear_errors();
        libxml_use_internal_errors( $prima );
        return $x ? $x : null;
    }

    private function social_data( $t ) {
        $t = $t ? strtotime( (string) $t ) : 0;
        return $t ? date_i18n( get_option( 'date_format' ), $t ) : '';
    }

    private function social_testo( $t, $max = 90 ) {
        $t = trim( preg_replace( '/\s+/u', ' ', wp_strip_all_tags( html_entity_decode( (string) $t, ENT_QUOTES, 'UTF-8' ) ) ) );
        return mb_strlen( $t ) > $max ? rtrim( mb_substr( $t, 0, $max - 1 ) ) . '…' : $t;
    }

    private function social_youtube( $id, $n ) {
        $q = '';
        if ( preg_match( '/[?&]list=([\w-]{10,})/', $id, $m ) || preg_match( '/^((?:PL|UU|OL|FL)[\w-]{10,})$/', $id, $m ) ) {
            $q = 'playlist_id=' . $m[1];
        } elseif ( preg_match( '/(UC[\w-]{22})/', $id, $m ) ) {
            $q = 'channel_id=' . $m[1];
        } else {
            $h    = preg_match( '#youtube\.com/(@[\w.-]+|c/[\w.-]+|user/[\w.-]+)#', $id, $m ) ? $m[1] : ( '@' === substr( $id, 0, 1 ) ? $id : '@' . $id );
            $h    = preg_replace( '#[^\w@./-]#', '', $h );
            $page = $this->social_get( 'https://www.youtube.com/' . ltrim( $h, '/' ) );
            if ( preg_match( '/"(?:channelId|externalId)":"(UC[\w-]{22})"/', $page, $m ) || preg_match( '#/channel/(UC[\w-]{22})#', $page, $m ) ) {
                $q = 'channel_id=' . $m[1];
            }
        }
        if ( '' === $q ) {
            return [];
        }
        $x = $this->social_xml( $this->social_get( 'https://www.youtube.com/feeds/videos.xml?' . $q ) );
        if ( ! $x ) {
            return [];
        }
        $out = [];
        foreach ( $x->entry as $e ) {
            $vid = (string) $e->children( 'http://www.youtube.com/xml/schemas/2015' )->videoId;
            if ( ! preg_match( '/^[\w-]{6,20}$/', $vid ) ) {
                continue;
            }
            $tit   = $this->social_testo( (string) $e->title );
            $out[] = [ 'type' => 'video', 'url' => '', 'embed' => 'https://www.youtube.com/watch?v=' . $vid, 'poster' => 'https://i.ytimg.com/vi/' . $vid . '/hqdefault.jpg',
                'caption' => $tit, 'alt' => $tit, 'subtitle' => $this->social_data( (string) $e->published ), 'link' => 'https://www.youtube.com/watch?v=' . $vid ];
            if ( count( $out ) >= $n ) {
                break;
            }
        }
        return $out;
    }

    private function social_vimeo( $id, $n ) {
        $p = preg_match( '#vimeo\.com/(channels/[\w-]+|groups/[\w-]+|[\w-]+)#', $id, $m ) ? $m[1] : trim( $id, '/ ' );
        if ( ! preg_match( '#^[\w-]+(/[\w-]+)?$#', $p ) ) {
            return [];
        }
        $x = $this->social_xml( $this->social_get( 'https://vimeo.com/' . $p . '/videos/rss' ) );
        if ( ! $x || ! isset( $x->channel->item ) ) {
            return [];
        }
        $mrss = 'http://search.yahoo.com/mrss/';
        $out  = [];
        foreach ( $x->channel->item as $it ) {
            if ( ! preg_match( '#vimeo\.com/(\d+)#', (string) $it->link, $mm ) ) {
                continue;
            }
            $th    = '';
            $dur   = '';
            $media = $it->children( $mrss );
            if ( isset( $media->content ) ) {
                $sec = (int) $media->content->attributes()->duration;
                if ( $sec > 0 ) {
                    $dur = $this->durata_testo( $sec );
                }
                $mc = $media->content->children( $mrss );
                if ( isset( $mc->thumbnail ) ) {
                    $th = (string) $mc->thumbnail->attributes()->url;
                }
            }
            if ( '' === $th && isset( $media->thumbnail ) ) {
                $th = (string) $media->thumbnail->attributes()->url;
            }
            if ( '' === $th && preg_match( '/<img[^>]+src="([^"]+)"/i', (string) $it->description, $im ) ) {
                $th = html_entity_decode( $im[1] );
            }
            $tit   = $this->social_testo( (string) $it->title );
            $out[] = [ 'type' => 'video', 'url' => '', 'embed' => 'https://vimeo.com/' . $mm[1], 'poster' => esc_url_raw( $th ), 'durata' => $dur,
                'caption' => $tit, 'alt' => $tit, 'subtitle' => $this->social_data( (string) $it->pubDate ), 'link' => 'https://vimeo.com/' . $mm[1] ];
            if ( count( $out ) >= $n ) {
                break;
            }
        }
        return $out;
    }

    private function social_flickr( $id, $n ) {
        $q = [ 'format' => 'json', 'nojsoncallback' => 1 ];
        if ( preg_match( '/(\d+@N\d+)/', $id, $m ) ) {
            $q['id'] = $m[1];
        } else {
            $q['tags'] = implode( ',', array_filter( array_map( 'sanitize_title', preg_split( '/[\s,#]+/u', $id ) ) ) );
            if ( '' === $q['tags'] ) {
                return [];
            }
        }
        $body = str_replace( "\\'", "'", $this->social_get( add_query_arg( $q, 'https://www.flickr.com/services/feeds/photos_public.gne' ) ) );
        $j    = json_decode( $body, true );
        if ( empty( $j['items'] ) || ! is_array( $j['items'] ) ) {
            return [];
        }
        $out = [];
        foreach ( $j['items'] as $it ) {
            $m = (string) ( $it['media']['m'] ?? '' );
            if ( '' === $m ) {
                continue;
            }
            $tit   = $this->social_testo( $it['title'] ?? '' );
            $out[] = [ 'url' => esc_url_raw( str_replace( '_m.', '_b.', $m ) ), 'alt' => $tit, 'caption' => $tit,
                'subtitle' => $this->social_data( $it['date_taken'] ?? '' ), 'link' => esc_url_raw( (string) ( $it['link'] ?? '' ) ) ];
            if ( count( $out ) >= $n ) {
                break;
            }
        }
        return $out;
    }

    private function social_pinterest( $id, $n ) {
        $p = preg_match( '#pinterest\.[a-z.]+/([\w.-]+(?:/[\w.-]+)?)#', $id, $m ) ? $m[1] : trim( $id, '/@ ' );
        if ( ! preg_match( '#^[\w.-]+(/[\w.-]+)?$#', $p ) ) {
            return [];
        }
        $url = false === strpos( $p, '/' ) ? 'https://www.pinterest.com/' . $p . '/feed.rss' : 'https://www.pinterest.com/' . $p . '.rss';
        $x   = $this->social_xml( $this->social_get( $url ) );
        if ( ! $x || ! isset( $x->channel->item ) ) {
            return [];
        }
        $out = [];
        foreach ( $x->channel->item as $it ) {
            if ( ! preg_match( '/<img[^>]+src="([^"]+)"/i', (string) $it->description, $im ) ) {
                continue;
            }
            $tit   = $this->social_testo( (string) $it->title );
            $out[] = [ 'url' => esc_url_raw( str_replace( '/236x/', '/736x/', html_entity_decode( $im[1] ) ) ), 'alt' => $tit, 'caption' => $tit,
                'subtitle' => $this->social_data( (string) $it->pubDate ), 'link' => esc_url_raw( (string) $it->link ) ];
            if ( count( $out ) >= $n ) {
                break;
            }
        }
        return $out;
    }

    private function social_rss( $id, $n ) {
        $url = esc_url_raw( $id );
        if ( ! $url || ! wp_http_validate_url( $url ) ) {
            return [];
        }
        $x = $this->social_xml( $this->social_get( $url ) );
        if ( ! $x ) {
            return [];
        }
        $voci = isset( $x->channel->item ) ? $x->channel->item : ( isset( $x->entry ) ? $x->entry : [] );
        $mrss = 'http://search.yahoo.com/mrss/';
        $out  = [];
        foreach ( $voci as $it ) {
            $img = '';
            if ( isset( $it->enclosure ) ) {
                $a = $it->enclosure->attributes();
                if ( 0 === strpos( (string) $a->type, 'image' ) ) {
                    $img = (string) $a->url;
                }
            }
            $media = $it->children( $mrss );
            if ( '' === $img && isset( $media->content ) ) {
                $a = $media->content->attributes();
                $t = (string) $a->medium . (string) $a->type;
                if ( '' === $t || false !== strpos( $t, 'image' ) ) {
                    $img = (string) $a->url;
                }
            }
            if ( '' === $img && isset( $media->thumbnail ) ) {
                $img = (string) $media->thumbnail->attributes()->url;
            }
            if ( '' === $img ) {
                $html = (string) $it->description . (string) $it->children( 'http://purl.org/rss/1.0/modules/content/' )->encoded . (string) $it->content . (string) $it->summary;
                if ( preg_match( '/<img[^>]+src=["\']([^"\']+)/i', $html, $im ) ) {
                    $img = html_entity_decode( $im[1] );
                }
            }
            if ( '' === $img ) {
                continue;
            }
            $lnk = (string) $it->link;
            if ( '' === trim( $lnk ) && isset( $it->link ) ) {
                $lnk = (string) $it->link->attributes()->href;
            }
            $d = (string) $it->pubDate;
            if ( '' === $d ) {
                $d = (string) $it->published;
            }
            if ( '' === $d ) {
                $d = (string) $it->updated;
            }
            $tit   = $this->social_testo( (string) $it->title );
            $out[] = [ 'url' => esc_url_raw( $img ), 'alt' => $tit, 'caption' => $tit, 'subtitle' => $this->social_data( $d ), 'link' => esc_url_raw( trim( $lnk ) ) ];
            if ( count( $out ) >= $n ) {
                break;
            }
        }
        return $out;
    }

    /** L'account di OLOsocial di una rete (per @nome o per nome; vuoto = il primo attivo) e il suo token. */
    private function social_account( $rete, $id ) {
        if ( ! class_exists( 'Olo_Social_Accounts' ) || ! class_exists( 'Olo_Social_Provider_Meta' ) ) {
            return null;
        }
        $h = strtolower( ltrim( trim( $id ), '@' ) );
        foreach ( (array) Olo_Social_Accounts::all( [ 'status' => 'active', 'network' => $rete ] ) as $a ) {
            $a = (array) $a;
            if ( '' === $h || strtolower( ltrim( (string) ( $a['handle'] ?? '' ), '@' ) ) === $h || strtolower( (string) ( $a['name'] ?? '' ) ) === $h ) {
                $tok = empty( $a['external_id'] ) ? '' : (string) Olo_Social_Accounts::token( $a );
                return '' === $tok ? null : [ $a, $tok ];
            }
        }
        return null;
    }

    private function social_facebook( $id, $n ) {
        $at = $this->social_account( 'facebook', $id );
        if ( ! $at ) {
            return [];
        }
        $url = add_query_arg( [ 'fields' => 'full_picture,message,permalink_url,created_time', 'limit' => min( 100, $n * 2 ), 'access_token' => rawurlencode( $at[1] ) ],
            Olo_Social_Provider_Meta::graph_url( $at[0]['external_id'] . '/posts' ) );
        $j   = json_decode( $this->social_get( $url ), true );
        if ( empty( $j['data'] ) || ! is_array( $j['data'] ) ) {
            return [];
        }
        $out = [];
        foreach ( $j['data'] as $it ) {
            if ( empty( $it['full_picture'] ) ) {
                continue;
            }
            $tit   = $this->social_testo( $it['message'] ?? '', 80 );
            $out[] = [ 'url' => esc_url_raw( $it['full_picture'] ), 'alt' => $tit, 'caption' => $tit, 'subtitle' => $this->social_data( $it['created_time'] ?? '' ), 'link' => esc_url_raw( (string) ( $it['permalink_url'] ?? '' ) ) ];
            if ( count( $out ) >= $n ) {
                break;
            }
        }
        return $out;
    }

    private function social_instagram( $id, $n ) {
        $at = $this->social_account( 'instagram', $id );
        if ( ! $at ) {
            return [];
        }
        list( $acc, $tok ) = $at;
        $url = add_query_arg( [ 'fields' => 'id,caption,media_type,media_url,thumbnail_url,permalink,timestamp', 'limit' => $n, 'access_token' => rawurlencode( $tok ) ],
            Olo_Social_Provider_Meta::graph_url( $acc['external_id'] . '/media' ) );
        $j   = json_decode( $this->social_get( $url ), true );
        if ( empty( $j['data'] ) || ! is_array( $j['data'] ) ) {
            return [];
        }
        $out = [];
        foreach ( $j['data'] as $it ) {
            $tit = $this->social_testo( $it['caption'] ?? '', 80 );
            $lnk = esc_url_raw( (string) ( $it['permalink'] ?? '' ) );
            $sub = $this->social_data( $it['timestamp'] ?? '' );
            if ( 'VIDEO' === ( $it['media_type'] ?? '' ) ) {
                if ( empty( $it['media_url'] ) ) {
                    continue;
                }
                $out[] = [ 'type' => 'video', 'url' => esc_url_raw( $it['media_url'] ), 'poster' => esc_url_raw( (string) ( $it['thumbnail_url'] ?? '' ) ), 'alt' => $tit, 'caption' => $tit, 'subtitle' => $sub, 'link' => $lnk ];
            } elseif ( ! empty( $it['media_url'] ) ) {
                $out[] = [ 'url' => esc_url_raw( $it['media_url'] ), 'alt' => $tit, 'caption' => $tit, 'subtitle' => $sub, 'link' => $lnk ];
            }
            if ( count( $out ) >= $n ) {
                break;
            }
        }
        return $out;
    }
}
