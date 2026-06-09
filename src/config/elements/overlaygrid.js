import { textEffectsFields, textEffectsDefaults, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, wowEffectsFields, wowEffectsDefaults } from './_shared';
import { shadowField } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile Overlay Grid — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → items (content-items), colonne (desktop/mobile),
 *                   posizione overlay, altezza, dimensione titolo,
 *                   visibilità CTA + testo CTA, posizione ribbon, hover_effect/hover_overlay (comportamento)
 *   styleFields[] → preset, sfondo, tipografia, tweak effetto preset, gap, allineamento testo,
 *                   stile overlay (primary/default), padding overlay, raggio elementi,
 *                   colore overlay/gradiente, colori/peso/spacing titolo+sottotitolo, stile CTA,
 *                   colori ribbon, text effects, ombra, bordi
 *   AVANZATE      → meta tecnico (id/class/condizioni)
 */
export default {
  type: 'overlaygrid',
  name: t('Overlay Grid'),
  icon: 'dashicons-grid-view',
  category: 'interactive',
  defaults: {
    typography_preset: '',
    bg: { type: 'none' },
    items: [
      { id: 'og-1', image: 'https://images.unsplash.com/photo-1774192621035-20d11389f781?w=1200&q=75&auto=format&fit=crop', title: t('Suite & Camere'), subtitle: 'Eleganza in ogni dettaglio', link: '' },
      { id: 'og-2', image: 'https://images.unsplash.com/photo-1760463502141-2b5166df169e?w=1200&q=75&auto=format&fit=crop', title: t('Bar & Lounge'), subtitle: 'Mixology d\'autore', link: '' },
      { id: 'og-3', image: 'https://images.unsplash.com/photo-1731941465921-eb4285693713?w=1200&q=75&auto=format&fit=crop', title: t('Restaurant'), subtitle: 'Cucina contemporanea', link: '' },
    ],
    columns: '3',
    columns_mobile: '1',
    gap: 'medium',
    height: '320',
    match_height: true,
    layout_mode: 'uniform',
    overlay_position: 'bottom',
    overlay_horizontal: 'left',
    overlay_style: 'overlay-primary',
    overlay_padding: 'medium',
    title_size: 'h3',
    hover_effect: 'zoom',
    hover_overlay: 'always',
    ribbon_position: 'top-right',
    ribbon_bg: '',
    ribbon_color: '',
    shadow: 'sm',

    // ── Preset & granular controls (V3.26.0) ──
    preset: 'editorial-grid',
    item_radius: 12,
    overlay_color: 'rgba(0,0,0,0.45)',
    overlay_gradient: true,
    title_color: '#ffffff',
    title_weight: '700',
    title_letter_spacing: 0,
    title_uppercase: false,
    subtitle_color: 'rgba(255,255,255,0.85)',
    subtitle_size: 14,
    show_cta: false,
    cta_text: 'Scopri',
    cta_style: 'arrow',

    effect_color: '',
    effect_intensity: 'medium',
    effect_speed: 0,

    ...textEffectsDefaults,
    text_effect_target: 'title',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
    ...wowEffectsDefaults,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { type: 'separator', label: t('Elementi') },
    { key: 'items', label: t('Elementi'), type: 'content-items', supportsDynamic: true,
      itemFields: [
        { key: 'image', label: t('Immagine'), type: 'image' },
        { key: 'hover_image', label: t('Immagine hover'), type: 'image' },
        { key: 'hover_video', label: t('Video hover'), type: 'media' },
        { key: 'title', label: t('Titolo'), type: 'text' },
        { key: 'item_title_color', label: t('Colore titolo (override)'), type: 'color',
          description: t('Vuoto = usa il colore globale impostato sotto.') },
        { key: 'subtitle', label: t('Sottotitolo'), type: 'text' },
        { key: 'item_subtitle_color', label: t('Colore sottotitolo (override)'), type: 'color',
          description: t('Vuoto = usa il colore globale impostato sotto.') },
        { key: 'link', label: t('Link'), type: 'link', placeholder: t('https://...') },
        { key: 'ribbon', label: t('Ribbon'), type: 'text' },
        { key: 'tall', label: t('Masonry: cella alta (×2 righe)'), type: 'toggle' },
        { key: 'wide', label: t('Masonry: cella larga (×2 colonne)'), type: 'toggle' },
      ],
      newItemDefaults: { image: '', hover_image: '', hover_video: '', title: t('Nuovo elemento'), subtitle: '', link: '', ribbon: '', item_title_color: '', item_subtitle_color: '', tall: false, wide: false },
      itemLabel: 'Elemento',
    },

    { type: 'separator', label: t('Griglia') },
    { key: 'columns', label: t('Colonne (desktop)'), type: 'select', options: [
      { value: '1', label: '1' },
      { value: '2', label: '2' },
      { value: '3', label: '3' },
      { value: '4', label: '4' },
      { value: '5', label: '5' },
    ]},
    { key: 'columns_mobile', label: t('Colonne (mobile)'), type: 'select', options: [
      { value: '1', label: '1' },
      { value: '2', label: '2' },
    ]},

    { type: 'separator', label: t('Overlay') },
    { key: 'overlay_position', label: t('Posizione'), type: 'select', options: [
      { value: 'bottom', label: t('In basso') },
      { value: 'top', label: t('In alto') },
      { value: 'center', label: t('Centro') },
      { value: 'cover', label: t('Copertura') },
      { value: 'bottom-left', label: t('Basso-sinistra') },
      { value: 'bottom-center', label: t('Basso-centro') },
      { value: 'bottom-right', label: t('Basso-destra') },
      { value: 'top-left', label: t('Alto-sinistra') },
      { value: 'top-center', label: t('Alto-centro') },
      { value: 'top-right', label: t('Alto-destra') },
      { value: 'center-left', label: t('Centro-sinistra') },
      { value: 'center-right', label: t('Centro-destra') },
    ]},
    { key: 'title_size', label: t('Dimensione titolo'), type: 'select', options: [
      { value: 'h1', label: t('H1') },
      { value: 'h2', label: t('H2') },
      { value: 'h3', label: t('H3') },
      { value: 'h4', label: t('H4') },
    ]},

    { type: 'separator', label: t('Effetti hover (comportamento)') },
    { key: 'hover_effect', label: t('Effetto immagine'), type: 'select', options: [
      { value: 'none', label: t('Nessuno') },
      { value: 'zoom', label: t('Zoom') },
      { value: 'zoom-rotate', label: t('Zoom + rotazione') },
      { value: 'brightness', label: t('Luminosità') },
      { value: 'desaturate', label: t('Desatura → colore') },
      { value: 'blur-in', label: t('Sfocatura → nitido') },
    ]},
    { key: 'hover_overlay', label: t('Overlay'), type: 'select', options: [
      { value: 'always', label: t('Sempre visibile') },
      { value: 'fade', label: t('Fade in') },
      { value: 'slide-bottom', label: t('Scorre dal basso') },
      { value: 'slide-top', label: "Scorre dall'alto" },
      { value: 'slide-left', label: t('Scorre da sinistra') },
      { value: 'slide-right', label: t('Scorre da destra') },
    ]},

    { type: 'separator', label: t('Call to action') },
    { key: 'show_cta', label: t('Mostra CTA su ogni item'), type: 'toggle' },
    { key: 'cta_text', label: t('Testo CTA'), type: 'text',
      condition: { field: 'show_cta', op: 'eq', value: true } },

    { type: 'separator', label: t('Ribbon') },
    { key: 'ribbon_position', label: t('Posizione ribbon'), type: 'select', options: [
      { value: 'top-left', label: t('Alto sinistra') },
      { value: 'top-right', label: t('Alto destra') },
    ]},
  ],

  // ─── STILE ─────────────────────────────────────────────────
  styleFields: [
    // ────────── Preset stile ──────────
    { type: 'separator', label: t('Preset stile') },
    { key: 'preset', label: t('Stile'), type: 'select', options: [
      { value: 'editorial-grid',   label: t('Editorial Grid (default)') },
      { value: 'minimal-square',   label: t('Minimal Square (1:1)') },
      { value: 'magazine-mosaic',  label: t('Magazine Mosaic') },
      { value: 'card-modern',      label: t('Card Modern (caption sotto)') },
      { value: 'duotone-portfolio',label: t('Duotone Portfolio (b/n→colore)') },
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

    // ────────── Tweak effetti (solo audaci) ──────────
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
    { key: 'effect_speed', label: t('Velocità animazioni (ms)'), type: 'range',
      min: 0, max: 4000, step: 100,
      condition: { field: 'preset', op: 'in', value: ['neon-cyber','magnetic-liquid','retro-terminal','3d-tilt'] } },

    ...textEffectsFields([
      { value: 'title', label: t('Solo Titolo') },
      { value: 'subtitle', label: t('Solo Sottotitolo') },
      { value: 'all', label: t('Tutti gli elementi testuali') },
    ]),

    { type: 'separator', label: t('Griglia — aspetto') },
    { key: 'gap', label: t('Spaziatura'), type: 'select', options: [
      { value: 'collapse', label: t('Nessuna') },
      { value: 'small', label: t('Piccola') },
      { value: 'medium', label: t('Media') },
      { value: 'large', label: t('Grande') },
    ]},
    { key: 'height', label: t('Altezza (px)'), type: 'range', min: 150, max: 800, step: 25 },
    { key: 'match_height', label: t('Altezza uniforme'), type: 'toggle',
      condition: { field: 'layout_mode', op: 'eq', value: 'uniform' } },
    { key: 'layout_mode', label: t('Disposizione'), type: 'select', options: [
      { value: 'uniform', label: t('Uniforme (griglia)') },
      { value: 'masonry', label: t('Masonry (celle alte/larghe)') },
    ], description: t('In Masonry le celle con “alta”/“larga” attive occupano 2 righe/colonne; l\'altezza diventa l\'altezza-riga di base.') },

    { type: 'separator', label: t('Overlay — aspetto') },
    { key: 'overlay_horizontal', label: t('Allineamento testo'), type: 'select', options: [
      { value: 'left', label: t('Sinistra') },
      { value: 'center', label: t('Centro') },
      { value: 'right', label: t('Destra') },
    ]},
    { key: 'overlay_style', label: t('Stile overlay'), type: 'select', options: [
      { value: 'overlay-primary', label: t('Primary') },
      { value: 'overlay-default', label: t('Predefinito') },
    ]},
    { key: 'overlay_padding', label: t('Padding overlay'), type: 'select', options: [
      { value: 'small', label: t('Piccolo') },
      { value: 'medium', label: t('Medio') },
      { value: 'large', label: t('Grande') },
    ]},

    { type: 'separator', label: t('Stile elementi') },
    { key: 'item_radius', label: t('Arrotondamento elementi (px)'), type: 'range', min: 0, max: 32, step: 1 },
    { key: 'overlay_color', label: t('Colore overlay'), type: 'color' },
    { key: 'overlay_gradient', label: t('Overlay gradiente (alto→basso)'), type: 'toggle' },

    { type: 'separator', label: t('Tipografia') },
    { type: 'typography', label: t('Titolo'),
      presetKey: 'typography_preset',
      responsiveKeys: ['letterSpacing'],
      letterSpacingUnit: 'em',
      keys: {
        weight:        'title_weight',
        letterSpacing: 'title_letter_spacing',
        color:         'title_color',
      },
    },
    { type: 'typography', label: t('Sottotitolo'),
      presetKey: 'typography_preset',
      responsiveKeys: ['size'],
      keys: {
        size:  'subtitle_size',
        color: 'subtitle_color',
      },
      sizeMin: 11, sizeMax: 20, sizeStep: 1,
    },
    { key: 'title_uppercase', label: t('Titolo maiuscolo'), type: 'toggle' },

    { type: 'separator', label: t('Call to action — stile') },
    { key: 'cta_style', label: t('Stile CTA'), type: 'select',
      condition: { field: 'show_cta', op: 'eq', value: true },
      options: [
        { value: 'underline', label: t('Underline') },
        { value: 'arrow',     label: t('Arrow link') },
        { value: 'pill',      label: t('Pill rounded') },
        { value: 'text',      label: t('Solo testo') },
      ]},

    { type: 'separator', label: t('Ribbon — colori') },
    { key: 'ribbon_bg', label: t('Sfondo ribbon'), type: 'color' },
    { key: 'ribbon_color', label: t('Testo ribbon'), type: 'color' },

    ...shadowField,
    ...wowEffectsFields(),
    ...borderFields(),
  ],
};
