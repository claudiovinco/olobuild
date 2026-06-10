/**
 * DnD Store — stato condiviso del drag & drop (parte UI/Pinia; la macchina
 * a stati fine del gesto — pending/threshold/finalizing — vive in useDnD.js).
 *
 * Stati:
 *   idle       → nessun drag attivo
 *   dragging   → drag in corso (da startDrag)
 *   dropping   → drop in corso (markDropping → endDrag)
 *
 * Unico source-of-truth per:
 *   - payload del drag (cosa si sta trascinando)
 *   - drop target corrente (dove il pointer sta passando)
 *
 * Safety net: ogni uscita anomala (Esc, blur, visibilitychange, window blur,
 * pagehide, pointercancel) entra nel ramo cancelDrag() che azzera tutto.
 */
import { defineStore } from 'pinia';

export const useDnDStore = defineStore('dnd', {
  state: () => ({
    phase: 'idle',

    // Cosa sta venendo trascinato
    // { source: 'sidebar'|'canvas', kind: 'tile-type'|'global-widget'|'node',
    //   tileType?, globalId?, nodeId?, fromParentId?, fromIndex? }
    payload: null,

    // Drop target attivo (aggiornato a ogni pointer move dentro una dropzone)
    // { kind: 'column'|'section-gap'|'row-gap'|'canvas-end'|'node-edge',
    //   id?, index?, edge?: 'top'|'bottom'|'left'|'right' }
    dropTarget: null,

    // Rect del drop target per animazioni drop-line (coordinate parent viewport)
    dropRect: null,

    // Timestamp di inizio drag — usato per analytics / debug
    dragStartedAt: 0,
  }),

  getters: {
    isDragging: (s) => s.phase === 'dragging',
    isIdle: (s) => s.phase === 'idle',
    hasDropTarget: (s) => s.dropTarget !== null,
  },

  actions: {
    /**
     * Inizia un drag. Chiamato dal callback onDragStart di Pragmatic.
     */
    startDrag(payload) {
      this.phase = 'dragging';
      this.payload = payload || null;
      this.dropTarget = null;
      this.dropRect = null;
      this.dragStartedAt = Date.now();
    },

    /**
     * Aggiorna il drop target corrente.
     */
    setDropTarget(target, rect) {
      this.dropTarget = target || null;
      this.dropRect = rect || null;
    },

    /**
     * Pulisce il drop target corrente (pointer uscito dalla dropzone).
     */
    clearDropTarget() {
      this.dropTarget = null;
      this.dropRect = null;
    },

    /**
     * Marca il drop in corso (tra onDrop di Pragmatic e settle).
     */
    markDropping() {
      if (this.phase === 'dragging') this.phase = 'dropping';
    },

    /**
     * Chiude il drag. Chiamato da onDrop / onDragEnd / cancelDrag.
     */
    endDrag() {
      this.phase = 'idle';
      this.payload = null;
      this.dropTarget = null;
      this.dropRect = null;
      this.dragStartedAt = 0;
    },

    /**
     * Cancella il drag forzatamente (Esc, blur, crash, timeout).
     * Equivalente a endDrag ma segnala intent di annullo.
     */
    cancelDrag() {
      this.endDrag();
    },
  },
});
