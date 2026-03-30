<template>
  <div class="olo-facebookpage" :style="wrapperStyle">
    <div
      class="mb-rounded-lg mb-overflow-hidden"
      :style="cardStyle"
    >
      <!-- Facebook header -->
      <div
        class="mb-flex mb-items-center mb-gap-2 mb-px-4 mb-py-3"
        style="background:#1877F2;"
      >
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="#fff">
          <path d="M9.101 23.691v-7.98H6.627v-3.667h2.474v-1.58c0-4.085 1.848-5.978 5.858-5.978.401 0 .955.042 1.468.103a8.68 8.68 0 0 1 1.141.195v3.325a8.623 8.623 0 0 0-.653-.036 26.805 26.805 0 0 0-.733-.009c-.707 0-1.259.096-1.675.309a1.686 1.686 0 0 0-.679.622c-.258.42-.374.995-.374 1.752v1.297h3.919l-.386 2.103-.287 1.564h-3.246v8.245C19.396 23.238 24 18.179 24 12.044c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.628 3.874 10.35 9.101 11.647Z"/>
        </svg>
        <span class="mb-text-sm mb-font-semibold" style="color:#fff;">Facebook Page</span>
      </div>

      <!-- Content area -->
      <div class="mb-px-4 mb-py-3" style="background:#f0f2f5;">
        <div v-if="s.page_url" class="mb-text-sm mb-text-gray-700 mb-truncate">{{ s.page_url }}</div>
        <div v-else class="mb-py-8 mb-text-center mb-text-sm mb-text-gray-400">
          Inserisci URL pagina Facebook
        </div>

        <div v-if="s.page_url" class="mb-mt-2 mb-text-xs mb-text-gray-400">
          Tab: {{ s.tabs || 'timeline' }} | {{ s.width }}x{{ s.height }}px
        </div>
      </div>

      <!-- Fake timeline placeholder -->
      <div
        class="mb-w-full"
        :style="{
          height: Math.min(parseInt(s.height) || 500, 300) + 'px',
          background: '#f0f2f5',
          borderTop: '1px solid #ddd',
        }"
      >
        <div class="mb-p-4" style="display:flex;flex-direction:column;gap:12px;">
          <div v-for="i in 2" :key="i" style="background:#fff;border-radius:8px;padding:12px;border:1px solid #e4e6eb;">
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">
              <div style="width:32px;height:32px;border-radius:50%;background:#e4e6eb;"></div>
              <div><div style="background:#e4e6eb;border-radius:4px;height:10px;width:100px;margin-bottom:4px;"></div><div style="background:#e4e6eb;border-radius:4px;height:8px;width:60px;"></div></div>
            </div>
            <div style="background:#e4e6eb;border-radius:4px;height:10px;width:90%;margin-bottom:4px;"></div>
            <div style="background:#e4e6eb;border-radius:4px;height:10px;width:70%;margin-bottom:8px;"></div>
            <div v-if="i === 1" style="background:#e4e6eb;border-radius:6px;height:80px;width:100%;"></div>
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
  page_url: '',
  width: '340',
  height: '500',
  tabs: 'timeline',
  show_cover: true,
  show_facepile: true,
  small_header: false,
  adapt_container: true,
  language: 'it_IT',
  alignment: 'center',
};

const s = computed(() => ({ ...defaults, ...props.settings }));

const justifyMap = { left: 'flex-start', center: 'center', right: 'flex-end' };

const wrapperStyle = computed(() => ({
  display: 'flex',
  justifyContent: justifyMap[s.value.alignment] || 'center',
}));

const cardStyle = computed(() => ({
  width: (parseInt(s.value.width) || 340) + 'px',
  maxWidth: '100%',
  borderRadius: '8px',
  overflow: 'hidden',
  border: '1px solid #ddd',
  background: '#fff',
}));
</script>
