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
      <!-- Le 4 aree della shell: la STESSA topbar delle altre pagine Olobuild.
           Il breadcrumb "Configurazione" è sparito: imbrogliava (menu "Sistema",
           atterraggio "Configurazione"); qui l'area attiva resta evidenziata,
           anche per i tab traslocati (Popup → Costruisci, ecc.). -->
      <nav v-if="AREAS.length" class="areas" :aria-label="t('Aree di Olobuild')">
        <a
          v-for="a in AREAS"
          :key="a.id"
          class="area-tab"
          :class="{ 'is-active': a.id === activeAreaId }"
          :href="a.url"
          :aria-current="a.id === activeAreaId ? 'true' : undefined"
          @click="onAreaClick(a, $event)"
        >
          <span class="ic" v-html="a.icon"></span>
          <span class="lbl">{{ a.label }}</span>
        </a>
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

    <!-- ═══ Sub-nav dell'area attiva: la STESSA riga delle altre pagine della
         shell. Da Sistema si vede [Configurazione | Strumenti]; con una scheda
         ospite (es. Popup via ricerca) si vede la sub-nav della sua area, con
         la voce evidenziata — nessun click "cambia menu". ═══ -->
    <nav v-if="areaSubnav.length" class="cfg-area-subnav" :aria-label="t('Sezioni dell\'area')">
      <a
        v-for="(it, i) in areaSubnav"
        :key="'as-' + i"
        :href="it.url"
        :class="{ 'is-active': isAreaSubnavActive(it) }"
        :aria-current="isAreaSubnavActive(it) ? 'page' : undefined"
        @click="onAreaSubnavClick(it, $event)"
      >{{ it.label }}</a>
    </nav>

    <!-- ═══ NAV: 5 aree come macro-schede, le voci dell'area come sotto-schede.
         Gli id delle 17 schede restano invariati (deep-link ?tab= e palette). ═══ -->
    <nav class="cfg-macrobar" role="tablist" :aria-label="t('Aree della configurazione')">
      <button
        v-for="group in IA_GROUPS"
        :key="group.id"
        type="button"
        class="cfg-macro-tab"
        :class="{ 'is-active': activeGroupId === group.id }"
        role="tab"
        :aria-selected="activeGroupId === group.id"
        @click="onPickGroup(group)"
      >
        {{ t(group.title) }}
        <span v-if="groupDirty(group)" class="dirty-dot" :title="t('Modifiche non salvate')"></span>
        <span v-if="searchActive && groupHits(group)" class="hit-n">{{ groupHits(group) }}</span>
      </button>
      <div class="macro-spacer"></div>
      <div class="macro-version"><span class="ok"></span>v{{ version }} · build {{ buildId }}</div>
    </nav>
    <nav class="cfg-subbar" role="tablist" :aria-label="t('Schede dell\'area')">
      <button
        v-for="item in visibleGroupItems"
        :key="item.id"
        type="button"
        class="cfg-sub-tab"
        :class="{ 'is-active': activeTab === item.id, 'is-soon': item.soon }"
        role="tab"
        :aria-selected="activeTab === item.id"
        :title="item.soon ? t('In arrivo nella prossima release') : ''"
        @click="onPick(item)"
      >
        <span class="ic" v-html="iconSvg(item.icon)"></span>
        {{ t(item.label) }}
        <span v-if="dirtyTabs.has(item.id)" class="dirty-dot" :title="t('Modifiche non salvate')"></span>
        <span v-if="searchActive && hitCount(item.id)" class="hit-n">{{ hitCount(item.id) }}</span>
        <span v-if="item.soon" class="pill-soon">Soon</span>
        <span v-else-if="item.badge" class="badge-new">{{ item.badge }}</span>
      </button>
    </nav>

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
      // Traslocata nell'area Costruisci (restyling Fase 3): hidden = fuori
      // dalla navigazione, ma il deep-link ?tab= e la ricerca la aprono ancora;
      // `area` dice quale area della topbar resta evidenziata quando è attiva.
      { id: 'popups',        label: 'Popup globali',         icon: 'window',  component: PopupsTab, hidden: true, area: 'costruisci' },
    ],
  },
  {
    id: 'seoprivacy', title: 'SEO & Privacy', items: [
      { id: 'seo',       label: 'SEO globale',           icon: 'search',     component: SeoTab },
      { id: 'redirects', label: 'Redirect & 404',        icon: 'redirect',   component: RedirectsTab },
      { id: 'cookie',    label: 'Cookie Consent & GDPR', icon: 'key',        component: CookieTab },
      // Traslocata nell'area Raccolta (restyling Fase 3), vedi sopra.
      { id: 'analytics', label: 'Tracking & Analytics',  icon: 'chart',      component: AnalyticsTab, hidden: true, area: 'raccolta' },
    ],
  },
  {
    id: 'prestazioni', title: 'Prestazioni & Servizi', items: [
      { id: 'performance', label: 'Performance & Cache',      icon: 'gauge',    component: PerformanceTab },
      { id: 'maintenance', label: 'Manutenzione & Coming Soon', icon: 'tool',    component: MaintenanceTab },
      { id: 'ai',          label: 'AI Assistant',             icon: 'sparkles', component: AITab,          badge: 'NEW' },
      // Tornata visibile: la voce "Chiavi provider" nell'area Media portava
      // alla console = salto di menu (feedback utente); senza una pagina
      // cockpit dedicata, la sua casa resta la Configurazione.
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
// Ultima scheda aperta per ogni area: {groupId: tabId} in localStorage.
// Cliccando una macro-scheda si riapre dove si era rimasti in quell'area.
const GROUP_LAST_KEY = 'olo_cfg_group_last';

const version = window.oloData?.version || '1.0.0';
const logoUrl = (window.oloData?.pluginUrl || '/wp-content/plugins/olobuild/') + 'assets/img/olobuild-horizontal.png';
const buildId = computed(() => {
  const d = new Date();
  return `${d.getFullYear()}.${String(d.getMonth() + 1).padStart(2, '0')}`;
});

const dashboardUrl = computed(() => (window.oloData?.adminUrl || '') + 'admin.php?page=olobuild');
const docsUrl = 'https://olotheme.com/docs/olobuild/';
const siteUrl = computed(() => window.oloData?.siteUrl || '/');

// Le 4 aree della shell (dal localize PHP, single source = cockpit_areas()).
// La console è la pagina dell'area Sistema; i tab traslocati dichiarano la
// loro area in IA_GROUPS e la topbar la tiene evidenziata.
const AREAS = window.oloData?.areas || [];
const activeAreaId = computed(() => currentItem.value?.area || 'sistema');
function onAreaClick(area, e) {
  // Già qui: non ricaricare la pagina (perderebbe le modifiche non salvate),
  // chiudi solo l'eventuale ricerca aperta.
  if (area.id === activeAreaId.value) {
    e.preventDefault();
    filterQuery.value = '';
  }
}

// Sub-nav dell'area attiva (stessa riga delle pagine cockpit): in area Sistema
// la voce "Configurazione" è questa pagina; una scheda ospite (tab traslocato
// aperto via ricerca/deep-link) evidenzia la sua voce nell'area di provenienza.
const areaSubnav = computed(() => {
  const area = AREAS.find(a => a.id === activeAreaId.value);
  return area?.subnav || [];
});
function isAreaSubnavActive(it) {
  if (it.tab) return it.tab === activeTab.value;
  return activeAreaId.value === 'sistema' && it.url.includes('olobuilder-settings');
}
function onAreaSubnavClick(it, e) {
  if (isAreaSubnavActive(it)) {
    e.preventDefault();
    filterQuery.value = '';
  }
}

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

// ─── Aree (macro-schede) ────────────────────────────────────────────
const activeGroup = computed(() =>
  IA_GROUPS.find(g => g.items.some(i => i.id === activeTab.value)) || IA_GROUPS[0]
);
const activeGroupId = computed(() => activeGroup.value.id);
// Le schede traslocate nelle aree (hidden) non compaiono nella subbar,
// TRANNE quella eventualmente attiva (aperta via deep-link o ricerca):
// si mostra come scheda ospite, così l'utente vede dove si trova.
const visibleGroupItems = computed(() =>
  activeGroup.value.items.filter(i => !i.hidden || i.id === activeTab.value)
);

function groupHits(group) {
  return group.items.reduce((n, i) => n + hitCount(i.id), 0);
}
function groupDirty(group) {
  return group.items.some(i => dirtyTabs.value.has(i.id));
}
function onPickGroup(group) {
  let lastId = null;
  try { lastId = JSON.parse(localStorage.getItem(GROUP_LAST_KEY) || '{}')[group.id]; } catch (e) { /* ignore */ }
  const item = group.items.find(i => i.id === lastId && !i.soon && !i.hidden)
    || group.items.find(i => !i.soon && !i.hidden);
  if (item) onPick(item);
}

const dirtyTabItems = computed(() =>
  ALL_ITEMS.filter(i => dirtyTabs.value.has(i.id))
);

const currentItem    = computed(() => ALL_ITEMS.find(i => i.id === activeTab.value));
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
  try {
    const g = IA_GROUPS.find(gr => gr.items.some(i => i.id === item.id));
    if (g) {
      const map = JSON.parse(localStorage.getItem(GROUP_LAST_KEY) || '{}');
      map[g.id] = item.id;
      localStorage.setItem(GROUP_LAST_KEY, JSON.stringify(map));
    }
  } catch (e) { /* ignore */ }
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

// La console è position:fixed e deve partire dove finisce il menu wp-admin.
// La larghezza vera si MISURA (#adminmenuwrap): 160px è solo il default, il
// menu può essere ripiegato (36), in app mode (52) o allargato da altri
// plugin. ResizeObserver segue fold/unfold e il toggle app mode dal vivo.
function syncWpMenuOffset() {
  const menuEl = document.getElementById('adminmenuwrap');
  if (!menuEl) return;
  const apply = () => {
    const w = window.innerWidth > 782 ? Math.round(menuEl.getBoundingClientRect().width) : 0;
    document.documentElement.style.setProperty('--olo-wpmenu-w', w + 'px');
  };
  apply();
  if (window.ResizeObserver) new ResizeObserver(apply).observe(menuEl);
  window.addEventListener('resize', apply);
}

onMounted(() => {
  syncWpMenuOffset();

  // La topbar ora ha link che lasciano la pagina (aree, Strumenti…): con
  // modifiche non salvate il browser chiede conferma prima di buttarle via.
  window.addEventListener('beforeunload', (e) => {
    if (dirty.value) {
      e.preventDefault();
      e.returnValue = '';
    }
  });

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
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap');

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
  /* Antracite della barra wp-admin, non piu' navy: l'utente non vuole
     superfici blu nelle pagine Olobuild (stessi grigi del menu WordPress). */
  --c-navy:       #1d2327;
  --c-navy-2:     #2c3338;
  --c-navy-3:     #3c434a;
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
  /* Inter come il builder: un'unica famiglia per tutta la shell Olobuild
     (il serif dei titoli è stato ritirato col restyling, --c-display resta
     come token per i punti che lo usano — numeri grandi, heading). */
  --c-sans:    'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
  --c-display: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
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
  grid-template-columns: 1fr;
  /* La riga areasub è auto: collassa a zero se il localize non porta le aree. */
  grid-template-rows: 56px auto 44px 42px 1fr 64px;
  grid-template-areas:
    'topbar'
    'areasub'
    'macrobar'
    'subbar'
    'content'
    'savebar';
  /* FIXED, non height:100vh nel flusso: su siti col menu wp-admin più alto
     della viewport la pagina scrolla comunque, e sotto la console compariva
     una fascia vuota. Fissata al viewport, la console riempie sempre lo
     schermo a destra del menu; lo scroll del body muove solo il menu di
     WordPress (le voci in fondo restano raggiungibili). */
  position: fixed;
  top: var(--wp-admin--admin-bar--height, 32px);
  right: 0;
  bottom: 0;
  /* La larghezza del menu wp-admin NON è affidabile a priori: 160px di
     default, 36 ripiegato, 52 in app mode, ma altri plugin possono
     allargarlo (successo su olotheme.com: console sotto il menu). La var
     è MISURATA dal componente su #adminmenuwrap (ResizeObserver), quindi
     copre anche fold/app-mode senza regole per stato. */
  left: var(--olo-wpmenu-w, 160px);
  background: var(--c-bg);
  overflow: hidden;
  isolation: isolate;
  -webkit-font-smoothing: antialiased;
}
@media (max-width: 782px) {
  /* Sotto i 783px il menu WP è un overlay a scomparsa: la console parte da 0. */
  .cfg-root { left: 0; top: 46px; }
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
/* Le 4 aree in topbar: stesso linguaggio di .olo-area-tab della shell cockpit. */
.cfg-topbar .areas {
  display: flex; gap: 4px; margin-left: 6px;
  overflow-x: auto; scrollbar-width: none;
}
.cfg-topbar .areas::-webkit-scrollbar { display: none; }
.cfg-topbar .area-tab {
  display: inline-flex; align-items: center; gap: 7px;
  font-size: 13px; font-weight: 600; color: var(--c-text-mute);
  border-radius: 8px; padding: 7px 13px;
  text-decoration: none; white-space: nowrap;
  transition: background .12s, color .12s;
}
.cfg-topbar .area-tab:hover { background: var(--c-bg); color: var(--c-text); }
.cfg-topbar .area-tab.is-active { background: var(--c-red-soft); color: var(--c-red); }
.cfg-topbar .area-tab:focus-visible { outline: 2px solid var(--c-red); outline-offset: 2px; }
.cfg-topbar .area-tab .ic { display: flex; }
.cfg-topbar .area-tab .ic svg { width: 15px; height: 15px; }
@media (max-width: 1180px) {
  .cfg-topbar .area-tab .lbl { display: none; }
  .cfg-topbar .area-tab { padding: 7px 10px; }
}

/* ── Sub-nav dell'area (stessa riga di .olo-cockpit-subnav della shell) ── */
.cfg-area-subnav {
  grid-area: areasub;
  height: 42px;
  display: flex; align-items: stretch; gap: 2px;
  padding: 0 20px;
  background: #fff;
  border-bottom: 1px solid var(--c-line);
  overflow-x: auto; scrollbar-width: none;
}
.cfg-area-subnav::-webkit-scrollbar { display: none; }
.cfg-area-subnav a {
  display: inline-flex; align-items: center;
  font-size: 13px; font-weight: 500; color: var(--c-text-mute);
  padding: 0 12px; text-decoration: none;
  border-bottom: 2px solid transparent; margin-bottom: -1px;
  white-space: nowrap;
  transition: color .12s, border-color .12s;
}
.cfg-area-subnav a:hover { color: var(--c-text); }
.cfg-area-subnav a.is-active {
  color: var(--c-red-dark);
  border-bottom-color: var(--c-red);
  font-weight: 600;
}
.cfg-area-subnav a:focus-visible { outline: 2px solid var(--c-red); outline-offset: -2px; border-radius: 6px; }
.cfg-topbar .spacer { flex: 1; }
.cfg-topbar .top-search {
  display: flex; align-items: center; gap: 8px;
  background: var(--c-bg);
  border: 1px solid var(--c-line);
  border-radius: 8px;
  padding: 7px 12px;
  width: 280px; /* come la search-mini del cockpit: topbar uguali ovunque */
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

/* ── MACROBAR: le 5 aree come schede ──────────────────────────────── */
.cfg-macrobar {
  grid-area: macrobar;
  display: flex; align-items: center; gap: 4px;
  padding: 0 20px;
  background: var(--c-navy);
  overflow-x: auto;
  scrollbar-width: none;
}
.cfg-macrobar::-webkit-scrollbar { display: none; }
.cfg-macro-tab {
  display: inline-flex; align-items: center; gap: 7px;
  border: 0; background: none; cursor: pointer;
  font: 600 13px var(--c-sans);
  color: rgba(255,255,255,.72);
  padding: 7px 14px; border-radius: 8px;
  white-space: nowrap;
  transition: background .12s, color .12s;
}
.cfg-macro-tab:hover { background: rgba(255,255,255,.08); color: #fff; }
.cfg-macro-tab.is-active { background: var(--c-red); color: #fff; }
.cfg-macro-tab:focus-visible { outline: 2px solid var(--c-red-soft-2); outline-offset: 2px; }
.cfg-macrobar .macro-spacer { flex: 1; }
.cfg-macrobar .macro-version {
  display: flex; align-items: center; gap: 7px;
  font-family: var(--c-mono); font-size: 11px;
  color: rgba(255,255,255,.45); white-space: nowrap;
}
.cfg-macrobar .macro-version .ok {
  width: 6px; height: 6px; border-radius: 50%;
  background: var(--c-success);
  box-shadow: 0 0 0 3px rgba(21,128,61,.25);
}

/* ── SUBBAR: schede dell'area attiva ──────────────────────────────── */
.cfg-subbar {
  grid-area: subbar;
  display: flex; align-items: stretch; gap: 2px;
  padding: 0 20px;
  background: #fff;
  border-bottom: 1px solid var(--c-line);
  overflow-x: auto;
  scrollbar-width: none;
}
.cfg-subbar::-webkit-scrollbar { display: none; }
.cfg-sub-tab {
  display: inline-flex; align-items: center; gap: 7px;
  border: 0; background: none; cursor: pointer;
  font: 500 13px var(--c-sans);
  color: var(--c-text-mute);
  padding: 0 12px;
  border-bottom: 2px solid transparent;
  margin-bottom: -1px;
  white-space: nowrap;
  transition: color .12s, border-color .12s;
}
.cfg-sub-tab:hover { color: var(--c-text); }
.cfg-sub-tab.is-active { color: var(--c-red-dark); border-bottom-color: var(--c-red); font-weight: 600; }
.cfg-sub-tab:focus-visible { outline: 2px solid var(--c-red); outline-offset: -2px; border-radius: 6px; }
.cfg-sub-tab .ic { display: flex; color: inherit; opacity: .7; }
.cfg-sub-tab .ic svg { width: 14px; height: 14px; }
.cfg-sub-tab.is-active .ic { opacity: 1; }
.cfg-sub-tab.is-soon { color: var(--c-text-faint); cursor: not-allowed; }
.cfg-sub-tab .pill-soon {
  font-size: 9px;
  background: var(--c-bg);
  color: var(--c-text-faint);
  border: 1px solid var(--c-line);
  padding: 2px 6px;
  border-radius: 4px;
  font-weight: 700; letter-spacing: .04em;
  text-transform: uppercase;
}
.cfg-sub-tab .badge-new {
  background: var(--c-red-soft);
  color: var(--c-red-dark);
  font-size: 9px; font-weight: 700;
  padding: 2px 6px;
  border-radius: 4px;
  letter-spacing: .04em;
  text-transform: uppercase;
}

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
/* Mai il blu di wp-admin (forms.css vince sui nostri stili base al focus):
   i campi nudi prendono il ring rosso; quelli dentro i wrapper .cfg-* lo
   lasciano al wrapper (focus-within qui sopra), niente doppio anello. */
.cfg-root input:focus, .cfg-layer input:focus,
.cfg-root textarea:focus, .cfg-layer textarea:focus,
.cfg-root select:focus, .cfg-layer select:focus {
  border-color: var(--c-red);
  box-shadow: 0 0 0 1px var(--c-red);
  outline: 2px solid transparent;
}
.cfg-input input:focus, .cfg-select select:focus, .cfg-textarea textarea:focus,
.cfg-topbar .top-search input:focus {
  border-color: transparent;
  box-shadow: none;
  outline: none;
}
.cfg-topbar .top-search:focus-within {
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

/* ── Responsive (sotto 960: le due barre restano, scorrono in orizzontale) ── */
@media (max-width: 960px) {
  .cfg-topbar .top-search { display: none; }
  .cfg-macrobar { padding: 0 12px; }
  .cfg-subbar { padding: 0 12px; }
  .cfg-macrobar .macro-version { display: none; }
  .cfg-content { padding: 20px; }
  .cfg-row { grid-template-columns: 1fr; gap: 8px; }
  .cfg-savebar { padding: 0 16px; }
}

/* ═══ Stato modifiche per scheda ═══ */
.cfg-macro-tab .dirty-dot,
.cfg-sub-tab .dirty-dot {
  width: 7px; height: 7px; border-radius: 50%;
  background: #f59e0b; flex-shrink: 0;
}
.cfg-macro-tab.is-active .dirty-dot { background: #fde68a; }
.cfg-savebar .meta .dirty-tab-link {
  background: none; border: 0; padding: 0; margin: 0 0 0 6px; cursor: pointer;
  font: inherit; font-weight: 600; color: var(--c-warning);
  text-decoration: underline; text-underline-offset: 3px;
}
.cfg-savebar .meta .dirty-tab-link:hover { color: var(--c-red-dark); }

/* ═══ Ricerca a livello di campo ═══ */
.cfg-macro-tab .hit-n,
.cfg-sub-tab .hit-n {
  flex-shrink: 0;
  font-size: 10.5px; font-weight: 700; line-height: 1;
  background: var(--c-red); color: #fff;
  border-radius: 9px; padding: 3px 7px;
}
.cfg-macro-tab.is-active .hit-n { background: rgba(255,255,255,.25); }
.cfg-sub-tab .hit-n { background: var(--c-red-soft-2); color: var(--c-red-dark); }
.cfg-sr { display: flex; flex-direction: column; gap: 16px; max-width: 860px; }
.cfg-sr-head { display: flex; align-items: baseline; gap: 12px; }
.cfg-sr-head h1 {
  margin: 0; font-family: var(--c-sans); font-weight: 700;
  font-size: 21px; letter-spacing: -0.01em; color: var(--c-navy);
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
