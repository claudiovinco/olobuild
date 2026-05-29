import { textEffectsFields, textEffectsDefaults, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, withHover } from './_shared';
import { shadowField } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile Chart — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → tipo grafico, dati (items), orientamento, stacked, dataset_label,
 *                   toggle show_* (legend/title/subtitle/tooltip), testi titolo/sottotitolo,
 *                   posizione/allineamento legenda, comportamento dati (animate, stacked,
 *                   stepped_line, fill_area, point_style, asse Y min/max/step, etichette assi,
 *                   formato valori), toggle griglia/bordi assi/begin_at_zero
 *   styleFields[] → preset, bg, typography_preset, textEffectsFields, altezza grafico,
 *                   colori/dimensioni/spaziature di legenda/titolo/sottotitolo/tooltip,
 *                   border_radius tooltip, bordi e raggi barre/punti, doughnut_cutout,
 *                   colori griglia/assi/testi, tension, shadow + borderFields
 */
export default {
  type: 'chart',
  name: t('Grafico'),
  icon: 'dashicons-chart-area',
  category: 'interactive',
  defaults: {
    preset: 'custom',
    bg: { type: 'none' },
    typography_preset: '',
    chart_type: 'bar',
    items: [
      { id: 'c-1', label: t('Gen'), value: '65', color: '#6366F1' },
      { id: 'c-2', label: 'Feb', value: '45', color: '#8B5CF6' },
      { id: 'c-3', label: t('Mar'), value: '80', color: '#A78BFA' },
      { id: 'c-4', label: t('Apr'), value: '55', color: '#C4B5FD' },
    ],
    chart_height: '400',

    // Legenda
    show_legend: true,
    legend_position: 'bottom',
    legend_align: 'center',
    legend_color: '',
    legend_font_size: '12',
    legend_font_weight: '400',
    legend_box_width: '40',
    legend_padding: '10',
    legend_point_style: false,

    // Titolo
    show_title: false,
    chart_title: '',
    title_color: '',
    title_font_size: '16',
    title_font_weight: '700',
    title_padding: '10',

    // Sottotitolo
    show_subtitle: false,
    chart_subtitle: '',
    subtitle_color: '',
    subtitle_font_size: '12',

    // Tooltip
    tooltip_enabled: true,
    tooltip_bg: '#000000',
    tooltip_text_color: '#ffffff',
    tooltip_border_color: '',
    tooltip_border_width: '0',
    tooltip_corner_radius: '6',
    tooltip_font_size: '12',
    tooltip_padding: '8',

    // Stile dati
    animate: true,
    bg_color: 'transparent',
    border_width: '2',
    border_color_override: '',
    bar_radius: '0',
    bar_percentage: '0.8',
    category_percentage: '0.8',
    fill_area: false,
    point_radius: '4',
    point_hover_radius: '6',
    point_style: 'circle',
    tension: '0.4',
    doughnut_cutout: '50',

    // Griglia e assi
    grid_color: '',
    grid_line_width: '1',
    axis_color: '',
    text_color: '',
    tick_font_size: '11',
    show_x_grid: true,
    show_y_grid: true,
    show_x_border: true,
    show_y_border: true,
    begin_at_zero: true,
    y_min: '',
    y_max: '',
    y_step_size: '',
    index_axis: 'x',
    dataset_label: '',
    stacked: false,
    stepped_line: '',
    x_label: '',
    y_label: '',
    tooltip_prefix: '',
    tooltip_suffix: '',
    number_format: false,

    shadow: 'none',
    ...textEffectsDefaults,
    text_effect_target: 'label',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { key: 'chart_type', label: t('Tipo grafico'), type: 'select', options: [
      { value: 'bar', label: t('Barre') },
      { value: 'line', label: t('Linea') },
      { value: 'pie', label: t('Torta') },
      { value: 'doughnut', label: t('Ciambella') },
      { value: 'radar', label: t('Radar') },
      { value: 'polarArea', label: t('Area polare') },
    ]},
    { key: 'index_axis', label: t('Orientamento'), type: 'select', options: [
      { value: 'x', label: t('Verticale') },
      { value: 'y', label: t('Orizzontale') },
    ], condition: { field: 'chart_type', op: 'in', value: ['bar'] } },
    { key: 'stacked', label: t('Impilato (stacked)'), type: 'toggle',
      condition: { field: 'chart_type', op: 'in', value: ['bar', 'line'] } },
    { key: 'items', label: t('Dati'), type: 'content-items',
      itemFields: [
        { key: 'label', label: t('Etichetta'), type: 'text' },
        { key: 'value', label: t('Valore'), type: 'text' },
        { key: 'color', label: t('Colore sfondo'), type: 'color' },
        { key: 'border_color', label: t('Colore bordo'), type: 'color' },
      ],
      newItemDefaults: { label: t('Nuovo'), value: '50', color: '#6366F1', border_color: '' },
      itemLabel: 'Dato',
    },
    { key: 'dataset_label', label: t('Etichetta dataset'), type: 'text', placeholder: t('Mostrata nella legenda'),
      condition: { field: 'chart_type', op: 'in', value: ['bar', 'line', 'radar'] } },

    // ── Legenda (contenuto) ──
    { type: 'separator', label: t('Legenda') },
    { key: 'show_legend', label: t('Mostra legenda'), type: 'toggle' },
    { key: 'legend_position', label: t('Posizione'), type: 'select', options: [
      { value: 'top', label: t('Alto') },
      { value: 'bottom', label: t('Basso') },
      { value: 'left', label: t('Sinistra') },
      { value: 'right', label: t('Destra') },
    ], condition: { field: 'show_legend', value: true } },
    { key: 'legend_align', label: t('Allineamento'), type: 'select', options: [
      { value: 'start', label: t('Inizio') },
      { value: 'center', label: t('Centro') },
      { value: 'end', label: t('Fine') },
    ], condition: { field: 'show_legend', value: true } },
    { key: 'legend_point_style', label: t('Indicatore circolare'), type: 'toggle',
      condition: { field: 'show_legend', value: true } },

    // ── Titolo (contenuto) ──
    { type: 'separator', label: t('Titolo') },
    { key: 'show_title', label: t('Mostra titolo'), type: 'toggle' },
    { key: 'chart_title', label: t('Testo titolo'), type: 'text',
      condition: { field: 'show_title', value: true } },

    // ── Sottotitolo (contenuto) ──
    { key: 'show_subtitle', label: t('Mostra sottotitolo'), type: 'toggle' },
    { key: 'chart_subtitle', label: t('Testo sottotitolo'), type: 'text',
      condition: { field: 'show_subtitle', value: true } },

    // ── Tooltip (contenuto) ──
    { type: 'separator', label: t('Tooltip') },
    { key: 'tooltip_enabled', label: t('Mostra tooltip'), type: 'toggle' },

    // ── Stile dati (comportamento) ──
    { type: 'separator', label: t('Stile dati') },
    { key: 'animate', label: t('Animazione'), type: 'toggle' },
    { key: 'bar_percentage', label: t('Larghezza barre (%)'), type: 'range', min: 0.1, max: 1, step: 0.05,
      condition: { field: 'chart_type', value: 'bar' } },
    { key: 'category_percentage', label: t('Larghezza categoria (%)'), type: 'range', min: 0.1, max: 1, step: 0.05,
      condition: { field: 'chart_type', value: 'bar' } },
    { key: 'fill_area', label: t('Riempi area'), type: 'toggle',
      condition: { field: 'chart_type', op: 'in', value: ['line', 'radar'] } },
    { key: 'stepped_line', label: t('Linea a gradini'), type: 'select', options: [
      { value: '', label: t('No') },
      { value: 'before', label: t('Prima') },
      { value: 'after', label: t('Dopo') },
      { value: 'middle', label: t('Centro') },
    ], condition: { field: 'chart_type', value: 'line' } },
    { key: 'point_style', label: t('Stile punti'), type: 'select', options: [
      { value: 'circle', label: t('Cerchio') },
      { value: 'rect', label: t('Quadrato') },
      { value: 'rectRounded', label: t('Quadrato arrotondato') },
      { value: 'triangle', label: t('Triangolo') },
      { value: 'star', label: t('Stella') },
      { value: 'cross', label: t('Croce') },
      { value: 'crossRot', label: t('Croce ruotata') },
      { value: 'dash', label: t('Trattino') },
    ], condition: { field: 'chart_type', op: 'in', value: ['line', 'radar'] } },

    // ── Griglia e assi (comportamento/labels) ──
    { type: 'separator', label: t('Griglia e assi') },
    { key: 'show_x_grid', label: t('Griglia asse X'), type: 'toggle',
      condition: { field: 'chart_type', op: 'in', value: ['bar', 'line'] } },
    { key: 'show_y_grid', label: t('Griglia asse Y'), type: 'toggle',
      condition: { field: 'chart_type', op: 'in', value: ['bar', 'line'] } },
    { key: 'show_x_border', label: t('Bordo asse X'), type: 'toggle',
      condition: { field: 'chart_type', op: 'in', value: ['bar', 'line'] } },
    { key: 'show_y_border', label: t('Bordo asse Y'), type: 'toggle',
      condition: { field: 'chart_type', op: 'in', value: ['bar', 'line'] } },
    { key: 'begin_at_zero', label: t('Inizia da zero'), type: 'toggle',
      condition: { field: 'chart_type', op: 'in', value: ['bar', 'line'] } },
    { key: 'y_min', label: t('Y minimo'), type: 'text', placeholder: t('Auto'),
      condition: { field: 'chart_type', op: 'in', value: ['bar', 'line'] } },
    { key: 'y_max', label: t('Y massimo'), type: 'text', placeholder: t('Auto'),
      condition: { field: 'chart_type', op: 'in', value: ['bar', 'line'] } },
    { key: 'y_step_size', label: t('Incremento Y'), type: 'text', placeholder: t('Auto'),
      condition: { field: 'chart_type', op: 'in', value: ['bar', 'line'] } },
    { key: 'x_label', label: t('Etichetta asse X'), type: 'text',
      condition: { field: 'chart_type', op: 'in', value: ['bar', 'line'] } },
    { key: 'y_label', label: t('Etichetta asse Y'), type: 'text',
      condition: { field: 'chart_type', op: 'in', value: ['bar', 'line'] } },

    // ── Formato valori ──
    { type: 'separator', label: t('Formato valori') },
    { key: 'tooltip_prefix', label: t('Prefisso valore'), type: 'text', placeholder: t('es. €') },
    { key: 'tooltip_suffix', label: t('Suffisso valore'), type: 'text', placeholder: t('es. %') },
    { key: 'number_format', label: t('Separatore migliaia'), type: 'toggle' },
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

    ...textEffectsFields([ { value: 'label', label: t('Solo Etichetta') } ]),
    { key: 'chart_height', label: t('Altezza (px)'), type: 'range', min: 200, max: 800, step: 10 },

    // ── Tipografia ──
    { type: 'separator', label: t('Tipografia') },
    { type: 'typography', label: t('Legenda'),
      presetKey: 'typography_preset',
      responsiveKeys: ['size'],
      keys: {
        size:   'legend_font_size',
        weight: 'legend_font_weight',
        color:  'legend_color',
      },
      sizeMin: 8, sizeMax: 20, sizeStep: 1,
    },
    { type: 'typography', label: t('Titolo'),
      presetKey: 'typography_preset',
      responsiveKeys: ['size'],
      keys: {
        size:   'title_font_size',
        weight: 'title_font_weight',
        color:  'title_color',
      },
      sizeMin: 10, sizeMax: 32, sizeStep: 1,
    },
    { type: 'typography', label: t('Sottotitolo'),
      presetKey: 'typography_preset',
      responsiveKeys: ['size'],
      keys: {
        size:  'subtitle_font_size',
        color: 'subtitle_color',
      },
      sizeMin: 8, sizeMax: 20, sizeStep: 1,
    },
    { type: 'typography', label: t('Tooltip'),
      presetKey: 'typography_preset',
      responsiveKeys: ['size'],
      keys: {
        size:  'tooltip_font_size',
        color: 'tooltip_text_color',
      },
      sizeMin: 8, sizeMax: 18, sizeStep: 1,
    },
    { type: 'typography', label: t('Assi'),
      presetKey: 'typography_preset',
      responsiveKeys: ['size'],
      keys: {
        size:  'tick_font_size',
        color: 'text_color',
      },
      sizeMin: 8, sizeMax: 16, sizeStep: 1,
    },

    // ── Legenda (stile) ──
    { type: 'separator', label: t('Legenda — stile') },
    { key: 'legend_box_width', label: t('Larghezza indicatore'), type: 'range', min: 10, max: 60, step: 2,
      condition: { field: 'show_legend', value: true } },
    { key: 'legend_padding', label: t('Padding'), type: 'spacing', max: 30,
      condition: { field: 'show_legend', value: true } },

    // ── Titolo (stile) ──
    { type: 'separator', label: t('Titolo — stile') },
    { key: 'title_padding', label: t('Padding titolo'), type: 'spacing', max: 40,
      condition: { field: 'show_title', value: true } },

    // ── Tooltip (stile) ──
    { type: 'separator', label: t('Tooltip — stile') },
    { key: 'tooltip_bg', label: t('Sfondo tooltip'), type: 'color',
      condition: { field: 'tooltip_enabled', value: true } },
    { key: 'tooltip_border_color', label: t('Bordo tooltip'), type: 'color',
      condition: { field: 'tooltip_enabled', value: true } },
    { key: 'tooltip_border_width', label: t('Spessore bordo tooltip'), type: 'range', min: 0, max: 4, step: 1,
      condition: { field: 'tooltip_enabled', value: true } },
    withHover({ key: 'tooltip_corner_radius', label: t('Raggio angoli tooltip'), type: 'border-radius',
      condition: { field: 'tooltip_enabled', value: true } }),
    { key: 'tooltip_padding', label: t('Padding tooltip'), type: 'spacing', max: 20,
      condition: { field: 'tooltip_enabled', value: true } },

    // ── Stile dati (aspetto) ──
    { type: 'separator', label: t('Stile dati — aspetto') },
    { key: 'border_width', label: t('Spessore bordo dati'), type: 'range', min: 0, max: 6, step: 1 },
    { key: 'border_color_override', label: t('Colore bordo globale'), type: 'color' },
    withHover({ key: 'bar_radius', label: t('Raggio angoli barre'), type: 'border-radius',
      condition: { field: 'chart_type', value: 'bar' } }),
    { key: 'tension', label: t('Curvatura linea'), type: 'range', min: 0, max: 1, step: 0.05,
      condition: { field: 'chart_type', op: 'in', value: ['line', 'radar'] } },
    { key: 'point_radius', label: t('Raggio punti'), type: 'range', min: 0, max: 12, step: 1,
      condition: { field: 'chart_type', op: 'in', value: ['line', 'radar'] } },
    { key: 'point_hover_radius', label: t('Raggio punti (hover)'), type: 'range', min: 0, max: 16, step: 1,
      condition: { field: 'chart_type', op: 'in', value: ['line', 'radar'] } },
    { key: 'doughnut_cutout', label: t('Taglio ciambella (%)'), type: 'range', min: 10, max: 90, step: 5,
      condition: { field: 'chart_type', value: 'doughnut' } },

    // ── Griglia e assi (colori/dimensioni) ──
    { type: 'separator', label: t('Griglia e assi — colori') },
    { key: 'bg_color', label: t('Sfondo grafico'), type: 'color' },
    { key: 'grid_color', label: t('Colore griglia'), type: 'color' },
    { key: 'grid_line_width', label: t('Spessore griglia'), type: 'range', min: 0, max: 4, step: 0.5 },
    { key: 'axis_color', label: t('Colore bordo assi'), type: 'color' },

    ...shadowField,
    ...borderFields(),
  ],
};
