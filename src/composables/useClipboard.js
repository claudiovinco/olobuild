import { ref } from 'vue';
import { useToast } from './useToast.js';

const clipboard = ref(null);

export function useClipboard() {
  const toast = useToast();

  function generateId() {
    if (typeof crypto !== 'undefined' && typeof crypto.randomUUID === 'function') {
      return crypto.randomUUID();
    }
    return 'tile-' + Date.now().toString(36) + '-' + Math.random().toString(36).substr(2, 9);
  }

  function copyTile(tile) {
    const clone = JSON.parse(JSON.stringify(tile));
    removeIds(clone);
    clipboard.value = clone;

    try {
      localStorage.setItem('olo_clipboard', JSON.stringify(clone));
    } catch (e) {
      toast.warning('Clipboard locale pieno — incolla disponibile solo in questa sessione.');
    }
  }

  function pasteTile() {
    if (clipboard.value) return JSON.parse(JSON.stringify(clipboard.value));

    try {
      const stored = localStorage.getItem('olo_clipboard');
      if (stored) return JSON.parse(stored);
    } catch (e) { /* parse error */ }

    return null;
  }

  function hasClipboard() {
    if (clipboard.value !== null) return true;
    try {
      return !!localStorage.getItem('olo_clipboard');
    } catch (e) {
      return false;
    }
  }

  function removeIds(node) {
    node.id = generateId();
    if (Array.isArray(node.children)) node.children.forEach(removeIds);
  }

  return { copyTile, pasteTile, hasClipboard, clipboard };
}
