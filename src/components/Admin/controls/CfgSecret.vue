<template>
  <div class="cfg-input mono cfg-secret">
    <!-- autocomplete="new-password" sui segreti: con "off" Chrome tratta il campo come
         login e autocompila lo username nel primo input di testo della pagina (vedi AITab). -->
    <input
      :type="secret && !reveal ? 'password' : 'text'"
      :value="modelValue"
      :name="name"
      :placeholder="placeholder"
      :aria-label="label"
      :autocomplete="secret ? 'new-password' : 'off'"
      spellcheck="false"
      data-1p-ignore
      data-lpignore="true"
      @focus="onFocus"
      @mouseup="onMouseup"
      @blur="onBlur"
      @input="emit('update:modelValue', $event.target.value)"
    />
    <button
      v-if="secret"
      type="button"
      class="reveal"
      :title="reveal ? t('Nascondi') : t('Mostra')"
      :aria-label="reveal ? t('Nascondi') : t('Mostra')"
      :aria-pressed="reveal ? 'true' : 'false'"
      @click="reveal = !reveal"
    >
      <svg v-if="!reveal" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg>
      <svg v-else viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M3 3l18 18M10.6 6.1A10.5 10.5 0 0 1 12 6c6.5 0 10 6 10 6a17.3 17.3 0 0 1-4 4.5M6.6 6.6A17.3 17.3 0 0 0 2 12s3.5 6 10 6c1.3 0 2.5-.3 3.6-.7"/></svg>
    </button>
  </div>
</template>

<script setup>
// Campo per una chiave API della Configurazione. Il server rimanda i segreti già
// salvati mascherati («****abcd») e ignora in scrittura ogni valore con un «*»:
// entrando nel campo il testo mascherato si seleziona tutto, così quello che si
// scrive o si incolla lo sostituisce invece di aggiungersi agli asterischi.
import { ref } from 'vue';
import { t } from '@/i18n';

defineProps({
  modelValue: { type: String, default: '' },
  name: { type: String, default: '' },
  label: { type: String, default: '' },
  placeholder: { type: String, default: '' },
  secret: { type: Boolean, default: true },
});
const emit = defineEmits(['update:modelValue']);

const reveal = ref(false);

// Col clic il rilascio del mouse sposterebbe il cursore e toglierebbe la selezione:
// il primo mouseup dopo il focus non lo fa.
let tieniSelezione = false;
function onFocus(e) {
  const el = e.target;
  if (el && typeof el.value === 'string' && el.value.includes('*')) {
    el.select();
    tieniSelezione = true;
  }
}
function onMouseup(e) {
  if (tieniSelezione) {
    e.preventDefault();
    tieniSelezione = false;
  }
}
function onBlur() {
  tieniSelezione = false;
}
</script>

<style scoped>
.cfg-secret .reveal:focus-visible {
  outline: 2px solid var(--c-red);
  outline-offset: 2px;
  border-radius: 4px;
}
</style>
