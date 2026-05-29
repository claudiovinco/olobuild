<template>
  <div style="padding:10px;background:var(--olo-color-surface-alt, #f6f7f9);border-radius:8px;min-height:60px;">
    <!-- Titolo bundle -->
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px;">
      <div :style="{ fontSize: '15px', fontWeight: 700, color: s.title_color || 'var(--olo-color-text, #374151)' }" data-olo-editable="bundle_title">
        {{ s.bundle_title || 'Offerta Bundle' }}
      </div>
      <!-- Badge sconto -->
      <div :style="discountStyle" data-olo-editable="discount_text">
        {{ s.discount_text || '-15%' }}
      </div>
    </div>

    <!-- Prodotti nel bundle -->
    <div style="display:flex;gap:10px;align-items:center;">
      <div
        v-for="i in 3"
        :key="i"
        style="flex:1;background:var(--olo-color-surface, #ffffff);border-radius:6px;overflow:hidden;border:1px solid var(--olo-color-border, #e5e7eb);"
      >
        <div style="width:100%;padding-top:70%;background:var(--olo-color-surface-alt, #f6f7f9);position:relative;">
          <svg style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:20px;height:20px;opacity:0.25;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <rect x="3" y="3" width="18" height="18" rx="2"/>
            <circle cx="8.5" cy="8.5" r="1.5"/>
            <path d="M21 15l-5-5L5 21"/>
          </svg>
        </div>
        <div style="padding:6px;text-align:center;">
          <div style="font-size:11px;color:var(--olo-color-text-soft, #6b7280);">Prodotto {{ i }}</div>
        </div>
      </div>
      <!-- Plus signs between items -->
      <template v-if="false"></template>
    </div>

    <!-- Prezzo bundle -->
    <div style="margin-top:10px;display:flex;align-items:center;justify-content:space-between;">
      <div style="display:flex;align-items:center;gap:8px;">
        <span style="text-decoration:line-through;color:var(--olo-color-text-faint, #94a3b8);font-size:13px;">€ 120,00</span>
        <span style="font-weight:700;font-size:16px;color:var(--olo-color-success, #15803d);">€ 102,00</span>
      </div>
      <button :style="btnStyle">{{ t('Aggiungi bundle') }}</button>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';
import { resolveColor, TOKENS } from '@/composables/oloTileDefaults';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  bundle_title: 'Offerta Bundle',
  discount_text: '-15%',
  title_color: 'var(--olo-color-text, #374151)',
  discount_bg: '',          // '' ⇒ TOKENS.success (risparmio = stato positivo)
  button_bg: 'var(--olo-color-primary, #e1474f)',
  button_color: '',         // '' ⇒ TOKENS.onPrimary
};
const s = computed(() => ({ ...defaults, ...props.settings }));

const discountStyle = computed(() => ({
  background: resolveColor(s.value.discount_bg, TOKENS.success.fg),
  color: TOKENS.onPrimary,
  fontSize: '11px',
  fontWeight: 700,
  padding: '2px 10px',
  borderRadius: '12px',
}));

const btnStyle = computed(() => ({
  padding: '6px 14px',
  background: resolveColor(s.value.button_bg, TOKENS.primary),
  color: resolveColor(s.value.button_color, TOKENS.onPrimary),
  border: 'none',
  borderRadius: '4px',
  fontSize: '12px',
  fontWeight: 600,
  cursor: 'pointer',
}));
</script>
