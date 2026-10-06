import { textEffectsFields, textEffectsDefaults, withHover } from './_shared';
import { shadowField } from './_shared.js';
import { ratioOptions, ADATTAMENTI } from './_imageFrame.js';
import { demo } from '../demoMedia.js';
import { t } from '@/i18n';

/**
 * Tile Grid — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → items (content-items), colonne, gap, equal_height, image_ratio,
 *                   image_fit, image_zoom, image_animation (behavior), toggle overlay_text,
 *                   posizione overlay, toggle show_filter, etichetta filtro, masonry
 *   styleFields[] → preset, bg, typography_preset, textEffectsFields, card_style, card_hover,
 *                   card_radius, padding, colori card, image_height, image_animation_speed (ms),
 *                   tipografia (dim titolo/testo, colori), stile filtro, allineamento filtro,
 *                   shadow
 */
export default {
  type: 'grid',
  name: t('Griglia'),
  icon: 'dashicons-grid-view',
  category: 'layout',
  defaults: {
    preset: 'custom',
    bg: { type: 'none' },
    typography_preset: '',
    items: [
      { id: 'g-1', title: t('Elemento 1'), content: 'Descrizione del primo elemento.', image: '', tag: 'all' },
      { id: 'g-2', title: t('Elemento 2'), content: 'Descrizione del secondo elemento.', image: '', tag: 'all' },
      { id: 'g-3', title: t('Elemento 3'), content: 'Descrizione del terzo elemento.', image: '', tag: 'all' },
    ],
    columns: '3',
    gap: 'default',
    show_filter: false,
    filter_style: 'pills',
    filter_align: 'left',
    masonry: false,
    card_style: 'default',
    shadow: 'none',
    card_hover: 'none',
    image_ratio: 'auto',
    image_height: '',
    image_fit: 'cover',
    object_position: 'center center',
    image_zoom: false,
    card_radius: '8',
    tile_padding: { top: 16, right: 16, bottom: 16, left: 16 },
    card_bg_color: '',
    card_border_color: '',
    equal_height: false,
    overlay_text: false,
    overlay_position: 'bottom',
    title_size: '',
    content_size: '',
    title_color: '',
    content_color: '',
    image_animation: 'none',
    image_animation_speed: '3',
    ...textEffectsDefaults,
    text_effect_target: 'title',
  },

  // Appena nata: «cosa vedere nei dintorni» di una struttura o di un'agenzia — sei card con
  // foto 4:3, titolo e testo, un badge «Da non perdere», la barra filtro a pillole per categoria
  // (Tutti · Natura · Cultura · Mare), card del tema con ombra leggera, zoom e sollevamento al passaggio.
  partenza: {
    items: [
      { id: 'g-1', title: t('Il lago in quota'), content: t('Un sentiero facile tra i larici porta alla riva: due ore fra andata e ritorno.'), image: demo('montagna-lago'), tag: t('Natura'), badge: t('Da non perdere'), badge_color: '', link: '', link_target: false, icon: '' },
      { id: 'g-2', title: t('Il centro storico'), content: t('Vicoli, botteghe e la terrazza panoramica sui tetti, a dieci minuti a piedi.'), image: demo('citta-tetti'), tag: t('Cultura'), badge: '', badge_color: '', link: '', link_target: false, icon: '' },
      { id: 'g-3', title: t('La baia'), content: t('Acqua turchese e sabbia chiara, raggiungibile in barca o con una breve camminata.'), image: demo('mare-costa'), tag: t('Mare'), badge: '', badge_color: '', link: '', link_target: false, icon: '' },
      { id: 'g-4', title: t('Il bosco'), content: t('All\'alba i raggi filtrano fra gli alberi: il momento migliore per una passeggiata.'), image: demo('bosco-luce'), tag: t('Natura'), badge: '', badge_color: '', link: '', link_target: false, icon: '' },
      { id: 'g-5', title: t('Architetture moderne'), content: t('Il quartiere nuovo, con le sue facciate firmate e i giardini pensili.'), image: demo('architettura'), tag: t('Cultura'), badge: '', badge_color: '', link: '', link_target: false, icon: '' },
      { id: 'g-6', title: t('La spiaggia degli scogli'), content: t('Una caletta fra le rocce, perfetta nelle mattine di sole e nei giorni senza vento.'), image: demo('spiaggia-alto'), tag: t('Mare'), badge: '', badge_color: '', link: '', link_target: false, icon: '' },
    ],
    gap: 'medium',
    show_filter: true,
    filter_all_label: t('Tutti'),
    card_style: 'default',
    card_bg_color: 'var(--olo-color-background, #ffffff)',
    card_border_color: 'var(--olo-color-border, #e5e7eb)',
    shadow: 'sm',
    card_hover: 'lift',
    card_radius: '12',
    image_ratio: '4/3',
    image_zoom: true,
    equal_height: true,
    title_size: '20',
    content_color: 'var(--olo-color-text-muted, #6b7280)',
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { key: 'items', label: t('Elementi'), type: 'content-items', supportsDynamic: true,
      itemFields: [
        { key: 'title', label: t('Titolo'), type: 'text' },
        { key: 'content', label: t('Contenuto'), type: 'textarea' },
        { key: 'image', label: t('Immagine'), type: 'image' },
        { key: 'hover_image', label: t('Immagine hover'), type: 'image' },
        { key: 'hover_video', label: t('Video hover'), type: 'media' },
        { key: 'tag', label: t('Tag filtro'), type: 'text', description: t('Separare più tag con virgola (es. natura, sport)') },
        { key: 'link', label: t('Link'), type: 'link' },
        { key: 'link_target', label: t('Apri in nuova scheda'), type: 'toggle' },
        { key: 'badge', label: t('Badge'), type: 'text' },
        { key: 'icon', label: t('Icona'), type: 'icon' },
      ],
      newItemDefaults: { title: t('Nuovo elemento'), content: '', image: '', hover_image: '', hover_video: '', tag: 'all', link: '', link_target: false, badge: '', badge_color: '', icon: '' },
    },

    { type: 'separator', label: t('Immagine') },
    { key: 'image_zoom', label: t('Zoom immagine al hover'), type: 'toggle' },
    { key: 'image_animation', label: t('Animazione continua immagine'), type: 'select', options: [
      { value: 'none', label: t('Nessuna') },
      { value: 'ken-burns', label: t('Ken Burns (zoom lento)') },
      { value: 'pan-left', label: t('Panoramica sinistra') },
      { value: 'pan-right', label: t('Panoramica destra') },
      { value: 'pan-up', label: t('Panoramica su') },
      { value: 'pan-down', label: t('Panoramica giù') },
      { value: 'pulse', label: t('Pulsazione') },
      { value: 'float', label: t('Galleggiamento') },
      { value: 'rotate', label: t('Rotazione lenta') },
      { value: 'shimmer', label: t('Luccichio (shimmer)') },
    ]},

    { type: 'separator', label: t('Overlay') },
    { key: 'overlay_text', label: t('Testo su immagine'), type: 'toggle' },

    { type: 'separator', label: t('Filtro') },
    { key: 'show_filter', label: t('Mostra filtro'), type: 'toggle' },
    { key: 'filter_all_label', label: t('Etichetta "Tutti"'), type: 'text', placeholder: t('Tutti'),
      condition: { field: 'show_filter', value: true } },

    { type: 'separator', label: t('Avanzato') },
    { key: 'masonry', label: t('Masonry'), type: 'toggle' },
  ],

  // ─── STILE ─────────────────────────────────────────────────
  styleFields: [
    { type: 'separator', label: t('Preset stilistico') },
    { key: 'preset', label: t('Stile'), type: 'select', options: [
      { value: 'modern-clean',    label: t('Modern Clean') },
      { value: 'minimal-mono',    label: t('Minimal Mono') },
      { value: 'magazine-bold',   label: t('Magazine Bold') },
      { value: 'editorial-serif', label: t('Editorial Serif') },
      { value: 'compact-inline',  label: t('Compact Inline') },
      { value: 'glass-frosted',   label: t('Glass Frosted') },
      { value: 'neon-glow',       label: t('Neon Glow') },
      { value: 'brutalist-stamp', label: t('Brutalist Stamp') },
      { value: 'gradient-aurora', label: t('Gradient Aurora') },
      { value: 'sticker-fun',     label: t('Sticker Fun') },
      { value: 'retro-terminal',  label: t('Retro Terminal') },
      { value: 'tilt-3d',         label: t('3D Tilt') },
      { value: 'custom',          label: t('Personalizzato') },
    ] },
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },

    ...textEffectsFields([
      { value: 'title', label: t('Solo Titolo') },
      { value: 'content', label: t('Solo Contenuto') },
      { value: 'all', label: t('Tutti gli elementi testuali') },
    ]),

    { type: 'separator', label: t('Stile Card') },
    { key: 'card_style', label: t('Stile card'), type: 'select', options: [
      { value: 'default', label: t('Predefinito') },
      { value: 'minimal', label: t('Minimale') },
      { value: 'outlined', label: t('Bordo') },
      { value: 'elevated', label: t('Elevata') },
      { value: 'glass', label: t('Vetro (Glass)') },
      { value: 'gradient', label: t('Bordo sfumato') },
      { value: 'flat', label: t('Piatta') },
    ]},
    { key: 'card_hover', label: t('Effetto hover card'), type: 'select', options: [
      { value: 'none', label: t('Nessuno') },
      { value: 'lift', label: t('Solleva') },
      { value: 'scale', label: t('Ingrandisci') },
      { value: 'glow', label: t('Bagliore') },
      { value: 'border-glow', label: t('Bordo luminoso') },
      { value: 'tilt', label: t('Inclinazione 3D') },
    ]},
    withHover({ key: 'card_radius', label: t('Raggio'), type: 'border-radius'}),
    { key: 'tile_padding', label: t('Padding'), type: 'spacing', max: 48 },
    { key: 'card_bg_color', label: t('Colore sfondo card'), type: 'color' },
    { key: 'card_border_color', label: t('Colore bordo card'), type: 'color' },

    { type: 'separator', label: t('Immagine — stile') },
    { key: 'image_height', label: t('Altezza immagine'), type: 'range', min: 0, max: 500, step: 10,
      description: t('0 = automatica') },
    { key: 'image_animation_speed', label: t('Velocità animazione'), type: 'range', min: 2, max: 20, step: 1,
      condition: { field: 'image_animation', operator: '!=', value: 'none' } },

    // Elenco canonico al posto delle sette voci scelte a occhio (mancavano 21:9, 4:5,
    // 9:16). Nessuna tabella da allargare: sia il PHP sia il canvas scrivono
    // `aspect-ratio:<valore>` così com'e', quindi ogni rapporto nuovo rende subito.
    // 'auto' resta il default e continua a significare «usa Altezza immagine».
    { key: 'image_ratio', label: t('Proporzioni'), type: 'select', options: ratioOptions() },
    // Stesso discorso per l'adattamento: `object-fit` viene emesso tale e quale, quindi
    // le due voci che mancavano ('none' e 'scale-down') funzionano senza altro lavoro.
    { key: 'image_fit', label: t('Adattamento'), type: 'select', options: ADATTAMENTI },
    { key: 'object_position', label: t('Punto focale'), type: 'object-position', reveal: true,
      contextKeys: { fit: 'image_fit', ratio: 'image_ratio', ratioCustom: '' },
      description: t('Punto focale globale: applicato a tutte le immagini della griglia.'),
      // Con «Deforma per riempire» la foto viene stirata sui due assi: non resta
      // niente fuori dall'inquadratura da spostare.
      condition: { field: 'image_fit', op: 'neq', value: 'fill' } },
    { type: 'separator', label: t('Tipografia') },
    { type: 'typography', label: t('Titolo'),
      responsiveKeys: [],
      keys: {
        size:  'title_size',
        color: 'title_color',
      },
      sizeMin: 0, sizeMax: 48, sizeStep: 1,
    },
    { type: 'typography', label: t('Contenuto'),
      responsiveKeys: [],
      keys: {
        size:  'content_size',
        color: 'content_color',
      },
      sizeMin: 0, sizeMax: 24, sizeStep: 1,
    },

    { type: 'separator', label: t('Filtro — stile') },
    { key: 'filter_style', label: t('Stile filtro'), type: 'select', options: [
      { value: 'pills', label: t('Pills') },
      { value: 'minimal', label: t('Minimale') },
      { value: 'buttons', label: t('Bottoni') },
    ], condition: { field: 'show_filter', value: true } },
    { key: 'filter_align', label: t('Allineamento filtri'), type: 'select', options: [
      { value: 'left', label: t('Sinistra') },
      { value: 'center', label: t('Centro') },
      { value: 'right', label: t('Destra') },
    ], condition: { field: 'show_filter', value: true } },

    ...shadowField,
    { type: 'separator', label: t('Elementi') },
    { key: 'items', type: 'content-items', label: t('Elementi'), etichettaDa: 'title', miniaturaDa: 'image', itemFields: [
        { key: 'badge_color', label: t('Colore badge'), type: 'color' },
    ] },
    { type: 'separator', label: t('Disposizione') },
    { key: 'columns', label: t('Colonne'), type: 'select', responsive: true, options: [
      { value: '1', label: '1' },
      { value: '2', label: '2' },
      { value: '3', label: '3' },
      { value: '4', label: '4' },
      { value: '5', label: '5' },
      { value: '6', label: '6' },
    ]},
    { key: 'gap', label: t('Gap'), type: 'select', options: [
      { value: 'collapse', label: t('Collassato') },
      { value: 'small', label: t('Piccolo') },
      { value: 'default', label: t('Predefinito') },
      { value: 'medium', label: t('Medio') },
      { value: 'large', label: t('Grande') },
    ]},
    { key: 'equal_height', label: t('Altezza uguale'), type: 'toggle' },
    { key: 'overlay_position', label: t('Posizione testo'), type: 'select', options: [
      { value: 'bottom', label: t('In basso') },
      { value: 'center', label: t('Centrato') },
      { value: 'top', label: t('In alto') },
    ], condition: { field: 'overlay_text', op: 'eq', value: true } },
  ],
};
