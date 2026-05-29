<template>
  <div>
    <!-- Trigger button: mostra preview + apri modal -->
    <button
      type="button"
      class="mb-w-full mb-flex mb-items-center mb-justify-between mb-gap-2 mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2.5 mb-py-2 mb-text-left mb-text-sm mb-text-gray-700 hover:mb-border-primary-500 hover:mb-bg-primary-50 mb-transition-colors"
      @click="open"
    >
      <span class="mb-flex-1 mb-min-w-0 mb-flex mb-flex-col mb-gap-0.5">
        <span v-if="previewLines.length === 0" class="mb-text-gray-400 mb-italic">{{ t('Nessun testo') }}</span>
        <span
          v-for="(line, i) in previewLines"
          :key="i"
          class="mb-truncate"
          :class="i === 0 ? 'mb-font-medium mb-text-gray-900' : 'mb-text-xs mb-text-gray-500'"
        >{{ line }}</span>
      </span>
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mb-flex-shrink-0 mb-text-gray-400">
        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
      </svg>
    </button>

    <!-- Modal -->
    <Teleport to="body">
      <transition name="cp-fade">
        <div
          v-if="visible"
          class="mb-fixed mb-inset-0 mb-z-[99999] mb-flex mb-items-center mb-justify-center"
          @keydown.escape="close"
        >
          <div class="mb-absolute mb-inset-0" style="background:rgba(0,0,0,0.35)" @click="close"></div>

          <div ref="panelRef" class="cp-panel" role="dialog" :aria-label="t(field.label)" tabindex="-1">
            <div class="cp-header">
              <span class="cp-title">{{ t(field.label || 'Modifica contenuto') }}</span>
              <button class="cp-close" @click="close" :aria-label="t('Chiudi')">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 6L6 18M6 6l12 12"/></svg>
              </button>
            </div>

            <div class="cp-body">
              <div v-for="(sub, idx) in field.fields || []" :key="sub.key || idx" class="cp-row">
                <label class="cp-field-label">{{ t(sub.label) }}</label>
                <InspectorField
                  :field="sub"
                  :modelValue="settings?.[sub.key]"
                  :tileSettings="settings || {}"
                  :tileId="tileId"
                  @update:modelValue="onFieldUpdate(sub.key, $event)"
                />
              </div>
            </div>

            <div class="cp-footer">
              <button class="cp-btn cp-btn--primary" @click="close">{{ t('Fatto') }}</button>
            </div>
          </div>
        </div>
      </transition>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, computed, nextTick, defineAsyncComponent } from 'vue';
import { t } from '@/i18n';

// Async import per evitare circular dependency con InspectorField
const InspectorField = defineAsyncComponent(() => import('@/components/Builder/InspectorField.vue'));

const props = defineProps({
  field: { type: Object, required: true },
  settings: { type: Object, default: () => ({}) },
  tileId: { type: String, default: '' },
});

const emit = defineEmits(['update:settingKey']);

const visible = ref(false);
const panelRef = ref(null);

const previewLines = computed(() => {
  const subs = props.field.fields || [];
  const lines = [];
  for (const sub of subs) {
    const v = props.settings?.[sub.key];
    if (typeof v === 'string' && v.trim()) {
      lines.push(v.trim().replace(/\s+/g, ' '));
    }
  }
  return lines.slice(0, 2);
});

function open() {
  visible.value = true;
  nextTick(() => panelRef.value?.focus());
}
function close() {
  visible.value = false;
}
function onFieldUpdate(key, value) {
  // Propaga al BuilderInspector via update:settingKey (esiste già il pattern)
  emit('update:settingKey', { key, value });
}
</script>

<style scoped>
.cp-panel {
  position: relative;
  width: 560px;
  max-width: 95vw;
  max-height: 80vh;
  display: flex;
  flex-direction: column;
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 25px 60px rgba(0,0,0,0.25), 0 0 0 1px rgba(0,0,0,0.08);
  overflow: hidden;
  animation: cp-slide-up 0.18s ease-out;
}
@keyframes cp-slide-up {
  from { opacity: 0; transform: translateY(16px) scale(0.97); }
  to   { opacity: 1; transform: translateY(0) scale(1); }
}
.cp-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 14px 18px 10px;
  border-bottom: 1px solid #e5e7eb;
}
.cp-title {
  font-size: 14px;
  font-weight: 600;
  color: #1a1a1a;
}
.cp-close {
  display: flex; align-items: center; justify-content: center;
  width: 26px; height: 26px;
  border-radius: 6px; border: none; background: none;
  color: #9CA3AF; cursor: pointer; transition: all 0.15s;
}
.cp-close:hover { background: #f3f4f6; color: #374151; }
.cp-body {
  padding: 14px 18px;
  overflow-y: auto;
  display: flex;
  flex-direction: column;
  gap: 14px;
}
.cp-row {
  display: flex;
  flex-direction: column;
  gap: 4px;
}
.cp-field-label {
  font-size: 11px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  color: #6B7280;
}
.cp-footer {
  display: flex;
  justify-content: flex-end;
  gap: 8px;
  padding: 12px 18px;
  border-top: 1px solid #e5e7eb;
}
.cp-btn {
  padding: 7px 16px;
  border-radius: 6px;
  border: 1px solid transparent;
  font-size: 13px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.15s;
}
.cp-btn--primary {
  background: var(--olo-color-primary, #6366F1);
  color: #fff;
}
.cp-btn--primary:hover { filter: brightness(1.08); }

.cp-fade-enter-active, .cp-fade-leave-active { transition: opacity 0.15s; }
.cp-fade-enter-from, .cp-fade-leave-to { opacity: 0; }
</style>
