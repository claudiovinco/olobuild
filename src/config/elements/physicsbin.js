import { shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile PhysicsBin — giocattoli trascinabili con gravità, rimbalzo e collisioni.
 * (famiglia C, bucket C · rif. handoff-tile-speciali/temi/69-tema-toy-store.html "tiny physics bin")
 *
 *   fields[]      → items[] (forma/colore/raggio/glifo/immagine), gravity, restitution,
 *                   friction, walls, spawn, maxItems
 *   styleFields[] → preset, bg_color, height (px), border_radius, shadow + borderFields
 *
 * SSR: il PHP dispone N .toy staticamente (decorativo, aria-hidden) → già visibile senza JS.
 * Runtime: integratore DOM (no canvas) con drag&throw via Pointer Events; off su touch/coarse,
 * ramo statico per prefers-reduced-motion, loop spento fuori viewport (IntersectionObserver).
 */
export default {
  type: 'physicsbin',
  name: t('Cesto Fisico'),
  icon: 'dashicons-games',
  category: 'interactive',
  defaults: {
    preset: 'custom',
    bg: { type: 'none' },
    items: [
      { id: 'pb-1', shape: 'circle', color: '#E63E3E', radius: '46', glyph: '★', image: '' },
      { id: 'pb-2', shape: 'square', color: '#2E6BE6', radius: '40', glyph: 'A', image: '' },
      { id: 'pb-3', shape: 'circle', color: '#F4B400', radius: '34', glyph: '',  image: '' },
      { id: 'pb-4', shape: 'circle', color: '#2BA65A', radius: '50', glyph: 'B', image: '' },
      { id: 'pb-5', shape: 'square', color: '#8B53D6', radius: '36', glyph: 'C', image: '' },
      { id: 'pb-6', shape: 'circle', color: '#E63E3E', radius: '32', glyph: '',  image: '' },
      { id: 'pb-7', shape: 'circle', color: '#2E6BE6', radius: '44', glyph: '1', image: '' },
      { id: 'pb-8', shape: 'star',   color: '#F4B400', radius: '42', glyph: '',  image: '' },
      { id: 'pb-9', shape: 'square', color: '#2BA65A', radius: '30', glyph: '2', image: '' },
    ],
    gravity: '0.55',
    restitution: '0.74',
    friction: '0.992',
    walls: true,
    spawn: 'random',
    max_items: '14',
    bg_color: '',
    height: '480',
    border_radius: { tl: 28, tr: 28, br: 28, bl: 28 },
    shadow: 'lg',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { type: 'separator', label: t('Giocattoli') },
    { key: 'items', label: t('Oggetti'), type: 'content-items',
      itemFields: [
        { key: 'shape', label: t('Forma'), type: 'select', options: [
          { value: 'circle', label: t('Cerchio') },
          { value: 'square', label: t('Quadrato') },
          { value: 'star',   label: t('Stella') },
        ]},
        { key: 'color', label: t('Colore'), type: 'color' },
        { key: 'radius', label: t('Raggio (px)'), type: 'range', min: 16, max: 80, step: 2 },
        { key: 'glyph', label: t('Glifo (lettera/numero)'), type: 'text',
          description: t('Un carattere mostrato al centro. Ignorato se imposti un\'immagine.') },
        { key: 'image', label: t('Immagine (opzionale)'), type: 'image',
          description: t('Sostituisce colore e glifo: l\'immagine riempie l\'oggetto.') },
      ],
      newItemDefaults: { shape: 'circle', color: '#E63E3E', radius: '40', glyph: '', image: '' },
      itemLabel: 'Oggetto',
    },

    { type: 'separator', label: t('Fisica') },
    { key: 'gravity', label: t('Gravità'), type: 'range', min: 0, max: 2, step: 0.05,
      description: t('Accelerazione verso il basso. 0 = galleggiamento, alto = caduta rapida.') },
    { key: 'restitution', label: t('Rimbalzo (elasticità)'), type: 'range', min: 0, max: 1, step: 0.02,
      description: t('0 = niente rimbalzo, 1 = rimbalzo quasi perfetto.') },
    { key: 'friction', label: t('Attrito (smorzamento)'), type: 'range', min: 0.9, max: 1, step: 0.002,
      description: t('1 = nessun attrito, valori più bassi rallentano gli oggetti.') },
    { key: 'walls', label: t('Pareti (bordi che contengono)'), type: 'toggle',
      description: t('Se attivo, gli oggetti rimbalzano contro i 4 lati del cesto.') },
    { key: 'spawn', label: t('Disposizione iniziale'), type: 'select', options: [
      { value: 'random', label: t('Casuale') },
      { value: 'grid',   label: t('Griglia') },
    ]},
    { key: 'max_items', label: t('Numero massimo di oggetti'), type: 'range', min: 3, max: 24, step: 1,
      description: t('Limita gli oggetti attivi per non appesantire la pagina.') },
  ],

  // ─── STILE ─────────────────────────────────────────────────
  styleFields: [
    { type: 'separator', label: t('Preset stilistico') },
    { key: 'preset', label: t('Stile'), type: 'select', options: [
      { value: 'modern-clean',    label: t('Modern Clean') },
      { value: 'minimal-mono',    label: t('Minimal Mono') },
      { value: 'magazine-bold',   label: t('Magazine Bold') },
      { value: 'playful-toy',     label: t('Playful Toy') },
      { value: 'soft-pastel',     label: t('Soft Pastel') },
      { value: 'glass-frosted',   label: t('Glass Frosted') },
      { value: 'neon-glow',       label: t('Neon Glow') },
      { value: 'brutalist-stamp', label: t('Brutalist Stamp') },
      { value: 'gradient-aurora', label: t('Gradient Aurora') },
      { value: 'sticker-fun',     label: t('Sticker Fun') },
      { value: 'retro-terminal',  label: t('Retro Terminal') },
      { value: 'tilt-3d',         label: t('3D Tilt') },
      { value: 'custom',          label: t('Personalizzato') },
    ]},

    { type: 'separator', label: t('Aspetto cesto') },
    { key: 'bg_color', label: t('Colore sfondo'), type: 'color',
      description: t('Lascia vuoto per il motivo a righe diagonali di default.') },
    { key: 'height', label: t('Altezza (px)'), type: 'range', min: 240, max: 800, step: 10 },
    { key: 'border_radius', label: t('Raggio bordi'), type: 'border-radius' },

    ...shadowField,
    ...borderFields(),
  ],
};
