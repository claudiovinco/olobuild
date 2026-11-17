import { shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile Barra progresso — split CONTENUTO/STILE.
 *   fields[]      → barre (etichetta|valore), percentuale, testo interno, animazione (riempimento, contatore, durata)
 *   styleFields[] → Aspetto (stile, colori, ombra, bordo) · Testo · Forma (raggio, altezza, cerchio) · Disposizione
 * 17 nov 2026 (episodio 43): stili onesti coi colori del sito (erano dodici nomi inglesi a colori fissi, alcuni con
 * effetti mai disegnati: «Segmented», «Glass Bar», «3D Tilt»); il Raggio, che il renderer leggeva senza controllo;
 * le unità nei cursori; «Barre» senza parentesi; la durata dell'animazione accanto alle sue due animazioni.
 */
export default {
  type: 'progress',
  name: t('Barra progresso'),
  icon: 'dashicons-chart-bar',
  category: 'marketing',
  defaults: {
    bg: { type: 'none' },
    typography_preset: '',
    preset: 'custom',
    bars: 'HTML|90\nJavaScript|80\nVue.js|75',
    bar_color: '',
    bar_bg: '',
    text_color: '',
    height: '20',
    border_radius: '10',
    show_percentage: true,
    animated: true,
    layout: 'bar',
    circle_size: '120',
    circle_width: '8',
    inner_text: '',
    animate_counter: true,
    animation_duration: '1500',
    shadow: 'none',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // Appena nata: quattro indicatori di un'attività qualunque in barre sottili e arrotondate
  // nel colore primario, su una traccia della stessa tinta; le barre si riempiono entrando.
  partenza: {
    bars: t('Clienti soddisfatti|98\nProgetti consegnati nei tempi|95\nClienti che tornano|87\nRisposte entro 24 ore|92'),
    bar_color: 'var(--olo-color-primary)',
    bar_bg: 'color-mix(in srgb, var(--olo-color-primary) 14%, transparent)',
    text_color: 'var(--olo-color-text)',
    height: '10',
    show_percentage: false,
  },

  fields: [
    { key: 'bars', label: t('Barre'), type: 'textarea',
      description: t('Una per riga, scritta «Etichetta|valore», col valore da 0 a 100: «Clienti soddisfatti|98».') },
    { key: 'show_percentage', label: t('Mostra percentuale'), type: 'toggle' },
    // L'etichetta diceva «vuoto = percentuale»: nella barra non è più vero (la percentuale
    // compariva due volte), la regola completa sta nella (i).
    { key: 'inner_text', label: t('Testo interno'), type: 'text',
      description: t('Il testo al centro della barra o del cerchio. Se è vuoto, nel cerchio compare la percentuale; nella barra la percentuale sta in testa con «Mostra percentuale», e dentro la barra con lo stile «Spessa».') },

    { type: 'separator', label: t('Animazione') },
    { key: 'animated', label: t('Animata'), type: 'toggle',
      description: t('Le barre e i cerchi si riempiono quando entrano nello schermo.') },
    { key: 'animate_counter', label: t('Anima contatore'), type: 'toggle',
      description: t('Le percentuali salgono da zero insieme alle barre.') },
    { key: 'animation_duration', label: t('Durata'), type: 'range', min: 500, max: 3000, step: 100, unit: 'ms' },
  ],

  styleFields: [
    { type: 'separator', label: t('Aspetto') },
    { key: 'preset', label: t('Stile'), type: 'select', options: [
      { value: 'sottile',  label: t('Sottile') },
      { value: 'classica', label: t('Classica') },
      { value: 'spessa',   label: t('Spessa') },
      { value: 'sfumata',  label: t('Sfumata') },
      { value: 'su-scuro', label: t('Su fondo scuro') },
      { value: 'custom',   label: t('Personalizzato') },
    ], description: t('Cambia insieme altezza, raggio e colori, coi colori del sito. Poi ogni controllo qui sotto si regola da solo.') },
    { key: 'bar_color', label: t('Colore barra'), type: 'color' },
    { key: 'bar_bg', label: t('Colore della traccia'), type: 'color',
      description: t('La parte vuota dietro la barra, o l\'anello dietro il cerchio.') },
    { key: 'text_color', label: t('Colore testo'), type: 'color' },
    ...shadowField,
    ...borderFields(),

    { type: 'separator', label: t('Testo') },
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },

    { type: 'separator', label: t('Forma') },
    { key: 'border_radius', label: t('Raggio'), type: 'border-radius',
      condition: { field: 'layout', op: 'neq', value: 'circle' } },
    { key: 'height', label: t('Altezza'), type: 'range', min: 4, max: 60, step: 1, unit: 'px',
      condition: { field: 'layout', op: 'neq', value: 'circle' } },
    { key: 'circle_size', label: t('Dimensione cerchio'), type: 'range', min: 60, max: 200, step: 10, unit: 'px',
      condition: { field: 'layout', op: 'eq', value: 'circle' } },
    { key: 'circle_width', label: t('Spessore cerchio'), type: 'range', min: 2, max: 20, step: 1, unit: 'px',
      condition: { field: 'layout', op: 'eq', value: 'circle' } },

    { type: 'separator', label: t('Disposizione') },
    { key: 'layout', label: t('Barre o cerchi'), type: 'select', options: [
      { value: 'bar', label: t('Barre') },
      { value: 'circle', label: t('Cerchi') },
    ]},
  ],
};
