<?php
/**
 * Olobuild_Builder_Tiles_Trait — registrazione manuale delle tile PHP + provider dati per i localize (menu, pagine, meta, servizi).
 *
 * Estratto verbatim da class-olo-builder.php (dieta monoliti v1.4.390):
 * stessi metodi, stessa visibilita', zero cambi alle chiamate ($this/self
 * risolvono nella classe che usa il trait).
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

trait Olobuild_Builder_Tiles_Trait {
    private function register_core_tiles() {
        require_once OLOBUILD_PATH . 'includes/class-tile-utils.php';
        require_once OLOBUILD_PATH . 'includes/class-text-effects.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-tile-base.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-section-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-column-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-hero-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-hero-split-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-audiohero-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-section-header-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-info-cards-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-worklist-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-workgrid-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-statstrip-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-hoursstrip-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-hoverlist-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-lookbookmixer-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-categoryrail-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-beforeafter-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-tripfinder-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-maskedvideohero-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-searchhero-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-smearhero-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-photocover-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-masthead-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-matchfixtures-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-showcasegrid-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-productgrid-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-announcementbar-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-introsplit-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-mediacta-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-imagehero-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-glowhero-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-terminalhero-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-olox-base.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-oloxnav-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-oloxhero-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-oloxmarquee-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-oloxcards-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-oloxsticky-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-oloxpricing-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-oloxstatement-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-oloxlist-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-oloxlessons-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-oloxquiz-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-oloxbanner-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-oloxfoot-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-oloxpagefx-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-oloxmanual-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-oloxpanel-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-oloxrail-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-oloxscene-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-coverdots-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-bottombar-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-pagelight-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-oloxhome-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-producthero-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-northvideohero-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-northquoteslider-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-featuredstory-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-glowgallery-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-chathero-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-product-cards-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-step-timeline-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-process-steps-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-cta-banner-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-trust-strip-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-content-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-image-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-video-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-spacer-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-button-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-gallery-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-row-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-testimonial-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-pricing-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-counter-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-iconbox-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-alert-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-badge-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-team-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-accordion-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-icontabs-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-projector-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-finder-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-builder-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-mixer-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-schedule-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-hotspots-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-scaler-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-timezone-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-availability-tile.php';

        require_once OLOBUILD_PATH . 'includes/tiles/class-social-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-map-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-countdown-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-headline-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-html-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-list-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-text-block-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-slideshow-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-table-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-overlay-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-divider-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-progress-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-desclist-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-panel-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-quotation-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-code-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-icon-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-totop-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-fragment-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-grid-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-switcher-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-switcherpanel-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-nav-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-subnav-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-panelslider-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-overlayslider-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-overlaygrid-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-popover-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-breadcrumbs-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-search-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-sitelogo-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-navmenu-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-postgrid-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-proslider-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-popup-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-megamenu-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-oloheader-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-inner-columns-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-timeline-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-flipcard-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-imgcompare-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-marquee-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-togglebtn-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-form-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-killnextprev-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-langswitcher-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-livesearch-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-shatteredimage-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-textmask-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-blendtext-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-progallery-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-pdfviewer-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-pdfpro-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-starrating-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-iconlist-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-animatedheading-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-toc-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-lottie-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-sharebuttons-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-scrollprogress-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-newsticker-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-hotspot-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-loginform-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-videoplaylist-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-textpath-tile.php';

        require_once OLOBUILD_PATH . 'includes/tiles/class-chart-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-audio-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-shapedivider-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-countercircle-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-postmeta-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-relatedposts-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-wpcomments-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-pagination-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-carousel-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-authorbox-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-soundcloud-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-tagcloud-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-viewscounter-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-menuanchor-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-osmmap-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-instagram-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-facebookpage-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-twitterfeed-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-postnavigation-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-pricelist-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-progresstracker-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-sitemap-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-linkinbio-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-shortcode-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-templateembed-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-paymentbuttons-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-pagetitlebar-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-portfolio-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-queryloop-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-readingtime-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-darkmode-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-lightbox-tile.php';

        // Clod — Evoluzione (studio editorial)
        require_once OLOBUILD_PATH . 'includes/tiles/class-studiohero-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-filmreel-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-scrubtext-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-themedemos-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-evonotes-tile.php';

        // WooCommerce tiles (solo se WooCommerce attivo)
        if ( class_exists( 'WooCommerce' ) ) {
            require_once OLOBUILD_PATH . 'includes/tiles/class-woo-products-tile.php';
            require_once OLOBUILD_PATH . 'includes/tiles/class-woo-price-tile.php';
            require_once OLOBUILD_PATH . 'includes/tiles/class-woo-minicart-tile.php';
            require_once OLOBUILD_PATH . 'includes/tiles/class-woo-addtocart-tile.php';
            require_once OLOBUILD_PATH . 'includes/tiles/class-woo-categories-tile.php';
            require_once OLOBUILD_PATH . 'includes/tiles/class-woo-rating-tile.php';
            require_once OLOBUILD_PATH . 'includes/tiles/class-woo-product-tabs-tile.php';
            require_once OLOBUILD_PATH . 'includes/tiles/class-woo-related-tile.php';
            require_once OLOBUILD_PATH . 'includes/tiles/class-woo-upsells-tile.php';
            require_once OLOBUILD_PATH . 'includes/tiles/class-woo-cart-tile.php';
            require_once OLOBUILD_PATH . 'includes/tiles/class-woo-checkout-tile.php';
            require_once OLOBUILD_PATH . 'includes/tiles/class-woo-checkout-multistep-tile.php';
            require_once OLOBUILD_PATH . 'includes/tiles/class-woo-product-title-tile.php';
            require_once OLOBUILD_PATH . 'includes/tiles/class-woo-product-image-tile.php';
            require_once OLOBUILD_PATH . 'includes/tiles/class-woo-product-description-tile.php';
            require_once OLOBUILD_PATH . 'includes/tiles/class-woo-product-meta-tile.php';
            require_once OLOBUILD_PATH . 'includes/tiles/class-woo-product-stock-tile.php';
            require_once OLOBUILD_PATH . 'includes/tiles/class-woo-order-tracking-tile.php';
            require_once OLOBUILD_PATH . 'includes/tiles/class-woo-breadcrumbs-tile.php';
            require_once OLOBUILD_PATH . 'includes/tiles/class-woo-notices-tile.php';
            require_once OLOBUILD_PATH . 'includes/tiles/class-woo-product-navigation-tile.php';
            require_once OLOBUILD_PATH . 'includes/tiles/class-woo-sale-badge-tile.php';
            require_once OLOBUILD_PATH . 'includes/tiles/class-woo-cross-sells-tile.php';
            require_once OLOBUILD_PATH . 'includes/tiles/class-woo-recently-viewed-tile.php';
            require_once OLOBUILD_PATH . 'includes/tiles/class-woo-product-bundle-tile.php';
            require_once OLOBUILD_PATH . 'includes/tiles/class-woo-product-filter-tile.php';
            require_once OLOBUILD_PATH . 'includes/tiles/class-woo-product-gallery-slider-tile.php';
            require_once OLOBUILD_PATH . 'includes/tiles/class-woo-quickview-tile.php';
            require_once OLOBUILD_PATH . 'includes/tiles/class-woo-wishlist-tile.php';
            require_once OLOBUILD_PATH . 'includes/tiles/class-woo-comparison-tile.php';
            require_once OLOBUILD_PATH . 'includes/tiles/class-woo-myaccount-tile.php';
        }

        // ── Tile speciali (batch tile-speciali) — require ──
        require_once OLOBUILD_PATH . 'includes/tiles/class-stackscroll-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-crtoverlay-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-physicsbin-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-scratchfx-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-particlefx-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-asciiviz-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-variablespecimen-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-presencegrid-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-leaderboard-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-scrollscrub-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-goo-tile.php';
        require_once OLOBUILD_PATH . 'includes/tiles/class-buildermock-tile.php';
        require_once OLOBUILD_PATH . 'includes/class-magnetic-cursor.php';
        Olobuild_Magnetic_Cursor::init();
        require_once OLOBUILD_PATH . 'includes/class-cursor-hud.php';
        Olobuild_Cursor_Hud::init();

        $manager = Olobuild_Tile_Manager::instance();
        $manager->register_tile( new Olobuild_Section_Tile() );
        // ── Tile speciali (batch tile-speciali) — register ──
        $manager->register_tile( new Olobuild_Stackscroll_Tile() );
        // Olobuild_Crtoverlay_Tile NON è un tile in flusso: è un EFFETTO DI PAGINA
        // (Impostazioni Pagina → Effetti di pagina), reso da render_tiles_array. La classe
        // resta require'd sopra come helper di render; nessun register_tile.
        $manager->register_tile( new Olobuild_Physicsbin_Tile() );
        $manager->register_tile( new Olobuild_Scratchfx_Tile() );
        $manager->register_tile( new Olobuild_Particlefx_Tile() );
        $manager->register_tile( new Olobuild_Asciiviz_Tile() );
        $manager->register_tile( new Olobuild_Variablespecimen_Tile() );
        $manager->register_tile( new Olobuild_Presencegrid_Tile() );
        $manager->register_tile( new Olobuild_Leaderboard_Tile() );
        $manager->register_tile( new Olobuild_Scrollscrub_Tile() );
        $manager->register_tile( new Olobuild_Goo_Tile() );
        $manager->register_tile( new Olobuild_BuilderMock_Tile() );
        $manager->register_tile( new Olobuild_Column_Tile() );
        $manager->register_tile( new Olobuild_Hero_Tile() );
        $manager->register_tile( new Olobuild_HeroSplit_Tile() );
        $manager->register_tile( new Olobuild_AudioHero_Tile() );
        $manager->register_tile( new Olobuild_SectionHeader_Tile() );
        $manager->register_tile( new Olobuild_InfoCards_Tile() );
        $manager->register_tile( new Olobuild_WorkList_Tile() );
        $manager->register_tile( new Olobuild_WorkGrid_Tile() );
        $manager->register_tile( new Olobuild_StatStrip_Tile() );
        $manager->register_tile( new Olobuild_HoursStrip_Tile() );
        $manager->register_tile( new Olobuild_HoverList_Tile() );
        $manager->register_tile( new Olobuild_LookbookMixer_Tile() );
        $manager->register_tile( new Olobuild_CategoryRail_Tile() );
        $manager->register_tile( new Olobuild_BeforeAfter_Tile() );
        $manager->register_tile( new Olobuild_TripFinder_Tile() );
        $manager->register_tile( new Olobuild_MaskedVideoHero_Tile() );
        $manager->register_tile( new Olobuild_SearchHero_Tile() );
        $manager->register_tile( new Olobuild_SmearHero_Tile() );
        $manager->register_tile( new Olobuild_PhotoCover_Tile() );
        $manager->register_tile( new Olobuild_Masthead_Tile() );
        $manager->register_tile( new Olobuild_MatchFixtures_Tile() );
        $manager->register_tile( new Olobuild_ShowcaseGrid_Tile() );
        $manager->register_tile( new Olobuild_ProductGrid_Tile() );
        $manager->register_tile( new Olobuild_AnnouncementBar_Tile() );
        $manager->register_tile( new Olobuild_IntroSplit_Tile() );
        $manager->register_tile( new Olobuild_MediaCTA_Tile() );
        $manager->register_tile( new Olobuild_ImageHero_Tile() );
        $manager->register_tile( new Olobuild_GlowHero_Tile() );
        $manager->register_tile( new Olobuild_TerminalHero_Tile() );
        $manager->register_tile( new Olobuild_OloxNav_Tile() );
        $manager->register_tile( new Olobuild_OloxHero_Tile() );
        $manager->register_tile( new Olobuild_OloxMarquee_Tile() );
        $manager->register_tile( new Olobuild_OloxCards_Tile() );
        $manager->register_tile( new Olobuild_OloxSticky_Tile() );
        $manager->register_tile( new Olobuild_OloxPricing_Tile() );
        $manager->register_tile( new Olobuild_OloxStatement_Tile() );
        $manager->register_tile( new Olobuild_OloxList_Tile() );
        $manager->register_tile( new Olobuild_OloxLessons_Tile() );
        $manager->register_tile( new Olobuild_OloxQuiz_Tile() );
        $manager->register_tile( new Olobuild_OloxBanner_Tile() );
        $manager->register_tile( new Olobuild_OloxFoot_Tile() );
        $manager->register_tile( new Olobuild_OloxPageFx_Tile() );
        $manager->register_tile( new Olobuild_OloxManual_Tile() );
        $manager->register_tile( new Olobuild_OloxPanel_Tile() );
        $manager->register_tile( new Olobuild_OloxRail_Tile() );
        $manager->register_tile( new Olobuild_OloxScene_Tile() );
        $manager->register_tile( new Olobuild_Coverdots_Tile() );
        $manager->register_tile( new Olobuild_Bottombar_Tile() );
        $manager->register_tile( new Olobuild_Pagelight_Tile() );
        $manager->register_tile( new Olobuild_OloxHome_Tile() );
        $manager->register_tile( new Olobuild_ProductHero_Tile() );
        $manager->register_tile( new Olobuild_NorthVideoHero_Tile() );
        $manager->register_tile( new Olobuild_NorthQuoteSlider_Tile() );
        $manager->register_tile( new Olobuild_FeaturedStory_Tile() );
        $manager->register_tile( new Olobuild_GlowGallery_Tile() );
        $manager->register_tile( new Olobuild_ChatHero_Tile() );
        $manager->register_tile( new Olobuild_ProductCards_Tile() );
        $manager->register_tile( new Olobuild_StepTimeline_Tile() );
        $manager->register_tile( new Olobuild_Process_Steps_Tile() );
        $manager->register_tile( new Olobuild_CtaBanner_Tile() );
        $manager->register_tile( new Olobuild_TrustStrip_Tile() );
        $manager->register_tile( new Olobuild_Content_Tile() );
        $manager->register_tile( new Olobuild_Image_Tile() );
        $manager->register_tile( new Olobuild_Video_Tile() );
        $manager->register_tile( new Olobuild_Spacer_Tile() );
        $manager->register_tile( new Olobuild_Button_Tile() );
        $manager->register_tile( new Olobuild_Gallery_Tile() );
        $manager->register_tile( new Olobuild_Row_Tile() );
        $manager->register_tile( new Olobuild_Testimonial_Tile() );
        $manager->register_tile( new Olobuild_Pricing_Tile() );
        $manager->register_tile( new Olobuild_Counter_Tile() );
        $manager->register_tile( new Olobuild_IconBox_Tile() );
        $manager->register_tile( new Olobuild_Alert_Tile() );
        $manager->register_tile( new Olobuild_Badge_Tile() );
        $manager->register_tile( new Olobuild_Team_Tile() );
        $manager->register_tile( new Olobuild_Accordion_Tile() );
        $manager->register_tile( new Olobuild_IconTabs_Tile() );
        $manager->register_tile( new Olobuild_Projector_Tile() );
        $manager->register_tile( new Olobuild_Finder_Tile() );
        $manager->register_tile( new Olobuild_Builder_Tile() );
        $manager->register_tile( new Olobuild_Mixer_Tile() );
        $manager->register_tile( new Olobuild_Schedule_Tile() );
        $manager->register_tile( new Olobuild_Hotspots_Tile() );
        $manager->register_tile( new Olobuild_Scaler_Tile() );
        $manager->register_tile( new Olobuild_Timezone_Tile() );
        $manager->register_tile( new Olobuild_Availability_Tile() );

        $manager->register_tile( new Olobuild_Social_Tile() );
        $manager->register_tile( new Olobuild_Map_Tile() );
        $manager->register_tile( new Olobuild_Countdown_Tile() );
        $manager->register_tile( new Olobuild_Headline_Tile() );
        $manager->register_tile( new Olobuild_Html_Tile() );
        $manager->register_tile( new Olobuild_List_Tile() );
        $manager->register_tile( new Olobuild_TextBlock_Tile() );
        $manager->register_tile( new Olobuild_Slideshow_Tile() );
        $manager->register_tile( new Olobuild_Table_Tile() );
        $manager->register_tile( new Olobuild_Overlay_Tile() );
        $manager->register_tile( new Olobuild_Divider_Tile() );
        $manager->register_tile( new Olobuild_Progress_Tile() );
        $manager->register_tile( new Olobuild_DescList_Tile() );
        $manager->register_tile( new Olobuild_Panel_Tile() );
        $manager->register_tile( new Olobuild_Quotation_Tile() );
        $manager->register_tile( new Olobuild_Code_Tile() );
        $manager->register_tile( new Olobuild_Icon_Tile() );
        $manager->register_tile( new Olobuild_Totop_Tile() );
        $manager->register_tile( new Olobuild_Fragment_Tile() );
        $manager->register_tile( new Olobuild_Grid_Tile() );
        $manager->register_tile( new Olobuild_Switcher_Tile() );
        $manager->register_tile( new Olobuild_SwitcherPanel_Tile() );
        $manager->register_tile( new Olobuild_Nav_Tile() );
        $manager->register_tile( new Olobuild_Subnav_Tile() );
        $manager->register_tile( new Olobuild_PanelSlider_Tile() );
        $manager->register_tile( new Olobuild_OverlaySlider_Tile() );
        $manager->register_tile( new Olobuild_OverlayGrid_Tile() );
        $manager->register_tile( new Olobuild_Popover_Tile() );
        $manager->register_tile( new Olobuild_Breadcrumbs_Tile() );
        $manager->register_tile( new Olobuild_Search_Tile() );
        $manager->register_tile( new Olobuild_SiteLogo_Tile() );
        $manager->register_tile( new Olobuild_NavMenu_Tile() );
        $manager->register_tile( new Olobuild_PostGrid_Tile() );
        $manager->register_tile( new Olobuild_ProSlider_Tile() );
        $manager->register_tile( new Olobuild_Popup_Tile() );
        $manager->register_tile( new Olobuild_MegaMenu_Tile() );
        $manager->register_tile( new Olobuild_OloHeader_Tile() );
        $manager->register_tile( new Olobuild_InnerColumns_Tile() );
        $manager->register_tile( new Olobuild_Timeline_Tile() );
        $manager->register_tile( new Olobuild_FlipCard_Tile() );
        $manager->register_tile( new Olobuild_ImgCompare_Tile() );
        $manager->register_tile( new Olobuild_Marquee_Tile() );
        $manager->register_tile( new Olobuild_ToggleBtn_Tile() );
        $manager->register_tile( new Olobuild_Form_Tile() );
        $manager->register_tile( new Olobuild_KillNextPrev_Tile() );
        $manager->register_tile( new Olobuild_LangSwitcher_Tile() );
        $manager->register_tile( new Olobuild_LiveSearch_Tile() );
        $manager->register_tile( new Olobuild_ShatteredImage_Tile() );
        $manager->register_tile( new Olobuild_Textmask_Tile() );
        $manager->register_tile( new Olobuild_Blendtext_Tile() );
        $manager->register_tile( new Olobuild_ProGallery_Tile() );
        $manager->register_tile( new Olobuild_PdfViewer_Tile() );
        $manager->register_tile( new Olobuild_PdfPro_Tile() );
        $manager->register_tile( new Olobuild_Starrating_Tile() );
        $manager->register_tile( new Olobuild_Iconlist_Tile() );
        $manager->register_tile( new Olobuild_Animatedheading_Tile() );
        $manager->register_tile( new Olobuild_Toc_Tile() );
        $manager->register_tile( new Olobuild_Lottie_Tile() );
        $manager->register_tile( new Olobuild_Sharebuttons_Tile() );
        $manager->register_tile( new Olobuild_Scrollprogress_Tile() );
        $manager->register_tile( new Olobuild_Newsticker_Tile() );
        $manager->register_tile( new Olobuild_Hotspot_Tile() );
        $manager->register_tile( new Olobuild_Loginform_Tile() );
        $manager->register_tile( new Olobuild_Videoplaylist_Tile() );
        $manager->register_tile( new Olobuild_Textpath_Tile() );

        $manager->register_tile( new Olobuild_Chart_Tile() );
        $manager->register_tile( new Olobuild_Audio_Tile() );
        $manager->register_tile( new Olobuild_Shapedivider_Tile() );
        $manager->register_tile( new Olobuild_Countercircle_Tile() );
        $manager->register_tile( new Olobuild_PostMeta_Tile() );
        $manager->register_tile( new Olobuild_RelatedPosts_Tile() );
        $manager->register_tile( new Olobuild_Wpcomments_Tile() );
        $manager->register_tile( new Olobuild_Pagination_Tile() );
        $manager->register_tile( new Olobuild_Carousel_Tile() );
        $manager->register_tile( new Olobuild_Authorbox_Tile() );
        $manager->register_tile( new Olobuild_Viewscounter_Tile() );
        $manager->register_tile( new Olobuild_Menuanchor_Tile() );
        $manager->register_tile( new Olobuild_Osmmap_Tile() );
        $manager->register_tile( new Olobuild_Soundcloud_Tile() );
        $manager->register_tile( new Olobuild_Tagcloud_Tile() );
        $manager->register_tile( new Olobuild_Instagram_Tile() );
        $manager->register_tile( new Olobuild_Facebookpage_Tile() );
        $manager->register_tile( new Olobuild_Twitterfeed_Tile() );
        $manager->register_tile( new Olobuild_Postnavigation_Tile() );
        $manager->register_tile( new Olobuild_Pricelist_Tile() );
        $manager->register_tile( new Olobuild_Progresstracker_Tile() );
        $manager->register_tile( new Olobuild_Sitemap_Tile() );
        $manager->register_tile( new Olobuild_LinkInBio_Tile() );
        $manager->register_tile( new Olobuild_Shortcode_Tile() );
        $manager->register_tile( new Olobuild_TemplateEmbed_Tile() );
        $manager->register_tile( new Olobuild_Paymentbuttons_Tile() );
        $manager->register_tile( new Olobuild_Pagetitlebar_Tile() );
        $manager->register_tile( new Olobuild_Portfolio_Tile() );
        $manager->register_tile( new Olobuild_Queryloop_Tile() );
        $manager->register_tile( new Olobuild_Readingtime_Tile() );
        $manager->register_tile( new Olobuild_Darkmode_Tile() );
        $manager->register_tile( new Olobuild_Lightbox_Tile() );

        // Clod — Evoluzione (studio editorial)
        $manager->register_tile( new Olobuild_StudioHero_Tile() );
        $manager->register_tile( new Olobuild_FilmReel_Tile() );
        $manager->register_tile( new Olobuild_ScrubText_Tile() );
        $manager->register_tile( new Olobuild_ThemeDemos_Tile() );
        $manager->register_tile( new Olobuild_EvoNotes_Tile() );

        // WooCommerce tiles (solo se WooCommerce attivo)
        if ( class_exists( 'WooCommerce' ) ) {
            $manager->register_tile( new Olobuild_Woo_Products_Tile() );
            $manager->register_tile( new Olobuild_Woo_Price_Tile() );
            $manager->register_tile( new Olobuild_Woo_Minicart_Tile() );
            $manager->register_tile( new Olobuild_Woo_Addtocart_Tile() );
            $manager->register_tile( new Olobuild_Woo_Categories_Tile() );
            $manager->register_tile( new Olobuild_Woo_Rating_Tile() );
            $manager->register_tile( new Olobuild_Woo_Product_Tabs_Tile() );
            $manager->register_tile( new Olobuild_Woo_Related_Tile() );
            $manager->register_tile( new Olobuild_Woo_Upsells_Tile() );
            $manager->register_tile( new Olobuild_Woo_Cart_Tile() );
            $manager->register_tile( new Olobuild_Woo_Checkout_Tile() );
            $manager->register_tile( new Olobuild_Woo_Checkout_Multistep_Tile() );
            $manager->register_tile( new Olobuild_Woo_Product_Title_Tile() );
            $manager->register_tile( new Olobuild_Woo_Product_Image_Tile() );
            $manager->register_tile( new Olobuild_Woo_Product_Description_Tile() );
            $manager->register_tile( new Olobuild_Woo_Product_Meta_Tile() );
            $manager->register_tile( new Olobuild_Woo_Product_Stock_Tile() );
            $manager->register_tile( new Olobuild_Woo_Order_Tracking_Tile() );
            $manager->register_tile( new Olobuild_Woo_Breadcrumbs_Tile() );
            $manager->register_tile( new Olobuild_Woo_Notices_Tile() );
            $manager->register_tile( new Olobuild_Woo_Product_Navigation_Tile() );
            $manager->register_tile( new Olobuild_Woo_Sale_Badge_Tile() );
            $manager->register_tile( new Olobuild_Woo_Cross_Sells_Tile() );
            $manager->register_tile( new Olobuild_Woo_Recently_Viewed_Tile() );
            $manager->register_tile( new Olobuild_Woo_Product_Bundle_Tile() );
            $manager->register_tile( new Olobuild_Woo_Product_Filter_Tile() );
            $manager->register_tile( new Olobuild_Woo_Product_Gallery_Slider_Tile() );
            $manager->register_tile( new Olobuild_Woo_Quickview_Tile() );
            $manager->register_tile( new Olobuild_Woo_Wishlist_Tile() );
            $manager->register_tile( new Olobuild_Woo_Comparison_Tile() );
            $manager->register_tile( new Olobuild_Woo_Myaccount_Tile() );
        }

        // Hook per plugin esterni che vogliono registrare tile
        do_action( 'olobuild_register_external_tiles', $manager );
    }

    /**
     * Get active single templates map: { post_type => template_id }.
     */
    private function get_active_singles_map() {
        $post_types = get_post_types( [ 'public' => true ], 'names' );
        $result     = [];
        foreach ( $post_types as $pt ) {
            if ( in_array( $pt, [ 'page', 'attachment' ], true ) ) continue;
            $tpl_id = (int) get_option( "olobuild_active_single_{$pt}", 0 );
            if ( $tpl_id ) {
                $result[ $pt ] = $tpl_id;
            }
        }
        // Ensure JS gets an object even if empty
        return ! empty( $result ) ? $result : new stdClass;
    }

    /**
     * Get public post types (excluding page and attachment) for the builder UI.
     */
    private function get_public_taxonomies() {
        $taxonomies = get_taxonomies( [ 'public' => true ], 'objects' );
        $result     = [ [ 'value' => '', 'label' => 'Nessuna' ] ];
        foreach ( $taxonomies as $tax ) {
            $result[] = [
                'value' => $tax->name,
                'label' => $tax->label,
            ];
        }
        return $result;
    }

    private function get_public_post_types() {
        $post_types = get_post_types( [ 'public' => true ], 'objects' );
        $result     = [];
        foreach ( $post_types as $pt ) {
            if ( in_array( $pt->name, [ 'page', 'attachment' ], true ) ) continue;
            $result[] = [
                'value' => $pt->name,
                'label' => $pt->label,
            ];
        }
        return $result;
    }

    /**
     * Get all published Olobuild templates for the template selector (e.g. Popup element).
     */
    private function get_template_list() {
        $db     = new Olobuild_Database();
        $result = $db->list_templates( [ 'status' => 'published', 'per_page' => 100 ] );
        $list   = [ [ 'value' => 0, 'label' => '— Seleziona template —' ] ];
        foreach ( $result['items'] as $t ) {
            $list[] = [ 'value' => (int) $t['id'], 'label' => $t['title'] ];
        }
        return $list;
    }

    /**
     * Get published templates of type "megapanel" for the mega menu panel selector.
     */
    private function get_megapanel_templates() {
        $db     = new Olobuild_Database();
        $result = $db->list_templates( [ 'status' => 'published', 'type' => 'megapanel', 'per_page' => 100 ] );
        $list   = [ [ 'value' => 0, 'label' => 'Auto (colonne)' ] ];
        foreach ( $result['items'] as $t ) {
            $list[] = [ 'value' => (int) $t['id'], 'label' => $t['title'] ];
        }
        return $list;
    }

    /**
     * Get published templates of type "widget" — riusabili come contenuto
     * delle schede/items dei tile container (accordion, tab, slider, ecc.).
     */
    private function get_widget_templates() {
        $db     = new Olobuild_Database();
        $result = $db->list_templates( [ 'status' => 'published', 'type' => 'widget', 'per_page' => 100 ] );
        $list   = [ [ 'value' => 0, 'label' => '— Nessun widget —' ] ];
        foreach ( $result['items'] as $t ) {
            $list[] = [ 'value' => (int) $t['id'], 'label' => $t['title'] ];
        }
        return $list;
    }

    /**
     * Get meta prefix options for booking tiles.
     * Only includes CPTs that have an active single template.
     */
    /**
     * Mappa dei meta_key + valori distinti per ciascun public post_type, usata
     * dall'inspector per offrire menu a discesa al posto del campo testo
     * "chiave=valore". Cache transient 5 min.
     *
     * Output: [
     *   'olo_service' => [ [ 'key' => '_olo_service_type', 'label' => '_olo_service_type', 'values' => [ 'accommodation', 'restaurant', ...] ], ... ],
     *   'post'        => [ ... ],
     * ]
     */
    private function get_meta_keys_map() {
        $cache_key = 'olo_meta_keys_map_v1';
        $cached    = get_transient( $cache_key );
        if ( is_array( $cached ) ) return $cached;

        global $wpdb;
        $post_types = get_post_types( [ 'public' => true ], 'names' );
        $map = [];
        // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter -- Query DISTINCT su postmeta per costruire i menu dell'inspector; nessun helper WP equivalente per "valori distinti di una meta_key". Interpolati solo $wpdb->postmeta/$wpdb->posts (nomi tabella core); tutti i valori utente passano da $wpdb->prepare (%s). Risultato cacheato in transient (vedi set_transient a fine metodo).
        foreach ( $post_types as $type ) {
            // Top 50 meta keys per post_type (esclude _edit_*, _wp_*, _oembed_* interne)
            $keys = $wpdb->get_col( $wpdb->prepare(
                "SELECT DISTINCT pm.meta_key
                 FROM {$wpdb->postmeta} pm INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
                 WHERE p.post_type = %s AND p.post_status IN ('publish','draft','private','future')
                   AND pm.meta_key != '' AND pm.meta_key NOT LIKE %s
                   AND pm.meta_key NOT LIKE %s AND pm.meta_key NOT LIKE %s
                 ORDER BY pm.meta_key ASC LIMIT 50",
                $type, '_edit_%', '_wp_%', '_oembed_%'
            ) );
            if ( empty( $keys ) ) continue;
            $entries = [];
            foreach ( $keys as $k ) {
                // Top 50 valori distinti scartando vuoti/JSON/serializzati lunghi
                $values = $wpdb->get_col( $wpdb->prepare(
                    "SELECT DISTINCT pm.meta_value FROM {$wpdb->postmeta} pm
                     INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
                     WHERE p.post_type = %s AND pm.meta_key = %s
                       AND pm.meta_value != ''
                       AND CHAR_LENGTH(pm.meta_value) < 80
                       AND pm.meta_value NOT LIKE %s AND pm.meta_value NOT LIKE %s
                     ORDER BY pm.meta_value ASC LIMIT 50",
                    $type, $k, 'a:%', 'O:%'
                ) );
                $values = array_values( array_filter( $values, function ( $v ) {
                    return $v !== '' && ! is_serialized( $v );
                } ) );
                $entries[] = [
                    'key'    => $k,
                    'label'  => $k,
                    'values' => $values,
                ];
            }
            $map[ $type ] = $entries;
        }
        // phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
        set_transient( $cache_key, $map, 5 * MINUTE_IN_SECONDS );
        return $map;
    }

    private function get_meta_prefixes() {
        $post_types = get_post_types( [ 'public' => true, '_builtin' => false ], 'objects' );
        $result     = [];

        // Friendly names for known CPTs
        $friendly = [
            'olo_service' => 'Baita / Servizio',
            'location'    => 'Location',
            'olo_event'   => 'Evento',
        ];

        foreach ( $post_types as $pt ) {
            $tpl_id = (int) get_option( "olobuild_active_single_{$pt->name}", 0 );
            if ( ! $tpl_id ) {
                continue;
            }

            $label = $friendly[ $pt->name ] ?? ( $pt->labels->singular_name ?: $pt->label );
            $count = wp_count_posts( $pt->name );
            $n     = isset( $count->publish ) ? (int) $count->publish : 0;
            if ( $n > 0 ) {
                $label .= " ({$n})";
            }

            $result[] = [
                'value' => "_{$pt->name}_",
                'label' => $label,
            ];
        }

        // Allow themes/plugins to customize labels
        $result = apply_filters( 'olobuild_meta_prefix_options', $result );

        // Fallback: always include olo_service
        $has_service = false;
        foreach ( $result as $r ) {
            if ( $r['value'] === '_olo_service_' ) {
                $has_service = true;
                break;
            }
        }
        if ( ! $has_service ) {
            $result[] = [ 'value' => '_olo_service_', 'label' => 'Servizio' ];
        }

        return $result;
    }

    /**
     * Get all published olo_service posts for the booking picker tile.
     */
    private function get_service_list() {
        if ( ! post_type_exists( 'olo_service' ) ) {
            return [];
        }
        $posts  = get_posts( [
            'post_type'      => 'olo_service',
            'post_status'    => 'publish',
            'posts_per_page' => 100,
            'orderby'        => 'title',
            'order'          => 'ASC',
        ] );
        $result = [];
        foreach ( $posts as $p ) {
            $result[] = [
                'value' => (string) $p->ID,
                'label' => $p->post_title,
            ];
        }
        return $result;
    }

    /**
     * Get posts that use the current template (for per-post conditional visibility).
     * Looks up which post type this template is assigned to, then returns all published posts of that type.
     */
    private function get_single_post_items() {
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- lettura read-only per bootstrap del builder admin (lista post del template); nessuna modifica di stato; valore sanitizzato via absint.
        $tpl_id = absint( wp_unslash( $_GET['template_id'] ?? 0 ) );
        if ( ! $tpl_id ) return [];

        // Find which post type uses this template
        $post_types = get_post_types( [ 'public' => true ], 'names' );
        $target_pt  = '';
        foreach ( $post_types as $pt ) {
            $opt_val = get_option( "olobuild_active_single_{$pt}", 0 );
            if ( (int) $opt_val === $tpl_id ) {
                $target_pt = $pt;
                break;
            }
        }
        // Fallback: also check the template's own post meta for assigned post type
        if ( ! $target_pt ) {
            $meta_pt = get_post_meta( $tpl_id, '_olo_single_post_type', true );
            if ( $meta_pt && post_type_exists( $meta_pt ) ) {
                $target_pt = $meta_pt;
            }
        }
        if ( ! $target_pt ) return [];

        $posts  = get_posts( [
            'post_type'      => $target_pt,
            'post_status'    => 'publish',
            'posts_per_page' => 200,
            'orderby'        => 'title',
            'order'          => 'ASC',
        ] );
        $result = [];
        foreach ( $posts as $p ) {
            $result[] = [
                'value' => (string) $p->ID,
                'label' => $p->post_title,
            ];
        }
        return $result;
    }

    /**
     * Get published WP pages for select dropdowns in the builder.
     */
    private function get_wp_pages() {
        $pages  = get_pages( [ 'post_status' => 'publish', 'sort_column' => 'post_title' ] );
        $result = [ [ 'value' => '', 'label' => '— Seleziona pagina —' ] ];
        foreach ( $pages as $p ) {
            $result[] = [
                'value' => str_replace( home_url(), '', get_permalink( $p->ID ) ),
                'label' => $p->post_title,
            ];
        }
        return $result;
    }

    /**
     * Get all registered WP nav menus for the builder UI.
     */
    private function get_wp_menus() {
        $menus  = wp_get_nav_menus();
        $result = [];
        foreach ( $menus as $menu ) {
            $menu_data = [
                'id'    => $menu->term_id,
                'name'  => $menu->name,
                'items' => [],
            ];
            $nav_items = wp_get_nav_menu_items( $menu->term_id );
            if ( $nav_items && is_array( $nav_items ) ) {
                foreach ( $nav_items as $item ) {
                    $menu_data['items'][] = [
                        'id'     => $item->ID,
                        'title'  => $item->title,
                        'parent' => (int) $item->menu_item_parent,
                    ];
                }
            }
            $result[] = $menu_data;
        }
        return $result;
    }

    /**
     * Get the site logo URL (from Customizer custom_logo).
     */
    private function get_site_logo_url() {
        $custom_logo_id = get_theme_mod( 'custom_logo' );
        if ( $custom_logo_id ) {
            $url = wp_get_attachment_image_url( $custom_logo_id, 'full' );
            if ( $url ) {
                return $url;
            }
        }
        return '';
    }
}
