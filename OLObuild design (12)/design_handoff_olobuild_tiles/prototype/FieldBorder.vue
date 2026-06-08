<template>
  <!--
    FieldBorder (redesign) — coerente con FieldBox (handoff regoletiles1).
    Sostituisce la "croce 2×2 + sezione Effetti separata" con un blocco compatto:
    riga spessore (collega+slider+valore, 4 lati on-demand via FieldBox) + stile + colore
    + effetto, in un solo blocco. Stato Normale/Hover e breakpoint gestiti dal pannello
    contenitore (StyleBoxStack), come per margini/padding/raggio.
    NB CHROME vs CONTENUTO: gli affordance del controllo (focus, link, slider — ereditati
    da FieldBox) usano l'accento CHROME del builder (arancio #e8622a, identità prodotto,
    fisso). Il *colore del bordo* è invece CONTENUTO → default token cliente (primary).
    Contratto dati salvato INVARIATO: { top,right,bottom,left,linked,style,color } +
    border_effect (chiave gestita dal renderer come oggi). NON cambia il formato.
  -->
  <div class="olo-borderfield">
    <!-- Spessore: riusa FieldBox in modalità lati -->
    <div class="olo-bf2-row">
      <span class="olo-bf2-lab">{{ t('Spessore') }}</span>
      <FieldBox
        class="olo-bf2-grow"
        mode="sides"
        preview="none"
        :sliderMax="20"
        :modelValue="widthModel"
        @update:modelValue="onWidth"
      />
    </div>

    <!-- Stile -->
    <div class="olo-bf2-row">
      <span class="olo-bf2-lab">{{ t('Stile') }}</span>
      <select class="olo-bf2-select" :value="val.style" @change="emit({ style: $event.target.value })">
        <option value="solid">{{ t('Solido') }}</option>
        <option value="dashed">{{ t('Tratteggiato') }}</option>
        <option value="dotted">{{ t('Punteggiato') }}</option>
        <option value="double">{{ t('Doppio') }}</option>
        <option value="groove">Groove</option>
        <option value="ridge">Ridge</option>
      </select>
    </div>

    <!-- Colore (token-first: vuoto ⇒ primary) -->
    <div class="olo-bf2-row">
      <span class="olo-bf2-lab">{{ t('Colore') }}</span>
      <div class="olo-bf2-color">
        <span class="olo-bf2-sw" :style="{ background: val.color || 'var(--olo-color-primary, #e1474f)' }"></span>
        <input
          type="text"
          class="olo-bf2-colinput"
          :value="val.color"
          :placeholder="t('primary')"
          @change="emit({ color: $event.target.value })"
        />
      </div>
    </div>

    <!-- Effetto (ripiegato qui, non più sezione separata) -->
    <div class="olo-bf2-row">
      <span class="olo-bf2-lab">{{ t('Effetto') }}</span>
      <select class="olo-bf2-select" :value="effect" @change="$emit('update:effect', $event.target.value)">
        <option value="none">{{ t('Nessuno') }}</option>
        <option value="glow">{{ t('Bagliore') }}</option>
        <option value="gradient">{{ t('Gradiente') }}</option>
        <option value="animated">{{ t('Animato') }}</option>
      </select>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';
import FieldBox from './FieldBox.vue';

const props = defineProps({
  modelValue: { default: null },        // { top,right,bottom,left,linked,style,color }
  effect: { type: String, default: 'none' },
});
const emits = defineEmits(['update:modelValue', 'update:effect']);

const EMPTY = { top: 0, right: 0, bottom: 0, left: 0, linked: true, style: 'solid', color: '' };

const val = computed(() => {
  const v = props.modelValue;
  if (v && typeof v === 'object') {
    return {
      top: Math.max(0, parseInt(v.top) || 0),
      right: Math.max(0, parseInt(v.right) || 0),
      bottom: Math.max(0, parseInt(v.bottom) || 0),
      left: Math.max(0, parseInt(v.left) || 0),
      linked: v.linked !== false,
      style: v.style || 'solid',
      color: v.color || '',
    };
  }
  return { ...EMPTY };
});

// FieldBox vuole numero (collegato) o oggetto {top,right,bottom,left} (separato)
const widthModel = computed(() => {
  const { top, right, bottom, left, linked } = val.value;
  if (linked && top === right && right === bottom && bottom === left) return top;
  return { top, right, bottom, left };
});

function emit(patch) {
  emits('update:modelValue', { ...val.value, ...patch });
}

function onWidth(w) {
  if (w && typeof w === 'object') {
    emit({
      top: parseInt(w.top) || 0, right: parseInt(w.right) || 0,
      bottom: parseInt(w.bottom) || 0, left: parseInt(w.left) || 0,
      linked: false,
    });
  } else {
    const n = parseInt(w) || 0;
    emit({ top: n, right: n, bottom: n, left: n, linked: true });
  }
}
</script>

<style scoped>
.olo-borderfield { display: flex; flex-direction: column; gap: 10px; padding: 2px 0; }
.olo-bf2-row { display: flex; align-items: center; gap: 10px; }
.olo-bf2-lab {
  width: 54px; flex-shrink: 0;
  font-size: 9px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase;
  color: #9ca3af;
}
.olo-bf2-grow { flex: 1; }
.olo-bf2-select {
  flex: 1; height: 34px;
  border: 1px solid #e5e7eb; border-radius: 9px; background: #fff;
  font-size: 13px; color: #1f2937; padding: 0 10px; outline: none; cursor: pointer;
}
.olo-bf2-select:focus { border-color: var(--olo-ui-accent, #e8622a); box-shadow: 0 0 0 3px rgba(232,98,42,.18); }
.olo-bf2-color { flex: 1; display: flex; align-items: center; gap: 8px; }
.olo-bf2-sw { width: 34px; height: 34px; border-radius: 9px; border: 1px solid #e5e7eb; flex-shrink: 0; }
.olo-bf2-colinput {
  flex: 1; height: 34px; border: 1px solid #e5e7eb; border-radius: 9px; background: #fff;
  font: 500 12px ui-monospace, monospace; color: #1f2937; padding: 0 10px; outline: none;
}
.olo-bf2-colinput:focus { border-color: var(--olo-ui-accent, #e8622a); box-shadow: 0 0 0 3px rgba(232,98,42,.18); }
</style>
