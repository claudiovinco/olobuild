import { shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile ParticleFX — sistema di particelle a tema (preset), canvas a tutta sezione
 * dietro al contenuto. Bucket C, famiglia C (canvas/generativo).
 *
 * Reference visivi (snippet runtime portati nel render PHP):
 *   - petals    → 36-tema-wedding.html   (petali che cadono, drift sinusoidale)
 *   - snow      → 42-tema-sci.html        (neve)
 *   - bubbles   → 50-tema-acquario.html   (bolle che salgono)
 *   - stars     → 49-tema-planetario.html (costellazioni con linee, interattivo al cursore)
 *   - confetti  → preset ONE-SHOT (burst con gravità e decadimento) — rif. Konami tema 60
 *
 * fields[]      → preset, conteggio, velocità, dimensione, vento, gravità,
 *                 connectLines (costellazioni), interactOnHover, contenuto slot
 * styleFields[] → palette (5 color picker), opacità, altezza minima sezione,
 *                 colore/sfondo, shadow, border
 *
 * Contratto §2: ogni numero/colore è un campo; nessun hardcode. UID scoped per
 * istanza (CSS + classe canvas). Render PHP = stato base SSR (contenuto visibile,
 * canvas aria-hidden dietro). Runtime rAF inline idempotente, multi-istanza,
 * IntersectionObserver per spegnere fuori viewport, dpr cap, prefers-reduced-motion,
 * pointer off su (hover:none)/(pointer:coarse).
 *
 * NB chiavi colore: niente field-type "array di colori" nativo nel sistema, quindi
 * la palette è esposta come 5 color picker (palette_1..palette_5) con default vuoto.
 * Il runtime raccoglie i non-vuoti in un array; se tutti vuoti usa la palette del preset.
 */
export default {
  type: 'particlefx',
  name: t('Particelle (ParticleFX)'),
  icon: 'dashicons-art',
  category: 'atmosphere',
  defaults: {
    scope: 'section',
    preset: 'petals',
    count: 40,
    speed: 1,
    size: 6,
    wind: 0.5,
    gravity: 1,
    connect_lines: false,
    connect_distance: 90,
    interact_on_hover: false,

    // Palette — 5 slot color picker, vuoti = usa palette del preset
    palette_1: '',
    palette_2: '',
    palette_3: '',
    palette_4: '',
    palette_5: '',
    particle_opacity: 80,

    // Contenuto sopra al canvas (slot HTML editabile inline)
    content: '',

    // Aspetto sezione
    min_height: 420,
    align_v: 'center',
    align_h: 'center',
    text_align: 'center',
    content_max_width: 720,
    padding_y: 80,
    bg_color: '',
    full_width: false,

    shadow: 'none',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  fields: [
    { type: 'separator', label: t('Ambito') },
    { key: 'scope', label: t('Riempi'), type: 'select', options: [
      { value: 'section', label: t('Sezione (sfondo della sezione ospite)') },
      { value: 'page',    label: t('Tutta la pagina (overlay fisso del sito)') },
    ], description: t('Tutta la pagina: le particelle vivono su tutto il documento e scorrono con esso, dietro al contenuto, senza mai bloccare i click.') },

    { type: 'separator', label: t('Sistema particelle') },
    { key: 'preset', label: t('Preset'), type: 'select', options: [
      { value: 'petals',   label: t('Petali (cadono)') },
      { value: 'snow',     label: t('Neve') },
      { value: 'bubbles',  label: t('Bolle (salgono)') },
      { value: 'stars',    label: t('Stelle / Costellazioni') },
      { value: 'confetti', label: t('Coriandoli (scoppio una tantum)') },
      { value: 'soccer',   label: t('Palloni da calcio') },
    ]},
    { key: 'count', label: t('Numero particelle'), type: 'range', min: 5, max: 300, step: 5,
      description: t('Su mobile il numero viene ridotto automaticamente per le prestazioni.') },
    { key: 'speed', label: t('Velocità'), type: 'range', min: 0.1, max: 4, step: 0.1 },
    { key: 'size', label: t('Dimensione (px)'), type: 'range', min: 1, max: 24, step: 1 },
    { key: 'wind', label: t('Vento (deriva orizzontale)'), type: 'range', min: 0, max: 3, step: 0.1 },
    { key: 'gravity', label: t('Gravità / spinta verticale'), type: 'range', min: 0, max: 3, step: 0.1,
      description: t('Petali/neve/coriandoli cadono; le bolle salgono; le stelle restano sospese.') },

    { type: 'separator', label: t('Costellazioni') },
    { key: 'connect_lines', label: t('Collega con linee'), type: 'toggle',
      description: t('Disegna linee tra particelle vicine (effetto costellazione). Ideale col preset Stelle.') },
    { key: 'connect_distance', label: t('Distanza max linee (px)'), type: 'range', min: 40, max: 200, step: 5,
      condition: { field: 'connect_lines', op: 'eq', value: true } },

    { type: 'separator', label: t('Interazione') },
    { key: 'interact_on_hover', label: t('Reagisci al cursore'), type: 'toggle',
      description: t('Con le costellazioni: collega le stelle al puntatore. Altrimenti le particelle si scostano leggermente dal cursore. Disattivato su touch.') },
  ],

  styleFields: [
    { type: 'separator', label: t('Palette particelle') },
    { type: 'description', description: t('Lascia vuoti gli slot per usare i colori del preset. I colori impostati hanno la precedenza.') },
    { key: 'palette_1', label: t('Colore 1'), type: 'color' },
    { key: 'palette_2', label: t('Colore 2'), type: 'color' },
    { key: 'palette_3', label: t('Colore 3'), type: 'color' },
    { key: 'palette_4', label: t('Colore 4'), type: 'color' },
    { key: 'palette_5', label: t('Colore 5'), type: 'color' },
    { key: 'particle_opacity', label: t('Opacità particelle (%)'), type: 'range', min: 10, max: 100, step: 5 },

    { type: 'separator', label: t('Sezione') },
    { key: 'min_height', label: t('Altezza minima (px)'), type: 'range', min: 80, max: 1000, step: 10 },
    { key: 'padding_y', label: t('Padding verticale (px)'), type: 'range', min: 0, max: 200, step: 5 },
    { key: 'content_max_width', label: t('Larghezza max contenuto (px)'), type: 'range', min: 200, max: 1400, step: 10 },
    { key: 'align_v', label: t('Allineamento verticale'), type: 'select', options: [
      { value: 'flex-start', label: t('Alto') },
      { value: 'center', label: t('Centro') },
      { value: 'flex-end', label: t('Basso') },
    ]},
    { key: 'align_h', label: t('Allineamento orizzontale'), type: 'select', options: [
      { value: 'flex-start', label: t('Sinistra') },
      { value: 'center', label: t('Centro') },
      { value: 'flex-end', label: t('Destra') },
    ]},
    { key: 'text_align', label: t('Allineamento testo'), type: 'select', options: [
      { value: 'left', label: t('Sinistra') },
      { value: 'center', label: t('Centro') },
      { value: 'right', label: t('Destra') },
    ]},
    { key: 'bg_color', label: t('Colore sfondo'), type: 'color' },
    { key: 'full_width', label: t('Larghezza piena'), type: 'toggle' },

    ...shadowField,
    ...borderFields(),
  ],
};
