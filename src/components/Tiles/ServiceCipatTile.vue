<template>
  <div :style="wrapStyle">
    <!-- Icon -->
    <span v-if="s.show_icon" style="display:flex;align-items:center;flex-shrink:0">
      <svg :width="iconSz" :height="iconSz" viewBox="0 0 24 24" fill="none">
        <rect x="3" y="3" width="18" height="18" rx="3" :stroke="iconC" stroke-width="1.8"/>
        <path d="M7 8h10M7 12h6" :stroke="iconC" stroke-width="1.5" stroke-linecap="round"/>
        <circle cx="16.5" cy="15.5" r="2.5" :stroke="iconC" stroke-width="1.5"/>
        <path d="M15.5 17.5L15 20l1.5-1 1.5 1-.5-2.5" :stroke="iconC" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </span>
    <!-- Text -->
    <span :style="textWrapStyle">
      <span v-if="s.show_label" :style="labelStyle">{{ s.label_text || 'Codice CIPAT' }}</span>
      <span :style="codeStyle">{{ t('CIPAT-012345-AT') }}</span>
    </span>
  </div>
</template>

<script setup>
import { t } from '@/i18n';
import { computed } from 'vue';
const props = defineProps({ settings: { type: Object, default: () => ({}) } });

const defaults = {
  layout: 'inline', show_icon: true, show_label: true, label_text: 'Codice CIPAT',
  font_size: '14', code_weight: '600',
  label_color: '', code_color: '', icon_color: '',
  bg_color: '', border_color: '', border_radius: '8', padding: '12', align: 'left',
};
const s = computed(() => ({ ...defaults, ...props.settings }));
const iconC = computed(() => s.value.icon_color || '#6366F1');

const iconSz = computed(() => Math.min(parseInt(s.value.font_size) || 14, 18) + 2);
const isBlock = computed(() => s.value.layout === 'block');

const alignMap = { left: 'flex-start', center: 'center', right: 'flex-end' };

const wrapStyle = computed(() => {
  const st = {
    display: 'flex',
    gap: isBlock.value ? '3px' : '6px',
  };
  if (isBlock.value) {
    st.flexDirection = 'column';
    st.alignItems = alignMap[s.value.align] || 'flex-start';
  } else {
    st.alignItems = 'center';
    st.justifyContent = alignMap[s.value.align] || 'flex-start';
  }
  if (s.value.bg_color) st.background = s.value.bg_color;
  if (s.value.border_color) st.border = '1px solid ' + s.value.border_color;
  if (parseInt(s.value.border_radius)) st.borderRadius = s.value.border_radius + 'px';
  if (parseInt(s.value.padding)) st.padding = s.value.padding + 'px';
  return st;
});

const textWrapStyle = computed(() => ({
  display: 'flex', flexDirection: 'column', gap: '1px',
}));

const labelStyle = computed(() => ({
  fontSize: Math.max(8, Math.min(parseInt(s.value.font_size) || 14, 14) - 3) + 'px',
  color: s.value.label_color || 'var(--olo-color-text-muted, #9CA3AF)', fontWeight: '500',
  textTransform: 'uppercase', letterSpacing: '0.5px', lineHeight: '1.2',
}));

const codeStyle = computed(() => ({
  fontSize: Math.min(parseInt(s.value.font_size) || 14, 18) + 'px',
  fontWeight: s.value.code_weight, color: s.value.code_color || 'var(--olo-color-text, #374151)',
  letterSpacing: '0.3px', lineHeight: '1.3',
}));
</script>
