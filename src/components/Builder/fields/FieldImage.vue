<template>
  <div class="fm">
    <div v-if="modelValue" class="fm-preview">
      <img :src="modelValue" alt="" class="fm-media" />
      <button type="button" class="fm-remove" :title="t('Rimuovi immagine')" @click="clear">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M6 6l12 12M18 6L6 18"/></svg>
      </button>
    </div>

    <button type="button" class="fm-pick" @click="pickImage">
      <span class="fm-pick-ico" v-html="imageIcon"></span>
      <span>{{ modelValue ? t('Cambia immagine') : t('Seleziona immagine') }}</span>
    </button>
  </div>
</template>

<script setup>
import { t } from '@/i18n';
import { useMediaPicker } from '@/composables/useMediaPicker';

const props = defineProps({
  modelValue: { type: String, default: '' },
});
const emit = defineEmits(['update:modelValue', 'update:attachmentId']);

const { openSingleImage } = useMediaPicker();

// Icona SVG immagine (coerente con FieldMedia / resto dell'inspector).
const imageIcon = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2.5"/><circle cx="8.5" cy="8.5" r="1.6"/><path d="M21 15l-4.5-4.5L5 21"/></svg>';

function clear() {
  emit('update:modelValue', '');
  emit('update:attachmentId', 0);
}

function pickImage() {
  openSingleImage(({ url, id }) => {
    emit('update:modelValue', url);
    emit('update:attachmentId', id);
  });
}
</script>

<style scoped>
.fm {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.fm-preview {
  position: relative;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  overflow: hidden;
  background: #f9fafb;
}
.fm-media {
  display: block;
  width: 100%;
  height: 104px;
  object-fit: cover;
  background: #f3f4f6;
}

.fm-remove {
  position: absolute;
  top: 6px;
  right: 6px;
  width: 22px;
  height: 22px;
  padding: 0;
  border: none;
  border-radius: 50%;
  background: rgba(17, 24, 39, 0.55);
  color: #fff;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transition: opacity 0.15s, background 0.15s;
}
.fm-preview:hover .fm-remove { opacity: 1; }
.fm-remove:hover { background: #ef4444; }
.fm-remove svg { width: 13px; height: 13px; }
.fm-remove:focus-visible {
  opacity: 1;
  outline: 2px solid var(--olo-ui-accent, #e8622a);
  outline-offset: 2px;
}

.fm-pick {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  width: 100%;
  padding: 8px 12px;
  background: #fff;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 12.5px;
  font-weight: 600;
  color: #374151;
  cursor: pointer;
  transition: border-color 0.15s, color 0.15s, background 0.15s;
}
.fm-pick:hover {
  border-color: var(--olo-ui-accent, #e8622a);
  color: var(--olo-ui-accent, #e8622a);
}
.fm-pick:focus-visible {
  outline: 2px solid var(--olo-ui-accent, #e8622a);
  outline-offset: 1px;
}
.fm-pick-ico { flex: none; display: inline-flex; }
.fm-pick-ico :deep(svg) { width: 16px; height: 16px; }
</style>
