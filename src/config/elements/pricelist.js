import { textEffectsFields, textEffectsDefaults, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, withHover } from './_shared';
import { shadowField } from './_shared.js';
import { t } from '@/i18n';
import { demo } from '../demoMedia.js';

/**
 * Tile PriceList — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → items (content-items), toggle mostra immagine, posizione prezzo (layout)
 *   styleFields[] → preset, sfondo creativo, typography preset, effetti testo,
 *                   card (sfondo/bordo/radius/hover), separatore, dimensione immagine, radius immagine,
 *                   gap, padding, colori (titolo/prezzo/descrizione/highlighted), badge (sfondo/colore/bordo/radius),
 *                   ombra, bordo
 *   AVANZATE      → meta tecnico (id/class/condizioni)
 */
export default {
  type: 'pricelist',
  name: t('Lista prezzi'),
  icon: 'dashicons-list-view',
  category: 'marketing',
  defaults: {
    preset: 'custom',
    bg: { type: 'none' },
    typography_preset: '',
    items: [
      { id: 'pl-1', title: t('Bruschetta'), description: t('Pomodoro fresco, basilico e olio EVO'), price: '€8', image_url: '', highlighted: false, badge: '' },
      { id: 'pl-2', title: t('Risotto ai funghi porcini'), description: t('Riso Carnaroli mantecato con porcini freschi'), price: '€14', image_url: '', highlighted: false, badge: '' },
      { id: 'pl-3', title: t('Tiramisù'), description: t('Mascarpone, savoiardi e caffè espresso'), price: '€7', image_url: '', highlighted: false, badge: 'Consigliato' },
    ],
    separator_style: 'dotted',
    separator_color: '',
    title_color: '',
    price_color: '',
    description_color: '',
    image_size: '60',
    image_border_radius: '8',
    image_object_position: 'center center',
    show_image: true,
    price_position: 'right',
    highlighted_bg: '',
    badge_bg: '',
    badge_color: '',
    badge_border_color: '',
    badge_border_width: '0',
    badge_border_style: 'solid',
    badge_border_radius: '6',
    gap: '12',
    tile_padding: { top: 14, right: 14, bottom: 14, left: 14 },
    card_bg: '',
    card_border_radius: '12',
    card_border_color: '',
    hover_lift: true,
    // Dimensioni dei testi ('' = quelle di sempre: titolo 15, descrizione 13, prezzo 17/15, badge 11).
    title_size: '',
    description_size: '',
    price_size: '',
    badge_size: '',
    shadow: 'none',
    ...textEffectsDefaults,
    text_effect_target: 'title',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // Appena nata: il listino di un locale con le foto dei piatti, descrizioni vere, prezzi in
  // euro e una voce in evidenza col badge «Il più scelto»; le card si sollevano al passaggio.
  partenza: {
    items: [
      { id: 'pl-1', title: t('Colazione completa'), description: t('Cappuccino, spremuta d\'arancia e cornetto appena sfornato'), price: '6,50 €', image_url: demo('caffe'), highlighted: false, badge: '' },
      { id: 'pl-2', title: t('Insalata dell\'orto'), description: t('Verdure di stagione, carote a nastro e semi tostati'), price: '9 €', image_url: demo('insalata'), highlighted: false, badge: '' },
      { id: 'pl-3', title: t('Piatto del giorno'), description: t('La proposta della cucina con gli ingredienti del mercato'), price: '14 €', image_url: demo('piatti-alto'), highlighted: true, badge: t('Il più scelto') },
      { id: 'pl-4', title: t('Cena per due'), description: t('Antipasto, due portate e un calice di vino a testa'), price: '48 €', image_url: demo('tavola'), highlighted: false, badge: '' },
    ],
    image_size: '64',
    image_border_radius: '10',
    badge_color: 'var(--olo-color-primary-contrast)',
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { key: 'items', label: t('Voci'), type: 'content-items',
      itemFields: [
        { key: 'title', label: t('Nome'), type: 'text' },
        { key: 'description', label: t('Descrizione'), type: 'text' },
        { key: 'price', label: t('Prezzo'), type: 'text' },
        { key: 'image_url', label: t('Immagine'), type: 'image' },
        { key: 'highlighted', label: t('In evidenza'), type: 'toggle' },
        { key: 'badge', label: t('Etichetta'), type: 'text',
          description: t('Una parola accanto al nome, per esempio «Novità» o «Il più scelto». Vuoto = nessuna.') },
      ],
      newItemDefaults: { title: t('Nuova voce'), description: t('Una riga che la racconta'), price: '€0', image_url: '', highlighted: false, badge: '' },
      itemLabel: t('Voce'),
    },
    { key: 'show_image', label: t('Mostra le immagini'), type: 'toggle' },
  ],

  // ─── STILE ─────────────────────────────────────────────────
  // Zone standard (16 nov 2026): Aspetto · Testo · Immagini · Puntini · Etichetta · Forma · Disposizione. Prima
  // «Colori extra», «Spaziatura», «Card» e i preset coi nomi in inglese.
  styleFields: [
    { type: 'separator', label: t('Aspetto') },
    { key: 'preset', label: t('Stile'), type: 'select', options: [
      { value: 'modern-clean',    label: t('Pulito') },
      { value: 'minimal-mono',    label: t('Essenziale') },
      { value: 'magazine-bold',   label: t('Rivista') },
      { value: 'editorial-serif', label: t('Menù con grazie') },
      { value: 'compact-inline',  label: t('Compatto') },
      { value: 'glass-frosted',   label: t('Vetro') },
      { value: 'neon-glow',       label: t('Neon su scuro') },
      { value: 'brutalist-stamp', label: t('Timbro netto') },
      { value: 'gradient-aurora', label: t('Sfumato') },
      { value: 'sticker-fun',     label: t('Adesivo') },
      { value: 'retro-terminal',  label: t('Terminale') },
      { value: 'tilt-3d',         label: t('Biglietti') },
      { value: 'custom',          label: t('Personalizzato') },
    ] },
    { key: 'card_bg', label: t('Sfondo voce'), type: 'color' },
    { key: 'highlighted_bg', label: t('Sfondo voce in evidenza'), type: 'color' },
    { key: 'hover_lift', label: t('Si solleva al passaggio'), type: 'toggle' },
    ...shadowField,

    { type: 'separator', label: t('Testo') },
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },
    // Dimensione e colore per ogni testo: le misure erano fisse nel renderer (badge a 9 px).
    { type: 'typography', label: t('Nome'),
      responsiveKeys: [],
      keys: {
        size: 'title_size',
        family: 'title_font',
        color: 'title_color',
      },
      sizeMin: 12, sizeMax: 40,
    },
    { type: 'typography', label: t('Descrizione'),
      responsiveKeys: [],
      keys: {
        size: 'description_size',
        color: 'description_color',
      },
      sizeMin: 10, sizeMax: 28,
    },
    { type: 'typography', label: t('Prezzo'),
      responsiveKeys: [],
      keys: {
        size: 'price_size',
        family: 'price_font',
        color: 'price_color',
      },
      sizeMin: 12, sizeMax: 48,
    },
    { type: 'typography', label: t('Etichetta'),
      responsiveKeys: [],
      keys: {
        size: 'badge_size',
        color: 'badge_color',
      },
      sizeMin: 9, sizeMax: 20,
    },
    ...textEffectsFields([
      { value: 'title', label: t('Solo i nomi') },
      { value: 'description', label: t('Solo le descrizioni') },
      { value: 'all', label: t('Tutto il testo') },
    ]),

    { type: 'separator', label: t('Immagini'), condition: { field: 'show_image', value: true } },
    { key: 'image_size', label: t('Dimensione'), type: 'range', unit: 'px', min: 30, max: 120, step: 5,
      condition: { field: 'show_image', value: true } },
    withHover({ key: 'image_border_radius', label: t('Raggio immagine'), type: 'border-radius',
      condition: { field: 'show_image', value: true } }),
    // Punto focale GLOBALE: le immagini sono per-item ma il frame è un quadrato 1:1 fisso
    // (dimensione unica image_size). Nessuna chiave tile-level per src/fit/ratio → contextKeys
    // vuoto: il pad resta neutro (cover) senza far trapelare l'immagine di una singola voce.
    { key: 'image_object_position', label: t('Punto focale'), type: 'object-position', reveal: true,
      contextKeys: {},
      condition: { field: 'show_image', value: true } },

    { type: 'separator', label: t('Puntini') },
    { key: 'separator_style', label: t('Fra nome e prezzo'), type: 'select', options: [
      { value: 'dotted', label: t('Puntini') },
      { value: 'dashed', label: t('Trattini') },
      { value: 'solid', label: t('Linea') },
      { value: 'none', label: t('Niente') },
    ]},
    { key: 'separator_color', label: t('Colore'), type: 'color',
      condition: { field: 'separator_style', operator: '!=', value: 'none' } },

    { type: 'separator', label: t('Etichetta') },
    { key: 'badge_bg', label: t('Sfondo etichetta'), type: 'color' },
    { key: 'badge_border', label: t('Bordo etichetta'), type: 'border',
      legacyKeys: { width: 'badge_border_width', style: 'badge_border_style', color: 'badge_border_color' } },
    withHover({ key: 'badge_border_radius', label: t('Raggio etichetta'), type: 'border-radius'}),

    { type: 'separator', label: t('Forma') },
    { key: 'card_border_color', label: t('Bordo voce'), type: 'border', legacyWidth: 1 },
    withHover({ key: 'card_border_radius', label: t('Raggio voce'), type: 'border-radius'}),
    { key: 'gap', label: t('Gap fra le voci'), type: 'range', unit: 'px', min: 0, max: 32, step: 2 },
    { key: 'tile_padding', label: t('Padding'), type: 'spacing', max: 32 },
    ...borderFields(),

    { type: 'separator', label: t('Disposizione') },
    { key: 'price_position', label: t('Posizione prezzo'), type: 'select', options: [
      { value: 'right', label: t('A destra') },
      { value: 'below', label: t('Sotto il nome') },
    ]},
  ],
};
