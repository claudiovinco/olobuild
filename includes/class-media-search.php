<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Ricerca Media — admin page for searching free stock photos, videos, audio, and 360 content.
 * UI dal design handoff "OLObuild - Ricerca Media redesign" (cockpit language, token --olo-*).
 */
class Olobuild_Media_Search {

    public static function render_page() {
        $nonce = wp_create_nonce( 'wp_rest' );
        $rest_url = esc_url_raw( rest_url( 'olobuild/v1' ) );
        $rest_url_vtour = esc_url_raw( rest_url( 'olo-vtour/v1' ) );
        $vtour_active = is_plugin_active( 'olo-vtour/olo-vtour.php' );

        // Check configured API keys (costante wp-config o opzione; nessun fallback hardcoded)
        $keys = [
            'unsplash'  => class_exists( 'Olobuild_Unsplash' ) && ! empty( Olobuild_Unsplash::get_access_key() ),
            'pexels'    => class_exists( 'Olobuild_Pexels' ) && ! empty( Olobuild_Pexels::get_api_key() ),
            'pixabay'   => class_exists( 'Olobuild_Pixabay' ) && ! empty( Olobuild_Pixabay::get_api_key() ),
            'openverse' => true,
            'freesound' => ! empty( get_option( 'olobuild_freesound_api_key', '' ) ),
            'polyhaven' => $vtour_active,
            'googlesv'  => $vtour_active,
        ];
        $providers_active = count( array_filter( $keys ) );
        $providers_total  = count( $keys );
        $settings_url     = admin_url( 'admin.php?page=olobuilder-settings&tab=stockmedia' );
        ?>
        <?php Olobuild_Builder::cockpit_shell_open( '<b>' . esc_html__( 'Ricerca Media', 'olobuild' ) . '</b>' ); ?>
        <main class="olo-cockpit-main olo-cockpit-legacy olo-ms-wrap">
            <?php
            // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML built by Olobuild_Builder::cockpit_page_head()/cockpit_button(), which escape via esc_html()/esc_url()/wp_kses_post() internally.
            echo Olobuild_Builder::cockpit_page_head( [
                'title' => __( 'Ricerca Media', 'olobuild' ),
                'sub'   => sprintf(
                    /* translators: 1: active providers, 2: total providers */
                    __( 'Foto, video, audio e panorami da provider stock, direttamente nella Media Library · %1$s/%2$s provider configurati', 'olobuild' ),
                    '<b>' . (int) $providers_active . '</b>',
                    (int) $providers_total
                ),
                'actions' => Olobuild_Builder::cockpit_button( [
                    'label'   => __( 'Configura provider', 'olobuild' ),
                    'variant' => 'sec',
                    'href'    => $settings_url,
                    'icon'    => '<path d="M4 7h10M18 7h2M4 17h2M10 17h10M14 4v6M8 14v6"/>',
                ] ),
            ] );
            // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
            ?>

            <!-- Pannello di ricerca unificato -->
            <section class="olo-ms-panel">
                <div class="olo-ms-tabs" role="tablist">
                    <button class="olo-ms-tab active" role="tab" aria-selected="true" data-tab="photo">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="9" cy="9" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>
                        <?php esc_html_e( 'Foto', 'olobuild' ); ?>
                    </button>
                    <button class="olo-ms-tab" role="tab" aria-selected="false" data-tab="video">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M7 4v16M17 4v16M3 9h4M3 15h4M17 9h4M17 15h4"/></svg>
                        <?php esc_html_e( 'Video', 'olobuild' ); ?>
                    </button>
                    <button class="olo-ms-tab" role="tab" aria-selected="false" data-tab="photo360">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a14 14 0 010 18M12 3a14 14 0 000 18"/></svg>
                        <?php esc_html_e( 'Foto 360', 'olobuild' ); ?>
                    </button>
                    <button class="olo-ms-tab" role="tab" aria-selected="false" data-tab="video360">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 109-9"/><path d="M3 4v5h5"/><circle cx="12" cy="12" r="2.5"/></svg>
                        <?php esc_html_e( 'Video 360', 'olobuild' ); ?>
                    </button>
                    <button class="olo-ms-tab" role="tab" aria-selected="false" data-tab="audio">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18V6l10-2v12"/><circle cx="6.5" cy="18" r="2.5"/><circle cx="16.5" cy="16" r="2.5"/></svg>
                        <?php esc_html_e( 'Audio', 'olobuild' ); ?>
                    </button>
                </div>

                <div class="olo-ms-providers" id="olo-ms-providers"></div>

                <!-- Google SV panel (hidden by default) -->
                <div id="olo-ms-gsv-panel" class="olo-ms-gsv" style="display:none;">
                    <div class="note">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;margin-top:1px;"><circle cx="12" cy="12" r="9"/><path d="M12 11v5M12 8h.01"/></svg>
                        <span><?php
                            printf(
                                /* translators: %1$s, %2$s: bold tags content */
                                esc_html__( 'Incolla un URL Google Maps, coordinate %1$s o un %2$s per scaricare un panorama Street View equirettangolare.', 'olobuild' ),
                                '<b>lat,lng</b>',
                                '<b>pano_id</b>'
                            );
                        ?></span>
                    </div>
                    <div class="olo-ms-search-row">
                        <div class="olo-ms-search-field">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 21s-7-6.1-7-11a7 7 0 0114 0c0 4.9-7 11-7 11z"/><circle cx="12" cy="10" r="2.5"/></svg>
                            <input type="text" id="olo-ms-gsv-input" placeholder="<?php echo esc_attr__( 'https://www.google.com/maps/@45.4642,9.1900,3a… oppure 45.4642,9.1900', 'olobuild' ); ?>" autocomplete="off" />
                        </div>
                        <span class="olo-ms-filter" style="border-radius:10px;">
                            <label for="olo-ms-gsv-zoom"><?php esc_html_e( 'Zoom', 'olobuild' ); ?></label>
                            <select id="olo-ms-gsv-zoom">
                                <option value="2"><?php esc_html_e( '2 · bassa', 'olobuild' ); ?></option>
                                <option value="3" selected><?php esc_html_e( '3 · media', 'olobuild' ); ?></option>
                                <option value="4"><?php esc_html_e( '4 · alta', 'olobuild' ); ?></option>
                            </select>
                        </span>
                        <button id="olo-ms-gsv-btn" class="olo-ms-search-btn"><?php esc_html_e( 'Cerca panorama', 'olobuild' ); ?></button>
                    </div>
                    <div id="olo-ms-gsv-result"></div>
                </div>

                <!-- Search row -->
                <div class="olo-ms-search-row" id="olo-ms-search-row">
                    <div class="olo-ms-search-field">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
                        <input type="text" id="olo-ms-query" placeholder="<?php echo esc_attr__( 'Cerca per parola chiave…', 'olobuild' ); ?>" autocomplete="off" />
                        <button class="clr" id="olo-ms-clear" title="<?php echo esc_attr__( 'Svuota', 'olobuild' ); ?>" style="display:none;">&times;</button>
                    </div>
                    <button id="olo-ms-search-btn" class="olo-ms-search-btn">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
                        <?php esc_html_e( 'Cerca', 'olobuild' ); ?>
                    </button>
                </div>

                <!-- Quick queries -->
                <div class="olo-ms-quick" id="olo-ms-quick">
                    <span class="lbl"><?php esc_html_e( 'Prova:', 'olobuild' ); ?></span>
                </div>

                <!-- Filtri (popolati da JS in base a tab/provider) -->
                <div id="olo-ms-filters" class="olo-ms-filters" style="display:none;"></div>
            </section>

            <!-- Status -->
            <div id="olo-ms-status" class="olo-ms-status"></div>

            <!-- Results -->
            <div id="olo-ms-results"></div>

            <!-- Pagination -->
            <nav id="olo-ms-pagination" class="olo-ms-pages"></nav>

            <!-- Preview Modal -->
            <div id="olo-ms-modal" class="olo-ms-modal" role="dialog" aria-modal="true" style="display:none;">
                <div class="ovl"></div>
                <div class="box">
                    <button class="x" title="<?php echo esc_attr__( 'Chiudi', 'olobuild' ); ?>">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M6 6l12 12M18 6L6 18"/></svg>
                    </button>
                    <div id="olo-ms-modal-body"></div>
                </div>
            </div>
        </main>
        <?php Olobuild_Builder::cockpit_shell_close(); ?>

        <style>
            /* ── Ricerca Media — redesign (token mancanti in dashboard.css, scoped) ── */
            .olo-ms-wrap {
                --olo-border-strong: #d1d5db;
                --olo-border-focus:  #3fa23f;
                --olo-shadow-focus:  0 0 0 3px rgba(63,162,63,.30);
                --olo-text-faint:    #9ca3af;
                --olo-navy:          #0f172a;
                --olo-navy-hover:    #1e293b;
                --olo-bg-softer:     #f3f4f6;
                --olo-slate-300:     #cbd5e1;
                --olo-primary-300:   #8fcb78;
                --olo-shadow-lg:     0 8px 32px rgba(0,0,0,.10);
                --olo-shadow-xl:     0 24px 64px rgba(0,0,0,.15);
                --olo-info-50:       #eff6ff;
                --olo-info-soft:     #dbeafe;
                --olo-info-dark:     #1d4ed8;
                --olo-radius-lg:     12px;
                --olo-ease-out:      cubic-bezier(0.25, 0.46, 0.45, 0.94);
                --olo-transition:    all .2s cubic-bezier(.4, 0, .2, 1);
                display: flex; flex-direction: column; gap: 16px;
            }

            /* ── Search panel ── */
            .olo-ms-panel {
                background: var(--olo-card); border: 1px solid var(--olo-border);
                border-radius: var(--olo-radius-lg); box-shadow: var(--olo-shadow-sm);
                padding: 16px 18px 14px;
                display: flex; flex-direction: column; gap: 12px;
            }

            /* type segmented control */
            .olo-ms-tabs {
                display: inline-flex; gap: 2px; align-self: flex-start;
                background: var(--olo-bg-muted); border: 1px solid var(--olo-border-light);
                padding: 3px; border-radius: 10px;
            }
            .olo-ms-tab {
                display: inline-flex; align-items: center; gap: 7px;
                padding: 7px 14px; border: 0; border-radius: 8px;
                background: transparent; font: inherit; font-size: 13px; font-weight: 600;
                color: var(--olo-text-muted); cursor: pointer; transition: var(--olo-transition);
            }
            .olo-ms-tab svg { opacity: .75; }
            .olo-ms-tab:hover { color: var(--olo-text); }
            .olo-ms-tab.active {
                background: #fff; color: var(--olo-text-strong);
                box-shadow: var(--olo-shadow-sm), 0 0 0 1px var(--olo-border);
            }
            .olo-ms-tab.active svg { color: var(--olo-primary); opacity: 1; }

            /* provider chips */
            .olo-ms-providers { display: flex; gap: 8px; flex-wrap: wrap; align-items: center; }
            .olo-ms-providers .lbl {
                font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: .05em;
                color: var(--olo-text-light); margin-right: 2px;
            }
            .olo-ms-provider {
                display: inline-flex; align-items: center; gap: 8px;
                padding: 6px 14px; border-radius: 99px;
                border: 1px solid var(--olo-border); background: #fff;
                font: inherit; font-size: 13px; font-weight: 600; color: var(--olo-text-soft);
                cursor: pointer; transition: var(--olo-transition); position: relative;
            }
            .olo-ms-provider:hover { border-color: var(--olo-border-strong); }
            .olo-ms-provider.active {
                background: var(--olo-navy); border-color: var(--olo-navy); color: #fff;
            }
            .olo-ms-provider .dot { width: 7px; height: 7px; border-radius: 99px; flex-shrink: 0; }
            .olo-ms-provider .dot.ok { background: var(--olo-success); }
            .olo-ms-provider.active .dot.ok { box-shadow: 0 0 0 3px rgba(34,197,94,.25); }
            .olo-ms-provider.disabled {
                opacity: .55; cursor: not-allowed; background: var(--olo-bg-softer);
                color: var(--olo-text-light);
            }
            .olo-ms-provider .dot.no { background: var(--olo-danger); }
            .olo-ms-provider .lock { display: inline-flex; color: var(--olo-text-light); }
            .olo-ms-provider .tip {
                position: absolute; left: 50%; bottom: calc(100% + 7px); transform: translateX(-50%);
                background: var(--olo-navy); color: #fff; font-size: 11px; font-weight: 500;
                padding: 5px 10px; border-radius: 6px; white-space: nowrap;
                opacity: 0; pointer-events: none; transition: opacity .15s; z-index: 20;
                box-shadow: var(--olo-shadow-md);
            }
            .olo-ms-provider.disabled:hover .tip { opacity: 1; }

            /* search row */
            .olo-ms-search-row { display: flex; gap: 10px; align-items: stretch; }
            .olo-ms-search-field {
                flex: 1; display: flex; align-items: center; gap: 10px;
                background: var(--olo-bg-soft); border: 1px solid var(--olo-border);
                border-radius: 10px; padding: 0 14px; height: 44px;
                transition: var(--olo-transition);
            }
            .olo-ms-search-field:focus-within {
                border-color: var(--olo-border-focus); background: #fff;
                box-shadow: var(--olo-shadow-focus);
            }
            .olo-ms-search-field svg { color: var(--olo-text-light); flex-shrink: 0; }
            .olo-ms-search-field input {
                flex: 1; border: 0; outline: 0; background: transparent; box-shadow: none;
                font: inherit; font-size: 15px; color: var(--olo-text);
                min-width: 0;
            }
            .olo-ms-search-field input::placeholder { color: var(--olo-text-faint); }
            .olo-ms-search-field .clr {
                border: 0; background: transparent; cursor: pointer; color: var(--olo-text-light);
                font-size: 16px; line-height: 1; padding: 4px; border-radius: 6px;
            }
            .olo-ms-search-field .clr:hover { color: var(--olo-text); background: var(--olo-bg-muted); }
            .olo-ms-search-btn {
                display: inline-flex; align-items: center; gap: 8px;
                padding: 0 22px; border: 0; border-radius: 10px;
                background: var(--olo-primary); color: #fff;
                font: inherit; font-size: 14px; font-weight: 600; cursor: pointer;
                transition: var(--olo-transition);
            }
            .olo-ms-search-btn:hover { background: var(--olo-primary-hover); }
            .olo-ms-search-btn:active { transform: translateY(1px); }

            /* quick queries */
            .olo-ms-quick { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
            .olo-ms-quick .lbl { font-size: 12px; color: var(--olo-text-light); font-weight: 600; }
            .olo-ms-quick button {
                border: 1px solid var(--olo-border-light); background: var(--olo-bg-soft);
                border-radius: 99px; padding: 4px 12px;
                font: inherit; font-size: 12px; font-weight: 500; color: var(--olo-text-muted);
                cursor: pointer; transition: var(--olo-transition);
            }
            .olo-ms-quick button:hover {
                border-color: var(--olo-primary-200); background: var(--olo-primary-50);
                color: var(--olo-primary-dark);
            }

            /* ── filter row ── */
            .olo-ms-filters {
                display: flex; gap: 10px; align-items: center; flex-wrap: wrap;
                padding-top: 12px; border-top: 1px dashed var(--olo-border-light);
            }
            .olo-ms-filters > svg { color: var(--olo-text-light); }
            .olo-ms-filter {
                display: inline-flex; align-items: center; gap: 7px;
                background: var(--olo-bg-soft); border: 1px solid var(--olo-border-light);
                border-radius: 8px; padding: 5px 8px 5px 11px;
            }
            .olo-ms-filter label {
                font-size: 12px; font-weight: 600; color: var(--olo-text-muted); white-space: nowrap;
            }
            .olo-ms-filter select, .olo-ms-filter input[type="number"] {
                border: 0; outline: 0; background: transparent; box-shadow: none;
                font: inherit; font-size: 13px; font-weight: 600; color: var(--olo-text);
                cursor: pointer; max-width: 200px;
            }
            .olo-ms-filter input[type="number"] { width: 64px; cursor: text; }
            .olo-ms-filter input[type="number"]::placeholder { color: var(--olo-text-faint); font-weight: 400; }
            .olo-ms-filters .sep { font-size: 12px; color: var(--olo-text-faint); }
            .olo-ms-filters .reset {
                border: 0; background: transparent; font: inherit; font-size: 12px; font-weight: 600;
                color: var(--olo-text-light); cursor: pointer; text-decoration: underline;
                text-underline-offset: 2px;
            }
            .olo-ms-filters .reset:hover { color: var(--olo-danger); }

            /* ── status row ── */
            .olo-ms-status {
                display: flex; align-items: center; gap: 10px;
                font-size: 13px; color: var(--olo-text-muted); min-height: 20px;
            }
            .olo-ms-status b { color: var(--olo-text-strong); }
            .olo-ms-status .err { color: var(--olo-danger); }
            .olo-ms-status .via {
                display: inline-flex; align-items: center; gap: 6px;
                font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: .04em;
                color: var(--olo-text-light);
                background: #fff; border: 1px solid var(--olo-border-light);
                padding: 3px 9px; border-radius: 99px;
            }
            .olo-ms-status .via .dot { width: 6px; height: 6px; border-radius: 99px; background: var(--olo-success); }

            /* ── results: photo masonry ── */
            .olo-ms-masonry { columns: 4 240px; column-gap: 14px; }
            .olo-ms-card {
                position: relative; border-radius: 12px; overflow: hidden;
                background: var(--olo-bg-softer); cursor: zoom-in;
                break-inside: avoid; margin: 0 0 14px;
                border: 1px solid var(--olo-border-light);
            }
            .olo-ms-card img { width: 100%; height: auto; display: block; }
            .olo-ms-card[style*="aspect-ratio"] img { height: 100%; object-fit: cover; }
            .olo-ms-card .veil {
                position: absolute; inset: 0; display: flex; flex-direction: column;
                justify-content: space-between; padding: 10px;
                background: linear-gradient(rgba(2,6,23,.38), transparent 38%, transparent 60%, rgba(2,6,23,.62));
                opacity: 0; transition: opacity .18s ease;
            }
            .olo-ms-card:hover .veil { opacity: 1; }
            .olo-ms-card .veil .top { display: flex; justify-content: flex-end; gap: 6px; }
            .olo-ms-card .veil .bottom { display: flex; align-items: flex-end; gap: 8px; }
            .olo-ms-card .who { flex: 1; min-width: 0; }
            .olo-ms-card .who .nm {
                color: #fff; font-size: 12px; font-weight: 600;
                overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
            }
            .olo-ms-card .who .dim { color: rgba(255,255,255,.75); font-size: 10.5px; font-variant-numeric: tabular-nums; }
            .olo-ms-badge {
                position: absolute; top: 10px; left: 10px;
                background: rgba(2,6,23,.55); color: #fff; backdrop-filter: blur(4px);
                font-size: 10px; font-weight: 700; letter-spacing: .04em;
                padding: 3px 9px; border-radius: 99px;
                display: inline-flex; align-items: center; gap: 5px;
                pointer-events: none; z-index: 1;
            }
            .olo-ms-iconbtn {
                width: 30px; height: 30px; border-radius: 8px; border: 0;
                background: rgba(255,255,255,.92); color: var(--olo-text);
                display: grid; place-items: center; cursor: pointer;
                transition: var(--olo-transition);
            }
            .olo-ms-iconbtn:hover { background: #fff; transform: translateY(-1px); }

            /* import button (shared) */
            .olo-ms-import {
                display: inline-flex; align-items: center; gap: 7px;
                border: 0; border-radius: 8px; padding: 7px 13px;
                background: var(--olo-primary); color: #fff;
                font: inherit; font-size: 12px; font-weight: 600; cursor: pointer;
                transition: var(--olo-transition); white-space: nowrap;
            }
            .olo-ms-import:hover { background: var(--olo-primary-hover); }
            .olo-ms-import.busy { opacity: .75; cursor: wait; }
            .olo-ms-import.done { background: var(--olo-success-dark); cursor: default; }
            .olo-ms-import .spin {
                width: 12px; height: 12px; border-radius: 99px;
                border: 2px solid rgba(255,255,255,.4); border-top-color: #fff;
                animation: oloMsSpin .7s linear infinite;
            }
            @keyframes oloMsSpin { to { transform: rotate(360deg); } }

            /* ── video grid ── */
            .olo-ms-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 14px; }
            .olo-ms-vcard {
                position: relative; border-radius: 12px; overflow: hidden; cursor: zoom-in;
                aspect-ratio: 16/9; background: var(--olo-bg-softer);
                border: 1px solid var(--olo-border-light);
            }
            .olo-ms-vcard img, .olo-ms-vcard video { width: 100%; height: 100%; object-fit: cover; display: block; }
            .olo-ms-vcard .veil {
                position: absolute; inset: 0; display: flex; align-items: center; justify-content: center;
                background: rgba(2,6,23,.28); opacity: 0; transition: opacity .18s;
            }
            .olo-ms-vcard:hover .veil { opacity: 1; }
            .olo-ms-vcard .playbtn {
                width: 46px; height: 46px; border-radius: 99px; border: 0;
                background: rgba(255,255,255,.95); color: var(--olo-navy);
                display: grid; place-items: center; cursor: pointer;
                box-shadow: var(--olo-shadow-lg);
            }
            .olo-ms-vcard .dur {
                position: absolute; right: 10px; bottom: 10px;
                background: rgba(2,6,23,.7); color: #fff;
                font-size: 11px; font-weight: 700; font-variant-numeric: tabular-nums;
                padding: 2px 8px; border-radius: 6px;
            }
            .olo-ms-vcard .who {
                position: absolute; left: 10px; bottom: 10px;
                color: #fff; font-size: 11.5px; font-weight: 600;
                text-shadow: 0 1px 4px rgba(0,0,0,.5);
                opacity: 0; transition: opacity .18s;
            }
            .olo-ms-vcard:hover .who { opacity: 1; }

            /* hdri tags */
            .olo-ms-tagrow { position: absolute; left: 10px; bottom: 10px; display: flex; gap: 4px; flex-wrap: wrap; }
            .olo-ms-tag {
                background: rgba(2,6,23,.55); color: #fff; backdrop-filter: blur(4px);
                font-size: 10px; font-weight: 600; padding: 2px 8px; border-radius: 99px;
            }

            /* ── audio list ── */
            .olo-ms-audio-list { display: flex; flex-direction: column; gap: 8px; max-width: 980px; }
            .olo-ms-arow {
                display: flex; align-items: center; gap: 14px;
                background: #fff; border: 1px solid var(--olo-border);
                border-radius: 12px; padding: 10px 14px;
                transition: var(--olo-transition);
            }
            .olo-ms-arow:hover { border-color: var(--olo-border-strong); box-shadow: var(--olo-shadow-sm); }
            .olo-ms-arow.playing { border-color: var(--olo-primary-300); box-shadow: 0 0 0 3px var(--olo-primary-50); }
            .olo-ms-aplay {
                width: 40px; height: 40px; border-radius: 99px; border: 0; flex-shrink: 0;
                background: var(--olo-navy); color: #fff; cursor: pointer;
                display: grid; place-items: center; transition: var(--olo-transition);
            }
            .olo-ms-aplay:hover { background: var(--olo-navy-hover); }
            .olo-ms-arow.playing .olo-ms-aplay { background: var(--olo-primary); }
            .olo-ms-ainfo { flex: 1; min-width: 0; }
            .olo-ms-ainfo .nm {
                font-size: 13.5px; font-weight: 600; color: var(--olo-text-strong);
                overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
            }
            .olo-ms-ainfo .meta {
                display: flex; gap: 10px; font-size: 11.5px; color: var(--olo-text-muted); margin-top: 2px;
                flex-wrap: wrap;
            }
            .olo-ms-ainfo .meta .lic {
                font-weight: 700; font-size: 10px; letter-spacing: .04em;
                color: var(--olo-primary-dark); background: var(--olo-primary-50);
                padding: 1px 7px; border-radius: 99px; align-self: center;
            }
            .olo-ms-wave {
                display: flex; align-items: center; gap: 2px; height: 36px;
                width: 240px; flex-shrink: 0; position: relative; overflow: hidden; border-radius: 6px;
            }
            .olo-ms-wave i {
                flex: 1; background: var(--olo-slate-300); border-radius: 2px;
                min-height: 3px; transition: background .2s;
            }
            .olo-ms-arow.playing .olo-ms-wave i.lit { background: var(--olo-primary); }
            .olo-ms-arow .dur {
                font-size: 12px; font-weight: 600; color: var(--olo-text-muted);
                font-variant-numeric: tabular-nums; width: 42px; text-align: right; flex-shrink: 0;
            }

            /* ── skeletons ── */
            @keyframes oloMsShimmer { from { background-position: 200% 0; } to { background-position: -200% 0; } }
            .olo-ms-skel {
                border-radius: 12px;
                background: linear-gradient(100deg, var(--olo-bg-softer) 40%, #fff 50%, var(--olo-bg-softer) 60%);
                background-size: 200% 100%;
                animation: oloMsShimmer 1.2s linear infinite;
                break-inside: avoid; margin-bottom: 14px;
            }

            /* ── empty state ── */
            .olo-ms-empty {
                display: flex; flex-direction: column; align-items: center; gap: 10px;
                padding: 64px 20px; text-align: center; color: var(--olo-text-muted);
            }
            .olo-ms-empty .ico {
                width: 56px; height: 56px; border-radius: 16px;
                background: var(--olo-primary-50); color: var(--olo-primary);
                display: grid; place-items: center;
            }
            .olo-ms-empty h3 { margin: 4px 0 0; font-size: 16px; color: var(--olo-text); }
            .olo-ms-empty p { margin: 0; font-size: 13px; max-width: 420px; }
            .olo-ms-empty .olo-ms-quick { justify-content: center; }

            /* ── GSV panel ── */
            .olo-ms-gsv { display: flex; flex-direction: column; gap: 12px; }
            .olo-ms-gsv .note {
                display: flex; gap: 10px; align-items: flex-start;
                background: var(--olo-info-50); border: 1px solid var(--olo-info-soft);
                color: var(--olo-info-dark); border-radius: 10px; padding: 10px 14px;
                font-size: 13px; line-height: 1.45;
            }
            .olo-ms-gsv-result {
                display: flex; gap: 18px; align-items: flex-start;
                background: #fff; border: 1px solid var(--olo-border); border-radius: 12px;
                padding: 16px; box-shadow: var(--olo-shadow-sm); max-width: 920px;
            }
            .olo-ms-gsv-result img { width: 420px; max-width: 48%; border-radius: 10px; display: block; }
            .olo-ms-gsv-result .meta { flex: 1; font-size: 13px; color: var(--olo-text-muted); }
            .olo-ms-gsv-result .meta p { margin: 3px 0; }
            .olo-ms-gsv-result .meta strong { color: var(--olo-text-strong); }
            .olo-ms-gsv-result .meta .mono { font-family: var(--olo-font-mono); font-size: 12px; }
            .olo-ms-gsv-result .hist { display: flex; gap: 5px; flex-wrap: wrap; margin: 8px 0 14px; }
            .olo-ms-gsv-result .hist button {
                border: 1px solid var(--olo-border); background: var(--olo-bg-soft);
                border-radius: 99px; padding: 2px 10px; font: inherit; font-size: 11px; font-weight: 600;
                color: var(--olo-text-soft); cursor: pointer;
            }
            .olo-ms-gsv-result .hist button.cur { background: var(--olo-navy); color: #fff; border-color: var(--olo-navy); }

            /* ── pagination ── */
            .olo-ms-pages { display: flex; gap: 6px; justify-content: center; align-items: center; padding-top: 8px; }
            .olo-ms-pages button {
                min-width: 34px; height: 34px; padding: 0 10px;
                border: 1px solid var(--olo-border); background: #fff; border-radius: 9px;
                font: inherit; font-size: 13px; font-weight: 600; color: var(--olo-text-soft);
                cursor: pointer; transition: var(--olo-transition);
                display: inline-flex; align-items: center; justify-content: center;
            }
            .olo-ms-pages button:hover:not(:disabled) { border-color: var(--olo-border-strong); color: var(--olo-text-strong); }
            .olo-ms-pages button.cur { background: var(--olo-navy); border-color: var(--olo-navy); color: #fff; }
            .olo-ms-pages button:disabled { opacity: .4; cursor: not-allowed; }
            .olo-ms-pages .ell { color: var(--olo-text-faint); font-size: 13px; padding: 0 2px; }

            /* ── modal ── */
            .olo-ms-modal { position: fixed; inset: 0; z-index: 100000; display: flex; align-items: center; justify-content: center; }
            .olo-ms-modal .ovl { position: absolute; inset: 0; background: rgba(2,6,23,.55); backdrop-filter: blur(6px); }
            .olo-ms-modal .box {
                position: relative; background: #fff; border-radius: 16px;
                width: min(860px, 92vw); max-height: 88vh; overflow: auto;
                box-shadow: var(--olo-shadow-xl); padding: 20px;
                animation: oloMsModalIn .22s var(--olo-ease-out);
            }
            @keyframes oloMsModalIn { from { opacity: 0; transform: translateY(10px) scale(.985); } to { opacity: 1; transform: none; } }
            .olo-ms-modal .x {
                position: absolute; top: 14px; right: 14px; z-index: 2;
                width: 32px; height: 32px; border-radius: 99px; border: 0;
                background: rgba(2,6,23,.45); color: #fff; cursor: pointer;
                display: grid; place-items: center; backdrop-filter: blur(4px);
            }
            .olo-ms-modal .x:hover { background: rgba(2,6,23,.7); }
            .olo-ms-modal .big { width: 100%; border-radius: 12px; display: block; max-height: 60vh; object-fit: contain; background: var(--olo-bg-softer); }
            .olo-ms-modal .row { display: flex; gap: 18px; align-items: flex-start; margin-top: 14px; flex-wrap: wrap; }
            .olo-ms-modal .facts { flex: 1; min-width: 240px; display: grid; grid-template-columns: auto 1fr; gap: 4px 14px; font-size: 13px; margin: 0; }
            .olo-ms-modal .facts dt { color: var(--olo-text-light); font-weight: 600; }
            .olo-ms-modal .facts dd { margin: 0; color: var(--olo-text-strong); font-weight: 500; }
            .olo-ms-modal .acts { display: flex; gap: 8px; align-items: center; }
            .olo-ms-modal .ghost {
                display: inline-flex; align-items: center; gap: 7px;
                padding: 7px 13px; border-radius: 8px;
                border: 1px solid var(--olo-border); background: #fff;
                font: inherit; font-size: 12px; font-weight: 600; color: var(--olo-text-soft);
                cursor: pointer; text-decoration: none;
            }
            .olo-ms-modal .ghost:hover { background: var(--olo-bg-muted); }

            @media (max-width: 1100px) { .olo-ms-masonry { columns: 3 220px; } .olo-ms-wave { width: 160px; } }
            @media (max-width: 860px)  { .olo-ms-wave { display: none; } }
        </style>

        <script>
        (function(){
            <?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline JSON in <script> built by wp_json_encode() from esc_url_raw()'d URLs, the REST nonce, boolean flags and translated strings. ?>
            const REST = <?php echo wp_json_encode( $rest_url ); ?>;
            const REST_VTOUR = <?php echo wp_json_encode( $rest_url_vtour ); ?>;
            const NONCE = <?php echo wp_json_encode( $nonce ); ?>;
            const KEYS = <?php echo wp_json_encode( $keys ); ?>;
            const I18N = <?php echo wp_json_encode( [
                'provider'      => __( 'Provider', 'olobuild' ),
                'quickLbl'      => __( 'Prova:', 'olobuild' ),
                'keyMissing'    => __( 'API key mancante — configurala in Configurazione → Stock Media', 'olobuild' ),
                /* translators: %s: search term */
                'searching'     => __( 'Ricerca di «%s» in corso…', 'olobuild' ),
                /* translators: 1: results count, 2: search term, 3: current page, 4: total pages */
                'resultsFor'    => __( '%1$s risultati per «%2$s» · pagina %3$s di %4$s', 'olobuild' ),
                /* translators: %s: provider name */
                'via'           => __( 'via %s', 'olobuild' ),
                'error'         => __( 'Errore', 'olobuild' ),
                'import'        => __( 'Importa', 'olobuild' ),
                'importLong'    => __( 'Importa nella Media Library', 'olobuild' ),
                'importPano'    => __( 'Importa panorama JPG', 'olobuild' ),
                'importing'     => __( 'Importazione…', 'olobuild' ),
                'imported'      => __( 'Importato', 'olobuild' ),
                'preview'       => __( 'Anteprima', 'olobuild' ),
                'play'          => __( 'Riproduci', 'olobuild' ),
                'pause'         => __( 'Pausa', 'olobuild' ),
                'emptyTitle'    => __( 'Cerca nei provider stock', 'olobuild' ),
                'emptyText'     => __( 'Scrivi una parola chiave e premi Invio: i risultati arrivano direttamente nella Media Library, con licenza libera.', 'olobuild' ),
                'audioPh'       => __( 'Cerca suoni, musica, ambience…', 'olobuild' ),
                'photoPh'       => __( 'Cerca per parola chiave…', 'olobuild' ),
                'orientation'   => __( 'Orientamento', 'olobuild' ),
                'any'           => __( 'Qualsiasi', 'olobuild' ),
                'landscape'     => __( 'Orizzontale', 'olobuild' ),
                'portrait'      => __( 'Verticale', 'olobuild' ),
                'square'        => __( 'Quadrato', 'olobuild' ),
                'size'          => __( 'Dimensione', 'olobuild' ),
                'small'         => __( 'Piccola', 'olobuild' ),
                'medium'        => __( 'Media', 'olobuild' ),
                'large'         => __( 'Grande', 'olobuild' ),
                'minW'          => __( 'Min L', 'olobuild' ),
                'minH'          => __( 'Min A', 'olobuild' ),
                'duration'      => __( 'Durata', 'olobuild' ),
                'durVeryShort'  => __( 'Brevissimo · < 5s', 'olobuild' ),
                'durShort'      => __( 'Breve · < 15s', 'olobuild' ),
                'dur5_30'       => __( '5 — 30 secondi', 'olobuild' ),
                'dur10_60'      => __( '10s — 1 minuto', 'olobuild' ),
                'dur30_120'     => __( '30s — 2 minuti', 'olobuild' ),
                'dur60_300'     => __( '1 — 5 minuti', 'olobuild' ),
                'durLong'       => __( 'Lungo · > 5 min', 'olobuild' ),
                'or'            => __( 'oppure', 'olobuild' ),
                'from'          => __( 'Da', 'olobuild' ),
                'to'            => __( 'A', 'olobuild' ),
                'reset'         => __( 'Azzera filtri', 'olobuild' ),
                'name'          => __( 'Nome', 'olobuild' ),
                'author'        => __( 'Autore', 'olobuild' ),
                'providerLbl'   => __( 'Provider', 'olobuild' ),
                'dims'          => __( 'Dimensioni', 'olobuild' ),
                'license'       => __( 'Licenza', 'olobuild' ),
                'licenseCc0'    => __( 'CC0 · dominio pubblico', 'olobuild' ),
                'licenseFree'   => __( 'Licenza libera, attribuzione consigliata', 'olobuild' ),
                'openOriginal'  => __( 'Apri originale', 'olobuild' ),
                'desc'          => __( 'Descrizione', 'olobuild' ),
                'place'         => __( 'Luogo', 'olobuild' ),
                'coords'        => __( 'Coordinate', 'olobuild' ),
                'timeline'      => __( 'Timeline:', 'olobuild' ),
                'gsvSearching'  => __( 'Ricerca panorama Street View…', 'olobuild' ),
                'gsvSearch'     => __( 'Cerca panorama', 'olobuild' ),
                'downloading'   => __( 'Download tile in corso…', 'olobuild' ),
                'unknown'       => __( 'Sconosciuto', 'olobuild' ),
                'quick'         => [
                    __( 'hotel resort', 'olobuild' ),
                    __( 'spiaggia tramonto', 'olobuild' ),
                    __( 'colazione', 'olobuild' ),
                    __( 'spa benessere', 'olobuild' ),
                    __( 'piscina', 'olobuild' ),
                    __( 'montagna', 'olobuild' ),
                ],
            ] ); ?>;
            <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>

            const PROVIDERS = {
                photo: [
                    { id: 'unsplash',  label: 'Unsplash',  key: KEYS.unsplash },
                    { id: 'pexels',    label: 'Pexels',    key: KEYS.pexels },
                    { id: 'pixabay',   label: 'Pixabay',   key: KEYS.pixabay },
                    { id: 'openverse', label: 'Openverse', key: KEYS.openverse },
                ],
                video: [
                    { id: 'pexels',  label: 'Pexels',  key: KEYS.pexels },
                    { id: 'pixabay', label: 'Pixabay', key: KEYS.pixabay },
                ],
                photo360: [
                    { id: 'polyhaven', label: 'Poly Haven · HDRI', key: KEYS.polyhaven },
                    { id: 'googlesv',  label: 'Google Street View', key: KEYS.googlesv },
                ],
                video360: [
                    { id: 'pexels',  label: 'Pexels',  key: KEYS.pexels },
                    { id: 'pixabay', label: 'Pixabay', key: KEYS.pixabay },
                ],
                audio: [
                    { id: 'freesound', label: 'Freesound', key: KEYS.freesound },
                ],
            };
            const PHOTO_FILTERS = {
                unsplash:  ['orientation'],
                pexels:    ['orientation', 'size'],
                pixabay:   ['orientation', 'min_width', 'min_height'],
                openverse: ['orientation', 'size'],
            };

            const ICONS = {
                search:   '<circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/>',
                image:    '<rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="9" cy="9" r="1.5"/><path d="M21 15l-5-5L5 21"/>',
                film:     '<rect x="3" y="4" width="18" height="16" rx="2"/><path d="M7 4v16M17 4v16M3 9h4M3 15h4M17 9h4M17 15h4"/>',
                globe:    '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a14 14 0 010 18M12 3a14 14 0 000 18"/>',
                rotate:   '<path d="M3 12a9 9 0 109-9"/><path d="M3 4v5h5"/><circle cx="12" cy="12" r="2.5"/>',
                music:    '<path d="M9 18V6l10-2v12"/><circle cx="6.5" cy="18" r="2.5"/><circle cx="16.5" cy="16" r="2.5"/>',
                play:     '<path d="M8 5l11 7-11 7z" fill="currentColor" stroke="none"/>',
                pause:    '<path d="M8 5v14M16 5v14" stroke-width="2.6"/>',
                download: '<path d="M12 4v11M7 10l5 5 5-5M5 20h14"/>',
                eye:      '<path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6-10-6-10-6z"/><circle cx="12" cy="12" r="2.6"/>',
                check:    '<path d="M4 12.5l5 5L20 6.5"/>',
                external: '<path d="M14 4h6v6M20 4l-8 8M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4"/>',
                lock:     '<rect x="5" y="11" width="14" height="9" rx="2"/><path d="M8 11V8a4 4 0 018 0v3"/>',
                sliders:  '<path d="M4 7h10M18 7h2M4 17h2M10 17h10M14 4v6M8 14v6"/>',
            };
            function icon(name, size) {
                size = size || 15;
                return '<svg width="' + size + '" height="' + size + '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">' + ICONS[name] + '</svg>';
            }

            let currentTab = 'photo';
            let currentProvider = 'unsplash';
            let currentPage = 1;
            let currentQuery = '';
            let totalPages = 0;
            let lastResults = [];
            let currentAudio = null;
            let currentAudioId = null;
            let audioRaf = null;
            let gsvData = null;
            const importedIds = new Set();

            const $ = s => document.querySelector(s);
            const $$ = s => document.querySelectorAll(s);

            function bindEvents() {
                $$('.olo-ms-tab').forEach(t => t.addEventListener('click', () => switchTab(t.dataset.tab)));
                $('#olo-ms-search-btn').addEventListener('click', () => doSearch());
                $('#olo-ms-query').addEventListener('keydown', e => { if (e.key === 'Enter') doSearch(); });
                $('#olo-ms-query').addEventListener('input', updateClear);
                $('#olo-ms-clear').addEventListener('click', () => {
                    $('#olo-ms-query').value = '';
                    updateClear();
                    renderEmpty();
                    $('#olo-ms-status').innerHTML = '';
                    $('#olo-ms-pagination').innerHTML = '';
                });
                $('.olo-ms-modal .ovl').addEventListener('click', closeModal);
                $('.olo-ms-modal .x').addEventListener('click', closeModal);
                document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });
                $('#olo-ms-gsv-btn').addEventListener('click', () => doGsvResolve());
                $('#olo-ms-gsv-input').addEventListener('keydown', e => { if (e.key === 'Enter') doGsvResolve(); });
            }

            function updateClear() {
                $('#olo-ms-clear').style.display = $('#olo-ms-query').value ? '' : 'none';
            }

            function switchTab(tab) {
                currentTab = tab;
                stopAudio();
                $$('.olo-ms-tab').forEach(t => {
                    const on = t.dataset.tab === tab;
                    t.classList.toggle('active', on);
                    t.setAttribute('aria-selected', on ? 'true' : 'false');
                });
                $('#olo-ms-query').placeholder = tab === 'audio' ? I18N.audioPh : I18N.photoPh;
                renderProviders();
                $('#olo-ms-status').innerHTML = '';
                $('#olo-ms-pagination').innerHTML = '';
                if ($('#olo-ms-query').value.trim() && !isGsv()) doSearch();
                else renderEmpty();
            }

            function isGsv() {
                return currentTab === 'photo360' && currentProvider === 'googlesv';
            }

            function updateVisibility() {
                const gsv = isGsv();
                $('#olo-ms-gsv-panel').style.display = gsv ? '' : 'none';
                $('#olo-ms-search-row').style.display = gsv ? 'none' : '';
                $('#olo-ms-quick').style.display = gsv ? 'none' : '';
                if (gsv) {
                    $('#olo-ms-results').innerHTML = '';
                    $('#olo-ms-status').innerHTML = '';
                    $('#olo-ms-pagination').innerHTML = '';
                }
                renderFilters();
            }

            /* ── providers ── */
            function renderProviders() {
                const wrap = $('#olo-ms-providers');
                const list = PROVIDERS[currentTab] || [];
                let html = '<span class="lbl">' + esc(I18N.provider) + '</span>';
                html += list.map(p => {
                    const dis = !p.key;
                    return '<button class="olo-ms-provider' + (dis ? ' disabled' : '') + '" data-provider="' + esc(p.id) + '">' +
                        '<span class="dot ' + (dis ? 'no' : 'ok') + '"></span>' + esc(p.label) +
                        (dis ? '<span class="lock">' + icon('lock', 12) + '</span>' : '') +
                        (dis ? '<span class="tip">' + esc(I18N.keyMissing) + '</span>' : '') +
                        '</button>';
                }).join('');
                wrap.innerHTML = html;

                const first = list.find(p => p.key) || list[0];
                if (first) {
                    currentProvider = first.id;
                    const btn = wrap.querySelector('[data-provider="' + first.id + '"]');
                    if (btn && !btn.classList.contains('disabled')) btn.classList.add('active');
                }

                wrap.querySelectorAll('.olo-ms-provider').forEach(btn => {
                    btn.addEventListener('click', () => {
                        if (btn.classList.contains('disabled')) return;
                        wrap.querySelectorAll('.olo-ms-provider').forEach(b => b.classList.remove('active'));
                        btn.classList.add('active');
                        currentProvider = btn.dataset.provider;
                        updateVisibility();
                        if ($('#olo-ms-query').value.trim() && !isGsv()) doSearch();
                    });
                });

                updateVisibility();
            }

            /* ── quick queries ── */
            function renderQuick(el, withLabel) {
                let html = withLabel ? '<span class="lbl">' + esc(I18N.quickLbl) + '</span>' : '';
                html += I18N.quick.map(q => '<button type="button" data-q="' + esc(q) + '">' + esc(q) + '</button>').join('');
                el.innerHTML = html;
                el.querySelectorAll('button[data-q]').forEach(b => {
                    b.addEventListener('click', () => {
                        $('#olo-ms-query').value = b.dataset.q;
                        updateClear();
                        doSearch();
                    });
                });
            }

            /* ── filters ── */
            const filterState = { orientation: '', size: '', min_width: '', min_height: '', durPreset: '', durMin: '', durMax: '' };

            function renderFilters() {
                const wrap = $('#olo-ms-filters');
                if (isGsv() || currentTab === 'photo360') { wrap.style.display = 'none'; return; }
                const isPhoto = currentTab === 'photo';
                let html = icon('sliders', 14);

                if (isPhoto) {
                    const allowed = PHOTO_FILTERS[currentProvider] || [];
                    if (allowed.includes('orientation')) {
                        html += '<span class="olo-ms-filter"><label>' + esc(I18N.orientation) + '</label>' +
                            '<select data-f="orientation">' +
                            '<option value="">' + esc(I18N.any) + '</option>' +
                            '<option value="landscape">' + esc(I18N.landscape) + '</option>' +
                            '<option value="portrait">' + esc(I18N.portrait) + '</option>' +
                            (currentProvider !== 'pixabay' ? '<option value="square">' + esc(I18N.square) + '</option>' : '') +
                            '</select></span>';
                    }
                    if (allowed.includes('size')) {
                        html += '<span class="olo-ms-filter"><label>' + esc(I18N.size) + '</label>' +
                            '<select data-f="size">' +
                            '<option value="">' + esc(I18N.any) + '</option>' +
                            '<option value="small">' + esc(I18N.small) + '</option>' +
                            '<option value="medium">' + esc(I18N.medium) + '</option>' +
                            '<option value="large">' + esc(I18N.large) + '</option>' +
                            '</select></span>';
                    }
                    if (allowed.includes('min_width')) {
                        html += '<span class="olo-ms-filter"><label>' + esc(I18N.minW) + '</label>' +
                            '<input type="number" data-f="min_width" placeholder="px" min="0" step="100"></span>';
                    }
                    if (allowed.includes('min_height')) {
                        html += '<span class="olo-ms-filter"><label>' + esc(I18N.minH) + '</label>' +
                            '<input type="number" data-f="min_height" placeholder="px" min="0" step="100"></span>';
                    }
                } else {
                    html += '<span class="olo-ms-filter"><label>' + esc(I18N.duration) + '</label>' +
                        '<select data-f="durPreset">' +
                        '<option value="">' + esc(I18N.any) + '</option>' +
                        '<option value="0,5">' + esc(I18N.durVeryShort) + '</option>' +
                        '<option value="0,15">' + esc(I18N.durShort) + '</option>' +
                        '<option value="5,30">' + esc(I18N.dur5_30) + '</option>' +
                        '<option value="10,60">' + esc(I18N.dur10_60) + '</option>' +
                        '<option value="30,120">' + esc(I18N.dur30_120) + '</option>' +
                        '<option value="60,300">' + esc(I18N.dur60_300) + '</option>' +
                        '<option value="300,">' + esc(I18N.durLong) + '</option>' +
                        '</select></span>' +
                        '<span class="sep">' + esc(I18N.or) + '</span>' +
                        '<span class="olo-ms-filter"><label>' + esc(I18N.from) + '</label>' +
                        '<input type="number" data-f="durMin" placeholder="sec" min="0"></span>' +
                        '<span class="olo-ms-filter"><label>' + esc(I18N.to) + '</label>' +
                        '<input type="number" data-f="durMax" placeholder="sec" min="0"></span>';
                }

                const active = isPhoto
                    ? (filterState.orientation || filterState.size || filterState.min_width || filterState.min_height)
                    : (filterState.durPreset || filterState.durMin || filterState.durMax);
                if (active) html += '<button class="reset" type="button">' + esc(I18N.reset) + '</button>';

                wrap.innerHTML = html;
                wrap.style.display = '';

                // ripristina valori + bind
                wrap.querySelectorAll('[data-f]').forEach(el => {
                    el.value = filterState[el.dataset.f] || '';
                    el.addEventListener('change', () => {
                        filterState[el.dataset.f] = el.value;
                        if (el.dataset.f === 'durPreset' && el.value) { filterState.durMin = ''; filterState.durMax = ''; }
                        if ((el.dataset.f === 'durMin' || el.dataset.f === 'durMax') && el.value) filterState.durPreset = '';
                        renderFilters();
                        if (currentQuery) doSearch();
                    });
                });
                const rst = wrap.querySelector('.reset');
                if (rst) rst.addEventListener('click', () => {
                    Object.keys(filterState).forEach(k => { filterState[k] = ''; });
                    renderFilters();
                    if (currentQuery) doSearch();
                });
            }

            /* ── search ── */
            function doSearch(page) {
                const q = $('#olo-ms-query').value.trim();
                if (!q) return;
                currentQuery = q;
                currentPage = page || 1;
                stopAudio();

                const status = $('#olo-ms-status');
                status.innerHTML = '<span>' + esc(I18N.searching.replace('%s', q)) + '</span>';
                $('#olo-ms-pagination').innerHTML = '';
                renderSkeleton();

                let url = '';
                const pp = (currentTab === 'video' || currentTab === 'video360') ? 15 : 30;

                if (currentTab === 'photo') {
                    url = REST + '/' + currentProvider + '/search?query=' + encodeURIComponent(q) +
                          '&page=' + currentPage + '&per_page=' + pp;
                    if (filterState.orientation) url += '&orientation=' + filterState.orientation;
                    if (filterState.size) url += '&size=' + filterState.size;
                    if (filterState.min_width) url += '&min_width=' + filterState.min_width;
                    if (filterState.min_height) url += '&min_height=' + filterState.min_height;
                } else if (currentTab === 'video') {
                    url = REST + '/' + currentProvider + '/videos?query=' + encodeURIComponent(q) +
                          '&page=' + currentPage + '&per_page=' + pp;
                    const vd = getVideoDuration();
                    if (vd.min) url += '&min_duration=' + vd.min;
                    if (vd.max) url += '&max_duration=' + vd.max;
                } else if (currentTab === 'photo360' && currentProvider === 'polyhaven') {
                    url = REST_VTOUR + '/polyhaven/search?query=' + encodeURIComponent(q) +
                          '&page=' + currentPage + '&per_page=' + pp;
                } else if (currentTab === 'video360') {
                    url = REST + '/' + currentProvider + '/videos?query=' + encodeURIComponent('360 ' + q) +
                          '&page=' + currentPage + '&per_page=' + pp;
                    const vd360 = getVideoDuration();
                    if (vd360.min) url += '&min_duration=' + vd360.min;
                    if (vd360.max) url += '&max_duration=' + vd360.max;
                } else if (currentTab === 'audio') {
                    url = REST + '/freesound/search?query=' + encodeURIComponent(q) +
                          '&page=' + currentPage + '&per_page=' + pp;
                    const durFilter = buildDurationFilter();
                    if (durFilter) url += '&filter=' + encodeURIComponent(durFilter);
                }

                if (!url) return;

                fetch(url, { headers: { 'X-WP-Nonce': NONCE } })
                    .then(r => r.json())
                    .then(data => {
                        if (data.code) {
                            status.innerHTML = '<span class="err">' + esc(data.message || I18N.error) + '</span>';
                            $('#olo-ms-results').innerHTML = '';
                            return;
                        }
                        const results = data.results || [];
                        lastResults = results;
                        totalPages = data.total_pages || 0;
                        const total = data.total || 0;
                        const provLabel = (PROVIDERS[currentTab] || []).filter(p => p.id === currentProvider).map(p => p.label)[0] || currentProvider;
                        status.innerHTML =
                            '<span>' + I18N.resultsFor
                                .replace('%1$s', '<b>' + fmtNum(total) + '</b>')
                                .replace('%2$s', esc(currentQuery))
                                .replace('%3$s', currentPage)
                                .replace('%4$s', Math.max(1, totalPages)) + '</span>' +
                            '<span class="via"><span class="dot"></span> ' + esc(I18N.via.replace('%s', provLabel)) + '</span>';
                        renderResults(results);
                        renderPagination();
                    })
                    .catch(err => {
                        status.innerHTML = '<span class="err">' + esc(I18N.error) + ': ' + esc(err.message) + '</span>';
                        $('#olo-ms-results').innerHTML = '';
                    });
            }

            function getVideoDuration() {
                if (filterState.durPreset) {
                    const parts = filterState.durPreset.split(',');
                    return { min: parts[0] || '', max: parts[1] || '' };
                }
                return { min: filterState.durMin || '', max: filterState.durMax || '' };
            }

            function buildDurationFilter() {
                if (filterState.durPreset) {
                    const parts = filterState.durPreset.split(',');
                    return 'duration:[' + (parts[0] || '*') + ' TO ' + (parts[1] || '*') + ']';
                }
                if (filterState.durMin || filterState.durMax) {
                    return 'duration:[' + (filterState.durMin || '*') + ' TO ' + (filterState.durMax || '*') + ']';
                }
                return '';
            }

            /* ── skeleton / empty ── */
            function renderSkeleton() {
                const wrap = $('#olo-ms-results');
                if (currentTab === 'audio') {
                    wrap.className = 'olo-ms-audio-list';
                    wrap.innerHTML = [0,1,2,3,4,5].map(() => '<div class="olo-ms-skel" style="height:62px;margin:0;"></div>').join('');
                } else if (currentTab === 'photo') {
                    wrap.className = 'olo-ms-masonry';
                    wrap.innerHTML = [180,260,210,320,190,240,300,200,250,170,280,220].map(h => '<div class="olo-ms-skel" style="height:' + h + 'px;"></div>').join('');
                } else {
                    wrap.className = 'olo-ms-grid';
                    wrap.innerHTML = [0,1,2,3,4,5,6,7].map(() => '<div class="olo-ms-skel" style="aspect-ratio:16/9;margin:0;"></div>').join('');
                }
            }

            function renderEmpty() {
                const icons = { photo: 'image', video: 'film', photo360: 'globe', video360: 'rotate', audio: 'music' };
                const wrap = $('#olo-ms-results');
                wrap.className = '';
                wrap.innerHTML =
                    '<div class="olo-ms-empty">' +
                    '<span class="ico">' + icon(icons[currentTab] || 'image', 26) + '</span>' +
                    '<h3>' + esc(I18N.emptyTitle) + '</h3>' +
                    '<p>' + esc(I18N.emptyText) + '</p>' +
                    '<div class="olo-ms-quick"></div>' +
                    '</div>';
                renderQuick(wrap.querySelector('.olo-ms-quick'), false);
            }

            /* ── results render ── */
            function renderResults(results) {
                const wrap = $('#olo-ms-results');
                if (!results.length) { renderEmpty(); return; }
                if (currentTab === 'audio') {
                    wrap.className = 'olo-ms-audio-list';
                    wrap.innerHTML = results.map(renderAudioRow).join('');
                    bindAudioEvents(results);
                } else if (currentTab === 'photo360' && currentProvider === 'polyhaven') {
                    wrap.className = 'olo-ms-grid';
                    wrap.innerHTML = results.map(renderHdriCard).join('');
                    bindCardEvents(results);
                } else if (currentTab === 'video' || currentTab === 'video360') {
                    wrap.className = 'olo-ms-grid';
                    wrap.innerHTML = results.map((r, i) => renderVideoCard(r, currentTab === 'video360', i)).join('');
                    bindCardEvents(results);
                } else {
                    wrap.className = 'olo-ms-masonry';
                    wrap.innerHTML = results.map(renderPhotoCard).join('');
                    bindCardEvents(results);
                }
            }

            function importBtnHtml(id, small, label) {
                const done = importedIds.has(String(id));
                const text = done ? I18N.imported : (label || (small ? I18N.import : I18N.importLong));
                return '<button class="olo-ms-import' + (done ? ' done' : '') + '" data-import-id="' + esc(String(id)) + '">' +
                       icon(done ? 'check' : 'download', 13) + esc(text) + '</button>';
            }

            function renderPhotoCard(r, i) {
                const ratio = (r.width && r.height) ? ' style="aspect-ratio:' + (+r.width) + '/' + (+r.height) + '"' : '';
                return '<figure class="olo-ms-card" data-i="' + i + '"' + ratio + '>' +
                    '<img src="' + esc(r.thumb) + '" alt="' + esc(r.alt || '') + '" loading="lazy" />' +
                    '<div class="veil">' +
                    '<div class="top"><button class="olo-ms-iconbtn" data-preview="' + i + '" title="' + esc(I18N.preview) + '">' + icon('eye') + '</button></div>' +
                    '<div class="bottom">' +
                    '<div class="who"><div class="nm">' + esc(r.photographer || '') + '</div>' +
                    (r.width ? '<div class="dim">' + (+r.width) + ' × ' + (+r.height) + '</div>' : '') + '</div>' +
                    importBtnHtml(r.id, true) +
                    '</div></div></figure>';
            }

            function renderVideoCard(r, badge360, i) {
                const thumb = r.thumb || '';
                const media = r.is_video_thumb
                    ? '<video src="' + esc(thumb) + '" muted preload="metadata" style="pointer-events:none;"></video>'
                    : '<img src="' + esc(thumb) + '" alt="" loading="lazy" />';
                return '<figure class="olo-ms-vcard" data-i="' + i + '">' + media +
                    (badge360 ? '<span class="olo-ms-badge">360°</span>' : '') +
                    '<div class="veil"><button class="playbtn" data-preview="' + i + '" title="' + esc(I18N.preview) + '">' + icon('play', 18) + '</button></div>' +
                    '<span class="who">' + esc(r.photographer || '') + '</span>' +
                    (r.duration ? '<span class="dur">' + fmtDur(r.duration) + '</span>' : '') +
                    '</figure>';
            }

            function renderHdriCard(r, i) {
                const tags = (r.tags || []).slice(0, 3).map(t => '<span class="olo-ms-tag">' + esc(t) + '</span>').join('');
                return '<figure class="olo-ms-vcard" data-i="' + i + '">' +
                    '<img src="' + esc(r.thumb) + '" alt="' + esc(r.name || '') + '" loading="lazy" />' +
                    '<span class="olo-ms-badge">' + icon('globe', 11) + ' HDRI 360</span>' +
                    '<div class="veil"><button class="playbtn" data-preview="' + i + '" title="' + esc(I18N.preview) + '">' + icon('eye', 17) + '</button></div>' +
                    (tags ? '<div class="olo-ms-tagrow">' + tags + '</div>' : '') +
                    '<span class="dur">' + esc(r.name || '') + '</span>' +
                    '</figure>';
            }

            // wave bars deterministiche dall'id (envelope sinusoidale)
            function waveBars(id) {
                let h = 2166136261;
                const s = String(id);
                for (let i = 0; i < s.length; i++) { h ^= s.charCodeAt(i); h = Math.imul(h, 16777619); }
                let seed = (h >>> 0) || 1;
                const rnd = () => { seed ^= seed << 13; seed ^= seed >>> 17; seed ^= seed << 5; seed >>>= 0; return seed / 4294967296; };
                const out = [];
                for (let b = 0; b < 48; b++) {
                    const env = Math.sin((b / 47) * Math.PI);
                    out.push(Math.max(0.08, Math.min(1, env * (0.35 + rnd() * 0.75))));
                }
                return out;
            }

            function renderAudioRow(r, i) {
                const bars = waveBars(r.id).map(b => '<i style="height:' + Math.round(b * 100) + '%"></i>').join('');
                const lic = r.license ? '<span class="lic">' + esc(r.license) + '</span>' : '';
                return '<div class="olo-ms-arow" data-i="' + i + '" data-id="' + esc(String(r.id)) + '">' +
                    '<button class="olo-ms-aplay" data-mp3="' + esc(r.preview_mp3 || r.preview_ogg || '') + '" title="' + esc(I18N.play) + '">' + icon('play') + '</button>' +
                    '<div class="olo-ms-ainfo">' +
                    '<div class="nm">' + esc(r.name || '') + '</div>' +
                    '<div class="meta">' + lic +
                    (r.type ? '<span>' + esc(String(r.type).toUpperCase()) + '</span>' : '') +
                    (r.username ? '<span>' + esc('di ' + r.username) + '</span>' : '') +
                    (r.filesize ? '<span>' + fmtSize(r.filesize) + '</span>' : '') +
                    '</div></div>' +
                    '<div class="olo-ms-wave" aria-hidden="true">' + bars + '</div>' +
                    (r.duration ? '<span class="dur">' + fmtDur(r.duration) + '</span>' : '') +
                    importBtnHtml(r.id, true) +
                    '</div>';
            }

            /* ── card events ── */
            function bindCardEvents(results) {
                $$('#olo-ms-results [data-i]').forEach(card => {
                    const item = results[+card.dataset.i];
                    card.addEventListener('click', () => showPreview(item));
                });
                $$('#olo-ms-results [data-preview]').forEach(btn => {
                    btn.addEventListener('click', e => {
                        e.stopPropagation();
                        showPreview(results[+btn.dataset.preview]);
                    });
                });
                $$('#olo-ms-results .olo-ms-import').forEach(btn => {
                    btn.addEventListener('click', e => {
                        e.stopPropagation();
                        const card = btn.closest('[data-i]');
                        importItem(results[+card.dataset.i], btn);
                    });
                });
            }

            function bindAudioEvents(results) {
                $$('.olo-ms-aplay').forEach(btn => {
                    btn.addEventListener('click', () => toggleAudio(btn));
                });
                $$('.olo-ms-arow .olo-ms-import').forEach(btn => {
                    btn.addEventListener('click', e => {
                        e.stopPropagation();
                        const row = btn.closest('.olo-ms-arow');
                        importAudio(results[+row.dataset.i], btn);
                    });
                });
            }

            /* ── audio playback con progress sulle barre ── */
            function stopAudio() {
                if (currentAudio) { currentAudio.pause(); currentAudio = null; }
                if (audioRaf) { cancelAnimationFrame(audioRaf); audioRaf = null; }
                currentAudioId = null;
                $$('.olo-ms-arow').forEach(r => {
                    r.classList.remove('playing');
                    r.querySelectorAll('.olo-ms-wave i').forEach(b => b.classList.remove('lit'));
                    const p = r.querySelector('.olo-ms-aplay');
                    if (p) { p.innerHTML = icon('play'); p.title = I18N.play; }
                });
            }

            function toggleAudio(btn) {
                const src = btn.dataset.mp3;
                if (!src) return;
                const row = btn.closest('.olo-ms-arow');
                const id = row.dataset.id;
                if (currentAudioId === id) { stopAudio(); return; }
                stopAudio();
                currentAudio = new Audio(src);
                currentAudioId = id;
                currentAudio.play();
                row.classList.add('playing');
                btn.innerHTML = icon('pause');
                btn.title = I18N.pause;
                const bars = row.querySelectorAll('.olo-ms-wave i');
                const tick = () => {
                    if (!currentAudio) return;
                    const p = currentAudio.duration ? currentAudio.currentTime / currentAudio.duration : 0;
                    const lit = Math.round(p * bars.length);
                    bars.forEach((b, j) => b.classList.toggle('lit', j < lit));
                    audioRaf = requestAnimationFrame(tick);
                };
                audioRaf = requestAnimationFrame(tick);
                currentAudio.addEventListener('ended', stopAudio);
            }

            /* ── import (stati busy/done) ── */
            function setBtnBusy(btn) {
                btn.classList.add('busy');
                btn.innerHTML = '<span class="spin"></span>' + esc(I18N.importing);
            }
            function setBtnDone(btn, id) {
                importedIds.add(String(id));
                btn.classList.remove('busy');
                btn.classList.add('done');
                btn.innerHTML = icon('check', 13) + esc(I18N.imported);
            }
            function setBtnError(btn, msg, label) {
                btn.classList.remove('busy');
                btn.innerHTML = esc(msg || I18N.error);
                setTimeout(() => { btn.innerHTML = icon('download', 13) + esc(label || I18N.import); }, 3000);
            }

            function importItem(item, btn) {
                if (btn.classList.contains('busy') || btn.classList.contains('done')) return;
                if (currentTab === 'photo360' && currentProvider === 'polyhaven') { import360(item, btn); return; }
                setBtnBusy(btn);

                let url, bodyData;
                if (currentTab === 'video' || currentTab === 'video360') {
                    url = REST + '/' + currentProvider + '/video-download';
                    bodyData = {
                        video_url: item.regular || item.thumb,
                        photographer: item.photographer || '',
                        video_id: String(item.id),
                    };
                } else {
                    url = REST + '/' + currentProvider + '/download';
                    bodyData = {
                        regular_url: item.regular,
                        alt: item.alt || '',
                        photographer: item.photographer || '',
                        photo_id: String(item.id),
                    };
                    if (item.download_location) bodyData.download_location = item.download_location;
                }

                fetch(url, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': NONCE },
                    body: JSON.stringify(bodyData),
                })
                .then(r => r.json())
                .then(data => {
                    if (data.id) setBtnDone(btn, item.id);
                    else setBtnError(btn, data.message);
                })
                .catch(() => setBtnError(btn));
            }

            function import360(item, btn) {
                if (btn.classList.contains('busy') || btn.classList.contains('done')) return;
                setBtnBusy(btn);
                fetch(REST_VTOUR + '/polyhaven/download', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': NONCE },
                    body: JSON.stringify({
                        slug: item.slug,
                        name: item.name || item.slug,
                        author: item.author || 'Poly Haven',
                        resolution: '4k',
                    }),
                })
                .then(r => r.json())
                .then(data => {
                    if (data.id) setBtnDone(btn, item.id || item.slug);
                    else setBtnError(btn, data.message, I18N.importPano);
                })
                .catch(() => setBtnError(btn, null, I18N.importPano));
            }

            function importAudio(item, btn) {
                if (btn.classList.contains('busy') || btn.classList.contains('done')) return;
                setBtnBusy(btn);
                fetch(REST + '/freesound/download', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': NONCE },
                    body: JSON.stringify({
                        sound_id: parseInt(item.id, 10),
                        name: item.name || '',
                        username: item.username || '',
                        preview_url: item.preview_mp3 || item.preview_ogg || '',
                    }),
                })
                .then(r => r.json())
                .then(data => {
                    if (data.id) setBtnDone(btn, item.id);
                    else setBtnError(btn, data.message);
                })
                .catch(() => setBtnError(btn));
            }

            /* ── preview modal ── */
            function showPreview(item) {
                if (!item) return;
                const modal = $('#olo-ms-modal');
                const body = $('#olo-ms-modal-body');
                const isVideo = currentTab === 'video' || currentTab === 'video360';
                const isHdri = currentTab === 'photo360' && currentProvider === 'polyhaven';

                let mediaHtml;
                if (isVideo) {
                    mediaHtml = '<video src="' + esc(item.regular || item.thumb || '') + '" controls class="big"></video>';
                } else {
                    mediaHtml = '<img class="big" src="' + esc(item.large || item.preview || item.regular || item.thumb || '') + '" alt="" />';
                }

                const provLabel = (PROVIDERS[currentTab] || []).filter(p => p.id === currentProvider).map(p => p.label)[0] || currentProvider;
                let facts = '';
                if (item.name) {
                    facts += '<dt>' + esc(I18N.name) + '</dt><dd>' + esc(item.name) + '</dd>';
                    if (item.author) facts += '<dt>' + esc(I18N.author) + '</dt><dd>' + esc(item.author) + '</dd>';
                } else {
                    facts += '<dt>' + esc(I18N.author) + '</dt><dd>' + esc(item.photographer || item.username || I18N.unknown) + '</dd>';
                }
                facts += '<dt>' + esc(I18N.providerLbl) + '</dt><dd>' + esc(provLabel) + '</dd>';
                if (item.width) facts += '<dt>' + esc(I18N.dims) + '</dt><dd>' + (+item.width) + ' × ' + (+item.height) + ' px</dd>';
                if (item.duration) facts += '<dt>' + esc(I18N.duration) + '</dt><dd>' + fmtDur(item.duration) + '</dd>';
                if (item.alt) facts += '<dt>' + esc(I18N.desc) + '</dt><dd>' + esc(item.alt) + '</dd>';
                facts += '<dt>' + esc(I18N.license) + '</dt><dd>' + esc(item.license || (isHdri ? I18N.licenseCc0 : I18N.licenseFree)) + '</dd>';

                const origin = item.link || item.photographer_url || '';
                const openOriginal = origin
                    ? '<a class="ghost" href="' + esc(origin) + '" target="_blank" rel="noopener noreferrer">' + icon('external', 13) + esc(I18N.openOriginal) + '</a>'
                    : '';

                body.innerHTML = mediaHtml +
                    '<div class="row">' +
                    '<dl class="facts">' + facts + '</dl>' +
                    '<div class="acts">' + openOriginal + importBtnHtml(item.id || item.slug, false, isHdri ? I18N.importPano : null) + '</div>' +
                    '</div>';

                body.querySelector('.olo-ms-import').addEventListener('click', function () {
                    if (currentTab === 'audio') importAudio(item, this);
                    else importItem(item, this);
                });

                modal.style.display = 'flex';
            }

            function closeModal() {
                $('#olo-ms-modal').style.display = 'none';
                const body = $('#olo-ms-modal-body');
                const vid = body.querySelector('video');
                if (vid) vid.pause();
            }

            /* ── Google Street View ── */
            function doGsvResolve(panoOverride) {
                const input = panoOverride || $('#olo-ms-gsv-input').value.trim();
                if (!input) return;
                const btn = $('#olo-ms-gsv-btn');
                btn.textContent = I18N.gsvSearching;
                btn.disabled = true;

                fetch(REST_VTOUR + '/googlesv/resolve', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': NONCE },
                    body: JSON.stringify({ input: input }),
                })
                .then(r => r.json())
                .then(data => {
                    btn.textContent = I18N.gsvSearch;
                    btn.disabled = false;
                    if (data.code) {
                        $('#olo-ms-gsv-result').innerHTML = '<div class="olo-ms-status"><span class="err">' + esc(data.message || I18N.error) + '</span></div>';
                        return;
                    }
                    gsvData = data;
                    renderGsvResult(data);
                })
                .catch(err => {
                    btn.textContent = I18N.gsvSearch;
                    btn.disabled = false;
                    $('#olo-ms-gsv-result').innerHTML = '<div class="olo-ms-status"><span class="err">' + esc(I18N.error) + ': ' + esc(err.message) + '</span></div>';
                });
            }

            function renderGsvResult(data) {
                const wrap = $('#olo-ms-gsv-result');
                let historyHtml = '';
                if (data.history && data.history.length > 1) {
                    historyHtml = '<p>' + esc(I18N.timeline) + '</p><div class="hist">' +
                        data.history.map(h =>
                            '<button type="button" data-pano="' + esc(h.pano_id) + '"' + (h.pano_id === data.pano_id ? ' class="cur"' : '') + '>' + esc(h.date) + '</button>'
                        ).join('') + '</div>';
                }
                wrap.innerHTML =
                    '<div class="olo-ms-gsv-result">' +
                    '<img src="' + esc(data.preview_url) + '" alt="Street View" />' +
                    '<div class="meta">' +
                    (data.description ? '<p><strong>' + esc(data.description) + '</strong></p>' : '') +
                    '<p>Pano ID: <strong class="mono">' + esc(data.pano_id) + '</strong></p>' +
                    '<p>' + esc(I18N.coords) + ': <strong>' + esc(String(data.lat)) + ', ' + esc(String(data.lng)) + '</strong></p>' +
                    (data.copyright ? '<p>© <strong>' + esc(data.copyright) + '</strong></p>' : '') +
                    historyHtml +
                    '<button class="olo-ms-import" id="olo-ms-gsv-download">' + icon('download', 13) + esc(I18N.importPano) + '</button>' +
                    '</div></div>';

                wrap.querySelectorAll('[data-pano]').forEach(b => {
                    b.addEventListener('click', () => {
                        $('#olo-ms-gsv-input').value = b.dataset.pano;
                        doGsvResolve(b.dataset.pano);
                    });
                });
                $('#olo-ms-gsv-download').addEventListener('click', function () { doGsvDownload(this); });
            }

            function doGsvDownload(btn) {
                if (!gsvData || btn.classList.contains('busy') || btn.classList.contains('done')) return;
                setBtnBusy(btn);
                const zoom = parseInt($('#olo-ms-gsv-zoom').value, 10) || 3;
                fetch(REST_VTOUR + '/googlesv/download', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': NONCE },
                    body: JSON.stringify({
                        pano_id: gsvData.pano_id,
                        zoom: zoom,
                        description: gsvData.description || '',
                        copyright: gsvData.copyright || '',
                    }),
                })
                .then(r => r.json())
                .then(data => {
                    if (data.id) setBtnDone(btn, 'gsv-' + gsvData.pano_id);
                    else setBtnError(btn, data.message, I18N.importPano);
                })
                .catch(() => setBtnError(btn, null, I18N.importPano));
            }

            /* ── pagination con ellissi ── */
            function renderPagination() {
                const wrap = $('#olo-ms-pagination');
                if (totalPages <= 1) { wrap.innerHTML = ''; return; }
                const total = Math.max(1, totalPages);
                const around = [currentPage - 1, currentPage, currentPage + 1].filter(p => p > 1 && p < total);
                const set = [1].concat(around, [total]);
                let html = '<button ' + (currentPage <= 1 ? 'disabled' : '') + ' data-page="' + (currentPage - 1) + '">&lsaquo;</button>';
                let prev = 0;
                set.forEach(p => {
                    if (p - prev > 1) html += '<span class="ell">…</span>';
                    html += '<button class="' + (p === currentPage ? 'cur' : '') + '" data-page="' + p + '">' + p + '</button>';
                    prev = p;
                });
                html += '<button ' + (currentPage >= total ? 'disabled' : '') + ' data-page="' + (currentPage + 1) + '">&rsaquo;</button>';
                wrap.innerHTML = html;
                wrap.querySelectorAll('button[data-page]').forEach(btn => {
                    btn.addEventListener('click', () => {
                        const p = parseInt(btn.dataset.page, 10);
                        if (p >= 1 && p <= total && p !== currentPage) {
                            doSearch(p);
                            window.scrollTo({ top: 0, behavior: 'smooth' });
                        }
                    });
                });
            }

            /* ── utils ── */
            function fmtDur(sec) {
                sec = Math.round(sec);
                const m = Math.floor(sec / 60);
                const s = sec % 60;
                return m + ':' + String(s).padStart(2, '0');
            }
            function fmtSize(bytes) {
                if (bytes > 1048576) return (bytes / 1048576).toFixed(1) + ' MB';
                if (bytes > 1024) return (bytes / 1024).toFixed(0) + ' KB';
                return bytes + ' B';
            }
            function fmtNum(n) {
                return String(n).replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }
            function esc(str) {
                const d = document.createElement('div');
                d.textContent = str == null ? '' : String(str);
                return d.innerHTML;
            }

            // Init — DOPO tutte le dichiarazioni (const filterState & co. non sono hoisted)
            renderProviders();
            renderQuick($('#olo-ms-quick'), true);
            renderEmpty();
            bindEvents();
        })();
        </script>
        <?php
    }
}
