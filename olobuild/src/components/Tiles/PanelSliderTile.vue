<template>
  <div class="olo-panelslider" style="position:relative;overflow:hidden;">
    <!-- Cards track -->
    <div
      class="olo-ps-track"
      :style="trackStyle"
    >
      <div
        v-for="(panel, i) in visiblePanels"
        :key="panel.id || i"
        class="olo-ps-card"
        :style="cardStyle"
      >
        <div
          v-if="panel.image"
          class="olo-ps-card-img"
          :style="{ background: `url(${panel.image}) center/cover no-repeat`, height: '100px' }"
        ></div>
        <div class="olo-ps-card-body">
          <div class="mb-text-sm mb-font-semibold mb-text-gray-200 mb-mb-1">{{ panel.title }}</div>
          <div class="mb-text-xs mb-text-gray-400 mb-line-clamp-2">{{ panel.content }}</div>
        </div>
      </div>
    </div>

    <!-- Arrows -->
    <template v-if="settings.show_arrows !== false && panels.length > maxVisible">
      <button class="olo-ps-arrow olo-ps-prev" @click="prev" aria-label="Precedente">&#10094;</button>
      <button class="olo-ps-arrow olo-ps-next" @click="next" aria-label="Successivo">&#10095;</button>
    </template>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const offset = ref(0);

const panels = computed(() => {
  const raw = props.settings.panels;
  if (Array.isArray(raw) && raw.length) return raw;
  return [
    { id: 'ps-1', title: 'Scheda 1', content: 'Contenuto...', image: '' },
    { id: 'ps-2', title: 'Scheda 2', content: 'Contenuto...', image: '' },
    { id: 'ps-3', title: 'Scheda 3', content: 'Contenuto...', image: '' },
  ];
});

const maxVisible = computed(() => Math.min(parseInt(props.settings.columns) || 3, 3));

const visiblePanels = computed(() => {
  const start = offset.value;
  const end = start + maxVisible.value;
  return panels.value.slice(start, end);
});

const trackStyle = computed(() => ({
  display: 'grid',
  gridTemplateColumns: `repeat(${maxVisible.value}, 1fr)`,
  gap: '8px',
}));

const cardStyle = computed(() => {
  const style = props.settings.card_style || 'default';
  const bgMap = {
    default: '#1f2937',
    primary: '#6366F1',
    secondary: '#374151',
    hover: '#1f2937',
  };
  return {
    background: bgMap[style] || '#1f2937',
    borderRadius: '6px',
    overflow: 'hidden',
  };
});

function next() {
  if (offset.value + maxVisible.value < panels.value.length) {
    offset.value++;
  }
}

function prev() {
  if (offset.value > 0) {
    offset.value--;
  }
}
</script>

<style scoped>
.olo-panelslider {
  min-height: 80px;
}

.olo-ps-card-body {
  padding: 10px 12px;
}

.olo-ps-arrow {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  background: rgba(0,0,0,0.6);
  color: #fff;
  border: none;
  width: 28px;
  height: 28px;
  border-radius: 50%;
  cursor: pointer;
  font-size: 14px;
  z-index: 2;
  transition: background 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
}

.olo-ps-arrow:hover {
  background: rgba(0,0,0,0.85);
}

.olo-ps-prev { left: 4px; }
.olo-ps-next { right: 4px; }
</style>
