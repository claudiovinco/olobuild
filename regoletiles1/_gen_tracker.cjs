/* Genera TILE_PROGRESS.md enumerando TUTTE le src/components/Tiles/*Tile.vue.
   Categorizza per le 8 categorie del checklist (+ Woo/Booking/Nav). Uso una-tantum. */
const fs = require('fs');
const path = require('path');

const ROOT = path.resolve(__dirname, '..');
const TILES_DIR = path.join(ROOT, 'src/components/Tiles');
const ELEMENTS_DIR = path.join(ROOT, 'src/config/elements');

const files = fs.readdirSync(TILES_DIR).filter(f => /Tile\.vue$/.test(f)).sort();
const configFiles = new Set(fs.readdirSync(ELEMENTS_DIR).filter(f => /\.js$/.test(f)).map(f => f.replace(/\.js$/, '')));

// base name without trailing "Tile"
const baseOf = (f) => f.replace(/Tile\.vue$/, '');

// Categorie in ordine di valutazione. Prima regola che matcha vince.
const RULES = [
  ['WooCommerce (Pro)',            /^Woo/i],
  ['Immobiliare (Property)',       /^Property/i],
  ['Booking & Servizi (OLObooking)', /^(Booking|BookingPicker|AppointmentGrid|Service|OloRoom|RentalInventory|Restaurant|Calendar|EventList)/i],
  ['Azioni & Bottoni',             /^(Button|CtaBanner|ToggleBtn|Darkmode|Totop|Paymentbuttons|Sharebuttons|Social|Scrollprogress)/i],
  ['Feedback',                     /^(Alert)/i],
  ['Layout & Struttura',           /^(Section|Row|Column|InnerColumns|Grid|Fragment|Spacer|Divider|Shapedivider|Overlay|Hero|HeroSplit|Panel|Killgutenberg|KillNextPrev)/i],
  ['Dati & Numeri',                /^(Counter|Countercircle|Countdown|Chart|Progress|Progresstracker|Starrating|Table|Viewscounter|Pricelist|Pricing)/i],
  ['Media',                        /^(Gallery|Progallery|Carousel|Slideshow|ProSlider|Audio|Video|Videoplaylist|Soundcloud|Image|ImgCompare|Lightbox|Lottie|Instagram|Facebookpage|Twitterfeed|Viewer360|ShatteredImage|SvgAnimator|Marquee|Map|Osmmap|Pdf)/i],
  ['Interattive & Form',           /^(Form|Loginform|Newsletter|Search|LiveSearch|Accordion|IconTabs|Switcher|Revealbox|Hiddenpop|Popover|Popup|Offcanvas)/i],
  ['Navigazione & Header/Footer',  /^(Nav|NavMenu|Subnav|Menuanchor|Mobilebar|SiteLogo|Breadcrumbs|Pagination|Postnavigation|Relatedposts|LangSwitcher|MegaMenu|Sitemap|Newsticker|Queryloop|Postgrid|Postmeta|Pagetitlebar|Toc)/i],
  ['Card & Feature',               /^(IconBox|InfoCards|ProductCards|TrustStrip|Portfolio|Timeline|StepTimeline|Authorbox|FlipCard|Hotspot|Floatingpanel|Team|Testimonial|Iconlist|Icon|Linkinbio|Hostcard|Quotation|Readingtime|Tagcloud)/i],
  ['Testo',                        /^(Headline|Animatedheading|Blendtext|Textmask|Textpath|DescList|List|Code|Html|Content|TextBlock)/i],
  ['Embed & Dinamico',             /^(Shortcode|Templateembed|Wpcomments)/i],
];

const cat = (base) => {
  for (const [name, re] of RULES) if (re.test(base)) return name;
  return 'Varie / Da classificare';
};

const groups = {};
for (const f of files) {
  const base = baseOf(f);
  const c = cat(base);
  (groups[c] ||= []).push({ f, base });
}

// ordine di presentazione (Azioni + essenziali per primi, poi categorie)
const ORDER = [
  'Azioni & Bottoni', 'Feedback', 'Testo', 'Layout & Struttura',
  'Card & Feature', 'Dati & Numeri', 'Media', 'Interattive & Form',
  'Navigazione & Header/Footer', 'Embed & Dinamico',
  'Booking & Servizi (OLObooking)', 'Immobiliare (Property)',
  'WooCommerce (Pro)', 'Varie / Da classificare',
];
const cats = Object.keys(groups).sort((a, b) => {
  const ia = ORDER.indexOf(a), ib = ORDER.indexOf(b);
  return (ia < 0 ? 99 : ia) - (ib < 0 ? 99 : ib);
});

// config name guess: lowercase base, ma alcuni differiscono (es. IconBox->iconbox, FlipCard->flipcard)
const guessConfig = (base) => {
  const lc = base.toLowerCase();
  if (configFiles.has(lc)) return lc;
  // prova varianti note
  const alt = lc.replace(/-/g, '');
  if (configFiles.has(alt)) return alt;
  return null;
};

let out = '';
out += '# TILE_PROGRESS — tracker audit "Tile belle & coerenti"\n\n';
out += '> Generato da `_gen_tracker.cjs`. UNA riga per ogni `*Tile.vue` (' + files.length + ' tile).\n';
out += '> Checklist a 10 punti (vedi `regoletiles1/TILE_AUDIT_CHECKLIST.md`).\n\n';
out += '> Esclusi (infrastruttura, non tile di contenuto): `TileBase.vue` (classe base) e\n';
out += '> `ExternalTilePlaceholder.vue` (placeholder per tile esterne non caricate).\n\n';
out += '## Legenda celle\n';
out += '- `·` = non ancora valutato · `✅` = conforme · `🟢` ok con nota minore · `🟡` estetica · `🔴` rompe coerenza/brand (da correggere)\n';
out += '- Punti: **1**COLORE · **2**SPAZI · **3**RAGGIO · **4**OMBRA · **5**TYPE · **6**ICONE · **7**STATI(hover+focus) · **8**MEDIA · **9**DEFAULT · **10**A11Y\n';
out += '- `Box` = box-model via useBoxModel · `Src` = default da fonte unica (buildDefaults)\n\n';
out += '## Definition of Done (§4)\n';
out += '- [ ] Ogni tile ha la sua riga con punti spuntati o severità motivata\n';
out += '- [ ] Zero hex hardcoded di colore nei componenti/config (solo file token)\n';
out += '- [ ] Zero emoji come icona di default\n';
out += '- [ ] Ogni elemento interattivo ha focus-visible\n';
out += '- [ ] Box-model via composable; default da fonte unica; chiavi salvate invariate\n';
out += '- [ ] Tile della stessa categoria condividono raggio, ombra, superficie, accento, scala type\n\n';

// riepilogo
out += '## Riepilogo categorie\n\n';
out += '| Categoria | # tile |\n|---|---:|\n';
for (const c of cats) out += `| ${c} | ${groups[c].length} |\n`;
out += `| **TOTALE** | **${files.length}** |\n\n`;

const HEAD = '| Tile | Config | 1 | 2 | 3 | 4 | 5 | 6 | 7 | 8 | 9 | 10 | Box | Src | Nota prima→dopo |\n'
           + '|---|:---:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|---|\n';

for (const c of cats) {
  out += `\n## ${c} (${groups[c].length})\n\n`;
  out += HEAD;
  for (const { f, base } of groups[c]) {
    const cfg = guessConfig(base);
    const cfgCell = cfg ? cfg : '—';
    out += `| ${base} | ${cfgCell} | · | · | · | · | · | · | · | · | · | · | · | · |  |\n`;
  }
}

fs.writeFileSync(path.join(ROOT, 'TILE_PROGRESS.md'), out, 'utf8');
console.log('TILE_PROGRESS.md scritto:', files.length, 'tile in', cats.length, 'categorie');
for (const c of cats) console.log('  -', c, groups[c].length);
