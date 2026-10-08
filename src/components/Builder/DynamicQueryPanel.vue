<template>
  <div class="dqp-wrap">
    <!-- Toggle -->
    <div class="dqp-toggle-row">
      <label class="dqp-toggle-label">
        <FieldToggle
          type="button"
          role="switch"
          :aria-checked="isEnabled ? 'true' : 'false'"
          :model-value="isEnabled"
          @update:model-value="toggleEnabled"
        />
        <span>{{ t('Sorgente dinamica') }}</span>
      </label>
    </div>

    <!-- Nella sandbox le fonti non arrivano: un avviso al posto delle opzioni vuote -->
    <DynamicUnavailableNotice v-if="avvisoAperto" @close="avvisoAperto = false" />

    <!-- Query config -->
    <div v-if="isEnabled && !nonDisponibili" class="dqp-config">
      <!-- Tipo di contenuto -->
      <div class="dqp-field">
        <label class="dqp-label">{{ t('Tipo di contenuto') }}</label>
        <FieldSelect ui="dropdown" :model-value="localQuery.post_type" :options="postTypes" @update:model-value="updateQuery('post_type', $event)" />
      </div>

      <!-- Posts per page -->
      <div class="dqp-field" style="display:flex;flex-direction:row;align-items:center;justify-content:space-between;gap:10px;">
        <label class="dqp-label" style="margin:0;">{{ t('Numero di elementi') }}</label>
        <NumberScrubber
          :modelValue="localQuery.posts_per_page"
          :min="1" :max="50" :step="1" :defaultValue="6"
          emitAs="number"
          :ariaLabel="t('Numero di elementi')"
          @update:modelValue="updateQuery('posts_per_page', $event || 6)"
        />
      </div>

      <!-- Order by -->
      <div class="dqp-row">
        <div class="dqp-field dqp-field--half">
          <label class="dqp-label">{{ t('Ordina per') }}</label>
          <FieldSelect ui="dropdown" :model-value="localQuery.orderby" :options="ORDERBY_OPTS" @update:model-value="updateQuery('orderby', $event)" />
        </div>
        <!-- In ordine casuale la direzione non conta: il controllo si nasconde -->
        <div v-if="localQuery.orderby !== 'rand'" class="dqp-field dqp-field--half">
          <label class="dqp-label">{{ t('Ordine') }}</label>
          <FieldSelect ui="dropdown" :model-value="localQuery.order" :options="orderOpts" @update:model-value="updateQuery('order', $event)" />
        </div>
      </div>

      <!-- Taxonomy filter -->
      <div class="dqp-field">
        <label class="dqp-label">{{ t('Filtra per tassonomia') }}</label>
        <FieldSelect ui="dropdown" :model-value="localQuery.taxonomy" :options="taxonomyOpts" @update:model-value="onTaxonomyChange($event)" />
      </div>

      <!-- Terms multi-select -->
      <div v-if="localQuery.taxonomy && selectedTaxTerms.length" class="dqp-field">
        <label class="dqp-label">{{ t('Termini') }}</label>
        <div class="dqp-terms">
          <label v-for="term in selectedTaxTerms" :key="term.value" class="dqp-term">
            <input
              type="checkbox"
              :checked="(localQuery.terms || []).includes(term.value)"
              @change="toggleTerm(term.value, $event.target.checked)"
            />
            <span>{{ term.label }}</span>
          </label>
        </div>
      </div>

      <!-- Primo risultato della query: dice subito se i filtri trovano qualcosa -->
      <div class="dqp-primo" aria-live="polite">
        <template v-if="primo.stato === 'carico'">{{ t('Carico…') }}</template>
        <template v-else-if="primo.stato === 'trovato'">{{ t('Primo risultato:') }} <strong>{{ primo.titolo || t('(senza titolo)') }}</strong></template>
        <template v-else-if="primo.stato === 'vuoto'">{{ t('Nessun contenuto con questi filtri') }}</template>
      </div>

      <!-- Field Mapping -->
      <div class="dqp-mapping">
        <label class="dqp-label dqp-label--section">{{ t('Mappatura campi') }}</label>
        <!-- Sorgente accesa senza campi collegati: il sito mostra ancora le voci scritte a mano -->
        <button v-if="puoCollegare" type="button" class="dqp-automappa" @click="collegaPerNome">{{ t('Collega i campi per nome') }}</button>
        <div v-for="field in itemFields" :key="field.key" class="dqp-map-row">
          <span class="dqp-map-key">{{ field.label }}</span>
          <FieldSelect
            ui="dropdown"
            size="compact"
            :model-value="localItemMap[field.key] || ''"
            :options="WP_FIELD_OPTS"
            @update:model-value="updateItemMap(field.key, $event)"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { t } from '@/i18n';
import { ref, computed, watch, onMounted, onBeforeUnmount } from 'vue';
import { useDynamicContent, dinamiciNonDisponibili } from '@/composables/useDynamicContent';
import DynamicUnavailableNotice from './DynamicUnavailableNotice.vue';
import FieldSelect from './fields/FieldSelect.vue';
import FieldToggle from './fields/FieldToggle.vue';
import NumberScrubber from './fields/NumberScrubber.vue';

const props = defineProps({
  query: { type: Object, default: () => ({}) },
  itemFields: { type: Array, default: () => [] },
  itemMap: { type: Object, default: () => ({}) },
});

const emit = defineEmits(['update:query', 'update:itemMap']);

const { fetchSources, sources, previewQuery } = useDynamicContent();

const defaultQuery = {
  enabled: false,
  post_type: 'post',
  posts_per_page: 6,
  orderby: 'date',
  order: 'DESC',
  taxonomy: '',
  terms: [],
};

const localQuery = ref({ ...defaultQuery, ...props.query });
const localItemMap = ref({ ...props.itemMap });

const isEnabled = computed(() => !!localQuery.value.enabled);
const nonDisponibili = dinamiciNonDisponibili();
// Acceso già nel modello (la sorgente si può spegnere, non configurare): l'avviso si vede subito
const avvisoAperto = ref(nonDisponibili && isEnabled.value);

const postTypes = computed(() => sources.value?.post_types || []);
const taxonomies = computed(() => sources.value?.taxonomies || []);

// Opzioni FieldSelect (label RAW: t() la applica FieldSelect internamente)
const taxonomyOpts = computed(() => [
  { value: '', label: 'Nessuna' },
  ...taxonomies.value.map(tax => ({ value: tax.value, label: tax.label })),
]);

const selectedTaxTerms = computed(() => {
  if (!localQuery.value.taxonomy) return [];
  const tax = taxonomies.value.find(t => t.value === localQuery.value.taxonomy);
  return tax?.terms || [];
});

const ORDERBY_OPTS = [
  { value: 'date', label: 'Data' },
  { value: 'title', label: 'Titolo' },
  { value: 'modified', label: 'Ultima modifica' },
  { value: 'rand', label: 'Casuale' },
  { value: 'menu_order', label: 'Ordine menu' },
];

// La direzione detta con le parole del criterio scelto. I valori salvati restano
// 'DESC' e 'ASC'.
const ORDER_LABELS = {
  date: ['Più recenti prima', 'Più vecchi prima'],
  modified: ['Modificati di recente prima', 'Modificati da più tempo prima'],
  title: ['Dalla Z alla A', 'Dalla A alla Z'],
  menu_order: ['Dal numero più alto', 'Dal numero più basso'],
};
const orderOpts = computed(() => {
  const [disc, asc] = ORDER_LABELS[localQuery.value.orderby] || ['Decrescente', 'Crescente'];
  return [{ value: 'DESC', label: disc }, { value: 'ASC', label: asc }];
});

const WP_FIELD_OPTS = [
  { value: '', label: '— Non mappato —' },
  { value: 'post_title', label: 'Titolo' },
  { value: 'post_excerpt', label: 'Estratto' },
  { value: 'post_content', label: 'Contenuto' },
  { value: 'featured_image', label: 'Immagine in evidenza' },
  { value: 'post_date', label: 'Data' },
  { value: 'author_name', label: 'Autore' },
  { value: 'permalink', label: 'Permalink' },
  { value: 'first_term', label: 'Primo termine tassonomia' },
  { value: 'post_year', label: 'Anno' },
  // 01, 02… nell'ordine dell'elenco: per i numeri delle liste e delle card
  { value: '_index', label: 'Numero progressivo' },
];

onMounted(() => {
  fetchSources();
});

watch(() => props.query, (val) => {
  localQuery.value = { ...defaultQuery, ...val };
}, { deep: true });

watch(() => props.itemMap, (val) => {
  localItemMap.value = { ...val };
}, { deep: true });

// Il campo di WordPress che una voce del ripetitore riceve, dedotto dal tipo e
// dal nome del campo: il link va al permalink, l'immagine all'immagine in
// evidenza, il testo lungo all'estratto, il titolo al titolo. Ogni campo di
// WordPress si usa una volta sola (la seconda immagine, quella al passaggio del
// mouse, resta da scegliere).
function campoPerNome(f) {
  const key = String(f.key || '').toLowerCase();
  if (f.type === 'link') return 'permalink';
  if (f.type === 'image') return 'featured_image';
  if (f.type === 'textarea' || f.type === 'editor' || /^(text|testo|description|descrizione|desc|excerpt|estratto|content|definition|body|quote|citazione)$/.test(key)) return 'post_excerpt';
  if (f.type !== 'text') return '';
  if (/^(title|titolo|heading|name|nome|term|author_name)$/.test(key)) return 'post_title';
  if (/^(number|numero|counter|step)$/.test(key)) return '_index';
  if (/^(year|anno)$/.test(key)) return 'post_year';
  if (/^(tag|category|categoria)$/.test(key)) return 'first_term';
  if (/^(date|data)$/.test(key)) return 'post_date';
  if (/^(author|autore)$/.test(key)) return 'author_name';
  return '';
}

function mappaPerNome() {
  const mappa = {};
  const usati = new Set();
  // Un logo riceve l'immagine in evidenza solo se la voce non ha altre immagini (Testimonianze: l'avatar)
  const ordinati = [...(props.itemFields || [])].sort((x, y) => (x.type === 'image' && /logo/i.test(x.key)) - (y.type === 'image' && /logo/i.test(y.key)));
  for (const f of ordinati) {
    const wp = campoPerNome(f);
    if (wp && !usati.has(wp)) {
      mappa[f.key] = wp;
      usati.add(wp);
    }
  }
  // Senza un campo «titolo» lo riceve il primo testo libero (Elenco, Elenco con icone, didascalia del
  // Carosello), anche se era andato all'estratto; mai un testo alternativo.
  if (!usati.has('post_title')) {
    const f = (props.itemFields || []).find((x) => x.type === 'text' && !/alt/i.test(x.key) && (!mappa[x.key] || mappa[x.key] === 'post_excerpt'));
    if (f) mappa[f.key] = 'post_title';
  }
  return mappa;
}

// Con la mappatura vuota il PHP non usa la query e il sito resta sulle voci
// scritte a mano, mentre qui le voci spariscono: accendendo la sorgente i
// campi si collegano per nome, se nessuno l'ha ancora fatto.
const mappaVuota = computed(() => Object.keys(localItemMap.value || {}).length === 0);
const puoCollegare = computed(() => mappaVuota.value && Object.keys(mappaPerNome()).length > 0);

function collegaPerNome() {
  const mappa = mappaPerNome();
  if (!Object.keys(mappa).length) return;
  localItemMap.value = mappa;
  emit('update:itemMap', { ...mappa });
}

function toggleEnabled() {
  if (nonDisponibili && !localQuery.value.enabled) { avvisoAperto.value = true; return; }
  localQuery.value.enabled = !localQuery.value.enabled;
  emitQuery();
  if (localQuery.value.enabled && mappaVuota.value) collegaPerNome();
}

// ── Primo risultato ──
// Si chiede al server il titolo del primo contenuto che la query trova, a
// ogni cambio dei filtri (con una piccola attesa, per non chiederlo a ogni
// passo dello scrubber). Vale l'ultima risposta.
const primo = ref({ stato: '', titolo: '' });
let attesaPrimo = null;
let richiestaPrimo = 0;
function aggiornaPrimo() {
  clearTimeout(attesaPrimo);
  if (!isEnabled.value || nonDisponibili) { primo.value = { stato: '', titolo: '' }; return; }
  primo.value = { stato: 'carico', titolo: '' };
  attesaPrimo = setTimeout(async () => {
    const mia = ++richiestaPrimo;
    const r = await previewQuery({ ...localQuery.value });
    if (mia !== richiestaPrimo) return;
    if (!r) primo.value = { stato: '', titolo: '' };
    else if (!r.post_id) primo.value = { stato: 'vuoto', titolo: '' };
    else primo.value = { stato: 'trovato', titolo: String(r.value ?? '') };
  }, 300);
}
watch(
  () => [isEnabled.value, localQuery.value.post_type, localQuery.value.orderby, localQuery.value.order, localQuery.value.taxonomy, (localQuery.value.terms || []).join(',')],
  aggiornaPrimo,
  { immediate: true },
);
onBeforeUnmount(() => clearTimeout(attesaPrimo));

function updateQuery(key, value) {
  localQuery.value[key] = value;
  emitQuery();
}

function onTaxonomyChange(value) {
  localQuery.value.taxonomy = value;
  localQuery.value.terms = [];
  emitQuery();
}

function toggleTerm(termId, checked) {
  const terms = [...(localQuery.value.terms || [])];
  if (checked) {
    terms.push(termId);
  } else {
    const idx = terms.indexOf(termId);
    if (idx !== -1) terms.splice(idx, 1);
  }
  localQuery.value.terms = terms;
  emitQuery();
}

function updateItemMap(key, value) {
  if (value) {
    localItemMap.value[key] = value;
  } else {
    delete localItemMap.value[key];
  }
  emit('update:itemMap', { ...localItemMap.value });
}

function emitQuery() {
  emit('update:query', { ...localQuery.value });
}
</script>

<style scoped>
/* Tema chiaro del chrome, come l'Inspector che lo contiene */
.dqp-wrap {
  --olo-ui-accent: #e8622a;
  margin-bottom: 8px;
  padding: 8px;
  background: #fff;
  border: 1px solid rgba(17, 24, 39, 0.12);
  border-radius: 6px;
}

.dqp-toggle-row {
  display: flex;
  align-items: center;
}

.dqp-toggle-label {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 12px;
  font-weight: 600;
  color: #1f2937;
  cursor: pointer;
}

.dqp-config {
  margin-top: 10px;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.dqp-field {
  display: flex;
  flex-direction: column;
  gap: 3px;
}

.dqp-field--half {
  flex: 1;
}

.dqp-row {
  display: flex;
  gap: 8px;
}

.dqp-label {
  font-size: 10px;
  font-weight: 600;
  color: #6b7280;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.dqp-label--section {
  font-size: 11px;
  color: #1f2937;
  margin-bottom: 4px;
}

.dqp-input {
  width: 100%;
  background: #fff;
  border: 1px solid #d1d5db;
  border-radius: 4px;
  padding: 5px 8px;
  font-size: 12px;
  color: #111827;
}

.dqp-input:focus {
  outline: none;
  border-color: var(--olo-ui-accent);
}

.dqp-terms {
  display: flex;
  flex-direction: column;
  gap: 4px;
  max-height: 120px;
  overflow-y: auto;
  padding: 4px;
  background: #f9fafb;
  border: 1px solid #e5e7eb;
  border-radius: 4px;
}

.dqp-term {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 11px;
  color: #374151;
  cursor: pointer;
}

.dqp-primo {
  font-size: 11px;
  color: #6b7280;
  min-height: 15px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.dqp-primo strong {
  color: #111827;
  font-weight: 600;
}

.dqp-automappa {
  align-self: flex-start;
  padding: 4px 8px;
  font-size: 11px;
  font-weight: 600;
  color: #1f2937;
  background: #fff;
  border: 1px solid #d1d5db;
  border-radius: 4px;
  cursor: pointer;
}

.dqp-automappa:hover {
  border-color: var(--olo-ui-accent);
}

.dqp-automappa:focus-visible {
  outline: 2px solid var(--olo-ui-accent);
  outline-offset: 2px;
}

.dqp-mapping {
  display: flex;
  flex-direction: column;
  gap: 6px;
  border-top: 1px solid #e5e7eb;
  padding-top: 8px;
}

.dqp-map-row {
  display: flex;
  align-items: center;
  gap: 8px;
}

.dqp-map-key {
  flex: 0 0 70px;
  font-size: 11px;
  color: #4b5563;
  text-overflow: ellipsis;
  overflow: hidden;
  white-space: nowrap;
}
</style>
