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
      <!-- Da dove vengono le voci: gli articoli (una query) o le foto salvate in un campo del contenuto
           che la pagina mostra (la galleria, le foto 360°…). Le foto solo se la voce ha un'immagine. -->
      <div v-if="originOpts.length > 1" class="dqp-field">
        <label class="dqp-label">{{ t('Elenco di') }}</label>
        <FieldSelect ui="dropdown" :model-value="origin" :options="originOpts" @update:model-value="setOrigin($event)" />
      </div>

      <template v-if="origin === 'images'">
        <div class="dqp-field">
          <label class="dqp-label">{{ t('Campo con le foto') }}</label>
          <FieldSelect ui="dropdown" :model-value="imagesSelect" :options="imageListOpts" @update:model-value="onImagesSelect($event)" />
        </div>
        <div v-if="imagesSelect === CAMPO_LIBERO" class="dqp-field">
          <label class="dqp-label">{{ t('Chiave del campo personalizzato') }}</label>
          <input
            type="text"
            class="dqp-input"
            :value="localQuery.images_key || ''"
            placeholder="_mia_galleria"
            spellcheck="false"
            @change="updateQuery('images_key', $event.target.value.trim())"
          />
        </div>
        <div class="dqp-field" style="display:flex;flex-direction:row;align-items:center;justify-content:space-between;gap:10px;">
          <label class="dqp-label" style="margin:0;">{{ t('Foto al massimo') }}</label>
          <NumberScrubber
            :modelValue="localQuery.images_max || 0"
            :min="0" :max="60" :step="1" :defaultValue="0"
            emitAs="number"
            :ariaLabel="t('Foto al massimo')"
            @update:modelValue="updateQuery('images_max', $event || 0)"
          />
        </div>
        <p class="dqp-hint">{{ t('0 = tutte. Sono le foto del contenuto che la pagina mostra (nel builder, quello dell\'anteprima): senza foto l\'elemento non compare sul sito.') }}</p>
      </template>

      <!-- Righe di un campo: le voci scritte nel contenuto che la pagina mostra, una per riga, parti divise da «|»
           (le mostre di un'opera, le misure di un prodotto). -->
      <template v-else-if="origin === 'righe'">
        <div class="dqp-field">
          <label class="dqp-label" style="display:flex;align-items:center;gap:4px;">{{ t('Chiave del campo con le righe') }}<InfoTip :titolo="t('Righe di un campo')" :testo="TESTO_RIGHE" /></label>
          <input
            type="text"
            class="dqp-input"
            :value="localQuery.righe_key || ''"
            placeholder="pedigree"
            spellcheck="false"
            @change="updateQuery('righe_key', $event.target.value.trim())"
          />
        </div>
        <div class="dqp-field" style="display:flex;flex-direction:row;align-items:center;justify-content:space-between;gap:10px;">
          <label class="dqp-label" style="margin:0;">{{ t('Righe al massimo') }}</label>
          <NumberScrubber
            :modelValue="localQuery.righe_max || 0"
            :min="0" :max="60" :step="1" :defaultValue="0"
            emitAs="number"
            :ariaLabel="t('Righe al massimo')"
            @update:modelValue="updateQuery('righe_max', $event || 0)"
          />
        </div>
      </template>

      <template v-else>
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
      </template>

      <!-- Primo risultato della query (o le foto di un esempio): dice subito se la sorgente trova qualcosa -->
      <div class="dqp-primo" aria-live="polite">
        <template v-if="primo.stato === 'carico'">{{ t('Carico…') }}</template>
        <template v-else-if="origin === 'images' && primo.stato === 'trovato'">{{ t('Esempio:') }} <strong>{{ fotoIn(primo) }}</strong></template>
        <template v-else-if="origin === 'images' && primo.stato === 'vuoto'">{{ t('Nessun contenuto ha foto in questo campo') }}</template>
        <template v-else-if="origin === 'righe' && primo.stato === 'trovato'">{{ t('Esempio:') }} <strong>{{ righeIn(primo) }}</strong></template>
        <template v-else-if="origin === 'righe' && primo.stato === 'vuoto'">{{ t('Nessun contenuto ha righe in questo campo') }}</template>
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
            :options="origin === 'images' ? IMAGE_PART_OPTS : (origin === 'righe' ? RIGA_PART_OPTS : WP_FIELD_OPTS)"
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
import InfoTip from './InfoTip.vue';

const props = defineProps({
  query: { type: Object, default: () => ({}) },
  itemFields: { type: Array, default: () => [] },
  itemMap: { type: Object, default: () => ({}) },
  // Le origini che il ripetitore accetta (dynamicOrigins del campo): ['images'] per le scene del
  // Tour 360, che con le immagini in evidenza degli articoli non avrebbero senso. null = tutte.
  origins: { type: Array, default: null },
});

const emit = defineEmits(['update:query', 'update:itemMap']);

const { fetchSources, sources, previewQuery } = useDynamicContent();

const defaultQuery = {
  enabled: false,
  // 'posts' = una query di articoli; 'images' = le foto di un campo del contenuto (images_key);
  // 'righe' = le righe di un campo del contenuto (righe_key), una voce per riga
  source: 'posts',
  images_key: '',
  images_max: 0,
  righe_key: '',
  righe_max: 0,
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

// ── Foto di un campo ──
// Le parti di una foto che una voce può ricevere (le chiavi le legge
// Olobuild_Dynamic_Content::image_part). L'originale serve alle foto 360°.
const IMAGE_PART_OPTS = [
  { value: '', label: '— Non mappato —' },
  { value: 'url', label: 'Foto' },
  { value: 'url_original', label: 'Foto originale (fino a 8192 px, per le foto 360°)' },
  { value: 'url_2048', label: 'Foto leggera (2048 px)' },
  { value: 'url_large', label: 'Foto grande' },
  { value: 'url_medium', label: 'Foto media' },
  { value: 'url_thumb', label: 'Miniatura' },
  { value: 'title', label: 'Titolo della foto' },
  { value: 'caption', label: 'Didascalia' },
  { value: 'alt', label: 'Testo alternativo' },
  { value: 'description', label: 'Descrizione' },
  { value: '_index', label: 'Numero progressivo' },
];
const CAMPO_LIBERO = '__campo';

// ── Righe di un campo ──
// Le parti di una riga (Olobuild_Dynamic_Content::build_items_from_righe): divise da «|», al massimo sei.
const RIGA_PART_OPTS = [
  { value: '', label: '— Non mappato —' },
  { value: 'parte_1', label: 'Parte 1' },
  { value: 'parte_2', label: 'Parte 2' },
  { value: 'parte_3', label: 'Parte 3' },
  { value: 'parte_4', label: 'Parte 4' },
  { value: 'parte_5', label: 'Parte 5' },
  { value: 'parte_6', label: 'Parte 6' },
  { value: 'riga', label: 'Riga intera' },
  { value: '_index', label: 'Numero progressivo' },
];
const TESTO_RIGHE = [
  t('Le voci vengono da un campo personalizzato del contenuto che la pagina mostra (un prodotto, un articolo): una voce per riga, con le parti separate da «|». Per esempio «2019 | Pietrasanta | Bianco su bianco».'),
  t('Nella mappatura ogni campo della voce riceve una parte (Parte 1 è il testo prima del primo «|»). Le righe vuote e quelle che iniziano con # non contano.'),
  t("Senza righe l'elemento non compare sul sito; nel builder resta il contenuto di esempio."),
];

// Le foto si propongono solo se la voce ha un'immagine; il campo può limitare le origini.
const hasImageField = computed(() => (props.itemFields || []).some((f) => f && f.type === 'image'));
const originOpts = computed(() => {
  const tutte = [
    { value: 'posts', label: 'Articoli' },
    { value: 'images', label: 'Foto di un campo' },
    { value: 'righe', label: 'Righe di un campo' },
  ].filter((o) => o.value !== 'images' || hasImageField.value);
  const ammesse = Array.isArray(props.origins) && props.origins.length ? props.origins : null;
  const scelte = ammesse ? tutte.filter((o) => ammesse.includes(o.value)) : tutte;
  return scelte.length ? scelte : tutte;
});
const origin = computed(() => {
  const s = ['images', 'righe'].includes(localQuery.value.source) ? localQuery.value.source : 'posts';
  return originOpts.value.some((o) => o.value === s) ? s : originOpts.value[0].value;
});

const imageLists = computed(() => sources.value?.image_lists || []);
const campoLibero = ref(false);
const imagesSelect = computed(() => {
  const k = localQuery.value.images_key || '';
  if (campoLibero.value) return CAMPO_LIBERO;
  if (!k) return '';
  return imageLists.value.some((l) => l.value === k) ? k : CAMPO_LIBERO;
});
const imageListOpts = computed(() => [
  { value: '', label: '— Scegli —' },
  ...imageLists.value.map((l) => ({ value: l.value, label: l.label })),
  { value: CAMPO_LIBERO, label: 'Altro campo personalizzato…' },
]);

function onImagesSelect(v) {
  if (v === CAMPO_LIBERO) {
    campoLibero.value = true;
    // Una chiave degli elenchi registrati non resta: si scrive quella del campo libero.
    if (imageLists.value.some((l) => l.value === localQuery.value.images_key)) updateQuery('images_key', '');
    return;
  }
  campoLibero.value = false;
  updateQuery('images_key', v || '');
}

function righeIn(p) {
  return `${p.n} ${t('righe in')} «${p.titolo || t('(senza titolo)')}»` + (p.primaRiga ? ` — ${p.primaRiga}` : '');
}

function fotoIn(p) {
  return `${p.n} ${t('foto in')} «${p.titolo || t('(senza titolo)')}»`;
}

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

// La parte della foto che un campo della voce riceve, dal tipo e dal nome. Un campo può
// dichiararla (dynamicImage: le scene del Tour 360 chiedono l'originale per la foto sferica).
function partePerNome(f) {
  const key = String(f.key || '').toLowerCase();
  if (f.type === 'image') {
    if (f.dynamicImage) return f.dynamicImage;
    if (/legger|light|small|mobile/.test(key)) return 'url_2048';
    if (/anteprima|thumb|miniatur|preview|poster/.test(key)) return 'url_medium';
    return 'url';
  }
  if (f.type === 'number' || f.type === 'range') return /^(number|numero|counter|step)$/.test(key) ? '_index' : '';
  if (/^alt/.test(key)) return 'alt';
  if (/^(title|titolo|heading|name|nome|label|etichetta)$/.test(key)) return 'title';
  if (/^(caption|didascalia|desc|descr|description|descrizione|text|testo|subtitle|sottotitolo)$/.test(key)) return 'caption';
  if (/^(number|numero|counter|step)$/.test(key)) return '_index';
  return '';
}

function mappaFotoPerNome() {
  const mappa = {};
  const usati = new Set();
  for (const f of props.itemFields || []) {
    if (!f || !f.key || f.type === 'separator') continue;
    const p = partePerNome(f);
    if (p && !usati.has(p)) {
      mappa[f.key] = p;
      usati.add(p);
    }
  }
  return mappa;
}

// Righe: le parti nell'ordine dei campi di testo della voce (il primo riceve la Parte 1…).
function mappaRighePerNome() {
  const mappa = {};
  let n = 0;
  for (const f of props.itemFields || []) {
    if (!f || !f.key || !['text', 'textarea'].includes(f.type) || n >= 6) continue;
    mappa[f.key] = 'parte_' + (++n);
  }
  return mappa;
}

function mappaPerNome() {
  if (origin.value === 'images') return mappaFotoPerNome();
  if (origin.value === 'righe') return mappaRighePerNome();
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
  // Carosello), anche se era andato all'estratto. Il testo alternativo solo se è l'unico testo della voce
  // (Marquee di immagini): lì il titolo dell'articolo descrive la foto.
  if (!usati.has('post_title')) {
    const testi = (props.itemFields || []).filter((x) => x.type === 'text' && (!mappa[x.key] || mappa[x.key] === 'post_excerpt'));
    const f = testi.find((x) => !/alt/i.test(x.key)) || (testi.length === 1 ? testi[0] : null);
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
  // L'origine salvata è sempre una di quelle ammesse dal campo (le scene del Tour 360: le foto).
  localQuery.value.source = origin.value;
  emitQuery();
  if (localQuery.value.enabled && mappaVuota.value) collegaPerNome();
}

// Cambiando origine la mappatura di prima non vale più (campi di articolo ≠ parti di una foto):
// si ricollega per nome.
function setOrigin(v) {
  if (v === origin.value) return;
  localQuery.value.source = v;
  emitQuery();
  const mappa = mappaPerNome();
  localItemMap.value = mappa;
  emit('update:itemMap', { ...mappa });
}

// ── Primo risultato ──
// Si chiede al server il titolo del primo contenuto che la query trova, a
// ogni cambio dei filtri (con una piccola attesa, per non chiederlo a ogni
// passo dello scrubber). Vale l'ultima risposta.
const primo = ref({ stato: '', titolo: '', n: 0 });
let attesaPrimo = null;
let richiestaPrimo = 0;
function aggiornaPrimo() {
  clearTimeout(attesaPrimo);
  if (!isEnabled.value || nonDisponibili) { primo.value = { stato: '', titolo: '', n: 0 }; return; }
  // Foto di un campo senza campo scelto: niente da chiedere.
  if (origin.value === 'images' && !localQuery.value.images_key) { primo.value = { stato: '', titolo: '', n: 0 }; return; }
  if (origin.value === 'righe' && !localQuery.value.righe_key) { primo.value = { stato: '', titolo: '', n: 0 }; return; }
  primo.value = { stato: 'carico', titolo: '', n: 0 };
  attesaPrimo = setTimeout(async () => {
    const mia = ++richiestaPrimo;
    const r = await previewQuery({ ...localQuery.value, source: origin.value });
    if (mia !== richiestaPrimo) return;
    if (!r) primo.value = { stato: '', titolo: '', n: 0 };
    else if (!r.post_id) primo.value = { stato: 'vuoto', titolo: '', n: 0 };
    // Articoli: value = il titolo del primo; foto: value = quante, title = il contenuto che le ha.
    else if (origin.value === 'images') primo.value = { stato: 'trovato', titolo: String(r.title ?? ''), n: Number(r.value) || 0 };
    else if (origin.value === 'righe') primo.value = { stato: 'trovato', titolo: String(r.title ?? ''), n: Number(r.value) || 0, primaRiga: String(r.prima ?? '') };
    else primo.value = { stato: 'trovato', titolo: String(r.value ?? ''), n: 0 };
  }, 300);
}
watch(
  () => [isEnabled.value, origin.value, localQuery.value.images_key, localQuery.value.righe_key, localQuery.value.post_type, localQuery.value.orderby, localQuery.value.order, localQuery.value.taxonomy, (localQuery.value.terms || []).join(',')],
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

.dqp-hint {
  margin: -2px 0 0;
  font-size: 11px;
  line-height: 1.45;
  color: #6b7280;
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
