import { onMounted, onUnmounted } from 'vue';

export function useKeyboardShortcuts(handlers) {
  // handlers = { 'ctrl+s': fn, 'ctrl+z': fn, 'ctrl+shift+z': fn, 'delete': fn, 'ctrl+d': fn, 'ctrl+c': fn, 'ctrl+v': fn, 'escape': fn }

  function onKeyDown(e) {
    // Don't trigger shortcuts when typing in input/textarea/contenteditable
    const tag = e.target.tagName;
    const editable = e.target.isContentEditable;
    if ((tag === 'INPUT' || tag === 'TEXTAREA' || editable) && e.key !== 'Escape') return;

    const key = buildKey(e);
    const handler = handlers[key];
    if (handler) {
      e.preventDefault();
      handler(e);
    }
  }

  function buildKey(e) {
    const parts = [];
    if (e.ctrlKey || e.metaKey) parts.push('ctrl');
    if (e.shiftKey) parts.push('shift');
    if (e.altKey) parts.push('alt');
    parts.push(e.key.toLowerCase());
    return parts.join('+');
  }

  onMounted(() => document.addEventListener('keydown', onKeyDown));
  onUnmounted(() => document.removeEventListener('keydown', onKeyDown));

  return { onKeyDown };
}
