<template>
    <transition name="fade">
      <div
        v-if="visible"
        class="mb-flex mb-items-center mb-justify-center"
        style="position:fixed;inset:0;z-index:99000;background:rgba(0,0,0,0.6)"
        @click.self="close"
      >
        <div
          ref="dialogRef"
          class="mb-bg-gray-800 mb-border mb-border-gray-600 mb-rounded-xl mb-shadow-2xl mb-w-[900px] mb-max-h-[85vh] mb-flex mb-flex-col mb-overflow-hidden"
          @click.stop
        >
          <!-- Header -->
          <div class="mb-flex mb-items-center mb-justify-between mb-px-5 mb-py-3 mb-border-b mb-border-gray-700">
            <div class="mb-flex mb-items-center mb-gap-2">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mb-text-primary-400">
                <rect width="18" height="18" x="3" y="3" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/>
              </svg>
              <h3 class="mb-text-white mb-text-sm mb-font-semibold mb-m-0">{{ t('Blocchi & Pagine') }}</h3>
            </div>
            <!-- Search -->
            <div class="mb-flex mb-items-center mb-gap-3">
              <div class="mb-relative">
                <svg class="mb-absolute mb-left-2 mb-top-1/2 mb--translate-y-1/2 mb-text-gray-500" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input
                  v-model="searchQuery"
                  type="text"
                  :placeholder="t('Cerca template...')"
                  class="mb-bg-gray-700 mb-border mb-border-gray-600 mb-rounded-lg mb-text-xs mb-text-gray-200 mb-pl-8 mb-pr-3 mb-py-1.5 mb-w-48 focus:mb-outline-none focus:mb-border-primary-500 mb-placeholder-gray-500"
                />
              </div>
              <button @click="close" class="mb-text-gray-400 hover:mb-text-white mb-transition-colors" :aria-label="t('Chiudi')">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
              </button>
            </div>
          </div>

          <!-- Category filter -->
          <div style="display:flex;align-items:center;gap:10px;padding:8px 20px;background:#111827;border-bottom:1px solid #374151">
            <label style="color:#9CA3AF;font-size:11px;white-space:nowrap">{{ t('Categoria:') }}</label>
            <div style="flex:1;max-width:220px">
              <FieldSelect
                ui="dropdown"
                theme="dark"
                :modelValue="activeCategory"
                :options="categoryFilterOptions"
                @update:modelValue="activeCategory = $event"
              />
            </div>
            <span style="color:#6B7280;font-size:10px">{{ filteredTemplates.length }} risultati</span>
          </div>

          <!-- Templates grid -->
          <div class="mb-flex-1 mb-overflow-y-auto mb-p-5">
            <div v-if="loading" class="mb-text-center mb-py-8">
              <span class="mb-text-sm mb-text-gray-400">{{ t('Caricamento template...') }}</span>
            </div>

            <div v-else-if="filteredTemplates.length === 0" class="mb-text-center mb-py-8">
              <p class="mb-text-sm mb-text-gray-500">{{ t('Nessun template trovato') }}</p>
            </div>

            <div :style="gridStyle">
              <div
                v-for="tpl in filteredTemplates"
                :key="tpl.id"
                class="olo-tpl-card"
                style="background:#374151;border-radius:8px;border:1px solid #4B5563;cursor:pointer;overflow:hidden;transition:border-color 0.15s, box-shadow 0.15s"
                @click="onCardClick(tpl)"
                @mouseenter="$event.currentTarget.style.borderColor='#e8622a';$event.currentTarget.style.boxShadow='0 4px 12px rgba(232,98,42,0.15)'"
                @mouseleave="$event.currentTarget.style.borderColor='#4B5563';$event.currentTarget.style.boxShadow='none'"
              >
                <!-- Thumbnail image (for page templates with thumbnail) -->
                <div v-if="tpl.thumbnail" style="position:relative;overflow:hidden;border-bottom:1px solid #4B5563" @mouseenter="$event.currentTarget.querySelector('.olo-tpl-hover').style.opacity='1'" @mouseleave="$event.currentTarget.querySelector('.olo-tpl-hover').style.opacity='0'">
                  <img :src="oloData.pluginUrl + tpl.thumbnail" :alt="tpl.name" style="display:block;width:100%;height:auto;aspect-ratio:16/10;object-fit:cover" loading="lazy" @error="$event.target.style.display='none'" />
                  <div class="olo-tpl-hover" style="position:absolute;inset:0;background:rgba(0,0,0,0.75);display:flex;align-items:center;justify-content:center;padding:12px;opacity:0;transition:opacity 0.15s">
                    <span style="color:#D1D5DB;font-size:11px;text-align:center;line-height:1.5">{{ tpl.preview_description }}</span>
                  </div>
                </div>
                <!-- SVG Preview (fallback) -->
                <div v-else style="position:relative;overflow:hidden;border-bottom:1px solid #4B5563" @mouseenter="$event.currentTarget.querySelector('.olo-tpl-hover').style.opacity='1'" @mouseleave="$event.currentTarget.querySelector('.olo-tpl-hover').style.opacity='0'">
                  <svg :viewBox="'0 0 260 120'" width="100%" style="display:block;background-color:var(--tpl-bg)" :style="{ '--tpl-bg': getPreviewBg(tpl) }">
                    <g v-for="(el, i) in getSvgElements(tpl)" :key="i">
                      <rect v-if="el.shape === 'rect'" :x="el.x" :y="el.y" :width="el.w" :height="el.h" :rx="el.rx || 0" :fill="el.fill" :opacity="el.opacity || 1" />
                      <line v-else-if="el.shape === 'line'" :x1="el.x1" :y1="el.y1" :x2="el.x2" :y2="el.y2" :stroke="el.stroke" :stroke-width="el.sw || 1" :opacity="el.opacity || 0.3" />
                      <circle v-else-if="el.shape === 'circle'" :cx="el.cx" :cy="el.cy" :r="el.r" :fill="el.fill" :opacity="el.opacity || 1" />
                    </g>
                  </svg>
                  <!-- Hover description -->
                  <div class="olo-tpl-hover" style="position:absolute;inset:0;background:rgba(0,0,0,0.75);display:flex;align-items:center;justify-content:center;padding:10px;opacity:0;transition:opacity 0.15s">
                    <span style="color:#D1D5DB;font-size:10px;text-align:center;line-height:1.5">{{ tpl.preview_description }}</span>
                  </div>
                </div>
                <!-- Info -->
                <div style="padding:6px 10px;display:flex;align-items:center;justify-content:space-between;gap:4px">
                  <div style="min-width:0;flex:1">
                    <div style="display:flex;align-items:center;gap:4px">
                      <span style="font-size:11px;font-weight:500;color:#E5E7EB;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ tpl.name }}</span>
                      <span v-if="tpl.category === 'page'" style="font-size:8px;padding:1px 5px;background:rgba(232,98,42,0.15);color:#F6A06B;border-radius:3px;flex-shrink:0">{{ t('Pagina') }}</span>
                      <span v-if="tpl.is_user" style="font-size:8px;padding:1px 4px;background:rgba(245,158,11,0.15);color:#FCD34D;border-radius:3px;flex-shrink:0">{{ t('Personale') }}</span>
                    </div>
                    <span style="font-size:9px;text-transform:capitalize" :style="{ color: getCategoryColor(tpl.category) }">{{ getCategoryLabel(tpl.category) }}</span>
                  </div>
                  <!-- Delete button for user templates -->
                  <button
                    v-if="tpl.is_user"
                    :title="t('Elimina template')"
                    style="flex-shrink:0;width:24px;height:24px;display:flex;align-items:center;justify-content:center;border-radius:4px;border:none;background:transparent;color:#EF4444;cursor:pointer;opacity:0.5;transition:opacity 0.15s"
                    @mouseenter="$event.currentTarget.style.opacity='1';$event.currentTarget.style.background='rgba(239,68,68,0.1)'"
                    @mouseleave="$event.currentTarget.style.opacity='0.5';$event.currentTarget.style.background='transparent'"
                    @click.stop="confirmDelete(tpl)"
                  >
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6"/><path d="M8 6V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg>
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Footer -->
          <div class="mb-px-5 mb-py-2 mb-border-t mb-border-gray-700 mb-bg-gray-900/50 mb-flex mb-items-center mb-justify-between">
            <span class="mb-text-[10px] mb-text-gray-500">{{ filteredTemplates.length }} / {{ templates.length }} template</span>
            <button @click="close" class="mb-text-xs mb-text-gray-400 hover:mb-text-gray-200 mb-transition-colors">{{ t('Chiudi') }}</button>
          </div>
        </div>
      </div>
    </transition>

    <!-- ═══ Save dialog ═══ -->
    <transition name="fade">
      <div
        v-if="saveDialogVisible"
        class="mb-flex mb-items-center mb-justify-center"
        style="position:fixed;inset:0;z-index:99500;background:rgba(0,0,0,0.7)"
        @click.self="closeSaveDialog"
      >
        <div
          style="background:#1F2937;border:1px solid #4B5563;border-radius:12px;width:420px;box-shadow:0 20px 40px rgba(0,0,0,0.5)"
          @click.stop
        >
          <!-- Header -->
          <div style="padding:16px 20px;border-bottom:1px solid #374151;display:flex;align-items:center;gap:8px">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#e8622a" stroke-width="2"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
            <span style="color:#F3F4F6;font-size:14px;font-weight:600">{{ t('Salva come template') }}</span>
          </div>
          <!-- Body -->
          <div style="padding:20px">
            <div style="margin-bottom:14px">
              <label style="display:block;color:#9CA3AF;font-size:11px;margin-bottom:4px">{{ t('Nome template') }}</label>
              <input
                ref="saveNameInput"
                v-model="saveName"
                type="text"
                :placeholder="t('es. Hero con video e CTA')"
                style="width:100%;background:#374151;border:1px solid #4B5563;border-radius:6px;color:#E5E7EB;padding:8px 12px;font-size:13px;outline:none;box-sizing:border-box"
                @focus="$event.target.style.borderColor='#e8622a'"
                @blur="$event.target.style.borderColor='#4B5563'"
                @keydown.enter="doSave"
              />
            </div>
            <div style="margin-bottom:14px">
              <label style="display:block;color:#9CA3AF;font-size:11px;margin-bottom:4px">{{ t('Categoria') }}</label>
              <FieldSelect
                ui="dropdown"
                theme="dark"
                :modelValue="saveCategory"
                :options="saveCategorySelectOptions"
                @update:modelValue="saveCategory = $event"
              />
            </div>
            <div style="margin-bottom:14px">
              <label style="display:block;color:#9CA3AF;font-size:11px;margin-bottom:4px">{{ t('Descrizione (opzionale)') }}</label>
              <input
                v-model="saveDescription"
                type="text"
                :placeholder="t('Breve descrizione del template')"
                style="width:100%;background:#374151;border:1px solid #4B5563;border-radius:6px;color:#E5E7EB;padding:8px 12px;font-size:13px;outline:none;box-sizing:border-box"
                @focus="$event.target.style.borderColor='#e8622a'"
                @blur="$event.target.style.borderColor='#4B5563'"
              />
            </div>
            <!-- Preview info -->
            <div v-if="saveSection" style="padding:8px 10px;background:#111827;border-radius:6px;margin-bottom:16px">
              <span style="color:#6B7280;font-size:10px">{{ t('Sezione:') }} </span>
              <span style="color:#D1D5DB;font-size:11px">{{ saveSection.settings?._label || 'Sezione' }}</span>
              <span style="color:#6B7280;font-size:10px;margin-left:8px">{{ countElements(saveSection) }} elementi</span>
            </div>
          </div>
          <!-- Footer -->
          <div style="padding:12px 20px;border-top:1px solid #374151;display:flex;justify-content:flex-end;gap:8px">
            <button
              @click="closeSaveDialog"
              style="padding:7px 16px;border-radius:6px;border:1px solid #4B5563;background:transparent;color:#9CA3AF;font-size:12px;cursor:pointer"
            >{{ t('Annulla') }}</button>
            <button
              @click="doSave"
              :disabled="!saveName.trim() || saving"
              style="padding:7px 16px;border-radius:6px;border:none;background:#e8622a;color:#fff;font-size:12px;font-weight:500;cursor:pointer;transition:opacity 0.15s"
              :style="{ opacity: (!saveName.trim() || saving) ? '0.5' : '1' }"
            >{{ saving ? 'Salvataggio...' : 'Salva template' }}</button>
          </div>
        </div>
      </div>
    </transition>

    <!-- ═══ Delete confirm dialog ═══ -->
    <transition name="fade">
      <div
        v-if="deleteDialogVisible"
        class="mb-flex mb-items-center mb-justify-center"
        style="position:fixed;inset:0;z-index:99500;background:rgba(0,0,0,0.7)"
        @click.self="deleteDialogVisible = false"
      >
        <div
          style="background:#1F2937;border:1px solid #4B5563;border-radius:12px;width:380px;box-shadow:0 20px 40px rgba(0,0,0,0.5)"
          @click.stop
        >
          <div style="padding:20px">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px">
              <div style="width:36px;height:36px;border-radius:8px;background:rgba(239,68,68,0.1);display:flex;align-items:center;justify-content:center">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#EF4444" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6"/><path d="M8 6V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg>
              </div>
              <div>
                <div style="color:#F3F4F6;font-size:14px;font-weight:600">{{ t('Elimina template') }}</div>
                <div style="color:#9CA3AF;font-size:11px">{{ t('Questa azione non può essere annullata') }}</div>
              </div>
            </div>
            <p style="color:#D1D5DB;font-size:12px;margin:0 0 4px">Vuoi eliminare il template <strong style="color:#FCD34D">{{ deleteTarget?.name }}</strong>?</p>
          </div>
          <div style="padding:12px 20px;border-top:1px solid #374151;display:flex;justify-content:flex-end;gap:8px">
            <button
              @click="deleteDialogVisible = false"
              style="padding:7px 16px;border-radius:6px;border:1px solid #4B5563;background:transparent;color:#9CA3AF;font-size:12px;cursor:pointer"
            >{{ t('Annulla') }}</button>
            <button
              @click="doDelete"
              :disabled="deleting"
              style="padding:7px 16px;border-radius:6px;border:none;background:#EF4444;color:#fff;font-size:12px;font-weight:500;cursor:pointer;transition:opacity 0.15s"
              :style="{ opacity: deleting ? '0.5' : '1' }"
            >{{ deleting ? 'Eliminazione...' : 'Elimina' }}</button>
          </div>
        </div>
      </div>
    </transition>

    <!-- Page template insert confirmation dialog -->
    <transition name="fade">
      <div v-if="pageInsertMode === 'ask'" style="position:fixed;inset:0;z-index:99500;background:rgba(0,0,0,0.7);display:flex;align-items:center;justify-content:center" @click.self="cancelPageInsert">
        <div style="background:#1F2937;border:1px solid #374151;border-radius:12px;padding:24px;max-width:400px;width:90%;box-shadow:0 20px 60px rgba(0,0,0,0.5)" @click.stop>
          <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#F6A06B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/></svg>
            <span style="color:#E5E7EB;font-size:14px;font-weight:600">{{ t('Inserisci pagina completa') }}</span>
          </div>
          <p style="color:#9CA3AF;font-size:12px;line-height:1.5;margin:0 0 16px">
            Il canvas contiene già del contenuto. Come vuoi procedere con il template <strong style="color:#E5E7EB">{{ pendingPageTpl?.name }}</strong>?
          </p>
          <div style="display:flex;gap:8px">
            <button @click="confirmPageInsert('replace')" style="flex:1;padding:8px 12px;background:#e8622a;color:#fff;border:none;border-radius:6px;font-size:12px;font-weight:500;cursor:pointer;transition:background 0.15s" @mouseenter="$event.target.style.background='#c44d1d'" @mouseleave="$event.target.style.background='#e8622a'">
              {{ t('Sostituisci tutto') }}
            </button>
            <button @click="confirmPageInsert('append')" style="flex:1;padding:8px 12px;background:#374151;color:#E5E7EB;border:1px solid #4B5563;border-radius:6px;font-size:12px;font-weight:500;cursor:pointer;transition:background 0.15s" @mouseenter="$event.target.style.background='#4B5563'" @mouseleave="$event.target.style.background='#374151'">
              {{ t('Aggiungi in fondo') }}
            </button>
            <button @click="cancelPageInsert" style="padding:8px 12px;background:transparent;color:#9CA3AF;border:1px solid #4B5563;border-radius:6px;font-size:12px;cursor:pointer;transition:color 0.15s" @mouseenter="$event.target.style.color='#E5E7EB'" @mouseleave="$event.target.style.color='#9CA3AF'">
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
import { useBuilderStore } from '@/stores/builder';
import { useToast } from '@/composables/useToast.js';
import FieldSelect from './fields/FieldSelect.vue';

const tilesStore = useTilesStore();
const builderStore = useBuilderStore();
const toast = useToast();

const oloData = window.oloData || {};

const visible = ref(false);
const loading = ref(false);
const templates = ref([]);
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
  { key: 'hero',          label: 'Hero',           color: '#6366F1' },
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

// Grid columns: 2 for page templates, 3 for others
const gridStyle = computed(() => {
  const isPage = activeCategory.value === 'page';
  return {
    display: 'grid',
    gridTemplateColumns: isPage ? 'repeat(2, 1fr)' : 'repeat(3, 1fr)',
    gap: isPage ? '16px' : '12px',
  };
});

const pageInsertMode = ref(null); // null, 'replace', 'append'
const pendingPageTpl = ref(null);

function onCardClick(tpl) {
  if (tpl.category === 'page') {
    // Pagina completa: con canvas già pieno chiedi (sostituisci/accoda),
    // su canvas vuoto sostituisci direttamente.
    if (tilesStore.canvasTiles.length > 0) {
      pendingPageTpl.value = tpl;
      pageInsertMode.value = 'ask';
    } else {
      insertTemplate(tpl, 'replace');
    }
  } else {
    // Blocco/sezione: si accoda in fondo alla pagina, non la sostituisce.
    insertTemplate(tpl, 'append');
  }
}

function confirmPageInsert(mode) {
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

function getCategoryColor(cat) {
  return categoryDefs.find(c => c.key === cat)?.color || '#6B7280';
}

function getCategoryLabel(cat) {
  return categoryDefs.find(c => c.key === cat)?.label || cat;
}

function getPreviewBg(tpl) {
  const sec = tpl.content?.[0];
  return sec?.style?.bg_color || '#F9FAFB';
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
  const isDark = bgColor && /^#[0-3][0-9a-fA-F]{5}$/.test(bgColor);
  const elColor = isDark ? '#ffffff' : '#374151';
  const elColorLight = isDark ? '#ffffff' : '#9CA3AF';
  const accentHex = '#e8622a';

  if (!sec?.children?.length) {
    els.push({ shape: 'rect', x: W*0.2, y: 30, w: W*0.6, h: 8, fill: elColor, opacity: 0.35, rx: 2 });
    els.push({ shape: 'rect', x: W*0.3, y: 48, w: W*0.4, h: 5, fill: elColor, opacity: 0.2, rx: 2 });
    els.push({ shape: 'rect', x: W*0.35, y: 66, w: W*0.3, h: 12, fill: accentHex, opacity: 0.7, rx: 4 });
    return els;
  }

  const row = sec.children[0];
  const cols = row?.children || [];
  const numCols = cols.length || 1;
  const totalW = W - PAD * 2;
  const colGap = numCols > 1 ? 8 : 0;
  const availW = totalW - colGap * (numCols - 1);

  let colX = PAD;
  for (let ci = 0; ci < numCols; ci++) {
    const col = cols[ci];
    const fraction = widthFraction[col?.settings?.width] || (1 / numCols);
    const colW = availW * fraction;
    const children = col?.children || [];
    let y = 12;

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
      } else {
        const gw2 = colW * 0.6;
        els.push({ shape: 'rect', x: cx - gw2/2, y, w: gw2, h: 6, fill: elColor, opacity: 0.12, rx: 2 });
        y += 10;
      }
    }
    colX += colW + colGap;
  }
  return els;
}

async function fetchTemplates() {
  loading.value = true;
  try {
    const res = await fetch(`${oloData.restUrl}/template-library`, {
      headers: { 'X-WP-Nonce': oloData.nonce },
    });
    if (res.ok) {
      templates.value = await res.json();
    }
  } catch (err) {
    console.error('fetchTemplates error:', err);
  } finally {
    loading.value = false;
  }
}

async function insertTemplate(tpl, mode = 'append') {
  try {
    const res = await fetch(`${oloData.restUrl}/template-library/${tpl.id}`, {
      headers: { 'X-WP-Nonce': oloData.nonce },
    });
    if (!res.ok) throw new Error('Fetch failed');
    const fullTpl = await res.json();

    // Support both array and object content
    let content = fullTpl.content;
    if (content && !Array.isArray(content)) {
      content = Object.values(content);
    }

    if (!content || content.length === 0) {
      toast.error(t('Template vuoto o non valido'));
      return;
    }

    const nodes = regenerateIds(content);

    if (mode === 'replace') {
      // Clear existing canvas content
      tilesStore.canvasTiles.splice(0, tilesStore.canvasTiles.length);
    }

    for (const node of nodes) {
      tilesStore.canvasTiles.push(node);
    }
    builderStore.isDirty = true;
    toast.success(mode === 'append'
      ? t(`"${tpl.name}" aggiunto in fondo`)
      : t(`"${tpl.name}" caricato`));
    close();
  } catch (err) {
    console.error('insertTemplate error:', err);
    toast.error(t('Errore nell\'inserimento del template'));
  }
}

function regenerateIds(nodes) {
  return nodes.map(node => {
    const newNode = { ...node, id: generateId() };
    if (node.children && Array.isArray(node.children)) {
      newNode.children = regenerateIds(node.children);
    }
    return newNode;
  });
}

function generateId() {
  return 'tl-' + Math.random().toString(36).substring(2, 10);
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
    const res = await fetch(`${oloData.restUrl}/template-library/save`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-WP-Nonce': oloData.nonce,
      },
      body: JSON.stringify({
        name: saveName.value.trim(),
        category: saveCategory.value,
        content: content,
      }),
    });
    if (!res.ok) throw new Error('Save failed');
    const result = await res.json();
    toast.success(t('Template salvato!'));
    closeSaveDialog();
    // Add to local list
    templates.value.push({
      id: result.id,
      name: saveName.value.trim(),
      category: saveCategory.value,
      preview_description: saveDescription.value,
      is_user: true,
      content: content,
    });
  } catch (err) {
    console.error('doSave error:', err);
    toast.error(t('Errore nel salvataggio del template'));
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
    const res = await fetch(`${oloData.restUrl}/template-library/user/${deleteTarget.value.id}`, {
      method: 'DELETE',
      headers: { 'X-WP-Nonce': oloData.nonce },
    });
    if (!res.ok) throw new Error('Delete failed');
    toast.success(t('Template eliminato'));
    templates.value = templates.value.filter(t => t.id !== deleteTarget.value.id);
    deleteDialogVisible.value = false;
    deleteTarget.value = null;
  } catch (err) {
    console.error('doDelete error:', err);
    toast.error(t('Errore nell\'eliminazione'));
  } finally {
    deleting.value = false;
  }
}

function open() {
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
</style>
