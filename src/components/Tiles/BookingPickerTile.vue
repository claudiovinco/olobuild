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

    <!-- Calendar mini -->
    <div style="padding:16px 20px">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px">
        <div :style="navBtnStyle">{{ t('&lsaquo;') }}</div>
        <div style="font-size:15px;font-weight:700;text-transform:capitalize">{{ t('Febbraio 2026') }}</div>
        <div :style="navBtnStyle">{{ t('&rsaquo;') }}</div>
      </div>
      <div style="display:grid;grid-template-columns:repeat(7,1fr);text-align:center;gap:4px">
        <span v-for="d in ['L','M','M','G','V','S','D']" :key="d" style="font-size:10px;font-weight:600;color:#9ca3af;text-transform:uppercase;padding:3px 0">{{ d }}</span>
        <div v-for="i in 14" :key="i"
             :style="{ aspectRatio:'1', display:'flex', alignItems:'center', justifyContent:'center', borderRadius:'6px', fontSize:'11px', fontWeight:'500', background: i % 3 === 0 ? s.available_color+'14' : 'transparent', color: i % 3 === 0 ? s.available_color : 'var(--olo-color-text-muted, #9CA3AF)' }">
          {{ i }}
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

const props = defineProps({ settings: { type: Object, default: () => ({}) } });

const defaults = {
  service_id: '', primary_color: 'var(--olo-color-primary, #6366F1)', show_price: true, show_duration: true,
  widget_max_width: 480, widget_bg: '#FFFFFF', widget_border_radius: 12,
  widget_border_color: '#E5E7EB', widget_shadow: 'sm', btn_bg: 'var(--olo-color-primary, #6366F1)', btn_color: '#FFFFFF',
  btn_radius: 8, available_color: 'var(--olo-color-primary, #6366F1)', full_color: '#EF4444', slot_border_radius: 8,
  title_size: 18, title_weight: '700', title_color: '', meta_color: '#6B7280', success_color: '#10B981',
};

const s = computed(() => ({ ...defaults, ...props.settings }));

const serviceLabel = computed(() => {
  if (!s.value.service_id) return 'Seleziona un servizio...';
  const list = window.oloData?.serviceList || [];
  const found = list.find(x => String(x.value) === String(s.value.service_id));
  return found ? found.label : 'Servizio #' + s.value.service_id;
});

const shadowMap = { none: 'none', sm: '0 1px 3px rgba(0,0,0,0.1)', md: '0 4px 12px rgba(0,0,0,0.1)', lg: '0 8px 24px rgba(0,0,0,0.15)' };

const widgetStyle = computed(() => ({
  maxWidth: s.value.widget_max_width + 'px',
  background: s.value.widget_bg,
  borderRadius: s.value.widget_border_radius + 'px',
  border: '1px solid ' + s.value.widget_border_color,
  boxShadow: shadowMap[s.value.widget_shadow] || 'none',
  overflow: 'hidden',
}));

const headerStyle = computed(() => ({
  padding: '16px 20px',
  borderBottom: '1px solid ' + s.value.widget_border_color,
}));

const navBtnStyle = computed(() => ({
  width: '30px', height: '30px', borderRadius: '6px', border: '1px solid #e5e7eb',
  display: 'flex', alignItems: 'center', justifyContent: 'center',
  fontSize: '16px', color: 'var(--olo-color-text-muted, #9CA3AF)',
}));

const ctaStyle = computed(() => ({
  padding: '12px 24px', borderRadius: s.value.btn_radius + 'px',
  background: s.value.btn_bg, color: s.value.btn_color,
  textAlign: 'center', fontWeight: '600', fontSize: '14px',
}));
</script>
