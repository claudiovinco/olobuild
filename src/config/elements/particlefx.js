import { borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
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
 * styleFields[] → palette (5 color picker), opacità, dimensione, altezza minima
 *                 della sezione vuota
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
    confetti_start: 'load',
    confetti_duration: 3,
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

  // Come nasce dalla palette: una costellazione discreta dietro alla sezione, punti nel primario
  // legati da linee sottili nel secondario che derivano piano, senza seguire il cursore. I colori
  // vengono dal tema (la palette dei preset è fissa).
  partenza: {
    preset: 'stars',
    count: 70,
    size: 10,
    speed: 0.6,
    wind: 0.4,
    gravity: 0.6,
    connect_lines: true,
    connect_distance: 120,
    palette_1: 'var(--olo-color-primary)',
    palette_2: 'var(--olo-color-secondary)',
    particle_opacity: 70,
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
    { key: 'wind', label: t('Vento (deriva orizzontale)'), type: 'range', min: 0, max: 3, step: 0.1 },
    { key: 'gravity', label: t('Gravità / spinta verticale'), type: 'range', min: 0, max: 3, step: 0.1,
      description: t('Petali/neve/coriandoli cadono; le bolle salgono; le stelle restano sospese.') },
    { key: 'confetti_start', label: t('Quando parte'), type: 'select', options: [
      { value: 'load', label: t("All'apertura della pagina") },
      { value: 'view', label: t('Quando la tile entra nello schermo') },
    ], condition: [ { field: 'preset', value: 'confetti' }, { field: 'scope', value: 'page' } ],
      description: t('Su tutta la pagina lo scoppio parte di serie appena la pagina si apre: se la tile sta più in basso, scegli «Quando la tile entra nello schermo» perché parta quando il visitatore ci arriva.') },
    { key: 'confetti_duration', label: t('Durata'), type: 'range', min: 1.5, max: 12, step: 0.5, unit: 's',
      condition: { field: 'preset', value: 'confetti' } },

    { type: 'separator', label: t('Costellazioni') },
    { key: 'connect_lines', label: t('Collega con linee'), type: 'toggle',
      description: t('Disegna linee tra particelle vicine (effetto costellazione). Ideale col preset Stelle.') },
    { key: 'connect_distance', label: t('Distanza max linee'), type: 'range', min: 40, max: 200, step: 5,
      condition: { field: 'connect_lines', op: 'eq', value: true } },

    { type: 'separator', label: t('Interazione') },
    { key: 'interact_on_hover', label: t('Reagisci al cursore'), type: 'toggle',
      description: t('Con le costellazioni: collega le stelle al puntatore. Altrimenti le particelle si scostano leggermente dal cursore. Disattivato su touch.') },
  ],

  // La tile è un decoratore a zero dimensioni: il canvas diventa lo sfondo della sezione che la
  // ospita. Padding, larghezza del contenuto, allineamenti, colore di sfondo, ombra e bordo erano
  // offerti qui ma il renderer non li stampava (non c'è un riquadro su cui disegnarli): tolti.
  // Le chiavi restano nei defaults per i template salvati. Contenuto, sfondo e spazi li dà la sezione.
  styleFields: [
    { type: 'separator', label: t('Aspetto') },
    { type: 'description', description: t('Lascia vuoti gli slot per usare i colori del preset. I colori impostati hanno la precedenza.') },
    { key: 'palette_1', label: t('Colore 1'), type: 'color' },
    { key: 'palette_2', label: t('Colore 2'), type: 'color' },
    { key: 'palette_3', label: t('Colore 3'), type: 'color' },
    { key: 'palette_4', label: t('Colore 4'), type: 'color' },
    { key: 'palette_5', label: t('Colore 5'), type: 'color' },
    { key: 'particle_opacity', label: t('Opacità particelle'), type: 'range', min: 10, max: 100, step: 5 },

    { type: 'separator', label: t('Forma') },
    { key: 'size', label: t('Dimensione'), type: 'range', min: 1, max: 24, step: 1 },
    // Agisce solo nel ramo «Sezione» del runtime: con «Tutta la pagina» non c'è una sezione da
    // allungare e il campo non farebbe niente.
    { key: 'min_height', label: t('Altezza minima'), type: 'range', min: 80, max: 1000, step: 10,
      condition: { field: 'scope', op: 'neq', value: 'page' },
      description: t('Vale solo se la sezione che ospita le particelle è vuota: le dà questa altezza perché l\'effetto si veda. Contenuto, sfondo e spazi si impostano sulla sezione.') },
  ],
};
