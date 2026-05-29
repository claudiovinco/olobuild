<template>
  <div class="olo-timeline-preview" :class="'olo-tl--preset-' + (s.preset || 'classic-center')" :style="wrapStyle">
    <!-- Horizontal layout -->
    <template v-if="isHorizontal">
      <div class="olo-tl-h-wrap" style="position:relative">
        <!-- Arrows -->
        <button
          class="olo-tl-h-arrow olo-tl-h-arrow--prev"
          :style="arrowStyle"
          @click="hPrev"
        >{{ t('&lsaquo;') }}</button>
        <button
          class="olo-tl-h-arrow olo-tl-h-arrow--next"
          :style="arrowStyle"
          @click="hNext"
        >{{ t('&rsaquo;') }}</button>

        <!-- Viewport -->
        <div class="olo-tl-h-viewport" :style="hViewportStyle">
          <!-- Horizontal line -->
          <div :style="hLineStyle"></div>
          <div
            class="olo-tl-h-track"
            :style="hTrackStyle"
          >
            <div
              v-for="(item, i) in items"
              :key="item.id || i"
              class="olo-tl-h-item"
              :style="hItemStyle"
            >
              <!-- Marker -->
              <div :style="markerStyle(item, i)" class="olo-tl-marker">
                <template v-if="s.marker_type === 'icon' && item.icon">
                  <span :uk-icon="'icon: ' + item.icon" :style="{ color: item.icon_color || s.marker_color || 'var(--olo-color-primary, #6366F1)' }"></span>
                </template>
                <template v-else-if="s.marker_type === 'number'">
                  {{ i + 1 }}
                </template>
              </div>
              <!-- Card -->
              <div :style="cardStyle" class="olo-tl-card">
                <div v-if="item.image || item.video" :style="mediaWrapStyle">
                  <img v-if="item.image && !item.video" :src="item.image" :style="mediaInnerStyle" />
                  <iframe v-else-if="isEmbedVideo(item.video)" :src="getEmbedUrl(item.video)" :style="mediaInnerStyle" frameborder="0" allow="autoplay" allowfullscreen></iframe>
                  <video v-else-if="item.video" :src="item.video" :style="mediaInnerStyle" controls preload="metadata"></video>
                </div>
                <div v-if="item.date && s.date_position === 'above'" :style="dateStyle" style="margin-bottom:4px" :data-olo-editable="'items.' + i + '.date'">{{ item.date }}</div>
                <div v-if="item.date && s.date_position === 'inside'" :style="dateStyle" style="margin-bottom:4px" :data-olo-editable="'items.' + i + '.date'">{{ item.date }}</div>
                <h4 :style="titleStyle" :data-olo-editable="'items.' + i + '.title'">{{ item.title || 'Titolo' }}</h4>
                <div :style="{ ...descStyle, whiteSpace: 'pre-wrap' }" :data-olo-editable="'items.' + i + '.description'" data-olo-multiline>{{ item.description || '' }}</div>
              </div>
              <!-- Date below marker -->
              <div v-if="item.date && s.date_position === 'outside'" :style="dateStyle" style="margin-top:8px;text-align:center" :data-olo-editable="'items.' + i + '.date'">
                {{ item.date }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </template>

    <!-- Vertical layout -->
    <template v-else>
      <div class="olo-tl-v-wrap" :style="vWrapStyle">
        <!-- Vertical line -->
        <div :style="vLineStyle"></div>

        <div
          v-for="(item, i) in items"
          :key="item.id || i"
          class="olo-tl-v-item"
          :style="vItemStyle(i)"
        >
          <!-- Date column (spacer for center layout, content only when outside) -->
          <div
            v-if="isCenter"
            class="olo-tl-v-date"
            :style="vDateWrapStyle(i)"
          >
            <span v-if="item.date && s.date_position === 'outside'" :style="dateStyle" :data-olo-editable="'items.' + i + '.date'">{{ item.date }}</span>
          </div>

          <!-- Marker -->
          <div class="olo-tl-v-marker" :style="vMarkerWrapStyle">
            <div :style="markerStyle(item, i)" class="olo-tl-marker">
              <template v-if="s.marker_type === 'icon' && item.icon">
                <span :uk-icon="'icon: ' + item.icon" :style="{ color: item.icon_color || s.marker_color || 'var(--olo-color-primary, #6366F1)' }"></span>
              </template>
              <template v-else-if="s.marker_type === 'number'">
                {{ i + 1 }}
              </template>
            </div>
          </div>

          <!-- Card -->
          <div class="olo-tl-v-card" :style="vCardWrapStyle(i)">
            <div v-if="item.date && s.date_position === 'above'" :style="dateStyle" style="margin-bottom:6px" :data-olo-editable="'items.' + i + '.date'">{{ item.date }}</div>
            <div :style="cardStyle" class="olo-tl-card">
              <!-- Arrow -->
              <div v-if="s.card_arrow" :style="cardArrowStyle(i)" class="olo-tl-card-arrow"></div>
              <!-- Media -->
              <div v-if="item.image || item.video" :style="mediaWrapStyle">
                <img v-if="item.image && !item.video" :src="item.image" :style="mediaInnerStyle" />
                <iframe v-else-if="isEmbedVideo(item.video)" :src="getEmbedUrl(item.video)" :style="mediaInnerStyle" frameborder="0" allow="autoplay" allowfullscreen></iframe>
                <video v-else-if="item.video" :src="item.video" :style="mediaInnerStyle" controls preload="metadata"></video>
              </div>
              <div v-if="item.date && s.date_position === 'inside'" :style="dateStyle" style="margin-bottom:4px" :data-olo-editable="'items.' + i + '.date'">{{ item.date }}</div>
              <h4 :style="titleStyle" :data-olo-editable="'items.' + i + '.title'">{{ item.title || 'Titolo' }}</h4>
              <div :style="{ ...descStyle, whiteSpace: 'pre-wrap' }" :data-olo-editable="'items.' + i + '.description'" data-olo-multiline>{{ item.description || '' }}</div>
            </div>
            <!-- Date outside for non-center layouts -->
            <div v-if="!isCenter && item.date && s.date_position === 'outside'" :style="dateStyle" style="margin-top:6px" :data-olo-editable="'items.' + i + '.date'">
              {{ item.date }}
            </div>
          </div>
        </div>

        <!-- End marker -->
        <div v-if="s.end_marker" class="olo-tl-v-item" :style="endItemStyle">
          <div v-if="isCenter" class="olo-tl-v-date" style="flex:1;min-width:0"></div>
          <div class="olo-tl-v-marker" :style="vMarkerWrapStyle">
            <div :style="endMarkerStyle" class="olo-tl-marker">
              <span v-if="s.end_marker_icon" :uk-icon="'icon: ' + s.end_marker_icon" :style="{ color: endIconColor }"></span>
            </div>
          </div>
          <div v-if="isCenter" style="flex:1;min-width:0"></div>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { t } from '@/i18n';
import { ref, computed } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  layout: 'vertical-center',
  line_color: 'var(--olo-color-border, #E5E7EB)',
  line_width: '3',
  line_style: 'solid',
  marker_type: 'dot',
  marker_size: '20',
  marker_color: 'var(--olo-color-primary, #6366F1)',
  marker_bg: 'var(--olo-color-background, #FFFFFF)',
  marker_border_width: '3',
  marker_border_color: 'var(--olo-color-primary, #6366F1)',
  marker_shape: 'circle',
  end_marker: true,
  end_marker_icon: 'flag',
  end_marker_color: '',
  end_marker_bg: '',
  end_marker_size: '',
  card_bg: 'var(--olo-color-background, #FFFFFF)',
  card_text_color: 'var(--olo-color-text, #374151)',
  card_padding: '20',
  card_border_radius: '12',
  card_shadow: 'md',
  card_border_width: '0',
  card_border_color: 'var(--olo-color-border, #E5E7EB)',
  card_hover: 'lift',
  card_arrow: true,
  card_max_width: '',
  card_media_ratio: 'auto',
  card_media_margin: '0',
  card_media_radius: '4',
  date_position: 'outside',
  date_color: '#9CA3AF',
  date_size: '14',
  date_weight: '600',
  title_size: '18',
  title_weight: '600',
  title_color: '',
  description_size: '14',
  description_color: '',
  h_card_width: '300',
  h_visible_items: '3',
  h_gap: '24',
  h_arrow_color: 'var(--olo-color-text, #374151)',
  h_arrow_bg: 'var(--olo-color-muted, #F3F4F6)',
};
const s = computed(() => ({ ...defaults, ...props.settings }));

const items = computed(() => {
  const raw = s.value.items;
  if (Array.isArray(raw)) return raw;
  return [];
});

const isHorizontal = computed(() => s.value.layout === 'horizontal');
const isCenter = computed(() => s.value.layout === 'vertical-center');
const isRight = computed(() => s.value.layout === 'vertical-right');

const mSize = computed(() => parseInt(s.value.marker_size) || 20);
const lineW = computed(() => parseInt(s.value.line_width) || 3);

const shadowMap = {
  none: 'none',
  sm: '0 2px 6px rgba(0,0,0,.15)',
  md: '0 4px 12px rgba(0,0,0,.2)',
  lg: '0 8px 24px rgba(0,0,0,.3)',
};

// --- Wrap ---
const wrapStyle = computed(() => ({
  minHeight: '100px',
  position: 'relative',
}));

// --- Marker ---
function markerStyle(item, i) {
  const size = mSize.value;
  const shape = s.value.marker_shape || 'circle';
  const st = {
    width: size + 'px',
    height: size + 'px',
    display: 'flex',
    alignItems: 'center',
    justifyContent: 'center',
    flexShrink: '0',
    fontSize: (size * 0.45) + 'px',
    fontWeight: '700',
    color: item.icon_color || s.value.marker_color || 'var(--olo-color-primary, #6366F1)',
    background: s.value.marker_bg || 'var(--olo-color-background, #FFFFFF)',
    borderRadius: shape === 'circle' ? '50%' : shape === 'diamond' ? '4px' : '4px',
    transform: shape === 'diamond' ? 'rotate(45deg)' : 'none',
    position: 'relative',
    zIndex: '2',
  };
  const bw = parseInt(s.value.marker_border_width) || 0;
  if (bw > 0) {
    st.border = `${bw}px solid ${s.value.marker_border_color || s.value.marker_color || 'var(--olo-color-primary, #6366F1)'}`;
  }
  if (s.value.marker_type === 'dot') {
    st.background = s.value.marker_color || 'var(--olo-color-primary, #6366F1)';
  }
  return st;
}

// --- Card ---
const cardStyle = computed(() => {
  const pad = parseInt(s.value.card_padding) || 20;
  const radius = (v => isNaN(v) ? 12 : v)(parseInt(s.value.card_border_radius));
  const bw = parseInt(s.value.card_border_width) || 0;
  const st = {
    background: s.value.card_bg || 'var(--olo-color-background, #FFFFFF)',
    color: s.value.card_text_color || 'var(--olo-color-text, #374151)',
    padding: pad + 'px',
    borderRadius: radius + 'px',
    boxShadow: shadowMap[s.value.card_shadow] || 'none',
    transition: 'transform 0.3s, box-shadow 0.3s',
    position: 'relative',
    overflow: 'hidden',
  };
  const mw = parseInt(s.value.card_max_width) || 0;
  if (mw > 0) {
    st.maxWidth = mw + 'px';
  }
  if (bw > 0) {
    st.border = `${bw}px solid ${s.value.card_border_color || 'var(--olo-color-border, #E5E7EB)'}`;
  }
  return st;
});

// --- Media ---
const mediaWrapStyle = computed(() => {
  const m = parseInt(s.value.card_media_margin) || 0;
  const r = parseInt(s.value.card_media_radius) ?? 4;
  const ratio = s.value.card_media_ratio || 'auto';
  const st = {
    overflow: 'hidden',
    borderRadius: r + 'px',
    marginBottom: '8px',
  };
  if (m < 0) {
    // Negative margin = bleed outside padding
    const pad = parseInt(s.value.card_padding) || 20;
    st.margin = `${-pad}px ${-pad}px 8px ${-pad}px`;
    st.borderRadius = '0';
  } else if (m > 0) {
    st.margin = `${m}px ${m}px 8px ${m}px`;
  }
  if (ratio !== 'auto') {
    st.aspectRatio = ratio;
  }
  return st;
});

const mediaInnerStyle = computed(() => {
  const ratio = s.value.card_media_ratio || 'auto';
  return {
    width: '100%',
    height: ratio !== 'auto' ? '100%' : 'auto',
    objectFit: ratio !== 'auto' ? 'cover' : undefined,
    display: 'block',
  };
});

function isEmbedVideo(url) {
  if (!url) return false;
  return /youtube|youtu\.be|vimeo/i.test(url);
}

function getEmbedUrl(url) {
  if (!url) return '';
  const yt = url.match(/(?:youtube\.com\/(?:watch\?v=|embed\/|shorts\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/);
  if (yt) return 'https://www.youtube-nocookie.com/embed/' + yt[1];
  const vm = url.match(/vimeo\.com\/(\d+)/);
  if (vm) return 'https://player.vimeo.com/video/' + vm[1];
  return url;
}

// --- Date ---
const dateStyle = computed(() => ({
  fontSize: (parseInt(s.value.date_size) || 14) + 'px',
  fontWeight: s.value.date_weight || '600',
  color: s.value.date_color || '#9CA3AF',
}));

// --- Title ---
const titleStyle = computed(() => ({
  fontSize: (parseInt(s.value.title_size) || 18) + 'px',
  fontWeight: s.value.title_weight || '600',
  color: s.value.title_color || '',
  margin: '0 0 6px',
  lineHeight: '1.3',
}));

// --- Description ---
const descStyle = computed(() => ({
  fontSize: (parseInt(s.value.description_size) || 14) + 'px',
  color: s.value.description_color || '',
  opacity: '0.85',
  lineHeight: '1.5',
  margin: '0',
}));

// ===================
// VERTICAL LAYOUT
// ===================
const vWrapStyle = computed(() => ({
  position: 'relative',
  display: 'flex',
  flexDirection: 'column',
  gap: '24px',
}));

const vLineStyle = computed(() => {
  const layout = s.value.layout || 'vertical-center';
  const w = lineW.value;
  const st = {
    position: 'absolute',
    top: '0',
    bottom: '0',
    width: w + 'px',
    background: s.value.line_color || 'var(--olo-color-border, #E5E7EB)',
    borderStyle: s.value.line_style || 'solid',
    zIndex: '1',
  };
  if (s.value.line_style !== 'solid') {
    st.background = 'none';
    st.borderLeftWidth = w + 'px';
    st.borderLeftStyle = s.value.line_style;
    st.borderLeftColor = s.value.line_color || 'var(--olo-color-border, #E5E7EB)';
    st.width = '0';
  }
  if (layout === 'vertical-center') {
    st.left = '50%';
    st.transform = 'translateX(-50%)';
  } else if (layout === 'vertical-right') {
    st.right = (mSize.value / 2 - w / 2) + 'px';
  } else {
    st.left = (mSize.value / 2 - w / 2) + 'px';
  }
  return st;
});

function vItemStyle(i) {
  const layout = s.value.layout || 'vertical-center';
  const st = {
    display: 'flex',
    alignItems: 'flex-start',
    gap: '16px',
    position: 'relative',
  };
  if (layout === 'vertical-center') {
    st.flexDirection = i % 2 === 0 ? 'row' : 'row-reverse';
  } else if (layout === 'vertical-right') {
    st.flexDirection = 'row-reverse';
  }
  return st;
}

const vMarkerWrapStyle = computed(() => ({
  flexShrink: '0',
  display: 'flex',
  alignItems: 'center',
  justifyContent: 'center',
  zIndex: '2',
}));

function vCardWrapStyle(i) {
  const layout = s.value.layout || 'vertical-center';
  if (layout === 'vertical-center') {
    return { flex: '1', minWidth: '0' };
  }
  return { flex: '1', minWidth: '0' };
}

function vDateWrapStyle(i) {
  return {
    flex: '1',
    minWidth: '0',
    display: 'flex',
    alignItems: 'flex-start',
    justifyContent: i % 2 === 0 ? 'flex-end' : 'flex-start',
    paddingTop: '4px',
  };
}

function cardArrowStyle(i) {
  const layout = s.value.layout || 'vertical-center';
  const bg = s.value.card_bg || 'var(--olo-color-background, #FFFFFF)';
  const size = 8;
  const base = {
    position: 'absolute',
    top: '14px',
    width: '0',
    height: '0',
    borderStyle: 'solid',
  };
  // Determine arrow direction
  let pointsLeft = true;
  if (layout === 'vertical-center') {
    pointsLeft = i % 2 === 0;
  } else if (layout === 'vertical-right') {
    pointsLeft = false;
  }
  if (pointsLeft) {
    base.left = -size + 'px';
    base.borderWidth = size + 'px ' + size + 'px ' + size + 'px 0';
    base.borderColor = `transparent ${bg} transparent transparent`;
  } else {
    base.right = -size + 'px';
    base.borderWidth = size + 'px 0 ' + size + 'px ' + size + 'px';
    base.borderColor = `transparent transparent transparent ${bg}`;
  }
  return base;
}

// ===================
// END MARKER
// ===================
const endMkSize = computed(() => parseInt(s.value.end_marker_size) || mSize.value);

const endMarkerStyle = computed(() => {
  const size = endMkSize.value;
  const shape = s.value.marker_shape || 'circle';
  const st = {
    width: size + 'px',
    height: size + 'px',
    display: 'flex',
    alignItems: 'center',
    justifyContent: 'center',
    flexShrink: '0',
    fontSize: (size * 0.5) + 'px',
    color: s.value.end_marker_color || s.value.marker_color || 'var(--olo-color-primary, #6366F1)',
    background: s.value.end_marker_bg || s.value.marker_bg || 'var(--olo-color-background, #FFFFFF)',
    borderRadius: shape === 'circle' ? '50%' : '4px',
    transform: shape === 'diamond' ? 'rotate(45deg)' : 'none',
    position: 'relative',
    zIndex: '2',
  };
  const bw = parseInt(s.value.marker_border_width) || 0;
  if (bw > 0) {
    st.border = `${bw}px solid ${s.value.marker_border_color || s.value.marker_color || 'var(--olo-color-primary, #6366F1)'}`;
  }
  return st;
});

const endIconColor = computed(() => s.value.end_marker_color || s.value.marker_color || 'var(--olo-color-primary, #6366F1)');

const endItemStyle = computed(() => ({
  display: 'flex',
  alignItems: 'flex-start',
  justifyContent: 'center',
  gap: '16px',
  position: 'relative',
}));

// ===================
// HORIZONTAL LAYOUT
// ===================
const hScrollPx = ref(0);

const hCardW = computed(() => parseInt(s.value.h_card_width) || 300);
const hGap = computed(() => parseInt(s.value.h_gap) || 24);
const hVis = computed(() => parseInt(s.value.h_visible_items) || 3);
const hViewportW = computed(() => hVis.value * hCardW.value + (hVis.value - 1) * hGap.value);
const hTrackW = computed(() => items.value.length * hCardW.value + Math.max(0, items.value.length - 1) * hGap.value);
const hMaxScroll = computed(() => Math.max(0, hTrackW.value - hViewportW.value));

const hViewportStyle = computed(() => ({
  overflow: 'hidden',
  maxWidth: hViewportW.value + 'px',
  margin: '0 auto',
}));

function hPrev() {
  const step = hCardW.value + hGap.value;
  hScrollPx.value = Math.max(0, hScrollPx.value - step);
}
function hNext() {
  const step = hCardW.value + hGap.value;
  hScrollPx.value = Math.min(hMaxScroll.value, hScrollPx.value + step);
}

const hLineStyle = computed(() => ({
  position: 'absolute',
  top: (mSize.value / 2) + 'px',
  left: '0',
  right: '0',
  height: lineW.value + 'px',
  background: s.value.line_color || 'var(--olo-color-border, #E5E7EB)',
  zIndex: '1',
}));

const hTrackStyle = computed(() => ({
  display: 'flex',
  gap: hGap.value + 'px',
  transition: 'transform 0.4s ease',
  transform: `translateX(-${hScrollPx.value}px)`,
  position: 'relative',
  zIndex: '2',
}));

const hItemStyle = computed(() => ({
  width: hCardW.value + 'px',
  flexShrink: '0',
  display: 'flex',
  flexDirection: 'column',
  alignItems: 'center',
  gap: '12px',
}));

const arrowStyle = computed(() => ({
  position: 'absolute',
  top: '50%',
  transform: 'translateY(-50%)',
  zIndex: '10',
  width: '36px',
  height: '36px',
  borderRadius: '50%',
  border: 'none',
  cursor: 'pointer',
  fontSize: '20px',
  fontWeight: '700',
  display: 'flex',
  alignItems: 'center',
  justifyContent: 'center',
  color: s.value.h_arrow_color || 'var(--olo-color-text, #374151)',
  background: s.value.h_arrow_bg || 'var(--olo-color-muted, #F3F4F6)',
}));
</script>

<style scoped>
.olo-tl-h-arrow--prev {
  left: -8px;
}
.olo-tl-h-arrow--next {
  right: -8px;
}

.olo-tl-card h4 {
  margin: 0 0 6px;
}

.olo-tl-card :deep(p) {
  margin: 0 0 0.5em;
}
.olo-tl-card :deep(p:last-child) {
  margin-bottom: 0;
}

.olo-tl-marker {
  transition: transform 0.3s;
}

/* ───── Preset visual hints in builder ───── */

/* Liquid Glass */
.olo-tl--preset-liquid-glass :deep(.olo-tl-card) {
  background: rgba(255,255,255,0.55) !important;
  backdrop-filter: blur(12px) saturate(180%);
  -webkit-backdrop-filter: blur(12px) saturate(180%);
  border: 1px solid rgba(255,255,255,0.5) !important;
  box-shadow: 0 8px 24px rgba(0,0,0,0.10) !important;
}
.olo-tl--preset-liquid-glass :deep(.olo-tl-marker) {
  background: rgba(255,255,255,0.7) !important;
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px);
}

/* Neon Cyber */
.olo-tl--preset-neon-cyber :deep(.olo-tl-card) {
  background: #0a0f1c !important;
  border: 1px solid rgba(255,106,42,0.45) !important;
  color: rgba(255,255,255,0.85) !important;
  box-shadow: 0 0 14px rgba(255,106,42,0.20) !important;
}
.olo-tl--preset-neon-cyber :deep(.olo-tl-title) {
  color: #ff6a2a !important;
  text-shadow: 0 0 6px rgba(255,106,42,0.5);
  text-transform: uppercase;
}
.olo-tl--preset-neon-cyber :deep(.olo-tl-desc) { color: rgba(255,255,255,0.78) !important; }
.olo-tl--preset-neon-cyber :deep(.olo-tl-date) { color: #ff6a2a !important; }
.olo-tl--preset-neon-cyber :deep(.olo-tl-line) { background: #ff6a2a !important; box-shadow: 0 0 6px rgba(255,106,42,0.6); }
.olo-tl--preset-neon-cyber :deep(.olo-tl-marker) {
  background: #0a0f1c !important;
  border: 2px solid #ff6a2a !important;
  color: #ff6a2a !important;
}

/* Brutalist */
.olo-tl--preset-brutalist-block :deep(.olo-tl-card) {
  background: #fff !important;
  border: 3px solid #000 !important;
  box-shadow: 5px 5px 0 0 #000 !important;
  border-radius: 0 !important;
  color: #000 !important;
}
.olo-tl--preset-brutalist-block :deep(.olo-tl-title) {
  color: #000 !important;
  font-weight: 900 !important;
  text-transform: uppercase;
}
.olo-tl--preset-brutalist-block :deep(.olo-tl-desc) { color: #000 !important; font-weight: 500; }
.olo-tl--preset-brutalist-block :deep(.olo-tl-date) { color: #000 !important; font-weight: 800; text-transform: uppercase; }
.olo-tl--preset-brutalist-block :deep(.olo-tl-line) { background: #000 !important; }
.olo-tl--preset-brutalist-block :deep(.olo-tl-marker) {
  background: #000 !important;
  color: #fff !important;
  border: 3px solid #000 !important;
  border-radius: 0 !important;
}

/* Magnetic */
.olo-tl--preset-magnetic-liquid :deep(.olo-tl-card) {
  border-radius: 18px !important;
  box-shadow: 0 8px 22px rgba(232,98,42,0.16) !important;
}
.olo-tl--preset-magnetic-liquid :deep(.olo-tl-line) {
  background: linear-gradient(180deg, #e8622a 0%, #ff8a5b 100%) !important;
}
.olo-tl--preset-magnetic-liquid :deep(.olo-tl-marker) {
  background: #e8622a !important;
  color: #fff !important;
  box-shadow: 0 6px 14px rgba(232,98,42,0.4) !important;
}
.olo-tl--preset-magnetic-liquid :deep(.olo-tl-date) { color: #e8622a !important; font-weight: 700; }

/* Sticker */
.olo-tl--preset-sticker :deep(.olo-tl-card) {
  background: #fff !important;
  border: 2px dashed rgba(232,98,42,0.55) !important;
  box-shadow: 0 8px 18px rgba(0,0,0,0.10) !important;
}
.olo-tl--preset-sticker :deep(.olo-tl-marker) {
  background: #e8622a !important;
  color: #fff !important;
  border: 3px dashed #f3aa86 !important;
}
.olo-tl--preset-sticker :deep(.olo-tl-line) {
  background: rgba(232,98,42,0.4) !important;
}

/* Retro Terminal */
.olo-tl--preset-retro-terminal :deep(.olo-tl-card) {
  background: #0c0c0c !important;
  border: 1px solid rgba(0,255,140,0.4) !important;
  font-family: ui-monospace, SFMono-Regular, Menlo, monospace !important;
  background-image: repeating-linear-gradient(0deg, transparent 0, transparent 2px, rgba(0,255,140,0.06) 2px, rgba(0,255,140,0.06) 3px);
  border-radius: 0 !important;
}
.olo-tl--preset-retro-terminal :deep(.olo-tl-title) {
  color: #00ff8c !important;
  text-shadow: 0 0 6px rgba(0,255,140,0.5);
  font-family: inherit !important;
  text-transform: uppercase;
}
.olo-tl--preset-retro-terminal :deep(.olo-tl-title)::before {
  content: '> ';
  opacity: 0.7;
}
.olo-tl--preset-retro-terminal :deep(.olo-tl-desc) { color: rgba(0,255,140,0.85) !important; font-family: inherit !important; }
.olo-tl--preset-retro-terminal :deep(.olo-tl-date) { color: #00ff8c !important; font-family: inherit !important; }
.olo-tl--preset-retro-terminal :deep(.olo-tl-line) { background: #00ff8c !important; }
.olo-tl--preset-retro-terminal :deep(.olo-tl-marker) {
  background: #0c0c0c !important;
  border: 2px solid #00ff8c !important;
  color: #00ff8c !important;
  border-radius: 0 !important;
}

/* 3D Tilt */
.olo-tl--preset-3d-tilt :deep(.olo-tl-card) {
  box-shadow: 0 18px 36px rgba(0,0,0,0.14) !important;
  transform: perspective(900px) rotateX(2deg);
  transform-origin: center top;
}
</style>
