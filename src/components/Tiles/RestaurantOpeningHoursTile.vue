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
              <span v-if="s.show_icon" class="olo-roh-icon" :style="{ color: day.closed ? closedColor : dayColor(day) }" v-html="day.closed ? closeSvg : clockSvg"></span>
              <span :style="{ fontSize: s.day_font_size+'px', fontWeight:'600', color: dayColor(day) }">{{ day.label }}</span>
            </div>
            <span v-if="day.closed" :style="{ fontSize: s.time_font_size+'px', fontWeight:'500', color: closedColor }">{{ s.closed_label }}</span>
            <span v-else :style="timeBadgeStyle">{{ day.hours }}</span>
          </div>
          <div v-if="i < days.length - 1" :style="{ borderBottom:'1px solid '+ dividerColor, margin:'0' }"></div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import iconsSvg from '../ProSlider/uikitIconsSvg.js';
import { resolveColor, TOKENS } from '@/composables/oloTileDefaults';
const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const clockSvg = iconsSvg['clock'] || '';
const closeSvg = iconsSvg['close'] || iconsSvg['x'] || '';
const defaults = { show_header: true, title: 'Orari di Apertura', subtitle: '', header_bg: '', header_text_color: '', card_border_radius: 12, card_shadow: 'sm', card_bg: '#fff', highlight_today: true, day_font_size: 14, time_font_size: 13, day_color: '', time_color: '', time_bg: '', time_radius: 6, show_icon: true, closed_label: 'Chiuso', closed_color: '', divider_color: '' };
const s = computed(() => ({ ...defaults, ...props.settings }));
const shadowMap = { none:'none', sm:'0 1px 3px rgba(0,0,0,0.1)', md:'0 4px 12px rgba(0,0,0,0.1)', lg:'0 8px 24px rgba(0,0,0,0.15)' };
const dayColorTok = computed(() => resolveColor(s.value.day_color, TOKENS.text));
const closedColor = computed(() => resolveColor(s.value.closed_color, TOKENS.error.fg));
const timeColor = computed(() => resolveColor(s.value.time_color, TOKENS.primary));
const dividerColor = computed(() => resolveColor(s.value.divider_color, TOKENS.surfaceAlt));
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
  background: s.value.card_bg, borderRadius: s.value.card_border_radius+'px', border:'1px solid '+TOKENS.border,
  boxShadow: shadowMap[s.value.card_shadow]||shadowMap.sm, overflow:'hidden',
}));
const headerStyle = computed(() => ({
  background: resolveColor(s.value.header_bg, TOKENS.dark), color: resolveColor(s.value.header_text_color, TOKENS.onPrimary), padding:'16px 20px',
}));
function dayRowStyle(day, i) {
  const isToday = s.value.highlight_today && i === todayIdx;
  return { display:'flex', justifyContent:'space-between', alignItems:'center', padding:'10px 4px',
    // highlight oggi → tinta soft brand (no più giallo fisso #FEFCE8)
    background: isToday ? 'color-mix(in srgb, var(--olo-color-primary, #e1474f) 8%, transparent)' : 'transparent', borderRadius: isToday ? '6px' : '0',
    fontWeight: isToday ? '700' : '400' };
}
function dayColor(day) {
  return day.closed ? closedColor.value : dayColorTok.value;
}
const timeBadgeStyle = computed(() => ({
  fontSize: s.value.time_font_size+'px', fontWeight:'600', color: timeColor.value,
  background: resolveColor(s.value.time_bg, `color-mix(in srgb, ${timeColor.value} 12%, #fff)`),
  padding:'4px 10px', borderRadius: s.value.time_radius+'px',
}));
</script>

<style scoped>
.olo-roh-icon { display: inline-flex; }
.olo-roh-icon :deep(svg) { width: 14px; height: 14px; stroke: currentColor; fill: none; }
</style>
