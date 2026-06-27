<template>
  <!--
    FieldBorder — REDESIGN (handoff "olobuild_bordercontrol").
    Porta il controllo Bordo in linea con FieldBox: niente più croce 2×2 +
    pannello "Effetti bordo" separato. Una riga compatta per lo SPESSORE
    (collega/separa + slider + valore, 4 lati on-demand) + righe Stile, Colore,
    Effetto, e un peek che mostra il bordo reale.

    CONTRATTO DATI INVARIATO rispetto all'attuale FieldBorder.vue:
      modelValue = { top, right, bottom, left, linked, style, color }
    Lo spessore riusa FieldBox in mode="sides" (chiavi top/right/bottom/left).

    Hover e breakpoint NON sono gestiti qui: come per FieldBox li avvolgono
    InspectorField (occhio hover) e ResponsiveFieldWrap (suffisso _<bp> sulla
    chiave). Lo switch device / toggle Normale-Hover nel mockup sono CHROME del
    wrapper, mostrati solo per contesto.

    Accento: usa l'accento FISSO del builder (arancio). Vedi README §"Accento".
  -->
  <div class="olo-border2">

    <!-- SPESSORE — stesso pattern di FieldBox (sides) -->
    <div class="olo-border2-row">
      <span class="olo-border2-lab">{{ t('Spess.') }}</span>
      <FieldBox
        class="olo-border2-width"
        mode="sides"
        :units="['px']"
        :slider-max="50"
        :slider-step="1"
        preview="none"
        :model-value="widthModel"
        @update:model-value="onWidth"
      />
    </div>

    <!-- STILE -->
    <div class="olo-border2-row">
      <span class="olo-border2-lab">{{ t('Stile') }}</span>
      <div class="olo-border2-selwrap">
        <select
          class="olo-border2-sel"
          :value="val.style"
          @change="emit({ style: $event.target.value })"
          :aria-label="t('Stile bordo')"
        >
          <option value="solid">{{ t('Solido') }}</option>
          <option value="dashed">{{ t('Tratteggiato') }}</option>
          <option value="dotted">{{ t('Punteggiato') }}</option>
          <option value="double">{{ t('Doppio') }}</option>
          <option value="groove">Groove</option>
          <option value="ridge">Ridge</option>
        </select>
        <svg class="olo-border2-chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
      </div>
    </div>

    <!-- COLORE — token-first (default 'primary') -->
    <div class="olo-border2-row">
      <span class="olo-border2-lab">{{ t('Colore') }}</span>
      <div class="olo-border2-color">
        <div class="olo-border2-swatch" :style="{ background: resolvedColor }" @click="focusColorInput">
          <input
            ref="colorInputRef"
            type="color"
            class="olo-border2-native"
            :value="colorHex"
            @input="emit({ color: $event.target.value })"
            :aria-label="t('Scegli colore')"
          />
        </div>
        <input
          type="text"
          class="olo-border2-coltext"
          :value="val.color"
          :placeholder="t('primary')"
          @change="emit({ color: $event.target.value })"
          @blur="emit({ color: $event.target.value })"
          :aria-label="t('Colore bordo')"
        />
      </div>
    </div>

    <!-- EFFETTO — opzionale: fold-in del vecchio pannello "Effetti bordo".
         Mostrare SOLO se la tile espone già un'opzione effetto bordo, riusando
         la sua chiave esistente (NON inventare una chiave nuova). -->
    <div class="olo-border2-row" v-if="showEffect">
      <span class="olo-border2-lab">{{ t('Effetto') }}</span>
      <div class="olo-border2-selwrap">
        <select
          class="olo-border2-sel"
          :value="effect"
          @change="$emit('update:effect', $event.target.value)"
          :aria-label="t('Effetto bordo')"
        >
          <option value="none">{{ t('Nessuno') }}</option>
          <option value="glow">{{ t('Bagliore') }}</option>
          <option value="inset">{{ t('Interno') }}</option>
        </select>
        <svg class="olo-border2-chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
      </div>
    </div>

    <!-- PEEK — anteprima del bordo reale (occhio gestito dal wrapper) -->
    <div class="olo-border2-peek">
      <span class="olo-border2-chip" :style="chipStyle"></span>
    </div>

  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { t } from '@/i18n';
import FieldBox from './FieldBox.vue';

const props = defineProps({
  // { top, right, bottom, left, linked, style, color }
  modelValue: { default: null },
  // effetto bordo opzionale (chiave separata, gestita dal genitore)
  effect: { type: String, default: 'none' },
  showEffect: { type: Boolean, default: false },
});
const emits = defineEmits(['update:modelValue', 'update:effect']);

const colorInputRef = ref(null);

const EMPTY = { top: 0, right: 0, bottom: 0, left: 0, linked: true, style: 'solid', color: 'primary' };

const val = computed(() => {
  const v = props.modelValue;
  if (v && typeof v === 'object') {
    return {
      top:    Math.max(0, parseInt(v.top)    || 0),
      right:  Math.max(0, parseInt(v.right)  || 0),
      bottom: Math.max(0, parseInt(v.bottom) || 0),
      left:   Math.max(0, parseInt(v.left)   || 0),
      linked: v.linked !== false,
      style:  v.style  || 'solid',
      color:  v.color  || '',
    };
  }
  return { ...EMPTY };
});

/* ── SPESSORE via FieldBox ──────────────────────────────────────
   FieldBox usa scalare quando "collegato", oggetto {top,right,bottom,left}
   quando "separato": combacia 1:1 col nostro modello. */
const widthModel = computed(() => {
  const { top, right, bottom, left, linked } = val.value;
  return linked ? top : { top, right, bottom, left };
});
function onWidth(v) {
  if (v && typeof v === 'object') {
    emit({ ...v, linked: false });
  } else {
    const n = Math.max(0, parseInt(v) || 0);
    emit({ top: n, right: n, bottom: n, left: n, linked: true });
  }
}

/* ── COLORE token-first ──────────────────────────────────────────
   `color` resta una stringa nel salvataggio (chiave invariata). Può essere
   un nome-token ('primary'…) o un literal ('#rrggbb'/'rgba(...)'). Per la
   resa risolviamo i token nella CSS var corrispondente. */
const TOKEN_VAR = {
  primary:   'var(--olo-color-primary)',
  secondary: 'var(--olo-color-secondary)',
  accent:    'var(--olo-color-accent)',
  dark:      'var(--olo-color-dark)',
  light:     'var(--olo-color-light)',
  text:      'var(--olo-color-text)',
};
const resolvedColor = computed(() => {
  const c = val.value.color || 'primary';
  return TOKEN_VAR[c] || c;
});
const colorHex = computed(() => {
  const c = val.value.color;
  if (!c || TOKEN_VAR[c]) return '#e1474f'; // token → fallback brand per il picker nativo
  if (c.startsWith('#') && c.length === 7) return c;
  if (c.startsWith('#') && c.length === 4) return '#' + c[1]+c[1]+c[2]+c[2]+c[3]+c[3];
  const m = c.match(/rgba?\((\d+),\s*(\d+),\s*(\d+)/);
  if (m) return '#' + [m[1],m[2],m[3]].map(n => parseInt(n).toString(16).padStart(2,'0')).join('');
  return '#000000';
});

/* ── PEEK ── */
const chipStyle = computed(() => {
  const { top, right, bottom, left, style } = val.value;
  const c = resolvedColor.value;
  const s = style || 'solid';
  return {
    borderTop:    `${Math.max(top, 1)}px ${s} ${c}`,
    borderRight:  `${Math.max(right, 1)}px ${s} ${c}`,
    borderBottom: `${Math.max(bottom, 1)}px ${s} ${c}`,
    borderLeft:   `${Math.max(left, 1)}px ${s} ${c}`,
  };
});

function emit(patch) {
  emits('update:modelValue', { ...val.value, ...patch });
}
function focusColorInput() {
  colorInputRef.value?.click();
}
</script>

<style scoped>
/* Accento CHROME del builder (arancio fisso). Vedi README §"Accento".
   Per allineare anche FieldBox, esporre lo stesso --olo-ui-accent a livello pannello. */
.olo-border2 {
  --ui: var(--olo-ui-accent, #e8622a);
  --ui-soft: color-mix(in srgb, var(--ui) 14%, #fff);
  --ui-line: color-mix(in srgb, var(--ui) 45%, #fff);
  --line: #e5e7eb;
  --ink: #1f2937;
  --faint: #94a3b8;
  display: flex;
  flex-direction: column;
  gap: 10px;
  padding: 2px 0;
  font-family: 'Work Sans', system-ui, sans-serif;
}

.olo-border2-row {
  display: flex;
  align-items: center;
  gap: 10px;
}
.olo-border2-lab {
  flex: 0 0 54px;
  font-size: 9px;
  font-weight: 700;
  letter-spacing: 0.06em;
  text-transform: uppercase;
  color: var(--faint);
}

/* lo spessore (FieldBox) occupa il resto della riga */
.olo-border2-width { flex: 1; }

/* select Stile / Effetto */
.olo-border2-selwrap { flex: 1; position: relative; }
.olo-border2-sel {
  width: 100%;
  height: 34px;
  padding: 0 30px 0 10px;
  border: 1px solid var(--line);
  border-radius: 9px;
  background: #fff;
  font: 500 13px 'Work Sans', sans-serif;
  color: var(--ink);
  outline: none;
  appearance: none;
  cursor: pointer;
  transition: border-color 0.15s;
}
.olo-border2-sel:focus { border-color: var(--ui); }
.olo-border2-chev {
  position: absolute;
  right: 10px;
  top: 50%;
  transform: translateY(-50%);
  width: 14px;
  height: 14px;
  color: var(--faint);
  pointer-events: none;
}

/* colore */
.olo-border2-color {
  flex: 1;
  display: flex;
  align-items: center;
  gap: 8px;
}
.olo-border2-swatch {
  position: relative;
  width: 34px;
  height: 34px;
  border-radius: 9px;
  border: 1px solid var(--line);
  flex-shrink: 0;
  cursor: pointer;
  overflow: hidden;
}
.olo-border2-native {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  opacity: 0;
  border: none;
  padding: 0;
  cursor: pointer;
}
.olo-border2-coltext {
  flex: 1;
  min-width: 0;
  height: 34px;
  border: 1px solid var(--line);
  border-radius: 9px;
  background: #fff;
  padding: 0 10px;
  font: 500 12px 'SF Mono', Monaco, monospace;
  color: var(--ink);
  outline: none;
  transition: border-color 0.15s;
}
.olo-border2-coltext:focus { border-color: var(--ui); }

/* peek */
.olo-border2-peek {
  margin-top: 4px;
  height: 62px;
  border-radius: 10px;
  background: repeating-conic-gradient(#f0f1f4 0 25%, #fff 0 50%) 50% / 14px 14px;
  display: flex;
  align-items: center;
  justify-content: center;
}
.olo-border2-chip {
  width: 90px;
  height: 40px;
  border-radius: 8px;
  background: #fff;
}
</style>
