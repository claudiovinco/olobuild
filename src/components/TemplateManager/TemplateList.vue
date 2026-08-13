<template>
  <div class="tpl-cockpit">
    <!-- ═══ Page header ══════════════════════════════════════════════ -->
    <div class="tpl-head">
      <div class="titles">
        <h1>{{ t('Gestione Template') }}</h1>
        <div class="sub">
          <b>{{ templates.length }}</b> {{ t('totali') }}
          <template v-if="activeCount > 0"> · <b>{{ activeCount }}</b> {{ t('attivi') }}</template>
          <template v-if="draftCount > 0"> · <b>{{ draftCount }}</b> {{ t('bozze') }}</template>
        </div>
      </div>
      <div class="spc"></div>
      <button class="btn-sec" :class="{ on: selectMode }" @click="toggleSelectMode" :title="t('Seleziona più template per esportarli come tema')">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
        {{ selectMode ? t('Annulla') : t('Seleziona') }}
      </button>
      <button v-if="!importsDisabled" class="btn-sec" @click="triggerImport" :title="t('Importa un template o un tema da JSON')">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 4v12M7 9l5-5 5 5M5 20h14"/></svg>
        {{ t('Importa') }}
      </button>
      <input v-if="!importsDisabled" ref="importFileRef" type="file" accept=".json" style="display:none" @change="handleImportFile" />
      <div class="split" ref="dropdownRef">
        <button class="btn-pri main" @click.stop="showNewMenu = !showNewMenu">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
          {{ t('Nuovo Template') }}
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-left:6px;opacity:.85"><path d="M6 9l6 6 6-6"/></svg>
        </button>
        <div v-if="showNewMenu" class="tpl-new-menu">
          <div class="grp-head">{{ t('Standard') }}</div>
          <div v-for="opt in standardNewOptions" :key="opt.id" class="item" @click="createNew(opt.type)">
            <span class="ic-box" :style="{ background: opt.gradient }">
              <component :is="opt.iconSvg"/>
            </span>
            {{ opt.label }}
          </div>
          <hr v-if="postTypes.length" />
          <div v-if="postTypes.length" class="grp-head">{{ t('Template Single') }}</div>
          <div v-for="pt in postTypes" :key="pt.value" class="item" @click="createNewSingle(pt.value)">
            <span class="ic-box" :style="{ background: TONE_BG.purple }">
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="3" width="14" height="18" rx="2"/><path d="M9 8h6M9 12h6M9 16h4"/></svg>
            </span>
            Single: {{ pt.label }}
          </div>
        </div>
      </div>
    </div>

    <!-- ═══ Barra selezione (export tema) ════════════════════════════ -->
    <div v-if="selectMode" class="tpl-selbar">
      <span class="cnt">{{ selectedCount }} {{ t('selezionati') }}</span>
      <button class="btn-sec" @click="selectAllFiltered">{{ t('Seleziona tutti') }}</button>
      <div class="spc"></div>
      <button class="btn-pri" :disabled="!selectedCount" @click="openBundleExport">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 4v12M7 9l5-5 5 5M5 20h14"/></svg>
        {{ t('Esporta tema') }}<template v-if="selectedCount"> ({{ selectedCount }})</template>
      </button>
    </div>

    <!-- ═══ Toolbar ══════════════════════════════════════════════════ -->
    <div class="tpl-toolbar">
      <div class="filters">
        <button v-for="t_ in typeFilters" :key="t_.id"
          :class="['chip', { on: activeType === t_.id }]"
          @click="activeType = t_.id"
        >
          <span v-if="t_.dotColor" class="dot" :style="{ background: t_.dotColor }"></span>
          {{ t_.label }}
          <span class="num">{{ t_.count }}</span>
        </button>
      </div>
      <div class="spc"></div>
      <div class="search">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" style="color:var(--ot-text-muted, #64748b)"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
        <input v-model="query" :placeholder="t('Cerca per nome o ID…')" />
        <button v-if="query" class="clear" @click="query = ''" :title="t('Pulisci')">
          <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 6l12 12M18 6L6 18"/></svg>
        </button>
      </div>
      <div class="sort-wrap" ref="sortRef">
        <button class="sort" @click.stop="showSortMenu = !showSortMenu">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M6 12h12M10 18h4"/></svg>
          {{ sortLabel }}
          <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
        </button>
        <div v-if="showSortMenu" class="tpl-sort-menu">
          <button v-for="s in sortOptions" :key="s.id"
            :class="['item', { on: sort === s.id }]"
            @click="setSort(s.id)"
          >{{ s.label }}</button>
        </div>
      </div>
      <div class="view-tog">
        <button :class="{ on: layout === 'grid' }" @click="setLayout('grid')" :title="t('Griglia')">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
        </button>
        <button :class="{ on: layout === 'list' }" @click="setLayout('list')" :title="t('Lista')">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
        </button>
      </div>
    </div>

    <!-- ═══ Body: grid / list / empty / loading ══════════════════════ -->
    <div v-if="loading" class="tpl-loading">
      <div class="loader-spinner"></div>
      <div>{{ t('Caricamento template…') }}</div>
    </div>

    <!-- La lista non ha potuto caricare: NON è uno stato vuoto. Dire "nessun
         template" quando la richiesta è fallita fa credere che i template siano
         stati persi (e invita a ricrearli), mentre di solito sono lì e manca
         solo il permesso: sessione scaduta, o un plugin di ruoli che toglie le
         capability all'amministratore sulle chiamate REST. -->
    <div v-else-if="loadError" class="tpl-empty">
      <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" style="opacity:.35"><path d="M12 9v4M12 17h.01"/><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/></svg>
      <h3>{{ loadError === 'forbidden' ? t('Non hai i permessi per vedere i template') : t('Impossibile caricare i template') }}</h3>
      <p v-if="loadError === 'forbidden'">{{ t('I template non sono stati persi: la richiesta è stata rifiutata. Ricarica la pagina; se il problema resta, verifica di essere ancora connesso come amministratore.') }}</p>
      <p v-else>{{ t('La richiesta al server non è riuscita. Controlla la connessione e riprova.') }}</p>
      <button class="btn-pri" @click="fetchTemplates()">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 4v6h-6M1 20v-6h6"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>
        {{ t('Riprova') }}
      </button>
    </div>

    <div v-else-if="filteredSorted.length === 0" class="tpl-empty">
      <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" style="opacity:.3"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="9" y1="3" x2="9" y2="21"/></svg>
      <h3>{{ query ? t('Nessun template trovato per la ricerca') : (activeType === 'all' ? t('Nessun template ancora') : t('Nessun template di questo tipo')) }}</h3>
      <p>{{ query ? t('Prova a cambiare il termine di ricerca o pulisci il filtro.') : t('Crea il tuo primo template per iniziare.') }}</p>
      <button v-if="!query" class="btn-pri" @click="createNew('page')">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
        {{ t('Crea Template') }}
      </button>
    </div>

    <!-- Grid view -->
    <div v-else-if="layout === 'grid'" class="tpl-grid">
      <div v-for="tpl in filteredSorted" :key="tpl.id" class="tpl-card" :class="{ 'sel-on': selectMode && isSelected(tpl.id) }" @click="onCardClick(tpl)">
        <div v-if="selectMode" class="sel-check" :class="{ on: isSelected(tpl.id) }" @click.stop="toggleSelect(tpl.id)">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
        </div>
        <div class="thumb">
          <img v-if="tpl.thumbnail" :src="tpl.thumbnail" :alt="tpl.title || ''" loading="lazy" class="thumb-img" />
          <TplPreviewShape v-else :kind="previewKindFor(tpl)" :type="tpl.type" />
          <div class="badges">
            <span :class="['badge', 't-' + (tpl.type || 'page')]">{{ typeLabelShort(tpl.type) }}<span v-if="getSinglePostType(tpl)">: {{ getSinglePostType(tpl) }}</span></span>
          </div>
          <div class="badge-r">
            <span v-if="isActive(tpl)" class="badge attivo">{{ t('Attivo') }}</span>
            <span v-if="tpl.status === 'draft'" class="badge draft">{{ t('Bozza') }}</span>
          </div>
          <span v-if="tpl.elements_count > 0 || (tpl.content && tpl.content.length)" class="pv-elements">
            {{ countElements(tpl.content) }} {{ t('elementi') }}
          </span>
          <!-- Hover actions -->
          <div class="actions" @click.stop>
            <button @click="editTemplate(tpl.id)" :title="t('Modifica')">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20l4-1 11-11-3-3L5 16l-1 4z"/></svg>
            </button>
            <button @click="duplicateTemplate(tpl.id)" :title="t('Duplica')">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="11" height="11" rx="2"/><path d="M5 15V5a2 2 0 012-2h10"/></svg>
            </button>
            <button @click="exportTemplate(tpl.id)" :title="t('Esporta')">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 4v12M7 9l5-5 5 5M5 20h14"/></svg>
            </button>
            <button class="danger" @click="deleteTemplate(tpl.id, tpl.title)" :title="t('Elimina')">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M8 6V4a2 2 0 012-2h4a2 2 0 012 2v2M6 6l1 14a2 2 0 002 2h6a2 2 0 002-2l1-14"/></svg>
            </button>
          </div>
        </div>
        <div class="body">
          <div class="title-row">
            <input v-if="renamingId === tpl.id" ref="renameInputRef" v-model="renameDraft"
              @click.stop @keyup.enter="confirmRename(tpl)" @keyup.esc="cancelRename" @blur="confirmRename(tpl)"
              class="title-edit"
            />
            <span v-else class="title" @dblclick.stop="startRename(tpl)" :title="tpl.title || t('Senza titolo')">
              {{ tpl.title || t('Senza titolo') }}
            </span>
          </div>
          <div class="meta">
            <span :class="['dot-status', tpl.status === 'draft' ? 'draft' : '']"></span>
            <span>{{ tpl.status === 'draft' ? t('Bozza') : t('Pubblicato') }}</span>
            <span class="sep">·</span>
            <span>{{ formatDate(tpl.updated_at) }}</span>
            <span class="sep">·</span>
            <span>ID {{ tpl.id }}</span>
          </div>
          <button class="shortcode" @click.stop="copyShortcode(tpl.id)" :title="t('Clicca per copiare')">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="11" height="11" rx="2"/><path d="M5 15V5a2 2 0 012-2h10"/></svg>
            [olo_template id=&quot;{{ tpl.id }}&quot;]
          </button>
        </div>
      </div>
    </div>

    <!-- List view -->
    <div v-else class="tpl-list">
      <div class="row h">
        <span></span>
        <span>{{ t('Template') }}</span>
        <span>{{ t('Tipo') }}</span>
        <span>{{ t('Stato') }}</span>
        <span>{{ t('Modificato') }}</span>
        <span></span>
      </div>
      <div v-for="tpl in filteredSorted" :key="tpl.id" class="row" :class="{ 'sel-on': selectMode && isSelected(tpl.id) }" @click="onCardClick(tpl)">
        <div class="mini-thumb" :style="miniThumbStyle(tpl)">
          <img v-if="tpl.thumbnail" :src="tpl.thumbnail" alt="" loading="lazy" />
          <span v-if="selectMode" class="sel-check list" :class="{ on: isSelected(tpl.id) }" @click.stop="toggleSelect(tpl.id)">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
          </span>
        </div>
        <div class="ttl-cell">
          <div class="ttl">{{ tpl.title || t('Senza titolo') }}</div>
          <div class="sub-line">[olo_template id=&quot;{{ tpl.id }}&quot;]</div>
        </div>
        <span :class="['badge', 't-' + (tpl.type || 'page')]">{{ typeLabelShort(tpl.type) }}</span>
        <div class="status-cell">
          <span :class="['dot-status', tpl.status === 'draft' ? 'draft' : '']"></span>
          {{ tpl.status === 'draft' ? t('Bozza') : t('Pubblicato') }}
          <span v-if="isActive(tpl)" class="active-pill">{{ t('Attivo') }}</span>
        </div>
        <div class="date-cell">{{ formatDate(tpl.updated_at) }} · {{ countElements(tpl.content) }} el.</div>
        <div class="acts" @click.stop>
          <button @click="editTemplate(tpl.id)" :title="t('Modifica')">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20l4-1 11-11-3-3L5 16l-1 4z"/></svg>
          </button>
          <button @click="duplicateTemplate(tpl.id)" :title="t('Duplica')">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="11" height="11" rx="2"/><path d="M5 15V5a2 2 0 012-2h10"/></svg>
          </button>
          <button class="danger" @click="deleteTemplate(tpl.id, tpl.title)" :title="t('Elimina')">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M8 6V4a2 2 0 012-2h4a2 2 0 012 2v2M6 6l1 14a2 2 0 002 2h6a2 2 0 002-2l1-14"/></svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Toast micro -->
    <Teleport to="body">
      <div v-if="toastMsg" class="tpl-toast">{{ toastMsg }}</div>
    </Teleport>

    <!-- Export dialog -->
    <Teleport to="body">
      <div v-if="exportDialogVisible" class="tpl-export-overlay" @click.self="exportDialogVisible = false">
        <div class="tpl-export-dialog">
          <h3>{{ t('Esporta template') }}</h3>
          <label>
            <input type="checkbox" v-model="exportIncludeMedia" />
            <span>{{ t('Includi media (immagini, video, PDF)') }}</span>
          </label>
          <p>{{ t('Attiva questa opzione se devi trasferire il template su un altro sito WordPress.') }}</p>
          <div class="actions-row">
            <button @click="exportDialogVisible = false" class="btn-sec">{{ t('Annulla') }}</button>
            <button @click="doExport" :disabled="exportLoading" class="btn-pri">
              {{ exportLoading ? t('Esportazione…') : t('Esporta') }}
            </button>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- Esporta tema (bundle) -->
    <Teleport to="body">
      <div v-if="bundleDialogVisible" class="tpl-export-overlay" @click.self="bundleDialogVisible = false">
        <div class="tpl-export-dialog">
          <h3>{{ t('Esporta tema') }}</h3>
          <p>{{ selectedCount }} {{ t('template verranno inclusi nel file del tema (re-importabile).') }}</p>
          <label class="fld"><span>{{ t('Nome tema') }}</span>
            <input type="text" v-model="bundleName" :placeholder="t('Tema Olobuild')" /></label>
          <label class="fld"><span>{{ t('Descrizione (opzionale)') }}</span>
            <input type="text" v-model="bundleDesc" /></label>
          <div class="actions-row">
            <button @click="bundleDialogVisible = false" class="btn-sec">{{ t('Annulla') }}</button>
            <button @click="doBundleExport" :disabled="bundleLoading" class="btn-pri">{{ bundleLoading ? t('Esportazione…') : t('Esporta .json') }}</button>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import { t } from '@/i18n';
import { ref, computed, onMounted, onUnmounted, nextTick, h } from 'vue';

const emit = defineEmits(['edit', 'create']);

const oloData = window.oloData || {};
const postTypes = oloData.postTypes || [];

/* ─── State ────────────────────────────────────────────────────────── */
const loading = ref(true);
/* null = tutto bene · 'forbidden' = 401/403 · 'failed' = rete o server */
const loadError = ref(null);
const templates = ref([]);
const byType = ref({});
const activeHeaderId = ref(parseInt(oloData.activeHeaderId) || 0);
const activeFooterId = ref(parseInt(oloData.activeFooterId) || 0);
const active404Id = ref(parseInt(oloData.active404Id) || 0);
const activeSingles = ref({ ...(oloData.activeSingles || {}) });

const activeType = ref('all');
const query = ref('');
const sort = ref('newest');
const layout = ref(localStorage.getItem('olo_tpl_layout') || 'grid');
const showSortMenu = ref(false);
const showNewMenu = ref(false);
const dropdownRef = ref(null);
const sortRef = ref(null);
const renamingId = ref(null);
const renameDraft = ref('');
const renameInputRef = ref(null);
const importFileRef = ref(null);
const importsDisabled = !!(window.oloData && window.oloData.importsDisabled);
const toastMsg = ref('');

/* ─── Selezione multipla + export "tema" (bundle di template) ──────── */
const selectMode = ref(false);
const selectedIds = ref([]);
const selectedCount = computed(() => selectedIds.value.length);
function isSelected(id) { return selectedIds.value.includes(id); }
function toggleSelect(id) {
  const i = selectedIds.value.indexOf(id);
  if (i === -1) selectedIds.value.push(id); else selectedIds.value.splice(i, 1);
}
function exitSelectMode() { selectMode.value = false; selectedIds.value = []; }
function toggleSelectMode() { selectMode.value ? exitSelectMode() : (selectMode.value = true); }
function onCardClick(tpl) { if (selectMode.value) toggleSelect(tpl.id); else editTemplate(tpl.id); }
function selectAllFiltered() { selectedIds.value = filteredSorted.value.map(x => x.id); }

const bundleDialogVisible = ref(false);
const bundleName = ref('Tema Olobuild');
const bundleDesc = ref('');
const bundleLoading = ref(false);
function openBundleExport() {
  if (!selectedCount.value) return;
  bundleName.value = 'Tema Olobuild';
  bundleDesc.value = '';
  bundleDialogVisible.value = true;
}
async function doBundleExport() {
  bundleLoading.value = true;
  try {
    const res = await fetch(`${oloData.restUrl}/templates/export-bundle`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': oloData.nonce },
      body: JSON.stringify({ ids: selectedIds.value, name: bundleName.value, description: bundleDesc.value }),
    });
    if (!res.ok) throw new Error('Export bundle failed');
    const data = await res.json();
    const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `tema-${(data.name || 'olobuild').replace(/[^a-z0-9]/gi, '-').toLowerCase()}.json`;
    a.click();
    URL.revokeObjectURL(url);
    bundleDialogVisible.value = false;
    exitSelectMode();
    showToast(t('Tema esportato'));
  } catch (err) {
    console.error(err);
    alert(t('Errore durante l\'esportazione del tema.'));
  } finally {
    bundleLoading.value = false;
  }
}

/* ─── Sort options ─────────────────────────────────────────────────── */
const sortOptions = [
  { id: 'newest', label: t('Più recenti') },
  { id: 'oldest', label: t('Più vecchi') },
  { id: 'az',     label: 'A → Z' },
  { id: 'za',     label: 'Z → A' },
  { id: 'used',   label: t('Più usati') },
];
const sortLabel = computed(() => sortOptions.find(s => s.id === sort.value)?.label || '');

function setSort(id) { sort.value = id; showSortMenu.value = false; }
function setLayout(l) { layout.value = l; localStorage.setItem('olo_tpl_layout', l); }

/* ─── Tone gradients (per icon-box dropdown "Nuovo") ────────────────── */
const TONE_BG = {
  primary: 'linear-gradient(135deg,#4a8c2a,#3fa23f)',
  blue:    'linear-gradient(135deg,#3b82f6,#1d4ed8)',
  slate:   'linear-gradient(135deg,#475569,#1e293b)',
  purple:  'linear-gradient(135deg,#a855f7,#7e22ce)',
  amber:   'linear-gradient(135deg,#f59e0b,#d97706)',
  violet:  'linear-gradient(135deg,#8b5cf6,#5b21b6)',
  red:     'linear-gradient(135deg,#ef4444,#b91c1c)',
};

/* ─── Type filters ─────────────────────────────────────────────────── */
const TYPE_META = {
  page:      { label: t('Pagina'),     short: 'PAGINA',     dot: '#4a8c2a' },
  header:    { label: t('Header'),     short: 'HEADER',     dot: '#3b82f6' },
  footer:    { label: t('Footer'),     short: 'FOOTER',     dot: '#64748b' },
  single:    { label: t('Single'),     short: 'SINGLE',     dot: '#a855f7' },
  megapanel: { label: t('Mega Panel'), short: 'MEGA',       dot: '#f59e0b' },
  widget:    { label: t('Widget'),     short: 'WIDGET',     dot: '#8b5cf6' },
  '404':     { label: '404',           short: '404',        dot: '#ef4444' },
};

const typeFilters = computed(() => {
  const list = [
    { id: 'all', label: t('Tutti'), count: byType.value.all || templates.value.length, dotColor: '' },
  ];
  for (const k of ['page', 'header', 'footer', 'single', 'megapanel', 'widget', '404']) {
    const c = byType.value[k] || 0;
    if (c === 0 && activeType.value !== k) continue;
    list.push({
      id: k,
      label: TYPE_META[k].label,
      count: c,
      dotColor: TYPE_META[k].dot,
    });
  }
  return list;
});

/* ─── Filtering + sorting ──────────────────────────────────────────── */
const filteredSorted = computed(() => {
  let arr = templates.value.slice();
  if (activeType.value !== 'all') arr = arr.filter(x => (x.type || 'page') === activeType.value);
  const q = query.value.trim().toLowerCase();
  if (q) {
    arr = arr.filter(x =>
      (x.title || '').toLowerCase().includes(q) ||
      String(x.id).includes(q)
    );
  }
  switch (sort.value) {
    case 'oldest': arr.sort((a,b) => +new Date(a.updated_at||0) - +new Date(b.updated_at||0)); break;
    case 'az':     arr.sort((a,b) => (a.title||'').localeCompare(b.title||'')); break;
    case 'za':     arr.sort((a,b) => (b.title||'').localeCompare(a.title||'')); break;
    case 'used':   arr.sort((a,b) => (b.instances||0) - (a.instances||0)); break;
    case 'newest':
    default:       arr.sort((a,b) => +new Date(b.updated_at||0) - +new Date(a.updated_at||0));
  }
  return arr;
});

const activeCount = computed(() =>
  templates.value.filter(t_ => isActive(t_)).length
);
const draftCount = computed(() =>
  templates.value.filter(t_ => t_.status === 'draft').length
);

/* ─── Standard new options for dropdown ────────────────────────────── */
const standardNewOptions = [
  { id: 'new-page',   type: 'page',      label: t('Nuova Pagina'),     iconSvg: () => h('svg', svgAttr(13), [h('path', { d: 'M5 3v18h14V3z' }), h('path', { d: 'M9 8h6M9 12h6M9 16h4' })]),                                  gradient: TONE_BG.primary },
  { id: 'new-header', type: 'header',    label: t('Nuovo Header'),     iconSvg: () => h('svg', svgAttr(13), [h('rect', { x: 3, y: 4, width: 18, height: 6, rx: 1 }), h('rect', { x: 3, y: 14, width: 8, height: 2 }), h('rect', { x: 13, y: 14, width: 8, height: 2 })]), gradient: TONE_BG.blue },
  { id: 'new-footer', type: 'footer',    label: t('Nuovo Footer'),     iconSvg: () => h('svg', svgAttr(13), [h('rect', { x: 3, y: 14, width: 18, height: 6, rx: 1 }), h('rect', { x: 3, y: 4, width: 8, height: 2 }), h('rect', { x: 13, y: 4, width: 8, height: 2 })]), gradient: TONE_BG.slate },
  { id: 'new-mega',   type: 'megapanel', label: t('Nuovo Mega Panel'), iconSvg: () => h('svg', svgAttr(13), [h('rect', { x: 3, y: 3, width: 8, height: 8, rx: 1 }), h('rect', { x: 13, y: 3, width: 8, height: 8, rx: 1 }), h('rect', { x: 3, y: 13, width: 8, height: 8, rx: 1 }), h('rect', { x: 13, y: 13, width: 8, height: 8, rx: 1 })]), gradient: TONE_BG.amber },
  { id: 'new-widget', type: 'widget',    label: t('Nuovo Widget'),     iconSvg: () => h('svg', svgAttr(13), [h('rect', { x: 5, y: 5, width: 14, height: 14, rx: 2 }), h('circle', { cx: 12, cy: 12, r: 3 })]),                gradient: TONE_BG.violet },
  { id: 'new-404',    type: '404',       label: t('Nuova 404'),        iconSvg: () => h('svg', svgAttr(13), [h('path', { d: 'M12 4l9 16H3z' }), h('path', { d: 'M12 11v4M12 18h.01' })]),                              gradient: TONE_BG.red },
];

function svgAttr(size) {
  return { width: size, height: size, viewBox: '0 0 24 24', fill: 'none', stroke: 'currentColor', 'stroke-width': '1.7', 'stroke-linecap': 'round', 'stroke-linejoin': 'round' };
}

/* ─── Preview shape kind (when no thumbnail) ───────────────────────── */
function previewKindFor(tpl) {
  const type = tpl.type || 'page';
  const cnt = countElements(tpl.content);
  if (cnt === 0) return 'empty';
  if (type === 'header') return 'header';
  if (type === 'footer') return 'footer';
  if (type === 'widget') return 'widget';
  if (type === 'megapanel') return 'grid';
  if (cnt > 12) return 'long';
  if (cnt > 4)  return 'hero+grid';
  return 'split';
}

/* ─── Click-outside ─────────────────────────────────────────────────── */
function handleClickOutside(e) {
  if (dropdownRef.value && !dropdownRef.value.contains(e.target)) showNewMenu.value = false;
  if (sortRef.value && !sortRef.value.contains(e.target)) showSortMenu.value = false;
}

/* ─── Data fetch ────────────────────────────────────────────────────── */
async function fetchTemplates() {
  loading.value = true;
  loadError.value = null;
  try {
    const res = await fetch(`${oloData.restUrl}/templates?per_page=200`, {
      headers: { 'X-WP-Nonce': oloData.nonce },
    });
    if (!res.ok) {
      // 401/403 = la richiesta è stata rifiutata, non "zero template": va
      // distinto, altrimenti la schermata vuota fa pensare a dati persi.
      throw new Error(res.status === 401 || res.status === 403 ? 'forbidden' : 'failed');
    }
    const data = await res.json();
    templates.value = data.items || [];
    byType.value = data.byType || {};
    healMissingThumbs();
  } catch (err) {
    console.error('fetchTemplates:', err);
    loadError.value = err && err.message === 'forbidden' ? 'forbidden' : 'failed';
    templates.value = [];
  } finally {
    loading.value = false;
  }
}

/* ─── Thumbnail auto-heal ───────────────────────────────────────────── */
/* I template creati senza passare dal builder (import tema, import file,
   API) non hanno thumbnail: genera in background quelle mancanti via
   olo-thumb-capture.js (render REST → cattura). Cap per visita per non
   tenere la coda occupata troppo a lungo su siti con molti template. */
const THUMB_HEAL_CAP = 30;
let thumbHealStarted = false;
function healMissingThumbs() {
  if (thumbHealStarted || typeof window.oloGenerateMissingThumbs !== 'function') return;
  const ids = templates.value.filter(t_ => !t_.thumbnail).map(t_ => t_.id).slice(0, THUMB_HEAL_CAP);
  if (!ids.length) return;
  thumbHealStarted = true;
  window.oloGenerateMissingThumbs(ids);
}

/* Card live update: l'upload di ogni thumbnail emette questo evento */
function onThumbUpdated(e) {
  const { templateId, url } = e.detail || {};
  if (!templateId || !url) return;
  const tpl = templates.value.find(t_ => t_.id === templateId);
  if (tpl) tpl.thumbnail = url;
}

/* ─── Actions ───────────────────────────────────────────────────────── */
function showToast(msg) {
  toastMsg.value = msg;
  setTimeout(() => { toastMsg.value = ''; }, 2200);
}

function createNew(type) {
  showNewMenu.value = false;
  emit('create', type);
}
function createNewSingle(postType) {
  showNewMenu.value = false;
  emit('create', { type: 'single', postType });
}

function editTemplate(id) { emit('edit', id); }

async function duplicateTemplate(id) {
  try {
    const res = await fetch(`${oloData.restUrl}/templates/${id}/duplicate`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': oloData.nonce },
    });
    if (!res.ok) throw new Error('Duplicate failed');
    showToast(t('Template duplicato'));
    await fetchTemplates();
  } catch (err) {
    console.error('duplicate:', err);
    showToast(t('Errore duplicazione'));
  }
}

async function deleteTemplate(id, title) {
  if (!confirm(t('Eliminare') + ' "' + (title || t('Senza titolo')) + '"? ' + t('Questa azione non può essere annullata.'))) return;
  try {
    const res = await fetch(`${oloData.restUrl}/templates/${id}`, {
      method: 'DELETE',
      headers: { 'X-WP-Nonce': oloData.nonce },
    });
    if (res.ok) {
      templates.value = templates.value.filter(x => x.id !== id);
      showToast(t('Template eliminato'));
    }
  } catch (err) { console.error(err); }
}

function startRename(tpl) {
  renameDraft.value = tpl.title || '';
  renamingId.value = tpl.id;
  nextTick(() => {
    const el = renameInputRef.value;
    const node = Array.isArray(el) ? el[0] : el;
    node?.focus(); node?.select();
  });
}
async function confirmRename(tpl) {
  if (renamingId.value !== tpl.id) return;
  renamingId.value = null;
  const newTitle = renameDraft.value.trim();
  if (!newTitle || newTitle === tpl.title) return;
  try {
    const res = await fetch(`${oloData.restUrl}/templates/${tpl.id}`, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': oloData.nonce },
      body: JSON.stringify({ title: newTitle }),
    });
    if (res.ok) {
      tpl.title = newTitle;
      showToast(t('Rinominato'));
    }
  } catch (err) { console.error(err); }
}
function cancelRename() { renamingId.value = null; }

async function copyShortcode(id) {
  const code = `[olo_template id="${id}"]`;
  try {
    await navigator.clipboard.writeText(code);
    showToast(t('Shortcode copiato'));
  } catch (err) {
    // Fallback
    const ta = document.createElement('textarea');
    ta.value = code; document.body.appendChild(ta);
    ta.select(); document.execCommand('copy'); document.body.removeChild(ta);
    showToast(t('Shortcode copiato'));
  }
}

/* ─── Active flags (header/footer/single/404) ─────────────────────── */
function getSinglePostType(tpl) {
  return tpl.settings?.single_post_type || '';
}
function isActive(tpl) {
  if (tpl.type === 'header' && tpl.id === activeHeaderId.value) return true;
  if (tpl.type === 'footer' && tpl.id === activeFooterId.value) return true;
  if (tpl.type === '404'    && tpl.id === active404Id.value) return true;
  if (tpl.type === 'single') {
    const pt = getSinglePostType(tpl);
    return pt && activeSingles.value[pt] === tpl.id;
  }
  return false;
}

/* ─── Export ───────────────────────────────────────────────────────── */
const exportDialogVisible = ref(false);
const exportDialogId = ref(null);
const exportIncludeMedia = ref(true);
const exportLoading = ref(false);

function exportTemplate(id) {
  exportDialogId.value = id;
  exportIncludeMedia.value = true;
  exportDialogVisible.value = true;
}
async function doExport() {
  const id = exportDialogId.value;
  exportLoading.value = true;
  try {
    const endpoint = exportIncludeMedia.value
      ? `${oloData.restUrl}/export-template/${id}?include_media=1`
      : `${oloData.restUrl}/templates/${id}/export`;
    const res = await fetch(endpoint, { headers: { 'X-WP-Nonce': oloData.nonce } });
    if (!res.ok) throw new Error('Export failed');
    const data = await res.json();
    const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `template-${(data.title || data.name || 'export').replace(/[^a-z0-9]/gi, '-').toLowerCase()}.json`;
    a.click();
    URL.revokeObjectURL(url);
    exportDialogVisible.value = false;
    showToast(t('Esportato'));
  } catch (err) {
    console.error(err);
    alert(t('Errore durante l\'esportazione del template.'));
  } finally {
    exportLoading.value = false;
  }
}

/* ─── Import ───────────────────────────────────────────────────────── */
function triggerImport() { importFileRef.value?.click(); }
async function handleImportFile(e) {
  const file = e.target.files?.[0];
  if (!file) return;
  e.target.value = '';
  try {
    const text = await file.text();
    const json = JSON.parse(text);
    const isOld = json.olo_export === 'template';
    const isNew = json.format === 'olobuild-template';
    const isBundle = json.olo_export === 'theme-bundle';
    if (!isOld && !isNew && !isBundle) {
      alert(t('File non valido: non è un export Olobuild (template o tema).'));
      return;
    }
    const endpoint = isBundle
      ? `${oloData.restUrl}/templates/import-bundle`
      : (isNew ? `${oloData.restUrl}/import-template` : `${oloData.restUrl}/templates/import`);
    const res = await fetch(endpoint, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': oloData.nonce },
      body: JSON.stringify(json),
    });
    if (!res.ok) {
      const err = await res.json();
      throw new Error(err.message || 'Import failed');
    }
    const result = await res.json().catch(() => ({}));
    await fetchTemplates();
    showToast(isBundle ? (t('Tema importato') + ': ' + (result.imported || 0) + ' template') : t('Importato'));
  } catch (err) {
    console.error(err);
    alert(t('Errore importazione') + ': ' + err.message);
  }
}

/* ─── Helpers ──────────────────────────────────────────────────────── */
function countElements(content) {
  if (!Array.isArray(content)) return 0;
  let count = 0;
  for (const node of content) {
    count++;
    if (Array.isArray(node.children)) count += countElements(node.children);
  }
  return count;
}
function typeLabelShort(type) {
  return TYPE_META[type]?.short || (type || '').toUpperCase();
}
function formatDate(dateStr) {
  if (!dateStr) return '';
  const d = new Date(dateStr);
  return d.toLocaleDateString('it-IT', { day: '2-digit', month: 'short', year: 'numeric' });
}
function miniThumbStyle(tpl) {
  if (tpl.thumbnail) return {};
  const meta = TYPE_META[tpl.type] || TYPE_META.page;
  return { background: `linear-gradient(135deg, ${meta.dot}33, ${meta.dot}11)` };
}

/* ─── Lifecycle ────────────────────────────────────────────────────── */
onMounted(() => {
  fetchTemplates();
  document.addEventListener('click', handleClickOutside);
  window.addEventListener('olobuild:thumbnail-updated', onThumbUpdated);
});
onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
  window.removeEventListener('olobuild:thumbnail-updated', onThumbUpdated);
});

/* ─── Preview shape sub-component ──────────────────────────────────── */
import TplPreviewShape from './TplPreviewShape.vue';
</script>

<style scoped>
/* ═══════════════════════════════════════════════════════════════════
   Olobuild — Templates cockpit (v3.38)
   ═══════════════════════════════════════════════════════════════════ */
.tpl-cockpit {
  --ot-primary:        #4a8c2a;
  --ot-primary-bright: #3fa23f;
  --ot-primary-dark:   #2d722d;
  --ot-primary-50:     #f0f7ec;
  --ot-primary-100:    #dcefd2;
  --ot-text:           #1e293b;
  --ot-text-muted:     #64748b;
  --ot-text-light:     #94a3b8;
  --ot-border:         #e2e8f0;
  --ot-border-light:   #f1f5f9;
  --ot-bg-muted:       #f1f5f9;
  --ot-bg-soft:        #f9fafb;
  --ot-shadow-xs:      0 1px 2px rgba(16,24,40,.05);
  --ot-shadow-sm:      0 1px 2px rgba(0,0,0,.04);
  font-family: 'Work Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
  color: var(--ot-text);
  display: flex; flex-direction: column; gap: 16px;
  min-width: 0;
  /* No padding — è il container .olo-cockpit-main a gestirlo */
}
.tpl-cockpit *,
.tpl-cockpit *::before,
.tpl-cockpit *::after { box-sizing: border-box; }

/* ── Page header ─────────────────────────────────────────────────── */
.tpl-head {
  display: flex; align-items: flex-end; gap: 12px;
  padding-bottom: 4px;
}
.tpl-head .titles { display: flex; flex-direction: column; gap: 4px; }
.tpl-head h1 {
  font-size: 22px !important; font-weight: 700; margin: 0; padding: 0;
  color: var(--ot-text); letter-spacing: -0.01em; line-height: 1.2;
}
.tpl-head .sub { font-size: 13px; color: var(--ot-text-muted); }
.tpl-head .sub b { color: var(--ot-text); font-weight: 600; }
.tpl-head .spc { flex: 1; }
.tpl-head .btn-sec, .tpl-head .btn-pri {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 8px 14px; border-radius: 8px;
  font: inherit; font-size: 13px; font-weight: 600;
  cursor: pointer; border: 1px solid transparent;
  transition: all .15s; line-height: 1;
}
.tpl-head .btn-sec {
  background: #fff; border-color: var(--ot-border);
  color: var(--ot-text);
}
.tpl-head .btn-sec:hover { border-color: var(--ot-text-muted); background: var(--ot-bg-muted); }
.tpl-head .btn-pri {
  background: var(--ot-primary); color: #fff;
}
.tpl-head .btn-pri:hover { background: var(--ot-primary-dark); }
.tpl-head .split {
  display: inline-flex; align-items: stretch;
  border-radius: 8px;
  position: relative;
}
.tpl-head .split .main { border-radius: 8px; padding-right: 16px; }

/* ── New template menu ───────────────────────────────────────────── */
.tpl-new-menu {
  position: absolute; top: calc(100% + 6px); right: 0;
  width: 240px; background: #fff;
  border: 1px solid var(--ot-border-light);
  border-radius: 10px;
  box-shadow: 0 12px 40px rgba(15,17,21,.15), 0 2px 6px rgba(15,17,21,.06);
  padding: 6px; z-index: 50;
  animation: tplFade .12s ease-out;
}
@keyframes tplFade {
  from { opacity: 0; transform: translateY(-4px); }
  to   { opacity: 1; transform: none; }
}
.tpl-new-menu .grp-head {
  font-size: 10px; font-weight: 700;
  color: var(--ot-text-muted);
  letter-spacing: .06em; text-transform: uppercase;
  padding: 8px 10px 4px;
}
.tpl-new-menu .item {
  display: flex; align-items: center; gap: 10px;
  padding: 7px 10px; border-radius: 6px;
  cursor: pointer; font-size: 13px;
  color: var(--ot-text);
  transition: background .12s;
}
.tpl-new-menu .item:hover { background: var(--ot-bg-muted); }
.tpl-new-menu .item .ic-box {
  width: 26px; height: 26px; border-radius: 6px;
  display: grid; place-items: center;
  color: #fff; flex-shrink: 0;
}
.tpl-new-menu hr {
  border: 0; border-top: 1px solid var(--ot-border-light);
  margin: 4px 6px;
}

/* ── Toolbar ─────────────────────────────────────────────────────── */
.tpl-toolbar {
  display: flex; align-items: center; gap: 10px;
  flex-wrap: wrap;
  background: #fff;
  border: 1px solid var(--ot-border-light);
  border-radius: 10px;
  padding: 8px 10px;
  box-shadow: var(--ot-shadow-xs);
  flex-shrink: 0;
}
.tpl-toolbar .filters {
  display: inline-flex; align-items: center; gap: 2px;
  flex-wrap: wrap;
}
.tpl-toolbar .chip {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 5px 11px; border-radius: 7px;
  font-size: 12px; font-weight: 500;
  color: var(--ot-text-muted);
  background: transparent; border: 0;
  cursor: pointer;
  transition: all .12s;
  font-family: inherit;
}
.tpl-toolbar .chip:hover {
  background: var(--ot-bg-muted);
  color: var(--ot-text);
}
.tpl-toolbar .chip.on {
  background: var(--ot-text); color: #fff;
  font-weight: 600;
}
.tpl-toolbar .chip .num {
  font-size: 10px; font-weight: 600;
  padding: 1px 5px; border-radius: 99px;
  background: rgba(0,0,0,.05);
  color: inherit; opacity: .7;
}
.tpl-toolbar .chip.on .num {
  background: rgba(255,255,255,.18);
  color: #fff; opacity: 1;
}
.tpl-toolbar .chip .dot {
  width: 6px; height: 6px; border-radius: 99px;
}
.tpl-toolbar .spc { flex: 1; }
.tpl-toolbar .search {
  display: flex; align-items: center; gap: 8px;
  padding: 5px 10px; border-radius: 7px;
  background: var(--ot-bg-muted);
  border: 1px solid transparent;
  width: 220px;
  transition: all .15s;
}
.tpl-toolbar .search:focus-within {
  background: #fff; border-color: var(--ot-primary);
}
.tpl-toolbar .search input {
  flex: 1; border: 0; outline: 0; background: transparent;
  font: inherit; font-size: 12px;
  padding: 0; box-shadow: none !important;
}
.tpl-toolbar .search input:focus { outline: 0; box-shadow: none !important; }
.tpl-toolbar .search .clear {
  background: none; border: 0; cursor: pointer;
  color: var(--ot-text-muted);
  padding: 2px;
  display: inline-flex;
}
.tpl-toolbar .search .clear:hover { color: var(--ot-text); }
.tpl-toolbar .sort-wrap { position: relative; }
.tpl-toolbar .sort {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 5px 10px; border-radius: 7px;
  background: transparent; border: 0;
  font: inherit; font-size: 12px;
  color: var(--ot-text-muted); cursor: pointer;
}
.tpl-toolbar .sort:hover { background: var(--ot-bg-muted); color: var(--ot-text); }
.tpl-sort-menu {
  position: absolute; right: 0; top: calc(100% + 4px);
  background: #fff; border: 1px solid var(--ot-border-light);
  border-radius: 8px; padding: 4px; z-index: 40;
  box-shadow: 0 8px 24px rgba(0,0,0,.10);
  min-width: 160px;
  display: flex; flex-direction: column;
}
.tpl-sort-menu .item {
  text-align: left; padding: 7px 10px; border-radius: 5px;
  background: transparent; border: 0; cursor: pointer;
  font: inherit; font-size: 12px; color: var(--ot-text);
}
.tpl-sort-menu .item:hover { background: var(--ot-bg-muted); }
.tpl-sort-menu .item.on { background: var(--ot-primary-50); color: var(--ot-primary-dark); font-weight: 600; }
.tpl-toolbar .view-tog {
  display: inline-flex; align-items: stretch;
  background: var(--ot-bg-muted);
  border-radius: 7px; padding: 2px;
}
.tpl-toolbar .view-tog button {
  width: 26px; height: 26px;
  display: grid; place-items: center;
  background: transparent; border: 0; cursor: pointer;
  color: var(--ot-text-muted);
  border-radius: 5px;
  transition: all .12s;
  padding: 0;
}
.tpl-toolbar .view-tog button.on {
  background: #fff; color: var(--ot-text);
  box-shadow: 0 1px 2px rgba(0,0,0,.06);
}

/* ── Loading / empty ─────────────────────────────────────────────── */
.tpl-loading, .tpl-empty {
  padding: 60px 20px; text-align: center;
  color: var(--ot-text-muted);
  display: flex; flex-direction: column; align-items: center; gap: 10px;
}
.tpl-empty h3 { font-size: 15px; font-weight: 600; color: var(--ot-text); margin: 8px 0 0; }
.tpl-empty p  { font-size: 13px; margin: 0 0 14px; }
.tpl-empty .btn-pri {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 8px 14px; border-radius: 8px;
  background: var(--ot-primary); color: #fff;
  border: 0; font: inherit; font-size: 13px; font-weight: 600;
  cursor: pointer;
}
.loader-spinner {
  width: 28px; height: 28px;
  border: 3px solid var(--ot-border-light);
  border-top-color: var(--ot-primary);
  border-radius: 50%;
  animation: spin .7s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

/* ── Grid ────────────────────────────────────────────────────────── */
.tpl-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 14px;
}

/* ── Card ────────────────────────────────────────────────────────── */
.tpl-card {
  background: #fff;
  border: 1px solid var(--ot-border-light);
  border-radius: 12px;
  overflow: hidden;
  display: flex; flex-direction: column;
  transition: all .15s;
  cursor: pointer;
  position: relative;
}
.tpl-card:hover {
  border-color: var(--ot-primary);
  box-shadow: 0 6px 20px rgba(74,140,42,.10), 0 1px 3px rgba(0,0,0,.04);
  transform: translateY(-2px);
}
.tpl-card .thumb {
  position: relative;
  aspect-ratio: 16 / 10;
  overflow: hidden;
  border-bottom: 1px solid var(--ot-border-light);
  background: var(--ot-bg-soft);
}
.tpl-card .thumb-img {
  position: absolute; inset: 0;
  width: 100%; height: 100%;
  object-fit: cover; object-position: top center;
  display: block;
}
.tpl-card .badges {
  position: absolute; top: 10px; left: 10px;
  display: flex; gap: 6px; z-index: 2;
}
.tpl-card .badge-r {
  position: absolute; top: 10px; right: 10px;
  display: flex; gap: 6px; z-index: 2;
}
.tpl-card .badge {
  font-size: 9.5px; font-weight: 700;
  letter-spacing: .05em; text-transform: uppercase;
  padding: 3px 8px; border-radius: 99px;
  background: #fff; border: 1px solid var(--ot-border-light);
  color: var(--ot-text-muted);
  display: inline-flex; align-items: center; gap: 4px;
  white-space: nowrap;
}
.tpl-card .badge.t-page    { background: #f0f7ec; color: #2d722d; border-color: #dcefd2; }
.tpl-card .badge.t-header  { background: #dbeafe; color: #1e40af; border-color: #bfdbfe; }
.tpl-card .badge.t-footer  { background: #e2e8f0; color: #1e293b; border-color: #cbd5e1; }
.tpl-card .badge.t-single  { background: #f3e8ff; color: #6b21a8; border-color: #e9d5ff; }
.tpl-card .badge.t-megapanel { background: #fef3c7; color: #92400e; border-color: #fde68a; }
.tpl-card .badge.t-widget  { background: #ede9fe; color: #5b21b6; border-color: #ddd6fe; }
.tpl-card .badge.t-404     { background: #fee2e2; color: #991b1b; border-color: #fecaca; }
.tpl-card .badge.attivo {
  background: var(--ot-primary); color: #fff; border-color: transparent;
}
.tpl-card .badge.draft {
  background: #fef3c7; color: #92400e; border-color: #fde68a;
}
.tpl-card .pv-elements {
  position: absolute; bottom: 8px; right: 10px;
  font-size: 10px; font-weight: 500;
  color: var(--ot-text);
  background: rgba(255,255,255,.92);
  padding: 2px 7px; border-radius: 99px;
  backdrop-filter: blur(4px);
  z-index: 2;
}
.tpl-card .actions {
  position: absolute; bottom: 8px; left: 10px;
  display: flex; gap: 4px;
  opacity: 0; transform: translateY(2px);
  transition: all .15s;
  z-index: 3;
}
.tpl-card:hover .actions { opacity: 1; transform: none; }
.tpl-card .actions button {
  width: 26px; height: 26px;
  display: grid; place-items: center;
  background: #fff;
  border: 1px solid var(--ot-border-light);
  border-radius: 6px;
  color: var(--ot-text-muted);
  cursor: pointer;
  box-shadow: 0 2px 6px rgba(0,0,0,.10);
  transition: all .12s;
  padding: 0;
}
.tpl-card .actions button:hover {
  border-color: var(--ot-text);
  color: var(--ot-text);
}
.tpl-card .actions button.danger:hover {
  border-color: #ef4444; color: #ef4444;
}

/* Card body */
.tpl-card .body {
  padding: 12px 14px;
  display: flex; flex-direction: column; gap: 8px;
}
.tpl-card .title-row {
  display: flex; align-items: center; gap: 8px;
}
.tpl-card .title {
  font-size: 13.5px; font-weight: 600;
  color: var(--ot-text);
  flex: 1; min-width: 0;
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.tpl-card .title-edit {
  flex: 1; font: inherit; font-size: 13.5px; font-weight: 600;
  border: 1px solid var(--ot-primary);
  border-radius: 5px;
  padding: 3px 6px;
  outline: 0;
  width: 100%;
  background: #fff;
  color: var(--ot-text);
  box-shadow: 0 0 0 3px rgba(74,140,42,.15);
}
.tpl-card .meta {
  font-size: 11.5px; color: var(--ot-text-muted);
  display: flex; align-items: center; gap: 6px;
  flex-wrap: wrap;
}
.tpl-card .meta .dot-status {
  width: 6px; height: 6px; border-radius: 99px; background: #22c55e;
  flex-shrink: 0;
}
.tpl-card .meta .dot-status.draft { background: #f59e0b; }
.tpl-card .meta .sep { opacity: .5; }
.tpl-card .shortcode {
  display: inline-flex; align-items: center; gap: 6px;
  font-family: ui-monospace, "SF Mono", Menlo, Consolas, monospace;
  font-size: 10.5px;
  background: var(--ot-bg-muted);
  border: 1px solid var(--ot-border-light);
  border-radius: 6px;
  padding: 4px 8px;
  color: var(--ot-text-muted);
  cursor: pointer;
  transition: all .12s;
  align-self: flex-start;
  max-width: 100%;
  overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
  font-weight: 500;
}
.tpl-card .shortcode:hover {
  border-color: var(--ot-primary);
  background: var(--ot-primary-50);
  color: var(--ot-primary-dark);
}

/* ── List view ───────────────────────────────────────────────────── */
.tpl-list {
  display: flex; flex-direction: column;
  background: #fff;
  border: 1px solid var(--ot-border-light);
  border-radius: 10px;
  overflow: hidden;
}
.tpl-list .row {
  display: grid;
  grid-template-columns: 56px 1fr 110px 160px 140px 90px;
  align-items: center;
  gap: 12px;
  padding: 10px 14px;
  border-bottom: 1px solid var(--ot-border-light);
  cursor: pointer;
  transition: background .12s;
  font-size: 13px;
  color: var(--ot-text);
}
.tpl-list .row:last-child { border-bottom: 0; }
.tpl-list .row:hover { background: var(--ot-bg-muted); }
.tpl-list .row.h {
  background: var(--ot-bg-muted);
  font-size: 11px; font-weight: 600;
  text-transform: uppercase; letter-spacing: .05em;
  color: var(--ot-text-muted);
  cursor: default;
  padding: 8px 14px;
}
.tpl-list .row.h:hover { background: var(--ot-bg-muted); }
.tpl-list .mini-thumb {
  width: 56px; height: 36px; border-radius: 4px;
  border: 1px solid var(--ot-border-light);
  overflow: hidden;
  background: var(--ot-bg-muted);
  flex-shrink: 0;
  position: relative;
}
.tpl-list .mini-thumb img {
  width: 100%; height: 100%; object-fit: cover;
}
.tpl-list .ttl-cell { min-width: 0; }
.tpl-list .ttl {
  font-weight: 600; color: var(--ot-text);
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.tpl-list .sub-line {
  font-size: 11px; color: var(--ot-text-muted);
  font-family: ui-monospace, "SF Mono", Menlo, monospace;
  margin-top: 2px;
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.tpl-list .status-cell {
  display: flex; align-items: center; gap: 6px;
  font-size: 12px; color: var(--ot-text-muted);
  white-space: nowrap;
}
.tpl-list .status-cell .dot-status {
  width: 6px; height: 6px; border-radius: 99px; background: #22c55e;
}
.tpl-list .status-cell .dot-status.draft { background: #f59e0b; }
.tpl-list .status-cell .active-pill {
  font-size: 9.5px; font-weight: 700; padding: 2px 6px;
  background: var(--ot-primary); color: #fff;
  border-radius: 99px; letter-spacing: .04em;
  text-transform: uppercase;
}
.tpl-list .date-cell {
  color: var(--ot-text-muted); font-size: 12px;
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.tpl-list .row .acts {
  display: flex; gap: 4px; justify-content: flex-end;
  opacity: 0; transition: opacity .12s;
}
.tpl-list .row:hover .acts { opacity: 1; }
.tpl-list .row .acts button {
  width: 24px; height: 24px;
  display: grid; place-items: center;
  background: #fff; border: 1px solid var(--ot-border-light);
  border-radius: 5px; cursor: pointer;
  color: var(--ot-text-muted);
  padding: 0;
}
.tpl-list .row .acts button:hover { color: var(--ot-text); border-color: var(--ot-text-muted); }
.tpl-list .row .acts button.danger:hover { color: #ef4444; border-color: #ef4444; }

/* ── Toast ───────────────────────────────────────────────────────── */
.tpl-toast {
  position: fixed; bottom: 32px; left: 50%;
  transform: translateX(-50%);
  background: #1e293b; color: #fff;
  padding: 10px 18px;
  border-radius: 8px;
  font-size: 13px; font-weight: 500;
  font-family: 'Work Sans', -apple-system, sans-serif;
  box-shadow: 0 12px 32px rgba(0,0,0,.25);
  z-index: 100001;
  animation: tplToast .25s ease-out;
}
@keyframes tplToast {
  from { opacity: 0; transform: translateX(-50%) translateY(10px); }
  to   { opacity: 1; transform: translateX(-50%) translateY(0); }
}

/* ── Export dialog ───────────────────────────────────────────────── */
.tpl-export-overlay {
  position: fixed; inset: 0;
  background: rgba(15,23,42,.45);
  z-index: 100000;
  display: flex; align-items: center; justify-content: center;
  backdrop-filter: blur(4px);
}
.tpl-export-dialog {
  background: #fff;
  padding: 24px;
  border-radius: 12px;
  width: 380px; max-width: 90vw;
  box-shadow: 0 24px 64px rgba(0,0,0,.25);
  font-family: 'Work Sans', -apple-system, sans-serif;
}
.tpl-export-dialog h3 {
  margin: 0 0 16px; font-size: 16px; font-weight: 600;
  color: #1e293b;
}
.tpl-export-dialog label {
  display: flex; align-items: center; gap: 10px;
  cursor: pointer; margin-bottom: 12px;
  font-size: 14px;
}
.tpl-export-dialog label input { width: 18px; height: 18px; accent-color: #4a8c2a; }
.tpl-export-dialog p {
  font-size: 12px; color: #64748b; margin: 0 0 20px;
}
.tpl-export-dialog .actions-row {
  display: flex; gap: 10px; justify-content: flex-end;
}
.tpl-export-dialog .btn-pri,
.tpl-export-dialog .btn-sec {
  padding: 8px 16px; font-size: 13px; font-weight: 600;
  border-radius: 8px; cursor: pointer; border: 1px solid transparent;
  font-family: inherit;
}
.tpl-export-dialog .btn-pri { background: #4a8c2a; color: #fff; }
.tpl-export-dialog .btn-pri:disabled { opacity: .6; cursor: not-allowed; }
.tpl-export-dialog .btn-sec { background: #fff; color: #1e293b; border-color: #e2e8f0; }
.tpl-export-dialog .fld { display: flex; flex-direction: column; gap: 4px; margin-bottom: 12px; align-items: stretch; }
.tpl-export-dialog .fld span { font-size: 12px; font-weight: 600; color: #1e293b; }
.tpl-export-dialog .fld input { width: 100%; height: auto; padding: 8px 10px; border: 1px solid #e2e8f0; border-radius: 7px; font: inherit; font-size: 13px; outline: 0; accent-color: initial; box-sizing: border-box; }
.tpl-export-dialog .fld input:focus { border-color: var(--ot-primary); box-shadow: 0 0 0 3px rgba(74,140,42,.15); }

/* ── Selezione multipla + barra export tema ──────────────────────── */
.tpl-head .btn-sec.on { background: var(--ot-text); color: #fff; border-color: var(--ot-text); }
.tpl-selbar {
  display: flex; align-items: center; gap: 10px;
  background: #1e293b; color: #fff;
  border-radius: 10px; padding: 8px 12px;
  font-size: 13px; flex-shrink: 0;
}
.tpl-selbar .cnt { font-weight: 600; }
.tpl-selbar .spc { flex: 1; }
.tpl-selbar .btn-sec {
  display: inline-flex; align-items: center; gap: 6px;
  background: rgba(255,255,255,.12); color: #fff;
  border: 1px solid rgba(255,255,255,.2);
  padding: 6px 12px; border-radius: 7px;
  font: inherit; font-size: 12px; font-weight: 600; cursor: pointer;
}
.tpl-selbar .btn-sec:hover { background: rgba(255,255,255,.22); }
.tpl-selbar .btn-pri {
  display: inline-flex; align-items: center; gap: 6px;
  background: var(--ot-primary); color: #fff; border: 0;
  padding: 6px 14px; border-radius: 7px;
  font: inherit; font-size: 12px; font-weight: 700; cursor: pointer;
}
.tpl-selbar .btn-pri:disabled { opacity: .5; cursor: not-allowed; }
.tpl-card .sel-check {
  position: absolute; top: 10px; left: 10px; z-index: 5;
  width: 24px; height: 24px; border-radius: 6px;
  background: #fff; border: 2px solid var(--ot-border);
  display: grid; place-items: center; color: transparent;
  box-shadow: 0 2px 6px rgba(0,0,0,.14); cursor: pointer;
}
.tpl-card .sel-check.on { background: var(--ot-primary); border-color: var(--ot-primary); color: #fff; }
.tpl-card.sel-on { border-color: var(--ot-primary); box-shadow: 0 0 0 2px var(--ot-primary); }
.tpl-list .mini-thumb .sel-check.list {
  position: absolute; inset: 0; z-index: 3;
  display: grid; place-items: center;
  background: rgba(30,41,59,.35); color: transparent; cursor: pointer;
}
.tpl-list .mini-thumb .sel-check.list.on { background: var(--ot-primary); color: #fff; }
.tpl-list .row.sel-on { background: var(--ot-primary-50); }
</style>
