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
      <BuilderSidebar v-if="!builderStore.previewMode" />
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
