<template>
  <div>
    <div v-if="!wooActive" class="olo-woo-notice">
      <span class="olo-woo-notice-icon" aria-hidden="true"><svg width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor"><circle cx="7.3" cy="17.3" r="1.4" fill="currentColor" stroke="none"/><circle cx="13.3" cy="17.3" r="1.4" fill="currentColor" stroke="none"/><polyline points="0 2 3.2 4 5.3 12.5 16 12.5 18 6.5 8 6.5"/></svg></span>
      <span>{{ t('WooCommerce richiesto') }}</span>
    </div>
    <div v-else>
      <component :is="s.title_tag" v-if="s.title" :style="titleStyle" data-olo-editable="title">{{ s.title }}</component>
      <div :style="formStyle">
        <div :style="fieldStyle">
          <label :style="labelStyle">{{ t('Numero ordine') }}</label>
          <input type="text" :style="inputStyle" :placeholder="t('Inserisci il numero del tuo ordine')" readonly />
        </div>
        <div :style="fieldStyle">
          <label :style="labelStyle">{{ t('Email di fatturazione') }}</label>
          <input type="email" :style="inputStyle" :placeholder="t('Email usata per l\'ordine')" readonly />
        </div>
        <button :style="btnStyle">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
          <span>{{ t('Traccia ordine') }}</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { t } from '@/i18n';
import { computed } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  title: 'Traccia il tuo ordine',
  title_tag: 'h2',
  accent_color: 'var(--olo-color-primary, #e1474f)',
  text_color: '',
  button_color: '#FFFFFF',
  button_bg: 'var(--olo-color-primary, #e1474f)',
  form_style: 'modern',
};
const s = computed(() => ({ ...defaults, ...props.settings }));
const wooActive = computed(() => true);

const isModern = computed(() => s.value.form_style === 'modern');

const titleStyle = computed(() => ({
  color: s.value.text_color,
  margin: '0 0 24px 0',
  fontSize: '24px',
  fontWeight: 700,
}));

const formStyle = computed(() => ({
  display: 'flex',
  flexDirection: 'column',
  gap: '16px',
  maxWidth: '480px',
}));

const fieldStyle = computed(() => ({
  display: 'flex',
  flexDirection: 'column',
  gap: '6px',
}));

const labelStyle = computed(() => ({
  fontSize: '14px',
  fontWeight: 600,
  color: s.value.text_color,
}));

const inputStyle = computed(() => ({
  padding: isModern.value ? '12px 16px' : '8px 12px',
  border: isModern.value ? '2px solid var(--olo-color-border, #e5e7eb)' : '1px solid var(--olo-color-border, #e5e7eb)',
  borderRadius: isModern.value ? '10px' : '4px',
  fontSize: '15px',
  outline: 'none',
  width: '100%',
  boxSizing: 'border-box',
}));

const btnStyle = computed(() => ({
  display: 'inline-flex',
  alignItems: 'center',
  justifyContent: 'center',
  gap: '8px',
  padding: isModern.value ? '14px 28px' : '10px 20px',
  background: s.value.button_bg,
  color: s.value.button_color,
  border: 'none',
  borderRadius: isModern.value ? '10px' : '4px',
  fontSize: '15px',
  fontWeight: 600,
  cursor: 'pointer',
  marginTop: '4px',
}));
</script>

<style scoped>
.olo-woo-notice { display:flex;align-items:center;gap:8px;padding:16px 20px;background:color-mix(in srgb, var(--olo-color-warning, #b45309) 12%, #fff);border:1px solid var(--olo-color-warning, #b45309);border-radius:8px;color:var(--olo-color-warning, #b45309);font-size:14px;font-weight:500 }
.olo-woo-notice-icon { width:20px;height:20px;display:inline-flex;flex-shrink:0 } .olo-woo-notice-icon :deep(svg) { width:100%;height:100% }
</style>
