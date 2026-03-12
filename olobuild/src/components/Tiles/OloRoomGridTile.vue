<template>
  <div>
    <!-- Filters bar -->
    <div style="display:flex;gap:10px;margin-bottom:12px;flex-wrap:wrap;align-items:stretch">
      <select :style="selStyle"><option>Tutti i tipi</option></select>
      <select :style="selStyle"><option>Tutte le zone</option></select>
      <input type="number" placeholder="Capienza min." :style="selStyle" />
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
            <svg width="18" height="28" viewBox="0 0 25 41"><path d="M12.5 0C5.6 0 0 5.6 0 12.5C0 22 12.5 41 12.5 41S25 22 25 12.5C25 5.6 19.4 0 12.5 0Z" :fill="s.marker_color||'#e11d48'"/><circle cx="12.5" cy="12.5" r="5" fill="#fff" fill-opacity=".9"/></svg>
          </div>
          <div style="position:absolute;bottom:6px;left:8px;background:rgba(255,255,255,.85);padding:2px 8px;border-radius:4px;font-size:9px;color:#374151;z-index:3">{{ s.tile_layer || 'positron' }}</div>
        </div>
      </div>

      <!-- Cards -->
      <div :style="{flex:'1',minWidth:'0'}">
        <div style="font-size:11px;color:#6b7280;margin-bottom:6px"><strong style="color:var(--olo-color-text, #374151)">3</strong> / 9 sale</div>
        <div :style="gridStyle">
          <div v-for="room in rooms" :key="room.name" style="border:1px solid #e5e7eb;border-radius:8px;overflow:hidden">
            <div v-if="s.card_image !== false" :style="{height:'100px',background:room.bg}"></div>
            <div style="padding:10px 12px">
              <div style="font-size:13px;font-weight:700;color:var(--olo-color-text, #374151)">{{ room.name }}</div>
              <span style="font-size:9px;font-weight:600;color:#1e87f0;background:rgba(30,135,240,.08);padding:1px 6px;border-radius:3px">{{ room.type }}</span>
              <div style="font-size:11px;color:#6b7280;margin-top:3px">{{ room.addr }}</div>
              <div style="display:flex;gap:8px;font-size:11px;color:#4b5563;margin-top:4px">
                <span>{{ room.cap }} persone</span>
                <span style="font-weight:700;color:#1e87f0">&euro;{{ room.rate }}/h</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const defaults = { layout: 'map-left', map_height: '500', tile_layer: 'positron', marker_color: '#e11d48', columns: '1', card_image: true };
const s = computed(() => ({ ...defaults, ...props.settings }));

const rooms = [
  { name: 'Sala Giunta', type: 'Conferenze', addr: 'P.zza Podestà 1, Rovereto', cap: 25, rate: 20, bg: 'linear-gradient(135deg,#1e3a5f,#0e7490)' },
  { name: 'Auditorium Melotti', type: 'Auditorium', addr: 'Via Laurenti 7', cap: 200, rate: 50, bg: 'linear-gradient(135deg,#6366f1,#4f46e5)' },
  { name: 'Sala Rosmini', type: 'Conferenze', addr: 'C.so Bettini 39', cap: 80, rate: 35, bg: 'linear-gradient(135deg,#059669,#047857)' },
];

const equipment = [
  { n: 'Wi-Fi', active: true }, { n: 'Proiettore', active: false }, { n: 'Microfono', active: true },
  { n: 'Condizionatore', active: false }, { n: 'Lavagna', active: false },
];

const pins = [
  { x: 35, y: 40 }, { x: 55, y: 30 }, { x: 45, y: 60 }, { x: 65, y: 50 }, { x: 30, y: 55 },
];

const selStyle = { padding: '7px 10px', border: '1px solid #d1d5db', borderRadius: '8px', fontSize: '12px', flex: '1', minWidth: '120px', background: '#fff', color: 'var(--olo-color-text, #374151)' };
const pillStyle = { padding: '4px 12px', borderRadius: '999px', fontSize: '11px', fontWeight: '500', background: '#f3f4f6', color: '#4b5563', cursor: 'pointer', border: '1px solid transparent' };
const activePill = { background: '#1e87f0', color: '#fff', borderColor: '#1e87f0' };

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
