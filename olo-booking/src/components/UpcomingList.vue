<template>
  <div class="olom-upcoming" v-if="events.length">
    <h3 class="olom-section-title">Prossimi arrivi / partenze</h3>
    <div class="olom-upcoming-list">
      <div v-for="e in events" :key="e.id + e.event_type" class="olom-upcoming-row"
           :class="'olom-upcoming-' + e.event_type">
        <span class="olom-upcoming-date">{{ formatDate(e.event_date) }}</span>
        <span class="olom-upcoming-type">
          {{ e.event_type === 'checkin' ? 'CHECK-IN' : 'CHECK-OUT' }}
        </span>
        <span class="olom-upcoming-guest">{{ e.guest_name }}</span>
        <span class="olom-upcoming-service">{{ e.service_name }}</span>
        <span class="olom-upcoming-nights">{{ e.nights }}n</span>
        <span class="olom-upcoming-status" :class="'olom-status-' + e.status">{{ statusLabel(e.status) }}</span>
      </div>
    </div>
  </div>
  <div v-else class="olom-upcoming-empty">
    Nessun arrivo o partenza nei prossimi 14 giorni.
  </div>
</template>

<script setup>
defineProps({ events: { type: Array, default: () => [] } });

function formatDate(d) {
  const dt = new Date(d + 'T00:00:00');
  return dt.toLocaleDateString('it-IT', { day: 'numeric', month: 'short' });
}
function statusLabel(s) {
  const m = { pending: 'In attesa', confirmed: 'Confermata', cancelled: 'Annullata', completed: 'Completata' };
  return m[s] || s;
}
</script>
