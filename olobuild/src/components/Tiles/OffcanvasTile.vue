<template>
  <div class="olo-oc-preview">
    <!-- Trigger preview -->
    <div v-if="s.show_trigger" class="olo-oc-trigger-wrap">
    <div class="olo-oc-trigger-preview" :style="triggerStyle">
      <svg v-if="s.trigger_style === 'bars'" :width="trigSize" :height="trigSize" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
        <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
      </svg>
      <svg v-else-if="s.trigger_style === 'dots'" :width="trigSize" :height="trigSize" viewBox="0 0 24 24" fill="currentColor">
        <circle cx="12" cy="5" r="2"/><circle cx="12" cy="12" r="2"/><circle cx="12" cy="19" r="2"/>
      </svg>
      <svg v-else-if="s.trigger_style === 'grid'" :width="trigSize" :height="trigSize" viewBox="0 0 24 24" fill="currentColor">
        <rect x="3" y="3" width="4" height="4" rx="1"/><rect x="10" y="3" width="4" height="4" rx="1"/><rect x="17" y="3" width="4" height="4" rx="1"/>
        <rect x="3" y="10" width="4" height="4" rx="1"/><rect x="10" y="10" width="4" height="4" rx="1"/><rect x="17" y="10" width="4" height="4" rx="1"/>
        <rect x="3" y="17" width="4" height="4" rx="1"/><rect x="10" y="17" width="4" height="4" rx="1"/><rect x="17" y="17" width="4" height="4" rx="1"/>
      </svg>
      <svg v-else :width="trigSize" :height="trigSize" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
        <line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>
      </svg>
    </div>
    <span v-if="triggerPositionLabel" class="olo-oc-pos-badge">{{ triggerPositionLabel }}</span>
    </div>

    <!-- Panel mockup -->
    <div class="olo-oc-mockup" :style="mockupStyle">
      <!-- Page area -->
      <div class="olo-oc-page" :class="{ 'olo-oc-page--blur': s.backdrop_blur }" :style="pageStyle">
        <div class="olo-oc-page-bar"></div>
        <div class="olo-oc-page-line" style="width:80%"></div>
        <div class="olo-oc-page-line" style="width:60%"></div>
        <div class="olo-oc-page-line" style="width:70%"></div>
      </div>
      <!-- Overlay -->
      <div v-if="s.overlay" class="olo-oc-overlay" :style="overlayStyle"></div>
      <!-- Panel -->
      <div class="olo-oc-panel" :style="panelStyle">
        <!-- Close btn -->
        <div v-if="s.close_button" class="olo-oc-close-preview" :style="{ color: s.close_color || '#fff' }">✕</div>
        <!-- Logo -->
        <div v-if="s.show_logo && s.logo_url" class="olo-oc-logo-preview">
          <img :src="s.logo_url" :style="{ height: Math.round(parseInt(s.logo_height || 36) * 0.5) + 'px' }" />
        </div>
        <!-- Menu lines -->
        <div class="olo-oc-menu-lines" :style="menuLinesStyle">
          <div class="olo-oc-menu-line" style="width:70%"></div>
          <div class="olo-oc-menu-line" style="width:55%"></div>
          <div class="olo-oc-menu-line" style="width:65%"></div>
          <div class="olo-oc-menu-line" style="width:45%"></div>
        </div>
        <!-- Template label -->
        <div class="olo-oc-tpl-label">
          {{ templateLabel }}
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
  tileId: { type: String, default: '' },
});

const defaults = {
  mode: 'right',
  panel_width: '320',
  panel_width_unit: 'px',
  bg_color: '#1F2937',
  text_color: '#FFFFFF',
  backdrop_blur: false,
  overlay: true,
  overlay_color: '#000000',
  overlay_opacity: '50',
  content_align: 'top',
  template_id: 0,
  show_logo: false,
  logo_url: '',
  logo_height: '36',
  close_button: true,
  close_color: '#FFFFFF',
  show_trigger: true,
  trigger_style: 'bars',
  trigger_color: '',
  trigger_size: '24',
};

const s = computed(() => ({ ...defaults, ...props.settings }));
const trigSize = computed(() => Math.max(16, parseInt(s.value.trigger_size) || 24));

const triggerStyle = computed(() => {
  const st = {
    color: s.value.trigger_color || 'var(--olo-color-text, #374151)',
  };
  if (s.value.trigger_bg) {
    st.background = s.value.trigger_bg;
    st.borderRadius = (parseInt(s.value.trigger_border_radius) || 6) + 'px';
  }
  return st;
});

const triggerPositionLabel = computed(() => {
  const pos = s.value.trigger_position || 'inline';
  const map = { 'top-left': '↖ alto-sx', 'top-right': '↗ alto-dx', 'bottom-left': '↙ basso-sx', 'bottom-right': '↘ basso-dx' };
  return map[pos] || '';
});

const mockupStyle = computed(() => ({
  flexDirection: s.value.mode === 'left' ? 'row' : 'row-reverse',
}));

const panelWidth = computed(() => {
  if (s.value.mode === 'fullscreen') return '100%';
  return '45%';
});

const panelStyle = computed(() => ({
  width: panelWidth.value,
  background: s.value.bg_color || '#1F2937',
  color: s.value.text_color || '#fff',
  justifyContent: s.value.content_align === 'center' ? 'center'
    : s.value.content_align === 'bottom' ? 'flex-end'
    : s.value.content_align === 'between' ? 'space-between'
    : 'flex-start',
}));

const pageStyle = computed(() => ({
  width: s.value.mode === 'fullscreen' ? '100%' : '55%',
  position: s.value.mode === 'fullscreen' ? 'absolute' : 'relative',
  inset: s.value.mode === 'fullscreen' ? '0' : undefined,
}));

const overlayStyle = computed(() => ({
  background: s.value.overlay_color || '#000',
  opacity: (parseInt(s.value.overlay_opacity) || 50) / 100,
}));

const menuLinesStyle = computed(() => ({
  alignItems: s.value.mode === 'fullscreen' ? 'center' : 'flex-start',
}));

const templateLabel = computed(() => {
  const tpl = parseInt(s.value.template_id);
  if (!tpl) return 'Nessun template selezionato';
  const list = (window.oloData?.templateList || []);
  const found = list.find(t => String(t.value) === String(tpl));
  return found ? found.label : `Template #${tpl}`;
});
</script>

<style scoped>
.olo-oc-preview { padding: 4px; }
.olo-oc-trigger-preview {
  display: inline-flex; align-items: center; justify-content: center;
  padding: 6px; border-radius: 6px; cursor: pointer;
  background: rgba(0,0,0,0.04);
  transition: background .15s;
}
.olo-oc-trigger-preview:hover { background: rgba(0,0,0,0.08); }
.olo-oc-trigger-wrap { display: flex; align-items: center; gap: 6px; margin-bottom: 8px; }
.olo-oc-pos-badge { font-size: 10px; color: #9ca3af; }

.olo-oc-mockup {
  display: flex; border-radius: 8px; overflow: hidden;
  height: 140px; border: 1px solid rgba(0,0,0,0.1); position: relative;
}
.olo-oc-page {
  display: flex; flex-direction: column; gap: 6px; padding: 12px;
  background: #f9fafb; z-index: 1;
}
.olo-oc-page--blur { filter: blur(2px); opacity: 0.5; }
.olo-oc-page-bar { width: 50%; height: 6px; border-radius: 3px; background: #d1d5db; }
.olo-oc-page-line { height: 4px; border-radius: 2px; background: #e5e7eb; }

.olo-oc-overlay {
  position: absolute; inset: 0; z-index: 2; pointer-events: none;
}

.olo-oc-panel {
  display: flex; flex-direction: column; gap: 6px; padding: 12px;
  position: relative; z-index: 3; min-width: 0; flex-shrink: 0;
}
.olo-oc-close-preview {
  position: absolute; top: 6px; right: 8px; font-size: 12px; opacity: 0.7;
}
.olo-oc-logo-preview { margin-bottom: 4px; }
.olo-oc-logo-preview img { display: block; object-fit: contain; }

.olo-oc-menu-lines {
  display: flex; flex-direction: column; gap: 5px; flex: 1;
  justify-content: center;
}
.olo-oc-menu-line {
  height: 5px; border-radius: 3px; background: currentColor; opacity: 0.5;
}
.olo-oc-menu-line:first-child { opacity: 0.8; }

.olo-oc-tpl-label {
  font-size: 9px; opacity: 0.5; text-align: center;
  margin-top: auto; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
</style>
