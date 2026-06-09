<template>
  <div class="olo-annbar oab" :style="rootStyle">
    <component :is="link ? 'a' : 'span'" :href="link || undefined" class="oab-link" style="color:inherit;text-decoration:none">{{ s.text }}<b v-if="s.accent_text" :style="{ color: accentColor, fontWeight: s.font_weight || '500' }">{{ s.accent_text }}</b></component>
    <button v-if="s.dismissible" class="oab-close" type="button" aria-label="Close" :style="closeStyle">&times;</button>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { buildBgStyle } from '@/composables/useBackgroundStyle';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });

const defaults = {
  text: 'Complimentary shipping & returns worldwide · ', accent_text: 'The Nocturne collection has arrived',
  link_url: '', dismissible: false, bg_color: '', text_color: '', accent_color: '',
  font_size: '11', font_weight: '500', letter_spacing: '0.2em', text_transform: 'uppercase', alignment: 'center',
  tile_padding: { top: 10, right: 20, bottom: 10, left: 20 }, border_bottom: '0', border_color: '', bg: { type: 'none' },
};

const s = computed(() => ({ ...defaults, ...props.settings }));
const link = computed(() => (s.value.link_url || '').trim());
const accentColor = computed(() => s.value.accent_color || 'var(--olo-color-primary, #e1474f)');
const SANS = "var(--olo-font-family, -apple-system, sans-serif)";

const kitBg = computed(() => {
  const bg = s.value.bg;
  if (bg && bg.type && bg.type !== 'none') return buildBgStyle(bg);
  return { background: s.value.bg_color || 'var(--olo-color-text, #0c0c0c)' };
});

const rootStyle = computed(() => {
  const cp = s.value.tile_padding || {};
  const st = {
    ...kitBg.value,
    color: s.value.text_color || 'var(--olo-color-secondary, #efe9de)',
    textAlign: s.value.alignment || 'center',
    fontFamily: SANS,
    fontSize: (parseInt(s.value.font_size, 10) || 11) + 'px',
    fontWeight: s.value.font_weight || '500',
    letterSpacing: s.value.letter_spacing || '0.2em',
    textTransform: s.value.text_transform || 'uppercase',
    padding: `${parseInt(cp.top, 10) || 10}px ${parseInt(cp.right, 10) || 20}px ${parseInt(cp.bottom, 10) || 10}px ${parseInt(cp.left, 10) || 20}px`,
    position: 'relative', lineHeight: 1.4,
  };
  const bb = parseInt(s.value.border_bottom, 10) || 0;
  if (bb > 0) st.borderBottom = `${bb}px solid ${s.value.border_color || 'rgba(239,233,222,.12)'}`;
  return st;
});

const closeStyle = { position: 'absolute', right: '14px', top: '50%', transform: 'translateY(-50%)', background: 'none', border: 0, color: 'inherit', cursor: 'pointer', fontSize: '16px', lineHeight: 1, opacity: 0.6 };
</script>
