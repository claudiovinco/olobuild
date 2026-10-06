import { t } from '@/i18n';

/**
 * Condivisi famiglia OLOX (replica pixel-perfect olotheme.com).
 * Palette prodotto del design + campo select accent riusato da tutte le tile.
 */
// OLOtour è verde acqua (#12A19A in olox.css, il colore del marchio): l'etichetta diceva «ambra»
// e la tile usciva verde acqua — l'errore era nel nome, non nel colore.
export const OLOX_COLOR_OPTIONS = [
  { value: 'olo', label: t('OLOtheme (rosso)') },
  { value: 'build', label: t('OLObuild (rosso)') },
  { value: 'booking', label: t('OLObooking (blu)') },
  { value: 'lang', label: t('OLOlang (magenta)') },
  { value: 'secur', label: t('OLOsecurity (ciano)') },
  { value: 'tour', label: t('OLOtour (verde acqua)') },
  { value: 'tutor', label: t('OLOtutor (verde)') },
];

export const OLOX_COLORS = {
  olo: '#E8453D',
  build: '#E8453D',
  booking: '#3D8BFF',
  lang: '#E8409A',
  tour: '#12A19A',
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

/**
 * Colore della tile oltre i 7 dei prodotti: i ruoli del tema (il valore è già CSS, il renderer lo
 * passa da safe_color_css) e un colore libero (`accent_custom`, letto dal renderer quando
 * accent = 'custom'). Le chiavi dei prodotti restano quelle salvate.
 */
export const OLOX_THEME_COLOR_OPTIONS = [
  { value: 'var(--olo-color-primary, #e1474f)', label: t('Tema — primario') },
  { value: 'var(--olo-color-secondary, #16263d)', label: t('Tema — secondario') },
  { value: 'var(--olo-color-accent, #e1474f)', label: t('Tema — accento') },
  { value: 'custom', label: t('Personalizzato') },
];

export function oloxAccentFields(label) {
  return [
    { ...oloxAccentField(label || t('Colore')), options: [...OLOX_COLOR_OPTIONS, ...OLOX_THEME_COLOR_OPTIONS] },
    { key: 'accent_custom', label: t('Colore personalizzato'), type: 'color',
      condition: { field: 'accent', op: 'eq', value: 'custom' } },
  ];
}

/**
 * Testi che stanno direttamente sulla pagina (titolo della sezione, orario, fasi): col colore del
 * testo del tema, o chiari come nel design scuro di olotheme.com. Le card restano scure.
 */
export function oloxTestiPaginaField() {
  return {
    key: 'testi_pagina', label: t('Testi sulla pagina'), type: 'select',
    options: [
      { value: 'tema', label: t('Colore del testo del tema') },
      { value: 'chiari', label: t('Chiari (su sfondo scuro)') },
    ],
    description: t('Il titolo e le scritte fuori dalle card stanno sullo sfondo della pagina. «Colore del testo del tema» li rende leggibili sul tema del sito; «Chiari» è il design originale, per pagine o sezioni scure. Le card restano scure in entrambi i casi.'),
  };
}

/** Risolve una chiave palette o un colore custom in CSS. */
export function oloxColor(key) {
  if (OLOX_COLORS[key]) return `var(--${key}, ${OLOX_COLORS[key]})`;
  return key || OLOX_COLORS.olo;
}
