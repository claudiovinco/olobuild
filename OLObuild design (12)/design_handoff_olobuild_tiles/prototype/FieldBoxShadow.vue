<template>
  <!--
    FieldBoxShadow (REDESIGN drop-in) — sostituisce src/components/Builder/fields/FieldBoxShadow.vue.
    Stesso contratto dati dell'esistente (così è un rimpiazzo 1:1, niente migrazione):
      · props.modelValue = oggetto `style.shadow_custom` con chiavi { h, v, blur, spread, color, inset }
      · emit('update:modelValue', nuovoOggetto)
      · default identici all'attuale: { h:0, v:4, blur:10, spread:0, color:'rgba(0,0,0,0.15)', inset:false }
      · colore via <FieldColor> (come oggi, token-aware)

    DOVE VIVE COSA (verificato su _styleFieldsBase.js → mapping shadow_block):
      · `style.shadow` = preset 'none|sm|md|lg|xl|custom'  → lo rende lo STYLE EFFECTS STACK
        (StyleEffectsStack), che monta QUESTO componente solo quando shadow === 'custom'.
        Il redesign della scala (segmented + chip elevazione) va lì, NON qui — vedi
        REFERENCE_shadow-control.html (colonna "Coerente", blocco LIVELLO) e la nota in fondo.
      · hover → `style.hover.shadow` + `style.hover.shadow_custom`, gestito dal toggle
        Normale/Hover globale dello stack (come StyleBoxStack per margini/bordo).

    Cosa cambia rispetto a oggi: i 4 number input ammucchiati + checkbox diventano righe
    compatte slider+valore (lingua di FieldBox), inset come switch, preview CSS reale.
    CHROME vs CONTENUTO: slider/switch/focus usano l'accento CHROME (arancio, fisso);
    il colore dell'ombra è CONTENUTO (default dark @ alpha, via FieldColor/token).
  -->
  <div class="olo-shadowfield">
    <div class="olo-sh-row">
      <span class="olo-sh-rl">{{ t('Offset') }}</span>
      <input class="olo-sh-slider" type="range" min="-50" max="50" :value="val.h"
             :aria-label="t('Offset orizzontale ombra (px)')" @input="update('h', $event.target.value)"/>
      <span class="olo-sh-val"><input type="number" min="-100" max="100" :value="val.h"
             :aria-label="t('Offset orizzontale ombra (px)')" @input="update('h', $event.target.value)"/><i>H</i></span>
    </div>
    <div class="olo-sh-row">
      <span class="olo-sh-rl"></span>
      <input class="olo-sh-slider" type="range" min="-50" max="50" :value="val.v"
             :aria-label="t('Offset verticale ombra (px)')" @input="update('v', $event.target.value)"/>
      <span class="olo-sh-val"><input type="number" min="-100" max="100" :value="val.v"
             :aria-label="t('Offset verticale ombra (px)')" @input="update('v', $event.target.value)"/><i>V</i></span>
    </div>
    <div class="olo-sh-row">
      <span class="olo-sh-rl">{{ t('Sfoc.') }}</span>
      <input class="olo-sh-slider" type="range" min="0" max="100" :value="val.blur"
             :aria-label="t('Sfocatura ombra (px)')" @input="update('blur', $event.target.value)"/>
      <span class="olo-sh-val"><input type="number" min="0" max="200" :value="val.blur"
             :aria-label="t('Sfocatura ombra (px)')" @input="update('blur', $event.target.value)"/><i>px</i></span>
    </div>
    <div class="olo-sh-row">
      <span class="olo-sh-rl">{{ t('Estens.') }}</span>
      <input class="olo-sh-slider" type="range" min="-30" max="50" :value="val.spread"
             :aria-label="t('Diffusione ombra (px)')" @input="update('spread', $event.target.value)"/>
      <span class="olo-sh-val"><input type="number" min="-100" max="100" :value="val.spread"
             :aria-label="t('Diffusione ombra (px)')" @input="update('spread', $event.target.value)"/><i>px</i></span>
    </div>
    <div class="olo-sh-row olo-sh-row--col">
      <span class="olo-sh-rl">{{ t('Colore') }}</span>
      <div class="olo-sh-color">
        <FieldColor :modelValue="val.color || 'rgba(0,0,0,0.15)'" @update:modelValue="update('color', $event)" />
      </div>
    </div>
    <div class="olo-sh-row olo-sh-row--last">
      <span class="olo-sh-rl">{{ t('Inset') }}</span>
      <button type="button" role="switch" :aria-checked="String(!!val.inset)"
              :class="['olo-sh-switch', { on: val.inset }]"
              :aria-label="t('Ombra interna (inset)')"
              @click="update('inset', !val.inset)"></button>
    </div>
    <div class="olo-sh-preview" aria-hidden="true">{{ previewText }}</div>
  </div>
</template>

<script setup>
import { t } from '@/i18n';
import { computed } from 'vue';
import FieldColor from './FieldColor.vue';

// Default IDENTICI all'attuale FieldBoxShadow (drop-in): nessun cambio di comportamento dei dati.
const props = defineProps({
  modelValue: { type: Object, default: () => ({ h: 0, v: 4, blur: 10, spread: 0, color: 'rgba(0,0,0,0.15)', inset: false }) },
});
const emit = defineEmits(['update:modelValue']);

const val = computed(() => ({
  h: props.modelValue?.h ?? 0,
  v: props.modelValue?.v ?? 4,
  blur: props.modelValue?.blur ?? 10,
  spread: props.modelValue?.spread ?? 0,
  color: props.modelValue?.color ?? 'rgba(0,0,0,0.15)',
  inset: props.modelValue?.inset ?? false,
}));

const previewText = computed(() => {
  const v = val.value;
  return `${v.inset ? 'inset ' : ''}${v.h}px ${v.v}px ${v.blur}px ${v.spread}px ${v.color}`;
});

function update(key, value) {
  const numKeys = ['h', 'v', 'blur', 'spread'];
  const newVal = { ...val.value, [key]: numKeys.includes(key) ? (parseInt(value) || 0) : value };
  emit('update:modelValue', newVal);
}
</script>

<style scoped>
.olo-shadowfield { display: flex; flex-direction: column; gap: 10px; padding: 2px 0; }
.olo-sh-row { display: flex; align-items: center; gap: 10px; }
.olo-sh-row--last { margin-bottom: 0; }
.olo-sh-row--col { align-items: flex-start; }
.olo-sh-rl { width: 54px; flex-shrink: 0; font-size: 9px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; color: #94a3b8; padding-top: 9px; }
.olo-sh-row:not(.olo-sh-row--col) .olo-sh-rl { padding-top: 0; }
.olo-sh-slider { flex: 1; accent-color: var(--olo-ui-accent, #e8622a); height: 5px; cursor: pointer; }
.olo-sh-slider:focus-visible { outline: 2px solid var(--olo-ui-accent, #e8622a); outline-offset: 4px; }
.olo-sh-val { display: flex; align-items: center; border: 1px solid #e5e7eb; border-radius: 9px; overflow: hidden; background: #fff; height: 34px; width: 72px; flex-shrink: 0; }
.olo-sh-val input { width: 100%; min-width: 0; border: 0; outline: none; text-align: center; font: 500 13px ui-monospace, monospace; color: #1f2937; background: transparent; -moz-appearance: textfield; }
.olo-sh-val input::-webkit-outer-spin-button, .olo-sh-val input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
.olo-sh-val:focus-within { border-color: var(--olo-ui-accent, #e8622a); box-shadow: 0 0 0 3px rgba(232,98,42,.18); }
.olo-sh-val i { font-style: normal; font-size: 11px; color: #94a3b8; font-weight: 600; padding: 0 8px; border-left: 1px solid #eef0f3; align-self: stretch; display: flex; align-items: center; background: #f6f7f9; }
.olo-sh-color { flex: 1; min-width: 0; }
.olo-sh-switch { width: 34px; height: 19px; border: 0; border-radius: 99px; background: #cbd5e1; position: relative; cursor: pointer; transition: background .15s; flex-shrink: 0; }
.olo-sh-switch::after { content: ""; position: absolute; top: 2px; left: 2px; width: 15px; height: 15px; border-radius: 50%; background: #fff; box-shadow: 0 1px 2px rgba(0,0,0,.2); transition: left .15s; }
.olo-sh-switch.on { background: var(--olo-ui-accent, #e8622a); }
.olo-sh-switch.on::after { left: 17px; }
.olo-sh-switch:focus-visible { outline: 2px solid var(--olo-ui-accent, #e8622a); outline-offset: 2px; }
.olo-sh-preview { font: 500 10px ui-monospace, monospace; color: #6b7280; background: #f6f7f9; border-radius: 7px; padding: 7px 9px; word-break: break-all; }
</style>
