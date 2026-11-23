<template>
  <div class="mb-bg-gray-900 mb-border-l mb-border-gray-700 mb-overflow-y-auto mb-flex mb-flex-col mb-h-full">
    <template v-if="layer">
      <!-- Header -->
      <div class="mb-p-2 mb-border-b mb-border-gray-700 mb-text-xs mb-font-semibold mb-text-gray-300">
        {{ t('Livello') }} {{ nomeTipo }}
        <!-- Il nome del livello (1.4.1352): quello che compare nella barra dei livelli. Vuoto = il testo, il file o il tipo. -->
        <div class="mb-mt-1.5 mb-font-normal">
          <label class="mps-label mb-flex mb-items-center mb-gap-1">{{ t('Nome del livello') }}<InfoTip :testo="t('Serve solo a te, per riconoscere il livello nella barra dei livelli: sul sito non si vede. Vuoto, il livello prende il nome dal suo testo o dal file.')" :titolo="t('Nome del livello')" /></label>
          <input :value="layer.name || ''" @input="up('name', $event.target.value)" class="mps-input" :placeholder="nomeSuggerito" />
        </div>
      </div>

      <!-- Tabs -->
      <div class="mb-flex mb-bg-gray-800 mb-border-b mb-border-gray-700">
        <button
          v-for="tab in tabs"
          :key="tab"
          @click="activeTab = tab"
          :class="[
            'mb-flex-1 mb-py-1.5 mb-text-[10px] mb-font-medium mb-transition-colors',
            activeTab === tab ? 'mb-text-primary-400 mb-border-b-2 mb-border-primary-400' : 'mb-text-gray-500 hover:mb-text-gray-300'
          ]"
        >{{ tab }}</button>
      </div>

      <div class="mb-flex-1 mb-p-3 mb-space-y-3 mb-overflow-y-auto">

        <!-- ===== CONTENUTO ===== -->
        <template v-if="activeTab === 'Contenuto'">
          <!-- Text -->
          <template v-if="layer.type === 'text'">
            <div>
              <label class="mps-label">{{ t('Testo') }}</label>
              <textarea :value="layer.content" @input="up('content', $event.target.value)" rows="3"
                class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded mb-px-2 mb-py-1 mb-text-sm mb-text-gray-900 mb-resize-y"></textarea>
            </div>
            <div>
              <label class="mps-label">{{ t('Tag') }}</label>
              <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.tag" :options="OPTS_TAG" @update:model-value="up('tag', $event)" />
            </div>
          </template>
          <!-- Image -->
          <template v-if="layer.type === 'image'">
            <div>
              <label class="mps-label">{{ t('Indirizzo dell’immagine') }}</label>
              <div class="mb-flex mb-gap-1">
                <input :value="layer.imageSrc" @input="up('imageSrc', $event.target.value)" class="mps-input mb-flex-1" placeholder="https://..." />
                <button @click="pickImage" class="mb-px-2 mb-bg-gray-600 mb-rounded mb-text-xs mb-text-gray-300 hover:mb-bg-gray-500">{{ t('Sfoglia') }}</button>
              </div>
            </div>
            <div>
              <label class="mps-label">{{ t('Testo alternativo') }}</label>
              <input :value="layer.imageAlt" @input="up('imageAlt', $event.target.value)" class="mps-input" :placeholder="t('Cosa mostra l’immagine (per chi non la vede)')" />
            </div>
          </template>
          <!-- Button -->
          <template v-if="layer.type === 'button'">
            <div>
              <label class="mps-label">{{ t('Etichetta') }}</label>
              <input :value="layer.content" @input="up('content', $event.target.value)" class="mps-input" />
            </div>
            <div>
              <label class="mps-label">{{ t('Indirizzo') }}</label>
              <input :value="layer.buttonUrl" @input="up('buttonUrl', $event.target.value)" class="mps-input" placeholder="https://..." />
            </div>
            <div>
              <label class="mps-label">{{ t('Destinazione') }}</label>
              <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.buttonTarget" :options="OPTS_LINK_TARGET" @update:model-value="up('buttonTarget', $event)" />
            </div>
          </template>
          <!-- Icon -->
          <template v-if="layer.type === 'icon'">
            <div>
              <label class="mps-label">{{ t('Nome icona') }}</label>
              <div class="mb-flex mb-gap-1 mb-items-center">
                <span v-if="layer.iconName && iconsSvg[layer.iconName]" class="mps-icon-preview" v-html="iconsSvg[layer.iconName]"></span>
                <input :value="layer.iconName" @input="up('iconName', $event.target.value)" class="mps-input mb-flex-1" placeholder="star" />
                <button @click="showIconPicker = true" class="mb-px-2 mb-bg-gray-600 mb-rounded mb-text-xs mb-text-gray-300 hover:mb-bg-gray-500">{{ t('Sfoglia') }}</button>
              </div>
            </div>
            <IconPicker
              v-if="showIconPicker"
              @select="up('iconName', $event); showIconPicker = false"
              @close="showIconPicker = false"
            />
          </template>
          <!-- Video -->
          <template v-if="layer.type === 'video'">
            <div>
              <label class="mps-label">{{ t('Indirizzo del video') }}</label>
              <div class="mb-flex mb-gap-1">
                <input :value="layer.videoSrc" @input="up('videoSrc', $event.target.value)" class="mps-input mb-flex-1" :placeholder="t('mp4 o indirizzo YouTube')" />
                <button @click="pickVideo" class="mb-px-2 mb-bg-gray-600 mb-rounded mb-text-xs mb-text-gray-300 hover:mb-bg-gray-500">{{ t('Sfoglia') }}</button>
              </div>
            </div>
            <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
              <input type="checkbox" :checked="layer.videoAutoplay" @change="up('videoAutoplay', $event.target.checked)" class="mb-rounded" />
              <span class="mb-text-[11px] mb-text-gray-400">{{ t('Riproduzione automatica') }}</span>
            </label>
            <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
              <input type="checkbox" :checked="layer.videoMuted" @change="up('videoMuted', $event.target.checked)" class="mb-rounded" />
              <span class="mb-text-[11px] mb-text-gray-400">{{ t('Muto') }}</span>
            </label>
            <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
              <input type="checkbox" :checked="layer.videoLoop" @change="up('videoLoop', $event.target.checked)" class="mb-rounded" />
              <span class="mb-text-[11px] mb-text-gray-400">{{ t('Ripeti') }}</span>
            </label>
          </template>
          <!-- Audio -->
          <template v-if="layer.type === 'audio'">
            <div>
              <label class="mps-label">{{ t('Indirizzo dell’audio') }}</label>
              <div class="mb-flex mb-gap-1">
                <input :value="layer.audioSrc" @input="up('audioSrc', $event.target.value)" class="mps-input mb-flex-1" :placeholder="t('mp3 o indirizzo dell’audio')" />
                <button @click="pickAudio" class="mb-px-2 mb-bg-gray-600 mb-rounded mb-text-xs mb-text-gray-300 hover:mb-bg-gray-500">{{ t('Sfoglia') }}</button>
              </div>
            </div>
            <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
              <input type="checkbox" :checked="layer.audioAutoplay" @change="up('audioAutoplay', $event.target.checked)" class="mb-rounded" />
              <span class="mb-text-[11px] mb-text-gray-400">{{ t('Riproduzione automatica') }}</span>
            </label>
            <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
              <input type="checkbox" :checked="layer.audioLoop" @change="up('audioLoop', $event.target.checked)" class="mb-rounded" />
              <span class="mb-text-[11px] mb-text-gray-400">{{ t('Ripeti') }}</span>
            </label>
          </template>

          <!-- Stato iniziale (tutti i tipi) -->
          <div class="mb-border-t mb-border-gray-700 mb-pt-2 mb-mt-2"></div>
          <div>
            <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
              <input type="checkbox" :checked="layer.initiallyHidden === true" @change="up('initiallyHidden', $event.target.checked)" class="mb-rounded" />
              <span class="mps-label mb-mb-0">{{ t("Nascosto all'avvio (lo mostra un altro livello)") }}</span>
            </label>
          </div>

          <!-- Layer Action (tutti i tipi) -->
          <div class="mb-border-t mb-border-gray-700 mb-pt-2 mb-mt-2"></div>
          <p class="mb-text-[10px] mb-text-gray-400 mb-font-semibold mb-uppercase">{{ t('Azione al clic') }}</p>
          <div>
            <label class="mps-label">{{ t('Tipo azione') }}</label>
            <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.action?.type || 'none'" :options="OPTS_ACTION_TYPE" @update:model-value="setAction('type', $event)" />
          </div>
          <template v-if="layer.action?.type === 'goToSlide'">
            <div>
              <label class="mps-label">{{ t('Indice slide (da 0)') }}</label>
              <NumberScrubber theme="dark" :modelValue="layer.action.target || 0" :min="0" :max="null" :step="1" :defaultValue="0" emitAs="number" :ariaLabel="t('Indice slide (da 0)')" @update:modelValue="setAction('target', $event)" />
            </div>
          </template>
          <template v-if="layer.action?.type === 'openUrl'">
            <div>
              <label class="mps-label">{{ t('Indirizzo') }}</label>
              <input :value="layer.action.url || ''" @input="setAction('url', $event.target.value)" class="mps-input mb-w-full" placeholder="https://..." />
            </div>
            <div>
              <label class="mps-label">{{ t('Destinazione') }}</label>
              <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.action.urlTarget || '_self'" :options="OPTS_LINK_TARGET" @update:model-value="setAction('urlTarget', $event)" />
            </div>
          </template>
          <template v-if="layer.action?.type === 'toggleLayer'">
            <div>
              <label class="mps-label">{{ t('Livello da mostrare o nascondere') }}</label>
              <FieldSelect
                v-if="targetableLayers.length"
                ui="dropdown" size="compact" theme="dark"
                :model-value="layer.action.target || ''"
                :options="targetLayerOptions"
                @update:model-value="setAction('target', $event)"
              />
              <p v-else class="mb-text-[10px] mb-text-gray-500 mb-italic">{{ t('Nessun altro livello in questa slide') }}</p>
              <p v-if="layer.action.target" class="mb-text-[9px] mb-text-gray-500 mb-mt-1 mb-font-mono">id: {{ layer.action.target }}</p>
            </div>
          </template>

          <template v-if="layer.action?.type === 'setState' || layer.action?.type === 'cycleState'">
            <div>
              <label class="mps-label mb-flex mb-items-center mb-gap-1">{{ t('Nome dello stato') }}<InfoTip :testo="t('Uno stato è un nome con un valore (luce = accesa). I livelli con «Visibile solo in uno stato» si mostrano quando lo stato ha uno dei loro valori. Il valore all’avvio si scrive nelle impostazioni dello slider, «Stati all’avvio».')" :titolo="t('Stati')" /></label>
              <input :value="layer.action.target || ''" @input="setAction('target', $event.target.value)" class="mps-input mb-w-full" placeholder="luce" />
            </div>
            <div v-if="layer.action.type === 'setState'">
              <label class="mps-label">{{ t('Valore') }}</label>
              <input :value="layer.action.value ?? ''" @input="setAction('value', $event.target.value)" class="mps-input mb-w-full" placeholder="accesa" />
            </div>
            <div v-if="layer.action.type === 'setState'">
              <label class="mps-label mb-flex mb-items-center mb-gap-1">{{ t('Colore dello stato') }}<InfoTip :testo="t('Facoltativo: il colore va nella variabile --mps-st-nome, che i colori dei livelli possono usare, per esempio var(--mps-st-tinta, #ffffff). Il cambio di colore è sfumato.')" :titolo="t('Colore dello stato')" /></label>
              <FieldColor :modelValue="layer.action.color || ''" @update:modelValue="setAction('color', $event)" />
            </div>
            <div v-if="layer.action.type === 'cycleState'">
              <label class="mps-label mb-flex mb-items-center mb-gap-1">{{ t('Valori, in ordine') }}<InfoTip :testo="t('Separati da virgole: a ogni clic lo stato passa al valore dopo (1, 2, 3, 4). Due valori fanno un interruttore.')" :titolo="t('Stato successivo')" /></label>
              <input :value="layer.action.values || ''" @input="setAction('values', $event.target.value)" class="mps-input mb-w-full" placeholder="1,2,3,4" />
            </div>
            <label v-if="layer.action.type === 'cycleState'" class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
              <input type="checkbox" :checked="layer.action.loop !== false" @change="setAction('loop', $event.target.checked)" class="mb-rounded" />
              <span class="mb-text-[11px] mb-text-gray-400">{{ t('Dall’ultimo valore ricomincia') }}</span>
            </label>
            <div>
              <label class="mps-label">{{ t('Nome per chi usa la tastiera o un lettore di schermo') }}</label>
              <input :value="layer.action.label || ''" @input="setAction('label', $event.target.value)" class="mps-input mb-w-full" :placeholder="t('Accendi la luce')" />
            </div>
          </template>

          <!-- Visibile solo in uno stato -->
          <div class="mb-border-t mb-border-gray-700 mb-pt-2 mb-mt-2"></div>
          <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
            <input type="checkbox" :checked="!!layer.stato" @change="up('stato', $event.target.checked ? { nome: '', vale: '', effetto: 'fade', durata: 600, ritardo: 0 } : null)" class="mb-rounded" />
            <span class="mps-label mb-mb-0 mb-flex mb-items-center mb-gap-1">{{ t('Visibile solo in uno stato') }}<InfoTip :testo="t('Il livello si mostra quando lo stato ha uno dei valori scritti (separati da virgole) e si nasconde negli altri casi. Con il punto esclamativo davanti vale il contrario: «!accesa» = finché non è accesa. Lo stato lo cambiano i livelli con l’azione «Imposta uno stato» o «Stato successivo».')" :titolo="t('Visibile solo in uno stato')" /></span>
          </label>
          <template v-if="layer.stato">
            <div class="mb-grid mb-grid-cols-2 mb-gap-2">
              <div>
                <label class="mps-label">{{ t('Stato') }}</label>
                <input :value="layer.stato.nome || ''" @input="upStato('nome', $event.target.value)" class="mps-input mb-w-full" placeholder="luce" />
              </div>
              <div>
                <label class="mps-label">{{ t('Valori') }}</label>
                <input :value="layer.stato.vale || ''" @input="upStato('vale', $event.target.value)" class="mps-input mb-w-full" placeholder="accesa" />
              </div>
            </div>
            <div>
              <label class="mps-label">{{ t('Come compare') }}</label>
              <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.stato.effetto || 'fade'" :options="OPTS_STATO_EFFETTO" @update:model-value="upStato('effetto', $event)" />
            </div>
            <div class="mb-grid mb-grid-cols-2 mb-gap-2">
              <div>
                <label class="mps-label">{{ t('Durata') }}</label>
                <NumberScrubber theme="dark" :modelValue="layer.stato.durata ?? 600" :min="0" :max="4000" :step="50" :defaultValue="600" emitAs="number" :sliderOnFocus="false" unit="ms" :ariaLabel="t('Durata')" @update:modelValue="upStato('durata', $event)" />
              </div>
              <div>
                <label class="mps-label">{{ t('Ritardo') }}</label>
                <NumberScrubber theme="dark" :modelValue="layer.stato.ritardo ?? 0" :min="0" :max="4000" :step="50" :defaultValue="0" emitAs="number" :sliderOnFocus="false" unit="ms" :ariaLabel="t('Ritardo')" @update:modelValue="upStato('ritardo', $event)" />
              </div>
            </div>
          </template>

          <!-- Da pulire col puntatore (solo immagini) -->
          <template v-if="layer.type === 'image'">
            <div class="mb-border-t mb-border-gray-700 mb-pt-2 mb-mt-2"></div>
            <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
              <input type="checkbox" :checked="!!layer.pulisci?.attivo" @change="up('pulisci', $event.target.checked ? { attivo: true, pennello: 70, soglia: 60, stato: '', colore: '#111111' } : null)" class="mb-rounded" />
              <span class="mps-label mb-mb-0 mb-flex mb-items-center mb-gap-1">{{ t('Da pulire col puntatore') }}<InfoTip :testo="t('L’immagine copre quello che c’è sotto e si cancella passandoci sopra col mouse (col dito sul telefono). Pulita la parte scelta, il resto svanisce e lo stato scritto (nome=valore) cambia: così compaiono i livelli di «dopo».')" :titolo="t('Da pulire col puntatore')" /></span>
            </label>
            <template v-if="layer.pulisci?.attivo">
              <div class="mb-grid mb-grid-cols-2 mb-gap-2">
                <div>
                  <label class="mps-label">{{ t('Pennello') }}</label>
                  <NumberScrubber theme="dark" :modelValue="layer.pulisci.pennello ?? 70" :min="10" :max="400" :step="5" :defaultValue="70" emitAs="number" :sliderOnFocus="false" unit="px" :ariaLabel="t('Pennello')" @update:modelValue="upPulisci('pennello', $event)" />
                </div>
                <div>
                  <label class="mps-label">{{ t('Pulito a') }}</label>
                  <NumberScrubber theme="dark" :modelValue="layer.pulisci.soglia ?? 60" :min="5" :max="100" :step="5" :defaultValue="60" emitAs="number" :sliderOnFocus="false" unit="%" :ariaLabel="t('Pulito a')" @update:modelValue="upPulisci('soglia', $event)" />
                </div>
              </div>
              <div>
                <label class="mps-label">{{ t('Colore sotto l’immagine') }}</label>
                <FieldColor :modelValue="layer.pulisci.colore || '#111111'" @update:modelValue="upPulisci('colore', $event)" />
              </div>
              <div>
                <label class="mps-label">{{ t('Stato quando è pulito') }}</label>
                <input :value="layer.pulisci.stato || ''" @input="upPulisci('stato', $event.target.value)" class="mps-input mb-w-full" placeholder="camino=pulito" />
              </div>
            </template>
          </template>

          <!-- Parallax depth -->
          <div class="mb-border-t mb-border-gray-700 mb-pt-2 mb-mt-2"></div>
          <div>
            <label class="mps-label">{{ t('Profondità parallasse') }}</label>
            <NumberScrubber theme="dark" :modelValue="layer.parallaxDepth || 0" :min="0" :max="10" :step="1" :defaultValue="0" emitAs="number" :sliderOnFocus="false" :ariaLabel="t('Profondità parallasse')" @update:modelValue="up('parallaxDepth', $event)" />
          </div>
        </template>

        <!-- ===== POSIZIONE ===== -->
        <template v-if="activeTab === 'Posizione'">
          <!-- Breakpoint indicator -->
          <div v-if="activeBreakpoint !== 'desktop'" class="mb-bg-yellow-900/30 mb-border mb-border-yellow-700/50 mb-rounded mb-p-2 mb-mb-2">
            <div class="mb-flex mb-items-center mb-justify-between">
              <span class="mb-text-[10px] mb-text-yellow-400">{{ t('Valori per') }} {{ nomeSchermo }} {{ t('— quelli non impostati ereditano dallo schermo più grande') }}</span>
              <button
                v-if="hasAnyOverride"
                @click="clearAllOverrides"
                class="mb-text-[10px] mb-text-yellow-500 hover:mb-text-yellow-300 mb-whitespace-nowrap mb-ml-2"
              >{{ t('Azzera tutti') }}</button>
            </div>
          </div>
          <!-- Visibility toggle per breakpoint (anche Desktop: un livello solo per il telefono si nasconde qui e si
               rimette «Visibile su Mobile»; gli schermi senza valore ereditano dal più grande, come sul sito) -->
          <div class="mb-mb-2">
            <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
              <input type="checkbox" :checked="rv('visible') !== false" @change="up('visible', $event.target.checked)" class="mb-rounded" />
              <span class="mps-label mb-mb-0">{{ t('Visibile su') }} {{ nomeSchermo }}</span>
            </label>
          </div>
          <div>
            <label class="mps-label" :class="{ 'mb-text-yellow-400': isOverridden('x') }">X (%)</label>
            <div class="mb-flex mb-items-center mb-gap-2">
              <NumberScrubber class="mb-flex-1" theme="dark" :modelValue="rv('x')" :min="0" :max="100" :step="0.5" emitAs="number" :sliderOnFocus="false" unit="%" ariaLabel="X (%)" @update:modelValue="up('x', Math.round($event * 10) / 10)" />
              <button v-if="isOverridden('x')" @click="clearOverride('x')" class="mb-text-yellow-500 mb-text-[10px]" :title="t('Azzera')">&times;</button>
            </div>
          </div>
          <div>
            <label class="mps-label" :class="{ 'mb-text-yellow-400': isOverridden('y') }">Y (%)</label>
            <div class="mb-flex mb-items-center mb-gap-2">
              <NumberScrubber class="mb-flex-1" theme="dark" :modelValue="rv('y')" :min="0" :max="100" :step="0.5" emitAs="number" :sliderOnFocus="false" unit="%" ariaLabel="Y (%)" @update:modelValue="up('y', Math.round($event * 10) / 10)" />
              <button v-if="isOverridden('y')" @click="clearOverride('y')" class="mb-text-yellow-500 mb-text-[10px]" :title="t('Azzera')">&times;</button>
            </div>
          </div>
          <div>
            <label class="mps-label" :class="{ 'mb-text-yellow-400': isOverridden('width') }">{{ t('Larghezza (%)') }}</label>
            <div class="mb-flex mb-items-center mb-gap-2">
              <label class="mb-flex mb-items-center mb-gap-1 mb-text-[10px] mb-text-gray-400">
                <input type="checkbox" :checked="rv('width') === 'auto'" @change="up('width', $event.target.checked ? 'auto' : 50)" class="mb-rounded" /> Auto
              </label>
              <template v-if="rv('width') !== 'auto'">
                <NumberScrubber class="mb-flex-1" theme="dark" :modelValue="rv('width')" :min="5" :max="100" :step="0.5" emitAs="number" :sliderOnFocus="false" unit="%" :ariaLabel="t('Larghezza (%)')" @update:modelValue="up('width', Math.round($event * 10) / 10)" />
              </template>
              <button v-if="isOverridden('width')" @click="clearOverride('width')" class="mb-text-yellow-500 mb-text-[10px]" :title="t('Azzera')">&times;</button>
            </div>
          </div>
          <div>
            <label class="mps-label" :class="{ 'mb-text-yellow-400': isOverridden('height') }">{{ t('Altezza (%)') }}</label>
            <div class="mb-flex mb-items-center mb-gap-2">
              <label class="mb-flex mb-items-center mb-gap-1 mb-text-[10px] mb-text-gray-400">
                <input type="checkbox" :checked="rv('height') === 'auto'" @change="up('height', $event.target.checked ? 'auto' : 30)" class="mb-rounded" /> Auto
              </label>
              <template v-if="rv('height') !== 'auto'">
                <NumberScrubber class="mb-flex-1" theme="dark" :modelValue="rv('height')" :min="5" :max="100" :step="0.5" emitAs="number" :sliderOnFocus="false" unit="%" :ariaLabel="t('Altezza (%)')" @update:modelValue="up('height', Math.round($event * 10) / 10)" />
              </template>
              <button v-if="isOverridden('height')" @click="clearOverride('height')" class="mb-text-yellow-500 mb-text-[10px]" :title="t('Azzera')">&times;</button>
            </div>
          </div>
          <div v-if="layer.type === 'text' || layer.type === 'button' || layer.type === 'icon'">
            <label class="mps-label" :class="{ 'mb-text-yellow-400': isOverridden('fontSize') }">{{ t('Dimensione testo') }}</label>
            <div class="mb-flex mb-items-center mb-gap-2">
              <NumberScrubber class="mb-flex-1" theme="dark" :modelValue="rv('fontSize')" :min="8" :max="200" :step="1" emitAs="number" :sliderOnFocus="false" unit="px" ariaLabel="Font size (px)" @update:modelValue="up('fontSize', $event)" />
              <button v-if="isOverridden('fontSize')" @click="clearOverride('fontSize')" class="mb-text-yellow-500 mb-text-[10px]" :title="t('Azzera')">&times;</button>
            </div>
          </div>
        </template>

        <!-- ===== STILE ===== -->
        <template v-if="activeTab === 'Stile'">

          <!-- ── Tipografia (text/button) ── -->
          <template v-if="layer.type === 'text' || layer.type === 'button'">
            <p class="mb-text-[10px] mb-text-gray-400 mb-font-semibold mb-uppercase">{{ t('Tipografia') }}</p>
            <div>
              <label class="mps-label">{{ t('Font') }}</label>
              <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.fontFamily || ''" :options="fontGroupOpts" @update:model-value="up('fontFamily', $event)" />
            </div>
            <div>
              <label class="mps-label">{{ t('Dimensione testo') }}</label>
              <NumberScrubber theme="dark" :modelValue="layer.fontSize" :min="8" :max="200" :step="1" emitAs="number" :sliderOnFocus="false" unit="px" :ariaLabel="t('Dimensione testo (px)')" @update:modelValue="up('fontSize', $event)" />
            </div>
            <div>
              <label class="mps-label">{{ t('Peso del carattere') }}</label>
              <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.fontWeight" :options="OPTS_FONT_WEIGHT" @update:model-value="up('fontWeight', $event)" />
            </div>
            <div>
              <label class="mps-label">{{ t('Stile del carattere') }}</label>
              <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.fontStyle || 'normal'" :options="OPTS_FONT_STYLE" @update:model-value="up('fontStyle', $event)" />
            </div>
            <div>
              <label class="mps-label">{{ t('Colore') }}</label>
              <FieldColor :modelValue="layer.color || '#ffffff'" @update:modelValue="up('color', $event)" />
            </div>
            <div>
              <label class="mps-label" :class="{ 'mb-text-yellow-400': isOverridden('textAlign') }">{{ t('Allineamento testo') }}</label>
              <div class="mb-flex mb-items-center mb-gap-1">
                <button v-for="a in ['left','center','right']" :key="a"
                  @click="up('textAlign', a)"
                  :class="['mb-flex-1 mb-py-1 mb-rounded mb-text-xs', (rv('textAlign') || 'left') === a ? 'mb-bg-primary-600 mb-text-white' : 'mb-bg-gray-700 mb-text-gray-400']"
                >{{ a }}</button>
                <button v-if="isOverridden('textAlign')" @click="clearOverride('textAlign')" class="mb-text-yellow-500 mb-text-[10px]" :title="t('Azzera')">&times;</button>
              </div>
            </div>
            <div v-if="layer.type === 'text' && layer.height && layer.height !== 'auto'">
              <label class="mps-label">{{ t('Allineamento verticale') }}</label>
              <div class="mb-flex mb-items-center mb-gap-1">
                <button v-for="a in [['top', 'In alto'], ['center', 'Al centro'], ['bottom', 'In basso']]" :key="a[0]"
                  @click="up('textValign', a[0])"
                  :class="['mb-flex-1 mb-py-1 mb-rounded mb-text-xs', (layer.textValign || 'top') === a[0] ? 'mb-bg-primary-600 mb-text-white' : 'mb-bg-gray-700 mb-text-gray-400']"
                >{{ t(a[1]) }}</button>
              </div>
            </div>
            <div>
              <label class="mps-label">{{ t('Interlinea') }}</label>
              <NumberScrubber theme="dark" :modelValue="layer.lineHeight ?? 1.2" :min="0.8" :max="3" :step="0.1" :defaultValue="1.2" emitAs="number" :sliderOnFocus="false" ariaLabel="Line-height" @update:modelValue="up('lineHeight', $event)" />
            </div>
            <div>
              <label class="mps-label">{{ t('Spaziatura lettere') }}</label>
              <NumberScrubber theme="dark" :modelValue="layer.letterSpacing ?? 0" :min="-5" :max="20" :step="0.5" :defaultValue="0" emitAs="number" :sliderOnFocus="false" unit="px" ariaLabel="Letter-spacing (px)" @update:modelValue="up('letterSpacing', $event)" />
            </div>
            <div>
              <label class="mps-label">{{ t('Trasformazione') }}</label>
              <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.textTransform || 'none'" :options="OPTS_TEXT_TRANSFORM" @update:model-value="up('textTransform', $event)" />
            </div>
            <div>
              <label class="mps-label">{{ t('Decorazione') }}</label>
              <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.textDecoration || 'none'" :options="OPTS_TEXT_DECORATION" @update:model-value="up('textDecoration', $event)" />
            </div>
            <!-- Text Stroke -->
            <div>
              <label class="mps-label">{{ t('Contorno del testo') }}</label>
              <NumberScrubber theme="dark" :modelValue="layer.textStrokeWidth ?? 0" :min="0" :max="10" :step="0.5" :defaultValue="0" emitAs="number" :sliderOnFocus="false" unit="px" ariaLabel="Text stroke (px)" @update:modelValue="up('textStrokeWidth', $event)" />
            </div>
            <template v-if="(layer.textStrokeWidth ?? 0) > 0">
              <div>
                <label class="mps-label">{{ t('Colore del tratto') }}</label>
                <FieldColor :modelValue="layer.textStrokeColor || '#000000'" @update:modelValue="up('textStrokeColor', $event)" />
              </div>
            </template>
            <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
              <input type="checkbox" :checked="layer.selectableText" @change="up('selectableText', $event.target.checked)" class="mb-rounded" />
              <span class="mb-text-[11px] mb-text-gray-400">{{ t('Testo selezionabile') }}</span>
            </label>
          </template>

          <!-- ── Dimensione font (icon) ── -->
          <template v-if="layer.type === 'icon'">
            <div>
              <label class="mps-label">{{ t('Dimensione icona') }}</label>
              <NumberScrubber theme="dark" :modelValue="layer.fontSize" :min="8" :max="200" :step="1" emitAs="number" :sliderOnFocus="false" unit="px" :ariaLabel="t('Dimensione icona (px)')" @update:modelValue="up('fontSize', $event)" />
            </div>
            <div>
              <label class="mps-label">{{ t('Colore di riempimento') }}</label>
              <FieldColor :modelValue="layer.color || '#ffffff'" @update:modelValue="up('color', $event)" />
            </div>
            <div>
              <label class="mps-label">{{ t('Colore di riempimento (al posto di quello del file)') }}</label>
              <FieldColor :modelValue="layer.iconFillColor || ''" @update:modelValue="up('iconFillColor', $event)" />
            </div>
            <div>
              <label class="mps-label">{{ t('Colore del tratto') }}</label>
              <FieldColor :modelValue="layer.iconStrokeColor || ''" @update:modelValue="up('iconStrokeColor', $event)" />
            </div>
            <div>
              <label class="mps-label">{{ t('Spessore del tratto') }}</label>
              <NumberScrubber theme="dark" :modelValue="layer.iconStrokeWidth ?? 0" :min="0" :max="10" :step="0.5" :defaultValue="0" emitAs="number" :sliderOnFocus="false" unit="px" ariaLabel="Stroke width (px)" @update:modelValue="up('iconStrokeWidth', $event)" />
            </div>
            <div>
              <label class="mps-label">{{ t('Tratteggio') }}</label>
              <NumberScrubber theme="dark" :modelValue="layer.iconStrokeDash ?? 0" :min="0" :max="50" :step="1" :defaultValue="0" emitAs="number" :sliderOnFocus="false" ariaLabel="Stroke dash" @update:modelValue="up('iconStrokeDash', $event)" />
            </div>
          </template>

          <!-- ── Sfondo e raggio ── -->
          <template v-if="layer.type !== 'image' && layer.type !== 'video'">
            <div class="mb-border-t mb-border-gray-700 mb-pt-2 mb-mt-2"></div>
            <p class="mb-text-[10px] mb-text-gray-400 mb-font-semibold mb-uppercase">{{ t('Sfondo') }}</p>
          </template>

          <!-- Shape gradient toggle -->
          <template v-if="layer.type === 'shape'">
            <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
              <input type="checkbox" :checked="!!layer.shapeGradient" @change="toggleShapeGradient($event.target.checked)" class="mb-rounded" />
              <span class="mb-text-[11px] mb-text-gray-400">{{ t('Gradiente') }}</span>
            </label>
            <template v-if="layer.shapeGradient">
              <div>
                <label class="mps-label">{{ t('Colore da') }}</label>
                <FieldColor :modelValue="layer.shapeGradient.from || '#3b82f6'" @update:modelValue="upShapeGrad('from', $event)" />
              </div>
              <div>
                <label class="mps-label">{{ t('Colore a') }}</label>
                <FieldColor :modelValue="layer.shapeGradient.to || '#8b5cf6'" @update:modelValue="upShapeGrad('to', $event)" />
              </div>
              <div>
                <label class="mps-label">{{ t('Angolo') }}</label>
                <NumberScrubber theme="dark" :modelValue="layer.shapeGradient.angle ?? 180" :min="0" :max="360" :step="15" :defaultValue="180" emitAs="number" :sliderOnFocus="false" unit="°" :ariaLabel="t('Angolo')" @update:modelValue="upShapeGrad('angle', $event)" />
              </div>
              <div>
                <label class="mps-label">{{ t('Inizio sfumatura') }}</label>
                <NumberScrubber theme="dark" :modelValue="layer.shapeGradient.fromStop ?? 0" :min="0" :max="95" :step="5" :defaultValue="0" emitAs="number" :sliderOnFocus="false" unit="%" :ariaLabel="t('Inizio sfumatura')" @update:modelValue="upShapeGrad('fromStop', $event)" />
              </div>
            </template>
            <template v-else>
              <div>
                <label class="mps-label">{{ t('Sfondo') }}</label>
                <FieldColor :modelValue="layer.bgColor || '#3b82f6'" @update:modelValue="up('bgColor', $event)" />
              </div>
            </template>
          </template>
          <template v-else-if="layer.type !== 'image' && layer.type !== 'video'">
            <div>
              <label class="mps-label">{{ t('Sfondo') }}</label>
              <FieldColor :modelValue="layer.bgColor || '#000000'" @update:modelValue="up('bgColor', $event)" />
            </div>
          </template>

          <div>
            <div class="mb-flex mb-items-center mb-justify-between">
              <label class="mps-label mb-mb-0">{{ t('Raggio (px)') }}</label>
              <button @click="up('borderRadiusLinked', !layer.borderRadiusLinked)" :class="['mb-text-[10px] mb-px-1.5 mb-rounded', layer.borderRadiusLinked !== false ? 'mb-bg-primary-600 mb-text-white' : 'mb-bg-gray-700 mb-text-gray-400']" :title="t('Lega/slega angoli')">&#x1F517;</button>
            </div>
            <template v-if="layer.borderRadiusLinked !== false">
              <NumberScrubber theme="dark" :modelValue="layer.borderRadius" :min="0" :max="200" :step="1" emitAs="number" :sliderOnFocus="false" unit="px" :ariaLabel="t('Raggio (px)')" @update:modelValue="up('borderRadius', $event)" />
            </template>
            <template v-else>
              <div class="mb-grid mb-grid-cols-2 mb-gap-2">
                <div>
                  <label class="mps-label">↖</label>
                  <NumberScrubber theme="dark" :modelValue="layer.borderRadiusTL ?? 0" :min="0" :max="200" :step="1" :defaultValue="0" emitAs="number" :sliderOnFocus="false" ariaLabel="Border radius TL" @update:modelValue="up('borderRadiusTL', $event)" />
                </div>
                <div>
                  <label class="mps-label">↗</label>
                  <NumberScrubber theme="dark" :modelValue="layer.borderRadiusTR ?? 0" :min="0" :max="200" :step="1" :defaultValue="0" emitAs="number" :sliderOnFocus="false" ariaLabel="Border radius TR" @update:modelValue="up('borderRadiusTR', $event)" />
                </div>
                <div>
                  <label class="mps-label">↙</label>
                  <NumberScrubber theme="dark" :modelValue="layer.borderRadiusBL ?? 0" :min="0" :max="200" :step="1" :defaultValue="0" emitAs="number" :sliderOnFocus="false" ariaLabel="Border radius BL" @update:modelValue="up('borderRadiusBL', $event)" />
                </div>
                <div>
                  <label class="mps-label">↘</label>
                  <NumberScrubber theme="dark" :modelValue="layer.borderRadiusBR ?? 0" :min="0" :max="200" :step="1" :defaultValue="0" emitAs="number" :sliderOnFocus="false" ariaLabel="Border radius BR" @update:modelValue="up('borderRadiusBR', $event)" />
                </div>
              </div>
            </template>
          </div>

          <!-- ── Bordo ── -->
          <div class="mb-border-t mb-border-gray-700 mb-pt-2 mb-mt-2"></div>
          <p class="mb-text-[10px] mb-text-gray-400 mb-font-semibold mb-uppercase">{{ t('Bordo') }}</p>
          <div>
            <div class="mb-flex mb-items-center mb-justify-between">
              <label class="mps-label mb-mb-0">{{ t('Larghezza (px)') }}</label>
              <button @click="up('borderWidthLinked', !layer.borderWidthLinked)" :class="['mb-text-[10px] mb-px-1.5 mb-rounded', layer.borderWidthLinked !== false ? 'mb-bg-primary-600 mb-text-white' : 'mb-bg-gray-700 mb-text-gray-400']" :title="t('Lega/slega lati')">&#x1F517;</button>
            </div>
            <template v-if="layer.borderWidthLinked !== false">
              <NumberScrubber theme="dark" :modelValue="layer.borderWidth ?? 0" :min="0" :max="20" :step="1" :defaultValue="0" emitAs="number" :sliderOnFocus="false" unit="px" :ariaLabel="t('Larghezza (px)')" @update:modelValue="up('borderWidth', $event)" />
            </template>
            <template v-else>
              <div class="mb-grid mb-grid-cols-2 mb-gap-2">
                <div v-for="side in ['Top','Right','Bottom','Left']" :key="side">
                  <label class="mps-label">{{ side }}</label>
                  <NumberScrubber theme="dark" :modelValue="layer['borderWidth' + side] ?? 0" :min="0" :max="20" :step="1" :defaultValue="0" emitAs="number" :sliderOnFocus="false" unit="px" :ariaLabel="'Border width ' + side" @update:modelValue="up('borderWidth' + side, $event)" />
                </div>
              </div>
            </template>
          </div>
          <template v-if="(layer.borderWidth ?? 0) > 0 || (layer.borderWidthLinked === false && ((layer.borderWidthTop ?? 0) > 0 || (layer.borderWidthRight ?? 0) > 0 || (layer.borderWidthBottom ?? 0) > 0 || (layer.borderWidthLeft ?? 0) > 0))">
            <div>
              <label class="mps-label">{{ t('Stile') }}</label>
              <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.borderStyle || 'solid'" :options="OPTS_BORDER_STYLE" @update:model-value="up('borderStyle', $event)" />
            </div>
            <div>
              <label class="mps-label">{{ t('Colore') }}</label>
              <FieldColor :modelValue="layer.borderColor || '#ffffff'" @update:modelValue="up('borderColor', $event)" />
            </div>
          </template>

          <!-- ── Spaziatura ── -->
          <template v-if="layer.type !== 'image' && layer.type !== 'video'">
            <div class="mb-border-t mb-border-gray-700 mb-pt-2 mb-mt-2"></div>
            <p class="mb-text-[10px] mb-text-gray-400 mb-font-semibold mb-uppercase">{{ t('Spaziatura') }}</p>
            <div>
              <div class="mb-flex mb-items-center mb-justify-between">
                <label class="mps-label mb-mb-0">{{ t('Padding') }}</label>
                <button @click="up('paddingLinked', !layer.paddingLinked)" :class="['mb-text-[10px] mb-px-1.5 mb-rounded', layer.paddingLinked !== false ? 'mb-bg-primary-600 mb-text-white' : 'mb-bg-gray-700 mb-text-gray-400']" :title="t('Lega/slega lati')">&#x1F517;</button>
              </div>
              <template v-if="layer.paddingLinked !== false">
                <NumberScrubber theme="dark" :modelValue="layer.padding" :min="0" :max="80" :step="1" emitAs="number" :sliderOnFocus="false" unit="px" ariaLabel="Padding (px)" @update:modelValue="up('padding', $event)" />
              </template>
              <template v-else>
                <div class="mb-grid mb-grid-cols-2 mb-gap-2">
                  <div v-for="side in ['Top','Right','Bottom','Left']" :key="side">
                    <label class="mps-label">{{ side }}</label>
                    <NumberScrubber theme="dark" :modelValue="layer['padding' + side] ?? 0" :min="0" :max="80" :step="1" :defaultValue="0" emitAs="number" :sliderOnFocus="false" unit="px" :ariaLabel="'Padding ' + side" @update:modelValue="up('padding' + side, $event)" />
                  </div>
                </div>
              </template>
            </div>
          </template>

          <!-- ── Immagine (solo image) ── -->
          <template v-if="layer.type === 'image'">
            <div class="mb-border-t mb-border-gray-700 mb-pt-2 mb-mt-2"></div>
            <p class="mb-text-[10px] mb-text-gray-400 mb-font-semibold mb-uppercase">{{ t('Immagine') }}</p>
            <div>
              <label class="mps-label">{{ t('Adattamento') }}</label>
              <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.objectFit || 'cover'" :options="OPTS_OBJECT_FIT" @update:model-value="up('objectFit', $event)" />
            </div>
            <div>
              <label class="mps-label">{{ t('Posizione') }}</label>
              <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.objectPosition || 'center'" :options="OPTS_OBJECT_POSITION" @update:model-value="up('objectPosition', $event)" />
            </div>
          </template>

          <!-- ── Filtri CSS (image/video) ── -->
          <template v-if="layer.type === 'image' || layer.type === 'video'">
            <div class="mb-border-t mb-border-gray-700 mb-pt-2 mb-mt-2"></div>
            <p class="mb-text-[10px] mb-text-gray-400 mb-font-semibold mb-uppercase">{{ t('Filtri') }}</p>
            <div>
              <label class="mps-label">{{ t('Luminosità') }}</label>
              <NumberScrubber theme="dark" :modelValue="layer.filterBrightness ?? 100" :min="0" :max="200" :step="5" :defaultValue="100" emitAs="number" :sliderOnFocus="false" unit="%" :ariaLabel="t('Luminosità (%)')" @update:modelValue="up('filterBrightness', $event)" />
            </div>
            <div>
              <label class="mps-label">{{ t('Contrasto') }}</label>
              <NumberScrubber theme="dark" :modelValue="layer.filterContrast ?? 100" :min="0" :max="200" :step="5" :defaultValue="100" emitAs="number" :sliderOnFocus="false" unit="%" :ariaLabel="t('Contrasto (%)')" @update:modelValue="up('filterContrast', $event)" />
            </div>
            <div>
              <label class="mps-label">{{ t('Saturazione') }}</label>
              <NumberScrubber theme="dark" :modelValue="layer.filterSaturate ?? 100" :min="0" :max="200" :step="5" :defaultValue="100" emitAs="number" :sliderOnFocus="false" unit="%" :ariaLabel="t('Saturazione (%)')" @update:modelValue="up('filterSaturate', $event)" />
            </div>
            <div>
              <label class="mps-label">{{ t('Scala di grigi') }}</label>
              <NumberScrubber theme="dark" :modelValue="layer.filterGrayscale ?? 0" :min="0" :max="100" :step="5" :defaultValue="0" emitAs="number" :sliderOnFocus="false" unit="%" :ariaLabel="t('Scala di grigi (%)')" @update:modelValue="up('filterGrayscale', $event)" />
            </div>
            <div>
              <label class="mps-label">{{ t('Rotazione tinta') }}</label>
              <NumberScrubber theme="dark" :modelValue="layer.filterHueRotate ?? 0" :min="0" :max="360" :step="5" :defaultValue="0" emitAs="number" :sliderOnFocus="false" unit="°" :ariaLabel="t('Rotazione tinta')" @update:modelValue="up('filterHueRotate', $event)" />
            </div>
            <div>
              <label class="mps-label">{{ t('Sfocatura') }}</label>
              <NumberScrubber theme="dark" :modelValue="layer.filterBlur ?? 0" :min="0" :max="20" :step="0.5" :defaultValue="0" emitAs="number" :sliderOnFocus="false" unit="px" :ariaLabel="t('Sfocatura (px)')" @update:modelValue="up('filterBlur', $event)" />
            </div>
            <div>
              <label class="mps-label">{{ t('Seppia') }}</label>
              <NumberScrubber theme="dark" :modelValue="layer.filterSepia ?? 0" :min="0" :max="100" :step="5" :defaultValue="0" emitAs="number" :sliderOnFocus="false" unit="%" :ariaLabel="t('Seppia (%)')" @update:modelValue="up('filterSepia', $event)" />
            </div>
            <div>
              <label class="mps-label">{{ t('Inverti') }}</label>
              <NumberScrubber theme="dark" :modelValue="layer.filterInvert ?? 0" :min="0" :max="100" :step="5" :defaultValue="0" emitAs="number" :sliderOnFocus="false" unit="%" :ariaLabel="t('Inverti (%)')" @update:modelValue="up('filterInvert', $event)" />
            </div>
          </template>

          <!-- ── Backdrop Filter (tutti i tipi) ── -->
          <div class="mb-border-t mb-border-gray-700 mb-pt-2 mb-mt-2"></div>
          <p class="mb-text-[10px] mb-text-gray-400 mb-font-semibold mb-uppercase">{{ t('Filtro sfondo') }}</p>
          <div>
            <label class="mps-label">{{ t('Sfocatura dietro') }}</label>
            <NumberScrubber theme="dark" :modelValue="layer.backdropBlur ?? 0" :min="0" :max="20" :step="1" :defaultValue="0" emitAs="number" :sliderOnFocus="false" unit="px" :ariaLabel="t('Sfocatura dietro')" @update:modelValue="up('backdropBlur', $event)" />
          </div>
          <div>
            <label class="mps-label">{{ t('Luminosità') }}</label>
            <NumberScrubber theme="dark" :modelValue="layer.backdropBrightness ?? 100" :min="0" :max="200" :step="5" :defaultValue="100" emitAs="number" :sliderOnFocus="false" unit="%" :ariaLabel="t('Luminosità (%)')" @update:modelValue="up('backdropBrightness', $event)" />
          </div>
          <div>
            <label class="mps-label">{{ t('Scala di grigi') }}</label>
            <NumberScrubber theme="dark" :modelValue="layer.backdropGrayscale ?? 0" :min="0" :max="100" :step="5" :defaultValue="0" emitAs="number" :sliderOnFocus="false" unit="%" :ariaLabel="t('Scala di grigi (%)')" @update:modelValue="up('backdropGrayscale', $event)" />
          </div>
          <div>
            <label class="mps-label">{{ t('Contrasto') }}</label>
            <NumberScrubber theme="dark" :modelValue="layer.backdropContrast ?? 100" :min="0" :max="300" :step="5" :defaultValue="100" emitAs="number" :sliderOnFocus="false" unit="%" :ariaLabel="t('Contrasto (%)')" @update:modelValue="up('backdropContrast', $event)" />
          </div>
          <div>
            <label class="mps-label">{{ t('Saturazione') }}</label>
            <NumberScrubber theme="dark" :modelValue="layer.backdropSaturate ?? 100" :min="0" :max="300" :step="5" :defaultValue="100" emitAs="number" :sliderOnFocus="false" unit="%" :ariaLabel="t('Saturazione (%)')" @update:modelValue="up('backdropSaturate', $event)" />
          </div>
          <div>
            <label class="mps-label">{{ t('Inverti') }}</label>
            <NumberScrubber theme="dark" :modelValue="layer.backdropInvert ?? 0" :min="0" :max="100" :step="5" :defaultValue="0" emitAs="number" :sliderOnFocus="false" unit="%" :ariaLabel="t('Inverti (%)')" @update:modelValue="up('backdropInvert', $event)" />
          </div>
          <div>
            <label class="mps-label">{{ t('Rotazione tinta') }}</label>
            <NumberScrubber theme="dark" :modelValue="layer.backdropHueRotate ?? 0" :min="0" :max="360" :step="5" :defaultValue="0" emitAs="number" :sliderOnFocus="false" unit="°" :ariaLabel="t('Rotazione tinta')" @update:modelValue="up('backdropHueRotate', $event)" />
          </div>

          <!-- ── Ombre ── -->
          <div class="mb-border-t mb-border-gray-700 mb-pt-2 mb-mt-2"></div>
          <p class="mb-text-[10px] mb-text-gray-400 mb-font-semibold mb-uppercase">{{ t('Ombre') }}</p>

          <!-- Box shadow (tutti) -->
          <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
            <input type="checkbox" :checked="!!layer.boxShadow" @change="toggleBoxShadow($event.target.checked)" class="mb-rounded" />
            <span class="mb-text-[11px] mb-text-gray-400">{{ t('Ombra') }}</span>
          </label>
          <template v-if="layer.boxShadow">
            <div class="mb-grid mb-grid-cols-2 mb-gap-2">
              <div>
                <label class="mps-label">X</label>
                <NumberScrubber theme="dark" :modelValue="layer.boxShadow.x ?? 0" :min="-30" :max="30" :step="1" :defaultValue="0" emitAs="number" :sliderOnFocus="false" ariaLabel="Box shadow X" @update:modelValue="upBoxShadow('x', $event)" />
              </div>
              <div>
                <label class="mps-label">Y</label>
                <NumberScrubber theme="dark" :modelValue="layer.boxShadow.y ?? 4" :min="-30" :max="30" :step="1" :defaultValue="4" emitAs="number" :sliderOnFocus="false" ariaLabel="Box shadow Y" @update:modelValue="upBoxShadow('y', $event)" />
              </div>
              <div>
                <label class="mps-label">{{ t('Sfocatura') }}</label>
                <NumberScrubber theme="dark" :modelValue="layer.boxShadow.blur ?? 10" :min="0" :max="60" :step="1" :defaultValue="10" emitAs="number" :sliderOnFocus="false" ariaLabel="Box shadow blur" @update:modelValue="upBoxShadow('blur', $event)" />
              </div>
              <div>
                <label class="mps-label">{{ t('Estensione') }}</label>
                <NumberScrubber theme="dark" :modelValue="layer.boxShadow.spread ?? 0" :min="-20" :max="20" :step="1" :defaultValue="0" emitAs="number" :sliderOnFocus="false" ariaLabel="Box shadow spread" @update:modelValue="upBoxShadow('spread', $event)" />
              </div>
            </div>
            <div>
              <label class="mps-label">{{ t('Colore ombra') }}</label>
              <FieldColor :modelValue="layer.boxShadow.color || 'rgba(0,0,0,0.3)'" @update:modelValue="upBoxShadow('color', $event)" />
            </div>
          </template>

          <!-- Text shadow (solo text/button) -->
          <template v-if="layer.type === 'text' || layer.type === 'button'">
            <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer mb-mt-2">
              <input type="checkbox" :checked="!!layer.textShadow" @change="toggleTextShadow($event.target.checked)" class="mb-rounded" />
              <span class="mb-text-[11px] mb-text-gray-400">{{ t('Ombra del testo') }}</span>
            </label>
            <template v-if="layer.textShadow">
              <div class="mb-grid mb-grid-cols-2 mb-gap-2">
                <div>
                  <label class="mps-label">X</label>
                  <NumberScrubber theme="dark" :modelValue="layer.textShadow.x ?? 2" :min="-20" :max="20" :step="1" :defaultValue="2" emitAs="number" :sliderOnFocus="false" ariaLabel="Text shadow X" @update:modelValue="upTextShadow('x', $event)" />
                </div>
                <div>
                  <label class="mps-label">Y</label>
                  <NumberScrubber theme="dark" :modelValue="layer.textShadow.y ?? 2" :min="-20" :max="20" :step="1" :defaultValue="2" emitAs="number" :sliderOnFocus="false" ariaLabel="Text shadow Y" @update:modelValue="upTextShadow('y', $event)" />
                </div>
              </div>
              <div>
                <label class="mps-label">{{ t('Sfocatura') }}</label>
                <NumberScrubber theme="dark" :modelValue="layer.textShadow.blur ?? 4" :min="0" :max="30" :step="1" :defaultValue="4" emitAs="number" :sliderOnFocus="false" ariaLabel="Text shadow blur" @update:modelValue="upTextShadow('blur', $event)" />
              </div>
              <div>
                <label class="mps-label">{{ t('Colore ombra') }}</label>
                <FieldColor :modelValue="layer.textShadow.color || '#000000'" @update:modelValue="upTextShadow('color', $event)" />
              </div>
            </template>
          </template>

          <!-- ── Effetti ── -->
          <div class="mb-border-t mb-border-gray-700 mb-pt-2 mb-mt-2"></div>
          <p class="mb-text-[10px] mb-text-gray-400 mb-font-semibold mb-uppercase">{{ t('Effetti') }}</p>
          <div>
            <label class="mps-label">{{ t('Opacità') }}</label>
            <NumberScrubber theme="dark" :modelValue="layer.opacity ?? 100" :min="0" :max="100" :step="5" :defaultValue="100" emitAs="number" :sliderOnFocus="false" unit="%" :ariaLabel="t('Opacità (%)')" @update:modelValue="up('opacity', $event)" />
          </div>
          <div>
            <label class="mps-label">{{ t('Fusione') }}</label>
            <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.blendMode || 'normal'" :options="OPTS_BLEND_MODE" @update:model-value="up('blendMode', $event)" />
          </div>

          <!-- SFX Block Reveal (solo text/button/icon) -->
          <template v-if="layer.type === 'text' || layer.type === 'button' || layer.type === 'icon'">
          <div class="mb-border-t mb-border-gray-700 mb-pt-2 mb-mt-2"></div>
          <p class="mb-text-[10px] mb-text-gray-400 mb-font-semibold mb-uppercase">{{ t('Rivelazione a blocco') }}</p>
          <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
            <input type="checkbox" :checked="!!layer.sfx" @change="toggleSfx($event.target.checked)" class="mb-rounded" />
            <span class="mb-text-[11px] mb-text-gray-400">{{ t('Attivo') }}</span>
          </label>
          <template v-if="layer.sfx">
            <div>
              <label class="mps-label">{{ t('Effetto') }}</label>
              <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.sfx.effect || 'blockRight'" :options="OPTS_SFX_EFFECT" @update:model-value="upSfx('effect', $event)" />
            </div>
            <div>
              <label class="mps-label">{{ t('Colore blocco') }}</label>
              <FieldColor :modelValue="layer.sfx.color || '#ffffff'" @update:modelValue="upSfx('color', $event)" />
            </div>
            <div>
              <label class="mps-label">{{ t('Durata') }}</label>
              <NumberScrubber theme="dark" :modelValue="layer.sfx.duration || 800" :min="200" :max="2000" :step="50" :defaultValue="800" emitAs="number" :sliderOnFocus="false" unit="ms" :ariaLabel="t('Durata (ms)')" @update:modelValue="upSfx('duration', $event)" />
            </div>
          </template>
          </template>

          <!-- ── Cursore ── -->
          <div class="mb-border-t mb-border-gray-700 mb-pt-2 mb-mt-2"></div>
          <p class="mb-text-[10px] mb-text-gray-400 mb-font-semibold mb-uppercase">{{ t('Cursore') }}</p>
          <div>
            <label class="mps-label">{{ t('Cursore del mouse') }}</label>
            <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.cursor || 'auto'" :options="OPTS_CURSOR" @update:model-value="up('cursor', $event)" />
          </div>

          <!-- ── Attributi personalizzati ── -->
          <div class="mb-border-t mb-border-gray-700 mb-pt-2 mb-mt-2"></div>
          <p class="mb-text-[10px] mb-text-gray-400 mb-font-semibold mb-uppercase">{{ t('Attributi') }}</p>
          <div>
            <label class="mps-label">{{ t('ID personalizzato') }}</label>
            <input :value="layer.customId || ''" @input="up('customId', $event.target.value)" class="mps-input" placeholder="es. hero-title" />
          </div>
          <div>
            <label class="mps-label">{{ t('Classi CSS') }}</label>
            <input :value="layer.customClass || ''" @input="up('customClass', $event.target.value)" class="mps-input" placeholder="es. my-class another-class" />
          </div>
          <div>
            <label class="mps-label">{{ t('CSS personalizzato') }}</label>
            <textarea :value="layer.customCSS || ''" @input="up('customCSS', $event.target.value)" rows="3"
              class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded mb-px-2 mb-py-1 mb-text-[10px] mb-text-gray-900 mb-resize-y mb-font-mono" placeholder="color: red; transform: rotate(5deg);"></textarea>
          </div>
        </template>

        <!-- ===== ANIMAZIONE ===== -->
        <template v-if="activeTab === 'Animazione'">
          <!-- Toggle Semplice / Timeline -->
          <div class="mb-flex mb-bg-gray-800 mb-rounded mb-p-0.5 mb-mb-3">
            <button
              @click="setAnimMode('simple')"
              :class="['mb-flex-1 mb-py-1 mb-rounded mb-text-[10px] mb-font-medium mb-transition-colors',
                !hasTimeline ? 'mb-bg-primary-600 mb-text-white' : 'mb-text-gray-400 hover:mb-text-gray-300']"
            >{{ t('Semplice') }}</button>
            <button
              @click="setAnimMode('timeline')"
              :class="['mb-flex-1 mb-py-1 mb-rounded mb-text-[10px] mb-font-medium mb-transition-colors',
                hasTimeline ? 'mb-bg-primary-600 mb-text-white' : 'mb-text-gray-400 hover:mb-text-gray-300']"
            >{{ t('Linea del tempo') }}</button>
          </div>

          <!-- Modo SEMPLICE -->
          <template v-if="!hasTimeline">
            <p class="mb-text-[10px] mb-text-gray-400 mb-font-semibold mb-uppercase">{{ t('Entrata') }}</p>
            <div>
              <label class="mps-label">{{ t('Animazione entrata') }}</label>
              <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.animIn" :options="animationsInGroups" @update:model-value="up('animIn', $event)" />
            </div>
            <div>
              <label class="mps-label">{{ t('Durata') }}</label>
              <div class="mb-flex mb-items-center mb-gap-2">
                <NumberScrubber theme="dark" :modelValue="layer.animInDuration" :min="100" :max="3000" :step="50" emitAs="number" :sliderOnFocus="false" unit="ms" :ariaLabel="t('Durata (ms)')" @update:modelValue="up('animInDuration', $event)" />
              </div>
            </div>
            <div>
              <label class="mps-label">{{ t('Ritardo') }}</label>
              <div class="mb-flex mb-items-center mb-gap-2">
                <NumberScrubber theme="dark" :modelValue="layer.animInDelay" :min="0" :max="3000" :step="50" emitAs="number" :sliderOnFocus="false" unit="ms" :ariaLabel="t('Ritardo (ms)')" @update:modelValue="up('animInDelay', $event)" />
              </div>
            </div>

            <div class="mb-border-t mb-border-gray-700 mb-pt-2 mb-mt-2"></div>
            <p class="mb-text-[10px] mb-text-gray-400 mb-font-semibold mb-uppercase">{{ t('Uscita') }}</p>
            <div>
              <label class="mps-label">{{ t('Animazione uscita') }}</label>
              <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.animOut" :options="animationsOutGroups" @update:model-value="up('animOut', $event)" />
            </div>
            <div>
              <label class="mps-label">{{ t('Durata') }}</label>
              <div class="mb-flex mb-items-center mb-gap-2">
                <NumberScrubber theme="dark" :modelValue="layer.animOutDuration" :min="100" :max="3000" :step="50" emitAs="number" :sliderOnFocus="false" unit="ms" :ariaLabel="t('Durata (ms)')" @update:modelValue="up('animOutDuration', $event)" />
              </div>
            </div>
            <div>
              <label class="mps-label">{{ t('Ritardo') }}</label>
              <div class="mb-flex mb-items-center mb-gap-2">
                <NumberScrubber theme="dark" :modelValue="layer.animOutDelay" :min="0" :max="3000" :step="50" emitAs="number" :sliderOnFocus="false" unit="ms" :ariaLabel="t('Ritardo (ms)')" @update:modelValue="up('animOutDelay', $event)" />
              </div>
            </div>
            <div>
              <label class="mps-label">{{ t('Andamento') }}</label>
              <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.animEasing" :options="OPTS_ANIM_EASING" @update:model-value="up('animEasing', $event)" />
            </div>

            <!-- Character Animation (solo per text) -->
            <template v-if="layer.type === 'text'">
              <div class="mb-border-t mb-border-gray-700 mb-pt-2 mb-mt-2"></div>
              <p class="mb-text-[10px] mb-text-gray-400 mb-font-semibold mb-uppercase">{{ t('Animazione del testo') }}</p>
              <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
                <input type="checkbox" :checked="!!layer.charAnim" @change="toggleCharAnim($event.target.checked)" class="mb-rounded" />
                <span class="mb-text-[11px] mb-text-gray-400">{{ t('Anima lettere o parole') }}</span>
              </label>
              <template v-if="layer.charAnim">
                <div>
                  <label class="mps-label">{{ t('Dividi per') }}</label>
                  <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.charAnim.split || 'chars'" :options="OPTS_CHAR_SPLIT" @update:model-value="upCharAnim('split', $event)" />
                </div>
                <div>
                  <label class="mps-label">{{ t('Direzione') }}</label>
                  <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.charAnim.direction || 'forward'" :options="OPTS_CHAR_DIRECTION" @update:model-value="upCharAnim('direction', $event)" />
                </div>
                <div>
                  <label class="mps-label">{{ t('Ritardo fra le lettere') }}</label>
                  <NumberScrubber theme="dark" :modelValue="layer.charAnim.stagger || 30" :min="5" :max="200" :step="5" :defaultValue="30" emitAs="number" :sliderOnFocus="false" unit="ms" ariaLabel="Stagger (ms)" @update:modelValue="upCharAnim('stagger', $event)" />
                </div>
                <div>
                  <label class="mps-label">{{ t('Spostamento orizzontale') }}</label>
                  <NumberScrubber theme="dark" :modelValue="layer.charAnim.offsetX || 0" :min="-100" :max="100" :step="5" :defaultValue="0" emitAs="number" :sliderOnFocus="false" unit="px" ariaLabel="Offset X (px)" @update:modelValue="upCharAnim('offsetX', $event)" />
                </div>
                <div>
                  <label class="mps-label">{{ t('Spostamento verticale') }}</label>
                  <NumberScrubber theme="dark" :modelValue="layer.charAnim.offsetY || 0" :min="-100" :max="100" :step="5" :defaultValue="0" emitAs="number" :sliderOnFocus="false" unit="px" ariaLabel="Offset Y (px)" @update:modelValue="upCharAnim('offsetY', $event)" />
                </div>
                <div>
                  <label class="mps-label">{{ t('Rotazione') }}</label>
                  <NumberScrubber theme="dark" :modelValue="layer.charAnim.rotation || 0" :min="-180" :max="180" :step="5" :defaultValue="0" emitAs="number" :sliderOnFocus="false" unit="°" :ariaLabel="t('Rotazione (deg)')" @update:modelValue="upCharAnim('rotation', $event)" />
                </div>
                <div>
                  <label class="mps-label">{{ t('Scala iniziale') }}</label>
                  <NumberScrubber theme="dark" :modelValue="layer.charAnim.scale ?? 1" :min="0" :max="3" :step="0.1" :defaultValue="1" emitAs="number" :sliderOnFocus="false" :ariaLabel="t('Scala iniziale')" @update:modelValue="upCharAnim('scale', $event)" />
                </div>
                <div>
                  <label class="mps-label">{{ t('Opacità iniziale') }}</label>
                  <NumberScrubber theme="dark" :modelValue="layer.charAnim.opacity ?? 0" :min="0" :max="100" :step="5" :defaultValue="0" emitAs="number" :sliderOnFocus="false" unit="%" :ariaLabel="t('Opacità iniziale (%)')" @update:modelValue="upCharAnim('opacity', $event)" />
                </div>
                <div>
                  <label class="mps-label">{{ t('Sfocatura iniziale') }}</label>
                  <NumberScrubber theme="dark" :modelValue="layer.charAnim.blur || 0" :min="0" :max="20" :step="1" :defaultValue="0" emitAs="number" :sliderOnFocus="false" unit="px" :ariaLabel="t('Sfocatura iniziale (px)')" @update:modelValue="upCharAnim('blur', $event)" />
                </div>
                <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
                  <input type="checkbox" :checked="layer.charAnim.yoyo" @change="upCharAnim('yoyo', $event.target.checked)" class="mb-rounded" />
                  <span class="mb-text-[11px] mb-text-gray-400">{{ t('Andata e ritorno') }}</span>
                </label>
              </template>
            </template>

            <!-- Loop Animation -->
            <div class="mb-border-t mb-border-gray-700 mb-pt-2 mb-mt-2"></div>
            <p class="mb-text-[10px] mb-text-gray-400 mb-font-semibold mb-uppercase">{{ t('Movimento continuo') }}</p>
            <div>
              <label class="mps-label">{{ t('Animazione continua') }}</label>
              <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.animLoop || 'none'" :options="loopAnimGroups" @update:model-value="up('animLoop', $event)" />
            </div>
            <template v-if="layer.animLoop && layer.animLoop !== 'none'">
              <div>
                <label class="mps-label">{{ t('Durata del ciclo') }}</label>
                <NumberScrubber theme="dark" :modelValue="layer.animLoopDuration || 3000" :min="500" :max="10000" :step="100" :defaultValue="3000" emitAs="number" :sliderOnFocus="false" unit="ms" :ariaLabel="t('Durata loop (ms)')" @update:modelValue="up('animLoopDuration', $event)" />
              </div>
              <div>
                <label class="mps-label">{{ t('Andamento') }}</label>
                <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.animLoopEasing || 'ease-in-out'" :options="EASING_GROUPS" @update:model-value="up('animLoopEasing', $event)" />
              </div>
            </template>

            <!-- Hover Effects -->
            <div class="mb-border-t mb-border-gray-700 mb-pt-2 mb-mt-2"></div>
            <p class="mb-text-[10px] mb-text-gray-400 mb-font-semibold mb-uppercase">{{ t('Al passaggio del mouse') }}</p>
            <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
              <input type="checkbox" :checked="!!layer.hover" @change="toggleHover($event.target.checked)" class="mb-rounded" />
              <span class="mb-text-[11px] mb-text-gray-400">{{ t('Cambia al passaggio del mouse') }}</span>
            </label>
            <template v-if="layer.hover">
              <!-- Transform -->
              <p class="mb-text-[9px] mb-text-gray-500 mb-uppercase mb-mt-1">{{ t('Trasformazione') }}</p>
              <div class="mb-grid mb-grid-cols-2 mb-gap-2">
                <div>
                  <label class="mps-label">{{ t('Scala') }}</label>
                  <NumberScrubber theme="dark" :modelValue="layer.hover.scale ?? 1" :min="0.5" :max="2" :step="0.05" :defaultValue="1" emitAs="number" :sliderOnFocus="false" :ariaLabel="t('Scala')" @update:modelValue="upHover('scale', $event)" />
                </div>
                <div>
                  <label class="mps-label">{{ t('Rotazione Z') }}</label>
                  <NumberScrubber theme="dark" :modelValue="layer.hover.rotation ?? 0" :min="-45" :max="45" :step="1" :defaultValue="0" emitAs="number" :sliderOnFocus="false" unit="°" :ariaLabel="t('Rotazione Z')" @update:modelValue="upHover('rotation', $event)" />
                </div>
                <div>
                  <label class="mps-label">{{ t('Rotazione X') }}</label>
                  <NumberScrubber theme="dark" :modelValue="layer.hover.rotateX ?? 0" :min="-90" :max="90" :step="5" :defaultValue="0" emitAs="number" :sliderOnFocus="false" unit="°" :ariaLabel="t('Rotazione X')" @update:modelValue="upHover('rotateX', $event)" />
                </div>
                <div>
                  <label class="mps-label">{{ t('Rotazione Y') }}</label>
                  <NumberScrubber theme="dark" :modelValue="layer.hover.rotateY ?? 0" :min="-90" :max="90" :step="5" :defaultValue="0" emitAs="number" :sliderOnFocus="false" unit="°" :ariaLabel="t('Rotazione Y')" @update:modelValue="upHover('rotateY', $event)" />
                </div>
                <div>
                  <label class="mps-label">{{ t('Inclinazione orizzontale') }}</label>
                  <NumberScrubber theme="dark" :modelValue="layer.hover.skewX ?? 0" :min="-30" :max="30" :step="1" :defaultValue="0" emitAs="number" :sliderOnFocus="false" unit="°" ariaLabel="Skew X" @update:modelValue="upHover('skewX', $event)" />
                </div>
                <div>
                  <label class="mps-label">{{ t('Inclinazione verticale') }}</label>
                  <NumberScrubber theme="dark" :modelValue="layer.hover.skewY ?? 0" :min="-30" :max="30" :step="1" :defaultValue="0" emitAs="number" :sliderOnFocus="false" unit="°" ariaLabel="Skew Y" @update:modelValue="upHover('skewY', $event)" />
                </div>
                <div>
                  <label class="mps-label">{{ t('Spostamento orizzontale') }}</label>
                  <NumberScrubber theme="dark" :modelValue="layer.hover.x ?? 0" :min="-20" :max="20" :step="1" :defaultValue="0" emitAs="number" :sliderOnFocus="false" unit="%" ariaLabel="Hover offset X (%)" @update:modelValue="upHover('x', $event)" />
                </div>
                <div>
                  <label class="mps-label">{{ t('Spostamento verticale') }}</label>
                  <NumberScrubber theme="dark" :modelValue="layer.hover.y ?? 0" :min="-20" :max="20" :step="1" :defaultValue="0" emitAs="number" :sliderOnFocus="false" unit="%" ariaLabel="Hover offset Y (%)" @update:modelValue="upHover('y', $event)" />
                </div>
              </div>
              <!-- Aspetto -->
              <p class="mb-text-[9px] mb-text-gray-500 mb-uppercase mb-mt-2">{{ t('Aspetto') }}</p>
              <div>
                <label class="mps-label">{{ t('Opacità') }}</label>
                <NumberScrubber theme="dark" :modelValue="layer.hover.opacity ?? 100" :min="0" :max="100" :step="5" :defaultValue="100" emitAs="number" :sliderOnFocus="false" unit="%" :ariaLabel="t('Opacità (%)')" @update:modelValue="upHover('opacity', $event)" />
              </div>
              <template v-if="layer.type === 'text' || layer.type === 'button'">
                <div>
                  <label class="mps-label">{{ t('Colore del testo') }}</label>
                  <FieldColor :modelValue="layer.hover.color || ''" @update:modelValue="upHover('color', $event)" />
                </div>
              </template>
              <div>
                <label class="mps-label">{{ t('Sfondo') }}</label>
                <FieldColor :modelValue="layer.hover.bgColor || ''" @update:modelValue="upHover('bgColor', $event)" />
              </div>
              <div>
                <label class="mps-label">{{ t('Bordo') }}</label>
                <FieldColor :modelValue="layer.hover.borderColor || ''" @update:modelValue="upHover('borderColor', $event)" />
              </div>
              <div>
                <label class="mps-label">{{ t('Raggio') }}</label>
                <NumberScrubber theme="dark" :modelValue="layer.hover.borderRadius ?? ''" :min="0" :max="200" :step="1" emitAs="number" :sliderOnFocus="false" unit="px" ariaLabel="Border radius hover" @update:modelValue="upHover('borderRadius', $event)" />
              </div>
              <!-- Filtri hover -->
              <p class="mb-text-[9px] mb-text-gray-500 mb-uppercase mb-mt-2">{{ t('Filtri') }}</p>
              <div class="mb-grid mb-grid-cols-2 mb-gap-2">
                <div>
                  <label class="mps-label">{{ t('Sfocatura') }}</label>
                  <NumberScrubber theme="dark" :modelValue="layer.hover.blur ?? 0" :min="0" :max="20" :step="1" :defaultValue="0" emitAs="number" :sliderOnFocus="false" unit="px" ariaLabel="Hover blur (px)" @update:modelValue="upHover('blur', $event)" />
                </div>
                <div>
                  <label class="mps-label">{{ t('Luminosità') }}</label>
                  <NumberScrubber theme="dark" :modelValue="layer.hover.brightness ?? 100" :min="0" :max="200" :step="5" :defaultValue="100" emitAs="number" :sliderOnFocus="false" unit="%" :ariaLabel="t('Luminosità (%)')" @update:modelValue="upHover('brightness', $event)" />
                </div>
                <div>
                  <label class="mps-label">{{ t('Scala di grigi') }}</label>
                  <NumberScrubber theme="dark" :modelValue="layer.hover.grayscale ?? 0" :min="0" :max="100" :step="5" :defaultValue="0" emitAs="number" :sliderOnFocus="false" unit="%" :ariaLabel="t('Scala di grigi (%)')" @update:modelValue="upHover('grayscale', $event)" />
                </div>
              </div>
              <!-- Transizione -->
              <p class="mb-text-[9px] mb-text-gray-500 mb-uppercase mb-mt-2">{{ t('Transizione') }}</p>
              <div>
                <label class="mps-label">{{ t('Durata') }}</label>
                <NumberScrubber theme="dark" :modelValue="layer.hover.duration ?? 300" :min="100" :max="1000" :step="50" :defaultValue="300" emitAs="number" :sliderOnFocus="false" unit="ms" :ariaLabel="t('Durata (ms)')" @update:modelValue="upHover('duration', $event)" />
              </div>
              <div>
                <label class="mps-label">{{ t('Andamento') }}</label>
                <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.hover.easing || 'ease'" :options="OPTS_EASING" @update:model-value="upHover('easing', $event)" />
              </div>
              <div>
                <label class="mps-label">{{ t('Cursore') }}</label>
                <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.hover.cursor || ''" :options="OPTS_HOVER_CURSOR" @update:model-value="upHover('cursor', $event)" />
              </div>
            </template>

            <!-- Mask / Clip-Path -->
            <div class="mb-border-t mb-border-gray-700 mb-pt-2 mb-mt-2"></div>
            <p class="mb-text-[10px] mb-text-gray-400 mb-font-semibold mb-uppercase">{{ t('Maschera') }}</p>
            <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
              <input type="checkbox" :checked="!!layer.mask" @change="toggleMask($event.target.checked)" class="mb-rounded" />
              <span class="mb-text-[11px] mb-text-gray-400">{{ t('Maschera attiva') }}</span>
            </label>
            <template v-if="layer.mask">
              <div>
                <label class="mps-label">{{ t('Modello') }}</label>
                <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.mask.preset || 'custom'" :options="OPTS_MASK_PRESET" @update:model-value="applyMaskPreset($event)" />
              </div>
              <div class="mb-grid mb-grid-cols-2 mb-gap-2">
                <div>
                  <label class="mps-label">{{ t('Alto') }}</label>
                  <NumberScrubber theme="dark" :modelValue="layer.mask.top ?? 0" :min="0" :max="100" :step="1" :defaultValue="0" emitAs="number" :sliderOnFocus="false" unit="%" ariaLabel="Mask top (%)" @update:modelValue="upMask('top', $event)" />
                </div>
                <div>
                  <label class="mps-label">{{ t('Destra') }}</label>
                  <NumberScrubber theme="dark" :modelValue="layer.mask.right ?? 0" :min="0" :max="100" :step="1" :defaultValue="0" emitAs="number" :sliderOnFocus="false" unit="%" ariaLabel="Mask right (%)" @update:modelValue="upMask('right', $event)" />
                </div>
                <div>
                  <label class="mps-label">{{ t('Basso') }}</label>
                  <NumberScrubber theme="dark" :modelValue="layer.mask.bottom ?? 0" :min="0" :max="100" :step="1" :defaultValue="0" emitAs="number" :sliderOnFocus="false" unit="%" ariaLabel="Mask bottom (%)" @update:modelValue="upMask('bottom', $event)" />
                </div>
                <div>
                  <label class="mps-label">{{ t('Sinistra') }}</label>
                  <NumberScrubber theme="dark" :modelValue="layer.mask.left ?? 0" :min="0" :max="100" :step="1" :defaultValue="0" emitAs="number" :sliderOnFocus="false" unit="%" ariaLabel="Mask left (%)" @update:modelValue="upMask('left', $event)" />
                </div>
              </div>
            </template>

            <!-- Ken Burns per Layer -->
            <template v-if="layer.type === 'image'">
              <div class="mb-border-t mb-border-gray-700 mb-pt-2 mb-mt-2"></div>
              <p class="mb-text-[10px] mb-text-gray-400 mb-font-semibold mb-uppercase">{{ t('Zoom lento (Ken Burns)') }}</p>
              <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
                <input type="checkbox" :checked="!!layer.kenBurns" @change="toggleKenBurns($event.target.checked)" class="mb-rounded" />
                <span class="mb-text-[11px] mb-text-gray-400">{{ t('Attivo') }}</span>
              </label>
              <template v-if="layer.kenBurns">
                <div>
                  <label class="mps-label">{{ t('Tipo') }}</label>
                  <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.kenBurns.type || 'zoomIn'" :options="OPTS_KENBURNS_TYPE" @update:model-value="upKenBurns('type', $event)" />
                </div>
                <div>
                  <label class="mps-label">{{ t('Intensità') }}</label>
                  <NumberScrubber theme="dark" :modelValue="layer.kenBurns.intensity ?? 20" :min="5" :max="50" :step="5" :defaultValue="20" emitAs="number" :sliderOnFocus="false" unit="%" :ariaLabel="t('Intensità (%)')" @update:modelValue="upKenBurns('intensity', $event)" />
                </div>
                <div>
                  <label class="mps-label">{{ t('Durata') }}</label>
                  <NumberScrubber theme="dark" :modelValue="layer.kenBurns.duration ?? 5000" :min="1000" :max="20000" :step="500" :defaultValue="5000" emitAs="number" :sliderOnFocus="false" unit="ms" :ariaLabel="t('Durata (ms)')" @update:modelValue="upKenBurns('duration', $event)" />
                </div>
              </template>
            </template>

            <!-- Motion Path -->
            <div class="mb-border-t mb-border-gray-700 mb-pt-2 mb-mt-2"></div>
            <p class="mb-text-[10px] mb-text-gray-400 mb-font-semibold mb-uppercase">{{ t('Percorso') }}</p>
            <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
              <input type="checkbox" :checked="!!layer.motionPath" @change="toggleMotionPath($event.target.checked)" class="mb-rounded" />
              <span class="mb-text-[11px] mb-text-gray-400">{{ t('Anima lungo percorso') }}</span>
            </label>
            <template v-if="layer.motionPath">
              <div>
                <label class="mps-label">{{ t('Preset percorso') }}</label>
                <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.motionPath.preset || 'circle'" :options="OPTS_MOTION_PRESET" @update:model-value="applyMotionPreset($event)" />
              </div>
              <template v-if="layer.motionPath.preset === 'custom'">
                <div>
                  <label class="mps-label">{{ t('Percorso SVG (d="")') }}</label>
                  <textarea :value="layer.motionPath.path" @input="upMotionPath('path', $event.target.value)" rows="2"
                    class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded mb-px-2 mb-py-1 mb-text-[10px] mb-text-gray-900 mb-resize-y mb-font-mono" placeholder="M0,0 C50,0 50,100 100,100"></textarea>
                </div>
              </template>
              <div>
                <label class="mps-label">{{ t('Durata') }}</label>
                <NumberScrubber theme="dark" :modelValue="layer.motionPath.duration ?? 3000" :min="500" :max="120000" :step="500" :defaultValue="3000" emitAs="number" :sliderOnFocus="false" unit="ms" :ariaLabel="t('Durata (ms)')" @update:modelValue="upMotionPath('duration', $event)" />
              </div>
              <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
                <input type="checkbox" :checked="layer.motionPath.autoRotate" @change="upMotionPath('autoRotate', $event.target.checked)" class="mb-rounded" />
                <span class="mb-text-[11px] mb-text-gray-400">{{ t('Ruota lungo il percorso') }}</span>
              </label>
              <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
                <input type="checkbox" :checked="layer.motionPath.loop !== false" @change="upMotionPath('loop', $event.target.checked)" class="mb-rounded" />
                <span class="mb-text-[11px] mb-text-gray-400">{{ t('Ripeti') }}</span>
              </label>
              <div>
                <label class="mps-label">{{ t('Andamento') }}</label>
                <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.motionPath.easing || 'linear'" :options="EASING_GROUPS" @update:model-value="upMotionPath('easing', $event)" />
              </div>
            </template>

            <!-- Parallax Layer -->
            <div class="mb-border-t mb-border-gray-700 mb-pt-2 mb-mt-2"></div>
            <p class="mb-text-[10px] mb-text-gray-400 mb-font-semibold mb-uppercase">{{ t('Parallasse') }}</p>
            <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
              <input type="checkbox" :checked="!!layer.parallax" @change="toggleParallax($event.target.checked)" class="mb-rounded" />
              <span class="mb-text-[11px] mb-text-gray-400">{{ t('Parallasse attiva') }}</span>
            </label>
            <template v-if="layer.parallax">
              <div>
                <label class="mps-label">{{ t('Tipo') }}</label>
                <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.parallax.type || 'mouse'" :options="OPTS_PARALLAX_TYPE" @update:model-value="upParallax('type', $event)" />
              </div>
              <div>
                <label class="mps-label">{{ t('Profondità') }}</label>
                <NumberScrubber theme="dark" :modelValue="layer.parallax.depth ?? 5" :min="1" :max="20" :step="1" :defaultValue="5" emitAs="number" :sliderOnFocus="false" :ariaLabel="t('Profondità')" @update:modelValue="upParallax('depth', $event)" />
              </div>
              <template v-if="layer.parallax.type === 'mouse' || layer.parallax.type === 'both'">
                <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
                  <input type="checkbox" :checked="layer.parallax.tilt3d" @change="upParallax('tilt3d', $event.target.checked)" class="mb-rounded" />
                  <span class="mb-text-[11px] mb-text-gray-400">{{ t('Inclinazione 3D') }}</span>
                </label>
              </template>
              <template v-if="layer.parallax.type === 'scroll' || layer.parallax.type === 'both'">
                <div>
                  <label class="mps-label">{{ t('Velocità con lo scorrimento') }}</label>
                  <NumberScrubber theme="dark" :modelValue="layer.parallax.scrollSpeed ?? 0.5" :min="0.1" :max="2" :step="0.1" :defaultValue="0.5" emitAs="number" :sliderOnFocus="false" :ariaLabel="t('Velocità con lo scorrimento')" @update:modelValue="upParallax('scrollSpeed', $event)" />
                </div>
              </template>
            </template>

            <!-- Movimento con lo scorrimento -->
            <div class="mb-border-t mb-border-gray-700 mb-pt-2 mb-mt-2"></div>
            <p class="mb-text-[10px] mb-text-gray-400 mb-font-semibold mb-uppercase mb-flex mb-items-center mb-gap-1">{{ t('Movimento con lo scorrimento') }}<InfoTip :testo="t('Con «Timeline a scorrimento» accesa conta il tratto in cui lo slider resta fermo in cima; se no, l’uscita dello slider dalla finestra. Spostamenti in px della tela del computer.')" :titolo="t('Movimento con lo scorrimento')" /></p>
            <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
              <input type="checkbox" :checked="!!layer.scrollMove" @change="toggleScrollMove($event.target.checked)" class="mb-rounded" />
              <span class="mb-text-[11px] mb-text-gray-400">{{ t('Si muove mentre la pagina scorre') }}</span>
            </label>
            <template v-if="layer.scrollMove">
              <div class="mb-grid mb-grid-cols-2 mb-gap-2">
                <div>
                  <label class="mps-label">{{ t('Inizio del tratto') }}</label>
                  <NumberScrubber theme="dark" :modelValue="layer.scrollMove.inizio ?? 0" :min="0" :max="100" :step="1" :defaultValue="0" emitAs="number" :sliderOnFocus="false" unit="%" :ariaLabel="t('Inizio del tratto')" @update:modelValue="upScrollMove('inizio', $event)" />
                </div>
                <div>
                  <label class="mps-label">{{ t('Fine del tratto') }}</label>
                  <NumberScrubber theme="dark" :modelValue="layer.scrollMove.fine ?? 100" :min="0" :max="100" :step="1" :defaultValue="100" emitAs="number" :sliderOnFocus="false" unit="%" :ariaLabel="t('Fine del tratto')" @update:modelValue="upScrollMove('fine', $event)" />
                </div>
              </div>
              <div>
                <label class="mps-label mb-flex mb-items-center mb-gap-1">{{ t('Andamento') }}<InfoTip :testo="t('Morbido: fra una tappa e l’altra il livello parte e arriva piano. Costante: va a velocità uniforme e la scala cresce in proporzione, così uno zoom che attraversa più tappe non rallenta a ogni tappa.')" :titolo="t('Andamento')" /></label>
                <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.scrollMove.andamento || 'morbido'" :options="OPTS_ANDAMENTO" @update:model-value="upScrollMove('andamento', $event === 'morbido' ? undefined : $event)" />
              </div>
              <!-- Le tappe del percorso (1.4.1352): tutte, col punto in cui stanno. Prima si vedevano solo la prima e
                   l'ultima («All'inizio», «Alla fine», 30 cursori): il momento in cui un livello si muove non si vedeva. -->
              <div class="mps-tappe">
                <div class="mb-flex mb-items-center mb-justify-between">
                  <label class="mps-label mb-mb-0 mb-flex mb-items-center mb-gap-1">{{ t('Tappe del percorso') }}<InfoTip :testo="t('Ogni tappa dice com’è il livello a quel punto del suo tratto (0% = inizio, 100% = fine); fra una tappa e l’altra il livello passa da un valore all’altro. Clicca una tappa per vederla sulla tela e cambiarla: la tela va a quel punto dello scorrimento.')" :titolo="t('Tappe del percorso')" /></label>
                  <span class="mb-text-[10px] mb-text-gray-500">{{ tappeSM.length }}</span>
                </div>
                <!-- il tratto: i punti delle tappe e, con l'anteprima accesa, dov'è lo scorrimento -->
                <div class="mps-tappe-barra" role="presentation">
                  <span v-for="(tp, i) in tappeSM" :key="'b' + i" :class="['mps-tappe-punto', { 'is-on': i === tappaScelta }]" :style="{ left: tp.p + '%' }" :title="fmtP(tp.p) + '%'" @click="scegliTappa(i)"></span>
                  <span v-if="scrollPreview != null" class="mps-tappe-ora" :style="{ left: delLivello(layer.scrollMove, scrollPreview) + '%' }"></span>
                </div>
                <div class="mb-space-y-0.5">
                  <button v-for="(tp, i) in tappeSM" :key="'r' + i" type="button" :class="['mps-tappa', { 'is-on': i === tappaScelta }]" @click="scegliTappa(i)">
                    <span class="mps-tappa-p">{{ fmtP(tp.p) }}%</span>
                    <span class="mps-tappa-v">{{ riassuntoTappa(tp) }}</span>
                  </button>
                </div>
                <div class="mb-flex mb-gap-1">
                  <button type="button" class="mps-tappe-btn" @click="aggiungiTappa">+ {{ t('Tappa') }}</button>
                  <button type="button" class="mps-tappe-btn" :disabled="tappeSM.length <= 2" @click="eliminaTappa">{{ t('Elimina tappa') }}</button>
                </div>
                <template v-if="tappaCorrente">
                  <div>
                    <label class="mps-label">{{ t('A che punto del tratto') }}</label>
                    <NumberScrubber theme="dark" :modelValue="tappaCorrente.p" :min="0" :max="100" :step="0.5" :defaultValue="0" emitAs="number" :sliderOnFocus="false" unit="%" :ariaLabel="t('A che punto del tratto')" @update:modelValue="upTappa('p', $event)" />
                  </div>
                  <div class="mb-grid mb-grid-cols-1 mb-gap-2">
                    <div v-for="c in campiTappa" :key="c.k">
                      <label class="mps-label mb-flex mb-items-center mb-justify-between">{{ t(c.l) }}<button type="button" class="mps-tappe-x" :title="t('Non muovere più questa proprietà')" :aria-label="t('Non muovere più questa proprietà')" @click="togliChiave(c.k)">&times;</button></label>
                      <NumberScrubber theme="dark" :modelValue="tappaCorrente.v[c.k] ?? c.d" :min="c.min" :max="c.max" :step="c.step" :defaultValue="c.d" emitAs="number" :sliderOnFocus="false" :unit="c.u" :ariaLabel="t(c.l)" @update:modelValue="upTappa(c.k, $event)" />
                    </div>
                  </div>
                  <FieldSelect v-if="campiAggiungibili.length" ui="dropdown" size="compact" theme="dark" :model-value="''" :options="opzioniAggiungi" @update:model-value="aggiungiChiave" />
                </template>
              </div>
            </template>
          </template>

          <!-- Modo TIMELINE -->
          <template v-else>
            <p class="mb-text-[10px] mb-text-gray-500 mb-italic mb-mb-2">
              {{ t('Usa la linea del tempo sotto la tela per aggiungere e spostare i fotogrammi chiave.') }}
            </p>
            <KeyframeProperties
              :keyframe="selectedKeyframe"
              :timeline="layer.timeline"
              @update-keyframe="(id, updates) => $emit('update-keyframe', id, updates)"
              @capture-from-canvas="$emit('capture-from-canvas')"
            />
          </template>
        </template>

      </div>
    </template>

    <!-- No selection -->
    <div v-else class="mb-p-4 mb-text-xs mb-text-gray-500 mb-text-center mb-mt-8">
      {{ t('Seleziona un livello per modificarne le proprietà') }}
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import IconPicker from './IconPicker.vue';
import iconsSvg from './uikitIconsSvg.js';
import FieldColor from '@/components/Builder/fields/FieldColor.vue';
import FieldSelect from '../Builder/fields/FieldSelect.vue';
import NumberScrubber from '../Builder/fields/NumberScrubber.vue';
import KeyframeProperties from './KeyframeProperties.vue';
import InfoTip from '../Builder/InfoTip.vue';
// Curve di ingresso, uscita, loop e percorso: le stesse della timeline (il PHP le traduce in cubic-bezier,
// easing_to_css). La curva dell'hover resta fra le 5 di base, che il PHP accetta per l'hover.
import { defaultTimeline, EASING_GROUPS } from './timelineUtils.js';
import { t } from '@/i18n';
import { nomeLivello, tappeDi, chiaviUsate, valoreSM, delLivello, dellaScena, SM_CHIAVI } from './livelloUtils.js';

const props = defineProps({
  layer: { type: Object, default: null },
  selectedKeyframeId: { type: String, default: null },
  activeBreakpoint: { type: String, default: 'desktop' },
  slideLayers: { type: Array, default: () => [] },
  // Anteprima dello scorrimento dell'editor (0–100 o null): le tappe del movimento la leggono e la spostano.
  scrollPreview: { type: Number, default: null },
});

// Il nome che il livello avrebbe senza «Nome del livello» (testo, file, id parlante, tipo): segnaposto del campo.
const nomeSuggerito = computed(() => (props.layer ? nomeLivello({ ...props.layer, name: '' }) : ''));

const targetableLayers = computed(() => {
  const cur = props.layer?.id;
  return (props.slideLayers || []).filter(l => l && l.id && l.id !== cur);
});

const targetLayerOptions = computed(() => [
  { value: '', label: '— Seleziona un layer —' },
  ...targetableLayers.value.map(l => ({ value: l.id, label: targetLayerLabel(l) })),
]);

function fmt(v) {
  if (v === 'auto' || v === undefined || v === null) return v ?? '';
  const n = Number(v);
  if (!Number.isFinite(n)) return v;
  return (Math.round(n * 10) / 10).toString();
}

function targetLayerLabel(l) {
  const icons = { text: 'T', image: '🖼', video: '▶', button: '▢', icon: '★', shape: '◆', audio: '♫' };
  const ico = icons[l.type] || '?';
  let name = l.type;
  if (l.type === 'text' || l.type === 'button') name = l.content || l.type;
  else if (l.type === 'icon') name = l.iconName || 'icona';
  else if (l.type === 'image') name = 'Immagine';
  else if (l.type === 'video') name = 'Video';
  else if (l.type === 'shape') name = 'Forma';
  const trimmed = String(name).replace(/\s+/g, ' ').trim();
  const short = trimmed.length > 40 ? trimmed.slice(0, 40) + '…' : trimmed;
  return `${ico} ${short}`;
}

const emit = defineEmits(['update', 'update-keyframe', 'capture-from-canvas', 'set-scroll-preview']);

const hasTimeline = computed(() => {
  return !!(props.layer?.timeline?.keyframes?.length);
});

const selectedKeyframe = computed(() => {
  if (!hasTimeline.value || !props.selectedKeyframeId) return null;
  return props.layer.timeline.keyframes.find(kf => kf.id === props.selectedKeyframeId) || null;
});

function setAnimMode(mode) {
  if (mode === 'timeline' && !hasTimeline.value) {
    // Attiva timeline con valori default basati sulla posizione corrente del layer
    up('timeline', defaultTimeline(props.layer));
  } else if (mode === 'simple' && hasTimeline.value) {
    // Disattiva timeline
    up('timeline', null);
  }
}

const showIconPicker = ref(false);

const activeTab = ref('Contenuto');

// Il nome del livello e dello schermo come li legge chi usa l'editor (17 nov 2026: «Livello Button», «Visibile su mobile»)
const NOMI_TIPO = { text: t('Testo'), button: t('Pulsante'), image: t('Immagine'), video: t('Video'), icon: t('Icona'),
  shape: t('Forma'), audio: t('Audio') };
const nomeTipo = computed(() => NOMI_TIPO[props.layer?.type] || (props.layer?.type || ''));
const NOMI_SCHERMO = { desktop: 'Desktop', notebook: 'Notebook', tablet: 'Tablet', mobile: 'Mobile' };
const nomeSchermo = computed(() => NOMI_SCHERMO[props.activeBreakpoint] || props.activeBreakpoint);
const tabs = ['Contenuto', 'Posizione', 'Stile', 'Animazione'];

// Responsive: risolve il valore effettivo per il breakpoint attivo
function rv(key) {
  if (!props.layer) return undefined;
  const bp = props.activeBreakpoint;
  if (bp !== 'desktop') {
    const chain = ['notebook', 'tablet', 'mobile'];
    const idx = chain.indexOf(bp);
    for (let i = idx; i >= 0; i--) {
      const ov = props.layer.responsive?.[chain[i]];
      if (ov && ov[key] !== undefined && ov[key] !== null) return ov[key];
    }
  }
  return props.layer[key];
}

function isOverridden(key) {
  const bp = props.activeBreakpoint;
  if (bp === 'desktop') return false;
  return props.layer?.responsive?.[bp]?.[key] !== undefined && props.layer?.responsive?.[bp]?.[key] !== null;
}

function clearOverride(key) {
  const bp = props.activeBreakpoint;
  if (bp === 'desktop' || !props.layer?.responsive?.[bp]) return;
  delete props.layer.responsive[bp][key];
}

const hasAnyOverride = computed(() => {
  const bp = props.activeBreakpoint;
  if (bp === 'desktop') return false;
  const ov = props.layer?.responsive?.[bp];
  return ov && Object.keys(ov).length > 0;
});

function clearAllOverrides() {
  const bp = props.activeBreakpoint;
  if (bp === 'desktop' || !props.layer?.responsive) return;
  props.layer.responsive[bp] = null;
}

const animationsInGroups = [
  { label: t('Base'), options: [
    { value: 'none', label: t('Nessuna') },
    { value: 'fadeIn', label: t('Dissolvenza') },
  ]},
  { label: t('Dissolvenza'), options: [
    { value: 'fadeInUp', label: t('Dissolvenza dal basso') },
    { value: 'fadeInDown', label: t('Dissolvenza dall’alto') },
    { value: 'fadeInLeft', label: t('Dissolvenza da sinistra') },
    { value: 'fadeInRight', label: t('Dissolvenza da destra') },
  ]},
  { label: t('Scivola'), options: [
    { value: 'slideInLeft', label: t('Scivola da sinistra') },
    { value: 'slideInRight', label: t('Scivola da destra') },
    { value: 'slideInUp', label: t('Scivola dal basso') },
    { value: 'slideInDown', label: t('Scivola dall’alto') },
    { value: 'slideShortFromTop', label: t('Breve dall’alto') },
    { value: 'slideShortFromBottom', label: t('Breve dal basso') },
    { value: 'slideShortFromLeft', label: t('Breve da sinistra') },
    { value: 'slideShortFromRight', label: t('Breve da destra') },
    { value: 'smoothSlideFromBottom', label: t('Morbida dal basso') },
    { value: 'smoothSlideFromTop', label: t('Morbida dall’alto') },
    { value: 'smoothSlideFromLeft', label: t('Morbida da sinistra') },
    { value: 'smoothSlideFromRight', label: t('Morbida da destra') },
  ]},
  { label: t('Inclinata'), options: [
    { value: 'skewFromLeft', label: t('Inclinata da sinistra') },
    { value: 'skewFromRight', label: t('Inclinata da destra') },
    { value: 'skewShortFromLeft', label: t('Inclinata breve da sinistra') },
    { value: 'skewShortFromRight', label: t('Inclinata breve da destra') },
  ]},
  { label: t('Ribaltata 3D'), options: [
    { value: 'flipFromTop', label: t('Ribaltata dall’alto') },
    { value: 'flipFromBottom', label: t('Ribaltata dal basso') },
    { value: 'flipFromLeft', label: t('Ribaltata da sinistra') },
    { value: 'flipFromRight', label: t('Ribaltata da destra') },
  ]},
  { label: t('Rotazione'), options: [
    { value: 'rotateIn', label: t('Ruota entrando') },
    { value: 'rotateInFromBottom', label: t('Ruota dal basso') },
    { value: 'rotate3D', label: t('Ruota in 3D') },
    { value: 'rotateInFromLeft', label: t('Ruota da sinistra') },
    { value: 'rotateInFromRight', label: t('Ruota da destra') },
  ]},
  { label: t('Zoom e rimbalzo'), options: [
    { value: 'zoomIn', label: t('Si ingrandisce') },
    { value: 'bounceIn', label: t('Rimbalzo') },
    { value: 'popUpSmooth', label: t('Spunta morbida') },
    { value: 'popUpBack', label: t('Spunta e torna indietro') },
    { value: 'bounceInUp', label: t('Rimbalzo dal basso') },
    { value: 'bounceInDown', label: t('Rimbalzo dall’alto') },
  ]},
  { label: t('Maschera'), options: [
    { value: 'maskFromLeft', label: t('Si scopre da sinistra') },
    { value: 'maskFromRight', label: t('Si scopre da destra') },
    { value: 'maskFromTop', label: t('Si scopre dall’alto') },
    { value: 'maskFromBottom', label: t('Si scopre dal basso') },
    { value: 'maskZoomOut', label: t('Si scopre dal centro con zoom') },
    { value: 'maskCenter', label: t('Si scopre dal centro') },
  ]},
];

const animationsOutGroups = [
  { label: t('Base'), options: [
    { value: 'none', label: t('Nessuna') },
    { value: 'fadeOut', label: t('Dissolvenza') },
  ]},
  { label: t('Dissolvenza'), options: [
    { value: 'fadeOutUp', label: t('Dissolvenza verso l’alto') },
    { value: 'fadeOutDown', label: t('Dissolvenza verso il basso') },
    { value: 'fadeOutLeft', label: t('Dissolvenza verso sinistra') },
    { value: 'fadeOutRight', label: t('Dissolvenza verso destra') },
  ]},
  { label: t('Scivola'), options: [
    { value: 'slideOutLeft', label: t('Scivola verso sinistra') },
    { value: 'slideOutRight', label: t('Scivola verso destra') },
    { value: 'slideOutUp', label: t('Scivola verso l’alto') },
    { value: 'slideOutDown', label: t('Scivola verso il basso') },
    { value: 'slideShortOutTop', label: t('Breve verso l’alto') },
    { value: 'slideShortOutBottom', label: t('Breve verso il basso') },
    { value: 'slideShortOutLeft', label: t('Breve verso sinistra') },
    { value: 'slideShortOutRight', label: t('Breve verso destra') },
    { value: 'smoothSlideOutBottom', label: t('Morbida verso il basso') },
    { value: 'smoothSlideOutTop', label: t('Morbida verso l’alto') },
    { value: 'smoothSlideOutLeft', label: t('Morbida verso sinistra') },
    { value: 'smoothSlideOutRight', label: t('Morbida verso destra') },
  ]},
  { label: t('Inclinata'), options: [
    { value: 'skewOutLeft', label: t('Inclinata verso sinistra') },
    { value: 'skewOutRight', label: t('Inclinata verso destra') },
    { value: 'skewShortOutLeft', label: t('Inclinata breve verso sinistra') },
    { value: 'skewShortOutRight', label: t('Inclinata breve verso destra') },
  ]},
  { label: t('Ribaltata 3D'), options: [
    { value: 'flipOutTop', label: t('Ribaltata verso l’alto') },
    { value: 'flipOutBottom', label: t('Ribaltata verso il basso') },
    { value: 'flipOutLeft', label: t('Ribaltata verso sinistra') },
    { value: 'flipOutRight', label: t('Ribaltata verso destra') },
  ]},
  { label: t('Rotazione'), options: [
    { value: 'rotateOut', label: t('Ruota uscendo') },
    { value: 'rotateOutToBottom', label: t('Ruota verso il basso') },
    { value: 'rotateOut3D', label: t('Ruota in 3D') },
    { value: 'rotateOutToLeft', label: t('Ruota verso sinistra') },
    { value: 'rotateOutToRight', label: t('Ruota verso destra') },
  ]},
  { label: t('Zoom e rimbalzo'), options: [
    { value: 'zoomOut', label: t('Si rimpicciolisce') },
    { value: 'bounceOut', label: t('Rimbalzo') },
    { value: 'popOutSmooth', label: t('Sparisce morbida') },
    { value: 'popOutBack', label: t('Si gonfia e sparisce') },
    { value: 'bounceOutUp', label: t('Rimbalzo verso l’alto') },
    { value: 'bounceOutDown', label: t('Rimbalzo verso il basso') },
  ]},
  { label: t('Maschera'), options: [
    { value: 'maskOutLeft', label: t('Si copre verso sinistra') },
    { value: 'maskOutRight', label: t('Si copre verso destra') },
    { value: 'maskOutTop', label: t('Si copre verso l’alto') },
    { value: 'maskOutBottom', label: t('Si copre verso il basso') },
    { value: 'maskZoomIn', label: t('Si chiude al centro con zoom') },
    { value: 'maskOutCenter', label: t('Si chiude al centro') },
  ]},
];

const loopAnimGroups = [
  { label: t('Nessuno'), options: [{ value: 'none', label: t('Nessuno') }] },
  { label: t('Pendolo'), options: [
    { value: 'pendulum', label: t('Pendolo') },
    { value: 'pendulumBelow', label: t('Pendolo basso') },
    { value: 'pendulumAbove', label: t('Pendolo alto') },
    { value: 'pendulumLeft', label: t('Pendolo a sinistra') },
    { value: 'pendulumRight', label: t('Pendolo a destra') },
  ]},
  { label: t('Onda'), options: [
    { value: 'waveSmallLeft', label: t('Onda piccola a sinistra') },
    { value: 'waveSmallRight', label: t('Onda piccola a destra') },
    { value: 'waveBigLeft', label: t('Onda grande a sinistra') },
    { value: 'waveBigRight', label: t('Onda grande a destra') },
  ]},
  { label: t('Vibrazione'), options: [
    { value: 'wiggleY', label: t('Vibrazione verticale') },
    { value: 'wiggleX', label: t('Vibrazione orizzontale') },
    { value: 'wiggle3D', label: t('Vibrazione 3D') },
    { value: 'crazyWiggle', label: t('Vibrazione scatenata') },
  ]},
  { label: t('Rotazione'), options: [
    { value: 'spinCW', label: t('Gira in senso orario') },
    { value: 'spinCCW', label: t('Gira in senso antiorario') },
  ]},
  { label: t('Effetto'), options: [
    { value: 'blinkLoop', label: t('Lampeggio') },
    { value: 'floatLoop', label: t('Galleggiamento') },
    { value: 'pulseLoop', label: t('Pulsazione') },
    { value: 'breathLoop', label: t('Respiro') },
    { value: 'slideHLoop', label: t('Scivola avanti e indietro') },
    { value: 'windLoop', label: t('Vento (piega dal basso)') },
    { value: 'hoverLoop', label: t('Fluttua piano') },
  ]},
];

function up(key, val) {
  emit('update', key, val);
}

function setAction(field, value) {
  const current = props.layer?.action || { type: 'none' };
  if (field === 'type') {
    if (value === 'none') {
      up('action', null);
    } else {
      up('action', { ...current, type: value });
    }
  } else {
    up('action', { ...current, [field]: value });
  }
}

function upStato(key, val) {
  up('stato', { ...(props.layer?.stato || {}), [key]: val });
}

function upPulisci(key, val) {
  up('pulisci', { ...(props.layer?.pulisci || { attivo: true }), [key]: val });
}

function toggleCharAnim(enabled) {
  if (enabled) {
    up('charAnim', { split: 'chars', direction: 'forward', stagger: 30 });
  } else {
    up('charAnim', null);
  }
}

function upCharAnim(key, val) {
  if (!props.layer?.charAnim) return;
  up('charAnim', { ...props.layer.charAnim, [key]: val });
}

function toggleHover(enabled) {
  if (enabled) {
    up('hover', { scale: 1, rotation: 0, rotateX: 0, rotateY: 0, skewX: 0, skewY: 0, opacity: 100, x: 0, y: 0, duration: 300, easing: 'ease', color: '', bgColor: '', borderColor: '', borderRadius: '', blur: 0, brightness: 100, grayscale: 0, cursor: '' });
  } else {
    up('hover', null);
  }
}

function upHover(key, val) {
  if (!props.layer?.hover) return;
  up('hover', { ...props.layer.hover, [key]: val });
}

function pickImage() {
  if (!window.wp?.media) return;
  const frame = wp.media({ title: 'Seleziona immagine', multiple: false, library: { type: 'image' } });
  frame.on('select', () => {
    const url = frame.state().get('selection').first().toJSON().url;
    up('imageSrc', url);
  });
  frame.open();
}

function pickVideo() {
  if (!window.wp?.media) return;
  const frame = wp.media({ title: 'Seleziona video', multiple: false, library: { type: 'video' } });
  frame.on('select', () => {
    const url = frame.state().get('selection').first().toJSON().url;
    up('videoSrc', url);
  });
  frame.open();
}

function pickAudio() {
  if (!window.wp?.media) return;
  const frame = wp.media({ title: 'Seleziona audio', multiple: false, library: { type: 'audio' } });
  frame.on('select', () => {
    const url = frame.state().get('selection').first().toJSON().url;
    up('audioSrc', url);
  });
  frame.open();
}

const blendModes = ['normal','multiply','screen','overlay','darken','lighten','color-dodge','color-burn','hard-light','soft-light','difference','exclusion','hue','saturation','color','luminosity'];

// Option arrays per FieldSelect (stessi value dei vecchi <option>: dati salvati invariati)
const OPTS_TAG = ['h1','h2','h3','h4','h5','h6','p','span','div'].map(v => ({ value: v, label: v }));
const OPTS_LINK_TARGET = [
  { value: '_self', label: t('Stessa scheda') },
  { value: '_blank', label: t('Nuova scheda') },
];
const OPTS_ACTION_TYPE = [
  { value: 'none', label: t('Nessuna') },
  { value: 'nextSlide', label: t('Slide successiva') },
  { value: 'prevSlide', label: t('Slide precedente') },
  { value: 'goToSlide', label: t('Vai alla slide…') },
  { value: 'openUrl', label: t('Apri un indirizzo') },
  { value: 'scrollBelow', label: t('Scorri sotto lo slider') },
  { value: 'toggleLayer', label: t('Mostra o nasconde un livello') },
  { value: 'setState', label: t('Imposta uno stato') },
  { value: 'cycleState', label: t('Stato successivo (a giro)') },
];
const OPTS_STATO_EFFETTO = [
  { value: 'fade', label: t('Dissolvenza') },
  { value: 'su', label: t('Sale dal basso') },
  { value: 'giu', label: t('Scende dall’alto') },
  { value: 'sinistra', label: t('Da sinistra') },
  { value: 'destra', label: t('Da destra') },
  { value: 'zoom', label: t('Si ingrandisce') },
  { value: 'nessuno', label: t('Subito, senza effetto') },
];
const OPTS_FONT_WEIGHT = ['300','400','500','600','700','800','900'].map(v => ({ value: v, label: v }));
const OPTS_FONT_STYLE = [
  { value: 'normal', label: t('Normale') },
  { value: 'italic', label: t('Corsivo') },
];
const OPTS_TEXT_TRANSFORM = [
  { value: 'none', label: t('Nessuna') },
  { value: 'uppercase', label: t('MAIUSCOLO') },
  { value: 'lowercase', label: t('minuscolo') },
  { value: 'capitalize', label: t('Iniziali maiuscole') },
];
const OPTS_TEXT_DECORATION = [
  { value: 'none', label: t('Nessuna') },
  { value: 'underline', label: t('Sottolineato') },
  { value: 'line-through', label: t('Barrato') },
  { value: 'overline', label: t('Sopralineato') },
];
const OPTS_BORDER_STYLE = [
  { value: 'solid', label: t('Solido') },
  { value: 'dashed', label: t('Tratteggiato') },
  { value: 'dotted', label: t('Puntinato') },
  { value: 'double', label: t('Doppio') },
];
const OPTS_OBJECT_FIT = [
  { value: 'cover', label: t('Copri (taglia)') },
  { value: 'contain', label: t('Contieni (intera)') },
  { value: 'fill', label: t('Riempi (deforma)') },
  { value: 'none', label: t('Nessuno') },
];
const OPTS_OBJECT_POSITION = [
  { value: 'center', label: t('Centro') },
  { value: 'top', label: t('Alto') },
  { value: 'bottom', label: t('Basso') },
  { value: 'left', label: t('Sinistra') },
  { value: 'right', label: t('Destra') },
  { value: 'top left', label: t('In alto a sinistra') },
  { value: 'top right', label: t('In alto a destra') },
  { value: 'bottom left', label: t('In basso a sinistra') },
  { value: 'bottom right', label: t('In basso a destra') },
];
const OPTS_BLEND_MODE = blendModes.map(v => ({ value: v, label: v }));
const OPTS_SFX_EFFECT = [
  { value: 'blockRight', label: t('Blocco da destra') },
  { value: 'blockLeft', label: t('Blocco da sinistra') },
  { value: 'blockDown', label: t('Blocco dall’alto') },
  { value: 'blockUp', label: t('Blocco dal basso') },
];
const OPTS_CURSOR = [
  { value: 'auto', label: t('Automatico') },
  { value: 'pointer', label: t('Manina') },
  { value: 'default', label: t('Freccia') },
  { value: 'move', label: t('Sposta') },
  { value: 'crosshair', label: t('Mirino') },
  { value: 'text', label: t('Testo') },
  { value: 'grab', label: t('Mano aperta') },
  { value: 'none', label: t('Nascosto') },
];
// L'andamento delle entrate e uscite: i livelli nuovi e quelli dei temi hanno cubic-bezier(.22,1,.36,1), che il menu
// degli andamenti non elencava e mostrava vuoto («—»).
const OPTS_ANIM_EASING = [{ value: 'cubic-bezier(.22,1,.36,1)', label: t('Morbido, frena alla fine (predefinito)') }, ...EASING_GROUPS];
const OPTS_EASING = ['ease','ease-in','ease-out','ease-in-out','linear'].map(v => ({ value: v, label: v }));
const OPTS_CHAR_SPLIT = [
  { value: 'chars', label: t('Caratteri') },
  { value: 'words', label: t('Parole') },
  { value: 'lines', label: t('Righe') },
];
const OPTS_CHAR_DIRECTION = [
  { value: 'forward', label: t('Avanti') },
  { value: 'backward', label: t('Indietro') },
  { value: 'random', label: t('Casuale') },
  { value: 'middletoedge', label: t('Centro → Bordi') },
  { value: 'edgetomiddle', label: t('Bordi → Centro') },
];
const OPTS_HOVER_CURSOR = [
  { value: '', label: t('Nessun cambio') },
  { value: 'pointer', label: t('Manina') },
  { value: 'default', label: t('Freccia') },
  { value: 'grab', label: t('Mano aperta') },
];
const OPTS_MASK_PRESET = [
  { value: 'custom', label: t('Personalizzato') },
  { value: 'revealRight', label: t('Si scopre da destra') },
  { value: 'revealLeft', label: t('Si scopre da sinistra') },
  { value: 'revealDown', label: t('Si scopre dall’alto') },
  { value: 'revealUp', label: t('Si scopre dal basso') },
  { value: 'curtainH', label: t('Sipario orizzontale') },
  { value: 'curtainV', label: t('Sipario verticale') },
];
const OPTS_KENBURNS_TYPE = [
  { value: 'zoomIn', label: t('Si avvicina') },
  { value: 'zoomOut', label: t('Si allontana') },
  { value: 'panLeft', label: t('Scorre a sinistra') },
  { value: 'panRight', label: t('Scorre a destra') },
  { value: 'panUp', label: t('Scorre in alto') },
  { value: 'panDown', label: t('Scorre in basso') },
];
const OPTS_MOTION_PRESET = [
  { value: 'circle', label: t('Cerchio') },
  { value: 'figure8', label: t('Otto') },
  { value: 'wave', label: t('Onda') },
  { value: 'zigzag', label: t('Zigzag') },
  { value: 'arc', label: t('Arco') },
  { value: 'custom', label: t('SVG personalizzato') },
];
const OPTS_PARALLAX_TYPE = [
  { value: 'mouse', label: t('Mouse') },
  { value: 'scroll', label: t('Scorrimento') },
  { value: 'both', label: t('Mouse e scorrimento') },
];

// Font families
const fontsSans = ['Inter', 'Roboto', 'Open Sans', 'Lato', 'Montserrat', 'Poppins', 'Nunito', 'Raleway', 'Source Sans 3', 'Work Sans', 'DM Sans', 'Manrope'];
const fontsSerif = ['Playfair Display', 'Merriweather', 'Lora', 'PT Serif', 'Libre Baskerville', 'Cormorant Garamond', 'Crimson Text', 'Noto Serif'];
const fontsMono = ['JetBrains Mono', 'Fira Code', 'Source Code Pro', 'IBM Plex Mono', 'Space Mono'];
const fontsDisplay = ['Bebas Neue', 'Oswald', 'Anton', 'Permanent Marker', 'Abril Fatface', 'Righteous', 'Pacifico', 'Dancing Script', 'Lobster', 'Satisfy'];
const fontGroupOpts = [
  { value: '', label: t('Predefinito') },
  { group: 'Sans-serif', options: fontsSans.map(f => ({ value: f, label: f })) },
  { group: 'Serif', options: fontsSerif.map(f => ({ value: f, label: f })) },
  { group: 'Mono', options: fontsMono.map(f => ({ value: f, label: f })) },
  { group: 'Display', options: fontsDisplay.map(f => ({ value: f, label: f })) },
];

function toggleSfx(enabled) {
  if (enabled) {
    up('sfx', { effect: 'blockRight', color: '#ffffff', duration: 800 });
  } else {
    up('sfx', null);
  }
}

function upSfx(key, val) {
  if (!props.layer?.sfx) return;
  up('sfx', { ...props.layer.sfx, [key]: val });
}

// Box shadow helpers
function toggleBoxShadow(enabled) {
  up('boxShadow', enabled ? { x: 0, y: 4, blur: 10, spread: 0, color: 'rgba(0,0,0,0.3)' } : null);
}
function upBoxShadow(key, val) {
  if (!props.layer?.boxShadow) return;
  up('boxShadow', { ...props.layer.boxShadow, [key]: val });
}

// Text shadow helpers
function toggleTextShadow(enabled) {
  up('textShadow', enabled ? { x: 2, y: 2, blur: 4, color: '#000000' } : null);
}
function upTextShadow(key, val) {
  if (!props.layer?.textShadow) return;
  up('textShadow', { ...props.layer.textShadow, [key]: val });
}

// Shape gradient helpers
function toggleShapeGradient(enabled) {
  up('shapeGradient', enabled ? { from: '#3b82f6', to: '#8b5cf6', angle: 180 } : null);
}
function upShapeGrad(key, val) {
  if (!props.layer?.shapeGradient) return;
  up('shapeGradient', { ...props.layer.shapeGradient, [key]: val });
}

// Mask (clip-path) helpers
function toggleMask(enabled) {
  up('mask', enabled ? { preset: 'custom', top: 0, right: 0, bottom: 0, left: 0 } : null);
}
function upMask(key, val) {
  if (!props.layer?.mask) return;
  up('mask', { ...props.layer.mask, [key]: val, preset: 'custom' });
}
function applyMaskPreset(preset) {
  const presets = {
    custom:     { top: 0, right: 0, bottom: 0, left: 0 },
    revealRight:{ top: 0, right: 100, bottom: 0, left: 0 },
    revealLeft: { top: 0, right: 0, bottom: 0, left: 100 },
    revealDown: { top: 100, right: 0, bottom: 0, left: 0 },
    revealUp:   { top: 0, right: 0, bottom: 100, left: 0 },
    curtainH:   { top: 0, right: 50, bottom: 0, left: 50 },
    curtainV:   { top: 50, right: 0, bottom: 50, left: 0 },
  };
  up('mask', { preset, ...(presets[preset] || presets.custom) });
}

// Ken Burns per layer helpers
function toggleKenBurns(enabled) {
  up('kenBurns', enabled ? { type: 'zoomIn', intensity: 20, duration: 5000 } : null);
}
function upKenBurns(key, val) {
  if (!props.layer?.kenBurns) return;
  up('kenBurns', { ...props.layer.kenBurns, [key]: val });
}

// Motion Path helpers
const MOTION_PRESETS = {
  circle:  'M50,0 A50,50 0 1,1 50,100 A50,50 0 1,1 50,0 Z',
  figure8: 'M50,50 C75,0 100,0 100,50 C100,100 75,100 50,50 C25,0 0,0 0,50 C0,100 25,100 50,50 Z',
  wave:    'M0,50 Q25,0 50,50 Q75,100 100,50',
  zigzag:  'M0,50 L25,10 L50,90 L75,10 L100,50',
  arc:     'M0,100 Q50,-20 100,100',
};
function toggleMotionPath(enabled) {
  up('motionPath', enabled ? { preset: 'circle', path: MOTION_PRESETS.circle, duration: 3000, autoRotate: false, loop: true, easing: 'linear' } : null);
}
function applyMotionPreset(preset) {
  if (!props.layer?.motionPath) return;
  const path = MOTION_PRESETS[preset] || props.layer.motionPath.path || '';
  up('motionPath', { ...props.layer.motionPath, preset, path });
}
function upMotionPath(key, val) {
  if (!props.layer?.motionPath) return;
  up('motionPath', { ...props.layer.motionPath, [key]: val });
}

// Movimento con lo scorrimento (scrollMove): due stati, o la prima e l'ultima delle tappe se ci sono
// Andamento del movimento con lo scorrimento (scrollMove.andamento): morbido = di serie (nessuna chiave salvata)
const OPTS_ANDAMENTO = [
  { value: 'morbido', label: t('Morbido fra le tappe') },
  { value: 'costante', label: t('Costante (zoom uniforme)') },
];
const SCROLL_MOVE_CAMPI = [
  { k: 'x', l: 'Sposta in orizzontale', d: 0, min: -2000, max: 2000, step: 10, u: 'px' },
  { k: 'y', l: 'Sposta in verticale', d: 0, min: -2000, max: 2000, step: 10, u: 'px' },
  { k: 's', l: 'Scala', d: 1, min: 0, max: 6, step: 0.05, u: '' },
  { k: 'sx', l: 'Allunga in orizzontale', d: 1, min: 0, max: 6, step: 0.05, u: '' },
  { k: 'sy', l: 'Allunga in verticale', d: 1, min: 0, max: 6, step: 0.05, u: '' },
  { k: 'r', l: 'Rotazione', d: 0, min: -720, max: 720, step: 1, u: '°' },
  { k: 'rx', l: 'Rotazione 3D (X)', d: 0, min: -180, max: 180, step: 1, u: '°' },
  { k: 'ry', l: 'Rotazione 3D (Y)', d: 0, min: -180, max: 180, step: 1, u: '°' },
  { k: 'o', l: 'Opacità', d: 100, min: 0, max: 100, step: 1, u: '%' },
  // la maschera: quanto del livello resta coperto da ogni lato (il livello si scopre o si copre scorrendo)
  { k: 'mt', l: 'Maschera dall’alto', d: 0, min: 0, max: 100, step: 1, u: '%' },
  { k: 'mr', l: 'Maschera da destra', d: 0, min: 0, max: 100, step: 1, u: '%' },
  { k: 'mb', l: 'Maschera dal basso', d: 0, min: 0, max: 100, step: 1, u: '%' },
  { k: 'ml', l: 'Maschera da sinistra', d: 0, min: 0, max: 100, step: 1, u: '%' },
  // gli assi del carattere variabile (livelli di testo): larghezza e peso veri del disegno, non la scritta deformata
  { k: 'wdth', testo: true, l: 'Larghezza del carattere', d: 100, min: 25, max: 200, step: 1, u: '' },
  { k: 'wght', testo: true, l: 'Peso del carattere', d: 400, min: 100, max: 1000, step: 10, u: '' },
];
function toggleScrollMove(enabled) {
  up('scrollMove', enabled ? { inizio: 0, fine: 100, da: {}, a: { y: -120, o: 0 } } : null);
}
function upScrollMove(key, val) {
  if (!props.layer?.scrollMove) return;
  up('scrollMove', { ...props.layer.scrollMove, [key]: val });
}
function smVal(stato, k, d) {
  const sm = props.layer?.scrollMove || {};
  const tappe = sm.tappe || [];
  const v = tappe.length > 1 ? (stato === 'da' ? tappe[0] : tappe[tappe.length - 1]) : (sm[stato] || {});
  return v[k] ?? d;
}
function upSmVal(stato, k, val) {
  const sm = props.layer?.scrollMove;
  if (!sm) return;
  const tappe = sm.tappe || [];
  if (tappe.length > 1) {
    const i = stato === 'da' ? 0 : tappe.length - 1;
    const nuove = tappe.map((t, j) => (j === i ? { ...t, [k]: val } : t));
    up('scrollMove', { ...sm, tappe: nuove });
  } else {
    up('scrollMove', { ...sm, [stato]: { ...(sm[stato] || {}), [k]: val } });
  }
}

// ── Le tappe del percorso (1.4.1352) ──
// Si leggono sempre come tappe ([{ p, v }]); si scrivono in scrollMove.tappe (il PHP le preferisce a da/a, che restano
// valide per chi non le tocca): i dati salvati dei temi non cambiano finché non si modifica il livello.
const tappaScelta = ref(0);
const tappeSM = computed(() => tappeDi(props.layer?.scrollMove));
const tappaCorrente = computed(() => tappeSM.value[Math.min(tappaScelta.value, tappeSM.value.length - 1)] || null);
const perTesto = (c) => !c.testo || props.layer?.type === 'text' || props.layer?.type === 'button';
const campiTappa = computed(() => {
  const usate = chiaviUsate(props.layer?.scrollMove);
  return SCROLL_MOVE_CAMPI.filter((c) => usate.includes(c.k) && perTesto(c));
});
const campiAggiungibili = computed(() => {
  const usate = chiaviUsate(props.layer?.scrollMove);
  return SCROLL_MOVE_CAMPI.filter((c) => !usate.includes(c.k) && perTesto(c));
});
const opzioniAggiungi = computed(() => [
  { value: '', label: t('+ Muovi anche…') },
  ...campiAggiungibili.value.map((c) => ({ value: c.k, label: t(c.l) })),
]);
watch(() => props.layer?.id, () => { tappaScelta.value = 0; });

function fmtP(p) {
  return Math.round(p * 10) / 10;
}
const BREVI = {
  x: ['oriz.', 'px'], y: ['vert.', 'px'], s: ['scala', ''], sx: ['largh.', '×'], sy: ['alt.', '×'], r: ['rot.', '°'], rx: ['rot. X', '°'], ry: ['rot. Y', '°'],
  o: ['opac.', '%'], mt: ['masch. ↓', '%'], mr: ['masch. ←', '%'], mb: ['masch. ↑', '%'], ml: ['masch. →', '%'], wdth: ['largh. car.', ''], wght: ['peso', ''],
};
function riassuntoTappa(tp) {
  const parti = SM_CHIAVI.filter((k) => tp.v[k] !== undefined).map((k) => t(BREVI[k][0]) + ' ' + (Math.round(tp.v[k] * 100) / 100) + BREVI[k][1]);
  return parti.length ? parti.join(' · ') : t('com’è sulla tela');
}
function scriviTappe(nuove) {
  const sm = props.layer?.scrollMove;
  if (!sm) return;
  const pulite = [...nuove].sort((a, b) => a.p - b.p).map((x) => ({ p: Math.round(x.p * 100) / 100, ...x.v }));
  const out = { ...sm, tappe: pulite };
  delete out.da;
  delete out.a;
  up('scrollMove', out);
}
function scegliTappa(i) {
  tappaScelta.value = i;
  const tp = tappeSM.value[i];
  // la tela va a quel punto dello scorrimento: si vede com'è il livello in quella tappa
  if (tp) emit('set-scroll-preview', Math.round(dellaScena(props.layer.scrollMove, tp.p) * 10) / 10);
}
function upTappa(k, val) {
  const i = Math.min(tappaScelta.value, tappeSM.value.length - 1);
  const nuove = tappeSM.value.map((x, j) => (j === i ? (k === 'p' ? { p: Math.max(0, Math.min(100, +val || 0)), v: x.v } : { p: x.p, v: { ...x.v, [k]: val } }) : x));
  if (k === 'p') {
    // dopo lo spostamento la tappa resta scelta anche se cambia posto nell'ordine
    const presa = nuove[i];
    scriviTappe(nuove);
    const ord = [...nuove].sort((a, b) => a.p - b.p);
    tappaScelta.value = ord.indexOf(presa);
  } else {
    scriviTappe(nuove);
  }
}
function aggiungiTappa() {
  const sm = props.layer?.scrollMove;
  const t2 = tappeSM.value;
  if (!sm || !t2.length) return;
  // al punto dell'anteprima, se è accesa; se no a metà fra la tappa scelta e la successiva (o la precedente)
  let p;
  if (props.scrollPreview != null) p = delLivello(sm, props.scrollPreview);
  else {
    const i = Math.min(tappaScelta.value, t2.length - 1);
    const b = t2[i + 1] || t2[i - 1] || { p: 100 };
    p = (t2[i].p + b.p) / 2;
  }
  p = Math.round(p * 10) / 10;
  const c = sm.andamento === 'costante';
  const v = {};
  for (const k of chiaviUsate(sm)) v[k] = Math.round(valoreSM(t2, p, k, c) * 100) / 100;
  const nuova = { p, v };
  const nuove = [...t2, nuova];
  scriviTappe(nuove);
  tappaScelta.value = [...nuove].sort((a, b) => a.p - b.p).indexOf(nuova);
}
function eliminaTappa() {
  const t2 = tappeSM.value;
  if (t2.length <= 2) return;
  const i = Math.min(tappaScelta.value, t2.length - 1);
  scriviTappe(t2.filter((_, j) => j !== i));
  tappaScelta.value = Math.max(0, i - 1);
}
function aggiungiChiave(k) {
  if (!k) return;
  const c = SCROLL_MOVE_CAMPI.find((x) => x.k === k);
  // il valore di serie su tutte le tappe: non cambia niente finché non lo si modifica in una tappa
  scriviTappe(tappeSM.value.map((x) => ({ p: x.p, v: { ...x.v, [k]: c ? c.d : 0 } })));
}
function togliChiave(k) {
  scriviTappe(tappeSM.value.map((x) => {
    const v = { ...x.v };
    delete v[k];
    return { p: x.p, v };
  }));
}

// Parallax layer helpers
function toggleParallax(enabled) {
  up('parallax', enabled ? { type: 'mouse', depth: 5, tilt3d: false, scrollSpeed: 0.5 } : null);
}
function upParallax(key, val) {
  if (!props.layer?.parallax) return;
  up('parallax', { ...props.layer.parallax, [key]: val });
}
</script>

<style scoped>
/* Le tappe del percorso (1.4.1352) */
.mps-tappe { display: flex; flex-direction: column; gap: 6px; padding: 8px; border: 1px solid #374151; border-radius: 6px; background: #111827; }
.mps-tappe-barra { position: relative; height: 18px; margin: 2px 6px; }
.mps-tappe-barra::before { content: ''; position: absolute; left: 0; right: 0; top: 8px; height: 2px; background: #374151; border-radius: 1px; }
.mps-tappe-punto { position: absolute; top: 3px; width: 12px; height: 12px; margin-left: -6px; border-radius: 50%; background: #60a5fa; border: 2px solid #111827; cursor: pointer; }
.mps-tappe-punto.is-on { background: #fbbf24; box-shadow: 0 0 0 2px #fbbf24; }
.mps-tappe-ora { position: absolute; top: 0; width: 2px; height: 18px; margin-left: -1px; background: #f87171; pointer-events: none; }
.mps-tappa { display: flex; align-items: baseline; gap: 8px; width: 100%; text-align: left; padding: 3px 6px; border-radius: 4px; background: transparent; border: 1px solid transparent; color: #d1d5db; cursor: pointer; font-size: 10px; }
.mps-tappa:hover { background: #1f2937; }
.mps-tappa.is-on { background: #1e293b; border-color: #fbbf24; }
.mps-tappa:focus-visible, .mps-tappe-btn:focus-visible, .mps-tappe-x:focus-visible { outline: 2px solid #93c5fd; outline-offset: 1px; }
.mps-tappa-p { font-family: ui-monospace, monospace; font-weight: 700; color: #93c5fd; min-width: 40px; }
.mps-tappa-v { color: #9ca3af; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.mps-tappe-btn { font-size: 10px; font-weight: 600; padding: 2px 8px; border-radius: 4px; background: #374151; color: #e5e7eb; border: 1px solid #4b5563; cursor: pointer; }
.mps-tappe-btn:hover:not(:disabled) { background: #4b5563; }
.mps-tappe-btn:disabled { opacity: .4; cursor: default; }
.mps-tappe-x { background: none; border: 0; color: #6b7280; cursor: pointer; font-size: 12px; line-height: 1; padding: 0 2px; }
.mps-tappe-x:hover { color: #f87171; }
.mps-label {
  display: block;
  font-size: 10px;
  color: #9ca3af;
  margin-bottom: 2px;
}
.mps-val {
  font-size: 11px;
  color: #9ca3af;
  width: 40px;
  text-align: right;
  flex-shrink: 0;
}
.mps-input {
  width: 100%;
  background: #ffffff;
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
  padding: 4px 8px;
  font-size: 0.75rem;
  color: #111827;
}
.mps-icon-preview {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 20px;
  height: 20px;
  flex-shrink: 0;
}
.mps-icon-preview :deep(svg) {
  width: 18px;
  height: 18px;
  color: #d1d5db;
  stroke: currentColor;
}
.mps-icon-preview :deep(svg:not([fill="none"])) {
  fill: currentColor;
}
.mps-icon-preview :deep(svg [fill="none"]) {
  fill: none;
}
</style>
