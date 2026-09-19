<template>
  <div class="olo-lightbox-tile">
    <div :style="gridStyle">
      <div
        v-for="(item, idx) in items"
        :key="item.id || idx"
        class="olo-lb-thumb"
        :style="thumbStyle"
      >
        <div
          v-if="ratioPercent !== '0'"
          :style="{ paddingBottom: ratioPercent, position: 'relative' }"
        >
          <img
            v-if="item.thumb || item.url"
            :src="item.thumb || item.url"
            :alt="item.title || ''"
            :style="imgCoverStyle"
          />
          <div v-else :style="placeholderStyle">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
              <rect x="3" y="3" width="18" height="18" rx="2" />
              <circle cx="8.5" cy="8.5" r="1.5" />
              <path d="m21 15-5-5L5 21" />
            </svg>
          </div>
        </div>
        <img
          v-else-if="item.thumb || item.url"
          :src="item.thumb || item.url"
          :alt="item.title || ''"
          :style="imgAutoStyle"
        />
        <div v-else :style="placeholderStyle">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <rect x="3" y="3" width="18" height="18" rx="2" />
            <circle cx="8.5" cy="8.5" r="1.5" />
            <path d="m21 15-5-5L5 21" />
          </svg>
        </div>
        <!-- Type badge (video/iframe) -->
        <div v-if="item.type && item.type !== 'image'" :style="typeBadgeStyle">
          {{ item.type === 'video' ? '▶' : '⊞' }} {{ item.type }}
        </div>
        <!-- Overlay icon -->
        <div :style="overlayStyle" class="olo-lb-overlay">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="11" cy="11" r="8"/>
            <line x1="21" y1="21" x2="16.65" y2="16.65"/>
            <line x1="11" y1="8" x2="11" y2="14"/>
            <line x1="8" y1="11" x2="14" y2="11"/>
          </svg>
        </div>
        <div v-if="s.show_caption && item.caption" :style="captionStyle">
          {{ item.caption }}
        </div>
      </div>
    </div>
    <p v-if="items.length === 0" :style="emptyStyle">
      {{ t('Aggiungi elementi alla lightbox') }}
    </p>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';
import { radiusToCss } from '@/composables/useRadius';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });

const defaults = {
  columns: '3',
  gap: '15',
  thumb_ratio: '1:1',
  thumb_radius: 8,
  // Cablato a 'cover' prima che esistesse il controllo: resta il default.
  thumb_fit: 'cover',
  object_position: 'center center',
  show_caption: true,
  items: [],
};

const s = computed(() => ({ ...defaults, ...props.settings }));

const items = computed(() => Array.isArray(s.value.items) ? s.value.items : []);

// Gemella ESATTA di $ratio_map nel renderer PHP (padding-bottom percentuale, non
// aspect-ratio): se qui manca una voce del select, il canvas ritaglia quadrato
// mentre il sito ritaglia giusto. Le due tabelle si toccano sempre insieme.
const ratioMap = {
  '1:1': '100%', '4:3': '75%', '3:2': '66.67%', '16:9': '56.25%', '21:9': '42.86%',
  '3:4': '133.33%', '4:5': '125%', '9:16': '177.78%', '2:3': '150%', 'auto': '0',
};
const ratioPercent = computed(() => ratioMap[s.value.thumb_ratio] || '100%');

const radiusCss = computed(() => radiusToCss(s.value.thumb_radius, { fallback: '8px' }));

const objectPosition = computed(() => s.value.object_position || 'center center');

const FIT = ['cover', 'contain', 'fill', 'none', 'scale-down'];
const objectFit = computed(() => (FIT.includes(s.value.thumb_fit) ? s.value.thumb_fit : 'cover'));

const imgCoverStyle = computed(() => {
  const st = {
    position: 'absolute',
    inset: '0',
    width: '100%',
    height: '100%',
    objectFit: objectFit.value,
  };
  // Con 'fill' l'immagine e' deformata per riempire: il focale non sposta nulla
  // e non si scrive (parita' col renderer PHP).
  if (objectFit.value !== 'fill') st.objectPosition = objectPosition.value;
  return st;
});

// Ramo 'auto' (nessun riquadro percentuale): l'adattamento si emette anche qui,
// gemello del renderer PHP. Il riquadro ha la forma della foto, quindi 'cover',
// 'contain' e 'fill' rendono identici — ma 'none' e 'scale-down' no, e senza
// questa riga il canvas mostrerebbe una cosa e il sito un'altra.
const imgAutoStyle = computed(() => {
  const st = { width: '100%', display: 'block', objectFit: objectFit.value };
  if (objectFit.value !== 'fill') st.objectPosition = objectPosition.value;
  return st;
});

const gridStyle = computed(() => ({
  display: 'grid',
  gridTemplateColumns: `repeat(${parseInt(s.value.columns) || 3}, 1fr)`,
  gap: (parseInt(s.value.gap) || 15) + 'px',
}));

const thumbStyle = computed(() => ({
  borderRadius: radiusCss.value,
  overflow: 'hidden',
  position: 'relative',
  cursor: 'pointer',
  background: 'var(--olo-color-muted, #F3F4F6)',
}));

const placeholderStyle = {
  position: 'absolute',
  inset: '0',
  display: 'flex',
  alignItems: 'center',
  justifyContent: 'center',
  background: 'var(--olo-color-muted, #F3F4F6)',
  color: 'var(--olo-color-text-muted, #9CA3AF)',
};

const overlayStyle = {
  position: 'absolute',
  inset: '0',
  display: 'flex',
  alignItems: 'center',
  justifyContent: 'center',
  background: 'rgba(0,0,0,0.3)',
  opacity: '0',
  transition: 'opacity 0.2s',
};

const typeBadgeStyle = {
  position: 'absolute',
  top: '8px',
  right: '8px',
  padding: '2px 8px',
  background: 'rgba(0,0,0,0.7)',
  color: '#fff',
  fontSize: '10px',
  borderRadius: '3px',
  textTransform: 'uppercase',
  fontWeight: '600',
  letterSpacing: '0.5px',
};

const captionStyle = {
  position: 'absolute',
  bottom: '0',
  left: '0',
  right: '0',
  padding: '6px 10px',
  background: 'rgba(0,0,0,0.6)',
  color: '#fff',
  fontSize: '11px',
};

const emptyStyle = {
  fontSize: '12px',
  color: 'var(--olo-color-text-muted, #9CA3AF)',
  textAlign: 'center',
  padding: '24px 0',
  margin: '0',
};
</script>

<style scoped>
.olo-lb-thumb:hover .olo-lb-overlay {
  opacity: 1 !important;
}
</style>
