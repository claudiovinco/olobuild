<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Tile Timeline "SUPER" — redesign (handoff "OLObuild - Tile Timeline SUPER").
 *
 * 10 dimensioni combinabili: Layout (alt·one·horizontal·navigator) · Ingresso
 * (sides·bloom·unroll·slot) · Tema (paper·night·neon·blue) · Card
 * (bubble·glass·polaroid·ticket) · Filo (solid2·dash·dot·comet) · Nodo
 * (icon·dot·num·year) · Colore (cat·mono) · Media (on·off) · Densità
 * (comfy·compact) · Stato (scroll-roadmap·solid).
 *
 * CSS condiviso col canvas Vue: assets/css/timeline-super.css (namespace .olo-tlsuper),
 * stampato una sola volta per pagina. Runtime JS scoped per istanza (no &&/|| —
 * vincolo wptexturize). Migrazione delle vecchie chiavi (layout/marker_type/…).
 */
class Olo_Timeline_Tile extends Olo_Tile_Base {

    protected $type     = 'timeline';
    protected $name     = 'Timeline';
    protected $icon     = 'dashicons-backup';
    protected $category = 'interactive';

    protected $defaults = [
        'items' => [
            [ 'title' => 'Prima riga di codice', 'tag' => 'Fondazione', 'description' => 'Il prototipo del builder: drag-and-drop nativo in WordPress.', 'date' => '2019', 'image' => '', 'video' => '', 'icon' => 'star', 'category' => 'primary', 'icon_color' => '' ],
            [ 'title' => 'Libreria tile v1', 'tag' => 'Prodotto', 'description' => '40 tile native e il sistema di colori globali.', 'date' => '2021', 'image' => '', 'video' => '', 'icon' => 'grid', 'category' => 'accent', 'icon_color' => '' ],
            [ 'title' => 'Aurora, bagliori, animazioni', 'tag' => 'Effetti', 'description' => 'Sfondi generativi e transizioni native.', 'date' => '2023', 'image' => '', 'video' => '', 'icon' => 'star', 'category' => 'success', 'icon_color' => '' ],
            [ 'title' => 'Linguaggio bello & coerente', 'tag' => 'Sistema', 'description' => 'Token globali e controlli inspector allineati.', 'date' => '2025', 'image' => '', 'video' => '', 'icon' => 'settings', 'category' => 'secondary', 'icon_color' => '' ],
            [ 'title' => '240 tile, un solo standard', 'tag' => 'Futuro', 'description' => 'Ogni categoria curata, un solo standard.', 'date' => '2026', 'image' => '', 'video' => '', 'icon' => 'flag', 'category' => 'primary', 'icon_color' => '' ],
        ],
        'tl_layout'  => 'alt',
        'tl_reveal'  => 'sides',
        'tl_theme'   => 'paper',
        'tl_card'    => 'bubble',
        'tl_thread'  => 'solid2',
        'tl_node'    => 'icon',
        'tl_color'   => 'cat',
        'tl_media'   => 'on',
        'tl_density' => 'comfy',
        'tl_line'    => 'scroll',
        'tl_transparent' => false,
        // Personalizzazione (override; '' o 0 = default variante)
        'tl_rail_color' => '', 'tl_rail_w' => 0, 'tl_fill_from' => '', 'tl_fill_to' => '',
        'tl_node_size' => 0, 'tl_node_border' => 0,
        'tl_card_bg' => '', 'tl_card_radius' => 0, 'tl_card_maxw' => 0, 'tl_card_pad' => 0,
        'tl_media_ratio' => 'auto', 'tl_media_h' => 0, 'tl_media_fit' => 'cover', 'tl_media_radius' => 0, 'tl_media_bar' => true,
        'tl_title_size' => 0, 'tl_title_weight' => '', 'tl_title_color' => '', 'tl_title_family' => '',
        'tl_text_size' => 0, 'tl_text_color' => '', 'tl_text_lh' => 0, 'tl_text_align' => 'left', 'tl_text_family' => '',
        'tl_yr_size' => 0, 'tl_yr_color' => '', 'tl_yr_family' => '',
        'tl_show_tag' => true, 'tl_tag_color' => '',
        'h_card_width' => '268',
        'border'                => [],
        'border_hover'          => [],
        'border_hover_duration' => 300,
    ];

    public function get_controls() { return []; }

    /** Stampa il foglio di stile condiviso una sola volta per richiesta. */
    private function print_shared_css() {
        static $done = false;
        if ( $done ) { return; }
        $done = true;
        $path = OLO_PATH . 'assets/css/timeline-super.css';
        if ( file_exists( $path ) ) {
            echo '<style id="olo-tlsuper-css">' . file_get_contents( $path ) . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped,WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- static stylesheet bundled with the plugin, read from a fixed OLO_PATH location
        }
    }

    /** Migra le vecchie chiavi (raw, prima del merge coi default). */
    private function migrate( $raw ) {
        if ( ! is_array( $raw ) ) { return $raw; }
        if ( ! isset( $raw['tl_layout'] ) && isset( $raw['layout'] ) ) {
            $map = [ 'vertical-center' => 'alt', 'vertical-left' => 'one', 'vertical-right' => 'one', 'horizontal' => 'horizontal' ];
            $raw['tl_layout'] = $map[ $raw['layout'] ] ?? 'alt';
        }
        if ( ! isset( $raw['tl_node'] ) && isset( $raw['marker_type'] ) ) {
            $map = [ 'dot' => 'dot', 'icon' => 'icon', 'number' => 'num' ];
            $raw['tl_node'] = $map[ $raw['marker_type'] ] ?? 'icon';
        }
        if ( ! isset( $raw['tl_thread'] ) && isset( $raw['line_style'] ) ) {
            $map = [ 'solid' => 'solid2', 'dashed' => 'dash', 'dotted' => 'dot' ];
            $raw['tl_thread'] = $map[ $raw['line_style'] ] ?? 'solid2';
        }
        if ( ! isset( $raw['tl_line'] ) && isset( $raw['line_progress'] ) ) {
            $raw['tl_line'] = ! empty( $raw['line_progress'] ) ? 'scroll' : 'solid';
        }
        return $raw;
    }

    private function parse_items( $raw ) {
        if ( ! is_array( $raw ) ) { return []; }
        $items = [];
        foreach ( $raw as $item ) {
            if ( ! is_array( $item ) ) { continue; }
            $items[] = [
                'title'       => $item['title'] ?? '',
                'tag'         => $item['tag'] ?? '',
                'description' => $item['description'] ?? '',
                'date'        => $item['date'] ?? '',
                'image'       => $item['image'] ?? '',
                'video'       => $item['video'] ?? '',
                'icon'        => $item['icon'] ?? 'star',
                'category'    => $item['category'] ?? 'primary',
                'icon_color'  => $item['icon_color'] ?? '',
            ];
        }
        return $items;
    }

    private function cat_class( $cat ) {
        $allow = [ 'primary', 'secondary', 'accent', 'success', 'warning', 'info' ];
        return in_array( $cat, $allow, true ) ? $cat : 'primary';
    }

    /** Override fini → custom property --tl-* sul root (vuoto/0 = usa default variante). */
    private function custom_style( $s ) {
        $v = [];
        $color = function ( $k, $css ) use ( $s, &$v ) {
            $raw = $s[ $k ] ?? '';
            if ( $raw === '' ) { return; }
            $c = $this->safe_color_css( $raw );
            if ( $c !== '' ) { $v[] = $css . ':' . $c; }
        };
        $px = function ( $k, $css ) use ( $s, &$v ) {
            $n = intval( $s[ $k ] ?? 0 );
            if ( $n > 0 ) { $v[] = $css . ':' . $n . 'px'; }
        };
        // Dual-format radius: Number legacy E oggetto {tl,tr,br,bl}; '' (zero/vuoto) = usa default variante.
        $rad = function ( $k, $css ) use ( $s, &$v ) {
            $r = $this->build_border_radius_css( $s[ $k ] ?? 0 );
            if ( $r !== '' ) { $v[] = $css . ':' . $r; }
        };
        // font-family: valore verbatim ripulito (no CSS injection); l'attributo style è poi esc_attr nel render.
        $str = function ( $k, $css ) use ( $s, &$v ) {
            $raw = trim( (string) ( $s[ $k ] ?? '' ) );
            $raw = preg_replace( '/[^A-Za-z0-9 ,"\'\-]/', '', $raw );
            if ( $raw !== '' ) { $v[] = $css . ':' . $raw; }
        };
        $color( 'tl_rail_color', '--tl-rail-color' );
        $px( 'tl_rail_w', '--tl-rail-w' );
        $color( 'tl_fill_from', '--tl-fill-from' );
        $color( 'tl_fill_to', '--tl-fill-to' );
        $px( 'tl_node_size', '--tl-node-size' );
        $px( 'tl_node_border', '--tl-node-bd' );
        $color( 'tl_card_bg', '--tl-card-bg' );
        $rad( 'tl_card_radius', '--tl-card-radius' );
        $px( 'tl_card_maxw', '--tl-card-maxw' );
        $px( 'tl_card_pad', '--tl-card-pad' );
        $ratio = $s['tl_media_ratio'] ?? 'auto';
        if ( $ratio !== 'auto' && preg_match( '#^\d+/\d+$#', $ratio ) ) {
            $v[] = '--tl-media-ar:' . $ratio;
            $v[] = '--tl-media-h:auto';
        } else {
            $px( 'tl_media_h', '--tl-media-h' );
        }
        $fit = $s['tl_media_fit'] ?? 'cover';
        if ( in_array( $fit, [ 'contain', 'fill', 'none' ], true ) ) { $v[] = '--tl-media-fit:' . $fit; }
        $rad( 'tl_media_radius', '--tl-media-radius' );
        $px( 'tl_title_size', '--tl-title-size' );
        $w = intval( $s['tl_title_weight'] ?? 0 );
        if ( $w > 0 ) { $v[] = '--tl-title-weight:' . $w; }
        $color( 'tl_title_color', '--tl-title-color' );
        $px( 'tl_text_size', '--tl-text-size' );
        $color( 'tl_text_color', '--tl-text-color' );
        $lh = floatval( $s['tl_text_lh'] ?? 0 );
        if ( $lh > 0 ) { $v[] = '--tl-text-lh:' . rtrim( rtrim( number_format( $lh, 2, '.', '' ), '0' ), '.' ); }
        $al = $s['tl_text_align'] ?? 'left';
        if ( in_array( $al, [ 'center', 'right' ], true ) ) { $v[] = '--tl-text-align:' . $al; }
        $px( 'tl_yr_size', '--tl-yr-size' );
        $color( 'tl_yr_color', '--tl-yr-color' );
        $color( 'tl_tag_color', '--tl-tag-color' );
        $str( 'tl_title_family', '--tl-title-family' );
        $str( 'tl_text_family', '--tl-text-family' );
        $str( 'tl_yr_family', '--tl-yr-family' );
        return implode( ';', $v );
    }

    /** Contenuto del nodo: icona (render_icon_html) + pip + label (num/anno). */
    private function node_inner( $item, $i, $node, $mono ) {
        $icon = $item['icon'] ?: 'star';
        $svg  = $this->render_icon_html( $icon, 0.9 );
        $lab  = '';
        if ( $node === 'num' )  { $lab = sprintf( '%02d', $i + 1 ); }
        if ( $node === 'year' ) { $lab = esc_html( $item['date'] ); }
        return $svg . '<span class="pip"></span><span class="lab">' . $lab . '</span>';
    }

    /** Blocco media della card (immagine/video o placeholder a strisce). */
    private function media_html( $item ) {
        $html = '<div class="it-media"><span class="bar"></span>';
        if ( ! empty( $item['video'] ) ) {
            $html .= $this->get_video_embed( $item['video'] );
        } elseif ( ! empty( $item['image'] ) ) {
            $html .= '<img src="' . esc_url( $item['image'] ) . '" alt="' . esc_attr( wp_strip_all_tags( $item['title'] ) ) . '" loading="lazy" />';
        } else {
            $html .= '<span class="ph">' . esc_html( $item['tag'] ?: $item['title'] ) . '</span>';
        }
        $html .= '</div>';
        return $html;
    }

    private function get_video_embed( $url ) {
        $url = trim( $url );
        if ( preg_match( '/(?:youtube\.com\/(?:watch\?v=|embed\/|shorts\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $url, $m ) ) {
            return '<iframe src="https://www.youtube-nocookie.com/embed/' . esc_attr( $m[1] ) . '" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen loading="lazy"></iframe>';
        }
        if ( preg_match( '/vimeo\.com\/(\d+)/', $url, $m ) ) {
            return '<iframe src="https://player.vimeo.com/video/' . esc_attr( $m[1] ) . '?dnt=1" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen loading="lazy"></iframe>';
        }
        return '<video controls preload="metadata"><source src="' . esc_url( $url ) . '" type="video/mp4"></video>';
    }

    public function render( $settings ) {
        $raw   = $this->migrate( is_array( $settings ) ? $settings : [] );
        $s     = wp_parse_args( $raw, $this->defaults );
        $items = $this->parse_items( $s['items'] );
        $count = count( $items );
        if ( $count === 0 ) { return ''; }

        $layout = in_array( $s['tl_layout'], [ 'alt', 'one', 'horizontal', 'navigator', 'schedule' ], true ) ? $s['tl_layout'] : 'alt';
        $theme  = in_array( $s['tl_theme'], [ 'paper', 'night', 'neon', 'blue' ], true ) ? $s['tl_theme'] : 'paper';
        $mono   = ( $s['tl_color'] === 'mono' );
        $uid    = 'olo-tl-' . wp_rand( 10000, 99999 );

        ob_start();
        $this->print_shared_css();

        $root_cls = 'olo-tlsuper';
        if ( $theme !== 'paper' ) { $root_cls .= ' t-' . $theme; }
        if ( $mono ) { $root_cls .= ' mono'; }
        if ( ! empty( $s['tl_transparent'] ) ) { $root_cls .= ' bg-transparent'; }
        if ( empty( $s['tl_media_bar'] ) ) { $root_cls .= ' tl-nobar'; }
        if ( empty( $s['tl_show_tag'] ) ) { $root_cls .= ' tl-notag'; }
        // stile card sul root (oltre che su .super): serve a navigatore/orizzontale
        if ( $s['tl_card'] !== 'bubble' ) { $root_cls .= ' card-' . sanitize_html_class( $s['tl_card'] ); }
        $cstyle = $this->custom_style( $s );
        echo '<div class="' . esc_attr( $root_cls . ' ' . $uid ) . '" id="' . esc_attr( $uid ) . '"' . ( $cstyle ? ' style="' . esc_attr( $cstyle ) . '"' : '' ) . '>';

        if ( $layout === 'horizontal' ) {
            $this->render_horizontal( $items, $s, $mono );
        } elseif ( $layout === 'navigator' ) {
            $this->render_navigator( $items, $s );
        } elseif ( $layout === 'schedule' ) {
            $this->render_schedule( $items, $s, $uid );
        } else {
            $this->render_vertical( $items, $s, $layout, $mono );
        }

        echo '</div>';

        // Bordo (sistema standard) sulla card
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid} .it-card", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid} .it-card", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) { echo ".{$uid} .it-card{{$border_css}}"; } // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS built by Olo_Tile_Base::build_border_css() from sanitized border settings; $uid is internally generated
            echo $border_hover_css . $border_effect_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS built by Olo_Tile_Base border helpers from sanitized border settings
        }

        return ob_get_clean();
    }

    /* ───────── VERTICALE (alt · one) ───────── */
    private function render_vertical( $items, $s, $layout, $mono ) {
        $cls = 'super js';
        $cls .= ( $s['tl_line'] === 'solid' ) ? ' line-solid' : ' line-scroll';
        $cls .= ' ing-' . sanitize_html_class( $s['tl_reveal'] );
        if ( $s['tl_reveal'] !== 'sides' ) { $cls .= ' ing-anim'; }
        if ( $layout === 'one' ) { $cls .= ' one'; }
        if ( $s['tl_card'] !== 'bubble' )   { $cls .= ' card-' . sanitize_html_class( $s['tl_card'] ); }
        if ( $s['tl_thread'] !== 'solid2' ) { $cls .= ' thread-' . sanitize_html_class( $s['tl_thread'] ); }
        if ( $s['tl_node'] !== 'icon' )     { $cls .= ' node-' . sanitize_html_class( $s['tl_node'] ); }
        if ( $s['tl_media'] === 'off' )     { $cls .= ' no-media'; }
        if ( $s['tl_density'] === 'compact' ) { $cls .= ' dense'; }
        if ( $mono ) { $cls .= ' mono'; }

        $node = $s['tl_node'];
        echo '<div class="' . esc_attr( $cls ) . '">';
        echo '<span class="rail"></span><span class="rail-fill"></span>';

        foreach ( $items as $i => $item ) {
            $cat   = $mono ? 'primary' : $this->cat_class( $item['category'] );
            $style = ( ! $mono && ! empty( $item['icon_color'] ) ) ? ' style="--cat:' . esc_attr( $this->safe_color_css( $item['icon_color'] ) ) . '"' : '';
            echo '<div class="it cat-' . esc_attr( $cat ) . '"' . $style . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $style built above from a literal CSS prefix plus an esc_attr( safe_color_css() ) value
            echo '<span class="it-node">' . $this->node_inner( $item, $i, $node, $mono ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- node markup from node_inner(): render_icon_html() helper output plus sprintf('%02d')/esc_html() label
            echo '<div class="it-date"><span class="yr">' . esc_html( $item['date'] ) . '</span><span class="ph">' . esc_html( $item['tag'] ) . '</span><span class="st" data-st>&mdash;</span></div>';
            echo '<div class="it-card">';
            echo $this->media_html( $item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- media markup built by media_html() exclusively from esc_url()/esc_attr()/esc_html() values
            echo '<div class="it-body">';
            if ( $item['tag'] !== '' )         { echo '<span class="it-tag">' . esc_html( $item['tag'] ) . '</span>'; }
            if ( $item['title'] !== '' )       { echo '<h4>' . esc_html( $item['title'] ) . '</h4>'; }
            if ( $item['description'] !== '' ) { echo '<p>' . esc_html( $item['description'] ) . '</p>'; }
            echo '</div></div></div>';
        }

        if ( $s['tl_thread'] === 'comet' ) { echo '<span class="comet"></span>'; }
        echo '</div>';

        $this->vertical_script( $s['tl_line'] !== 'solid' );
    }

    private function vertical_script( $scroll ) {
        // Scoping per istanza via document.currentScript.parentNode (il wrapper .olo-tlsuper).
        ?>
        <script>(function(){
            var sup = document.currentScript.parentNode.querySelector('.super');
            if(!sup){return;}
            var rfill = sup.querySelector('.rail-fill');
            var its = [].slice.call(sup.querySelectorAll('.it'));
            var scroll = sup.classList.contains('line-scroll');
            function inView(el){ var r=el.getBoundingClientRect(); var vh=window.innerHeight; if(r.top>=vh*0.92){return false;} if(r.bottom<=0){return false;} return true; }
            function reveal(){ for(var i=0;i<its.length;i++){ if(inView(its[i])){ its[i].classList.add('in'); } } }
            function update(){
                reveal();
                if(!scroll){ return; }
                var r = sup.getBoundingClientRect();
                var play = window.innerHeight*0.56;
                var fill = Math.max(0, Math.min(r.height, play - r.top));
                if(rfill){ rfill.style.height = Math.max(0, fill-6) + 'px'; }
                var last = -1;
                for(var i=0;i<its.length;i++){
                    var node = its[i].querySelector('.it-node');
                    var nr = node.getBoundingClientRect();
                    var c = (nr.top + nr.height/2) - r.top;
                    var on = c <= fill;
                    its[i].classList.toggle('reached', on);
                    its[i].classList.remove('active');
                    if(on){ last = i; }
                }
                for(var j=0;j<its.length;j++){
                    var st = its[j].querySelector('[data-st]');
                    if(st){ if(j < last){ st.textContent='Fatto'; } else if(j === last){ st.textContent='Fatto'; } else { st.textContent='In arrivo'; } }
                }
                if(last>=0){ its[last].classList.add('active'); var sl=its[last].querySelector('[data-st]'); if(sl){ sl.textContent='In corso'; } }
            }
            window.addEventListener('scroll', update, {passive:true});
            window.addEventListener('resize', update);
            update();
        })();</script>
        <?php
    }

    /* ───────── SCHEDULE / RUN-OF-SHOW (orario a sx · filo · nodo) ───────── */
    private function render_schedule( $items, $s, $uid ) {
        $accent = $this->safe_color_css( $s['tl_yr_color'] ?? '' ) ?: 'var(--olo-color-primary, #e0afca)';
        $title  = $this->safe_color_css( $s['tl_title_color'] ?? '' ) ?: 'var(--olo-color-text-emphasis, #f3e9ef)';
        $text   = $this->safe_color_css( $s['tl_text_color'] ?? '' ) ?: 'var(--olo-color-text-muted, #94809a)';
        $rail   = $this->safe_color_css( $s['tl_rail_color'] ?? '' ) ?: 'var(--olo-color-border, rgba(255,255,255,.16))';
        $halo   = 'var(--olo-color-background, #241430)';
        $disp   = 'var(--olo-font-family-heading, Georgia, "Times New Roman", serif)';

        echo '<div class="olo-tl-sched">';
        foreach ( $items as $item ) {
            echo '<div class="olo-tl-slot">';
            echo '<div class="olo-tl-slot__t">' . esc_html( $item['date'] ) . '</div>';
            echo '<div class="olo-tl-slot__c">';
            if ( $item['title'] !== '' )       { echo '<h3>' . esc_html( $item['title'] ) . '</h3>'; }
            if ( $item['description'] !== '' ) { echo '<p>' . esc_html( $item['description'] ) . '</p>'; }
            echo '</div></div>';
        }
        echo '</div>';

        // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from safe_color_css()-validated colors with fixed var() fallbacks, fixed font-stack literals and the internally generated $uid.
        echo '<style>'
           . ".{$uid} .olo-tl-sched{max-width:760px;margin:0 auto;position:relative;}"
           . ".{$uid} .olo-tl-sched::before{content:\"\";position:absolute;left:90px;top:8px;bottom:8px;width:1px;background:{$rail};}"
           . ".{$uid} .olo-tl-slot{display:grid;grid-template-columns:80px 1fr;gap:30px;padding:0 0 28px;position:relative;}"
           . ".{$uid} .olo-tl-slot:last-child{padding-bottom:0;}"
           . ".{$uid} .olo-tl-slot__t{font-family:{$disp};font-size:20px;color:{$accent};text-align:right;line-height:1.25;}"
           . ".{$uid} .olo-tl-slot__c{position:relative;padding-left:30px;}"
           . ".{$uid} .olo-tl-slot__c::before{content:\"\";position:absolute;left:-25px;top:7px;width:11px;height:11px;border-radius:50%;background:{$accent};box-shadow:0 0 0 4px {$halo};}"
           . ".{$uid} .olo-tl-slot__c h3{font-family:{$disp};font-size:20px;font-weight:400;margin:0 0 5px;color:{$title};line-height:1.2;}"
           . ".{$uid} .olo-tl-slot__c p{font-size:14px;color:{$text};margin:0;line-height:1.6;}"
           . "@media(max-width:600px){.{$uid} .olo-tl-sched::before{left:0;}.{$uid} .olo-tl-slot{grid-template-columns:1fr;gap:6px;}.{$uid} .olo-tl-slot__t{text-align:left;}.{$uid} .olo-tl-slot__c{padding-left:24px;}.{$uid} .olo-tl-slot__c::before{left:-24px;}}"
           . '</style>';
        // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
    }

    /* ───────── ORIZZONTALE ───────── */
    private function render_horizontal( $items, $s, $mono ) {
        $cw = intval( $s['h_card_width'] ) ?: 268;
        echo '<div class="hwrap">';
        echo '<div class="hbar"><span class="ht"></span><div class="hnav">';
        echo '<button class="prev" type="button" aria-label="Precedente"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg></button>';
        echo '<button class="next" type="button" aria-label="Successivo"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg></button>';
        echo '</div></div>';
        echo '<div class="hscroll"><div class="htrack">';
        foreach ( $items as $i => $item ) {
            $cat = $mono ? 'primary' : $this->cat_class( $item['category'] );
            $style = ' style="width:' . $cw . 'px"';
            $style2 = ( ! $mono && ! empty( $item['icon_color'] ) ) ? ';--cat:' . esc_attr( $this->safe_color_css( $item['icon_color'] ) ) : '';
            echo '<div class="hit cat-' . esc_attr( $cat ) . '" style="width:' . (int) $cw . 'px' . $style2 . '">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $style2 built above from a literal CSS prefix plus an esc_attr( safe_color_css() ) value
            echo '<span class="hit-node">' . $this->render_icon_html( $item['icon'] ?: 'star', 0.8 ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- icon markup generated by the render_icon_html() helper (sanitized SVG / esc_attr()'d uk-icon attrs)
            echo '<span class="hit-date">' . esc_html( $item['date'] ) . '</span>';
            echo '<div class="hit-card">';
            if ( $item['tag'] !== '' )   { echo '<span class="t">' . esc_html( $item['tag'] ) . '</span>'; }
            if ( $item['title'] !== '' ) { echo '<h4>' . esc_html( $item['title'] ) . '</h4>'; }
            if ( $item['description'] !== '' ) { echo '<p>' . esc_html( $item['description'] ) . '</p>'; }
            echo '</div></div>';
        }
        echo '</div></div></div>';
        ?>
        <script>(function(){
            var root = document.currentScript.parentNode;
            var hs = root.querySelector('.hscroll'); if(!hs){return;}
            function step(){ var c=hs.querySelector('.hit'); if(c){ return c.getBoundingClientRect().width; } return 280; }
            var p = root.querySelector('.hnav .prev'), n = root.querySelector('.hnav .next');
            if(p){ p.addEventListener('click', function(){ hs.scrollBy({left:-step(), behavior:'smooth'}); }); }
            if(n){ n.addEventListener('click', function(){ hs.scrollBy({left:step(), behavior:'smooth'}); }); }
        })();</script>
        <?php
    }

    /* ───────── NAVIGATORE (asse date + post singolo) ───────── */
    private function render_navigator( $items, $s ) {
        echo '<div class="navd">';
        echo '<div class="nv-nav">';
        echo '<button class="nv-arrow nv-prev" type="button" aria-label="Precedente"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg></button>';
        echo '<div class="nv-viewport"><div class="nv-track"><span class="nv-base"></span><span class="nv-fill"></span>';
        foreach ( $items as $i => $item ) {
            echo '<div class="nv-step"'
                . ' data-yr="' . esc_attr( $item['date'] ) . '"'
                . ' data-tag="' . esc_attr( $item['tag'] ) . '"'
                . ' data-title="' . esc_attr( $item['title'] ) . '"'
                . ' data-text="' . esc_attr( $item['description'] ) . '"'
                . ' data-img="' . esc_attr( esc_url( $item['image'] ) ) . '">'
                . '<span class="l">' . esc_html( $item['date'] ) . '</span><span class="d"></span></div>';
        }
        echo '</div></div>';
        echo '<button class="nv-arrow nv-next" type="button" aria-label="Successivo"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg></button>';
        echo '</div>';
        echo '<div class="nv-stage"><div class="nv-post">';
        echo '<div class="nv-media"><img alt="" /><span class="nyr nv-yr"></span><span class="nph nv-ph"></span></div>';
        echo '<div class="nv-body"><div class="m"><span class="tg nv-tag"></span><span class="dt nv-date"></span></div>';
        echo '<h2 class="nv-title"></h2><p class="nv-text"></p></div>';
        echo '</div></div>';
        echo '<div class="nv-counter"></div>';
        echo '</div>';
        ?>
        <script>(function(){
            var root = document.currentScript.parentNode;
            var track = root.querySelector('.nv-track'); if(!track){return;}
            var fill = root.querySelector('.nv-fill');
            var steps = [].slice.call(root.querySelectorAll('.nv-step'));
            var post = root.querySelector('.nv-post');
            var STEP = 168, idx = 0, n = steps.length;
            function set(sel, val){ var e=root.querySelector(sel); if(e){ e.textContent = val ? val : ''; } }
            function layout(){
                var vp = track.parentElement, vpW = vp.clientWidth, trackW = n*STEP;
                var desired = vpW/2 - (idx*STEP + STEP/2), min = Math.min(0, vpW - trackW);
                track.style.transform = 'translateX(' + Math.max(min, Math.min(0, desired)) + 'px)';
                if(fill){ fill.style.width = (idx*STEP + STEP/2) + 'px'; }
                for(var i=0;i<n;i++){ steps[i].classList.toggle('done', i<=idx); steps[i].classList.toggle('sel', i===idx); }
                var pb=root.querySelector('.nv-prev'), nb=root.querySelector('.nv-next');
                if(pb){ pb.disabled = (idx===0); } if(nb){ nb.disabled = (idx===n-1); }
            }
            function render(){
                var st = steps[idx]; if(!st){return;}
                set('.nv-yr', st.getAttribute('data-yr'));
                set('.nv-ph', 'archivio · ' + st.getAttribute('data-yr'));
                set('.nv-tag', st.getAttribute('data-tag'));
                set('.nv-date', st.getAttribute('data-yr'));
                set('.nv-title', st.getAttribute('data-title'));
                set('.nv-text', st.getAttribute('data-text'));
                var img = root.querySelector('.nv-media img'); var src = st.getAttribute('data-img');
                if(img){ if(src){ img.src = src; img.style.display='block'; } else { img.removeAttribute('src'); img.style.display='none'; } }
                var cnt = root.querySelector('.nv-counter'); if(cnt){ cnt.innerHTML = '<b>' + (idx+1) + '</b> / ' + n; }
            }
            function go(i){
                if(i<0){ i=0; } if(i>n-1){ i=n-1; }
                if(i!==idx){ idx=i; post.classList.add('out'); setTimeout(function(){ render(); post.classList.remove('out'); }, 200); }
                layout();
            }
            var pb=root.querySelector('.nv-prev'), nb=root.querySelector('.nv-next');
            if(pb){ pb.addEventListener('click', function(){ go(idx-1); }); }
            if(nb){ nb.addEventListener('click', function(){ go(idx+1); }); }
            for(var i=0;i<n;i++){ (function(k){ steps[k].addEventListener('click', function(){ go(k); }); })(i); }
            window.addEventListener('resize', layout);
            render(); layout();
        })();</script>
        <?php
    }
}
