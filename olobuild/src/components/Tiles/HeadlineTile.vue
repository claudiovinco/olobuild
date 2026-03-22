<template>
  <div :style="{ textAlign: effectiveAlignment }">
    <!-- Line decoration -->
    <div v-if="s.decoration === 'line'" class="mb-flex mb-items-center mb-gap-4 mb-mb-1" :style="{ justifyContent: alignJustify }">
      <span v-if="effectiveAlignment !== 'left'" class="mb-flex-1" style="height:1px;" :style="{ background: decoColor }"></span>
      <component :is="s.tag" v-if="isMultiline" class="mb-m-0 mb-leading-tight" :style="headingStyle" data-olo-editable="heading" v-html="headingText"></component>
      <component :is="s.tag" v-else class="mb-m-0 mb-leading-tight mb-whitespace-nowrap" :style="headingStyle" data-olo-editable="heading">{{ headingText }}</component>
      <span v-if="effectiveAlignment !== 'right'" class="mb-flex-1" style="height:1px;" :style="{ background: decoColor }"></span>
    </div>

    <!-- Divider decoration -->
    <template v-else-if="s.decoration === 'divider'">
      <component :is="s.tag" v-if="isMultiline" class="mb-m-0 mb-leading-tight mb-pb-3 mb-mb-1" style="border-bottom:1px solid;" :style="{ ...headingStyle, borderColor: decoColor }" data-olo-editable="heading" v-html="headingText"></component>
      <component :is="s.tag" v-else class="mb-m-0 mb-leading-tight mb-pb-3 mb-mb-1" style="border-bottom:1px solid;" :style="{ ...headingStyle, borderColor: decoColor }" data-olo-editable="heading">{{ headingText }}</component>
    </template>

    <!-- Other decorations (dot, star, none) -->
    <template v-else>
      <div v-if="s.decoration === 'dot'" class="mb-mb-3" :style="{ display: 'flex', justifyContent: alignJustify, gap: (s.decoration_spacing || 6) + 'px' }">
        <span v-for="n in decoCount" :key="n" class="mb-inline-block mb-rounded-full" style="width:10px;height:10px;" :style="{ background: decoColor }"></span>
      </div>
      <div v-if="s.decoration === 'star'" class="mb-mb-2" :style="{ display: 'flex', justifyContent: alignJustify, gap: (s.decoration_spacing || 6) + 'px', fontSize: '1.5em', color: decoColor }">
        <span v-for="n in decoCount" :key="n">&#x2605;</span>
      </div>
      <component :is="s.tag" v-if="isMultiline" class="mb-m-0 mb-leading-tight" :style="headingStyle" data-olo-editable="heading" v-html="headingText"></component>
      <component :is="s.tag" v-else class="mb-m-0 mb-leading-tight" :style="headingStyle" data-olo-editable="heading">{{ headingText }}</component>
    </template>

    <!-- Subtitle -->
    <p v-if="s.subtitle" class="mb-text-base mb-leading-relaxed" :style="subtitleStyle" data-olo-editable="subtitle">{{ s.subtitle }}</p>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useBuilderStore } from '@/stores/builder';
import { rv } from '@/composables/useResponsiveValue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const builderStore = useBuilderStore();

const defaults = {
  heading: 'Titolo sezione',
  subtitle: '',
  tag: 'h2',
  alignment: 'center',
  heading_size: 'lg',
  heading_color: '',
  heading_italic: false,
  heading_uppercase: false,
  decoration: 'line',
  decoration_color: '',
  decoration_count: 3,
  decoration_spacing: 6,
  subtitle_color: '',
  text_stroke: '0',
  text_stroke_color: '',
  text_shadow: '',
  gradient_text: false,
  gradient_from: '',
  gradient_to: '',
  gradient_angle: '90',
  blend_mode: 'normal',
};
const s = computed(() => ({ ...defaults, ...props.settings }));

const sizeMap = { sm: '1.25em', md: '1.75em', lg: '2.25em', xl: '3em' };

// Strip any legacy HTML tags from heading, support multiline via \n or <br>
const headingText = computed(() => {
  let text = (s.value.heading || '');
  // Convert <br> to \n before stripping other tags
  text = text.replace(/<br\s*\/?>/gi, '\n');
  text = text.replace(/<[^>]*>/g, '').trim();
  return text.includes('\n') ? text.replace(/\n/g, '<br>') : text;
});
const isMultiline = computed(() => {
  const text = (s.value.heading || '');
  return text.includes('\n') || /<br\s*\/?>/i.test(text);
});

// Responsive alignment
const effectiveAlignment = computed(() => rv(props.settings, 'alignment', s.value.alignment, builderStore.viewMode));

// Decoration color with fallback
const decoColor = computed(() => s.value.decoration_color || 'var(--olo-color-primary, #6366F1)');
const decoCount = computed(() => Math.max(1, Math.min(9, parseInt(s.value.decoration_count) || 3)));

const headingStyle = computed(() => {
  const mode = builderStore.viewMode;
  const headingSize = rv(props.settings, 'heading_size', s.value.heading_size, mode);
  const st = {
    fontSize: sizeMap[headingSize] || '2.25em',
    fontWeight: 'bold',
  };

  // Italic
  if (s.value.heading_italic) {
    st.fontStyle = 'italic';
  }

  // Uppercase
  if (s.value.heading_uppercase) {
    st.textTransform = 'uppercase';
    st.letterSpacing = '0.05em';
  }

  // Gradient wins over heading_color
  if (s.value.gradient_text) {
    const angle = parseInt(s.value.gradient_angle) || 90;
    const from = s.value.gradient_from || 'var(--olo-color-primary, #6366F1)';
    const to = s.value.gradient_to || '#EC4899';
    st.background = `linear-gradient(${angle}deg, ${from}, ${to})`;
    st.WebkitBackgroundClip = 'text';
    st.WebkitTextFillColor = 'transparent';
    st.backgroundClip = 'text';
  } else if (s.value.heading_color) {
    st.color = s.value.heading_color;
  }

  // Text stroke
  const stroke = parseInt(s.value.text_stroke) || 0;
  if (stroke > 0) {
    st.WebkitTextStroke = stroke + 'px ' + (s.value.text_stroke_color || '#000');
  }

  // Text shadow
  if (s.value.text_shadow) {
    if (s.value.text_shadow === 'custom') {
      const th = parseInt(s.value.text_shadow_h, 10) || 0;
      const tv = parseInt(s.value.text_shadow_v, 10) || 0;
      const tb = parseInt(s.value.text_shadow_blur, 10) || 0;
      const tc = s.value.text_shadow_color || 'rgba(0,0,0,0.3)';
      st.textShadow = `${th}px ${tv}px ${tb}px ${tc}`;
    } else {
      st.textShadow = s.value.text_shadow;
    }
  }

  // Blend mode
  if (s.value.blend_mode && s.value.blend_mode !== 'normal') {
    st.mixBlendMode = s.value.blend_mode;
  }

  return st;
});

const subtitleStyle = computed(() => {
  const st = { margin: '12px 0 0' };
  if (s.value.subtitle_color) {
    st.color = s.value.subtitle_color;
  }
  return st;
});

const alignJustify = computed(() => {
  const m = { left: 'flex-start', center: 'center', right: 'flex-end' };
  return m[effectiveAlignment.value] || 'center';
});
</script>
