<template>
  <div class="olo-templateembed-tile">
    <!-- Has template -->
    <div v-if="s.template_id" :style="boxStyle">
      <div style="display:flex;align-items:center;gap:8px;">
        <span style="font-size:20px;opacity:0.5;">&#128196;</span>
        <div>
          <div style="font-weight:600;font-size:0.95em;color:var(--olo-color-text, #374151);">
            {{ templateName }}
          </div>
          <div style="font-size:0.75em;color:#6b7280;margin-top:2px;">
            Template #{{ s.template_id }}
          </div>
        </div>
      </div>
    </div>

    <!-- Empty state -->
    <div v-else :style="emptyStyle">
      <div style="font-size:24px;margin-bottom:6px;opacity:0.5;">&#128196;</div>
      <div style="font-size:0.85em;color:#9ca3af;">{{ t('Seleziona un template') }}</div>
      <div style="font-size:0.75em;color:#6b7280;margin-top:4px;">
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
