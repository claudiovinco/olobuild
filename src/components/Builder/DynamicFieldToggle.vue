<template>
  <div class="dft-wrap">
    <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1 mb-flex mb-items-center mb-gap-1">
      {{ field.label }}
      <button
        type="button"
        class="dft-btn"
        :class="{ 'dft-btn--active': hasDynamic }"
        :title="hasDynamic ? 'Collegamento dinamico attivo — clicca per modificare' : 'Collega a contenuto dinamico'"
        @click="togglePanel"
      >&#9889;</button>
    </label>

    <!-- Dynamic bound badge (replaces the static field) -->
    <div v-if="hasDynamic" class="dft-badge">
      <span class="dft-badge-icon">&#9889;</span>
      <span class="dft-badge-label">{{ bindingLabel }}</span>
      <button type="button" class="dft-badge-remove" :title="t('Rimuovi collegamento')" @click="removeDynamic">{{ t('&times;') }}</button>
    </div>

    <!-- Static field (hidden when dynamic binding active) -->
    <slot v-else />

    <!-- Binding panel -->
    <DynamicBindingPanel
      v-if="panelOpen"
      :binding="currentBinding"
      :fieldType="field.type"
      @select="onBindingSelect"
      @close="panelOpen = false"
    />
  </div>
</template>

<script setup>
import { t } from '@/i18n';
import { ref, computed, onMounted } from 'vue';
import { useDynamicContent } from '@/composables/useDynamicContent';
import DynamicBindingPanel from './DynamicBindingPanel.vue';

const props = defineProps({
  field: { type: Object, required: true },
  tileId: { type: String, required: true },
  dynamic: { type: Object, default: () => ({}) },
});

const emit = defineEmits(['update:dynamic']);

const { fetchSources, getBindingLabel } = useDynamicContent();
const panelOpen = ref(false);

onMounted(() => {
  fetchSources();
});

const currentBinding = computed(() => {
  return props.dynamic?.[props.field.key] || null;
});

const hasDynamic = computed(() => {
  const b = currentBinding.value;
  return b && b.source && b.field;
});

const bindingLabel = computed(() => {
  return getBindingLabel(currentBinding.value);
});

function togglePanel() {
  panelOpen.value = !panelOpen.value;
}

function onBindingSelect(binding) {
  emit('update:dynamic', { [props.field.key]: binding });
  panelOpen.value = false;
}

function removeDynamic() {
  const updated = { ...props.dynamic };
  delete updated[props.field.key];
  emit('update:dynamic', updated, true);
}
</script>

<style scoped>
.dft-wrap {
  position: relative;
}

.dft-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 18px;
  height: 18px;
  border: none;
  border-radius: 3px;
  background: transparent;
  color: #9CA3AF;
  font-size: 11px;
  cursor: pointer;
  padding: 0;
  line-height: 1;
  transition: color 0.15s, background 0.15s;
}

.dft-btn:hover {
  color: #f6a06b;
  background: rgb(var(--olo-primary-rgb, 232 98 42) / 0.15);
}

.dft-btn--active {
  color: var(--olo-ui-accent, #e8622a);
  background: rgb(var(--olo-primary-rgb, 232 98 42) / 0.2);
}

.dft-badge {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 6px 8px;
  background: rgb(var(--olo-primary-rgb, 232 98 42) / 0.12);
  border: 1px solid rgb(var(--olo-primary-rgb, 232 98 42) / 0.3);
  border-radius: 6px;
  font-size: 11px;
  color: #c7d2fe;
}

.dft-badge-icon {
  font-size: 12px;
  flex-shrink: 0;
}

.dft-badge-label {
  flex: 1;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  min-width: 0;
}

.dft-badge-remove {
  width: 16px;
  height: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  border: none;
  border-radius: 3px;
  background: transparent;
  color: #9ca3af;
  font-size: 14px;
  cursor: pointer;
  flex-shrink: 0;
  padding: 0;
}

.dft-badge-remove:hover {
  background: #dc2626;
  color: #fff;
}

</style>
