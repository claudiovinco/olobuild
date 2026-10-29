<template>
  <div class="cc">
    <p v-if="!campi.length" class="cc-vuoto">{{ t('Ancora nessun campo.') }}</p>
    <div v-for="(c, i) in campi" :key="c._id" class="cc-campo" :class="{ 'is-aperto': aperto(c) }">
      <!-- Chiuso: una riga col nome e il riassunto delle scelte; un clic lo apre -->
      <div class="cc-testa">
        <button type="button" class="cc-apri" :aria-expanded="aperto(c) ? 'true' : 'false'" @click="alterna(c)">
          <svg class="cc-chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m9 6 6 6-6 6"/></svg>
          <span class="cc-icona" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" v-html="ICONE[c.tipo] || ICONE.testo"></svg>
          </span>
          <span class="cc-testi">
            <span class="cc-nome" :class="{ 'is-vuoto': !c.etichetta }">{{ c.etichetta || t('Campo senza nome') }}</span>
            <span class="cc-sommario">{{ sommario(c) }}</span>
          </span>
        </button>
        <div class="cc-azioni">
          <button type="button" class="cfg-btn cfg-btn-ghost cfg-btn-icon" :disabled="i === 0" :title="t('Sposta prima')" :aria-label="t('Sposta prima')" @click="sposta(i, -1)">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 19V5M5 12l7-7 7 7"/></svg>
          </button>
          <button type="button" class="cfg-btn cfg-btn-ghost cfg-btn-icon" :disabled="i === campi.length - 1" :title="t('Sposta dopo')" :aria-label="t('Sposta dopo')" @click="sposta(i, 1)">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 5v14M19 12l-7 7-7-7"/></svg>
          </button>
          <button type="button" class="cfg-btn cfg-btn-ghost cfg-btn-icon cc-togli" :title="t('Togli il campo')" :aria-label="t('Togli il campo')" @click="togli(i)">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6M10 11v6M14 11v6"/></svg>
          </button>
        </div>
      </div>

      <!-- Aperto: ogni scelta con la sua etichetta sopra -->
      <div v-if="aperto(c)" class="cc-corpo">
        <label class="cc-voce">
          <span class="cc-lbl">{{ t('Nome del campo') }}</span>
          <span class="cfg-input"><input :ref="(el) => { if (el) nomi[c._id] = el; }" type="text" :value="c.etichetta" maxlength="60" :placeholder="t('Per esempio Allegati')" @input="setEtichetta(c, $event.target.value)" /></span>
        </label>
        <div class="cc-voce">
          <span class="cc-lbl">{{ t('Tipo') }}</span>
          <CfgSelect :model-value="c.tipo" :options="TIPI" @update:model-value="set(c, 'tipo', $event)" />
        </div>
        <div class="cc-voce">
          <span class="cc-lbl">{{ t('Larghezza nella scheda') }}</span>
          <div class="cfg-segment" role="group" :aria-label="t('Larghezza nella scheda')">
            <button v-for="l in LARGHEZZE" :key="l.value" type="button" :class="{ 'is-on': c.larghezza === l.value }" :aria-pressed="c.larghezza === l.value ? 'true' : 'false'" @click="set(c, 'larghezza', l.value)">{{ t(l.label) }}</button>
          </div>
        </div>
        <label class="cc-voce">
          <span class="cc-lbl">{{ t('Chiave') }}</span>
          <span class="cfg-input mono"><input type="text" :value="c.chiave" maxlength="40" spellcheck="false" @input="setChiave(c, $event.target.value)" /></span>
        </label>
        <label class="cc-voce is-intera">
          <span class="cc-lbl">{{ t('Suggerimento per chi scrive') }}</span>
          <span class="cfg-input"><input type="text" :value="c.aiuto" maxlength="300" :placeholder="t('Facoltativo: compare sotto il campo')" @input="set(c, 'aiuto', $event.target.value)" /></span>
        </label>

        <label v-if="c.tipo === 'scelta'" class="cc-voce is-intera">
          <span class="cc-lbl">{{ t('Scelte') }}</span>
          <span class="cfg-textarea"><textarea rows="3" :value="c.opzioni" :placeholder="t('Una scelta per riga')" @input="set(c, 'opzioni', $event.target.value)"></textarea></span>
        </label>
        <div v-if="c.tipo === 'testo_social'" class="cc-voce is-intera">
          <span class="cc-lbl">{{ t('Per quali reti') }}</span>
          <div class="cfg-segment cc-a-capo" role="group" :aria-label="t('Per quali reti')">
            <button v-for="r in RETI" :key="r.value" type="button" :class="{ 'is-on': (c.reti || 'tutte') === r.value }" :aria-pressed="(c.reti || 'tutte') === r.value ? 'true' : 'false'" @click="set(c, 'reti', r.value)">{{ r.label }}</button>
          </div>
        </div>
        <div v-if="c.tipo === 'foto_social'" class="cc-voce is-intera">
          <span class="cc-lbl">{{ t('Formati') }}</span>
          <div class="cc-formati" role="group" :aria-label="t('Formati')">
            <label v-for="f in formati" :key="f.value" class="cc-formato" :class="{ 'is-on': (c.formati || []).includes(f.value) }">
              <input type="checkbox" class="cc-nascosto" :checked="(c.formati || []).includes(f.value)" @change="setFormato(c, f.value, $event.target.checked)" />
              <svg class="cc-spunta" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12.5 4.5 4.5L19 7.5"/></svg>
              <span>{{ f.label }}</span>
            </label>
          </div>
        </div>

        <div v-if="categorie.length" class="cc-voce is-intera">
          <span class="cc-lbl">{{ t('Dove compare') }}</span>
          <div class="cfg-segment cc-a-capo" role="group" :aria-label="t('Dove compare')">
            <button type="button" :class="{ 'is-on': !soloAlcune(c) }" :aria-pressed="soloAlcune(c) ? 'false' : 'true'" @click="inTutte(c)">{{ t('In tutte le categorie') }}</button>
            <button type="button" :class="{ 'is-on': soloAlcune(c) }" :aria-pressed="soloAlcune(c) ? 'true' : 'false'" @click="alcune.add(c._id)">{{ t('Solo in alcune') }}</button>
          </div>
          <div v-if="soloAlcune(c)" class="cc-categorie">
            <span v-for="id in (c.categorie || [])" :key="id" class="cc-chip">
              {{ nomeCategoria(id) }}
              <button type="button" :aria-label="t('Togli') + ': ' + nomeCategoria(id)" :title="t('Togli')" @click="togliCategoria(c, id)">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"/></svg>
              </button>
            </span>
            <CfgSelect
              v-if="restanti(c).length"
              size="sm"
              model-value=""
              :options="restanti(c)"
              :placeholder="t('Aggiungi una categoria…')"
              :searchable="restanti(c).length > 8"
              @update:model-value="aggiungiCategoria(c, $event)"
            />
          </div>
        </div>
      </div>
    </div>
    <button type="button" class="cfg-btn cfg-btn-secondary cc-aggiungi" @click="aggiungi">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
      {{ t('Aggiungi un campo') }}
    </button>
  </div>
</template>

<script setup>
import { reactive, nextTick } from 'vue';
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
// L'icona di ogni tipo, nella riga del campo.
const ICONE = {
  testo: '<path d="M4 7V5h16v2M9 19h6M12 5v14"/>',
  testo_lungo: '<path d="M4 6h16M4 12h16M4 18h10"/>',
  formattato: '<path d="M7 5h6a3.5 3.5 0 0 1 0 7H7zM7 12h7a3.5 3.5 0 0 1 0 7H7z"/>',
  numero: '<path d="M5 9h14M5 15h14M10 4 8 20M16 4l-2 16"/>',
  data: '<rect x="3.5" y="5" width="17" height="15" rx="2"/><path d="M8 3v4M16 3v4M3.5 10h17"/>',
  ora: '<circle cx="12" cy="12" r="8.5"/><path d="M12 7.5V12l3 2"/>',
  link: '<path d="M10 14a4.5 4.5 0 0 0 6.4 0l3-3a4.5 4.5 0 0 0-6.4-6.4l-1.2 1.2M14 10a4.5 4.5 0 0 0-6.4 0l-3 3a4.5 4.5 0 0 0 6.4 6.4l1.2-1.2"/>',
  scelta: '<path d="M9 6h11M9 12h11M9 18h11M4.5 6h.01M4.5 12h.01M4.5 18h.01"/>',
  sino: '<rect x="2.5" y="7" width="19" height="10" rx="5"/><circle cx="16.5" cy="12" r="2.5"/>',
  immagine: '<rect x="3.5" y="4" width="17" height="16" rx="2"/><circle cx="9" cy="9.5" r="1.8"/><path d="m20.5 15-5-5-9 10"/>',
  galleria: '<rect x="7" y="7" width="14" height="13" rx="2"/><path d="M3 16V5a2 2 0 0 1 2-2h11"/><path d="m21 16-4-4-6 8"/>',
  foto_social: '<path d="M4 8h3l2-3h6l2 3h3v11H4z"/><circle cx="12" cy="13" r="3.5"/>',
  testo_social: '<circle cx="18" cy="5.5" r="2.5"/><circle cx="6" cy="12" r="2.5"/><circle cx="18" cy="18.5" r="2.5"/><path d="m8.2 13.3 7.6 4M15.8 6.7l-7.6 4"/>',
  allegati: '<path d="m20.5 11.5-8.4 8.4a5.5 5.5 0 0 1-7.8-7.8l8.8-8.8a3.7 3.7 0 0 1 5.2 5.2l-8.8 8.8a1.8 1.8 0 0 1-2.6-2.6l8.1-8.1"/>',
};
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

// Aperti e «Solo in alcune» stanno qui, non nei campi: aprire una riga non è una modifica da salvare.
const aperti = reactive(new Set());
const alcune = reactive(new Set());
const nomi = {};
function aperto(c) { return aperti.has(c._id); }
function alterna(c) { if (aperti.has(c._id)) aperti.delete(c._id); else aperti.add(c._id); }

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

/** La riga del campo chiuso: tipo, larghezza se non intera, scelte, reti e categorie. */
function sommario(c) {
  const tipo = (TIPI.find((x) => x.value === c.tipo) || TIPI[0]).label;
  // Il tipo si ripete solo se il nome non lo dice già («Allegati · Allegati»).
  const parti = tipo === c.etichetta ? [] : [tipo];
  const l = LARGHEZZE.find((x) => x.value === c.larghezza);
  if (l && l.value !== 'piena') parti.push(t(l.label));
  if (c.tipo === 'scelta') {
    const n = String(c.opzioni || '').split('\n').filter((x) => x.trim()).length;
    parti.push(n === 1 ? t('1 scelta') : t('{n} scelte').replace('{n}', n));
  }
  if (c.tipo === 'testo_social') parti.push((RETI.find((x) => x.value === (c.reti || 'tutte')) || RETI[0]).label);
  if ((c.categorie || []).length) parti.push(t('solo in {nomi}').replace('{nomi}', c.categorie.map(nomeCategoria).join(', ')));
  else if (props.categorie.length) parti.push(t('in tutte le categorie'));
  const s = parti.join(' · ');
  return s ? s.charAt(0).toUpperCase() + s.slice(1) : tipo;
}

function nomeCategoria(id) {
  const x = props.categorie.find((v) => v.value === id);
  return x ? x.label : t('Categoria tolta');
}
function soloAlcune(c) { return (c.categorie || []).length > 0 || alcune.has(c._id); }
function restanti(c) { return props.categorie.filter((x) => !(c.categorie || []).includes(x.value)); }
function inTutte(c) {
  alcune.delete(c._id);
  if ((c.categorie || []).length) set(c, 'categorie', []);
}
function aggiungiCategoria(c, v) {
  const id = parseInt(v, 10);
  if (!id || (c.categorie || []).includes(id)) return;
  set(c, 'categorie', [...(c.categorie || []), id]);
}
function togliCategoria(c, id) {
  set(c, 'categorie', (c.categorie || []).filter((x) => x !== id));
}
function aggiungi() {
  const c = { _id: nuovoId(), etichetta: '', chiave: '', tipo: 'testo', larghezza: 'piena', aiuto: '', opzioni: '', formati: props.formati.map((f) => f.value), reti: 'tutte', categorie: [] };
  props.campi.push(c);
  aperti.add(c._id);
  emit('cambia');
  nextTick(() => nomi[c._id]?.focus());
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
.cc { display: flex; flex-direction: column; gap: 8px; max-width: 760px; }
.cc-vuoto { margin: 0; padding: 14px 16px; border: 1px dashed var(--c-line); border-radius: 10px; color: var(--c-text-mute); font-size: 13px; }
.cc-campo { border: 1px solid var(--c-line); border-radius: 10px; background: #fff; transition: box-shadow .15s, border-color .15s; }
.cc-campo.is-aperto { box-shadow: 0 2px 8px rgba(15, 23, 42, .06); }

.cc-testa { display: flex; align-items: center; gap: 6px; padding: 6px 8px 6px 6px; }
.cc-apri { flex: 1; min-width: 0; display: flex; align-items: center; gap: 10px; padding: 6px 8px; border: 0; border-radius: 8px; background: transparent; color: inherit; font: inherit; text-align: left; cursor: pointer; }
.cc-apri:hover { background: var(--c-bg); }
.cc-chev { flex: none; width: 16px; height: 16px; color: var(--c-text-faint); transition: transform .15s; }
.is-aperto .cc-chev { transform: rotate(90deg); }
.cc-icona { flex: none; display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 8px; background: var(--c-red-soft); color: var(--c-red); }
.cc-icona svg { width: 16px; height: 16px; }
.cc-testi { min-width: 0; display: flex; flex-direction: column; gap: 2px; }
.cc-nome, .cc-sommario { overflow: hidden; white-space: nowrap; text-overflow: ellipsis; }
.cc-nome { font-size: 13.5px; font-weight: 600; color: var(--c-navy); }
.cc-nome.is-vuoto { font-weight: 500; color: var(--c-text-faint); }
.cc-sommario { font-size: 12px; color: var(--c-text-mute); }
.cc-azioni { flex: none; display: flex; gap: 2px; }
.cc-azioni .cfg-btn-icon { width: 30px; height: 30px; }
.cc-azioni .cfg-btn-icon:disabled { opacity: .3; }
.cc-togli:hover:not(:disabled) { color: var(--c-red); background: var(--c-red-soft); }

.cc-corpo { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px 16px; padding: 14px 16px 16px; border-top: 1px solid var(--c-line-soft); }
.cc-voce { display: flex; flex-direction: column; align-items: flex-start; gap: 6px; min-width: 0; }
.cc-voce.is-intera { grid-column: 1 / -1; }
.cc-lbl { font-size: 12px; font-weight: 600; color: var(--c-text-mute); }
.cc .cc-voce .cfg-input, .cc .cc-voce .cfg-textarea, .cc .cc-voce > .cfg-select { width: 100%; max-width: none; }
.cc .cc-voce .cfg-input, .cc .cc-voce .cfg-textarea { box-sizing: border-box; }
/* Alti come il menu del Tipo accanto: niente altezza minima ereditata dagli input della pagina. */
.cc .cc-voce .cfg-input { min-height: 38px; padding: 0 12px; }
.cc .cc-voce .cfg-input input { height: 36px; min-height: 0; }
.cc-voce .cfg-textarea { min-height: 0; font-family: inherit; font-size: 13.5px; }
.cc-a-capo { flex-wrap: wrap; }
.cc-formati { display: flex; flex-wrap: wrap; gap: 8px; }
.cc-formato { position: relative; display: inline-flex; align-items: center; gap: 8px; padding: 7px 12px; border: 1px solid var(--c-line); border-radius: 8px; background: #fff; font-size: 12.5px; color: var(--c-text-mute); cursor: pointer; }
.cc-formato.is-on { border-color: var(--c-red); background: var(--c-red-soft); color: var(--c-navy); }
.cc-formato:focus-within { outline: 2px solid var(--c-red); outline-offset: 2px; }
/* La casella vera resta per tastiera e lettori di schermo; si vede la spunta nel colore del marchio. */
.cc .cc-nascosto { position: absolute; width: 1px; height: 1px; margin: 0; opacity: 0; pointer-events: none; }
.cc-spunta { flex: none; box-sizing: border-box; width: 16px; height: 16px; padding: 1px; border: 1.5px solid var(--c-text-faint); border-radius: 4px; background: #fff; color: transparent; }
.cc-formato.is-on .cc-spunta { border-color: var(--c-red); background: var(--c-red); color: #fff; }
.cc-categorie { display: flex; flex-wrap: wrap; align-items: center; gap: 6px 8px; }
.cc-chip { display: inline-flex; align-items: center; gap: 4px; padding: 3px 4px 3px 10px; border: 1px solid var(--c-line); border-radius: 999px; background: var(--c-bg); font-size: 12.5px; color: var(--c-navy); }
.cc-chip button { display: inline-flex; align-items: center; justify-content: center; width: 20px; height: 20px; padding: 0; border: 0; border-radius: 50%; background: transparent; color: var(--c-text-mute); cursor: pointer; }
.cc-chip button svg { width: 11px; height: 11px; }
.cc-chip button:hover { background: var(--c-line); color: var(--c-navy); }
.cc-aggiungi { align-self: flex-start; }
.cc button:focus-visible, .cc input:focus-visible, .cc textarea:focus-visible { outline: 2px solid var(--c-red); outline-offset: 2px; }
@media (max-width: 640px) {
  .cc-corpo { grid-template-columns: minmax(0, 1fr); padding: 12px; }
  .cc-icona { display: none; }
}
</style>
