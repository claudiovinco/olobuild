<template>
  <div class="olo-templateembed-tile">
    <!-- Has template -->
    <div v-if="s.template_id" :style="boxStyle">
      <div style="display:flex;align-items:center;gap:8px;">
        <span style="opacity:0.5;display:inline-flex;color:var(--olo-color-text-muted, #6b7280);" v-html="docIcon"></span>
        <div>
          <div style="font-weight:600;font-size:0.95em;color:var(--olo-color-text, #374151);">
            {{ templateName }}
          </div>
          <div style="font-size:0.75em;color:var(--olo-color-text-muted, #6b7280);margin-top:2px;">
            Template #{{ s.template_id }}
          </div>
        </div>
      </div>
    </div>

    <!-- Empty state -->
    <div v-else :style="emptyStyle">
      <div style="margin-bottom:6px;opacity:0.5;display:inline-flex;color:var(--olo-color-text-muted, #9ca3af);" v-html="docIcon"></div>
      <div style="font-size:0.85em;color:var(--olo-color-text-muted, #9ca3af);">{{ t('Seleziona un template') }}</div>
      <div style="font-size:0.75em;color:var(--olo-color-text-muted, #6b7280);margin-top:4px;">
        {{ t('Scegli un template dal menu a discesa nell\'inspector') }}
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  template_id: 0,
};

const s = computed(() => ({ ...defaults, ...props.settings }));

// Icona SVG "layout" (coerente con dashicons-layout della tile) — niente emoji
const docIcon = '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>';

const templateName = computed(() => {
  const list = window.oloData?.templateList || [];
  const found = list.find(t => t.value === s.value.template_id);
  return found ? found.label : `Template #${s.value.template_id}`;
});

const boxStyle = computed(() => ({
  border: '2px dashed var(--olo-color-border, #E5E7EB)',
  borderRadius: '8px',
  padding: '16px',
  background: 'var(--olo-color-muted, #F3F4F6)',
  minHeight: '60px',
  display: 'flex',
  alignItems: 'center',
}));

const emptyStyle = computed(() => ({
  border: '2px dashed var(--olo-color-border, #E5E7EB)',
  borderRadius: '8px',
  padding: '24px',
  background: 'var(--olo-color-background, #FFFFFF)',
  textAlign: 'center',
  minHeight: '80px',
  display: 'flex',
  flexDirection: 'column',
  alignItems: 'center',
  justifyContent: 'center',
}));
</script>
