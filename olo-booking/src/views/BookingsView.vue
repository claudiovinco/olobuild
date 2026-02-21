<template>
  <div class="olom-bookings">
    <div class="olom-page-header">
      <h2 class="olom-page-title">Prenotazioni</h2>
      <div class="olom-view-toggle">
        <button class="olom-vt-btn" :class="{ 'olom-vt-active': viewMode === 'list' }" @click="viewMode = 'list'" title="Vista lista">
          <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor"><rect x="1" y="2" width="14" height="2" rx="0.5"/><rect x="1" y="7" width="14" height="2" rx="0.5"/><rect x="1" y="12" width="14" height="2" rx="0.5"/></svg>
        </button>
        <button class="olom-vt-btn" :class="{ 'olom-vt-active': viewMode === 'calendar' }" @click="viewMode = 'calendar'" title="Vista calendario">
          <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor"><rect x="1" y="3" width="14" height="12" rx="1.5" fill="none" stroke="currentColor" stroke-width="1.3"/><line x1="1" y1="6.5" x2="15" y2="6.5" stroke="currentColor" stroke-width="1.3"/><line x1="5.5" y1="6.5" x2="5.5" y2="15" stroke="currentColor" stroke-width="0.8" opacity="0.4"/><line x1="10.5" y1="6.5" x2="10.5" y2="15" stroke="currentColor" stroke-width="0.8" opacity="0.4"/><line x1="1" y1="10.5" x2="15" y2="10.5" stroke="currentColor" stroke-width="0.8" opacity="0.4"/><rect x="4" y="1" width="1.5" height="3.5" rx="0.5"/><rect x="10.5" y="1" width="1.5" height="3.5" rx="0.5"/></svg>
        </button>
      </div>
      <div class="olom-btn-group">
        <button v-if="perm.export_bookings" class="olom-btn olom-btn-ghost" @click="exportCSV" :disabled="exporting" title="Esporta CSV">
          <svg width="14" height="14" viewBox="0 0 16 16" fill="currentColor" style="vertical-align:-2px"><path d="M2 12h12v2H2zm5-1V4H5l3-3 3 3H9v7z"/></svg>
          {{ exporting ? '...' : 'Esporta' }}
        </button>
        <button v-if="perm.import_bookings" class="olom-btn olom-btn-ghost" @click="$refs.csvInput.click()" title="Importa CSV">
          <svg width="14" height="14" viewBox="0 0 16 16" fill="currentColor" style="vertical-align:-2px"><path d="M2 12h12v2H2zm5-4v7h2V8h2l-3-3-3 3z" transform="rotate(180 8 10)"/></svg>
          Importa
        </button>
        <input ref="csvInput" type="file" accept=".csv" style="display:none" @change="onImportFile" />
      </div>
      <button class="olom-btn olom-btn-primary" @click="showModal = true">+ Nuova prenotazione</button>
    </div>

    <!-- Filters -->
    <div class="olom-filters">
      <!-- Service type filter (admin/supervisor) -->
      <select v-if="canFilterType" v-model="filters.service_type" @change="loadBookings" class="olom-filter-type">
        <option value="">Tutte le strutture</option>
        <option value="accommodation">Solo Baite</option>
        <option value="service">Solo Servizi</option>
      </select>
      <select v-model="filters.service_id" @change="loadBookings">
        <option value="">{{ filters.service_type ? 'Tutte' : 'Tutte le strutture' }}</option>
        <option v-for="s in filteredServices" :key="s.id" :value="s.id">{{ s.title }}</option>
      </select>
      <select v-model="filters.status" @change="loadBookings">
        <option value="">Tutti gli stati</option>
        <option value="pending">In attesa</option>
        <option value="confirmed">Confermate</option>
        <option value="cancelled">Annullate</option>
        <option value="completed">Completate</option>
      </select>
      <template v-if="viewMode === 'list'">
        <input type="date" v-model="filters.date_from" @change="loadBookings" placeholder="Dal" />
        <input type="date" v-model="filters.date_to" @change="loadBookings" placeholder="Al" />
      </template>
      <input type="text" v-model="filters.search" @input="debounceSearch" placeholder="Cerca ospite..." class="olom-search" />
    </div>

    <!-- Calendar view -->
    <BookingCalendar v-if="viewMode === 'calendar'"
      :bookings="store.bookings"
      :services="filteredServices"
      @month-change="onCalendarMonthChange"
      @booking-moved="onBookingMoved" />

    <!-- List view -->
    <template v-if="viewMode === 'list'">
      <div class="olom-booking-table" v-if="store.bookings.length">
        <div class="olom-bt-header">
          <span class="olom-bt-col olom-bt-guest">Ospite</span>
          <span class="olom-bt-col olom-bt-service">Struttura</span>
          <span class="olom-bt-col olom-bt-dates">Check-in</span>
          <span class="olom-bt-col olom-bt-dates">Check-out</span>
          <span class="olom-bt-col olom-bt-sm">Notti</span>
          <span class="olom-bt-col olom-bt-sm">Ospiti</span>
          <span class="olom-bt-col olom-bt-price">Totale</span>
          <span class="olom-bt-col olom-bt-status">Stato</span>
        </div>
        <router-link v-for="b in store.bookings" :key="b.id" :to="'/prenotazione/' + b.id" class="olom-bt-row">
          <span class="olom-bt-col olom-bt-guest">{{ b.guest_name }}</span>
          <span class="olom-bt-col olom-bt-service">
            <span class="olom-svc-dot" :style="{ background: b.service_color }"></span>
            {{ b.service_name }}
          </span>
          <span class="olom-bt-col olom-bt-dates">{{ formatDate(b.checkin_date) }}</span>
          <span class="olom-bt-col olom-bt-dates">{{ formatDate(b.checkout_date) }}</span>
          <span class="olom-bt-col olom-bt-sm">{{ b.nights }}</span>
          <span class="olom-bt-col olom-bt-sm">{{ b.guest_count }}</span>
          <span class="olom-bt-col olom-bt-price">{{ b.price_total ? '\u20AC ' + b.price_total.toFixed(2) : '—' }}</span>
          <span class="olom-bt-col olom-bt-status">
            <span class="olom-status-badge" :class="'olom-status-' + b.status">{{ statusLabel(b.status) }}</span>
          </span>
        </router-link>
      </div>
      <div v-else-if="!store.loading" class="olom-empty">Nessuna prenotazione trovata.</div>
    </template>

    <div v-if="store.loading" class="olom-loading">Caricamento...</div>

    <!-- Import result modal -->
    <div v-if="importResult" class="olom-modal-backdrop" @click.self="importResult = null">
      <div class="olom-modal">
        <div class="olom-modal-header">
          <h3>Risultato importazione</h3>
          <button class="olom-modal-close" @click="importResult = null">&times;</button>
        </div>
        <div class="olom-modal-body">
          <p><strong>{{ importResult.imported }}</strong> su {{ importResult.total }} prenotazioni importate.</p>
          <div v-if="importResult.errors.length" class="olom-import-errors">
            <p class="olom-form-error">Errori:</p>
            <ul>
              <li v-for="(e, i) in importResult.errors" :key="i">{{ e }}</li>
            </ul>
          </div>
        </div>
        <div class="olom-modal-footer">
          <button class="olom-btn olom-btn-primary" @click="importResult = null; loadBookings();">Chiudi</button>
        </div>
      </div>
    </div>

    <!-- Modal -->
    <BookingModal v-if="showModal" :services="filteredServices" @close="showModal = false"
                  @saved="showModal = false; loadBookings();" />
  </div>
</template>

<script setup>
import { ref, reactive, computed, watch, onMounted } from 'vue';
import { useBookingsStore } from '../stores/bookings.js';
import { useManagerStore } from '../stores/manager.js';
import { api } from '../stores/api.js';
import BookingModal from '../components/BookingModal.vue';
import BookingCalendar from '../components/BookingCalendar.vue';

const store = useBookingsStore();
const mgrStore = useManagerStore();
const cfg = window.oloManagerConfig || {};
const perm = cfg.user?.permissions || {};
const canFilterType = perm.filter_service_type || false;
const showModal = ref(false);
const viewMode = ref('list');
const exporting = ref(false);
const importResult = ref(null);
const csvInput = ref(null);
let searchTimer = null;
let calMonth = '';
let listTypeFilter = ''; // remember list filter when switching views

const filters = reactive({
  service_type: '',
  service_id: '',
  status: '',
  date_from: '',
  date_to: '',
  search: '',
});

// When switching to calendar, auto-filter to accommodation for admin/supervisor
watch(viewMode, (mode) => {
  if (mode === 'calendar') {
    listTypeFilter = filters.service_type; // save current filter
    if (canFilterType && !filters.service_type) {
      filters.service_type = 'accommodation';
    }
  } else {
    // Restore previous filter when going back to list
    filters.service_type = listTypeFilter;
  }
  loadBookings();
});

// Filter services by type for the dropdown
const filteredServices = computed(() => {
  if (!filters.service_type) return mgrStore.services;
  return mgrStore.services.filter(s => s.service_type === filters.service_type);
});

onMounted(async () => {
  if (!mgrStore.services.length) await mgrStore.fetchServices();
  loadBookings();
});

function loadBookings() {
  const params = { ...filters };
  // In calendar mode, use the calendar month range instead of manual date filters
  if (viewMode.value === 'calendar' && calMonth) {
    const [y, m] = calMonth.split('-').map(Number);
    // Fetch a range covering the visible month (including padding days)
    const from = new Date(y, m - 1, -6);
    const to = new Date(y, m, 7);
    params.date_from = fmtDate(from);
    params.date_to = fmtDate(to);
    delete params.search;
  }
  store.filters = params;
  store.fetchBookings();
}

function onCalendarMonthChange(yearMonth) {
  calMonth = yearMonth;
  if (viewMode.value === 'calendar') {
    loadBookings();
  }
}

function debounceSearch() {
  clearTimeout(searchTimer);
  searchTimer = setTimeout(loadBookings, 400);
}

function fmtDate(d) {
  const y = d.getFullYear();
  const m = String(d.getMonth() + 1).padStart(2, '0');
  const dd = String(d.getDate()).padStart(2, '0');
  return `${y}-${m}-${dd}`;
}

function formatDate(d) {
  return new Date(d + 'T00:00:00').toLocaleDateString('it-IT', { day: '2-digit', month: 'short', year: 'numeric' });
}

function statusLabel(s) {
  const m = { pending: 'In attesa', confirmed: 'Confermata', cancelled: 'Annullata', completed: 'Completata' };
  return m[s] || s;
}

/* ── Booking moved (drag & drop from calendar) ── */

async function onBookingMoved({ id, checkin_date, checkout_date }) {
  try {
    await store.updateBooking(id, { checkin_date, checkout_date });
    loadBookings();
  } catch (e) {
    alert('Errore nello spostamento: ' + (e.message || ''));
    loadBookings();
  }
}

/* ── CSV Export ── */

async function exportCSV() {
  exporting.value = true;
  try {
    const query = new URLSearchParams();
    for (const [k, v] of Object.entries(filters)) {
      if (v) query.set(k, v);
    }
    const qs = query.toString();
    const data = await api.get('/bookings/export' + (qs ? '?' + qs : ''));

    const headers = ['id','struttura','ospite','email','telefono','ospiti','checkin','checkout','notti','prezzo_notte','prezzo_totale','stagione','stato','origine','note','note_interne','data_creazione'];
    const statusMap = { pending: 'In attesa', confirmed: 'Confermata', cancelled: 'Annullata', completed: 'Completata' };

    const rows = data.map(b => [
      b.id, b.service_name, b.guest_name, b.guest_email, b.guest_phone,
      b.guest_count, b.checkin_date, b.checkout_date, b.nights,
      b.price_per_night, b.price_total, b.season_name,
      statusMap[b.status] || b.status, b.source,
      b.notes || '', b.internal_notes || '', b.created_at
    ]);

    const csvContent = [headers, ...rows]
      .map(row => row.map(cell => {
        const s = String(cell ?? '');
        return s.includes(',') || s.includes('"') || s.includes('\n')
          ? '"' + s.replace(/"/g, '""') + '"' : s;
      }).join(','))
      .join('\n');

    const BOM = '\uFEFF';
    const blob = new Blob([BOM + csvContent], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'prenotazioni_' + new Date().toISOString().slice(0,10) + '.csv';
    a.click();
    URL.revokeObjectURL(url);
  } catch (e) {
    alert('Errore durante l\'esportazione: ' + (e.message || ''));
  } finally {
    exporting.value = false;
  }
}

/* ── CSV Import ── */

function onImportFile(e) {
  const file = e.target.files[0];
  if (!file) return;
  e.target.value = '';

  const reader = new FileReader();
  reader.onload = async (ev) => {
    try {
      const text = ev.target.result;
      const rows = parseCSV(text);
      if (rows.length < 2) {
        alert('Il file CSV è vuoto o non contiene dati.');
        return;
      }

      const headers = rows[0].map(h => h.trim().toLowerCase());
      const dataRows = rows.slice(1).filter(r => r.some(c => c.trim()));

      if (!dataRows.length) {
        alert('Nessuna riga dati nel CSV.');
        return;
      }

      if (!confirm(`Importare ${dataRows.length} prenotazioni?`)) return;

      const mapped = dataRows.map(cols => {
        const obj = {};
        headers.forEach((h, i) => { obj[h] = (cols[i] || '').trim(); });
        return obj;
      });

      const result = await api.post('/bookings/import', { rows: mapped });
      importResult.value = result;
    } catch (err) {
      alert('Errore durante l\'importazione: ' + (err.message || ''));
    }
  };
  reader.readAsText(file, 'UTF-8');
}

function parseCSV(text) {
  const rows = [];
  let row = [];
  let cell = '';
  let inQuotes = false;

  for (let i = 0; i < text.length; i++) {
    const c = text[i];
    if (inQuotes) {
      if (c === '"') {
        if (text[i + 1] === '"') { cell += '"'; i++; }
        else { inQuotes = false; }
      } else {
        cell += c;
      }
    } else {
      if (c === '"') { inQuotes = true; }
      else if (c === ',') { row.push(cell); cell = ''; }
      else if (c === '\n' || (c === '\r' && text[i + 1] === '\n')) {
        if (c === '\r') i++;
        row.push(cell); cell = '';
        rows.push(row); row = [];
      } else if (c === '\r') {
        row.push(cell); cell = '';
        rows.push(row); row = [];
      } else {
        cell += c;
      }
    }
  }
  if (cell || row.length) { row.push(cell); rows.push(row); }
  return rows;
}
</script>
