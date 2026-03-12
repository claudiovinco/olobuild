<template>
  <div :style="wrapperStyle">
    <component :is="s.tag" class="olo-bt-text" :style="textStyle" data-olo-editable="text">{{ s.text || 'BLEND' }}</component>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  text: 'BLEND',
  tag: 'div',
  font_size: '120',
  font_weight: '900',
  font_family: '',
  text_transform: 'uppercase',
  letter_spacing: '5',
  line_height: '1',
  text_align: 'center',
  text_color: '#ffffff',
  blend_mode: 'difference',
  padding_top: '40',
  padding_bottom: '40',
  padding_left: '20',
  padding_right: '20',
};

const s = computed(() => ({ ...defaults, ...props.settings }));

const wrapperStyle = computed(() => ({
  mixBlendMode: s.value.blend_mode || 'difference',
  padding: `${parseInt(s.value.padding_top)||0}px ${parseInt(s.value.padding_right)||0}px ${parseInt(s.value.padding_bottom)||0}px ${parseInt(s.value.padding_left)||0}px`,
}));

const textStyle = computed(() => ({
  fontSize: `${Math.min(parseInt(s.value.font_size) || 120, 200)}px`,
  fontWeight: s.value.font_weight || '900',
  fontFamily: s.value.font_family || 'inherit',
  textTransform: s.value.text_transform || 'uppercase',
  letterSpacing: `${parseInt(s.value.letter_spacing) || 0}px`,
  lineHeight: s.value.line_height || '1',
  textAlign: s.value.text_align || 'center',
  color: s.value.text_color || '#ffffff',
  margin: '0',
}));
</script>
