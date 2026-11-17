import { shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
import { demo } from '../demoMedia.js';
import { t } from '@/i18n';

/**
 * Tile StackScroll — sezione con card `position:sticky` che si impilano.
 *
 * Effetto "Section · StackScroll" (rif. handoff-tile-speciali/temi/64-tema-pastificio.html,
 * blocco sticky stacking). Bucket C / famiglia B.
 *
 * Ogni card si ferma a `topOffset + i*topStep` px dall'alto: scorrendo la successiva
 * le sale sopra, creando una pila. È SOLO CSS sticky — il runtime è minimo (un IIFE che
 * disattiva lo sticky quando il browser non lo supporta o l'utente preferisce meno movimento).
 *
 * Contratto §2:
 *   - parametrico: ogni numero/colore = campo editor con default; nessun hardcode.
 *   - UID scoped: tutto il CSS è prefissato con .olo-stack-<id> (N istanze non si calpestano).
 *   - SSR: tutte le card sono renderizzate server-side e visibili.
 *   - prefers-reduced-motion / no-sticky → flusso verticale normale (card una sotto l'altra).
 *   - additivo: chiavi salvate invariate.
 *
 *   fields[]      → cards (repeater), topOffset, topStep, cardGap, scaleOnStack, scaleAmount
 *   styleFields[] → minHeight, round, padding interno, colori default card/testo, shadow, bordi
 */
export default {
  type: 'stackscroll',
  name: t('Card impilate'),
  icon: 'dashicons-index-card',
  category: 'layout',

  defaults: {
    // ── Comportamento pila ──
    top_offset: 90,
    top_step: 20,
    card_gap: 24,
    scale_on_stack: true,
    scale_amount: 4,           // % di riduzione per ogni card sotto la pila

    // ── Aspetto card (default, sovrascrivibili per card) ──
    card_min_height: 420,
    round: 20,
    card_padding: 48,
    media_position: 'right',   // immagine a destra / sinistra del testo
    object_position: 'center center', // punto focale globale immagine card (object-position)
    card_bg_default: '',       // vuoto → token di superficie
    text_color_default: '',    // vuoto → token testo
    num_color_default: '',     // colore numero progressivo (vuoto → accent)
    show_number: true,
    title_display: false,      // titolo display (heading-font + clamp 40..60) — no-op di default

    shadow: 'custom',
    shadow_h: '0',
    shadow_v: '-10',
    shadow_blur: '40',
    shadow_spread: '-20',
    shadow_color: 'rgba(0,0,0,0.30)',
    shadow_inset: false,

    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,

    // ── Card (repeater) ──
    cards: [
      { eyebrow: '', title: 'Primo gesto', accent: '', text: 'Descrivi il primo passaggio del tuo processo. Scrivi una frase chiara e concreta.', media: '', media_label: 'Immagine', color: '', text_color: '' },
      { eyebrow: '', title: 'Secondo gesto', accent: '', text: 'Il secondo passaggio. Le card si fermano una dopo l\'altra mentre scorri la pagina.', media: '', media_label: 'Immagine', color: '', text_color: '' },
      { eyebrow: '', title: 'Terzo gesto', accent: '', text: 'Il terzo passaggio. Ogni card sale sopra la precedente creando la pila.', media: '', media_label: 'Immagine', color: '', text_color: '' },
      { eyebrow: '', title: 'Quarto gesto', accent: '', text: 'L\'ultimo passaggio resta in cima. Aggiungi, rimuovi o riordina le card a piacere.', media: '', media_label: 'Immagine', color: '', text_color: '' },
    ],
  },

  // Appena nata: «perché sceglierci» in quattro card con foto che si impilano scorrendo —
  // numero, etichetta, titolo con l'accento attenuato e testo; le card alternano il fondo del
  // tema e una sua tinta tenue, così la pila si legge anche da ferma.
  partenza: {
    cards: [
      { eyebrow: t('Ascolto'), title: t('Partiamo'), accent: t('da te'), text: t('Prima di proporre qualsiasi cosa ascoltiamo: obiettivi, gusti, tempi e budget.'), media: demo('ufficio'), media_label: '', color: '', text_color: '' },
      { eyebrow: t('Qualità'), title: t('Curiamo'), accent: t('ogni dettaglio'), text: t('Materiali scelti, fornitori di fiducia e controlli a ogni fase del lavoro.'), media: demo('architettura'), media_label: '', color: 'color-mix(in srgb, var(--olo-color-primary, #e1474f) 7%, var(--olo-color-background, #ffffff))', text_color: '' },
      { eyebrow: t('Puntualità'), title: t('Tempi'), accent: t('chiari'), text: t('Un calendario condiviso fin dal primo giorno: sai sempre a che punto siamo.'), media: demo('scrivania'), media_label: '', color: '', text_color: '' },
      { eyebrow: t('Assistenza'), title: t('Sempre'), accent: t('con te'), text: t('Anche dopo la consegna restiamo raggiungibili, per ogni domanda o modifica.'), media: demo('caffe'), media_label: '', color: 'color-mix(in srgb, var(--olo-color-primary, #e1474f) 7%, var(--olo-color-background, #ffffff))', text_color: '' },
    ],
    title_display: true,
  },

  fields: [
    { key: 'cards', label: t('Card'), type: 'content-items', supportsDynamic: true,
      itemLabel: t('Card'),
      defaults: { eyebrow: '', title: t('Nuova card'), accent: '', text: t('Testo della card…'), media: '', media_label: t('Immagine'), color: '', text_color: '' },
      itemFields: [
        { key: 'eyebrow',     label: t('Occhiello'), type: 'text',
          description: t('Una parola sopra il titolo, in maiuscolo: «Qualità», «Ascolto».') },
        { key: 'title',       label: t('Titolo'),                type: 'text' },
        { key: 'accent',      label: t('Seconda parte del titolo'), type: 'text',
          description: t('Continua il titolo in un tono più tenue: «Curiamo» + «ogni dettaglio».') },
        { key: 'text',        label: t('Testo'),                 type: 'editor', mode: 'block' },
        { key: 'media',       label: t('Immagine'),              type: 'image',
          condizioneTile: { field: 'media_position', op: 'neq', value: 'none' } },
        { key: 'media_label', label: t('Scritta senza immagine'), type: 'text',
          condition: { field: 'media', op: 'empty' },
          condizioneTile: { field: 'media_position', op: 'neq', value: 'none' },
          description: t('Compare nel riquadro dell’immagine finché non ne scegli una.') },
      ],
    },
    { key: 'show_number', label: t('Mostra il numero'), type: 'toggle',
      description: t('Il numero progressivo sopra ogni card: 01, 02, 03…') },

    { type: 'separator', label: t('Impilamento') },
    { key: 'top_offset', label: t('Distanza dall’alto'), type: 'range', min: 0, max: 240, step: 5, unit: 'px',
      description: t('A che altezza della finestra si ferma la prima card mentre scorri.') },
    { key: 'top_step', label: t('Scalino fra le card'), type: 'range', min: 0, max: 80, step: 2, unit: 'px',
      description: t('Ogni card si ferma un po’ più in basso della precedente, così della pila resta visibile il bordo.') },
    { key: 'scale_on_stack', label: t('Rimpicciolisci le card coperte'), type: 'toggle',
      description: t('Le card già coperte si riducono un poco, per dare profondità. Chi ha chiesto al sistema meno animazioni vede le card una sotto l’altra.') },
    { key: 'scale_amount', label: t('Quanto rimpicciolire'), type: 'range', min: 1, max: 12, step: 1, unit: '%',
      condition: { field: 'scale_on_stack', op: 'eq', value: true },
      description: t('Per ogni card che sale sopra.') },
  ],

  styleFields: [
    // ── Aspetto ──
    { type: 'separator', label: t('Aspetto') },
    { key: 'card_bg_default', label: t('Sfondo card'), type: 'color',
      description: t('Per le card senza un colore proprio (lo scegli card per card qui sotto, in «Card»). Vuoto: la superficie del tema.') },
    { key: 'text_color_default', label: t('Colore testo card'), type: 'color',
      description: t('Per le card senza un colore del testo proprio. Vuoto: il testo del tema.') },
    { key: 'num_color_default', label: t('Colore numero'), type: 'color',
      condition: { field: 'show_number', op: 'eq', value: true },
      description: t('Vuoto: il colore principale del tema.') },
    ...shadowField,

    // ── Testo ──
    { type: 'separator', label: t('Testo') },
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },
    { key: 'title_display', label: t('Titolo grande'), type: 'toggle',
      description: t('Il carattere dei titoli del sito e una misura più grande.') },
    { type: 'typography', label: t('Titolo'), responsiveKeys: [],
      keys: { family: 'ss_title_font_family', size: 'ss_title_font_size', weight: 'ss_title_font_weight', italic: 'ss_title_italic', color: 'ss_title_color' },
      sizeMin: 18, sizeMax: 80, sizeStep: 1 },
    { type: 'typography', label: t('Seconda parte del titolo'), responsiveKeys: [],
      keys: { family: 'ss_accent_font_family', weight: 'ss_accent_font_weight', italic: 'ss_accent_italic', color: 'ss_accent_color' } },
    { type: 'typography', label: t('Occhiello'), responsiveKeys: [],
      keys: { family: 'ss_eyebrow_font_family', size: 'ss_eyebrow_font_size', weight: 'ss_eyebrow_font_weight', transform: 'ss_eyebrow_text_transform', letterSpacing: 'ss_eyebrow_letter_spacing', color: 'ss_eyebrow_color' },
      sizeMin: 10, sizeMax: 22, sizeStep: 1 },
    { type: 'typography', label: t('Testo'), responsiveKeys: [],
      keys: { family: 'ss_text_font_family', size: 'ss_text_font_size', lineHeight: 'ss_text_line_height', color: 'ss_text_color' },
      sizeMin: 12, sizeMax: 24, sizeStep: 1 },
    { type: 'typography', label: t('Numero'), responsiveKeys: [],
      keys: { family: 'ss_num_font_family', size: 'ss_num_font_size', weight: 'ss_num_font_weight' },
      condition: { field: 'show_number', op: 'eq', value: true },
      sizeMin: 10, sizeMax: 40, sizeStep: 1 },

    // ── Forma ──
    { type: 'separator', label: t('Forma') },
    { key: 'card_min_height', label: t('Altezza minima card'), type: 'range', min: 200, max: 700, step: 10, unit: 'px', responsive: true },
    // Per dispositivo come l'altezza: il renderer legge card_padding_<dispositivo> (numero o
    // 4 lati). Il selettore era sparito passando dal cursore al controllo Padding (1.4.436),
    // e i valori per tablet/telefono già salvati restavano attivi senza poterli vedere.
    { key: 'card_padding', label: t('Padding'), type: 'spacing', min: 0, max: 80, responsive: true },
    { key: 'round', label: t('Raggio card'), type: 'border-radius' },
    ...borderFields(),

    // ── Immagine ──
    { type: 'separator', label: t('Immagine'), condition: { field: 'media_position', op: 'neq', value: 'none' } },
    { key: 'object_position', label: t('Punto focale'), type: 'object-position', reveal: true,
      contextKeys: { fit: 'cover' },
      description: t('Lo stesso per tutte le immagini delle card.') },

    // ── Disposizione ──
    { type: 'separator', label: t('Disposizione') },
    { key: 'media_position', label: t('Posizione immagine'), type: 'select', options: [
      { value: 'right', label: t('A destra del testo') },
      { value: 'left',  label: t('A sinistra del testo') },
      { value: 'none',  label: t('Nessuna immagine (solo testo)') },
    ], description: t('In una colonna stretta e sul telefono l’immagine va sotto il testo.') },
    { key: 'card_gap', label: t('Gap card'), type: 'range', min: 0, max: 80, step: 2, unit: 'px' },

    // ── Le card una per una (specchio del ripetitore: solo lo stile) ──
    { type: 'separator', label: t('Ogni card') },
    { key: 'cards', type: 'content-items', label: t('Card'), itemLabel: t('Card'), etichettaDa: 'title', miniaturaDa: 'media', itemFields: [
        { key: 'color',       label: t('Sfondo card'),    type: 'color' },
        { key: 'text_color',  label: t('Colore testo'),   type: 'color' },
        // Per card: una schermata o un disegno non va tagliato ai bordi come una foto (8 nov 2026)
        { key: 'fit', label: t('Adattamento immagine'), type: 'select', options: [
          { value: '', label: t('Riempi') },
          { value: 'contain', label: t('Intera') },
        ], condizioneTile: { field: 'media_position', op: 'neq', value: 'none' },
          description: t('Riempi copre il riquadro e può tagliare i bordi; Intera mostra tutta la figura sul colore della card.') },
    ] },
  ],
};
