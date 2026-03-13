<template>
  <div class="olo-bp-tab">
    <div class="olo-card">
      <div class="olo-card-head">
        <div class="olo-card-icon responsive">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect width="18" height="12" x="3" y="4" rx="2"/><line x1="8" x2="16" y1="20" y2="20"/><line x1="12" x2="12" y1="16" y2="20"/></svg>
        </div>
        <div>
          <h3>Breakpoint Responsive</h3>
          <p>Valori predefiniti per tutte le pagine. Ogni pagina pu&ograve; sovrascriverli.</p>
        </div>
      </div>
      <div class="olo-card-body">
        <div v-for="bp in breakpoints" :key="bp.key" class="olo-bp-row">
          <div class="olo-bp-device">
            <span class="olo-bp-device-icon" v-html="bp.icon"></span>
            <div>
              <label :for="bp.key">{{ bp.label }}</label>
              <p class="olo-field-hint">{{ bp.help }}</p>
            </div>
          </div>
          <div class="olo-bp-control">
            <input
              :id="bp.key"
              type="number"
              v-model.number="values[bp.key]"
              :min="bp.min"
              :max="bp.max"
              class="olo-field-input olo-bp-input"
            />
            <span class="olo-bp-unit">px</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Actions -->
    <div class="olo-actions">
      <button @click="save" :disabled="saving" class="olo-btn-save">
        <svg v-if="!saving" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
        <span v-if="saving" class="olo-spinner"></span>
        {{ saving ? 'Salvataggio...' : 'Salva modifiche' }}
      </button>
      <button @click="resetDefaults" class="olo-btn-reset">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 4v6h6"/><path d="M3.51 15a9 9 0 105.42-8.37L1 10"/></svg>
        Ripristina predefiniti
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

const breakpoints = [
  { key: 'widescreen', label: 'Widescreen', min: 1200, max: 2560, help: 'min-width — Schermi molto grandi', icon: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="1" y="3" width="22" height="14" rx="2"/><line x1="7" x2="17" y1="21" y2="21"/><line x1="12" x2="12" y1="17" y2="21"/></svg>' },
  { key: 'tablet_landscape', label: 'Tablet Landscape', min: 900, max: 1600, help: 'max-width — Tablet orizzontale', icon: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="5" width="20" height="14" rx="2"/><circle cx="19" cy="12" r="0.5" fill="currentColor"/></svg>' },
  { key: 'tablet', label: 'Tablet', min: 600, max: 1200, help: 'max-width — Tablet verticale', icon: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="5" y="2" width="14" height="20" rx="2"/><circle cx="12" cy="19" r="0.5" fill="currentColor"/></svg>' },
  { key: 'mobile_landscape', label: 'Mobile Landscape', min: 400, max: 900, help: 'max-width — Smartphone orizzontale', icon: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="6" width="20" height="12" rx="2"/><circle cx="19" cy="12" r="0.5" fill="currentColor"/></svg>' },
  { key: 'mobile', label: 'Mobile', min: 320, max: 768, help: 'max-width — Smartphone verticale', icon: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="7" y="2" width="10" height="20" rx="2"/><circle cx="12" cy="19" r="0.5" fill="currentColor"/></svg>' },
];

const values = ref({ ...defaults });
const saving = ref(false);

onMounted(async () => {
  try {
    const res = await fetch(`${oloData.restUrl}/settings/breakpoints`, {
      headers: { 'X-WP-Nonce': oloData.nonce },
    });
    if (res.ok) {
      const data = await res.json();
      values.value = { ...defaults, ...data };
    }
  } catch (e) {
    console.error('Load breakpoints failed:', e);
  }
});

function resetDefaults() {
  values.value = { ...defaults };
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
      body: JSON.stringify(values.value),
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
