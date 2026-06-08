<template>
  <div class="olo-projector" :class="{ 'is-center': s.align === 'center' }" :style="rootStyle">
    <div class="olo-pj__l">
      <span v-if="s.eyebrow" class="olo-pj__eyebrow">{{ s.eyebrow }}</span>
      <h2 v-if="s.heading" class="olo-pj__h" v-html="s.heading"></h2>
      <p v-if="s.intro" class="olo-pj__intro">{{ s.intro }}</p>
      <div v-if="s.input_label" class="olo-pj__inlabel">{{ s.input_label }}</div>
      <input
        class="olo-pj__range"
        type="range"
        :min="num('min', 0)"
        :max="num('max', 100)"
        :step="num('step', 1)"
        :value="curVal"
        :style="{ '--pct': pct + '%' }"
        :aria-label="s.input_label || 'slider'"
        @input="curVal = parseFloat($event.target.value)"
      />
      <div v-if="s.show_contrib" class="olo-pj__contrib"><span>{{ fmt(curVal) }}</span></div>
    </div>
    <div class="olo-pj__r">
      <div v-if="s.out_caption" class="olo-pj__caption">{{ s.out_caption }}</div>
      <div class="olo-pj__out" aria-live="polite">{{ fmt(projected) }}</div>
      <div v-if="s.note" class="olo-pj__note">{{ s.note }}</div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, watch } from 'vue';
import { resolveColor, TOKENS, SHADOW } from '@/composables/oloTileDefaults';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });

const defaults = {
  eyebrow: 'Una stima', heading: 'La pazienza <em>compone</em>',
  intro: 'Imposta quanto metti da parte ogni anno.',
  min: '2000', max: '50000', step: '1000', value: '12000',
  rate: '0.06', years: '20', currency: '€',
  input_label: 'Investito ogni anno', out_caption: 'Proiezione finale',
  note: 'Solo illustrativo.', show_contrib: true,
  zone_accent: '', align: 'left',
  tile_padding: { top: 48, right: 48, bottom: 48, left: 48 },
  border_radius: '16', shadow: 'sm',
};
const s = computed(() => ({ ...defaults, ...props.settings }));

const num = (k, fb) => { const v = parseFloat(s.value[k]); return isNaN(v) ? fb : v; };

const curVal = ref(num('value', 12000));
// se cambia il valore iniziale da inspector, riallinea l'anteprima
watch(() => s.value.value, () => { curVal.value = num('value', 12000); });

const pct = computed(() => {
  const mn = num('min', 0), mx = num('max', 100);
  if (mx === mn) return 0;
  return Math.max(0, Math.min(100, ((curVal.value - mn) / (mx - mn)) * 100));
});

const projected = computed(() => {
  const c = curVal.value || 0, rate = num('rate', 0), years = num('years', 1);
  return rate === 0 ? c * years : c * ((Math.pow(1 + rate, years) - 1) / rate);
});

function fmt(n) {
  const cur = s.value.currency == null ? '€' : s.value.currency;
  return cur + new Intl.NumberFormat('en-US', { maximumFractionDigits: 0 }).format(Math.round(n || 0));
}

const accent = computed(() => resolveColor(s.value.zone_accent, TOKENS.primary));

const rootStyle = computed(() => {
  const tp = s.value.tile_padding || {};
  const st = {
    '--pj-accent': accent.value,
    '--pj-line': TOKENS.border,
    '--pj-soft': `color-mix(in srgb, ${accent.value} 12%, transparent)`,
    '--pj-surface': TOKENS.surfaceAlt,
    color: 'var(--olo-color-text, #1f2937)',
    background: TOKENS.surfaceAlt,
    padding: `${tp.top ?? 48}px ${tp.right ?? 48}px ${tp.bottom ?? 48}px ${tp.left ?? 48}px`,
    border: `1px solid ${TOKENS.border}`,
  };
  const br = s.value.border_radius;
  st.borderRadius = (br && typeof br === 'object')
    ? `${br.tl||0}px ${br.tr||0}px ${br.br||0}px ${br.bl||0}px`
    : (parseInt(br) || 0) + 'px';
  if (s.value.shadow && s.value.shadow !== 'none' && SHADOW[s.value.shadow]) st.boxShadow = SHADOW[s.value.shadow];
  return st;
});
</script>

<style scoped>
.olo-projector {
  display: grid; grid-template-columns: 1.15fr .85fr; gap: clamp(28px, 4vw, 64px);
  align-items: center;
}
.olo-projector.is-center { text-align: center; }
@media (max-width: 820px) { .olo-projector { grid-template-columns: 1fr; } }
.olo-pj__eyebrow { font-size: 12px; font-weight: 600; letter-spacing: .18em; text-transform: uppercase; color: var(--pj-accent); }
.olo-pj__h { font-size: clamp(28px, 4vw, 46px); line-height: 1.1; margin: 14px 0 0; color: var(--olo-color-text, #1f2937); }
.olo-pj__h :deep(em) { font-style: italic; color: var(--pj-accent); }
.olo-pj__intro { font-size: 15.5px; line-height: 1.6; opacity: .85; margin: 14px 0 26px; max-width: 460px; }
.olo-pj__inlabel { font-size: 12px; font-weight: 600; letter-spacing: .08em; text-transform: uppercase; opacity: .7; margin-bottom: 12px; }
.olo-pj__range { -webkit-appearance: none; appearance: none; width: 100%; height: 6px; border-radius: 99px; cursor: pointer;
  background: linear-gradient(to right, var(--pj-accent) var(--pct, 50%), var(--pj-line) var(--pct, 50%)); }
.olo-pj__range::-webkit-slider-thumb { -webkit-appearance: none; width: 20px; height: 20px; border-radius: 50%;
  background: var(--pj-surface); border: 2px solid var(--pj-accent); box-shadow: 0 1px 4px rgba(16,24,40,.3); cursor: pointer; }
.olo-pj__range::-moz-range-thumb { width: 20px; height: 20px; border-radius: 50%; background: var(--pj-surface); border: 2px solid var(--pj-accent); cursor: pointer; }
.olo-pj__range:focus-visible { outline: 2px solid var(--pj-accent); outline-offset: 4px; }
.olo-pj__contrib { font-size: 26px; font-weight: 600; margin-top: 14px; color: var(--olo-color-text, #1f2937); }
.olo-pj__r { text-align: center; border-left: 1px solid var(--pj-line); padding-left: clamp(18px, 4vw, 48px); }
.is-center .olo-pj__r { border-left: 0; }
@media (max-width: 820px) { .olo-pj__r { border-left: 0; border-top: 1px solid var(--pj-line); padding-left: 0; padding-top: 28px; } }
.olo-pj__caption { font-size: 12px; font-weight: 600; letter-spacing: .08em; text-transform: uppercase; opacity: .7; }
.olo-pj__out { font-size: clamp(36px, 5vw, 60px); font-weight: 700; line-height: 1.05; color: var(--pj-accent); margin: 10px 0 14px; font-variant-numeric: tabular-nums; letter-spacing: -.01em; }
.olo-pj__note { font-size: 11.5px; line-height: 1.5; opacity: .6; max-width: 300px; margin: 0 auto; }
@media (prefers-reduced-motion: reduce) { .olo-pj__range { transition: none; } }
</style>
