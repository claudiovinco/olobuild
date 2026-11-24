/**
 * Lo Slider Pro leggibile (1.4.1352). Utente, 23 nov 2026: «il banner hero slider pro, che spesso aprendolo non si capisce
 * nulla… migliorare la gestione degli slider pro in modo da poter gestire bene le animazioni… attualmente sono
 * ingestibili e sembrano trucchi di magia».
 *
 * Qui le regole in comune fra la tela dell'editor, il pannello Livelli e il pannello delle proprietà:
 *  - il movimento con lo scorrimento (scrollMove) calcolato COME sul sito (gemello di olo-proslider.js, «Livelli che si
 *    muovono con lo scorrimento»), per vederlo sulla tela trascinando il cursore «Scorrimento»;
 *  - gli stati (stato.vale «a,b» o «!a,b»), gemelli di stato_vale() del PHP;
 *  - il nome di un livello e le sue animazioni, per il pannello Livelli;
 *  - i gruppi del pannello Livelli (1.4.1424).
 */
import { t } from '@/i18n';

/** Le grandezze che il movimento con lo scorrimento sa muovere (le stesse del PHP, scroll_move_attr()). */
export const SM_CHIAVI = ['x', 'y', 's', 'sx', 'sy', 'r', 'rx', 'ry', 'o', 'mt', 'mr', 'mb', 'ml', 'wdth', 'wght'];

function numeri(v) {
  const o = {};
  if (!v || typeof v !== 'object') return o;
  for (const k of SM_CHIAVI) {
    if (v[k] !== undefined && v[k] !== '' && v[k] !== null && !Number.isNaN(+v[k])) o[k] = +v[k];
  }
  return o;
}

/** Le tappe di un movimento, ordinate: [{ p, v: { x, o, … } }]. Senza tappe: «da» a 0 e «a» a 100. */
export function tappeDi(sm) {
  if (!sm || typeof sm !== 'object') return [];
  let t2;
  if (Array.isArray(sm.tappe) && sm.tappe.length) {
    t2 = sm.tappe.filter((x) => x && typeof x === 'object').map((x) => ({ p: Math.max(0, Math.min(100, +x.p || 0)), v: numeri(x) }));
  } else {
    t2 = [{ p: 0, v: numeri(sm.da) }, { p: 100, v: numeri(sm.a) }];
  }
  return t2.sort((a, b) => a.p - b.p);
}

/** Le grandezze che il movimento usa davvero (in almeno una tappa), nell'ordine di SM_CHIAVI. */
export function chiaviUsate(sm) {
  const t2 = tappeDi(sm);
  return SM_CHIAVI.filter((k) => t2.some((x) => x.v[k] !== undefined));
}

function base(k) {
  if (k === 's' || k === 'sx' || k === 'sy') return 1;
  if (k === 'o' || k === 'wdth') return 100;
  if (k === 'wght') return 400;
  return 0;
}

/** Il valore di una grandezza al punto k (0–100) del tratto del livello: «morbido» o «costante», come sul sito. */
export function valoreSM(t2, k, chiave, costante) {
  if (!t2.length) return base(chiave);
  let u = 0;
  for (let i = 0; i < t2.length - 1; i++) { if (t2[i + 1].p >= k) { u = i; break; } u = i; }
  const a = t2[u], b = t2[Math.min(u + 1, t2.length - 1)];
  const span = Math.max(0.0001, b.p - a.p);
  let f = Math.max(0, Math.min(1, (k - a.p) / span));
  if (!costante) f = f * f * (3 - 2 * f);
  let va = a.v[chiave], vb = b.v[chiave];
  const d = base(chiave);
  va = va == null ? d : va; vb = vb == null ? d : vb;
  const scala = chiave === 's' || chiave === 'sx' || chiave === 'sy';
  if (costante && scala && va > 0 && vb > 0 && va !== vb) return va * Math.pow(vb / va, f);
  return va + (vb - va) * f;
}

/** Dall'avanzamento della scena (0–100) a quello del livello nel suo tratto (inizio, fine), e viceversa. */
export function delLivello(sm, pScena) {
  const i = Math.max(0, Math.min(100, +(sm?.inizio ?? 0)));
  const f = Math.max(0, Math.min(100, +(sm?.fine ?? 100)));
  return f > i ? Math.max(0, Math.min(100, (pScena - i) / (f - i) * 100)) : (pScena >= i ? 100 : 0);
}
export function dellaScena(sm, k) {
  const i = Math.max(0, Math.min(100, +(sm?.inizio ?? 0)));
  const f = Math.max(0, Math.min(100, +(sm?.fine ?? 100)));
  return i + k * (f - i) / 100;
}

/**
 * Lo stile di un livello in movimento al punto pScena della scena (0–100): le proprietà singole translate, scale e
 * rotate (si sommano al transform del livello), l'opacità come fattore e la maschera come clip-path. `fattore` riduce
 * gli spostamenti dalla tela del computer (1440 px) a quella dell'editor.
 */
export function statoSM(sm, pScena, fattore = 1) {
  const t2 = tappeDi(sm);
  if (t2.length < 2) return null;
  const k = delLivello(sm, pScena);
  const c = sm.andamento === 'costante';
  const usa = (chiavi) => t2.some((x) => chiavi.some((ch) => x.v[ch] !== undefined));
  const out = {};
  if (usa(['x', 'y'])) out.translate = (valoreSM(t2, k, 'x', c) * fattore).toFixed(1) + 'px ' + (valoreSM(t2, k, 'y', c) * fattore).toFixed(1) + 'px';
  if (usa(['s', 'sx', 'sy'])) {
    const sc = valoreSM(t2, k, 's', c), sx = valoreSM(t2, k, 'sx', c), sy = valoreSM(t2, k, 'sy', c);
    out.scale = sx !== 1 || sy !== 1 ? (sc * sx).toFixed(4) + ' ' + (sc * sy).toFixed(4) : sc.toFixed(4);
  }
  if (usa(['r', 'rx', 'ry'])) {
    const rx = valoreSM(t2, k, 'rx', c), ry = valoreSM(t2, k, 'ry', c);
    out.rotate = rx ? 'x ' + rx.toFixed(2) + 'deg' : (ry ? 'y ' + ry.toFixed(2) + 'deg' : valoreSM(t2, k, 'r', c).toFixed(2) + 'deg');
  }
  if (usa(['o'])) out.opacita = Math.max(0, Math.min(100, valoreSM(t2, k, 'o', c))) / 100;
  if (usa(['mt', 'mr', 'mb', 'ml'])) {
    out.clipPath = 'inset(' + ['mt', 'mr', 'mb', 'ml'].map((ch) => Math.max(0, Math.min(100, valoreSM(t2, k, ch, c))).toFixed(2) + '%').join(' ') + ')';
  }
  return out;
}

/* ── Stati ──────────────────────────────────────────────────────────────── */

/** «luce=spenta; tinta=bianco» → { luce: 'spenta', tinta: 'bianco' } (gemella di leggi_stati() del PHP). */
export function leggiStati(testo) {
  const out = {};
  String(testo || '').split(/[;\n]+/).forEach((coppia) => {
    const [n, ...resto] = coppia.split('=');
    const nome = String(n || '').trim().toLowerCase().replace(/[^a-z0-9_-]/g, '');
    if (nome) out[nome] = resto.join('=').trim();
  });
  return out;
}

/** Il valore è fra quelli della condizione? «a,b» = uno dei due; «!a,b» = nessuno dei due. */
export function statoVale(condizione, valore) {
  const c = String(condizione || '').trim();
  const neg = c.charAt(0) === '!';
  const lista = (neg ? c.slice(1) : c).split(',').map((x) => x.trim());
  const dentro = lista.includes(String(valore ?? ''));
  return neg ? !dentro : dentro;
}

/** Gli stati di una slide, coi valori che i livelli nominano: { spia: ['olio', 'freni'], … } (per il menu «Stato»). */
export function statiDellaSlide(layers, iniziali = {}) {
  const st = {};
  const aggiungi = (nome, v) => {
    const n = String(nome || '').trim().toLowerCase();
    if (!n) return;
    st[n] = st[n] || new Set();
    String(v ?? '').replace(/^!/, '').split(',').map((x) => x.trim()).filter((x) => x !== '').forEach((x) => st[n].add(x));
  };
  for (const l of layers || []) {
    if (l?.stato?.nome) aggiungi(l.stato.nome, l.stato.vale);
    const a = l?.action;
    if (a && (a.type === 'setState' || a.type === 'cycleState')) aggiungi(a.target, a.type === 'setState' ? a.value : a.values);
    if (l?.pulisci?.stato && String(l.pulisci.stato).includes('=')) {
      const [n, v] = String(l.pulisci.stato).split('=');
      aggiungi(n, v);
    }
  }
  for (const [n, v] of Object.entries(iniziali || {})) aggiungi(n, v);
  return Object.fromEntries(Object.entries(st).map(([n, s]) => [n, [...s]]));
}

/* ── Nome e animazioni di un livello (pannello Livelli) ─────────────────── */

const TIPI = { text: 'Testo', image: 'Immagine', video: 'Video', button: 'Pulsante', icon: 'Icona', shape: 'Forma', audio: 'Audio' };

function nomeFile(url) {
  const f = String(url || '').split(/[?#]/)[0].split('/').pop() || '';
  const n = f.replace(/\.[a-z0-9]+$/i, '');
  // i nomi delle foto d'archivio (pexels-123456, IMG_0042) non dicono niente
  if (!n || /^(pexels|pixabay|unsplash|img|dsc|photo|video)[-_ ]?(video[-_ ]?)?\d*/i.test(n) || /^\d+$/.test(n)) return '';
  const pulito = n.replace(/[-_]+/g, ' ').replace(/\s+/g, ' ').trim();
  return pulito.charAt(0).toUpperCase() + pulito.slice(1);
}

/**
 * Il nome di un livello nella barra: quello scritto in «Nome del livello»; poi il testo (testi e pulsanti), il nome
 * del file (immagini e video, se dice qualcosa), la parte parlante dell'id messo dal tema («ae-cielo» → «Cielo»), e
 * infine il tipo.
 */
export function nomeLivello(l) {
  if (!l) return '';
  if (l.name && String(l.name).trim()) return String(l.name).trim();
  // il testo senza i segni delle parole colorate o in grassetto (<br>, <span …>): il nome è quello che si legge
  if ((l.type === 'text' || l.type === 'button') && String(l.content || '').trim()) {
    const leggibile = String(l.content).replace(/<br\s*\/?>/gi, ' ').replace(/<[^>]*>/g, '').replace(/&nbsp;/g, ' ').replace(/&amp;/g, '&').replace(/\s+/g, ' ').trim();
    if (leggibile) return leggibile;
  }
  if (l.type === 'icon' && l.iconName) return String(l.iconName);
  const f = nomeFile(l.type === 'video' ? l.videoSrc : l.imageSrc);
  if (f) return f;
  const m = String(l.id || '').match(/^[a-z]{1,4}-([a-z][a-z0-9-]{3,})$/i);
  if (m && !/^ps-/.test(l.id)) {
    const p = m[1].replace(/-/g, ' ');
    return p.charAt(0).toUpperCase() + p.slice(1);
  }
  return t(TIPI[l.type] || l.type || 'Livello');
}

/**
 * Le animazioni di un livello, per i bollini della barra: [{ k, testo, titolo }]. Il titolo dice in parole cosa fa.
 */
export function animazioniLivello(l) {
  const out = [];
  if (!l) return out;
  if (l.scrollMove) {
    const t2 = tappeDi(l.scrollMove);
    out.push({ k: 'scroll', testo: '↕', titolo: t('Si muove scorrendo la pagina') + (t2.length > 2 ? ' · ' + t2.length + ' ' + t('tappe') : '') });
  }
  if (l.timeline?.keyframes?.length >= 2) out.push({ k: 'timeline', testo: '⏱', titolo: t('Linea del tempo') + ' · ' + l.timeline.keyframes.length + ' ' + t('fotogrammi chiave') });
  if (l.charAnim) out.push({ k: 'lettere', testo: 'Aa', titolo: t('Le lettere entrano una alla volta') });
  if (l.stato?.nome) out.push({ k: 'stato', testo: '◐', titolo: t('Si vede solo in uno stato') + ': ' + l.stato.nome + ' = ' + (l.stato.vale || '') });
  if (l.action && l.action.type && l.action.type !== 'none') out.push({ k: 'azione', testo: '☝', titolo: t('Fa qualcosa al clic') });
  if (l.pulisci?.attivo) out.push({ k: 'pulisci', testo: '✎', titolo: t('Si cancella passandoci sopra') });
  if (l.animLoop && l.animLoop !== 'none') out.push({ k: 'ciclo', testo: '∞', titolo: t('Animazione continua') });
  if (l.parallax || l.parallaxDepth) out.push({ k: 'parallasse', testo: '⇄', titolo: t('Parallasse') });
  if (l.kenBurns) out.push({ k: 'kenburns', testo: '⤢', titolo: 'Ken Burns' });
  if (l.customCSS && String(l.customCSS).trim()) out.push({ k: 'css', testo: '{}', titolo: t('Ha un CSS su misura (Avanzate)') });
  if (l.initiallyHidden) out.push({ k: 'nascosto', testo: '⌀', titolo: t('Nascosto all’avvio: lo mostra un altro livello al clic') });
  // visible:false = non si vede sul computer (i formati più piccoli ereditano, salvo «Visibile» nelle loro regole)
  const formati = formatiVisibili(l);
  if (formati.length < 4) {
    const nomi = { desktop: t('computer'), notebook: t('notebook'), tablet: t('tablet'), mobile: t('telefono') };
    out.push({ k: 'formati', testo: '▭', titolo: formati.length ? t('Si vede solo su') + ': ' + formati.map((f) => nomi[f]).join(', ') : t('Non si vede su nessuno schermo') });
  }
  return out;
}

/** Gli schermi su cui un livello si vede (gemella di visibile_nel_formato() del PHP). */
export function formatiVisibili(l) {
  const out = [];
  let vis = !(l && l.visible === false);
  if (vis) out.push('desktop');
  for (const f of ['notebook', 'tablet', 'mobile']) {
    const ov = l?.responsive?.[f];
    if (ov && typeof ov.visible === 'boolean') vis = ov.visible;
    if (vis) out.push(f);
  }
  return out;
}

/* ── Gruppi dei livelli (1.4.1424) ─────────────────────────────────────── */
// Utente, 24 nov 2026: «quando i livelli sono tanti come si fa a scorrerne 10-15 o più e capirci ancora qualche cosa?».
// Il pannello Livelli li raccoglie in gruppi che si fanno da soli da ciò che i livelli FANNO (come i gruppi rientrati
// del pannello Livelli di Slider Revolution 7): Cric passa da 34 livelli in fila a 8 gruppi, uno per spia.
//  1. «gruppo» scritto nel livello (campo «Gruppo» del pannello o «Rinomina» del gruppo): vince sempre;
//  2. una scelta: il livello che col clic imposta uno stato e quelli che si vedono quando è scelto;
//  3. «Prima di ogni scelta»: visibili finché lo stato è vuoto;
//  4. gli altri stati («Quando luce è accesa», «Finché luce non è accesa»);
//  5. le serie coi nomi numerati (tp-anello1-0, -1, -2…: la cupola di Tepidario), almeno tre;
//  6. i veli, i testi e i pulsanti, le forme, le foto e i disegni.
// Nel salvataggio entra solo «gruppo», e solo quando l'utente lo scrive: il sito non lo legge.

const nomeStatoG = (n) => String(n || '').trim().toLowerCase().replace(/[^a-z0-9_-]/g, '');
const baseSerie = (id) => {
  const s = String(id || '');
  if (/^ps-/.test(s)) return ''; // gli id fatti dall'editor (ps-<tempo><caso>) non sono serie
  const m = s.match(/^(.*?)[-_]?\d+$/);
  return m && m[1] ? m[1] : '';
};
// Il nome di una scelta: quello del livello, la sua etichetta per la tastiera («Tinta Terracotta»), il testo, il valore.
function nomeScelta(l, valore) {
  const nome = String(l?.name || '').trim() || String(l?.action?.label || '').trim();
  if (nome) return nome;
  if ((l?.type === 'text' || l?.type === 'button') && String(l.content || '').trim()) return nomeLivello(l);
  return valore;
}
function nomeSerie(b) {
  const p = String(b).replace(/^[a-z]{1,4}-/i, '').replace(/[-_]+/g, ' ').replace(/([a-zà-ù])(\d)/gi, '$1 $2').trim();
  return p ? p.charAt(0).toUpperCase() + p.slice(1) : String(b);
}

/**
 * I gruppi di un elenco di livelli: [{ key, tipo, nome, livelli: [dal più in alto], top }], in cima il gruppo col
 * livello più in alto. tipo: manuale · scelta · prima · stato · serie · veli · testi · forme · media.
 * Per scelta e prima: { stato, valore }; per stato: { stato, vale }.
 */
export function gruppiLivelli(layers) {
  const L = (layers || []).filter(Boolean);
  const scelte = new Map(); // «stato=valore» → il livello che la imposta
  L.forEach((l) => {
    const a = l.action;
    if (a?.type === 'setState' && nomeStatoG(a.target)) {
      const k = nomeStatoG(a.target) + '=' + String(a.value ?? '').trim();
      if (!scelte.has(k)) scelte.set(k, l);
    }
  });
  const agisce = (l) => !!(l.action && l.action.type && l.action.type !== 'none');
  // le serie sono di forme, foto e icone: i testi numerati (to-n1, to-n2…) restano testi, ognuno col suo nome
  const inSerie = (l) => l.type !== 'text' && l.type !== 'button' && !String(l.gruppo || '').trim() && !l.stato?.nome && !agisce(l);
  const contaBase = {};
  L.forEach((l) => {
    const b = baseSerie(l.id);
    if (b && inSerie(l)) contaBase[b] = (contaBase[b] || 0) + 1;
  });
  const gruppi = new Map();
  const metti = (key, g, l, i) => {
    if (!gruppi.has(key)) gruppi.set(key, { key, ...g, livelli: [], top: -1 });
    const x = gruppi.get(key);
    x.livelli.push(l);
    x.top = Math.max(x.top, i);
    // il nome scritto dall'utente su una scelta o uno stato ne cambia solo l'etichetta (1.4.1428): il gruppo resta
    // la scelta, con «Mostra sulla tela» e la sua riga nelle Interazioni (prima la rinomina la faceva sparire)
    const nm = String(l.gruppo || '').trim();
    if (nm && g.tipo !== 'manuale' && !x.nomeManuale) x.nomeManuale = nm;
  };
  L.forEach((l, i) => {
    const manuale = String(l.gruppo || '').trim();
    const a = l.action || {};
    const semantico = (a.type === 'setState' && nomeStatoG(a.target)) || nomeStatoG(l.stato?.nome);
    if (manuale && !semantico) return metti('m:' + manuale.toLowerCase(), { tipo: 'manuale', nome: manuale }, l, i);
    if (a.type === 'setState' && nomeStatoG(a.target)) {
      const n = nomeStatoG(a.target), v = String(a.value ?? '').trim();
      return metti('s:' + n + '=' + v, { tipo: 'scelta', nome: nomeScelta(scelte.get(n + '=' + v), v), stato: n, valore: v }, l, i);
    }
    const n = nomeStatoG(l.stato?.nome);
    if (n) {
      const v = String(l.stato.vale ?? '').trim();
      if (v === '') return metti('p:' + n, { tipo: 'prima', nome: '', stato: n, valore: '' }, l, i);
      if (!v.startsWith('!') && !v.includes(',') && scelte.has(n + '=' + v)) {
        return metti('s:' + n + '=' + v, { tipo: 'scelta', nome: nomeScelta(scelte.get(n + '=' + v), v), stato: n, valore: v }, l, i);
      }
      return metti('st:' + n + '=' + v, { tipo: 'stato', nome: '', stato: n, vale: v }, l, i);
    }
    const b = baseSerie(l.id);
    if (b && inSerie(l) && contaBase[b] >= 3) return metti('r:' + b, { tipo: 'serie', nome: nomeSerie(b) }, l, i);
    if (l.shapeGradient) return metti('veli', { tipo: 'veli', nome: '' }, l, i);
    if (l.type === 'text' || l.type === 'button') return metti('testi', { tipo: 'testi', nome: '' }, l, i);
    if (l.type === 'shape') return metti('forme', { tipo: 'forme', nome: '' }, l, i);
    return metti('media', { tipo: 'media', nome: '' }, l, i);
  });
  // I gruppi generici troppo lunghi (più di 10 forme o foto) si dividono per famiglia di nome (1.4.1428): in Fuorigiri le
  // 25 forme diventano «pre», «g», «via»; in Strappo i tre fogli «1», «2», «3». Famiglia = le lettere (o le cifre) con
  // cui comincia il nome dopo il prefisso del tema (fg-pre1x-1-0 → pre), almeno tre livelli per famiglia.
  const famiglia = (id) => {
    const r = String(id || '');
    if (/^ps-/.test(r)) return '';
    const s = r.replace(/^[a-z]{1,4}-/i, '');
    const m = s.match(/^[a-zà-ù]+/i) || s.match(/^\d+/);
    return m ? m[0].toLowerCase() : '';
  };
  for (const g of [...gruppi.values()]) {
    // anche i «veli» troppi: in Vertebra le vertebre sono forme sfumate, non veli
    if (!['forme', 'media', 'veli'].includes(g.tipo) || g.livelli.length <= 10) continue;
    const per = {};
    g.livelli.forEach((l) => { const f = famiglia(l.id); if (f) (per[f] = per[f] || []).push(l); });
    const prefisso = (String(g.livelli[0]?.id || '').match(/^[a-z]{1,4}-/i) || [''])[0];
    for (const [f, membri] of Object.entries(per)) {
      if (membri.length < 3 || membri.length === g.livelli.length) continue;
      const nuovo = { key: 'f:' + g.key + ':' + f, tipo: 'serie', nome: nomeSerie(prefisso + f), livelli: membri, top: Math.max(...membri.map((l) => L.indexOf(l))) };
      gruppi.set(nuovo.key, nuovo);
      g.livelli = g.livelli.filter((l) => !membri.includes(l));
    }
    if (!g.livelli.length) gruppi.delete(g.key);
    else g.top = Math.max(...g.livelli.map((l) => L.indexOf(l)));
  }
  return [...gruppi.values()]
    .map((g) => ({ ...g, livelli: g.livelli.slice().reverse() }))
    .sort((a, b) => b.top - a.top);
}

/** Il nome di un gruppo da mostrare: «Scelta: Spia olio motore», «Quando luce è accesa», «Veli»… */
export function nomeGruppo(g) {
  if (!g) return '';
  switch (g.tipo) {
    case 'manuale': return g.nome;
    default: break;
  }
  if (g.nomeManuale) return g.nomeManuale;
  switch (g.tipo) {
    case 'scelta': return t('Scelta: %s').replace('%s', g.nome || g.valore);
    case 'prima': return t('Prima di ogni scelta');
    case 'stato': {
      const neg = String(g.vale).startsWith('!');
      const valori = String(g.vale).replace(/^!/, '').split(',').map((x) => x.trim()).filter(Boolean).join(t(' o '));
      return (neg ? t('Finché %1 non è %2') : t('Quando %1 è %2')).replace('%1', g.stato).replace('%2', valori);
    }
    // un nome di serie corto («Pre», «3», «D») da solo non dice niente: «Serie «Pre»»
    case 'serie': return String(g.nome).length <= 3 ? t('Serie «%s»').replace('%s', g.nome) : g.nome;
    case 'veli': return t('Veli');
    case 'testi': return t('Testi e pulsanti');
    case 'forme': return t('Forme');
    default: return t('Foto, video e disegni');
  }
}

/** Le etichette colorate dei livelli (1.4.1428, come le etichette di After Effects): solo nell'editor, nel campo «etichetta». */
export const ETICHETTE = [
  { k: 'rosso', nome: t('Rosso'), c: '#ef4444' },
  { k: 'arancio', nome: t('Arancio'), c: '#f97316' },
  { k: 'giallo', nome: t('Giallo'), c: '#eab308' },
  { k: 'verde', nome: t('Verde'), c: '#22c55e' },
  { k: 'azzurro', nome: t('Azzurro'), c: '#38bdf8' },
  { k: 'viola', nome: t('Viola'), c: '#a855f7' },
  { k: 'grigio', nome: t('Grigio'), c: '#9ca3af' },
];

/* ── I comandi che nei temi erano CSS su misura (1.4.1431): gemelli di comandi_ex_css() del PHP, per la tela ── */
const MISURA_C = /^-?\d*\.?\d+(px|%|em|rem|vw|vh)?$/;
const PAROLE_C = /^[a-z0-9.%\s-]{1,40}$/i;
const COLORE_SICURO = (c) => (/^[#a-z0-9(),.%\s/-]+$/i.test(String(c || '')) && !/[;{}<>]/.test(String(c || '')) ? String(c).trim() : '');

/** Una sfumatura dai comandi ({tipo, ripeti, angolo | forma, dimensione, posizione, tappe}) in CSS. */
export function sfumaturaCss(g) {
  const tappe = (g?.tappe || []).map((tp) => {
    const c = COLORE_SICURO(tp?.colore);
    if (!c) return '';
    return [c, ...['da', 'a'].map((k) => String(tp[k] ?? '').trim()).filter((v) => v && MISURA_C.test(v))].join(' ');
  }).filter(Boolean);
  if (tappe.length < 2) return '';
  const radiale = g.tipo === 'radiale';
  let testa = '';
  if (radiale) {
    const forma = ['circle', 'ellipse'].includes(g.forma) ? g.forma : '';
    const dim = PAROLE_C.test(String(g.dimensione || '')) ? String(g.dimensione).trim() : '';
    const pos = PAROLE_C.test(String(g.posizione || '')) ? String(g.posizione).trim() : '';
    testa = [forma, dim].filter(Boolean).join(' ');
    if (pos) testa = (testa + ' at ' + pos).trim();
  } else if (g.angolo !== undefined && g.angolo !== '' && !Number.isNaN(Number(g.angolo))) {
    testa = Number(g.angolo) + 'deg';
  }
  return (g.ripeti ? 'repeating-' : '') + (radiale ? 'radial' : 'linear') + '-gradient(' + (testa ? testa + ',' : '') + tappe.join(',') + ')';
}

/** Lo stile dei comandi ex CSS di un livello, come oggetto per Vue. */
export function stileComandi(l) {
  const st = {};
  if (!l) return st;
  if (l.type === 'shape' && Array.isArray(l.sfondoForma) && l.sfondoForma.length) {
    const parti = l.sfondoForma.map(sfumaturaCss).filter(Boolean);
    if (parti.length) {
      if (l.sfondoFormaSulColore) st.backgroundImage = parti.join(',');
      else st.background = parti.join(',') + (COLORE_SICURO(l.sfondoFormaColore) ? ',' + COLORE_SICURO(l.sfondoFormaColore) : '');
    }
  }
  if (Array.isArray(l.ombre) && l.ombre.length) {
    st.boxShadow = l.ombre.map((o) => (o.inset ? 'inset ' : '') + `${parseInt(o.x, 10) || 0}px ${parseInt(o.y, 10) || 0}px ${Math.abs(parseInt(o.blur, 10) || 0)}px ${parseInt(o.spread, 10) || 0}px ${COLORE_SICURO(o.color) || 'rgba(0,0,0,.3)'}`).join(',');
  }
  if (l.ombraForma) {
    const o = l.ombraForma;
    st.filter = `drop-shadow(${parseInt(o.x, 10) || 0}px ${parseInt(o.y, 10) || 0}px ${Math.abs(parseInt(o.blur, 10) || 0)}px ${COLORE_SICURO(o.color) || 'rgba(0,0,0,.4)'})`;
  }
  if (Number(l.sfumaColori)) st.transition = `background-color ${Math.min(5000, Math.abs(parseInt(l.sfumaColori, 10)))}ms ease`;
  if (Array.isArray(l.ritaglio) && l.ritaglio.length >= 3) {
    const pt = l.ritaglio.filter((p) => Array.isArray(p) && p.length === 2 && MISURA_C.test(String(p[0])) && MISURA_C.test(String(p[1]))).map((p) => p[0] + ' ' + p[1]);
    if (pt.length >= 3) st.clipPath = 'polygon(' + pt.join(',') + ')';
  }
  if (l.clicPassanti) st.pointerEvents = 'none';
  if (l.ritaglia) st.overflow = 'hidden';
  if (l.fontWdth !== undefined && l.fontWdth !== '' && !Number.isNaN(Number(l.fontWdth))) st.fontVariationSettings = `"wdth" ${Number(l.fontWdth)}`;
  if (Number(l.bordiSfumati)) {
    const m = `radial-gradient(closest-side,#000 ${Math.max(0, Math.min(99, parseInt(l.bordiSfumati, 10)))}%,transparent 100%)`;
    st.WebkitMaskImage = m;
    st.maskImage = m;
  }
  return st;
}

/* ── Il testo di un livello (1.4.1431) ───────────────────────────────────
   Il testo può avere parole in grassetto, in corsivo, a capo, colorate, più piccole o più tenui: lo scrivono i pulsanti
   del campo Testo (FieldTestoLivello), non più l'HTML a mano. Qui si tiene solo quello che quei pulsanti fanno (e i link),
   per mostrarlo sulla tela dell'editor e salvarlo pulito. Il PHP lo ripassa da wp_kses_post. */
const TAG_TESTO = new Set(['B', 'STRONG', 'I', 'EM', 'U', 'S', 'BR', 'SPAN', 'A', 'SMALL', 'SUP', 'SUB', 'MARK']);
const STILI_TESTO = /^(color|opacity|font-size|font-weight|font-style|letter-spacing|text-decoration|background-color)$/;
export function stileTestoPulito(stile) {
  return String(stile || '').split(';').map((d) => {
    const i = d.indexOf(':');
    if (i < 0) return '';
    const p = d.slice(0, i).trim().toLowerCase();
    const v = d.slice(i + 1).trim();
    if (!STILI_TESTO.test(p) || !v || /url\(|expression|javascript:|[<>{}]/i.test(v)) return '';
    return p + ':' + v;
  }).filter(Boolean).join(';');
}
export function testoSicuro(html) {
  const s = String(html ?? '');
  if (!/[<&]/.test(s) || typeof DOMParser === 'undefined') return s.replace(/</g, '&lt;');
  const doc = new DOMParser().parseFromString('<div>' + s + '</div>', 'text/html');
  const radice = doc.body.firstChild;
  const pulisci = (el) => {
    for (const n of [...el.childNodes]) {
      if (n.nodeType === 3) continue;
      if (n.nodeType !== 1) { n.remove(); continue; }
      pulisci(n);
      if (!TAG_TESTO.has(n.tagName)) {
        if (/^(SCRIPT|STYLE|IFRAME|OBJECT|EMBED|TEMPLATE)$/.test(n.tagName)) { n.remove(); continue; }
        // un blocco (div, p) diventa un a capo: è quello che il browser fa quando si preme Invio in un campo modificabile
        const blocco = /^(DIV|P)$/.test(n.tagName) && n.previousSibling;
        if (blocco) n.before(doc.createElement('br'));
        n.replaceWith(...n.childNodes);
        continue;
      }
      for (const a of [...n.attributes]) {
        const nome = a.name.toLowerCase();
        if (nome === 'style') {
          const st = stileTestoPulito(a.value);
          if (st) n.setAttribute('style', st); else n.removeAttribute('style');
        } else if (n.tagName === 'A' && nome === 'href') {
          if (!/^(https?:|#|\/|mailto:|tel:)/i.test(a.value.trim())) n.removeAttribute('href');
        } else if (!(n.tagName === 'A' && (nome === 'target' || nome === 'rel'))) {
          n.removeAttribute(a.name);
        }
      }
      // uno span senza stile non serve
      if (n.tagName === 'SPAN' && !n.attributes.length) n.replaceWith(...n.childNodes);
    }
  };
  pulisci(radice);
  return radice.innerHTML;
}
