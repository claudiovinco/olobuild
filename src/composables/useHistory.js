import { ref, watch, toRaw } from 'vue';
import { useTilesStore } from '@/stores/tiles';
import { useBuilderStore } from '@/stores/builder';

// Shared singleton state
const undoStack = ref([]);
const redoStack = ref([]);
const maxHistory = 100;
let isProgrammatic = false;
// { h, b, f, s, o } — una stringa JSON per zona (header/body/footer) e, in s, titolo e
// impostazioni della pagina (sfondo, larghezza, effetti: parteImpostazioni).
// o = ordine delle chiavi di settings, di sistema comprese (ordineChiavi): non conta
// nel confronto fra passi, serve solo a ricomporre le impostazioni come erano.
let lastSnapshot = null;
let initialized = false;
let debounceTimer = null;
const DEBOUNCE_MS = 400;
// Template aperto all'ultima fotografia: se diventa un altro oggetto (caricamento,
// risposta di un salvataggio) il cambio non è un gesto dell'utente.
let ultimoTpl = null;
// Movimenti della cronologia (passo aggiunto, annulla, ripeti, nuovo punto zero):
// dice all'«Annulla» del toast se dopo il suo gesto è successo altro.
let passi = 0;

// Chiavi di settings scritte dal sistema, non dall'utente: fuori dalla cronologia.
// post_id = pagina WordPress collegata, la crea il server al primo salvataggio: un
// annulla che la togliesse farebbe creare al salvataggio dopo un'altra pagina bozza.
// preview_post_id lo legge solo l'anteprima.
const CHIAVI_DI_SISTEMA = ['post_id', 'preview_post_id'];

/**
 * Parte «pagina» della fotografia: titolo e impostazioni (settings senza le chiavi
 * di sistema, ordine invariato). Lo stato (Pubblica/Ritira) resta fuori: non si
 * annulla. '' se non c'è un template aperto.
 */
function parteImpostazioni(tpl) {
  if (!tpl) return '';
  const src = tpl.settings && typeof tpl.settings === 'object' && !Array.isArray(tpl.settings)
    ? tpl.settings : {};
  const settings = {};
  Object.keys(src).forEach((k) => {
    if (!CHIAVI_DI_SISTEMA.includes(k)) settings[k] = src[k];
  });
  return JSON.stringify({ title: tpl.title || '', settings });
}

/**
 * Ordine delle chiavi di settings, di sistema comprese. Il «da salvare» confronta
 * stringhe JSON: un ripeti che rimette una chiave tolta deve rimetterla al suo posto,
 * anche rispetto al post_id che il server accoda al primo salvataggio.
 */
function ordineChiavi(tpl) {
  const src = tpl && tpl.settings;
  return src && typeof src === 'object' && !Array.isArray(src) ? Object.keys(src) : [];
}

export function useHistory() {
  const tilesStore = useTilesStore();
  const builderStore = useBuilderStore();

  /**
   * Snapshot per-zona {h, b, f}: copre l'editing unificato header/body/footer
   * (prima era solo canvasTiles: un delete nell'header non era annullabile).
   * s = titolo e impostazioni della pagina: senza, ⌘Z dopo un cambio di sfondo
   * annullava l'ultima modifica a una tile.
   * Le zone invariate riusano la STRINGA del passo precedente, così 100 step
   * di history non duplicano header/footer quando cambia solo il body.
   */
  function snapshot() {
    const h = JSON.stringify(tilesStore.headerTiles);
    const b = JSON.stringify(tilesStore.canvasTiles);
    const f = JSON.stringify(tilesStore.footerTiles);
    const s = parteImpostazioni(builderStore.currentTemplate);
    const o = ordineChiavi(builderStore.currentTemplate);
    if (lastSnapshot) {
      return {
        h: h === lastSnapshot.h ? lastSnapshot.h : h,
        b: b === lastSnapshot.b ? lastSnapshot.b : b,
        f: f === lastSnapshot.f ? lastSnapshot.f : f,
        s: s === lastSnapshot.s ? lastSnapshot.s : s,
        o,
      };
    }
    return { h, b, f, s, o };
  }

  function sameSnapshot(a, b) {
    return !!a && !!b && a.h === b.h && a.b === b.b && a.f === b.f && a.s === b.s;
  }

  /**
   * Riporta titolo e impostazioni della pagina dalla fotografia `da` alla `a`.
   * Si scrivono solo le chiavi diverse fra le due: restano come sono le chiavi di
   * sistema e lo stato. Le chiavi tornano nell'ordine della fotografia di arrivo
   * (`ordine`, di sistema comprese; in coda quelle arrivate dopo, come il post_id
   * del primo salvataggio): il «da salvare» confronta stringhe, e un annulla o un
   * ripeti che torna al salvato deve mostrare «Salvato».
   */
  function applicaImpostazioni(da, a, ordine) {
    const tpl = builderStore.currentTemplate;
    if (!tpl) return;
    const prima = JSON.parse(da);
    const dopo = JSON.parse(a);
    if (prima.title !== dopo.title) tpl.title = dopo.title;
    const cur = tpl.settings && typeof tpl.settings === 'object' && !Array.isArray(tpl.settings)
      ? tpl.settings : {};
    const out = Object.assign({}, cur);
    const chiavi = new Set(Object.keys(prima.settings).concat(Object.keys(dopo.settings)));
    chiavi.forEach((k) => {
      if (JSON.stringify(prima.settings[k]) === JSON.stringify(dopo.settings[k])) return;
      if (Object.prototype.hasOwnProperty.call(dopo.settings, k)) out[k] = dopo.settings[k];
      else delete out[k];
    });
    const ordinate = {};
    (Array.isArray(ordine) ? ordine : []).forEach((k) => {
      if (Object.prototype.hasOwnProperty.call(out, k)) ordinate[k] = out[k];
    });
    Object.keys(out).forEach((k) => {
      if (!Object.prototype.hasOwnProperty.call(ordinate, k)) ordinate[k] = out[k];
    });
    tpl.settings = ordinate;
    builderStore.isDirty = true;
    // Lo sfondo pagina ha anche uno stile dal vivo nell'anteprima (olo:set-page-bg,
    // con !important): il render completo non lo sostituisce, va rimandato. Oggetto
    // semplice: postMessage non clona i proxy reattivi. {} = nessuno sfondo.
    const bgPrima = JSON.stringify(prima.settings.page_bg || {});
    const bgDopo = JSON.stringify(dopo.settings.page_bg || {});
    if (bgPrima !== bgDopo && typeof window !== 'undefined'
      && typeof window.__oloBridgePostToIframe === 'function') {
      try {
        window.__oloBridgePostToIframe('olo:set-page-bg', { page_bg: JSON.parse(bgDopo) });
      } catch (e) {
        console.warn('[Olobuild] olo:set-page-bg dopo annulla/ripeti:', e);
      }
    }
  }

  /**
   * La selezione non resta su nodi che l'annulla o il ripeti hanno tolto (Ctrl+Z di un
   * inserimento): l'inspector si chiudeva da sé, ma Canc diceva «Elemento eliminato»
   * senza eliminare niente. Primaria sparita (o già azzerata dal getter selectedTile)
   * = niente selezione; altrimenti escono dalla multi-selezione solo gli id spariti.
   * Un ripeti rimette il nodo con lo stesso id, non la selezione.
   */
  function potaSelezione() {
    const primaria = builderStore.selectedTileId;
    const ids = Array.isArray(builderStore.selectedTileIds) ? builderStore.selectedTileIds : [];
    if (!primaria && !ids.length) return;
    if (!primaria || !tilesStore.getTileById(primaria)) {
      builderStore.deselectTile();
      return;
    }
    const vivi = ids.filter((id) => tilesStore.getTileById(id));
    if (vivi.length !== ids.length) builderStore.selectedTileIds = vivi;
  }

  function restore(state) {
    isProgrammatic = true;
    passi++;
    try {
      const prev = lastSnapshot;
      if (!prev || state.b !== prev.b) {
        tilesStore.canvasTiles = JSON.parse(state.b);
        builderStore.isDirty = true;
      }
      if (!prev || state.h !== prev.h) {
        tilesStore.headerTiles = JSON.parse(state.h);
        builderStore.headerDirty = true;
      }
      if (!prev || state.f !== prev.f) {
        tilesStore.footerTiles = JSON.parse(state.f);
        builderStore.footerDirty = true;
      }
      // '' = fotografia senza template aperto: niente da riportare.
      if (prev && prev.s && state.s && state.s !== prev.s) {
        applicaImpostazioni(prev.s, state.s, state.o);
      }
      // Header/footer non hanno un deep watch dedicato nell'iframe bridge:
      // il bump forza il re-render del live preview per tutte le zone.
      tilesStore._bumpVersion();
      // Dopo il bump (la cache degli indici troverebbe ancora i nodi staccati).
      potaSelezione();
      // La fotografia è ciò che c'è davvero: se le impostazioni differiscono da
      // quelle del passo (ordine delle chiavi), il ⌘Z dopo non crea un passo fantasma.
      const tpl = builderStore.currentTemplate;
      const s = parteImpostazioni(tpl);
      lastSnapshot = Object.assign({}, state, { s: s === state.s ? state.s : s, o: ordineChiavi(tpl) });
      // Un annulla che torna alla versione salvata spegne il «da salvare».
      builderStore.reconcileDirty(state);
    } catch (e) {
      console.error('[Olobuild] Undo/Redo restore failed:', e);
    } finally {
      setTimeout(() => { isProgrammatic = false; }, 50);
    }
  }

  /**
   * Commit sincrono dello stato corrente (bypassa/azzera il debounce).
   * Usato dal DnD e dalla rimozione tile per garantire un punto di undo
   * atomico prima della mutazione, e da undo/redo come flush.
   */
  function pushStateNow() {
    if (isProgrammatic) return;
    if (debounceTimer) {
      clearTimeout(debounceTimer);
      debounceTimer = null;
    }
    const current = snapshot();
    // Ogni zona è «da salvare» se è diversa dalla sua versione salvata, qualunque
    // punto l'abbia modificata (anche chi ha segnato la zona sbagliata).
    builderStore.reconcileDirty(current);
    if (sameSnapshot(current, lastSnapshot)) return;
    if (lastSnapshot !== null) {
      undoStack.value.push(lastSnapshot);
      if (undoStack.value.length > maxHistory) undoStack.value.shift();
      redoStack.value = [];
    }
    lastSnapshot = current;
    passi++;
  }

  // Alias legacy: stessa semantica di pushStateNow.
  const pushState = pushStateNow;

  function undo() {
    // Flush del debounce pendente: senza, un Ctrl+Z entro 400ms dall'ultima
    // modifica salterebbe indietro di DUE stati (l'ultimo non ancora committato).
    pushStateNow();
    if (undoStack.value.length === 0) return;
    redoStack.value.push(lastSnapshot);
    restore(undoStack.value.pop());
  }

  function redo() {
    pushStateNow();
    if (redoStack.value.length === 0) return;
    undoStack.value.push(lastSnapshot);
    restore(redoStack.value.pop());
  }

  /**
   * Silent rollback: ripristina lo stato corrente all'ultimo push,
   * rimuovendo quello push dalla history (no fantasma undo step).
   * Usato quando un drop fallisce e vogliamo annullare tutto senza
   * che l'utente debba premere Ctrl+Z.
   */
  function rollback() {
    if (undoStack.value.length === 0) return;
    restore(undoStack.value.pop());
  }

  /**
   * Punto della cronologia di adesso (dopo un pushStateNow): chi offre un «Annulla»
   * legato a un gesto, come il toast dell'eliminazione, lo prende prima del gesto
   * (`prima`) e subito dopo averlo fatto diventare un passo (`dopo`).
   */
  function puntoAttuale() {
    return { passi, foto: lastSnapshot ? toRaw(lastSnapshot) : null };
  }

  /**
   * Riporta a `prima` solo se il gesto fatto da lì è ancora l'ultimo passo, cioè:
   * un solo passo da allora con in cima la fotografia di prima; oppure, dopo un
   * annulla e un ripeti di quel passo, la pagina uguale a `dopo` con in cima una
   * fotografia uguale a `prima` (restore ne fa copie: si confronta il contenuto).
   * Dopo altre modifiche (una tile, lo sfondo della pagina) un undo() secco
   * annullerebbe altro. true = la pagina è com'era a `prima` (anche già prima).
   */
  function annullaSeUltimo(prima, dopo) {
    pushStateNow();
    if (!prima || !prima.foto) return false;
    // Già com'era (il gesto non ha cambiato niente, o è stato annullato con Ctrl+Z).
    if (sameSnapshot(lastSnapshot, prima.foto)) return true;
    const n = undoStack.value.length;
    const cima = n ? toRaw(undoStack.value[n - 1]) : null;
    // Nessun movimento dopo il gesto. Qui non si confronta il contenuto: il
    // riallineamento dopo un salvataggio riscrive la fotografia senza creare passi.
    const fermo = passi === prima.passi + 1 && !!cima && cima === prima.foto;
    const ripetuto = !!(dopo && dopo.foto) && sameSnapshot(lastSnapshot, dopo.foto)
      && sameSnapshot(cima, prima.foto);
    if (!fermo && !ripetuto) return false;
    undo();
    return true;
  }

  // Fotografia con debounce, per le tile e per titolo e impostazioni della pagina:
  // evita un JSON.stringify a ogni tasto, e un cursore trascinato fa un solo passo.
  function programma() {
    if (isProgrammatic) return;
    if (debounceTimer) clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
      debounceTimer = null;
      pushStateNow();
    }, DEBOUNCE_MS);
  }

  /**
   * Titolo o impostazioni della pagina cambiati. Se è cambiato l'oggetto template
   * (caricamento, risposta di un salvataggio) non è un gesto dell'utente: il server
   * può riscrivere le impostazioni (un oggetto vuoto diventa [], post_id aggiunto)
   * e la fotografia si riallinea senza creare un passo, altrimenti il primo ⌘Z dopo
   * il salvataggio non farebbe niente. Una modifica ancora in attesa del debounce
   * diventa subito il suo passo.
   */
  function suImpostazioni() {
    const tpl = builderStore.currentTemplate;
    if (tpl !== ultimoTpl) {
      ultimoTpl = tpl;
      if (debounceTimer && !isProgrammatic) {
        pushStateNow();
      } else if (lastSnapshot) {
        lastSnapshot = Object.assign({}, lastSnapshot, {
          s: parteImpostazioni(tpl),
          o: ordineChiavi(tpl),
        });
      }
      return;
    }
    programma();
  }

  /**
   * Punto zero della cronologia. Va richiamato a pagina CARICATA (fine di
   * openBuilder/createAndOpenBuilder, header e footer compresi): se partisse
   * prima, il primo passo annullabile sarebbe la pagina vuota e un Ctrl+Z
   * svuoterebbe corpo, header e footer segnandoli da salvare.
   * Il debounce in sospeso (mutazioni del caricamento) va buttato: è già
   * compreso nella fotografia di partenza.
   */
  function initHistory() {
    if (debounceTimer) {
      clearTimeout(debounceTimer);
      debounceTimer = null;
    }
    undoStack.value = [];
    redoStack.value = [];
    passi++;
    // Azzera prima di ricampionare: snapshot() riusa le stringhe di lastSnapshot,
    // e su un nuovo template non vogliamo riferimenti al template precedente.
    lastSnapshot = null;
    lastSnapshot = snapshot();
    ultimoTpl = builderStore.currentTemplate;
    // Stessa fotografia = versione salvata di corpo, header e footer.
    builderStore.captureSavedBaseline(lastSnapshot);

    if (!initialized) {
      initialized = true;
      tilesStore.$subscribe(programma);
      // Solo titolo e impostazioni: un watch sull'intero store scatterebbe a ogni
      // selezione o zoom, uno sull'intero template attraverserebbe il content.
      watch(() => {
        const tpl = builderStore.currentTemplate;
        return tpl ? [tpl.title, tpl.settings] : null;
      }, suImpostazioni, { deep: true });
    }
  }

  return {
    undoStack,
    redoStack,
    canUndo: () => undoStack.value.length > 0,
    canRedo: () => redoStack.value.length > 0,
    pushState,
    pushStateNow,
    rollback,
    undo,
    redo,
    initHistory,
    puntoAttuale,
    annullaSeUltimo,
  };
}
