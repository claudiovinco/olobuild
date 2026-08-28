<template>
  <div class="cfg-root" data-variant="console">
    <!-- ═══ TOPBAR ═══ -->
    <header class="cfg-topbar">
      <div class="brand">
        <a :href="dashboardUrl" class="brand-link" :title="t('Vai alla dashboard')">
          <img :src="logoUrl" alt="Olobuild" class="brand-logo" />
        </a>
        <span class="brand-version">v{{ version }}</span>
      </div>
      <nav class="crumb" aria-label="breadcrumb">
        <a :href="dashboardUrl">{{ t('Dashboard') }}</a>
        <span class="sep">/</span>
        <a href="#" @click.prevent="">{{ t('Configurazione') }}</a>
        <span class="sep">/</span>
        <b>{{ activeTabLabel }}</b>
      </nav>
      <div class="spacer"></div>
      <div class="top-search" role="search">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
        <input
          ref="topSearchEl"
          type="search"
          :value="filterQuery"
          @input="onSearchInput"
          @focus="searchArmed = true"
          :placeholder="t('Cerca un\'impostazione…')"
          :aria-label="t('Cerca impostazione')"
          autocomplete="off"
          autocorrect="off"
          autocapitalize="off"
          spellcheck="false"
          name="olo-cfg-topbar-search"
          data-1p-ignore
          data-lpignore="true"
        />
        <span class="kbd">⌘ K</span>
      </div>
      <div class="top-actions">
        <a class="doc-link" :href="docsUrl" target="_blank" rel="noopener">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 4h6a4 4 0 0 1 4 4v12a3 3 0 0 0-3-3H2zM22 4h-6a4 4 0 0 0-4 4v12a3 3 0 0 1 3-3h7z"/></svg>
          {{ t('Documentazione') }}
        </a>
        <a class="doc-link" :href="siteUrl" target="_blank" rel="noopener">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg>
          {{ t('Anteprima sito') }}
        </a>
      </div>
    </header>

    <!-- ═══ SIDEBAR ═══ -->
    <aside class="cfg-sidebar">
      <div class="cfg-side-search">
        <div class="cfg-side-search-input">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
          <input
            type="search"
            :value="filterQuery"
            @input="onSearchInput"
            @focus="searchArmed = true"
            :placeholder="t('Filtra impostazioni…')"
            :aria-label="t('Filtra impostazioni')"
            autocomplete="off"
            autocorrect="off"
            autocapitalize="off"
            spellcheck="false"
            name="olo-cfg-sidebar-filter"
            data-1p-ignore
            data-lpignore="true"
          />
        </div>
      </div>
      <div class="cfg-sidegroups">
        <!-- Usate di recente (nascosto durante la ricerca) -->
        <div v-if="recentItems.length && !searchActive" class="cfg-group cfg-group-recent">
          <div class="cfg-group-head cfg-recent-head">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
            {{ t('Usate di recente') }}
          </div>
          <div
            v-for="r in recentItems"
            :key="'recent-' + r.item.id"
            class="cfg-side-item"
            @click="onPick(r.item)"
          >
            <span class="ic" v-html="iconSvg(r.item.icon)"></span>
            <span>{{ t(r.item.label) }}</span>
            <span class="recent-when">{{ r.when }}</span>
          </div>
        </div>

        <div v-for="group in filteredGroups" :key="group.id" class="cfg-group">
          <div class="cfg-group-head">
            {{ t(group.title) }}
            <span class="count">{{ group.items.length }}</span>
          </div>
          <div
            v-for="item in group.items"
            :key="item.id"
            class="cfg-side-item"
            :class="{ 'is-active': activeTab === item.id, 'is-soon': item.soon }"
            :title="item.soon ? t('In arrivo nella prossima release') : ''"
            @click="onPick(item)"
          >
            <span class="ic" v-html="iconSvg(item.icon)"></span>
            <span>{{ t(item.label) }}</span>
            <span v-if="dirtyTabs.has(item.id)" class="dirty-dot" :title="t('Modifiche non salvate')"></span>
            <span v-if="searchActive && hitCount(item.id)" class="hit-n">{{ hitCount(item.id) }}</span>
            <span v-if="item.soon" class="pill-soon">Soon</span>
            <span v-else-if="item.badge" class="badge-new">{{ item.badge }}</span>
            <span v-else class="chev">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
            </span>
          </div>
        </div>
      </div>
      <div class="cfg-side-footer">
        <div class="lic">
          <span class="ok"></span>
          <span>{{ t('Versione') }} <b>{{ version }}</b></span>
        </div>
        <div class="ver">build {{ buildId }}</div>
      </div>
    </aside>

    <!-- ═══ CONTENT ═══ -->
    <main ref="contentEl" class="cfg-content" :id="'cfg-panel-' + activeTab" role="tabpanel" :aria-labelledby="'cfg-tab-' + activeTab">
      <!-- Ricerca a livello di campo: la vista risultati sostituisce il tab finché c'è una query -->
      <div v-if="searchActive" class="cfg-sr">
        <div class="cfg-sr-head">
          <h1>{{ srTitle }}</h1>
          <span v-if="totalHits" class="sub">{{ t('in') }} {{ fieldResults.length }} {{ fieldResults.length === 1 ? t('scheda') : t('schede') }}</span>
        </div>
        <div v-if="!totalHits" class="cfg-sr-empty">
          <p>{{ t('Nessun campo combacia. Prova con una parola diversa, o sfoglia le schede a sinistra.') }}</p>
        </div>
        <div v-for="res in fieldResults" :key="'sr-' + res.item.id" class="cfg-sr-group">
          <button type="button" class="cfg-sr-group-head" @click="onPick(res.item)">
            <span class="ic" v-html="iconSvg(res.item.icon)"></span>
            <b>{{ t(res.item.label) }}</b>
            <span class="grp">{{ t(res.groupTitle) }}</span>
            <span class="open">{{ t('Apri scheda') }} →</span>
          </button>
          <button
            v-for="(hit, hi) in res.hits"
            :key="'hit-' + res.item.id + '-' + hi"
            type="button"
            class="cfg-sr-row"
            @click="openField(res.item, hit)"
          >
            <span class="sr-lab">
              <span v-html="hl(t(hit.label))"></span>
              <span v-if="hit.kind === 'section'" class="sr-kind">{{ t('sezione') }}</span>
            </span>
            <span v-if="hit.hint" class="sr-hint" v-html="hl(t(hit.hint))"></span>
            <span class="sr-go">{{ t('Vai al campo') }} →</span>
          </button>
        </div>
      </div>
      <!-- v-show (non v-else): il KeepAlive deve restare montato durante la
           ricerca, o la cache dei tab (e le modifiche non salvate) si perde. -->
      <div v-show="!searchActive" class="cfg-tab-host">
        <KeepAlive>
          <component :is="currentTabComponent" v-bind="currentTabProps" @dirty="onTabDirty" />
        </KeepAlive>
      </div>
    </main>

    <!-- ═══ SAVEBAR ═══ -->
    <footer class="cfg-savebar">
      <div class="meta">
        <span v-if="dirty" class="dirty">
          <span class="dot"></span> {{ t('Modifiche non salvate in') }}:
          <button
            v-for="d in dirtyTabItems"
            :key="'dirty-' + d.id"
            type="button"
            class="dirty-tab-link"
            @click="onPick(d)"
          >{{ t(d.label) }}</button>
        </span>
        <span v-if="lastSavedAt">{{ t('Ultimo salvataggio') }} <b>{{ lastSavedAt }}</b></span>
      </div>
      <div class="grow"></div>
      <div class="save-actions">
        <button class="cfg-btn cfg-btn-ghost" :disabled="!dirty || saving" @click="onDiscard">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7v6h6"/><path d="M3 13a9 9 0 1 0 3-6.7L3 9"/></svg>
          {{ t('Annulla modifiche') }}
        </button>
        <button class="cfg-btn cfg-btn-secondary" @click="onPreview">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg>
          {{ t('Anteprima') }}
        </button>
        <button class="cfg-btn cfg-btn-primary" :disabled="!dirty || saving" @click="onSave">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><path d="M17 21v-8H7v8M7 3v5h8"/></svg>
          {{ saving ? t('Salvataggio…') : t('Salva impostazioni') }}
        </button>
      </div>
    </footer>

    <!-- Toast -->
    <Transition name="cfg-toast">
      <div v-if="toast" class="cfg-toast" :class="toast.type">
        <svg v-if="toast.type === 'success'" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>
        <svg v-else width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path d="M15 9l-6 6M9 9l6 6"/></svg>
        {{ toast.message }}
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, computed, provide, watch, onMounted, nextTick } from 'vue';
import { t } from '@/i18n';
import { SETTINGS_FIELD_INDEX } from '@/config/settingsSearchIndex';

// ─── Tab components ─────────────────────────────────────────────────
import ColorsTab from './ColorsTab.vue';
import TypographyTab from './TypographyTab.vue';
import SpaziatureTab from './SpaziatureTab.vue';
import BreakpointsTab from './BreakpointsTab.vue';
import TemplateConditionsTab from './TemplateConditionsTab.vue';
import WooTemplatesTab from './WooTemplatesTab.vue';
import PopupsTab from './PopupsTab.vue';
import SeoTab from './SeoTab.vue';
import RedirectsTab from './RedirectsTab.vue';
import CookieTab from './CookieTab.vue';
import AnalyticsTab from './AnalyticsTab.vue';
import PerformanceTab from './PerformanceTab.vue';
import MaintenanceTab from './MaintenanceTab.vue';
import AITab from './AITab.vue';
import StockmediaTab from './StockmediaTab.vue';
import WhitelabelTab from './WhitelabelTab.vue';
import PermessiTab from './PermessiTab.vue';

// ─── Information Architecture ───────────────────────────────────────
const IA_GROUPS = [
  {
    id: 'design', title: 'Design', items: [
      { id: 'colori',     label: 'Palette & Stili',       icon: 'palette', component: ColorsTab },
      { id: 'tipografia', label: 'Tipografia',            icon: 'type',    component: TypographyTab },
      { id: 'spaziature', label: 'Spaziature & layout',   icon: 'layers',  component: SpaziatureTab },
      { id: 'responsive', label: 'Breakpoint responsive', icon: 'devices', component: BreakpointsTab },
    ],
  },
  {
    id: 'content', title: 'Contenuti & Template', items: [
      { id: 'tplconditions', label: 'Assegnazione template', icon: 'sitemap', component: TemplateConditionsTab },
      { id: 'wootemplates',  label: 'WooCommerce template',  icon: 'cart',    component: WooTemplatesTab },
      { id: 'popups',        label: 'Popup globali',         icon: 'window',  component: PopupsTab },
    ],
  },
  {
    id: 'seoprivacy', title: 'SEO & Privacy', items: [
      { id: 'seo',       label: 'SEO globale',           icon: 'search',     component: SeoTab },
      { id: 'redirects', label: 'Redirect & 404',        icon: 'redirect',   component: RedirectsTab },
      { id: 'cookie',    label: 'Cookie Consent & GDPR', icon: 'key',        component: CookieTab },
      { id: 'analytics', label: 'Tracking & Analytics',  icon: 'chart',      component: AnalyticsTab },
    ],
  },
  {
    id: 'prestazioni', title: 'Prestazioni & Servizi', items: [
      { id: 'performance', label: 'Performance & Cache',      icon: 'gauge',    component: PerformanceTab },
      { id: 'maintenance', label: 'Manutenzione & Coming Soon', icon: 'tool',    component: MaintenanceTab },
      { id: 'ai',          label: 'AI Assistant',             icon: 'sparkles', component: AITab,          badge: 'NEW' },
      { id: 'stockmedia',  label: 'Stock media',              icon: 'image',    component: StockmediaTab },
    ],
  },
  {
    id: 'team', title: 'Team & Brand', items: [
      { id: 'whitelabel', label: 'White Label',      icon: 'drop',   component: WhitelabelTab },
      { id: 'permessi',   label: 'Permessi & Ruoli', icon: 'wrench', component: PermessiTab },
    ],
  },
];

// Flatten per ricerca / lookup
const ALL_ITEMS = IA_GROUPS.flatMap(g => g.items);

// ─── State ──────────────────────────────────────────────────────────
const activeTab = ref(initialTab());
const filterQuery = ref('');
// Dirty per-tab: sappiamo DOVE sono le modifiche non salvate, non solo che
// esistono. Set riassegnato a ogni cambio (mai mutato) per la reattività.
const dirtyTabs = ref(new Set());
const dirty = computed(() => dirtyTabs.value.size > 0);
const saving = ref(false);
const lastSavedAt = ref(window.oloData?.settingsLastSaved || '');
const toast = ref(null);
const contentEl = ref(null);
const topSearchEl = ref(null);
// Anti-autofill: il modello accetta testo SOLO dopo un focus reale sul campo.
// Chrome accoppia il password dell'API key (tab AI) con uno "username" e lo
// scriveva qui senza alcun focus: la vista risultati si apriva da sola.
const searchArmed = ref(false);
function onSearchInput(e) {
  if (!searchArmed.value) {
    e.target.value = filterQuery.value; // ripulisce l'autofill dal campo
    return;
  }
  filterQuery.value = e.target.value;
}
// Schede usate di recente: [{id, at}] in localStorage.
const RECENT_KEY = 'olo_cfg_recent_tabs';
const recentRaw = ref(loadRecent());

const version = window.oloData?.version || '1.0.0';
const logoUrl = (window.oloData?.pluginUrl || '/wp-content/plugins/olobuild/') + 'assets/img/olobuild-horizontal.png';
const buildId = computed(() => {
  const d = new Date();
  return `${d.getFullYear()}.${String(d.getMonth() + 1).padStart(2, '0')}`;
});

const dashboardUrl = computed(() => (window.oloData?.adminUrl || '') + 'admin.php?page=olobuild');
const docsUrl = 'https://olotheme.com/docs/olobuild/';
const siteUrl = computed(() => window.oloData?.siteUrl || '/');

function initialTab() {
  const url = new URL(window.location.href);
  const fromUrl = url.searchParams.get('tab');
  if (fromUrl && ALL_ITEMS.find(i => i.id === fromUrl && !i.soon)) return fromUrl;
  try {
    const stored = localStorage.getItem('olo_cfg_active_tab');
    if (stored && ALL_ITEMS.find(i => i.id === stored && !i.soon)) return stored;
  } catch (e) { /* ignore */ }
  return 'colori';
}

// ─── Ricerca a livello di campo ─────────────────────────────────────
// L'indice (generato dai *Tab.vue) elenca label/hint/sezioni di ogni scheda;
// il match usa t() così vale anche con l'interfaccia tradotta.
const searchActive = computed(() => filterQuery.value.trim().length >= 2);

function entryMatches(entry, q) {
  if (t(entry.label).toLowerCase().includes(q)) return true;
  if (entry.hint && t(entry.hint).toLowerCase().includes(q)) return true;
  if (entry.section && t(entry.section).toLowerCase().includes(q)) return true;
  return false;
}

const fieldResults = computed(() => {
  const q = filterQuery.value.trim().toLowerCase();
  if (q.length < 2) return [];
  const out = [];
  for (const group of IA_GROUPS) {
    for (const item of group.items) {
      if (item.soon) continue;
      const entries = SETTINGS_FIELD_INDEX[item.id] || [];
      const hits = entries.filter(e => entryMatches(e, q));
      // Il nome della scheda che combacia conta come risultato anche senza campi.
      if (hits.length || t(item.label).toLowerCase().includes(q)) {
        out.push({ item, groupTitle: group.title, hits });
      }
    }
  }
  return out;
});

const totalHits = computed(() => fieldResults.value.reduce((n, r) => n + r.hits.length, 0));
const srTitle = computed(() => {
  const q = filterQuery.value.trim();
  if (!totalHits.value) return `${t('Nessun campo trovato per')} «${q}»`;
  const n = totalHits.value;
  return `${n} ${n === 1 ? t('campo trovato per') : t('campi trovati per')} «${q}»`;
});

function hitCount(tabId) {
  const r = fieldResults.value.find(x => x.item.id === tabId);
  return r ? r.hits.length : 0;
}

// Evidenzia il termine cercato (testo escapato prima, poi <mark>).
function hl(text) {
  const q = filterQuery.value.trim();
  const esc = String(text).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
  if (!q) return esc;
  const safeQ = q.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
  return esc.replace(new RegExp(`(${safeQ})`, 'ig'), '<mark>$1</mark>');
}

// ─── Filtered groups (live search) ──────────────────────────────────
// La sidebar tiene le voci il cui NOME combacia oppure che hanno campi trovati.
const filteredGroups = computed(() => {
  const q = filterQuery.value.trim().toLowerCase();
  if (!q) return IA_GROUPS;
  return IA_GROUPS
    .map(g => ({ ...g, items: g.items.filter(i => i.label.toLowerCase().includes(q) || hitCount(i.id) > 0) }))
    .filter(g => g.items.length > 0);
});

// ─── Usate di recente ───────────────────────────────────────────────
function loadRecent() {
  try {
    const arr = JSON.parse(localStorage.getItem(RECENT_KEY) || '[]');
    return Array.isArray(arr) ? arr.filter(r => r && r.id) : [];
  } catch (e) { return []; }
}
function touchRecent(id) {
  const next = [{ id, at: Date.now() }, ...recentRaw.value.filter(r => r.id !== id)].slice(0, 6);
  recentRaw.value = next;
  try { localStorage.setItem(RECENT_KEY, JSON.stringify(next)); } catch (e) { /* ignore */ }
}
function relTime(at) {
  const mins = Math.round((Date.now() - at) / 60000);
  if (mins < 2) return t('adesso');
  if (mins < 60) return `${mins} min`;
  const hours = Math.round(mins / 60);
  if (hours < 24) return `${hours} h`;
  const days = Math.round(hours / 24);
  return days === 1 ? t('ieri') : `${days} ${t('gg')}`;
}
const recentItems = computed(() =>
  recentRaw.value
    .filter(r => r.id !== activeTab.value)
    .map(r => {
      const item = ALL_ITEMS.find(i => i.id === r.id && !i.soon);
      return item ? { item, when: relTime(r.at) } : null;
    })
    .filter(Boolean)
    .slice(0, 3)
);

const dirtyTabItems = computed(() =>
  ALL_ITEMS.filter(i => dirtyTabs.value.has(i.id))
);

const currentItem    = computed(() => ALL_ITEMS.find(i => i.id === activeTab.value));
const activeTabLabel = computed(() => t(currentItem.value?.label || ''));
const currentTabComponent = computed(() => currentItem.value?.component || ColorsTab);
const currentTabProps = computed(() => ({}));

// ─── Icon mapping (Lucide-style inline SVG) ─────────────────────────
const ICONS = {
  layers:   '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 2 9 5-9 5-9-5 9-5z"/><path d="m3 17 9 5 9-5"/><path d="m3 12 9 5 9-5"/></svg>',
  palette:  '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="13.5" cy="6.5" r="1.5"/><circle cx="17.5" cy="10.5" r="1.5"/><circle cx="8.5" cy="7.5" r="1.5"/><circle cx="6.5" cy="12.5" r="1.5"/><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10c.9 0 1.5-.5 1.5-1.5 0-.4-.2-.8-.5-1.2a1.5 1.5 0 0 1 1.2-2.3h2C18.5 17 20.5 15 20.5 12.4 20.5 6.5 16.7 2 12 2z"/></svg>',
  type:     '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7V5h16v2"/><path d="M9 20h6"/><path d="M12 5v15"/></svg>',
  devices:  '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="14" height="11" rx="1.5"/><rect x="14" y="9" width="8" height="11" rx="1.5"/><path d="M5 20h6"/></svg>',
  search:   '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>',
  key:      '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="14" r="4"/><path d="m11 12 9-9 3 3-3 3-2-2-2 2-2-2-3 3"/></svg>',
  gauge:    '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 14 8 10"/><circle cx="12" cy="14" r="9"/><path d="M3 14a9 9 0 0 1 18 0"/></svg>',
  sparkles: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l1.5 4.5L18 9l-4.5 1.5L12 15l-1.5-4.5L6 9l4.5-1.5L12 3z"/><path d="M19 14l.7 2.3L22 17l-2.3.7L19 20l-.7-2.3L16 17l2.3-.7L19 14z"/></svg>',
  image:    '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-5-5L5 21"/></svg>',
  drop:     '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3s7 8 7 13a7 7 0 0 1-14 0c0-5 7-13 7-13z"/></svg>',
  wrench:   '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a4 4 0 0 0-5.4 5.4L2 19l3 3 7.3-7.3a4 4 0 0 0 5.4-5.4l-2.6 2.6-2.4-.6-.6-2.4 2.6-2.6z"/></svg>',
  sitemap:  '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="10" y="2" width="4" height="4"/><rect x="3" y="14" width="4" height="4"/><rect x="10" y="14" width="4" height="4"/><rect x="17" y="14" width="4" height="4"/><path d="M12 6v4M5 14v-2h14v2"/></svg>',
  cart:     '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="20" r="1.5"/><circle cx="19" cy="20" r="1.5"/><path d="M1 1h4l2.7 13.4a2 2 0 0 0 2 1.6h9.7a2 2 0 0 0 2-1.6L23 6H6"/></svg>',
  window:   '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="15" rx="2"/><path d="M3 10h18"/><circle cx="6.5" cy="7.5" r=".5" fill="currentColor"/><circle cx="9" cy="7.5" r=".5" fill="currentColor"/></svg>',
  redirect: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12h13M13 6l6 6-6 6"/><path d="M7 18v-3"/></svg>',
  chart:    '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18"/><path d="M7 17V8M12 17V4M17 17v-6"/></svg>',
  tool:     '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22a10 10 0 1 1 0-20 10 10 0 0 1 0 20z"/><path d="M12 6v6l4 2"/></svg>',
};
function iconSvg(name) {
  return ICONS[name] || ICONS.layers;
}

// ─── Actions ────────────────────────────────────────────────────────
function onPick(item) {
  if (item.soon) return;
  touchRecent(item.id);
  if (item.id === activeTab.value) {
    // Già attiva: la ricerca (se aperta) si chiude e si torna alla scheda.
    filterQuery.value = '';
    return;
  }
  activeTab.value = item.id;
  try { localStorage.setItem('olo_cfg_active_tab', item.id); } catch (e) { /* ignore */ }
  // Aggiorna l'URL senza reload per supportare bookmark/deep link.
  const url = new URL(window.location.href);
  url.searchParams.set('tab', item.id);
  window.history.replaceState({}, '', url);
  // Scroll content area to top
  nextTick(() => {
    if (contentEl.value) contentEl.value.scrollTop = 0;
  });
}

// «Vai al campo»: apre la scheda giusta, scorre fino al campo e lo illumina.
function openField(item, entry) {
  onPick(item);
  filterQuery.value = '';
  // I tab montano subito (import statici) ma alcuni idratano async: pochi
  // tentativi distanziati, poi ci si arrende in silenzio (la scheda è comunque aperta).
  let tries = 0;
  const attempt = () => {
    tries++;
    if (findAndFlash(entry) || tries >= 10) return;
    setTimeout(attempt, 150);
  };
  nextTick(attempt);
}

function findAndFlash(entry) {
  const host = contentEl.value;
  if (!host) return false;
  const wanted = t(entry.label).trim();
  const selector = entry.kind === 'section' ? 'h3' : 'label';
  const nodes = host.querySelectorAll(selector);
  for (const node of nodes) {
    if (node.textContent.trim() !== wanted) continue;
    const target = node.closest('.cfg-row') || node.closest('.cfg-card') || node;
    // Scroll SOLO del pannello contenuti (mai scrollIntoView: scrolla anche
    // gli antenati della pagina admin).
    const top = target.getBoundingClientRect().top - host.getBoundingClientRect().top + host.scrollTop - 84;
    host.scrollTo({ top: Math.max(0, top), behavior: 'smooth' });
    target.classList.remove('cfg-field-flash');
    // Reflow per far ripartire l'animazione se il campo era già evidenziato.
    void target.offsetWidth;
    target.classList.add('cfg-field-flash');
    setTimeout(() => target.classList.remove('cfg-field-flash'), 2400);
    return true;
  }
  return false;
}

// I tab figli emettono `dirty` quando un loro field cambia.
function markDirty(v) {
  const id = activeTab.value;
  const next = new Set(dirtyTabs.value);
  if (v) next.add(id); else next.delete(id);
  dirtyTabs.value = next;
}
function onTabDirty() { markDirty(true); }

async function onSave() {
  if (!dirty.value || saving.value) return;
  saving.value = true;
  try {
    // Il singolo tab si auto-salva via provide/inject 'requestSave';
    // qui notifichiamo tutti i tab presenti via custom event globale,
    // così ognuno fa il suo POST. Sequenziale per evitare race su option keys.
    window.dispatchEvent(new CustomEvent('olo-cfg-save'));
    // Mostriamo toast subito; il singolo tab fa il proprio toast in caso di errore.
    showToast(t('Impostazioni salvate'), 'success');
    dirtyTabs.value = new Set();
    lastSavedAt.value = formatNow();
  } catch (e) {
    showToast(e?.message || t('Errore di salvataggio'), 'error');
  } finally {
    saving.value = false;
  }
}

function onDiscard() {
  if (!dirty.value) return;
  if (!confirm(t('Annullare tutte le modifiche non salvate?'))) return;
  window.dispatchEvent(new CustomEvent('olo-cfg-discard'));
  dirtyTabs.value = new Set();
}

function onPreview() {
  window.open(window.oloData?.siteUrl || '/', '_blank', 'noopener');
}

function formatNow() {
  const d = new Date();
  const hh = String(d.getHours()).padStart(2, '0');
  const mm = String(d.getMinutes()).padStart(2, '0');
  return `${t('oggi alle')} ${hh}:${mm}`;
}

function showToast(message, type = 'success') {
  toast.value = { message, type };
  setTimeout(() => { toast.value = null; }, 2500);
}

// Provide il setter di toast ai tab figli (mantiene compat con codice esistente).
provide('showToast', showToast);
// Provide anche il setter di dirty per i tab che vogliono notificarlo direttamente.
provide('setDirty', (v) => { markDirty(!!v); });

// Cleanup search se cambia tab.
watch(activeTab, () => { filterQuery.value = ''; });

onMounted(() => {
  // Deep-link dalla palette globale ⌘K: ?tab=…&field=<label> apre la scheda
  // (già fatto da initialTab) e scorre fino al campo, illuminandolo.
  try {
    const wanted = new URL(window.location.href).searchParams.get('field');
    if (wanted) {
      const entry = (SETTINGS_FIELD_INDEX[activeTab.value] || []).find(e => e.label === wanted);
      if (entry) openField(currentItem.value, entry);
    }
  } catch (e) { /* ignore */ }

  window.addEventListener('keydown', (e) => {
    // Trap Ctrl/Cmd+S → Save
    if ((e.ctrlKey || e.metaKey) && e.key === 's') {
      e.preventDefault();
      onSave();
    }
    // Ctrl/Cmd+K → focus sulla ricerca (il badge ⌘K in topbar ora dice il vero)
    if ((e.ctrlKey || e.metaKey) && (e.key === 'k' || e.key === 'K')) {
      e.preventDefault();
      topSearchEl.value?.focus();
      topSearchEl.value?.select();
    }
  });
});
</script>

<style>
@import url('https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Work+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap');

/* La pagina admin WP riserva l'area #wpbody-content; vogliamo che la nostra shell la riempia per intero. */
#wpbody-content { padding-bottom: 0 !important; }
.wrap.olo-cfg-wrap { margin: 0; padding: 0; }
.wrap.olo-cfg-wrap > h1.screen-reader-text { display: none; }

/* Token condivisi: .cfg-root (shell) + .cfg-layer (popup/dropdown teleportati
   su body, che altrimenti non erediterebbero le variabili). */
.cfg-root, .cfg-layer {
  --c-red:        #e1474f;
  --c-red-dark:   #c8323a;
  --c-red-soft:   #fef2f3;
  --c-red-soft-2: #fde2e4;
  --c-navy:       #0f172a;
  --c-navy-2:     #1e293b;
  --c-navy-3:     #334155;
  --c-cream:      #faf7f2;
  --c-cream-2:    #f3ede2;
  --c-line:       #e6e8ee;
  --c-line-soft:  #eef0f4;
  --c-bg:         #f6f7fa;
  --c-text:       #1e293b;
  --c-text-mute:  #5e6a7a;
  --c-text-faint: #94a3b8;
  --c-success:    #15803d;
  --c-success-soft:#dcfce7;
  --c-warning:    #b45309;
  --c-warning-soft:#fef3c7;
  --c-sans:    'Work Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
  --c-display: 'Instrument Serif', 'Playfair Display', Georgia, serif;
  --c-mono:    'JetBrains Mono', ui-monospace, SFMono-Regular, monospace;
}
.cfg-layer {
  font-family: var(--c-sans);
  color: var(--c-text);
  -webkit-font-smoothing: antialiased;
}
.cfg-root {
  font-family: var(--c-sans);
  color: var(--c-text);
  font-size: 14px;
  line-height: 1.45;
  display: grid;
  grid-template-columns: 264px 1fr;
  grid-template-rows: 56px 1fr 64px;
  grid-template-areas:
    'topbar topbar'
    'sidebar content'
    'sidebar savebar';
  /* Altezza calcolata sottraendo la barra admin di WP (32px desktop, 46px mobile) */
  height: calc(100vh - var(--wp-admin--admin-bar--height, 32px));
  background: var(--c-bg);
  overflow: hidden;
  isolation: isolate;
  -webkit-font-smoothing: antialiased;
}
@media (max-width: 782px) {
  .cfg-root { height: calc(100vh - 46px); }
}

/* ── TOPBAR ────────────────────────────────────────────────────────── */
.cfg-topbar {
  grid-area: topbar;
  display: flex; align-items: center; gap: 16px;
  padding: 0 24px;
  background: #fff;
  border-bottom: 1px solid var(--c-line);
  z-index: 2;
}
.cfg-topbar .brand {
  display: flex; align-items: center; gap: 10px;
}
.cfg-topbar .brand-link {
  display: inline-flex; align-items: center;
  text-decoration: none;
  transition: opacity .15s;
}
.cfg-topbar .brand-link:hover { opacity: .75; }
.cfg-topbar .brand-logo {
  height: 28px; width: auto; display: block;
}
.cfg-topbar .brand-version {
  font-size: 11px;
  font-weight: 600;
  background: var(--c-success-soft);
  color: var(--c-success);
  padding: 3px 8px;
  border-radius: 999px;
  font-family: var(--c-mono);
  white-space: nowrap;
}
.cfg-topbar .crumb {
  display: flex; align-items: center; gap: 8px;
  font-size: 13px;
  color: var(--c-text-mute);
  padding-left: 24px;
  margin-left: 8px;
  border-left: 1px solid var(--c-line);
  height: 26px;
}
.cfg-topbar .crumb a { color: var(--c-text-mute); text-decoration: none; cursor: pointer; }
.cfg-topbar .crumb a:hover { color: var(--c-navy); }
.cfg-topbar .crumb b { color: var(--c-navy); font-weight: 600; }
.cfg-topbar .crumb .sep { color: var(--c-text-faint); }
.cfg-topbar .spacer { flex: 1; }
.cfg-topbar .top-search {
  display: flex; align-items: center; gap: 8px;
  background: var(--c-bg);
  border: 1px solid var(--c-line);
  border-radius: 8px;
  padding: 7px 12px;
  width: 320px;
  font-size: 13px;
  color: var(--c-text-mute);
}
.cfg-topbar .top-search svg { width: 14px; height: 14px; color: var(--c-text-faint); flex-shrink: 0; }
.cfg-topbar .top-search input { flex: 1; border: 0; outline: none; background: transparent; font: inherit; color: var(--c-text); min-width: 0; }
.cfg-topbar .top-search .kbd {
  margin-left: auto;
  font-family: var(--c-mono);
  font-size: 11px; color: var(--c-text-faint);
  background: #fff;
  border: 1px solid var(--c-line);
  border-radius: 4px;
  padding: 1px 5px;
}
.cfg-topbar .top-actions { display: flex; align-items: center; gap: 8px; }
.cfg-topbar .doc-link {
  display: inline-flex; align-items: center; gap: 6px;
  font-size: 13px; font-weight: 500;
  color: var(--c-text-mute);
  padding: 7px 12px;
  border-radius: 8px;
  text-decoration: none;
  white-space: nowrap;
}
.cfg-topbar .doc-link:hover { background: var(--c-bg); color: var(--c-navy); }
.cfg-topbar .doc-link svg { width: 14px; height: 14px; }

/* ── SIDEBAR ──────────────────────────────────────────────────────── */
.cfg-sidebar {
  grid-area: sidebar / sidebar / savebar / sidebar;
  background: var(--c-navy);
  border-right: 0;
  overflow-y: auto;
  display: flex; flex-direction: column;
}
.cfg-side-search { padding: 16px 16px 8px; }
.cfg-side-search-input {
  display: flex; align-items: center; gap: 8px;
  background: rgba(255,255,255,.06);
  border: 1px solid rgba(255,255,255,.08);
  border-radius: 8px;
  padding: 8px 10px;
  font-size: 13px;
  color: rgba(255,255,255,.6);
}
.cfg-side-search-input svg { width: 14px; height: 14px; color: rgba(255,255,255,.5); flex-shrink: 0; }
.cfg-side-search-input input {
  flex: 1; border: 0; outline: none; background: transparent;
  font: inherit; color: #fff; min-width: 0;
}
.cfg-side-search-input input::placeholder { color: rgba(255,255,255,.4); }
.cfg-sidegroups { flex: 1; padding: 4px 8px 16px; }
.cfg-group { padding: 8px 0 4px; }
.cfg-group-head {
  display: flex; align-items: center; gap: 8px;
  font-size: 10px; font-weight: 700;
  text-transform: uppercase;
  letter-spacing: .12em;
  color: rgba(255,255,255,.4);
  padding: 10px 12px 6px;
}
.cfg-group-head .count {
  margin-left: auto;
  background: rgba(255,255,255,.06);
  color: rgba(255,255,255,.5);
  font-size: 10px;
  padding: 1px 6px;
  border-radius: 999px;
  font-weight: 600;
}
.cfg-side-item {
  display: grid;
  grid-template-columns: 28px 1fr auto;
  align-items: center; gap: 8px;
  padding: 8px 10px;
  margin: 1px 4px;
  border-radius: 8px;
  cursor: pointer;
  font-size: 13.5px;
  font-weight: 500;
  color: rgba(255,255,255,.85);
  transition: background .12s, color .12s;
}
.cfg-side-item:hover { background: rgba(255,255,255,.05); }
.cfg-side-item .ic {
  width: 26px; height: 26px;
  display: grid; place-items: center;
  border-radius: 7px;
  background: rgba(255,255,255,.05);
  color: rgba(255,255,255,.7);
}
.cfg-side-item .ic svg { width: 14px; height: 14px; }
.cfg-side-item.is-active { background: var(--c-red); color: #fff; }
.cfg-side-item.is-active .ic {
  background: rgba(255,255,255,.18);
  color: #fff;
}
.cfg-side-item.is-active .chev { color: rgba(255,255,255,.7); }
.cfg-side-item.is-soon { color: rgba(255,255,255,.35); cursor: not-allowed; }
.cfg-side-item .pill-soon {
  font-size: 9px;
  background: rgba(255,255,255,.06);
  color: rgba(255,255,255,.4);
  padding: 2px 6px;
  border-radius: 4px;
  font-weight: 700; letter-spacing: .04em;
  text-transform: uppercase;
}
.cfg-side-item .chev {
  color: rgba(255,255,255,.4);
  width: 14px; height: 14px;
  display: grid; place-items: center;
}
.cfg-side-item .chev svg { width: 14px; height: 14px; }
.cfg-side-item .badge-new {
  background: var(--c-red-soft);
  color: var(--c-red-dark);
  font-size: 9px; font-weight: 700;
  padding: 2px 6px;
  border-radius: 4px;
  letter-spacing: .04em;
  text-transform: uppercase;
}
.cfg-side-footer {
  border-top: 1px solid rgba(255,255,255,.08);
  padding: 14px 16px;
  display: grid; gap: 6px;
  font-size: 12px;
  color: rgba(255,255,255,.5);
}
.cfg-side-footer .lic { display: flex; align-items: center; gap: 8px; }
.cfg-side-footer .lic .ok {
  width: 7px; height: 7px; border-radius: 50%;
  background: var(--c-success);
  box-shadow: 0 0 0 3px var(--c-success-soft);
}
.cfg-side-footer .lic b { color: #fff; font-weight: 600; }
.cfg-side-footer .ver { font-family: var(--c-mono); font-size: 11px; color: rgba(255,255,255,.35); }

/* ── CONTENT ──────────────────────────────────────────────────────── */
.cfg-content {
  grid-area: content;
  overflow-y: auto;
  padding: 32px 40px 40px;
}
/* Righe di lettura contenute: su viewport larghi le card non si stirano
   all'infinito (look da console professionale, à la Stripe/Linear). */
.cfg-content > * { max-width: 1080px; }
.cfg-page-head {
  display: flex; align-items: flex-end; gap: 24px;
  margin-bottom: 28px;
  padding-bottom: 24px;
  border-bottom: 1px solid var(--c-line);
}
.cfg-page-head h1 {
  font-family: var(--c-sans);
  font-weight: 700;
  font-size: 24px;
  letter-spacing: -.02em;
  margin: 0 0 6px;
  color: var(--c-navy);
}
.cfg-page-head h1 em { color: var(--c-red); font-style: normal; }
.cfg-page-head p {
  font-size: 14px;
  color: var(--c-text-mute);
  margin: 0;
  max-width: 60ch;
}
.cfg-page-head .head-actions {
  margin-left: auto;
  display: flex; gap: 8px;
  flex-shrink: 0;
}

.cfg-card {
  background: #fff;
  border: 1px solid var(--c-line);
  border-radius: 14px;
  margin-bottom: 16px;
  overflow: hidden;
}
.cfg-card-head {
  display: flex; align-items: center; gap: 14px;
  padding: 18px 22px;
  border-bottom: 1px solid var(--c-line-soft);
}
.cfg-card-head h3 {
  font-size: 15px; font-weight: 600;
  margin: 0; color: var(--c-navy);
  letter-spacing: -.005em;
}
.cfg-card-head p { font-size: 13px; color: var(--c-text-mute); margin: 2px 0 0; }
.cfg-card-head .head-ic {
  width: 36px; height: 36px;
  border-radius: 9px;
  background: var(--c-bg);
  color: var(--c-navy);
  display: grid; place-items: center;
  border: 1px solid var(--c-line);
  flex-shrink: 0;
}
.cfg-card-head .head-ic svg { width: 18px; height: 18px; }
.cfg-card-head .head-actions { margin-left: auto; display: flex; align-items: center; gap: 8px; }
.cfg-card-body { padding: 22px; }
.cfg-card-body.tight { padding: 16px 22px; }

/* Form row generico */
.cfg-row {
  display: grid;
  grid-template-columns: 220px 1fr;
  gap: 32px;
  padding: 14px 0;
  border-bottom: 1px solid var(--c-line-soft);
}
.cfg-row:last-child { border-bottom: 0; }
.cfg-row.no-divider { border-bottom: 0; }
.cfg-row .label-col label {
  display: block; font-size: 13px; font-weight: 600;
  color: var(--c-navy); margin-bottom: 4px;
}
.cfg-row .label-col .hint {
  font-size: 12px; color: var(--c-text-mute); line-height: 1.5;
}
.cfg-row .label-col .req { color: var(--c-red); margin-left: 2px; }

/* Form controls — base */
.cfg-input, .cfg-select, .cfg-textarea {
  display: flex; align-items: center; gap: 8px;
  width: 100%;
  background: #fff;
  border: 1px solid var(--c-line);
  border-radius: 8px;
  padding: 8px 12px;
  font-family: inherit;
  font-size: 13.5px;
  color: var(--c-text);
  transition: border-color .12s, box-shadow .12s;
}
.cfg-input:focus-within, .cfg-select:focus-within, .cfg-textarea:focus-within {
  border-color: var(--c-red);
  box-shadow: 0 0 0 3px var(--c-red-soft);
}
.cfg-textarea {
  padding: 10px 12px;
  font-family: var(--c-mono);
  font-size: 12.5px;
  min-height: 88px;
  align-items: flex-start;
  line-height: 1.55;
}
.cfg-input.mono { font-family: var(--c-mono); font-size: 12.5px; }
.cfg-input .suffix { color: var(--c-text-faint); font-size: 12px; margin-left: auto; padding-left: 8px; }
.cfg-input .prefix { color: var(--c-text-faint); font-family: var(--c-mono); font-size: 12px; }
.cfg-input .reveal { color: var(--c-text-mute); cursor: pointer; background: transparent; border: 0; padding: 0; display: flex; }
.cfg-input .reveal svg { width: 14px; height: 14px; }
.cfg-input input, .cfg-textarea textarea {
  flex: 1;
  border: 0; outline: none;
  background: transparent;
  font-family: inherit; font-size: inherit; color: inherit;
  padding: 0; min-width: 0; width: 100%;
}
.cfg-textarea textarea { line-height: inherit; resize: vertical; }
.cfg-select { padding-right: 8px; }
.cfg-select select {
  flex: 1;
  border: 0; outline: none;
  background: transparent;
  font-family: inherit; font-size: inherit; color: inherit;
  appearance: none;
  padding: 0;
}
.cfg-select .chev { color: var(--c-text-faint); pointer-events: none; }
.cfg-select .chev svg { width: 14px; height: 14px; }

/* Larghezza dei controlli: un campo dev'essere largo quanto il dato che
   contiene. Default contenuto dentro le cfg-row; taglie esplicite cfg-w-*. */
.cfg-row .control-col .cfg-input,
.cfg-row .control-col .cfg-select,
.cfg-row .control-col .cfg-slider { max-width: 440px; }
.cfg-input.cfg-w-xs,  .cfg-select.cfg-w-xs  { width: 130px; flex: 0 0 auto; }
.cfg-input.cfg-w-sm,  .cfg-select.cfg-w-sm  { width: 210px; flex: 0 0 auto; }
.cfg-input.cfg-w-md,  .cfg-select.cfg-w-md  { width: 320px; flex: 0 0 auto; }
.cfg-input.cfg-w-lg,  .cfg-select.cfg-w-lg  { width: 480px; flex: 0 0 auto; }
.cfg-input.cfg-w-full, .cfg-select.cfg-w-full { width: 100%; max-width: none; }

/* Niente spinner nativi del browser sugli input numerici. */
.cfg-input input[type='number'] { -moz-appearance: textfield; appearance: textfield; }
.cfg-input input[type='number']::-webkit-outer-spin-button,
.cfg-input input[type='number']::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }

/* Segmented control */
.cfg-segment {
  display: inline-flex;
  background: var(--c-bg);
  border: 1px solid var(--c-line);
  border-radius: 8px;
  padding: 3px;
  gap: 2px;
}
.cfg-segment button {
  appearance: none; border: 0;
  background: transparent;
  font: 600 12.5px var(--c-sans);
  color: var(--c-text-mute);
  padding: 6px 12px;
  border-radius: 6px;
  cursor: pointer;
  display: inline-flex; align-items: center; gap: 6px;
}
.cfg-segment button svg { width: 14px; height: 14px; }
.cfg-segment button.is-on {
  background: #fff;
  color: var(--c-navy);
  box-shadow: 0 1px 2px rgba(15,23,42,.08);
}

/* Switch */
.cfg-switch {
  width: 36px; height: 20px;
  background: #cbd5e1;
  border-radius: 999px;
  position: relative;
  cursor: pointer;
  transition: background .15s;
  flex-shrink: 0;
  border: 0; padding: 0;
}
.cfg-switch::after {
  content: '';
  position: absolute;
  top: 2px; left: 2px;
  width: 16px; height: 16px;
  background: #fff;
  border-radius: 50%;
  transition: transform .15s;
  box-shadow: 0 1px 2px rgba(0,0,0,.2);
}
.cfg-switch.is-on { background: var(--c-red); }
.cfg-switch.is-on::after { transform: translateX(16px); }
.cfg-switch:disabled, .cfg-switch[aria-disabled='true'] { opacity: .55; cursor: not-allowed; }

/* Slider */
.cfg-slider { display: flex; align-items: center; gap: 14px; }
.cfg-slider input[type='range'] {
  flex: 1;
  -webkit-appearance: none; appearance: none;
  height: 6px;
  background: var(--c-bg);
  border-radius: 999px;
  border: 1px solid var(--c-line-soft);
  outline: none;
}
.cfg-slider input[type='range']::-webkit-slider-thumb {
  -webkit-appearance: none;
  width: 14px; height: 14px;
  background: #fff;
  border: 2px solid var(--c-red);
  border-radius: 50%;
  box-shadow: 0 1px 4px rgba(0,0,0,.15);
  cursor: pointer;
}
.cfg-slider input[type='range']::-moz-range-thumb {
  width: 14px; height: 14px;
  background: #fff;
  border: 2px solid var(--c-red);
  border-radius: 50%;
  cursor: pointer;
}
.cfg-slider .val {
  font: 500 12.5px var(--c-mono);
  width: 52px; text-align: right;
  color: var(--c-navy);
  font-variant-numeric: tabular-nums;
}

/* Buttons */
.cfg-btn {
  display: inline-flex; align-items: center; gap: 8px;
  padding: 8px 14px;
  border-radius: 8px;
  font: 600 13px var(--c-sans);
  border: 1px solid transparent;
  cursor: pointer;
  text-decoration: none;
  transition: background .12s, border-color .12s;
}
.cfg-btn svg { width: 14px; height: 14px; }
.cfg-btn:disabled { opacity: .55; cursor: not-allowed; }
.cfg-btn-primary {
  background: var(--c-red);
  color: #fff;
  box-shadow: 0 1px 2px rgba(15,23,42,.08), inset 0 -1px 0 rgba(0,0,0,.12);
}
.cfg-btn-primary:hover:not(:disabled) { background: var(--c-red-dark); }
.cfg-btn-secondary {
  background: #fff;
  color: var(--c-navy);
  border-color: var(--c-line);
}
.cfg-btn-secondary:hover:not(:disabled) { background: var(--c-bg); }
.cfg-btn-ghost { background: transparent; color: var(--c-text-mute); }
.cfg-btn-ghost:hover:not(:disabled) { color: var(--c-navy); background: var(--c-bg); }
.cfg-btn-danger {
  background: transparent;
  color: var(--c-red);
  border-color: var(--c-red-soft-2);
}
.cfg-btn-danger:hover:not(:disabled) { background: var(--c-red-soft); }
.cfg-btn-icon { width: 32px; height: 32px; padding: 0; justify-content: center; }

/* Status pill */
.cfg-pill {
  display: inline-flex; align-items: center; gap: 6px;
  font-size: 11px; font-weight: 700;
  letter-spacing: .04em;
  padding: 4px 8px;
  border-radius: 5px;
  text-transform: uppercase;
}
.cfg-pill .dot { width: 6px; height: 6px; border-radius: 50%; }
.cfg-pill.ok { background: var(--c-success-soft); color: var(--c-success); }
.cfg-pill.ok .dot { background: var(--c-success); }
.cfg-pill.warn { background: var(--c-warning-soft); color: var(--c-warning); }
.cfg-pill.warn .dot { background: var(--c-warning); }
.cfg-pill.off { background: var(--c-bg); color: var(--c-text-faint); }
.cfg-pill.off .dot { background: var(--c-text-faint); }
.cfg-pill.new { background: var(--c-red-soft); color: var(--c-red-dark); }

/* ── SAVEBAR ──────────────────────────────────────────────────────── */
.cfg-savebar {
  grid-area: savebar;
  background: #fff;
  border-top: 1px solid var(--c-line);
  display: flex; align-items: center; gap: 12px;
  padding: 0 40px;
  z-index: 2;
}
.cfg-savebar .meta { font-size: 12.5px; color: var(--c-text-mute); }
.cfg-savebar .meta b { color: var(--c-navy); font-weight: 600; }
.cfg-savebar .meta .dirty {
  display: inline-flex; align-items: center; gap: 6px;
  color: var(--c-warning);
  font-weight: 600;
  margin-right: 14px;
}
.cfg-savebar .meta .dirty .dot {
  width: 6px; height: 6px; border-radius: 50%;
  background: var(--c-warning);
  box-shadow: 0 0 0 3px var(--c-warning-soft);
}
.cfg-savebar .grow { flex: 1; }
.cfg-savebar .save-actions { display: flex; gap: 8px; }

/* ── TOAST ────────────────────────────────────────────────────────── */
.cfg-toast {
  position: fixed;
  bottom: 84px;
  right: 32px;
  display: flex; align-items: center; gap: 8px;
  padding: 12px 22px;
  border-radius: 12px;
  font-size: 13px; font-weight: 600;
  box-shadow: 0 8px 30px rgba(0,0,0,.12);
  z-index: 99999;
}
.cfg-toast.success { background: var(--c-navy); color: #fff; }
.cfg-toast.error   { background: var(--c-red);  color: #fff; }
.cfg-toast-enter-active { animation: cfg-toast-in .3s ease; }
.cfg-toast-leave-active { animation: cfg-toast-in .2s ease reverse; }
@keyframes cfg-toast-in {
  from { opacity: 0; transform: translateY(12px) scale(.95); }
  to   { opacity: 1; transform: translateY(0) scale(1); }
}

/* ── Responsive (sotto 960: collassa sidebar in drawer-stack) ───────── */
@media (max-width: 960px) {
  .cfg-root {
    grid-template-columns: 1fr;
    grid-template-rows: 56px auto 1fr 64px;
    grid-template-areas:
      'topbar'
      'sidebar'
      'content'
      'savebar';
    height: auto;
    min-height: calc(100vh - var(--wp-admin--admin-bar--height, 32px));
  }
  .cfg-sidebar {
    max-height: 280px;
    grid-area: sidebar;
    border-right: 0;
    border-bottom: 1px solid rgba(255,255,255,.08);
  }
  .cfg-topbar .top-search { display: none; }
  .cfg-topbar .crumb { display: none; }
  .cfg-content { padding: 20px; }
  .cfg-row { grid-template-columns: 1fr; gap: 8px; }
  .cfg-savebar { padding: 0 16px; }
}

/* ═══ Usate di recente (sidebar) ═══ */
.cfg-recent-head { display: flex; align-items: center; gap: 6px; color: var(--c-warning); }
.cfg-recent-head svg { width: 11px; height: 11px; }
.cfg-group-recent { border-bottom: 1px solid var(--c-line-soft); padding-bottom: 10px; }
.cfg-group-recent .cfg-side-item .recent-when { margin-left: auto; font-size: 11px; color: var(--c-text-faint); }

/* ═══ Stato modifiche per scheda ═══ */
.cfg-side-item .dirty-dot {
  width: 7px; height: 7px; border-radius: 50%;
  background: #f59e0b; margin-left: auto; flex-shrink: 0;
}
.cfg-side-item .dirty-dot + .hit-n,
.cfg-side-item .dirty-dot + .pill-soon,
.cfg-side-item .dirty-dot + .badge-new,
.cfg-side-item .dirty-dot + .chev { margin-left: 6px; }
.cfg-savebar .meta .dirty-tab-link {
  background: none; border: 0; padding: 0; margin: 0 0 0 6px; cursor: pointer;
  font: inherit; font-weight: 600; color: var(--c-warning);
  text-decoration: underline; text-underline-offset: 3px;
}
.cfg-savebar .meta .dirty-tab-link:hover { color: var(--c-red-dark); }

/* ═══ Ricerca a livello di campo ═══ */
.cfg-side-item .hit-n {
  margin-left: auto; flex-shrink: 0;
  font-size: 10.5px; font-weight: 700; line-height: 1;
  background: var(--c-red); color: #fff;
  border-radius: 9px; padding: 3px 7px;
}
.cfg-sr { display: flex; flex-direction: column; gap: 16px; max-width: 860px; }
.cfg-sr-head { display: flex; align-items: baseline; gap: 12px; }
.cfg-sr-head h1 {
  margin: 0; font-family: var(--c-display); font-weight: 400;
  font-size: 26px; color: var(--c-navy);
}
.cfg-sr-head .sub { font-size: 13px; color: var(--c-text-mute); }
.cfg-sr-empty { font-size: 14px; color: var(--c-text-mute); }
.cfg-sr mark { background: #fde68a; color: inherit; border-radius: 3px; padding: 0 1px; }
.cfg-sr-group { background: #fff; border: 1px solid var(--c-line); border-radius: 12px; overflow: hidden; }
.cfg-sr-group-head {
  display: flex; align-items: center; gap: 9px; width: 100%;
  background: var(--c-cream); border: 0; border-bottom: 1px solid var(--c-line);
  padding: 11px 18px; cursor: pointer; font: inherit; text-align: left;
}
.cfg-sr-group-head .ic { display: flex; color: var(--c-red); }
.cfg-sr-group-head .ic svg { width: 14px; height: 14px; }
.cfg-sr-group-head b { font-size: 13px; color: var(--c-navy); }
.cfg-sr-group-head .grp { font-size: 11.5px; color: var(--c-text-faint); }
.cfg-sr-group-head .open { margin-left: auto; font-size: 12px; font-weight: 600; color: var(--c-text-mute); }
.cfg-sr-group-head:hover .open { color: var(--c-red); }
.cfg-sr-row {
  display: flex; flex-direction: column; gap: 2px; width: 100%;
  background: none; border: 0; border-top: 1px solid var(--c-line-soft);
  padding: 11px 18px; cursor: pointer; font: inherit; text-align: left;
}
.cfg-sr-row:first-of-type { border-top: 0; }
.cfg-sr-row:hover { background: var(--c-red-soft); }
.cfg-sr-row .sr-lab { display: flex; align-items: center; gap: 8px; font-size: 13.5px; font-weight: 600; color: var(--c-text); }
.cfg-sr-row .sr-kind {
  font-size: 10px; font-weight: 700; letter-spacing: .05em; text-transform: uppercase;
  color: var(--c-text-faint); border: 1px solid var(--c-line); border-radius: 5px; padding: 1px 6px;
}
.cfg-sr-row .sr-hint { font-size: 12px; color: var(--c-text-mute); }
.cfg-sr-row .sr-go { font-size: 12px; font-weight: 600; color: var(--c-red); opacity: 0; }
.cfg-sr-row:hover .sr-go { opacity: 1; }

/* ═══ Flash del campo raggiunto da «Vai al campo» ═══ */
@keyframes cfg-field-flash-kf {
  0%   { box-shadow: 0 0 0 3px rgba(245,158,11,.65); background: #fef3c7; }
  70%  { box-shadow: 0 0 0 3px rgba(245,158,11,.35); background: #fef3c7; }
  100% { box-shadow: 0 0 0 3px rgba(245,158,11,0); background: transparent; }
}
.cfg-field-flash { border-radius: 8px; animation: cfg-field-flash-kf 2.2s ease-out both; }
@media (prefers-reduced-motion: reduce) {
  .cfg-field-flash { animation: none; box-shadow: 0 0 0 3px rgba(245,158,11,.55); background: #fef3c7; }
}
</style>
