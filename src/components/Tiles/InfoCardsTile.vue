<template>
  <div class="olo-icards" :class="hoverClass" :style="containerStyle">
    <div class="olo-icards__grid" :style="gridStyle">
      <component
        v-for="(it, idx) in items"
        :key="idx"
        :is="it.link_url ? 'a' : 'div'"
        :href="it.link_url || undefined"
        :class="['olo-icards__card', 'olo-icards__card--' + idx]"
        :style="cardStyle"
      >
        <div v-if="s.show_media" class="olo-icards__media" :style="mediaStyle">
          <img v-if="it.media_image" :src="it.media_image" alt="" :style="{ width: '100%', height: '100%', objectFit: 'cover', display: 'block' }" />
          <span v-else-if="it.media_label" :style="mediaLabelStyle">{{ it.media_label }}</span>
        </div>
        <div v-if="s.show_icon || s.show_counter || s.show_arrow" class="olo-icards__top">
          <div class="olo-icards__icon-wrap" :style="{ color: cardColor }">
            <span v-if="s.show_icon && it.icon" v-html="resolveIcon(it.icon)"></span>
          </div>
          <div class="olo-icards__right-row">
            <span v-if="s.show_counter && it.counter" class="olo-icards__counter" :style="counterStyle">
              {{ it.counter }}<span v-if="s.show_counter_label && it.counter_label"> / {{ it.counter_label }}</span>
            </span>
            <span v-if="s.show_arrow" class="olo-icards__arrow" :style="{ color: cardColor, borderColor: cardColor + '33' }">→</span>
          </div>
        </div>

        <div v-if="it.title || it.title_accent" class="olo-icards__title" :style="titleStyle">
          <template v-if="it.title">{{ it.title }}</template>
          <span v-if="it.title_accent" :style="{ fontSize: '.45em', verticalAlign: 'baseline', marginLeft: '0.05em', fontStyle: it.title_accent_italic ? 'italic' : 'normal' }">{{ it.title_accent }}</span>
        </div>

        <div v-if="it.description" class="olo-icards__desc" :style="descStyle" v-html="it.description"></div>

        <div v-if="s.show_divider" class="olo-icards__divider" :style="{ background: cardColor + '1a' }"></div>

        <div v-if="s.show_footer && it.footer_text" class="olo-icards__footer" :style="footerStyle">
          <span class="olo-icards__dot" :style="{ background: it.footer_dot_color || '#10b981' }"></span>
          <span>{{ it.footer_text }}</span>
        </div>
      </component>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import iconsSvg from '../ProSlider/iconsLibrary.js';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });

const R = (n) => ({ tl: n, tr: n, br: n, bl: n, linked: true });

const defaults = {
  container_bg:      { type: 'solid', color: '#0f172a' },
  container_radius:  R(24),
  container_padding: 12,
  container_gap:     0,
  columns:           3,
  items_gap:         0,
  items: [
    { counter: '01', counter_label: 'Carta',         title: 'Zero',    title_accent: '',   title_accent_italic: true, description: 'Niente <strong>carta di credito</strong> per scaricare e provare. Niente trial scaduto, niente sblocchi nascosti.', icon: '', footer_dot_color: '#10b981', footer_text: '', link_url: '' },
    { counter: '02', counter_label: 'Registrazione', title: 'Niente',  title_accent: '',   title_accent_italic: true, description: 'Nessuna <strong>registrazione obbligatoria</strong>.', icon: '', footer_dot_color: '#10b981', footer_text: '', link_url: '' },
    { counter: '03', counter_label: 'Pro',           title: '30',      title_accent: 'gg', title_accent_italic: false, description: '<strong>Soddisfatti o rimborsati</strong> su OLObuild Pro.', icon: '', footer_dot_color: '#10b981', footer_text: '', link_url: '' },
  ],
  card_bg:           { type: 'solid', color: '#0f172a' },
  card_color:        '#e5e7eb',
  card_accent_color: '',
  card_radius:       R(18),
  card_padding:      40,
  card_border:       '',
  show_icon: false, show_counter: true, show_counter_label: true,
  show_arrow: true, show_footer: false, show_divider: false,
  show_media: false, media_aspect_ratio: '4/3', media_radius: R(18),
  title_font_family: 'serif',
  title_size: 72, title_weight: '500', title_italic: true,
  counter_size: 11, description_size: 15, footer_size: 10,
  card_hover_effect: 'none',
};

const s = computed(() => ({ ...defaults, ...props.settings }));

const SERIF = "'Playfair Display','Cormorant Garamond',Georgia,'Times New Roman',serif";
const SANS  = "'Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif";
const MONO  = "ui-monospace,'SF Mono',Menlo,Consolas,monospace";
const fmap  = { serif: SERIF, 'sans-serif': SANS, mono: MONO };

const items = computed(() => Array.isArray(s.value.items) ? s.value.items : []);
const cardColor = computed(() => s.value.card_color || '#e5e7eb');
const accentColor = computed(() => s.value.card_accent_color || 'var(--olo-color-primary, #e1474f)');
const tfam = computed(() => fmap[s.value.title_font_family] || SERIF);
const hoverClass = computed(() => 'olo-icards-hover-' + (s.value.card_hover_effect || 'none'));

function bgToCss(bg, fallback = '') {
  if (!bg || bg.type === 'none') return fallback;
  if (bg.type === 'solid')    return `background:${bg.color || fallback || 'transparent'};`;
  if (bg.type === 'gradient') {
    const a = bg.gradient_angle ?? 135;
    return `background:linear-gradient(${a}deg, ${bg.gradient_from || '#fff'}, ${bg.gradient_to || '#000'});`;
  }
  if (bg.type === 'image' && bg.image_url) {
    return `background-image:url(${bg.image_url});background-size:${bg.image_size || 'cover'};background-position:${bg.image_position || 'center'};background-repeat:${bg.image_repeat || 'no-repeat'};`;
  }
  return fallback;
}

function radiusToCss(r) {
  if (!r || typeof r !== 'object') return r ? r + 'px' : '';
  const tl = r.tl ?? 0, tr = r.tr ?? 0, br = r.br ?? 0, bl = r.bl ?? 0;
  if (!tl && !tr && !br && !bl) return '';
  return `${tl}px ${tr}px ${br}px ${bl}px`;
}

function parseCss(str) {
  const out = {};
  (str || '').split(';').forEach(d => {
    const i = d.indexOf(':');
    if (i < 0) return;
    const prop = d.slice(0, i).trim();
    const val = d.slice(i + 1).trim();
    if (prop) out[prop.replace(/-([a-z])/g, (_, c) => c.toUpperCase())] = val;
  });
  return out;
}

const containerStyle = computed(() => ({
  ...parseCss(bgToCss(s.value.container_bg, 'background:#0f172a;')),
  borderRadius: radiusToCss(s.value.container_radius) || '0',
  padding: (s.value.container_padding || 0) + 'px',
}));

const gridStyle = computed(() => ({
  display: 'grid',
  gridTemplateColumns: `repeat(${s.value.columns || 3}, 1fr)`,
  gap: (s.value.items_gap || 0) + 'px',
}));

const cardStyle = computed(() => {
  const out = {
    ...parseCss(bgToCss(s.value.card_bg, 'background:#0f172a;')),
    color: cardColor.value,
    borderRadius: radiusToCss(s.value.card_radius) || '0',
    padding: (s.value.card_padding || 0) + 'px',
    position: 'relative',
    display: 'flex',
    flexDirection: 'column',
    minHeight: '280px',
    transition: 'transform .3s ease, box-shadow .3s ease, border-color .3s ease',
    textDecoration: 'none',
  };
  if (s.value.card_border) out.border = '1px solid ' + s.value.card_border;
  return out;
});

const counterStyle = computed(() => ({
  fontFamily: MONO,
  fontSize: (s.value.counter_size || 11) + 'px',
  letterSpacing: '0.08em',
  textTransform: 'uppercase',
  color: cardColor.value,
  opacity: 0.6,
}));

const titleStyle = computed(() => ({
  fontFamily: tfam.value,
  fontSize: (s.value.title_size || 72) + 'px',
  lineHeight: 1.05,
  fontWeight: s.value.title_weight || '500',
  color: accentColor.value,
  fontStyle: s.value.title_italic ? 'italic' : 'normal',
  letterSpacing: '-0.02em',
  marginBottom: '20px',
}));

const descStyle = computed(() => ({
  fontFamily: SANS,
  fontSize: (s.value.description_size || 15) + 'px',
  lineHeight: 1.55,
  color: cardColor.value,
  flex: 1,
}));

const mediaStyle = computed(() => ({
  width: '100%',
  aspectRatio: s.value.media_aspect_ratio || '4/3',
  borderRadius: radiusToCss(s.value.media_radius) || '0',
  overflow: 'hidden',
  background: cardColor.value + '14',
  border: `1px solid ${cardColor.value}22`,
  display: 'flex',
  alignItems: 'center',
  justifyContent: 'center',
  marginBottom: '28px',
}));

const mediaLabelStyle = computed(() => ({
  fontFamily: MONO,
  fontSize: '11px',
  letterSpacing: '0.12em',
  color: cardColor.value,
  opacity: 0.45,
  textTransform: 'uppercase',
}));

const footerStyle = computed(() => ({
  display: 'inline-flex',
  alignItems: 'center',
  gap: '10px',
  marginTop: s.value.show_divider ? '0' : '24px',
  fontFamily: MONO,
  fontSize: (s.value.footer_size || 10) + 'px',
  letterSpacing: '0.08em',
  textTransform: 'uppercase',
  color: cardColor.value,
  opacity: 0.7,
}));

function resolveIcon(name) {
  if (!name) return '';
  return iconsSvg[name] || '';
}
</script>

<style scoped>
.olo-icards__top {
  display: flex; align-items: flex-start; justify-content: space-between;
  margin-bottom: 48px; min-height: 36px;
}
.olo-icards__icon-wrap { display: flex; align-items: center; gap: 10px; flex: 1; font-size: 1.8em; }
.olo-icards__icon-wrap :deep(svg) { width: 1.8em; height: 1.8em; }
.olo-icards__right-row { display: flex; align-items: center; gap: 14px; }
.olo-icards__arrow {
  width: 34px; height: 34px; border-radius: 50%;
  border: 1px solid currentColor;
  display: inline-flex; align-items: center; justify-content: center;
  font-size: 14px; opacity: 0.7;
}
.olo-icards__divider { height: 1px; margin: 24px 0 18px; }
.olo-icards__dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; }

/* Hover effects */
.olo-icards-hover-lift .olo-icards__card:hover { transform: translateY(-6px); box-shadow: 0 14px 36px rgba(0,0,0,0.15); }
.olo-icards-hover-scale .olo-icards__card:hover { transform: scale(1.03); z-index: 2; }
.olo-icards-hover-tilt .olo-icards__card { transform-style: preserve-3d; }
.olo-icards-hover-tilt .olo-icards__card:hover { transform: perspective(800px) rotateX(2deg) rotateY(-2deg) scale(1.02); }
</style>
