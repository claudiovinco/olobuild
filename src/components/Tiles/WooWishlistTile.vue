<template>
  <div style="padding:10px;background:var(--olo-color-surface-alt, #f6f7f9);border-radius:8px;min-height:60px;display:flex;align-items:center;justify-content:center;">
    <div style="position:relative;display:inline-flex;">
      <svg :style="{ width: iconSize + 'px', height: iconSize + 'px', color: iconColor }" viewBox="0 0 24 24" fill="currentColor" stroke="none">
        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
      </svg>
      <!-- Badge conteggio -->
      <div :style="badgeStyle">{{ s.count || 3 }}</div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { resolveColor, TOKENS } from '@/composables/oloTileDefaults';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  icon_color: '',           // '' ⇒ TOKENS.primary (cuore = brand)
  badge_bg: '',             // '' ⇒ TOKENS.primary
  count: 3,
  icon_size: '32',
};
const s = computed(() => ({ ...defaults, ...props.settings }));

const iconSize = computed(() => parseInt(s.value.icon_size) || 32);
const iconColor = computed(() => resolveColor(s.value.icon_color, TOKENS.primary));

const badgeStyle = computed(() => ({
  position: 'absolute',
  top: '-6px',
  right: '-8px',
  background: resolveColor(s.value.badge_bg, TOKENS.primary),
  color: TOKENS.onPrimary,
  fontSize: '10px',
  fontWeight: 700,
  width: '18px',
  height: '18px',
  borderRadius: '50%',
  display: 'flex',
  alignItems: 'center',
  justifyContent: 'center',
  lineHeight: 1,
}));
</script>
