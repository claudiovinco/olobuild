<template>
  <Teleport to="body">
    <div class="mps-modal-overlay" style="position:fixed;top:0;left:0;width:100vw;height:100vh;z-index:999999;background:#111827;display:flex;flex-direction:column;outline:none;" @keydown="onKeydown" tabindex="-1" ref="modalEl">
      <!-- Header bar -->
      <div class="mb-flex mb-items-center mb-justify-between mb-bg-gray-800 mb-border-b mb-border-gray-700 mb-px-4 mb-py-2 mb-shrink-0">
        <div class="mb-flex mb-items-center mb-gap-3 mb-min-w-0">
          <h2 class="mb-text-sm mb-font-bold mb-text-gray-200">{{ t('Editor dello Slider Pro') }}</h2>
          <!-- Le modalità (1.4.1426, come Disegno/Animazione/Azioni di Slider Revolution 7): ognuna mostra solo i suoi comandi -->
          <div class="mps-modi" role="group" :aria-label="t('Modalità dell’editor')">
            <button v-for="m in MODI" :key="m.k" type="button" class="mps-modo" :class="{ 'is-on': modo === m.k }" :aria-pressed="modo === m.k ? 'true' : 'false'"
                    @click="modo = m.k" :title="m.spiega">{{ m.nome }}</button>
          </div>
          <!-- Annulla e ripeti, copia e incolla (1.4.1430): quello che ogni editor ha, e qui mancava -->
          <div class="mps-attrezzi" role="group" :aria-label="t('Modifiche')">
            <button type="button" class="mps-attrezzo" :disabled="!puoAnnullare" @click="indietro" :title="t('Torna indietro di un passo (Ctrl+Z)')" :aria-label="t('Torna indietro di un passo (Ctrl+Z)')"><span aria-hidden="true" v-html="ICO.indietro"></span></button>
            <button type="button" class="mps-attrezzo" :disabled="!puoRipetere" @click="avanti" :title="t('Rifai il passo (Ctrl+Maiusc+Z)')" :aria-label="t('Rifai il passo (Ctrl+Maiusc+Z)')"><span aria-hidden="true" v-html="ICO.avanti"></span></button>
            <button type="button" class="mps-attrezzo" :disabled="!selectedLayerId || editingGlobal" @click="copiaLivelli" :title="t('Copia i livelli scelti (Ctrl+C)')" :aria-label="t('Copia i livelli scelti (Ctrl+C)')"><span aria-hidden="true" v-html="ICO.copia"></span></button>
            <button type="button" class="mps-attrezzo" :disabled="!appunti.length" @click="incollaLivelli" :title="t('Incolla (Ctrl+V)') + (appunti.length ? ' · ' + appunti.length : '')" :aria-label="t('Incolla (Ctrl+V)')"><span aria-hidden="true" v-html="ICO.incolla"></span><span v-if="appunti.length" class="mps-attrezzo-n">{{ appunti.length }}</span></button>
          </div>
          <span v-if="avvisoScena" class="mb-text-xs mb-text-green-300 mb-truncate" role="status">{{ avvisoScena }}</span>
        </div>
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
        <!-- Left: le slide in alto, sotto il pannello Livelli a gruppi (1.4.1424) coi livelli di tutte le slide -->
        <div class="mb-flex mb-flex-col mb-shrink-0 mb-min-h-0" :style="{ width: leftPanelWidth + 'px' }">
          <SlideList
            class="mps-colonna-slide"
            :slides="data.slides"
            :activeIndex="activeSlideIndex"
            :globalBackground="data.globalBackground"
            @select="activeSlideIndex = $event; editingGlobal = false"
            @add="addSlide"
            @duplicate="duplicateSlide"
            @remove="removeSlide"
            @import-slides="importSlides"
            @scene="mostraScene = true"
          />

          <LayerTree
            :layers="activeSlide?.layers || []"
            :globalLayers="data.globalLayers || []"
            :selectedId="selectedLayerId"
            :selectedIds="selezione"
            :editingGlobal="editingGlobal"
            :hiddenIds="hiddenLayerIds"
            :lockedIds="lockedLayerIds"
            :soloKey="soloKey"
            :statePreview="statePreview"
            :activeBreakpoint="activeBreakpoint"
            @select="(id, agg) => scegliLivello(id, { aggiungi: agg })"
            @select-many="scegliMolti"
            @da-parte="mettiDaParte"
            @sposta="spostaLivello"
            @select-global="selectGlobalLayer"
            @nascondi="nascondiLivelli"
            @blocca="bloccaLivelli"
            @solo="soloGruppo"
            @guarda="guardaScena"
            @rinomina="rinominaGruppo"
            @move-up="moveLayerUp"
            @move-down="moveLayerDown"
            @remove="removeLayer"
            @add-global="addGlobalLayer"
            @remove-global="removeGlobalLayer"
          />
        </div>

        <!-- Left resize handle -->
        <div class="mps-panel-resize-handle" @pointerdown="startResizeLeft"></div>

        <!-- Center: Canvas + Layer bar + Slide background panel -->
        <div class="mb-flex mb-flex-col mb-flex-1 mb-overflow-hidden">
          <!-- Slide bg controls (collapsible) -->
          <div v-if="showSlideBg" class="mb-bg-gray-850 mb-border-b mb-border-gray-700 mb-p-3">
            <div class="mb-flex mb-items-center mb-justify-between mb-mb-2">
              <span class="mb-text-[10px] mb-text-gray-400 mb-font-semibold mb-uppercase">{{ t('Sfondo della slide') }}</span>
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
                    <NumberScrubber theme="dark" :modelValue="currentBg.kenBurnsPanX || 0" :min="-50" :max="50" :step="5" :defaultValue="0" emitAs="number" unit="%" :ariaLabel="t('Spostamento orizzontale')" @update:modelValue="updateBg('kenBurnsPanX', $event)" />
                  </div>
                  <div class="mb-flex mb-items-center mb-gap-1">
                    <label class="mb-text-[10px] mb-text-gray-400">{{ t('Spostamento verticale') }}</label>
                    <NumberScrubber theme="dark" :modelValue="currentBg.kenBurnsPanY || 0" :min="-50" :max="50" :step="5" :defaultValue="0" emitAs="number" unit="%" :ariaLabel="t('Spostamento verticale')" @update:modelValue="updateBg('kenBurnsPanY', $event)" />
                  </div>
                  <div class="mb-flex mb-items-center mb-gap-1">
                    <label class="mb-text-[10px] mb-text-gray-400">{{ t('Sfocatura all’inizio') }}</label>
                    <NumberScrubber theme="dark" :modelValue="currentBg.kenBurnsBlurStart || 0" :min="0" :max="20" :step="1" :defaultValue="0" emitAs="number" unit="px" :ariaLabel="t('Sfocatura all’inizio')" @update:modelValue="updateBg('kenBurnsBlurStart', $event)" />
                  </div>
                  <div class="mb-flex mb-items-center mb-gap-1">
                    <label class="mb-text-[10px] mb-text-gray-400">{{ t('Sfocatura alla fine') }}</label>
                    <NumberScrubber theme="dark" :modelValue="currentBg.kenBurnsBlurEnd || 0" :min="0" :max="20" :step="1" :defaultValue="0" emitAs="number" unit="px" :ariaLabel="t('Sfocatura alla fine')" @update:modelValue="updateBg('kenBurnsBlurEnd', $event)" />
                  </div>
                </template>
              </div>
              <!-- Video -->
              <div v-if="currentBg.type === 'video'">
                <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-0.5">{{ t('Indirizzo del video') }}</label>
                <div class="mb-flex mb-gap-1">
                  <input :value="currentBg.video" @input="updateBg('video', $event.target.value)" class="mps-inp mb-w-52" :placeholder="t('mp4 o indirizzo YouTube')" />
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
            </div>
          </div>
          <!-- Le scene della slide (1.4.1428, come le scene di Slider Revolution 7 e le varianti di Framer): come la vede chi
               apre la pagina, ogni scelta col suo nome, ogni altro stato, tutti i livelli insieme. Prima un menu «Stato dei
               livelli» coi nomi tecnici («spia = olio»). -->
          <div v-if="scene.length > 2" class="mps-scene-riga" role="group" :aria-label="t('Scena da guardare')">
            <span class="mps-scene-et">{{ t('Scena') }}<InfoTip :testo="t('Quello che si vede sulla tela: all’avvio, dopo una scelta, in un altro stato, o tutti i livelli insieme per sistemarli. Anche l’elenco dei livelli e la linea del tempo seguono la scena.')" :titolo="t('Scena')" /></span>
            <button v-for="s in scene" :key="s.v" type="button" class="mps-scena" :class="{ 'is-on': valoreStatoScelto === s.v, 'is-tutti': s.v === 'tutti' }"
                    :aria-pressed="valoreStatoScelto === s.v ? 'true' : 'false'" @click="scegliStato(s.v)">{{ s.nome }}</button>
          </div>
          <!-- Cosa fa la slide, in numeri (i bollini del pannello Livelli dicono quale livello) -->
          <div v-if="riepilogoAnimazioni.length" class="mps-riepilogo mb-flex mb-flex-wrap mb-items-center mb-gap-x-3 mb-gap-y-1 mb-px-3 mb-py-1 mb-border-b mb-border-gray-700">
            <span class="mb-text-[10px] mb-text-gray-500 mb-flex mb-items-center mb-gap-1">{{ t('In questa slide') }}<InfoTip :testo="t('Quanti livelli fanno qualcosa, e cosa. Nel pannello Livelli lo stesso bollino dice quali: ↕ scorrendo, ⏱ linea del tempo, Aa lettere, ◐ stato, ☝ clic, ∞ continuo, {} CSS, ▭ solo su alcuni schermi. Passa il puntatore su un bollino per leggerlo.')" :titolo="t('Cosa succede in questa slide')" />:</span>
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
              :hiddenIds="nascostiSullaTela"
              :lockedIds="lockedLayerIds"
              :scrollPreview="scrollPreview"
              :statePreview="statePreview"
              :initialStates="statiIniziali"
              @deselect="selectedLayerId = null"
              @select-layer="scegliLivello"
              @update-layers="aggiornaLivelli"
              :selectedIds="selezione"
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
            v-if="hasAnyTimeline && modo === 'animazione'"
            :layers="livelliInScena"
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

        <!-- Right: le interazioni della slide (modalità Interazioni, 1.4.1426) e le proprietà del livello scelto -->
        <div class="mb-flex mb-flex-col mb-min-h-0" :style="{ width: rightPanelWidth + 'px', flexShrink: 0 }">
          <InteractionsPanel
            v-if="modo === 'interazioni'"
            class="mps-colonna-int"
            :layers="activeSlide?.layers || []"
            :selectedId="editingGlobal ? null : selectedLayerId"
            :stati="data.stati || ''"
            :statePreview="statePreview"
            @select="(id) => scegliLivello(id)"
            @guarda="guardaScena"
            @aggiungi-scelta="aggiungiScelta"
            @togli-scelta="togliScelta"
          />
          <MultiPanel
            v-if="livelliScelti.length > 1"
            :key="[...selezione].join(',')"
            class="mps-colonna-prop"
            :layers="livelliScelti"
            :slideLayers="activeSlide?.layers || []"
            @annulla="scegliMolti([])"
            @allinea="allinea"
            @distribuisci="distribuisci"
            @entrata="entrataInsieme"
            @transizione="transizioneInsieme"
            @gruppo="gruppoScelti"
            @etichetta="etichettaScelti"
            @da-parte="(v) => mettiDaParte([...selezione], v)"
            @duplica="duplicaScelti"
            @elimina="eliminaScelti"
          />
          <LayerProperties
            v-else
            class="mps-colonna-prop"
            :layer="selectedLayer"
            :modo="modo"
            :selectedKeyframeId="selectedKeyframeId"
            :activeBreakpoint="activeBreakpoint"
            :slideLayers="activeSlide?.layers || []"
            :scrollPreview="scrollPreview"
            @set-scroll-preview="fermaScorrimento(); scrollPreview = $event"
            @update="updateLayerProp"
            @update-keyframe="onUpdateKeyframe"
            @capture-from-canvas="captureLayerToKeyframe"
          />
        </div>
      </div>

      <!-- Footer: Slider settings -->
      <SliderSettings :settings="data" @update="updateGlobal" />

      <!-- Scene dei temi (1.4.1422) -->
      <SceneLibrary v-if="mostraScene" :haContenuto="haContenuto" :numSlide="data.slides.length" @usa="usaScena" @chiudi="mostraScene = false" />
    </div>
  </Teleport>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, reactive, nextTick, watch } from 'vue';
import { useTilesStore } from '@/stores/tiles';
import { useBuilderStore } from '@/stores/builder';
import { useStylesStore } from '@/stores/styles';
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
import { leggiStati, statiDellaSlide, animazioniLivello, nomeLivello, gruppiLivelli, statoVale } from './livelloUtils.js';
import SceneLibrary from './SceneLibrary.vue';
import LayerTree from './LayerTree.vue';
import InteractionsPanel from './InteractionsPanel.vue';
import MultiPanel from './MultiPanel.vue';
import lucideIcons from './lucideIconsSvg.js';

// Option arrays per FieldSelect (stessi value dei vecchi <option>: dati salvati invariati)
const OPTS_BG_TYPE = [
  { value: 'color', label: t('Colore') },
  { value: 'image', label: t('Immagine') },
  { value: 'video', label: t('Video') },
  { value: 'gradient', label: t('Gradiente') },
  { value: 'transparent', label: t('Trasparente') },
];
const OPTS_KENBURNS_DIR = [
  { value: 'in', label: t('Si avvicina') },
  { value: 'out', label: t('Si allontana') },
];

const props = defineProps({
  tileId: { type: String, required: true },
  // Aperto da «Parti da una scena dei temi» dell'inspector: la libreria delle scene subito (1.4.1422).
  apriScene: { type: Boolean, default: false },
});

const emit = defineEmits(['close']);

const tilesStore = useTilesStore();
const builderStore = useBuilderStore();
const stylesStore = useStylesStore();

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
  { key: 'desktop',  label: t('Computer'),  icon: 'M4 3h16a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2zM8 21h8M12 17v4', width: 1440 },
  { key: 'notebook', label: t('Notebook'), icon: 'M20 16V7a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v9m16 0H4m16 0 1.28 2.55a1 1 0 0 1-.9 1.45H3.62a1 1 0 0 1-.9-1.45L4 16', width: 1240 },
  { key: 'tablet',   label: t('Tablet'),   icon: 'M6 2h12a2 2 0 0 1 2 2v16a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2zM12 18h.01', width: 1024 },
  { key: 'mobile',   label: t('Telefono'),   icon: 'M7 2h10a2 2 0 0 1 2 2v16a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2zM12 18h.01', width: 640 },
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

// --- Scene dei temi (1.4.1422) ---
// Si parte da uno Slider Pro di un tema (Olobuild_Scene_Slider): «Usa questa scena» prende slide, livelli globali e
// impostazioni che lo fanno funzionare (altezze, scorrimento, stati, schede…), non lo sfondo né il bordo della tile;
// «Aggiungi le slide in fondo» solo le slide (e gli stati di partenza). Gli id dei livelli si rifanno: due copie
// della stessa scena non si pestano i piedi (stati, CSS dei livelli), e i riferimenti «mostra o nasconde» seguono.
const DEL_CONTENITORE = ['bg', 'border', 'border_hover', 'border_hover_duration', 'border_effect', 'border_effect_intensity',
  'border_effect_color2', 'border_effect_angle', 'border_effect_speed', 'typography_preset'];
const mostraScene = ref(!!props.apriScene);
const avvisoScena = ref('');
let timerAvviso = null;
// Lo slider ha già del lavoro: le slide non sono quelle con cui arriva (le due d'esempio di quando si trascina, o
// quella vuota). Prova su olo03 del 24 nov: lo slider appena trascinato chiedeva «2 slide vengono sostituite».
const firmaSlide = (slides) => JSON.stringify((slides || []).map((s) => [s.background?.image || '', (s.layers || []).map((l) => l.content || l.imageSrc || l.type)]));
const haContenuto = computed(() => {
  const f = firmaSlide(data.slides);
  return ![prosliderDef.partenza?.slides, prosliderDef.defaults?.slides, [defaultSlide()]].some((p) => p && firmaSlide(p) === f);
});

function rinnovaIds(livelli) {
  const mappa = {};
  const nuovi = (livelli || []).map((l) => {
    const n = { ...l, id: makeId() };
    mappa[l.id] = n.id;
    // il nome parlante che veniva dall'id del tema («cr-velo» → «Velo») resta come nome del livello
    if (!l.name) {
      const daId = nomeLivello(l);
      if (daId && daId !== nomeLivello({ ...l, id: '' })) n.name = daId;
    }
    return n;
  });
  nuovi.forEach((l) => {
    if (l.action?.type === 'toggleLayer' && mappa[l.action.target]) l.action = { ...l.action, target: mappa[l.action.target] };
  });
  return nuovi;
}

function usaScena({ settings, nome, media, colori, modo }) {
  // I colori propri del tema entrati nella Palette (1.4.1433): si rileggono, così compaiono fra le tinte del selettore
  if (colori) {
    fetch(`${window.oloData?.restUrl}global-colors`, { headers: { 'X-WP-Nonce': window.oloData?.nonce }, credentials: 'same-origin' })
      .then((r) => (r.ok ? r.json() : null)).then((l) => { if (Array.isArray(l)) stylesStore.globalColors = l; }).catch(() => {});
  }
  const s = JSON.parse(JSON.stringify(settings || {}));
  if (!Array.isArray(s.slides) || !s.slides.length) return;
  s.slides = s.slides.map((sl) => ({ ...sl, id: makeId(), layers: rinnovaIds(sl.layers) }));
  if (Array.isArray(s.globalLayers)) s.globalLayers = rinnovaIds(s.globalLayers);
  if (modo === 'aggiungi') {
    const da = data.slides.length;
    data.slides.push(...s.slides);
    if (s.stati) data.stati = [data.stati, s.stati].filter((x) => String(x || '').trim()).join('; ');
    activeSlideIndex.value = da;
  } else {
    const def = prosliderDef.defaults;
    for (const k of new Set([...Object.keys(def), ...Object.keys(s)])) {
      if (DEL_CONTENITORE.includes(k)) continue;
      data[k] = k in s ? s[k] : JSON.parse(JSON.stringify(def[k]));
    }
    activeSlideIndex.value = 0;
  }
  selectedLayerId.value = null;
  editingLayerId.value = null;
  editingGlobal.value = false;
  mostraScene.value = false;
  // Solo le foto e i video che dovevano copiarsi e non ci sono riusciti: i disegni SVG restano apposta nella libreria
  // (WordPress non li accetta nella Libreria media).
  const fallite = media?.fallite ? ' ' + t('(%d file non copiati: restano all’indirizzo della libreria)').replace('%d', media.fallite) : '';
  const nuoviColori = colori ? ' ' + (colori === 1 ? t('1 colore del tema è entrato nella Palette.') : t('%d colori del tema sono entrati nella Palette.').replace('%d', colori)) : '';
  avvisoScena.value = t('Scena di %s pronta: cambia testi, foto e link, poi «Salva e chiudi».').replace('%s', nome || '') + fallite + nuoviColori;
  clearTimeout(timerAvviso);
  timerAvviso = setTimeout(() => { avvisoScena.value = ''; }, 12000);
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
  const responsiveKeys = ['x', 'y', 'width', 'height', 'fontSize', 'visible', 'textAlign', 'letterSpacing'];

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
  const responsiveKeys = ['x', 'y', 'width', 'height', 'fontSize', 'visible', 'textAlign', 'letterSpacing'];
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

// --- Pannello Livelli a gruppi (1.4.1424) ---
// Occhio, lucchetto e «solo» valgono solo nell'editor (come in Slider Revolution 7 e After Effects): il sito non li
// vede e non si salvano. Bloccato = sulla tela non si prende né si sposta, i clic passano al livello sotto.
const lockedLayerIds = reactive(new Set());
const soloKey = ref(null);
const soloIds = ref(null);
function nascondiLivelli(ids, nascondi) {
  (ids || []).forEach((id) => (nascondi ? hiddenLayerIds.add(id) : hiddenLayerIds.delete(id)));
}
function bloccaLivelli(ids, blocca) {
  (ids || []).forEach((id) => (blocca ? lockedLayerIds.add(id) : lockedLayerIds.delete(id)));
  if (blocca && ids?.includes(selectedLayerId.value)) selectedLayerId.value = null;
}
function soloGruppo(key, ids) {
  soloKey.value = key;
  soloIds.value = key ? new Set(ids) : null;
}
// Sulla tela: i nascosti con l'occhio e, col «solo», tutti quelli fuori dal gruppo
const nascostiSullaTela = computed(() => {
  if (!soloIds.value) return hiddenLayerIds;
  const s = new Set(hiddenLayerIds);
  (activeSlide.value?.layers || []).forEach((l) => { if (!soloIds.value.has(l.id)) s.add(l.id); });
  return s;
});
watch(activeSlideIndex, () => soloGruppo(null, []));
// «Mostra sulla tela» di una scelta: lo stato che la fa vedere (null = di nuovo com'è all'avvio)
function guardaScena(v) {
  statePreview.value = v ? { [v.stato]: v.valore } : {};
}
// --- Annulla e ripeti (1.4.1430) ---
// Una fotografia dei dati dopo ogni modifica (ferma da 350 ms: un trascinamento è un passo solo), fino a 100 passi.
// Ctrl+Z / Cmd+Z indietro, Ctrl+Maiusc+Z o Ctrl+Y avanti; dentro un campo di testo restano quelli del campo.
const ICO = { indietro: lucideIcons['undo-2'] || '↶', avanti: lucideIcons['redo-2'] || '↷', copia: lucideIcons['copy'] || '⧉', incolla: lucideIcons['clipboard-paste'] || '⎘' };
const storia = [];
let posStoria = -1;
let applicando = false;
let timerStoria = null;
const puoAnnullare = ref(false);
const puoRipetere = ref(false);
function aggiornaStoria() { puoAnnullare.value = posStoria > 0; puoRipetere.value = posStoria < storia.length - 1; }
function registraPasso() {
  if (applicando) return;
  const f = JSON.stringify(data);
  if (posStoria >= 0 && storia[posStoria] === f) return;
  storia.splice(posStoria + 1);
  storia.push(f);
  if (storia.length > 100) storia.shift();
  posStoria = storia.length - 1;
  aggiornaStoria();
}
function applicaPasso(f) {
  applicando = true;
  const o = JSON.parse(f);
  for (const k of Object.keys(data)) if (!(k in o)) delete data[k];
  Object.assign(data, o);
  if (activeSlideIndex.value >= (data.slides || []).length) activeSlideIndex.value = Math.max(0, (data.slides || []).length - 1);
  const esiste = (id) => (activeSlide.value?.layers || []).some((l) => l.id === id) || (data.globalLayers || []).some((l) => l.id === id);
  if (selectedLayerId.value && !esiste(selectedLayerId.value)) selectedLayerId.value = null;
  selezione.value = new Set([...selezione.value].filter(esiste));
  nextTick(() => { applicando = false; aggiornaStoria(); });
}
function indietro() {
  clearTimeout(timerStoria);
  registraPasso();
  if (posStoria <= 0) return;
  posStoria--;
  applicaPasso(storia[posStoria]);
}
function avanti() {
  if (posStoria >= storia.length - 1) return;
  posStoria++;
  applicaPasso(storia[posStoria]);
}
watch(data, () => { if (applicando) return; clearTimeout(timerStoria); timerStoria = setTimeout(registraPasso, 350); }, { deep: true });
onMounted(() => registraPasso());

// --- Copia, incolla, duplica (1.4.1430) ---
// Gli appunti restano finché l'editor è aperto e, se il browser lo lascia, anche per il prossimo slider (si incolla
// l'apertura di un tema nello slider di un'altra pagina). Incollati: id nuovi, un passo più in là se sono nella stessa slide.
const CHIAVE_APPUNTI = 'olo_mps_appunti';
const appunti = ref((() => { try { const a = JSON.parse(localStorage.getItem(CHIAVE_APPUNTI) || '[]'); return Array.isArray(a) ? a : []; } catch (_) { return []; } })());
function copiaLivelli() {
  if (editingGlobal.value) return;
  const ls = livelliScelti.value.length ? livelliScelti.value : (selectedLayer.value ? [selectedLayer.value] : []);
  if (!ls.length) return;
  appunti.value = JSON.parse(JSON.stringify(ls));
  try { localStorage.setItem(CHIAVE_APPUNTI, JSON.stringify(appunti.value)); } catch (_) { /* senza memoria del browser restano solo qui */ }
  avvisoScena.value = (ls.length === 1 ? t('1 livello copiato') : t('%d livelli copiati').replace('%d', ls.length)) + ' · ' + t('Ctrl+V per incollarli');
  clearTimeout(timerAvviso);
  timerAvviso = setTimeout(() => { avvisoScena.value = ''; }, 5000);
}
function incollaLivelli() {
  const layers = activeSlide.value?.layers;
  if (!layers || !appunti.value.length) return;
  const stessi = appunti.value.some((a) => layers.some((l) => l.id === a.id));
  const copie = rinnovaIds(JSON.parse(JSON.stringify(appunti.value))).map((c) => {
    if (stessi) { c.x = arrot((Number(c.x) || 0) + 2); c.y = arrot((Number(c.y) || 0) + 2); }
    return c;
  });
  layers.push(...copie);
  scegliMolti(copie.map((c) => c.id));
}
function duplicaLivelli() {
  if (livelliScelti.value.length > 1) { duplicaScelti(); return; }
  if (!selectedLayer.value || editingGlobal.value) return;
  selezione.value = new Set([selectedLayer.value.id]);
  duplicaScelti();
}

// --- Più livelli insieme (1.4.1428) ---
// La scelta: il livello «principale» resta selectedLayerId (le sue proprietà, la sua linea del tempo); gli altri scelti
// con Maiusc/Ctrl + clic sulla tela o nell'elenco, o con «Scegli tutto il gruppo», si spostano e si cambiano insieme.
const selezione = ref(new Set());
function scegliLivello(id, opz = {}) {
  editingGlobal.value = false;
  if (opz.aggiungi) {
    const s = new Set(selezione.value);
    if (selectedLayerId.value && !s.has(selectedLayerId.value)) s.add(selectedLayerId.value);
    if (s.has(id) && s.size > 1) {
      s.delete(id);
      if (selectedLayerId.value === id) selectedLayerId.value = [...s][s.size - 1];
    } else {
      s.add(id);
      selectedLayerId.value = id;
    }
    selezione.value = s;
    return;
  }
  if (opz.mantieni && selezione.value.has(id)) { selectedLayerId.value = id; return; }
  selezione.value = new Set([id]);
  selectedLayerId.value = id;
}
function scegliMolti(ids) {
  editingGlobal.value = false;
  const s = new Set((ids || []).filter((id) => !lockedLayerIds.has(id)));
  selezione.value = s;
  selectedLayerId.value = s.size ? [...s][0] : null;
}
watch(selectedLayerId, (id) => {
  if (!id) { if (selezione.value.size) selezione.value = new Set(); return; }
  if (!selezione.value.has(id)) selezione.value = new Set([id]);
});
watch(activeSlideIndex, () => { selezione.value = new Set(); });
const livelliScelti = computed(() => (editingGlobal.value ? [] : (activeSlide.value?.layers || []).filter((l) => selezione.value.has(l.id))));
// La posizione di un livello sullo schermo che si sta guardando (come resolveLayerValues della tela)
function posVista(l) {
  const bp = activeBreakpoint.value;
  const m = { x: l.x, y: l.y };
  if (bp !== 'desktop' && l.responsive) {
    const chain = ['notebook', 'tablet', 'mobile'];
    for (let k = 0; k <= chain.indexOf(bp); k++) {
      const ov = l.responsive?.[chain[k]];
      if (ov && ov.x !== undefined) m.x = ov.x;
      if (ov && ov.y !== undefined) m.y = ov.y;
    }
  }
  return { x: Number(m.x) || 0, y: Number(m.y) || 0 };
}
const arrot = (v) => Math.round(Math.max(-50, Math.min(150, v)) * 10) / 10;
function aggiornaLivelli(lista) { (lista || []).forEach((u) => updateLayer(u.id, { x: u.x, y: u.y })); }
function spostaScelti(dx, dy) {
  const ls = livelliScelti.value.length ? livelliScelti.value : (selectedLayer.value && !editingGlobal.value ? [selectedLayer.value] : []);
  ls.filter((l) => !lockedLayerIds.has(l.id)).forEach((l) => { const p = posVista(l); updateLayer(l.id, { x: arrot(p.x + dx), y: arrot(p.y + dy) }); });
}
function allinea(dir) {
  const ls = livelliScelti.value;
  if (ls.length < 2) return;
  const k = dir === 'sinistra' ? 'x' : 'y';
  const m = Math.min(...ls.map((l) => posVista(l)[k]));
  ls.forEach((l) => updateLayer(l.id, { [k]: m }));
}
function distribuisci(asse) {
  const ls = livelliScelti.value.map((l) => ({ l, p: posVista(l) })).sort((a, b) => a.p[asse] - b.p[asse]);
  if (ls.length < 3) return;
  const a = ls[0].p[asse], z = ls[ls.length - 1].p[asse];
  ls.forEach((o, i) => updateLayer(o.l.id, { [asse]: arrot(a + ((z - a) * i) / (ls.length - 1)) }));
}
// dall'alto in basso, poi da sinistra a destra, come si legge
const inLettura = (ls) => ls.slice().sort((a, b) => posVista(a).y - posVista(b).y || posVista(a).x - posVista(b).x);
function entrataInsieme({ animIn, base, cascata }) {
  inLettura(livelliScelti.value).forEach((l, i) => updateLayer(l.id, { ...(animIn ? { animIn } : {}), animInDelay: Math.round((base || 0) + i * (cascata || 0)) }));
}
function transizioneInsieme({ effetto, durata, cascata }) {
  inLettura(livelliScelti.value.filter((l) => l.stato?.nome)).forEach((l, i) => updateLayer(l.id, { stato: { ...l.stato, effetto, durata, ritardo: Math.round(i * (cascata || 0)) } }));
}
function cambiaScelti(fn) {
  const layers = activeSlide.value?.layers;
  if (!layers) return;
  layers.forEach((l, i) => { if (selezione.value.has(l.id)) layers.splice(i, 1, fn({ ...l })); });
}
function gruppoScelti(nome) { cambiaScelti((l) => { if (nome) l.gruppo = nome; else delete l.gruppo; return l; }); }
function etichettaScelti(k) { cambiaScelti((l) => { if (k) l.etichetta = k; else delete l.etichetta; return l; }); }
function mettiDaParte(ids, si) {
  const layers = activeSlide.value?.layers;
  if (!layers) return;
  const set = new Set(ids || []);
  layers.forEach((l, i) => { if (set.has(l.id)) { const n = { ...l }; if (si) n.daParte = true; else delete n.daParte; layers.splice(i, 1, n); } });
}
function duplicaScelti() {
  const layers = activeSlide.value?.layers;
  if (!layers || !livelliScelti.value.length) return;
  const dove = Math.max(...livelliScelti.value.map((l) => layers.indexOf(l)));
  const copie = livelliScelti.value.map((l) => {
    const c = JSON.parse(JSON.stringify(l));
    c.id = makeId();
    c.x = arrot((Number(c.x) || 0) + 2);
    c.y = arrot((Number(c.y) || 0) + 2);
    if (c.name) c.name = c.name + ' ' + t('(copia)');
    return c;
  });
  layers.splice(dove + 1, 0, ...copie);
  scegliMolti(copie.map((c) => c.id));
}
function eliminaScelti() {
  const layers = activeSlide.value?.layers;
  if (!layers) return;
  for (let i = layers.length - 1; i >= 0; i--) if (selezione.value.has(layers[i].id)) layers.splice(i, 1);
  selezione.value = new Set();
  selectedLayerId.value = null;
}
// Trascinare un livello nell'elenco: ordine sulla slide e gruppo d'arrivo (1.4.1428)
function spostaLivello({ id, su, dove, gruppo }) {
  const layers = activeSlide.value?.layers;
  if (!layers) return;
  const i = layers.findIndex((l) => l.id === id);
  if (i < 0) return;
  const l = { ...layers[i] };
  const cliccabileDiScelta = l.action?.type === 'setState';
  const stessoGruppo = gruppo && gruppo.ids.includes(id);
  if (gruppo && !stessoGruppo) {
    if ((gruppo.tipo === 'scelta' || gruppo.tipo === 'prima' || gruppo.tipo === 'stato') && !cliccabileDiScelta) {
      // in una scelta: compare con quella scelta (o finché non si è scelto niente, o in quello stato)
      const fx = l.stato || {};
      l.stato = { nome: gruppo.stato, vale: gruppo.tipo === 'stato' ? gruppo.vale : gruppo.valore, effetto: fx.effetto || 'fade', durata: fx.durata ?? 400, ritardo: fx.ritardo ?? 0 };
      delete l.gruppo;
    } else if (gruppo.tipo === 'manuale') {
      if (!cliccabileDiScelta) delete l.stato;
      l.gruppo = gruppo.nome;
    } else if (!cliccabileDiScelta) {
      // in un gruppo automatico (testi, forme, veli, serie…): fuori da scelte e stati; se lì non ci va da solo, il gruppo
      // diventa manuale con lo stesso nome, così il livello ci resta
      delete l.stato;
      delete l.gruppo;
    }
  }
  layers.splice(i, 1);
  let j = su ? layers.findIndex((x) => x.id === su) : -1;
  let dov = dove;
  if (j < 0 && gruppo) {
    j = Math.max(-1, ...gruppo.ids.filter((x) => x !== id).map((x) => layers.findIndex((y) => y.id === x)));
    dov = 'sopra';
  }
  // l'elenco va dal davanti al dietro: «sopra» una riga = davanti = dopo di lei nella slide
  layers.splice(j < 0 ? layers.length : (dov === 'sotto' ? j : j + 1), 0, l);
  if (gruppo && !stessoGruppo && !['scelta', 'prima', 'stato', 'manuale'].includes(gruppo.tipo) && !cliccabileDiScelta) {
    const dovè = gruppiLivelli(layers).find((g) => g.livelli.some((x) => x.id === id));
    if (dovè && dovè.key !== gruppo.key) {
      const tutti = new Set([...gruppo.ids, id]);
      layers.forEach((x, k) => { if (tutti.has(x.id)) layers.splice(k, 1, { ...x, gruppo: gruppo.nome }); });
    }
  }
  scegliLivello(id);
}

// --- Le scene (1.4.1428) ---
const scene = computed(() => {
  const nomi = {};
  for (const g of gruppiLivelli(activeSlide.value?.layers || [])) if (g.tipo === 'scelta') nomi[g.stato + '=' + g.valore] = g.nomeManuale || g.nome || g.valore;
  const out = [{ v: 'avvio', nome: t('All’avvio') }];
  for (const [n, valori] of Object.entries(statiSlide.value)) {
    for (const v of valori) out.push({ v: n + '=' + v, nome: nomi[n + '=' + v] || (n + ': ' + v) });
  }
  out.push({ v: 'tutti', nome: t('Tutti i livelli insieme') });
  return out;
});
// I livelli che si vedono nella scena scelta (per la linea del tempo)
const livelliInScena = computed(() => {
  const ls = activeSlide.value?.layers || [];
  const sp = statePreview.value;
  if (!sp) return ls;
  return ls.filter((l) => {
    const n = String(l.stato?.nome || '').trim().toLowerCase();
    if (!n) return true;
    const v = Object.prototype.hasOwnProperty.call(sp, n) ? sp[n] : (statiIniziali.value[n] ?? '');
    return statoVale(l.stato.vale, v);
  });
});

// --- Modalità e interazioni (1.4.1426) ---
const MODI = [
  { k: 'disegno', nome: t('Disegno'), spiega: t('Testi, foto, posizione e stile dei livelli') },
  { k: 'animazione', nome: t('Animazione'), spiega: t('Entrate, movimento con lo scorrimento e linea del tempo') },
  { k: 'interazioni', nome: t('Interazioni'), spiega: t('Cosa succede al clic: scelte, stati e azioni') },
];
const modo = ref('disegno');
const nomeStatoE = (n) => String(n || '').trim().toLowerCase().replace(/[^a-z0-9_-]/g, '');
function sostituisciValore(vale, vecchio, nuovo) {
  const s = String(vale ?? '').trim();
  const neg = s.startsWith('!');
  return (neg ? '!' : '') + (neg ? s.slice(1) : s).split(',').map((x) => (x.trim() === vecchio ? nuovo : x.trim())).join(',');
}
// «Aggiungi una scelta»: copia il gruppo dell'ultima scelta della fila (il livello cliccabile e quelli che compaiono),
// con un valore nuovo; il livello cliccabile e ciò che gli sta sopra (la spia spenta di Cric) scivolano di un passo sulla
// fila, anche su notebook, tablet e telefono; il display resta dov'è. Poi la tela mostra la scelta nuova, da riempire.
function aggiungiScelta(stato) {
  const layers = activeSlide.value?.layers;
  if (!layers) return;
  const voci = gruppiLivelli(layers)
    .filter((g) => g.tipo === 'scelta' && g.stato === stato)
    .map((g) => ({ g, tr: g.livelli.find((l) => l.action?.type === 'setState') }))
    .filter((v) => v.tr)
    .sort((a, b) => (Number(a.tr.x) || 0) - (Number(b.tr.x) || 0));
  if (!voci.length) return;
  const ultimo = voci[voci.length - 1];
  const prima = voci[voci.length - 2];
  const usati = new Set(voci.map((v) => v.g.valore));
  let n = voci.length + 1;
  let nuovo = 'scelta-' + n;
  while (usati.has(nuovo)) nuovo = 'scelta-' + (++n);
  const vecchio = ultimo.g.valore;
  const posto = (l, bp) => (bp ? l.responsive?.[bp] : l);
  const passo = (bp) => {
    const a = Number(posto(ultimo.tr, bp)?.x);
    const b = prima ? Number(posto(prima.tr, bp)?.x) : NaN;
    if (Number.isFinite(a) && Number.isFinite(b) && a !== b) return a - b;
    if (bp) return null;
    const w = Number(ultimo.tr.width);
    return Number.isFinite(w) ? w + 2 : 6;
  };
  const stessoPosto = (l, bp) => {
    const a = posto(l, bp), b = posto(ultimo.tr, bp);
    return !!a && !!b && a.x !== undefined && Number(a.x) === Number(b.x) && Number(a.y) === Number(b.y);
  };
  const membri = layers.filter((l) => ultimo.g.livelli.includes(l));
  const dove = Math.max(...membri.map((l) => layers.indexOf(l)));
  const copie = membri.map((l) => {
    const c = JSON.parse(JSON.stringify(l));
    c.id = makeId();
    if (c.action?.type === 'setState' && nomeStatoE(c.action.target) === stato) {
      c.action.value = nuovo;
      c.name = t('Nuova scelta') + ' ' + n;
      if (c.action.label) c.action.label = c.name;
    }
    if (c.stato && nomeStatoE(c.stato.nome) === stato) c.stato = { ...c.stato, vale: sostituisciValore(c.stato.vale, vecchio, nuovo) };
    if (stessoPosto(l, null)) c.x = Math.round(((Number(c.x) || 0) + passo(null)) * 100) / 100;
    for (const bp of ['notebook', 'tablet', 'mobile']) {
      const d = passo(bp);
      if (d != null && stessoPosto(l, bp)) c.responsive[bp].x = Math.round((Number(c.responsive[bp].x) + d) * 100) / 100;
    }
    return c;
  });
  // i livelli «finché non si è scelto niente» scritti con l'elenco di tutti i valori (!a,b,c) prendono anche il nuovo
  layers.forEach((l, i) => {
    const v = String(l.stato?.vale ?? '').trim();
    if (nomeStatoE(l.stato?.nome) !== stato || !v.startsWith('!')) return;
    const lista = v.slice(1).split(',').map((x) => x.trim());
    if ([...usati].every((x) => lista.includes(x))) layers.splice(i, 1, { ...l, stato: { ...l.stato, vale: v + ',' + nuovo } });
  });
  layers.splice(dove + 1, 0, ...copie);
  editingGlobal.value = false;
  // scelto il testo più grande della scelta nuova, da riscrivere subito (la spia, scelta, nella sua scena è nascosta:
  // i riquadri di selezione finivano nell'angolo della tela; prova su olo03 del 24 nov)
  const testi = copie.filter((c) => c.type === 'text' && c.stato?.nome);
  const titolo = testi.sort((a, b) => (Number(b.fontSize) || 0) - (Number(a.fontSize) || 0))[0];
  selectedLayerId.value = (titolo || copie.find((c) => c.action?.type === 'setState') || copie[0]).id;
  statePreview.value = { [stato]: nuovo };
}
// «Togli questa scelta»: i suoi livelli, e il suo valore dagli elenchi «finché non è …» degli altri
function togliScelta({ stato, valore }) {
  const layers = activeSlide.value?.layers;
  if (!layers) return;
  const g = gruppiLivelli(layers).find((x) => x.tipo === 'scelta' && x.stato === stato && x.valore === valore);
  if (!g) return;
  const via = new Set(g.livelli.map((l) => l.id));
  for (let i = layers.length - 1; i >= 0; i--) {
    const l = layers[i];
    if (via.has(l.id)) { layers.splice(i, 1); continue; }
    const v = String(l.stato?.vale ?? '').trim();
    if (nomeStatoE(l.stato?.nome) === stato && v.startsWith('!') && v.includes(',')) {
      layers.splice(i, 1, { ...l, stato: { ...l.stato, vale: '!' + v.slice(1).split(',').map((x) => x.trim()).filter((x) => x !== valore).join(',') } });
    }
  }
  if (via.has(selectedLayerId.value)) selectedLayerId.value = null;
  if (statePreview.value && statePreview.value[stato] === valore) statePreview.value = {};
}

// Rinomina un gruppo: il nome va nei suoi livelli («gruppo»); vuoto = torna automatico
function rinominaGruppo(ids, nome) {
  const layers = activeSlide.value?.layers || [];
  layers.forEach((l, i) => {
    if (!ids.includes(l.id)) return;
    const n = { ...l };
    if (nome) n.gruppo = nome; else delete n.gruppo;
    layers.splice(i, 1, n);
  });
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
  // Canc in un campo cancella il testo, non il livello scelto (1.4.1422: prima toglieva il livello).
  const campo = e.target?.closest?.('input, textarea, select, [contenteditable="true"]');
  // Annulla, ripeti, copia, incolla, duplica (1.4.1430): fuori dai campi di testo, che hanno i loro
  if ((e.ctrlKey || e.metaKey) && !campo && !editingLayerId.value) {
    const k = e.key.toLowerCase();
    if (k === 'z' && !e.shiftKey) { e.preventDefault(); indietro(); return; }
    if ((k === 'z' && e.shiftKey) || k === 'y') { e.preventDefault(); avanti(); return; }
    if (k === 'c') { e.preventDefault(); copiaLivelli(); return; }
    if (k === 'v') { e.preventDefault(); incollaLivelli(); return; }
    if (k === 'd') { e.preventDefault(); duplicaLivelli(); return; }
  }
  // Frecce: spostano i livelli scelti di mezzo punto (con Maiusc di 5), sullo schermo che si sta guardando (1.4.1428)
  if (!campo && !editingLayerId.value && selectedLayerId.value && ['ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown'].includes(e.key)) {
    e.preventDefault();
    const d = e.shiftKey ? 5 : 0.5;
    spostaScelti(e.key === 'ArrowLeft' ? -d : e.key === 'ArrowRight' ? d : 0, e.key === 'ArrowUp' ? -d : e.key === 'ArrowDown' ? d : 0);
    return;
  }
  if (e.key === 'Delete' && !campo && selezione.value.size > 1 && !editingLayerId.value) {
    eliminaScelti();
    return;
  }
  if (e.key === 'Delete' && !campo && selectedLayerId.value && !editingLayerId.value) {
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
// 260: le slide e, sotto, il pannello Livelli coi nomi interi (1.4.1424; prima 180 per le sole miniature)
const leftPanelWidth = ref(260);
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
  // Aperto con la libreria delle scene: il fuoco resta nella sua ricerca (l'editor si monta dopo e lo prendeva).
  if (!mostraScene.value) nextTick(() => modalEl.value?.focus());
});

onUnmounted(() => {
  stopTimelinePlay();
  fermaScorrimento();
  window.removeEventListener('pointermove', onPanelResizeMove);
  window.removeEventListener('pointerup', onPanelResizeUp);
});
</script>

<style scoped>
/* Le slide in alto prendono al massimo un terzo abbondante: il resto è del pannello Livelli (1.4.1424) */
.mps-colonna-slide { max-height: 38%; flex: none; }
.mps-scene-riga { display: flex; flex-wrap: wrap; align-items: center; gap: 4px; padding: 5px 12px; border-bottom: 1px solid #374151; background: #111827; }
.mps-scene-et { display: inline-flex; align-items: center; gap: 3px; font-size: 10px; font-weight: 600; letter-spacing: .04em; text-transform: uppercase; color: #fcd34d; margin-right: 4px; }
.mps-scena { font: inherit; font-size: 11px; padding: 3px 9px; border-radius: 999px; border: 1px solid #374151; background: transparent; color: #d1d5db; cursor: pointer; }
.mps-scena:hover { border-color: #6b7280; }
.mps-scena.is-on { background: #facc15; border-color: #facc15; color: #1f2937; font-weight: 600; }
.mps-scena.is-tutti { border-style: dashed; }
.mps-scena:focus-visible { outline: 2px solid #fff; outline-offset: 1px; }
/* Modalità Interazioni: le regole in alto (al massimo metà), sotto le proprietà del livello (1.4.1426) */
.mps-colonna-int { max-height: 50%; flex: none; border-left: 1px solid #374151; }
.mps-colonna-prop { flex: 1; min-height: 0; height: auto; }
.mps-attrezzi { display: inline-flex; gap: 2px; }
.mps-attrezzo { font: inherit; position: relative; display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 26px; padding: 0; color: #d1d5db; background: transparent; border: 1px solid transparent; border-radius: 5px; cursor: pointer; }
.mps-attrezzo:hover:not(:disabled) { background: #374151; color: #fff; }
.mps-attrezzo:disabled { opacity: .35; cursor: default; }
.mps-attrezzo :deep(svg) { width: 15px; height: 15px; }
.mps-attrezzo-n { position: absolute; top: -3px; right: -3px; min-width: 14px; height: 14px; padding: 0 3px; border-radius: 7px; font-size: 9px; line-height: 14px; background: var(--olo-color-primary, #e1474f); color: var(--olo-color-primary-contrast, #fff); }
.mps-attrezzo:focus-visible { outline: 2px solid #fff; outline-offset: -2px; }
.mps-modi { display: inline-flex; border: 1px solid #374151; border-radius: 6px; overflow: hidden; }
.mps-modo { font: inherit; font-size: 11px; font-weight: 600; padding: 4px 12px; color: #9ca3af; background: transparent; border: 0; cursor: pointer; }
.mps-modo + .mps-modo { border-left: 1px solid #374151; }
.mps-modo:hover { color: #f3f4f6; }
.mps-modo.is-on { background: var(--olo-color-primary, #e1474f); color: var(--olo-color-primary-contrast, #fff); }
.mps-modo:focus-visible { outline: 2px solid #fff; outline-offset: -2px; }
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
