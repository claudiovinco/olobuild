/**
 * Minimal toast notification system for Olobuild builder.
 * No dependencies. Injects styles once, shows/hides messages.
 */
let container = null;

function ensureContainer() {
  if (container && document.body.contains(container)) return;
  container = document.createElement('div');
  container.className = 'olo-toast-container';
  container.setAttribute('role', 'status');
  container.setAttribute('aria-live', 'polite');
  container.setAttribute('aria-atomic', 'false');
  const style = document.createElement('style');
  style.textContent =
    '.olo-toast-container{position:fixed;bottom:20px;left:50%;transform:translateX(-50%);z-index:99999;display:flex;flex-direction:column;gap:8px;align-items:center;pointer-events:none}' +
    '.olo-toast{padding:10px 20px;border-radius:8px;color:#fff;font-size:13px;font-family:system-ui,sans-serif;pointer-events:auto;opacity:0;transform:translateY(12px);transition:opacity .3s,transform .3s;max-width:400px;text-align:center}' +
    '.olo-toast.olo-toast-show{opacity:1;transform:translateY(0)}' +
    '.olo-toast-error{background:#ef4444}' +
    '.olo-toast-success{background:#10b981}' +
    '.olo-toast-info{background:var(--olo-ui-accent,#e8622a)}' +
    '.olo-toast-warning{background:#f59e0b}';
  document.head.appendChild(style);
  document.body.appendChild(container);
}

function show(message, type = 'info', duration = 3000) {
  ensureContainer();
  const el = document.createElement('div');
  el.className = 'olo-toast olo-toast-' + type;
  el.setAttribute('role', type === 'error' ? 'alert' : 'status');
  el.textContent = message;
  container.appendChild(el);
  requestAnimationFrame(() => el.classList.add('olo-toast-show'));
  setTimeout(() => {
    el.classList.remove('olo-toast-show');
    setTimeout(() => el.remove(), 300);
  }, duration);
}

export function useToast() {
  return {
    info:    (msg, dur) => show(msg, 'info', dur),
    success: (msg, dur) => show(msg, 'success', dur),
    error:   (msg, dur) => show(msg, 'error', dur),
    warning: (msg, dur) => show(msg, 'warning', dur),
  };
}
