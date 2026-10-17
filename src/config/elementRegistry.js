/**
 * Element Registry - loads all element definitions and provides lookup API.
 * Uses Vite's glob import to auto-discover element files.
 */

const modules = import.meta.glob('./elements/*.js', { eager: true });

const elements = {};

for (const path in modules) {
  // Skip helper modules (file che iniziano con "_", es. _shared.js, _oloxShared.js)
  if (/\/_/.test(path)) continue;
  const def = modules[path].default;
  if (def && def.type) {
    elements[def.type] = def;
  }
}

/**
 * Una tile esterna può riusare i controlli di una nativa: `extends: 'progallery'`.
 * Campi e stile della nativa vengono dopo i suoi, tolte le chiavi in `omit` (e i loro
 * default); un gruppo con più chiavi (`keys`, come la tipografia) se ne va quando sono tolte
 * tutte, e un separatore rimasto senza controlli prima del successivo se ne va con loro. I
 * default si fondono, quelli della tile esterna vincono. Con `extendsWhen: { field, value,
 * default }` i controlli ereditati si vedono solo quando quel campo ha quel valore (le loro
 * condizioni `show` restano, in AND). Così una tile che rende una nativa (Ac — Galleria con
 * la Gallery Pro, Ac — Tour virtuale col Tour 360) ne ha sempre tutte le opzioni, aggiornate,
 * senza copiarle in un altro plugin.
 */
function inherit(def) {
  const base = def.extends ? elements[def.extends] : null;
  if (!base || base === def) return def;
  const omit = new Set(def.omit || []);
  const when = def.extendsWhen || null;
  const gate = when
    ? (s) => {
        const v = s && s[when.field] !== undefined ? s[when.field] : when.default;
        return Array.isArray(when.value) ? when.value.includes(v) : v === when.value;
      }
    : null;
  const keep = (f) => {
    if (!f) return false;
    if (f.key) return !omit.has(f.key);
    const keys = f.keys && typeof f.keys === 'object' ? Object.values(f.keys).filter(Boolean) : [];
    return !keys.length || !keys.every((k) => omit.has(k));
  };
  const prune = (list) => list.filter((f, i) => f.type !== 'separator' || (list[i + 1] && list[i + 1].type !== 'separator'));
  const wrap = (f) => {
    if (!gate) return f;
    const show = f.show;
    return { ...f, show: (s) => gate(s) && (typeof show === 'function' ? show(s) : true) };
  };
  const defaults = {};
  for (const k in base.defaults || {}) {
    if (!omit.has(k)) defaults[k] = base.defaults[k];
  }
  return {
    ...def,
    defaults: { ...defaults, ...(def.defaults || {}) },
    fields: [...(def.fields || []), ...prune((base.fields || []).filter(keep)).map(wrap)],
    styleFields: [...(def.styleFields || []), ...prune((base.styleFields || []).filter(keep)).map(wrap)],
  };
}

// External elements registered by other plugins via oloExternalData
if (window.oloExternalData && window.oloExternalData.elements) {
  const ext = window.oloExternalData.elements;
  for (const key in ext) {
    if (ext[key] && ext[key].type) {
      elements[ext[key].type] = inherit(ext[key]);
    }
  }
}

/**
 * Get element definition by type
 * @param {string} type - Element type (e.g. 'hero', 'button')
 * @returns {object|null} Element definition or null
 */
export function getElementDef(type) {
  return elements[type] || null;
}

/**
 * Get all element definitions
 * @returns {object[]} Array of element definitions
 */
export function getAllElements() {
  // hidden: true = tile ritirata dalla palette (deprecata/superata da feature
  // native) ma ancora risolvibile via getElementDef per i template salvati.
  return Object.values(elements).filter((el) => !el.hidden);
}

/**
 * Get element defaults for a given type
 * @param {string} type - Element type
 * @returns {object} Default settings
 */
export function getElementDefaults(type) {
  return elements[type]?.defaults || {};
}

/**
 * Get element fields for a given type
 * @param {string} type - Element type
 * @returns {object[]} Array of field definitions
 */
export function getElementFields(type) {
  return elements[type]?.fields || [];
}
