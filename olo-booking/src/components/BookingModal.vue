<template>
  <div class="olom-modal-backdrop" @click.self="$emit('close')">
    <div class="olom-modal">
      <div class="olom-modal-header">
        <h3>{{ isEdit ? 'Modifica prenotazione' : 'Nuova prenotazione' }}</h3>
        <button class="olom-modal-close" @click="$emit('close')">&times;</button>
      </div>
      <div class="olom-modal-body">
        <div class="olom-form-row">
          <label>Struttura *</label>
          <select v-model="form.service_id" required>
            <option value="">— Seleziona —</option>
            <option v-for="s in services" :key="s.id" :value="s.id">{{ s.title }}</option>
          </select>
        </div>
        <div class="olom-form-grid">
          <div class="olom-form-row">
            <label>Check-in *</label>
            <input type="date" v-model="form.checkin_date" required />
          </div>
          <div class="olom-form-row">
            <label>Check-out *</label>
            <input type="date" v-model="form.checkout_date" required />
          </div>
          <div class="olom-form-row">
            <label>Ospiti</label>
            <input type="number" v-model.number="form.guest_count" min="1" max="20" />
          </div>
        </div>
        <div class="olom-form-row">
          <label>Nome ospite *</label>
          <input type="text" v-model="form.guest_name" required placeholder="Nome e cognome" />
        </div>
        <div class="olom-form-grid">
          <div class="olom-form-row">
            <label>Email</label>
            <input type="email" v-model="form.guest_email" placeholder="email@esempio.it" />
          </div>
          <div class="olom-form-row">
            <label>Telefono</label>
            <input type="tel" v-model="form.guest_phone" placeholder="+39 ..." />
          </div>
        </div>
        <div class="olom-form-grid">
          <div class="olom-form-row">
            <label>Prezzo/notte (€)</label>
            <input type="number" v-model.number="form.price_per_night" step="0.01" min="0" />
          </div>
          <div class="olom-form-row">
            <label>Totale (€)</label>
            <input type="number" v-model.number="form.price_total" step="0.01" min="0" />
          </div>
          <div class="olom-form-row">
            <label>Stato</label>
            <select v-model="form.status">
              <option value="pending">In attesa</option>
              <option value="confirmed">Confermata</option>
              <option value="cancelled">Annullata</option>
              <option value="completed">Completata</option>
            </select>
          </div>
        </div>
        <div class="olom-form-row">
          <label>Note</label>
          <textarea v-model="form.notes" rows="2" placeholder="Note visibili all'ospite"></textarea>
        </div>
        <div class="olom-form-row">
          <label>Note interne</label>
          <textarea v-model="form.internal_notes" rows="2" placeholder="Solo per il gestore"></textarea>
        </div>
        <div v-if="error" class="olom-form-error">{{ error }}</div>
      </div>
      <div class="olom-modal-footer">
        <button class="olom-btn olom-btn-ghost" @click="$emit('close')">Annulla</button>
        <button class="olom-btn olom-btn-success" @click="submit" :disabled="saving">
          {{ saving ? 'Salvataggio...' : (isEdit ? 'Salva' : 'Crea prenotazione') }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, watch } from 'vue';
import { useBookingsStore } from '../stores/bookings.js';

const props = defineProps({
  booking: { type: Object, default: null },
  services: { type: Array, default: () => [] },
  preselectedServiceId: { type: Number, default: 0 },
});
const emit = defineEmits(['close', 'saved']);

const isEdit = !!props.booking;
const store = useBookingsStore();
const saving = ref(false);
const error = ref('');

const form = reactive({
  service_id: props.booking?.service_id || props.preselectedServiceId || '',
  checkin_date: props.booking?.checkin_date || '',
  checkout_date: props.booking?.checkout_date || '',
  guest_count: props.booking?.guest_count || 1,
  guest_name: props.booking?.guest_name || '',
  guest_email: props.booking?.guest_email || '',
  guest_phone: props.booking?.guest_phone || '',
  price_per_night: props.booking?.price_per_night || 0,
  price_total: props.booking?.price_total || 0,
  status: props.booking?.status || 'pending',
  notes: props.booking?.notes || '',
  internal_notes: props.booking?.internal_notes || '',
  source: 'manual',
});

// Auto-calc total when price/night or dates change
watch([() => form.checkin_date, () => form.checkout_date, () => form.price_per_night], () => {
  if (form.checkin_date && form.checkout_date && form.price_per_night) {
    const d1 = new Date(form.checkin_date);
    const d2 = new Date(form.checkout_date);
    const nights = Math.max(1, Math.round((d2 - d1) / 86400000));
    form.price_total = +(nights * form.price_per_night).toFixed(2);
  }
});

async function submit() {
  error.value = '';
  if (!form.service_id || !form.guest_name || !form.checkin_date || !form.checkout_date) {
    error.value = 'Compila tutti i campi obbligatori.';
    return;
  }
  saving.value = true;
  try {
    if (isEdit) {
      await store.updateBooking(props.booking.id, form);
    } else {
      await store.createBooking(form);
    }
    emit('saved');
  } catch (e) {
    error.value = e.message;
  } finally {
    saving.value = false;
  }
}
</script>
