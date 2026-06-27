/**
 * OloBuild Converter — Admin Page JavaScript
 * Handles tab switching, file upload, AJAX conversion, and result display.
 */
(function () {
    'use strict';

    // State per-tab.
    const state = {};

    function getTabState(builder) {
        if (!state[builder]) {
            state[builder] = { fileData: null, fileName: '', mode: 'file', template: null };
        }
        return state[builder];
    }

    // ─── Tab switching ───

    document.querySelectorAll('.olo-converter-tabs .nav-tab').forEach(tab => {
        tab.addEventListener('click', e => {
            e.preventDefault();
            const target = tab.dataset.tab;
            document.querySelectorAll('.nav-tab').forEach(t => t.classList.remove('nav-tab-active'));
            document.querySelectorAll('.olo-tab-panel').forEach(p => p.classList.remove('olo-tab-active'));
            tab.classList.add('nav-tab-active');
            document.getElementById('olo-tab-' + target).classList.add('olo-tab-active');
        });
    });

    // ─── Mode switching ───

    document.querySelectorAll('.olo-mode-radio').forEach(radio => {
        radio.addEventListener('change', function () {
            const panel = this.closest('.olo-tab-panel');
            const builder = panel.dataset.builder;
            const mode = this.value;

            getTabState(builder).mode = mode;

            panel.querySelectorAll('.olo-mode-section').forEach(sec => {
                sec.style.display = sec.dataset.mode === mode ? '' : 'none';
            });

            // Load pages if DB mode and not already loaded.
            if (mode === 'db') {
                loadPages(panel, builder);
            }

            updateConvertButton(panel, builder);
        });
    });

    // ─── File upload ───

    document.querySelectorAll('.olo-tab-panel').forEach(panel => {
        const builder = panel.dataset.builder;
        const dropzone = panel.querySelector('.olo-dropzone');
        const fileInput = panel.querySelector('.olo-file-input');
        const browseBtn = panel.querySelector('.olo-browse-btn');
        const fileInfo = panel.querySelector('.olo-file-info');
        const fileName = panel.querySelector('.olo-file-name');
        const fileSize = panel.querySelector('.olo-file-size');
        const removeBtn = panel.querySelector('.olo-file-remove');

        if (!dropzone) return;

        browseBtn.addEventListener('click', () => fileInput.click());

        fileInput.addEventListener('change', () => {
            if (fileInput.files[0]) handleFile(fileInput.files[0]);
        });

        // Drag and drop.
        dropzone.addEventListener('dragover', e => {
            e.preventDefault();
            dropzone.classList.add('olo-drag-over');
        });
        dropzone.addEventListener('dragleave', () => {
            dropzone.classList.remove('olo-drag-over');
        });
        dropzone.addEventListener('drop', e => {
            e.preventDefault();
            dropzone.classList.remove('olo-drag-over');
            if (e.dataTransfer.files[0]) handleFile(e.dataTransfer.files[0]);
        });

        removeBtn.addEventListener('click', () => {
            getTabState(builder).fileData = null;
            getTabState(builder).fileName = '';
            fileInput.value = '';
            fileInfo.style.display = 'none';
            updateConvertButton(panel, builder);
        });

        function handleFile(file) {
            if (!file.name.endsWith('.json')) {
                alert('Solo file .json sono supportati.');
                return;
            }
            if (file.size > 10 * 1024 * 1024) {
                alert('File troppo grande (max 10MB).');
                return;
            }

            const reader = new FileReader();
            reader.onload = function (e) {
                const s = getTabState(builder);
                s.fileData = e.target.result;
                s.fileName = file.name;
                fileName.textContent = file.name;
                fileSize.textContent = formatSize(file.size);
                fileInfo.style.display = '';
                updateConvertButton(panel, builder);
            };
            reader.readAsText(file);
        }
    });

    // ─── DB page loading ───

    function loadPages(panel, builder) {
        const select = panel.querySelector('.olo-page-select');
        if (select.dataset.loaded === 'true') return;

        select.innerHTML = '<option value="">— Caricamento... —</option>';

        const fd = new FormData();
        fd.append('action', 'olo_converter_list_pages');
        fd.append('nonce', oloConverter.nonce);
        fd.append('builder', builder);

        fetch(oloConverter.ajaxUrl, { method: 'POST', body: fd })
            .then(r => r.json())
            .then(res => {
                select.innerHTML = '<option value="">— Seleziona pagina —</option>';
                if (res.success && res.data) {
                    Object.entries(res.data).forEach(([id, title]) => {
                        const opt = document.createElement('option');
                        opt.value = id;
                        opt.textContent = title;
                        select.appendChild(opt);
                    });
                    select.dataset.loaded = 'true';
                } else {
                    select.innerHTML = '<option value="">Nessuna pagina trovata</option>';
                }
            })
            .catch(() => {
                select.innerHTML = '<option value="">Errore nel caricamento</option>';
            });

        select.addEventListener('change', () => updateConvertButton(panel, builder));
    }

    // ─── Convert button state ───

    function updateConvertButton(panel, builder) {
        const btn = panel.querySelector('.olo-convert-btn');
        const s = getTabState(builder);

        if (s.mode === 'file') {
            btn.disabled = !s.fileData;
        } else {
            const select = panel.querySelector('.olo-page-select');
            btn.disabled = !select || !select.value;
        }
    }

    // ─── Convert action ───

    document.querySelectorAll('.olo-convert-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const panel = this.closest('.olo-tab-panel');
            const builder = panel.dataset.builder;
            const s = getTabState(builder);

            const progress = panel.querySelector('.olo-progress');
            const results = panel.querySelector('.olo-results');

            btn.disabled = true;
            progress.style.display = '';
            results.style.display = 'none';

            const fd = new FormData();
            fd.append('action', 'olo_converter_convert');
            fd.append('nonce', oloConverter.nonce);
            fd.append('builder', builder);
            fd.append('mode', s.mode);

            if (s.mode === 'file') {
                fd.append('file_content', s.fileData);
            } else {
                fd.append('post_id', panel.querySelector('.olo-page-select').value);
            }

            fetch(oloConverter.ajaxUrl, { method: 'POST', body: fd })
                .then(r => r.json())
                .then(res => {
                    progress.style.display = 'none';
                    btn.disabled = false;

                    if (res.success) {
                        s.template = res.data.template;
                        showResults(panel, res.data);
                    } else {
                        alert('Errore: ' + (res.data || 'Conversione fallita'));
                    }
                })
                .catch(err => {
                    progress.style.display = 'none';
                    btn.disabled = false;
                    alert('Errore di rete: ' + err.message);
                });
        });
    });

    // ─── Show results ───

    function showResults(panel, data) {
        const results = panel.querySelector('.olo-results');
        const report = data.report;
        const summary = report.summary;

        // Message.
        panel.querySelector('.olo-result-message').textContent =
            `Conversione completata: ${summary.total} elementi processati da ${report.source_builder}.`;

        // Summary counts.
        panel.querySelector('.olo-count-converted').textContent = summary.converted;
        panel.querySelector('.olo-count-approximated').textContent = summary.approximated;
        panel.querySelector('.olo-count-fallback').textContent = summary.fallback_html;
        panel.querySelector('.olo-count-skipped').textContent = summary.skipped;
        panel.querySelector('.olo-count-warnings').textContent = summary.warnings;

        // Detail items.
        const tbody = panel.querySelector('.olo-report-items tbody');
        tbody.innerHTML = '';

        const statusLabels = {
            converted: 'Convertito',
            approximated: 'Approssimato',
            fallback_html: 'Fallback HTML',
            skipped: 'Saltato',
        };

        report.items.forEach(item => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${esc(item.source_type)}</td>
                <td>${item.olo_type ? esc(item.olo_type) : '—'}</td>
                <td><span class="olo-status-${item.status}">${statusLabels[item.status] || item.status}</span></td>
                <td>${esc(item.details || item.message)}</td>
            `;
            tbody.appendChild(tr);
        });

        // Warnings.
        if (report.warnings.length) {
            report.warnings.forEach(w => {
                const tr = document.createElement('tr');
                tr.innerHTML = `<td colspan="4" style="color:#dc2626">⚠ ${esc(w)}</td>`;
                tbody.appendChild(tr);
            });
        }

        results.style.display = '';
    }

    // ─── Import to OloBuild ───

    document.querySelectorAll('.olo-import-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const panel = this.closest('.olo-tab-panel');
            const builder = panel.dataset.builder;
            const s = getTabState(builder);

            if (!s.template) {
                alert('Nessun template convertito da importare.');
                return;
            }

            btn.disabled = true;
            btn.textContent = 'Importazione...';

            const fd = new FormData();
            fd.append('action', 'olo_converter_import');
            fd.append('nonce', oloConverter.nonce);
            fd.append('template', JSON.stringify(s.template));

            fetch(oloConverter.ajaxUrl, { method: 'POST', body: fd })
                .then(r => r.json())
                .then(res => {
                    btn.disabled = false;
                    btn.textContent = 'Importa in OloBuild';

                    if (res.success) {
                        const msg = res.data.message || 'Template importato!';
                        const editUrl = res.data.edit_url;
                        if (editUrl && confirm(msg + '\n\nAprire il template in OloBuild?')) {
                            window.location.href = editUrl;
                        } else {
                            alert(msg);
                        }
                    } else {
                        alert('Errore: ' + (res.data || 'Import fallito'));
                    }
                })
                .catch(err => {
                    btn.disabled = false;
                    btn.textContent = 'Importa in OloBuild';
                    alert('Errore di rete: ' + err.message);
                });
        });
    });

    // ─── Download JSON ───

    document.querySelectorAll('.olo-download-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const panel = this.closest('.olo-tab-panel');
            const builder = panel.dataset.builder;
            const s = getTabState(builder);

            if (!s.template) {
                alert('Nessun template da scaricare.');
                return;
            }

            const blob = new Blob([JSON.stringify(s.template, null, 2)], { type: 'application/json' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `olobuild-${builder}-${Date.now()}.json`;
            a.click();
            URL.revokeObjectURL(url);
        });
    });

    // ─── Utilities ───

    function formatSize(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
    }

    function esc(str) {
        const div = document.createElement('div');
        div.textContent = str || '';
        return div.innerHTML;
    }
})();
