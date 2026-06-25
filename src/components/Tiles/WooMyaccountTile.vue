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
        <h3 :style="{ margin: '0 0 16px', fontSize: '20px', fontWeight: 700, color: resolveColor(s.heading_color, TOKENS.text) }">{{ t('Dashboard') }}</h3>
        <p :style="{ color: resolveColor(s.text_color, TOKENS.text), fontSize: '14px', lineHeight: '1.6' }">
          {{ t('Benvenuto') }} <strong>{{ t('Mario Rossi') }}</strong>{{ t('. Dalla dashboard del tuo account puoi gestire i tuoi ordini recenti, indirizzi di spedizione e fatturazione e modificare la password e i dettagli del tuo account.') }}
        </p>
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;margin-top:20px;">
          <div v-for="stat in stats" :key="stat.label" :style="statCardStyle">
            <div :style="{ fontSize: '24px', fontWeight: 700, color: resolveColor(s.link_color, TOKENS.primary) }">{{ stat.value }}</div>
            <div :style="{ fontSize: '12px', color: resolveColor(s.text_color, TOKENS.textSoft), marginTop: '4px' }">{{ stat.label }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { t } from '@/i18n';
import { computed } from 'vue';
import { resolveColor, TOKENS } from '@/composables/oloTileDefaults';
const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const s = computed(() => props.settings || {});
const menuItems = [t('Dashboard'), t('Ordini'), 'Download', t('Indirizzi'), t('Dettagli account'), t('Esci')];
const stats = [
  { label: t('Ordini totali'), value: '12' },
  { label: t('In attesa'), value: '2' },
  { label: t('Completati'), value: '10' },
];
const radius = computed(() => ((v => isNaN(v) ? 8 : v)(parseInt(s.value.border_radius))) + 'px');
const wrapperStyle = computed(() => ({
  display: 'grid',
  gridTemplateColumns: '220px 1fr',
  gap: '24px',
  minHeight: '300px',
}));
const sidebarStyle = computed(() => ({
  background: resolveColor(s.value.sidebar_bg, TOKENS.surfaceAlt),
  borderRadius: radius.value,
  padding: '8px',
}));
const menuItemStyle = (i) => ({
  padding: '10px 16px',
  borderRadius: '6px',
  fontSize: '14px',
  fontWeight: i === 0 ? 600 : 400,
  cursor: 'pointer',
  background: i === 0 ? resolveColor(s.value.sidebar_active_bg, TOKENS.primary) : 'transparent',
  color: i === 0 ? resolveColor(s.value.sidebar_active_color, TOKENS.onPrimary) : resolveColor(s.value.sidebar_color, TOKENS.text),
  marginBottom: '2px',
});
const contentStyle = computed(() => ({
  background: resolveColor(s.value.content_bg, TOKENS.surface),
  border: `1px solid ${resolveColor(s.value.border_color, TOKENS.border)}`,
  borderRadius: radius.value,
  padding: '24px',
}));
const statCardStyle = computed(() => ({
  background: resolveColor(s.value.sidebar_bg, TOKENS.surfaceAlt),
  borderRadius: radius.value,
  padding: '16px',
  textAlign: 'center',
}));
</script>
