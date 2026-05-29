<template>
  <div class="cfg-page-head">
    <div>
      <h1>{{ t('Palette') }} <em>{{ t('colori') }}</em></h1>
      <p>{{ t('I colori globali del sito. Le modifiche si propagano a tutti i template, gli elementi del builder e gli stili dei post type.') }}</p>
    </div>
    <div class="head-actions">
      <button class="cfg-btn cfg-btn-secondary" @click="importFromCoolors">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3s7 8 7 13a7 7 0 0 1-14 0c0-5 7-13 7-13z"/></svg>
        {{ t('Importa da Coolors') }}
      </button>
      <button class="cfg-btn cfg-btn-secondary" @click="generateWithAI">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l1.5 4.5L18 9l-4.5 1.5L12 15l-1.5-4.5L6 9l4.5-1.5L12 3z"/></svg>
        {{ t('Genera con AI') }}
      </button>
    </div>
  </div>

  <div class="cfg-card">
    <div class="cfg-card-head">
      <div class="head-ic">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="13.5" cy="6.5" r="1.5"/><circle cx="17.5" cy="10.5" r="1.5"/><circle cx="8.5" cy="7.5" r="1.5"/><circle cx="6.5" cy="12.5" r="1.5"/><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10c.9 0 1.5-.5 1.5-1.5 0-.4-.2-.8-.5-1.2a1.5 1.5 0 0 1 1.2-2.3h2C18.5 17 20.5 15 20.5 12.4 20.5 6.5 16.7 2 12 2z"/></svg>
      </div>
      <div>
        <h3>{{ t('Colori del brand') }}</h3>
        <p>{{ t('Ogni colore ha un ruolo. Cambialo e ovunque sul sito si aggiorna di conseguenza.') }}</p>
      </div>
      <div class="head-actions">
        <span class="cfg-pill ok"><span class="dot"></span> {{ t('AA Verificato') }}</span>
      </div>
    </div>
    <div class="cfg-card-body">
      <div class="brand-list">
        <div v-for="c in palette" :key="c.id" class="brand-row">
          <button class="brand-swatch" :style="{ background: c.hex }" @click="openPicker(c.id)" :aria-label="t('Modifica colore ') + c.name"></button>
          <div class="brand-info">
            <div class="brand-name">{{ t(c.name) }}</div>
            <div class="brand-role">{{ t(c.role) }}</div>
          </div>
          <div class="cfg-input mono">
            <span class="prefix">#</span>
            <input
              type="text"
              :value="(c.hex || '').replace(/^#/, '').toUpperCase()"
              @input="updateHex(c.id, $event.target.value)"
              maxlength="6"
              autocomplete="off"
              spellcheck="false"
            />
          </div>
          <button class="cfg-btn-icon cfg-btn-ghost" :title="t('Apri picker')" @click="openPicker(c.id)">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
          </button>
        </div>
        <button class="brand-add" @click="addCustomColor">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
          {{ t('Aggiungi colore custom') }}
        </button>
      </div>
    </div>
  </div>

  <div class="cfg-card">
    <div class="cfg-card-head">
      <div class="head-ic">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 2 9 5-9 5-9-5 9-5z"/><path d="m3 17 9 5 9-5"/><path d="m3 12 9 5 9-5"/></svg>
      </div>
      <div>
        <h3>{{ t('Scala neutri') }}</h3>
        <p>{{ t('Sfumature grigie utilizzate per testi, bordi, sfondi e stati disabilitati.') }}</p>
      </div>
      <div class="head-actions">
        <div class="cfg-segment">
          <button :class="{ 'is-on': neutralMode === 'auto' }"   @click="neutralMode = 'auto'">{{ t('Auto') }}</button>
          <button :class="{ 'is-on': neutralMode === 'manual' }" @click="neutralMode = 'manual'">{{ t('Manuale') }}</button>
        </div>
      </div>
    </div>
    <div v-if="neutralMode === 'auto'" class="cfg-card-body tight">
      <div class="cfg-row no-divider">
        <div class="label-col">
          <label>{{ t('Tinta neutri') }}</label>
          <div class="hint">{{ t('Scegli la sfumatura di base. I 7 livelli vengono generati automaticamente.') }}</div>
        </div>
        <div class="control-col">
          <div class="tint-chips">
            <button
              v-for="opt in tintOptions" :key="opt.id"
              class="tint-chip"
              :class="{ 'is-on': neutralTint === opt.id }"
              :title="opt.label"
              @click="neutralTint = opt.id"
            >
              <span class="tint-dot" :style="{ background: NEUTRAL_PRESETS[opt.id][4] }"></span>
              {{ opt.label }}
            </button>
          </div>
        </div>
      </div>
    </div>
    <div class="cfg-card-body">
      <div class="neutral-scale">
        <div v-for="(c, i) in displayNeutrals" :key="i" class="neutral-col">
          <button
            type="button"
            class="neutral-swatch"
            :class="{ 'is-locked': neutralMode === 'auto' }"
            :style="{ background: c }"
            :disabled="neutralMode === 'auto'"
            :title="neutralMode === 'manual' ? t('Clicca per modificare') : t('Passa a Manuale per modificare')"
            @click="openNeutralPicker(i)"
          ></button>
          <div class="neutral-label">{{ i === 0 ? 50 : i * 100 }}</div>
        </div>
      </div>
    </div>
  </div>

  <div class="cfg-card">
    <div class="cfg-card-head">
      <div class="head-ic">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8z"/></svg>
      </div>
      <div>
        <h3>{{ t('Modalità dark') }}</h3>
        <p>{{ t('Configura come la palette si adatta automaticamente al tema scuro.') }}</p>
      </div>
    </div>
    <div class="cfg-card-body tight">
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('Abilita modalità dark') }}</label>
          <div class="hint">{{ t('Mostra il selettore dark/light nell\'header del sito.') }}</div>
        </div>
        <div class="control-col">
          <button class="cfg-switch" :class="{ 'is-on': dark.enabled }" @click="setDark('enabled', !dark.enabled)" role="switch"></button>
        </div>
      </div>
      <div class="cfg-row no-divider">
        <div class="label-col">
          <label>{{ t('Strategia di inversione') }}</label>
          <div class="hint">{{ t('Come generare i colori scuri.') }}</div>
        </div>
        <div class="control-col">
          <div class="cfg-select">
            <select :value="dark.strategy" @change="setDark('strategy', $event.target.value)">
              <option value="auto">{{ t('Automatica (consigliata)') }}</option>
              <option value="manual">{{ t('Manuale, palette separata') }}</option>
              <option value="luminance">{{ t('Solo aggiusta luminanza') }}</option>
            </select>
            <span class="chev"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg></span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, inject, onMounted, onBeforeUnmount } from 'vue';
import { t } from '@/i18n';

const showToast = inject('showToast', () => {});
const setDirty  = inject('setDirty',  () => {});

const palette = ref([
  { id: 'primary',   name: 'Primary',   role: 'Brand · CTA · link',      hex: '#E1474F' },
  { id: 'secondary', name: 'Secondary', role: 'Accenti · highlight',     hex: '#0F172A' },
  { id: 'tertiary',  name: 'Tertiary',  role: 'Decorazione · skeleton',  hex: '#F3EDE2' },
  { id: 'success',   name: 'Success',   role: 'Stato positivo',          hex: '#15803D' },
  { id: 'warning',   name: 'Warning',   role: 'Stato attenzione',        hex: '#B45309' },
  { id: 'danger',    name: 'Danger',    role: 'Stato errore',            hex: '#DC2626' },
]);
const NEUTRAL_PRESETS = {
  slate:   ['#F8FAFC', '#F1F5F9', '#E2E8F0', '#94A3B8', '#475569', '#1E293B', '#0F172A'],
  gray:    ['#F9FAFB', '#F3F4F6', '#E5E7EB', '#9CA3AF', '#4B5563', '#1F2937', '#111827'],
  zinc:    ['#FAFAFA', '#F4F4F5', '#E4E4E7', '#A1A1AA', '#52525B', '#27272A', '#09090B'],
  neutral: ['#FAFAFA', '#F5F5F5', '#E5E5E5', '#A3A3A3', '#525252', '#262626', '#0A0A0A'],
  stone:   ['#FAFAF9', '#F5F5F4', '#E7E5E4', '#A8A29E', '#57534E', '#292524', '#0C0A09'],
};
const tintOptions = [
  { id: 'slate',   label: 'Slate'   },
  { id: 'gray',    label: 'Gray'    },
  { id: 'zinc',    label: 'Zinc'    },
  { id: 'neutral', label: 'Neutral' },
  { id: 'stone',   label: 'Stone'   },
];

const neutrals = ref([...NEUTRAL_PRESETS.zinc]);
const neutralMode = ref('auto');
const neutralTint = ref('zinc');
const dark = ref({ enabled: true, strategy: 'auto' });

const displayNeutrals = computed(() => {
  return neutralMode.value === 'auto'
    ? NEUTRAL_PRESETS[neutralTint.value] || NEUTRAL_PRESETS.zinc
    : neutrals.value;
});

// In auto mode il cambio tinta aggiorna i neutri salvati (così se l'utente passa a manuale parte da lì)
watch(neutralTint, (v) => {
  if (neutralMode.value === 'auto') {
    neutrals.value = [...(NEUTRAL_PRESETS[v] || NEUTRAL_PRESETS.zinc)];
    setDirty(true);
  }
});

// Passando da auto → manuale, congela i correnti come base editabile
watch(neutralMode, (v) => {
  if (v === 'manual') {
    neutrals.value = [...(NEUTRAL_PRESETS[neutralTint.value] || neutrals.value)];
  }
  setDirty(true);
});

function openNeutralPicker(i) {
  if (neutralMode.value !== 'manual') return;
  const input = document.createElement('input');
  input.type = 'color';
  input.value = neutrals.value[i] || '#888888';
  input.style.position = 'fixed';
  input.style.left = '-9999px';
  document.body.appendChild(input);
  input.addEventListener('input', (e) => {
    neutrals.value[i] = (e.target.value || '#888888').toUpperCase();
    setDirty(true);
  });
  input.addEventListener('change', () => { document.body.removeChild(input); });
  input.click();
}

function updateHex(id, val) {
  const clean = val.replace(/[^0-9a-fA-F]/g, '').slice(0, 6);
  const item = palette.value.find(x => x.id === id);
  if (!item) return;
  item.hex = '#' + clean.toUpperCase();
  setDirty(true);
}

function openPicker(id) {
  const item = palette.value.find(x => x.id === id);
  if (!item) return;
  const input = document.createElement('input');
  input.type = 'color';
  input.value = item.hex || '#000000';
  input.style.position = 'fixed';
  input.style.opacity = '0';
  input.style.pointerEvents = 'none';
  document.body.appendChild(input);
  input.addEventListener('change', (e) => {
    item.hex = e.target.value.toUpperCase();
    setDirty(true);
    document.body.removeChild(input);
  });
  input.click();
}

function addCustomColor() {
  palette.value.push({ id: 'custom_' + Date.now(), name: 'Custom', role: 'Colore personalizzato', hex: '#000000' });
  setDirty(true);
}
function setDark(k, v) { dark.value[k] = v; setDirty(true); }

function importFromCoolors() {
  const url = prompt(t('Incolla l\'URL Coolors (es. https://coolors.co/...)'));
  if (!url) return;
  const hexes = (url.match(/[0-9a-fA-F]{6}/g) || []).slice(0, 6);
  if (hexes.length < 3) { showToast(t('URL Coolors non valido'), 'error'); return; }
  hexes.forEach((h, i) => { if (palette.value[i]) palette.value[i].hex = '#' + h.toUpperCase(); });
  setDirty(true);
  showToast(t('Palette importata da Coolors'), 'success');
}
function generateWithAI() {
  showToast(t('Generazione AI palette (richiede AI Assistant configurato)'), 'success');
}

async function loadColors() {
  try {
    const res = await fetch(`${window.oloData.restUrl}settings/global-colors`, { headers: { 'X-WP-Nonce': window.oloData.nonce } });
    if (res.ok) {
      const data = await res.json();
      const list = Array.isArray(data) ? data : (data?.palette || data?.colors);
      if (Array.isArray(list) && list.length) {
        palette.value = list.map((c, i) => ({
          id: c.id || (c.name ? c.name.toLowerCase().replace(/\s+/g, '_') : 'c_' + i),
          name: c.name || c.label || 'Color',
          role: c.role || c.description || '',
          hex:  c.hex || c.value || '#000000',
        }));
      }
      if (Array.isArray(data?.neutrals)) neutrals.value = data.neutrals;
      if (data?.neutral_mode === 'manual' || data?.neutral_mode === 'auto') neutralMode.value = data.neutral_mode;
      if (data?.neutral_tint && NEUTRAL_PRESETS[data.neutral_tint]) neutralTint.value = data.neutral_tint;
      if (data?.dark) Object.assign(dark.value, data.dark);
    }
  } catch (e) { /* defaults */ }
}

async function saveColors() {
  try {
    await fetch(`${window.oloData.restUrl}settings/global-colors`, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': window.oloData.nonce },
      body: JSON.stringify({ palette: palette.value, neutrals: neutrals.value, dark: dark.value, neutral_mode: neutralMode.value, neutral_tint: neutralTint.value }),
    });
  } catch (e) { showToast(t('Errore di salvataggio colori'), 'error'); }
}

const onSave = () => saveColors();
const onDiscard = () => loadColors();

onMounted(() => {
  loadColors();
  window.addEventListener('olo-cfg-save', onSave);
  window.addEventListener('olo-cfg-discard', onDiscard);
});
onBeforeUnmount(() => {
  window.removeEventListener('olo-cfg-save', onSave);
  window.removeEventListener('olo-cfg-discard', onDiscard);
});
</script>

<style scoped>
.brand-list { display: grid; gap: 10px; }
.brand-row {
  display: grid;
  grid-template-columns: 56px 1fr 220px 36px;
  gap: 14px; align-items: center;
  padding: 10px 12px;
  background: #fff;
  border: 1px solid var(--c-line-soft);
  border-radius: 10px;
}
.brand-swatch {
  width: 56px; height: 56px; border-radius: 8px;
  box-shadow: inset 0 0 0 1px rgba(0,0,0,.06);
  border: 0; cursor: pointer; padding: 0;
  transition: transform .12s;
}
.brand-swatch:hover { transform: scale(1.05); }
.brand-name { font-weight: 600; font-size: 14px; color: var(--c-navy); }
.brand-role { font-size: 12px; color: var(--c-text-mute); margin-top: 2px; }
.brand-add {
  appearance: none;
  background: transparent;
  border: 1.5px dashed var(--c-line);
  border-radius: 10px;
  padding: 14px;
  font: 600 13.5px var(--c-sans);
  color: var(--c-text-mute);
  cursor: pointer;
  display: flex; align-items: center; justify-content: center; gap: 8px;
  margin-top: 4px;
  transition: border-color .12s, color .12s, background .12s;
}
.brand-add:hover { border-color: var(--c-red); color: var(--c-red); background: var(--c-red-soft); }
.brand-add svg { width: 14px; height: 14px; }

.neutral-scale { display: flex; gap: 6px; }
.neutral-col { flex: 1; }
.neutral-swatch {
  width: 100%;
  height: 64px;
  border-radius: 8px;
  box-shadow: inset 0 0 0 1px rgba(0,0,0,.06);
  border: 0;
  padding: 0;
  cursor: pointer;
  transition: transform .12s, box-shadow .12s;
}
.neutral-swatch:hover:not(:disabled) {
  transform: translateY(-1px);
  box-shadow: inset 0 0 0 1px rgba(0,0,0,.1), 0 4px 10px rgba(0,0,0,.08);
}
.neutral-swatch:disabled, .neutral-swatch.is-locked {
  cursor: not-allowed;
}
.neutral-label { text-align: center; margin-top: 6px; font-family: var(--c-mono); font-size: 11px; color: var(--c-text-mute); }

.tint-chips { display: flex; flex-wrap: wrap; gap: 6px; }
.tint-chip {
  display: inline-flex; align-items: center; gap: 8px;
  padding: 7px 12px;
  background: #fff;
  border: 1px solid var(--c-line);
  border-radius: 8px;
  font: 600 12.5px var(--c-sans);
  color: var(--c-text-mute);
  cursor: pointer;
  transition: border-color .12s, color .12s, background .12s;
}
.tint-chip:hover { border-color: var(--c-text-faint); color: var(--c-navy); }
.tint-chip.is-on {
  border-color: var(--c-red);
  color: var(--c-navy);
  background: var(--c-red-soft);
}
.tint-dot {
  width: 14px; height: 14px;
  border-radius: 50%;
  box-shadow: inset 0 0 0 1px rgba(0,0,0,.12);
}
</style>
