import { t } from '@/i18n';
import { shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, focalField, focalDefault } from './_shared.js';

/**
 * Intro Split — feature split editoriale: testo (eyebrow + titolo gigante con parola-accento
 * + lead + N statistiche inline + CTA) accanto a un media (slot/strisce) con badge d'angolo
 * (numero + label). Estratta dal blueprint OLOthemes (.vd-intro / SplitText + Counter).
 * Render Vue == PHP (IntroSplitTile.vue). Nessun JS.
 */
export default {
  type: 'introsplit',
  name: t('Intro Split (testo + stats + media badge)'),
  icon: 'dashicons-align-pull-left',
  category: 'marketing',
  // Ritirata dalla palette (unificazione hero, Fase 2): assorbita da `hero-split`
  // (pannello "media + badge d'angolo"; stats già nel lato messaggio). I template salvati continuano a renderizzare.
  hidden: true,

  // Unificazione sfondo: media_image legacy → pannello unico media_bg (non distruttivo).
  bgMigrate: { imageKey: 'media_image', imagePosKey: 'media_image_object_position' },

  defaults: {
    eyebrow: 'One unit · since 1974',
    eyebrow_color: '',
    headline: 'A regional club with a',
    accent: 'rich history',
    headline_tail: '',
    uppercase: true,
    headline_color: '',
    accent_color: '',
    lead: "From a handful of friends on a muddy field to eight competitive teams across men's, women's and youth football — Verdano FC is built on the people who keep showing up.",
    lead_color: '',
    stats: [
      { number: '50', label: 'Years of football' },
      { number: '8', label: 'Competitive teams' },
      { number: '600+', label: 'Active members' },
    ],
    stat_number_color: '',
    stat_label_color: '',
    cta_text: 'About the club',
    cta_url: '#',
    cta2_text: '',
    cta2_url: '#',
    cta2_style: 'outline',
    cta_bg: '',
    cta_color: 'var(--olo-color-light, #f8f9fa)',
    media_image: '',
    ...focalDefault('media_image'),
    media_bg: { type: 'none' },
    media_label: 'club portrait — squad on the pitch',
    media_light: true,
    media_aspect: '4/4.4',
    media_radius: 20,
    media_radius_top: 0,
    media_blob: false,
    media_blob_color: '',
    // Spaziatura/forma additive — default no-op (padding gated OFF, raggi = valori attuali).
    pad_custom: false,
    content_padding: { top: 0, right: 0, bottom: 0, left: 0 },
    badge_radius: { tl: 16, tr: 16, br: 16, bl: 16 },
    cta_radius: { tl: 999, tr: 999, br: 999, bl: 999 },
    badge_number: '1974',
    badge_label: 'Established',
    badge_bg: '',
    badge_color: '',
    media_position: 'right',
    // Flush 50/50 + firma + CTA underline + tipografia titolo (additivi, default no-op).
    flush: false,
    content_bg: '',
    signature: '',
    cta_style: 'button',
    accent_italic: false,
    headline_weight: '900',
    headline_size: '',

    // KIT standard OLObuild — sfondo completo + ombra + bordo sul contenitore principale.
    // Default no-op: bg none, shadow none, border 0 → render invariato.
    bg: { type: 'none' },
    shadow: 'none',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  fields: [
    { key: 'eyebrow', label: t('Occhiello'), type: 'text' },
    { key: 'headline', label: t('Titolo'), type: 'text' },
    { key: 'accent', label: t('Parola accento'), type: 'text' },
    { key: 'headline_tail', label: t('Coda titolo (dopo accento)'), type: 'text' },
    { key: 'accent_italic', label: t('Parola accento corsiva'), type: 'toggle' },
    { key: 'uppercase', label: t('Maiuscolo'), type: 'toggle' },
    { key: 'lead', label: t('Testo'), type: 'textarea' },
    { key: 'signature', label: t('Firma (es. — Camille Aubry, Founder)'), type: 'text' },

    { type: 'separator', label: t('Statistiche') },
    { key: 'stats', label: t('Voci'), type: 'content-items',
      itemLabel: t('Stat'),
      defaults: { number: '0', label: 'Etichetta' },
      itemFields: [
        { key: 'number', label: t('Numero (es. 600+)'), type: 'text' },
        { key: 'label', label: t('Etichetta'), type: 'text' },
      ],
    },

    { type: 'separator', label: t('CTA') },
    { key: 'cta_text', label: t('Testo CTA'), type: 'text' },
    { key: 'cta_url', label: t('Link CTA'), type: 'link' },
    { key: 'cta_style', label: t('Stile CTA'), type: 'select', options: [
      { value: 'button', label: t('Bottone') },
      { value: 'outline', label: t('Bottone outline') },
      { value: 'underline', label: t('Link sottolineato') },
    ]},
    { key: 'cta2_text', label: t('CTA 2 — Testo (vuoto = nessuno)'), type: 'text' },
    { key: 'cta2_url', label: t('CTA 2 — Link'), type: 'link' },
    { key: 'cta2_style', label: t('CTA 2 — Stile'), type: 'select', options: [
      { value: 'outline', label: t('Bottone outline') },
      { value: 'button', label: t('Bottone') },
      { value: 'underline', label: t('Link sottolineato') },
    ]},

    { type: 'separator', label: t('Media + badge') },
    { key: 'media_bg', label: t('Sfondo / media (immagine, video, gradiente, colore…)'), type: 'background', showParallax: false },
    { key: 'media_label', label: t('Etichetta placeholder'), type: 'text' },
    { key: 'media_light', label: t('Placeholder chiaro'), type: 'toggle' },
    { key: 'badge_number', label: t('Badge — numero'), type: 'text' },
    { key: 'badge_label', label: t('Badge — etichetta'), type: 'text' },
    { key: 'media_position', label: t('Posizione media'), type: 'select', options: [
      { value: 'right', label: t('Destra') },
      { value: 'left', label: t('Sinistra') },
    ]},
    { key: 'flush', label: t('Layout flush 50/50 (gap 0, media a tutta altezza)'), type: 'toggle' },
  ],

  styleFields: [
    { type: 'separator', label: t('Colori — testo') },
    { key: 'eyebrow_color', label: t('Occhiello (vuoto = secondario)'), type: 'color' },
    { key: 'headline_color', label: t('Titolo'), type: 'color' },
    { key: 'accent_color', label: t('Parola accento (vuoto = secondario)'), type: 'color' },
    { key: 'lead_color', label: t('Testo'), type: 'color' },

    { type: 'separator', label: t('Colori — statistiche') },
    { key: 'stat_number_color', label: t('Numeri stat'), type: 'color' },
    { key: 'stat_label_color', label: t('Etichette stat'), type: 'color' },

    { type: 'separator', label: t('Colori — CTA') },
    { key: 'cta_bg', label: t('CTA sfondo (vuoto = testo del tema)'), type: 'color' },
    { key: 'cta_color', label: t('CTA testo'), type: 'color' },

    { type: 'separator', label: t('Colori — pannello & badge') },
    { key: 'content_bg', label: t('Sfondo pannello testo'), type: 'color' },
    { key: 'badge_bg', label: t('Badge sfondo (vuoto = primario)'), type: 'color' },
    { key: 'badge_color', label: t('Badge testo (vuoto = contrasto primario)'), type: 'color' },

    { type: 'separator', label: t('Tipografia titolo') },
    { key: 'headline_weight', label: t('Peso titolo'), type: 'select', options: [
      { value: '400', label: '400' }, { value: '500', label: '500' }, { value: '600', label: '600' }, { value: '700', label: '700' }, { value: '900', label: '900' },
    ]},
    { key: 'headline_size', label: t('Dim. max titolo (px, 0 = auto)'), type: 'range', min: 0, max: 120, step: 2 },

    { type: 'separator', label: t('Media') },
    { key: 'media_aspect', label: t('Proporzioni media'), type: 'select', options: [
      { value: '4/4.4', label: '4:4.4' },
      { value: '1/1', label: '1:1' },
      { value: '4/5', label: '4:5' },
      { value: '3/4', label: '3:4' },
    ]},
    { key: 'media_radius', label: t('Raggio media (px)'), type: 'border-radius' },
    { key: 'media_radius_top', label: t('Raggio angoli superiori (arco, px — 0 = uniforme)'), type: 'range', min: 0, max: 300, step: 4 },
    { key: 'media_blob', label: t('Blob decorativo dietro il media'), type: 'toggle' },
    { key: 'media_blob_color', label: t('Blob — colore (vuoto = primario)'), type: 'color', condition: { field: 'media_blob', value: true } },

    { type: 'separator', label: t('Spaziatura') },
    { key: 'pad_custom', label: t('Padding personalizzato'), type: 'toggle' },
    { key: 'content_padding', label: t('Padding contenitore (px)'), type: 'spacing',
      condition: { field: 'pad_custom', op: 'eq', value: true } },

    { type: 'separator', label: t('Forma') },
    { key: 'badge_radius', label: t('Raggio badge'), type: 'border-radius' },
    { key: 'cta_radius', label: t('Raggio CTA'), type: 'border-radius' },

    { type: 'separator', label: t('Sfondo') },
    { key: 'bg', label: t('Sfondo completo'), type: 'background', showParallax: false },
    { type: 'separator', label: t('Ombra') },
    ...shadowField,
    ...borderFields(),
  ],
};
