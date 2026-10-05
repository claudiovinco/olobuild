// Lo sfondo di un preset del menu Stile come oggetto sfondo del contenitore (style.bg).
//
// I preset (TILE_PRESETS / BASE_THEME_PRESETS) danno il colore come stringa: un colore o un
// `linear-gradient(<n>deg, c1, c2…)`. Scritto così com'era, in style.bg_color, perdeva contro lo
// «Sfondo: Nessuno» che quasi ogni tile ha di partenza (settings.bg) e il contenitore restava
// trasparente. Lo usa applyTilePresetTheme() (BuilderInspector.vue).

/**
 * @param {string} valore Colore del preset (anche gradiente lineare).
 * @returns {object} { type: 'none' } | { type: 'solid', color } | { type: 'gradient', gradient }
 */
export function sfondoDaPreset(valore) {
  const v = String(valore || '').trim();
  if (!v) return { type: 'none' };
  const m = /^linear-gradient\(\s*(-?\d+(?:\.\d+)?)deg\s*,(.+)\)$/i.exec(v);
  if (m) {
    // Virgole fuori dalle parentesi: rgba(…) resta un colore solo.
    const colori = m[2].split(/,(?![^(]*\))/).map((c) => c.trim()).filter(Boolean);
    if (colori.length >= 2) {
      return {
        type: 'gradient',
        gradient: {
          type: 'linear',
          angle: Number(m[1]),
          stops: colori.map((color, i) => ({ color, position: Math.round((i * 100) / (colori.length - 1)) })),
        },
      };
    }
  }
  return { type: 'solid', color: v };
}
