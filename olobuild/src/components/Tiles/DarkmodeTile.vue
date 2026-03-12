<template>
  <div class="mb-flex mb-items-center mb-justify-center mb-py-6">
    <!-- Toggle style -->
    <div v-if="s.style === 'toggle'" class="olo-dm-toggle-preview" :style="toggleWrapStyle">
      <span class="olo-dm-toggle-track" :style="trackStyle">
        <span class="olo-dm-toggle-thumb" :style="thumbStyle">
          <svg v-if="!isDark" :width="thumbIconSize" :height="thumbIconSize" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
          </svg>
          <svg v-else :width="thumbIconSize" :height="thumbIconSize" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
          </svg>
        </span>
      </span>
    </div>

    <!-- Icon style -->
    <button v-else-if="s.style === 'icon'" class="olo-dm-icon-preview" :style="iconBtnStyle" @click="isDark = !isDark">
      <svg v-if="!isDark" :width="s.icon_size" :height="s.icon_size" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
      </svg>
      <svg v-else :width="s.icon_size" :height="s.icon_size" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
      </svg>
    </button>

    <!-- Button style -->
    <button v-else class="olo-dm-btn-preview" :style="buttonStyle" @click="isDark = !isDark">
      <svg v-if="!isDark" :width="s.icon_size" :height="s.icon_size" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px; flex-shrink: 0;">
        <circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
      </svg>
      <svg v-else :width="s.icon_size" :height="s.icon_size" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px; flex-shrink: 0;">
        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
      </svg>
      <span>{{ isDark ? s.button_text_dark : s.button_text_light }}</span>
    </button>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  style: 'toggle',
  light_icon: 'sun',
  dark_icon: 'moon',
  icon_size: 24,
  button_text_light: 'Modalità scura',
  button_text_dark: 'Modalità chiara',
  toggle_color: '#333333',
  toggle_active_color: '#ffd700',
  save_preference: true,
  respect_system: true,
  transition_duration: 300,
};

const s = computed(() => ({ ...defaults, ...props.settings }));
const isDark = ref(false);

const thumbIconSize = computed(() => Math.max(12, Math.round(s.value.icon_size * 0.6)));

const toggleWrapStyle = computed(() => ({
  cursor: 'pointer',
}));

const trackStyle = computed(() => {
  const w = Math.max(44, s.value.icon_size * 2.2);
  const h = Math.max(24, s.value.icon_size * 1.2);
  return {
    display: 'inline-flex',
    alignItems: 'center',
    width: w + 'px',
    height: h + 'px',
    borderRadius: h + 'px',
    background: isDark.value ? (s.value.toggle_active_color || '#ffd700') : (s.value.toggle_color || '#333'),
    padding: '3px',
    transition: `background ${s.value.transition_duration}ms ease`,
    position: 'relative',
  };
});

const thumbStyle = computed(() => {
  const h = Math.max(24, s.value.icon_size * 1.2);
  const thumbSize = h - 6;
  const w = Math.max(44, s.value.icon_size * 2.2);
  const travel = w - thumbSize - 6;
  return {
    display: 'inline-flex',
    alignItems: 'center',
    justifyContent: 'center',
    width: thumbSize + 'px',
    height: thumbSize + 'px',
    borderRadius: '50%',
    background: '#fff',
    transform: isDark.value ? `translateX(${travel}px)` : 'translateX(0)',
    transition: `transform ${s.value.transition_duration}ms ease`,
    color: isDark.value ? (s.value.toggle_active_color || '#ffd700') : (s.value.toggle_color || '#333'),
  };
});

const iconBtnStyle = computed(() => ({
  background: 'none',
  border: 'none',
  cursor: 'pointer',
  color: isDark.value ? (s.value.toggle_active_color || '#ffd700') : (s.value.toggle_color || '#333'),
  transition: `color ${s.value.transition_duration}ms ease`,
  padding: '8px',
  borderRadius: '50%',
  display: 'inline-flex',
  alignItems: 'center',
  justifyContent: 'center',
}));

const buttonStyle = computed(() => ({
  display: 'inline-flex',
  alignItems: 'center',
  padding: '10px 20px',
  borderRadius: '8px',
  border: '2px solid ' + (isDark.value ? (s.value.toggle_active_color || '#ffd700') : (s.value.toggle_color || '#333')),
  background: 'transparent',
  color: isDark.value ? (s.value.toggle_active_color || '#ffd700') : (s.value.toggle_color || '#333'),
  fontSize: '14px',
  fontWeight: '600',
  cursor: 'pointer',
  transition: `all ${s.value.transition_duration}ms ease`,
}));
</script>
