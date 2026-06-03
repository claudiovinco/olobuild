import { shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile Goo / Aurora — sfondo "metaball" decorativo per Section. Bucket C,
 * famiglia A (effetti guidati dal cursore). Scheda §4 GooBackground (tema 61) +
 * variante Aurora (temi 45/48 e tile-mancanti §2).
 *
 * Due modalità su un solo tile:
 *   - goo    → N blob di colore dentro un layer con filter:url(#goo-<UID>)
 *              (feGaussianBlur + feColorMatrix) che li FONDE come metaball;
 *              un blob extra insegue il cursore (le gocce si uniscono/staccano).
 *              Rif. 61-tema-profumeria.html, blocco "goo blobs".
 *   - aurora → stessi blob ma SENZA filtro goo: solo blur + blend, aloni morbidi
 *              che derivano lentamente (no metaball merge). Rif. 45/48.
 *
 * fields[]      → modalità, palette (2-5 color picker), numero blob, raggio
 *                 min/max, velocità deriva, intensità "goo" (alpha feColorMatrix),
 *                 segui-cursore, blend, sfocatura Aurora, opacità layer, slot
 *                 contenuto sopra (HTML editabile inline)
 * styleFields[] → layout sezione (altezza, padding, allineamenti), colore base,
 *                 full-width, shadow, border
 *
 * Contratto §2: ogni numero/colore = campo con default; nessun hardcode; colori
 * via color picker / token. UID scoped per istanza → CSS, @keyframes E id del
 * filtro SVG (#goo-<UID>) tutti prefissati: N istanze non si calpestano. Render
 * PHP = stato base SSR (blob già visibili come gradiente; contenuto nel DOM).
 * Runtime rAF inline idempotente (guard dataset), multi-istanza, spento fuori
 * viewport (IntersectionObserver). prefers-reduced-motion → blob fermi
 * (gradiente statico). (hover:none)/(pointer:coarse) → niente cursor-blob.
 * Layer solo decorativo: aria-hidden. Additivo: include il sistema bordi standard.
 *
 * NB palette: niente field-type "array di colori" nativo → 5 color picker
 * (color_1..color_5). Il runtime/PHP raccoglie i non-vuoti; se tutti vuoti usa
 * una palette token-first di default.
 */
export default {
  type: 'goo',
  name: t('Sfondo Goo / Aurora'),
  icon: 'dashicons-art',
  category: 'atmosphere',
  defaults: {
    scope: 'section',
    mode: 'goo',

    // Palette — 5 slot color picker; vuoti = palette token di default.
    color_1: 'var(--olo-color-primary)',
    color_2: 'var(--olo-color-secondary)',
    color_3: 'var(--olo-color-accent)',
    color_4: '',
    color_5: '',

    blob_count: 4,
    blob_size_min: 180,
    blob_size_max: 340,
    drift_speed: 0.5,
    goo_strength: 18,
    follow_cursor: true,
    cursor_blob_size: 260,
    aurora_blur: 60,
    blend_mode: 'normal',
    layer_opacity: 90,

    // Contenuto sopra al layer (slot HTML editabile inline)
    content: '',

    // Aspetto sezione
    min_height: 480,
    align_v: 'center',
    align_h: 'center',
    text_align: 'left',
    content_max_width: 720,
    padding_y: 100,
    base_color: '',
    full_width: false,

    shadow: 'none',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  fields: [
    { type: 'separator', label: t('Sfondo') },
    { key: 'mode', label: t('Modalità'), type: 'select', options: [
      { value: 'goo',    label: t('Goo (metaball che si fondono)') },
      { value: 'aurora', label: t('Aurora (blob sfumati, senza fusione)') },
    ], description: t('Goo: i blob si fondono come gocce (filtro SVG) e uno insegue il cursore. Aurora: aloni morbidi sfocati che derivano, senza fusione metaball.') },

    { type: 'separator', label: t('Palette') },
    { type: 'description', description: t('Lascia vuoti gli slot per usare i ruoli colore del tema. I colori impostati hanno la precedenza.') },
    { key: 'color_1', label: t('Colore 1'), type: 'color' },
    { key: 'color_2', label: t('Colore 2'), type: 'color' },
    { key: 'color_3', label: t('Colore 3'), type: 'color' },
    { key: 'color_4', label: t('Colore 4'), type: 'color' },
    { key: 'color_5', label: t('Colore 5'), type: 'color' },

    { type: 'separator', label: t('Blob') },
    { key: 'blob_count', label: t('Numero blob'), type: 'range', min: 3, max: 8, step: 1 },
    { key: 'blob_size_min', label: t('Dimensione minima (px)'), type: 'range', min: 60, max: 500, step: 10 },
    { key: 'blob_size_max', label: t('Dimensione massima (px)'), type: 'range', min: 100, max: 700, step: 10 },
    { key: 'drift_speed', label: t('Velocità deriva'), type: 'range', min: 0, max: 1, step: 0.05,
      description: t('0 = blob fermi. La deriva è un movimento sinusoidale lento.') },
    { key: 'layer_opacity', label: t('Opacità sfondo (%)'), type: 'range', min: 20, max: 100, step: 5 },

    { type: 'separator', label: t('Goo') },
    { key: 'goo_strength', label: t('Intensità fusione'), type: 'range', min: 8, max: 28, step: 1,
      condition: { field: 'mode', op: 'eq', value: 'goo' },
      description: t('Alpha del feColorMatrix: più alto = bordi più netti e fusione più marcata tra i blob vicini.') },
    { key: 'follow_cursor', label: t('Blob che segue il cursore'), type: 'toggle',
      condition: { field: 'mode', op: 'eq', value: 'goo' },
      description: t('Un blob extra insegue il puntatore con easing. Disattivato su touch.') },
    { key: 'cursor_blob_size', label: t('Dimensione blob cursore (px)'), type: 'range', min: 100, max: 500, step: 10,
      condition: { field: 'follow_cursor', op: 'eq', value: true } },

    { type: 'separator', label: t('Aurora') },
    { key: 'aurora_blur', label: t('Sfocatura (px)'), type: 'range', min: 10, max: 140, step: 5,
      condition: { field: 'mode', op: 'eq', value: 'aurora' },
      description: t('Quanto sono morbidi gli aloni. In modalità Aurora i blob non si fondono: si sfocano e si miscelano.') },
    { key: 'blend_mode', label: t('Fusione colori (blend)'), type: 'select', options: [
      { value: 'normal',     label: t('Normale') },
      { value: 'screen',     label: t('Screen (schiarisce)') },
      { value: 'lighten',    label: t('Lighten') },
      { value: 'overlay',    label: t('Overlay') },
      { value: 'soft-light', label: t('Soft Light') },
    ], condition: { field: 'mode', op: 'eq', value: 'aurora' } },
  ],

  // Decoratore a zero dimensioni: nessun controllo di layout/contenuto.
  styleFields: [],
};
