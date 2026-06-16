import { shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
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
  name: t('Card impilate (StackScroll)'),
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

  fields: [
    { key: 'cards', label: t('Card'), type: 'content-items',
      itemLabel: t('Card'),
      defaults: { eyebrow: '', title: 'Nuova card', accent: '', text: 'Testo della card…', media: '', media_label: 'Immagine', color: '', text_color: '' },
      itemFields: [
        { key: 'eyebrow',     label: t('Sopra-titolo (mono maiuscolo)'), type: 'text',
          description: t('Etichetta breve sopra il titolo, resa in maiuscolo con font mono. Distinta dal numero e dall\'accento.') },
        { key: 'title',       label: t('Titolo'),                type: 'text' },
        { key: 'accent',      label: t('Suffisso titolo (accento)'), type: 'text' },
        { key: 'text',        label: t('Testo'),                 type: 'editor', mode: 'block' },
        { key: 'media',       label: t('Immagine'),              type: 'image' },
        { key: 'media_label', label: t('Etichetta segnaposto immagine'), type: 'text' },
        { key: 'color',       label: t('Colore sfondo card'),    type: 'color' },
        { key: 'text_color',  label: t('Colore testo card'),     type: 'color' },
      ],
    },

    { type: 'separator', label: t('Impilamento') },
    { key: 'top_offset', label: t('Distanza dall\'alto (px)'), type: 'range', min: 0, max: 240, step: 5,
      description: t('Quota a cui la prima card si "incolla" durante lo scroll.') },
    { key: 'top_step', label: t('Scalino per card (px)'), type: 'range', min: 0, max: 80, step: 2,
      description: t('Ogni card si ferma un po\' più in basso della precedente, così resta visibile un bordo della pila.') },
    { key: 'card_gap', label: t('Spazio tra card (px)'), type: 'range', min: 0, max: 80, step: 2 },
    { key: 'scale_on_stack', label: t('Rimpicciolisci le card sotto la pila'), type: 'toggle',
      description: t('Le card già impilate si riducono leggermente per dare profondità. Rispetta prefers-reduced-motion.') },
    { key: 'scale_amount', label: t('Intensità rimpicciolimento (%)'), type: 'range', min: 1, max: 12, step: 1,
      condition: { field: 'scale_on_stack', op: 'eq', value: true } },
  ],

  styleFields: [
    { type: 'separator', label: t('Aspetto card') },
    { key: 'card_min_height', label: t('Altezza minima card (px)'), type: 'range', min: 200, max: 700, step: 10, responsive: true },
    { key: 'card_padding', label: t('Padding interno (px)'), type: 'range', min: 16, max: 80, step: 2, responsive: true },
    { key: 'round', label: t('Raggio angoli (px)'), type: 'border-radius' },
    { key: 'media_position', label: t('Posizione immagine'), type: 'select', options: [
      { value: 'right', label: t('A destra del testo') },
      { value: 'left',  label: t('A sinistra del testo') },
      { value: 'none',  label: t('Nessuna immagine (solo testo)') },
    ]},
    { key: 'object_position', label: t('Posizione contenuto'), type: 'object-position', reveal: true,
      contextKeys: { fit: 'cover' },
      condition: { field: 'media_position', op: 'neq', value: 'none' },
      description: t('Punto focale globale di tutte le immagini delle card.') },

    { type: 'separator', label: t('Colori predefiniti') },
    { key: 'card_bg_default', label: t('Sfondo card (default)'), type: 'color',
      description: t('Usato per le card senza colore proprio. Vuoto → superficie del tema.') },
    { key: 'text_color_default', label: t('Testo card (default)'), type: 'color' },
    { key: 'show_number', label: t('Mostra numero progressivo'), type: 'toggle' },
    { key: 'title_display', label: t('Titolo display (font heading + grande)'), type: 'toggle',
      description: t('Usa il font heading e una dimensione display (fino a 60px). Off = resa classica.') },
    { key: 'num_color_default', label: t('Colore numero'), type: 'color',
      condition: { field: 'show_number', op: 'eq', value: true } },

    ...shadowField,
    ...borderFields(),
  ],
};
