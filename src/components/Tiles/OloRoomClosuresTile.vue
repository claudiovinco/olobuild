<template>
  <div>
    <h3 style="font-size:16px;font-weight:700;color:var(--olo-color-text, #374151);margin:0 0 10px">{{ t('Chiusure programmate') }}</h3>
    <div style="display:flex;flex-direction:column;gap:8px">
      <div v-for="cl in closures" :key="cl.date" style="display:flex;align-items:center;gap:10px;padding:8px 12px;background:#fef2f2;border-radius:8px;border-left:3px solid #ef4444">
        <span style="font-size:13px;font-weight:600;color:#991b1b;white-space:nowrap">{{ cl.date }}</span>
        <span style="font-size:13px;color:#7f1d1d">{{ cl.reason }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';
const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const defaults = { max_items: 5 };
const s = computed(() => ({ ...defaults, ...props.settings }));
const allClosures = [
  { date: '10-12 Mar 2026', reason: 'Manutenzione impianto climatizzazione' },
  { date: '25 Mar 2026', reason: 'Festa patronale' },
  { date: '1 Apr 2026', reason: 'Lavori di tinteggiatura' },
];
const closures = computed(() => allClosures.slice(0, parseInt(s.value.max_items) || 5));
</script>
