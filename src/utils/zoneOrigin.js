/**
 * Provenienza di header e footer nel builder unificato: quale template si sta
 * modificando, da dove arriva sulla pagina (oloData.resolvedZones, risolto dal
 * PHP come sul sito: meta della pagina → regole → globale) e se altre pagine lo
 * usano. Unica fonte per il chip del canvas, il badge dell'inspector e l'avviso
 * alla prima modifica: le tre cose dicono sempre la stessa frase.
 *
 * Mai un'etichetta fissa: «globale» solo se lo è davvero, «di questa pagina» mai
 * (lo stesso header si assegna a più pagine), e se la provenienza non è nota si
 * dice solo «Header».
 */
import { t } from '@/i18n';

/**
 * @param {object} builderStore  store del builder (headerTemplate, footerTemplate, zoneOrigin)
 * @param {'header'|'footer'} zone
 * @returns {null|{ zone: string, id: number, label: string, title: string, notes: string[], text: string, shared: boolean }}
 *   null = nessuna zona caricata (niente chip, niente avviso).
 */
export function zoneOrigin(builderStore, zone) {
  if (!builderStore || (zone !== 'header' && zone !== 'footer')) return null;
  const tpl = zone === 'footer' ? builderStore.footerTemplate : builderStore.headerTemplate;
  if (!tpl) return null;

  const isHeader = zone === 'header';
  const o = (builderStore.zoneOrigin && builderStore.zoneOrigin[zone]) || {};
  const olo = (typeof window !== 'undefined' && window.oloData) || {};
  const globalId = parseInt(isHeader ? olo.activeHeaderId : olo.activeFooterId, 10) || 0;
  const id = parseInt(tpl.id, 10) || 0;
  const pages = parseInt(o.pages, 10) || 0;
  const isGlobal = id > 0 && id === globalId;
  // Assegnato alla pagina, ma lo dà anche una regola di visualizzazione ad altre.
  const byRule = o.source === 'page' && o.rules === true;

  let label;
  if (o.source === 'global') {
    label = isHeader ? t('Header del sito (globale)') : t('Footer del sito (globale)');
  } else if (o.source === 'rule') {
    label = isHeader ? t('Header da regola di visualizzazione') : t('Footer da regola di visualizzazione');
  } else if (o.source === 'page') {
    label = isHeader ? t('Header assegnato a questa pagina') : t('Footer assegnato a questa pagina');
  } else {
    label = isHeader ? t('Header') : t('Footer');
  }

  const notes = [];
  // Assegnato alla pagina ma è anche il globale, o assegnato anche ad altre pagine.
  if (o.source !== 'global' && isGlobal) {
    notes.push(isHeader ? t("è anche l'header globale") : t('è anche il footer globale'));
  } else if (o.source === 'page' && pages > 2) {
    notes.push(t('anche su altre %d pagine').replace('%d', String(pages - 1)));
  } else if (o.source === 'page' && pages === 2) {
    notes.push(t("anche su un'altra pagina"));
  }
  if (byRule) {
    notes.push(t('usato anche da una regola di visualizzazione'));
  }
  // Il sito rende solo i template pubblicati (render_header/render_footer).
  if (tpl.status && tpl.status !== 'published') {
    notes.push(t('in bozza: il sito non lo mostra'));
  }

  const title = typeof tpl.title === 'string' ? tpl.title.trim() : '';
  const text = label + (title ? ' «' + title + '»' : '') + (notes.length ? ' · ' + notes.join(' · ') : '');
  // Condivisa = una modifica cambia anche altre pagine. Una regola può valere per
  // molte pagine (o per chi guarda): si tratta sempre come condivisa.
  const shared = o.source === 'global' || o.source === 'rule' || isGlobal || byRule || (o.source === 'page' && pages > 1);

  return { zone, id, label, title, notes, text, shared };
}
