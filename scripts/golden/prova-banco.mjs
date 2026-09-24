#!/usr/bin/env node
/**
 * prova-banco.mjs — prova in locale la parte del banco che non ha bisogno di WordPress:
 *   1. le regole di normalizza.json (le stesse che render-golden.php applica in PHP);
 *   2. confronta.sh su snapshot finti: identici; un template e una riga di Style System
 *      cambiati; tutto fatale in A e in B; fatale noto (ACCETTA); stesso errore o errore
 *      diverso; _style-system.css mancante; pagine di tema «vuote» (la riga finta nella cache
 *      non letta) e template del DB vuoti; B parziale (_solo.txt) e B con chiavi mancanti.
 *      Serve bash: env OLO_BASH, altrimenti 'bash'.
 *
 *   node scripts/golden/prova-banco.mjs
 * Uscita 0 se tutto torna. Scrive solo nella cartella temporanea del sistema.
 */
import fs from 'fs';
import os from 'os';
import path from 'path';
import { spawnSync } from 'child_process';
import { fileURLToPath } from 'url';

const HERE = path.dirname(fileURLToPath(import.meta.url));
let errori = 0;
const verifica = (cond, msg) => { console.log((cond ? 'ok   ' : 'NO   ') + msg); if (!cond) errori++; };

// ── 1. normalizzazione ───────────────────────────────────────────────────────
const { regole } = JSON.parse(fs.readFileSync(path.join(HERE, 'normalizza.json'), 'utf8'));
const VERSIONE = '1.4.480';
const quota = (s) => s.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
const normalizza = (html) => regole.reduce((h, r) => h.replace(new RegExp(r.cerca.replace('{VERSIONE}', quota(VERSIONE)), 'g'), r.sostituisci), html);
// Sintassi comune a PCRE e JavaScript: niente lookbehind, gruppi con nome, flag in linea, ~.
for (const r of regole) {
  verifica(!/\(\?<|\(\?[imsx]|~/.test(r.cerca), `regola ${r.nome}: sintassi portabile fra PCRE e JS`);
}
const token = 'v2:1790000000:' + 'a1'.repeat(32);
const campione = [
  `<input type="hidden" name="_olo_form_token" value="${token}" />`,
  `<link rel="stylesheet" href="https://x.test/olo-7-abc.css?v=${VERSIONE}" media="all" />`,
  `<script src="https://x.test/a.js?ver=${VERSIONE}&amp;x=1"></script>`,
  `<img src="a.png?ver=${VERSIONE}1">`,
  `<p>Versione ${VERSIONE} nel testo</p>`,
].join('\n');
const n = normalizza(campione);
verifica(n.includes('value="v2:0:TOKEN"'), 'il token del modulo (time()) diventa v2:0:TOKEN');
verifica(!n.includes(token), 'nessun token originale resta nell\'HTML');
verifica(n.includes('.css?v=VERSIONE"'), '?v=<versione> → ?v=VERSIONE');
verifica(n.includes('a.js?ver=VERSIONE&amp;'), '?ver=<versione> → ?ver=VERSIONE');
verifica(n.includes(`a.png?ver=${VERSIONE}1`), 'una versione più lunga (1.4.4801) non viene toccata');
verifica(n.includes(`Versione ${VERSIONE} nel testo`), 'la versione fuori da ?ver= non viene toccata');
verifica(normalizza(n) === n, 'la normalizzazione è idempotente');
verifica(normalizza(campione.replace('1790000000', '1790000123')) === n, 'due token di secondi diversi danno lo stesso HTML');
const fratelli = (u, t, c) => `<section class="oa-ac\uFFFD-${u} oa-tile"><style>.oa-ac\uFFFD-${u} a{}</style><div id="otfaq-${u}"></div><div data-x="{&quot;username&quot;:&quot;${t}:mosaic&quot;,&quot;credential&quot;:&quot;${c}&quot;}"></div>`;
const f1 = normalizza(fratelli('1cf219', '1790290765', 'JgRAnekWgKZAQYqdyUfakkGLz3U='));
const f2 = normalizza(fratelli('d9ded1', '1790291413', 'YzPb6lVYL5mci2Ecz4sS+3w2RvU='));
verifica(f1 === f2, 'uid di olo-booking e olo-tutor e credenziali TURN di olotour: due render danno lo stesso HTML (plugin fratelli)');
verifica(normalizza(f1) === f1, 'le regole dei plugin fratelli sono idempotenti');
const crlf = '<style>\r\n.a{color:red}\r\n</style>\r\n<p>x</p>\r';
verifica(normalizza(crlf) === normalizza(crlf.replace(/\r\n?/g, '\n')), 'fine riga CRLF (PHP salvato su Windows) = LF: il browser li normalizza');
verifica(!normalizza(crlf).includes('\r'), 'nessun CR resta nell\'HTML normalizzato');
verifica(normalizza(fratelli('aaaaaa', '1790290765', 'gH8DX\\/CVvXZ8wcHElkgXE6nDiTE=')) === f1, 'credenziale TURN con lo slash scritto \\/ (JSON dentro un attributo)');
verifica(normalizza('<p class="oa-grid is-lift">oa-note-12345z</p>') === '<p class="oa-grid is-lift">oa-note-12345z</p>', 'una classe oa- senza uid esadecimale non viene toccata');

// ── 2. confronta.sh ──────────────────────────────────────────────────────────
const bash = process.env.OLO_BASH || 'bash';
const tmp = fs.mkdtempSync(path.join(os.tmpdir(), 'olo-golden-prova-'));
const col = ['chiave', 'fonte', 'id', 'tipo', 'stato', 'md5', 'md5_grezzo', 'byte', 'volatili', 'errore'];
const snapshot = (nome, righe, dati, css) => {
  const d = path.join(tmp, nome);
  fs.mkdirSync(d);
  fs.writeFileSync(path.join(d, '_summary.tsv'), [col.join('\t'), ...righe.map((r) => col.map((c) => r[c] ?? '').join('\t'))].join('\n') + '\n');
  fs.writeFileSync(path.join(d, '_dati.tsv'), ['chiave\tfonte\tid\ttipo\tstato\tupdated_at\timpronta', ...dati.map(([k, h]) => `${k}\tdb\t\tpage\tpublished\t\t${h}`)].join('\n') + '\n');
  if (css) fs.writeFileSync(path.join(d, '_style-system.css'), css.join('\n') + '\n');
  return d;
};
const base = [
  { chiave: 'db-7', fonte: 'db', stato: 'ok', md5: 'aaa' },
  { chiave: 'db-8', fonte: 'db', stato: 'ok', md5: 'bbb' },
  { chiave: 'tema-x--home', fonte: 'tema', stato: 'ok', md5: 'ccc' },
];
const css = [':root {', '  --olo-color-primary: #e1474f;', '}'];
const A = snapshot('A', base, [['db-7', 'h7'], ['db-8', 'h8']], css);
const B = snapshot('B', base, [['db-7', 'h7'], ['db-8', 'h8']], [...css, '.nuova { color: red; }']);
const C = snapshot('C', [base[0], { ...base[1], md5: 'BBB' }, { ...base[2], md5: 'CCC', volatili: 'tempo:popup' }], [['db-7', 'h7'], ['db-8', 'h8-nuovo']], [':root {', '  --olo-color-primary: #000;', '}']);
// Chiavi non rese: il pilota scrive i fatali con md5 vuoto, gli errori con l'md5 della stringa vuota.
const MD5_VUOTO = 'd41d8cd98f00b204e9800998ecf8427e';
const fatale = (r) => ({ ...r, stato: 'fatale', md5: '', errore: 'PHP Fatal error: Allowed memory size exhausted' });
const errore = (r, msg) => ({ ...r, stato: 'errore', md5: MD5_VUOTO, errore: msg });
const datiBase = [['db-7', 'h7'], ['db-8', 'h8']];
const D = snapshot('D', base.map(fatale), datiBase, css);                       // tutto fatale (i figli muoiono)
const E = snapshot('E', base.map(fatale), datiBase, css);
const F = snapshot('F', [base[0], fatale(base[1]), base[2]], datiBase, css);    // un fatale noto
const G = snapshot('G', [base[0], fatale(base[1]), base[2]], datiBase, css);
const H = snapshot('H', [base[0], errore(base[1], 'Error: x @ a.php:1'), base[2]], datiBase, css);
const I = snapshot('I', [base[0], errore(base[1], 'Error: x @ a.php:1'), base[2]], datiBase, css);
const J = snapshot('J', [base[0], errore(base[1], 'TypeError: y @ b.php:9'), base[2]], datiBase, css);
const K = snapshot('K', base, datiBase, null);                                   // senza _style-system.css
// «vuoto»: il renderer ha risposto con un commento. Per una pagina di tema vuol dire che la riga
// finta nel gruppo di cache 'olo' non è stata letta (nessun JSON dei temi è vuoto).
const COMMENTO = '<!-- Olobuilder: Template not found -->';
const vuoto = (r) => ({ ...r, stato: 'vuoto', md5: 'e1e1e1', errore: COMMENTO });
const L = snapshot('L', [base[0], base[1], vuoto(base[2])], datiBase, css);
const M = snapshot('M', [base[0], base[1], vuoto(base[2])], datiBase, css);
const N = snapshot('N', [base[0], vuoto(base[1]), base[2]], datiBase, css);    // template del DB vuoto: legittimo
const O = snapshot('O', [base[0], vuoto(base[1]), base[2]], datiBase, css);
// B parziale: reso con SOLO=db-7 (render-golden.php scrive _solo.txt); senza _solo.txt le chiavi
// mancanti restano differenze.
const P = snapshot('P', [base[0]], datiBase, css);
fs.writeFileSync(path.join(P, '_solo.txt'), 'db-7\n');
const Q = snapshot('Q', [base[0]], datiBase, css);
const R = snapshot('R', [{ ...base[0], md5: 'AAA' }], datiBase, css);
fs.writeFileSync(path.join(R, '_solo.txt'), 'db-7\n');
const lancia = (a, b, env = {}) => spawnSync(bash, [path.join(HERE, 'confronta.sh'), a, b], { encoding: 'utf8', env: { ...process.env, ...env } });
const r1 = lancia(A, B);
if (r1.error) {
  console.log(`--   confronta.sh non provato: bash non trovato (${bash}); imposta OLO_BASH`);
} else {
  verifica(r1.status === 0 && /IDENTICI/.test(r1.stdout) && /1 righe aggiunte/.test(r1.stdout), 'A vs B: md5 uguali e Style System solo con aggiunte → uscita 0');
  const r2 = lancia(A, C);
  verifica(r2.status === 1, 'A vs C: uscita 1');
  verifica(/cambiato \(dati\)\s+db-8/.test(r2.stdout), 'db-8 cambiato perché sono cambiati i dati');
  verifica(/cambiato \(\?\)\s+tema-x--home/.test(r2.stdout) || /cambiato \(resa\)\s+tema-x--home/.test(r2.stdout), 'tema-x--home cambiato');
  verifica(/tema-x--home\s+\[volatile: tempo:popup\]/.test(r2.stdout), 'il cambio di un template volatile lo dice');
  verifica(!/db-8\s+\[volatile/.test(r2.stdout), 'nessuna nota volatile dove non serve');
  verifica(/--olo-color-primary: #e1474f/.test(r2.stdout), 'la riga tolta dallo Style System è elencata (V4)');
  const r3 = lancia(A, path.join(tmp, 'non-esiste'));
  verifica(r3.status === 2, 'snapshot mancante → uscita 2');
  const r4 = lancia(D, E);
  verifica(r4.status === 1 && !/IDENTICI/.test(r4.stdout), 'tutto fatale in A e in B (md5 vuoti uguali) → uscita 1, mai IDENTICI');
  verifica(/non reso\s+db-7 è fatale in B/.test(r4.stdout) && /nessuna chiave resa/.test(r4.stdout), 'i fatali sono elencati come non resi, e il confronto dice che non vale');
  const r5 = lancia(F, G);
  verifica(r5.status === 1, 'lo stesso fatale in A e in B non è identico → uscita 1');
  const r6 = lancia(F, G, { ACCETTA: 'db-8' });
  verifica(r6.status === 0 && /attenzione\s+db-8 è fatale in B, come in A/.test(r6.stdout), 'ACCETTA=db-8 ammette il fatale noto (e lo segnala) → uscita 0');
  const r7 = lancia(A, G, { ACCETTA: 'db-8' });
  verifica(r7.status === 1, 'ACCETTA non ammette un fatale che in A era ok → uscita 1');
  const r8 = lancia(H, I);
  verifica(r8.status === 0 && /attenzione\s+db-8 è errore in B, come in A/.test(r8.stdout), 'lo stesso errore con lo stesso messaggio in A e in B → ammesso, uscita 0');
  const r9 = lancia(H, J);
  verifica(r9.status === 1 && /non reso\s+db-8 è errore in B/.test(r9.stdout), 'errore con un messaggio diverso → uscita 1');
  const r10 = lancia(A, K);
  verifica(r10.status === 2 && /NON VERIFICATO/.test(r10.stdout), '_style-system.css mancante → uscita 2 (V4 non verificato)');
  const r11 = lancia(L, M);
  verifica(r11.status === 1 && !/IDENTICI/.test(r11.stdout), 'pagina di tema «vuoto» in A e in B con lo stesso md5 → uscita 1, mai IDENTICI');
  verifica(/non reso\s+tema-x--home è vuoto in B: <!-- Olobuilder: Template not found -->/.test(r11.stdout), 'la pagina di tema vuota è elencata come non resa, col commento del renderer');
  const r12 = lancia(L, M, { ACCETTA: 'tema-x--home' });
  verifica(r12.status === 0 && /attenzione\s+tema-x--home è vuoto in B, come in A/.test(r12.stdout), 'ACCETTA ammette una pagina di tema vuota nota (e la segnala) → uscita 0');
  const r13 = lancia(N, O);
  verifica(r13.status === 0 && /IDENTICI/.test(r13.stdout) && !/non reso/.test(r13.stdout), 'un template del DB vuoto in A e in B resta legittimo → uscita 0');
  const r14 = lancia(A, P);
  verifica(r14.status === 0 && /IDENTICI/.test(r14.stdout) && /2 chiavi di A non confrontate/.test(r14.stdout) && !/^solo in A /m.test(r14.stdout), 'B parziale (_solo.txt) con le sue chiavi identiche → uscita 0, le altre «non confrontate»');
  const r15 = lancia(A, Q);
  verifica(r15.status === 1 && /solo in A\s+db-8/.test(r15.stdout), 'B con chiavi mancanti e senza _solo.txt → uscita 1 («solo in A»)');
  const r16 = lancia(A, R);
  verifica(r16.status === 1 && /cambiato \(resa\)\s+db-7/.test(r16.stdout), 'B parziale con una chiave cambiata → uscita 1');
}
fs.rmSync(tmp, { recursive: true, force: true });

console.log(errori ? `\n${errori} verifiche fallite` : '\nTutto torna.');
process.exit(errori ? 1 : 0);
