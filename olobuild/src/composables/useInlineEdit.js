/**
 * useInlineEdit — Inline editing composable per Olobuild.
 *
 * Consente di editare il testo delle tile direttamente nel canvas.
 *
 * Supporta:
 * - Campi plain text (singola riga e multilinea)
 * - Campi rich text (data-olo-richtext) con floating toolbar
 * - Campi array (panels.0.title pattern) per Accordion, Timeline, etc.
 * - Escape = annulla, blur = salva, Enter/Ctrl+Enter = salva
 * - Integrazione automatica con undo/redo (via tilesStore.updateTile)
 * - Protezione campi con dynamic binding attivo
 * - Protezione durante preview mode
 */
import { onMounted, onUnmounted } from 'vue';
import { useBuilderStore } from '@/stores/builder';
import { useTilesStore } from '@/stores/tiles';

export function useInlineEdit(canvasEl) {
  const builderStore = useBuilderStore();
  const tilesStore = useTilesStore();

  // Stato interno (non reattivo — non serve re-render Vue)
  let activeElement = null;
  let originalValue = '';
  let activeTileId = null;
  let activeField = null;
  let isCommitting = false;
  let isRichtext = false;
  let toolbarClicking = false;

  // ═══════════════════════════════════════════════
  //  Floating Toolbar (singleton raw DOM)
  // ═══════════════════════════════════════════════
  let toolbar = null;

  function getToolbar() {
    if (toolbar) return toolbar;

    var el = document.createElement('div');
    el.className = 'olo-inline-toolbar';
    el.innerHTML = [
      '<button type="button" data-cmd="bold" title="Grassetto (Ctrl+B)"><strong>B</strong></button>',
      '<button type="button" data-cmd="italic" title="Corsivo (Ctrl+I)"><em>I</em></button>',
      '<button type="button" data-cmd="underline" title="Sottolineato (Ctrl+U)"><u>U</u></button>',
      '<button type="button" data-cmd="strikeThrough" title="Barrato"><s>S</s></button>',
      '<span class="olo-it-sep"></span>',
      // Colore testo
      '<label class="olo-it-color" title="Colore testo">',
      '  <span class="olo-it-color-a">A</span>',
      '  <input type="color" value="#ffffff" class="olo-it-color-input" data-color-cmd="foreColor" />',
      '</label>',
      '<span class="olo-it-sep"></span>',
      // Link
      '<button type="button" data-cmd="createLink" title="Inserisci link">',
      '  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>',
      '</button>',
      '<button type="button" data-cmd="unlink" title="Rimuovi link">',
      '  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/><line x1="2" y1="2" x2="22" y2="22"/></svg>',
      '</button>',
      '<span class="olo-it-sep"></span>',
      '<button type="button" data-cmd="removeFormat" title="Rimuovi formattazione">',
      '  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>',
      '</button>',
    ].join('');

    // Command buttons — mousedown previene blur
    el.addEventListener('mousedown', function(e) {
      e.preventDefault();
      e.stopPropagation();
      toolbarClicking = true;
      setTimeout(function() { toolbarClicking = false; }, 300);

      var btn = e.target.closest('[data-cmd]');
      if (!btn) return;

      var cmd = btn.dataset.cmd;
      if (cmd === 'createLink') {
        var currentUrl = '';
        try {
          var sel = window.getSelection();
          if (sel && sel.rangeCount) {
            var anchor = sel.anchorNode;
            while (anchor && anchor.tagName !== 'A') anchor = anchor.parentElement;
            if (anchor && anchor.tagName === 'A') currentUrl = anchor.href;
          }
        } catch (ex) { /* ignore */ }
        var url = prompt('URL del link:', currentUrl || 'https://');
        if (url) {
          document.execCommand('createLink', false, url);
        }
      } else {
        document.execCommand(cmd, false, null);
      }
      updateToolbarState();
    });

    // Color input
    el.addEventListener('input', function(e) {
      if (e.target.matches('[data-color-cmd]')) {
        document.execCommand(e.target.dataset.colorCmd, false, e.target.value);
        // Aggiorna visual della "A"
        var label = e.target.parentElement.querySelector('.olo-it-color-a');
        if (label) label.style.textDecorationColor = e.target.value;
      }
    });

    // Color picker: proteggi dal blur
    var colorInputs = el.querySelectorAll('input[type="color"]');
    for (var ci = 0; ci < colorInputs.length; ci++) {
      colorInputs[ci].addEventListener('mousedown', function(e) {
        e.stopPropagation();
        toolbarClicking = true;
        setTimeout(function() { toolbarClicking = false; }, 1000);
      });
    }

    document.body.appendChild(el);
    toolbar = el;
    return el;
  }

  function positionToolbar() {
    var tb = getToolbar();
    var sel = window.getSelection();
    if (!sel || sel.isCollapsed || !sel.rangeCount) {
      tb.style.display = 'none';
      return;
    }

    var range = sel.getRangeAt(0);
    var rect = range.getBoundingClientRect();
    if (rect.width === 0 && rect.height === 0) {
      tb.style.display = 'none';
      return;
    }

    tb.style.display = 'flex';
    var tbW = tb.offsetWidth;
    var tbH = tb.offsetHeight;
    var left = rect.left + (rect.width / 2) - (tbW / 2);
    var top = rect.top - tbH - 10;

    // Viewport bounds
    if (left < 8) left = 8;
    if (left + tbW > window.innerWidth - 8) left = window.innerWidth - 8 - tbW;
    if (top < 8) top = rect.bottom + 10;

    tb.style.left = left + 'px';
    tb.style.top = top + 'px';
  }

  function updateToolbarState() {
    if (!toolbar) return;
    var buttons = toolbar.querySelectorAll('[data-cmd]');
    for (var i = 0; i < buttons.length; i++) {
      var cmd = buttons[i].dataset.cmd;
      if (cmd === 'createLink' || cmd === 'unlink' || cmd === 'removeFormat') continue;
      try {
        buttons[i].classList.toggle('active', document.queryCommandState(cmd));
      } catch (e) { /* ignore */ }
    }
  }

  function hideToolbar() {
    if (toolbar) toolbar.style.display = 'none';
  }

  function onSelectionChange() {
    if (!activeElement || !isRichtext) return;
    // Verifica che la selezione sia dentro il nostro elemento
    var sel = window.getSelection();
    if (!sel || !sel.rangeCount) { hideToolbar(); return; }
    if (!activeElement.contains(sel.anchorNode)) { hideToolbar(); return; }
    positionToolbar();
    updateToolbarState();
  }

  // ═══════════════════════════════════════════════
  //  Core inline editing
  // ═══════════════════════════════════════════════

  /**
   * Trova il tile-id risalendo il DOM fino all'elemento con data-tile-id
   */
  function findTileId(el) {
    var cell = el.closest('[data-tile-id]');
    return cell ? cell.dataset.tileId : null;
  }

  /**
   * Handler del doppio click — entry point dell'inline editing
   */
  function onDblClick(event) {
    // Non editare in preview mode
    if (builderStore.previewMode) return;

    // Trova l'elemento editabile più vicino al target del click
    var editable = event.target.closest('[data-olo-editable]');
    if (!editable) return;

    var tileId = findTileId(editable);
    if (!tileId) return;

    var fieldKey = editable.getAttribute('data-olo-editable');
    if (!fieldKey) return;

    // Il tile deve essere già selezionato
    if (builderStore.selectedTileId !== tileId) return;

    // Verifica che il campo NON abbia un dynamic binding attivo
    // Per campi array (panels.0.title), controlla il campo base
    var tile = tilesStore.getTileById(tileId);
    if (!tile) return;
    var baseField = fieldKey.indexOf('.') !== -1 ? fieldKey.split('.')[0] : fieldKey;
    if (tile.dynamic && tile.dynamic[baseField]) return;

    // Se c'è già un editing attivo su un altro campo, committa prima
    if (activeElement && (activeTileId !== tileId || activeField !== fieldKey)) {
      commitEdit();
    }

    // Se siamo già in editing su questo campo, lascia stare
    if (activeElement === editable) return;

    event.preventDefault();
    event.stopPropagation();

    startEdit(editable, tileId, fieldKey);
  }

  /**
   * Attiva contenteditable sull'elemento
   */
  function startEdit(el, tileId, fieldKey) {
    activeElement = el;
    activeTileId = tileId;
    activeField = fieldKey;
    isCommitting = false;
    isRichtext = el.hasAttribute('data-olo-richtext');

    var isMultiline = el.hasAttribute('data-olo-multiline');

    // Salva il valore originale per poter annullare con Escape
    if (isRichtext) {
      originalValue = el.innerHTML;
    } else {
      originalValue = isMultiline ? el.innerText : el.textContent;
    }

    // Rimuovi placeholder styling prima di editare
    var placeholder = el.querySelector('.olo-editable-ph');
    if (placeholder) {
      el.innerHTML = '';
    }

    // Attiva contenteditable
    el.contentEditable = 'true';
    el.focus();

    // Seleziona tutto il testo per sovrascrittura immediata
    var range = document.createRange();
    range.selectNodeContents(el);
    var sel = window.getSelection();
    sel.removeAllRanges();
    sel.addRange(range);

    // Stile visivo
    el.classList.add('olo-inline-editing');

    // Notifica il builder store
    builderStore.startInlineEdit(tileId, fieldKey);

    // Bind eventi
    el.addEventListener('blur', onBlur);
    el.addEventListener('keydown', onKeydown);
    el.addEventListener('paste', onPaste);

    // Rich text: ascolta selection per mostrare la toolbar
    if (isRichtext) {
      document.addEventListener('selectionchange', onSelectionChange);
    }
  }

  /**
   * Keydown handler — gestisce Enter, Escape, Ctrl+Enter
   */
  function onKeydown(event) {
    if (event.key === 'Escape') {
      // Annulla: ripristina valore originale
      if (isRichtext) {
        activeElement.innerHTML = originalValue;
      } else if (activeElement.hasAttribute('data-olo-multiline')) {
        activeElement.innerText = originalValue;
      } else {
        activeElement.textContent = originalValue;
      }
      cancelEdit();
      event.preventDefault();
      event.stopPropagation();
    } else if (event.key === 'Enter') {
      if (activeElement.hasAttribute('data-olo-multiline') || isRichtext) {
        // Multilinea/rich: Ctrl+Enter salva
        if (event.ctrlKey || event.metaKey) {
          event.preventDefault();
          commitEdit();
        }
        // Enter puro = newline (comportamento browser default)
      } else {
        // Singola riga: Enter = salva
        event.preventDefault();
        commitEdit();
      }
    }
  }

  /**
   * Blur handler — salva quando l'elemento perde il focus
   */
  function onBlur() {
    setTimeout(function() {
      // Non committare se il click era sulla toolbar
      if (toolbarClicking) return;
      if (activeElement && !isCommitting) {
        commitEdit();
      }
    }, 120);
  }

  /**
   * Paste handler
   */
  function onPaste(event) {
    if (isRichtext) {
      // Rich text: incolla HTML sanitizzato
      event.preventDefault();
      var html = (event.clipboardData || window.clipboardData).getData('text/html');
      var text = (event.clipboardData || window.clipboardData).getData('text/plain');
      if (html) {
        // Strip elementi pericolosi, mantieni formattazione base
        var temp = document.createElement('div');
        temp.innerHTML = html;
        var dangerous = temp.querySelectorAll('script,style,meta,link,iframe,object,embed,form');
        for (var d = 0; d < dangerous.length; d++) dangerous[d].remove();
        document.execCommand('insertHTML', false, temp.innerHTML);
      } else {
        document.execCommand('insertText', false, text);
      }
      return;
    }
    // Plain text: incolla solo testo puro
    event.preventDefault();
    var plainText = (event.clipboardData || window.clipboardData).getData('text/plain');
    document.execCommand('insertText', false, plainText);
  }

  /**
   * Salva il contenuto editato e aggiorna lo store
   */
  function commitEdit() {
    if (!activeElement || isCommitting) return;
    isCommitting = true;

    var isMultiline = activeElement.hasAttribute('data-olo-multiline');
    var newValue;

    if (isRichtext) {
      newValue = activeElement.innerHTML;
      // Pulisci artefatti contenteditable
      newValue = newValue
        .replace(/^(<br\s*\/?>|\s|&nbsp;|<p><br\s*\/?><\/p>)*$/i, '')
        .replace(/^\s+|\s+$/g, '');
    } else {
      newValue = isMultiline ? activeElement.innerText : activeElement.textContent;
      newValue = newValue.replace(/^\s+|\s+$/g, '');
    }

    var tileId = activeTileId;
    var fieldKey = activeField;
    var changed = newValue !== originalValue;

    cleanup();

    if (changed && tileId && fieldKey) {
      // Gestisci campi array: "panels.0.title"
      if (fieldKey.indexOf('.') !== -1) {
        updateArrayField(tileId, fieldKey, newValue);
      } else {
        tilesStore.updateTile(tileId, { [fieldKey]: newValue });
      }
      builderStore.isDirty = true;
    }
  }

  /**
   * Aggiorna un campo nested in un array: "panels.0.title" → tile.settings.panels[0].title = value
   */
  function updateArrayField(tileId, fieldPath, value) {
    var parts = fieldPath.split('.');
    if (parts.length < 3) return;

    var arrayKey = parts[0];
    var index = parseInt(parts[1]);
    var itemKey = parts.slice(2).join('.');

    var tile = tilesStore.getTileById(tileId);
    if (!tile || !tile.settings || !Array.isArray(tile.settings[arrayKey])) return;

    var array = tile.settings[arrayKey].map(function(item, i) {
      if (i === index) {
        var updated = {};
        for (var k in item) updated[k] = item[k];
        updated[itemKey] = value;
        return updated;
      }
      return item;
    });

    tilesStore.updateTile(tileId, { [arrayKey]: array });
  }

  /**
   * Annulla l'editing senza salvare
   */
  function cancelEdit() {
    cleanup();
  }

  /**
   * Pulizia: rimuove contenteditable, stili e event listeners
   */
  function cleanup() {
    if (!activeElement) return;

    activeElement.contentEditable = 'false';
    activeElement.classList.remove('olo-inline-editing');
    activeElement.removeEventListener('blur', onBlur);
    activeElement.removeEventListener('keydown', onKeydown);
    activeElement.removeEventListener('paste', onPaste);

    if (isRichtext) {
      document.removeEventListener('selectionchange', onSelectionChange);
      hideToolbar();
    }

    activeElement = null;
    activeTileId = null;
    activeField = null;
    originalValue = '';
    isCommitting = false;
    isRichtext = false;

    builderStore.stopInlineEdit();
  }

  // ═══════════════════════════════════════════════
  //  Lifecycle
  // ═══════════════════════════════════════════════

  function attach() {
    var el = canvasEl && canvasEl.value ? canvasEl.value : canvasEl;
    if (el && el.addEventListener) {
      el.addEventListener('dblclick', onDblClick);
    }
  }

  function detach() {
    if (activeElement) cancelEdit();
    var el = canvasEl && canvasEl.value ? canvasEl.value : canvasEl;
    if (el && el.removeEventListener) {
      el.removeEventListener('dblclick', onDblClick);
    }
    // Non rimuovere toolbar al detach — è singleton riusabile
  }

  onMounted(attach);
  onUnmounted(detach);

  return { commitEdit, cancelEdit };
}
