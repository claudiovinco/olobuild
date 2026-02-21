import { ref, watch } from 'vue';
import { useTilesStore } from '@/stores/tiles';
import { useBuilderStore } from '@/stores/builder';

const undoStack = ref([]);
const redoStack = ref([]);
const maxHistory = 50;
let isProgrammatic = false;

export function useHistory() {
  const tilesStore = useTilesStore();
  const builderStore = useBuilderStore();

  function snapshot() {
    return JSON.stringify(tilesStore.canvasTiles);
  }

  function restore(state) {
    isProgrammatic = true;
    tilesStore.setCanvasTiles(JSON.parse(state));
    builderStore.isDirty = true;
    isProgrammatic = false;
  }

  function pushState() {
    if (isProgrammatic) return;
    const state = snapshot();
    // Don't push if identical to last state
    if (undoStack.value.length > 0 && undoStack.value[undoStack.value.length - 1] === state) {
      return;
    }
    undoStack.value.push(state);
    if (undoStack.value.length > maxHistory) {
      undoStack.value.shift();
    }
    // Clear redo stack on new action
    redoStack.value = [];
  }

  function undo() {
    if (undoStack.value.length === 0) return;
    // Save current state to redo
    redoStack.value.push(snapshot());
    // Restore previous state
    const prevState = undoStack.value.pop();
    restore(prevState);
  }

  function redo() {
    if (redoStack.value.length === 0) return;
    // Save current state to undo
    undoStack.value.push(snapshot());
    // Restore next state
    const nextState = redoStack.value.pop();
    restore(nextState);
  }

  function initHistory() {
    // Save initial empty state
    undoStack.value = [];
    redoStack.value = [];

    // Watch for changes and auto-push state
    watch(
      () => JSON.stringify(tilesStore.canvasTiles),
      (newVal, oldVal) => {
        if (isProgrammatic || newVal === oldVal) return;
        // Push the OLD state before the change
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
