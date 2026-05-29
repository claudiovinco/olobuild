
import { textEffectsFields, textEffectsDefaults, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, withHover, wowEffectsFields, wowEffectsDefaults } from './_shared';
import { t } from '@/i18n';

/**
 * Tile Timeline — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → array items (content-items), layout (vertical/horizontal + mobile),
 *                   toggle visibilità marker (end_marker, line_progress, marker_pulse, card_arrow),
 *                   marker_type/marker_shape, tipo animazione (animation)
 *   styleFields[] → preset, bg, typography_preset, textEffects, effect tweaks,
 *                   linea (colore/spessore/stile), marker (colore/dim/bordo),
 *                   card (sfondo, padding, radius, shadow, hover, max-width, media ratio/radius),
 *                   data (posizione/colore/dim/peso), tipografia (size/peso/color),
 *                   stagger/duration animazione, opzioni orizzontale (width/visible/gap/arrow), bordo
 *   defaults      → identico (nessuna modifica)
 *
 * Note di scelta:
 *   - `marker_type` (dot/icon/number) è tipo di marker → fields[]
 *   - `marker_shape` (circle/square/diamond) è tipo geometrico → fields[]
 *   - `marker_pulse` (toggle animazione comportamentale) → fields[]
 *   - `card_arrow` (toggle visibilità freccia) → fields[]
 *   - `end_marker` + `end_marker_icon` → fields[]; `end_marker_color/bg/size` → styleFields[]
 *   - `card_hover` è effetto → styleFields[]
 *   - `animation` (tipo) → fields[]; `stagger_delay`/`animation_duration` (ms) → styleFields[]
 *   - `effect_*` (tweak preset audace) → styleFields[]
 */
export default {
  type: 'timeline',
  name: t('Timeline'),
  icon: 'dashicons-backup',
  category: 'interactive',
  defaults: {
    bg: { type: 'none' },
    typography_preset: '',
    // Items
    items: [
      { id: 'tl-1', title: t('La fondazione'), description: t('L\'inizio del nostro percorso, con una visione chiara e un team appassionato.'), date: '2014', image: 'https://images.unsplash.com/photo-1618468121522-fa4b1cbac0e2?w=900&q=75&auto=format&fit=crop', icon: 'star', icon_color: '' },
      { id: 'tl-2', title: t('L\'espansione'), description: t('Crescita del team e apertura di nuove sedi nel cuore della città.'), date: '2019', image: 'https://images.unsplash.com/photo-1573408268160-571b710c06c2?w=900&q=75&auto=format&fit=crop', icon: 'check', icon_color: '' },
      { id: 'tl-3', title: t('Il riconoscimento'), description: t('Premi internazionali e collaborazioni con brand di livello mondiale.'), date: '2024', image: 'https://images.unsplash.com/photo-1761682751228-783c48e7cd30?w=900&q=75&auto=format&fit=crop', icon: 'heart', icon_color: '' },
    ],

    // Preset
    preset: 'classic-center',

    // Layout
    layout: 'vertical-center',
    mobile_layout: 'vertical-left',

    // Linea
    line_color: '',
    line_width: '3',
    line_style: 'solid',
    line_progress: false,
    line_progress_color: '',
    line_progress_width: '',

    // Marker
    marker_type: 'dot',
    marker_size: '20',
    marker_color: '',
    marker_bg: '',
    marker_border_width: '3',
    marker_border_color: '',
    marker_shape: 'circle',
    marker_pulse: false,

    // Marker finale
    end_marker: true,
    end_marker_icon: 'flag',
    end_marker_color: '',
    end_marker_bg: '',
    end_marker_size: '',

    // Card
    card_bg: '',
    card_text_color: '',
    tile_padding: { top: 20, right: 20, bottom: 20, left: 20 },
    card_border_radius: '12',
    card_shadow: 'md',
    card_border_width: '0',
    card_border_color: '',
    card_hover: 'lift',
    card_arrow: true,
    card_max_width: '',
    card_media_ratio: 'auto',
    card_media_margin: '0',
    card_media_radius: '4',

    // Data
    date_position: 'outside',
    date_color: '',
    date_size: '14',
    date_weight: '600',

    // Tipografia
    title_size: '18',
    title_weight: '600',
    title_color: '',
    description_size: '14',
    description_color: '',

    // Animazione
    animation: 'fade-up',
    stagger_delay: '150',
    animation_duration: '600',

    // Orizzontale
    h_card_width: '300',
    h_visible_items: '3',
    h_gap: '24',
    h_arrow_color: '',
    h_arrow_bg: '',

    // Effect tweaks (audacious presets only)
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
    // ── Items ──
    { type: 'separator', label: t('Eventi') },
    { key: 'items', label: t('Eventi'), type: 'content-items',
      itemFields: [
        { key: 'title', label: t('Titolo'), type: 'text' },
        { key: 'description', label: t('Descrizione'), type: 'textarea' },
        { key: 'date', label: t('Data / etichetta'), type: 'text' },
        { key: 'image', label: t('Immagine'), type: 'image' },
        { key: 'video', label: t('Video'), type: 'media' },
        { key: 'icon', label: t('Icona marker'), type: 'icon' },
        { key: 'icon_color', label: t('Colore icona'), type: 'color' },
      ],
      newItemDefaults: { title: t('Nuovo evento'), description: t('Descrizione evento.'), date: '', image: '', video: '', icon: '', icon_color: '' },
      itemLabel: 'Evento',
    },

    // ── Layout ──
    { type: 'separator', label: t('Layout') },
    { key: 'layout', label: t('Layout'), type: 'select', options: [
      { value: 'vertical-center', label: t('Verticale alternato') },
      { value: 'vertical-left', label: t('Verticale sinistra') },
      { value: 'vertical-right', label: t('Verticale destra') },
      { value: 'horizontal', label: t('Orizzontale') },
    ]},
    { key: 'mobile_layout', label: t('Layout mobile'), type: 'select', options: [
      { value: 'vertical-left', label: t('Sinistra') },
      { value: 'vertical-right', label: t('Destra') },
    ]},

    // ── Linea (comportamento) ──
    { type: 'separator', label: t('Linea — Comportamento') },
    { key: 'line_progress', label: t('Linea progressiva'), type: 'toggle' },

    // ── Marker (tipo / forma / comportamento) ──
    { type: 'separator', label: t('Marker — Tipo') },
    { key: 'marker_type', label: t('Tipo marker'), type: 'select', options: [
      { value: 'dot', label: t('Pallino') },
      { value: 'icon', label: t('Icona') },
      { value: 'number', label: t('Numero') },
    ]},
    { key: 'marker_shape', label: t('Forma'), type: 'select', options: [
      { value: 'circle', label: t('Cerchio') },
      { value: 'square', label: t('Quadrato') },
      { value: 'diamond', label: t('Rombo') },
    ]},
    { key: 'marker_pulse', label: t('Animazione pulse'), type: 'toggle' },

    // ── Marker finale ──
    { type: 'separator', label: t('Marker finale') },
    { key: 'end_marker', label: t('Mostra marker finale'), type: 'toggle' },
    { key: 'end_marker_icon', label: t('Icona finale'), type: 'icon',
      condition: { field: 'end_marker', value: true } },

    // ── Card (comportamento) ──
    { type: 'separator', label: t('Card — Comportamento') },
    { key: 'card_arrow', label: t('Freccia verso linea'), type: 'toggle' },
    { key: 'card_media_ratio', label: t('Rapporto media'), type: 'select', options: [
      { value: 'auto', label: t('Automatico') },
      { value: '16/9', label: '16:9' },
      { value: '4/3', label: '4:3' },
      { value: '3/2', label: '3:2' },
      { value: '1/1', label: '1:1' },
      { value: '2/1', label: '2:1' },
    ]},

    // ── Data (posizione) ──
    { type: 'separator', label: t('Etichetta data') },
    { key: 'date_position', label: t('Posizione data'), type: 'select', options: [
      { value: 'outside', label: t('Esterna') },
      { value: 'inside', label: t('Interna alla card') },
      { value: 'above', label: t('Sopra la card') },
    ]},

    // ── Animazione (tipo) ──
    { type: 'separator', label: t('Animazione') },
    { key: 'animation', label: t('Animazione'), type: 'select', options: [
      { value: 'none', label: t('Nessuna') },
      { value: 'fade-up', label: t('Fade up') },
      { value: 'fade-in', label: t('Fade in') },
      { value: 'slide-left', label: t('Scorrimento da sinistra') },
      { value: 'slide-right', label: t('Scorrimento da destra') },
      { value: 'zoom-in', label: t('Zoom in') },
    ]},
  ],

  // ─── STILE ─────────────────────────────────────────────────
  styleFields: [
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },
    // ────────── Preset stile ──────────
    { type: 'separator', label: t('Preset stile') },
    { key: 'preset', label: t('Stile'), type: 'select', options: [
      { value: 'classic-center',   label: t('Classic Center (default alternato)') },
      { value: 'modern-cards',     label: t('Modern Cards (sx con shadow)') },
      { value: 'minimal-line',     label: t('Minimal Line (linea sottile)') },
      { value: 'magazine-history', label: t('Magazine History (editorial)') },
      { value: 'corporate-clean',  label: t('Corporate Clean (verticale dx)') },
      { value: 'liquid-glass',     label: t('Liquid Glass (Vision Pro)') },
      { value: 'neon-cyber',       label: t('Neon Cyberpunk (Tron)') },
      { value: 'brutalist-block',  label: t('Brutalist Block (neo-brutalist)') },
      { value: 'magnetic-liquid',  label: t('Magnetic Liquid (next-gen)') },
      { value: 'sticker',          label: t('Sticker / Scrapbook') },
      { value: 'retro-terminal',   label: t('Retro Terminal (CRT)') },
      { value: '3d-tilt',          label: t('3D Card Tilt') },
      { value: 'custom',           label: t('Personalizzato (usa controlli sotto)') },
    ]},

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
      { value: 'description', label: t('Solo Descrizione') },
      { value: 'all', label: t('Tutti gli elementi testuali') },
    ]),

    // ── Linea ──
    { type: 'separator', label: t('Linea') },
    { key: 'line_color', label: t('Colore linea'), type: 'color' },
    { key: 'line_width', label: t('Spessore linea (px)'), type: 'range', min: 1, max: 8, step: 1 },
    { key: 'line_style', label: t('Stile linea'), type: 'select', options: [
      { value: 'solid', label: t('Continua') },
      { value: 'dashed', label: t('Tratteggiata') },
      { value: 'dotted', label: t('Puntinata') },
    ]},
    { key: 'line_progress_color', label: t('Colore progresso'), type: 'color',
      condition: { field: 'line_progress', value: true } },
    { key: 'line_progress_width', label: t('Spessore progresso (px)'), type: 'range', min: 1, max: 16, step: 1,
      condition: { field: 'line_progress', value: true } },

    // ── Marker (stile) ──
    { type: 'separator', label: t('Marker — Stile') },
    { key: 'marker_size', label: t('Dimensione (px)'), type: 'range', min: 10, max: 40, step: 2 },
    { key: 'marker_color', label: t('Colore marker'), type: 'color' },
    { key: 'marker_bg', label: t('Sfondo marker'), type: 'color' },
    { key: 'marker_border_width', label: t('Bordo marker (px)'), type: 'range', min: 0, max: 6, step: 1 },
    { key: 'marker_border_color', label: t('Colore bordo marker'), type: 'color',
      condition: { field: 'marker_border_width', operator: '>', value: '0' } },

    // ── Marker finale (stile) ──
    { type: 'separator', label: t('Marker finale — Stile') },
    { key: 'end_marker_color', label: t('Colore icona finale'), type: 'color',
      condition: { field: 'end_marker', value: true } },
    { key: 'end_marker_bg', label: t('Sfondo marker finale'), type: 'color',
      condition: { field: 'end_marker', value: true } },
    { key: 'end_marker_size', label: t('Dimensione finale (px)'), type: 'range', min: 10, max: 60, step: 2,
      condition: { field: 'end_marker', value: true } },

    // ── Card ──
    { type: 'separator', label: t('Card — Aspetto') },
    { key: 'card_bg', label: t('Sfondo card'), type: 'color' },
    { key: 'card_text_color', label: t('Colore testo card'), type: 'color' },
    { key: 'tile_padding', label: t('Padding (px)'), type: 'spacing', max: 40 },
    withHover({ key: 'card_border_radius', label: t('Arrotondamento (px)'), type: 'border-radius' }),

    { type: 'separator', label: t('Card — Ombra') },
    { key: 'card_shadow', label: t('Ombra'), type: 'select', options: [
      { value: 'none', label: t('Nessuna') },
      { value: 'sm', label: t('Leggera') },
      { value: 'md', label: t('Media') },
      { value: 'lg', label: t('Grande') },
      { value: 'custom', label: t('Personalizzata') },
    ]},
    { key: 'card_shadow_h', label: t('Offset H (px)'), type: 'range', min: -50, max: 50, step: 1,
      condition: { field: 'card_shadow', op: 'eq', value: 'custom' } },
    { key: 'card_shadow_v', label: t('Offset V (px)'), type: 'range', min: -50, max: 50, step: 1,
      condition: { field: 'card_shadow', op: 'eq', value: 'custom' } },
    { key: 'card_shadow_blur', label: t('Sfocatura (px)'), type: 'range', min: 0, max: 100, step: 1,
      condition: { field: 'card_shadow', op: 'eq', value: 'custom' } },
    { key: 'card_shadow_spread', label: t('Espansione (px)'), type: 'range', min: -50, max: 50, step: 1,
      condition: { field: 'card_shadow', op: 'eq', value: 'custom' } },
    { key: 'card_shadow_color', label: t('Colore ombra'), type: 'color',
      condition: { field: 'card_shadow', op: 'eq', value: 'custom' } },
    { key: 'card_shadow_inset', label: t('Ombra interna'), type: 'toggle',
      condition: { field: 'card_shadow', op: 'eq', value: 'custom' } },

    { type: 'separator', label: t('Card — Bordo e hover') },
    { key: 'card_border_width', label: t('Bordo card (px)'), type: 'range', min: 0, max: 4, step: 1 },
    { key: 'card_border_color', label: t('Colore bordo card'), type: 'color',
      condition: { field: 'card_border_width', operator: '>', value: '0' } },
    { key: 'card_hover', label: t('Effetto hover'), type: 'select', options: [
      { value: 'none', label: t('Nessuno') },
      { value: 'lift', label: t('Sollevamento') },
      { value: 'glow', label: t('Bagliore') },
      { value: 'scale', label: t('Scala') },
    ]},
    { key: 'card_max_width', label: t('Larghezza max card (px)'), type: 'range', min: 0, max: 800, step: 10 },

    { type: 'separator', label: t('Card — Media') },
    { key: 'card_media_margin', label: t('Margine media (px)'), type: 'spacing', max: 20 },
    withHover({ key: 'card_media_radius', label: t('Arrotondamento media (px)'), type: 'border-radius' }),

    // ── Tipografia ──
    { type: 'separator', label: t('Tipografia') },
    { type: 'typography', label: t('Titolo'),
      presetKey: 'typography_preset',
      responsiveKeys: ['size'],
      keys: {
        size:   'title_size',
        weight: 'title_weight',
        color:  'title_color',
      },
      sizeMin: 14, sizeMax: 32, sizeStep: 1,
    },
    { type: 'typography', label: t('Descrizione'),
      presetKey: 'typography_preset',
      responsiveKeys: ['size'],
      keys: {
        size:  'description_size',
        color: 'description_color',
      },
      sizeMin: 12, sizeMax: 20, sizeStep: 1,
    },
    { type: 'typography', label: t('Data'),
      presetKey: 'typography_preset',
      responsiveKeys: ['size'],
      keys: {
        size:   'date_size',
        weight: 'date_weight',
        color:  'date_color',
      },
      sizeMin: 10, sizeMax: 20, sizeStep: 1,
    },

    // ── Animazione (tempi) ──
    { type: 'separator', label: t('Animazione — Tempi') },
    { key: 'stagger_delay', label: t('Ritardo stagger (ms)'), type: 'range', min: 0, max: 500, step: 25,
      condition: { field: 'animation', operator: '!=', value: 'none' } },
    { key: 'animation_duration', label: t('Durata animazione (ms)'), type: 'range', min: 200, max: 1200, step: 50,
      condition: { field: 'animation', operator: '!=', value: 'none' } },

    // ── Orizzontale ──
    { type: 'separator', label: t('Opzioni orizzontale') },
    { key: 'h_card_width', label: t('Larghezza card (px)'), type: 'range', min: 200, max: 500, step: 10,
      condition: { field: 'layout', value: 'horizontal' } },
    { key: 'h_visible_items', label: t('Elementi visibili'), type: 'range', min: 1, max: 5, step: 1,
      condition: { field: 'layout', value: 'horizontal' } },
    { key: 'h_gap', label: t('Gap (px)'), type: 'range', min: 8, max: 48, step: 4,
      condition: { field: 'layout', value: 'horizontal' } },
    { key: 'h_arrow_color', label: t('Colore frecce'), type: 'color',
      condition: { field: 'layout', value: 'horizontal' } },
    { key: 'h_arrow_bg', label: t('Sfondo frecce'), type: 'color',
      condition: { field: 'layout', value: 'horizontal' } },
    ...wowEffectsFields(),
    ...borderFields(),
  ],
};
