/**
 * megamenuTemplates.js — 40 template pronti per il tile Mega Menu di OLObuild.
 *
 * COSA SONO: ogni template è un set di `settings` con le CHIAVI REALI di
 * `src/config/elements/megamenu.js`. Le chiavi NON elencate restano ai `defaults`
 * del tile (i template contengono solo gli scostamenti = la "ricetta").
 *
 * APPLICAZIONE (BuilderInspector.applyMegamenuTemplate, apply-once):
 *   const tpl = MEGAMENU_TEMPLATES.find(t => t.id === id);
 *   tilesStore.updateTile(tileId, structuredClone(tpl.settings)); // valori sovrascrivibili
 *
 * REGOLE rispettate:
 *  - Chiavi salvate INVARIATE (nomi e formati identici a megamenu.js:
 *    spacing {top,right,bottom,left}, border-radius {tl,tr,br,bl}, range/size come stringa).
 *  - Token-first: brand via var(--olo-color-*). Hex grezzi SOLO per superfici "decise"
 *    (bar scure, oro, neon…) → se il cliente vuole, si possono ricondurre a un ruolo.
 *  - `_frontend`: estensione CSS/JS opzionale del render del componente (NON è una chiave
 *    salvata; è una nota per chi implementa). I 12 `preset` ufficiali sono valorizzati nel
 *    campo `preset`; i bespoke usano `preset: 'custom'`.
 */
const R  = (tl, tr, br, bl) => ({ tl, tr: tr ?? tl, br: br ?? tl, bl: bl ?? (tr ?? tl) });        // border-radius
const SP = (t, r, b, l)     => ({ top: t, right: r ?? t, bottom: b ?? t, left: l ?? r ?? t }); // spacing

// Ruoli colore cliente (token globali) — alias leggibili
const PRIMARY = 'var(--olo-color-primary)';
const SECOND  = 'var(--olo-color-secondary)';
const ACCENT  = 'var(--olo-color-accent)';
const DARK    = 'var(--olo-color-dark)';

export const MEGAMENU_TEMPLATES = [

  /* ════════ A · CHIARI & PROFESSIONALI ════════ */

  { id:'modern-clean', name:'Modern Clean', family:'A', preset:'modern-clean',
    notes:'Default consigliato: bianco, voci centrate, underline, accento brand.',
    settings:{
      nav_bg:'', text_color:'', active_color:PRIMARY,
      layout:'center', font_weight:'500', text_transform:'none', letter_spacing:'0',
      hover_effect:'underline', hover_effect_height:'2',
      button_mode:'last', btn_radius:R(7),
      panel_shadow:'md', panel_open_animation:'fade', panel_border_top:'3',
    }},

  { id:'compact-app', name:'Compact App', family:'A', preset:'compact-bar',
    notes:'Barra densa da web-app: bassa, gap ridotto, hover a sfondo.',
    settings:{
      active_color:PRIMARY, nav_height:'48', item_gap:'12', font_size:'14',
      bar_padding:SP(8,16,8,16), hover_effect:'background',
      button_mode:'last', btn_radius:R(6),
      panel_size:'auto', panel_open_animation:'fade', panel_shadow:'md',
    }},

  { id:'corporate-navy', name:'Corporate Navy', family:'A', preset:'custom',
    notes:'Barra navy, testo chiaro, accento ambra; CTA ambra. B2B/finanza.',
    settings:{
      nav_bg:SECOND, text_color:'#c7d2e0', active_color:ACCENT,
      layout:'left', font_weight:'500', hover_effect:'underline',
      button_mode:'last', btn_bg:ACCENT, btn_color:'#1a1206', btn_radius:R(6),
      panel_bg:'#ffffff', panel_open_animation:'fade', panel_shadow:'lg',
    }},

  { id:'tech-saas', name:'Tech SaaS', family:'A', preset:'gradient-bar',
    notes:'Chiaro, accento viola, CTA a gradiente, hover che cresce. Badge "NEW" = voce con classe.',
    settings:{
      active_color:'#7c3aed', font_weight:'600', hover_effect:'underline-grow',
      button_mode:'last', btn_bg:'linear-gradient(135deg,#7c3aed,#db2777)', btn_color:'#ffffff', btn_radius:R(8),
      panel_open_animation:'fade', panel_shadow:'md',
    },
    _frontend:'Badge "NEW" su una voce: da classe CSS della voce menu WP (badge-new). [E3 implementato]' },

  { id:'pharma-trust', name:'Pharma Trust', family:'A', preset:'custom',
    notes:'Salute/farmacia: accento verde, raggio ampio, top bar con numero verde.',
    settings:{
      active_color:'#2f8f4e', font_weight:'500', hover_effect:'underline',
      button_mode:'last', btn_bg:'#2f8f4e', btn_radius:R(14),
      panel_radius:R(14), panel_open_animation:'fade', panel_shadow:'md',
      topbar_enabled:true, topbar_left_content:'text',
      topbar_left_text:'☎ Numero verde 800 123 456 · Lun–Sab 8:30–20:00',
      topbar_bg:'#eaf6ee', topbar_text_color:'#2d6a43', topbar_link_color:'#2f8f4e',
      search_icon:true,
    }},

  { id:'editorial-hairline', name:'Editorial Hairline', family:'A', preset:'minimal-line',
    notes:'Hairline sotto la barra, voci serif, underline dal centro, CTA ghost.',
    settings:{
      nav_bg:'', active_color:PRIMARY, layout:'center', font_weight:'600',
      hover_effect:'underline',
      button_mode:'last', btn_bg:'transparent', btn_border_width:'1', btn_radius:R(0),
      panel_shadow:'sm', panel_open_animation:'fade',
    },
  },

  /* ════════ B · SCURI & CINEMATICI ════════ */

  { id:'cinema-amber', name:'Cinema Amber', family:'B', preset:'cinema-bar',
    notes:'Barra #12121a, accento ambra, voci centrate, CTA ambra.',
    settings:{
      nav_bg:'#12121a', text_color:'#e8e8ea', active_color:ACCENT,
      layout:'center', letter_spacing:'0.5', hover_effect:'highlight',
      button_mode:'last', btn_bg:ACCENT, btn_color:'#1a1206',
      panel_bg:'#15151f', panel_open_animation:'scale-center', panel_shadow:'lg',
    }},

  { id:'neon-strip', name:'Neon Strip', family:'B', preset:'neon-strip',
    notes:'Dark profondo, accento neon, hover glitch, CTA glow, pannello blur.',
    settings:{
      nav_bg:'#0b1220', text_color:'#c9d4e0', active_color:'#4ade80',
      font_size:'14', hover_effect:'glitch',
      button_mode:'last', btn_bg:'#4ade80', btn_color:'#06210f', btn_radius:R(7),
      panel_bg:'#0e1726', panel_open_animation:'blur', panel_border_top:'2',
    },
    _frontend:'CTA “glow”: box-shadow neon sul .olo-mm-btn (estensione stile).' },

  { id:'aurora-glass', name:'Aurora Glass', family:'B', preset:'glass-bar',
    notes:'Header overlay su hero a gradiente; barra glass translucida bianca.',
    settings:{
      nav_bg:'rgba(255,255,255,0.13)', text_color:'#ffffff', active_color:'#ffffff',
      header_mode:'overlay', sticky:true, sticky_bg:DARK,
      layout:'center', hover_effect:'underline',
      button_mode:'last', btn_bg:'#ffffff', btn_color:'#3b1d6e', btn_radius:R(8),
      panel_open_animation:'fade', panel_shadow:'lg',
    },
  },

  { id:'news-dark-ticker', name:'News Dark Ticker', family:'B', preset:'magazine-bold',
    notes:'Portale news: barra scura, top bar ticker rosso, voci 700 maiuscole.',
    settings:{
      nav_bg:'#11151c', text_color:'#d6dde6', active_color:PRIMARY,
      font_weight:'700', text_transform:'uppercase',
      hover_effect:'underline', button_mode:'last',
      topbar_enabled:true, topbar_left_content:'ticker',
      topbar_ticker_label:'ULTIM\'ORA:',
      topbar_ticker_items:'Builder che cambia WordPress\n12 nuovi template\nPerformance +30%',
      topbar_bg:PRIMARY, topbar_text_color:'#ffffff', topbar_link_color:'#ffffff',
      search_icon:true,
    }},

  { id:'dev-docs-mono', name:'Dev Docs Mono', family:'B', preset:'retro-terminal',
    notes:'Documentazione: dark, mono, ricerca ⌘K espansa, hover bracket.',
    settings:{
      nav_bg:'#0e1726', text_color:'#9fb3c8', active_color:'#38bdf8',
      hover_effect:'bracket', button_mode:'none',
      search_icon:true, search_position:'navbar', search_style:'expand',
    },
    _frontend:'Logo con version-chip (es. v3.34) e ricerca search_style:expand da rendere come campo inline.' },

  { id:'midnight-lux', name:'Midnight Lux', family:'B', preset:'cinema-bar',
    notes:'Premium notturno: quasi nero, oro, voci maiuscole spaziate, CTA ghost oro.',
    settings:{
      nav_bg:'#0a0a0c', text_color:'#cbb389', active_color:'#d4af37',
      layout:'center', letter_spacing:'3.5', text_transform:'uppercase', font_weight:'500',
      nav_height:'72',
      button_mode:'last', btn_bg:'transparent', btn_color:'#d4af37', btn_border_width:'1', btn_radius:R(0),
      panel_bg:'#111016', panel_open_animation:'fade', panel_shadow:'lg',
    }},

  /* ════════ C · EDITORIALI & RAFFINATI ════════ */

  { id:'magazine-bold', name:'Magazine Bold', family:'C', preset:'magazine-bold',
    notes:'Rivista: top bar ticker+social, voci 700 maiuscole, bordo navy, hover doppia linea.',
    settings:{
      nav_bg:'#ffffff', active_color:PRIMARY, layout:'left',
      font_weight:'700', text_transform:'uppercase', letter_spacing:'0.3',
      hover_effect:'double-line',
      button_mode:'last', btn_bg:SECOND, btn_radius:R(0),
      topbar_enabled:true, topbar_left_content:'ticker', topbar_right_social:true,
      topbar_ticker_label:'IN EVIDENZA:', topbar_ticker_items:'La rivoluzione del page builder',
      topbar_bg:PRIMARY, topbar_text_color:'#ffffff', topbar_link_color:'#ffffff',
      panel_open_animation:'slide-down', panel_border_top:'3',
    },
  },

  { id:'minimal-line', name:'Minimal Line', family:'C', preset:'minimal-line',
    notes:'Portfolio/lusso: trasparente, maiuscole spaziate, hover che cresce, CTA ghost.',
    settings:{
      nav_bg:'transparent', active_color:PRIMARY, layout:'center',
      text_transform:'uppercase', letter_spacing:'2.5', font_weight:'600',
      hover_effect:'underline-grow',
      button_mode:'last', btn_bg:'transparent', btn_border_width:'1', btn_radius:R(0),
      panel_shadow:'sm', panel_open_animation:'fade',
    }},

  { id:'luxury-gold', name:'Luxury Gold', family:'C', preset:'custom',
    notes:'Hotel/fine dining: barra nera, oro, voci a destra maiuscole, CTA ghost oro.',
    settings:{
      nav_bg:'#111111', text_color:'#e9d9b6', active_color:'#d4af37',
      layout:'right', letter_spacing:'2', text_transform:'uppercase', font_weight:'500',
      button_mode:'last', btn_bg:'transparent', btn_color:'#d4af37', btn_border_width:'1', btn_radius:R(0),
      panel_bg:'#1a1813', panel_open_animation:'fade', panel_shadow:'lg',
    }},

  { id:'architecture', name:'Architecture', family:'C', preset:'custom',
    notes:'Minimalismo estremo: bianco, voci mono micro, spacing larghissimo, radius 0.',
    settings:{
      nav_bg:'#ffffff', text_color:'#1a1a1a', active_color:'#1a1a1a',
      font_size:'12', text_transform:'uppercase', letter_spacing:'4', font_weight:'500',
      hover_effect:'underline', button_mode:'last', btn_bg:'transparent', btn_color:'#1a1a1a',
      btn_border_width:'1', btn_radius:R(0),
      border:{ width:SP(1,1,1,1), color:'#1a1a1a', style:'solid' },
      panel_radius:R(0), panel_shadow:'none', panel_open_animation:'fade',
    },
  },

  { id:'serif-display', name:'Serif Display', family:'C', preset:'custom',
    notes:'Food/lifestyle: fondo crema, voci serif, accento terracotta, underline dal centro.',
    settings:{
      nav_bg:'#fbf7f0', text_color:'#5c4a3a', active_color:'#a8542a',
      layout:'center', font_weight:'600', hover_effect:'underline',
      button_mode:'last', btn_bg:'#a8542a', btn_radius:R(8),
      border:{ width:SP(1,1,1,1), color:'#ece3d5', style:'solid' },
      panel_open_animation:'fade', panel_shadow:'md',
    },
  },

  { id:'kraft-eco', name:'Kraft Eco', family:'C', preset:'sticker-tape',
    notes:'Bio/artigianale: fondo kraft, accento oliva, hover che cresce, voci serif.',
    settings:{
      nav_bg:'#efe6d4', text_color:'#4f4030', active_color:'#6a7d3a',
      letter_spacing:'0.2', font_weight:'600', hover_effect:'underline-grow',
      button_mode:'last', btn_bg:'#6a7d3a', btn_radius:R(10),
      border:{ width:SP(1,1,1,1), color:'#ddd0b8', style:'solid' },
      search_icon:true, panel_open_animation:'fade', panel_shadow:'sm',
    },
  },

  /* ════════ D · ESPRESSIVI & POP ════════ */

  { id:'sticker-tape', name:'Sticker Tape', family:'D', preset:'sticker-tape',
    notes:'Pop giallo: voci a pillola, bordo nero spesso, CTA nera tonda.',
    settings:{
      nav_bg:ACCENT, text_color:'#1a1a1a', active_color:'#111111',
      font_weight:'700', hover_effect:'background',
      button_mode:'last', btn_bg:'#111111', btn_radius:R(999),
      border:{ width:SP(2,2,2,2), color:'#111111', style:'solid' },
      panel_radius:R(14), panel_open_animation:'scale-center',
    },
    _frontend:'Voce attiva “a pillola” piena (variante di hover_effect background con radius pill).' },

  { id:'playful-pastel', name:'Playful Pastel', family:'D', preset:'custom',
    notes:'App consumer: lavanda, raggi morbidi, pallino sotto, CTA viola tonda.',
    settings:{
      nav_bg:'#f3effe', text_color:'#4c3a6b', active_color:'#7c3aed',
      layout:'center', font_weight:'600', hover_effect:'dot',
      button_mode:'last', btn_bg:'#7c3aed', btn_radius:R(999),
      panel_radius:R(18), panel_open_animation:'scale-center', panel_shadow:'md',
    }},

  { id:'color-block', name:'Color Block', family:'D', preset:'gradient-bar',
    notes:'Brand audace: fondo blu pieno, testo bianco, voci maiuscole, hover a blocco.',
    settings:{
      nav_bg:'#3b5bdb', text_color:'#dbe4ff', active_color:'#ffffff',
      font_weight:'700', text_transform:'uppercase', letter_spacing:'0.3', hover_effect:'background',
      button_mode:'last', btn_bg:'#ffffff', btn_color:'#3b5bdb', btn_radius:R(8),
      panel_open_animation:'scale', panel_shadow:'lg',
    }},

  { id:'festival-gradient', name:'Festival Gradient', family:'D', preset:'tilt-bar',
    notes:'Eventi/musica: barra su gradiente, glass, hover glitch, CTA bianca.',
    settings:{
      nav_bg:'rgba(255,255,255,0.14)', text_color:'#ffffff', active_color:'#ffffff',
      header_mode:'overlay', font_weight:'700', hover_effect:'glitch',
      button_mode:'last', btn_bg:'#ffffff', btn_color:'#7c3aed', btn_radius:R(10),
      panel_open_animation:'flip', panel_shadow:'lg',
    },
    _frontend:'Micro-rotazione della barra (transform: rotate) — concetto tilt-bar.' },

  { id:'retro-terminal', name:'Retro Terminal', family:'D', preset:'retro-terminal',
    notes:'Nostalgia dev: verde fosforo su nero, mono, hover [bracket], pannello reveal.',
    settings:{
      nav_bg:'#06180d', text_color:'#5fe08a', active_color:'#9dff70',
      letter_spacing:'0.8', hover_effect:'bracket',
      button_mode:'last', btn_bg:'transparent', btn_color:'#9dff70', btn_border_width:'1', btn_radius:R(0),
      panel_bg:'#06180d', panel_open_animation:'reveal', panel_border_top:'2',
    },
  },

  { id:'brutalist-block', name:'Brutalist Block', family:'D', preset:'brutalist-block',
    notes:'Statement: bianco, mono maiuscolo, bordo 3px nero + ombra dura, hover framed.',
    settings:{
      nav_bg:'#ffffff', text_color:'#000000', active_color:'#000000',
      text_transform:'uppercase', font_weight:'600',
      hover_effect:'framed', hover_effect_color:'#000000', hover_effect_height:'2',
      button_mode:'last', btn_bg:'#000000', btn_color:'#ffffff', btn_border_width:'2', btn_radius:R(0),
      panel_radius:R(0), panel_shadow:'none', panel_border_top:'0',
      border:{ width:SP(3,3,3,3), color:'#000000', style:'solid' },
    },
    _frontend:'Ombra hard (box-shadow pieno, senza blur) sul contenitore della barra.' },

  /* ════════ E · STRUTTURE & OVERLAY ════════ */

  { id:'split-nav', name:'Split Nav', family:'E', preset:'custom',
    notes:'Logo al centro tra due metà del menu (simmetria).',
    settings:{
      logo_position:'split', active_color:PRIMARY, layout:'left',
      hover_effect:'underline', button_mode:'none',
      panel_open_animation:'fade', panel_shadow:'md',
    },
  },

  { id:'stacked-center', name:'Stacked Center', family:'E', preset:'custom',
    notes:'Logo sopra, menu centrato sotto; maiuscole spaziate, underline dal centro.',
    settings:{
      logo_position:'stacked', logo_gap:'10', layout:'center',
      text_transform:'uppercase', letter_spacing:'2', font_weight:'600',
      hover_effect:'underline', button_mode:'none',
    },
  },

  { id:'glass-on-photo', name:'Glass on Photo', family:'E', preset:'glass-bar',
    notes:'Header overlay su hero foto; barra glass, sticky che diventa solido on-scroll.',
    settings:{
      nav_bg:'rgba(255,255,255,0.13)', text_color:'#ffffff', active_color:'#ffffff',
      header_mode:'overlay', sticky:true, sticky_bg:DARK, sticky_shadow:true,
      layout:'center', hover_effect:'underline',
      button_mode:'last', btn_bg:'#ffffff', btn_color:DARK, btn_radius:R(8),
      panel_open_animation:'fade', panel_shadow:'lg',
    }},

  { id:'ecommerce', name:'E-commerce', family:'E', preset:'custom',
    notes:'Shop: top bar promo+outlet, ricerca espansa, carrello WooCommerce, accento teal.',
    settings:{
      active_color:'#0f7b6c', layout:'left',
      search_icon:true, search_position:'navbar', search_style:'expand',
      button_mode:'last', btn_bg:SECOND, btn_radius:R(8),
      topbar_enabled:true, topbar_left_content:'text',
      topbar_left_text:'Spedizione gratuita sopra 49€ · Reso facile 30 giorni',
      topbar_right_cart:true, topbar_right_cta_label:'Outlet -50%', topbar_right_cta_url:'#',
      topbar_bg:'#0f7b6c', topbar_text_color:'#d2f0ea', topbar_link_color:'#ffffff',
      topbar_right_cta_bg:'#ffffff', topbar_right_cta_color:'#0f7b6c',
    }},

  { id:'mega-panel-open', name:'Mega Panel Open', family:'E', preset:'modern-clean',
    notes:'Mostra il mega-pannello: colonne dal menu WP con descrizioni + linea accento.',
    settings:{
      active_color:PRIMARY, layout:'left',
      mega_mode:'auto', panel_columns:'4', show_descriptions:true,
      panel_size:'container', panel_open_animation:'fade', panel_shadow:'md',
      panel_border_top:'3', panel_padding:SP(32,32,32,32),
      button_mode:'last',
    }},

  { id:'search-overlay', name:'Search Overlay', family:'E', preset:'custom',
    notes:'Ricerca al centro dell\'esperienza: barra scura, overlay/scorciatoia.',
    settings:{
      nav_bg:'#0e1726', text_color:'#aebbcb', active_color:'#ffffff',
      search_icon:true, search_position:'navbar', search_style:'overlay',
      button_mode:'none', hover_effect:'underline',
    },
    _frontend:'search_style “overlay” da rendere (campo a tutta barra + scorciatoia “/”). [E1 implementato]' },

  /* ════════ F · SPERIMENTALI & INATTESI ════════ */

  { id:'command-palette', name:'Command Palette', family:'F', preset:'custom',
    notes:'Menu reinterpretato come ricerca-comandi (⌘K) con dropdown risultati.',
    settings:{
      active_color:'#6d28d9', search_icon:true, search_position:'navbar', search_style:'command',
      button_mode:'last', btn_bg:'#6d28d9', btn_radius:R(10),
    },
    _frontend:'Pattern command-palette: input centrale + dropdown risultati (search_style "command"). [E1 implementato]' },

  { id:'marquee-statement', name:'Marquee Statement', family:'F', preset:'custom',
    notes:'Le voci scorrono come un ticker; font display, voce attiva in outline.',
    settings:{
      nav_bg:'#0c0c0e', text_color:'#f4f4f5', active_color:ACCENT,
      button_mode:'last', btn_bg:ACCENT, btn_color:'#1a1206', font_weight:'700', text_transform:'uppercase',
    },
    _frontend:'Nav scorrevole (marquee): track animato in translateX + mask ai bordi.' },

  { id:'editorial-index', name:'Editorial Index', family:'F', preset:'minimal-line',
    notes:'Voci numerate 01–05 (indice), serif, allineate a destra, hover che cresce.',
    settings:{
      active_color:SECOND, layout:'right', letter_spacing:'0.2', item_gap:'26',
      hover_effect:'underline-grow',
      button_mode:'last', btn_bg:'transparent', btn_border_width:'1', btn_radius:R(0),
    },
    _frontend:'Numerazione 01-05 davanti alle voci (counter CSS o numero dal menu WP).' },

  { id:'departure-board', name:'Departure Board', family:'F', preset:'custom',
    notes:'Estetica tabellone split-flap: celle mono ambra su nero.',
    settings:{
      nav_bg:'#0d0d0d', text_color:'#ffb703', active_color:'#ffd166', item_gap:'10',
      button_mode:'last', btn_bg:'#ffb703', btn_color:'#111111', btn_radius:R(3),
    },
    _frontend:'Voci come celle flap: ogni .olo-mm-nav-link con sfondo scuro, riga centrale e ombre interne.' },

  { id:'memphis-80s', name:'Memphis 80s', family:'F', preset:'sticker-tape',
    notes:'Pop anni 80: bordo spesso + ombra dura, confetti geometrici, hover a sfondo.',
    settings:{
      nav_bg:'#ffffff', text_color:'#111111', active_color:'#7c3aed', font_weight:'800',
      hover_effect:'background',
      button_mode:'last', btn_bg:'#ff4d6d', btn_radius:R(10),
      border:{ width:SP(3,3,3,3), color:'#111111', style:'solid' },
    },
    _frontend:'Decori geometrici (cerchi/triangoli/stripe) attorno alla barra + ombra dura colorata: livello decorativo nel render.' },

  { id:'notebook-hand', name:'Notebook Hand', family:'F', preset:'custom',
    notes:'Scritto a mano: righe da quaderno, voci handwriting, underline tratteggiato.',
    settings:{
      nav_bg:'#fffdf3', text_color:'#2a2a2a', active_color:'#2563eb',
      layout:'center', item_gap:'40', hover_effect:'underline',
      button_mode:'last', btn_bg:'#2563eb', btn_radius:R(12),
      border:{ width:SP(1,1,1,1), color:'#e3dcc8', style:'solid' },
    },
    _frontend:'Righe orizzontali di sfondo + underline tratteggiato (handwriting via typography_preset).' },

  { id:'bauhaus-blocks', name:'Bauhaus Blocks', family:'F', preset:'custom',
    notes:'Bauhaus: forme primarie nel logo, voci maiuscole, voce attiva a blocco pieno.',
    settings:{
      nav_bg:'#f3efe6', text_color:'#111111', active_color:'#1c54b2',
      text_transform:'uppercase', letter_spacing:'0.4', font_weight:'700', hover_effect:'background',
      button_mode:'last', btn_bg:'#f4c20d', btn_color:'#111111', btn_radius:R(0),
      border:{ width:SP(2,2,2,2), color:'#111111', style:'solid' },
    },
    _frontend:'Marchio geometrico (cerchio+quadrato) accanto al logo.' },

  { id:'y2k-aqua', name:'Y2K Aqua', family:'F', preset:'glass-bar',
    notes:'Nostalgia 2000: barra gel aqua, bottone glossy, raggi morbidi.',
    settings:{
      nav_bg:'linear-gradient(#eafaff,#bfe9ff 47%,#9ad8f5 53%,#d4f1ff)',
      text_color:'#0a3b5c', active_color:'#1f9fd8',
      hover_effect:'underline', button_mode:'last',
      btn_bg:'linear-gradient(#86e3ff,#1f9fd8)', btn_color:'#ffffff', btn_radius:R(999),
    },
    _frontend:'Riflessi gel: inset highlight su barra e CTA (box-shadow inset).' },

  { id:'tag-cloud', name:'Tag Cloud', family:'F', preset:'custom',
    notes:'Voci a nuvola con dimensioni variabili; alcune a pillola colorata.',
    settings:{
      active_color:PRIMARY, item_gap:'10', hover_effect:'background',
      button_mode:'last', btn_bg:PRIMARY, btn_radius:R(8),
    },
    _frontend:'Dimensione per-voce variabile + alcune voci “a pillola” (font-size e bg per item, da classe della voce menu WP).' },

  { id:'mosaic-mega', name:'Mosaic Mega', family:'F', preset:'modern-clean',
    notes:'Mega-pannello come mosaico di immagini + card promo (visual menu).',
    settings:{
      active_color:PRIMARY, layout:'center',
      mega_mode:'auto', panel_columns:'4', panel_size:'container',
      panel_open_animation:'fade', panel_shadow:'lg', panel_border_top:'3',
      panel_padding:SP(28,30,28,30), button_mode:'last',
      search_icon:true,
    },
    _frontend:'Tipo-colonna “immagini/promo” nel megapanel-map (griglia di thumbnail + card in evidenza). [E2 implementato via classe mega-promo]' },

];

export default MEGAMENU_TEMPLATES;
