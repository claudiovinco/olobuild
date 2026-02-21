<template>
  <div v-if="visible" class="te-overlay" @click.self="visible = false">
    <div class="te-panel">
      <div class="te-header">
        <h3>Personalizza Colori</h3>
        <button class="te-close" @click="visible = false">&times;</button>
      </div>
      <div class="te-body">
        <div v-for="item in colorFields" :key="item.key" class="te-row">
          <label class="te-label">{{ item.label }}</label>
          <div class="te-input-group">
            <input type="color" :value="colors[item.key]" @input="setColor(item.key, $event.target.value)" class="te-color" />
            <input type="text" :value="colors[item.key]" @change="setColor(item.key, $event.target.value)" class="te-hex" maxlength="7" />
          </div>
        </div>
      </div>
      <div class="te-footer">
        <button class="te-btn te-btn-reset" @click="resetDefaults">Ripristina default</button>
        <button class="te-btn te-btn-save" @click="saveTheme" :disabled="saving">
          {{ saving ? 'Salvataggio...' : 'Salva tema' }}
        </button>
      </div>
      <p v-if="savedMsg" class="te-msg">{{ savedMsg }}</p>
    </div>
  </div>
  <!-- Floating toggle button -->
  <button v-if="!visible" class="te-toggle" @click="visible = true" title="Personalizza colori">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <circle cx="13.5" cy="6.5" r="2.5"/>
      <path d="M16 6.5a5 5 0 0 1-6.7 7.1L3 20l2-2 .5-2.5L8 15l1-2.5L16 6.5z"/>
    </svg>
  </button>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';

const cfg = window.oloManagerConfig || {};

const defaultColors = {
  bg:           '#696969',
  card:         '#7a7a7a',
  sidebar:      '#5c5c5c',
  accent:       '#f59e0b',
  primary:      '#fe8839',
  primaryDark:  '#ffffff',
  text:         '#ffffff',
  textMuted:    '#d0d0d0',
  headerBg:     '#5c5c5c',
  btnPrimary:   '#111111',
  btnSuccess:   '#059669',
  border:       '#ffffff12',
};

const cssVarMap = {
  bg:           '--olom-bg',
  card:         '--olom-card',
  sidebar:      '--olom-sidebar-bg',
  accent:       '--olom-accent',
  primary:      '--olom-primary',
  primaryDark:  '--olom-primary-dark',
  text:         '--olom-text',
  textMuted:    '--olom-text-muted',
  headerBg:     '--olom-header-bg',
  btnPrimary:   '--olom-btn-primary-bg',
  btnSuccess:   '--olom-btn-success-bg',
  border:       '--olom-border',
};

const colorFields = [
  { key: 'bg',          label: 'Sfondo principale' },
  { key: 'card',        label: 'Sfondo card' },
  { key: 'sidebar',     label: 'Sfondo sidebar' },
  { key: 'headerBg',    label: 'Sfondo header' },
  { key: 'accent',      label: 'Colore accento' },
  { key: 'primary',     label: 'Colore primario (blu)' },
  { key: 'primaryDark', label: 'Primario scuro' },
  { key: 'text',        label: 'Testo principale' },
  { key: 'textMuted',   label: 'Testo attenuato' },
  { key: 'btnPrimary',  label: 'Bottone primario' },
  { key: 'btnSuccess', label: 'Bottone salva' },
];

const visible = ref(false);
const saving = ref(false);
const savedMsg = ref('');
const colors = reactive({ ...defaultColors });

function setColor(key, val) {
  colors[key] = val;
  applyColors();
}

function applyColors() {
  const root = document.documentElement;
  for (const [key, cssVar] of Object.entries(cssVarMap)) {
    if (colors[key]) {
      root.style.setProperty(cssVar, colors[key]);
    }
  }
  // Derived values
  root.style.setProperty('--olom-card-hover', lighten(colors.card, 12));
  root.style.setProperty('--olom-surface', lighten(colors.card, -5));
  root.style.setProperty('--olom-text-secondary', colors.text);
  // Primary glow (rgba version of primary color)
  const pr = hexToRgb(colors.primary);
  if (pr) {
    root.style.setProperty('--olom-primary-glow', `rgba(${pr.r},${pr.g},${pr.b},0.15)`);
  }
  root.style.setProperty('--olom-accent-light', lighten(colors.accent, 10));
}

function lighten(hex, amt) {
  const c = hexToRgb(hex);
  if (!c) return hex;
  const r = Math.min(255, Math.max(0, c.r + amt));
  const g = Math.min(255, Math.max(0, c.g + amt));
  const b = Math.min(255, Math.max(0, c.b + amt));
  return '#' + [r, g, b].map(v => v.toString(16).padStart(2, '0')).join('');
}

function hexToRgb(hex) {
  if (!hex || hex.length < 7) return null;
  return {
    r: parseInt(hex.slice(1, 3), 16),
    g: parseInt(hex.slice(3, 5), 16),
    b: parseInt(hex.slice(5, 7), 16),
  };
}

function resetDefaults() {
  Object.assign(colors, defaultColors);
  applyColors();
}

async function saveTheme() {
  saving.value = true;
  savedMsg.value = '';
  try {
    const res = await fetch(cfg.restUrl + '/manager/theme', {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'X-WP-Nonce': cfg.nonce,
      },
      body: JSON.stringify({ colors: { ...colors } }),
    });
    if (!res.ok) throw new Error('Errore salvataggio');
    savedMsg.value = 'Salvato!';
    setTimeout(() => savedMsg.value = '', 2500);
  } catch (e) {
    savedMsg.value = 'Errore: ' + e.message;
  } finally {
    saving.value = false;
  }
}

async function loadTheme() {
  try {
    const res = await fetch(cfg.restUrl + '/manager/theme', {
      headers: { 'X-WP-Nonce': cfg.nonce },
    });
    if (res.ok) {
      const data = await res.json();
      if (data && data.colors) {
        Object.assign(colors, data.colors);
        applyColors();
      }
    }
  } catch (e) {
    // silently use defaults
  }
}

onMounted(() => {
  loadTheme();
});
</script>

<style scoped>
.te-toggle {
  position: fixed;
  bottom: 20px;
  right: 20px;
  width: 44px;
  height: 44px;
  border-radius: 50%;
  background: #222;
  color: #f59e0b;
  border: 2px solid rgba(255,255,255,0.2);
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 20px rgba(0,0,0,0.4);
  z-index: 99999;
  transition: all 0.2s;
}
.te-toggle:hover {
  background: #333;
  box-shadow: 0 0 20px rgba(245,158,11,0.3);
  transform: scale(1.1);
}

.te-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.5);
  z-index: 100000;
  display: flex;
  align-items: center;
  justify-content: flex-end;
  padding: 20px;
}

.te-panel {
  background: #1a1a1a;
  border-radius: 16px;
  width: 340px;
  max-height: 90vh;
  display: flex;
  flex-direction: column;
  box-shadow: 0 20px 60px rgba(0,0,0,0.5);
  border: 1px solid rgba(255,255,255,0.1);
}

.te-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 16px 20px;
  border-bottom: 1px solid rgba(255,255,255,0.08);
}
.te-header h3 {
  margin: 0;
  font-size: 16px;
  font-weight: 600;
  color: #fff;
}
.te-close {
  background: none;
  border: none;
  color: #999;
  font-size: 22px;
  cursor: pointer;
  padding: 0 4px;
  line-height: 1;
}
.te-close:hover { color: #fff; }

.te-body {
  padding: 16px 20px;
  overflow-y: auto;
  flex: 1;
}

.te-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 12px;
}

.te-label {
  font-size: 13px;
  color: #ccc;
  flex: 1;
}

.te-input-group {
  display: flex;
  align-items: center;
  gap: 6px;
}

.te-color {
  width: 32px;
  height: 28px;
  border: 1px solid rgba(255,255,255,0.15);
  border-radius: 6px;
  cursor: pointer;
  background: none;
  padding: 1px;
}
.te-color::-webkit-color-swatch-wrapper { padding: 0; }
.te-color::-webkit-color-swatch { border: none; border-radius: 4px; }

.te-hex {
  width: 72px;
  padding: 4px 6px;
  background: #2a2a2a;
  border: 1px solid rgba(255,255,255,0.12);
  border-radius: 6px;
  color: #fff;
  font-size: 12px;
  font-family: monospace;
  text-align: center;
}
.te-hex:focus {
  outline: none;
  border-color: #f59e0b;
}

.te-footer {
  display: flex;
  gap: 8px;
  padding: 14px 20px;
  border-top: 1px solid rgba(255,255,255,0.08);
}

.te-btn {
  flex: 1;
  padding: 8px 12px;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 500;
  cursor: pointer;
  border: 1px solid transparent;
  transition: all 0.15s;
}
.te-btn-reset {
  background: transparent;
  color: #999;
  border-color: rgba(255,255,255,0.12);
}
.te-btn-reset:hover { color: #fff; border-color: rgba(255,255,255,0.25); }
.te-btn-save {
  background: #059669;
  color: #fff;
}
.te-btn-save:hover { background: #047857; }
.te-btn-save:disabled { opacity: 0.5; cursor: not-allowed; }

.te-msg {
  text-align: center;
  font-size: 13px;
  color: #34d399;
  padding: 0 20px 12px;
  margin: 0;
}
</style>
