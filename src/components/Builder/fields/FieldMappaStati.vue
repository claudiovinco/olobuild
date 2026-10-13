<template>
  <div class="olo-ms">
    <div class="olo-ms-box">
      <svg
        v-if="dati"
        class="olo-ms-mappa"
        :viewBox="dati.viewBox"
        :style="variabili"
        aria-hidden="true"
        @click="clicSullaMappa"
      >
        <path v-for="s in dati.stati" :key="s.c" :d="s.d" :data-c="s.c" :class="classeDi(s.c)"><title>{{ nomeDi(s) }}</title></path>
        <circle v-for="p in dati.puntini" :key="p.c" :cx="p.x" :cy="p.y" r="6" :data-c="p.c" :class="['olo-ms-p', classeDi(p.c)]"><title>{{ nomeDi(p) }}</title></circle>
      </svg>
      <p v-else class="olo-ms-carica">{{ errore ? t('Mappa non disponibile.') : t('Caricamento della mappa…') }}</p>
    </div>
    <div class="olo-ms-riga">
      <span class="olo-ms-conti" :style="variabili">
        <span class="olo-ms-conto"><i class="olo-ms-k olo-ms-k2" aria-hidden="true"></i>{{ etichetta2 }} <b>{{ conta[2] }}</b></span>
        <span class="olo-ms-conto"><i class="olo-ms-k olo-ms-k1" aria-hidden="true"></i>{{ etichetta1 }} <b>{{ conta[1] }}</b></span>
      </span>
      <button type="button" class="olo-ms-apri" :disabled="!dati" @click="apri">{{ t('Apri in grande') }}</button>
    </div>
  </div>

  <Teleport to="body">
    <div v-if="aperto" class="olo-msd-velo" @click.self="chiudi">
      <div ref="finestra" class="olo-msd" role="dialog" aria-modal="true" aria-labelledby="olo-msd-titolo" :style="variabili">
        <div class="olo-msd-testa">
          <h3 id="olo-msd-titolo" class="olo-msd-titolo">{{ t('Stati') }}<InfoTip class="olo-msd-info" :titolo="t('Stati')" :testo="[t('Un clic su uno stato gli dà il primo colore, un secondo clic il secondo, un terzo lo riporta trasparente.'), t('Gli stati troppo piccoli per la cartina sono puntini: puoi anche cercarli per nome qui sotto.')]" /></h3>
          <button type="button" class="olo-msd-chiudi" :title="t('Chiudi')" :aria-label="t('Chiudi')" @click="chiudi">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
          </button>
        </div>

        <svg class="olo-ms-mappa olo-msd-mappa" :viewBox="dati.viewBox" aria-hidden="true" @click="clicSullaMappa">
          <path v-for="s in dati.stati" :key="s.c" :d="s.d" :data-c="s.c" :class="classeDi(s.c)"><title>{{ nomeDi(s) }}</title></path>
          <circle v-for="p in dati.puntini" :key="p.c" :cx="p.x" :cy="p.y" r="4" :data-c="p.c" :class="['olo-ms-p', classeDi(p.c)]"><title>{{ nomeDi(p) }}</title></circle>
        </svg>

        <div class="olo-msd-sotto">
          <div class="olo-msd-cerca">
            <input
              v-model="cerca"
              type="search"
              class="olo-msd-input"
              :placeholder="t('Cerca uno stato')"
              :aria-label="t('Cerca uno stato')"
            />
            <ul v-if="trovati.length" class="olo-msd-trovati">
              <li v-for="s in trovati" :key="s.c">
                <button type="button" class="olo-msd-voce" :aria-label="nomeDi(s) + ': ' + statoDi(s.c)" @click="giro(s.c)">
                  <i :class="['olo-ms-k', 'olo-ms-k' + (valoreDi(s.c) || 0)]" aria-hidden="true"></i>{{ nomeDi(s) }}
                </button>
              </li>
            </ul>
            <p v-else-if="cerca.trim()" class="olo-msd-vuoto">{{ t('Nessuno stato con questo nome.') }}</p>
          </div>
          <div class="olo-msd-elenchi">
            <div v-for="g in gruppi" :key="g.v" class="olo-msd-gruppo">
              <h4 class="olo-msd-gtitolo"><i :class="['olo-ms-k', 'olo-ms-k' + g.v]" aria-hidden="true"></i>{{ g.etichetta }} <b>{{ g.stati.length }}</b></h4>
              <div v-if="g.stati.length" class="olo-msd-chips">
                <button v-for="s in g.stati" :key="s.c" type="button" class="olo-msd-chip" :aria-label="nomeDi(s) + ': ' + g.etichetta" @click="giro(s.c)">{{ nomeDi(s) }}</button>
              </div>
              <p v-else class="olo-msd-vuoto">{{ t('Nessuno.') }}</p>
            </div>
          </div>
        </div>

        <div class="olo-msd-piede">
          <button type="button" class="olo-msd-azzera" :disabled="!conta[1] && !conta[2]" @click="azzera">{{ conferma ? t('Sicuro? Azzera tutto') : t('Azzera') }}</button>
          <button type="button" class="olo-msd-fatto" @click="chiudi">{{ t('Fatto') }}</button>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
// Controllo «Stati» della tile Mappa del mondo: la cartina si clicca, un clic dà il primo colore, due il
// secondo, tre lo riportano trasparente; salva { "IT": 2, "FR": 1 }. Stessi dati del renderer PHP
// (assets/data/mappa-mondo.json, una fonte sola), caricati una volta per tutte le tile. «Apri in grande»:
// la cartina larga, la ricerca per nome (per i puntini degli stati piccoli e per la tastiera) e gli elenchi.
// Fuori da #olobuilder-app (Teleport nel body): solo classi proprie e colori del chrome.
import { t } from '@/i18n';
import { computed, nextTick, ref, watch } from 'vue';
import { useStylesStore } from '@/stores/styles';
import { resolveColorToken } from '@/utils/colorToken';
import { useFocusTrap } from '@/composables/useFocusTrap';
import InfoTip from '../InfoTip.vue';

const props = defineProps({
  modelValue: { type: [Object, Array], default: () => ({}) },
  tileSettings: { type: Object, default: null },
});
const emit = defineEmits(['update:modelValue']);

let promessa = null;
function caricaDati() {
  if (!promessa) {
    const o = window.oloData || {};
    const base = o.pluginUrl || '/wp-content/plugins/olobuild/';
    promessa = fetch(base + 'assets/data/mappa-mondo.json' + (o.version ? '?ver=' + encodeURIComponent(o.version) : ''))
      .then((r) => { if (!r.ok) throw new Error(String(r.status)); return r.json(); })
      .catch((e) => { promessa = null; throw e; });
  }
  return promessa;
}

const dati = ref(null);
const errore = ref(false);
caricaDati().then((d) => { dati.value = d; }).catch(() => { errore.value = true; });

// Nomi: quelli del file nelle cinque lingue del sito, se no quelli del browser
const lingua = String((window.oloData && window.oloData.locale) || document.documentElement.lang || 'it').slice(0, 2).toLowerCase();
let intl = null;
try { intl = new Intl.DisplayNames([lingua], { type: 'region' }); } catch (e) { intl = null; }
function nomeDi(s) {
  return (s.n && s.n[lingua]) || (intl && intl.of(s.c)) || (s.n && s.n.en) || s.c;
}

const scelti = computed(() => {
  const v = props.modelValue;
  return v && typeof v === 'object' && !Array.isArray(v) ? v : {};
});
function valoreDi(c) {
  const v = Number(scelti.value[c]) || 0;
  return v === 1 || v === 2 ? v : 0;
}
function classeDi(c) {
  const v = valoreDi(c);
  return v ? 'olo-ms-' + v : '';
}

const imp = computed(() => props.tileSettings || {});
const etichetta1 = computed(() => String(imp.value.etichetta_1 || '').trim() || t('Primo colore'));
const etichetta2 = computed(() => String(imp.value.etichetta_2 || '').trim() || t('Secondo colore'));
function statoDi(c) {
  const v = valoreDi(c);
  return v === 2 ? etichetta2.value : v === 1 ? etichetta1.value : t('senza colore');
}

// I colori della tile come nel canvas (un token si risolve con la palette; se non c'è, la sua riserva)
const stylesStore = useStylesStore();
const variabili = computed(() => ({
  '--ms-1': resolveColorToken(imp.value.colore_1, stylesStore) || '#F59E0B',
  '--ms-2': resolveColorToken(imp.value.colore_2, stylesStore) || '#10B981',
}));

// Contati solo i codici che la cartina disegna
const tutti = computed(() => (dati.value ? [...dati.value.stati, ...(dati.value.puntini || [])] : []));
const conta = computed(() => {
  const n = { 1: 0, 2: 0 };
  for (const s of tutti.value) { const v = valoreDi(s.c); if (v) n[v]++; }
  return n;
});
const perNome = (a, b) => nomeDi(a).localeCompare(nomeDi(b), lingua);
const gruppi = computed(() => [2, 1].map((v) => ({
  v,
  etichetta: v === 2 ? etichetta2.value : etichetta1.value,
  stati: tutti.value.filter((s) => valoreDi(s.c) === v).sort(perNome),
})));

function giro(c) {
  const ora = { ...scelti.value };
  const dopo = (valoreDi(c) + 1) % 3;
  if (dopo) ora[c] = dopo; else delete ora[c];
  emit('update:modelValue', ora);
}
function clicSullaMappa(e) {
  const el = e.target && e.target.closest ? e.target.closest('[data-c]') : null;
  if (el) giro(el.getAttribute('data-c'));
}

const cerca = ref('');
const senzaAccenti = (s) => String(s).normalize('NFD').replace(/[̀-ͯ]/g, '').toLowerCase();
const trovati = computed(() => {
  const q = senzaAccenti(cerca.value.trim());
  if (!q) return [];
  return tutti.value.filter((s) => senzaAccenti(nomeDi(s)).includes(q)).sort(perNome).slice(0, 8);
});

// «Azzera» chiede conferma col secondo clic (niente dialoghi del browser)
const conferma = ref(false);
let timerConferma = null;
function azzera() {
  if (!conferma.value) {
    conferma.value = true;
    clearTimeout(timerConferma);
    timerConferma = setTimeout(() => { conferma.value = false; }, 3000);
    return;
  }
  clearTimeout(timerConferma);
  conferma.value = false;
  emit('update:modelValue', {});
}

// Esc chiude, Tab resta nella finestra, alla chiusura il focus torna al pulsante
const aperto = ref(false);
const finestra = ref(null);
const trap = useFocusTrap(finestra, { onEscape: () => chiudi() });
function apri() { if (dati.value) aperto.value = true; }
function chiudi() { aperto.value = false; }
watch(aperto, (v) => {
  if (v) {
    nextTick(() => trap.activate());
  } else {
    trap.deactivate();
    cerca.value = '';
    conferma.value = false;
  }
});
</script>

<style scoped>
.olo-ms { display: flex; flex-direction: column; gap: 8px; }
.olo-ms-box { border: 1px solid #e2e8f0; border-radius: 8px; background: #f8fafc; padding: 6px; }
.olo-ms-carica { margin: 0; padding: 24px 0; text-align: center; font-size: 11px; color: #64748b; }
.olo-ms-mappa { display: block; width: 100%; height: auto; cursor: pointer; }
.olo-ms-mappa path,
.olo-ms-mappa circle {
  fill: #ffffff;
  stroke: #94a3b8;
  stroke-width: 0.6px;
  vector-effect: non-scaling-stroke;
  stroke-linejoin: round;
  transition: fill .15s ease;
}
.olo-ms-mappa path:hover,
.olo-ms-mappa circle:hover { stroke: #0f172a; stroke-width: 1.4px; }
.olo-ms-mappa .olo-ms-1 { fill: var(--ms-1); }
.olo-ms-mappa .olo-ms-2 { fill: var(--ms-2); }
.olo-ms-riga { display: flex; align-items: center; justify-content: space-between; gap: 8px; flex-wrap: wrap; }
.olo-ms-conti { display: inline-flex; flex-wrap: wrap; gap: 4px 12px; font-size: 11px; color: #475569; }
.olo-ms-conto { display: inline-flex; align-items: center; gap: 5px; }
.olo-ms-conto b { color: #1e293b; font-variant-numeric: tabular-nums; }
.olo-ms-k { display: inline-block; flex: none; width: 9px; height: 9px; border-radius: 50%; border: 1px solid #94a3b8; background: #ffffff; }
.olo-ms-k1 { background: var(--ms-1); border-color: transparent; }
.olo-ms-k2 { background: var(--ms-2); border-color: transparent; }
.olo-ms-apri {
  padding: 5px 10px;
  font: inherit;
  font-size: 11px;
  font-weight: 600;
  color: #1e293b;
  background: #ffffff;
  border: 1px solid #cbd5e1;
  border-radius: 6px;
  cursor: pointer;
}
.olo-ms-apri:hover:not(:disabled) { background: #f1f5f9; }
.olo-ms-apri:disabled { opacity: .5; cursor: default; }

.olo-msd-velo {
  position: fixed;
  inset: 0;
  z-index: 99999; /* sotto i popover dei campi (NumberScrubber 100000, FieldSelect 100090) */
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 16px;
  background: rgba(15, 23, 42, 0.45);
}
.olo-msd {
  display: flex;
  flex-direction: column;
  gap: 12px;
  width: min(1180px, 100%);
  max-height: calc(100vh - 32px);
  overflow-y: auto;
  padding: 16px;
  color: #1e293b;
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  box-shadow: 0 20px 40px rgba(15, 23, 42, 0.18);
  font-family: inherit;
  font-size: 12px;
}
.olo-msd-testa { display: flex; align-items: center; justify-content: space-between; gap: 8px; }
.olo-msd-titolo { display: inline-flex; align-items: center; gap: 6px; margin: 0; font-size: 15px; font-weight: 600; color: #1e293b; }
.olo-msd-chiudi {
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
.olo-msd-chiudi:hover { color: #1e293b; background: #f1f5f9; }
.olo-msd-mappa { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 8px; box-sizing: border-box; }
.olo-msd-sotto { display: grid; grid-template-columns: minmax(0, 260px) minmax(0, 1fr); gap: 16px; align-items: start; }
.olo-msd-cerca { display: flex; flex-direction: column; gap: 6px; }
.olo-msd-input {
  width: 100%;
  box-sizing: border-box;
  height: 32px;
  padding: 0 10px;
  font: inherit;
  font-size: 12px;
  color: #1e293b;
  background: #ffffff;
  border: 1px solid #cbd5e1;
  border-radius: 6px;
  box-shadow: none;
}
.olo-msd-trovati { list-style: none; margin: 0; padding: 0; display: flex; flex-direction: column; gap: 2px; }
.olo-msd-voce {
  display: flex;
  align-items: center;
  gap: 8px;
  width: 100%;
  padding: 6px 8px;
  font: inherit;
  font-size: 12px;
  text-align: left;
  color: #1e293b;
  background: transparent;
  border: 0;
  border-radius: 6px;
  cursor: pointer;
}
.olo-msd-voce:hover { background: #f1f5f9; }
.olo-msd-elenchi { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; }
.olo-msd-gruppo { display: flex; flex-direction: column; gap: 8px; padding: 10px 12px; background: #f9fafb; border: 1px solid #f1f5f9; border-radius: 8px; min-width: 0; }
.olo-msd-gtitolo { display: flex; align-items: center; gap: 6px; margin: 0; font-size: 12px; font-weight: 600; color: #1e293b; }
.olo-msd-gtitolo b { font-variant-numeric: tabular-nums; }
.olo-msd-chips { display: flex; flex-wrap: wrap; gap: 4px; }
.olo-msd-chip {
  padding: 3px 8px;
  font: inherit;
  font-size: 11px;
  color: #1e293b;
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 999px;
  cursor: pointer;
}
.olo-msd-chip:hover { border-color: #94a3b8; }
.olo-msd-vuoto { margin: 0; font-size: 11px; color: #64748b; }
.olo-msd-piede { display: flex; justify-content: space-between; gap: 8px; }
.olo-msd-azzera,
.olo-msd-fatto {
  padding: 7px 14px;
  font: inherit;
  font-size: 12px;
  font-weight: 600;
  border-radius: 6px;
  cursor: pointer;
}
.olo-msd-azzera { color: #b91c1c; background: #ffffff; border: 1px solid #fecaca; }
.olo-msd-azzera:hover:not(:disabled) { background: #fef2f2; }
.olo-msd-azzera:disabled { opacity: .5; cursor: default; }
.olo-msd-fatto { color: #ffffff; background: var(--olo-ui-accent, #e8622a); border: 1px solid transparent; }
.olo-ms-apri:focus-visible,
.olo-msd-chiudi:focus-visible,
.olo-msd-input:focus-visible,
.olo-msd-voce:focus-visible,
.olo-msd-chip:focus-visible,
.olo-msd-azzera:focus-visible,
.olo-msd-fatto:focus-visible {
  outline: 2px solid var(--olo-ui-accent, #e8622a);
  outline-offset: 2px;
}
@media (max-width: 640px) {
  .olo-msd-sotto, .olo-msd-elenchi { grid-template-columns: minmax(0, 1fr); }
}
</style>
