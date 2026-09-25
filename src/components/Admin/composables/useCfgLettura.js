// Stato della PRIMA lettura di una scheda della Configurazione: 'loading' | 'ready' | 'error'.
//
// Prima ogni scheda mostrava i suoi valori scritti nel codice finché la risposta non
// arrivava (Instrument Serif in Tipografia, «Non configurato» in AI, «off» in
// Manutenzione…): quello che l'utente cambiava nel frattempo la risposta lo cancellava,
// e se la lettura falliva (403, 429, rete) non compariva niente fino al «Salva».
// Ora la shell (SettingsApp.vue) al posto della scheda mostra lo scheletro finché la
// lettura non riesce, e se fallisce una card con il motivo e «Riprova».
//
// Si appoggia a ciò che le schede hanno già (lotto 2, cfgSave.js): `loaded` diventa
// true solo se la lettura riesce, e assertLoaded resta l'ultima difesa del salvataggio.
// «Annulla modifiche» (reloadJob) e la rilettura dopo un salvataggio (salvaERileggi) non
// passano di qui: la scheda resta visibile con le sue modifiche, come prima.
import { ref, inject, getCurrentInstance } from 'vue';
import { rinnovaNonce, nonceScaduto } from '@/utils/restNonce';

/**
 * @param {string}   id     id della scheda (TAB_ID, lo stesso della shell)
 * @param {Ref}      loaded il `loaded` della scheda
 * @param {Function} load   la sua funzione di lettura, chiamata SENZA argomenti
 */
export function useCfgLettura(id, loaded, load) {
  const stato = ref('loading');
  const motivo = ref('');   // 'nonce' | 'permessi' | 'troppe' | 'server' | 'rete' | 'risposta'
  const codice = ref(0);    // stato HTTP della prima risposta fallita
  let inCorso = false;
  let primo = null;

  /**
   * fetch della lettura principale: restituisce SEMPRE la risposta (il flusso della
   * scheda non cambia) e, durante leggi(), ricorda il primo fallimento per dirne il motivo.
   */
  async function leggiFetch(url, opts) {
    let res;
    try {
      res = await fetch(url, opts);
    } catch (e) {
      if (inCorso && !primo) primo = { motivo: 'rete', codice: 0 };
      throw e;
    }
    if (!res.ok && inCorso && !primo) {
      let m = 'server';
      if (await nonceScaduto(res)) m = 'nonce';
      else if (res.status === 401 || res.status === 403) m = 'permessi';
      else if (res.status === 429) m = 'troppe';
      primo = { motivo: m, codice: res.status };
    }
    return res;
  }

  async function leggi() {
    if (inCorso) return;
    inCorso = true;
    primo = null;
    stato.value = 'loading';
    try {
      await load();
    } catch (e) {
      /* la scheda resta non caricata: lo dice lo stato qui sotto */
    } finally {
      inCorso = false;
    }
    if (loaded.value) {
      stato.value = 'ready';
      motivo.value = '';
      codice.value = 0;
    } else {
      stato.value = 'error';
      motivo.value = primo ? primo.motivo : 'risposta';
      codice.value = primo ? primo.codice : 0;
    }
  }

  // Sessione scaduta: prima un nonce nuovo (nessuna ricarica, le modifiche delle
  // altre schede restano), poi la lettura.
  async function riprova() {
    if (motivo.value === 'nonce') await rinnovaNonce();
    return leggi();
  }

  const api = { stato, motivo, codice, riprova };
  const registra = getCurrentInstance() ? inject('cfgLettura', null) : null;
  if (typeof registra === 'function') registra(id, api);

  return { stato, motivo, codice, fetch: leggiFetch, leggi, riprova };
}
