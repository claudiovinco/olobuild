<template>
  <div class="olo-finder" :class="'olo-finder-preset-' + (s.preset || 'custom')" :style="rootStyle">
    <span v-if="s.eyebrow" class="ofn-eyebrow" :style="eyebrowStyle" data-olo-editable="eyebrow">{{ s.eyebrow }}</span>
    <h2 v-if="s.heading" class="ofn-h" :style="headingStyle" data-olo-editable="heading" v-html="s.heading"></h2>
    <p v-if="s.intro" class="ofn-intro" :style="introStyle" data-olo-editable="intro">{{ s.intro }}</p>
    <div class="ofn-chips" :style="chipsWrap">
      <button v-for="(it, i) in items" :key="'c'+i" type="button" class="ofn-chip"
        :style="chipStyle(i)" @click="active = i" :data-olo-editable="'items.' + i + '.option'">
        <span v-if="it.icon" class="ofn-ic" :style="{ width: '16px', height: '16px', display: 'inline-flex' }" v-html="renderIcon(it.icon)"></span>
        {{ it.option || ('Opzione ' + (i + 1)) }}
      </button>
    </div>
    <div v-if="current" class="ofn-res" :class="{ 'ofn-res--media': hasMedia(current) }" :style="resStyle">
      <template v-if="hasMedia(current)">
        <div class="ofn-media" :style="mediaStyle(current)"><span v-if="!current.image && !itemHasBg(current) && current.media_label" class="ofn-media__lbl">{{ current.media_label }}</span></div>
        <div class="ofn-res__body">
          <span v-if="current.kicker" class="ofn-kicker" :style="kickerStyle">{{ current.kicker }}</span>
          <h3 v-if="current.title" :style="resTitleStyle" :data-olo-editable="'items.' + active + '.title'">{{ current.title }}</h3>
          <p v-if="current.text" :style="resTextStyle" :data-olo-editable="'items.' + active + '.text'">{{ current.text }}</p>
          <div v-if="current.meta" :style="[metaStyle, { marginTop: '14px' }]">{{ current.meta }}</div>
          <a v-if="current.cta_text" :href="current.cta_url || '#'" :style="ctaStyle" @click.prevent>{{ current.cta_text }}</a>
        </div>
      </template>
      <template v-else>
        <span v-if="current.kicker" class="ofn-kicker" :style="kickerStyle">{{ current.kicker }}</span>
        <div v-if="current.meta" :style="metaStyle">{{ current.meta }}</div>
        <h3 v-if="current.title" :style="resTitleStyle" :data-olo-editable="'items.' + active + '.title'">{{ current.title }}</h3>
        <p v-if="current.text" :style="resTextStyle" :data-olo-editable="'items.' + active + '.text'">{{ current.text }}</p>
        <a v-if="current.cta_text" :href="current.cta_url || '#'" :style="ctaStyle" @click.prevent>{{ current.cta_text }}</a>
      </template>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, watch } from 'vue';
import { resolveColor, TOKENS, SHADOW } from '@/composables/oloTileDefaults';
import { buildBgStyle } from '@/composables/useBackgroundStyle';
import { radiusToCss } from '@/composables/useRadius';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });

const defaults = {
  preset: 'custom',
  eyebrow: 'Trova il tuo', heading: 'Da dove vuoi partire?', intro: '',
  items: [
    { option: 'Opzione A', title: 'Risultato A', text: 'Descrizione del risultato.', meta: '', cta_text: '', cta_url: '#', icon: '' },
    { option: 'Opzione B', title: 'Risultato B', text: 'Descrizione del risultato.', meta: '', cta_text: '', cta_url: '#', icon: '' },
    { option: 'Opzione C', title: 'Risultato C', text: 'Descrizione del risultato.', meta: '', cta_text: '', cta_url: '#', icon: '' },
  ],
  zone_accent: '', zone_on: '#ffffff', card_bg: '', card_border: '', media_bg: '', align: 'center',
  default_index: '0', chip_bg: '', chip_radius: '999',
  card_radius: '16', card_padding: { top: 34, right: 38, bottom: 34, left: 38 },
  card_max_width: '680', tile_padding: { top: 0, right: 0, bottom: 0, left: 0 }, shadow: 'none',
};

const s = computed(() => ({ ...defaults, ...props.settings }));
const items = computed(() => Array.isArray(s.value.items) ? s.value.items : []);

const defIdx = computed(() => Math.max(0, Math.min(parseInt(s.value.default_index) || 0, Math.max(0, items.value.length - 1))));
const active = ref(defIdx.value);
watch(defIdx, (v) => { active.value = v; });
const current = computed(() => items.value[Math.min(active.value, items.value.length - 1)] || null);

const SERIF = "var(--olo-font-family-heading, 'Playfair Display',Georgia,serif)";
const SANS = "var(--olo-font-family, 'Inter',-apple-system,sans-serif)";
const accent = computed(() => resolveColor(s.value.zone_accent, TOKENS.primary));
const on = computed(() => resolveColor(s.value.zone_on, '#ffffff'));
const center = computed(() => s.value.align === 'center');
const itemHasBg = (it) => !!(it && it.media_bg && it.media_bg.type && it.media_bg.type !== 'none');
const hasMedia = (it) => !!(it && ((it.image && String(it.image).trim()) || it.media_label || itemHasBg(it)));
const mediaBg = computed(() => resolveColor(s.value.media_bg, 'var(--olo-color-surface-alt, #1e1e1e)'));
function mediaStyle(it) {
  if (itemHasBg(it)) return { backgroundSize: 'cover', backgroundPosition: 'center', ...buildBgStyle(it.media_bg) };
  const img = it && it.image ? String(it.image).trim() : '';
  return {
    background: mediaBg.value,
    backgroundImage: img ? `url(${img})` : 'repeating-linear-gradient(135deg, rgba(255,255,255,.06) 0 16px, transparent 16px 32px)',
    backgroundSize: 'cover', backgroundPosition: 'center',
  };
}
const kickerStyle = computed(() => ({ display: 'block', fontSize: '10.5px', fontWeight: 700, letterSpacing: '.18em', textTransform: 'uppercase', color: accent.value, marginBottom: '6px' }));

function radiusStr(v, fb) {
  if (v && typeof v === 'object') return `${v.tl||0}px ${v.tr||0}px ${v.br||0}px ${v.bl||0}px`;
  const n = parseInt(v);
  return (isNaN(n) ? fb : n) + 'px';
}
function padStr(v, fb) {
  const p = v && typeof v === 'object' ? v : {};
  return `${p.top ?? fb}px ${p.right ?? fb}px ${p.bottom ?? fb}px ${p.left ?? fb}px`;
}

const rootStyle = computed(() => {
  const st = {
    '--fn-accent': accent.value, '--fn-on': on.value,
    fontFamily: SANS, textAlign: center.value ? 'center' : 'left',
  };
  const tp = s.value.tile_padding || {};
  if ((tp.top || 0) + (tp.right || 0) + (tp.bottom || 0) + (tp.left || 0) > 0) st.padding = padStr(tp, 0);
  return st;
});
const eyebrowStyle = computed(() => ({ fontSize: '12px', fontWeight: 700, letterSpacing: '.16em', textTransform: 'uppercase', color: accent.value, display: 'block', marginBottom: '10px' }));
const headingStyle = computed(() => ({ fontFamily: SERIF, fontSize: 'clamp(26px,3.6vw,42px)', lineHeight: 1.12, margin: 0, color: 'var(--olo-color-text,#111827)' }));
const introStyle = computed(() => ({ fontSize: '15.5px', lineHeight: 1.6, opacity: .8, margin: center.value ? '14px auto 0' : '14px 0 0', maxWidth: '560px' }));
const chipsWrap = computed(() => ({ display: 'flex', flexWrap: 'wrap', gap: '10px', margin: '26px 0 24px', justifyContent: center.value ? 'center' : 'flex-start' }));
function chipStyle(i) {
  const onSel = i === active.value;
  // dual-format: Number legacy ('999') E oggetto {tl,tr,br,bl}; '' → 0px come storico
  const r = radiusToCss(s.value.chip_radius, { fallback: '0px' });
  return {
    fontFamily: SANS, fontWeight: 600, fontSize: '13.5px', borderRadius: r, padding: '10px 18px',
    cursor: 'pointer', transition: 'all .18s', display: 'inline-flex', alignItems: 'center', gap: '8px',
    border: '1px solid ' + (onSel ? accent.value : 'var(--olo-color-border,#e5e7eb)'),
    background: onSel ? accent.value : (resolveColor(s.value.chip_bg, 'transparent')),
    color: onSel ? on.value : 'var(--olo-color-text,#111827)',
  };
}
const resStyle = computed(() => {
  const st = {
    background: resolveColor(s.value.card_bg, 'var(--olo-color-surface-alt,#f6f7f9)'),
    border: '1px solid ' + resolveColor(s.value.card_border, 'var(--olo-color-border,#e5e7eb)'),
    borderRadius: radiusStr(s.value.card_radius, 16),
    padding: padStr(s.value.card_padding, 34),
    textAlign: 'left',
    maxWidth: (parseInt(s.value.card_max_width) || 680) + 'px',
    margin: center.value ? '0 auto' : '0',
    boxSizing: 'border-box',
  };
  if (s.value.shadow && s.value.shadow !== 'none' && SHADOW[s.value.shadow]) st.boxShadow = SHADOW[s.value.shadow];
  return st;
});
const metaStyle = computed(() => ({ fontSize: '12px', fontWeight: 700, letterSpacing: '.1em', textTransform: 'uppercase', color: accent.value }));
const resTitleStyle = computed(() => ({ fontFamily: SERIF, fontSize: 'clamp(22px,3vw,30px)', lineHeight: 1.15, margin: '8px 0 0', color: 'var(--olo-color-text,#111827)' }));
const resTextStyle = computed(() => ({ fontSize: '15.5px', lineHeight: 1.6, opacity: .8, margin: '12px 0 0' }));
const ctaStyle = computed(() => ({ display: 'inline-flex', alignItems: 'center', gap: '8px', marginTop: '20px', fontWeight: 600, fontSize: '14px', color: on.value, background: accent.value, padding: '11px 22px', borderRadius: '999px', textDecoration: 'none' }));

function renderIcon(icon) {
  if (!icon) return '';
  const v = String(icon).trim();
  if (v.startsWith('<svg')) return v;
  if (v.startsWith('dashicons-')) return `<span class="dashicons ${v}" style="font-size:16px;width:16px;height:16px;line-height:16px"></span>`;
  return v; // emoji o testo
}
</script>

<style scoped>
.ofn-h :deep(em) { font-style: italic; color: var(--fn-accent); }
.ofn-chip:focus-visible { outline: 2px solid var(--fn-accent); outline-offset: 3px; }
.ofn-res--media { display: flex; gap: 32px; align-items: center; }
.ofn-media { width: 190px; flex: 0 0 auto; aspect-ratio: 190/240; border-radius: 2px; overflow: hidden; position: relative; }
.ofn-media__lbl { position: absolute; left: 12px; bottom: 10px; font-size: 10px; letter-spacing: .04em; text-transform: uppercase; color: rgba(255,255,255,.4); }
.ofn-res__body { flex: 1; min-width: 0; }
@media (max-width: 600px) { .ofn-res--media { flex-direction: column; } .ofn-media { width: 100%; aspect-ratio: auto; height: 240px; } }
</style>
