<template>
  <RichTextEditor
    :modelValue="modelValue"
    :mode="mode"
    @update:modelValue="$emit('update:modelValue', $event)"
  />
</template>

<script setup>
import RichTextEditor from '../RichTextEditor.vue';

defineProps({
  modelValue: { type: String, default: '' },
  mode: { type: String, default: 'inline' },
});
defineEmits(['update:modelValue']);
</script>
