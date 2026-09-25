/**
 * useTileActions — azioni utente sulle tile con feedback e annullo.
 *
 * Centralizza l'ELIMINAZIONE (canvas, context menu, struttura, tastiera, iframe)
 * in un unico punto così che tutti i percorsi diano lo stesso comportamento:
 *   1. checkpoint di history atomico prima e dopo (Ctrl+Z / "Annulla" ripristinano
 *      in 1 passo, e l'eliminazione è un passo suo)
 *   2. rimozione con prune degli involucri vuoti (delegato allo store)
 *   3. deselezione se la tile eliminata era selezionata
 *   4. dirty flag sulla zona corretta (body/header/footer)
 *   5. toast "Eliminato — Annulla" (undo esplicito, cruciale perché il Canc
 *      non ha conferma e può eliminare più tile insieme): annulla solo finché
 *      l'eliminazione è l'ultimo passo, altrimenti lo dice
 *
 * E l'INCOLLA (menu contestuale e Ctrl+V, anche dall'anteprima): stessa posizione
 * (tilesStore.pasteAfterTile), un passo di annullo suo, la zona giusta da salvare,
 * la copia selezionata e portata in vista.
 */
import { useTilesStore } from '@/stores/tiles';
import { useBuilderStore } from '@/stores/builder';
import { useHistory } from '@/composables/useHistory';
import { useToast } from '@/composables/useToast';
import { requestScrollToTile } from '@/utils/scrollToTileChannel';
import { t } from '@/i18n';

export function useTileActions() {
  const tilesStore = useTilesStore();
  const builderStore = useBuilderStore();
  const history = useHistory();
  const toast = useToast();

  /**
   * Elimina una o più tile con toast + annullo.
   * @param {string|string[]} idsOrId
   */
  function removeTiles(idsOrId) {
    // Solo i nodi che esistono: un id rimasto da un nodo già tolto (una selezione
    // superata) non crea passi di annullo e non fa dire «Elemento eliminato» a un
    // gesto che non elimina niente.
    const ids = (Array.isArray(idsOrId) ? idsOrId : [idsOrId])
      .filter((id) => id && tilesStore.getTileById(id));
    if (!ids.length) return;

    // Checkpoint PRIMA della mutazione: separa questa eliminazione da eventuali
    // edit non ancora committati, così l'annullo ripristina esattamente lo stato
    // pre-eliminazione.
    history.pushStateNow();
    // Il punto a cui riporta l'«Annulla» del toast: vale finché l'eliminazione è
    // l'ultimo passo (dopo, un undo secco annullerebbe la modifica nuova).
    const prima = history.puntoAttuale();

    for (const id of ids) {
      // Zona calcolata prima della rimozione (dopo il nodo non esiste più).
      builderStore.markDirtyForTile(id);
      tilesStore.removeTile(id);
    }

    // L'eliminazione diventa subito un passo suo: col debounce una modifica fatta
    // entro 400 ms (lo sfondo, un'altra tile) finirebbe nello stesso passo, e
    // l'«Annulla» del toast la annullerebbe insieme alla tile.
    history.pushStateNow();
    const dopo = history.puntoAttuale();

    if (ids.includes(builderStore.selectedTileId)) builderStore.deselectTile();

    const msg = ids.length > 1
      ? t('%d elementi eliminati').replace('%d', String(ids.length))
      : t('Elemento eliminato');
    toast.action(msg, t('Annulla'), () => {
      if (!history.annullaSeUltimo(prima, dopo)) {
        toast.info(t("L'eliminazione non è più l'ultimo passo: usa Ctrl+Z per tornare indietro un passo alla volta"), 5000);
      }
    });
  }

  /**
   * Incolla gli appunti rispetto alla tile targetId (null = nessuna selezione).
   * @returns {object|null} la copia incollata, null se non incollata
   */
  function incolla(targetId) {
    if (!tilesStore.clipboardTile) return null;
    // Checkpoint prima e dopo, come l'eliminazione: l'incolla è un passo suo.
    history.pushStateNow();
    const clone = tilesStore.pasteAfterTile(targetId || null);
    if (!clone) {
      // Solo una colonna interna fuori da un blocco Colonne interne: niente cambia,
      // niente da salvare.
      toast.info(t('Qui non si può incollare: una colonna interna va dentro un blocco Colonne interne'), 5000);
      return null;
    }
    // Dopo l'inserimento (la zona si legge dal nodo): segna header, footer o pagina
    // e fa partire l'avviso di zona condivisa.
    builderStore.markDirtyForTile(clone.id);
    history.pushStateNow();
    builderStore.selectTile(clone.id);
    requestScrollToTile(clone.id);
    return clone;
  }

  return { removeTiles, incolla };
}
