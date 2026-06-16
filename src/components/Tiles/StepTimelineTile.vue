<template>
  <div class="olo-stl">
    <div v-if="s.show_timeline" class="olo-stl__timeline" :style="timelineStyle">
      <div :style="lineStyle"></div>
      <span
        v-for="d in dotsCount"
        :key="d"
        :style="dotStyle(d - 1)"
      ></span>
    </div>

    <div class="olo-stl__grid" :style="gridStyle">
      <div
        v-for="(it, idx) in items"
        :key="idx"
        class="olo-stl__item"
        style="display:flex;flex-direction:column;gap:18px;position:relative"
      >
        <div style="display:flex;align-items:flex-end;gap:18px;flex-wrap:wrap">
          <span v-if="it.counter" :style="counterStyle">{{ it.counter }}</span>
          <div v-if="it.tag_text" style="display:inline-flex;align-items:center;gap:8px;padding-bottom:14px">
            <span :style="{ width: '8px', height: '8px', borderRadius: '50%', background: resolveColor(it.tag_dot_color, TOKENS.primary) }"></span>
            <span :style="tagStyle">{{ it.tag_text }}</span>
          </div>
        </div>

        <div class="olo-stl__mockup" :style="mockupStyle(it)">
          <div v-if="s.show_media_label && it.media_label" :style="mockupHeaderStyle(it)">
            <span style="display:inline-flex;gap:4px">
              <span style="width:8px;height:8px;border-radius:50%;background:#ef4444;opacity:.7"></span>
              <span style="width:8px;height:8px;border-radius:50%;background:#f59e0b;opacity:.7"></span>
              <span style="width:8px;height:8px;border-radius:50%;background:#10b981;opacity:.7"></span>
            </span>
            <span style="margin-left:8px">{{ it.media_label }}</span>
          </div>
          <div style="flex:1;padding:14px;display:flex;align-items:center;justify-content:center;overflow:hidden">
            <img v-if="it.media_type === 'image' && it.media_image" :src="it.media_image" :style="{ width: '100%', height: '100%', objectFit: 'cover', objectPosition: (s.media_object_position || 'center center') }" />
            <pre v-else-if="it.media_type === 'terminal' && it.media_content" :style="terminalStyle(it)">{{ it.media_content }}</pre>
            <div v-else style="width:100%;display:flex;flex-direction:column;gap:8px;opacity:.7">
              <span :style="{ height: '8px', background: 'color-mix(in srgb, ' + resolveColor(it.media_color, TOKENS.primary) + ' 20%, transparent)', borderRadius: '4px' }"></span>
              <span :style="{ height: '8px', width: '80%', background: 'color-mix(in srgb, ' + resolveColor(it.media_color, TOKENS.primary) + ' 13%, transparent)', borderRadius: '4px' }"></span>
              <span :style="{ height: '8px', width: '60%', background: 'color-mix(in srgb, ' + resolveColor(it.media_color, TOKENS.primary) + ' 13%, transparent)', borderRadius: '4px' }"></span>
            </div>
          </div>
        </div>

        <div v-if="it.pre_title" :style="preTitleStyle">{{ it.pre_title }}</div>

        <h3 v-if="it.title || it.title_accent || it.title_after" :style="titleStyle">
          <span v-if="it.title">{{ it.title }}</span><template v-if="it.title_accent"> <span :style="{ color: resolveColor(s.title_accent_color, TOKENS.primary), fontStyle: it.title_accent_italic ? 'italic' : 'normal' }">{{ it.title_accent }}</span></template><template v-if="it.title_after"> <span :style="{ fontStyle: it.title_after_italic ? 'italic' : 'normal' }">{{ it.title_after }}</span></template>
        </h3>

        <div v-if="it.description" :style="descStyle" v-html="it.description"></div>

        <div v-if="it.footer_value || it.footer_label" :style="footerStyle">
          <span v-if="s.footer_icon" :style="{ color: resolveColor(s.footer_value_color, TOKENS.text) }" v-html="resolveIcon(s.footer_icon)"></span>
          <span v-if="it.footer_value" :style="footerValueStyle">{{ it.footer_value }}</span>
          <span v-if="it.footer_label" :style="footerLabelStyle">{{ it.footer_label }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import iconsSvg from '../ProSlider/iconsLibrary.js';
import { resolveColor, resolveFontFamily, TOKENS, SHADOW } from '@/composables/oloTileDefaults';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });

const R = (n) => ({ tl: n, tr: n, br: n, bl: n, linked: true });

const defaults = {
  items: [],
  show_timeline: true,
  timeline_line_color: '',
  timeline_dot_color: '',
  timeline_dot_size: 14,
  timeline_height: 3,
  timeline_margin_bottom: 50,
  counter_font_family: 'serif',
  counter_size: 96, counter_color: '', counter_italic: true, counter_weight: '500',
  tag_size: 12, tag_color: '',
  media_aspect_ratio: '5/4',
  media_object_position: 'center center',
  media_radius: R(14),
  media_shadow: 'sm',
  show_media_label: true,
  pre_title_size: 12, pre_title_color: '',
  title_font_family: 'serif', title_size: 30, title_weight: '500',
  title_color: '', title_accent_color: '',
  description_size: 14, description_color: '',
  footer_icon: 'clock', footer_value_size: 18, footer_label_size: 11,
  footer_value_color: '', footer_label_color: '',
  separator_color: '', show_separator: true,
  columns: 3, gap: 32, items_align: 'start',
};

const s = computed(() => ({ ...defaults, ...props.settings }));
const items = computed(() => Array.isArray(s.value.items) ? s.value.items : []);
const dotsCount = computed(() => items.value.length + 1);

const SERIF = "var(--olo-font-family-heading, 'Playfair Display','Cormorant Garamond',Georgia,'Times New Roman',serif)";
const SANS  = "var(--olo-font-family, 'Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif)";
const MONO  = "ui-monospace,'SF Mono',Menlo,Consolas,monospace";
// Stack storici della tile per i valori legacy ancora salvati nei template (IDENTICI al PHP).
const FONT_LEGACY = { serif: SERIF, 'sans-serif': SANS, mono: MONO };

function radiusToCss(r) {
  if (!r) return '0';
  if (typeof r === 'number') return r + 'px';
  return `${r.tl ?? 0}px ${r.tr ?? 0}px ${r.br ?? 0}px ${r.bl ?? 0}px`;
}

const timelineStyle = computed(() => ({
  position: 'relative',
  height: (s.value.timeline_dot_size || 14) + 'px',
  marginBottom: (s.value.timeline_margin_bottom || 50) + 'px',
}));

const lineStyle = computed(() => ({
  position: 'absolute',
  left: 0, right: 0, top: '50%', transform: 'translateY(-50%)',
  height: (s.value.timeline_height || 3) + 'px',
  background: resolveColor(s.value.timeline_line_color, 'color-mix(in srgb, var(--olo-color-primary, #e1474f) 18%, #fff)'),
  borderRadius: (s.value.timeline_height || 3) + 'px',
}));

function dotStyle(i) {
  const n = dotsCount.value;
  const pct = n > 1 ? (i / (n - 1)) * 100 : 50;
  let color = resolveColor(s.value.timeline_dot_color, TOKENS.primary);
  if (i === n - 1 && items.value[items.value.length - 1]?.tag_dot_color) {
    color = items.value[items.value.length - 1].tag_dot_color;
  }
  return {
    position: 'absolute', left: pct + '%', top: '50%',
    transform: 'translate(-50%, -50%)',
    width: (s.value.timeline_dot_size || 14) + 'px',
    height: (s.value.timeline_dot_size || 14) + 'px',
    borderRadius: '50%',
    background: color,
    boxShadow: `0 0 0 4px #fff, 0 0 0 5px color-mix(in srgb, ${color} 20%, transparent)`,
  };
}

const gridStyle = computed(() => ({
  display: 'grid',
  gridTemplateColumns: `repeat(${s.value.columns || 3}, 1fr)`,
  gap: (s.value.gap || 32) + 'px',
  alignItems: s.value.items_align === 'center' ? 'center' : 'flex-start',
  position: 'relative',
}));

const counterStyle = computed(() => ({
  fontFamily: resolveFontFamily(s.value.counter_font_family, FONT_LEGACY) || SERIF,
  fontSize: (s.value.counter_size || 96) + 'px',
  lineHeight: 0.9,
  color: resolveColor(s.value.counter_color, TOKENS.primary),
  fontStyle: s.value.counter_italic ? 'italic' : 'normal',
  fontWeight: s.value.counter_weight || '500',
  letterSpacing: '-0.02em',
}));

const tagStyle = computed(() => ({
  fontFamily: MONO,
  fontSize: (s.value.tag_size || 12) + 'px',
  letterSpacing: '0.08em',
  textTransform: 'uppercase',
  color: resolveColor(s.value.tag_color, TOKENS.text),
}));

function mockupStyle(it) {
  return {
    background: it.media_bg || '#f5efe7',
    borderRadius: radiusToCss(s.value.media_radius),
    aspectRatio: s.value.media_aspect_ratio || '5/4',
    overflow: 'hidden',
    boxShadow: SHADOW[s.value.media_shadow || 'sm'] || SHADOW.sm,
    display: 'flex',
    flexDirection: 'column',
    transition: 'transform .3s ease',
  };
}

function mockupHeaderStyle(it) {
  return {
    padding: '10px 14px',
    background: 'rgba(0,0,0,0.06)',
    fontFamily: MONO,
    fontSize: '10px',
    letterSpacing: '0.1em',
    textTransform: 'uppercase',
    color: (it.media_bg === '#0f172a') ? TOKENS.textFaint : TOKENS.textSoft,
    display: 'flex',
    alignItems: 'center',
    gap: '6px',
  };
}

function terminalStyle(it) {
  return {
    margin: 0, width: '100%', fontFamily: MONO, fontSize: '11px',
    lineHeight: 1.6, color: it.media_color || '#10b981', whiteSpace: 'pre-wrap',
  };
}

const preTitleStyle = computed(() => ({
  fontFamily: MONO,
  fontSize: (s.value.pre_title_size || 12) + 'px',
  letterSpacing: '0.12em',
  textTransform: 'uppercase',
  color: resolveColor(s.value.pre_title_color, TOKENS.textFaint),
}));

const titleStyle = computed(() => ({
  fontFamily: resolveFontFamily(s.value.title_font_family, FONT_LEGACY) || SERIF,
  fontSize: (s.value.title_size || 30) + 'px',
  fontWeight: s.value.title_weight || '500',
  lineHeight: 1.15,
  letterSpacing: '-0.01em',
  color: resolveColor(s.value.title_color, TOKENS.text),
  margin: 0,
}));

const descStyle = computed(() => ({
  fontFamily: SANS,
  fontSize: (s.value.description_size || 14) + 'px',
  lineHeight: 1.55,
  color: resolveColor(s.value.description_color, TOKENS.textSoft),
}));

const footerStyle = computed(() => ({
  display: 'inline-flex', alignItems: 'center', gap: '10px',
  marginTop: '12px', paddingTop: '14px',
  borderTop: '1px solid rgba(15,23,42,0.08)',
}));

const footerValueStyle = computed(() => ({
  fontFamily: resolveFontFamily(s.value.title_font_family, FONT_LEGACY) || SERIF,
  fontSize: (s.value.footer_value_size || 18) + 'px',
  fontWeight: 600,
  color: resolveColor(s.value.footer_value_color, TOKENS.text),
}));

const footerLabelStyle = computed(() => ({
  fontFamily: MONO,
  fontSize: (s.value.footer_label_size || 11) + 'px',
  letterSpacing: '0.08em',
  textTransform: 'uppercase',
  color: resolveColor(s.value.footer_label_color, TOKENS.textFaint),
}));

function resolveIcon(name) {
  return iconsSvg[name] || '';
}
</script>

<style scoped>
.olo-stl__mockup :deep(svg) { width: 0.9em; height: 0.9em; }
</style>
