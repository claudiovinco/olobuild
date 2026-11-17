<template>
  <div class="olo-tab">
    <!-- Nell'inspector: un'anteprima delle prime righe e il pulsante che apre la tabella da modificare. -->
    <button type="button" class="olo-tab-anteprima" :aria-label="t('Modifica la tabella')" @click="apri">
      <table>
        <tr v-for="(riga, ri) in anteprima" :key="'a' + ri" :class="{ 'is-testa': ri === 0 && intestazione }">
          <td v-for="(cella, ci) in riga" :key="'a' + ri + '-' + ci">{{ cella || ' ' }}</td>
        </tr>
      </table>
    </button>
    <div class="olo-tab-sotto">
      <span class="olo-tab-misura">{{ misura }}</span>
      <button type="button" class="olo-tab-apri" @click="apri">{{ t('Modifica la tabella') }}</button>
    </div>
  </div>

  <!-- Fuori da #olobuilder-app (Teleport nel body): classi proprie e colori del chrome, e i campi resettati con una
       specificità più alta di forms.css di WordPress (bordo e anello blu). -->
  <Teleport to="body">
    <div v-if="aperta" class="olo-tab-velo" @mousedown.self="chiudi">
      <div ref="finestra" class="olo-tab-finestra" role="dialog" aria-modal="true" aria-labelledby="olo-tab-titolo">
        <div class="olo-tab-testa">
          <h3 id="olo-tab-titolo" class="olo-tab-titolo">
            {{ t('Dati della tabella') }}
            <InfoTip class="olo-tab-info" :titolo="t('Dati della tabella')"
              :testo="[t('Scrivi nelle celle: la pagina si aggiorna mentre scrivi.'), t('Tab passa alla cella dopo, Invio a quella sotto (alla fine aggiunge una riga).'), t('Sopra ogni colonna scegli l’allineamento; la × toglie la colonna o la riga.')]" />
          </h3>
          <button type="button" class="olo-tab-x" :title="t('Chiudi')" :aria-label="t('Chiudi')" @click="chiudi">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
          </button>
        </div>

        <div class="olo-tab-scorri">
          <table class="olo-tab-griglia">
            <thead>
              <tr>
                <th class="olo-tab-angolo" aria-hidden="true"></th>
                <th v-for="ci in colonne" :key="'c' + ci" class="olo-tab-col">
                  <div class="olo-tab-col-barra">
                    <div class="olo-tab-allinea" role="group" :aria-label="t('Allineamento colonna') + ' ' + ci">
                      <button v-for="a in ALLINEAMENTI" :key="a.v" type="button" class="olo-tab-al"
                        :class="{ on: allineamento(ci - 1) === a.v }" :title="a.l" :aria-label="a.l"
                        :aria-pressed="allineamento(ci - 1) === a.v ? 'true' : 'false'" @click="allinea(ci - 1, a.v)">
                        <svg width="12" height="12" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><rect v-for="(r, k) in a.righe" :key="k" :x="r[0]" :y="2 + k * 4" :width="r[1]" height="2" rx="1"/></svg>
                      </button>
                    </div>
                    <button v-if="colonne > 1" type="button" class="olo-tab-togli" :title="t('Togli la colonna')"
                      :aria-label="t('Togli la colonna') + ' ' + ci" @click="togliColonna(ci - 1)">×</button>
                  </div>
                </th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(riga, ri) in righe" :key="'r' + ri" :class="{ 'is-testa': ri === 0 && intestazione }">
                <th class="olo-tab-num" scope="row">
                  <span>{{ ri === 0 && intestazione ? t('Testa') : (intestazione ? ri : ri + 1) }}</span>
                  <button v-if="righe.length > 1" type="button" class="olo-tab-togli" :title="t('Togli la riga')"
                    :aria-label="t('Togli la riga') + ' ' + (ri + 1)" @click="togliRiga(ri)">×</button>
                </th>
                <td v-for="ci in colonne" :key="'v' + ri + '-' + ci">
                  <input class="olo-tab-cella" type="text" :value="riga[ci - 1] ?? ''" :data-r="ri" :data-c="ci - 1"
                    :style="{ textAlign: allineamento(ci - 1) }"
                    :aria-label="t('Riga') + ' ' + (ri + 1) + ', ' + t('colonna') + ' ' + ci"
                    @input="scrivi(ri, ci - 1, $event.target.value)" @keydown.enter.prevent="giu(ri, ci - 1)" />
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="olo-tab-piede">
          <button type="button" class="olo-tab-ag" @click="aggiungiRiga">{{ t('+ Riga') }}</button>
          <button type="button" class="olo-tab-ag" @click="aggiungiColonna">{{ t('+ Colonna') }}</button>
          <span class="olo-tab-spazio"></span>
          <button type="button" class="olo-tab-fatto" @click="chiudi">{{ t('Fatto') }}</button>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
// Dati della Tabella (table_data: righe di celle di testo; la prima è l'intestazione se «Riga intestazione» è
// accesa) e allineamento delle colonne (col_alignments). Prima si modificavano solo nel canvas Vue, che non si monta
// più da quando l'anteprima è il renderer PHP (25 set 2026): nel builder la tabella non si poteva cambiare
// (16 nov 2026, episodio 27). Le chiavi restano quelle di sempre: nessuna migrazione.
import { computed, nextTick, ref, watch } from 'vue';
import { t } from '@/i18n';
import { useFocusTrap } from '@/composables/useFocusTrap';
import InfoTip from '../InfoTip.vue';

const props = defineProps({
  modelValue: { type: Array, default: () => [] },
  tileSettings: { type: Object, default: () => ({}) },
});
const emit = defineEmits(['update:modelValue', 'chiave']);

const ALLINEAMENTI = [
  { v: 'left', l: t('A sinistra'), righe: [[1, 11], [1, 14], [1, 9]] },
  { v: 'center', l: t('Al centro'), righe: [[3, 10], [1, 14], [4, 8]] },
  { v: 'right', l: t('A destra'), righe: [[4, 11], [1, 14], [6, 9]] },
];

// Come il renderer (parse_table): righe di celle; il formato storico è un testo con una riga per riga e le celle
// separate da «|» (anche come elenco di righe di testo). Alla prima modifica si salva come righe di celle.
const righe = computed(() => {
  let d = props.modelValue;
  if (typeof d === 'string') d = d.split('\n').map((l) => l.trim()).filter(Boolean);
  if (!Array.isArray(d)) d = [];
  d = d.map((r) => (Array.isArray(r) ? r.map((c) => String(c ?? '')) : String(r ?? '').split('|').map((c) => c.trim())));
  return d.length ? d : [['']];
});
const colonne = computed(() => Math.max(1, ...righe.value.map((r) => r.length)));
const intestazione = computed(() => props.tileSettings?.has_header !== false);
const misura = computed(() => {
  const n = righe.value.length - (intestazione.value ? 1 : 0);
  return (n === 1 ? t('1 riga') : t('%d righe').replace('%d', n)) + ' · '
    + (colonne.value === 1 ? t('1 colonna') : t('%d colonne').replace('%d', colonne.value));
});
const anteprima = computed(() => righe.value.slice(0, 4).map((r) => r.slice(0, 3)));

// Ogni modifica parte da una copia rettangolare: righe corte completate con celle vuote.
function copia() {
  return righe.value.map((r) => Array.from({ length: colonne.value }, (_, i) => r[i] ?? ''));
}
function scrivi(ri, ci, v) {
  const d = copia();
  d[ri][ci] = v;
  emit('update:modelValue', d);
}
function aggiungiRiga() {
  const d = copia();
  d.push(Array.from({ length: colonne.value }, () => ''));
  emit('update:modelValue', d);
  nextTick(() => fuoco(d.length - 1, 0));
}
function aggiungiColonna() {
  const d = copia().map((r) => [...r, '']);
  emit('update:modelValue', d);
  nextTick(() => fuoco(0, colonne.value - 1));
}
function togliRiga(ri) {
  const d = copia();
  if (d.length <= 1) return;
  d.splice(ri, 1);
  emit('update:modelValue', d);
}
function togliColonna(ci) {
  if (colonne.value <= 1) return;
  emit('update:modelValue', copia().map((r) => r.filter((_, i) => i !== ci)));
  const al = [...(props.tileSettings?.col_alignments || [])];
  if (al.length > ci) {
    al.splice(ci, 1);
    emit('chiave', { key: 'col_alignments', value: al });
  }
}
function allineamento(ci) {
  const al = props.tileSettings?.col_alignments;
  return (Array.isArray(al) && al[ci]) || 'left';
}
function allinea(ci, v) {
  const al = [...(props.tileSettings?.col_alignments || [])];
  while (al.length <= ci) al.push('left');
  al[ci] = v;
  emit('chiave', { key: 'col_alignments', value: al });
}
// Invio scende alla cella sotto; dall'ultima riga ne aggiunge una.
function giu(ri, ci) {
  if (ri + 1 < righe.value.length) fuoco(ri + 1, ci);
  else { aggiungiRiga(); nextTick(() => fuoco(ri + 1, ci)); }
}
function fuoco(ri, ci) {
  const el = finestra.value?.querySelector('input[data-r="' + ri + '"][data-c="' + ci + '"]');
  if (el) { el.focus(); el.select(); }
}

const aperta = ref(false);
const finestra = ref(null);
const trap = useFocusTrap(finestra, { onEscape: () => chiudi() });
function apri() { aperta.value = true; }
function chiudi() { aperta.value = false; }
watch(aperta, (v) => {
  if (v) nextTick(() => { trap.activate(); fuoco(intestazione.value && righe.value.length > 1 ? 1 : 0, 0); });
  else trap.deactivate();
});
</script>

<style scoped>
.olo-tab-anteprima {
  display: block;
  width: 100%;
  padding: 0;
  overflow: hidden;
  background: #fff;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  cursor: pointer;
  text-align: left;
}
.olo-tab-anteprima:hover { border-color: #cbd5e1; }
.olo-tab-anteprima:focus-visible { outline: 2px solid var(--olo-ui-accent, #e8622a); outline-offset: 1px; }
.olo-tab-anteprima table { width: 100%; border-collapse: collapse; table-layout: fixed; font-size: 11px; color: #334155; }
.olo-tab-anteprima td {
  padding: 5px 8px;
  overflow: hidden;
  border-bottom: 1px solid #f1f5f9;
  white-space: nowrap;
  text-overflow: ellipsis;
}
.olo-tab-anteprima tr:last-child td { border-bottom: 0; }
.olo-tab-anteprima tr.is-testa td { font-weight: 600; color: #1e293b; background: #f8fafc; }
.olo-tab-sotto { display: flex; align-items: center; justify-content: space-between; gap: 8px; margin-top: 6px; }
.olo-tab-misura { font-size: 11px; color: #64748b; }
.olo-tab-apri {
  padding: 6px 10px;
  font-size: 12px;
  font-weight: 600;
  color: #fff;
  background: var(--olo-ui-accent, #e8622a);
  border: 0;
  border-radius: 6px;
  cursor: pointer;
}
.olo-tab-apri:hover { filter: brightness(0.95); }
.olo-tab-apri:focus-visible { outline: 2px solid #1e293b; outline-offset: 2px; }

.olo-tab-velo {
  position: fixed;
  inset: 0;
  z-index: 99999; /* sotto i popover dei campi (NumberScrubber 100000, FieldSelect 100090) */
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 24px;
  background: rgba(15, 23, 42, 0.45);
}
.olo-tab-finestra {
  display: flex;
  flex-direction: column;
  width: 860px;
  max-width: 100%;
  max-height: 86vh;
  padding: 16px;
  color: #1e293b;
  background: #fff;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  box-shadow: 0 20px 40px rgba(15, 23, 42, 0.18);
  font-family: -apple-system, BlinkMacSystemFont, 'Inter', 'Segoe UI', sans-serif;
  font-size: 12px;
}
.olo-tab-testa { display: flex; align-items: center; justify-content: space-between; gap: 8px; margin-bottom: 12px; }
.olo-tab-titolo { display: flex; align-items: center; gap: 6px; margin: 0; font-size: 15px; font-weight: 600; color: #1e293b; }
.olo-tab-x {
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
.olo-tab-x:hover { color: #1e293b; background: #f1f5f9; }
.olo-tab-x:focus-visible { outline: 2px solid var(--olo-ui-accent, #e8622a); outline-offset: 1px; }
.olo-tab-scorri { flex: 1 1 auto; min-height: 0; overflow: auto; border: 1px solid #e2e8f0; border-radius: 8px; }
.olo-tab-griglia { border-collapse: separate; border-spacing: 0; min-width: 100%; }
.olo-tab-griglia th, .olo-tab-griglia td { padding: 0; border-right: 1px solid #f1f5f9; border-bottom: 1px solid #f1f5f9; }
.olo-tab-angolo, .olo-tab-col { position: sticky; top: 0; z-index: 1; background: #f8fafc; }
.olo-tab-col { min-width: 150px; }
.olo-tab-col-barra { display: flex; align-items: center; justify-content: space-between; gap: 4px; padding: 4px 6px; }
.olo-tab-allinea { display: inline-flex; gap: 2px; padding: 2px; background: #eef2f6; border-radius: 6px; }
.olo-tab-al {
  display: grid;
  place-items: center;
  width: 24px;
  height: 22px;
  padding: 0;
  color: #64748b;
  background: transparent;
  border: 0;
  border-radius: 4px;
  cursor: pointer;
}
.olo-tab-al.on { color: #1e293b; background: #fff; box-shadow: 0 1px 2px rgba(15, 23, 42, 0.12); }
.olo-tab-al:focus-visible, .olo-tab-togli:focus-visible { outline: 2px solid var(--olo-ui-accent, #e8622a); outline-offset: 1px; }
.olo-tab-num {
  position: sticky;
  left: 0;
  z-index: 1;
  min-width: 64px;
  padding: 0 4px 0 8px !important;
  font-size: 11px;
  font-weight: 500;
  color: #64748b;
  text-align: left;
  white-space: nowrap;
  background: #f8fafc;
}
.olo-tab-num span { display: inline-block; min-width: 34px; }
.olo-tab-togli {
  width: 20px;
  height: 20px;
  padding: 0;
  font-size: 14px;
  line-height: 1;
  color: #94a3b8;
  background: transparent;
  border: 0;
  border-radius: 4px;
  cursor: pointer;
}
.olo-tab-togli:hover { color: #dc2626; background: #fef2f2; }
tr.is-testa .olo-tab-num { color: #1e293b; font-weight: 600; }
/* Reset dei campi con specificità sopra forms.css di WordPress (0,2,1): niente bordo né anello blu. */
.olo-tab-finestra .olo-tab-griglia input.olo-tab-cella {
  display: block;
  width: 100%;
  min-width: 140px;
  height: 34px;
  margin: 0;
  padding: 0 10px;
  font: inherit;
  font-size: 13px;
  color: #1e293b;
  background: #fff;
  border: 0;
  border-radius: 0;
  box-shadow: none;
  outline: none;
}
.olo-tab-finestra .olo-tab-griglia tr.is-testa input.olo-tab-cella { font-weight: 600; background: #f8fafc; }
.olo-tab-finestra .olo-tab-griglia input.olo-tab-cella:focus {
  background: #fff7f2;
  box-shadow: inset 0 0 0 2px var(--olo-ui-accent, #e8622a);
}
.olo-tab-piede { display: flex; align-items: center; gap: 8px; margin-top: 12px; }
.olo-tab-spazio { flex: 1; }
.olo-tab-ag {
  padding: 7px 12px;
  font-size: 12px;
  font-weight: 500;
  color: #334155;
  background: #fff;
  border: 1px dashed #cbd5e1;
  border-radius: 6px;
  cursor: pointer;
}
.olo-tab-ag:hover { color: #1e293b; border-color: #94a3b8; }
.olo-tab-fatto {
  padding: 7px 16px;
  font-size: 12px;
  font-weight: 600;
  color: #fff;
  background: var(--olo-ui-accent, #e8622a);
  border: 0;
  border-radius: 6px;
  cursor: pointer;
}
.olo-tab-ag:focus-visible, .olo-tab-fatto:focus-visible { outline: 2px solid #1e293b; outline-offset: 2px; }
</style>
