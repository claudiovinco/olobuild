/**
 * Olobuild — lazy autoplay video.
 * I <video data-olo-lazyvid> arrivano dal server con preload="none" e senza autoplay:
 * vengono scaricati e avviati solo quando entrano (o stanno per entrare) nel viewport,
 * e messi in pausa quando ne escono. Risparmia decine di MB su pagine con molti video.
 */
(function () {
    'use strict';

    function init() {
        var vids = document.querySelectorAll('video[data-olo-lazyvid]');
        if (!vids.length) {
            return;
        }

        // Browser senza IntersectionObserver: ripristina il comportamento nativo.
        if (!('IntersectionObserver' in window)) {
            vids.forEach(function (v) {
                v.preload = 'auto';
                var p = v.play();
                if (p && p.catch) { p.catch(function () {}); }
            });
            return;
        }

        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (e) {
                var v = e.target;
                if (e.isIntersecting) {
                    if (v.preload === 'none') {
                        v.preload = 'auto';
                    }
                    if (v.dataset.oloAutoplay !== undefined && v.paused) {
                        var p = v.play();
                        if (p && p.catch) { p.catch(function () {}); }
                    }
                } else if (!v.paused) {
                    v.pause();
                }
            });
        }, { rootMargin: '200px', threshold: 0 });

        vids.forEach(function (v) { io.observe(v); });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
