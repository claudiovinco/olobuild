/* ═══════════════════════════════════════════════════════════════════
   Olobuild — Auto thumbnail capture
   Listener su evento `olobuild:saved`. Cattura il primo viewport del
   canvas/iframe del builder via html2canvas → upload come JPEG 1280×720
   sul template. Lazy load di html2canvas alla prima esecuzione.
   ═══════════════════════════════════════════════════════════════════ */
(function() {
    'use strict';

    const cfg = window.oloThumbConfig || {};
    if (!cfg.restUrl || !cfg.nonce || !cfg.vendorUrl) return;

    const VIEWPORT = { w: 1280, h: 720 }; // 16:9 logico
    const OUT     = { w: 640,  h: 360 };  // output thumbnail
    const QUALITY = 0.78;
    const DEBUG   = !!cfg.debug;

    function log() {
        if (DEBUG && window.console) console.log.apply(console, [ '[olo-thumb]' ].concat([].slice.call(arguments)));
    }
    function warn(msg) {
        if (window.console && console.warn) console.warn('[olo-thumb]', msg);
    }

    let h2cPromise = null;
    function loadHtml2Canvas() {
        if (window.html2canvas) return Promise.resolve(window.html2canvas);
        if (h2cPromise) return h2cPromise;
        h2cPromise = new Promise((resolve, reject) => {
            const s = document.createElement('script');
            s.src = cfg.vendorUrl;
            s.async = true;
            s.onload = () => resolve(window.html2canvas);
            s.onerror = () => { h2cPromise = null; reject(new Error('html2canvas load failed')); };
            document.head.appendChild(s);
        });
        return h2cPromise;
    }

    /**
     * Trova il nodo da catturare. Considera il TIPO del template per pescare
     * la zona giusta nel canvas Vue (per evitare di catturare solo l'header
     * quando si salva una page).
     *
     * Priorità:
     *  1. Iframe live preview same-origin → doc.body (rendering completo del sito)
     *  2. Canvas Vue: zona corrispondente al type, oppure body zone, oppure
     *     primo .olo-template di dimensioni significative.
     */
    function findCaptureTarget(templateType) {
        // 1. Iframe live preview (rendering reale)
        const iframe = document.querySelector('iframe.olo-live-iframe');
        if (iframe) {
            try {
                const doc = iframe.contentDocument;
                if (doc && doc.body && doc.body.scrollHeight > 50) {
                    log('target: iframe body', doc.body.scrollWidth, '×', doc.body.scrollHeight);
                    return { node: doc.body, doc, iframe, isIframe: true };
                }
                warn('iframe body troppo piccolo, scrollHeight=' + (doc && doc.body ? doc.body.scrollHeight : 'n/a'));
            } catch (e) {
                warn('iframe cross-origin: ' + e.message);
            }
        }

        // 2. Canvas Vue — pesca la zona corretta
        const root = document;

        // Per template type-specifici, prendi la zona corrispondente
        if (templateType === 'header') {
            const el = root.querySelector('.olo-unified-zone--header .olo-template, .olo-preview-header.olo-template');
            if (el && elemHasContent(el)) { log('target: header zone'); return { node: el, doc: root }; }
        }
        if (templateType === 'footer') {
            const el = root.querySelector('.olo-unified-zone--footer .olo-template, .olo-preview-footer.olo-template');
            if (el && elemHasContent(el)) { log('target: footer zone'); return { node: el, doc: root }; }
        }

        // Body zone (unified mode)
        const bodyZone = root.querySelector('.olo-unified-zone--body .olo-template');
        if (bodyZone && elemHasContent(bodyZone)) {
            log('target: unified body zone');
            return { node: bodyZone, doc: root };
        }

        // Non-unified fallback: il primo .olo-template che ha dimensione e
        // NON è preview header/footer e NON è dentro la zona header.
        const candidates = root.querySelectorAll('.olo-template');
        for (const el of candidates) {
            if (el.classList.contains('olo-preview-header') || el.classList.contains('olo-preview-footer')) continue;
            if (el.closest('.olo-unified-zone--header')) continue;
            if (el.closest('.olo-unified-zone--footer')) continue;
            if (elemHasContent(el)) { log('target: fallback .olo-template', el); return { node: el, doc: root }; }
        }

        return null;
    }

    function elemHasContent(el) {
        const r = el.getBoundingClientRect();
        return r.width > 200 && r.height > 100;
    }

    /**
     * Esegue la cattura, ridimensiona e upload.
     */
    async function captureAndUpload(templateId, opts) {
        opts = opts || {};
        if (!templateId) return;

        const target = findCaptureTarget(opts.templateType);
        if (!target) { warn('no capture target found'); return; }

        let h2c;
        try { h2c = await loadHtml2Canvas(); }
        catch (e) { warn('html2canvas unavailable: ' + e.message); return; }

        const node = target.node;
        // Dimensioni REALI: per body iframe usiamo scrollWidth/scrollHeight,
        // per div Vue usiamo getBoundingClientRect (offsetWidth/Height).
        const W = node.scrollWidth || node.offsetWidth || node.getBoundingClientRect().width || VIEWPORT.w;
        const H = node.scrollHeight || node.offsetHeight || node.getBoundingClientRect().height || VIEWPORT.h;

        // Capture: largh = quella vera; alt = limita ai primi 1.5×viewport (above-the-fold + un po')
        const captureW = Math.max(W, 800);
        const captureH = Math.min(H, Math.round(captureW * (VIEWPORT.h / VIEWPORT.w) * 1.5));
        log('capture size', captureW, '×', captureH, '(node was', W, '×', H, ')');

        let canvas;
        try {
            canvas = await h2c(node, {
                width: captureW,
                height: captureH,
                windowWidth: captureW,
                windowHeight: captureH,
                scale: 1,
                useCORS: true,
                allowTaint: false,
                backgroundColor: '#ffffff',
                logging: DEBUG,
                imageTimeout: 15000,
                ignoreElements: function(el) {
                    // Escludi overlay del builder che NON sono parte della pagina utente
                    if (!el.classList) return false;
                    return el.classList.contains('olo-tile-toolbar') ||
                           el.classList.contains('olo-cell-tools') ||
                           el.classList.contains('olo-zone-label') ||
                           el.classList.contains('olo-empty-zone-cta') ||
                           el.classList.contains('olo-resize-handle') ||
                           el.classList.contains('CanvasDragOverlay') ||
                           el.id === 'wpadminbar';
                },
            });
        } catch (e) {
            warn('html2canvas error: ' + e.message);
            return;
        }
        log('canvas captured', canvas.width, '×', canvas.height);

        // Crop 16:9 dall'alto (above-the-fold)
        const out = document.createElement('canvas');
        out.width = OUT.w; out.height = OUT.h;
        const ctx = out.getContext('2d');
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, OUT.w, OUT.h);

        const srcRatio = canvas.width / canvas.height;
        const dstRatio = OUT.w / OUT.h;
        let sx = 0, sy = 0, sw = canvas.width, sh = canvas.height;
        if (srcRatio > dstRatio) {
            // sorgente più larga del 16:9: ritaglia ai lati (raro)
            sw = Math.round(canvas.height * dstRatio);
            sx = Math.round((canvas.width - sw) / 2);
        } else {
            // sorgente più alta: above-the-fold (sy=0)
            sh = Math.round(canvas.width / dstRatio);
            sy = 0;
        }
        ctx.drawImage(canvas, sx, sy, sw, sh, 0, 0, OUT.w, OUT.h);

        const blob = await new Promise(res => out.toBlob(res, 'image/jpeg', QUALITY));
        if (!blob) { warn('toBlob failed'); return; }
        log('blob ready', blob.size, 'bytes');

        const fd = new FormData();
        fd.append('file', blob, 'thumb.jpg');

        try {
            const res = await fetch(cfg.restUrl + 'templates/' + templateId + '/thumbnail', {
                method: 'POST',
                headers: { 'X-WP-Nonce': cfg.nonce },
                credentials: 'same-origin',
                body: fd,
            });
            if (!res.ok) throw new Error('HTTP ' + res.status);
            const json = await res.json();
            if (window.console) console.log('[olo-thumb] saved:', json.thumbnail_url);
            window.dispatchEvent(new CustomEvent('olobuild:thumbnail-updated', {
                detail: { templateId: templateId, url: json.thumbnail_url }
            }));
        } catch (e) {
            warn('upload failed: ' + e.message);
        }
    }

    /* Debounce: evita capture multiple ravvicinate */
    let pending = null;
    function scheduleCapture(templateId, opts) {
        if (pending) clearTimeout(pending);
        pending = setTimeout(() => {
            pending = null;
            captureAndUpload(templateId, opts);
        }, 1800);
    }

    /* Listener: store builder dispatcha l'evento dopo save success */
    window.addEventListener('olobuild:saved', function(e) {
        const detail = e.detail || {};
        if (!detail.templateId) return;
        scheduleCapture(detail.templateId, { silent: true, templateType: detail.type });
    });

    /* API pubblica per cattura manuale (bottone "Rigenera thumbnail") */
    window.oloCaptureThumbnail = function(templateId, templateType) {
        return captureAndUpload(templateId, { silent: false, templateType: templateType });
    };

})();
