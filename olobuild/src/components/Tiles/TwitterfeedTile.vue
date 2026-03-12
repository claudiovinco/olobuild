<template>
  <div class="olo-twitterfeed" :style="wrapperStyle">
    <div
      class="mb-overflow-hidden"
      :style="cardStyle"
    >
      <!-- X/Twitter header -->
      <div
        class="mb-flex mb-items-center mb-gap-2 mb-px-4 mb-py-3"
        :style="headerStyle"
      >
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" :fill="s.theme === 'dark' ? '#fff' : '#0f1419'">
          <path d="M18.901 1.153h3.68l-8.04 9.19L24 22.846h-7.406l-5.8-7.584-6.638 7.584H.474l8.6-9.83L0 1.154h7.594l5.243 6.932ZM17.61 20.644h2.039L6.486 3.24H4.298Z"/>
        </svg>
        <span class="mb-text-sm mb-font-semibold" :style="{ color: s.theme === 'dark' ? '#fff' : '#0f1419' }">
          {{ s.embed_type === 'tweet' ? 'Post' : 'Timeline' }}
        </span>
      </div>

      <!-- Content area -->
      <div class="mb-px-4 mb-py-3" :style="{ background: s.theme === 'dark' ? '#15202b' : '#fff' }">
        <div v-if="s.url" class="mb-text-sm mb-truncate" :style="{ color: s.theme === 'dark' ? '#8899a6' : '#536471' }">{{ s.url }}</div>
        <div v-else class="mb-py-8 mb-text-center mb-text-sm" :style="{ color: s.theme === 'dark' ? '#8899a6' : '#536471' }">
          Inserisci URL profilo X/Twitter
        </div>
      </div>

      <!-- Fake timeline placeholder -->
      <div
        class="mb-w-full"
        :style="placeholderStyle"
      >
        <div class="mb-p-4" style="display:flex;flex-direction:column;gap:16px;">
          <div v-for="i in 3" :key="i" :style="{ display: 'flex', gap: '12px', alignItems: 'flex-start', paddingBottom: '16px', borderBottom: '1px solid ' + (s.theme === 'dark' ? '#38444d' : '#e1e8ed') }">
            <div :style="avatarStyle"></div>
            <div style="flex:1;display:flex;flex-direction:column;gap:4px;">
              <div style="display:flex;gap:6px;align-items:center;">
                <div :style="{ background: skeletonColor, borderRadius: '4px', height: '12px', width: '80px' }"></div>
                <div :style="{ background: skeletonColor, borderRadius: '4px', height: '10px', width: '60px', opacity: 0.5 }"></div>
              </div>
              <div :style="{ background: skeletonColor, borderRadius: '4px', height: '10px', width: '95%' }"></div>
              <div :style="{ background: skeletonColor, borderRadius: '4px', height: '10px', width: i === 2 ? '60%' : '80%' }"></div>
              <div style="display:flex;gap:24px;margin-top:4px;">
                <div v-for="j in 4" :key="j" :style="{ background: skeletonColor, borderRadius: '50%', width: '16px', height: '16px' }"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
  tileId: { type: String, default: '' },
});

const defaults = {
  url: '',
  embed_type: 'timeline',
  theme: 'light',
  width: '',
  height: '600',
  chrome: 'noheader,nofooter,noborders,noscrollbar',
  tweet_limit: '5',
  language: 'it',
  alignment: 'center',
  border_radius: '8',
};

const s = computed(() => ({ ...defaults, ...props.settings }));

const justifyMap = { left: 'flex-start', center: 'center', right: 'flex-end' };

const wrapperStyle = computed(() => ({
  display: 'flex',
  justifyContent: justifyMap[s.value.alignment] || 'center',
}));

const cardStyle = computed(() => ({
  width: s.value.width ? (parseInt(s.value.width) + 'px') : '100%',
  maxWidth: '550px',
  borderRadius: (parseInt(s.value.border_radius) || 0) + 'px',
  border: s.value.theme === 'dark' ? '1px solid #38444d' : '1px solid #e1e8ed',
  background: s.value.theme === 'dark' ? '#15202b' : '#fff',
  overflow: 'hidden',
}));

const headerStyle = computed(() => ({
  background: s.value.theme === 'dark' ? '#1c2938' : '#f7f9fa',
  borderBottom: s.value.theme === 'dark' ? '1px solid #38444d' : '1px solid #e1e8ed',
}));

const placeholderStyle = computed(() => ({
  height: Math.min(parseInt(s.value.height) || 600, 300) + 'px',
  background: s.value.theme === 'dark' ? '#15202b' : '#fff',
  borderTop: s.value.theme === 'dark' ? '1px solid #38444d' : '1px solid #e1e8ed',
  overflow: 'hidden',
}));

const skeletonColor = computed(() => s.value.theme === 'dark' ? '#253341' : '#eff3f4');

const avatarStyle = computed(() => ({
  width: '40px',
  height: '40px',
  borderRadius: '50%',
  background: skeletonColor.value,
  flexShrink: '0',
}));
</script>
