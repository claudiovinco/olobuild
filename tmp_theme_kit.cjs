/* ════════════════════════════════════════════════════════════════
   tmp_theme_kit.cjs — KIT CONDIVISO per ricomporre gli OLOtheme TILE-PURE.
   Garantisce le chiavi ESATTE di theme.json (Olo_Style_System), header.json
   (megamenu) e footer.json. Ogni generatore tema fa SOLO:
     const K = require('./tmp_theme_kit.cjs');
     const {sec,row,col,tile,R} = K.builders('slug');
     const home = [ ...sezioni... ];
     K.emit({ slug, name, ...config }, home);
   ════════════════════════════════════════════════════════════════ */
const fs = require('fs'), path = require('path');

let _n = 0;
const R = (n) => ({ tl: n, tr: n, br: n, bl: n, linked: true });

function builders(prefix) {
  const id = (t) => `${prefix}-${t}-${++_n}`;
  const sec = (color, padding, children, extra = {}) => ({
    id: id('se'), type: 'section',
    settings: Object.assign({ style: 'default', width: 'large', padding }, extra),
    style: { bg: { type: 'solid', color } }, advanced: {}, children,
  });
  // sec con sfondo ricco (gradient/glow) passando un oggetto bg completo
  const secBg = (bg, padding, children, extra = {}) => ({
    id: id('se'), type: 'section',
    settings: Object.assign({ style: 'default', width: 'large', padding }, extra),
    style: { bg }, advanced: {}, children,
  });
  const row = (children, settings = {}) => ({ id: id('ro'), type: 'row', settings, style: {}, advanced: {}, children });
  const col = (width, children, settings = {}) => ({ id: id('co'), type: 'column', settings: Object.assign({ width }, settings), style: {}, advanced: {}, children });
  const tile = (type, settings) => ({ id: id(type), type, settings, style: {}, advanced: {}, children: [] });
  return { sec, secBg, row, col, tile, R, id };
}

/* ---- theme.json (chiavi ESATTE — non rinominare) ---- */
function buildTheme(cfg) {
  const c = cfg.colors;
  const t = {
    id: cfg.slug, name: cfg.name, author: 'Olobuild', version: '1.0.0',
    tags: cfg.tags || [], description: cfg.description || '',
    menu: { name: cfg.name + ' Menu', items: cfg.menu || [] },
    templates: {
      header: { file: 'header.json', type: 'header', title: cfg.name + ' — Header' },
      footer: { file: 'footer.json', type: 'footer', title: cfg.name + ' — Footer' },
      homepage: { file: 'homepage.json', type: 'page', title: cfg.name + ' — Homepage' },
    },
    activate: { header: 'header', footer: 'footer' },
    pages: { home: { title: 'Home', template: 'homepage', set_as_homepage: true } },
    styles: {
      colors: {
        primary: c.primary, primary_contrast: c.primary_contrast,
        secondary: c.secondary || c.primary, secondary_contrast: c.secondary_contrast || c.primary_contrast,
        muted: c.muted, muted_contrast: c.muted_contrast || c.text,
        text: c.text, text_muted: c.text_muted,
        background: c.background, border: c.border, link: c.link || c.primary,
      },
      typography: {
        font_family: cfg.css_sans, font_family_heading: cfg.css_disp,
        font_size_base: '16px', line_height: '1.6',
        font_weight_heading: String(cfg.heading_weight || '700'),
        heading_line_height: String(cfg.heading_line_height || '1.1'),
      },
      google_fonts: cfg.google_fonts || [], spacing: {},
    },
  };
  if (cfg.cursor !== false) {
    t.cursor = Object.assign({
      enabled: true, ring_size: 36, ring_color: '#ffffff', ring_width: 1.5,
      dot_size: 7, dot_color: '#ffffff', blend_mode: 'exclusion', hot_scale: 2.2,
      hot_color: '', hide_system: true, pull_strength: 0.18,
      magnetic_selector: 'a, button', follow_easing: 0.18,
    }, cfg.cursor || {});
  }
  return t;
}

/* ---- header.json (megamenu, come Ledger) ---- */
function buildHeader(cfg) {
  const p = cfg.slug.slice(0, 2);
  const nav = cfg.header || {};
  return [{
    id: `${p}-hse`, type: 'section',
    settings: { style: 'default', width: 'expand', padding: 'none' },
    style: { bg: { type: 'solid', color: nav.bg || cfg.colors.background } }, advanced: {},
    children: [{
      id: `${p}-hro`, type: 'row',
      settings: { layout: '100', gap: 0, vertical_align: 'center' }, style: {}, advanced: {},
      children: [{
        id: `${p}-hco`, type: 'column', settings: { width: '1-1', width_medium: '1-1' }, style: {}, advanced: {},
        children: [{
          id: `${p}-hmm`, type: 'megamenu',
          settings: {
            menu_id: 'auto', layout: 'horizontal', alignment: 'right',
            font_size: '14', font_weight: '600', text_color: nav.text_color || cfg.colors.text_muted,
            sticky: true, sticky_bg: nav.sticky_bg || 'rgba(20,20,28,.85)', sticky_shadow: true,
            social_icons: false, search_icon: false,
            logo_image: 'LOGO_PLACEHOLDER', logo_width: String(nav.logo_width || 138),
            logo_position: 'left', logo_link: '/', logo_sticky: 'LOGO_PLACEHOLDER',
            mobile_logo: 'LOGO_PLACEHOLDER', mobile_logo_height: '30', mobile_bar_logo: true,
          }, style: {}, advanced: {}, children: [],
        }],
      }],
    }],
  }];
}

/* ---- footer.json (colonne link, stile Ledger) ---- */
function buildFooter(cfg) {
  const p = cfg.slug.slice(0, 2);
  const f = cfg.footer || {};
  const sans = cfg.css_sans, disp = cfg.css_disp;
  const C = cfg.colors;
  const heads = C.primary_contrast === '#ffffff' || /^#fff|white|#eaf|#f/i.test(f.headColor || '') ? (f.headColor || '#ffffff') : (f.headColor || '#ffffff');
  const headC = f.headColor || '#ffffff';
  const dim = C.text_muted, txt = C.text, line = C.border;
  const tb = (id, content) => ({ id, type: 'text-block', settings: { content, text_color: txt, font_size: '16', text_align: 'left', line_height: '1.6', max_width: '' }, style: {}, advanced: {}, children: [] });
  const brand = f.brand || { name: cfg.name, tagline: '' };
  const mark = `<span style="display:inline-block;width:24px;height:24px;background:${C.primary};border-radius:7px;vertical-align:middle;margin-right:10px"></span>`;
  const brandHtml = `<div style="font-family:${disp};font-size:20px;color:${headC};font-weight:${cfg.heading_weight||700};margin-bottom:14px;letter-spacing:-.02em">${mark}${brand.name}</div><p style="font-size:14px;color:${dim};max-width:260px">${brand.tagline||''}</p>`;
  const colHtml = (title, links) => `<h4 style="color:${headC};font-family:${sans};font-weight:700;font-size:12px;text-transform:uppercase;letter-spacing:.08em;margin:0 0 16px">${title}</h4><ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:10px;font-size:14px;color:${dim}">${links.map(l => `<li>${l}</li>`).join('')}</ul>`;
  const cols = f.columns || [];
  const linkCols = cols.map((cc, i) => ({ id: `${p}-fco${i}`, type: 'column', settings: { width: '1-5' }, style: {}, advanced: {}, children: [tb(`${p}-ftb${i}`, colHtml(cc.title, cc.links))] }));
  const bottom = f.bottom || { left: `© 2026 ${cfg.name} — an OLOtheme demo.`, right: 'Built with OLObuild' };
  const botHtml = `<div style="border-top:1px solid ${line};padding-top:24px;margin-top:42px;display:flex;justify-content:space-between;gap:10px;flex-wrap:wrap;font-size:12.5px;color:${dim}"><span>${bottom.left}</span><span>${bottom.right}</span></div>`;
  return [{
    id: `${p}-fse`, type: 'section', settings: { style: 'default', width: 'large', padding: 'small' },
    style: { bg: { type: 'solid', color: f.bg || C.muted } }, advanced: {},
    children: [
      { id: `${p}-fro1`, type: 'row', settings: { gap: 32 }, style: {}, advanced: {}, children: [
        { id: `${p}-fcb`, type: 'column', settings: { width: '2-5' }, style: {}, advanced: {}, children: [tb(`${p}-ftbb`, brandHtml)] },
        ...linkCols,
      ] },
      { id: `${p}-fro2`, type: 'row', settings: {}, style: {}, advanced: {}, children: [
        { id: `${p}-fcbot`, type: 'column', settings: { width: '1-1' }, style: {}, advanced: {}, children: [tb(`${p}-ftbot`, botHtml)] },
      ] },
    ],
  }];
}

/* ---- copia loghi ufficiali (variant 'light' = bianco su scuro / 'dark' = colorato) ---- */
function ensureLogos(dir, variant) {
  const img = path.join(__dirname, 'assets', 'img');
  const white = path.join(img, 'olobuild-logo-200-white.png');
  const dark = path.join(img, 'olobuild-logo-200.png');
  const srcMain = variant === 'dark' ? dark : white;
  for (const name of ['logo.png', 'logo-light.png']) {
    const dest = path.join(dir, name);
    try { if (fs.existsSync(srcMain)) fs.copyFileSync(srcMain, dest); } catch (e) {}
  }
}

function emit(cfg, home) {
  const dir = path.join(__dirname, 'assets', 'data', 'themes', cfg.slug);
  fs.mkdirSync(dir, { recursive: true });
  fs.writeFileSync(path.join(dir, 'homepage.json'), JSON.stringify(home));
  fs.writeFileSync(path.join(dir, 'header.json'), JSON.stringify(buildHeader(cfg)));
  fs.writeFileSync(path.join(dir, 'footer.json'), JSON.stringify(buildFooter(cfg)));
  fs.writeFileSync(path.join(dir, 'theme.json'), JSON.stringify(buildTheme(cfg)));
  ensureLogos(dir, cfg.logo_variant || 'light');
  // diagnostica
  let nodes = 0; const types = {};
  const walk = (ns) => ns.forEach(x => { nodes++; types[x.type] = (types[x.type] || 0) + 1; if (x.children) walk(x.children); });
  walk(home);
  const tb = types['text-block'] || 0;
  console.log(`[${cfg.slug}] ${cfg.name}: ${home.length} sez | ${nodes} nodi | text-block ${tb} ${tb > 0 ? '⚠️(verifica: solo footer ammesso)' : '✓'}`);
  console.log('  tile:', JSON.stringify(types));
  return { nodes, types };
}

module.exports = { builders, buildTheme, buildHeader, buildFooter, emit, ensureLogos, R };
