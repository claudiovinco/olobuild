/**
 * Shared field presets for element definitions.
 * Elements can spread these into their fields arrays.
 */

export const linkFields = [
  { key: 'link_url', label: 'URL link', type: 'text' },
  { key: 'link_target', label: 'Apri in', type: 'select', options: [
    { value: '_self', label: 'Stessa finestra' },
    { value: '_blank', label: 'Nuova scheda' },
  ]},
];

export const targetField = {
  key: 'target', label: 'Apri in', type: 'select', options: [
    { value: '_self', label: 'Stessa finestra' },
    { value: '_blank', label: 'Nuova scheda' },
  ],
};

export const alignmentField = {
  key: 'alignment', label: 'Allineamento', type: 'select', options: [
    { value: 'left', label: 'Sinistra' },
    { value: 'center', label: 'Centro' },
    { value: 'right', label: 'Destra' },
  ],
};

export const columnWidthOptions = [
  { value: '', label: 'Auto' },
  { value: '1-1', label: '1/1' },
  { value: '1-2', label: '1/2' },
  { value: '1-3', label: '1/3' },
  { value: '2-3', label: '2/3' },
  { value: '1-4', label: '1/4' },
  { value: '3-4', label: '3/4' },
  { value: '1-5', label: '1/5' },
  { value: '2-5', label: '2/5' },
  { value: '3-5', label: '3/5' },
  { value: '4-5', label: '4/5' },
  { value: '1-6', label: '1/6' },
  { value: '5-6', label: '5/6' },
];
