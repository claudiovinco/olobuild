<template>
  <div class="cc">
    <div v-for="(c, i) in campi" :key="c._id" class="cc-campo">
      <div class="cc-riga">
        <div class="cfg-input cc-etichetta">
          <input type="text" :value="c.etichetta" maxlength="60" :placeholder="t('Nome del campo')" :aria-label="t('Nome del campo')" @input="setEtichetta(c, $event.target.value)" />
        </div>
        <CfgSelect class="cc-tipo" :model-value="c.tipo" :options="TIPI" @update:model-value="set(c, 'tipo', $event)" />
        <div class="cfg-segment cc-larghezza" role="group" :aria-label="t('Larghezza')">
          <button v-for="l in LARGHEZZE" :key="l.value" type="button" :class="{ 'is-on': c.larghezza === l.value }" :aria-pressed="c.larghezza === l.value ? 'true' : 'false'" @click="set(c, 'larghezza', l.value)">{{ t(l.label) }}</button>
        </div>
        <div class="cc-azioni">
          <button type="button" class="cfg-btn-icon" :disabled="i === 0" :title="t('Sposta prima')" :aria-label="t('Sposta prima')" @click="sposta(i, -1)">↑</button>
          <button type="button" class="cfg-btn-icon" :disabled="i === campi.length - 1" :title="t('Sposta dopo')" :aria-label="t('Sposta dopo')" @click="sposta(i, 1)">↓</button>
          <button type="button" class="cfg-btn-icon cfg-btn-danger" :title="t('Togli il campo')" :aria-label="t('Togli il campo')" @click="togli(i)">×</button>
        </div>
      </div>
      <div class="cc-dettagli">
        <label class="cc-chiave">
          <span>{{ t('Chiave') }}</span>
          <input type="text" :value="c.chiave" maxlength="40" spellcheck="false" @input="setChiave(c, $event.target.value)" />
        </label>
        <div class="cfg-input cc-aiuto">
          <input type="text" :value="c.aiuto" maxlength="300" :placeholder="t('Suggerimento per chi scrive (facoltativo)')" :aria-label="t('Suggerimento per chi scrive')" @input="set(c, 'aiuto', $event.target.value)" />
        </div>
      </div>
      <textarea v-if="c.tipo === 'scelta'" class="cc-opzioni" rows="3" :value="c.opzioni" :placeholder="t('Una scelta per riga')" :aria-label="t('Scelte')" @input="set(c, 'opzioni', $event.target.value)"></textarea>
      <div v-if="c.tipo === 'testo_social'" class="cfg-segment cc-reti" role="group" :aria-label="t('Per quali reti')">
        <button v-for="r in RETI" :key="r.value" type="button" :class="{ 'is-on': (c.reti || 'tutte') === r.value }" :aria-pressed="(c.reti || 'tutte') === r.value ? 'true' : 'false'" @click="set(c, 'reti', r.value)">{{ r.label }}</button>
      </div>
      <div v-if="categorie.length" class="cc-categorie">
        <span class="cc-categorie-nome">{{ t('Solo nelle categorie') }}</span>
        <span v-for="id in (c.categorie || [])" :key="id" class="cc-chip">
          {{ nomeCategoria(id) }}
          <button type="button" :aria-label="t('Togli') + ': ' + nomeCategoria(id)" :title="t('Togli')" @click="togliCategoria(c, id)">×</button>
        </span>
        <select class="cc-categorie-scegli" :aria-label="t('Aggiungi una categoria')" @change="aggiungiCategoria(c, $event)">
          <option value="">{{ (c.categorie || []).length ? t('Aggiungi una categoria…') : t('In tutte le categorie') }}</option>
          <option v-for="x in categorie.filter((x) => !(c.categorie || []).includes(x.value))" :key="x.value" :value="x.value">{{ x.label }}</option>
        </select>
      </div>
      <div v-if="c.tipo === 'foto_social'" class="cc-formati" role="group" :aria-label="t('Formati')">
        <label v-for="f in formati" :key="f.value">
          <input type="checkbox" :checked="(c.formati || []).includes(f.value)" @change="setFormato(c, f.value, $event.target.checked)" />
          <span>{{ f.label }}</span>
        </label>
      </div>
    </div>
    <button type="button" class="cfg-btn cfg-btn-ghost cc-aggiungi" @click="aggiungi">
      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
      {{ t('Aggiungi un campo') }}
    </button>
  </div>
</template>

<script setup>
import { t } from '@/i18n';
import CfgSelect from './CfgSelect.vue';

/** I campi di un tipo di contenuto (Olobuild_Tipi_Contenuto::normalizza_campi): si modificano sul posto. */
const props = defineProps({
  campi: { type: Array, required: true },
  formati: { type: Array, default: () => [] },
  // Le categorie del tipo ([{ value, label }]): con almeno una, ogni campo può valere solo in alcune.
  categorie: { type: Array, default: () => [] },
});
const emit = defineEmits(['cambia']);

const TIPI = [
  { value: 'testo', label: t('Testo') },
  { value: 'testo_lungo', label: t('Testo lungo') },
  { value: 'formattato', label: t('Testo formattato') },
  { value: 'numero', label: t('Numero') },
  { value: 'data', label: t('Data') },
  { value: 'ora', label: t('Ora') },
  { value: 'link', label: t('Link') },
  { value: 'scelta', label: t('Scelta da un elenco') },
  { value: 'sino', label: t('Sì / no') },
  { value: 'immagine', label: t('Foto') },
  { value: 'galleria', label: t('Galleria') },
  { value: 'foto_social', label: t('Foto per i social') },
  { value: 'testo_social', label: t('Testo per i social') },
  { value: 'allegati', label: t('Allegati') },
];
// «Testo per i social»: OLOsocial lo usa come testo di partenza della rete (Olobuild_Tipi_Contenuto::testo_per_rete).
const RETI = [
  { value: 'tutte', label: t('Tutte le reti') },
  { value: 'instagram', label: 'Instagram' },
  { value: 'altre', label: t('Facebook, LinkedIn e le altre') },
];
const LARGHEZZE = [
  { value: 'piena', label: 'Intera' },
  { value: 'meta', label: 'Metà' },
  { value: 'terzo', label: 'Un terzo' },
];

let prossimo = 1;
function nuovoId() { prossimo += 1; return 'c' + Date.now().toString(36) + prossimo; }
props.campi.forEach((c) => { if (!c._id) c._id = nuovoId(); });

// La chiave segue il nome finché chi configura non la scrive a mano.
function chiaveDa(testo) {
  return String(testo || '').normalize('NFD').replace(/[̀-ͯ]/g, '').toLowerCase()
    .replace(/[^a-z0-9]+/g, '_').replace(/^_+|_+$/g, '').slice(0, 40);
}
function set(c, k, v) { c[k] = v; emit('cambia'); }
function setEtichetta(c, v) {
  if (!c._chiaveScritta) c.chiave = chiaveDa(v);
  set(c, 'etichetta', v);
}
function setChiave(c, v) { c._chiaveScritta = true; set(c, 'chiave', chiaveDa(v)); }
function setFormato(c, f, acceso) {
  const s = new Set(c.formati || []);
  if (acceso) s.add(f); else s.delete(f);
  set(c, 'formati', props.formati.map((x) => x.value).filter((x) => s.has(x)));
}
function nomeCategoria(id) {
  const x = props.categorie.find((v) => v.value === id);
  return x ? x.label : t('Categoria tolta');
}
function aggiungiCategoria(c, e) {
  const id = parseInt(e.target.value, 10);
  e.target.value = '';
  if (!id || (c.categorie || []).includes(id)) return;
  set(c, 'categorie', [...(c.categorie || []), id]);
}
function togliCategoria(c, id) {
  set(c, 'categorie', (c.categorie || []).filter((x) => x !== id));
}
function aggiungi() {
  props.campi.push({ _id: nuovoId(), etichetta: '', chiave: '', tipo: 'testo', larghezza: 'piena', aiuto: '', opzioni: '', formati: props.formati.map((f) => f.value), reti: 'tutte', categorie: [] });
  emit('cambia');
}
function togli(i) {
  if (!confirm(t('Togliere questo campo? I valori già scritti restano nei contenuti, ma la scheda non li mostra più.'))) return;
  props.campi.splice(i, 1);
  emit('cambia');
}
function sposta(i, d) {
  const j = i + d;
  if (j < 0 || j >= props.campi.length) return;
  const [c] = props.campi.splice(i, 1);
  props.campi.splice(j, 0, c);
  emit('cambia');
}
</script>

<style scoped>
.cc { display: flex; flex-direction: column; gap: 10px; }
.cc-campo { display: flex; flex-direction: column; gap: 8px; padding: 10px; border: 1px solid var(--c-line); border-radius: 8px; }
.cc-riga { display: grid; grid-template-columns: minmax(0, 1.4fr) minmax(0, 1fr) auto auto; gap: 8px; align-items: center; }
.cc-azioni { display: flex; gap: 4px; }
.cc-azioni button:disabled { opacity: .35; cursor: not-allowed; }
.cc-dettagli { display: grid; grid-template-columns: minmax(0, 220px) minmax(0, 1fr); gap: 8px; align-items: center; }
.cc-chiave { display: flex; align-items: center; gap: 6px; font-size: 12px; color: var(--c-text-mute); }
.cc-chiave input { flex: 1; min-width: 0; font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-size: 12px; padding: 4px 6px; border: 1px solid var(--c-line); border-radius: 6px; }
.cc-opzioni { width: 100%; padding: 6px 8px; border: 1px solid var(--c-line); border-radius: 6px; font: inherit; resize: vertical; }
.cc-formati { display: flex; flex-wrap: wrap; gap: 6px 16px; font-size: 12px; }
.cc-reti { align-self: flex-start; flex-wrap: wrap; }
.cc-categorie { display: flex; flex-wrap: wrap; align-items: center; gap: 6px 8px; font-size: 12px; }
.cc-categorie-nome { color: var(--c-text-mute); }
.cc-chip { display: inline-flex; align-items: center; gap: 4px; padding: 2px 4px 2px 10px; border: 1px solid var(--c-line); border-radius: 999px; background: var(--c-line-soft); }
.cc-chip button { width: 20px; height: 20px; padding: 0; border: 0; border-radius: 50%; background: transparent; color: inherit; font-size: 14px; line-height: 1; cursor: pointer; }
.cc-chip button:hover { background: var(--c-line); }
.cc-categorie-scegli { min-width: 0; max-width: 260px; padding: 3px 6px; border: 1px solid var(--c-line); border-radius: 6px; font: inherit; font-size: 12px; }
.cc-formati label { display: inline-flex; align-items: center; gap: 6px; cursor: pointer; }
.cc-aggiungi { align-self: flex-start; padding: 6px 10px; }
.cc button:focus-visible, .cc input:focus-visible, .cc textarea:focus-visible, .cc select:focus-visible { outline: 2px solid var(--c-red); outline-offset: 2px; }
@media (max-width: 720px) {
  .cc-riga { grid-template-columns: minmax(0, 1fr) auto; }
  .cc-etichetta { grid-column: 1 / -1; }
  .cc-dettagli { grid-template-columns: minmax(0, 1fr); }
}
</style>
