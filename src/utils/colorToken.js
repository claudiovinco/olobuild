/**
 * Token colore — lettura condivisa fra i campi dell'inspector.
 *
 * I token `var(--olo-color-*)` sono definiti su `.olo-template`, cioè DENTRO il
 * canvas: nel pannello di destra non risolvono. Ogni campo che deve mostrare
 * un'anteprima del colore (la pastiglia di FieldColor, il pallino della sintesi
 * tipografica) deve quindi risolverli in JS, leggendo la palette dallo store.
 *
 * Qui sta quella lettura, una volta sola: prima viveva dentro FieldColor.vue e
 * non era riusabile da nessun altro.
 */

// Ruoli del tema selezionabili come token, oltre ai globali custom.
export const ROLE_SWATCHES = [
  { id: 'primary', label: 'Primary' }, { id: 'secondary', label: 'Secondary' },
  { id: 'success', label: 'Success' }, { id: 'warning', label: 'Warning' },
  { id: 'danger', label: 'Danger' }, { id: 'link', label: 'Link' },
  { id: 'text', label: 'Testo' }, { id: 'background', label: 'Sfondo' },
  { id: 'muted', label: 'Superficie' }, { id: 'border', label: 'Bordo' },
];

/**
 * Scompone un token colore nelle sue due parti: l'id e l'eventuale RISERVA.
 *   var(--olo-color-dark)            -> { id: 'dark', fallback: '' }
 *   var(--olo-color-dark, #16263d)   -> { id: 'dark', fallback: '#16263d' }
 * Restituisce null se non e' un token.
 *
 * La riserva si prende con `(.+)` e non con `[^)]+`: dev'essere in grado di
 * attraversare una parentesi, perche' una riserva puo' essere a sua volta un
 * token — `var(--olo-color-accent, var(--olo-color-primary))`.
 */
export function tokenParts(val) {
  const m = /^var\(\s*--olo-color-([a-z0-9_-]+)\s*(?:,\s*(.+))?\)$/i.exec(String(val || '').trim());
  if (!m) return null;
  return { id: m[1].toLowerCase(), fallback: (m[2] || '').trim() };
}

/**
 * Swatch disponibili: ruoli del tema (olo_styles.colors) + globali custom.
 */
export function buildSwatchColors(stylesStore) {
  const c = stylesStore?.colors || {};
  const roleIds = new Set(ROLE_SWATCHES.map(r => r.id));
  const roles = ROLE_SWATCHES
    .filter(r => c[r.id])
    .map(r => ({ id: r.id, label: r.label, value: c[r.id], quick: false }));
  const globals = (stylesStore?.globalColors || [])
    .filter(g => g && g.id && !roleIds.has(g.id))
    .map(g => ({ id: g.id, label: g.label || g.id, value: g.value, quick: !!g.quick }));
  return [...roles, ...globals];
}

/**
 * Valore colore → stringa CSS dipingibile nell'inspector.
 * Un token non definito nella palette vale la sua riserva; se non ne ha,
 * torna '' — meglio nessun pallino che un pallino nero (il nero era il vecchio
 * ripiego di FieldColor, e mostrava centinaia di pastiglie sbagliate).
 */
export function resolveColorToken(val, stylesStore) {
  const v = String(val || '').trim();
  if (!v) return '';
  const t = tokenParts(v);
  if (!t) return v;
  const swatches = buildSwatchColors(stylesStore);
  const hit = swatches.find(s => s.id === t.id);
  if (hit?.value) return hit.value;
  if (t.fallback) return resolveColorToken(t.fallback, stylesStore);
  return '';
}
