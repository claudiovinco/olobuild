<template>
  <div class="olom-dashboard">
    <h2 class="olom-page-title">
      Ciao, {{ user.name }}!
    </h2>

    <StatsCards :stats="store.stats" />

    <!-- Service type filter + create button -->
    <div class="olom-page-header" v-if="store.services.length || perm.create_services">
      <h3 class="olom-section-title" style="margin:0">Le tue strutture</h3>
      <div v-if="canFilterType" class="olom-type-filter">
        <button class="olom-tf-btn" :class="{ 'olom-tf-active': typeFilter === '' }" @click="typeFilter = ''">Tutte</button>
        <button class="olom-tf-btn" :class="{ 'olom-tf-active': typeFilter === 'accommodation' }" @click="typeFilter = 'accommodation'">Baite</button>
        <button class="olom-tf-btn" :class="{ 'olom-tf-active': typeFilter === 'service' }" @click="typeFilter = 'service'">Servizi</button>
      </div>
      <button v-if="perm.create_services" class="olom-btn olom-btn-primary" @click="showCreateModal = true">+ Nuova struttura</button>
    </div>

    <div class="olom-svc-grid" v-if="displayedServices.length">
      <ServiceCard v-for="s in displayedServices" :key="s.id" :service="s" />
    </div>
    <div v-else-if="!store.loading && store.services.length" class="olom-empty">
      Nessuna struttura di questo tipo.
    </div>
    <div v-else-if="!store.loading" class="olom-empty">
      Nessuna struttura assegnata al tuo account.
    </div>

    <UpcomingList :events="store.upcoming" />

    <div v-if="store.loading" class="olom-loading">Caricamento...</div>

    <!-- Create service modal -->
    <div v-if="showCreateModal" class="olom-modal-backdrop" @click.self="showCreateModal = false">
      <div class="olom-modal">
        <div class="olom-modal-header">
          <h3>Nuova struttura</h3>
          <button class="olom-modal-close" @click="showCreateModal = false">&times;</button>
        </div>
        <div class="olom-modal-body">
          <div v-if="createError" class="olom-form-error">{{ createError }}</div>

          <div class="olom-form-row">
            <label>Nome struttura *</label>
            <input type="text" v-model="createForm.title" placeholder="es. Baita Stella Alpina" />
          </div>

          <div class="olom-form-grid">
            <div class="olom-form-row">
              <label>Tipo</label>
              <select v-model="createForm.service_type">
                <option value="accommodation">Baita / Alloggio</option>
                <option value="service">Servizio generico</option>
              </select>
            </div>
            <div class="olom-form-row">
              <label>Colore calendario</label>
              <input type="color" v-model="createForm.color" style="height:38px;padding:2px 4px" />
            </div>
          </div>

          <div class="olom-form-grid">
            <div class="olom-form-row">
              <label>Capienza (ospiti)</label>
              <input type="number" v-model="createForm.capacity" min="1" placeholder="6" />
            </div>
            <div class="olom-form-row">
              <label>Prezzo base / notte</label>
              <input type="text" v-model="createForm.price" placeholder="80" />
            </div>
          </div>

          <p class="olom-hint">Potrai completare tutti i dettagli (stagioni, galleria, regole...) dalla scheda struttura dopo la creazione.</p>
        </div>
        <div class="olom-modal-footer">
          <button class="olom-btn olom-btn-ghost" @click="showCreateModal = false">Annulla</button>
          <button class="olom-btn olom-btn-primary" @click="createService" :disabled="creating">
            {{ creating ? 'Creazione...' : 'Crea struttura' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useManagerStore } from '../stores/manager.js';
import { api } from '../stores/api.js';
import StatsCards from '../components/StatsCards.vue';
import ServiceCard from '../components/ServiceCard.vue';
import UpcomingList from '../components/UpcomingList.vue';

const router = useRouter();
const store = useManagerStore();
const cfg = window.oloManagerConfig || {};
const user = cfg.user || {};
const perm = cfg.user?.permissions || {};
const canFilterType = perm.filter_service_type || false;
const typeFilter = ref('');

const showCreateModal = ref(false);
const creating = ref(false);
const createError = ref('');
const createForm = reactive({
  title: '',
  service_type: 'accommodation',
  color: '#6366F1',
  capacity: '',
  price: '',
});

const displayedServices = computed(() => {
  if (!typeFilter.value) return store.services;
  return store.services.filter(s => s.service_type === typeFilter.value);
});

onMounted(() => {
  store.fetchDashboard();
});

async function createService() {
  createError.value = '';
  if (!createForm.title.trim()) {
    createError.value = 'Il nome della struttura è obbligatorio.';
    return;
  }

  creating.value = true;
  try {
    const result = await api.post('/manager/services', {
      title: createForm.title,
      service_type: createForm.service_type,
      color: createForm.color,
      capacity: createForm.capacity || undefined,
      price: createForm.price || undefined,
    });
    showCreateModal.value = false;
    // Refresh services and navigate to the new service
    await store.fetchServices();
    router.push('/struttura/' + result.id);
  } catch (e) {
    createError.value = e.message || 'Errore nella creazione.';
  } finally {
    creating.value = false;
  }
}
</script>
