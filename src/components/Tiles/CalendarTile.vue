<template>
  <div class="olo-calendar-preview">
    <!-- Category filter -->
    <div v-if="s.show_category_filter" class="ocp-filters" :class="'ocp-filters--' + (s.filter_style || 'pills')"
         :style="filterWrapStyle">
      <span class="ocp-filter-item ocp-filter-active"
            :class="{ 'ocp-filter-pill': isPills, 'ocp-filter-check': isCheckboxes }">
        <input v-if="isCheckboxes" type="checkbox" checked disabled />
        <span v-if="isPills || isMinimal" class="ocp-filter-dot"
              :style="{ background: 'var(--olo-color-primary, #6366F1)' }"></span>
        <span>Tutti</span>
      </span>
      <span v-for="cat in previewCats" :key="cat.name" class="ocp-filter-item ocp-filter-active"
            :class="{ 'ocp-filter-pill': isPills, 'ocp-filter-check': isCheckboxes }">
        <input v-if="isCheckboxes" type="checkbox" checked disabled :style="{ accentColor: cat.color }" />
        <span v-if="isPills || isMinimal" class="ocp-filter-dot" :style="{ background: cat.color }"></span>
        <span>{{ cat.name }}</span>
        <span class="ocp-filter-count">{{ cat.count }}</span>
      </span>
    </div>

    <!-- Toolbar -->
    <div v-if="s.show_toolbar" class="ocp-toolbar" :style="toolbarContainerStyle">
      <div class="ocp-toolbar-left">
        <span class="ocp-btn ocp-btn-nav" :style="btnStyle">&lsaquo;</span>
        <span class="ocp-btn ocp-btn-nav" :style="btnStyle">&rsaquo;</span>
        <span v-if="s.show_today_btn" class="ocp-btn" :style="btnStyle">Oggi</span>
      </div>
      <div class="ocp-toolbar-center">
        <strong :style="titleStyle">{{ currentMonthLabel }}</strong>
      </div>
      <div class="ocp-toolbar-right">
        <span v-if="s.show_month_view" class="ocp-btn"
              :style="s.default_view === 'dayGridMonth' ? btnActiveStyle : btnStyle">Mese</span>
        <span v-if="s.show_week_view" class="ocp-btn"
              :style="s.default_view === 'timeGridWeek' ? btnActiveStyle : btnStyle">Settimana</span>
        <span v-if="s.show_day_view" class="ocp-btn"
              :style="s.default_view === 'timeGridDay' ? btnActiveStyle : btnStyle">Giorno</span>
        <span v-if="s.show_list_view" class="ocp-btn"
              :style="s.default_view === 'listMonth' ? btnActiveStyle : btnStyle">Lista</span>
      </div>
    </div>

    <!-- Calendar Grid -->
    <div v-if="isGridView" class="ocp-grid" :style="gridBorderStyle">
      <div class="ocp-header-row">
        <div v-for="day in dayHeaders" :key="day" class="ocp-header-cell" :style="headerStyle">
          {{ day }}
        </div>
      </div>
      <div v-for="(week, wi) in weeks" :key="wi" class="ocp-week-row">
        <div v-for="(d, di) in week" :key="di"
             class="ocp-day-cell"
             :class="{ 'ocp-today': d.isToday, 'ocp-other': d.isOther }"
             :style="d.isToday ? todayStyle : cellStyle">
          <span class="ocp-day-num" :style="dayNumStyle">{{ d.day }}</span>
          <div v-if="d.events.length" class="ocp-events">
            <div v-for="(ev, ei) in d.events" :key="ei" class="ocp-event" :style="eventStyle(ev)">
              {{ ev.title }}
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- List view preview -->
    <div v-else class="ocp-list">
      <div v-for="(ev, i) in sampleEvents" :key="i" class="ocp-list-item">
        <div class="ocp-list-dot" :style="{ backgroundColor: ev.color }"></div>
        <div class="ocp-list-date">{{ ev.dateLabel }}</div>
        <div class="ocp-list-title">{{ ev.title }}</div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  default_view: 'dayGridMonth',
  show_toolbar: true,
  show_today_btn: true,
  show_month_view: true,
  show_week_view: true,
  show_day_view: true,
  show_list_view: true,
  show_category_filter: true,
  filter_style: 'pills',
  filter_gap: '8',
  filter_margin_bottom: '16',
  toolbar_bg: '',
  toolbar_border_color: '',
  toolbar_border_width: '0',
  toolbar_padding_x: '4',
  toolbar_padding_y: '10',
  toolbar_margin_bottom: '0',
  toolbar_radius: '0',
  toolbar_title_size: '20',
  toolbar_title_weight: '600',
  toolbar_title_color: '',
  toolbar_title_transform: 'capitalize',
  toolbar_btn_bg: '',
  toolbar_btn_color: '',
  toolbar_btn_active_bg: '',
  toolbar_btn_active_color: '',
  toolbar_btn_radius: '8',
  toolbar_btn_padding_x: '14',
  toolbar_btn_padding_y: '6',
  toolbar_btn_font_size: '13',
  toolbar_btn_font_weight: '500',
  toolbar_btn_text_transform: 'capitalize',
  toolbar_btn_border_width: '0',
  toolbar_btn_border_color: '',
  toolbar_btn_shadow: false,
  header_bg: '',
  header_color: '',
  today_bg: '',
  day_number_size: '13',
  border_color: '',
  event_radius: '4',
  event_font_size: '12',
};
const s = computed(() => ({ ...defaults, ...props.settings }));

const isPills = computed(() => (s.value.filter_style || 'pills') === 'pills');
const isCheckboxes = computed(() => s.value.filter_style === 'checkboxes');
const isMinimal = computed(() => s.value.filter_style === 'minimal');

const previewCats = [
  { name: 'Conferenze', color: '#6366F1', count: 2 },
  { name: 'Workshop', color: '#10B981', count: 3 },
  { name: 'Social', color: '#EC4899', count: 1 },
];

const isGridView = computed(() => {
  return ['dayGridMonth', 'timeGridWeek', 'timeGridDay'].includes(s.value.default_view || 'dayGridMonth');
});

const dayHeaders = ['Lun', 'Mar', 'Mer', 'Gio', 'Ven', 'Sab', 'Dom'];
const now = new Date();

const currentMonthLabel = computed(() => {
  const months = ['Gennaio', 'Febbraio', 'Marzo', 'Aprile', 'Maggio', 'Giugno',
    'Luglio', 'Agosto', 'Settembre', 'Ottobre', 'Novembre', 'Dicembre'];
  return months[now.getMonth()] + ' ' + now.getFullYear();
});

const weeks = computed(() => {
  const year = now.getFullYear();
  const month = now.getMonth();
  const today = now.getDate();
  const firstDay = new Date(year, month, 1);
  let startWeekday = firstDay.getDay() - 1;
  if (startWeekday < 0) startWeekday = 6;
  const result = [];
  const current = new Date(firstDay);
  current.setDate(current.getDate() - startWeekday);
  for (let w = 0; w < 5; w++) {
    const week = [];
    for (let d = 0; d < 7; d++) {
      const isOther = current.getMonth() !== month;
      const isToday = !isOther && current.getDate() === today;
      week.push({ day: current.getDate(), isToday, isOther, events: getSampleEventsForDay(current.getDate(), isOther) });
      current.setDate(current.getDate() + 1);
    }
    result.push(week);
  }
  return result;
});

const sampleColors = ['#6366F1', '#EC4899', '#10B981', '#F59E0B'];
function getSampleEventsForDay(day, isOther) {
  if (isOther) return [];
  const events = [];
  if (day === 5) events.push({ title: 'Meeting team', color: sampleColors[0] });
  if (day === 5) events.push({ title: 'Pranzo azienda', color: sampleColors[1] });
  if (day === 12) events.push({ title: 'Workshop design', color: sampleColors[2] });
  if (day === 18) events.push({ title: 'Conferenza', color: sampleColors[3] });
  if (day === 22) events.push({ title: 'Evento lancio', color: sampleColors[0] });
  if (day === 28) events.push({ title: 'Review progetto', color: sampleColors[1] });
  return events;
}

const sampleEvents = computed(() => [
  { title: 'Meeting team', dateLabel: '5 Feb', color: sampleColors[0] },
  { title: 'Workshop design', dateLabel: '12 Feb', color: sampleColors[2] },
  { title: 'Conferenza', dateLabel: '18 Feb', color: sampleColors[3] },
  { title: 'Evento lancio', dateLabel: '22 Feb', color: sampleColors[0] },
  { title: 'Review progetto', dateLabel: '28 Feb', color: sampleColors[1] },
]);

// ── Styles ──

const filterWrapStyle = computed(() => ({
  gap: (parseInt(s.value.filter_gap) || 8) + 'px',
  marginBottom: (parseInt(s.value.filter_margin_bottom) || 16) + 'px',
}));

const toolbarContainerStyle = computed(() => {
  const style = {
    padding: (parseInt(s.value.toolbar_padding_y) || 10) + 'px ' + (parseInt(s.value.toolbar_padding_x) || 4) + 'px',
  };
  if (s.value.toolbar_bg) style.backgroundColor = s.value.toolbar_bg;
  if (s.value.toolbar_border_color && parseInt(s.value.toolbar_border_width)) {
    style.borderBottom = parseInt(s.value.toolbar_border_width) + 'px solid ' + s.value.toolbar_border_color;
  }
  if (parseInt(s.value.toolbar_margin_bottom)) style.marginBottom = parseInt(s.value.toolbar_margin_bottom) + 'px';
  if (parseInt(s.value.toolbar_radius)) style.borderRadius = parseInt(s.value.toolbar_radius) + 'px';
  return style;
});

const titleStyle = computed(() => ({
  fontSize: (parseInt(s.value.toolbar_title_size) || 20) + 'px',
  fontWeight: s.value.toolbar_title_weight || '600',
  color: s.value.toolbar_title_color || 'inherit',
  textTransform: s.value.toolbar_title_transform || 'capitalize',
}));

const btnRadius = computed(() => ((v => isNaN(v) ? 8 : v)(parseInt(s.value.toolbar_btn_radius))) + 'px');

const btnStyle = computed(() => {
  const st = {
    backgroundColor: s.value.toolbar_btn_bg || 'var(--olo-color-primary, #6366F1)',
    color: s.value.toolbar_btn_color || '#fff',
    borderRadius: btnRadius.value,
    padding: (parseInt(s.value.toolbar_btn_padding_y) || 6) + 'px ' + (parseInt(s.value.toolbar_btn_padding_x) || 14) + 'px',
    fontSize: (parseInt(s.value.toolbar_btn_font_size) || 13) + 'px',
    fontWeight: s.value.toolbar_btn_font_weight || '500',
    textTransform: s.value.toolbar_btn_text_transform || 'capitalize',
  };
  if (parseInt(s.value.toolbar_btn_border_width) && s.value.toolbar_btn_border_color) {
    st.border = parseInt(s.value.toolbar_btn_border_width) + 'px solid ' + s.value.toolbar_btn_border_color;
  }
  if (s.value.toolbar_btn_shadow) st.boxShadow = '0 2px 6px rgba(0,0,0,0.12)';
  return st;
});

const btnActiveStyle = computed(() => ({
  ...btnStyle.value,
  backgroundColor: s.value.toolbar_btn_active_bg || 'color-mix(in srgb, var(--olo-color-primary, #6366F1) 75%, black)',
  color: s.value.toolbar_btn_active_color || btnStyle.value.color,
  opacity: '0.8',
}));

const headerStyle = computed(() => ({
  backgroundColor: s.value.header_bg || '#f8f9fa',
  color: s.value.header_color || '#6b7280',
}));

const gridBorderStyle = computed(() => ({
  borderColor: s.value.border_color || 'rgba(128,128,128,0.12)',
}));

const cellStyle = computed(() => ({
  borderColor: s.value.border_color || 'rgba(128,128,128,0.12)',
}));

const todayStyle = computed(() => ({
  ...cellStyle.value,
  backgroundColor: s.value.today_bg || 'rgba(99,102,241,0.06)',
}));

const dayNumStyle = computed(() => ({
  fontSize: (parseInt(s.value.day_number_size) || 13) + 'px',
}));

function eventStyle(ev) {
  return {
    backgroundColor: ev.color,
    borderRadius: ((v => isNaN(v) ? 4 : v)(parseInt(s.value.event_radius))) + 'px',
    fontSize: (parseInt(s.value.event_font_size) || 12) + 'px',
  };
}
</script>

<style scoped>
.olo-calendar-preview {
  min-height: 300px;
  font-size: 13px;
}

/* Filter */
.ocp-filters {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
}

.ocp-filter-item {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  cursor: default;
  font-size: 12px;
  font-weight: 500;
}

.ocp-filter-pill {
  padding: 4px 12px;
  border-radius: 20px;
  background: #fff;
  border: 1px solid rgba(128,128,128,0.2);
  box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}

.ocp-filter-check {
  padding: 3px 8px;
}

.ocp-filter-check input {
  width: 14px;
  height: 14px;
  margin: 0;
}

.ocp-filter-dot {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  flex-shrink: 0;
}

.ocp-filter-count {
  font-size: 10px;
  opacity: 0.4;
}

/* Toolbar */
.ocp-toolbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 8px;
  flex-wrap: wrap;
}

.ocp-toolbar-left,
.ocp-toolbar-right {
  display: flex;
  gap: 3px;
}

.ocp-btn {
  display: inline-block;
  padding: 4px 10px;
  font-size: 11px;
  cursor: default;
  user-select: none;
  line-height: 1.4;
}

.ocp-btn-nav {
  padding: 4px 7px;
  font-weight: 700;
  font-size: 14px;
  line-height: 1;
}

/* Grid */
.ocp-grid {
  border: 1px solid rgba(128,128,128,0.12);
  border-radius: 4px;
  overflow: hidden;
}

.ocp-header-row {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
}

.ocp-header-cell {
  padding: 6px 4px;
  text-align: center;
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.04em;
}

.ocp-week-row {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
}

.ocp-day-cell {
  min-height: 50px;
  padding: 3px;
  border-top: 1px solid rgba(128,128,128,0.12);
  border-right: 1px solid rgba(128,128,128,0.12);
}

.ocp-day-cell:last-child { border-right: none; }
.ocp-other { opacity: 0.3; }

.ocp-day-num {
  font-weight: 500;
  color: #9ca3af;
}

.ocp-today .ocp-day-num {
  color: var(--olo-color-primary, #6366F1);
  font-weight: 700;
}

.ocp-events {
  display: flex;
  flex-direction: column;
  gap: 1px;
  margin-top: 1px;
}

.ocp-event {
  padding: 1px 3px;
  color: #fff;
  font-size: 9px;
  font-weight: 500;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

/* List */
.ocp-list-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px 8px;
  border-bottom: 1px solid rgba(128,128,128,0.1);
}

.ocp-list-dot {
  width: 10px;
  height: 10px;
  border-radius: 50%;
  flex-shrink: 0;
}

.ocp-list-date {
  font-size: 12px;
  color: #9ca3af;
  min-width: 60px;
}

.ocp-list-title {
  font-weight: 500;
}
</style>
