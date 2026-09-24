// Contratto tra la shell della Configurazione (SettingsApp.vue) e le schede.
//
// «Salva impostazioni» e «Annulla modifiche» lanciano 'olo-cfg-save' /
// 'olo-cfg-discard' con detail = { tabs, jobs }: `tabs` sono gli id delle SOLE
// schede modificate. Ogni scheda il cui id è nell'elenco aggiunge a `jobs` la
// propria FUNZIONE (non una richiesta già partita): la shell le esegue in fila,
// ne raccoglie l'esito e spegne il puntino solo di chi è riuscito.
import { t } from '@/i18n';

/**
 * Attende la risposta di una fetch e lancia un errore se non è ok.
 * fetch non rifiuta su 403/500: senza questo controllo un salvataggio fallito
 * (nonce scaduto, permessi, errore del server) sembrava riuscito.
 * Il messaggio porta lo stato HTTP, che la shell mostra accanto alla scheda.
 */
export async function okOrThrow(resOrPromise) {
  const res = await resOrPromise;
  if (res.ok) return res;
  let detail = '';
  try {
    const body = await res.clone().json();
    if (body && body.code === 'rest_cookie_invalid_nonce') detail = t('sessione scaduta');
    else if (body && body.message) detail = String(body.message);
  } catch (e) { /* corpo non JSON */ }
  throw new Error(String(res.status) + (detail ? ' ' + detail : ''));
}

/**
 * Listener per gli eventi della shell: iscrive `fn` solo se la scheda `id`
 * è fra quelle da trattare. Una scheda solo visitata non rimanda più niente.
 */
export function cfgJob(id, fn) {
  return (e) => {
    const d = e && e.detail;
    if (d && Array.isArray(d.tabs) && Array.isArray(d.jobs) && d.tabs.includes(id)) {
      d.jobs.push({ id, run: fn });
    }
  };
}

/**
 * Una scheda la cui lettura dal server non è mai riuscita mostra i valori
 * scritti nel codice: salvarla li spedirebbe al posto di quelli veri.
 */
export function assertLoaded(loaded) {
  if (!loaded.value) throw new Error(t('dati non caricati, ricarica la pagina'));
}

/**
 * Valori da mostrare dopo una lettura: i default della scheda coperti da quelli
 * del server. Si riparte SEMPRE dai default: un Object.assign sui valori a video
 * lasciava le chiavi che il server non restituisce (option mai salvata = [],
 * chiavi aggiunte dopo) e «Annulla modifiche» non le annullava.
 */
export function conIniziali(iniziale, data) {
  const dalServer = data && typeof data === 'object' && !Array.isArray(data) ? data : {};
  return { ...iniziale, ...dalServer };
}

/**
 * «Annulla modifiche»: rilegge la scheda dal server. `load` mette
 * loaded.value = true solo se la lettura riesce; se fallisce la scheda resta
 * «da salvare» (le modifiche sono ancora a video) e loaded torna com'era.
 */
export function reloadJob(loaded, load) {
  return async () => {
    const before = loaded.value;
    loaded.value = false;
    let ok = false;
    try {
      await load();
      ok = loaded.value;
    } finally {
      if (!ok) loaded.value = before;
    }
    if (!ok) throw new Error(t('rilettura non riuscita'));
  };
}
