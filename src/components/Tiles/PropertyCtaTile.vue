<template>
  <div class="mb-font-sans" :style="cardStyle">
    <div :style="{ display:'flex', alignItems:'center', gap:'16px', marginBottom:'16px' }">
      <div :style="avatarStyle"></div>
      <div>
        <div :style="{ fontSize:'16px', fontWeight:'700', color:'#1F2937' }">Mario Rossi</div>
        <div :style="{ fontSize:'13px', color:'#6B7280' }">Agente Immobiliare</div>
      </div>
    </div>
    <div :style="{ display:'flex', flexDirection:'column', gap:'10px' }">
      <div :style="infoRow">
        <span :style="{ fontSize:'16px' }">&#128222;</span>
        <span>+39 0461 123 456</span>
      </div>
      <div :style="infoRow">
        <span :style="{ fontSize:'16px' }">&#9993;</span>
        <span>mario.rossi@agenzia.it</span>
      </div>
    </div>
    <div :style="whatsappStyle">
      <span>&#128172; Contatta su WhatsApp</span>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const defaults = { max_width: 380, border_radius: 12, shadow: 'md', padding: 24, accent_color: '' };
const s = computed(() => ({ ...defaults, ...props.settings }));
const accent = computed(() => s.value.accent_color || 'var(--olo-color-primary, #6366F1)');
const shadowMap = { none:'none', sm:'0 1px 3px rgba(0,0,0,0.1)', md:'0 4px 12px rgba(0,0,0,0.1)', lg:'0 8px 24px rgba(0,0,0,0.15)' };
const cardStyle = computed(() => ({ maxWidth: s.value.max_width+'px', background:'#fff', borderRadius: s.value.border_radius+'px', boxShadow: shadowMap[s.value.shadow]||shadowMap.md, padding: s.value.padding+'px', border:'1px solid #E5E7EB' }));
const avatarStyle = computed(() => ({ width:'56px', height:'56px', borderRadius:'50%', background:'linear-gradient(135deg,#6366F140,#6366F190)', flexShrink:0 }));
const infoRow = { display:'flex', alignItems:'center', gap:'10px', fontSize:'14px', color:'#374151' };
const whatsappStyle = computed(() => ({ marginTop:'16px', padding:'12px', background:'#25D366', color:'#fff', borderRadius:'8px', textAlign:'center', fontWeight:'600', fontSize:'14px', cursor:'pointer' }));
</script>
