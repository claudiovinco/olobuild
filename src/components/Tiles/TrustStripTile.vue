<template>
  <div class="olo-tstrip" :class="{ 'olo-tstrip--pill': isPill }" :style="rowStyle">
    <template v-if="isPill">
      <span v-for="(it, idx) in items" :key="idx" class="olo-tstrip__pill" :style="pillStyle">
        <img v-if="it.logo" :src="it.logo" alt="" :style="{ height: logoH + 'px', width: 'auto', display: 'block', flexShrink: 0 }" />
        <!-- Fallback icona = colore testo pill, come il PHP (in pill l'icona non è "success") -->
        <span v-else-if="it.icon" class="olo-tstrip__icon" :style="{ color: resolveColor(it.icon_color, pillTxtColor) }" v-html="resolveIcon(it.icon)"></span>
        <span v-if="it.text" class="olo-tstrip__pill-txt" :style="pillTextStyle(it)" v-html="it.text"></span>
        <span v-if="it.badge" :style="badgeStyle">{{ it.badge }}</span>
      </span>
    </template>
    <template v-else>
      <template v-for="(it, idx) in items" :key="idx">
        <span v-if="it.text || it.icon" class="olo-tstrip__item" style="display:inline-flex;align-items:center;gap:8px">
          <span v-if="it.icon" class="olo-tstrip__icon" :style="{ color: resolveColor(it.icon_color, TOKENS.success.fg) }" v-html="resolveIcon(it.icon)"></span>
          <span v-if="it.text" v-html="it.text"></span>
        </span>
        <span v-if="s.separator_char && idx < items.length - 1" class="olo-tstrip__sep" :style="{ color: resolveColor(s.separator_color, TOKENS.textFaint), opacity: .65 }">{{ s.separator_char }}</span>
      </template>
    </template>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import iconsSvg from '../ProSlider/iconsLibrary.js';
import { resolveColor, resolveFontFamily, TOKENS } from '@/composables/oloTileDefaults';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });

const defaults = {
  items: [
    { icon: 'check', icon_color: '', text: 'Licenza <b>GPL-v3</b>' },
    { icon: 'check', icon_color: '', text: '<b>WCAG 2.2 AA</b>' },
    { icon: 'check', icon_color: '', text: 'Hosting <b>a scelta tua</b>' },
    { icon: 'check', icon_color: '', text: 'Export <b>HTML/JSON</b> totale' },
    { icon: 'check', icon_color: '', text: 'Trento, <b>Italia 🇮🇹</b>' },
  ],
  separator_char: '·',
  separator_color: '',
  text_color: '',
  text_size: 14,
  font_family: 'sans-serif',
  align: 'center',
  flow: 'wrap',
  gap: 24,
  variant: 'inline',
  logo_height: 18,
  pill_bg: 'rgba(255,255,255,0.05)',
  pill_border: 'rgba(255,255,255,0.12)',
  pill_text_color: '',
  badge_bg: '#D8FF4A',
  badge_color: '#1B2A4E',
};

const s = computed(() => ({ ...defaults, ...props.settings }));
const items = computed(() => Array.isArray(s.value.items) ? s.value.items : []);

const SERIF = "var(--olo-font-family-heading, 'Playfair Display','Cormorant Garamond',Georgia,'Times New Roman',serif)";
const SANS  = "var(--olo-font-family, 'Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif)";
const MONO  = "var(--olo-font-family-mono, ui-monospace,'SF Mono',Menlo,Consolas,monospace)";
// Stack storici della tile per i valori legacy ancora salvati nei template.
const FONT_LEGACY = { serif: SERIF, 'sans-serif': SANS, mono: MONO };

const alignMap = { left: 'flex-start', center: 'center', right: 'flex-end', 'space-between': 'space-between' };

const rowStyle = computed(() => ({
  display: 'flex',
  flexWrap: s.value.flow === 'nowrap' ? 'nowrap' : 'wrap',
  alignItems: 'center',
  justifyContent: alignMap[s.value.align] || 'center',
  gap: (s.value.gap || 24) + 'px',
  fontFamily: resolveFontFamily(s.value.font_family, FONT_LEGACY) || SANS,
  fontSize: (s.value.text_size || 14) + 'px',
  color: resolveColor(s.value.text_color, TOKENS.text),
  lineHeight: 1.5,
}));

const isPill = computed(() => s.value.variant === 'pill');
const logoH = computed(() => s.value.logo_height || 18);
// Colore testo pill effettivo — stessa catena di fallback del PHP
// ($pill_txt = pill_text_color || text_color || token text): un solo valore
// risolto, riusato da testo E icona così canvas e frontend coincidono.
const pillTxtColor = computed(() =>
  resolveColor(s.value.pill_text_color, resolveColor(s.value.text_color, TOKENS.text))
);
const pillStyle = computed(() => ({
  display: 'inline-flex',
  alignItems: 'center',
  gap: '11px',
  padding: '10px 16px',
  borderRadius: '100px',
  background: s.value.pill_bg || 'rgba(255,255,255,0.05)',
  border: '1px solid ' + (s.value.pill_border || 'rgba(255,255,255,0.12)'),
  backdropFilter: 'blur(8px)',
  WebkitBackdropFilter: 'blur(8px)',
  whiteSpace: 'nowrap',
}));
const badgeStyle = computed(() => ({
  fontFamily: MONO,
  fontSize: '9.5px',
  fontWeight: 600,
  letterSpacing: '.06em',
  textTransform: 'uppercase',
  color: s.value.badge_color || '#1B2A4E',
  background: s.value.badge_bg || '#D8FF4A',
  padding: '2px 7px',
  borderRadius: '5px',
}));
function pillTextStyle(it) {
  const st = { color: pillTxtColor.value };
  if (it.logo || it.icon) {
    st.borderLeft = '1px solid ' + (s.value.pill_border || 'rgba(255,255,255,0.12)');
    st.paddingLeft = '11px';
  }
  return st;
}

function resolveIcon(name) {
  if (!name) return '';
  return iconsSvg[name] || '';
}
</script>

<style scoped>
.olo-tstrip__icon { display: inline-flex; align-items: center; }
.olo-tstrip__icon :deep(svg) { width: 0.9em; height: 0.9em; }
.olo-tstrip--pill .olo-tstrip__pill { max-width: 100%; box-sizing: border-box; }
@media (max-width: 768px) {
  .olo-tstrip--pill { flex-direction: column !important; align-items: stretch !important; gap: 8px !important; }
  .olo-tstrip--pill .olo-tstrip__pill { width: 100% !important; max-width: 100% !important; justify-content: center !important; align-items: center !important; flex-wrap: wrap !important; }
  .olo-tstrip--pill .olo-tstrip__pill-txt { border-left: none !important; padding-left: 0 !important; text-align: center; }
}
</style>
