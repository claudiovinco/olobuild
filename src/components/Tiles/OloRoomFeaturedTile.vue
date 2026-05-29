<template>
  <div>
    <h3 :style="{ fontSize:'18px', fontWeight:'700', color: TOKENS.text, margin:'0 0 12px' }">{{ t('Sale in evidenza') }}</h3>
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px">
      <div v-for="room in rooms" :key="room.name" :style="{ border:'1px solid ' + TOKENS.border, borderRadius:'10px', overflow:'hidden' }">
        <div :style="imgStyle">
          <span class="olo-feat-ph" :style="{ width:'26px', height:'26px', color: TOKENS.textFaint }" v-html="imgIcon"></span>
          <span style="position:absolute;top:8px;right:8px;background:rgba(0,0,0,.5);color:#fff;padding:2px 8px;border-radius:10px;font-size:10px;font-weight:700;display:inline-flex;align-items:center;gap:3px">
            <span class="olo-feat-star" style="width:11px;height:11px;display:inline-flex" v-html="starIcon"></span>{{ t('In evidenza') }}
          </span>
        </div>
        <div style="padding:10px">
          <div :style="{ fontSize:'14px', fontWeight:'600', color: TOKENS.text }">{{ room.name }}</div>
          <div :style="{ fontSize:'12px', color: TOKENS.textSoft, marginTop:'2px' }">{{ room.info }}</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';
import iconsSvg from '../ProSlider/uikitIconsSvg.js';
import { TOKENS } from '@/composables/oloTileDefaults';
defineProps({ settings: { type: Object, default: () => ({}) } });
const imgIcon = computed(() => iconsSvg['image'] || '');
const starIcon = computed(() => iconsSvg['star'] || '');
const rooms = [
  { name: 'Auditorium Melotti', info: '200 posti · Rovereto' },
  { name: 'Salone delle Feste', info: '120 posti · Palazzo Fedrigotti' },
  { name: 'Aula Magna', info: '80 posti · Centro Civico' },
];
const imgStyle = { position: 'relative', height: '110px', background: TOKENS.surfaceAlt, display: 'flex', alignItems: 'center', justifyContent: 'center' };
</script>

<style scoped>
.olo-feat-ph :deep(svg),
.olo-feat-star :deep(svg) { width: 100%; height: 100%; fill: currentColor; stroke: currentColor; }
.olo-feat-star { color: #fff; }
</style>
