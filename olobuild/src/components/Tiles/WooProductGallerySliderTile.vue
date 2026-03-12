<template>
  <div style="padding:10px;background:#f9fafb;border-radius:8px;min-height:60px;">
    <!-- Immagine principale -->
    <div :style="mainImageStyle">
      <svg style="width:48px;height:48px;opacity:0.2;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
        <rect x="3" y="3" width="18" height="18" rx="2"/>
        <circle cx="8.5" cy="8.5" r="1.5"/>
        <path d="M21 15l-5-5L5 21"/>
      </svg>
      <!-- Frecce navigazione -->
      <div style="position:absolute;top:50%;left:8px;transform:translateY(-50%);width:28px;height:28px;background:rgba(255,255,255,0.85);border-radius:50%;display:flex;align-items:center;justify-content:center;box-shadow:0 1px 3px rgba(0,0,0,0.15);">
        <svg style="width:14px;height:14px;color:#374151;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <path d="M15 18l-6-6 6-6"/>
        </svg>
      </div>
      <div style="position:absolute;top:50%;right:8px;transform:translateY(-50%);width:28px;height:28px;background:rgba(255,255,255,0.85);border-radius:50%;display:flex;align-items:center;justify-content:center;box-shadow:0 1px 3px rgba(0,0,0,0.15);">
        <svg style="width:14px;height:14px;color:#374151;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <path d="M9 18l6-6-6-6"/>
        </svg>
      </div>
    </div>

    <!-- Thumbnails strip -->
    <div :style="thumbStripStyle">
      <div
        v-for="i in thumbCount"
        :key="i"
        :style="[thumbStyle, i === 1 ? activeThumbStyle : {}]"
      >
        <svg style="width:14px;height:14px;opacity:0.25;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
          <rect x="3" y="3" width="18" height="18" rx="2"/>
          <circle cx="8.5" cy="8.5" r="1.5"/>
          <path d="M21 15l-5-5L5 21"/>
        </svg>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  thumbs_count: '5',
  thumb_gap: '8',
  image_ratio: '4-3',
};
const s = computed(() => ({ ...defaults, ...props.settings }));

const ratioMap = {
  '1-1': '100%',
  '4-3': '75%',
  '3-4': '133%',
  '16-9': '56.25%',
  'auto': '75%',
};

const thumbCount = computed(() => Math.max(3, Math.min(8, parseInt(s.value.thumbs_count) || 5)));

const mainImageStyle = computed(() => ({
  width: '100%',
  paddingTop: ratioMap[s.value.image_ratio] || '75%',
  background: '#E5E7EB',
  borderRadius: '6px',
  position: 'relative',
  display: 'flex',
  alignItems: 'center',
  justifyContent: 'center',
  marginBottom: (parseInt(s.value.thumb_gap) || 8) + 'px',
}));

const thumbStripStyle = computed(() => ({
  display: 'grid',
  gridTemplateColumns: `repeat(${thumbCount.value}, 1fr)`,
  gap: (parseInt(s.value.thumb_gap) || 8) + 'px',
}));

const thumbStyle = {
  width: '100%',
  paddingTop: '100%',
  background: '#E5E7EB',
  borderRadius: '4px',
  position: 'relative',
  display: 'flex',
  alignItems: 'center',
  justifyContent: 'center',
  border: '2px solid transparent',
};

const activeThumbStyle = {
  border: '2px solid #6366F1',
};
</script>
