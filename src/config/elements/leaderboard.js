import { shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, withHover } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile Leaderboard — classifica con barre XP animate (famiglia E · bucket C).
 * Reference visiva: handoff-tile-speciali/temi/60-tema-community-gamer.html (#board).
 *
 *   fields[]      → source (manual/query), rows[] (nome|ruolo|valore|max), animateOnView,
 *                   highlightTop, mostra valori/posizioni, etichetta unità.
 *   styleFields[] → colori (bg riga/barra-traccia/testo/posizione), barGradient (2 colori + angolo),
 *                   colori highlight, dimensioni (altezza barra, raggio, gap), velocità animazione,
 *                   shadow + sistema bordi.
 *
 * Contratto §2: ogni numero/colore = campo con default; nessun hardcode; colori via token o picker.
 * Le barre hanno role=progressbar + aria-valuenow/min/max; reduced-motion → barre già piene.
 */
export default {
  type: 'leaderboard',
  name: t('Classifica (barre XP)'),
  icon: 'dashicons-awards',
  category: 'marketing',

  defaults: {
    bg: { type: 'none' },
    typography_preset: '',

    // ── Dati ──
    source: 'manual',
    rows: [
      { name: 'KiraByte',   role: 'Capoclan',  value: 24810, max: 25000 },
      { name: 'nott2late',  role: 'Veterano',  value: 20140, max: 25000 },
      { name: 'pixelmom',   role: 'Veterano',  value: 18305, max: 25000 },
      { name: 'vex_',       role: 'Membro',    value: 15022, max: 25000 },
      { name: 't0fu',       role: 'Membro',    value: 11870, max: 25000 },
      { name: 'mochi',      role: 'Recluta',   value: 8140,  max: 25000 },
    ],
    name_prefix: '@',
    value_suffix: 'xp',
    show_position: true,
    show_value: true,
    show_role: true,

    // ── Comportamento ──
    animate_on_view: true,
    animation_duration: 1300,
    highlight_top: 3,

    // ── Aspetto ──
    row_bg: '',
    row_padding: { top: 16, right: 20, bottom: 16, left: 20 },
    row_gap: 12,
    text_color: '',
    role_color: '',
    position_color: '',
    badge_bg: '',
    badge_color: '',

    bar_track_color: '',
    bar_gradient_from: '',
    bar_gradient_to: '',
    bar_gradient_angle: 90,
    bar_height: 8,
    bar_radius: 6,

    highlight_color: '',
    name_size: 16,
    name_weight: '700',
    role_size: 11,
    position_size: 24,

    border_radius: { tl: 14, tr: 14, br: 14, bl: 14 },
    shadow: 'none',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // ═══ CONTENUTO ════════════════════════════════════════════════
  fields: [
    { type: 'separator', label: t('Sorgente dati') },
    { key: 'source', label: t('Origine dati'), type: 'select', options: [
      { value: 'manual', label: t('Manuale') },
      { value: 'query',  label: t('Query (in arrivo)') },
    ], description: t('Manuale usa le righe sotto. Query collegherà un endpoint olo/v1 (placeholder: usa le righe come demo).') },

    { key: 'rows', label: t('Righe classifica'), type: 'content-items',
      condition: { field: 'source', op: 'eq', value: 'manual' },
      itemLabel: t('Riga'),
      defaults: { name: 'Nuovo membro', role: 'Membro', value: 1000, max: 25000 },
      newItemDefaults: { name: 'Nuovo membro', role: 'Membro', value: 1000, max: 25000 },
      itemFields: [
        { key: 'name',  label: t('Nome'),        type: 'text' },
        { key: 'role',  label: t('Ruolo / badge'), type: 'text' },
        { key: 'value', label: t('Valore (XP / punti)'), type: 'number', min: 0 },
        { key: 'max',   label: t('Massimo (per la barra)'), type: 'number', min: 1 },
      ],
    },

    { type: 'separator', label: t('Etichette') },
    { key: 'name_prefix',  label: t('Prefisso nome'), type: 'text', placeholder: '@' },
    { key: 'value_suffix', label: t('Unità valore'), type: 'text', placeholder: 'xp' },
    { key: 'show_position', label: t('Mostra posizione'), type: 'toggle' },
    { key: 'show_value',    label: t('Mostra valore'), type: 'toggle' },
    { key: 'show_role',     label: t('Mostra ruolo / badge'), type: 'toggle' },

    { type: 'separator', label: t('Animazione') },
    { key: 'animate_on_view', label: t('Anima le barre all\'ingresso'), type: 'toggle',
      description: t('Le barre crescono da 0 al valore quando la classifica entra nel viewport. Rispetta prefers-reduced-motion (barre già piene, nessuna animazione).') },
    { key: 'animation_duration', label: t('Durata animazione (ms)'), type: 'range', min: 300, max: 3000, step: 100,
      condition: { field: 'animate_on_view', op: 'eq', value: true } },
    { key: 'highlight_top', label: t('Evidenzia prime N posizioni'), type: 'range', min: 0, max: 3, step: 1 },
  ],

  // ═══ STILE ════════════════════════════════════════════════════
  styleFields: [
    { type: 'separator', label: t('Stile tipografico') },
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },

    { type: 'separator', label: t('Riga') },
    { key: 'row_bg', label: t('Sfondo riga'), type: 'color' },
    { key: 'row_padding', label: t('Padding riga (px)'), type: 'spacing', max: 48 },
    { key: 'row_gap', label: t('Spazio tra righe (px)'), type: 'range', min: 0, max: 40, step: 2 },

    { type: 'separator', label: t('Barra XP') },
    { key: 'bar_track_color', label: t('Colore traccia barra'), type: 'color' },
    { key: 'bar_gradient_from', label: t('Gradiente barra — colore 1'), type: 'color' },
    { key: 'bar_gradient_to',   label: t('Gradiente barra — colore 2'), type: 'color' },
    { key: 'bar_gradient_angle', label: t('Angolo gradiente (°)'), type: 'range', min: 0, max: 360, step: 5 },
    { key: 'bar_height', label: t('Altezza barra (px)'), type: 'range', min: 4, max: 28, step: 1 },
    { key: 'bar_radius', label: t('Raggio barra (px)'), type: 'range', min: 0, max: 16, step: 1 },

    { type: 'separator', label: t('Colori') },
    { key: 'text_color',     label: t('Colore nome'), type: 'color' },
    { key: 'role_color',     label: t('Colore ruolo'), type: 'color' },
    { key: 'position_color', label: t('Colore posizione'), type: 'color' },
    { key: 'badge_bg',       label: t('Sfondo badge ruolo'), type: 'color',
      condition: { field: 'show_role', op: 'eq', value: true } },
    { key: 'badge_color',    label: t('Testo badge ruolo'), type: 'color',
      condition: { field: 'show_role', op: 'eq', value: true } },
    { key: 'highlight_color', label: t('Colore evidenziazione (top)'), type: 'color',
      condition: { field: 'highlight_top', op: 'neq', value: 0 } },

    { type: 'separator', label: t('Tipografia') },
    { type: 'typography', label: t('Nome'),
      presetKey: 'typography_preset',
      responsiveKeys: ['size'],
      keys: {
        size:   'name_size',
        weight: 'name_weight',
        color:  'text_color',
      },
      sizeMin: 12, sizeMax: 32, sizeStep: 1,
    },
    { key: 'role_size',     label: t('Dimensione ruolo (px)'), type: 'range', min: 8, max: 18, step: 1 },
    { key: 'position_size', label: t('Dimensione posizione (px)'), type: 'range', min: 14, max: 40, step: 1 },

    { type: 'separator', label: t('Aspetto') },
    withHover({ key: 'border_radius', label: t('Arrotondamento riga (px)'), type: 'border-radius' }),

    ...shadowField,
    ...borderFields(),
  ],
};
