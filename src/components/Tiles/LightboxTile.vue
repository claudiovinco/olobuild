<template>
  <div class="olo-lightbox-tile">
    <div
      class="mb-grid"
      :style="{ gridTemplateColumns: `repeat(${settings.columns || 3}, 1fr)`, gap: (settings.gap || 15) + 'px' }"
    >
      <div
        v-for="(item, idx) in (settings.items || [])"
        :key="item.id || idx"
        class="olo-lb-thumb"
        :style="{ borderRadius: (settings.thumb_radius || 0) + 'px', overflow: 'hidden', position: 'relative', cursor: 'pointer' }"
      >
        <div
          v-if="ratioPercent !== '0'"
          :style="{ paddingBottom: ratioPercent, position: 'relative' }"
        >
          <img
            v-if="item.thumb || item.url"
            :src="item.thumb || item.url"
            :alt="item.title || ''"
            style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover"
          />
          <div v-else style="position:absolute;inset:0;background:#334155;display:flex;align-items:center;justify-content:center">
            <span style="font-size:24px;opacity:0.4">{{ t('&#x1F5BC;') }}</span>
          </div>
        </div>
        <img
          v-else-if="item.thumb || item.url"
          :src="item.thumb || item.url"
          :alt="item.title || ''"
          style="width:100%;display:block"
        />
        <!-- Overlay icon -->
        <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,0.3);opacity:0;transition:opacity 0.2s" class="olo-lb-overlay">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
            <line x1="11" y1="8" x2="11" y2="14"/><line x1="8" y1="11" x2="14" y2="11"/>
          </svg>
        </div>
        <div v-if="settings.show_caption && item.caption" style="position:absolute;bottom:0;left:0;right:0;padding:6px 10px;background:rgba(0,0,0,0.6);color:#fff;font-size:11px">
          {{ item.caption }}
        </div>
      </div>
    </div>
    <p v-if="!settings.items || settings.items.length === 0" class="mb-text-xs mb-text-gray-500 mb-text-center mb-py-6">
      {{ t('Aggiungi elementi alla lightbox') }}
    </p>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';
const props = defineProps({ settings: { type: Object, default: () => ({}) } });

const ratioMap = { '1:1': '100%', '4:3': '75%', '16:9': '56.25%', 'auto': '0' };
const ratioPercent = computed(() => ratioMap[props.settings.thumb_ratio] || '100%');
</script>

<style scoped>
.olo-lb-thumb:hover .olo-lb-overlay { opacity: 1 !important; }
</style>
