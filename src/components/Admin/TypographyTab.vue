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
          <div class="cfg-select">
            <select :value="display.family" @change="setDisplay('family', $event.target.value)">
              <option v-for="f in DISPLAY_FONTS" :key="f.value" :value="f.value">{{ f.label }}</option>
            </select>
            <span class="chev"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg></span>
          </div>
          <div class="font-pills">
            <span class="cfg-pill off">italic</span>
            <span class="cfg-pill off">400</span>
            <span class="cfg-pill ok">+ Google Fonts</span>
          </div>
        </div>
        <div class="font-card">
          <div class="font-card-label">{{ t('Body · testo corrente') }}</div>
          <div class="font-card-preview body" :style="{ fontFamily: bodyFontFamily }">Abc · 123</div>
          <div class="cfg-select">
            <select :value="body.family" @change="setBody('family', $event.target.value)">
              <option v-for="f in BODY_FONTS" :key="f.value" :value="f.value">{{ f.label }}</option>
            </select>
            <span class="chev"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg></span>
          </div>
          <div class="font-pills">
            <span class="cfg-pill off">400</span>
            <span class="cfg-pill off">500</span>
            <span class="cfg-pill off">600</span>
            <span class="cfg-pill off">700</span>
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
          <div class="cfg-input mono">
            <input type="number" min="10" max="32" :value="scale.base" @input="setScale('base', parseInt($event.target.value) || 16)" />
            <span class="suffix">px</span>
          </div>
        </div>
      </div>
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('Ratio scala') }}</label>
          <div class="hint">{{ t('Più alto = differenza più marcata fra body e h1.') }}</div>
        </div>
        <div class="control-col">
          <div class="cfg-select">
            <select :value="scale.ratio" @change="setScale('ratio', parseFloat($event.target.value))">
              <option value="1.125">1.125 · Major Second</option>
              <option value="1.2">1.2 · Minor Third</option>
              <option value="1.25">1.25 · Major Third</option>
              <option value="1.333">1.333 · Perfect Fourth</option>
              <option value="1.414">1.414 · Augmented Fourth</option>
              <option value="1.5">1.5 · Perfect Fifth</option>
            </select>
            <span class="chev"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg></span>
          </div>
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

const display = ref({ family: 'Instrument Serif', weight: 400 });
const body    = ref({ family: 'Work Sans', weight: 500 });
const scale   = ref({ base: 16, ratio: 1.25, lineHeight: 1.55 });

const displayFontFamily = computed(() => `'${display.value.family}', serif`);
const bodyFontFamily    = computed(() => `'${body.value.family}', sans-serif`);

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
    const res = await fetch(`${window.oloData.restUrl}settings/global-typography`, { headers: { 'X-WP-Nonce': window.oloData.nonce } });
    if (res.ok) {
      const data = await res.json();
      if (data?.display) Object.assign(display.value, data.display);
      if (data?.body)    Object.assign(body.value, data.body);
      if (data?.scale)   Object.assign(scale.value, data.scale);
      ensureFontLoaded(display.value.family);
      ensureFontLoaded(body.value.family);
    }
  } catch (e) { /* defaults */ }
}

async function saveSettings() {
  try {
    await fetch(`${window.oloData.restUrl}settings/global-typography`, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': window.oloData.nonce },
      body: JSON.stringify({ display: display.value, body: body.value, scale: scale.value }),
    });
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
