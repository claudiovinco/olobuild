<template>
  <div :style="wrapStyle">
    <!-- Badge evergreen -->
    <div v-if="isEvergreen" style="position:absolute;top:6px;right:8px;background:var(--olo-color-primary, #6366F1);color:#fff;font-size:10px;padding:2px 6px;border-radius:4px;z-index:2;">
      &#9851; Evergreen
    </div>
    <template v-if="!expired">
      <!-- Modalità inline -->
      <template v-if="isInline">
        <template v-for="(unit, i) in visibleUnits" :key="unit.key">
          <span v-if="i > 0" :style="{ ...separatorStyle, margin: '0 4px' }">{{ s.separator || ':' }}</span>
          <span :style="{ ...numberStyle, fontSize: Math.max(16, Math.round((parseInt(s.number_font_size) || 48) * 0.55)) + 'px' }">{{ String(timeValues[unit.key] || 0).padStart(2, '0') }}</span>
          <span :style="{ ...labelStyle, marginLeft: '2px', marginTop: '0', textTransform: 'lowercase' }">{{ unit.shortLabel }}</span>
        </template>
      </template>
      <!-- Modalità blocco (default) -->
      <template v-else>
        <template v-for="(unit, i) in visibleUnits" :key="unit.key">
          <span v-if="i > 0 && s.separator" :style="separatorStyle">{{ s.separator }}</span>
          <div class="mb-text-center" :style="{ minWidth: (parseInt(s.item_min_width) || 70) + 'px' }">
            <div :style="numberStyle">
              {{ String(timeValues[unit.key] || 0).padStart(2, '0') }}
            </div>
            <div :style="labelStyle">{{ unit.label }}</div>
          </div>
        </template>
      </template>
    </template>
    <div v-else class="mb-text-xl mb-py-4">{{ expireDisplay }}</div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';

const defaults = {
  countdown_style: 'custom',
  countdown_type: 'date',
  evergreen_hours: '0',
  evergreen_minutes: '30',
  evergreen_loop: false,
  expire_action: 'none',
  expire_redirect_url: '',
  expire_message: 'Tempo scaduto!',
  display_mode: 'block',
  target_date: '2026-12-31T23:59',
  show_days: true,
  show_hours: true,
  show_minutes: true,
  show_seconds: true,
  expired_message: "L'evento è iniziato!",
  label_days: 'Giorni',
  label_hours: 'Ore',
  label_minutes: 'Minuti',
  label_seconds: 'Secondi',
  separator: ':',
  bg_color: 'var(--olo-color-background, #FFFFFF)',
  text_color: 'var(--olo-color-text, #374151)',
  accent_color: 'var(--olo-color-primary, #6366F1)',
  number_font_size: '48',
  number_font_weight: '700',
  label_font_size: '12',
  label_font_weight: '500',
  separator_font_size: '32',
  item_min_width: '70',
  padding: '32',
  border_width: '0',
  border_color: '#e5e7eb',
  border_radius: { tl: 0, tr: 0, br: 0, bl: 0 },
};

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const s = computed(() => ({ ...defaults, ...props.settings }));

const timeValues = ref({ days: 0, hours: 0, minutes: 0, seconds: 0 });
const expired = ref(false);
let timer = null;

const isEvergreen = computed(() => s.value.countdown_type === 'evergreen');
const isInline = computed(() => s.value.display_mode === 'inline');

const expireDisplay = computed(() => {
  const action = s.value.expire_action;
  if (action === 'message') return s.value.expire_message || 'Tempo scaduto!';
  if (action === 'hide') return '';
  return s.value.expired_message;
});

const visibleUnits = computed(() => {
  const units = [];
  if (s.value.show_days !== false) units.push({ key: 'days', label: s.value.label_days, shortLabel: 'd' });
  if (s.value.show_hours !== false) units.push({ key: 'hours', label: s.value.label_hours, shortLabel: 'h' });
  if (s.value.show_minutes !== false) units.push({ key: 'minutes', label: s.value.label_minutes, shortLabel: 'm' });
  if (s.value.show_seconds !== false) units.push({ key: 'seconds', label: s.value.label_seconds, shortLabel: 's' });
  return units;
});

const wrapStyle = computed(() => {
  const pad = parseInt(s.value.padding) || 32;
  const bw = parseInt(s.value.border_width) || 0;
  const br = s.value.border_radius || {};
  const inline = isInline.value;
  const style = {
    position: 'relative',
    display: 'flex',
    gap: inline ? '4px' : '16px',
    justifyContent: 'center',
    alignItems: inline ? 'baseline' : 'center',
    flexWrap: inline ? 'nowrap' : 'wrap',
    padding: inline ? Math.round(pad / 2) + 'px' : pad + 'px ' + Math.round(pad / 2) + 'px',
    background: s.value.bg_color,
    color: s.value.text_color,
    borderRadius: `${br.tl || 0}px ${br.tr || 0}px ${br.br || 0}px ${br.bl || 0}px`,
  };
  if (bw > 0) {
    style.border = bw + 'px solid ' + (s.value.border_color || '#e5e7eb');
  }
  return style;
});

const numberStyle = computed(() => ({
  fontSize: (parseInt(s.value.number_font_size) || 48) + 'px',
  fontWeight: s.value.number_font_weight || '700',
  lineHeight: 1.1,
  color: s.value.accent_color,
}));

const labelStyle = computed(() => ({
  fontSize: (parseInt(s.value.label_font_size) || 12) + 'px',
  fontWeight: s.value.label_font_weight || '500',
  opacity: 0.7,
  marginTop: '4px',
  textTransform: 'uppercase',
  letterSpacing: '0.05em',
}));

const separatorStyle = computed(() => ({
  fontSize: (parseInt(s.value.separator_font_size) || 32) + 'px',
  fontWeight: '700',
  opacity: 0.5,
}));

// Per evergreen nell'editor: mostra sempre il tempo configurato (anteprima statica)
function getEvergreenTarget() {
  const h = parseInt(s.value.evergreen_hours) || 0;
  const m = parseInt(s.value.evergreen_minutes) || 0;
  return Date.now() + (h * 3600000) + (m * 60000);
}

function tick() {
  let diff;
  if (s.value.countdown_type === 'evergreen') {
    // Nell'editor mostra il tempo configurato come anteprima statica
    const h = parseInt(s.value.evergreen_hours) || 0;
    const m = parseInt(s.value.evergreen_minutes) || 0;
    diff = (h * 3600000) + (m * 60000);
  } else {
    const target = new Date(s.value.target_date).getTime();
    diff = target - Date.now();
  }
  if (diff <= 0) {
    expired.value = true;
    if (s.value.expire_action === 'hide') {
      // Nell'editor mostra un placeholder
    }
    clearInterval(timer);
    return;
  }
  expired.value = false;
  timeValues.value = {
    days: Math.floor(diff / 86400000),
    hours: Math.floor((diff % 86400000) / 3600000),
    minutes: Math.floor((diff % 3600000) / 60000),
    seconds: Math.floor((diff % 60000) / 1000),
  };
}

onMounted(() => {
  tick();
  timer = setInterval(tick, 1000);
});

onUnmounted(() => {
  clearInterval(timer);
});

watch(() => s.value.target_date, () => {
  expired.value = false;
  tick();
});

watch(() => [s.value.countdown_type, s.value.evergreen_hours, s.value.evergreen_minutes], () => {
  expired.value = false;
  tick();
});
</script>
