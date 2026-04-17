<template>
  <div class="mb-font-sans">
    <div :style="cardWrapStyle">
      <div v-if="s.show_header" :style="headerStyle">
        <div :style="{ fontSize:'18px', fontWeight:'700' }">Prenota un Tavolo</div>
        <div :style="{ fontSize:'13px', opacity:0.85, marginTop:'2px' }">Compila il modulo per riservare</div>
      </div>
      <div :style="formBodyStyle">
        <!-- Step badge -->
        <div :style="{ display:'flex', alignItems:'center', gap:'8px', marginBottom:'16px' }">
          <span :style="stepBadgeStyle">1</span>
          <span :style="{ fontSize:'14px', fontWeight:'600', color:'#374151' }">Dettagli prenotazione</span>
        </div>
        <!-- Date / People / Service row -->
        <div :style="{ display:'flex', gap:'12px', marginBottom:'16px', flexWrap:'wrap' }">
          <div :style="{ flex:'1', minWidth:'120px' }">
            <label :style="labelStyle">Data</label>
            <div :style="inputStyle">{{ today }}</div>
          </div>
          <div :style="{ flex:'1', minWidth:'100px' }">
            <label :style="labelStyle">Persone</label>
            <div :style="inputStyle">2</div>
          </div>
          <div :style="{ flex:'1', minWidth:'120px' }">
            <label :style="labelStyle">Servizio</label>
            <div :style="inputStyle">Cena</div>
          </div>
        </div>
        <!-- Step badge 2 -->
        <div :style="{ display:'flex', alignItems:'center', gap:'8px', marginBottom:'16px' }">
          <span :style="stepBadgeStyle">2</span>
          <span :style="{ fontSize:'14px', fontWeight:'600', color:'#374151' }">I tuoi dati</span>
        </div>
        <!-- Name / Email row -->
        <div :style="{ display:'flex', gap:'12px', marginBottom:'20px', flexWrap:'wrap' }">
          <div :style="{ flex:'1', minWidth:'140px' }">
            <label :style="labelStyle">Nome</label>
            <div :style="inputStyle">Mario Rossi</div>
          </div>
          <div :style="{ flex:'1', minWidth:'140px' }">
            <label :style="labelStyle">Email</label>
            <div :style="inputStyle">mario@email.com</div>
          </div>
        </div>
        <!-- Submit button -->
        <div :style="{ textAlign: s.btn_full_width ? 'stretch' : 'right' }">
          <span :style="submitBtnStyle">{{ s.btn_text }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const defaults = { show_header: true, header_bg: '#1F2937', header_text_color: '#fff', card_border_radius: 12, card_shadow: 'sm', accent_color: '', btn_text: 'Conferma Prenotazione', btn_bg: '', btn_color: '#fff', btn_radius: 8, btn_full_width: true, max_width: '', step_badge_color: '', label_color: '#6B7280', input_radius: 8 };
const s = computed(() => ({ ...defaults, ...props.settings }));
const accent = computed(() => s.value.accent_color || 'var(--olo-color-primary, #6366F1)');
const shadowMap = { none:'none', sm:'0 1px 3px rgba(0,0,0,0.1)', md:'0 4px 12px rgba(0,0,0,0.1)', lg:'0 8px 24px rgba(0,0,0,0.15)' };
const today = new Date().toLocaleDateString('it-IT', { day:'2-digit', month:'short', year:'numeric' });
const cardWrapStyle = computed(() => ({
  background:'#fff', borderRadius: s.value.card_border_radius+'px', border:'1px solid #E5E7EB',
  boxShadow: shadowMap[s.value.card_shadow]||shadowMap.sm, overflow:'hidden',
  maxWidth: s.value.max_width ? s.value.max_width+'px' : 'none',
}));
const headerStyle = computed(() => ({
  background: s.value.header_bg, color: s.value.header_text_color, padding:'16px 20px',
}));
const formBodyStyle = { padding:'20px' };
const stepBadgeStyle = computed(() => ({
  display:'inline-flex', alignItems:'center', justifyContent:'center', width:'24px', height:'24px',
  borderRadius:'50%', background: s.value.step_badge_color || accent.value, color:'#fff',
  fontSize:'12px', fontWeight:'700',
}));
const labelStyle = computed(() => ({
  display:'block', fontSize:'12px', fontWeight:'600', color: s.value.label_color, marginBottom:'4px',
}));
const inputStyle = computed(() => ({
  padding:'8px 12px', borderRadius: s.value.input_radius+'px', border:'1px solid #D1D5DB',
  fontSize:'13px', color:'#374151', background:'#F9FAFB',
}));
const submitBtnStyle = computed(() => ({
  display: s.value.btn_full_width ? 'block' : 'inline-block',
  width: s.value.btn_full_width ? '100%' : 'auto',
  padding:'12px 24px', background: s.value.btn_bg || accent.value, color: s.value.btn_color,
  borderRadius: s.value.btn_radius+'px', fontSize:'14px', fontWeight:'700', cursor:'pointer',
  textAlign:'center', boxSizing:'border-box',
}));
</script>
