# Olobuild — Scheda Tecnica e Commerciale

> Page Builder professionale olonico per WordPress.
> Sistema a griglia (tile drag & drop), 187 elementi nativi, motore di animazione, integrazioni media e architettura modulare.

---

## In una frase
**Olobuild è il primo page builder WordPress in cui ogni elemento è un "tile" indipendente con animazioni, effetti testo, hover transitions, parallax scroll-linked, mouse tracking e visibilità condizionale di serie — senza plugin esterni.**

---

## Numeri chiave

| Metrica | Valore |
|---|---|
| Tile nativi | **187** |
| Effetti testo (Olobuild Text FX) | **11** (typewriter, gradient animato, glitch RGB, wave, scramble, reveal lettera/parola, highlight grow, underline grow, …) |
| Animazioni di ingresso | **36** (entrance) |
| Animazioni continue | **8** (infinite: float, pulse, spin, wiggle, bounce, swing, breathe, …) |
| Breakpoint responsive | **6** (desktop, widescreen, tablet landscape, tablet, mobile landscape, mobile) |
| Provider media integrati | **5+** (Unsplash, Pexels, Pixabay, Openverse, Freesound, Polyhaven HDRI, Google Street View) |
| Tabelle DB dedicate | **2** (`wp_olo_templates`, `wp_olo_revisions`) |
| Stack frontend | Vue 3 + Pinia + Vite 5 + Tailwind + SASS + UIkit |
| Stack backend | PHP 7.4+, REST API namespace `olo/v1` |

---

## Categorie tile (distribuzione)

| Categoria | # tile | Esempi |
|---|---|---|
| **WooCommerce** | 31 | Quickview, Wishlist, Comparison, Bundle, Filter, Gallery slider, Cross-sells, Upsells, Recently viewed, Multi-step checkout |
| **Booking** | 22 | Calendar, Booking picker, Slot orari, Sistema reception per olo-spaces |
| **Interactive** | 20 | Flipcard, Hotspot, ImgCompare (before/after), TextMask, BlendText, ShatteredImage, Switcher, RevealBox, FloatingPanel, Lottie, SVG Animator, Viewer360 |
| **Media** | 19 | Video facade (poster + lazy-iframe), Carousel, Slideshow, ProGallery (10+ layout), Audio player custom, Lightbox, SoundCloud, Twitter feed, Instagram |
| **Marketing** | 18 | Hero, Counter, CounterCircle, Countdown, Testimonial, Pricing, Pricelist, Quotation, ProgressTracker, Newsletter, ViewsCounter, LinkInBio |
| **Navigation** | 16 | Megamenu, Navmenu, Subnav, Breadcrumbs, MenuAnchor, MobileBar, KillNextPrev, ToTop, TOC, ScrollProgress, Lang switcher, Dark mode toggle |
| **Dynamic** | 15 | PostGrid, QueryLoop (Elementor-killer), RelatedPosts, AuthorBox, ReadingTime, PostMeta, Search live, Sitemap, Tag cloud, Page title bar |
| **Text** | 10 | Headline, AnimatedHeading, Content, Text-block, Quotation, Marquee, NewsTicker, TextPath, TextMask, BlendText |
| **Olo-Space** | 10 | Modulo dedicato per coworking/affittacamere/strutture turistiche con calendario stanze, gallerie servizi, prezzi, contatti |
| **Layout** | 7 | Section, Row, Column, Inner-columns, Spacer, Divider, ShapeDivider |
| **Essential** | 9 | Image, Video, Button, Icon, IconBox, IconList, List, DescList, Table |
| **Creative** | 2 | Marquee, NewsTicker |
| **Forms** | — | Form builder con 30+ tipi campo, calcolatore formule, file upload, multi-step, Honeypot anti-spam, integrazioni mailer |

---

## Tile più caratteristici (vs concorrenza)

### Tile UNICI di Olobuild (rari o assenti negli altri builder)

| Tile | Descrizione |
|---|---|
| **BlendText** | Testo gigante che fa CSS `mix-blend-mode: difference` con lo sfondo della section, scappando attraverso gli stacking-context degli avi (auto-fix `isolation`/`z-index`) |
| **TextMask** | Immagine o video usato come pattern di riempimento del testo (-webkit-background-clip + scroll-driven animation) |
| **TextPath** | Testo che segue un path SVG curvilineo, con animazione di scorrimento lungo la curva |
| **AnimatedHeading** | Titolo con parole sostituibili animate (typewriter, slide, zoom, flip su singola parola) |
| **ShatteredImage** | Immagine spezzata in poligoni configurabili (preset shards/triangoli/stelle), con Ken Burns + parallax sui frammenti |
| **ImgCompare** | Slider before/after con divisore animato, hover/scrub, supporta video |
| **Hotspot** | Immagine con punti interattivi posizionabili a `%` con tooltip rich, posizione tooltip auto-rilevata |
| **Flipcard** | Card 3D con 6 modalità di flip (orizzontale, verticale, diagonale, cube 3D, slide+flip, zoom+flip) |
| **Viewer360** | Visualizzatore HDRI 360° con integrazione Polyhaven + Google Street View |
| **Lottie** | Animazione After Effects con scroll-trigger, hover-trigger, viewport-trigger |
| **SVG Animator** | Anima qualsiasi SVG con stroke-dash, fill, transforms su `viewport-entry` |
| **PdfPro** | Visualizzatore PDF con bookmark, ricerca, annotazioni — non un viewer base |
| **Marquee** | Carousel infinito con velocità, pausa hover, direzione, gradient mask laterali, contenuto custom (loghi, frasi, immagini) |
| **NewsTicker** | Ticker news con auto-rotate, badge per item, link, animazione fade/slide |
| **OverlayGrid / OverlaySlider** | Griglie/slider con overlay testuale animato e hover effects |
| **ProGallery** | 10+ layout (justified, masonry, scattered, parallax-slow/fast, filmstrip, split-grid, …) — alcuni unici |
| **VideoPlaylist** | Playlist video con player principale + thumbnail laterali, auto-resolve YouTube/Vimeo metadata |
| **DarkMode toggle** | Switch nativo light/dark con localStorage e preference detection, sincronizzato CSS variables |
| **HiddenPop** | Tile-popup nascosto che esce con trigger (click, scroll-percent, exit-intent, time-delay, inactivity) |
| **HostCard** | Card profilo host (per affitti/coworking) con stelle, recensioni, badge verificato, contatti diretti |
| **FloatingPanel** | Panel laterale always-on-top con trigger, max-z, posizione configurabile |
| **Olo-Space module** | 10 tile dedicati alla gestione di stanze/strutture: calendario disponibilità, contatti, descrizione, gallerie, griglie, hero, info, prezzi, related |

### Tile WooCommerce avanzati

WooCommerce è coperto da **31 tile dedicati**, includendo feature normalmente a pagamento o esterne:
- Multi-step checkout
- Wishlist + Comparison nativi (no plugin)
- Bundle prodotto
- Filtro AJAX live
- Cross-sells / Upsells / Recently viewed / Related
- Quickview con lightbox
- Gallery slider con zoom
- Cart minicart fluttuante
- Sale badge personalizzabile
- Order tracking pubblico

---

## Funzionalità extra (le "killer feature" vs altri builder)

### 1. Text Effects (11 effetti, applicabili a OGNI tile testuale)
- **Typewriter** singolo o loop su frasi multiple (anche con cursore lampeggiante personalizzabile)
- **Reveal** lettera-per-lettera o parola-per-parola con blur/translate
- **Gradient animato** con colori personalizzabili
- **Glitch RGB** stile cyberpunk
- **Wave** lettere ondulanti
- **Highlight grow** evidenziatore che cresce on viewport entry
- **Underline grow** sottolineatura animata
- **Scramble** matrix-style scramble→reveal

> Ogni effetto è applicabile a *tutti* i campi testo dei 187 tile, con target selezionabile (titolo / sottotitolo / testo / "tutti").

### 2. Animazioni di ingresso (36 entrance animations)
Da fade/slide/zoom base fino a `bounce`, `elastic`, `rubber`, `jello`, `lightspeed-left/right`, `roll-in`, `jack-in-box`, `hinge`, `flip-x/y`, `back-in`, `curtain-reveal`, `blur-zoom`, `skew-in`, `swing`, `wobble`. Trigger su `IntersectionObserver` con threshold/delay/once configurabili.

### 3. Stagger figli (parent → children)
Le 36 animazioni si possono propagare ai figli di un tile (gallery, gridcards, list, slider) con delay incrementale configurabile in ms.

### 4. Animazioni continue (8 infinite)
Float, pulse, spin, wiggle, bounce, swing, breathe + direzione (normale, alternata, inversa) e velocità.

### 5. Hover transforms (su qualsiasi elemento)
Scale, translateX/Y, rotate, skewX/Y con transition timing function configurabile.

### 6. **Border-Radius animato all'hover** (FEATURE BANDIERA)
Ogni elemento con bordo arrotondato espone un secondo controllo `Raggio bordo (hover)` — i 4 angoli si animano in modo indipendente. Esempio: card che parte rettangolare e all'hover diventa "asimmetrica organica" (`0 / 50 / 50 / 50`). Default in tutti i 187 tile.

### 7. Mouse Tilt 3D + Cursor Tracking
Effetto perspective che inclina l'elemento verso il mouse, con intensità configurabile. Versione "track" segue il cursore con easing.

### 8. Parallax Scroll-Linked (multi-property)
Su un singolo elemento puoi animare contemporaneamente: opacity, scale, rotate, translateX, translateY, blur — ognuno con valore start/end indipendente, mappato sul progresso di scroll dell'elemento nel viewport.

### 9. CSS Filters + Backdrop Filter
Sfocatura, luminosità, contrasto, saturazione, scala di grigi, seppia. Backdrop con `glassmorphism` (sfocatura sfondo).

### 10. Visibilità condizionale (AND/OR avanzata)
Logica con 24 condizioni: ruolo utente, login state, fascia oraria, giorno settimana, browser, referrer URL, device type, post type, custom field, carrello WooCommerce, data, frontpage, archive, ecc. Combinabili in AND/OR fino a 2 condizioni per tile.

### 11. Responsive a 6 breakpoint
Ogni controllo numerico (font-size, padding, gap, layout, posizione, …) ha versioni separate per:
- Desktop (default)
- Widescreen (>1600px)
- Tablet landscape (<1200px)
- Tablet (<960px)
- Mobile landscape (<640px)
- Mobile (<480px)

### 12. Inline Editing (live in canvas)
Doppio click su titoli/testi per editing diretto nel canvas con floating toolbar Tiptap (rich text, link, formattazione). Nessun preview separato.

### 13. Copy/Paste Style
Tasto destro su qualsiasi tile → "Copia stile" / "Incolla stile". Scorciatoie `Ctrl+Alt+C` / `Ctrl+Alt+V`. Trasferisce padding, margin, colori, ombre, hover, animazioni — non il contenuto.

### 14. Ricerca media integrata (5+ provider)
Pannello di ricerca media direttamente nel builder, query verso:
- **Unsplash, Pexels, Pixabay, Openverse** (foto)
- **Pexels Video, Pixabay Video** (video MP4)
- **Freesound** (audio CC)
- **Polyhaven** (HDRI 360° per Viewer360)
- **Google Street View** (foto sferiche reali)
- Filtri: orientamento, dimensione, durata, licenza, soggetto

Download diretto in WP Media Library con metadata e crediti.

### 15. Template Conditions (assignment automatico)
Header, footer e single-post layout possono avere condizioni di applicazione: post type, ruolo, lingua, URL pattern, ecc. — con priorità multipla. Un template vince sull'altro per regola configurata.

### 16. Global Widgets
Salva un tile (anche complesso, es. un hero) come widget globale → riusabile in N pagine. Edit centralizzato → propagazione automatica.

### 17. Header / Footer / Single-Post system
Builder dedicato per ognuno dei 3 contesti, con sticky modes, scroll-shrink, transparency-on-top, mobile menu personalizzato, breadcrumbs auto.

### 18. Dark mode nativo
Sistema CSS variables con supporto dark mode integrato. Toggle automatico o manuale, con persistenza localStorage. Si applica anche al builder admin.

### 19. Form builder integrato
Multi-step, calcolatore formule (es. `{prezzo} * {quantita} - {sconto}`), file upload, color/date/time picker, signature pad, reCAPTCHA, Honeypot, integrazioni email + webhook. Senza plugin esterni.

### 20. Live Search WP-wide
Tile `Livesearch` con search live JS (debounced) sui post WordPress + custom post types, con thumbnail, excerpt, categorie. Modal o inline.

### 21. PdfPro (visualizzatore avanzato)
Visualizzatore PDF con bookmark, ricerca testuale, annotazioni, multi-pagina, lazy-loading. No plugin.

### 22. SVG Animator + Lottie integrati
Carica SVG o JSON Lottie e animali con scroll-trigger, hover-trigger, autoplay. Senza plugin AfterEffects-bridge esterni.

---

## Architettura tecnica

### Stack
| Layer | Tecnologia |
|---|---|
| **Backend** | PHP 7.4+, WordPress 5.8+ minimum |
| **Frontend builder** | Vue 3 (Composition API) + Pinia (store) + Vite 5 (build) + SASS + Tailwind (prefix `mb-`) + UIkit (utilities) |
| **REST API** | Namespace dedicato `olo/v1` |
| **DnD** | `@atlaskit/pragmatic-drag-and-drop` (no più vuedraggable, performance superiore) |
| **DB** | 2 tabelle dedicate: `wp_olo_templates`, `wp_olo_revisions` (no abuse di postmeta) |
| **Editor inline** | Tiptap (rich text con floating toolbar) |
| **Animazioni** | CSS keyframes + IntersectionObserver + custom JS runner |

### Pattern architetturali
- **Tile-based**: ogni elemento è una classe PHP `Olo_<Name>_Tile` che eredita da `Olo_Tile_Base`. Render PHP server-side, configurazione via JSON config in `src/config/elements/`.
- **Auto-discovery**: i tile si registrano automaticamente via `import.meta.glob`. Aggiungere un tile = creare 2 file (config JS + classe PHP).
- **Helper centralizzati**: `Olo_Tile_Utils` (border-radius, spacing, shadow, color, etc.) + `Olo_Text_Effects` (effetti testo) — codice DRY su 187 tile.
- **Scoped styles**: ogni istanza di tile ha un UID univoco (`olo-XXX-12345`), CSS scoped per evitare leak tra istanze.
- **Per-instance hover**: classi UID univoche risolvono il classico bug "il primo elemento eredita gli hover dell'ultimo".

### Performance
- Render PHP server-side (HTML statico nel sorgente, no idratazione client-side per il rendering)
- IntersectionObserver per animazioni e text-effects (no scroll listener fame)
- Lazy-load immagini, video facade (poster + click-to-load iframe per YouTube/Vimeo)
- CSS scoped per istanza (no regole globali che cascano su tutta la pagina)

---

## Confronto con la concorrenza

| Feature | Olobuild | Elementor Pro | Divi | Bricks | Beaver Builder |
|---|:---:|:---:|:---:|:---:|:---:|
| Tile nativi | **187** | ~95 | ~50 | ~80 | ~40 |
| Text effects integrati | **11** | 0 (plugin) | 1 | 3 | 0 |
| Border-radius hover animato | **✅ default** | ❌ | ❌ | ⚠️ via CSS | ❌ |
| Mouse tilt 3D | **✅** | ⚠️ addon | ❌ | ❌ | ❌ |
| Parallax multi-property | **✅** | ⚠️ basic | ⚠️ basic | ✅ | ❌ |
| Ricerca media multi-provider | **✅ 8 provider** | ⚠️ Unsplash only | ❌ | ❌ | ❌ |
| Conditional visibility AND/OR | **✅ 24 cond** | ✅ Pro | ⚠️ basic | ✅ | ❌ |
| Inline editing rich text | **✅ Tiptap** | ✅ TinyMCE | ✅ | ✅ | ⚠️ |
| Wishlist/Comparison Woo | **✅ nativi** | ❌ | ❌ | ❌ | ❌ |
| Multi-step checkout | **✅** | ⚠️ Pro | ⚠️ addon | ❌ | ❌ |
| Builder DB dedicato (no postmeta) | **✅** | ❌ | ❌ | ✅ | ❌ |
| Pragmatic DnD (perf) | **✅** | ❌ | ❌ | ✅ | ❌ |
| Responsive 6 breakpoint | **✅** | ✅ | ⚠️ 3 | ✅ | ⚠️ 3 |
| Stagger figli animazioni | **✅** | ⚠️ Pro | ❌ | ⚠️ | ❌ |
| Lottie + SVG Animator | **✅ entrambi** | ⚠️ Lottie Pro | ❌ | ⚠️ Lottie | ❌ |
| Viewer 360° HDRI | **✅** | ❌ | ❌ | ❌ | ❌ |

---

## Pubblico target

### B2B agenzie web
- Pagine landing con conversioni alte (testo animato, hero parallax, CTA dinamiche)
- E-commerce avanzati (wishlist, comparison, multi-step checkout senza plugin esterni)
- Strutture ricettive / coworking (modulo Olo-Space dedicato)

### Freelance / Studi piccoli
- Sostituisce 8-10 plugin (form, popup, slider, gallery, animation, lottie, viewer 360°, comparison, dark-mode toggle, ricerca media)
- Curva di apprendimento: chi conosce Elementor è operativo in 1 giorno

### Agenzie con clienti enterprise
- Sistema condizioni avanzato (24 condizioni, AND/OR) per personalizzazione contenuti
- Responsive a 6 breakpoint per dispositivi non-standard
- Performance: render server-side + lazy-load aggressivo

---

## Pricing positioning (suggerito)

| Edition | Target | Feature |
|---|---|---|
| **Free** | Hobbisti / progetti personali | 50 tile essenziali, no animazioni, no Olo-Space, no premium media providers |
| **Pro** | Agenzie / freelance | Tutti i 187 tile, animazioni complete, ricerca media full, support standard |
| **Agency** | Studi multi-cliente | Pro + multi-site license, white-label, priority support, custom tile builder |

---

## Roadmap (visione)

- **AI design assistant**: generazione tile/sezioni da prompt testo (richiede backend AI)
- **A/B testing nativo**: variant tile con metric tracking
- **Connect to CMS headless** (Strapi, Contentful) via REST data source
- **Marketplace tile community** con revenue share
- **Mobile app builder** (riuso config JSON via React Native renderer)

---

## Tagline candidates

1. *"Più di un page builder. Un sistema di design olonico."*
2. *"187 tile, 11 effetti testo, 36 animazioni — zero plugin esterni."*
3. *"Quello che gli altri builder vendono come addon, Olobuild lo include."*
4. *"WordPress builder per chi disegna, non per chi clicca."*
5. *"Border-radius animato all'hover di serie. Tutto il resto è in più."*

---

## File / Asset chiave per pitching

| File | Uso |
|---|---|
| Screenshot dashboard builder | Prima impressione UX |
| Loop video di hover-radius animato | Wow factor immediato |
| Mockup mobile responsive | Dimostrazione 6 breakpoint |
| Comparison table side-by-side vs Elementor | Sales sheet B2B |
| Demo live (`mosaic.clod.eu`) | Prova diretta del prodotto |
