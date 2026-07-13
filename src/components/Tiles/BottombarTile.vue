<template>
  <div class="olo-bottombar-preview" :style="barStyle">
    <span v-if="s.content_html" v-html="s.content_html"></span>
    <span v-else class="ph">{{ placeholder }}</span>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';
import def from '@/config/elements/bottombar.js';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const s = computed(() => ({ ...def.defaults, ...props.settings }));
const placeholder = t('Barra fissa in basso — inserisci il contenuto');

// Nel canvas la barra resta in flusso: fixed solo sul frontend.
const barStyle = computed(() => ({
  background: s.value.bg_color || 'var(--olo-color-surface, rgba(12,14,19,.92))',
  color: s.value.text_color || 'var(--olo-color-text-muted, #8B90A0)',
  textAlign: s.value.align || 'center',
  padding: `${s.value.padding_y || 14}px 24px`,
  fontFamily: 'var(--olo-font-family-mono, monospace)',
  fontSize: `${s.value.font_size || 11}px`,
  letterSpacing: `${s.value.letter_spacing ?? 2}px`,
  textTransform: s.value.uppercase ? 'uppercase' : 'none',
  lineHeight: 1.4,
  borderTop: s.value.border_top
    ? `1px solid ${s.value.border_color || 'var(--olo-color-border, rgba(250,247,242,.14))'}`
    : 'none',
}));
</script>

<style scoped>
.olo-bottombar-preview :deep(a){color:var(--olo-color-heading, #FAF7F2);text-decoration:none;font-weight:600;}
.olo-bottombar-preview .ph{opacity:.6;font-style:italic;}
</style>
