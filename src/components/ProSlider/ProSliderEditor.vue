<template>
  <Teleport to="body">
    <div class="mps-modal-overlay" style="position:fixed;top:0;left:0;width:100vw;height:100vh;z-index:999999;background:#111827;display:flex;flex-direction:column;outline:none;" @keydown="onKeydown" tabindex="-1" ref="modalEl">
      <!-- Header bar -->
      <div class="mb-flex mb-items-center mb-justify-between mb-bg-gray-800 mb-border-b mb-border-gray-700 mb-px-4 mb-py-2 mb-shrink-0">
        <h2 class="mb-text-sm mb-font-bold mb-text-gray-200">{{ t('Editor dello Slider Pro') }}</h2>
        <div class="mb-flex mb-items-center mb-gap-2">
          <button @click="save" class="mb-px-4 mb-py-1.5 mb-bg-primary-600 mb-text-white mb-text-xs mb-font-semibold mb-rounded hover:mb-bg-primary-500 mb-transition-colors">
            {{ t('Salva e chiudi') }}
          </button>
          <button @click="cancel" class="mb-px-3 mb-py-1.5 mb-bg-gray-700 mb-text-gray-300 mb-text-xs mb-rounded hover:mb-bg-gray-600 mb-transition-colors">
            {{ t('Annulla') }}
          </button>
        </div>
      </div>

      <!-- Main area: 3 columns -->
      <div class="mb-flex mb-flex-1 mb-overflow-hidden">
        <!-- Left: Slide list + Global Layers -->
        <div class="mb-flex mb-flex-col mb-shrink-0" :style="{ width: leftPanelWidth + 'px' }">
          <SlideList
            :slides="data.slides"
            :activeIndex="activeSlideIndex"
            :globalBackground="data.globalBackground"
            @select="activeSlideIndex = $event; editingGlobal = false"
            @add="addSlide"
            @duplicate="duplicateSlide"
            @remove="removeSlide"
            @import-slides="importSlides"
          />

          <!-- Global Layers section -->
          <div class="mb-border-t mb-border-gray-700 mb-px-2 mb-py-2 mb-bg-gray-800">
            <div class="mb-flex mb-items-center mb-justify-between mb-mb-1">
              <span class="mb-text-[10px] mb-text-gray-400 mb-font-semibold mb-uppercase">{{ t('Livelli globali') }}</span>
              <div class="mb-flex mb-gap-1">
                <button @click="addGlobalLayer('text')" class="mb-text-[10px] mb-text-gray-400 hover:mb-text-white mb-px-1" :title="t('Testo')">T</button>
                <button @click="addGlobalLayer('button')" class="mb-text-[10px] mb-text-gray-400 hover:mb-text-white mb-px-1" :title="t('Bottone')">B</button>
                <button @click="addGlobalLayer('image')" class="mb-text-[10px] mb-text-gray-400 hover:mb-text-white mb-px-1" :title="t('Immagine')">I</button>
                <button @click="addGlobalLayer('icon')" class="mb-text-[10px] mb-text-gray-400 hover:mb-text-white mb-px-1" :title="t('Icona')">Ic</button>
              </div>
            </div>
            <div v-if="data.globalLayers && data.globalLayers.length" class="mb-space-y-0.5">
              <div v-for="gl in data.globalLayers" :key="gl.id"
                @click="selectGlobalLayer(gl.id)"
                :class="['mb-flex mb-items-center mb-justify-between mb-px-2 mb-py-1 mb-rounded mb-text-[10px] mb-cursor-pointer mb-transition-colors',
                  selectedLayerId === gl.id && editingGlobal ? 'mb-bg-primary-600 mb-text-white' : 'mb-bg-gray-700 mb-text-gray-300 hover:mb-bg-gray-600']"
              >
                <span class="mb-truncate mb-flex-1">{{ gl.type === 'text' ? (gl.content || t('Testo')).substring(0, 16) : gl.type }} <span class="mb-text-gray-500">({{ gl.globalPosition === 'back' ? t('dietro') : t('davanti') }})</span></span>
                <button @click.stop="removeGlobalLayer(gl.id)" class="mb-text-gray-400 hover:mb-text-red-400 mb-ml-1">&times;</button>
              </div>
            </div>
            <div v-else class="mb-text-[9px] mb-text-gray-500 mb-italic">{{ t('Nessun livello globale') }}</div>
          </div>
        </div>

        <!-- Left resize handle -->
        <div class="mps-panel-resize-handle" @pointerdown="startResizeLeft"></div>

        <!-- Center: Canvas + Layer bar + Slide background panel -->
        <div class="mb-flex mb-flex-col mb-flex-1 mb-overflow-hidden">
          <!-- Slide bg controls (collapsible) -->
          <div v-if="showSlideBg" class="mb-bg-gray-850 mb-border-b mb-border-gray-700 mb-p-3">
            <div class="mb-flex mb-items-center mb-justify-between mb-mb-2">
              <span class="mb-text-[10px] mb-text-gray-400 mb-font-semibold mb-uppercase">{{ t('Sfondo Slide') }}</span>
              <button @click="showSlideBg = false" class="mb-text-gray-500 mb-text-xs">&times;</button>
            </div>
            <div class="mb-flex mb-gap-3 mb-flex-wrap mb-items-end">
              <!-- Type -->
              <div class="mb-w-32">
                <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-0.5">{{ t('Tipo') }}</label>
                <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="currentBg.type" :options="OPTS_BG_TYPE" @update:model-value="updateBg('type', $event)" />
              </div>
              <!-- Transparent note -->
              <div v-if="currentBg.type === 'transparent'" class="mb-flex mb-items-center mb-gap-1">
                <span class="mb-text-[10px] mb-text-gray-400 mb-italic">{{ t('Sarà visibile lo sfondo globale') }}</span>
              </div>
              <!-- Color -->
              <div v-if="currentBg.type === 'color'">
                <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-0.5">{{ t('Colore') }}</label>
                <FieldColor :modelValue="currentBg.color || '#ffffff'" @update:modelValue="updateBg('color', $event)" />
              </div>
              <!-- Image -->
              <div v-if="currentBg.type === 'image'" class="mb-flex mb-gap-2 mb-items-end">
                <div>
                  <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-0.5">{{ t('Immagine') }}</label>
                  <div class="mb-flex mb-gap-1">
                    <input :value="currentBg.image" @input="updateBg('image', $event.target.value)" class="mps-inp mb-w-48" placeholder="URL" />
                    <button @click="pickSlideBg" class="mb-px-2 mb-bg-gray-600 mb-rounded mb-text-[10px] mb-text-gray-300">{{ t('Sfoglia') }}</button>
                  </div>
                </div>
                <label class="mb-flex mb-items-center mb-gap-1">
                  <input type="checkbox" :checked="currentBg.kenBurns" @change="updateBg('kenBurns', $event.target.checked)" class="mb-rounded" />
                  <span class="mb-text-[10px] mb-text-gray-400">{{ t('Zoom lento (Ken Burns)') }}</span>
                </label>
                <template v-if="currentBg.kenBurns">
                  <div class="mb-w-28">
                    <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="currentBg.kenBurnsDirection || 'in'" :options="OPTS_KENBURNS_DIR" @update:model-value="updateBg('kenBurnsDirection', $event)" />
                  </div>
                  <div class="mb-flex mb-items-center mb-gap-1">
                    <label class="mb-text-[10px] mb-text-gray-400">{{ t('Spostamento orizzontale') }}</label>
                    <NumberScrubber theme="dark" :modelValue="currentBg.kenBurnsPanX || 0" :min="-50" :max="50" :step="5" :defaultValue="0" emitAs="number" unit="%" :ariaLabel="'Pan X%'" @update:modelValue="updateBg('kenBurnsPanX', $event)" />
                  </div>
                  <div class="mb-flex mb-items-center mb-gap-1">
                    <label class="mb-text-[10px] mb-text-gray-400">{{ t('Spostamento verticale') }}</label>
                    <NumberScrubber theme="dark" :modelValue="currentBg.kenBurnsPanY || 0" :min="-50" :max="50" :step="5" :defaultValue="0" emitAs="number" unit="%" :ariaLabel="'Pan Y%'" @update:modelValue="updateBg('kenBurnsPanY', $event)" />
                  </div>
                  <div class="mb-flex mb-items-center mb-gap-1">
                    <label class="mb-text-[10px] mb-text-gray-400">{{ t('Blur inizio') }}</label>
                    <NumberScrubber theme="dark" :modelValue="currentBg.kenBurnsBlurStart || 0" :min="0" :max="20" :step="1" :defaultValue="0" emitAs="number" unit="px" :ariaLabel="t('Blur inizio')" @update:modelValue="updateBg('kenBurnsBlurStart', $event)" />
                  </div>
                  <div class="mb-flex mb-items-center mb-gap-1">
                    <label class="mb-text-[10px] mb-text-gray-400">{{ t('Blur fine') }}</label>
                    <NumberScrubber theme="dark" :modelValue="currentBg.kenBurnsBlurEnd || 0" :min="0" :max="20" :step="1" :defaultValue="0" emitAs="number" unit="px" :ariaLabel="t('Blur fine')" @update:modelValue="updateBg('kenBurnsBlurEnd', $event)" />
                  </div>
                </template>
              </div>
              <!-- Video -->
              <div v-if="currentBg.type === 'video'">
                <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-0.5">{{ t('URL Video') }}</label>
                <div class="mb-flex mb-gap-1">
                  <input :value="currentBg.video" @input="updateBg('video', $event.target.value)" class="mps-inp mb-w-52" :placeholder="t('mp4 o URL YouTube')" />
                  <button @click="pickSlideVideo" class="mb-px-2 mb-bg-gray-600 mb-rounded mb-text-[10px] mb-text-gray-300 hover:mb-bg-gray-500">{{ t('Sfoglia') }}</button>
                </div>
              </div>
              <label v-if="currentBg.type === 'video'" class="mb-flex mb-items-center mb-gap-1">
                <input type="checkbox" :checked="!!currentBg.videoScroll" @change="updateBg('videoScroll', $event.target.checked)" class="mb-rounded" />
                <span class="mb-text-[10px] mb-text-gray-400">{{ t('Segue lo scorrimento') }}</span>
                <InfoTip :testo="t('Il video non va da solo: avanza mentre si scorre la pagina e torna indietro risalendo. Con «Timeline a scorrimento» accesa conta il tratto in cui lo slider resta fermo in cima, se no l’uscita dello slider dalla finestra.')" :titolo="t('Segue lo scorrimento')" />
              </label>
              <!-- Gradient -->
              <template v-if="currentBg.type === 'gradient'">
                <div>
                  <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-0.5">{{ t('Da') }}</label>
                  <FieldColor :modelValue="currentBg.gradientFrom || '#ffffff'" @update:modelValue="updateBg('gradientFrom', $event)" />
                </div>
                <div>
                  <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-0.5">{{ t('A') }}</label>
                  <FieldColor :modelValue="currentBg.gradientTo || '#000000'" @update:modelValue="updateBg('gradientTo', $event)" />
                </div>
                <div>
                  <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-0.5">{{ t('Angolo') }}</label>
                  <NumberScrubber theme="dark" :modelValue="currentBg.gradientAngle" :min="0" :max="360" :step="1" :defaultValue="0" emitAs="number" unit="°" :ariaLabel="t('Angolo')" @update:modelValue="updateBg('gradientAngle', $event)" />
                </div>
              </template>
              <!-- Slide duration (seconds, 0 = use global) -->
              <div class="mb-flex mb-items-center mb-gap-1">
                <label class="mb-text-[10px] mb-text-gray-400">{{ t('Durata') }}</label>
                <NumberScrubber theme="dark" :modelValue="(activeSlide?.duration || 0) / 1000" :min="0" :max="120" :step="0.5" :defaultValue="0" emitAs="number" unit="sec" :ariaLabel="t('Durata')" @update:modelValue="activeSlide.duration = Math.round(($event || 0) * 1000)" />
              </div>
              <!-- Persist for N slides -->
              <div class="mb-flex mb-items-center mb-gap-1">
                <label class="mb-text-[10px] mb-text-gray-400">{{ t('Persisti per') }}</label>
                <NumberScrubber theme="dark" :modelValue="activeSlide?.persistFor || 0" :min="0" :max="20" :step="1" :defaultValue="0" emitAs="number" unit="slide" :ariaLabel="t('Persisti per')" @update:modelValue="activeSlide.persistFor = $event || 0" />
              </div>
              <!-- Tab label (per navigazione tab) -->
              <div class="mb-flex mb-items-center mb-gap-1">
                <label class="mb-text-[10px] mb-text-gray-400">{{ t('Etichetta della scheda') }}</label>
                <input :value="activeSlide?.tabLabel || ''" @input="activeSlide.tabLabel = $event.target.value" class="mps-inp mb-w-28" placeholder="Slide N" />
              </div>
              <!-- Overlay -->
              <div v-if="currentBg.type !== 'transparent'">
                <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-0.5">{{ t('Sovrapposizione') }}</label>
                <FieldColor :modelValue="currentBg.overlay || '#000000'" @update:modelValue="updateBg('overlay', $event)" />
                <div class="mb-flex mb-gap-1 mb-items-center mb-mt-1">
                  <NumberScrubber theme="dark" class="mb-flex-1" :modelValue="Math.round((currentBg.overlayOpacity ?? 0.3) * 100)" :min="0" :max="100" :step="5" :defaultValue="30" emitAs="number" unit="%" :sliderOnFocus="false" :ariaLabel="t('Sovrapposizione')" @update:modelValue="updateBg('overlayOpacity', $event / 100)" />
                </div>
                <!-- sfumata: piena sul bordo, trasparente a questa distanza; 0 = uniforme -->
                <div class="mb-flex mb-gap-1 mb-items-center mb-mt-1">
                  <label class="mb-text-[10px] mb-text-gray-400 mb-w-16">{{ t('Sfuma dall’alto') }}</label>
                  <NumberScrubber theme="dark" class="mb-flex-1" :modelValue="currentBg.overlayFadeTop || 0" :min="0" :max="100" :step="5" :defaultValue="0" emitAs="number" unit="%" :sliderOnFocus="false" :ariaLabel="t('Sfuma dall’alto')" @update:modelValue="updateBg('overlayFadeTop', $event)" />
                </div>
                <div class="mb-flex mb-gap-1 mb-items-center mb-mt-1">
                  <label class="mb-text-[10px] mb-text-gray-400 mb-w-16">{{ t('Sfuma dal basso') }}</label>
                  <NumberScrubber theme="dark" class="mb-flex-1" :modelValue="currentBg.overlayFadeBottom || 0" :min="0" :max="100" :step="5" :defaultValue="0" emitAs="number" unit="%" :sliderOnFocus="false" :ariaLabel="t('Sfuma dal basso')" @update:modelValue="updateBg('overlayFadeBottom', $event)" />
                </div>
              </div>
            </div>
          </div>
          <!-- Sfondo della slide chiuso (1.4.1352): dice subito cos'è (un video, un'immagine) e da dove si cambia.
               Prima c'era solo «Mostra sfondo slide»: il video della tela sembrava venire dal nulla. -->
          <div v-else class="mps-sfondo-chiuso mb-border-b mb-border-gray-700 mb-px-3 mb-py-1.5">
            <span class="mb-text-[10px] mb-text-gray-500">{{ t('Sfondo della slide') }}:</span>
            <span :class="['mps-sfondo-tipo', 'mps-sfondo-tipo--' + (currentBg.type || 'color')]">{{ nomeTipoSfondo }}</span>
            <span v-if="fileSfondo" class="mb-text-[10px] mb-text-gray-400 mb-truncate mb-max-w-xs" :title="fileSfondo">{{ fileSfondo }}</span>
            <span v-if="currentBg.type === 'video' && currentBg.videoScroll" class="mb-text-[10px] mb-text-blue-300">· {{ t('segue lo scorrimento') }}</span>
            <button @click="showSlideBg = true" class="mps-sfondo-cambia">{{ currentBg.type === 'video' || currentBg.type === 'image' ? t('Cambia') : t('Modifica') }}</button>
          </div>

          <!-- Breakpoint bar -->
          <div class="mb-flex mb-items-center mb-gap-1 mb-px-3 mb-py-1 mb-border-b mb-border-gray-700 mb-bg-gray-800">
            <span class="mb-text-[10px] mb-text-gray-500 mb-mr-1">{{ t('Schermo:') }}</span>
            <button v-for="bp in breakpoints" :key="bp.key"
              @click="activeBreakpoint = bp.key"
              :class="['mb-px-2 mb-py-0.5 mb-rounded mb-text-[10px] mb-transition-colors', activeBreakpoint === bp.key ? 'mb-bg-primary-600 mb-text-white' : 'mb-bg-gray-700 mb-text-gray-400 hover:mb-bg-gray-600']"
              :title="bp.label + ' (' + bp.width + 'px)'"
            ><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="display:inline-block;vertical-align:-1px;margin-right:3px"><path :d="bp.icon" /></svg>{{ bp.label }}</button>

            <!-- Anteprime (1.4.1352): lo scorrimento della pagina e gli stati, come sul sito -->
            <div class="mps-anteprime mb-ml-auto mb-flex mb-items-center mb-gap-3">
              <div v-if="usaScorrimento" class="mb-flex mb-items-center mb-gap-1.5">
                <span class="mb-text-[10px] mb-text-gray-400 mb-flex mb-items-center mb-gap-1">{{ t('Scorrimento') }}<InfoTip :testo="t('Trascina per vedere come si muovono i livelli mentre la pagina scorre: 0% è l’inizio, 100% la fine del tratto dello slider. ▶ fa vedere tutto il movimento. Per spostare e modificare i livelli torna alla vista ferma con ×.')" :titolo="t('Anteprima dello scorrimento')" /></span>
                <button @click="guardaMovimento" class="mps-ant-btn" :title="t('Guarda il movimento')" :aria-label="t('Guarda il movimento')">
                  <svg v-if="!scrollPlaying" width="10" height="10" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M6 4l14 8-14 8z"/></svg>
                  <svg v-else width="10" height="10" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M6 4h4v16H6zM14 4h4v16h-4z"/></svg>
                </button>
                <input type="range" min="0" max="100" step="0.5" :value="scrollPreview ?? 0" @input="fermaScorrimento(); scrollPreview = +$event.target.value" class="mps-ant-range" :aria-label="t('Scorrimento')" />
                <span class="mb-text-[10px] mb-font-mono mb-w-9 mb-text-right" :class="scrollPreview == null ? 'mb-text-gray-500' : 'mb-text-blue-300'">{{ scrollPreview == null ? t('ferma') : Math.round(scrollPreview) + '%' }}</span>
                <button v-if="scrollPreview != null" @click="chiudiAnteprimaScorrimento" class="mps-ant-btn" :title="t('Torna alla vista ferma')" :aria-label="t('Torna alla vista ferma')">&times;</button>
              </div>
              <div v-if="opzioniStato.length > 2" class="mb-flex mb-items-center mb-gap-1.5">
                <span class="mb-text-[10px] mb-text-gray-400 mb-flex mb-items-center mb-gap-1">{{ t('Stato dei livelli') }}<InfoTip :testo="t('Alcuni livelli si vedono solo in uno stato (◐ nella barra dei livelli), che cambia quando si clicca un altro livello. «Come all’avvio» mostra lo slider come lo vede chi apre la pagina; scegli uno stato per vedere solo i suoi livelli; «Tutti i livelli insieme» li mostra uno sopra l’altro, per modificarli.')" :titolo="t('Anteprima degli stati')" /></span>
                <div class="mb-w-40">
                  <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="valoreStatoScelto" :options="opzioniStato" @update:model-value="scegliStato" />
                </div>
              </div>
            </div>
          </div>
          <!-- Cosa fa la slide, in numeri (i bollini della barra dei livelli dicono quale livello) -->
          <div v-if="riepilogoAnimazioni.length" class="mps-riepilogo mb-flex mb-flex-wrap mb-items-center mb-gap-x-3 mb-gap-y-1 mb-px-3 mb-py-1 mb-border-b mb-border-gray-700">
            <span class="mb-text-[10px] mb-text-gray-500 mb-flex mb-items-center mb-gap-1">{{ t('In questa slide') }}<InfoTip :testo="t('Quanti livelli fanno qualcosa, e cosa. Nella barra dei livelli lo stesso bollino dice quali: ↕ scorrendo, ⏱ linea del tempo, Aa lettere, ◐ stato, ☝ clic, ∞ continuo, {} CSS, ▭ solo su alcuni schermi. Passa il puntatore su un bollino per leggerlo.')" :titolo="t('Cosa succede in questa slide')" />:</span>
            <span v-for="r in riepilogoAnimazioni" :key="r.k" :class="['mps-riep', 'mps-riep--' + r.k]">{{ r.testo }}</span>
          </div>

          <!-- Canvas -->
          <div class="mb-relative mb-flex mb-flex-col mb-flex-1 mb-overflow-hidden" @wheel.prevent="onCanvasWheel">
            <SliderCanvas
              :slide="activeSlide"
              :sliderHeight="activeSliderHeight"
              :globalBackground="data.globalBackground"
              :selectedLayerId="selectedLayerId"
              :editingLayerId="editingLayerId"
              :timelinePlayhead="selectedLayerHasTimeline ? timelinePlayhead : null"
              :selectedKeyframeId="selectedKeyframeId"
              :activeBreakpoint="activeBreakpoint"
              :canvasMaxWidth="canvasDesignWidth"
              :zoom="canvasZoom"
              :hiddenIds="hiddenLayerIds"
              :scrollPreview="scrollPreview"
              :statePreview="statePreview"
              :initialStates="statiIniziali"
              @deselect="selectedLayerId = null"
              @select-layer="selectedLayerId = $event"
              @update-layer="updateLayer"
              @update-keyframe="onCanvasKeyframeUpdate"
              @start-edit="editingLayerId = $event"
              @stop-edit="editingLayerId = null"
            />
            <!-- Zoom controls overlay -->
            <div class="mb-absolute mb-bottom-2 mb-right-2 mb-flex mb-items-center mb-gap-1 mb-bg-gray-800/90 mb-rounded mb-px-2 mb-py-1 mb-z-10">
              <button @click="canvasZoom = Math.max(0.25, canvasZoom - 0.25)" class="mb-text-gray-400 hover:mb-text-white mb-text-xs mb-px-1" :title="t('Riduci')">−</button>
              <input type="range" :value="canvasZoom * 100" @input="canvasZoom = Math.max(25, Math.min(300, parseInt($event.target.value))) / 100" min="25" max="300" step="25" class="mb-w-20" />
              <button @click="canvasZoom = Math.min(3, canvasZoom + 0.25)" class="mb-text-gray-400 hover:mb-text-white mb-text-xs mb-px-1" :title="t('Ingrandisci')">+</button>
              <span class="mb-text-[10px] mb-text-gray-400 mb-font-mono mb-w-10 mb-text-center">{{ Math.round(canvasZoom * 100) }}%</span>
              <button @click="canvasZoom = 1" class="mb-text-[10px] mb-text-gray-500 hover:mb-text-white mb-px-1" :title="t('Zoom al 100%')">⊘</button>
            </div>
          </div>

          <!-- Multi-track Keyframe Timeline (sempre visibile se ci sono layer con timeline) -->
          <KeyframeTimeline
            v-if="hasAnyTimeline"
            :layers="activeSlide?.layers || []"
            :selectedLayerId="selectedLayerId"
            :selectedKeyframeId="selectedKeyframeId"
            :playhead="timelinePlayhead"
            :isPlaying="isTimelinePlaying"
            :slideDuration="activeSlide?.duration || 0"
            :autoplaySpeed="data.autoplaySpeed || 5000"
            @seek="timelinePlayhead = $event"
            @toggle-play="toggleTimelinePlay"
            @select-layer="onTimelineSelectLayer"
            @select-keyframe="selectedKeyframeId = $event"
            @add-keyframe="onAddKeyframe"
            @remove-keyframe="onRemoveKeyframe"
            @update-keyframe="onUpdateKeyframe"
            @update-timeline-prop="onUpdateTimelineProp"
            @update-slide-duration="onUpdateSlideDuration"
            @set-all-durations="onSetAllDurations"
          />

          <!-- Layer bar -->
          <LayerBar
            :layers="activeSlide?.layers || []"
            :selectedId="selectedLayerId"
            :hiddenIds="hiddenLayerIds"
            :activeBreakpoint="activeBreakpoint"
            @add-layer="addLayer"
            @select="editingGlobal = false; selectedLayerId = $event"
            @remove="removeLayer"
            @toggle-visibility="toggleLayerVisibility"
            @move-up="moveLayerUp"
            @move-down="moveLayerDown"
          />
        </div>

        <!-- Right resize handle -->
        <div class="mps-panel-resize-handle" @pointerdown="startResizeRight"></div>

        <!-- Right: Layer properties -->
        <LayerProperties
          :layer="selectedLayer"
          :selectedKeyframeId="selectedKeyframeId"
          :activeBreakpoint="activeBreakpoint"
          :slideLayers="activeSlide?.layers || []"
          :scrollPreview="scrollPreview"
          @set-scroll-preview="fermaScorrimento(); scrollPreview = $event"
          @update="updateLayerProp"
          @update-keyframe="onUpdateKeyframe"
          @capture-from-canvas="captureLayerToKeyframe"
          :style="{ width: rightPanelWidth + 'px', flexShrink: 0 }"
        />
      </div>

      <!-- Footer: Slider settings -->
      <SliderSettings :settings="data" @update="updateGlobal" />
    </div>
  </Teleport>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, reactive, nextTick, watch } from 'vue';
import { useTilesStore } from '@/stores/tiles';
import { useBuilderStore } from '@/stores/builder';
import prosliderDef, { normalizeHeight, resolveHeightPx } from '@/config/elements/proslider.js';
import SlideList from './SlideList.vue';
import SliderCanvas from './SliderCanvas.vue';
import LayerProperties from './LayerProperties.vue';
import LayerBar from './LayerBar.vue';
import SliderSettings from './SliderSettings.vue';
import KeyframeTimeline from './KeyframeTimeline.vue';
import FieldColor from '@/components/Builder/fields/FieldColor.vue';
import FieldSelect from '../Builder/fields/FieldSelect.vue';
import NumberScrubber from '../Builder/fields/NumberScrubber.vue';
import { t } from '@/i18n';
import InfoTip from '../Builder/InfoTip.vue';
import { leggiStati, statiDellaSlide, animazioniLivello } from './livelloUtils.js';

// Option arrays per FieldSelect (stessi value dei vecchi <option>: dati salvati invariati)
const OPTS_BG_TYPE = [
  { value: 'color', label: 'Colore' },
  { value: 'image', label: 'Immagine' },
  { value: 'video', label: 'Video' },
  { value: 'gradient', label: 'Gradiente' },
  { value: 'transparent', label: 'Trasparente' },
];
const OPTS_KENBURNS_DIR = [
  { value: 'in', label: 'Zoom In' },
  { value: 'out', label: 'Zoom Out' },
];

const props = defineProps({
  tileId: { type: String, required: true },
});

const emit = defineEmits(['close']);

const tilesStore = useTilesStore();
const builderStore = useBuilderStore();

const { defaultSlide, defaultLayer, makeId } = prosliderDef.helpers;

// Clone current tile settings into local state
const tile = tilesStore.getTileById(props.tileId);
const data = reactive(JSON.parse(JSON.stringify(tile?.settings || prosliderDef.defaults)));

// Ensure slides array
if (!data.slides || !data.slides.length) {
  data.slides = [defaultSlide()];
}

const activeSlideIndex = ref(0);
const selectedLayerId = ref(null);
const editingLayerId = ref(null);
const showSlideBg = ref(false);
const hiddenLayerIds = reactive(new Set());
const modalEl = ref(null);
const editingGlobal = ref(false);

// Responsive breakpoints
const activeBreakpoint = ref('desktop');
const breakpoints = [
  // Icone SVG (tracciati Lucide), mai emoji.
  { key: 'desktop',  label: 'Desktop',  icon: 'M4 3h16a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2zM8 21h8M12 17v4', width: 1440 },
  { key: 'notebook', label: 'Notebook', icon: 'M20 16V7a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v9m16 0H4m16 0 1.28 2.55a1 1 0 0 1-.9 1.45H3.62a1 1 0 0 1-.9-1.45L4 16', width: 1240 },
  { key: 'tablet',   label: 'Tablet',   icon: 'M6 2h12a2 2 0 0 1 2 2v16a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2zM12 18h.01', width: 1024 },
  { key: 'mobile',   label: 'Mobile',   icon: 'M7 2h10a2 2 0 0 1 2 2v16a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2zM12 18h.01', width: 640 },
];

const activeCanvasWidth = computed(() => {
  const bp = breakpoints.find(b => b.key === activeBreakpoint.value);
  return bp ? bp.width : null;
});

// Carosello: la tela di una slide è larga quanto la card (Larghezza slide %, sul telefono almeno 80%, come
// sul sito: olo-proslider.css/.js). I livelli globali coprono tutto lo slider e restano sulla tela intera.
const canvasDesignWidth = computed(() => {
  const w = activeCanvasWidth.value || 1440;
  if (!data.carousel || editingGlobal.value) return w;
  let frazione = Math.min(95, Math.max(40, Number(data.carouselWidth) || 80));
  if (activeBreakpoint.value === 'mobile') frazione = Math.max(frazione, 80);
  return Math.round(w * frazione / 100);
});

const activeSliderHeight = computed(() => {
  const cw = activeCanvasWidth.value || 1440;
  let raw;
  if (activeBreakpoint.value === 'desktop') {
    raw = data.height;
  } else {
    const hKey = 'height' + activeBreakpoint.value.charAt(0).toUpperCase() + activeBreakpoint.value.slice(1);
    raw = data[hKey] || data.height;
  }
  const h = normalizeHeight(raw);
  return resolveHeightPx(h, cw) || 600;
});

// Timeline state
const selectedKeyframeId = ref(null);
const timelinePlayhead = ref(0);
const isTimelinePlaying = ref(false);
let timelineRafId = null;
let timelineStartTs = 0;
let timelineStartPlayhead = 0;

const activeSlide = computed(() => data.slides[activeSlideIndex.value] || null);
const currentBg = computed(() => activeSlide.value?.background || {});

const selectedLayer = computed(() => {
  if (!selectedLayerId.value) return null;
  // Check global layers first
  if (editingGlobal.value && data.globalLayers) {
    const gl = data.globalLayers.find(l => l.id === selectedLayerId.value);
    if (gl) return gl;
  }
  if (!activeSlide.value) return null;
  return activeSlide.value.layers.find(l => l.id === selectedLayerId.value) || null;
});

const selectedLayerHasTimeline = computed(() => {
  return !!(selectedLayer.value?.timeline?.keyframes?.length >= 2);
});

const hasAnyTimeline = computed(() => {
  const layers = activeSlide.value?.layers || [];
  return layers.some(l => l.timeline?.keyframes?.length >= 2);
});

// ── Anteprime (1.4.1352): vedere sulla tela quello che fa lo slider sul sito ──
// Scorrimento: 0–100 come l'avanzamento della scena sul sito (col «Scorrimento fissato» il tratto in cui lo slider resta
// fermo in cima); null = vista ferma, quella per modificare (i livelli che entrano scorrendo restano visibili).
const scrollPreview = ref(null);
// Stato: {} = come all'avvio (quello che vede chi apre la pagina: di serie, se no i livelli dei vari stati stanno
// tutti uno sopra l'altro), null = tutti i livelli insieme (per modificarli), { nome: valore } = quello stato.
const statePreview = ref({});
let scrollPlayRaf = null;
const usaScorrimento = computed(() => {
  const layers = activeSlide.value?.layers || [];
  return !!data.scrollTimeline || layers.some(l => l.scrollMove) || !!activeSlide.value?.background?.videoScroll;
});
const statiIniziali = computed(() => leggiStati(data.stati || ''));
const statiSlide = computed(() => statiDellaSlide(activeSlide.value?.layers || [], statiIniziali.value));
const opzioniStato = computed(() => {
  const opts = [{ value: 'avvio', label: t('Come all’avvio') }, { value: 'tutti', label: t('Tutti i livelli insieme') }];
  for (const [nome, valori] of Object.entries(statiSlide.value)) {
    for (const v of valori) opts.push({ value: nome + '=' + v, label: nome + ' = ' + v });
  }
  return opts;
});
const valoreStatoScelto = computed(() => {
  const s = statePreview.value;
  if (!s) return 'tutti';
  const [n] = Object.keys(s);
  return n ? n + '=' + s[n] : 'avvio';
});
function scegliStato(v) {
  if (!v || v === 'avvio') { statePreview.value = {}; return; }
  if (v === 'tutti') { statePreview.value = null; return; }
  const i = v.indexOf('=');
  statePreview.value = { [v.slice(0, i)]: v.slice(i + 1) };
}
const scrollPlaying = ref(false);
function fermaScorrimento() {
  if (scrollPlayRaf) cancelAnimationFrame(scrollPlayRaf);
  scrollPlayRaf = null;
  scrollPlaying.value = false;
}
// Lo sfondo della slide, detto in chiaro nella barra chiusa: il tipo e il nome del file.
const nomeTipoSfondo = computed(() => {
  const tipi = { color: t('Colore'), image: t('Immagine'), video: t('Video'), gradient: t('Gradiente'), transparent: t('Trasparente') };
  return tipi[currentBg.value.type || 'color'] || currentBg.value.type;
});
const fileSfondo = computed(() => {
  const bg = currentBg.value;
  const url = bg.type === 'video' ? bg.video : (bg.type === 'image' ? bg.image : '');
  return url ? String(url).split(/[?#]/)[0].split('/').pop() : '';
});
function chiudiAnteprimaScorrimento() {
  fermaScorrimento();
  scrollPreview.value = null;
}
// «Guarda il movimento»: la scena da 0 a 100 in 6 secondi, poi resta alla fine.
function guardaMovimento() {
  if (scrollPlayRaf) { fermaScorrimento(); return; }
  const durata = 6000;
  const partenza = scrollPreview.value != null && scrollPreview.value < 99 ? scrollPreview.value : 0;
  const t0 = performance.now() - (partenza / 100) * durata;
  const passo = (ts) => {
    const p = Math.min(100, ((ts - t0) / durata) * 100);
    scrollPreview.value = Math.round(p * 10) / 10;
    scrollPlayRaf = p < 100 ? requestAnimationFrame(passo) : null;
    if (!scrollPlayRaf) scrollPlaying.value = false;
  };
  scrollPlaying.value = true;
  scrollPlayRaf = requestAnimationFrame(passo);
}
// Cosa succede nella slide, in numeri (riga sopra la tela): quanti livelli scorrendo, con uno stato, ecc.
const riepilogoAnimazioni = computed(() => {
  const conta = {};
  for (const l of activeSlide.value?.layers || []) {
    for (const a of animazioniLivello(l)) conta[a.k] = (conta[a.k] || 0) + 1;
  }
  const nomi = {
    scroll: t('si muovono scorrendo'), timeline: t('con linea del tempo'), lettere: t('entrano lettera per lettera'),
    stato: t('legati a uno stato'), azione: t('cliccabili'), pulisci: t('da pulire col puntatore'), ciclo: t('in movimento continuo'),
    parallasse: t('in parallasse'), css: t('con CSS su misura'), formati: t('solo su alcuni schermi'),
  };
  return Object.entries(conta).filter(([k]) => nomi[k]).map(([k, n]) => ({ k, testo: n + ' ' + nomi[k] }));
});
watch(activeSlideIndex, () => { statePreview.value = {}; });

function onTimelineSelectLayer(layerId) {
  editingGlobal.value = false;
  selectedLayerId.value = layerId;
}

// --- Slide operations ---
function addSlide() {
  data.slides.push(defaultSlide());
  activeSlideIndex.value = data.slides.length - 1;
  selectedLayerId.value = null;
}

function duplicateSlide(idx) {
  const clone = JSON.parse(JSON.stringify(data.slides[idx]));
  clone.id = makeId();
  clone.layers.forEach(l => { l.id = makeId(); });
  data.slides.splice(idx + 1, 0, clone);
  activeSlideIndex.value = idx + 1;
}

function removeSlide(idx) {
  if (data.slides.length <= 1) return;
  data.slides.splice(idx, 1);
  if (activeSlideIndex.value >= data.slides.length) {
    activeSlideIndex.value = data.slides.length - 1;
  }
  selectedLayerId.value = null;
}

function importSlides(json) {
  data.slides = json.slides;
  if (json.globalBackground) {
    data.globalBackground = json.globalBackground;
  }
  activeSlideIndex.value = 0;
  selectedLayerId.value = null;
}

// --- Background ---
function updateBg(key, val) {
  if (activeSlide.value) {
    activeSlide.value.background = { ...activeSlide.value.background, [key]: val };
  }
}

function pickSlideBg() {
  if (!window.wp?.media) return;
  const frame = wp.media({ title: 'Seleziona immagine di sfondo', multiple: false, library: { type: 'image' } });
  frame.on('select', () => {
    const url = frame.state().get('selection').first().toJSON().url;
    updateBg('image', url);
  });
  frame.open();
}

function pickSlideVideo() {
  if (!window.wp?.media) return;
  const frame = wp.media({ title: 'Seleziona video', multiple: false, library: { type: 'video' } });
  frame.on('select', () => {
    const url = frame.state().get('selection').first().toJSON().url;
    updateBg('video', url);
  });
  frame.open();
}

// --- Layer operations ---
function addLayer(type) {
  if (!activeSlide.value) return;
  const layer = defaultLayer(type);
  // Offset each new layer slightly
  const count = activeSlide.value.layers.length;
  layer.y = 20 + count * 8;
  layer.animInDelay = count * 200;
  activeSlide.value.layers.push(layer);
  selectedLayerId.value = layer.id;
}

function removeLayer(id) {
  if (!activeSlide.value) return;
  const idx = activeSlide.value.layers.findIndex(l => l.id === id);
  if (idx >= 0) {
    activeSlide.value.layers.splice(idx, 1);
    if (selectedLayerId.value === id) selectedLayerId.value = null;
  }
}

function replaceLayer(layersArr, layerId, newLayer) {
  const idx = layersArr.findIndex(l => l.id === layerId);
  if (idx >= 0) layersArr.splice(idx, 1, newLayer);
}

function updateLayer(layerId, updates) {
  if (!activeSlide.value) return;
  const layer = activeSlide.value.layers.find(l => l.id === layerId);
  if (!layer) return;

  const bp = activeBreakpoint.value;
  const responsiveKeys = ['x', 'y', 'width', 'height', 'fontSize', 'visible', 'textAlign'];

  if (bp !== 'desktop') {
    // Salva in responsive override solo le chiavi responsive
    const resp = layer.responsive || { notebook: null, tablet: null, mobile: null };
    const bpOverride = resp[bp] || {};
    const respUpdates = {};
    const directUpdates = {};
    for (const [k, v] of Object.entries(updates)) {
      if (responsiveKeys.includes(k)) {
        respUpdates[k] = v;
      } else {
        directUpdates[k] = v;
      }
    }
    replaceLayer(activeSlide.value.layers, layerId, {
      ...layer,
      ...directUpdates,
      responsive: { ...resp, [bp]: { ...bpOverride, ...respUpdates } },
    });
  } else {
    replaceLayer(activeSlide.value.layers, layerId, { ...layer, ...updates });
  }
}

function updateLayerProp(key, val) {
  if (!selectedLayerId.value) return;
  const id = selectedLayerId.value;
  // Find layer in global layers or slide layers
  let layer = null;
  let layersArr = null;
  if (editingGlobal.value && data.globalLayers) {
    layer = data.globalLayers.find(l => l.id === id);
    if (layer) layersArr = data.globalLayers;
  }
  if (!layer && activeSlide.value) {
    layer = activeSlide.value.layers.find(l => l.id === id);
    if (layer) layersArr = activeSlide.value.layers;
  }
  if (!layer || !layersArr) return;

  // Se siamo su un breakpoint diverso da desktop, salva nel responsive override
  const bp = activeBreakpoint.value;
  const responsiveKeys = ['x', 'y', 'width', 'height', 'fontSize', 'visible', 'textAlign'];
  if (bp !== 'desktop' && responsiveKeys.includes(key)) {
    const resp = layer.responsive || { notebook: null, tablet: null, mobile: null };
    const bpOverride = resp[bp] || {};
    replaceLayer(layersArr, id, {
      ...layer,
      responsive: { ...resp, [bp]: { ...bpOverride, [key]: val } },
    });
  } else {
    replaceLayer(layersArr, id, { ...layer, [key]: val });
  }
}

function toggleLayerVisibility(id) {
  if (hiddenLayerIds.has(id)) hiddenLayerIds.delete(id);
  else hiddenLayerIds.add(id);
}

// Move layer toward front (higher z-index = later in array)
function moveLayerUp(id) {
  if (!activeSlide.value) return;
  const layers = activeSlide.value.layers;
  const idx = layers.findIndex(l => l.id === id);
  if (idx < 0 || idx >= layers.length - 1) return;
  const item = layers.splice(idx, 1)[0];
  layers.splice(idx + 1, 0, item);
}

// Move layer toward back (lower z-index = earlier in array)
function moveLayerDown(id) {
  if (!activeSlide.value) return;
  const layers = activeSlide.value.layers;
  const idx = layers.findIndex(l => l.id === id);
  if (idx <= 0) return;
  const item = layers.splice(idx, 1)[0];
  layers.splice(idx - 1, 0, item);
}

// --- Global Layer operations ---
function addGlobalLayer(type) {
  if (!data.globalLayers) data.globalLayers = [];
  const layer = defaultLayer(type);
  layer.isGlobal = true;
  layer.globalPosition = 'front';
  data.globalLayers.push(layer);
  editingGlobal.value = true;
  selectedLayerId.value = layer.id;
}

function removeGlobalLayer(id) {
  if (!data.globalLayers) return;
  const idx = data.globalLayers.findIndex(l => l.id === id);
  if (idx >= 0) {
    data.globalLayers.splice(idx, 1);
    if (selectedLayerId.value === id) {
      selectedLayerId.value = null;
      editingGlobal.value = false;
    }
  }
}

function selectGlobalLayer(id) {
  editingGlobal.value = true;
  selectedLayerId.value = id;
}

// --- Timeline operations ---
function toggleTimelinePlay() {
  if (isTimelinePlaying.value) {
    stopTimelinePlay();
  } else {
    startTimelinePlay();
  }
}

function getMaxTimelineDuration() {
  const layers = activeSlide.value?.layers || [];
  let max = 0;
  for (const l of layers) {
    if (l.timeline?.keyframes?.length >= 2) {
      const d = (l.timeline.duration || 3000) + (l.timeline.delay || 0);
      if (d > max) max = d;
    }
  }
  const slideDur = activeSlide.value?.duration || data.autoplaySpeed || 5000;
  if (slideDur > max) max = slideDur;
  return max || 3000;
}

function startTimelinePlay() {
  if (!hasAnyTimeline.value) return;
  isTimelinePlaying.value = true;
  timelineStartTs = performance.now();
  timelineStartPlayhead = timelinePlayhead.value;
  timelineRafId = requestAnimationFrame(tickTimeline);
}

function stopTimelinePlay() {
  isTimelinePlaying.value = false;
  if (timelineRafId) {
    cancelAnimationFrame(timelineRafId);
    timelineRafId = null;
  }
}

function tickTimeline(now) {
  if (!isTimelinePlaying.value) return;
  const maxDur = getMaxTimelineDuration();
  const elapsed = now - timelineStartTs;
  let t = timelineStartPlayhead + elapsed;
  if (t >= maxDur) {
    t = t % maxDur;
    timelineStartTs = now;
    timelineStartPlayhead = 0;
  }
  timelinePlayhead.value = Math.round(t);
  timelineRafId = requestAnimationFrame(tickTimeline);
}

function onAddKeyframe(kf) {
  if (!selectedLayer.value?.timeline) return;
  selectedLayer.value.timeline.keyframes.push(kf);
  selectedKeyframeId.value = kf.id;
}

function onRemoveKeyframe(kfId) {
  if (!selectedLayer.value?.timeline) return;
  const kfs = selectedLayer.value.timeline.keyframes;
  if (kfs.length <= 2) return;
  const idx = kfs.findIndex(k => k.id === kfId);
  if (idx >= 0) kfs.splice(idx, 1);
  if (selectedKeyframeId.value === kfId) {
    selectedKeyframeId.value = kfs[0]?.id || null;
  }
}

function onUpdateKeyframe(kfId, updates) {
  if (!selectedLayer.value?.timeline) return;
  const tl = selectedLayer.value.timeline;
  const idx = tl.keyframes.findIndex(k => k.id === kfId);
  if (idx >= 0) {
    tl.keyframes.splice(idx, 1, { ...tl.keyframes[idx], ...updates });
  }
}

function onCanvasKeyframeUpdate(kfId, updates) {
  if (!selectedLayer.value?.timeline) return;
  const tl = selectedLayer.value.timeline;
  const idx = tl.keyframes.findIndex(k => k.id === kfId);
  if (idx >= 0 && updates.props) {
    const kf = tl.keyframes[idx];
    tl.keyframes.splice(idx, 1, { ...kf, props: { ...(kf.props || {}), ...updates.props } });
  }
}

function onUpdateTimelineProp(key, val) {
  if (!selectedLayer.value?.timeline) return;
  selectedLayer.value.timeline[key] = val;
  // Se la durata cambia, clamp i keyframe
  if (key === 'duration') {
    for (const kf of selectedLayer.value.timeline.keyframes) {
      if (kf.time > val) kf.time = val;
    }
    if (timelinePlayhead.value > val) timelinePlayhead.value = val;
  }
}

function onUpdateSlideDuration(ms) {
  if (activeSlide.value) {
    activeSlide.value.duration = ms;
  }
}

function onSetAllDurations(ms) {
  if (!activeSlide.value) return;
  for (const layer of activeSlide.value.layers) {
    if (layer.timeline?.keyframes?.length >= 2) {
      layer.timeline.duration = ms;
      // Clamp keyframes
      for (const kf of layer.timeline.keyframes) {
        if (kf.time > ms) kf.time = ms;
      }
    }
  }
}

function captureLayerToKeyframe() {
  if (!selectedLayer.value || !selectedKeyframeId.value) return;
  const l = selectedLayer.value;
  const kf = l.timeline?.keyframes?.find(k => k.id === selectedKeyframeId.value);
  if (kf) {
    kf.props.x = l.x;
    kf.props.y = l.y;
    kf.props.opacity = l.opacity ?? 100;
  }
}

// --- Global settings ---
function updateGlobal(key, val) {
  data[key] = val;
}

// --- Save / Cancel ---
function save() {
  // Write back to tile store
  const settings = JSON.parse(JSON.stringify(data));
  // Update all settings keys
  for (const key of Object.keys(settings)) {
    tilesStore.updateTile(props.tileId, { [key]: settings[key] });
  }
  builderStore.isDirty = true;
  emit('close');
}

function cancel() {
  emit('close');
}

// --- Keyboard ---
function onKeydown(e) {
  if (e.key === 'Escape') {
    if (editingLayerId.value) {
      editingLayerId.value = null;
    } else {
      cancel();
    }
  }
  if (e.key === 'Delete' && selectedLayerId.value && !editingLayerId.value) {
    removeLayer(selectedLayerId.value);
  }
}

// Reset timeline state when switching layers
watch(selectedLayerId, () => {
  stopTimelinePlay();
  timelinePlayhead.value = 0;
  selectedKeyframeId.value = null;
});

// --- Canvas zoom ---
const canvasZoom = ref(1);
function onCanvasWheel(e) {
  if (e.ctrlKey || e.metaKey) {
    const delta = e.deltaY > 0 ? -0.1 : 0.1;
    canvasZoom.value = Math.round(Math.max(0.25, Math.min(3, canvasZoom.value + delta)) * 100) / 100;
  }
}

// --- Resizable side panels ---
const leftPanelWidth = ref(180);
const rightPanelWidth = ref(256);
let panelResizeState = null;

function startResizeLeft(e) {
  panelResizeState = { side: 'left', startX: e.clientX, startW: leftPanelWidth.value };
  window.addEventListener('pointermove', onPanelResizeMove);
  window.addEventListener('pointerup', onPanelResizeUp);
  e.preventDefault();
}

function startResizeRight(e) {
  panelResizeState = { side: 'right', startX: e.clientX, startW: rightPanelWidth.value };
  window.addEventListener('pointermove', onPanelResizeMove);
  window.addEventListener('pointerup', onPanelResizeUp);
  e.preventDefault();
}

function onPanelResizeMove(e) {
  if (!panelResizeState) return;
  const delta = e.clientX - panelResizeState.startX;
  if (panelResizeState.side === 'left') {
    leftPanelWidth.value = Math.max(120, Math.min(400, panelResizeState.startW + delta));
  } else {
    rightPanelWidth.value = Math.max(200, Math.min(500, panelResizeState.startW - delta));
  }
}

function onPanelResizeUp() {
  panelResizeState = null;
  window.removeEventListener('pointermove', onPanelResizeMove);
  window.removeEventListener('pointerup', onPanelResizeUp);
}

onMounted(() => {
  nextTick(() => modalEl.value?.focus());
});

onUnmounted(() => {
  stopTimelinePlay();
  fermaScorrimento();
  window.removeEventListener('pointermove', onPanelResizeMove);
  window.removeEventListener('pointerup', onPanelResizeUp);
});
</script>

<style scoped>
.mb-bg-gray-850 {
  background: #1a2234;
}
/* Lo sfondo della slide nella barra chiusa (1.4.1352) */
.mps-sfondo-chiuso { display: flex; align-items: center; gap: 8px; min-width: 0; }
.mps-sfondo-tipo { font-size: 10px; font-weight: 700; padding: 1px 7px; border-radius: 999px; background: #334155; color: #e2e8f0; flex-shrink: 0; }
.mps-sfondo-tipo--video { background: #7f1d1d; color: #fecaca; }
.mps-sfondo-tipo--image { background: #1e3a5f; color: #bfdbfe; }
.mps-sfondo-cambia { margin-left: 4px; font-size: 10px; font-weight: 600; color: #e5e7eb; background: #374151; border: 1px solid #4b5563; border-radius: 4px; padding: 1px 8px; cursor: pointer; flex-shrink: 0; }
.mps-sfondo-cambia:hover { background: #4b5563; }
.mps-sfondo-cambia:focus-visible, .mps-ant-btn:focus-visible { outline: 2px solid #93c5fd; outline-offset: 1px; }
/* Anteprime di scorrimento e stato */
.mps-ant-btn { display: inline-flex; align-items: center; justify-content: center; width: 20px; height: 20px; border-radius: 4px; background: #374151; color: #e5e7eb; border: 1px solid #4b5563; cursor: pointer; font-size: 13px; line-height: 1; }
.mps-ant-btn:hover { background: #4b5563; }
.mps-ant-range { width: 140px; accent-color: #60a5fa; }
.mps-riep { font-size: 10px; font-weight: 600; padding: 1px 7px; border-radius: 999px; background: #334155; color: #cbd5e1; }
.mps-riep--scroll { background: #1e3a5f; color: #93c5fd; }
.mps-riep--timeline { background: #3b2f5f; color: #c4b5fd; }
.mps-riep--stato, .mps-riep--azione, .mps-riep--pulisci { background: #4a3a14; color: #fcd34d; }
.mps-riep--css { background: #4c1d1d; color: #fca5a5; }
.mps-inp {
  background: #ffffff;
  border: 1px solid #d1d5db;
  border-radius: 0.25rem;
  padding: 3px 6px;
  font-size: 11px;
  color: #111827;
}
.mps-sel {
  background: #ffffff;
  border: 1px solid #d1d5db;
  border-radius: 0.25rem;
  padding: 3px 6px;
  font-size: 11px;
  color: #111827;
}
.mps-panel-resize-handle {
  width: 6px;
  cursor: ew-resize;
  background: transparent;
  position: relative;
  flex-shrink: 0;
  z-index: 10;
}
.mps-panel-resize-handle::after {
  content: '';
  position: absolute;
  top: 50%;
  left: 1px;
  transform: translateY(-50%);
  width: 4px;
  height: 40px;
  border-radius: 2px;
  background: #4b5563;
  opacity: 0;
  transition: opacity 0.15s;
}
.mps-panel-resize-handle:hover::after {
  opacity: 1;
}
</style>

<!-- Global: force WP media modal above ProSlider editor -->
<style>
.media-modal { z-index: 1100000 !important; }
.media-modal-backdrop { z-index: 1099999 !important; }
</style>
