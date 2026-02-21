<template>
  <!-- Template List View -->
  <TemplateList
    v-if="currentView === 'list'"
    @edit="openBuilder"
    @create="createAndOpenBuilder"
  />

  <!-- Builder View -->
  <div v-else class="mb-flex mb-flex-col mb-h-screen mb-bg-gray-900 mb-text-gray-100 mb-overflow-hidden">
    <BuilderToolbar @back="goToList" />
    <div class="mb-flex mb-flex-1 mb-overflow-hidden">
      <BuilderSidebar v-if="!builderStore.previewMode && !sidebarCollapsed" :style="{ width: sidebarWidth + 'px', flexShrink: 0 }" />
      <!-- Resize handle + collapse toggle -->
      <div v-if="!builderStore.previewMode" style="flex-shrink:0;display:flex;flex-direction:column;align-items:center;background:#1f2937;border-right:1px solid #374151">
        <button @click="toggleSidebar" @mousedown.stop
          style="width:16px;height:24px;background:none;border:none;color:#6B7280;cursor:pointer;display:flex;align-items:center;justify-content:center;padding:0;margin:4px 0 0"
          :title="sidebarCollapsed ? 'Espandi sidebar' : 'Comprimi sidebar'"
        >
          <svg width="8" height="8" viewBox="0 0 8 8" fill="currentColor">
            <path :d="sidebarCollapsed ? 'M1 0l6 4-6 4z' : 'M7 0l-6 4 6 4z'"/>
          </svg>
        </button>
        <div v-if="!sidebarCollapsed" @mousedown.prevent="startResize($event)" style="flex:1;width:16px;cursor:col-resize"></div>
      </div>
      <BuilderCanvas />
      <BuilderInspector v-if="!builderStore.previewMode" />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { useTilesStore } from './stores/tiles';
import { useBuilderStore } from './stores/builder';
import { useHistory } from './composables/useHistory';
import TemplateList from './components/TemplateManager/TemplateList.vue';
import BuilderToolbar from './components/Builder/BuilderToolbar.vue';
import BuilderSidebar from './components/Builder/BuilderSidebar.vue';
import BuilderCanvas from './components/Builder/BuilderCanvas.vue';
import BuilderInspector from './components/Builder/BuilderInspector.vue';

const builderStore = useBuilderStore();
const tilesStore = useTilesStore();
const { initHistory, handleKeyboard } = useHistory();

const currentView = ref('list'); // 'list' | 'builder'

// Sidebar resize + collapse
var SIDEBAR_W_KEY = 'olo_sidebar_w';
var SIDEBAR_C_KEY = 'olo_sidebar_c';
const sidebarWidth = ref(parseInt(localStorage.getItem(SIDEBAR_W_KEY)) || 240);
const sidebarCollapsed = ref(localStorage.getItem(SIDEBAR_C_KEY) === '1');

function toggleSidebar() {
  sidebarCollapsed.value = !sidebarCollapsed.value;
  localStorage.setItem(SIDEBAR_C_KEY, sidebarCollapsed.value ? '1' : '');
}

function startResize(event) {
  var startX = event.clientX;
  var startW = sidebarWidth.value;
  document.body.style.cursor = 'col-resize';
  document.body.style.userSelect = 'none';
  function onMove(e) {
    var w = startW + (e.clientX - startX);
    if (w < 100) {
      sidebarCollapsed.value = true;
      localStorage.setItem(SIDEBAR_C_KEY, '1');
    } else {
      sidebarCollapsed.value = false;
      localStorage.setItem(SIDEBAR_C_KEY, '');
      sidebarWidth.value = Math.min(600, w);
    }
  }
  function onUp() {
    document.body.style.cursor = '';
    document.body.style.userSelect = '';
    localStorage.setItem(SIDEBAR_W_KEY, String(sidebarWidth.value));
    document.removeEventListener('mousemove', onMove);
    document.removeEventListener('mouseup', onUp);
  }
  document.addEventListener('mousemove', onMove);
  document.addEventListener('mouseup', onUp);
}

onMounted(async () => {
  tilesStore.fetchRegisteredTiles();
  initHistory();
  document.addEventListener('keydown', handleKeyboard);

  // Auto-open builder if templateId is passed from WordPress
  const oloData = window.oloData || {};
  const templateId = parseInt(oloData.templateId) || 0;
  if (templateId > 0) {
    await openBuilder(templateId);
  }
});

onUnmounted(() => {
  document.removeEventListener('keydown', handleKeyboard);
});

async function openBuilder(templateId) {
  await builderStore.loadTemplate(templateId);
  // Load template tiles into canvas
  if (builderStore.currentTemplate && builderStore.currentTemplate.content) {
    tilesStore.setCanvasTiles(builderStore.currentTemplate.content);
  }
  currentView.value = 'builder';
}

async function createAndOpenBuilder(typeOrObj = 'page') {
  // Accept string ('page') or object ({type: 'single', postType: 'location'})
  const oloData = window.oloData || {};
  let type, postType, title, settings;

  if (typeof typeOrObj === 'object' && typeOrObj !== null) {
    type = typeOrObj.type || 'page';
    postType = typeOrObj.postType || '';
    title = type === 'single' ? `Single: ${postType}` : 'Template senza titolo';
    settings = type === 'single' ? { single_post_type: postType } : {};
  } else {
    type = typeOrObj;
    postType = '';
    title = type === 'header' ? 'Nuovo Header'
          : type === 'footer' ? 'Nuovo Footer'
          : 'Template senza titolo';
    settings = {};
  }

  try {
    const res = await fetch(`${oloData.restUrl}/templates`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-WP-Nonce': oloData.nonce,
      },
      body: JSON.stringify({
        title,
        type,
        content: [],
        settings,
        status: 'draft',
      }),
    });
    if (res.ok) {
      const tpl = await res.json();
      builderStore.currentTemplate = tpl;
      tilesStore.setCanvasTiles([]);
      builderStore.isDirty = false;
      currentView.value = 'builder';
    }
  } catch (err) {
    console.error('Create template error:', err);
    builderStore.currentTemplate = { title, type, content: [], settings, status: 'draft' };
    tilesStore.setCanvasTiles([]);
    currentView.value = 'builder';
  }
}

function goToList() {
  if (builderStore.isDirty) {
    if (!confirm('Hai modifiche non salvate. Uscire comunque?')) return;
  }
  builderStore.deselectTile();
  builderStore.previewMode = false;
  currentView.value = 'list';
}
</script>
