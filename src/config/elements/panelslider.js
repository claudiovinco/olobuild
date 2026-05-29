import { textEffectsFields, textEffectsDefaults, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, withHover, widgetTemplateField, wowEffectsFields, wowEffectsDefaults } from './_shared';
import { shadowField } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile Panel Slider — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → panels (content-items), colonne (responsive), posizione immagine (top/bottom/bg/side),
 *                   proporzione/altezza/fit immagine, hover behaviors (lift/scale/image_zoom),
 *                   visibilità frecce/dots, autoplay + intervallo,
 *                   visibilità CTA + testo CTA
 *   styleFields[] → preset, sfondo, tipografia, tweak effetto preset, gap, altezza uniforme,
 *                   stile card (bg/bordo/raggio/padding), hover shadow, raggio immagine,
 *                   caption overlay color/gradient, tipografia titolo+contenuto, stile CTA,
 *                   stile/dimensione/colore frecce, ombra, bordi
 *   AVANZATE      → meta tecnico (id/class/condizioni)
 */
export default {
  type: 'panelslider',
  name: t('Panel Slider'),
  icon: 'dashicons-slides',
  category: 'interactive',
  defaults: {
    bg: { type: 'none' },
    typography_preset: '',
    panels: [
      { id: 'ps-1', title: t('Suite con piscina'), content: 'Spazi privati immersi nella natura, con vista panoramica e piscina riservata.', image: 'https://images.unsplash.com/photo-1667987566780-3b31fa5485c8?w=1200&q=75&auto=format&fit=crop', link: '' },
      { id: 'ps-2', title: t('Lounge & Cabana'), content: 'Aree relax all\'aperto curate nel dettaglio, perfette per giornate in completo comfort.', image: 'https://images.unsplash.com/photo-1774552803490-1c95b9020453?w=1200&q=75&auto=format&fit=crop', link: '' },
      { id: 'ps-3', title: t('Esperienze in mare'), content: 'Tour esclusivi e charter privati nelle acque cristalline della costa.', image: 'https://images.unsplash.com/photo-1762353853326-20cc0b74e952?w=1200&q=75&auto=format&fit=crop', link: '' },
    ],
    columns: '3',
    gap: 'medium',
    card_style: 'default',
    card_radius: '12',
    card_padding: { top: 24, right: 24, bottom: 24, left: 24 },
    equal_height: true,
    image_ratio: '4/3',
    image_height: '',
    image_fit: 'cover',
    image_zoom: true,
    show_arrows: true,
    arrow_style: 'circle',
    arrow_size: '40',
    arrow_color: '',
    arrow_bg: '',
    show_dots: false,
    autoplay: false,
    autoplay_interval: '5000',
    title_size: '',
    title_color: '',
    content_size: '',
    content_color: '',
    shadow: 'sm',

    // ── Preset & granular controls (V3.25.0) ──
    preset: 'card-modern',

    // Card layout
    card_bg: '#ffffff',
    card_border_color: 'transparent',
    card_border_width: 0,
    card_border_style: 'solid',
    card_image_radius: 0,           // 0 = inherit card radius for top corners
    card_image_position: 'top',     // top | bottom | bg | side-left | side-right

    // Hover behavior
    hover_lift: false,
    hover_scale: false,
    hover_shadow: 'none',           // none | sm | md | lg | xl

    // Title
    title_weight: '700',
    title_letter_spacing: 0,
    title_uppercase: false,
    title_align: 'left',

    // Content
    content_align: 'left',
    content_lines_clamp: 0,         // 0 = no clamp

    // Caption mode (for overlay-caption preset)
    caption_overlay_color: 'rgba(0,0,0,0.55)',
    caption_overlay_gradient: true,

    // CTA / Link
    show_cta: false,
    cta_text: 'Scopri di più',
    cta_style: 'underline',          // underline | arrow | pill | text

    // ── Effect tweaks (audacious presets only) ──
    effect_color: '',                // '' = use preset default
    effect_intensity: 'medium',      // low | medium | high
    effect_speed: 0,                 // 0 = use preset default; ms

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
    // ────────── Pannelli ──────────
    { type: 'separator', label: t('Pannelli') },
    { key: 'panels', label: t('Pannelli'), type: 'content-items', supportsDynamic: true,
      itemFields: [
        { key: 'title', label: t('Titolo'), type: 'text' },
        widgetTemplateField,
        { key: 'content', label: t('Contenuto'), type: 'textarea' },
        { key: 'image', label: t('Immagine'), type: 'image' },
        { key: 'hover_image', label: t('Immagine hover'), type: 'image' },
        { key: 'hover_video', label: t('Video hover'), type: 'media' },
        { key: 'link', label: t('Link'), type: 'link' },
        { key: 'link_target', label: t('Apri in nuova scheda'), type: 'toggle' },
      ],
      newItemDefaults: { title: t('Nuova card'), content: 'Contenuto della card.', image: '', hover_image: '', hover_video: '', link: '', link_target: false, widget_template_id: 0 },
      itemLabel: 'Card',
    },

    // ────────── Layout (struttura) ──────────
    { type: 'separator', label: t('Layout') },
    { key: 'columns', label: t('Colonne'), type: 'select', responsive: true, options: [
      { value: '1', label: t('1 colonna') },
      { value: '2', label: t('2 colonne') },
      { value: '3', label: t('3 colonne') },
      { value: '4', label: t('4 colonne') },
      { value: '5', label: t('5 colonne') },
      { value: '6', label: t('6 colonne') },
    ]},
    { key: 'card_image_position', label: t('Posizione immagine'), type: 'select', options: [
      { value: 'top',        label: t('In alto (default)') },
      { value: 'bottom',     label: t('In basso') },
      { value: 'bg',         label: t('Sfondo (testo sopra)') },
      { value: 'side-left',  label: t('Lato sinistro') },
      { value: 'side-right', label: t('Lato destro') },
    ]},

    // ────────── Immagine (struttura/comportamento) ──────────
    { type: 'separator', label: t('Immagine') },
    { key: 'image_ratio', label: t('Proporzione immagine'), type: 'select', options: [
      { value: 'auto', label: t('Automatica (sconsigliato)') },
      { value: '1/1',  label: t('1:1 Quadrato') },
      { value: '4/3',  label: t('4:3 Standard') },
      { value: '3/2',  label: t('3:2 Foto') },
      { value: '16/9', label: t('16:9 Wide') },
      { value: '21/9', label: t('21:9 Cinema') },
      { value: '3/4',  label: t('3:4 Verticale') },
      { value: '2/3',  label: t('2:3 Verticale') },
    ]},
    { key: 'image_height', label: t('Altezza fissa (px)'), type: 'range', min: 0, max: 500, step: 10,
      condition: { field: 'image_ratio', op: 'eq', value: 'auto' } },
    { key: 'image_fit', label: t('Adattamento'), type: 'select', options: [
      { value: 'cover',   label: t('Copri (riempie e taglia)') },
      { value: 'contain', label: t('Contieni (visibile interamente)') },
      { value: 'fill',    label: t('Riempi (deforma)') },
    ]},
    { key: 'image_zoom', label: t('Zoom al hover'), type: 'toggle' },

    // ────────── Hover behavior ──────────
    { type: 'separator', label: t('Hover behavior') },
    { key: 'hover_lift', label: t('Sollevamento al hover'), type: 'toggle' },
    { key: 'hover_scale', label: t('Ingrandimento card al hover'), type: 'toggle' },

    // ────────── CTA ──────────
    { type: 'separator', label: t('Call to action') },
    { key: 'show_cta', label: t('Mostra CTA in fondo a ogni card'), type: 'toggle' },
    { key: 'cta_text', label: t('Testo CTA'), type: 'text',
      condition: { field: 'show_cta', op: 'eq', value: true } },

    // ────────── Frecce e navigazione ──────────
    { type: 'separator', label: t('Frecce e navigazione') },
    { key: 'show_arrows', label: t('Mostra frecce'), type: 'toggle' },
    { key: 'show_dots', label: t('Mostra dots'), type: 'toggle' },

    { type: 'separator', label: t('Autoplay') },
    { key: 'autoplay', label: t('Autoplay'), type: 'toggle' },
    { key: 'autoplay_interval', label: t('Intervallo (ms)'), type: 'range', min: 1000, max: 10000, step: 500,
      condition: { field: 'autoplay', op: 'eq', value: true } },
  ],

  // ─── STILE ─────────────────────────────────────────────────
  styleFields: [
    // ────────── Preset stile ──────────
    { type: 'separator', label: t('Preset stile') },
    { key: 'preset', label: t('Stile'), type: 'select', options: [
      { value: 'card-modern',        label: t('Card Modern (default)') },
      { value: 'editorial-magazine', label: t('Editorial Magazine (no card)') },
      { value: 'polaroid',           label: t('Polaroid (bordo bianco)') },
      { value: 'overlay-caption',    label: t('Overlay Caption (testo su foto)') },
      { value: 'minimal-clean',      label: t('Minimal Clean') },
      { value: 'liquid-glass',       label: t('Liquid Glass (Vision Pro)') },
      { value: 'neon-cyber',         label: t('Neon Cyberpunk (Tron)') },
      { value: 'brutalist-block',    label: t('Brutalist Block (neo-brutalist)') },
      { value: 'magnetic-liquid',    label: t('Magnetic Liquid (next-gen)') },
      { value: 'sticker',            label: t('Sticker / Scrapbook') },
      { value: 'retro-terminal',     label: t('Retro Terminal (CRT)') },
      { value: '3d-tilt',            label: t('3D Card Tilt') },
      { value: 'custom',             label: t('Personalizzato (usa controlli sotto)') },
    ]},
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },

    // ────────── Tweak effetti (solo preset audaci) ──────────
    { type: 'separator', label: t('Tweak effetto preset'),
      condition: { field: 'preset', op: 'in', value: ['liquid-glass','neon-cyber','brutalist-block','magnetic-liquid','sticker','retro-terminal','3d-tilt'] } },
    { key: 'effect_color', label: t('Colore effetto'), type: 'color',
      description: t('Colore principale degli accent del preset (neon, brutalist, terminal, accent magnetic/sticker). Vuoto = colore di default del preset.'),
      condition: { field: 'preset', op: 'in', value: ['neon-cyber','brutalist-block','magnetic-liquid','sticker','retro-terminal'] } },
    { key: 'effect_intensity', label: t('Intensità effetto'), type: 'select',
      description: t('Scala blur/glow/shadow/rotazione/perspective dell\'effetto.'),
      options: [
        { value: 'low',    label: t('Bassa') },
        { value: 'medium', label: t('Media (default)') },
        { value: 'high',   label: t('Alta') },
      ],
      condition: { field: 'preset', op: 'in', value: ['liquid-glass','neon-cyber','brutalist-block','magnetic-liquid','sticker','retro-terminal','3d-tilt'] } },
    { key: 'effect_speed', label: t('Velocità animazioni (ms)'), type: 'range',
      min: 0, max: 4000, step: 100,
      description: t('0 = default del preset. Controlla pulse neon, transizione magnetic/3d, cursor terminal.'),
      condition: { field: 'preset', op: 'in', value: ['neon-cyber','magnetic-liquid','retro-terminal','3d-tilt'] } },

    ...textEffectsFields([
      { value: 'title', label: t('Solo Titolo') },
      { value: 'content', label: t('Solo Contenuto') },
      { value: 'all', label: t('Tutti gli elementi testuali') },
    ]),

    // ────────── Layout (aspetto) ──────────
    { type: 'separator', label: t('Layout — aspetto') },
    { key: 'gap', label: t('Gap'), type: 'select', options: [
      { value: 'collapse', label: t('Collassato') },
      { value: 'small', label: t('Piccolo') },
      { value: 'default', label: t('Predefinito') },
      { value: 'medium', label: t('Medio') },
      { value: 'large', label: t('Grande') },
    ]},
    { key: 'equal_height', label: t('Altezza card uniforme'), type: 'toggle' },

    // ────────── Stile Card ──────────
    { type: 'separator', label: t('Stile Card') },
    { key: 'card_bg', label: t('Sfondo card'), type: 'color' },
    { key: 'card_border_color', label: t('Colore bordo card'), type: 'color' },
    { key: 'card_border_width', label: t('Spessore bordo (px)'), type: 'range', min: 0, max: 8, step: 1 },
    { key: 'card_border_style', label: t('Stile bordo'), type: 'select', options: [
      { value: 'solid',  label: t('Solido') },
      { value: 'dashed', label: t('Tratteggiato') },
      { value: 'dotted', label: t('Punteggiato') },
      { value: 'double', label: t('Doppio') },
    ]},
    withHover({ key: 'card_radius', label: t('Raggio bordi (px)'), type: 'border-radius'}),
    { key: 'card_padding', label: t('Padding interno (px)'), type: 'spacing', max: 48 },
    ...shadowField,

    // ────────── Hover (stile) ──────────
    { type: 'separator', label: t('Hover — stile') },
    { key: 'hover_shadow', label: t('Ombra hover'), type: 'select', options: [
      { value: 'none', label: t('Nessuna') },
      { value: 'sm',   label: t('Leggera') },
      { value: 'md',   label: t('Media') },
      { value: 'lg',   label: t('Forte') },
      { value: 'xl',   label: t('Molto forte') },
    ]},

    // ────────── Immagine (stile) ──────────
    { type: 'separator', label: t('Immagine — stile') },
    { key: 'card_image_radius', label: t('Arrotondamento immagine (px)'), type: 'range', min: 0, max: 40, step: 1,
      description: t('0 = eredita dal raggio card') },

    // ────────── Caption overlay (per overlay-caption preset) ──────────
    { type: 'separator', label: t('Caption overlay (testo su foto)'),
      condition: { field: 'card_image_position', op: 'eq', value: 'bg' } },
    { key: 'caption_overlay_color', label: t('Overlay colore'), type: 'color',
      condition: { field: 'card_image_position', op: 'eq', value: 'bg' } },
    { key: 'caption_overlay_gradient', label: t('Overlay gradiente (alto→basso)'), type: 'toggle',
      condition: { field: 'card_image_position', op: 'eq', value: 'bg' } },

    // ────────── Tipografia ──────────
    { type: 'separator', label: t('Tipografia') },
    { type: 'typography', label: t('Titolo'),
      presetKey: 'typography_preset',
      responsiveKeys: ['size', 'letterSpacing'],
      letterSpacingUnit: 'em',
      keys: {
        size:          'title_size',
        weight:        'title_weight',
        letterSpacing: 'title_letter_spacing',
        color:         'title_color',
      },
      sizeMin: 0, sizeMax: 48, sizeStep: 1,
    },
    { type: 'typography', label: t('Contenuto'),
      presetKey: 'typography_preset',
      responsiveKeys: ['size'],
      keys: {
        size:  'content_size',
        color: 'content_color',
      },
      sizeMin: 0, sizeMax: 24, sizeStep: 1,
    },
    { key: 'title_uppercase', label: t('Titolo maiuscolo'), type: 'toggle' },
    { key: 'title_align', label: t('Allineamento titolo'), type: 'select', options: [
      { value: 'left', label: t('Sinistra') }, { value: 'center', label: t('Centro') }, { value: 'right', label: t('Destra') },
    ]},
    { key: 'content_align', label: t('Allineamento testo'), type: 'select', options: [
      { value: 'left', label: t('Sinistra') }, { value: 'center', label: t('Centro') }, { value: 'right', label: t('Destra') },
    ]},
    { key: 'content_lines_clamp', label: t('Limita righe testo'), type: 'range', min: 0, max: 8, step: 1,
      description: t('0 = nessun limite') },

    // ────────── CTA — stile ──────────
    { type: 'separator', label: t('Call to action — stile') },
    { key: 'cta_style', label: t('Stile CTA'), type: 'select',
      condition: { field: 'show_cta', op: 'eq', value: true },
      options: [
        { value: 'underline', label: t('Underline') },
        { value: 'arrow',     label: t('Arrow link') },
        { value: 'pill',      label: t('Pill rounded') },
        { value: 'text',      label: t('Solo testo') },
      ]},

    // ────────── Frecce — stile ──────────
    { type: 'separator', label: t('Frecce — stile') },
    { key: 'arrow_style', label: t('Stile frecce'), type: 'select', options: [
      { value: 'circle',         label: t('Cerchio pieno') },
      { value: 'circle-outline', label: t('Cerchio bordato') },
      { value: 'square',         label: t('Quadrato') },
      { value: 'minimal',        label: t('Minimale (solo chevron)') },
      { value: 'chevron-bold',   label: t('Chevron grasso') },
      { value: 'arrow-long',     label: t('Freccia lunga') },
      { value: 'fancy',          label: t('Stilizzato (gradient)') },
      { value: 'uikit',          label: t('UIkit classico') },
    ], condition: { field: 'show_arrows', op: 'eq', value: true } },
    { key: 'arrow_size', label: t('Dimensione frecce (px)'), type: 'range', min: 24, max: 80, step: 2,
      condition: { field: 'show_arrows', op: 'eq', value: true } },
    { key: 'arrow_color', label: t('Colore icona'), type: 'color',
      condition: { field: 'show_arrows', op: 'eq', value: true } },
    { key: 'arrow_bg', label: t('Colore sfondo'), type: 'color',
      condition: { field: 'show_arrows', op: 'eq', value: true } },

    ...wowEffectsFields(),
    ...borderFields(),
  ],
};
