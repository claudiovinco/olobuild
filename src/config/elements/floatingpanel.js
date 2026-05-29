import { borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, withHover } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile FloatingPanel — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → posizionamento, dimensioni, layout figli, trigger/visibilità, chiusura, animazione, responsive
 *   styleFields[] → aspetto pannello (sfondo, padding, radius, ombra, bordo) e stile trigger (sfondo/colore/radius/ombra)
 *   AVANZATE      → meta tecnico (id/class/condizioni)
 */
export default {
  type: 'floatingpanel',
  name: t('Pannello flottante'),
  icon: 'dashicons-move',
  category: 'interactive',
  isContainer: true,
  defaults: {
    position: 'fixed',
    placement: 'bottom-right',
    offset_x: '20',
    offset_y: '20',
    custom_top: '',
    custom_left: '',
    custom_bottom: '',
    custom_right: '',
    width: '300',
    height: '',
    z_index: '9999',
    bg_color: '#ffffff',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
    border_radius: '12',
    shadow: true,
    shadow_color: 'rgba(0,0,0,0.15)',
    shadow_blur: '20',
    shadow_y: '4',
    tile_padding: { top: 20, right: 20, bottom: 20, left: 20 },
    trigger_mode: 'always',
    trigger_icon: 'plus',
    trigger_size: '48',
    trigger_bg: '',
    trigger_color: '#ffffff',
    trigger_radius: '50',
    trigger_shadow: true,
    show_close: true,
    close_color: '#666666',
    close_size: '20',
    close_outside: true,
    animation: 'fade',
    animation_duration: '300',
    visible_desktop: true,
    visible_tablet: true,
    visible_mobile: true,
    layout_direction: 'column',
    layout_gap: '12',
    layout_align: 'stretch',
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    // ─── Posizionamento ───
    { type: 'separator', label: t('Posizionamento') },
    {
      key: 'position',
      label: t('Tipo posizione'),
      type: 'select',
      options: [
        { value: 'fixed', label: t('Fisso (viewport)') },
        { value: 'absolute', label: t('Assoluto (pagina)') },
        { value: 'sticky', label: t('Appiccicoso') },
      ],
    },
    {
      key: 'placement',
      label: t('Posizione'),
      type: 'select',
      options: [
        { value: 'top-left', label: t('↖ Alto sinistra') },
        { value: 'top-center', label: t('↑ Alto centro') },
        { value: 'top-right', label: t('↗ Alto destra') },
        { value: 'center-left', label: t('← Centro sinistra') },
        { value: 'center', label: t('● Centro') },
        { value: 'center-right', label: t('→ Centro destra') },
        { value: 'bottom-left', label: t('↙ Basso sinistra') },
        { value: 'bottom-center', label: t('↓ Basso centro') },
        { value: 'bottom-right', label: t('↘ Basso destra') },
        { value: 'custom', label: t('✎ Personalizzato') },
      ],
    },
    { key: 'offset_x', label: t('Offset orizzontale (px)'), type: 'range', min: 0, max: 200, step: 5,
      condition: { field: 'placement', value: 'custom', operator: '!=' } },
    { key: 'offset_y', label: t('Offset verticale (px)'), type: 'range', min: 0, max: 200, step: 5,
      condition: { field: 'placement', value: 'custom', operator: '!=' } },
    { key: 'custom_top', label: t('Top (px o %)'), type: 'text',
      condition: { field: 'placement', value: 'custom' } },
    { key: 'custom_left', label: t('Left (px o %)'), type: 'text',
      condition: { field: 'placement', value: 'custom' } },
    { key: 'custom_bottom', label: t('Bottom (px o %)'), type: 'text',
      condition: { field: 'placement', value: 'custom' } },
    { key: 'custom_right', label: t('Right (px o %)'), type: 'text',
      condition: { field: 'placement', value: 'custom' } },

    { type: 'separator', label: t('Dimensioni') },
    { key: 'width', label: t('Larghezza (px o %)'), type: 'text' },
    { key: 'height', label: t('Altezza (vuoto = auto)'), type: 'text' },
    { key: 'z_index', label: t('Z-Index'), type: 'range', min: 1, max: 99999, step: 1 },

    // ─── Layout figli ───
    { type: 'separator', label: t('Layout contenuto') },
    {
      key: 'layout_direction',
      label: t('Direzione'),
      type: 'select',
      options: [
        { value: 'column', label: t('Verticale') },
        { value: 'row', label: t('Orizzontale') },
      ],
    },
    { key: 'layout_gap', label: t('Spaziatura figli (px)'), type: 'range', min: 0, max: 40, step: 2 },
    {
      key: 'layout_align',
      label: t('Allineamento figli'),
      type: 'select',
      options: [
        { value: 'stretch', label: t('Estendi') },
        { value: 'flex-start', label: t('Inizio') },
        { value: 'center', label: t('Centro') },
        { value: 'flex-end', label: t('Fine') },
      ],
    },

    // ─── Trigger ───
    { type: 'separator', label: t('Visibilità') },
    {
      key: 'trigger_mode',
      label: t('Modalità'),
      type: 'select',
      options: [
        { value: 'always', label: t('Sempre visibile') },
        { value: 'button', label: t('Con pulsante trigger') },
      ],
    },
    {
      key: 'trigger_icon',
      label: t('Icona trigger'),
      type: 'select',
      options: [
        { value: 'plus', label: t('+ Plus') },
        { value: 'chat', label: t('💬 Chat') },
        { value: 'info', label: t('ℹ Info') },
        { value: 'menu', label: t('☰ Menu') },
        { value: 'arrow-up', label: t('↑ Freccia su') },
        { value: 'star', label: t('★ Stella') },
        { value: 'heart', label: t('♥ Cuore') },
        { value: 'settings', label: t('⚙ Impostazioni') },
      ],
      condition: { field: 'trigger_mode', value: 'button' },
    },

    // ─── Chiusura ───
    { type: 'separator', label: t('Chiusura') },
    { key: 'show_close', label: t('Mostra pulsante chiudi'), type: 'toggle',
      condition: { field: 'trigger_mode', value: 'button' } },
    { key: 'close_outside', label: t('Chiudi cliccando fuori'), type: 'toggle',
      condition: { field: 'trigger_mode', value: 'button' } },

    // ─── Animazione ───
    { type: 'separator', label: t('Animazione') },
    {
      key: 'animation',
      label: t('Animazione apertura'),
      type: 'select',
      options: [
        { value: 'fade', label: t('Dissolvenza') },
        { value: 'slide-up', label: t('Scorrimento dal basso') },
        { value: 'slide-down', label: "Scorrimento dall'alto" },
        { value: 'slide-left', label: t('Scorrimento da destra') },
        { value: 'slide-right', label: t('Scorrimento da sinistra') },
        { value: 'scale', label: t('Scala') },
      ],
      condition: { field: 'trigger_mode', value: 'button' },
    },

    // ─── Responsive ───
    { type: 'separator', label: t('Responsive') },
    { key: 'visible_desktop', label: t('Visibile su desktop'), type: 'toggle' },
    { key: 'visible_tablet', label: t('Visibile su tablet'), type: 'toggle' },
    { key: 'visible_mobile', label: t('Visibile su mobile'), type: 'toggle' },
  ],

  // ─── STILE ─────────────────────────────────────────────────
  styleFields: [
    // ─── Aspetto ───
    { type: 'separator', label: t('Aspetto') },
    { key: 'bg_color', label: t('Sfondo'), type: 'color' },
    withHover({ key: 'border_radius', label: t('Arrotondamento (px)'), type: 'border-radius' }),
    { key: 'tile_padding', label: t('Padding (px)'), type: 'spacing', max: 60 },
    { key: 'shadow', label: t('Ombra'), type: 'toggle' },
    { key: 'shadow_color', label: t('Colore ombra'), type: 'color',
      condition: { field: 'shadow', value: true } },
    { key: 'shadow_blur', label: t('Sfocatura ombra (px)'), type: 'range', min: 0, max: 60, step: 2,
      condition: { field: 'shadow', value: true } },
    { key: 'shadow_y', label: t('Offset Y ombra (px)'), type: 'range', min: 0, max: 30, step: 1,
      condition: { field: 'shadow', value: true } },

    // ─── Stile trigger ───
    { type: 'separator', label: t('Stile pulsante trigger') },
    { key: 'trigger_size', label: t('Dimensione trigger (px)'), type: 'range', min: 32, max: 80, step: 2,
      condition: { field: 'trigger_mode', value: 'button' } },
    { key: 'trigger_bg', label: t('Sfondo trigger'), type: 'color',
      condition: { field: 'trigger_mode', value: 'button' } },
    { key: 'trigger_color', label: t('Colore icona trigger'), type: 'color',
      condition: { field: 'trigger_mode', value: 'button' } },
    { key: 'trigger_radius', label: t('Arrotondamento trigger (%)'), type: 'range', min: 0, max: 50, step: 5,
      condition: { field: 'trigger_mode', value: 'button' } },
    { key: 'trigger_shadow', label: t('Ombra trigger'), type: 'toggle',
      condition: { field: 'trigger_mode', value: 'button' } },

    // ─── Stile chiusura ───
    { type: 'separator', label: t('Stile pulsante chiudi') },
    { key: 'close_color', label: t('Colore X'), type: 'color',
      condition: { field: 'show_close', value: true } },
    { key: 'close_size', label: t('Dimensione X (px)'), type: 'range', min: 12, max: 32, step: 2,
      condition: { field: 'show_close', value: true } },

    // ─── Durata animazione ───
    { type: 'separator', label: t('Durata animazione') },
    { key: 'animation_duration', label: t('Durata (ms)'), type: 'range', min: 100, max: 800, step: 50,
      condition: { field: 'trigger_mode', value: 'button' } },

    ...borderFields(),
  ],
};
