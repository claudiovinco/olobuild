// Generatore template Olobuild per le 6 pagine prodotto + pricing.
// Strategia: splittiamo ogni handoff source in <header>/<section class="olo-..."> top-level
// → ogni blocco diventa una section Olobuild con UN tile html dentro (fedele al sorgente).
// Conversione in tile-native: TODO in round successivi (chiama buildHero/Pricing/FAQ).

const { randomUUID } = require('crypto');
const fs = require('fs');
const path = require('path');
const { execSync } = require('child_process');

const uid = () => randomUUID();

// ── Helpers struttura (stesso pattern di build-home.cjs) ───────────────────
const section = (children, settings = {}) => ({
  id: uid(), type: 'section',
  settings: { style: 'default', width: 'default', padding: 'default', flex_direction: 'row', flex_justify: 'flex-start', flex_align: 'stretch', flex_wrap: 'nowrap', flex_gap: '0', layout_mode: 'flex', sticky_effect: 'none', scroll_snap: false, ...settings },
  style: [], advanced: [], children,
});

const row = (children, settings = {}) => ({
  id: uid(), type: 'row',
  settings: { layout: '100', gap: 0, column_gap: 'default', vertical_align: 'top', stack_mobile: true, flex_direction: 'row', flex_justify: 'flex-start', flex_align: 'stretch', flex_wrap: 'nowrap', flex_gap: '0', ...settings },
  style: [], advanced: [], children,
});

const column = (children, settings = {}) => ({
  id: uid(), type: 'column',
  settings: { width_default: '', width_medium: '1-1', ...settings },
  style: [], advanced: [], children,
});

const tile = (type, settings, style = [], advanced = []) => ({
  id: uid(), type, settings, style, advanced,
});

const sectionHtml = (htmlContent, sectionSettings = {}) =>
  section([row([column([tile('html', { html_content: htmlContent })])])], sectionSettings);

// ── Splitter HTML in top-level <header>/<section> ──────────────────────────
//
// Parsing manuale a livello di tag-balance: scorre il testo, accumula <header> o
// <section> top-level (nesting depth 0). Niente jsdom — l'handoff è ben formato.
function splitTopLevelSections(html) {
  const blocks = [];
  let i = 0;
  const tagsOfInterest = ['header', 'section'];
  // Regex per match qualunque <tag ...> di apertura
  const openRe = /<(header|section)\b[^>]*>/gi;
  let mOpen;
  while ((mOpen = openRe.exec(html)) !== null) {
    const tag = mOpen[1].toLowerCase();
    const startTagStart = mOpen.index;
    // Da qui devo trovare il matching </tag> bilanciato.
    let depth = 1;
    let pos = openRe.lastIndex;
    const nestedRe = new RegExp(`<\\/?${tag}\\b[^>]*>`, 'gi');
    nestedRe.lastIndex = pos;
    let mNest;
    let closePos = -1;
    while ((mNest = nestedRe.exec(html)) !== null) {
      const fullTag = mNest[0];
      if (fullTag.startsWith('</')) {
        depth--;
        if (depth === 0) {
          closePos = nestedRe.lastIndex;
          break;
        }
      } else {
        depth++;
      }
    }
    if (closePos > 0) {
      const fullBlock = html.substring(startTagStart, closePos).trim();
      blocks.push({ tag, html: fullBlock });
      openRe.lastIndex = closePos;
    } else {
      // Mal-formato: skip oltre questo open tag.
      break;
    }
  }
  return blocks;
}

// ── Build template content per UNA pagina ──────────────────────────────────
function buildPageTemplate(handoffPath) {
  const raw = fs.readFileSync(handoffPath, 'utf8');
  const blocks = splitTopLevelSections(raw);
  // Se nessun blocco trovato (handoff diverso/inline), fallback singolo tile html.
  if (blocks.length === 0) {
    return [ sectionHtml(raw.trim()) ];
  }
  return blocks.map(b => sectionHtml(b.html));
}

// ── Mappa post → template ──────────────────────────────────────────────────
const PAGES = [
  { post_id: 2120, template_id: 21, file: 'handoff-2120.html', name: 'OLObuild' },
  { post_id: 2113, template_id: 14, file: 'handoff-2113.html', name: 'OLObooking' },
  { post_id: 2121, template_id: 22, file: 'handoff-2121.html', name: 'OLOlang' },
  { post_id: 2122, template_id: 23, file: 'handoff-2122.html', name: 'OLOtour' },
  { post_id: 2123, template_id: 24, file: 'handoff-2123.html', name: 'OLOtutor' },
  { post_id: 2133, template_id: 34, file: 'handoff-2133.html', name: 'Pricing' },
];

const outDir = path.join(__dirname, 'pages-out');
fs.mkdirSync(outDir, { recursive: true });

for (const p of PAGES) {
  const handoffPath = path.join(__dirname, 'pages', p.file);
  if (!fs.existsSync(handoffPath)) {
    console.log(`SKIP ${p.name} (${p.file}): not found`);
    continue;
  }
  const tpl = buildPageTemplate(handoffPath);
  const outPath = path.join(outDir, `template-${p.template_id}.json`);
  fs.writeFileSync(outPath, JSON.stringify(tpl), 'utf8');
  const sizeKB = (fs.statSync(outPath).size / 1024).toFixed(1);
  console.log(`✓ ${p.name.padEnd(12)} → template ${p.template_id}: ${tpl.length} sezioni, ${sizeKB}KB → ${outPath}`);
}
