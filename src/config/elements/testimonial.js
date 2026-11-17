import { textEffectsFields, textEffectsDefaults, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, withHover, focalField } from './_shared.js';
import { t } from '@/i18n';

const STELLE = [
  { value: '0', label: t('Nessuna') },
  { value: '1', label: t('1 stella') },
  { value: '2', label: t('2 stelle') },
  { value: '3', label: t('3 stelle') },
  { value: '4', label: t('4 stelle') },
  { value: '5', label: t('5 stelle') },
];
// La testimonianza singola in cima al Contenuto: in Carosello e Griglia conta solo finché la lista è vuota.
const singola = (s) => !['carousel', 'grid'].includes(s.layout) || !(Array.isArray(s.items) && s.items.length);

/**
 * Tile Testimonial — split CONTENUTO/STILE.
 *   fields[]      → quote, author, role, avatar, rating, items multiple, layout (single/carousel/grid) + autoplay/dots/arrows
 *   styleFields[] → preset, bg, typo, text-effects, colori, linea decorativa, posizione testimone, avatar aspetto/ombra/bordo/filtro, radius card, bordo
 */
export default {
  type: 'testimonial',
  name: t('Testimonianza'),
  icon: 'dashicons-format-quote',
  category: 'marketing',
  defaults: {
    bg: { type: 'none' },
    typography_preset: '',
    preset: 'custom',
    quote: t('Un prodotto fantastico!'),
    logo: '',
    author_name: t('Mario Rossi'),
    author_role: t('CEO'),
    avatar: '',
    rating: '5',
    bg_color: '',
    text_color: '',
    star_color: '',
    author_color: '',
    quote_accent_color: '',
    quote_font: 'inherit',
    quote_size: 0,
    // Corsivo della citazione (Singola, Carosello, Griglia): acceso come quando era fisso nel renderer.
    quote_italic: true,
    author_uppercase: false,
    quote_uppercase: false,
    layout: 'single',
    autoplay: false,
    autoplay_interval: 5,
    slides_to_show: 1,
    show_dots: true,
    show_arrows: true,
    grid_columns: 2,
    items: [],
    show_line: true,
    line_color: '',
    author_position: 'bottom-left',
    avatar_size: '48',
    avatar_shape: 'circle',
    // L'avatar è sempre ritagliato in quadrato (width = height = avatar_size) con
    // `object-fit: cover` cablato nel CSS: l'unico pezzo di cornice che mancava era
    // DOVE cade quel ritaglio. 'center center' = esattamente la resa di oggi.
    avatar_object_position: 'center center',
    avatar_radius: '6',
    avatar_shadow: 'none',
    avatar_border_width: '0',
    avatar_border_color: '',
    avatar_filter: 'none',
    border_radius: '12',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    card_border: { ...borderDefault },
    card_border_hover: { ...borderHoverDefault },
    card_border_hover_duration: 300,
    ...borderEffectDefaults,
    ...textEffectsDefaults,
    text_effect_target: 'quote',
  },

  // Come nasce dalla palette: tre recensioni di clienti in griglia, con le stelle, la linea del colore
  // del sito accanto alla citazione e nome e città sotto, su card tinte appena del primario. Niente
  // ritratti (il pacchetto demo non ne ha). In «Singola» si vede la prima recensione.
  partenza: {
    layout: 'grid',
    grid_columns: 3,
    quote: t('Gentili, puntuali e sempre chiari sui costi. Ci hanno seguito passo passo e il risultato è andato oltre le aspettative.'),
    author_name: t('Laura Conti'),
    author_role: t('Cliente da Bologna'),
    items: [
      { quote: t('Gentili, puntuali e sempre chiari sui costi. Ci hanno seguito passo passo e il risultato è andato oltre le aspettative.'), logo: '', author_name: t('Laura Conti'), author_role: t('Cliente da Bologna'), avatar: '', rating: '5' },
      { quote: t('Ho scritto un sabato sera e lunedì mattina avevo già la risposta. Un servizio così attento non è scontato.'), logo: '', author_name: t('Marco Gallo'), author_role: t('Cliente da Torino'), avatar: '', rating: '5' },
      { quote: t('Ottimo rapporto fra qualità e prezzo, e tanta disponibilità. L\'ho già consigliato a due amici.'), logo: '', author_name: t('Chiara Esposito'), author_role: t('Cliente da Napoli'), avatar: '', rating: '4' },
    ],
    bg_color: 'color-mix(in srgb, var(--olo-color-primary) 6%, var(--olo-color-background))',
    text_color: 'var(--olo-color-text)',
    star_color: 'var(--olo-color-accent)',
    border_radius: '16',
  },

  // In Carosello e Griglia le voci vengono da «Testimonianze» (sotto): la testimonianza singola qui in cima serve
  // solo a «Singola» ed «Editoriale», o finché la lista è vuota (il renderer allora mostra lei).
  fields: [
    // La citazione col solo corsivo: le parole messe in corsivo prendono il «Colore parole in evidenza» (Stile).
    { key: 'quote', label: t('Citazione'), type: 'editor', mode: 'inline', strumenti: ['corsivo'], show: singola,
      description: t('Seleziona una parola e premi I: prende il colore delle parole in evidenza.') },
    { key: 'logo', label: t('Logo del cliente'), type: 'image', show: singola,
      description: t('Compare in alto nella scheda.') },
    { key: 'author_name', label: t('Nome'), type: 'text', show: singola },
    { key: 'author_role', label: t('Ruolo o città'), type: 'text', show: singola },
    { key: 'avatar', label: t('Ritratto'), type: 'image', show: singola },
    { key: 'rating', label: t('Valutazione'), type: 'select', show: singola, options: STELLE },

    { key: 'items', label: t('Testimonianze'), type: 'content-items', supportsDynamic: true,
      condition: { field: 'layout', operator: '!=', value: 'single' },
      // Nell'elenco ogni voce si chiama col nome e mostra il ritratto (non la citazione, né il logo).
      etichettaDa: 'author_name', miniaturaDa: 'avatar',
      itemFields: [
        { key: 'quote', label: t('Citazione'), type: 'editor', mode: 'inline', strumenti: ['corsivo'],
          description: t('Seleziona una parola e premi I: prende il colore delle parole in evidenza.') },
        { key: 'logo', label: t('Logo del cliente'), type: 'image' },
        { key: 'author_name', label: t('Nome'), type: 'text' },
        { key: 'author_role', label: t('Ruolo o città'), type: 'text' },
        { key: 'avatar', label: t('Ritratto'), type: 'image' },
        { key: 'rating', label: t('Valutazione'), type: 'select', options: STELLE },
      ],
      newItemDefaults: { quote: t('Un ottimo servizio!'), logo: '', author_name: t('Nome'), author_role: '', avatar: '', rating: '5' },
      itemLabel: t('Testimonianza'),
    },

    { type: 'separator', label: t('Carosello'), condition: { field: 'layout', value: 'carousel' } },
    { key: 'autoplay', label: t('Scorre da solo'), type: 'toggle',
      condition: { field: 'layout', value: 'carousel' } },
    { key: 'autoplay_interval', label: t('Ogni'), type: 'range', unit: 's', min: 1, max: 30, step: 1,
      condition: [ { field: 'layout', value: 'carousel' }, { field: 'autoplay', value: true } ] },
    { key: 'show_dots', label: t('Pallini'), type: 'toggle',
      condition: { field: 'layout', value: 'carousel' } },
    { key: 'show_arrows', label: t('Frecce'), type: 'toggle',
      condition: { field: 'layout', value: 'carousel' } },
  ],

  // Zone standard (16 nov 2026): Aspetto · Testo · Ritratto · Forma · Disposizione. Prima otto sezioni («Preset
  // stilistico», «Colori», «Testimone», «Aspetto card»…) e la linea decorativa nel Contenuto.
  styleFields: [
    { type: 'separator', label: t('Aspetto') },
    { key: 'preset', label: t('Stile'), type: 'select', options: [
      { value: 'modern-card',      label: t('Scheda') },
      { value: 'editorial-serif',  label: t('Editoriale con grazie') },
      { value: 'minimal-line',     label: t('Linea sottile') },
      { value: 'agency-bold',      label: t('Deciso, su scuro') },
      { value: 'glass-frosted',    label: t('Vetro') },
      { value: 'neon-quote',       label: t('Neon su scuro') },
      { value: 'sticky-note',      label: t('Foglietti adesivi') },
      { value: 'retro-typewriter', label: t('Macchina da scrivere') },
      { value: 'brutalist-stamp',  label: t('Timbro netto') },
      { value: 'custom',           label: t('Personalizzato') },
    ]},
    { key: 'bg_color', label: t('Sfondo scheda'), type: 'color' },
    { key: 'star_color', label: t('Colore stelle'), type: 'color',
      description: t('Vuoto: l’ambra della palette.') },
    { key: 'quote_accent_color', label: t('Colore evidenza'), type: 'color',
      description: t('Le parole che nella citazione hai messo in corsivo col pulsante I. Vuoto: il colore primario.') },
    { key: 'show_line', label: t('Linea accanto alla citazione'), type: 'toggle' },
    { key: 'line_color', label: t('Colore linea'), type: 'color',
      condition: { field: 'show_line', value: true } },

    { type: 'separator', label: t('Testo') },
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },
    // Il Corsivo vale dove la citazione è un blockquote (Singola, Carosello, Griglia): era fisso nel
    // renderer, e con un font senza corsivo caricato usciva un obliquo finto. L'Editoriale ha la sua
    // resa (tondo, con le parole in <em> in corsivo) e lì il comando non agirebbe: stessa «Citazione»
    // senza la riga Corsivo. Una sola delle due si vede, secondo il layout.
    { type: 'typography', label: t('Citazione'),
      responsiveKeys: [],
      keys: {
        size:      'quote_size',
        color:     'text_color',
        family:    'quote_font',
        uppercase: 'quote_uppercase',
        italic:    'quote_italic',
      },
      sizeMin: 0, sizeMax: 56,
      condition: { field: 'layout', op: 'neq', value: 'editorial' },
    },
    { type: 'typography', label: t('Citazione'),
      responsiveKeys: [],
      keys: {
        size:      'quote_size',
        color:     'text_color',
        family:    'quote_font',
        uppercase: 'quote_uppercase',
      },
      sizeMin: 0, sizeMax: 56,
      condition: { field: 'layout', op: 'eq', value: 'editorial' },
    },
    { type: 'typography', label: t('Nome'), responsiveKeys: [],
      keys: { color: 'author_color', uppercase: 'author_uppercase' } },
    ...textEffectsFields([ { value: 'quote', label: t('Solo la citazione') } ]),

    { type: 'separator', label: t('Ritratto') },
    { key: 'avatar_size', label: t('Dimensione'), type: 'range', unit: 'px', min: 30, max: 120, step: 5 },
    { key: 'avatar_shape', label: t('Forma'), type: 'select', options: [
      { value: 'circle', label: t('Tonda') },
      { value: 'square', label: t('Quadrata') },
    ]},
    withHover({ key: 'avatar_radius', label: t('Raggio ritratto'), type: 'border-radius',
      condition: { field: 'avatar_shape', value: 'square' } }),
    // Ritaglio sempre quadrato e sempre «riempi»: ratio e fit sono LETTERALI nel
    // contesto dell'anteprima (non esistono chiavi da leggere), si sceglie solo il
    // punto. Vale per tutte le testimonianze della lista, non una per voce:
    // altrimenti in griglia i volti si sfalserebbero riga per riga.
    focalField('avatar', { ratio: '1/1', fit: '(cover)', label: t('Punto focale') }),
    { key: 'avatar_shadow', label: t('Ombra ritratto'), type: 'select', options: [
      { value: 'none', label: t('Nessuna') },
      { value: 'sm', label: t('Piccola') },
      { value: 'md', label: t('Media') },
      { value: 'lg', label: t('Grande') },
      { value: 'custom', label: t('Personalizzata') },
    ]},
    { key: 'avatar_shadow_custom', label: t('Ombra personalizzata'), type: 'box-shadow',
      legacyKeys: { h: 'avatar_shadow_h', v: 'avatar_shadow_v', blur: 'avatar_shadow_blur', spread: 'avatar_shadow_spread', color: 'avatar_shadow_color', inset: 'avatar_shadow_inset' },
      condition: { field: 'avatar_shadow', op: 'eq', value: 'custom' } },
    { key: 'avatar_border', label: t('Bordo ritratto'), type: 'border',
      legacyKeys: { width: 'avatar_border_width', color: 'avatar_border_color' } },
    { key: 'avatar_filter', label: t('Filtro'), type: 'select', options: [
      { value: 'none', label: t('Nessuno') },
      { value: 'grayscale', label: t('Bianco e nero') },
      { value: 'sepia', label: t('Seppia') },
      { value: 'brightness', label: t('Luminoso') },
      { value: 'contrast', label: t('Alto contrasto') },
    ]},

    { type: 'separator', label: t('Forma') },
    withHover({ key: 'border_radius', label: t('Raggio scheda'), type: 'border-radius' }),
    { key: 'card_shadow', label: t('Ombra scheda'), type: 'select', options: [
      { value: 'none', label: t('Nessuna') },
      { value: 'sm', label: t('Leggera') },
      { value: 'md', label: t('Media') },
      { value: 'lg', label: t('Grande') },
      { value: 'netta', label: t('Netta, a timbro') },
    ]},
    withHover(
      { key: 'card_border', label: t('Bordo scheda'), type: 'border' },
      { hoverKey: 'card_border_hover', hoverDurationKey: 'card_border_hover_duration' }
    ),
    ...borderFields(),
    // Niente «Filtri CSS» (sfocatura, luminosità…): nessun renderer li leggeva, e l'unica immagine
    // su cui avrebbero senso, l'avatar, ha già il suo «Filtro avatar» qui sopra.

    { type: 'separator', label: t('Disposizione') },
    { key: 'layout', label: t('Layout'), type: 'select', options: [
      { value: 'single', label: t('Singola') },
      { value: 'editorial', label: t('Editoriale') },
      { value: 'carousel', label: t('Carosello') },
      { value: 'grid', label: t('Griglia') },
    ], description: t('Editoriale: centrata, con le stelle in alto, la citazione con le grazie e il nome sotto.') },
    { key: 'grid_columns', label: t('Colonne'), type: 'range', min: 1, max: 4, step: 1,
      description: t('Il massimo: in una colonna stretta le schede vanno a capo da sole.'),
      condition: { field: 'layout', value: 'grid' } },
    { key: 'slides_to_show', label: t('Schede visibili'), type: 'range', min: 1, max: 4, step: 1,
      condition: { field: 'layout', value: 'carousel' } },
    { key: 'author_position', label: t('Posizione del nome'), type: 'select', options: [
      { value: 'bottom-left', label: t('Sotto a sinistra') },
      { value: 'bottom-center', label: t('Sotto al centro') },
      { value: 'bottom-right', label: t('Sotto a destra') },
      { value: 'left', label: t('A sinistra') },
      { value: 'right', label: t('A destra') },
    ]},
  ],
};
