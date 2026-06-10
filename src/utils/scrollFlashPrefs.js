var KEY = 'olo_scroll_flash';
var DEFAULTS = {
  color: '#E8622A', // arancio chrome builder (--olo-ui-accent); hex pieno: viaggia verso l'iframe canvas dove la var non esiste
  effect: 'flash',
  size: 6,
  duration: 1000,
  pulse_count: 2,
  scroll_ms: 500,
};

export function loadScrollFlashPrefs() {
  try {
    var raw = localStorage.getItem(KEY);
    if (raw) {
      var parsed = JSON.parse(raw);
      return Object.assign({}, DEFAULTS, parsed);
    }
  } catch (e) { /* ignore */ }
  return Object.assign({}, DEFAULTS);
}

export function saveScrollFlashPrefs(prefs) {
  try {
    localStorage.setItem(KEY, JSON.stringify(prefs));
  } catch (e) { /* ignore */ }
}
