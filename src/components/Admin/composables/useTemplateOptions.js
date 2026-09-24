// Elenco dei template per i menu della Configurazione (Assegnazione template,
// Manutenzione, Popup, Template WooCommerce). Prima ogni scheda aveva la sua copia,
// che trattava la risposta di GET /templates ({ items, total, pages, byType }) come
// un array: il .map andava in errore, il catch lo nascondeva e il menu restava
// vuoto, con le scelte già salvate mostrate come «—». Template WooCommerce leggeva
// i 200 template modificati più di recente: oltre quelli non si potevano assegnare.
//
// - Una sola richiesta leggera (`fields`: niente content/settings) condivisa dalle
//   schede, che restano montate nel KeepAlive della shell; tutte le pagine.
// - Un errore non resta in memoria: lo stato 'error' si mostra (TemplateListError)
//   e «Riprova» rifà la richiesta.
// - Il template già scelto resta SEMPRE fra le voci, anche se il filtro lo
//   escluderebbe o se non esiste più: il valore salvato non si tocca mai.
import { ref } from 'vue';
import { t } from '@/i18n';
import { oloFetch } from '@/composables/useApi';

const PER_PAGE = 500;
const MAX_PAGES = 20;

const list = ref([]);          // [{ id, title, type, status }] ordinati per titolo
const state = ref('idle');     // 'idle' | 'loading' | 'ready' | 'error'
// false se l'elenco si è fermato a MAX_PAGES: un id che non c'è può esistere lo stesso.
const complete = ref(true);
let pending = null;
let seq = 0;

// Stessi nomi della gestione template (TYPE_META di TemplateList.vue).
function typeLabel(type) {
  const map = {
    page: t('Pagina'), header: t('Header'), footer: t('Footer'), single: t('Single'),
    megapanel: t('Mega Panel'), widget: t('Widget'), '404': '404',
  };
  return map[type] || String(type || '');
}

function normalize(rows) {
  const byId = new Map();
  for (const r of rows) {
    const id = parseInt(r && (r.id ?? r.ID), 10) || 0;
    if (id <= 0) continue;
    const title = String((r.title ?? r.post_title) || '').trim();
    byId.set(id, {
      id,
      title,
      type: String(r.type || 'page'),
      status: String(r.status || ''),
    });
  }
  const items = Array.from(byId.values());
  // Titoli uguali (es. più «Coming Soon — Default» generati) restano distinguibili.
  const count = new Map();
  for (const it of items) count.set(it.title, (count.get(it.title) || 0) + 1);
  for (const it of items) {
    if (!it.title) it.title = `${t('Senza titolo')} #${it.id}`;
    else if (count.get(it.title) > 1) it.title = `${it.title} #${it.id}`;
  }
  items.sort((a, b) => a.title.localeCompare(b.title, undefined, { sensitivity: 'base', numeric: true }) || a.id - b.id);
  return items;
}

async function fetchAll() {
  const rows = [];
  let pages = 1;
  for (let page = 1; page <= pages && page <= MAX_PAGES; page++) {
    const data = await oloFetch('/templates', {
      params: { fields: 'id,title,type,status', per_page: PER_PAGE, page },
    });
    if (Array.isArray(data)) { rows.push(...data); break; }
    if (!data || !Array.isArray(data.items)) throw new Error(t('risposta non valida'));
    rows.push(...data.items);
    pages = Number(data.pages) || 1;
  }
  const truncated = pages > MAX_PAGES;
  if (truncated) {
    console.warn(`[Olobuild] Elenco dei template fermato a ${MAX_PAGES * PER_PAGE} voci su ${pages} pagine.`);
  }
  return { items: normalize(rows), truncated };
}

function load(force = false) {
  if (pending && !force) return pending;
  if (!force && state.value === 'ready') return Promise.resolve(list.value);
  const mine = ++seq;
  state.value = 'loading';
  const p = fetchAll()
    .then(({ items, truncated }) => {
      // Una richiesta superata da un «Riprova» o da un refresh non sovrascrive.
      if (mine === seq) { list.value = items; complete.value = !truncated; state.value = 'ready'; }
    })
    .catch((e) => {
      if (mine === seq) {
        state.value = 'error';
        console.warn('[Olobuild] Elenco dei template non caricato:', e);
      }
    })
    .then(() => {
      if (pending === p) pending = null;
      return list.value;
    });
  pending = p;
  return p;
}

function refresh() {
  return load(true);
}

/**
 * Voci per CfgSelect.
 * @param {Object}   o
 * @param {string[]} [o.types]         tipi ammessi (null = tutti)
 * @param {boolean}  [o.publishedOnly] solo pubblicati (header e footer: le bozze non si vedono sul sito)
 * @param {boolean}  [o.showType]      tipo accanto al titolo (menu con tutti i tipi)
 * @param {number}   [o.selected]      template già scelto: resta sempre fra le voci
 * @param {string}   [o.emptyLabel]    voce di valore 0
 */
function optionsFor({ types = null, publishedOnly = false, showType = false, selected = 0, emptyLabel } = {}) {
  const sel = parseInt(selected, 10) || 0;
  const passes = (tpl) =>
    (!types || types.includes(tpl.type)) && (!publishedOnly || tpl.status === 'published');
  const opts = [{ value: 0, label: emptyLabel || t('— Seleziona template —') }];
  let found = false;
  for (const tpl of list.value) {
    if (!passes(tpl)) continue;
    if (tpl.id === sel) found = true;
    opts.push({ value: tpl.id, label: showType ? `${tpl.title} · ${typeLabel(tpl.type)}` : tpl.title });
  }
  if (sel > 0 && !found) {
    const tpl = list.value.find((x) => x.id === sel);
    let label;
    if (tpl) {
      const why = [];
      if (types && !types.includes(tpl.type)) why.push(`${t('tipo')}: ${typeLabel(tpl.type)}`);
      if (publishedOnly && tpl.status !== 'published') why.push(t('bozza: non compare sul sito'));
      label = why.length ? `${tpl.title} (${why.join(', ')})` : tpl.title;
    } else if (state.value === 'ready' && complete.value) {
      label = `${t('Template non trovato')} #${sel}`;
    } else {
      // Elenco non (ancora) arrivato o fermato a MAX_PAGES: non si sa se esiste,
      // si mostra solo il numero.
      label = `${t('Template')} #${sel}`;
    }
    opts.splice(1, 0, { value: sel, label });
  }
  return opts;
}

export function useTemplateOptions() {
  return { list, state, load, refresh, optionsFor };
}
