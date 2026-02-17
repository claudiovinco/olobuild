<template>
  <div class="mb-min-h-screen mb-bg-gray-900 mb-text-gray-100 mb-p-8">
    <!-- Header -->
    <div class="mb-max-w-5xl mb-mx-auto">
      <div class="mb-flex mb-items-center mb-justify-between mb-mb-8">
        <div class="mb-flex mb-items-center mb-gap-4">
          <a
            :href="wpAdminUrl"
            class="mb-text-gray-400 hover:mb-text-gray-200 mb-text-lg mb-transition-colors"
            title="Torna a WordPress"
          >
            &larr;
          </a>
          <div>
            <img
              :src="pluginUrl + 'assets/img/olobuild-logo-200.png'"
              alt="Olobuild"
              class="mb-h-10 mb-object-contain"
            />
            <p class="mb-text-sm mb-text-gray-400 mb-mt-1">Gestisci i tuoi template</p>
          </div>
        </div>
        <div class="mb-flex mb-items-center mb-gap-3">
          <a
            :href="wpAdminUrl"
            class="mb-px-4 mb-py-2.5 mb-border mb-border-gray-600 mb-text-gray-300 mb-rounded-lg mb-font-medium mb-text-sm hover:mb-bg-gray-700 mb-transition-colors mb-no-underline"
          >
            &larr; WordPress
          </a>
          <!-- Import template -->
          <button
            @click="triggerImport"
            class="mb-px-4 mb-py-2.5 mb-border mb-border-gray-600 mb-text-gray-300 mb-rounded-lg mb-font-medium mb-text-sm hover:mb-bg-gray-700 mb-transition-colors"
          >
            &#8593; Importa
          </button>
          <input
            ref="importFileRef"
            type="file"
            accept=".json"
            class="mb-hidden"
            @change="handleImportFile"
          />
          <!-- New template dropdown -->
          <div class="mb-relative" ref="dropdownRef">
            <button
              @click="showNewMenu = !showNewMenu"
              class="mb-px-5 mb-py-2.5 mb-bg-primary-600 mb-text-white mb-rounded-lg mb-font-medium mb-text-sm hover:mb-bg-primary-700 mb-transition-colors"
            >
              + Nuovo Template
            </button>
            <div
              v-if="showNewMenu"
              class="mb-absolute mb-right-0 mb-mt-1 mb-w-44 mb-bg-gray-800 mb-border mb-border-gray-600 mb-rounded-lg mb-shadow-lg mb-z-10 mb-overflow-hidden"
            >
              <button
                @click="createNew('page')"
                class="mb-block mb-w-full mb-text-left mb-px-4 mb-py-2.5 mb-text-sm mb-text-gray-200 hover:mb-bg-gray-700 mb-transition-colors"
              >
                Nuova Pagina
              </button>
              <button
                @click="createNew('header')"
                class="mb-block mb-w-full mb-text-left mb-px-4 mb-py-2.5 mb-text-sm mb-text-gray-200 hover:mb-bg-gray-700 mb-transition-colors"
              >
                Nuovo Header
              </button>
              <button
                @click="createNew('footer')"
                class="mb-block mb-w-full mb-text-left mb-px-4 mb-py-2.5 mb-text-sm mb-text-gray-200 hover:mb-bg-gray-700 mb-transition-colors"
              >
                Nuovo Footer
              </button>
              <div class="mb-border-t mb-border-gray-700"></div>
              <div class="mb-px-4 mb-py-2 mb-text-[10px] mb-text-gray-500 mb-uppercase mb-font-bold">Template Single</div>
              <button
                v-for="pt in postTypes"
                :key="pt.value"
                @click="createNewSingle(pt.value)"
                class="mb-block mb-w-full mb-text-left mb-px-4 mb-py-2.5 mb-text-sm mb-text-gray-200 hover:mb-bg-gray-700 mb-transition-colors"
              >
                Single: {{ pt.label }}
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Tab filter -->
      <div class="mb-flex mb-gap-1 mb-mb-6 mb-bg-gray-800 mb-rounded-lg mb-p-1 mb-w-fit">
        <button
          v-for="tab in tabs"
          :key="tab.value"
          @click="activeTab = tab.value"
          :class="[
            'mb-px-4 mb-py-1.5 mb-rounded-md mb-text-sm mb-font-medium mb-transition-colors',
            activeTab === tab.value
              ? 'mb-bg-primary-600 mb-text-white'
              : 'mb-text-gray-400 hover:mb-text-gray-200'
          ]"
        >
          {{ tab.label }}
        </button>
      </div>

      <!-- Loading state -->
      <div v-if="loading" class="mb-text-center mb-py-16 mb-text-gray-500">
        <p class="mb-text-lg">Caricamento template...</p>
      </div>

      <!-- Empty state -->
      <div
        v-else-if="filteredTemplates.length === 0"
        class="mb-text-center mb-py-16 mb-bg-gray-800 mb-rounded-xl mb-border mb-border-gray-700"
      >
        <span class="mb-text-5xl mb-block mb-mb-4 mb-opacity-30">&#x1F9E9;</span>
        <h2 class="mb-text-lg mb-font-medium mb-text-gray-300 mb-mb-2">
          {{ activeTab === 'header' ? 'Nessun header' : activeTab === 'footer' ? 'Nessun footer' : activeTab === 'single' ? 'Nessun template single' : 'Nessun template' }}
        </h2>
        <p class="mb-text-sm mb-text-gray-500 mb-mb-6">
          {{ activeTab === 'header' ? 'Crea il tuo primo template header.' : activeTab === 'footer' ? 'Crea il tuo primo template footer.' : activeTab === 'single' ? 'Crea un template single per un custom post type.' : 'Crea il tuo primo template per iniziare.' }}
        </p>
        <button
          v-if="activeTab !== 'single'"
          @click="createNew(activeTab === 'all' ? 'page' : activeTab)"
          class="mb-px-5 mb-py-2.5 mb-bg-primary-600 mb-text-white mb-rounded-lg mb-font-medium mb-text-sm hover:mb-bg-primary-700 mb-transition-colors"
        >
          {{ activeTab === 'header' ? 'Crea Header' : activeTab === 'footer' ? 'Crea Footer' : 'Crea Template' }}
        </button>
      </div>

      <!-- Template grid -->
      <div v-else class="mb-grid mb-grid-cols-1 md:mb-grid-cols-2 lg:mb-grid-cols-3 mb-gap-4">
        <div
          v-for="tpl in filteredTemplates"
          :key="tpl.id"
          class="mb-bg-gray-800 mb-rounded-xl mb-border mb-border-gray-700 mb-overflow-hidden mb-group hover:mb-border-gray-600 mb-transition-colors"
        >
          <!-- Preview area -->
          <div class="mb-h-40 mb-bg-gray-750 mb-flex mb-items-center mb-justify-center mb-border-b mb-border-gray-700 mb-relative">
            <div class="mb-text-gray-600 mb-text-sm">
              {{ countElements(tpl.content) }} elementi
            </div>
            <!-- Type badge -->
            <span
              v-if="tpl.type === 'header'"
              class="mb-absolute mb-top-2 mb-left-2 mb-px-2 mb-py-0.5 mb-text-[10px] mb-font-bold mb-uppercase mb-rounded mb-bg-purple-600/20 mb-text-purple-300 mb-border mb-border-purple-500/30"
            >
              Header
            </span>
            <span
              v-if="tpl.type === 'footer'"
              class="mb-absolute mb-top-2 mb-left-2 mb-px-2 mb-py-0.5 mb-text-[10px] mb-font-bold mb-uppercase mb-rounded mb-bg-teal-600/20 mb-text-teal-300 mb-border mb-border-teal-500/30"
            >
              Footer
            </span>
            <span
              v-if="tpl.type === 'single'"
              class="mb-absolute mb-top-2 mb-left-2 mb-px-2 mb-py-0.5 mb-text-[10px] mb-font-bold mb-uppercase mb-rounded mb-bg-amber-600/20 mb-text-amber-300 mb-border mb-border-amber-500/30"
            >
              Single: {{ getSinglePostType(tpl) }}
            </span>
            <!-- Active header indicator -->
            <span
              v-if="tpl.type === 'header' && tpl.id === activeHeaderId"
              class="mb-absolute mb-top-2 mb-right-2 mb-px-2 mb-py-0.5 mb-text-[10px] mb-font-bold mb-uppercase mb-rounded mb-bg-green-600/20 mb-text-green-300 mb-border mb-border-green-500/30"
            >
              Attivo
            </span>
            <!-- Active footer indicator -->
            <span
              v-if="tpl.type === 'footer' && tpl.id === activeFooterId"
              class="mb-absolute mb-top-2 mb-right-2 mb-px-2 mb-py-0.5 mb-text-[10px] mb-font-bold mb-uppercase mb-rounded mb-bg-green-600/20 mb-text-green-300 mb-border mb-border-green-500/30"
            >
              Attivo
            </span>
            <!-- Active single indicator -->
            <span
              v-if="tpl.type === 'single' && isActiveSingle(tpl)"
              class="mb-absolute mb-top-2 mb-right-2 mb-px-2 mb-py-0.5 mb-text-[10px] mb-font-bold mb-uppercase mb-rounded mb-bg-green-600/20 mb-text-green-300 mb-border mb-border-green-500/30"
            >
              Attivo
            </span>
            <!-- Hover overlay -->
            <div class="mb-absolute mb-inset-0 mb-bg-black/50 mb-flex mb-items-center mb-justify-center mb-gap-3 mb-opacity-0 group-hover:mb-opacity-100 mb-transition-opacity">
              <button
                @click="$emit('edit', tpl.id)"
                class="mb-px-4 mb-py-2 mb-bg-primary-600 mb-text-white mb-rounded-lg mb-text-sm mb-font-medium hover:mb-bg-primary-700"
              >
                Modifica
              </button>
              <button
                @click="duplicateTemplate(tpl.id)"
                class="mb-px-4 mb-py-2 mb-bg-gray-600 mb-text-white mb-rounded-lg mb-text-sm mb-font-medium hover:mb-bg-gray-500"
              >
                Duplica
              </button>
              <button
                @click="exportTemplate(tpl.id)"
                class="mb-px-4 mb-py-2 mb-bg-gray-600 mb-text-white mb-rounded-lg mb-text-sm mb-font-medium hover:mb-bg-gray-500"
              >
                Esporta
              </button>
            </div>
          </div>
          <!-- Info -->
          <div class="mb-p-4">
            <div class="mb-flex mb-items-start mb-justify-between">
              <div class="mb-flex-1 mb-min-w-0">
                <!-- Inline rename -->
                <input
                  v-if="renamingId === tpl.id"
                  :ref="el => { if (el) renameInputRef = el }"
                  v-model="renameDraft"
                  @blur="confirmRename(tpl)"
                  @keydown.enter="confirmRename(tpl)"
                  @keydown.escape="cancelRename"
                  class="mb-w-full mb-bg-gray-700 mb-border mb-border-primary-500 mb-rounded mb-px-2 mb-py-0.5 mb-text-sm mb-text-gray-200 mb-font-medium mb-outline-none"
                />
                <h3 v-else class="mb-font-medium mb-text-gray-200 mb-text-sm mb-truncate">{{ tpl.title || 'Senza titolo' }}</h3>
                <p class="mb-text-xs mb-text-gray-500 mb-mt-1">
                  <span :class="statusClass(tpl.status)">{{ tpl.status }}</span>
                  &middot; {{ formatDate(tpl.updated_at) }}
                </p>
              </div>
              <div class="mb-flex mb-items-center mb-gap-1 mb-shrink-0">
                <button
                  @click="startRename(tpl)"
                  class="mb-text-gray-600 hover:mb-text-primary-400 mb-transition-colors mb-text-xs"
                  title="Rinomina"
                >
                  &#9998;
                </button>
                <button
                  @click="deleteTemplate(tpl.id, tpl.title)"
                  class="mb-text-gray-600 hover:mb-text-red-400 mb-transition-colors mb-text-lg mb-leading-none"
                  title="Elimina"
                >
                  &times;
                </button>
              </div>
            </div>
            <div class="mb-mt-3 mb-flex mb-items-center mb-gap-2">
              <!-- Shortcode for pages -->
              <code v-if="tpl.type !== 'header' && tpl.type !== 'footer' && tpl.type !== 'single'" class="mb-text-[10px] mb-text-gray-500 mb-bg-gray-700 mb-px-2 mb-py-0.5 mb-rounded">
                [olo_template id="{{ tpl.id }}"]
              </code>
              <!-- Activate/Deactivate for headers -->
              <template v-if="tpl.type === 'header'">
                <button
                  v-if="tpl.id === activeHeaderId"
                  @click="deactivateHeader"
                  class="mb-text-[11px] mb-px-3 mb-py-1 mb-rounded mb-bg-green-600/20 mb-text-green-300 mb-border mb-border-green-500/30 hover:mb-bg-red-600/20 hover:mb-text-red-300 hover:mb-border-red-500/30 mb-transition-colors"
                >
                  Disattiva
                </button>
                <button
                  v-else
                  @click="activateHeader(tpl.id)"
                  :disabled="tpl.status !== 'published'"
                  :class="[
                    'mb-text-[11px] mb-px-3 mb-py-1 mb-rounded mb-border mb-transition-colors',
                    tpl.status === 'published'
                      ? 'mb-bg-gray-700 mb-text-gray-300 mb-border-gray-600 hover:mb-bg-primary-600/20 hover:mb-text-primary-300 hover:mb-border-primary-500/30'
                      : 'mb-bg-gray-700/50 mb-text-gray-600 mb-border-gray-700 mb-cursor-not-allowed'
                  ]"
                  :title="tpl.status !== 'published' ? 'Pubblica prima di attivare' : 'Imposta come header attivo'"
                >
                  Attiva
                </button>
              </template>
              <!-- Activate/Deactivate for footers -->
              <template v-if="tpl.type === 'footer'">
                <button
                  v-if="tpl.id === activeFooterId"
                  @click="deactivateFooter"
                  class="mb-text-[11px] mb-px-3 mb-py-1 mb-rounded mb-bg-green-600/20 mb-text-green-300 mb-border mb-border-green-500/30 hover:mb-bg-red-600/20 hover:mb-text-red-300 hover:mb-border-red-500/30 mb-transition-colors"
                >
                  Disattiva
                </button>
                <button
                  v-else
                  @click="activateFooter(tpl.id)"
                  :disabled="tpl.status !== 'published'"
                  :class="[
                    'mb-text-[11px] mb-px-3 mb-py-1 mb-rounded mb-border mb-transition-colors',
                    tpl.status === 'published'
                      ? 'mb-bg-gray-700 mb-text-gray-300 mb-border-gray-600 hover:mb-bg-teal-600/20 hover:mb-text-teal-300 hover:mb-border-teal-500/30'
                      : 'mb-bg-gray-700/50 mb-text-gray-600 mb-border-gray-700 mb-cursor-not-allowed'
                  ]"
                  :title="tpl.status !== 'published' ? 'Pubblica prima di attivare' : 'Imposta come footer attivo'"
                >
                  Attiva
                </button>
              </template>
              <!-- Activate/Deactivate for singles -->
              <template v-if="tpl.type === 'single'">
                <button
                  v-if="isActiveSingle(tpl)"
                  @click="deactivateSingle(getSinglePostType(tpl))"
                  class="mb-text-[11px] mb-px-3 mb-py-1 mb-rounded mb-bg-green-600/20 mb-text-green-300 mb-border mb-border-green-500/30 hover:mb-bg-red-600/20 hover:mb-text-red-300 hover:mb-border-red-500/30 mb-transition-colors"
                >
                  Disattiva
                </button>
                <button
                  v-else
                  @click="activateSingle(tpl.id, getSinglePostType(tpl))"
                  :disabled="tpl.status !== 'published'"
                  :class="[
                    'mb-text-[11px] mb-px-3 mb-py-1 mb-rounded mb-border mb-transition-colors',
                    tpl.status === 'published'
                      ? 'mb-bg-gray-700 mb-text-gray-300 mb-border-gray-600 hover:mb-bg-amber-600/20 hover:mb-text-amber-300 hover:mb-border-amber-500/30'
                      : 'mb-bg-gray-700/50 mb-text-gray-600 mb-border-gray-700 mb-cursor-not-allowed'
                  ]"
                  :title="tpl.status !== 'published' ? 'Pubblica prima di attivare' : 'Imposta come template single attivo'"
                >
                  Attiva
                </button>
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
    const res = await fetch(`${oloData.restUrl}/templates`, {
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
      // If deleting active header, footer, or single, clear it
      if (id === activeHeaderId.value) {
        activeHeaderId.value = 0;
      }
      if (id === activeFooterId.value) {
        activeFooterId.value = 0;
      }
      // Check if it was an active single template
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
    }
  } catch (err) {
    console.error('deactivateFooter error:', err);
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
  // Reset input so the same file can be re-selected
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
    published: 'mb-text-green-400',
    draft: 'mb-text-yellow-400',
    archived: 'mb-text-gray-500',
  };
  return map[status] || 'mb-text-gray-400';
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
