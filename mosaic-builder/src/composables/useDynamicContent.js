import { ref, readonly } from 'vue';

const oloData = window.oloData || {};

// Shared in-memory cache
let sourcesCache = null;
const sourcesLoading = ref(false);
const sourcesData = ref(null);

async function apiFetch(endpoint, options = {}) {
  const url = `${oloData.restUrl}/${endpoint}`;
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

export function useDynamicContent() {
  /**
   * Fetch available dynamic sources (cached).
   */
  async function fetchSources() {
    if (sourcesCache) {
      sourcesData.value = sourcesCache;
      return sourcesCache;
    }
    if (sourcesLoading.value) {
      // Wait for ongoing fetch
      return new Promise((resolve) => {
        const check = setInterval(() => {
          if (!sourcesLoading.value) {
            clearInterval(check);
            resolve(sourcesCache);
          }
        }, 100);
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
    return sourcesData.value[sourceKey]?.label || sourceKey;
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
      if (f) return `${sourceLabel} → ${f.label}`;
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
      return await apiFetch('dynamic-preview', {
        method: 'POST',
        body: JSON.stringify({ source, field, post_id: postId || 0 }),
      });
    } catch (err) {
      console.error('[OlobuilderBuilder] Preview failed:', err);
      return null;
    }
  }

  /**
   * Get available source keys (for binding, not post_types/taxonomies).
   */
  function getBindingSources() {
    if (!sourcesData.value) return [];
    const keys = ['current_post', 'site', 'custom_field', 'author'];
    if (sourcesData.value.acf?.available) keys.push('acf');
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
  };
}
