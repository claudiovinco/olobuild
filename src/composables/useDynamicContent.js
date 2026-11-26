import { ref, readonly, watch } from 'vue';
import { t } from '@/i18n';
import { useBuilderStore } from '@/stores/builder';

const oloData = window.oloData || {};

// Shared in-memory cache
let sourcesCache = null;
const sourcesLoading = ref(false);
const sourcesData = ref(null);

async function apiFetch(endpoint, options = {}) {
  const url = `${oloData.restUrl}${endpoint}`;
  const res = await fetch(url, {
    headers: {
      'X-WP-Nonce': oloData.nonce,
      'Content-Type': 'application/json',
    },
    ...options,
  });
  if (!res.ok) throw new Error(`API error: ${res.status}`);
  return res.json();
}

/**
 * Il contenuto di cui l'anteprima del collegamento mostra il valore: quello che il canvas sta mostrando (17 nov 2026).
 * In un template singolo il PHP sceglie lo stesso contenuto del canvas (post_anteprima_single); in una pagina, la
 * pagina stessa. Senza, il pannello mostrava l'ultimo articolo, e i campi di un Tipo di contenuto restavano vuoti.
 */
function contestoAnteprima() {
  try {
    const tpl = useBuilderStore().currentTemplate;
    if (!tpl) return {};
    if (tpl.type === 'single') {
      return {
        template_type: 'single',
        post_type: tpl.settings?.single_post_type || tpl.settings?.post_type || 'post',
        preview_post_id: Number(tpl.settings?.preview_post_id) || 0,
      };
    }
    const pagina = Number(tpl.linked_post_id || tpl.settings?.post_id) || 0;
    return pagina ? { post_id: pagina } : {};
  } catch (e) {
    return {};
  }
}

/**
 * I contenuti dinamici si possono usare qui? Nella sandbox di try (oloExternalData.sandbox,
 * dal plugin olo-sandbox) le API delle fonti sono chiuse: il fulmine e la «Sorgente
 * dinamica» aprono un avviso (DynamicUnavailableNotice) e le fonti non si chiedono.
 */
export function dinamiciNonDisponibili() {
  const ext = window.oloExternalData;
  return !!(ext && ext.sandbox && ext.sandbox.enabled);
}

export function useDynamicContent() {
  /**
   * Fetch available dynamic sources (cached).
   */
  async function fetchSources() {
    if (dinamiciNonDisponibili()) return null;
    if (sourcesCache) {
      sourcesData.value = sourcesCache;
      return sourcesCache;
    }
    if (sourcesLoading.value) {
      // Wait for ongoing fetch
      return new Promise((resolve) => {
        const unwatch = watch(sourcesLoading, (val) => {
          if (!val) {
            unwatch();
            resolve(sourcesCache);
          }
        }, { immediate: true });
      });
    }

    sourcesLoading.value = true;
    try {
      const data = await apiFetch('dynamic-sources');
      sourcesCache = data;
      sourcesData.value = data;
      return data;
    } catch (err) {
      console.error('[OlobuilderBuilder] Failed to fetch dynamic sources:', err);
      return null;
    } finally {
      sourcesLoading.value = false;
    }
  }

  /**
   * Get fields for a given source key.
   */
  function getFieldsForSource(sourceKey) {
    if (!sourcesData.value) return [];
    const source = sourcesData.value[sourceKey];
    if (!source) return [];
    if (source.fields === 'manual') return 'manual';
    if (Array.isArray(source.fields)) return source.fields;
    return [];
  }

  /**
   * Get label for a source key.
   */
  function getSourceLabel(sourceKey) {
    if (!sourcesData.value) return sourceKey;
    const label = sourcesData.value[sourceKey]?.label;
    return label ? t(label) : sourceKey;
  }

  /**
   * Get label for a binding.
   */
  function getBindingLabel(binding) {
    if (!binding || !binding.source || !binding.field) return '';
    const sourceLabel = getSourceLabel(binding.source);
    const fields = getFieldsForSource(binding.source);
    if (fields === 'manual') return `${sourceLabel}: ${binding.field}`;
    if (Array.isArray(fields)) {
      // Check flat fields
      const f = fields.find(f => f.key === binding.field);
      if (f) return `${sourceLabel} → ${t(f.label)}`;
      // Check ACF grouped fields
      for (const group of fields) {
        if (group.fields) {
          const gf = group.fields.find(f => f.key === binding.field);
          if (gf) return `${sourceLabel} → ${gf.label}`;
        }
      }
    }
    return `${sourceLabel} → ${binding.field}`;
  }

  /**
   * Preview a dynamic binding value.
   */
  async function previewBinding(source, field, postId) {
    try {
      const ctx = contestoAnteprima();
      return await apiFetch('dynamic-preview', {
        method: 'POST',
        body: JSON.stringify({ ...ctx, source, field, post_id: postId || ctx.post_id || 0 }),
      });
    } catch (err) {
      console.error('[OlobuilderBuilder] Preview failed:', err);
      return null;
    }
  }

  /**
   * Il titolo del primo risultato di una query di ripetitore.
   * { value: titolo, post_id } oppure { value: null, post_id: 0 } se non trova niente;
   * null se la richiesta fallisce.
   */
  async function previewQuery(query) {
    try {
      return await apiFetch('dynamic-preview', {
        method: 'POST',
        body: JSON.stringify({ source: 'current_post', field: 'post_title', query }),
      });
    } catch (err) {
      console.error('[OlobuilderBuilder] Query preview failed:', err);
      return null;
    }
  }

  /**
   * Slide dagli articoli dello Slider Pro (1.4.1457): i valori dei segnaposto del primo contenuto della ricerca
   * ({ post_title, featured_image, … }), o null se la ricerca non trova niente.
   */
  async function segnapostoEsempio(query) {
    try {
      const r = await apiFetch('dynamic-preview', {
        method: 'POST',
        body: JSON.stringify({ source: 'current_post', field: 'post_title', query, segnaposto: true }),
      });
      return r && r.segnaposto ? r.segnaposto : null;
    } catch (err) {
      return null;
    }
  }

  /**
   * Get available source keys (for binding, not post_types/taxonomies).
   */
  function getBindingSources(fieldType = '') {
    if (!sourcesData.value) return [];
    const keys = ['current_post', 'site', 'custom_field', 'author'];
    if (sourcesData.value.acf?.available) keys.push('acf');
    // I campi dei tipi di contenuto delle Impostazioni (Notizie › Sottotitolo…), quando ce n'è almeno uno.
    if (sourcesData.value.campi?.fields?.length) keys.push('campi');
    // Per un link anche le sorgenti che danno indirizzi (17 nov 2026): la pagina dei biglietti di uno spettacolo,
    // il contenuto precedente e successivo, la pagina aperta, il file del media, il prodotto. Il pannello poi
    // mostra solo i loro campi indirizzo.
    if (fieldType === 'link') {
      for (const k of ['spettacolo', 'post_navigation', 'request', 'media', 'woocommerce']) {
        const s = sourcesData.value[k];
        if (s && s.available !== false && Array.isArray(s.fields) && s.fields.length) keys.push(k);
      }
    }
    return keys.map(key => ({
      value: key,
      label: sourcesData.value[key]?.label || key,
    }));
  }

  return {
    sources: readonly(sourcesData),
    loading: readonly(sourcesLoading),
    fetchSources,
    getFieldsForSource,
    getSourceLabel,
    getBindingLabel,
    getBindingSources,
    previewBinding,
    previewQuery,
    segnapostoEsempio,
  };
}
