<template>
  <div style="display:inline-flex;flex-direction:column;gap:8px;max-width:340px;">
    <!-- Marker badge -->
    <div :style="markerStyle">
      <div style="display:flex;align-items:center;gap:6px;">
        <span style="font-size:16px;flex-shrink:0;">&#x1F6A9;</span>
        <span style="font-weight:700;color:#fbbf24;font-size:13px;">Hidden Pop</span>
      </div>
      <div style="color:#fde68a;font-size:11px;padding-left:22px;">
        Attivazione: scroll {{ s.trigger_direction === 'up' ? '\u2191' : s.trigger_direction === 'both' ? '\u2195' : '\u2193' }} al {{ s.trigger_threshold || 50 }}% viewport
      </div>
      <div v-if="s.popup_frequency !== 'always'" style="color:#d97706;font-size:10px;padding-left:22px;">
        {{ freqLabel }}
      </div>
    </div>

    <!-- Mini popup preview -->
    <div :style="previewStyle">
      <div style="position:absolute;top:-8px;left:12px;background:#78350f;padding:0 6px;font-size:9px;color:#fbbf24;font-weight:600;border-radius:3px;">POPUP PREVIEW</div>

      <!-- Close X -->
      <div v-if="s.modal_close_button !== false" style="position:absolute;top:8px;right:10px;color:#9ca3af;font-size:16px;font-weight:bold;line-height:1;">&times;</div>

      <template v-if="s.mode === 'template'">
        <div style="font-size:11px;color:#9ca3af;text-align:center;padding:12px 0;">
          Template Olobuild
        </div>
      </template>
      <template v-else>
        <!-- Image top -->
        <div v-if="s.image && (s.image_position === 'top' || !s.image_position)"
          style="margin:-12px -16px 8px;border-radius:12px 12px 0 0;overflow:hidden;max-height:80px;">
          <img :src="s.image" style="width:100%;height:80px;object-fit:cover;display:block;" />
        </div>

        <div v-if="s.title" :style="titlePreviewStyle" data-olo-editable="title">{{ s.title }}</div>
        <div v-if="s.subtitle" style="font-size:11px;color:#6b7280;margin-top:4px;line-height:1.4;" data-olo-editable="subtitle">{{ subtitlePreview }}</div>

        <!-- CTA button -->
        <div v-if="s.cta_text" style="margin-top:10px;">
          <span :style="ctaBtnStyle">{{ s.cta_text }}</span>
        </div>

        <!-- Image bottom -->
        <div v-if="s.image && s.image_position === 'bottom'"
          style="margin:8px -16px -12px;border-radius:0 0 12px 12px;overflow:hidden;max-height:80px;">
          <img :src="s.image" style="width:100%;height:80px;object-fit:cover;display:block;" />
        </div>
      </template>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const s = computed(() => ({
  mode: 'simple',
  title: 'Titolo popup',
  subtitle: '',
  image: '',
  image_position: 'top',
  cta_text: '',
  cta_style: 'primary',
  trigger_threshold: 50,
  trigger_direction: 'down',
  popup_frequency: 'always',
  modal_close_button: true,
  modal_bg_color: '#ffffff',
  title_color: '#111827',
  title_size: '24',
  text_color: '#4b5563',
  ...props.settings,
}));

const subtitlePreview = computed(() => {
  const t = s.value.subtitle || '';
  return t.length > 80 ? t.slice(0, 80) + '\u2026' : t;
});

const freqLabel = computed(() => {
  const map = {
    once_session: '1x per sessione',
    once_day: '1x al giorno',
    once_week: '1x a settimana',
    once_ever: 'Solo una volta',
  };
  return map[s.value.popup_frequency] || '';
});

const markerStyle = computed(() => ({
  display: 'inline-flex',
  flexDirection: 'column',
  gap: '3px',
  background: '#78350f',
  color: '#fde68a',
  border: '1px dashed #f59e0b',
  borderRadius: '8px',
  padding: '10px 16px',
  fontSize: '12px',
  lineHeight: '1.4',
  fontFamily: 'system-ui, sans-serif',
}));

const previewStyle = computed(() => ({
  position: 'relative',
  background: s.value.modal_bg_color || '#fff',
  border: '1px solid #e5e7eb',
  borderRadius: '12px',
  padding: '12px 16px',
  maxWidth: '300px',
  minWidth: '200px',
}));

const titlePreviewStyle = computed(() => ({
  fontSize: Math.min(parseInt(s.value.title_size) || 24, 20) + 'px',
  fontWeight: '700',
  color: s.value.title_color || '#111827',
  lineHeight: '1.3',
}));

const ctaStyleMap = {
  primary:   { bg: 'var(--olo-color-primary, #111)', color: '#fff' },
  secondary: { bg: '#e5e7eb', color: '#111' },
  danger:    { bg: '#dc2626', color: '#fff' },
  text:      { bg: 'transparent', color: 'var(--olo-color-primary, #111)' },
};

const ctaBtnStyle = computed(() => {
  const st = ctaStyleMap[s.value.cta_style] || ctaStyleMap.primary;
  return {
    display: 'inline-block',
    background: st.bg,
    color: st.color,
    padding: '6px 18px',
    fontSize: '12px',
    fontWeight: '600',
    borderRadius: '4px',
    cursor: 'default',
  };
});
</script>
