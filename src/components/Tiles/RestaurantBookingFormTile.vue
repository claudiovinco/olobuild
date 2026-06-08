<template>
  <div class="mb-font-sans">
    <div :style="cardWrapStyle">
      <div v-if="s.show_header" :style="headerStyle">
        <div :style="{ fontSize:'18px', fontWeight:'700' }">{{ t('Prenota un Tavolo') }}</div>
        <div :style="{ fontSize:'13px', opacity:0.85, marginTop:'2px' }">{{ t('Compila il modulo per riservare') }}</div>
      </div>
      <div :style="formBodyStyle">
        <!-- Step badge -->
        <div :style="{ display:'flex', alignItems:'center', gap:'8px', marginBottom:'16px' }">
          <span :style="stepBadgeStyle">1</span>
          <span :style="{ fontSize:'14px', fontWeight:'600', color: TOKENS.text }">{{ t('Dettagli prenotazione') }}</span>
        </div>
        <!-- Date / People / Service row -->
        <div :style="{ display:'flex', gap:'12px', marginBottom:'16px', flexWrap:'wrap' }">
          <div :style="{ flex:'1', minWidth:'120px' }">
            <label :style="labelStyle">{{ t('Data') }}</label>
            <div class="olo-rbf-input" tabindex="0" :style="inputStyle">{{ today }}</div>
          </div>
          <div :style="{ flex:'1', minWidth:'100px' }">
            <label :style="labelStyle">{{ t('Persone') }}</label>
            <div class="olo-rbf-input" tabindex="0" :style="inputStyle">2</div>
          </div>
          <div :style="{ flex:'1', minWidth:'120px' }">
            <label :style="labelStyle">{{ t('Servizio') }}</label>
            <div class="olo-rbf-input" tabindex="0" :style="inputStyle">{{ t('Cena') }}</div>
          </div>
        </div>
        <!-- Step badge 2 -->
        <div :style="{ display:'flex', alignItems:'center', gap:'8px', marginBottom:'16px' }">
          <span :style="stepBadgeStyle">2</span>
          <span :style="{ fontSize:'14px', fontWeight:'600', color: TOKENS.text }">{{ t('I tuoi dati') }}</span>
        </div>
        <!-- Name / Email row -->
        <div :style="{ display:'flex', gap:'12px', marginBottom:'20px', flexWrap:'wrap' }">
          <div :style="{ flex:'1', minWidth:'140px' }">
            <label :style="labelStyle">{{ t('Nome') }}</label>
            <div class="olo-rbf-input" tabindex="0" :style="inputStyle">{{ t('Mario Rossi') }}</div>
          </div>
          <div :style="{ flex:'1', minWidth:'140px' }">
            <label :style="labelStyle">{{ t('Email') }}</label>
            <div class="olo-rbf-input" tabindex="0" :style="inputStyle">{{ t('mario@email.com') }}</div>
          </div>
        </div>
        <!-- Submit button -->
        <div :style="{ textAlign: s.btn_full_width ? 'stretch' : 'right' }">
          <button type="button" class="olo-rbf-submit" :style="submitBtnStyle">{{ s.btn_text }}</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';
import { resolveColor, TOKENS, SHADOW } from '@/composables/oloTileDefaults';
const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const defaults = { show_header: true, header_bg: '', header_text_color: '', card_border_radius: 12, card_shadow: 'sm', accent_color: '', btn_text: 'Conferma Prenotazione', btn_bg: '', btn_color: '', btn_radius: 8, btn_full_width: true, max_width: '', step_badge_color: '', label_color: '', input_radius: 8 };
const s = computed(() => ({ ...defaults, ...props.settings }));
const accent = computed(() => resolveColor(s.value.accent_color, TOKENS.primary));
const shadowMap = SHADOW;
const today = new Date().toLocaleDateString('it-IT', { day:'2-digit', month:'short', year:'numeric' });
const cardWrapStyle = computed(() => ({
  background: TOKENS.surface, borderRadius: s.value.card_border_radius+'px', border:'1px solid '+TOKENS.border,
  boxShadow: shadowMap[s.value.card_shadow]||shadowMap.sm, overflow:'hidden',
  maxWidth: s.value.max_width ? s.value.max_width+'px' : 'none',
}));
const headerStyle = computed(() => ({
  background: resolveColor(s.value.header_bg, TOKENS.dark), color: resolveColor(s.value.header_text_color, TOKENS.onPrimary), padding:'16px 20px',
}));
const formBodyStyle = { padding:'20px' };
const stepBadgeStyle = computed(() => ({
  display:'inline-flex', alignItems:'center', justifyContent:'center', width:'24px', height:'24px',
  borderRadius:'50%', background: resolveColor(s.value.step_badge_color, accent.value), color: TOKENS.onPrimary,
  fontSize:'12px', fontWeight:'700',
}));
const labelStyle = computed(() => ({
  display:'block', fontSize:'12px', fontWeight:'600', color: resolveColor(s.value.label_color, TOKENS.textSoft), marginBottom:'4px',
}));
const inputStyle = computed(() => ({
  padding:'8px 12px', borderRadius: s.value.input_radius+'px', border:'1px solid '+TOKENS.border,
  fontSize:'13px', color: TOKENS.text, background: TOKENS.surfaceAlt, cursor:'text',
}));
const submitBtnStyle = computed(() => ({
  display: s.value.btn_full_width ? 'block' : 'inline-block',
  width: s.value.btn_full_width ? '100%' : 'auto',
  padding:'12px 24px', background: resolveColor(s.value.btn_bg, accent.value), color: resolveColor(s.value.btn_color, TOKENS.onPrimary),
  border:'none', fontFamily:'inherit',
  borderRadius: s.value.btn_radius+'px', fontSize:'14px', fontWeight:'700', cursor:'pointer',
  textAlign:'center', boxSizing:'border-box',
}));
</script>

<style scoped>
/* a11y: bordo input attivo → primary + anello focus (form/picker) */
.olo-rbf-input:focus-visible {
  outline: none;
  border-color: var(--olo-color-primary, #e1474f) !important;
  box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent);
}
.olo-rbf-submit:focus-visible {
  outline: none;
  box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent);
}
</style>
