<template>
  <!-- Fallback shape inline quando il template non ha thumbnail catturata -->
  <div class="tpl-preview" :class="['kind-' + kind, 'type-' + (type || 'page')]" :style="bgStyle">
    <!-- Empty -->
    <template v-if="kind === 'empty'">
      <div class="pv-empty">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" style="opacity:.4">
          <rect x="3" y="3" width="18" height="18" rx="2"/><line x1="9" y1="3" x2="9" y2="21"/>
        </svg>
        <span>0 elementi</span>
      </div>
    </template>

    <!-- Header -->
    <template v-else-if="kind === 'header'">
      <div class="pv-header-bar">
        <div class="pv-h-logo"></div>
        <div class="pv-h-spc"></div>
        <div class="pv-h-link"></div>
        <div class="pv-h-link"></div>
        <div class="pv-h-link"></div>
      </div>
      <div class="pv-pad">
        <div class="pv-bar med"></div>
        <div class="pv-bar short"></div>
      </div>
    </template>

    <!-- Footer -->
    <template v-else-if="kind === 'footer'">
      <div class="pv-pad">
        <div class="pv-bar med"></div>
        <div class="pv-bar short"></div>
      </div>
      <div class="pv-footer-bar">
        <div v-for="i in 4" :key="i" class="pv-f-col">
          <div class="pv-f-tit"></div>
          <div class="pv-f-l"></div>
          <div class="pv-f-l short"></div>
        </div>
      </div>
    </template>

    <!-- Widget -->
    <template v-else-if="kind === 'widget'">
      <div class="pv-widget">
        <div class="pv-w-card">
          <div class="pv-w-icon"></div>
          <div class="pv-w-text">
            <div class="pv-bar"></div>
            <div class="pv-bar short"></div>
          </div>
        </div>
      </div>
    </template>

    <!-- Split -->
    <template v-else-if="kind === 'split'">
      <div class="pv-split">
        <div class="pv-s-img"></div>
        <div class="pv-s-text">
          <div class="pv-bar"></div>
          <div class="pv-bar med"></div>
          <div class="pv-bar short"></div>
          <div class="pv-bar brand short"></div>
        </div>
      </div>
    </template>

    <!-- Long (article) -->
    <template v-else-if="kind === 'long'">
      <div class="pv-pad">
        <div class="pv-bar med dark"></div>
        <div class="pv-bar short"></div>
        <div class="pv-row">
          <div class="cell"></div><div class="cell"></div><div class="cell"></div>
        </div>
        <div class="pv-row" style="grid-template-columns:1fr 1fr">
          <div class="cell tall"></div><div class="cell tall"></div>
        </div>
      </div>
    </template>

    <!-- Grid (mega panel) -->
    <template v-else-if="kind === 'grid'">
      <div class="pv-pad">
        <div class="pv-row" style="grid-template-columns:repeat(4,1fr); gap:5px">
          <div class="cell tall"></div><div class="cell tall"></div><div class="cell tall"></div><div class="cell tall"></div>
          <div class="cell tall"></div><div class="cell tall"></div><div class="cell tall"></div><div class="cell tall"></div>
        </div>
      </div>
    </template>

    <!-- hero+grid (default) -->
    <template v-else>
      <div class="pv-hero-grid">
        <div class="pv-hero">
          <div class="pv-h-bars">
            <div class="pv-bar dark"></div>
            <div class="pv-bar short"></div>
          </div>
        </div>
        <div class="pv-row">
          <div class="cell"></div><div class="cell"></div><div class="cell"></div>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { computed } from 'vue';
const props = defineProps({
  kind: { type: String, default: 'hero+grid' },
  type: { type: String, default: 'page' },
});

// Gradient di sfondo basato sul tipo
const TINTS = {
  page:      ['#dcefd2', '#fff'],
  header:    ['#dbeafe', '#fff'],
  footer:    ['#e2e8f0', '#fff'],
  single:    ['#f3e8ff', '#fff'],
  megapanel: ['#fef3c7', '#fff'],
  widget:    ['#ede9fe', '#fff'],
  '404':     ['#fee2e2', '#fff'],
};
const bgStyle = computed(() => {
  const tint = TINTS[props.type] || TINTS.page;
  return { background: `linear-gradient(135deg, ${tint[0]} 0%, ${tint[1]} 70%)` };
});
</script>

<style scoped>
.tpl-preview {
  position: absolute; inset: 0;
  padding: 14px;
  display: flex; flex-direction: column; gap: 6px;
}

/* Bar primitives */
.pv-bar {
  height: 6px; border-radius: 2px;
  background: rgba(15,17,21,.10);
}
.pv-bar.dark  { background: rgba(15,17,21,.55); }
.pv-bar.brand { background: rgba(74,140,42,.65); }
.pv-bar.short { width: 35%; }
.pv-bar.med   { width: 60%; }
.pv-row {
  display: grid; grid-template-columns: repeat(3, 1fr); gap: 6px; margin-top: 4px;
}
.pv-row .cell {
  height: 30px; border-radius: 4px;
  background: rgba(15,17,21,.06);
}
.pv-row .cell.tall { height: 24px; }

/* Empty */
.pv-empty {
  position: absolute; inset: 0;
  display: flex; flex-direction: column;
  align-items: center; justify-content: center;
  color: var(--ot-text-muted, #64748b);
  font-size: 11px; gap: 6px;
}

/* Header preview */
.kind-header { padding: 0; }
.pv-header-bar {
  height: 30%;
  background: #1d2327;
  display: flex; align-items: center;
  padding: 0 12px; gap: 8px;
}
.pv-h-logo { width: 24px; height: 8px; background: rgba(255,255,255,.55); border-radius: 2px; }
.pv-h-spc  { flex: 1; }
.pv-h-link { width: 28px; height: 5px; background: rgba(255,255,255,.30); border-radius: 2px; }
.pv-pad { padding: 12px 14px; display: flex; flex-direction: column; gap: 6px; }

/* Footer preview */
.kind-footer { padding: 0; }
.pv-footer-bar {
  height: 38%; background: #1d2327;
  padding: 10px 12px;
  display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px;
  align-content: center;
}
.pv-f-col { display: flex; flex-direction: column; gap: 4px; }
.pv-f-tit { height: 5px; width: 60%; background: rgba(255,255,255,.5); border-radius: 2px; }
.pv-f-l   { height: 3px; background: rgba(255,255,255,.25); border-radius: 2px; }
.pv-f-l.short { width: 70%; }

/* Widget */
.pv-widget {
  flex: 1; display: flex; align-items: center; justify-content: center;
  background: linear-gradient(135deg, #dcefd2, #f0f7ec);
  border-radius: 6px;
  margin: 4px;
}
.pv-w-card {
  display: flex; align-items: center; gap: 8px;
  background: #fff;
  padding: 8px 12px;
  border-radius: 6px;
  border: 1px solid rgba(0,0,0,.06);
  box-shadow: 0 2px 6px rgba(0,0,0,.06);
}
.pv-w-icon {
  width: 20px; height: 20px; border-radius: 4px;
  background: #4a8c2a;
}
.pv-w-text { display: flex; flex-direction: column; gap: 3px; }
.pv-w-text .pv-bar { width: 50px; height: 4px; }

/* Split */
.pv-split {
  flex: 1; display: flex; gap: 10px;
}
.pv-s-img {
  flex: 1; border-radius: 6px;
  background: rgba(15,17,21,.08);
  display: flex; align-items: center; justify-content: center;
}
.pv-s-img::after {
  content: ''; width: 24px; height: 24px;
  border-radius: 4px;
  background: rgba(74,140,42,.55);
}
.pv-s-text {
  flex: 1.2;
  display: flex; flex-direction: column; justify-content: center;
  gap: 5px;
}

/* Long article */

/* Grid (mega panel) */

/* Hero + grid */
.pv-hero-grid {
  flex: 1; display: flex; flex-direction: column; gap: 6px;
}
.pv-hero {
  flex: 1.2; border-radius: 6px;
  background: linear-gradient(135deg, rgba(74,140,42,.20), rgba(74,140,42,.05));
  display: flex; align-items: center;
  padding: 8px 10px;
}
.pv-h-bars {
  display: flex; flex-direction: column; gap: 5px; width: 60%;
}
</style>
