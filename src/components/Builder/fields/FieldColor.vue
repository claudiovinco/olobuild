<template>
  <div class="mb-space-y-1">
    <!-- Global Colors swatches (espansi al bisogno via icona globe) -->
    <div v-if="showGlobals" class="fc-global">
      <div class="fc-global-swatches">
        <span
          v-for="sc in swatchColors"
          :key="sc.id"
          class="fc-swatch-wrap"
        >
          <button
            type="button"
            class="fc-swatch"
            :class="{ 'fc-swatch--active': isSwatchSelected(sc.id) }"
            :style="{ background: sc.value }"
            :title="sc.label + ' — var(--olo-color-' + sc.id + ')'"
            @click="selectColor(sc.id)"
          ></button>
          <button
            v-if="sc.quick"
            type="button"
            class="fc-swatch-del"
            :title="t('Rimuovi colore')"
            @click.stop="removeQuickColor(sc.id)"
          >{{ t('&times;') }}</button>
        </span>
        <button
          type="button"
          class="fc-swatch fc-swatch--add"
          :title="puoAggiungere ? t('Aggiungi colore corrente ai globali') : t('Nessun colore concreto da aggiungere')"
          :disabled="!puoAggiungere"
          @click="addCurrentAsGlobal"
        >+</button>
      </div>
    </div>

    <div class="fc-hex-wrap">
      <!-- La pastiglia dipinge il valore VERO sopra una scacchiera; l'input nativo,
           che sa mostrare solo un #rrggbb pieno, è invisibile e fa da pulsante. -->
      <label class="fc-swatch-inline" :class="'fc-swatch-inline--' + colore.kind" :title="titoloSwatch">
        <span class="fc-swatch-face" aria-hidden="true">
          <span v-if="colore.paint" class="fc-swatch-paint" :style="{ background: colore.paint }"></span>
        </span>
        <input
          type="color"
          :value="hexPart"
          @input="onHexChange($event.target.value)"
          @change="onHexChange($event.target.value)"
          class="fc-swatch-native"
          :aria-label="etichettaSwatch"
        />
      </label>
      <input
        type="text"
        :value="displayValue"
        :title="modelValue || ''"
        :placeholder="t('Predefinito')"
        spellcheck="false"
        @focus="scrivendo = true"
        @blur="scrivendo = false"
        @change="onTextChange($event.target.value)"
        class="fc-hex-input"
        :class="{ 'fc-hex-input--nome': !modelValue || (!scrivendo && nomeToken) }"
      />
      <button
        type="button"
        class="fc-globe-btn"
        :class="{ 'fc-globe-btn--active': showGlobals || isGlobalActive }"
        :title="t('Colori globali')"
        :aria-pressed="showGlobals"
        @click="showGlobals = !showGlobals"
      >
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <circle cx="12" cy="12" r="10"/>
          <path d="M2 12h20"/>
          <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
        </svg>
      </button>
    </div>
    <!-- L'alfa esiste solo per un colore concreto: su un valore vuoto o su
         currentColor non c'è niente da rendere trasparente, e un 100% lo
         affermerebbe il falso. -->
    <div class="fc-alpha-row" :title="haColore ? null : t('Nessun colore concreto: l\'alfa non si applica')">
      <span class="fc-alpha-label">{{ t('Alfa') }}</span>
      <input
        type="range"
        :value="alphaPct"
        :disabled="!haColore"
        @input="onAlphaChange(parseInt($event.target.value))"
        min="0" max="100" step="5"
        class="fc-alpha-range"
        :style="haColore ? { '--fc-rgb': previewRgb } : null"
        :aria-label="t('Opacità colore')"
        :aria-valuetext="haColore ? alphaPct + '%' : t('Non applicabile')"
      />
      <span class="fc-alpha-val">{{ haColore ? alphaPct + '%' : '—' }}</span>
    </div>
  </div>
</template>

<script setup>
import { t } from '@/i18n';
import { computed, ref } from 'vue';
import { useStylesStore } from '@/stores/styles';
import { tokenParts, buildSwatchColors, tokenLabel, describeColor } from '@/utils/colorToken';

const props = defineProps({
  // Vuoto = nessun colore impostato: decide la tile. Il vecchio default
  // '#000000' faceva vedere nero anche un campo mai toccato.
  modelValue: { type: String, default: '' },
});
const emit = defineEmits(['update:modelValue']);

const stylesStore = useStylesStore();

// Swatch mostrate: ruoli del tema (olo_styles.colors) + globali custom (accent, "+").
// La lista e la lettura dei token stanno in @/utils/colorToken: le usa anche la
// sintesi di FieldTypography, che deve dipingere lo stesso colore.
const swatchColors = computed(() => buildSwatchColors(stylesStore));

function resolveSwatch(id) {
  const sc = swatchColors.value.find(c => c.id === id);
  return sc ? sc.value : null;
}

const showGlobals = ref(false);

const isGlobalActive = computed(() => {
  const cur = (props.modelValue || '').toLowerCase();
  if (!cur) return false;
  if (cur.startsWith('var(--olo-color-')) return true;
  return swatchColors.value.some(sc => sc.value?.toLowerCase() === cur);
});

function isSwatchSelected(id) {
  const cur = (props.modelValue || '').toLowerCase();
  if (!cur) return false;
  // Il confronto sta sull'ID del token: `var(--olo-color-dark)` e
  // `var(--olo-color-dark, #16263d)` sono lo stesso colore scelto.
  const t = tokenParts(cur);
  if (t && t.id === String(id).toLowerCase()) return true;
  const hex = resolveSwatch(id);
  return !!hex && cur === hex.toLowerCase();
}

// Seleziona un colore come TOKEN var(--olo-color-id): così segue la palette globale.
function selectColor(id) {
  emit('update:modelValue', `var(--olo-color-${id})`);
}

function toOutput(hex, alpha) {
  if (alpha >= 1) return hex;
  const h = hex.replace('#', '');
  const rp = parseInt(h.substring(0, 2), 16); const r = !isNaN(rp) ? rp : 0;
  const gp = parseInt(h.substring(2, 4), 16); const g = !isNaN(gp) ? gp : 0;
  const bp = parseInt(h.substring(4, 6), 16); const b = !isNaN(bp) ? bp : 0;
  return `rgba(${r}, ${g}, ${b}, ${alpha})`;
}

// Cosa dipinge il valore — colore concreto (anche `transparent`, nero ad alfa 0),
// sfumatura, parola di contesto, vuoto, irrisolvibile. La lettura sta in
// colorToken.js: il vecchio parser ripiegava su #000000 per tutto ciò che non
// era hex/rgba/token, e `transparent` diventava un quadrato nero con alfa 100%.
const colore = computed(() => describeColor(props.modelValue, stylesStore));
const haColore = computed(() => colore.value.kind === 'color');

// L'input nativo accetta solo #rrggbb: dove un colore non c'è apre il selettore
// sul nero, ma quel nero non si vede — la pastiglia visibile è quella dipinta.
const hexPart = computed(() => (haColore.value
  ? '#' + colore.value.rgb.map(c => c.toString(16).padStart(2, '0')).join('')
  : '#000000'));
const alphaPct = computed(() => (haColore.value ? Math.round(colore.value.alpha * 100) : 0));
// Terna "r, g, b" del colore corrente (senza alfa) per il gradiente della barra Alfa.
const previewRgb = computed(() => (haColore.value ? colore.value.rgb.join(', ') : ''));

// Cosa la pastiglia non può dipingere, detto a parole (tooltip + lettore di schermo).
const statoColore = computed(() => {
  const c = colore.value;
  switch (c.kind) {
    case 'color': return c.alpha === 0 ? t('Trasparente') : '';
    case 'empty': return t('Nessun colore impostato: vale quello predefinito della tile');
    case 'keyword': return String(props.modelValue).trim() + ': ' + t('dipende da dove si trova, qui non si può mostrare');
    case 'paint': return t('Sfumatura o spazio colore esteso: l\'alfa non si regola da qui');
    default:
      if (c.reason === 'token') return t('Token fuori dalla palette del builder: qui non si può mostrare');
      if (c.reason === 'var') return t('Variabile CSS che qui non si può risolvere');
      return t('Non è un colore CSS valido');
  }
});
const titoloSwatch = computed(() => statoColore.value || t('Scegli colore'));
const etichettaSwatch = computed(() => (statoColore.value
  ? t('Scegli colore') + ' — ' + statoColore.value
  : t('Scegli colore')));

// Un token scritto per esteso non ci sta e si legge a meta':
// «var(--olo-color-muted-co…». Il nome del colore lo abbiamo gia' negli
// swatch, quindi a riposo si mostra quello; il token completo resta nel
// tooltip e torna nel campo appena lo si mette a fuoco, perche' li' si scrive.
const scrivendo = ref(false);
const nomeToken = computed(() => tokenLabel(props.modelValue, stylesStore));
const displayValue = computed(() => {
  if (!scrivendo.value && nomeToken.value) return nomeToken.value;
  return props.modelValue || '';
});

function onHexChange(hex) {
  // Un colore scelto dal selettore si deve vedere: da `transparent` (o da un
  // colore ad alfa 0) si riparte opachi, se no la scelta resterebbe invisibile.
  const alpha = haColore.value && colore.value.alpha > 0 ? colore.value.alpha : 1;
  emit('update:modelValue', toOutput(hex, alpha));
}

function onAlphaChange(pct) {
  if (!haColore.value) return;
  // Da un token si passa al colore risolto, con la nuova alfa.
  emit('update:modelValue', toOutput(hexPart.value, pct / 100));
}

function onTextChange(val) {
  // Accept hex, rgba, and var(--olo-color-*) typed manually
  emit('update:modelValue', val);
}

// Fra i globali entra solo un colore che si vede: da un valore vuoto, da
// currentColor o da `transparent` il vecchio «+» aggiungeva un #000000.
const puoAggiungere = computed(() => haColore.value && colore.value.alpha > 0);

/**
 * Add the current color as a new global color and persist immediately.
 */
async function addCurrentAsGlobal() {
  if (!puoAggiungere.value) return;
  const hex = hexPart.value;
  // Check if this hex already exists in globals
  const existing = (stylesStore.globalColors || []).find(
    gc => gc.value.toLowerCase() === hex.toLowerCase()
  );
  if (existing) {
    // Already exists — just select it
    selectColor(existing.id);
    return;
  }
  const id = 'c' + Date.now().toString(36);
  const label = hex.toUpperCase();
  const newColors = [...(stylesStore.globalColors || []), { id, label, value: hex, quick: true }];
  stylesStore.setGlobalColors(newColors);
  await stylesStore.saveGlobalColors();
  // Auto-select the newly added color
  selectColor(id);
}

/**
 * Remove a quick-added global color and persist.
 */
async function removeQuickColor(colorId) {
  // Il colore risolto si legge PRIMA di togliere il globale: dopo, il token non
  // si risolve più e al suo posto usciva un #000000.
  const eraScelto = isSwatchSelected(colorId);
  const risolto = haColore.value ? toOutput(hexPart.value, colore.value.alpha) : '';
  const newColors = (stylesStore.globalColors || []).filter(gc => gc.id !== colorId);
  stylesStore.setGlobalColors(newColors);
  await stylesStore.saveGlobalColors();
  // If this color was selected, revert to its resolved color
  if (eraScelto && risolto) {
    emit('update:modelValue', risolto);
  }
}
</script>

<style scoped>
.fc-global {
  margin-bottom: 4px;
}

.fc-global-label {
  font-size: 9px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  color: #9ca3af;
  margin-bottom: 4px;
}

.fc-global-swatches {
  display: flex;
  flex-wrap: wrap;
  gap: 3px;
  align-items: flex-start;
}

.fc-swatch-wrap {
  position: relative;
  display: inline-flex;
}

.fc-swatch-del {
  display: none;
  position: absolute;
  top: -5px;
  right: -5px;
  width: 11px;
  height: 11px;
  border-radius: 50%;
  background: #ef4444;
  color: #fff;
  font-size: 8px;
  line-height: 1;
  border: none;
  cursor: pointer;
  padding: 0;
  align-items: center;
  justify-content: center;
  z-index: 1;
}
.fc-swatch-wrap:hover .fc-swatch-del {
  display: flex;
}

.fc-swatch {
  width: 14px;
  height: 14px;
  border-radius: 3px;
  border: 1.5px solid transparent;
  cursor: pointer;
  padding: 0;
  transition: border-color 0.15s, transform 0.1s;
  box-shadow: 0 0 0 0.5px rgba(0, 0, 0, 0.12);
}

.fc-swatch:hover {
  transform: scale(1.15);
  border-color: rgba(255, 255, 255, 0.5);
}

.fc-swatch--active {
  border-color: var(--olo-ui-accent, #e8622a);
  box-shadow: 0 0 0 1px var(--olo-ui-accent, #e8622a);
}

.fc-swatch--add {
  background: #f3f4f6;
  color: #9ca3af;
  font-size: 11px;
  font-weight: 700;
  line-height: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: none;
  border: 1px dashed #d1d5db;
}
.fc-swatch--add:hover:not(:disabled) {
  background: #e5e7eb;
  color: #374151;
  border-color: #9ca3af;
  transform: scale(1.15);
}
.fc-swatch--add:disabled {
  opacity: 0.45;
  cursor: not-allowed;
}

/* ── Hex wrap (swatch inline + text input) ── */
.fc-hex-wrap {
  display: flex;
  align-items: center;
  border: 1px solid #e5e7eb;
  border-radius: 6px;
  overflow: hidden;
  background: #fff;
}
/* ── Pastiglia: il valore VERO, dipinto sopra una scacchiera ──
   L'input nativo sa mostrare solo un #rrggbb pieno: `transparent`, un'alfa o una
   parola chiave diventavano un quadrato nero. Ora l'input è invisibile e fa solo
   da pulsante; si vede la faccia, con il colore sopra la scacchiera. */
.fc-swatch-inline {
  position: relative;
  flex-shrink: 0;
  box-sizing: border-box;
  width: 32px;
  height: 32px;
  padding: 3px;
  cursor: pointer;
}
.fc-swatch-face {
  position: relative;
  display: block;
  width: 100%;
  height: 100%;
  border-radius: 3px;
  overflow: hidden;
  background:
    linear-gradient(45deg, #cbd5e1 25%, transparent 0, transparent 75%, #cbd5e1 0) 0 0 / 8px 8px,
    linear-gradient(45deg, #cbd5e1 25%, transparent 0, transparent 75%, #cbd5e1 0) 4px 4px / 8px 8px,
    #fff;
}
.fc-swatch-paint {
  position: absolute;
  inset: 0;
}
/* Filo sopra il colore: un bianco su fondo bianco resta leggibile. */
.fc-swatch-face::after {
  content: '';
  position: absolute;
  inset: 0;
  box-sizing: border-box;
  border-radius: inherit;
  box-shadow: inset 0 0 0 1px rgba(0, 0, 0, 0.12);
  pointer-events: none;
}
/* Vuoto: nessun colore impostato. Niente scacchiera — quella vuol dire
   «trasparente», che è un valore — ma un riquadro tratteggiato. */
.fc-swatch-inline--empty .fc-swatch-face {
  background: #fff;
}
.fc-swatch-inline--empty .fc-swatch-face::after {
  box-shadow: none;
  border: 1px dashed #cbd5e1;
}
/* Parola di contesto (currentColor, inherit…): tratteggio neutro, il colore
   lo decide la pagina. Irrisolvibile o non valido: lo stesso, in ambra. */
.fc-swatch-inline--keyword .fc-swatch-face {
  background: repeating-linear-gradient(135deg, #e5e7eb 0 2px, #fff 2px 5px);
}
.fc-swatch-inline--unknown .fc-swatch-face {
  background: repeating-linear-gradient(135deg, #fcd34d 0 2px, #fff 2px 5px);
}
.fc-swatch-native {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  margin: 0;
  padding: 0;
  border: 0;
  opacity: 0;
  cursor: pointer;
}
.fc-swatch-inline:has(.fc-swatch-native:focus-visible) .fc-swatch-face {
  outline: 2px solid var(--olo-ui-accent, #e8622a);
  outline-offset: 1px;
}
.fc-hex-input::placeholder {
  color: #9ca3af;
}
.fc-hex-input--nome {
  font-family: inherit;
  letter-spacing: 0;
}
.fc-hex-input {
  flex: 1;
  min-width: 0;
  border: none;
  background: transparent;
  padding: 4px 8px 4px 0;
  font-size: 13px;
  color: #374151;
  outline: none;
  font-family: 'SF Mono', Monaco, 'Cascadia Code', monospace;
}

.fc-globe-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 26px;
  height: 32px;
  margin-right: 2px;
  padding: 0;
  border: none;
  background: transparent;
  color: #9ca3af;
  cursor: pointer;
  transition: color 0.15s, background-color 0.15s;
  flex-shrink: 0;
  border-radius: 4px;
}
.fc-globe-btn:hover {
  color: #374151;
  background: #f3f4f6;
}
.fc-globe-btn--active {
  color: var(--olo-ui-accent, #e8622a);
}
.fc-globe-btn--active:hover {
  color: var(--olo-ui-accent, #e8622a);
  background: rgba(232, 98, 42, 0.08);
}

/* ── Barra Alfa (checkerboard + gradiente trasparenza→colore) ── */
.fc-alpha-row {
  display: flex;
  align-items: center;
  gap: 6px;
  margin-top: 2px;
}
.fc-alpha-label {
  font-size: 10px;
  color: #9ca3af;
  flex-shrink: 0;
}
.fc-alpha-val {
  font-size: 10px;
  color: #9ca3af;
  width: 30px;
  text-align: right;
  flex-shrink: 0;
  font-variant-numeric: tabular-nums;
}
.fc-alpha-range {
  -webkit-appearance: none;
  appearance: none;
  flex: 1;
  min-width: 0;
  height: 14px;
  border-radius: 7px;
  outline: none;
  cursor: pointer;
  border: 1px solid #d1d5db;
  /* alto → basso: gradiente alfa, scacchiera (2 layer), base bianca */
  background:
    linear-gradient(90deg, rgba(var(--fc-rgb, 0, 0, 0), 0), rgba(var(--fc-rgb, 0, 0, 0), 1)),
    linear-gradient(45deg, #cbd5e1 25%, transparent 0, transparent 75%, #cbd5e1 0) 0 0 / 10px 10px,
    linear-gradient(45deg, #cbd5e1 25%, transparent 0, transparent 75%, #cbd5e1 0) 5px 5px / 10px 10px,
    #fff;
}
.fc-alpha-range::-webkit-slider-thumb {
  -webkit-appearance: none;
  width: 16px;
  height: 16px;
  border-radius: 50%;
  background: #fff;
  border: 1px solid rgba(0, 0, 0, 0.35);
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.35);
  cursor: pointer;
}
.fc-alpha-range::-moz-range-thumb {
  width: 16px;
  height: 16px;
  border-radius: 50%;
  background: #fff;
  border: 1px solid rgba(0, 0, 0, 0.35);
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.35);
  cursor: pointer;
}
.fc-alpha-range::-moz-range-track {
  height: 14px;
  border-radius: 7px;
  background: transparent;
}
.fc-alpha-range:focus-visible {
  box-shadow: 0 0 0 2px rgba(232, 98, 42, 0.5);
}
/* Senza un colore concreto la barra è spenta: sola scacchiera, niente cursore. */
.fc-alpha-range:disabled {
  cursor: not-allowed;
  opacity: 0.45;
  background:
    linear-gradient(45deg, #cbd5e1 25%, transparent 0, transparent 75%, #cbd5e1 0) 0 0 / 10px 10px,
    linear-gradient(45deg, #cbd5e1 25%, transparent 0, transparent 75%, #cbd5e1 0) 5px 5px / 10px 10px,
    #fff;
}
.fc-alpha-range:disabled::-webkit-slider-thumb {
  visibility: hidden;
}
.fc-alpha-range:disabled::-moz-range-thumb {
  visibility: hidden;
}
</style>
