<template>
  <div :style="cardStyle">
    <h3 :style="{ margin:'0 0 18px', fontSize: (s.title_size||20)+'px', fontWeight:700, color: s.title_color||'#0F172A', lineHeight:'1.35' }">{{ s.title_text || 'Vuoi avere più informazioni riguardo a questo immobile?' }}</h3>
    <div v-if="s.show_name !== false" style="margin-bottom:12px"><input :style="inputStyle" :placeholder="t('Nome')" disabled /></div>
    <div v-if="s.show_phone !== false" style="margin-bottom:12px"><input :style="inputStyle" :placeholder="t('Telefono')" disabled /></div>
    <div v-if="s.show_email !== false" style="margin-bottom:12px"><input :style="inputStyle" :placeholder="t('Email')" disabled /></div>
    <div v-if="s.show_message !== false" style="margin-bottom:12px">
      <textarea :style="Object.assign({}, inputStyle, { minHeight:'80px', resize:'vertical' })" disabled>Buongiorno, sono interessato/a [TRILOCALE CENTRO STORICO]</textarea>
    </div>
    <label v-if="s.show_consent !== false" style="display:flex;align-items:flex-start;gap:8px;margin:14px 0 18px;fontSize:13px;color:#64748B;lineHeight:'1.5'">
      <input type="checkbox" disabled style="margin-top:3px" />
      <span>{{ s.consent_text || 'Inviando questo modulo acconsento' }}<br><span :style="{color: s.btn_bg || '#F59E0B', fontWeight:600}">{{ s.consent_link_text || 'Termini di utilizzo' }}</span></span>
    </label>
    <div style="display:flex;gap:10px;flex-wrap:wrap">
      <button :style="submitStyle">{{ s.btn_text || 'Invia messaggio' }}</button>
      <button v-if="s.show_phone_btn !== false" :style="phoneStyle">{{ s.phone_btn_text || 'chiama' }}</button>
    </div>
    <div v-if="s.show_wa_btn !== false" style="margin-top:10px">
      <button :style="waStyle">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 2C6.477 2 2 6.477 2 12c0 1.89.525 3.66 1.438 5.168L2 22l4.832-1.438A9.955 9.955 0 0 0 12 22c5.523 0 10-4.477 10-10S17.523 2 12 2z"/></svg>
        {{ s.wa_btn_text || 'WhatsApp' }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';
const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const s = computed(() => ({ ...props.settings }));

const cardStyle = computed(() => ({
  background: s.value.form_bg || '#FFFFFF',
  border: '1px solid ' + (s.value.form_border || '#E5E7EB'),
  borderRadius: (s.value.form_radius || 16) + 'px',
  padding: (s.value.form_padding || 28) + 'px',
}));

const inputStyle = computed(() => ({
  width: '100%', padding: '13px 16px',
  border: '1px solid ' + (s.value.input_border || '#D1D5DB'),
  borderRadius: (s.value.input_radius || 10) + 'px',
  fontSize: (s.value.input_size || 15) + 'px',
  fontFamily: 'inherit', boxSizing: 'border-box', color: '#1E293B', background: '#fff',
}));

const submitStyle = computed(() => ({
  flex: 1, display: 'flex', alignItems: 'center', justifyContent: 'center',
  padding: '13px 24px', border: 'none', cursor: 'default',
  borderRadius: (s.value.btn_radius || 10) + 'px',
  fontSize: '15px', fontWeight: 600,
  background: s.value.btn_bg || '#F59E0B', color: s.value.btn_color || '#FFFFFF',
}));

const phoneStyle = computed(() => ({
  flex: 1, display: 'flex', alignItems: 'center', justifyContent: 'center',
  padding: '13px 24px', cursor: 'default',
  borderRadius: (s.value.btn_radius || 10) + 'px',
  fontSize: '15px', fontWeight: 600,
  background: s.value.phone_btn_bg || '#FFFFFF',
  color: s.value.phone_btn_color || '#374151',
  border: '1px solid ' + (s.value.phone_btn_border || '#D1D5DB'),
}));

const waStyle = computed(() => ({
  width: '100%', display: 'flex', alignItems: 'center', justifyContent: 'center', gap: '8px',
  padding: '13px 24px', cursor: 'default',
  borderRadius: (s.value.btn_radius || 10) + 'px',
  fontSize: '15px', fontWeight: 600,
  background: s.value.wa_bg || '#FFFFFF',
  color: s.value.wa_color || '#374151',
  border: '1px solid ' + (s.value.wa_border || '#D1D5DB'),
}));
</script>
