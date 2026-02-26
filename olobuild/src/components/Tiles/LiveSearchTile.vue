<template>
  <div style="padding:12px">

    <!-- ═══ MODAL preview: icona trigger + anteprima modale ═══ -->
    <div v-if="s.mode === 'modal'" style="display:flex;flex-direction:column;align-items:center">
      <!-- Trigger -->
      <div style="display:inline-flex;align-items:center;justify-content:center;cursor:pointer" :style="compactStyle">
        <svg :style="{ width: iconSize + 'px', height: iconSize + 'px' }" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="8.5" cy="8.5" r="5.5"/><line x1="13" y1="13" x2="17" y2="17"/>
        </svg>
      </div>
      <!-- Preview modale -->
      <div style="margin-top:12px;border-radius:12px;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.15);width:100%" :style="{ maxWidth: (parseInt(s.modal_width || 560)) + 'px', background: s.results_bg || '#fff' }">
        <!-- Input modale -->
        <div style="display:flex;align-items:center;padding:0 16px;border-bottom:1px solid" :style="{ borderColor: s.results_border_color || '#e5e7eb', background: s.input_bg || '#fff' }">
          <div :style="{ color: s.icon_color || '#9ca3af' }">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="8.5" cy="8.5" r="5.5"/><line x1="13" y1="13" x2="17" y2="17"/></svg>
          </div>
          <div style="flex:1;padding:16px 12px;font-size:18px" :style="{ color: (s.input_color || '#374151') + '88' }">{{ s.placeholder || 'Cerca...' }}</div>
          <div style="color:#d1d5db;font-size:18px">&times;</div>
        </div>
        <!-- Risultati -->
        <div v-for="(item, i) in fakeResults" :key="'m'+i" style="display:flex;align-items:center;gap:10px;padding:10px 16px" :style="i === 0 ? { background: s.item_hover_bg || '#f3f4f6' } : {}">
          <div v-if="s.show_thumbnail !== false" :style="thumbStyle"><svg style="width:100%;height:100%;color:#d1d5db" viewBox="0 0 20 20" fill="currentColor"><rect width="20" height="20" rx="2" fill="#e5e7eb"/><path d="M4 14l3-3 2 2 4-5 3 3.5V14H4z" fill="#d1d5db"/></svg></div>
          <div style="flex:1;min-width:0">
            <div style="font-size:14px;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis" :style="{ color: s.title_color || '#111827' }">{{ item.title }}</div>
            <div style="font-size:12px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-top:2px" :style="{ color: s.excerpt_color || '#6b7280' }">{{ item.excerpt }}</div>
          </div>
        </div>
      </div>
    </div>

    <!-- ═══ COMPACT: icona a destra, campo si espande a sinistra ═══ -->
    <div v-else-if="s.mode === 'compact'" style="display:flex;justify-content:flex-end;position:relative">
      <div style="display:flex;align-items:center;margin-right:8px;border-radius:8px;overflow:hidden;width:200px" :style="fieldPreviewStyle">
        <div style="padding-left:10px;display:flex;align-items:center" :style="{ color: s.icon_color || '#9ca3af' }">
          <svg width="14" height="14" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="8.5" cy="8.5" r="5.5"/><line x1="13" y1="13" x2="17" y2="17"/></svg>
        </div>
        <div style="flex:1;padding:0 8px;font-size:13px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis" :style="{ color: (s.input_color || '#374151') + '88', lineHeight: (parseInt(s.input_height || 44)) + 'px' }">{{ s.placeholder || 'Cerca...' }}</div>
      </div>
      <div style="display:inline-flex;align-items:center;justify-content:center;cursor:pointer;flex-shrink:0" :style="compactStyle">
        <svg :style="{ width: iconSize + 'px', height: iconSize + 'px' }" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="8.5" cy="8.5" r="5.5"/><line x1="13" y1="13" x2="17" y2="17"/>
        </svg>
      </div>
      <div style="position:absolute;top:calc(100% + 6px);right:0;z-index:10;width:320px;border-radius:10px;overflow:hidden;box-shadow:0 10px 25px -5px rgba(0,0,0,.12)" :style="dropdownStyle">
        <div v-for="(item, i) in fakeResults" :key="'c'+i" style="display:flex;align-items:center;gap:10px;padding:8px 12px" :style="i === 0 ? { background: s.item_hover_bg || '#f3f4f6' } : {}">
          <div v-if="s.show_thumbnail !== false" :style="thumbStyle"><svg style="width:100%;height:100%;color:#d1d5db" viewBox="0 0 20 20" fill="currentColor"><rect width="20" height="20" rx="2" fill="#e5e7eb"/><path d="M4 14l3-3 2 2 4-5 3 3.5V14H4z" fill="#d1d5db"/></svg></div>
          <div style="flex:1;min-width:0">
            <div style="font-size:13px;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis" :style="{ color: s.title_color || '#111827' }">{{ item.title }}</div>
            <div style="font-size:11px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-top:2px" :style="{ color: s.excerpt_color || '#6b7280' }">{{ item.excerpt }}</div>
          </div>
        </div>
      </div>
    </div>

    <!-- ═══ EXPANDED: campo input full-width + popup sotto ═══ -->
    <div v-else style="position:relative">
      <div :style="inputWrapStyle">
        <div style="position:absolute;left:12px;top:50%;transform:translateY(-50%);display:flex;align-items:center;pointer-events:none" :style="{ color: s.icon_color || '#9ca3af' }">
          <svg width="16" height="16" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="8.5" cy="8.5" r="5.5"/><line x1="13" y1="13" x2="17" y2="17"/>
          </svg>
        </div>
        <div :style="inputStyle">{{ s.placeholder || 'Cerca...' }}</div>
        <div style="position:absolute;right:10px;top:50%;transform:translateY(-50%);color:#d1d5db;font-size:14px;line-height:1">&times;</div>
      </div>
      <div style="margin-top:6px;border-radius:10px;overflow:hidden;box-shadow:0 10px 25px -5px rgba(0,0,0,.12);min-width:340px" :style="dropdownStyle">
        <div v-for="(item, i) in fakeResults" :key="'e'+i" style="display:flex;align-items:center;gap:10px;padding:8px 12px" :style="i === 0 ? { background: s.item_hover_bg || '#f3f4f6' } : {}">
          <div v-if="s.show_thumbnail !== false" :style="thumbStyle"><svg style="width:100%;height:100%;color:#d1d5db" viewBox="0 0 20 20" fill="currentColor"><rect width="20" height="20" rx="2" fill="#e5e7eb"/><path d="M4 14l3-3 2 2 4-5 3 3.5V14H4z" fill="#d1d5db"/></svg></div>
          <div style="flex:1;min-width:0">
            <div style="font-size:13px;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis" :style="{ color: s.title_color || '#111827' }">{{ item.title }}</div>
            <div style="font-size:11px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-top:2px" :style="{ color: s.excerpt_color || '#6b7280' }">{{ item.excerpt }}</div>
          </div>
        </div>
        <div v-if="s.show_all_url" style="padding:8px 12px;font-size:12px;text-align:center;border-top:1px solid" :style="{ borderColor: s.results_border_color || '#e5e7eb', color: '#6366f1' }">
          {{ s.show_all_text || 'Vedi tutti i risultati' }}
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const s = computed(() => props.settings);

const fakeResults = [
  { title: 'Risultato di esempio 1', excerpt: 'Breve descrizione del primo risultato...' },
  { title: 'Risultato di esempio 2', excerpt: 'Un altro contenuto trovato...' },
  { title: 'Risultato di esempio 3', excerpt: 'Terzo risultato con anteprima...' },
];

const iconSize = computed(() => Math.round(parseInt(s.value.input_font_size || 14) * 1.2));

const compactStyle = computed(() => {
  const h = parseInt(s.value.input_height || 44);
  return {
    width: h + 'px',
    height: h + 'px',
    borderRadius: '50%',
    background: s.value.input_bg || '#ffffff',
    color: s.value.icon_color || '#9ca3af',
    border: '1px solid ' + (s.value.results_border_color || '#e5e7eb'),
  };
});

const fieldPreviewStyle = computed(() => ({
  background: s.value.input_bg || '#ffffff',
  border: '1px solid ' + (s.value.results_border_color || '#e5e7eb'),
  height: (parseInt(s.value.input_height || 44)) + 'px',
}));

const inputWrapStyle = computed(() => ({
  position: 'relative',
  borderRadius: '8px',
  border: '1px solid ' + (s.value.results_border_color || '#e5e7eb'),
  background: s.value.input_bg || '#ffffff',
  overflow: 'hidden',
}));

const inputStyle = computed(() => ({
  height: (parseInt(s.value.input_height || 44)) + 'px',
  lineHeight: (parseInt(s.value.input_height || 44)) + 'px',
  paddingLeft: '36px',
  paddingRight: '28px',
  fontSize: (parseInt(s.value.input_font_size || 14)) + 'px',
  color: s.value.input_color ? s.value.input_color + '88' : '#9ca3af',
  whiteSpace: 'nowrap',
  overflow: 'hidden',
  textOverflow: 'ellipsis',
}));

const thumbStyle = computed(() => {
  const w = parseInt(s.value.thumb_width || 48);
  const h = parseInt(s.value.thumb_height || 48);
  const rad = parseInt(s.value.thumb_radius || 6);
  return {
    width: w + 'px',
    height: h + 'px',
    minWidth: w + 'px',
    borderRadius: rad + 'px',
    overflow: 'hidden',
    background: '#f3f4f6',
  };
});

const dropdownStyle = computed(() => ({
  background: s.value.results_bg || '#ffffff',
  border: '1px solid ' + (s.value.results_border_color || '#e5e7eb'),
  maxHeight: (parseInt(s.value.results_max_height || 400)) + 'px',
  overflow: 'auto',
}));
</script>
