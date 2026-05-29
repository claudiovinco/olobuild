<template>
  <div :style="wrapStyle">
    <div style="display:flex;align-items:center;gap:2px;">
      <svg
        v-for="star in 5"
        :key="star"
        :width="starSize"
        :height="starSize"
        viewBox="0 0 24 24"
        stroke="none"
      >
        <!-- Full star -->
        <path
          v-if="star <= 4"
          d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"
          :fill="starColor"
        />
        <!-- Half star (5th) -->
        <template v-else>
          <defs>
            <linearGradient :id="'half-' + star">
              <stop offset="50%" :stop-color="starColor" />
              <stop offset="50%" :stop-color="emptyColor" />
            </linearGradient>
          </defs>
          <path
            d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"
            :fill="`url(#half-${star})`"
          />
        </template>
      </svg>
    </div>
    <span v-if="s.show_average || s.show_count" :style="textStyle">
      <template v-if="s.show_average">4.5 su 5</template>
      <template v-if="s.show_average && s.show_count"> - </template>
      <template v-if="s.show_count">12 recensioni</template>
    </span>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { resolveColor, TOKENS } from '@/composables/oloTileDefaults';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  show_count: true,
  show_average: true,
  star_color: '',           // '' ⇒ TOKENS.accent (stelle = ambra)
  empty_star_color: '',     // '' ⇒ TOKENS.border
  text_color: '',           // '' ⇒ TOKENS.textSoft
  star_size: '20',
  text_size: '14',
};
const s = computed(() => ({ ...defaults, ...props.settings }));

const starSize = computed(() => (parseInt(s.value.star_size) || 20) + 'px');
const starColor = computed(() => resolveColor(s.value.star_color, TOKENS.accent));
const emptyColor = computed(() => resolveColor(s.value.empty_star_color, TOKENS.border));

const wrapStyle = computed(() => ({
  display: 'flex',
  alignItems: 'center',
  gap: '10px',
  padding: '8px 0',
}));

const textStyle = computed(() => ({
  color: resolveColor(s.value.text_color, TOKENS.textSoft),
  fontSize: (parseInt(s.value.text_size) || 14) + 'px',
}));
</script>
