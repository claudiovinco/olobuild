<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Switcher_Tile extends Olobuild_Tile_Base {

    protected $type     = 'switcher';
    protected $name     = 'Switcher';
    protected $icon     = 'dashicons-welcome-widgets-menus';
    protected $category = 'interactive';
    protected $defaults = [
        'items'     => [
            [ 'title' => 'Prima scheda', 'content' => 'Contenuto della prima scheda.' ],
            [ 'title' => 'Seconda scheda', 'content' => 'Contenuto della seconda scheda.' ],
        ],
        'preset'             => 'pill-slide',
        'nav_style'          => 'tab',
        'animation'          => 'fade',
        'animation_duration' => '250',
        'vertical'           => false,
        'tab_padding_y'      => '10',
        'tab_padding_x'      => '18',
        'tab_font_size'      => '14',
        'tab_font_weight'    => '500',
        'tab_gap'            => '4',
        'tab_radius'         => '8',
        'container_bg'       => 'var(--olo-color-surface, #f1f5f9)',
        'container_padding'  => '4',
        'container_radius'   => '10',
        'active_bg'          => 'var(--olo-color-surface, #ffffff)',
        'active_color'       => 'var(--olo-color-text, #1e293b)',
        'inactive_color'     => 'var(--olo-color-text-soft, #64748b)',
        'hover_bg'           => '',
        'indicator_type'     => 'none',
        'indicator_color'    => '',
        'content_bg'         => '',
        'content_color'      => 'var(--olo-color-text, #1e293b)',
        'content_padding_y'  => '20',
        'content_padding_x'  => '0',
        'shadow'             => 'none',
        // Ombra «Personalizzata» (controllo box-shadow condiviso): le sei chiavi storiche.
        'shadow_h'           => '0',
        'shadow_v'           => '4',
        'shadow_blur'        => '10',
        'shadow_spread'      => '0',
        'shadow_color'       => 'rgba(0,0,0,0.15)',
        'shadow_inset'       => false,
        'effect_color'       => '',
        'effect_intensity'   => 'medium',
        'effect_speed'       => 0,
        'wow_disable'           => false,
        'wow_backdrop_blur'     => 0,
        'wow_backdrop_saturate' => 100,
        'wow_border_style'      => 'solid',
        'wow_font_family'       => 'inherit',
        'wow_rotation'          => 0,
        'wow_perspective'       => 0,
        'wow_tilt_x'            => 0,
        'wow_glow_pulse'        => false,
        'wow_title_glow'        => false,
        'wow_scanlines'         => false,

        'wow_terminal_prompt' => false,
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

    public function render( $settings ) {
        $s     = wp_parse_args( $settings, $this->defaults );
        $items = $this->parse_items( $s['items'] );
        $count = count( $items );

        if ( $count === 0 ) {
            return '';
        }

        // V3.23.1 — preset is applied JS-side at the moment the user picks it
        // (BuilderInspector.applyPreset). The PHP renderer just reads the
        // already-populated fields, so manual edits on top of a preset win.
        $preset_id = $s['preset'] ?? 'pill-slide';

        $vertical  = ! empty( $s['vertical'] );
        $duration  = max( 80, intval( $s['animation_duration'] ?? 250 ) );

        // Build switcher attribute (UIkit animation)
        $switcher_attr = '';
        if ( ! empty( $s['animation'] ) ) {
            $switcher_attr = 'animation: uk-animation-' . esc_attr( $s['animation'] );
        }

        $uid = 'olo-sw-' . wp_rand( 10000, 99999 );

        // ── Color helpers ──
        $tab_bg_active   = $this->safe_color_css( $s['active_bg'] ) ?: '';
        $tab_color_act   = $this->safe_color_css( $s['active_color'] ) ?: 'var(--olo-color-text, #1e293b)';
        $tab_color_inact = $this->safe_color_css( $s['inactive_color'] ) ?: '#64748b';
        $tab_hover_bg    = $this->safe_color_css( $s['hover_bg'] ) ?: '';
        $container_bg    = $this->safe_color_css( $s['container_bg'] ) ?: '';
        $indicator_clr   = $this->safe_color_css( $s['indicator_color'] ) ?: 'var(--olo-color-primary, #e1474f)';
        $content_bg      = $this->safe_color_css( $s['content_bg'] ) ?: '';
        $content_color   = $this->safe_color_css( $s['content_color'] ) ?: '';

        // Padding a 4 lati (controllo standard `spacing`), con ripiego sulle chiavi piatte storiche.
        $tab_pad   = Olobuild_Tile_Utils::spacing_sides(
            $s['tab_padding'] ?? null,
            [ 'y' => $s['tab_padding_y'] ?? null, 'x' => $s['tab_padding_x'] ?? null ],
            [ 10, 18, 10, 18 ]
        );
        $tab_pad_y = max( 0, $tab_pad['top'] );
        $tab_pad_x = max( 0, $tab_pad['left'] );
        $tab_fs    = max( 10, intval( $s['tab_font_size'] ?? 14 ) );
        $tab_fw    = preg_match( '/^[1-9]00$/', (string) ($s['tab_font_weight'] ?? '500') ) ? $s['tab_font_weight'] : '500';
        $tab_gap   = max( 0, intval( $s['tab_gap'] ?? 4 ) );
        // Dual-format: Number legacy E oggetto {tl,tr,br,bl} (build_border_radius_css ritorna '' se zero/vuoto).
        $tab_rad_css  = $this->build_border_radius_css( $s['tab_radius'] ?? 8 ) ?: '0px';
        $cont_pad_css = Olobuild_Tile_Utils::spacing_css( $s['container_padding'] ?? 4, 4 );
        $cont_rad_css = $this->build_border_radius_css( $s['container_radius'] ?? 10 );
        $content_pad_css = Olobuild_Tile_Utils::sides_css( Olobuild_Tile_Utils::spacing_sides(
            $s['content_padding'] ?? null,
            [ 'y' => $s['content_padding_y'] ?? null, 'x' => $s['content_padding_x'] ?? null ],
            [ 20, 0, 20, 0 ]
        ) );
        $indicator = $s['indicator_type'] ?? 'none';

        // Ombra della barra schede. sm/md/lg: i valori di sempre. «Molto forte» (xl) e
        // «Personalizzata» (custom, che il preset Brutalist imposta) prima erano ignorate.
        $shadow_key = (string) ( $s['shadow'] ?? 'none' );
        $shadow_css = '';
        if ( $shadow_key === 'sm' ) {
            $shadow_css = 'box-shadow: 0 1px 2px rgba(16,24,40,0.06), 0 1px 3px rgba(16,24,40,0.08);';
        } elseif ( $shadow_key === 'md' ) {
            $shadow_css = 'box-shadow: 0 4px 6px rgba(16,24,40,0.08), 0 2px 4px rgba(16,24,40,0.06);';
        } elseif ( $shadow_key === 'lg' ) {
            $shadow_css = 'box-shadow: 0 12px 24px rgba(16,24,40,0.10), 0 4px 8px rgba(16,24,40,0.08);';
        } elseif ( $shadow_key === 'xl' ) {
            $shadow_css = 'box-shadow: ' . Olobuild_Tile_Utils::shadow( 'xl' ) . ';';
        } elseif ( $shadow_key === 'custom' ) {
            $sh_color   = $this->safe_color_css( $s['shadow_color'] ?? '' ) ?: 'rgba(0,0,0,0.15)';
            $sh_inset   = ( ! empty( $s['shadow_inset'] ) && $s['shadow_inset'] !== 'false' ) ? 'inset ' : '';
            $shadow_css = 'box-shadow: ' . $sh_inset . intval( $s['shadow_h'] ?? 0 ) . 'px ' . intval( $s['shadow_v'] ?? 4 ) . 'px '
                        . max( 0, intval( $s['shadow_blur'] ?? 10 ) ) . 'px ' . intval( $s['shadow_spread'] ?? 0 ) . 'px ' . $sh_color . ';';
        }

        ob_start();
        // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above: safe_color_css() whitelist for every colour, intval()/max() clamps for numerics, preg_match() whitelist for font weight, fixed-literal shadow strings, build_border_radius_css()/build_wow_effects_css() helpers, and the internally generated $uid.
        ?>
        <style>
            /* ═══ Switcher V3.23.0 — preset: <?php echo esc_html( $preset_id ); ?> ═══ */
            .<?php echo esc_attr( $uid ); ?> { margin: 0; }
            .<?php echo esc_attr( $uid ); ?> .olo-sw-nav,
            .<?php echo esc_attr( $uid ); ?> ul.uk-tab,
            .<?php echo esc_attr( $uid ); ?> ul.uk-subnav,
            .<?php echo esc_attr( $uid ); ?> ul.uk-tab-left {
                margin: 0;
                padding: <?php echo esc_attr( $cont_pad_css ); ?>;
                <?php if ( $container_bg ) : ?>background: <?php echo $container_bg; ?>;<?php endif; ?>
                <?php if ( $cont_rad_css ) : ?>border-radius: <?php echo $cont_rad_css; ?>;<?php endif; ?>
                <?php echo $shadow_css; ?>
                gap: <?php echo $tab_gap; ?>px;
                list-style: none;
                display: flex;
                <?php if ( $vertical ) : ?>flex-direction: column;<?php endif; ?>
                border: 0;
                <?php if ( $indicator === 'underline' && ! $vertical ) : ?>
                border-bottom: 1px solid #e5e7eb;
                gap: 0;
                padding: 0;
                <?php endif; ?>
            }
            .<?php echo esc_attr( $uid ); ?> ul.uk-tab > *,
            .<?php echo esc_attr( $uid ); ?> ul.uk-subnav > *,
            .<?php echo esc_attr( $uid ); ?> ul.uk-tab-left > * {
                padding: 0;
                margin: 0;
                position: relative;
                <?php if ( $indicator === 'underline' && ! $vertical ) : ?>margin-bottom: -1px;<?php endif; ?>
            }
            .<?php echo esc_attr( $uid ); ?> ul.uk-tab > * > a,
            .<?php echo esc_attr( $uid ); ?> ul.uk-subnav > * > a,
            .<?php echo esc_attr( $uid ); ?> ul.uk-tab-left > * > a {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                padding: <?php echo esc_attr( Olobuild_Tile_Utils::sides_css( $tab_pad ) ); ?>;
                font-size: <?php echo $tab_fs; ?>px;
                font-weight: <?php echo $tab_fw; ?>;
                line-height: 1.4;
                color: <?php echo $tab_color_inact; ?>;
                /* la scheda attiva di UIkit ha il bordo blu: qui lo colora solo l'indicatore scelto */
                border-color: transparent;
                text-transform: none;
                text-decoration: none;
                border-radius: <?php echo $tab_rad_css; ?>;
                transition: background-color <?php echo $duration; ?>ms ease, color <?php echo $duration; ?>ms ease, box-shadow <?php echo $duration; ?>ms ease;
                white-space: nowrap;
                <?php if ( $vertical ) : ?>
                justify-content: flex-start;
                width: 100%;
                <?php endif; ?>
                <?php if ( $indicator === 'underline' && ! $vertical ) : ?>
                border-bottom: 2px solid transparent;
                border-radius: 0;
                padding-bottom: <?php echo max( 0, $tab_pad_y - 2 ); ?>px;
                <?php endif; ?>
                <?php if ( $indicator === 'overline' && ! $vertical ) : ?>
                border-top: 2px solid transparent;
                border-radius: 0;
                padding-top: <?php echo max( 0, $tab_pad_y - 2 ); ?>px;
                <?php endif; ?>
                <?php if ( $indicator === 'left-bar' && $vertical ) : ?>
                border-left: 2px solid transparent;
                padding-left: <?php echo max( 0, $tab_pad_x - 2 ); ?>px;
                border-radius: 0 6px 6px 0;
                <?php endif; ?>
            }
            <?php if ( $tab_hover_bg ) : ?>
            .<?php echo esc_attr( $uid ); ?> ul.uk-tab > *:not(.uk-active) > a:hover,
            .<?php echo esc_attr( $uid ); ?> ul.uk-subnav > *:not(.uk-active) > a:hover,
            .<?php echo esc_attr( $uid ); ?> ul.uk-tab-left > *:not(.uk-active) > a:hover {
                background: <?php echo $tab_hover_bg; ?>;
                color: <?php echo $tab_color_act; ?>;
            }
            <?php endif; ?>
            /* a11y: anello di focus visibile da tastiera sul tab */
            .<?php echo esc_attr( $uid ); ?> ul.uk-tab > * > a:focus-visible,
            .<?php echo esc_attr( $uid ); ?> ul.uk-subnav > * > a:focus-visible,
            .<?php echo esc_attr( $uid ); ?> ul.uk-tab-left > * > a:focus-visible {
                outline: none;
                box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent);
            }
            .<?php echo esc_attr( $uid ); ?> ul.uk-tab > .uk-active > a,
            .<?php echo esc_attr( $uid ); ?> ul.uk-subnav > .uk-active > a,
            .<?php echo esc_attr( $uid ); ?> ul.uk-tab-left > .uk-active > a {
                color: <?php echo $tab_color_act; ?>;
                <?php if ( $tab_bg_active ) : ?>background: <?php echo $tab_bg_active; ?>;<?php endif; ?>
                <?php if ( $indicator === 'pill' ) : ?>
                box-shadow: 0 1px 2px rgba(16,24,40,0.06), 0 1px 3px rgba(16,24,40,0.05);
                <?php endif; ?>
                <?php if ( $indicator === 'underline' && ! $vertical ) : ?>
                border-bottom-color: <?php echo $indicator_clr; ?>;
                <?php endif; ?>
                <?php if ( $indicator === 'overline' && ! $vertical ) : ?>
                border-top-color: <?php echo $indicator_clr; ?>;
                <?php endif; ?>
                <?php if ( $indicator === 'left-bar' && $vertical ) : ?>
                border-left-color: <?php echo $indicator_clr; ?>;
                <?php endif; ?>
            }
            /* UIkit kills its own ::before/::after pseudo-elements that we don't need */
            .<?php echo esc_attr( $uid ); ?> ul.uk-tab::before,
            .<?php echo esc_attr( $uid ); ?> ul.uk-subnav::before {
                content: none !important;
            }
            .<?php echo esc_attr( $uid ); ?> ul.uk-tab > * > a::before,
            .<?php echo esc_attr( $uid ); ?> ul.uk-subnav > * > a::before {
                content: none !important;
            }
            /* Content panel */
            .<?php echo esc_attr( $uid ); ?> .uk-switcher,
            .<?php echo esc_attr( $uid ); ?> .olo-switcher-content {
                margin: 0;
                padding: 0;
                list-style: none;
            }
            .<?php echo esc_attr( $uid ); ?> .uk-switcher > li,
            .<?php echo esc_attr( $uid ); ?> .olo-switcher-content > li {
                padding: <?php echo esc_attr( $content_pad_css ); ?>;
                <?php if ( $content_bg ) : ?>background: <?php echo $content_bg; ?>;<?php endif; ?>
                <?php if ( $content_color ) : ?>color: <?php echo $content_color; ?>;<?php endif; ?>
                line-height: 1.65;
                font-size: 14px;
            }
            <?php if ( $vertical ) : ?>
            .<?php echo esc_attr( $uid ); ?>.olo-switcher--vert {
                display: grid;
                grid-template-columns: minmax(160px, 220px) 1fr;
                gap: 24px;
                align-items: stretch;
            }
            /* V3.23.2 — vertical layout: nav distributes evenly to match the
               content panel height, so the left column never looks shorter
               than the right one. Each tab keeps a sensible min-height. */
            .<?php echo esc_attr( $uid ); ?>.olo-switcher--vert ul.uk-tab-left {
                height: 100%;
                align-self: stretch;
            }
            .<?php echo esc_attr( $uid ); ?>.olo-switcher--vert ul.uk-tab-left > li {
                flex: 1 1 auto;
                min-height: 40px;
                display: flex;
            }
            .<?php echo esc_attr( $uid ); ?>.olo-switcher--vert ul.uk-tab-left > li > a {
                width: 100%;
                height: 100%;
                align-items: center;
            }
            <?php endif; ?>
            /* Icona prima del testo della scheda; immagine accanto al testo del pannello */
            .<?php echo esc_attr( $uid ); ?> .olo-sw-icon { display: inline-flex; margin-right: 0.5rem; vertical-align: -0.15em; }
            .<?php echo esc_attr( $uid ); ?> .olo-sw-media { display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 1fr); gap: clamp(1rem, 3vw, 2rem); align-items: center; }
            .<?php echo esc_attr( $uid ); ?> .olo-sw-img { display: block; width: 100%; height: auto; border-radius: 0.75rem; object-fit: cover; }
            .<?php echo esc_attr( $uid ); ?> .olo-sw-cta { margin: 1rem 0 0; }
            @media (max-width: 640px) { .<?php echo esc_attr( $uid ); ?> .olo-sw-media { grid-template-columns: 1fr; } }
            /* Testo formattato nei pannelli: niente margine esterno sul primo e sull'ultimo blocco */
            .<?php echo esc_attr( $uid ); ?> .olo-switcher-content > li > :first-child { margin-top: 0; }
            .<?php echo esc_attr( $uid ); ?> .olo-switcher-content > li > :last-child { margin-bottom: 0; }
            <?php
            // Effetti wow (i preset audaci impostano i campi wow_* via TILE_PRESETS.switcher).
            // Il «titolo» del bagliore e del prompt terminale è il testo di ogni scheda.
            echo $this->build_wow_effects_css( $s, '.' . esc_attr( $uid ), '.olo-sw-label' );
            ?>
        </style>
        <?php
        // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped

        // Voci della barra e pannelli, costruiti una volta sola per i due layout.
        $nav_html  = '';
        $pane_html = '';
        foreach ( $items as $i => $item ) {
            $titolo = wp_strip_all_tags( $item['title'] );
            list( $swt_cls, $swt_data ) = $this->tfx_attrs( $s, 'title', $titolo );
            // Il testo della scheda sta in uno span .olo-sw-label: è il «titolo» degli effetti
            // wow (bagliore, prompt terminale) e degli effetti testo, che ne riscrivono il
            // contenuto senza toccare il link di UIkit.
            // Icona della scheda (dalla 1.4.508, era della tile Tab a Icone): prima del testo.
            $icona = '' !== $item['icon'] ? $this->render_icon_html( $item['icon'], 0.9, 'aria-hidden="true"' ) : '';
            $nav_html .= '<li' . ( $i === 0 ? ' class="uk-active"' : '' ) . '><a href="#">' . ( $icona ? '<span class="olo-sw-icon">' . $icona . '</span>' : '' ) . '<span class="olo-sw-label' . $swt_cls . '"' . $swt_data . '>' . esc_html( $titolo ) . '</span></a></li>';

            list( $swc_cls, $swc_data ) = $this->tfx_attrs( $s, 'content', wp_strip_all_tags( (string) $item['content'] ) );
            $widget_html = $this->render_widget_template( $item['widget_template_id'] ?? 0 );
            // Immagine e pulsante del pannello (dalla 1.4.508, erano della tile Switcher Panel).
            $img_html = '' !== $item['image'] ? '<img class="olo-sw-img" src="' . esc_url( $item['image'] ) . '" alt="" loading="lazy">' : '';
            $btn_html = ( '' !== $item['link_text'] && '' !== $item['link_url'] )
                ? '<p class="olo-sw-cta"><a class="uk-button uk-button-primary" href="' . esc_url( $item['link_url'] ) . '">' . esc_html( $item['link_text'] ) . '</a></p>'
                : '';
            $corpo = $this->contenuto_scheda( $item['content'] ) . $btn_html;
            if ( $img_html ) {
                $corpo = '<div class="olo-sw-media">' . $img_html . '<div class="olo-sw-text">' . $corpo . '</div></div>';
            }
            $pane_html  .= '<li class="' . trim( $swc_cls . ( $i === 0 ? ' uk-active' : '' ) ) . '"' . $swc_data . '>'
                . ( $widget_html ? '<div class="olo-item-widget">' . $widget_html . '</div>' : '' )
                . $corpo
                . '</li>';
        }
        // Verticale: uk-tab-left; orizzontale: uk-tab (connect + .uk-active automatico).
        $nav_class = $vertical ? 'uk-tab-left olo-sw-nav' : 'uk-tab olo-sw-nav';
        ?>
        <div class="olo-switcher<?php echo $vertical ? ' olo-switcher--vert' : ''; ?> <?php echo esc_attr( $uid ); ?>">
            <ul class="<?php echo esc_attr( $nav_class ); ?>" uk-tab="connect: .<?php echo esc_attr( $uid ); ?>-content; <?php echo esc_attr( $switcher_attr ); ?>"><?php echo $nav_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tab titles esc_html()'d above; tfx_attrs() fragments are escaped internally (sanitize_html_class/esc_attr) ?></ul>
            <ul class="uk-switcher olo-switcher-content <?php echo esc_attr( $uid ); ?>-content"><?php echo $pane_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- panes built above: contenuto_scheda() returns esc_html()'d plain text or wp_kses_post()'d HTML; widget HTML from render_widget_template() (escapes its own output); tfx_attrs() fragments escaped internally ?></ul>
        </div>
        <?php

        $tfx_css = $this->tfx_css( $s, '.' . $uid );
        if ( $tfx_css ) echo '<style>' . $tfx_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Text_Effects::css() from whitelisted effects, sanitized colors and integer timings
        $this->tfx_print_script();

        // Border system (wrapper)
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) echo ".{$uid}{{$border_css}}"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_css() from sanitized settings; $uid is internally generated
            echo $border_hover_css . $border_effect_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_hover_css()/build_border_effect_css() from sanitized settings
        }
        return ob_get_clean();
    }

    /**
     * Parse items from array format.
     */
    private function parse_items( $raw ) {
        if ( is_array( $raw ) ) {
            $items = [];
            foreach ( $raw as $item ) {
                if ( is_array( $item ) && ! empty( $item['title'] ) ) {
                    $items[] = [
                        'title'              => $item['title'],
                        'content'            => $item['content'] ?? '',
                        'widget_template_id' => absint( $item['widget_template_id'] ?? 0 ),
                        'icon'               => sanitize_text_field( (string) ( $item['icon'] ?? '' ) ),
                        'image'              => (string) ( $item['image'] ?? '' ),
                        'link_text'          => trim( wp_strip_all_tags( (string) ( $item['link_text'] ?? '' ) ) ),
                        'link_url'           => trim( (string) ( $item['link_url'] ?? '' ) ),
                    ];
                }
            }
            return $items;
        }
        return [];
    }

    /**
     * Il testo di una scheda, nei due formati che può avere.
     * Testo semplice (come lo salvava il campo storico): resa di sempre, a capo → <br>.
     * Testo formattato (l'editor salva HTML): HTML pulito via wp_kses_post(); se ha solo
     * marcatori in linea (grassetto, link…) gli a capo scritti a mano restano.
     *
     * @param string $raw Contenuto salvato.
     * @return string HTML sicuro.
     */
    private function contenuto_scheda( $raw ) {
        $raw = (string) $raw;
        if ( ! preg_match( '/<\/?(?:p|br|strong|b|em|i|u|s|a|span|mark|small|sub|sup|code|ul|ol|li|h[1-6]|blockquote|pre|hr|div|img|figure|table)\b/i', $raw ) ) {
            return nl2br( esc_html( wp_strip_all_tags( $raw ) ) );
        }
        $html = $this->safe_richtext_content( $raw );
        if ( ! preg_match( '/<(?:p|div|ul|ol|li|h[1-6]|blockquote|pre|hr|figure|table)\b/i', $html ) ) {
            $html = nl2br( $html );
        }
        return $html;
    }
}
