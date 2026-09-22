/**
 * inspectorSearch — helper condivisi per la ricerca impostazioni dell'inspector
 * ("Cerca impostazione..."). Usati da BuilderInspector (tab Contenuto + badge
 * conteggio sui tab) e StyleFieldsRenderer (tab Stile), così il filtro
 * label/key è identico cross-tab.
 */
import { t } from '@/i18n';

/** Normalizza la query (trim + lowercase). Stringa vuota = ricerca inattiva. */
export function normalizeSearchQuery(query) {
  return (query || '').trim().toLowerCase();
}

function textMatches(text, query) {
  if (!text || typeof text !== 'string') return false;
  return text.toLowerCase().includes(query) || t(text).toLowerCase().includes(query);
}

/**
 * Match case-insensitive di un field contro la query già normalizzata:
 * label (raw + tradotta — l'utente cerca ciò che vede a schermo), key, e
 * `searchTerms[]` opzionali (usati dai pannelli compatti senza label propria
 * tipo box-stack/effects-stack, che inglobano più controlli).
 */
export function fieldMatchesSearch(field, query) {
  if (!query) return true;
  if (!field || field.type === 'separator') return false;
  if (textMatches(field.label, query)) return true;
  if (typeof field.key === 'string' && field.key.toLowerCase().includes(query)) return true;
  if (field.type === 'typography' && typographyTerms(field).some((term) => term.includes(query))) return true;
  if (Array.isArray(field.searchTerms)) {
    return field.searchTerms.some((term) => String(term).toLowerCase().includes(query));
  }
  return false;
}

/**
 * Il controllo `typography` tiene le sue proprietà DENTRO un popover: la sua
 * label dice a cosa si applica ("Titolo"), non cosa contiene. Senza questo,
 * cercare «interlinea» o «peso» non troverebbe nulla in nessuna delle ~100 tile
 * che lo montano. I termini si costruiscono sulle chiavi REALMENTE mappate —
 * un controllo che non offre il colore non deve uscire cercando «colore» — e
 * includono i nomi delle chiavi salvate, per chi cerca `lead_size`.
 */
const TYPO_TERMS = {
  tag:           'tag html semantica titolo h1 h2 h3 paragrafo',
  family:        'famiglia font carattere tipografia',
  size:          'dimensione corpo grandezza testo px tipografia',
  fluidMin:      'dimensione minima fluida scala clamp responsive',
  fluidMax:      'dimensione massima fluida scala clamp responsive',
  maxWidth:      'larghezza massima misura riga caratteri ch colonna',
  weight:        'peso grassetto bold spessore',
  lineHeight:    'interlinea altezza riga leading',
  letterSpacing: 'spaziatura caratteri lettere tracking crenatura',
  wordSpacing:   'spaziatura parole',
  transform:     'trasformazione maiuscolo minuscolo capitalizza',
  style:         'stile corsivo italic obliquo',
  decoration:    'decorazione sottolineato barrato',
  color:         'colore testo',
  colorHover:    'colore testo hover',
  shadow:        'ombra testo',
};

function typographyTerms(field) {
  const out = [];
  for (const [logical, key] of Object.entries(field.keys || {})) {
    if (!key) continue;
    if (TYPO_TERMS[logical]) out.push(TYPO_TERMS[logical]);
    out.push(String(key).toLowerCase());
  }
  if (field.presetKey) out.push('preset stile tipografico globale');
  return out;
}

/** Match della label di una sezione (separator): se matcha, l'intera sezione resta visibile. */
export function sectionLabelMatchesSearch(label, query) {
  if (!query) return false;
  return textMatches(label, query);
}

/**
 * Conta i field che la ricerca mostrerebbe in una lista flat di field
 * (separator inclusi): un field conta se matcha direttamente o se la label
 * della sua sezione matcha. Usato per i badge conteggio sui tab.
 */
export function countSearchMatches(fields, query) {
  if (!query) return 0;
  let count = 0;
  let sectionMatches = false;
  for (const f of fields || []) {
    if (!f) continue;
    if (f.type === 'separator') {
      sectionMatches = sectionLabelMatchesSearch(f.label, query);
      continue;
    }
    if (sectionMatches || fieldMatchesSearch(f, query)) count++;
  }
  return count;
}
