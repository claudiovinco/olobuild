<template>
  <div class="olo-br-wrap">
    <!-- Visual preview box with corner inputs -->
    <div class="olo-br-preview" :style="previewStyle">
      <!-- Corner inputs positioned at corners -->
      <input
        class="olo-br-input olo-br-input--tl"
        type="number"
        :value="corners.tl"
        @input="onCornerInput('tl', $event.target.value)"
        min="0" step="1"
        @focus="focused = 'tl'"
        @blur="focused = null"
      />
      <input
        class="olo-br-input olo-br-input--tr"
        type="number"
        :value="corners.tr"
        @input="onCornerInput('tr', $event.target.value)"
        min="0" step="1"
        @focus="focused = 'tr'"
        @blur="focused = null"
      />
      <input
        class="olo-br-input olo-br-input--bl"
        type="number"
        :value="corners.bl"
        @input="onCornerInput('bl', $event.target.value)"
        min="0" step="1"
        @focus="focused = 'bl'"
        @blur="focused = null"
      />
      <input
        class="olo-br-input olo-br-input--br"
        type="number"
        :value="corners.br"
        @input="onCornerInput('br', $event.target.value)"
        min="0" step="1"
        @focus="focused = 'br'"
        @blur="focused = null"
      />

      <!-- Center link/unlink button -->
      <button
        class="olo-br-link-btn"
        :class="{ 'olo-br-link-btn--linked': linked }"
        @click="toggleLink"
        :title="linked ? 'Scollega angoli' : 'Collega angoli'"
      >
        <svg v-if="linked" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
          <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
        </svg>
        <svg v-else width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M15 7h3a5 5 0 0 1 5 5 5 5 0 0 1-4.46 4.97"/>
          <path d="M9 17H6a5 5 0 0 1-5-5 5 5 0 0 1 4.46-4.97"/>
          <line x1="2" y1="2" x2="22" y2="22"/>
        </svg>
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';

const props = defineProps({
  modelValue: { default: 0 },
});
const emit = defineEmits(['update:modelValue']);

const focused = ref(null);

// Detect linked/unlinked from modelValue
const linked = ref(!(props.modelValue && typeof props.modelValue === 'object'));

// Parse 4 corners from modelValue
const corners = computed(() => {
  const v = props.modelValue;
  if (v && typeof v === 'object') {
    return { tl: v.tl || 0, tr: v.tr || 0, br: v.br || 0, bl: v.bl || 0 };
  }
  const n = parseInt(String(v || '0')) || 0;
  return { tl: n, tr: n, br: n, bl: n };
});

// Preview style — the box shows the actual border-radius
const previewStyle = computed(() => {
  const c = corners.value;
  return {
    borderRadius: `${c.tl}px ${c.tr}px ${c.br}px ${c.bl}px`,
  };
});

// Re-detect linked state when modelValue changes externally
watch(() => props.modelValue, (val) => {
  linked.value = !(val && typeof val === 'object');
}, { immediate: false });

function nonNeg(n) {
  return Math.max(0, parseInt(n) || 0);
}

function onCornerInput(corner, val) {
  const n = nonNeg(val);
  if (linked.value) {
    // All corners change together
    emit('update:modelValue', n);
  } else {
    emit('update:modelValue', { ...corners.value, [corner]: n });
  }
}

function toggleLink() {
  if (linked.value) {
    // Unlink
    linked.value = false;
    const c = corners.value;
    emit('update:modelValue', { tl: nonNeg(c.tl), tr: nonNeg(c.tr), br: nonNeg(c.br), bl: nonNeg(c.bl) });
  } else {
    // Relink — use max corner
    linked.value = true;
    const c = corners.value;
    const max = nonNeg(Math.max(c.tl, c.tr, c.br, c.bl));
    emit('update:modelValue', max);
  }
}
</script>

<style scoped>
.olo-br-wrap {
  padding: 4px 0;
  max-width: 200px;
}

.olo-br-preview {
  position: relative;
  width: 100%;
  max-width: 200px;
  aspect-ratio: 16 / 10;
  border: 2.5px solid #60a5fa;
  background: rgba(96, 165, 250, 0.06);
  transition: border-radius 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* Corner inputs */
.olo-br-input {
  position: absolute;
  width: 48px;
  height: 26px;
  background: #f0f4f8;
  border: 1.5px solid #d1d9e6;
  border-radius: 5px;
  font-size: 11px;
  font-weight: 500;
  color: #374151;
  text-align: center;
  outline: none;
  transition: border-color 0.15s, box-shadow 0.15s, background 0.15s;
  -moz-appearance: textfield;
  padding: 0 2px;
}

.olo-br-input::-webkit-inner-spin-button,
.olo-br-input::-webkit-outer-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

.olo-br-input:focus {
  border-color: #60a5fa;
  box-shadow: 0 0 0 2px rgba(96, 165, 250, 0.25);
  background: #fff;
}

.olo-br-input:hover:not(:focus) {
  border-color: #93c5fd;
  background: #fff;
}

/* Corner positions */
.olo-br-input--tl { top: 6px; left: 6px; }
.olo-br-input--tr { top: 6px; right: 6px; }
.olo-br-input--bl { bottom: 6px; left: 6px; }
.olo-br-input--br { bottom: 6px; right: 6px; }

/* Center link button */
.olo-br-link-btn {
  width: 32px;
  height: 32px;
  border-radius: 8px;
  border: 1.5px solid #d1d9e6;
  background: #f0f4f8;
  color: #6b7280;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.15s;
}

.olo-br-link-btn:hover {
  border-color: #60a5fa;
  color: #3b82f6;
  background: #eff6ff;
}

.olo-br-link-btn--linked {
  border-color: #60a5fa;
  color: #3b82f6;
  background: #eff6ff;
}
</style>
