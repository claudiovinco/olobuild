import { focalField } from './_shared.js';
import { t } from '@/i18n';

/**
 * _imageFrame — la CORNICE di un'immagine, in un gruppo solo.
 *
 * Ovunque una tile faccia inserire un'immagine, l'utente deve poter decidere tre
 * cose, e sempre le stesse: la **maschera di ritaglio** (1:1, 16:9, …), come la
 * foto **riempie** quella maschera, e **quale punto** resta inquadrato quando si
 * ritaglia. La tile Immagine le aveva già tutte e tre; le altre avevano un pezzo
 * a testa — o nessuno — e ogni volta con nomi e opzioni diversi.
 *
 * Le chiavi seguono uno schema unico, derivato dalla chiave dell'immagine:
 *   <key>_ratio · <key>_ratio_custom · <key>_fit · <key>_object_position
 * L'ultima è la convenzione che `focalField()` usa già, quindi il punto focale
 * eventualmente presente non si sposta.
 *
 * IL DEFAULT DEVE RIPRODURRE LA RESA DI OGGI. Una tile che oggi ritaglia in
 * quadrato passa `ratio: '1/1'`, non 'auto': così le pagine già pubblicate non si
 * muovono di un pixel finché l'utente non tocca il controllo.
 *
 * Resa: `Olobuild_Tile_Utils::image_frame()` in PHP, `useImageFrame.js` nel canvas.
 */

// LE PROPORZIONI, UNA VOLTA SOLA.
//
// Prima di questo file ogni tile aveva inventato il suo elenco: 29 selettori, 29
// insiemi diversi, e persino tre modi di SCRIVERE lo stesso rapporto — '16/9' nella
// maggior parte, '16:9' in quattro tile, '16-9' nelle cinque WooCommerce. Il valore
// salvato non si può cambiare senza rompere i template pubblicati; l'ELENCO e
// l'ORDINE e i NOMI si', e devono essere gli stessi ovunque.
//
// Da qui in poi: stesse voci, stesso ordine, stessi nomi. Il separatore lo decide la
// tile con `sep`, e i rapporti storici che non stanno nel set canonico si passano in
// `extra` — restano selezionabili, così chi li ha salvati li ritrova.
//
// L'etichetta dice sempre 'W:H' perché e' come si leggono ad alta voce, anche quando
// il valore salvato usa la barra.
const CANONICHE = [
  ['1/1',  '1:1 (Quadrato)'],
  ['4/3',  '4:3 (TV classico)'],
  ['3/2',  '3:2 (Foto)'],
  ['16/9', '16:9 (Widescreen)'],
  ['21/9', '21:9 (Cinema)'],
  ['3/4',  '3:4 (Ritratto)'],
  ['4/5',  '4:5 (Ritratto social)'],
  ['9/16', '9:16 (Verticale)'],
  ['2/3',  '2:3 (Foto verticale)'],
];

// Nomi per i rapporti storici che una singola tile si porta dietro.
const NOMI_EXTRA = {
  '16/10': '16:10',
  '2/1': '2:1 (Panorama)',
  '5/4': '5:4',
  '16/11': '16:11',
  '3/3.5': '3:3.5 (Vetrina)',
  '4/4.4': '4:4.4',
  '190/240': '19:24',
};

/**
 * L'elenco delle proporzioni per il `select` di una tile.
 *
 * @param {object} opts
 *   - sep        separatore del VALORE salvato: '/' (default), ':' o '-'
 *   - auto       false per togliere la voce 'nessun ritaglio' (tile con
 *                background-image, dove senza altezza il contenitore collassa)
 *   - autoValue  il valore della voce automatica ('auto' di default; '' dove la
 *                tile storicamente salva la stringa vuota)
 *   - autoLabel  il suo nome, quando 'segui altezza' non descrive cosa fa
 *   - custom     true per la voce 'Personalizzato' (serve la chiave _ratio_custom)
 *   - extra      rapporti storici da tenere selezionabili, es. ['16/10']
 *   - skip       canoniche da NON offrire
 */
export function ratioOptions(opts = {}) {
  const sep = opts.sep || '/';
  const scrivi = (v) => (sep === '/' ? v : v.replace('/', sep));
  const out = [];

  if (opts.auto !== false) {
    out.push({ value: opts.autoValue ?? 'auto', label: t(opts.autoLabel || 'Auto (segui altezza)') });
  }
  const salta = new Set(opts.skip || []);
  for (const [v, nome] of CANONICHE) {
    if (salta.has(v)) continue;
    out.push({ value: scrivi(v), label: t(nome) });
  }
  for (const v of opts.extra || []) {
    if (CANONICHE.some((c) => c[0] === v)) continue;
    out.push({ value: scrivi(v), label: t(NOMI_EXTRA[v] || v.replace('/', ':')) });
  }
  if (opts.custom) out.push({ value: 'custom', label: t('Personalizzato') });
  return out;
}

// Il set completo con la voce automatica e la personalizzata: e' quello che monta
// `imageFrameFields()` quando la tile non chiede niente di diverso.
export const PROPORZIONI = ratioOptions({ custom: true });

export const ADATTAMENTI = [
  { value: 'cover',      label: t('Riempi (ritaglia)') },
  { value: 'contain',    label: t('Contieni (tutta visibile)') },
  { value: 'fill',       label: t('Deforma per riempire') },
  { value: 'none',       label: t('Dimensione originale') },
  { value: 'scale-down', label: t('Riduci se necessario') },
];

/**
 * Il gruppo di campi da spreddare nei `styleFields` (o negli `itemFields`) di una tile.
 *
 * @param {string} key  chiave del campo immagine (es. 'cover_image')
 * @param {object} opts
 *   - label      etichetta del blocco (default: «Ritaglio immagine»)
 *   - separator  false per non emettere il separatore
 *   - focal      false per non emettere il punto focale (immagine mai ritagliata)
 *   - condition  condizione di visibilità applicata a tutto il gruppo
 *   - fit        false per non emettere l'adattamento (quando è per forza cover)
 *   - ratio      false per non emettere le proporzioni (cornice di forma fissa)
 *   - ratioOpts  opzioni passate a ratioOptions() (sep, extra, auto, custom…)
 *   - focalOpts  contextKeys extra per l'anteprima del focale
 */
export function imageFrameFields(key, opts = {}) {
  const cond = opts.condition;
  const con = (f) => (cond ? { ...f, condition: cond } : f);
  const out = [];

  if (opts.separator !== false) {
    out.push(con({ type: 'separator', label: opts.label || t('Ritaglio immagine') }));
  }

  const ratioOpts = { custom: true, ...(opts.ratioOpts || {}) };

  if (opts.ratio !== false) out.push(con({
    key: `${key}_ratio`,
    label: t('Proporzioni'),
    type: 'select',
    options: ratioOptions(ratioOpts),
    description: t('La maschera di ritaglio: la foto viene inquadrata dentro questa forma.'),
  }));

  if (opts.ratio !== false && ratioOpts.custom) out.push(con({
    key: `${key}_ratio_custom`,
    label: t('Proporzioni personalizzate'),
    type: 'text',
    placeholder: t('es. 5/4'),
    // L'AND si esprime come ARRAY: è la forma che evaluateCondition() valuta
    // davvero (`condition.every(...)`). Un oggetto `{ and: [...] }` non ha campo
    // `field` e cadrebbe nel ramo di default, risultando sempre vero.
    condition: cond
      ? [cond, { field: `${key}_ratio`, op: 'eq', value: 'custom' }]
      : { field: `${key}_ratio`, op: 'eq', value: 'custom' },
  }));

  if (opts.fit !== false) out.push(con({
    key: `${key}_fit`,
    label: t('Adattamento'),
    type: 'select',
    options: ADATTAMENTI,
    description: t('Come la foto riempie la maschera: «Riempi» ritaglia, «Contieni» la mostra tutta.'),
  }));

  if (opts.focal !== false) {
    const f = focalField(key, {
      ratio: `${key}_ratio`,
      ratioCustom: `${key}_ratio_custom`,
      ...(opts.focalOpts || {}),
    });
    // Il punto focale ha senso solo dove c'è un ritaglio da spostare — ma se
    // l'adattamento non è esposto (tile che ritaglia e basta) la condizione non
    // avrebbe nulla da leggere e nasconderebbe il controllo per sempre.
    if (opts.fit !== false) {
      // SI NASCONDE SOLO CON 'fill', ed è importante che sia scritto così:
      //  - con `contain` il focale funziona eccome, decide da che parte l'immagine
      //    si appoggia dentro le bande vuote (e i due renderer lo emettono);
      //  - una tile SALVATA PRIMA di questo sprint non ha affatto la chiave `_fit`,
      //    e con un elenco `in` il valore `undefined` non combacia con niente: il
      //    controllo sparirebbe per sempre, perché l'inspector valuta le condizioni
      //    sui settings GREZZI, senza fondere i default.
      // `neq 'fill'` copre tutti e due i casi ed è lo stesso criterio dei renderer
      // (`image_frame()` in PHP e `useImageFrame.js` saltano object-position solo lì).
      const suFit = { field: `${key}_fit`, op: 'neq', value: 'fill' };
      f.condition = cond ? [cond, suFit] : suFit;
    } else if (cond) {
      f.condition = cond;
    }
    out.push(f);
  }

  return out;
}

/**
 * I default da spreddare nei `defaults` della tile.
 * ⚠️ Passare i valori che riproducono la resa ATTUALE, non quelli "belli".
 */
export function imageFrameDefaults(key, def = {}) {
  return {
    [`${key}_ratio`]: def.ratio || 'auto',
    [`${key}_ratio_custom`]: def.ratioCustom || '16/9',
    [`${key}_fit`]: def.fit || 'cover',
    [`${key}_object_position`]: def.pos || 'center center',
  };
}
