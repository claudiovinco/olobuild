<template>
    <transition name="fade">
      <div
        v-if="visible"
        class="mb-flex mb-items-center mb-justify-center"
        style="position:fixed;inset:0;z-index:100000;background:rgba(0,0,0,0.6)"
        @click.self="close"
      >
        <div
          ref="dialogRef"
          class="mb-bg-gray-800 mb-border mb-border-gray-600 mb-rounded-xl mb-shadow-2xl mb-w-[640px] mb-max-h-[85vh] mb-flex mb-flex-col mb-overflow-hidden"
          @click.stop
        >
          <!-- Header -->
          <div class="mb-flex mb-items-center mb-justify-between mb-px-5 mb-py-3 mb-border-b mb-border-gray-700">
            <div class="mb-flex mb-items-center mb-gap-2">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mb-text-purple-400">
                <path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/>
              </svg>
              <h3 class="mb-text-white mb-text-sm mb-font-semibold mb-m-0">{{ t('Assistente AI') }}</h3>
            </div>
            <button
              @click="close"
              class="mb-text-gray-400 hover:mb-text-white mb-transition-colors"
              :aria-label="t('Chiudi')"
            >
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
          </div>

          <!-- Tabs -->
          <div class="mb-flex mb-gap-0 mb-bg-gray-900 mb-border-b mb-border-gray-700">
            <button
              v-for="tab in tabs"
              :key="tab.id"
              @click="activeTab = tab.id"
              :class="[
                'mb-flex-1 mb-py-2.5 mb-text-xs mb-font-medium mb-transition-colors mb-border-b-2',
                activeTab === tab.id
                  ? 'mb-text-purple-300 mb-border-purple-500'
                  : 'mb-text-gray-500 mb-border-transparent hover:mb-text-gray-300'
              ]"
            >
              {{ tab.label }}
            </button>
          </div>

          <!-- Body -->
          <div class="mb-flex-1 mb-overflow-y-auto mb-p-5">

            <!-- ====== TAB: Genera Testo ====== -->
            <div v-if="activeTab === 'generate'" class="mb-space-y-4">
              <div class="mb-grid mb-grid-cols-3 mb-gap-3">
                <div>
                  <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">{{ t('Tipo') }}</label>
                  <FieldSelect ui="dropdown" :model-value="gen.type" :options="GEN_TYPE_OPTS" @update:model-value="gen.type = $event" />
                </div>
                <div>
                  <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">{{ t('Tono') }}</label>
                  <FieldSelect ui="dropdown" :model-value="gen.tone" :options="GEN_TONE_OPTS" @update:model-value="gen.tone = $event" />
                </div>
                <div>
                  <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">{{ t('Lingua') }}</label>
                  <FieldSelect ui="dropdown" :model-value="gen.language" :options="LANGUAGE_OPTS" @update:model-value="gen.language = $event" />
                </div>
              </div>

              <div>
                <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">{{ t('Descrivi cosa vuoi generare...') }}</label>
                <textarea
                  v-model="gen.prompt"
                  rows="3"
                  class="mb-w-full mb-bg-gray-700 mb-border mb-border-gray-600 mb-rounded-lg mb-px-3 mb-py-2 mb-text-sm mb-text-gray-200 mb-outline-none focus:mb-border-purple-500 mb-resize-none"
                  :placeholder="t('Es: Descrizione per un hotel di lusso con vista mare...')"
                ></textarea>
              </div>

              <div>
                <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">Lunghezza massima: {{ gen.maxLength }} parole</label>
                <input
                  type="range"
                  v-model.number="gen.maxLength"
                  min="50"
                  max="500"
                  step="10"
                  class="mb-w-full mb-accent-purple-500"
                />
              </div>

              <div class="mb-flex mb-gap-2">
                <button
                  @click="generateText"
                  :disabled="loading.generate || !gen.prompt.trim()"
                  class="mb-px-4 mb-py-2 mb-bg-purple-600 mb-text-white mb-text-sm mb-font-medium mb-rounded-lg hover:mb-bg-purple-500 disabled:mb-opacity-50 disabled:mb-cursor-not-allowed mb-transition-colors"
                >
                  {{ loading.generate ? 'Generazione...' : 'Genera' }}
                </button>
                <button
                  v-if="result.generate"
                  @click="generateText"
                  :disabled="loading.generate"
                  class="mb-px-4 mb-py-2 mb-bg-gray-700 mb-text-gray-300 mb-text-sm mb-rounded-lg hover:mb-bg-gray-600 disabled:mb-opacity-50 mb-transition-colors"
                >
                  {{ t('Rigenera') }}
                </button>
              </div>

              <!-- Risultato -->
              <div v-if="result.generate" class="mb-bg-gray-900 mb-border mb-border-gray-700 mb-rounded-lg mb-p-4">
                <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-500 mb-mb-2">{{ t('Risultato:') }}</label>
                <div class="mb-text-sm mb-text-gray-200 mb-whitespace-pre-wrap mb-leading-relaxed">{{ result.generate }}</div>
                <div class="mb-flex mb-gap-2 mb-mt-3">
                  <button
                    @click="insertGeneratedText(result.generate)"
                    class="mb-px-3 mb-py-1.5 mb-bg-green-600 mb-text-white mb-text-xs mb-font-medium mb-rounded-lg hover:mb-bg-green-500 mb-transition-colors"
                  >
                    {{ t('Inserisci nella tile') }}
                  </button>
                  <button
                    @click="copyToClipboard(result.generate)"
                    class="mb-px-3 mb-py-1.5 mb-bg-gray-700 mb-text-gray-300 mb-text-xs mb-rounded-lg hover:mb-bg-gray-600 mb-transition-colors"
                  >
                    {{ t('Copia') }}
                  </button>
                </div>
              </div>
            </div>

            <!-- ====== TAB: Migliora Testo ====== -->
            <div v-if="activeTab === 'improve'" class="mb-space-y-4">
              <div>
                <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">{{ t('Testo da migliorare') }}</label>
                <textarea
                  v-model="improve.text"
                  rows="4"
                  class="mb-w-full mb-bg-gray-700 mb-border mb-border-gray-600 mb-rounded-lg mb-px-3 mb-py-2 mb-text-sm mb-text-gray-200 mb-outline-none focus:mb-border-purple-500 mb-resize-none"
                  :placeholder="t('Incolla qui il testo da migliorare...')"
                ></textarea>
              </div>

              <div>
                <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-2">{{ t('Azione') }}</label>
                <div class="mb-flex mb-flex-wrap mb-gap-2">
                  <button
                    v-for="action in improveActions"
                    :key="action.id"
                    @click="improveText(action.id)"
                    :disabled="loading.improve || !improve.text.trim()"
                    :class="[
                      'mb-px-3 mb-py-1.5 mb-text-xs mb-font-medium mb-rounded-lg mb-border mb-transition-colors',
                      loading.improve
                        ? 'mb-opacity-50 mb-cursor-not-allowed mb-border-gray-600 mb-text-gray-500'
                        : 'mb-border-gray-600 mb-text-gray-300 hover:mb-bg-gray-700 hover:mb-border-purple-500'
                    ]"
                  >
                    {{ action.label }}
                  </button>
                </div>
              </div>

              <div v-if="loading.improve" class="mb-flex mb-items-center mb-gap-2 mb-text-sm mb-text-gray-400">
                <svg class="mb-animate-spin" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                Elaborazione in corso...
              </div>

              <!-- Risultato -->
              <div v-if="result.improve" class="mb-bg-gray-900 mb-border mb-border-gray-700 mb-rounded-lg mb-p-4">
                <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-500 mb-mb-2">{{ t('Risultato:') }}</label>
                <div class="mb-text-sm mb-text-gray-200 mb-whitespace-pre-wrap mb-leading-relaxed">{{ result.improve }}</div>
                <div class="mb-flex mb-gap-2 mb-mt-3">
                  <button
                    @click="insertGeneratedText(result.improve)"
                    class="mb-px-3 mb-py-1.5 mb-bg-green-600 mb-text-white mb-text-xs mb-font-medium mb-rounded-lg hover:mb-bg-green-500 mb-transition-colors"
                  >
                    {{ t('Applica alla tile') }}
                  </button>
                  <button
                    @click="copyToClipboard(result.improve)"
                    class="mb-px-3 mb-py-1.5 mb-bg-gray-700 mb-text-gray-300 mb-text-xs mb-rounded-lg hover:mb-bg-gray-600 mb-transition-colors"
                  >
                    {{ t('Copia') }}
                  </button>
                </div>
              </div>
            </div>

            <!-- ====== TAB: Genera Immagine ====== -->
            <div v-if="activeTab === 'image'" class="mb-space-y-4">
              <p class="mb-text-[10px] mb-text-amber-400/80 mb-m-0 mb-mb-1">{{ t('Richiede chiave OpenAI separata (configurabile nelle impostazioni AI)') }}</p>
              <div>
                <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">{{ t('Descrivi l\'immagine da generare...') }}</label>
                <textarea
                  v-model="img.prompt"
                  rows="3"
                  class="mb-w-full mb-bg-gray-700 mb-border mb-border-gray-600 mb-rounded-lg mb-px-3 mb-py-2 mb-text-sm mb-text-gray-200 mb-outline-none focus:mb-border-purple-500 mb-resize-none"
                  :placeholder="t('Es: Un paesaggio montano al tramonto con un lago cristallino...')"
                ></textarea>
              </div>

              <div class="mb-grid mb-grid-cols-2 mb-gap-3">
                <div>
                  <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">{{ t('Dimensione') }}</label>
                  <FieldSelect ui="dropdown" :model-value="img.size" :options="IMG_SIZE_OPTS" @update:model-value="img.size = $event" />
                </div>
                <div>
                  <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">{{ t('Stile') }}</label>
                  <FieldSelect ui="dropdown" :model-value="img.style" :options="IMG_STYLE_OPTS" @update:model-value="img.style = $event" />
                </div>
              </div>

              <button
                @click="generateImage"
                :disabled="loading.image || !img.prompt.trim()"
                class="mb-px-4 mb-py-2 mb-bg-purple-600 mb-text-white mb-text-sm mb-font-medium mb-rounded-lg hover:mb-bg-purple-500 disabled:mb-opacity-50 disabled:mb-cursor-not-allowed mb-transition-colors"
              >
                {{ loading.image ? 'Generazione in corso...' : 'Genera immagine' }}
              </button>

              <div v-if="loading.image" class="mb-flex mb-items-center mb-gap-2 mb-text-sm mb-text-gray-400">
                <svg class="mb-animate-spin" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                L'immagine potrebbe richiedere fino a 60 secondi...
              </div>

              <!-- Risultato immagine -->
              <div v-if="result.image" class="mb-bg-gray-900 mb-border mb-border-gray-700 mb-rounded-lg mb-p-4">
                <img
                  :src="result.image.url"
                  :alt="t('Immagine generata dall\'AI')"
                  class="mb-w-full mb-rounded-lg mb-mb-3"
                  style="max-height:400px;object-fit:contain"
                />
                <p class="mb-text-xs mb-text-gray-500 mb-m-0 mb-mb-2">Salvata nella Media Library (ID: {{ result.image.attachment_id }})</p>
                <div class="mb-flex mb-gap-2">
                  <button
                    @click="insertImageInTile(result.image)"
                    class="mb-px-3 mb-py-1.5 mb-bg-green-600 mb-text-white mb-text-xs mb-font-medium mb-rounded-lg hover:mb-bg-green-500 mb-transition-colors"
                  >
                    {{ t('Inserisci nella tile') }}
                  </button>
                  <button
                    @click="copyToClipboard(result.image.url)"
                    class="mb-px-3 mb-py-1.5 mb-bg-gray-700 mb-text-gray-300 mb-text-xs mb-rounded-lg hover:mb-bg-gray-600 mb-transition-colors"
                  >
                    {{ t('Copia URL') }}
                  </button>
                </div>
              </div>
            </div>

            <!-- ====== TAB: Traduci ====== -->
            <div v-if="activeTab === 'translate'" class="mb-space-y-4">
              <div>
                <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">{{ t('Testo da tradurre') }}</label>
                <textarea
                  v-model="translate.text"
                  rows="4"
                  class="mb-w-full mb-bg-gray-700 mb-border mb-border-gray-600 mb-rounded-lg mb-px-3 mb-py-2 mb-text-sm mb-text-gray-200 mb-outline-none focus:mb-border-purple-500 mb-resize-none"
                  :placeholder="t('Incolla il testo da tradurre...')"
                ></textarea>
              </div>

              <div>
                <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">{{ t('Lingua di destinazione') }}</label>
                <FieldSelect ui="dropdown" :model-value="translate.targetLanguage" :options="LANGUAGE_OPTS" @update:model-value="translate.targetLanguage = $event" />
              </div>

              <button
                @click="translateText"
                :disabled="loading.translate || !translate.text.trim()"
                class="mb-px-4 mb-py-2 mb-bg-purple-600 mb-text-white mb-text-sm mb-font-medium mb-rounded-lg hover:mb-bg-purple-500 disabled:mb-opacity-50 disabled:mb-cursor-not-allowed mb-transition-colors"
              >
                {{ loading.translate ? 'Traduzione...' : 'Traduci' }}
              </button>

              <!-- Risultato -->
              <div v-if="result.translate" class="mb-bg-gray-900 mb-border mb-border-gray-700 mb-rounded-lg mb-p-4">
                <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-500 mb-mb-2">{{ t('Traduzione:') }}</label>
                <div class="mb-text-sm mb-text-gray-200 mb-whitespace-pre-wrap mb-leading-relaxed">{{ result.translate }}</div>
                <div class="mb-flex mb-gap-2 mb-mt-3">
                  <button
                    @click="insertGeneratedText(result.translate)"
                    class="mb-px-3 mb-py-1.5 mb-bg-green-600 mb-text-white mb-text-xs mb-font-medium mb-rounded-lg hover:mb-bg-green-500 mb-transition-colors"
                  >
                    {{ t('Applica alla tile') }}
                  </button>
                  <button
                    @click="copyToClipboard(result.translate)"
                    class="mb-px-3 mb-py-1.5 mb-bg-gray-700 mb-text-gray-300 mb-text-xs mb-rounded-lg hover:mb-bg-gray-600 mb-transition-colors"
                  >
                    {{ t('Copia') }}
                  </button>
                </div>
              </div>
            </div>

            <!-- ====== TAB: Genera Layout ====== -->
            <div v-if="activeTab === 'layout'" class="mb-space-y-4">
              <div>
                <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">{{ t('Descrivi il layout da generare...') }}</label>
                <textarea
                  v-model="layout.prompt"
                  rows="3"
                  class="mb-w-full mb-bg-gray-700 mb-border mb-border-gray-600 mb-rounded-lg mb-px-3 mb-py-2 mb-text-sm mb-text-gray-200 mb-outline-none focus:mb-border-purple-500 mb-resize-none"
                  :placeholder="t('Es: Sezione hero con titolo grande, sottotitolo e due pulsanti CTA affiancati...')"
                ></textarea>
              </div>

              <div class="mb-grid mb-grid-cols-2 mb-gap-3">
                <div>
                  <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">{{ t('Stile') }}</label>
                  <FieldSelect ui="dropdown" :model-value="layout.style" :options="LAYOUT_STYLE_OPTS" @update:model-value="layout.style = $event" />
                </div>
                <div>
                  <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">{{ t('Colonne') }}</label>
                  <FieldSelect ui="dropdown" :model-value="layout.columns" :options="LAYOUT_COLUMNS_OPTS" @update:model-value="layout.columns = $event" />
                </div>
              </div>

              <button
                @click="generateLayout"
                :disabled="loading.layout || !layout.prompt.trim()"
                class="mb-px-4 mb-py-2 mb-bg-purple-600 mb-text-white mb-text-sm mb-font-medium mb-rounded-lg hover:mb-bg-purple-500 disabled:mb-opacity-50 disabled:mb-cursor-not-allowed mb-transition-colors"
              >
                {{ loading.layout ? 'Generazione layout...' : 'Genera Layout' }}
              </button>

              <div v-if="loading.layout" class="mb-flex mb-items-center mb-gap-2 mb-text-sm mb-text-gray-400">
                <svg class="mb-animate-spin" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                Generazione struttura in corso...
              </div>

              <div v-if="result.layout" class="mb-bg-gray-900 mb-border mb-border-gray-700 mb-rounded-lg mb-p-4">
                <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-500 mb-mb-2">{{ t('Struttura generata:') }}</label>
                <div class="mb-text-xs mb-text-gray-300 mb-font-mono mb-whitespace-pre-wrap mb-max-h-48 mb-overflow-y-auto mb-mb-3">{{ JSON.stringify(result.layout.structure, null, 2) }}</div>
                <div class="mb-flex mb-gap-2">
                  <button
                    @click="insertLayout(result.layout.structure)"
                    class="mb-px-3 mb-py-1.5 mb-bg-green-600 mb-text-white mb-text-xs mb-font-medium mb-rounded-lg hover:mb-bg-green-500 mb-transition-colors"
                  >
                    {{ t('Inserisci nel builder') }}
                  </button>
                  <button
                    @click="copyToClipboard(JSON.stringify(result.layout.structure, null, 2))"
                    class="mb-px-3 mb-py-1.5 mb-bg-gray-700 mb-text-gray-300 mb-text-xs mb-rounded-lg hover:mb-bg-gray-600 mb-transition-colors"
                  >
                    {{ t('Copia JSON') }}
                  </button>
                </div>
              </div>
            </div>

            <!-- ====== TAB: Suggerisci Stile ====== -->
            <div v-if="activeTab === 'style'" class="mb-space-y-4">
              <p class="mb-text-xs mb-text-gray-400 mb-m-0">{{ t('Analizza i colori e font attuali del sito e suggerisce palette alternative armoniche.') }}</p>

              <div>
                <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">{{ t('Tipo di palette') }}</label>
                <FieldSelect ui="dropdown" :model-value="styleSuggest.palette" :options="PALETTE_OPTS" @update:model-value="styleSuggest.palette = $event" />
              </div>

              <button
                @click="suggestStyle"
                :disabled="loading.style"
                class="mb-px-4 mb-py-2 mb-bg-purple-600 mb-text-white mb-text-sm mb-font-medium mb-rounded-lg hover:mb-bg-purple-500 disabled:mb-opacity-50 disabled:mb-cursor-not-allowed mb-transition-colors"
              >
                {{ loading.style ? 'Analisi in corso...' : 'Suggerisci Stile' }}
              </button>

              <div v-if="result.style" class="mb-space-y-3">
                <div v-for="(suggestion, idx) in result.style.suggestions" :key="idx" class="mb-bg-gray-900 mb-border mb-border-gray-700 mb-rounded-lg mb-p-4">
                  <p class="mb-text-xs mb-font-semibold mb-text-gray-300 mb-m-0 mb-mb-2">{{ suggestion.name }}</p>
                  <div class="mb-flex mb-gap-2 mb-mb-2">
                    <div
                      v-for="(color, ci) in suggestion.colors"
                      :key="ci"
                      class="mb-w-10 mb-h-10 mb-rounded-lg mb-border mb-border-gray-600"
                      :style="{ backgroundColor: color }"
                      :title="color"
                    ></div>
                  </div>
                  <p v-if="suggestion.fonts" class="mb-text-xs mb-text-gray-500 mb-m-0">Font: {{ suggestion.fonts.join(', ') }}</p>
                  <button
                    @click="applyStyleSuggestion(suggestion)"
                    class="mb-mt-2 mb-px-3 mb-py-1.5 mb-bg-purple-600 mb-text-white mb-text-xs mb-font-medium mb-rounded-lg hover:mb-bg-purple-500 mb-transition-colors"
                  >
                    {{ t('Applica palette') }}
                  </button>
                </div>
              </div>
            </div>

            <!-- ====== TAB: Alt Text SEO ====== -->
            <div v-if="activeTab === 'alt'" class="mb-space-y-4">
              <p class="mb-text-xs mb-text-gray-400 mb-m-0">{{ t('Genera alt text SEO-friendly per le immagini. Inserisci l\'URL di un\'immagine o seleziona una tile immagine.') }}</p>

              <div>
                <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">{{ t('URL immagine') }}</label>
                <div class="mb-flex mb-gap-2">
                  <input
                    v-model="alt.imageUrl"
                    type="text"
                    class="mb-flex-1 mb-bg-gray-700 mb-border mb-border-gray-600 mb-rounded-lg mb-px-3 mb-py-2 mb-text-sm mb-text-gray-200 mb-outline-none focus:mb-border-purple-500"
                    placeholder="https://example.com/image.jpg"
                  />
                  <button
                    @click="fillImageUrlFromTile"
                    class="mb-px-3 mb-py-2 mb-bg-gray-700 mb-text-gray-300 mb-text-xs mb-rounded-lg hover:mb-bg-gray-600 mb-border mb-border-gray-600 mb-transition-colors mb-whitespace-nowrap"
                  >
                    {{ t('Da tile') }}
                  </button>
                </div>
              </div>

              <div>
                <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">{{ t('Lingua') }}</label>
                <FieldSelect ui="dropdown" :model-value="alt.language" :options="LANGUAGE_OPTS" @update:model-value="alt.language = $event" />
              </div>

              <button
                @click="generateAltText"
                :disabled="loading.alt || !alt.imageUrl.trim()"
                class="mb-px-4 mb-py-2 mb-bg-purple-600 mb-text-white mb-text-sm mb-font-medium mb-rounded-lg hover:mb-bg-purple-500 disabled:mb-opacity-50 disabled:mb-cursor-not-allowed mb-transition-colors"
              >
                {{ loading.alt ? 'Analisi immagine...' : 'Genera Alt Text' }}
              </button>

              <div v-if="result.alt" class="mb-bg-gray-900 mb-border mb-border-gray-700 mb-rounded-lg mb-p-4">
                <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-500 mb-mb-2">{{ t('Alt text generato:') }}</label>
                <div class="mb-text-sm mb-text-gray-200 mb-leading-relaxed">{{ result.alt }}</div>
                <div class="mb-flex mb-gap-2 mb-mt-3">
                  <button
                    @click="applyAltText(result.alt)"
                    class="mb-px-3 mb-py-1.5 mb-bg-green-600 mb-text-white mb-text-xs mb-font-medium mb-rounded-lg hover:mb-bg-green-500 mb-transition-colors"
                  >
                    {{ t('Applica alla tile') }}
                  </button>
                  <button
                    @click="copyToClipboard(result.alt)"
                    class="mb-px-3 mb-py-1.5 mb-bg-gray-700 mb-text-gray-300 mb-text-xs mb-rounded-lg hover:mb-bg-gray-600 mb-transition-colors"
                  >
                    {{ t('Copia') }}
                  </button>
                </div>
              </div>
            </div>

            <!-- ====== TAB: Genera CSS ====== -->
            <div v-if="activeTab === 'css'" class="mb-space-y-4">
              <div>
                <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">{{ t('Descrivi lo stile CSS che vuoi generare...') }}</label>
                <textarea
                  v-model="css.prompt"
                  rows="3"
                  class="mb-w-full mb-bg-gray-700 mb-border mb-border-gray-600 mb-rounded-lg mb-px-3 mb-py-2 mb-text-sm mb-text-gray-200 mb-outline-none focus:mb-border-purple-500 mb-resize-none"
                  :placeholder="t('Es: Effetto glassmorphism con sfondo sfocato e bordo semi-trasparente...')"
                ></textarea>
              </div>

              <div>
                <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">{{ t('Selettore CSS (opzionale)') }}</label>
                <input
                  v-model="css.selector"
                  type="text"
                  class="mb-w-full mb-bg-gray-700 mb-border mb-border-gray-600 mb-rounded-lg mb-px-3 mb-py-2 mb-text-sm mb-text-gray-200 mb-outline-none focus:mb-border-purple-500"
                  :placeholder="t('.mia-classe oppure lascia vuoto per stile generico')"
                />
              </div>

              <button
                @click="generateCSS"
                :disabled="loading.css || !css.prompt.trim()"
                class="mb-px-4 mb-py-2 mb-bg-purple-600 mb-text-white mb-text-sm mb-font-medium mb-rounded-lg hover:mb-bg-purple-500 disabled:mb-opacity-50 disabled:mb-cursor-not-allowed mb-transition-colors"
              >
                {{ loading.css ? 'Generazione CSS...' : 'Genera CSS' }}
              </button>

              <div v-if="result.css" class="mb-bg-gray-900 mb-border mb-border-gray-700 mb-rounded-lg mb-p-4">
                <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-500 mb-mb-2">{{ t('CSS generato:') }}</label>
                <pre class="mb-text-xs mb-text-green-300 mb-font-mono mb-whitespace-pre-wrap mb-bg-gray-950 mb-rounded-lg mb-p-3 mb-m-0 mb-max-h-48 mb-overflow-y-auto">{{ result.css }}</pre>
                <div class="mb-flex mb-gap-2 mb-mt-3">
                  <button
                    @click="applyCSSToTile(result.css)"
                    class="mb-px-3 mb-py-1.5 mb-bg-green-600 mb-text-white mb-text-xs mb-font-medium mb-rounded-lg hover:mb-bg-green-500 mb-transition-colors"
                  >
                    {{ t('Applica alla tile') }}
                  </button>
                  <button
                    @click="copyToClipboard(result.css)"
                    class="mb-px-3 mb-py-1.5 mb-bg-gray-700 mb-text-gray-300 mb-text-xs mb-rounded-lg hover:mb-bg-gray-600 mb-transition-colors"
                  >
                    {{ t('Copia') }}
                  </button>
                </div>
              </div>
            </div>

          </div>

          <!-- Footer: errore + history -->
          <div v-if="error" class="mb-px-5 mb-py-2 mb-bg-red-900/30 mb-border-t mb-border-red-800/50">
            <p class="mb-text-xs mb-text-red-400 mb-m-0">{{ error }}</p>
          </div>

          <div v-if="history.length > 0" class="mb-px-5 mb-py-2 mb-border-t mb-border-gray-700 mb-bg-gray-900/50">
            <button
              @click="showHistory = !showHistory"
              class="mb-text-xs mb-text-gray-500 hover:mb-text-gray-300 mb-transition-colors"
            >
              Cronologia ({{ history.length }}) {{ showHistory ? '▲' : '▼' }}
            </button>
            <div v-if="showHistory" class="mb-mt-2 mb-space-y-1 mb-max-h-32 mb-overflow-y-auto">
              <div
                v-for="(item, idx) in history"
                :key="idx"
                class="mb-text-xs mb-text-gray-400 mb-bg-gray-800 mb-rounded mb-px-2 mb-py-1 mb-cursor-pointer hover:mb-bg-gray-700 mb-truncate"
                :title="item.text"
                @click="applyHistoryItem(item)"
              >
                <span class="mb-text-gray-600">[{{ item.type }}]</span> {{ item.text.slice(0, 80) }}...
              </div>
            </div>
          </div>
        </div>
      </div>
    </transition>
</template>

<script setup>
import { ref, reactive, onMounted, onBeforeUnmount, watch, nextTick } from 'vue';
import { useFocusTrap } from '@/composables/useFocusTrap';
import { useBuilderStore } from '@/stores/builder';
import { useTilesStore } from '@/stores/tiles';
import { useToast } from '@/composables/useToast.js';
import { t } from '@/i18n';
import FieldSelect from './fields/FieldSelect.vue';

const builderStore = useBuilderStore();
const tilesStore = useTilesStore();
const toast = useToast();

const visible = ref(false);
const activeTab = ref('generate');
const error = ref('');
const showHistory = ref(false);
const history = reactive([]);

const tabs = [
  { id: 'generate', label: 'Genera Testo' },
  { id: 'improve', label: 'Migliora' },
  { id: 'image', label: 'Immagine' },
  { id: 'translate', label: 'Traduci' },
  { id: 'layout', label: 'Layout' },
  { id: 'style', label: 'Stile' },
  { id: 'alt', label: 'Alt Text' },
  { id: 'css', label: 'CSS' },
];

// Opzioni dei FieldSelect (label RAW: t() la applica FieldSelect internamente)
const GEN_TYPE_OPTS = [
  { value: 'headline', label: 'Titolo' },
  { value: 'paragraph', label: 'Paragrafo' },
  { value: 'list', label: 'Lista' },
  { value: 'cta', label: 'Call to Action' },
  { value: 'seo_description', label: 'Meta Description SEO' },
];

const GEN_TONE_OPTS = [
  { value: 'professionale', label: 'Professionale' },
  { value: 'creativo', label: 'Creativo' },
  { value: 'informale', label: 'Informale' },
  { value: 'formale', label: 'Formale' },
];

const LANGUAGE_OPTS = [
  { value: 'it', label: 'Italiano' },
  { value: 'en', label: 'English' },
  { value: 'de', label: 'Deutsch' },
  { value: 'fr', label: 'Français' },
  { value: 'es', label: 'Español' },
];

const IMG_SIZE_OPTS = [
  { value: '1024x1024', label: 'Quadrata (1024x1024)' },
  { value: '1792x1024', label: 'Orizzontale (1792x1024)' },
  { value: '1024x1792', label: 'Verticale (1024x1792)' },
];

const IMG_STYLE_OPTS = [
  { value: 'vivid', label: 'Vivido' },
  { value: 'natural', label: 'Naturale' },
];

const LAYOUT_STYLE_OPTS = [
  { value: 'corporate', label: 'Corporate' },
  { value: 'creative', label: 'Creativo' },
  { value: 'minimal', label: 'Minimale' },
  { value: 'bold', label: 'Bold / Impattante' },
];

const LAYOUT_COLUMNS_OPTS = [
  { value: '1', label: '1 colonna' },
  { value: '2', label: '2 colonne' },
  { value: '3', label: '3 colonne' },
  { value: '4', label: '4 colonne' },
];

const PALETTE_OPTS = [
  { value: 'auto', label: 'Automatica (analisi colori attuali)' },
  { value: 'warm', label: 'Toni caldi' },
  { value: 'cool', label: 'Toni freddi' },
  { value: 'pastel', label: 'Pastello' },
  { value: 'dark', label: 'Dark mode' },
  { value: 'vibrant', label: 'Vivace / Bold' },
];

const improveActions = [
  { id: 'rephrase', label: 'Riformula' },
  { id: 'shorten', label: 'Accorcia' },
  { id: 'expand', label: 'Espandi' },
  { id: 'fix_grammar', label: 'Correggi grammatica' },
  { id: 'make_professional', label: 'Professionale' },
  { id: 'make_casual', label: 'Informale' },
];

// Form state
const gen = reactive({
  type: 'paragraph',
  tone: 'professionale',
  language: 'it',
  prompt: '',
  maxLength: 150,
});

const improve = reactive({
  text: '',
});

const img = reactive({
  prompt: '',
  size: '1024x1024',
  style: 'vivid',
});

const translate = reactive({
  text: '',
  targetLanguage: 'en',
});

const layout = reactive({
  prompt: '',
  style: 'corporate',
  columns: '2',
});

const styleSuggest = reactive({
  palette: 'auto',
});

const alt = reactive({
  imageUrl: '',
  language: 'it',
});

const css = reactive({
  prompt: '',
  selector: '',
});

// Loading + results
const loading = reactive({
  generate: false,
  improve: false,
  image: false,
  translate: false,
  layout: false,
  style: false,
  alt: false,
  css: false,
});

const result = reactive({
  generate: '',
  improve: '',
  image: null,
  translate: '',
  layout: null,
  style: null,
  alt: '',
  css: '',
});

// ─── REST API helper ───
function getOloData() {
  return window.oloData || {};
}

async function apiCall(endpoint, body) {
  const olo = getOloData();
  error.value = '';

  const res = await fetch(olo.restUrl + '/ai/' + endpoint, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-WP-Nonce': olo.nonce,
    },
    body: JSON.stringify(body),
  });

  const data = await res.json();

  if (!res.ok) {
    const msg = data.message || data.error || 'Errore sconosciuto';
    throw new Error(msg);
  }

  return data;
}

// ─── Actions ───

async function generateText() {
  loading.generate = true;
  error.value = '';
  try {
    const data = await apiCall('generate-text', {
      prompt: gen.prompt,
      type: gen.type,
      tone: gen.tone,
      language: gen.language,
      max_length: gen.maxLength,
    });
    result.generate = data.text;
    addToHistory('genera', data.text);
  } catch (e) {
    error.value = e.message;
  } finally {
    loading.generate = false;
  }
}

async function improveText(action) {
  loading.improve = true;
  error.value = '';
  try {
    const data = await apiCall('improve-text', {
      text: improve.text,
      action: action,
    });
    result.improve = data.text;
    addToHistory('migliora', data.text);
  } catch (e) {
    error.value = e.message;
  } finally {
    loading.improve = false;
  }
}

async function generateImage() {
  loading.image = true;
  error.value = '';
  result.image = null;
  try {
    const data = await apiCall('generate-image', {
      prompt: img.prompt,
      size: img.size,
      style: img.style,
    });
    result.image = { url: data.url, attachment_id: data.attachment_id };
    addToHistory('immagine', data.url);
  } catch (e) {
    error.value = e.message;
  } finally {
    loading.image = false;
  }
}

async function translateText() {
  loading.translate = true;
  error.value = '';
  try {
    const data = await apiCall('translate-text', {
      text: translate.text,
      target_language: translate.targetLanguage,
    });
    result.translate = data.text;
    addToHistory('traduzione', data.text);
  } catch (e) {
    error.value = e.message;
  } finally {
    loading.translate = false;
  }
}

// ─── Layout Generation ───

async function generateLayout() {
  loading.layout = true;
  error.value = '';
  result.layout = null;
  try {
    const data = await apiCall('generate-layout', {
      prompt: layout.prompt,
      style: layout.style,
      columns: layout.columns,
    });
    result.layout = { structure: data.structure };
    addToHistory('layout', JSON.stringify(data.structure).slice(0, 100));
  } catch (e) {
    error.value = e.message;
  } finally {
    loading.layout = false;
  }
}

function insertLayout(structure) {
  if (!structure || !Array.isArray(structure)) {
    toast.error(t('Struttura layout non valida.'));
    return;
  }
  // Importa la struttura nel builder come nuove tile a livello root
  try {
    const assignIds = (nodes) => {
      return nodes.map(node => {
        const id = 'tile_' + Date.now() + '_' + Math.random().toString(36).substr(2, 6);
        const tile = { ...node, id };
        if (tile.children && Array.isArray(tile.children)) {
          tile.children = assignIds(tile.children);
        }
        return tile;
      });
    };
    const tilesWithIds = assignIds(structure);
    tilesWithIds.forEach(tile => tilesStore.addTile(tile));
    builderStore.isDirty = true;
    toast.success(t('Layout inserito nel builder'));
  } catch (e) {
    toast.error(t('Errore nell\'inserimento') + ': ' + e.message);
  }
}

// ─── Style Suggestions ───

async function suggestStyle() {
  loading.style = true;
  error.value = '';
  result.style = null;
  try {
    // Raccoglie i colori attuali dal CSS del sito
    const currentColors = collectCurrentColors();
    const data = await apiCall('suggest-style', {
      palette: styleSuggest.palette,
      current_colors: currentColors,
    });
    result.style = { suggestions: data.suggestions };
  } catch (e) {
    error.value = e.message;
  } finally {
    loading.style = false;
  }
}

function collectCurrentColors() {
  // Prova a leggere i colori globali dallo store
  const styles = builderStore.globalStyles || {};
  const colors = [];
  if (styles.primary_color) colors.push(styles.primary_color);
  if (styles.secondary_color) colors.push(styles.secondary_color);
  if (styles.text_color) colors.push(styles.text_color);
  if (styles.background_color) colors.push(styles.background_color);
  return colors;
}

function applyStyleSuggestion(suggestion) {
  if (!suggestion || !suggestion.colors) return;
  // Copia i colori suggeriti negli appunti come CSS custom properties
  const colors = suggestion.colors;
  const lines = [];
  if (colors[0]) lines.push('--olo-color-primary: ' + colors[0] + ';');
  if (colors[1]) lines.push('--olo-color-secondary: ' + colors[1] + ';');
  if (colors[2]) lines.push('--olo-color-text: ' + colors[2] + ';');
  if (colors[3]) lines.push('--olo-color-background: ' + colors[3] + ';');
  if (colors[4]) lines.push('--olo-color-accent: ' + colors[4] + ';');
  const cssVars = ':root {\n  ' + lines.join('\n  ') + '\n}';
  copyToClipboard(cssVars);
  toast.success(t('Palette copiata come CSS custom properties. Incollala nel Custom CSS del sito.'));
}

// ─── Alt Text SEO ───

function fillImageUrlFromTile() {
  const tileId = builderStore.selectedTileId;
  if (!tileId) {
    toast.error(t('Seleziona una tile immagine.'));
    return;
  }
  const tile = tilesStore.getTileById(tileId);
  if (!tile) return;
  const imgFields = ['image_url', 'src', 'url', 'image', 'background_image'];
  for (const f of imgFields) {
    if (tile.settings && tile.settings[f]) {
      alt.imageUrl = tile.settings[f];
      return;
    }
  }
  toast.error(t('Nessun campo immagine trovato nella tile selezionata.'));
}

async function generateAltText() {
  loading.alt = true;
  error.value = '';
  result.alt = '';
  try {
    const data = await apiCall('generate-alt', {
      image_url: alt.imageUrl,
      language: alt.language,
    });
    result.alt = data.text;
    addToHistory('alt-text', data.text);
  } catch (e) {
    error.value = e.message;
  } finally {
    loading.alt = false;
  }
}

function applyAltText(text) {
  const tileId = builderStore.selectedTileId;
  if (!tileId) {
    toast.error(t('Seleziona una tile immagine.'));
    return;
  }
  const tile = tilesStore.getTileById(tileId);
  if (!tile) return;
  const altFields = ['alt', 'alt_text', 'image_alt'];
  let field = 'alt';
  for (const f of altFields) {
    if (tile.settings && typeof tile.settings[f] !== 'undefined') {
      field = f;
      break;
    }
  }
  tilesStore.updateTile(tileId, { [field]: text });
  builderStore.isDirty = true;
  toast.success(t('Alt text applicato alla tile'));
}

// ─── Generate CSS ───

async function generateCSS() {
  loading.css = true;
  error.value = '';
  result.css = '';
  try {
    const data = await apiCall('generate-css', {
      prompt: css.prompt,
      selector: css.selector,
    });
    result.css = data.css;
    addToHistory('css', data.css.slice(0, 80));
  } catch (e) {
    error.value = e.message;
  } finally {
    loading.css = false;
  }
}

function applyCSSToTile(cssCode) {
  const tileId = builderStore.selectedTileId;
  if (!tileId) {
    toast.error(t('Seleziona una tile per applicare il CSS personalizzato.'));
    return;
  }
  const tile = tilesStore.getTileById(tileId);
  if (!tile) return;
  // Scrive il CSS nel campo custom_css della tile
  const existing = (tile.advanced && tile.advanced.custom_css) || '';
  const newCss = existing ? existing + '\n\n' + cssCode : cssCode;
  tilesStore.updateTileAdvanced(tileId, { custom_css: newCss });
  builderStore.isDirty = true;
  toast.success(t('CSS applicato alla tile'));
}

// ─── Insert into tile ───

function getSelectedTileTextField() {
  // Trova il primo campo testo della tile selezionata
  const tileId = builderStore.selectedTileId;
  if (!tileId) return null;
  const tile = tilesStore.getTileById(tileId);
  if (!tile) return null;

  // Campi testo comuni nelle tile
  const textFields = ['content', 'text', 'title', 'headline', 'description', 'subtitle', 'label', 'caption'];
  for (const field of textFields) {
    if (tile.settings && typeof tile.settings[field] !== 'undefined') {
      return field;
    }
  }
  // Se non trova nulla, prova comunque 'content'
  return 'content';
}

function insertGeneratedText(text) {
  const tileId = builderStore.selectedTileId;
  if (!tileId) {
    toast.error(t('Nessuna tile selezionata. Seleziona una tile prima di inserire il testo.'));
    return;
  }
  const field = getSelectedTileTextField();
  tilesStore.updateTile(tileId, { [field]: text });
  builderStore.isDirty = true;
  toast.success(t('Testo inserito nella tile'));
}

function insertImageInTile(imageData) {
  const tileId = builderStore.selectedTileId;
  if (!tileId) {
    toast.error(t('Nessuna tile selezionata. Seleziona una tile immagine prima di inserire.'));
    return;
  }
  const tile = tilesStore.getTileById(tileId);
  if (!tile) return;

  // Cerca il campo immagine appropriato
  const imgFields = ['image_url', 'src', 'url', 'image', 'background_image'];
  let field = 'image_url';
  for (const f of imgFields) {
    if (tile.settings && typeof tile.settings[f] !== 'undefined') {
      field = f;
      break;
    }
  }

  const updates = { [field]: imageData.url };
  // Se esiste un campo _id corrispondente, aggiorna anche quello
  if (imageData.attachment_id) {
    updates[field + '_id'] = imageData.attachment_id;
  }

  tilesStore.updateTile(tileId, updates);
  builderStore.isDirty = true;
  toast.success(t('Immagine inserita nella tile'));
}

// ─── History ───

function addToHistory(type, text) {
  history.unshift({ type, text, timestamp: Date.now() });
  if (history.length > 10) {
    history.pop();
  }
}

function applyHistoryItem(item) {
  if (item.type === 'immagine') {
    copyToClipboard(item.text);
  } else {
    insertGeneratedText(item.text);
  }
}

// ─── Clipboard ───

async function copyToClipboard(text) {
  try {
    await navigator.clipboard.writeText(text);
    toast.success(t('Copiato negli appunti'));
  } catch (e) {
    // Fallback
    const ta = document.createElement('textarea');
    ta.value = text;
    ta.style.position = 'fixed';
    ta.style.left = '-9999px';
    document.body.appendChild(ta);
    ta.select();
    document.execCommand('copy');
    document.body.removeChild(ta);
    toast.success(t('Copiato negli appunti'));
  }
}

// ─── Open / Close ───

function open() {
  visible.value = true;
  error.value = '';

  // Pre-popola il tab "Migliora Testo" col contenuto della tile selezionata
  const tileId = builderStore.selectedTileId;
  if (tileId) {
    const tile = tilesStore.getTileById(tileId);
    if (tile) {
      const field = getSelectedTileTextField();
      if (field) {
        const val = tile.settings ? tile.settings[field] : '';
        if (val) {
          if (typeof val === 'string') {
            improve.text = val;
            translate.text = val;
          }
        }
      }
    }
  }
}

function close() {
  visible.value = false;
}

const dialogRef = ref(null);
const aiTrap = useFocusTrap(dialogRef, { onEscape: close });
watch(visible, (v) => { if (v) { nextTick(() => aiTrap.activate()); } else { aiTrap.deactivate(); } });

// Keyboard shortcut: Ctrl+Shift+A (solo se chiave API configurata)
const hasAiKey = !!(window.oloData && window.oloData.hasAiKey);

function onKeydown(e) {
  if (!hasAiKey) return;
  if (e.ctrlKey && e.shiftKey && e.key === 'A') {
    e.preventDefault();
    if (visible.value) {
      close();
    } else {
      open();
    }
  }
  // Esc chiude il dialog
  if (e.key === 'Escape' && visible.value) {
    close();
  }
}

onMounted(() => {
  if (hasAiKey) document.addEventListener('keydown', onKeydown);
});

onBeforeUnmount(() => {
  document.removeEventListener('keydown', onKeydown);
});

// Expose per uso da toolbar
defineExpose({ open, close, visible });
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.15s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
