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
    text: t('Spedizione gratuita sopra i 50 € · '),
    accent_text: t('Scopri la nuova collezione'),
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

  // Appena nata: una striscia nel colore primario del sito con un avviso vero (spedizioni e
  // novità), la parte in evidenza piena e il resto appena velato, in maiuscolo spaziato.
  partenza: {
    text: t('Spedizione gratuita sopra i 50 € · '),
    accent_text: t('Scopri la nuova collezione'),
    bg_color: 'var(--olo-color-primary)',
    text_color: 'color-mix(in srgb, var(--olo-color-primary-contrast) 78%, transparent)',
    accent_color: 'var(--olo-color-primary-contrast)',
    font_size: '12',
    font_weight: '600',
    letter_spacing: '0.16em',
    tile_padding: { top: 12, right: 20, bottom: 12, left: 20 },
  },

  fields: [
    // 16 nov 2026 (episodio 38): etichette senza parentesi, le spiegazioni nelle (i); lo Stile nelle zone Aspetto,
    // Testo, Forma, Disposizione; i testi di riserva in italiano (erano quelli inglesi del blueprint).
    { key: 'text', label: t('Testo'), type: 'text' },
    { key: 'accent_text', label: t('Parte evidenziata'), type: 'text',
      description: t('Le parole dopo il testo, nel colore d’accento: la novità, l’offerta, la data.') },
    { key: 'link_url', label: t('Link'), type: 'link',
      description: t('Facoltativo: con un link tutta la barra si può cliccare.') },
    { key: 'dismissible', label: t('Si può chiudere'), type: 'toggle',
      description: t('Una × a destra: chi la chiude non la rivede su quel dispositivo, finché il testo non cambia.') },
  ],

  styleFields: [
    { type: 'separator', label: t('Aspetto') },
    { key: 'bg_color', label: t('Colore di fondo'), type: 'color' },
    { key: 'accent_color', label: t('Colore accento'), type: 'color',
      description: t('Il colore della parte evidenziata. Vuoto: il primario del sito.') },
    { key: 'bg', label: t('Sfondo'), type: 'background', showParallax: false },

    { type: 'separator', label: t('Testo') },
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

    { type: 'separator', label: t('Forma') },
    { key: 'tile_padding', label: t('Padding'), type: 'spacing', max: 40 },
    { key: 'border_bottom', label: t('Filo sotto'), type: 'range', min: 0, max: 4, step: 1, unit: 'px',
      description: t('Una riga sottile lungo il bordo inferiore, per staccare la barra dal menu. 0: nessuna.') },
    { key: 'border_color', label: t('Colore del filo'), type: 'color',
      condition: { field: 'border_bottom', op: 'gt', value: 0 } },

    { type: 'separator', label: t('Disposizione') },
    { key: 'alignment', label: t('Allineamento'), type: 'select', options: [
      { value: 'left', label: t('Sinistra') }, { value: 'center', label: t('Centro') }, { value: 'right', label: t('Destra') },
    ]},
  ],
};
