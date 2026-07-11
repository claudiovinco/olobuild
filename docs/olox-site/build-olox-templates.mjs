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

/* ==================================================================== */
/* HOME EXPERIENCE                                                       */
/* ==================================================================== */
const home = {
  title: 'OLOtheme · Experience',
  slug: 'olotheme-experience',
  content: page([
    tile('oloxhome', {
      logo: LOGO('olotheme'),
      intro_kicker: 'OLOtheme · suite WordPress',
      olw_text: 'olonica',
      intro_title: 'Un telaio. Sei prodotti. <em>Nessuna catena.</em>',
      intro_sub: 'Niente SaaS, niente lock-in, niente cloud altrui: tutto vive <strong>sul tuo hosting</strong>, in GPL, scritto a Trento. Scorri: ogni fermata è un prodotto.',
      intro_cta1: 'Inizia il viaggio →',
      intro_cta2: 'Contatti',
      marquee_items: ['no SaaS', 'GPL', '187 tile', '28 lingue', '6 verticali booking', '100% locale', 'made in Trento'].map((text) => ({ text })),
      op_kicker: 'olos · intero e parte',
      op_title: 'La cellula <em>olonica</em>',
      op_p1: 'Un <strong>olone</strong> è qualcosa che è insieme <strong>un tutto e una parte</strong>: completo da solo, più forte dentro un organismo. OLOtheme è costruito così, ogni prodotto è una cellula autonoma che funziona da sola, ma condivide telaio, dati e lingua con le altre.',
      op_p2: 'Niente monolite: <strong>i prodotti si uniscono a seconda della battaglia</strong> da affrontare, e si sciolgono quando non servono.',
      battles: [
        { q: 'Aprire un B&B', chips: 'build,booking,lang' },
        { q: 'Respingere un attacco', chips: 'secur' },
        { q: 'Vendere all’estero', chips: 'build,lang' },
        { q: 'Far visitare un immobile a distanza', chips: 'tour,booking' },
        { q: 'Portare i corsi online', chips: 'tutor,booking,lang' },
      ],
      panels: [
        { color: 'build', label: 'OLObuild', logo: LOGO('olobuild'), kicker: 'Il telaio · page builder', title_html: 'Costruisce come un <em>cantiere</em>', sub_html: 'Mattone su mattone: <strong>187 tile in 12 famiglie</strong>, tutti auto-discovered, con animazioni ed effetti di serie. <strong>La Free (100+ tile) vale quanto i builder Pro a pagamento della concorrenza</strong>; Pro sblocca l’intera libreria.', tags: '€0 free · 100+ tile|36 animazioni|Woo nativo|dark mode', cta_text: 'Entra nel cantiere', cta_url: P('olobuild'), scene: 'wall', coord: 'grid · 44×44 · lot 187' },
        { color: 'booking', label: 'OLObooking', logo: LOGO('olobooking'), kicker: 'Prenotazioni · 6 verticali', title_html: 'Un motore che riempie <em>l’agenda</em>', sub_html: 'Camere, tavoli, appuntamenti, eventi, noleggi, immobili: <strong>una sola configurazione</strong> e il motore diventa il tuo mestiere. Con caparra anti no-show e zero commissioni.', tags: '6 verticali|anti no-show|QR access|0% commissioni', cta_text: 'Apri il calendario', cta_url: P('olobooking'), scene: 'cal', coord: 'occupancy feed · live' },
        { color: 'lang', label: 'OLOlang', logo: LOGO('ololang'), kicker: 'Multilingua nativo', title_html: 'Lo stesso sito, <em>28 voci</em>', sub_html: 'DeepL + traduttore IA, glossario e memoria di traduzione. Contenuti, menu e stringhe tradotti <strong>via database</strong>, con hreflang, URL localizzati e sitemap per ogni lingua.', tags: '28 lingue|DeepL + IA|SEO hreflang|a vita con Pro', cta_text: 'Cambia lingua', cta_url: P('ololang'), scene: 'lang', coord: 'hreflang × 28' },
        { color: 'secur', label: 'OLOsecurity', logo: LOGO('olosecurity'), kicker: 'Sicurezza · 100% locale', title_html: 'Un radar che non <em>dorme mai</em>', sub_html: 'Firewall OWASP, 2FA, scanner anti-webshell e bonifica guidata dal pannello <strong>Sentinel</strong>. Tutto elaborato <strong>sul tuo server</strong>: il traffico non finisce in nessun cloud altrui.', tags: '100% locale|mini-WAF|TOTP 2FA|Plugin Check 0/0', cta_text: 'Accendi il radar', cta_url: P('olosecurity'), scene: 'radar', coord: 'perimetro · armato' },
        { color: 'tour', label: 'OLOtour', logo: LOGO('olotour'), kicker: 'Tour virtuali · in arrivo', title_html: 'Guarda dentro, <em>prima di entrare</em>', sub_html: 'Panorami sferici e HDRI (Polyhaven, Street View), <strong>hot-spot cliccabili</strong>, ambienti collegati, fruizione VR. Il sopralluogo diventa parte del sito, e finisce sul bottone “prenota”.', tags: '360°|hot-spot|multi-stanza|VR ready', cta_text: 'Affaccia lo sguardo', cta_url: P('olotour'), scene: 'pano', coord: 'lat 46.07 · lon 11.12 · trento' },
        { color: 'tutor', label: 'OLOtutor', logo: LOGO('olotutor'), kicker: 'Formazione · in arrivo', title_html: 'Sali di livello, <em>lezione dopo lezione</em>', sub_html: 'Corsi, quiz, punti e badge, registro voti e certificati, dentro il tuo WordPress. <strong>Gli allievi restano tuoi</strong>, non di un marketplace che ti mette in fila coi concorrenti.', tags: 'LMS|quiz & badge|certificati|area allievi', cta_text: 'Iscriviti all’idea', cta_url: P('olotutor'), scene: 'course', coord: 'syllabus · v1 · 4 lezioni' },
      ],
      outro_kicker: 'Capolinea · si scende',
      outro_title: 'Tutto questo, <em>sul tuo hosting</em>',
      outro_sub: 'GPL · niente SaaS · GDPR in casa · 30 giorni di rimborso su OLObuild Pro. Ogni fermata ha la sua pagina di approfondimento.',
      outro_fine: 'OLOtheme · made in Trento · no SaaS · nessuna catena',
      mad_doc: 'modulo · OLO-CNT-07',
      mad_line: 'linea diretta · Trento',
      mad_intro: 'Ciao, mi chiamo',
      mad_nome_ph: 'nome e cognome',
      mad_mid: 'e il mio sito sogna di diventare',
      mad_picks: [
        { label: 'cantiere', value: 'un cantiere', color: 'build' },
        { label: 'agenda piena', value: 'un’agenda piena', color: 'booking' },
        { label: 'poliglotta', value: 'poliglotta', color: 'lang' },
        { label: 'fortezza', value: 'una fortezza', color: 'secur' },
        { label: 'tour 360°', value: 'un tour 360°', color: 'tour' },
        { label: 'aula', value: 'un’aula', color: 'tutor' },
      ],
      mad_pre_mail: 'Scrivetemi a',
      mad_mail_ph: 'nome@dominio.it',
      mad_end: ', promesso, niente catene.',
      mad_btn: 'Timbra e invia ▾',
      mad_note: 'il timbro apre la tua mail già compilata',
      mad_stamp: 'Ricevuto ◦ OLOtheme',
      mad_mailto: 'info@olotheme.com',
      hint_desktop: 'Scrolla in basso', hint_desktop2: 'si va a destra',
      hint_mobile: 'Scorri', hint_mobile2: 'una fermata alla volta',
      credits_html: 'OLOtheme by <a href="https://clod.eu" target="_blank" rel="noopener">clod.eu</a> | @2026 | sito introduttivo | <a href="mailto:info@olotheme.com">info@olotheme.com</a>',
    }),
  ]),
};

/* ==================================================================== */
/* OLOBUILD                                                              */
/* ==================================================================== */
const buildPage = {
  title: 'OLObuild · Il cantiere del tuo sito',
  slug: 'olobuild',
  content: page([
    tile('oloxhero', {
      accent: 'build', bg_variant: 'build', logo: LOGO('olobuild'),
      kicker: 'Il telaio · page builder olonico',
      title_html: 'Mattone su <em>mattone.</em>', title_fx: 'drop',
      sub_html: '<strong>187 tile in 12 famiglie</strong>, auto-discovered, con animazioni ed effetti di serie. Il cantiere è aperto: la Free, con <strong>oltre 100 tile</strong>, è al livello delle versioni Pro a pagamento della concorrenza.',
      tags: [
        { text: '€0 free · 100+ tile', hot: true }, { text: '36 animazioni', hot: false },
        { text: '11 effetti testo', hot: false }, { text: 'Woo nativo', hot: false },
      ],
      cta1_text: 'Guarda il cantiere ↓', cta1_url: '#cantiere',
      cta2_text: 'Free vs Pro', cta2_url: '#prezzi',
      scene: 'wall', wall_count: 187, wall_label: 'tile / 187',
    }),
    marquee(['quickview', 'hotspot 3D', 'before/after', 'viewer 360°', 'marquee', 'countdown', 'query loop', 'dark mode', 'form builder', 'lottie'], '▪', 'build'),
    tile('oloxsticky', {
      accent: 'build', variant: 'assembler', anchor: 'cantiere', kicker: 'Il cantiere',
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
    tile('oloxcards', {
      accent: 'build', variant: 'brick', anchor: '',
      kicker: 'La libreria', title_html: '12 famiglie, posate come <em>mattoni</em>',
      lead: 'Ogni famiglia arriva da sinistra e da destra, come in cantiere. 187 tile, un solo motore.',
      items: [
        { label: '31', title: 'WooCommerce', text_html: 'Quickview, wishlist, comparazione, bundle, filtro AJAX, checkout multi-step.', extra: '' },
        { label: '22', title: 'Booking', text_html: 'Calendario disponibilità, picker, slot orari, reception olo-spaces.', extra: '' },
        { label: '20', title: 'Interactive', text_html: 'Card 3D, hotspot, before-after, immagini frantumate, Viewer 360°, Lottie.', extra: '' },
        { label: '19', title: 'Media', text_html: 'Gallerie, video, audio, slider e feed social con lazy-load.', extra: '' },
        { label: '18', title: 'Marketing', text_html: 'Hero, contatori, countdown, prezzi, testimonianze, newsletter.', extra: '' },
        { label: '16', title: 'Navigation', text_html: 'Menu, header/footer, scroll-progress, switch lingua e dark-mode.', extra: '' },
        { label: '15', title: 'Dynamic', text_html: 'Post grid, query loop, related, meta, ricerca live.', extra: '' },
        { label: '10', title: 'Text', text_html: 'Heading animati, testo mascherato, marquee, TextPath.', extra: '' },
        { label: '10', title: 'Olo-Space', text_html: 'Stanze, servizi, prezzi, host card, calendario.', extra: '' },
        { label: '9', title: 'Essential', text_html: 'Immagine, video, bottone, icone, liste, tabella.', extra: '' },
        { label: '7', title: 'Layout', text_html: 'Sezioni, righe, colonne, spacer, divisori di forma.', extra: '' },
        { label: '2', title: 'Creative', text_html: 'Nastri e ticker animati a scorrimento infinito.', extra: '' },
      ],
    }),
    tile('oloxpricing', {
      accent: 'build', anchor: 'prezzi', kicker: 'Due edizioni', title_html: 'La gru cala il <em>Pro</em>',
      free_kicker: 'OLObuild · Free', free_price: '€0', free_per: 'per sempre · GPL · su WP.org',
      free_items: [
        { text_html: '<strong>Oltre 100 tile nativi</strong> + form builder + dark mode' },
        { text_html: 'Al livello dei <strong>builder Pro a pagamento</strong> della concorrenza' },
        { text_html: '<strong>11</strong> effetti testo · <strong>36</strong> animazioni' },
        { text_html: '<strong>OLOlang gratis</strong> il primo anno' },
      ],
      free_cta: 'Scarica Free', free_url: HOME_URL,
      pro_kicker: 'OLObuild · Pro', pro_price: '€29<em>*</em>', pro_per: '*prezzo lancio · poi €59/anno',
      pro_items: [
        { text_html: 'L’intera libreria: <strong>187 tile</strong>' },
        { text_html: 'Animazioni complete + ricerca media <strong>8 provider</strong>' },
        { text_html: '<strong>OLOlang a vita</strong> · supporto prioritario' },
        { text_html: '<strong>30 giorni</strong> di rimborso, senza domande' },
      ],
      pro_cta: 'Passa a Pro', pro_url: HOME_URL,
    }),
    follow('build'),
    next('Approfondimento tecnico', 'Manuale base + <em>scheda tecnica</em> →', P('olobuild-manuale'), 'build'),
    next('Prossima fermata', 'OLO<em>booking</em> →', P('olobooking'), 'booking'),
  ]),
};

/* ==================================================================== */
/* OLOBOOKING                                                            */
/* ==================================================================== */
const bookingPage = {
  title: 'OLObooking · Il tempo è tuo',
  slug: 'olobooking',
  content: page([
    tile('oloxhero', {
      accent: 'booking', bg_variant: 'booking', logo: LOGO('olobooking'),
      kicker: 'Prenotazioni · 6 verticali',
      title_html: 'Il tempo è tuo. <em>Riempilo.</em>', title_fx: 'none',
      sub_html: 'Camere, tavoli, appuntamenti, eventi, noleggi, immobili: <strong>un solo motore</strong>, una sola configurazione, <strong>zero commissioni</strong> a piattaforme di mezzo.',
      tags: [
        { text: '6 verticali', hot: true }, { text: 'anti no-show', hot: false },
        { text: 'QR access', hot: false }, { text: '0% commissioni', hot: false },
      ],
      cta1_text: 'Vivi una giornata ↓', cta1_url: '#giornata',
      cta2_text: 'I sei biglietti', cta2_url: '#verticali',
      scene: 'clock', clock_label: 'lo scroll muove le lancette',
    }),
    marquee(['check-in', 'tavolo 12', 'slot 15:30', 'biglietto QR', 'caparra', 'visita immobile', 'noleggio e-bike', 'conferma via mail'], '●', 'booking'),
    tile('oloxsticky', {
      accent: 'booking', variant: 'day', anchor: 'giornata', kicker: 'Una giornata col motore',
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
    tile('oloxcards', {
      accent: 'booking', variant: 'ticket', anchor: 'verticali',
      kicker: 'I verticali', title_html: 'Sei biglietti, <em>stesso motore</em>',
      lead: 'Attivi il verticale e campi, calendari e flussi si riconfigurano da soli. Se cambi mestiere, i dati restano.',
      items: [
        { label: 'Accommodation', title: 'Ospitalità', text_html: 'B&B, agriturismi, case-vacanza: calendario camere, tariffe stagionali, soggiorni minimi.', extra: 'OLO-ACC-01' },
        { label: 'Restaurants', title: 'Ristoranti', text_html: 'Tavoli, turni e menu, con caparra anti no-show per proteggere le serate piene.', extra: 'OLO-RST-02' },
        { label: 'Appointments', title: 'Appuntamenti', text_html: 'Studi, consulenza, estetica: slot orari, promemoria, gestione dello staff.', extra: 'OLO-APP-03' },
        { label: 'Events', title: 'Eventi', text_html: 'Conferenze, concerti, workshop: ticketing, posti numerati, accessi con QR.', extra: 'OLO-EVT-04' },
        { label: 'Rentals', title: 'Noleggi', text_html: 'Auto, bici, attrezzature, barche: inventario, cauzioni, contratti.', extra: 'OLO-RNT-05' },
        { label: 'Real estate', title: 'Immobiliare', text_html: 'Visite immobili su slot, agenzie, raccolta proposte.', extra: 'OLO-EST-06' },
      ],
    }),
    tile('oloxstatement', {
      accent: 'booking', variant: 'stamp', anchor: '',
      kicker: 'Incassi protetti',
      title_html: 'Il tavolo vuoto <em>non paga più te</em>',
      body_html: 'Prenotazione con <strong>caparra</strong>: chi non si presenta lascia qualcosa sul tavolo. E ogni prenotazione arriva <strong>senza commissioni</strong>: il canale diretto è davvero tuo.',
      stamp_text: 'No-show ◦ Coperto',
    }),
    follow('booking'),
    next('Approfondimento tecnico', 'Manuale base + <em>scheda tecnica</em> →', P('olobooking-manuale'), 'booking'),
    next('Prossima fermata', 'OLO<em>lang</em> →', P('ololang'), 'lang'),
  ]),
};

/* ==================================================================== */
/* OLOLANG                                                               */
/* ==================================================================== */
const langPage = {
  title: 'OLOlang · Di’ benvenuto in 28 modi',
  slug: 'ololang',
  content: page([
    tile('oloxhero', {
      accent: 'lang', bg_variant: 'lang', logo: LOGO('ololang'),
      kicker: 'Multilingua nativo · 28 lingue',
      title_html: 'Di’ «{scramble}»<br>in 28 modi.', title_fx: 'scramble',
      scramble_words: ['Benvenuto', 'Welcome', 'Willkommen', 'Bienvenue', 'Bienvenido', 'Bem-vindo', 'Welkom', 'Välkommen', 'ようこそ', '欢迎'].map((text) => ({ text })),
      sub_html: 'DeepL + traduttore IA con <strong>glossario e memoria di traduzione</strong>. Contenuti, menu e stringhe tradotti <strong>via database</strong>: non patch fragili sul frontend.',
      tags: [
        { text: '28 lingue', hot: true }, { text: 'DeepL + IA', hot: false },
        { text: 'via DB', hot: false }, { text: 'a vita con Pro', hot: false },
      ],
      cta1_text: 'Guarda la lingua girare ↓', cta1_url: '#flip',
      cta2_text: 'SEO multilingua', cta2_url: '#seo',
      scene: 'console',
      console_title: 'translator', console_sub: '· dashboard · batch in corso',
      console_rows: [
        { lc: 'EN', w: 100, pc: '' }, { lc: 'DE', w: 100, pc: '' }, { lc: 'FR', w: 96, pc: '' },
        { lc: 'ES', w: 92, pc: '' }, { lc: 'PT', w: 84, pc: '' }, { lc: 'NL', w: 78, pc: '' },
        { lc: 'JA', w: 64, pc: '' }, { lc: '+21', w: 52, pc: '…' },
      ],
    }),
    marquee(['Welcome', 'Willkommen', 'Bienvenue', 'Bienvenido', 'Bem-vindo', 'Welkom', 'Καλώς ήρθες', 'Добро пожаловать', 'ようこそ', '欢迎', 'Hoş geldin'], '·', 'lang'),
    tile('oloxlist', {
      accent: 'lang', variant: 'flip', anchor: 'flip',
      kicker: 'Tradotto davvero', title_html: 'Ogni riga <em>gira</em> come un tabellone',
      lead: 'Non solo i testi: menu, stringhe di tema e plugin, tutto passa dal database e torna fuori nella lingua giusta.',
      flip_items: [
        { src_label: 'contenuto · it', src_html: 'Prenota il tuo soggiorno', dst_label: 'content · en', dst_html: 'Book your stay' },
        { src_label: 'menu · it', src_html: 'Chi siamo → Contatti', dst_label: 'menü · de', dst_html: 'Über uns → Kontakt' },
        { src_label: 'stringa plugin · it', src_html: '«Aggiungi al carrello»', dst_label: 'chaîne · fr', dst_html: '«Ajouter au panier»' },
        { src_label: 'glossario · it', src_html: 'OLObuild <i style="font-style:normal;">(non tradurre)</i>', dst_label: 'glossary · *', dst_html: 'OLObuild ✓ protetto' },
        { src_label: 'memoria · it', src_html: '«Colazione inclusa», già tradotta', dst_label: 'memory · es', dst_html: '«Desayuno incluido» riusata, €0' },
      ],
    }),
    tile('oloxlist', {
      accent: 'lang', variant: 'url', anchor: 'seo',
      kicker: 'SEO multilingua', title_html: 'Google vede <em>28 siti di prima classe</em>',
      lead: 'hreflang, URL localizzati, sitemap e meta per ogni lingua: nessuna versione è figlia di un dio minore.',
      url_items: [
        { html: 'https://tuosito.it<b>/it/</b>camere-vista-lago', ok: 'indicizzata' },
        { html: 'https://tuosito.it<b>/en/</b>lake-view-rooms', ok: 'indexed' },
        { html: 'https://tuosito.it<b>/de/</b>zimmer-mit-seeblick', ok: 'indexiert' },
        { html: '&lt;link rel="alternate" hreflang="en" …&gt;', ok: 'auto' },
        { html: 'sitemap.xml, 28 varianti per pagina', ok: 'auto' },
      ],
    }),
    tile('oloxstatement', {
      accent: 'lang', variant: 'plain', anchor: '',
      kicker: 'Incluso, non venduto due volte',
      title_html: 'Gratis il 1° anno. <em>A vita</em> con OLObuild Pro.',
      body_html: 'Il multilingua è un diritto del sito, non un upsell. E traduce anche i flussi di OLObooking e i tile di OLObuild: un solo sistema di lingue per tutto.',
      cta_text: 'Prendilo con OLObuild', cta_url: P('olobuild'),
    }),
    follow('lang'),
    next('Approfondimento tecnico', 'Manuale base + <em>scheda tecnica</em> →', P('ololang-manuale'), 'lang'),
    next('Prossima fermata', 'OLO<em>security</em> →', P('olosecurity'), 'secur'),
  ]),
};

/* ==================================================================== */
/* OLOSECURITY                                                           */
/* ==================================================================== */
const securPage = {
  title: 'OLOsecurity · Chi bussa male resta fuori',
  slug: 'olosecurity',
  content: page([
    tile('oloxpagefx', { variant: 'scan' }),
    tile('oloxhero', {
      accent: 'secur', bg_variant: 'secur', logo: LOGO('olosecurity'),
      kicker: 'Sicurezza · 100% locale',
      title_html: 'Chi bussa male, <em>resta fuori</em>', title_fx: 'none',
      sub_html: 'Firewall OWASP, 2FA, scanner anti-webshell e bonifica guidata dal pannello <strong>Sentinel</strong>. Tutto elaborato <strong>sul tuo server</strong>: il traffico non finisce in nessun cloud altrui.',
      tags: [
        { text: '100% locale', hot: true }, { text: 'mini-WAF', hot: false },
        { text: 'TOTP 2FA', hot: false }, { text: 'v1.2.0 · GPL', hot: false },
      ],
      cta1_text: 'Togli i sigilli ↓', cta1_url: '#difese',
      cta2_text: 'Plugin Check 0/0', cta2_url: '#zerozero',
      scene: 'term',
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
    }),
    marquee(['SQLi', 'XSS', 'path traversal', 'LFI/RCE', 'brute force', 'finti crawler', 'webshell', 'password compromesse', 'bot'], '✕', 'secur'),
    tile('oloxstatement', {
      accent: 'secur', variant: 'counter', anchor: '',
      kicker: 'Mentre leggevi questa pagina',
      body_html: 'bloccati da un WordPress medio esposto in rete. Non serve essere famosi per essere un bersaglio: basta essere online.',
      counter_to: 47, counter_after: 'tentativi',
    }),
    tile('oloxcards', {
      accent: 'secur', variant: 'red', anchor: 'difese',
      kicker: 'Il pannello Sentinel', title_html: 'Quattro linee di difesa, <em>declassificate</em>',
      lead: 'Scorri: i sigilli si tolgono man mano. Otto schede operative, queste sono le quattro che fanno la differenza.',
      items: [
        { label: '01 · Prevenzione', title: 'Firewall · mini-WAF', text_html: 'Regole <strong>OWASP per famiglia</strong> (SQLi, XSS, traversal, LFI/RCE), reputazione IP, rate limiting, geo-blocco IPv4/IPv6.', extra: '' },
        { label: '02 · Identità', title: 'Accessi &amp; 2FA', text_html: 'Anti brute-force, <strong>TOTP</strong> con codici di recupero, password compromesse (HIBP, k-anonymity), CAPTCHA, anti finti crawler con verifica <strong>FCrDNS</strong>.', extra: '' },
        { label: '03 · Rilevamento', title: 'Scanner', text_html: 'Integrità di core, plugin e temi via <strong>checksum</strong>; scansione profonda <strong>anti-webshell</strong>; feed CVE e firme malware aggiornate.', extra: '' },
        { label: '04 · Reazione', title: 'Ripristino', text_html: '<strong>Bonifica guidata</strong> post-attacco, quarantena reversibile, rigenerazione dei salt, report d’incidente pronto da consegnare.', extra: '' },
      ],
    }),
    tile('oloxstatement', {
      accent: 'secur', variant: 'zerozero', anchor: 'zerozero',
      kicker: 'Trasparenza', zz_text: '0/0',
      title_html: 'WP Plugin Check: zero errori, <em>zero warning</em>',
      body_html: 'Codice GPL che puoi leggere riga per riga. Il contrario dei security-in-cloud che mandano il tuo traffico nei loro datacenter: qui analisi, firme e log restano <strong>a casa tua</strong>. GDPR semplice.',
    }),
    follow('secur'),
    next('Approfondimento tecnico', 'Manuale base + <em>scheda tecnica</em> →', P('olosecurity-manuale'), 'secur'),
    next('Prossima fermata', 'OLO<em>tour</em> →', P('olotour'), 'tour'),
  ]),
};

/* ==================================================================== */
/* OLOTOUR                                                               */
/* ==================================================================== */
const tourPage = {
  title: 'OLOtour · Questa pagina gira a 360°',
  slug: 'olotour',
  content: page([
    tile('oloxpagefx', { variant: 'pano', deg_label: 'lo scroll ruota la vista' }),
    tile('oloxhero', {
      accent: 'tour', bg_variant: 'none', logo: LOGO('olotour'),
      kicker: 'Tour virtuali · in arrivo',
      title_html: 'Questa pagina <em>gira a 360°</em>', title_fx: 'none',
      sub_html: 'Scrolla e guarda lo sfondo ruotare: è quello che faranno i tuoi visitatori nei tuoi spazi. Panorami sferici, <strong>hot-spot cliccabili</strong>, ambienti collegati, anche in <strong>VR</strong>.',
      tags: [
        { text: '360°', hot: true }, { text: 'Polyhaven · Street View', hot: false },
        { text: 'multi-stanza', hot: false }, { text: 'VR ready', hot: false },
      ],
      cta1_text: 'Percorri le stanze ↓', cta1_url: '#stanze',
      cta2_text: 'Gli hot-spot', cta2_url: '#hotspot',
      scene: 'porthole',
    }),
    tile('oloxcards', {
      accent: 'tour', variant: 'room', anchor: 'stanze', section_bg: 'rgba(12,14,19,.6)',
      kicker: 'Multi-stanza', title_html: 'Gli ambienti si <em>collegano</em>',
      lead: 'Ogni panorama porta al successivo: il visitatore cammina nel sito come camminerebbe da te.',
      items: [
        { label: 'scena 01', title: 'Ingresso', text_html: 'Panorama di benvenuto, hot-spot verso la reception e le camere.', extra: '' },
        { label: 'scena 02', title: 'Camera vista lago', text_html: 'Foto sferica reale, punti informativi su letto, vista, servizi.', extra: '' },
        { label: 'scena 03', title: 'Terrazza', text_html: 'Video 360° al tramonto, il momento che vende la notte.', extra: '' },
        { label: 'uscita', title: '→ Prenota', text_html: 'Il tour finisce dove deve: sul bottone di OLObooking.', extra: '1' },
      ],
    }),
    tile('oloxcards', {
      accent: 'tour', variant: 'hs', anchor: 'hotspot',
      kicker: 'Test finali prelancio', title_html: 'Le fondamenta di <em>OLOtour</em>', lead: '',
      items: [
        { label: '', title: 'Panorami &amp; <em>HDRI</em>', text_html: 'Librerie <strong>Polyhaven</strong> e <strong>Google Street View</strong> integrate: parti da panorami professionali o dai tuoi scatti sferici.', extra: '' },
        { label: '', title: 'Hot-spot <em>cliccabili</em>', text_html: 'Punti interattivi con testo, immagini e link tra ambienti: il visitatore esplora, tu racconti.', extra: '' },
        { label: '', title: '3D, splat &amp; <em>VR</em>', text_html: 'Scene 3D e gaussian splat, fruizione con visore: l’immersione non è un embed di terzi, vive <strong>nel tuo WordPress</strong>.', extra: '' },
      ],
      foot_html: 'Un assaggio esiste già: il tile <strong>Viewer 360°</strong> della famiglia Interactive di OLObuild. OLOtour lo porta al livello successivo, senza piattaforme esterne a canone né branding altrui sui tuoi spazi.',
      foot_cta: 'Prova il Viewer 360° in OLObuild', foot_url: P('olobuild'),
    }),
    follow('tour'),
    next('Approfondimento tecnico', 'Manuale base + <em>scheda tecnica</em> →', P('olotour-manuale'), 'tour'),
    next('Prossima fermata', 'OLO<em>tutor</em> →', P('olotutor'), 'tutor'),
  ]),
};

/* ==================================================================== */
/* OLOTUTOR                                                              */
/* ==================================================================== */
const tutorPage = {
  title: 'OLOtutor · Questa pagina è un corso',
  slug: 'olotutor',
  content: page([
    tile('oloxpagefx', { variant: 'xp', xp_label: 'corso · questa pagina', xp_total: 540, xp_cap: 630, xp_step: 180 }),
    tile('oloxhero', {
      accent: 'tutor', bg_variant: 'tutor', logo: LOGO('olotutor'), pad_top: 150,
      kicker: 'Formazione · in arrivo',
      title_html: 'Questa pagina è <em>un corso</em>', title_fx: 'none',
      sub_html: 'Scrolla e guadagni XP: è la logica di OLOtutor. Corsi, lezioni, quiz, punti, badge e certificati, <strong>dentro il tuo WordPress</strong>, non su un marketplace che ti mette in fila coi concorrenti.',
      tags: [
        { text: 'LMS', hot: true }, { text: 'quiz & badge', hot: false },
        { text: 'registro voti', hot: false }, { text: 'certificati', hot: false },
      ],
      cta1_text: 'Sblocca le lezioni ↓', cta1_url: '#lezioni',
      cta2_text: 'Fai il quiz', cta2_url: '#quiz',
      scene: 'medal', medal_top: 'livello', medal_big: '1', medal_bot: 'studente',
    }),
    marquee(['+120 xp', 'quiz superato', 'badge sbloccato', 'lezione 4/12', 'certificato pronto', 'registro aggiornato', 'streak 7 giorni'], '★', 'tutor'),
    tile('oloxlessons', {
      accent: 'tutor', anchor: 'lezioni',
      kicker: 'Il percorso', title_html: 'Le lezioni si <em>sbloccano</em> scendendo',
      lock_text: 'scendi per sbloccare',
      items: [
        { xp: '+120 xp', title: 'Corsi &amp; lezioni', text_html: 'Strutture di corso, lezioni ordinate, area allievi con i progressi di ciascuno. Il programma lo detti tu.' },
        { xp: '+180 xp', title: 'Quiz &amp; gamification', text_html: 'Quiz, mini-giochi, punti e badge. La motivazione fa parte del metodo, non è un plugin in più.' },
        { xp: '+90 xp', title: 'Registro &amp; certificati', text_html: 'Registro voti e certificati di completamento: quello che serve a scuole, accademie e formatori.' },
        { xp: '+150 xp', title: 'Gli allievi restano tuoi', text_html: 'Iscrizioni, dati e pagamenti sul tuo sito. Con OLObooking le lezioni individuali si prenotano su slot; con OLOlang i corsi parlano 28 lingue.' },
      ],
    }),
    tile('oloxquiz', {
      accent: 'tutor', anchor: 'quiz',
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
    follow('tutor'),
    next('Approfondimento tecnico', 'Manuale base + <em>scheda tecnica</em> →', P('olotutor-manuale'), 'tutor'),
    next('Fine del percorso', 'Torna al <em>viaggio</em> →', HOME_URL, 'olo'),
  ]),
};

/* ==================================================================== */
/* MANUALI                                                               */
/* ==================================================================== */
const manual = (slug, prodSlug, active, accent, logoName, docCode, extraDoc, subHtml, chapters, spec) => ({
  title: `Manuale ${logoName} · OLOtheme`,
  slug,
  content: page([
    tile('oloxmanual', {
      accent,
      doc_codes: [
        { html: `doc <b>${docCode}</b>` }, { html: 'manuale base' }, { html: '+ scheda tecnica' },
        ...(extraDoc ? [{ html: extraDoc }] : []),
      ],
      logo: LOGO(logoName),
      title_html: 'Manuale <em>base</em>',
      sub_html: subHtml,
      chapters,
      toc_spec: 'Scheda tecnica',
      spec_title: 'Scheda <em>tecnica</em>',
      spec_name: spec.name, spec_sub: spec.sub,
      spec_rows: spec.rows.map(([f, text_html]) => ({ f, text_html })),
      spec_cta1: '← Torna alla scheda prodotto', spec_url1: P(prodSlug),
      spec_cta2: spec.cta2 || 'Il viaggio OLOtheme', spec_url2: spec.url2 || HOME_URL,
    }),
  ]),
});

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
const headerTpl = {
  title: 'OLOtheme — Header',
  slug: 'olox-header',
  kind: 'header',
  content: page([
    tile('oloxnav', {
      logo: LOGO('olotheme'), logo_url: '/',
      links: NAV_LINKS(''),
      show_lang: true,
      exp_text: '← il viaggio', exp_url: '/',
      active_auto: true, exp_auto: true,
      exp_manual_text: '← scheda prodotto',
      accent: 'olo',
    }),
  ]),
};
const footerTpl = {
  title: 'OLOtheme — Footer',
  slug: 'olox-footer',
  kind: 'footer',
  content: page([
    tile('oloxfoot', {
      logo: LOGO('olotheme'),
      links_auto: true,
      home_label: 'il viaggio',
      fine: 'GPL · Trento · no SaaS',
      fine_manual: 'manuali base · GPL · Trento',
      fine_overrides: 'olosecurity:GPL · Trento · no SaaS · 100% locale',
      show_credits: true,
      credits_html: 'OLOtheme by <a href="https://clod.eu" target="_blank" rel="noopener">clod.eu</a> | @2026 | sito introduttivo | <a href="mailto:info@olotheme.com">info@olotheme.com</a>',
      accent: 'olo',
    }),
  ]),
};

/* ---------- scrittura ---------- */
const ALL = [headerTpl, footerTpl, home, buildPage, bookingPage, langPage, securPage, tourPage, tutorPage, manBuild, manBooking, manLang, manSecur, manTour, manTutor];
for (const t of ALL) {
  writeFileSync(`${OUT}/${t.slug}.json`, JSON.stringify({ title: t.title, slug: t.slug, kind: t.kind || 'page', content: t.content }), 'utf8');
  console.log(`✓ ${t.slug}.json (${t.content.length} sezioni · ${t.kind || 'page'})`);
}
console.log(`\n${ALL.length} template generati in ${OUT}`);
