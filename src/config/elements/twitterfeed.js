
import { borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, withHover } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile X (Twitter) Timeline — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → URL, tipo embed, chrome, limite tweet, lingua
 *   styleFields[] → preset, typography_preset, tema, dimensioni, allineamento, border-radius, bordo
 *   defaults      → identico (nessuna modifica)
 */
export default {
  type: 'twitterfeed',
  name: t('X Timeline'),
  icon: 'dashicons-twitter',
  category: 'marketing',
  defaults: {
    preset: 'custom',
    typography_preset: '',
    url: '',
    embed_type: 'timeline',
    theme: 'light',
    width: '',
    height: '600',
    chrome: 'noheader,nofooter,noborders,noscrollbar',
    tweet_limit: '5',
    language: 'it',
    alignment: 'center',
    border_radius: '8',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { key: 'url', label: t('URL profilo X / Tweet'), type: 'link', placeholder: t('https://x.com/username') },
    { key: 'embed_type', label: t('Tipo embed'), type: 'select', options: [
      { value: 'timeline', label: t('Timeline') },
      { value: 'tweet', label: t('Tweet singolo') },
    ]},
    { key: 'chrome', label: t('Chrome (noheader,nofooter,...)'), type: 'multi_pills', options: [
      { value: 'noheader', label: t('Senza header') },
      { value: 'nofooter', label: t('Senza footer') },
      { value: 'noborders', label: t('Senza bordi') },
      { value: 'noscrollbar', label: t('Senza scrollbar') },
      { value: 'transparent', label: t('Sfondo trasparente') },
    ]},
    { key: 'tweet_limit', label: t('Limite tweet'), type: 'range', min: 1, max: 20, step: 1 },
    { key: 'language', label: t('Lingua'), type: 'select', options: [
      { value: 'it', label: t('Italiano') },
      { value: 'en', label: t('English') },
      { value: 'de', label: t('Deutsch') },
      { value: 'fr', label: t('Francais') },
      { value: 'es', label: t('Espanol') },
    ]},
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

    { type: 'separator', label: t('Aspetto') },
    { key: 'theme', label: t('Tema'), type: 'select', options: [
      { value: 'light', label: t('Chiaro') },
      { value: 'dark', label: t('Scuro') },
    ]},
    { key: 'width', label: t('Larghezza (px, vuoto = auto)'), type: 'number', min: 0, max: 1200, step: 10 },
    { key: 'height', label: t('Altezza (px)'), type: 'range', min: 200, max: 1200, step: 50 },
    { key: 'alignment', label: t('Allineamento'), type: 'select', options: [
      { value: 'left', label: t('Sinistra') },
      { value: 'center', label: t('Centro') },
      { value: 'right', label: t('Destra') },
    ]},
    withHover({ key: 'border_radius', label: t('Arrotondamento (px)'), type: 'border-radius' }),
    ...borderFields(),
  ],
};
