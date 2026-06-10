<template>
  <div class="cfg-page-head">
    <div>
      <h1>{{ t('Spaziature') }} <em>{{ t('& layout') }}</em></h1>
      <p>{{ t('Token globali di spacing, larghezze container, ritmo verticale delle sezioni e comportamento responsive.') }}</p>
    </div>
    <div class="head-actions">
      <button class="cfg-btn cfg-btn-ghost" @click="resetAll">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7v6h6"/><path d="M3 13a9 9 0 1 0 3-6.7L3 9"/></svg>
        {{ t('Ripristina default') }}
      </button>
    </div>
  </div>

  <!-- ── 1. SPACING SCALE ── -->
  <div class="cfg-card">
    <div class="cfg-card-head">
      <div class="head-ic">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 3H3M21 21H3M12 7v10M8 7l4-4 4 4M8 17l4 4 4-4"/></svg>
      </div>
      <div>
        <h3>{{ t('Scala di spacing') }}</h3>
        <p>{{ t('8 token globali usati ovunque (--olo-space-*). Tutti i padding e i gap del builder pescano da qui.') }}</p>
      </div>
    </div>
    <div class="cfg-card-body">
      <div class="sp-grid">
        <div v-for="(label, key) in spacingLabels" :key="key" class="sp-cell">
          <label>{{ label }}</label>
          <div class="cfg-input mono">
            <input
              type="text"
              :value="(stylesStore.styles.spacing || {})[key] || spacingDefaults[key]"
              @input="stylesStore.updateSpacing(key, $event.target.value)"
            />
          </div>
          <div class="sp-visual" :style="{ width: visualWidth(key) + 'px' }"></div>
        </div>
      </div>
    </div>
  </div>

  <!-- ── 2. CONTAINER WIDTHS ── -->
  <div class="cfg-card">
    <div class="cfg-card-head">
      <div class="head-ic">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="6" width="18" height="12" rx="2"/><path d="M7 6v12M17 6v12"/></svg>
      </div>
      <div>
        <h3>{{ t('Larghezze container') }}</h3>
        <p>{{ t('Tre preset di larghezza massima del contenuto. Il valore Default è il container principale del sito.') }}</p>
      </div>
    </div>
    <div class="cfg-card-body tight">
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('Narrow') }}</label>
          <div class="hint">{{ t('Per testi lunghi e blog, leggibilità ottimale.') }}</div>
        </div>
        <div class="control-col">
          <div class="cfg-input mono cfg-w-sm">
            <input type="text" :value="layout.container_narrow || '720px'" @input="stylesStore.updateLayout('container_narrow', $event.target.value)" />
          </div>
        </div>
      </div>
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('Default') }}</label>
          <div class="hint">{{ t('Larghezza standard del contenuto del sito.') }}</div>
        </div>
        <div class="control-col">
          <div class="cfg-input mono cfg-w-sm">
            <input type="text" :value="layout.container_max_width || '1200px'" @input="stylesStore.updateLayout('container_max_width', $event.target.value)" />
          </div>
        </div>
      </div>
      <div class="cfg-row no-divider">
        <div class="label-col">
          <label>{{ t('Wide') }}</label>
          <div class="hint">{{ t('Per hero, gallerie ampie, layout immersivi.') }}</div>
        </div>
        <div class="control-col">
          <div class="cfg-input mono cfg-w-sm">
            <input type="text" :value="layout.container_wide || '1440px'" @input="stylesStore.updateLayout('container_wide', $event.target.value)" />
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ── 3. SECTION RHYTHM ── -->
  <div class="cfg-card">
    <div class="cfg-card-head">
      <div class="head-ic">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="5" rx="1"/><rect x="3" y="11" width="18" height="3" rx="1"/><rect x="3" y="16" width="18" height="4" rx="1"/></svg>
      </div>
      <div>
        <h3>{{ t('Ritmo verticale sezioni') }}</h3>
        <p>{{ t('Padding-top/bottom di default per le sezioni. I valori sono token della scala spacing — cambiando lì cambia tutto.') }}</p>
      </div>
    </div>
    <div class="cfg-card-body tight">
      <div v-for="row in sectionRows" :key="row.key" class="cfg-row" :class="row.last ? 'no-divider' : ''">
        <div class="label-col">
          <label>{{ row.label }}</label>
          <div class="hint">{{ row.hint }}</div>
        </div>
        <div class="control-col">
          <div class="cfg-segment">
            <button
              v-for="tok in spacingTokens"
              :key="tok"
              :class="{ 'is-on': (stylesStore.styles.section_padding || {})[row.key] === tok }"
              @click="stylesStore.updateSectionPadding(row.key, tok)"
            >{{ tok.toUpperCase() }}</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ── 4. GUTTER RESPONSIVE ── -->
  <div class="cfg-card">
    <div class="cfg-card-head">
      <div class="head-ic">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6v12M21 6v12M9 6v12M15 6v12"/></svg>
      </div>
      <div>
        <h3>{{ t('Gutter responsive') }}</h3>
        <p>{{ t('Gap tra colonne griglia e padding laterale del container, per ciascun breakpoint.') }}</p>
      </div>
    </div>
    <div class="cfg-card-body tight">
      <div class="gutter-table">
        <div class="gutter-th">
          <span></span>
          <span>🖥️ {{ t('Desktop') }}</span>
          <span>📱 {{ t('Tablet') }}</span>
          <span>📱 {{ t('Mobile') }}</span>
        </div>
        <div class="gutter-tr">
          <label>{{ t('Gap colonne') }}</label>
          <CfgNumber size="" :min="0" :max="120" suffix="px" :model-value="gutter.desktop ?? 32" @update:model-value="stylesStore.updateGutter('desktop', $event)" />
          <CfgNumber size="" :min="0" :max="120" suffix="px" :model-value="gutter.tablet ?? 24" @update:model-value="stylesStore.updateGutter('tablet', $event)" />
          <CfgNumber size="" :min="0" :max="120" suffix="px" :model-value="gutter.mobile ?? 16" @update:model-value="stylesStore.updateGutter('mobile', $event)" />
        </div>
        <div class="gutter-tr">
          <label>{{ t('Padding laterale') }}</label>
          <CfgNumber size="" :min="0" :max="120" suffix="px" :model-value="gutter.side_desktop ?? 32" @update:model-value="stylesStore.updateGutter('side_desktop', $event)" />
          <div class="gutter-cell-dash">—</div>
          <CfgNumber size="" :min="0" :max="120" suffix="px" :model-value="gutter.side_mobile ?? 16" @update:model-value="stylesStore.updateGutter('side_mobile', $event)" />
        </div>
      </div>
    </div>
  </div>

  <!-- ── 5. FLUID SCALING ── -->
  <div class="cfg-card">
    <div class="cfg-card-head">
      <div class="head-ic">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12h18"/><path d="M9 6l-6 6 6 6"/><path d="M15 6l6 6-6 6"/></svg>
      </div>
      <div>
        <h3>{{ t('Scaling fluido responsive') }}</h3>
        <p>{{ t('Rimpicciolisce automaticamente tutti i token spacing su tablet e mobile, senza dover ritoccare ogni tile.') }}</p>
      </div>
      <div class="head-actions">
        <button
          class="cfg-switch"
          :class="{ 'is-on': !!(stylesStore.styles.fluid_scaling || {}).enabled }"
          @click="stylesStore.updateFluidScaling('enabled', !(stylesStore.styles.fluid_scaling || {}).enabled)"
          role="switch"
        ></button>
      </div>
    </div>
    <div v-if="(stylesStore.styles.fluid_scaling || {}).enabled" class="cfg-card-body tight">
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('Fattore tablet') }}</label>
          <div class="hint">{{ t('Es. 0.85 → spacing al 85% sotto 960px.') }}</div>
        </div>
        <div class="control-col">
          <div class="cfg-slider">
            <input type="range" min="0.3" max="1" step="0.05" :value="fluid.tablet ?? 0.85" @input="stylesStore.updateFluidScaling('tablet', parseFloat($event.target.value))" />
            <span class="val">{{ Math.round(((fluid.tablet ?? 0.85) * 100)) }}%</span>
          </div>
        </div>
      </div>
      <div class="cfg-row no-divider">
        <div class="label-col">
          <label>{{ t('Fattore mobile') }}</label>
          <div class="hint">{{ t('Es. 0.65 → spacing al 65% sotto 640px.') }}</div>
        </div>
        <div class="control-col">
          <div class="cfg-slider">
            <input type="range" min="0.3" max="1" step="0.05" :value="fluid.mobile ?? 0.65" @input="stylesStore.updateFluidScaling('mobile', parseFloat($event.target.value))" />
            <span class="val">{{ Math.round(((fluid.mobile ?? 0.65) * 100)) }}%</span>
          </div>
        </div>
      </div>
    </div>
    <div v-else class="cfg-card-body" style="text-align:center; padding:18px 22px; color:var(--c-text-mute); font-size:13px;">
      {{ t('Disattivato — i token spacing restano identici su ogni breakpoint.') }}
    </div>
  </div>
</template>

<script setup>
import { computed, inject, onMounted, onBeforeUnmount, watch } from 'vue';
import { t } from '@/i18n';
import { useStylesStore } from '@/stores/styles';
import CfgNumber from './controls/CfgNumber.vue';

const stylesStore = useStylesStore();
const showToast = inject('showToast', () => {});
const setDirty  = inject('setDirty',  () => {});

const spacingLabels = {
  xs: 'XS', sm: 'SM', md: 'MD', lg: 'LG', xl: 'XL', '2xl': '2XL', '3xl': '3XL', '4xl': '4XL',
};
const spacingDefaults = { xs: '4px', sm: '8px', md: '16px', lg: '24px', xl: '32px', '2xl': '48px', '3xl': '64px', '4xl': '96px' };
const spacingTokens = ['xs', 'sm', 'md', 'lg', 'xl', '2xl', '3xl', '4xl'];

const sectionRows = [
  { key: 'compact',  label: t('Padding sezione compatta'),  hint: t('Per sezioni dense, es. CTA strette.') },
  { key: 'default',  label: t('Padding sezione standard'),  hint: t('Default applicato a tutte le sezioni.') },
  { key: 'spacious', label: t('Padding sezione ampia'),     hint: t('Per hero e showcase importanti.') },
  { key: 'between',  label: t('Spazio tra sezioni'),        hint: t('Margine verticale che separa due sezioni adiacenti.'), last: true },
];

const layout  = computed(() => stylesStore.styles.layout || {});
const gutter  = computed(() => stylesStore.styles.gutter || {});
const fluid   = computed(() => stylesStore.styles.fluid_scaling || {});

function visualWidth(key) {
  const raw = (stylesStore.styles.spacing || {})[key] || spacingDefaults[key];
  const n = parseFloat(raw);
  if (!isFinite(n)) return 0;
  return Math.min(n, 120);
}

watch(() => stylesStore.isDirty, (v) => { if (v) setDirty(true); });

function resetAll() {
  if (!confirm(t('Ripristinare tutti i valori di spaziature, container, ritmo, gutter e scaling?'))) return;
  const sp = { ...spacingDefaults };
  Object.keys(sp).forEach(k => stylesStore.updateSpacing(k, sp[k]));
  stylesStore.updateLayout('container_narrow',    '720px');
  stylesStore.updateLayout('container_max_width', '1200px');
  stylesStore.updateLayout('container_wide',      '1440px');
  stylesStore.updateSectionPadding('compact',  'lg');
  stylesStore.updateSectionPadding('default',  'xl');
  stylesStore.updateSectionPadding('spacious', '2xl');
  stylesStore.updateSectionPadding('between',  'md');
  stylesStore.updateGutter('desktop',      32);
  stylesStore.updateGutter('tablet',       24);
  stylesStore.updateGutter('mobile',       16);
  stylesStore.updateGutter('side_desktop', 32);
  stylesStore.updateGutter('side_mobile',  16);
  stylesStore.updateFluidScaling('enabled', false);
  stylesStore.updateFluidScaling('tablet',  0.85);
  stylesStore.updateFluidScaling('mobile',  0.65);
}

async function onSave() { await stylesStore.saveStyles(); showToast(t('Spaziature salvate')); }

onMounted(() => {
  window.addEventListener('olo-cfg-save', onSave);
});
onBeforeUnmount(() => {
  window.removeEventListener('olo-cfg-save', onSave);
});
</script>

<style scoped>
.sp-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
  gap: 14px;
}
.sp-cell { display: flex; flex-direction: column; gap: 6px; }
.sp-cell label {
  font: 600 11px var(--c-mono);
  letter-spacing: .06em;
  color: var(--c-navy);
  text-transform: uppercase;
}
.sp-visual {
  height: 6px;
  background: var(--c-red);
  border-radius: 999px;
  transition: width .2s;
  max-width: 120px;
}

.gutter-table { display: grid; gap: 8px; }
.gutter-th {
  display: grid;
  grid-template-columns: 160px repeat(3, 1fr);
  gap: 10px;
  font: 600 11px var(--c-sans);
  text-transform: uppercase;
  letter-spacing: .06em;
  color: var(--c-text-faint);
  padding: 0 0 6px;
}
.gutter-tr {
  display: grid;
  grid-template-columns: 160px repeat(3, 1fr);
  gap: 10px;
  align-items: center;
  padding: 8px 0;
  border-top: 1px solid var(--c-line-soft);
}
.gutter-tr label {
  font: 600 13px var(--c-sans);
  color: var(--c-navy);
}
.gutter-cell-dash {
  color: var(--c-text-faint);
  text-align: center;
  font-family: var(--c-mono);
}

.cfg-segment {
  flex-wrap: wrap;
}
.cfg-segment button { font-family: var(--c-mono); font-size: 11.5px; }
</style>
