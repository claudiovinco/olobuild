import { textEffectsDefaults, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, withHover } from './_shared';
import { shadowField } from './_shared.js';
import { t, tPartenza } from '@/i18n';

/**
 * Tile Grafico — Chart.js su un canvas, una serie di dati (etichetta, valore, colore).
 *   fields[]      → tipo, dati, nome della serie · Titolo · Legenda e fumetto (mostrare, animazione) · Scala e assi
 *                   (da zero, minimo, massimo, passo, nomi degli assi) · Formato valori
 *   styleFields[] → Aspetto (stile, sfondo, colore dei bordi, ombra) · Testo (titolo, sottotitolo, assi, legenda,
 *                   fumetto) · Forma (altezza, barre, linea, punti, ciambella) · Griglia e assi · Legenda · Fumetto ·
 *                   Disposizione (orientamento) · Ogni dato (colore e bordo di ciascuno)
 * Ordinato il 16 nov 2026 (episodio 26 di «Una tile in un minuto»): griglia, larghezze, area, gradini e orientamento
 * stavano nel Contenuto; gli stili erano i dodici generici in inglese che il grafico quasi non leggeva.
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
      { id: 'c-1', label: t('Gen'), value: '65', color: 'var(--olo-color-primary, #e1474f)' },
      { id: 'c-2', label: 'Feb', value: '45', color: 'var(--olo-color-dark, #16263d)' },
      { id: 'c-3', label: tPartenza('Mar'), value: '80', color: 'var(--olo-color-accent, #f4a23b)' },
      { id: 'c-4', label: t('Apr'), value: '55', color: 'var(--olo-color-success, #15803d)' },
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
    tooltip_bg: 'var(--olo-color-dark, #16263d)',
    tooltip_text_color: 'var(--olo-color-light, #f8f9fa)',
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
    border_radius: 0,
    ...borderEffectDefaults,
  },

  // Come nasce dalla palette: le visite al sito mese per mese, barre arrotondate nel primario
  // del tema con il mese migliore pieno e gli altri in tinta, titolo, griglia orizzontale appena
  // accennata e il fumetto «1.240 visite». Griglia e testi coi token: i default PHP sono grigi fissi.
  partenza: {
    items: [
      { id: 'c-1', label: t('Gen'), value: '1240', color: 'color-mix(in srgb, var(--olo-color-primary) 40%, transparent)' },
      { id: 'c-2', label: t('Feb'), value: '1310', color: 'color-mix(in srgb, var(--olo-color-primary) 40%, transparent)' },
      { id: 'c-3', label: tPartenza('Mar'), value: '1680', color: 'color-mix(in srgb, var(--olo-color-primary) 40%, transparent)' },
      { id: 'c-4', label: t('Apr'), value: '1920', color: 'color-mix(in srgb, var(--olo-color-primary) 40%, transparent)' },
      { id: 'c-5', label: t('Mag'), value: '2250', color: 'color-mix(in srgb, var(--olo-color-primary) 40%, transparent)' },
      { id: 'c-6', label: t('Giu'), value: '2610', color: 'color-mix(in srgb, var(--olo-color-primary) 40%, transparent)' },
      { id: 'c-7', label: t('Lug'), value: '2890', color: 'var(--olo-color-primary)' },
      { id: 'c-8', label: t('Ago'), value: '2140', color: 'color-mix(in srgb, var(--olo-color-primary) 40%, transparent)' },
      { id: 'c-9', label: tPartenza('Set'), value: '2470', color: 'color-mix(in srgb, var(--olo-color-primary) 40%, transparent)' },
      { id: 'c-10', label: t('Ott'), value: '2330', color: 'color-mix(in srgb, var(--olo-color-primary) 40%, transparent)' },
      { id: 'c-11', label: t('Nov'), value: '1860', color: 'color-mix(in srgb, var(--olo-color-primary) 40%, transparent)' },
      { id: 'c-12', label: t('Dic'), value: '2050', color: 'color-mix(in srgb, var(--olo-color-primary) 40%, transparent)' },
    ],
    chart_height: '340',
    show_legend: false,
    show_title: true,
    chart_title: t('Visite al sito nel 2025'),
    title_color: 'var(--olo-color-text)',
    title_font_size: '17',
    title_padding: '18',
    bar_radius: '8',
    bar_percentage: '0.7',
    border_width: '0',
    show_x_grid: false,
    show_x_border: false,
    show_y_border: false,
    grid_color: 'color-mix(in srgb, var(--olo-color-text) 12%, transparent)',
    text_color: 'var(--olo-color-text-muted)',
    tick_font_size: '12',
    // Conflitto PHP/config: vince il config (riserve del tema scuro/chiaro).
    tooltip_bg: 'var(--olo-color-dark, #16263d)',
    tooltip_text_color: 'var(--olo-color-light, #f8f9fa)',
    tooltip_corner_radius: '8',
    tooltip_padding: '10',
    tooltip_suffix: ' ' + t('visite'),
    number_format: true,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  // 16 nov 2026 (episodio 26): ogni proprietà nella sua casa. Nel Contenuto ciò che il grafico dice (tipo, dati, nome
  // della serie, titoli, scala, nomi degli assi, formato dei numeri) e il comportamento (fumetto, animazione); la
  // griglia, le larghezze delle barre, l'area piena, i gradini, l'orientamento e l'impilato sono aspetto → Stile.
  fields: [
    { key: 'chart_type', label: t('Tipo grafico'), type: 'select', options: [
      { value: 'bar', label: t('Barre') },
      { value: 'line', label: t('Linea') },
      { value: 'pie', label: t('Torta') },
      { value: 'doughnut', label: t('Ciambella') },
      { value: 'radar', label: t('Radar') },
      { value: 'polarArea', label: t('Area polare') },
    ]},
    { key: 'items', label: t('Dati'), type: 'content-items', itemLabel: t('Dato'), etichettaDa: 'label',
      itemFields: [
        { key: 'label', label: t('Etichetta'), type: 'text' },
        { key: 'value', label: t('Valore'), type: 'number', step: 0.01 },
      ],
      newItemDefaults: { label: t('Nuovo'), value: '50', color: 'var(--olo-color-primary)', border_color: '' },
    },
    { key: 'dataset_label', label: t('Nome della serie'), type: 'text', placeholder: t('Mostrato nella legenda'),
      description: t('Il nome dei valori, che la legenda e il fumetto mostrano accanto al colore: per esempio «kWh prodotti».'),
      condition: { field: 'chart_type', op: 'in', value: ['bar', 'line', 'radar'] } },

    { type: 'separator', label: t('Titolo') },
    { key: 'show_title', label: t('Mostra titolo'), type: 'toggle' },
    { key: 'chart_title', label: t('Testo titolo'), type: 'text',
      condition: { field: 'show_title', value: true } },
    { key: 'show_subtitle', label: t('Mostra sottotitolo'), type: 'toggle' },
    { key: 'chart_subtitle', label: t('Testo sottotitolo'), type: 'text',
      condition: { field: 'show_subtitle', value: true } },

    { type: 'separator', label: t('Legenda e fumetto') },
    { key: 'show_legend', label: t('Mostra legenda'), type: 'toggle',
      description: t('Torte, ciambelle e aree polari elencano i dati coi loro colori. Barre, linee e radar mostrano il nome della serie: senza nome la legenda non compare.') },
    { key: 'tooltip_enabled', label: t('Fumetto al passaggio'), type: 'toggle',
      description: t('Passando sopra un dato (o toccandolo, sul telefono) un fumetto ne mostra il valore.') },
    { key: 'animate', label: t('Animazione'), type: 'toggle',
      description: t('Le barre crescono, le linee si disegnano quando il grafico entra nello schermo.') },

    { type: 'separator', label: t('Scala e assi') },
    { key: 'begin_at_zero', label: t('Inizia da zero'), type: 'toggle',
      condition: { field: 'chart_type', op: 'in', value: ['bar', 'line'] } },
    // Minimo, massimo e incremento valgono anche per la scala dei valori di radar e area
    // polare (prima il radar li ignorava).
    { key: 'y_min', label: t('Valore minimo'), type: 'number', placeholder: t('Auto'),
      condition: { field: 'chart_type', op: 'in', value: ['bar', 'line', 'radar', 'polarArea'] } },
    { key: 'y_max', label: t('Valore massimo'), type: 'number', placeholder: t('Auto'),
      condition: { field: 'chart_type', op: 'in', value: ['bar', 'line', 'radar', 'polarArea'] } },
    { key: 'y_step_size', label: t('Passo della scala'), type: 'number', placeholder: t('Auto'),
      condition: { field: 'chart_type', op: 'in', value: ['bar', 'line', 'radar', 'polarArea'] } },
    { key: 'x_label', label: t('Nome asse delle etichette'), type: 'text',
      condition: { field: 'chart_type', op: 'in', value: ['bar', 'line'] } },
    { key: 'y_label', label: t('Nome asse dei valori'), type: 'text',
      condition: { field: 'chart_type', op: 'in', value: ['bar', 'line'] } },

    { type: 'separator', label: t('Formato valori') },
    { key: 'tooltip_prefix', label: t('Prefisso valore'), type: 'text', placeholder: t('es. €') },
    { key: 'tooltip_suffix', label: t('Suffisso valore'), type: 'text', placeholder: t('es. %') },
    { key: 'number_format', label: t('Separatore migliaia'), type: 'toggle' },
  ],

  // ─── STILE ─────────────────────────────────────────────────
  // Zone dello standard: Aspetto · Testo · Forma · Griglia e assi · Legenda · Fumetto · Disposizione · Ogni dato.
  styleFields: [
    { type: 'separator', label: t('Aspetto') },
    { key: 'preset', label: t('Stile'), type: 'select', options: [
      { value: 'pulito',     label: t('Pulito') },
      { value: 'scheda',     label: t('Scheda') },
      { value: 'scuro',      label: t('Scuro') },
      { value: 'tinta',      label: t('Tinta') },
      { value: 'essenziale', label: t('Essenziale') },
      { value: 'pillole',    label: t('Pillole') },
      { value: 'custom',     label: t('Personalizzato') },
    ], description: t('Cambia insieme sfondo, griglia, testi, fumetto e forma delle barre, coi colori del sito. I colori dei dati restano i tuoi (in fondo, «Ogni dato»).') },
    { key: 'bg_color', label: t('Sfondo grafico'), type: 'color' },
    { key: 'border_color_override', label: t('Colore dei bordi'), type: 'color',
      description: t('Un colore solo per il bordo di tutti i dati. Vuoto: ogni dato ha il suo.') },
    ...shadowField,

    // Niente «Stile tipografico» né «Effetti testo»: il grafico è disegnato su un canvas,
    // dove né l'uno né gli altri possono agire (erano due controlli che non facevano niente).
    { type: 'separator', label: t('Testo') },
    { type: 'typography', label: t('Titolo'), responsiveKeys: [],
      keys: { size: 'title_font_size', weight: 'title_font_weight', color: 'title_color' },
      sizeMin: 10, sizeMax: 32, sizeStep: 1 },
    { type: 'typography', label: t('Sottotitolo'), responsiveKeys: [],
      keys: { size: 'subtitle_font_size', color: 'subtitle_color' },
      sizeMin: 8, sizeMax: 20, sizeStep: 1 },
    { type: 'typography', label: t('Assi'), responsiveKeys: [],
      keys: { size: 'tick_font_size', color: 'text_color' },
      sizeMin: 8, sizeMax: 16, sizeStep: 1 },
    { type: 'typography', label: t('Legenda'), responsiveKeys: [],
      keys: { size: 'legend_font_size', weight: 'legend_font_weight', color: 'legend_color' },
      sizeMin: 8, sizeMax: 20, sizeStep: 1 },
    { type: 'typography', label: t('Fumetto'), responsiveKeys: [],
      keys: { size: 'tooltip_font_size', color: 'tooltip_text_color' },
      sizeMin: 8, sizeMax: 18, sizeStep: 1 },

    { type: 'separator', label: t('Forma') },
    { key: 'chart_height', label: t('Altezza'), type: 'range', min: 200, max: 800, step: 10, unit: 'px' },
    { key: 'title_padding', label: t('Padding titolo'), type: 'spacing', max: 40,
      condition: { field: 'show_title', value: true } },
    { key: 'bar_radius', label: t('Raggio barre'), type: 'border-radius',
      condition: { field: 'chart_type', value: 'bar' } },
    { key: 'bar_percentage', label: t('Larghezza barre'), type: 'range', min: 0.1, max: 1, step: 0.05,
      description: t('Quanto della sua colonna occupa ogni barra: 1 = tutta.'),
      condition: { field: 'chart_type', value: 'bar' } },
    { key: 'category_percentage', label: t('Larghezza colonne'), type: 'range', min: 0.1, max: 1, step: 0.05,
      description: t('Quanto dello spazio di ogni etichetta occupa la sua colonna: più basso, più aria fra le barre.'),
      condition: { field: 'chart_type', value: 'bar' } },
    { key: 'fill_area', label: t('Riempi area'), type: 'toggle',
      condition: { field: 'chart_type', op: 'in', value: ['line', 'radar'] } },
    { key: 'stepped_line', label: t('Linea a gradini'), type: 'select', options: [
      { value: '', label: t('No') },
      { value: 'before', label: t('Prima') },
      { value: 'after', label: t('Dopo') },
      { value: 'middle', label: t('Centro') },
    ], condition: { field: 'chart_type', value: 'line' } },
    { key: 'tension', label: t('Curvatura linea'), type: 'range', min: 0, max: 1, step: 0.05,
      condition: { field: 'chart_type', op: 'in', value: ['line', 'radar'] } },
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
    { key: 'point_radius', label: t('Raggio punti'), type: 'range', min: 0, max: 12, step: 1, unit: 'px',
      condition: { field: 'chart_type', op: 'in', value: ['line', 'radar'] } },
    { key: 'point_hover_radius', label: t('Raggio punti al passaggio'), type: 'range', min: 0, max: 16, step: 1, unit: 'px',
      condition: { field: 'chart_type', op: 'in', value: ['line', 'radar'] } },
    { key: 'doughnut_cutout', label: t('Foro della ciambella'), type: 'range', min: 10, max: 90, step: 5, unit: '%',
      condition: { field: 'chart_type', value: 'doughnut' } },

    { type: 'separator', label: t('Griglia e assi'), condition: { field: 'chart_type', op: 'in', value: ['bar', 'line'] } },
    { key: 'show_x_grid', label: t('Griglia verticale'), type: 'toggle' },
    { key: 'show_y_grid', label: t('Griglia orizzontale'), type: 'toggle' },
    { key: 'grid_color', label: t('Colore griglia'), type: 'color' },
    { key: 'grid_line_width', label: t('Spessore griglia'), type: 'range', min: 0, max: 4, step: 0.5, unit: 'px' },
    { key: 'show_x_border', label: t('Linea dell’asse orizzontale'), type: 'toggle' },
    { key: 'show_y_border', label: t('Linea dell’asse verticale'), type: 'toggle' },
    { key: 'axis_color', label: t('Colore linee degli assi'), type: 'color' },

    { type: 'separator', label: t('Legenda'), condition: { field: 'show_legend', value: true } },
    { key: 'legend_position', label: t('Posizione'), type: 'select', options: [
      { value: 'top', label: t('Alto') },
      { value: 'bottom', label: t('Basso') },
      { value: 'left', label: t('Sinistra') },
      { value: 'right', label: t('Destra') },
    ] },
    { key: 'legend_align', label: t('Allineamento'), type: 'select', options: [
      { value: 'start', label: t('Inizio') },
      { value: 'center', label: t('Centro') },
      { value: 'end', label: t('Fine') },
    ] },
    { key: 'legend_point_style', label: t('Indicatore circolare'), type: 'toggle' },
    { key: 'legend_box_width', label: t('Larghezza indicatore'), type: 'range', min: 10, max: 60, step: 2, unit: 'px',
      condition: { field: 'legend_point_style', op: 'neq', value: true } },
    // Chart.js ha un solo numero (lo spazio attorno a ogni voce): il renderer legge il lato alto; coi lati
    // collegati, com'è di partenza, se ne cambia uno e cambiano tutti.
    { key: 'legend_padding', label: t('Padding'), type: 'spacing', max: 30,
      description: t('Lo spazio attorno a ogni voce della legenda.') },

    { type: 'separator', label: t('Fumetto'), condition: { field: 'tooltip_enabled', op: 'neq', value: false } },
    { key: 'tooltip_bg', label: t('Sfondo fumetto'), type: 'color' },
    { key: 'tooltip_border', label: t('Bordo fumetto'), type: 'border',
      legacyKeys: { width: 'tooltip_border_width', color: 'tooltip_border_color' } },
    { key: 'tooltip_corner_radius', label: t('Raggio fumetto'), type: 'border-radius' },
    { key: 'tooltip_padding', label: t('Padding fumetto'), type: 'spacing', max: 20 },

    // «Impilato (stacked)» tolto dall'inspector: il grafico ha una serie sola (i Dati), e una serie sola impilata è
    // identica a quella affiancata. La chiave resta letta dal renderer.
    { type: 'separator', label: t('Disposizione'), condition: { field: 'chart_type', value: 'bar' } },
    { key: 'index_axis', label: t('Orientamento'), type: 'select', options: [
      { value: 'x', label: t('Barre verticali') },
      { value: 'y', label: t('Barre orizzontali') },
    ] },

    { type: 'separator', label: t('Ogni dato') },
    { key: 'items', type: 'content-items', label: t('Dati'), itemLabel: t('Dato'), etichettaDa: 'label', itemFields: [
        { key: 'color', label: t('Colore'), type: 'color' },
        { key: 'border', label: t('Bordo'), type: 'border',
          legacyKeys: { width: 'border_width', color: 'border_color' } },
    ] },

    // bordo, raggio ed effetti sul riquadro del grafico: dalla 1.4.506 il selettore .{uid} c'è
    ...borderFields({ raggio: withHover({ key: 'border_radius', label: t('Raggio'), type: 'border-radius' }) }),
  ],
};
