<template>
  <div class="dun-panel" role="status" @keydown.esc.stop="$emit('close')">
    <div class="dun-header">
      <span class="dun-title">{{ t('Non disponibile nella sandbox') }}</span>
      <button ref="chiudi" type="button" class="dun-close" :aria-label="t('Chiudi')" @click="$emit('close')">{{ t('&times;') }}</button>
    </div>
    <p class="dun-text">{{ t('Sul tuo sito collega testi, immagini ed elenchi ai contenuti di WordPress: articoli, pagine, autore, campi personalizzati.') }}</p>
  </div>
</template>

<script setup>
// L'avviso del fulmine e della «Sorgente dinamica» dove i contenuti dinamici non si possono
// usare (la sandbox di try chiude le API delle fonti): prima si apriva un pannello vuoto.
import { t } from '@/i18n';
import { ref, onMounted } from 'vue';

defineEmits(['close']);

const chiudi = ref(null);

onMounted(() => {
  chiudi.value?.focus();
});
</script>

<style scoped>
/* Stesso aspetto del pannello di collegamento (DynamicBindingPanel), tema chiaro del chrome */
.dun-panel {
  --olo-ui-accent: #e8622a;
  margin-top: 6px;
  padding: 10px;
  background: #fff;
  border: 1px solid rgba(17, 24, 39, 0.12);
  border-radius: 8px;
  box-shadow: 0 6px 18px rgba(17, 24, 39, 0.08);
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.dun-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 8px;
}

.dun-title {
  font-size: 11px;
  font-weight: 600;
  color: #1f2937;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.dun-close {
  width: 18px;
  height: 18px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  border: none;
  border-radius: 3px;
  background: transparent;
  color: #6b7280;
  font-size: 14px;
  cursor: pointer;
  padding: 0;
}

.dun-close:hover {
  background: #f3f4f6;
  color: #111827;
}

.dun-close:focus-visible {
  outline: 2px solid var(--olo-ui-accent);
  outline-offset: 2px;
}

.dun-text {
  margin: 0;
  font-size: 11px;
  line-height: 1.45;
  color: #4b5563;
}
</style>
