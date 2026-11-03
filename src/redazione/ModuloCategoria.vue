<template>
  <div class="orz-categoria-campi">
    <label class="orz-campo orz-campo--meta">
      <span class="orz-etichetta">{{ t('Nome') }}<span v-if="lingue.length" class="orz-lingua-nome">{{ principale }}</span></span>
      <input :value="modelValue.nome" type="text" required @input="cambia('nome', $event.target.value)" ref="primo" />
    </label>
    <label v-if="tassonomia.gerarchica" class="orz-campo orz-campo--meta">
      <span class="orz-etichetta">{{ t('Dentro') }}</span>
      <select :value="modelValue.genitore" @change="cambia('genitore', parseInt($event.target.value, 10) || 0)">
        <option :value="0">{{ t('In cima') }}</option>
        <option v-for="x in madri" :key="x.id" :value="x.id">{{ '— '.repeat(x.livello) + x.nome }}</option>
      </select>
    </label>
    <label v-for="l in lingue" :key="l.codice" class="orz-campo orz-campo--terzo">
      <span class="orz-etichetta">{{ t('Nome') }}<span class="orz-lingua-nome">{{ l.nome }}</span></span>
      <input
        :value="(modelValue.nomi || {})[l.codice] || ''"
        type="text"
        :lang="l.codice"
        :placeholder="modelValue.nome"
        @input="cambiaNome(l.codice, $event.target.value)"
      />
    </label>
  </div>
</template>

<script setup>
/**
 * I campi di una categoria: il nome, dove sta (le categorie a cartelle) e il nome nelle altre lingue del sito. Vuoto in
 * una lingua = sul sito in quella lingua resta il nome principale. Lo usano la vista Categorie e «Aggiungi» nella scheda.
 */
import { ref, onMounted } from 'vue';
import { t } from '@/i18n';

const props = defineProps({
  modelValue: { type: Object, required: true },
  tassonomia: { type: Object, required: true },
  madri: { type: Array, default: () => [] },
  lingue: { type: Array, default: () => [] },
});
const emit = defineEmits(['update:modelValue']);

const datiRedazione = (window.oloData && window.oloData.redazione) || {};
const principale = (datiRedazione.lingue && datiRedazione.lingue.principale && datiRedazione.lingue.principale.nome) || '';

const primo = ref(null);
onMounted(() => {
  if (primo.value) primo.value.focus();
});

function cambia(chiave, valore) {
  emit('update:modelValue', { ...props.modelValue, [chiave]: valore });
}

function cambiaNome(codice, valore) {
  emit('update:modelValue', { ...props.modelValue, nomi: { ...(props.modelValue.nomi || {}), [codice]: valore } });
}
</script>
