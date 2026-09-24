#!/usr/bin/env node
/**
 * golden-shots.mjs — banco L2 del sistema compatto (passo 0.1): le pagine fotografate a
 * 5 larghezze, con più temi, prima e dopo un passo; poi il confronto pixel per pixel.
 *
 * FOTOGRAFARE
 *   node scripts/golden/golden-shots.mjs --out <fuori dal repo>/shots/<fase> \
 *        --urls <file> | --site <origine> --ids 7,8,12|tutti --mappa <snap>/_urls.tsv
 *        [--widths 1440,1199,959,639,390] [--theme attivo,olo-tema-prova,hello-olobuild]
 *        [--state <storageState.json>] [--altezza 900] [--no-clock]
 *
 *   --urls   un file di indirizzi: il _urls.tsv di render-golden.php (chiave, post_id, url),
 *            oppure una riga per pagina, «url» o «chiave<TAB>url» («#» = commento)
 *   --site + --ids  i template per id: l'indirizzo si prende dalla --mappa (_urls.tsv) e
 *            si rimonta sull'origine data (utile per puntare l'IP di rete locale)
 *   --theme  «attivo» = il tema del sito; un altro nome = ?wp_theme_preview=<slug> (core
 *            WordPress ≥ 6.3, serve un utente con switch_themes: senza --state lo script si
 *            ferma). Ogni scatto di un altro tema controlla che la pagina carichi un foglio di
 *            stile da /themes/<slug>/: se no (tema non installato, sessione scaduta) niente
 *            scatto e un errore nel manifest. Lo stesso per HTTP ≥ 400 e per wp-login.php.
 *   --state  (o env OLO_GOLDEN_STATE) storage state di Playwright con l'accesso admin,
 *            salvato FUORI dal repo dall'utente: lo script non scrive né chiede credenziali
 *   Ogni indirizzo riceve ?olo_golden=1 (salta la cache a pagina intera). Env utili:
 *   OLO_GOLDEN_RESOLVE="MAP mosaic.clod.eu <ip>" (→ --host-resolver-rules, scavalca il CDN),
 *   OLO_CHROME=<chrome.exe> o OLO_CHROME_CHANNEL=chrome (browser di sistema),
 *   OLO_GOLDEN_INSECURE=1 (certificati non validi, es. IP locale).
 *   Uscita: <out>/<tema>/<larghezza>/<chiave>.png + <out>/_manifest.json
 *
 * ACCESSO ADMIN (una volta, lo fa l'utente)
 *   node scripts/golden/golden-shots.mjs --salva-accesso <fuori dal repo>/mosaic-state.json --site <origine>
 *   Apre un browser visibile su /wp-login.php; si accede a mano, poi Invio nel terminale.
 *
 * CONFRONTARE
 *   node scripts/golden/golden-shots.mjs --compare <shots A> <shots B> [--tolleranza 0]
 *   Uscita 0 se ogni PNG di A ha il gemello identico in B, 1 se no (elenco con i pixel diversi)
 *   o se A non ha scatti (il confronto non vale).
 *   Il confronto si fa DENTRO il browser (canvas): nessuna dipendenza come pixelmatch.
 *
 * PLAYWRIGHT non è una dipendenza del repo (package.json non si tocca): lo script lo cerca
 * in env OLO_PLAYWRIGHT (cartella del pacchetto playwright o playwright-core, o una cartella
 * che lo contiene in node_modules), poi come modulo 'playwright' / 'playwright-core'. Se non
 * lo trova, dice come averlo senza toccare il repo.
 */
import fs from 'fs';
import path from 'path';
import crypto from 'crypto';
import { createRequire } from 'module';

const LARGHEZZE = [1440, 1199, 959, 639, 390];
const argv = process.argv.slice(2);
const opz = (nome, predefinito) => { const i = argv.indexOf(nome); return i >= 0 && argv[i + 1] !== undefined ? argv[i + 1] : predefinito; };
const bandiera = (nome) => argv.includes(nome);

function esci(msg, codice = 2) { console.error(msg); process.exit(codice); }

async function caricaPlaywright() {
  const prove = [];
  const dir = process.env.OLO_PLAYWRIGHT;
  if (dir) {
    const req = createRequire(path.join(path.resolve(dir), 'noop.js'));
    prove.push(() => req(path.resolve(dir)));
    prove.push(() => req('playwright'));
    prove.push(() => req('playwright-core'));
  }
  prove.push(async () => import('playwright'));
  prove.push(async () => import('playwright-core'));
  for (const prova of prove) {
    try {
      const mod = await prova();
      const pw = mod && (mod.chromium ? mod : mod.default);
      if (pw && pw.chromium) return pw;
    } catch { /* prossimo tentativo */ }
  }
  esci([
    'Playwright non trovato. Non è una dipendenza del repo e lo script non lo installa.',
    'Per averlo senza toccare il repo, in una cartella FUORI dal repo:',
    '  "C:/Program Files/nodejs/npm.cmd" install --prefix <cartella> playwright-core',
    'e poi:  set OLO_PLAYWRIGHT=<cartella>   (PowerShell: $env:OLO_PLAYWRIGHT="<cartella>")',
    'Va bene anche un pacchetto playwright-core già presente sul PC (per esempio quello del',
    'driver di Playwright per Python: …/site-packages/playwright/driver/package).',
    'Il browser: quello di Playwright, se scaricato, oppure Chrome di sistema con',
    'OLO_CHROME_CHANNEL=chrome o OLO_CHROME=<percorso di chrome.exe>.',
  ].join('\n'));
}

async function apriBrowser(pw) {
  const args = [];
  if (process.env.OLO_GOLDEN_RESOLVE) args.push('--host-resolver-rules=' + process.env.OLO_GOLDEN_RESOLVE);
  const base = { headless: true, args };
  if (process.env.OLO_CHROME) return pw.chromium.launch({ ...base, executablePath: process.env.OLO_CHROME });
  if (process.env.OLO_CHROME_CHANNEL) return pw.chromium.launch({ ...base, channel: process.env.OLO_CHROME_CHANNEL });
  try {
    return await pw.chromium.launch(base);
  } catch (e) {
    try {
      return await pw.chromium.launch({ ...base, channel: 'chrome' });
    } catch {
      esci('Nessun browser: ' + String(e && e.message ? e.message : e).split('\n')[0] + '\nImposta OLO_CHROME o OLO_CHROME_CHANNEL=chrome.');
    }
  }
}

const pulisci = (s) => String(s).replace(/[^A-Za-z0-9_.-]/g, '_');

/** Righe «chiave → url» da un file di indirizzi (anche il _urls.tsv di render-golden). */
function leggiIndirizzi(file) {
  if (!fs.existsSync(file)) esci('File di indirizzi non trovato: ' + file);
  const righe = fs.readFileSync(file, 'utf8').split(/\r?\n/).map((r) => r.trim()).filter((r) => r && !r.startsWith('#'));
  const out = [];
  let intestazione = null;
  for (const riga of righe) {
    const campi = riga.split('\t');
    if (!intestazione && campi.includes('url') && campi.includes('chiave')) { intestazione = campi; continue; }
    if (intestazione) {
      const r = Object.fromEntries(intestazione.map((c, i) => [c, campi[i] || '']));
      if (r.url) out.push({ chiave: r.chiave, url: r.url });
      continue;
    }
    const parti = riga.split(/\s+/);
    const url = parti[parti.length - 1];
    const chiave = parti.length > 1 ? parti[0] : pulisci(url.replace(/^[a-z]+:\/\/[^/]+/i, '').replace(/^\/+|\/+$/g, '') || 'home');
    out.push({ chiave, url });
  }
  return out;
}

function conParametri(url, tema) {
  const u = new URL(url);
  u.searchParams.set('olo_golden', '1');
  if (tema && tema !== 'attivo') u.searchParams.set('wp_theme_preview', tema);
  return u.toString();
}

// Idrata le tile lazy (<template class="olo-lazy-content">, immagini loading=lazy):
// si scende a passi di una schermata fino in fondo, poi si torna in cima.
async function scorri(page) {
  await page.evaluate(async () => {
    const pausa = (ms) => new Promise((r) => setTimeout(r, ms));
    let y = 0;
    for (let giri = 0; giri < 200; giri++) {
      const alto = document.documentElement.scrollHeight;
      if (y >= alto) break;
      window.scrollTo(0, y);
      await pausa(120);
      y += Math.max(200, Math.floor(window.innerHeight * 0.8));
    }
    window.scrollTo(0, document.documentElement.scrollHeight);
    await pausa(300);
    window.scrollTo(0, 0);
    await pausa(300);
  });
}

async function fotografa(pw) {
  const out = opz('--out');
  if (!out) esci('Manca --out <cartella fuori dal repo>/shots/<fase>');
  const larghezze = String(opz('--widths', LARGHEZZE.join(','))).split(',').map((x) => parseInt(x, 10)).filter((x) => x > 0);
  const temi = String(opz('--theme', 'attivo')).split(',').map((x) => x.trim()).filter(Boolean);
  const altezza = parseInt(opz('--altezza', '900'), 10) || 900;
  const state = opz('--state', process.env.OLO_GOLDEN_STATE || '');
  if (state && !fs.existsSync(state)) esci('Storage state non trovato: ' + state);
  // Senza un utente con switch_themes WordPress ignora wp_theme_preview in silenzio e rende il
  // tema attivo: lo scatto «di un altro tema» sarebbe quello del tema attivo.
  const altri = temi.filter((t) => t !== 'attivo');
  if (altri.length && !state) esci(`--theme ${altri.join(',')} vuole --state (o OLO_GOLDEN_STATE): wp_theme_preview vale solo per un utente con switch_themes, senza WordPress rende il tema attivo.`);

  let pagine;
  if (opz('--urls')) {
    pagine = leggiIndirizzi(opz('--urls'));
  } else if (opz('--site') && opz('--ids')) {
    const mappa = opz('--mappa');
    if (!mappa) esci('--ids vuole --mappa <snapshot>/_urls.tsv (render-golden.php): il permalink del post collegato al template.');
    const noti = new Map(leggiIndirizzi(mappa).map((p) => [p.chiave, p.url]));
    const ids = opz('--ids') === 'tutti' ? [...noti.keys()] : opz('--ids').split(',').map((x) => 'db-' + x.trim().replace(/^db-/, ''));
    pagine = [];
    for (const chiave of ids) {
      if (!noti.has(chiave)) { console.warn('senza URL (solo L1): ' + chiave); continue; }
      const u = new URL(noti.get(chiave));
      pagine.push({ chiave, url: new URL(u.pathname + u.search, opz('--site')).toString() });
    }
  } else {
    esci('Servono --urls <file> oppure --site <origine> --ids <id,…|tutti> --mappa <_urls.tsv>.');
  }
  if (!pagine.length) esci('Nessuna pagina da fotografare.');

  fs.mkdirSync(out, { recursive: true });
  const browser = await apriBrowser(pw);
  const manifest = {
    generato: new Date().toISOString(),
    browser: browser.version(),
    larghezze, temi, altezza,
    storage_state: state ? path.basename(state) : null,
    pagine, errori: [],
  };
  const inizio = Date.now();
  try {
    for (const tema of temi) {
      for (const w of larghezze) {
        const ctx = await browser.newContext({
          viewport: { width: w, height: altezza },
          deviceScaleFactor: 1,
          reducedMotion: 'reduce',
          ignoreHTTPSErrors: !!process.env.OLO_GOLDEN_INSECURE,
          ...(state ? { storageState: state } : {}),
        });
        // L'ora ferma: countdown, «nuovo», saluti per fascia oraria non cambiano fra due giri.
        if (!bandiera('--no-clock') && ctx.clock && ctx.clock.setFixedTime) await ctx.clock.setFixedTime(new Date('2026-01-01T10:00:00Z'));
        const dir = path.join(out, pulisci(tema), String(w));
        fs.mkdirSync(dir, { recursive: true });
        for (const p of pagine) {
          const page = await ctx.newPage();
          try {
            const risposta = await page.goto(conParametri(p.url, tema), { waitUntil: 'load', timeout: 60000 });
            // Una pagina d'errore o di login non è uno scatto valido.
            if (!risposta) throw new Error('nessuna risposta HTTP');
            if (risposta.status() >= 400) throw new Error(`HTTP ${risposta.status()}`);
            if (/\/wp-login\.php/.test(page.url())) throw new Error('rimandato a wp-login.php (sessione scaduta o pagina privata)');
            // Il template atteso deve esserci davvero: il coming soon e la manutenzione rispondono
            // 200 (o 503) con un ALTRO template (class-maintenance-mode.php) e senza rimandi.
            const idDb = /^db-(\d+)$/.exec(p.chiave);
            if (idDb && !(await page.locator('.olo-template-' + idDb[1]).count())) {
              throw new Error(`nella pagina non c'è .olo-template-${idDb[1]}: coming soon/manutenzione o sessione non valida (serve --state di un utente che li scavalca)`);
            }
            // Il tema chiesto deve essere quello reso: WordPress torna in silenzio al tema attivo se
            // il tema non è installato o l'utente non ha switch_themes.
            if (tema !== 'attivo') {
              const applicato = await page.evaluate((slug) => {
                const cerca = '/themes/' + slug + '/';
                return [...document.querySelectorAll('link[href]')].some((l) => /\bstylesheet\b/i.test(l.rel) && l.href.includes(cerca))
                  || performance.getEntriesByType('resource').some((r) => r.name.includes(cerca) && /\.css(\?|$)/.test(r.name));
              }, tema);
              if (!applicato) throw new Error(`tema «${tema}» non applicato: nessun foglio di stile da /themes/${tema}/ (non installato, o utente senza switch_themes)`);
            }
            await page.addStyleTag({ content: '#wpadminbar{display:none!important}html{margin-top:0!important}*{caret-color:transparent!important}' });
            await scorri(page);
            await page.waitForLoadState('networkidle', { timeout: 15000 }).catch(() => {});
            await page.evaluate(() => document.fonts && document.fonts.ready);
            await page.screenshot({
              path: path.join(dir, pulisci(p.chiave) + '.png'),
              fullPage: true,
              animations: 'disabled',
              caret: 'hide',
              mask: [page.locator('video, iframe, canvas, .leaflet-container')],
            });
          } catch (e) {
            manifest.errori.push({ tema, larghezza: w, chiave: p.chiave, errore: String(e && e.message ? e.message : e).split('\n')[0] });
          } finally {
            await page.close();
          }
        }
        await ctx.close();
        console.log(`${tema} · ${w}px · ${pagine.length} pagine · ${Math.round((Date.now() - inizio) / 1000)}s`);
      }
    }
  } finally {
    await browser.close();
  }
  fs.writeFileSync(path.join(out, '_manifest.json'), JSON.stringify(manifest, null, 1) + '\n');
  const chiesti = temi.length * larghezze.length * pagine.length;
  console.log(`Fatto: ${chiesti - manifest.errori.length} scatti su ${chiesti} in ${out}` + (manifest.errori.length ? ` · ${manifest.errori.length} errori, senza scatto (vedi _manifest.json)` : ''));
  process.exit(manifest.errori.length ? 1 : 0);
}

function png(dir, acc = [], base = dir) {
  for (const e of fs.readdirSync(dir, { withFileTypes: true })) {
    const p = path.join(dir, e.name);
    if (e.isDirectory()) png(p, acc, base);
    else if (e.name.endsWith('.png')) acc.push(path.relative(base, p).split(path.sep).join('/'));
  }
  return acc.sort();
}

async function confronta(pw, A, B) {
  if (!A || !B || !fs.existsSync(A) || !fs.existsSync(B)) esci('Uso: --compare <shots A> <shots B>');
  const tolleranza = parseInt(opz('--tolleranza', '0'), 10) || 0;
  const inA = png(A);
  // Come confronta.sh: senza scatti in A non c'è niente di confrontato, e «IDENTICI» mentirebbe.
  if (!inA.length) {
    console.log(`0 scatti in A (${A}): il confronto non vale`);
    console.log('DIVERSI');
    process.exit(1);
  }
  const inB = new Set(png(B));
  const md5 = (f) => crypto.createHash('md5').update(fs.readFileSync(f)).digest('hex');
  const diversi = [];
  const mancanti = inA.filter((f) => !inB.has(f));
  const soloB = [...inB].filter((f) => !inA.includes(f));
  let browser = null;
  let page = null;
  for (const f of inA.filter((x) => inB.has(x))) {
    const fa = path.join(A, f);
    const fb = path.join(B, f);
    if (md5(fa) === md5(fb)) continue;
    if (!browser) { browser = await apriBrowser(pw); page = await browser.newPage(); }
    const esito = await page.evaluate(async ({ a, b, tol }) => {
      const carica = (src) => new Promise((ok, ko) => { const im = new Image(); im.onload = () => ok(im); im.onerror = ko; im.src = src; });
      const [ia, ib] = await Promise.all([carica(a), carica(b)]);
      if (ia.width !== ib.width || ia.height !== ib.height) return { pixel: -1, dim: `${ia.width}×${ia.height} → ${ib.width}×${ib.height}` };
      const dati = (im) => { const c = document.createElement('canvas'); c.width = im.width; c.height = im.height; const x = c.getContext('2d'); x.drawImage(im, 0, 0); return x.getImageData(0, 0, im.width, im.height).data; };
      const da = dati(ia), db = dati(ib);
      let n = 0;
      for (let i = 0; i < da.length; i += 4) {
        if (Math.abs(da[i] - db[i]) > tol || Math.abs(da[i + 1] - db[i + 1]) > tol || Math.abs(da[i + 2] - db[i + 2]) > tol || Math.abs(da[i + 3] - db[i + 3]) > tol) n++;
      }
      return { pixel: n, dim: `${ia.width}×${ia.height}` };
    }, {
      a: 'data:image/png;base64,' + fs.readFileSync(fa).toString('base64'),
      b: 'data:image/png;base64,' + fs.readFileSync(fb).toString('base64'),
      tol: tolleranza,
    });
    if (esito.pixel !== 0) diversi.push({ f, ...esito });
  }
  if (browser) await browser.close();
  for (const d of diversi) console.log(d.pixel < 0 ? `dimensioni   ${d.f}  (${d.dim})` : `diverso      ${d.f}  ${d.pixel} pixel (${d.dim})`);
  for (const f of mancanti) console.log(`manca in B   ${f}`);
  for (const f of soloB) console.log(`solo in B    ${f}`);
  console.log(`\n${inA.length} scatti in A · diversi ${diversi.length} · mancanti in B ${mancanti.length} · solo in B ${soloB.length}`);
  const esito = diversi.length + mancanti.length + soloB.length ? 1 : 0;
  console.log(esito ? 'DIVERSI' : 'IDENTICI');
  process.exit(esito);
}

// L'accesso admin lo fa l'UTENTE, a mano, in un browser visibile: lo script salva solo lo
// storage state (cookie di sessione) nel file indicato, che va tenuto FUORI dal repo.
async function salvaAccesso(pw, file, sito) {
  if (!file || !sito) esci('Uso: --salva-accesso <file fuori dal repo> --site <origine>');
  const browser = await pw.chromium.launch({
    headless: false,
    ...(process.env.OLO_CHROME ? { executablePath: process.env.OLO_CHROME } : process.env.OLO_CHROME_CHANNEL ? { channel: process.env.OLO_CHROME_CHANNEL } : {}),
    args: process.env.OLO_GOLDEN_RESOLVE ? ['--host-resolver-rules=' + process.env.OLO_GOLDEN_RESOLVE] : [],
  });
  const ctx = await browser.newContext({ ignoreHTTPSErrors: !!process.env.OLO_GOLDEN_INSECURE });
  const page = await ctx.newPage();
  await page.goto(new URL('/wp-login.php', sito).toString());
  console.log('Accedi nel browser che si è aperto, poi torna qui e premi Invio.');
  await new Promise((ok) => process.stdin.once('data', ok));
  fs.mkdirSync(path.dirname(path.resolve(file)), { recursive: true });
  await ctx.storageState({ path: file });
  await browser.close();
  console.log('Storage state salvato in ' + file + ' (contiene la sessione: non va nel repo).');
  process.exit(0);
}

const pw = await caricaPlaywright();
if (bandiera('--salva-accesso')) {
  await salvaAccesso(pw, opz('--salva-accesso'), opz('--site'));
} else if (bandiera('--compare')) {
  const i = argv.indexOf('--compare');
  await confronta(pw, argv[i + 1], argv[i + 2]);
} else {
  await fotografa(pw);
}
