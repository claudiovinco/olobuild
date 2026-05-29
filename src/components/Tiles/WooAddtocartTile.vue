<template>
  <div :style="wrapStyle">
    <!-- Quantity -->
    <div v-if="s.show_quantity" :style="qtyWrapStyle">
      <template v-if="s.quantity_style === 'stepper'">
        <button :style="stepperBtnStyle">-</button>
        <span :style="stepperValueStyle">1</span>
        <button :style="stepperBtnStyle">+</button>
      </template>
      <template v-else>
        <input type="number" value="1" min="1" :style="qtyInputStyle" readonly />
      </template>
    </div>
    <!-- Button -->
    <button :style="btnStyle">
      <svg
        v-if="s.show_icon"
        width="16"
        height="16"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
        style="flex-shrink:0;"
      >
        <template v-if="s.icon === 'bag'">
          <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/>
          <line x1="3" y1="6" x2="21" y2="6"/>
          <path d="M16 10a4 4 0 01-8 0"/>
        </template>
        <template v-else-if="s.icon === 'plus'">
          <line x1="12" y1="5" x2="12" y2="19"/>
          <line x1="5" y1="12" x2="19" y2="12"/>
        </template>
        <template v-else>
          <circle cx="9" cy="21" r="1"/>
          <circle cx="20" cy="21" r="1"/>
          <path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6"/>
        </template>
      </svg>
      <span data-olo-editable="button_text">{{ s.button_text || 'Aggiungi al carrello' }}</span>
    </button>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { resolveColor, TOKENS } from '@/composables/oloTileDefaults';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  button_text: 'Aggiungi al carrello',
  show_quantity: true,
  show_icon: true,
  icon: 'cart',
  style: 'filled',
  size: 'medium',
  full_width: false,
  bg_color: '',            // '' ⇒ TOKENS.primary (CTA carrello = brand)
  text_color: '',          // '' ⇒ TOKENS.onPrimary
  hover_bg: '',            // '' ⇒ TOKENS.primary
  hover_text: '',          // '' ⇒ TOKENS.onPrimary
  border_radius: '6',
  quantity_style: 'input',
};
const s = computed(() => ({ ...defaults, ...props.settings }));

const sizeMap = {
  small:  { py: 8,  px: 16, fs: 13 },
  medium: { py: 12, px: 24, fs: 15 },
  large:  { py: 16, px: 32, fs: 17 },
};

const sz = computed(() => sizeMap[s.value.size] || sizeMap.medium);

const wrapStyle = computed(() => ({
  display: 'flex',
  alignItems: 'center',
  gap: '10px',
  flexWrap: 'wrap',
}));

const btnStyle = computed(() => {
  const base = {
    display: 'inline-flex',
    alignItems: 'center',
    gap: '8px',
    padding: `${sz.value.py}px ${sz.value.px}px`,
    fontSize: sz.value.fs + 'px',
    fontWeight: 600,
    borderRadius: ((v => isNaN(v) ? 6 : v)(parseInt(s.value.border_radius))) + 'px',
    cursor: 'pointer',
    transition: 'all 0.2s ease',
    border: 'none',
    width: s.value.full_width ? '100%' : 'auto',
    justifyContent: 'center',
  };
  const accent = resolveColor(s.value.bg_color, TOKENS.primary);
  if (s.value.style === 'filled') {
    base.background = accent;
    base.color = resolveColor(s.value.text_color, TOKENS.onPrimary);
  } else if (s.value.style === 'outline') {
    base.background = 'transparent';
    base.color = accent;
    base.border = `2px solid ${accent}`;
  } else {
    base.background = 'transparent';
    base.color = accent;
    base.padding = `${sz.value.py / 2}px 4px`;
  }
  return base;
});

const qtyWrapStyle = computed(() => ({
  display: 'flex',
  alignItems: 'center',
  gap: '0',
}));

const qtyInputStyle = computed(() => ({
  width: '50px',
  height: (sz.value.py * 2 + sz.value.fs) + 'px',
  textAlign: 'center',
  border: '1px solid ' + TOKENS.border,
  borderRadius: ((v => isNaN(v) ? 6 : v)(parseInt(s.value.border_radius))) + 'px',
  fontSize: sz.value.fs + 'px',
  padding: '0 4px',
}));

const stepperBtnStyle = computed(() => ({
  width: (sz.value.py * 2 + sz.value.fs) + 'px',
  height: (sz.value.py * 2 + sz.value.fs) + 'px',
  display: 'flex',
  alignItems: 'center',
  justifyContent: 'center',
  border: '1px solid ' + TOKENS.border,
  background: TOKENS.surfaceAlt,
  fontSize: sz.value.fs + 'px',
  cursor: 'pointer',
}));

const stepperValueStyle = computed(() => ({
  width: '36px',
  textAlign: 'center',
  fontSize: sz.value.fs + 'px',
  borderTop: '1px solid ' + TOKENS.border,
  borderBottom: '1px solid ' + TOKENS.border,
  height: (sz.value.py * 2 + sz.value.fs) + 'px',
  lineHeight: (sz.value.py * 2 + sz.value.fs) + 'px',
}));
</script>
