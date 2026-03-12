<template>
  <div style="padding:12px">

    <!-- ═══ MODAL preview ═══ -->
    <div v-if="s.mode === 'modal'" style="display:flex;flex-direction:column;align-items:center">
      <!-- Trigger -->
      <div style="display:inline-flex;align-items:center;justify-content:center;cursor:pointer" :style="compactStyle">
        <svg :style="{ width: iconSize + 'px', height: iconSize + 'px' }" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="8.5" cy="8.5" r="5.5"/><line x1="13" y1="13" x2="17" y2="17"/>
        </svg>
      </div>
      <!-- Preview modale -->
      <div style="margin-top:12px;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.15);width:100%" :style="{ maxWidth: (parseInt(s.modal_width) || 560) + 'px', background: s.results_bg || '#fff', borderRadius: resultsRadius + 'px' }">
        <!-- Input modale -->
        <div style="display:flex;align-items:center;padding:0 16px;border-bottom:1px solid" :style="{ borderColor: s.results_border_color || '#e5e7eb', background: s.input_bg || '#fff' }">
          <div :style="{ color: s.icon_color || '#9ca3af' }">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="8.5" cy="8.5" r="5.5"/><line x1="13" y1="13" x2="17" y2="17"/></svg>
          </div>
          <div style="flex:1;padding:16px 12px;font-size:18px" :style="{ color: (s.input_color || '#374151') + '88' }">{{ displayPlaceholder }}</div>
          <div style="color:#d1d5db;font-size:18px">&times;</div>
        </div>
        <!-- Risultati -->
        <div :style="resultsGridStyle">
          <div
            v-for="(item, i) in fakeResults"
            :key="'m'+i"
            style="display:flex;align-items:center;gap:10px;padding:10px 16px"
            :style="i === 0 ? { background: s.item_hover_bg || '#f3f4f6' } : {}"
          >
            <div v-if="s.show_thumbnail !== false" :style="thumbStyle">
              <svg style="width:100%;height:100%;color:#d1d5db" viewBox="0 0 20 20" fill="currentColor"><rect width="20" height="20" rx="2" fill="#e5e7eb"/><path d="M4 14l3-3 2 2 4-5 3 3.5V14H4z" fill="#d1d5db"/></svg>
            </div>
            <div style="flex:1;min-width:0">
              <div style="font-size:14px;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis" :style="{ color: s.title_color || '#374151' }">{{ item.title }}</div>
              <div v-if="s.show_excerpt !== false" style="font-size:12px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-top:2px" :style="{ color: s.excerpt_color || '#6b7280' }">{{ item.excerpt }}</div>
            </div>
          </div>
        </div>
        <!-- Footer -->
        <div v-if="s.show_all_url" style="padding:8px 12px;font-size:12px;text-align:center;border-top:1px solid;font-weight:600" :style="{ borderColor: s.results_border_color || '#e5e7eb', color: '#6366F1' }" data-olo-editable="show_all_text">
          {{ s.show_all_text || 'Vedi tutti i risultati' }}
        </div>
      </div>
    </div>

    <!-- ═══ COMPACT ═══ -->
    <div v-else-if="s.mode === 'compact'" style="display:flex;justify-content:flex-end;position:relative">
      <div style="display:flex;align-items:center;margin-right:8px;overflow:hidden;width:200px" :style="fieldPreviewStyle">
        <div style="padding-left:10px;display:flex;align-items:center" :style="{ color: s.icon_color || '#9ca3af' }">
          <svg width="14" height="14" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="8.5" cy="8.5" r="5.5"/><line x1="13" y1="13" x2="17" y2="17"/></svg>
        </div>
        <div style="flex:1;padding:0 8px;font-size:13px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis" :style="{ color: (s.input_color || '#374151') + '88', lineHeight: inputH + 'px' }">{{ displayPlaceholder }}</div>
      </div>
      <div style="display:inline-flex;align-items:center;justify-content:center;cursor:pointer;flex-shrink:0" :style="compactStyle">
        <svg :style="{ width: iconSize + 'px', height: iconSize + 'px' }" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="8.5" cy="8.5" r="5.5"/><line x1="13" y1="13" x2="17" y2="17"/>
        </svg>
      </div>
      <div style="position:absolute;top:calc(100% + 6px);right:0;z-index:10;width:320px;overflow:hidden;box-shadow:0 10px 25px -5px rgba(0,0,0,.12)" :style="dropdownStyle">
        <div :style="resultsGridStyle">
          <div v-for="(item, i) in fakeResults" :key="'c'+i" style="display:flex;align-items:center;gap:10px;padding:8px 12px" :style="i === 0 ? { background: s.item_hover_bg || '#f3f4f6' } : {}">
            <div v-if="s.show_thumbnail !== false" :style="thumbStyle">
              <svg style="width:100%;height:100%;color:#d1d5db" viewBox="0 0 20 20" fill="currentColor"><rect width="20" height="20" rx="2" fill="#e5e7eb"/><path d="M4 14l3-3 2 2 4-5 3 3.5V14H4z" fill="#d1d5db"/></svg>
            </div>
            <div style="flex:1;min-width:0">
              <div style="font-size:13px;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis" :style="{ color: s.title_color || '#374151' }">{{ item.title }}</div>
              <div v-if="s.show_excerpt !== false" style="font-size:11px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-top:2px" :style="{ color: s.excerpt_color || '#6b7280' }">{{ item.excerpt }}</div>
            </div>
          </div>
        </div>
        <div v-if="s.show_all_url" style="padding:8px 12px;font-size:12px;text-align:center;border-top:1px solid;font-weight:600" :style="{ borderColor: s.results_border_color || '#e5e7eb', color: '#6366F1' }" data-olo-editable="show_all_text">
          {{ s.show_all_text || 'Vedi tutti i risultati' }}
        </div>
      </div>
    </div>

    <!-- ═══ INLINE (megamenu/sidebar) ═══ -->
    <div v-else-if="s.mode === 'inline'" style="position:relative">
      <div :style="inlineInputStyle">
        <div style="position:absolute;left:10px;top:50%;transform:translateY(-50%);display:flex;align-items:center;pointer-events:none" :style="{ color: s.icon_color || '#9ca3af' }">
          <svg width="14" height="14" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="8.5" cy="8.5" r="5.5"/><line x1="13" y1="13" x2="17" y2="17"/>
          </svg>
        </div>
        <div style="height:36px;line-height:36px;padding-left:30px;padding-right:24px;font-size:13px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis" :style="{ color: (s.input_color || '#374151') + '88' }">{{ displayPlaceholder }}</div>
        <div style="position:absolute;right:8px;top:50%;transform:translateY(-50%);color:#d1d5db;font-size:12px;line-height:1">&times;</div>
      </div>
      <div style="margin-top:4px;overflow:hidden;border:1px solid;box-shadow:none" :style="{ borderColor: s.results_border_color || '#e5e7eb', background: s.results_bg || '#fff', borderRadius: '6px' }">
        <div v-for="(item, i) in fakeResults.slice(0, 3)" :key="'i'+i" style="display:flex;align-items:center;gap:8px;padding:6px 10px" :style="i === 0 ? { background: s.item_hover_bg || '#f3f4f6' } : {}">
          <div v-if="s.show_thumbnail !== false" :style="{ ...thumbStyle, width: '32px', height: '32px', minWidth: '32px' }">
            <svg style="width:100%;height:100%;color:#d1d5db" viewBox="0 0 20 20" fill="currentColor"><rect width="20" height="20" rx="2" fill="#e5e7eb"/><path d="M4 14l3-3 2 2 4-5 3 3.5V14H4z" fill="#d1d5db"/></svg>
          </div>
          <div style="flex:1;min-width:0">
            <div style="font-size:12px;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis" :style="{ color: s.title_color || '#374151' }">{{ item.title }}</div>
          </div>
        </div>
      </div>
    </div>

    <!-- ═══ EXPANDED (default) ═══ -->
    <div v-else style="position:relative">
      <div :style="inputWrapStyle">
        <div style="position:absolute;left:12px;top:50%;transform:translateY(-50%);display:flex;align-items:center;pointer-events:none" :style="{ color: s.icon_color || '#9ca3af' }">
          <svg width="16" height="16" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="8.5" cy="8.5" r="5.5"/><line x1="13" y1="13" x2="17" y2="17"/>
          </svg>
        </div>
        <div :style="inputStyle">{{ displayPlaceholder }}</div>
        <div style="position:absolute;right:10px;top:50%;transform:translateY(-50%);color:#d1d5db;font-size:14px;line-height:1">&times;</div>
      </div>
      <div style="margin-top:6px;overflow:hidden;box-shadow:0 10px 25px -5px rgba(0,0,0,.12);min-width:340px" :style="dropdownStyle">
        <div :style="resultsGridStyle">
          <div v-for="(item, i) in fakeResults" :key="'e'+i" style="display:flex;align-items:center;gap:10px;padding:8px 12px" :style="i === 0 ? { background: s.item_hover_bg || '#f3f4f6' } : {}">
            <div v-if="s.show_thumbnail !== false" :style="thumbStyle">
              <svg style="width:100%;height:100%;color:#d1d5db" viewBox="0 0 20 20" fill="currentColor"><rect width="20" height="20" rx="2" fill="#e5e7eb"/><path d="M4 14l3-3 2 2 4-5 3 3.5V14H4z" fill="#d1d5db"/></svg>
            </div>
            <div style="flex:1;min-width:0">
              <div style="font-size:13px;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis" :style="{ color: s.title_color || '#374151' }">{{ item.title }}</div>
              <div v-if="s.show_excerpt !== false" style="font-size:11px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-top:2px" :style="{ color: s.excerpt_color || '#6b7280' }">{{ item.excerpt }}</div>
            </div>
          </div>
        </div>
        <div v-if="s.show_all_url" style="padding:8px 12px;font-size:12px;text-align:center;border-top:1px solid;font-weight:600" :style="{ borderColor: s.results_border_color || '#e5e7eb', color: '#6366F1' }" data-olo-editable="show_all_text">
          {{ s.show_all_text || 'Vedi tutti i risultati' }}
        </div>
      </div>
    </div>

    <!-- Animated placeholder words preview -->
    <div v-if="s.animated_placeholder && animWords.length" style="margin-top:8px;display:flex;gap:4px;flex-wrap:wrap;justify-content:center;">
      <span v-for="(w, i) in animWords" :key="i" style="font-size:9px;padding:1px 5px;border-radius:3px;background:rgba(99,102,241,0.1);color:#6366F1;">{{ w }}</span>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, onMounted, onBeforeUnmount, watch } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  placeholder: 'Cerca...',
  mode: 'expanded',
  modal_width: '560',
  backdrop_color: 'rgba(0,0,0,0.5)',
  min_chars: '2',
  debounce_ms: '300',
  animated_placeholder: false,
  placeholder_words: '',
  max_results: '10',
  results_columns: '1',
  show_all_url: '',
  show_all_text: 'Vedi tutti i risultati',
  show_thumbnail: true,
  show_excerpt: true,
  title_only: false,
  no_results_text: 'Nessun risultato trovato',
  post_types: 'post,page',
  taxonomy_filter: '',
  taxonomy_terms: '',
  exclude_terms: '',
  input_bg: '#ffffff',
  input_color: '',
  icon_color: '',
  input_font_size: '14',
  input_height: '44',
  input_border_color: '#e5e7eb',
  input_border_radius: '8',
  focus_border_color: '#6366f1',
  results_bg: '#ffffff',
  results_border_color: '',
  item_hover_bg: '',
  title_color: '',
  excerpt_color: '#6b7280',
  results_max_height: '400',
  results_border_radius: '10',
  thumb_width: '48',
  thumb_height: '48',
  thumb_radius: '6',
};

const s = computed(() => ({ ...defaults, ...props.settings }));

const fakeResults = computed(() => {
  const pt = s.value.post_types || 'post,page';
  const types = pt.split(',').map(t => t.trim());
  const isService = types.includes('olo_service') || types.includes('service');
  if (isService) {
    return [
      { title: 'Hotel Belvedere', excerpt: 'Struttura 4 stelle nel cuore delle Dolomiti...' },
      { title: 'Residence Al Sole', excerpt: 'Appartamenti vacanza con vista panoramica...' },
      { title: 'Garni La Rosa', excerpt: 'Accoglienza familiare in posizione centrale...' },
      { title: 'Camping Lago Blu', excerpt: 'Immerso nella natura a due passi dal lago...' },
    ];
  }
  return [
    { title: 'Come iniziare con il progetto', excerpt: 'Guida passo-passo per configurare e personalizzare...' },
    { title: 'Le novità dell\'ultimo aggiornamento', excerpt: 'Scopri le funzionalità introdotte nella versione recente...' },
    { title: 'Domande frequenti e soluzioni', excerpt: 'Risposte ai problemi più comuni riscontrati dagli utenti...' },
    { title: 'Best practice e consigli utili', excerpt: 'Suggerimenti per ottenere il massimo dal tuo sito...' },
  ];
});

// Animated placeholder
const animWords = computed(() =>
  (s.value.placeholder_words || '').split('\n').map(w => w.trim()).filter(Boolean)
);

const currentPlaceholder = ref('');
let animTimer = null;
let wordIdx = 0;

function startPlaceholderAnim() {
  stopPlaceholderAnim();
  if (!s.value.animated_placeholder || !animWords.value.length) {
    currentPlaceholder.value = '';
    return;
  }
  wordIdx = 0;
  currentPlaceholder.value = animWords.value[0];
  animTimer = setInterval(() => {
    wordIdx = (wordIdx + 1) % animWords.value.length;
    currentPlaceholder.value = animWords.value[wordIdx];
  }, 2500);
}
function stopPlaceholderAnim() {
  if (animTimer) { clearInterval(animTimer); animTimer = null; }
}

onMounted(startPlaceholderAnim);
onBeforeUnmount(stopPlaceholderAnim);
watch(() => [s.value.animated_placeholder, s.value.placeholder_words], startPlaceholderAnim);

const displayPlaceholder = computed(() => {
  if (s.value.animated_placeholder && currentPlaceholder.value) {
    return currentPlaceholder.value;
  }
  return s.value.placeholder || 'Cerca...';
});

const iconSize = computed(() => Math.round(parseInt(s.value.input_font_size || 14) * 1.2));
const inputH = computed(() => parseInt(s.value.input_height) || 44);
const inputRadius = computed(() => ((v => isNaN(v) ? 8 : v)(parseInt(s.value.input_border_radius))) + 'px');
const resultsRadius = computed(() => (v => isNaN(v) ? 10 : v)(parseInt(s.value.results_border_radius)));
const cols = computed(() => Math.max(1, parseInt(s.value.results_columns) || 1));

const compactStyle = computed(() => ({
  width: inputH.value + 'px',
  height: inputH.value + 'px',
  borderRadius: '50%',
  background: s.value.input_bg || '#ffffff',
  color: s.value.icon_color || '#9ca3af',
  border: '1px solid ' + (s.value.input_border_color || '#e5e7eb'),
}));

const fieldPreviewStyle = computed(() => ({
  background: s.value.input_bg || '#ffffff',
  border: '1px solid ' + (s.value.input_border_color || '#e5e7eb'),
  height: inputH.value + 'px',
  borderRadius: inputRadius.value,
}));

const inputWrapStyle = computed(() => ({
  position: 'relative',
  borderRadius: inputRadius.value,
  border: '1px solid ' + (s.value.input_border_color || '#e5e7eb'),
  background: s.value.input_bg || '#ffffff',
  overflow: 'hidden',
}));

const inlineInputStyle = computed(() => ({
  position: 'relative',
  borderRadius: '6px',
  border: '1px solid ' + (s.value.input_border_color || '#e5e7eb'),
  background: s.value.input_bg || '#ffffff',
  overflow: 'hidden',
}));

const inputStyle = computed(() => ({
  height: inputH.value + 'px',
  lineHeight: inputH.value + 'px',
  paddingLeft: '36px',
  paddingRight: '28px',
  fontSize: (parseInt(s.value.input_font_size) || 14) + 'px',
  color: s.value.input_color ? s.value.input_color + '88' : '#9ca3af',
  whiteSpace: 'nowrap',
  overflow: 'hidden',
  textOverflow: 'ellipsis',
}));

const thumbStyle = computed(() => {
  const w = parseInt(s.value.thumb_width) || 48;
  const h = parseInt(s.value.thumb_height) || 48;
  const rad = (v => isNaN(v) ? 6 : v)(parseInt(s.value.thumb_radius));
  return {
    width: w + 'px',
    height: h + 'px',
    minWidth: w + 'px',
    borderRadius: rad + 'px',
    overflow: 'hidden',
    background: '#f3f4f6',
    flexShrink: '0',
  };
});

const dropdownStyle = computed(() => ({
  background: s.value.results_bg || '#ffffff',
  border: '1px solid ' + (s.value.results_border_color || '#e5e7eb'),
  maxHeight: (parseInt(s.value.results_max_height) || 400) + 'px',
  overflow: 'auto',
  borderRadius: resultsRadius.value + 'px',
}));

const resultsGridStyle = computed(() => {
  if (cols.value > 1) {
    return {
      display: 'grid',
      gridTemplateColumns: `repeat(${cols.value}, 1fr)`,
      gap: '1px',
      background: s.value.results_border_color || '#e5e7eb',
    };
  }
  return {};
});
</script>
