// Passaggio al caricamento delle impostazioni salvate in chiavi che l'inspector non mostra più.
//
// Una tile può dichiarare nel suo config `migraAlCaricamento(settings)`: quando il builder apre la pagina
// (preparaContenuto in stores/tiles.js) la funzione sposta i valori vecchi nei campi che l'utente vede e
// svuota le chiavi vecchie, che il renderer PHP usava come ripiego. Senza questo passaggio il campo
// dell'inspector si mostrava vuoto (o col predefinito) mentre la pagina disegnava il valore vecchio, e
// cambiare il campo a volte faceva ricomparire quel valore «dal nulla». La pagina resta identica: il PHP
// legge per primo il campo nuovo, e finché la pagina non si salva conta ancora la chiave vecchia.
import { getElementDef } from '@/config/elementRegistry';

export function migraImpostazioni(nodi, profondita = 0) {
  if (!Array.isArray(nodi) || profondita > 20) return;
  for (const n of nodi) {
    if (!n || typeof n !== 'object') continue;
    const def = n.type ? getElementDef(n.type) : null;
    if (def && typeof def.migraAlCaricamento === 'function' && n.settings && typeof n.settings === 'object' && !Array.isArray(n.settings)) {
      try { def.migraAlCaricamento(n.settings); } catch (e) { /* una migrazione che fallisce non blocca l'apertura */ }
    }
    if (Array.isArray(n.children)) migraImpostazioni(n.children, profondita + 1);
  }
}
