<template>
  <!-- Decoratore a dimensione zero: nel canvas mostra un chip riconoscibile
       con l'anteprima dell'alone (sul frontend è un layer fisso invisibile). -->
  <div class="olo-pagelight-preview">
    <span class="halo" :style="haloStyle"></span>
    <span class="txt">{{ t('Luce di pagina') }} · {{ s.position }}</span>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';
import { resolveColor, TOKENS } from '@/composables/oloTileDefaults';
import def from '@/config/elements/pagelight.js';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const s = computed(() => ({ ...def.defaults, ...props.settings }));

const haloStyle = computed(() => {
  const c = resolveColor(s.value.light_color, TOKENS.primary);
  return {
    background: `radial-gradient(circle at 50% 50%, ${c} 0%, transparent 70%)`,
    opacity: Math.min(1, (parseInt(s.value.intensity) || 26) / 40),
  };
});
</script>

<style scoped>
.olo-pagelight-preview{display:flex;align-items:center;gap:10px;padding:10px 14px;
  border:1px dashed var(--olo-color-border, rgba(128,128,128,.4));border-radius:8px;
  font-size:12px;color:var(--olo-color-text-muted, #8B90A0);}
.olo-pagelight-preview .halo{width:26px;height:26px;border-radius:50%;flex:0 0 auto;}
.olo-pagelight-preview .txt{font-family:var(--olo-font-family-mono, monospace);letter-spacing:.06em;}
</style>
