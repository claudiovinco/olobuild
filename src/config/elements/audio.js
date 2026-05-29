import { textEffectsFields, textEffectsDefaults, shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile Audio — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → sorgente (file/url), file/URL, info traccia (titolo/artista/cover), opzioni playback
 *   styleFields[] → typo preset, player style, accent/bg/text color, radius, text-effects, ombra, bordo
 *   AVANZATE      → meta tecnico
 */
export default {
  type: 'audio',
  name: t('Audio'),
  icon: 'dashicons-format-audio',
  category: 'media',
  defaults: {
    typography_preset: '',
    source_type: 'file',
    file_url: '',
    audio_url: '',
    autoplay: false,
    loop: false,
    muted: false,
    show_controls: true,
    player_style: 'default',
    accent_color: '',
    bg_color: '',
    text_color: '',
    border_radius: { tl: 8, tr: 8, br: 8, bl: 8 },
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
    title: '',
    artist: '',
    cover_image: '',
    shadow: 'none',
    ...textEffectsDefaults,
    text_effect_target: 'title',
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { key: 'source_type', label: t('Sorgente'), type: 'select', options: [
      { value: 'file', label: t('File (Media Library)') },
      { value: 'url', label: t('URL esterno') },
    ]},
    { key: 'file_url', label: t('File audio'), type: 'media', accept: 'audio',
      condition: { field: 'source_type', value: 'file' } },
    { key: 'audio_url', label: t('URL audio'), type: 'text',
      condition: { field: 'source_type', value: 'url' } },

    { type: 'separator', label: t('Informazioni traccia') },
    { key: 'title', label: t('Titolo'), type: 'text' },
    { key: 'artist', label: t('Artista'), type: 'text' },
    { key: 'cover_image', label: t('Immagine copertina'), type: 'image' },

    { type: 'separator', label: t('Riproduzione') },
    { key: 'autoplay', label: t('Riproduzione automatica'), type: 'toggle' },
    { key: 'loop', label: t('Ripeti'), type: 'toggle' },
    { key: 'muted', label: t('Silenziato'), type: 'toggle' },
    { key: 'show_controls', label: t('Mostra controlli'), type: 'toggle' },
  ],

  // ─── STILE ─────────────────────────────────────────────────
  styleFields: [
    { type: 'separator', label: t('Tipografia') },
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },

    ...textEffectsFields([ { value: 'title', label: t('Solo Titolo') } ]),

    { type: 'separator', label: t('Player') },
    { key: 'player_style', label: t('Stile player'), type: 'select', options: [
      { value: 'default', label: t('Predefinito') },
      { value: 'minimal', label: t('Minimale') },
      { value: 'custom', label: t('Personalizzato') },
    ]},
    { key: 'accent_color', label: t('Colore accent'), type: 'color' },
    { key: 'bg_color', label: t('Sfondo'), type: 'color' },
    { key: 'text_color', label: t('Colore testo'), type: 'color' },
    { key: 'border_radius', label: t('Border Radius'), type: 'border-radius' },

    ...shadowField,
    ...borderFields(),
  ],
};
