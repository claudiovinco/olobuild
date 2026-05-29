<template>
  <div>
    <h3 :style="{ fontSize:'18px', fontWeight:'700', color: TOKENS.text, margin:'0 0 12px' }">{{ t('Tipi di struttura') }}</h3>
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px">
      <div v-for="cat in categories" :key="cat.name" :style="{ background: TOKENS.primary, borderRadius:'10px', padding:'16px', textAlign:'center', color: TOKENS.onPrimary }">
        <div style="display:flex;justify-content:center;margin-bottom:6px">
          <span class="olo-cat-ic" style="width:24px;height:24px;display:inline-flex" v-html="iconSvg(cat.icon)"></span>
        </div>
        <div style="font-size:14px;font-weight:600">{{ cat.name }}</div>
        <div style="font-size:12px;opacity:.85;margin-top:2px">{{ cat.count }} strutture</div>
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
const iconSvg = (name) => iconsSvg[name] || '';
const categories = [
  { name: 'Sale conferenze', count: 5, icon: 'users' },
  { name: 'Auditorium', count: 2, icon: 'star' },
  { name: 'Palestre', count: 3, icon: 'home' },
  { name: 'Piazze', count: 1, icon: 'map-pin' },
];
</script>

<style scoped>
.olo-cat-ic { color: #fff; }
.olo-cat-ic :deep(svg) { width: 100%; height: 100%; fill: currentColor; stroke: currentColor; }
</style>
