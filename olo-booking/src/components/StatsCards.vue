<template>
  <div class="olom-stats" v-if="stats">
    <div class="olom-stat-card">
      <div class="olom-stat-icon"><ClipboardList :size="20" :stroke-width="1.75" /></div>
      <div class="olom-stat-data">
        <span class="olom-stat-num">{{ stats.total }}</span>
        <span class="olom-stat-label">Prenotazioni</span>
      </div>
    </div>
    <div class="olom-stat-card olom-stat-warning">
      <div class="olom-stat-icon"><Clock :size="20" :stroke-width="1.75" /></div>
      <div class="olom-stat-data">
        <span class="olom-stat-num">{{ stats.pending }}</span>
        <span class="olom-stat-label">In attesa</span>
      </div>
    </div>
    <div class="olom-stat-card olom-stat-success">
      <div class="olom-stat-icon"><CircleCheck :size="20" :stroke-width="1.75" /></div>
      <div class="olom-stat-data">
        <span class="olom-stat-num">{{ stats.confirmed }}</span>
        <span class="olom-stat-label">Confermate</span>
      </div>
    </div>
    <div class="olom-stat-card olom-stat-checkin">
      <div class="olom-stat-icon"><CalendarCheck :size="20" :stroke-width="1.75" /></div>
      <div class="olom-stat-data">
        <span class="olom-stat-num">{{ stats.today_checkins }}</span>
        <span class="olom-stat-label">Arrivi oggi</span>
      </div>
    </div>
    <div class="olom-stat-card olom-stat-revenue">
      <div class="olom-stat-icon"><Banknote :size="20" :stroke-width="1.75" /></div>
      <div class="olom-stat-data">
        <span class="olom-stat-num">{{ formatCurrency(stats.revenue_month) }}</span>
        <span class="olom-stat-label">Mese</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ClipboardList, Clock, CircleCheck, CalendarCheck, Banknote } from 'lucide-vue-next';
defineProps({ stats: Object });
function formatCurrency(n) {
  return new Intl.NumberFormat('it-IT', { style: 'currency', currency: 'EUR', minimumFractionDigits: 0 }).format(n || 0);
}
</script>
