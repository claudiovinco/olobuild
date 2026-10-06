<template>
    <!-- Colori del chrome chiaro del builder (come Cerca e i pannelli): fondo bianco
         vetro, testi grigio scuro, accento arancio locale (--olo-ui-accent). Prima la
         libreria mescolava classi grigie Tailwind — che main.scss rimappa sul tema
         chiaro — e colori scuri scritti a mano: titolo bianco su bianco, barra e card
         scure dentro una finestra chiara. -->
    <transition name="fade">
      <div
        v-if="visible"
        class="olo-tpl-overlay"
        @click.self="close"
      >
        <div
          ref="dialogRef"
          class="olo-tpl-dialog"
          role="dialog"
          aria-modal="true"
          :aria-label="t('Blocchi & Pagine')"
          @click.stop
        >
          <!-- Header -->
          <div class="olo-tpl-head">
            <div class="olo-tpl-title">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <rect width="18" height="18" x="3" y="3" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/>
              </svg>
              <h3>{{ t('Blocchi & Pagine') }}</h3>
            </div>
            <!-- Search -->
            <div class="olo-tpl-tools">
              <label class="olo-tpl-searchbox">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input
                  v-model="searchQuery"
                  type="text"
                  :placeholder="t('Cerca template...')"
                  :aria-label="t('Cerca template...')"
                  class="olo-tpl-search"
                />
              </label>
              <button type="button" @click="close" class="olo-tpl-x" :aria-label="t('Chiudi')">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
              </button>
            </div>
          </div>

          <!-- Category filter -->
          <div class="olo-tpl-filter">
            <label class="olo-tpl-filter-label">{{ t('Categoria:') }}</label>
            <div class="olo-tpl-filter-select">
              <FieldSelect
                ui="dropdown"
                :modelValue="activeCategory"
                :options="categoryFilterOptions"
                @update:modelValue="activeCategory = $event"
              />
            </div>
            <span class="olo-tpl-count">{{ filteredTemplates.length }} {{ t('risultati') }}</span>
          </div>

          <!-- Templates grid -->
          <div class="olo-tpl-body">
            <div v-if="loading" class="olo-tpl-state">{{ t('Caricamento template...') }}</div>

            <div v-else-if="erroreLista" class="olo-tpl-state">{{ erroreLista }}</div>

            <div v-else-if="filteredTemplates.length === 0" class="olo-tpl-state">{{ t('Nessun template trovato') }}</div>

            <!-- Blocchi a muratura: ogni miniatura alle sue proporzioni, intera, senza tagli né
                 bande (le miniature dei blocchi del catalogo non sono 16:10). Le pagine intere
                 restano su due colonne con l'anteprima 16:10 dall'alto. -->
            <div :class="['olo-tpl-elenco', activeCategory === 'page' ? 'olo-tpl-elenco--pagine' : 'olo-tpl-elenco--muro']">
              <!-- Card = pulsante: si raggiunge col Tab e si sceglie con Invio o Spazio.
                   Non un <button> perché contiene il cestino; .self: Invio sul cestino
                   non inserisce il template. -->
              <div
                v-for="tpl in filteredTemplates"
                :key="tpl.id"
                class="olo-tpl-card"
                :class="{ 'is-busy': inserendoId === tpl.id }"
                role="button"
                tabindex="0"
                :aria-label="tpl.name"
                :aria-busy="inserendoId === tpl.id ? 'true' : null"
                @click="onCardClick(tpl)"
                @keydown.enter.self.prevent="onCardClick(tpl)"
                @keydown.space.self.prevent="onCardClick(tpl)"
              >
                <!-- Miniatura: del plugin (indirizzo relativo) o della libreria remota (assoluto) -->
                <div v-if="tpl.thumbnail" class="olo-tpl-media">
                  <img :src="thumbSrc(tpl)" :alt="tpl.name" class="olo-tpl-img" :class="{ 'olo-tpl-img--naturale': tpl.thumbnail_ratio }" :style="tpl.thumbnail_ratio ? { aspectRatio: tpl.thumbnail_ratio } : null" loading="lazy" @error="$event.target.style.display='none'" />
                  <div v-if="tpl.preview_description" class="olo-tpl-hover">
                    <span>{{ tpl.preview_description }}</span>
                  </div>
                  <!-- In inserimento: le foto del blocco si copiano nella Libreria media (qualche secondo) -->
                  <div v-if="inserendoId === tpl.id" class="olo-tpl-busy" aria-hidden="true">
                    <span class="olo-tpl-spin"></span>
                    <span>{{ tpl.source === 'catalogo' ? t('Copio le foto…') : t('Inserimento…') }}</span>
                  </div>
                </div>
                <!-- SVG Preview (fallback) -->
                <div v-else class="olo-tpl-media">
                  <svg :viewBox="'0 0 260 120'" width="100%" style="display:block;background-color:var(--tpl-bg)" :style="{ '--tpl-bg': getPreviewBg(tpl) }">
                    <g v-for="(el, i) in getSvgElements(tpl)" :key="i">
                      <rect v-if="el.shape === 'rect'" :x="el.x" :y="el.y" :width="el.w" :height="el.h" :rx="el.rx || 0" :fill="el.fill" :opacity="el.opacity || 1" />
                      <line v-else-if="el.shape === 'line'" :x1="el.x1" :y1="el.y1" :x2="el.x2" :y2="el.y2" :stroke="el.stroke" :stroke-width="el.sw || 1" :opacity="el.opacity || 0.3" />
                      <circle v-else-if="el.shape === 'circle'" :cx="el.cx" :cy="el.cy" :r="el.r" :fill="el.fill" :opacity="el.opacity || 1" />
                    </g>
                  </svg>
                  <!-- Hover description -->
                  <div v-if="tpl.preview_description" class="olo-tpl-hover olo-tpl-hover--sm">
                    <span>{{ tpl.preview_description }}</span>
                  </div>
                </div>
                <!-- Info -->
                <div class="olo-tpl-info">
                  <div class="olo-tpl-info-text">
                    <div class="olo-tpl-name-row">
                      <span class="olo-tpl-name">{{ tpl.name }}</span>
                      <span v-if="isPagina(tpl)" class="olo-tpl-badge olo-tpl-badge--page">{{ t('Pagina') }}</span>
                      <span v-if="tpl.is_user" class="olo-tpl-badge olo-tpl-badge--user">{{ t('Personale') }}</span>
                    </div>
                    <!-- Il colore della categoria sta nel pallino: come testo, sul bianco,
                         i toni chiari (lime, ambra, azzurro) non si leggevano. -->
                    <span class="olo-tpl-cat"><span class="olo-tpl-dot" :style="{ background: getCategoryColor(tpl.category) }" aria-hidden="true"></span>{{ getCategoryLabel(tpl.category) }}</span>
                  </div>
                  <!-- Delete button for user templates -->
                  <button
                    v-if="tpl.is_user"
                    type="button"
                    class="olo-tpl-del"
                    :title="t('Elimina template')"
                    :aria-label="t('Elimina template')"
                    @click.stop="confirmDelete(tpl)"
                  >
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M3 6h18"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6"/><path d="M8 6V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg>
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Footer -->
          <div class="olo-tpl-foot">
            <span>{{ filteredTemplates.length }} / {{ templates.length }} {{ t('template') }}</span>
            <button type="button" @click="close" class="olo-tpl-link">{{ t('Chiudi') }}</button>
          </div>
        </div>
      </div>
    </transition>

    <!-- ═══ Save dialog ═══ -->
    <transition name="fade">
      <div
        v-if="saveDialogVisible"
        class="olo-tpl-overlay olo-tpl-overlay--top"
        @click.self="closeSaveDialog"
      >
        <div class="olo-tpl-box olo-tpl-box--save" @click.stop>
          <!-- Header -->
          <div class="olo-tpl-box-head">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="olo-tpl-accent" aria-hidden="true"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
            <span class="olo-tpl-box-title">{{ t('Salva come template') }}</span>
          </div>
          <!-- Body -->
          <div class="olo-tpl-box-body">
            <div class="olo-tpl-field">
              <label class="olo-tpl-label">{{ t('Nome template') }}</label>
              <input
                ref="saveNameInput"
                v-model="saveName"
                type="text"
                :placeholder="t('es. Hero con video e CTA')"
                class="olo-tpl-input"
                @keydown.enter="doSave"
              />
            </div>
            <div class="olo-tpl-field">
              <label class="olo-tpl-label">{{ t('Categoria') }}</label>
              <FieldSelect
                ui="dropdown"
                :modelValue="saveCategory"
                :options="saveCategorySelectOptions"
                @update:modelValue="saveCategory = $event"
              />
            </div>
            <div class="olo-tpl-field">
              <label class="olo-tpl-label">{{ t('Descrizione (opzionale)') }}</label>
              <input
                v-model="saveDescription"
                type="text"
                :placeholder="t('Breve descrizione del template')"
                class="olo-tpl-input"
              />
            </div>
            <!-- Preview info -->
            <div v-if="saveSection" class="olo-tpl-note">
              <span class="olo-tpl-note-k">{{ t('Sezione:') }}</span>
              <span class="olo-tpl-note-v">{{ saveSection.settings?._label || t('Sezione') }}</span>
              <span class="olo-tpl-note-k olo-tpl-note-gap">{{ countElements(saveSection) }} {{ countElements(saveSection) === 1 ? t('elemento') : t('elementi') }}</span>
            </div>
          </div>
          <!-- Footer -->
          <div class="olo-tpl-box-foot">
            <button type="button" @click="closeSaveDialog" class="olo-tpl-btn">{{ t('Annulla') }}</button>
            <button
              type="button"
              @click="doSave"
              :disabled="!saveName.trim() || saving"
              class="olo-tpl-btn olo-tpl-btn--primary"
            >{{ saving ? t('Salvataggio...') : t('Salva template') }}</button>
          </div>
        </div>
      </div>
    </transition>

    <!-- ═══ Delete confirm dialog ═══ -->
    <transition name="fade">
      <div
        v-if="deleteDialogVisible"
        class="olo-tpl-overlay olo-tpl-overlay--top"
        @click.self="deleteDialogVisible = false"
      >
        <div class="olo-tpl-box olo-tpl-box--del" @click.stop>
          <div class="olo-tpl-box-body">
            <div class="olo-tpl-del-head">
              <div class="olo-tpl-del-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M3 6h18"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6"/><path d="M8 6V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg>
              </div>
              <div>
                <div class="olo-tpl-box-title">{{ t('Elimina template') }}</div>
                <div class="olo-tpl-sub">{{ t('Questa azione non può essere annullata') }}</div>
              </div>
            </div>
            <p class="olo-tpl-text">{{ t('Vuoi eliminare il template') }} <strong>{{ deleteTarget?.name }}</strong>?</p>
          </div>
          <div class="olo-tpl-box-foot">
            <button type="button" @click="deleteDialogVisible = false" class="olo-tpl-btn">{{ t('Annulla') }}</button>
            <button
              type="button"
              @click="doDelete"
              :disabled="deleting"
              class="olo-tpl-btn olo-tpl-btn--danger"
            >{{ deleting ? t('Eliminazione...') : t('Elimina') }}</button>
          </div>
        </div>
      </div>
    </transition>

    <!-- Page template insert confirmation dialog -->
    <!-- Il secondo clic di un doppio clic sulla card cade già nel dialogo, sullo sfondo
         o su un pulsante: non chiude e non sceglie (detail = numero del clic; Invio e
         Spazio sui pulsanti danno 0). Il mousedown sullo sfondo non toglie il focus al
         riquadro, così Esc chiude anche dopo un clic lì. Il riquadro prende il focus, non
         un pulsante: lo Spazio che ha scelto la card si rilascia qui e non preme
         «Sostituisci tutto». Da lì il Tab gira fra i tre pulsanti senza uscire (la
         trappola della libreria è spenta mentre il dialogo è aperto); Esc chiude. -->
    <transition name="fade">
      <div v-if="pageInsertMode === 'ask'" class="olo-tpl-overlay olo-tpl-overlay--top" @mousedown.self.prevent @click.self="onPageCancelClick" @keydown.esc.stop="cancelPageInsert">
        <div ref="pageDialogRef" tabindex="-1" role="dialog" aria-modal="true" :aria-label="t('Inserisci pagina completa')" class="olo-tpl-box olo-tpl-box--page" @click.stop @keydown.tab="tabNelDialogoPagina">
          <div class="olo-tpl-page-head">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="olo-tpl-accent" aria-hidden="true"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/></svg>
            <span class="olo-tpl-box-title">{{ t('Inserisci pagina completa') }}</span>
          </div>
          <p class="olo-tpl-text olo-tpl-text--page">
            {{ t('Il canvas contiene già del contenuto. Come vuoi procedere con il template') }} <strong>{{ pendingPageTpl?.name }}</strong>?
          </p>
          <div class="olo-tpl-page-actions">
            <button type="button" @click="confirmPageInsert('replace', $event)" class="olo-tpl-btn olo-tpl-btn--primary olo-tpl-btn--grow">
              {{ t('Sostituisci tutto') }}
            </button>
            <button type="button" @click="confirmPageInsert('append', $event)" class="olo-tpl-btn olo-tpl-btn--soft olo-tpl-btn--grow">
              {{ t('Aggiungi in fondo') }}
            </button>
            <button type="button" @click="onPageCancelClick" class="olo-tpl-btn">
              {{ t('Annulla') }}
            </button>
          </div>
        </div>
      </div>
    </transition>
</template>

<script setup>
import { t } from '@/i18n';
import { ref, computed, nextTick, watch } from 'vue';
import { useFocusTrap } from '@/composables/useFocusTrap';
import { useTilesStore } from '@/stores/tiles';
import { findNodeById } from '@/stores/treeUtils';
import { useBuilderStore } from '@/stores/builder';
import { useToast } from '@/composables/useToast.js';
import { useHistory } from '@/composables/useHistory';
import { requestScrollToTile } from '@/utils/scrollToTileChannel';
import FieldSelect from './fields/FieldSelect.vue';

const tilesStore = useTilesStore();
const builderStore = useBuilderStore();
const toast = useToast();
const history = useHistory();

const oloData = window.oloData || {};

const visible = ref(false);
const loading = ref(false);
const templates = ref([]);
// Messaggio quando la lista non arriva (rete, permessi, sandbox): senza, la finestra
// diceva «Nessun template trovato» come se la libreria fosse vuota.
const erroreLista = ref('');
// Dove va un blocco: { zone, index, afterId } dal «+» o dal menu contestuale, null = in
// fondo al body (pulsante della toolbar). Si riazzera a ogni apertura.
const posizione = ref(null);
const inserendo = ref(false);
// La card in inserimento: mostra l'attesa (la copia delle foto richiede qualche secondo)
const inserendoId = ref(null);
// Aperture della libreria: un blocco scaricato per un'apertura precedente non si inserisce.
let aperture = 0;
const activeCategory = ref('all');
const searchQuery = ref('');

// ═══ Save dialog state ═══
const saveDialogVisible = ref(false);
const saveSection = ref(null);
const saveName = ref('');
const saveCategory = ref('custom');
const saveDescription = ref('');
const saving = ref(false);
const saveNameInput = ref(null);

// ═══ Delete dialog state ═══
const deleteDialogVisible = ref(false);
const deleteTarget = ref(null);
const deleting = ref(false);

const categoryDefs = [
  { key: 'all',           label: 'Tutti',          color: '#9CA3AF' },
  { key: 'hero',          label: 'Hero',           color: '#e1474f' },
  { key: 'features',      label: 'Features',       color: '#10B981' },
  { key: 'services',      label: 'Servizi',        color: '#14B8A6' },
  { key: 'pricing',       label: 'Prezzi',         color: '#F59E0B' },
  { key: 'testimonials',  label: 'Testimonianze',  color: '#8B5CF6' },
  { key: 'cta',           label: 'CTA',            color: '#EF4444' },
  { key: 'about',         label: 'Chi siamo',      color: '#3B82F6' },
  { key: 'team',          label: 'Team',           color: '#06B6D4' },
  { key: 'contact',       label: 'Contatti',       color: '#F97316' },
  { key: 'faq',           label: 'FAQ',            color: '#84CC16' },
  { key: 'stats',         label: 'Statistiche',    color: '#A855F7' },
  { key: 'footer',        label: 'Footer',         color: '#64748B' },
  { key: 'blog',          label: 'Blog',           color: '#EC4899' },
  { key: 'gallery',       label: 'Galleria',       color: '#F43F5E' },
  { key: 'portfolio',     label: 'Portfolio',      color: '#0EA5E9' },
  { key: 'video',         label: 'Video',          color: '#DC2626' },
  { key: 'timeline',      label: 'Timeline',       color: '#7C3AED' },
  { key: 'text',          label: 'Testo e titoli', color: '#0369A1' },
  { key: 'newsletter',    label: 'Newsletter',     color: '#059669' },
  { key: 'logos',         label: 'Loghi',          color: '#78716C' },
  { key: 'coming-soon',   label: 'Coming Soon',    color: '#D946EF' },
  { key: '404',           label: '404',            color: '#EF4444' },
  { key: 'ecommerce',     label: 'E-Commerce',     color: '#F97316' },
  { key: 'page',           label: 'Pagine complete', color: '#2563EB' },
  { key: 'misc',          label: 'Varie',          color: '#6B7280' },
  { key: 'custom',        label: 'Personali',      color: '#F59E0B' },
];

// Category options for save dialog (exclude 'all')
const saveCategoryOptions = categoryDefs.filter(c => c.key !== 'all');

// Options { value, label } per i dropdown custom FieldSelect
const saveCategorySelectOptions = saveCategoryOptions.map(cat => ({ value: cat.key, label: cat.label }));

const categoriesWithCount = computed(() => {
  return categoryDefs
    .map(cat => ({
      ...cat,
      count: cat.key === 'all'
        ? templates.value.length
        : templates.value.filter(t => t.category === cat.key).length,
    }))
    .filter(cat => cat.count > 0 || cat.key === 'all');
});

const categoryFilterOptions = computed(() =>
  categoriesWithCount.value.map(cat => ({ value: cat.key, label: `${cat.label} (${cat.count})` }))
);

const filteredTemplates = computed(() => {
  let list = templates.value;
  if (activeCategory.value !== 'all') {
    list = list.filter(t => t.category === activeCategory.value);
  }
  if (searchQuery.value.trim()) {
    const q = searchQuery.value.toLowerCase().trim();
    list = list.filter(t =>
      t.name.toLowerCase().includes(q) ||
      (t.preview_description || '').toLowerCase().includes(q) ||
      (t.category || '').toLowerCase().includes(q)
    );
  }
  return list;
});

// Miniatura: quelle del plugin hanno un indirizzo relativo (assets/img/…), quelle dei blocchi del
// catalogo stanno nella libreria remota e arrivano con l'indirizzo intero.
function thumbSrc(tpl) {
  const src = String(tpl.thumbnail || '');
  return /^https?:\/\//i.test(src) ? src : (oloData.pluginUrl || '') + src;
}

const pageInsertMode = ref(null); // null, 'replace', 'append'
const pendingPageTpl = ref(null);
const pageDialogRef = ref(null);

// Pagina intera = un template di serie della categoria «Pagine complete». Un template
// personale è sempre una sezione (il builder salva la sezione cliccata), anche se chi
// l'ha salvato ha scelto quella categoria: va dove è stata aperta la libreria e non
// sostituisce il body. La categoria salvata resta com'è.
function isPagina(tpl) {
  return !!tpl && tpl.category === 'page' && !tpl.is_user;
}

function onCardClick(tpl) {
  // Libreria che sta sparendo (dissolvenza di chiusura), blocco ancora in arrivo o
  // dialogo della pagina aperto sopra (una card dietro di lui non sceglie): un clic o
  // un Invio qui non sceglie niente.
  if (!visible.value || inserendo.value || pageInsertMode.value === 'ask') return;
  if (isPagina(tpl)) {
    // Pagina completa: con canvas già pieno chiedi (sostituisci/accoda),
    // su canvas vuoto sostituisci direttamente.
    if (tilesStore.canvasTiles.length > 0) {
      pendingPageTpl.value = tpl;
      pageInsertMode.value = 'ask';
      nextTick(() => pageDialogRef.value?.focus());
    } else {
      insertTemplate(tpl, 'replace');
    }
  } else {
    // Blocco/sezione: va dove è stata aperta la libreria (vedi destinazione()), non
    // sostituisce niente.
    insertTemplate(tpl, 'append');
  }
}

// Secondo (o terzo) clic di un doppio clic sulla card: arriva nel dialogo perché si è
// appena aperto sotto il puntatore, sullo sfondo o su un pulsante. Non va preso per una
// scelta: «Sostituisci tutto» sotto il puntatore sostituiva la pagina. Invio e Spazio sui
// pulsanti danno detail 0 e passano.
function secondoClic(e) {
  return !!e && e.detail > 1;
}

// Clic sullo sfondo del dialogo o su «Annulla»: chiude, tranne il secondo clic di un
// doppio clic sulla card.
function onPageCancelClick(e) {
  if (secondoClic(e)) return;
  cancelPageInsert();
}

function confirmPageInsert(mode, e) {
  if (secondoClic(e)) return;
  if (!visible.value) { cancelPageInsert(); return; }
  if (pendingPageTpl.value) {
    insertTemplate(pendingPageTpl.value, mode);
  }
  pendingPageTpl.value = null;
  pageInsertMode.value = null;
}

function cancelPageInsert() {
  pendingPageTpl.value = null;
  pageInsertMode.value = null;
}

// Il Tab resta nel dialogo della pagina (aria-modal): dall'ultimo pulsante torna al
// primo, Shift+Tab dal primo o dal riquadro va all'ultimo; negli altri casi il Tab fa
// il suo corso fra i pulsanti. Non useFocusTrap: activate() metterebbe il focus su
// «Sostituisci tutto», e il rilascio dello Spazio che ha scelto la card lo premerebbe.
function tabNelDialogoPagina(e) {
  const box = pageDialogRef.value;
  if (!box) return;
  const pulsanti = box.querySelectorAll('button:not([disabled])');
  if (!pulsanti.length) return;
  const primo = pulsanti[0];
  const ultimo = pulsanti[pulsanti.length - 1];
  const attivo = document.activeElement;
  if (e.shiftKey) {
    if (attivo === primo || attivo === box) { e.preventDefault(); ultimo.focus(); }
  } else if (attivo === ultimo) {
    e.preventDefault();
    primo.focus();
  }
}

function getCategoryColor(cat) {
  return categoryDefs.find(c => c.key === cat)?.color || '#6B7280';
}

function getCategoryLabel(cat) {
  return categoryDefs.find(c => c.key === cat)?.label || cat;
}

function getPreviewBg(tpl) {
  const sec = tpl.content?.[0];
  const bg = sec?.style?.bg_color;
  if (!bg) return '#F9FAFB';
  // I blocchi moderni usano token tema; il documento del builder può non averli
  // definiti → mappa i token su un fallback hex così l'anteprima mostra la tinta giusta.
  const map = {
    'var(--olo-color-dark)': '#16263d',
    'var(--olo-color-primary)': '#e1474f',
    'var(--olo-color-secondary)': '#16263d',
    'var(--olo-color-light)': '#f8f9fa',
    'var(--olo-color-accent)': '#f4a23b',
  };
  return map[bg] || bg;
}

// Width fraction map for column widths
const widthFraction = { '1-1': 1, '1-2': 0.5, '1-3': 0.333, '2-3': 0.666, '1-4': 0.25, '3-4': 0.75, '1-5': 0.2, '2-5': 0.4, '3-5': 0.6, '1-6': 0.166, '5-6': 0.833 };

function getSvgElements(tpl) {
  const els = [];
  const W = 260, H = 120, PAD = 16;

  // Full-page templates: show stacked section strips
  if (tpl.category === 'page') {
    const sections = [];
    for (let k = 0; k < 20; k++) {
      if (tpl.content?.[k]) sections.push(tpl.content[k]);
      else break;
    }
    if (sections.length > 1) {
      const secH = Math.floor(H / sections.length);
      sections.forEach((sec, i) => {
        const bg = sec?.style?.bg_color || (i % 2 === 0 ? '#F9FAFB' : '#FFFFFF');
        els.push({ shape: 'rect', x: 0, y: i * secH, w: W, h: secH, fill: bg, opacity: 1 });
        const row = sec?.children?.[0];
        const numCols = row?.children?.length || 1;
        const cy = i * secH + secH / 2;
        const isDark = bg && /^#[0-3]/.test(bg);
        const lineColor = isDark ? '#ffffff' : '#374151';
        if (numCols === 1) {
          els.push({ shape: 'rect', x: W * 0.25, y: cy - 3, w: W * 0.5, h: 5, fill: lineColor, opacity: 0.3, rx: 2 });
        } else {
          const colW = (W - PAD * 2 - 8 * (numCols - 1)) / numCols;
          for (let c = 0; c < numCols; c++) {
            const cx = PAD + c * (colW + 8) + colW / 2;
            els.push({ shape: 'rect', x: cx - colW * 0.35, y: cy - 2, w: colW * 0.7, h: 4, fill: lineColor, opacity: 0.25, rx: 1 });
          }
        }
        if (i > 0) {
          els.push({ shape: 'line', x1: 0, y1: i * secH, x2: W, y2: i * secH, stroke: lineColor, sw: 0.5, opacity: 0.1 });
        }
      });
      return els;
    }
  }

  const sec = tpl.content?.[0];
  const bgColor = sec?.style?.bg_color || '#F9FAFB';
  // Riconosci superfici scure sia da hex scuri sia dai token tema (dark/primary/secondary).
  const isDark = !!bgColor && (/^#[0-3][0-9a-fA-F]{5}$/.test(bgColor) || /--olo-color-(dark|primary|secondary)/.test(bgColor));
  const elColor = isDark ? '#ffffff' : '#374151';
  const elColorLight = isDark ? '#ffffff' : '#9CA3AF';
  // Accento delle anteprime = primario brand del sito (token, con fallback rosso brand).
  const accentHex = 'var(--olo-color-primary, #e1474f)';

  if (!sec?.children?.length) {
    els.push({ shape: 'rect', x: W*0.2, y: 30, w: W*0.6, h: 8, fill: elColor, opacity: 0.35, rx: 2 });
    els.push({ shape: 'rect', x: W*0.3, y: 48, w: W*0.4, h: 5, fill: elColor, opacity: 0.2, rx: 2 });
    els.push({ shape: 'rect', x: W*0.35, y: 66, w: W*0.3, h: 12, fill: accentHex, opacity: 0.7, rx: 4 });
    return els;
  }

  // Impila TUTTE le righe della sezione (i blocchi hanno spesso headline + contenuto
  // in righe separate), non solo la prima.
  const rowsAll = (sec.children || []).filter(n => n && n.type === 'row');
  const rowsToDraw = rowsAll.length ? rowsAll : (sec.children || []).slice(0, 1);
  let yCursor = 12;
  for (const drow of rowsToDraw) {
    if (yCursor > H - 12) break;
    const cols = drow?.children || [];
    const numCols = cols.length || 1;
    const totalW = W - PAD * 2;
    const colGap = numCols > 1 ? 8 : 0;
    const availW = totalW - colGap * (numCols - 1);
    let colX = PAD;
    let rowMaxY = yCursor;
    for (let ci = 0; ci < numCols; ci++) {
      const col = cols[ci];
      const fraction = widthFraction[col?.settings?.width] || (1 / numCols);
      const colW = availW * fraction;
      const children = col?.children || [];
      let y = yCursor;

    for (const child of children.slice(0, 6)) {
      if (y > H - 10) break;
      const t = child.type;
      const cx = colX + colW / 2;

      if (t === 'headline') {
        const tw = colW * 0.75;
        els.push({ shape: 'rect', x: cx - tw/2, y, w: tw, h: 7, fill: elColor, opacity: 0.5, rx: 2 });
        y += 11;
        if (child.settings?.subtitle) {
          const sw = colW * 0.55;
          els.push({ shape: 'rect', x: cx - sw/2, y, w: sw, h: 4, fill: elColorLight, opacity: 0.35, rx: 1 });
          y += 8;
        }
      } else if (t === 'button') {
        const bw = Math.min(colW * 0.45, 60);
        const bgc = child.settings?.bg_color || accentHex;
        els.push({ shape: 'rect', x: cx - bw/2, y, w: bw, h: 11, fill: bgc || accentHex, opacity: 0.85, rx: 4 });
        y += 16;
      } else if (t === 'image') {
        const iw = colW * 0.85;
        els.push({ shape: 'rect', x: cx - iw/2, y, w: iw, h: 28, fill: elColor, opacity: 0.08, rx: 3 });
        els.push({ shape: 'circle', cx: cx, cy: y + 14, r: 5, fill: elColorLight, opacity: 0.2 });
        y += 33;
      } else if (t === 'spacer') {
        y += 6;
      } else if (t === 'content' || t === 'editor') {
        for (let li = 0; li < 2; li++) {
          const lw = colW * (0.8 - li * 0.15);
          els.push({ shape: 'rect', x: cx - lw/2, y, w: lw, h: 3, fill: elColor, opacity: 0.18, rx: 1 });
          y += 6;
        }
      } else if (t === 'icon' || t === 'iconbox') {
        els.push({ shape: 'circle', cx, cy: y + 8, r: 8, fill: accentHex, opacity: 0.15 });
        y += 20;
        if (t === 'iconbox') {
          const tw2 = colW * 0.6;
          els.push({ shape: 'rect', x: cx - tw2/2, y, w: tw2, h: 4, fill: elColor, opacity: 0.3, rx: 1 });
          y += 8;
        }
      } else if (t === 'divider') {
        els.push({ shape: 'line', x1: colX + 4, y1: y + 2, x2: colX + colW - 4, y2: y + 2, stroke: elColorLight, sw: 1, opacity: 0.3 });
        y += 7;
      } else if (t === 'counter') {
        els.push({ shape: 'rect', x: cx - 12, y, w: 24, h: 10, fill: accentHex, opacity: 0.2, rx: 2 });
        y += 15;
      } else if (t === 'gallery' || t === 'progallery') {
        const gw = colW * 0.9;
        const gx = cx - gw/2;
        for (let r = 0; r < 2; r++) {
          for (let c = 0; c < 3; c++) {
            const cw2 = (gw - 4) / 3;
            els.push({ shape: 'rect', x: gx + c * (cw2 + 2), y: y + r * 14, w: cw2, h: 12, fill: elColor, opacity: 0.08, rx: 2 });
          }
        }
        y += 30;
      } else if (t === 'pricing') {
        const pw = colW * 0.8;
        els.push({ shape: 'rect', x: cx - pw/2, y, w: pw, h: 40, fill: elColor, opacity: 0.05, rx: 4 });
        els.push({ shape: 'rect', x: cx - 15, y: y + 6, w: 30, h: 6, fill: elColor, opacity: 0.25, rx: 2 });
        els.push({ shape: 'rect', x: cx - 10, y: y + 16, w: 20, h: 4, fill: accentHex, opacity: 0.6, rx: 1 });
        y += 46;
      } else if (t === 'team') {
        els.push({ shape: 'circle', cx, cy: y + 10, r: 10, fill: elColor, opacity: 0.1 });
        els.push({ shape: 'rect', x: cx - 16, y: y + 24, w: 32, h: 4, fill: elColor, opacity: 0.25, rx: 1 });
        y += 34;
      } else if (t === 'testimonial' || t === 'quotation') {
        const qw = colW * 0.8;
        els.push({ shape: 'rect', x: cx - qw/2, y, w: qw, h: 30, fill: elColor, opacity: 0.04, rx: 4 });
        els.push({ shape: 'rect', x: cx - qw*0.3, y: y + 6, w: qw*0.6, h: 3, fill: elColor, opacity: 0.2, rx: 1 });
        els.push({ shape: 'circle', cx: cx, cy: y + 22, r: 4, fill: elColorLight, opacity: 0.2 });
        y += 36;
      } else if (t === 'form') {
        const fw = colW * 0.8;
        const fx = cx - fw/2;
        for (let fi = 0; fi < 2; fi++) {
          els.push({ shape: 'rect', x: fx, y: y + fi * 12, w: fw, h: 8, fill: elColor, opacity: 0.07, rx: 3 });
        }
        els.push({ shape: 'rect', x: cx - 20, y: y + 28, w: 40, h: 9, fill: accentHex, opacity: 0.7, rx: 3 });
        y += 42;
      } else if (t === 'video') {
        const vw = colW * 0.85;
        els.push({ shape: 'rect', x: cx - vw/2, y, w: vw, h: 26, fill: elColor, opacity: 0.08, rx: 3 });
        els.push({ shape: 'circle', cx, cy: y + 13, r: 6, fill: accentHex, opacity: 0.4 });
        y += 31;
      } else if (t === 'social') {
        for (let si = -2; si <= 2; si++) {
          els.push({ shape: 'circle', cx: cx + si * 12, cy: y + 5, r: 4, fill: accentHex, opacity: 0.2 });
        }
        y += 14;
      } else if (t === 'countdown') {
        for (let ci2 = 0; ci2 < 4; ci2++) {
          const bx = cx - 30 + ci2 * 18;
          els.push({ shape: 'rect', x: bx, y, w: 14, h: 14, fill: elColor, opacity: 0.08, rx: 3 });
        }
        y += 20;
      } else if (t === 'list') {
        for (let li = 0; li < 3; li++) {
          const lw = colW * (0.7 - li * 0.05);
          els.push({ shape: 'circle', cx: colX + 6, cy: y + 2, r: 2, fill: accentHex, opacity: 0.4 });
          els.push({ shape: 'rect', x: colX + 12, y: y, w: lw, h: 3, fill: elColor, opacity: 0.2, rx: 1 });
          y += 8;
        }
      } else if (t === 'accordion') {
        const aw = colW * 0.85;
        for (let ai = 0; ai < 3; ai++) {
          els.push({ shape: 'rect', x: cx - aw/2, y, w: aw, h: 7, fill: elColor, opacity: 0.07, rx: 3 });
          y += 10;
        }
      } else if (t === 'map') {
        const mw = colW * 0.9;
        els.push({ shape: 'rect', x: cx - mw/2, y, w: mw, h: 30, fill: '#D1FAE5', opacity: 0.5, rx: 3 });
        els.push({ shape: 'circle', cx, cy: y + 12, r: 3, fill: '#EF4444', opacity: 0.8 });
        y += 35;
      } else if (t === 'timeline') {
        els.push({ shape: 'line', x1: cx, y1: y, x2: cx, y2: y + 35, stroke: elColorLight, sw: 1, opacity: 0.3 });
        for (let ti = 0; ti < 3; ti++) {
          els.push({ shape: 'circle', cx, cy: y + 4 + ti * 14, r: 3, fill: accentHex, opacity: 0.6 });
        }
        y += 40;
      } else if (t === 'slideshow' || t === 'proslider') {
        const sw2 = colW * 0.9;
        els.push({ shape: 'rect', x: cx - sw2/2, y, w: sw2, h: 30, fill: elColor, opacity: 0.07, rx: 3 });
        for (let di = -1; di <= 1; di++) {
          els.push({ shape: 'circle', cx: cx + di * 6, cy: y + 34, r: 2, fill: accentHex, opacity: di === 0 ? 0.6 : 0.2 });
        }
        y += 40;
      } else if (['info-cards','showcasegrid','productgrid','product-cards','postgrid','queryloop','portfolio','overlaygrid','workgrid','glowgallery','progallery','lookbookmixer','icontabs'].includes(t)) {
        const gw = colW * 0.94, gx = cx - gw/2, cw2 = (gw - 12) / 3;
        for (let r = 0; r < 2; r++) for (let c = 0; c < 3; c++) {
          els.push({ shape: 'rect', x: gx + c*(cw2+6), y: y + r*20, w: cw2, h: 17, fill: elColor, opacity: 0.08, rx: 3 });
          els.push({ shape: 'circle', cx: gx + c*(cw2+6) + 7, cy: y + r*20 + 6, r: 2.5, fill: accentHex, opacity: 0.55 });
        }
        y += 44;
      } else if (t === 'statstrip' || t === 'countercircle') {
        for (let s = 0; s < 4; s++) {
          const bx = colX + (colW/4)*s + colW/8;
          els.push({ shape: 'rect', x: bx - 12, y, w: 24, h: 9, fill: accentHex, opacity: 0.55, rx: 2 });
          els.push({ shape: 'rect', x: bx - 16, y: y + 13, w: 32, h: 3, fill: elColor, opacity: 0.25, rx: 1 });
        }
        y += 22;
      } else if (['cta-banner','newsletter','mediacta','announcementbar','trust-strip'].includes(t)) {
        els.push({ shape: 'rect', x: cx - colW*0.32, y, w: colW*0.64, h: 6, fill: elColor, opacity: 0.4, rx: 2 });
        y += 12;
        if (t === 'newsletter') {
          els.push({ shape: 'rect', x: cx - colW*0.34, y, w: colW*0.46, h: 11, fill: elColor, opacity: 0.08, rx: 4 });
          els.push({ shape: 'rect', x: cx + colW*0.14, y, w: colW*0.2, h: 11, fill: accentHex, opacity: 0.8, rx: 4 });
        } else {
          els.push({ shape: 'rect', x: cx - 22, y, w: 44, h: 11, fill: accentHex, opacity: 0.8, rx: 4 });
        }
        y += 16;
      } else if (['introsplit','featuredstory','switcherpanel'].includes(t)) {
        els.push({ shape: 'rect', x: colX, y, w: colW*0.46, h: 34, fill: elColor, opacity: 0.09, rx: 3 });
        els.push({ shape: 'circle', cx: colX + colW*0.23, cy: y + 17, r: 5, fill: elColorLight, opacity: 0.2 });
        const tx = colX + colW*0.52;
        els.push({ shape: 'rect', x: tx, y: y+2, w: colW*0.4, h: 6, fill: elColor, opacity: 0.4, rx: 2 });
        els.push({ shape: 'rect', x: tx, y: y+12, w: colW*0.36, h: 3, fill: elColor, opacity: 0.2, rx: 1 });
        els.push({ shape: 'rect', x: tx, y: y+22, w: 36, h: 9, fill: accentHex, opacity: 0.75, rx: 3 });
        y += 40;
      } else if (['glowhero','imagehero','maskedvideohero','producthero','photocover','masthead','hero','searchhero'].includes(t)) {
        els.push({ shape: 'rect', x: cx - colW*0.36, y, w: colW*0.72, h: 7, fill: elColor, opacity: 0.45, rx: 2 });
        y += 11;
        els.push({ shape: 'rect', x: cx - colW*0.26, y, w: colW*0.52, h: 4, fill: elColorLight, opacity: 0.3, rx: 1 });
        y += 9;
        els.push({ shape: 'rect', x: cx - 26, y, w: 52, h: 11, fill: accentHex, opacity: 0.8, rx: 4 });
        y += 17;
      } else if (t === 'process-steps' || t === 'step-timeline') {
        for (let s = 0; s < 4; s++) {
          const bx = colX + (colW/4)*s + colW/8;
          els.push({ shape: 'circle', cx: bx, cy: y + 6, r: 6, fill: accentHex, opacity: 0.5 });
          if (s < 3) els.push({ shape: 'line', x1: bx + 7, y1: y + 6, x2: bx + colW/4 - 7, y2: y + 6, stroke: elColorLight, sw: 1, opacity: 0.3 });
          els.push({ shape: 'rect', x: bx - 14, y: y + 16, w: 28, h: 3, fill: elColor, opacity: 0.22, rx: 1 });
        }
        y += 26;
      } else if (t === 'marquee') {
        for (let s = 0; s < 5; s++) els.push({ shape: 'rect', x: colX + s*(colW/5) + 6, y, w: colW/5 - 12, h: 10, fill: elColor, opacity: 0.12, rx: 2 });
        y += 16;
      } else if (['worklist','workgrid'].includes(t)) {
        for (let s = 0; s < 3; s++) els.push({ shape: 'rect', x: colX, y: y + s*11, w: colW*0.9, h: 8, fill: elColor, opacity: 0.07, rx: 2 });
        y += 36;
      } else if (['navmenu','subnav','search','sitelogo','authorbox'].includes(t)) {
        els.push({ shape: 'rect', x: colX, y, w: colW*0.5, h: 4, fill: elColor, opacity: 0.25, rx: 1 });
        y += 9;
      } else {
        const gw2 = colW * 0.6;
        els.push({ shape: 'rect', x: cx - gw2/2, y, w: gw2, h: 6, fill: elColor, opacity: 0.12, rx: 2 });
        y += 10;
      }
    }
      if (y > rowMaxY) rowMaxY = y;
      colX += colW + colGap;
    }
    yCursor = rowMaxY + 6;
  }
  return els;
}

// Nome e descrizione passano dal sanitize_text_field del PHP, che salva un «<» che non
// apre un tag come «&lt;» (e da lì in poi & e virgolette come entità): «Prezzi < 10»
// si leggeva «Prezzi &lt; 10» e la ricerca con «<» non lo trovava. Si rileggono come
// sono stati scritti; l'interpolazione di Vue fa l'escape. Caso limite: un'entità scritta
// alla lettera («R&amp;D») si legge decodificata («R&D»). Solo lettura: il dato resta com'è.
function testoLeggibile(v) {
  return String(v ?? '')
    .replace(/&lt;/g, '<').replace(/&gt;/g, '>')
    .replace(/&quot;/g, '"').replace(/&#0?39;/g, "'")
    .replace(/&amp;/g, '&');
}

// Il messaggio del server (WP_Error) quando c'è: dice perché non è andata, non solo che
// non è andata (nella demo, per esempio, la libreria non si modifica).
async function messaggioErrore(res) {
  try { return (await res.json())?.message || ''; } catch (e) { return ''; }
}

async function fetchTemplates() {
  loading.value = true;
  erroreLista.value = '';
  try {
    const res = await fetch(`${oloData.restUrl}template-library`, {
      headers: { 'X-WP-Nonce': oloData.nonce },
    });
    if (res.ok) {
      const lista = await res.json();
      templates.value = Array.isArray(lista)
        ? lista.map((tpl) => ({
            ...tpl,
            name: testoLeggibile(tpl.name),
            preview_description: testoLeggibile(tpl.preview_description),
          }))
        : [];
    } else {
      erroreLista.value = (await messaggioErrore(res)) || t('Non è stato possibile caricare la libreria.');
    }
  } catch (err) {
    console.error('fetchTemplates error:', err);
    erroreLista.value = t('Non è stato possibile caricare la libreria.');
  } finally {
    loading.value = false;
  }
}

// Zona e indice dove va il template. La posizione chiesta (p) è quella del clic sulla
// card; gli array delle zone si leggono AL MOMENTO dell'inserimento (dopo la fetch:
// intanto un Ctrl+Z può averli sostituiti). Le pagine complete vanno sempre
// nel body, anche dal «+» di header o footer: «Sostituisci tutto» e «Aggiungi in
// fondo» parlano della pagina, e una pagina intera in un header condiviso sarebbe un
// danno. Header e footer valgono solo in modalità unificata, altrimenti il canvas è
// il template aperto. Il menu contestuale dà la sezione cliccata (afterId): si
// inserisce subito dopo, nella sua zona; se non c'è più, in fondo alla zona.
function destinazione(tpl, mode, p = posizione.value) {
  if (isPagina(tpl) || !p) {
    return { zone: 'body', index: undefined, replace: mode === 'replace' };
  }
  const zonaValida = (z) => (builderStore.unifiedMode && (z === 'header' || z === 'footer') ? z : 'body');
  const zonaCliccata = p.afterId ? tilesStore.getZoneForTile(p.afterId) : null;
  const zone = zonaValida(zonaCliccata || p.zone);
  const arr = tilesStore.getZoneTiles(zone);
  if (zonaCliccata) {
    const i = arr.findIndex((r) => r && (r.id === p.afterId || findNodeById([r], p.afterId)));
    if (i >= 0) return { zone, index: i + 1, replace: false };
  }
  if (!p.afterId && Number.isInteger(p.index)) {
    return { zone, index: Math.min(Math.max(p.index, 0), arr.length), replace: false };
  }
  return { zone, index: undefined, replace: false };
}

// Il blocco da inserire. Quelli del catalogo passano dall'importazione: le loro foto, nella
// libreria remota, si copiano nella Libreria media del sito (la pagina non dipende da
// olotheme.com). Se non si può (demo, permessi, server più vecchio) si inserisce lo stesso,
// con le foto all'indirizzo remoto.
async function caricaBlocco(tpl) {
  if (tpl.source === 'catalogo') {
    try {
      const r = await fetch(`${oloData.restUrl}template-library/${tpl.id}/import`, {
        method: 'POST',
        headers: { 'X-WP-Nonce': oloData.nonce },
      });
      if (r.ok) return r;
    } catch (_) { /* si prova la lettura semplice */ }
  }
  return fetch(`${oloData.restUrl}template-library/${tpl.id}`, {
    headers: { 'X-WP-Nonce': oloData.nonce },
  });
}

async function insertTemplate(tpl, mode = 'append') {
  // Un doppio clic sulla card farebbe due fetch e due blocchi.
  if (inserendo.value) return;
  inserendo.value = true;
  inserendoId.value = tpl.id;
  // Letti al clic. Se la libreria si chiude e si riapre mentre il blocco si scarica,
  // vale la nuova apertura (altra posizione, altra scelta): questo non inserisce più.
  const apertura = aperture;
  const p = posizione.value;
  try {
    const res = await caricaBlocco(tpl);
    if (!res.ok) throw new Error('Fetch failed');
    const fullTpl = await res.json();
    if (apertura !== aperture) return;

    // Support both array and object content
    let content = fullTpl.content;
    if (content && !Array.isArray(content)) {
      content = Object.values(content);
    }

    if (!content || content.length === 0) {
      toast.error(t('Template vuoto o non valido'));
      return;
    }

    const dest = destinazione(tpl, mode, p);
    const inFondoAlBody = dest.zone === 'body' && !dest.replace
      && (dest.index === undefined || dest.index >= tilesStore.canvasTiles.length);

    // Un passo di annullo suo, come l'eliminazione e l'incolla: checkpoint prima e dopo.
    history.pushStateNow();
    const prima = history.puntoAttuale();
    // Copia con id nuovi + la preparazione del caricamento di una pagina (i {} del PHP
    // tornano oggetti: la rinomina dall'albero non si perde più al salvataggio).
    const nodes = tilesStore.inserisciContenuto(content, dest);
    if (!nodes.length) {
      toast.error(t('Template vuoto o non valido'));
      return;
    }
    // Dopo l'inserimento (la zona si legge dal nodo): segna header, footer o pagina e
    // fa partire l'avviso di zona condivisa.
    builderStore.markDirtyForTile(nodes[0].id);
    history.pushStateNow();
    const dopo = history.puntoAttuale();

    if (dest.replace) {
      // Le tile di prima non esistono più: l'inspector si chiude.
      builderStore.deselectTile();
    } else {
      builderStore.selectTile(nodes[0].id);
      requestScrollToTile(nodes[0].id);
    }

    const modello = dest.replace
      ? t('«%s» caricato')
      : (inFondoAlBody ? t('«%s» aggiunto in fondo alla pagina') : t('«%s» inserito'));
    const nome = String(tpl.name || '');
    // Le foto copiate nella Libreria media (blocchi del catalogo): si dice dove sono finite
    const copiate = Number(fullTpl.media?.copiate || 0);
    const foto = copiate > 0 ? ' · ' + t('foto nella Libreria media') : '';
    toast.action(modello.replace('%s', () => nome) + foto, t('Annulla'), () => {
      if (!history.annullaSeUltimo(prima, dopo)) {
        toast.info(t("L'inserimento non è più l'ultimo passo: usa Ctrl+Z per tornare indietro un passo alla volta"), 5000);
      }
    }, 6000, 'success');
    close();
  } catch (err) {
    console.error('insertTemplate error:', err);
    // Scelta superata da una nuova apertura: l'errore non riguarda più l'utente.
    if (apertura === aperture) toast.error(t('Errore nell\'inserimento del template'));
  } finally {
    // Dopo una riapertura il blocco appartiene alla nuova apertura (open() l'ha già
    // liberato, e una nuova scelta può essere in volo): non va toccato.
    if (apertura === aperture) { inserendo.value = false; inserendoId.value = null; }
  }
}

// ═══ Count elements recursively ═══
function countElements(node) {
  let count = 0;
  if (node.children) {
    for (const child of node.children) {
      if (child.type !== 'row' && child.type !== 'column') count++;
      count += countElements(child);
    }
  }
  return count;
}

// ═══ Deep clone a section for saving ═══
function cloneForSave(node) {
  const clone = { ...node, settings: { ...(node.settings || {}) }, style: { ...(node.style || {}) }, advanced: { ...(node.advanced || {}) } };
  // Remove runtime-only fields
  delete clone.settings._label;
  if (node.children) {
    clone.children = node.children.map(c => cloneForSave(c));
  }
  return clone;
}

// ═══ Save as template ═══
function openSaveDialog(section) {
  saveSection.value = section;
  saveName.value = section.settings?._label || '';
  saveCategory.value = 'custom';
  saveDescription.value = '';
  saving.value = false;
  saveDialogVisible.value = true;
  nextTick(() => {
    if (saveNameInput.value) {
      saveNameInput.value.focus();
      saveNameInput.value.select();
    }
  });
}

function closeSaveDialog() {
  saveDialogVisible.value = false;
  saveSection.value = null;
}

async function doSave() {
  if (!saveName.value.trim() || !saveSection.value || saving.value) return;
  saving.value = true;
  try {
    const content = [cloneForSave(saveSection.value)];
    const res = await fetch(`${oloData.restUrl}template-library/save`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-WP-Nonce': oloData.nonce,
      },
      body: JSON.stringify({
        name: saveName.value.trim(),
        category: saveCategory.value,
        description: saveDescription.value.trim(),
        content: content,
      }),
    });
    if (!res.ok) throw Object.assign(new Error('Save failed'), { messaggio: await messaggioErrore(res) });
    const result = await res.json();
    // Lista non ancora scaricata (salvataggio dal menu prima di aprire la libreria):
    // una voce sola la renderebbe «già caricata» e open() mostrerebbe solo quella.
    const listaCaricata = templates.value.length > 0;
    toast.success(t('Template salvato!'));
    closeSaveDialog();
    // Add to local list, coi valori salvati dal server (sanificati) quando li dà, letti
    // come in fetchTemplates(): subito ciò che si vedrà dopo il ricaricamento.
    if (listaCaricata) {
      templates.value.push({
        id: result.id,
        name: testoLeggibile(result.name ?? saveName.value.trim()),
        category: saveCategory.value,
        preview_description: testoLeggibile(result.preview_description ?? saveDescription.value.trim()),
        is_user: true,
        content: content,
      });
    }
  } catch (err) {
    console.error('doSave error:', err);
    toast.error(t('Errore nel salvataggio del template') + (err.messaggio ? ' — ' + err.messaggio : ''), err.messaggio ? 6000 : undefined);
  } finally {
    saving.value = false;
  }
}

// ═══ Delete template ═══
function confirmDelete(tpl) {
  deleteTarget.value = tpl;
  deleteDialogVisible.value = true;
  deleting.value = false;
}

async function doDelete() {
  if (!deleteTarget.value || deleting.value) return;
  deleting.value = true;
  try {
    const res = await fetch(`${oloData.restUrl}template-library/user/${deleteTarget.value.id}`, {
      method: 'DELETE',
      headers: { 'X-WP-Nonce': oloData.nonce },
    });
    if (!res.ok) throw Object.assign(new Error('Delete failed'), { messaggio: await messaggioErrore(res) });
    toast.success(t('Template eliminato'));
    templates.value = templates.value.filter(t => t.id !== deleteTarget.value.id);
    deleteDialogVisible.value = false;
    deleteTarget.value = null;
  } catch (err) {
    console.error('doDelete error:', err);
    toast.error(t('Errore nell\'eliminazione') + (err.messaggio ? ' — ' + err.messaggio : ''), err.messaggio ? 6000 : undefined);
  } finally {
    deleting.value = false;
  }
}

// pos: { zone, index, query } dal «+», { afterId, zone } dal menu contestuale; senza (o
// con un Event, dalla toolbar) il blocco va in fondo al body. Sempre riazzerata: la
// libreria resta montata e la posizione di un «+» precedente non va riusata.
// query = ciò che c'è nel campo «Cerca in libreria» del pannello Inserisci (anche vuoto):
// diventa la ricerca della libreria. Senza, la ricerca resta quella di prima.
function open(pos) {
  const semplice = !!pos && typeof pos === 'object' && Object.getPrototypeOf(pos) === Object.prototype;
  posizione.value = semplice
    ? {
        zone: typeof pos.zone === 'string' ? pos.zone : 'body',
        index: Number.isInteger(pos.index) ? pos.index : null,
        afterId: pos.afterId || null,
      }
    : null;
  if (semplice && typeof pos.query === 'string') searchQuery.value = pos.query;
  // Nuova apertura: un blocco ancora in arrivo da quella di prima non si inserisce più
  // (insertTemplate confronta il contatore) e le card tornano cliccabili.
  aperture++;
  inserendo.value = false;
  inserendoId.value = null;
  visible.value = true;
  if (templates.value.length === 0) {
    fetchTemplates();
  }
}

function close() {
  visible.value = false;
}

const dialogRef = ref(null);
const tplTrap = useFocusTrap(dialogRef, { onEscape: close });
// Trap attivo solo quando il modale principale è aperto e nessun sotto-dialog
// (salva/elimina/inserimento pagina) è in primo piano, così il focus può
// raggiungere i dialog annidati.
const _mainTrapActive = computed(() => visible.value && !saveDialogVisible.value && !deleteDialogVisible.value && pageInsertMode.value !== 'ask');
watch(_mainTrapActive, (v) => { if (v) { nextTick(() => tplTrap.activate()); } else { tplTrap.deactivate(); } });

defineExpose({ open, close, visible, openSaveDialog });
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.15s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

/* ── Chrome chiaro del builder ──
   I colori stanno qui, sulla radice di ogni finestra (libreria e dialoghi): l'accento
   del chrome è locale come in BuilderSidebar, perché nessun antenato della libreria lo
   definisce e var() senza valore toglieva bordi e fondo dei pulsanti. */
.olo-tpl-overlay {
  --olo-ui-accent: #e8622a;
  --tl-ink: #1a1a1a;
  --tl-ink-2: #555;
  --tl-mute: #888;
  --tl-faint: #a3a3a3;
  --tl-line: rgba(0, 0, 0, 0.08);
  --tl-line-2: rgba(0, 0, 0, 0.12);
  --tl-fill: rgba(0, 0, 0, 0.04);
  --tl-ring: rgba(232, 98, 42, 0.18);
  --tl-danger: #dc2626;
  position: fixed;
  inset: 0;
  z-index: 99000;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 16px;
  background: rgba(17, 17, 17, 0.38);
}
.olo-tpl-overlay--top {
  z-index: 99500;
}

.olo-tpl-dialog {
  width: 900px;
  max-width: 100%;
  max-height: 85vh;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  background: rgba(255, 255, 255, 0.96);
  backdrop-filter: blur(24px) saturate(180%);
  -webkit-backdrop-filter: blur(24px) saturate(180%);
  border: 1px solid var(--tl-line-2);
  border-radius: 14px;
  box-shadow: 0 24px 80px rgba(0, 0, 0, 0.18), 0 0 0 1px rgba(0, 0, 0, 0.04);
  color: var(--tl-ink);
}

/* Intestazione */
.olo-tpl-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  padding: 14px 20px;
  border-bottom: 1px solid var(--tl-line);
}
.olo-tpl-title {
  display: flex;
  align-items: center;
  gap: 10px;
  min-width: 0;
  color: var(--olo-ui-accent);
}
.olo-tpl-title h3 {
  margin: 0;
  padding: 0;
  font-size: 14px;
  font-weight: 600;
  line-height: 1.3;
  color: var(--tl-ink);
}
.olo-tpl-tools {
  display: flex;
  align-items: center;
  gap: 10px;
}
.olo-tpl-searchbox {
  display: flex;
  align-items: center;
  gap: 8px;
  width: 230px;
  height: 34px;
  margin: 0;
  padding: 0 11px;
  background: #fff;
  border: 1px solid var(--tl-line-2);
  border-radius: 9px;
  color: var(--tl-mute);
  cursor: text;
  transition: border-color 0.15s, box-shadow 0.15s;
}
.olo-tpl-searchbox:focus-within {
  border-color: var(--olo-ui-accent);
  box-shadow: 0 0 0 3px var(--tl-ring);
}
.olo-tpl-searchbox svg {
  flex: none;
}
/* Il riquadro è la label: il campo è solo testo. forms.css di WordPress gli darebbe
   bordo, padding e l'anello blu al focus (input[type=text]:focus, 0,2,1). */
.olo-tpl-searchbox .olo-tpl-search[type="text"],
.olo-tpl-searchbox .olo-tpl-search[type="text"]:focus {
  flex: 1;
  min-width: 0;
  width: auto;
  height: auto;
  min-height: 0;
  margin: 0;
  padding: 0;
  border: 0;
  border-radius: 0;
  box-shadow: none;
  outline: none;
  font-size: 12.5px;
  line-height: 1.4;
}
.olo-tpl-search::placeholder {
  color: var(--tl-faint);
  opacity: 1;
}
.olo-tpl-x {
  flex: none;
  width: 30px;
  height: 30px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 0;
  border: 0;
  border-radius: 8px;
  background: transparent;
  color: var(--tl-mute);
  cursor: pointer;
  transition: background-color 0.15s, color 0.15s;
}
.olo-tpl-x:hover {
  background: var(--tl-fill);
  color: var(--tl-ink);
}
.olo-tpl-x:focus-visible {
  outline: 2px solid var(--olo-ui-accent);
  outline-offset: 1px;
}

/* Filtro categoria */
.olo-tpl-filter {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 9px 20px;
  background: rgba(0, 0, 0, 0.02);
  border-bottom: 1px solid var(--tl-line);
}
.olo-tpl-filter-label {
  margin: 0;
  color: var(--tl-mute);
  font-size: 11.5px;
  white-space: nowrap;
}
.olo-tpl-filter-select {
  flex: 1;
  max-width: 220px;
}
.olo-tpl-count {
  margin-left: auto;
  color: var(--tl-faint);
  font-size: 11px;
  font-variant-numeric: tabular-nums;
}

/* Griglia */
.olo-tpl-body {
  flex: 1;
  min-height: 0;
  overflow-y: auto;
  padding: 20px;
  background: rgba(0, 0, 0, 0.015);
}
.olo-tpl-state {
  padding: 40px 16px;
  text-align: center;
  color: var(--tl-mute);
  font-size: 13px;
}

/* Card: stesso rilievo al passaggio del mouse e al focus da tastiera, più il contorno
   con l'accento del chrome sul focus-visible. Al passaggio e al focus compare anche la
   descrizione (lo strato .olo-tpl-hover). Il cestino sta dentro la card. */
.olo-tpl-card {
  background: #fff;
  border: 1px solid var(--tl-line-2);
  border-radius: 12px;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
  cursor: pointer;
  overflow: hidden;
  transition: border-color 0.18s ease, box-shadow 0.18s ease, transform 0.18s ease;
}
.olo-tpl-card:hover,
.olo-tpl-card:focus-visible {
  border-color: var(--olo-ui-accent);
  box-shadow: 0 14px 30px -14px rgba(0, 0, 0, 0.28);
  transform: translateY(-3px);
}
.olo-tpl-card:focus-visible {
  outline: 2px solid var(--olo-ui-accent);
  outline-offset: 2px;
}
.olo-tpl-media {
  position: relative;
  overflow: hidden;
  border-bottom: 1px solid var(--tl-line);
}
.olo-tpl-img {
  display: block;
  width: 100%;
  height: auto;
  aspect-ratio: 16 / 10;
  object-fit: cover;
  object-position: top center;
  background: #f3f4f6;
}
/* Miniatura alle sue proporzioni (aspect-ratio in linea, dalla lista): lo spazio è riservato
   prima che l'immagine arrivi e niente si taglia */
.olo-tpl-img--naturale {
  object-position: center;
}

/* Elenco: blocchi a muratura (colonne CSS, ogni card intera), pagine su due colonne */
.olo-tpl-elenco--muro {
  columns: 3 220px;
  column-gap: 12px;
}
.olo-tpl-elenco--muro > .olo-tpl-card {
  display: block;
  break-inside: avoid;
  margin: 0 0 12px;
}
.olo-tpl-elenco--pagine {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 16px;
}

/* Inserimento in corso sulla card scelta */
.olo-tpl-busy {
  position: absolute;
  inset: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  background: rgba(255, 255, 255, 0.82);
  color: var(--tl-ink);
  font-size: 12px;
  font-weight: 600;
}
.olo-tpl-spin {
  width: 16px;
  height: 16px;
  border: 2px solid var(--tl-line);
  border-top-color: var(--olo-ui-accent);
  border-radius: 50%;
  animation: olo-tpl-gira 0.8s linear infinite;
}
@keyframes olo-tpl-gira {
  to { transform: rotate(360deg); }
}
@media (prefers-reduced-motion: reduce) {
  .olo-tpl-spin { animation-duration: 2.4s; }
}
.olo-tpl-card.is-busy {
  pointer-events: none;
}
.olo-tpl-hover {
  position: absolute;
  inset: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 12px;
  background: rgba(20, 20, 20, 0.74);
  opacity: 0;
  transition: opacity 0.15s;
}
.olo-tpl-hover span {
  color: #fff;
  font-size: 11px;
  line-height: 1.5;
  text-align: center;
}
.olo-tpl-hover--sm {
  padding: 10px;
}
.olo-tpl-hover--sm span {
  font-size: 10.5px;
}
.olo-tpl-card:hover .olo-tpl-hover,
.olo-tpl-card:focus-visible .olo-tpl-hover {
  opacity: 1;
}
.olo-tpl-info {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 6px;
  padding: 8px 10px 9px;
}
.olo-tpl-info-text {
  flex: 1;
  min-width: 0;
}
.olo-tpl-name-row {
  display: flex;
  align-items: center;
  gap: 5px;
}
.olo-tpl-name {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  color: var(--tl-ink);
  font-size: 12px;
  font-weight: 500;
}
.olo-tpl-badge {
  flex-shrink: 0;
  padding: 3px 6px;
  border-radius: 4px;
  font-size: 9px;
  font-weight: 600;
  line-height: 1;
  letter-spacing: 0.02em;
}
.olo-tpl-badge--page {
  background: rgba(232, 98, 42, 0.12);
  color: #b4451a;
}
.olo-tpl-badge--user {
  background: rgba(245, 158, 11, 0.16);
  color: #92400e;
}
.olo-tpl-cat {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  margin-top: 3px;
  color: var(--tl-mute);
  font-size: 10.5px;
  text-transform: capitalize;
}
.olo-tpl-dot {
  flex: none;
  width: 6px;
  height: 6px;
  border-radius: 50%;
}
/* Cestino dei template personali */
.olo-tpl-del {
  flex-shrink: 0;
  width: 24px;
  height: 24px;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0;
  border-radius: 6px;
  border: none;
  background: transparent;
  color: var(--tl-danger);
  cursor: pointer;
  opacity: 0.55;
  transition: opacity 0.15s, background-color 0.15s;
}
.olo-tpl-del:hover,
.olo-tpl-del:focus-visible {
  opacity: 1;
  background: rgba(220, 38, 38, 0.08);
}
.olo-tpl-del:focus-visible {
  outline: 2px solid var(--olo-ui-accent);
  outline-offset: 1px;
}

/* Piede */
.olo-tpl-foot {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 9px 20px;
  border-top: 1px solid var(--tl-line);
  background: rgba(0, 0, 0, 0.02);
  color: var(--tl-faint);
  font-size: 11px;
}
.olo-tpl-link {
  margin: -4px -8px;
  padding: 4px 8px;
  border: 0;
  border-radius: 6px;
  background: transparent;
  color: var(--tl-ink-2);
  font-size: 12px;
  cursor: pointer;
  transition: background-color 0.15s, color 0.15s;
}
.olo-tpl-link:hover {
  background: var(--tl-fill);
  color: var(--tl-ink);
}
.olo-tpl-link:focus-visible {
  outline: 2px solid var(--olo-ui-accent);
  outline-offset: 1px;
}

/* ── Dialoghi: salva, elimina, inserisci pagina ── */
.olo-tpl-box {
  max-width: 100%;
  background: #fff;
  border: 1px solid var(--tl-line-2);
  border-radius: 12px;
  box-shadow: 0 24px 64px rgba(0, 0, 0, 0.22);
  color: var(--tl-ink);
}
.olo-tpl-box--save {
  width: 420px;
}
.olo-tpl-box--del {
  width: 380px;
}
.olo-tpl-box--page {
  width: 400px;
  padding: 24px;
  outline: none;
}
.olo-tpl-box-head {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 16px 20px;
  border-bottom: 1px solid var(--tl-line);
}
.olo-tpl-box-title {
  color: var(--tl-ink);
  font-size: 14px;
  font-weight: 600;
}
.olo-tpl-accent {
  flex: none;
  color: var(--olo-ui-accent);
}
.olo-tpl-box-body {
  padding: 20px;
}
.olo-tpl-field {
  margin-bottom: 14px;
}
.olo-tpl-label {
  display: block;
  margin: 0 0 5px;
  color: var(--tl-ink-2);
  font-size: 11.5px;
  font-weight: 500;
}
/* Fondo, testo e colore del bordo li impone main.scss a ogni campo del builder
   (!important): il focus si disegna con l'ombra, che lì non è toccata. */
.olo-tpl-box .olo-tpl-input[type="text"],
.olo-tpl-box .olo-tpl-input[type="text"]:focus {
  width: 100%;
  box-sizing: border-box;
  height: auto;
  min-height: 0;
  margin: 0;
  padding: 8px 12px;
  border: 1px solid var(--tl-line-2);
  border-radius: 8px;
  outline: none;
  font-size: 13px;
  line-height: 1.4;
  transition: box-shadow 0.15s;
}
.olo-tpl-box .olo-tpl-input[type="text"] {
  box-shadow: none;
}
.olo-tpl-box .olo-tpl-input[type="text"]:focus {
  box-shadow: 0 0 0 1px var(--olo-ui-accent), 0 0 0 4px var(--tl-ring);
}
.olo-tpl-input::placeholder {
  color: var(--tl-faint);
  opacity: 1;
}
.olo-tpl-note {
  margin-bottom: 4px;
  padding: 9px 11px;
  border-radius: 8px;
  background: var(--tl-fill);
  font-size: 11px;
}
.olo-tpl-note-k {
  color: var(--tl-mute);
  font-size: 10.5px;
}
.olo-tpl-note-v {
  margin-left: 4px;
  color: var(--tl-ink);
  font-weight: 500;
}
.olo-tpl-note-gap {
  margin-left: 8px;
}
.olo-tpl-box-foot {
  display: flex;
  justify-content: flex-end;
  gap: 8px;
  padding: 12px 20px;
  border-top: 1px solid var(--tl-line);
}
.olo-tpl-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 7px 16px;
  border: 1px solid var(--tl-line-2);
  border-radius: 8px;
  background: #fff;
  color: var(--tl-ink-2);
  font-size: 12px;
  font-weight: 500;
  line-height: 1.4;
  cursor: pointer;
  transition: background-color 0.15s, color 0.15s, filter 0.15s;
}
.olo-tpl-btn:hover {
  background: var(--tl-fill);
  color: var(--tl-ink);
}
.olo-tpl-btn:focus-visible {
  outline: 2px solid var(--olo-ui-accent);
  outline-offset: 2px;
}
.olo-tpl-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}
.olo-tpl-btn--primary,
.olo-tpl-btn--primary:hover {
  background: var(--olo-ui-accent);
  border-color: var(--olo-ui-accent);
  color: #fff;
}
.olo-tpl-btn--danger,
.olo-tpl-btn--danger:hover {
  background: var(--tl-danger);
  border-color: var(--tl-danger);
  color: #fff;
}
.olo-tpl-btn--primary:hover:not(:disabled),
.olo-tpl-btn--danger:hover:not(:disabled) {
  filter: brightness(0.93);
}
.olo-tpl-btn--soft {
  background: var(--tl-fill);
  border-color: var(--tl-line);
  color: var(--tl-ink);
}
.olo-tpl-btn--soft:hover {
  background: rgba(0, 0, 0, 0.07);
}
.olo-tpl-btn--grow {
  flex: 1;
  padding-left: 12px;
  padding-right: 12px;
}
.olo-tpl-del-head {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 14px;
}
.olo-tpl-del-icon {
  flex: none;
  width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 8px;
  background: rgba(220, 38, 38, 0.08);
  color: var(--tl-danger);
}
.olo-tpl-sub {
  margin-top: 2px;
  color: var(--tl-mute);
  font-size: 11.5px;
}
.olo-tpl-text {
  margin: 0;
  color: var(--tl-ink-2);
  font-size: 12.5px;
  line-height: 1.55;
}
.olo-tpl-text strong {
  color: var(--tl-ink);
  font-weight: 600;
}
.olo-tpl-page-head {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 12px;
}
.olo-tpl-text--page {
  margin-bottom: 18px;
}
.olo-tpl-page-actions {
  display: flex;
  gap: 8px;
}
</style>
