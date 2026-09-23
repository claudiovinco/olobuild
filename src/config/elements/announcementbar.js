import { t } from '@/i18n';

/**
 * Announcement Bar — striscia annuncio full-width in cima al sito (sopra la nav).
 * Testo centrato + parte evidenziata (accento), link opzionale, chiudibile opzionale
 * (con memoria localStorage). Estratta dal blueprint OLOthemes Atelier Noir (.an-ann).
 * Va tipicamente nell'header (sopra il megamenu). Render Vue == PHP (AnnouncementBarTile.vue).
 */
export default {
  type: 'announcementbar',
  name: t('Barra Annuncio'),
  icon: 'dashicons-megaphone',
  category: 'marketing',

  defaults: {
    typography_preset: '',
    text: 'Complimentary shipping & returns worldwide · ',
    accent_text: 'The Nocturne collection has arrived',
    link_url: '',
    dismissible: false,
    bg_color: '',
    text_color: '',
    accent_color: '',
    font_size: '11',
    font_weight: '500',
    letter_spacing: '0.2em',
    text_transform: 'uppercase',
    alignment: 'center',
    tile_padding: { top: 10, right: 20, bottom: 10, left: 20 },
    border_bottom: '0',
    border_color: '',
    bg: { type: 'none' },
  },

  fields: [
    { key: 'text', label: t('Testo'), type: 'text' },
    { key: 'accent_text', label: t('Testo evidenziato (accento)'), type: 'text' },
    { key: 'link_url', label: t('Link (opzionale, rende cliccabile la barra)'), type: 'link' },
    { key: 'dismissible', label: t('Chiudibile (X + memoria)'), type: 'toggle' },
  ],

  styleFields: [
    { type: 'separator', label: t('Colori') },
    { key: 'bg_color', label: t('Sfondo'), type: 'color' },
    { key: 'accent_color', label: t('Accento'), type: 'color' },

    { type: 'separator', label: t('Tipografia') },
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },
    { type: 'typography', label: t('Testo'),
      responsiveKeys: [],
      letterSpacingUnit: 'em',
      keys: {
        size:          'font_size',
        weight:        'font_weight',
        transform:     'text_transform',
        letterSpacing: 'letter_spacing',
        color:         'text_color',
      },
      sizeMin: 9, sizeMax: 18, sizeStep: 1,
    },
    { key: 'alignment', label: t('Allineamento'), type: 'select', options: [
      { value: 'center', label: t('Centro') }, { value: 'left', label: t('Sinistra') }, { value: 'right', label: t('Destra') },
    ]},

    { type: 'separator', label: t('Spaziatura') },
    { key: 'tile_padding', label: t('Padding'), type: 'spacing', max: 40 },

    { type: 'separator', label: t('Bordo inferiore') },
    { key: 'border_bottom', label: t('Spessore'), type: 'range', min: 0, max: 4, step: 1 },
    { key: 'border_color', label: t('Colore bordo'), type: 'color' },

    { type: 'separator', label: t('Sfondo creativo') },
    { key: 'bg', label: t('Sfondo completo'), type: 'background', showParallax: false },
  ],
};
