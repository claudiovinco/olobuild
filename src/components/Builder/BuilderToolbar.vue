<template>
  <div class="mb-flex mb-items-center mb-justify-between mb-h-12 mb-px-4 mb-bg-gray-800 mb-border-b mb-border-gray-700 mb-shrink-0" role="toolbar" :aria-label="t('Barra strumenti builder')">
    <!-- Left: Navigation + Logo -->
    <div class="mb-flex mb-items-center mb-gap-2">
      <a
        :href="wpAdminUrl"
        class="mb-px-2 mb-py-1 mb-text-gray-500 hover:mb-text-gray-200 mb-text-xs mb-transition-colors mb-no-underline"
        :title="t('Torna a WordPress')"
      >
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
      </a>
      <span class="mb-text-gray-600">|</span>
      <button
        @click="$emit('back')"
        class="mb-text-gray-400 hover:mb-text-gray-200 mb-text-xs mb-transition-colors"
        :title="t('Torna ai template')"
      >
        {{ t('Template') }}
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
        :title="t('Clicca per rinominare')"
      >{{ templateTitle }}</span>
      <span
        v-if="templateType === 'header'"
        class="mb-ml-2 mb-px-2 mb-py-0.5 mb-text-[10px] mb-font-bold mb-uppercase mb-rounded mb-bg-purple-600/20 mb-text-purple-300 mb-border mb-border-purple-500/30"
      >{{ t('Header') }}</span>
      <span
        v-if="templateType === 'footer'"
        class="mb-ml-2 mb-px-2 mb-py-0.5 mb-text-[10px] mb-font-bold mb-uppercase mb-rounded mb-bg-teal-600/20 mb-text-teal-300 mb-border mb-border-teal-500/30"
      >{{ t('Footer') }}</span>
      <span
        v-if="templateType === 'single'"
        class="mb-ml-2 mb-px-2 mb-py-0.5 mb-text-[10px] mb-font-bold mb-uppercase mb-rounded mb-bg-amber-600/20 mb-text-amber-300 mb-border mb-border-amber-500/30"
      >Single: {{ singlePostType }}</span>
    </div>

    <!-- Center: Page Settings + Viewport controls -->
    <div class="mb-flex mb-items-center mb-gap-2">
      <button
        @click="builderStore.togglePageSettings()"
        :class="[
          'mb-px-2 mb-py-1.5 mb-rounded-md mb-transition-colors',
          builderStore.pageSettingsOpen
            ? 'mb-bg-primary-600/20 mb-text-primary-300'
            : 'mb-text-gray-400 hover:mb-text-gray-200 hover:mb-bg-gray-700'
        ]"
        :title="t('Impostazioni pagina')"
        :aria-label="t('Impostazioni pagina')"
      >
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg>
      </button>
    <div class="mb-flex mb-items-center mb-gap-1 mb-bg-gray-700 mb-rounded-lg mb-p-0.5">
      <button
        v-for="vp in visibleViewports"
        :key="vp.key"
        @click="builderStore.setViewMode(vp.key)"
        :class="[
          'mb-px-2.5 mb-py-1 mb-rounded-md mb-transition-colors',
          builderStore.viewMode === vp.key
            ? 'mb-bg-primary-600 mb-text-white'
            : 'mb-text-gray-400 hover:mb-text-gray-200'
        ]"
        :title="vp.title"
        :aria-label="'Vista ' + vp.label"
        v-html="vp.svg"
      ></button>
    </div>

      <!-- Zoom controls -->
      <div class="mb-flex mb-items-center mb-gap-0.5 mb-ml-2">
        <button
          @click="builderStore.zoomOut()"
          class="mb-px-1.5 mb-py-1 mb-rounded mb-text-gray-400 hover:mb-text-gray-200 hover:mb-bg-gray-700 mb-transition-colors"
          :title="t('Riduci zoom')"
          :disabled="builderStore.canvasZoom <= 25"
        >
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/><line x1="8" y1="11" x2="14" y2="11"/></svg>
        </button>
        <button
          @click="showZoomMenu = !showZoomMenu"
          class="mb-px-1.5 mb-py-0.5 mb-rounded mb-text-[11px] mb-font-bold mb-tabular-nums mb-min-w-[40px] mb-text-center mb-transition-colors"
          :class="builderStore.canvasZoom !== 100 ? 'mb-text-primary-300 mb-bg-primary-600/20' : 'mb-text-gray-400 hover:mb-text-gray-200 hover:mb-bg-gray-700'"
          :title="t('Zoom canvas')"
        >{{ builderStore.canvasZoom }}%</button>
        <button
          @click="builderStore.zoomIn()"
          class="mb-px-1.5 mb-py-1 mb-rounded mb-text-gray-400 hover:mb-text-gray-200 hover:mb-bg-gray-700 mb-transition-colors"
          :title="t('Aumenta zoom')"
          :disabled="builderStore.canvasZoom >= 200"
        >
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/><line x1="11" y1="8" x2="11" y2="14"/><line x1="8" y1="11" x2="14" y2="11"/></svg>
        </button>
      </div>
      <!-- Zoom dropdown -->
      <Teleport to="body">
        <div v-if="showZoomMenu" class="mb-fixed mb-inset-0 mb-z-[9999]" @click="showZoomMenu = false">
          <div class="mb-absolute mb-bg-gray-800 mb-border mb-border-gray-600 mb-rounded-lg mb-shadow-xl mb-py-1 mb-w-24" :style="zoomMenuStyle" @click.stop>
            <button v-for="z in [25, 50, 75, 100, 125, 150, 175, 200]" :key="z"
              @click="builderStore.setZoom(z); showZoomMenu = false"
              :class="['mb-w-full mb-text-left mb-px-3 mb-py-1 mb-text-xs mb-transition-colors', builderStore.canvasZoom === z ? 'mb-text-primary-300 mb-font-bold' : 'mb-text-gray-300 hover:mb-bg-gray-700']"
            >{{ z }}%</button>
          </div>
        </div>
      </Teleport>
    </div>

    <!-- Right: Actions -->
    <div class="mb-flex mb-items-center mb-gap-2">
      <!-- Keyboard shortcuts help -->
      <button
        @click="showShortcuts = !showShortcuts"
        class="mb-px-2 mb-py-1.5 mb-rounded-md mb-transition-colors mb-text-gray-400 hover:mb-text-gray-200 hover:mb-bg-gray-700 mb-text-xs mb-font-bold"
        :title="t('Scorciatoie tastiera')"
        :aria-label="t('Scorciatoie tastiera')"
      >
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M6 8h.001M10 8h.001M14 8h.001M18 8h.001M8 12h.001M12 12h.001M16 12h.001M7 16h10"/></svg>
      </button>
      <!-- Controllo accessibilità (contrasto) -->
      <button
        @click="openA11y()"
        class="mb-px-2 mb-py-1.5 mb-rounded-md mb-transition-colors mb-text-gray-400 hover:mb-text-gray-200 hover:mb-bg-gray-700 mb-text-xs mb-font-bold"
        :title="t('Controllo accessibilità (contrasto colore)')"
        :aria-label="t('Controllo accessibilità')"
      >
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="7.6" r="1.1" fill="currentColor" stroke="none"/><path d="M8.5 10h7M12 10v4.5M12 14.5 9.8 18M12 14.5 14.2 18"/></svg>
      </button>
      <Teleport to="body">
      <div
        v-if="showShortcuts"
        class="mb-fixed mb-inset-0 mb-z-[99999] mb-flex mb-items-center mb-justify-center"
        @click.self="showShortcuts = false"
        style="background:rgba(0,0,0,0.6)"
      >
        <div ref="shortcutsModalRef" class="mb-bg-gray-800 mb-border mb-border-gray-600 mb-rounded-xl mb-p-6 mb-shadow-2xl mb-w-[380px]" @click.stop>
          <div class="mb-flex mb-items-center mb-justify-between mb-mb-4">
            <h3 class="mb-text-white mb-text-base mb-font-semibold mb-m-0">{{ t('Scorciatoie tastiera') }}</h3>
            <button
              @click="showShortcuts = false"
              class="mb-text-gray-400 hover:mb-text-white mb-transition-colors"
              :aria-label="t('Chiudi')"
            >
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
          </div>
          <div class="mb-space-y-3">
            <div class="mb-flex mb-items-center mb-justify-between">
              <span class="mb-text-gray-300 mb-text-sm">{{ t('Annulla') }}</span>
              <div class="mb-flex mb-gap-1">
                <kbd class="mb-bg-gray-700 mb-text-gray-300 mb-px-2 mb-py-0.5 mb-rounded mb-text-xs mb-font-mono mb-border mb-border-gray-600">Ctrl</kbd>
                <span class="mb-text-gray-500 mb-text-xs">+</span>
                <kbd class="mb-bg-gray-700 mb-text-gray-300 mb-px-2 mb-py-0.5 mb-rounded mb-text-xs mb-font-mono mb-border mb-border-gray-600">Z</kbd>
              </div>
            </div>
            <div class="mb-flex mb-items-center mb-justify-between">
              <span class="mb-text-gray-300 mb-text-sm">{{ t('Ripeti') }}</span>
              <div class="mb-flex mb-gap-1">
                <kbd class="mb-bg-gray-700 mb-text-gray-300 mb-px-2 mb-py-0.5 mb-rounded mb-text-xs mb-font-mono mb-border mb-border-gray-600">Ctrl</kbd>
                <span class="mb-text-gray-500 mb-text-xs">+</span>
                <kbd class="mb-bg-gray-700 mb-text-gray-300 mb-px-2 mb-py-0.5 mb-rounded mb-text-xs mb-font-mono mb-border mb-border-gray-600">Shift</kbd>
                <span class="mb-text-gray-500 mb-text-xs">+</span>
                <kbd class="mb-bg-gray-700 mb-text-gray-300 mb-px-2 mb-py-0.5 mb-rounded mb-text-xs mb-font-mono mb-border mb-border-gray-600">Z</kbd>
              </div>
            </div>
            <div class="mb-flex mb-items-center mb-justify-between">
              <span class="mb-text-gray-300 mb-text-sm">{{ t('Salva') }}</span>
              <div class="mb-flex mb-gap-1">
                <kbd class="mb-bg-gray-700 mb-text-gray-300 mb-px-2 mb-py-0.5 mb-rounded mb-text-xs mb-font-mono mb-border mb-border-gray-600">Ctrl</kbd>
                <span class="mb-text-gray-500 mb-text-xs">+</span>
                <kbd class="mb-bg-gray-700 mb-text-gray-300 mb-px-2 mb-py-0.5 mb-rounded mb-text-xs mb-font-mono mb-border mb-border-gray-600">S</kbd>
              </div>
            </div>
            <div class="mb-flex mb-items-center mb-justify-between">
              <span class="mb-text-gray-300 mb-text-sm">{{ t('Elimina tile selezionato') }}</span>
              <div class="mb-flex mb-gap-1">
                <kbd class="mb-bg-gray-700 mb-text-gray-300 mb-px-2 mb-py-0.5 mb-rounded mb-text-xs mb-font-mono mb-border mb-border-gray-600">Canc</kbd>
                <span class="mb-text-gray-500 mb-text-xs">/</span>
                <kbd class="mb-bg-gray-700 mb-text-gray-300 mb-px-2 mb-py-0.5 mb-rounded mb-text-xs mb-font-mono mb-border mb-border-gray-600">Backspace</kbd>
              </div>
            </div>
            <div class="mb-flex mb-items-center mb-justify-between">
              <span class="mb-text-gray-300 mb-text-sm">{{ t('Copia tile') }}</span>
              <div class="mb-flex mb-gap-1">
                <kbd class="mb-bg-gray-700 mb-text-gray-300 mb-px-2 mb-py-0.5 mb-rounded mb-text-xs mb-font-mono mb-border mb-border-gray-600">Ctrl</kbd>
                <span class="mb-text-gray-500 mb-text-xs">+</span>
                <kbd class="mb-bg-gray-700 mb-text-gray-300 mb-px-2 mb-py-0.5 mb-rounded mb-text-xs mb-font-mono mb-border mb-border-gray-600">C</kbd>
              </div>
            </div>
            <div class="mb-flex mb-items-center mb-justify-between">
              <span class="mb-text-gray-300 mb-text-sm">{{ t('Incolla tile') }}</span>
              <div class="mb-flex mb-gap-1">
                <kbd class="mb-bg-gray-700 mb-text-gray-300 mb-px-2 mb-py-0.5 mb-rounded mb-text-xs mb-font-mono mb-border mb-border-gray-600">Ctrl</kbd>
                <span class="mb-text-gray-500 mb-text-xs">+</span>
                <kbd class="mb-bg-gray-700 mb-text-gray-300 mb-px-2 mb-py-0.5 mb-rounded mb-text-xs mb-font-mono mb-border mb-border-gray-600">V</kbd>
              </div>
            </div>
            <div class="mb-flex mb-items-center mb-justify-between">
              <span class="mb-text-gray-300 mb-text-sm">{{ t('Duplica tile') }}</span>
              <div class="mb-flex mb-gap-1">
                <kbd class="mb-bg-gray-700 mb-text-gray-300 mb-px-2 mb-py-0.5 mb-rounded mb-text-xs mb-font-mono mb-border mb-border-gray-600">Ctrl</kbd>
                <span class="mb-text-gray-500 mb-text-xs">+</span>
                <kbd class="mb-bg-gray-700 mb-text-gray-300 mb-px-2 mb-py-0.5 mb-rounded mb-text-xs mb-font-mono mb-border mb-border-gray-600">D</kbd>
              </div>
            </div>
            <div class="mb-flex mb-items-center mb-justify-between">
              <span class="mb-text-gray-300 mb-text-sm">{{ t('Selezione multipla') }}</span>
              <div class="mb-flex mb-gap-1">
                <kbd class="mb-bg-gray-700 mb-text-gray-300 mb-px-2 mb-py-0.5 mb-rounded mb-text-xs mb-font-mono mb-border mb-border-gray-600">Ctrl</kbd>
                <span class="mb-text-gray-500 mb-text-xs">+</span>
                <kbd class="mb-bg-gray-700 mb-text-gray-300 mb-px-2 mb-py-0.5 mb-rounded mb-text-xs mb-font-mono mb-border mb-border-gray-600">Click</kbd>
              </div>
            </div>
            <div class="mb-flex mb-items-center mb-justify-between">
              <span class="mb-text-gray-300 mb-text-sm">{{ t('Sposta su / giù') }}</span>
              <div class="mb-flex mb-gap-1">
                <kbd class="mb-bg-gray-700 mb-text-gray-300 mb-px-2 mb-py-0.5 mb-rounded mb-text-xs mb-font-mono mb-border mb-border-gray-600">Alt</kbd>
                <span class="mb-text-gray-500 mb-text-xs">+</span>
                <kbd class="mb-bg-gray-700 mb-text-gray-300 mb-px-2 mb-py-0.5 mb-rounded mb-text-xs mb-font-mono mb-border mb-border-gray-600">↑</kbd>
                <span class="mb-text-gray-500 mb-text-xs">/</span>
                <kbd class="mb-bg-gray-700 mb-text-gray-300 mb-px-2 mb-py-0.5 mb-rounded mb-text-xs mb-font-mono mb-border mb-border-gray-600">↓</kbd>
              </div>
            </div>
            <div class="mb-flex mb-items-center mb-justify-between">
              <span class="mb-text-gray-300 mb-text-sm">{{ t('Naviga albero / seleziona (Struttura)') }}</span>
              <div class="mb-flex mb-gap-1">
                <kbd class="mb-bg-gray-700 mb-text-gray-300 mb-px-2 mb-py-0.5 mb-rounded mb-text-xs mb-font-mono mb-border mb-border-gray-600">↑</kbd>
                <kbd class="mb-bg-gray-700 mb-text-gray-300 mb-px-2 mb-py-0.5 mb-rounded mb-text-xs mb-font-mono mb-border mb-border-gray-600">↓</kbd>
                <span class="mb-text-gray-500 mb-text-xs">/</span>
                <kbd class="mb-bg-gray-700 mb-text-gray-300 mb-px-2 mb-py-0.5 mb-rounded mb-text-xs mb-font-mono mb-border mb-border-gray-600">↵</kbd>
              </div>
            </div>
            <div class="mb-flex mb-items-center mb-justify-between">
              <span class="mb-text-gray-300 mb-text-sm">{{ t('Espandi / comprimi nodo') }}</span>
              <div class="mb-flex mb-gap-1">
                <kbd class="mb-bg-gray-700 mb-text-gray-300 mb-px-2 mb-py-0.5 mb-rounded mb-text-xs mb-font-mono mb-border mb-border-gray-600">←</kbd>
                <span class="mb-text-gray-500 mb-text-xs">/</span>
                <kbd class="mb-bg-gray-700 mb-text-gray-300 mb-px-2 mb-py-0.5 mb-rounded mb-text-xs mb-font-mono mb-border mb-border-gray-600">→</kbd>
              </div>
            </div>
            <div class="mb-flex mb-items-center mb-justify-between">
              <span class="mb-text-gray-300 mb-text-sm">{{ t('Sposta tra contenitori (albero Struttura)') }}</span>
              <div class="mb-flex mb-gap-1">
                <kbd class="mb-bg-gray-700 mb-text-gray-300 mb-px-2 mb-py-0.5 mb-rounded mb-text-xs mb-font-mono mb-border mb-border-gray-600">M</kbd>
                <span class="mb-text-gray-500 mb-text-xs">→</span>
                <kbd class="mb-bg-gray-700 mb-text-gray-300 mb-px-2 mb-py-0.5 mb-rounded mb-text-xs mb-font-mono mb-border mb-border-gray-600">↑</kbd>
                <kbd class="mb-bg-gray-700 mb-text-gray-300 mb-px-2 mb-py-0.5 mb-rounded mb-text-xs mb-font-mono mb-border mb-border-gray-600">↓</kbd>
                <span class="mb-text-gray-500 mb-text-xs">→</span>
                <kbd class="mb-bg-gray-700 mb-text-gray-300 mb-px-2 mb-py-0.5 mb-rounded mb-text-xs mb-font-mono mb-border mb-border-gray-600">↵</kbd>
              </div>
            </div>
            <div class="mb-flex mb-items-center mb-justify-between">
              <span class="mb-text-gray-300 mb-text-sm">{{ t('Copia stile') }}</span>
              <div class="mb-flex mb-gap-1">
                <kbd class="mb-bg-gray-700 mb-text-gray-300 mb-px-2 mb-py-0.5 mb-rounded mb-text-xs mb-font-mono mb-border mb-border-gray-600">Ctrl</kbd>
                <span class="mb-text-gray-500 mb-text-xs">+</span>
                <kbd class="mb-bg-gray-700 mb-text-gray-300 mb-px-2 mb-py-0.5 mb-rounded mb-text-xs mb-font-mono mb-border mb-border-gray-600">Alt</kbd>
                <span class="mb-text-gray-500 mb-text-xs">+</span>
                <kbd class="mb-bg-gray-700 mb-text-gray-300 mb-px-2 mb-py-0.5 mb-rounded mb-text-xs mb-font-mono mb-border mb-border-gray-600">C</kbd>
              </div>
            </div>
            <div class="mb-flex mb-items-center mb-justify-between">
              <span class="mb-text-gray-300 mb-text-sm">{{ t('Incolla stile') }}</span>
              <div class="mb-flex mb-gap-1">
                <kbd class="mb-bg-gray-700 mb-text-gray-300 mb-px-2 mb-py-0.5 mb-rounded mb-text-xs mb-font-mono mb-border mb-border-gray-600">Ctrl</kbd>
                <span class="mb-text-gray-500 mb-text-xs">+</span>
                <kbd class="mb-bg-gray-700 mb-text-gray-300 mb-px-2 mb-py-0.5 mb-rounded mb-text-xs mb-font-mono mb-border mb-border-gray-600">Alt</kbd>
                <span class="mb-text-gray-500 mb-text-xs">+</span>
                <kbd class="mb-bg-gray-700 mb-text-gray-300 mb-px-2 mb-py-0.5 mb-rounded mb-text-xs mb-font-mono mb-border mb-border-gray-600">V</kbd>
              </div>
            </div>
            <div class="mb-flex mb-items-center mb-justify-between">
              <span class="mb-text-gray-300 mb-text-sm">{{ t('Cerca tile') }}</span>
              <div class="mb-flex mb-gap-1">
                <kbd class="mb-bg-gray-700 mb-text-gray-300 mb-px-2 mb-py-0.5 mb-rounded mb-text-xs mb-font-mono mb-border mb-border-gray-600">Ctrl</kbd>
                <span class="mb-text-gray-500 mb-text-xs">+</span>
                <kbd class="mb-bg-gray-700 mb-text-gray-300 mb-px-2 mb-py-0.5 mb-rounded mb-text-xs mb-font-mono mb-border mb-border-gray-600">K</kbd>
              </div>
            </div>
            <div v-if="hasAiKey" class="mb-flex mb-items-center mb-justify-between">
              <span class="mb-text-gray-300 mb-text-sm">{{ t('Assistente AI') }}</span>
              <div class="mb-flex mb-gap-1">
                <kbd class="mb-bg-gray-700 mb-text-gray-300 mb-px-2 mb-py-0.5 mb-rounded mb-text-xs mb-font-mono mb-border mb-border-gray-600">Ctrl</kbd>
                <span class="mb-text-gray-500 mb-text-xs">+</span>
                <kbd class="mb-bg-gray-700 mb-text-gray-300 mb-px-2 mb-py-0.5 mb-rounded mb-text-xs mb-font-mono mb-border mb-border-gray-600">Shift</kbd>
                <span class="mb-text-gray-500 mb-text-xs">+</span>
                <kbd class="mb-bg-gray-700 mb-text-gray-300 mb-px-2 mb-py-0.5 mb-rounded mb-text-xs mb-font-mono mb-border mb-border-gray-600">A</kbd>
              </div>
            </div>
          </div>
          <div class="mb-mt-4 mb-pt-3 mb-border-t mb-border-gray-700">
            <p class="mb-text-gray-500 mb-text-xs mb-m-0">Su Mac usa <kbd class="mb-bg-gray-700 mb-text-gray-400 mb-px-1.5 mb-py-0.5 mb-rounded mb-text-[10px] mb-font-mono mb-border mb-border-gray-600">Cmd</kbd> al posto di Ctrl</p>
          </div>
        </div>
      </div>
      </Teleport>
      <Teleport to="body">
      <div
        v-if="showA11y"
        class="mb-fixed mb-inset-0 mb-z-[99999] mb-flex mb-items-center mb-justify-center"
        @click.self="showA11y = false"
        style="background:rgba(0,0,0,0.6)"
      >
        <div class="mb-bg-gray-800 mb-border mb-border-gray-600 mb-rounded-xl mb-p-6 mb-shadow-2xl mb-w-[460px] mb-max-h-[80vh] mb-overflow-auto" @click.stop>
          <div class="mb-flex mb-items-center mb-justify-between mb-mb-4">
            <h3 class="mb-text-white mb-text-base mb-font-semibold mb-m-0">{{ t('Accessibilità — contrasto & alt') }}</h3>
            <button @click="showA11y = false" class="mb-text-gray-400 hover:mb-text-white mb-transition-colors" :aria-label="t('Chiudi')">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
          </div>
          <div v-if="a11yLoading" class="mb-text-gray-400 mb-text-sm mb-py-4 mb-text-center">{{ t('Analisi in corso…') }}</div>
          <template v-else>
            <div v-if="a11yScore !== null" class="mb-text-gray-300 mb-text-sm mb-mb-3">
              {{ t('Punteggio') }}:
              <span :class="a11yScore >= 90 ? 'mb-text-green-400' : (a11yScore >= 60 ? 'mb-text-yellow-400' : 'mb-text-red-400')" class="mb-font-bold">{{ a11yScore }}/100</span>
            </div>
            <div v-if="a11yIssues.length === 0" class="mb-text-green-400 mb-text-sm mb-py-3">✓ {{ t('Nessun problema di contrasto o alt rilevato.') }}</div>
            <ul v-else class="mb-space-y-2 mb-list-none mb-p-0 mb-m-0">
              <li v-for="(iss, i) in a11yIssues" :key="i" class="mb-bg-gray-700/50 mb-rounded-lg mb-p-3 mb-border-l-2" :class="iss.type === 'error' ? 'mb-border-red-500' : 'mb-border-yellow-500'">
                <div class="mb-text-gray-200 mb-text-sm">{{ iss.message }}</div>
                <div v-if="iss.ratio" class="mb-flex mb-items-center mb-gap-2 mb-mt-1.5 mb-text-xs mb-text-gray-400">
                  <span class="mb-inline-block mb-w-4 mb-h-4 mb-rounded mb-border mb-border-gray-500" :style="{ background: iss.bg }"></span>
                  <span class="mb-inline-block mb-w-4 mb-h-4 mb-rounded mb-border mb-border-gray-500" :style="{ background: iss.fg }"></span>
                  <span>{{ iss.ratio }}:1 ({{ t('min') }} {{ iss.threshold }}:1)</span>
                </div>
                <div v-else-if="iss.kind === 'alt'" class="mb-mt-1 mb-text-xs mb-text-gray-500">{{ t('Manca alt — WCAG 1.1.1') }}</div>
              </li>
            </ul>
          </template>
          <div class="mb-mt-4 mb-pt-3 mb-border-t mb-border-gray-700">
            <p class="mb-text-gray-500 mb-text-xs mb-m-0">{{ t('Contrasto testo/sfondo secondo WCAG 2.2 AA (4.5:1 normale, 3:1 grande). I token colore sono risolti al valore reale.') }}</p>
          </div>
        </div>
      </div>
      </Teleport>
      <!-- Libreria Blocchi & Pagine -->
      <button
        @click="$emit('open-library')"
        class="mb-px-2 mb-py-1.5 mb-rounded-md mb-transition-colors mb-text-cyan-400 hover:mb-text-cyan-200 hover:mb-bg-gray-700"
        :title="t('Blocchi & Pagine')"
        :aria-label="t('Blocchi & Pagine')"
      >
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/></svg>
      </button>
      <!-- Temi (nascosto dove l'import è disabilitato: sandbox condivise) -->
      <button
        v-if="!importsDisabled"
        @click="$emit('open-themes')"
        class="mb-px-2 mb-py-1.5 mb-rounded-md mb-transition-colors mb-text-amber-400 hover:mb-text-amber-200 hover:mb-bg-gray-700"
        :title="t('Temi sito')"
        :aria-label="t('Temi sito')"
      >
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2a10 10 0 1 0 10 10H12V2Z"/><path d="M12 2a10 10 0 0 1 10 10"/><path d="M12 12l8-4"/></svg>
      </button>
      <!-- AI Assistant (solo se chiave API configurata) -->
      <button
        v-if="hasAiKey"
        @click="$emit('open-ai')"
        class="mb-px-2 mb-py-1.5 mb-rounded-md mb-transition-colors mb-text-purple-400 hover:mb-text-purple-200 hover:mb-bg-gray-700"
        :title="t('Assistente AI (Ctrl+Shift+A)')"
        :aria-label="t('Assistente AI')"
      >
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
      </button>
      <!-- Import/Export -->
      <button
        @click="exportTemplate"
        class="mb-px-2 mb-py-1.5 mb-rounded-md mb-transition-colors mb-text-gray-400 hover:mb-text-gray-200 hover:mb-bg-gray-700"
        :title="t('Esporta template JSON')"
        :aria-label="t('Esporta template')"
      >
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
      </button>
      <button
        v-if="!importsDisabled"
        @click="importTemplate"
        class="mb-px-2 mb-py-1.5 mb-rounded-md mb-transition-colors mb-text-gray-400 hover:mb-text-gray-200 hover:mb-bg-gray-700"
        :title="t('Importa template JSON')"
        :aria-label="t('Importa template')"
      >
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" x2="12" y1="3" y2="15"/></svg>
      </button>
      <!-- Cronologia revisioni -->
      <button
        @click="onOpenRevisions"
        class="mb-px-2 mb-py-1.5 mb-rounded-md mb-transition-colors mb-text-gray-400 hover:mb-text-gray-200 hover:mb-bg-gray-700"
        :title="t('Cronologia revisioni')"
        :aria-label="t('Cronologia revisioni')"
      >
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/><path d="M12 7v5l4 2"/></svg>
      </button>
      <!-- Ricerca rapida -->
      <button
        @click="$emit('open-finder')"
        class="mb-px-2 mb-py-1.5 mb-rounded-md mb-transition-colors mb-text-gray-400 hover:mb-text-gray-200 hover:mb-bg-gray-700"
        :title="t('Cerca tile (Ctrl+K)')"
        :aria-label="t('Cerca tile')"
      >
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
      </button>
      <span class="mb-text-gray-700">|</span>
      <button
        @click="history.undo()"
        :disabled="!history.canUndo()"
        :class="[
          'mb-px-2 mb-py-1.5 mb-rounded-md mb-transition-colors',
          history.canUndo()
            ? 'mb-text-gray-300 hover:mb-text-white hover:mb-bg-gray-700'
            : 'mb-text-gray-600 mb-cursor-not-allowed'
        ]"
        :title="t('Annulla (Ctrl+Z)')"
        :aria-label="t('Annulla')"
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
        :title="t('Ripeti (Ctrl+Shift+Z)')"
        :aria-label="t('Ripeti')"
      >
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 7v6h-6"/><path d="M3 17a9 9 0 0 1 9-9 9 9 0 0 1 6 2.3L21 13"/></svg>
      </button>
      <button
        @click="builderStore.cleanMode = !builderStore.cleanMode"
        :class="[
          'mb-px-2 mb-py-1.5 mb-rounded-md mb-transition-colors',
          builderStore.cleanMode
            ? 'mb-bg-primary-600/20 mb-text-primary-300'
            : 'mb-text-gray-400 hover:mb-text-gray-200 hover:mb-bg-gray-700'
        ]"
        :title="t('Modalità pulita (WYSIWYG)')"
        :aria-label="t('Modalità pulita')"
      >
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
      </button>
      <button
        @click="builderStore.wireframeMode = !builderStore.wireframeMode"
        :class="[
          'mb-px-3 mb-py-1.5 mb-text-xs mb-rounded-md mb-border mb-transition-colors mb-flex mb-items-center mb-gap-1.5',
          builderStore.wireframeMode
            ? 'mb-border-amber-500 mb-bg-amber-600/20 mb-text-amber-300'
            : 'mb-border-gray-600 mb-text-gray-400 hover:mb-bg-gray-700'
        ]"
        :title="t('Mostra gabbia di costruzione (bordi celle, righe, sezioni)')"
      >
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>
        {{ t('Gabbia') }}
      </button>
      <button
        v-if="realPreviewUrl"
        @click="openRealPreview"
        class="mb-px-3 mb-py-1.5 mb-text-xs mb-rounded-md mb-border mb-border-emerald-600 mb-text-emerald-400 hover:mb-bg-emerald-600/20 mb-transition-colors mb-flex mb-items-center mb-gap-1"
        :title="t('Apri la pagina reale in un nuovo tab')"
      >
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" x2="21" y1="14" y2="3"/></svg>
        {{ t('Reale') }}
      </button>
      <button
        v-if="builderStore.currentTemplate?.id"
        @click="regenerateThumbnail"
        class="mb-px-2 mb-py-1.5 mb-text-xs mb-rounded-md mb-border mb-border-gray-600 mb-text-gray-300 hover:mb-bg-gray-700 mb-transition-colors"
        :title="t('Rigenera anteprima 16:9 della pagina')"
      >
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="14" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>
      </button>
      <!-- Indicatore stato salvataggio -->
      <div class="mb-flex mb-items-center mb-gap-1 mb-text-[11px] mb-mr-0.5 mb-select-none" :title="saveStatusTitle">
        <template v-if="builderStore.isSaving">
          <svg class="mb-animate-spin mb-text-gray-400" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
        </template>
        <template v-else-if="builderStore.isAnyDirty">
          <span class="mb-w-1.5 mb-h-1.5 mb-rounded-full mb-bg-amber-400"></span>
          <span class="mb-text-amber-400/90">{{ t('Non salvato') }}</span>
        </template>
        <template v-else>
          <svg class="mb-text-emerald-400" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
          <span class="mb-text-gray-500">{{ t('Salvato') }}</span>
        </template>
      </div>
      <button
        @click="builderStore.saveTemplate()"
        :disabled="builderStore.isSaving || !builderStore.isAnyDirty"
        :class="[
          'mb-px-4 mb-py-1.5 mb-text-xs mb-font-medium mb-rounded-md mb-transition-colors',
          builderStore.isAnyDirty
            ? 'mb-bg-primary-600 mb-text-white hover:mb-bg-primary-700'
            : 'mb-bg-gray-700 mb-text-gray-500 mb-cursor-not-allowed'
        ]"
      >
        {{ builderStore.isSaving ? t('Salvataggio…') : t('Salva') }}
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
        {{ isPublished ? t('Pubblicato') : t('Pubblica') }}
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
        {{ isActiveHeader ? t('Disattiva') : t('Attiva') }}
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
        {{ isActiveFooter ? t('Disattiva') : t('Attiva') }}
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
        {{ isActiveSingle ? t('Disattiva') : t('Attiva') }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, nextTick, onMounted, onBeforeUnmount, watch } from 'vue';
import { useBuilderStore } from '@/stores/builder';
import { useTilesStore } from '@/stores/tiles';
import { useHistory } from '@/composables/useHistory';
import { useToast } from '@/composables/useToast';
import { useFocusTrap } from '@/composables/useFocusTrap';
import { t } from '@/i18n';

const emit = defineEmits(['back', 'open-revisions', 'open-finder', 'open-ai', 'open-library', 'open-themes']);

function onOpenRevisions() {
  emit('open-revisions');
}

const builderStore = useBuilderStore();
const tilesStore = useTilesStore();
const history = useHistory();
const toast = useToast();

// ─── Indicatore stato salvataggio ───
const lastSavedAt = ref(null);
watch(() => builderStore.isSaving, (now, prev) => {
  if (prev && !now && !builderStore.isAnyDirty) lastSavedAt.value = new Date();
});
const saveStatusTitle = computed(() => {
  if (builderStore.isSaving) return t('Salvataggio in corso…');
  if (builderStore.isAnyDirty) return t('Ci sono modifiche non salvate');
  if (lastSavedAt.value) return t('Salvato alle') + ' ' + lastSavedAt.value.toLocaleTimeString();
  return t('Tutte le modifiche sono salvate');
});

// AI availability
const hasAiKey = !!(window.oloData && window.oloData.hasAiKey);
const importsDisabled = !!(window.oloData && window.oloData.importsDisabled);

// ─── Dynamic viewport buttons (only show enabled breakpoints) ───
const allViewports = [
  { key: 'widescreen', bpKey: 'widescreen', label: 'Widescreen', svg: '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="22" height="12" x="1" y="4" rx="2"/><line x1="8" x2="16" y1="20" y2="20"/><line x1="12" x2="12" y1="16" y2="20"/></svg>' },
  { key: 'desktop', bpKey: null, label: 'Desktop', svg: '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="3" rx="2"/><line x1="8" x2="16" y1="21" y2="21"/><line x1="12" x2="12" y1="17" y2="21"/></svg>' },
  { key: 'tablet_landscape', bpKey: 'tablet_landscape', label: 'Tablet Landscape', svg: '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><line x1="12" x2="12.01" y1="12" y2="12"/></svg>' },
  { key: 'tablet', bpKey: 'tablet', label: 'Tablet', svg: '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="16" height="20" x="4" y="2" rx="2"/><line x1="12" x2="12.01" y1="18" y2="18"/></svg>' },
  { key: 'mobile_landscape', bpKey: 'mobile_landscape', label: 'Mobile Landscape', svg: '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="12" x2="12.01" y1="12" y2="12"/></svg>' },
  { key: 'mobile', bpKey: 'mobile', label: 'Mobile', svg: '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="14" height="20" x="5" y="2" rx="2"/><line x1="12" x2="12.01" y1="18" y2="18"/></svg>' },
];

const visibleViewports = computed(() => {
  const bp = builderStore.pageSettings.breakpoints || {};
  const bpEnabled = (window.oloData || {}).breakpointsEnabled || {};
  return allViewports.filter(vp => {
    if (!vp.bpKey) return true; // Desktop always visible
    return bpEnabled[vp.bpKey] !== false;
  }).map(vp => {
    const px = vp.bpKey ? (bp[vp.bpKey] || '') : '';
    return { ...vp, title: px ? `${vp.label} (${px}px)` : vp.label };
  });
});

// Shortcuts panel
const showShortcuts = ref(false);

// Pannello accessibilità: controllo contrasto colore del template (endpoint /contrast-check,
// che ora risolve i token var(--olo-color-*) al valore reale). Cabla il checker prima orfano.
const showA11y = ref(false);
const a11yLoading = ref(false);
const a11yIssues = ref([]);
const a11yScore = ref(null);
async function openA11y() {
  showA11y.value = true;
  const id = builderStore.currentTemplate?.id;
  if (!id) { a11yIssues.value = []; a11yScore.value = null; return; }
  a11yLoading.value = true;
  try {
    const olo = window.oloData || {};
    const res = await fetch(`${olo.restUrl}/contrast-check/${id}`, { headers: { 'X-WP-Nonce': olo.nonce } });
    const data = await res.json();
    a11yIssues.value = Array.isArray(data.issues) ? data.issues : [];
    a11yScore.value = typeof data.score === 'number' ? data.score : null;
  } catch (e) {
    a11yIssues.value = [];
    a11yScore.value = null;
  } finally {
    a11yLoading.value = false;
  }
}
const shortcutsModalRef = ref(null);
const shortcutsTrap = useFocusTrap(shortcutsModalRef, { onEscape: () => { showShortcuts.value = false; } });
watch(showShortcuts, (v) => {
  if (v) { nextTick(() => shortcutsTrap.activate()); }
  else { shortcutsTrap.deactivate(); }
});
const showZoomMenu = ref(false);
const zoomMenuStyle = computed(() => {
  return { top: '48px', left: '50%', transform: 'translateX(-50%)' };
});

// ─── Global Keyboard Shortcuts (Copy/Paste Style) ───
function onGlobalKeydown(e) {
  const tag = e.target?.tagName || 'DIV';
  const isEditing = tag === 'INPUT' || tag === 'TEXTAREA' || tag === 'SELECT' || e.target?.isContentEditable;

  // Ctrl+Z → Undo / Ctrl+Shift+Z → Redo
  if ((e.ctrlKey || e.metaKey) && (e.key === 'z' || e.code === 'KeyZ')) {
    e.preventDefault();
    if (e.shiftKey) { history.redo(); } else { history.undo(); }
    return;
  }
  // Ctrl+S → Save
  if ((e.ctrlKey || e.metaKey) && (e.key === 's' || e.code === 'KeyS') && !e.altKey) {
    e.preventDefault();
    if (builderStore.isDirty) { builderStore.saveTemplate(); }
    return;
  }

  // Delete / Backspace → elimina la/le tile selezionate (multi-selezione inclusa)
  if ((e.key === 'Delete' || e.key === 'Backspace') && !isEditing) {
    const ids = builderStore.selectedTileIds.length
      ? [...builderStore.selectedTileIds]
      : (builderStore.selectedTileId ? [builderStore.selectedTileId] : []);
    if (ids.length) {
      e.preventDefault();
      ids.forEach(id => tilesStore.removeTile(id));
      builderStore.deselectTile();
      builderStore.isDirty = true;
    }
  }
  // Alt+↑/↓ → sposta il nodo selezionato su/giù dentro il suo parent
  // (riordino senza drag: tastiera = accessibilità + precisione)
  if (e.altKey && !e.ctrlKey && !e.metaKey && (e.key === 'ArrowUp' || e.key === 'ArrowDown') && !isEditing) {
    const id = builderStore.selectedTileId;
    if (id) {
      e.preventDefault();
      if (tilesStore.nudgeNode(id, e.key === 'ArrowUp' ? -1 : 1)) {
        builderStore.markDirtyForTile(id);
      }
      return;
    }
  }
  // Ctrl+C → Copia tile
  if ((e.ctrlKey || e.metaKey) && e.key === 'c' && !e.altKey && !e.shiftKey && !isEditing) {
    const id = builderStore.selectedTileId;
    if (id) {
      e.preventDefault();
      tilesStore.copyTile(id);
    }
  }
  // Ctrl+V → Incolla tile
  if ((e.ctrlKey || e.metaKey) && e.key === 'v' && !e.altKey && !e.shiftKey && !isEditing) {
    if (tilesStore.clipboardTile) {
      e.preventDefault();
      const id = builderStore.selectedTileId;
      tilesStore.pasteAfterTile(id);
      builderStore.isDirty = true;
    }
  }
  // Ctrl+D → Duplica la/le tile selezionate (multi-selezione inclusa)
  if ((e.ctrlKey || e.metaKey) && (e.key === 'd' || e.code === 'KeyD') && !e.altKey && !e.shiftKey && !isEditing) {
    const ids = builderStore.selectedTileIds.length
      ? [...builderStore.selectedTileIds]
      : (builderStore.selectedTileId ? [builderStore.selectedTileId] : []);
    if (ids.length) {
      e.preventDefault();
      ids.forEach(id => tilesStore.duplicateTile(id));
      builderStore.isDirty = true;
    }
  }
  // Ctrl+Alt+C → Copy Style
  if (e.ctrlKey && e.altKey && (e.key === 'c' || e.code === 'KeyC')) {
    e.preventDefault();
    const id = builderStore.selectedTileId;
    if (id) tilesStore.copyStyle(id);
    return;
  }
  // Ctrl+Alt+V → Paste Style
  if (e.ctrlKey && e.altKey && (e.key === 'v' || e.code === 'KeyV')) {
    e.preventDefault();
    const id = builderStore.selectedTileId;
    if (id && tilesStore.clipboardStyle) {
      tilesStore.pasteStyle(id);
      builderStore.isDirty = true;
    }
    return;
  }
}

onMounted(() => {
  document.addEventListener('keydown', onGlobalKeydown);
  window.addEventListener('message', onIframeKey);
});
onBeforeUnmount(() => {
  document.removeEventListener('keydown', onGlobalKeydown);
  window.removeEventListener('message', onIframeKey);
});

function onIframeKey(e) {
  if (e.data && e.data.type === 'olo:keydown') {
    // Create a fake event with preventDefault
    const fakeEvent = { ...e.data, target: { tagName: 'DIV', isContentEditable: false }, preventDefault() {} };
    onGlobalKeydown(fakeEvent);
  }
}

// ─── Template Import/Export ───
async function exportTemplate() {
  const tplId = builderStore.currentTemplate?.id;
  if (!tplId) return;
  try {
    const oloData = window.oloData || {};
    const res = await fetch(`${oloData.restUrl}/templates/${tplId}/export`, {
      headers: { 'X-WP-Nonce': oloData.nonce },
    });
    if (!res.ok) throw new Error('Export failed');
    const data = await res.json();
    const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `olobuild-${(builderStore.currentTemplate?.title || 'template').replace(/\s+/g, '-')}.json`;
    a.click();
    URL.revokeObjectURL(url);
  } catch (err) {
    console.error('Export error:', err);
    toast.error(t('Errore durante l\'esportazione'));
  }
}

function importTemplate() {
  const input = document.createElement('input');
  input.type = 'file';
  input.accept = '.json';
  input.onchange = async (e) => {
    const file = e.target.files[0];
    if (!file) return;
    try {
      const text = await file.text();
      const data = JSON.parse(text);
      const oloData = window.oloData || {};
      const res = await fetch(`${oloData.restUrl}/templates/import`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-WP-Nonce': oloData.nonce,
        },
        body: JSON.stringify(data),
      });
      if (!res.ok) throw new Error('Import failed');
      const result = await res.json();
      toast.success(t('Template importato con successo'));
    } catch (err) {
      console.error('Import error:', err);
      toast.error(t('Errore durante l\'importazione'));
    }
  };
  input.click();
}

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
const wpAdminUrl = (oloData.restUrl || '').replace('/wp-json/olobuild/v1', '/wp-admin/');

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

// ─── Real Preview ───
const realPreviewUrl = computed(() => {
  // Priority 1: permalink passato da PHP (aperto da una pagina specifica via ?post_id=)
  if (oloData.postPermalink) return oloData.postPermalink;
  // Priority 2: permalink REALE risolto dal resolver robusto lato REST (primo post
  // PUBBLICATO collegato al template via meta `_olo_template_id`), con get_permalink()
  // corretto per page/CPT/post. Arriva nel template via REST → NON dipende dai parametri
  // dell'URL del builder, a differenza di oloData.linkedPostPermalink.
  // Evita il 404 quando settings.post_id punta a un draft stantio (es. dopo import tema).
  if (builderStore.currentTemplate?.linked_post_permalink) {
    return builderStore.currentTemplate.linked_post_permalink;
  }
  // Priority 3: permalink risolto lato PHP al caricamento pagina (richiede template_id in URL).
  if (oloData.linkedPostPermalink) return oloData.linkedPostPermalink;
  // Nessun fallback `?p=ID`: dava 404 sulle page/CPT. Se non c'è un post collegato
  // risolvibile, il pulsante resta nascosto invece di offrire un link rotto.
  return '';
});

async function openRealPreview() {
  // Auto-save se ci sono modifiche non salvate
  if (builderStore.isDirty) {
    await builderStore.saveTemplate();
  }
  const url = realPreviewUrl.value;
  if (!url) {
    toast.warning(t('Nessun post collegato al template. Imposta "Post collegato" nelle impostazioni.'));
    return;
  }
  // Hint per mu-plugin/sandbox: bypassa eventuali landing/CTA intermedie e
  // renderizza direttamente il template. Su server senza sandbox il parametro
  // è innocuo (ignorato da WordPress core).
  const sep = url.includes('?') ? '&' : '?';
  window.open(url + sep + 'olo_preview=1', '_blank');
}

async function regenerateThumbnail() {
  const id = builderStore.currentTemplate?.id;
  if (!id) return;
  if (typeof window.oloCaptureThumbnail !== 'function') {
    toast.error('Modulo thumbnail non caricato');
    return;
  }
  toast.info('Rigenero anteprima…');
  try {
    await window.oloCaptureThumbnail(id);
    toast.success('Anteprima aggiornata');
  } catch (e) {
    toast.error('Errore: ' + (e?.message || e));
  }
}
</script>
