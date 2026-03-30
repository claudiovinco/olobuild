import { onUnmounted } from 'vue';

const FOCUSABLE_SELECTOR = 'a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])';

export function useFocusTrap(containerRef, { onEscape } = {}) {
  let previousFocus = null;

  function activate() {
    previousFocus = document.activeElement;
    const el = containerRef.value;
    if (!el) return;

    const focusable = el.querySelectorAll(FOCUSABLE_SELECTOR);
    if (focusable.length) focusable[0].focus();

    el._trapKeydown = function(e) {
      // Escape key
      if (e.key === 'Escape') {
        e.preventDefault();
        e.stopPropagation();
        if (typeof onEscape === 'function') onEscape();
        return;
      }

      if (e.key !== 'Tab') return;

      // Re-query focusable elements (DOM may have changed)
      const currentFocusable = el.querySelectorAll(FOCUSABLE_SELECTOR);
      if (!currentFocusable.length) return;

      const first = currentFocusable[0];
      const last = currentFocusable[currentFocusable.length - 1];

      if (e.shiftKey) {
        if (document.activeElement === first) { e.preventDefault(); last.focus(); }
      } else {
        if (document.activeElement === last) { e.preventDefault(); first.focus(); }
      }
    };
    el.addEventListener('keydown', el._trapKeydown);
  }

  function deactivate() {
    const el = containerRef.value;
    if (el && el._trapKeydown) {
      el.removeEventListener('keydown', el._trapKeydown);
      delete el._trapKeydown;
    }
    if (previousFocus && typeof previousFocus.focus === 'function') {
      try { previousFocus.focus(); } catch (e) { /* element may have been removed */ }
    }
    previousFocus = null;
  }

  onUnmounted(deactivate);
  return { activate, deactivate };
}
