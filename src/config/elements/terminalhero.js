import { t } from '@/i18n';
import { shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';

/**
 * Hero — Terminale (typewriter) : hero centrato su fondo chiaro "tech" con label mono
 * maiuscola (parole tra [parentesi] rese come chip accento), titolo gigante, riga
 * TYPEWRITER con frasi a rotazione (prefisso configurabile, cursore a blocco), sub,
 * form email inline (GET verso una pagina) o 2 CTA, riga small con link freccia,
 * immagini decorative laterali (nascoste < 1000px), crosshair "+" agli angoli e
 * hairline superiore. Estratta dal blueprint konghq.com (modalità umana).
 * Render Vue == PHP (TerminalHeroTile.vue / class-terminalhero-tile.php).
 * Runtime: micro-IIFE typewriter (rispetta prefers-reduced-motion).
 */
export default {
  type: 'terminalhero',
  name: t('Hero — Terminale (typewriter)'),
  icon: 'dashicons-editor-code',
  category: 'marketing',
  // Tile firma, fuori palette (unificazione hero, Fase 2): typewriter a frasi + form
  // email inline. Disponibile come blocco "Hero speciale — Terminale" nella libreria
  // Blocchi & Pagine. I template salvati continuano a renderizzare.
  hidden: true,

  defaults: {
    show_label: true,
    label: 'UN SOLO [ECOSISTEMA] WORDPRESS',
    heading: 'Costruisci. Traduci. Prenota.',
    type_phrases: [
      { text: 'per chi costruisce siti' },
      { text: 'per chi affitta camere' },
      { text: 'per chi parla al mondo' },
    ],
    type_prefix: '— ',
    type_speed: 55,
    type_pause: 1600,
    subhead: 'La suite modulare per WordPress: page builder, prenotazioni, traduzioni, tour virtuali e corsi. Un solo fornitore, un solo standard.',

    // Cattura email (GET verso una pagina) — in alternativa 2 CTA classiche
    show_form: true,
    form_placeholder: 'La tua email',
    form_button: 'Richiedi una demo',
    form_action: '#',
    small_text: 'Prova i prodotti su siti demo reali.',
    small_link_text: 'Apri la demo',
    small_link_url: '#',
    cta1_text: '',
    cta1_url: '#',
    cta2_text: '',
    cta2_url: '#',

    // Immagini decorative laterali (stile "mani" del blueprint)
    img_left: '',
    img_right: '',
    side_width: 520,
    side_opacity: 100,

    // Decorazioni tecniche
    show_crosshairs: true,
    show_topline: true,

    // Colori (vuoto = token globali)
    bg_color: 'var(--olo-color-light, #f8f9fa)',
    text_color: 'var(--olo-color-dark, #1a1a2e)',
    sub_color: '',
    accent: '',
    accent_on: '',

    // Titolo / typewriter
    h_size_min: 44,
    h_size_vw: 6.5,
    h_size_max: 92,
    h_line_height: 1.02,
    type_size_min: 20,
    type_size_vw: 2.4,
    type_size_max: 34,

    // Layout
    align: 'center',
    max_width: 1200,
    min_height: 76,

    // Spaziatura — override GATED del padding responsive del contenitore (clamp). No-op coi default.
    pad_custom: false,
    content_padding: { top: 96, right: 0, bottom: 96, left: 0 },
    // Forma — raggio pill di input/bottoni. No-op coi default (999 = pill).
    btn_radius: { tl: 999, tr: 999, br: 999, bl: 999 },

    // KIT standard OLObuild — sfondo completo / ombra / bordo (no-op coi default)
    bg: { type: 'none' },
    shadow: 'none',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  fields: [
    { type: 'separator', label: t('Label mono') },
    { key: 'show_label', label: t('Mostra label'), type: 'toggle' },
    { key: 'label', label: t('Testo label'), type: 'text',
      description: t('Le parole tra [parentesi quadre] diventano chip accento.'),
      condition: { field: 'show_label', op: 'eq', value: true } },

    { type: 'separator', label: t('Titolo') },
    { key: 'heading', label: t('Titolo'), type: 'text' },
    { key: 'type_phrases', label: t('Frasi typewriter'), type: 'content-items',
      itemLabel: t('Frase'),
      defaults: { text: 'nuova frase a rotazione' },
      itemFields: [
        { key: 'text', label: t('Testo'), type: 'text' },
      ],
    },
    { key: 'type_prefix', label: t('Prefisso typewriter'), type: 'text',
      description: t('Carattere fisso prima della frase (es. "— ").') },
    { key: 'subhead', label: t('Sottotitolo'), type: 'textarea' },

    { type: 'separator', label: t('Cattura email') },
    { key: 'show_form', label: t('Mostra form email'), type: 'toggle' },
    { key: 'form_placeholder', label: t('Placeholder email'), type: 'text',
      condition: { field: 'show_form', op: 'eq', value: true } },
    { key: 'form_button', label: t('Testo bottone'), type: 'text',
      condition: { field: 'show_form', op: 'eq', value: true } },
    { key: 'form_action', label: t('Pagina di destinazione'), type: 'link',
      description: t('La email viene passata come parametro ?email= alla pagina.'),
      condition: { field: 'show_form', op: 'eq', value: true } },
    { key: 'small_text', label: t('Riga small — testo'), type: 'text' },
    { key: 'small_link_text', label: t('Riga small — testo link'), type: 'text' },
    { key: 'small_link_url', label: t('Riga small — link'), type: 'link' },

    { type: 'separator', label: t('CTA alternative (se form nascosto)') },
    { key: 'cta1_text', label: t('CTA 1 — testo'), type: 'text' },
    { key: 'cta1_url', label: t('CTA 1 — link'), type: 'link' },
    { key: 'cta2_text', label: t('CTA 2 — testo'), type: 'text' },
    { key: 'cta2_url', label: t('CTA 2 — link'), type: 'link' },

    { type: 'separator', label: t('Immagini laterali') },
    { key: 'img_left', label: t('Immagine sinistra'), type: 'image' },
    { key: 'img_right', label: t('Immagine destra'), type: 'image' },
  ],

  styleFields: [
    { type: 'separator', label: t('Layout') },
    { key: 'align', label: t('Allineamento'), type: 'select', options: [
      { value: 'center', label: t('Centro') },
      { value: 'left', label: t('Sinistra') },
    ]},
    { key: 'max_width', label: t('Larghezza max (px)'), type: 'range', min: 600, max: 1600, step: 20 },
    { key: 'min_height', label: t('Altezza minima (vh)'), type: 'range', min: 0, max: 100, step: 1 },

    { type: 'separator', label: t('Spaziatura') },
    { key: 'pad_custom', label: t('Padding personalizzato'), type: 'toggle',
      description: t('Disattivo = padding fluido responsive (clamp). Attivo = usa i valori sotto.') },
    { key: 'content_padding', label: t('Padding contenuto (px)'), type: 'spacing',
      condition: { field: 'pad_custom', op: 'eq', value: true } },

    { type: 'separator', label: t('Forma') },
    { key: 'btn_radius', label: t('Raggio input/bottoni (px)'), type: 'border-radius' },

    { type: 'separator', label: t('Titolo') },
    { key: 'h_size_min', label: t('Dimensione min (px)'), type: 'range', min: 20, max: 90, step: 1 },
    { key: 'h_size_vw', label: t('Dimensione fluida (vw)'), type: 'range', min: 2, max: 14, step: 0.5 },
    { key: 'h_size_max', label: t('Dimensione max (px)'), type: 'range', min: 60, max: 200, step: 2 },
    { key: 'h_line_height', label: t('Interlinea'), type: 'range', min: 0.8, max: 1.4, step: 0.01 },

    { type: 'separator', label: t('Typewriter') },
    { key: 'type_size_min', label: t('Dimensione min (px)'), type: 'range', min: 14, max: 40, step: 1 },
    { key: 'type_size_vw', label: t('Dimensione fluida (vw)'), type: 'range', min: 1, max: 6, step: 0.2 },
    { key: 'type_size_max', label: t('Dimensione max (px)'), type: 'range', min: 20, max: 64, step: 1 },
    { key: 'type_speed', label: t('Velocità battitura (ms/carattere)'), type: 'range', min: 20, max: 160, step: 5 },
    { key: 'type_pause', label: t('Pausa a fine frase (ms)'), type: 'range', min: 400, max: 5000, step: 100 },

    { type: 'separator', label: t('Decorazioni') },
    { key: 'show_crosshairs', label: t('Crosshair "+" agli angoli'), type: 'toggle' },
    { key: 'show_topline', label: t('Hairline superiore'), type: 'toggle' },
    { key: 'side_width', label: t('Larghezza immagini laterali (px)'), type: 'range', min: 200, max: 800, step: 10 },
    { key: 'side_opacity', label: t('Opacità immagini laterali (%)'), type: 'range', min: 10, max: 100, step: 5 },

    { type: 'separator', label: t('Colori') },
    { key: 'bg_color', label: t('Sfondo'), type: 'color' },
    { key: 'text_color', label: t('Testo / titolo'), type: 'color' },
    { key: 'sub_color', label: t('Sottotitolo'), type: 'color',
      description: t('Vuoto = testo attenuato automaticamente.') },
    { key: 'accent', label: t('Accento (chip, cursore, bottone)'), type: 'color',
      description: t('Vuoto = primario del tema.') },
    { key: 'accent_on', label: t('Testo su accento'), type: 'color',
      description: t('Vuoto = contrasto automatico del tema.') },

    { type: 'separator', label: t('Sfondo') },
    { key: 'bg', label: t('Sfondo completo'), type: 'background', showParallax: false },

    { type: 'separator', label: t('Ombra') },
    ...shadowField,
    ...borderFields(),
  ],
};
