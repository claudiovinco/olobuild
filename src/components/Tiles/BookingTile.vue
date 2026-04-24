<template>
  <div class="mb-font-sans" :style="widgetStyle">
    <!-- Service header -->
    <div :style="headerStyle">
      <div style="display:flex;align-items:center;gap:12px">
        <div :style="{ width:'48px',height:'48px',borderRadius:'10px',background:s.primary_color,display:'flex',alignItems:'center',justifyContent:'center',color:'#fff',fontSize:'20px',flexShrink:0 }">
          &#128197;
        </div>
        <div>
          <div :style="{ fontSize: s.title_size+'px', fontWeight: s.title_weight, color: s.title_color || 'var(--olo-color-text, #374151)' }">
            {{ serviceLabel }}
          </div>
          <div :style="{ fontSize:'13px', color: s.meta_color, display:'flex', gap:'10px', marginTop:'2px' }">
            <span v-if="s.show_duration">{{ t('&#9201; 60 min') }}</span>
            <span v-if="s.show_price" :style="{ fontWeight:'600', color: s.primary_color }">{{ t('&euro; 80,00') }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Calendar preview -->
    <div style="padding:16px 20px">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px">
        <div :style="navBtnStyle">{{ t('&lsaquo;') }}</div>
        <div style="font-size:15px;font-weight:700;text-transform:capitalize">{{ t('Febbraio 2026') }}</div>
        <div :style="navBtnStyle">{{ t('&rsaquo;') }}</div>
      </div>
      <div style="display:grid;grid-template-columns:repeat(7,1fr);text-align:center;gap:2px;margin-bottom:4px">
        <span v-for="d in ['L','M','M','G','V','S','D']" :key="d" style="font-size:10px;font-weight:600;color:#9ca3af;text-transform:uppercase;padding:3px 0">{{ d }}</span>
      </div>
      <div style="display:grid;grid-template-columns:repeat(7,1fr);gap:3px">
        <div v-for="(day, i) in calendarDays" :key="i"
             :style="dayStyle(day)">
          {{ day.num || '' }}
        </div>
      </div>
    </div>

    <!-- Slots preview -->
    <div style="padding:0 20px 16px">
      <div style="font-size:13px;font-weight:600;margin-bottom:8px">{{ t('Slot disponibili:') }}</div>
      <div style="display:flex;gap:6px;flex-wrap:wrap">
        <div v-for="slot in sampleSlots" :key="slot.time"
             :style="slotStyle(slot)">
          {{ slot.time }}
        </div>
      </div>
    </div>

    <!-- CTA -->
    <div style="padding:0 20px 20px">
      <div :style="ctaStyle">{{ t('Conferma prenotazione') }}</div>
    </div>
  </div>
</template>

<script setup>
import { t } from '@/i18n';
import { computed } from 'vue';
import { getShadowValue } from '@/composables/useShadowMap';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });

const defaults = {
  service_id: 'auto', primary_color: 'var(--olo-color-primary, #6366F1)', show_price: true, show_duration: true,
  widget_max_width: 480, widget_bg: '#FFFFFF', widget_border_radius: 12,
  widget_border_color: '#E5E7EB', widget_shadow: 'sm', btn_bg: 'var(--olo-color-primary, #6366F1)', btn_color: '#FFFFFF',
  btn_radius: 8, available_color: 'var(--olo-color-primary, #6366F1)', full_color: '#EF4444', slot_border_radius: 8,
  title_size: 18, title_weight: '700', title_color: '', meta_color: '#6B7280', success_color: '#10B981',
};

const s = computed(() => ({ ...defaults, ...props.settings }));

const serviceLabel = computed(() => s.value.service_id === 'auto' ? 'Servizio corrente' : s.value.service_id === 'all' ? 'Seleziona servizio' : 'Consulenza Fiscale');

const widgetStyle = computed(() => ({
  maxWidth: s.value.widget_max_width + 'px',
  background: s.value.widget_bg,
  borderRadius: s.value.widget_border_radius + 'px',
  border: '1px solid ' + s.value.widget_border_color,
  boxShadow: getShadowValue(s.value, 'widget_shadow'),
  overflow: 'hidden',
}));

const headerStyle = computed(() => ({
  padding: '16px 20px',
  borderBottom: '1px solid ' + s.value.widget_border_color,
}));

const navBtnStyle = computed(() => ({
  width: '30px', height: '30px', borderRadius: '6px', border: '1px solid #e5e7eb',
  display: 'flex', alignItems: 'center', justifyContent: 'center', cursor: 'pointer',
  fontSize: '16px', color: '#6b7280',
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
  };
  if (day.other) return { ...base, color: '#d1d5db' };
  if (day.selected) return { ...base, background: s.value.available_color, color: '#fff', fontWeight: '700' };
  if (day.today) return { ...base, boxShadow: `inset 0 0 0 2px ${s.value.available_color}`, fontWeight: '700' };
  if (day.available) return { ...base, background: s.value.available_color + '14', color: s.value.available_color, fontWeight: '600' };
  if (day.closed) return { ...base, color: '#d1d5db' };
  if (day.past) return { ...base, opacity: '0.4' };
  return base;
}

const sampleSlots = [
  { time: '09:00', available: false }, { time: '10:15', available: true },
  { time: '11:30', available: true }, { time: '14:00', available: true },
  { time: '15:15', available: true }, { time: '16:30', available: true },
];

function slotStyle(slot) {
  return {
    padding: '6px 12px', borderRadius: s.value.slot_border_radius + 'px',
    border: '1px solid ' + (slot.available ? '#e5e7eb' : '#f3f4f6'),
    fontSize: '13px', fontWeight: '600',
    color: slot.available ? '#374151' : '#d1d5db',
    textDecoration: slot.available ? 'none' : 'line-through',
    opacity: slot.available ? '1' : '0.4',
  };
}

const ctaStyle = computed(() => ({
  padding: '12px 24px', borderRadius: s.value.btn_radius + 'px',
  background: s.value.btn_bg, color: s.value.btn_color,
  textAlign: 'center', fontWeight: '600', fontSize: '14px', cursor: 'pointer',
}));
</script>
