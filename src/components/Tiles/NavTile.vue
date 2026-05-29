<template>
  <div class="mb-p-3" style="min-height: 60px;">
    <component :is="isHorizontal ? 'div' : 'ul'" :class="containerClasses" :style="containerStyle">
      <template v-for="(item, i) in items" :key="item.id || i">
        <!-- Separator -->
        <span
          v-if="i > 0 && s.separator && isHorizontal"
          class="mb-self-stretch"
          :style="{ width: '1px', background: s.separator_color || 'var(--olo-color-border, #374151)' }"
        ></span>
        <hr
          v-if="i > 0 && s.separator && !isHorizontal"
          class="mb-border-0 mb-my-0"
          :style="{ height: '1px', background: s.separator_color || 'var(--olo-color-border, #374151)' }"
        />

        <!-- Nav item -->
        <component
          :is="isHorizontal ? 'span' : 'li'"
          :class="itemClasses(i)"
          :style="itemStyle(i)"
          @mouseenter="hoverIndex = i"
          @mouseleave="hoverIndex = -1"
        >
          <!-- Dot indicator -->
          <span
            v-if="s.active_style === 'dot' && i === 0"
            :style="{ width: '6px', height: '6px', borderRadius: '50%', background: activeColorVal, flexShrink: 0 }"
          ></span>

          <!-- Icon left -->
          <span
            v-if="s.show_icons && item.tag && s.icon_position !== 'right'"
            class="mb-flex mb-items-center mb-flex-shrink-0"
            :style="iconStyle(i)"
            v-html="getIconSvg(item.tag)"
          ></span>

          <!-- Label -->
          <span :style="labelStyle(i)" :data-olo-editable="'items.' + i + '.title'">{{ item.title }}</span>

          <!-- Icon right -->
          <span
            v-if="s.show_icons && item.tag && s.icon_position === 'right'"
            class="mb-flex mb-items-center mb-flex-shrink-0"
            :style="iconStyle(i)"
            v-html="getIconSvg(item.tag)"
          ></span>
        </component>
      </template>
    </component>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue';
import iconsSvg from '../ProSlider/uikitIconsSvg.js';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  style: 'default',
  direction: 'vertical',
  alignment: 'left',
  show_icons: true,
  icon_position: 'left',
  icon_size: '16',
  font_size: '14',
  font_weight: '400',
  text_transform: 'none',
  letter_spacing: '0',
  link_color: '',
  link_hover_color: '',
  active_color: '',
  icon_color: '',
  separator: false,
  separator_color: '',
  gap: '4',
  padding_x: '12',
  padding_y: '8',
  border_radius: '6',
  active_style: 'left-border',
  active_bg: '',
  hover_bg: '',
  hover_effect: 'none',
};
const s = computed(() => ({ ...defaults, ...props.settings }));
const hoverIndex = ref(-1);

const isHorizontal = computed(() => s.value.direction === 'horizontal');
const iconSizePx = computed(() => parseInt(s.value.icon_size) || 16);

// TOKEN-FIRST: neutri nudi → token testo; voce attiva = primario brand (era #e1474f indaco)
const linkColorVal = computed(() => s.value.link_color || 'var(--olo-color-text-soft, #6b7280)');
const hoverColorVal = computed(() => s.value.link_hover_color || 'var(--olo-color-text, #374151)');
const activeColorVal = computed(() => s.value.active_color || 'var(--olo-color-primary, #e1474f)');
// Tinta soft dal colore attivo (compatibile con i token CSS var: niente più concat hex-alpha)
const activeTint = (pct) => `color-mix(in srgb, ${activeColorVal.value} ${pct}%, transparent)`;
const hoverBgVal = computed(() => s.value.hover_bg || '');
const activeBgVal = computed(() => s.value.active_bg || '');

function getIconSvg(name) {
  const raw = iconsSvg[name];
  if (!raw) return '';
  const size = iconSizePx.value;
  return raw.replace(/width="[^"]*"/, `width="${size}"`).replace(/height="[^"]*"/, `height="${size}"`);
}

const containerClasses = computed(() => {
  const cls = ['mb-flex', 'mb-list-none', 'mb-p-0', 'mb-m-0'];
  if (isHorizontal.value) {
    cls.push('mb-flex-row', 'mb-flex-wrap', 'mb-items-center');
  } else {
    cls.push('mb-flex-col');
  }
  return cls;
});

const containerStyle = computed(() => {
  const st = { gap: `${parseInt(s.value.gap) || 4}px` };
  const align = s.value.alignment;
  if (isHorizontal.value) {
    st.justifyContent = align === 'center' ? 'center' : align === 'right' ? 'flex-end' : align === 'stretch' ? 'stretch' : 'flex-start';
  } else {
    st.alignItems = align === 'center' ? 'center' : align === 'right' ? 'flex-end' : align === 'stretch' ? 'stretch' : 'flex-start';
  }
  return st;
});

function itemClasses() {
  return ['mb-flex', 'mb-items-center', 'mb-gap-2', 'mb-cursor-pointer', 'mb-transition-all'];
}

function itemStyle(i) {
  const isActive = i === 0;
  const isHover = hoverIndex.value === i;
  const px = parseInt(s.value.padding_x) || 12;
  const py = parseInt(s.value.padding_y) || 8;
  const radius = (v => isNaN(v) ? 6 : v)(parseInt(s.value.border_radius));
  const style_type = s.value.style;
  const active_style = s.value.active_style;

  const st = {
    padding: `${py}px ${px}px`,
    borderRadius: `${radius}px`,
    fontSize: `${parseInt(s.value.font_size) || 14}px`,
    fontWeight: s.value.font_weight || '400',
    textTransform: s.value.text_transform || 'none',
    letterSpacing: `${parseFloat(s.value.letter_spacing) || 0}px`,
    color: isActive ? activeColorVal.value : isHover ? hoverColorVal.value : linkColorVal.value,
    transition: 'all 0.2s ease',
    position: 'relative',
    textDecoration: 'none',
    borderLeft: 'none',
    borderBottom: 'none',
    background: 'transparent',
    listStyle: 'none',
  };

  if (s.value.alignment === 'stretch' && !isHorizontal.value) {
    st.width = '100%';
  }

  // Active styles
  if (isActive) {
    if (active_style === 'left-border') {
      st.borderLeft = `3px solid ${activeColorVal.value}`;
    } else if (active_style === 'bottom-border') {
      st.borderBottom = `2px solid ${activeColorVal.value}`;
    } else if (active_style === 'background') {
      st.background = activeBgVal.value || activeTint(8);
    } else if (active_style === 'bold') {
      st.fontWeight = '700';
    }
  }

  // Hover
  if (isHover && !isActive) {
    if (hoverBgVal.value) {
      st.background = hoverBgVal.value;
    } else if (s.value.hover_effect === 'slide-bg') {
      st.background = 'rgba(255,255,255,0.06)';
    }
    if (s.value.hover_effect === 'lift') {
      st.transform = 'translateY(-1px)';
    }
    if (s.value.hover_effect === 'underline') {
      st.textDecoration = 'underline';
      st.textUnderlineOffset = '4px';
    }
  }

  // Style presets
  if (style_type === 'pill') {
    st.borderRadius = '999px';
    if (isActive) st.background = activeBgVal.value || activeTint(12);
  } else if (style_type === 'boxed') {
    st.border = `1px solid ${isActive ? activeColorVal.value : 'rgba(255,255,255,0.1)'}`;
    if (isActive) st.background = activeBgVal.value || activeTint(6);
  } else if (style_type === 'underline') {
    st.borderRadius = '0';
    st.borderBottom = isActive ? `2px solid ${activeColorVal.value}` : '2px solid transparent';
  } else if (style_type === 'minimal') {
    st.padding = `${py}px ${Math.max(px - 4, 4)}px`;
  }

  return st;
}

function iconStyle(i) {
  const isActive = i === 0;
  return {
    color: s.value.icon_color || (isActive ? activeColorVal.value : linkColorVal.value),
  };
}

function labelStyle() {
  return { flex: s.value.alignment === 'stretch' ? '1' : 'none' };
}

const items = computed(() => {
  const raw = s.value.items;
  if (Array.isArray(raw) && raw.length) return raw;
  return [
    { title: 'Home', content: '#', tag: 'home' },
    { title: 'Chi siamo', content: '#', tag: 'users' },
    { title: 'Contatti', content: '#', tag: 'mail' },
  ];
});
</script>

<style scoped>
:deep(svg) {
  fill: currentColor;
}
/* a11y tastiera: anello di focus visibile sulle voci di menu */
.mb-cursor-pointer:focus-visible {
  outline: none;
  box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent);
}
</style>
