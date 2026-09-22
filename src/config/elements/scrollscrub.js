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
    typography_preset: '',
    // ── Comportamento pin/scroll ──
    behavior: 'pin',             // 'pin' (pagina ferma, fila guidata) | 'inline' (altezza contenuto)
    scroll_length: 3,            // × viewport (2–6): più alto = scroll più lungo/lento
    align: 'center',            // start / center: allineamento verticale della traccia nel pin
    gap: 24,                    // px tra gli item
    easing: 'linear',           // mappatura scroll→translateX
    show_progress: true,        // scrubbar in basso
    pause_on_reduced_motion: true, // reduced-motion → scroll orizzontale nativo (niente pin)

    // ── Comandi di scorrimento ──
    // La barra di scorrimento del browser era l'UNICO comando visibile del nastro:
    // brutta, sempre accesa e muta (non dice quanti elementi ci sono). Ora di
    // fabbrica è spenta e al suo posto ci sono comandi veri — frecce e pallini —
    // che dicono anche DOVE sei e quanto manca.
    arrows: 'tonde',            // none | tonde | vetro | minimale | quadrate | pillola
    arrows_pos: 'dentro',       // dentro | fuori | sotto
    arrows_size: 44,
    arrows_bg: '',              // vuoto → superficie del tema
    arrows_color: '',           // vuoto → testo del tema
    indicators: 'pallini',      // none | pallini | trattini | numeri | linea
    indicators_color: '',       // vuoto → testo del tema, attenuato
    indicators_active_color: '', // vuoto → come la barra di progresso
    show_scrollbar: false,      // barra nativa del browser sotto il nastro
    edge_fade: true,            // sfumatura ai bordi: «continua oltre»
    drag_scroll: true,          // trascina col mouse per scorrere
    snap_strong: false,         // aggancio obbligatorio al centro dell'elemento

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
    { key: 'gap', label: t('Gap elementi'), type: 'range', min: 0, max: 80, step: 2 },
    { key: 'easing', label: t('Curva di scorrimento'), type: 'select', options: [
      { value: 'linear',   label: t('Lineare (segue lo scroll)') },
      { value: 'ease',     label: t('Morbida (ease-in-out)') },
      { value: 'ease-out', label: t('Decelera (ease-out)') },
    ]},
    { key: 'show_progress', label: t('Mostra barra di progresso'), type: 'toggle' },
    { key: 'pause_on_reduced_motion', label: t('Rispetta “riduci movimento”'), type: 'toggle',
      description: t('Con prefers-reduced-motion (o su mobile) la traccia diventa uno scroll orizzontale nativo, senza ancoraggio verticale. Consigliato attivo per accessibilità.') },

    { type: 'separator', label: t('Comandi di scorrimento') },
    { key: 'arrows', label: t('Frecce'), type: 'select', options: [
      { value: 'none',     label: t('Nessuna') },
      { value: 'tonde',    label: t('Tonde — cerchio pieno') },
      { value: 'vetro',    label: t('Vetro — cerchio semitrasparente sfocato') },
      { value: 'minimale', label: t('Minimale — solo il segno') },
      { value: 'quadrate', label: t('Quadrate — riquadro come le card') },
      { value: 'pillola',  label: t('Pillola — le due frecce affiancate') },
    ], description: t('Un clic sposta il nastro di un elemento. La freccia si spegne da sola quando da quel lato non c\'è più corsa.') },
    { key: 'arrows_pos', label: t('Posizione frecce'), type: 'select', options: [
      { value: 'dentro', label: t('Dentro — sopra i bordi del nastro') },
      { value: 'fuori',  label: t('Fuori — ai lati, nel margine') },
      { value: 'sotto',  label: t('Sotto — accanto agli indicatori') },
    ], condition: { field: 'arrows', op: 'neq', value: 'none' },
      description: t('Sotto i 960px «fuori» rientra da sé sul nastro: nel margine stretto dei piccoli schermi non ci starebbe.') },
    { key: 'indicators', label: t('Indicatori'), type: 'select', options: [
      { value: 'none',      label: t('Nessuno') },
      { value: 'pallini',   label: t('Pallini — uno per elemento, cliccabili') },
      { value: 'trattini',  label: t('Trattini — uno per elemento, cliccabili') },
      { value: 'numeri',    label: t('Numeri — 01 / 05') },
      { value: 'linea',     label: t('Linea — avanzamento continuo') },
    ], description: t('Pallini e trattini portano all\'elemento corrispondente; numeri e linea dicono soltanto a che punto sei.') },
    { key: 'drag_scroll', label: t('Trascina per scorrere'), type: 'toggle',
      description: t('Col mouse il nastro si trascina come una fotografia sul tavolo. Il touch usa già il suo scorrimento nativo e resta intatto.') },
    { key: 'edge_fade', label: t('Sfumatura ai bordi'), type: 'toggle',
      description: t('Gli elementi svaniscono sui bordi del nastro invece di essere tagliati di netto: si capisce a colpo d\'occhio che c\'è dell\'altro.') },
    { key: 'snap_strong', label: t('Aggancio deciso agli elementi'), type: 'toggle',
      description: t('Lo scorrimento si ferma sempre al centro di un elemento, mai a metà strada. Vale dove la fila si scorre da sé (mobile, «scorrimento libero», riduci movimento).') },
    { key: 'show_scrollbar', label: t('Barra di scorrimento'), type: 'toggle',
      description: t('La barra grigia del browser sotto il nastro. Spenta: al suo posto ci sono frecce e indicatori. Lo scorrimento con dito, trackpad e tastiera funziona comunque.') },
  ],

  styleFields: [
    { type: 'separator', label: t('Testata') },

    { key: 'kicker_color', label: t('Colore etichetta (kicker)'), type: 'color',
      description: t('Vuoto → come il titolo, attenuato.') },
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },
    { type: 'typography', label: t('Titolo'), responsiveKeys: [], keys: { size: 'heading_size', family: 'heading_font', color: 'heading_color' }, sizeMin: 20, sizeMax: 96, sizeStep: 2 },

    { type: 'separator', label: t('Aspetto elementi') },
    { key: 'item_width', label: t('Larghezza elemento'), type: 'range', min: 160, max: 720, step: 10, responsive: true },
    { key: 'item_min_height', label: t('Altezza elemento'), type: 'range', min: 200, max: 760, step: 10, responsive: true },
    { key: 'round', label: t('Raggio angoli'), type: 'border-radius' },
    { key: 'item_padding', label: t('Padding'), type: 'spacing', min: 0, max: 80,
      description: t('0 = immagine a tutto bordo con testo sovrapposto in basso.') },

    { type: 'separator', label: t('Sovraimpressione (foto a tutto bordo)'), condition: { field: 'item_padding', op: 'eq', value: 0 } },
    { key: 'overlay_scrim_color', label: t('Colore sfumatura'), type: 'color',
      condition: { field: 'item_padding', op: 'eq', value: 0 } },
    { key: 'overlay_scrim_opacity', label: t('Intensità sfumatura'), type: 'range', min: 0, max: 100, step: 2,
      condition: { field: 'item_padding', op: 'eq', value: 0 },
      description: t('La sfumatura sta SOPRA la foto e fa leggere il testo. 0 = nessuna sfumatura.') },
    { key: 'overlay_scrim_height', label: t('Altezza sfumatura'), type: 'range', min: 20, max: 100, step: 2,
      condition: { field: 'item_padding', op: 'eq', value: 0 },
      description: t('Quanta parte della card copre, dal fondo verso l\'alto.') },
    { key: 'object_position', label: t('Punto focale'), type: 'object-position', reveal: true,
      contextKeys: { fit: '' },
      description: t('Punto focale applicato a tutte le immagini del nastro (object-position).') },

    { type: 'separator', label: t('Colori predefiniti') },
    { key: 'item_bg_default', label: t('Sfondo elemento (default)'), type: 'color',
      description: t('Usato per gli elementi senza colore proprio. Vuoto → superficie del tema.') },
    { key: 'text_color_default', label: t('Testo elemento (default)'), type: 'color' },
    { key: 'show_number', label: t('Mostra numero progressivo'), type: 'toggle' },
    { key: 'progress_color', label: t('Colore barra di progresso'), type: 'color',
      condition: { field: 'show_progress', op: 'eq', value: true } },

    { type: 'separator', label: t('Comandi di scorrimento') },
    { key: 'arrows_size', label: t('Dimensione frecce'), type: 'range', min: 28, max: 72, step: 2,
      condition: { field: 'arrows', op: 'neq', value: 'none' } },
    // `not-in` e non `in`: le condizioni si valutano sui settings GREZZI, senza
    // i default. Nei template salvati prima di questi campi la chiave `arrows`
    // non esiste, e `in` su una chiave assente è falso → il campo resterebbe
    // nascosto mentre la tile disegna frecce «tonde» con il loro sfondo.
    // Con `not-in` l'assenza equivale al default, che è appunto «tonde».
    { key: 'arrows_bg', label: t('Sfondo frecce'), type: 'color',
      condition: { field: 'arrows', op: 'not-in', value: [ 'none', 'minimale' ] },
      description: t('Vuoto → superficie del tema. Lo stile «Vetro» lo rende semitrasparente da sé.') },
    { key: 'arrows_color', label: t('Colore frecce'), type: 'color',
      condition: { field: 'arrows', op: 'neq', value: 'none' },
      description: t('Il segno dentro il pulsante. Vuoto → colore testo del tema (lo stile «Minimale», che sta sullo sfondo della sezione, eredita il colore della sezione).') },
    { key: 'indicators_color', label: t('Colore indicatori'), type: 'color',
      condition: { field: 'indicators', op: 'neq', value: 'none' },
      description: t('Gli indicatori spenti. Vuoto → eredita il colore della sezione, attenuato: così si vedono sia su chiaro sia su scuro.') },
    { key: 'indicators_active_color', label: t('Colore indicatore attivo'), type: 'color',
      condition: { field: 'indicators', op: 'neq', value: 'none' },
      description: t('Vuoto → lo stesso colore della barra di progresso.') },

    ...shadowField,
    ...borderFields(),
  ],
};
