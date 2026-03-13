<template>
  <div class="mb-space-y-3">
    <!-- Type selector -->
    <div>
      <label class="mb-block mb-text-xs mb-font-semibold mb-text-gray-300 mb-mb-2">Tipo di sfondo</label>
      <div class="mb-flex mb-gap-1 mb-bg-gray-700 mb-rounded-lg mb-p-0.5">
        <button
          v-for="t in types"
          :key="t.value"
          @click="updateField('type', t.value)"
          :class="[
            'mb-flex-1 mb-py-1.5 mb-text-[10px] mb-font-medium mb-rounded-md mb-transition-colors',
            bg.type === t.value
              ? 'mb-bg-primary-600 mb-text-white'
              : 'mb-text-gray-400 hover:mb-text-gray-300'
          ]"
        >
          {{ t.label }}
        </button>
      </div>
    </div>

    <!-- Solid color -->
    <div v-if="bg.type === 'solid'" class="mb-space-y-2">
      <label class="mb-block mb-text-[10px] mb-text-gray-400">Colore</label>
      <FieldColor
        :modelValue="bg.color || '#ffffff'"
        @update:modelValue="updateField('color', $event)"
      />
      <div>
        <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-1">Opacità sfondo</label>
        <div class="mb-flex mb-items-center mb-gap-2">
          <input
            type="range"
            :value="bg.color_opacity ?? 100"
            @input="updateField('color_opacity', parseInt($event.target.value))"
            min="0" max="100" step="5"
            class="mb-flex-1"
          />
          <span class="mb-text-xs mb-text-gray-400 mb-w-10 mb-text-right">{{ bg.color_opacity ?? 100 }}%</span>
        </div>
        <p class="mb-text-[9px] mb-text-gray-500 mb-mt-0.5">Solo lo sfondo diventa trasparente, non il contenuto</p>
      </div>
      <!-- Preview swatch -->
      <div
        class="mb-h-6 mb-rounded-md mb-border mb-border-gray-600"
        :style="{ background: solidPreview }"
      ></div>
    </div>

    <!-- Gradient (multi-stop) -->
    <div v-if="bg.type === 'gradient'" class="mb-space-y-3">
      <FieldGradient
        :modelValue="gradientModel"
        @update:modelValue="onGradientUpdate"
      />
      <!-- Gradient preview -->
      <div
        class="mb-h-6 mb-rounded-md mb-border mb-border-gray-600"
        :style="{ background: gradientPreview }"
      ></div>
    </div>

    <!-- Image -->
    <div v-if="bg.type === 'image'" class="mb-space-y-3">
      <!-- Thumbnail -->
      <div v-if="bg.image_url" class="mb-relative mb-group">
        <img
          :src="bg.image_url"
          alt="Background"
          class="mb-w-full mb-h-20 mb-object-cover mb-rounded-md mb-border mb-border-gray-600"
        />
        <button
          @click="updateField('image_url', '')"
          class="mb-absolute mb-top-1 mb-right-1 mb-bg-red-600 mb-text-white mb-rounded-full mb-w-5 mb-h-5 mb-text-xs mb-flex mb-items-center mb-justify-center mb-opacity-0 group-hover:mb-opacity-100 mb-transition-opacity"
          title="Rimuovi immagine"
        >&times;</button>
      </div>
      <button
        @click="pickBgImage"
        class="mb-w-full mb-py-1.5 mb-px-3 mb-bg-gray-700 mb-border mb-border-gray-600 mb-rounded-md mb-text-xs mb-text-gray-300 hover:mb-bg-gray-600 mb-transition-colors"
      >
        {{ bg.image_url ? 'Cambia immagine' : 'Seleziona immagine' }}
      </button>

      <!-- Size -->
      <div>
        <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-1">Dimensione</label>
        <select
          :value="bg.image_size || 'cover'"
          @change="updateField('image_size', $event.target.value)"
          class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1 mb-text-sm mb-text-gray-900"
        >
          <option value="cover">Cover</option>
          <option value="contain">Contain</option>
          <option value="auto">Auto</option>
        </select>
      </div>

      <!-- Position -->
      <div>
        <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-1">Posizione</label>
        <select
          :value="bg.image_position || 'center center'"
          @change="updateField('image_position', $event.target.value)"
          class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1 mb-text-sm mb-text-gray-900"
        >
          <option value="center center">Centro</option>
          <option value="top center">Alto</option>
          <option value="bottom center">Basso</option>
          <option value="left center">Sinistra</option>
          <option value="right center">Destra</option>
          <option value="top left">Alto sinistra</option>
          <option value="top right">Alto destra</option>
          <option value="bottom left">Basso sinistra</option>
          <option value="bottom right">Basso destra</option>
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
          <span class="mb-text-xs mb-text-gray-300">Parallasse</span>
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
          <img v-if="bg.video_poster" :src="bg.video_poster" alt="Video poster" class="mb-w-full mb-h-20 mb-object-cover" />
          <span v-else class="mb-text-2xl">&#x1F3AC;</span>
        </div>
        <button
          @click="removeVideo"
          class="mb-absolute mb-top-1 mb-right-1 mb-bg-red-600 mb-text-white mb-rounded-full mb-w-5 mb-h-5 mb-text-xs mb-flex mb-items-center mb-justify-center mb-opacity-0 group-hover:mb-opacity-100 mb-transition-opacity"
          title="Rimuovi video"
        >&times;</button>
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
        <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-1">Adattamento</label>
        <select
          :value="bg.video_fit || 'cover'"
          @change="updateField('video_fit', $event.target.value)"
          class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1 mb-text-sm mb-text-gray-900"
        >
          <option value="cover">Cover</option>
          <option value="contain">Contain</option>
          <option value="fill">Riempi</option>
          <option value="none">Nessuno (dimensione originale)</option>
        </select>
      </div>

      <!-- Position -->
      <div>
        <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-1">Posizione</label>
        <select
          :value="bg.image_position || 'center center'"
          @change="updateField('image_position', $event.target.value)"
          class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1 mb-text-sm mb-text-gray-900"
        >
          <option value="center center">Centro</option>
          <option value="top center">Alto</option>
          <option value="bottom center">Basso</option>
          <option value="left center">Sinistra</option>
          <option value="right center">Destra</option>
          <option value="top left">Alto sinistra</option>
          <option value="top right">Alto destra</option>
          <option value="bottom left">Basso sinistra</option>
          <option value="bottom right">Basso destra</option>
        </select>
      </div>

      <!-- Cover Height -->
      <div>
        <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-1">Altezza cover (px)</label>
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
        <p class="mb-text-[9px] mb-text-gray-500 mb-mt-0.5">0 = auto (altezza contenuto)</p>
      </div>

      <!-- Video Scale (zoom) -->
      <div>
        <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-1">Scala video (%)</label>
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
        <p class="mb-text-[9px] mb-text-gray-500 mb-mt-0.5">100% = normale, 200% = doppio</p>
      </div>
    </div>

    <!-- Overlay (for all types except none) -->
    <div v-if="bg.type && bg.type !== 'none'" class="mb-space-y-2 mb-pt-2 mb-border-t mb-border-gray-700">
      <label class="mb-block mb-text-xs mb-font-semibold mb-text-gray-300">Sovrapposizione</label>
      <FieldColor
        :modelValue="bg.overlay_color || '#000000'"
        @update:modelValue="updateField('overlay_color', $event)"
      />
      <div class="mb-flex mb-items-center mb-gap-2">
        <span class="mb-text-[10px] mb-text-gray-400 mb-shrink-0">Opacità</span>
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
import { useMediaPicker } from '@/composables/useMediaPicker';
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

const { openSingleImage } = useMediaPicker();

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
  overlay_color: '#000000',
  overlay_opacity: 0,
  color_opacity: 100,
};

const types = [
  { value: 'none', label: 'Nessuno' },
  { value: 'solid', label: 'Tinta unita' },
  { value: 'gradient', label: 'Gradiente' },
  { value: 'image', label: 'Immagine' },
  { value: 'video', label: 'Video' },
];

const bg = computed(() => ({ ...defaultBg, ...props.modelValue }));

const solidPreview = computed(() => {
  const hex = bg.value.color || '#ffffff';
  const opacity = (bg.value.color_opacity ?? 100) / 100;
  return hexToRgba(hex, opacity);
});

function hexToRgba(hex, alpha) {
  const h = hex.replace('#', '');
  const rp = parseInt(h.substring(0, 2), 16); const r = isNaN(rp) ? 0 : rp;
  const gp = parseInt(h.substring(2, 4), 16); const g = isNaN(gp) ? 0 : gp;
  const bp = parseInt(h.substring(4, 6), 16); const b = isNaN(bp) ? 0 : bp;
  return `rgba(${r}, ${g}, ${b}, ${alpha})`;
}

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
