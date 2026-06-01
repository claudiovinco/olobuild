<template>
  <div class="olo-styles-tab">

    <!-- Preset bar -->
    <div class="olo-card">
      <div class="olo-card-head">
        <div class="olo-card-icon" style="background:#1a1a1a;color:#fff">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
        </div>
        <div>
          <h3>{{ t('Presets') }}</h3>
          <p>{{ t('Applica un set predefinito di stili con un click') }}</p>
        </div>
      </div>
      <div class="olo-card-body">
        <div class="olo-presets-grid">
          <button
            v-for="(preset, key) in stylesStore.presets"
            :key="key"
            @click="stylesStore.applyPreset(key)"
            class="olo-preset-btn"
          >
            <span class="olo-preset-dot" :style="{ backgroundColor: preset.colors.primary }"></span>
            {{ preset.name }}
          </button>
        </div>

        <!-- Custom presets -->
        <div v-if="stylesStore.customPresets.length > 0" class="olo-custom-presets">
          <div class="olo-custom-presets-label">{{ t('I tuoi preset') }}</div>
          <div class="olo-presets-grid">
            <div
              v-for="cp in stylesStore.customPresets"
              :key="cp.id"
              class="olo-preset-btn olo-preset-btn--custom"
              @click="stylesStore.applyCustomPreset(cp)"
            >
              <span class="olo-preset-dot" :style="{ backgroundColor: (cp.style?.colors?.primary || '#888') }"></span>
              <span class="olo-preset-btn-name">{{ cp.name }}</span>
              <button class="olo-preset-delete" @click.stop="deletePreset(cp.id)" :title="t('Elimina preset')">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
              </button>
            </div>
          </div>
        </div>

        <!-- Save current as preset -->
        <div class="olo-preset-save-row">
          <input
            v-model="newPresetName"
            type="text"
            :placeholder="t('Nome del preset...')"
            class="olo-field-input olo-preset-name-input"
            @keyup.enter="savePreset"
          />
          <button @click="savePreset" class="olo-btn-add" :disabled="!newPresetName.trim()">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/></svg>
            {{ t('Salva preset') }}
          </button>
        </div>
      </div>
    </div>

    <!-- Colori -->
    <div class="olo-card">
      <button class="olo-card-head olo-card-toggle" @click="toggle('colors')">
        <div style="display:flex;align-items:flex-start;gap:14px">
          <div class="olo-card-icon" style="background:#e8622a;color:#fff">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="4"/></svg>
          </div>
          <div>
            <h3>{{ t('Colori') }}</h3>
            <p>{{ t('Palette colori principale del sito') }}</p>
          </div>
        </div>
        <svg :class="['olo-chevron', sections.colors && 'open']" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
      </button>
      <div v-show="sections.colors" class="olo-card-body">
        <div class="olo-color-grid">
          <div v-for="(value, key) in stylesStore.colors" :key="key" class="olo-color-item">
            <label>{{ formatLabel(key) }}</label>
            <FieldColor
              :modelValue="value"
              @update:modelValue="stylesStore.updateColor(key, $event)"
            />
          </div>
        </div>
      </div>
    </div>

    <!-- Colori Dark Mode -->
    <div class="olo-card">
      <button class="olo-card-head olo-card-toggle" @click="toggle('darkColors')">
        <div style="display:flex;align-items:flex-start;gap:14px">
          <div class="olo-card-icon" style="background:#2a2a2a;color:#fff">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/></svg>
          </div>
          <div>
            <h3>{{ t('Colori Dark Mode') }}</h3>
            <p>{{ t('Sovrascrivono i colori base quando il dark mode è attivo') }}</p>
          </div>
        </div>
        <svg :class="['olo-chevron', sections.darkColors && 'open']" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
      </button>
      <div v-show="sections.darkColors" class="olo-card-body">
        <div class="olo-color-grid">
          <div v-for="(value, key) in stylesStore.darkColors" :key="'dk-' + key" class="olo-color-item">
            <label>{{ formatLabel(key) }}</label>
            <FieldColor
              :modelValue="value"
              @update:modelValue="stylesStore.updateDarkColor(key, $event)"
            />
          </div>
        </div>
      </div>
    </div>

    <!-- Tipografia -->
    <div class="olo-card">
      <button class="olo-card-head olo-card-toggle" @click="toggle('typo')">
        <div style="display:flex;align-items:flex-start;gap:14px">
          <div class="olo-card-icon" style="background:#f5f0eb;color:#1a1a1a">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><polyline points="4 7 4 4 20 4 20 7"/><line x1="9" y1="20" x2="15" y2="20"/><line x1="12" y1="4" x2="12" y2="20"/></svg>
          </div>
          <div>
            <h3>{{ t('Tipografia') }}</h3>
            <p>{{ t('Font, dimensioni e pesi del testo') }}</p>
          </div>
        </div>
        <svg :class="['olo-chevron', sections.typo && 'open']" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
      </button>
      <div v-show="sections.typo" class="olo-card-body">
        <div class="olo-fields-grid">
          <div class="olo-field-block">
            <label>{{ t('Font corpo') }}</label>
            <FieldFontFamily
              :modelValue="stylesStore.typography.font_family"
              @update:modelValue="stylesStore.updateTypography('font_family', $event)"
            />
          </div>
          <div class="olo-field-block">
            <label>{{ t('Font titoli') }}</label>
            <FieldFontFamily
              :modelValue="stylesStore.typography.font_family_heading"
              @update:modelValue="stylesStore.updateTypography('font_family_heading', $event)"
            />
          </div>
          <div class="olo-field-block">
            <label>{{ t('Dimensione font base') }}</label>
            <FieldText
              :modelValue="stylesStore.typography.font_size_base"
              @update:modelValue="stylesStore.updateTypography('font_size_base', $event)"
            />
          </div>
          <div v-for="i in 6" :key="'h' + i" class="olo-field-block">
            <label>{{ t('Dimensione') }} H{{ i }}</label>
            <FieldText
              :modelValue="stylesStore.typography['font_size_h' + i]"
              @update:modelValue="stylesStore.updateTypography('font_size_h' + i, $event)"
            />
          </div>
          <div class="olo-field-block">
            <label>{{ t('Interlinea') }}</label>
            <FieldText
              :modelValue="stylesStore.typography.line_height"
              @update:modelValue="stylesStore.updateTypography('line_height', $event)"
            />
          </div>
          <div class="olo-field-block">
            <label>{{ t('Peso titoli') }}</label>
            <FieldSelect
              :modelValue="stylesStore.typography.font_weight_heading"
              :options="weightOptions"
              @update:modelValue="stylesStore.updateTypography('font_weight_heading', $event)"
            />
          </div>
        </div>
      </div>
    </div>

    <!-- Layout -->
    <div class="olo-card">
      <button class="olo-card-head olo-card-toggle" @click="toggle('layout')">
        <div style="display:flex;align-items:flex-start;gap:14px">
          <div class="olo-card-icon" style="background:#f0f0f0;color:#1a1a1a">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
          </div>
          <div>
            <h3>{{ t('Layout') }}</h3>
            <p>{{ t('Bordi, contenitore e dimensioni generali') }}</p>
          </div>
        </div>
        <svg :class="['olo-chevron', sections.layout && 'open']" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
      </button>
      <div v-show="sections.layout" class="olo-card-body">
        <div class="olo-fields-grid">
          <div class="olo-field-block">
            <label>{{ t('Raggio bordi') }}</label>
            <FieldBox mode="corners"
              :modelValue="parseBorderRadius(stylesStore.layout.border_radius)"
              @update:modelValue="stylesStore.updateLayout('border_radius', $event)"
            />
          </div>
          <div class="olo-field-block">
            <label>{{ t('Raggio bordi grande') }}</label>
            <FieldBox mode="corners"
              :modelValue="parseBorderRadius(stylesStore.layout.border_radius_large)"
              @update:modelValue="stylesStore.updateLayout('border_radius_large', $event)"
            />
          </div>
        </div>
        <div class="olo-moved-hint">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
          <span>{{ t('Larghezza container e scala spacing sono ora in') }} <a href="#" @click.prevent="goToSpaziature">{{ t('Spaziature & layout') }}</a></span>
        </div>
      </div>
    </div>

    <!-- Scala Border Radius -->
    <div class="olo-card">
      <button class="olo-card-head olo-card-toggle" @click="toggle('radius')">
        <div style="display:flex;align-items:flex-start;gap:14px">
          <div class="olo-card-icon" style="background:#f5f5f5;color:#666">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="18" height="18" rx="6"/></svg>
          </div>
          <div>
            <h3>{{ t('Scala Border Radius') }}</h3>
            <p>{{ t('Valori predefiniti dei bordi arrotondati') }}</p>
          </div>
        </div>
        <svg :class="['olo-chevron', sections.radius && 'open']" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
      </button>
      <div v-show="sections.radius" class="olo-card-body">
        <div class="olo-fields-grid">
          <div v-for="(lbl, key) in radiusLabels" :key="key" class="olo-field-block">
            <label>{{ lbl }}</label>
            <FieldText
              :modelValue="(stylesStore.styles.border_radius_scale || {})[key] || radiusDefaults[key]"
              @update:modelValue="stylesStore.updateRadiusScale(key, $event)"
            />
          </div>
        </div>
      </div>
    </div>

    <!-- Ombre Globali -->
    <div class="olo-card">
      <button class="olo-card-head olo-card-toggle" @click="toggle('shadows')">
        <div style="display:flex;align-items:flex-start;gap:14px">
          <div class="olo-card-icon" style="background:#f0f0f0;color:#444">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="18" height="18" rx="2"/><rect x="6" y="6" width="18" height="18" rx="2" opacity=".3"/></svg>
          </div>
          <div>
            <h3>{{ t('Ombre Globali') }}</h3>
            <p>{{ t('Box-shadow per i vari livelli di elevazione') }}</p>
          </div>
        </div>
        <svg :class="['olo-chevron', sections.shadows && 'open']" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
      </button>
      <div v-show="sections.shadows" class="olo-card-body">
        <div class="olo-fields-grid olo-fields-1col">
          <div v-for="(lbl, key) in shadowLabels" :key="key" class="olo-field-block">
            <label>{{ lbl }}</label>
            <FieldText
              :modelValue="(stylesStore.styles.shadows || {})[key] || shadowDefaults[key]"
              @update:modelValue="stylesStore.updateShadow(key, $event)"
            />
          </div>
        </div>
      </div>
    </div>

    <!-- Design Tokens -->
    <div class="olo-card">
      <div class="olo-card-head">
        <div class="olo-card-icon" style="background:#1a1a1a;color:#fff">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
        </div>
        <div>
          <h3>{{ t('Design Tokens') }}</h3>
          <p>{{ t('Esporta/importa tutti gli stili come JSON') }}</p>
        </div>
      </div>
      <div class="olo-card-body">
        <div class="olo-token-actions">
          <button @click="stylesStore.exportDesignTokens()" class="olo-btn-token export">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            {{ t('Esporta JSON') }}
          </button>
          <button @click="importTokens" class="olo-btn-token import">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
            {{ t('Importa JSON') }}
          </button>
        </div>
      </div>
    </div>

    <!-- Google Fonts -->
    <div class="olo-card">
      <button class="olo-card-head olo-card-toggle" @click="toggle('fonts')">
        <div style="display:flex;align-items:flex-start;gap:14px">
          <div class="olo-card-icon" style="background:#fdf0e8;color:#e8622a">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 7V4h16v3M9 20h6M12 4v16"/></svg>
          </div>
          <div>
            <h3>{{ t('Google Fonts') }}</h3>
            <p>{{ t('Font caricati da Google Fonts CDN') }}</p>
          </div>
        </div>
        <svg :class="['olo-chevron', sections.fonts && 'open']" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
      </button>
      <div v-show="sections.fonts" class="olo-card-body">
        <div class="olo-fonts-list">
          <div v-for="font in stylesStore.googleFonts" :key="font" class="olo-font-item">
            <span class="olo-font-name">{{ font }}</span>
            <button @click="stylesStore.removeGoogleFont(font)" class="olo-font-remove" :title="t('Rimuovi')">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
          </div>
          <div v-if="!stylesStore.googleFonts?.length" class="olo-fonts-empty">{{ t('Nessun font aggiunto') }}</div>
        </div>
        <div class="olo-font-add">
          <input
            v-model="newFontName"
            type="text"
            :placeholder="t('Nome font (es. Inter, Poppins...)')"
            class="olo-field-input"
            @keyup.enter="addFont"
          />
          <button @click="addFont" class="olo-btn-add" :disabled="!newFontName.trim()">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            {{ t('Aggiungi') }}
          </button>
        </div>
      </div>
    </div>

    <!-- Grana / Texture -->
    <div class="olo-card">
      <button class="olo-card-head olo-card-toggle" @click="toggle('grain')">
        <div style="display:flex;align-items:flex-start;gap:14px">
          <div class="olo-card-icon" style="background:#1a1a1a;color:#fff">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="5" cy="6" r="1"/><circle cx="12" cy="5" r="1"/><circle cx="19" cy="7" r="1"/><circle cx="7" cy="13" r="1"/><circle cx="15" cy="12" r="1"/><circle cx="20" cy="15" r="1"/><circle cx="5" cy="19" r="1"/><circle cx="13" cy="19" r="1"/></svg>
          </div>
          <div>
            <h3>{{ t('Grana / Texture') }}</h3>
            <p>{{ t('Sovrapposizione di rumore fine su tutta la pagina') }}</p>
          </div>
        </div>
        <svg :class="['olo-chevron', sections.grain && 'open']" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
      </button>
      <div v-show="sections.grain" class="olo-card-body">
        <div class="olo-color-item" style="margin-bottom:14px">
          <label style="display:flex;align-items:center;gap:10px;cursor:pointer">
            <input
              type="checkbox"
              :checked="stylesStore.grain.enabled"
              @change="stylesStore.updateGrain('enabled', $event.target.checked)"
            />
            {{ t('Attiva grana su tutto il sito') }}
          </label>
        </div>
        <template v-if="stylesStore.grain.enabled">
          <div class="olo-color-item" style="margin-bottom:12px">
            <label>{{ t('Opacità') }} ({{ stylesStore.grain.opacity ?? 6 }}%)</label>
            <input
              type="range" min="0" max="30" step="1"
              :value="stylesStore.grain.opacity ?? 6"
              @input="stylesStore.updateGrain('opacity', parseInt($event.target.value))"
              style="width:100%"
            />
          </div>
          <div class="olo-color-item">
            <label>{{ t('Dimensione grana (px)') }} ({{ stylesStore.grain.scale ?? 180 }})</label>
            <input
              type="range" min="60" max="400" step="10"
              :value="stylesStore.grain.scale ?? 180"
              @input="stylesStore.updateGrain('scale', parseInt($event.target.value))"
              style="width:100%"
            />
          </div>
        </template>
      </div>
    </div>

    <!-- Save / Reset -->
    <div class="olo-actions">
      <button
        @click="saveStyles"
        :disabled="stylesStore.isSaving || !stylesStore.isDirty"
        class="olo-btn-save"
        :class="{ disabled: !stylesStore.isDirty }"
      >
        <svg v-if="!stylesStore.isSaving" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
        <span v-if="stylesStore.isSaving" class="olo-spinner"></span>
        {{ stylesStore.isSaving ? t('Salvataggio...') : t('Salva stili') }}
      </button>
      <button
        @click="stylesStore.resetStyles()"
        :disabled="stylesStore.isSaving"
        class="olo-btn-reset"
      >
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 4v6h6"/><path d="M3.51 15a9 9 0 105.42-8.37L1 10"/></svg>
        {{ t('Ripristina') }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, inject } from 'vue';
import { t } from '@/i18n';
import { useStylesStore } from '@/stores/styles';
import FieldColor from '../Builder/fields/FieldColor.vue';
import FieldText from '../Builder/fields/FieldText.vue';
import FieldSelect from '../Builder/fields/FieldSelect.vue';
import FieldBox from '../Builder/fields/FieldBox.vue';
import FieldFontFamily from '../Builder/fields/FieldFontFamily.vue';

const stylesStore = useStylesStore();
const showToast = inject('showToast', () => {});
const newFontName = ref('');
const newPresetName = ref('');

// Load custom presets on mount
stylesStore.loadCustomPresets();

async function savePreset() {
  const name = newPresetName.value.trim();
  if (!name) return;
  try {
    await stylesStore.saveCurrentAsPreset(name);
    newPresetName.value = '';
    showToast(t('Preset salvato'));
  } catch {
    showToast(t('Errore nel salvataggio del preset'), 'error');
  }
}

async function deletePreset(id) {
  try {
    await stylesStore.deleteCustomPreset(id);
    showToast(t('Preset eliminato'));
  } catch {
    showToast(t('Errore nell\'eliminazione del preset'), 'error');
  }
}

const sections = reactive({
  colors: true,
  darkColors: false,
  typo: false,
  layout: false,
  radius: false,
  shadows: false,
  fonts: false,
  grain: false,
});

function toggle(key) {
  sections[key] = !sections[key];
}

const weightOptions = [
  { value: '300', label: 'Leggero (300)' },
  { value: '400', label: 'Normale (400)' },
  { value: '500', label: 'Medio (500)' },
  { value: '600', label: 'Semi Grassetto (600)' },
  { value: '700', label: 'Grassetto (700)' },
  { value: '800', label: 'Extra Grassetto (800)' },
  { value: '900', label: 'Nero (900)' },
];

function formatLabel(key) {
  return key.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
}

function parseBorderRadius(val) {
  if (val && typeof val === 'object') return val;
  const s = String(val || '0');
  return parseInt(s) || 0;
}

const radiusLabels = { sm: 'Piccolo (4px)', md: 'Medio (8px)', lg: 'Grande (16px)', full: 'Pieno (9999px)' };
const radiusDefaults = { sm: '4px', md: '8px', lg: '16px', full: '9999px' };
const shadowLabels = { sm: 'Leggera', md: 'Media', lg: 'Forte', xl: 'Molto forte' };
const shadowDefaults = { sm: '0 1px 2px 0 rgba(0,0,0,0.05)', md: '0 4px 6px -1px rgba(0,0,0,0.1)', lg: '0 10px 15px -3px rgba(0,0,0,0.1)', xl: '0 20px 25px -5px rgba(0,0,0,0.1)' };

function importTokens() {
  const input = document.createElement('input');
  input.type = 'file';
  input.accept = '.json';
  input.onchange = async (e) => {
    const file = e.target.files[0];
    if (!file) return;
    try {
      const text = await file.text();
      const tokens = JSON.parse(text);
      stylesStore.importDesignTokens(tokens);
      showToast(t('Design tokens importati'));
    } catch (err) {
      console.error('Import tokens error:', err);
      showToast(t('Errore importazione'), 'error');
    }
  };
  input.click();
}

function addFont() {
  const name = newFontName.value.trim();
  if (name) {
    stylesStore.addGoogleFont(name);
    newFontName.value = '';
  }
}

async function saveStyles() {
  await stylesStore.saveStyles();
  showToast(t('Stili salvati con successo'));
}

function goToSpaziature() {
  const url = new URL(window.location.href);
  url.searchParams.set('tab', 'spaziature');
  window.location.href = url.toString();
}
</script>

<style scoped>
/* ── Presets Grid ── */
.olo-presets-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
  gap: 8px;
}
.olo-preset-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 14px;
  background: #fafafa;
  border: 1.5px solid #eaeaea;
  border-radius: 10px;
  font-size: 13px;
  font-weight: 600;
  color: #1a1a1a;
  cursor: pointer;
  transition: all 0.15s;
}
.olo-preset-btn:hover {
  border-color: #1a1a1a;
  background: #f5f5f5;
}
.olo-preset-dot {
  width: 14px;
  height: 14px;
  border-radius: 50%;
  flex-shrink: 0;
  border: 2px solid rgba(0,0,0,0.06);
}

/* ── Custom Presets ── */
.olo-custom-presets {
  margin-top: 16px;
  padding-top: 16px;
  border-top: 1px solid #f0f0f0;
}
.olo-custom-presets-label {
  font-size: 11px;
  font-weight: 600;
  color: #999;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  margin-bottom: 8px;
}
.olo-preset-btn--custom {
  position: relative;
  padding-right: 32px;
}
.olo-preset-btn-name {
  flex: 1;
  min-width: 0;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
.olo-preset-delete {
  position: absolute;
  right: 6px;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  color: #ccc;
  cursor: pointer;
  padding: 4px;
  border-radius: 4px;
  display: flex;
  opacity: 0;
  transition: all 0.15s;
}
.olo-preset-btn--custom:hover .olo-preset-delete {
  opacity: 1;
}
.olo-preset-delete:hover {
  color: #e84430;
  background: #fef2f2;
}

/* ── Preset save row ── */
.olo-preset-save-row {
  display: flex;
  gap: 8px;
  margin-top: 16px;
  padding-top: 16px;
  border-top: 1px solid #f0f0f0;
}
.olo-preset-name-input {
  flex: 1;
}

/* ── Toggle headers ── */
.olo-card-toggle {
  width: 100%;
  cursor: pointer;
  background: none;
  border: none;
  border-bottom: 1px solid #f5f5f5;
  display: flex;
  align-items: center;
  justify-content: space-between;
  text-align: left;
  padding: 20px 24px;
}
.olo-card-toggle:hover {
  background: #fcfcfc;
}
.olo-chevron {
  color: #ccc;
  transition: transform 0.2s;
  flex-shrink: 0;
}
.olo-chevron.open {
  transform: rotate(180deg);
  color: #e8622a;
}

/* ── Color Grid ── */
.olo-color-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 16px;
}
.olo-color-item label {
  display: block;
  font-size: 12px;
  font-weight: 600;
  color: #999;
  margin-bottom: 6px;
  text-transform: uppercase;
  letter-spacing: 0.03em;
}

/* ── Fields Grid ── */
.olo-fields-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 16px;
}
.olo-fields-grid.olo-fields-1col {
  grid-template-columns: 1fr;
}
.olo-field-block label {
  display: block;
  font-size: 12px;
  font-weight: 600;
  color: #999;
  margin-bottom: 6px;
}

/* ── Fonts ── */
.olo-fonts-list {
  margin-bottom: 14px;
}
.olo-font-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 12px 16px;
  background: #fafafa;
  border: 1px solid #eaeaea;
  border-radius: 10px;
  margin-bottom: 6px;
}
.olo-font-name {
  font-size: 13px;
  font-weight: 600;
  color: #1a1a1a;
}
.olo-font-remove {
  background: none;
  border: none;
  color: #ddd;
  cursor: pointer;
  padding: 4px;
  border-radius: 6px;
  display: flex;
  transition: all 0.15s;
}
.olo-font-remove:hover {
  color: #e84430;
  background: #fef2f2;
}
.olo-fonts-empty {
  font-size: 13px;
  color: #bbb;
  text-align: center;
  padding: 20px;
}
.olo-font-add {
  display: flex;
  gap: 8px;
}
.olo-btn-add {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  padding: 9px 18px;
  background: #fff;
  border: 1.5px solid #eaeaea;
  border-radius: 10px;
  font-size: 13px;
  font-weight: 600;
  color: #1a1a1a;
  cursor: pointer;
  transition: all 0.15s;
  white-space: nowrap;
}
.olo-btn-add:hover:not(:disabled) {
  background: #fafafa;
  border-color: #ccc;
}
.olo-btn-add:disabled {
  opacity: 0.3;
  cursor: not-allowed;
}

/* ── Token Actions ── */
.olo-token-actions {
  display: flex;
  gap: 10px;
}
.olo-btn-token {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  padding: 10px 22px;
  border-radius: 10px;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.15s;
  border: 1.5px solid;
}
.olo-btn-token.export {
  background: #1a1a1a;
  color: #fff;
  border-color: #1a1a1a;
}
.olo-btn-token.export:hover {
  background: #333;
}
.olo-btn-token.import {
  background: #fff;
  color: #666;
  border-color: #eaeaea;
}
.olo-btn-token.import:hover {
  background: #fafafa;
  color: #1a1a1a;
}

/* ── Moved hint ── */
.olo-moved-hint {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-top: 16px;
  padding: 10px 14px;
  background: #fef8f0;
  border: 1px dashed #fcd9b6;
  border-radius: 8px;
  font-size: 12.5px;
  color: #92541a;
}
.olo-moved-hint svg { flex-shrink: 0; color: #e8622a; }
.olo-moved-hint a { color: #c54a18; font-weight: 600; text-decoration: underline; cursor: pointer; }
.olo-moved-hint a:hover { color: #8a3410; }

/* ── Save disabled state ── */
.olo-btn-save.disabled {
  background: #ddd;
  cursor: not-allowed;
  box-shadow: none;
}
.olo-btn-save.disabled:hover {
  background: #ddd;
  box-shadow: none;
}
</style>
