<template>
  <div class="mb-flex mb-gap-4 mb-justify-center mb-items-center mb-py-8 mb-px-4 mb-flex-wrap" :style="{ background: settings.bg_color, color: settings.text_color }">
    <template v-if="!expired">
      <template v-for="(unit, i) in visibleUnits" :key="unit.key">
        <span v-if="i > 0 && settings.separator" class="mb-text-2xl mb-font-bold mb-opacity-50">{{ settings.separator }}</span>
        <div class="mb-text-center" style="min-width:70px;">
          <div class="mb-text-4xl mb-font-extrabold mb-leading-tight" :style="{ color: settings.accent_color }">
            {{ String(timeValues[unit.key] || 0).padStart(2, '0') }}
          </div>
          <div class="mb-text-xs mb-opacity-70 mb-mt-1 mb-uppercase mb-tracking-wider">{{ unit.label }}</div>
        </div>
      </template>
    </template>
    <div v-else class="mb-text-xl mb-py-4">{{ settings.expired_message }}</div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const timeValues = ref({ days: 0, hours: 0, minutes: 0, seconds: 0 });
const expired = ref(false);
let timer = null;

const visibleUnits = computed(() => {
  const units = [];
  if (props.settings.show_days !== false) units.push({ key: 'days', label: props.settings.label_days || 'Days' });
  if (props.settings.show_hours !== false) units.push({ key: 'hours', label: props.settings.label_hours || 'Hours' });
  if (props.settings.show_minutes !== false) units.push({ key: 'minutes', label: props.settings.label_minutes || 'Minutes' });
  if (props.settings.show_seconds !== false) units.push({ key: 'seconds', label: props.settings.label_seconds || 'Seconds' });
  return units;
});

function tick() {
  const target = new Date(props.settings.target_date).getTime();
  const diff = target - Date.now();
  if (diff <= 0) {
    expired.value = true;
    clearInterval(timer);
    return;
  }
  expired.value = false;
  timeValues.value = {
    days: Math.floor(diff / 86400000),
    hours: Math.floor((diff % 86400000) / 3600000),
    minutes: Math.floor((diff % 3600000) / 60000),
    seconds: Math.floor((diff % 60000) / 1000),
  };
}

onMounted(() => {
  tick();
  timer = setInterval(tick, 1000);
});

onUnmounted(() => {
  clearInterval(timer);
});

watch(() => props.settings.target_date, () => {
  expired.value = false;
  tick();
});
</script>
