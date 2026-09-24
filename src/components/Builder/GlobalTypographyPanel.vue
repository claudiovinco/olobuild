<template>
  <div class="gtp-root">
    <div class="gtp-header">
      <h3 class="gtp-title">{{ t('Tipografia Globale') }}</h3>
      <p class="gtp-desc">{{ t('Definisci set tipografici riutilizzabili tramite variabili CSS.') }}</p>
    </div>

    <div class="gtp-list">
      <div
        v-for="(set, index) in localSets"
        :key="set._uid"
        class="gtp-item"
      >
        <div class="gtp-item-header">
          <input
            type="text"
            :ref="el => registraNome(set._uid, el)"
            :value="set.label"
            @input="updateField(index, 'label', $event.target.value)"
            :placeholder="t('Nome set')"
            class="gtp-input gtp-input--label"
          />
          <button
            class="gtp-remove"
            @click="removeSet(index)"
            :title="t('Rimuovi set')"
          >
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
          </button>
        </div>

        <div class="gtp-fields">
          <!-- Font Family -->
          <div class="gtp-field">
            <label class="gtp-field-label">{{ t('Font family') }}</label>
            <FieldFontFamily
              :modelValue="set.family"
              @update:modelValue="updateField(index, 'family', $event)"
            />
          </div>

          <!-- Weight -->
          <div class="gtp-field">
            <label class="gtp-field-label">{{ t('Peso') }}</label>
            <FieldSelect
              ui="dropdown"
              :modelValue="set.weight"
              :options="weightOptions"
              @update:modelValue="updateField(index, 'weight', $event)"
            />
          </div>

          <!-- Transform -->
          <div class="gtp-field">
            <label class="gtp-field-label">{{ t('Trasformazione') }}</label>
            <FieldSelect
              ui="dropdown"
              :modelValue="set.transform"
              :options="transformOptions"
              @update:modelValue="updateField(index, 'transform', $event)"
            />
          </div>

          <!-- Line Height -->
          <div class="gtp-field">
            <label class="gtp-field-label">{{ t('Line height') }}</label>
            <input
              type="text"
              :value="set.line_height"
              @input="updateField(index, 'line_height', $event.target.value)"
              class="gtp-input gtp-input--small"
            />
          </div>

          <!-- Letter Spacing -->
          <div class="gtp-field">
            <label class="gtp-field-label">{{ t('Letter spacing (px)') }}</label>
            <input
              type="text"
              :value="set.letter_spacing"
              @input="updateField(index, 'letter_spacing', $event.target.value)"
              class="gtp-input gtp-input--small"
            />
          </div>
        </div>

        <div class="gtp-vars">
          <span class="gtp-var" v-if="set.family">var(--olo-font-{{ set.id }}-family)</span>
          <span class="gtp-var">var(--olo-font-{{ set.id }}-weight)</span>
        </div>

        <!-- Preview -->
        <div
          class="gtp-preview"
          :style="{
            fontFamily: famigliaCss(set.family),
            fontWeight: set.weight,
            textTransform: set.transform === 'none' ? 'initial' : set.transform,
            lineHeight: set.line_height || 'normal',
            letterSpacing: set.letter_spacing === '' || set.letter_spacing == null ? 'normal' : set.letter_spacing + 'px',
          }"
        >
          {{ t('Anteprima del testo - Abc 123') }}
        </div>
      </div>
    </div>

    <button class="gtp-add" @click="addSet">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
      </svg>
      {{ t('Aggiungi set tipografico') }}
    </button>

    <div class="gtp-actions">
      <button
        class="gtp-save"
        :disabled="!isDirty || isSaving"
        @click="save"
      >
        {{ isSaving ? t('Salvataggio...') : t('Salva tipografia') }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { t } from '@/i18n';
import { ref, watch, computed, nextTick, onBeforeUnmount } from 'vue';
import { useStylesStore } from '@/stores/styles';
import { useToast } from '@/composables/useToast.js';
import FieldFontFamily from './fields/FieldFontFamily.vue';
import FieldSelect from './fields/FieldSelect.vue';

const stylesStore = useStylesStore();
const toast = useToast();

// Opzioni dei dropdown custom (FieldSelect applica t() alle label).
// Value stringa: set.weight/set.transform restano stringhe come col select nativo.
const weightOptions = [
  { value: '100', label: '100 - Thin' },
  { value: '200', label: '200 - Extra Light' },
  { value: '300', label: '300 - Light' },
  { value: '400', label: '400 - Regular' },
  { value: '500', label: '500 - Medium' },
  { value: '600', label: '600 - Semi Bold' },
  { value: '700', label: '700 - Bold' },
  { value: '800', label: '800 - Extra Bold' },
  { value: '900', label: '900 - Black' },
];
const transformOptions = [
  { value: 'none', label: 'Nessuna' },
  { value: 'uppercase', label: 'MAIUSCOLO' },
  { value: 'lowercase', label: 'minuscolo' },
  { value: 'capitalize', label: 'Capitalizza' },
];

// `_uid` NON si salva: serve solo come chiave stabile del v-for. Con la chiave
// derivata dall'id — che si rigenera dal nome a ogni tasto — Vue smontava e
// rimontava l'input e il focus si perdeva dopo una lettera sola.
let contatoreUid = 0;
function normalizza(sets, nuovi = false) {
  return (sets || []).map((s) => ({
    ...s,
    _uid: 'u' + (++contatoreUid),
    _nuovo: nuovi,
  }));
}

// Chiudendo il pannello con un salvataggio fallito o ancora in volo, la bozza
// resta nello store: riaprendo la si ritrova, ancora «da salvare». Un set che
// non è fra quelli salvati è nuovo: il suo id segue ancora il nome.
const bozza = stylesStore.globalTypographyDraft;
const localSets = ref(bozza ? normalizzaBozza(bozza) : normalizza(JSON.parse(JSON.stringify(stylesStore.globalTypography || []))));

function normalizzaBozza(sets) {
  const salvati = new Set((stylesStore.globalTypography || []).map((s) => s && s.id));
  return normalizza(JSON.parse(JSON.stringify(sets))).map((s) => ({ ...s, _nuovo: !salvati.has(s.id) }));
}

const isDirty = ref(!!bozza);
const isSaving = computed(() => stylesStore.savingTypography);

// Se il sito non ha ancora set, la lista parte con una proposta: sono set nuovi,
// quindi il pulsante «Salva» deve essere attivo (altrimenti non si salvano mai).
if (!bozza && localSets.value.length === 0) {
  localSets.value = normalizza([
    { id: 'heading', label: 'Titoli', family: 'Montserrat', weight: '700', transform: 'none', line_height: '1.3', letter_spacing: '0' },
    { id: 'subheading', label: 'Sottotitoli', family: 'Montserrat', weight: '500', transform: 'none', line_height: '1.4', letter_spacing: '0.5' },
    { id: 'body', label: 'Corpo testo', family: 'Open Sans', weight: '400', transform: 'none', line_height: '1.6', letter_spacing: '0' },
    { id: 'small', label: 'Testo piccolo', family: 'Open Sans', weight: '400', transform: 'none', line_height: '1.5', letter_spacing: '0' },
  ], true);
  isDirty.value = true;
}

// Lavoro mandato a salvare: pannello nato da una bozza, oppure «Salva» premuto
// (in volo, fallito, o riuscito con modifiche fatte durante il volo). Chiudendo
// il pannello non si butta. Una modifica mai mandata a salvare si scarta alla
// chiusura, come prima.
let daSalvare = !!bozza;
let inVolo = false;
// La bozza da cui il pannello è nato, com'era all'apertura: se la scrive un
// salvataggio partito prima (in volo alla chiusura, o il «Riprova» del vecchio
// toast) e qui non è cambiato niente, il pannello torna pulito.
let nataDa = bozza ? JSON.stringify(puliti()) : null;

// Il nome dei set appena aggiunti va messo a fuoco: si scrive subito.
const campiNome = new Map();
function registraNome(uid, el) {
  if (el) campiNome.set(uid, el);
  else campiNome.delete(uid);
}

function famigliaCss(family) {
  if (!family) return 'inherit';
  // I valori possono essere un var() di ruolo, uno stack web-safe o un nome
  // singolo: la riserva si aggiunge solo all'ultimo caso.
  if (family.includes(',') || family.startsWith('var(')) return family;
  return family + ', sans-serif';
}

function slug(label) {
  return String(label || '')
    .toLowerCase()
    .replace(/[^a-z0-9]+/g, '-')
    .replace(/^-|-$/g, '');
}

function idLibero(base, index) {
  const radice = base || 'set';
  let id = radice;
  let n = 2;
  while (localSets.value.some((s, i) => i !== index && s.id === id)) {
    id = radice + '-' + (n++);
  }
  return id;
}

function updateField(index, field, value) {
  localSets.value[index][field] = value;
  // L'id è il nome della variabile CSS (--olo-font-<id>-family): rinominare un
  // set già salvato lo staccherebbe dalle tile che lo usano. Si rigenera solo
  // finché il set non è mai stato salvato.
  if (field === 'label' && localSets.value[index]._nuovo) {
    localSets.value[index].id = idLibero(slug(value), index);
  }
  isDirty.value = true;
}

async function addSet() {
  const n = localSets.value.length + 1;
  const [set] = normalizza([{
    id: '',
    label: 'Set ' + n,
    family: '',
    weight: '400',
    transform: 'none',
    line_height: '1.5',
    letter_spacing: '0',
  }], true);
  localSets.value.push(set);
  localSets.value[localSets.value.length - 1].id = idLibero(slug(set.label), localSets.value.length - 1);
  isDirty.value = true;
  await nextTick();
  const el = campiNome.get(set._uid);
  if (el) {
    el.scrollIntoView({ block: 'nearest' });
    el.focus();
    el.select();
  }
}

function removeSet(index) {
  campiNome.delete(localSets.value[index]._uid);
  localSets.value.splice(index, 1);
  isDirty.value = true;
}

function puliti() {
  return localSets.value.map((s, i) => {
    const { _uid, _nuovo, ...resto } = s;
    return { ...resto, id: resto.id || idLibero(slug(resto.label), i) };
  });
}

async function save() {
  if (stylesStore.savingTypography) return; // lo annuncerà il salvataggio già in volo
  const inviato = JSON.stringify(puliti());
  const uidInviati = localSets.value.map((s) => s._uid);
  daSalvare = true;
  inVolo = true;
  // Lo store adotta i set solo se il server li scrive: un salvataggio fallito
  // non deve offrire alle tile set che sul sito non esistono.
  let esito;
  try {
    esito = await stylesStore.saveGlobalTypography(JSON.parse(inviato));
  } finally {
    inVolo = false;
  }
  if (esito.inCorso) return;
  if (!esito.ok) {
    // Resta «da salvare»: Salva attivo, id dei set nuovi ancora liberi. Se il
    // pannello è stato chiuso nel frattempo, la bozza l'ha lasciata la chiusura.
    toast.action(
      t('Tipografia globale non salvata') + (esito.motivo ? ' — ' + esito.motivo : ''),
      t('Riprova'), riprova, 10000, 'error'
    );
    return;
  }
  if (montato) {
    // Una bozza rimasta nello store è quella da cui il pannello è nato, e ciò che
    // è stato scritto la comprende. (Chiuso durante il volo, la bozza è quella
    // della chiusura: lo store la toglie solo se non ha modifiche successive.)
    stylesStore.globalTypographyDraft = null;
    nataDa = null;
  }
  // Modifiche fatte mentre il salvataggio era in volo: restano da salvare.
  localSets.value.forEach((s) => { if (uidInviati.includes(s._uid)) s._nuovo = false; });
  isDirty.value = JSON.stringify(puliti()) !== inviato;
  daSalvare = isDirty.value;
  toast.success(t('Tipografia globale salvata'));
}

// «Riprova» dal toast: col pannello aperto si risalva ciò che è a video; chiuso,
// la bozza rimasta nello store (se non c'è più, è già stata scritta).
let montato = true;
async function riprova() {
  if (montato) { save(); return; }
  const sets = stylesStore.globalTypographyDraft;
  if (!sets) return;
  const esito = await stylesStore.saveGlobalTypography(JSON.parse(JSON.stringify(sets)));
  if (esito.inCorso) return;
  if (esito.ok) {
    toast.success(t('Tipografia globale salvata'));
  } else {
    toast.action(
      t('Tipografia globale non salvata') + (esito.motivo ? ' — ' + esito.motivo : ''),
      t('Riprova'), riprova, 10000, 'error'
    );
  }
}

// Chiudere il pannello con lavoro mandato a salvare (fallito, in volo, o nato da
// una bozza) non lo butta: la bozza prende anche le modifiche fatte dopo il
// clic su Salva. Se il salvataggio in volo poi riesce, lo store la toglie solo
// se coincide con ciò che ha scritto.
onBeforeUnmount(() => {
  montato = false;
  if (isDirty.value && daSalvare) {
    stylesStore.globalTypographyDraft = puliti();
  }
});

// La bozza da cui il pannello è nato è stata scritta da un salvataggio partito
// prima (chiuso mentre era in volo, o «Riprova» del vecchio toast): se qui non
// è cambiato niente, a video ci sono i set salvati e non resta niente da salvare.
// Se invece è cambiato qualcosa resta da salvare, ma i set ormai sul sito
// tengono il loro id (rinominarli li staccherebbe dalle tile).
watch(() => stylesStore.globalTypographyDraft, (d) => {
  if (d || nataDa === null || inVolo) return;
  if (isDirty.value && JSON.stringify(puliti()) === nataDa) {
    localSets.value = normalizza(JSON.parse(JSON.stringify(stylesStore.globalTypography || [])));
    isDirty.value = false;
    daSalvare = false;
  } else {
    const salvati = new Set((stylesStore.globalTypography || []).map((s) => s && s.id));
    localSets.value.forEach((s) => { if (salvati.has(s.id)) s._nuovo = false; });
  }
  nataDa = null;
});

// Sync from store if it changes externally
watch(() => stylesStore.globalTypography, (newVal) => {
  if (!isDirty.value) {
    localSets.value = normalizza(JSON.parse(JSON.stringify(newVal || [])));
  }
}, { deep: true });
</script>

<style scoped>
.gtp-root {
  padding: 12px;
}

.gtp-header {
  margin-bottom: 12px;
}

.gtp-title {
  font-size: 13px;
  font-weight: 600;
  color: #1f2937;
  margin: 0 0 4px 0;
}

.gtp-desc {
  font-size: 11px;
  color: #6b7280;
  margin: 0;
  line-height: 1.4;
}

.gtp-list {
  display: flex;
  flex-direction: column;
  gap: 10px;
  margin-bottom: 12px;
}

.gtp-item {
  background: #f9fafb;
  border: 1px solid #e5e7eb;
  border-radius: 6px;
  padding: 10px;
}

.gtp-item-header {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 8px;
}

.gtp-fields {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.gtp-field {
  display: flex;
  align-items: center;
  gap: 8px;
}

.gtp-field-label {
  font-size: 10px;
  color: #6b7280;
  width: 90px;
  flex-shrink: 0;
}

.gtp-input {
  width: 100%;
  background: #fff;
  border: 1px solid #e5e7eb;
  border-radius: 4px;
  padding: 4px 6px;
  font-size: 11px;
  color: #1f2937;
  outline: none;
  font-family: inherit;
}

.gtp-input:focus {
  border-color: var(--olo-ui-accent, #e8622a);
}

.gtp-input--label {
  flex: 1;
  font-weight: 600;
  font-size: 12px;
}

.gtp-input--small {
  width: 60px;
  flex: none;
}

.gtp-vars {
  display: flex;
  flex-wrap: wrap;
  gap: 4px;
  margin-top: 6px;
}

.gtp-var {
  font-size: 9px;
  color: #6b7280;
  font-family: monospace;
  background: #f9fafb;
  padding: 1px 5px;
  border-radius: 3px;
}

.gtp-preview {
  margin-top: 6px;
  padding: 6px 8px;
  background: #f3f4f6;
  border-radius: 4px;
  color: #1f2937;
  font-size: 14px;
}

.gtp-remove {
  background: none;
  border: none;
  color: #6b7280;
  cursor: pointer;
  padding: 4px;
  border-radius: 4px;
  flex-shrink: 0;
  display: flex;
  align-items: center;
  justify-content: center;
}

.gtp-remove:hover {
  color: #ef4444;
  background: rgba(239, 68, 68, 0.1);
}

.gtp-add {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  width: 100%;
  padding: 6px 0;
  background: none;
  border: 1px dashed #d1d5db;
  border-radius: 6px;
  color: #6b7280;
  font-size: 11px;
  cursor: pointer;
  font-family: inherit;
  transition: border-color 0.15s, color 0.15s;
}

.gtp-add:hover {
  border-color: var(--olo-ui-accent, #e8622a);
  color: #1f2937;
}

.gtp-actions {
  margin-top: 12px;
  display: flex;
  justify-content: flex-end;
}

.gtp-save {
  background: var(--olo-ui-accent, #e8622a);
  color: #fff;
  border: none;
  border-radius: 6px;
  padding: 6px 16px;
  font-size: 11px;
  font-weight: 600;
  cursor: pointer;
  font-family: inherit;
  transition: opacity 0.15s;
}

.gtp-save:hover {
  opacity: 0.9;
}

.gtp-save:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}
</style>
