<template>
  <div class="mb-font-sans" :style="widgetStyle">
    <!-- Service header -->
    <div :style="headerStyle">
      <div style="display:flex;align-items:center;gap:12px">
        <div class="olo-bp-svc-icon" :style="{ width:'48px',height:'48px',borderRadius:'10px',background:s.primary_color,display:'flex',alignItems:'center',justifyContent:'center',color: TOKENS.onPrimary,flexShrink:0 }" v-html="calendarSvg"></div>
        <div>
          <div :style="{ fontSize: s.title_size+'px', fontWeight: s.title_weight, color: s.title_color || TOKENS.text }">
            {{ serviceLabel }}
          </div>
          <div :style="{ fontSize:'13px', color: resolveColor(s.meta_color, TOKENS.textSoft), display:'flex', alignItems:'center', gap:'10px', marginTop:'2px' }">
            <span v-if="s.show_duration" style="display:inline-flex;align-items:center;gap:4px"><span class="olo-bp-meta-icon" v-html="clockSvg"></span>{{ t('60 min') }}</span>
            <span v-if="s.show_price" :style="{ fontWeight:'600', color: s.primary_color }">{{ t('&euro; 80,00') }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Calendar mini -->
    <div style="padding:16px 20px">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px">
        <button type="button" class="olo-bp-nav" :style="navBtnStyle">{{ t('&lsaquo;') }}</button>
        <div style="font-size:15px;font-weight:700;text-transform:capitalize">{{ t('Febbraio 2026') }}</div>
        <button type="button" class="olo-bp-nav" :style="navBtnStyle">{{ t('&rsaquo;') }}</button>
      </div>
      <div style="display:grid;grid-template-columns:repeat(7,1fr);text-align:center;gap:4px">
        <span v-for="(d, di) in ['L','M','M','G','V','S','D']" :key="di" :style="{ fontSize:'10px', fontWeight:'600', color: TOKENS.textFaint, textTransform:'uppercase', padding:'3px 0' }">{{ d }}</span>
        <button v-for="i in 14" :key="i" type="button"
             class="olo-bp-day"
             :style="{ aspectRatio:'1', display:'flex', alignItems:'center', justifyContent:'center', borderRadius:'6px', fontSize:'11px', fontWeight:'500', background: i % 3 === 0 ? `color-mix(in srgb, ${s.available_color} 12%, transparent)` : 'transparent', color: i % 3 === 0 ? s.available_color : TOKENS.textFaint }">
          {{ i }}
        </button>
      </div>
    </div>

    <!-- CTA -->
    <div style="padding:0 20px 20px">
      <button type="button" class="olo-bp-cta" :style="{ ...ctaStyle, width:'100%', border:'none' }">{{ t('Conferma prenotazione') }}</button>
    </div>
  </div>
</template>

<script setup>
import { t } from '@/i18n';
import { computed } from 'vue';
import iconsSvg from '../ProSlider/uikitIconsSvg.js';
import { resolveColor, TOKENS, SHADOW } from '@/composables/oloTileDefaults';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });

const calendarSvg = iconsSvg['calendar'] || '';
const clockSvg = iconsSvg['clock'] || '';

const defaults = {
  service_id: '', primary_color: 'var(--olo-color-primary, #e1474f)', show_price: true, show_duration: true,
  widget_max_width: 480, widget_bg: '#FFFFFF', widget_border_radius: 12,
  widget_border_color: '', widget_shadow: 'sm', btn_bg: 'var(--olo-color-primary, #e1474f)', btn_color: '#FFFFFF',
  btn_radius: 8, available_color: 'var(--olo-color-primary, #e1474f)', full_color: '', slot_border_radius: 8,
  title_size: 18, title_weight: '700', title_color: '', meta_color: '', success_color: '',
};

const s = computed(() => ({ ...defaults, ...props.settings }));

const serviceLabel = computed(() => {
  if (!s.value.service_id) return 'Seleziona un servizio...';
  const list = window.oloData?.serviceList || [];
  const found = list.find(x => String(x.value) === String(s.value.service_id));
  return found ? found.label : 'Servizio #' + s.value.service_id;
});

const shadowMap = SHADOW;

const borderColor = computed(() => resolveColor(s.value.widget_border_color, TOKENS.border));

const widgetStyle = computed(() => ({
  maxWidth: s.value.widget_max_width + 'px',
  background: s.value.widget_bg,
  borderRadius: s.value.widget_border_radius + 'px',
  border: '1px solid ' + borderColor.value,
  boxShadow: shadowMap[s.value.widget_shadow] || 'none',
  overflow: 'hidden',
}));

const headerStyle = computed(() => ({
  padding: '16px 20px',
  borderBottom: '1px solid ' + borderColor.value,
}));

const navBtnStyle = computed(() => ({
  width: '30px', height: '30px', borderRadius: '6px', border: '1px solid ' + borderColor.value,
  background: 'transparent',
  display: 'flex', alignItems: 'center', justifyContent: 'center',
  fontSize: '16px', color: TOKENS.textSoft, cursor: 'pointer',
}));

const ctaStyle = computed(() => ({
  padding: '12px 24px', borderRadius: s.value.btn_radius + 'px',
  background: s.value.btn_bg, color: s.value.btn_color,
  textAlign: 'center', fontWeight: '600', fontSize: '14px', cursor: 'pointer',
}));
</script>

<style scoped>
.olo-bp-svc-icon :deep(svg) { width: 22px; height: 22px; stroke: currentColor; fill: none; }
.olo-bp-meta-icon { display: inline-flex; }
.olo-bp-meta-icon :deep(svg) { width: 13px; height: 13px; stroke: currentColor; fill: none; }
.olo-bp-nav, .olo-bp-day {
  border: none;
  font-family: inherit;
  cursor: pointer;
  transition: box-shadow 0.15s ease, background 0.15s ease;
}
.olo-bp-day:focus-visible,
.olo-bp-nav:focus-visible,
.olo-bp-cta:focus-visible {
  outline: none;
  box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent);
}
</style>
