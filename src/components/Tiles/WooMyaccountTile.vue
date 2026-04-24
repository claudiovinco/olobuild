<template>
  <div>
    <div :style="wrapperStyle">
      <!-- Sidebar -->
      <div :style="sidebarStyle">
        <div v-for="(item, i) in menuItems" :key="i" :style="menuItemStyle(i)">
          {{ item }}
        </div>
      </div>
      <!-- Content -->
      <div :style="contentStyle">
        <h3 :style="{ margin: '0 0 16px', fontSize: '20px', fontWeight: 700, color: s.heading_color || 'var(--olo-color-text, #374151)' }">{{ t('Dashboard') }}</h3>
        <p :style="{ color: s.text_color || '#374151', fontSize: '14px', lineHeight: '1.6' }">
          Benvenuto <strong>{{ t('Mario Rossi') }}</strong>. Dalla dashboard del tuo account puoi gestire i tuoi ordini recenti, indirizzi di spedizione e fatturazione e modificare la password e i dettagli del tuo account.
        </p>
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;margin-top:20px;">
          <div v-for="stat in stats" :key="stat.label" :style="statCardStyle">
            <div :style="{ fontSize: '24px', fontWeight: 700, color: s.link_color || '#4f46e5' }">{{ stat.value }}</div>
            <div :style="{ fontSize: '12px', color: s.text_color || '#6B7280', marginTop: '4px' }">{{ stat.label }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { t } from '@/i18n';
import { computed } from 'vue';
const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const s = computed(() => props.settings || {});
const menuItems = ['Dashboard', 'Ordini', 'Download', 'Indirizzi', 'Dettagli account', 'Esci'];
const stats = [
  { label: 'Ordini totali', value: '12' },
  { label: 'In attesa', value: '2' },
  { label: 'Completati', value: '10' },
];
const radius = computed(() => ((v => isNaN(v) ? 8 : v)(parseInt(s.value.border_radius))) + 'px');
const wrapperStyle = computed(() => ({
  display: 'grid',
  gridTemplateColumns: '220px 1fr',
  gap: '24px',
  minHeight: '300px',
}));
const sidebarStyle = computed(() => ({
  background: s.value.sidebar_bg || '#F9FAFB',
  borderRadius: radius.value,
  padding: '8px',
}));
const menuItemStyle = (i) => ({
  padding: '10px 16px',
  borderRadius: '6px',
  fontSize: '14px',
  fontWeight: i === 0 ? 600 : 400,
  cursor: 'pointer',
  background: i === 0 ? (s.value.sidebar_active_bg || '#4f46e5') : 'transparent',
  color: i === 0 ? (s.value.sidebar_active_color || '#FFFFFF') : (s.value.sidebar_color || '#374151'),
  marginBottom: '2px',
});
const contentStyle = computed(() => ({
  background: s.value.content_bg || '#FFFFFF',
  border: `1px solid ${s.value.border_color || '#E5E7EB'}`,
  borderRadius: radius.value,
  padding: '24px',
}));
const statCardStyle = computed(() => ({
  background: s.value.sidebar_bg || '#F9FAFB',
  borderRadius: radius.value,
  padding: '16px',
  textAlign: 'center',
}));
</script>
