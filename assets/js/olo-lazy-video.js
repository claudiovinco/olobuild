/**
 * Olobuild — lazy autoplay video.
 * I <video data-olo-lazyvid> arrivano dal server con preload="none" e senza autoplay:
 * vengono scaricati e avviati solo quando entrano (o stanno per entrare) nel viewport,
 * e messi in pausa quando ne escono. Risparmia decine di MB su pagine con molti video.
 * Un MutationObserver aggancia anche i video stampati DOPO il load (es. tile
 * lazy-render: template.olo-lazy-content idratati allo scroll).
 */
(function () {
    'use strict';

    function init() {
        // Browser senza IntersectionObserver: ripristina il comportamento nativo.
        if (!('IntersectionObserver' in window)) {
            document.querySelectorAll('video[data-olo-lazyvid]').forEach(function (v) {
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

        function observeIn(scope) {
            scope.querySelectorAll('video[data-olo-lazyvid]').forEach(function (v) {
                io.observe(v); // observe() su target già osservato è un no-op
            });
        }

        observeIn(document);

        // Video introdotti dopo il load (lazy-render dei template, contenuti dinamici)
        if ('MutationObserver' in window && document.body) {
            new MutationObserver(function (mutations) {
                for (var i = 0; i < mutations.length; i++) {
                    var added = mutations[i].addedNodes;
                    for (var j = 0; j < added.length; j++) {
                        var n = added[j];
                        if (n.nodeType !== 1) { continue; }
                        if (n.matches && n.matches('video[data-olo-lazyvid]')) {
                            io.observe(n);
                        } else if (n.querySelectorAll) {
                            observeIn(n);
                        }
                    }
                }
            }).observe(document.body, { childList: true, subtree: true });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
