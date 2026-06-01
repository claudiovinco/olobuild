<template>
  <div class="mb-space-y-3">
    <!-- Type selector -->
    <div>
      <label class="mb-block mb-text-xs mb-font-semibold mb-text-gray-300 mb-mb-2">{{ t('Tipo di sfondo') }}</label>
      <div class="mb-flex mb-gap-1 mb-bg-gray-700 mb-rounded-lg mb-p-0.5">
        <button
          v-for="bgType in types"
          :key="bgType.value"
          @click="updateField('type', bgType.value)"
          :class="[
            'mb-flex-1 mb-py-1.5 mb-text-[10px] mb-font-medium mb-rounded-md mb-transition-colors',
            bg.type === bgType.value
              ? 'mb-bg-primary-600 mb-text-white'
              : 'mb-text-gray-400 hover:mb-text-gray-300'
          ]"
        >
          {{ t(bgType.label) }}
        </button>
      </div>
    </div>

    <!-- Solid color -->
    <!-- v1.0.74 — rimosso lo slider "Opacità sfondo": era ridondante con l'Alfa
         dentro FieldColor (entrambi producono background-color rgba). Il
         default color_opacity:100 resta nei dati e il backend lo applica come
         no-op; i template legacy con color_opacity != 100 continuano a
         funzionare ma non sono più modificabili dall'inspector — l'utente
         migra usando l'Alfa del color picker. -->
    <div v-if="bg.type === 'solid'" class="mb-space-y-2">
      <label class="mb-block mb-text-[10px] mb-text-gray-400">{{ t('Colore') }}</label>
      <FieldColor
        :modelValue="bg.color || '#ffffff'"
        @update:modelValue="updateField('color', $event)"
      />
      <!-- Preview swatch -->
      <div
        class="mb-h-6 mb-rounded-md mb-border mb-border-gray-600"
        :style="{ background: solidPreview }"
      ></div>
    </div>

    <!-- Gradient (multi-stop) -->
    <div v-if="bg.type === 'gradient'" class="mb-space-y-3">
      <!-- FieldGradient include già una preview interna; nessun bisogno di
           duplicarla qui (creava le due barre "morte" non cliccabili). -->
      <FieldGradient
        :modelValue="gradientModel"
        @update:modelValue="onGradientUpdate"
      />
    </div>

    <!-- Image -->
    <div v-if="bg.type === 'image'" class="mb-space-y-3">
      <!-- Thumbnail -->
      <div v-if="bg.image_url" class="mb-relative mb-group">
        <img
          :src="bg.image_url"
          :alt="t('Background')"
          class="mb-w-full mb-h-20 mb-object-cover mb-rounded-md mb-border mb-border-gray-600"
        />
        <button
          @click="updateField('image_url', '')"
          class="mb-absolute mb-top-1 mb-right-1 mb-bg-red-600 mb-text-white mb-rounded-full mb-w-5 mb-h-5 mb-text-xs mb-flex mb-items-center mb-justify-center mb-opacity-0 group-hover:mb-opacity-100 mb-transition-opacity"
          :title="t('Rimuovi immagine')"
        >{{ t('&times;') }}</button>
      </div>
      <button
        @click="pickBgImage"
        class="mb-w-full mb-py-1.5 mb-px-3 mb-bg-gray-700 mb-border mb-border-gray-600 mb-rounded-md mb-text-xs mb-text-gray-300 hover:mb-bg-gray-600 mb-transition-colors"
      >
        {{ bg.image_url ? 'Cambia immagine' : 'Seleziona immagine' }}
      </button>

      <!-- Size -->
      <div>
        <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-1">{{ t('Dimensione') }}</label>
        <select
          :value="bg.image_size || 'cover'"
          @change="updateField('image_size', $event.target.value)"
          class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1 mb-text-sm mb-text-gray-900"
        >
          <option value="cover">{{ t('Cover') }}</option>
          <option value="contain">{{ t('Contain') }}</option>
          <option value="auto">{{ t('Auto') }}</option>
        </select>
      </div>

      <!-- Position -->
      <div>
        <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-1">{{ t('Posizione') }}</label>
        <select
          :value="bg.image_position || 'center center'"
          @change="updateField('image_position', $event.target.value)"
          class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1 mb-text-sm mb-text-gray-900"
        >
          <option value="center center">{{ t('Centro') }}</option>
          <option value="top center">{{ t('Alto') }}</option>
          <option value="bottom center">{{ t('Basso') }}</option>
          <option value="left center">{{ t('Sinistra') }}</option>
          <option value="right center">{{ t('Destra') }}</option>
          <option value="top left">{{ t('Alto sinistra') }}</option>
          <option value="top right">{{ t('Alto destra') }}</option>
          <option value="bottom left">{{ t('Basso sinistra') }}</option>
          <option value="bottom right">{{ t('Basso destra') }}</option>
        </select>
      </div>

      <!-- Parallax -->
      <div v-if="showParallax">
        <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
          <button
            @click="toggleParallax"
            :class="[
              'mb-relative mb-w-10 mb-h-5 mb-rounded-full mb-transition-colors mb-shrink-0',
              isParallaxEnabled ? 'mb-bg-primary-600' : 'mb-bg-gray-600'
            ]"
          >
            <span
              :class="[
                'mb-absolute mb-top-0.5 mb-w-4 mb-h-4 mb-rounded-full mb-bg-white mb-transition-transform',
                isParallaxEnabled ? 'mb-left-5' : 'mb-left-0.5'
              ]"
            ></span>
          </button>
          <span class="mb-text-xs mb-text-gray-300">{{ t('Parallasse') }}</span>
        </label>
        <div v-if="isParallaxEnabled" class="mb-mt-2">
          <ParallaxEditor
            :modelValue="bgParallaxData"
            :properties="bgParallaxProperties"
            @update:modelValue="updateParallaxData"
          />
        </div>
      </div>
    </div>

    <!-- Video -->
    <div v-if="bg.type === 'video'" class="mb-space-y-3">
      <!-- Video preview -->
      <div v-if="bg.video_url" class="mb-relative mb-group">
        <div class="mb-w-full mb-h-20 mb-rounded-md mb-border mb-border-gray-600 mb-bg-gray-800 mb-flex mb-items-center mb-justify-center mb-overflow-hidden">
          <img v-if="bg.video_poster" :src="bg.video_poster" :alt="t('Video poster')" class="mb-w-full mb-h-20 mb-object-cover" />
          <span v-else class="mb-text-2xl">{{ t('&#x1F3AC;') }}</span>
        </div>
        <button
          @click="removeVideo"
          class="mb-absolute mb-top-1 mb-right-1 mb-bg-red-600 mb-text-white mb-rounded-full mb-w-5 mb-h-5 mb-text-xs mb-flex mb-items-center mb-justify-center mb-opacity-0 group-hover:mb-opacity-100 mb-transition-opacity"
          :title="t('Rimuovi video')"
        >{{ t('&times;') }}</button>
      </div>
      <button
        @click="pickBgVideo"
        class="mb-w-full mb-py-1.5 mb-px-3 mb-bg-gray-700 mb-border mb-border-gray-600 mb-rounded-md mb-text-xs mb-text-gray-300 hover:mb-bg-gray-600 mb-transition-colors"
      >
        {{ bg.video_url ? 'Cambia video' : 'Seleziona video' }}
      </button>

      <!-- Poster image (optional) -->
      <button
        @click="pickBgPoster"
        class="mb-w-full mb-py-1.5 mb-px-3 mb-bg-gray-700 mb-border mb-border-gray-600 mb-rounded-md mb-text-xs mb-text-gray-300 hover:mb-bg-gray-600 mb-transition-colors"
      >
        {{ bg.video_poster ? 'Cambia poster' : 'Seleziona poster' }}
      </button>

      <!-- Fit mode -->
      <div>
        <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-1">{{ t('Adattamento') }}</label>
        <select
          :value="bg.video_fit || 'cover'"
          @change="updateField('video_fit', $event.target.value)"
          class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1 mb-text-sm mb-text-gray-900"
        >
          <option value="cover">{{ t('Cover') }}</option>
          <option value="contain">{{ t('Contain') }}</option>
          <option value="fill">{{ t('Riempi') }}</option>
          <option value="none">{{ t('Nessuno (dimensione originale)') }}</option>
        </select>
      </div>

      <!-- Position -->
      <div>
        <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-1">{{ t('Posizione') }}</label>
        <select
          :value="bg.image_position || 'center center'"
          @change="updateField('image_position', $event.target.value)"
          class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1 mb-text-sm mb-text-gray-900"
        >
          <option value="center center">{{ t('Centro') }}</option>
          <option value="top center">{{ t('Alto') }}</option>
          <option value="bottom center">{{ t('Basso') }}</option>
          <option value="left center">{{ t('Sinistra') }}</option>
          <option value="right center">{{ t('Destra') }}</option>
          <option value="top left">{{ t('Alto sinistra') }}</option>
          <option value="top right">{{ t('Alto destra') }}</option>
          <option value="bottom left">{{ t('Basso sinistra') }}</option>
          <option value="bottom right">{{ t('Basso destra') }}</option>
        </select>
      </div>

      <!-- Cover Height -->
      <div>
        <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-1">{{ t('Altezza cover (px)') }}</label>
        <div class="mb-flex mb-items-center mb-gap-2">
          <input
            type="range"
            :value="bg.cover_height || 0"
            @input="updateField('cover_height', parseInt($event.target.value))"
            min="0" max="1200" step="10"
            class="mb-flex-1"
          />
          <span class="mb-text-xs mb-text-gray-400 mb-w-10 mb-text-right">{{ bg.cover_height || 'auto' }}</span>
        </div>
        <p class="mb-text-[9px] mb-text-gray-500 mb-mt-0.5">{{ t('0 = auto (altezza contenuto)') }}</p>
      </div>

      <!-- Video Scale (zoom) -->
      <div>
        <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-1">{{ t('Scala video (%)') }}</label>
        <div class="mb-flex mb-items-center mb-gap-2">
          <input
            type="range"
            :value="bg.video_scale || 100"
            @input="updateField('video_scale', parseInt($event.target.value))"
            min="100" max="300" step="10"
            class="mb-flex-1"
          />
          <span class="mb-text-xs mb-text-gray-400 mb-w-10 mb-text-right">{{ bg.video_scale || 100 }}%</span>
        </div>
        <p class="mb-text-[9px] mb-text-gray-500 mb-mt-0.5">{{ t('100% = normale, 200% = doppio') }}</p>
      </div>
    </div>

    <!-- Gallery slideshow -->
    <div v-if="bg.type === 'gallery'" class="mb-space-y-3">
      <!-- Image picker -->
      <div>
        <div class="mb-flex mb-items-center mb-justify-between mb-mb-2">
          <span class="mb-text-[10px] mb-text-gray-400">{{ (bg.gallery_images || []).length }} {{ t('immagini selezionate') }}</span>
          <button
            v-if="(bg.gallery_images || []).length"
            @click="updateField('gallery_images', [])"
            class="mb-text-gray-500 hover:mb-text-red-400 mb-text-sm"
          >&#128465;</button>
        </div>
        <div v-if="(bg.gallery_images || []).length" class="mb-flex mb-flex-wrap mb-gap-1 mb-mb-2">
          <div v-for="(img, idx) in bg.gallery_images" :key="idx" class="mb-relative mb-group">
            <img :src="img.url" :alt="img.alt" class="mb-w-16 mb-h-12 mb-object-cover mb-rounded mb-border mb-border-gray-600" />
            <button
              @click="removeGalleryImage(idx)"
              class="mb-absolute mb-top-0 mb-right-0 mb-bg-red-600 mb-text-white mb-rounded-full mb-w-4 mb-h-4 mb-text-[9px] mb-flex mb-items-center mb-justify-center mb-opacity-0 group-hover:mb-opacity-100 mb-transition-opacity mb-leading-none"
            >{{ t('&times;') }}</button>
          </div>
        </div>
        <button
          @click="pickGalleryImages"
          class="mb-w-full mb-py-1.5 mb-px-3 mb-bg-gray-700 mb-border mb-border-gray-600 mb-rounded-md mb-text-xs mb-text-gray-300 hover:mb-bg-gray-600"
        >{{ (bg.gallery_images || []).length ? t('Cambia immagini') : t('Seleziona immagini') }}</button>
      </div>

      <!-- Loop -->
      <div class="mb-flex mb-items-center mb-justify-between">
        <span class="mb-text-[10px] mb-text-gray-400">{{ t('Ciclo infinito') }}</span>
        <button
          @click="updateField('gallery_loop', !(bg.gallery_loop !== false))"
          :class="['mb-relative mb-w-10 mb-h-5 mb-rounded-full mb-transition-colors mb-shrink-0', (bg.gallery_loop !== false) ? 'mb-bg-primary-600' : 'mb-bg-gray-600']"
        ><span :class="['mb-absolute mb-top-0.5 mb-w-4 mb-h-4 mb-rounded-full mb-bg-white mb-transition-transform', (bg.gallery_loop !== false) ? 'mb-left-5' : 'mb-left-0.5']"></span></button>
      </div>

      <!-- Duration -->
      <div class="mb-flex mb-items-center mb-justify-between">
        <span class="mb-text-[10px] mb-text-gray-400">{{ t('Durata (ms)') }}</span>
        <input type="number" :value="bg.gallery_duration || 5000" @change="updateField('gallery_duration', parseInt($event.target.value))" min="1000" max="30000" step="500"
          class="mb-w-20 mb-bg-white mb-border mb-border-gray-300 mb-rounded mb-px-2 mb-py-1 mb-text-xs mb-text-gray-900 mb-text-right" />
      </div>

      <!-- Transition type -->
      <div class="mb-flex mb-items-center mb-justify-between">
        <span class="mb-text-[10px] mb-text-gray-400">{{ t('Transizione') }}</span>
        <select :value="bg.gallery_transition || 'fade'" @change="updateField('gallery_transition', $event.target.value)"
          class="mb-w-28 mb-bg-white mb-border mb-border-gray-300 mb-rounded mb-px-2 mb-py-1 mb-text-xs mb-text-gray-900">
          <option value="fade">{{ t('Fade') }}</option>
          <option value="crossfade">{{ t('Crossfade') }}</option>
          <option value="slide">{{ t('Slide') }}</option>
          <option value="slide-up">{{ t('Slide Up') }}</option>
          <option value="zoom">{{ t('Zoom') }}</option>
          <option value="blur">{{ t('Blur') }}</option>
          <option value="flip">{{ t('Flip') }}</option>
          <option value="none">{{ t('Nessuna') }}</option>
        </select>
      </div>

      <!-- Transition duration -->
      <div class="mb-flex mb-items-center mb-justify-between">
        <span class="mb-text-[10px] mb-text-gray-400">{{ t('Durata della transizione (ms)') }}</span>
        <input type="number" :value="bg.gallery_transition_ms || 500" @change="updateField('gallery_transition_ms', parseInt($event.target.value))" min="100" max="3000" step="100"
          class="mb-w-20 mb-bg-white mb-border mb-border-gray-300 mb-rounded mb-px-2 mb-py-1 mb-text-xs mb-text-gray-900 mb-text-right" />
      </div>

      <!-- Size -->
      <div class="mb-flex mb-items-center mb-justify-between">
        <span class="mb-text-[10px] mb-text-gray-400">{{ t('Dimensione sfondo') }}</span>
        <select :value="bg.image_size || 'cover'" @change="updateField('image_size', $event.target.value)"
          class="mb-w-28 mb-bg-white mb-border mb-border-gray-300 mb-rounded mb-px-2 mb-py-1 mb-text-xs mb-text-gray-900">
          <option value="cover">{{ t('Cover') }}</option>
          <option value="contain">{{ t('Contain') }}</option>
          <option value="auto">{{ t('Auto') }}</option>
        </select>
      </div>

      <!-- Position -->
      <div class="mb-flex mb-items-center mb-justify-between">
        <span class="mb-text-[10px] mb-text-gray-400">{{ t('Posizione sfondo') }}</span>
        <select :value="bg.image_position || 'center center'" @change="updateField('image_position', $event.target.value)"
          class="mb-w-28 mb-bg-white mb-border mb-border-gray-300 mb-rounded mb-px-2 mb-py-1 mb-text-xs mb-text-gray-900">
          <option value="center center">{{ t('Centro') }}</option>
          <option value="top center">{{ t('Alto') }}</option>
          <option value="bottom center">{{ t('Basso') }}</option>
          <option value="left center">{{ t('Sinistra') }}</option>
          <option value="right center">{{ t('Destra') }}</option>
        </select>
      </div>

      <!-- Lazyload -->
      <div class="mb-flex mb-items-center mb-justify-between">
        <span class="mb-text-[10px] mb-text-gray-400">{{ t('Lazyload') }}</span>
        <button
          @click="updateField('gallery_lazyload', !(bg.gallery_lazyload !== false))"
          :class="['mb-relative mb-w-10 mb-h-5 mb-rounded-full mb-transition-colors mb-shrink-0', (bg.gallery_lazyload !== false) ? 'mb-bg-primary-600' : 'mb-bg-gray-600']"
        ><span :class="['mb-absolute mb-top-0.5 mb-w-4 mb-h-4 mb-rounded-full mb-bg-white mb-transition-transform', (bg.gallery_lazyload !== false) ? 'mb-left-5' : 'mb-left-0.5']"></span></button>
      </div>

      <!-- Ken Burns -->
      <div class="mb-flex mb-items-center mb-justify-between">
        <span class="mb-text-[10px] mb-text-gray-400">{{ t('Effetto Ken Burns') }}</span>
        <button
          @click="updateField('gallery_kenburns', !bg.gallery_kenburns)"
          :class="['mb-relative mb-w-10 mb-h-5 mb-rounded-full mb-transition-colors mb-shrink-0', bg.gallery_kenburns ? 'mb-bg-primary-600' : 'mb-bg-gray-600']"
        ><span :class="['mb-absolute mb-top-0.5 mb-w-4 mb-h-4 mb-rounded-full mb-bg-white mb-transition-transform', bg.gallery_kenburns ? 'mb-left-5' : 'mb-left-0.5']"></span></button>
      </div>

      <!-- Ken Burns direction -->
      <div v-if="bg.gallery_kenburns" class="mb-flex mb-items-center mb-justify-between">
        <span class="mb-text-[10px] mb-text-gray-400">{{ t('Direzione') }}</span>
        <select :value="bg.gallery_kenburns_dir || 'in'" @change="updateField('gallery_kenburns_dir', $event.target.value)"
          class="mb-w-28 mb-bg-white mb-border mb-border-gray-300 mb-rounded mb-px-2 mb-py-1 mb-text-xs mb-text-gray-900">
          <option value="in">{{ t('Dentro') }} (Zoom in)</option>
          <option value="out">{{ t('Fuori') }} (Zoom out)</option>
          <option value="alternate">{{ t('Alternato') }}</option>
        </select>
      </div>
    </div>

    <!-- Mesh / Aurora -->
    <div v-if="bg.type === 'mesh'" class="mb-space-y-3">
      <p class="mb-text-[10px] mb-text-gray-400">{{ t('Sfondo aurora: 3 blob sfumati sopra un colore base. Usa i ruoli colore del tema.') }}</p>
      <div class="mb-grid mb-grid-cols-2 mb-gap-2">
        <div>
          <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-1">{{ t('Colore 1') }}</label>
          <FieldColor :modelValue="bg.mesh_c1 || 'var(--olo-color-primary)'" @update:modelValue="updateField('mesh_c1', $event)" />
        </div>
        <div>
          <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-1">{{ t('Colore 2') }}</label>
          <FieldColor :modelValue="bg.mesh_c2 || 'var(--olo-color-secondary)'" @update:modelValue="updateField('mesh_c2', $event)" />
        </div>
        <div>
          <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-1">{{ t('Colore 3') }}</label>
          <FieldColor :modelValue="bg.mesh_c3 || 'var(--olo-color-accent)'" @update:modelValue="updateField('mesh_c3', $event)" />
        </div>
        <div>
          <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-1">{{ t('Colore base') }}</label>
          <FieldColor :modelValue="bg.mesh_base || 'var(--olo-color-background)'" @update:modelValue="updateField('mesh_base', $event)" />
        </div>
      </div>
      <!-- Preview -->
      <div class="mb-h-16 mb-rounded mb-border mb-border-gray-600" :style="meshPreviewStyle"></div>
      <!-- Animate -->
      <div class="mb-flex mb-items-center mb-justify-between">
        <span class="mb-text-[10px] mb-text-gray-400">{{ t('Movimento lento (drift)') }}</span>
        <button
          @click="updateField('mesh_animate', !bg.mesh_animate)"
          :class="['mb-relative mb-w-10 mb-h-5 mb-rounded-full mb-transition-colors mb-shrink-0', bg.mesh_animate ? 'mb-bg-primary-600' : 'mb-bg-gray-600']"
        ><span :class="['mb-absolute mb-top-0.5 mb-w-4 mb-h-4 mb-rounded-full mb-bg-white mb-transition-transform', bg.mesh_animate ? 'mb-left-5' : 'mb-left-0.5']"></span></button>
      </div>
      <div v-if="bg.mesh_animate">
        <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-1">{{ t('Velocità (s)') }} ({{ bg.mesh_speed || 18 }})</label>
        <input type="range" :value="bg.mesh_speed || 18" @input="updateField('mesh_speed', parseInt($event.target.value))" min="4" max="60" step="1" class="mb-w-full" />
      </div>
    </div>

    <!-- Pattern -->
    <div v-if="bg.type === 'pattern'" class="mb-space-y-3">
      <div>
        <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-1">{{ t('Pattern') }}</label>
        <select
          :value="bg.pattern_type || 'dots'"
          @change="updateField('pattern_type', $event.target.value)"
          class="mb-w-full mb-bg-gray-700 mb-border mb-border-gray-600 mb-rounded mb-px-2 mb-py-1.5 mb-text-xs mb-text-white"
        >
          <optgroup v-for="group in patternGroups" :key="group.label" :label="group.label">
            <option v-for="p in group.items" :key="p.value" :value="p.value">{{ p.label }}</option>
          </optgroup>
        </select>
      </div>
      <!-- Preview -->
      <div class="mb-h-16 mb-rounded mb-border mb-border-gray-600" :style="patternPreviewStyle"></div>
      <div class="mb-grid mb-grid-cols-2 mb-gap-2">
        <div>
          <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-1">{{ t('Colore pattern') }}</label>
          <FieldColor :modelValue="bg.pattern_color || '#000000'" @update:modelValue="updateField('pattern_color', $event)" />
        </div>
        <div>
          <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-1">{{ t('Colore sfondo') }}</label>
          <FieldColor :modelValue="bg.pattern_bg_color || '#ffffff'" @update:modelValue="updateField('pattern_bg_color', $event)" />
        </div>
      </div>
      <div>
        <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-1">{{ t('Dimensione') }} ({{ bg.pattern_size || 20 }}px)</label>
        <input type="range" :value="bg.pattern_size || 20" @input="updateField('pattern_size', parseInt($event.target.value))" min="8" max="100" step="1" class="mb-w-full" />
      </div>
      <div>
        <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-1">{{ t('Opacità') }} ({{ bg.pattern_opacity ?? 50 }}%)</label>
        <input type="range" :value="bg.pattern_opacity ?? 50" @input="updateField('pattern_opacity', parseInt($event.target.value))" min="5" max="100" step="5" class="mb-w-full" />
      </div>
    </div>

    <!-- Glow / Bagliori -->
    <div v-if="bg.type === 'glow'" class="mb-space-y-3">
      <!-- Preset posizione -->
      <div>
        <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-1">{{ t('Disposizione') }}</label>
        <select :value="bg.glow_preset || 'spread'" @change="updateField('glow_preset', $event.target.value)"
          class="mb-w-full mb-bg-gray-700 mb-border mb-border-gray-600 mb-rounded mb-px-2 mb-py-1.5 mb-text-xs mb-text-white">
          <option v-for="p in glowPresets" :key="p.value" :value="p.value">{{ t(p.label) }}</option>
        </select>
      </div>
      <!-- Anteprima -->
      <div class="mb-h-16 mb-rounded mb-border mb-border-gray-600" :style="glowPreviewStyle"></div>
      <div class="mb-grid mb-grid-cols-2 mb-gap-2">
        <div>
          <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-1">{{ t('Colore alone') }}</label>
          <FieldColor :modelValue="bg.glow_color || 'var(--olo-color-primary)'" @update:modelValue="updateField('glow_color', $event)" />
        </div>
        <div>
          <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-1">{{ t('Colore base') }}</label>
          <FieldColor :modelValue="bg.glow_base || '#0b0d12'" @update:modelValue="updateField('glow_base', $event)" />
        </div>
      </div>
      <div>
        <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-1">{{ t('Colore alone 2 (opzionale)') }}</label>
        <FieldColor :modelValue="bg.glow_color2 || ''" @update:modelValue="updateField('glow_color2', $event)" />
      </div>
      <div>
        <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-1">{{ t('Intensità') }} ({{ bg.glow_intensity ?? 62 }}%)</label>
        <input type="range" :value="bg.glow_intensity ?? 62" @input="updateField('glow_intensity', parseInt($event.target.value))" min="10" max="100" step="2" class="mb-w-full" />
      </div>
      <div>
        <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-1">{{ t('Ampiezza') }} ({{ bg.glow_size ?? 78 }}%)</label>
        <input type="range" :value="bg.glow_size ?? 78" @input="updateField('glow_size', parseInt($event.target.value))" min="30" max="120" step="2" class="mb-w-full" />
      </div>
      <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
        <button @click="updateField('glow_grain', !(bg.glow_grain !== false))"
          :class="['mb-relative mb-w-10 mb-h-5 mb-rounded-full mb-shrink-0', (bg.glow_grain !== false) ? 'mb-bg-primary-600' : 'mb-bg-gray-600']">
          <span :class="['mb-absolute mb-top-0.5 mb-w-4 mb-h-4 mb-rounded-full mb-bg-white mb-transition-transform', (bg.glow_grain !== false) ? 'mb-left-5' : 'mb-left-0.5']"></span>
        </button>
        <span class="mb-text-xs mb-text-gray-300">{{ t('Grana film') }}</span>
      </label>
      <!-- Animazione bagliori -->
      <div class="mb-pt-2 mb-border-t mb-border-gray-700">
        <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-1">{{ t('Animazione bagliore') }}</label>
        <select :value="bg.glow_anim || 'none'" @change="updateField('glow_anim', $event.target.value)"
          class="mb-w-full mb-bg-gray-700 mb-border mb-border-gray-600 mb-rounded mb-px-2 mb-py-1.5 mb-text-xs mb-text-white">
          <option value="none">{{ t('Nessuna (statico)') }}</option>
          <option value="pulse">{{ t('Pulsazione (respiro)') }}</option>
          <option value="drift">{{ t('Deriva (movimento lento)') }}</option>
          <option value="wander">{{ t('Vagare (respiro + deriva)') }}</option>
          <option value="flicker">{{ t('Sfarfallio (energia neon)') }}</option>
          <option value="vivo">{{ t('Vivo (respiro + orbita) ✦') }}</option>
          <option value="tempesta">{{ t('Tempesta (sfarfallio + ondeggio) ✦') }}</option>
          <option value="scroll">{{ t('Reattivo allo scroll') }}</option>
        </select>
      </div>
      <div v-if="(bg.glow_anim || 'none') !== 'none' && bg.glow_anim !== 'scroll'">
        <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-1">{{ t('Velocità') }} ({{ bg.glow_anim_speed ?? 6 }})</label>
        <input type="range" :value="bg.glow_anim_speed ?? 6" @input="updateField('glow_anim_speed', parseInt($event.target.value))" min="1" max="10" step="1" class="mb-w-full" />
      </div>
      <div v-if="['pulse','vivo'].includes(bg.glow_anim)">
        <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-1">{{ t('Intensità respiro') }} ({{ bg.glow_anim_intensity ?? 46 }}%)</label>
        <input type="range" :value="bg.glow_anim_intensity ?? 46" @input="updateField('glow_anim_intensity', parseInt($event.target.value))" min="0" max="100" step="2" class="mb-w-full" />
      </div>
    </div>

    <!-- Overlay (for all types except none) -->
    <div v-if="bg.type && bg.type !== 'none'" class="mb-space-y-2 mb-pt-2 mb-border-t mb-border-gray-700">
      <label class="mb-block mb-text-xs mb-font-semibold mb-text-gray-300">{{ t('Sovrapposizione') }}</label>
      <FieldColor
        :modelValue="bg.overlay_color || '#000000'"
        @update:modelValue="updateField('overlay_color', $event)"
      />
      <div class="mb-flex mb-items-center mb-gap-2">
        <span class="mb-text-[10px] mb-text-gray-400 mb-shrink-0">{{ t('Opacità') }}</span>
        <input
          type="range"
          :value="bg.overlay_opacity || 0"
          @input="updateField('overlay_opacity', parseInt($event.target.value))"
          min="0" max="100" step="5"
          class="mb-flex-1"
        />
        <span class="mb-text-xs mb-text-gray-400 mb-w-10 mb-text-right">{{ bg.overlay_opacity || 0 }}%</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';
import { useMediaPicker } from '@/composables/useMediaPicker';
import { patternList, getPatternCSS } from '@/utils/patternCSS';
import { getGlowCSS, glowPresets } from '@/utils/glowCSS';
import ParallaxEditor from './ParallaxEditor.vue';
import FieldGradient from './fields/FieldGradient.vue';
import FieldColor from './fields/FieldColor.vue';

const bgParallaxProperties = [
  { key: 'bgx', label: 'Spostamento X', min: -800, max: 800, step: 10, unit: 'px' },
  { key: 'bgy', label: 'Spostamento Y', min: -800, max: 800, step: 10, unit: 'px' },
  { key: 'scale', label: 'Scala', min: 0.5, max: 2, step: 0.05, unit: '' },
  { key: 'opacity', label: 'Opacità', min: 0, max: 1, step: 0.05, unit: '' },
  { key: 'blur', label: 'Sfocatura', min: 0, max: 20, step: 1, unit: 'px' },
];

const props = defineProps({
  modelValue: { type: Object, default: () => ({}) },
  showParallax: { type: Boolean, default: true },
});

const emit = defineEmits(['update:modelValue']);

const { openSingleImage, openGallery } = useMediaPicker();

const defaultBg = {
  type: 'none',
  color: '#ffffff',
  gradient_angle: 180,
  gradient_from: '#ffffff',
  gradient_to: '#000000',
  image_url: '',
  image_size: 'cover',
  image_position: 'center center',
  video_url: '',
  video_poster: '',
  video_fit: 'cover',
  parallax: false,
  parallax_speed: 0.3,
  parallax_bgy: -200,
  parallax_bgx: 0,
  parallax_opacity: false,
  parallax_opacity_start: 0.3,
  parallax_opacity_end: 1,
  parallax_scale: false,
  parallax_scale_start: 1,
  parallax_scale_end: 1.2,
  parallax_blur: false,
  parallax_blur_start: 5,
  parallax_blur_end: 0,
  parallax_nomobile: true,
  cover_height: 0,
  video_scale: 100,
  mesh_c1: 'var(--olo-color-primary)',
  mesh_c2: 'var(--olo-color-secondary)',
  mesh_c3: 'var(--olo-color-accent)',
  mesh_base: 'var(--olo-color-background)',
  mesh_animate: false,
  mesh_speed: 18,
  glow_base: '#0b0d12',
  glow_color: 'var(--olo-color-primary)',
  glow_color2: '',
  glow_preset: 'spread',
  glow_intensity: 62,
  glow_size: 78,
  glow_grain: true,
  glow_anim: 'none',
  glow_anim_speed: 6,
  glow_anim_intensity: 46,
  overlay_color: '#000000',
  overlay_opacity: 0,
  color_opacity: 100,
  gallery_images: [],
  gallery_loop: true,
  gallery_duration: 5000,
  gallery_transition: 'fade',
  gallery_transition_ms: 500,
  gallery_lazyload: true,
  gallery_kenburns: true,
  gallery_kenburns_dir: 'in',
};

const types = [
  { value: 'none', label: 'Nessuno' },
  { value: 'solid', label: 'Tinta unita' },
  { value: 'gradient', label: 'Gradiente' },
  { value: 'mesh', label: 'Aurora' },
  { value: 'glow', label: 'Bagliori' },
  { value: 'pattern', label: 'Pattern' },
  { value: 'image', label: 'Immagine' },
  { value: 'video', label: 'Video' },
  { value: 'gallery', label: 'Galleria' },
];

// Pattern groups for select optgroup
const patternGroups = [
  { label: 'Linee', items: patternList.filter(p => ['horizontal-lines','vertical-lines','diagonal-lines','diagonal-lines-reverse','crosshatch','diagonal-crosshatch'].includes(p.value)) },
  { label: 'Punti', items: patternList.filter(p => ['dots','dots-large','dots-grid','polka-dots'].includes(p.value)) },
  { label: 'Geometrici', items: patternList.filter(p => ['checkerboard','triangles','diamonds','hexagons','zigzag','chevrons','herringbone'].includes(p.value)) },
  { label: 'Onde & Organici', items: patternList.filter(p => ['waves','wavy-lines','scales','circles','concentric-circles'].includes(p.value)) },
  { label: 'Texture', items: patternList.filter(p => ['carbon-fiber','graph-paper','lined-paper','blueprint','noise','brick','wood-grain'].includes(p.value)) },
  { label: 'Decorativi', items: patternList.filter(p => ['stars','crosses','plus-signs','hearts'].includes(p.value)) },
];

// Mesh/aurora preview: rispecchia build_mesh_css() lato PHP (3 blob radiali + base).
const meshPreviewStyle = computed(() => {
  const b = bg.value;
  if (b.type !== 'mesh') return {};
  const c1 = b.mesh_c1 || 'var(--olo-color-primary)';
  const c2 = b.mesh_c2 || 'var(--olo-color-secondary)';
  const c3 = b.mesh_c3 || 'var(--olo-color-accent)';
  const base = b.mesh_base || 'var(--olo-color-background, #0b0a0d)';
  return {
    backgroundColor: base,
    backgroundImage: [
      `radial-gradient(60% 60% at 20% 25%, ${c1} 0%, transparent 60%)`,
      `radial-gradient(55% 55% at 80% 30%, ${c2} 0%, transparent 60%)`,
      `radial-gradient(70% 70% at 50% 90%, ${c3} 0%, transparent 65%)`,
    ].join(', '),
    backgroundRepeat: 'no-repeat',
  };
});

// Glow/Bagliori preview: usa lo stesso util getGlowCSS della resa canvas/PHP.
const glowPreviewStyle = computed(() => bg.value.type === 'glow' ? getGlowCSS(bg.value) : {});

const patternPreviewStyle = computed(() => {
  const b = bg.value;
  if (b.type !== 'pattern') return {};
  return getPatternCSS(
    b.pattern_type || 'dots',
    b.pattern_color || '#000000',
    b.pattern_bg_color || '#ffffff',
    b.pattern_size || 20,
    (b.pattern_opacity ?? 50) / 100
  );
});

const bg = computed(() => ({ ...defaultBg, ...props.modelValue }));

// v1.0.77 — preview usa direttamente bg.color (può essere #hex, rgba(...) o var(--olo-color-*)),
// l'Alfa è già parte del valore emesso da FieldColor. color_opacity è legacy no-op.
const solidPreview = computed(() => bg.value.color || '#ffffff');

// Gradient multi-stop model (backward-compat with old gradient_from/gradient_to)
const gradientModel = computed(() => {
  const b = bg.value;
  // New format: bg.gradient object with { type, angle, stops }
  if (b.gradient && typeof b.gradient === 'object' && Array.isArray(b.gradient.stops)) {
    return b.gradient;
  }
  // Migrate from old 2-color format
  return {
    type: 'linear',
    angle: parseInt(b.gradient_angle) || 180,
    stops: [
      { color: b.gradient_from || '#ffffff', position: 0 },
      { color: b.gradient_to || '#000000', position: 100 },
    ],
  };
});

function onGradientUpdate(newGrad) {
  // Save as new gradient object + keep old keys for backward compat
  const stops = newGrad.stops || [];
  emit('update:modelValue', {
    ...bg.value,
    gradient: newGrad,
    gradient_angle: newGrad.angle,
    gradient_from: stops[0]?.color || '#ffffff',
    gradient_to: stops[stops.length - 1]?.color || '#000000',
  });
}

const gradientPreview = computed(() => {
  const g = gradientModel.value;
  const stops = (g.stops || []).map(s => `${s.color} ${s.position}%`).join(', ');
  if (g.type === 'radial') return `radial-gradient(circle, ${stops})`;
  return `linear-gradient(${g.angle || 180}deg, ${stops})`;
});

function updateField(key, value) {
  emit('update:modelValue', { ...bg.value, [key]: value });
}

function pickBgImage() {
  openSingleImage(({ url }) => {
    updateField('image_url', url);
  });
}

function pickGalleryImages() {
  openGallery((images) => {
    updateField('gallery_images', images);
  });
}

function removeGalleryImage(idx) {
  const imgs = [...(bg.value.gallery_images || [])];
  imgs.splice(idx, 1);
  updateField('gallery_images', imgs);
}

function removeVideo() {
  emit('update:modelValue', { ...bg.value, video_url: '', video_poster: '' });
}

function pickBgVideo() {
  const frame = wp.media({ library: { type: 'video' }, multiple: false });
  frame.on('select', () => {
    const url = frame.state().get('selection').first().toJSON().url;
    updateField('video_url', url);
  });
  frame.open();
}

function pickBgPoster() {
  openSingleImage(({ url }) => {
    updateField('video_poster', url);
  });
}

// Whether parallax is enabled (supports both old boolean and new object format)
const isParallaxEnabled = computed(() => {
  const p = bg.value.parallax;
  return p === true || (typeof p === 'object' && p !== null);
});

// Migrate old flat parallax format to new multi-stop object
const bgParallaxData = computed(() => {
  const b = bg.value;
  // Already new format
  if (typeof b.parallax === 'object' && b.parallax !== null) {
    return b.parallax;
  }
  // Migrate from old flat keys
  const obj = { bgx: [], bgy: [], scale: [], opacity: [], blur: [], nomobile: true, easing: null, start: '', end: '' };
  if (b.parallax === true) {
    const bgy = b.parallax_bgy ?? -200;
    if (bgy !== 0) {
      obj.bgy = [{ value: 0, position: 0 }, { value: parseInt(bgy), position: 100 }];
    }
    const bgx = b.parallax_bgx ?? 0;
    if (bgx !== 0) {
      obj.bgx = [{ value: 0, position: 0 }, { value: parseInt(bgx), position: 100 }];
    }
    if (b.parallax_opacity) {
      obj.opacity = [
        { value: parseFloat(b.parallax_opacity_start ?? 0.3), position: 0 },
        { value: parseFloat(b.parallax_opacity_end ?? 1), position: 100 },
      ];
    }
    if (b.parallax_scale) {
      obj.scale = [
        { value: parseFloat(b.parallax_scale_start ?? 1), position: 0 },
        { value: parseFloat(b.parallax_scale_end ?? 1.2), position: 100 },
      ];
    }
    if (b.parallax_blur) {
      obj.blur = [
        { value: parseInt(b.parallax_blur_start ?? 5), position: 0 },
        { value: parseInt(b.parallax_blur_end ?? 0), position: 100 },
      ];
    }
    obj.nomobile = b.parallax_nomobile !== false;
  }
  return obj;
});

function toggleParallax() {
  if (isParallaxEnabled.value) {
    updateField('parallax', false);
  } else {
    // Enable with default bgy
    updateField('parallax', {
      bgx: [], bgy: [{ value: -200, position: 0 }, { value: 0, position: 100 }],
      scale: [], opacity: [], blur: [],
      nomobile: true, easing: null, start: '', end: '',
    });
  }
}

function updateParallaxData(newData) {
  updateField('parallax', newData);
}
</script>
