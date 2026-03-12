<template>
  <div :style="wrapStyle">
    <!-- Click mode: mostra il pulsante -->
    <template v-if="s.popup_trigger === 'click' || !s.popup_trigger">
      <button type="button" :style="btnStyle">
        <span v-if="s.button_icon && iconSvg" class="olo-popup-icon" :style="iconStyle" v-html="iconSvg"></span>
        <span data-olo-editable="button_text">{{ s.button_text || 'Apri' }}</span>
      </button>
    </template>
    <!-- Auto trigger: mostra badge informativo -->
    <template v-else>
      <div :style="triggerBadgeStyle">
        <div :style="triggerIconRowStyle">
          <span :style="triggerIconStyle">{{ triggerInfo.icon }}</span>
          <span :style="triggerLabelStyle">{{ triggerInfo.label }}</span>
        </div>
        <div v-if="triggerInfo.detail" :style="triggerDetailStyle">{{ triggerInfo.detail }}</div>
        <div v-if="hasLimits" :style="triggerDetailStyle">{{ limitsLabel }}</div>
      </div>
    </template>

    <!-- Mini preview del popup -->
    <div style="margin-top:12px;border:1px dashed #4b5563;border-radius:8px;padding:16px 20px;background:rgba(30,27,75,0.3);max-width:320px;position:relative;">
      <div style="position:absolute;top:-8px;left:12px;background:#1e1b4b;padding:0 6px;font-size:9px;color:#a5b4fc;font-weight:600;border-radius:3px;">POPUP CONTENT</div>
      <div style="font-size:11px;color:#c7d2fe;opacity:0.7;text-align:center;padding:8px 0;">
        {{ s.mode === 'simple' ? 'Contenuto popup (children tiles)' : 'Template popup' }}
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import iconsSvg from '../ProSlider/uikitIconsSvg.js';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const s = computed(() => ({
  button_text: 'Apri',
  button_style: 'default',
  button_size: '',
  button_icon: '',
  button_fullwidth: false,
  mode: 'simple',
  popup_trigger: 'click',
  timer_delay: '5',
  scroll_percent: '50',
  inactivity_delay: '30',
  show_max_times: '0',
  show_once_per_session: false,
  ...props.settings,
}));

const iconSvg = computed(() => iconsSvg[s.value.button_icon] || '');

const styleMap = {
  default:   { bg: '#222', color: '#fff', border: 'none' },
  primary:   { bg: 'var(--olo-color-primary, #6366F1)', color: '#fff', border: 'none' },
  secondary: { bg: '#e5e7eb', color: '#222', border: 'none' },
  danger:    { bg: '#dc2626', color: '#fff', border: 'none' },
  text:      { bg: 'transparent', color: '#999', border: 'none', textDecoration: 'none' },
  link:      { bg: 'transparent', color: 'var(--olo-color-primary, #6366F1)', border: 'none', textDecoration: 'none' },
};

const sizeMap = {
  '':      { padding: '8px 24px', fontSize: '14px' },
  small:   { padding: '5px 16px', fontSize: '12px' },
  large:   { padding: '12px 32px', fontSize: '16px' },
};

const wrapStyle = computed(() => {
  if (s.value.button_fullwidth) {
    return { display: 'block' };
  }
  return { display: 'inline-block' };
});

const btnStyle = computed(() => {
  const st = styleMap[s.value.button_style] || styleMap.default;
  const sz = sizeMap[s.value.button_size] || sizeMap[''];
  const base = {
    display: 'inline-flex',
    alignItems: 'center',
    gap: '6px',
    background: st.bg,
    color: st.color,
    border: st.border,
    padding: sz.padding,
    fontSize: sz.fontSize,
    fontWeight: '500',
    lineHeight: '1.4',
    borderRadius: '2px',
    cursor: 'default',
    textTransform: 'uppercase',
    letterSpacing: '0.5px',
    fontFamily: 'inherit',
  };
  if (st.textDecoration) base.textDecoration = st.textDecoration;
  if (s.value.button_fullwidth) base.width = '100%';
  return base;
});

const iconStyle = computed(() => {
  const sz = s.value.button_size === 'small' ? '14px' : s.value.button_size === 'large' ? '18px' : '16px';
  return { display: 'inline-flex', width: sz, height: sz, flexShrink: '0' };
});

/* --- Trigger info per la preview nel builder --- */

const triggerInfo = computed(() => {
  const t = s.value.popup_trigger;
  if (t === 'page_load') {
    const d = parseInt(s.value.timer_delay) || 0;
    return {
      icon: '\u23F1',
      label: 'Caricamento pagina',
      detail: d > 0 ? ('Ritardo: ' + d + 's') : 'Immediato',
    };
  }
  if (t === 'exit_intent') {
    return {
      icon: '\u21A9',
      label: 'Exit Intent',
      detail: 'Si apre quando il mouse esce dalla pagina',
    };
  }
  if (t === 'scroll') {
    return {
      icon: '\u2193',
      label: 'Scroll ' + (parseInt(s.value.scroll_percent) || 50) + '%',
      detail: 'Si apre al raggiungimento della percentuale',
    };
  }
  if (t === 'inactivity') {
    return {
      icon: '\u23F3',
      label: 'Inattivit\u00E0 utente',
      detail: (parseInt(s.value.inactivity_delay) || 30) + ' secondi senza attivit\u00E0',
    };
  }
  if (t === 'timer') {
    return {
      icon: '\u23F0',
      label: 'Timer',
      detail: (parseInt(s.value.timer_delay) || 5) + ' secondi',
    };
  }
  return { icon: '\u{1F5B1}', label: 'Click', detail: '' };
});

const hasLimits = computed(() => {
  const max = parseInt(s.value.show_max_times) || 0;
  return max > 0 || s.value.show_once_per_session;
});

const limitsLabel = computed(() => {
  const parts = [];
  const max = parseInt(s.value.show_max_times) || 0;
  if (max > 0) parts.push('Max ' + max + 'x');
  if (s.value.show_once_per_session) parts.push('1x/sessione');
  return parts.join(' \u2022 ');
});

const triggerBadgeStyle = computed(() => ({
  display: 'inline-flex',
  flexDirection: 'column',
  gap: '4px',
  background: '#1e1b4b',
  color: '#c7d2fe',
  border: '1px dashed #6366f1',
  borderRadius: '8px',
  padding: '12px 18px',
  fontSize: '12px',
  lineHeight: '1.4',
  fontFamily: 'system-ui, sans-serif',
  maxWidth: '280px',
}));

const triggerIconRowStyle = computed(() => ({
  display: 'flex',
  alignItems: 'center',
  gap: '6px',
}));

const triggerIconStyle = computed(() => ({
  fontSize: '16px',
  flexShrink: '0',
}));

const triggerLabelStyle = computed(() => ({
  fontWeight: '600',
  color: '#e0e7ff',
  fontSize: '13px',
}));

const triggerDetailStyle = computed(() => ({
  color: '#a5b4fc',
  fontSize: '11px',
  paddingLeft: '22px',
}));
</script>

<style scoped>
.olo-popup-icon :deep(svg) {
  width: 100%;
  height: 100%;
  fill: currentColor;
  stroke: currentColor;
}
</style>
