import { ref } from 'vue';
import { useTilesStore } from '@/stores/tiles';
import { useBuilderStore } from '@/stores/builder';

// Shared singleton state
const undoStack = ref([]);
const redoStack = ref([]);
const maxHistory = 100;
let isProgrammatic = false;
let lastSnapshot = null; // { h, b, f } — una stringa JSON per zona (header/body/footer)
let initialized = false;
let debounceTimer = null;
const DEBOUNCE_MS = 400;

export function useHistory() {
  const tilesStore = useTilesStore();
  const builderStore = useBuilderStore();

  /**
   * Snapshot per-zona {h, b, f}: copre l'editing unificato header/body/footer
   * (prima era solo canvasTiles: un delete nell'header non era annullabile).
   * Le zone invariate riusano la STRINGA del passo precedente, così 100 step
   * di history non duplicano header/footer quando cambia solo il body.
   */
  function snapshot() {
    const h = JSON.stringify(tilesStore.headerTiles);
    const b = JSON.stringify(tilesStore.canvasTiles);
    const f = JSON.stringify(tilesStore.footerTiles);
    if (lastSnapshot) {
      return {
        h: h === lastSnapshot.h ? lastSnapshot.h : h,
        b: b === lastSnapshot.b ? lastSnapshot.b : b,
        f: f === lastSnapshot.f ? lastSnapshot.f : f,
      };
    }
    return { h, b, f };
  }

  function sameSnapshot(a, b) {
    return !!a && !!b && a.h === b.h && a.b === b.b && a.f === b.f;
  }

  function restore(state) {
    isProgrammatic = true;
    try {
      const prev = lastSnapshot;
      if (!prev || state.b !== prev.b) {
        tilesStore.canvasTiles = JSON.parse(state.b);
        builderStore.isDirty = true;
      }
      if (!prev || state.h !== prev.h) {
        tilesStore.headerTiles = JSON.parse(state.h);
        builderStore.headerDirty = true;
      }
      if (!prev || state.f !== prev.f) {
        tilesStore.footerTiles = JSON.parse(state.f);
        builderStore.footerDirty = true;
      }
      // Header/footer non hanno un deep watch dedicato nell'iframe bridge:
      // il bump forza il re-render del live preview per tutte le zone.
      tilesStore._bumpVersion();
      lastSnapshot = state;
    } catch (e) {
      console.error('[Olobuild] Undo/Redo restore failed:', e);
    } finally {
      setTimeout(() => { isProgrammatic = false; }, 50);
    }
  }

  /**
   * Commit sincrono dello stato corrente (bypassa/azzera il debounce).
   * Usato dal DnD e dalla rimozione tile per garantire un punto di undo
   * atomico prima della mutazione, e da undo/redo come flush.
   */
  function pushStateNow() {
    if (isProgrammatic) return;
    if (debounceTimer) {
      clearTimeout(debounceTimer);
      debounceTimer = null;
    }
    const current = snapshot();
    if (sameSnapshot(current, lastSnapshot)) return;
    if (lastSnapshot !== null) {
      undoStack.value.push(lastSnapshot);
      if (undoStack.value.length > maxHistory) undoStack.value.shift();
      redoStack.value = [];
    }
    lastSnapshot = current;
  }

  // Alias legacy: stessa semantica di pushStateNow.
  const pushState = pushStateNow;

  function undo() {
    // Flush del debounce pendente: senza, un Ctrl+Z entro 400ms dall'ultima
    // modifica salterebbe indietro di DUE stati (l'ultimo non ancora committato).
    pushStateNow();
    if (undoStack.value.length === 0) return;
    redoStack.value.push(lastSnapshot);
    restore(undoStack.value.pop());
  }

  function redo() {
    pushStateNow();
    if (redoStack.value.length === 0) return;
    undoStack.value.push(lastSnapshot);
    restore(redoStack.value.pop());
  }

  /**
   * Silent rollback: ripristina lo stato corrente all'ultimo push,
   * rimuovendo quello push dalla history (no fantasma undo step).
   * Usato quando un drop fallisce e vogliamo annullare tutto senza
   * che l'utente debba premere Ctrl+Z.
   */
  function rollback() {
    if (undoStack.value.length === 0) return;
    restore(undoStack.value.pop());
  }

  function initHistory() {
    undoStack.value = [];
    redoStack.value = [];
    // Azzera prima di ricampionare: snapshot() riusa le stringhe di lastSnapshot,
    // e su un nuovo template non vogliamo riferimenti al template precedente.
    lastSnapshot = null;
    lastSnapshot = snapshot();

    if (!initialized) {
      initialized = true;
      tilesStore.$subscribe(() => {
        if (isProgrammatic) return;
        // Debounce snapshot to avoid JSON.stringify on every keystroke
        if (debounceTimer) clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
          debounceTimer = null;
          pushStateNow();
        }, DEBOUNCE_MS);
      });
    }
  }

  return {
    undoStack,
    redoStack,
    canUndo: () => undoStack.value.length > 0,
    canRedo: () => redoStack.value.length > 0,
    pushState,
    pushStateNow,
    rollback,
    undo,
    redo,
    initHistory,
  };
}
