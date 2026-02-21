<template>
  <div class="olom-booking-detail" v-if="booking">
    <div class="olom-page-header">
      <router-link to="/prenotazioni" class="olom-back">&larr; Prenotazioni</router-link>
      <h2 class="olom-page-title">Prenotazione #{{ booking.id }}</h2>
      <span class="olom-status-badge olom-status-lg" :class="'olom-status-' + booking.status">{{ statusLabel(booking.status) }}</span>
    </div>

    <div class="olom-detail-grid">
      <!-- Left: Details -->
      <div class="olom-detail-card">
        <h4>Dettagli soggiorno</h4>
        <div class="olom-detail-rows">
          <div class="olom-detail-row">
            <span class="olom-detail-label">Struttura</span>
            <span>
              <span class="olom-svc-dot" :style="{ background: booking.service_color }"></span>
              <router-link :to="'/struttura/' + booking.service_id">{{ booking.service_name }}</router-link>
            </span>
          </div>
          <div class="olom-detail-row">
            <span class="olom-detail-label">Check-in</span>
            <span>{{ formatDate(booking.checkin_date) }}</span>
          </div>
          <div class="olom-detail-row">
            <span class="olom-detail-label">Check-out</span>
            <span>{{ formatDate(booking.checkout_date) }}</span>
          </div>
          <div class="olom-detail-row">
            <span class="olom-detail-label">Notti</span>
            <span>{{ booking.nights }}</span>
          </div>
          <div class="olom-detail-row">
            <span class="olom-detail-label">Ospiti</span>
            <span>{{ booking.guest_count }}</span>
          </div>
          <div class="olom-detail-row">
            <span class="olom-detail-label">Stagione</span>
            <span>{{ booking.season_name || '—' }}</span>
          </div>
          <div class="olom-detail-row">
            <span class="olom-detail-label">Prezzo/notte</span>
            <span>€ {{ booking.price_per_night.toFixed(2) }}</span>
          </div>
          <div class="olom-detail-row olom-detail-total">
            <span class="olom-detail-label">Totale</span>
            <span>€ {{ booking.price_total.toFixed(2) }}</span>
          </div>
          <div class="olom-detail-row" v-if="booking.source">
            <span class="olom-detail-label">Origine</span>
            <span>{{ sourceLabel(booking.source) }}</span>
          </div>
        </div>
      </div>

      <!-- Right: Guest -->
      <div class="olom-detail-card">
        <h4>Dati ospite</h4>
        <div class="olom-detail-rows">
          <div class="olom-detail-row">
            <span class="olom-detail-label">Nome</span>
            <span>{{ booking.guest_name }}</span>
          </div>
          <div class="olom-detail-row">
            <span class="olom-detail-label">Email</span>
            <span><a v-if="booking.guest_email" :href="'mailto:' + booking.guest_email">{{ booking.guest_email }}</a><span v-else>—</span></span>
          </div>
          <div class="olom-detail-row">
            <span class="olom-detail-label">Telefono</span>
            <span><a v-if="booking.guest_phone" :href="'tel:' + booking.guest_phone">{{ booking.guest_phone }}</a><span v-else>—</span></span>
          </div>
          <div class="olom-detail-row" v-if="booking.notes">
            <span class="olom-detail-label">Note</span>
            <span>{{ booking.notes }}</span>
          </div>
          <div class="olom-detail-row" v-if="booking.internal_notes">
            <span class="olom-detail-label">Note interne</span>
            <span class="olom-internal-note">{{ booking.internal_notes }}</span>
          </div>
        </div>

        <!-- Actions -->
        <div class="olom-detail-actions">
          <button v-if="booking.status === 'pending'" class="olom-btn olom-btn-success" @click="setStatus('confirmed')">✓ Conferma</button>
          <button v-if="booking.status === 'confirmed'" class="olom-btn olom-btn-success" @click="setStatus('completed')">✓ Completa</button>
          <button v-if="booking.status !== 'cancelled'" class="olom-btn olom-btn-danger" @click="setStatus('cancelled')">✕ Annulla</button>
          <button class="olom-btn olom-btn-ghost" @click="showEdit = true">Modifica</button>
          <button class="olom-btn olom-btn-ghost olom-btn-danger-text" @click="confirmDelete">Elimina</button>
        </div>
      </div>
    </div>

    <!-- Log -->
    <div class="olom-detail-card olom-detail-log" v-if="log.length">
      <h4>Storico modifiche</h4>
      <div v-for="entry in log" :key="entry.id" class="olom-log-entry">
        <span class="olom-log-date">{{ formatDateTime(entry.created_at) }}</span>
        <span class="olom-log-user">{{ entry.user_name || 'Sistema' }}</span>
        <span class="olom-log-action">{{ entry.details }}</span>
      </div>
    </div>

    <!-- Edit modal -->
    <BookingModal v-if="showEdit" :booking="booking" :services="services"
                  @close="showEdit = false" @saved="showEdit = false; reload();" />
  </div>
  <div v-else class="olom-loading">Caricamento...</div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useBookingsStore } from '../stores/bookings.js';
import { useManagerStore } from '../stores/manager.js';
import BookingModal from '../components/BookingModal.vue';

const props = defineProps({ id: [String, Number] });
const router = useRouter();
const store = useBookingsStore();
const mgrStore = useManagerStore();
const booking = ref(null);
const log = ref([]);
const services = ref([]);
const showEdit = ref(false);

onMounted(() => reload());

async function reload() {
  await store.fetchBooking(props.id);
  booking.value = store.current;
  await store.fetchLog(props.id);
  log.value = store.log;
  if (!mgrStore.services.length) await mgrStore.fetchServices();
  services.value = mgrStore.services;
}

async function setStatus(status) {
  await store.updateStatus(props.id, status);
  booking.value = store.current;
  await store.fetchLog(props.id);
  log.value = store.log;
}

async function confirmDelete() {
  if (confirm('Eliminare questa prenotazione?')) {
    await store.deleteBooking(props.id);
    router.push('/prenotazioni');
  }
}

function formatDate(d) {
  return new Date(d + 'T00:00:00').toLocaleDateString('it-IT', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
}

function formatDateTime(dt) {
  return new Date(dt).toLocaleString('it-IT', { day: '2-digit', month: 'short', hour: '2-digit', minute: '2-digit' });
}

function statusLabel(s) {
  return { pending: 'In attesa', confirmed: 'Confermata', cancelled: 'Annullata', completed: 'Completata' }[s] || s;
}

function sourceLabel(s) {
  return { website: 'Sito web', manual: 'Manuale', phone: 'Telefono' }[s] || s;
}
</script>
