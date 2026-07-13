/**
 * Genera i 13 template olobuild (JSON) della replica olotheme.com.
 * Output: ./olox-out/<slug>.json — { title, slug, content: [...sections] }
 */
import { mkdirSync, writeFileSync } from 'fs';
import { randomUUID } from 'crypto';

const OUT = new URL('./olox-out/', import.meta.url).pathname.replace(/^\/([A-Za-z]:)/, '$1');
mkdirSync(OUT, { recursive: true });

const LOGO = (n) => `/wp-content/uploads/olotheme-site/${n}-orizz.png`;
const HOME_URL = '/'; // la Experience è la front page del sito
const P = (slug) => `/${slug}/`;

const tile = (type, settings) => ({ id: randomUUID(), type, settings, style: [], advanced: [] });
const col = (children) => ({ id: randomUUID(), type: 'column', settings: { bg: { type: 'none' }, width_medium: '1-1' }, style: [], advanced: [], children });
const row = (children) => ({ id: randomUUID(), type: 'row', settings: { bg: { type: 'none' }, layout: '100', stack_mobile: true }, style: [], advanced: [], children });
const section = (tiles) => ({
  id: randomUUID(), type: 'section',
  settings: { bg: { type: 'none' }, style: 'default', width: 'fullbleed', padding: 'remove-vertical', bg_scope: 'container' },
  style: [], advanced: [],
  children: [row([col(tiles)])],
});
const page = (tiles) => tiles.map((t) => section([t]));

/* ---------- pezzi comuni ---------- */
const NAV_LINKS = (active) => ([
  { label: 'build', url: P('olobuild'), color: 'build', active: active === 'build' },
  { label: 'booking', url: P('olobooking'), color: 'booking', active: active === 'booking' },
  { label: 'lang', url: P('ololang'), color: 'lang', active: active === 'lang' },
  { label: 'security', url: P('olosecurity'), color: 'secur', active: active === 'secur' },
  { label: 'tour', url: P('olotour'), color: 'tour', active: active === 'tour' },
  { label: 'tutor', url: P('olotutor'), color: 'tutor', active: active === 'tutor' },
]);
const nav = (active, accent, expText, expUrl) => tile('oloxnav', {
  logo: LOGO('olotheme'), logo_url: HOME_URL, links: NAV_LINKS(active),
  show_lang: true, exp_text: expText || '← il viaggio', exp_url: expUrl || HOME_URL, accent,
});
const FOOT_ALL = [
  ['il viaggio', HOME_URL], ['build', P('olobuild')], ['booking', P('olobooking')],
  ['lang', P('ololang')], ['security', P('olosecurity')], ['tour', P('olotour')], ['tutor', P('olotutor')],
];
const foot = (excludeLabel, fine) => tile('oloxfoot', {
  logo: LOGO('olotheme'),
  links: FOOT_ALL.filter(([l]) => l !== excludeLabel).map(([label, url]) => ({ label, url })),
  fine: fine || 'GPL · Trento · no SaaS',
  show_credits: true,
  credits_html: 'OLOtheme by <a href="https://clod.eu" target="_blank" rel="noopener">clod.eu</a> | @2026 | sito introduttivo | <a href="mailto:info@olotheme.com">info@olotheme.com</a>',
  accent: 'olo',
});
const footManuals = () => tile('oloxfoot', {
  logo: LOGO('olotheme'),
  links: [
    { label: 'build', url: P('olobuild-manuale') }, { label: 'booking', url: P('olobooking-manuale') },
    { label: 'lang', url: P('ololang-manuale') }, { label: 'security', url: P('olosecurity-manuale') },
    { label: 'tour', url: P('olotour-manuale') }, { label: 'tutor', url: P('olotutor-manuale') },
  ],
  fine: 'manuali base · GPL · Trento', show_credits: true,
  credits_html: 'OLOtheme by <a href="https://clod.eu" target="_blank" rel="noopener">clod.eu</a> | @2026 | sito introduttivo | <a href="mailto:info@olotheme.com">info@olotheme.com</a>',
  accent: 'olo',
});
const follow = (accent) => tile('oloxbanner', {
  variant: 'follow', accent, fk_text: 'In arrivo',
  body_html: 'Versione demo o gratuita/completa in arrivo: segui <a href="https://www.linkedin.com/company/olotheme/" target="_blank" rel="noopener">OLOtheme su LinkedIn</a> o <a href="https://www.linkedin.com/in/vincoclaudio/" target="_blank" rel="noopener">Claudio Vinco</a> per rimanere aggiornato.',
});
const next = (label, html, url, accent) => tile('oloxbanner', { variant: 'next', accent, label, link_html: html, link_url: url });
const marquee = (items, sep, accent, reverse) => tile('oloxmarquee', {
  items: items.map((text) => ({ text })), sep, accent, reverse: !!reverse, duration: 28,
});

/* ---------- home "vera olobuild": sezioni native + tile classiche ---------- */
// Palette del design (valori contenuto, come li sceglierebbe l'utente dal picker).
const INK = { bg: '#0C0E13', paper: '#FAF7F2', dim: '#B9BDC9', faint: '#8B90A0' };
// tour = #12A19A: colore base del logo OLOtour (decisione utente 2026-07-13;
// il vecchio design usava ambra #F5A623, ma il brand del prodotto è teal).
const PRODUCT_HEX = { build: '#E8453D', booking: '#3D8BFF', lang: '#E8409A', secur: '#26B8E8', tour: '#12A19A', tutor: '#38C172' };

// Sezione-fermata: bg scuro edge-to-edge, contenuto centrato in verticale,
// Stile → Sticky → "Cover orizzontale" (il core raggruppa le sezioni adiacenti
// in un binario orizzontale a runtime — feature nativa di olobuild).
const hSection = (rows, glowHex) => ({
  id: randomUUID(), type: 'section',
  settings: {
    // Sezione TRASPARENTE: fondo e halo li porta la tile "Luce di pagina"
    // (atmosfera). La sezione dichiara solo il SUO colore luce: la luce fissa
    // sfuma da un colore all'altro allo scroll, come sul sito originale.
    bg: { type: 'none' },
    light_color: glowHex || '',
    style: 'default',
    width: 'default', padding: 'custom', padding_top_custom: 60, padding_bottom_custom: 60,
    bg_scope: 'section', sticky_effect: 'cover-h', sticky_top: 0,
    flex_direction: 'column', flex_justify: 'center',
  },
  style: [], advanced: [], children: rows,
});
const hRow = (layout, cols) => ({
  id: randomUUID(), type: 'row',
  settings: { bg: { type: 'none' }, layout, stack_mobile: true },
  style: [], advanced: [], children: cols,
});
const hCol = (children, w = '1-1') => ({
  id: randomUUID(), type: 'column',
  settings: { bg: { type: 'none' }, width_medium: w },
  style: [], advanced: [], children,
});

// Tile classiche pre-configurate per il tema scuro del sito.
// Kicker mono: etichetta senza pill, JetBrains via set globale olox-mono.
const xKicker = (text, hex) => tile('badge', {
  text, variant: 'outline', bg_color: 'transparent', text_color: hex || INK.faint,
  typography_preset: 'olox-mono',
  font_size: '11', font_weight: '700', text_transform: 'uppercase', letter_spacing: '2.4',
  padding_y: 0, padding_x: 0, alignment: 'left',
});
// Titolo Fraunces regular con accento corsivo colorato (come il live).
const xTitle = (heading, accent, hex, size, tag) => tile('headline', {
  heading, accent_text: accent || '', accent_color: hex || PRODUCT_HEX.build, accent_italic: true,
  tag: tag || 'h2', alignment: 'left', heading_size: 'lg', heading_font_size: size || '52',
  heading_font: "'Fraunces', serif", heading_weight: '400',
  heading_color: INK.paper, decoration: 'none', subtitle: '',
});
const xText = (html, size, color) => tile('text-block', {
  content: html, text_color: color || INK.dim, font_size: size || '16',
  text_align: 'left', max_width: '560',
  tile_padding: { top: 0, right: 0, bottom: 0, left: 0 },
});
// Tag pills: la prima "hot" col colore prodotto, le altre neutre — un badge multiplo.
const xTags = (tags, hex) => tile('badge', {
  text: tags[0], variant: 'outline', bg_color: hex, text_color: hex,
  typography_preset: 'olox-mono',
  font_size: '11', font_weight: '600', text_transform: 'lowercase', letter_spacing: '0.5',
  padding_y: 7, padding_x: 13, alignment: 'left',
  extra_items: tags.slice(1).map((t) => ({ text: t, color: 'rgba(250,247,242,.25)' })),
});
// CTA mono squadrato: uppercase, spaziatura larga, angoli 3px.
const xBtn = (text, url, hex) => tile('button', {
  text, url, alignment: 'left', bg_color: hex, text_color: INK.bg,
  hover_bg_color: INK.paper, hover_text_color: INK.bg,
  typography_preset: 'olox-mono',
  font_size: '12', font_weight: '700', text_transform: 'uppercase', letter_spacing: '2',
  border_radius: { tl: 3, tr: 3, br: 3, bl: 3 },
  tile_padding: { top: 15, right: 24, bottom: 15, left: 24 },
});
// CTA fantasma: bordo tenue, testo chiaro.
const xBtnGhost = (text, url) => tile('button', {
  text, url, alignment: 'left', bg_color: 'transparent', text_color: INK.paper,
  hover_bg_color: 'rgba(250,247,242,.08)', hover_text_color: INK.paper,
  typography_preset: 'olox-mono',
  font_size: '12', font_weight: '700', text_transform: 'uppercase', letter_spacing: '2',
  border_radius: { tl: 3, tr: 3, br: 3, bl: 3 },
  tile_padding: { top: 15, right: 24, bottom: 15, left: 24 },
  border: { top: 1, right: 1, bottom: 1, left: 1, linked: true, style: 'solid', color: 'rgba(250,247,242,.3)' },
});
// Logo prodotto (i PNG orizzontali sono già chiari su scuro).
const xLogo = (name, alt) => tile('image', {
  image_url: LOGO(name), alt_text: alt || name, image_width: '170px',
  height: 'auto', object_fit: 'contain', image_alignment: 'left',
});
// Indice fermata "01 / 06" mono.
const xIdx = (idx) => tile('badge', {
  text: idx, variant: 'outline', bg_color: 'transparent', text_color: INK.faint,
  typography_preset: 'olox-mono',
  font_size: '12', font_weight: '600', text_transform: 'none', letter_spacing: '2',
  padding_y: 0, padding_x: 0, alignment: 'left',
});
const xScene = (scene, color, coord) => tile('oloxscene', { scene, color, coord, show_deco: true });
// Respiro verticale fra tile (la via del builder: tile spacer).
const xGap = (h) => tile('spacer', { height: String(h) });
// Etichetta mono "nuda" (idx, coordinate, hint): badge trasparente senza pill.
const xMono = (text, { size = '11', color = INK.faint, ls = '2', align = 'left', upper = true } = {}) => tile('badge', {
  text, variant: 'outline', bg_color: 'transparent', text_color: color,
  typography_preset: 'olox-mono',
  font_size: size, font_weight: '600', text_transform: upper ? 'uppercase' : 'none', letter_spacing: ls,
  padding_y: 0, padding_x: 0, alignment: align,
});
// Coordinate tecniche mono (in fondo alla colonna testo, come il live).
const xCoord = (text) => xMono(text, { size: '10', color: 'rgba(250,247,242,.35)', ls: '3' });
// Colonna flex-row: più tile affiancate (coppie di bottoni, ecc.).
const hColRow = (children, w = '1-1', gap = '12') => ({
  id: randomUUID(), type: 'column',
  settings: { bg: { type: 'none' }, width_medium: w, flex_direction: 'row', flex_wrap: 'wrap', flex_align: 'center', flex_column_gap: gap, flex_row_gap: gap },
  style: [], advanced: [], children,
});

// Una fermata prodotto = sezione cover-h con row 50-50: copy classico + scena.
const productStop = (p) => hSection([
  hRow('50-50', [
    hCol([
      ...(p.anchor ? [ tile('menuanchor', { anchor_id: p.anchor }) ] : []),
      xLogo(p.logo || p.name.toLowerCase(), p.name),
      xGap(18),
      xKicker(p.kicker, PRODUCT_HEX[p.color]),
      xGap(12),
      xTitle(p.title, p.accent, PRODUCT_HEX[p.color]),
      xGap(14),
      xText(p.sub, '16'),
      xGap(24),
      xTags(p.tags, PRODUCT_HEX[p.color]),
      xGap(26),
      xBtn(p.cta, p.url, PRODUCT_HEX[p.color]),
      xGap(48),
      xCoord(p.coord),
    ], '1-2'),
    hCol([
      xIdx(p.idx),
      xScene(p.scene, p.color, ''),
    ], '1-2'),
  ]),
], PRODUCT_HEX[p.color]);

/* ==================================================================== */
/* HOME EXPERIENCE                                                       */
/* ==================================================================== */
// Le fermate prodotto: contenuti reali, composti con tile classiche.
const STOPS = [
  { color: 'build', name: 'OLObuild', anchor: 'viaggio', kicker: 'Il telaio \u00b7 page builder', title: 'Costruisce come un cantiere', accent: 'cantiere', sub: '<p>Mattone su mattone: <strong>187 tile in 12 famiglie</strong>, tutti auto-discovered, con animazioni ed effetti di serie. <strong>La Free (100+ tile) vale quanto i builder Pro a pagamento della concorrenza</strong>; Pro sblocca l\u2019intera libreria.</p>', tags: ['\u20ac0 free \u00b7 100+ tile', '36 animazioni', 'Woo nativo', 'dark mode'], cta: 'Entra nel cantiere', url: P('olobuild'), scene: 'wall', coord: 'grid \u00b7 44\u00d744 \u00b7 lot 187', idx: '01 / 06' },
  { color: 'booking', name: 'OLObooking', kicker: 'Prenotazioni \u00b7 6 verticali', title: 'Un motore che riempie l\u2019agenda', accent: 'l\u2019agenda', sub: '<p>Camere, tavoli, appuntamenti, eventi, noleggi, immobili: <strong>una sola configurazione</strong> e il motore diventa il tuo mestiere. Con caparra anti no-show e zero commissioni.</p>', tags: ['6 verticali', 'anti no-show', 'QR access', '0% commissioni'], cta: 'Apri il calendario', url: P('olobooking'), scene: 'cal', coord: 'occupancy feed \u00b7 live', idx: '02 / 06' },
  { color: 'lang', name: 'OLOlang', kicker: 'Multilingua nativo', title: 'Lo stesso sito, 28 voci', accent: '28 voci', sub: '<p>DeepL + traduttore IA, glossario e memoria di traduzione. Contenuti, menu e stringhe tradotti <strong>via database</strong>, con hreflang, URL localizzati e sitemap per ogni lingua.</p>', tags: ['28 lingue', 'DeepL + IA', 'SEO hreflang', 'a vita con Pro'], cta: 'Cambia lingua', url: P('ololang'), scene: 'lang', coord: 'hreflang \u00d7 28', idx: '03 / 06' },
  { color: 'secur', name: 'OLOsecurity', kicker: 'Sicurezza \u00b7 100% locale', title: 'Un radar che non dorme mai', accent: 'dorme mai', sub: '<p>Firewall OWASP, 2FA, scanner anti-webshell e bonifica guidata dal pannello <strong>Sentinel</strong>. Tutto elaborato <strong>sul tuo server</strong>: il traffico non finisce in nessun cloud altrui.</p>', tags: ['100% locale', 'mini-WAF', 'TOTP 2FA', 'Plugin Check 0/0'], cta: 'Accendi il radar', url: P('olosecurity'), scene: 'radar', coord: 'perimetro \u00b7 armato', idx: '04 / 06' },
  { color: 'tour', name: 'OLOtour', kicker: 'Tour virtuali \u00b7 in arrivo', title: 'Guarda dentro, prima di entrare', accent: 'prima di entrare', sub: '<p>Panorami sferici e HDRI (Polyhaven, Street View), <strong>hot-spot cliccabili</strong>, ambienti collegati, fruizione VR. Il sopralluogo diventa parte del sito, e finisce sul bottone \u201cprenota\u201d.</p>', tags: ['360\u00b0', 'hot-spot', 'multi-stanza', 'VR ready'], cta: 'Affaccia lo sguardo', url: P('olotour'), scene: 'pano', coord: 'lat 46.07 \u00b7 lon 11.12 \u00b7 trento', idx: '05 / 06' },
  { color: 'tutor', name: 'OLOtutor', kicker: 'Formazione \u00b7 in arrivo', title: 'Sali di livello, lezione dopo lezione', accent: 'lezione dopo lezione', sub: '<p>Corsi, quiz, punti e badge, registro voti e certificati, dentro il tuo WordPress. <strong>Gli allievi restano tuoi</strong>, non di un marketplace che ti mette in fila coi concorrenti.</p>', tags: ['LMS', 'quiz & badge', 'certificati', 'area allievi'], cta: 'Iscriviti all\u2019idea', url: P('olotutor'), scene: 'course', coord: 'syllabus \u00b7 v1 \u00b7 4 lezioni', idx: '06 / 06' },
];

const home = {
  title: 'OLOtheme \u00b7 Experience',
  slug: 'olotheme-experience',
  content: [
    // Fermata 0 \u00b7 intro \u2014 tile classiche (barra scroll, badge, titolo, testo, CTA, marquee)
    hSection([
      hRow('100', [
        hColRow([
          // Atmosfera: fondo ink + luce che segue le fermate (colore per-sezione).
          tile('pagelight', {
            light_color: PRODUCT_HEX.build, base_color: INK.bg,
            position: 'center', size: 90, intensity: 26, transition_ms: 800,
          }),
          tile('scrollprogress', { position: 'bottom', bar_color: PRODUCT_HEX.build, bar_bg: 'rgba(250,247,242,.08)', bar_height: '3', z_index: '9000' }),
          xKicker('OLOtheme \u00b7 suite WordPress \u00b7'),
          // Popup "olonica": tile nativa popup in modalit\u00e0 TEMPLATE \u2014 dentro la
          // modale si monta il partial di tile classiche (kicker, titolo,
          // testi, card battaglia con chips). Il segnaposto OLOX_TPL viene
          // risolto in id reale dall'inserter.
          tile('popup', {
            mode: 'template', template_id: 'OLOX_TPL:olox-popup-olonica',
            button_text: 'olonica', button_style: 'link', button_size: 'small', button_uppercase: true,
            modal_title: '',
            modal_bg: '#0E1016', modal_text_color: INK.dim, modal_title_color: INK.paper,
            modal_radius: '16', modal_overlay: '72', modal_border_width: '1', modal_border_color: PRODUCT_HEX.build,
            modal_size: 'container',
          }),
        ], '1-1', '8'),
      ]),
      hRow('100', [
        hCol([
          xTitle('Un telaio. Sei prodotti. Nessuna catena.', 'Nessuna catena.', PRODUCT_HEX.build, '68', 'h1'),
          xText('<p>Niente SaaS, niente lock-in, niente cloud altrui: tutto vive <strong>sul tuo hosting</strong>, in GPL, scritto a Trento. Scorri: ogni fermata \u00e8 un prodotto.</p>', '17'),
        ]),
      ]),
      hRow('100', [
        hColRow([
          xBtn('Inizia il viaggio \u2192', '#viaggio', PRODUCT_HEX.build),
          xBtnGhost('Contatti', '#capolinea'),
        ]),
      ]),
      hRow('100', [
        hCol([
          tile('marquee', {
            content_type: 'text',
            text_items: 'no SaaS \u25cf GPL \u25cf 187 tile \u25cf 28 lingue \u25cf 6 verticali booking \u25cf 100% locale \u25cf made in Trento',
            separator: ' \u25cf ', speed: '30', direction: 'left', pause_hover: true, gap: '60',
            bg_color: 'transparent', text_color: INK.faint, font_size: '12', font_weight: '600',
            letter_spacing: '2', text_transform: 'uppercase', font_family: 'JetBrains Mono', height: '44',
            border_top: '1', border_bottom: '0', border_color: 'rgba(250,247,242,.14)',
          }),
          xGap(10),
          xMono('Scrolla in basso \u2192 si va a destra', { size: '10', ls: '3', align: 'right' }),
        ]),
      ]),
    ], PRODUCT_HEX.build),
    // Fermate prodotto \u00b7 una sezione "Cover orizzontale" ciascuna
    ...STOPS.map(productStop),
    // Fermata finale \u00b7 capolinea + scena mad-lib
    hSection([
      hRow('50-50', [
        hCol([
          tile('menuanchor', { anchor_id: 'capolinea' }),
          xKicker('Capolinea \u00b7 si scende'),
          xTitle('Tutto questo, sul tuo hosting', 'sul tuo hosting', PRODUCT_HEX.build, '56'),
          xText('<p>GPL \u00b7 niente SaaS \u00b7 GDPR in casa \u00b7 30 giorni di rimborso su OLObuild Pro. Ogni fermata ha la sua pagina di approfondimento.</p>'),
          xGap(20),
          tile('badge', {
            text: 'OLObuild', variant: 'outline', bg_color: 'rgba(250,247,242,.28)', text_color: INK.paper,
            typography_preset: 'olox-mono',
            font_size: '11', font_weight: '700', text_transform: 'uppercase', letter_spacing: '1.5',
            padding_y: 10, padding_x: 18, alignment: 'left',
            extra_items: ['OLObooking', 'OLOlang', 'OLOsecurity', 'OLOtour', 'OLOtutor'].map((t) => ({
              text: t, color: 'rgba(250,247,242,.28)', text_color: INK.paper,
            })),
          }),
          xGap(34),
          xMono('OLOtheme \u00b7 made in Trento \u00b7 no SaaS \u00b7 nessuna catena', { size: '10', ls: '2.5' }),
        ], '1-2'),
        hCol([
          xScene('madlib', 'olo', ''),
        ], '1-2'),
      ]),
    ], PRODUCT_HEX.build),
  ],
};

/* ==================================================================== */
/* OLOBUILD                                                              */
/* ==================================================================== */
/* ==================================================================== */
/* OLOBUILD — sezioni verticali + tile componibili                       */
/* ==================================================================== */
// Sezione verticale trasparente con colore luce (per la tile Luce di pagina).
const vSection = (rows, lightHex, extra = {}) => ({
  id: randomUUID(), type: 'section',
  settings: {
    bg: { type: 'none' }, light_color: lightHex || '',
    style: 'default', width: 'default',
    padding: 'custom', padding_top_custom: 90, padding_bottom_custom: 90,
    bg_scope: 'section', sticky_effect: 'none',
    ...extra,
  },
  style: [], advanced: [], children: rows,
});

/* ---------- helpers CLASSICI condivisi (pagine prodotto + manuali) ---------- */
// Scena showcase con settings extra (righe console/terminale, medaglia…).
const xSceneX = (scene, color, extra = {}) => tile('oloxscene', { scene, color, coord: '', show_deco: true, ...extra });
// Colonna scena: centrata in verticale accanto al copy.
const sceneCol = (scene, color, extra) => ({
  id: randomUUID(), type: 'column',
  settings: { bg: { type: 'none' }, width_medium: '1-2', flex_direction: 'column', flex_justify: 'center' },
  style: [], advanced: [],
  children: [ xSceneX(scene, color, extra) ],
});
// Pill singola outline colore prodotto (timbri, stati).
const xPill = (text, hex) => tile('badge', {
  text, variant: 'outline', bg_color: hex, text_color: hex,
  typography_preset: 'olox-mono',
  font_size: '11', font_weight: '700', text_transform: 'uppercase', letter_spacing: '1.5',
  padding_y: 8, padding_x: 16, alignment: 'left',
});
// Testata di sezione: kicker + titolo + lead.
const secHead = (kicker, title, accent, hex, lead, size = '44') => [
  xKicker(kicker, hex), xGap(12), xTitle(title, accent, hex, size),
  ...(lead ? [xGap(10), xText(`<p>${lead}</p>`)] : []), xGap(30),
];
// Griglia di card scure (famiglie/verticali/difese) su info-cards nativa.
// items: [counterLabel, title, description, footerText?]
const xCards = (hex, columns, items) => tile('info-cards', {
  columns, container_bg: { type: 'none' }, container_padding: 0,
  card_bg: { type: 'solid', color: '#12151D' },
  card_color: INK.paper, card_accent_color: hex, card_hover_effect: 'glow',
  title_size: 20, title_weight: '700', title_italic: false, counter_size: 10,
  items: items.map(([label, t2, d, foot]) => ({
    counter: label, counter_label: '', title: t2, title_accent: '', description: d,
    icon: '', footer_dot_color: hex, footer_text: foot || '', link_url: '', media_image: '', media_label: '',
  })),
});
// Marquee nativo mono con bordi sopra/sotto.
const marqueeC = (items, sep) => tile('marquee', {
  content_type: 'text', text_items: items.join(` ${sep} `),
  separator: ` ${sep} `, speed: '30', direction: 'left', pause_hover: true, gap: '60',
  bg_color: 'transparent', text_color: INK.faint, font_size: '12', font_weight: '600',
  letter_spacing: '2', text_transform: 'uppercase', font_family: 'JetBrains Mono', height: '44',
  border_top: '1', border_bottom: '1', border_color: 'rgba(250,247,242,.14)',
});
const marqueeSection = (items, sep, hex) => vSection([
  hRow('100', [ hCol([ marqueeC(items, sep) ]) ]),
], hex || '', { padding_top_custom: 0, padding_bottom_custom: 0 });
// Sezione a colonna singola.
const bodySection = (tiles, hex, extra) => vSection([ hRow('100', [ hCol(tiles) ]) ], hex, extra);
// Hero prodotto classico: copy a sinistra, scena showcase a destra, CTA sotto.
const productHeroC = (p) => vSection([
  hRow('50-50', [
    hCol([
      tile('pagelight', { light_color: p.hex, base_color: INK.bg, position: 'top-right', size: 95, intensity: 24, transition_ms: 800 }),
      xLogo(p.logo, p.name),
      xGap(18),
      xKicker(p.kicker, p.hex),
      xGap(12),
      xTitle(p.title, p.accent, p.hex, '62', 'h1'),
      xGap(14),
      xText(p.sub),
      xGap(24),
      xTags(p.tags, p.hex),
    ], '1-2'),
    sceneCol(p.scene, p.color, p.sceneExtra),
  ]),
  hRow('50-50', [
    hColRow([ xBtn(p.cta1[0], p.cta1[1], p.hex), xBtnGhost(p.cta2[0], p.cta2[1]) ], '1-2'),
    hCol([], '1-2'),
  ]),
], p.hex, { padding_top_custom: 130, padding_bottom_custom: 40 });
// Banner "in arrivo / segui" — cta-banner nativa.
const followC = (hex) => bodySection([
  tile('cta-banner', {
    headline: 'In arrivo', headline_accent: '', headline_accent_italic: false,
    subtitle: 'Versione demo o gratuita/completa in arrivo: segui OLOtheme su LinkedIn per rimanere aggiornato.',
    cta_text: 'OLOtheme su LinkedIn →', cta_url: 'https://www.linkedin.com/company/olotheme/',
    bg: { type: 'solid', color: '#12151D' },
  }),
], hex, { padding_top_custom: 40, padding_bottom_custom: 0 });
// Coppia di banner in coda pagina (manuale + prossima fermata).
// items: [headline, accent, subtitle, ctaText, url]
const nextBannersC = (hex, items) => vSection([
  hRow('50-50', items.map(([headline, accent, sub, ctaText, url]) => hCol([
    tile('cta-banner', {
      headline, headline_accent: accent, headline_accent_italic: !!accent,
      subtitle: sub, cta_text: ctaText, cta_url: url,
      bg: { type: 'solid', color: '#12151D' },
    }),
  ], '1-2'))),
], hex, { padding_top_custom: 40, padding_bottom_custom: 120 });
// Estrae l'accento <em> da un titolo HTML → { text, accent } per la headline.
const emSplit = (html) => {
  const m = html.match(/<em>(.*?)<\/em>/);
  const clean = (s) => s.replace(/<\/?em>/g, '').replace(/&amp;/g, '&');
  return { text: clean(html), accent: m ? clean(m[1]) : '' };
};
const stripTags = (s) => s.replace(/<[^>]+>/g, '').replace(/&amp;/g, '&').replace(/&lt;/g, '<').replace(/&gt;/g, '>');
// Normalizza i body HTML dei manuali (classi .dash/.notice/.dtab → HTML pulito
// che il text-block nativo rende con i suoi stili).
const mdBody = (html) => html
  .replace(/<ul class="dash">/g, '<ul>')
  .replace(/<div class="notice"><span class="nl">(.*?)<\/span><p>(.*?)<\/p><\/div>/g, '<p><strong>$1 — </strong>$2</p>')
  .replace(/<table class="dtab"><tbody>(.*?)<\/tbody><\/table>/g, (mm, rows) => {
    const items = [...rows.matchAll(/<tr><td class="f">(.*?)<\/td><td>(.*?)<\/td><\/tr>/g)]
      .map(([, f, d]) => `<li><strong>${f.replace(/<span>(.*?)<\/span>/, ' · $1')}</strong> — ${d}</li>`);
    return `<ul>${items.join('')}</ul>`;
  });
const buildPage = {
  title: 'OLObuild · Il cantiere del tuo sito',
  slug: 'olobuild',
  content: [
    // HERO — luce rossa, testo classico + scena showcase griglia cantiere
    vSection([
      hRow('50-50', [
        hCol([
          tile('pagelight', { light_color: PRODUCT_HEX.build, base_color: INK.bg, position: 'top-right', size: 95, intensity: 24, transition_ms: 800 }),
          xLogo('olobuild', 'OLObuild'),
          xGap(18),
          xKicker('Il telaio · page builder olonico', PRODUCT_HEX.build),
          xGap(12),
          xTitle('Mattone su\nmattone.', 'mattone.', PRODUCT_HEX.build, '62', 'h1'),
          xGap(14),
          xText('<p><strong>187 tile in 12 famiglie</strong>, auto-discovered, con animazioni ed effetti di serie. Il cantiere è aperto: la Free, con <strong>oltre 100 tile</strong>, è al livello delle versioni Pro a pagamento della concorrenza.</p>'),
          xGap(24),
          xTags(['€0 free · 100+ tile', '36 animazioni', '11 effetti testo', 'Woo nativo'], PRODUCT_HEX.build),
        ], '1-2'),
        {
          id: randomUUID(), type: 'column',
          settings: { bg: { type: 'none' }, width_medium: '1-2', flex_direction: 'column', flex_justify: 'center' },
          style: [], advanced: [],
          children: [ xScene('hero-wall', 'build', '') ],
        },
      ]),
      hRow('50-50', [
        hColRow([
          xBtn('Guarda il cantiere ↓', '#cantiere', PRODUCT_HEX.build),
          xBtnGhost('Free vs Pro', '#prezzi'),
        ], '1-2'),
        hCol([], '1-2'),
      ]),
    ], PRODUCT_HEX.build, { padding_top_custom: 130, padding_bottom_custom: 40 }),
    // MARQUEE
    vSection([
      hRow('100', [ hCol([
        tile('marquee', {
          content_type: 'text',
          text_items: 'quickview ▪ hotspot 3D ▪ before/after ▪ viewer 360° ▪ marquee ▪ countdown ▪ query loop ▪ dark mode ▪ form builder ▪ lottie',
          separator: ' ▪ ', speed: '30', direction: 'left', pause_hover: true, gap: '60',
          bg_color: 'transparent', text_color: INK.faint, font_size: '12', font_weight: '600',
          letter_spacing: '2', text_transform: 'uppercase', font_family: 'JetBrains Mono', height: '44',
          border_top: '1', border_bottom: '1', border_color: 'rgba(250,247,242,.14)',
        }),
      ]) ]),
    ], '', { padding_top_custom: 0, padding_bottom_custom: 0 }),
    // IL CANTIERE — assembler (tile scenografica dedicata, come i minigiochi)
    vSection([
      hRow('100', [ hCol([
        tile('menuanchor', { anchor_id: 'cantiere' }),
        tile('oloxsticky', {
          accent: 'build', variant: 'assembler', kicker: 'Il cantiere',
          browser_url: 'https://il-tuo-sito.it, costruito con OLObuild',
          asm_hint: '▼ continua a scorrere',
          asm_blocks: [
            { text: 'header + menu' }, { text: 'hero animato' }, { text: 'galleria media' },
            { text: 'form builder' }, { text: 'footer' },
          ],
          asm_steps: [
            { text: 'Scrolla: il sito si <em>monta da solo</em>.' },
            { text: 'Fase 1 · <em>header</em> e menu al loro posto.' },
            { text: 'Fase 2, l’<em>hero</em> animato entra in scena.' },
            { text: 'Fase 3, la <em>galleria</em> aggancia i media.' },
            { text: 'Fase 4, il <em>form</em> raccoglie contatti.' },
            { text: 'Fase 5 · <em>footer</em>: sito consegnato. ~1h30.' },
          ],
        }),
      ]) ]),
    ], PRODUCT_HEX.build, { padding_top_custom: 0, padding_bottom_custom: 0 }),
    // LA LIBRERIA — 12 famiglie come card classiche
    vSection([
      hRow('100', [ hCol([
        xKicker('La libreria', PRODUCT_HEX.build),
        xGap(12),
        xTitle('12 famiglie, posate come mattoni', 'mattoni', PRODUCT_HEX.build, '44'),
        xGap(10),
        xText('<p>Ogni famiglia arriva da sinistra e da destra, come in cantiere. 187 tile, un solo motore.</p>'),
        xGap(30),
        tile('info-cards', {
          columns: 4, container_bg: { type: 'none' }, container_padding: 0,
          card_bg: { type: 'solid', color: '#12151D' },
          card_color: INK.paper, card_accent_color: PRODUCT_HEX.build,
          card_hover_effect: 'glow',
          title_size: 20, title_weight: '700', title_italic: false, counter_size: 10,
          items: [
            ['31', 'WooCommerce', 'Quickview, wishlist, comparazione, bundle, filtro AJAX, checkout multi-step.'],
            ['22', 'Booking', 'Calendario disponibilità, picker, slot orari, reception olo-spaces.'],
            ['20', 'Interactive', 'Card 3D, hotspot, before-after, immagini frantumate, Viewer 360°, Lottie.'],
            ['19', 'Media', 'Gallerie, video, audio, slider e feed social con lazy-load.'],
            ['18', 'Marketing', 'Hero, contatori, countdown, prezzi, testimonianze, newsletter.'],
            ['16', 'Navigation', 'Menu, header/footer, scroll-progress, switch lingua e dark-mode.'],
            ['15', 'Dynamic', 'Post grid, query loop, related, meta, ricerca live.'],
            ['10', 'Text', 'Heading animati, testo mascherato, marquee, TextPath.'],
            ['10', 'Olo-Space', 'Stanze, servizi, prezzi, host card, calendario.'],
            ['9', 'Essential', 'Immagine, video, bottone, icone, liste, tabella.'],
            ['7', 'Layout', 'Sezioni, righe, colonne, spacer, divisori di forma.'],
            ['2', 'Creative', 'Nastri e ticker animati a scorrimento infinito.'],
          ].map(([n, t2, d]) => ({
            counter: n, counter_label: 'tile', title: t2, title_accent: '', description: d,
            icon: '', footer_dot_color: PRODUCT_HEX.build, footer_text: '', link_url: '', media_image: '', media_label: '',
          })),
        }),
      ]) ]),
    ], PRODUCT_HEX.build),
    // DUE EDIZIONI — 2× pricing classiche
    vSection([
      hRow('100', [ hCol([
        tile('menuanchor', { anchor_id: 'prezzi' }),
        xKicker('Due edizioni', PRODUCT_HEX.build),
        xGap(12),
        xTitle('La gru cala il Pro', 'Pro', PRODUCT_HEX.build, '44'),
        xGap(30),
      ]) ]),
      hRow('50-50', [
        hCol([
          tile('pricing', {
            plan_name: 'OLObuild · Free', price: '0', currency: '€', period: 'per sempre · GPL · su WP.org',
            features: 'Oltre 100 tile nativi + form builder + dark mode\nAl livello dei builder Pro a pagamento della concorrenza\n11 effetti testo · 36 animazioni\nOLOlang gratis il primo anno',
            feature_dividers: true, cta_text: 'Scarica Free', cta_url: '/',
            bg_color: '#12151D', text_color: INK.paper,
            cta_bg_color: 'rgba(250,247,242,.1)', cta_text_color: INK.paper,
          }),
        ], '1-2'),
        hCol([
          tile('pricing', {
            plan_name: 'OLObuild · Pro', price: '29', currency: '€', period: 'prezzo lancio · poi €59/anno',
            features: 'L’intera libreria: 187 tile\nAnimazioni complete + ricerca media 8 provider\nOLOlang a vita · supporto prioritario\n30 giorni di rimborso, senza domande',
            feature_dividers: true, cta_text: 'Passa a Pro', cta_url: '/', is_popular: true,
            bg_color: '#12151D', text_color: INK.paper,
            cta_bg_color: PRODUCT_HEX.build, cta_text_color: INK.bg,
          }),
        ], '1-2'),
      ]),
    ], PRODUCT_HEX.build),
    // PROSSIME FERMATE — banner classici
    vSection([
      hRow('50-50', [
        hCol([
          tile('cta-banner', {
            headline: 'Approfondimento', headline_accent: 'tecnico.', headline_accent_italic: true,
            subtitle: 'Manuale base + scheda tecnica.',
            cta_text: 'Apri il manuale →', cta_url: P('olobuild-manuale'),
            bg: { type: 'solid', color: '#12151D' },
          }),
        ], '1-2'),
        hCol([
          tile('cta-banner', {
            headline: 'Prossima fermata', headline_accent: '', headline_accent_italic: false,
            subtitle: 'OLObooking: il motore che riempie l’agenda.',
            cta_text: 'OLObooking →', cta_url: P('olobooking'),
            bg: { type: 'solid', color: '#12151D' },
          }),
        ], '1-2'),
      ]),
    ], PRODUCT_HEX.build, { padding_top_custom: 40, padding_bottom_custom: 120 }),
  ],
};

/* ==================================================================== */
/* OLOBOOKING                                                            */
/* ==================================================================== */
const bookingPage = {
  title: 'OLObooking · Il tempo è tuo',
  slug: 'olobooking',
  content: [
    productHeroC({
      hex: PRODUCT_HEX.booking, color: 'booking', logo: 'olobooking', name: 'OLObooking',
      kicker: 'Prenotazioni · 6 verticali',
      title: 'Il tempo è tuo. Riempilo.', accent: 'Riempilo.',
      sub: '<p>Camere, tavoli, appuntamenti, eventi, noleggi, immobili: <strong>un solo motore</strong>, una sola configurazione, <strong>zero commissioni</strong> a piattaforme di mezzo.</p>',
      tags: ['6 verticali', 'anti no-show', 'QR access', '0% commissioni'],
      cta1: ['Vivi una giornata ↓', '#giornata'], cta2: ['I sei biglietti', '#verticali'],
      scene: 'hero-clock', sceneExtra: { clock_label: 'lo scroll muove le lancette' },
    }),
    marqueeSection(['check-in', 'tavolo 12', 'slot 15:30', 'biglietto QR', 'caparra', 'visita immobile', 'noleggio e-bike', 'conferma via mail'], '●'),
    // UNA GIORNATA — scena interattiva sticky dedicata (come l'assembler di build)
    bodySection([
      tile('menuanchor', { anchor_id: 'giornata' }),
      tile('oloxsticky', {
        accent: 'booking', variant: 'day', kicker: 'Una giornata col motore',
        day_label: 'agenda riempita', day_hint: 'scrolla per far passare le ore', day_stamp: 'Confermato',
        day_slots: [
          { hh: '09:00', what: 'Visita immobile, via Verdi 8', who: 'real estate' },
          { hh: '10:30', what: 'Consulenza fiscale, Studio B.', who: 'appuntamenti' },
          { hh: '12:00', what: 'Check-in camera Doppia Nord', who: 'accommodation' },
          { hh: '13:00', what: 'Tavolo 4, pranzo ×2', who: 'ristorante' },
          { hh: '15:30', what: 'Noleggio e-bike, 3 ore', who: 'rentals' },
          { hh: '17:00', what: 'Estetica, slot 45 min', who: 'appuntamenti' },
          { hh: '19:00', what: 'Workshop serale, 24 posti', who: 'eventi' },
          { hh: '20:30', what: 'Tavolo 12, cena ×6 (caparra)', who: 'ristorante' },
        ],
      }),
    ], PRODUCT_HEX.booking, { padding_top_custom: 0, padding_bottom_custom: 0 }),
    // I VERTICALI — 6 card classiche
    bodySection([
      tile('menuanchor', { anchor_id: 'verticali' }),
      ...secHead('I verticali', 'Sei biglietti, stesso motore', 'stesso motore', PRODUCT_HEX.booking,
        'Attivi il verticale e campi, calendari e flussi si riconfigurano da soli. Se cambi mestiere, i dati restano.'),
      xCards(PRODUCT_HEX.booking, 3, [
        ['Accommodation', 'Ospitalità', 'B&B, agriturismi, case-vacanza: calendario camere, tariffe stagionali, soggiorni minimi.', 'OLO-ACC-01'],
        ['Restaurants', 'Ristoranti', 'Tavoli, turni e menu, con caparra anti no-show per proteggere le serate piene.', 'OLO-RST-02'],
        ['Appointments', 'Appuntamenti', 'Studi, consulenza, estetica: slot orari, promemoria, gestione dello staff.', 'OLO-APP-03'],
        ['Events', 'Eventi', 'Conferenze, concerti, workshop: ticketing, posti numerati, accessi con QR.', 'OLO-EVT-04'],
        ['Rentals', 'Noleggi', 'Auto, bici, attrezzature, barche: inventario, cauzioni, contratti.', 'OLO-RNT-05'],
        ['Real estate', 'Immobiliare', 'Visite immobili su slot, agenzie, raccolta proposte.', 'OLO-EST-06'],
      ]),
    ], PRODUCT_HEX.booking),
    // INCASSI PROTETTI — statement classico con timbro pill
    bodySection([
      xKicker('Incassi protetti', PRODUCT_HEX.booking),
      xGap(12),
      xTitle('Il tavolo vuoto non paga più te', 'non paga più te', PRODUCT_HEX.booking, '48'),
      xGap(14),
      xText('<p>Prenotazione con <strong>caparra</strong>: chi non si presenta lascia qualcosa sul tavolo. E ogni prenotazione arriva <strong>senza commissioni</strong>: il canale diretto è davvero tuo.</p>'),
      xGap(24),
      xPill('No-show ◦ Coperto', PRODUCT_HEX.booking),
    ], PRODUCT_HEX.booking),
    followC(PRODUCT_HEX.booking),
    nextBannersC(PRODUCT_HEX.booking, [
      ['Approfondimento', 'tecnico.', 'Manuale base + scheda tecnica.', 'Apri il manuale →', P('olobooking-manuale')],
      ['Prossima fermata', '', 'OLOlang: lo stesso sito, 28 voci.', 'OLOlang →', P('ololang')],
    ]),
  ],
};

/* ==================================================================== */
/* OLOLANG                                                               */
/* ==================================================================== */
// Card del tabellone: flipcard nativa, fronte IT → retro tradotto.
const langFlip = (srcLabel, src, dstLabel, dst) => tile('flipcard', {
  front_title: src, front_description: srcLabel,
  back_title: dst, back_description: dstLabel,
  front_icon: '', back_icon: '',
  front_bg: '#12151D', back_bg: 'rgba(232,64,154,.16)',
  front_text_color: INK.paper, back_text_color: INK.paper,
  front_text_align: 'left', back_text_align: 'left',
  front_valign: 'center', back_valign: 'center',
  back_cta_text: '', back_cta_url: '',
  flip_direction: 'vertical', flip_trigger: 'hover', flip_duration: '600',
  card_height: '200', card_border_radius: '10', card_shadow: 'none',
  title_size: '19', title_weight: '600', desc_size: '11',
  tile_padding: { top: 22, right: 22, bottom: 22, left: 22 },
});
const langPage = {
  title: 'OLOlang · Di’ benvenuto in 28 modi',
  slug: 'ololang',
  content: [
    productHeroC({
      hex: PRODUCT_HEX.lang, color: 'lang', logo: 'ololang', name: 'OLOlang',
      kicker: 'Multilingua nativo · 28 lingue',
      title: 'Di’ «Benvenuto» in 28 modi.', accent: '«Benvenuto»',
      sub: '<p>DeepL + traduttore IA con <strong>glossario e memoria di traduzione</strong>. Contenuti, menu e stringhe tradotti <strong>via database</strong>: non patch fragili sul frontend.</p>',
      tags: ['28 lingue', 'DeepL + IA', 'via DB', 'a vita con Pro'],
      cta1: ['Guarda la lingua girare ↓', '#flip'], cta2: ['SEO multilingua', '#seo'],
      scene: 'hero-console', sceneExtra: {
        console_title: 'translator', console_sub: '· dashboard · batch in corso',
        console_rows: [
          { lc: 'EN', w: 100, pc: '' }, { lc: 'DE', w: 100, pc: '' }, { lc: 'FR', w: 96, pc: '' },
          { lc: 'ES', w: 92, pc: '' }, { lc: 'PT', w: 84, pc: '' }, { lc: 'NL', w: 78, pc: '' },
          { lc: 'JA', w: 64, pc: '' }, { lc: '+21', w: 52, pc: '…' },
        ],
      },
    }),
    marqueeSection(['Welcome', 'Willkommen', 'Bienvenue', 'Bienvenido', 'Bem-vindo', 'Welkom', 'Καλώς ήρθες', 'Добро пожаловать', 'ようこそ', '欢迎', 'Hoş geldin'], '·'),
    // TRADOTTO DAVVERO — tabellone di flipcard native (hover = la riga "gira")
    vSection([
      hRow('100', [ hCol([
        tile('menuanchor', { anchor_id: 'flip' }),
        ...secHead('Tradotto davvero', 'Ogni riga gira come un tabellone', 'gira', PRODUCT_HEX.lang,
          'Non solo i testi: menu, stringhe di tema e plugin, tutto passa dal database e torna fuori nella lingua giusta. Passa il mouse sulle schede.'),
      ]) ]),
      { ...hRow('33-33-33', [
        hCol([ langFlip('contenuto · it', 'Prenota il tuo soggiorno', 'content · en', 'Book your stay') ], '1-3'),
        hCol([ langFlip('menu · it', 'Chi siamo → Contatti', 'menü · de', 'Über uns → Kontakt') ], '1-3'),
        hCol([ langFlip('stringa plugin · it', '«Aggiungi al carrello»', 'chaîne · fr', '«Ajouter au panier»') ], '1-3'),
      ]), settings: { bg: { type: 'none' }, layout: '33-33-33', stack_mobile: true, gap: 24 } },
      { ...hRow('33-33-33', [
        hCol([ langFlip('glossario · it', 'OLObuild (non tradurre)', 'glossary · *', 'OLObuild ✓ protetto') ], '1-3'),
        hCol([ langFlip('memoria · it', '«Colazione inclusa», già tradotta', 'memory · es', '«Desayuno incluido» riusata, €0') ], '1-3'),
        hCol([], '1-3'),
      ]), settings: { bg: { type: 'none' }, layout: '33-33-33', stack_mobile: true, gap: 24 } },
    ], PRODUCT_HEX.lang),
    // SEO MULTILINGUA — lista descrittiva mono (URL → stato)
    bodySection([
      tile('menuanchor', { anchor_id: 'seo' }),
      ...secHead('SEO multilingua', 'Google vede 28 siti di prima classe', '28 siti di prima classe', PRODUCT_HEX.lang,
        'hreflang, URL localizzati, sitemap e meta per ogni lingua: nessuna versione è figlia di un dio minore.'),
      tile('desclist', {
        items: [
          ['https://tuosito.it/it/camere-vista-lago', 'indicizzata'],
          ['https://tuosito.it/en/lake-view-rooms', 'indexed'],
          ['https://tuosito.it/de/zimmer-mit-seeblick', 'indexiert'],
          ['<link rel="alternate" hreflang="en" …>', 'auto'],
          ['sitemap.xml, 28 varianti per pagina', 'auto'],
        ].map(([term, definition], i) => ({ id: `seo-${i}`, term, definition, icon: '' })),
        layout: 'stacked', show_icon: false, separator: true, striped: false,
        typography_preset: 'olox-mono',
        term_color: INK.paper, term_font_size: '14', term_font_weight: '600',
        definition_color: PRODUCT_HEX.lang, definition_font_size: '11',
        border_color: 'rgba(250,247,242,.12)', spacing: '14', text_align: 'left',
      }),
    ], PRODUCT_HEX.lang),
    // INCLUSO, NON VENDUTO DUE VOLTE — statement classico
    bodySection([
      xKicker('Incluso, non venduto due volte', PRODUCT_HEX.lang),
      xGap(12),
      xTitle('Gratis il 1° anno. A vita con OLObuild Pro.', 'A vita', PRODUCT_HEX.lang, '48'),
      xGap(14),
      xText('<p>Il multilingua è un diritto del sito, non un upsell. E traduce anche i flussi di OLObooking e i tile di OLObuild: un solo sistema di lingue per tutto.</p>'),
      xGap(24),
      xBtn('Prendilo con OLObuild', P('olobuild'), PRODUCT_HEX.lang),
    ], PRODUCT_HEX.lang),
    followC(PRODUCT_HEX.lang),
    nextBannersC(PRODUCT_HEX.lang, [
      ['Approfondimento', 'tecnico.', 'Manuale base + scheda tecnica.', 'Apri il manuale →', P('ololang-manuale')],
      ['Prossima fermata', '', 'OLOsecurity: un radar che non dorme mai.', 'OLOsecurity →', P('olosecurity')],
    ]),
  ],
};

/* ==================================================================== */
/* OLOSECURITY                                                           */
/* ==================================================================== */
const securPage = {
  title: 'OLOsecurity · Chi bussa male resta fuori',
  slug: 'olosecurity',
  content: [
    productHeroC({
      hex: PRODUCT_HEX.secur, color: 'secur', logo: 'olosecurity', name: 'OLOsecurity',
      kicker: 'Sicurezza · 100% locale',
      title: 'Chi bussa male, resta fuori', accent: 'resta fuori',
      sub: '<p>Firewall OWASP, 2FA, scanner anti-webshell e bonifica guidata dal pannello <strong>Sentinel</strong>. Tutto elaborato <strong>sul tuo server</strong>: il traffico non finisce in nessun cloud altrui.</p>',
      tags: ['100% locale', 'mini-WAF', 'TOTP 2FA', 'v1.2.0 · GPL'],
      cta1: ['Togli i sigilli ↓', '#difese'], cta2: ['Plugin Check 0/0', '#zerozero'],
      scene: 'hero-term', sceneExtra: {
        term_title: 'sentinel', term_sub: '· boot sequence',
        term_lines: [
          { cls: 'cy', text: '[sentinel] avvio pannello v1.2.0 …' },
          { cls: 'ok', text: '[waf]      regole OWASP caricate (4 famiglie)' },
          { cls: 'ok', text: '[geo]      blocco IPv4/IPv6 + rate limit ARMATO' },
          { cls: 'bad', text: '[waf]      SQLi da 185.220.•.•  → BLOCCATO' },
          { cls: 'ok', text: '[2fa]      TOTP attivo · codici recupero ok' },
          { cls: 'bad', text: '[bot]      finto Googlebot (FCrDNS) → RESPINTO' },
          { cls: 'ok', text: '[scan]     checksum core 100% · 0 webshell' },
          { cls: 'ok', text: '[cve]      feed firme sincronizzato' },
          { cls: 'cy', text: '[sentinel] tutto sotto controllo. resto in ascolto…' },
        ],
      },
    }),
    marqueeSection(['SQLi', 'XSS', 'path traversal', 'LFI/RCE', 'brute force', 'finti crawler', 'webshell', 'password compromesse', 'bot'], '✕'),
    // MENTRE LEGGEVI — contatore nativo + testo
    vSection([
      hRow('50-50', [
        hCol([
          xKicker('Mentre leggevi questa pagina', PRODUCT_HEX.secur),
          xGap(10),
          tile('counter', {
            number: '47', prefix: '', suffix: '', label: 'tentativi',
            icon_emoji: '', icon_size: '16',
            text_color: PRODUCT_HEX.secur, number_font_size: '110', number_font_weight: '400',
            label_color: INK.faint, label_font_size: '13', label_font_weight: '600',
            typography_preset: 'olox-serif',
            tile_padding: { top: 0, right: 0, bottom: 0, left: 0 },
            bg: { type: 'none' }, media_bg: { type: 'none' }, shadow: 'none',
          }),
        ], '1-2'),
        {
          id: randomUUID(), type: 'column',
          settings: { bg: { type: 'none' }, width_medium: '1-2', flex_direction: 'column', flex_justify: 'center' },
          style: [], advanced: [],
          children: [
            xText('<p>bloccati da un WordPress medio esposto in rete. Non serve essere famosi per essere un bersaglio: basta essere online.</p>', '17'),
          ],
        },
      ]),
    ], PRODUCT_HEX.secur),
    // QUATTRO LINEE DI DIFESA — 4 card classiche
    bodySection([
      tile('menuanchor', { anchor_id: 'difese' }),
      ...secHead('Il pannello Sentinel', 'Quattro linee di difesa, declassificate', 'declassificate', PRODUCT_HEX.secur,
        'Otto schede operative, queste sono le quattro che fanno la differenza.'),
      xCards(PRODUCT_HEX.secur, 2, [
        ['01 · Prevenzione', 'Firewall · mini-WAF', 'Regole OWASP per famiglia (SQLi, XSS, traversal, LFI/RCE), reputazione IP, rate limiting, geo-blocco IPv4/IPv6.'],
        ['02 · Identità', 'Accessi & 2FA', 'Anti brute-force, TOTP con codici di recupero, password compromesse (HIBP, k-anonymity), CAPTCHA, anti finti crawler con verifica FCrDNS.'],
        ['03 · Rilevamento', 'Scanner', 'Integrità di core, plugin e temi via checksum; scansione profonda anti-webshell; feed CVE e firme malware aggiornate.'],
        ['04 · Reazione', 'Ripristino', 'Bonifica guidata post-attacco, quarantena reversibile, rigenerazione dei salt, report d’incidente pronto da consegnare.'],
      ]),
    ], PRODUCT_HEX.secur),
    // 0/0 — statement classico con cifra gigante
    bodySection([
      tile('menuanchor', { anchor_id: 'zerozero' }),
      xKicker('Trasparenza', PRODUCT_HEX.secur),
      xGap(8),
      xTitle('0/0', '0/0', PRODUCT_HEX.secur, '120', 'p'),
      xGap(8),
      xTitle('WP Plugin Check: zero errori, zero warning', 'zero warning', PRODUCT_HEX.secur, '44'),
      xGap(14),
      xText('<p>Codice GPL che puoi leggere riga per riga. Il contrario dei security-in-cloud che mandano il tuo traffico nei loro datacenter: qui analisi, firme e log restano <strong>a casa tua</strong>. GDPR semplice.</p>'),
    ], PRODUCT_HEX.secur),
    followC(PRODUCT_HEX.secur),
    nextBannersC(PRODUCT_HEX.secur, [
      ['Approfondimento', 'tecnico.', 'Manuale base + scheda tecnica.', 'Apri il manuale →', P('olosecurity-manuale')],
      ['Prossima fermata', '', 'OLOtour: guarda dentro, prima di entrare.', 'OLOtour →', P('olotour')],
    ]),
  ],
};

/* ==================================================================== */
/* OLOTOUR                                                               */
/* ==================================================================== */
const tourPage = {
  title: 'OLOtour · Questa pagina gira a 360°',
  slug: 'olotour',
  content: [
    productHeroC({
      hex: PRODUCT_HEX.tour, color: 'tour', logo: 'olotour', name: 'OLOtour',
      kicker: 'Tour virtuali · in arrivo',
      title: 'Guarda dentro, prima di entrare', accent: 'prima di entrare',
      sub: '<p>Affacciati all’oblò e trascinalo: è quello che faranno i tuoi visitatori nei tuoi spazi. Panorami sferici, <strong>hot-spot cliccabili</strong>, ambienti collegati, anche in <strong>VR</strong>.</p>',
      tags: ['360°', 'Polyhaven · Street View', 'multi-stanza', 'VR ready'],
      cta1: ['Percorri le stanze ↓', '#stanze'], cta2: ['Le fondamenta', '#hotspot'],
      scene: 'hero-porthole',
    }),
    // MULTI-STANZA — 4 card classiche
    bodySection([
      tile('menuanchor', { anchor_id: 'stanze' }),
      ...secHead('Multi-stanza', 'Gli ambienti si collegano', 'collegano', PRODUCT_HEX.tour,
        'Ogni panorama porta al successivo: il visitatore cammina nel sito come camminerebbe da te.'),
      xCards(PRODUCT_HEX.tour, 4, [
        ['scena 01', 'Ingresso', 'Panorama di benvenuto, hot-spot verso la reception e le camere.'],
        ['scena 02', 'Camera vista lago', 'Foto sferica reale, punti informativi su letto, vista, servizi.'],
        ['scena 03', 'Terrazza', 'Video 360° al tramonto, il momento che vende la notte.'],
        ['uscita', '→ Prenota', 'Il tour finisce dove deve: sul bottone di OLObooking.'],
      ]),
    ], PRODUCT_HEX.tour),
    // LE FONDAMENTA — 3 card + chiusura con CTA
    bodySection([
      tile('menuanchor', { anchor_id: 'hotspot' }),
      ...secHead('Test finali prelancio', 'Le fondamenta di OLOtour', 'OLOtour', PRODUCT_HEX.tour, ''),
      xCards(PRODUCT_HEX.tour, 3, [
        ['01', 'Panorami & HDRI', 'Librerie Polyhaven e Google Street View integrate: parti da panorami professionali o dai tuoi scatti sferici.'],
        ['02', 'Hot-spot cliccabili', 'Punti interattivi con testo, immagini e link tra ambienti: il visitatore esplora, tu racconti.'],
        ['03', '3D, splat & VR', 'Scene 3D e gaussian splat, fruizione con visore: l’immersione non è un embed di terzi, vive nel tuo WordPress.'],
      ]),
      xGap(28),
      xText('<p>Un assaggio esiste già: il tile <strong>Viewer 360°</strong> della famiglia Interactive di OLObuild. OLOtour lo porta al livello successivo, senza piattaforme esterne a canone né branding altrui sui tuoi spazi.</p>'),
      xGap(20),
      xBtnGhost('Prova il Viewer 360° in OLObuild', P('olobuild')),
    ], PRODUCT_HEX.tour),
    followC(PRODUCT_HEX.tour),
    nextBannersC(PRODUCT_HEX.tour, [
      ['Approfondimento', 'tecnico.', 'Manuale base + scheda tecnica.', 'Apri il manuale →', P('olotour-manuale')],
      ['Prossima fermata', '', 'OLOtutor: sali di livello, lezione dopo lezione.', 'OLOtutor →', P('olotutor')],
    ]),
  ],
};

/* ==================================================================== */
/* OLOTUTOR                                                              */
/* ==================================================================== */
const tutorPage = {
  title: 'OLOtutor · Questa pagina è un corso',
  slug: 'olotutor',
  content: [
    productHeroC({
      hex: PRODUCT_HEX.tutor, color: 'tutor', logo: 'olotutor', name: 'OLOtutor',
      kicker: 'Formazione · in arrivo',
      title: 'Questa pagina è un corso', accent: 'un corso',
      sub: '<p>Scendi e sblocca le lezioni: è la logica di OLOtutor. Corsi, lezioni, quiz, punti, badge e certificati, <strong>dentro il tuo WordPress</strong>, non su un marketplace che ti mette in fila coi concorrenti.</p>',
      tags: ['LMS', 'quiz & badge', 'registro voti', 'certificati'],
      cta1: ['Sblocca le lezioni ↓', '#lezioni'], cta2: ['Fai il quiz', '#quiz'],
      scene: 'hero-medal', sceneExtra: { medal_top: 'livello', medal_big: '1', medal_bot: 'studente' },
    }),
    marqueeSection(['+120 xp', 'quiz superato', 'badge sbloccato', 'lezione 4/12', 'certificato pronto', 'registro aggiornato', 'streak 7 giorni'], '★'),
    // IL PERCORSO — minigioco lezioni-che-si-sbloccano (tile dedicata)
    bodySection([
      tile('menuanchor', { anchor_id: 'lezioni' }),
      tile('oloxlessons', {
        accent: 'tutor',
        kicker: 'Il percorso', title_html: 'Le lezioni si <em>sbloccano</em> scendendo',
        lock_text: 'scendi per sbloccare',
        items: [
          { xp: '+120 xp', title: 'Corsi &amp; lezioni', text_html: 'Strutture di corso, lezioni ordinate, area allievi con i progressi di ciascuno. Il programma lo detti tu.' },
          { xp: '+180 xp', title: 'Quiz &amp; gamification', text_html: 'Quiz, mini-giochi, punti e badge. La motivazione fa parte del metodo, non è un plugin in più.' },
          { xp: '+90 xp', title: 'Registro &amp; certificati', text_html: 'Registro voti e certificati di completamento: quello che serve a scuole, accademie e formatori.' },
          { xp: '+150 xp', title: 'Gli allievi restano tuoi', text_html: 'Iscrizioni, dati e pagamenti sul tuo sito. Con OLObooking le lezioni individuali si prenotano su slot; con OLOlang i corsi parlano 28 lingue.' },
        ],
      }),
    ], PRODUCT_HEX.tutor, { padding_top_custom: 0, padding_bottom_custom: 0 }),
    // VERIFICA FINALE — minigioco quiz (tile dedicata)
    bodySection([
      tile('menuanchor', { anchor_id: 'quiz' }),
      tile('oloxquiz', {
        accent: 'tutor',
        kicker: 'Verifica finale', title_html: 'Un quiz <em>vero</em>, provalo',
        question_html: 'Dove vivono i tuoi corsi con <em>OLOtutor</em>?',
        answers: [
          { text: 'Su un marketplace, in fila coi concorrenti', ok: false },
          { text: 'Sul mio WordPress, con i miei allievi e i miei dati', ok: true },
          { text: 'In un cloud di terzi, a canone mensile', ok: false },
        ],
        hint: 'rispondi per guadagnare +90 xp',
        ok_html: 'esatto · <b>+90 xp</b> · badge sbloccato',
        ko_text: 'mmh, riprova: la risposta è nel nome della suite…',
        bonus: 90,
      }),
    ], PRODUCT_HEX.tutor, { padding_top_custom: 0, padding_bottom_custom: 0 }),
    followC(PRODUCT_HEX.tutor),
    nextBannersC(PRODUCT_HEX.tutor, [
      ['Approfondimento', 'tecnico.', 'Manuale base + scheda tecnica.', 'Apri il manuale →', P('olotutor-manuale')],
      ['Fine del percorso', '', 'Torna al viaggio: ogni fermata è un prodotto.', 'Torna al viaggio →', HOME_URL],
    ]),
  ],
};

/* ==================================================================== */
/* MANUALI                                                               */
/* ==================================================================== */
// Manuale CLASSICO: testata + corpo a due colonne (toc sticky | capitoli),
// scheda tecnica su desclist nativa, CTA in coda. Nessuna tile dedicata.
const manual = (slug, prodSlug, active, accent, logoName, docCode, extraDoc, subHtml, chapters, spec) => {
  const hex = PRODUCT_HEX[accent];
  const chapC = (ch) => {
    const { text, accent: acc } = emSplit(ch.title_html);
    return [
      tile('menuanchor', { anchor_id: ch.anchor }),
      xMono(ch.no, { color: hex, size: '12' }),
      xGap(8),
      xTitle(text, acc, hex, '34'),
      xGap(14),
      xText(mdBody(ch.body_html), '15'),
      xGap(44),
    ];
  };
  return {
    title: `Manuale ${logoName} · OLOtheme`,
    slug,
    content: [
      // TESTATA
      bodySection([
        tile('pagelight', { light_color: hex, base_color: INK.bg, position: 'top-right', size: 95, intensity: 22, transition_ms: 800 }),
        xMono(`doc ${docCode} · manuale base · + scheda tecnica${extraDoc ? ` · ${extraDoc}` : ''}`, { color: INK.faint, size: '10', ls: '2.5' }),
        xGap(20),
        xLogo(logoName, logoName),
        xGap(18),
        xTitle('Manuale base', 'base', hex, '56', 'h1'),
        xGap(12),
        xText(`<p>${subHtml}</p>`, '16'),
      ], hex, { padding_top_custom: 130, padding_bottom_custom: 30 }),
      // CORPO: sommario sticky a sinistra, capitoli + scheda tecnica a destra
      vSection([
        hRow('25-75', [
          hCol([
            tile('toc', {
              title: 'Sommario', max_depth: '2', list_style: 'numbered',
              sticky: true, highlight_active: true, smooth_scroll: true,
              typography_preset: 'olox-mono', font_size: '12',
              text_color: INK.faint, link_color: INK.dim, title_color: INK.paper,
            }),
          ], '1-4'),
          hCol([
            ...chapters.flatMap(chapC),
            // SCHEDA TECNICA
            tile('menuanchor', { anchor_id: 'spec' }),
            xTitle('Scheda tecnica', 'tecnica', hex, '40'),
            xGap(10),
            xMono(`${spec.name} · ${stripTags(spec.sub)}`, { color: hex, size: '11' }),
            xGap(22),
            tile('desclist', {
              items: spec.rows.map(([term, d], i) => ({ id: `sp-${i}`, term, definition: stripTags(d), icon: '' })),
              layout: 'stacked', show_icon: false, separator: true, striped: false,
              term_color: INK.paper, term_font_size: '14', term_font_weight: '700',
              definition_color: INK.dim, definition_font_size: '14',
              border_color: 'rgba(250,247,242,.12)', spacing: '14', text_align: 'left',
            }),
          ], '3-4'),
        ]),
        hRow('25-75', [
          hCol([], '1-4'),
          hColRow([
            xBtn('← Torna alla scheda prodotto', P(prodSlug), hex),
            xBtnGhost(spec.cta2 || 'Il viaggio OLOtheme', spec.url2 || HOME_URL),
          ], '3-4'),
        ]),
      ], hex, { padding_top_custom: 20, padding_bottom_custom: 120 }),
    ],
  };
};

const manBuild = manual('olobuild-manuale', 'olobuild', 'build', 'build', 'olobuild', 'OLO-BLD-M01', '',
  'Cos’è OLObuild, come è costruito e perché regge 187 tile con un motore solo. Cinque capitoli, poi la scheda tecnica.',
  [
    { anchor: 'c1', no: '§1', title_html: 'Cos’è <em>OLObuild</em>', body_html: '<p>OLObuild è il <strong>telaio</strong> della suite: un page builder per WordPress in cui ogni elemento della pagina è un <strong>tile</strong>: un blocco autonomo con i propri campi, le proprie animazioni e il proprio render. La libreria conta <strong>187 tile in 12 famiglie</strong> (oltre 100 già nella versione Free), tutti sullo stesso motore.</p><p>Non è un tema con degli shortcode né un builder generico con add-on a pagamento: animazioni, effetti testo, hover e parallax fanno parte di ogni tile <strong>di serie</strong>. È costruito come un’app moderna, non come un tema.</p>' },
    { anchor: 'c2', no: '§2', title_html: 'Architettura <em>tile-based</em>', body_html: '<p>Ogni tile è una classe PHP <code>Olo_&lt;Name&gt;_Tile</code> che eredita da <code>Olo_Tile_Base</code> e renderizza <strong>server-side</strong>: l’HTML esce già pronto, senza generazione a runtime nel browser.</p><ul class="dash"><li><strong>Auto-discovery</strong>: i tile si registrano da soli via <code>import.meta.glob</code>: aggiungere un tile significa creare <strong>due file</strong> (config JS del pannello + classe PHP). Niente compilazione, niente shortcode.</li><li><strong>Helper centralizzati</strong>: <code>Olo_Tile_Utils</code> e <code>Olo_Text_Effects</code> tengono il codice DRY su tutti i 187 tile.</li><li><strong>Scoped styles</strong>: ogni istanza ha un UID univoco (<code>olo-XXX-12345</code>): il CSS resta confinato, nessun leak fra istanze.</li><li><strong>Per-instance hover</strong>: le classi UID risolvono il classico bug “il primo elemento eredita gli hover dell’ultimo”.</li></ul><div class="notice"><span class="nl">Eredità gratuita</span><p>Ogni tile eredita dal telaio animazioni d’ingresso, effetti testo, hover, visibility condizionale e responsive su 6 breakpoint, senza scrivere una riga in più.</p></div>' },
    { anchor: 'c3', no: '§3', title_html: 'Dentro il <em>builder</em>', body_html: '<p>L’editor visuale è un’app <strong>Vue 3</strong> (Composition API) con store <strong>Pinia</strong> e build <strong>Vite 5</strong>. Il testo si modifica inline con <strong>Tiptap</strong> (toolbar flottante), il drag &amp; drop usa <strong>Pragmatic drag-and-drop</strong>: 5 kb contro gli 80 kb delle alternative.</p><ul class="dash"><li><strong>REST API</strong> con namespace pulito <code>olo/v1</code>.</li><li><strong>Due tabelle dedicate</strong>: <code>wp_olo_templates</code> e <code>wp_olo_revisions</code>: niente abuso di postmeta, revisioni vere.</li><li><strong>Ricerca media integrata</strong>: foto e video da 8 provider senza uscire dal builder (edizione Pro).</li></ul>' },
    { anchor: 'c4', no: '§4', title_html: 'Performance per <em>default</em>', body_html: '<p>Niente plugin di cache aggressivi né “ottimizzatori” esterni: la velocità è scritta nell’architettura.</p><ul class="dash"><li><strong>IntersectionObserver per tutto</strong>: entrance animations, text-effects, parallax: zero scroll listener.</li><li><strong>Video facade</strong>: YouTube/Vimeo mostrano il poster; l’iframe pesante carica solo al click.</li><li><strong>Lazy-load nativo</strong> del browser su immagini, video e gallerie.</li><li><strong>Critical CSS inline</strong> sui blocchi above-the-fold; il resto carica async.</li><li><strong>CSS scoped per istanza</strong>: nessuna regola globale che cade a cascata sulla pagina.</li></ul>' },
    { anchor: 'c5', no: '§5', title_html: 'Le due <em>edizioni</em>', body_html: '<table class="dtab"><tbody><tr><td class="f">Free<span>per sempre · GPL</span></td><td><strong>Oltre 100 tile nativi</strong>, form builder, dark mode, 11 effetti testo, 36 animazioni, un corredo al livello dei builder Pro a pagamento della concorrenza. OLOlang incluso il primo anno. Distribuita su WordPress.org.</td></tr><tr><td class="f">Pro<span>€29 lancio · poi €59/anno</span></td><td>L’intera libreria (<strong>187 tile</strong>), animazioni complete, ricerca media da 8 provider, <strong>OLOlang a vita</strong>, supporto prioritario, 30 giorni di rimborso.</td></tr></tbody></table>' },
  ],
  {
    name: 'OLObuild', sub: 'page builder · GPL',
    rows: [
      ['Tipo', 'Page builder WordPress tile-based, render server-side'],
      ['Requisiti', 'WordPress 5.8+ · PHP 7.4+'],
      ['Frontend builder', 'Vue 3 (Composition API) + Pinia · build Vite 5'],
      ['Stile', 'SASS + Tailwind (prefix <code>mb-</code>) + UIkit'],
      ['Editor inline', 'Tiptap, rich text con toolbar flottante'],
      ['Drag &amp; drop', '@atlaskit/pragmatic-drag-and-drop (~5 kb)'],
      ['REST API', 'Namespace <code>olo/v1</code>'],
      ['Database', 'Tabelle dedicate <code>wp_olo_templates</code> · <code>wp_olo_revisions</code>'],
      ['Libreria', '187 tile · 12 famiglie · auto-discovery (2 file per tile)'],
      ['Animazioni', 'CSS keyframes + IntersectionObserver · 36 animazioni · 11 effetti testo'],
      ['Responsive', '6 breakpoint ereditati da ogni tile'],
      ['Licenza', 'GPL, codice aperto, nessun blob crittografato'],
    ],
  });

const manBooking = manual('olobooking-manuale', 'olobooking', 'booking', 'booking', 'olobooking', 'OLO-BKG-M01', '',
  'Un motore di prenotazione, sei verticali: come funziona, come si configura, come viaggia una prenotazione dall’inizio alla fine.',
  [
    { anchor: 'c1', no: '§1', title_html: 'Cos’è <em>OLObooking</em>', body_html: '<p>OLObooking è il motore di prenotazione della suite: un solo plugin che, con <strong>una sola configurazione</strong>, diventa il verticale che serve, camere, tavoli, appuntamenti, biglietti, noleggi o visite immobiliari.</p><p>La differenza rispetto al mercato: gli altri vendono <strong>un plugin per mestiere</strong>, ognuno con la sua interfaccia e i suoi dati. Qui il cuore è unico; cambiando verticale cambiano campi, calendari e flussi, <strong>i dati restano</strong>.</p>' },
    { anchor: 'c2', no: '§2', title_html: 'Il <em>motore</em>', body_html: '<p>Alla base ci sono tre concetti, uguali per tutti i verticali:</p><ul class="dash"><li><strong>Risorsa</strong>: la cosa che si prenota: una camera, un tavolo, un operatore, un posto, un mezzo, un immobile.</li><li><strong>Disponibilità</strong>: calendario e slot della risorsa: notti, turni, fasce orarie, date evento.</li><li><strong>Regole</strong>: vincoli e prezzi: soggiorno minimo, capienza, tariffe stagionali, caparre, cauzioni.</li></ul><p>Il verticale scelto pre-configura questi tre livelli con i campi e i termini del mestiere. Tutto vive <strong>sul tuo hosting</strong>: nessuna piattaforma di mezzo, nessuna commissione per prenotazione.</p>' },
    { anchor: 'c3', no: '§3', title_html: 'I sei <em>verticali</em>', body_html: '<table class="dtab"><tbody><tr><td class="f">Accommodation</td><td>B&amp;B, agriturismi, case-vacanza, calendario camere, tariffe stagionali, soggiorni minimi, check-in/out.</td></tr><tr><td class="f">Restaurants</td><td>Tavoli, turni e menu, con <strong>caparra anti no-show</strong> sulle prenotazioni a rischio.</td></tr><tr><td class="f">Appointments</td><td>Studi, consulenza, estetica, slot orari per operatore, promemoria, gestione staff.</td></tr><tr><td class="f">Events</td><td>Conferenze, concerti, workshop, ticketing, posti numerati, accessi con QR.</td></tr><tr><td class="f">Rentals</td><td>Auto, bici, attrezzature, barche, inventario, cauzioni, contratti.</td></tr><tr><td class="f">Real estate</td><td>Visite immobili su slot, agenzie, raccolta proposte.</td></tr></tbody></table>' },
    { anchor: 'c4', no: '§4', title_html: 'Il <em>flusso</em>, dall’inizio alla fine', body_html: '<ul class="dash"><li><strong>1 · Richiesta</strong>: il visitatore sceglie risorsa e data dai tile booking sul sito.</li><li><strong>2 · Verifica</strong>: il motore controlla disponibilità e regole in tempo reale.</li><li><strong>3 · Garanzia</strong>: dove previsto, caparra o cauzione bloccano la prenotazione seria.</li><li><strong>4 · Conferma</strong>: notifica a cliente e gestore; la risorsa esce dal calendario.</li><li><strong>5 · Promemoria</strong>: comunicazioni automatiche a ridosso della data.</li><li><strong>6 · Arrivo</strong>: check-in; per gli eventi, accesso con QR.</li></ul><div class="notice"><span class="nl">Anti no-show</span><p>La caparra trasforma la prenotazione da promessa a impegno: chi non si presenta lascia qualcosa sul tavolo, e la serata piena resta piena.</p></div>' },
    { anchor: 'c5', no: '§5', title_html: 'Nella <em>suite</em>', body_html: '<ul class="dash"><li><strong>OLObuild</strong>: 22 tile booking già pronti nel builder: calendario disponibilità, picker, slot orari, reception. Sito e motore parlano la stessa lingua.</li><li><strong>OLOlang</strong>: flussi di prenotazione tradotti in 28 lingue per la clientela internazionale.</li><li><strong>OLOtour</strong>: il tour 360° finisce dove deve: sul bottone “prenota”.</li></ul>' },
  ],
  {
    name: 'OLObooking', sub: 'motore prenotazioni · GPL',
    rows: [
      ['Tipo', 'Plugin di prenotazione WordPress multi-verticale'],
      ['Requisiti', 'WordPress 5.9+ · PHP 7.4+'],
      ['Verticali', '6, accommodation, restaurants, appointments, events, rentals, real estate (una configurazione)'],
      ['Modello dati', 'Risorse · disponibilità · regole, condiviso tra i verticali'],
      ['Garanzie', 'Caparra anti no-show, cauzioni sui noleggi'],
      ['Accessi evento', 'Biglietti con QR code, posti numerati'],
      ['Notifiche', 'Conferme e promemoria automatici a cliente e gestore'],
      ['Frontend', '22 tile dedicati nella famiglia Booking di OLObuild'],
      ['Commissioni', 'Zero, canale diretto sul proprio hosting'],
      ['Multilingua', 'Flussi traducibili in 28 lingue via OLOlang'],
      ['Licenza', 'GPL, dati e prenotazioni restano sul proprio server'],
    ],
  });

const manLang = manual('ololang-manuale', 'ololang', 'lang', 'lang', 'ololang', 'OLO-LNG-M01', '',
  'Come OLOlang traduce davvero un sito WordPress, dal database alla SEO, e come si lavora nella dashboard translator.',
  [
    { anchor: 'c1', no: '§1', title_html: 'Cos’è <em>OLOlang</em>', body_html: '<p>OLOlang è il sistema multilingua <strong>nativo</strong> della suite: porta un sito WordPress in <strong>28 lingue</strong> senza plugin terzi e senza patch fragili sul frontend. È incluso gratis il primo anno con OLObuild Free e <strong>a vita</strong> con OLObuild Pro.</p><p>“Nativo” significa che la traduzione non è uno strato appiccicato sopra: passa dal <strong>database</strong>, copre contenuti, menu e stringhe, e produce URL e metadati di prima classe per ogni lingua.</p>' },
    { anchor: 'c2', no: '§2', title_html: 'Come <em>traduce</em>', body_html: '<ul class="dash"><li><strong>Contenuti</strong>: pagine, articoli, tile OLObuild: ogni lingua ha la sua versione, salvata nel DB.</li><li><strong>Menu</strong>: le voci di navigazione seguono la lingua attiva, senza duplicare i menu a mano.</li><li><strong>Stringhe</strong>: testi di tema e plugin (bottoni, etichette, messaggi) tradotti via database.</li></ul><div class="notice"><span class="nl">Perché via DB</span><p>Le soluzioni che riscrivono l’HTML al volo si rompono a ogni cambio di tema. Le traduzioni nel database sopravvivono: sono contenuto, non trucco.</p></div>' },
    { anchor: 'c3', no: '§3', title_html: 'I <em>motori</em>', body_html: '<ul class="dash"><li><strong>DeepL</strong>: traduzione automatica di qualità per le lingue coperte.</li><li><strong>Traduttore IA</strong>: per raffinare, adattare il tono, coprire le lingue restanti.</li><li><strong>Glossario</strong>: i tuoi termini (brand, prodotti, tecnicismi) restano tuoi in ogni lingua: “OLObuild” non diventa mai altro.</li><li><strong>Memoria di traduzione</strong>: una frase già tradotta si riusa: coerenza garantita e costi che scendono col tempo.</li></ul>' },
    { anchor: 'c4', no: '§4', title_html: 'Il <em>workflow</em>', body_html: '<p>La <strong>dashboard translator</strong> è la vista di chi traduce: avanzamento per lingua, stringhe mancanti, coda di revisione. Il flusso tipico:</p><ul class="dash"><li><strong>1 · Batch automatico</strong>: DeepL/IA traducono in blocco i contenuti nella lingua scelta.</li><li><strong>2 · Revisione umana</strong>: il translator scorre le voci, corregge, approva.</li><li><strong>3 · Pubblicazione</strong>: la lingua va online con URL, menu e SEO già a posto.</li></ul>' },
    { anchor: 'c5', no: '§5', title_html: 'SEO <em>multilingua</em>', body_html: '<ul class="dash"><li><strong>hreflang</strong>: ogni pagina dichiara le sue varianti: Google indirizza l’utente alla lingua giusta.</li><li><strong>URL localizzati</strong>: <code>/it/camere-vista-lago</code>, <code>/en/lake-view-rooms</code>, <code>/de/zimmer-mit-seeblick</code>.</li><li><strong>Sitemap</strong>: tutte le varianti in sitemap, meta e title per lingua.</li></ul><p>Nessuna versione è figlia di un dio minore: per i motori di ricerca ogni lingua è una pagina di prima classe.</p>' },
  ],
  {
    name: 'OLOlang', sub: 'multilingua · incluso con OLObuild',
    rows: [
      ['Tipo', 'Sistema multilingua nativo per WordPress'],
      ['Requisiti', 'WordPress 5.9+ · PHP 7.4+ · OLObuild'],
      ['Lingue', '28'],
      ['Motori', 'DeepL + traduttore IA'],
      ['Qualità', 'Glossario termini protetti · memoria di traduzione'],
      ['Ambito', 'Contenuti, menu, stringhe di tema e plugin, via database'],
      ['Workflow', 'Dashboard translator: batch automatico → revisione → pubblicazione'],
      ['SEO', 'hreflang, URL localizzati, sitemap e meta per lingua'],
      ['Suite', 'Traduce anche i flussi OLObooking e i tile OLObuild'],
      ['Costo', 'Gratis il 1° anno (Free) · a vita con OLObuild Pro'],
    ],
  });

const manSecur = manual('olosecurity-manuale', 'olosecurity', 'secur', 'secur', 'olosecurity', 'OLO-SEC-M01', 'v1.2.0',
  'Il pannello Sentinel e le quattro linee di difesa: prevenzione, identità, rilevamento, reazione. Tutto elaborato in locale.',
  [
    { anchor: 'c1', no: '§1', title_html: 'Cos’è <em>OLOsecurity</em>', body_html: '<p>OLOsecurity è un plugin di sicurezza WordPress <strong>all-in-one</strong>: firewall, antivirus, gestione accessi con 2FA, audit e bonifica in un unico pannello, <strong>Sentinel</strong>, 8 schede operative.</p><p>La scelta di fondo: <strong>elaborazione 100% locale</strong>. Analisi, firme e log restano sul tuo server; nessun traffico viene spedito a cloud terzi per essere “analizzato”. Nessun dataset di terzi è impacchettato nel plugin: i feed si scaricano on-demand dal tuo sito. GDPR semplice.</p><div class="notice"><span class="nl">Conformità</span><p>Codice GPL, verificabile riga per riga. <strong>WP Plugin Check: 0 errori / 0 warning.</strong></p></div>' },
    { anchor: 'c2', no: '§2', title_html: 'Firewall · <em>mini-WAF</em>', body_html: '<ul class="dash"><li><strong>Regole per famiglia OWASP</strong>: SQL injection, XSS, path traversal, LFI/RCE, attivabili a gruppi.</li><li><strong>Reputazione IP</strong> da liste pubbliche + regole di blocco avanzate.</li><li><strong>Rate limiting</strong> sulle richieste anomale.</li><li><strong>Geo-blocco</strong> IPv4 e IPv6 per paese.</li><li><strong>Proxy fidati</strong>: riconoscimento di Cloudflare e reti locali per valutare correttamente <code>X-Forwarded-For</code>.</li></ul>' },
    { anchor: 'c3', no: '§3', title_html: 'Accessi &amp; <em>2FA</em>', body_html: '<ul class="dash"><li><strong>Anti brute-force</strong>: limite tentativi, lockout temporizzato, auto-blocklist permanente per IP recidivi, allowlist/blocklist.</li><li><strong>2FA TOTP</strong>: setup con QR, codici di recupero, fallback email, reset admin.</li><li><strong>Password compromesse</strong>: verifica su HIBP con k-anonymity: la password non lascia mai il server.</li><li><strong>CAPTCHA</strong> e blocco enumerazione utenti.</li><li><strong>Anti finti crawler</strong>: verifica FCrDNS: chi si spaccia per Googlebot viene smascherato.</li><li><strong>Hardening opzionale</strong>: XML-RPC, security header, occultamento versione, <code>DISALLOW_FILE_EDIT</code>, blocco upload PHP, protezione .htaccess.</li></ul>' },
    { anchor: 'c4', no: '§4', title_html: 'Scanner · <em>tre passaggi</em>', body_html: '<ul class="dash"><li><strong>Integrità / checksum</strong>: baseline trust-on-first-use; confronto di core, plugin e temi con wordpress.org; rileva i file modificati.</li><li><strong>Euristica profonda</strong>: scorre i file PHP (salta i non eseguibili) con 5 euristiche forti calibrate anti falsi-positivi; la doppia estensione mascherata (es. <code>.jpg.php</code>) è trattata come webshell quasi certa.</li><li><strong>Firme malware</strong>: passaggio per hash MD5/SHA256 sul feed firme: lookup O(1), costo trascurabile.</li></ul><p>Prestazioni: <strong>~11.000 file PHP in ~2–3 secondi</strong> su un sito tipico; sugli hosting con timeout, modalità a blocchi con ripresa. Feed CVE per plugin e temi installati, aggiornamento giornaliero via cron, indici con autoload OFF.</p>' },
    { anchor: 'c5', no: '§5', title_html: 'Ripristino &amp; <em>registro</em>', body_html: '<ul class="dash"><li><strong>Bonifica guidata</strong> post-attacco: riparazione core one-click dalla versione ufficiale, reinstallazione plugin/temi da wordpress.org con backup automatico.</li><li><strong>Quarantena reversibile</strong> dei file sospetti.</li><li><strong>Rigenerazione salt</strong> e report d’incidente pronto da consegnare.</li><li><strong>Audit log</strong> “chi/cosa/quando” su tabella dedicata, filtrabile, export CSV, conservazione 90 giorni.</li><li><strong>Monitoraggio</strong>: traffico in tempo reale, grafico eventi, monitor spazio disco, digest email, webhook.</li></ul>' },
  ],
  {
    name: 'OLOsecurity v1.2.0', sub: 'GPLv2 or later · Plugin Check 0/0',
    rows: [
      ['Tipo', 'Plugin di sicurezza WordPress all-in-one · pannello Sentinel, 8 schede'],
      ['Requisiti', 'WordPress 5.9+ (testato fino a WP 7.0) · PHP 7.4+'],
      ['Elaborazione', '100% locale, nessun traffico verso cloud terzi'],
      ['Firewall', 'Mini-WAF: regole OWASP per famiglia, reputazione IP, rate limiting, geo-blocco IPv4/IPv6'],
      ['Accessi', 'Anti brute-force, 2FA TOTP + recupero, HIBP k-anonymity, CAPTCHA, FCrDNS'],
      ['Scanner', 'Checksum vs wordpress.org · 5 euristiche anti-webshell · hash MD5/SHA256 O(1)'],
      ['Prestazioni', '~11.000 file PHP in ~2–3 s · modalità a blocchi con ripresa'],
      ['Feed', 'maldet/rfxn.com, InterServer (~76.000 firme) · OpenPhish + URLhaus (~30.000 URL) · api.wordpress.org (checksum, CVE) · cron giornaliero'],
      ['Ripristino', 'Bonifica guidata, quarantena reversibile, rigenerazione salt, report incidente'],
      ['Registro', 'Audit log su tabella dedicata · export CSV · 90 giorni'],
      ['Licenza', 'GPLv2 or later · WP Plugin Check 0 errori / 0 warning'],
    ],
  });

const manTour = manual('olotour-manuale', 'olotour', 'tour', 'tour', 'olotour', 'OLO-TUR-M01', 'prodotto in arrivo',
  'Le fondamenta di OLOtour: scene, hot-spot, percorsi multi-stanza e fruizione immersiva. Documento concettuale del prodotto in fase di test finali prelancio.',
  [
    { anchor: 'c1', no: '§1', title_html: 'Cos’è <em>OLOtour</em>', body_html: '<p>OLOtour porta i <strong>tour virtuali 360°</strong> dentro WordPress: percorsi interattivi fatti di panorami sferici, hot-spot cliccabili e ambienti collegati, pubblicati come qualsiasi altro contenuto del sito.</p><p>La differenza rispetto alle piattaforme di tour esistenti: <strong>niente embed a canone</strong> da servizi esterni, niente branding altrui sui tuoi spazi, niente dati dei visitatori che finiscono a terzi. Il tour vive sul tuo hosting.</p><div class="notice"><span class="nl">Stato</span><p>Prodotto <strong>in fase di test finali prelancio</strong>: questo manuale descrive le fondamenta su cui è costruito. Un assaggio esiste già oggi, il tile <strong>Viewer 360°</strong> della famiglia Interactive di OLObuild.</p></div>' },
    { anchor: 'c2', no: '§2', title_html: 'Scene &amp; <em>media</em>', body_html: '<ul class="dash"><li><strong>Foto sferiche</strong>: panorami equirettangolari dai tuoi scatti (fotocamere 360° o smartphone).</li><li><strong>Video 360°</strong>: riprese sferiche riproducibili dentro la scena.</li><li><strong>Librerie integrate</strong>: panorami professionali e HDRI da <strong>Polyhaven</strong>, viste da <strong>Google Street View</strong>: si parte anche senza materiale proprio.</li></ul>' },
    { anchor: 'c3', no: '§3', title_html: 'Gli <em>hot-spot</em>', body_html: '<p>Ogni scena può contenere punti interattivi:</p><ul class="dash"><li><strong>Informativi</strong>: testo e immagini: la scheda della camera, il cartellino dell’opera.</li><li><strong>Di navigazione</strong>: portali verso un’altra scena: la porta che si apre sull’ambiente successivo.</li><li><strong>Di azione</strong>: link verso il sito: la CTA “prenota questa camera” dentro la camera stessa.</li></ul>' },
    { anchor: 'c4', no: '§4', title_html: 'Percorsi, 3D &amp; <em>VR</em>', body_html: '<ul class="dash"><li><strong>Multi-stanza</strong>: scene collegate in percorsi: ingresso → camera → terrazza → prenota.</li><li><strong>Scene 3D e splat</strong>: oltre le foto: modelli 3D e gaussian splat per ambienti ricostruiti.</li><li><strong>VR</strong>: fruizione con visore direttamente dal browser, senza app dedicate.</li></ul>' },
    { anchor: 'c5', no: '§5', title_html: 'Nella <em>suite</em>', body_html: '<ul class="dash"><li><strong>OLObuild</strong>: il tile Viewer 360° è già disponibile; OLOtour ne è l’evoluzione a percorsi completi.</li><li><strong>OLObooking</strong>: l’hot-spot di azione porta al motore di prenotazione: vedi la camera, la prenoti.</li><li><strong>OLOlang</strong>: schede e didascalie degli hot-spot traducibili in 28 lingue.</li></ul>' },
  ],
  {
    name: 'OLOtour', sub: 'test finali prelancio',
    rows: [
      ['Tipo', 'Plugin WordPress per tour virtuali 360° interattivi'],
      ['Stato', '<strong>Test finali prelancio</strong>: rilascio imminente'],
      ['Media', 'Foto sferiche equirettangolari · video 360° · HDRI'],
      ['Sorgenti integrate', 'Polyhaven (panorami/HDRI) · Google Street View'],
      ['Interazione', 'Hot-spot informativi, di navigazione e di azione'],
      ['Percorsi', 'Multi-stanza: scene collegate in sequenze navigabili'],
      ['Immersivo', 'Scene 3D e gaussian splat · fruizione VR da browser'],
      ['Hosting', 'Tour e media sul proprio server, nessun embed a canone'],
      ['Oggi disponibile', 'Tile Viewer 360° nella famiglia Interactive di OLObuild'],
    ],
    cta2: 'Prova il Viewer 360° in OLObuild', url2: P('olobuild'),
  });

const manTutor = manual('olotutor-manuale', 'olotutor', 'tutor', 'tutor', 'olotutor', 'OLO-TUT-M01', 'prodotto in arrivo',
  'Le fondamenta di OLOtutor: corsi, quiz, gamification, registro e certificati. Documento concettuale del prodotto in sviluppo.',
  [
    { anchor: 'c1', no: '§1', title_html: 'Cos’è <em>OLOtutor</em>', body_html: '<p>OLOtutor è il modulo formazione della suite: un <strong>LMS dentro WordPress</strong> per scuole, accademie e formatori. Corsi, lezioni, quiz, punti, badge e certificati, pubblicati sul tuo dominio.</p><p>La differenza rispetto ai marketplace di corsi: <strong>gli allievi restano tuoi</strong>. Iscrizioni, dati e pagamenti passano dal tuo sito, non da una piattaforma che ti mette in fila coi concorrenti e trattiene una percentuale.</p><div class="notice"><span class="nl">Stato</span><p>Prodotto <strong>in sviluppo</strong>: questo manuale descrive le fondamenta su cui è costruito. Il telaio su cui poggerà, sito, prenotazioni, lingue, è già disponibile.</p></div>' },
    { anchor: 'c2', no: '§2', title_html: 'Corsi &amp; <em>lezioni</em>', body_html: '<ul class="dash"><li><strong>Struttura di corso</strong>: moduli e lezioni ordinate, con prerequisiti: la lezione 4 si apre dopo la 3.</li><li><strong>Contenuti misti</strong>: testo, video, materiali scaricabili, costruiti coi tile di OLObuild.</li><li><strong>Area allievi</strong>: ogni iscritto vede i propri corsi, i progressi e il punto in cui ha lasciato.</li></ul>' },
    { anchor: 'c3', no: '§3', title_html: 'Quiz &amp; <em>gamification</em>', body_html: '<ul class="dash"><li><strong>Quiz</strong>: verifiche a risposta multipla con soglia di superamento e tentativi configurabili.</li><li><strong>Punti e badge</strong>: XP per lezioni e verifiche completate, badge ai traguardi.</li><li><strong>Mini-giochi</strong>: esercizi interattivi per tenere alta l’attenzione.</li></ul><p>La motivazione fa parte del metodo, non è un plugin in più.</p>' },
    { anchor: 'c4', no: '§4', title_html: 'Registro &amp; <em>certificati</em>', body_html: '<ul class="dash"><li><strong>Registro voti</strong>: l’esito di ogni verifica, per allievo e per corso, consultabile dal docente.</li><li><strong>Certificati di completamento</strong>: generati al superamento del corso, pronti da scaricare.</li></ul>' },
    { anchor: 'c5', no: '§5', title_html: 'Nella <em>suite</em>', body_html: '<ul class="dash"><li><strong>OLObooking</strong>: lezioni individuali e workshop prenotabili su slot: il calendario è lo stesso motore della suite.</li><li><strong>OLOlang</strong>: contenuti didattici traducibili in 28 lingue.</li><li><strong>OLObuild</strong>: le pagine dei corsi si costruiscono coi tile del telaio.</li></ul>' },
  ],
  {
    name: 'OLOtutor', sub: 'in sviluppo · specifiche preliminari',
    rows: [
      ['Tipo', 'LMS (Learning Management System) per WordPress'],
      ['Stato', '<strong>In sviluppo</strong>: pagina prodotto online, rilascio in arrivo'],
      ['Struttura', 'Corsi → moduli → lezioni, con prerequisiti e progressi per allievo'],
      ['Verifiche', 'Quiz con soglia e tentativi configurabili · mini-giochi'],
      ['Gamification', 'Punti XP, badge, traguardi'],
      ['Output', 'Registro voti · certificati di completamento'],
      ['Dati', 'Allievi, iscrizioni e pagamenti sul proprio sito, nessun marketplace'],
      ['Suite', 'Slot con OLObooking · 28 lingue con OLOlang · pagine coi tile OLObuild'],
    ],
  });

/* ==================================================================== */
/* HEADER & FOOTER condivisi (struttura classica olobuild)               */
/* Tile in modalità AUTO: attivo/pill/link dedotti dallo slug corrente.  */
/* ==================================================================== */
// Header VERO: tile "Mega Menu / Site Header" (oloheader) di olobuild,
// configurata coi contenuti del sito (loghi bundled del plugin in assets/img/menu/).
const MLOGO = (f) => `/wp-content/plugins/olobuild/assets/img/menu/${f}`;
// Header classico: sitelogo + menu WP prodotti + lingue + pallini fermate +
// pill viaggio (nascosta sulla front page via condizione invertita).
const MENU_PRODOTTI_ID = 232; // menu WP "OLOX Prodotti" su mosaic
const headerTpl = {
  title: 'OLOtheme \u2014 Header',
  slug: 'olox-header',
  kind: 'header',
  content: [
    {
      id: randomUUID(), type: 'section',
      settings: {
        bg: { type: 'solid', color: 'rgba(12,14,19,.92)' }, style: 'default',
        width: 'fullbleed', padding: 'custom', padding_top_custom: 9, padding_bottom_custom: 9,
        bg_scope: 'section', sticky_effect: 'none',
      },
      style: [], advanced: [],
      children: [
        hRow('50-50', [
          // Sinistra: logo + lingue accanto (nessun menu testuale \u2014 decisione utente)
          hColRow([
            tile('sitelogo', {
              source: 'custom_image', custom_image: '/wp-content/uploads/olotheme-site/olotheme-orizz-white.png',
              max_height: 24, link_url: '/', alignment: 'left',
            }),
            // Lingue (decorative come sul live; langswitcher vero quando OLOlang avr\u00e0 pi\u00f9 lingue)
            tile('badge', {
              text: 'IT', variant: 'solid', bg_color: 'rgba(232,69,61,.18)', text_color: PRODUCT_HEX.build,
              typography_preset: 'olox-mono',
              font_size: '10', font_weight: '700', text_transform: 'uppercase', letter_spacing: '1',
              padding_y: 4, padding_x: 7, alignment: 'left',
              badge_radius: { tl: 4, tr: 4, br: 4, bl: 4 },
              extra_items: ['EN', 'FR', 'DE', 'ES'].map((l) => ({ text: l, color: 'transparent', text_color: INK.faint })),
            }),
          ], '1-2', '22'),
          {
            id: randomUUID(), type: 'column',
            settings: { bg: { type: 'none' }, width_medium: '1-2', flex_direction: 'row', flex_wrap: 'nowrap', flex_align: 'center', flex_justify: 'flex-end', flex_column_gap: '14', flex_row_gap: '8' },
            style: [], advanced: [],
            children: [
              // Pallini fermate: solo sulle pagine con Cover orizzontale (AUTO)
              tile('coverdots', {
                items: [
                  { label: 'OLOtheme', color: PRODUCT_HEX.build },
                  { label: 'OLObuild', color: PRODUCT_HEX.build },
                  { label: 'OLObooking', color: PRODUCT_HEX.booking },
                  { label: 'OLOlang', color: PRODUCT_HEX.lang },
                  { label: 'OLOsecurity', color: PRODUCT_HEX.secur },
                  { label: 'OLOtour', color: PRODUCT_HEX.tour },
                  { label: 'OLOtutor', color: PRODUCT_HEX.tutor },
                  { label: 'Capolinea', color: PRODUCT_HEX.build },
                ],
                hide_without_group: true, dot_size: 26, dot_gap: 3, dot_inner: 8, active_glow: true,
              }),
              // Pill "\u2190 il viaggio": ovunque tranne che sulla front page
              tile('button', {
                text: '\u2190 il viaggio', url: '/', alignment: 'right',
                bg_color: 'transparent', text_color: INK.paper,
                hover_bg_color: 'rgba(250,247,242,.08)', hover_text_color: INK.paper,
                typography_preset: 'olox-mono',
                font_size: '10', font_weight: '700', text_transform: 'uppercase', letter_spacing: '1.5',
                border_radius: { tl: 999, tr: 999, br: 999, bl: 999 },
                tile_padding: { top: 9, right: 16, bottom: 9, left: 16 },
                border: { top: 1, right: 1, bottom: 1, left: 1, linked: true, style: 'solid', color: 'rgba(250,247,242,.3)' },
                cond_type: 'is_front_page', cond_negate: true,
              }),
            ],
          },
        ]),
      ],
    },
  ],
};
const headerTplOld = {
  title: 'OLOtheme \u2014 Header (mega, inutilizzato)',
  slug: 'olox-header-old',
  kind: 'unused',
  content: page([
    tile('oloheader', {
      brand_logo: MLOGO('olotheme-orizz.png'),
      brand_height: 25,
      brand_url: '/',
      nav_items: [
        { label: 'Prodotti', url: '#', type: 'mega' },
        { label: 'Esperienza', url: '/', type: 'link' },
      ],
      rail_show: true,
      rail_badge: 'Ecosistema OLO',
      rail_title: 'Un telaio, sei prodotti',
      rail_text: 'Stessa anima olonica per costruire, gestire e far crescere il tuo sito WordPress. Senza SaaS, senza lock-in.',
      rail_cta1_label: 'Inizia il viaggio',
      rail_cta1_url: '/',
      rail_cta2_label: 'OLObuild free',
      rail_cta2_url: P('olobuild'),
      mega_columns: 2,
      mega_products: [
        { group: 'Costruisci', logo: MLOGO('olobuild-q.png'), name: 'OLObuild', desc: 'Page builder \u00b7 187 tile drag & drop', url: P('olobuild'), soon: false },
        { group: 'Costruisci', logo: MLOGO('ololang-q.png'), name: 'OLOlang', desc: 'Multilingua nativo \u00b7 28 lingue', url: P('ololang'), soon: false },
        { group: 'Costruisci', logo: MLOGO('olosecurity-q.png'), name: 'OLOsecurity', desc: 'Firewall, 2FA e scanner \u00b7 100% locale', url: P('olosecurity'), soon: false },
        { group: 'Gestisci & cresci', logo: MLOGO('olobooking-q.png'), name: 'OLObooking', desc: 'Prenotazioni \u00b7 6 verticali', url: P('olobooking'), soon: false },
        { group: 'Gestisci & cresci', logo: MLOGO('olotour-q.png'), name: 'OLOtour', desc: 'Tour virtuali 360\u00b0', url: P('olotour'), soon: true },
        { group: 'Gestisci & cresci', logo: MLOGO('olotutor-q.png'), name: 'OLOtutor', desc: 'Corsi, quiz e certificati', url: P('olotutor'), soon: true },
      ],
      mega_footer_show: true,
      mega_footer_logos: [ { logo: MLOGO('olotour-q.png') }, { logo: MLOGO('olotutor-q.png') } ],
      mega_footer_title: 'In arrivo \u00b7 OLOtour & OLOtutor',
      mega_footer_text: 'Tour virtuali 360\u00b0 e corsi online. Le schede prodotto sono gi\u00e0 online.',
      mega_footer_cta_label: 'Vedi le anteprime',
      mega_footer_cta_url: P('olotour'),
      featured_show: false,
      search_show: false,
      lang_show: true,
      lang_current: 'it',
      languages: [
        { code: 'it', label: 'Italiano', url: '/' },
        { code: 'en', label: 'English', url: '#' },
        { code: 'fr', label: 'Fran\u00e7ais', url: '#' },
        { code: 'de', label: 'Deutsch', url: '#' },
        { code: 'es', label: 'Espa\u00f1ol', url: '#' },
      ],
      cta_show: true,
      cta_label: 'Inizia il viaggio',
      cta_url: '/',
      open_mega_on: 'hover',
    }),
  ]),
};
// Footer composto con tile classiche: wordmark, link, fine + riga credits.
const footerTpl = {
  title: 'OLOtheme \u2014 Footer',
  slug: 'olox-footer',
  kind: 'footer',
  content: [
    {
      id: randomUUID(), type: 'section',
      settings: {
        bg: { type: 'solid', color: INK.bg }, style: 'default',
        width: 'default', padding: 'custom', padding_top_custom: 44, padding_bottom_custom: 28,
        bg_scope: 'section', sticky_effect: 'none',
      },
      style: [], advanced: [],
      children: [
        hRow('25-50-25', [
          hCol([ tile('image', {
            image_url: '/wp-content/uploads/olotheme-site/olotheme-orizz-white.png', alt_text: 'OLOtheme',
            image_width: '150px', height: 'auto', object_fit: 'contain', image_alignment: 'left',
          }) ], '1-4'),
          hCol([ xText('<p><a href="/">il viaggio</a> \u00b7 <a href="' + P('olobuild') + '">build</a> \u00b7 <a href="' + P('olobooking') + '">booking</a> \u00b7 <a href="' + P('ololang') + '">lang</a> \u00b7 <a href="' + P('olosecurity') + '">security</a> \u00b7 <a href="' + P('olotour') + '">tour</a> \u00b7 <a href="' + P('olotutor') + '">tutor</a></p>', '13', INK.dim) ], '1-2'),
          hCol([ xText('<p>GPL \u00b7 Trento \u00b7 no SaaS</p>', '12', INK.faint) ], '1-4'),
        ]),
        hRow('100', [
          hCol([
            // Credits sempre visibili: barra fissa in fondo alla viewport.
            tile('bottombar', {
              content_html: 'OLOtheme by <a href="https://clod.eu" target="_blank" rel="noopener">clod.eu</a> | @2026 | sito introduttivo | <a href="mailto:info@olotheme.com">info@olotheme.com</a>',
              align: 'center', bg_color: 'rgba(12,14,19,.92)', text_color: INK.faint, link_color: INK.paper,
              font_size: 10, letter_spacing: 2, uppercase: true, font_preset: 'olox-mono',
              padding_y: 14, border_top: false, z_index: 92,
            }),
          ]),
        ]),
      ],
    },
  ],
};

/* ==================================================================== */
/* PARTIAL · contenuto della modale "olonica" (popup mode template)      */
/* ==================================================================== */
const CHIP_NAME = { build: 'build', booking: 'booking', lang: 'lang', secur: 'security', tour: 'tour', tutor: 'tutor' };
// Riga battaglia: card scura con domanda serif a sinistra e chips prodotto a destra.
const battleGap = () => hRow('100', [ hCol([ xGap(10) ]) ]);
const battleRow = (q, chips) => ({
  id: randomUUID(), type: 'row',
  settings: { bg: { type: 'solid', color: '#161A23' }, layout: '50-50', stack_mobile: true },
  style: [], advanced: [],
  children: [
    hCol([
      xTitle(q, '', PRODUCT_HEX.build, '18', 'h4'),
    ], '1-2'),
    {
      id: randomUUID(), type: 'column',
      settings: { bg: { type: 'none' }, width_medium: '1-2', flex_direction: 'row', flex_wrap: 'wrap', flex_align: 'center', flex_justify: 'flex-end', flex_column_gap: '8', flex_row_gap: '8' },
      style: [], advanced: [],
      children: [
        tile('badge', {
          text: CHIP_NAME[chips[0]], variant: 'solid', bg_color: PRODUCT_HEX[chips[0]], text_color: INK.bg,
          typography_preset: 'olox-mono',
          font_size: '10', font_weight: '700', text_transform: 'lowercase', letter_spacing: '0.5',
          padding_y: 6, padding_x: 12, alignment: 'right',
          extra_items: chips.slice(1).map((c) => ({ text: CHIP_NAME[c], color: PRODUCT_HEX[c], text_color: INK.bg })),
        }),
      ],
    },
  ],
});
const olonicaPartial = {
  title: 'OLOX — Popup olonica',
  slug: 'olox-popup-olonica',
  kind: 'partial',
  content: [
    {
      id: randomUUID(), type: 'section',
      settings: {
        bg: { type: 'none' }, style: 'default', width: 'default',
        padding: 'custom', padding_top_custom: 8, padding_bottom_custom: 8,
        bg_scope: 'section', sticky_effect: 'none',
      },
      style: [], advanced: [],
      children: [
        hRow('100', [ hCol([
          xKicker('—— OLOS · INTERO E PARTE', PRODUCT_HEX.build),
          xGap(10),
          xTitle('La cellula olonica', 'olonica', PRODUCT_HEX.build, '34', 'h3'),
          xGap(8),
          xText('<p>Un <strong>olone</strong> è qualcosa che è insieme <strong>un tutto e una parte</strong>: completo da solo, più forte dentro un organismo. OLOtheme è costruito così, ogni prodotto è una cellula autonoma che funziona da sola, ma condivide telaio, dati e lingua con le altre.</p>', '15'),
          xGap(6),
          xText('<p>Niente monolite: <strong>i prodotti si uniscono a seconda della battaglia</strong> da affrontare, e si sciolgono quando non servono.</p>', '15'),
          xGap(14),
        ]) ]),
        battleRow('Aprire un B&B', ['build', 'booking', 'lang']),
        battleGap(),
        battleRow('Respingere un attacco', ['secur']),
        battleGap(),
        battleRow('Vendere all’estero', ['build', 'lang']),
        battleGap(),
        battleRow('Far visitare un immobile a distanza', ['tour', 'booking']),
        battleGap(),
        battleRow('Portare i corsi online', ['tutor', 'booking', 'lang']),
      ],
    },
  ],
};

/* ---------- scrittura ---------- */
const ALL = [olonicaPartial, headerTpl, footerTpl, home, buildPage, bookingPage, langPage, securPage, tourPage, tutorPage, manBuild, manBooking, manLang, manSecur, manTour, manTutor];
for (const t of ALL) {
  writeFileSync(`${OUT}/${t.slug}.json`, JSON.stringify({ title: t.title, slug: t.slug, kind: t.kind || 'page', content: t.content }), 'utf8');
  console.log(`✓ ${t.slug}.json (${t.content.length} sezioni · ${t.kind || 'page'})`);
}
console.log(`\n${ALL.length} template generati in ${OUT}`);
