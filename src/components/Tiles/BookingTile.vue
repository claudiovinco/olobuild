<template>
  <div class="mb-font-sans" :style="widgetStyle">
    <!-- Service header -->
    <div :style="headerStyle">
      <div style="display:flex;align-items:center;gap:12px">
        <div class="olo-bk-svc-icon" :style="{ width:'48px',height:'48px',borderRadius:'10px',background:s.primary_color,display:'flex',alignItems:'center',justifyContent:'center',color: TOKENS.onPrimary,flexShrink:0 }" v-html="calendarSvg"></div>
        <div>
          <div :style="{ fontSize: s.title_size+'px', fontWeight: s.title_weight, color: s.title_color || TOKENS.text }">
            {{ serviceLabel }}
          </div>
          <div :style="{ fontSize:'13px', color: resolveColor(s.meta_color, TOKENS.textSoft), display:'flex', alignItems:'center', gap:'10px', marginTop:'2px' }">
            <span v-if="s.show_duration" style="display:inline-flex;align-items:center;gap:4px"><span class="olo-bk-meta-icon" v-html="clockSvg"></span>{{ t('60 min') }}</span>
            <span v-if="s.show_price" :style="{ fontWeight:'600', color: s.primary_color }">{{ t('&euro; 80,00') }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Calendar preview -->
    <div style="padding:16px 20px">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px">
        <button type="button" class="olo-bk-nav" :style="navBtnStyle">{{ t('&lsaquo;') }}</button>
        <div style="font-size:15px;font-weight:700;text-transform:capitalize">{{ t('Febbraio 2026') }}</div>
        <button type="button" class="olo-bk-nav" :style="navBtnStyle">{{ t('&rsaquo;') }}</button>
      </div>
      <div style="display:grid;grid-template-columns:repeat(7,1fr);text-align:center;gap:2px;margin-bottom:4px">
        <span v-for="(d, di) in ['L','M','M','G','V','S','D']" :key="di" :style="{ fontSize:'10px', fontWeight:'600', color: TOKENS.textFaint, textTransform:'uppercase', padding:'3px 0' }">{{ d }}</span>
      </div>
      <div style="display:grid;grid-template-columns:repeat(7,1fr);gap:3px">
        <button v-for="(day, i) in calendarDays" :key="i" type="button"
             class="olo-bk-day" :disabled="day.other || day.past || day.closed"
             :style="dayStyle(day)">
          {{ day.num || '' }}
        </button>
      </div>
    </div>

    <!-- Slots preview -->
    <div style="padding:0 20px 16px">
      <div style="font-size:13px;font-weight:600;margin-bottom:8px">{{ t('Slot disponibili:') }}</div>
      <div style="display:flex;gap:6px;flex-wrap:wrap">
        <button v-for="slot in sampleSlots" :key="slot.time" type="button"
             class="olo-bk-slot" :disabled="!slot.available"
             :style="slotStyle(slot)">
          {{ slot.time }}
        </button>
      </div>
    </div>

    <!-- CTA -->
    <div style="padding:0 20px 20px">
      <button type="button" class="olo-bk-cta" :style="{ ...ctaStyle, width:'100%', border:'none' }">{{ t('Conferma prenotazione') }}</button>
    </div>
  </div>
</template>

<script setup>
import { t } from '@/i18n';
import { computed } from 'vue';
import { getShadowValue } from '@/composables/useShadowMap';
import iconsSvg from '../ProSlider/uikitIconsSvg.js';
import { resolveColor, TOKENS } from '@/composables/oloTileDefaults';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });

const calendarSvg = iconsSvg['calendar'] || '';
const clockSvg = iconsSvg['clock'] || '';

const defaults = {
  service_id: 'auto', primary_color: 'var(--olo-color-primary, #e1474f)', show_price: true, show_duration: true,
  widget_max_width: 480, widget_bg: '#FFFFFF', widget_border_radius: 12,
  widget_border_color: '', widget_shadow: 'sm', btn_bg: 'var(--olo-color-primary, #e1474f)', btn_color: '#FFFFFF',
  btn_radius: 8, available_color: 'var(--olo-color-primary, #e1474f)', full_color: '', slot_border_radius: 8,
  title_size: 18, title_weight: '700', title_color: '', meta_color: '', success_color: '',
};

const s = computed(() => ({ ...defaults, ...props.settings }));

const serviceLabel = computed(() => s.value.service_id === 'auto' ? 'Servizio corrente' : s.value.service_id === 'all' ? 'Seleziona servizio' : 'Consulenza Fiscale');

const borderColor = computed(() => resolveColor(s.value.widget_border_color, TOKENS.border));
const fullColor = computed(() => resolveColor(s.value.full_color, TOKENS.error.fg));

const widgetStyle = computed(() => ({
  maxWidth: s.value.widget_max_width + 'px',
  background: s.value.widget_bg,
  borderRadius: s.value.widget_border_radius + 'px',
  border: '1px solid ' + borderColor.value,
  boxShadow: getShadowValue(s.value, 'widget_shadow'),
  overflow: 'hidden',
}));

const headerStyle = computed(() => ({
  padding: '16px 20px',
  borderBottom: '1px solid ' + borderColor.value,
}));

const navBtnStyle = computed(() => ({
  width: '30px', height: '30px', borderRadius: '6px', border: '1px solid ' + borderColor.value,
  background: 'transparent',
  display: 'flex', alignItems: 'center', justifyContent: 'center', cursor: 'pointer',
  fontSize: '16px', color: TOKENS.textSoft,
}));

const calendarDays = computed(() => {
  const days = [];
  // Padding (Feb 2026 starts on Sunday, so 6 padding days for Monday-start grid)
  for (let i = 0; i < 6; i++) days.push({ num: 23 + i, other: true });
  for (let d = 1; d <= 28; d++) {
    const isWeekend = (d + 6) % 7 === 6 || (d + 6) % 7 === 0;
    days.push({
      num: d, today: d === 19,
      available: !isWeekend && d >= 19,
      full: false,
      closed: isWeekend,
      past: d < 19,
      selected: d === 20,
    });
  }
  return days;
});

function dayStyle(day) {
  const base = {
    aspectRatio: '1', display: 'flex', alignItems: 'center', justifyContent: 'center',
    borderRadius: '6px', fontSize: '12px', fontWeight: '500', transition: 'all 0.15s',
    background: 'transparent', border: 'none', cursor: 'pointer', fontFamily: 'inherit',
    color: TOKENS.text,
  };
  if (day.other) return { ...base, color: TOKENS.textFaint, cursor: 'default' };
  // slot/giorno selezionato → primary + onPrimary
  if (day.selected) return { ...base, background: s.value.available_color, color: TOKENS.onPrimary, fontWeight: '700' };
  if (day.today) return { ...base, boxShadow: `inset 0 0 0 2px ${s.value.available_color}`, fontWeight: '700' };
  // disponibile → tinta soft del primary
  if (day.available) return { ...base, background: `color-mix(in srgb, ${s.value.available_color} 12%, transparent)`, color: s.value.available_color, fontWeight: '600' };
  if (day.closed) return { ...base, color: TOKENS.textFaint, cursor: 'default' };
  if (day.past) return { ...base, color: TOKENS.textFaint, opacity: '0.5', cursor: 'default' };
  return base;
}

const sampleSlots = [
  { time: '09:00', available: false }, { time: '10:15', available: true },
  { time: '11:30', available: true }, { time: '14:00', available: true },
  { time: '15:15', available: true }, { time: '16:30', available: true },
];

function slotStyle(slot) {
  // disponibile → bordo neutro + testo brand; occupato → tinta/testo error barrato
  return {
    padding: '6px 12px', borderRadius: s.value.slot_border_radius + 'px',
    border: '1px solid ' + (slot.available ? borderColor.value : `color-mix(in srgb, ${fullColor.value} 30%, transparent)`),
    background: 'transparent', cursor: slot.available ? 'pointer' : 'not-allowed',
    fontFamily: 'inherit', fontSize: '13px', fontWeight: '600',
    color: slot.available ? s.value.available_color : fullColor.value,
    textDecoration: slot.available ? 'none' : 'line-through',
    opacity: slot.available ? '1' : '0.6',
  };
}

const ctaStyle = computed(() => ({
  padding: '12px 24px', borderRadius: s.value.btn_radius + 'px',
  background: s.value.btn_bg, color: s.value.btn_color,
  textAlign: 'center', fontWeight: '600', fontSize: '14px', cursor: 'pointer',
}));
</script>

<style scoped>
.olo-bk-svc-icon :deep(svg) { width: 22px; height: 22px; stroke: currentColor; fill: none; }
.olo-bk-meta-icon { display: inline-flex; }
.olo-bk-meta-icon :deep(svg) { width: 13px; height: 13px; stroke: currentColor; fill: none; }
.olo-bk-day:disabled, .olo-bk-slot:disabled { pointer-events: none; }
.olo-bk-day:focus-visible,
.olo-bk-slot:focus-visible,
.olo-bk-nav:focus-visible,
.olo-bk-cta:focus-visible {
  outline: none;
  box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent);
}
</style>
