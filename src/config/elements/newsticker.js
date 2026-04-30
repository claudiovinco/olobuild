import { textEffectsFields, textEffectsDefaults } from './_shared';
export default {
  type: 'newsticker',
  name: 'News Ticker',
  icon: 'dashicons-megaphone',
  category: 'dynamic',
  defaults: {
    items: [
      { id: 'nt-1', title: 'Nuova funzionalità disponibile per tutti gli utenti', url: '', badge: 'Novità' },
      { id: 'nt-2', title: 'Manutenzione programmata venerdì 21:00 - 23:00', url: '', badge: 'Avviso' },
      { id: 'nt-3', title: 'Aggiornamento versione 2.0 rilasciato con successo', url: '', badge: '' },
    ],
    label_text: 'Breaking',
    label_bg: '',
    label_color: '',
    bg_color: '',
    text_color: '',
    speed: '3000',
    height: '42',
    separator: '|',
    auto_scroll: true,
    pause_on_hover: true,
    ...textEffectsDefaults,
  },
  fields: [
    { key: 'items', label: 'Notizie', type: 'content-items',
      itemFields: [
        { key: 'title', label: 'Titolo', type: 'text' },
        { key: 'url', label: 'URL (opzionale)', type: 'text', placeholder: 'https://...' },
        { key: 'badge', label: 'Badge (opzionale)', type: 'text', placeholder: 'es. Novità' },
      ],
      newItemDefaults: { title: 'Nuova notizia', url: '', badge: '' },
      itemLabel: 'Notizia',
    },

    { type: 'separator', label: 'Etichetta' },
    { key: 'label_text', label: 'Testo etichetta', type: 'text' },
    { key: 'label_bg', label: 'Sfondo etichetta', type: 'color' },
    { key: 'label_color', label: 'Colore testo etichetta', type: 'color' },

    { type: 'separator', label: 'Aspetto' },
    { key: 'bg_color', label: 'Colore sfondo', type: 'color' },
    { key: 'text_color', label: 'Colore testo', type: 'color' },
    { key: 'height', label: 'Altezza (px)', type: 'range', min: 30, max: 60 },
    { key: 'separator', label: 'Separatore badge', type: 'text' },

    { type: 'separator', label: 'Animazione' },
    { key: 'speed', label: 'Intervallo (ms)', type: 'range', min: 2000, max: 8000, step: 500 },
    { key: 'auto_scroll', label: 'Scorrimento automatico', type: 'toggle' },
    { key: 'pause_on_hover', label: 'Pausa al passaggio mouse', type: 'toggle' },
    ...textEffectsFields([ { value: 'title', label: 'Solo Titolo' } ]),
  ],
};
