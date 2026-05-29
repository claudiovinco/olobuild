// Generatore template Olobuild "Home" — versione tile-native (impact-first):
// - HERO, GALAXY 5 PRODOTTI, PRICING TEASER, FAQ → tile-native (hero/iconbox/pricing/accordion)
// - Altre sezioni → tile html fedele al sorgente (zero perdita testo)
//
// Tutto il copy proviene da home-source.html (estratto da _olo_handoff_content_source).

const { randomUUID } = require('crypto');
const fs = require('fs');
const path = require('path');

const uid = () => randomUUID();

// ── Helpers struttura ──────────────────────────────────────────────────────
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

// ──────────────────────────────────────────────────────────────────────────
// SEZIONI TILE-NATIVE
// ──────────────────────────────────────────────────────────────────────────

// ── HERO ───────────────────────────────────────────────────────────────────
function buildHero() {
  // Eyebrow + trust bar + screenshot mockup: preservati come tile html attorno al hero.
  const eyebrowHtml = `<p style="text-align:center;font-size:13px;font-weight:600;color:#dc2626;text-transform:uppercase;letter-spacing:0.08em;margin:0 0 20px">Stack WordPress per agenzie e PMI</p>`;

  const heroTile = tile('hero', {
    preset: 'modern-centered',
    bg: { type: 'none' },
    title: 'Costruisci. Traduci. Prenota.<br>Un solo ecosistema WordPress.',
    subtitle: 'Cinque prodotti integrati, una sola logica, nessun SaaS esterno. <strong>OLObuild è gratis per sempre</strong>. OLObuild Pro è in lancio a <strong>€ 29</strong> (era € 59) con OLOlang multilingua <strong>incluso a vita</strong>.',
    text_color: '',
    title_tag: 'h1',
    title_font_size: '52',
    title_font_weight: '800',
    title_line_height: '1.15',
    title_color: '#0f172a',
    subtitle_font_size: '18',
    subtitle_color: '#475569',
    subtitle_max_width: '780',
    min_height: '480px',
    content_max_width: '960',
    vertical_align: 'center',
    horizontal_align: 'center',
    text_align: 'center',
    tile_padding: { top: 20, right: 24, bottom: 32, left: 24 },
    bg_type: 'color',
    bg_color: '',
    cta_text: 'Prenota una demo →',
    cta_url: '#prenota-demo',
    cta_target: '_self',
    cta_style: 'filled',
    cta_size: '16',
    cta_bg_color: '#dc2626',
    cta_text_color: '#ffffff',
    cta_radius: { tl: 8, tr: 8, br: 8, bl: 8 },
    cta2_text: 'Prova OLObuild ora · 12h gratis',
    cta2_url: 'https://try.olotheme.com/',
    cta2_target: '_blank',
    cta2_style: 'outline',
    cta2_text_color: '#dc2626',
    shadow: 'none',
    full_bleed: false,
  });

  // Reassure + trust bar + screenshot mockup: tile html sotto al hero, preserva
  // 1:1 il copy originale (5 reassurance items, 5 trust items, screenshot stilizzato).
  const reassureTrustScreenshotHtml = `
<p style="text-align:center;font-size:13px;color:#64748b;margin:0 auto 28px;max-width:720px">Niente carta di credito · Niente registrazione obbligatoria · 30 giorni soddisfatti o rimborsati su Pro</p>

<ul style="list-style:none;display:flex;flex-wrap:wrap;justify-content:center;gap:8px 18px;padding:0;margin:0 0 40px;font-size:13px;color:#475569">
  <li><span aria-hidden="true">📂</span> Codice aperto · no lock-in</li>
  <li><span aria-hidden="true">🇮🇹</span> Sviluppato a Trento, Italia</li>
  <li><span aria-hidden="true">🔒</span> GDPR ready</li>
  <li><span aria-hidden="true">🚫</span> Niente SaaS · niente lock-in</li>
  <li><span aria-hidden="true">↩️</span> 30gg rimborsato</li>
</ul>

<div style="max-width:880px;margin:0 auto 0;border-radius:18px;box-shadow:0 24px 60px -28px rgba(15,23,42,.35);overflow:hidden;background:#fff;border:1px solid #e2e8f0">
  <div style="display:flex;align-items:center;gap:6px;padding:10px 14px;background:#f8fafc;border-bottom:1px solid #e2e8f0">
    <span style="display:inline-block;width:11px;height:11px;border-radius:50%;background:#ef4444"></span>
    <span style="display:inline-block;width:11px;height:11px;border-radius:50%;background:#f59e0b"></span>
    <span style="display:inline-block;width:11px;height:11px;border-radius:50%;background:#10b981"></span>
    <span style="margin-left:10px;font-size:12px;color:#64748b">OLObuild — Anteprima</span>
  </div>
  <div style="padding:18px;display:flex;flex-direction:column;gap:14px">
    <div style="display:flex;gap:14px">
      <div style="flex:2;height:120px;border-radius:12px;background:linear-gradient(135deg,#fee2e2,#fef3c7);display:flex;align-items:center;justify-content:center;color:#7f1d1d;font-weight:600">Hero</div>
      <div style="flex:1;height:120px;border-radius:12px;background:#f1f5f9;display:flex;align-items:center;justify-content:center;color:#475569;font-weight:600">Image</div>
    </div>
    <div style="display:flex;gap:14px">
      <div style="flex:1;height:90px;border-radius:12px;background:#f1f5f9;display:flex;align-items:center;justify-content:center;color:#475569;font-weight:600">Card</div>
      <div style="flex:1;height:90px;border-radius:12px;background:#f1f5f9;display:flex;align-items:center;justify-content:center;color:#475569;font-weight:600">Card</div>
      <div style="flex:1;height:90px;border-radius:12px;background:#f1f5f9;display:flex;align-items:center;justify-content:center;color:#475569;font-weight:600">Card</div>
    </div>
    <div style="display:flex;gap:14px">
      <div style="flex:1;height:90px;border-radius:12px;background:#f1f5f9;display:flex;align-items:center;justify-content:center;color:#475569;font-weight:600">Form</div>
      <div style="flex:2;height:90px;border-radius:12px;background:linear-gradient(135deg,#dbeafe,#e0e7ff);display:flex;align-items:center;justify-content:center;color:#1e40af;font-weight:600">Gallery</div>
    </div>
  </div>
  <p style="text-align:center;font-size:12px;color:#94a3b8;margin:0;padding:10px 14px;background:#f8fafc;border-top:1px solid #e2e8f0">↑ Anteprima stilizzata del builder. Screenshot reale al lancio pubblico.</p>
</div>
`.trim();

  return section(
    [row([column([
      tile('html', { html_content: eyebrowHtml }),
      heroTile,
      tile('html', { html_content: reassureTrustScreenshotHtml }),
    ])])],
    { padding: 'large' }
  );
}

// ── GALAXY 5 PRODOTTI ──────────────────────────────────────────────────────
function buildGalaxy() {
  const headline = tile('headline', {
    preset: 'modern-clean',
    heading: 'Un telaio, cinque prodotti, nessuna catena',
    subtitle: 'Ogni prodotto è autonomo. Li usi uno per uno o tutti insieme. Quando si parlano lo fanno senza middleware — è lo stesso codice.',
    tag: 'h2',
    alignment: 'center',
    heading_size: 'lg',
    heading_color: '#0f172a',
    subtitle_color: '#64748b',
    decoration: 'line',
    decoration_color: '#dc2626',
    tile_padding: { top: 0, right: 0, bottom: 12, left: 0 },
  });

  const eyebrowHtml = `<p style="text-align:center;font-size:13px;font-weight:600;color:#dc2626;text-transform:uppercase;letter-spacing:0.08em;margin:0 0 12px">La galassia</p>`;

  // 6 prodotti con copy fedele al sorgente (taglines, descrizioni, CTA).
  const products = [
    {
      icon: '🎨', title: 'OLObuild', color: '#E94B50', url: 'https://olotheme.com/prodotti/olobuild/',
      tagline: 'Il telaio · gratis per sempre · Pro per il wow',
      description: 'Page builder olonico con 187 tile, doppio click per editare, anteprima fedele. È la base di tutto il resto.',
      cta_text: 'Scopri →',
    },
    {
      icon: '🌍', title: 'OLOlang', color: '#C2185B', url: 'https://olotheme.com/prodotti/ololang/',
      tagline: 'Multilingua nativo',
      description: '28 lingue native, traduzioni IA con editing umano, SEO per ogni locale, URL puliti, switcher pronto.',
      cta_text: 'Scopri →',
    },
    {
      icon: '📅', title: 'OLObooking', color: '#1E88E5', url: 'https://olotheme.com/prodotti/olobooking/',
      tagline: 'Hub prenotazioni · sei verticali',
      description: 'Stesso motore sotto accommodation, ristoranti, immobiliare, noleggi, eventi, appuntamenti. Pagamenti, calendari, sync OTA: tutto integrato.',
      cta_text: 'Esplora i verticali →',
      highlight: true,
    },
    {
      icon: '📍', title: 'OLOtour', color: '#16A085', url: 'https://olotheme.com/prodotti/olotour/',
      badge: 'In arrivo',
      tagline: 'Virtual tour 360°',
      description: 'Panorami e video equirectangolari, nove tipi di hotspot, VR stereo + giroscopo mobile, varianti scena, planimetria interattiva.',
      cta_text: 'Anteprima →',
    },
    {
      icon: '🎓', title: 'OLOtutor', color: '#2E7D32', url: 'https://olotheme.com/prodotti/olotutor/',
      badge: 'In arrivo',
      tagline: 'E-learning gamificato',
      description: 'Editor corsi drag & drop, 20+ mini-game, gamification con Open Badges, analytics studente, certificati automatici.',
      cta_text: 'Anteprima →',
    },
    {
      icon: '🧪', title: 'OLO Space', color: '#8BC34A', url: '',
      badge: '🧪 Lab',
      tagline: 'Modulo sperimentale dentro OLObuild',
      description: '10 tile dedicati a coworking, affittacamere, host card, sale prenotabili e spazi condivisi. Vive dentro OLObuild — non è un prodotto separato, è un esperimento di settore.',
      note: 'Disponibile come categoria tile in OLObuild. Pagina dedicata non prevista per ora.',
    },
  ];

  const productCols = products.map((p) => {
    const titleHtml = p.badge
      ? `${p.title} <span style="display:inline-block;font-size:10px;font-weight:600;padding:2px 8px;border-radius:999px;background:${p.color}20;color:${p.color};margin-left:6px;vertical-align:middle">${p.badge}</span>`
      : p.title;
    const descFull = `<strong style="display:block;font-size:13px;color:${p.color};margin-bottom:6px;font-weight:600">${p.tagline}</strong>${p.description}${p.note ? `<br><span style="display:block;margin-top:8px;font-size:11px;color:#94a3b8">${p.note}</span>` : ''}`;

    return column([
      tile('iconbox', {
        preset: p.highlight ? 'modern-card' : 'modern-card',
        bg: { type: 'none' },
        icon_emoji: p.icon,
        title: titleHtml,
        description: descFull,
        link_url: p.url,
        link_text: p.url ? p.cta_text || 'Scopri →' : '',
        alignment: 'left',
        icon_size: '2.4',
        icon_position: 'top',
        icon_color: p.color,
        icon_bg_color: p.color + '15',
        icon_bg_shape: 'rounded',
        title_font_size: '20',
        title_font_weight: '700',
        title_color: '#0f172a',
        text_color: '#475569',
        link_color: p.color,
        icon_gap: '14',
        title_gap: '10',
        desc_gap: '14',
        tile_padding: { top: 26, right: 24, bottom: 26, left: 24 },
        border_radius: { tl: 16, tr: 16, br: 16, bl: 16 },
        bg_type: 'color',
        bg_color: '#ffffff',
        shadow: p.highlight ? 'md' : 'sm',
        border: { style: 'solid', color: p.highlight ? p.color : '#e5e7eb', width: { top: p.highlight ? 2 : 1, right: p.highlight ? 2 : 1, bottom: p.highlight ? 2 : 1, left: p.highlight ? 2 : 1 } },
      }),
    ], { width_default: '', width_medium: '1-3' });
  });

  const grid1 = row(productCols.slice(0, 3), { layout: '1-3,1-3,1-3', gap: 24, column_gap: 'default', vertical_align: 'top', stack_mobile: true });
  const grid2 = row(productCols.slice(3, 6), { layout: '1-3,1-3,1-3', gap: 24, column_gap: 'default', vertical_align: 'top', stack_mobile: true });

  return section(
    [row([column([
      tile('html', { html_content: eyebrowHtml }),
      headline,
      grid1,
      grid2,
    ])])],
    { padding: 'large' }
  );
}

// ── PRICING TEASER ─────────────────────────────────────────────────────────
function buildPricing() {
  const headline = tile('headline', {
    preset: 'modern-clean',
    heading: 'Tre modi di iniziare',
    subtitle: 'OLObuild gratis per sempre. OLObuild Pro a prezzo lancio scontato del 50%. Con Pro, <strong>OLOlang è incluso a vita</strong>.',
    tag: 'h2',
    alignment: 'center',
    heading_size: 'lg',
    heading_color: '#0f172a',
    subtitle_color: '#64748b',
    decoration: 'line',
    decoration_color: '#dc2626',
    tile_padding: { top: 0, right: 0, bottom: 12, left: 0 },
  });

  const eyebrowHtml = `<p style="text-align:center;font-size:13px;font-weight:600;color:#dc2626;text-transform:uppercase;letter-spacing:0.08em;margin:0 0 12px">Pricing · offerta lancio</p>`;

  // OLObuild Free
  const planFree = tile('pricing', {
    preset: 'modern-clean',
    bg: { type: 'none' },
    plan_name: 'OLObuild',
    price: '0',
    currency: '€',
    currency_position: 'before',
    period: '/per sempre',
    features: '✓ Page builder completo\n✓ 90+ tile gratuiti\n✓ Form builder, mega-menu, dark mode\n✓ Aggiornamenti via WP.org\n✓ Codice aperto · niente lock-in\n✓ OLOlang gratis il 1° anno (poi senza update)\n— Tile Pro (22 wow-factor)',
    check_style: 'none',
    feature_dividers: false,
    cta_text: 'Provalo ora (12h gratis, un assaggio) →',
    cta_url: 'https://try.olotheme.com/',
    cta_target: '_blank',
    cta_width: '100',
    cta_style: 'outline',
    cta_bg_color: '#ffffff',
    cta_text_color: '#dc2626',
    cta_border_width: '2',
    cta_border_color: '#dc2626',
    cta_radius: { tl: 8, tr: 8, br: 8, bl: 8 },
    bg_color: '#ffffff',
    text_color: '#475569',
    price_color: '#0f172a',
    accent_color: '#dc2626',
    border_radius: '16',
    shadow: 'sm',
  });

  // OLObuild Pro (highlight)
  const planPro = tile('pricing', {
    preset: 'highlighted-pro',
    bg: { type: 'none' },
    plan_name: 'OLObuild Pro',
    price: '29',
    currency: '€',
    currency_position: 'before',
    period: '/anno · 1° anno',
    sale_price: '59',
    sale_badge_text: 'Lancio · -50%',
    sale_badge_color: '#dc2626',
    is_popular: true,
    badge_text: 'Lancio · -50%',
    badge_style: 'pill',
    badge_bg_color: '#dc2626',
    badge_text_color: '#ffffff',
    features: '✓ Tutto OLObuild Free +\n✓ 22 tile Pro (animated heading, hotspot, gallery pro, chart, marquee…)\n✓ 🎁 OLOlang incluso a vita (28 lingue, DeepL)\n✓ Aggiornamenti + supporto prioritario\n✓ Niente site-lock a scadenza\n✓ 1 sito · upgrade multi-sito disponibile',
    check_style: 'checkmark',
    feature_dividers: false,
    cta_text: 'Prenota lo sconto lancio →',
    cta_url: 'https://olotheme.com/pricing/',
    cta_target: '_self',
    cta_width: '100',
    cta_style: 'filled',
    cta_bg_color: '#dc2626',
    cta_text_color: '#ffffff',
    cta_radius: { tl: 8, tr: 8, br: 8, bl: 8 },
    additional_info: 'Prezzo lancio garantito per il primo anno · rinnovi €59/anno',
    bg_color: '#ffffff',
    text_color: '#475569',
    price_color: '#0f172a',
    accent_color: '#dc2626',
    border_radius: '16',
    shadow: 'md',
  });

  // OLOlang
  const planLang = tile('pricing', {
    preset: 'modern-clean',
    bg: { type: 'none' },
    plan_name: 'OLOlang',
    price: '0',
    currency: '€',
    currency_position: 'before',
    period: '/primo anno · standalone',
    features: '✓ 28 lingue native\n✓ Traduzione automatica DeepL\n✓ Dashboard translator + glossario\n✓ Translation memory\n✓ Export/Import XLIFF\n✓ REST API + WP-CLI\n✓ Dal 2° anno: resta attivo senza update (niente site-lock)\n💡 Aggiornato a vita se prendi OLObuild Pro',
    check_style: 'none',
    feature_dividers: false,
    cta_text: 'Inizia il primo anno gratis →',
    cta_url: 'https://olotheme.com/prodotti/ololang/',
    cta_target: '_self',
    cta_width: '100',
    cta_style: 'outline',
    cta_bg_color: '#ffffff',
    cta_text_color: '#2563eb',
    cta_border_width: '2',
    cta_border_color: '#2563eb',
    cta_radius: { tl: 8, tr: 8, br: 8, bl: 8 },
    bg_color: '#ffffff',
    text_color: '#475569',
    price_color: '#0f172a',
    accent_color: '#2563eb',
    border_radius: '16',
    shadow: 'sm',
  });

  const planRow = row(
    [
      column([planFree], { width_default: '', width_medium: '1-3' }),
      column([planPro],  { width_default: '', width_medium: '1-3' }),
      column([planLang], { width_default: '', width_medium: '1-3' }),
    ],
    { layout: '1-3,1-3,1-3', gap: 24, column_gap: 'default', vertical_align: 'stretch', stack_mobile: true }
  );

  return section(
    [row([column([
      tile('html', { html_content: eyebrowHtml }),
      headline,
      planRow,
    ])])],
    { padding: 'large' }
  );
}

// ── FAQ ────────────────────────────────────────────────────────────────────
function buildFAQ() {
  const headline = tile('headline', {
    preset: 'modern-clean',
    heading: 'Le cose che ci chiedono prima di provare',
    subtitle: '',
    tag: 'h2',
    alignment: 'center',
    heading_size: 'lg',
    heading_color: '#0f172a',
    decoration: 'line',
    decoration_color: '#dc2626',
    tile_padding: { top: 0, right: 0, bottom: 24, left: 0 },
  });

  const eyebrowHtml = `<p style="text-align:center;font-size:13px;font-weight:600;color:#dc2626;text-transform:uppercase;letter-spacing:0.08em;margin:0 0 12px">Domande frequenti</p>`;

  const faqPanels = [
    { id: 'faq-1', title: 'OLObuild è davvero gratis per sempre?',                  content: 'Sì. Il page builder con 90 tile è gratuito a vita, distribuito sotto la stessa licenza di WordPress (GPL v3). Le 22 tile "Pro" (wow-factor, dataviz, dynamic) sono parte del bundle a pagamento, ma il resto resta libero — niente versione "lite mutilata".' },
    { id: 'faq-2', title: 'Posso usarlo su più siti?',                              content: 'OLObuild gratis: sì, su qualunque numero di siti. Le licenze a pagamento dipendono dal piano: il piano Pro è single-site, il piano Suite Agency è multi-sito.' },
    { id: 'faq-3', title: 'Cosa succede dopo la scadenza della licenza?',           content: 'Il sito continua a funzionare. Non c\'è alcun "site lock" come fanno altri vendor: smetti di ricevere aggiornamenti e supporto, ma le funzionalità che hai pagato restano attive sul tuo sito.' },
    { id: 'faq-4', title: 'I miei contenuti dove restano?',                         content: 'Sul tuo WordPress. OLOtheme non è SaaS: nessun database esterno, nessuna API obbligatoria al runtime. Se cambi idea, esporti tutto in formato WP standard e te ne vai.' },
    { id: 'faq-5', title: 'C\'è una migration da Elementor / Divi / Bricks?',       content: 'Tool di migration automatica sono in roadmap (non al lancio). Per ora la migration è manuale via OLObuild builder — la curva di apprendimento è breve, chi conosce Elementor è operativo in 1 giornata.' },
    { id: 'faq-6', title: 'Funziona con WooCommerce?',                              content: 'Sì. OLObuild include 31 tile WooCommerce nativi (loop, product, checkout, mini-cart, filtri, badges).' },
    { id: 'faq-7', title: 'E con tema custom o block theme?',                       content: 'OLObuild gira su qualunque tema WordPress (block themes inclusi). Lo abbiamo testato in particolare con Twenty Twenty-Four. Distribuiamo anche il nostro tema starter hello-olobuild — gratuito, ottimizzato per OLObuild.' },
    { id: 'faq-8', title: 'Supportate l\'italiano e altre lingue?',                 content: 'Interfaccia builder in italiano, inglese, tedesco. Il prodotto OLOlang gestisce 28 lingue native sul frontend. Email transazionali e descriptor prodotto tradotti via OLOlang.' },
  ];

  const accordion = tile('accordion', {
    bg: { type: 'none' },
    preset: 'card-soft',
    panels: faqPanels.map(p => ({ ...p, image: '', video: '', icon: '', hover_image: '', widget_template_id: 0, children: [] })),
    toggle_mode: true,
    default_open: 'first',
    icon_position: 'right',
    icon_style: 'plus',
    animate_icon: true,
    animation_speed: '280',
    content_transition: 'fade',
    header_bg: '#ffffff',
    header_bg_active: '#fef2f2',
    header_text_color: '#0f172a',
    header_text_color_active: '#dc2626',
    header_padding_y: '18',
    header_padding_x: '22',
    header_font_size: '16',
    header_font_weight: '600',
    content_bg: '#ffffff',
    content_padding_y: '20',
    content_padding_x: '22',
    content_font_size: '15',
    text_color: '#475569',
    border_color: '#e5e7eb',
    border_width: '1',
    gap: '12',
    border_radius: '12',
    faq_schema: true,
    shadow: 'sm',
  });

  return section(
    [row([column([
      tile('html', { html_content: eyebrowHtml }),
      headline,
      accordion,
    ])])],
    { padding: 'large' }
  );
}

// ── SOCIAL PROOF BAND ──────────────────────────────────────────────────────
function buildSocialProof() {
  const counterItems = [
    { number: '187', suffix: '', label: 'Tile nativi in OLObuild' },
    { number: '5',   suffix: '', label: 'Prodotti integrati' },
    { number: '28',  suffix: '', label: 'Lingue native (OLOlang)' },
    { number: '100', suffix: '%', label: 'Codice tuo · niente lock-in' },
  ];
  const counterCols = counterItems.map(item => column([
    tile('counter', {
      preset: 'modern-bold', bg: { type: 'none' },
      number: item.number, label: item.label, prefix: '', suffix: item.suffix,
      icon_emoji: '', icon_size: '0',
      number_font_size: '52', number_font_weight: '800',
      text_color: '#0f172a', label_color: '#64748b',
      label_font_size: '14', label_font_weight: '500',
      bg_type: 'color', bg_color: '',
      tile_padding: { top: 16, right: 16, bottom: 16, left: 16 },
      border_radius: '0', shadow: 'none',
    }),
  ], { width_default: '', width_medium: '1-4' }));

  const counterRow = row(counterCols, { layout: '1-4,1-4,1-4,1-4', gap: 12, column_gap: 'default', vertical_align: 'middle', stack_mobile: true });

  // Logo placeholder + label originale (lab fittizi pre-lancio).
  const logosHtml = `
<p style="text-align:center;font-size:13px;color:#64748b;margin:40px 0 16px"><strong>Stanno costruendo su OLOtheme</strong> <em>(loghi reali al lancio pubblico)</em></p>
<div style="display:flex;flex-wrap:wrap;justify-content:center;gap:10px 22px;max-width:880px;margin:0 auto">
  <div style="padding:10px 18px;border:1px dashed #cbd5e1;border-radius:8px;font-size:11px;color:#94a3b8;letter-spacing:0.05em">HOTEL · ALPINA</div>
  <div style="padding:10px 18px;border:1px dashed #cbd5e1;border-radius:8px;font-size:11px;color:#94a3b8;letter-spacing:0.05em">STUDIO ROSSI</div>
  <div style="padding:10px 18px;border:1px dashed #cbd5e1;border-radius:8px;font-size:11px;color:#94a3b8;letter-spacing:0.05em">RENT EASY</div>
  <div style="padding:10px 18px;border:1px dashed #cbd5e1;border-radius:8px;font-size:11px;color:#94a3b8;letter-spacing:0.05em">AGENZIA NORD</div>
  <div style="padding:10px 18px;border:1px dashed #cbd5e1;border-radius:8px;font-size:11px;color:#94a3b8;letter-spacing:0.05em">BISTRO Q</div>
  <div style="padding:10px 18px;border:1px dashed #cbd5e1;border-radius:8px;font-size:11px;color:#94a3b8;letter-spacing:0.05em">EVENTI24</div>
</div>
`.trim();

  return section([row([column([
    counterRow,
    tile('html', { html_content: logosHtml }),
  ])])], { padding: 'default', style: 'muted' });
}

// ── HOW IT WORKS · 3 STEP ──────────────────────────────────────────────────
function buildHowItWorks() {
  const eyebrowHtml = `<p style="text-align:center;font-size:13px;font-weight:600;color:#dc2626;text-transform:uppercase;letter-spacing:0.08em;margin:0 0 12px">Come funziona</p>`;
  const headline = tile('headline', {
    preset: 'modern-clean',
    heading: 'Da zero a online in tre passi',
    subtitle: 'Niente SaaS da configurare, niente account esterni. Tutto succede sul tuo WordPress.',
    tag: 'h2', alignment: 'center', heading_size: 'lg',
    heading_color: '#0f172a', subtitle_color: '#64748b',
    decoration: 'line', decoration_color: '#dc2626',
    tile_padding: { top: 0, right: 0, bottom: 24, left: 0 },
  });
  const steps = [
    { num: '1', title: 'Installi OLObuild gratis', desc: 'Direttamente da WordPress.org (al lancio) o dal nostro sito. 30 secondi, zero configurazione obbligatoria. Funziona con qualunque tema WP.', time: '⏱ ~30 secondi' },
    { num: '2', title: 'Costruisci con drag &amp; drop', desc: 'Trascini i tile, scegli i colori, doppio click per editare il testo. Anteprima fedele in tempo reale. Mobile/tablet/desktop con un click.', time: '⏱ ~1 ora per la prima pagina' },
    { num: '3', title: 'Pubblichi e scali', desc: 'Quando ti serve, aggiungi OLOlang per i multilingua o OLObooking per le prenotazioni. Stesso stack, stessa interfaccia, niente migration.', time: '⏱ Pronto al traffico' },
  ];
  const cols = steps.map(s => column([
    tile('iconbox', {
      preset: 'modern-card', bg: { type: 'none' },
      icon_emoji: s.num,
      title: s.title,
      description: `${s.desc}<br><br><small style="display:inline-block;font-size:12px;color:#94a3b8;font-weight:600">${s.time}</small>`,
      link_url: '', link_text: '',
      alignment: 'left',
      icon_size: '3', icon_position: 'top',
      icon_color: '#ffffff', icon_bg_color: '#dc2626', icon_bg_shape: 'circle',
      title_font_size: '20', title_font_weight: '700',
      title_color: '#0f172a', text_color: '#475569',
      icon_gap: '18', title_gap: '10', desc_gap: '0',
      tile_padding: { top: 28, right: 24, bottom: 28, left: 24 },
      border_radius: { tl: 16, tr: 16, br: 16, bl: 16 },
      bg_type: 'color', bg_color: '#ffffff',
      shadow: 'sm',
      border: { style: 'solid', color: '#e5e7eb', width: { top: 1, right: 1, bottom: 1, left: 1 } },
    }),
  ], { width_default: '', width_medium: '1-3' }));
  const stepsRow = row(cols, { layout: '1-3,1-3,1-3', gap: 24, column_gap: 'default', vertical_align: 'top', stack_mobile: true });
  return section([row([column([
    tile('html', { html_content: eyebrowHtml }),
    headline,
    stepsRow,
  ])])], { padding: 'large' });
}

// ── USE CASES ──────────────────────────────────────────────────────────────
function buildUseCases() {
  const eyebrowHtml = `<p style="text-align:center;font-size:13px;font-weight:600;color:#dc2626;text-transform:uppercase;letter-spacing:0.08em;margin:0 0 12px">Casi reali · <em>esempi al lancio</em></p>`;
  const headline = tile('headline', {
    preset: 'modern-clean',
    heading: 'Tre modi di sostituire il tuo stack con OLOtheme',
    subtitle: '⚠ Contenuti di esempio — verranno sostituiti con dati reali post-lancio',
    tag: 'h2', alignment: 'center', heading_size: 'lg',
    heading_color: '#0f172a', subtitle_color: '#94a3b8',
    decoration: 'line', decoration_color: '#dc2626',
    tile_padding: { top: 0, right: 0, bottom: 24, left: 0 },
  });
  const cases = [
    {
      icon: '🏔', title: 'B&amp;B in Trentino',
      stack: 'Booking Engine + WPML + Mailchimp',
      metric: '−€240/mese di licenze',
      desc: 'Sostituiti tre vendor con OLOtheme. Sito 3× più veloce, calendario sincronizzato con Airbnb + Booking.com in tempo reale, niente più export Excel.',
      cta: 'Vedi Accommodation →', url: 'https://olotheme.com/prodotti/olobooking/accommodation/',
    },
    {
      icon: '⚖️', title: 'Studio notarile',
      stack: 'Calendly + sito vetrina + Mailchimp',
      metric: '−40% no-show appuntamenti',
      desc: 'Slot generator parametrico + reminder T-24h e T-1h. Clienti che si auto-prenotano senza chiamare. Tre ore al giorno recuperate.',
      cta: 'Vedi Appointments →', url: 'https://olotheme.com/prodotti/olobooking/appointments/',
    },
    {
      icon: '🧰', title: 'Agenzia WordPress',
      stack: 'Elementor Pro × 30 siti + WPML × 12 siti',
      metric: '−€3.500/anno di licenze',
      desc: 'Switch a OLObuild gratis su tutti i clienti. Stesso stack ovunque, formazione team una sola volta, supporto unico.',
      cta: 'Vedi soluzione agenzie →', url: 'https://olotheme.com/soluzioni/agenzie-web/',
    },
  ];
  const cols = cases.map(c => column([
    tile('iconbox', {
      preset: 'modern-card', bg: { type: 'none' },
      icon_emoji: c.icon,
      title: c.title,
      description: `<small style="display:block;font-size:11px;color:#94a3b8;margin:0 0 8px;text-transform:uppercase;letter-spacing:0.05em">prima: <span style="text-decoration:line-through">${c.stack}</span></small><strong style="display:block;font-size:18px;color:#16a34a;margin:0 0 10px;font-weight:700">${c.metric}</strong>${c.desc}`,
      link_url: c.url, link_text: c.cta,
      alignment: 'left',
      icon_size: '2.5', icon_position: 'top',
      icon_color: '#0f172a', icon_bg_color: '#fef3c7', icon_bg_shape: 'rounded',
      title_font_size: '20', title_font_weight: '700',
      title_color: '#0f172a', text_color: '#475569', link_color: '#dc2626',
      icon_gap: '14', title_gap: '10', desc_gap: '14',
      tile_padding: { top: 28, right: 24, bottom: 28, left: 24 },
      border_radius: { tl: 16, tr: 16, br: 16, bl: 16 },
      bg_type: 'color', bg_color: '#ffffff', shadow: 'sm',
      border: { style: 'solid', color: '#e5e7eb', width: { top: 1, right: 1, bottom: 1, left: 1 } },
    }),
  ], { width_default: '', width_medium: '1-3' }));
  const useRow = row(cols, { layout: '1-3,1-3,1-3', gap: 24, column_gap: 'default', vertical_align: 'top', stack_mobile: true });
  return section([row([column([
    tile('html', { html_content: eyebrowHtml }),
    headline,
    useRow,
  ])])], { padding: 'large' });
}

// ── TESTIMONIANZE ──────────────────────────────────────────────────────────
function buildTestimonials() {
  const eyebrowHtml = `<p style="text-align:center;font-size:13px;font-weight:600;color:#dc2626;text-transform:uppercase;letter-spacing:0.08em;margin:0 0 12px">Cosa dicono · <em>testimonianze al lancio</em></p>`;
  const headline = tile('headline', {
    preset: 'modern-clean',
    heading: 'Le voci di chi ci sta provando',
    subtitle: '⚠ Contenuti di esempio — verranno sostituiti con dati reali post-lancio',
    tag: 'h2', alignment: 'center', heading_size: 'lg',
    heading_color: '#0f172a', subtitle_color: '#94a3b8',
    decoration: 'line', decoration_color: '#dc2626',
    tile_padding: { top: 0, right: 0, bottom: 24, left: 0 },
  });
  const items = [
    { quote: 'Era il telaio che mi mancava: niente più 15 plugin a paghino che non si parlano. Tutto coerente, tutto WordPress, e <strong>OLObuild gratis non è un teaser</strong> — è davvero gratis.', author_name: 'Marco T.', author_role: 'Hotel Le Dolomiti · Trento', avatar: '', rating: '5' },
    { quote: 'Slot generator + reminder T-24h e T-1h. <strong>I no-show sono crollati</strong>. I clienti si auto-gestiscono, io recupero tre ore al giorno.', author_name: 'Lucia R.', author_role: 'Studio Notarile · Verona', avatar: '', rating: '5' },
    { quote: 'Gestiamo 32 siti clienti su OLObuild. Margini su per <strong>~€3.500/anno solo di licenze risparmiate</strong>. Stesso stack ovunque, formazione una sola volta.', author_name: 'Andrea M.', author_role: 'Agenzia Web · Milano', avatar: '', rating: '5' },
  ];
  const testimonial = tile('testimonial', {
    preset: 'modern-card', bg: { type: 'none' },
    quote: items[0].quote, author_name: items[0].author_name, author_role: items[0].author_role, avatar: '', rating: '5',
    layout: 'grid', grid_columns: 3,
    items: items,
    show_line: true, line_color: '#dc2626',
    author_position: 'bottom-left',
    avatar_size: '48', avatar_shape: 'circle',
    border_radius: '16', bg_color: '#ffffff', text_color: '#475569',
  });
  return section([row([column([
    tile('html', { html_content: eyebrowHtml }),
    headline,
    testimonial,
  ])])], { padding: 'large' });
}

// ── VALUES / 4 PROMESSE ────────────────────────────────────────────────────
function buildValues() {
  const eyebrowHtml = `<p style="text-align:center;font-size:13px;font-weight:600;color:#dc2626;text-transform:uppercase;letter-spacing:0.08em;margin:0 0 12px">Le nostre promesse</p>`;
  const headline = tile('headline', {
    preset: 'modern-clean',
    heading: 'Quattro punti fermi, senza asterischi',
    subtitle: '',
    tag: 'h2', alignment: 'center', heading_size: 'lg',
    heading_color: '#0f172a',
    decoration: 'line', decoration_color: '#dc2626',
    tile_padding: { top: 0, right: 0, bottom: 24, left: 0 },
  });
  const promises = [
    { icon: '🚫', title: 'Niente SaaS forzato',          desc: 'I dati restano sul tuo WordPress. Nessuna API obbligatoria, nessun account esterno, nessuna dipendenza che può sparire domani.' },
    { icon: '🔓', title: 'Niente lock-in',               desc: 'Contenuti in formato standard, esportabili in qualunque momento. Se cambi idea, te ne vai con tutto.' },
    { icon: '🎁', title: 'OLObuild è gratis. Per sempre.', desc: 'Il telaio non lo paghi mai. 90 tile, 11 effetti testo, 36 animazioni, form builder — tutto incluso. Per il <strong>wow-factor</strong> (22 tile Pro: AnimatedHeading, Hotspot, Lottie, Chart, ProGallery…) c\'è <strong>Pro a € 29</strong> lancio.' },
    { icon: '📂', title: 'Codice tuo',                   desc: 'Vedi cosa fa OLObuild, lo modifichi, lo porti dove vuoi. Stessa licenza di WordPress — niente "black box", niente vendor che sparisce, niente formato proprietario.' },
  ];
  const cols = promises.map(p => column([
    tile('iconbox', {
      preset: 'modern-card', bg: { type: 'none' },
      icon_emoji: p.icon, title: p.title, description: p.desc,
      link_url: '', link_text: '',
      alignment: 'left',
      icon_size: '2.2', icon_position: 'top',
      icon_color: '#dc2626', icon_bg_color: '#fef2f2', icon_bg_shape: 'circle',
      title_font_size: '18', title_font_weight: '700',
      title_color: '#0f172a', text_color: '#475569',
      icon_gap: '14', title_gap: '8', desc_gap: '0',
      tile_padding: { top: 24, right: 22, bottom: 24, left: 22 },
      border_radius: { tl: 14, tr: 14, br: 14, bl: 14 },
      bg_type: 'color', bg_color: '#ffffff', shadow: 'sm',
      border: { style: 'solid', color: '#e5e7eb', width: { top: 1, right: 1, bottom: 1, left: 1 } },
    }),
  ], { width_default: '', width_medium: '1-2' }));
  const r1 = row(cols.slice(0, 2), { layout: '1-2,1-2', gap: 20, column_gap: 'default', vertical_align: 'top', stack_mobile: true });
  const r2 = row(cols.slice(2, 4), { layout: '1-2,1-2', gap: 20, column_gap: 'default', vertical_align: 'top', stack_mobile: true });
  return section([row([column([
    tile('html', { html_content: eyebrowHtml }),
    headline,
    r1,
    r2,
  ])])], { padding: 'large' });
}

// ── SOLUTIONS QUICK ────────────────────────────────────────────────────────
function buildSolutions() {
  const eyebrowHtml = `<p style="text-align:center;font-size:13px;font-weight:600;color:#dc2626;text-transform:uppercase;letter-spacing:0.08em;margin:0 0 12px">Per settore</p>`;
  const headline = tile('headline', {
    preset: 'modern-clean',
    heading: 'Otto storie diverse, lo stesso prodotto sotto',
    subtitle: '',
    tag: 'h2', alignment: 'center', heading_size: 'lg',
    heading_color: '#0f172a',
    decoration: 'line', decoration_color: '#dc2626',
    tile_padding: { top: 0, right: 0, bottom: 24, left: 0 },
  });
  const links = [
    { icon: '🏨', label: 'Hotel & ospitalità',          url: 'https://olotheme.com/prodotti/olobooking/accommodation/' },
    { icon: '🍽️', label: 'Ristorazione',                url: 'https://olotheme.com/prodotti/olobooking/restaurants/' },
    { icon: '🏘️', label: 'Immobiliare',                 url: 'https://olotheme.com/prodotti/olobooking/real-estate/' },
    { icon: '🚗', label: 'Noleggi',                     url: 'https://olotheme.com/prodotti/olobooking/rentals/' },
    { icon: '🎫', label: 'Eventi',                      url: 'https://olotheme.com/prodotti/olobooking/events/' },
    { icon: '👔', label: 'Professionisti & cliniche',   url: 'https://olotheme.com/prodotti/olobooking/appointments/' },
    { icon: '🎓', label: 'Formazione',                  url: 'https://olotheme.com/prodotti/olotutor/' },
    { icon: '🧰', label: 'Agenzie web',                 url: 'https://olotheme.com/prodotti/olobuild/' },
  ];
  const cols = links.map(l => column([
    tile('iconbox', {
      preset: 'horizontal-row', bg: { type: 'none' },
      icon_emoji: l.icon, title: l.label, description: '',
      link_url: l.url, link_text: '',
      alignment: 'center',
      icon_size: '1.6', icon_position: 'left',
      icon_color: '#dc2626', icon_bg_color: '',
      title_font_size: '14', title_font_weight: '600', title_color: '#0f172a',
      icon_gap: '10', title_gap: '0', desc_gap: '0',
      tile_padding: { top: 14, right: 16, bottom: 14, left: 16 },
      border_radius: { tl: 10, tr: 10, br: 10, bl: 10 },
      bg_type: 'color', bg_color: '#ffffff', shadow: 'none',
      border: { style: 'solid', color: '#e5e7eb', width: { top: 1, right: 1, bottom: 1, left: 1 } },
    }),
  ], { width_default: '', width_medium: '1-4' }));
  const r1 = row(cols.slice(0, 4), { layout: '1-4,1-4,1-4,1-4', gap: 12, column_gap: 'default', vertical_align: 'middle', stack_mobile: true });
  const r2 = row(cols.slice(4, 8), { layout: '1-4,1-4,1-4,1-4', gap: 12, column_gap: 'default', vertical_align: 'middle', stack_mobile: true });
  return section([row([column([
    tile('html', { html_content: eyebrowHtml }),
    headline,
    r1,
    r2,
  ])])], { padding: 'large' });
}

// ── RESOURCES TEASER ───────────────────────────────────────────────────────
function buildResources() {
  const eyebrowHtml = `<p style="text-align:center;font-size:13px;font-weight:600;color:#dc2626;text-transform:uppercase;letter-spacing:0.08em;margin:0 0 12px">Per chi vuole capire prima</p>`;
  const headline = tile('headline', {
    preset: 'modern-clean',
    heading: 'Documentazione e paper tecnici',
    subtitle: '',
    tag: 'h2', alignment: 'center', heading_size: 'lg',
    heading_color: '#0f172a',
    decoration: 'line', decoration_color: '#dc2626',
    tile_padding: { top: 0, right: 0, bottom: 24, left: 0 },
  });
  const resources = [
    { icon: '📘', title: 'La guida OLOtheme',                desc: '13 pagine A4, whitepaper di valutazione.', cta: 'Apri →', url: 'https://olotheme.com/risorse/guida/' },
    { icon: '🔧', title: 'OLObuild Technical Paper',         desc: 'Architettura, REST API, sistema tile.',     cta: 'Leggi →', url: 'https://olotheme.com/risorse/olobuild-technical-paper/' },
    { icon: '📅', title: 'Flusso prenotazione OLObooking',   desc: 'Pipeline end-to-end.',                       cta: 'Vedi →', url: 'https://olotheme.com/risorse/olobooking-flow/' },
  ];
  const cols = resources.map(r => column([
    tile('iconbox', {
      preset: 'modern-card', bg: { type: 'none' },
      icon_emoji: r.icon, title: r.title, description: r.desc,
      link_url: r.url, link_text: r.cta,
      alignment: 'left',
      icon_size: '2.2', icon_position: 'top',
      icon_color: '#0f172a', icon_bg_color: '#f1f5f9', icon_bg_shape: 'rounded',
      title_font_size: '18', title_font_weight: '700',
      title_color: '#0f172a', text_color: '#475569', link_color: '#dc2626',
      icon_gap: '12', title_gap: '8', desc_gap: '14',
      tile_padding: { top: 24, right: 22, bottom: 24, left: 22 },
      border_radius: { tl: 14, tr: 14, br: 14, bl: 14 },
      bg_type: 'color', bg_color: '#ffffff', shadow: 'sm',
      border: { style: 'solid', color: '#e5e7eb', width: { top: 1, right: 1, bottom: 1, left: 1 } },
    }),
  ], { width_default: '', width_medium: '1-3' }));
  const resRow = row(cols, { layout: '1-3,1-3,1-3', gap: 24, column_gap: 'default', vertical_align: 'top', stack_mobile: true });
  return section([row([column([
    tile('html', { html_content: eyebrowHtml }),
    headline,
    resRow,
  ])])], { padding: 'large' });
}

// ──────────────────────────────────────────────────────────────────────────
// PARSE SORGENTE + ASSEMBLE
// ──────────────────────────────────────────────────────────────────────────
const sourcePath = path.join(__dirname, 'home-source.html');
const sourceRaw = fs.readFileSync(sourcePath, 'utf8');

const blockRe = /<!--\s*={4,}\s*([A-Z][^=]+?)\s*={4,}\s*-->([\s\S]*?)(?=<!--\s*={4,}|$)/g;
const blocks = [];
let m;
while ((m = blockRe.exec(sourceRaw)) !== null) {
  blocks.push({ title: m[1].trim(), html: m[2].trim() });
}

console.log(`Parsed ${blocks.length} blocchi dal sorgente`);

// SKIP del SCHEMA (vive in meta SEO).
// Per HERO/GALAXY 5 PRODOTTI/PRICING TEASER/FAQ → tile-native.
// Altri → tile html.

const templateSections = [];
for (const b of blocks) {
  const t = b.title;
  if (t.includes('SCHEMA')) continue;
  if (t === 'HERO') {
    templateSections.push(buildHero());
    console.log('  → HERO: tile-native');
  } else if (t.includes('SOCIAL PROOF')) {
    templateSections.push(buildSocialProof());
    console.log('  → SOCIAL PROOF: tile-native (4 counter)');
  } else if (t.includes('HOW IT WORKS')) {
    templateSections.push(buildHowItWorks());
    console.log('  → HOW IT WORKS: tile-native (3 iconbox)');
  } else if (t.includes('GALAXY')) {
    templateSections.push(buildGalaxy());
    console.log('  → GALAXY: tile-native (6 iconbox)');
  } else if (t.includes('USE CASES')) {
    templateSections.push(buildUseCases());
    console.log('  → USE CASES: tile-native (3 iconbox)');
  } else if (t.includes('TESTIMONIANZE')) {
    templateSections.push(buildTestimonials());
    console.log('  → TESTIMONIANZE: tile-native (testimonial grid)');
  } else if (t.includes('VALUES')) {
    templateSections.push(buildValues());
    console.log('  → VALUES: tile-native (4 iconbox)');
  } else if (t.includes('PRICING')) {
    templateSections.push(buildPricing());
    console.log('  → PRICING: tile-native (3 pricing)');
  } else if (t.includes('SOLUTIONS')) {
    templateSections.push(buildSolutions());
    console.log('  → SOLUTIONS: tile-native (8 iconbox)');
  } else if (t.includes('RESOURCES')) {
    templateSections.push(buildResources());
    console.log('  → RESOURCES: tile-native (3 iconbox)');
  } else if (t === 'FAQ') {
    templateSections.push(buildFAQ());
    console.log('  → FAQ: tile-native (accordion)');
  } else {
    templateSections.push(sectionHtml(b.html, { padding: 'default' }));
    console.log(`  → ${t}: tile html (fedele al sorgente)`);
  }
}

const outPath = path.join(__dirname, 'home-tiles.json');
fs.writeFileSync(outPath, JSON.stringify(templateSections), 'utf8');
console.log(`\nGenerato: ${outPath}`);
console.log(`Sezioni totali: ${templateSections.length}`);
console.log(`Bytes: ${fs.statSync(outPath).size}`);
