
import { borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, withHover } from './_shared.js';
import { demo } from '../demoMedia.js';
import { t } from '@/i18n';

/**
 * Tile RevealBox — split CONTENUTO/STILE.
 *   fields[]      → effetto reveal (type+perspective), sfondo globale (media),
 *                   zona visibile (top_media+top_content+top_icon), zona rivelata (bottom_media+bottom_content+bottom_icon+cta),
 *                   reveal_amount per slide
 *   styleFields[] → visible_height, icon size/color, content tipografia, overlay colori,
 *                   transition speed/easing, border radius, padding, border
 *
 * Sfondi UNIFICATI sul pannello media universale (type:'background'), PER ZONA:
 *   - `media`        → sfondo globale dietro entrambe le zone (ex image_url/image_position/image_size)
 *   - `top_media`    → sfondo zona visibile   (ex top_image_url/top_image_position/top_video_url)
 *   - `bottom_media` → sfondo zona rivelata   (ex bottom_image_url/bottom_image_position/bottom_video_url)
 * Non distruttivo: le chiavi legacy restano nei defaults come FALLBACK; i renderer
 * (Vue+PHP) preferiscono il media object quando type!=none.
 */
export default {
  type: 'revealbox',
  name: t('Reveal Box'),
  icon: 'dashicons-arrow-up-alt',
  category: 'interactive',

  // Migrazione NON distruttiva → un media object per zona (array di spec).
  // image_url globale → `media`; top_* → `top_media`; bottom_* → `bottom_media`.
  bgMigrate: [
    { imageKey: 'image_url',        imageSizeKey: 'image_size',        imagePosKey: 'image_position',        target: 'media' },
    { imageKey: 'top_image_url',    imageSizeKey: 'top_image_size',    imagePosKey: 'top_image_position',    videoKey: 'top_video_url',    target: 'top_media' },
    { imageKey: 'bottom_image_url', imageSizeKey: 'bottom_image_size', imagePosKey: 'bottom_image_position', videoKey: 'bottom_video_url', target: 'bottom_media' },
  ],

  defaults: {
    typography_preset: '',
    media: { type: 'none' },
    top_media: { type: 'none' },
    bottom_media: { type: 'none' },
    visible_height: '300',
    top_image_url: '',
    top_image_position: 'center center',
    top_image_size: 'cover',
    top_video_url: '',
    bottom_image_url: '',
    bottom_image_position: 'center center',
    bottom_image_size: 'cover',
    bottom_video_url: '',
    top_content: '<h3>Titolo</h3>',
    bottom_content: '<p>Contenuto rivelato al passaggio del mouse</p>',
    top_icon: '',
    top_icon_size: '2',
    top_icon_color: 'var(--olo-color-light, #f8f9fa)',
    bottom_icon: '',
    bottom_icon_size: '2',
    bottom_icon_color: 'var(--olo-color-light, #f8f9fa)',
    reveal_effect: 'slide-up',
    reveal_amount: '',
    transition_speed: '0.5',
    transition_easing: 'ease',
    top_text_color: 'var(--olo-color-light, #f8f9fa)',
    top_font_size: '',
    bottom_text_color: 'var(--olo-color-light, #f8f9fa)',
    bottom_font_size: '',
    overlay_color: 'var(--olo-color-dark, #16263d)',
    overlay_opacity: '0',
    reveal_overlay_color: 'var(--olo-color-dark, #16263d)',
    reveal_overlay_opacity: '60',
    text_color: 'var(--olo-color-light, #f8f9fa)',
    top_align: 'flex-end',
    top_justify: 'flex-start',
    bottom_align: 'flex-start',
    bottom_justify: 'flex-start',
    top_padding: '24',
    bottom_padding: '24',
    border_radius: { tl: 0, tr: 0, br: 0, bl: 0 },
    perspective: '800',
    tile_padding: { top: 0, right: 0, bottom: 0, left: 0 },
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // Come nasce dalla palette: la foto di una tavola apparecchiata con il nome della serata su un
  // velo leggero; al passaggio del mouse il riquadro scorre in su e svela, su una seconda foto
  // velata, il menu con il prezzo. Raggio morbido e transizione fluida.
  partenza: {
    visible_height: '340',
    border_radius: { tl: 16, tr: 16, br: 16, bl: 16 },
    top_media: { type: 'image', image_url: demo('tavola'), image_size: 'cover', image_position: 'center center' },
    // Paragrafi e non un titolo: il colore del testo della zona non arriva ai titoli (h1-h6), che
    // prendono quello del tema e sulla foto non si leggevano.
    top_content: t('<p><strong>La cena in terrazza</strong></p><p>Il venerdì e il sabato sera</p>'),
    top_font_size: '20',
    top_padding: '28',
    overlay_opacity: '30',
    bottom_media: { type: 'image', image_url: demo('piatti-alto'), image_size: 'cover', image_position: 'center center' },
    bottom_content: t('<p><strong>Menu degustazione · 45 €</strong></p><p>Cinque portate con i vini abbinati, dalle 20. Prenota entro il giovedì.</p>'),
    reveal_overlay_opacity: '70',
    bottom_align: 'center',
    bottom_justify: 'center',
    bottom_padding: '32',
    transition_speed: '0.6',
    transition_easing: 'cubic-bezier(0.4,0,0.2,1)',
    // Conflitti PHP/config: vince il config (riserve del tema chiaro/scuro).
    top_icon_color: 'var(--olo-color-light, #f8f9fa)',
    bottom_icon_color: 'var(--olo-color-light, #f8f9fa)',
    top_text_color: 'var(--olo-color-light, #f8f9fa)',
    bottom_text_color: 'var(--olo-color-light, #f8f9fa)',
    text_color: 'var(--olo-color-light, #f8f9fa)',
    overlay_color: 'var(--olo-color-dark, #16263d)',
    reveal_overlay_color: 'var(--olo-color-dark, #16263d)',
  },

  fields: [
    { type: 'separator', label: t('Effetto reveal') },
    { key: 'reveal_effect', label: t('Tipo effetto'), type: 'select', options: [
      { value: 'slide-up', label: t('Scorrimento ↑') },
      { value: 'slide-down', label: t('Scorrimento ↓') },
      { value: 'slide-left', label: t('Scorrimento ←') },
      { value: 'slide-right', label: t('Scorrimento →') },
      { value: 'fade', label: t('Dissolvenza') },
      { value: 'zoom-in', label: t('Zoom in') },
      { value: 'zoom-out', label: t('Zoom out') },
      { value: 'flip-x', label: t('Giro 3D orizzontale') },
      { value: 'flip-y', label: t('Giro 3D verticale') },
      { value: 'rotate-in', label: t('Rotazione') },
    ]},
    { key: 'reveal_amount', label: t('Scorrimento (px, 0 = auto)'), type: 'number', min: 0, max: 800, placeholder: t('auto'),
      condition: { field: 'reveal_effect', operator: 'in', value: ['slide-up', 'slide-down', 'slide-left', 'slide-right'] } },



    { type: 'separator', label: t('Zona visibile — Contenuto') },
    { key: 'top_icon', label: t('Icona'), type: 'icon' },
    { key: 'top_content', label: t('Contenuto visibile'), type: 'editor', mode: 'block' },


    { type: 'separator', label: t('Zona rivelata — Contenuto') },
    { key: 'bottom_icon', label: t('Icona'), type: 'icon' },
    { key: 'bottom_content', label: t('Contenuto rivelato'), type: 'editor', mode: 'block' },
  ],

  styleFields: [
    { type: 'separator', label: t('Effetto reveal — Aspetto') },
    { key: 'perspective', label: t('Prospettiva 3D'), type: 'range', min: 200, max: 2000, step: 50,
      condition: { field: 'reveal_effect', operator: 'in', value: ['flip-x', 'flip-y'] } },

    { type: 'separator', label: t('Zona visibile — Aspetto') },
    { key: 'visible_height', label: t('Altezza visibile'), type: 'range', min: 100, max: 800, step: 10 },
    { key: 'top_icon_size', label: t('Dimensione icona'), type: 'range', min: 0.5, max: 6, step: 0.1,
      condition: { field: 'top_icon', operator: '!=', value: '' } },
    { key: 'top_icon_color', label: t('Colore icona'), type: 'color',
      condition: { field: 'top_icon', operator: '!=', value: '' } },
    { key: 'top_media', label: t('Zona alta — sfondo (immagine, video, gradiente…)'), type: 'background', showParallax: false },
    { type: 'separator', label: t('Tipografia') },
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },
    { type: 'typography', label: t('Zona Visibile'),
      responsiveKeys: [],
      keys: {
        size:  'top_font_size',
        color: 'top_text_color',
      },
      sizeMin: 10, sizeMax: 72,
    },
    { key: 'top_align', label: t('Allineamento verticale'), type: 'select', options: [
      { value: 'flex-start', label: t('Alto') },
      { value: 'center', label: t('Centro') },
      { value: 'flex-end', label: t('Basso') },
    ]},
    { key: 'top_justify', label: t('Allineamento orizzontale'), type: 'select', options: [
      { value: 'flex-start', label: t('Sinistra') },
      { value: 'center', label: t('Centro') },
      { value: 'flex-end', label: t('Destra') },
    ]},
    { key: 'top_padding', label: t('Padding'), type: 'spacing', min: 0, max: 120 },

    { type: 'separator', label: t('Zona visibile — Overlay') },
    { key: 'overlay_color', label: t('Colore overlay'), type: 'color' },
    { key: 'overlay_opacity', label: t('Opacità overlay'), type: 'range', min: 0, max: 100 },

    { type: 'separator', label: t('Zona rivelata — Aspetto') },
    { key: 'bottom_icon_size', label: t('Dimensione icona'), type: 'range', min: 0.5, max: 6, step: 0.1,
      condition: { field: 'bottom_icon', operator: '!=', value: '' } },
    { key: 'bottom_icon_color', label: t('Colore icona'), type: 'color',
      condition: { field: 'bottom_icon', operator: '!=', value: '' } },
    { key: 'bottom_media', label: t('Zona bassa — sfondo (immagine, video, gradiente…)'), type: 'background', showParallax: false },
    { type: 'separator', label: t('Tipografia') },
    { type: 'typography', label: t('Zona Rivelata'),
      responsiveKeys: [],
      keys: {
        size:  'bottom_font_size',
        color: 'bottom_text_color',
      },
      sizeMin: 10, sizeMax: 72,
    },
    { key: 'bottom_align', label: t('Allineamento verticale'), type: 'select', options: [
      { value: 'flex-start', label: t('Alto') },
      { value: 'center', label: t('Centro') },
      { value: 'flex-end', label: t('Basso') },
    ]},
    { key: 'bottom_justify', label: t('Allineamento orizzontale'), type: 'select', options: [
      { value: 'flex-start', label: t('Sinistra') },
      { value: 'center', label: t('Centro') },
      { value: 'flex-end', label: t('Destra') },
    ]},
    { key: 'bottom_padding', label: t('Padding'), type: 'spacing', min: 0, max: 120 },

    { type: 'separator', label: t('Zona rivelata — Overlay') },
    { key: 'reveal_overlay_color', label: t('Colore overlay rivelato'), type: 'color' },
    { key: 'reveal_overlay_opacity', label: t('Opacità overlay rivelato'), type: 'range', min: 0, max: 100 },

    { type: 'separator', label: t('Transizione') },
    { key: 'transition_speed', label: t('Velocità transizione'), type: 'range', min: 0.1, max: 2, step: 0.1 },
    { key: 'transition_easing', label: t('Curva transizione'), type: 'select', options: [
      { value: 'ease', label: t('Ease') },
      { value: 'ease-in-out', label: t('Ease In/Out') },
      { value: 'ease-out', label: t('Ease Out') },
      { value: 'cubic-bezier(0.4,0,0.2,1)', label: t('Smooth') },
      { value: 'linear', label: t('Lineare') },
    ]},
    withHover({ key: 'border_radius', label: t('Raggio'), type: 'border-radius' }),
    // `tile_padding` resta nei defaults ma non ha più un campo: nessun renderer lo leggeva
    // (il padding delle zone è nei loro «Padding», quello del riquadro nel Contenitore).

    ...borderFields(), // bordo, hover ed effetti sul riquadro (ora ha la classe $uid che il PHP usa)
    { type: 'separator', label: t('Sfondo globale') },
    { key: 'media', label: t('Sfondo entrambe le zone (immagine, video, gradiente…)'), type: 'background', showParallax: false },
  ],
};
