<template>
  <div class="mb-w-48 mb-bg-gray-900 mb-border-r mb-border-gray-700 mb-flex mb-flex-col mb-overflow-hidden">
    <div class="mb-p-2 mb-border-b mb-border-gray-700 mb-text-xs mb-font-semibold mb-text-gray-300 mb-flex mb-items-center mb-justify-between">
      <span>Slides ({{ slides.length }})</span>
      <div class="mb-flex mb-items-center mb-gap-1">
        <button @click="doExport" class="mb-text-gray-400 hover:mb-text-primary-300" :title="t('Esporta slides')">&#8595;</button>
        <button @click="triggerImport" class="mb-text-gray-400 hover:mb-text-primary-300" :title="t('Importa slides')">&#8593;</button>
        <button @click="$emit('add')" class="mb-text-primary-400 hover:mb-text-primary-300" :title="t('Aggiungi slide')">+</button>
      </div>
      <input ref="importInput" type="file" accept=".json" class="mb-hidden" @change="handleImport" />
    </div>
    <div class="mb-flex-1 mb-overflow-y-auto mb-p-2 mb-space-y-2">
      <div
        v-for="(slide, idx) in slides"
        :key="slide.id"
        @click="$emit('select', idx)"
        :class="[
          'mb-rounded mb-overflow-hidden mb-cursor-pointer mb-border-2 mb-transition-colors mb-relative mb-group',
          idx === activeIndex ? 'mb-border-primary-500' : 'mb-border-gray-700 hover:mb-border-gray-500'
        ]"
      >
        <!-- Thumbnail -->
        <div class="mb-h-20 mb-bg-gray-800" :style="thumbStyle(slide)">
          <span class="mb-absolute mb-top-1 mb-left-1 mb-text-[10px] mb-bg-black/60 mb-text-white mb-px-1.5 mb-rounded">{{ idx + 1 }}</span>
          <span v-if="slide.persistFor > 0" class="mb-absolute mb-bottom-1 mb-left-1 mb-text-[9px] mb-bg-primary-600/80 mb-text-white mb-px-1 mb-rounded" :title="t('Persiste per') + ' ' + slide.persistFor + ' ' + t('slide')">P{{ slide.persistFor }}</span>
        </div>
        <!-- Actions -->
        <div class="mb-absolute mb-top-1 mb-right-1 mb-flex mb-gap-1 mb-opacity-0 group-hover:mb-opacity-100 mb-transition-opacity">
          <button @click.stop="$emit('duplicate', idx)" class="mb-text-[10px] mb-bg-black/60 mb-text-white mb-px-1 mb-rounded" :title="t('Duplica')">D</button>
          <button @click.stop="$emit('remove', idx)" class="mb-text-[10px] mb-bg-red-600/80 mb-text-white mb-px-1 mb-rounded" :title="t('Elimina')">&times;</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { t } from '@/i18n';

const props = defineProps({
  slides: { type: Array, required: true },
  activeIndex: { type: Number, default: 0 },
  globalBackground: { type: Object, default: null },
});

const emit = defineEmits(['select', 'add', 'duplicate', 'remove', 'import-slides']);

const importInput = ref(null);

function doExport() {
  const payload = {
    olo_export: 'proslider',
    version: (window.oloData || {}).pluginVersion || '1.22.0',
    slides: JSON.parse(JSON.stringify(props.slides)),
    globalBackground: props.globalBackground ? JSON.parse(JSON.stringify(props.globalBackground)) : {},
  };
  const blob = new Blob([JSON.stringify(payload, null, 2)], { type: 'application/json' });
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = 'proslider-export.json';
  a.click();
  URL.revokeObjectURL(url);
}

function triggerImport() {
  importInput.value?.click();
}

async function handleImport(e) {
  const file = e.target.files?.[0];
  if (!file) return;
  e.target.value = '';

  try {
    const text = await file.text();
    const json = JSON.parse(text);

    if (json.olo_export !== 'proslider' || !Array.isArray(json.slides)) {
      alert(t('File non valido: non è un export ProSlider.'));
      return;
    }

    emit('import-slides', json);
  } catch (err) {
    console.error('ProSlider import error:', err);
    alert(t("Errore durante l'importazione:") + ' ' + err.message);
  }
}

function thumbStyle(slide) {
  const bg = slide.background || {};
  if (bg.type === 'transparent') {
    return { background: 'repeating-conic-gradient(#808080 0% 25%, #c0c0c0 0% 50%) 0 0 / 16px 16px' };
  }
  if (bg.type === 'image' && bg.image) {
    return { backgroundImage: `url(${bg.image})`, backgroundSize: 'cover', backgroundPosition: 'center' };
  }
  if (bg.type === 'gradient') {
    return { background: `linear-gradient(${bg.gradientAngle || 180}deg, ${bg.gradientFrom || '#1e293b'}, ${bg.gradientTo || '#0f172a'})` };
  }
  return { background: bg.color || '#1e293b' };
}
</script>
