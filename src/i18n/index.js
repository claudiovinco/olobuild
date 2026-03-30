/**
 * Olobuild i18n — runtime translation helper.
 *
 * The default UI language is Italian. This module provides a simple t(key)
 * function that returns the English (or other locale) translation when the
 * site locale is not Italian.
 *
 * Usage:
 *   import { t } from '@/i18n';
 *   const label = t('Spaziatura interna'); // => "Padding" on en_US sites
 *
 * The locale is determined from:
 *   1. window.oloData.locale  (set by PHP via wp_localize_script)
 *   2. document.documentElement.lang  (HTML lang attribute)
 *   3. Falls back to 'it' (Italian — no translation needed)
 */

import en from './en_US.json';
import fr from './fr_FR.json';
import de from './de_DE.json';
import es from './es_ES.json';
import pt from './pt_BR.json';
import nl from './nl_NL.json';
import pl from './pl_PL.json';
import ja from './ja.json';

const translations = {
  // English
  en_US: en, en_GB: en, en_AU: en, en_CA: en, en_NZ: en, en_ZA: en,
  // French
  fr_FR: fr, fr_BE: fr, fr_CA: fr,
  // German
  de_DE: de, de_AT: de, de_CH: de,
  // Spanish
  es_ES: es, es_MX: es, es_AR: es, es_CO: es, es_CL: es, es_PE: es, es_VE: es,
  // Portuguese
  pt_BR: pt, pt_PT: pt,
  // Dutch
  nl_NL: nl, nl_BE: nl,
  // Polish
  pl_PL: pl,
  // Japanese
  ja: ja,
};

/**
 * Detect the current locale.
 * Returns a normalized locale string like 'en_US', 'it_IT', etc.
 */
function detectLocale() {
  // 1. Check oloData (set by PHP wp_localize_script)
  if (typeof window !== 'undefined' && window.oloData && window.oloData.locale) {
    return window.oloData.locale.replace('-', '_');
  }

  // 2. Check HTML lang attribute
  if (typeof document !== 'undefined' && document.documentElement.lang) {
    return document.documentElement.lang.replace('-', '_');
  }

  // 3. Default to Italian (no translation needed)
  return 'it';
}

const locale = detectLocale();

// Find the best matching dictionary:
// Try exact match first (en_US), then language prefix (en)
const dict = translations[locale]
  || translations[locale.split('_')[0] + '_US']
  || translations[locale.split('_')[0]]
  || {};

/**
 * Translate a string.
 * Returns the translated string if found, otherwise returns the key unchanged.
 *
 * @param {string} key - The Italian string to translate
 * @returns {string} The translated string or the original key
 */
export function t(key) {
  if (!key || typeof key !== 'string') return key;
  // Skip keys that start with _ (meta/comment keys in the JSON)
  return dict[key] || key;
}

/**
 * Check if the current locale needs translation (i.e., is not Italian).
 * @returns {boolean}
 */
export function needsTranslation() {
  return !locale.startsWith('it');
}

/**
 * Get the current detected locale.
 * @returns {string}
 */
export function getLocale() {
  return locale;
}
