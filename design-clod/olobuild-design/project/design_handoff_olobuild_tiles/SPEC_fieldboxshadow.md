# SPEC IMPLEMENTAZIONE — Controllo OMBRA (FieldBoxShadow) · OLObuild

> Documento **auto-contenuto** per Claude Code. Leggendo SOLO questo file hai tutto:
> contesto, codice attuale, codice target (incluso inline), contratto dati verificato,
> i due task, i guardrail e come verificare. Non servono altri file per implementare.
> (File di supporto, se li vuoi vedere: `REFERENCE_shadow-control.html` mostra la UI Oggi-vs-Coerente;
> `prototype/FieldBoxShadow.vue` è identico al codice TARGET incollato qui sotto.)

- **Repo:** `claudiovinco/olobuild` · branch `main` · stack **Vue 3 + Pinia + PHP** (build Vite, Tailwind con prefisso `mb-`).
- **i18n:** `import { t } from '@/i18n'`.
- **Regola d'oro del progetto:** le **chiavi salvate non cambiano mai** (i template esistenti devono continuare a rendere identici). Si cambia la UI di editing, non il formato dati.

---

## 1) Contesto in 5 righe
Il tab "Stile" del wrapper di ogni tile è dichiarativo in `src/config/elements/_styleFieldsBase.js`.
La sezione **Effetti** è un pannello unico `{ type: 'effects-stack' }` reso da **`StyleEffectsStack`**
(in `src/components/Builder/style-renderers/`), che raccoglie ombra, opacità, transform, text-shadow,
backdrop, mask + un **toggle globale Normale/Hover**. L'ombra ha due pezzi:
un **preset di livello** e un **oggetto custom**. Oggi il custom è 4 number-input ammucchiati;
lo ridisegniamo, e rendiamo il preset una **scala segmented** coerente.

## 2) Contratto dati — VERIFICATO (NON cambiarlo)
Da `src/config/elements/_styleFieldsBase.js`, mapping `shadow_block`:

```
style.shadow         = 'none' | 'sm' | 'md' | 'lg' | 'xl' | 'custom'   ← PRESET (lo rende StyleEffectsStack)
style.shadow_custom  = { h, v, blur, spread, color, inset }            ← OGGETTO (lo edita FieldBoxShadow)
hover                = style.hover.shadow + style.hover.shadow_custom   ← gestito dal toggle Normale/Hover dello stack
default oggetto      = { h:0, v:4, blur:10, spread:0, color:'rgba(0,0,0,0.15)', inset:false }
colore               = via <FieldColor> (token-aware, come oggi)
```
`h` = offset orizzontale, `v` = offset verticale (NON `x`/`y`). Il renderer PHP (`collect_hover_css`)
legge queste stesse chiavi: non toccarle.

## 3) Codice ATTUALE — `src/components/Builder/fields/FieldBoxShadow.vue` (da sostituire)
UI odierna: 4 `<input type="number">` in fila (H, V, Blur, Spread) + `<FieldColor>` + checkbox Inset
+ una riga di preview mono. Props `modelValue` (oggetto sopra), emit `update:modelValue`, default come sopra.
**Lo sostituiamo mantenendo identici props/emit/default** → è un rimpiazzo drop-in, zero migrazione.

## 4) TASK 1 — sostituire FieldBoxShadow.vue con questo (drop-in)
Incolla integralmente al posto di `src/components/Builder/fields/FieldBoxShadow.vue`.
Stesse props/emit/default dell'attuale → i dati non cambiano; cambia solo la UI
(righe compatte **slider + valore editabile**, inset come **switch**, preview CSS reale).

```vue
<template>
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
```

> **Nota tema (importante):** l'attuale `FieldBoxShadow.vue` usa classi Tailwind scure (`mb-bg-gray-700`…),
> mentre questo redesign usa CSS scoped chiaro coerente con `FieldBox`/`FieldBorder` ridisegnati.
> Se nel repo l'inspector è ANCORA scuro e i field redisegnati non sono ancora mergeati, **non creare
> un terzo stile**: mantieni questo layout/markup e aggancia i colori al tema corrente (token/vars
> dell'inspector). L'accento dei controlli (slider/switch/focus) è la CHROME del builder: arancio
> `--olo-ui-accent` (fallback `#e8622a`), FISSO. Il colore dell'ombra è CONTENUTO → resta su `FieldColor`.

## 5) TASK 2 — preset come "scala segmented" nello StyleEffectsStack
Nel renderer `StyleEffectsStack` (dove oggi `style.shadow` è un `<select>` none/sm/md/lg/xl/custom),
sostituisci il select con una **scala segmented** a 5 voci: **None · S · M · L · Custom**
(xl resta valido come valore ma può ricadere visivamente su L). Requisiti:
- ogni voce ha una **chip di anteprima dell'elevazione** usando la scala token ombre
  (`--ot-shadow-sm`, `--ot-shadow-md`, `--ot-shadow-lg`); "None" = nessuna; "Custom" = chip con accento.
- voce attiva evidenziata con l'accento CHROME (arancio); `role="radiogroup"` + `aria-checked`.
- selezionando **Custom** si monta `FieldBoxShadow` (Task 1) legato a `style.shadow_custom`
  (esattamente come avviene oggi quando `shadow === 'custom'`).
- default sensato **`'sm'`** (micro-ombra elegante), non `'none'` piatto — coerente con `oloTileDefaults`.
- il toggle **Normale/Hover** dello stack continua a indirizzare su `style.hover.shadow` /
  `style.hover.shadow_custom` (non duplicare la logica hover qui).

Layout di riferimento: colonna "Coerente" → blocco **LIVELLO** in `REFERENCE_shadow-control.html`.

## 6) Guardrail
- **Chiavi salvate INVARIATE** (vedi §2). Nessuna chiave nuova, nessuna migrazione.
- **CHROME vs CONTENUTO:** affordance del controllo = arancio CHROME fisso; colore-ombra = contenuto (FieldColor/token).
- **A11y:** `focus-visible` su ogni controllo, target ≥ 44px, `role/aria-*` come nel markup.
- **Default da fonte unica** (`oloTileDefaults`/`buildDefaults`): non ridichiarare default sparsi.
- **Un solo look:** allinea a FieldBox/FieldBorder ridisegnati (vedi nota tema in §4).

## 7) Definition of Done
- [ ] `FieldBoxShadow.vue` sostituito: il custom si edita dal blocco compatto, preview CSS corretta (inset incluso).
- [ ] `StyleEffectsStack` mostra la **scala segmented** per `style.shadow`; "Custom" apre il blocco.
- [ ] **Normale/Hover** scrive `style.hover.shadow` / `style.hover.shadow_custom`.
- [ ] **Nessuna regressione dati:** un template esistente con `shadow`/`shadow_custom` rende IDENTICO a prima.
- [ ] focus-visible + aria ok; accento arancio sui controlli; colore-ombra via FieldColor.
- [ ] Aggiorna `TILE_PROGRESS.md` (avanzamento controlli inspector).

## 8) Come verificare (rapido)
1. Inserisci una tile (es. Iconbox/Button), tab Stile → Effetti: vedi la scala segmented, default S.
2. Passa a **Custom**: compare il blocco; muovi gli slider → la preview CSS in fondo si aggiorna
   (`Hpx Vpx blur spread color`, con `inset ` davanti se attivo).
3. Attiva **Hover** dal toggle dello stack, cambia l'ombra: viene scritta su `style.hover.shadow*`.
4. Salva, ricarica: i valori persistono. Apri un template salvato PRIMA della modifica: resa identica.
