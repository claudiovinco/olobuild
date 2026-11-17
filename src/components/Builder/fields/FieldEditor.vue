<template>
  <RichTextEditor
    :modelValue="modelValue"
    :mode="mode"
    :strumenti="strumenti"
    :a-capo="aCapo"
    @update:modelValue="$emit('update:modelValue', $event)"
  />
</template>

<script setup>
import RichTextEditor from '../RichTextEditor.vue';

defineProps({
  modelValue: { type: String, default: '' },
  mode: { type: String, default: 'inline' },
  strumenti: { type: Array, default: null },
  aCapo: { type: Boolean, default: false },
});
defineEmits(['update:modelValue']);
</script>
