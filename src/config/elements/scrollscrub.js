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
    behavior: 'pin',             // 'pin' (pagina ferma, fila guidata) | 'inline' (altezza contenuto)
    scroll_length: 3,            // × viewport (2–6): più alto = scroll più lungo/lento
    align: 'center',            // start / center: allineamento verticale della traccia nel pin
    gap: 24,                    // px tra gli item
    easing: 'linear',           // mappatura scroll→translateX
    show_progress: true,        // scrubbar in basso
    pause_on_reduced_motion: true, // reduced-motion → scroll orizzontale nativo (niente pin)

    // ── Testata (opzionale) ──
    heading: 'Scorri in orizzontale',
    kicker: 'scroll → orizzontale',
    heading_color: '',           // vuoto → eredita dalla sezione
    kicker_color: '',            // vuoto → come il titolo, attenuato
    heading_size: 44,            // px: massimo del clamp responsive
    heading_font: '',            // vuoto → font della sezione

    // ── Aspetto item (default, sovrascrivibili per item) ──
    item_width: 360,            // px (desktop)
    item_min_height: 460,       // px (desktop)
    round: 14,
    item_padding: 0,            // px (0 = immagine a tutto bordo)
    object_position: 'center center', // punto focale GLOBALE applicato a ogni immagine del nastro
    item_bg_default: '',        // vuoto → token di superficie
    text_color_default: '',     // vuoto → token testo (overlay: bianco)
    progress_color: '',         // vuoto → accent
    show_number: true,

    // Sovraimpressione (item_padding = 0): scrim sopra la foto
    overlay_scrim_color: '#000000',
    overlay_scrim_opacity: 78,
    overlay_scrim_height: 62,

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
    { key: 'behavior', label: t('Comportamento'), type: 'select', options: [
      { value: 'pin',    label: t('Pin — la pagina si ferma, la fila scorre (altezza schermo)') },
      { value: 'inline', label: t('Scorrimento libero — altezza del contenuto') },
    ], description: t('Pin: la sezione occupa lo schermo intero e lo scroll di pagina guida la fila. Scorrimento libero: la sezione è alta quanto le card e la fila si scorre direttamente (touch, trackpad, tastiera).') },
    { key: 'scroll_length', label: t('Lunghezza scroll (× schermo)'), type: 'range', min: 2, max: 6, step: 0.5,
      condition: { field: 'behavior', op: 'neq', value: 'inline' },
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
    { type: 'separator', label: t('Testata') },
    { key: 'heading_color', label: t('Colore titolo'), type: 'color',
      description: t('Vuoto → eredita dalla sezione. Con foto di sfondo scegli un colore leggibile (es. bianco).') },
    { key: 'kicker_color', label: t('Colore etichetta (kicker)'), type: 'color',
      description: t('Vuoto → come il titolo, attenuato.') },
    { key: 'heading_size', label: t('Dimensione titolo (px)'), type: 'range', min: 20, max: 96, step: 2,
      description: t('Dimensione massima: sotto i grandi schermi scala da sola (clamp responsive).') },
    { key: 'heading_font', label: t('Font titolo'), type: 'font-family' },

    { type: 'separator', label: t('Aspetto elementi') },
    { key: 'item_width', label: t('Larghezza elemento (px)'), type: 'range', min: 160, max: 720, step: 10, responsive: true },
    { key: 'item_min_height', label: t('Altezza elemento (px)'), type: 'range', min: 200, max: 760, step: 10, responsive: true },
    { key: 'round', label: t('Raggio angoli (px)'), type: 'border-radius' },
    { key: 'item_padding', label: t('Padding interno (px)'), type: 'range', min: 0, max: 64, step: 2,
      description: t('0 = immagine a tutto bordo con testo sovrapposto in basso.') },

    { type: 'separator', label: t('Sovraimpressione (foto a tutto bordo)'), condition: { field: 'item_padding', op: 'eq', value: 0 } },
    { key: 'overlay_scrim_color', label: t('Colore sfumatura'), type: 'color',
      condition: { field: 'item_padding', op: 'eq', value: 0 } },
    { key: 'overlay_scrim_opacity', label: t('Intensità sfumatura (%)'), type: 'range', min: 0, max: 100, step: 2,
      condition: { field: 'item_padding', op: 'eq', value: 0 },
      description: t('La sfumatura sta SOPRA la foto e fa leggere il testo. 0 = nessuna sfumatura.') },
    { key: 'overlay_scrim_height', label: t('Altezza sfumatura (%)'), type: 'range', min: 20, max: 100, step: 2,
      condition: { field: 'item_padding', op: 'eq', value: 0 },
      description: t('Quanta parte della card copre, dal fondo verso l\'alto.') },
    { key: 'object_position', label: t('Posizione contenuto'), type: 'object-position', reveal: true,
      contextKeys: { fit: '' },
      description: t('Punto focale applicato a tutte le immagini del nastro (object-position).') },

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
