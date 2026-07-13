import { t } from '@/i18n';

/**
 * OLOX Page FX — decoratore a dimensione zero: scanline (security),
 * panorama 360° fisso (tour), barra XP fissa + toast (tutor).
 * Render Vue == PHP (OloxPageFxTile.vue / class-oloxpagefx-tile.php).
 */
export default {
  type: 'oloxpagefx',
  name: t('OLOX — Effetti pagina (scan/pano/xp)'),
  icon: 'dashicons-visibility',
  category: 'marketing',
  // Ritirata dalla palette: le pagine della replica olotheme.com sono composte
  // con tile classiche (headline, badge, info-cards, pricing, cta-banner, toc,
  // desclist, flipcard, counter, marquee). Resta per i template salvati.
  hidden: true,

  defaults: {
    variant: 'scan',
    deg_label: 'lo scroll ruota la vista',
    xp_label: 'corso · questa pagina',
    xp_total: 540,
    xp_cap: 630,
    xp_step: 180,
  },

  fields: [
    { key: 'variant', label: t('Variante'), type: 'select', options: [
      { value: 'scan', label: t('Scanline che segue lo scroll (security)') },
      { value: 'pano', label: t('Panorama 360° dietro la pagina (tour)') },
      { value: 'xp', label: t('Barra XP fissa + toast (tutor)') },
    ] },
    { key: 'deg_label', label: t('Etichetta gradi (pano)'), type: 'text',
      condition: { field: 'variant', op: 'eq', value: 'pano' } },
    { key: 'xp_label', label: t('Etichetta barra (xp)'), type: 'text',
      condition: { field: 'variant', op: 'eq', value: 'xp' } },
    { key: 'xp_total', label: t('XP da scroll completo'), type: 'number',
      condition: { field: 'variant', op: 'eq', value: 'xp' } },
    { key: 'xp_cap', label: t('XP tetto barra'), type: 'number',
      condition: { field: 'variant', op: 'eq', value: 'xp' } },
    { key: 'xp_step', label: t('XP per livello'), type: 'number',
      condition: { field: 'variant', op: 'eq', value: 'xp' } },
  ],

  styleFields: [],
};
