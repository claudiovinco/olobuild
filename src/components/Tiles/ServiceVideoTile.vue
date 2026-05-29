<template>
  <div :style="wrapStyle">
    <!-- Placeholder background -->
    <div :style="bgStyle"></div>
    <!-- Play button -->
    <div v-if="s.show_play_btn && !s.autoplay" :style="playBtnStyle">
      <svg :width="btnIconSz" :height="btnIconSz" viewBox="0 0 24 24" :fill="s.play_btn_color || '#FFF'">
        <polygon points="6,3 20,12 6,21"/>
      </svg>
    </div>
    <!-- Info badges -->
    <div style="position:absolute;bottom:8px;left:8px;display:flex;gap:4px;z-index:3">
      <span :style="badgeStyle">Video {{ s.video_slot || '1' }}</span>
      <span v-if="s.loop" :style="badgeStyle">{{ t('Loop') }}</span>
      <span v-if="s.muted" :style="badgeStyle">{{ t('Muto') }}</span>
      <span v-if="s.autoplay" :style="badgeStyle">{{ t('Auto') }}</span>
      <span v-if="s.controls" :style="badgeStyle">{{ t('Comandi') }}</span>
    </div>
    <!-- Aspect ratio label -->
    <div style="position:absolute;top:8px;right:8px;z-index:3">
      <span :style="badgeStyle">{{ s.aspect_ratio || '16/9' }}</span>
    </div>
  </div>
</template>

<script setup>
import { t } from '@/i18n';
import { computed } from 'vue';
const props = defineProps({ settings: { type: Object, default: () => ({}) } });

const defaults = {
  video_slot: '1', autoplay: false, loop: true, muted: true, controls: true,
  aspect_ratio: '16/9', border_radius: '12',
  show_play_btn: true, play_btn_size: '64', play_btn_bg: 'rgba(0,0,0,0.5)', play_btn_color: '#FFFFFF',
};
const s = computed(() => ({ ...defaults, ...props.settings }));

const wrapStyle = computed(() => ({
  position: 'relative',
  aspectRatio: s.value.aspect_ratio || '16/9',
  borderRadius: s.value.border_radius + 'px',
  overflow: 'hidden',
  maxHeight: '200px',
}));

const bgStyle = computed(() => ({
  position: 'absolute', inset: '0',
  // poster video placeholder: navy brand (era slate grigio nudo)
  background: 'linear-gradient(135deg, var(--olo-color-secondary, #16263d) 0%, color-mix(in srgb, var(--olo-color-secondary, #16263d) 80%, #fff) 50%, var(--olo-color-secondary, #16263d) 100%)',
}));

const btnSz = computed(() => Math.min(parseInt(s.value.play_btn_size) || 64, 50));
const btnIconSz = computed(() => Math.round(btnSz.value * 0.4));

const playBtnStyle = computed(() => ({
  position: 'absolute', top: '50%', left: '50%',
  transform: 'translate(-50%,-50%)',
  width: btnSz.value + 'px', height: btnSz.value + 'px',
  borderRadius: '50%',
  background: s.value.play_btn_bg,
  display: 'flex', alignItems: 'center', justifyContent: 'center',
  zIndex: '3',
  paddingLeft: Math.round(btnSz.value * 0.05) + 'px',
}));

const badgeStyle = computed(() => ({
  // testo badge su poster scuro: bianco neutro (era ciano #a5f3fc off-brand)
  background: 'rgba(0,0,0,0.5)', color: 'var(--olo-color-on-primary, #ffffff)',
  padding: '1px 6px', borderRadius: '8px',
  fontSize: '8px', fontWeight: '700', textTransform: 'uppercase', letterSpacing: '0.3px',
}));
</script>
