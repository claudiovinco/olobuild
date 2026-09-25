// usePageSeo — fetch/save per i meta SEO del post collegato al template corrente.
// Endpoint: /olobuild/v1/page-seo/{postId} (vedi class-rest-api.php::page_seo_*).
//
// Pattern: pull al mount (quando il post_id è noto), debounced push su ogni cambio.
// Stato locale reattivo, NON nel Pinia store (i meta vivono come post_meta WP,
// non come state globale del builder).

import { ref, watch, computed } from 'vue';
import { t } from '@/i18n';

const DEFAULT_DATA = {
  title: '',
  description: '',
  focus_keyword: '',
  canonical: '',
  noindex: false,
  nofollow: false,
  og_title: '',
  og_description: '',
  og_image: '',
  tw_title: '',
  tw_description: '',
  schema_type: '',
  extra_jsonld: '',
  faq: [],
};

export function usePageSeo(postIdRef) {
  const data = ref({ ...DEFAULT_DATA });
  const defaults = ref({ site_name: '', post_title: '', post_url: '', site_host: '' });
  const loading = ref(false);
  const saving = ref(false);
  // Vero per qualche secondo dopo un salvataggio andato a buon fine PER INTERO: la SEO
  // si salva da sola, e senza un «Salvato» non si sapeva se la modifica fosse arrivata.
  // Un 200 con `errors` (page_seo_save scarta il JSON-LD non valido e lascia il valore
  // di prima, e le voci FAQ senza domanda o risposta) non è «Salvato»: i campi scartati
  // stanno in validationErrors.
  const saved = ref(false);
  const lastError = ref(null);
  // Da dove viene lastError: 'load' (GET iniziale) o 'save' (POST). Un caricamento
  // fallito non è un salvataggio fallito, e la testata lo dice in modo diverso.
  const errorKind = ref(null);
  const validationErrors = ref({});
  let savedTimer = null;

  const oloData = window.oloData || {};
  const restRoot = oloData.restUrl || '/wp-json/';
  const nonce = oloData.nonce || '';

  let saveTimer = null;
  let suppressWatch = false;

  function url(id) {
    // oloData.restUrl include già "olobuild/v1" (es. https://site/wp-json/olobuild/v1).
    // Quindi qui appendiamo solo il segmento finale, niente doppio prefisso.
    return restRoot.replace(/\/$/, '') + '/page-seo/' + Number(id);
  }

  async function pull() {
    const id = unwrap(postIdRef);
    if (!id) {
      data.value = { ...DEFAULT_DATA };
      return;
    }
    loading.value = true;
    lastError.value = null;
    errorKind.value = null;
    try {
      const res = await fetch(url(id), {
        credentials: 'same-origin',
        headers: { 'X-WP-Nonce': nonce },
      });
      if (!res.ok) throw new Error('HTTP ' + res.status);
      const j = await res.json();
      suppressWatch = true;
      data.value = mergeWithDefaults(j);
      defaults.value = j._defaults || defaults.value;
      // Allow new state to settle before re-enabling auto-save.
      setTimeout(() => { suppressWatch = false; }, 50);
    } catch (e) {
      lastError.value = t('Caricamento non riuscito') + ': ' + String(e && e.message || e);
      errorKind.value = 'load';
    } finally {
      loading.value = false;
    }
  }

  function mergeWithDefaults(srv) {
    const out = { ...DEFAULT_DATA };
    Object.keys(DEFAULT_DATA).forEach(k => {
      if (srv && Object.prototype.hasOwnProperty.call(srv, k)) out[k] = srv[k];
    });
    if (!Array.isArray(out.faq)) out.faq = [];
    return out;
  }

  async function push() {
    const id = unwrap(postIdRef);
    if (!id) return;
    saving.value = true;
    saved.value = false;
    clearTimeout(savedTimer);
    validationErrors.value = {};
    try {
      const res = await fetch(url(id), {
        method: 'POST',
        credentials: 'same-origin',
        headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': nonce },
        body: JSON.stringify(data.value),
      });
      const j = await res.json().catch(() => ({}));
      if (!res.ok) {
        lastError.value = t('Salvataggio fallito') + ': ' + ((j && j.message) || 'HTTP ' + res.status);
        errorKind.value = 'save';
        return;
      }
      // Arrivato: un errore di prima non resta a schermo come se valesse ancora.
      lastError.value = null;
      errorKind.value = null;
      const scartati = (j && j.errors && typeof j.errors === 'object') ? j.errors : {};
      validationErrors.value = scartati;
      // Salvato in parte: niente «Salvato», la testata dice cosa è stato scartato.
      if (Object.keys(scartati).length) return;
      saved.value = true;
      savedTimer = setTimeout(() => { saved.value = false; }, 2500);
    } catch (e) {
      lastError.value = t('Salvataggio fallito') + ': ' + String(e && e.message || e);
      errorKind.value = 'save';
    } finally {
      saving.value = false;
    }
  }

  function schedulePush() {
    // Una modifica non ancora inviata non è «Salvato»: senza questo il «Salvato» del
    // push precedente restava a schermo per tutto il debounce, sopra un testo che il
    // server non aveva ancora ricevuto.
    saved.value = false;
    clearTimeout(savedTimer);
    clearTimeout(saveTimer);
    saveTimer = setTimeout(push, 600);
  }

  // Auto-pull quando il post_id diventa noto / cambia.
  watch(() => unwrap(postIdRef), (id) => { if (id) pull(); }, { immediate: true });

  // Auto-push debounced su ogni cambio dati (skip durante il pull iniziale).
  watch(data, () => { if (!suppressWatch) schedulePush(); }, { deep: true });

  function update(key, value) {
    data.value = { ...data.value, [key]: value };
  }

  const isReady = computed(() => !!unwrap(postIdRef));

  return {
    data,
    defaults,
    loading,
    saving,
    saved,
    lastError,
    errorKind,
    validationErrors,
    isReady,
    update,
    pull,
    push,
  };
}

function unwrap(r) {
  if (!r) return null;
  return ('value' in r) ? r.value : r;
}
