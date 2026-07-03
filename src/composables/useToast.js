/**
 * Minimal toast notification system for Olobuild builder.
 * No dependencies. Injects styles once, shows/hides messages.
 */
let container = null;
let liveRegion = null;

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
    '.olo-toast{display:flex;align-items:center;gap:14px;padding:10px 16px;border-radius:8px;color:#fff;font-size:13px;font-family:system-ui,sans-serif;pointer-events:auto;opacity:0;transform:translateY(12px);transition:opacity .3s,transform .3s;max-width:420px;text-align:center;box-shadow:0 4px 16px rgba(0,0,0,.18)}' +
    '.olo-toast.olo-toast-show{opacity:1;transform:translateY(0)}' +
    '.olo-toast-error{background:#ef4444}' +
    '.olo-toast-success{background:#10b981}' +
    '.olo-toast-info{background:var(--olo-ui-accent,#e8622a)}' +
    '.olo-toast-warning{background:#f59e0b}' +
    '.olo-toast-msg{flex:1 1 auto}' +
    '.olo-toast-action{flex:0 0 auto;background:rgba(255,255,255,.22);color:#fff;border:1px solid rgba(255,255,255,.5);' +
      'border-radius:5px;padding:3px 12px;font-size:12px;font-weight:600;cursor:pointer;white-space:nowrap;transition:background .15s}' +
    '.olo-toast-action:hover{background:rgba(255,255,255,.35)}' +
    '.olo-toast-action:focus-visible{outline:2px solid #fff;outline-offset:1px}';
  document.head.appendChild(style);
  document.body.appendChild(container);
}

// Live region invisibile per annunci screen-reader senza toast visibile
// (es. riordino/spostamento da tastiera che non merita un banner grafico).
function ensureLiveRegion() {
  if (liveRegion && document.body.contains(liveRegion)) return;
  liveRegion = document.createElement('div');
  liveRegion.setAttribute('aria-live', 'polite');
  liveRegion.setAttribute('aria-atomic', 'true');
  liveRegion.style.cssText =
    'position:absolute;width:1px;height:1px;margin:-1px;padding:0;overflow:hidden;' +
    'clip:rect(0 0 0 0);clip-path:inset(50%);border:0;white-space:nowrap';
  document.body.appendChild(liveRegion);
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

/**
 * Toast con un pulsante azione (es. "Annulla" dopo un'eliminazione).
 * Durata più lunga (default 6s) per dare tempo al click. Cliccando l'azione
 * il toast si chiude subito.
 */
function showAction(message, actionLabel, onAction, type = 'info', duration = 6000) {
  ensureContainer();
  const el = document.createElement('div');
  el.className = 'olo-toast olo-toast-' + type;
  el.setAttribute('role', 'status');

  const msg = document.createElement('span');
  msg.className = 'olo-toast-msg';
  msg.textContent = message;
  el.appendChild(msg);

  const btn = document.createElement('button');
  btn.className = 'olo-toast-action';
  btn.type = 'button';
  btn.textContent = actionLabel;
  el.appendChild(btn);

  let closed = false;
  const dismiss = () => {
    if (closed) return;
    closed = true;
    el.classList.remove('olo-toast-show');
    setTimeout(() => el.remove(), 300);
  };
  btn.addEventListener('click', () => {
    try { if (typeof onAction === 'function') onAction(); } finally { dismiss(); }
  });

  container.appendChild(el);
  requestAnimationFrame(() => el.classList.add('olo-toast-show'));
  setTimeout(dismiss, duration);
}

export function useToast() {
  return {
    info:    (msg, dur) => show(msg, 'info', dur),
    success: (msg, dur) => show(msg, 'success', dur),
    error:   (msg, dur) => show(msg, 'error', dur),
    warning: (msg, dur) => show(msg, 'warning', dur),
    action:  (msg, actionLabel, onAction, dur) => showAction(msg, actionLabel, onAction, 'info', dur),
    announce: (msg) => { ensureLiveRegion(); liveRegion.textContent = ''; requestAnimationFrame(() => { liveRegion.textContent = msg; }); },
  };
}
