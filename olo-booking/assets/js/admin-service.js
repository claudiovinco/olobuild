/**
 * Olo Booking — Service Meta Box JS (baita/alloggio)
 */
(function () {
    'use strict';

    // ══════════════════════════════════════
    // ── Seasons (Stagioni e Tariffe) ──
    // ══════════════════════════════════════

    var seasonsList = document.getElementById('olo-svc-seasons-list');
    var addSeasonBtn = document.getElementById('olo-svc-add-season');

    // Toggle collapse
    function bindSeasonRow(row) {
        var header = row.querySelector('.olo-svc-season-header');
        var toggleBtn = row.querySelector('.olo-svc-season-toggle');
        var removeBtn = row.querySelector('.olo-svc-remove-season');
        var nameInput = row.querySelector('.olo-svc-season-name-input');
        var namePreview = row.querySelector('.olo-svc-season-name-preview');

        // Toggle on header click (but not on buttons)
        if (header) {
            header.addEventListener('click', function (e) {
                if (e.target === removeBtn || e.target.closest('.olo-svc-remove-season')) return;
                row.classList.toggle('olo-svc-collapsed');
            });
        }

        // Remove season
        if (removeBtn) {
            removeBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                if (confirm('Rimuovere questa stagione?')) {
                    row.remove();
                    reindexSeasons();
                }
            });
        }

        // Sync name to preview
        if (nameInput && namePreview) {
            nameInput.addEventListener('input', function () {
                namePreview.textContent = nameInput.value || 'Nuova stagione';
            });
        }

        // Drag to reorder seasons
        var dragHandle = row.querySelector('.olo-svc-season-drag');
        if (dragHandle) {
            dragHandle.addEventListener('mousedown', function () {
                row.draggable = true;
            });
            row.addEventListener('dragstart', function (e) {
                row.classList.add('olo-svc-season-dragging');
                e.dataTransfer.effectAllowed = 'move';
                e.dataTransfer.setData('text/plain', '');
            });
            row.addEventListener('dragend', function () {
                row.draggable = false;
                row.classList.remove('olo-svc-season-dragging');
                reindexSeasons();
            });
            row.addEventListener('dragover', function (e) {
                e.preventDefault();
                var dragging = seasonsList.querySelector('.olo-svc-season-dragging');
                if (!dragging || dragging === row) return;
                var rect = row.getBoundingClientRect();
                var midY = rect.top + rect.height / 2;
                if (e.clientY < midY) {
                    seasonsList.insertBefore(dragging, row);
                } else {
                    seasonsList.insertBefore(dragging, row.nextSibling);
                }
            });
        }
    }

    function reindexSeasons() {
        if (!seasonsList) return;
        var rows = seasonsList.querySelectorAll('.olo-svc-season-row');
        rows.forEach(function (row, i) {
            row.setAttribute('data-index', i);
            row.querySelectorAll('[name]').forEach(function (el) {
                el.name = el.name.replace(/olo_seasons\[\d+\]/, 'olo_seasons[' + i + ']');
            });
        });
    }

    // Add new season
    if (addSeasonBtn && seasonsList) {
        addSeasonBtn.addEventListener('click', function () {
            var idx = seasonsList.querySelectorAll('.olo-svc-season-row').length;
            var prefix = 'olo_seasons[' + idx + ']';

            var div = document.createElement('div');
            div.className = 'olo-svc-season-row';
            div.setAttribute('data-index', idx);
            div.innerHTML =
                '<div class="olo-svc-season-header">' +
                    '<span class="olo-svc-season-drag" title="Trascina per riordinare">☰</span>' +
                    '<strong class="olo-svc-season-name-preview">Nuova stagione</strong>' +
                    '<span class="olo-svc-season-dates-preview"></span>' +
                    '<button type="button" class="button button-small olo-svc-season-toggle">▼</button>' +
                    '<button type="button" class="button button-small olo-svc-remove-season" title="Rimuovi stagione">&times;</button>' +
                '</div>' +
                '<div class="olo-svc-season-body">' +
                    '<div class="olo-svc-row">' +
                        '<div class="olo-svc-field">' +
                            '<label>Nome stagione</label>' +
                            '<input type="text" name="' + prefix + '[name]" value="" placeholder="es. Alta stagione" class="olo-svc-season-name-input" />' +
                        '</div>' +
                        '<div class="olo-svc-field">' +
                            '<label>Dal</label>' +
                            '<input type="date" name="' + prefix + '[date_from]" value="" />' +
                        '</div>' +
                        '<div class="olo-svc-field">' +
                            '<label>Al</label>' +
                            '<input type="date" name="' + prefix + '[date_to]" value="" />' +
                        '</div>' +
                    '</div>' +
                    '<div class="olo-svc-row">' +
                        '<div class="olo-svc-field">' +
                            '<label>Prezzo a notte (€)</label>' +
                            '<input type="text" name="' + prefix + '[price_night]" value="" placeholder="es. 150.00" />' +
                            '<p class="description">Lascia vuoto per usare il prezzo base.</p>' +
                        '</div>' +
                        '<div class="olo-svc-field">' +
                            '<label>Soggiorno minimo (notti)</label>' +
                            '<input type="number" name="' + prefix + '[min_nights]" value="1" min="1" max="30" />' +
                        '</div>' +
                        '<div class="olo-svc-field">' +
                            '<label>Solo settimane intere</label>' +
                            '<label class="olo-svc-checkbox-label">' +
                                '<input type="checkbox" name="' + prefix + '[week_only]" value="1" />' +
                                'Obbligatorio soggiorno di 7 notti (sabato-sabato o giorno configurato)' +
                            '</label>' +
                        '</div>' +
                    '</div>' +
                    '<div class="olo-svc-row">' +
                        '<div class="olo-svc-field" style="flex:1">' +
                            '<label>Note (opzionale)</label>' +
                            '<input type="text" name="' + prefix + '[note]" value="" placeholder="es. Capodanno minimo 3 notti" style="width:100%" />' +
                        '</div>' +
                    '</div>' +
                '</div>';

            seasonsList.appendChild(div);
            bindSeasonRow(div);
        });
    }

    // Bind existing season rows
    if (seasonsList) {
        seasonsList.querySelectorAll('.olo-svc-season-row').forEach(bindSeasonRow);
    }

    // ══════════════════════════════════════
    // ── Closures (Chiusure e Blocchi) ──
    // ══════════════════════════════════════

    var closuresList = document.getElementById('olo-svc-closures-list');
    var addClosureBtn = document.getElementById('olo-svc-add-closure');

    function bindClosureRow(row) {
        var removeBtn = row.querySelector('.olo-svc-remove-closure');
        if (removeBtn) {
            removeBtn.addEventListener('click', function () {
                row.remove();
                reindexClosures();
            });
        }
    }

    function reindexClosures() {
        if (!closuresList) return;
        var rows = closuresList.querySelectorAll('.olo-svc-closure-row');
        rows.forEach(function (row, i) {
            row.querySelectorAll('[name]').forEach(function (el) {
                el.name = el.name.replace(/olo_closures\[\d+\]/, 'olo_closures[' + i + ']');
            });
        });
    }

    if (addClosureBtn && closuresList) {
        addClosureBtn.addEventListener('click', function () {
            var idx = closuresList.querySelectorAll('.olo-svc-closure-row').length;
            var prefix = 'olo_closures[' + idx + ']';

            var div = document.createElement('div');
            div.className = 'olo-svc-closure-row';
            div.innerHTML =
                '<input type="date" name="' + prefix + '[date_from]" value="" />' +
                '<span>→</span>' +
                '<input type="date" name="' + prefix + '[date_to]" value="" />' +
                '<input type="text" name="' + prefix + '[reason]" placeholder="Motivo (opzionale)" style="flex:1" />' +
                '<button type="button" class="button button-small olo-svc-remove-closure" title="Rimuovi">&times;</button>';

            closuresList.appendChild(div);
            bindClosureRow(div);
        });
    }

    if (closuresList) {
        closuresList.querySelectorAll('.olo-svc-closure-row').forEach(bindClosureRow);
    }

    // ══════════════════════════════════════
    // ── Gallery – Media Picker ──
    // ══════════════════════════════════════

    var galGrid = document.getElementById('olo-svc-gallery-grid');
    var galAddBtn = document.getElementById('olo-svc-gallery-add');

    if (galAddBtn && galGrid && typeof wp !== 'undefined' && wp.media) {
        var galFrame = null;

        galAddBtn.addEventListener('click', function () {
            if (galFrame) {
                galFrame.open();
                return;
            }

            galFrame = wp.media({
                title: 'Seleziona foto per la galleria',
                button: { text: 'Aggiungi alla galleria' },
                library: { type: 'image' },
                multiple: true,
            });

            galFrame.on('select', function () {
                var selection = galFrame.state().get('selection');
                selection.each(function (attachment) {
                    var data = attachment.toJSON();
                    if (galGrid.querySelector('[data-id="' + data.id + '"]')) return;

                    var thumb = (data.sizes && data.sizes.thumbnail)
                        ? data.sizes.thumbnail.url
                        : data.url;

                    var item = document.createElement('div');
                    item.className = 'olo-svc-gallery-item';
                    item.setAttribute('data-id', data.id);
                    item.draggable = true;
                    item.innerHTML =
                        '<img src="' + thumb + '" alt="" />' +
                        '<button type="button" class="olo-svc-gallery-remove" title="Rimuovi">&times;</button>' +
                        '<input type="hidden" name="olo_service_gallery[]" value="' + data.id + '" />';

                    galGrid.appendChild(item);
                    bindGalleryItem(item);
                });
            });

            galFrame.open();
        });

        galGrid.querySelectorAll('.olo-svc-gallery-item').forEach(bindGalleryItem);
    }

    function bindGalleryItem(item) {
        var removeBtn = item.querySelector('.olo-svc-gallery-remove');
        if (removeBtn) {
            removeBtn.addEventListener('click', function (e) {
                e.preventDefault();
                item.remove();
            });
        }

        item.draggable = true;

        item.addEventListener('dragstart', function (e) {
            item.classList.add('olo-svc-dragging');
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/plain', item.getAttribute('data-id'));
        });

        item.addEventListener('dragend', function () {
            item.classList.remove('olo-svc-dragging');
        });

        item.addEventListener('dragover', function (e) {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';

            var dragging = galGrid.querySelector('.olo-svc-dragging');
            if (!dragging || dragging === item) return;

            var rect = item.getBoundingClientRect();
            var midX = rect.left + rect.width / 2;
            if (e.clientX < midX) {
                galGrid.insertBefore(dragging, item);
            } else {
                galGrid.insertBefore(dragging, item.nextSibling);
            }
        });
    }
})();
