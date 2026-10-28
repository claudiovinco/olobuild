/**
 * Campi «Solo nelle categorie» nella scheda di modifica (Olobuild_Tipi_Contenuto::campo_html): il campo si vede solo
 * quando il contenuto sta in una delle sue categorie (o in una figlia, già comprese in data-categorie). Legge le
 * categorie dall'editor a blocchi (wp.data) o dalle caselle dell'editor classico; se non le trova lascia il campo come
 * l'ha disegnato il PHP. Il valore di un campo nascosto resta salvato.
 */
(function () {
    'use strict';
    var campi = Array.prototype.slice.call(document.querySelectorAll('.olo-tc-campo[data-categorie]'));
    if (!campi.length) {
        return;
    }
    var tassonomia = campi[0].getAttribute('data-tassonomia');
    var rest = campi[0].getAttribute('data-rest');
    var nomeCasella = tassonomia === 'category' ? 'post_category[]' : 'tax_input[' + tassonomia + '][]';

    function dalBlocchi() {
        var ed = window.wp && window.wp.data && window.wp.data.select ? window.wp.data.select('core/editor') : null;
        if (!ed || !ed.getEditedPostAttribute) {
            return null;
        }
        var v = ed.getEditedPostAttribute(rest);
        return Array.isArray(v) ? v.map(Number) : null;
    }

    function dalClassico() {
        var caselle = document.querySelectorAll('input[name="' + nomeCasella + '"]');
        if (!caselle.length) {
            return null;
        }
        var ids = [];
        Array.prototype.forEach.call(caselle, function (c) {
            if (c.checked) {
                ids.push(parseInt(c.value, 10));
            }
        });
        return ids;
    }

    function aggiorna() {
        var scelte = dalBlocchi();
        if (scelte === null) {
            scelte = dalClassico();
        }
        if (scelte === null) {
            return;
        }
        campi.forEach(function (campo) {
            var ammesse = campo.getAttribute('data-categorie').split(',').map(Number);
            campo.hidden = !scelte.some(function (id) {
                return ammesse.indexOf(id) !== -1;
            });
        });
    }

    document.addEventListener('change', function (e) {
        if (e.target && e.target.name === nomeCasella) {
            aggiorna();
        }
    });
    if (window.wp && window.wp.data && window.wp.data.subscribe) {
        var prima = '';
        window.wp.data.subscribe(function () {
            var s = dalBlocchi();
            var chiave = s ? s.join(',') : '';
            if (chiave !== prima) {
                prima = chiave;
                aggiorna();
            }
        });
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', aggiorna);
    } else {
        aggiorna();
    }
})();
