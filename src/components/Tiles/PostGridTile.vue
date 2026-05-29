<template>
  <div class="olo-postgrid-preview">
    <!-- Loading overlay -->
    <div v-if="loading" class="mpg-loading">
      <span class="mpg-spinner"></span>
    </div>
    <div class="mpg-grid" :style="gridStyle">
      <div v-for="(item, idx) in displayItems" :key="idx" class="mpg-card" :class="cardClasses" :style="cardStyle">
        <!-- Image area -->
        <div v-if="settings.show_image !== false" class="mpg-img-wrap" :style="{ height: imgHeight + 'px' }">
          <div :class="['mpg-img-bg', hoverImgClass, kenburnsClass]" :style="{ ...kenburnsStyle, ...(item.image ? { backgroundImage: 'url(' + item.image + ')', backgroundSize: 'cover', backgroundPosition: 'center' } : {}) }"></div>
          <!-- Overlay gradient -->
          <div v-if="settings.overlay_gradient" class="mpg-overlay" :style="overlayStyle"></div>
          <!-- Category badge -->
          <span v-if="settings.show_category !== false" class="mpg-category">{{ item.category || 'Categoria' }}</span>
          <!-- Ribbon -->
          <span
            v-if="settings.ribbon_field"
            class="mpg-ribbon"
            :class="'mpg-ribbon--' + (settings.ribbon_position || 'top-right')"
            :style="{ background: settings.ribbon_bg || '#e11d48', color: settings.ribbon_color || '#fff' }"
          >{{ t('Ribbon') }}</span>
          <!-- Opening badge -->
          <span v-if="settings.show_service_opening && item.opening_type" class="mpg-opening" :style="{ background: item.opening_type === 'annual' ? (settings.opening_bg_annual || '#059669') : (settings.opening_bg_seasonal || '#d97706'), fontSize: (parseInt(settings.opening_size) || 11) * 0.65 + 'px' }">{{ item.opening_type === 'annual' ? 'Annuale' : 'Stagionale' }}</span>
          <span v-else-if="settings.show_service_opening && !hasRealData" class="mpg-opening" :style="{ background: idx % 2 === 0 ? (settings.opening_bg_annual || '#059669') : (settings.opening_bg_seasonal || '#d97706'), fontSize: (parseInt(settings.opening_size) || 11) * 0.65 + 'px' }">{{ idx % 2 === 0 ? 'Annuale' : 'Stagionale' }}</span>
        </div>
        <!-- Card body -->
        <div class="mpg-body" :style="bodyStyle">
          <div class="mpg-title" :style="{ fontSize: titleSize, color: settings.title_color || undefined }">{{ item.title }}</div>
          <div v-if="settings.show_meta !== false" class="mpg-meta" :style="{ color: settings.meta_color || undefined }">{{ item.date_fmt }} · {{ item.author }}</div>
          <div v-if="settings.show_excerpt !== false" class="mpg-excerpt" :style="{ fontSize: excerptSize, color: settings.excerpt_color || undefined }">{{ item.excerpt }}</div>
          <!-- Service stats preview -->
          <div v-if="settings.show_service_stats" class="mpg-service-stats">
            <span class="mpg-stat" :title="t('Ospiti')">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none"><circle cx="9" cy="7" r="3" stroke="currentColor" stroke-width="2"/><path d="M3 20C3 16.6863 5.68629 14 9 14C12.3137 14 15 16.6863 15 20" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg> {{ item.service_capacity || '6' }}
            </span>
            <span class="mpg-stat" :title="t('Camere')">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none"><path d="M3 18V12C3 10.3431 4.34315 9 6 9H18C19.6569 9 21 10.3431 21 12V18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M3 18V20M21 18V20" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg> {{ item.service_bedrooms || '3' }}
            </span>
            <span class="mpg-stat" :title="t('Bagni')">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none"><path d="M4 12H20V15C20 17.2091 18.2091 19 16 19H8C5.79086 19 4 17.2091 4 15V12Z" stroke="currentColor" stroke-width="2"/></svg> {{ item.service_bathrooms || '2' }}
            </span>
            <span v-if="item.service_altitude" class="mpg-stat" :title="t('Altitudine')">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none"><path d="M12 3L20 19H4L12 3Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></svg> {{ item.service_altitude }}m
            </span>
          </div>
          <!-- Service club preview -->
          <div v-if="settings.show_service_club" class="mpg-service-club">{{ item.service_club ? (item.service_club + (item.service_stars ? ' · ' + item.service_stars + ' stelle' : '')) : 'Dolomiti · 3 stelle' }}</div>
          <div v-if="settings.show_price && item.price != null" class="mpg-price">{{ settings.price_prefix || '€' }}{{ item.price }}{{ settings.price_suffix || '' }}</div>
          <div v-else-if="settings.show_price" class="mpg-price">{{ settings.price_prefix || '€' }}99{{ settings.price_suffix || '' }}</div>
          <div v-if="settings.link_style === 'button'" class="mpg-btn" data-olo-editable="link_text">{{ settings.link_text || 'Vedi' }}</div>
          <div v-else-if="settings.link_style === 'text'" class="mpg-link" data-olo-editable="link_text">{{ settings.link_text || 'Vedi' }} →</div>
        </div>
      </div>
    </div>
    <!-- Pagination preview (matches frontend .olo-pg-pagination) -->
    <div v-if="settings.pagination" class="mpg-pagination">
      <template v-if="paginationStyle === 'dots'">
        <span v-for="i in pagPageCount" :key="i" class="mpg-pag-dot" :class="{ 'mpg-pag-active': i === 1 }"></span>
      </template>
      <template v-else-if="paginationStyle === 'numbers'">
        <span v-for="i in pagPageCount" :key="i" class="mpg-pag-num" :class="{ 'mpg-pag-active': i === 1 }">{{ i }}</span>
      </template>
      <template v-else-if="paginationStyle === 'arrows'">
        <span class="mpg-pag-btn" style="opacity:0.35">‹</span>
        <span class="mpg-pag-info">1 / {{ pagPageCount }}</span>
        <span class="mpg-pag-btn">›</span>
      </template>
      <template v-else-if="paginationStyle === 'loadmore'">
        <span class="mpg-pag-loadmore">{{ t('Carica altri') }}</span>
      </template>
    </div>
    <!-- Info bar -->
    <div class="mpg-info">
      {{ settings.post_type || 'post' }} · {{ settings.posts_per_page || 12 }} {{ t('articoli') }} · {{ cols }} {{ t('colonne') }}<template v-if="settings.pagination"> · {{ settings.items_per_page || 6 }}/{{ t('pagina') }}</template><template v-if="hasRealData"> · <span class="mpg-live-badge">{{ t('anteprima reale') }}</span></template>
    </div>
  </div>
</template>

<script setup>
import { t } from '@/i18n';
import { computed, ref, watch, inject, onMounted } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const oloData = inject('oloData', { restUrl: '/wp-json/olo/v1', nonce: '' });

// Fake data fallback
const fakeTitles = ['Come migliorare le performance', 'Guida completa al design', 'Le ultime novità del settore', 'Consigli pratici per iniziare', 'Tendenze e ispirazioni', 'Strategie per il successo'];
const fakeDates = ['12 Feb 2026', '8 Gen 2026', '23 Mar 2026', '5 Apr 2026', '17 Nov 2025', '30 Dic 2025'];
const fakeAuthors = ['Marco B.', 'Sara L.', 'Luca R.', 'Anna M.', 'Giorgio P.', 'Elena T.'];
const fakeExcerpts = ['Scopri le tecniche più efficaci per ottimizzare ogni aspetto del tuo progetto...', 'Una panoramica completa su strumenti, metodi e best practice da adottare...', 'Le novità più interessanti e come possono influenzare il tuo lavoro quotidiano...', 'Passi concreti e suggerimenti utili per chi vuole partire col piede giusto...', 'Esplora le tendenze emergenti e lasciati ispirare da esempi reali...', 'Approcci collaudati per raggiungere obiettivi ambiziosi con efficacia...'];

// Real data state
const realPosts = ref([]);
const loading = ref(false);
const hasRealData = computed(() => realPosts.value.length > 0);

// Query key for debounced fetch
const queryKey = computed(() => JSON.stringify({
  post_type: props.settings.post_type || 'post',
  posts_per_page: props.settings.posts_per_page || '12',
  orderby: props.settings.orderby || 'date',
  order: props.settings.order || 'DESC',
  meta_key: props.settings.meta_key || '',
  meta_filter: props.settings.meta_filter || '',
  excerpt_length: props.settings.excerpt_length || '20',
  price_field: props.settings.price_field || '',
}));

let fetchTimer = null;
const fetchCache = {};

async function fetchPosts() {
  const s = props.settings;
  const params = new URLSearchParams({
    post_type: s.post_type || 'post',
    posts_per_page: s.posts_per_page || '12',
    orderby: s.orderby || 'date',
    order: s.order || 'DESC',
    excerpt_length: s.excerpt_length || '20',
  });
  if (s.meta_key) params.set('meta_key', s.meta_key);
  if (s.meta_filter) params.set('meta_filter', s.meta_filter);
  if (s.price_field) params.set('price_field', s.price_field);

  const cacheKey = params.toString();
  if (fetchCache[cacheKey]) {
    realPosts.value = fetchCache[cacheKey];
    return;
  }

  loading.value = true;
  try {
    const url = `${oloData.restUrl}/postgrid-preview?${params}`;
    const res = await fetch(url, {
      headers: { 'X-WP-Nonce': oloData.nonce },
    });
    if (res.ok) {
      const data = await res.json();
      if (Array.isArray(data) && data.length > 0) {
        realPosts.value = data;
        fetchCache[cacheKey] = data;
      } else {
        realPosts.value = [];
      }
    }
  } catch (e) {
    // Silently fall back to fake data
    realPosts.value = [];
  } finally {
    loading.value = false;
  }
}

watch(queryKey, () => {
  clearTimeout(fetchTimer);
  fetchTimer = setTimeout(fetchPosts, 600);
});

onMounted(() => {
  fetchTimer = setTimeout(fetchPosts, 300);
});

// Display items: real or fake
const cols = computed(() => parseInt(props.settings.columns) || 3);
const cardCount = computed(() => Math.min(parseInt(props.settings.posts_per_page) || 6, cols.value * 2));

const displayItems = computed(() => {
  const count = cardCount.value;
  if (hasRealData.value) {
    return realPosts.value.slice(0, count);
  }
  // Fake data fallback
  const items = [];
  for (let i = 0; i < count; i++) {
    items.push({
      title: fakeTitles[i % fakeTitles.length],
      date_fmt: fakeDates[i % fakeDates.length],
      author: fakeAuthors[i % fakeAuthors.length],
      excerpt: fakeExcerpts[i % fakeExcerpts.length],
      image: '',
      category: '',
    });
  }
  return items;
});

const imgHeight = computed(() => {
  const h = parseInt(props.settings.image_height) || 200;
  const c = cols.value;
  if (c >= 4) return Math.min(h, 100);
  if (c >= 3) return Math.min(h, 130);
  return Math.min(h, 180);
});

const gapMap = { collapse: '0px', small: '8px', default: '12px', medium: '16px', large: '24px' };
const gap = computed(() => gapMap[props.settings.gap || 'medium'] || '16px');

const gridStyle = computed(() => ({
  display: 'grid',
  gridTemplateColumns: `repeat(${cols.value}, 1fr)`,
  gap: gap.value,
}));

const cardClasses = computed(() => {
  const cs = props.settings.card_style || 'default';
  return ['mpg-card--' + cs];
});

const cardStyle = computed(() => {
  const style = { borderRadius: (parseInt(props.settings.card_radius) || 4) + 'px' };
  if ((props.settings.card_style || 'default') === 'primary') {
    style.background = props.settings.card_primary_bg || 'var(--olo-color-primary, #6366F1)';
    style.borderColor = props.settings.card_primary_bg || 'var(--olo-color-primary, #6366F1)';
  }
  return style;
});

const hoverImgClass = computed(() => {
  const fx = props.settings.hover_effect || 'none';
  return fx !== 'none' ? 'mpg-hover-' + fx : '';
});

// Stile testo
const bodyStyle = computed(() => {
  const style = { padding: (parseInt(props.settings.body_padding) ?? 15) + 'px' };
  const bg = props.settings.body_bg;
  if (bg && bg.length >= 7) {
    const r = parseInt(bg.slice(1, 3), 16);
    const g = parseInt(bg.slice(3, 5), 16);
    const b = parseInt(bg.slice(5, 7), 16);
    const a = (parseInt(props.settings.body_bg_opacity) || 100) / 100;
    style.background = `rgba(${r},${g},${b},${a})`;
  }
  return style;
});

const titleSize = computed(() => (parseFloat(props.settings.title_size) || 1) + 'em');
const excerptSize = computed(() => (parseFloat(props.settings.excerpt_size) || 0.92) + 'em');

// Overlay gradient
const overlayStyle = computed(() => {
  const color = props.settings.overlay_color || '#000000';
  const opacity = (parseInt(props.settings.overlay_opacity) || 50) / 100;
  const dir = props.settings.overlay_direction || 'bottom';
  const height = parseInt(props.settings.overlay_height) || 50;

  const r = parseInt(color.slice(1, 3), 16);
  const g = parseInt(color.slice(3, 5), 16);
  const b = parseInt(color.slice(5, 7), 16);

  const dirMap = { bottom: 'to top', top: 'to bottom', left: 'to right', right: 'to left' };
  const cssDir = dirMap[dir] || 'to top';

  const isHoriz = dir === 'left' || dir === 'right';

  return {
    position: 'absolute',
    ...(dir === 'bottom' ? { bottom: 0, left: 0, right: 0 } : {}),
    ...(dir === 'top' ? { top: 0, left: 0, right: 0 } : {}),
    ...(dir === 'left' ? { top: 0, left: 0, bottom: 0 } : {}),
    ...(dir === 'right' ? { top: 0, right: 0, bottom: 0 } : {}),
    width: isHoriz ? height + '%' : '100%',
    height: isHoriz ? '100%' : height + '%',
    pointerEvents: 'none',
    zIndex: 1,
    background: `linear-gradient(${cssDir}, rgba(${r},${g},${b},${opacity}), transparent)`,
  };
});

// Ken Burns
const kenburnsClass = computed(() => props.settings.fx_kenburns ? 'mpg-kenburns' : '');
const kenburnsStyle = computed(() => {
  if (!props.settings.fx_kenburns) return {};
  const speed = parseInt(props.settings.fx_kenburns_speed) || 20;
  const scale = parseFloat(props.settings.fx_kenburns_scale) || 1.12;
  return {
    '--kb-speed': speed + 's',
    '--kb-scale': scale,
  };
});

// Pagination
const paginationStyle = computed(() => props.settings.pagination_style || 'dots');
const pagPageCount = computed(() => {
  const total = parseInt(props.settings.posts_per_page) || 12;
  const perPage = parseInt(props.settings.items_per_page) || 6;
  return Math.max(1, Math.ceil(total / perPage));
});
</script>

<style scoped>
.olo-postgrid-preview {
  min-height: 120px;
  position: relative;
}
.mpg-grid {
  display: grid;
}
.mpg-card {
  background: #fff;
  overflow: hidden;
  border: 1px solid #e5e7eb;
}
.mpg-card--hover {
  transition: box-shadow 0.3s ease;
}
.mpg-card--hover:hover {
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
.mpg-card--primary .mpg-title,
.mpg-card--primary .mpg-meta,
.mpg-card--primary .mpg-excerpt,
.mpg-card--primary .mpg-price {
  color: rgba(255, 255, 255, 0.85);
}
.mpg-card--primary .mpg-title {
  color: #fff;
}
.mpg-card--primary .mpg-btn {
  background: rgba(255, 255, 255, 0.2);
  border: 1px solid #fff;
}
.mpg-card--primary .mpg-link {
  color: #fff;
}

/* Loading */
.mpg-loading {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(255,255,255,0.6);
  z-index: 10;
  border-radius: 4px;
}
.mpg-spinner {
  width: 20px;
  height: 20px;
  border: 2px solid #e5e7eb;
  border-top-color: var(--olo-color-primary, #6366F1);
  border-radius: 50%;
  animation: mpg-spin 0.6s linear infinite;
}
@keyframes mpg-spin {
  to { transform: rotate(360deg); }
}

/* Image area */
.mpg-img-wrap {
  position: relative;
  overflow: hidden;
}
.mpg-img-bg {
  width: 100%;
  height: 100%;
  background: linear-gradient(135deg, #94a3b8 0%, #64748b 100%);
  transition: transform 0.5s ease, filter 0.5s ease;
}

/* Hover effects */
.mpg-card:hover .mpg-hover-zoom { transform: scale(1.08); }
.mpg-card:hover .mpg-hover-zoom-rotate { transform: scale(1.08) rotate(2deg); }
.mpg-hover-brightness { filter: brightness(0.7); }
.mpg-card:hover .mpg-hover-brightness { filter: brightness(1); }
.mpg-hover-desaturate { filter: grayscale(100%); }
.mpg-card:hover .mpg-hover-desaturate { filter: grayscale(0%); }
.mpg-hover-blur-in { filter: blur(3px); }
.mpg-card:hover .mpg-hover-blur-in { filter: blur(0); }
/* New hover effects */
.mpg-card:hover .mpg-hover-slide-up { transform: translateY(-8px) scale(1.02); }
.mpg-hover-glow { filter: brightness(1); }
.mpg-card:hover .mpg-hover-glow { filter: brightness(1.15) saturate(1.2); }
.mpg-card { perspective: 800px; }
.mpg-card:hover .mpg-hover-tilt { transform: rotateY(4deg) rotateX(2deg) scale(1.03); }

/* Ken Burns preview */
@keyframes mpg-kenburns {
  0% { transform: scale(1); }
  50% { transform: scale(var(--kb-scale, 1.12)); }
  100% { transform: scale(1); }
}
.mpg-kenburns {
  animation: mpg-kenburns var(--kb-speed, 20s) ease-in-out infinite;
}

/* Overlay */
.mpg-overlay {
  border-radius: 0;
}

/* Category badge */
.mpg-category {
  position: absolute;
  bottom: 8px;
  left: 8px;
  background: rgba(0,0,0,0.7);
  color: #fff;
  font-size: 9px;
  padding: 2px 6px;
  border-radius: 3px;
  z-index: 2;
}

/* Ribbon */
.mpg-ribbon {
  position: absolute;
  font-size: 9px;
  font-weight: 700;
  padding: 3px 10px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  z-index: 2;
}
.mpg-ribbon--top-right {
  top: 0;
  right: 12px;
  border-radius: 0 0 4px 4px;
}
.mpg-ribbon--top-left {
  top: 0;
  left: 12px;
  border-radius: 0 0 4px 4px;
}

/* Card body */
.mpg-body {
  padding: 8px 10px;
}
.mpg-title {
  font-size: 11px;
  font-weight: 700;
  color: var(--olo-color-text, #374151);
  margin-bottom: 2px;
}
.mpg-meta {
  font-size: 9px;
  color: #9ca3af;
  margin-bottom: 4px;
}
.mpg-excerpt {
  font-size: 9px;
  color: #6b7280;
  line-height: 1.3;
  margin-bottom: 4px;
  overflow: hidden;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
}
.mpg-price {
  font-size: 11px;
  font-weight: 700;
  color: #059669;
  margin-bottom: 4px;
}
.mpg-btn {
  display: inline-block;
  font-size: 9px;
  padding: 3px 8px;
  background: var(--olo-color-primary, #6366F1);
  color: #fff;
  border-radius: 3px;
}
.mpg-link {
  font-size: 9px;
  color: var(--olo-color-primary, #6366F1);
}

/* Pagination preview */
/* Pagination — matches frontend .olo-pg-pagination */
.mpg-pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 8px;
  margin-top: 24px;
  flex-wrap: wrap;
}
.mpg-pag-dot {
  display: inline-block;
  width: 10px;
  height: 10px;
  border-radius: 50%;
  border: 2px solid #d1d5db;
  background: transparent;
  cursor: pointer;
  transition: background 0.2s, border-color 0.2s;
}
.mpg-pag-dot:hover { border-color: #9ca3af; }
.mpg-pag-dot.mpg-pag-active {
  background: var(--olo-color-primary, #6366F1);
  border-color: var(--olo-color-primary, #6366F1);
}
.mpg-pag-num {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 32px;
  height: 32px;
  padding: 0 8px;
  font-size: 0.85em;
  font-weight: 500;
  border: 1px solid #d1d5db;
  border-radius: 4px;
  color: inherit;
  cursor: pointer;
  transition: background 0.2s, border-color 0.2s;
}
.mpg-pag-num:hover { background: rgba(0,0,0,0.05); border-color: #9ca3af; }
.mpg-pag-num.mpg-pag-active {
  background: var(--olo-color-primary, #6366F1);
  border-color: var(--olo-color-primary, #6366F1);
  color: #fff;
}
.mpg-pag-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 36px;
  height: 36px;
  border-radius: 50%;
  border: 1px solid #d1d5db;
  background: transparent;
  font-size: 1.2em;
  color: inherit;
  cursor: pointer;
  transition: background 0.2s, border-color 0.2s;
}
.mpg-pag-btn:hover { background: rgba(0,0,0,0.05); border-color: #9ca3af; }
.mpg-pag-info {
  font-size: 0.85em;
  color: #6b7280;
  min-width: 50px;
  text-align: center;
}
.mpg-pag-loadmore {
  display: inline-block;
  padding: 8px 24px;
  border: 1px solid #d1d5db;
  border-radius: 999px;
  font-size: 0.85em;
  font-weight: 500;
  color: #6b7280;
  cursor: pointer;
  transition: background 0.2s, border-color 0.2s;
}
.mpg-pag-loadmore:hover { background: rgba(0,0,0,0.05); border-color: #9ca3af; }

/* Service stats preview */
.mpg-service-stats {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
  margin-bottom: 3px;
  font-size: 8px;
  color: #6b7280;
}
.mpg-stat {
  display: inline-flex;
  align-items: center;
  gap: 2px;
}
.mpg-stat svg {
  flex-shrink: 0;
}
.mpg-service-club {
  font-size: 8px;
  font-weight: 600;
  color: var(--olo-color-primary, #6366F1);
  margin-bottom: 3px;
}
/* Opening badge preview */
.mpg-opening {
  position: absolute;
  top: 6px;
  right: 6px;
  color: #fff;
  font-weight: 700;
  padding: 2px 6px;
  border-radius: 3px;
  z-index: 2;
  text-transform: uppercase;
  letter-spacing: 0.3px;
}

/* Info bar */
.mpg-info {
  margin-top: 8px;
  text-align: center;
  font-size: 9px;
  color: #9ca3af;
  padding: 4px;
  background: rgba(0,0,0,0.05);
  border-radius: 3px;
}
.mpg-live-badge {
  color: #059669;
  font-weight: 600;
}
</style>
