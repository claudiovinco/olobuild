<template>
  <div class="olo-external-tile-placeholder" :style="wrapStyle">
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px">
      <span class="olo-ext-icon" :style="iconStyle" v-html="dashicon"></span>
      <span style="font-size:14px;font-weight:600;color:#1E293B">{{ label }}</span>
    </div>
    <div style="font-size:12px;color:#64748B;line-height:1.4">{{ description }}</div>
    <div v-if="settingsSummary" style="margin-top:8px;font-size:11px;color:#94A3B8;font-family:monospace">{{ settingsSummary }}</div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { getElementDef } from '@/config/elementRegistry.js';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
  tileId: { type: String, default: '' },
});

const tileDef = computed(() => {
  const type = props.settings?._type || '';
  return getElementDef(type);
});

const label = computed(() => tileDef.value?.name || 'Elemento esterno');
const rawIcon = computed(() => tileDef.value?.icon || 'dashicons-admin-generic');

const description = computed(() => {
  const type = props.settings?._type || '';
  const descs = {
    propertygrid: 'Griglia di immobili con filtri e card cliccabili',
    propertysearch: 'Form di ricerca con filtri tipo, prezzo, camere',
    propertymap: 'Mappa interattiva con marker degli immobili',
    propertymapsearch: 'Mappa + lista risultati con filtri avanzati',
    propertyfeatured: 'Carosello immobili in evidenza',
    propertystats: 'Contatori statistiche (vendita, affitto, venduti)',
    propertyhero: 'Banner hero con immagine, overlay ed effetti',
    propertyinfo: 'Scheda completa con info, gallery e amenities',
    propertygallery: 'Galleria immagini con lightbox nativo',
    propertydescription: 'Descrizione immobile con "Leggi tutto"',
    propertyaddress: 'Indirizzo con mini-mappa e link Google Maps',
    propertyfeatures: 'Dotazioni raggruppate per categoria',
    propertyprice: 'Prezzo con badge e spese condominiali',
    propertyspecs: 'Griglia icone specifiche (mq, locali, bagni...)',
    propertyvideo: 'Player video YouTube/Vimeo/self-hosted',
    propertyexcerpt: 'Estratto breve dell\'immobile',
    propertyrules: 'Note legali e disclaimer',
    propertycta: 'Contatto agente con foto, telefono, WhatsApp',
    propertycard: 'Card singolo immobile con badge e prezzo',
  };
  return descs[type] || tileDef.value?.placeholder || 'Questo elemento viene renderizzato in anteprima live.';
});

// Show key settings
const settingsSummary = computed(() => {
  const s = props.settings || {};
  const parts = [];
  if (s.columns) parts.push(s.columns + ' col');
  if (s.layout) parts.push(s.layout);
  if (s.style) parts.push(s.style);
  if (s.max_posts) parts.push('max ' + s.max_posts);
  return parts.length ? parts.join(' · ') : '';
});

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
