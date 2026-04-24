<template>
  <div class="olo-sp-preview">
    <!-- Hero area -->
    <div class="sp-hero" :style="heroStyle">
      <img v-if="s.hero_image" :src="s.hero_image" alt="" class="sp-hero__img" />
      <div v-else class="sp-hero__placeholder">{{ t('Immagine teaser') }}</div>
      <!-- Nav -->
      <div class="sp-nav" :class="navClass">
        <button
          v-for="(item, i) in items"
          :key="item.id || i"
          class="sp-nav__btn"
          :class="{ 'sp-nav__btn--active': activeIndex === i }"
          @click="activeIndex = i"
          :data-olo-editable="'items.' + i + '.nav_label'"
        >{{ item.nav_label || 'Tab ' + (i + 1) }}</button>
      </div>
    </div>

    <!-- Active panel content -->
    <div v-if="activeItem" class="sp-panel" :class="{ 'sp-panel--img-left': s.image_position === 'left' }" :style="{ padding: (parseInt(s.content_padding) || 12) + 'px' }">
      <div class="sp-panel__content">
        <component :is="s.title_tag || 'div'" class="sp-panel__title" :data-olo-editable="'items.' + activeIndex + '.title'">{{ activeItem.title || 'Titolo' }}</component>
        <div class="sp-panel__text" style="white-space:pre-wrap;" :data-olo-editable="'items.' + activeIndex + '.text'" data-olo-multiline>{{ activeItem.text || 'Testo del pannello...' }}</div>
        <div v-if="activeItem.button_text" class="sp-panel__btn" :class="'sp-btn--' + (s.button_style || 'default')" :data-olo-editable="'items.' + activeIndex + '.button_text'">
          {{ activeItem.button_text }}
        </div>
      </div>
      <div v-if="activeItem.image" class="sp-panel__media">
        <img :src="activeItem.image" alt="" class="sp-panel__img" />
      </div>
      <div v-else class="sp-panel__media sp-panel__media--empty">
        <span>{{ t('Immagine') }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { t } from '@/i18n';
import { ref, computed, watch } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const s = computed(() => props.settings);
const activeIndex = ref(0);

const items = computed(() => {
  const raw = s.value.items;
  return Array.isArray(raw) ? raw : [];
});

const activeItem = computed(() => items.value[activeIndex.value] || items.value[0] || null);

// Reset index when items change
watch(() => items.value.length, () => {
  if (activeIndex.value >= items.value.length) {
    activeIndex.value = 0;
  }
});

const heroStyle = computed(() => {
  const h = parseInt(s.value.hero_height) || 400;
  return { height: Math.min(h, 250) + 'px' };
});

const navClass = computed(() => {
  const style = s.value.nav_style || 'minimal';
  return 'sp-nav--' + style;
});
</script>

<style scoped>
.olo-sp-preview {
  min-height: 120px;
}

/* Hero */
.sp-hero {
  position: relative;
  overflow: hidden;
  background: #e5e7eb;
}
.sp-hero__img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}
.sp-hero__placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #94a3b8, #64748b);
  color: #fff;
  font-size: 10px;
  font-weight: 600;
}

/* Nav */
.sp-nav {
  position: absolute;
  bottom: 0;
  left: 0;
  display: flex;
  gap: 0;
  padding: 0 16px;
}
.sp-nav__btn {
  background: none;
  border: none;
  color: rgba(255,255,255,0.65);
  font-size: 8px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  padding: 6px 10px;
  cursor: pointer;
  transition: color 0.2s;
  position: relative;
}
.sp-nav__btn--active {
  color: #fff;
}

/* Minimal nav */
.sp-nav--minimal .sp-nav__btn {
  border-bottom: 2px solid transparent;
}
.sp-nav--minimal .sp-nav__btn--active {
  border-bottom-color: #fff;
}

/* Tab nav */
.sp-nav--tab .sp-nav__btn--active {
  background: rgba(255,255,255,0.15);
  border-radius: 3px 3px 0 0;
}

/* Pills nav */
.sp-nav--pills .sp-nav__btn--active {
  background: rgba(255,255,255,0.2);
  border-radius: 99px;
}

/* Panel */
.sp-panel {
  display: flex;
  gap: 12px;
  padding: 12px;
  align-items: flex-start;
}
.sp-panel--img-left {
  flex-direction: row-reverse;
}
.sp-panel__content {
  flex: 1;
  min-width: 0;
}
.sp-panel__title {
  font-size: 12px;
  font-weight: 700;
  color: var(--olo-color-text, #374151);
  margin-bottom: 4px;
}
.sp-panel__text {
  font-size: 9px;
  color: #6b7280;
  line-height: 1.4;
  margin-bottom: 8px;
  overflow: hidden;
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
}
.sp-panel__btn {
  display: inline-block;
  font-size: 8px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  padding: 4px 12px;
  border: 1px solid var(--olo-color-border, #E5E7EB);
  color: var(--olo-color-text, #374151);
  border-radius: 0;
}
.sp-btn--primary {
  background: var(--olo-color-primary, #6366F1);
  border-color: var(--olo-color-primary, #6366F1);
  color: #fff;
}
.sp-btn--secondary {
  background: var(--olo-color-text, #374151);
  border-color: var(--olo-color-text, #374151);
  color: #fff;
}
.sp-btn--text {
  border: none;
  padding: 0;
  color: var(--olo-color-primary, #6366F1);
  text-decoration: underline;
}

/* Panel media */
.sp-panel__media {
  width: 40%;
  flex-shrink: 0;
}
.sp-panel__img {
  width: 100%;
  height: auto;
  display: block;
  object-fit: cover;
}
.sp-panel__media--empty {
  height: 80px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f3f4f6;
  color: #9ca3af;
  font-size: 9px;
  border-radius: 3px;
}
</style>
