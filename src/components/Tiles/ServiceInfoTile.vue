<template>
  <div class="mb-font-sans" :style="wrapStyle">
    <!-- Hero -->
    <div :style="heroStyle">
      <div style="position:absolute;inset:0;background:linear-gradient(to bottom,rgba(0,0,0,0.02) 50%,rgba(0,0,0,0.35) 100%)"></div>
      <div style="position:absolute;top:16px;right:16px;display:flex;gap:8px;z-index:2">
        <span :style="badgeStyle" style="display:inline-flex;align-items:center;gap:4px"><span class="olo-svinfo-badge-ico" v-html="pinSvg"></span>{{ t('Località') }}</span>
        <span :style="badgeStyle">{{ t('1900 m') }}</span>
      </div>
    </div>

    <!-- Body -->
    <div :style="bodyStyle">
      <!-- Title -->
      <h1 :style="{ fontSize: s.acc_title_size+'px', fontWeight:'700', color:'var(--olo-color-text, #374151)', margin:'0 0 20px', lineHeight:'1.2' }">
        {{ t('Nome Baita / Servizio') }}
      </h1>

      <!-- Ordered sections -->
      <template v-for="sec in orderedSections" :key="sec">
        <!-- Gallery -->
        <div v-if="sec === 'gallery' && s.acc_show_gallery" style="margin-bottom:24px">
          <div :style="galleryStyle">
            <div v-for="i in Math.min(parseInt(s.acc_gallery_columns) || 4, 4)" :key="i"
                 :style="galleryThumbStyle">
            </div>
          </div>
        </div>

        <!-- Stats -->
        <div v-if="sec === 'stats'" style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:24px;text-align:center">
          <div v-for="stat in stats" :key="stat.label">
            <div class="olo-svinfo-stat-ico" style="display:flex;justify-content:center;margin-bottom:4px;color:var(--olo-color-text-soft, #6B7280)" v-html="stat.svg"></div>
            <div style="font-size:20px;font-weight:700;color:var(--olo-color-text, #374151)">{{ stat.value }}</div>
            <div style="font-size:11px;text-transform:uppercase;color:var(--olo-color-text-soft, #6B7280);letter-spacing:0.5px;font-weight:600">{{ stat.label }}</div>
          </div>
        </div>

        <!-- Prices -->
        <div v-if="sec === 'prices'" style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:28px">
          <div v-for="price in prices" :key="price.label"
               style="border:2px solid var(--olo-color-border, #E5E7EB);border-radius:10px;padding:16px 12px;text-align:center">
            <div :style="{ fontSize:'22px', fontWeight:'700', color: accent }">{{ price.amount }}</div>
            <div style="font-size:13px;color:var(--olo-color-text-soft, #6B7280);margin-top:2px">{{ price.label }}</div>
          </div>
        </div>

        <!-- Description -->
        <div v-if="sec === 'description' && s.acc_show_description" style="margin-bottom:28px">
          <h3 style="font-size:17px;font-weight:700;color:var(--olo-color-text, #374151);margin:0 0 12px;font-style:italic">{{ t('Descrizione') }}</h3>
          <div style="font-size:14px;line-height:1.7;color:var(--olo-color-text, #374151)">
            <p style="margin:0 0 10px">{{ t('La baita è un piccolo gioiello nascosto a 1.900 metri di altitudine, affacciata sui laghi cristallini.') }}</p>
            <p style="margin:0">{{ t('Completamente autonoma con pannelli solari e stufa a legna, offre 1 camera matrimoniale con vista lago.') }}</p>
          </div>
        </div>

        <!-- Amenities -->
        <div v-if="sec === 'amenities' && s.acc_show_amenities" style="margin-bottom:28px">
          <h3 style="font-size:17px;font-weight:700;color:var(--olo-color-text, #374151);margin:0 0 12px;font-style:italic">{{ t('Servizi e comfort') }}</h3>
          <div :style="amenitiesStyle">
            <div v-for="amenity in amenities" :key="amenity"
                 style="display:flex;align-items:center;gap:6px;font-size:13px;color:var(--olo-color-text, #374151);padding:3px 0">
              <span class="olo-svinfo-check" :style="{ color: accent, flexShrink:0 }" v-html="checkSvg"></span>
              <span>{{ amenity }}</span>
            </div>
          </div>
        </div>

        <!-- Check-in -->
        <div v-if="sec === 'checkin' && s.acc_show_checkin"
             :style="checkinStyle">
          <strong style="color:var(--olo-color-text, #374151)">{{ t('Check-in:') }}</strong> dalle 15:00
          &nbsp;| &nbsp;<strong style="color:var(--olo-color-text, #374151)">{{ t('Check-out:') }}</strong> entro 10:00
          <br><strong style="color:var(--olo-color-text, #374151)">{{ t('Soggiorno minimo:') }}</strong> 2 notti
        </div>
      </template>
    </div>
  </div>
</template>

<script setup>
import { t } from '@/i18n';
import { computed } from 'vue';
import { getShadowValue } from '@/composables/useShadowMap';
import iconsSvg from '../ProSlider/uikitIconsSvg.js';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });

const checkSvg = iconsSvg['check'] || '';
const pinSvg = iconsSvg['map-pin'] || iconsSvg['location'] || '';

// SVG stat (camere/bagni/ospiti/superficie) — currentColor, niente emoji
const STAT_ICON = 'stroke="currentColor" stroke-width="1.8" fill="none"';
const bedSvg = `<svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M3 18V12C3 10.34 4.34 9 6 9H18C19.66 9 21 10.34 21 12V18" ${STAT_ICON} stroke-linecap="round"/><path d="M3 18V20M21 18V20M3 13h18" ${STAT_ICON} stroke-linecap="round"/></svg>`;
const bathSvg = `<svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M4 12H20V15C20 17.2 18.2 19 16 19H8C5.8 19 4 17.2 4 15V12Z" ${STAT_ICON}/><path d="M4 12V5C4 4.45 4.45 4 5 4H7C7.55 4 8 4.45 8 5V12" ${STAT_ICON} stroke-linecap="round"/></svg>`;
const usersSvg = `<svg width="20" height="20" viewBox="0 0 24 24" fill="none"><circle cx="9" cy="7" r="3" ${STAT_ICON}/><path d="M3 20C3 16.69 5.69 14 9 14C12.31 14 15 16.69 15 20" ${STAT_ICON} stroke-linecap="round"/></svg>`;
const areaSvg = `<svg width="20" height="20" viewBox="0 0 24 24" fill="none"><rect x="4" y="4" width="16" height="16" rx="2" ${STAT_ICON}/><path d="M10 12L14 12M12 10V14" ${STAT_ICON} stroke-linecap="round"/></svg>`;

const defaults = {
  acc_hero_height: '400', acc_max_width: '900', acc_card_radius: '16',
  acc_card_shadow: 'lg', acc_body_padding: '32', acc_title_size: '30',
  acc_show_gallery: true, acc_gallery_columns: '4', acc_gallery_height: '180',
  acc_show_description: true, acc_show_amenities: true, acc_show_checkin: true,
  acc_show_booking_btn: false, acc_amenity_cols_desktop: '4', acc_accent_color: '',
  acc_section_order: 'gallery,stats,prices,description,amenities,checkin',
};

const s = computed(() => ({ ...defaults, ...props.settings }));

const accent = computed(() => s.value.acc_accent_color || 'var(--olo-color-primary, #e1474f)');

const orderedSections = computed(() => {
  const raw = s.value.acc_section_order || 'gallery,stats,prices,description,amenities,checkin';
  return raw.split(',').map(x => x.trim()).filter(Boolean);
});

const wrapStyle = computed(() => ({
  maxWidth: s.value.acc_max_width + 'px',
  margin: '0 auto',
}));

const heroStyle = computed(() => ({
  position: 'relative',
  height: Math.min(parseInt(s.value.acc_hero_height) || 400, 250) + 'px',
  // placeholder hero elegante: tinta soft brand (no più teal off-brand #0891B2)
  background: 'linear-gradient(135deg, color-mix(in srgb, var(--olo-color-primary, #e1474f) 25%, #fff), color-mix(in srgb, var(--olo-color-primary, #e1474f) 55%, #fff))',
  borderRadius: `${s.value.acc_card_radius}px ${s.value.acc_card_radius}px 0 0`,
  overflow: 'hidden',
}));

const badgeStyle = {
  background: 'rgba(22,38,61,0.78)', color: 'var(--olo-color-on-primary, #fff)', padding: '5px 12px',
  borderRadius: '18px', fontSize: '11px', fontWeight: '600',
};

const bodyStyle = computed(() => ({
  padding: s.value.acc_body_padding + 'px',
  background: '#fff',
  boxShadow: getShadowValue(s.value, 'acc_card_shadow'),
  borderRadius: `0 0 ${s.value.acc_card_radius}px ${s.value.acc_card_radius}px`,
}));

const galleryStyle = computed(() => ({
  display: 'grid',
  gridTemplateColumns: `repeat(${Math.min(parseInt(s.value.acc_gallery_columns) || 4, 4)}, 1fr)`,
  gap: '6px',
}));

const galleryThumbStyle = computed(() => ({
  height: Math.min(parseInt(s.value.acc_gallery_height) || 180, 120) + 'px',
  // placeholder thumb neutro elegante (no più grigi nudi)
  background: 'linear-gradient(135deg, var(--olo-color-surface-alt, #f6f7f9), var(--olo-color-border, #e5e7eb))',
  borderRadius: '6px',
}));

const amenitiesStyle = computed(() => ({
  display: 'grid',
  gridTemplateColumns: `repeat(${parseInt(s.value.acc_amenity_cols_desktop) || 4}, 1fr)`,
  gap: '6px 20px',
}));

const checkinStyle = computed(() => ({
  background: 'var(--olo-color-surface-alt, #f6f7f9)',
  borderTop: '1px solid var(--olo-color-border, #E5E7EB)',
  margin: `0 -${s.value.acc_body_padding}px -${s.value.acc_body_padding}px`,
  padding: `14px ${s.value.acc_body_padding}px`,
  borderRadius: `0 0 ${s.value.acc_card_radius}px ${s.value.acc_card_radius}px`,
  fontSize: '13px',
  color: 'var(--olo-color-text, #374151)',
}));

const stats = [
  { svg: bedSvg, value: '1', label: 'Camere' },
  { svg: bathSvg, value: '1', label: 'Bagni' },
  { svg: usersSvg, value: '2', label: 'Ospiti' },
  { svg: areaSvg, value: '35', label: 'MQ' },
];

const prices = [
  { amount: '€ 75', label: '/ notte' },
  { amount: '€ 95', label: '/ notte weekend' },
  { amount: '€ 450', label: '/ settimana' },
];

const amenities = [
  'Camino', 'Parcheggio', 'Cucina attrezzata', 'Riscaldamento',
  'Biancheria inclusa', 'Asciugamani inclusi', 'Vista montagna',
];
</script>

<style scoped>
.olo-svinfo-check { display: inline-flex; }
.olo-svinfo-check :deep(svg) { width: 15px; height: 15px; stroke: currentColor; fill: none; }
.olo-svinfo-badge-ico { display: inline-flex; }
.olo-svinfo-badge-ico :deep(svg) { width: 12px; height: 12px; stroke: currentColor; fill: none; }
.olo-svinfo-stat-ico :deep(svg) { width: 20px; height: 20px; stroke: currentColor; fill: none; }
</style>
