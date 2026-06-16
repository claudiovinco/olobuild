<template>
  <div class="olo-workgrid" :class="uid" :style="gridStyle">
    <component
      :is="it.link_url ? 'a' : 'div'"
      v-for="(it, idx) in items"
      :key="idx"
      :href="it.link_url || undefined"
      class="olo-workgrid__item"
    >
      <div class="olo-workgrid__media" :style="{ aspectRatio: it.tall ? tallAspect : aspect, overflow: 'hidden', marginBottom: '16px', background: mediaBg }">
        <img v-if="it.image" :src="it.image" :alt="it.title" class="olo-workgrid__img" :style="{ objectPosition: objPos }" />
        <div v-else class="olo-workgrid__ph" :style="phStyle">
          <span v-if="it.media_label" class="olo-workgrid__lbl" :style="labelStyle">{{ it.media_label }}</span>
        </div>
      </div>
      <div class="olo-workgrid__b">
        <h3 v-if="it.title" :style="titleStyle">{{ it.title }}</h3>
        <span v-if="it.meta" class="olo-workgrid__meta" :style="metaStyle">{{ it.meta }}</span>
      </div>
      <p v-if="s.show_desc && it.description" class="olo-workgrid__desc" :style="descStyle" v-html="it.description"></p>
    </component>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { resolveFontFamily } from '@/composables/oloTileDefaults';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });

const defaults = {
  items: [
    { image: '', media_label: 'Marisol — identity system', title: 'Marisol', meta: "'26 — Brand", description: 'A coastal hotel group, rebuilt around one mark and a lot of restraint.', link_url: '', tall: false },
    { image: '', media_label: 'Atlas Press — book covers', title: 'Atlas Press', meta: "'25 — Editorial", description: "An independent publisher's new look, from spine to site.", link_url: '', tall: true },
    { image: '', media_label: 'Field Museum — wayfinding', title: 'Field Museum', meta: "'25 — Wayfinding", description: 'A signage and type system that quietly tells you where you are.', link_url: '', tall: true },
    { image: '', media_label: 'Cobalt — product UI', title: 'Cobalt', meta: "'24 — Product", description: 'Brand and interface for a developer tool that hates noise.', link_url: '', tall: false },
  ],
  columns: 2, items_gap: 32,
  media_aspect: '4/3', media_tall_aspect: '4/5', media_bg: '#ebe7dc', media_label_color: '#18181a', hover_zoom: true, object_position: 'center center',
  title_font_family: 'heading', title_color: '#18181a', title_size: 22, title_weight: '500',
  meta_color: '#8d8a82', meta_size: 12,
  show_desc: true, desc_color: '#8d8a82', desc_size: 15, mono_font_family: '',
};

const HEADING = "var(--olo-font-family-heading, 'DM Sans',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif)";
const BODY = "var(--olo-font-family, 'Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif)";
const MONO_FB = "ui-monospace,'SF Mono',Menlo,Consolas,monospace";

const s = computed(() => ({ ...defaults, ...props.settings }));
const items = computed(() => Array.isArray(s.value.items) ? s.value.items : []);
const uid = 'olo-wg-' + Math.abs(JSON.stringify(s.value.columns) ? 1 : 0); // classe statica (anteprima singola)
const aspect = computed(() => s.value.media_aspect || '4/3');
const tallAspect = computed(() => s.value.media_tall_aspect || '4/5');
const mediaBg = computed(() => s.value.media_bg || '#ebe7dc');
const objPos = computed(() => s.value.object_position || 'center center');
const mono = computed(() => {
  const fam = resolveFontFamily(s.value.mono_font_family);
  if (!fam) return MONO_FB;
  // Nome font puro (legacy campo text) → wrap con lo stack mono di fallback storico.
  return /^[A-Za-z0-9 \-]+$/.test(fam) ? `'${fam}',${MONO_FB}` : fam;
});
const tfam = computed(() => resolveFontFamily(s.value.title_font_family, { heading: HEADING, body: BODY, mono: mono.value }) || HEADING);
const hexAlpha = (hex, a) => {
  const h = String(hex || '').replace('#', '');
  return (h.length === 6 && /^[0-9a-f]{6}$/i.test(h)) ? '#' + h + a : hex;
};

const gridStyle = computed(() => ({ display: 'grid', gridTemplateColumns: `repeat(${Math.max(1, Math.min(4, s.value.columns || 2))},minmax(0,1fr))`, gap: (s.value.items_gap || 32) + 'px' }));
const phStyle = computed(() => ({ width: '100%', height: '100%', position: 'relative', backgroundImage: `repeating-linear-gradient(135deg, ${hexAlpha(s.value.media_label_color, '0d')} 0 15px, transparent 15px 30px)` }));
const labelStyle = computed(() => ({ position: 'absolute', left: '13px', right: '13px', bottom: '11px', fontFamily: mono.value, fontSize: '10.5px', letterSpacing: '.02em', textTransform: 'uppercase', color: hexAlpha(s.value.media_label_color, '66') }));
const titleStyle = computed(() => ({ fontFamily: tfam.value, fontWeight: s.value.title_weight || '500', fontSize: (s.value.title_size || 22) + 'px', letterSpacing: '-0.02em', color: s.value.title_color || '#18181a', margin: 0 }));
const metaStyle = computed(() => ({ flex: 'none', fontFamily: mono.value, fontSize: (s.value.meta_size || 12) + 'px', letterSpacing: '.02em', textTransform: 'uppercase', color: s.value.meta_color || '#8d8a82' }));
const descStyle = computed(() => ({ fontSize: (s.value.desc_size || 15) + 'px', lineHeight: 1.5, color: s.value.desc_color || '#8d8a82', margin: '6px 0 0' }));
</script>

<style scoped>
.olo-workgrid__item { display: block; color: inherit; text-decoration: none; }
.olo-workgrid__b { display: flex; align-items: baseline; justify-content: space-between; gap: 16px; }
.olo-workgrid__img { width: 100%; height: 100%; object-fit: cover; display: block; }
.olo-workgrid__media img, .olo-workgrid__media .olo-workgrid__ph { transition: transform .6s cubic-bezier(.2, .7, .3, 1); }
.olo-workgrid__item:hover .olo-workgrid__media img, .olo-workgrid__item:hover .olo-workgrid__media .olo-workgrid__ph { transform: scale(1.04); }
@media (max-width: 680px) {
  .olo-workgrid { grid-template-columns: 1fr !important; }
}
</style>
