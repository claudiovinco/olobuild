import { t } from '@/i18n';

/**
 * Condivisi famiglia OLOX (replica pixel-perfect olotheme.com).
 * Palette prodotto del design + campo select accent riusato da tutte le tile.
 */
export const OLOX_COLOR_OPTIONS = [
  { value: 'olo', label: t('OLOtheme (rosso)') },
  { value: 'build', label: t('OLObuild (rosso)') },
  { value: 'booking', label: t('OLObooking (blu)') },
  { value: 'lang', label: t('OLOlang (magenta)') },
  { value: 'secur', label: t('OLOsecurity (ciano)') },
  { value: 'tour', label: t('OLOtour (ambra)') },
  { value: 'tutor', label: t('OLOtutor (verde)') },
];

export const OLOX_COLORS = {
  olo: '#E8453D',
  build: '#E8453D',
  booking: '#3D8BFF',
  lang: '#E8409A',
  tour: '#F5A623',
  tutor: '#38C172',
  secur: '#26B8E8',
};

export function oloxAccentField(label) {
  return {
    key: 'accent',
    label: label || t('Colore prodotto'),
    type: 'select',
    options: OLOX_COLOR_OPTIONS,
  };
}

/** Risolve una chiave palette o un colore custom in CSS. */
export function oloxColor(key) {
  if (OLOX_COLORS[key]) return `var(--${key}, ${OLOX_COLORS[key]})`;
  return key || OLOX_COLORS.olo;
}
