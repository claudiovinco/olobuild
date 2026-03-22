import { ref, watch, onUnmounted } from 'vue';
import { useTilesStore } from '@/stores/tiles';
import { useBuilderStore } from '@/stores/builder';

export function useHistory() {
  const undoStack = ref([]);
  const redoStack = ref([]);
  const maxHistory = 100;
  let isProgrammatic = false;
  let stopWatch = null;
  const tilesStore = useTilesStore();
  const builderStore = useBuilderStore();

  function snapshot() {
    return JSON.stringify(tilesStore.canvasTiles);
  }

  function restore(state) {
    isProgrammatic = true;
    try {
      tilesStore.setCanvasTiles(JSON.parse(state));
      builderStore.isDirty = true;
    } catch (e) {
      console.error('[Olobuild] Undo/Redo restore failed:', e);
    } finally {
      isProgrammatic = false;
    }
  }

  function pushState() {
    if (isProgrammatic) return;
    const state = snapshot();
    if (undoStack.value.length > 0 && undoStack.value[undoStack.value.length - 1] === state) {
      return;
    }
    undoStack.value.push(state);
    if (undoStack.value.length > maxHistory) {
      undoStack.value.shift();
    }
    redoStack.value = [];
  }

  function undo() {
    if (undoStack.value.length === 0) return;
    redoStack.value.push(snapshot());
    const prevState = undoStack.value.pop();
    restore(prevState);
  }

  function redo() {
    if (redoStack.value.length === 0) return;
    undoStack.value.push(snapshot());
    const nextState = redoStack.value.pop();
    restore(nextState);
  }

  function initHistory() {
    undoStack.value = [];
    redoStack.value = [];

    // Clean up previous watcher if re-initialized
    if (stopWatch) stopWatch();

    stopWatch = watch(
      () => JSON.stringify(tilesStore.canvasTiles),
      (newVal, oldVal) => {
        if (isProgrammatic || newVal === oldVal) return;
        if (oldVal !== undefined) {
          if (undoStack.value.length === 0 || undoStack.value[undoStack.value.length - 1] !== oldVal) {
            undoStack.value.push(oldVal);
            if (undoStack.value.length > maxHistory) {
              undoStack.value.shift();
            }
            redoStack.value = [];
          }
        }
      },
      { deep: false }
    );
  }

  // Clean up watcher on unmount
  onUnmounted(() => {
    if (stopWatch) {
      stopWatch();
      stopWatch = null;
    }
  });

  // Keyboard shortcuts
  function handleKeyboard(event) {
    if ((event.ctrlKey || event.metaKey) && event.key === 'z') {
      event.preventDefault();
      if (event.shiftKey) {
        redo();
      } else {
        undo();
      }
    }
    if ((event.ctrlKey || event.metaKey) && event.key === 's') {
      event.preventDefault();
      if (builderStore.isDirty) {
        builderStore.saveTemplate();
      }
    }
    // Delete / Backspace: elimina tile selezionato
    if (event.key === 'Delete' || event.key === 'Backspace') {
      // Non eliminare se si sta editando testo
      const tag = event.target.tagName;
      if (tag === 'INPUT' || tag === 'TEXTAREA' || event.target.isContentEditable) return;
      const selectedId = builderStore.selectedTileId;
      if (selectedId) {
        event.preventDefault();
        tilesStore.removeTile(selectedId);
        builderStore.selectedTileId = null;
      }
    }
  }

  return {
    undoStack,
    redoStack,
    canUndo: () => undoStack.value.length > 0,
    canRedo: () => redoStack.value.length > 0,
    pushState,
    undo,
    redo,
    initHistory,
    handleKeyboard,
  };
}
