
import { borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile MobileBar — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → sorgenti dati (logo image, logo link, menu WP), toggle visibilità (ricerca, separatori), placeholder ricerca, breakpoint
 *   styleFields[] → aspetto barra/hamburger/pannello (sfondo, colori, dimensioni, padding, ombra, bordo)
 *   AVANZATE      → meta tecnico (id/class/condizioni)
 */
export default {
  type: 'mobilebar',
  name: t('Mobile Bar'),
  icon: 'dashicons-smartphone',
  category: 'navigation',

  defaults: {
    breakpoint:          '1024',
    logo_image:          '',
    logo_width:          '120',
    logo_link:           '',
    bar_bg:              '',
    bar_height:          '56',
    bar_shadow:          true,
    tile_padding: { top: 12, right: 12, bottom: 12, left: 12 },
    hamburger_style:     'classic',
    hamburger_size:      '28',
    hamburger_color:     '#ffffff',
    menu_id:             '',
    panel_bg:            '#ffffff',
    panel_text_color:    '#222222',
    panel_active_color:  '',
    panel_font_size:     '17',
    panel_item_padding:  '16',
    panel_separator:     true,
    panel_chevron_color: '',
    search_enabled:      true,
    search_icon_color:   '#ffffff',
    search_placeholder:  'Cerca...',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    // ── Generale ──
    { type: 'separator', label: t('Generale') },
    { key: 'breakpoint', label: t('Breakpoint visibilità (px)'), type: 'range', min: 480, max: 1400, step: 10 },

    // ── Logo ──
    { type: 'separator', label: t('Logo') },
    { key: 'logo_image', label: t('Immagine logo'), type: 'image' },
    { key: 'logo_link', label: t('Link logo (vuoto = home)'), type: 'link' },

    // ── Menu ──
    { type: 'separator', label: t('Menu') },
    { key: 'menu_id', label: t('Menu WordPress'), type: 'select', optionsSource: 'wpMenus' },

    // ── Pannello (toggle) ──
    { type: 'separator', label: t('Pannello menu') },
    { key: 'panel_separator', label: t('Separatore tra voci'), type: 'toggle' },

    // ── Ricerca ──
    { type: 'separator', label: t('Ricerca') },
    { key: 'search_enabled', label: t('Mostra icona ricerca'), type: 'toggle' },
    { key: 'search_placeholder', label: t('Placeholder ricerca'), type: 'text',
      condition: { field: 'search_enabled', value: true } },
  ],

  // ─── STILE ─────────────────────────────────────────────────
  styleFields: [
    // ── Logo ──
    { type: 'separator', label: t('Logo') },
    { key: 'logo_width', label: t('Larghezza logo (px)'), type: 'range', min: 40, max: 250, step: 5 },

    // ── Barra ──
    { type: 'separator', label: t('Barra') },
    { key: 'bar_bg', label: t('Sfondo barra'), type: 'color' },
    { key: 'bar_height', label: t('Altezza barra (px)'), type: 'range', min: 40, max: 80, step: 2 },
    { key: 'bar_shadow', label: t('Ombra barra'), type: 'toggle' },
    { key: 'tile_padding', label: t('Padding (px)'), type: 'spacing', max: 30 },

    // ── Hamburger ──
    { type: 'separator', label: t('Hamburger') },
    { key: 'hamburger_style', label: t('Stile'), type: 'select', options: [
      { value: 'classic', label: t('Classic (3 linee → X)') },
      { value: 'squeeze', label: t('Squeeze (comprime → X)') },
      { value: 'arrow', label: t('Arrow (→ freccia)') },
      { value: 'dot-grid', label: t('Dot Grid (9 pallini)') },
      { value: 'minimal', label: t('Minimal (2 linee)') },
    ]},
    { key: 'hamburger_size', label: t('Dimensione (px)'), type: 'range', min: 20, max: 44, step: 2 },
    { key: 'hamburger_color', label: t('Colore'), type: 'color' },

    // ── Tipografia ──
    { type: 'separator', label: t('Tipografia') },
    { type: 'typography', label: t('Pannello'),
      responsiveKeys: ['size'],
      keys: {
        size:       'panel_font_size',
        color:      'panel_text_color',
        colorHover: 'panel_active_color',
      },
      sizeMin: 14, sizeMax: 24, sizeStep: 1,
    },

    // ── Pannello Menu ──
    { type: 'separator', label: t('Pannello menu') },
    { key: 'panel_bg', label: t('Sfondo pannello'), type: 'color' },
    { key: 'panel_item_padding', label: t('Padding voci (px)'), type: 'spacing', max: 30 },
    { key: 'panel_chevron_color', label: t('Colore chevron'), type: 'color' },

    // ── Ricerca ──
    { type: 'separator', label: t('Ricerca') },
    { key: 'search_icon_color', label: t('Colore icona ricerca'), type: 'color',
      condition: { field: 'search_enabled', value: true } },
    ...borderFields(),
  ],
};
