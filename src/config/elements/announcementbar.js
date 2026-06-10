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
    { key: 'text_color', label: t('Testo'), type: 'color' },
    { key: 'accent_color', label: t('Accento'), type: 'color' },

    { type: 'separator', label: t('Tipografia') },
    { key: 'font_size', label: t('Dim. testo (px)'), type: 'range', min: 9, max: 18, step: 1 },
    { key: 'font_weight', label: t('Peso'), type: 'select', options: [
      { value: '400', label: '400' }, { value: '500', label: '500' }, { value: '600', label: '600' }, { value: '700', label: '700' },
    ]},
    { key: 'letter_spacing', label: t('Spaziatura lettere (es. 0.2em)'), type: 'unit', units: ['em', 'px', 'rem'], min: 0, step: 0.05 },
    { key: 'text_transform', label: t('Trasformazione'), type: 'select', options: [
      { value: 'none', label: t('Nessuna') }, { value: 'uppercase', label: t('MAIUSCOLO') }, { value: 'lowercase', label: t('minuscolo') },
    ]},
    { key: 'alignment', label: t('Allineamento'), type: 'select', options: [
      { value: 'center', label: t('Centro') }, { value: 'left', label: t('Sinistra') }, { value: 'right', label: t('Destra') },
    ]},

    { type: 'separator', label: t('Spaziatura') },
    { key: 'tile_padding', label: t('Padding (px)'), type: 'spacing', max: 40 },

    { type: 'separator', label: t('Bordo inferiore') },
    { key: 'border_bottom', label: t('Spessore (px)'), type: 'range', min: 0, max: 4, step: 1 },
    { key: 'border_color', label: t('Colore bordo'), type: 'color' },

    { type: 'separator', label: t('Sfondo creativo') },
    { key: 'bg', label: t('Sfondo completo'), type: 'background', showParallax: false },
  ],
};
