<template>
  <div class="mb-flex mb-justify-center olo-newsletter" :style="rootStyle">
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
        <button type="button" :style="btnStyle" class="mb-inline-flex mb-items-center mb-gap-1.5 mb-justify-center mb-whitespace-nowrap mb-cursor-default">
          {{ s.button_text }}
          <svg v-if="s.button_icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
        </button>
      </div>

      <!-- Privacy -->
      <div v-if="s.privacy_text" class="mb-mt-2 mb-text-[11px] mb-flex mb-items-start mb-gap-1 mb-justify-center" :style="{ color: s.privacy_color || TOKENS.textFaint }">
        <input v-if="s.privacy_required" type="checkbox" disabled class="mb-mt-0.5" />
        <span v-html="s.privacy_text"></span>
      </div>

      <!-- Content Lock indicator -->
      <div v-if="s.content_lock" class="mb-mt-4 mb-p-3 mb-rounded-md mb-text-xs" :style="lockBadgeStyle">
        <span class="olo-newsletter-lock-ico" v-html="lockSvg"></span>
        {{ t('Content Lock attivo — il contenuto successivo sarà bloccato fino all\'iscrizione') }}
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';
import { TOKENS } from '@/composables/oloTileDefaults';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });

// Icona lucchetto SVG (sostituisce l'emoji 🔒 nell'hint del builder)
const lockSvg = '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>';

const defaults = {
  layout: 'horizontal', title: 'Iscriviti alla newsletter',
  subtitle: 'Ricevi aggiornamenti e contenuti esclusivi.', icon_type: 'none', icon_name: '📧',
  icon_image: '', icon_size: '48', show_name: false, name_placeholder: 'Il tuo nome',
  email_placeholder: 'La tua email', button_text: 'Iscriviti', button_icon: true,
  privacy_text: '', privacy_required: false, content_lock: false,
  max_width: '600', bg_color: '', box_border: '', border_radius: 12, padding: '32',
  title_size: '24', title_weight: '700', title_color: '', subtitle_size: '14', subtitle_color: '',
  input_bg: '#ffffff', input_color: '#1F2937', input_placeholder_color: '', input_border: '#D1D5DB', input_radius: 8, input_height: '44',
  btn_bg: '', btn_color: '#ffffff', btn_radius: 8, btn_font_size: '14', btn_font_weight: '600',
  privacy_color: '',
};

const s = computed(() => ({ ...defaults, ...props.settings }));

// Colore placeholder via CSS variable (il ::placeholder non è inline-style-abile)
const rootStyle = computed(() => (
  s.value.input_placeholder_color ? { '--olo-nl-ph': s.value.input_placeholder_color } : {}
));

const boxStyle = computed(() => ({
  maxWidth: (s.value.max_width || 600) + 'px',
  background: s.value.bg_color || 'transparent',
  border: s.value.box_border ? '1px solid ' + s.value.box_border : 'none',
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
  // TOKEN-FIRST: pulsante → primary (era fallback #3B82F6 off-brand)
  background: s.value.btn_bg || 'var(--olo-color-primary, #e1474f)',
  color: s.value.btn_color,
  border: 'none',
  borderRadius: s.value.btn_radius + 'px',
  fontSize: s.value.btn_font_size + 'px',
  fontWeight: s.value.btn_font_weight,
  cursor: 'pointer',
}));

// Badge "content lock": token warning semantico invece di amber hardcoded
const lockBadgeStyle = computed(() => ({
  background: 'color-mix(in srgb, var(--olo-color-warning, #b45309) 12%, #fff)',
  border: '1px solid color-mix(in srgb, var(--olo-color-warning, #b45309) 35%, #fff)',
  color: 'var(--olo-color-warning, #b45309)',
  display: 'flex',
  alignItems: 'center',
  gap: '6px',
  justifyContent: 'center',
}));
</script>

<style scoped>
.olo-newsletter-lock-ico { display: inline-flex; }
.olo-newsletter-lock-ico :deep(svg) { display: block; }
/* a11y: focus su input (bordo primary) e pulsante */
.olo-newsletter input:focus {
  outline: none;
  border-color: var(--olo-color-primary, #e1474f);
  box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent);
}
.olo-newsletter button:focus-visible {
  outline: none;
  box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent);
}
/* Colore placeholder configurabile (var impostata solo se valorizzata) */
.olo-newsletter input::placeholder { color: var(--olo-nl-ph); opacity: 1; }
</style>
