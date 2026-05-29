import { flexContainerFields, flexContainerDefaults } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile Row — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → layout strutturale (n. colonne) + Loop post (sorgente dati semantica)
 *   styleFields[] → sfondo, gap, allineamento, stacking, flex/grid controls
 *   AVANZATE      → meta tecnico
 */
export default {
  type: 'row',
  name: t('Riga / Colonne'),
  icon: 'dashicons-columns',
  category: 'layout',
  defaults: {
    bg: { type: 'none' },
    layout: '50-50',
    gap: '16',
    column_gap: 'default',
    vertical_align: 'stretch',
    stack_mobile: true,
    stack_tablet: false,
    grid_column_gap: '',
    grid_row_gap: '',
    grid_auto_flow: 'row',
    grid_auto_flow_dense: false,
    grid_justify_content: 'stretch',
    grid_align_items: 'stretch',
    grid_align_content: 'stretch',
    loop_enabled: false,
    loop_post_type: 'post',
    loop_posts_per_page: '6',
    loop_orderby: 'date',
    loop_order: 'DESC',
    loop_taxonomy: '',
    loop_terms: '',
    loop_terms_exclude: '',
    loop_offset: '0',
    loop_exclude_current: true,
    loop_meta_key: '',
    loop_meta_value: '',
    loop_meta_compare: '=',
    loop_pagination: 'none',
    loop_pagination_align: 'center',
    loop_load_more_label: 'Carica altri',
    ...flexContainerDefaults,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { key: 'layout', label: t('Layout colonne'), type: 'select', options: [
      { value: '100', label: '100' },
      { value: '50-50', label: t('50 / 50') },
      { value: '33-33-33', label: t('33 / 33 / 33') },
      { value: '25-50-25', label: t('25 / 50 / 25') },
      { value: '25-25-25-25', label: t('25 / 25 / 25 / 25') },
      { value: '66-33', label: t('66 / 33') },
      { value: '33-66', label: t('33 / 66') },
      { value: 'custom', label: t('Personalizzato (%)') },
    ]},
    { key: 'custom_widths', label: t('Larghezze personalizzate'), type: 'text', placeholder: t('es: 20,30,50'), condition: { field: 'layout', value: 'custom' } },

    // ── Loop Post (sorgente dati semantica) ──
    { key: '_loop_sep', label: t('Loop'), type: 'separator' },
    { key: 'loop_enabled', label: t('Loop Element'), type: 'toggle' },
    { type: 'description', description: t('Quando attivo, la PRIMA colonna della riga viene usata come template del singolo card e ripetuta per ogni post. Il layout della riga (es. 33-33-33) gestisce la disposizione: con 6 post + layout 33-33-33 ottieni 2 righe da 3 card.'),
      condition: { field: 'loop_enabled', value: true } },
    { key: 'loop_post_type', label: t('Tipo contenuto'), type: 'select', optionsSource: 'postTypes', options: [],
      condition: { field: 'loop_enabled', value: true } },
    { key: 'loop_posts_per_page', label: t('Numero di post'), type: 'range', min: 1, max: 50, step: 1,
      condition: { field: 'loop_enabled', value: true } },
    { key: 'loop_orderby', label: t('Ordina per'), type: 'select', options: [
      { value: 'date', label: t('Data') },
      { value: 'title', label: t('Titolo') },
      { value: 'modified', label: t('Data modifica') },
      { value: 'rand', label: t('Casuale') },
      { value: 'menu_order', label: t('Ordine menu') },
      { value: 'comment_count', label: t('N. commenti') },
      { value: 'meta_value_num', label: t('Meta value (numerico)') },
    ], condition: { field: 'loop_enabled', value: true } },
    { key: 'loop_order', label: t('Direzione'), type: 'select', options: [
      { value: 'DESC', label: t('Decrescente') },
      { value: 'ASC', label: t('Crescente') },
    ], condition: { field: 'loop_enabled', value: true } },
    { key: 'loop_taxonomy', label: t('Tassonomia filtro'), type: 'text', placeholder: t('category, post_tag...'),
      condition: { field: 'loop_enabled', value: true } },
    { key: 'loop_terms', label: t('Includi termini (slug, virgola)'), type: 'text', placeholder: t('news, tutorial'),
      condition: { field: 'loop_enabled', value: true } },
    { key: 'loop_terms_exclude', label: t('Escludi termini (slug, virgola)'), type: 'text', placeholder: t('bozze, privati'),
      condition: { field: 'loop_enabled', value: true } },
    { key: 'loop_offset', label: t('Offset'), type: 'range', min: 0, max: 50, step: 1,
      condition: { field: 'loop_enabled', value: true } },
    { key: 'loop_exclude_current', label: t('Escludi post corrente'), type: 'toggle',
      condition: { field: 'loop_enabled', value: true } },
    { key: 'loop_meta_key', label: t('Meta key filtro'), type: 'text', placeholder: t('prezzo, colore...'),
      condition: { field: 'loop_enabled', value: true } },
    { key: 'loop_meta_value', label: t('Meta value'), type: 'text',
      condition: { field: 'loop_enabled', value: true } },
    { key: 'loop_meta_compare', label: t('Operatore meta'), type: 'select', options: [
      { value: '=', label: t('= Uguale') },
      { value: '!=', label: t('!= Diverso') },
      { value: '>', label: t('> Maggiore') },
      { value: '<', label: t('< Minore') },
      { value: 'LIKE', label: t('LIKE (contiene)') },
      { value: 'EXISTS', label: t('EXISTS') },
      { value: 'NOT EXISTS', label: t('NOT EXISTS') },
    ], condition: { field: 'loop_enabled', value: true } },

    { key: 'loop_pagination', label: t('Paginazione'), type: 'select', options: [
      { value: 'none',      label: t('Nessuna') },
      { value: 'numbers',   label: t('Numerica (link)') },
      { value: 'load_more', label: t('Carica altri (bottone)') },
    ], condition: { field: 'loop_enabled', value: true } },
    { key: 'loop_pagination_align', label: t('Allineamento'), type: 'select', options: [
      { value: 'left',   label: t('Sinistra') },
      { value: 'center', label: t('Centro') },
      { value: 'right',  label: t('Destra') },
    ], condition: { field: 'loop_pagination', op: 'neq', value: 'none' } },
    { key: 'loop_load_more_label', label: t('Etichetta bottone'), type: 'text', placeholder: t('Carica altri'),
      condition: { field: 'loop_pagination', value: 'load_more' } },
  ],

  // ─── STILE ─────────────────────────────────────────────────
  styleFields: [
    { type: 'separator', label: t('Sfondo & spaziatura') },
    { key: 'gap', label: t('Gap (px)'), type: 'range', min: 0, max: 48, step: 4,
      show: (s) => {
        const fcg = parseInt(s.flex_column_gap || 0);
        const frg = parseInt(s.flex_row_gap || 0);
        return !(fcg > 0 || frg > 0);
      }
    },
    { key: 'vertical_align', label: t('Allineamento verticale'), type: 'select', options: [
      { value: 'stretch', label: t('Stretch') },
      { value: 'start', label: t('Alto') },
      { value: 'center', label: t('Centro') },
      { value: 'end', label: t('Basso') },
    ]},

    { type: 'separator', label: t('Responsive') },
    { key: 'stack_mobile', label: t('Impila su mobile'), type: 'toggle' },
    { key: 'stack_tablet', label: t('Impila su tablet'), type: 'toggle' },

    { key: '_grid_sep', label: t('Controlli CSS Grid'), type: 'separator', condition: { field: 'layout_mode', value: 'grid' } },
    { key: 'grid_column_gap', label: t('Gap Orizzontale (px)'), type: 'range', min: 0, max: 60, step: 2, condition: { field: 'layout_mode', value: 'grid' } },
    { key: 'grid_row_gap', label: t('Gap Verticale (px)'), type: 'range', min: 0, max: 60, step: 2, condition: { field: 'layout_mode', value: 'grid' } },
    { key: 'grid_auto_flow', label: t('Direzione Grid'), type: 'icon-select', options: [
      { value: 'row', label: t('Riga'), icon: 'arrow-right' },
      { value: 'column', label: t('Colonna'), icon: 'arrow-down' },
    ], condition: { field: 'layout_mode', value: 'grid' } },
    { key: 'grid_auto_flow_dense', label: t('Densità (dense)'), type: 'toggle', condition: { field: 'layout_mode', value: 'grid' } },
    { key: 'grid_justify_content', label: t('Justify Content'), type: 'icon-select', options: [
      { value: 'start', label: t('Start'), icon: 'align-left' },
      { value: 'center', label: t('Center'), icon: 'align-center' },
      { value: 'end', label: t('End'), icon: 'align-right' },
      { value: 'stretch', label: t('Stretch'), icon: 'align-justify' },
      { value: 'space-between', label: t('Space Between'), icon: 'space-between' },
      { value: 'space-around', label: t('Space Around'), icon: 'space-around' },
      { value: 'space-evenly', label: t('Space Evenly'), icon: 'space-evenly' },
    ], condition: { field: 'layout_mode', value: 'grid' } },
    { key: 'grid_align_items', label: t('Align Items'), type: 'icon-select', options: [
      { value: 'start', label: t('Start'), icon: 'align-top' },
      { value: 'center', label: t('Center'), icon: 'align-middle' },
      { value: 'end', label: t('End'), icon: 'align-bottom' },
      { value: 'stretch', label: t('Stretch'), icon: 'align-stretch-v' },
      { value: 'baseline', label: t('Baseline'), icon: 'align-baseline' },
    ], condition: { field: 'layout_mode', value: 'grid' } },
    { key: 'grid_align_content', label: t('Align Content'), type: 'icon-select', options: [
      { value: 'start', label: t('Start'), icon: 'align-top' },
      { value: 'center', label: t('Center'), icon: 'align-middle' },
      { value: 'end', label: t('End'), icon: 'align-bottom' },
      { value: 'stretch', label: t('Stretch'), icon: 'align-stretch-v' },
      { value: 'space-between', label: t('Space Between'), icon: 'space-between-v' },
      { value: 'space-around', label: t('Space Around'), icon: 'space-around-v' },
    ], condition: { field: 'layout_mode', value: 'grid' } },

    ...flexContainerFields,
  ],
};
