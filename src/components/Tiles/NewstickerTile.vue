<template>
  <div class="olo-newsticker-preview" :style="containerStyle">
    <!-- Label -->
    <div v-if="s.label_text" class="olo-newsticker-label" :style="labelStyle" data-olo-editable="label_text">
      {{ s.label_text }}
    </div>

    <!-- Ticker area -->
    <div class="olo-newsticker-content" :style="contentAreaStyle">
      <div class="olo-newsticker-item" v-if="currentItem" :key="currentIndex" :style="itemStyle">
        <span v-if="currentItem.badge" class="olo-newsticker-badge" :style="badgeStyle" :data-olo-editable="`items.${currentIndex}.badge`">{{ currentItem.badge }}</span>
        <span class="olo-newsticker-title" :data-olo-editable="`items.${currentIndex}.title`">{{ currentItem.title || 'Notizia...' }}</span>
      </div>
      <div v-else class="olo-newsticker-item" :style="itemStyle">
        <span class="olo-newsticker-title" style="opacity:0.5">{{ t('Aggiungi notizie...') }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onBeforeUnmount } from 'vue';
import { t } from '@/i18n';

const defaults = {
  items: [
    { id: 'nt-1', title: 'Nuova funzionalità disponibile per tutti gli utenti', url: '', badge: 'Novità' },
    { id: 'nt-2', title: 'Manutenzione programmata venerdì 21:00 - 23:00', url: '', badge: 'Avviso' },
    { id: 'nt-3', title: 'Aggiornamento versione 2.0 rilasciato con successo', url: '', badge: '' },
  ],
  label_text: 'Breaking',
  label_bg: '#dc2626',
  label_color: '#ffffff',
  bg_color: 'var(--olo-color-muted, #F3F4F6)',
  text_color: 'var(--olo-color-text, #374151)',
  speed: '3000',
  height: '42',
  separator: '|',
  auto_scroll: true,
  pause_on_hover: true,
};

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const s = computed(() => ({ ...defaults, ...props.settings }));

const items = computed(() => {
  const raw = s.value.items;
  if (Array.isArray(raw)) return raw;
  return [];
});

const currentIndex = ref(0);
let intervalId = null;

const currentItem = computed(() => {
  if (items.value.length === 0) return null;
  return items.value[currentIndex.value % items.value.length];
});

function startTicker() {
  stopTicker();
  if (!s.value.auto_scroll) return;
  if (items.value.length <= 1) return;
  const speed = Math.max(2000, parseInt(s.value.speed) || 3000);
  intervalId = setInterval(() => {
    currentIndex.value = (currentIndex.value + 1) % items.value.length;
  }, speed);
}

function stopTicker() {
  if (intervalId) {
    clearInterval(intervalId);
    intervalId = null;
  }
}

onMounted(() => startTicker());
onBeforeUnmount(() => stopTicker());

watch(() => [s.value.speed, s.value.auto_scroll, items.value.length], () => {
  startTicker();
});

const containerStyle = computed(() => ({
  display: 'flex',
  alignItems: 'center',
  background: s.value.bg_color || 'var(--olo-color-muted, #F3F4F6)',
  height: (parseInt(s.value.height) || 42) + 'px',
  overflow: 'hidden',
  borderRadius: '4px',
  fontFamily: 'inherit',
}));

const labelStyle = computed(() => ({
  background: s.value.label_bg || '#dc2626',
  color: s.value.label_color || '#ffffff',
  padding: '0 12px',
  height: '100%',
  display: 'flex',
  alignItems: 'center',
  fontWeight: '700',
  fontSize: '12px',
  textTransform: 'uppercase',
  letterSpacing: '1px',
  whiteSpace: 'nowrap',
  flexShrink: 0,
}));

const contentAreaStyle = computed(() => ({
  flex: 1,
  overflow: 'hidden',
  padding: '0 14px',
  minWidth: 0,
}));

const itemStyle = computed(() => ({
  color: s.value.text_color || 'var(--olo-color-text, #374151)',
  fontSize: '13px',
  whiteSpace: 'nowrap',
  overflow: 'hidden',
  textOverflow: 'ellipsis',
  display: 'flex',
  alignItems: 'center',
  gap: '8px',
}));

const badgeStyle = computed(() => ({
  background: 'rgba(255,255,255,0.15)',
  padding: '2px 8px',
  borderRadius: '3px',
  fontSize: '11px',
  fontWeight: '600',
  flexShrink: 0,
}));
</script>

<style scoped>
.olo-newsticker-preview {
  position: relative;
}
.olo-newsticker-item {
  animation: olo-nt-fadein 0.3s ease;
}
@keyframes olo-nt-fadein {
  from { opacity: 0; transform: translateY(8px); }
  to   { opacity: 1; transform: translateY(0); }
}
</style>
