
import { borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile Room Gallery — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → comportamento (lightbox/autoplay/kenburns/transition), controlli (frecce/dots/counter)
 *   styleFields[] → sfondo, tipografia, layout (colonne/altezze), bordo
 */
export default {
  type: 'olo_room_gallery',
  name: t('Sala - Galleria'),
  icon: 'dashicons-format-gallery',
  category: 'olo-space',
  defaults: {
    bg: { type: 'none' },
    typography_preset: '',
    columns: 5,
    lightbox: true,
    main_height: 450,
    kenburns: true,
    autoplay: true,
    show_counter: true,
    show_arrows: true,
    show_dots: true,
    thumb_height: 80,
    transition: 'kenburns',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { type: 'separator', label: t('Comportamento') },
    { key: 'lightbox', label: t('Apri in lightbox al click'), type: 'toggle' },
    { key: 'autoplay', label: t('Avanzamento automatico'), type: 'toggle' },
    { key: 'kenburns', label: t('Effetto Ken Burns'), type: 'toggle' },
    { key: 'transition', label: t('Transizione'), type: 'select', options: [
      { value: 'kenburns', label: t('Ken Burns') },
      { value: 'slide', label: t('Slide') },
      { value: 'fade', label: t('Dissolvenza') },
    ]},

    { type: 'separator', label: t('Controlli') },
    { key: 'show_arrows', label: t('Mostra frecce navigazione'), type: 'toggle' },
    { key: 'show_dots', label: t('Mostra indicatori (dots)'), type: 'toggle' },
    { key: 'show_counter', label: t('Mostra contatore immagini'), type: 'toggle' },
  ],

  // ─── STILE ─────────────────────────────────────────────────
  styleFields: [
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },
    { type: 'separator', label: t('Layout') },
    { key: 'columns', label: t('Colonne miniature'), type: 'range', min: 2, max: 8, step: 1 },
    { key: 'main_height', label: t('Altezza immagine principale (px)'), type: 'range', min: 200, max: 800, step: 10 },
    { key: 'thumb_height', label: t('Altezza miniature (px)'), type: 'range', min: 40, max: 150, step: 5 },
    ...borderFields(),
  ],
};
