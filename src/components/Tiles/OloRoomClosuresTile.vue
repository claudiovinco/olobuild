<template>
  <div>
    <h3 :style="{ fontSize:'16px', fontWeight:'700', color: TOKENS.text, margin:'0 0 10px' }">{{ t('Chiusure programmate') }}</h3>
    <div style="display:flex;flex-direction:column;gap:8px">
      <div v-for="cl in closures" :key="cl.date" :style="rowStyle">
        <span :style="{ fontSize:'13px', fontWeight:'600', color: TOKENS.error.fg, whiteSpace:'nowrap' }">{{ cl.date }}</span>
        <span :style="{ fontSize:'13px', color: TOKENS.error.fg }">{{ cl.reason }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';
import { TOKENS } from '@/composables/oloTileDefaults';
const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const defaults = { max_items: 5 };
const s = computed(() => ({ ...defaults, ...props.settings }));
// Chiusure = stato semantico "error": sfondo tinta soft + accento error.
const rowStyle = { display: 'flex', alignItems: 'center', gap: '10px', padding: '8px 12px', background: TOKENS.error.bg, borderRadius: '8px', borderLeft: '3px solid ' + TOKENS.error.fg };
const allClosures = [
  { date: '10-12 Mar 2026', reason: 'Manutenzione impianto climatizzazione' },
  { date: '25 Mar 2026', reason: 'Festa patronale' },
  { date: '1 Apr 2026', reason: 'Lavori di tinteggiatura' },
];
const closures = computed(() => allClosures.slice(0, parseInt(s.value.max_items) || 5));
</script>
