
import { borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
import { t } from '@/i18n';
// I tracciati delle forme: gli stessi che disegna il PHP (fonte unica), qui per le miniature del selettore.
import FORME from '../../../assets/data/shapedivider-shapes.json';

// Un bordo (normale o in hover) con almeno un lato > 0, oppure un effetto bordo: ciò che il
// renderer disegna ancora e che dall'inspector si deve poter togliere.
const latiPieni = (b) => !!b && typeof b === 'object'
  && ['top', 'right', 'bottom', 'left'].some((k) => (parseInt(b[k], 10) || 0) > 0);
const bordoSalvato = (s) => [s.border, s.border_hover].some(latiPieni)
  || (!!s.border_effect && s.border_effect !== 'none');

const forma = (value, label, extra = {}) => ({ value, label, anteprima: { viewBox: '0 0 1200 120', d: FORME[value] }, ...extra });
const dinamica = { field: 'shape', op: 'eq', value: 'dinamica' };
const conLivelli = { field: 'layers', op: 'in', value: ['2', '3'] };

/**
 * Tile ShapeDivider — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → specchia orizzontale/verticale, movimento delle onde (forma «Dinamica»), ingresso dei livelli
 *   styleFields[] → Aspetto (colori, sfumatura, colori dei livelli) · Forma (forma con le miniature, livelli,
 *                   onde, altezza per dispositivo, larghezza) · Disposizione (posizione, z-index); bordo solo se già salvato
 *   AVANZATE      → meta tecnico (id/class/condizioni)
 * Le funzioni del Divider Builder di pixfort: 28 forme, fino a 3 livelli traslucidi con il loro colore,
 * sfumatura, livelli che entrano animati, divisore dinamico (onde che si muovono da sole), altezza per dispositivo.
 */
export default {
  type: 'shapedivider',
  name: t('Shape Divider'),
  icon: 'dashicons-editor-contract',
  category: 'layout',
  defaults: {
    shape: 'wave',
    position: 'bottom',
    flip_horizontal: false,
    flip_vertical: false,
    width: '100',
    height: '80',
    color: '',
    z_index: '1',
    responsive_height_tablet: '',
    responsive_height_mobile: '',
    layers: '1',
    layer_gap: 15,
    layer2_color: '',
    layer3_color: '',
    gradient: false,
    color_end: '',
    gradient_angle: 90,
    wave_count: 3,
    wave_amplitude: 70,
    wave_duration: 10,
    layers_anim: 'none',
    layers_anim_duration: 1000,
    layers_anim_delay: 200,
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { key: 'flip_horizontal', label: t('Specchia orizzontale'), type: 'toggle' },
    { key: 'flip_vertical', label: t('Specchia verticale'), type: 'toggle' },

    { type: 'separator', label: t('Movimento delle onde'), condition: dinamica },
    { key: 'wave_duration', label: t('Durata del ciclo'), type: 'range', min: 3, max: 40, step: 1, unit: 's',
      description: t('Il tempo in cui le onde compiono un giro completo: più è lungo, più si muovono piano. I livelli dietro vanno un po\' più lenti, così non si muovono mai insieme.') },

    { type: 'separator', label: t('Ingresso dei livelli'), condition: conLivelli },
    { key: 'layers_anim', label: t('Animazione'), type: 'select', ui: 'dropdown', options: [
      { value: 'none', label: t('Nessuna') },
      { value: 'fade', label: t('Dissolvenza') },
      { value: 'fade-up', label: t('Dal basso') },
      { value: 'fade-down', label: t('Dall\'alto') },
      { value: 'fade-left', label: t('Da sinistra') },
      { value: 'fade-right', label: t('Da destra') },
    ], description: t('I livelli dietro la forma entrano uno dopo l\'altro quando il divisore arriva nello schermo. La forma piena resta ferma: è il bordo della sezione.') },
    { key: 'layers_anim_duration', label: t('Durata'), type: 'range', min: 200, max: 3000, step: 100, unit: 'ms',
      condition: { field: 'layers_anim', op: 'neq', value: 'none' } },
    { key: 'layers_anim_delay', label: t('Ritardo fra i livelli'), type: 'range', min: 0, max: 1000, step: 50, unit: 'ms',
      condition: { field: 'layers_anim', op: 'neq', value: 'none' } },
  ],

  // ─── STILE ─────────────────────────────────────────────────
  styleFields: [
    { type: 'separator', label: t('Aspetto') },
    { key: 'color', label: t('Colore'), type: 'color' },
    { key: 'gradient', label: t('Sfumatura'), type: 'toggle' },
    { key: 'color_end', label: t('Colore finale'), type: 'color', condition: { field: 'gradient', op: 'eq', value: true },
      description: t('La sfumatura va dal colore di ogni livello a questo. Vuoto = il colore svanisce nel trasparente.') },
    { key: 'gradient_angle', label: t('Direzione sfumatura'), type: 'range', min: 0, max: 360, step: 15, unit: '°',
      condition: { field: 'gradient', op: 'eq', value: true } },
    { key: 'layer2_color', label: t('Colore livello 2'), type: 'color', condition: conLivelli,
      description: t('Vuoto = il colore della forma con opacità 60%.') },
    { key: 'layer3_color', label: t('Colore livello 3'), type: 'color', condition: { field: 'layers', op: 'eq', value: '3' },
      description: t('Vuoto = il colore della forma con opacità 30%.') },

    // Il «Bordo» disegnerebbe un rettangolo attorno alla fascia della forma (posizionata fuori
    // dalla sezione): non è un divisore. Non si offre più, ma chi l'ha già impostato lo ritrova
    // per toglierlo: la sezione compare solo se il PHP disegna qualcosa, cioè con lati > 0 nel
    // bordo normale O in quello in hover (con la base a 0 l'hover ricade sui suoi lati), oppure
    // con un effetto salvato. La `show` del separatore vale per tutta la sezione.
    ...borderFields().map((f, i) => (i === 0 ? { ...f, show: bordoSalvato } : f)),

    { type: 'separator', label: t('Forma') },
    { key: 'shape', label: t('Forma'), type: 'select', layout: 'block', options: [
      { group: t('Onde e curve'), options: [
        forma('wave', t('Onda')),
        forma('wave2', t('Onda doppia')),
        forma('ripples', t('Onde fitte')),
        forma('curve', t('Curva')),
        forma('valley', t('Valle')),
        forma('hill', t('Collina')),
        forma('swoosh', t('Slancio')),
        forma('book', t('Libro aperto')),
        forma('clouds', t('Nuvole')),
        forma('drops', t('Gocce')),
      ]},
      { group: t('Angoli'), options: [
        forma('triangle', t('Triangolo')),
        // «Freccia» ha lo stesso tracciato di «Triangolo»: resta per chi l'ha scelta, non si ripropone.
        forma('arrow', t('Freccia'), { soloSeAttiva: true }),
        forma('chevron', t('V')),
        forma('tilt', t('Inclinato')),
        forma('zigzag', t('Zigzag')),
        forma('teeth', t('Denti')),
        forma('mountains', t('Montagne')),
        forma('notch', t('Tacca')),
        forma('peak', t('Picco')),
        forma('steps', t('Gradini')),
        forma('skyline', t('Città')),
      ]},
      { group: t('Lastre'), options: [
        forma('plateau', t('Altopiano')),
        forma('slope', t('Pendio')),
        forma('corner', t('Angolo tondo')),
      ]},
      { group: t('Forme libere'), options: [
        forma('bubbles', t('Bolle')),
        forma('drips', t('Colature')),
        forma('brush', t('Pennellata')),
      ]},
      { group: t('In movimento'), options: [
        forma('dinamica', t('Dinamica')),
      ]},
    ], description: t('«Dinamica» disegna onde che si muovono da sole, senza fine.') },
    { key: 'layers', label: t('Livelli'), type: 'select', options: [
      { value: '1', label: '1' },
      { value: '2', label: '2' },
      { value: '3', label: '3' },
    ], description: t('Copie della forma dietro quella piena, sempre più trasparenti: danno profondità al bordo della sezione.') },
    { key: 'layer_gap', label: t('Gap livelli'), type: 'range', min: 5, max: 30, step: 1, unit: '%', condition: conLivelli },
    { key: 'wave_count', label: t('Onde'), type: 'range', min: 1, max: 6, step: 1, condition: dinamica },
    { key: 'wave_amplitude', label: t('Ampiezza'), type: 'range', min: 10, max: 100, step: 5, unit: '%', condition: dinamica },
    { key: 'height', label: t('Altezza'), type: 'range', min: 10, max: 500, step: 5, unit: 'px', responsive: true,
      legacyDeviceKeys: { tablet: 'responsive_height_tablet', mobile_landscape: 'responsive_height_mobile' } },
    { key: 'width', label: t('Larghezza'), type: 'range', min: 100, max: 300, step: 5, unit: '%' },

    { type: 'separator', label: t('Disposizione') },
    { key: 'position', label: t('Posizione'), type: 'select', options: [
      { value: 'top', label: t('Alto') },
      { value: 'bottom', label: t('Basso') },
    ]},
    { key: 'z_index', label: t('Z-Index'), type: 'range', min: 0, max: 99, step: 1 },
  ],
};
