<template>
  <div class="olo-tlsuper" :class="rootClass" :style="customVars">

    <!-- ═══ VERTICALE (alt · one) ═══ -->
    <div v-if="layout === 'alt' || layout === 'one'" class="super js" :class="superClass">
      <span class="rail"></span>
      <span class="rail-fill" :style="{ height: isScroll ? 'calc(100% - 12px)' : '0' }"></span>

      <div
        v-for="(item, i) in items"
        :key="item.id || i"
        class="it in"
        :class="[ 'cat-' + catClass(item), isScroll ? 'reached' : '', (isScroll && i === items.length - 1) ? 'active' : '' ]"
        :style="itStyle(item)"
      >
        <span class="it-node">
          <span :uk-icon="'icon: ' + (item.icon || 'star')"></span>
          <span class="pip"></span>
          <span class="lab">{{ lab(item, i) }}</span>
        </span>
        <div class="it-date">
          <span class="yr">{{ item.date }}</span>
          <span class="ph">{{ item.tag }}</span>
          <span class="st">{{ stText(i) }}</span>
        </div>
        <div class="it-card">
          <div class="it-media">
            <span class="bar"></span>
            <img v-if="item.image" :src="item.image" :alt="item.title" />
            <span v-else class="ph">{{ item.tag || item.title }}</span>
          </div>
          <div class="it-body">
            <span v-if="item.tag" class="it-tag">{{ item.tag }}</span>
            <h4 v-if="item.title">{{ item.title }}</h4>
            <p v-if="item.description">{{ item.description }}</p>
          </div>
        </div>
      </div>

      <span v-if="s.tl_thread === 'comet'" class="comet"></span>
    </div>

    <!-- ═══ ORIZZONTALE ═══ -->
    <div v-else-if="layout === 'horizontal'" class="hwrap">
      <div class="hbar">
        <span class="ht"></span>
        <div class="hnav">
          <button type="button" class="prev" @click="hScroll(-1)" :aria-label="t('Precedente')">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
          </button>
          <button type="button" class="next" @click="hScroll(1)" :aria-label="t('Successivo')">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
          </button>
        </div>
      </div>
      <div class="hscroll" ref="hscrollRef">
        <div class="htrack">
          <div
            v-for="(item, i) in items"
            :key="item.id || i"
            class="hit"
            :class="'cat-' + catClass(item)"
            :style="hitStyle(item)"
          >
            <span class="hit-node"><span :uk-icon="'icon: ' + (item.icon || 'star')"></span></span>
            <span class="hit-date">{{ item.date }}</span>
            <div class="hit-card">
              <span v-if="item.tag" class="t">{{ item.tag }}</span>
              <h4 v-if="item.title">{{ item.title }}</h4>
              <p v-if="item.description">{{ item.description }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ═══ NAVIGATORE ═══ -->
    <div v-else class="navd">
      <div class="nv-nav">
        <button type="button" class="nv-arrow nv-prev" :disabled="nvIdx === 0" @click="nvGo(nvIdx - 1)" :aria-label="t('Precedente')">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
        </button>
        <div class="nv-viewport">
          <div class="nv-track" :style="nvTrackStyle">
            <span class="nv-base"></span>
            <span class="nv-fill" :style="{ width: (nvIdx * 168 + 84) + 'px' }"></span>
            <div
              v-for="(item, i) in items"
              :key="item.id || i"
              class="nv-step"
              :class="[ i <= nvIdx ? 'done' : '', i === nvIdx ? 'sel' : '' ]"
              @click="nvGo(i)"
            >
              <span class="l">{{ item.date }}</span><span class="d"></span>
            </div>
          </div>
        </div>
        <button type="button" class="nv-arrow nv-next" :disabled="nvIdx === items.length - 1" @click="nvGo(nvIdx + 1)" :aria-label="t('Successivo')">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
        </button>
      </div>
      <div class="nv-stage">
        <div class="nv-post">
          <div class="nv-media">
            <img v-if="cur.image" :src="cur.image" :alt="cur.title" />
            <span class="nyr">{{ cur.date }}</span>
            <span class="nph">{{ t('archivio') }} · {{ cur.date }}</span>
          </div>
          <div class="nv-body">
            <div class="m"><span class="tg">{{ cur.tag }}</span><span class="dt">{{ cur.date }}</span></div>
            <h2>{{ cur.title }}</h2>
            <p>{{ cur.description }}</p>
          </div>
        </div>
      </div>
      <div class="nv-counter"><b>{{ nvIdx + 1 }}</b> / {{ items.length }}</div>
    </div>

  </div>
</template>

<script setup>
import { t } from '@/i18n';
import { ref, computed } from 'vue';
// CSS condiviso col frontend PHP (assets/css/timeline-super.css) — Vite lo bundla.
import '../../../assets/css/timeline-super.css';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  tl_layout: 'alt',
  tl_reveal: 'sides',
  tl_theme: 'paper',
  tl_card: 'bubble',
  tl_thread: 'solid2',
  tl_node: 'icon',
  tl_color: 'cat',
  tl_media: 'on',
  tl_density: 'comfy',
  tl_line: 'scroll',
  tl_transparent: false,
  h_card_width: '268',
  items: [],
};

// Migrazione vecchie chiavi → tl_* (parità col PHP).
function migrate(raw) {
  const r = { ...raw };
  if (r.tl_layout == null && r.layout != null) {
    r.tl_layout = ({ 'vertical-center': 'alt', 'vertical-left': 'one', 'vertical-right': 'one', horizontal: 'horizontal' })[r.layout] || 'alt';
  }
  if (r.tl_node == null && r.marker_type != null) {
    r.tl_node = ({ dot: 'dot', icon: 'icon', number: 'num' })[r.marker_type] || 'icon';
  }
  if (r.tl_thread == null && r.line_style != null) {
    r.tl_thread = ({ solid: 'solid2', dashed: 'dash', dotted: 'dot' })[r.line_style] || 'solid2';
  }
  if (r.tl_line == null && r.line_progress != null) {
    r.tl_line = r.line_progress ? 'scroll' : 'solid';
  }
  return r;
}

const s = computed(() => ({ ...defaults, ...migrate(props.settings || {}) }));

const items = computed(() => {
  const arr = Array.isArray(s.value.items) ? s.value.items : [];
  return arr.map((it, i) => ({
    id: it.id || ('tl-' + i),
    title: it.title || '',
    tag: it.tag || '',
    description: it.description || '',
    date: it.date || '',
    image: it.image || '',
    video: it.video || '',
    icon: it.icon || 'star',
    category: it.category || 'primary',
    icon_color: it.icon_color || '',
  }));
});

const layout = computed(() => ['alt', 'one', 'horizontal', 'navigator'].includes(s.value.tl_layout) ? s.value.tl_layout : 'alt');
const theme = computed(() => ['paper', 'night', 'neon', 'blue'].includes(s.value.tl_theme) ? s.value.tl_theme : 'paper');
const mono = computed(() => s.value.tl_color === 'mono');
const isScroll = computed(() => s.value.tl_line !== 'solid');

const rootClass = computed(() => [
  theme.value !== 'paper' ? 't-' + theme.value : '',
  mono.value ? 'mono' : '',
  s.value.tl_transparent ? 'bg-transparent' : '',
  !s.value.tl_media_bar ? 'tl-nobar' : '',
  !s.value.tl_show_tag ? 'tl-notag' : '',
  s.value.tl_card && s.value.tl_card !== 'bubble' ? 'card-' + s.value.tl_card : '',
]);

// Override fini → custom property --tl-* sul root (parità col PHP custom_style).
const customVars = computed(() => {
  const v = {};
  const col = (k, css) => { const c = s.value[k]; if (c) v[css] = c; };
  const px = (k, css) => { const n = parseInt(s.value[k]) || 0; if (n > 0) v[css] = n + 'px'; };
  col('tl_rail_color', '--tl-rail-color'); px('tl_rail_w', '--tl-rail-w');
  col('tl_fill_from', '--tl-fill-from'); col('tl_fill_to', '--tl-fill-to');
  px('tl_node_size', '--tl-node-size'); px('tl_node_border', '--tl-node-bd');
  col('tl_card_bg', '--tl-card-bg'); px('tl_card_radius', '--tl-card-radius'); px('tl_card_maxw', '--tl-card-maxw'); px('tl_card_pad', '--tl-card-pad');
  const ratio = s.value.tl_media_ratio || 'auto';
  if (ratio !== 'auto' && /^\d+\/\d+$/.test(ratio)) { v['--tl-media-ar'] = ratio; v['--tl-media-h'] = 'auto'; }
  else px('tl_media_h', '--tl-media-h');
  const fit = s.value.tl_media_fit || 'cover';
  if (['contain', 'fill', 'none'].includes(fit)) v['--tl-media-fit'] = fit;
  px('tl_media_radius', '--tl-media-radius');
  px('tl_title_size', '--tl-title-size');
  const w = parseInt(s.value.tl_title_weight) || 0; if (w > 0) v['--tl-title-weight'] = String(w);
  col('tl_title_color', '--tl-title-color');
  px('tl_text_size', '--tl-text-size'); col('tl_text_color', '--tl-text-color');
  const lh = parseFloat(s.value.tl_text_lh) || 0; if (lh > 0) v['--tl-text-lh'] = String(lh);
  const al = s.value.tl_text_align || 'left';
  if (al === 'center' || al === 'right') v['--tl-text-align'] = al;
  px('tl_yr_size', '--tl-yr-size'); col('tl_yr_color', '--tl-yr-color');
  col('tl_tag_color', '--tl-tag-color');
  col('tl_title_family', '--tl-title-family'); col('tl_text_family', '--tl-text-family'); col('tl_yr_family', '--tl-yr-family');
  return v;
});

const superClass = computed(() => {
  const c = [];
  c.push(isScroll.value ? 'line-scroll' : 'line-solid');
  c.push('ing-' + s.value.tl_reveal);
  if (s.value.tl_reveal !== 'sides') c.push('ing-anim');
  if (layout.value === 'one') c.push('one');
  if (s.value.tl_card !== 'bubble') c.push('card-' + s.value.tl_card);
  if (s.value.tl_thread !== 'solid2') c.push('thread-' + s.value.tl_thread);
  if (s.value.tl_node !== 'icon') c.push('node-' + s.value.tl_node);
  if (s.value.tl_media === 'off') c.push('no-media');
  if (s.value.tl_density === 'compact') c.push('dense');
  if (mono.value) c.push('mono');
  return c;
});

function catClass(item) {
  if (mono.value) return 'primary';
  const allow = ['primary', 'secondary', 'accent', 'success', 'warning', 'info'];
  return allow.includes(item.category) ? item.category : 'primary';
}
function itStyle(item) {
  return (!mono.value && item.icon_color) ? { '--cat': item.icon_color } : {};
}
function hitStyle(item) {
  const w = parseInt(s.value.h_card_width) || 268;
  const st = { width: w + 'px' };
  if (!mono.value && item.icon_color) st['--cat'] = item.icon_color;
  return st;
}
function lab(item, i) {
  if (s.value.tl_node === 'num') return ('0' + (i + 1)).slice(-2);
  if (s.value.tl_node === 'year') return item.date;
  return '';
}
function stText(i) {
  if (!isScroll.value) return '—';
  return i === items.value.length - 1 ? t('In corso') : t('Fatto');
}

// ── orizzontale ──
const hscrollRef = ref(null);
function hScroll(dir) {
  const el = hscrollRef.value;
  if (!el) return;
  const card = el.querySelector('.hit');
  const step = card ? card.getBoundingClientRect().width : 280;
  el.scrollBy({ left: dir * step, behavior: 'smooth' });
}

// ── navigatore ──
const nvIdx = ref(0);
const cur = computed(() => items.value[Math.min(nvIdx.value, items.value.length - 1)] || {});
function nvGo(i) {
  nvIdx.value = Math.max(0, Math.min(items.value.length - 1, i));
}
const nvTrackStyle = computed(() => {
  // centratura approssimata del passo selezionato (parità con la resa frontend)
  const STEP = 168;
  return { transform: 'translateX(' + (-Math.max(0, nvIdx.value * STEP - STEP)) + 'px)' };
});
</script>
