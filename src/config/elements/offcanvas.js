import { shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile OffCanvas — split CONTENUTO/STILE.
 *   fields[]      → trigger (visibility/testo/icona/selector esterno), posizione, transition, overlay toggle, close button toggle
 *   styleFields[] → dimensioni pannello, colori pannello/overlay/close, ombra, bordo
 */
export default {
  type: 'offcanvas',
  name: t('Off-Canvas'),
  icon: 'dashicons-slides',
  category: 'interactive',
  hasChildren: true,
  defaults: {
    trigger_selector: '',
    position: 'right',
    transition: 'slide',
    width: '300',
    height: '300',
    overlay: true,
    overlay_color: 'var(--olo-color-dark, #16263d)',
    overlay_opacity: '50',
    close_button: true,
    close_color: '',
    bg_color: '',
    text_color: '',
    trigger_text: 'Apri pannello',
    trigger_icon: 'menu',
    show_trigger: true,
    shadow: 'none',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  fields: [
    { key: 'show_trigger', label: t('Mostra pulsante trigger'), type: 'toggle' },
    { key: 'trigger_text', label: t('Testo trigger'), type: 'text',
      condition: { field: 'show_trigger', value: true } },
    { key: 'trigger_icon', label: t('Icona trigger'), type: 'icon',
      condition: { field: 'show_trigger', value: true } },
    { key: 'trigger_selector', label: t('Selettore CSS trigger esterno'), type: 'text' },

    { type: 'separator', label: t('Pannello — Comportamento') },
    { key: 'position', label: t('Posizione'), type: 'select', options: [
      { value: 'left', label: t('Sinistra') },
      { value: 'right', label: t('Destra') },
      { value: 'top', label: t('Alto') },
      { value: 'bottom', label: t('Basso') },
    ]},
    { key: 'transition', label: t('Transizione'), type: 'select', options: [
      { value: 'slide', label: t('Slide') },
      { value: 'push', label: t('Push') },
      { value: 'reveal', label: t('Reveal') },
    ]},

    { type: 'separator', label: t('Overlay & chiusura') },
    { key: 'overlay', label: t('Mostra overlay'), type: 'toggle' },
    { key: 'close_button', label: t('Pulsante chiudi'), type: 'toggle' },
  ],

  styleFields: [
    { type: 'separator', label: t('Pannello — Dimensione & colori') },
    { key: 'width', label: t('Larghezza (px)'), type: 'range', min: 200, max: 600, step: 10 },
    { key: 'height', label: t('Altezza (px)'), type: 'range', min: 200, max: 600, step: 10 },
    { key: 'bg_color', label: t('Sfondo pannello'), type: 'color' },
    { key: 'text_color', label: t('Colore testo'), type: 'color' },

    { type: 'separator', label: t('Overlay — Aspetto') },
    { key: 'overlay_color', label: t('Colore overlay'), type: 'color',
      condition: { field: 'overlay', value: true } },
    { key: 'overlay_opacity', label: t('Opacità overlay (%)'), type: 'range', min: 0, max: 100, step: 5,
      condition: { field: 'overlay', value: true } },

    { type: 'separator', label: t('Chiusura — Aspetto') },
    { key: 'close_color', label: t('Colore pulsante chiudi'), type: 'color',
      condition: { field: 'close_button', value: true } },

    ...shadowField,
    ...borderFields(),
  ],
};
