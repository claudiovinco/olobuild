import { shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile ScrollScrub — pin verticale → scorrimento orizzontale.
 *
 * Effetto "Section · ScrollScrub" (rif. handoff-tile-speciali/temi/62-tema-libreria-indie.html
 * blocco "pinned horizontal shelf"; anche 35-tema-immobiliare.html "HorizontalScroll · portfolio").
 * Bucket C / famiglia B.
 *
 * Una sezione alta N×100vh resta "incollata" (sticky) mentre lo scroll verticale viene
 * rimappato a translateX di una traccia orizzontale (reel di progetti, dorsi di libri…).
 * Con scrubbar opzionale.
 *
 * Anatomia:
 *   outer { height: scrollLength*100vh } → .pin { position:sticky; top:0; height:100vh; overflow:hidden }
 *   → .track { display:flex; will-change:transform }. Progress bar opzionale.
 *
 * Contratto §2:
 *   - Parametrico: ogni numero/colore = campo editor con default; nessun hardcode.
 *   - UID scoped: tutto il CSS + @keyframes prefissati con .olo-scrub-<id> (N istanze non si calpestano).
 *   - SSR: tutti gli item sono renderizzati server-side e visibili senza JS (la traccia diventa
 *     scroll orizzontale nativo).
 *   - Runtime INLINE nel render() PHP: su scroll p=clamp((-rect.top)/(outer.h-vh),0..1);
 *     track.x=-p*(track.scrollW-vw); ricalcola max su resize; passive:true; IntersectionObserver.
 *   - prefers-reduced-motion / no-JS / mobile → la traccia diventa scroll orizzontale nativo
 *     (overflow-x:auto), niente pin; traccia focusabile/scrollabile da tastiera.
 *   - Additivo: chiavi salvate invariate; riusa build_border_*_css come il marquee.
 *
 *   fields[]      → items (repeater), scroll_length, align, gap, easing, show_progress,
 *                   pause_on_reduced_motion
 *   styleFields[] → item_width, item_min_height, round, padding interno, colori default,
 *                   progress_color, shadow + borderFields
 */
export default {
  type: 'scrollscrub',
  name: t('Scorrimento orizzontale (ScrollScrub)'),
  icon: 'dashicons-leftright',
  category: 'layout',

  defaults: {
    // ── Comportamento pin/scroll ──
    scroll_length: 3,            // × viewport (2–6): più alto = scroll più lungo/lento
    align: 'center',            // start / center: allineamento verticale della traccia nel pin
    gap: 24,                    // px tra gli item
    easing: 'linear',           // mappatura scroll→translateX
    show_progress: true,        // scrubbar in basso
    pause_on_reduced_motion: true, // reduced-motion → scroll orizzontale nativo (niente pin)

    // ── Testata (opzionale) ──
    heading: 'Scorri in orizzontale',
    kicker: 'scroll → orizzontale',

    // ── Aspetto item (default, sovrascrivibili per item) ──
    item_width: 360,            // px (desktop)
    item_min_height: 460,       // px (desktop)
    round: 14,
    item_padding: 0,            // px (0 = immagine a tutto bordo)
    item_bg_default: '',        // vuoto → token di superficie
    text_color_default: '',     // vuoto → token testo
    progress_color: '',         // vuoto → accent
    show_number: true,

    shadow: 'custom',
    shadow_h: '0',
    shadow_v: '14',
    shadow_blur: '34',
    shadow_spread: '-16',
    shadow_color: 'rgba(0,0,0,0.32)',
    shadow_inset: false,

    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,

    // ── Item (repeater) ──
    items: [
      { title: 'Primo progetto', subtitle: 'Categoria · luogo', text: 'Una breve descrizione del progetto. La traccia scorre in orizzontale mentre scendi nella pagina.', media: '', media_label: 'Immagine', color: '', text_color: '' },
      { title: 'Secondo progetto', subtitle: 'Categoria · luogo', text: 'Aggiungi quanti item vuoi: scorrono tutti lungo un unico nastro orizzontale.', media: '', media_label: 'Immagine', color: '', text_color: '' },
      { title: 'Terzo progetto', subtitle: 'Categoria · luogo', text: 'Su mobile e con “meno movimento” attivo la traccia diventa uno scroll orizzontale nativo.', media: '', media_label: 'Immagine', color: '', text_color: '' },
      { title: 'Quarto progetto', subtitle: 'Categoria · luogo', text: 'Riordina, rimuovi o personalizza i singoli item dal pannello a destra.', media: '', media_label: 'Immagine', color: '', text_color: '' },
      { title: 'Quinto progetto', subtitle: 'Categoria · luogo', text: 'La scrubbar in basso mostra il progresso dello scorrimento.', media: '', media_label: 'Immagine', color: '', text_color: '' },
    ],
  },

  fields: [
    { key: 'items', label: t('Elementi'), type: 'content-items',
      itemLabel: t('Elemento'),
      defaults: { title: 'Nuovo elemento', subtitle: 'Categoria · luogo', text: 'Testo dell\'elemento…', media: '', media_label: 'Immagine', color: '', text_color: '' },
      itemFields: [
        { key: 'title',       label: t('Titolo'),                        type: 'text' },
        { key: 'subtitle',    label: t('Sottotitolo'),                   type: 'text' },
        { key: 'text',        label: t('Testo'),                         type: 'editor', mode: 'block' },
        { key: 'media',       label: t('Immagine'),                      type: 'image' },
        { key: 'media_label', label: t('Etichetta segnaposto immagine'), type: 'text' },
        { key: 'color',       label: t('Colore sfondo elemento'),        type: 'color' },
        { key: 'text_color',  label: t('Colore testo elemento'),         type: 'color' },
      ],
    },

    { type: 'separator', label: t('Testata') },
    { key: 'heading', label: t('Titolo sezione'), type: 'text' },
    { key: 'kicker', label: t('Etichetta (kicker)'), type: 'text' },

    { type: 'separator', label: t('Scorrimento') },
    { key: 'scroll_length', label: t('Lunghezza scroll (× schermo)'), type: 'range', min: 2, max: 6, step: 0.5,
      description: t('Quanto deve scendere la pagina per percorrere tutta la traccia. Più alto = scorrimento orizzontale più lento e lungo.') },
    { key: 'align', label: t('Allineamento verticale'), type: 'select', options: [
      { value: 'center', label: t('Centro') },
      { value: 'start',  label: t('In alto') },
    ]},
    { key: 'gap', label: t('Spazio tra elementi (px)'), type: 'range', min: 0, max: 80, step: 2 },
    { key: 'easing', label: t('Curva di scorrimento'), type: 'select', options: [
      { value: 'linear',   label: t('Lineare (segue lo scroll)') },
      { value: 'ease',     label: t('Morbida (ease-in-out)') },
      { value: 'ease-out', label: t('Decelera (ease-out)') },
    ]},
    { key: 'show_progress', label: t('Mostra barra di progresso'), type: 'toggle' },
    { key: 'pause_on_reduced_motion', label: t('Rispetta “riduci movimento”'), type: 'toggle',
      description: t('Con prefers-reduced-motion (o su mobile) la traccia diventa uno scroll orizzontale nativo, senza ancoraggio verticale. Consigliato attivo per accessibilità.') },
  ],

  styleFields: [
    { type: 'separator', label: t('Aspetto elementi') },
    { key: 'item_width', label: t('Larghezza elemento (px)'), type: 'range', min: 160, max: 720, step: 10, responsive: true },
    { key: 'item_min_height', label: t('Altezza elemento (px)'), type: 'range', min: 200, max: 760, step: 10, responsive: true },
    { key: 'round', label: t('Raggio angoli (px)'), type: 'border-radius' },
    { key: 'item_padding', label: t('Padding interno (px)'), type: 'range', min: 0, max: 64, step: 2,
      description: t('0 = immagine a tutto bordo con testo sovrapposto in basso.') },

    { type: 'separator', label: t('Colori predefiniti') },
    { key: 'item_bg_default', label: t('Sfondo elemento (default)'), type: 'color',
      description: t('Usato per gli elementi senza colore proprio. Vuoto → superficie del tema.') },
    { key: 'text_color_default', label: t('Testo elemento (default)'), type: 'color' },
    { key: 'show_number', label: t('Mostra numero progressivo'), type: 'toggle' },
    { key: 'progress_color', label: t('Colore barra di progresso'), type: 'color',
      condition: { field: 'show_progress', op: 'eq', value: true } },

    ...shadowField,
    ...borderFields(),
  ],
};
