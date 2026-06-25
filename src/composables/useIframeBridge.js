/**
 * useIframeBridge — manages postMessage communication between parent builder and preview iframe.
 */
import { ref, watch, onMounted, onUnmounted } from 'vue';
import { useTilesStore, createRow, createColumn } from '@/stores/tiles';
import { useBuilderStore } from '@/stores/builder';
import { useDragDrop } from '@/composables/useDragDrop';
import { onScrollToTileRequest } from '@/utils/scrollToTileChannel';
import { loadScrollFlashPrefs } from '@/utils/scrollFlashPrefs';

let debounceTimer = null;
let patchTimer = null;
let zoneTimer = null;
let lastTileSnapshot = null;
let renderInFlight = false;

// JSON.parse(JSON.stringify) — robusto su tutti i payload Pinia/Vue.
//
// Avevamo provato `structuredClone` per performance ma fallisce con DataCloneError
// su payload provenienti da Pinia reactive (proxy/function/symbol non-clonabili,
// es. funzioni getter sui derived state). Il fallback try/catch sarebbe stato un
// flag di errore globale — meglio restare su JSON, che è "schema-safe": cloni
// solo i dati serializzabili, e quelli sono esattamente quelli che la REST
// builder/render accetta come body.
function deepClone(obj) {
  return JSON.parse(JSON.stringify(obj));
}

export function useIframeBridge(iframeRef) {
  const tilesStore = useTilesStore();
  const builderStore = useBuilderStore();
  const { handleDropFromSidebar, handleDropIntoColumn } = useDragDrop();
  const iframeReady = ref(false);
  const iframeHeight = ref(800);
  // 'standalone' (default): l'iframe carica il template builder-iframe.php standalone,
  // il bridge inietta HTML completo (header + body + footer renderizzato dal REST).
  // 'inline': l'iframe carica una pagina WP reale (permalink del post associato);
  // header e footer sono già renderizzati dal tema/Olo_Header_Integration, quindi
  // qui dobbiamo iniettare SOLO il body — pena doppio header/footer.
  let iframeMode = 'standalone';

  function postToIframe(type, data) {
    const iframe = iframeRef.value;
    if (!iframe || !iframe.contentWindow) return;
    iframe.contentWindow.postMessage(Object.assign({ type }, data || {}), '*');
  }

  // ── Full render via REST ──

  async function renderFull() {
    const tiles = tilesStore.canvasTiles;
    const headerTiles = tilesStore.headerTiles;
    const footerTiles = tilesStore.footerTiles;
    const pageSettings = builderStore.pageSettings || {};

    if ((!tiles || !tiles.length) && (!headerTiles || !headerTiles.length) && (!footerTiles || !footerTiles.length)) {
      const emptyHtml = (window.oloData && window.oloData.iframeEmptyHtml)
        || '<div class="olo-iframe-empty"><div class="olo-iframe-empty-card"><h3 class="olo-iframe-empty-title">Pagina vuota</h3><p class="olo-iframe-empty-text">Aggiungi un modulo o scegli un layout per iniziare</p><div class="olo-iframe-empty-actions"><button type="button" class="olo-iframe-empty-btn" data-olo-empty-action="add-module"><span class="olo-iframe-empty-btn-icon"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" stroke-dasharray="3 3"/><path d="M12 8v8M8 12h8"/></svg></span><span class="olo-iframe-empty-btn-label">Aggiungi modulo</span></button><button type="button" class="olo-iframe-empty-btn" data-olo-empty-action="add-row"><span class="olo-iframe-empty-btn-icon"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg></span><span class="olo-iframe-empty-btn-label">Scegli layout</span></button></div></div></div>';
      postToIframe('olo:render', { html: emptyHtml });
      lastTileSnapshot = null;
      return;
    }

    renderInFlight = true;
    try {
      const body = { tiles: deepClone(tiles), page_settings: pageSettings };
      // Include header/footer SOLO in modalità standalone. In modalità inline
      // (iframe = pagina WP reale) header e footer sono già renderizzati dal
      // tema; aggiungerli qui produrrebbe duplicati.
      if (iframeMode !== 'inline') {
        if (headerTiles && headerTiles.length) body.header_tiles = deepClone(headerTiles);
        if (footerTiles && footerTiles.length) body.footer_tiles = deepClone(footerTiles);
      }

      // For single templates, pass type and post_type so PHP can set up post context
      const tpl = builderStore.currentTemplate;
      if (tpl && tpl.type === 'single') {
        body.template_type = 'single';
        body.post_type = tpl.settings?.single_post_type || tpl.settings?.post_type || tpl.post_type || 'post';
        if (tpl.settings?.preview_post_id) {
          body.preview_post_id = tpl.settings.preview_post_id;
        }
      }

      console.log('[IframeBridge] POST /builder/render body.page_settings.page_bg:', body?.page_settings?.page_bg);
      const res = await fetch(window.oloData.restUrl + '/builder/render', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': window.oloData.nonce },
        body: JSON.stringify(body),
      });
      const data = await res.json();
      console.log('[IframeBridge] render response:', { htmlLen: (data.html||'').length, inline_css_has_body_bg: (data.inline_css||'').includes('html, body'), hasError: !!data.code, status: res.status });
      if (data.code) {
        console.error('[IframeBridge] REST error:', data.code, data.message);
      }
      if (data.html) {
        postToIframe('olo:render', { html: data.html, css: data.inline_css || '' });
      }
      lastTileSnapshot = deepClone(tiles);
    } catch (err) {
      console.error('[IframeBridge] render error:', err);
    }
    renderInFlight = false;
  }

  // ── Patch single tile — incremental render via /builder/render-tile ──
  // Per modifiche settings/style di un singolo tile foglia, evitiamo il full
  // render dell'intero template (60-80% riduzione traffico REST in editing).
  // Su qualsiasi errore o tile non-patchable → fallback a scheduleFullRender().

  // Tile-type/condition NON patchabili: il single-tile render rompe la struttura
  // o richiede re-init JS che il bridge non gestisce in modo affidabile.
  const NON_PATCHABLE_TYPES = new Set([
    'section', 'row', 'inner-columns', 'inner-column', 'floatingpanel',
    'map', 'osmmap', // Leaflet: resetScriptGuards non è scoped al subtree, rischio race con altri map
  ]);

  function isPatchable(node) {
    if (!node) return false;
    if (NON_PATCHABLE_TYPES.has(node.type)) return false;
    const s = node.settings || {};
    if (s.widget_template_id || s.tile_template_id) return false; // espande template esterno
    if (s.loop_enabled)                              return false; // ripete tile N volte
    if (node.advanced?.html_id)                      return false; // ID custom: full render aggiorna tutti i selettori CSS
    return true;
  }

  let patchInFlight = false;

  async function patchTile(tileId) {
    if (renderInFlight || patchInFlight) return;
    const node = findNodeById(tilesStore.canvasTiles, tileId);
    if (!node) { scheduleFullRender(); return; }
    if (!isPatchable(node)) { scheduleFullRender(); return; }

    // Leggi il css_id del nodo già renderizzato (es. "ms-118-3") per estrarre il
    // counter — così il nuovo render genera lo STESSO ID e gli hover/responsive
    // rules continuano a matchare.
    const iframe = iframeRef.value;
    const existingEl = iframe?.contentDocument?.querySelector('[data-olo-tile-id="' + tileId + '"]');
    const existingId = existingEl?.id || '';
    // Pattern: ms- (section), mr- (row), mc- (column), mt- (tile/element), ma- (advanced?)
    const counterMatch = existingId.match(/^m[srcatu]-\d+-(\d+)$/);
    const counterHint = counterMatch ? parseInt(counterMatch[1]) : 0;
    if (!counterHint) { scheduleFullRender(); return; } // ID non riconosciuto → safe fallback

    const olo = window.oloData || {};
    const tpl = builderStore.currentTemplate;
    const body = {
      tile:               deepClone(node),
      page_settings:      builderStore.pageSettings || {},
      // template_id: 0 — coerente con render_tiles_array() che hardcoda 0 per il
      // builder rendering. Il CSS ID risultante (mt-0-N) deve matchare quello già
      // nel DOM dell'iframe, sennò hover/responsive non si applicano al nuovo nodo.
      template_id:        0,
      tile_counter_hint:  counterHint,
      template_type:      tpl?.type || 'page',
      post_type:          tpl?.settings?.single_post_type || tpl?.post_type || 'post',
      preview_post_id:    tpl?.settings?.preview_post_id || 0,
    };

    patchInFlight = true;
    try {
      const res = await fetch(olo.restUrl + '/builder/render-tile', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': olo.nonce },
        body: JSON.stringify(body),
      });
      const data = await res.json();
      if (!res.ok || !data || !data.html) {
        console.warn('[IframeBridge] patch returned empty/error → full render', data?.code);
        scheduleFullRender();
        return;
      }
      postToIframe('olo:patch', {
        tileId,
        html:       data.html,
        scoped_css: data.scoped_css || '',
      });
    } catch (err) {
      console.error('[IframeBridge] patch error → full render:', err);
      scheduleFullRender();
    } finally {
      patchInFlight = false;
    }
  }

  function findNodeById(nodes, id) {
    for (const n of nodes) {
      if (n.id === id) return n;
      if (n.children) {
        const found = findNodeById(n.children, id);
        if (found) return found;
      }
    }
    return null;
  }

  // ── Smart change detection ──

  function detectChange(oldTiles, newTiles) {
    if (!oldTiles || !newTiles) return { type: 'full' };
    if (oldTiles.length !== newTiles.length) return { type: 'full' };

    // Compare each top-level section
    for (let i = 0; i < newTiles.length; i++) {
      if (oldTiles[i].id !== newTiles[i].id) return { type: 'full' };
    }

    // Find which tile changed (deep comparison)
    const changedId = findChangedTile(oldTiles, newTiles);
    if (changedId) return { type: 'patch', tileId: changedId };

    // Fallback: if structures differ at any depth (add/remove/reorder), full render
    if (JSON.stringify(oldTiles) !== JSON.stringify(newTiles)) return { type: 'full' };

    return { type: 'none' };
  }

  function findChangedTile(oldNodes, newNodes) {
    if (!oldNodes || !newNodes) return null;
    if (oldNodes.length !== newNodes.length) return null;

    for (let i = 0; i < newNodes.length; i++) {
      const o = oldNodes[i], n = newNodes[i];
      if (o.id !== n.id) return null;

      // Compare settings/style/advanced (JSON stringify comparison)
      if (JSON.stringify(o.settings) !== JSON.stringify(n.settings) ||
          JSON.stringify(o.style) !== JSON.stringify(n.style) ||
          JSON.stringify(o.advanced) !== JSON.stringify(n.advanced)) {
        return n.id;
      }

      // Recurse into children
      if (o.children && n.children) {
        const childChanged = findChangedTile(o.children, n.children);
        if (childChanged) return childChanged;
      }
    }
    return null;
  }

  // ── Scheduling ──

  function scheduleFullRender() {
    clearTimeout(debounceTimer);
    clearTimeout(patchTimer);
    debounceTimer = setTimeout(renderFull, 300);
  }

  function schedulePatch(tileId) {
    clearTimeout(patchTimer);
    // 80ms vs 300ms del full: la patch è 5-10× più leggera, possiamo essere reattivi.
    // Componenti inspector hanno già debounce/throttle propri (color picker, range, ecc.)
    // quindi NON moltiplichiamo richieste.
    patchTimer = setTimeout(() => patchTile(tileId), 80);
  }

  // ── Render di una singola zona (header/footer) — usato in modalità INLINE ──
  // In inline mode l'header/footer è renderizzato dal tema FUORI da #olo-iframe-root,
  // quindi renderFull (che aggiorna solo il body) NON lo tocca: le modifiche all'header
  // (template/preset/stile) si salvano ma non si vedono live. Qui ri-renderizziamo SOLO
  // la zona via REST e la iniettiamo nell'iframe sostituendo il CONTENUTO della zona
  // (preservandone il wrapper <header>/<footer>, quindi overlay/sticky restano intatti).
  // Fail-safe: su qualsiasi errore non facciamo nulla → la zona resta com'era (= stato attuale).
  async function renderZone(zone) {
    if (renderInFlight || patchInFlight) return;
    const zoneTiles = zone === 'footer' ? tilesStore.footerTiles : tilesStore.headerTiles;
    if (!zoneTiles || !zoneTiles.length) return;
    const olo = window.oloData || {};
    const body = { page_settings: builderStore.pageSettings || {} };
    // SOLO i tile della zona richiesta (body tiles assenti → il REST rende solo la zona).
    if (zone === 'footer') body.footer_tiles = deepClone(zoneTiles);
    else body.header_tiles = deepClone(zoneTiles);
    try {
      const res = await fetch(olo.restUrl + '/builder/render', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': olo.nonce },
        body: JSON.stringify(body),
      });
      const data = await res.json();
      if (data && data.html) {
        postToIframe('olo:render-zone', { zone, html: data.html });
      }
    } catch (err) {
      console.error('[IframeBridge] zone render error:', err);
    }
  }

  function scheduleZoneRender(zone) {
    clearTimeout(zoneTimer);
    zoneTimer = setTimeout(() => renderZone(zone), 200);
  }

  function onTilesChange() {
    if (!iframeReady.value || renderInFlight) return;

    const newTiles = deepClone(tilesStore.canvasTiles);
    const change = detectChange(lastTileSnapshot, newTiles);

    if (change.type === 'full') {
      lastTileSnapshot = newTiles;
      scheduleFullRender();
    } else if (change.type === 'patch') {
      lastTileSnapshot = newTiles;
      schedulePatch(change.tileId);
    }
    // 'none' — no visual change needed
  }

  // ── Message handler ──

  function onMessage(event) {
    const d = event.data;
    if (!d || typeof d.type !== 'string' || d.type.indexOf('olo:') !== 0) return;

    switch (d.type) {
      case 'olo:ready':
        iframeMode = (d.mode === 'inline') ? 'inline' : 'standalone';
        iframeReady.value = true;
        renderFull();
        break;

      case 'olo:tile-click':
        if (d.tileId) {
          if (d.additive) builderStore.toggleTileSelection(d.tileId);
          else builderStore.selectTile(d.tileId);
        }
        break;

      case 'olo:canvas-click':
        builderStore.deselectTile();
        break;

      case 'olo:tile-contextmenu':
        if (d.tileId) {
          builderStore.selectTile(d.tileId);
          const iframe = iframeRef.value;
          if (iframe) {
            const rect = iframe.getBoundingClientRect();
            builderStore._iframeContextMenu = {
              tileId: d.tileId,
              x: d.x + rect.left,
              y: d.y + rect.top,
            };
          }
        }
        break;

      case 'olo:tile-dblclick':
        // Handled in bridge now (inline contenteditable)
        break;

      case 'olo:inline-edit':
        if (d.tileId && d.field && d.value !== undefined) {
          // Gestisci campi array dotted (es. "headline_lines.0.text"): senza
          // questo split, updateTile salverebbe la chiave letterale con i punti,
          // lasciando intatto l'array originale e perdendo la modifica inline.
          if (d.field.indexOf('.') !== -1) {
            const parts = d.field.split('.');
            if (parts.length >= 3) {
              const arrayKey = parts[0];
              const index    = parseInt(parts[1], 10);
              const itemKey  = parts.slice(2).join('.');
              const tile     = tilesStore.getTileById(d.tileId);
              if (tile && tile.settings && Array.isArray(tile.settings[arrayKey])) {
                const newArr = tile.settings[arrayKey].map((item, i) =>
                  i === index ? { ...item, [itemKey]: d.value } : item
                );
                tilesStore.updateTile(d.tileId, { [arrayKey]: newArr });
              }
            }
          } else {
            tilesStore.updateTile(d.tileId, { [d.field]: d.value });
          }
          builderStore.markDirtyForTile(d.tileId || builderStore.selectedTileId);
        }
        break;

      case 'olo:layout-snapshot':
        builderStore.iframeLayout = { sections: d.sections || [], columns: d.columns || [], containers: d.containers || [] };
        break;

      case 'olo:open-finder-for':
        if (d.tileId) {
          builderStore.insertAfterTileId = null;
          const openFinder = window.__oloOpenFinder;
          if (openFinder) openFinder(d.tileId);
        }
        break;

      case 'olo:add-section':
        if (d.index !== undefined) {
          const openInsertPanel = window.__oloOpenInsertPanel;
          if (openInsertPanel) {
            openInsertPanel(d.index);
          } else {
            handleDropFromSidebar('section', d.index);
          }
        }
        break;

      case 'olo:empty-action': {
        const openInsertPanel = window.__oloOpenInsertPanel;
        if (openInsertPanel) {
          const tab = d.action === 'add-row' ? 'row' : 'module';
          openInsertPanel(0, tab);
        }
        break;
      }

      case 'olo:reorder':
        if (d.sourceId && d.targetId) {
          tilesStore.moveNodeNear(d.sourceId, d.targetId, d.before);
          builderStore.markDirtyForTile(d.tileId || builderStore.selectedTileId);
        }
        break;

      case 'olo:add-tile-after':
        if (d.tileId) {
          // Open Finder to insert a tile right after the clicked one (same column)
          builderStore.insertAfterTileId = d.tileId;
          window.dispatchEvent(new CustomEvent('olo:open-finder-after', { detail: { tileId: d.tileId } }));
        }
        break;

      case 'olo:add-column':
        if (d.tileId) {
          tilesStore.addColumnForTile(d.tileId);
          builderStore.isDirty = true;
        }
        break;

      case 'olo:add-row':
        if (d.sectionId) {
          const section = tilesStore.getTileById(d.sectionId);
          if (section) {
            const idx = typeof d.rowIndex === 'number' ? d.rowIndex : (section.children || []).length;
            const col1 = createColumn('1-2', []);
            const col2 = createColumn('1-2', []);
            const newRow = createRow('50-50', [col1, col2]);
            if (!Array.isArray(section.children)) section.children = [];
            section.children.splice(idx, 0, newRow);
            tilesStore._bumpVersion();
            builderStore.markDirtyForTile(d.sectionId);
            builderStore.selectTile(newRow.id);
          }
        }
        break;

      case 'olo:tile-action':
        if (d.tileId && d.action) {
          if (d.action === 'duplicate') {
            tilesStore.duplicateTile(d.tileId);
            builderStore.markDirtyForTile(d.tileId || builderStore.selectedTileId);
          } else if (d.action === 'delete') {
            tilesStore.removeTile(d.tileId);
            builderStore.markDirtyForTile(d.tileId || builderStore.selectedTileId);
            builderStore.deselectTile();
          } else if (d.action === 'moveup') {
            tilesStore.moveUp(d.tileId);
            builderStore.markDirtyForTile(d.tileId || builderStore.selectedTileId);
          } else if (d.action === 'movedown') {
            tilesStore.moveDown(d.tileId);
            builderStore.markDirtyForTile(d.tileId || builderStore.selectedTileId);
          } else if (d.action === 'moveleft') {
            tilesStore.moveToSiblingColumn(d.tileId, -1);
            builderStore.markDirtyForTile(d.tileId || builderStore.selectedTileId);
          } else if (d.action === 'moveright') {
            tilesStore.moveToSiblingColumn(d.tileId, 1);
            builderStore.markDirtyForTile(d.tileId || builderStore.selectedTileId);
          }
        }
        break;

      case 'olo:height':
        if (d.height) iframeHeight.value = d.height;
        break;
      case 'olo:request-full-render':
        scheduleFullRender();
        break;
    }
  }

  // ── Watchers ──

  // Sincronizza l'INTERO set di selezione verso l'iframe (multi-selezione ctrl-click).
  // selectedTileIds cambia insieme a selectedTileId, quindi questo copre anche il singolo.
  watch(() => builderStore.selectedTileIds.join('|'), () => {
    const ids = builderStore.selectedTileIds.slice();
    if (ids.length) {
      postToIframe('olo:select-set', { ids });
    } else {
      postToIframe('olo:deselect');
    }
  });

  // Use tilesVersion counter instead of deep watchers for better performance
  // tilesVersion is incremented on structural changes (add/remove/move)
  watch(() => tilesStore.tilesVersion, () => {
    onTilesChange(tilesStore.canvasTiles);
    if (iframeReady.value) scheduleFullRender();
  });

  // Still need deep watch on canvasTiles for property-level changes (settings, style)
  // but throttled with a shallow check first
  watch(() => tilesStore.canvasTiles, onTilesChange, { deep: true });

  // Header/footer → re-render. In INLINE mode l'header/footer è del tema (fuori da
  // #olo-iframe-root): il full render lo salta, quindi facciamo un re-render MIRATO
  // della zona. In STANDALONE il full render include già header_tiles/footer_tiles.
  watch(() => tilesStore.headerTiles, () => {
    if (!iframeReady.value) return;
    if (iframeMode === 'inline') scheduleZoneRender('header');
    else scheduleFullRender();
  }, { deep: true });
  watch(() => tilesStore.footerTiles, () => {
    if (!iframeReady.value) return;
    if (iframeMode === 'inline') scheduleZoneRender('footer');
    else scheduleFullRender();
  }, { deep: true });

  watch(() => builderStore.pageSettings, () => {
    if (iframeReady.value) scheduleFullRender();
  }, { deep: true });

  watch(() => builderStore.previewMode, (val) => {
    postToIframe('olo:preview-mode', { enabled: val });
  });

  watch(() => builderStore.wireframeMode, (val) => {
    postToIframe('olo:wireframe-mode', { enabled: val });
  });

  // Force-hover: quando l'utente attiva l'editing hover dall'inspector,
  // mostra la tile selezionata "come fosse in hover" nel canvas (clone delle
  // regole CSS :hover applicato via [data-olo-force-hover]).
  function syncForceHover() {
    if (!iframeReady.value) return;
    postToIframe('olo:force-hover', {
      enabled: !!builderStore.editingHover,
      tileId: builderStore.selectedTileId || null,
    });
  }
  watch(() => builderStore.editingHover, syncForceHover);
  // Anche quando la tile selezionata cambia mentre editingHover è attivo,
  // dobbiamo spostare il force-hover sulla nuova tile.
  watch(() => builderStore.selectedTileId, () => {
    if (builderStore.editingHover) syncForceHover();
  });

  // ── Lifecycle ──

  // Canale scrollToTile: StructureTree → utility → questo listener → iframe.
  // Non passa dallo store (memoria: scroll-flash deve restare fuori da builder.js).
  let unsubScroll = null;

  // Backup: alcuni componenti (PageSettingsPanel onBgUpdate) richiedono full re-render
  // esplicitamente via CustomEvent, perché il watcher su pageSettings non sempre scatta
  // per nested mutations sui getter Pinia.
  function onForceRerender() {
    if (iframeReady.value) scheduleFullRender();
  }

  onMounted(() => {
    window.addEventListener('message', onMessage, false);
    window.addEventListener('olo:builder-force-rerender', onForceRerender);
    // Espone scheduleFullRender come fallback diretto. Quando il watcher su pageSettings
    // non scatta (es. nested mutation Pinia su getter ricomputato), PageSettingsPanel
    // può chiamare questa direttamente — è più affidabile del CustomEvent.
    window.__oloBridgeForceRerender = scheduleFullRender;
    window.__oloBridgePostToIframe = postToIframe;
    unsubScroll = onScrollToTileRequest((tileId) => {
      // Le prefs flash viaggiano nel messaggio: l'iframe non ha accesso al
      // localStorage del builder (origin/document diversi).
      if (tileId) postToIframe('olo:scroll-to', { tileId, flash: loadScrollFlashPrefs() });
    });
  });

  onUnmounted(() => {
    window.removeEventListener('message', onMessage, false);
    window.removeEventListener('olo:builder-force-rerender', onForceRerender);
    if (window.__oloBridgeForceRerender === scheduleFullRender) {
      delete window.__oloBridgeForceRerender;
    }
    if (window.__oloBridgePostToIframe === postToIframe) {
      delete window.__oloBridgePostToIframe;
    }
    if (unsubScroll) unsubScroll();
    clearTimeout(debounceTimer);
    clearTimeout(patchTimer);
  });

  return { iframeReady, iframeHeight, renderFull, postToIframe };
}
