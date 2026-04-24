<template>
  <div class="olo-tb-preview" :style="wrapStyle">
    <button type="button" class="olo-tb-btn" :style="btnStyle" @click="toggle" @mouseenter="hovered=true" @mouseleave="hovered=false">
      <span v-if="s.icon_position === 'left'" class="olo-tb-icon" v-html="currentIcon"></span>
      <span class="olo-tb-label">{{ currentText }}</span>
      <span v-if="s.icon_position === 'right'" class="olo-tb-icon" v-html="currentIcon"></span>
    </button>
    <div v-if="!s.target_id" style="color:#EF4444;font-size:12px;margin-top:6px;text-align:center;">
      {{ t('Imposta l\'ID della sezione target') }}
    </div>
  </div>
</template>

<script setup>
import { t } from '@/i18n';
import { ref, computed } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const s = computed(() => ({
  text_show: 'Mostra di più',
  text_hide: 'Mostra di meno',
  icon_show: 'chevron-down',
  icon_hide: 'chevron-up',
  icon_position: 'right',
  target_id: '',
  initial_state: 'hidden',
  btn_bg: 'transparent',
  btn_color: '#6366F1',
  btn_hover_bg: 'rgba(99,102,241,0.1)',
  btn_border_width: '2',
  btn_border_color: '#6366F1',
  btn_border_radius: '8',
  btn_padding_x: '24',
  btn_padding_y: '12',
  btn_font_size: '15',
  btn_font_weight: '600',
  btn_align: 'center',
  btn_full_width: false,
  ...props.settings,
}));

const isOpen = ref(false);
const hovered = ref(false);

// Sync initial state when settings change
const initialOpen = computed(() => s.value.initial_state === 'visible');

const currentText = computed(() => {
  const open = isOpen.value !== initialOpen.value ? !initialOpen.value : initialOpen.value;
  return (isOpen.value ? s.value.text_hide : s.value.text_show) || 'Toggle';
});

const iconMap = {
  'chevron-down': '<svg viewBox="0 0 24 24" style="width:1.1em;height:1.1em;fill:none;stroke:currentColor;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;"><polyline points="6 9 12 15 18 9"/></svg>',
  'chevron-up': '<svg viewBox="0 0 24 24" style="width:1.1em;height:1.1em;fill:none;stroke:currentColor;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;"><polyline points="6 15 12 9 18 15"/></svg>',
  'plus': '<svg viewBox="0 0 24 24" style="width:1.1em;height:1.1em;fill:none;stroke:currentColor;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>',
  'minus': '<svg viewBox="0 0 24 24" style="width:1.1em;height:1.1em;fill:none;stroke:currentColor;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;"><line x1="5" y1="12" x2="19" y2="12"/></svg>',
  'arrow-down': '<svg viewBox="0 0 24 24" style="width:1.1em;height:1.1em;fill:none;stroke:currentColor;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;"><line x1="12" y1="5" x2="12" y2="19"/><polyline points="19 12 12 19 5 12"/></svg>',
  'arrow-up': '<svg viewBox="0 0 24 24" style="width:1.1em;height:1.1em;fill:none;stroke:currentColor;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;"><line x1="12" y1="19" x2="12" y2="5"/><polyline points="5 12 12 5 19 12"/></svg>',
  'eye': '<svg viewBox="0 0 24 24" style="width:1.1em;height:1.1em;fill:none;stroke:currentColor;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>',
  'eye-off': '<svg viewBox="0 0 24 24" style="width:1.1em;height:1.1em;fill:none;stroke:currentColor;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/></svg>',
};

const currentIcon = computed(() => {
  const key = isOpen.value ? s.value.icon_hide : s.value.icon_show;
  return iconMap[key] || '';
});

function toggle() {
  isOpen.value = !isOpen.value;
}

const wrapStyle = computed(() => ({
  textAlign: s.value.btn_align || 'center',
}));

const btnStyle = computed(() => {
  const bw = parseInt(s.value.btn_border_width) || 0;
  const st = {
    display: 'inline-flex',
    alignItems: 'center',
    gap: '8px',
    background: hovered.value ? (s.value.btn_hover_bg || 'rgba(99,102,241,0.1)') : (s.value.btn_bg || 'transparent'),
    color: s.value.btn_color || '#6366F1',
    fontSize: (parseInt(s.value.btn_font_size) || 15) + 'px',
    fontWeight: s.value.btn_font_weight || '600',
    lineHeight: '1.2',
    padding: `${parseInt(s.value.btn_padding_y) || 12}px ${parseInt(s.value.btn_padding_x) || 24}px`,
    borderRadius: (parseInt(s.value.btn_border_radius) || 0) + 'px',
    cursor: 'pointer',
    transition: 'background 0.2s',
    userSelect: 'none',
    border: bw > 0 ? `${bw}px solid ${s.value.btn_border_color || '#6366F1'}` : 'none',
  };
  if (s.value.btn_full_width) {
    st.width = '100%';
    st.justifyContent = 'center';
  }
  return st;
});
</script>
