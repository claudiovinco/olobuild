<?php
/**
 * Tile «Mega Menu / Site Header» (slug: oloheader) — famiglia Header / Navigation.
 *
 * Porting 1:1 della demo `menu-demo.html` (pacchetto OLOtheme). Barra "pill"
 * flottante con brand, nav primaria + una voce mega-menu (rail + colonne prodotti
 * + banner footer), link featured, ricerca, selettore lingua, CTA e mobile sheet.
 * Markup, CSS e JS sono lift-and-shift dalla demo (fonte di verità) e
 * parametrizzati dai controlli definiti in src/config/elements/oloheader.js.
 *
 * Il MEGA-MENU è ancorato alla barra (non al trigger): .olo-sh-item[data-mega]
 * usa position:static e il pannello si centra sulla barra → non esce dal viewport.
 * Apertura su hover (con ritardo di chiusura ~160ms) e fallback click per il touch.
 *
 * I prodotti del mega sono un repeater piatto raggruppato per colonna tramite il
 * campo `group` (OLObuild non ha repeater annidati): il render ricostruisce le
 * colonne preservando l'ordine di prima comparsa dei gruppi.
 *
 * ⚠️ Il logo `olollottie-q.png` è il marchio OLOlottie (non esiste "OLOcalendar").
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_OloHeader_Tile extends Olo_Tile_Base {

    protected $type     = 'oloheader';
    protected $name     = 'Mega Menu / Site Header';
    protected $icon     = 'dashicons-menu-alt3';
    protected $category = 'header';

    /** Traccia il caricamento (una volta per richiesta) dei font della demo. */
    private static $fonts_loaded = false;

    public function get_controls() { return []; }

    /**
     * Carica i font della demo (Manrope + JetBrains Mono) una sola volta.
     * Se il tema li fornisce già il fallback system-ui resta comunque coerente.
     */
    private function render_fonts() {
        if ( self::$fonts_loaded ) { return; }
        self::$fonts_loaded = true;
        // Font self-hosted via Olo_Font_Host: i woff2 sono scaricati una volta dal server
        // e serviti localmente. Nessuna richiesta del visitatore a Google (coerente con la
        // privacy dichiarata dal plugin) e niente <link>/<preconnect> verso il CDN.
        if ( class_exists( 'Olo_Font_Host' ) ) {
            $css = Olo_Font_Host::get_font_face_css( [ 'Manrope', 'JetBrains Mono' ], '400;500;600;700;800' );
            if ( $css ) {
                // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- @font-face CSS generato internamente da Olo_Font_Host (URL woff2 locali esc_url'd a monte); l'escape romperebbe il CSS valido
                echo '<style id="olo-oloheader-fonts">' . $css . '</style>';
            }
        }
    }

    /**
     * Default mirror di src/config/elements/oloheader.js (logo via OLO_URL come
     * fallback frontend; le istanze reali salvano i propri valori dal config JS).
     */
    public function get_defaults() {
        $logo = function ( $f ) { return OLO_URL . 'assets/img/menu/' . $f; };
        return [
            // Brand
            'brand_logo'        => $logo( 'olotheme-orizz.png' ),
            'brand_logo_white'  => '',
            'brand_height'      => 25,
            'brand_url'         => '/',
            // Stile barra
            'bar_style'         => 'pill',
            'bar_max_width'     => 1200,
            'bar_top_offset'    => 22,
            'bar_sticky'        => true,
            // Movimento "smart sticky" (come Mega Menu): nascondi scorrendo giù,
            // riappari subito scorrendo su; sfondo/ombra/riduzione allo stick.
            'sticky_show_on_up' => true,
            'sticky_bg'         => '',
            'sticky_shadow'     => true,
            'sticky_shrink'     => false,
            'bar_blur'          => true,
            'bar_radius'        => 100,
            'bar_shadow'        => 'md',
            'bar_bg'            => '#FFFFFF',
            'bar_text'          => '#5A6076',
            'bar_text_hover'    => '#1F2330',
            'mobile_breakpoint' => 1040,
            // Nav primaria
            'nav_items'         => [
                [ 'label' => 'Prodotti', 'url' => '#',        'type' => 'mega' ],
                [ 'label' => 'Temi',     'url' => '/temi',    'type' => 'link' ],
                [ 'label' => 'Prezzi',   'url' => '/prezzi',  'type' => 'link' ],
                [ 'label' => 'Risorse',  'url' => '/risorse', 'type' => 'link' ],
            ],
            // Mega rail
            'rail_show'         => true,
            'rail_badge'        => 'Ecosistema OLO',
            'rail_title'        => 'Un motore, otto prodotti',
            'rail_text'         => 'Stessa anima olonica per costruire, gestire e far crescere il tuo sito WordPress. Senza SaaS, senza lock-in.',
            'rail_cta1_label'   => 'Scopri la Suite OLO',
            'rail_cta1_url'     => '/suite',
            'rail_cta2_label'   => 'Confronta i piani',
            'rail_cta2_url'     => '/prezzi',
            'rail_bg'           => '',
            // Mega prodotti
            'mega_columns'      => 2,
            'mega_products'     => [
                [ 'group' => 'Costruisci',        'logo' => $logo( 'olobuild-q.png' ),    'name' => 'OLObuild',    'desc' => 'Page builder · 187 tile drag & drop', 'url' => '/prodotti/olobuild',    'soon' => false ],
                [ 'group' => 'Costruisci',        'logo' => $logo( 'olotheme-q.png' ),    'name' => 'OLOtheme',    'desc' => 'Temi pronti per ogni settore',        'url' => '/prodotti/olotheme',    'soon' => false ],
                [ 'group' => 'Costruisci',        'logo' => $logo( 'olollottie-q.png' ),  'name' => 'OLOlottie',   'desc' => 'Animazioni Lottie, senza codice',     'url' => '/prodotti/olollottie',  'soon' => false ],
                [ 'group' => 'Gestisci & cresci', 'logo' => $logo( 'olobooking-q.png' ),  'name' => 'OLObooking',  'desc' => 'Prenotazioni · 6 verticali',          'url' => '/prodotti/olobooking',  'soon' => false ],
                [ 'group' => 'Gestisci & cresci', 'logo' => $logo( 'ololang-q.png' ),     'name' => 'OLOlang',     'desc' => 'Multilingua nativo · 28 lingue',      'url' => '/prodotti/ololang',     'soon' => false ],
                [ 'group' => 'Gestisci & cresci', 'logo' => $logo( 'olosecurity-q.png' ), 'name' => 'OLOsecurity', 'desc' => 'Sicurezza, firewall e backup',        'url' => '/prodotti/olosecurity', 'soon' => false ],
            ],
            // Mega footer
            'mega_footer_show'  => true,
            'mega_footer_logos' => [
                [ 'logo' => $logo( 'olotour-q.png' ) ],
                [ 'logo' => $logo( 'olotutor-q.png' ) ],
            ],
            'mega_footer_title' => 'In arrivo · OLOtour & OLOtutor',
            'mega_footer_text'  => 'Tour virtuali 360° e corsi online. Pagina prodotto già online.',
            'mega_footer_cta_label' => "Vedi l'anteprima",
            'mega_footer_cta_url'   => '/prodotti',
            // Featured
            'featured_show'     => true,
            'featured_icon'     => 'bolt',
            'featured_label'    => 'Suite OLO',
            'featured_url'      => '/suite',
            // Ricerca
            'search_show'       => true,
            'search_placeholder'=> 'Cerca temi, prodotti, guide…',
            'search_url'        => '/cerca',
            'search_shortcuts'  => [
                [ 'label' => 'Temi popolari',  'url' => '/temi' ],
                [ 'label' => 'OLObuild',       'url' => '/prodotti/olobuild' ],
                [ 'label' => 'Prezzi',         'url' => '/prezzi' ],
                [ 'label' => 'Documentazione', 'url' => '/risorse' ],
            ],
            // Lingua
            'lang_show'         => true,
            'lang_globe'        => true,
            'lang_current'      => 'it',
            'lang_bind_ololang' => true,
            'languages'         => [
                [ 'code' => 'it', 'label' => 'Italiano',  'url' => '/' ],
                [ 'code' => 'en', 'label' => 'English',   'url' => '/en/' ],
                [ 'code' => 'es', 'label' => 'Español',   'url' => '/es/' ],
                [ 'code' => 'fr', 'label' => 'Français',  'url' => '/fr/' ],
                [ 'code' => 'de', 'label' => 'Deutsch',   'url' => '/de/' ],
                [ 'code' => 'pt', 'label' => 'Português', 'url' => '/pt/' ],
            ],
            // CTA
            'cta_show'          => true,
            'cta_label'         => 'Sandbox',
            'cta_url'           => '/sandbox',
            'cta_style'         => 'navy',
            // Interazioni
            'open_mega_on'      => 'hover',
            'mega_animation'    => 'fade-slide',
            'close_on_esc'      => true,
        ];
    }

    /** Mappa ombra → box-shadow (palette demo). */
    private function shadow_css( $key ) {
        switch ( $key ) {
            case 'sm': return '0 2px 6px -2px rgba(10,20,40,.06)';
            case 'lg': return '0 28px 70px -22px rgba(27,42,78,.40)';
            case 'none': return 'none';
            case 'md':
            default:   return '0 8px 30px -12px rgba(10,20,40,.18)';
        }
    }

    public function render( $settings ) {
        $s   = wp_parse_args( is_array( $settings ) ? $settings : [], $this->get_defaults() );
        $uid = 'olo-sh-' . wp_rand( 10000, 99999 );

        ob_start();
        $this->render_fonts();
        $this->render_css( $s, $uid );
        $this->render_html( $s, $uid );
        $this->render_js( $s, $uid );
        return ob_get_clean();
    }

    /* ─────────────────────────────────────────── CSS ── */

    private function render_css( $s, $uid ) {
        $bp        = max( 480, intval( $s['mobile_breakpoint'] ) ?: 1040 );
        $bp_up     = $bp + 1;
        $max_w     = max( 600, intval( $s['bar_max_width'] ) ?: 1200 );
        $offset    = max( 0, intval( $s['bar_top_offset'] ) );
        $radius    = max( 0, min( 100, intval( $s['bar_radius'] ) ) );
        $shadow    = $this->shadow_css( $s['bar_shadow'] ?? 'md' );
        $bg        = $this->safe_color_css( $s['bar_bg'] ) ?: '#FFFFFF';
        $text      = $this->safe_color_css( $s['bar_text'] ) ?: '#5A6076';
        $text_h    = $this->safe_color_css( $s['bar_text_hover'] ) ?: '#1F2330';
        $is_pill   = ( ( $s['bar_style'] ?? 'pill' ) === 'pill' );
        $sticky    = ! empty( $s['bar_sticky'] );
        $blur      = ! empty( $s['bar_blur'] );
        $rail_bg   = $this->safe_color_css( $s['rail_bg'] ?? '' );
        $cta_style = $s['cta_style'] ?? 'navy';
        $anim      = ( ( $s['mega_animation'] ?? 'fade-slide' ) !== 'none' );

        // Smart sticky (come Mega Menu): la barra scorre con la pagina e si aggancia
        // in alto (position:sticky); JS aggiunge .olo-sh-stuck (oltre soglia) e
        // .olo-sh-hidden (nascondi scorrendo giù, riappari scorrendo su).
        $sticky_bg     = $this->safe_color_css( $s['sticky_bg'] ?? '' );
        $sticky_shadow = ! empty( $s['sticky_shadow'] );
        $sticky_shrink = ! empty( $s['sticky_shrink'] );
        $brand_h       = max( 14, intval( $s['brand_height'] ) ?: 25 );
        // sticky → fixed in alto (robusto a qualsiasi annidamento; position:sticky
        // fallirebbe perché il wrapper della tile è alto quanto la barra). Il JS
        // nasconde scorrendo giù e fa riapparire scorrendo su. Off → in flusso.
        $hdr_pos   = $sticky ? 'fixed' : 'relative';
        $hdr_top   = $sticky ? ( $is_pill ? $offset : 0 ) : 0;

        // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS sotto è costruito solo da valori già sanitizzati: colori via safe_color_css(), interi via intval()/min/max, enum via ternari letterali; $uid è generato internamente.
        ?>
        <style>
        /* === OLOtheme Site Header (oloheader) — scope .<?php echo $uid; ?> === */
        .<?php echo $uid; ?>{
          --sh-ink:#1F2330; --sh-ink-2:<?php echo $text; ?>; --sh-ink-3:#8B91A1;
          --sh-line:#E2E5EC; --sh-line-2:#F0F2F6;
          --sh-paper:#FAFAF7; --sh-paper-2:<?php echo $bg; ?>;
          --sh-navy:#1B2A4E; --sh-navy-2:#0F1A33;
          --sh-royal:#1E88E5; --sh-royal-ink:#0D4A85;
          --sh-shadow-sm:0 2px 6px -2px rgba(10,20,40,.06);
          --sh-shadow:<?php echo $shadow; ?>;
          --sh-shadow-lg:0 28px 70px -22px rgba(27,42,78,.40);
          color:var(--sh-ink);
          font-family:"Manrope",system-ui,-apple-system,sans-serif;
          -webkit-font-smoothing:antialiased;
        }
        .<?php echo $uid; ?> *{box-sizing:border-box}
        .<?php echo $uid; ?> a{color:inherit;text-decoration:none}
        .<?php echo $uid; ?> button{font-family:inherit;cursor:pointer}

        /* container per fixed: non clippare il pannello */
        section:has(.<?php echo $uid; ?>),
        header.olo-site-header:has(.<?php echo $uid; ?>){overflow:visible}

        /* ── Header ── */
        .<?php echo $uid; ?> .olo-sh-hdr{
          position:<?php echo $hdr_pos; ?>;top:<?php echo intval( $hdr_top ); ?>px;left:0;right:0;z-index:60;
          padding:0 <?php echo $is_pill ? 22 : 0; ?>px;display:flex;justify-content:center;pointer-events:none;
          transition:transform .3s ease;
        }
        <?php if ( $sticky ) : ?>
        .<?php echo $uid; ?> .olo-sh-bar{transition:padding .25s ease,background .25s ease,box-shadow .25s ease}
        .<?php echo $uid; ?> .olo-sh-blogo{transition:height .25s ease}
        /* nascondi scorrendo giù / riappari scorrendo su */
        .<?php echo $uid; ?> .olo-sh-hdr.olo-sh-hidden{transform:translateY(-130%)}
        /* stato "agganciato" (oltre la soglia di scroll): sfondo/ombra/riduzione */
        <?php if ( $sticky_bg ) : ?>
        .<?php echo $uid; ?> .olo-sh-hdr.olo-sh-stuck .olo-sh-bar{background:<?php echo $sticky_bg; ?>}
        <?php endif; ?>
        <?php if ( $sticky_shadow ) : ?>
        .<?php echo $uid; ?> .olo-sh-hdr.olo-sh-stuck .olo-sh-bar{box-shadow:var(--sh-shadow-lg)}
        <?php endif; ?>
        <?php if ( $sticky_shrink ) : ?>
        .<?php echo $uid; ?> .olo-sh-hdr.olo-sh-stuck .olo-sh-bar{padding-top:7px;padding-bottom:7px;transition:padding .25s ease,background .25s ease,box-shadow .25s ease}
        .<?php echo $uid; ?> .olo-sh-hdr.olo-sh-stuck .olo-sh-blogo{height:<?php echo max( 12, (int) round( $brand_h * 0.82 ) ); ?>px;transition:height .25s ease}
        <?php endif; ?>
        <?php endif; ?>
        .<?php echo $uid; ?> .olo-sh-bar{
          pointer-events:auto;
          width:<?php echo $is_pill ? 'min(' . $max_w . 'px,100%)' : '100%'; ?>;
          <?php if ( ! $is_pill ) : ?>max-width:none;<?php endif; ?>
          background:var(--sh-paper-2);
          border-radius:<?php echo $is_pill ? $radius : 0; ?>px;
          box-shadow:var(--sh-shadow);
          display:flex;align-items:center;gap:8px;
          padding:11px 14px 11px 24px;position:relative;
          <?php if ( $blur ) : ?>backdrop-filter:saturate(140%) blur(8px);<?php endif; ?>
        }
        <?php if ( ! $is_pill ) : ?>
        .<?php echo $uid; ?> .olo-sh-bar{padding-left:max(24px,calc(50% - <?php echo intval( $max_w / 2 ); ?>px));padding-right:max(14px,calc(50% - <?php echo intval( $max_w / 2 ); ?>px));}
        <?php endif; ?>

        /* brand */
        .<?php echo $uid; ?> .olo-sh-brand{display:flex;align-items:center;gap:9px;padding-right:8px;flex-shrink:0}
        .<?php echo $uid; ?> .olo-sh-blogo{height:<?php echo max( 14, intval( $s['brand_height'] ) ?: 25 ); ?>px;width:auto;display:block}

        /* primary nav */
        .<?php echo $uid; ?> .olo-sh-nav{display:flex;align-items:center;gap:2px;margin-left:14px;list-style:none;padding:0}
        .<?php echo $uid; ?> .olo-sh-item{position:relative}
        .<?php echo $uid; ?> .olo-sh-item[data-mega]{position:static}/* mega ancorato alla barra */
        .<?php echo $uid; ?> .olo-sh-link{display:inline-flex;align-items:center;gap:6px;padding:10px 14px;border-radius:100px;font-size:14.5px;font-weight:600;color:var(--sh-ink-2);background:transparent;border:none;transition:background .14s,color .14s;white-space:nowrap}
        .<?php echo $uid; ?> .olo-sh-link:hover,.<?php echo $uid; ?> .olo-sh-link[aria-expanded="true"]{color:<?php echo $text_h; ?>;background:var(--sh-line-2)}
        .<?php echo $uid; ?> .olo-sh-chev{width:10px;height:10px;transition:transform .2s}
        .<?php echo $uid; ?> .olo-sh-link[aria-expanded="true"] .olo-sh-chev{transform:rotate(180deg)}

        .<?php echo $uid; ?> .olo-sh-divider{width:1px;height:26px;background:var(--sh-line);margin:0 8px;flex-shrink:0}

        /* featured */
        .<?php echo $uid; ?> .olo-sh-featured{display:inline-flex;align-items:center;gap:9px;padding:9px 14px;border-radius:100px;font-size:14.5px;font-weight:700;color:var(--sh-ink);transition:background .14s}
        .<?php echo $uid; ?> .olo-sh-featured:hover{background:var(--sh-line-2)}
        .<?php echo $uid; ?> .olo-sh-fic{width:30px;height:30px;border-radius:9px;display:grid;place-items:center;color:#fff;background:linear-gradient(135deg,var(--sh-royal),var(--sh-navy));box-shadow:0 6px 16px -6px rgba(30,136,229,.6)}
        .<?php echo $uid; ?> .olo-sh-fic svg{width:15px;height:15px}

        /* right cluster */
        .<?php echo $uid; ?> .olo-sh-right{margin-left:auto;display:flex;align-items:center;gap:6px;padding-left:8px}
        .<?php echo $uid; ?> .olo-sh-iconbtn{width:40px;height:40px;border-radius:100px;border:none;background:transparent;color:var(--sh-ink-2);display:grid;place-items:center;transition:background .14s,color .14s}
        .<?php echo $uid; ?> .olo-sh-iconbtn:hover{background:var(--sh-line-2);color:var(--sh-ink)}
        .<?php echo $uid; ?> .olo-sh-langbtn{display:inline-flex;align-items:center;gap:7px;padding:9px 12px;border-radius:100px;border:none;background:transparent;font-size:14px;font-weight:700;color:var(--sh-ink-2);transition:background .14s,color .14s}
        .<?php echo $uid; ?> .olo-sh-langbtn:hover,.<?php echo $uid; ?> .olo-sh-langbtn[aria-expanded="true"]{background:var(--sh-line-2);color:var(--sh-ink)}
        .<?php echo $uid; ?> .olo-sh-langbtn .olo-sh-chev{width:9px;height:9px}
        .<?php echo $uid; ?> .olo-sh-cta{display:inline-flex;align-items:center;gap:8px;padding:12px 22px;border-radius:100px;font-weight:700;font-size:14.5px;border:none;transition:transform .14s,background .14s,box-shadow .14s;white-space:nowrap}
        <?php if ( $cta_style === 'royal' ) : ?>
        .<?php echo $uid; ?> .olo-sh-cta{background:var(--sh-royal);color:#fff;box-shadow:var(--sh-shadow)}
        .<?php echo $uid; ?> .olo-sh-cta:hover{background:var(--sh-royal-ink);transform:translateY(-1px);box-shadow:var(--sh-shadow-lg)}
        <?php elseif ( $cta_style === 'outline' ) : ?>
        .<?php echo $uid; ?> .olo-sh-cta{background:transparent;color:var(--sh-navy);border:1.5px solid var(--sh-navy)}
        .<?php echo $uid; ?> .olo-sh-cta:hover{background:var(--sh-navy);color:#fff;transform:translateY(-1px)}
        <?php else : ?>
        .<?php echo $uid; ?> .olo-sh-cta{background:var(--sh-navy);color:#fff;box-shadow:var(--sh-shadow)}
        .<?php echo $uid; ?> .olo-sh-cta:hover{background:var(--sh-navy-2);transform:translateY(-1px);box-shadow:var(--sh-shadow-lg)}
        <?php endif; ?>

        .<?php echo $uid; ?> .olo-sh-ham{display:none;place-items:center;width:40px;height:40px;border-radius:100px;border:none;background:transparent;color:var(--sh-ink)}

        /* ── Panel base ── */
        .<?php echo $uid; ?> .olo-sh-panel{position:absolute;top:calc(100% + 14px);background:var(--sh-paper-2);border:1px solid var(--sh-line);border-radius:24px;box-shadow:var(--sh-shadow-lg);opacity:0;visibility:hidden;<?php echo $anim ? 'transform:translateY(8px) scale(.99);' : ''; ?>transform-origin:top center;transition:opacity .18s ease,transform .18s ease,visibility .18s;z-index:5}
        .<?php echo $uid; ?> .olo-sh-panel.olo-sh-open{opacity:1;visibility:visible;transform:translateY(0) scale(1)}

        /* ── Mega ── */
        .<?php echo $uid; ?> .olo-sh-mega{left:50%;<?php echo $anim ? 'transform:translateX(-50%) translateY(8px) scale(.99);' : 'transform:translateX(-50%);'; ?>transform-origin:top center;width:min(960px,calc(100vw - 44px));max-width:calc(100vw - 44px);padding:0;overflow:hidden}
        .<?php echo $uid; ?> .olo-sh-mega.olo-sh-open{transform:translateX(-50%) translateY(0) scale(1)}
        .<?php echo $uid; ?> .olo-sh-mega-grid{display:grid;grid-template-columns:.92fr repeat(<?php echo max( 1, min( 3, intval( $s['mega_columns'] ) ?: 2 ) ); ?>,1fr);gap:0}
        <?php if ( empty( $s['rail_show'] ) ) : ?>
        .<?php echo $uid; ?> .olo-sh-mega-grid{grid-template-columns:repeat(<?php echo max( 1, min( 3, intval( $s['mega_columns'] ) ?: 2 ) ); ?>,1fr)}
        <?php endif; ?>

        .<?php echo $uid; ?> .olo-sh-rail{padding:30px 28px;background:<?php echo $rail_bg ? $rail_bg : 'linear-gradient(180deg,#f6f8ff,#eef3ff)'; ?>;border-right:1px solid var(--sh-line);display:flex;flex-direction:column}
        .<?php echo $uid; ?> .olo-sh-rail .olo-sh-badge{align-self:flex-start;display:inline-flex;align-items:center;gap:6px;padding:5px 11px;border-radius:100px;background:#fff;border:1px solid var(--sh-line);font-size:10px;font-weight:800;letter-spacing:.08em;text-transform:uppercase;color:var(--sh-royal-ink)}
        .<?php echo $uid; ?> .olo-sh-rail h3{font-size:23px;font-weight:800;letter-spacing:-.02em;margin:16px 0 0}
        .<?php echo $uid; ?> .olo-sh-rail p{font-size:13.5px;color:var(--sh-ink-2);margin:10px 0 0;line-height:1.5;font-weight:500}
        .<?php echo $uid; ?> .olo-sh-rail-cta{margin-top:auto;padding-top:22px;display:flex;flex-direction:column;gap:10px}
        .<?php echo $uid; ?> .olo-sh-rail-cta a.olo-sh-solid{display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:13px 18px;border-radius:100px;background:var(--sh-navy);color:#fff;font-weight:700;font-size:14px}
        .<?php echo $uid; ?> .olo-sh-rail-cta a.olo-sh-solid:hover{background:var(--sh-navy-2)}
        .<?php echo $uid; ?> .olo-sh-rail-cta a.olo-sh-link2{font-size:13px;font-weight:700;color:var(--sh-royal-ink);display:inline-flex;align-items:center;gap:6px}
        .<?php echo $uid; ?> .olo-sh-rail-cta a.olo-sh-link2:hover{gap:9px}

        .<?php echo $uid; ?> .olo-sh-col{padding:28px 26px 22px}
        .<?php echo $uid; ?> .olo-sh-col + .olo-sh-col{border-left:1px solid var(--sh-line-2)}
        .<?php echo $uid; ?> .olo-sh-col h4{font-size:11px;font-weight:700;letter-spacing:.13em;text-transform:uppercase;color:var(--sh-ink-3);margin:0 0 8px;padding-left:12px}
        .<?php echo $uid; ?> .olo-sh-prod{display:flex;align-items:center;gap:13px;padding:11px 12px;border-radius:14px;transition:background .12s;position:relative}
        .<?php echo $uid; ?> .olo-sh-plogo{width:46px;height:46px;object-fit:contain;flex-shrink:0}
        .<?php echo $uid; ?> .olo-sh-prod:hover{background:var(--sh-line-2)}
        .<?php echo $uid; ?> .olo-sh-pd{font-size:13.5px;color:var(--sh-ink-2);line-height:1.4;font-weight:600}
        .<?php echo $uid; ?> .olo-sh-pname{font-size:14.5px;font-weight:700;letter-spacing:-.01em;display:block;color:var(--sh-ink)}
        .<?php echo $uid; ?> .olo-sh-soon{display:inline-flex;align-items:center;padding:2px 7px;margin-left:6px;border-radius:100px;background:#fff;border:1px solid var(--sh-line);font-size:9px;font-weight:800;letter-spacing:.06em;text-transform:uppercase;color:var(--sh-ink-3)}

        .<?php echo $uid; ?> .olo-sh-mega-foot{grid-column:1 / -1;border-top:1px solid var(--sh-line);background:var(--sh-paper);display:flex;align-items:center;gap:16px;padding:18px 28px}
        .<?php echo $uid; ?> .olo-sh-ff{display:flex;align-items:center;gap:13px}
        .<?php echo $uid; ?> .olo-sh-duo{display:flex;gap:8px;align-items:center}
        .<?php echo $uid; ?> .olo-sh-duo img{height:34px;width:auto;object-fit:contain}
        .<?php echo $uid; ?> .olo-sh-ff b{font-size:14px;font-weight:800}
        .<?php echo $uid; ?> .olo-sh-ff p{margin:2px 0 0;font-size:12.5px;color:var(--sh-ink-2);font-weight:500}
        .<?php echo $uid; ?> .olo-sh-foot-cta{margin-left:auto;font-size:13px;font-weight:700;color:var(--sh-royal-ink);display:inline-flex;align-items:center;gap:6px}
        .<?php echo $uid; ?> .olo-sh-foot-cta:hover{gap:9px}

        /* ── Lang dropdown ── */
        .<?php echo $uid; ?> .olo-sh-lang-menu{right:0;width:210px;padding:8px}
        .<?php echo $uid; ?> .olo-sh-lang-menu .olo-sh-lh{padding:8px 12px 6px;font-size:10px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--sh-ink-3)}
        .<?php echo $uid; ?> .olo-sh-lang-opt{display:flex;align-items:center;gap:11px;width:100%;text-align:left;padding:10px 12px;border-radius:11px;border:none;background:transparent;font-size:14px;font-weight:600;color:var(--sh-ink);transition:background .12s}
        .<?php echo $uid; ?> .olo-sh-lang-opt:hover{background:var(--sh-line-2)}
        .<?php echo $uid; ?> .olo-sh-lang-opt .olo-sh-code{font-family:"JetBrains Mono",monospace;font-size:11px;font-weight:600;color:var(--sh-ink-3);width:22px}
        .<?php echo $uid; ?> .olo-sh-lang-opt .olo-sh-ck{margin-left:auto;color:var(--sh-royal);opacity:0;width:15px}
        .<?php echo $uid; ?> .olo-sh-lang-opt[aria-current="true"]{background:#eef3ff}
        .<?php echo $uid; ?> .olo-sh-lang-opt[aria-current="true"] .olo-sh-ck{opacity:1}
        .<?php echo $uid; ?> .olo-sh-lang-opt[aria-current="true"] .olo-sh-code{color:var(--sh-royal-ink)}

        /* ── Search panel ── */
        .<?php echo $uid; ?> .olo-sh-search-panel{right:0;width:min(380px,calc(100vw - 44px));padding:16px}
        .<?php echo $uid; ?> .olo-sh-search-field{display:flex;align-items:center;gap:10px;padding:0 14px;border:1.5px solid var(--sh-line);border-radius:13px;height:48px;transition:border-color .14s}
        .<?php echo $uid; ?> .olo-sh-search-field:focus-within{border-color:var(--sh-royal)}
        .<?php echo $uid; ?> .olo-sh-search-field input{flex:1;border:none;outline:none;font-family:inherit;font-size:15px;color:var(--sh-ink);background:transparent}
        .<?php echo $uid; ?> .olo-sh-search-field input::placeholder{color:var(--sh-ink-3)}
        .<?php echo $uid; ?> .olo-sh-search-field kbd{font-family:"JetBrains Mono",monospace;font-size:10px;color:var(--sh-ink-3);border:1px solid var(--sh-line);border-radius:6px;padding:3px 6px}
        .<?php echo $uid; ?> .olo-sh-search-quick{margin-top:14px}
        .<?php echo $uid; ?> .olo-sh-search-quick .olo-sh-lh{font-size:10px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--sh-ink-3);padding:0 4px 8px}
        .<?php echo $uid; ?> .olo-sh-qrow{display:flex;flex-wrap:wrap;gap:7px;padding:0 4px}
        .<?php echo $uid; ?> .olo-sh-qchip{padding:7px 13px;border-radius:100px;background:var(--sh-line-2);font-size:13px;font-weight:600;color:var(--sh-ink-2);transition:background .12s,color .12s}
        .<?php echo $uid; ?> .olo-sh-qchip:hover{background:var(--sh-navy);color:#fff}

        /* ── Mobile sheet ── */
        .<?php echo $uid; ?> .olo-sh-scrim{position:fixed;inset:0;background:rgba(13,20,40,.5);backdrop-filter:blur(2px);opacity:0;visibility:hidden;transition:.2s;z-index:55}
        .<?php echo $uid; ?> .olo-sh-scrim.olo-sh-open{opacity:1;visibility:visible}
        .<?php echo $uid; ?> .olo-sh-sheet{display:none}

        /* focus visibile su tutti gli elementi interattivi */
        .<?php echo $uid; ?> a:focus-visible,
        .<?php echo $uid; ?> button:focus-visible,
        .<?php echo $uid; ?> input:focus-visible{outline:2px solid var(--sh-royal);outline-offset:2px;border-radius:8px}

        /* ── Responsive ── */
        @media (max-width:<?php echo $bp; ?>px){
          .<?php echo $uid; ?> .olo-sh-nav,
          .<?php echo $uid; ?> .olo-sh-divider,
          .<?php echo $uid; ?> .olo-sh-featured,
          .<?php echo $uid; ?> .olo-sh-langbtn{display:none}
          .<?php echo $uid; ?> .olo-sh-right{gap:2px}
          .<?php echo $uid; ?> .olo-sh-ham{display:grid}
          .<?php echo $uid; ?> .olo-sh-bar{padding:11px 12px 11px 20px}
          .<?php echo $uid; ?> .olo-sh-cta{padding:11px 18px}

          .<?php echo $uid; ?> .olo-sh-sheet{display:flex;flex-direction:column;position:fixed;top:0;right:0;bottom:0;width:min(420px,88vw);z-index:58;background:var(--sh-paper-2);box-shadow:var(--sh-shadow-lg);transform:translateX(100%);transition:transform .26s cubic-bezier(.4,0,.1,1);padding:22px;overflow-y:auto}
          .<?php echo $uid; ?> .olo-sh-sheet.olo-sh-open{transform:translateX(0)}
          .<?php echo $uid; ?> .olo-sh-sheet-top{display:flex;align-items:center;justify-content:space-between;margin-bottom:8px}
          .<?php echo $uid; ?> .olo-sh-sheet-top img{height:<?php echo max( 14, intval( $s['brand_height'] ) ?: 25 ); ?>px}
          .<?php echo $uid; ?> .olo-sh-sheet-x{width:40px;height:40px;border-radius:100px;border:none;background:var(--sh-line-2);display:grid;place-items:center}
          .<?php echo $uid; ?> .olo-sh-acc{border-bottom:1px solid var(--sh-line-2)}
          .<?php echo $uid; ?> .olo-sh-acc > button{display:flex;align-items:center;justify-content:space-between;width:100%;padding:16px 4px;border:none;background:transparent;font-size:18px;font-weight:700;color:var(--sh-ink)}
          .<?php echo $uid; ?> .olo-sh-acc > button .olo-sh-chev{width:13px;height:13px;transition:transform .2s;color:var(--sh-ink-3)}
          .<?php echo $uid; ?> .olo-sh-acc.olo-sh-open > button .olo-sh-chev{transform:rotate(180deg)}
          .<?php echo $uid; ?> .olo-sh-mbody{max-height:0;overflow:hidden;transition:max-height .26s ease}
          .<?php echo $uid; ?> .olo-sh-acc.olo-sh-open .olo-sh-mbody{max-height:760px}
          .<?php echo $uid; ?> .olo-sh-mprod{display:flex;gap:12px;padding:11px 6px 11px 4px;align-items:center}
          .<?php echo $uid; ?> .olo-sh-mprod img{width:38px;height:38px;object-fit:contain;flex-shrink:0}
          .<?php echo $uid; ?> .olo-sh-mprod .olo-sh-pname{font-size:15px}
          .<?php echo $uid; ?> .olo-sh-mprod .olo-sh-pd{font-size:12px}
          .<?php echo $uid; ?> .olo-sh-sheet-langs{display:flex;flex-wrap:wrap;gap:8px;padding:14px 0 4px}
          .<?php echo $uid; ?> .olo-sh-sheet-langs button{padding:9px 15px;border-radius:100px;border:1px solid var(--sh-line);background:#fff;font-size:14px;font-weight:600}
          .<?php echo $uid; ?> .olo-sh-sheet-langs button[aria-current="true"]{background:var(--sh-navy);color:#fff;border-color:var(--sh-navy)}
          .<?php echo $uid; ?> .olo-sh-sheet .olo-sh-cta{width:100%;justify-content:center;margin-top:20px;padding:16px}
          .<?php echo $uid; ?> .olo-sh-sheet-lh{font-size:11px;letter-spacing:.12em;text-transform:uppercase;color:var(--sh-ink-3);font-family:"JetBrains Mono",monospace;padding-top:16px}
        }
        @media (prefers-reduced-motion: reduce){
          .<?php echo $uid; ?> .olo-sh-panel,
          .<?php echo $uid; ?> .olo-sh-sheet,
          .<?php echo $uid; ?> .olo-sh-scrim{transition:none}
        }
        </style>
        <?php
        // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
    }

    /* ─────────────────────────────────────────── HTML ── */

    /** Raggruppa i prodotti per `group` preservando l'ordine di prima comparsa. */
    private function group_products( $products ) {
        $groups = [];
        $order  = [];
        foreach ( (array) $products as $p ) {
            $g = isset( $p['group'] ) ? (string) $p['group'] : '';
            if ( ! isset( $groups[ $g ] ) ) { $groups[ $g ] = []; $order[] = $g; }
            $groups[ $g ][] = $p;
        }
        $out = [];
        foreach ( $order as $g ) { $out[] = [ 'title' => $g, 'items' => $groups[ $g ] ]; }
        return $out;
    }

    private function chev_svg() {
        return '<svg class="olo-sh-chev" viewBox="0 0 12 12" fill="none" aria-hidden="true"><path d="M2.5 4.5 6 8l3.5-3.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>';
    }

    private function arrow_svg() {
        return '<svg width="14" height="14" viewBox="0 0 14 14" fill="none" aria-hidden="true"><path d="M3 7h8M7.5 3.5 11 7l-3.5 3.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';
    }

    private function render_html( $s, $uid ) {
        $brand_logo = $s['brand_logo'] ?: ( OLO_URL . 'assets/img/menu/olotheme-orizz.png' );
        $nav_items  = (array) ( $s['nav_items'] ?? [] );
        $columns    = $this->group_products( $s['mega_products'] ?? [] );
        $cur        = strtolower( (string) ( $s['lang_current'] ?? 'it' ) );
        $langs      = (array) ( $s['languages'] ?? [] );
        // Auto-popolamento da OLOlang se attivo (best-effort, fallback al repeater).
        if ( ! empty( $s['lang_bind_ololang'] ) ) {
            $auto = $this->ololang_languages();
            if ( ! empty( $auto ) ) { $langs = $auto; }
        }

        ?>
        <div class="<?php echo esc_attr( $uid ); ?> olo-oloheader" data-open-mega="<?php echo esc_attr( $s['open_mega_on'] ?? 'hover' ); ?>" data-close-esc="<?php echo empty( $s['close_on_esc'] ) ? '0' : '1'; ?>" data-bp="<?php echo intval( $s['mobile_breakpoint'] ) ?: 1040; ?>">
          <header class="olo-sh-hdr">
            <div class="olo-sh-bar" data-bar>
              <!-- brand -->
              <a class="olo-sh-brand" href="<?php echo esc_url( $s['brand_url'] ?: '/' ); ?>" aria-label="<?php echo esc_attr__( 'Home', 'olobuild' ); ?>">
                <img class="olo-sh-blogo" src="<?php echo esc_url( $brand_logo ); ?>" alt="" />
              </a>

              <!-- primary nav -->
              <nav class="olo-sh-nav" aria-label="<?php echo esc_attr__( 'Principale', 'olobuild' ); ?>">
                <?php
                $mega_used = false;
                foreach ( $nav_items as $it ) :
                    $label = (string) ( $it['label'] ?? '' );
                    $url   = (string) ( $it['url'] ?? '#' );
                    $is_mega = ( ( $it['type'] ?? 'link' ) === 'mega' ) && ! $mega_used;
                    if ( $is_mega ) :
                        $mega_used = true;
                        $panel_id = $uid . '-mega';
                        ?>
                        <div class="olo-sh-item" data-mega>
                          <button class="olo-sh-link" data-trigger="mega" aria-expanded="false" aria-haspopup="true" aria-controls="<?php echo esc_attr( $panel_id ); ?>">
                            <?php echo esc_html( $label ); ?>
                            <?php echo $this->chev_svg(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG ?>
                          </button>
                          <div class="olo-sh-panel olo-sh-mega" id="<?php echo esc_attr( $panel_id ); ?>" data-panel="mega" role="menu" aria-label="<?php echo esc_attr( $label ); ?>">
                            <div class="olo-sh-mega-grid">
                              <?php if ( ! empty( $s['rail_show'] ) ) : ?>
                              <div class="olo-sh-rail">
                                <?php if ( $s['rail_badge'] ) : ?><span class="olo-sh-badge"><?php echo esc_html( $s['rail_badge'] ); ?></span><?php endif; ?>
                                <?php if ( $s['rail_title'] ) : ?><h3><?php echo esc_html( $s['rail_title'] ); ?></h3><?php endif; ?>
                                <?php if ( $s['rail_text'] ) : ?><p><?php echo esc_html( $s['rail_text'] ); ?></p><?php endif; ?>
                                <div class="olo-sh-rail-cta">
                                  <?php if ( $s['rail_cta1_label'] ) : ?><a class="olo-sh-solid" href="<?php echo esc_url( $s['rail_cta1_url'] ?: '#' ); ?>"><?php echo esc_html( $s['rail_cta1_label'] ); ?></a><?php endif; ?>
                                  <?php if ( $s['rail_cta2_label'] ) : ?><a class="olo-sh-link2" href="<?php echo esc_url( $s['rail_cta2_url'] ?: '#' ); ?>"><?php echo esc_html( $s['rail_cta2_label'] ); ?> <?php echo $this->arrow_svg(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG ?></a><?php endif; ?>
                                </div>
                              </div>
                              <?php endif; ?>

                              <?php foreach ( $columns as $col ) : ?>
                              <div class="olo-sh-col">
                                <?php if ( $col['title'] !== '' ) : ?><h4><?php echo esc_html( $col['title'] ); ?></h4><?php endif; ?>
                                <?php foreach ( $col['items'] as $p ) :
                                    $p_url  = (string) ( $p['url'] ?? '#' );
                                    $p_name = (string) ( $p['name'] ?? '' );
                                    $p_logo = (string) ( $p['logo'] ?? '' );
                                    $p_desc = (string) ( $p['desc'] ?? '' );
                                    $p_soon = ! empty( $p['soon'] );
                                    ?>
                                    <a class="olo-sh-prod" href="<?php echo esc_url( $p_url ); ?>" role="menuitem">
                                      <?php if ( $p_logo ) : ?><img class="olo-sh-plogo" src="<?php echo esc_url( $p_logo ); ?>" alt="<?php echo esc_attr( $p_name ); ?>" /><?php endif; ?>
                                      <span class="olo-sh-pd">
                                        <?php if ( $p_name ) : ?><span class="olo-sh-pname"><?php echo esc_html( $p_name ); ?><?php if ( $p_soon ) : ?><span class="olo-sh-soon"><?php echo esc_html__( 'In arrivo', 'olobuild' ); ?></span><?php endif; ?></span><?php endif; ?>
                                        <?php echo esc_html( $p_desc ); ?>
                                      </span>
                                    </a>
                                <?php endforeach; ?>
                              </div>
                              <?php endforeach; ?>

                              <?php if ( ! empty( $s['mega_footer_show'] ) ) :
                                  $flogos = (array) ( $s['mega_footer_logos'] ?? [] );
                                  ?>
                              <div class="olo-sh-mega-foot">
                                <div class="olo-sh-ff">
                                  <?php if ( $flogos ) : ?>
                                  <span class="olo-sh-duo">
                                    <?php foreach ( $flogos as $fl ) : $u = (string) ( $fl['logo'] ?? '' ); if ( ! $u ) continue; ?>
                                      <img src="<?php echo esc_url( $u ); ?>" alt="" />
                                    <?php endforeach; ?>
                                  </span>
                                  <?php endif; ?>
                                  <div>
                                    <?php if ( $s['mega_footer_title'] ) : ?><b><?php echo esc_html( $s['mega_footer_title'] ); ?></b><?php endif; ?>
                                    <?php if ( $s['mega_footer_text'] ) : ?><p><?php echo esc_html( $s['mega_footer_text'] ); ?></p><?php endif; ?>
                                  </div>
                                </div>
                                <?php if ( $s['mega_footer_cta_label'] ) : ?>
                                <a class="olo-sh-foot-cta" href="<?php echo esc_url( $s['mega_footer_cta_url'] ?: '#' ); ?>"><?php echo esc_html( $s['mega_footer_cta_label'] ); ?> <?php echo $this->arrow_svg(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG ?></a>
                                <?php endif; ?>
                              </div>
                              <?php endif; ?>
                            </div>
                          </div>
                        </div>
                        <?php
                    else :
                        ?>
                        <div class="olo-sh-item"><a class="olo-sh-link" href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $label ); ?></a></div>
                        <?php
                    endif;
                endforeach;
                ?>
              </nav>

              <?php if ( ! empty( $s['featured_show'] ) ) : ?>
              <div class="olo-sh-divider" aria-hidden="true"></div>
              <a class="olo-sh-featured" href="<?php echo esc_url( $s['featured_url'] ?: '#' ); ?>">
                <span class="olo-sh-fic" aria-hidden="true"><?php echo $this->featured_icon_html( $s['featured_icon'] ?? 'bolt' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG from icon set / static fallback ?></span>
                <?php echo esc_html( $s['featured_label'] ); ?>
              </a>
              <?php endif; ?>

              <!-- right cluster -->
              <div class="olo-sh-right">
                <?php if ( ! empty( $s['search_show'] ) ) :
                    $sp_id = $uid . '-search';
                    ?>
                <div class="olo-sh-item">
                  <button class="olo-sh-iconbtn" data-trigger="search" aria-label="<?php echo esc_attr__( 'Cerca', 'olobuild' ); ?>" aria-expanded="false" aria-controls="<?php echo esc_attr( $sp_id ); ?>">
                    <svg width="19" height="19" viewBox="0 0 20 20" fill="none" aria-hidden="true"><circle cx="9" cy="9" r="6" stroke="currentColor" stroke-width="1.7"/><path d="m14 14 3.5 3.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                  </button>
                  <div class="olo-sh-panel olo-sh-search-panel" id="<?php echo esc_attr( $sp_id ); ?>" data-panel="search" role="dialog" aria-label="<?php echo esc_attr__( 'Cerca nel sito', 'olobuild' ); ?>">
                    <form class="olo-sh-search-field" action="<?php echo esc_url( $s['search_url'] ?: '/' ); ?>" method="get" role="search">
                      <svg width="18" height="18" viewBox="0 0 20 20" fill="none" aria-hidden="true"><circle cx="9" cy="9" r="6" stroke="#8B91A1" stroke-width="1.7"/><path d="m14 14 3.5 3.5" stroke="#8B91A1" stroke-width="1.7" stroke-linecap="round"/></svg>
                      <input type="search" name="s" data-search-input placeholder="<?php echo esc_attr( $s['search_placeholder'] ); ?>" aria-label="<?php echo esc_attr( $s['search_placeholder'] ); ?>" />
                      <kbd>Esc</kbd>
                    </form>
                    <?php $shortcuts = (array) ( $s['search_shortcuts'] ?? [] ); if ( $shortcuts ) : ?>
                    <div class="olo-sh-search-quick">
                      <div class="olo-sh-lh"><?php echo esc_html__( 'Scorciatoie', 'olobuild' ); ?></div>
                      <div class="olo-sh-qrow">
                        <?php foreach ( $shortcuts as $sc ) : ?>
                          <a class="olo-sh-qchip" href="<?php echo esc_url( $sc['url'] ?? '#' ); ?>"><?php echo esc_html( $sc['label'] ?? '' ); ?></a>
                        <?php endforeach; ?>
                      </div>
                    </div>
                    <?php endif; ?>
                  </div>
                </div>
                <?php endif; ?>

                <?php if ( ! empty( $s['lang_show'] ) ) :
                    $lm_id = $uid . '-lang';
                    ?>
                <div class="olo-sh-item">
                  <button class="olo-sh-langbtn" data-trigger="lang" aria-expanded="false" aria-haspopup="true" aria-controls="<?php echo esc_attr( $lm_id ); ?>">
                    <?php if ( ! empty( $s['lang_globe'] ) ) : ?>
                    <svg width="17" height="17" viewBox="0 0 20 20" fill="none" aria-hidden="true"><circle cx="10" cy="10" r="7.2" stroke="currentColor" stroke-width="1.5"/><path d="M3 10h14M10 3c2 2 2 12 0 14M10 3c-2 2-2 12 0 14" stroke="currentColor" stroke-width="1.3"/></svg>
                    <?php endif; ?>
                    <span data-lang-label><?php echo esc_html( ucfirst( $cur ) ); ?></span>
                    <?php echo $this->chev_svg(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG ?>
                  </button>
                  <div class="olo-sh-panel olo-sh-lang-menu" id="<?php echo esc_attr( $lm_id ); ?>" data-panel="lang" role="menu" aria-label="<?php echo esc_attr__( 'Lingua', 'olobuild' ); ?>">
                    <div class="olo-sh-lh"><?php echo esc_html__( 'Lingua', 'olobuild' ); ?></div>
                    <?php foreach ( $langs as $lg ) :
                        $code  = strtolower( (string) ( $lg['code'] ?? '' ) );
                        $lbl   = (string) ( $lg['label'] ?? '' );
                        $lurl  = (string) ( $lg['url'] ?? '#' );
                        $is_cur = ( $code === $cur );
                        ?>
                      <a class="olo-sh-lang-opt" href="<?php echo esc_url( $lurl ); ?>" data-code="<?php echo esc_attr( ucfirst( $code ) ); ?>" role="menuitemradio" aria-current="<?php echo $is_cur ? 'true' : 'false'; ?>">
                        <span class="olo-sh-code"><?php echo esc_html( strtoupper( $code ) ); ?></span><?php echo esc_html( $lbl ); ?>
                        <svg class="olo-sh-ck" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="m3 8.5 3 3 7-7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                      </a>
                    <?php endforeach; ?>
                  </div>
                </div>
                <?php endif; ?>

                <?php if ( ! empty( $s['cta_show'] ) ) : ?>
                <a class="olo-sh-cta" href="<?php echo esc_url( $s['cta_url'] ?: '#' ); ?>"><?php echo esc_html( $s['cta_label'] ); ?></a>
                <?php endif; ?>

                <button class="olo-sh-ham" data-ham aria-label="<?php echo esc_attr__( 'Apri menu', 'olobuild' ); ?>" aria-expanded="false">
                  <svg width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                </button>
              </div>
            </div>
          </header>

          <!-- mobile sheet -->
          <div class="olo-sh-scrim" data-scrim></div>
          <aside class="olo-sh-sheet" data-sheet aria-label="<?php echo esc_attr__( 'Menu mobile', 'olobuild' ); ?>">
            <div class="olo-sh-sheet-top">
              <span class="olo-sh-brand"><img src="<?php echo esc_url( $brand_logo ); ?>" alt="" /></span>
              <button class="olo-sh-sheet-x" data-sheet-x aria-label="<?php echo esc_attr__( 'Chiudi', 'olobuild' ); ?>">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
              </button>
            </div>

            <?php
            $first_mega = true;
            foreach ( $nav_items as $it ) :
                $label = (string) ( $it['label'] ?? '' );
                $url   = (string) ( $it['url'] ?? '#' );
                $is_mega = ( ( $it['type'] ?? 'link' ) === 'mega' ) && $first_mega && ! empty( $columns );
                if ( $is_mega ) :
                    $first_mega = false;
                    ?>
                    <div class="olo-sh-acc olo-sh-open">
                      <button aria-expanded="true"><?php echo esc_html( $label ); ?> <?php echo $this->chev_svg(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG ?></button>
                      <div class="olo-sh-mbody">
                        <?php foreach ( $columns as $col ) : foreach ( $col['items'] as $p ) :
                            $p_url = (string) ( $p['url'] ?? '#' ); $p_name = (string) ( $p['name'] ?? '' );
                            $p_logo = (string) ( $p['logo'] ?? '' ); $p_desc = (string) ( $p['desc'] ?? '' );
                            ?>
                          <a class="olo-sh-mprod" href="<?php echo esc_url( $p_url ); ?>">
                            <?php if ( $p_logo ) : ?><img src="<?php echo esc_url( $p_logo ); ?>" alt="" /><?php endif; ?>
                            <span><span class="olo-sh-pname"><?php echo esc_html( $p_name ); ?></span><span class="olo-sh-pd"><?php echo esc_html( $p_desc ); ?></span></span>
                          </a>
                        <?php endforeach; endforeach; ?>
                      </div>
                    </div>
                    <?php
                else :
                    ?>
                    <div class="olo-sh-acc"><button aria-expanded="false" onclick="window.location.href='<?php echo esc_js( esc_url( $url ) ); ?>'"><?php echo esc_html( $label ); ?></button></div>
                    <?php
                endif;
            endforeach;
            ?>

            <?php if ( ! empty( $s['lang_show'] ) ) : ?>
            <div class="olo-sh-sheet-lh"><?php echo esc_html__( 'Lingua', 'olobuild' ); ?></div>
            <div class="olo-sh-sheet-langs" data-sheet-langs>
              <?php foreach ( $langs as $lg ) :
                  $code = strtolower( (string) ( $lg['code'] ?? '' ) );
                  $lbl  = (string) ( $lg['label'] ?? '' );
                  $lurl = (string) ( $lg['url'] ?? '#' );
                  ?>
                <button data-code="<?php echo esc_attr( ucfirst( $code ) ); ?>" aria-current="<?php echo ( $code === $cur ) ? 'true' : 'false'; ?>" onclick="window.location.href='<?php echo esc_js( esc_url( $lurl ) ); ?>'"><?php echo esc_html( $lbl ); ?></button>
              <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <?php if ( ! empty( $s['cta_show'] ) ) : ?>
            <a class="olo-sh-cta" href="<?php echo esc_url( $s['cta_url'] ?: '#' ); ?>"><?php echo esc_html( $s['cta_label'] ); ?></a>
            <?php endif; ?>
          </aside>
        </div>
        <?php
    }

    /**
     * Icona del link featured: prova il set SVG della tile base, fallback al bolt
     * della demo. Mai emoji.
     */
    private function featured_icon_html( $icon ) {
        $icon = trim( (string) $icon );
        // Default 'bolt' (o vuoto) → fulmine inline della demo, indipendente da UIkit.
        if ( $icon !== '' && $icon !== 'bolt' && method_exists( $this, 'render_icon_html' ) ) {
            $html = $this->render_icon_html( $icon );
            if ( $html ) { return $html; }
        }
        return '<svg width="15" height="15" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M9 1 2 9h5l-1 6 7-8H8l1-6Z" fill="currentColor"/></svg>';
    }

    /**
     * Auto-popolamento lingue da OLOlang se attivo. Best-effort: usa il filtro
     * pubblico se esposto, altrimenti ritorna [] (fallback al repeater manuale).
     * Vedi i18n standard del vault — integrazione OLOlang.
     */
    private function ololang_languages() {
        // <!-- TODO: agganciare l'API reale di OLOlang quando disponibile. -->
        $langs = apply_filters( 'ololang_active_languages', [] );
        if ( empty( $langs ) || ! is_array( $langs ) ) { return []; }
        $out = [];
        foreach ( $langs as $l ) {
            if ( ! is_array( $l ) ) { continue; }
            $out[] = [
                'code'  => (string) ( $l['code'] ?? $l['slug'] ?? '' ),
                'label' => (string) ( $l['label'] ?? $l['name'] ?? '' ),
                'url'   => (string) ( $l['url'] ?? $l['href'] ?? '#' ),
            ];
        }
        return $out;
    }

    /* ─────────────────────────────────────────── JS ── */

    private function render_js( $s, $uid ) {
        ?>
        <script>
        (function(){
          var root = document.querySelector('.<?php echo esc_js( $uid ); ?>');
          if (!root || root.dataset.oloshInit) return;
          root.dataset.oloshInit = '1';

          var bar = root.querySelector('[data-bar]');
          var openMega = root.dataset.openMega || 'hover';
          var closeEsc = root.dataset.closeEsc !== '0';
          var bp = parseInt(root.dataset.bp, 10) || 1040;

          // ── panel controller (un solo pannello aperto) ──
          var groups = Array.prototype.map.call(root.querySelectorAll('[data-trigger]'), function(btn){
            var name = btn.getAttribute('data-trigger');
            var panel = root.querySelector('[data-panel="' + name + '"]');
            return { name: name, btn: btn, panel: panel };
          }).filter(function(g){ return g.panel; });

          var openGroup = null, hoverTimer = null;
          function close(g){ if(!g) return; g.panel.classList.remove('olo-sh-open'); g.btn.setAttribute('aria-expanded','false'); if(openGroup===g) openGroup=null; }
          function closeAll(){ groups.forEach(close); }
          function open(g){ if(openGroup&&openGroup!==g) close(openGroup); g.panel.classList.add('olo-sh-open'); g.btn.setAttribute('aria-expanded','true'); openGroup=g;
            if(g.name==='search'){ var i=g.panel.querySelector('[data-search-input]'); if(i) setTimeout(function(){ i.focus(); },60); } }
          function toggle(g){ g.panel.classList.contains('olo-sh-open') ? close(g) : open(g); }

          groups.forEach(function(g){
            g.btn.addEventListener('click', function(e){ e.preventDefault(); toggle(g); });
            if(g.name==='mega' && openMega==='hover'){
              var item = g.btn.closest('[data-mega]');
              if(item){
                item.addEventListener('mouseenter', function(){ clearTimeout(hoverTimer); if(window.matchMedia('(min-width:' + (bp+1) + 'px)').matches) open(g); });
                item.addEventListener('mouseleave', function(){ hoverTimer = setTimeout(function(){ close(g); }, 160); });
                g.panel.addEventListener('mouseenter', function(){ clearTimeout(hoverTimer); });
                g.panel.addEventListener('mouseleave', function(){ hoverTimer = setTimeout(function(){ close(g); }, 160); });
              }
            }
          });

          document.addEventListener('click', function(e){ if(bar && !bar.contains(e.target)) closeAll(); });
          if(closeEsc){ document.addEventListener('keydown', function(e){ if(e.key==='Escape') closeAll(); }); }

          // ── language selection ──
          var langLabel = root.querySelector('[data-lang-label]');
          function setLang(code){
            if(langLabel) langLabel.textContent = code;
            root.querySelectorAll('.olo-sh-lang-opt').forEach(function(o){ o.setAttribute('aria-current', o.getAttribute('data-code')===code ? 'true':'false'); });
            root.querySelectorAll('[data-sheet-langs] button').forEach(function(o){ o.setAttribute('aria-current', o.getAttribute('data-code')===code ? 'true':'false'); });
          }
          root.querySelectorAll('.olo-sh-lang-opt').forEach(function(o){ o.addEventListener('click', function(){ setLang(o.getAttribute('data-code')); }); });

          // ── mobile sheet ──
          var sheet = root.querySelector('[data-sheet]'), scrim = root.querySelector('[data-scrim]'), ham = root.querySelector('[data-ham]');
          function openSheet(){ if(!sheet) return; sheet.classList.add('olo-sh-open'); if(scrim) scrim.classList.add('olo-sh-open'); if(ham) ham.setAttribute('aria-expanded','true'); document.body.style.overflow='hidden'; }
          function closeSheet(){ if(!sheet) return; sheet.classList.remove('olo-sh-open'); if(scrim) scrim.classList.remove('olo-sh-open'); if(ham) ham.setAttribute('aria-expanded','false'); document.body.style.overflow=''; }
          if(ham) ham.addEventListener('click', openSheet);
          var sx = root.querySelector('[data-sheet-x]'); if(sx) sx.addEventListener('click', closeSheet);
          if(scrim) scrim.addEventListener('click', closeSheet);
          if(closeEsc){ document.addEventListener('keydown', function(e){ if(e.key==='Escape') closeSheet(); }); }

          // ── mobile accordions ──
          root.querySelectorAll('.olo-sh-acc > button').forEach(function(b){
            if(b.getAttribute('onclick')) return; // voci link semplici
            b.addEventListener('click', function(){
              var acc=b.parentElement, willOpen=!acc.classList.contains('olo-sh-open');
              acc.classList.toggle('olo-sh-open', willOpen);
              b.setAttribute('aria-expanded', String(willOpen));
            });
          });

          // ── smart sticky: scorre con la pagina, si aggancia in alto, si nasconde
          //    scorrendo giù e riappare subito scorrendo su (come Mega Menu) ──
          var hdr = root.querySelector('.olo-sh-hdr');
          var stickyOn = <?php echo ! empty( $s['bar_sticky'] ) ? 'true' : 'false'; ?>;
          var showOnUp = <?php echo ! empty( $s['sticky_show_on_up'] ) ? 'true' : 'false'; ?>;
          if (hdr && stickyOn) {
            var lastY = window.pageYOffset || document.documentElement.scrollTop || 0;
            var ticking = false, hidden = false;
            var STUCK_AT = 10, HIDE_AFTER = 300;
            var onScroll = function(){
              if (ticking) return;
              ticking = true;
              requestAnimationFrame(function(){
                ticking = false;
                var y = window.pageYOffset || document.documentElement.scrollTop || 0;
                if (y > STUCK_AT) hdr.classList.add('olo-sh-stuck');
                else hdr.classList.remove('olo-sh-stuck');
                if (showOnUp) {
                  var delta = y - lastY;
                  if (delta > 5 && y > HIDE_AFTER) {
                    if (!hidden) { hdr.classList.add('olo-sh-hidden'); hidden = true; closeAll(); }
                  } else if (delta < -5) {
                    if (hidden) { hdr.classList.remove('olo-sh-hidden'); hidden = false; }
                  }
                }
                lastY = y;
              });
            };
            window.addEventListener('scroll', onScroll, { passive: true });
            onScroll();
          }
        })();
        </script>
        <?php
    }
}
