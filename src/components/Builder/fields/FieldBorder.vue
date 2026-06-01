<template>
  <!--
    FieldBorder — controllo Bordo compatto, in linea con FieldBox (handoff
    "olobuild_bordercontrol"). Una riga per lo SPESSORE (collega/separa + slider
    + valore, 4 lati on-demand — riusa FieldBox in mode="sides"), poi Stile e
    Colore allineati, e un peek che mostra il bordo reale su sfondo a scacchi.
    Sostituisce la vecchia croce 2×2 + il pannello "Effetti bordo" separato.

    CONTRATTO DATI INVARIATO: modelValue = { top, right, bottom, left, linked,
    style, color }. Nessuna chiave nuova: i template esistenti continuano a
    funzionare. Il colore è "token-first" SOLO nella resa (swatch/peek): il
    valore salvato resta una stringa (hex/rgba o '' = nessun bordo), come oggi —
    il frontend PHP usa il colore verbatim e tratta '' come bordo inattivo.

    Hover e breakpoint NON sono gestiti qui: come per FieldBox li avvolge
    InspectorField (occhio Normale/Hover sulla chiave `border_hover`, switch
    device via DeviceSwitch). Lo switch device / toggle hover del mockup sono
    chrome del wrapper, mostrati solo per contesto.

    Accento = CHROME del builder (arancio fisso) via --olo-ui-accent, NON il
    primario tile. Definito qui sul root .olo-border2 così lo eredita anche il
    FieldBox interno dello spessore (vedi FieldBox.vue: --olo-bf-accent).
  -->
  <div class="olo-border2">

    <!-- SPESSORE — stesso pattern di FieldBox (sides): collega/separa + slider + valore -->
    <div class="olo-border2-row">
      <span class="olo-border2-lab">{{ t('Spessore') }}</span>
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
          <option value="groove">{{ t('Incasso') }}</option>
          <option value="ridge">{{ t('Rilievo') }}</option>
        </select>
        <svg class="olo-border2-chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
      </div>
    </div>

    <!-- COLORE — FieldColor standard del builder: palette globali, pulsante colori
         globali (globe), picker nativo e slider Alfa. Emette sempre un valore CSS
         valido (hex / rgba / var(--olo-color-*)) → coerente col render PHP del bordo. -->
    <div class="olo-border2-row olo-border2-row--top">
      <span class="olo-border2-lab">{{ t('Colore') }}</span>
      <div class="olo-border2-colorwrap">
        <FieldColor :modelValue="val.color || ''" @update:modelValue="emit({ color: $event })" />
      </div>
    </div>

    <!-- PEEK — anteprima del bordo reale (l'occhio Normale/Hover è del wrapper).
         Disattivabile (showPeek=false) quando il field è ospitato in un pannello
         che fornisce la propria anteprima, es. StyleBoxStack "Spazi & Bordi". -->
    <div class="olo-border2-peek" v-if="showPeek">
      <span class="olo-border2-chip" :style="chipStyle"></span>
    </div>

  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';
import FieldBox from './FieldBox.vue';
import FieldColor from './FieldColor.vue';

const props = defineProps({
  // { top, right, bottom, left, linked, style, color }
  modelValue: { default: null },
  // Mostra il peek interno. false quando un pannello esterno fornisce l'anteprima.
  showPeek: { type: Boolean, default: true },
});
const emits = defineEmits(['update:modelValue']);

const EMPTY = { top: 0, right: 0, bottom: 0, left: 0, linked: true, style: 'solid', color: '' };

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
   FieldBox usa uno scalare quando "collegato" e un oggetto
   {top,right,bottom,left} quando "separato": combacia 1:1 col nostro modello. */
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

/* ── PEEK ──
   Anteprima del controllo: usa il colore COSÌ COM'È (FieldColor emette hex/rgba o
   var(--olo-color-*), tutti CSS validi). Vuoto → 'transparent' (nessun bordo,
   coerente con il render). Hairline minimo (1px) per dare un'idea con spessore 0. */
const chipStyle = computed(() => {
  const { top, right, bottom, left, style, color } = val.value;
  const c = color || 'transparent';
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
</script>

<style scoped>
/* Accento CHROME del builder (arancio fisso) — vedi README §"Accento".
   --olo-ui-accent è ereditato dal FieldBox interno (spessore) via --olo-bf-accent,
   così l'intero blocco bordo parla la stessa lingua cromatica. */
.olo-border2 {
  --ui: var(--olo-ui-accent, #e8622a);
  --line: #e5e7eb;
  --ink: #1f2937;
  --faint: #94a3b8;
  display: flex;
  flex-direction: column;
  gap: 10px;
  padding: 2px 0;
}

.olo-border2-row {
  display: flex;
  align-items: center;
  gap: 10px;
}
.olo-border2-lab {
  flex: 0 0 56px;
  font-size: 9px;
  font-weight: 700;
  letter-spacing: 0.06em;
  text-transform: uppercase;
  color: var(--faint);
  white-space: nowrap;
}

/* lo spessore (FieldBox) occupa il resto della riga */
.olo-border2-width { flex: 1; min-width: 0; }

/* select Stile */
.olo-border2-selwrap { flex: 1; position: relative; min-width: 0; }
.olo-border2-sel {
  width: 100%;
  height: 34px;
  padding: 0 30px 0 10px;
  border: 1px solid var(--line);
  border-radius: 9px;
  background: #fff;
  font-size: 13px;
  font-weight: 500;
  color: var(--ink);
  outline: none;
  appearance: none;
  -webkit-appearance: none;
  cursor: pointer;
  transition: border-color 0.15s, box-shadow 0.15s;
}
.olo-border2-sel:focus,
.olo-border2-sel:focus-visible {
  border-color: var(--ui);
  box-shadow: 0 0 0 3px color-mix(in srgb, var(--ui) 20%, transparent);
}
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

/* colore — la riga ospita FieldColor (blocco multi-riga): label allineata in alto */
.olo-border2-row--top { align-items: flex-start; }
.olo-border2-row--top .olo-border2-lab { padding-top: 10px; }
.olo-border2-colorwrap {
  flex: 1;
  min-width: 0;
}

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
  box-sizing: border-box;
}
</style>
