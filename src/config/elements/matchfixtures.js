import { shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
import { t } from '@/i18n';

/**
 * Match Fixtures — rail di partite (sport): data/ora+luogo, badge lega + giornata,
 * due crest squadra (monogramma colorato), punteggio o "vs", riga squadra/venue.
 * Estratta dal blueprint OLOthemes "MatchFixtures" (verdano). Render Vue == PHP.
 * Statica (nessun JS). Sezione-header separata (section-header).
 */
export default {
  type: 'matchfixtures',
  name: t('Match Fixtures'),
  icon: 'dashicons-calendar-alt',
  category: 'content',

  defaults: {
    items: [
      { day: 'Sat, 14.03', time_place: '15:00 · Verdano Park', league: 'Super League', matchday: 'Matchday 04',
        home_crest: 'VF', home_crest_bg: '#15543c', home_name: 'Verdano FC',
        away_crest: 'RA', away_crest_bg: '#7a2230', away_name: 'Real Alta', score: '', venue: "First Men's Team" },
      { day: 'Sat, 14.03', time_place: '17:30 · Walter Field', league: "Women's League", matchday: 'Matchday 04',
        home_crest: 'BF', home_crest_bg: '#26506b', home_name: 'SC Bendfeld',
        away_crest: 'VF', away_crest_bg: '#15543c', away_name: 'Verdano FC', score: '', venue: "First Women's Team" },
      { day: 'Sun, 08.03', time_place: '13:30 · Redwood Park', league: 'Youth Elite', matchday: 'Matchday 03',
        home_crest: 'RC', home_crest_bg: '#8a5a1f', home_name: 'Redwood City',
        away_crest: 'VF', away_crest_bg: '#15543c', away_name: 'Verdano FC', score: '4 : 3', venue: 'Under 14 Team · Full time' },
    ],
    columns: 3,
    gap: 16,
    card_bg: '#0f3a2a',
    card_border: 'rgba(255,255,255,0.1)',
    accent: '',
    day_color: '#ffffff',
    meta_color: 'rgba(255,255,255,0.55)',
    name_color: '#ffffff',
    score_color: '#ffffff',
    crest_text_color: '#ffffff',
    radius: 18,

    // ── Spaziatura / Forma (additivi, no-op coi default) ──
    // Padding interno card: oggi è fisso 22px → default = ESATTAMENTE 22 su 4 lati (render invariato).
    content_padding: { top: 22, right: 22, bottom: 22, left: 22 },
    // Override 4-angoli del raggio card: vuoto = usa il range 'radius' esistente (no-op).
    card_radius: { tl: 0, tr: 0, br: 0, bl: 0 },

    // KIT standard OLObuild: sfondo completo opzionale + ombra + bordo (no-op coi default)
    bg: { type: 'none' },
    shadow: 'none',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  fields: [
    { type: 'separator', label: t('Partite') },
    { key: 'items', label: t('Fixtures'), type: 'content-items',
      itemLabel: t('Partita'),
      defaults: { day: 'Sab, 01.01', time_place: '15:00 · Stadio', league: 'Lega', matchday: 'Giornata 01',
        home_crest: 'HM', home_crest_bg: '#15543c', home_name: 'Squadra Casa',
        away_crest: 'AW', away_crest_bg: '#7a2230', away_name: 'Squadra Ospite', score: '', venue: 'Prima Squadra' },
      itemFields: [
        { key: 'day', label: t('Data (es. Sat, 14.03)'), type: 'text' },
        { key: 'time_place', label: t('Ora · luogo'), type: 'text' },
        { key: 'league', label: t('Lega'), type: 'text' },
        { key: 'matchday', label: t('Giornata'), type: 'text' },
        { key: 'home_crest', label: t('Casa — sigla crest'), type: 'text' },
        { key: 'home_crest_bg', label: t('Casa — colore crest'), type: 'color' },
        { key: 'home_name', label: t('Casa — nome'), type: 'text' },
        { key: 'away_crest', label: t('Ospite — sigla crest'), type: 'text' },
        { key: 'away_crest_bg', label: t('Ospite — colore crest'), type: 'color' },
        { key: 'away_name', label: t('Ospite — nome'), type: 'text' },
        { key: 'score', label: t('Punteggio (vuoto = "vs")'), type: 'text' },
        { key: 'venue', label: t('Riga squadra / stato'), type: 'text' },
      ],
    },
    { type: 'separator', label: t('Layout') },
    { key: 'columns', label: t('Colonne'), type: 'range', min: 1, max: 4, step: 1, responsive: true },
  ],

  styleFields: [
    { type: 'separator', label: t('Card') },
    { key: 'card_bg', label: t('Sfondo card'), type: 'color' },
    { key: 'card_border', label: t('Bordo card'), type: 'color' },
    { key: 'radius', label: t('Raggio (px)'), type: 'range', min: 0, max: 32, step: 1 },
    { key: 'gap', label: t('Spazio tra card (px)'), type: 'range', min: 8, max: 32, step: 2 },

    { type: 'separator', label: t('Spaziatura') },
    { key: 'content_padding', label: t('Padding card (px)'), type: 'spacing', max: 64,
      description: t('Spaziatura interna della card. Default 22px.') },

    { type: 'separator', label: t('Forma') },
    { key: 'card_radius', label: t('Raggio card — 4 angoli (px)'), type: 'border-radius',
      description: t('Override a 4 angoli. Vuoto/0 = usa il "Raggio (px)" sopra.') },

    { type: 'separator', label: t('Colori') },
    { key: 'accent', label: t('Accento (lega · vs · badge)'), type: 'color',
      description: t('Vuoto = primario del tema.') },
    { key: 'day_color', label: t('Colore data'), type: 'color' },
    { key: 'meta_color', label: t('Colore meta (ora/venue)'), type: 'color' },
    { key: 'name_color', label: t('Colore nome squadra'), type: 'color' },
    { key: 'score_color', label: t('Colore punteggio'), type: 'color' },
    { key: 'crest_text_color', label: t('Colore sigla crest'), type: 'color' },

    { type: 'separator', label: t('Sfondo') },
    { key: 'bg', label: t('Sfondo completo'), type: 'background', showParallax: false },

    { type: 'separator', label: t('Ombra') },
    ...shadowField,
    ...borderFields(),
  ],
};
