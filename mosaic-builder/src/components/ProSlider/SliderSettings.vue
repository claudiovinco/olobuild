<template>
  <div class="mb-bg-gray-900 mb-border-t mb-border-gray-700 mb-px-3 mb-py-2">
    <!-- Global background (collapsible) -->
    <div v-if="showGlobalBg" class="mb-mb-2 mb-pb-2 mb-border-b mb-border-gray-700">
      <div class="mb-flex mb-items-center mb-justify-between mb-mb-2">
        <span class="mb-text-[10px] mb-text-gray-400 mb-font-semibold mb-uppercase">Sfondo globale</span>
        <button @click="showGlobalBg = false" class="mb-text-gray-500 mb-text-xs">&times;</button>
      </div>
      <div class="mb-flex mb-gap-3 mb-flex-wrap mb-items-end">
        <div>
          <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-0.5">Tipo</label>
          <select :value="globalBg.type" @change="updateGlobalBg('type', $event.target.value)" class="mps-select-sm">
            <option value="color">Colore</option>
            <option value="image">Immagine</option>
            <option value="video">Video</option>
            <option value="gradient">Gradiente</option>
          </select>
        </div>
        <div v-if="globalBg.type === 'color'">
          <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-0.5">Colore</label>
          <input type="color" :value="globalBg.color" @input="updateGlobalBg('color', $event.target.value)" class="mb-w-8 mb-h-8 mb-rounded mb-border-0 mb-cursor-pointer" />
        </div>
        <div v-if="globalBg.type === 'image'" class="mb-flex mb-gap-2 mb-items-end">
          <div>
            <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-0.5">Immagine</label>
            <div class="mb-flex mb-gap-1">
              <input :value="globalBg.image" @input="updateGlobalBg('image', $event.target.value)" class="mps-num-input mb-w-48" placeholder="URL" />
              <button @click="pickGlobalBgImage" class="mb-px-2 mb-bg-gray-600 mb-rounded mb-text-[10px] mb-text-gray-300 hover:mb-bg-gray-500">Sfoglia</button>
            </div>
          </div>
        </div>
        <div v-if="globalBg.type === 'video'">
          <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-0.5">URL Video</label>
          <div class="mb-flex mb-gap-1">
            <input :value="globalBg.video" @input="updateGlobalBg('video', $event.target.value)" class="mps-num-input mb-w-52" placeholder="mp4 o URL YouTube" />
            <button @click="pickGlobalBgVideo" class="mb-px-2 mb-bg-gray-600 mb-rounded mb-text-[10px] mb-text-gray-300 hover:mb-bg-gray-500">Sfoglia</button>
          </div>
        </div>
        <template v-if="globalBg.type === 'gradient'">
          <div>
            <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-0.5">Da</label>
            <input type="color" :value="globalBg.gradientFrom" @input="updateGlobalBg('gradientFrom', $event.target.value)" class="mb-w-8 mb-h-8 mb-rounded mb-border-0 mb-cursor-pointer" />
          </div>
          <div>
            <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-0.5">A</label>
            <input type="color" :value="globalBg.gradientTo" @input="updateGlobalBg('gradientTo', $event.target.value)" class="mb-w-8 mb-h-8 mb-rounded mb-border-0 mb-cursor-pointer" />
          </div>
          <div>
            <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-0.5">Angolo</label>
            <input type="number" :value="globalBg.gradientAngle" @input="updateGlobalBg('gradientAngle', parseInt($event.target.value))" min="0" max="360" class="mps-num-input mb-w-14" />
          </div>
        </template>
      </div>
    </div>
    <div v-else class="mb-mb-1">
      <button @click="showGlobalBg = true" class="mb-text-[10px] mb-text-gray-400 hover:mb-text-gray-300">
        Sfondo globale &darr;
      </button>
    </div>

    <div class="mb-flex mb-items-center mb-gap-4 mb-flex-wrap">
      <!-- Height -->
      <div class="mb-flex mb-items-center mb-gap-1">
        <label class="mb-text-[10px] mb-text-gray-400">Altezza</label>
        <input type="number" :value="settings.height" @input="up('height', parseInt($event.target.value) || 600)" min="200" max="1200" step="10" class="mps-num-input mb-w-16" />
      </div>

      <!-- Autoplay -->
      <label class="mb-flex mb-items-center mb-gap-1 mb-cursor-pointer">
        <button @click="up('autoplay', !settings.autoplay)" :class="['mps-toggle', settings.autoplay ? 'mps-toggle-on' : 'mps-toggle-off']">
          <span :class="['mps-toggle-dot', settings.autoplay ? 'mps-toggle-dot-on' : 'mps-toggle-dot-off']"></span>
        </button>
        <span class="mb-text-[10px] mb-text-gray-400">Riproduzione automatica</span>
      </label>

      <!-- Speed -->
      <div v-if="settings.autoplay" class="mb-flex mb-items-center mb-gap-1">
        <label class="mb-text-[10px] mb-text-gray-400">Velocita</label>
        <input type="number" :value="settings.autoplaySpeed" @input="up('autoplaySpeed', parseInt($event.target.value) || 5000)" min="1000" max="20000" step="500" class="mps-num-input mb-w-16" />
      </div>

      <!-- Pause on hover -->
      <label v-if="settings.autoplay" class="mb-flex mb-items-center mb-gap-1 mb-cursor-pointer">
        <button @click="up('pauseOnHover', !settings.pauseOnHover)" :class="['mps-toggle', settings.pauseOnHover ? 'mps-toggle-on' : 'mps-toggle-off']">
          <span :class="['mps-toggle-dot', settings.pauseOnHover ? 'mps-toggle-dot-on' : 'mps-toggle-dot-off']"></span>
        </button>
        <span class="mb-text-[10px] mb-text-gray-400">Pausa hover</span>
      </label>

      <!-- Transition -->
      <div class="mb-flex mb-items-center mb-gap-1">
        <label class="mb-text-[10px] mb-text-gray-400">Trans.</label>
        <select :value="settings.transition" @change="up('transition', $event.target.value)" class="mps-select-sm">
          <option value="fade">Dissolvenza</option>
          <option value="slide">Scorrimento</option>
          <option value="zoom">Zoom</option>
        </select>
      </div>

      <!-- Arrows -->
      <label class="mb-flex mb-items-center mb-gap-1 mb-cursor-pointer">
        <button @click="up('showArrows', !settings.showArrows)" :class="['mps-toggle', settings.showArrows ? 'mps-toggle-on' : 'mps-toggle-off']">
          <span :class="['mps-toggle-dot', settings.showArrows ? 'mps-toggle-dot-on' : 'mps-toggle-dot-off']"></span>
        </button>
        <span class="mb-text-[10px] mb-text-gray-400">Frecce</span>
      </label>

      <!-- Dots -->
      <label class="mb-flex mb-items-center mb-gap-1 mb-cursor-pointer">
        <button @click="up('showDots', !settings.showDots)" :class="['mps-toggle', settings.showDots ? 'mps-toggle-on' : 'mps-toggle-off']">
          <span :class="['mps-toggle-dot', settings.showDots ? 'mps-toggle-dot-on' : 'mps-toggle-dot-off']"></span>
        </button>
        <span class="mb-text-[10px] mb-text-gray-400">Punti</span>
      </label>

      <!-- Keyboard -->
      <label class="mb-flex mb-items-center mb-gap-1 mb-cursor-pointer">
        <button @click="up('keyboard', !settings.keyboard)" :class="['mps-toggle', settings.keyboard ? 'mps-toggle-on' : 'mps-toggle-off']">
          <span :class="['mps-toggle-dot', settings.keyboard ? 'mps-toggle-dot-on' : 'mps-toggle-dot-off']"></span>
        </button>
        <span class="mb-text-[10px] mb-text-gray-400">Tastiera</span>
      </label>

      <!-- Swipe -->
      <label class="mb-flex mb-items-center mb-gap-1 mb-cursor-pointer">
        <button @click="up('swipe', !settings.swipe)" :class="['mps-toggle', settings.swipe ? 'mps-toggle-on' : 'mps-toggle-off']">
          <span :class="['mps-toggle-dot', settings.swipe ? 'mps-toggle-dot-on' : 'mps-toggle-dot-off']"></span>
        </button>
        <span class="mb-text-[10px] mb-text-gray-400">Swipe</span>
      </label>

      <!-- Loop -->
      <label class="mb-flex mb-items-center mb-gap-1 mb-cursor-pointer">
        <button @click="up('loop', !settings.loop)" :class="['mps-toggle', settings.loop ? 'mps-toggle-on' : 'mps-toggle-off']">
          <span :class="['mps-toggle-dot', settings.loop ? 'mps-toggle-dot-on' : 'mps-toggle-dot-off']"></span>
        </button>
        <span class="mb-text-[10px] mb-text-gray-400">Ripeti</span>
      </label>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
  settings: { type: Object, required: true },
});

const emit = defineEmits(['update']);

const showGlobalBg = ref(false);

const globalBg = computed(() => props.settings.globalBackground || {
  type: 'color', color: '#1e293b', image: '', video: '',
  gradientFrom: '#1e293b', gradientTo: '#0f172a', gradientAngle: 180,
});

function up(key, val) {
  emit('update', key, val);
}

function updateGlobalBg(key, val) {
  const current = JSON.parse(JSON.stringify(globalBg.value));
  current[key] = val;
  emit('update', 'globalBackground', current);
}

function pickGlobalBgImage() {
  if (!window.wp?.media) return;
  const frame = wp.media({ title: 'Seleziona immagine sfondo globale', multiple: false, library: { type: 'image' } });
  frame.on('select', () => {
    const url = frame.state().get('selection').first().toJSON().url;
    updateGlobalBg('image', url);
  });
  frame.open();
}

function pickGlobalBgVideo() {
  if (!window.wp?.media) return;
  const frame = wp.media({ title: 'Seleziona video sfondo globale', multiple: false, library: { type: 'video' } });
  frame.on('select', () => {
    const url = frame.state().get('selection').first().toJSON().url;
    updateGlobalBg('video', url);
  });
  frame.open();
}
</script>

<style scoped>
.mps-num-input {
  background: #ffffff;
  border: 1px solid #d1d5db;
  border-radius: 0.25rem;
  padding: 2px 6px;
  font-size: 11px;
  color: #111827;
  text-align: center;
}
.mps-select-sm {
  background: #ffffff;
  border: 1px solid #d1d5db;
  border-radius: 0.25rem;
  padding: 2px 6px;
  font-size: 11px;
  color: #111827;
}
.mps-toggle {
  position: relative;
  width: 28px;
  height: 16px;
  border-radius: 9999px;
  transition: background-color 0.2s;
  flex-shrink: 0;
  border: none;
  cursor: pointer;
  padding: 0;
}
.mps-toggle-on  { background: var(--olo-color-primary, #6366f1); }
.mps-toggle-off { background: #4b5563; }
.mps-toggle-dot {
  position: absolute;
  top: 2px;
  width: 12px;
  height: 12px;
  border-radius: 9999px;
  background: #fff;
  transition: left 0.2s;
}
.mps-toggle-dot-on  { left: 14px; }
.mps-toggle-dot-off { left: 2px; }
</style>
