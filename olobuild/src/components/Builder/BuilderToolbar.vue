<template>
  <div class="mb-flex mb-items-center mb-justify-between mb-h-12 mb-px-4 mb-bg-gray-800 mb-border-b mb-border-gray-700 mb-shrink-0" role="toolbar" aria-label="Barra strumenti builder">
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
        @click="builderStore.togglePageSettings()"
        :class="[
          'mb-px-2 mb-py-1.5 mb-rounded-md mb-transition-colors',
          builderStore.pageSettingsOpen
            ? 'mb-bg-primary-600/20 mb-text-primary-300'
            : 'mb-text-gray-400 hover:mb-text-gray-200 hover:mb-bg-gray-700'
        ]"
        title="Impostazioni pagina"
        aria-label="Impostazioni pagina"
      >
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg>
      </button>
    <div class="mb-flex mb-items-center mb-gap-1 mb-bg-gray-700 mb-rounded-lg mb-p-0.5">
      <!-- Widescreen -->
      <button
        @click="builderStore.setViewMode('widescreen')"
        :class="[
          'mb-px-2.5 mb-py-1 mb-rounded-md mb-transition-colors',
          builderStore.viewMode === 'widescreen'
            ? 'mb-bg-primary-600 mb-text-white'
            : 'mb-text-gray-400 hover:mb-text-gray-200'
        ]"
        title="Widescreen (1400px+)"
        aria-label="Vista widescreen"
      >
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="22" height="12" x="1" y="4" rx="2"/><line x1="8" x2="16" y1="20" y2="20"/><line x1="12" x2="12" y1="16" y2="20"/></svg>
      </button>
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
        aria-label="Vista desktop"
      >
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="3" rx="2"/><line x1="8" x2="16" y1="21" y2="21"/><line x1="12" x2="12" y1="17" y2="21"/></svg>
      </button>
      <!-- Tablet Landscape -->
      <button
        @click="builderStore.setViewMode('tablet_landscape')"
        :class="[
          'mb-px-2.5 mb-py-1 mb-rounded-md mb-transition-colors',
          builderStore.viewMode === 'tablet_landscape'
            ? 'mb-bg-primary-600 mb-text-white'
            : 'mb-text-gray-400 hover:mb-text-gray-200'
        ]"
        title="Tablet Landscape (1200px)"
        aria-label="Vista tablet landscape"
      >
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><line x1="12" x2="12.01" y1="12" y2="12"/></svg>
      </button>
      <!-- Tablet Portrait -->
      <button
        @click="builderStore.setViewMode('tablet')"
        :class="[
          'mb-px-2.5 mb-py-1 mb-rounded-md mb-transition-colors',
          builderStore.viewMode === 'tablet'
            ? 'mb-bg-primary-600 mb-text-white'
            : 'mb-text-gray-400 hover:mb-text-gray-200'
        ]"
        title="Tablet Portrait (960px)"
        aria-label="Vista tablet portrait"
      >
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="16" height="20" x="4" y="2" rx="2"/><line x1="12" x2="12.01" y1="18" y2="18"/></svg>
      </button>
      <!-- Mobile Landscape -->
      <button
        @click="builderStore.setViewMode('mobile_landscape')"
        :class="[
          'mb-px-2.5 mb-py-1 mb-rounded-md mb-transition-colors',
          builderStore.viewMode === 'mobile_landscape'
            ? 'mb-bg-primary-600 mb-text-white'
            : 'mb-text-gray-400 hover:mb-text-gray-200'
        ]"
        title="Mobile Landscape (640px)"
        aria-label="Vista mobile landscape"
      >
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="12" x2="12.01" y1="12" y2="12"/></svg>
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
        title="Mobile (480px)"
        aria-label="Vista mobile"
      >
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="14" height="20" x="5" y="2" rx="2"/><line x1="12" x2="12.01" y1="18" y2="18"/></svg>
      </button>
    </div>
    </div>

    <!-- Right: Actions -->
    <div class="mb-flex mb-items-center mb-gap-2">
      <!-- Keyboard shortcuts help -->
      <button
        @click="showShortcuts = !showShortcuts"
        class="mb-px-2 mb-py-1.5 mb-rounded-md mb-transition-colors mb-text-gray-400 hover:mb-text-gray-200 hover:mb-bg-gray-700 mb-text-xs mb-font-bold"
        title="Scorciatoie tastiera"
        aria-label="Scorciatoie tastiera"
      >
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M6 8h.001M10 8h.001M14 8h.001M18 8h.001M8 12h.001M12 12h.001M16 12h.001M7 16h10"/></svg>
      </button>
      <div
        v-if="showShortcuts"
        class="mb-fixed mb-inset-0 mb-z-[9999] mb-flex mb-items-center mb-justify-center"
        @click.self="showShortcuts = false"
        style="background:rgba(0,0,0,0.6)"
      >
        <div class="mb-bg-gray-800 mb-border mb-border-gray-600 mb-rounded-xl mb-p-6 mb-shadow-2xl mb-w-[380px]" @click.stop>
          <div class="mb-flex mb-items-center mb-justify-between mb-mb-4">
            <h3 class="mb-text-white mb-text-base mb-font-semibold mb-m-0">Scorciatoie tastiera</h3>
            <button
              @click="showShortcuts = false"
              class="mb-text-gray-400 hover:mb-text-white mb-transition-colors"
              aria-label="Chiudi"
            >
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
          </div>
          <div class="mb-space-y-3">
            <div class="mb-flex mb-items-center mb-justify-between">
              <span class="mb-text-gray-300 mb-text-sm">Annulla</span>
              <div class="mb-flex mb-gap-1">
                <kbd class="mb-bg-gray-700 mb-text-gray-300 mb-px-2 mb-py-0.5 mb-rounded mb-text-xs mb-font-mono mb-border mb-border-gray-600">Ctrl</kbd>
                <span class="mb-text-gray-500 mb-text-xs">+</span>
                <kbd class="mb-bg-gray-700 mb-text-gray-300 mb-px-2 mb-py-0.5 mb-rounded mb-text-xs mb-font-mono mb-border mb-border-gray-600">Z</kbd>
              </div>
            </div>
            <div class="mb-flex mb-items-center mb-justify-between">
              <span class="mb-text-gray-300 mb-text-sm">Ripeti</span>
              <div class="mb-flex mb-gap-1">
                <kbd class="mb-bg-gray-700 mb-text-gray-300 mb-px-2 mb-py-0.5 mb-rounded mb-text-xs mb-font-mono mb-border mb-border-gray-600">Ctrl</kbd>
                <span class="mb-text-gray-500 mb-text-xs">+</span>
                <kbd class="mb-bg-gray-700 mb-text-gray-300 mb-px-2 mb-py-0.5 mb-rounded mb-text-xs mb-font-mono mb-border mb-border-gray-600">Shift</kbd>
                <span class="mb-text-gray-500 mb-text-xs">+</span>
                <kbd class="mb-bg-gray-700 mb-text-gray-300 mb-px-2 mb-py-0.5 mb-rounded mb-text-xs mb-font-mono mb-border mb-border-gray-600">Z</kbd>
              </div>
            </div>
            <div class="mb-flex mb-items-center mb-justify-between">
              <span class="mb-text-gray-300 mb-text-sm">Salva</span>
              <div class="mb-flex mb-gap-1">
                <kbd class="mb-bg-gray-700 mb-text-gray-300 mb-px-2 mb-py-0.5 mb-rounded mb-text-xs mb-font-mono mb-border mb-border-gray-600">Ctrl</kbd>
                <span class="mb-text-gray-500 mb-text-xs">+</span>
                <kbd class="mb-bg-gray-700 mb-text-gray-300 mb-px-2 mb-py-0.5 mb-rounded mb-text-xs mb-font-mono mb-border mb-border-gray-600">S</kbd>
              </div>
            </div>
            <div class="mb-flex mb-items-center mb-justify-between">
              <span class="mb-text-gray-300 mb-text-sm">Elimina tile selezionato</span>
              <div class="mb-flex mb-gap-1">
                <kbd class="mb-bg-gray-700 mb-text-gray-300 mb-px-2 mb-py-0.5 mb-rounded mb-text-xs mb-font-mono mb-border mb-border-gray-600">Canc</kbd>
                <span class="mb-text-gray-500 mb-text-xs">/</span>
                <kbd class="mb-bg-gray-700 mb-text-gray-300 mb-px-2 mb-py-0.5 mb-rounded mb-text-xs mb-font-mono mb-border mb-border-gray-600">Backspace</kbd>
              </div>
            </div>
            <div class="mb-flex mb-items-center mb-justify-between">
              <span class="mb-text-gray-300 mb-text-sm">Copia stile</span>
              <div class="mb-flex mb-gap-1">
                <kbd class="mb-bg-gray-700 mb-text-gray-300 mb-px-2 mb-py-0.5 mb-rounded mb-text-xs mb-font-mono mb-border mb-border-gray-600">Ctrl</kbd>
                <span class="mb-text-gray-500 mb-text-xs">+</span>
                <kbd class="mb-bg-gray-700 mb-text-gray-300 mb-px-2 mb-py-0.5 mb-rounded mb-text-xs mb-font-mono mb-border mb-border-gray-600">Alt</kbd>
                <span class="mb-text-gray-500 mb-text-xs">+</span>
                <kbd class="mb-bg-gray-700 mb-text-gray-300 mb-px-2 mb-py-0.5 mb-rounded mb-text-xs mb-font-mono mb-border mb-border-gray-600">C</kbd>
              </div>
            </div>
            <div class="mb-flex mb-items-center mb-justify-between">
              <span class="mb-text-gray-300 mb-text-sm">Incolla stile</span>
              <div class="mb-flex mb-gap-1">
                <kbd class="mb-bg-gray-700 mb-text-gray-300 mb-px-2 mb-py-0.5 mb-rounded mb-text-xs mb-font-mono mb-border mb-border-gray-600">Ctrl</kbd>
                <span class="mb-text-gray-500 mb-text-xs">+</span>
                <kbd class="mb-bg-gray-700 mb-text-gray-300 mb-px-2 mb-py-0.5 mb-rounded mb-text-xs mb-font-mono mb-border mb-border-gray-600">Alt</kbd>
                <span class="mb-text-gray-500 mb-text-xs">+</span>
                <kbd class="mb-bg-gray-700 mb-text-gray-300 mb-px-2 mb-py-0.5 mb-rounded mb-text-xs mb-font-mono mb-border mb-border-gray-600">V</kbd>
              </div>
            </div>
            <div class="mb-flex mb-items-center mb-justify-between">
              <span class="mb-text-gray-300 mb-text-sm">Cerca tile</span>
              <div class="mb-flex mb-gap-1">
                <kbd class="mb-bg-gray-700 mb-text-gray-300 mb-px-2 mb-py-0.5 mb-rounded mb-text-xs mb-font-mono mb-border mb-border-gray-600">Ctrl</kbd>
                <span class="mb-text-gray-500 mb-text-xs">+</span>
                <kbd class="mb-bg-gray-700 mb-text-gray-300 mb-px-2 mb-py-0.5 mb-rounded mb-text-xs mb-font-mono mb-border mb-border-gray-600">K</kbd>
              </div>
            </div>
            <div v-if="hasAiKey" class="mb-flex mb-items-center mb-justify-between">
              <span class="mb-text-gray-300 mb-text-sm">Assistente AI</span>
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
      <!-- Libreria Template -->
      <button
        @click="$emit('open-library')"
        class="mb-px-2 mb-py-1.5 mb-rounded-md mb-transition-colors mb-text-cyan-400 hover:mb-text-cyan-200 hover:mb-bg-gray-700"
        title="Libreria Template"
        aria-label="Libreria Template"
      >
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/></svg>
      </button>
      <!-- AI Assistant (solo se chiave API configurata) -->
      <button
        v-if="hasAiKey"
        @click="$emit('open-ai')"
        class="mb-px-2 mb-py-1.5 mb-rounded-md mb-transition-colors mb-text-purple-400 hover:mb-text-purple-200 hover:mb-bg-gray-700"
        title="Assistente AI (Ctrl+Shift+A)"
        aria-label="Assistente AI"
      >
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
      </button>
      <!-- Import/Export -->
      <button
        @click="exportTemplate"
        class="mb-px-2 mb-py-1.5 mb-rounded-md mb-transition-colors mb-text-gray-400 hover:mb-text-gray-200 hover:mb-bg-gray-700"
        title="Esporta template JSON"
        aria-label="Esporta template"
      >
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
      </button>
      <button
        @click="importTemplate"
        class="mb-px-2 mb-py-1.5 mb-rounded-md mb-transition-colors mb-text-gray-400 hover:mb-text-gray-200 hover:mb-bg-gray-700"
        title="Importa template JSON"
        aria-label="Importa template"
      >
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" x2="12" y1="3" y2="15"/></svg>
      </button>
      <!-- Cronologia revisioni -->
      <button
        @click="onOpenRevisions"
        class="mb-px-2 mb-py-1.5 mb-rounded-md mb-transition-colors mb-text-gray-400 hover:mb-text-gray-200 hover:mb-bg-gray-700"
        title="Cronologia revisioni"
        aria-label="Cronologia revisioni"
      >
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/><path d="M12 7v5l4 2"/></svg>
      </button>
      <!-- Ricerca rapida -->
      <button
        @click="$emit('open-finder')"
        class="mb-px-2 mb-py-1.5 mb-rounded-md mb-transition-colors mb-text-gray-400 hover:mb-text-gray-200 hover:mb-bg-gray-700"
        title="Cerca tile (Ctrl+K)"
        aria-label="Cerca tile"
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
        title="Annulla (Ctrl+Z)"
        aria-label="Annulla"
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
        aria-label="Ripeti"
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
        title="Anteprima nel builder"
      >
        {{ builderStore.previewMode ? 'Modifica' : 'Anteprima' }}
      </button>
      <button
        v-if="realPreviewUrl"
        @click="openRealPreview"
        class="mb-px-3 mb-py-1.5 mb-text-xs mb-rounded-md mb-border mb-border-emerald-600 mb-text-emerald-400 hover:mb-bg-emerald-600/20 mb-transition-colors mb-flex mb-items-center mb-gap-1"
        title="Apri la pagina reale in un nuovo tab"
      >
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" x2="21" y1="14" y2="3"/></svg>
        Reale
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
import { computed, ref, nextTick, onMounted, onBeforeUnmount } from 'vue';
import { useBuilderStore } from '@/stores/builder';
import { useTilesStore } from '@/stores/tiles';
import { useHistory } from '@/composables/useHistory';
import { useToast } from '@/composables/useToast';

const emit = defineEmits(['back', 'open-revisions', 'open-finder', 'open-ai', 'open-library']);

function onOpenRevisions() {
  emit('open-revisions');
}

const builderStore = useBuilderStore();
const tilesStore = useTilesStore();
const history = useHistory();
const toast = useToast();

// AI availability
const hasAiKey = !!(window.oloData && window.oloData.hasAiKey);

// Shortcuts panel
const showShortcuts = ref(false);

// ─── Global Keyboard Shortcuts (Copy/Paste Style) ───
function onGlobalKeydown(e) {
  // Ctrl+Alt+C → Copy Style
  if (e.ctrlKey && e.altKey && e.key === 'c') {
    e.preventDefault();
    const id = builderStore.selectedTileId;
    if (id) tilesStore.copyStyle(id);
  }
  // Ctrl+Alt+V → Paste Style
  if (e.ctrlKey && e.altKey && e.key === 'v') {
    e.preventDefault();
    const id = builderStore.selectedTileId;
    if (id && tilesStore.clipboardStyle) {
      tilesStore.pasteStyle(id);
      builderStore.isDirty = true;
    }
  }
}

onMounted(() => document.addEventListener('keydown', onGlobalKeydown));
onBeforeUnmount(() => document.removeEventListener('keydown', onGlobalKeydown));

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
    toast.error('Errore durante l\'esportazione');
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
      toast.success('Template importato con successo');
    } catch (err) {
      console.error('Import error:', err);
      toast.error('Errore durante l\'importazione');
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
  builderStore.togglePreview();
}

// ─── Real Preview ───
const realPreviewUrl = computed(() => {
  // Priority 1: permalink passato da PHP (aperto da una pagina specifica)
  if (oloData.postPermalink) return oloData.postPermalink;
  // Priority 2: post_id nei settings del template (collegamento salvato)
  const settingsPostId = builderStore.currentTemplate?.settings?.post_id;
  if (settingsPostId && parseInt(settingsPostId) > 0) {
    const home = oloData.siteInfo?.home_url || '';
    if (home) return `${home}/?p=${settingsPostId}`;
  }
  return '';
});

async function openRealPreview() {
  // Auto-save se ci sono modifiche non salvate
  if (builderStore.isDirty) {
    await builderStore.saveTemplate();
  }
  window.open(realPreviewUrl.value, '_blank');
}
</script>
