<template>
  <div class="mb-flex mb-items-center mb-justify-between mb-h-12 mb-px-4 mb-bg-gray-800 mb-border-b mb-border-gray-700 mb-shrink-0">
    <!-- Left: Navigation + Logo -->
    <div class="mb-flex mb-items-center mb-gap-2">
      <a
        :href="wpAdminUrl"
        class="mb-px-2 mb-py-1 mb-text-gray-500 hover:mb-text-gray-200 mb-text-xs mb-transition-colors mb-no-underline"
        title="Torna a WordPress"
      >
        WP
      </a>
      <span class="mb-text-gray-600">/</span>
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
          'mb-px-2 mb-py-1.5 mb-text-sm mb-rounded-md mb-transition-colors',
          builderStore.stylePanelOpen
            ? 'mb-bg-primary-600/20 mb-text-primary-300'
            : 'mb-text-gray-400 hover:mb-text-gray-200 hover:mb-bg-gray-700'
        ]"
        title="Sistema stili"
      >&#127912;</button>
      <button
        @click="builderStore.togglePageSettings()"
        :class="[
          'mb-px-2 mb-py-1.5 mb-text-sm mb-rounded-md mb-transition-colors',
          builderStore.pageSettingsOpen
            ? 'mb-bg-primary-600/20 mb-text-primary-300'
            : 'mb-text-gray-400 hover:mb-text-gray-200 hover:mb-bg-gray-700'
        ]"
        title="Impostazioni pagina"
      >&#9881;</button>
    <div class="mb-flex mb-items-center mb-gap-1 mb-bg-gray-700 mb-rounded-lg mb-p-0.5">
      <button
        v-for="mode in viewModes"
        :key="mode.value"
        @click="builderStore.setViewMode(mode.value)"
        :class="[
          'mb-px-3 mb-py-1 mb-rounded-md mb-text-xs mb-font-medium mb-transition-colors',
          builderStore.viewMode === mode.value
            ? 'mb-bg-primary-600 mb-text-white'
            : 'mb-text-gray-400 hover:mb-text-gray-200'
        ]"
        :title="mode.label"
      >
        {{ mode.icon }}
      </button>
    </div>
    </div>

    <!-- Right: Actions -->
    <div class="mb-flex mb-items-center mb-gap-2">
      <button
        @click="history.undo()"
        :disabled="!history.canUndo()"
        :class="[
          'mb-px-3 mb-py-1.5 mb-text-xs mb-rounded-md mb-transition-colors',
          history.canUndo()
            ? 'mb-text-gray-300 hover:mb-bg-gray-700'
            : 'mb-text-gray-500 mb-cursor-not-allowed'
        ]"
        title="Annulla (Ctrl+Z)"
      >
        Annulla
      </button>
      <button
        @click="history.redo()"
        :disabled="!history.canRedo()"
        :class="[
          'mb-px-3 mb-py-1.5 mb-text-xs mb-rounded-md mb-transition-colors',
          history.canRedo()
            ? 'mb-text-gray-300 hover:mb-bg-gray-700'
            : 'mb-text-gray-500 mb-cursor-not-allowed'
        ]"
        title="Ripeti (Ctrl+Shift+Z)"
      >
        Ripeti
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

const viewModes = [
  { value: 'desktop', label: 'Desktop', icon: '\u{1F5B5}' },
  { value: 'tablet', label: 'Tablet', icon: '\u{1F4F1}' },
  { value: 'mobile', label: 'Mobile', icon: '\u{1F4F2}' },
];

function togglePreview() {
  builderStore.previewMode = !builderStore.previewMode;
}
</script>
