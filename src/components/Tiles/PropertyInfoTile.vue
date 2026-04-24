<template>
  <div :style="cardStyle">
    <!-- Header -->
    <div v-if="s.show_header !== false" style="display:flex;justify-content:space-between;align-items:flex-start;gap:16px;margin-bottom:16px;flex-wrap:wrap">
      <div>
        <div style="font-size:11px;color:#94A3B8;font-family:monospace;letter-spacing:1px;margin-bottom:3px">{{ t('RIF. CEDA-2024-001') }}</div>
        <h3 :style="{ fontSize:(s.title_size||16)+'px', fontWeight:700, color:s.title_color||'#0F172A', margin:'0 0 6px', lineHeight:'1.3' }">{{ t('Trilocale Centro Storico') }}</h3>
        <div style="display:flex;gap:6px">
          <span :style="{ padding:'3px 10px', borderRadius:'6px', fontSize:'11px', fontWeight:700, textTransform:'uppercase', background:(s.accent_color||'#2563EB')+'14', color:s.accent_color||'#2563EB' }">{{ t('Vendita') }}</span>
          <span style="padding:3px 10px;border-radius:6px;font-size:11px;font-weight:700;background:#F1F5F9;color:#475569">{{ t('Appartamento') }}</span>
        </div>
      </div>
      <div style="text-align:right;flex-shrink:0">
        <div :style="{ fontSize:'24px', fontWeight:800, color:s.accent_color||'#2563EB', letterSpacing:'-0.02em' }">{{ t('&euro; 325.000') }}</div>
        <div style="font-size:12px;color:#94A3B8;margin-top:2px">{{ t('&euro; 3.421/m&sup2;') }}</div>
      </div>
    </div>

    <!-- Tabs -->
    <div v-if="s.layout !== 'flat' && s.layout !== 'accordion'" style="display:flex;gap:2px;border-bottom:2px solid #F1F5F9;margin-bottom:16px;overflow-x:auto">
      <button v-for="(tab, i) in visibleTabs" :key="tab.id"
        :style="{
          padding:'9px 16px', fontSize:'12px', fontWeight:600, border:'none', cursor:'pointer', background:'none', fontFamily:'inherit',
          color: activeTab === tab.id ? (s.accent_color||'#2563EB') : '#94A3B8',
          borderBottom: '2px solid ' + (activeTab === tab.id ? (s.accent_color||'#2563EB') : 'transparent'),
          marginBottom: '-2px',
        }"
        @click="activeTab = tab.id"
      >{{ tab.label }}</button>
    </div>

    <!-- Details -->
    <div v-show="showPanel('details')">
      <div :style="gridStyle">
        <div v-for="item in detailItems" :key="item[0]" :style="rowStyle">
          <span :style="{ flex:1, fontSize:'13px', color:s.label_color||'#64748B' }">{{ item[0] }}</span>
          <span :style="{ fontSize:'14px', fontWeight:600, color:s.value_color||'#1E293B' }">{{ item[1] }}</span>
        </div>
      </div>
    </div>

    <!-- Structure -->
    <div v-show="showPanel('structure')">
      <div :style="gridStyle">
        <div v-for="item in structureItems" :key="item[0]" :style="rowStyle">
          <span :style="{ flex:1, fontSize:'13px', color:s.label_color||'#64748B' }">{{ item[0] }}</span>
          <span :style="{ fontSize:'14px', fontWeight:600, color:s.value_color||'#1E293B' }">{{ item[1] }}</span>
        </div>
      </div>
    </div>

    <!-- Energy -->
    <div v-show="showPanel('energy')">
      <div :style="{ height:'24px', borderRadius:'4px', overflow:'hidden', background:'#F1F5F9', marginBottom:'12px' }">
        <div :style="{ width:'76%', height:'100%', borderRadius:'4px', background:'#BFD730', display:'flex', alignItems:'center', justifyContent:'center', fontSize:'12px', fontWeight:800, color:'#fff' }">{{ t('Classe A1') }}</div>
      </div>
      <div :style="gridStyle">
        <div v-for="item in energyItems" :key="item[0]" :style="rowStyle">
          <span :style="{ flex:1, fontSize:'13px', color:s.label_color||'#64748B' }">{{ item[0] }}</span>
          <span :style="{ fontSize:'14px', fontWeight:600, color:s.value_color||'#1E293B' }">{{ item[1] }}</span>
        </div>
      </div>
    </div>

    <!-- Costs -->
    <div v-show="showPanel('costs')">
      <div :style="gridStyle">
        <div v-for="item in costItems" :key="item[0]" :style="rowStyle">
          <span :style="{ flex:1, fontSize:'13px', color:s.label_color||'#64748B' }">{{ item[0] }}</span>
          <span :style="{ fontSize:'14px', fontWeight:600, color:s.value_color||'#1E293B' }">{{ item[1] }}</span>
        </div>
      </div>
    </div>

    <!-- Calculator -->
    <div v-show="showPanel('calculator')" v-if="s.show_calculator">
      <div style="background:#F8FAFC;border-radius:10px;padding:16px">
        <div :style="{ fontSize:'22px', fontWeight:800, color:s.accent_color||'#2563EB', textAlign:'center', padding:'8px 0' }">&euro; 1.142 <span style="font-size:14px;font-weight:400;color:#94A3B8">{{ t('/mese') }}</span></div>
        <div style="font-size:12px;color:#94A3B8;text-align:center">{{ t('Rata stimata per un mutuo di &euro; 260.000 a 25 anni') }}</div>
        <div style="display:flex;gap:10px;margin-top:12px">
          <div style="flex:1"><div style="font-size:10px;font-weight:700;color:#94A3B8;text-transform:uppercase;margin-bottom:3px">{{ t('Importo') }}</div><div style="padding:7px 10px;border:1px solid #D1D5DB;border-radius:6px;font-size:13px;color:#6B7280;background:#fff">{{ t('&euro; 325.000') }}</div></div>
          <div style="flex:1"><div style="font-size:10px;font-weight:700;color:#94A3B8;text-transform:uppercase;margin-bottom:3px">{{ t('Anticipo') }}</div><div style="padding:7px 10px;border:1px solid #D1D5DB;border-radius:6px;font-size:13px;color:#6B7280;background:#fff">{{ t('&euro; 65.000') }}</div></div>
        </div>
        <div style="display:flex;gap:10px;margin-top:8px">
          <div style="flex:1"><div style="font-size:10px;font-weight:700;color:#94A3B8;text-transform:uppercase;margin-bottom:3px">{{ t('Tasso') }}</div><div style="padding:7px 10px;border:1px solid #D1D5DB;border-radius:6px;font-size:13px;color:#6B7280;background:#fff">3.5%</div></div>
          <div style="flex:1"><div style="font-size:10px;font-weight:700;color:#94A3B8;text-transform:uppercase;margin-bottom:3px">{{ t('Anni') }}</div><div style="padding:7px 10px;border:1px solid #D1D5DB;border-radius:6px;font-size:13px;color:#6B7280;background:#fff">25</div></div>
        </div>
      </div>
    </div>

    <!-- Actions -->
    <div v-if="s.show_print !== false || s.show_share !== false" style="display:flex;gap:6px;flex-wrap:wrap;margin-top:16px;padding-top:14px;border-top:1px solid #F1F5F9">
      <span v-if="s.show_print !== false" style="padding:5px 12px;border-radius:8px;font-size:11px;font-weight:600;color:#94A3B8;background:#F1F5F9">{{ t('Stampa scheda') }}</span>
      <span v-if="s.show_share !== false" style="padding:5px 12px;border-radius:8px;font-size:11px;font-weight:600;color:#94A3B8;background:#F1F5F9">{{ t('WhatsApp') }}</span>
      <span v-if="s.show_share !== false" style="padding:5px 12px;border-radius:8px;font-size:11px;font-weight:600;color:#94A3B8;background:#F1F5F9">{{ t('Email') }}</span>
      <span v-if="s.show_share !== false" style="padding:5px 12px;border-radius:8px;font-size:11px;font-weight:600;color:#94A3B8;background:#F1F5F9">{{ t('Copia link') }}</span>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { t } from '@/i18n';
const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const s = computed(() => ({ ...props.settings }));
const activeTab = ref('details');

const detailItems = [['Tipologia','Appartamento'],['Tipo annuncio','Vendita'],['Condizione','Buono'],['Anno costruzione','1985'],['Ristrutturazione','2020'],['Codice rif.','CEDA-2024-001']];
const structureItems = [['Superficie totale','95 m\u00B2'],['Locali','4'],['Camere da letto','2'],['Bagni','1'],['Piano','3/5'],['Posti auto','1']];
const energyItems = [['Classe energetica','A1'],['Riscaldamento','Autonomo'],['Combustibile','Gas metano']];
const costItems = [['Prezzo richiesto','\u20AC 325.000'],['Prezzo al m\u00B2','\u20AC 3.421'],['Spese condominiali','\u20AC 120/mese'],['Trattabile','S\u00EC']];

const visibleTabs = computed(() => {
  const tabs = [];
  if (s.value.show_details !== false) tabs.push({ id:'details', label:'Dettagli' });
  if (s.value.show_structure !== false) tabs.push({ id:'structure', label:'Struttura' });
  if (s.value.show_energy !== false) tabs.push({ id:'energy', label:'Energia' });
  if (s.value.show_costs !== false) tabs.push({ id:'costs', label:'Costi' });
  if (s.value.show_calculator) tabs.push({ id:'calculator', label:'Mutuo' });
  return tabs;
});

function showPanel(id) {
  if (s.value.layout === 'flat') return true;
  return activeTab.value === id;
}

const cols = computed(() => Math.max(2, Math.min(4, parseInt(s.value.columns) || 3)));
const cardStyle = computed(() => ({
  background: s.value.card_bg || '#FFFFFF',
  border: '1px solid ' + (s.value.card_border || '#E5E7EB'),
  borderRadius: (s.value.card_radius || 14) + 'px',
  padding: (s.value.card_padding || 28) + 'px',
}));
const gridStyle = computed(() => ({
  display: 'grid',
  gridTemplateColumns: 'repeat(' + cols.value + ', 1fr)',
  gap: '0 24px',
}));
const rowStyle = computed(() => ({
  display: 'flex', alignItems: 'center', gap: '10px',
  padding: '9px 0',
  borderBottom: '1px solid ' + (s.value.divider_color || '#F1F5F9'),
}));
</script>
