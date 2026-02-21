/**
 * Olo Lang — JavaScript frontend (selettore lingua dropdown)
 */
(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        // Gestione apertura/chiusura dropdown
        var dropdowns = document.querySelectorAll('.olo-lang-dropdown');

        dropdowns.forEach(function (dd) {
            var btn = dd.querySelector('.olo-lang-current');
            if (!btn) return;

            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                dd.classList.toggle('open');
            });
        });

        // Chiudi dropdown al click fuori
        document.addEventListener('click', function () {
            dropdowns.forEach(function (dd) {
                dd.classList.remove('open');
            });
        });

        // Chiudi con Escape
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                dropdowns.forEach(function (dd) {
                    dd.classList.remove('open');
                });
            }
        });
    });
})();
