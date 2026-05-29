<template>
  <div>
    <h3 :style="{ fontSize:'16px', fontWeight:'700', color: TOKENS.text, margin:'0 0 10px' }">{{ t('Condividi') }}</h3>
    <div style="display:flex;gap:8px;flex-wrap:wrap">
      <button v-for="btn in buttons" :key="btn.label" type="button" class="olo-share-btn" :style="{ ...btnStyle, background: btn.bg }">{{ btn.label }}</button>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';
import { TOKENS } from '@/composables/oloTileDefaults';
defineProps({ settings: { type: Object, default: () => ({}) } });
// Colori brand-social (Facebook/WhatsApp) = identità ufficiali, restano.
// I generici (Email/Copia link) usano token neutri di sistema.
const buttons = [
  { label: 'Facebook', bg: '#1877f2' },
  { label: 'WhatsApp', bg: '#25d366' },
  { label: 'Email', bg: TOKENS.textSoft },
  { label: 'Copia link', bg: TOKENS.dark },
];
const btnStyle = { color: TOKENS.onPrimary, padding: '6px 14px', borderRadius: '6px', fontSize: '12px', fontWeight: '600', cursor: 'pointer', border: 'none' };
</script>

<style scoped>
.olo-share-btn:focus-visible {
  outline: none;
  box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent);
}
</style>
