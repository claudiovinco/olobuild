/**
 * Element Registry - loads all element definitions and provides lookup API.
 * Uses Vite's glob import to auto-discover element files.
 */

const modules = import.meta.glob('./elements/*.js', { eager: true });

const elements = {};

for (const path in modules) {
  // Skip _shared.js
  if (path.includes('_shared')) continue;
  const def = modules[path].default;
  if (def && def.type) {
    elements[def.type] = def;
  }
}

// External elements registered by other plugins via oloExternalData
if (window.oloExternalData && window.oloExternalData.elements) {
  const ext = window.oloExternalData.elements;
  for (const key in ext) {
    if (ext[key] && ext[key].type) {
      elements[ext[key].type] = ext[key];
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
  return Object.values(elements);
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
