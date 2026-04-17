/**
 * useIframeBridge — manages postMessage communication between parent builder and preview iframe.
 */
import { ref, watch, onMounted, onUnmounted } from 'vue';
import { useTilesStore } from '@/stores/tiles';
import { useBuilderStore } from '@/stores/builder';
import { useDragDrop } from '@/composables/useDragDrop';

let debounceTimer = null;
let patchTimer = null;
let lastTileSnapshot = null;
let renderInFlight = false;

function deepClone(obj) { return JSON.parse(JSON.stringify(obj)); }

export function useIframeBridge(iframeRef) {
  const tilesStore = useTilesStore();
  const builderStore = useBuilderStore();
  const { handleDropFromSidebar } = useDragDrop();
  const iframeReady = ref(false);
  const iframeHeight = ref(800);

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
      postToIframe('olo:render', { html: '<div class="olo-iframe-empty">Nessun contenuto — trascina una tile dalla sidebar</div>' });
      lastTileSnapshot = null;
      return;
    }

    renderInFlight = true;
    try {
      const body = { tiles: deepClone(tiles), page_settings: pageSettings };
      // Include header/footer if loaded
      if (headerTiles && headerTiles.length) body.header_tiles = deepClone(headerTiles);
      if (footerTiles && footerTiles.length) body.footer_tiles = deepClone(footerTiles);

      // For single templates, pass type and post_type so PHP can set up post context
      const tpl = builderStore.currentTemplate;
      if (tpl && tpl.type === 'single') {
        body.template_type = 'single';
        // Extract post_type from template settings or conditions
        body.post_type = tpl.settings?.single_post_type || tpl.settings?.post_type || tpl.post_type || 'post';
        if (tpl.settings?.preview_post_id) {
          body.preview_post_id = tpl.settings.preview_post_id;
        }
      }

      const res = await fetch(window.oloData.restUrl + '/builder/render', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': window.oloData.nonce },
        body: JSON.stringify(body),
      });
      const data = await res.json();
      console.log('[IframeBridge] render response:', { htmlLen: (data.html||'').length, hasError: !!data.code, status: res.status });
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

  // ── Patch single tile — always full render for reliability ──
  // Single-tile patching caused issues with complex tiles (galleries, carousels, sliders)
  // whose JS wouldn't re-initialize after DOM replacement.

  function patchTile(/* tileId */) {
    scheduleFullRender();
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
    patchTimer = setTimeout(() => patchTile(tileId), 250);
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
        iframeReady.value = true;
        renderFull();
        break;

      case 'olo:tile-click':
        if (d.tileId) builderStore.selectTile(d.tileId);
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
          tilesStore.updateTile(d.tileId, { [d.field]: d.value });
          builderStore.markDirtyForTile(d.tileId || builderStore.selectedTileId);
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
          // Find the row containing this tile and add a column after the current one
          tilesStore.addColumnForTile(d.tileId);
          builderStore.isDirty = true;
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

  watch(() => builderStore.selectedTileId, (newId) => {
    if (newId) {
      postToIframe('olo:select', { tileId: newId });
    } else {
      postToIframe('olo:deselect');
    }
  });

  watch(() => tilesStore.canvasTiles, onTilesChange, { deep: true });

  // Header/footer changes → full re-render
  watch(() => tilesStore.headerTiles, () => {
    if (iframeReady.value) scheduleFullRender();
  }, { deep: true });
  watch(() => tilesStore.footerTiles, () => {
    if (iframeReady.value) scheduleFullRender();
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

  // ── Lifecycle ──

  onMounted(() => {
    window.addEventListener('message', onMessage, false);
  });

  onUnmounted(() => {
    window.removeEventListener('message', onMessage, false);
    clearTimeout(debounceTimer);
    clearTimeout(patchTimer);
  });

  return { iframeReady, iframeHeight, renderFull, postToIframe };
}
