<template>
  <!--
    DeviceSwitch — selettore responsive unificato (icone), 5 zone principali.
    Legato al viewport globale (builderStore.viewMode): cambiare dispositivo qui
    cambia anche l'anteprima canvas, e tutti i controlli responsive seguono lo
    stesso breakpoint. Sostituisce le vecchie pill testuali DT/TL/TP/ML/MB.
  -->
  <div class="olo-devsw" role="group" :aria-label="t('Dispositivo')">
    <button
      v-for="d in devices"
      :key="d.key"
      type="button"
      :class="{ on: activeKey === d.key }"
      @click="select(d.key)"
      :title="t(d.label)"
      :aria-label="t(d.label)"
      :aria-pressed="activeKey === d.key"
      v-html="d.icon"
    ></button>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useBuilderStore } from '@/stores/builder';
import { t } from '@/i18n';

const builderStore = useBuilderStore();

const devices = [
  { key: 'desktop', label: 'Desktop', icon: '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="3" rx="2"/><line x1="8" x2="16" y1="21" y2="21"/><line x1="12" x2="12" y1="17" y2="21"/></svg>' },
  { key: 'tablet_landscape', label: 'Tablet orizzontale', icon: '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><line x1="12" x2="12.01" y1="12" y2="12"/></svg>' },
  { key: 'tablet', label: 'Tablet', icon: '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="16" height="20" x="4" y="2" rx="2"/><line x1="12" x2="12.01" y1="18" y2="18"/></svg>' },
  { key: 'mobile_landscape', label: 'Mobile orizzontale', icon: '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="12" x2="12.01" y1="12" y2="12"/></svg>' },
  { key: 'mobile', label: 'Mobile', icon: '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="14" height="20" x="5" y="2" rx="2"/><line x1="12" x2="12.01" y1="18" y2="18"/></svg>' },
];

// widescreen condivide i valori "desktop": evidenzia Desktop.
const activeKey = computed(() => (builderStore.viewMode === 'widescreen' ? 'desktop' : builderStore.viewMode));

function select(key) {
  builderStore.setViewMode(key);
}
</script>

<style scoped>
.olo-devsw {
  display: flex;
  gap: 2px;
  background: #374151;
  border-radius: 8px;
  padding: 2px;
  flex-shrink: 0;
}
.olo-devsw button {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 30px;
  height: 26px;
  border: none;
  background: transparent;
  color: #9ca3af;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.15s;
}
.olo-devsw button:hover { color: #e5e7eb; }
.olo-devsw button.on {
  background: var(--olo-ui-accent, #e8622a);
  color: #fff;
}
</style>
