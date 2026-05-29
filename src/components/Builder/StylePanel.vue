<template>
  <div>
    <!-- Header -->
    <div class="mb-flex mb-items-center mb-justify-between mb-mb-4">
      <h3 class="mb-text-sm mb-font-semibold mb-text-gray-200">{{ t('Sistema Stili') }}</h3>
      <button
        @click="builderStore.toggleStylePanel()"
        class="mb-text-gray-500 hover:mb-text-gray-300 mb-text-lg"
      >{{ t('&times;') }}</button>
    </div>

    <!-- Preset bar -->
    <div class="mb-mb-4">
      <label class="mb-block mb-text-xs mb-font-semibold mb-text-gray-400 mb-mb-2">{{ t('Presets') }}</label>
      <div class="mb-grid mb-grid-cols-3 mb-gap-1">
        <button
          v-for="(preset, key) in stylesStore.presets"
          :key="key"
          @click="stylesStore.applyPreset(key)"
          class="mb-px-2 mb-py-1.5 mb-text-[10px] mb-font-medium mb-rounded-md mb-border mb-border-gray-600 mb-text-gray-300 hover:mb-border-primary-500 hover:mb-text-primary-300 mb-transition-colors"
        >
          <span
            class="mb-inline-block mb-w-2 mb-h-2 mb-rounded-full mb-mr-1"
            :style="{ backgroundColor: preset.colors.primary }"
          ></span>
          {{ preset.name }}
        </button>
      </div>
    </div>

    <!-- Sections -->
    <CollapseSection :title="t('Colori')" :defaultOpen="true">
      <div class="mb-space-y-2.5">
        <div v-for="(value, key) in stylesStore.colors" :key="key">
          <label class="mb-block mb-text-[10px] mb-font-medium mb-text-gray-400 mb-mb-0.5">
            {{ formatLabel(key) }}
          </label>
          <FieldColor
            :modelValue="value"
            @update:modelValue="stylesStore.updateColor(key, $event)"
          />
        </div>
      </div>
    </CollapseSection>

    <CollapseSection :title="t('Colori Dark Mode')">
      <p class="mb-text-[10px] mb-text-gray-500 mb-mb-3">{{ t('Colori usati quando il Dark Mode Toggle è attivo. Sovrascrivono i colori base.') }}</p>
      <div class="mb-space-y-2.5">
        <div v-for="(value, key) in stylesStore.darkColors" :key="'dk-' + key">
          <label class="mb-block mb-text-[10px] mb-font-medium mb-text-gray-400 mb-mb-0.5">
            {{ formatLabel(key) }}
          </label>
          <FieldColor
            :modelValue="value"
            @update:modelValue="stylesStore.updateDarkColor(key, $event)"
          />
        </div>
      </div>
    </CollapseSection>

    <CollapseSection :title="t('Tipografia')">
      <div class="mb-space-y-2.5">
        <!-- Font Family (body) -->
        <div>
          <label class="mb-block mb-text-[10px] mb-font-medium mb-text-gray-400 mb-mb-0.5">{{ t('Font corpo') }}</label>
          <FieldFontFamily
            :modelValue="stylesStore.typography.font_family"
            @update:modelValue="stylesStore.updateTypography('font_family', $event)"
          />
        </div>
        <!-- Font Family (heading) -->
        <div>
          <label class="mb-block mb-text-[10px] mb-font-medium mb-text-gray-400 mb-mb-0.5">{{ t('Font titoli') }}</label>
          <FieldFontFamily
            :modelValue="stylesStore.typography.font_family_heading"
            @update:modelValue="stylesStore.updateTypography('font_family_heading', $event)"
          />
        </div>
        <!-- Base font size -->
        <div>
          <label class="mb-block mb-text-[10px] mb-font-medium mb-text-gray-400 mb-mb-0.5">{{ t('Dimensione font base') }}</label>
          <FieldText
            :modelValue="stylesStore.typography.font_size_base"
            @update:modelValue="stylesStore.updateTypography('font_size_base', $event)"
          />
        </div>
        <!-- Heading sizes -->
        <div v-for="i in 6" :key="'h' + i">
          <label class="mb-block mb-text-[10px] mb-font-medium mb-text-gray-400 mb-mb-0.5">Dimensione H{{ i }}</label>
          <FieldText
            :modelValue="stylesStore.typography['font_size_h' + i]"
            @update:modelValue="stylesStore.updateTypography('font_size_h' + i, $event)"
          />
        </div>
        <!-- Line Height -->
        <div>
          <label class="mb-block mb-text-[10px] mb-font-medium mb-text-gray-400 mb-mb-0.5">{{ t('Interlinea') }}</label>
          <FieldText
            :modelValue="stylesStore.typography.line_height"
            @update:modelValue="stylesStore.updateTypography('line_height', $event)"
          />
        </div>
        <!-- Heading Weight -->
        <div>
          <label class="mb-block mb-text-[10px] mb-font-medium mb-text-gray-400 mb-mb-0.5">{{ t('Peso titoli') }}</label>
          <FieldSelect
            :modelValue="stylesStore.typography.font_weight_heading"
            :options="weightOptions"
            @update:modelValue="stylesStore.updateTypography('font_weight_heading', $event)"
          />
        </div>
      </div>
    </CollapseSection>

    <CollapseSection :title="t('Layout')">
      <div class="mb-space-y-2.5">
        <div>
          <label class="mb-block mb-text-[10px] mb-font-medium mb-text-gray-400 mb-mb-0.5">{{ t('Raggio bordi') }}</label>
          <FieldBox mode="corners"
            :modelValue="parseBorderRadius(stylesStore.layout.border_radius)"
            @update:modelValue="stylesStore.updateLayout('border_radius', $event)"
          />
        </div>
        <div>
          <label class="mb-block mb-text-[10px] mb-font-medium mb-text-gray-400 mb-mb-0.5">{{ t('Raggio bordi grande') }}</label>
          <FieldBox mode="corners"
            :modelValue="parseBorderRadius(stylesStore.layout.border_radius_large)"
            @update:modelValue="stylesStore.updateLayout('border_radius_large', $event)"
          />
        </div>
        <div>
          <label class="mb-block mb-text-[10px] mb-font-medium mb-text-gray-400 mb-mb-0.5">{{ t('Larghezza max contenitore') }}</label>
          <FieldText
            :modelValue="stylesStore.layout.container_max_width"
            @update:modelValue="stylesStore.updateLayout('container_max_width', $event)"
          />
        </div>
      </div>
    </CollapseSection>

    <CollapseSection :title="t('Scala Spaziatura')">
      <p class="mb-text-[9px] mb-text-gray-500 mb-mb-2">{{ t('8 livelli di spacing globali (CSS custom properties --olo-space-*)') }}</p>
      <div class="mb-space-y-2">
        <div v-for="(lbl, key) in spacingLabels" :key="key">
          <label class="mb-block mb-text-[10px] mb-font-medium mb-text-gray-400 mb-mb-0.5">{{ lbl }}</label>
          <FieldText
            :modelValue="(stylesStore.styles.spacing || {})[key] || spacingDefaults[key]"
            @update:modelValue="stylesStore.updateSpacing(key, $event)"
          />
        </div>
      </div>
    </CollapseSection>

    <CollapseSection :title="t('Scala Border Radius')">
      <div class="mb-space-y-2">
        <div v-for="(lbl, key) in radiusLabels" :key="key">
          <label class="mb-block mb-text-[10px] mb-font-medium mb-text-gray-400 mb-mb-0.5">{{ lbl }}</label>
          <FieldText
            :modelValue="(stylesStore.styles.border_radius_scale || {})[key] || radiusDefaults[key]"
            @update:modelValue="stylesStore.updateRadiusScale(key, $event)"
          />
        </div>
      </div>
    </CollapseSection>

    <CollapseSection :title="t('Ombre Globali')">
      <div class="mb-space-y-2">
        <div v-for="(lbl, key) in shadowLabels" :key="key">
          <label class="mb-block mb-text-[10px] mb-font-medium mb-text-gray-400 mb-mb-0.5">{{ lbl }}</label>
          <FieldText
            :modelValue="(stylesStore.styles.shadows || {})[key] || shadowDefaults[key]"
            @update:modelValue="stylesStore.updateShadow(key, $event)"
          />
        </div>
      </div>
    </CollapseSection>

    <CollapseSection :title="t('Design Tokens')">
      <p class="mb-text-[9px] mb-text-gray-500 mb-mb-2">{{ t('Esporta/importa tutti gli stili come file JSON per riutilizzarli su altri siti.') }}</p>
      <div class="mb-flex mb-gap-2">
        <button
          @click="stylesStore.exportDesignTokens()"
          class="mb-flex-1 mb-py-1.5 mb-text-[10px] mb-font-medium mb-rounded-md mb-border mb-border-cyan-600 mb-text-cyan-400 hover:mb-bg-cyan-600/20 mb-transition-colors"
        >{{ t('Esporta JSON') }}</button>
        <button
          @click="importTokens"
          class="mb-flex-1 mb-py-1.5 mb-text-[10px] mb-font-medium mb-rounded-md mb-border mb-border-gray-600 mb-text-gray-400 hover:mb-bg-gray-700 mb-transition-colors"
        >{{ t('Importa JSON') }}</button>
      </div>
    </CollapseSection>

    <CollapseSection :title="t('Google Fonts')">
      <div class="mb-space-y-2">
        <!-- Existing fonts -->
        <div
          v-for="font in stylesStore.googleFonts"
          :key="font"
          class="mb-flex mb-items-center mb-justify-between mb-bg-gray-700 mb-rounded-md mb-px-2 mb-py-1.5"
        >
          <span class="mb-text-xs mb-text-gray-300">{{ font }}</span>
          <button
            @click="stylesStore.removeGoogleFont(font)"
            class="mb-text-gray-500 hover:mb-text-red-400 mb-text-sm"
          >{{ t('&times;') }}</button>
        </div>
        <!-- Add new -->
        <div class="mb-flex mb-gap-1">
          <input
            v-model="newFontName"
            type="text"
            :placeholder="t('Nome font (es. Inter)')"
            class="mb-flex-1 mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1 mb-text-xs mb-text-gray-900"
            @keyup.enter="addFont"
          />
          <button
            @click="addFont"
            class="mb-px-2 mb-py-1 mb-text-xs mb-bg-gray-600 mb-text-gray-300 mb-rounded-md hover:mb-bg-gray-500 mb-transition-colors"
          >+</button>
        </div>
      </div>
    </CollapseSection>

    <!-- Save / Reset buttons -->
    <div class="mb-flex mb-gap-2 mb-mt-4 mb-pt-3 mb-border-t mb-border-gray-700">
      <button
        @click="stylesStore.saveStyles()"
        :disabled="stylesStore.isSaving || !stylesStore.isDirty"
        :class="[
          'mb-flex-1 mb-py-2 mb-text-xs mb-font-medium mb-rounded-md mb-transition-colors',
          stylesStore.isDirty
            ? 'mb-bg-primary-600 mb-text-white hover:mb-bg-primary-700'
            : 'mb-bg-gray-700 mb-text-gray-500 mb-cursor-not-allowed'
        ]"
      >
        {{ stylesStore.isSaving ? 'Salvataggio...' : 'Salva stili' }}
      </button>
      <button
        @click="stylesStore.resetStyles()"
        :disabled="stylesStore.isSaving"
        class="mb-px-3 mb-py-2 mb-text-xs mb-font-medium mb-rounded-md mb-border mb-border-gray-600 mb-text-gray-400 hover:mb-text-gray-200 hover:mb-border-gray-500 mb-transition-colors"
      >
        {{ t('Ripristina') }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { t } from '@/i18n';
import { ref } from 'vue';
import { useBuilderStore } from '@/stores/builder';
import { useStylesStore } from '@/stores/styles';
import CollapseSection from './CollapseSection.vue';
import FieldColor from './fields/FieldColor.vue';
import FieldText from './fields/FieldText.vue';
import FieldSelect from './fields/FieldSelect.vue';
import FieldBox from './fields/FieldBox.vue';
import FieldFontFamily from './fields/FieldFontFamily.vue';

const builderStore = useBuilderStore();
const stylesStore = useStylesStore();

const newFontName = ref('');

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

const spacingLabels = { xs: 'XS (4px)', sm: 'SM (8px)', md: 'MD (16px)', lg: 'LG (24px)', xl: 'XL (32px)', '2xl': '2XL (48px)', '3xl': '3XL (64px)', '4xl': '4XL (96px)' };
const spacingDefaults = { xs: '4px', sm: '8px', md: '16px', lg: '24px', xl: '32px', '2xl': '48px', '3xl': '64px', '4xl': '96px' };
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
    } catch (err) {
      console.error('Import tokens error:', err);
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
</script>
