<template>
  <div style="display:inline-flex;flex-direction:column;gap:8px;max-width:340px;">
    <!-- Marker badge -->
    <div :style="markerStyle">
      <div style="display:flex;align-items:center;gap:6px;">
        <span style="flex-shrink:0;display:inline-flex;color:#fbbf24;" v-html="flagSvg"></span>
        <span style="font-weight:700;color:#fbbf24;font-size:13px;">{{ t('Hidden Pop') }}</span>
      </div>
      <div style="color:#fde68a;font-size:11px;padding-left:22px;">
        Attivazione: scroll {{ s.trigger_direction === 'up' ? '\u2191' : s.trigger_direction === 'both' ? '\u2195' : '\u2193' }} al {{ s.trigger_threshold || 50 }}% viewport
      </div>
      <div v-if="s.popup_frequency !== 'always'" style="color:#d97706;font-size:10px;padding-left:22px;">
        {{ freqLabel }}
      </div>
    </div>

    <!-- Mini popup preview -->
    <div :style="previewStyle">
      <div style="position:absolute;top:-8px;left:12px;background:#78350f;padding:0 6px;font-size:9px;color:#fbbf24;font-weight:600;border-radius:3px;">{{ t('POPUP PREVIEW') }}</div>

      <!-- Close X -->
      <div v-if="s.modal_close_button !== false" style="position:absolute;top:8px;right:10px;color:#9ca3af;font-size:16px;font-weight:bold;line-height:1;">{{ t('&times;') }}</div>

      <template v-if="s.mode === 'template'">
        <div style="font-size:11px;color:#9ca3af;text-align:center;padding:12px 0;">
          {{ t('Template Olobuild') }}
        </div>
      </template>
      <template v-else>
        <!-- Image top -->
        <div v-if="s.image && (s.image_position === 'top' || !s.image_position)"
          :style="{ margin: '-12px -16px 8px', borderRadius: '12px 12px 0 0', overflow: 'hidden', ...previewWrapStyle }">
          <img :src="s.image" :style="previewImgStyle" />
        </div>

        <div v-if="s.title" :style="titlePreviewStyle" data-olo-editable="title">{{ s.title }}</div>
        <div v-if="s.subtitle" style="font-size:11px;color:#6b7280;margin-top:4px;line-height:1.4;" data-olo-editable="subtitle">{{ subtitlePreview }}</div>

        <!-- CTA button -->
        <div v-if="s.cta_text" style="margin-top:10px;">
          <span :style="ctaBtnStyle">{{ s.cta_text }}</span>
        </div>

        <!-- Image bottom -->
        <div v-if="s.image && s.image_position === 'bottom'"
          :style="{ margin: '8px -16px -12px', borderRadius: '0 0 12px 12px', overflow: 'hidden', ...previewWrapStyle }">
          <img :src="s.image" :style="previewImgStyle" />
        </div>
      </template>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { imageFrame } from '@/composables/useImageFrame';
import { t } from '@/i18n';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

// Icona bandierina SVG (sostituisce l'emoji 🚩 dell'hint builder)
const flagSvg = '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/><line x1="4" y1="22" x2="4" y2="15"/></svg>';

const s = computed(() => ({
  mode: 'simple',
  title: 'Titolo popup',
  subtitle: '',
  image: '',
  image_position: 'top',
  cta_text: '',
  cta_style: 'primary',
  trigger_threshold: 50,
  trigger_direction: 'down',
  popup_frequency: 'always',
  modal_close_button: true,
  modal_bg_color: '#ffffff',
  title_color: '#111827',
  title_size: '24',
  text_color: '#4b5563',
  ...props.settings,
}));

const subtitlePreview = computed(() => {
  const t = s.value.subtitle || '';
  return t.length > 80 ? t.slice(0, 80) + '\u2026' : t;
});

// ─── La cornice dell'immagine nell'anteprima del popup ───
//
// L'anteprima qui dentro è uno SCHIZZO del popup, non una copia in scala: il popup
// vero (class-hiddenpop-tile.php) con «Proporzioni → Auto» mostra la foto intera
// sopra/sotto, il canvas la incastra da sempre in una fascia di 80px. Quella fascia
// resta: cambiarla sposterebbe la geometria di ogni popup già costruito.
// Quando però l'utente SCEGLIE una proporzione, il cap fisso va tolto, altrimenti
// l'overflow:hidden del contenitore ritaglierebbe di nuovo a 80px e del ritaglio
// scelto non si vedrebbe niente: il controllo sembrerebbe inerte proprio nel posto
// in cui lo si sta muovendo.
const previewRatio = computed(
  () => imageFrame(s.value, 'aspect', { ratio: 'auto' }).contenitore.aspectRatio || ''
);

// Il punto focale di hiddenpop sta sulla chiave PIATTA `object_position` (storica,
// mai migrata): è quella che il PHP legge e quella che i popup pubblicati hanno
// già salvata. NON è `aspect_object_position`, quindi non passa dall'helper.
const previewPos = computed(() => {
  const p = String(s.value.object_position ?? 'center center').trim();
  // Stessa difesa breakout del gemello useImageFrame.js: è un campo di testo libero.
  return p && !/[;{}()]/.test(p) ? p : 'center center';
});

const previewWrapStyle = computed(() =>
  (previewRatio.value ? { aspectRatio: previewRatio.value } : { maxHeight: '80px' })
);

const previewImgStyle = computed(() => ({
  width: '100%',
  height: previewRatio.value ? '100%' : '80px',
  objectFit: 'cover',
  // Solo con una proporzione attiva, come fa il sito: con «Auto» il PHP emette
  // `width:100%;height:auto` e basta (class-hiddenpop-tile.php, $img_crop solo se
  // il rapporto c'e'), quindi muovere qui il punto focale mostrerebbe nel builder
  // un'inquadratura che sulla pagina non succede.
  objectPosition: previewRatio.value ? previewPos.value : undefined,
  display: 'block',
}));

const freqLabel = computed(() => {
  const map = {
    once_session: '1x per sessione',
    once_day: '1x al giorno',
    once_week: '1x a settimana',
    once_ever: 'Solo una volta',
  };
  return map[s.value.popup_frequency] || '';
});

const markerStyle = computed(() => ({
  display: 'inline-flex',
  flexDirection: 'column',
  gap: '3px',
  background: '#78350f',
  color: '#fde68a',
  border: '1px dashed #f59e0b',
  borderRadius: '8px',
  padding: '10px 16px',
  fontSize: '12px',
  lineHeight: '1.4',
  fontFamily: 'system-ui, sans-serif',
}));

const previewStyle = computed(() => ({
  position: 'relative',
  background: s.value.modal_bg_color || '#fff',
  border: '1px solid #e5e7eb',
  borderRadius: '12px',
  padding: '12px 16px',
  maxWidth: '300px',
  minWidth: '200px',
}));

const titlePreviewStyle = computed(() => ({
  fontSize: Math.min(parseInt(s.value.title_size) || 24, 20) + 'px',
  fontWeight: '700',
  color: s.value.title_color || '#111827',
  lineHeight: '1.3',
}));

const ctaStyleMap = {
  primary:   { bg: 'var(--olo-color-primary, #e1474f)', color: 'var(--olo-color-primary-contrast, #fff)' },
  secondary: { bg: 'var(--olo-color-surface-alt, #e5e7eb)', color: 'var(--olo-color-text, #111)' },
  danger:    { bg: 'var(--olo-color-danger, #dc2626)', color: '#fff' },
  text:      { bg: 'transparent', color: 'var(--olo-color-primary, #e1474f)' },
};

const ctaBtnStyle = computed(() => {
  const st = ctaStyleMap[s.value.cta_style] || ctaStyleMap.primary;
  return {
    display: 'inline-block',
    background: st.bg,
    color: st.color,
    padding: '6px 18px',
    fontSize: '12px',
    fontWeight: '600',
    borderRadius: '4px',
    cursor: 'default',
  };
});
</script>
