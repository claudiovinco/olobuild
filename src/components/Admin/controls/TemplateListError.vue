<template>
  <div v-if="state === 'error'" class="tpl-list-error" role="alert">
    <span>{{ t('Elenco dei template non caricato.') }}</span>
    <button type="button" class="cfg-btn cfg-btn-ghost" @click="refresh()">{{ t('Riprova') }}</button>
  </div>
</template>

<script setup>
// Riga sotto i menu dei template (Assegnazione, Manutenzione, Popup; in Template
// WooCommerce una sola, sopra le sei righe) quando l'elenco non arriva: al posto
// del menu vuoto e muto di prima.
import { t } from '@/i18n';
import { useTemplateOptions } from '../composables/useTemplateOptions';

const { state, refresh } = useTemplateOptions();
</script>

<style scoped>
.tpl-list-error {
  display: flex; flex-wrap: wrap; align-items: center; gap: 4px 8px;
  flex-basis: 100%; /* a capo anche dentro una riga flessibile (menu + pulsante) */
  margin-top: 6px;
  font-size: 12px; line-height: 1.5;
  color: var(--c-red-dark);
}
.tpl-list-error .cfg-btn { padding: 4px 10px; font-size: 12px; }
.tpl-list-error .cfg-btn:focus-visible { outline: 2px solid var(--c-red); outline-offset: 2px; }
</style>
