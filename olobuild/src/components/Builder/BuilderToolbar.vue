<template>
  <div class="mb-flex mb-items-center mb-justify-between mb-h-12 mb-px-4 mb-bg-gray-800 mb-border-b mb-border-gray-700 mb-shrink-0">
    <!-- Left: Navigation + Logo -->
    <div class="mb-flex mb-items-center mb-gap-2">
      <a
        :href="wpAdminUrl"
        class="mb-px-2 mb-py-1 mb-text-gray-500 hover:mb-text-gray-200 mb-text-xs mb-transition-colors mb-no-underline"
        title="Torna a WordPress"
      >
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
      </a>
      <span class="mb-text-gray-600">|</span>
      <button
        @click="$emit('back')"
        class="mb-text-gray-400 hover:mb-text-gray-200 mb-text-xs mb-transition-colors"
        title="Torna ai template"
      >
        Template
      </button>
      <span class="mb-text-gray-600">/</span>
      <!-- Editable template title -->
      <input
        v-if="editingTitle"
        ref="titleInputRef"
        v-model="titleDraft"
        @blur="confirmTitle"
        @keydown.enter="confirmTitle"
        @keydown.escape="cancelTitle"
        class="mb-bg-white mb-border mb-border-primary-500 mb-rounded mb-px-2 mb-py-0.5 mb-text-sm mb-text-gray-900 mb-font-bold mb-outline-none mb-w-48"
      />
      <span
        v-else
        @click="startEditTitle"
        class="mb-text-primary-400 mb-font-bold mb-text-sm mb-cursor-pointer hover:mb-underline"
        title="Clicca per rinominare"
      >{{ templateTitle }}</span>
      <span
        v-if="templateType === 'header'"
        class="mb-ml-2 mb-px-2 mb-py-0.5 mb-text-[10px] mb-font-bold mb-uppercase mb-rounded mb-bg-purple-600/20 mb-text-purple-300 mb-border mb-border-purple-500/30"
      >Header</span>
      <span
        v-if="templateType === 'footer'"
        class="mb-ml-2 mb-px-2 mb-py-0.5 mb-text-[10px] mb-font-bold mb-uppercase mb-rounded mb-bg-teal-600/20 mb-text-teal-300 mb-border mb-border-teal-500/30"
      >Footer</span>
      <span
        v-if="templateType === 'single'"
        class="mb-ml-2 mb-px-2 mb-py-0.5 mb-text-[10px] mb-font-bold mb-uppercase mb-rounded mb-bg-amber-600/20 mb-text-amber-300 mb-border mb-border-amber-500/30"
      >Single: {{ singlePostType }}</span>
    </div>

    <!-- Center: Page Settings + Viewport controls -->
    <div class="mb-flex mb-items-center mb-gap-2">
      <button
        @click="builderStore.toggleStylePanel()"
        :class="[
          'mb-px-2 mb-py-1.5 mb-rounded-md mb-transition-colors',
          builderStore.stylePanelOpen
            ? 'mb-bg-primary-600/20 mb-text-primary-300'
            : 'mb-text-gray-400 hover:mb-text-gray-200 hover:mb-bg-gray-700'
        ]"
        title="Sistema stili"
      >
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="4" x2="4" y1="21" y2="14"/><line x1="4" x2="4" y1="10" y2="3"/><line x1="12" x2="12" y1="21" y2="12"/><line x1="12" x2="12" y1="8" y2="3"/><line x1="20" x2="20" y1="21" y2="16"/><line x1="20" x2="20" y1="12" y2="3"/><line x1="1" x2="7" y1="14" y2="14"/><line x1="9" x2="15" y1="8" y2="8"/><line x1="17" x2="23" y1="16" y2="16"/></svg>
      </button>
      <button
        @click="builderStore.togglePageSettings()"
        :class="[
          'mb-px-2 mb-py-1.5 mb-rounded-md mb-transition-colors',
          builderStore.pageSettingsOpen
            ? 'mb-bg-primary-600/20 mb-text-primary-300'
            : 'mb-text-gray-400 hover:mb-text-gray-200 hover:mb-bg-gray-700'
        ]"
        title="Impostazioni pagina"
      >
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg>
      </button>
    <div class="mb-flex mb-items-center mb-gap-1 mb-bg-gray-700 mb-rounded-lg mb-p-0.5">
      <!-- Desktop -->
      <button
        @click="builderStore.setViewMode('desktop')"
        :class="[
          'mb-px-2.5 mb-py-1 mb-rounded-md mb-transition-colors',
          builderStore.viewMode === 'desktop'
            ? 'mb-bg-primary-600 mb-text-white'
            : 'mb-text-gray-400 hover:mb-text-gray-200'
        ]"
        title="Desktop"
      >
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="3" rx="2"/><line x1="8" x2="16" y1="21" y2="21"/><line x1="12" x2="12" y1="17" y2="21"/></svg>
      </button>
      <!-- Tablet -->
      <button
        @click="builderStore.setViewMode('tablet')"
        :class="[
          'mb-px-2.5 mb-py-1 mb-rounded-md mb-transition-colors',
          builderStore.viewMode === 'tablet'
            ? 'mb-bg-primary-600 mb-text-white'
            : 'mb-text-gray-400 hover:mb-text-gray-200'
        ]"
        title="Tablet"
      >
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="16" height="20" x="4" y="2" rx="2"/><line x1="12" x2="12.01" y1="18" y2="18"/></svg>
      </button>
      <!-- Mobile -->
      <button
        @click="builderStore.setViewMode('mobile')"
        :class="[
          'mb-px-2.5 mb-py-1 mb-rounded-md mb-transition-colors',
          builderStore.viewMode === 'mobile'
            ? 'mb-bg-primary-600 mb-text-white'
            : 'mb-text-gray-400 hover:mb-text-gray-200'
        ]"
        title="Mobile"
      >
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="14" height="20" x="5" y="2" rx="2"/><line x1="12" x2="12.01" y1="18" y2="18"/></svg>
      </button>
    </div>
    </div>

    <!-- Right: Actions -->
    <div class="mb-flex mb-items-center mb-gap-2">
      <button
        @click="history.undo()"
        :disabled="!history.canUndo()"
        :class="[
          'mb-px-2 mb-py-1.5 mb-rounded-md mb-transition-colors',
          history.canUndo()
            ? 'mb-text-gray-300 hover:mb-text-white hover:mb-bg-gray-700'
            : 'mb-text-gray-600 mb-cursor-not-allowed'
        ]"
        title="Annulla (Ctrl+Z)"
      >
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7v6h6"/><path d="M21 17a9 9 0 0 0-9-9 9 9 0 0 0-6 2.3L3 13"/></svg>
      </button>
      <button
        @click="history.redo()"
        :disabled="!history.canRedo()"
        :class="[
          'mb-px-2 mb-py-1.5 mb-rounded-md mb-transition-colors',
          history.canRedo()
            ? 'mb-text-gray-300 hover:mb-text-white hover:mb-bg-gray-700'
            : 'mb-text-gray-600 mb-cursor-not-allowed'
        ]"
        title="Ripeti (Ctrl+Shift+Z)"
      >
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 7v6h-6"/><path d="M3 17a9 9 0 0 1 9-9 9 9 0 0 1 6 2.3L21 13"/></svg>
      </button>
      <button
        @click="togglePreview"
        :class="[
          'mb-px-3 mb-py-1.5 mb-text-xs mb-text-gray-300 mb-rounded-md mb-border mb-transition-colors',
          builderStore.previewMode
            ? 'mb-border-primary-500 mb-bg-primary-600/20 mb-text-primary-300'
            : 'mb-border-gray-600 hover:mb-bg-gray-700'
        ]"
        title="Anteprima"
      >
        {{ builderStore.previewMode ? 'Modifica' : 'Anteprima' }}
      </button>
      <button
        @click="builderStore.saveTemplate()"
        :disabled="builderStore.isSaving || !builderStore.isDirty"
        :class="[
          'mb-px-4 mb-py-1.5 mb-text-xs mb-font-medium mb-rounded-md mb-transition-colors',
          builderStore.isDirty
            ? 'mb-bg-primary-600 mb-text-white hover:mb-bg-primary-700'
            : 'mb-bg-gray-700 mb-text-gray-500 mb-cursor-not-allowed'
        ]"
      >
        {{ builderStore.isSaving ? 'Salvataggio...' : 'Salva' }}
      </button>
      <button
        @click="builderStore.togglePublish()"
        :disabled="builderStore.isSaving"
        :class="[
          'mb-px-4 mb-py-1.5 mb-text-xs mb-font-medium mb-rounded-md mb-border mb-transition-colors',
          isPublished
            ? 'mb-border-green-500 mb-text-green-400 hover:mb-bg-green-600/20'
            : 'mb-border-yellow-500 mb-text-yellow-400 hover:mb-bg-yellow-600/20'
        ]"
      >
        {{ isPublished ? 'Pubblicato' : 'Pubblica' }}
      </button>
      <!-- Activate header button -->
      <button
        v-if="templateType === 'header' && isPublished"
        @click="toggleActivateHeader"
        :class="[
          'mb-px-4 mb-py-1.5 mb-text-xs mb-font-medium mb-rounded-md mb-border mb-transition-colors',
          isActiveHeader
            ? 'mb-border-purple-500 mb-text-purple-300 mb-bg-purple-600/20 hover:mb-bg-red-600/20 hover:mb-text-red-300 hover:mb-border-red-500/30'
            : 'mb-border-purple-500/50 mb-text-purple-400 hover:mb-bg-purple-600/20'
        ]"
      >
        {{ isActiveHeader ? 'Disattiva' : 'Attiva' }}
      </button>
      <!-- Activate footer button -->
      <button
        v-if="templateType === 'footer' && isPublished"
        @click="toggleActivateFooter"
        :class="[
          'mb-px-4 mb-py-1.5 mb-text-xs mb-font-medium mb-rounded-md mb-border mb-transition-colors',
          isActiveFooter
            ? 'mb-border-teal-500 mb-text-teal-300 mb-bg-teal-600/20 hover:mb-bg-red-600/20 hover:mb-text-red-300 hover:mb-border-red-500/30'
            : 'mb-border-teal-500/50 mb-text-teal-400 hover:mb-bg-teal-600/20'
        ]"
      >
        {{ isActiveFooter ? 'Disattiva' : 'Attiva' }}
      </button>
      <!-- Activate single button -->
      <button
        v-if="templateType === 'single' && isPublished && singlePostType"
        @click="toggleActivateSingle"
        :class="[
          'mb-px-4 mb-py-1.5 mb-text-xs mb-font-medium mb-rounded-md mb-border mb-transition-colors',
          isActiveSingle
            ? 'mb-border-amber-500 mb-text-amber-300 mb-bg-amber-600/20 hover:mb-bg-red-600/20 hover:mb-text-red-300 hover:mb-border-red-500/30'
            : 'mb-border-amber-500/50 mb-text-amber-400 hover:mb-bg-amber-600/20'
        ]"
      >
        {{ isActiveSingle ? 'Disattiva' : 'Attiva' }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, nextTick } from 'vue';
import { useBuilderStore } from '@/stores/builder';
import { useHistory } from '@/composables/useHistory';

defineEmits(['back']);

const builderStore = useBuilderStore();
const history = useHistory();

// Editable title
const editingTitle = ref(false);
const titleDraft = ref('');
const titleInputRef = ref(null);

const templateTitle = computed(() => builderStore.currentTemplate?.title || 'Senza titolo');

function startEditTitle() {
  titleDraft.value = builderStore.currentTemplate?.title || '';
  editingTitle.value = true;
  nextTick(() => {
    titleInputRef.value?.focus();
    titleInputRef.value?.select();
  });
}

function confirmTitle() {
  if (!editingTitle.value) return;
  editingTitle.value = false;
  const newTitle = titleDraft.value.trim();
  if (newTitle && builderStore.currentTemplate && newTitle !== builderStore.currentTemplate.title) {
    builderStore.currentTemplate.title = newTitle;
    builderStore.isDirty = true;
  }
}

function cancelTitle() {
  editingTitle.value = false;
}

const isPublished = computed(() => builderStore.currentTemplate?.status === 'published');
const templateType = computed(() => builderStore.currentTemplate?.type || 'page');
const oloData = window.oloData || {};
const wpAdminUrl = (oloData.restUrl || '').replace('/wp-json/olo/v1', '/wp-admin/');

const activeHeaderId = ref(parseInt(oloData.activeHeaderId) || 0);
const isActiveHeader = computed(() => {
  const tplId = builderStore.currentTemplate?.id;
  return tplId && tplId === activeHeaderId.value;
});

const activeFooterId = ref(parseInt(oloData.activeFooterId) || 0);
const isActiveFooter = computed(() => {
  const tplId = builderStore.currentTemplate?.id;
  return tplId && tplId === activeFooterId.value;
});

const singlePostType = computed(() => builderStore.currentTemplate?.settings?.single_post_type || '');
const activeSingles = ref({ ...(oloData.activeSingles || {}) });
const isActiveSingle = computed(() => {
  const tplId = builderStore.currentTemplate?.id;
  const pt = singlePostType.value;
  return pt && activeSingles.value[pt] === tplId;
});

async function toggleActivateHeader() {
  const tplId = builderStore.currentTemplate?.id;
  if (!tplId) return;

  try {
    if (isActiveHeader.value) {
      const res = await fetch(`${oloData.restUrl}/header/activate`, {
        method: 'DELETE',
        headers: { 'X-WP-Nonce': oloData.nonce },
      });
      if (res.ok) activeHeaderId.value = 0;
    } else {
      const res = await fetch(`${oloData.restUrl}/header/activate`, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          'X-WP-Nonce': oloData.nonce,
        },
        body: JSON.stringify({ id: tplId }),
      });
      if (res.ok) activeHeaderId.value = tplId;
    }
  } catch (err) {
    console.error('Toggle header activation error:', err);
  }
}

async function toggleActivateFooter() {
  const tplId = builderStore.currentTemplate?.id;
  if (!tplId) return;

  try {
    if (isActiveFooter.value) {
      const res = await fetch(`${oloData.restUrl}/footer/activate`, {
        method: 'DELETE',
        headers: { 'X-WP-Nonce': oloData.nonce },
      });
      if (res.ok) activeFooterId.value = 0;
    } else {
      const res = await fetch(`${oloData.restUrl}/footer/activate`, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          'X-WP-Nonce': oloData.nonce,
        },
        body: JSON.stringify({ id: tplId }),
      });
      if (res.ok) activeFooterId.value = tplId;
    }
  } catch (err) {
    console.error('Toggle footer activation error:', err);
  }
}

async function toggleActivateSingle() {
  const tplId = builderStore.currentTemplate?.id;
  const pt = singlePostType.value;
  if (!tplId || !pt) return;

  try {
    if (isActiveSingle.value) {
      const res = await fetch(`${oloData.restUrl}/single/activate`, {
        method: 'DELETE',
        headers: {
          'Content-Type': 'application/json',
          'X-WP-Nonce': oloData.nonce,
        },
        body: JSON.stringify({ post_type: pt }),
      });
      if (res.ok) {
        const updated = { ...activeSingles.value };
        delete updated[pt];
        activeSingles.value = updated;
      }
    } else {
      const res = await fetch(`${oloData.restUrl}/single/activate`, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          'X-WP-Nonce': oloData.nonce,
        },
        body: JSON.stringify({ id: tplId, post_type: pt }),
      });
      if (res.ok) {
        activeSingles.value = { ...activeSingles.value, [pt]: tplId };
      }
    }
  } catch (err) {
    console.error('Toggle single activation error:', err);
  }
}

function togglePreview() {
  builderStore.previewMode = !builderStore.previewMode;
}
</script>
