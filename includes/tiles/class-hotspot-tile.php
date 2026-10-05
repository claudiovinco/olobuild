<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Hotspot_Tile extends Olobuild_Tile_Base {

    protected $type     = 'hotspot';
    protected $name     = 'Hotspot';
    protected $icon     = 'dashicons-location-alt';
    protected $category = 'interactive';
    protected $defaults = [
        'preset' => 'custom',
        'image'           => '',
        // Vuoto = il testo alternativo dell'immagine nella libreria media.
        'image_alt'       => '',
        'image_height'    => '400',
        // 'auto' = resa storica: il riquadro tiene l'altezza fissa di image_height.
        'aspect_ratio'        => 'auto',
        'aspect_ratio_custom' => '16/9',
        'object_fit'          => 'cover',
        'object_position' => 'center center',
        // Stesso default del config: 0 = angoli vivi (prima 0 diventava 8px).
        'border_radius'   => '0',
        // Ogni marker può avere anche `link` e `link_label` (facoltativi) e, dalla 1.4.508,
        // `image`: una foto in cima al fumetto (era il motivo d'essere della tile Popover).
        'markers'         => [
            [ 'pos_x' => '30', 'pos_y' => '40', 'title' => 'Punto di interesse', 'description' => 'Descrizione del primo hotspot.', 'icon' => 'pin', 'tooltip_position' => 'top' ],
            [ 'pos_x' => '65', 'pos_y' => '55', 'title' => 'Secondo punto', 'description' => 'Descrizione del secondo hotspot.', 'icon' => 'pin', 'tooltip_position' => 'bottom' ],
        ],
        // 'icon' = l'icona di ogni marker (resa di sempre) · 'number' = cerchio numerato · 'dot' = punto pieno.
        'marker_style'      => 'icon',
        'marker_color'      => '',
        // Colore del numero (solo marker numerati); vuoto = testo su primario.
        'marker_text_color' => '',
        'marker_size'     => '24',
        'pulse_animation' => true,
        'tooltip_bg'      => '',
        'tooltip_color'   => '',
        'tooltip_width'   => '220',
        'border'                  => [],
        'border_hover'            => [],
        'border_hover_duration'   => 300,
        'border_effect'           => 'none',
        'border_effect_intensity' => 'medium',
        'border_effect_color2'    => '',
        'border_effect_angle'     => 135,
        'border_effect_speed'     => 4,
    ];

    /** Uid già dati in questa richiesta: lo stesso nodo reso due volte (un widget ripetuto) resta distinto. */
    private static $uid_usati = [];

    public function get_controls() {
        return [
            [ 'key' => 'image',           'type' => 'image',  'label' => 'Image' ],
            [ 'key' => 'image_height',    'type' => 'range',  'label' => 'Image Height' ],
            [ 'key' => 'markers',         'type' => 'custom', 'label' => 'Markers' ],
            [ 'key' => 'marker_color',    'type' => 'color',  'label' => 'Marker Color' ],
            [ 'key' => 'marker_size',     'type' => 'range',  'label' => 'Marker Size' ],
            [ 'key' => 'pulse_animation', 'type' => 'toggle', 'label' => 'Pulse Animation' ],
            [ 'key' => 'tooltip_bg',      'type' => 'color',  'label' => 'Tooltip Background' ],
            [ 'key' => 'tooltip_color',   'type' => 'color',  'label' => 'Tooltip Text Color' ],
            [ 'key' => 'tooltip_width',   'type' => 'range',  'label' => 'Tooltip Width' ],
        ];
    }

    public function render( $settings ) {
        // Per primo: l'id del nodo in resa (chiave_stabile) vale finché non si rende altro.
        // Stabile fra un render e l'altro, così nel canvas il render di una sola tile non
        // riusa la classe di un altro hotspot già nella pagina (wp_unique_id ripartiva da 1).
        $uid = 'olo-hotspot-' . $this->chiave_stabile( $settings );
        if ( isset( self::$uid_usati[ $uid ] ) ) {
            self::$uid_usati[ $uid ]++;
            $uid .= '-' . self::$uid_usati[ $uid ];
        } else {
            self::$uid_usati[ $uid ] = 1;
        }

        $s       = wp_parse_args( $settings, $this->defaults );
        $builder = ! empty( $s['_builder_mode'] );

        $markers = is_array( $s['markers'] ) ? $s['markers'] : [];
        if ( empty( $markers ) ) {
            return '';
        }

        $image         = esc_url( $s['image'] );
        $img_height    = max( 200, min( 800, intval( $s['image_height'] ) ) );
        $marker_color  = $this->safe_color_css( $s['marker_color'] ) ?: 'var(--olo-color-primary, #e1474f)';
        $marker_size   = max( 16, min( 40, intval( $s['marker_size'] ) ) );
        $marker_style  = in_array( $s['marker_style'] ?? 'icon', [ 'icon', 'number', 'dot' ], true ) ? $s['marker_style'] : 'icon';
        $marker_text   = $this->safe_color_css( $s['marker_text_color'] ?? '' ) ?: 'var(--olo-color-on-primary, #ffffff)';
        $pulse         = ! empty( $s['pulse_animation'] );
        $tooltip_bg    = $this->safe_color_css( $s['tooltip_bg'] ) ?: 'var(--olo-color-surface, #FFFFFF)';
        $tooltip_color = $this->safe_color_css( $s['tooltip_color'] ) ?: 'var(--olo-color-text, #374151)';
        $tooltip_width = max( 150, min( 350, intval( $s['tooltip_width'] ) ) );
        // Raggio dell'immagine: 0 resta 0 (build_border_radius_css() restituisce '' per lo zero).
        $radius_css    = $this->build_border_radius_css( $s['border_radius'] ?? '0' ) ?: '0';
        $obj_pos       = trim( (string) ( $s['object_position'] ?? 'center center' ) );
        if ( $obj_pos === '' ) {
            $obj_pos = 'center center';
        }
        // Cornice: proporzione + adattamento. Le chiavi sono quelle piatte del tile
        // Immagine (aspect_ratio/object_fit) perché `object_position` qui è già piatta:
        // dentro una stessa tile si segue la convenzione che c'è.
        $aspect = trim( (string) ( $s['aspect_ratio'] ?? 'auto' ) );
        if ( $aspect === 'custom' ) {
            $aspect = trim( (string) ( $s['aspect_ratio_custom'] ?? '' ) );
        }
        $aspect = str_replace( ':', '/', $aspect );
        $has_aspect = ( $aspect !== '' && $aspect !== 'auto'
            && preg_match( '/^\d+(?:\.\d+)?(?:\s*\/\s*\d+(?:\.\d+)?)?$/', $aspect ) );
        // Con un rapporto scelto l'altezza DEVE cedere il passo, altrimenti sono due
        // dimensioni definite e l'aspect-ratio non ha alcun effetto.
        $frame_css = $has_aspect
            ? 'aspect-ratio: ' . str_replace( ' ', '', $aspect ) . '; height: auto;'
            : 'height: ' . $img_height . 'px;';
        $valid_fit = [ 'cover', 'contain', 'fill', 'none', 'scale-down' ];
        $obj_fit   = in_array( $s['object_fit'] ?? 'cover', $valid_fit, true ) ? ( $s['object_fit'] ?? 'cover' ) : 'cover';

        // Testo alternativo: quello scritto nella tile, altrimenti quello della libreria media.
        $alt = trim( wp_strip_all_tags( (string) ( $s['image_alt'] ?? '' ) ) );
        if ( $alt === '' && $image ) {
            $att_id = absint( $s['image_id'] ?? 0 );
            if ( ! $att_id ) {
                $att_id = attachment_url_to_postid( (string) $s['image'] );
            }
            if ( $att_id ) {
                $alt = trim( (string) get_post_meta( $att_id, '_wp_attachment_image_alt', true ) );
            }
        }

        $pin_svg = '<svg width="' . $marker_size . '" height="' . $marker_size . '" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 110-5 2.5 2.5 0 010 5z"/></svg>';
        $arrow_svg = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M5 12h14M13 6l6 6-6 6"/></svg>';

        ob_start();
        ?>
        <?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above: safe_color_css() whitelist for every colour, intval()/max()/min()/round() clamps for every size, build_border_radius_css() (integer-forced) with a fixed fallback, $frame_css built from a strict "W/H" regex whitelist (or an intval()'d height), $obj_fit from an in_array() whitelist, and the internally generated esc_attr()'d $uid. ?>
        <style>
            .<?php echo esc_attr( $uid ); ?> {
                position: relative;
                width: 100%;
                <?php echo $frame_css; ?>
                border-radius: <?php echo $radius_css; ?>;
            }
            /* Il raggio lo prende l'immagine: il riquadro non taglia più ciò che esce (i fumetti). */
            .<?php echo esc_attr( $uid ); ?> > img,
            .<?php echo esc_attr( $uid ); ?> > .olo-hs-empty {
                width: 100%;
                height: 100%;
                display: block;
                border-radius: inherit;
            }
            .<?php echo esc_attr( $uid ); ?> > img {
                object-fit: <?php echo esc_attr( $obj_fit ); ?>;
                object-position: <?php echo esc_attr( $obj_pos ); ?>;
            }
            .<?php echo esc_attr( $uid ); ?> > .olo-hs-empty {
                background: var(--olo-color-surface-alt, #F3F4F6);
                display: flex;
                align-items: center;
                justify-content: center;
                color: var(--olo-color-text-faint, #9CA3AF);
                font-size: 14px;
            }
            .<?php echo esc_attr( $uid ); ?> .olo-hs-point {
                position: absolute;
                transform: translate(-50%, -50%);
                z-index: 10;
            }
            .<?php echo esc_attr( $uid ); ?> .olo-hs-point.is-open {
                z-index: 30;
            }
            .<?php echo esc_attr( $uid ); ?> .olo-hs-marker {
                position: relative;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0;
                padding: 0;
                border: 0;
                background: none;
                box-shadow: none;
                min-width: 0;
                min-height: 0;
                font: inherit;
                line-height: 1;
                cursor: pointer;
                color: <?php echo $marker_color; ?>;
                filter: drop-shadow(0 2px 4px rgba(0,0,0,0.4));
                transition: transform 0.2s ease;
            }
            .<?php echo esc_attr( $uid ); ?> .olo-hs-marker:hover {
                transform: scale(1.15);
            }
            .<?php echo esc_attr( $uid ); ?> .olo-hs-marker:focus-visible {
                outline: 2px solid var(--olo-color-primary, #e1474f);
                outline-offset: 3px;
                border-radius: 50%;
            }
            .<?php echo esc_attr( $uid ); ?> .olo-hs-num,
            .<?php echo esc_attr( $uid ); ?> .olo-hs-dot {
                display: flex;
                align-items: center;
                justify-content: center;
                width: <?php echo (int) $marker_size; ?>px;
                height: <?php echo (int) $marker_size; ?>px;
                border-radius: 50%;
                background: <?php echo $marker_color; ?>;
                color: <?php echo $marker_text; ?>;
                box-shadow: 0 0 0 2px color-mix(in srgb, var(--olo-color-surface, #ffffff) 85%, transparent);
                font-size: <?php echo (int) round( $marker_size * 0.5 ); ?>px;
                font-weight: 700;
                font-variant-numeric: tabular-nums;
            }
            <?php if ( $pulse ) : ?>
            @keyframes <?php echo esc_attr( $uid ); ?>-pulse {
                0%, 100% { box-shadow: 0 0 0 0 color-mix(in srgb, <?php echo $marker_color; ?> 50%, transparent); }
                50% { box-shadow: 0 0 0 <?php echo round( $marker_size * 0.6 ); ?>px color-mix(in srgb, <?php echo $marker_color; ?> 0%, transparent); }
            }
            .<?php echo esc_attr( $uid ); ?> .olo-hs-ring {
                position: absolute;
                width: <?php echo $marker_size + 8; ?>px;
                height: <?php echo $marker_size + 8; ?>px;
                border-radius: 50%;
                animation: <?php echo esc_attr( $uid ); ?>-pulse 2s ease-in-out infinite;
            }
            <?php endif; ?>
            .<?php echo esc_attr( $uid ); ?> .olo-hs-tooltip {
                display: none;
                position: absolute;
                z-index: 20;
                width: <?php echo $tooltip_width; ?>px;
                max-width: calc(100vw - 16px);
                box-sizing: border-box;
                background: <?php echo $tooltip_bg; ?>;
                /* Visibile solo con uno sfondo semitrasparente (preset «Glass»): vetro smerigliato. */
                -webkit-backdrop-filter: blur(10px);
                backdrop-filter: blur(10px);
                color: <?php echo $tooltip_color; ?>;
                padding: 12px 14px;
                border-radius: 6px;
                font-size: 13px;
                line-height: 1.5;
                text-align: left;
                box-shadow: 0 4px 16px rgba(0,0,0,0.3);
            }
            .<?php echo esc_attr( $uid ); ?> .olo-hs-tooltip.is-visible {
                display: block;
            }
            .<?php echo esc_attr( $uid ); ?> .olo-hs-tooltip-img {
                display: block;
                width: calc(100% + 1.75rem);
                max-width: none;
                max-height: 10rem;
                object-fit: cover;
                margin: -0.75rem -0.875rem 0.625rem;
                border-radius: 0.375rem 0.375rem 0 0;
            }
            .<?php echo esc_attr( $uid ); ?> .olo-hs-tooltip-title {
                font-weight: 700;
                font-size: 14px;
                margin-bottom: 4px;
            }
            .<?php echo esc_attr( $uid ); ?> .olo-hs-link {
                display: inline-flex;
                align-items: center;
                gap: .35em;
                margin-top: .6em;
                color: inherit;
                font-weight: 600;
                text-decoration: underline;
                text-underline-offset: .2em;
            }
            .<?php echo esc_attr( $uid ); ?> .olo-hs-link:focus-visible {
                outline: 2px solid currentColor;
                outline-offset: 2px;
                border-radius: 2px;
            }
            .<?php echo esc_attr( $uid ); ?> .olo-hs-tooltip::after {
                content: '';
                position: absolute;
                width: 0;
                height: 0;
                border: 6px solid transparent;
            }
            /* Arrow positions. --olo-hs-dx: lo spostamento che lo script dà al fumetto per
               tenerlo dentro lo schermo; la freccia lo compensa e resta sul marker. */
            .<?php echo esc_attr( $uid ); ?> .olo-hs-tooltip[data-pos="top"] {
                bottom: 100%;
                left: 50%;
                transform: translateX(calc(-50% + var(--olo-hs-dx, 0px)));
                margin-bottom: 10px;
            }
            .<?php echo esc_attr( $uid ); ?> .olo-hs-tooltip[data-pos="top"]::after {
                top: 100%;
                left: calc(50% - var(--olo-hs-dx, 0px));
                transform: translateX(-50%);
                border-top-color: <?php echo $tooltip_bg; ?>;
            }
            .<?php echo esc_attr( $uid ); ?> .olo-hs-tooltip[data-pos="bottom"] {
                top: 100%;
                left: 50%;
                transform: translateX(calc(-50% + var(--olo-hs-dx, 0px)));
                margin-top: 10px;
            }
            .<?php echo esc_attr( $uid ); ?> .olo-hs-tooltip[data-pos="bottom"]::after {
                bottom: 100%;
                left: calc(50% - var(--olo-hs-dx, 0px));
                transform: translateX(-50%);
                border-bottom-color: <?php echo $tooltip_bg; ?>;
            }
            .<?php echo esc_attr( $uid ); ?> .olo-hs-tooltip[data-pos="left"] {
                right: 100%;
                top: 50%;
                transform: translateY(-50%);
                margin-right: 10px;
            }
            .<?php echo esc_attr( $uid ); ?> .olo-hs-tooltip[data-pos="left"]::after {
                left: 100%;
                top: 50%;
                transform: translateY(-50%);
                border-left-color: <?php echo $tooltip_bg; ?>;
            }
            .<?php echo esc_attr( $uid ); ?> .olo-hs-tooltip[data-pos="right"] {
                left: 100%;
                top: 50%;
                transform: translateY(-50%);
                margin-left: 10px;
            }
            .<?php echo esc_attr( $uid ); ?> .olo-hs-tooltip[data-pos="right"]::after {
                right: 100%;
                top: 50%;
                transform: translateY(-50%);
                border-right-color: <?php echo $tooltip_bg; ?>;
            }
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>

        <div class="<?php echo esc_attr( $uid ); ?> olo-hs-preset-<?php echo esc_attr( sanitize_key( $s['preset'] ?? 'custom' ) ); ?>" id="<?php echo esc_attr( $uid ); ?>">
            <?php if ( $image ) : ?>
                <img src="<?php echo $image; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped via esc_url() at assignment above ?>" alt="<?php echo esc_attr( $alt ); ?>" loading="lazy" />
            <?php elseif ( $builder ) : ?>
                <?php // Segnaposto per l'autore, solo nel canvas: al visitatore resta il riquadro vuoto (con lo Sfondo della tile). ?>
                <div class="olo-hs-empty"><?php echo esc_html( olobuild_t( "Seleziona un'immagine" ) ); ?></div>
            <?php endif; ?>

            <?php foreach ( $markers as $idx => $marker ) :
                if ( ! is_array( $marker ) ) {
                    continue;
                }
                $pos_x   = max( 0, min( 100, floatval( $marker['pos_x'] ?? 50 ) ) );
                $pos_y   = max( 0, min( 100, floatval( $marker['pos_y'] ?? 50 ) ) );
                $title   = wp_kses_post( $marker['title'] ?? '' );
                $desc    = wp_kses_post( $marker['description'] ?? '' );
                $m_icon  = sanitize_text_field( $marker['icon'] ?? 'pin' );
                $tt_pos  = in_array( $marker['tooltip_position'] ?? 'top', [ 'top', 'bottom', 'left', 'right' ], true ) ? ( $marker['tooltip_position'] ?? 'top' ) : 'top';
                $tt_id   = $uid . '-tip-' . (int) $idx;
                $m_label = trim( wp_strip_all_tags( $title ) );
                if ( $m_label === '' ) {
                    /* translators: %d: marker number */
                    $m_label = sprintf( __( 'Hotspot %d', 'olobuild' ), (int) $idx + 1 );
                }
                $link_url   = trim( (string) ( $marker['link'] ?? '' ) );
                $link_label = trim( wp_strip_all_tags( (string) ( $marker['link_label'] ?? '' ) ) );
                if ( $link_label === '' ) {
                    $link_label = olobuild_t( 'Scopri di più' );
                }

                // Il segno del marker: icona (pin storico o icona scelta col picker), numero o punto.
                if ( $marker_style === 'number' ) {
                    $segno = '<span class="olo-hs-num" aria-hidden="true">' . ( (int) $idx + 1 ) . '</span>';
                } elseif ( $marker_style === 'dot' ) {
                    $segno = '<span class="olo-hs-dot" aria-hidden="true"></span>';
                } else {
                    $segno = ( $m_icon === 'pin' || $m_icon === '' ) ? '' : $this->render_icon_html( $m_icon, $marker_size / 20, 'aria-hidden="true"' );
                    // Un nome che nessuna libreria conosce (o un'icona personalizzata eliminata)
                    // resterebbe un marker invisibile: si ripiega sul pin.
                    if ( $segno === '' || $this->origine_icona( $m_icon ) === '' ) {
                        $segno = $pin_svg;
                    }
                }
            ?>
            <div class="olo-hs-point" style="left:<?php echo (float) $pos_x; ?>%;top:<?php echo (float) $pos_y; ?>%;">
                <button type="button" class="olo-hs-marker"
                        data-idx="<?php echo (int) $idx; ?>"
                        data-olo-interactive
                        aria-label="<?php echo esc_attr( $m_label ); ?>"
                        aria-expanded="false"
                        aria-controls="<?php echo esc_attr( $tt_id ); ?>">
                    <?php if ( $pulse ) : ?><span class="olo-hs-ring" aria-hidden="true"></span><?php endif; ?>
                    <?php echo $segno; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static pin SVG (hardcoded path, int size), an int-cast number, or icon markup from render_icon_html() (sanitized SVG / esc_attr()'d uk-icon attrs) ?>
                </button>
                <?php
                list( $hst_cls, $hst_data ) = $this->tfx_attrs( $s, 'title', wp_strip_all_tags( $title ) );
                list( $hsd_cls, $hsd_data ) = $this->tfx_attrs( $s, 'description', wp_strip_all_tags( $desc ) );
                ?>
                <div class="olo-hs-tooltip" id="<?php echo esc_attr( $tt_id ); ?>" data-pos="<?php echo esc_attr( $tt_pos ); ?>">
                    <?php if ( ! empty( $marker['image'] ) ) : ?>
                        <img class="olo-hs-tooltip-img" src="<?php echo esc_url( $marker['image'] ); ?>" alt="" loading="lazy">
                    <?php endif; ?>
                    <?php if ( $title ) : ?>
                        <div class="olo-hs-tooltip-title<?php echo $hst_cls; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tfx_attrs() fragments are escaped internally (sanitize_html_class/esc_attr); title sanitized via wp_kses_post() above ?>"<?php echo $hst_data; ?>><?php echo $title; ?></div>
                    <?php endif; ?>
                    <?php if ( $desc ) : ?>
                        <div class="olo-hs-tooltip-desc<?php echo $hsd_cls; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tfx_attrs() fragments are escaped internally (sanitize_html_class/esc_attr); description sanitized via wp_kses_post() above ?>"<?php echo $hsd_data; ?>><?php echo $desc; ?></div>
                    <?php endif; ?>
                    <?php if ( $link_url !== '' ) : ?>
                        <a class="olo-hs-link" href="<?php echo esc_url( $link_url ); ?>"><?php echo esc_html( $link_label ); ?><?php echo $arrow_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG literal defined above ?></a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <script>
        (function(){
            function init(){
                var container = document.getElementById('<?php echo esc_js( $uid ); ?>');
                if(!container){return}
                if(container.getAttribute('data-olo-hs-ready')){return}
                container.setAttribute('data-olo-hs-ready','1');
                function closeAll(){
                    container.querySelectorAll('.olo-hs-tooltip').forEach(function(t){ t.classList.remove('is-visible'); });
                    container.querySelectorAll('.olo-hs-point').forEach(function(p){ p.classList.remove('is-open'); });
                    container.querySelectorAll('.olo-hs-marker').forEach(function(m){ m.setAttribute('aria-expanded','false'); });
                }
                /* Tiene il fumetto dentro lo schermo: sopra/sotto scorre di lato, sinistra/destra si gira. */
                function place(tip){
                    var base = tip.getAttribute('data-pos-base');
                    if(!base){
                        base = tip.getAttribute('data-pos') || 'top';
                        tip.setAttribute('data-pos-base', base);
                    }
                    tip.setAttribute('data-pos', base);
                    tip.style.removeProperty('--olo-hs-dx');
                    var r = tip.getBoundingClientRect();
                    var vw = document.documentElement.clientWidth || window.innerWidth;
                    if(base === 'left'){
                        if(r.left < 8){ tip.setAttribute('data-pos','right'); }
                    } else if(base === 'right'){
                        if(r.right > vw - 8){ tip.setAttribute('data-pos','left'); }
                    } else {
                        var dx = 0;
                        if(r.left < 8){ dx = 8 - r.left; }
                        else if(r.right > vw - 8){ dx = (vw - 8) - r.right; }
                        if(dx !== 0){ tip.style.setProperty('--olo-hs-dx', Math.round(dx) + 'px'); }
                    }
                }
                function toggleMarker(marker){
                    var point = marker.parentNode;
                    var tooltip = point ? point.querySelector('.olo-hs-tooltip') : null;
                    if(!tooltip){return}
                    var isVisible = tooltip.classList.contains('is-visible');
                    closeAll();
                    if(!isVisible){
                        tooltip.classList.add('is-visible');
                        point.classList.add('is-open');
                        marker.setAttribute('aria-expanded','true');
                        place(tooltip);
                    }
                }
                container.addEventListener('click', function(e){
                    /* Dentro il fumetto (il link, il testo) non si chiude niente. */
                    if(e.target.closest('.olo-hs-tooltip')){return}
                    var marker = e.target.closest('.olo-hs-marker');
                    if(!marker){
                        closeAll();
                        return;
                    }
                    toggleMarker(marker);
                });
                container.addEventListener('keydown', function(e){
                    if(e.key !== 'Escape'){return}
                    var open = container.querySelector('.olo-hs-point.is-open');
                    closeAll();
                    if(open){
                        var m = open.querySelector('.olo-hs-marker');
                        if(m){ m.focus(); }
                    }
                });
            }
            if(document.readyState === 'loading'){ document.addEventListener('DOMContentLoaded', init); } else { init(); }
        })();
        </script>
        <?php
        $tfx_css = $this->tfx_css( $s, '.' . $uid );
        if ( $tfx_css ) echo '<style>' . $tfx_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Text_Effects::css() from fixed effect definitions
        $this->tfx_print_script();
        // Border system
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
        // Raggio in hover: dopo il bordo in hover, di cui riprende la transizione (stesso elemento).
        $radius_hover_css  = Olobuild_Tile_Utils::radius_hover_rules( ".{$uid}", $s, 'border_radius_hover', Olobuild_Tile_Utils::transizione_di( $border_hover_css ) );
        if ( $border_css || $border_hover_css || $border_effect_css || $radius_hover_css ) {
            echo '<style>';
            if ( $border_css ) echo ".{$uid}{{$border_css}}"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_css() (integer-forced widths) for the internally generated uid
            echo $border_hover_css . $border_effect_css . $radius_hover_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by the Olobuild_Tile_Base::build_border_hover_css()/build_border_effect_css() shared helpers; radius rules from Olobuild_Tile_Utils::radius_hover_rules() (integer px)
        }
        return ob_get_clean();
    }
}
