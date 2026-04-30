<template>
  <div class="olo-ext-tile">
    <!-- Anteprima server-rendered (WYSIWYG fedele) -->
    <div
      v-if="serverHtml"
      class="olo-ext-tile__server"
      v-html="serverHtml"
    ></div>

    <!-- Skeleton loading mentre arriva la prima preview -->
    <div
      v-else-if="loading"
      class="olo-ext-tile__skeleton"
      :style="wrapStyle"
    >
      <div class="olo-ext-tile__skel-header">
        <span class="olo-ext-icon" :style="iconStyle" v-html="dashicon"></span>
        <span style="font-size:14px;font-weight:600;color:#1E293B">{{ label }}</span>
        <span class="olo-ext-tile__spinner" :title="t('Caricamento anteprima...')"></span>
      </div>
      <div class="olo-ext-tile__skel-line" style="width:80%"></div>
      <div class="olo-ext-tile__skel-line" style="width:60%"></div>
      <div class="olo-ext-tile__skel-line" style="width:90%"></div>
    </div>

    <!-- Fallback statico (errore o tile sconosciuta) -->
    <div
      v-else
      class="olo-ext-tile__fallback"
      :style="wrapStyle"
    >
      <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px">
        <span class="olo-ext-icon" :style="iconStyle" v-html="dashicon"></span>
        <span style="font-size:14px;font-weight:600;color:#1E293B">{{ label }}</span>
      </div>
      <div style="font-size:12px;color:#64748B;line-height:1.4">{{ description }}</div>
      <div v-if="settingsSummary" style="margin-top:8px;font-size:11px;color:#94A3B8;font-family:monospace">{{ settingsSummary }}</div>
      <div
        v-if="error"
        style="margin-top:10px;font-size:11px;color:#dc2626;display:flex;align-items:center;gap:6px"
      >
        <span>⚠</span>
        <span>{{ t('Anteprima non disponibile') }}</span>
        <button
          type="button"
          class="olo-ext-tile__retry"
          @click.stop="forcePreview"
        >{{ t('Riprova') }}</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, watch, onMounted, onBeforeUnmount } from 'vue';
import { getElementDef } from '@/config/elementRegistry.js';
import { t } from '@/i18n';

const props = defineProps({
  // Nodo tile completo (preferito quando disponibile)
  tile:     { type: Object, default: null },
  // Settings legacy (alcuni call-site passano solo questi)
  settings: { type: Object, default: () => ({}) },
  tileId:   { type: String, default: '' },
});

// ──────────────────────────────────────────────────────────────────────
// Tipo + settings unificati (compat tile/settings legacy)
// ──────────────────────────────────────────────────────────────────────
const tileType = computed(() => props.tile?.type || props.settings?._type || '');
const tileSettings = computed(() => props.tile?.settings || props.settings || {});

const tileDef = computed(() => getElementDef(tileType.value));
const label = computed(() => tileDef.value?.name || t('Elemento esterno'));
const rawIcon = computed(() => tileDef.value?.icon || 'dashicons-admin-generic');

// ──────────────────────────────────────────────────────────────────────
// Live server-side preview via /olo/v1/builder/render-tile
// ──────────────────────────────────────────────────────────────────────
const serverHtml = ref('');
const loading   = ref(false);
const error     = ref(false);

let abortCtrl     = null;
let debounceTimer = null;
let lastKey       = '';

function buildTileNode() {
  // Se abbiamo già il nodo intero, lo riusiamo (per id/children/zone)
  if (props.tile && props.tile.type) {
    return {
      id: props.tile.id || props.tileId || '',
      type: props.tile.type,
      settings: props.tile.settings || {},
      children: props.tile.children || [],
    };
  }
  return {
    id: props.tileId || '',
    type: tileType.value,
    settings: tileSettings.value,
    children: [],
  };
}

async function fetchPreview(force = false) {
  const type = tileType.value;
  if (!type) return;

  const node = buildTileNode();
  const key = JSON.stringify({ t: node.type, s: node.settings });
  if (!force && key === lastKey) return;
  lastKey = key;

  if (abortCtrl) abortCtrl.abort();
  abortCtrl = new AbortController();

  loading.value = true;
  error.value = false;

  try {
    const restUrl = (window.oloData?.restUrl || '/wp-json/olo/v1').replace(/\/$/, '');
    const res = await fetch(restUrl + '/builder/render-tile', {
      method:  'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-WP-Nonce':   window.oloData?.nonce || '',
      },
      body:   JSON.stringify({ tile: node }),
      signal: abortCtrl.signal,
    });
    const data = await res.json();
    if (data && typeof data.html === 'string' && data.html.trim()) {
      serverHtml.value = data.html;
      error.value = false;
    } else {
      // PHP non ha tornato HTML utile: fallback al placeholder
      if (!serverHtml.value) error.value = true;
    }
  } catch (e) {
    if (e.name !== 'AbortError') {
      console.warn('[ExternalTile] preview fetch failed:', e);
      if (!serverHtml.value) error.value = true;
    }
  } finally {
    loading.value = false;
  }
}

function schedulePreview() {
  if (debounceTimer) clearTimeout(debounceTimer);
  debounceTimer = setTimeout(() => fetchPreview(false), 350);
}

function forcePreview() {
  lastKey = '';
  fetchPreview(true);
}

watch(
  () => JSON.stringify(tileSettings.value),
  () => schedulePreview(),
);
watch(
  () => tileType.value,
  () => {
    serverHtml.value = '';
    schedulePreview();
  },
);

onMounted(() => fetchPreview(false));
onBeforeUnmount(() => {
  if (debounceTimer) clearTimeout(debounceTimer);
  if (abortCtrl) abortCtrl.abort();
});

// ──────────────────────────────────────────────────────────────────────
// Fallback placeholder (descrizioni hardcoded per back-compat)
// ──────────────────────────────────────────────────────────────────────
const description = computed(() => {
  const type = tileType.value;
  const descs = {
    propertygrid:        t('Griglia di immobili con filtri e card cliccabili'),
    propertysearch:      t('Form di ricerca con filtri tipo, prezzo, camere'),
    propertymap:         t('Mappa interattiva con marker degli immobili'),
    propertymapsearch:   t('Mappa + lista risultati con filtri avanzati'),
    propertyfeatured:    t('Carosello immobili in evidenza'),
    propertystats:       t('Contatori statistiche (vendita, affitto, venduti)'),
    propertyhero:        t('Banner hero con immagine, overlay ed effetti'),
    propertyinfo:        t('Scheda completa con info, gallery e amenities'),
    propertygallery:     t('Galleria immagini con lightbox nativo'),
    propertydescription: t('Descrizione immobile con "Leggi tutto"'),
    propertyaddress:     t('Indirizzo con mini-mappa e link Google Maps'),
    propertyfeatures:    t('Dotazioni raggruppate per categoria'),
    propertyprice:       t('Prezzo con badge e spese condominiali'),
    propertyspecs:       t('Griglia icone specifiche (mq, locali, bagni…)'),
    propertyvideo:       t('Player video YouTube/Vimeo/self-hosted'),
    propertyexcerpt:     t('Estratto breve dell\'immobile'),
    propertyrules:       t('Note legali e disclaimer'),
    propertycta:         t('Contatto agente con foto, telefono, WhatsApp'),
    propertycard:        t('Card singolo immobile con badge e prezzo'),
    // ── Accommodation (olo-booking) ──
    'ac-hero':                  t('Hero cinematic struttura ricettiva (5 layout, parallax, shape divider)'),
    'ac-card':                  t('Card riusabile struttura con badge, prezzo e CTA'),
    'ac-grid':                  t('Griglia multi-strutture con filtri'),
    'ac-related':                t('Strutture correlate con card cliccabili'),
    'ac-booking-form':          t('Form prenotazione con date e price preview'),
    'ac-availability-calendar': t('Calendario disponibilità con mese navigabile'),
    'ac-gallery':               t('Galleria immagini con lightbox'),
    'ac-hero-video':            t('Hero con background video'),
    'ac-video':                 t('Video tour della struttura'),
    'ac-pricing-seasons':       t('Tabella tariffe per stagione'),
    'ac-reviews':               t('Recensioni con stelle e media'),
    'ac-host-info':             t('Card info host con foto e contatti'),
    'ac-stats':                 t('Statistiche (capacità, mq, camere, bagni)'),
    'ac-description':           t('Descrizione long-form della struttura'),
    'ac-amenities':             t('Servizi e dotazioni con icone'),
    'ac-map':                   t('Mappa Leaflet con pin posizione'),
    'ac-features':              t('Specifiche tecniche (capacità/mq/camere/bagni)'),
    'ac-faq':                   t('Domande frequenti accordion'),
    'ac-address':               t('Indirizzo con città e altitudine'),
    'ac-rules':                 t('Regole della casa e check-in/out'),
    'ac-search':                t('Form ricerca disponibilità struttura'),
    'ac-breadcrumb-hero':       t('Hero con breadcrumb sopra immagine'),
    'ac-cta':                   t('Banner call-to-action standalone'),
    'ac-certifications':        t('Certificazioni e badge ufficiali'),
    'ac-contact-form':          t('Form contatti dedicato struttura'),
  };
  return descs[type] || tileDef.value?.placeholder || t('Questo elemento viene renderizzato in anteprima live.');
});

// Riepilogo settings nel fallback
const settingsSummary = computed(() => {
  const s = tileSettings.value || {};
  const parts = [];
  if (s.columns)   parts.push(s.columns + ' col');
  if (s.layout)    parts.push(s.layout);
  if (s.style)     parts.push(s.style);
  if (s.max_posts) parts.push('max ' + s.max_posts);
  return parts.length ? parts.join(' · ') : '';
});

// ──────────────────────────────────────────────────────────────────────
// Stili statici (icona / wrapper / skeleton)
// ──────────────────────────────────────────────────────────────────────
const dashicon = computed(() => {
  const icon = rawIcon.value || '';
  if (icon.startsWith('dashicons-')) {
    return '<span class="dashicons ' + icon + '" style="font-size:20px;width:20px;height:20px"></span>';
  }
  return '<span style="font-size:18px">' + (icon || '🔌') + '</span>';
});

const iconStyle = computed(() => ({
  width: '36px',
  height: '36px',
  display: 'flex',
  alignItems: 'center',
  justifyContent: 'center',
  borderRadius: '8px',
  background: '#EEF2FF',
  color: '#4F46E5',
  flexShrink: 0,
}));

const wrapStyle = computed(() => ({
  padding: '16px 20px',
  background: 'linear-gradient(135deg, #F8FAFC, #EEF2FF)',
  border: '1px solid #CBD5E1',
  borderRadius: '10px',
  minHeight: '60px',
}));
</script>

<style>
.olo-ext-tile { position: relative; }
.olo-ext-tile__server { /* contenitore HTML server-rendered: niente reset, lascia stili PHP */ }

.olo-ext-tile__skeleton,
.olo-ext-tile__fallback { user-select: none; }

.olo-ext-tile__skel-header {
  display: flex; align-items: center; gap: 10px; margin-bottom: 12px;
}
.olo-ext-tile__skel-line {
  height: 12px; margin-top: 8px; border-radius: 6px;
  background: linear-gradient(90deg, #E2E8F0 0%, #F1F5F9 50%, #E2E8F0 100%);
  background-size: 200% 100%;
  animation: olo-ext-shimmer 1.4s linear infinite;
}
.olo-ext-tile__spinner {
  width: 14px; height: 14px; border-radius: 50%;
  border: 2px solid #CBD5E1; border-top-color: #4F46E5;
  margin-left: auto;
  animation: olo-ext-spin .8s linear infinite;
}
.olo-ext-tile__retry {
  margin-left: auto; padding: 2px 8px; font-size: 11px;
  background: #FEE2E2; color: #991B1B; border: 1px solid #FCA5A5;
  border-radius: 4px; cursor: pointer;
}
.olo-ext-tile__retry:hover { background: #FECACA; }

@keyframes olo-ext-shimmer {
  from { background-position: 200% 0 }
  to   { background-position: -200% 0 }
}
@keyframes olo-ext-spin {
  to { transform: rotate(360deg) }
}
</style>
