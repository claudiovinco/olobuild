/**
 * useTileActions — azioni utente sulle tile con feedback e annullo.
 *
 * Centralizza l'ELIMINAZIONE (canvas, context menu, struttura, tastiera, iframe)
 * in un unico punto così che tutti i percorsi diano lo stesso comportamento:
 *   1. checkpoint di history atomico (Ctrl+Z / "Annulla" ripristinano in 1 passo)
 *   2. rimozione con prune degli involucri vuoti (delegato allo store)
 *   3. deselezione se la tile eliminata era selezionata
 *   4. dirty flag sulla zona corretta (body/header/footer)
 *   5. toast "Eliminato — Annulla" (undo esplicito, cruciale perché il Canc
 *      non ha conferma e può eliminare più tile insieme)
 */
import { useTilesStore } from '@/stores/tiles';
import { useBuilderStore } from '@/stores/builder';
import { useHistory } from '@/composables/useHistory';
import { useToast } from '@/composables/useToast';
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
    const ids = (Array.isArray(idsOrId) ? idsOrId : [idsOrId]).filter(Boolean);
    if (!ids.length) return;

    // Checkpoint PRIMA della mutazione: separa questa eliminazione da eventuali
    // edit non ancora committati, così l'annullo ripristina esattamente lo stato
    // pre-eliminazione.
    history.pushStateNow();

    for (const id of ids) {
      // Zona calcolata prima della rimozione (dopo il nodo non esiste più).
      builderStore.markDirtyForTile(id);
      tilesStore.removeTile(id);
    }

    if (ids.includes(builderStore.selectedTileId)) builderStore.deselectTile();

    const msg = ids.length > 1
      ? t('%d elementi eliminati').replace('%d', String(ids.length))
      : t('Elemento eliminato');
    toast.action(msg, t('Annulla'), () => history.undo());
  }

  return { removeTiles };
}
