var KEY = 'olo_scroll_flash';
var DEFAULTS = {
  color: '#6366F1',
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
