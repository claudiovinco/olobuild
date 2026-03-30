<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Ricerca Media — admin page for searching free stock photos, videos, audio, and 360 content.
 */
class Olo_Media_Search {

    public static function render_page() {
        $nonce = wp_create_nonce( 'wp_rest' );
        $rest_url = esc_url_raw( rest_url( 'olo/v1' ) );
        $rest_url_vtour = esc_url_raw( rest_url( 'olo-vtour/v1' ) );
        $vtour_active = is_plugin_active( 'olo-vtour/olo-vtour.php' );

        // Check configured API keys
        $keys = [
            'unsplash'  => ! empty( get_option( 'olo_unsplash_api_key', 'mAtcGSa97BuefUN55vaORLV6YvFH4SHjdcCFbq_gJ84' ) ),
            'pexels'    => ! empty( get_option( 'olo_pexels_api_key', '***REMOVED-API-KEY***' ) ),
            'pixabay'   => ! empty( get_option( 'olo_pixabay_api_key', '***REMOVED-API-KEY***' ) ),
            'openverse' => true,
            'freesound' => ! empty( get_option( 'olo_freesound_api_key', '' ) ),
            'polyhaven' => $vtour_active,
            'googlesv'  => $vtour_active,
        ];
        ?>
        <?php Olo_Builder::page_shell_open( 'Ricerca Media', 'olo-ms-wrap' ); ?>

            <!-- Tabs -->
            <div class="olo-admin-tabs">
                <button class="olo-admin-tab olo-ms-tab active" data-tab="photo">Foto</button>
                <button class="olo-admin-tab olo-ms-tab" data-tab="video">Video</button>
                <button class="olo-admin-tab olo-ms-tab" data-tab="photo360">Foto 360</button>
                <button class="olo-admin-tab olo-ms-tab" data-tab="video360">Video 360</button>
                <button class="olo-admin-tab olo-ms-tab" data-tab="audio">Audio</button>
            </div>

            <!-- Search bar -->
            <div class="olo-ms-search-bar">
                <div class="olo-ms-providers" id="olo-ms-providers"></div>
                <div class="olo-ms-input-wrap">
                    <input type="text" id="olo-ms-query" class="olo-field-input olo-ms-query" placeholder="Cerca foto, video, audio..." autocomplete="off" />
                    <button id="olo-ms-search-btn" class="olo-btn-save olo-btn-sm">Cerca</button>
                </div>
            </div>

            <!-- Google SV panel (hidden by default) -->
            <div id="olo-ms-gsv-panel" class="olo-ms-gsv-panel">
                <div class="olo-msg info">
                    Incolla un URL Google Maps, coordinate (lat,lng) o un pano_id per scaricare un panorama Street View.
                </div>
                <div class="olo-ms-input-wrap olo-ms-gsv-inputs">
                    <input type="text" id="olo-ms-gsv-input" class="olo-field-input olo-ms-gsv-field" placeholder="https://www.google.com/maps/@45.4642,9.1900,3a... oppure 45.4642,9.1900" autocomplete="off" />
                    <select id="olo-ms-gsv-zoom" class="olo-field-input olo-ms-gsv-select">
                        <option value="2">Zoom 2 (bassa)</option>
                        <option value="3" selected>Zoom 3 (media)</option>
                        <option value="4">Zoom 4 (alta)</option>
                    </select>
                    <button id="olo-ms-gsv-btn" class="olo-btn-save olo-btn-sm">Cerca panorama</button>
                </div>
            </div>

            <!-- Photo filters (orientation + size + min dims) -->
            <div id="olo-ms-photo-filters" class="olo-ms-filters">
                <div class="olo-ms-filters-row">
                    <span class="olo-ms-pf olo-ms-filter-group" data-pf="orientation">
                        <label class="olo-ms-filter-label">Orientamento:</label>
                        <select id="olo-ms-orientation" class="olo-field-input olo-ms-filter-select">
                            <option value="">Qualsiasi</option>
                            <option value="landscape">Orizzontale</option>
                            <option value="portrait">Verticale</option>
                            <option value="square">Quadrato</option>
                        </select>
                    </span>
                    <span class="olo-ms-pf olo-ms-filter-group" data-pf="size">
                        <label class="olo-ms-filter-label">Dimensione:</label>
                        <select id="olo-ms-size" class="olo-field-input olo-ms-filter-select">
                            <option value="">Qualsiasi</option>
                            <option value="small">Piccola</option>
                            <option value="medium">Media</option>
                            <option value="large">Grande</option>
                        </select>
                    </span>
                    <span class="olo-ms-pf olo-ms-filter-group" data-pf="min_width">
                        <label class="olo-ms-filter-label">Min larghezza:</label>
                        <input type="number" id="olo-ms-min-width" class="olo-field-input olo-ms-filter-num" placeholder="px" min="0" step="100" />
                    </span>
                    <span class="olo-ms-pf olo-ms-filter-group" data-pf="min_height">
                        <label class="olo-ms-filter-label">Min altezza:</label>
                        <input type="number" id="olo-ms-min-height" class="olo-field-input olo-ms-filter-num" placeholder="px" min="0" step="100" />
                    </span>
                </div>
            </div>

            <!-- Duration filters (audio + video) -->
            <div id="olo-ms-duration-filters" class="olo-ms-filters">
                <div class="olo-ms-filters-row">
                    <label class="olo-ms-filter-label">Durata:</label>
                    <select id="olo-ms-dur-preset" class="olo-field-input olo-ms-filter-select">
                        <option value="">Qualsiasi</option>
                        <option value="0,5">Brevissimo (&lt; 5s)</option>
                        <option value="0,15">Breve (&lt; 15s)</option>
                        <option value="5,30">5 — 30 secondi</option>
                        <option value="10,60">10s — 1 minuto</option>
                        <option value="30,120">30s — 2 minuti</option>
                        <option value="60,300">1 — 5 minuti</option>
                        <option value="300,">Lungo (&gt; 5 min)</option>
                    </select>
                    <span class="olo-ms-filter-sep">oppure</span>
                    <input type="number" id="olo-ms-dur-min" class="olo-field-input olo-ms-filter-num" placeholder="min sec" min="0" />
                    <span class="olo-ms-filter-dash">&mdash;</span>
                    <input type="number" id="olo-ms-dur-max" class="olo-field-input olo-ms-filter-num" placeholder="max sec" min="0" />
                    <span class="olo-ms-filter-sep">secondi</span>
                </div>
            </div>

            <!-- Status -->
            <div id="olo-ms-status" class="olo-ms-status"></div>

            <!-- Results -->
            <div id="olo-ms-results" class="olo-ms-results"></div>

            <!-- Pagination -->
            <div id="olo-ms-pagination" class="olo-ms-pagination"></div>

            <!-- Preview Modal -->
            <div id="olo-ms-modal" class="olo-ms-modal">
                <div class="olo-ms-modal-overlay"></div>
                <div class="olo-ms-modal-content">
                    <button class="olo-ms-modal-close">&times;</button>
                    <div id="olo-ms-modal-body"></div>
                </div>
            </div>
        <?php Olo_Builder::page_shell_close(); ?>

        <style>
            /* ── Media Search — page-specific overrides ── */
            /* Search bar */
            .olo-ms-search-bar { margin-bottom: 20px; }
            .olo-ms-providers { display: flex; gap: 8px; margin-bottom: 12px; flex-wrap: wrap; }
            .olo-ms-provider {
                padding: 6px 16px; border-radius: 20px; border: 1.5px solid #eaeaea;
                background: #fff; cursor: pointer; font-size: 13px; font-weight: 600;
                transition: all .15s; display: flex; align-items: center; gap: 6px;
                font-family: inherit;
            }
            .olo-ms-provider:hover { border-color: #1a1a1a; color: #1a1a1a; }
            .olo-ms-provider.active { background: #1a1a1a; color: #fff; border-color: #1a1a1a; }
            .olo-ms-provider.disabled { opacity: .4; cursor: not-allowed; }
            .olo-ms-provider .olo-ms-dot { width: 8px; height: 8px; border-radius: 50%; }
            .olo-ms-dot-ok { background: #059669; }
            .olo-ms-dot-no { background: #dc2626; }

            .olo-ms-input-wrap { display: flex; gap: 8px; align-items: center; }
            .olo-ms-query { flex: 1; max-width: 600px; width: auto !important; }

            /* Filters */
            .olo-ms-filters { display: none; margin-bottom: 16px; }
            .olo-ms-filters-row { display: flex; gap: 12px; align-items: center; flex-wrap: wrap; }
            .olo-ms-filter-group { display: flex; gap: 6px; align-items: center; }
            .olo-ms-filter-label { font-size: 13px; font-weight: 600; color: #1a1a1a; white-space: nowrap; }
            .olo-ms-filter-select { width: auto !important; padding: 7px 12px !important; }
            .olo-ms-filter-num { width: 80px !important; padding: 7px 10px !important; text-align: center; }
            .olo-ms-filter-sep { font-size: 12px; color: #999; }
            .olo-ms-filter-dash { font-size: 13px; color: #999; }

            /* GSV panel */
            .olo-ms-gsv-panel { display: none; }
            .olo-ms-gsv-inputs { margin-top: 12px; }
            .olo-ms-gsv-field { flex: 1; max-width: 600px; width: auto !important; }
            .olo-ms-gsv-select { width: auto !important; padding: 9px 12px !important; }
            .olo-ms-gsv-preview {
                display: flex; gap: 20px; padding: 20px; background: #fff;
                border: 1px solid #eaeaea; border-radius: 14px; margin-top: 16px; align-items: flex-start;
            }
            .olo-ms-gsv-preview img { max-width: 400px; border-radius: 10px; }
            .olo-ms-gsv-meta { flex: 1; font-size: 13px; }
            .olo-ms-gsv-meta p { margin: 4px 0; color: #666; }
            .olo-ms-gsv-meta strong { color: #1a1a1a; }

            /* Status */
            .olo-ms-status { margin-bottom: 16px; font-size: 13px; color: #999; }
            .olo-ms-status .olo-ms-loading { color: #e8622a; font-weight: 600; }
            .olo-ms-status .olo-ms-error { color: #dc2626; }

            /* Results grid */
            .olo-ms-results {
                display: grid; gap: 16px;
                grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            }
            .olo-ms-results.audio-grid {
                grid-template-columns: 1fr;
                max-width: 900px;
            }

            /* Photo/Video card */
            .olo-ms-card {
                position: relative; border-radius: 14px; overflow: hidden;
                background: #f5f5f5; cursor: pointer; transition: transform .15s, box-shadow .15s;
                aspect-ratio: 4/3;
            }
            .olo-ms-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,.12); }
            .olo-ms-card img { width: 100%; height: 100%; object-fit: cover; display: block; }
            .olo-ms-card video { width: 100%; height: 100%; object-fit: cover; display: block; }
            .olo-ms-card-info {
                position: absolute; bottom: 0; left: 0; right: 0;
                padding: 28px 12px 10px; font-size: 11px; color: #fff;
                background: linear-gradient(transparent, rgba(0,0,0,.7));
            }
            .olo-ms-card-info .photographer { font-weight: 600; }
            .olo-ms-card-badge {
                position: absolute; top: 10px; right: 10px;
                background: rgba(26,26,26,.7); color: #fff; font-size: 10px;
                padding: 3px 10px; border-radius: 20px; font-weight: 600;
                backdrop-filter: blur(4px);
            }
            .olo-ms-card-tags {
                position: absolute; top: 10px; left: 10px;
                display: flex; gap: 4px; flex-wrap: wrap;
            }
            .olo-ms-card-tag {
                background: rgba(26,26,26,.6); color: #fff; font-size: 9px;
                padding: 2px 8px; border-radius: 20px; backdrop-filter: blur(4px);
            }

            /* Audio card */
            .olo-ms-audio-card {
                display: flex; gap: 14px; padding: 14px 18px; border: 1px solid #eaeaea;
                border-radius: 14px; background: #fff; align-items: center;
                transition: border-color .15s;
            }
            .olo-ms-audio-card:hover { border-color: #1a1a1a; }
            .olo-ms-audio-play {
                width: 44px; height: 44px; border-radius: 50%; background: #1a1a1a;
                color: #fff; border: none; cursor: pointer; font-size: 16px;
                display: flex; align-items: center; justify-content: center; flex-shrink: 0;
                transition: background .15s;
            }
            .olo-ms-audio-play:hover { background: #333; }
            .olo-ms-audio-play.playing { background: #e8622a; }
            .olo-ms-audio-info { flex: 1; min-width: 0; }
            .olo-ms-audio-name { font-weight: 700; font-size: 13px; color: #1a1a1a; margin-bottom: 2px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
            .olo-ms-audio-meta { font-size: 11px; color: #999; display: flex; gap: 12px; flex-wrap: wrap; }
            .olo-ms-audio-wave { width: 200px; height: 40px; flex-shrink: 0; border-radius: 8px; object-fit: cover; }
            .olo-ms-audio-actions { flex-shrink: 0; }
            .olo-ms-import-btn {
                padding: 6px 14px; border-radius: 8px; border: none;
                background: #1a1a1a; color: #fff; cursor: pointer; font-size: 12px;
                font-weight: 600; transition: all .15s; font-family: inherit;
            }
            .olo-ms-import-btn:hover { background: #333; box-shadow: 0 4px 12px rgba(0,0,0,.15); }
            .olo-ms-import-btn:disabled { opacity: .4; cursor: wait; }
            .olo-ms-import-btn.imported { background: #059669; }

            /* Pagination */
            .olo-ms-pagination { display: flex; gap: 6px; justify-content: center; margin-top: 24px; }
            .olo-ms-pagination button {
                padding: 7px 14px; border: 1.5px solid #eaeaea; background: #fff;
                cursor: pointer; border-radius: 10px; font-size: 13px; font-weight: 600;
                color: #666; transition: all .15s; font-family: inherit;
            }
            .olo-ms-pagination button:hover { border-color: #1a1a1a; color: #1a1a1a; }
            .olo-ms-pagination button.active { background: #1a1a1a; color: #fff; border-color: #1a1a1a; }
            .olo-ms-pagination button:disabled { opacity: .4; cursor: not-allowed; }

            /* Modal */
            .olo-ms-modal { position: fixed; inset: 0; z-index: 100000; display: none; align-items: center; justify-content: center; }
            .olo-ms-modal-overlay { position: absolute; inset: 0; background: rgba(0,0,0,.6); backdrop-filter: blur(4px); }
            .olo-ms-modal-content {
                position: relative; background: #fff; border-radius: 16px;
                max-width: 800px; width: 90%; max-height: 85vh; overflow: auto; padding: 28px;
                box-shadow: 0 20px 60px rgba(0,0,0,.2);
            }
            .olo-ms-modal-close {
                position: absolute; top: 14px; right: 14px; background: none; border: none;
                font-size: 24px; cursor: pointer; color: #999; z-index: 1; transition: color .15s;
            }
            .olo-ms-modal-close:hover { color: #dc2626; }
            .olo-ms-modal-img { width: 100%; border-radius: 12px; margin-bottom: 16px; }
            .olo-ms-modal-info { font-size: 13px; color: #999; }
            .olo-ms-modal-info strong { color: #1a1a1a; }
            .olo-ms-modal-actions { margin-top: 16px; display: flex; gap: 10px; }
        </style>

        <script>
        (function(){
            const REST = <?php echo wp_json_encode( $rest_url ); ?>;
            const REST_VTOUR = <?php echo wp_json_encode( $rest_url_vtour ); ?>;
            const NONCE = <?php echo wp_json_encode( $nonce ); ?>;
            const KEYS = <?php echo wp_json_encode( $keys ); ?>;

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
                    { id: 'polyhaven', label: 'Poly Haven (HDRI)', key: KEYS.polyhaven },
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

            let currentTab = 'photo';
            let currentProvider = 'unsplash';
            let currentPage = 1;
            let currentQuery = '';
            let totalPages = 0;
            let currentAudio = null;
            let gsvData = null;

            const $ = s => document.querySelector(s);
            const $$ = s => document.querySelectorAll(s);

            // Filtri supportati per provider foto
            const PHOTO_FILTERS = {
                unsplash:  ['orientation'],
                pexels:    ['orientation', 'size'],
                pixabay:   ['orientation', 'min_width', 'min_height'],
                openverse: ['orientation', 'size'],
            };

            // Init
            renderProviders();
            bindEvents();

            function bindEvents() {
                $$('.olo-ms-tab').forEach(t => t.addEventListener('click', () => switchTab(t.dataset.tab)));
                $('#olo-ms-search-btn').addEventListener('click', () => doSearch());
                $('#olo-ms-query').addEventListener('keydown', e => { if(e.key === 'Enter') doSearch(); });
                $('.olo-ms-modal-overlay').addEventListener('click', closeModal);
                $('.olo-ms-modal-close').addEventListener('click', closeModal);
                $('#olo-ms-gsv-btn').addEventListener('click', doGsvResolve);
                $('#olo-ms-gsv-input').addEventListener('keydown', e => { if(e.key === 'Enter') doGsvResolve(); });
            }

            function switchTab(tab) {
                currentTab = tab;
                $$('.olo-ms-tab').forEach(t => t.classList.toggle('active', t.dataset.tab === tab));
                renderProviders();
                $('#olo-ms-results').innerHTML = '';
                $('#olo-ms-pagination').innerHTML = '';
                $('#olo-ms-status').innerHTML = '';
                updateVisibility();
            }

            function updateVisibility() {
                const isGsv = currentTab === 'photo360' && currentProvider === 'googlesv';
                $('#olo-ms-gsv-panel').style.display = isGsv ? 'block' : 'none';
                $('.olo-ms-search-bar').style.display = isGsv ? 'none' : 'block';
                const showDur = currentTab === 'audio' || currentTab === 'video' || currentTab === 'video360';
                $('#olo-ms-duration-filters').style.display = showDur ? 'block' : 'none';

                const isPhoto = currentTab === 'photo';
                $('#olo-ms-photo-filters').style.display = isPhoto ? 'block' : 'none';
                if (isPhoto) {
                    const allowed = PHOTO_FILTERS[currentProvider] || [];
                    $$('.olo-ms-pf').forEach(el => {
                        el.style.display = allowed.includes(el.dataset.pf) ? 'flex' : 'none';
                    });
                    // Pixabay non supporta "quadrato" — nascondo l'opzione
                    const sqOpt = $('#olo-ms-orientation').querySelector('option[value="square"]');
                    if (sqOpt) sqOpt.style.display = currentProvider === 'pixabay' ? 'none' : '';
                }
            }

            function renderProviders() {
                const wrap = $('#olo-ms-providers');
                const list = PROVIDERS[currentTab] || [];
                wrap.innerHTML = list.map(p => {
                    const dot = p.key ? 'olo-ms-dot-ok' : 'olo-ms-dot-no';
                    const cls = p.key ? '' : ' disabled';
                    return '<button class="olo-ms-provider' + cls + '" data-provider="' + p.id + '">' +
                           '<span class="olo-ms-dot ' + dot + '"></span>' + p.label + '</button>';
                }).join('');

                // Select first available
                const first = list.find(p => p.key);
                if (first) {
                    currentProvider = first.id;
                    const btn = wrap.querySelector('[data-provider="' + first.id + '"]');
                    if (btn) btn.classList.add('active');
                }

                wrap.querySelectorAll('.olo-ms-provider').forEach(btn => {
                    btn.addEventListener('click', () => {
                        if (btn.classList.contains('disabled')) return;
                        wrap.querySelectorAll('.olo-ms-provider').forEach(b => b.classList.remove('active'));
                        btn.classList.add('active');
                        currentProvider = btn.dataset.provider;
                        updateVisibility();
                        if (currentQuery && currentProvider !== 'googlesv') doSearch();
                    });
                });

                updateVisibility();
            }

            function doSearch(page) {
                const q = $('#olo-ms-query').value.trim();
                if (!q) return;
                currentQuery = q;
                currentPage = page || 1;

                const status = $('#olo-ms-status');
                status.innerHTML = '<span class="olo-ms-loading">Ricerca in corso...</span>';
                $('#olo-ms-results').innerHTML = '';
                $('#olo-ms-pagination').innerHTML = '';

                let url = '';
                const pp = (currentTab === 'video' || currentTab === 'video360') ? 15 : 30;

                if (currentTab === 'photo') {
                    url = REST + '/' + currentProvider + '/search?query=' + encodeURIComponent(q) +
                          '&page=' + currentPage + '&per_page=' + pp;
                    const pf = getPhotoFilters();
                    if (pf.orientation) url += '&orientation=' + pf.orientation;
                    if (pf.size) url += '&size=' + pf.size;
                    if (pf.min_width) url += '&min_width=' + pf.min_width;
                    if (pf.min_height) url += '&min_height=' + pf.min_height;
                } else if (currentTab === 'video') {
                    url = REST + '/' + currentProvider + '/videos?query=' + encodeURIComponent(q) +
                          '&page=' + currentPage + '&per_page=' + pp;
                    const vdur = getVideoDuration();
                    if (vdur.min) url += '&min_duration=' + vdur.min;
                    if (vdur.max) url += '&max_duration=' + vdur.max;
                } else if (currentTab === 'photo360' && currentProvider === 'polyhaven') {
                    url = REST_VTOUR + '/polyhaven/search?query=' + encodeURIComponent(q) +
                          '&page=' + currentPage + '&per_page=' + pp;
                } else if (currentTab === 'video360') {
                    url = REST + '/' + currentProvider + '/videos?query=' + encodeURIComponent('360 ' + q) +
                          '&page=' + currentPage + '&per_page=' + pp;
                    const vdur360 = getVideoDuration();
                    if (vdur360.min) url += '&min_duration=' + vdur360.min;
                    if (vdur360.max) url += '&max_duration=' + vdur360.max;
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
                            status.innerHTML = '<span style="color:#d63638;">' + (data.message || 'Errore') + '</span>';
                            return;
                        }
                        const results = data.results || [];
                        totalPages = data.total_pages || 0;
                        const total = data.total || 0;
                        status.innerHTML = total + ' risultati trovati — Pagina ' + currentPage + ' di ' + Math.max(1, totalPages);
                        renderResults(results);
                        renderPagination();
                    })
                    .catch(err => {
                        status.innerHTML = '<span style="color:#d63638;">Errore: ' + err.message + '</span>';
                    });
            }

            function getPhotoFilters() {
                return {
                    orientation: $('#olo-ms-orientation').value || '',
                    size: $('#olo-ms-size').value || '',
                    min_width: $('#olo-ms-min-width').value || '',
                    min_height: $('#olo-ms-min-height').value || '',
                };
            }

            function getVideoDuration() {
                const preset = $('#olo-ms-dur-preset').value;
                if (preset) {
                    const parts = preset.split(',');
                    return { min: parts[0] || '', max: parts[1] || '' };
                }
                return { min: $('#olo-ms-dur-min').value || '', max: $('#olo-ms-dur-max').value || '' };
            }

            function buildDurationFilter() {
                // Check preset first
                const preset = $('#olo-ms-dur-preset').value;
                if (preset) {
                    const parts = preset.split(',');
                    const min = parts[0] || '*';
                    const max = parts[1] || '*';
                    return 'duration:[' + min + ' TO ' + max + ']';
                }
                // Check manual inputs
                const min = $('#olo-ms-dur-min').value;
                const max = $('#olo-ms-dur-max').value;
                if (min || max) {
                    return 'duration:[' + (min || '*') + ' TO ' + (max || '*') + ']';
                }
                return '';
            }

            // Sync preset with manual inputs
            $('#olo-ms-dur-preset').addEventListener('change', function() {
                if (this.value) {
                    $('#olo-ms-dur-min').value = '';
                    $('#olo-ms-dur-max').value = '';
                }
            });
            $('#olo-ms-dur-min').addEventListener('input', function() { $('#olo-ms-dur-preset').value = ''; });
            $('#olo-ms-dur-max').addEventListener('input', function() { $('#olo-ms-dur-preset').value = ''; });

            function renderResults(results) {
                const wrap = $('#olo-ms-results');
                if (currentTab === 'audio') {
                    wrap.className = 'olo-ms-results audio-grid';
                    wrap.innerHTML = results.map(renderAudioCard).join('');
                    bindAudioEvents();
                } else if (currentTab === 'photo360' && currentProvider === 'polyhaven') {
                    wrap.className = 'olo-ms-results';
                    wrap.innerHTML = results.map(render360Card).join('');
                    bind360CardEvents(results);
                } else if (currentTab === 'video' || currentTab === 'video360') {
                    wrap.className = 'olo-ms-results';
                    wrap.innerHTML = results.map(renderVideoCard).join('');
                    bindCardEvents(results);
                } else {
                    wrap.className = 'olo-ms-results';
                    wrap.innerHTML = results.map(renderPhotoCard).join('');
                    bindCardEvents(results);
                }
            }

            function renderPhotoCard(r) {
                return '<div class="olo-ms-card" data-id="' + r.id + '">' +
                       '<img src="' + esc(r.thumb) + '" alt="' + esc(r.alt || '') + '" loading="lazy" />' +
                       '<div class="olo-ms-card-info"><span class="photographer">' + esc(r.photographer || '') + '</span></div>' +
                       (r.width ? '<span class="olo-ms-card-badge">' + r.width + 'x' + r.height + '</span>' : '') +
                       '</div>';
            }

            function renderVideoCard(r) {
                const thumb = r.thumb || '';
                const isVideoThumb = r.is_video_thumb;
                const media = isVideoThumb
                    ? '<video src="' + esc(thumb) + '" muted preload="metadata" style="pointer-events:none;"></video>'
                    : '<img src="' + esc(thumb) + '" alt="" loading="lazy" />';
                return '<div class="olo-ms-card" data-id="' + r.id + '">' +
                       media +
                       '<div class="olo-ms-card-info"><span class="photographer">' + esc(r.photographer || '') + '</span></div>' +
                       '<span class="olo-ms-card-badge">' + (r.duration ? formatDuration(r.duration) : 'Video') + '</span>' +
                       '</div>';
            }

            function render360Card(r) {
                const tags = (r.tags || []).slice(0, 3).map(t =>
                    '<span class="olo-ms-card-tag">' + esc(t) + '</span>'
                ).join('');
                return '<div class="olo-ms-card olo-ms-360-card" data-slug="' + esc(r.slug) + '">' +
                       '<img src="' + esc(r.thumb) + '" alt="' + esc(r.name || '') + '" loading="lazy" />' +
                       '<div class="olo-ms-card-info"><span class="photographer">' + esc(r.author || 'Poly Haven') + '</span></div>' +
                       (tags ? '<div class="olo-ms-card-tags">' + tags + '</div>' : '') +
                       '<span class="olo-ms-card-badge">HDRI 360</span>' +
                       '</div>';
            }

            function bind360CardEvents(results) {
                $$('.olo-ms-360-card').forEach((card, i) => {
                    card.addEventListener('click', () => show360Preview(results[i]));
                });
            }

            function show360Preview(item) {
                const modal = $('#olo-ms-modal');
                const body = $('#olo-ms-modal-body');
                const tags = (item.tags || []).map(t => '<span style="display:inline-block;padding:2px 8px;background:#f0f0f0;border-radius:10px;font-size:11px;margin:2px;">' + esc(t) + '</span>').join(' ');

                body.innerHTML =
                    '<img src="' + esc(item.preview || item.thumb) + '" class="olo-ms-modal-img" />' +
                    '<div class="olo-ms-modal-info">' +
                    '<p><strong>Nome:</strong> ' + esc(item.name) + '</p>' +
                    '<p><strong>Autore:</strong> ' + esc(item.author || 'Poly Haven') + '</p>' +
                    '<p><strong>Licenza:</strong> CC0 (dominio pubblico)</p>' +
                    (tags ? '<p><strong>Tags:</strong> ' + tags + '</p>' : '') +
                    '</div>' +
                    '<div class="olo-ms-modal-actions">' +
                    '<button class="olo-ms-import-btn" id="olo-ms-modal-import-360">Importa JPG nella Media Library</button>' +
                    '</div>';

                modal.style.display = 'flex';

                $('#olo-ms-modal-import-360').addEventListener('click', function() {
                    import360(item, this);
                });
            }

            function import360(item, btn) {
                btn.disabled = true;
                btn.textContent = 'Download...';

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
                    if (data.id) {
                        btn.textContent = 'Importato!';
                        btn.classList.add('imported');
                    } else {
                        btn.textContent = data.message || 'Errore';
                        setTimeout(() => { btn.textContent = 'Importa JPG nella Media Library'; btn.disabled = false; }, 3000);
                    }
                })
                .catch(() => {
                    btn.textContent = 'Errore';
                    setTimeout(() => { btn.textContent = 'Importa JPG nella Media Library'; btn.disabled = false; }, 3000);
                });
            }

            /* ── Google Street View ── */

            function doGsvResolve() {
                const input = $('#olo-ms-gsv-input').value.trim();
                if (!input) return;

                const status = $('#olo-ms-status');
                status.innerHTML = '<span class="olo-ms-loading">Ricerca panorama Street View...</span>';
                $('#olo-ms-results').innerHTML = '';

                fetch(REST_VTOUR + '/googlesv/resolve', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': NONCE },
                    body: JSON.stringify({ input: input }),
                })
                .then(r => r.json())
                .then(data => {
                    if (data.code) {
                        status.innerHTML = '<span style="color:#d63638;">' + (data.message || 'Errore') + '</span>';
                        return;
                    }
                    gsvData = data;
                    status.innerHTML = '';
                    renderGsvResult(data);
                })
                .catch(err => {
                    status.innerHTML = '<span style="color:#d63638;">Errore: ' + err.message + '</span>';
                });
            }

            function renderGsvResult(data) {
                const wrap = $('#olo-ms-results');
                wrap.className = 'olo-ms-results';
                wrap.style.display = 'block';

                let historyHtml = '';
                if (data.history && data.history.length > 1) {
                    historyHtml = '<p><strong>Timeline:</strong> ' +
                        data.history.map(h => '<span style="display:inline-block;padding:2px 8px;background:#f0f6fc;border-radius:10px;font-size:11px;margin:2px;cursor:pointer;" onclick="document.getElementById(\'olo-ms-gsv-input\').value=\'' + esc(h.pano_id) + '\'">' + esc(h.date) + '</span>').join(' ') +
                        '</p>';
                }

                wrap.innerHTML =
                    '<div class="olo-ms-gsv-preview">' +
                    '<img src="' + esc(data.preview_url) + '" alt="Street View preview" />' +
                    '<div class="olo-ms-gsv-meta">' +
                    '<p><strong>Pano ID:</strong> ' + esc(data.pano_id) + '</p>' +
                    (data.description ? '<p><strong>Luogo:</strong> ' + esc(data.description) + '</p>' : '') +
                    '<p><strong>Coordinate:</strong> ' + data.lat + ', ' + data.lng + '</p>' +
                    (data.copyright ? '<p><strong>Copyright:</strong> ' + esc(data.copyright) + '</p>' : '') +
                    (data.date ? '<p><strong>Data:</strong> ' + esc(data.date) + '</p>' : '') +
                    '<p><strong>Zoom max:</strong> ' + data.max_zoom + '</p>' +
                    historyHtml +
                    '<div style="margin-top:16px;">' +
                    '<button class="olo-ms-import-btn" id="olo-ms-gsv-download">Scarica e importa nella Media Library</button>' +
                    '</div>' +
                    '</div>' +
                    '</div>';

                $('#olo-ms-gsv-download').addEventListener('click', function() {
                    doGsvDownload(this);
                });
            }

            function doGsvDownload(btn) {
                if (!gsvData) return;
                btn.disabled = true;
                btn.textContent = 'Download tile in corso...';

                const zoom = parseInt($('#olo-ms-gsv-zoom').value) || 3;

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
                    if (data.id) {
                        btn.textContent = 'Importato!';
                        btn.classList.add('imported');
                    } else {
                        btn.textContent = data.message || 'Errore';
                        setTimeout(() => { btn.textContent = 'Scarica e importa nella Media Library'; btn.disabled = false; }, 3000);
                    }
                })
                .catch(() => {
                    btn.textContent = 'Errore';
                    setTimeout(() => { btn.textContent = 'Scarica e importa nella Media Library'; btn.disabled = false; }, 3000);
                });
            }

            function renderAudioCard(r) {
                const dur = r.duration ? formatDuration(r.duration) : '';
                const size = r.filesize ? formatSize(r.filesize) : '';
                const wave = r.waveform ? '<img class="olo-ms-audio-wave" src="' + esc(r.waveform) + '" alt="" />' : '';
                return '<div class="olo-ms-audio-card" data-id="' + r.id + '">' +
                       '<button class="olo-ms-audio-play" data-mp3="' + esc(r.preview_mp3 || r.preview_ogg || '') + '">&#9654;</button>' +
                       '<div class="olo-ms-audio-info">' +
                       '<div class="olo-ms-audio-name">' + esc(r.name) + '</div>' +
                       '<div class="olo-ms-audio-meta">' +
                       '<span>' + esc(r.username) + '</span>' +
                       (dur ? '<span>' + dur + '</span>' : '') +
                       (size ? '<span>' + size + '</span>' : '') +
                       (r.type ? '<span>' + esc(r.type.toUpperCase()) + '</span>' : '') +
                       '</div>' +
                       '</div>' +
                       wave +
                       '<div class="olo-ms-audio-actions">' +
                       '<button class="olo-ms-import-btn" data-sound-id="' + r.id + '" data-name="' + esc(r.name) + '" data-username="' + esc(r.username) + '" data-preview-url="' + esc(r.preview_mp3 || r.preview_ogg || '') + '">Importa</button>' +
                       '</div>' +
                       '</div>';
            }

            function bindAudioEvents() {
                $$('.olo-ms-audio-play').forEach(btn => {
                    btn.addEventListener('click', () => {
                        const src = btn.dataset.mp3;
                        if (!src) return;
                        if (currentAudio) {
                            currentAudio.pause();
                            $$('.olo-ms-audio-play').forEach(b => { b.classList.remove('playing'); b.innerHTML = '&#9654;'; });
                        }
                        if (currentAudio && currentAudio.src === src) {
                            currentAudio = null;
                            return;
                        }
                        currentAudio = new Audio(src);
                        currentAudio.play();
                        btn.classList.add('playing');
                        btn.innerHTML = '&#9632;';
                        currentAudio.addEventListener('ended', () => {
                            btn.classList.remove('playing');
                            btn.innerHTML = '&#9654;';
                            currentAudio = null;
                        });
                    });
                });

                $$('.olo-ms-audio-card .olo-ms-import-btn').forEach(btn => {
                    btn.addEventListener('click', () => importAudio(btn));
                });
            }

            function bindCardEvents(results) {
                $$('.olo-ms-card').forEach((card, i) => {
                    card.addEventListener('click', () => showPreview(results[i]));
                });
            }

            function showPreview(item) {
                const modal = $('#olo-ms-modal');
                const body = $('#olo-ms-modal-body');

                let mediaHtml = '';
                if (currentTab === 'video' || currentTab === 'video360') {
                    const src = item.regular || item.thumb || '';
                    mediaHtml = '<video src="' + esc(src) + '" controls class="olo-ms-modal-img" style="max-height:400px;"></video>';
                } else {
                    mediaHtml = '<img src="' + esc(item.large || item.regular || item.thumb) + '" class="olo-ms-modal-img" />';
                }

                body.innerHTML = mediaHtml +
                    '<div class="olo-ms-modal-info">' +
                    '<p><strong>Autore:</strong> ' + esc(item.photographer || 'Sconosciuto') + '</p>' +
                    (item.width ? '<p><strong>Dimensioni:</strong> ' + item.width + ' x ' + item.height + '</p>' : '') +
                    (item.alt ? '<p><strong>Descrizione:</strong> ' + esc(item.alt) + '</p>' : '') +
                    (item.license ? '<p><strong>Licenza:</strong> ' + esc(item.license) + '</p>' : '') +
                    '</div>' +
                    '<div class="olo-ms-modal-actions">' +
                    '<button class="olo-ms-import-btn" id="olo-ms-modal-import">Importa nella Media Library</button>' +
                    '</div>';

                modal.style.display = 'flex';

                $('#olo-ms-modal-import').addEventListener('click', function() {
                    importMedia(item, this);
                });
            }

            function closeModal() {
                $('#olo-ms-modal').style.display = 'none';
            }

            function importMedia(item, btn) {
                btn.disabled = true;
                btn.textContent = 'Download...';

                let url = '';
                let bodyData = {};

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
                    if (item.download_location) {
                        bodyData.download_location = item.download_location;
                    }
                }

                fetch(url, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': NONCE },
                    body: JSON.stringify(bodyData),
                })
                .then(r => r.json())
                .then(data => {
                    if (data.id) {
                        btn.textContent = 'Importato!';
                        btn.classList.add('imported');
                    } else {
                        btn.textContent = 'Errore';
                        btn.disabled = false;
                    }
                })
                .catch(() => {
                    btn.textContent = 'Errore';
                    btn.disabled = false;
                });
            }

            function importAudio(btn) {
                btn.disabled = true;
                btn.textContent = 'Download...';

                fetch(REST + '/freesound/download', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': NONCE },
                    body: JSON.stringify({
                        sound_id: parseInt(btn.dataset.soundId),
                        name: btn.dataset.name || '',
                        username: btn.dataset.username || '',
                        preview_url: btn.dataset.previewUrl || '',
                    }),
                })
                .then(r => r.json())
                .then(data => {
                    if (data.id) {
                        btn.textContent = 'Importato!';
                        btn.classList.add('imported');
                    } else {
                        btn.textContent = data.message || 'Errore';
                        setTimeout(() => { btn.textContent = 'Importa'; btn.disabled = false; }, 3000);
                    }
                })
                .catch(() => {
                    btn.textContent = 'Errore';
                    setTimeout(() => { btn.textContent = 'Importa'; btn.disabled = false; }, 3000);
                });
            }

            function renderPagination() {
                const wrap = $('#olo-ms-pagination');
                if (totalPages <= 1) { wrap.innerHTML = ''; return; }

                let html = '';
                html += '<button ' + (currentPage <= 1 ? 'disabled' : '') + ' data-page="' + (currentPage - 1) + '">&laquo; Prec</button>';

                const start = Math.max(1, currentPage - 3);
                const end = Math.min(totalPages, currentPage + 3);
                for (let i = start; i <= end; i++) {
                    html += '<button class="' + (i === currentPage ? 'active' : '') + '" data-page="' + i + '">' + i + '</button>';
                }

                html += '<button ' + (currentPage >= totalPages ? 'disabled' : '') + ' data-page="' + (currentPage + 1) + '">Succ &raquo;</button>';

                wrap.innerHTML = html;
                wrap.querySelectorAll('button').forEach(btn => {
                    btn.addEventListener('click', () => {
                        const p = parseInt(btn.dataset.page);
                        if (p >= 1 && p <= totalPages) doSearch(p);
                    });
                });
            }

            function formatDuration(sec) {
                sec = Math.round(sec);
                const m = Math.floor(sec / 60);
                const s = sec % 60;
                return m + ':' + String(s).padStart(2, '0');
            }

            function formatSize(bytes) {
                if (bytes > 1048576) return (bytes / 1048576).toFixed(1) + ' MB';
                if (bytes > 1024) return (bytes / 1024).toFixed(0) + ' KB';
                return bytes + ' B';
            }

            function esc(str) {
                const d = document.createElement('div');
                d.textContent = str || '';
                return d.innerHTML;
            }
        })();
        </script>
        <?php
    }
}
