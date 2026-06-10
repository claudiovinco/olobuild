<template>
  <div class="cfg-page-head">
    <div>
      <h1>{{ t('Tipografia') }} <em>{{ t('globale') }}</em></h1>
      <p>{{ t('Coppia di font, scala modulare, pesi e interlinea. Si applica a tutti i blocchi di testo del sito — sovrascrivibile a livello di pagina.') }}</p>
    </div>
    <div class="head-actions">
      <button class="cfg-btn cfg-btn-secondary" @click="uploadCustomFont">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
        {{ t('Carica font custom') }}
      </button>
    </div>
  </div>

  <div class="cfg-card">
    <div class="cfg-card-head">
      <div class="head-ic">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7V5h16v2"/><path d="M9 20h6"/><path d="M12 5v15"/></svg>
      </div>
      <div>
        <h3>{{ t('Coppia di font') }}</h3>
        <p>{{ t('Display per titoli, body per testo corrente. Tieni 2 famiglie max per buona leggibilità.') }}</p>
      </div>
    </div>
    <div class="cfg-card-body">
      <div class="font-pair-grid">
        <div class="font-card">
          <div class="font-card-label">{{ t('Display · titoli') }}</div>
          <div class="font-card-preview" :style="{ fontFamily: displayFontFamily }">Abc · Æg</div>
          <CfgSelect :model-value="display.family" :options="DISPLAY_FONTS" @update:model-value="setDisplay('family', $event)" />
          <div class="font-pills">
            <span class="cfg-pill off">italic</span>
            <span class="cfg-pill off">400</span>
            <span class="cfg-pill ok">+ Google Fonts</span>
          </div>
        </div>
        <div class="font-card">
          <div class="font-card-label">{{ t('Body · testo corrente') }}</div>
          <div class="font-card-preview body" :style="{ fontFamily: bodyFontFamily }">Abc · 123</div>
          <CfgSelect :model-value="body.family" :options="BODY_FONTS" @update:model-value="setBody('family', $event)" />
          <div class="font-pills">
            <span class="cfg-pill off">400</span>
            <span class="cfg-pill off">500</span>
            <span class="cfg-pill off">600</span>
            <span class="cfg-pill off">700</span>
          </div>
        </div>
        <div class="font-card">
          <div class="font-card-label">{{ t('Mono · codice e dati') }}</div>
          <div class="font-card-preview body" :style="{ fontFamily: monoFontFamily }">{ 01 · Aa }</div>
          <CfgSelect :model-value="mono.family" :options="MONO_FONTS" @update:model-value="setMono('family', $event)" />
          <div class="font-pills">
            <span class="cfg-pill ok">var(--olo-font-family-mono)</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="cfg-card">
    <div class="cfg-card-head">
      <div class="head-ic">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 2 9 5-9 5-9-5 9-5z"/><path d="m3 17 9 5 9-5"/><path d="m3 12 9 5 9-5"/></svg>
      </div>
      <div>
        <h3>{{ t('Scala tipografica') }}</h3>
        <p>{{ t('Ratio matematico tra le grandezze. Anteprima live a destra.') }}</p>
      </div>
    </div>
    <div class="cfg-card-body tight">
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('Dimensione base') }}</label>
          <div class="hint">{{ t('Punto di partenza, di solito 16px per il corpo del testo.') }}</div>
        </div>
        <div class="control-col">
          <CfgNumber :model-value="scale.base" :min="10" :max="32" suffix="px" @update:model-value="setScale('base', $event)" />
        </div>
      </div>
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('Ratio scala') }}</label>
          <div class="hint">{{ t('Più alto = differenza più marcata fra body e h1.') }}</div>
        </div>
        <div class="control-col">
          <CfgSelect size="md" :model-value="String(scale.ratio)" :options="RATIO_OPTIONS" @update:model-value="setScale('ratio', parseFloat($event))" />
        </div>
      </div>
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('Interlinea body') }}</label>
          <div class="hint">{{ t('Altezza riga del testo corrente.') }}</div>
        </div>
        <div class="control-col">
          <div class="cfg-slider">
            <input type="range" min="1.1" max="2" step="0.05" :value="scale.lineHeight" @input="setScale('lineHeight', parseFloat($event.target.value))" />
            <span class="val">{{ scale.lineHeight.toFixed(2) }}</span>
          </div>
        </div>
      </div>
      <div class="cfg-row no-divider">
        <div class="label-col">
          <label>{{ t('Anteprima scala') }}</label>
        </div>
        <div class="control-col">
          <div class="scale-preview">
            <div :style="{ fontFamily: displayFontFamily, fontSize: sizes.h1 + 'px', color: 'var(--c-navy)', lineHeight: 1 }">H1 — {{ sizes.h1 }} / 1.0</div>
            <div :style="{ fontFamily: displayFontFamily, fontSize: sizes.h2 + 'px', color: 'var(--c-navy)', lineHeight: 1.05 }">H2 — {{ sizes.h2 }} / 1.05</div>
            <div :style="{ fontFamily: bodyFontFamily, fontWeight: 600, fontSize: sizes.h3 + 'px', color: 'var(--c-navy)' }">H3 — {{ sizes.h3 }} / 1.2</div>
            <div :style="{ fontFamily: bodyFontFamily, fontSize: scale.base + 'px', lineHeight: scale.lineHeight, color: 'var(--c-text)' }">Body — {{ scale.base }} / {{ scale.lineHeight.toFixed(2) }} — Lorem ipsum dolor sit amet consectetur adipiscing elit.</div>
            <div :style="{ fontFamily: bodyFontFamily, fontSize: sizes.small + 'px', color: 'var(--c-text-mute)' }">Small — {{ sizes.small }} / 1.5 — Caption, meta, didascalie.</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, inject, onMounted, onBeforeUnmount } from 'vue';
import { t } from '@/i18n';
import CfgSelect from './controls/CfgSelect.vue';
import CfgNumber from './controls/CfgNumber.vue';

const showToast = inject('showToast', () => {});
const setDirty  = inject('setDirty',  () => {});

const DISPLAY_FONTS = [
  { value: 'Instrument Serif',  label: 'Instrument Serif' },
  { value: 'Playfair Display',  label: 'Playfair Display' },
  { value: 'Fraunces',          label: 'Fraunces' },
  { value: 'DM Serif Display',  label: 'DM Serif Display' },
  { value: 'Cormorant Garamond', label: 'Cormorant Garamond' },
  { value: 'Lora',              label: 'Lora' },
];
const BODY_FONTS = [
  { value: 'Work Sans',         label: 'Work Sans' },
  { value: 'Inter',             label: 'Inter' },
  { value: 'Manrope',           label: 'Manrope' },
  { value: 'Plus Jakarta Sans', label: 'Plus Jakarta Sans' },
  { value: 'system-ui',         label: 'System UI stack' },
];
// Ruolo mono: '' = nessun override → le tile usano il fallback del proprio
// var(--olo-font-family-mono, …). Un valore = emesso come var globale.
const MONO_FONTS = [
  { value: '',                 label: 'Predefinito (per tile)' },
  { value: 'JetBrains Mono',   label: 'JetBrains Mono' },
  { value: 'Space Mono',       label: 'Space Mono' },
  { value: 'IBM Plex Mono',    label: 'IBM Plex Mono' },
  { value: 'Fira Code',        label: 'Fira Code' },
  { value: 'Source Code Pro',  label: 'Source Code Pro' },
];
const RATIO_OPTIONS = [
  { value: '1.125', label: '1.125 · Major Second' },
  { value: '1.2',   label: '1.2 · Minor Third' },
  { value: '1.25',  label: '1.25 · Major Third' },
  { value: '1.333', label: '1.333 · Perfect Fourth' },
  { value: '1.414', label: '1.414 · Augmented Fourth' },
  { value: '1.5',   label: '1.5 · Perfect Fifth' },
];

const display = ref({ family: 'Instrument Serif', weight: 400 });
const body    = ref({ family: 'Work Sans', weight: 500 });
const mono    = ref({ family: '' });
const scale   = ref({ base: 16, ratio: 1.25, lineHeight: 1.55 });
const fullStyles = ref({});   // tutto olo_styles, per non perdere gli altri blocchi al PUT

const displayFontFamily = computed(() => `'${display.value.family}', serif`);
const bodyFontFamily    = computed(() => `'${body.value.family}', sans-serif`);
const monoFontFamily    = computed(() => mono.value.family ? `'${mono.value.family}', monospace` : 'ui-monospace, Menlo, Consolas, monospace');

const sizes = computed(() => {
  const b = scale.value.base;
  const r = scale.value.ratio;
  return {
    small: Math.round(b / r),
    h3:    Math.round(b * Math.pow(r, 2)),
    h2:    Math.round(b * Math.pow(r, 4)),
    h1:    Math.round(b * Math.pow(r, 6)),
  };
});

function setDisplay(k, v) { display.value[k] = v; setDirty(true); ensureFontLoaded(v); }
function setBody(k, v)    { body.value[k]    = v; setDirty(true); ensureFontLoaded(v); }
function setMono(k, v)    { mono.value[k]    = v; setDirty(true); if (v) ensureFontLoaded(v); }
function setScale(k, v)   { scale.value[k]   = v; setDirty(true); }

const loadedFonts = new Set();
function ensureFontLoaded(name) {
  if (!name || name === 'system-ui' || loadedFonts.has(name)) return;
  loadedFonts.add(name);
  const link = document.createElement('link');
  link.rel = 'stylesheet';
  link.href = `https://fonts.googleapis.com/css2?family=${encodeURIComponent(name).replace(/%20/g, '+')}:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap`;
  document.head.appendChild(link);
}

function uploadCustomFont() {
  showToast(t('Caricamento font custom in arrivo nella prossima release'), 'success');
}

async function loadSettings() {
  try {
    const res = await fetch(`${window.oloData.restUrl}styles`, { headers: { 'X-WP-Nonce': window.oloData.nonce } });
    if (res.ok) {
      const data = await res.json();
      fullStyles.value = data.styles || {};
      const tp = (data.styles && data.styles.typography) || {};
      if (tp.font_family_heading) display.value.family = tp.font_family_heading;
      if (tp.font_weight_heading) display.value.weight = parseInt(tp.font_weight_heading) || 400;
      if (tp.font_family)         body.value.family = tp.font_family;
      if (tp.font_family_mono)    mono.value.family = tp.font_family_mono;
      if (tp.font_weight_body)    body.value.weight = parseInt(tp.font_weight_body) || 500;
      if (tp.font_size_base)      scale.value.base = parseInt(tp.font_size_base) || 16;
      if (tp.line_height)         scale.value.lineHeight = parseFloat(tp.line_height) || 1.55;
      if (tp.scale_ratio)         scale.value.ratio = parseFloat(tp.scale_ratio) || 1.25;
      ensureFontLoaded(display.value.family);
      ensureFontLoaded(body.value.family);
      if (mono.value.family) ensureFontLoaded(mono.value.family);
    }
  } catch (e) { /* defaults */ }
}

// Ricostruisce il blocco flat olo_styles.typography dal modello UI (display/body/scale),
// derivando le dimensioni h1..h6 dalla scala modulare (base * ratio^k, in rem su root 16px).
function buildTypographyBlock() {
  const b = scale.value.base, r = scale.value.ratio;
  const rem = (k) => (Math.round((b * Math.pow(r, k) / 16) * 1000) / 1000) + 'rem';
  return {
    ...(fullStyles.value.typography || {}),
    font_family: body.value.family === 'system-ui' ? '' : body.value.family,
    font_family_heading: display.value.family,
    font_family_mono: mono.value.family,
    font_weight_body: String(body.value.weight),
    font_weight_heading: String(display.value.weight),
    font_size_base: `${scale.value.base}px`,
    line_height: String(scale.value.lineHeight),
    scale_ratio: String(scale.value.ratio),
    font_size_h1: rem(6), font_size_h2: rem(5), font_size_h3: rem(4),
    font_size_h4: rem(3), font_size_h5: rem(2), font_size_h6: rem(1),
  };
}

async function saveSettings() {
  try {
    const payload = { ...fullStyles.value, typography: buildTypographyBlock() };
    const res = await fetch(`${window.oloData.restUrl}styles`, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': window.oloData.nonce },
      body: JSON.stringify(payload),
    });
    if (!res.ok) throw new Error();
    fullStyles.value = payload;
  } catch (e) { showToast(t('Errore di salvataggio tipografia'), 'error'); }
}

const onSave = () => saveSettings();
const onDiscard = () => loadSettings();

onMounted(() => {
  ensureFontLoaded('Instrument Serif');
  ensureFontLoaded('Work Sans');
  loadSettings();
  window.addEventListener('olo-cfg-save', onSave);
  window.addEventListener('olo-cfg-discard', onDiscard);
});
onBeforeUnmount(() => {
  window.removeEventListener('olo-cfg-save', onSave);
  window.removeEventListener('olo-cfg-discard', onDiscard);
});
</script>

<style scoped>
.font-pair-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
@media (max-width: 900px) { .font-pair-grid { grid-template-columns: 1fr; } }
.font-card { background: #fff; border: 1px solid var(--c-line-soft); border-radius: 12px; padding: 18px; }
.font-card-label { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: .08em; color: var(--c-text-faint); margin-bottom: 8px; }
.font-card-preview { font-size: 46px; line-height: 1; letter-spacing: -.02em; margin: 6px 0 16px; color: var(--c-navy); }
.font-card-preview.body { font-weight: 500; font-size: 32px; line-height: 1.1; }
.font-pills { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 12px; }
.scale-preview { background: var(--c-bg); border-radius: 10px; padding: 18px; display: grid; gap: 10px; }
</style>
