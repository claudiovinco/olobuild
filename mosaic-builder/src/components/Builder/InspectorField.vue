<template>
  <div>
    <!-- Dynamic-capable field -->
    <DynamicFieldToggle
      v-if="allowDynamic && tileId"
      :field="field"
      :tileId="tileId"
      :dynamic="dynamic"
      @update:dynamic="onDynamicUpdate"
    >
      <component :is="fieldComponent" v-bind="fieldProps" @update:modelValue="$emit('update:modelValue', $event)" />
    </DynamicFieldToggle>

    <!-- Non-dynamic field (original behavior) -->
    <template v-else>
      <!-- Separator (visual divider) -->
      <div v-if="field.type === 'separator'" class="mb-border-t mb-border-gray-700 mb-pt-2 mb-mt-1">
        <span class="mb-text-[10px] mb-font-semibold mb-uppercase mb-tracking-wider mb-text-gray-500">{{ field.label }}</span>
      </div>

      <template v-else>
      <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">
        {{ field.label }}
      </label>

      <FieldToggle
        v-if="field.type === 'toggle'"
        :modelValue="modelValue"
        @update:modelValue="$emit('update:modelValue', $event)"
      />

      <FieldColor
        v-else-if="field.type === 'color'"
        :modelValue="modelValue"
        @update:modelValue="$emit('update:modelValue', $event)"
      />

      <FieldSelect
        v-else-if="field.type === 'select'"
        :modelValue="modelValue"
        :options="resolvedOptions"
        @update:modelValue="$emit('update:modelValue', $event)"
      />

      <FieldRange
        v-else-if="field.type === 'range'"
        :modelValue="modelValue"
        :min="field.min || 0"
        :max="field.max || 100"
        :step="field.step || 1"
        @update:modelValue="$emit('update:modelValue', $event)"
      />

      <FieldBorderRadius
        v-else-if="field.type === 'border-radius'"
        :modelValue="modelValue"
        @update:modelValue="$emit('update:modelValue', $event)"
      />

      <FieldEditor
        v-else-if="field.type === 'editor'"
        :modelValue="modelValue"
        :mode="field.mode || 'inline'"
        @update:modelValue="$emit('update:modelValue', $event)"
      />

      <FieldImage
        v-else-if="field.type === 'image'"
        :modelValue="modelValue"
        @update:modelValue="$emit('update:modelValue', $event)"
      />

      <FieldMedia
        v-else-if="field.type === 'media'"
        :modelValue="modelValue"
        @update:modelValue="$emit('update:modelValue', $event)"
      />

      <FieldGallery
        v-else-if="field.type === 'gallery'"
        :modelValue="modelValue"
        @update:modelValue="$emit('update:modelValue', $event)"
      />

      <FieldIcon
        v-else-if="field.type === 'icon'"
        :modelValue="modelValue"
        @update:modelValue="$emit('update:modelValue', $event)"
      />

      <FieldTextarea
        v-else-if="field.type === 'textarea'"
        :modelValue="modelValue"
        @update:modelValue="$emit('update:modelValue', $event)"
      />

      <FieldMegaPanelMap
        v-else-if="field.type === 'megapanel-map'"
        :modelValue="modelValue"
        @update:modelValue="$emit('update:modelValue', $event)"
      />

      <!-- Custom field types rendered by parent (content-items) -->
      <slot v-else-if="field.type === 'content-items'" name="content-items" />

      <FieldText
        v-else
        :modelValue="modelValue"
        @update:modelValue="$emit('update:modelValue', $event)"
        @confirm="$emit('confirm', $event)"
      />
      </template>
    </template>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import FieldText from './fields/FieldText.vue';
import FieldTextarea from './fields/FieldTextarea.vue';
import FieldSelect from './fields/FieldSelect.vue';
import FieldToggle from './fields/FieldToggle.vue';
import FieldColor from './fields/FieldColor.vue';
import FieldRange from './fields/FieldRange.vue';
import FieldBorderRadius from './fields/FieldBorderRadius.vue';
import FieldEditor from './fields/FieldEditor.vue';
import FieldImage from './fields/FieldImage.vue';
import FieldMedia from './fields/FieldMedia.vue';
import FieldGallery from './fields/FieldGallery.vue';
import FieldIcon from './fields/FieldIcon.vue';
import FieldMegaPanelMap from './fields/FieldMegaPanelMap.vue';
import DynamicFieldToggle from './DynamicFieldToggle.vue';

const DYNAMIC_TYPES = ['text', 'textarea', 'editor', 'image', 'media'];

const props = defineProps({
  field: { type: Object, required: true },
  modelValue: { default: '' },
  tileId: { type: String, default: '' },
  dynamic: { type: Object, default: () => ({}) },
});

const emit = defineEmits(['update:modelValue', 'update:dynamic', 'confirm']);

const resolvedOptions = computed(() => {
  if (props.field.optionsSource) {
    const md = window.oloData || {};
    if (props.field.optionsSource === 'wpMenus') {
      return (md.wpMenus || []).map(m => ({ value: m.id, label: m.name }));
    }
    if (props.field.optionsSource === 'postTypes') {
      return (md.postTypes || []);
    }
    if (props.field.optionsSource === 'taxonomies') {
      return (md.taxonomies || []);
    }
    if (props.field.optionsSource === 'templates') {
      return (md.templateList || []);
    }
  }
  return props.field.options || [];
});

const allowDynamic = computed(() => {
  if (props.field.allowDynamic === false) return false;
  if (props.field.allowDynamic === true) return true;
  return DYNAMIC_TYPES.includes(props.field.type);
});

// Determine component and props for the slot content
const fieldComponent = computed(() => {
  switch (props.field.type) {
    case 'toggle': return FieldToggle;
    case 'color': return FieldColor;
    case 'select': return FieldSelect;
    case 'range': return FieldRange;
    case 'border-radius': return FieldBorderRadius;
    case 'editor': return FieldEditor;
    case 'image': return FieldImage;
    case 'media': return FieldMedia;
    case 'gallery': return FieldGallery;
    case 'icon': return FieldIcon;
    case 'textarea': return FieldTextarea;
    case 'megapanel-map': return FieldMegaPanelMap;
    default: return FieldText;
  }
});

const fieldProps = computed(() => {
  const base = { modelValue: props.modelValue };
  switch (props.field.type) {
    case 'select': return { ...base, options: resolvedOptions.value };
    case 'range': return { ...base, min: props.field.min || 0, max: props.field.max || 100, step: props.field.step || 1 };
    case 'editor': return { ...base, mode: props.field.mode || 'inline' };
    default: return base;
  }
});

function onDynamicUpdate(dynamicUpdate, isRemove) {
  if (isRemove) {
    emit('update:dynamic', dynamicUpdate, true);
  } else {
    emit('update:dynamic', dynamicUpdate);
  }
}
</script>
