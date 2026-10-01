<template>
  <div :class="['olo-collapse-section', macro ? 'olo-collapse-section--macro' : '']">
    <!-- Testata: tutta la riga apre e chiude, il PULSANTE è il titolo (aria-expanded, Tab).
         Non è più un unico <button>: dentro ci stanno la (i) della spiegazione e, nello
         slot header-right, interruttori che sono pulsanti a loro volta (un pulsante dentro
         un pulsante non è HTML valido). Gli spaziatori ai lati tengono il titolo centrato. -->
    <div
      class="collapse-head mb-flex mb-items-center mb-w-full mb-rounded-md mb-transition-colors mb-px-3 mb-py-2 mb-text-[11px] mb-font-bold mb-uppercase mb-tracking-wider"
      :class="open ? 'collapse-head--open' : ''"
      @click="open = !open"
    >
      <span class="collapse-spacer" aria-hidden="true"></span>
      <button
        type="button"
        class="collapse-toggle"
        :aria-expanded="open"
        :aria-controls="bodyId"
        @click.stop="open = !open"
      >{{ title }}</button>
      <InfoTip v-if="testiInfo.length" class="collapse-info" :testo="testiInfo" :titolo="title" />
      <span class="collapse-spacer" aria-hidden="true"></span>
      <slot name="header-right" />
      <svg
        :class="['mb-transition-transform mb-duration-200', open ? 'mb-rotate-180' : '']"
        width="12" height="12" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
        style="color: rgba(255,255,255,0.6)"
        aria-hidden="true"
      >
        <path d="M3 4.5L6 7.5L9 4.5" />
      </svg>
    </div>
    <div :id="bodyId" v-show="open || forceOpen" :class="['mb-pt-3 mb-pb-1 mb-space-y-3', macro ? 'olo-collapse-body--macro' : '']">
      <slot />
    </div>
  </div>
</template>

<script setup>
import { ref, watch, computed, provide, reactive } from 'vue';
import InfoTip from './InfoTip.vue';

const props = defineProps({
  title: { type: String, required: true },
  defaultOpen: { type: Boolean, default: false },
  // macro: true = accordion principale (top-level); false (default) = sub-accordion
  macro: { type: Boolean, default: false },
  // forceOpen: tiene il body visibile a prescindere dallo stato del toggle
  // (usato durante la ricerca impostazioni per mostrare i match nelle sezioni chiuse)
  forceOpen: { type: Boolean, default: false },
  // storageKey (facoltativa): se c'è, aperto/chiuso si ricorda in localStorage
  // ('olo_collapse_<storageKey>'), letto e scritto in try/catch. Storage vuoto o
  // bloccato = defaultOpen. Senza la prop nulla cambia per gli usi esistenti.
  storageKey: { type: String, default: '' },
  // Spiegazione della sezione (stringa o paragrafi, già tradotti): va nella (i) accanto
  // al titolo, MAI in linea (regola utente 1 ott 2026: le spiegazioni lunghe stanno in
  // un popup). Vi si aggiungono da sole quelle dei campi `type:'description'` visibili
  // nella sezione (InspectorField le registra qui, vedi provide sotto).
  info: { type: [String, Array], default: '' },
});

const bodyId = 'olo-sec-' + Math.random().toString(36).slice(2, 10);

// Spiegazioni registrate dai campi `description` della sezione, nell'ordine dei campi.
// Un campo nascosto dalla sua condizione non è montato, quindi la sua spiegazione non c'è.
const registrate = reactive(new Map());
let ordine = 0;
provide('oloSezioneInfo', {
  registra(testo) {
    const chiave = ++ordine;
    registrate.set(chiave, testo);
    return () => registrate.delete(chiave);
  },
  aggiorna(chiave, testo) { if (registrate.has(chiave)) registrate.set(chiave, testo); },
});
const testiInfo = computed(() => {
  const propri = Array.isArray(props.info) ? props.info : [props.info];
  const daiCampi = [...registrate.entries()].sort((a, b) => a[0] - b[0]).map(([, v]) => (typeof v === 'function' ? v() : v));
  return [...propri, ...daiCampi].map((x) => String(x || '').trim()).filter(Boolean);
});

const STORAGE_PREFIX = 'olo_collapse_';

function statoIniziale() {
  if (props.storageKey) {
    try {
      const v = localStorage.getItem(STORAGE_PREFIX + props.storageKey);
      if (v === '1') return true;
      if (v === '0') return false;
    } catch (e) { /* storage non disponibile: vale defaultOpen */ }
  }
  return props.defaultOpen;
}

const open = ref(statoIniziale());

watch(open, (v) => {
  if (!props.storageKey) return;
  try {
    localStorage.setItem(STORAGE_PREFIX + props.storageKey, v ? '1' : '0');
  } catch (e) { /* storage non disponibile: lo stato vale fino alla chiusura */ }
});
</script>

<style scoped>
/* Stile flat uniforme per tutti gli accordion (minimale, no gradient) */
.collapse-head {
  background: rgba(0, 0, 0, 0.32);
  color: rgba(255, 255, 255, 0.85);
  cursor: pointer;
  gap: 4px;
}
.collapse-spacer { flex: 1 1 0; min-width: 0; }
/* Il titolo è il pulsante: eredita tipografia e colore della testata */
.collapse-toggle {
  flex: 0 1 auto;
  min-width: 0;
  padding: 0;
  margin: 0;
  border: none;
  background: transparent;
  color: inherit;
  font: inherit;
  letter-spacing: inherit;
  text-transform: inherit;
  text-align: center;
  cursor: pointer;
}
.collapse-toggle:focus-visible {
  outline: 2px solid var(--olo-ui-accent, #e8622a);
  outline-offset: 3px;
  border-radius: 3px;
}
.collapse-info { margin-left: 2px; }
.collapse-head:hover {
  background: rgba(0, 0, 0, 0.42);
  color: #fff;
}
.collapse-head--open {
  background: rgba(0, 0, 0, 0.5);
  color: #fff;
}

/* Macro accordion: stesso stile di header, solo separator visivo via spaziatura.
 * NESSUN gradient, NESSUN bordo accent — gerarchia data solo dal body indentato. */
.olo-collapse-section--macro {
  margin-top: 0.5rem;
}
.olo-collapse-section--macro:first-child {
  margin-top: 0;
}
/* Sub-accordion dentro una macro: indentati con border-left sottile per gerarchia */
.olo-collapse-body--macro {
  padding-left: 0.5rem;
  border-left: 1px solid rgba(255, 255, 255, 0.08);
  margin-left: 0.25rem;
}
</style>
