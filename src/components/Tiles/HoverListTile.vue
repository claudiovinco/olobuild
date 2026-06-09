<template>
  <div class="olo-hoverlist" :style="{ borderTop: '1px solid ' + line }">
    <component
      :is="it.link_url ? 'a' : 'div'"
      v-for="(it, idx) in items"
      :key="idx"
      :href="it.link_url || undefined"
      class="olo-hoverlist__row"
      :style="rowStyle"
    >
      <span class="olo-hoverlist__sw" :style="swStyle(it.color)"></span>
      <span class="olo-hoverlist__nm" :style="nameStyle">{{ it.name }}</span>
      <span v-if="it.sub" class="olo-hoverlist__sub" :style="subStyle">{{ it.sub }}</span>
    </component>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });

const defaults = {
  items: [
    { color: '#9a3b52', name: 'Rosewood', sub: 'Cool · matte', link_url: '' },
    { color: '#c77a6a', name: 'Terracotta', sub: 'Warm · matte', link_url: '' },
    { color: '#e79aa6', name: 'Peony', sub: 'Cool · blush', link_url: '' },
    { color: '#e6a17e', name: 'Apricot', sub: 'Warm · blush', link_url: '' },
    { color: '#7d2e3e', name: 'Merlot', sub: 'Deep · matte', link_url: '' },
  ],
  swatch_size: 26, swatch_shape: 'circle',
  name_font_family: 'heading', name_color: '#f6e9ec', name_size: 22,
  sub_color: '#9c7e8c', sub_size: 12, sub_uppercase: true,
  row_padding_y: 20, hover_indent: 20, hover_bg: '#4d2f40', line_color: 'rgba(246,233,236,.13)',
  peek: true, peek_width: 170, peek_ratio: '4/5', mono_font_family: '',
};

const HEADING = "var(--olo-font-family-heading, 'DM Sans',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif)";
const BODY = "var(--olo-font-family, 'Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif)";
const MONO_FB = "ui-monospace,'SF Mono',Menlo,Consolas,monospace";

const s = computed(() => ({ ...defaults, ...props.settings }));
const items = computed(() => Array.isArray(s.value.items) ? s.value.items : []);
const line = computed(() => s.value.line_color || 'rgba(246,233,236,.13)');
const mono = computed(() => {
  const n = String(s.value.mono_font_family || '').replace(/[^A-Za-z0-9 \-]/g, '').trim();
  return n ? `'${n}',${MONO_FB}` : MONO_FB;
});
const nfam = computed(() => ({ heading: HEADING, body: BODY, mono: mono.value }[s.value.name_font_family] || HEADING));

const rowStyle = computed(() => ({
  display: 'flex', alignItems: 'center', gap: '18px', padding: (s.value.row_padding_y || 20) + 'px 8px',
  borderBottom: '1px solid ' + line.value, color: 'inherit', textDecoration: 'none',
  '--olo-hl-indent': (s.value.hover_indent || 0) + 'px', '--olo-hl-bg': s.value.hover_bg || 'rgba(255,255,255,.05)',
}));
function swStyle(color) {
  return { width: (s.value.swatch_size || 26) + 'px', height: (s.value.swatch_size || 26) + 'px',
    borderRadius: s.value.swatch_shape === 'square' ? '7px' : '50%', flex: 'none', background: color || '#999',
    boxShadow: 'inset 0 0 0 1.5px rgba(246,233,236,.3)' };
}
const nameStyle = computed(() => ({ fontFamily: nfam.value, fontSize: (s.value.name_size || 22) + 'px', color: s.value.name_color || '#f6e9ec', lineHeight: 1.1 }));
const subStyle = computed(() => ({ marginLeft: 'auto', fontFamily: mono.value, fontSize: (s.value.sub_size || 12) + 'px', letterSpacing: '0.06em', color: s.value.sub_color || '#9c7e8c', textTransform: s.value.sub_uppercase ? 'uppercase' : 'none' }));
</script>

<style scoped>
.olo-hoverlist__row { transition: padding .25s ease, background .2s ease; }
.olo-hoverlist__row:hover { padding-left: var(--olo-hl-indent) !important; background: var(--olo-hl-bg); }
</style>
