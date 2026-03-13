<template>
  <div class="mb-w-64 mb-bg-gray-900 mb-border-l mb-border-gray-700 mb-overflow-y-auto mb-flex mb-flex-col">
    <template v-if="layer">
      <!-- Header -->
      <div class="mb-p-2 mb-border-b mb-border-gray-700 mb-text-xs mb-font-semibold mb-text-gray-300">
        Livello {{ layer.type.charAt(0).toUpperCase() + layer.type.slice(1) }}
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
              <label class="mps-label">Testo</label>
              <textarea :value="layer.content" @input="up('content', $event.target.value)" rows="3"
                class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded mb-px-2 mb-py-1 mb-text-sm mb-text-gray-900 mb-resize-y"></textarea>
            </div>
            <div>
              <label class="mps-label">Tag</label>
              <select :value="layer.tag" @change="up('tag', $event.target.value)" class="mps-select">
                <option v-for="t in ['h1','h2','h3','h4','h5','h6','p','span','div']" :key="t" :value="t">{{ t }}</option>
              </select>
            </div>
          </template>
          <!-- Image -->
          <template v-if="layer.type === 'image'">
            <div>
              <label class="mps-label">URL Immagine</label>
              <div class="mb-flex mb-gap-1">
                <input :value="layer.imageSrc" @input="up('imageSrc', $event.target.value)" class="mps-input mb-flex-1" placeholder="https://..." />
                <button @click="pickImage" class="mb-px-2 mb-bg-gray-600 mb-rounded mb-text-xs mb-text-gray-300 hover:mb-bg-gray-500">Sfoglia</button>
              </div>
            </div>
          </template>
          <!-- Button -->
          <template v-if="layer.type === 'button'">
            <div>
              <label class="mps-label">Etichetta</label>
              <input :value="layer.content" @input="up('content', $event.target.value)" class="mps-input" />
            </div>
            <div>
              <label class="mps-label">URL</label>
              <input :value="layer.buttonUrl" @input="up('buttonUrl', $event.target.value)" class="mps-input" placeholder="https://..." />
            </div>
            <div>
              <label class="mps-label">Destinazione</label>
              <select :value="layer.buttonTarget" @change="up('buttonTarget', $event.target.value)" class="mps-select">
                <option value="_self">Stessa scheda</option>
                <option value="_blank">Nuova scheda</option>
              </select>
            </div>
          </template>
          <!-- Icon -->
          <template v-if="layer.type === 'icon'">
            <div>
              <label class="mps-label">Nome icona</label>
              <div class="mb-flex mb-gap-1 mb-items-center">
                <span v-if="layer.iconName && iconsSvg[layer.iconName]" class="mps-icon-preview" v-html="iconsSvg[layer.iconName]"></span>
                <input :value="layer.iconName" @input="up('iconName', $event.target.value)" class="mps-input mb-flex-1" placeholder="star" />
                <button @click="showIconPicker = true" class="mb-px-2 mb-bg-gray-600 mb-rounded mb-text-xs mb-text-gray-300 hover:mb-bg-gray-500">Sfoglia</button>
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
              <label class="mps-label">URL Video</label>
              <div class="mb-flex mb-gap-1">
                <input :value="layer.videoSrc" @input="up('videoSrc', $event.target.value)" class="mps-input mb-flex-1" placeholder="mp4 o YouTube URL" />
                <button @click="pickVideo" class="mb-px-2 mb-bg-gray-600 mb-rounded mb-text-xs mb-text-gray-300 hover:mb-bg-gray-500">Sfoglia</button>
              </div>
            </div>
            <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
              <input type="checkbox" :checked="layer.videoAutoplay" @change="up('videoAutoplay', $event.target.checked)" class="mb-rounded" />
              <span class="mb-text-[11px] mb-text-gray-400">Autoplay</span>
            </label>
            <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
              <input type="checkbox" :checked="layer.videoMuted" @change="up('videoMuted', $event.target.checked)" class="mb-rounded" />
              <span class="mb-text-[11px] mb-text-gray-400">Muto</span>
            </label>
            <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
              <input type="checkbox" :checked="layer.videoLoop" @change="up('videoLoop', $event.target.checked)" class="mb-rounded" />
              <span class="mb-text-[11px] mb-text-gray-400">Loop</span>
            </label>
          </template>
        </template>

        <!-- ===== POSIZIONE ===== -->
        <template v-if="activeTab === 'Posizione'">
          <div>
            <label class="mps-label">X (%)</label>
            <div class="mb-flex mb-items-center mb-gap-2">
              <input type="range" :value="layer.x" @input="up('x', parseFloat($event.target.value))" min="0" max="100" step="0.5" class="mb-flex-1" />
              <span class="mps-val">{{ layer.x }}</span>
            </div>
          </div>
          <div>
            <label class="mps-label">Y (%)</label>
            <div class="mb-flex mb-items-center mb-gap-2">
              <input type="range" :value="layer.y" @input="up('y', parseFloat($event.target.value))" min="0" max="100" step="0.5" class="mb-flex-1" />
              <span class="mps-val">{{ layer.y }}</span>
            </div>
          </div>
          <div>
            <label class="mps-label">Larghezza (%)</label>
            <div class="mb-flex mb-items-center mb-gap-2">
              <label class="mb-flex mb-items-center mb-gap-1 mb-text-[10px] mb-text-gray-400">
                <input type="checkbox" :checked="layer.width === 'auto'" @change="up('width', $event.target.checked ? 'auto' : 50)" class="mb-rounded" /> Auto
              </label>
              <template v-if="layer.width !== 'auto'">
                <input type="range" :value="layer.width" @input="up('width', parseFloat($event.target.value))" min="5" max="100" step="0.5" class="mb-flex-1" />
                <span class="mps-val">{{ layer.width }}</span>
              </template>
            </div>
          </div>
          <div>
            <label class="mps-label">Altezza (%)</label>
            <div class="mb-flex mb-items-center mb-gap-2">
              <label class="mb-flex mb-items-center mb-gap-1 mb-text-[10px] mb-text-gray-400">
                <input type="checkbox" :checked="layer.height === 'auto'" @change="up('height', $event.target.checked ? 'auto' : 30)" class="mb-rounded" /> Auto
              </label>
              <template v-if="layer.height !== 'auto'">
                <input type="range" :value="layer.height" @input="up('height', parseFloat($event.target.value))" min="5" max="100" step="0.5" class="mb-flex-1" />
                <span class="mps-val">{{ layer.height }}</span>
              </template>
            </div>
          </div>
        </template>

        <!-- ===== STILE ===== -->
        <template v-if="activeTab === 'Stile'">
          <div>
            <label class="mps-label">Dimensione font (px)</label>
            <div class="mb-flex mb-items-center mb-gap-2">
              <input type="range" :value="layer.fontSize" @input="up('fontSize', parseFloat($event.target.value))" min="8" max="200" step="1" class="mb-flex-1" />
              <span class="mps-val">{{ layer.fontSize }}</span>
            </div>
          </div>
          <div>
            <label class="mps-label">Peso font</label>
            <select :value="layer.fontWeight" @change="up('fontWeight', $event.target.value)" class="mps-select">
              <option v-for="w in ['300','400','500','600','700','800','900']" :key="w" :value="w">{{ w }}</option>
            </select>
          </div>
          <div>
            <label class="mps-label">Colore</label>
            <FieldColor :modelValue="layer.color || '#ffffff'" @update:modelValue="up('color', $event)" />
          </div>
          <div>
            <label class="mps-label">Allineamento testo</label>
            <div class="mb-flex mb-gap-1">
              <button v-for="a in ['left','center','right']" :key="a"
                @click="up('textAlign', a)"
                :class="['mb-flex-1 mb-py-1 mb-rounded mb-text-xs', layer.textAlign === a ? 'mb-bg-primary-600 mb-text-white' : 'mb-bg-gray-700 mb-text-gray-400']"
              >{{ a }}</button>
            </div>
          </div>
          <div>
            <label class="mps-label">Sfondo</label>
            <FieldColor :modelValue="layer.bgColor || '#000000'" @update:modelValue="up('bgColor', $event)" />
          </div>
          <div>
            <label class="mps-label">Raggio bordo (px)</label>
            <div class="mb-flex mb-items-center mb-gap-2">
              <input type="range" :value="layer.borderRadius" @input="up('borderRadius', parseFloat($event.target.value))" min="0" max="100" step="1" class="mb-flex-1" />
              <span class="mps-val">{{ layer.borderRadius }}</span>
            </div>
          </div>
          <div>
            <label class="mps-label">Padding (px)</label>
            <div class="mb-flex mb-items-center mb-gap-2">
              <input type="range" :value="layer.padding" @input="up('padding', parseFloat($event.target.value))" min="0" max="80" step="1" class="mb-flex-1" />
              <span class="mps-val">{{ layer.padding }}</span>
            </div>
          </div>
          <div>
            <label class="mps-label">Opacita (%)</label>
            <div class="mb-flex mb-items-center mb-gap-2">
              <input type="range" :value="layer.opacity ?? 100" @input="up('opacity', parseFloat($event.target.value))" min="0" max="100" step="5" class="mb-flex-1" />
              <span class="mps-val">{{ layer.opacity ?? 100 }}</span>
            </div>
          </div>
        </template>

        <!-- ===== ANIMAZIONE ===== -->
        <template v-if="activeTab === 'Animazione'">
          <p class="mb-text-[10px] mb-text-gray-400 mb-font-semibold mb-uppercase">Entrata</p>
          <div>
            <label class="mps-label">Animazione entrata</label>
            <select :value="layer.animIn" @change="up('animIn', $event.target.value)" class="mps-select">
              <option v-for="a in animationsIn" :key="a" :value="a">{{ a }}</option>
            </select>
          </div>
          <div>
            <label class="mps-label">Durata (ms)</label>
            <div class="mb-flex mb-items-center mb-gap-2">
              <input type="range" :value="layer.animInDuration" @input="up('animInDuration', parseFloat($event.target.value))" min="100" max="3000" step="50" class="mb-flex-1" />
              <span class="mps-val">{{ layer.animInDuration }}</span>
            </div>
          </div>
          <div>
            <label class="mps-label">Ritardo (ms)</label>
            <div class="mb-flex mb-items-center mb-gap-2">
              <input type="range" :value="layer.animInDelay" @input="up('animInDelay', parseFloat($event.target.value))" min="0" max="3000" step="50" class="mb-flex-1" />
              <span class="mps-val">{{ layer.animInDelay }}</span>
            </div>
          </div>

          <div class="mb-border-t mb-border-gray-700 mb-pt-2 mb-mt-2"></div>
          <p class="mb-text-[10px] mb-text-gray-400 mb-font-semibold mb-uppercase">Uscita</p>
          <div>
            <label class="mps-label">Animazione uscita</label>
            <select :value="layer.animOut" @change="up('animOut', $event.target.value)" class="mps-select">
              <option v-for="a in animationsOut" :key="a" :value="a">{{ a }}</option>
            </select>
          </div>
          <div>
            <label class="mps-label">Durata (ms)</label>
            <div class="mb-flex mb-items-center mb-gap-2">
              <input type="range" :value="layer.animOutDuration" @input="up('animOutDuration', parseFloat($event.target.value))" min="100" max="3000" step="50" class="mb-flex-1" />
              <span class="mps-val">{{ layer.animOutDuration }}</span>
            </div>
          </div>
          <div>
            <label class="mps-label">Ritardo (ms)</label>
            <div class="mb-flex mb-items-center mb-gap-2">
              <input type="range" :value="layer.animOutDelay" @input="up('animOutDelay', parseFloat($event.target.value))" min="0" max="3000" step="50" class="mb-flex-1" />
              <span class="mps-val">{{ layer.animOutDelay }}</span>
            </div>
          </div>
          <div>
            <label class="mps-label">Curva di easing</label>
            <select :value="layer.animEasing" @change="up('animEasing', $event.target.value)" class="mps-select">
              <option v-for="e in ['ease','ease-in','ease-out','ease-in-out','linear']" :key="e" :value="e">{{ e }}</option>
            </select>
          </div>
        </template>

      </div>
    </template>

    <!-- No selection -->
    <div v-else class="mb-p-4 mb-text-xs mb-text-gray-500 mb-text-center mb-mt-8">
      Seleziona un livello per modificarne le proprietà
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import IconPicker from './IconPicker.vue';
import iconsSvg from './uikitIconsSvg.js';
import FieldColor from '@/components/Builder/fields/FieldColor.vue';

const props = defineProps({
  layer: { type: Object, default: null },
});

const emit = defineEmits(['update']);

const showIconPicker = ref(false);

const activeTab = ref('Contenuto');
const tabs = ['Contenuto', 'Posizione', 'Stile', 'Animazione'];

const animationsIn = [
  'none',
  'fadeIn', 'fadeInUp', 'fadeInDown', 'fadeInLeft', 'fadeInRight',
  'slideInLeft', 'slideInRight', 'slideInUp', 'slideInDown',
  'zoomIn', 'bounceIn', 'rotateIn',
];
const animationsOut = [
  'none',
  'fadeOut', 'fadeOutUp', 'fadeOutDown', 'fadeOutLeft', 'fadeOutRight',
  'slideOutLeft', 'slideOutRight', 'slideOutUp', 'slideOutDown',
  'zoomOut', 'bounceOut', 'rotateOut',
];

function up(key, val) {
  emit('update', key, val);
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
</script>

<style scoped>
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
.mps-select {
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
  fill: #d1d5db;
  stroke: #d1d5db;
}
</style>
