<template>
  <div class="olo-icon-select">
    <button
      v-for="opt in options"
      :key="opt.value"
      :class="['olo-is-btn', { 'olo-is-btn--active': modelValue === opt.value }]"
      :title="t(opt.label)"
      @click="$emit('update:modelValue', opt.value)"
    >
      <svg viewBox="0 0 20 20" class="olo-is-icon">
        <!-- Arrow Right (row direction) -->
        <template v-if="opt.icon === 'arrow-right'">
          <line x1="4" y1="10" x2="16" y2="10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
          <polyline points="12,6 16,10 12,14" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
        </template>
        <!-- Arrow Down (column direction) -->
        <template v-else-if="opt.icon === 'arrow-down'">
          <line x1="10" y1="4" x2="10" y2="16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
          <polyline points="6,12 10,16 14,12" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
        </template>
        <!-- Align Left (justify start) -->
        <template v-else-if="opt.icon === 'align-left'">
          <line x1="3" y1="3" x2="3" y2="17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
          <rect x="5" y="5" width="10" height="3" rx="1" fill="currentColor"/>
          <rect x="5" y="12" width="7" height="3" rx="1" fill="currentColor"/>
        </template>
        <!-- Align Center (justify center) -->
        <template v-else-if="opt.icon === 'align-center'">
          <line x1="10" y1="2" x2="10" y2="18" stroke="currentColor" stroke-width="1" stroke-dasharray="1.5 1.5" opacity="0.4"/>
          <rect x="4" y="5" width="12" height="3" rx="1" fill="currentColor"/>
          <rect x="6" y="12" width="8" height="3" rx="1" fill="currentColor"/>
        </template>
        <!-- Align Right (justify end) -->
        <template v-else-if="opt.icon === 'align-right'">
          <line x1="17" y1="3" x2="17" y2="17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
          <rect x="5" y="5" width="10" height="3" rx="1" fill="currentColor"/>
          <rect x="8" y="12" width="7" height="3" rx="1" fill="currentColor"/>
        </template>
        <!-- Align Justify (stretch) -->
        <template v-else-if="opt.icon === 'align-justify'">
          <rect x="3" y="5" width="14" height="3" rx="1" fill="currentColor"/>
          <rect x="3" y="12" width="14" height="3" rx="1" fill="currentColor"/>
        </template>
        <!-- Space Between -->
        <template v-else-if="opt.icon === 'space-between'">
          <rect x="2" y="6" width="4" height="8" rx="1" fill="currentColor"/>
          <rect x="14" y="6" width="4" height="8" rx="1" fill="currentColor"/>
          <line x1="8" y1="10" x2="12" y2="10" stroke="currentColor" stroke-width="1" stroke-dasharray="1.5 1.5" opacity="0.5"/>
        </template>
        <!-- Space Around -->
        <template v-else-if="opt.icon === 'space-around'">
          <rect x="4" y="6" width="4" height="8" rx="1" fill="currentColor"/>
          <rect x="12" y="6" width="4" height="8" rx="1" fill="currentColor"/>
        </template>
        <!-- Space Evenly -->
        <template v-else-if="opt.icon === 'space-evenly'">
          <rect x="3" y="6" width="3" height="8" rx="1" fill="currentColor"/>
          <rect x="8.5" y="6" width="3" height="8" rx="1" fill="currentColor"/>
          <rect x="14" y="6" width="3" height="8" rx="1" fill="currentColor"/>
        </template>
        <!-- Align Top (vertical start) -->
        <template v-else-if="opt.icon === 'align-top'">
          <line x1="3" y1="3" x2="17" y2="3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
          <rect x="5" y="5" width="3" height="10" rx="1" fill="currentColor"/>
          <rect x="12" y="5" width="3" height="7" rx="1" fill="currentColor"/>
        </template>
        <!-- Align Middle (vertical center) -->
        <template v-else-if="opt.icon === 'align-middle'">
          <line x1="2" y1="10" x2="18" y2="10" stroke="currentColor" stroke-width="1" stroke-dasharray="1.5 1.5" opacity="0.4"/>
          <rect x="5" y="4" width="3" height="12" rx="1" fill="currentColor"/>
          <rect x="12" y="6" width="3" height="8" rx="1" fill="currentColor"/>
        </template>
        <!-- Align Bottom (vertical end) -->
        <template v-else-if="opt.icon === 'align-bottom'">
          <line x1="3" y1="17" x2="17" y2="17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
          <rect x="5" y="5" width="3" height="10" rx="1" fill="currentColor"/>
          <rect x="12" y="8" width="3" height="7" rx="1" fill="currentColor"/>
        </template>
        <!-- Align Stretch Vertical -->
        <template v-else-if="opt.icon === 'align-stretch-v'">
          <line x1="3" y1="3" x2="17" y2="3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
          <line x1="3" y1="17" x2="17" y2="17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
          <rect x="5" y="5" width="3" height="10" rx="1" fill="currentColor"/>
          <rect x="12" y="5" width="3" height="10" rx="1" fill="currentColor"/>
        </template>
        <!-- Baseline -->
        <template v-else-if="opt.icon === 'align-baseline'">
          <line x1="2" y1="13" x2="18" y2="13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-dasharray="2 2"/>
          <rect x="5" y="4" width="3" height="9" rx="1" fill="currentColor"/>
          <rect x="12" y="7" width="3" height="6" rx="1" fill="currentColor"/>
        </template>
        <!-- Space Between Vertical -->
        <template v-else-if="opt.icon === 'space-between-v'">
          <rect x="6" y="2" width="8" height="4" rx="1" fill="currentColor"/>
          <rect x="6" y="14" width="8" height="4" rx="1" fill="currentColor"/>
          <line x1="10" y1="8" x2="10" y2="12" stroke="currentColor" stroke-width="1" stroke-dasharray="1.5 1.5" opacity="0.5"/>
        </template>
        <!-- Space Around Vertical -->
        <template v-else-if="opt.icon === 'space-around-v'">
          <rect x="6" y="4" width="8" height="4" rx="1" fill="currentColor"/>
          <rect x="6" y="12" width="8" height="4" rx="1" fill="currentColor"/>
        </template>
        <!-- Fallback: text -->
        <template v-else>
          <text x="10" y="14" text-anchor="middle" font-size="8" fill="currentColor">{{ opt.label.substring(0,3) }}</text>
        </template>
      </svg>
    </button>
  </div>
</template>

<script setup>
import { t } from '@/i18n';
defineProps({
  modelValue: { type: String, default: '' },
  options: { type: Array, default: () => [] },
});
defineEmits(['update:modelValue']);
</script>

<style scoped>
.olo-icon-select {
  display: flex;
  flex-wrap: wrap;
  gap: 3px;
}
.olo-is-btn {
  width: 30px;
  height: 26px;
  border: 1.5px solid #3a3a4a;
  border-radius: 5px;
  background: #2a2a35;
  color: #888;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 3px;
  transition: all 0.15s;
}
.olo-is-btn:hover {
  border-color: #555;
  color: #bbb;
  background: #333340;
}
.olo-is-btn--active {
  border-color: #6366f1;
  color: #a5b4fc;
  background: #2d2b55;
}
.olo-is-icon {
  width: 16px;
  height: 16px;
  display: block;
}
</style>
