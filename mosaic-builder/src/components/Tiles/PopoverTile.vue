<template>
  <div class="olo-popover" style="position:relative;">
    <!-- Image -->
    <div
      v-if="settings.image"
      class="olo-popover-img"
      :style="{ background: `url(${settings.image}) center/cover no-repeat`, paddingBottom: '56.25%' }"
    ></div>
    <div
      v-else
      class="olo-popover-placeholder mb-bg-gray-700 mb-rounded-lg mb-flex mb-items-center mb-justify-center"
      style="padding-bottom: 56.25%; position: relative;"
    >
      <span class="mb-text-gray-500 mb-text-sm" style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);">Image Placeholder</span>
    </div>

    <!-- Markers -->
    <div
      v-for="(marker, i) in markers"
      :key="marker.id || i"
      class="olo-popover-marker"
      :style="{
        left: marker.x + '%',
        top: marker.y + '%',
        background: settings.marker_color || '#6366F1',
      }"
      :title="marker.title"
    >
      <span class="olo-popover-marker-pulse" :style="{ borderColor: settings.marker_color || '#6366F1' }"></span>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const markers = computed(() => {
  const raw = props.settings.markers;
  if (Array.isArray(raw) && raw.length) return raw;
  return [
    { id: 'mk-1', x: 25, y: 30, title: 'Point 1', content: 'Description...' },
    { id: 'mk-2', x: 70, y: 60, title: 'Point 2', content: 'Description...' },
  ];
});
</script>

<style scoped>
.olo-popover {
  border-radius: 6px;
  overflow: hidden;
  min-height: 80px;
}

.olo-popover-img {
  width: 100%;
  border-radius: 6px;
}

.olo-popover-marker {
  position: absolute;
  width: 16px;
  height: 16px;
  border-radius: 50%;
  transform: translate(-50%, -50%);
  cursor: pointer;
  z-index: 2;
  box-shadow: 0 0 0 3px rgba(255,255,255,0.4);
}

.olo-popover-marker-pulse {
  position: absolute;
  inset: -4px;
  border-radius: 50%;
  border: 2px solid;
  opacity: 0.5;
  animation: popover-pulse 2s ease-in-out infinite;
}

@keyframes popover-pulse {
  0%, 100% { transform: scale(1); opacity: 0.5; }
  50% { transform: scale(1.4); opacity: 0; }
}
</style>
