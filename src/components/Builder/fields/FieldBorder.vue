<template>
  <div class="olo-border-wrap">

    <!-- Riga style + colore -->
    <div class="olo-border-header">
      <div class="olo-border-style-group">
        <span class="olo-border-hdr-label">{{ t('Stile') }}</span>
        <select class="olo-border-select" :value="val.style" @change="emit({ style: $event.target.value })">
          <option value="solid">{{ t('Solido') }}</option>
          <option value="dashed">{{ t('Tratteggiato') }}</option>
          <option value="dotted">{{ t('Punteggiato') }}</option>
          <option value="double">{{ t('Doppio') }}</option>
          <option value="groove">Groove</option>
          <option value="ridge">Ridge</option>
        </select>
      </div>
      <div class="olo-border-color-group">
        <span class="olo-border-hdr-label">{{ t('Colore') }}</span>
        <div class="olo-border-color-row">
          <div class="olo-border-color-swatch" :style="swatchStyle" @click="focusColorInput">
            <span v-if="!val.color" class="olo-border-color-empty">—</span>
          </div>
          <input
            ref="colorInputRef"
            type="color"
            class="olo-border-color-native"
            :value="colorHex"
            @input="onColorPick($event.target.value)"
          />
          <input
            type="text"
            class="olo-border-color-text"
            :value="val.color"
            :placeholder="t('vuoto')"
            @change="emit({ color: $event.target.value })"
            @blur="emit({ color: $event.target.value })"
          />
        </div>
      </div>
    </div>

    <!-- Croce visiva: top / left·preview·right / bottom -->
    <div class="olo-border-cross">

      <!-- Top -->
      <div class="olo-border-cross-top">
        <input
          type="number"
          @wheel="handleNumberWheel"
          class="olo-border-num"
          :class="{ 'olo-border-num--active': val.top > 0 }"
          :value="val.top"
          @input="onSide('top', $event.target.value)"
          min="0" max="50" step="1"
          :title="t('Bordo superiore (px)')"
        />
        <div class="olo-border-seg olo-border-seg--h" :class="{ 'olo-border-seg--on': val.top > 0 }"></div>
      </div>

      <!-- Riga centrale: left | preview | right -->
      <div class="olo-border-cross-mid">

        <!-- Left -->
        <div class="olo-border-cross-side olo-border-cross-side--l">
          <input
            type="number"
            @wheel="handleNumberWheel"
            class="olo-border-num"
            :class="{ 'olo-border-num--active': val.left > 0 }"
            :value="val.left"
            @input="onSide('left', $event.target.value)"
            min="0" max="50" step="1"
            :title="t('Bordo sinistro (px)')"
          />
          <div class="olo-border-seg olo-border-seg--v" :class="{ 'olo-border-seg--on': val.left > 0 }"></div>
        </div>

        <!-- Preview box -->
        <div class="olo-border-preview" :style="previewStyle">
          <button
            type="button"
            class="olo-border-link-btn"
            :class="{ 'olo-border-link-btn--linked': val.linked }"
            @click="toggleLink"
            :title="val.linked ? t('Scollega lati') : t('Collega lati')"
          >
            <svg v-if="val.linked" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
              <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
            </svg>
            <svg v-else width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M15 7h3a5 5 0 0 1 5 5 5 5 0 0 1-4.46 4.97"/>
              <path d="M9 17H6a5 5 0 0 1-5-5 5 5 0 0 1 4.46-4.97"/>
              <line x1="2" y1="2" x2="22" y2="22"/>
            </svg>
          </button>
        </div>

        <!-- Right -->
        <div class="olo-border-cross-side olo-border-cross-side--r">
          <div class="olo-border-seg olo-border-seg--v" :class="{ 'olo-border-seg--on': val.right > 0 }"></div>
          <input
            type="number"
            @wheel="handleNumberWheel"
            class="olo-border-num"
            :class="{ 'olo-border-num--active': val.right > 0 }"
            :value="val.right"
            @input="onSide('right', $event.target.value)"
            min="0" max="50" step="1"
            :title="t('Bordo destro (px)')"
          />
        </div>

      </div>

      <!-- Bottom -->
      <div class="olo-border-cross-bot">
        <div class="olo-border-seg olo-border-seg--h" :class="{ 'olo-border-seg--on': val.bottom > 0 }"></div>
        <input
          type="number"
          @wheel="handleNumberWheel"
          class="olo-border-num"
          :class="{ 'olo-border-num--active': val.bottom > 0 }"
          :value="val.bottom"
          @input="onSide('bottom', $event.target.value)"
          min="0" max="50" step="1"
          :title="t('Bordo inferiore (px)')"
        />
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { t } from '@/i18n';
import { handleNumberWheel } from '@/utils/numberInputWheel';

const props = defineProps({
  modelValue: { default: null },
});
const emits = defineEmits(['update:modelValue']);

const colorInputRef = ref(null);

const EMPTY = { top: 0, right: 0, bottom: 0, left: 0, linked: true, style: 'solid', color: '' };

const val = computed(() => {
  const v = props.modelValue;
  if (v && typeof v === 'object') {
    return {
      top:    Math.max(0, parseInt(v.top)    || 0),
      right:  Math.max(0, parseInt(v.right)  || 0),
      bottom: Math.max(0, parseInt(v.bottom) || 0),
      left:   Math.max(0, parseInt(v.left)   || 0),
      linked: v.linked !== false,
      style:  v.style  || 'solid',
      color:  v.color  || '',
    };
  }
  return { ...EMPTY };
});

const colorHex = computed(() => {
  const c = val.value.color;
  if (!c) return '#000000';
  if (c.startsWith('#') && c.length === 7) return c;
  if (c.startsWith('#') && c.length === 4) {
    return '#' + c[1]+c[1]+c[2]+c[2]+c[3]+c[3];
  }
  const m = c.match(/rgba?\((\d+),\s*(\d+),\s*(\d+)/);
  if (m) return '#' + [m[1],m[2],m[3]].map(n => parseInt(n).toString(16).padStart(2,'0')).join('');
  return '#000000';
});

const swatchStyle = computed(() => {
  const c = val.value.color;
  return c ? { background: c } : { background: '#f3f4f6' };
});

const previewStyle = computed(() => {
  const { top, right, bottom, left, style, color } = val.value;
  const c = color || '#9ca3af';
  const s = style || 'solid';
  const opacity = color ? 1 : 0.35;
  const t2 = top > 0 ? `${top}px` : '1px';
  const r2 = right > 0 ? `${right}px` : '1px';
  const b2 = bottom > 0 ? `${bottom}px` : '1px';
  const l2 = left > 0 ? `${left}px` : '1px';
  const st = color ? s : 'dashed';
  return {
    borderTop:    `${t2} ${st} ${c}`,
    borderRight:  `${r2} ${st} ${c}`,
    borderBottom: `${b2} ${st} ${c}`,
    borderLeft:   `${l2} ${st} ${c}`,
    opacity,
  };
});

function emit(patch) {
  emits('update:modelValue', { ...val.value, ...patch });
}

function onSide(side, rawVal) {
  const n = Math.max(0, parseInt(rawVal) || 0);
  if (val.value.linked) {
    emit({ top: n, right: n, bottom: n, left: n });
  } else {
    emit({ [side]: n });
  }
}

function toggleLink() {
  if (val.value.linked) {
    emit({ linked: false });
  } else {
    const { top, right, bottom, left } = val.value;
    const max = Math.max(top, right, bottom, left);
    emit({ linked: true, top: max, right: max, bottom: max, left: max });
  }
}

function focusColorInput() {
  colorInputRef.value?.click();
}

function onColorPick(hex) {
  emit({ color: hex });
}
</script>

<style scoped>
.olo-border-wrap {
  padding: 4px 0;
  user-select: none;
}

/* ── Header (style + colore) ── */
.olo-border-header {
  display: flex;
  align-items: flex-end;
  gap: 10px;
  margin-bottom: 10px;
}
.olo-border-hdr-label {
  display: block;
  font-size: 9px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.4px;
  color: #9ca3af;
  margin-bottom: 4px;
}

.olo-border-style-group { flex: 0 0 auto; }
.olo-border-select {
  height: 28px;
  padding: 0 6px;
  border: 1.5px solid #e5e7eb;
  border-radius: 6px;
  font-size: 11.5px;
  color: #374151;
  background: #fff;
  outline: none;
  cursor: pointer;
  transition: border-color 0.15s;
}
.olo-border-select:focus { border-color: #60a5fa; }

.olo-border-color-group { flex: 1 1 auto; min-width: 0; }
.olo-border-color-row {
  position: relative;
  display: flex;
  align-items: center;
  gap: 4px;
  border: 1.5px solid #e5e7eb;
  border-radius: 6px;
  overflow: hidden;
  background: #fff;
  height: 28px;
  transition: border-color 0.15s;
}
.olo-border-color-row:focus-within { border-color: #60a5fa; }

.olo-border-color-swatch {
  position: relative;
  width: 28px;
  height: 100%;
  flex-shrink: 0;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  border-right: 1px solid #e5e7eb;
  transition: filter 0.15s;
}
.olo-border-color-swatch:hover { filter: brightness(0.93); }
.olo-border-color-empty { font-size: 13px; color: #9ca3af; }

/* Native color input: full overlay on the swatch so the OS picker opens
   on direct user click, without needing a programmatic .click() dispatch
   (Chromium recent versions ignore programmatic clicks on size-0 inputs). */
.olo-border-color-native {
  position: absolute;
  inset: 0;
  width: 28px;
  height: 100%;
  opacity: 0;
  cursor: pointer;
  border: none;
  background: transparent;
  padding: 0;
  z-index: 2;
}
.olo-border-color-text {
  flex: 1;
  min-width: 0;
  border: none;
  background: transparent;
  font-size: 11px;
  font-family: 'SF Mono', Monaco, 'Cascadia Code', monospace;
  color: #374151;
  outline: none;
  padding: 0 6px 0 0;
}

/* ── Croce ── */
.olo-border-cross {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 2px;
}

.olo-border-cross-top,
.olo-border-cross-bot {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 2px;
}

.olo-border-cross-mid {
  display: flex;
  align-items: center;
  gap: 2px;
}

.olo-border-cross-side {
  display: flex;
  align-items: center;
  gap: 2px;
}

/* Segmenti lato (indicatore attivo) */
.olo-border-seg {
  background: #e5e7eb;
  border-radius: 2px;
  transition: background 0.2s;
}
.olo-border-seg--h { width: 68px; height: 3px; }
.olo-border-seg--v { width: 3px;  height: 50px; }
.olo-border-seg--on { background: var(--olo-color-primary, #6366f1); }

/* Input numerico lato */
.olo-border-num {
  width: 56px;
  height: 26px;
  background: #f0f4f8;
  border: 1.5px solid #d1d9e6;
  border-radius: 5px;
  font-size: 11px;
  font-weight: 500;
  color: #374151;
  text-align: center;
  outline: none;
  -moz-appearance: textfield;
  padding: 0 4px;
  transition: border-color 0.15s, box-shadow 0.15s, background 0.15s;
}
.olo-border-num::-webkit-inner-spin-button,
.olo-border-num::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
.olo-border-num:focus {
  border-color: #60a5fa;
  box-shadow: 0 0 0 2px rgba(96, 165, 250, 0.25);
  background: #fff;
}
.olo-border-num:hover:not(:focus) { border-color: #93c5fd; background: #fff; }
.olo-border-num--active {
  border-color: var(--olo-color-primary, #6366f1);
  color: var(--olo-color-primary, #6366f1);
  font-weight: 600;
}

/* Preview box centrale */
.olo-border-preview {
  width: 68px;
  height: 50px;
  background: rgba(96, 165, 250, 0.04);
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 4px;
  transition: border 0.15s, opacity 0.2s;
}

/* Bottone link */
.olo-border-link-btn {
  width: 28px;
  height: 28px;
  border-radius: 7px;
  border: 1.5px solid #d1d9e6;
  background: #f0f4f8;
  color: #6b7280;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.15s;
}
.olo-border-link-btn:hover { border-color: #60a5fa; color: #3b82f6; background: #eff6ff; }
.olo-border-link-btn--linked { border-color: #60a5fa; color: #3b82f6; background: #eff6ff; }
</style>
