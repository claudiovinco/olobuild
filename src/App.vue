<template>
  <!-- Template List View -->
  <TemplateList
    v-if="currentView === 'list'"
    @edit="openBuilder"
    @create="createAndOpenBuilder"
  />

  <!-- Builder View -->
  <div v-else class="mb-flex mb-flex-col mb-h-screen mb-bg-gray-900 mb-text-gray-100 mb-overflow-hidden">
    <!-- Skip link per accessibilità -->
    <a href="#olo-canvas" class="mb-sr-only focus:mb-not-sr-only focus:mb-fixed focus:mb-top-2 focus:mb-left-2 focus:mb-z-50 focus:mb-bg-gray-800 focus:mb-text-white focus:mb-px-4 focus:mb-py-2 focus:mb-rounded">
      Salta al contenuto
    </a>
    <BuilderToolbar @back="goToList" @open-revisions="onOpenRevisions" @open-finder="openFinder" @open-ai="aiAssistantRef?.open()" @open-library="templateLibraryRef?.open()" @open-themes="themeSelectorRef?.open()" />
    <div class="mb-flex mb-flex-1 mb-overflow-hidden">
      <BuilderSidebar v-if="!builderStore.previewMode && !sidebarCollapsed" :style="{ width: sidebarWidth + 'px', flexShrink: 0 }" role="complementary" aria-label="Pannello elementi" @save-as-template="section => templateLibraryRef?.openSaveDialog(section)" />
      <!-- Resize handle + collapse toggle -->
      <div v-if="!builderStore.previewMode" style="flex-shrink:0;display:flex;flex-direction:column;align-items:center;background:rgba(255,255,255,0.5);border-right:1px solid rgba(0,0,0,0.06)">
        <button @click="toggleSidebar" @mousedown.stop
          style="width:16px;height:24px;background:none;border:none;color:#aaa;cursor:pointer;display:flex;align-items:center;justify-content:center;padding:0;margin:4px 0 0"
          :title="sidebarCollapsed ? 'Espandi sidebar' : 'Comprimi sidebar'"
          :aria-label="sidebarCollapsed ? 'Espandi sidebar' : 'Comprimi sidebar'"
        >
          <svg width="8" height="8" viewBox="0 0 8 8" fill="currentColor">
            <path :d="sidebarCollapsed ? 'M1 0l6 4-6 4z' : 'M7 0l-6 4 6 4z'"/>
          </svg>
        </button>
        <div v-if="!sidebarCollapsed" @mousedown.prevent="startResize($event)" style="flex:1;width:16px;cursor:col-resize"></div>
      </div>
      <BuilderCanvas id="olo-canvas" role="main" aria-label="Area di lavoro" />
      <!-- Inspector resize handle + collapse toggle -->
      <div v-if="!builderStore.previewMode" style="flex-shrink:0;display:flex;flex-direction:column;align-items:center;background:rgba(255,255,255,0.5);border-left:1px solid rgba(0,0,0,0.06)">
        <button @click="toggleInspector" @mousedown.stop
          style="width:16px;height:24px;background:none;border:none;color:#aaa;cursor:pointer;display:flex;align-items:center;justify-content:center;padding:0;margin:4px 0 0"
          :title="inspectorCollapsed ? 'Espandi pannello' : 'Comprimi pannello'"
          :aria-label="inspectorCollapsed ? 'Espandi pannello' : 'Comprimi pannello'"
        >
          <svg width="8" height="8" viewBox="0 0 8 8" fill="currentColor">
            <path :d="inspectorCollapsed ? 'M7 0l-6 4 6 4z' : 'M1 0l6 4-6 4z'"/>
          </svg>
        </button>
        <div v-if="!inspectorCollapsed" @mousedown.prevent="startInspectorResize($event)" style="flex:1;width:16px;cursor:col-resize"></div>
      </div>
      <BuilderInspector v-if="!builderStore.previewMode && !inspectorCollapsed" :style="{ width: inspectorWidth + 'px', flexShrink: 0 }" role="complementary" aria-label="Proprietà" />
    </div>
    <!-- Finder / Ricerca rapida (Ctrl+K) -->
    <BuilderFinder ref="builderFinderRef" />
    <!-- Cronologia revisioni -->
    <RevisionHistory ref="revisionHistoryRef" />
    <!-- Assistente AI -->
    <AIAssistant ref="aiAssistantRef" />
    <!-- Libreria Template -->
    <TemplateLibrary ref="templateLibraryRef" />
    <!-- Temi -->
    <ThemeSelector ref="themeSelectorRef" />
    <!-- Insert Panel (clean mode) -->
    <InsertPanel ref="insertPanelRef" @open-library="templateLibraryRef?.open()" />
  </div>
</template>

<script setup>
import { ref, watch, onMounted, onUnmounted, provide } from 'vue';
import { useTilesStore } from './stores/tiles';
import { useBuilderStore } from './stores/builder';
import { useHistory } from './composables/useHistory';
import { useToast } from './composables/useToast.js';
import { t } from './i18n';
import TemplateList from './components/TemplateManager/TemplateList.vue';
import BuilderToolbar from './components/Builder/BuilderToolbar.vue';
import BuilderSidebar from './components/Builder/BuilderSidebar.vue';
import BuilderCanvas from './components/Builder/BuilderCanvas.vue';
import BuilderInspector from './components/Builder/BuilderInspector.vue';
import BuilderFinder from './components/Builder/BuilderFinder.vue';
import RevisionHistory from './components/Builder/RevisionHistory.vue';
import AIAssistant from './components/Builder/AIAssistant.vue';
import TemplateLibrary from './components/Builder/TemplateLibrary.vue';
import ThemeSelector from './components/Builder/ThemeSelector.vue';
import InsertPanel from './components/Builder/InsertPanel.vue';

const builderStore = useBuilderStore();
const tilesStore = useTilesStore();
const { initHistory, handleKeyboard } = useHistory();
const toast = useToast();
const revisionHistoryRef = ref(null);
const builderFinderRef = ref(null);
const aiAssistantRef = ref(null);
const templateLibraryRef = ref(null);
const themeSelectorRef = ref(null);
const insertPanelRef = ref(null);

function onOpenRevisions() {
  if (revisionHistoryRef.value) {
    revisionHistoryRef.value.open();
  }
}

function openFinder(columnId) {
  builderFinderRef.value?.open(columnId);
}

function onOpenFinderAfter() {
  builderFinderRef.value?.open();
}

provide('openFinder', openFinder);
window.__oloOpenFinder = openFinder;

function openInsertPanel(sectionIndex, initialTab) {
  insertPanelRef.value?.open(sectionIndex, initialTab);
}
provide('openInsertPanel', openInsertPanel);
window.__oloOpenInsertPanel = openInsertPanel;

// Dirty state: warn on page leave + title indicator
function onBeforeUnload(e) {
  if (builderStore.isDirty || builderStore.headerDirty || builderStore.footerDirty) {
    e.preventDefault();
    e.returnValue = '';
  }
}
watch([() => builderStore.isDirty, () => builderStore.headerDirty, () => builderStore.footerDirty], () => {
  const base = builderStore.currentTemplate?.title || 'Olobuild';
  const anyDirty = builderStore.isDirty || builderStore.headerDirty || builderStore.footerDirty;
  document.title = anyDirty ? '\u2022 ' + base + ' — Olobuild' : base + ' — Olobuild';
});

const currentView = ref('list'); // 'list' | 'builder'

// Sidebar resize + collapse
const SIDEBAR_W_KEY = 'olo_sidebar_w';
const SIDEBAR_C_KEY = 'olo_sidebar_c';
// V2 sidebar (rail 56 + panel ≥220) needs at least 280px to render comfortably.
const SIDEBAR_MIN_W = 280;
const _savedSidebarW = parseInt(localStorage.getItem(SIDEBAR_W_KEY));
const sidebarWidth = ref(
  Number.isFinite(_savedSidebarW) && _savedSidebarW >= SIDEBAR_MIN_W ? _savedSidebarW : 336
);
const sidebarCollapsed = ref(localStorage.getItem(SIDEBAR_C_KEY) === '1');

function toggleSidebar() {
  sidebarCollapsed.value = !sidebarCollapsed.value;
  localStorage.setItem(SIDEBAR_C_KEY, sidebarCollapsed.value ? '1' : '');
}

function blockIframes() {
  document.querySelectorAll('iframe').forEach(f => { f.style.pointerEvents = 'none'; });
}
function unblockIframes() {
  document.querySelectorAll('iframe').forEach(f => { f.style.pointerEvents = ''; });
}

function startResize(event) {
  var startX = event.clientX;
  var startW = sidebarWidth.value;
  document.body.style.cursor = 'col-resize';
  document.body.style.userSelect = 'none';
  blockIframes();
  function onMove(e) {
    var w = startW + (e.clientX - startX);
    if (w < 100) {
      sidebarCollapsed.value = true;
      localStorage.setItem(SIDEBAR_C_KEY, '1');
    } else {
      sidebarCollapsed.value = false;
      localStorage.setItem(SIDEBAR_C_KEY, '');
      sidebarWidth.value = Math.min(600, Math.max(SIDEBAR_MIN_W, w));
    }
  }
  function onUp() {
    document.body.style.cursor = '';
    document.body.style.userSelect = '';
    unblockIframes();
    localStorage.setItem(SIDEBAR_W_KEY, String(sidebarWidth.value));
    document.removeEventListener('mousemove', onMove);
    document.removeEventListener('mouseup', onUp);
  }
  document.addEventListener('mousemove', onMove);
  document.addEventListener('mouseup', onUp);
}

// Inspector resize + collapse
const INSPECTOR_W_KEY = 'olo_inspector_w';
const INSPECTOR_C_KEY = 'olo_inspector_c';
// V2 inspector (1fr content + 64px rail) needs at least 320px to render comfortably.
const INSPECTOR_MIN_W = 320;
const _savedInspectorW = parseInt(localStorage.getItem(INSPECTOR_W_KEY));
const inspectorWidth = ref(
  Number.isFinite(_savedInspectorW) && _savedInspectorW >= INSPECTOR_MIN_W ? _savedInspectorW : 384
);
const inspectorCollapsed = ref(localStorage.getItem(INSPECTOR_C_KEY) === '1');

function toggleInspector() {
  inspectorCollapsed.value = !inspectorCollapsed.value;
  localStorage.setItem(INSPECTOR_C_KEY, inspectorCollapsed.value ? '1' : '');
}

function startInspectorResize(event) {
  var startX = event.clientX;
  var startW = inspectorWidth.value;
  document.body.style.cursor = 'col-resize';
  document.body.style.userSelect = 'none';
  blockIframes();
  function onMove(e) {
    var w = startW - (e.clientX - startX);
    if (w < 100) {
      inspectorCollapsed.value = true;
      localStorage.setItem(INSPECTOR_C_KEY, '1');
    } else {
      inspectorCollapsed.value = false;
      localStorage.setItem(INSPECTOR_C_KEY, '');
      inspectorWidth.value = Math.min(600, Math.max(INSPECTOR_MIN_W, w));
    }
  }
  function onUp() {
    document.body.style.cursor = '';
    document.body.style.userSelect = '';
    unblockIframes();
    localStorage.setItem(INSPECTOR_W_KEY, String(inspectorWidth.value));
    document.removeEventListener('mousemove', onMove);
    document.removeEventListener('mouseup', onUp);
  }
  document.addEventListener('mousemove', onMove);
  document.addEventListener('mouseup', onUp);
}

// Auto-expand inspector when tile selected / page settings opened
watch([
  function() { return builderStore.selectedTileId; },
  function() { return builderStore.pageSettingsOpen; }
], function() {
  if (inspectorCollapsed.value && (builderStore.selectedTileId || builderStore.pageSettingsOpen)) {
    inspectorCollapsed.value = false;
    localStorage.setItem(INSPECTOR_C_KEY, '');
  }
});

// Responsive: auto-collapse sidebar e inspector su viewport <=1024px
const TABLET_BREAKPOINT = 1024;
function handleResize() {
  if (window.innerWidth <= TABLET_BREAKPOINT) {
    if (!sidebarCollapsed.value) {
      sidebarCollapsed.value = true;
      localStorage.setItem(SIDEBAR_C_KEY, '1');
    }
    if (!inspectorCollapsed.value) {
      inspectorCollapsed.value = true;
      localStorage.setItem(INSPECTOR_C_KEY, '1');
    }
  }
}

function onSaveSection(e) {
  const section = e.detail?.section;
  if (section) templateLibraryRef.value?.openSaveDialog(section);
}

function onLoadTemplate() {
  templateLibraryRef.value?.open();
}

onMounted(async () => {
  tilesStore.fetchRegisteredTiles();
  initHistory();
  document.addEventListener('keydown', handleKeyboard);
  document.addEventListener('olo:save-section', onSaveSection);
  document.addEventListener('olo:load-template', onLoadTemplate);
  window.addEventListener('beforeunload', onBeforeUnload);
  window.addEventListener('resize', handleResize);
  window.addEventListener('olo:open-finder-after', onOpenFinderAfter);
  handleResize(); // check iniziale

  // Auto-open builder if templateId is passed from WordPress
  const oloData = window.oloData || {};
  const templateId = parseInt(oloData.templateId) || 0;
  if (templateId > 0) {
    await openBuilder(templateId);
  }
});

onUnmounted(() => {
  document.removeEventListener('keydown', handleKeyboard);
  document.removeEventListener('olo:save-section', onSaveSection);
  document.removeEventListener('olo:load-template', onLoadTemplate);
  window.removeEventListener('beforeunload', onBeforeUnload);
  window.removeEventListener('resize', handleResize);
  window.removeEventListener('olo:open-finder-after', onOpenFinderAfter);
});

async function openBuilder(templateId) {
  // If we're in the admin shell (list mode), navigate to fullscreen editor URL
  const oloData = window.oloData || {};
  if (!parseInt(oloData.templateId)) {
    window.location.href = `admin.php?page=olobuilder-templates&template_id=${templateId}`;
    return;
  }

  const success = await builderStore.loadTemplate(templateId);
  if (!success || !builderStore.currentTemplate) {
    console.error('[Olobuild] Impossibile caricare il template', templateId, '— ricarico la pagina.');
    // Retry: reload the whole page once (guard against infinite loop)
    const reloadKey = 'olo_reload_' + templateId;
    if (!sessionStorage.getItem(reloadKey)) {
      sessionStorage.setItem(reloadKey, '1');
      window.location.reload();
      return;
    }
    sessionStorage.removeItem(reloadKey);
    toast.error(t('Errore di caricamento del template. Riprova tra qualche secondo.'));
    return;
  }
  sessionStorage.removeItem('olo_reload_' + templateId);
  // Load template tiles into canvas
  if (builderStore.currentTemplate.content) {
    tilesStore.setCanvasTiles(builderStore.currentTemplate.content);
  }
  // Sincronizza widget globali: aggiorna istanze locali dal master DB
  await tilesStore.fetchGlobalWidgets();
  tilesStore.syncGlobalWidgetsOnLoad();

  // Load header + footer for unified editing (page/single templates only)
  await builderStore.loadUnifiedContext();

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
      // If in admin shell (list mode), redirect to fullscreen editor
      if (!parseInt(oloData.templateId) && tpl.id) {
        window.location.href = `admin.php?page=olobuilder-templates&template_id=${tpl.id}`;
        return;
      }
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
  if (builderStore.isDirty || builderStore.headerDirty || builderStore.footerDirty) {
    if (!confirm('Hai modifiche non salvate. Uscire comunque?')) return;
  }
  // If in fullscreen editor mode (template_id in URL), redirect to list page
  const oloData = window.oloData || {};
  if (parseInt(oloData.templateId)) {
    window.location.href = 'admin.php?page=olobuilder-templates';
    return;
  }
  builderStore.deselectTile();
  builderStore.previewMode = false;
  currentView.value = 'list';
}
</script>
