// Voci del «Valore» delle condizioni in Assegnazione template.
//
// Prima il valore era un campo di testo sempre visibile con le etichette
// «Pagina (slug)», «Categoria (slug)»: slug e ID si scrivevano a memoria. Ora ogni
// tipo ha il suo controllo (CondValue.vue) e le voci arrivano da una sola rotta,
// GET template-conditions/choices, sulla radice REST del plugin (oloFetch: niente
// '/wp-json/' scritto a mano, che si rompe nelle installazioni in sottocartella e
// con i permalink semplici). Valori: ID per pagine, articoli e categorie; slug per
// tag (l'ID se lo slug ha ottetti %xx, che il salvataggio toglierebbe), tipi di
// contenuto, archivi e ruoli: quelli che il server legge.
//
// Il valore salvato non si riscrive mai da sé: 'all', '0' (e per pagine e categorie
// ogni numero che vale 0) si mostrano come la voce «Tutte…», un valore che non indica
// più niente resta fra le voci come «… (non trovato)», uno scritto a mano come testo
// si mostra coi titoli di ciò che indica sul sito (il server lo cerca col confronto
// esatto del valutatore: 'chi siamo' non è la pagina «Chi siamo»).
import { reactive } from 'vue';
import { t } from '@/i18n';
import { oloFetch } from '@/composables/useApi';

// Tipi che non hanno un valore: nessun controllo (il server non lo legge).
export const TIPI_SENZA_VALORE = new Set([
  'entire_site', 'front_page', 'singular', 'user_logged_in', 'user_logged_out',
  '404', 'search', 'woo_shop', 'woo_product', 'woo_cart', 'woo_checkout', 'woo_account',
]);

// Tipi con un menu. `any`: la voce di valore '' e cosa vuol dire per il server
// (per il ruolo '' non corrisponde a nessuno: niente voce, solo il segnaposto).
// `alias`: valori salvati che il server tratta come '' (empty('0'), 'all').
// `zero`: anche ogni numero che per intval() vale 0 ('00', '0.5') è «qualsiasi»
// (is_page(0), has_category(0)); per post no: lì un numero è un ID e 0 non vale mai.
// `perId`: il menu salva un ID; un testo salvato a mano va detto.
export const SCELTE = {
  page:      { kind: 'page',      any: 'Tutte le pagine',     alias: ['all', '0'], zero: true, perId: true },
  post:      { kind: 'post',      any: 'Tutti gli articoli',  alias: ['all', '0'], perId: true },
  category:  { kind: 'category',  any: 'Qualsiasi categoria', alias: ['all', '0'], zero: true, perId: true },
  tag:       { kind: 'tag',       any: 'Qualsiasi tag',       alias: ['0'] },
  post_type: { kind: 'post_type', any: 'Qualsiasi tipo',      alias: ['0'], fisso: true },
  archive:   { kind: 'archive',   any: 'Tutti gli archivi',   alias: ['all', '0'], fisso: true },
  user_role: { kind: 'role',      any: null, placeholder: 'Scegli un ruolo', alias: [], fisso: true },
};

// Come is_numeric() di PHP sui valori salvati (già senza spazi ai lati).
export function isNumerico(v) {
  return /^[+-]?(\d+(\.\d*)?|\.\d+)([eE][+-]?\d+)?$/.test(String(v ?? '').trim());
}

/** Il valore da mostrare nel menu: '' per i valori che il server tratta come «qualsiasi». */
export function valoreMostrato(tipo, valore) {
  const s = SCELTE[tipo];
  const v = String(valore ?? '');
  if (!s) return v;
  if (s.alias.includes(v)) return '';
  // Math.trunc(Number()) come intval() di PHP su una stringa numerica.
  if (s.zero && isNumerico(v) && Math.trunc(Number(v)) === 0) return '';
  return v;
}

function fold(s) {
  return String(s ?? '').normalize('NFD').replace(/[̀-ͯ]/g, '').toLowerCase();
}

// `${kind}:${valore}` → etichetta; null = cercato e non trovato; assente = non ancora noto.
const etichette = reactive({});
const inRisoluzione = new Set();
const elenchiFissi = {}; // kind → Promise<voci>

function normalizza(data) {
  const rows = data && Array.isArray(data.items) ? data.items : (Array.isArray(data) ? data : null);
  if (!rows) throw new Error(t('risposta non valida'));
  return rows
    .filter((r) => r && r.value != null)
    .map((r) => ({ value: String(r.value), label: String(r.label ?? r.value) }));
}

function registra(kind, voci) {
  for (const v of voci) etichette[`${kind}:${v.value}`] = v.label;
}

function elencoFisso(kind) {
  if (!elenchiFissi[kind]) {
    elenchiFissi[kind] = oloFetch('/template-conditions/choices', { params: { kind } })
      .then((data) => {
        const voci = normalizza(data);
        registra(kind, voci);
        return voci;
      })
      .catch((e) => {
        delete elenchiFissi[kind]; // «Riprova» = riaprire il menu
        throw e;
      });
  }
  return elenchiFissi[kind];
}

/**
 * Voci per la ricerca del menu (prop remoteSearch di CfgSelect). Pagine, articoli,
 * categorie e tag le cerca il server; tipi, archivi e ruoli sono pochi: arrivano
 * interi una volta e si filtrano qui.
 */
export async function cercaScelte(tipo, q) {
  const s = SCELTE[tipo];
  if (!s) return [];
  const testo = String(q ?? '').trim();
  if (s.fisso) {
    const voci = await elencoFisso(s.kind);
    const f = fold(testo);
    return f ? voci.filter((o) => fold(o.label).includes(f) || fold(o.value).includes(f)) : voci;
  }
  const data = await oloFetch('/template-conditions/choices', { params: { kind: s.kind, search: testo } });
  const voci = normalizza(data);
  registra(s.kind, voci);
  return voci;
}

/** Cerca l'etichetta di un valore salvato (una volta per valore). */
export function risolviValore(tipo, valore) {
  const s = SCELTE[tipo];
  const v = valoreMostrato(tipo, valore);
  if (!s || v === '') return;
  const key = `${s.kind}:${v}`;
  if (key in etichette || inRisoluzione.has(key)) return;
  inRisoluzione.add(key);
  const lettura = s.fisso
    ? elencoFisso(s.kind).then(() => { if (!(key in etichette)) etichette[key] = null; })
    : oloFetch('/template-conditions/choices', { params: { kind: s.kind, include: v } }).then((data) => {
      const voce = normalizza(data).find((o) => o.value === v);
      etichette[key] = voce ? voce.label : null;
    });
  // Se la lettura fallisce l'etichetta resta ignota: si mostra il valore com'è.
  lettura.catch(() => {}).then(() => inRisoluzione.delete(key));
}

/** Esito della risoluzione: l'etichetta, null = non indica niente, undefined = non ancora noto. */
export function esitoValore(tipo, valore) {
  const s = SCELTE[tipo];
  const v = valoreMostrato(tipo, valore);
  if (!s || v === '') return undefined;
  return etichette[`${s.kind}:${v}`];
}

/**
 * Etichetta di un valore per il menu e per il riassunto della regola.
 * Non trovato: «#12 (non trovato)» / «“chi-siamo” (non trovato)». Un testo salvato
 * a mano al posto dell'ID si mostra col titolo che indica e il testo accanto.
 */
export function etichettaValore(tipo, valore) {
  const s = SCELTE[tipo];
  const v = valoreMostrato(tipo, valore);
  if (!s) return v;
  if (v === '') return s.any ? t(s.any) : '';
  const perId = s.perId && isNumerico(v);
  const grezzo = perId ? `#${v}` : `“${v}”`;
  const l = etichette[`${s.kind}:${v}`];
  if (l === null) return `${grezzo} (${t('non trovato')})`;
  if (l === undefined) return grezzo;
  if (s.perId && !perId) return `${l} (${grezzo})`;
  return l;
}
