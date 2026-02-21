<template>
  <div class="olom-tl" @click="popup = null">
    <!-- Navigation -->
    <div class="olom-cal-nav">
      <button class="olom-btn-icon olom-cal-arrow" @click="changeMonth(-1)" title="Mese precedente">&lsaquo;</button>
      <h3 class="olom-cal-title">{{ monthLabel }}</h3>
      <button class="olom-btn-icon olom-cal-arrow" @click="changeMonth(1)" title="Mese successivo">&rsaquo;</button>
      <button class="olom-btn olom-btn-ghost olom-cal-today-btn" @click="goToday">Oggi</button>
    </div>

    <div class="olom-tl-body">
      <!-- Sidebar: service names -->
      <div class="olom-tl-sidebar">
        <div class="olom-tl-corner">Strutture</div>
        <div v-for="svc in services" :key="svc.id" class="olom-tl-svc-label">
          <span class="olom-svc-dot" :style="{ background: svc.color || '#6366F1' }"></span>
          <span class="olom-tl-svc-text">{{ svc.title }}</span>
        </div>
      </div>

      <!-- Scrollable timeline -->
      <div class="olom-tl-scroll" ref="scrollRef">
        <div class="olom-tl-grid" :style="{ width: days.length * cellW + 'px' }">
          <!-- Day headers -->
          <div class="olom-tl-days-head">
            <div v-for="d in days" :key="d.date" class="olom-tl-dh"
                 :class="{ 'olom-tl-dh-today': d.isToday, 'olom-tl-dh-we': d.isWe }"
                 :style="{ width: cellW + 'px' }">
              <span class="olom-tl-dh-name">{{ d.dayShort }}</span>
              <span class="olom-tl-dh-num">{{ d.num }}</span>
            </div>
          </div>

          <!-- Service rows -->
          <div v-for="svc in services" :key="'r'+svc.id" class="olom-tl-row">
            <!-- Cell backgrounds -->
            <div v-for="d in days" :key="d.date" class="olom-tl-cell"
                 :class="{ 'olom-tl-c-today': d.isToday, 'olom-tl-c-we': d.isWe }"
                 :style="{ width: cellW + 'px' }">
            </div>
            <!-- Today line -->
            <div v-if="todayIdx >= 0" class="olom-tl-today-line" :style="{ left: todayIdx * cellW + cellW / 2 + 'px' }"></div>
            <!-- Booking bars -->
            <div v-for="bar in barsForService(svc.id)" :key="bar.id"
                 class="olom-tl-bar"
                 :class="['olom-tl-bar-' + bar.status, { 'olom-tl-bar-drag': drag && drag.barId === bar.id }]"
                 :style="barStyle(bar)"
                 @click.stop="openPopup(bar, $event)"
                 @mousedown.prevent="onDragStart($event, bar)">
              <span class="olom-tl-bar-name">{{ bar.guest_name }}</span>
              <span class="olom-tl-bar-info">{{ bar.nights }}n</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Popup -->
    <div v-if="popup" class="olom-tl-popup" :style="popupStyle" @click.stop>
      <div class="olom-tl-popup-head">
        <strong>{{ popup.b.guest_name }}</strong>
        <button class="olom-modal-close" @click.stop="popup = null">&times;</button>
      </div>
      <div class="olom-tl-popup-body">
        <div class="olom-tl-popup-row">
          <span class="olom-svc-dot" :style="{ background: popup.b.service_color }"></span>
          {{ popup.b.service_name }}
        </div>
        <div class="olom-tl-popup-row">
          {{ fmtDateHuman(popup.b.checkin_date) }} &rarr; {{ fmtDateHuman(popup.b.checkout_date) }}
        </div>
        <div class="olom-tl-popup-grid">
          <span>{{ popup.b.nights }} notti</span>
          <span>{{ popup.b.guest_count }} ospiti</span>
          <span>&euro; {{ popup.b.price_total?.toFixed(2) || '—' }}</span>
        </div>
        <div class="olom-tl-popup-row">
          <span class="olom-status-badge" :class="'olom-status-' + popup.b.status">{{ statusLabel(popup.b.status) }}</span>
          <span v-if="popup.b.source" class="olom-tl-popup-src">{{ popup.b.source }}</span>
        </div>
        <div v-if="popup.b.guest_email" class="olom-tl-popup-row olom-tl-popup-contact">{{ popup.b.guest_email }}</div>
        <div v-if="popup.b.guest_phone" class="olom-tl-popup-row olom-tl-popup-contact">{{ popup.b.guest_phone }}</div>
      </div>
      <div class="olom-tl-popup-foot">
        <router-link :to="'/prenotazione/' + popup.b.id" class="olom-btn olom-btn-primary olom-btn-sm">Apri dettagli</router-link>
      </div>
    </div>

    <!-- Legend -->
    <div class="olom-cal-legend">
      <span class="olom-cal-legend-item"><i class="olom-cal-lswatch" style="background:var(--olom-primary)"></i> Confermata</span>
      <span class="olom-cal-legend-item"><i class="olom-cal-lswatch olom-cal-ls-pending"></i> In attesa</span>
      <span class="olom-cal-legend-item"><i class="olom-cal-lswatch" style="background:#10B981"></i> Completata</span>
      <span class="olom-cal-legend-item"><i class="olom-cal-lswatch" style="background:#D1D5DB"></i> Annullata</span>
      <span class="olom-cal-legend-item olom-tl-legend-drag">
        <svg width="14" height="14" viewBox="0 0 16 16" fill="#9CA3AF"><path d="M6 2h4v4h4v4h-4v4H6v-4H2V6h4z"/></svg>
        Trascina per spostare
      </span>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onBeforeUnmount, nextTick } from 'vue';

const props = defineProps({
  bookings: { type: Array, default: () => [] },
  services: { type: Array, default: () => [] },
});

const emit = defineEmits(['month-change', 'booking-moved']);

const cellW = 48;
const scrollRef = ref(null);
const popup = ref(null);
const drag = ref(null);

const today = new Date();
const currentYear = ref(today.getFullYear());
const currentMonth = ref(today.getMonth());

const monthNames = [
  'Gennaio','Febbraio','Marzo','Aprile','Maggio','Giugno',
  'Luglio','Agosto','Settembre','Ottobre','Novembre','Dicembre'
];

const monthLabel = computed(() => `${monthNames[currentMonth.value]} ${currentYear.value}`);

/* ── Navigation ── */

function changeMonth(delta) {
  let m = currentMonth.value + delta;
  let y = currentYear.value;
  if (m < 0) { m = 11; y--; }
  if (m > 11) { m = 0; y++; }
  currentMonth.value = m;
  currentYear.value = y;
  popup.value = null;
}

function goToday() {
  currentYear.value = today.getFullYear();
  currentMonth.value = today.getMonth();
  // Scroll to today
  nextTick(() => {
    if (scrollRef.value && todayIdx.value >= 0) {
      const targetX = todayIdx.value * cellW - scrollRef.value.clientWidth / 2 + cellW / 2;
      scrollRef.value.scrollLeft = Math.max(0, targetX);
    }
  });
}

watch([currentYear, currentMonth], ([y, m]) => {
  emit('month-change', `${y}-${String(m + 1).padStart(2, '0')}`);
}, { immediate: true });

/* ── Days computation ── */

const todayStr = fmt(today);

const days = computed(() => {
  const year = currentYear.value;
  const month = currentMonth.value;
  const daysInMonth = new Date(year, month + 1, 0).getDate();
  const result = [];

  for (let d = 1; d <= daysInMonth; d++) {
    const date = new Date(year, month, d);
    const dateStr = fmt(date);
    const dow = date.getDay();
    result.push({
      date: dateStr,
      num: d,
      dayShort: ['Do','Lu','Ma','Me','Gi','Ve','Sa'][dow],
      isToday: dateStr === todayStr,
      isWe: dow === 0 || dow === 6,
    });
  }
  return result;
});

const todayIdx = computed(() => days.value.findIndex(d => d.isToday));

/* ── Bars per service ── */

function barsForService(serviceId) {
  if (!days.value.length) return [];
  const firstDate = days.value[0].date;
  const lastDate = days.value[days.value.length - 1].date;

  return props.bookings.filter(b =>
    b.service_id === serviceId &&
    b.checkin_date <= lastDate &&
    b.checkout_date > firstDate
  );
}

/* ── Bar style ── */

function barStyle(bar) {
  const first = days.value[0].date;
  const totalDays = days.value.length;

  const startIdx = Math.max(0, daysDiff(first, bar.checkin_date));
  const endIdx = Math.min(totalDays, daysDiff(first, bar.checkout_date));

  const left = startIdx * cellW + 2;
  const width = Math.max((endIdx - startIdx) * cellW - 4, 20);

  const isClippedLeft = bar.checkin_date < first;
  const isClippedRight = daysDiff(first, bar.checkout_date) > totalDays;

  const style = {
    left: left + 'px',
    width: width + 'px',
    '--bar-color': bar.service_color || '#6366F1',
    borderRadius: (isClippedLeft ? '0' : '6px') + ' ' + (isClippedRight ? '0' : '6px') + ' ' + (isClippedRight ? '0' : '6px') + ' ' + (isClippedLeft ? '0' : '6px'),
  };

  if (drag.value && drag.value.barId === bar.id) {
    style.transform = `translateX(${drag.value.offsetX}px)`;
    style.zIndex = '20';
    style.boxShadow = '0 4px 12px rgba(0,0,0,0.2)';
    style.cursor = 'grabbing';
  }

  return style;
}

/* ── Popup ── */

function openPopup(bar, event) {
  if (drag.value && Math.abs(drag.value.offsetX) > 5) return;

  const rect = event.currentTarget.getBoundingClientRect();
  const x = Math.min(rect.left, window.innerWidth - 310);
  const y = rect.bottom + 8;

  popup.value = {
    b: bar,
    x: Math.max(8, x),
    y: y > window.innerHeight - 280 ? rect.top - 280 : y,
  };
}

const popupStyle = computed(() => {
  if (!popup.value) return {};
  return {
    position: 'fixed',
    left: popup.value.x + 'px',
    top: popup.value.y + 'px',
    zIndex: 1000,
  };
});

/* ── Drag & Drop ── */

function onDragStart(e, bar) {
  if (bar.status === 'cancelled' || bar.status === 'completed') return;

  drag.value = {
    barId: bar.id,
    booking: bar,
    startX: e.clientX,
    offsetX: 0,
  };
  popup.value = null;

  document.addEventListener('mousemove', onDragMove);
  document.addEventListener('mouseup', onDragEnd);
}

function onDragMove(e) {
  if (!drag.value) return;
  drag.value.offsetX = e.clientX - drag.value.startX;
}

function onDragEnd() {
  if (!drag.value) return;

  const deltaDays = Math.round(drag.value.offsetX / cellW);
  if (deltaDays !== 0) {
    const b = drag.value.booking;
    const newCheckin = addDays(b.checkin_date, deltaDays);
    const newCheckout = addDays(b.checkout_date, deltaDays);
    emit('booking-moved', {
      id: b.id,
      checkin_date: newCheckin,
      checkout_date: newCheckout,
    });
  }

  drag.value = null;
  document.removeEventListener('mousemove', onDragMove);
  document.removeEventListener('mouseup', onDragEnd);
}

onBeforeUnmount(() => {
  document.removeEventListener('mousemove', onDragMove);
  document.removeEventListener('mouseup', onDragEnd);
});

/* ── Helpers ── */

function fmt(d) {
  return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
}

function parseDate(s) {
  const [y, m, d] = s.split('-').map(Number);
  return new Date(y, m - 1, d);
}

function daysDiff(a, b) {
  return Math.round((parseDate(b) - parseDate(a)) / 86400000);
}

function addDays(dateStr, n) {
  const d = parseDate(dateStr);
  d.setDate(d.getDate() + n);
  return fmt(d);
}

function fmtDateHuman(d) {
  return new Date(d + 'T00:00:00').toLocaleDateString('it-IT', { day: '2-digit', month: 'short', year: 'numeric' });
}

function statusLabel(s) {
  return { pending: 'In attesa', confirmed: 'Confermata', cancelled: 'Annullata', completed: 'Completata' }[s] || s;
}

/* ── Scroll to today on mount ── */

onMounted(() => {
  nextTick(() => {
    if (scrollRef.value && todayIdx.value >= 0) {
      const targetX = todayIdx.value * cellW - scrollRef.value.clientWidth / 2 + cellW / 2;
      scrollRef.value.scrollLeft = Math.max(0, targetX);
    }
  });
});
</script>
