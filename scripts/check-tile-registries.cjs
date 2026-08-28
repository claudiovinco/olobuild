#!/usr/bin/env node
/**
 * Guardiano dei tre registri tile — Olobuild.
 *
 * Le tile vivono in TRE registri indipendenti che nessuno tiene allineati:
 *   1. config inspector  → src/config/elements/<type>.js        (auto-discovery Vite)
 *   2. componente canvas → src/components/Tiles/TileBase.vue    (mappa manuale tileComponents)
 *   3. renderer frontend → includes/tiles/class-*-tile.php      (protected $type)
 *
 * Questo script confronta i tre insiemi e stampa ogni disallineamento:
 *   - config senza componente Vue  → nessuna anteprima nel canvas
 *   - config senza classe PHP      → configurabile ma NON renderizzabile in frontend
 *   - Vue senza config             → componente morto nel bundle (nessun inspector)
 *   - PHP senza config             → renderer irraggiungibile dal builder
 *
 * Uso:  node scripts/check-tile-registries.cjs [--strict]
 *   --strict  esce con codice 1 se ci sono config senza PHP o senza Vue
 *             (le classi di anomalie che rompono l'esperienza utente).
 * Pensato per il flusso pre-release: npm run check:registries
 */
const fs = require('fs');
const path = require('path');

const ROOT = path.resolve(__dirname, '..');
const strict = process.argv.includes('--strict');

/* ── 1. config ── */
const configDir = path.join(ROOT, 'src', 'config', 'elements');
const configs = new Map(); // type → { hidden }
for (const f of fs.readdirSync(configDir)) {
  if (!f.endsWith('.js') || f.startsWith('_')) continue;
  const src = fs.readFileSync(path.join(configDir, f), 'utf8');
  // type del TILE = proprietà top-level (indent 2) dell'export default — il match
  // libero prenderebbe anche i type dei valori nei defaults (media_bg: {type:'none'}…).
  const m = /^  type:\s*['"]([^'"]+)['"]/m.exec(src);
  if (!m) continue;
  configs.set(m[1], { hidden: /^\s{2}hidden:\s*true/m.test(src), file: f });
}

/* ── 2. componenti Vue (mappa tileComponents in TileBase.vue) ── */
const tileBase = fs.readFileSync(path.join(ROOT, 'src', 'components', 'Tiles', 'TileBase.vue'), 'utf8');
const mapMatch = /const\s+tileComponents\s*=\s*\{([\s\S]*?)\n\};/.exec(tileBase);
const vueTypes = new Set();
if (mapMatch) {
  for (const line of mapMatch[1].split('\n')) {
    const m = /^\s*(?:'([^']+)'|"([^"]+)"|([A-Za-z0-9_-]+))\s*:/.exec(line);
    if (m) vueTypes.add(m[1] || m[2] || m[3]);
  }
}

/* ── 3. classi PHP ── */
const phpDir = path.join(ROOT, 'includes', 'tiles');
const phpTypes = new Set();
for (const f of fs.readdirSync(phpDir)) {
  if (!f.endsWith('.php')) continue;
  const src = fs.readFileSync(path.join(phpDir, f), 'utf8');
  const m = /protected\s+\$type\s*=\s*['"]([^'"]+)['"]/.exec(src);
  if (m) phpTypes.add(m[1]);
}

/* ── confronto ── */
// Tipi risolti fuori dal plugin (verticali esterni via oloExternalData) o
// puramente client-side: non pretendono la classe PHP interna.
const NO_PHP_EXPECTED_PREFIXES = ['olo_room_'];
const NO_PHP_EXPECTED = new Set(['offcanvas']);

const sort = (a) => [...a].sort();
const report = { configNoVue: [], configNoPhp: [], vueNoConfig: [], phpNoConfig: [] };

for (const [type, meta] of configs) {
  if (!vueTypes.has(type)) report.configNoVue.push(type + (meta.hidden ? ' (hidden)' : ''));
  const phpExpected = !NO_PHP_EXPECTED.has(type)
    && !NO_PHP_EXPECTED_PREFIXES.some((p) => type.startsWith(p));
  if (phpExpected && !phpTypes.has(type)) report.configNoPhp.push(type + (meta.hidden ? ' (hidden)' : ''));
}
for (const type of vueTypes) {
  if (!configs.has(type)) report.vueNoConfig.push(type);
}
for (const type of phpTypes) {
  if (!configs.has(type)) report.phpNoConfig.push(type);
}

const line = (label, arr) => {
  console.log(`\n${label} — ${arr.length}`);
  if (arr.length) console.log('  ' + sort(arr).join('\n  '));
};

console.log(`Registri tile — config: ${configs.size} · Vue (tileComponents): ${vueTypes.size} · PHP: ${phpTypes.size}`);
line('Config SENZA componente Vue (nessuna anteprima nel canvas)', report.configNoVue);
line('Config SENZA classe PHP (NON renderizzabile in frontend)', report.configNoPhp);
line('Componenti Vue SENZA config (peso morto nel bundle)', report.vueNoConfig);
line('Classi PHP SENZA config (irraggiungibili dal builder)', report.phpNoConfig);

const critical = report.configNoVue.length + report.configNoPhp.length;
console.log(`\nTotale anomalie: ${critical + report.vueNoConfig.length + report.phpNoConfig.length} (critiche: ${critical})`);
process.exit(strict && critical > 0 ? 1 : 0);
