<template>
  <div>
    <!-- Filters bar -->
    <div style="display:flex;gap:10px;margin-bottom:12px;flex-wrap:wrap;align-items:stretch">
      <select :style="selStyle"><option>{{ t('Tutti i tipi') }}</option></select>
      <select :style="selStyle"><option>{{ t('Tutte le zone') }}</option></select>
      <input type="number" :placeholder="t('Capienza min.')" :style="selStyle" />
    </div>
    <div style="display:flex;gap:6px;flex-wrap:wrap;margin-bottom:14px">
      <span v-for="eq in equipment" :key="eq.n" :style="{...pillStyle, ...(eq.active ? activePill : {})}">{{ eq.n }}</span>
    </div>

    <!-- Body: map + cards -->
    <div :style="bodyStyle">
      <!-- Map -->
      <div v-if="s.layout !== 'cards-only'" :style="mapWrapStyle">
        <div :style="mapStyle">
          <div style="position:absolute;inset:0;background:linear-gradient(135deg,#e0f2fe,#bae6fd 40%,#7dd3fc);opacity:.5"></div>
          <div style="position:absolute;inset:0;opacity:.12;background:repeating-linear-gradient(0deg,transparent,transparent 25px,#0284c7 25px,#0284c7 26px),repeating-linear-gradient(90deg,transparent,transparent 25px,#0284c7 25px,#0284c7 26px)"></div>
          <div v-for="pin in pins" :key="pin.x" :style="{position:'absolute',left:pin.x+'%',top:pin.y+'%',transform:'translate(-50%,-100%)',zIndex:2}">
            <svg width="18" height="28" viewBox="0 0 25 41"><path d="M12.5 0C5.6 0 0 5.6 0 12.5C0 22 12.5 41 12.5 41S25 22 25 12.5C25 5.6 19.4 0 12.5 0Z" :fill="markerColor"/><circle cx="12.5" cy="12.5" r="5" fill="#fff" fill-opacity=".9"/></svg>
          </div>
          <div :style="{ position:'absolute', bottom:'6px', left:'8px', background:'rgba(255,255,255,.85)', padding:'2px 8px', borderRadius:'4px', fontSize:'9px', color: TOKENS.text, zIndex:3 }">{{ s.tile_layer || 'positron' }}</div>
        </div>
      </div>

      <!-- Cards -->
      <div :style="{flex:'1',minWidth:'0'}">
        <div :style="{ fontSize:'11px', color: TOKENS.textSoft, marginBottom:'6px' }"><strong :style="{ color: TOKENS.text }">3</strong> / 9 sale</div>
        <div :style="gridStyle">
          <div v-for="room in rooms" :key="room.name" :style="{ border:'1px solid ' + TOKENS.border, borderRadius:'8px', overflow:'hidden' }">
            <div v-if="s.card_image !== false" :style="cardImgStyle">
              <span class="olo-grid-ph" :style="{ width:'24px', height:'24px', color: TOKENS.textFaint }" v-html="imgIcon"></span>
            </div>
            <div style="padding:10px 12px">
              <div :style="{ fontSize:'13px', fontWeight:'700', color: TOKENS.text }">{{ room.name }}</div>
              <span :style="tagStyle">{{ room.type }}</span>
              <div :style="{ fontSize:'11px', color: TOKENS.textSoft, marginTop:'3px' }">{{ room.addr }}</div>
              <div :style="{ display:'flex', gap:'8px', fontSize:'11px', color: TOKENS.textSoft, marginTop:'4px' }">
                <span>{{ room.cap }} persone</span>
                <span :style="{ fontWeight:'700', color: TOKENS.primary }">&euro;{{ room.rate }}/h</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { t } from '@/i18n';
import { computed } from 'vue';
import iconsSvg from '../ProSlider/uikitIconsSvg.js';
import { resolveColor, TOKENS } from '@/composables/oloTileDefaults';
const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const defaults = { layout: 'map-left', map_height: '500', tile_layer: 'positron', marker_color: '', columns: '1', card_image: true };
const s = computed(() => ({ ...defaults, ...props.settings }));

// Pin token-first: brand primario se l'utente non sceglie un colore.
const markerColor = computed(() => resolveColor(s.value.marker_color, TOKENS.primary));
const imgIcon = computed(() => iconsSvg['image'] || '');

const rooms = [
  { name: 'Sala Giunta', type: 'Conferenze', addr: 'P.zza Podestà 1, Rovereto', cap: 25, rate: 20 },
  { name: 'Auditorium Melotti', type: 'Auditorium', addr: 'Via Laurenti 7', cap: 200, rate: 50 },
  { name: 'Sala Rosmini', type: 'Conferenze', addr: 'C.so Bettini 39', cap: 80, rate: 35 },
];

const equipment = [
  { n: 'Wi-Fi', active: true }, { n: 'Proiettore', active: false }, { n: 'Microfono', active: true },
  { n: 'Condizionatore', active: false }, { n: 'Lavagna', active: false },
];

const pins = [
  { x: 35, y: 40 }, { x: 55, y: 30 }, { x: 45, y: 60 }, { x: 65, y: 50 }, { x: 30, y: 55 },
];

const selStyle = { padding: '7px 10px', border: '1px solid ' + TOKENS.border, borderRadius: '8px', fontSize: '12px', flex: '1', minWidth: '120px', background: TOKENS.surface, color: TOKENS.text };
const pillStyle = { padding: '4px 12px', borderRadius: '999px', fontSize: '11px', fontWeight: '500', background: TOKENS.surfaceAlt, color: TOKENS.textSoft, cursor: 'pointer', border: '1px solid transparent' };
const activePill = { background: TOKENS.primary, color: TOKENS.onPrimary, borderColor: TOKENS.primary };
const cardImgStyle = { height: '100px', background: TOKENS.surfaceAlt, display: 'flex', alignItems: 'center', justifyContent: 'center' };
const tagStyle = { fontSize: '9px', fontWeight: '600', color: TOKENS.primary, background: 'color-mix(in srgb, var(--olo-color-primary, #e1474f) 8%, transparent)', padding: '1px 6px', borderRadius: '3px' };

const isHoriz = computed(() => s.value.layout === 'map-left' || s.value.layout === 'map-right');
const bodyStyle = computed(() => ({
  display: 'flex',
  flexDirection: s.value.layout === 'map-right' ? 'row-reverse' : (isHoriz.value ? 'row' : 'column'),
  gap: '16px',
}));
const mapWrapStyle = computed(() => ({
  flex: isHoriz.value ? '0 0 50%' : 'none',
  minWidth: '0',
  borderRadius: '8px',
  overflow: 'hidden',
}));
const mapStyle = computed(() => ({
  position: 'relative',
  height: Math.min(parseInt(s.value.map_height) || 500, isHoriz.value ? 280 : 180) + 'px',
  background: '#dbeafe',
}));
const gridStyle = computed(() => ({
  display: 'grid',
  gridTemplateColumns: `repeat(${parseInt(s.value.columns) || 1}, 1fr)`,
  gap: '10px',
}));
</script>

<style scoped>
.olo-grid-ph :deep(svg) { width: 100%; height: 100%; fill: currentColor; stroke: currentColor; }
</style>
