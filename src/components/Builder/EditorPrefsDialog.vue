<template>
  <Teleport to="body">
    <div v-if="open" class="olo-ep-overlay" @click.self="emit('close')">
      <div ref="dialogRef" class="olo-ep" role="dialog" aria-modal="true" aria-labelledby="olo-ep-title">
        <div class="olo-ep-head">
          <h3 id="olo-ep-title" class="olo-ep-title">{{ t('Preferenze editor') }}</h3>
          <button type="button" class="olo-ep-close" :title="t('Chiudi')" :aria-label="t('Chiudi')" @click="emit('close')">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
          </button>
        </div>
        <p class="olo-ep-note">{{ t('Solo per te, in questo browser: la pagina non cambia.') }}</p>

        <section class="olo-ep-sec" aria-labelledby="olo-ep-flash">
          <h4 id="olo-ep-flash" class="olo-ep-sec-title">{{ t('Evidenziazione dalla Struttura') }}</h4>
          <p class="olo-ep-help">{{ t('Effetto visivo quando selezioni un tile dalla Struttura.') }}</p>
          <div class="olo-ep-row olo-ep-row--fill">
            <span class="olo-ep-label">{{ t('Tipo effetto') }}</span>
            <FieldSelect
              ui="dropdown"
              :aria-label="t('Tipo effetto')"
              :modelValue="sf.effect"
              :options="scrollEffectOptions"
              @update:modelValue="updateSf('effect', $event)"
            />
          </div>
          <div class="olo-ep-row olo-ep-row--fill">
            <span class="olo-ep-label">{{ t('Colore') }}</span>
            <FieldColor :modelValue="sf.color" @update:modelValue="updateSf('color', $event)" />
          </div>
          <template v-for="r in SF_RANGES" :key="r.key">
            <div v-if="!r.soloPulse || sf.effect === 'pulse'" class="olo-ep-row">
              <span class="olo-ep-label">{{ t(r.label) }}</span>
              <FieldRange
                compact
                :unit="r.unit"
                :min="r.min"
                :max="r.max"
                :step="r.step"
                :defaultValue="sfDefaults[r.key]"
                :aria-label="t(r.label)"
                :modelValue="sf[r.key]"
                @update:modelValue="setSfInt(r, $event)"
                @focusout="risincronizzaNumero($event, sf[r.key])"
              />
            </div>
          </template>
          <p class="olo-ep-help">{{ t('Durata scorrimento: 0 = istantaneo.') }}</p>
        </section>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
// Preferenze dell'editor: scelte di chi usa il builder, salvate in questo browser
// (localStorage), che non toccano la pagina. Oggi c'è l'Evidenziazione dalla
// Struttura (chiave olo_scroll_flash, la stessa di sempre: useIframeBridge la
// rilegge a ogni selezione), che prima stava in fondo alle Impostazioni pagina
// come se fosse una proprietà della pagina.
// Fuori da #olobuilder-app (Teleport nel body): nessuna classe mb-*-gray, che lì
// non sarebbe rimappata; solo classi proprie e colori del chrome.
import { t } from '@/i18n';
import { nextTick, reactive, ref, watch } from 'vue';
import { useFocusTrap } from '@/composables/useFocusTrap';
import { loadScrollFlashPrefs, saveScrollFlashPrefs, scrollFlashDefaults } from '@/utils/scrollFlashPrefs';
import { interoNelRange, risincronizzaNumero } from '@/utils/numeroIntero';
import FieldColor from './fields/FieldColor.vue';
import FieldSelect from './fields/FieldSelect.vue';
import FieldRange from './fields/FieldRange.vue';

const props = defineProps({
  open: { type: Boolean, default: false },
});
const emit = defineEmits(['close']);

const sf = reactive(loadScrollFlashPrefs());
const sfDefaults = scrollFlashDefaults();

const scrollEffectOptions = [
  { value: 'flash', label: 'Flash (singolo)' },
  { value: 'pulse', label: 'Pulse (ripetuto)' },
];
// Estremi e passi di sempre; l'unità sta nel controllo. «Durata scorrimento» era
// «Velocità scorrimento»: sono millisecondi, più alto = più lento.
const SF_RANGES = [
  { key: 'size', label: 'Dimensione effetto', unit: 'px', min: 2, max: 20, step: 1 },
  { key: 'duration', label: 'Durata effetto', unit: 'ms', min: 300, max: 3000, step: 100 },
  { key: 'pulse_count', label: 'Ripetizioni pulse', unit: '×', min: 1, max: 6, step: 1, soloPulse: true },
  { key: 'scroll_ms', label: 'Durata scorrimento', unit: 'ms', min: 0, max: 1500, step: 50 },
];

function updateSf(key, value) {
  sf[key] = value;
  saveScrollFlashPrefs(sf);
}
// Numeri come prima (parseInt), entro gli estremi: FieldRange emette stringhe.
function setSfInt(r, raw) {
  const n = interoNelRange(raw, r.min, r.max);
  if (n !== null) updateSf(r.key, n);
}

// Esc chiude, Tab resta nella finestra, alla chiusura il focus torna al pulsante.
const dialogRef = ref(null);
const trap = useFocusTrap(dialogRef, { onEscape: () => emit('close') });
watch(() => props.open, (v) => {
  if (v) {
    // Riaprendo si rilegge: un'altra scheda del builder può averle cambiate.
    Object.assign(sf, loadScrollFlashPrefs());
    nextTick(() => trap.activate());
  } else {
    trap.deactivate();
  }
});
</script>

<style scoped>
.olo-ep-overlay {
  position: fixed;
  inset: 0;
  z-index: 99999; /* sotto i popover dei campi (NumberScrubber 100000, FieldSelect 100090) */
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 16px;
  background: rgba(15, 23, 42, 0.45);
}
.olo-ep {
  width: 380px;
  max-width: 100%;
  max-height: 85vh;
  overflow-y: auto;
  padding: 16px;
  color: #1e293b;
  background: #fff;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  box-shadow: 0 20px 40px rgba(15, 23, 42, 0.18);
  font-family: inherit;
  font-size: 12px;
}
.olo-ep-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 8px;
}
.olo-ep-title {
  margin: 0;
  font-size: 15px;
  font-weight: 600;
  color: #1e293b;
}
.olo-ep-close {
  display: grid;
  place-items: center;
  width: 28px;
  height: 28px;
  padding: 0;
  color: #475569;
  background: transparent;
  border: 0;
  border-radius: 6px;
  cursor: pointer;
}
.olo-ep-close:hover { color: #1e293b; background: #f1f5f9; }
.olo-ep-note {
  margin: 6px 0 12px;
  font-size: 11px;
  line-height: 1.4;
  color: #475569;
}
.olo-ep-sec {
  display: flex;
  flex-direction: column;
  gap: 10px;
  padding: 10px 12px 12px;
  background: #f9fafb;
  border: 1px solid #f1f5f9;
  border-radius: 8px;
}
.olo-ep-sec-title {
  margin: 0;
  font-size: 10px;
  font-weight: 700;
  letter-spacing: 0.06em;
  text-transform: uppercase;
  color: #1e293b;
}
.olo-ep-help {
  margin: 0;
  font-size: 11px;
  line-height: 1.4;
  color: #64748b; /* 4,5:1 sul fondo #f9fafb della sezione */
}
.olo-ep-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 10px;
  min-height: 30px;
}
.olo-ep-row > .olo-ep-label { flex: 1 1 auto; min-width: 0; }
.olo-ep-row > :not(.olo-ep-label) { flex: 0 0 auto; }
.olo-ep-row--fill > .olo-ep-label { flex: 0 1 auto; max-width: 46%; }
.olo-ep-row--fill > :not(.olo-ep-label) { flex: 1 1 0; min-width: 0; }
.olo-ep-label {
  font-size: 12px;
  font-weight: 500;
  line-height: 1.35;
  color: #475569;
}
.olo-ep-close:focus-visible {
  outline: 2px solid var(--olo-ui-accent, #e8622a);
  outline-offset: 2px;
}
</style>
