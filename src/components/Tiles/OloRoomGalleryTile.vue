<template>
  <div>
    <div :style="mainStyle">
      <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center">
        <span class="olo-gal-ph" :style="{ width:'40px', height:'40px', color: TOKENS.textFaint }" v-html="imgIcon"></span>
      </div>
      <div style="position:absolute;bottom:10px;right:10px;background:rgba(0,0,0,.6);color:#fff;padding:3px 10px;border-radius:12px;font-size:11px;z-index:1">1 / 8</div>
      <div v-if="s.show_arrows" style="position:absolute;top:50%;left:8px;z-index:1;transform:translateY(-50%)">
        <span style="background:rgba(0,0,0,.4);color:#fff;width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:14px">{{ t('&lsaquo;') }}</span>
      </div>
      <div v-if="s.show_arrows" style="position:absolute;top:50%;right:8px;z-index:1;transform:translateY(-50%)">
        <span style="background:rgba(0,0,0,.4);color:#fff;width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:14px">{{ t('&rsaquo;') }}</span>
      </div>
    </div>
    <div :style="thumbsStyle">
      <div v-for="i in thumbCount" :key="i" :style="thumbStyle(i)"></div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';
import iconsSvg from '../ProSlider/uikitIconsSvg.js';
import { TOKENS } from '@/composables/oloTileDefaults';
const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const defaults = { columns: 5, lightbox: true, main_height: 450, kenburns: true, autoplay: true, show_counter: true, show_arrows: true, show_dots: true, thumb_height: 80, transition: 'kenburns' };
const s = computed(() => ({ ...defaults, ...props.settings }));
const imgIcon = computed(() => iconsSvg['image'] || '');
const thumbCount = computed(() => Math.min(parseInt(s.value.columns) || 5, 6));
const mainStyle = computed(() => ({ position: 'relative', height: Math.min(parseInt(s.value.main_height) || 450, 220) + 'px', borderRadius: '10px', overflow: 'hidden', marginBottom: '8px', background: TOKENS.surfaceAlt }));
const thumbsStyle = computed(() => ({ display: 'flex', gap: '6px' }));
function thumbStyle(i) {
  return { flex: '1', height: Math.min(parseInt(s.value.thumb_height) || 80, 50) + 'px', borderRadius: '6px', background: TOKENS.surfaceAlt, border: '1px solid ' + TOKENS.border, cursor: 'pointer' };
}
</script>

<style scoped>
.olo-gal-ph :deep(svg) { width: 100%; height: 100%; fill: currentColor; stroke: currentColor; }
</style>
