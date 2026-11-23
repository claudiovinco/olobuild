/**
 * Lo Slider Pro leggibile (1.4.1352). Utente, 23 nov 2026: «il banner hero slider pro, che spesso aprendolo non si capisce
 * nulla… migliorare la gestione degli slider pro in modo da poter gestire bene le animazioni… attualmente sono
 * ingestibili e sembrano trucchi di magia».
 *
 * Qui le regole in comune fra la tela dell'editor, la barra dei livelli e il pannello delle proprietà:
 *  - il movimento con lo scorrimento (scrollMove) calcolato COME sul sito (gemello di olo-proslider.js, «Livelli che si
 *    muovono con lo scorrimento»), per vederlo sulla tela trascinando il cursore «Scorrimento»;
 *  - gli stati (stato.vale «a,b» o «!a,b»), gemelli di stato_vale() del PHP;
 *  - il nome di un livello e le sue animazioni, per la barra dei livelli.
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

/* ── Nome e animazioni di un livello (barra dei livelli) ─────────────────── */

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
  if ((l.type === 'text' || l.type === 'button') && String(l.content || '').trim()) return String(l.content).replace(/\s+/g, ' ').trim();
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
