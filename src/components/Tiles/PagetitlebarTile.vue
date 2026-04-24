<template>
  <div class="olo-page-title-bar" :style="wrapperStyle">
    <div v-if="s.bg_image && overlayOpacity > 0" :style="overlayStyle" aria-hidden="true"></div>
    <div :style="innerStyle">
      <component :is="titleTag" :style="titleStyle">{{ titleText }}</component>
      <p v-if="s.subtitle" :style="subtitleStyle" data-olo-editable="subtitle">{{ s.subtitle }}</p>
      <nav v-if="s.show_breadcrumbs" :style="{ marginTop: '16px', fontSize: '13px', color: bcColor }">
        <span style="opacity:.7">{{ t('Home') }}</span>
        <span :style="{ margin: '0 6px', opacity: '.5' }">{{ s.breadcrumb_separator || '/' }}</span>
        <span>{{ t('Titolo pagina') }}</span>
      </nav>
    </div>
  </div>
</template>

<script setup>
import { t } from '@/i18n';
import { computed } from 'vue';
const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const s = computed(() => props.settings || {});

const titleTag = computed(() => {
  const valid = ['h1','h2','h3','h4','h5','h6','div','span'];
  return valid.includes(s.value.title_tag) ? s.value.title_tag : 'h1';
});
const titleText = computed(() => 'Titolo pagina');
const titleColor = computed(() => s.value.title_color || '#FFFFFF');
const titleSize = computed(() => Math.max(14, parseInt(s.value.title_size) || 36));
const titleWeight = computed(() => s.value.title_weight || '700');
const align = computed(() => ['left','center','right'].includes(s.value.title_align) ? s.value.title_align : 'center');

const bgColor = computed(() => s.value.bg_color || '#1F2937');
const overlayOpacity = computed(() => Math.max(0, Math.min(100, parseInt(s.value.bg_overlay) || 60)));
const overlayColor = computed(() => s.value.bg_overlay_color || '#000000');
const minH = computed(() => Math.max(0, parseInt(s.value.min_height) || 200));
const padY = computed(() => Math.max(0, parseInt(s.value.padding_y) || 60));
const maxW = computed(() => Math.max(0, parseInt(s.value.content_width) || 1200));
const bcColor = computed(() => s.value.breadcrumb_color || '#9CA3AF');

const wrapperStyle = computed(() => {
  const st = {
    backgroundColor: bgColor.value,
    position: 'relative',
    minHeight: minH.value + 'px',
    display: 'flex',
    alignItems: 'center',
    textAlign: align.value,
  };
  if (s.value.bg_image) {
    st.backgroundImage = `url(${s.value.bg_image})`;
    st.backgroundSize = s.value.bg_size || 'cover';
    st.backgroundPosition = s.value.bg_position || 'center center';
    st.backgroundRepeat = 'no-repeat';
  }
  if (s.value.border_bottom) {
    st.borderBottom = `1px solid ${s.value.border_color || '#374151'}`;
  }
  return st;
});

const overlayStyle = computed(() => ({
  position: 'absolute',
  inset: '0',
  background: overlayColor.value,
  opacity: overlayOpacity.value / 100,
  pointerEvents: 'none',
}));

const innerStyle = computed(() => ({
  position: 'relative',
  zIndex: 1,
  width: '100%',
  maxWidth: maxW.value + 'px',
  margin: '0 auto',
  padding: padY.value + 'px 20px',
}));

const titleStyle = computed(() => ({
  color: titleColor.value,
  fontSize: titleSize.value + 'px',
  fontWeight: titleWeight.value,
  margin: '0',
  lineHeight: '1.2',
}));

const subtitleStyle = computed(() => ({
  color: s.value.subtitle_color || '#D1D5DB',
  fontSize: Math.max(12, parseInt(s.value.subtitle_size) || 16) + 'px',
  margin: '10px 0 0',
  opacity: '.85',
}));
</script>
