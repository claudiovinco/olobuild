<template>
  <div class="olo-bp-tab">
    <div class="olo-card">
      <div class="olo-card-head">
        <div class="olo-card-icon responsive">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect width="18" height="12" x="3" y="4" rx="2"/><line x1="8" x2="16" y1="20" y2="20"/><line x1="12" x2="12" y1="16" y2="20"/></svg>
        </div>
        <div>
          <h3>Sitewide Responsive Breakpoints</h3>
          <p>Breakpoint predefiniti per tutte le pagine. Attiva solo quelli che usi.</p>
        </div>
      </div>
      <div class="olo-card-body">
        <div
          v-for="bp in breakpoints"
          :key="bp.key"
          class="olo-bp-row"
          :class="{ 'olo-bp-row--disabled': !bp.alwaysOn && !enabled[bp.key], 'olo-bp-row--base': bp.alwaysOn }"
        >
          <!-- Device icon -->
          <div class="olo-bp-icon" v-html="bp.icon"></div>

          <!-- Name -->
          <div class="olo-bp-name">{{ bp.label }}</div>

          <!-- Direction + value -->
          <div class="olo-bp-value" v-if="!bp.alwaysOn">
            <span class="olo-bp-dir">{{ bp.direction }}</span>
            <input
              type="number"
              v-model.number="values[bp.key]"
              :min="bp.min"
              :max="bp.max"
              class="olo-bp-input"
              :disabled="!enabled[bp.key]"
            />
            <span class="olo-bp-unit">px</span>
          </div>
          <div class="olo-bp-value olo-bp-base-label" v-else>
            Base device
          </div>

          <!-- Toggle -->
          <label v-if="!bp.alwaysOn" class="olo-bp-toggle">
            <input type="checkbox" v-model="enabled[bp.key]" />
            <span class="olo-bp-toggle-track">
              <span class="olo-bp-toggle-thumb"></span>
            </span>
          </label>
          <div v-else class="olo-bp-toggle-placeholder"></div>
        </div>
      </div>
    </div>

    <!-- Actions -->
    <div class="olo-actions">
      <button @click="resetDefaults" class="olo-btn-reset">
        Cancel
      </button>
      <button @click="save" :disabled="saving" class="olo-btn-save">
        <span v-if="saving" class="olo-spinner"></span>
        {{ saving ? 'Salvataggio...' : 'Save' }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, inject, onMounted } from 'vue';

const oloData = window.oloData || {};
const showToast = inject('showToast', () => {});

const defaults = {
  widescreen: 1400,
  tablet_landscape: 1200,
  tablet: 960,
  mobile_landscape: 640,
  mobile: 480,
};

const enabledDefaults = {
  widescreen: true,
  tablet_landscape: false,
  tablet: true,
  mobile_landscape: false,
  mobile: true,
};

const breakpoints = [
  {
    key: 'mobile',
    label: 'Phone',
    direction: '<',
    min: 320, max: 768,
    icon: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="7" y="2" width="10" height="20" rx="2"/><circle cx="12" cy="19" r="0.5" fill="currentColor"/></svg>',
  },
  {
    key: 'mobile_landscape',
    label: 'Phone Wide',
    direction: '<',
    min: 400, max: 900,
    icon: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" opacity="0.7"><rect x="2" y="6" width="20" height="12" rx="2"/><circle cx="19" cy="12" r="0.5" fill="currentColor"/></svg>',
  },
  {
    key: 'tablet',
    label: 'Tablet',
    direction: '<',
    min: 600, max: 1200,
    icon: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="5" y="2" width="14" height="20" rx="2"/><circle cx="12" cy="19" r="0.5" fill="currentColor"/></svg>',
  },
  {
    key: 'tablet_landscape',
    label: 'Tablet Wide',
    direction: '<',
    min: 900, max: 1600,
    icon: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" opacity="0.7"><rect x="2" y="5" width="20" height="14" rx="2"/><circle cx="19" cy="12" r="0.5" fill="currentColor"/></svg>',
  },
  {
    key: null,
    label: 'Desktop',
    alwaysOn: true,
    icon: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect width="20" height="14" x="2" y="3" rx="2"/><line x1="8" x2="16" y1="21" y2="21"/><line x1="12" x2="12" y1="17" y2="21"/></svg>',
  },
  {
    key: 'widescreen',
    label: 'Widescreen',
    direction: '>',
    min: 1200, max: 2560,
    icon: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="1" y="3" width="22" height="14" rx="2"/><line x1="7" x2="17" y1="21" y2="21"/><line x1="12" x2="12" y1="17" y2="21"/></svg>',
  },
];

const values = ref({ ...defaults });
const enabled = ref({ ...enabledDefaults });
const saving = ref(false);

onMounted(async () => {
  try {
    const res = await fetch(`${oloData.restUrl}/settings/breakpoints`, {
      headers: { 'X-WP-Nonce': oloData.nonce },
    });
    if (res.ok) {
      const data = await res.json();
      if (data.values) values.value = { ...defaults, ...data.values };
      if (data.enabled) enabled.value = { ...enabledDefaults, ...data.enabled };
    }
  } catch (e) {
    console.error('Load breakpoints failed:', e);
  }
});

function resetDefaults() {
  values.value = { ...defaults };
  enabled.value = { ...enabledDefaults };
  showToast('Valori predefiniti ripristinati');
}

async function save() {
  saving.value = true;
  try {
    const res = await fetch(`${oloData.restUrl}/settings/breakpoints`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'X-WP-Nonce': oloData.nonce,
      },
      body: JSON.stringify({ values: values.value, enabled: enabled.value }),
    });
    if (res.ok) {
      showToast('Breakpoint salvati con successo');
    } else {
      showToast('Errore nel salvataggio', 'error');
    }
  } catch (e) {
    console.error('Save breakpoints failed:', e);
    showToast('Errore nel salvataggio', 'error');
  } finally {
    saving.value = false;
  }
}
</script>

<style scoped>
.olo-bp-row {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 16px;
  border-bottom: 1px solid #f0f0f5;
  transition: opacity 0.2s;
}
.olo-bp-row:last-child { border-bottom: none; }
.olo-bp-row--disabled { opacity: 0.45; }
.olo-bp-row--base { background: #fafaff; }

.olo-bp-icon {
  flex-shrink: 0;
  width: 28px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #6b7280;
}
.olo-bp-row--disabled .olo-bp-icon { color: #c0c4cc; }

.olo-bp-name {
  flex: 1;
  font-size: 14px;
  font-weight: 500;
  color: #1f2937;
  white-space: nowrap;
}
.olo-bp-row--disabled .olo-bp-name { color: #9ca3af; }

.olo-bp-value {
  display: flex;
  align-items: center;
  gap: 4px;
  flex-shrink: 0;
}

.olo-bp-dir {
  font-size: 13px;
  color: #9ca3af;
  font-weight: 500;
  width: 14px;
  text-align: center;
}

.olo-bp-input {
  width: 72px;
  height: 34px;
  border: 1.5px solid #e5e7eb;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 500;
  text-align: center;
  color: #374151;
  background: #fff;
  outline: none;
  transition: border-color 0.15s, box-shadow 0.15s;
  -moz-appearance: textfield;
}
.olo-bp-input::-webkit-inner-spin-button,
.olo-bp-input::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
.olo-bp-input:focus { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,0.12); }
.olo-bp-input:disabled { background: #f9fafb; color: #c0c4cc; cursor: not-allowed; }

.olo-bp-unit {
  font-size: 13px;
  color: #9ca3af;
  font-weight: 400;
}

.olo-bp-base-label {
  font-size: 13px;
  color: #9ca3af;
  font-style: italic;
}

/* Toggle switch */
.olo-bp-toggle {
  flex-shrink: 0;
  position: relative;
  cursor: pointer;
}
.olo-bp-toggle input { position: absolute; opacity: 0; width: 0; height: 0; }

.olo-bp-toggle-track {
  display: block;
  width: 44px;
  height: 24px;
  border-radius: 12px;
  background: #d1d5db;
  transition: background 0.2s;
  position: relative;
}
.olo-bp-toggle input:checked + .olo-bp-toggle-track {
  background: #6366f1;
}

.olo-bp-toggle-thumb {
  position: absolute;
  top: 2px;
  left: 2px;
  width: 20px;
  height: 20px;
  border-radius: 50%;
  background: #fff;
  box-shadow: 0 1px 3px rgba(0,0,0,0.15);
  transition: transform 0.2s;
}
.olo-bp-toggle input:checked + .olo-bp-toggle-track .olo-bp-toggle-thumb {
  transform: translateX(20px);
}

.olo-bp-toggle-placeholder {
  width: 44px;
  flex-shrink: 0;
}

/* Actions */
.olo-actions {
  display: flex;
  gap: 12px;
  margin-top: 20px;
  justify-content: flex-end;
}
.olo-btn-save {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 8px 28px;
  border-radius: 8px;
  border: none;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  background: #6366f1;
  color: #fff;
  transition: background 0.15s;
}
.olo-btn-save:hover { background: #4f46e5; }
.olo-btn-save:disabled { opacity: 0.6; cursor: not-allowed; }

.olo-btn-reset {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 8px 20px;
  border-radius: 8px;
  border: 1.5px solid #e5e7eb;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  background: #fff;
  color: #6b7280;
  transition: all 0.15s;
}
.olo-btn-reset:hover { border-color: #d1d5db; color: #374151; }
</style>
