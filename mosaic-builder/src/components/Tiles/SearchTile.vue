<template>
  <div class="olo-search">
    <div class="olo-search-form" :class="formClasses">
      <!-- Search icon -->
      <span v-if="settings.show_icon !== false" class="olo-search-icon">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="11" cy="11" r="8"></circle>
          <path d="m21 21-4.35-4.35"></path>
        </svg>
      </span>

      <!-- Input -->
      <input
        type="text"
        class="olo-search-input"
        :class="inputClasses"
        :placeholder="settings.placeholder || 'Cerca...'"
        disabled
      />
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const formClasses = computed(() => {
  const cls = [];
  const style = props.settings.style || 'default';
  cls.push('olo-search--' + style);
  return cls;
});

const inputClasses = computed(() => {
  const cls = [];
  if (props.settings.size === 'large') {
    cls.push('olo-search-input--large');
  }
  return cls;
});
</script>

<style scoped>
.olo-search {
  min-height: 40px;
}

.olo-search-form {
  display: flex;
  align-items: center;
  gap: 8px;
  background: #1f2937;
  border: 1px solid #374151;
  border-radius: 6px;
  padding: 0 12px;
}

.olo-search--navbar {
  background: transparent;
  border-color: transparent;
}

.olo-search--large .olo-search-input {
  font-size: 16px;
  padding: 14px 0;
}

.olo-search-icon {
  color: #6b7280;
  display: flex;
  align-items: center;
  flex-shrink: 0;
}

.olo-search-input {
  background: transparent;
  border: none;
  outline: none;
  color: #d1d5db;
  font-size: 14px;
  padding: 10px 0;
  width: 100%;
}

.olo-search-input--large {
  font-size: 16px;
  padding: 14px 0;
}

.olo-search-input::placeholder {
  color: #6b7280;
}
</style>
