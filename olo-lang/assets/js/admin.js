/**
 * Olo Lang — JavaScript admin
 * Gestisce salvataggio traduzioni e impostazioni via REST API.
 */
(function ($) {
    'use strict';

    var restUrl = oloLangData.restUrl;
    var nonce   = oloLangData.nonce;

    // =========================================================================
    // Utilita'
    // =========================================================================

    function apiCall(method, endpoint, data) {
        return $.ajax({
            url: restUrl + endpoint,
            method: method,
            contentType: 'application/json',
            data: data ? JSON.stringify(data) : undefined,
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-WP-Nonce', nonce);
            }
        });
    }

    function showToast(msg, type) {
        var $toast = $('<div class="olo-lang-toast ' + (type || '') + '">' + msg + '</div>');
        $('body').append($toast);
        setTimeout(function () { $toast.addClass('show'); }, 10);
        setTimeout(function () {
            $toast.removeClass('show');
            setTimeout(function () { $toast.remove(); }, 300);
        }, 3000);
    }

    // =========================================================================
    // Editor traduzioni
    // =========================================================================

    function initTranslationEditor() {
        var $form = $('#olo-lang-editor-form');
        if (!$form.length) return;

        var templateId = $form.data('template-id');
        var lang       = $form.data('lang');

        // Segna input modificati
        $form.on('input change', '.olo-lang-input', function () {
            $(this).addClass('olo-lang-dirty');
        });

        // Salvataggio
        function saveAll() {
            var translations = [];

            $form.find('.olo-lang-row').each(function () {
                var $row    = $(this);
                var tileId  = $row.data('tile-id');
                var fieldPath = $row.data('field-path');
                var original  = $row.find('.olo-lang-original-value').val();
                var $input    = $row.find('.olo-lang-input');
                var trans     = $input.val().trim();

                if (trans) {
                    translations.push({
                        tile_id:    tileId,
                        field_path: fieldPath,
                        original:   original,
                        translation: trans,
                        status:     'tradotto'
                    });
                }
            });

            if (!translations.length) {
                showToast('Nessuna traduzione da salvare', 'error');
                return;
            }

            var $btns = $('.olo-lang-save-btn').addClass('saving').text('Salvataggio...');

            apiCall('PUT', 'translations/' + templateId + '/' + lang, {
                translations: translations
            }).done(function (res) {
                showToast('Salvate ' + res.saved + ' traduzioni', 'success');

                // Aggiorna stato visivo
                $form.find('.olo-lang-row').each(function () {
                    var $row   = $(this);
                    var $input = $row.find('.olo-lang-input');
                    if ($input.val().trim()) {
                        $row.removeClass('olo-lang-row--outdated').addClass('olo-lang-row--done');
                        $row.find('.olo-lang-status').remove();
                        $row.find('.olo-lang-row-meta').append(
                            '<span class="olo-lang-status olo-lang-status--done">tradotto</span>'
                        );
                    }
                    $input.removeClass('olo-lang-dirty');
                });

                // Aggiorna barra progresso
                var total = $form.find('.olo-lang-row').length;
                var done  = $form.find('.olo-lang-row--done').length;
                var pct   = total > 0 ? Math.round((done / total) * 100) : 0;
                $('.olo-lang-progress-fill').css('width', pct + '%');
                $('#olo-lang-progress-text').text(done + '/' + total + ' (' + pct + '%)');

            }).fail(function () {
                showToast('Errore nel salvataggio', 'error');
            }).always(function () {
                $btns.removeClass('saving').text('Salva tutte le traduzioni');
            });
        }

        $(document).on('click', '#olo-lang-save-all, #olo-lang-save-bottom', saveAll);

        // Ctrl+S per salvare
        $(document).on('keydown', function (e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                e.preventDefault();
                saveAll();
            }
        });
    }

    // =========================================================================
    // Pagina impostazioni
    // =========================================================================

    function initSettings() {
        var $form = $('#olo-lang-settings-form');
        if (!$form.length) return;

        // Toggle visibilita' opzioni switcher
        $('#olo-lang-show-switcher').on('change', function () {
            $('#olo-lang-switcher-options').toggle(this.checked);
        }).trigger('change');

        // Aggiungi lingua
        $('#olo-lang-add-btn').on('click', function () {
            var $sel  = $('#olo-lang-add-select');
            var code  = $sel.val();
            if (!code) return;

            var $opt   = $sel.find('option:selected');
            var name   = $opt.data('name');
            var locale = $opt.data('locale');

            // Aggiungi riga alla tabella
            var html = '<tr data-code="' + code + '">' +
                '<td><strong>' + code.toUpperCase() + '</strong></td>' +
                '<td>' + name + '</td>' +
                '<td><input type="checkbox" class="olo-lang-active-cb" checked></td>' +
                '<td><button type="button" class="button button-small olo-lang-remove-btn">Rimuovi</button></td>' +
                '</tr>';

            $('#olo-lang-languages-table tbody').append(html);
            $opt.remove();

            // Aggiorna anche il select lingua predefinita
            $('#olo-lang-default').append(
                '<option value="' + code + '">' + name + ' (' + code.toUpperCase() + ')</option>'
            );
        });

        // Rimuovi lingua
        $(document).on('click', '.olo-lang-remove-btn', function () {
            var $tr   = $(this).closest('tr');
            var code  = $tr.data('code');
            var name  = $tr.find('td:eq(1)').text();

            if (!confirm('Rimuovere la lingua ' + name + '?\nLe traduzioni esistenti NON verranno eliminate.')) {
                return;
            }

            $tr.remove();
            $('#olo-lang-default option[value="' + code + '"]').remove();
        });

        // Salva impostazioni
        $('#olo-lang-save-settings').on('click', function () {
            var languages = [];
            $('#olo-lang-languages-table tbody tr').each(function () {
                var $tr = $(this);
                var code = $tr.data('code');
                // Trova dati completi nel catalogo
                var found = null;
                $.each(oloLangData.availableLanguages, function (i, l) {
                    if (l.code === code) { found = l; return false; }
                });
                languages.push({
                    code:   code,
                    name:   found ? found.name : code,
                    locale: found ? found.locale : code,
                    active: $tr.find('.olo-lang-active-cb').is(':checked')
                });
            });

            var config = {
                default_lang:      $('#olo-lang-default').val(),
                languages:         languages,
                url_mode:          $('#olo-lang-url-mode').val(),
                show_switcher:     $('#olo-lang-show-switcher').is(':checked'),
                switcher_style:    $('#olo-lang-switcher-style').val(),
                switcher_position: $('#olo-lang-switcher-position').val()
            };

            var $btn = $(this).prop('disabled', true).text('Salvataggio...');
            var $fb  = $('#olo-lang-save-feedback');

            apiCall('PUT', 'config', config).done(function () {
                $fb.text('Salvato!').removeClass('error').addClass('success');
                showToast('Impostazioni salvate', 'success');
                oloLangData.config = config;
            }).fail(function () {
                $fb.text('Errore').removeClass('success').addClass('error');
                showToast('Errore nel salvataggio', 'error');
            }).always(function () {
                $btn.prop('disabled', false).text('Salva impostazioni');
                setTimeout(function () { $fb.text(''); }, 3000);
            });
        });
    }

    // =========================================================================
    // Stringhe Globali
    // =========================================================================

    function initGlobalStrings() {
        var $app = $('#olo-gs-app');
        if (!$app.length) return;

        var currentTab  = 'wp-menu';
        var stringsData = []; // dati correnti dal server

        // --- Tab switching ---
        $app.on('click', '.olo-gs-tab', function () {
            var tab = $(this).data('tab');
            if (tab === currentTab) return;

            $('.olo-gs-tab').removeClass('olo-gs-tab--active');
            $(this).addClass('olo-gs-tab--active');
            $('.olo-gs-panel').hide();
            $('#olo-gs-panel-' + tab).show();

            currentTab = tab;
            loadStrings();
        });

        // --- Load stringhe ---
        function loadStrings() {
            var lang   = $('#olo-gs-lang').val();
            var tileId = getTileIdForTab(currentTab);

            apiCall('GET', 'global-strings/' + lang + '?tile_id=' + encodeURIComponent(tileId))
                .done(function (res) {
                    stringsData = res.strings || [];
                    renderTable();
                    updateCounter();
                })
                .fail(function () {
                    showToast('Errore nel caricamento', 'error');
                });
        }

        function getTileIdForTab(tab) {
            if (tab === 'wp-menu') return 'wp-menu';
            if (tab === 'plugin') return 'plugin-olo-booking';
            if (tab === 'custom') return 'custom';
            return '';
        }

        // --- Render tabella ---
        function renderTable() {
            var $tbody = $('#olo-gs-table-' + currentTab + ' tbody');
            $tbody.empty();

            var search = ($('#olo-gs-search').val() || '').toLowerCase();

            var filtered = stringsData;
            if (search) {
                filtered = stringsData.filter(function (s) {
                    return s.original.toLowerCase().indexOf(search) !== -1 ||
                           (s.translation || '').toLowerCase().indexOf(search) !== -1;
                });
            }

            if (!filtered.length) {
                $tbody.append('<tr><td colspan="4" class="olo-gs-empty">Nessuna stringa trovata. Usa "Scansiona" per popolare.</td></tr>');
                return;
            }

            filtered.forEach(function (s) {
                var statusClass = s.status === 'tradotto' ? 'olo-gs-badge--tradotto' :
                                  s.status === 'obsoleta' ? 'olo-gs-badge--obsoleta' : 'olo-gs-badge--bozza';
                var statusLabel = s.status === 'tradotto' ? 'Tradotto' :
                                  s.status === 'obsoleta' ? 'Obsoleta' : 'Bozza';

                var row = '<tr data-id="' + s.id + '" data-tile-id="' + escHtml(s.tile_id) + '" data-field-path="' + escHtml(s.field_path) + '">' +
                    '<td class="olo-gs-col-original">' + escHtml(s.original) + '</td>' +
                    '<td class="olo-gs-col-translation"><input type="text" class="olo-gs-input" value="' + escAttr(s.translation || '') + '" placeholder="Traduzione..."></td>' +
                    '<td class="olo-gs-col-status"><span class="olo-gs-badge ' + statusClass + '">' + statusLabel + '</span></td>' +
                    '<td class="olo-gs-col-actions"><button type="button" class="button button-small olo-gs-delete" title="Elimina">&times;</button></td>' +
                    '</tr>';
                $tbody.append(row);
            });
        }

        function updateCounter() {
            var total    = stringsData.length;
            var tradotte = stringsData.filter(function (s) { return s.status === 'tradotto'; }).length;
            $('#olo-gs-counter').text(tradotte + '/' + total + ' tradotte');
        }

        // --- Cambio lingua ---
        $('#olo-gs-lang').on('change', function () {
            loadStrings();
        });

        // --- Ricerca live (debounce) ---
        var searchTimer;
        $('#olo-gs-search').on('keyup', function () {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(function () {
                renderTable();
            }, 250);
        });

        // --- Scansiona menu ---
        $('#olo-gs-scan-menus').on('click', function () {
            var $btn = $(this).prop('disabled', true).text('Scansione...');
            apiCall('POST', 'scan/menus/populate')
                .done(function (res) {
                    showToast('Popolate ' + res.populated + ' stringhe menu', 'success');
                    $('#olo-gs-menu-info').text(res.populated + ' stringhe trovate');
                    loadStrings();
                })
                .fail(function () {
                    showToast('Errore scansione menu', 'error');
                })
                .always(function () {
                    $btn.prop('disabled', false).html('<span class="dashicons dashicons-search"></span> Scansiona Menu');
                });
        });

        // --- Scansiona plugin ---
        $('#olo-gs-scan-plugins').on('click', function () {
            var $btn = $(this).prop('disabled', true).text('Scansione...');
            apiCall('POST', 'scan/plugins/populate')
                .done(function (res) {
                    showToast('Popolate ' + res.populated + ' stringhe plugin', 'success');
                    $('#olo-gs-plugin-info').text(res.populated + ' stringhe trovate');
                    loadStrings();
                })
                .fail(function () {
                    showToast('Errore scansione plugin', 'error');
                })
                .always(function () {
                    $btn.prop('disabled', false).html('<span class="dashicons dashicons-search"></span> Scansiona Plugin');
                });
        });

        // --- Aggiungi stringa custom ---
        $('#olo-gs-add-custom').on('click', function () {
            var original    = $('#olo-gs-custom-original').val().trim();
            var translation = $('#olo-gs-custom-translation').val().trim();
            var lang        = $('#olo-gs-lang').val();

            if (!original) {
                showToast('Inserisci la stringa originale', 'error');
                return;
            }

            apiCall('POST', 'global-strings/custom', {
                original:    original,
                translation: translation,
                lang:        lang
            }).done(function () {
                showToast('Stringa aggiunta', 'success');
                $('#olo-gs-custom-original').val('');
                $('#olo-gs-custom-translation').val('');
                loadStrings();
            }).fail(function () {
                showToast('Errore aggiunta stringa', 'error');
            });
        });

        // --- Elimina stringa ---
        $app.on('click', '.olo-gs-delete', function () {
            if (!confirm('Eliminare questa stringa?')) return;

            var $tr = $(this).closest('tr');
            var id  = $tr.data('id');

            apiCall('DELETE', 'global-strings/' + id)
                .done(function () {
                    $tr.fadeOut(200, function () { $(this).remove(); });
                    stringsData = stringsData.filter(function (s) { return parseInt(s.id) !== parseInt(id); });
                    updateCounter();
                    showToast('Stringa eliminata', 'success');
                })
                .fail(function () {
                    showToast('Errore eliminazione', 'error');
                });
        });

        // --- Salvataggio bulk ---
        function saveAll() {
            var lang         = $('#olo-gs-lang').val();
            var translations = [];

            $('#olo-gs-table-' + currentTab + ' tbody tr').each(function () {
                var $tr     = $(this);
                var tileId  = $tr.data('tile-id');
                var fp      = $tr.data('field-path');
                var orig    = $tr.find('.olo-gs-col-original').text().trim();
                var trans   = $tr.find('.olo-gs-input').val().trim();

                if (!tileId) return; // skip empty rows

                translations.push({
                    tile_id:     tileId,
                    field_path:  fp,
                    original:    orig,
                    translation: trans,
                    status:      trans ? 'tradotto' : 'bozza'
                });
            });

            if (!translations.length) {
                showToast('Nessuna stringa da salvare', 'error');
                return;
            }

            var $btn = $('#olo-gs-save').prop('disabled', true).text('Salvataggio...');

            apiCall('PUT', 'global-strings/' + lang, { translations: translations })
                .done(function (res) {
                    showToast('Salvate ' + res.saved + ' stringhe', 'success');
                    loadStrings();
                })
                .fail(function () {
                    showToast('Errore nel salvataggio', 'error');
                })
                .always(function () {
                    $btn.prop('disabled', false).text('Salva (Ctrl+S)');
                });
        }

        $('#olo-gs-save').on('click', saveAll);

        // Ctrl+S
        $(document).on('keydown', function (e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                e.preventDefault();
                saveAll();
            }
        });

        // --- Utility ---
        function escHtml(str) {
            if (!str) return '';
            return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }
        function escAttr(str) {
            return escHtml(str);
        }

        // Load iniziale
        loadStrings();
    }

    // =========================================================================
    // Init
    // =========================================================================

    $(function () {
        initTranslationEditor();
        initSettings();
        initGlobalStrings();
    });

})(jQuery);
