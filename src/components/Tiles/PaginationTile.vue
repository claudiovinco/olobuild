<template>
  <div class="olo-pagination-preview">
    <nav :style="navStyle">
      <!-- First -->
      <span
        v-if="s.show_first_last && showNumbers"
        class="olo-pgn-btn"
        :style="btnStyle"
      >{{ t('&laquo;') }}</span>

      <!-- Prev -->
      <span
        v-if="showPrevNext"
        class="olo-pgn-btn"
        :style="btnStyle"
        data-olo-editable="prev_text"
      >{{ s.prev_text }}</span>

      <!-- Page numbers -->
      <template v-if="showNumbers">
        <span class="olo-pgn-btn" :style="btnStyle">1</span>
        <span class="olo-pgn-btn olo-pgn-active" :style="activeBtnStyle">2</span>
        <span class="olo-pgn-btn" :style="btnStyle">3</span>
        <span class="olo-pgn-btn" :style="btnStyle">4</span>
        <span class="olo-pgn-btn" :style="btnStyle">5</span>
      </template>

      <!-- Next -->
      <span
        v-if="showPrevNext"
        class="olo-pgn-btn"
        :style="btnStyle"
        data-olo-editable="next_text"
      >{{ s.next_text }}</span>

      <!-- Last -->
      <span
        v-if="s.show_first_last && showNumbers"
        class="olo-pgn-btn"
        :style="btnStyle"
      >&raquo;</span>
    </nav>
  </div>
</template>

<script setup>
import { t } from '@/i18n';
import { computed } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
  tileId: { type: String, default: '' },
});

const defaults = {
  style: 'both',
  alignment: 'center',
  show_first_last: false,
  prev_text: '\u00ab Precedente',
  next_text: 'Successivo \u00bb',
  gap: '8',
  button_padding: '8 16',
  text_color: '',
  active_color: '',
  active_text_color: '',
  background_color: '',
  active_background: '',
  border_radius: '4',
  hover_background: '',
  font_size: '14',
  border_color: '#e5e7eb',
  border_width: '1',
};

const s = computed(() => ({ ...defaults, ...props.settings }));

const showNumbers = computed(() => s.value.style === 'numbered' || s.value.style === 'both');
const showPrevNext = computed(() => s.value.style === 'prev-next' || s.value.style === 'both');

const alignMap = { left: 'flex-start', center: 'center', right: 'flex-end' };

const navStyle = computed(() => ({
  display: 'flex',
  flexWrap: 'wrap',
  justifyContent: alignMap[s.value.alignment] || 'center',
  gap: (parseInt(s.value.gap) || 8) + 'px',
}));

function parsePadding(val) {
  const parts = (val || '8 16').split(/\s+/).map(Number);
  if (parts.length === 1) return parts[0] + 'px';
  if (parts.length === 2) return parts[0] + 'px ' + parts[1] + 'px';
  return val.split(/\s+/).map(v => v + 'px').join(' ');
}

const btnStyle = computed(() => {
  const st = {
    padding: parsePadding(s.value.button_padding),
    fontSize: (parseInt(s.value.font_size) || 14) + 'px',
    borderRadius: ((v => isNaN(v) ? 4 : v)(parseInt(s.value.border_radius))) + 'px',
    lineHeight: '1',
    cursor: 'pointer',
    display: 'inline-flex',
    alignItems: 'center',
    justifyContent: 'center',
    minWidth: '32px',
    textAlign: 'center',
    transition: 'background 0.2s, color 0.2s',
    textDecoration: 'none',
  };
  // Neutro nudo (#9CA3AF) → token testo soft
  if (s.value.text_color) st.color = s.value.text_color;
  else st.color = 'var(--olo-color-text-soft, #6b7280)';
  if (s.value.background_color) st.background = s.value.background_color;
  else st.background = 'transparent';
  const bw = parseInt(s.value.border_width) || 1;
  if (bw > 0) {
    st.border = bw + 'px solid ' + (s.value.border_color || '#e5e7eb');
  } else {
    st.border = 'none';
  }
  return st;
});

const activeBtnStyle = computed(() => {
  // TOKEN-FIRST: voce attiva = primario brand (era #e1474f blu off-brand)
  const activeBg = s.value.active_background || 'var(--olo-color-primary, #e1474f)';
  return {
    ...btnStyle.value,
    color: s.value.active_text_color || 'var(--olo-color-primary-contrast, #ffffff)',
    background: activeBg,
    borderColor: activeBg,
    fontWeight: '600',
  };
});
</script>

<style scoped>
.olo-pagination-preview {
  padding: 8px 0;
  min-height: 40px;
}
/* a11y tastiera: anello di focus visibile sui controlli di paginazione */
.olo-pgn-btn:focus-visible {
  outline: none;
  box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent);
}
</style>
