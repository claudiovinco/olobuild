import { textEffectsFields, textEffectsDefaults, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, withHover } from './_shared';
import { ratioOptions, ADATTAMENTI } from './_imageFrame.js';
import { demo, demoAlt } from '../demoMedia.js';
import { t } from '@/i18n';

/**
 * Tile Hotspot — split CONTENUTO/STILE.
 *   fields[]      → immagine + testo alternativo, markers (pos_x+pos_y+title+description+icon+link+link_label)
 *   styleFields[] → preset, bg, typo, image height + radius, text-effects, marker tipo/color/size/pulse, tooltip_position (specchio), tooltip colori/width, border
 */
export default {
  type: 'hotspot',
  name: t('Hotspot'),
  icon: 'dashicons-location-alt',
  category: 'interactive',
  defaults: {
    bg: { type: 'none' },
    typography_preset: '',
    preset: 'custom',
    image: '',
    image_alt: '',
    image_height: '400',
    // 'auto' = la resa di sempre: il contenitore tiene l'altezza fissa di
    // `image_height` (400px) e il ritaglio dipende dalla larghezza della colonna.
    // Un rapporto cablato qui (16/9 o altro) cambierebbe l'inquadratura di ogni
    // hotspot già pubblicato, perché i marker sono in percentuale sul riquadro.
    aspect_ratio: 'auto',
    aspect_ratio_custom: '16/9',
    // Era cablato nel CSS di entrambi i renderer: esposto, ma con lo stesso valore.
    object_fit: 'cover',
    object_position: 'center center',
    markers: [
      { id: 'hs-1', pos_x: '30', pos_y: '40', title: t('Punto di interesse'), description: t('Descrizione del primo hotspot.'), icon: 'pin', tooltip_position: 'top' },
      { id: 'hs-2', pos_x: '65', pos_y: '55', title: t('Secondo punto'), description: t('Descrizione del secondo hotspot.'), icon: 'pin', tooltip_position: 'bottom' },
    ],
    border_radius: '0',
    marker_style: 'icon',
    marker_color: '',
    marker_text_color: '',
    marker_size: '24',
    pulse_animation: true,
    tooltip_bg: '',
    tooltip_color: '',
    tooltip_width: '220',
    ...textEffectsDefaults,
    text_effect_target: 'all',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // Come nasce dalla palette: la foto di uno spazio di lavoro in 16:9 con tre punti numerati che
  // pulsano (la vista, la postazione, lo schermo); ogni numero apre il suo fumetto.
  partenza: {
    image: demo('ufficio'),
    image_alt: demoAlt('ufficio'),
    aspect_ratio: '16/9',
    border_radius: '16',
    markers: [
      { id: 'hs-1', pos_x: '37', pos_y: '46', title: t('Vista sulla città'), description: t('Vetrate dal pavimento al soffitto: luce naturale per tutta la giornata.'), icon: 'pin', tooltip_position: 'bottom' },
      { id: 'hs-2', pos_x: '45', pos_y: '80', title: t('Postazione ergonomica'), description: t('Sedia regolabile e scrivania ampia, per lavorare comodi anche tutto il giorno.'), icon: 'pin', tooltip_position: 'top' },
      { id: 'hs-3', pos_x: '77', pos_y: '62', title: t('Schermo da 27 pollici'), description: t('In ogni postazione, con tastiera, mouse e connessione in fibra.'), icon: 'pin', tooltip_position: 'left' },
    ],
    marker_style: 'number',
    marker_color: 'var(--olo-color-primary)',
    marker_text_color: 'var(--olo-color-primary-contrast)',
    marker_size: '30',
  },

  fields: [
    { key: 'image', label: t('Immagine'), type: 'image' },
    { key: 'image_alt', label: t('Testo alternativo'), type: 'text',
      description: t('Descrive l’immagine a chi non la vede. Vuoto = il testo alternativo dell’immagine nella libreria media.') },
    { key: 'markers', label: t('Marker'), type: 'content-items',
      itemFields: [
        { key: 'pos_x', label: t('Da sinistra'), type: 'range', min: 0, max: 100, step: 1, unit: '%',
          description: t('Dove sta il marker sull’immagine: 0 è il bordo sinistro, 100 il destro.') },
        { key: 'pos_y', label: t('Dall’alto'), type: 'range', min: 0, max: 100, step: 1, unit: '%',
          description: t('0 è il bordo in alto, 100 quello in basso.') },
        { key: 'title', label: t('Titolo'), type: 'text' },
        { key: 'description', label: t('Descrizione'), type: 'textarea' },
        { key: 'icon', label: t('Icona'), type: 'icon',
          condizioneTile: { field: 'marker_style', op: 'eq', value: 'icon' } },
        { key: 'image', label: t('Foto nel fumetto'), type: 'image',
          description: t('Facoltativa: una foto in cima al fumetto, sopra il titolo.') },
        { key: 'link', label: t('Link'), type: 'link',
          description: t('Facoltativo: compare nel fumetto come collegamento sotto la descrizione.') },
        { key: 'link_label', label: t('Testo del link'), type: 'text', placeholder: t('Scopri di più'),
          condition: { field: 'link', op: 'notEmpty' } },
      ],
      newItemDefaults: { pos_x: '50', pos_y: '50', title: t('Nuovo punto'), description: t('Descrizione.'), icon: 'pin', tooltip_position: 'top', link: '', link_label: '' },
      itemLabel: t('Marker'),
    },
  ],

  styleFields: [
    // ── Aspetto ──
    { type: 'separator', label: t('Aspetto') },
    { key: 'preset', label: t('Stile'), type: 'select', options: [
      { value: 'numeri',     label: t('Numeri') },
      { value: 'punti',      label: t('Punti') },
      { value: 'icone',      label: t('Icone') },
      { value: 'scuro',      label: t('Scuro') },
      { value: 'vetro',      label: t('Vetro') },
      { value: 'editoriale', label: t('Editoriale') },
      { value: 'custom',     label: t('Personalizzato') },
    ], description: t('Cambia insieme marker, fumetto e raggio dell’immagine, coi colori del sito. Poi ogni controllo qui sotto si regola da solo.') },

    // ── Immagine ──
    { type: 'separator', label: t('Immagine') },
    withHover({ key: 'border_radius', label: t('Raggio immagine'), type: 'border-radius' }),
    { key: 'aspect_ratio', label: t('Proporzioni'), type: 'select', options: ratioOptions({ custom: true }),
      description: t('«Auto» tiene l’altezza fissa qui sotto, com’è sempre stato.') },
    { key: 'aspect_ratio_custom', label: t('Proporzioni personalizzate'), type: 'text',
      placeholder: t('es. 5/4'),
      condition: { field: 'aspect_ratio', op: 'eq', value: 'custom' } },
    // Con un rapporto scelto l'altezza la calcola il rapporto: lasciare il cursore
    // visibile darebbe un controllo che non muove niente.
    // Scritto come `show` e non come `condition: eq 'auto'` perché l'inspector valuta
    // sui settings GREZZI, senza fondere i default: un hotspot salvato PRIMA di questo
    // sprint non ha affatto la chiave `aspect_ratio`, e con l'uguaglianza il cursore
    // dell'altezza sarebbe sparito proprio dalle tile già pubblicate — quelle che
    // l'altezza ce l'hanno davvero. Chiave assente = 'auto' = cursore visibile.
    { key: 'image_height', label: t('Altezza immagine'), type: 'range', min: 200, max: 800, step: 10, unit: 'px',
      show: (s) => (s.aspect_ratio || 'auto') === 'auto' },
    { key: 'object_fit', label: t('Adattamento'), type: 'select', options: ADATTAMENTI },
    { key: 'object_position', label: t('Punto focale'), type: 'object-position',
      contextKeys: { src: 'image', height: 'image_height', ratio: 'aspect_ratio', ratioCustom: 'aspect_ratio_custom', fit: 'object_fit' },
      condition: { field: 'object_fit', op: 'neq', value: 'fill' } },

    // ── Marker ──
    { type: 'separator', label: t('Marker') },
    { key: 'marker_style', label: t('Forma del marker'), type: 'select', options: [
      { value: 'icon',   label: t('Icona') },
      { value: 'number', label: t('Numero') },
      { value: 'dot',    label: t('Punto') },
    ]},
    { key: 'marker_color', label: t('Colore marker'), type: 'color',
      description: t('Vuoto: il colore principale del sito.') },
    { key: 'marker_text_color', label: t('Colore numero'), type: 'color',
      condition: { field: 'marker_style', op: 'eq', value: 'number' } },
    { key: 'marker_size', label: t('Dimensione marker'), type: 'range', min: 16, max: 40, step: 2, unit: 'px' },
    { key: 'pulse_animation', label: t('Marker che pulsano'), type: 'toggle',
      description: t('Un anello si allarga attorno a ogni marker, per dire che si può toccare.') },

    // ── Fumetto ──
    { type: 'separator', label: t('Fumetto') },
    { key: 'tooltip_bg', label: t('Sfondo fumetto'), type: 'color',
      description: t('Vuoto: la superficie del sito. Con un colore semitrasparente il fumetto diventa vetro smerigliato.') },
    { key: 'tooltip_color', label: t('Colore testo fumetto'), type: 'color' },
    { key: 'tooltip_width', label: t('Larghezza fumetto'), type: 'range', min: 150, max: 350, step: 10, unit: 'px' },
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },
    { type: 'typography', label: t('Titolo'), responsiveKeys: [],
      keys: { family: 'hs_title_font_family', size: 'hs_title_font_size', weight: 'hs_title_font_weight', color: 'hs_title_color' },
      sizeMin: 11, sizeMax: 28, sizeStep: 1 },
    { type: 'typography', label: t('Descrizione'), responsiveKeys: [],
      keys: { family: 'hs_desc_font_family', size: 'hs_desc_font_size', lineHeight: 'hs_desc_line_height', color: 'hs_desc_color' },
      sizeMin: 11, sizeMax: 22, sizeStep: 1 },
    { key: 'markers', type: 'content-items', label: t('Dove si apre il fumetto'), itemLabel: t('Marker'), etichettaDa: 'title', itemFields: [
        { key: 'tooltip_position', label: t('Posizione del fumetto'), type: 'select', options: [
          { value: 'top', label: t('Sopra') },
          { value: 'bottom', label: t('Sotto') },
          { value: 'left', label: t('A sinistra') },
          { value: 'right', label: t('A destra') },
        ], description: t('Se non ci sta, il fumetto si sposta o si gira da solo per restare nello schermo.') },
    ] },

    ...textEffectsFields([
      { value: 'title', label: t('Solo Titolo') },
      { value: 'description', label: t('Solo Descrizione') },
      { value: 'all', label: t('Tutti gli elementi testuali') },
    ]),

    ...borderFields(),
  ],
};
