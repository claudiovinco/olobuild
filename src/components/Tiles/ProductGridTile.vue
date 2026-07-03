<template>
  <div class="olo-productgrid opg" :class="{ 'opg--zoom': zoom }" :style="rootStyle">
    <div v-if="showFilters" class="opg-filters" :style="filtersStyle">
      <button class="opg-filter" :style="filterChipStyle(true)">{{ s.filter_all_label || 'All' }}</button>
      <button v-for="(c, ci) in cats" :key="'f' + ci" class="opg-filter" :style="filterChipStyle(false)">{{ c }}</button>
    </div>
    <div class="opg-grid" :style="gridStyle">
      <div v-for="(it, i) in items" :key="'p' + i" class="opg-card" :style="cardStyle">
        <a class="opg-mw" :href="it.link || '#'" :style="mwStyle">
          <span class="opg-media" :style="mediaStyle(it)"></span>
          <span v-if="!it.image && !itemHasBg(it) && it.media_label" class="opg-lbl" :style="lblStyle">{{ it.media_label }}</span>
          <span v-if="it.tag" class="opg-tag" :style="tagStyle">{{ it.tag }}</span>
          <span v-if="qaShow && it.quick_add" class="opg-add" :style="addStyle">{{ it.quick_add }}</span>
        </a>
        <div class="opg-body" :style="bodyStyle">
          <div v-if="it.category" class="opg-cat" :style="catStyle">{{ it.category }}</div>
          <h3 v-if="it.title" class="opg-t" :style="titleStyle"><a :href="it.link || '#'" style="color:inherit;text-decoration:none">{{ it.title }}</a></h3>
          <p v-if="it.notes" class="opg-notes" :style="notesStyle">{{ it.notes }}</p>
          <div v-if="shadesArr(it).length" class="opg-shades" :style="shadesWrapStyle">
            <i v-for="(sc, si) in shadesArr(it)" :key="'s' + si" :style="{ ...shadeDotStyle, background: sc }"></i>
          </div>
          <div v-if="roastN(it) > 0" class="opg-roast" :style="roastWrapStyle">
            <span :style="roastLblStyle">{{ s.roast_label || 'Roast' }}</span>
            <span style="display:flex;gap:4px">
              <i v-for="ri in 5" :key="'r' + ri" :style="roastDotStyle(ri <= roastN(it))"></i>
            </span>
          </div>
          <div v-if="addOn" class="opg-cardfoot" :style="cardFootStyle">
            <div v-if="it.price" class="opg-price" :style="priceStyle">{{ it.price }}</div>
            <button type="button" class="opg-addbtn" :style="addBtnStyle">{{ s.add_label || 'Add' }}</button>
          </div>
          <div v-else-if="it.price" class="opg-price" :style="priceStyle">{{ it.price }}</div>
        </div>
      </div>
    </div>
    <div v-if="s.footer_text" class="opg-foot" style="text-align:center;margin-top:48px">
      <a :href="s.footer_url || '#'" :style="footStyle">{{ s.footer_text }}</a>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, watch } from 'vue';
import { resolveFontFamily } from '@/composables/oloTileDefaults';
import { buildBgStyle } from '@/composables/useBackgroundStyle';
import { radiusToCss } from '@/composables/useRadius';
import { borderDefault, borderHoverDefault, borderEffectDefaults } from '@/config/elements/_shared.js';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });

const defaults = {
  source: 'custom',
  woo_category: '', woo_limit: 8, woo_orderby: 'date', woo_order: 'DESC', woo_on_sale: false, woo_quick_add: 'Quick add',
  items: [
    { image: '', media_label: 'wool crêpe coat', tag: 'New', category: 'Outerwear', title: 'Crêpe Tailored Coat', price: '€1,290', link: '#', quick_add: 'Quick add' },
    { image: '', media_label: 'silk column dress', tag: '', category: 'Eveningwear', title: 'Silk Column Dress', price: '€980', link: '#', quick_add: 'Quick add' },
    { image: '', media_label: 'tuxedo jacket', tag: '', category: 'Tailoring', title: 'Le Smoking Jacket', price: '€1,150', link: '#', quick_add: 'Quick add' },
    { image: '', media_label: 'cashmere knit', tag: 'Atelier', category: 'Knitwear', title: 'Cashmere Roll-Neck', price: '€620', link: '#', quick_add: 'Quick add' },
  ],
  columns: 4, gap: 22, media_aspect: '3/4', media_bg: '', stripe_dark: false, hover_zoom: true,
  tag_bg: '', tag_color: '', quick_add_show: true, quick_add_bg: '', quick_add_color: '',
  category_color: '', title_font: 'heading', title_size: 21, title_color: '', price_color: '',
  footer_text: '', footer_url: '#', footer_color: '',
  card_bg: '', card_border: '', card_radius: 0, card_padding: 0,
  shade_size: 16, shade_border: 'rgba(255,255,255,0.3)',
  notes_color: '', notes_mono: false, roast_label: 'Roast', roast_on_color: '', roast_off_color: '',
  add_button: false, add_label: 'Add', add_bg: '', add_color: '',
  bg: { type: 'none' }, shadow: 'none',
  border: { ...borderDefault }, border_hover: { ...borderHoverDefault }, border_hover_duration: 300,
  ...borderEffectDefaults,
};

const s = computed(() => ({ ...defaults, ...props.settings }));

// ── Sorgente WooCommerce: anteprima reale nel canvas via REST ──
const isWoo = computed(() => (s.value.source || 'custom') === 'woocommerce');
const wooItems = ref(null); // null = non ancora caricato
let wooTimer = null;
function fetchWooItems() {
  const od = window.oloData || {};
  if (!od.restUrl) { wooItems.value = []; return; }
  const params = new URLSearchParams({
    category: s.value.woo_category || '',
    limit: String(s.value.woo_limit || 8),
    orderby: s.value.woo_orderby || 'date',
    order: s.value.woo_order || 'DESC',
    on_sale: s.value.woo_on_sale ? '1' : '0',
    quick_add: s.value.woo_quick_add || 'Quick add',
  });
  fetch(`${od.restUrl}productgrid-products?${params}`, { headers: { 'X-WP-Nonce': od.nonce || '' } })
    .then(r => r.json())
    .then(d => { wooItems.value = Array.isArray(d.items) ? d.items : []; })
    .catch(() => { wooItems.value = []; });
}
watch(
  [isWoo, () => s.value.woo_category, () => s.value.woo_limit, () => s.value.woo_orderby, () => s.value.woo_order, () => s.value.woo_on_sale, () => s.value.woo_quick_add],
  () => {
    if (!isWoo.value) return;
    clearTimeout(wooTimer);
    wooTimer = setTimeout(fetchWooItems, 350);
  },
  { immediate: true }
);
// Placeholder informativi finché i prodotti reali non arrivano (o se il negozio è vuoto)
const WOO_PLACEHOLDER = [1, 2, 3, 4].map(n => ({
  image: '', media_label: 'woocommerce', tag: '', category: '—', title: `Prodotto ${n}`, price: '', link: '#', quick_add: '',
}));
const items = computed(() => {
  if (isWoo.value) {
    if (wooItems.value === null) return WOO_PLACEHOLDER;          // loading
    return wooItems.value.length ? wooItems.value : WOO_PLACEHOLDER; // negozio vuoto
  }
  return Array.isArray(s.value.items) ? s.value.items : [];
});
const itemHasBg = (it) => !!(it && it.media_bg && it.media_bg.type && it.media_bg.type !== 'none');
const flist = computed(() => { const raw = String(s.value.filter_list || '').trim(); return raw ? raw.split(',').map(x => x.trim()).filter(Boolean) : []; });
const cats = computed(() => { if (flist.value.length) return flist.value; const out = []; items.value.forEach(it => { const c = (it && it.category) ? String(it.category).trim() : ''; if (c && !out.includes(c)) out.push(c); }); return out; });
const showFilters = computed(() => !!s.value.show_filters && cats.value.length > 0);

const SANS = "var(--olo-font-family, -apple-system, sans-serif)";
const cols = computed(() => Math.max(1, Math.min(5, parseInt(s.value.columns, 10) || 4)));
const dark = computed(() => !!s.value.stripe_dark);
const zoom = computed(() => !!s.value.hover_zoom);
const qaShow = computed(() => !!s.value.quick_add_show);
const asp = computed(() => String(s.value.media_aspect || '3/4').replace(/[^0-9.\/]/g, '') || '3/4');

const mbg = computed(() => s.value.media_bg || 'var(--olo-color-surface-alt, #f2f2f4)');
const stripe = computed(() => dark.value ? 'rgba(0,0,0,.06)' : 'rgba(255,255,255,.05)');
const lblcol = computed(() => dark.value ? 'rgba(0,0,0,.45)' : 'rgba(255,255,255,.4)');
const tagbg = computed(() => s.value.tag_bg || 'var(--olo-color-text, #0c0c0c)');
const tagcol = computed(() => s.value.tag_color || 'var(--olo-color-primary, #e1474f)');
const qabg = computed(() => s.value.quick_add_bg || 'rgba(255,255,255,0.95)');
const qacol = computed(() => s.value.quick_add_color || '#111111');
const catcol = computed(() => s.value.category_color || 'var(--olo-color-text-muted, #7c776e)');
const tcol = computed(() => s.value.title_color || 'var(--olo-color-text, #111827)');
const pcol = computed(() => s.value.price_color || 'var(--olo-color-primary, #e1474f)');
const fTxt = computed(() => s.value.filter_text_color || 'var(--olo-color-text, #111827)');
const fBd = computed(() => s.value.filter_border_color || 'var(--olo-color-border, rgba(0,0,0,.14))');
const fAbg = computed(() => s.value.filter_active_bg || pcol.value);
const fAcol = computed(() => s.value.filter_active_color || '#ffffff');
const filtersStyle = { display: 'flex', gap: '10px', justifyContent: 'center', flexWrap: 'wrap', marginBottom: '48px' };
function filterChipStyle(active) {
  return { fontWeight: 500, fontSize: '11px', letterSpacing: '.16em', textTransform: 'uppercase', padding: '10px 20px', cursor: 'pointer', transition: 'all .2s', border: '1px solid ' + (active ? fAbg.value : fBd.value), background: active ? fAbg.value : 'transparent', color: active ? fAcol.value : fTxt.value };
}
const fcol = computed(() => s.value.footer_color || 'var(--olo-color-text, #111827)');
const tsize = computed(() => Math.max(12, parseInt(s.value.title_size, 10) || 21));
const HEAD = "var(--olo-font-family-heading, Georgia, serif)";
// Stack storici per i valori legacy: 'heading' e 'serif' → heading del tema, 'sans' → sans.
const FONT_LEGACY = { heading: HEAD, serif: HEAD, sans: SANS };
const titleFont = computed(() => resolveFontFamily(s.value.title_font, FONT_LEGACY) || HEAD);

// Card (sfondo opzionale) + shades + add button
// card_radius dual-format: numero legacy (range) E oggetto {tl,tr,br,bl} (border-radius).
// Parity con build_border_radius_css PHP: '' se zero/vuoto.
const cardRadiusCss = computed(() => {
  const css = radiusToCss(s.value.card_radius, { fallback: '' });
  return css && css.split(' ').some((p) => (parseInt(p, 10) || 0) > 0) ? css : '';
});
const hasCard = computed(() => !!(s.value.card_bg || s.value.card_border || cardRadiusCss.value || (parseInt(s.value.card_padding, 10) || 0) > 0));
const cardStyle = computed(() => {
  if (!hasCard.value) return {};
  const o = { overflow: 'hidden' };
  if (s.value.card_bg) o.background = s.value.card_bg;
  if (s.value.card_border) o.border = '1px solid ' + s.value.card_border;
  if (cardRadiusCss.value) o.borderRadius = cardRadiusCss.value;
  return o;
});
const mwStyle = computed(() => ({ marginBottom: hasCard.value ? '0' : '16px' }));
const bodyStyle = computed(() => {
  const p = parseInt(s.value.card_padding, 10) || 0;
  return { display: 'flex', flexDirection: 'column', flex: 1, ...(hasCard.value && p ? { padding: p + 'px' } : {}) };
});
const shSize = computed(() => Math.max(8, parseInt(s.value.shade_size, 10) || 16));
const shBd = computed(() => s.value.shade_border || 'rgba(255,255,255,0.3)');
function shadesArr(it) {
  const raw = (it && it.shades ? String(it.shades) : '').trim();
  if (!raw) return [];
  return raw.split(/[,\s]+/).map(x => x.trim()).filter(Boolean);
}
const shadesWrapStyle = { display: 'flex', gap: '6px', margin: '8px 0 14px' };
const shadeDotStyle = computed(() => ({ width: shSize.value + 'px', height: shSize.value + 'px', borderRadius: '50%', boxShadow: 'inset 0 0 0 1.5px ' + shBd.value, flex: 'none' }));
const addOn = computed(() => !!s.value.add_button);
const addBtnStyle = computed(() => ({ fontFamily: SANS, fontWeight: 700, fontSize: '12px', color: s.value.add_color || 'var(--olo-color-text, #111)', background: s.value.add_bg || 'var(--olo-color-text-emphasis, #f6e9ec)', border: 0, borderRadius: '999px', padding: '9px 15px', cursor: 'pointer' }));
const cardFootStyle = { display: 'flex', alignItems: 'center', justifyContent: 'space-between', gap: '10px', marginTop: 'auto' };
const MONO = "var(--olo-font-family-mono, ui-monospace, 'Spline Sans Mono', Menlo, monospace)";
const notesCol = computed(() => s.value.notes_color || 'var(--olo-color-text-muted, #7c776e)');
const notesStyle = computed(() => ({ fontSize: '13.5px', color: notesCol.value, margin: '2px 0 14px', lineHeight: 1.5, ...(s.value.notes_mono ? { fontFamily: MONO } : {}) }));
const roastOn = computed(() => s.value.roast_on_color || 'var(--olo-color-primary, #e1474f)');
const roastOff = computed(() => s.value.roast_off_color || 'rgba(255,255,255,0.15)');
function roastN(it) { return Math.max(0, Math.min(5, parseInt(it && it.roast, 10) || 0)); }
const roastWrapStyle = { display: 'flex', alignItems: 'center', gap: '8px', margin: '0 0 18px' };
const roastLblStyle = computed(() => ({ fontFamily: MONO, fontSize: '10.5px', letterSpacing: '.05em', textTransform: 'uppercase', color: notesCol.value }));
function roastDotStyle(on) { return { width: '8px', height: '8px', borderRadius: '50%', background: on ? roastOn.value : roastOff.value, display: 'inline-block', flex: 'none' }; }

// KIT (parità con PHP)
const kitBgStyle = computed(() => buildBgStyle(s.value.bg));
function shadowDecl(st) {
  const preset = st.shadow || 'none';
  if (preset === 'none' || preset === '') return '';
  if (preset === 'custom') {
    const h = parseInt(st.shadow_h, 10) || 0;
    const v = Number.isNaN(parseInt(st.shadow_v, 10)) ? 4 : parseInt(st.shadow_v, 10);
    const blur = Math.max(0, Number.isNaN(parseInt(st.shadow_blur, 10)) ? 10 : parseInt(st.shadow_blur, 10));
    const spread = parseInt(st.shadow_spread, 10) || 0;
    const color = st.shadow_color || 'rgba(0,0,0,0.15)';
    const inset = st.shadow_inset ? 'inset ' : '';
    return `${inset}${h}px ${v}px ${blur}px ${spread}px ${color}`;
  }
  const map = {
    sm: '0 1px 2px rgba(16,24,40,.06), 0 6px 16px -10px rgba(16,24,40,.18)',
    md: '0 2px 4px rgba(16,24,40,.06), 0 14px 28px -12px rgba(22,38,61,.28)',
    lg: '0 8px 24px -6px rgba(16,24,40,.18), 0 18px 40px -12px rgba(22,38,61,.30)',
    xl: '0 12px 32px -8px rgba(16,24,40,.20), 0 28px 56px -14px rgba(22,38,61,.34)',
  };
  return map[preset] || '';
}
const kitBorderStyle = computed(() => {
  const b = s.value.border || {};
  const style = b.style || 'solid';
  const color = b.color || 'currentColor';
  const out = {};
  const t = parseInt(b.top, 10) || 0, r = parseInt(b.right, 10) || 0, bo = parseInt(b.bottom, 10) || 0, l = parseInt(b.left, 10) || 0;
  if (t) out.borderTop = `${t}px ${style} ${color}`;
  if (r) out.borderRight = `${r}px ${style} ${color}`;
  if (bo) out.borderBottom = `${bo}px ${style} ${color}`;
  if (l) out.borderLeft = `${l}px ${style} ${color}`;
  return out;
});
const rootStyle = computed(() => {
  const kit = { ...kitBgStyle.value, ...kitBorderStyle.value };
  const sh = shadowDecl(s.value);
  if (sh) kit.boxShadow = sh;
  if (Object.keys(kit).length) kit.position = 'relative';
  return { fontFamily: SANS, ...kit };
});
const gridStyle = computed(() => ({ display: 'grid', gridTemplateColumns: `repeat(${cols.value}, 1fr)`, gap: (parseInt(s.value.gap, 10) || 22) + 'px' }));

function mediaStyle(it) {
  const base = { display: 'block', aspectRatio: asp.value, transition: 'transform .7s cubic-bezier(.2,.7,.3,1)' };
  const mb = it && it.media_bg;
  if (mb && mb.type && mb.type !== 'none') {
    return { ...base, backgroundSize: 'cover', backgroundPosition: (s.value.object_position || 'center center'), ...buildBgStyle(mb) };
  }
  const img = it && it.image ? it.image : '';
  return {
    ...base, background: mbg.value, backgroundSize: 'cover', backgroundPosition: (s.value.object_position || 'center center'),
    backgroundImage: img ? `url(${img})` : `repeating-linear-gradient(135deg, ${stripe.value} 0 16px, transparent 16px 32px)`,
  };
}
const lblStyle = computed(() => ({ position: 'absolute', left: '14px', bottom: '12px', fontSize: '10px', letterSpacing: '.14em', textTransform: 'uppercase', fontWeight: 500, color: lblcol.value }));
const tagStyle = computed(() => ({ position: 'absolute', top: '14px', left: '14px', background: tagbg.value, color: tagcol.value, fontWeight: 500, fontSize: '9.5px', letterSpacing: '.16em', textTransform: 'uppercase', padding: '5px 11px' }));
const addStyle = computed(() => ({ position: 'absolute', left: '14px', right: '14px', bottom: '14px', background: qabg.value, color: qacol.value, fontWeight: 500, fontSize: '11px', letterSpacing: '.18em', textTransform: 'uppercase', textAlign: 'center', padding: '12px', opacity: 0, transform: 'translateY(8px)', transition: 'all .35s' }));
const catStyle = computed(() => ({ fontWeight: 500, fontSize: '10px', letterSpacing: '.16em', textTransform: 'uppercase', color: catcol.value }));
const titleStyle = computed(() => ({ fontFamily: titleFont.value, fontWeight: 400, fontSize: tsize.value + 'px', margin: '7px 0 6px', color: tcol.value, lineHeight: 1.15 }));
const priceStyle = computed(() => ({ fontSize: '14px', letterSpacing: '.06em', color: pcol.value }));
const footStyle = computed(() => ({ display: 'inline-block', fontWeight: 500, fontSize: '12px', letterSpacing: '.18em', textTransform: 'uppercase', color: fcol.value, borderBottom: `1px solid ${pcol.value}`, paddingBottom: '3px', textDecoration: 'none' }));
</script>

<style scoped>
.opg-mw { position: relative; overflow: hidden; margin-bottom: 16px; display: block; }
.opg-card { display: flex; flex-direction: column; }
.opg--zoom .opg-card:hover .opg-media { transform: scale(1.05); }
.opg-card:hover .opg-add { opacity: 1 !important; transform: none !important; }
.opg-mw:focus-visible { outline: 2px solid currentColor; outline-offset: 3px; }
</style>
