<template>
  <div class="olo-search-tile" :style="wrapperStyle">
    <div :style="formStyle" class="olo-search-form-wrap">
      <!-- Icon left -->
      <span v-if="s.show_icon !== false && iconPos === 'left'" :style="iconStyle">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" :stroke="iconColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="11" cy="11" r="8"/>
          <path d="m21 21-4.35-4.35"/>
        </svg>
      </span>

      <!-- Input -->
      <input
        type="text"
        :placeholder="displayPlaceholder"
        :style="inputStyle"
        disabled
      />

      <!-- Icon right -->
      <span v-if="s.show_icon !== false && iconPos === 'right' && !s.show_button" :style="iconStyle">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" :stroke="iconColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="11" cy="11" r="8"/>
          <path d="m21 21-4.35-4.35"/>
        </svg>
      </span>

      <!-- Button -->
      <button v-if="s.show_button" :style="buttonStyle">
        <svg v-if="btnStyle === 'icon-only'" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="11" cy="11" r="8"/>
          <path d="m21 21-4.35-4.35"/>
        </svg>
        <template v-else><span data-olo-editable="button_text">{{ s.button_text || 'Cerca' }}</span></template>
      </button>
    </div>

    <!-- Animated placeholder words preview -->
    <div v-if="s.animated_placeholder && animWords.length" style="margin-top:6px;display:flex;gap:4px;flex-wrap:wrap;justify-content:center;">
      <span v-for="(w, i) in animWords" :key="i" class="olo-search-word">{{ w }}</span>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, onMounted, onBeforeUnmount, watch } from 'vue';
import { resolveColor, TOKENS } from '@/composables/oloTileDefaults';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  placeholder: 'Cerca...',
  style: 'default',
  size: 'medium',
  show_icon: true,
  icon_position: 'left',
  show_button: false,
  button_text: 'Cerca',
  button_style: 'filled',
  full_width: true,
  max_width: '',
  alignment: 'center',
  bg_color: '#FFFFFF',
  text_color: '#374151',
  placeholder_color: '#9CA3AF',
  icon_color: '#6B7280',
  border_color: '#E5E7EB',
  border_width: '1',
  border_radius: '8',
  focus_border_color: '',   // '' ⇒ primary (era #e1474f off-brand)
  button_bg: '',            // '' ⇒ primary (era #e1474f off-brand)
  button_color: '#FFFFFF',
  button_radius: '8',
  input_shadow: false,
  focus_shadow: true,
  animated_placeholder: false,
  placeholder_words: '',
};

const s = computed(() => ({ ...defaults, ...props.settings }));

const iconPos = computed(() => s.value.icon_position || 'left');
const btnStyle = computed(() => s.value.button_style || 'filled');

// TOKEN-FIRST: icona neutra via token text-soft; pulsante/focus via primary
const iconColor = computed(() => resolveColor(s.value.icon_color, TOKENS.textSoft));
const accentColor = computed(() => resolveColor(s.value.button_bg, 'var(--olo-color-primary, #e1474f)'));

const animWords = computed(() =>
  (s.value.placeholder_words || '').split('\n').map(w => w.trim()).filter(Boolean)
);

// Animated placeholder
const currentPlaceholder = ref('');
let animTimer = null;
let wordIdx = 0;

function startPlaceholderAnim() {
  stopPlaceholderAnim();
  if (!s.value.animated_placeholder || !animWords.value.length) {
    currentPlaceholder.value = '';
    return;
  }
  wordIdx = 0;
  currentPlaceholder.value = animWords.value[0];
  animTimer = setInterval(() => {
    wordIdx = (wordIdx + 1) % animWords.value.length;
    currentPlaceholder.value = animWords.value[wordIdx];
  }, 2500);
}
function stopPlaceholderAnim() {
  if (animTimer) { clearInterval(animTimer); animTimer = null; }
}

onMounted(startPlaceholderAnim);
onBeforeUnmount(stopPlaceholderAnim);
watch(() => [s.value.animated_placeholder, s.value.placeholder_words], startPlaceholderAnim);

const displayPlaceholder = computed(() => {
  if (s.value.animated_placeholder && currentPlaceholder.value) {
    return currentPlaceholder.value;
  }
  return s.value.placeholder || 'Cerca...';
});

// Sizes
const sizeMap = { small: { fontSize: '13px', padding: '8px 12px', iconSize: 16 }, medium: { fontSize: '15px', padding: '12px 16px', iconSize: 20 }, large: { fontSize: '18px', padding: '16px 20px', iconSize: 22 } };
const sz = computed(() => sizeMap[s.value.size] || sizeMap.medium);

// Styles
const radius = computed(() => {
  const st = s.value.style;
  if (st === 'pill') return '50px';
  if (st === 'hero') return '16px';
  return ((v => isNaN(v) ? 8 : v)(parseInt(s.value.border_radius))) + 'px';
});

const borderW = computed(() => {
  if (s.value.style === 'underline') return '0';
  return (parseInt(s.value.border_width) || 1) + 'px';
});

const wrapperStyle = computed(() => {
  const st = {};
  if (!s.value.full_width && s.value.max_width) {
    st.maxWidth = parseInt(s.value.max_width) + 'px';
  }
  const alignMap = { left: 'flex-start', center: 'center', right: 'flex-end' };
  if (!s.value.full_width) {
    st.display = 'flex';
    st.justifyContent = alignMap[s.value.alignment] || 'center';
  }
  return st;
});

const formStyle = computed(() => {
  const st = {
    display: 'flex',
    alignItems: 'center',
    gap: '0',
    borderRadius: radius.value,
    overflow: 'hidden',
    transition: 'all 0.2s ease',
  };

  const style = s.value.style;
  if (style === 'underline') {
    st.background = 'transparent';
    st.border = 'none';
    st.borderBottom = `2px solid ${s.value.border_color || '#E5E7EB'}`;
    st.borderRadius = '0';
  } else if (style === 'minimal') {
    st.background = 'transparent';
    st.border = `${borderW.value} solid ${s.value.border_color || '#E5E7EB'}`;
  } else if (style === 'floating') {
    st.background = s.value.bg_color || '#FFFFFF';
    st.border = 'none';
    st.boxShadow = '0 4px 20px rgba(0,0,0,0.08), 0 1px 4px rgba(0,0,0,0.04)';
  } else if (style === 'hero') {
    st.background = s.value.bg_color || '#FFFFFF';
    st.border = `${borderW.value} solid ${s.value.border_color || '#E5E7EB'}`;
    st.boxShadow = '0 8px 32px rgba(0,0,0,0.1), 0 2px 8px rgba(0,0,0,0.05)';
  } else {
    st.background = s.value.bg_color || '#FFFFFF';
    st.border = `${borderW.value} solid ${s.value.border_color || '#E5E7EB'}`;
  }

  if (s.value.input_shadow && style !== 'floating' && style !== 'hero') {
    st.boxShadow = '0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04)';
  }

  return st;
});

const iconStyle = computed(() => ({
  display: 'flex',
  alignItems: 'center',
  flexShrink: '0',
  padding: iconPos.value === 'left' ? `0 0 0 ${s.value.style === 'hero' ? '20px' : '14px'}` : `0 ${s.value.style === 'hero' ? '20px' : '14px'} 0 0`,
}));

const inputStyle = computed(() => {
  const heroPad = s.value.style === 'hero' ? { fontSize: '20px', padding: '18px 16px' } : {};
  return {
    flex: '1',
    minWidth: '0',
    background: 'transparent',
    border: 'none',
    outline: 'none',
    color: s.value.text_color || '#374151',
    fontSize: sz.value.fontSize,
    padding: sz.value.padding,
    width: '100%',
    ...heroPad,
  };
});

const buttonStyle = computed(() => {
  const base = {
    flexShrink: '0',
    display: 'inline-flex',
    alignItems: 'center',
    justifyContent: 'center',
    gap: '6px',
    cursor: 'pointer',
    fontWeight: '600',
    fontSize: sz.value.fontSize,
    padding: btnStyle.value === 'icon-only' ? sz.value.padding : `${sz.value.padding.split(' ')[0]} 24px`,
    border: 'none',
    transition: 'all 0.2s ease',
  };

  if (btnStyle.value === 'outline') {
    base.background = 'transparent';
    base.color = accentColor.value;
    base.border = `2px solid ${accentColor.value}`;
    base.borderRadius = ((v => isNaN(v) ? 8 : v)(parseInt(s.value.button_radius))) + 'px';
    base.margin = '4px';
  } else {
    base.background = accentColor.value;
    base.color = s.value.button_color || '#FFFFFF';
    base.borderRadius = s.value.style === 'pill' ? '50px' : ((v => isNaN(v) ? 8 : v)(parseInt(s.value.button_radius))) + 'px';
    base.margin = '4px';
  }

  if (s.value.style === 'hero') {
    base.padding = '18px 32px';
    base.fontSize = '16px';
  }

  return base;
});
</script>

<style scoped>
.olo-search-tile { min-height: 40px; }
/* a11y: focus visibile sul wrap input + bordo input attivo → primary */
.olo-search-form-wrap:focus-within {
  outline: none;
  border-color: var(--olo-color-primary, #e1474f) !important;
  box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent);
}
input::placeholder { color: var(--olo-color-text-faint, #94a3b8); }
input:disabled { cursor: default; opacity: 1; -webkit-text-fill-color: unset; }
.olo-search-tile button:focus-visible {
  outline: none;
  box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent);
}
.olo-search-word {
  font-size: 9px;
  padding: 1px 5px;
  border-radius: 3px;
  background: color-mix(in srgb, var(--olo-color-primary, #e1474f) 10%, transparent);
  color: var(--olo-color-primary, #e1474f);
}
</style>
