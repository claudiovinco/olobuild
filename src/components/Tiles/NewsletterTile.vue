<template>
  <div class="mb-flex mb-justify-center">
    <div :style="boxStyle" class="mb-w-full mb-text-center">
      <!-- Icon -->
      <div v-if="s.icon_type === 'emoji'" class="mb-mb-3" :style="{ fontSize: s.icon_size + 'px' }">{{ s.icon_name }}</div>
      <div v-else-if="s.icon_type === 'image' && s.icon_image" class="mb-mb-3">
        <img :src="s.icon_image" :style="{ width: s.icon_size + 'px' }" class="mb-inline-block" />
      </div>

      <!-- Title -->
      <h3 v-if="s.title" :style="titleStyle" class="mb-mb-2 mb-leading-tight">{{ s.title }}</h3>
      <p v-if="s.subtitle" :style="subStyle" class="mb-mb-5 mb-leading-relaxed">{{ s.subtitle }}</p>

      <!-- Form -->
      <div :class="['mb-flex mb-gap-2', s.layout === 'horizontal' ? 'mb-flex-row mb-items-stretch' : 'mb-flex-col']">
        <input v-if="s.show_name" type="text" :placeholder="s.name_placeholder" :style="inputStyle" class="mb-flex-1 mb-min-w-0" disabled />
        <input type="email" :placeholder="s.email_placeholder" :style="inputStyle" class="mb-flex-1 mb-min-w-0" disabled />
        <button :style="btnStyle" class="mb-inline-flex mb-items-center mb-gap-1.5 mb-justify-center mb-whitespace-nowrap mb-cursor-default">
          {{ s.button_text }}
          <svg v-if="s.button_icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
        </button>
      </div>

      <!-- Privacy -->
      <div v-if="s.privacy_text" class="mb-mt-2 mb-text-[11px] mb-text-gray-400 mb-flex mb-items-start mb-gap-1 mb-justify-center">
        <input v-if="s.privacy_required" type="checkbox" disabled class="mb-mt-0.5" />
        <span v-html="s.privacy_text"></span>
      </div>

      <!-- Content Lock indicator -->
      <div v-if="s.content_lock" class="mb-mt-4 mb-p-3 mb-bg-amber-50 mb-border mb-border-amber-200 mb-rounded-md mb-text-xs mb-text-amber-700">
        {{ t('🔒 Content Lock attivo — il contenuto successivo sarà bloccato fino all\'iscrizione') }}
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });

const defaults = {
  layout: 'horizontal', title: 'Iscriviti alla newsletter',
  subtitle: 'Ricevi aggiornamenti e contenuti esclusivi.', icon_type: 'none', icon_name: '📧',
  icon_image: '', icon_size: '48', show_name: false, name_placeholder: 'Il tuo nome',
  email_placeholder: 'La tua email', button_text: 'Iscriviti', button_icon: true,
  privacy_text: '', privacy_required: false, content_lock: false,
  max_width: '600', bg_color: '', border_radius: 12, padding: '32',
  title_size: '24', title_weight: '700', title_color: '', subtitle_size: '14', subtitle_color: '',
  input_bg: '#ffffff', input_color: '#1F2937', input_border: '#D1D5DB', input_radius: 8, input_height: '44',
  btn_bg: '', btn_color: '#ffffff', btn_radius: 8, btn_font_size: '14', btn_font_weight: '600',
};

const s = computed(() => ({ ...defaults, ...props.settings }));

const boxStyle = computed(() => ({
  maxWidth: (s.value.max_width || 600) + 'px',
  background: s.value.bg_color || 'transparent',
  borderRadius: s.value.border_radius + 'px',
  padding: s.value.padding + 'px',
}));

const titleStyle = computed(() => ({
  fontSize: s.value.title_size + 'px',
  fontWeight: s.value.title_weight,
  color: s.value.title_color || 'inherit',
  margin: '0 0 8px',
}));

const subStyle = computed(() => ({
  fontSize: s.value.subtitle_size + 'px',
  color: s.value.subtitle_color || '#6B7280',
  margin: '0 0 20px',
}));

const inputStyle = computed(() => ({
  height: s.value.input_height + 'px',
  padding: '0 14px',
  background: s.value.input_bg,
  color: s.value.input_color,
  border: '1px solid ' + s.value.input_border,
  borderRadius: s.value.input_radius + 'px',
  fontSize: '14px',
}));

const btnStyle = computed(() => ({
  height: s.value.input_height + 'px',
  padding: '0 24px',
  background: s.value.btn_bg || 'var(--olo-color-primary, #3B82F6)',
  color: s.value.btn_color,
  border: 'none',
  borderRadius: s.value.btn_radius + 'px',
  fontSize: s.value.btn_font_size + 'px',
  fontWeight: s.value.btn_font_weight,
}));
</script>
