/**
 * Olobuild i18n — runtime translation helper.
 *
 * Sistema dinamico: le traduzioni arrivano da PHP via
 * `window.oloData.translations` (popolato leggendo le stringhe tradotte
 * gestite dal plugin Olo Lang o da WordPress).
 *
 * Nessun dizionario hardcoded nel codice: aggiungere una lingua =
 * installarla in WordPress / Olo Lang e tradurre le stringhe via pannello.
 *
 * Usage:
 *   import { t } from '@/i18n';
 *   const label = t('Testo italiano');
 */

function getTranslations() {
  if (typeof window === 'undefined') return {};
  if (window.oloData && window.oloData.translations) {
    return window.oloData.translations;
  }
  return {};
}

function getLocaleInternal() {
  if (typeof window !== 'undefined' && window.oloData && window.oloData.locale) {
    return String(window.oloData.locale).replace('-', '_');
  }
  if (typeof document !== 'undefined' && document.documentElement.lang) {
    return document.documentElement.lang.replace('-', '_');
  }
  return 'it_IT';
}

const dict = getTranslations();
const locale = getLocaleInternal();

export function t(key) {
  if (!key || typeof key !== 'string') return key;
  return dict[key] || key;
}

export function needsTranslation() {
  return !locale.startsWith('it');
}

export function getLocale() {
  return locale;
}
