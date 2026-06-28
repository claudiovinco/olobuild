<template>
  <div class="olo-sh" :style="rootVars" @keydown.esc="closeAll">
    <header class="olo-sh-hdr" :class="{ 'olo-sh-hdr--full': !isPill }">
      <div class="olo-sh-bar" ref="barEl" :style="barStyle">
        <!-- brand -->
        <a class="olo-sh-brand" href="#" @click.prevent>
          <img v-if="s.brand_logo" class="olo-sh-blogo" :src="s.brand_logo" alt="" :style="{ height: (s.brand_height || 25) + 'px' }" />
        </a>

        <!-- primary nav -->
        <nav class="olo-sh-nav">
          <template v-for="(it, i) in navItems" :key="i">
            <div v-if="isMega(it, i)" class="olo-sh-item" data-mega
                 @mouseenter="onMegaEnter" @mouseleave="onMegaLeave">
              <button class="olo-sh-link" :aria-expanded="String(openPanel==='mega')" @click="toggle('mega')">
                {{ it.label }}
                <svg class="olo-sh-chev" viewBox="0 0 12 12" fill="none"><path d="M2.5 4.5 6 8l3.5-3.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
              </button>
              <div class="olo-sh-panel olo-sh-mega" :class="{ 'olo-sh-open': openPanel==='mega' }"
                   @mouseenter="clearHover" @mouseleave="onMegaLeave">
                <div class="olo-sh-mega-grid" :style="megaGridStyle">
                  <div v-if="s.rail_show" class="olo-sh-rail" :style="railStyle">
                    <span v-if="s.rail_badge" class="olo-sh-badge">{{ s.rail_badge }}</span>
                    <h3 v-if="s.rail_title">{{ s.rail_title }}</h3>
                    <p v-if="s.rail_text">{{ s.rail_text }}</p>
                    <div class="olo-sh-rail-cta">
                      <a v-if="s.rail_cta1_label" class="olo-sh-solid" href="#" @click.prevent>{{ s.rail_cta1_label }}</a>
                      <a v-if="s.rail_cta2_label" class="olo-sh-link2" href="#" @click.prevent>{{ s.rail_cta2_label }} <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M3 7h8M7.5 3.5 11 7l-3.5 3.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg></a>
                    </div>
                  </div>

                  <div v-for="(col, ci) in columns" :key="ci" class="olo-sh-col">
                    <h4 v-if="col.title">{{ col.title }}</h4>
                    <a v-for="(p, pi) in col.items" :key="pi" class="olo-sh-prod" href="#" @click.prevent>
                      <img v-if="p.logo" class="olo-sh-plogo" :src="p.logo" :alt="p.name || ''" />
                      <span class="olo-sh-pd">
                        <span v-if="p.name" class="olo-sh-pname">{{ p.name }}<span v-if="p.soon" class="olo-sh-soon">In arrivo</span></span>
                        {{ p.desc }}
                      </span>
                    </a>
                  </div>

                  <div v-if="s.mega_footer_show" class="olo-sh-mega-foot">
                    <div class="olo-sh-ff">
                      <span v-if="footerLogos.length" class="olo-sh-duo">
                        <img v-for="(fl, fi) in footerLogos" :key="fi" :src="fl" alt="" />
                      </span>
                      <div>
                        <b v-if="s.mega_footer_title">{{ s.mega_footer_title }}</b>
                        <p v-if="s.mega_footer_text">{{ s.mega_footer_text }}</p>
                      </div>
                    </div>
                    <a v-if="s.mega_footer_cta_label" class="olo-sh-foot-cta" href="#" @click.prevent>{{ s.mega_footer_cta_label }} <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M3 7h8M7.5 3.5 11 7l-3.5 3.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg></a>
                  </div>
                </div>
              </div>
            </div>
            <div v-else class="olo-sh-item"><a class="olo-sh-link" href="#" @click.prevent>{{ it.label }}</a></div>
          </template>
        </nav>

        <template v-if="s.featured_show">
          <div class="olo-sh-divider"></div>
          <a class="olo-sh-featured" href="#" @click.prevent>
            <span class="olo-sh-fic" v-html="featuredIcon"></span>
            {{ s.featured_label }}
          </a>
        </template>

        <!-- right cluster -->
        <div class="olo-sh-right">
          <div v-if="s.search_show" class="olo-sh-item">
            <button class="olo-sh-iconbtn" :aria-expanded="String(openPanel==='search')" aria-label="Cerca" @click="toggle('search')">
              <svg width="19" height="19" viewBox="0 0 20 20" fill="none"><circle cx="9" cy="9" r="6" stroke="currentColor" stroke-width="1.7"/><path d="m14 14 3.5 3.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
            </button>
            <div class="olo-sh-panel olo-sh-search-panel" :class="{ 'olo-sh-open': openPanel==='search' }">
              <div class="olo-sh-search-field">
                <svg width="18" height="18" viewBox="0 0 20 20" fill="none"><circle cx="9" cy="9" r="6" stroke="#8B91A1" stroke-width="1.7"/><path d="m14 14 3.5 3.5" stroke="#8B91A1" stroke-width="1.7" stroke-linecap="round"/></svg>
                <input type="text" :placeholder="s.search_placeholder" />
                <kbd>Esc</kbd>
              </div>
              <div v-if="searchShortcuts.length" class="olo-sh-search-quick">
                <div class="olo-sh-lh">Scorciatoie</div>
                <div class="olo-sh-qrow">
                  <a v-for="(sc, si) in searchShortcuts" :key="si" class="olo-sh-qchip" href="#" @click.prevent>{{ sc.label }}</a>
                </div>
              </div>
            </div>
          </div>

          <div v-if="s.lang_show" class="olo-sh-item">
            <button class="olo-sh-langbtn" :aria-expanded="String(openPanel==='lang')" @click="toggle('lang')">
              <svg v-if="s.lang_globe" width="17" height="17" viewBox="0 0 20 20" fill="none"><circle cx="10" cy="10" r="7.2" stroke="currentColor" stroke-width="1.5"/><path d="M3 10h14M10 3c2 2 2 12 0 14M10 3c-2 2-2 12 0 14" stroke="currentColor" stroke-width="1.3"/></svg>
              <span>{{ currentLabel }}</span>
              <svg class="olo-sh-chev" viewBox="0 0 12 12" fill="none"><path d="M2.5 4.5 6 8l3.5-3.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
            <div class="olo-sh-panel olo-sh-lang-menu" :class="{ 'olo-sh-open': openPanel==='lang' }">
              <div class="olo-sh-lh">Lingua</div>
              <a v-for="(lg, li) in languages" :key="li" class="olo-sh-lang-opt" href="#"
                 :aria-current="String((lg.code||'').toLowerCase() === current)"
                 @click.prevent="current = (lg.code||'').toLowerCase()">
                <span class="olo-sh-code">{{ (lg.code || '').toUpperCase() }}</span>{{ lg.label }}
                <svg class="olo-sh-ck" viewBox="0 0 16 16" fill="none"><path d="m3 8.5 3 3 7-7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
              </a>
            </div>
          </div>

          <a v-if="s.cta_show" class="olo-sh-cta" :class="'olo-sh-cta--' + (s.cta_style || 'navy')" href="#" @click.prevent>{{ s.cta_label }}</a>
        </div>
      </div>
    </header>
  </div>
</template>

<script setup>
import { ref, computed, onBeforeUnmount } from 'vue';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const s = computed(() => props.settings || {});

const barEl = ref(null);
const openPanel = ref(null);
const current = ref((props.settings?.lang_current || 'it').toLowerCase());
let hoverTimer = null;

const isPill = computed(() => (s.value.bar_style || 'pill') === 'pill');

const navItems = computed(() => Array.isArray(s.value.nav_items) ? s.value.nav_items : []);
const firstMegaIdx = computed(() => navItems.value.findIndex(it => (it?.type || 'link') === 'mega'));
function isMega(it, i) { return (it?.type || 'link') === 'mega' && i === firstMegaIdx.value; }

const columns = computed(() => {
  const groups = {}; const order = [];
  (Array.isArray(s.value.mega_products) ? s.value.mega_products : []).forEach(p => {
    const g = p.group || '';
    if (!groups[g]) { groups[g] = []; order.push(g); }
    groups[g].push(p);
  });
  return order.map(g => ({ title: g, items: groups[g] }));
});

const footerLogos = computed(() =>
  (Array.isArray(s.value.mega_footer_logos) ? s.value.mega_footer_logos : [])
    .map(x => x && x.logo).filter(Boolean)
);
const searchShortcuts = computed(() => Array.isArray(s.value.search_shortcuts) ? s.value.search_shortcuts : []);
const languages = computed(() => Array.isArray(s.value.languages) ? s.value.languages : []);
const currentLabel = computed(() => current.value.charAt(0).toUpperCase() + current.value.slice(1));

const megaCols = computed(() => Math.max(1, Math.min(3, parseInt(s.value.mega_columns, 10) || 2)));
const megaGridStyle = computed(() => ({
  gridTemplateColumns: (s.value.rail_show ? '.92fr ' : '') + `repeat(${megaCols.value}, 1fr)`,
}));
const railStyle = computed(() => s.value.rail_bg ? { background: s.value.rail_bg } : {});

const featuredIcon = '<svg width="15" height="15" viewBox="0 0 16 16" fill="none"><path d="M9 1 2 9h5l-1 6 7-8H8l1-6Z" fill="currentColor"/></svg>';

const SHADOWS = {
  none: 'none',
  sm: '0 2px 6px -2px rgba(10,20,40,.06)',
  md: '0 8px 30px -12px rgba(10,20,40,.18)',
  lg: '0 28px 70px -22px rgba(27,42,78,.40)',
};

const rootVars = computed(() => ({
  '--sh-ink': '#1F2330',
  '--sh-ink-2': s.value.bar_text || '#5A6076',
  '--sh-ink-3': '#8B91A1',
  '--sh-line': '#E2E5EC',
  '--sh-line-2': '#F0F2F6',
  '--sh-paper': '#FAFAF7',
  '--sh-paper-2': s.value.bar_bg || '#FFFFFF',
  '--sh-navy': '#1B2A4E',
  '--sh-navy-2': '#0F1A33',
  '--sh-royal': '#1E88E5',
  '--sh-royal-ink': '#0D4A85',
  '--sh-text-h': s.value.bar_text_hover || '#1F2330',
  '--sh-shadow': SHADOWS[s.value.bar_shadow] || SHADOWS.md,
  '--sh-shadow-lg': SHADOWS.lg,
}));

const barStyle = computed(() => ({
  width: isPill.value ? `min(${s.value.bar_max_width || 1200}px, 100%)` : '100%',
  borderRadius: (isPill.value ? (s.value.bar_radius ?? 100) : 0) + 'px',
  backdropFilter: s.value.bar_blur ? 'saturate(140%) blur(8px)' : 'none',
}));

function clearHover() { clearTimeout(hoverTimer); }
function onMegaEnter() {
  if ((s.value.open_mega_on || 'hover') !== 'hover') return;
  clearTimeout(hoverTimer);
  openPanel.value = 'mega';
}
function onMegaLeave() {
  if ((s.value.open_mega_on || 'hover') !== 'hover') return;
  hoverTimer = setTimeout(() => { if (openPanel.value === 'mega') openPanel.value = null; }, 160);
}
function toggle(name) { openPanel.value = openPanel.value === name ? null : name; }
function closeAll() { openPanel.value = null; }

onBeforeUnmount(() => clearTimeout(hoverTimer));
</script>

<style scoped>
.olo-sh {
  font-family: "Manrope", system-ui, -apple-system, sans-serif;
  color: var(--sh-ink);
  -webkit-font-smoothing: antialiased;
  position: relative;
  width: 100%;
}
.olo-sh * { box-sizing: border-box; }
.olo-sh a { color: inherit; text-decoration: none; }
.olo-sh button { font-family: inherit; cursor: pointer; }

.olo-sh-hdr { display: flex; justify-content: center; padding: 0 22px; }
.olo-sh-hdr--full { padding: 0; }

.olo-sh-bar {
  background: var(--sh-paper-2);
  box-shadow: var(--sh-shadow);
  display: flex; align-items: center; gap: 8px;
  padding: 11px 14px 11px 24px; position: relative;
}

.olo-sh-brand { display: flex; align-items: center; gap: 9px; padding-right: 8px; flex-shrink: 0; }
.olo-sh-blogo { width: auto; display: block; }

.olo-sh-nav { display: flex; align-items: center; gap: 2px; margin-left: 14px; }
.olo-sh-item { position: relative; }
.olo-sh-item[data-mega] { position: static; }
.olo-sh-link { display: inline-flex; align-items: center; gap: 6px; padding: 10px 14px; border-radius: 100px; font-size: 14.5px; font-weight: 600; color: var(--sh-ink-2); background: transparent; border: none; transition: background .14s, color .14s; white-space: nowrap; }
.olo-sh-link:hover, .olo-sh-link[aria-expanded="true"] { color: var(--sh-text-h); background: var(--sh-line-2); }
.olo-sh-chev { width: 10px; height: 10px; transition: transform .2s; }
.olo-sh-link[aria-expanded="true"] .olo-sh-chev { transform: rotate(180deg); }

.olo-sh-divider { width: 1px; height: 26px; background: var(--sh-line); margin: 0 8px; flex-shrink: 0; }

.olo-sh-featured { display: inline-flex; align-items: center; gap: 9px; padding: 9px 14px; border-radius: 100px; font-size: 14.5px; font-weight: 700; color: var(--sh-ink); transition: background .14s; }
.olo-sh-featured:hover { background: var(--sh-line-2); }
.olo-sh-fic { width: 30px; height: 30px; border-radius: 9px; display: grid; place-items: center; color: #fff; background: linear-gradient(135deg, var(--sh-royal), var(--sh-navy)); box-shadow: 0 6px 16px -6px rgba(30,136,229,.6); }
.olo-sh-fic :deep(svg) { width: 15px; height: 15px; }

.olo-sh-right { margin-left: auto; display: flex; align-items: center; gap: 6px; padding-left: 8px; }
.olo-sh-iconbtn { width: 40px; height: 40px; border-radius: 100px; border: none; background: transparent; color: var(--sh-ink-2); display: grid; place-items: center; transition: background .14s, color .14s; }
.olo-sh-iconbtn:hover { background: var(--sh-line-2); color: var(--sh-ink); }
.olo-sh-langbtn { display: inline-flex; align-items: center; gap: 7px; padding: 9px 12px; border-radius: 100px; border: none; background: transparent; font-size: 14px; font-weight: 700; color: var(--sh-ink-2); transition: background .14s, color .14s; }
.olo-sh-langbtn:hover, .olo-sh-langbtn[aria-expanded="true"] { background: var(--sh-line-2); color: var(--sh-ink); }
.olo-sh-langbtn .olo-sh-chev { width: 9px; height: 9px; }
.olo-sh-cta { display: inline-flex; align-items: center; gap: 8px; padding: 12px 22px; border-radius: 100px; font-weight: 700; font-size: 14.5px; border: none; transition: transform .14s, background .14s, box-shadow .14s; white-space: nowrap; }
.olo-sh-cta--navy { background: var(--sh-navy); color: #fff; box-shadow: var(--sh-shadow); }
.olo-sh-cta--navy:hover { background: var(--sh-navy-2); transform: translateY(-1px); box-shadow: var(--sh-shadow-lg); }
.olo-sh-cta--royal { background: var(--sh-royal); color: #fff; box-shadow: var(--sh-shadow); }
.olo-sh-cta--royal:hover { background: var(--sh-royal-ink); transform: translateY(-1px); }
.olo-sh-cta--outline { background: transparent; color: var(--sh-navy); border: 1.5px solid var(--sh-navy); }
.olo-sh-cta--outline:hover { background: var(--sh-navy); color: #fff; }

/* panel */
.olo-sh-panel { position: absolute; top: calc(100% + 14px); background: var(--sh-paper-2); border: 1px solid var(--sh-line); border-radius: 24px; box-shadow: var(--sh-shadow-lg); opacity: 0; visibility: hidden; transform: translateY(8px) scale(.99); transform-origin: top center; transition: opacity .18s ease, transform .18s ease, visibility .18s; z-index: 5; }
.olo-sh-panel.olo-sh-open { opacity: 1; visibility: visible; transform: translateY(0) scale(1); }

.olo-sh-mega { left: 50%; transform: translateX(-50%) translateY(8px) scale(.99); width: min(960px, calc(100% - 44px)); max-width: calc(100vw - 44px); padding: 0; overflow: hidden; }
.olo-sh-mega.olo-sh-open { transform: translateX(-50%) translateY(0) scale(1); }
.olo-sh-mega-grid { display: grid; gap: 0; }

.olo-sh-rail { padding: 30px 28px; background: linear-gradient(180deg, #f6f8ff, #eef3ff); border-right: 1px solid var(--sh-line); display: flex; flex-direction: column; }
.olo-sh-badge { align-self: flex-start; display: inline-flex; align-items: center; gap: 6px; padding: 5px 11px; border-radius: 100px; background: #fff; border: 1px solid var(--sh-line); font-size: 10px; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; color: var(--sh-royal-ink); }
.olo-sh-rail h3 { font-size: 23px; font-weight: 800; letter-spacing: -.02em; margin: 16px 0 0; }
.olo-sh-rail p { font-size: 13.5px; color: var(--sh-ink-2); margin: 10px 0 0; line-height: 1.5; font-weight: 500; }
.olo-sh-rail-cta { margin-top: auto; padding-top: 22px; display: flex; flex-direction: column; gap: 10px; }
.olo-sh-solid { display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 13px 18px; border-radius: 100px; background: var(--sh-navy); color: #fff; font-weight: 700; font-size: 14px; }
.olo-sh-link2 { font-size: 13px; font-weight: 700; color: var(--sh-royal-ink); display: inline-flex; align-items: center; gap: 6px; }

.olo-sh-col { padding: 28px 26px 22px; }
.olo-sh-col + .olo-sh-col { border-left: 1px solid var(--sh-line-2); }
.olo-sh-col h4 { font-size: 11px; font-weight: 700; letter-spacing: .13em; text-transform: uppercase; color: var(--sh-ink-3); margin: 0 0 8px; padding-left: 12px; }
.olo-sh-prod { display: flex; align-items: center; gap: 13px; padding: 11px 12px; border-radius: 14px; transition: background .12s; }
.olo-sh-plogo { width: 46px; height: 46px; object-fit: contain; flex-shrink: 0; }
.olo-sh-prod:hover { background: var(--sh-line-2); }
.olo-sh-pd { font-size: 13.5px; color: var(--sh-ink-2); line-height: 1.4; font-weight: 600; }
.olo-sh-pname { font-size: 14.5px; font-weight: 700; letter-spacing: -.01em; display: block; color: var(--sh-ink); }
.olo-sh-soon { display: inline-flex; align-items: center; padding: 2px 7px; margin-left: 6px; border-radius: 100px; background: #fff; border: 1px solid var(--sh-line); font-size: 9px; font-weight: 800; letter-spacing: .06em; text-transform: uppercase; color: var(--sh-ink-3); }

.olo-sh-mega-foot { grid-column: 1 / -1; border-top: 1px solid var(--sh-line); background: var(--sh-paper); display: flex; align-items: center; gap: 16px; padding: 18px 28px; }
.olo-sh-ff { display: flex; align-items: center; gap: 13px; }
.olo-sh-duo { display: flex; gap: 8px; align-items: center; }
.olo-sh-duo img { height: 34px; width: auto; object-fit: contain; }
.olo-sh-ff b { font-size: 14px; font-weight: 800; }
.olo-sh-ff p { margin: 2px 0 0; font-size: 12.5px; color: var(--sh-ink-2); font-weight: 500; }
.olo-sh-foot-cta { margin-left: auto; font-size: 13px; font-weight: 700; color: var(--sh-royal-ink); display: inline-flex; align-items: center; gap: 6px; }

.olo-sh-lang-menu { right: 0; width: 210px; padding: 8px; }
.olo-sh-lh { padding: 8px 12px 6px; font-size: 10px; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; color: var(--sh-ink-3); }
.olo-sh-lang-opt { display: flex; align-items: center; gap: 11px; width: 100%; text-align: left; padding: 10px 12px; border-radius: 11px; border: none; background: transparent; font-size: 14px; font-weight: 600; color: var(--sh-ink); transition: background .12s; }
.olo-sh-lang-opt:hover { background: var(--sh-line-2); }
.olo-sh-code { font-family: "JetBrains Mono", monospace; font-size: 11px; font-weight: 600; color: var(--sh-ink-3); width: 22px; }
.olo-sh-ck { margin-left: auto; color: var(--sh-royal); opacity: 0; width: 15px; }
.olo-sh-lang-opt[aria-current="true"] { background: #eef3ff; }
.olo-sh-lang-opt[aria-current="true"] .olo-sh-ck { opacity: 1; }
.olo-sh-lang-opt[aria-current="true"] .olo-sh-code { color: var(--sh-royal-ink); }

.olo-sh-search-panel { right: 0; width: min(380px, calc(100vw - 44px)); padding: 16px; }
.olo-sh-search-field { display: flex; align-items: center; gap: 10px; padding: 0 14px; border: 1.5px solid var(--sh-line); border-radius: 13px; height: 48px; }
.olo-sh-search-field:focus-within { border-color: var(--sh-royal); }
.olo-sh-search-field input { flex: 1; border: none; outline: none; font-family: inherit; font-size: 15px; color: var(--sh-ink); background: transparent; }
.olo-sh-search-field kbd { font-family: "JetBrains Mono", monospace; font-size: 10px; color: var(--sh-ink-3); border: 1px solid var(--sh-line); border-radius: 6px; padding: 3px 6px; }
.olo-sh-search-quick { margin-top: 14px; }
.olo-sh-search-quick .olo-sh-lh { padding: 0 4px 8px; }
.olo-sh-qrow { display: flex; flex-wrap: wrap; gap: 7px; padding: 0 4px; }
.olo-sh-qchip { padding: 7px 13px; border-radius: 100px; background: var(--sh-line-2); font-size: 13px; font-weight: 600; color: var(--sh-ink-2); transition: background .12s, color .12s; }
.olo-sh-qchip:hover { background: var(--sh-navy); color: #fff; }
</style>
