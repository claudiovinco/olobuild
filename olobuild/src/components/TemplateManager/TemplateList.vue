<template>
  <div class="tpl-page">
    <div class="tpl-container">
      <!-- Actions bar -->
      <div class="tpl-header-actions">
        <button @click="triggerImport" class="tpl-btn tpl-btn-outline">&#8593; Importa</button>
        <input
          ref="importFileRef"
          type="file"
          accept=".json"
          style="display:none"
          @change="handleImportFile"
        />
        <!-- New template dropdown -->
        <div class="tpl-dropdown" ref="dropdownRef">
          <button @click="showNewMenu = !showNewMenu" class="tpl-btn tpl-btn-primary">
            + Nuovo Template
          </button>
          <div v-if="showNewMenu" class="tpl-dropdown-menu">
            <button @click="createNew('page')" class="tpl-dropdown-item">Nuova Pagina</button>
            <button @click="createNew('header')" class="tpl-dropdown-item">Nuovo Header</button>
            <button @click="createNew('footer')" class="tpl-dropdown-item">Nuovo Footer</button>
            <button @click="createNew('megapanel')" class="tpl-dropdown-item">Nuovo Mega Panel</button>
            <button @click="createNew('404')" class="tpl-dropdown-item">Nuova 404</button>
            <div class="tpl-dropdown-sep"></div>
            <div class="tpl-dropdown-label">Template Single</div>
            <button
              v-for="pt in postTypes"
              :key="pt.value"
              @click="createNewSingle(pt.value)"
              class="tpl-dropdown-item"
            >
              Single: {{ pt.label }}
            </button>
          </div>
        </div>
      </div>

      <!-- Tab filter -->
      <div class="tpl-tabs">
        <button
          v-for="tab in tabs"
          :key="tab.value"
          @click="activeTab = tab.value"
          :class="['tpl-tab', { active: activeTab === tab.value }]"
        >
          {{ tab.label }}
        </button>
      </div>

      <!-- Loading state -->
      <div v-if="loading" class="tpl-loading">
        <p>Caricamento template...</p>
      </div>

      <!-- Empty state -->
      <div v-else-if="filteredTemplates.length === 0" class="tpl-empty">
        <div class="tpl-empty-icon">
          <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="9" y1="3" x2="9" y2="21"/></svg>
        </div>
        <h2>
          {{ activeTab === 'header' ? 'Nessun header' : activeTab === 'footer' ? 'Nessun footer' : activeTab === 'single' ? 'Nessun template single' : activeTab === '404' ? 'Nessuna pagina 404' : 'Nessun template' }}
        </h2>
        <p>
          {{ activeTab === 'header' ? 'Crea il tuo primo template header.' : activeTab === 'footer' ? 'Crea il tuo primo template footer.' : activeTab === 'single' ? 'Crea un template single per un custom post type.' : activeTab === '404' ? 'Crea la tua pagina 404 personalizzata.' : 'Crea il tuo primo template per iniziare.' }}
        </p>
        <button
          v-if="activeTab !== 'single'"
          @click="createNew(activeTab === 'all' ? 'page' : activeTab)"
          class="tpl-btn tpl-btn-primary"
        >
          {{ activeTab === 'header' ? 'Crea Header' : activeTab === 'footer' ? 'Crea Footer' : activeTab === '404' ? 'Crea 404' : 'Crea Template' }}
        </button>
      </div>

      <!-- Template grid -->
      <div v-else class="tpl-grid">
        <div
          v-for="tpl in filteredTemplates"
          :key="tpl.id"
          class="tpl-card"
        >
          <!-- Preview area -->
          <div class="tpl-card-preview">
            <div class="tpl-card-count">{{ countElements(tpl.content) }} elementi</div>
            <!-- Type badge -->
            <span v-if="tpl.type === 'header'" class="tpl-type-badge purple">Header</span>
            <span v-if="tpl.type === 'footer'" class="tpl-type-badge teal">Footer</span>
            <span v-if="tpl.type === 'single'" class="tpl-type-badge amber">Single: {{ getSinglePostType(tpl) }}</span>
            <span v-if="tpl.type === 'megapanel'" class="tpl-type-badge indigo">Mega Panel</span>
            <span v-if="tpl.type === '404'" class="tpl-type-badge red">404</span>
            <!-- Active indicators -->
            <span v-if="tpl.type === 'header' && tpl.id === activeHeaderId" class="tpl-active-badge">Attivo</span>
            <span v-if="tpl.type === 'footer' && tpl.id === activeFooterId" class="tpl-active-badge">Attivo</span>
            <span v-if="tpl.type === 'single' && isActiveSingle(tpl)" class="tpl-active-badge">Attivo</span>
            <span v-if="tpl.type === '404' && tpl.id === active404Id" class="tpl-active-badge">Attivo</span>
            <!-- Hover overlay -->
            <div class="tpl-card-overlay">
              <button @click="$emit('edit', tpl.id)" class="tpl-btn tpl-btn-primary tpl-btn-sm">Modifica</button>
              <button @click="duplicateTemplate(tpl.id)" class="tpl-btn tpl-btn-outline-light tpl-btn-sm">Duplica</button>
              <button @click="exportTemplate(tpl.id)" class="tpl-btn tpl-btn-outline-light tpl-btn-sm">Esporta</button>
            </div>
          </div>
          <!-- Info -->
          <div class="tpl-card-info">
            <div class="tpl-card-info-top">
              <div class="tpl-card-info-left">
                <input
                  v-if="renamingId === tpl.id"
                  :ref="el => { if (el) renameInputRef = el }"
                  v-model="renameDraft"
                  @blur="confirmRename(tpl)"
                  @keydown.enter="confirmRename(tpl)"
                  @keydown.escape="cancelRename"
                  class="tpl-rename-input"
                />
                <h3 v-else class="tpl-card-title">{{ tpl.title || 'Senza titolo' }}</h3>
                <p class="tpl-card-meta">
                  <span :class="statusClass(tpl.status)">{{ tpl.status }}</span>
                  &middot; {{ formatDate(tpl.updated_at) }}
                </p>
              </div>
              <div class="tpl-card-actions-mini">
                <button @click="startRename(tpl)" class="tpl-icon-btn" title="Rinomina">&#9998;</button>
                <button @click="deleteTemplate(tpl.id, tpl.title)" class="tpl-icon-btn tpl-icon-btn-danger" title="Elimina">&times;</button>
              </div>
            </div>
            <div class="tpl-card-bottom">
              <!-- Shortcode for pages -->
              <code v-if="tpl.type !== 'header' && tpl.type !== 'footer' && tpl.type !== 'single' && tpl.type !== 'megapanel' && tpl.type !== '404'" class="tpl-shortcode">
                [olo_template id="{{ tpl.id }}"]
              </code>
              <!-- Activate/Deactivate for headers -->
              <template v-if="tpl.type === 'header'">
                <button
                  v-if="tpl.id === activeHeaderId"
                  @click="deactivateHeader"
                  class="tpl-activate-btn active"
                >Disattiva</button>
                <button
                  v-else
                  @click="activateHeader(tpl.id)"
                  :disabled="tpl.status !== 'published'"
                  :class="['tpl-activate-btn', { disabled: tpl.status !== 'published' }]"
                  :title="tpl.status !== 'published' ? 'Pubblica prima di attivare' : 'Imposta come header attivo'"
                >Attiva</button>
              </template>
              <!-- Activate/Deactivate for footers -->
              <template v-if="tpl.type === 'footer'">
                <button
                  v-if="tpl.id === activeFooterId"
                  @click="deactivateFooter"
                  class="tpl-activate-btn active"
                >Disattiva</button>
                <button
                  v-else
                  @click="activateFooter(tpl.id)"
                  :disabled="tpl.status !== 'published'"
                  :class="['tpl-activate-btn', { disabled: tpl.status !== 'published' }]"
                  :title="tpl.status !== 'published' ? 'Pubblica prima di attivare' : 'Imposta come footer attivo'"
                >Attiva</button>
              </template>
              <!-- Activate/Deactivate for singles -->
              <template v-if="tpl.type === 'single'">
                <button
                  v-if="isActiveSingle(tpl)"
                  @click="deactivateSingle(getSinglePostType(tpl))"
                  class="tpl-activate-btn active"
                >Disattiva</button>
                <button
                  v-else
                  @click="activateSingle(tpl.id, getSinglePostType(tpl))"
                  :disabled="tpl.status !== 'published'"
                  :class="['tpl-activate-btn', { disabled: tpl.status !== 'published' }]"
                  :title="tpl.status !== 'published' ? 'Pubblica prima di attivare' : 'Imposta come template single attivo'"
                >Attiva</button>
              </template>
              <!-- Activate/Deactivate for 404 -->
              <template v-if="tpl.type === '404'">
                <button
                  v-if="tpl.id === active404Id"
                  @click="deactivate404"
                  class="tpl-activate-btn active"
                >Disattiva</button>
                <button
                  v-else
                  @click="activate404(tpl.id)"
                  :disabled="tpl.status !== 'published'"
                  :class="['tpl-activate-btn', { disabled: tpl.status !== 'published' }]"
                  :title="tpl.status !== 'published' ? 'Pubblica prima di attivare' : 'Imposta come pagina 404 attiva'"
                >Attiva</button>
              </template>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick } from 'vue';

const emit = defineEmits(['edit', 'create']);

const oloData = window.oloData || {};
const wpAdminUrl = (oloData.restUrl || '').replace('/wp-json/olo/v1', '/wp-admin/');
const pluginUrl = oloData.pluginUrl || '';

const loading = ref(true);
const templates = ref([]);
const showNewMenu = ref(false);
const dropdownRef = ref(null);
const activeHeaderId = ref(parseInt(oloData.activeHeaderId) || 0);
const activeFooterId = ref(parseInt(oloData.activeFooterId) || 0);
const active404Id = ref(parseInt(oloData.active404Id) || 0);
const activeSingles = ref({ ...(oloData.activeSingles || {}) });
const postTypes = oloData.postTypes || [];
const renamingId = ref(null);
const renameDraft = ref('');
const renameInputRef = ref(null);
const importFileRef = ref(null);

const tabs = [
  { value: 'all', label: 'Tutti' },
  { value: 'page', label: 'Pagine' },
  { value: 'header', label: 'Header' },
  { value: 'footer', label: 'Footer' },
  { value: 'single', label: 'Single' },
  { value: 'megapanel', label: 'Mega Panel' },
  { value: '404', label: '404' },
];
const activeTab = ref('all');

const filteredTemplates = computed(() => {
  if (activeTab.value === 'all') return templates.value;
  return templates.value.filter(t => (t.type || 'page') === activeTab.value);
});

function handleClickOutside(e) {
  if (dropdownRef.value && !dropdownRef.value.contains(e.target)) {
    showNewMenu.value = false;
  }
}

async function fetchTemplates() {
  loading.value = true;
  try {
    const res = await fetch(`${oloData.restUrl}/templates?per_page=200`, {
      headers: { 'X-WP-Nonce': oloData.nonce },
    });
    if (!res.ok) throw new Error('Failed to fetch');
    const data = await res.json();
    templates.value = data.items || [];
  } catch (err) {
    console.error('fetchTemplates error:', err);
    templates.value = [];
  } finally {
    loading.value = false;
  }
}

function createNew(type) {
  showNewMenu.value = false;
  emit('create', type);
}

async function duplicateTemplate(id) {
  const tpl = templates.value.find((t) => t.id === id);
  if (!tpl) return;

  try {
    const res = await fetch(`${oloData.restUrl}/templates`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-WP-Nonce': oloData.nonce,
      },
      body: JSON.stringify({
        title: `${tpl.title || 'Senza titolo'} (Copia)`,
        type: tpl.type || 'page',
        content: tpl.content || [],
        settings: tpl.settings || {},
        status: 'draft',
      }),
    });
    if (res.ok) {
      await fetchTemplates();
    }
  } catch (err) {
    console.error('duplicateTemplate error:', err);
  }
}

async function deleteTemplate(id, title) {
  if (!confirm(`Eliminare "${title || 'Senza titolo'}"? Questa azione non può essere annullata.`)) return;

  try {
    const res = await fetch(`${oloData.restUrl}/templates/${id}`, {
      method: 'DELETE',
      headers: { 'X-WP-Nonce': oloData.nonce },
    });
    if (res.ok) {
      templates.value = templates.value.filter((t) => t.id !== id);
      if (id === activeHeaderId.value) {
        activeHeaderId.value = 0;
      }
      if (id === activeFooterId.value) {
        activeFooterId.value = 0;
      }
      if (id === active404Id.value) {
        active404Id.value = 0;
      }
      for (const [pt, tplId] of Object.entries(activeSingles.value)) {
        if (tplId === id) {
          const updated = { ...activeSingles.value };
          delete updated[pt];
          activeSingles.value = updated;
          break;
        }
      }
    }
  } catch (err) {
    console.error('deleteTemplate error:', err);
  }
}

function startRename(tpl) {
  renameDraft.value = tpl.title || '';
  renamingId.value = tpl.id;
  nextTick(() => {
    renameInputRef.value?.focus();
    renameInputRef.value?.select();
  });
}

async function confirmRename(tpl) {
  if (renamingId.value !== tpl.id) return;
  renamingId.value = null;
  const newTitle = renameDraft.value.trim();
  if (!newTitle || newTitle === tpl.title) return;

  try {
    const res = await fetch(`${oloData.restUrl}/templates/${tpl.id}`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'X-WP-Nonce': oloData.nonce,
      },
      body: JSON.stringify({ title: newTitle }),
    });
    if (res.ok) {
      tpl.title = newTitle;
    }
  } catch (err) {
    console.error('renameTemplate error:', err);
  }
}

function cancelRename() {
  renamingId.value = null;
}

async function activateHeader(id) {
  try {
    const res = await fetch(`${oloData.restUrl}/header/activate`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'X-WP-Nonce': oloData.nonce,
      },
      body: JSON.stringify({ id }),
    });
    if (res.ok) {
      activeHeaderId.value = id;
      oloData.activeHeaderId = id;
    }
  } catch (err) {
    console.error('activateHeader error:', err);
  }
}

async function deactivateHeader() {
  try {
    const res = await fetch(`${oloData.restUrl}/header/activate`, {
      method: 'DELETE',
      headers: { 'X-WP-Nonce': oloData.nonce },
    });
    if (res.ok) {
      activeHeaderId.value = 0;
      oloData.activeHeaderId = 0;
    }
  } catch (err) {
    console.error('deactivateHeader error:', err);
  }
}

async function activateFooter(id) {
  try {
    const res = await fetch(`${oloData.restUrl}/footer/activate`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'X-WP-Nonce': oloData.nonce,
      },
      body: JSON.stringify({ id }),
    });
    if (res.ok) {
      activeFooterId.value = id;
      oloData.activeFooterId = id;
    }
  } catch (err) {
    console.error('activateFooter error:', err);
  }
}

async function deactivateFooter() {
  try {
    const res = await fetch(`${oloData.restUrl}/footer/activate`, {
      method: 'DELETE',
      headers: { 'X-WP-Nonce': oloData.nonce },
    });
    if (res.ok) {
      activeFooterId.value = 0;
      oloData.activeFooterId = 0;
    }
  } catch (err) {
    console.error('deactivateFooter error:', err);
  }
}

async function activate404(id) {
  try {
    const res = await fetch(`${oloData.restUrl}/404/activate`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'X-WP-Nonce': oloData.nonce,
      },
      body: JSON.stringify({ id }),
    });
    if (res.ok) {
      active404Id.value = id;
      oloData.active404Id = id;
    }
  } catch (err) {
    console.error('activate404 error:', err);
  }
}

async function deactivate404() {
  try {
    const res = await fetch(`${oloData.restUrl}/404/activate`, {
      method: 'DELETE',
      headers: { 'X-WP-Nonce': oloData.nonce },
    });
    if (res.ok) {
      active404Id.value = 0;
      oloData.active404Id = 0;
    }
  } catch (err) {
    console.error('deactivate404 error:', err);
  }
}

function getSinglePostType(tpl) {
  return tpl.settings?.single_post_type || '';
}

function isActiveSingle(tpl) {
  const pt = getSinglePostType(tpl);
  return pt && activeSingles.value[pt] === tpl.id;
}

async function activateSingle(id, postType) {
  if (!postType) return;
  try {
    const res = await fetch(`${oloData.restUrl}/single/activate`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'X-WP-Nonce': oloData.nonce,
      },
      body: JSON.stringify({ id, post_type: postType }),
    });
    if (res.ok) {
      activeSingles.value = { ...activeSingles.value, [postType]: id };
      oloData.activeSingles = { ...activeSingles.value };
    }
  } catch (err) {
    console.error('activateSingle error:', err);
  }
}

async function deactivateSingle(postType) {
  if (!postType) return;
  try {
    const res = await fetch(`${oloData.restUrl}/single/activate`, {
      method: 'DELETE',
      headers: {
        'Content-Type': 'application/json',
        'X-WP-Nonce': oloData.nonce,
      },
      body: JSON.stringify({ post_type: postType }),
    });
    if (res.ok) {
      const updated = { ...activeSingles.value };
      delete updated[postType];
      activeSingles.value = updated;
      oloData.activeSingles = { ...updated };
    }
  } catch (err) {
    console.error('deactivateSingle error:', err);
  }
}

function createNewSingle(postType) {
  showNewMenu.value = false;
  emit('create', { type: 'single', postType });
}

async function exportTemplate(id) {
  try {
    const res = await fetch(`${oloData.restUrl}/templates/${id}/export`, {
      headers: { 'X-WP-Nonce': oloData.nonce },
    });
    if (!res.ok) throw new Error('Export failed');
    const data = await res.json();
    const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `template-${(data.title || 'export').replace(/[^a-z0-9]/gi, '-').toLowerCase()}.json`;
    a.click();
    URL.revokeObjectURL(url);
  } catch (err) {
    console.error('exportTemplate error:', err);
    alert('Errore durante l\'esportazione del template.');
  }
}

function triggerImport() {
  importFileRef.value?.click();
}

async function handleImportFile(e) {
  const file = e.target.files?.[0];
  if (!file) return;
  e.target.value = '';

  try {
    const text = await file.text();
    const json = JSON.parse(text);

    if (json.olo_export !== 'template') {
      alert('File non valido: non è un export Olobuild template.');
      return;
    }

    const res = await fetch(`${oloData.restUrl}/templates/import`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-WP-Nonce': oloData.nonce,
      },
      body: JSON.stringify(json),
    });

    if (!res.ok) {
      const err = await res.json();
      throw new Error(err.message || 'Import failed');
    }

    await fetchTemplates();
  } catch (err) {
    console.error('importTemplate error:', err);
    alert('Errore durante l\'importazione: ' + err.message);
  }
}

function statusClass(status) {
  const map = {
    published: 'status-published',
    draft: 'status-draft',
    archived: 'status-archived',
  };
  return map[status] || 'status-archived';
}

function countElements(content) {
  if (!Array.isArray(content)) return 0;
  let count = 0;
  for (const node of content) {
    count++;
    if (Array.isArray(node.children)) {
      count += countElements(node.children);
    }
  }
  return count;
}

function formatDate(dateStr) {
  if (!dateStr) return '';
  const d = new Date(dateStr);
  return d.toLocaleDateString('it-IT', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
  });
}

onMounted(() => {
  fetchTemplates();
  document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
});
</script>

<style scoped>
/* ═══ Template Manager — warm, minimal design ═══ */
/* NOTE: !important needed to override Tailwind preflight (border-width:0, background:transparent on button/*, etc.) */
.tpl-page {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  color: #1a1a1a !important;
}
.tpl-container { }

/* ── Actions bar ── */
.tpl-header-actions { display: flex; align-items: center; justify-content: flex-end; gap: 8px; margin-bottom: 20px; }

/* ── Buttons ── */
.tpl-btn {
  display: inline-flex !important; align-items: center; gap: 6px;
  padding: 9px 18px !important; border-radius: 10px !important; font-size: 13px !important; font-weight: 600;
  cursor: pointer; transition: all .15s; font-family: inherit; border: none !important;
  text-decoration: none !important; white-space: nowrap; line-height: 1.4 !important;
}
.tpl-btn-primary { background: #1a1a1a !important; color: #fff !important; }
.tpl-btn-primary:hover { background: #333 !important; box-shadow: 0 4px 12px rgba(0,0,0,.15); }
.tpl-btn-outline {
  background: #fff !important; color: #666 !important; border: 1.5px solid #eaeaea !important;
}
.tpl-btn-outline:hover { background: #fafafa !important; color: #1a1a1a !important; border-color: #ccc !important; }
.tpl-btn-outline-light {
  background: rgba(255,255,255,.92) !important; color: #1a1a1a !important; border: 1.5px solid #eaeaea !important;
}
.tpl-btn-outline-light:hover { background: #fff !important; }
.tpl-btn-sm { padding: 7px 14px !important; font-size: 12px !important; }

/* ── Dropdown ── */
.tpl-dropdown { position: relative; }
.tpl-dropdown-menu {
  position: absolute; right: 0; top: 100%; margin-top: 6px;
  width: 200px; background: #fff !important; border: 1px solid #eaeaea !important;
  border-radius: 12px !important; box-shadow: 0 12px 40px rgba(0,0,0,.12) !important;
  z-index: 10; overflow: hidden;
}
.tpl-dropdown-item {
  display: block; width: 100%; text-align: left; padding: 10px 16px;
  font-size: 13px; color: #1a1a1a !important; background: none !important; border: none !important;
  cursor: pointer; font-family: inherit; transition: background .1s;
}
.tpl-dropdown-item:hover { background: #f5f0eb !important; }
.tpl-dropdown-sep { border-top: 1px solid #eaeaea !important; }
.tpl-dropdown-label {
  padding: 8px 16px 4px; font-size: 10px; text-transform: uppercase;
  font-weight: 700; color: #999; letter-spacing: .05em;
}

/* ── Tabs ── */
.tpl-tabs {
  display: flex !important; gap: 4px; margin-bottom: 24px;
  background: #fff !important; border-radius: 12px !important; padding: 4px !important;
  width: fit-content; border: 1px solid #eaeaea !important;
}
.tpl-tab {
  padding: 7px 16px !important; border-radius: 8px !important; font-size: 13px !important; font-weight: 600;
  color: #999 !important; background: transparent !important; border: none !important; cursor: pointer;
  font-family: inherit; transition: all .15s;
}
.tpl-tab:hover { color: #1a1a1a !important; }
.tpl-tab.active { background: #1a1a1a !important; color: #fff !important; }

/* ── Loading ── */
.tpl-loading { text-align: center; padding: 64px 0; color: #999; font-size: 15px; }

/* ── Empty state ── */
.tpl-empty {
  text-align: center; padding: 64px 24px;
  background: #fff !important; border-radius: 14px !important; border: 1px solid #eaeaea !important;
  box-shadow: 0 2px 8px rgba(0,0,0,.04) !important;
}
.tpl-empty-icon {
  width: 64px; height: 64px; margin: 0 auto 16px;
  background: #f5f0eb !important; border-radius: 16px;
  display: flex; align-items: center; justify-content: center; color: #999;
}
.tpl-empty h2 { font-size: 16px; font-weight: 600; color: #1a1a1a !important; margin: 0 0 6px; }
.tpl-empty p { font-size: 13px; color: #999; margin: 0 0 20px; }

/* ── Grid ── */
.tpl-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 16px;
}

/* ── Card ── */
.tpl-card {
  background: #fff !important; border-radius: 14px !important; border: 1px solid #eaeaea !important;
  overflow: hidden; transition: border-color .15s, box-shadow .15s;
  box-shadow: 0 2px 8px rgba(0,0,0,.04) !important;
}
.tpl-card:hover { border-color: #ccc !important; box-shadow: 0 8px 24px rgba(0,0,0,.08) !important; }

.tpl-card-preview {
  height: 160px; background: #f5f0eb !important;
  display: flex; align-items: center; justify-content: center;
  border-bottom: 1px solid #eaeaea !important; position: relative;
}
.tpl-card-count { color: #bbb; font-size: 13px; }

/* ── Type badges ── */
.tpl-type-badge {
  position: absolute; top: 10px; left: 10px;
  padding: 3px 10px !important; font-size: 10px; font-weight: 700;
  text-transform: uppercase; border-radius: 20px !important; letter-spacing: .02em;
  border: none !important;
}
.tpl-type-badge.purple { background: #f0e6ff !important; color: #7c3aed !important; }
.tpl-type-badge.teal { background: #e0faf4 !important; color: #0d9488 !important; }
.tpl-type-badge.amber { background: #fff3e0 !important; color: #d97706 !important; }
.tpl-type-badge.indigo { background: #e8eaf6 !important; color: #4f46e5 !important; }
.tpl-type-badge.red { background: #fde8e8 !important; color: #dc2626 !important; }

.tpl-active-badge {
  position: absolute; top: 10px; right: 10px;
  padding: 3px 10px !important; font-size: 10px; font-weight: 700;
  text-transform: uppercase; border-radius: 20px !important;
  background: #e6f9ee !important; color: #059669 !important;
  border: none !important;
}

/* ── Overlay ── */
.tpl-card-overlay {
  position: absolute; inset: 0;
  background: rgba(26, 26, 26, .6) !important; backdrop-filter: blur(2px);
  display: flex; align-items: center; justify-content: center; gap: 8px;
  opacity: 0; transition: opacity .2s;
}
.tpl-card:hover .tpl-card-overlay { opacity: 1; }

/* ── Card info ── */
.tpl-card-info { padding: 14px 16px; }
.tpl-card-info-top { display: flex; align-items: flex-start; justify-content: space-between; }
.tpl-card-info-left { flex: 1; min-width: 0; }
.tpl-card-title {
  font-size: 13px; font-weight: 600; color: #1a1a1a !important; margin: 0;
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.tpl-card-meta { font-size: 11px; color: #999; margin: 4px 0 0; }
.status-published { color: #059669 !important; }
.status-draft { color: #d97706 !important; }
.status-archived { color: #999 !important; }

.tpl-card-actions-mini { display: flex; align-items: center; gap: 2px; flex-shrink: 0; }
.tpl-icon-btn {
  background: none !important; border: none !important; cursor: pointer; font-size: 13px;
  color: #ccc !important; padding: 2px 4px; transition: color .15s;
}
.tpl-icon-btn:hover { color: #e8622a !important; }
.tpl-icon-btn-danger:hover { color: #dc2626 !important; }

.tpl-rename-input {
  width: 100% !important; padding: 4px 8px !important; font-size: 13px; font-weight: 600;
  border: 1.5px solid #e8622a !important; border-radius: 8px !important; background: #fff !important;
  color: #1a1a1a !important; outline: none; font-family: inherit;
}

/* ── Bottom area ── */
.tpl-card-bottom { display: flex; align-items: center; gap: 8px; margin-top: 10px; }
.tpl-shortcode {
  font-size: 10px; color: #999 !important; background: #f5f5f5 !important;
  padding: 3px 8px !important; border-radius: 6px !important; font-family: 'SF Mono', Monaco, monospace;
  border: none !important;
}

/* ── Activate buttons ── */
.tpl-activate-btn {
  font-size: 11px !important; padding: 4px 12px !important; border-radius: 6px !important; font-weight: 600;
  border: 1.5px solid #eaeaea !important; background: #fff !important; color: #666 !important;
  cursor: pointer; transition: all .15s; font-family: inherit;
}
.tpl-activate-btn:hover { border-color: #1a1a1a !important; color: #1a1a1a !important; }
.tpl-activate-btn.active {
  background: #e6f9ee !important; color: #059669 !important; border-color: #b2e5cc !important;
}
.tpl-activate-btn.active:hover {
  background: #fde8e8 !important; color: #dc2626 !important; border-color: #f5b7b7 !important;
}
.tpl-activate-btn.disabled {
  opacity: .4; cursor: not-allowed;
}
.tpl-activate-btn.disabled:hover { border-color: #eaeaea !important; color: #666 !important; }

/* ── Responsive ── */
@media (max-width: 900px) {
  .tpl-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 768px) {
  .tpl-header-actions { flex-wrap: wrap; }
  .tpl-grid { grid-template-columns: 1fr; }
  .tpl-tabs { overflow-x: auto; width: 100%; }
}
</style>
