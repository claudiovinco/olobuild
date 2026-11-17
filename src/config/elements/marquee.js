import { shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, focalField } from './_shared.js';
import { ADATTAMENTI } from './_imageFrame.js';
import { t } from '@/i18n';

/**
 * Tile Marquee — split CONTENUTO/STILE.
 *   fields[]      → content_type (text/images), text_items, separator, images, image_height, movimento (speed/direction/pause_hover/gap)
 *   styleFields[] → preset, bg, typo, colori (bg/text), tipografia (size/weight/spacing/transform), altezza, full_width, bordi top/bottom + color, shadow, border
 */
export default {
  type: 'marquee',
  name: t('Nastro Scorrevole'),
  icon: 'dashicons-slides',
  category: 'media',
  defaults: {
    preset: 'custom',
    bg: { type: 'none' },
    typography_preset: '',
    content_type: 'text',
    text_items: 'Testo scorrevole di esempio',
    separator: ' — ',
    images: [],
    image_height: '40',
    // CORNICE dei loghi, SENZA maschera di ritaglio. Qui `images` e' una galleria
    // (type:'gallery'), non un repeater: un solo selettore di proporzioni imporrebbe
    // la stessa forma a TUTTI i marchi insieme, ed e' esattamente cio' che il
    // `width:auto` del nastro evita — ogni logo tiene la sua sagoma. Resta il solo
    // ADATTAMENTO, che nasce 'contain' (era cablato nella regola .olo-mq-img) e NON
    // 'cover' come nella tile Immagine: su un logo 'cover' taglierebbe via i bordi.
    image_fit: 'contain',
    image_object_position: 'center center',
    speed: '30',
    direction: 'left',
    pause_hover: true,
    drag_scroll: false,          // scorrimento libero: nastro trascinabile (mouse/touch)
    gap: '60',
    // VelocitySkew (reattivo allo scroll) — default OFF: i Marquee esistenti restano invariati
    velocity_skew: false,
    vskew_base_speed: 0.6,
    vskew_scroll_boost: 0.6,
    vskew_max_skew: 14,
    vskew_damping: 0.86,
    bg_color: 'var(--olo-color-dark, #16263d)',
    text_color: '',
    font_size: '16',
    font_weight: '500',
    letter_spacing: '1',
    text_transform: 'uppercase',
    font_family: '',
    font_style: 'normal',
    separator_color: '',
    separator_size: '',
    height: '50',
    full_width: false,
    border_top: '0',
    border_bottom: '0',
    border_color: '',
    shadow: 'none',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // Come nasce dalla palette: una fascia nel colore del sito con quattro messaggi di servizio che
  // scorrono separati da un punto, il testo nel contrasto del primario (leggibile per costruzione)
  // e la pausa al passaggio del mouse.
  partenza: {
    text_items: t('Prenota online in un minuto|Aperti dal lunedì al sabato|Consegna gratuita da 50 €|Oltre 500 clienti soddisfatti'),
    separator: '•',
    bg_color: 'var(--olo-color-primary, #e1474f)',
    text_color: 'var(--olo-color-primary-contrast, #ffffff)',
    height: '56',
  },

  fields: [
    { key: 'content_type', label: t('Contenuto'), type: 'select', options: [
      { value: 'text', label: t('Testo') },
      { value: 'images', label: t('Immagini') },
    ]},
    // Le frasi si scrivono una per riga (16 nov 2026): prima tutte in una riga separate da «|». Il renderer accetta
    // l'una e l'altra forma, e i nastri salvati con «|» si vedono già una frase per riga.
    { key: 'text_items', label: t('Frasi (una per riga)'), type: 'textarea', righeDa: '|',
      condition: { field: 'content_type', value: 'text' } },
    { key: 'separator', label: t('Separatore'), type: 'text',
      condition: { field: 'content_type', value: 'text' } },
    { key: 'images', label: t('Immagini'), type: 'gallery', supportsDynamic: true,
      dynamicItemFields: [
        { key: 'url', label: t('Immagine'), type: 'image' },
        { key: 'alt', label: t('Testo alternativo'), type: 'text' },
      ],
      condition: { field: 'content_type', value: 'images' } },

    { type: 'separator', label: t('Movimento') },
    { key: 'speed', label: t('Durata di un giro'), type: 'range', unit: 's', min: 5, max: 80, step: 5 },
    { key: 'direction', label: t('Direzione'), type: 'select', options: [
      { value: 'left', label: t('Verso sinistra') },
      { value: 'right', label: t('Verso destra') },
    ]},
    { key: 'pause_hover', label: t('Pausa al passaggio del mouse'), type: 'toggle' },
    { key: 'drag_scroll', label: t('Si trascina col dito o col mouse'), type: 'toggle',
      description: t('Il nastro si può trascinare con mouse o dito; al rilascio riprende a scorrere da solo. Il loop resta continuo.') },

    { type: 'separator', label: t('Reazione allo scorrimento') },
    { key: 'velocity_skew', label: t('Accelera e si inclina scorrendo'), type: 'toggle',
      description: t('Lo scorrimento della pagina aggiunge spinta e inclinazione al nastro, che si smorza quando ti fermi. Con «riduci movimento» del sistema resta solo lo scorrimento di base.') },
    { key: 'vskew_base_speed', label: t('Velocità di base'), type: 'range', min: 0, max: 3, step: 0.1,
      condition: { field: 'velocity_skew', op: 'eq', value: true } },
    { key: 'vskew_scroll_boost', label: t('Spinta dello scorrimento'), type: 'range', min: 0, max: 2, step: 0.05,
      condition: { field: 'velocity_skew', op: 'eq', value: true } },
    { key: 'vskew_max_skew', label: t('Inclinazione massima'), type: 'range', unit: '°', min: 0, max: 30, step: 1,
      condition: { field: 'velocity_skew', op: 'eq', value: true } },
    { key: 'vskew_damping', label: t('Smorzamento'), type: 'range', min: 0.5, max: 0.98, step: 0.01,
      condition: { field: 'velocity_skew', op: 'eq', value: true } },
  ],

  // Zone standard (16 nov 2026): Aspetto · Testo · Immagini · Forma · Bordo · Disposizione. Prima «Preset stilistico» coi
  // nomi in inglese e stili che non agivano, «Tipografia» e «Font & separatore» separati, «Aspetto» in fondo.
  styleFields: [
    { type: 'separator', label: t('Aspetto') },
    { key: 'preset', label: t('Stile'), type: 'select', options: [
      { value: 'striscia', label: t('Striscia piena') },
      { value: 'fili', label: t('Tra due fili') },
      { value: 'titoli', label: t('Titoli grandi') },
      { value: 'corsivo', label: t('Corsivo') },
      { value: 'notte', label: t('Notte') },
      { value: 'avviso', label: t('Avviso') },
      { value: 'custom',          label: t('Personalizzato') },
    ] },
    { key: 'bg_color', label: t('Sfondo nastro'), type: 'color' },
    ...shadowField,

    { type: 'separator', label: t('Testo'),
      condition: { field: 'content_type', value: 'text' } },
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography',
      condition: { field: 'content_type', value: 'text' } },
    // Famiglia e corsivo stavano in due campi a parte: ora sono nel controllo tipografia (chiavi INVARIATE).
    { type: 'typography', label: t('Frasi'),
      responsiveKeys: [],
      keys: {
        family:        'font_family',
        size:          'font_size',
        weight:        'font_weight',
        style:         'font_style',
        letterSpacing: 'letter_spacing',
        transform:     'text_transform',
        color:         'text_color',
      },
      sizeMin: 12, sizeMax: 48, sizeStep: 1,
    },
    { key: 'separator_color', label: t('Colore separatore'), type: 'color',
      description: t('Vuoto: il colore delle frasi.') },
    { key: 'separator_size', label: t('Dimensione separatore'), type: 'range', unit: 'px', min: 0, max: 48, step: 1,
      description: t('0: la dimensione delle frasi.') },

    { type: 'separator', label: t('Immagini'),
      condition: { field: 'content_type', value: 'images' } },
    { key: 'image_height', label: t('Altezza immagini'), type: 'range', unit: 'px', min: 20, max: 120 },
    // NIENTE selettore di proporzioni: il nastro e' fatto di loghi di sagoma diversa
    // e l'altezza e' l'unica dimensione imposta (width:auto). Una maschera comune
    // riempirebbe di bande vuote ogni marchio che non ha la forma dello slot.
    { key: 'image_fit', label: t('Adattamento'), type: 'select', options: ADATTAMENTI },
    // Il focale morde solo con «Dimensione originale», dove il logo viene tagliato dall'altezza del nastro. La guardia
    // e' `neq 'fill'`: un nastro salvato prima che il campo esistesse non ha la chiave `image_fit`.
    focalField('image', { src: '', fit: 'image_fit',
      condition: { field: 'image_fit', op: 'neq', value: 'fill' } }),

    { type: 'separator', label: t('Forma') },
    { key: 'height', label: t('Altezza nastro'), type: 'range', unit: 'px', min: 30, max: 120 },
    { key: 'border_top', label: t('Filo sopra'), type: 'range', unit: 'px', min: 0, max: 4 },
    { key: 'border_bottom', label: t('Filo sotto'), type: 'range', unit: 'px', min: 0, max: 4 },
    { key: 'border_color', label: t('Colore fili'), type: 'color',
      show: (s) => parseInt(s.border_top, 10) > 0 || parseInt(s.border_bottom, 10) > 0 },

    ...borderFields(),

    { type: 'separator', label: t('Disposizione') },
    { key: 'gap', label: t('Gap'), type: 'range', unit: 'px', min: 20, max: 120, step: 10 },
    { key: 'full_width', label: t('A tutta larghezza della finestra'), type: 'toggle' },
  ],
};
