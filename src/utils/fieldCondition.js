/**
 * fieldCondition — UN SOLO valutatore per le `condition` dei field dell'inspector.
 *
 * PERCHÉ ESISTE
 * -------------
 * Ne esistevano TRE copie (BuilderInspector, StyleFieldsRenderer, ContentItemsEditor)
 * con insiemi di operatori diversi. Conseguenze reali trovate durante l'audit:
 *   • `op:'ne'` e `op:'!='` (9 usi) cadevano nel ramo finale e si comportavano
 *     come UGUAGLIANZA: il campo compariva esattamente quando doveva sparire.
 *   • `operator:'in'` (3 usi) confrontava un array con `===` → sempre falso:
 *     il campo non compariva mai.
 *   • dentro i repeater (ContentItemsEditor) esistevano solo in/eq/notEmpty:
 *     `neq` (34 usi), `empty` e tutti gli `operator:` erano ignorati.
 *
 * Qui gli alias sono normalizzati una volta sola: chi scrive un config non deve
 * più indovinare quale dialetto capisce il punto in cui il campo verrà reso.
 *
 * VALORI COMPOSITI
 * ----------------
 * Dopo l'uniformazione dei controlli, una chiave che prima era un numero può
 * essere un oggetto a 4 lati (spacing/border/radius). Per i confronti numerici
 * l'oggetto viene ridotto alla somma dei lati, così condizioni storiche del tipo
 * «visibile solo se padding = 0» continuano a significare la stessa cosa.
 */

const EMPTY = (v) => v === undefined || v === null || v === '' || v === false;

/** Un valore composito (spacing/radius/border) collassa al totale dei suoi lati. */
function numeric(v) {
  if (v && typeof v === 'object') {
    const parts = ['top', 'right', 'bottom', 'left', 'tl', 'tr', 'br', 'bl'];
    let sum = 0;
    let found = false;
    for (const k of parts) {
      if (v[k] !== undefined) { sum += parseFloat(v[k]) || 0; found = true; }
    }
    if (found) return sum;
    return NaN;
  }
  return parseFloat(v);
}

/** Uguaglianza tollerante: un composito tutto-a-zero equivale a 0. */
function same(val, expected) {
  if (val === expected) return true;
  if (val && typeof val === 'object' && typeof expected === 'number') {
    const n = numeric(val);
    return !Number.isNaN(n) && n === expected;
  }
  return false;
}

// Alias → operatore canonico. Copre entrambe le forme storiche (`op` e `operator`).
const ALIAS = {
  eq: 'eq', '=': 'eq', '==': 'eq', '===': 'eq', equals: 'eq', is: 'eq',
  neq: 'neq', ne: 'neq', '!=': 'neq', '!==': 'neq', '<>': 'neq', not: 'neq',
  in: 'in', 'not-in': 'notIn', notIn: 'notIn', nin: 'notIn',
  notEmpty: 'notEmpty', not_empty: 'notEmpty', filled: 'notEmpty',
  empty: 'empty', isEmpty: 'empty',
  gt: 'gt', '>': 'gt', gte: 'gte', '>=': 'gte',
  lt: 'lt', '<': 'lt', lte: 'lte', '<=': 'lte',
};

/**
 * @param {Object} condition  { field, op|operator, value }
 * @param {Object} settings   valori correnti (tile.settings o l'item del repeater)
 * @returns {boolean} true se il campo deve essere visibile
 */
export function evaluateCondition(condition, settings) {
  if (!condition || !settings) return true;
  if (Array.isArray(condition)) return condition.every((c) => evaluateCondition(c, settings));

  const val = settings[condition.field];
  const exp = condition.value;
  const raw = condition.op ?? condition.operator;
  const op = ALIAS[raw] || null;

  switch (op) {
    case 'notEmpty': return !EMPTY(val);
    case 'empty':    return EMPTY(val);
    case 'in':       return Array.isArray(exp) ? exp.some((e) => same(val, e)) : same(val, exp);
    case 'notIn':    return Array.isArray(exp) ? !exp.some((e) => same(val, e)) : !same(val, exp);
    case 'eq':       return Array.isArray(exp) ? exp.some((e) => same(val, e)) : same(val, exp);
    case 'neq':      return Array.isArray(exp) ? !exp.some((e) => same(val, e)) : !same(val, exp);
    case 'gt':       return numeric(val) > numeric(exp);
    case 'gte':      return numeric(val) >= numeric(exp);
    case 'lt':       return numeric(val) < numeric(exp);
    case 'lte':      return numeric(val) <= numeric(exp);
    default:
      // Nessun operatore (o sconosciuto): array = appartenenza, altrimenti uguaglianza.
      return Array.isArray(exp) ? exp.some((e) => same(val, e)) : same(val, exp);
  }
}

/** Visibilità completa di un field: `condition` + `show(settings)`. */
export function isFieldVisible(field, settings) {
  if (!field) return false;
  if (field.condition && !evaluateCondition(field.condition, settings)) return false;
  if (typeof field.show === 'function' && !field.show(settings || {})) return false;
  return true;
}
