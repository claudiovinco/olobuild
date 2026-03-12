<template>
  <div class="olo-textpath-preview">
    <svg
      :viewBox="viewBox"
      xmlns="http://www.w3.org/2000/svg"
      style="width:100%;height:auto;overflow:visible;"
    >
      <defs>
        <path :id="pathId" :d="pathD" fill="none" />
      </defs>
      <text
        :fill="s.text_color || '#f3f4f6'"
        :font-size="parseInt(s.font_size) || 24"
        :letter-spacing="parseInt(s.letter_spacing) || 0"
        font-family="inherit"
      >
        <textPath
          :href="'#' + pathId"
          :startOffset="animStartOffset"
          dominant-baseline="middle"
        >
          {{ s.text || 'Testo su tracciato' }}
        </textPath>
      </text>
    </svg>
  </div>
</template>

<script setup>
import { computed, ref, onMounted, onBeforeUnmount } from 'vue';

const defaults = {
  text: 'Testo che segue un tracciato curvo',
  path_preset: 'arc',
  custom_path: '',
  font_size: '24',
  text_color: '#f3f4f6',
  letter_spacing: '2',
  animation: 'none',
  animation_speed: '10',
};

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const s = computed(() => ({ ...defaults, ...props.settings }));

const pathId = 'olo-tp-' + Math.random().toString(36).slice(2, 9);

const presets = {
  arc: 'M 10 80 Q 150 10 290 80',
  wave: 'M 0 50 Q 75 0 150 50 Q 225 100 300 50',
  circle: 'M 150,10 A 140,140 0 1,1 149.99,10',
  spiral: 'M 150,75 C 150,30 200,10 220,50 C 240,90 200,120 160,100 C 120,80 110,40 150,20 C 190,0 250,30 260,75 C 270,120 220,160 160,140',
};

const pathD = computed(() => {
  if (s.value.path_preset === 'custom') {
    return s.value.custom_path || presets.arc;
  }
  return presets[s.value.path_preset] || presets.arc;
});

const viewBox = computed(() => {
  if (s.value.path_preset === 'circle') return '0 0 300 300';
  return '0 0 300 100';
});

// Simple animation offset for preview
const animOffset = ref(0);
let animFrame = null;

const animStartOffset = computed(() => {
  if (s.value.animation === 'none') return '0%';
  return animOffset.value + '%';
});

function animate() {
  if (s.value.animation === 'none') return;
  const speed = Math.max(1, parseInt(s.value.animation_speed) || 10);
  const step = 0.05 / (speed / 10);

  function tick() {
    animOffset.value += step;
    if (s.value.animation === 'scroll') {
      if (animOffset.value > 100) {
        animOffset.value = 100;
        return;
      }
    } else {
      if (animOffset.value > 100) {
        animOffset.value = -50;
      }
    }
    animFrame = requestAnimationFrame(tick);
  }
  animFrame = requestAnimationFrame(tick);
}

onMounted(() => {
  if (s.value.animation !== 'none') {
    animate();
  }
});

onBeforeUnmount(() => {
  if (animFrame) cancelAnimationFrame(animFrame);
});
</script>

<style scoped>
.olo-textpath-preview {
  min-height: 60px;
  display: flex;
  align-items: center;
  justify-content: center;
}
</style>
