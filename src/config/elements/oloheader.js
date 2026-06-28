import { t } from '@/i18n';

/**
 * Tile «Mega Menu / Site Header» (slug: oloheader) — famiglia Header / Navigation.
 *
 * Porting 1:1 della demo `menu-demo.html` (pacchetto OLOtheme): barra "pill"
 * flottante con brand, nav primaria + UNA voce mega-menu (rail + colonne prodotti
 * + banner footer), link featured, ricerca, selettore lingua, CTA e mobile sheet.
 *
 * Il MEGA-MENU è il cuore: ancorato alla barra (non al trigger), apertura su
 * hover con fallback click, generato da un repeater di prodotti raggruppati per
 * colonna (campo `group`) + rail + footer. Vedi class-oloheader-tile.php per la
 * resa frontend autorevole (markup + CSS + JS lift-and-shift dalla demo).
 *
 * ⚠️ Il logo `olollottie-q.png` è il marchio OLOlottie (non esiste "OLOcalendar").
 */

// Base URL del plugin per i loghi ufficiali bundled in assets/img/menu/.
const PURL = (typeof window !== 'undefined' && window.oloData && window.oloData.pluginUrl) || '';
const LOGO = (f) => PURL + 'assets/img/menu/' + f;

export default {
  type: 'oloheader',
  name: t('Mega Menu / Site Header'),
  icon: 'dashicons-menu-alt3',
  category: 'header',

  defaults: {
    // ── Brand ──
    brand_logo:        LOGO('olotheme-orizz.png'),
    brand_logo_white:  '',
    brand_height:      25,
    brand_url:         '/',

    // ── Stile barra ──
    bar_style:         'pill',          // pill | full
    bar_max_width:     1200,
    bar_top_offset:    22,
    bar_sticky:        true,
    sticky_show_on_up: true,
    sticky_bg:         '',
    sticky_shadow:     true,
    sticky_shrink:     false,
    bar_blur:          true,
    bar_radius:        100,             // 100 = pill
    bar_shadow:        'md',            // none | sm | md | lg
    bar_bg:            '#FFFFFF',
    bar_text:          '#5A6076',       // colore voci nav (ink-2 della demo)
    bar_text_hover:    '#1F2330',       // ink
    mobile_breakpoint: 1040,

    // ── Nav primaria (repeater) ──
    // type: 'link' | 'mega' → la prima voce 'mega' apre l'unico pannello mega.
    nav_items: [
      { label: 'Prodotti', url: '#', type: 'mega' },
      { label: 'Temi',     url: '/temi',    type: 'link' },
      { label: 'Prezzi',   url: '/prezzi',  type: 'link' },
      { label: 'Risorse',  url: '/risorse', type: 'link' },
    ],

    // ── Mega-menu · feature rail ──
    rail_show:        true,
    rail_badge:       'Ecosistema OLO',
    rail_title:       'Un motore, otto prodotti',
    rail_text:        'Stessa anima olonica per costruire, gestire e far crescere il tuo sito WordPress. Senza SaaS, senza lock-in.',
    rail_cta1_label:  'Scopri la Suite OLO',
    rail_cta1_url:    '/suite',
    rail_cta2_label:  'Confronta i piani',
    rail_cta2_url:    '/prezzi',
    rail_bg:          '',               // vuoto = gradiente default della demo

    // ── Mega-menu · prodotti (repeater) ──
    // Ogni prodotto appartiene a una colonna tramite `group` (titolo colonna).
    mega_columns: 2,
    mega_products: [
      { group: 'Costruisci',        logo: LOGO('olobuild-q.png'),    name: 'OLObuild',    desc: 'Page builder · 187 tile drag & drop', url: '/prodotti/olobuild',    soon: false },
      { group: 'Costruisci',        logo: LOGO('olotheme-q.png'),    name: 'OLOtheme',    desc: 'Temi pronti per ogni settore',        url: '/prodotti/olotheme',    soon: false },
      { group: 'Costruisci',        logo: LOGO('olollottie-q.png'),  name: 'OLOlottie',   desc: 'Animazioni Lottie, senza codice',     url: '/prodotti/olollottie',  soon: false },
      { group: 'Gestisci & cresci', logo: LOGO('olobooking-q.png'),  name: 'OLObooking',  desc: 'Prenotazioni · 6 verticali',          url: '/prodotti/olobooking',  soon: false },
      { group: 'Gestisci & cresci', logo: LOGO('ololang-q.png'),     name: 'OLOlang',     desc: 'Multilingua nativo · 28 lingue',      url: '/prodotti/ololang',     soon: false },
      { group: 'Gestisci & cresci', logo: LOGO('olosecurity-q.png'), name: 'OLOsecurity', desc: 'Sicurezza, firewall e backup',        url: '/prodotti/olosecurity', soon: false },
    ],

    // ── Mega-menu · banner footer ──
    mega_footer_show:  true,
    mega_footer_logos: [
      { logo: LOGO('olotour-q.png') },
      { logo: LOGO('olotutor-q.png') },
    ],
    mega_footer_title: 'In arrivo · OLOtour & OLOtutor',
    mega_footer_text:  'Tour virtuali 360° e corsi online. Pagina prodotto già online.',
    mega_footer_cta_label: "Vedi l'anteprima",
    mega_footer_cta_url:   '/prodotti',

    // ── Link featured (dopo il divider) ──
    featured_show:  true,
    featured_icon:  'bolt',
    featured_label: 'Suite OLO',
    featured_url:   '/suite',

    // ── Ricerca ──
    search_show:        true,
    search_placeholder: 'Cerca temi, prodotti, guide…',
    search_url:         '/cerca',
    search_shortcuts: [
      { label: 'Temi popolari',  url: '/temi' },
      { label: 'OLObuild',       url: '/prodotti/olobuild' },
      { label: 'Prezzi',         url: '/prezzi' },
      { label: 'Documentazione', url: '/risorse' },
    ],

    // ── Selettore lingua ──
    lang_show:    true,
    lang_globe:   true,
    lang_current: 'it',
    lang_bind_ololang: true,
    languages: [
      { code: 'it', label: 'Italiano',   url: '/' },
      { code: 'en', label: 'English',    url: '/en/' },
      { code: 'es', label: 'Español',    url: '/es/' },
      { code: 'fr', label: 'Français',   url: '/fr/' },
      { code: 'de', label: 'Deutsch',    url: '/de/' },
      { code: 'pt', label: 'Português',  url: '/pt/' },
    ],

    // ── CTA ──
    cta_show:  true,
    cta_label: 'Sandbox',
    cta_url:   '/sandbox',
    cta_style: 'navy',    // navy | royal | outline

    // ── Interazioni ──
    open_mega_on:  'hover',   // hover | click
    mega_animation: 'fade-slide', // fade-slide | none
    close_on_esc:  true,
  },

  // ═══ CONTENUTO ════════════════════════════════════════════════════════════
  fields: [
    { type: 'separator', label: t('Brand') },
    { key: 'brand_logo',       label: t('Logo'),                 type: 'image' },
    { key: 'brand_logo_white', label: t('Logo bianco (barre scure)'), type: 'image' },
    { key: 'brand_height',     label: t('Altezza logo (px)'),    type: 'range', min: 14, max: 60, step: 1 },
    { key: 'brand_url',        label: t('Link logo'),            type: 'link' },

    { type: 'separator', label: t('Nav primaria') },
    { key: 'nav_items', label: t('Voci di menu'), type: 'content-items',
      itemLabel: t('Voce'),
      newItemDefaults: { label: 'Nuova voce', url: '#', type: 'link' },
      itemFields: [
        { key: 'label', label: t('Etichetta'), type: 'text' },
        { key: 'url',   label: t('Link'),      type: 'link' },
        { key: 'type',  label: t('Tipo'),      type: 'select', options: [
          { value: 'link', label: t('Link semplice') },
          { value: 'mega', label: t('Mega-menu') },
        ]},
      ],
    },

    { type: 'separator', label: t('Mega-menu · Feature rail') },
    { key: 'rail_show',       label: t('Mostra rail'),        type: 'toggle' },
    { key: 'rail_badge',      label: t('Badge'),              type: 'text',     condition: { field: 'rail_show', op: 'eq', value: true } },
    { key: 'rail_title',      label: t('Titolo'),             type: 'text',     condition: { field: 'rail_show', op: 'eq', value: true } },
    { key: 'rail_text',       label: t('Testo'),              type: 'textarea', condition: { field: 'rail_show', op: 'eq', value: true } },
    { key: 'rail_cta1_label', label: t('CTA primaria — testo'), type: 'text',   condition: { field: 'rail_show', op: 'eq', value: true } },
    { key: 'rail_cta1_url',   label: t('CTA primaria — link'),  type: 'link',   condition: { field: 'rail_show', op: 'eq', value: true } },
    { key: 'rail_cta2_label', label: t('Link secondario — testo'), type: 'text', condition: { field: 'rail_show', op: 'eq', value: true } },
    { key: 'rail_cta2_url',   label: t('Link secondario — link'),  type: 'link', condition: { field: 'rail_show', op: 'eq', value: true } },

    { type: 'separator', label: t('Mega-menu · Prodotti') },
    { key: 'mega_products', label: t('Prodotti'), type: 'content-items',
      itemLabel: t('Prodotto'),
      newItemDefaults: { group: 'Costruisci', logo: '', name: 'Prodotto', desc: '', url: '#', soon: false },
      itemFields: [
        { key: 'group', label: t('Colonna (gruppo)'), type: 'text' },
        { key: 'logo',  label: t('Logo (*-q.png)'),   type: 'image' },
        { key: 'name',  label: t('Nome (alt / a11y)'), type: 'text' },
        { key: 'desc',  label: t('Descrizione'),      type: 'text' },
        { key: 'url',   label: t('Link'),             type: 'link' },
        { key: 'soon',  label: t('Badge "In arrivo"'), type: 'toggle' },
      ],
    },

    { type: 'separator', label: t('Mega-menu · Banner footer') },
    { key: 'mega_footer_show', label: t('Mostra banner'), type: 'toggle' },
    { key: 'mega_footer_logos', label: t('Loghi'), type: 'content-items',
      itemLabel: t('Logo'),
      newItemDefaults: { logo: '' },
      condition: { field: 'mega_footer_show', op: 'eq', value: true },
      itemFields: [
        { key: 'logo', label: t('Logo'), type: 'image' },
      ],
    },
    { key: 'mega_footer_title',     label: t('Titolo'),     type: 'text', condition: { field: 'mega_footer_show', op: 'eq', value: true } },
    { key: 'mega_footer_text',      label: t('Testo'),      type: 'text', condition: { field: 'mega_footer_show', op: 'eq', value: true } },
    { key: 'mega_footer_cta_label', label: t('CTA — testo'), type: 'text', condition: { field: 'mega_footer_show', op: 'eq', value: true } },
    { key: 'mega_footer_cta_url',   label: t('CTA — link'),  type: 'link', condition: { field: 'mega_footer_show', op: 'eq', value: true } },

    { type: 'separator', label: t('Link featured') },
    { key: 'featured_show',  label: t('Mostra'),     type: 'toggle' },
    { key: 'featured_icon',  label: t('Icona'),      type: 'icon', condition: { field: 'featured_show', op: 'eq', value: true } },
    { key: 'featured_label', label: t('Etichetta'),  type: 'text', condition: { field: 'featured_show', op: 'eq', value: true } },
    { key: 'featured_url',   label: t('Link'),       type: 'link', condition: { field: 'featured_show', op: 'eq', value: true } },

    { type: 'separator', label: t('Ricerca') },
    { key: 'search_show',        label: t('Mostra'),      type: 'toggle' },
    { key: 'search_placeholder', label: t('Placeholder'), type: 'text', condition: { field: 'search_show', op: 'eq', value: true } },
    { key: 'search_url',         label: t('URL ricerca'), type: 'link', condition: { field: 'search_show', op: 'eq', value: true } },
    { key: 'search_shortcuts', label: t('Scorciatoie'), type: 'content-items',
      itemLabel: t('Scorciatoia'),
      newItemDefaults: { label: 'Nuova', url: '#' },
      condition: { field: 'search_show', op: 'eq', value: true },
      itemFields: [
        { key: 'label', label: t('Etichetta'), type: 'text' },
        { key: 'url',   label: t('Link'),      type: 'link' },
      ],
    },

    { type: 'separator', label: t('Selettore lingua') },
    { key: 'lang_show',  label: t('Mostra'),       type: 'toggle' },
    { key: 'lang_globe', label: t('Icona globo'),  type: 'toggle', condition: { field: 'lang_show', op: 'eq', value: true } },
    { key: 'lang_bind_ololang', label: t('Auto-popola da OLOlang (se attivo)'), type: 'toggle', condition: { field: 'lang_show', op: 'eq', value: true } },
    { key: 'lang_current', label: t('Lingua corrente (codice)'), type: 'text', condition: { field: 'lang_show', op: 'eq', value: true } },
    { key: 'languages', label: t('Lingue'), type: 'content-items',
      itemLabel: t('Lingua'),
      newItemDefaults: { code: 'xx', label: 'Lingua', url: '/' },
      condition: { field: 'lang_show', op: 'eq', value: true },
      itemFields: [
        { key: 'code',  label: t('Codice (es. it)'), type: 'text' },
        { key: 'label', label: t('Etichetta'),       type: 'text' },
        { key: 'url',   label: t('Link'),            type: 'link' },
      ],
    },

    { type: 'separator', label: t('CTA') },
    { key: 'cta_show',  label: t('Mostra'),    type: 'toggle' },
    { key: 'cta_label', label: t('Etichetta'), type: 'text', condition: { field: 'cta_show', op: 'eq', value: true } },
    { key: 'cta_url',   label: t('Link'),      type: 'link', condition: { field: 'cta_show', op: 'eq', value: true } },
  ],

  // ═══ STILE ════════════════════════════════════════════════════════════════
  styleFields: [
    { type: 'separator', label: t('Stile barra') },
    { key: 'bar_style', label: t('Stile'), type: 'select', options: [
      { value: 'pill', label: t('Pill flottante') },
      { value: 'full', label: t('Full-width') },
    ]},
    { key: 'bar_max_width',  label: t('Larghezza max (px)'), type: 'range', min: 800, max: 1600, step: 20 },
    { key: 'bar_top_offset', label: t('Offset dal top (px)'), type: 'range', min: 0, max: 60, step: 1, condition: { field: 'bar_style', op: 'eq', value: 'pill' } },
    { key: 'bar_sticky', label: t('Sticky (segue lo scroll)'), type: 'toggle' },
    { key: 'sticky_show_on_up', label: t('Nascondi giù / riappari su'), type: 'toggle', condition: { field: 'bar_sticky', op: 'eq', value: true } },
    { key: 'sticky_shadow', label: t('Ombra quando agganciata'), type: 'toggle', condition: { field: 'bar_sticky', op: 'eq', value: true } },
    { key: 'sticky_shrink', label: t('Riduci quando agganciata'), type: 'toggle', condition: { field: 'bar_sticky', op: 'eq', value: true } },
    { key: 'sticky_bg', label: t('Sfondo quando agganciata'), type: 'color', condition: { field: 'bar_sticky', op: 'eq', value: true } },
    { key: 'bar_blur',   label: t('Blur sfondo'),  type: 'toggle' },
    { key: 'bar_radius', label: t('Raggio angoli (px)'), type: 'range', min: 0, max: 100, step: 1 },
    { key: 'bar_shadow', label: t('Ombra'), type: 'select', options: [
      { value: 'none', label: t('Nessuna') },
      { value: 'sm',   label: t('Leggera') },
      { value: 'md',   label: t('Media') },
      { value: 'lg',   label: t('Grande') },
    ]},
    { key: 'bar_bg',         label: t('Colore sfondo barra'), type: 'color' },
    { key: 'bar_text',       label: t('Colore testo voci'),   type: 'color' },
    { key: 'bar_text_hover', label: t('Colore testo (hover)'), type: 'color' },
    { key: 'mobile_breakpoint', label: t('Breakpoint mobile (px)'), type: 'range', min: 768, max: 1280, step: 8 },

    { type: 'separator', label: t('Mega-menu') },
    { key: 'mega_columns', label: t('N. colonne prodotti'), type: 'range', min: 1, max: 3, step: 1 },
    { key: 'rail_bg', label: t('Colore rail (vuoto = gradiente)'), type: 'color' },

    { type: 'separator', label: t('CTA') },
    { key: 'cta_style', label: t('Stile CTA'), type: 'select', options: [
      { value: 'navy',    label: t('Navy (pieno)') },
      { value: 'royal',   label: t('Royal (blu)') },
      { value: 'outline', label: t('Outline') },
    ]},

    { type: 'separator', label: t('Interazioni') },
    { key: 'open_mega_on', label: t('Apertura mega'), type: 'select', options: [
      { value: 'hover', label: t('Hover (desktop)') },
      { value: 'click', label: t('Click') },
    ]},
    { key: 'mega_animation', label: t('Animazione'), type: 'select', options: [
      { value: 'fade-slide', label: t('Fade + slide') },
      { value: 'none',       label: t('Nessuna') },
    ]},
    { key: 'close_on_esc', label: t('Chiudi con Esc e click esterno'), type: 'toggle' },
  ],
};
