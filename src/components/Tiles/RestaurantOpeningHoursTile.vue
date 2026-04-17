<template>
  <div class="mb-font-sans">
    <div :style="cardWrapStyle">
      <div v-if="s.show_header" :style="headerStyle">
        <div :style="{ fontSize:'18px', fontWeight:'700' }">{{ s.title }}</div>
        <div v-if="s.subtitle" :style="{ fontSize:'13px', opacity:0.85, marginTop:'2px' }">{{ s.subtitle }}</div>
      </div>
      <div :style="{ padding:'16px' }">
        <div v-for="(day, i) in days" :key="i">
          <div :style="dayRowStyle(day, i)">
            <div :style="{ display:'flex', alignItems:'center', gap:'8px' }">
              <span v-if="s.show_icon" :style="{ fontSize:'14px' }">{{ day.closed ? '\u274C' : '\u23F0' }}</span>
              <span :style="{ fontSize: s.day_font_size+'px', fontWeight:'600', color: dayColor(day) }">{{ day.label }}</span>
            </div>
            <span v-if="day.closed" :style="{ fontSize: s.time_font_size+'px', fontWeight:'500', color: s.closed_color }">{{ s.closed_label }}</span>
            <span v-else :style="timeBadgeStyle">{{ day.hours }}</span>
          </div>
          <div v-if="i < days.length - 1" :style="{ borderBottom:'1px solid '+ s.divider_color, margin:'0' }"></div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const defaults = { show_header: true, title: 'Orari di Apertura', subtitle: '', header_bg: '#1F2937', header_text_color: '#fff', card_border_radius: 12, card_shadow: 'sm', card_bg: '#fff', highlight_today: true, day_font_size: 14, time_font_size: 13, day_color: '#374151', time_color: '#6366F1', time_bg: '#EEF2FF', time_radius: 6, show_icon: true, closed_label: 'Chiuso', closed_color: '#EF4444', divider_color: '#F3F4F6' };
const s = computed(() => ({ ...defaults, ...props.settings }));
const shadowMap = { none:'none', sm:'0 1px 3px rgba(0,0,0,0.1)', md:'0 4px 12px rgba(0,0,0,0.1)', lg:'0 8px 24px rgba(0,0,0,0.15)' };
const todayIndex = new Date().getDay(); // 0=Sun
const dayMap = [6,0,1,2,3,4,5]; // map JS getDay to Mon=0
const todayIdx = dayMap[todayIndex];
const days = [
  { label:'Luned\u00EC', hours:'11:30 \u2013 14:30 / 18:00 \u2013 22:30', closed:false },
  { label:'Marted\u00EC', hours:'11:30 \u2013 14:30 / 18:00 \u2013 22:30', closed:false },
  { label:'Mercoled\u00EC', hours:'11:30 \u2013 14:30 / 18:00 \u2013 22:30', closed:false },
  { label:'Gioved\u00EC', hours:'11:30 \u2013 14:30 / 18:00 \u2013 22:30', closed:false },
  { label:'Venerd\u00EC', hours:'11:30 \u2013 14:30 / 18:00 \u2013 23:00', closed:false },
  { label:'Sabato', hours:'18:00 \u2013 23:00', closed:false },
  { label:'Domenica', hours:'', closed:true },
];
const cardWrapStyle = computed(() => ({
  background: s.value.card_bg, borderRadius: s.value.card_border_radius+'px', border:'1px solid #E5E7EB',
  boxShadow: shadowMap[s.value.card_shadow]||shadowMap.sm, overflow:'hidden',
}));
const headerStyle = computed(() => ({
  background: s.value.header_bg, color: s.value.header_text_color, padding:'16px 20px',
}));
function dayRowStyle(day, i) {
  const isToday = s.value.highlight_today && i === todayIdx;
  return { display:'flex', justifyContent:'space-between', alignItems:'center', padding:'10px 4px',
    background: isToday ? '#FEFCE8' : 'transparent', borderRadius: isToday ? '6px' : '0',
    fontWeight: isToday ? '700' : '400' };
}
function dayColor(day) {
  return day.closed ? s.value.closed_color : s.value.day_color;
}
const timeBadgeStyle = computed(() => ({
  fontSize: s.value.time_font_size+'px', fontWeight:'600', color: s.value.time_color,
  background: s.value.time_bg, padding:'4px 10px', borderRadius: s.value.time_radius+'px',
}));
</script>
