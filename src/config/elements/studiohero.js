import { t } from '@/i18n';
import { shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';

/**
 * Hero — Studio (Editorial + OLOmap) : hero "studio editoriale" su fondo inchiostro.
 * Riga meta mono (EST. / coordinate / divisione), eyebrow lime, H1 display industriale
 * gigante su 2 righe — riga 1 con entrata lettere una-a-una, riga 2 in outline che si
 * RIEMPIE di accento allo scroll (--fill) — sub con <b>, 2 CTA (fill + ghost) e a destra
 * l'OLOmap: infografica SVG animata della mappa del sistema (camera dive su 4 livelli,
 * breadcrumb readout sulla didascalia, indicatore profondità L1/L4). Parallax interno
 * leggero su meta e media. Render Vue == PHP (StudioHeroTile.vue). Estratta dal
 * blueprint "Clod — Evoluzione (supertemplate) v2".
 */
export default {
  type: 'studiohero',
  name: t('Hero — Studio (Editorial + OLOmap)'),
  icon: 'dashicons-networking',
  category: 'marketing',

  // Unificazione media hero: il vecchio campo immagine (media_image + focal
  // media_object_position) confluisce nel pannello unico media_bg (immagine/video/
  // gradiente/colore…). Migrazione non distruttiva al load (src/utils/bgMigrate.js);
  // i renderer (Vue+PHP) tengono media_image come fallback quando media_bg è vuoto.
  bgMigrate: { imageKey: 'media_image', imagePosKey: 'media_object_position', target: 'media_bg' },

  defaults: {
    media_bg: { type: 'none' },
    eyebrow: 'R&S · divisione idee',
    eyebrow_color: '',
    title_line1: 'Visual',
    title_line2: 'studio',
    title_color: '',
    title_size_min: 74,
    title_size_max: 210,
    line2_stroke_width: 1.4,
    line2_stroke_color: '',
    line2_scroll_fill: true,
    line2_fill_color: '',
    letters_entrance: true,
    subtitle: 'Aiuto le aziende a <b>farsi vedere</b>: strategia, produzione media e identità visiva, con contenuti originali che lavorano davvero.',
    subtitle_color: '',
    show_meta: true,
    meta_items: [
      { strong: 'EST.', text: 'Trento — Italia' },
      { strong: '46.07°N', text: '11.12°E' },
      { strong: 'R&S', text: 'divisione idee' },
      { strong: '2026', text: '— project media manager' },
    ],
    cta1_text: 'Progettiamo assieme',
    cta1_url: '#contatto',
    cta1_show_arrow: true,
    cta2_text: 'Selezione progetti',
    cta2_url: '#lavori',
    accent_color: '',
    media_mode: 'olomap',
    media_image: '',
    media_object_position: 'center center',
    media_label: 'Visual studio — still',
    cap_text: 'OLObuild · sistema',
    map_label: 'Mappa del sistema',
    map_root: 'OLObuild',
    map_l1: 'Forge,Prisma*,Saffron,Soundwave,+46\\ntemi',
    map_l2: 'Hero*,Galleria,Griglia,CTA',
    map_l3: 'Spazi,Bordi,Ombra,Colore*',
    map_tokens: [
      { label: 'Primario', color: '#e1474f' },
      { label: 'Accento', color: '#f4a23b' },
      { label: 'Lime', color: '#C6F24E' },
      { label: 'Scuro', color: '#16263d' },
    ],
    map_duration: 21,
    parallax_internal: true,
    bg_color: '',

    // Spaziatura (gated): il padding di base è clamp(40px,7vw,84px) 0 clamp(44px,6vw,72px).
    // Override attivo SOLO se pad_custom=true → no-op coi default.
    pad_custom: false,
    content_padding: { top: 84, right: 0, bottom: 72, left: 0 },

    // KIT standard OLObuild — additivi, no-op coi default (sfondo none, ombra none, bordo 0)
    bg: { type: 'none' },
    shadow: 'none',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  fields: [
    { key: 'eyebrow', label: t('Eyebrow'), type: 'text' },
    { key: 'title_line1', label: t('Titolo — riga 1'), type: 'text' },
    { key: 'title_line2', label: t('Titolo — riga 2 (outline)'), type: 'text' },
    { key: 'letters_entrance', label: t('Entrata lettere riga 1'), type: 'toggle',
      description: t('Le lettere entrano una a una (rispetta il reduced-motion).') },
    { key: 'line2_scroll_fill', label: t('Riempimento riga 2 allo scroll'), type: 'toggle' },
    { key: 'subtitle', label: t('Sottotitolo'), type: 'textarea',
      description: t('HTML inline consentito: usa <b> per le parole forti.') },

    { type: 'separator', label: t('Riga meta') },
    { key: 'show_meta', label: t('Mostra riga meta'), type: 'toggle' },
    { key: 'meta_items', label: t('Voci meta'), type: 'content-items',
      itemLabel: t('Voce'),
      newItemDefaults: { strong: 'EST.', text: 'Trento — Italia' },
      itemFields: [
        { key: 'strong', label: t('Valore (in evidenza)'), type: 'text' },
        { key: 'text', label: t('Testo'), type: 'text' },
      ],
      condition: { field: 'show_meta', op: 'eq', value: true } },

    { type: 'separator', label: t('CTA') },
    { key: 'cta1_text', label: t('CTA 1 — testo'), type: 'text' },
    { key: 'cta1_url', label: t('CTA 1 — link'), type: 'link' },
    { key: 'cta1_show_arrow', label: t('CTA 1 — freccia'), type: 'toggle' },
    { key: 'cta2_text', label: t('CTA 2 — testo'), type: 'text' },
    { key: 'cta2_url', label: t('CTA 2 — link'), type: 'link' },

    { type: 'separator', label: t('Media') },
    { key: 'media_mode', label: t('Tipo media'), type: 'select', options: [
      { value: 'none', label: t('Nessuno') },
      { value: 'image', label: t('Immagine') },
      { value: 'olomap', label: t('OLOmap') },
    ]},
    { key: 'media_bg', type: 'background', showParallax: false,
      label: t('Immagine / media hero (immagine, video, gradiente…)'),
      condition: { field: 'media_mode', op: 'eq', value: 'image' } },
    { key: 'media_label', label: t('Etichetta placeholder'), type: 'text',
      condition: { field: 'media_mode', op: 'eq', value: 'image' } },
    { key: 'cap_text', label: t('Didascalia (mono, in basso a sinistra)'), type: 'text',
      condition: { field: 'media_mode', op: 'neq', value: 'none' } },

    { type: 'separator', label: t('OLOmap') },
    { key: 'map_label', label: t('Etichetta mappa'), type: 'text',
      condition: { field: 'media_mode', op: 'eq', value: 'olomap' } },
    { key: 'map_root', label: t('Nodo radice'), type: 'text',
      condition: { field: 'media_mode', op: 'eq', value: 'olomap' } },
    { key: 'map_l1', label: t('Livello 1 (CSV)'), type: 'text',
      description: t('Voci separate da virgola. * = nodo focus del dive, \\n = a capo nel nodo.'),
      condition: { field: 'media_mode', op: 'eq', value: 'olomap' } },
    { key: 'map_l2', label: t('Livello 2 (CSV)'), type: 'text',
      condition: { field: 'media_mode', op: 'eq', value: 'olomap' } },
    { key: 'map_l3', label: t('Livello 3 (CSV)'), type: 'text',
      condition: { field: 'media_mode', op: 'eq', value: 'olomap' } },
    { key: 'map_tokens', label: t('Livello 4 — token colore'), type: 'content-items',
      itemLabel: t('Token'),
      newItemDefaults: { label: 'Primario', color: '#e1474f' },
      itemFields: [
        { key: 'label', label: t('Etichetta'), type: 'text' },
        { key: 'color', label: t('Colore'), type: 'color' },
      ],
      condition: { field: 'media_mode', op: 'eq', value: 'olomap' } },
    { key: 'map_duration', label: t('Durata ciclo camera (s)'), type: 'number', min: 4, max: 120,
      condition: { field: 'media_mode', op: 'eq', value: 'olomap' } },

    { type: 'separator', label: t('Movimento') },
    { key: 'parallax_internal', label: t('Parallax interno (meta + media)'), type: 'toggle',
      description: t('Leggero scorrimento differenziato allo scroll. Rispetta il reduced-motion.') },
  ],

  styleFields: [
    { type: 'separator', label: t('Titolo') },
    { key: 'title_size_min', label: t('Dimensione min (px)'), type: 'number', min: 20, max: 160 },
    { key: 'title_size_max', label: t('Dimensione max (px)'), type: 'number', min: 60, max: 320 },
    { key: 'line2_stroke_width', label: t('Spessore contorno riga 2 (px)'), type: 'range', min: 0.5, max: 4, step: 0.1 },

    { type: 'separator', label: t('Colori') },
    { key: 'accent_color', label: t('Accento (mappa + CTA + riempimento)'), type: 'color',
      description: t('Vuoto = primario del tema.') },
    { key: 'eyebrow_color', label: t('Colore eyebrow'), type: 'color',
      description: t('Vuoto = accento.') },
    { key: 'title_color', label: t('Colore titolo'), type: 'color' },
    { key: 'line2_stroke_color', label: t('Contorno riga 2'), type: 'color' },
    { key: 'line2_fill_color', label: t('Riempimento riga 2'), type: 'color',
      description: t('Vuoto = accento.') },
    { key: 'subtitle_color', label: t('Colore sottotitolo'), type: 'color' },
    { key: 'bg_color', label: t('Sfondo'), type: 'color',
      description: t('Vuoto = background del tema.') },

    { type: 'separator', label: t('Spaziatura') },
    { key: 'pad_custom', label: t('Padding personalizzato'), type: 'toggle',
      description: t('Off = padding verticale responsivo predefinito. On = usa i valori sotto.') },
    { key: 'content_padding', label: t('Padding (px)'), type: 'spacing', max: 240,
      condition: { field: 'pad_custom', op: 'eq', value: true } },

    { type: 'separator', label: t('Sfondo') },
    { key: 'bg', label: t('Sfondo completo'), type: 'background', showParallax: false },

    { type: 'separator', label: t('Ombra') },
    ...shadowField,

    ...borderFields(),
  ],
};
