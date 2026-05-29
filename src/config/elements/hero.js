import { textEffectsFields, textEffectsDefaults, withHover } from './_shared';
import { t } from '@/i18n';

/**
 * Tile Hero — applica la regola universale Olobuild:
 *
 *   tab CONTENUTO  → fields[]       solo dati testuali (titolo, testi CTA, URL, tag HTML)
 *   tab STILE      → styleFields[]  tutto l'aspetto (colori, tipografia, layout, effetti, CTA stile)
 *                  + styleFieldsBase (sfondo, padding/margin esterno, bordo, ombra, opacità del wrapper)
 *   tab AVANZATE   → meta tecnico (HTML id, CSS class, condizioni, dynamic data)
 *
 * Tutti i campi (sia fields che styleFields) salvano in tile.settings.* — il PHP renderer
 * resta invariato. Solo l'inspector è riorganizzato in due tab logici.
 */
export default {
  type: 'hero',
  name: t('Hero'),
  icon: 'dashicons-cover-image',
  category: 'layout',
  defaults: {
    typography_preset: '',
    preset: 'custom',

    // Contenuto
    title: t('Titolo Provvisorio'),
    subtitle: t('Sottotitolo provvisorio'),

    // Colori
    text_color: '',
    title_color: '',
    subtitle_color: '',

    // Titolo tipografia
    title_tag: 'h1',
    title_font_family: '',
    title_font_size: '',
    title_font_weight: '700',
    title_letter_spacing: '0',
    title_line_height: '1.2',
    title_text_transform: 'none',
    title_text_shadow: '',

    // Sottotitolo tipografia
    subtitle_font_size: '',
    subtitle_font_weight: '400',
    subtitle_letter_spacing: '0',
    subtitle_max_width: '',

    // Layout
    min_height: '500px',
    content_max_width: '700',
    vertical_align: 'center',
    horizontal_align: 'center',
    text_align: 'center',
    tile_padding: { top: 60, right: 20, bottom: 60, left: 20 },

    // CTA Primario
    cta_text: t('Inizia ora'),
    cta_url: '#',
    cta_target: '_self',
    cta_bg_color: '',
    cta_text_color: '',
    cta_radius: { tl: 6, tr: 6, br: 6, bl: 6 },
    cta_size: '15',
    cta_style: 'filled',

    // CTA Secondario
    cta2_text: '',
    cta2_url: '#',
    cta2_target: '_self',
    cta2_bg_color: '',
    cta2_text_color: '',
    cta2_style: 'outline',

    // Effetti testo
    ...textEffectsDefaults,
    text_effect_target: 'all',
  },

  // ─────────────────────────────────────────────────────────────────
  // TAB CONTENUTO — dati testuali, semantica HTML, link
  // ─────────────────────────────────────────────────────────────────
  fields: [
    { key: 'title', label: t('Titolo'), type: 'text' },
    { key: 'subtitle', label: t('Sottotitolo'), type: 'text' },

    { type: 'separator', label: t('CTA Primario') },
    { key: 'cta_text',   label: t('Testo pulsante'), type: 'text' },
    { key: 'cta_url',    label: t('URL pulsante'),   type: 'link' },
    { key: 'cta_target', label: t('Apri in'), type: 'select', options: [
      { value: '_self',  label: t('Stessa finestra') },
      { value: '_blank', label: t('Nuova scheda') },
    ]},

    { type: 'separator', label: t('CTA Secondario') },
    { key: 'cta2_text',   label: t('Testo (vuoto = nascosto)'), type: 'text' },
    { key: 'cta2_url',    label: t('URL'), type: 'link' },
    { key: 'cta2_target', label: t('Apri in'), type: 'select', options: [
      { value: '_self',  label: t('Stessa finestra') },
      { value: '_blank', label: t('Nuova scheda') },
    ]},
  ],

  // ─────────────────────────────────────────────────────────────────
  // TAB STILE — aspetto visivo specifico della hero. Mostrato dal
  // StyleFieldsRenderer PRIMA dei campi style universali del wrapper.
  // ─────────────────────────────────────────────────────────────────
  styleFields: [
    { type: 'separator', label: t('Preset stilistico') },
    { key: 'preset', label: t('Stile'), type: 'select', options: [
      { value: 'modern-centered',   label: t('Modern Centered') },
      { value: 'split-image',       label: t('Split Image') },
      { value: 'minimal-editorial', label: t('Minimal Editorial') },
      { value: 'bold-statement',    label: t('Bold Statement') },
      { value: 'video-cinema',      label: t('Video Cinema') },
      { value: 'glass-overlay',     label: t('Glass Overlay') },
      { value: 'neon-cyberpunk',    label: t('Neon Cyberpunk') },
      { value: 'brutalist-mega',    label: t('Brutalist Mega') },
      { value: 'gradient-aurora',   label: t('Gradient Aurora') },
      { value: 'sticker-collage',   label: t('Sticker Collage') },
      { value: 'retro-poster',      label: t('Retro Poster') },
      { value: 'tilt-parallax',     label: t('Tilt Parallax') },
      { value: 'custom',            label: t('Personalizzato') },
    ]},
    // ── Tipografia (unica sezione: titolo, sottotitolo, default + effetti) ──
    { type: 'separator', label: t('Tipografia') },
    { key: 'text_color', label: t('Colore testo (default)'), type: 'color',
      description: t('Default per titolo + sottotitolo, sovrascritto dai colori specifici nei popover qui sotto.') },
    { type: 'typography', label: t('Titolo'),
      presetKey: 'typography_preset',
      responsiveKeys: ['size', 'lineHeight', 'letterSpacing'],
      keys: {
        tag:           'title_tag',
        family:        'title_font_family',
        size:          'title_font_size',
        weight:        'title_font_weight',
        transform:     'title_text_transform',
        lineHeight:    'title_line_height',
        letterSpacing: 'title_letter_spacing',
        color:         'title_color',
        shadow:        'title_text_shadow',
      },
      sizeMin: 14, sizeMax: 120, sizeStep: 1,
    },
    { type: 'typography', label: t('Sottotitolo'),
      presetKey: 'typography_preset',
      responsiveKeys: ['size', 'letterSpacing'],
      keys: {
        size:          'subtitle_font_size',
        weight:        'subtitle_font_weight',
        letterSpacing: 'subtitle_letter_spacing',
        color:         'subtitle_color',
      },
      sizeMin: 12, sizeMax: 48, sizeStep: 1,
    },
    { key: 'subtitle_max_width', label: t('Larghezza max sottotitolo (px)'), type: 'range', min: 200, max: 1000, step: 10 },

    // ── Effetti testo (gradient/neon/typewriter/...) ──
    ...textEffectsFields([
      { value: 'title',    label: t('Solo Titolo') },
      { value: 'subtitle', label: t('Solo Sottotitolo') },
      { value: 'all',      label: t('Tutti gli elementi testuali') },
    ]),

    // ── Layout interno ──
    { type: 'separator', label: t('Layout interno') },
    { key: 'min_height',         label: t('Altezza minima (es. 500px, 80vh)'), type: 'text' },
    { key: 'content_max_width',  label: t('Larghezza max contenuto (px)'), type: 'range', min: 200, max: 1200, step: 50 },
    { key: 'vertical_align',     label: t('Posizione verticale del blocco'), type: 'select', options: [
      { value: 'top',    label: t('In alto') },
      { value: 'center', label: t('Al centro') },
      { value: 'bottom', label: t('In basso') },
    ]},
    { key: 'horizontal_align',   label: t('Posizione orizzontale del blocco'), type: 'select', options: [
      { value: 'left',   label: t('A sinistra') },
      { value: 'center', label: t('Al centro') },
      { value: 'right',  label: t('A destra') },
    ]},
    { key: 'text_align',         label: t('Allineamento del testo'), type: 'select', options: [
      { value: 'left',   label: t('A sinistra') },
      { value: 'center', label: t('Al centro') },
      { value: 'right',  label: t('A destra') },
    ]},
    { key: 'tile_padding', label: t('Padding del contenuto interno (px)'), type: 'spacing', max: 200 },

    // ── Stile pulsante CTA Primario ──
    { type: 'separator', label: t('Stile CTA Primario') },
    { key: 'cta_style', label: t('Tipo'), type: 'select', options: [
      { value: 'filled',  label: t('Pieno') },
      { value: 'outline', label: t('Contorno') },
      { value: 'ghost',   label: t('Trasparente') },
    ]},
    { key: 'cta_size',       label: t('Dimensione testo (px)'), type: 'range', min: 12, max: 24, step: 1 },
    { key: 'cta_bg_color',   label: t('Colore sfondo'), type: 'color' },
    { key: 'cta_text_color', label: t('Colore testo'),  type: 'color' },
    withHover({ key: 'cta_radius', label: t('Raggio bordo (px)'), type: 'border-radius' }),

    // ── Stile pulsante CTA Secondario ──
    { type: 'separator', label: t('Stile CTA Secondario') },
    { key: 'cta2_style', label: t('Tipo'), type: 'select', options: [
      { value: 'filled',  label: t('Pieno') },
      { value: 'outline', label: t('Contorno') },
      { value: 'ghost',   label: t('Trasparente') },
    ]},
    { key: 'cta2_bg_color',   label: t('Colore sfondo'), type: 'color' },
    { key: 'cta2_text_color', label: t('Colore testo'),  type: 'color' },
  ],
};
