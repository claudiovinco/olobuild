import { textEffectsFields, textEffectsDefaults, shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, withHover, wowEffectsFields, wowEffectsDefaults } from './_shared.js';
import { ratioOptions, ADATTAMENTI } from './_imageFrame.js';
import { t } from '@/i18n';

/**
 * Tile Panel — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → titolo, meta, contenuto, media (image/video), link
 *   styleFields[] → preset, effect tweaks, text-effects, dimensioni media, tipografia, padding card, radius, shadow, border, bg
 *   AVANZATE      → meta tecnico
 */
export default {
  type: 'panel',
  name: t('Pannello'),
  icon: 'dashicons-id-alt',
  category: 'interactive',
  defaults: {
    typography_preset: '',
    preset: 'card-classic',
    style: 'default',
    title: t('Titolo Provvisorio'),
    meta: 'Scritto da Autore',
    content: 'Il contenuto del pannello va qui. Aggiungi testo, immagini o qualsiasi altro contenuto.',
    media_type: 'image',
    image: '',
    effect_color: '',
    effect_intensity: 'medium',
    effect_speed: 0,
    image_ratio: 'auto',
    // Prefilled col rapporto più comune: «Personalizzato» non cambia nulla finché non
    // lo si riscrive. (Con image_ratio:'auto' — il default — non viene nemmeno letto.)
    image_ratio_custom: '16/9',
    image_height: '',
    image_fit: 'cover',
    object_position: 'center center',
    image_zoom: false,
    media_padding: { top: 0, right: 0, bottom: 0, left: 0 },
    text_align: 'left',
    title_size: '',
    title_weight: '',
    title_color: '',
    meta_size: '',
    meta_color: '',
    content_size: '',
    content_color: '',
    link_label: '',
    link_color: '',
    hover_image: '',
    hover_video: '',
    video: '',
    video_autoplay: true,
    video_loop: true,
    video_muted: true,
    video_controls: false,
    video_poster: '',
    link_url: '',
    link_target: '_self',
    title_element: 'h3',
    card_padding: { top: 20, right: 20, bottom: 20, left: 20 },
    shadow: 'none',
    border_radius: '0',
    card_radius: '0',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
    ...wowEffectsDefaults,
    ...textEffectsDefaults,
    text_effect_target: 'all',
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { key: 'title', label: t('Titolo'), type: 'text' },
    { key: 'meta', label: t('Meta'), type: 'text' },
    { key: 'content', label: t('Contenuto'), type: 'textarea' },

    { type: 'separator', label: t('Media') },
    { key: 'media_type', label: t('Tipo media'), type: 'select', options: [
      { value: 'none', label: t('Nessuno') },
      { value: 'image', label: t('Immagine') },
      { value: 'video', label: t('Video') },
    ]},
    { key: 'image', label: t('Immagine'), type: 'image',
      condition: { field: 'media_type', op: 'eq', value: 'image' } },
    { key: 'hover_image', label: t('Immagine hover'), type: 'image',
      condition: { field: 'media_type', op: 'eq', value: 'image' } },
    { key: 'hover_video', label: t('Video hover'), type: 'media',
      condition: { field: 'media_type', op: 'eq', value: 'image' } },
    { key: 'video', label: t('Video (mp4)'), type: 'media',
      condition: { field: 'media_type', op: 'eq', value: 'video' } },
    { key: 'video_poster', label: t('Poster (immagine anteprima)'), type: 'image',
      condition: { field: 'media_type', op: 'eq', value: 'video' } },
    { key: 'video_autoplay', label: t('Autoplay'), type: 'toggle',
      condition: { field: 'media_type', op: 'eq', value: 'video' } },
    { key: 'video_loop', label: t('Loop'), type: 'toggle',
      condition: { field: 'media_type', op: 'eq', value: 'video' } },
    { key: 'video_muted', label: t('Muto'), type: 'toggle',
      condition: { field: 'media_type', op: 'eq', value: 'video' } },
    { key: 'video_controls', label: t('Mostra controlli'), type: 'toggle',
      condition: { field: 'media_type', op: 'eq', value: 'video' } },

    { type: 'separator', label: t('Link') },
    { key: 'link_url', label: t('URL link'), type: 'link' },
    { key: 'link_target', label: t('Apri in'), type: 'select', options: [
      { value: '_self', label: t('Stessa finestra') },
      { value: '_blank', label: t('Nuova finestra') },
    ]},
    { key: 'link_label', label: t('Etichetta link'), type: 'text',
      description: 'Vuoto = "Read more →"',
      condition: { field: 'link_url', op: 'notEmpty' } },
  ],

  // ─── STILE ─────────────────────────────────────────────────
  styleFields: [
    { type: 'separator', label: t('Preset stile') },
    { key: 'preset', label: t('Stile'), type: 'select', options: [
      { value: 'card-classic',     label: t('Card Classic (default)') },
      { value: 'magazine',         label: t('Magazine (foto grande)') },
      { value: 'editorial',        label: t('Editorial (no bordi)') },
      { value: 'polaroid',         label: t('Polaroid (bordo bianco)') },
      { value: 'overlay-caption',  label: t('Overlay Caption (testo su foto)') },
      { value: 'liquid-glass',     label: t('Liquid Glass (Vision Pro)') },
      { value: 'neon-cyber',       label: t('Neon Cyberpunk (Tron)') },
      { value: 'brutalist-block',  label: t('Brutalist Block (neo-brutalist)') },
      { value: 'magnetic-liquid',  label: t('Magnetic Liquid (next-gen)') },
      { value: 'sticker',          label: t('Sticker / Scrapbook') },
      { value: 'retro-terminal',   label: t('Retro Terminal (CRT)') },
      { value: '3d-tilt',          label: t('3D Card Tilt') },
      { value: 'custom',           label: t('Personalizzato (usa controlli sotto)') },
    ]},
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },

    { key: 'style', label: t('Stile UIkit'), type: 'select', options: [
      { value: 'default', label: t('Predefinito') },
      { value: 'primary', label: t('Primary') },
      { value: 'secondary', label: t('Secondary') },
      { value: 'hover', label: t('Hover') },
    ]},

    { type: 'separator', label: t('Tweak effetto preset'),
      condition: { field: 'preset', op: 'in', value: ['liquid-glass','neon-cyber','brutalist-block','magnetic-liquid','sticker','retro-terminal','3d-tilt'] } },
    { key: 'effect_color', label: t('Colore effetto'), type: 'color',
      condition: { field: 'preset', op: 'in', value: ['neon-cyber','brutalist-block','magnetic-liquid','sticker','retro-terminal'] } },
    { key: 'effect_intensity', label: t('Intensità effetto'), type: 'select',
      options: [
        { value: 'low',    label: t('Bassa') },
        { value: 'medium', label: t('Media (default)') },
        { value: 'high',   label: t('Alta') },
      ],
      condition: { field: 'preset', op: 'in', value: ['liquid-glass','neon-cyber','brutalist-block','magnetic-liquid','sticker','retro-terminal','3d-tilt'] } },
    { key: 'effect_speed', label: t('Velocità animazioni'), type: 'range',
      min: 0, max: 4000, step: 100,
      condition: { field: 'preset', op: 'in', value: ['neon-cyber','magnetic-liquid','retro-terminal','3d-tilt'] } },

    ...textEffectsFields([
      { value: 'title', label: t('Solo Titolo') },
      { value: 'content', label: t('Solo Contenuto') },
      { value: 'all', label: t('Tutti gli elementi testuali') },
    ]),

    { type: 'separator', label: t('Dimensioni media') },
    // Elenco canonico: la tile ne offriva 7 su 9 (mancavano 4:5 e 9:16). Qui il
    // rapporto finisce tale e quale nel CSS del media, nessuna tabella da allargare.
    { key: 'image_ratio', label: t('Proporzioni'), type: 'select',
      options: ratioOptions({ custom: true }),
      condition: { field: 'media_type', op: 'neq', value: 'none' } },
    // L'AND si esprime come ARRAY: è la forma che evaluateCondition() valuta davvero.
    { key: 'image_ratio_custom', label: t('Proporzioni personalizzate'), type: 'text',
      placeholder: t('es. 5/4'),
      condition: [
        { field: 'media_type', op: 'neq', value: 'none' },
        { field: 'image_ratio', op: 'eq', value: 'custom' },
      ] },
    { key: 'image_height', label: t('Altezza fissa'), type: 'range', min: 0, max: 600, step: 10,
      description: t('0 = automatica'),
      // Con un rapporto scelto l'altezza non viene nemmeno letta — il renderer sceglie
      // l'uno o l'altra (`if ratio !== auto … elseif height > 0`, class-panel-tile.php):
      // mostrarla lì inerte sarebbe solo bugiardo.
      // La lista comprende anche il vuoto in tutte le sue forme perché l'inspector valuta le condizioni
      // sui settings GREZZI, senza fondere i default: un pannello salvato senza la chiave
      // image_ratio viene reso come 'auto' (`$s['image_ratio'] ?? 'auto'`), cioè proprio
      // dove l'altezza comanda — e con la sola voce 'auto' il controllo sparirebbe
      // esattamente sulle pagine dove serve. Il caso «chiave assente» va dalla parte giusta.
      condition: [
        { field: 'media_type', op: 'neq', value: 'none' },
        { field: 'image_ratio', op: 'eq', value: ['auto', '', null, undefined] },
      ] },
    { key: 'image_fit', label: t('Adattamento'), type: 'select', options: ADATTAMENTI,
      condition: { field: 'media_type', op: 'neq', value: 'none' } },
    { key: 'object_position', label: t('Punto focale'), type: 'object-position',
      contextKeys: { src: 'image', fit: 'image_fit', ratio: 'image_ratio', ratioCustom: 'image_ratio_custom' },
      // Con «Deforma per riempire» la foto è stirata sul riquadro e l'object-position
      // non sposta più niente: il renderer lo scrive lo stesso, ma non cambia un pixel.
      // È lo stesso criterio dello standard condiviso (_imageFrame.js) e delle gemelle
      // del gruppo. `neq` e non un elenco `in`: un pannello salvato prima dello sprint
      // può non avere affatto la chiave image_fit, e così il controllo gli resta.
      condition: [
        { field: 'media_type', op: 'neq', value: 'none' },
        { field: 'image_fit', op: 'neq', value: 'fill' },
      ] },
    { key: 'image_zoom', label: t('Zoom al hover'), type: 'toggle',
      condition: { field: 'media_type', op: 'neq', value: 'none' } },
    { key: 'media_padding', label: t('Padding attorno al media'), type: 'spacing', max: 60,
      description: t('Aggiunge uno spazio bianco attorno al media'),
      condition: { field: 'media_type', op: 'neq', value: 'none' } },
    withHover({ key: 'border_radius', label: t('Raggio media'), type: 'border-radius',
      condition: { field: 'media_type', op: 'neq', value: 'none' } }),

    { type: 'separator', label: t('Tipografia') },
    { key: 'text_align', label: t('Allineamento testo'), type: 'select', options: [
      { value: 'left', label: t('Sinistra') },
      { value: 'center', label: t('Centro') },
      { value: 'right', label: t('Destra') },
      { value: 'justify', label: t('Giustificato') },
    ]},
    { type: 'typography', label: t('Titolo'),
      responsiveKeys: [],
      keys: {
        tag:    'title_element',
        size:   'title_size',
        weight: 'title_weight',
        color:  'title_color',
      },
      sizeMin: 0, sizeMax: 60, sizeStep: 1,
    },
    { type: 'typography', label: t('Meta'),
      responsiveKeys: [],
      keys: {
        size:  'meta_size',
        color: 'meta_color',
      },
      sizeMin: 0, sizeMax: 24, sizeStep: 1,
    },
    { type: 'typography', label: t('Contenuto'),
      responsiveKeys: [],
      keys: {
        size:  'content_size',
        color: 'content_color',
      },
      sizeMin: 0, sizeMax: 28, sizeStep: 1,
    },
    { type: 'typography', label: t('Link'),
      keys: {
        color: 'link_color',
      },
      condition: { field: 'link_url', op: 'notEmpty' },
    },

    { type: 'separator', label: t('Stile card') },
    { key: 'card_padding', label: t('Padding'), type: 'spacing', max: 60 },
    withHover({ key: 'card_radius', label: t('Raggio card'), type: 'border-radius'}),
    ...shadowField,
    ...wowEffectsFields(),
    ...borderFields(),
  ],
};
