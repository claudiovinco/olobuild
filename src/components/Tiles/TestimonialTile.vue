<template>
  <div class="mb-p-6" :style="wrapStyle">
    <!-- Layout: left -->
    <div v-if="s.author_position === 'left'" class="mb-flex mb-gap-5 mb-items-start">
      <div class="mb-shrink-0">
        <AuthorBlock :s="s" :avatarStyle="avatarStyle" />
      </div>
      <div class="mb-flex-1 mb-min-w-0">
        <QuoteBlock :s="s" :rating="rating" :lineStyle="lineStyle" />
      </div>
    </div>

    <!-- Layout: right -->
    <div v-else-if="s.author_position === 'right'" class="mb-flex mb-gap-5 mb-items-start">
      <div class="mb-flex-1 mb-min-w-0">
        <QuoteBlock :s="s" :rating="rating" :lineStyle="lineStyle" />
      </div>
      <div class="mb-shrink-0">
        <AuthorBlock :s="s" :avatarStyle="avatarStyle" />
      </div>
    </div>

    <!-- Layout: bottom-left / bottom-center / bottom-right -->
    <div v-else>
      <QuoteBlock :s="s" :rating="rating" :lineStyle="lineStyle" />
      <div class="mb-mt-5" :style="bottomAlignStyle">
        <AuthorBlock :s="s" :avatarStyle="avatarStyle" horizontal />
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, h } from 'vue';
import { resolveColor, TOKENS } from '@/composables/oloTileDefaults';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

// Default colore = '' ⇒ risolti token-first a runtime (allineato a config testimonial.js)
const s = computed(() => ({
  quote: 'Un prodotto fantastico!',
  author_name: 'Mario Rossi',
  author_role: 'CEO',
  avatar: '',
  rating: '5',
  bg_color: '',
  text_color: '',
  show_line: true,
  line_color: '',
  author_position: 'bottom-left',
  avatar_size: '48',
  avatar_shape: 'circle',
  avatar_radius: '6',
  avatar_shadow: 'none',
  avatar_border_width: '0',
  avatar_border_color: '',
  avatar_filter: 'none',
  border_radius: '12',
  border_width: '0',
  border_color: '',
  ...props.settings,
}));

const rating = computed(() => parseInt(s.value.rating) || 0);

const wrapStyle = computed(() => {
  const bw = parseInt(s.value.border_width) || 0;
  const st = {
    background: resolveColor(s.value.bg_color, TOKENS.surfaceAlt),
    color: resolveColor(s.value.text_color, TOKENS.text),
    minHeight: '80px',
    borderRadius: (parseInt(s.value.border_radius) || 0) + 'px',
  };
  if (bw > 0) {
    st.border = `${bw}px solid ${resolveColor(s.value.border_color, TOKENS.border)}`;
  }
  return st;
});

const shadowMap = {
  none: 'none',
  sm: '0 1px 3px rgba(0,0,0,.3)',
  md: '0 4px 8px rgba(0,0,0,.3)',
  lg: '0 8px 20px rgba(0,0,0,.4)',
};
const filterMap = {
  none: 'none',
  grayscale: 'grayscale(100%)',
  sepia: 'sepia(80%)',
  brightness: 'brightness(1.15)',
  contrast: 'contrast(1.3)',
};

const avatarStyle = computed(() => {
  const size = (parseInt(s.value.avatar_size) || 48) + 'px';
  const isSquare = s.value.avatar_shape === 'square';
  const bw = parseInt(s.value.avatar_border_width) || 0;
  const st = {
    width: size,
    height: size,
    borderRadius: isSquare ? ((v => isNaN(v) ? 6 : v)(parseInt(s.value.avatar_radius))) + 'px' : '50%',
    objectFit: 'cover',
  };
  const shadow = shadowMap[s.value.avatar_shadow] || 'none';
  if (shadow !== 'none') st.boxShadow = shadow;
  if (bw > 0) st.border = `${bw}px solid ${resolveColor(s.value.avatar_border_color, TOKENS.onPrimary)}`;
  const flt = filterMap[s.value.avatar_filter] || 'none';
  if (flt !== 'none') st.filter = flt;
  return st;
});

const lineStyle = computed(() => {
  if (s.value.show_line === false || s.value.show_line === 'false' || s.value.show_line === '0' || s.value.show_line === '') {
    return { borderLeft: 'none', paddingLeft: '0' };
  }
  return {
    borderLeft: `3px solid ${resolveColor(s.value.line_color, TOKENS.primary)}`,
    paddingLeft: '16px',
  };
});

const bottomAlignStyle = computed(() => {
  const pos = s.value.author_position || 'bottom-left';
  const map = {
    'bottom-left': 'flex-start',
    'bottom-center': 'center',
    'bottom-right': 'flex-end',
  };
  return { display: 'flex', justifyContent: map[pos] || 'flex-start' };
});

/* ── Sub-components (functional) ── */

const starSvg = '<svg width="16" height="16" viewBox="0 0 20 20" fill="currentColor" style="vertical-align:-2px"><polygon points="10,1.5 12.5,7 18.5,7.6 14,11.5 15.3,17.5 10,14.5 4.7,17.5 6,11.5 1.5,7.6 7.5,7"/></svg>';

const QuoteBlock = {
  props: ['s', 'rating', 'lineStyle'],
  setup(p) {
    return () => h('div', [
      p.rating > 0
        ? h('div', {
            class: 'mb-mb-3 mb-flex mb-gap-0.5',
            style: 'color: #FBBF24',
            innerHTML: starSvg.repeat(p.rating),
          })
        : null,
      h('blockquote', {
        class: 'mb-text-lg mb-italic mb-leading-relaxed mb-opacity-95',
        style: {
          margin: 0,
          whiteSpace: 'pre-wrap',
          ...p.lineStyle,
        },
        'data-olo-editable': 'quote',
        'data-olo-multiline': '',
      }, p.s.quote || 'Aggiungi qui la tua citazione.'),
    ]);
  },
};

const AuthorBlock = {
  props: ['s', 'avatarStyle', 'horizontal'],
  setup(p) {
    return () => {
      const avatar = p.s.avatar
        ? h('img', { src: p.s.avatar, alt: '', style: p.avatarStyle })
        : null;
      const info = h('div', [
        h('div', { class: 'mb-font-semibold', 'data-olo-editable': 'author_name' }, p.s.author_name || 'Nome Autore'),
        p.s.author_role
          ? h('div', { class: 'mb-text-sm mb-opacity-70', 'data-olo-editable': 'author_role' }, p.s.author_role)
          : null,
      ]);

      if (p.horizontal) {
        return h('div', { class: 'mb-flex mb-items-center mb-gap-3' }, [avatar, info]);
      }
      return h('div', { class: 'mb-flex mb-flex-col mb-items-center mb-gap-2 mb-text-center' }, [avatar, info]);
    };
  },
};
</script>
