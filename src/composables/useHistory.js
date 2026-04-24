import { ref } from 'vue';
import { useTilesStore } from '@/stores/tiles';
import { useBuilderStore } from '@/stores/builder';

// Shared singleton state
const undoStack = ref([]);
const redoStack = ref([]);
const maxHistory = 100;
let isProgrammatic = false;
let lastSnapshot = null;
let initialized = false;
let debounceTimer = null;
const DEBOUNCE_MS = 400;

export function useHistory() {
  const tilesStore = useTilesStore();
  const builderStore = useBuilderStore();

  function snapshot() {
    return JSON.stringify(tilesStore.canvasTiles);
  }

  function restore(state) {
    isProgrammatic = true;
    try {
      tilesStore.canvasTiles = JSON.parse(state);
      builderStore.isDirty = true;
      lastSnapshot = state;
    } catch (e) {
      console.error('[Olobuild] Undo/Redo restore failed:', e);
    } finally {
      setTimeout(() => { isProgrammatic = false; }, 50);
    }
  }

  function undo() {
    if (undoStack.value.length === 0) return;
    redoStack.value.push(snapshot());
    restore(undoStack.value.pop());
  }

  function redo() {
    if (redoStack.value.length === 0) return;
    undoStack.value.push(snapshot());
    restore(redoStack.value.pop());
  }

  function pushState() {
    if (isProgrammatic) return;
    const current = snapshot();
    if (current === lastSnapshot) return;
    if (lastSnapshot !== null) {
      undoStack.value.push(lastSnapshot);
      if (undoStack.value.length > maxHistory) undoStack.value.shift();
      redoStack.value = [];
    }
    lastSnapshot = current;
  }

  /**
   * Forza uno snapshot sincrono bypassando il debounce.
   * Usato dal sistema DnD per garantire un punto di undo atomico
   * prima di ogni drag: se il drop fallisce, rollback sicuro.
   */
  function pushStateNow() {
    if (isProgrammatic) return;
    if (debounceTimer) {
      clearTimeout(debounceTimer);
      debounceTimer = null;
    }
    const current = snapshot();
    if (current === lastSnapshot) return;
    if (lastSnapshot !== null) {
      undoStack.value.push(lastSnapshot);
      if (undoStack.value.length > maxHistory) undoStack.value.shift();
      redoStack.value = [];
    }
    lastSnapshot = current;
  }

  /**
   * Silent rollback: ripristina lo stato corrente all'ultimo push,
   * rimuovendo quello push dalla history (no fantasma undo step).
   * Usato quando un drop fallisce e vogliamo annullare tutto senza
   * che l'utente debba premere Ctrl+Z.
   */
  function rollback() {
    if (undoStack.value.length === 0) return;
    const last = undoStack.value.pop();
    isProgrammatic = true;
    try {
      tilesStore.canvasTiles = JSON.parse(last);
      lastSnapshot = last;
    } catch (e) {
      console.error('[Olobuild] rollback failed:', e);
    } finally {
      setTimeout(() => { isProgrammatic = false; }, 50);
    }
  }

  function initHistory() {
    undoStack.value = [];
    redoStack.value = [];
    lastSnapshot = snapshot();

    if (!initialized) {
      initialized = true;
      tilesStore.$subscribe(() => {
        if (isProgrammatic) return;
        // Debounce snapshot to avoid JSON.stringify on every keystroke
        if (debounceTimer) clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
          const current = snapshot();
          if (current === lastSnapshot) return;
          if (lastSnapshot !== null) {
            undoStack.value.push(lastSnapshot);
            if (undoStack.value.length > maxHistory) undoStack.value.shift();
            redoStack.value = [];
          }
          lastSnapshot = current;
        }, DEBOUNCE_MS);
      });
    }
  }

  function handleKeyboard() {}

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
    handleKeyboard,
  };
}
