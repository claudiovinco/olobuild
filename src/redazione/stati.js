// I nomi degli stati di WordPress nella Redazione: per il segno sulla riga e, al plurale, per i filtri dell'elenco.
import { t } from '@/i18n';

export function nomeStato(stato, plurale = false) {
  const nomi = plurale
    ? { publish: t('Pubblicati'), future: t('Programmati'), draft: t('Bozze'), pending: t('In revisione'), private: t('Privati') }
    : { publish: t('Pubblicato'), future: t('Programmato'), draft: t('Bozza'), pending: t('In revisione'), private: t('Privato') };
  return nomi[stato] || stato;
}
