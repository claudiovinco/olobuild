# Olobuild — Manuale Pratico

**Versione**: 2.7.2
**Ultimo aggiornamento**: Marzo 2026

---

## Indice

1. [Cos'è Olobuild](#cosè-olobuild)
2. [Architettura e Stack Tecnologico](#architettura-e-stack-tecnologico)
3. [Installazione e Requisiti](#installazione-e-requisiti)
4. [L'interfaccia del Builder](#linterfaccia-del-builder)
5. [Sistema a Griglia e Tile](#sistema-a-griglia-e-tile)
6. [Catalogo Completo Elementi (168 tile)](#catalogo-completo-elementi)
7. [Sistema Template](#sistema-template)
8. [Design System Globale](#design-system-globale)
9. [Contenuto Dinamico](#contenuto-dinamico)
10. [Integrazione WooCommerce (32 tile)](#integrazione-woocommerce)
11. [Ricerca Media Stock](#ricerca-media-stock)
12. [Assistente AI](#assistente-ai)
13. [Form Builder](#form-builder)
14. [Performance e SEO](#performance-e-seo)
15. [Funzionalità Avanzate](#funzionalità-avanzate)
16. [Confronto con i Competitor](#confronto-con-i-competitor)

---

## Cos'è Olobuild

Olobuild è un page builder WordPress professionale basato su un **sistema a griglia drag & drop** (tile). A differenza dei builder tradizionali che usano sezioni, righe e colonne, Olobuild organizza il contenuto in **tile** posizionabili liberamente su una griglia responsiva.

**Caratteristiche principali:**

- **168 elementi** (tile) — dal semplice titolo al checkout WooCommerce multi-step
- **Builder visuale** con canvas interattivo, inspector laterale, albero struttura
- **Template system completo** — header, footer, single, archive, 404, search
- **6 breakpoint responsivi** — desktop, widescreen, tablet landscape, tablet, mobile landscape, mobile
- **Design system globale** — colori, tipografia, preset design, token CSS
- **Contenuto dinamico** — binding a post corrente, ACF, tassonomie, utente, WooCommerce
- **32 tile WooCommerce** — shop completo con checkout multi-step, filtri, quickview, wishlist
- **Assistente AI** — generazione testo, immagini, layout, CSS tramite Claude e DALL-E
- **5 librerie media stock** — Unsplash, Pexels, Pixabay, Openverse, Freesound
- **Form builder integrato** con token HMAC, rate limiting, upload file, export CSV
- **Critical CSS automatico**, asset optimizer, performance hints

---

## Architettura e Stack Tecnologico

| Livello | Tecnologia |
|---------|-----------|
| **Frontend Builder** | Vue.js 3 + Pinia + Vite 5 |
| **CSS Builder** | Tailwind CSS (prefix `mb-`) + SASS |
| **Frontend Sito** | UIkit 3 (grid, lightbox, animazioni) |
| **Backend** | PHP 7.4+ (classi WordPress) |
| **API** | WordPress REST API (namespace `olo/v1`) |
| **Database** | 3 tabelle custom (`olo_templates`, `olo_revisions`, `olo_form_submissions`) |
| **Drag & Drop** | vuedraggable (SortableJS) |
| **Editor Testo** | Tiptap (ProseMirror) |
| **Grafici** | Chart.js |
| **Animazioni** | Lottie, AOS (Animate on Scroll) |

### Struttura File

```
olobuild/
├── olobuild.php                    → Entry point plugin
├── includes/                       → 40+ classi PHP
│   ├── class-olo-builder.php       → Core builder
│   ├── class-rest-api.php          → 80+ endpoint REST
│   ├── class-frontend-renderer.php → Render HTML frontend
│   ├── class-database.php          → CRUD template/revisioni
│   ├── class-dynamic-content.php   → Binding dati dinamici
│   ├── class-ai-assistant.php      → 8 endpoint AI
│   ├── class-form-handler.php      → Gestione form
│   ├── tiles/                      → 168 classi tile PHP
│   └── ...
├── src/                            → Sorgenti Vue.js
│   ├── stores/                     → Pinia (builder, tiles, styles)
│   ├── config/elements/            → 178 config elementi
│   ├── components/
│   │   ├── Builder/                → Toolbar, Sidebar, Canvas, Inspector
│   │   ├── Tiles/                  → 195 componenti Vue tile
│   │   └── Grid/                   → Sistema griglia
│   └── composables/                → 12 composable (history, autosave, shortcuts...)
├── assets/                         → Build output (JS/CSS)
└── templates/                      → Template PHP speciali
```

---

## Installazione e Requisiti

**Requisiti minimi:**
- WordPress 5.8+
- PHP 7.4+
- MySQL 5.7+ / MariaDB 10.3+

**Requisiti consigliati:**
- WordPress 6.4+
- PHP 8.1+
- 256 MB memory limit

**Installazione:**
1. Caricare lo ZIP `olobuild.zip` da **Plugin > Aggiungi nuovo > Carica plugin**
2. Attivare il plugin
3. Le tabelle database vengono create automaticamente
4. Accedere a **Olobuild** nel menu admin di WordPress

---

## L'interfaccia del Builder

L'interfaccia è divisa in 4 aree principali:

```
┌──────────────────────────────────────────────────────────┐
│  TOOLBAR  [Undo] [Redo] [Device] [Preview] [Settings] [Save]  │
├────────┬──────────────────────────────┬──────────────────┤
│        │                              │                  │
│  SIDE  │         CANVAS               │    INSPECTOR     │
│  BAR   │                              │                  │
│        │   ┌────┬────┬────┐           │  [Content]       │
│ Elementi│   │tile│tile│tile│           │  [Style]         │
│ Struttura│  ├────┴────┴────┤          │  [Advanced]      │
│        │   │   tile        │           │                  │
│        │   └───────────────┘           │  Campo 1: ____   │
│        │                              │  Campo 2: ____   │
│        │                              │  Campo 3: ____   │
└────────┴──────────────────────────────┴──────────────────┘
```

### Toolbar (barra superiore)
- **Undo/Redo** — storico illimitato con Ctrl+Z / Ctrl+Shift+Z
- **Device Switcher** — 6 breakpoint (mobile → widescreen)
- **Preview** — anteprima frontend in tempo reale
- **Page Settings** — background pagina, max-width, breakpoint personalizzati
- **Save** — salvataggio con auto-dirty detection

### Sidebar (pannello sinistro)
- **Libreria Elementi** — tutti i 168 tile, filtrabili per categoria
- **Albero Struttura** — gerarchia DOM interattiva con show/hide e lock
- **Finder** — ricerca rapida per nome/tipo elemento (Ctrl+F)

### Canvas (area centrale)
- Rendering live dei tile nella griglia
- **Drag & drop** per posizionare e riordinare
- **Click** per selezionare → apre inspector
- **Context menu** (tasto destro) — copia, incolla, duplica, elimina

### Inspector (pannello destro)
- **Tab Content** — campi specifici del tile (testo, immagini, link, opzioni)
- **Tab Style** — background, bordi, ombre, padding, margin, transform
- **Tab Advanced** — ID/classe CSS, animazioni scroll, visibilità condizionale
- **20 tipi di campo** — testo, textarea, select, toggle, color picker, gradient, range, image, media, icon, font-family, gallery, rich editor, spacing, border-radius, box-shadow, transform, lottie picker, multi-pills, panel map

### Scorciatoie tastiera
| Tasto | Azione |
|-------|--------|
| `Ctrl+S` | Salva |
| `Ctrl+Z` | Annulla |
| `Ctrl+Shift+Z` | Ripristina |
| `Ctrl+C` | Copia tile |
| `Ctrl+V` | Incolla tile |
| `Ctrl+D` | Duplica tile |
| `Canc` | Elimina tile |
| `Ctrl+F` | Finder (cerca elemento) |

---

## Sistema a Griglia e Tile

Il cuore di Olobuild è il **sistema a griglia responsiva**. Ogni pagina è composta da **sezioni** che contengono **griglie** di **tile**.

### Gerarchia strutturale

```
Template
└── Section (sfondo, padding, max-width)
    └── Grid (colonne responsive, gap)
        ├── Cell (tile singolo con span colonna/riga)
        ├── Cell
        └── Cell
            └── Inner Grid (nesting illimitato)
                ├── Cell
                └── Cell
```

### Responsive Design — 6 Breakpoint

| Breakpoint | Larghezza | Icona |
|-----------|-----------|-------|
| Mobile | < 480px | smartphone verticale |
| Mobile Landscape | 480–639px | smartphone orizzontale |
| Tablet | 640–959px | tablet verticale |
| Tablet Landscape | 960–1199px | tablet orizzontale |
| Desktop | 1200–1399px | monitor |
| Widescreen | 1400px+ | monitor grande |

Ogni campo dell'inspector con l'icona responsive supporta **valori diversi per breakpoint** (es. font-size 48px su desktop, 24px su mobile).

---

## Catalogo Completo Elementi

### Layout e Struttura (5)
| Tile | Descrizione |
|------|-------------|
| **Section** | Contenitore principale con background, overflow, padding |
| **Row** | Riga con colonne (compatibilità legacy) |
| **Column** | Colonna singola |
| **Inner Columns** | Sistema colonna annidato |
| **Grid** | Griglia drag & drop con colonne responsive |

### Testo e Titoli (6)
| Tile | Descrizione |
|------|-------------|
| **Headline** | Titolo con decorazioni (linea, divider, dot, star), gradiente testo, text stroke |
| **Animated Heading** | Titolo con animazioni (reveal, rotate, typewriter) |
| **Content** | Paragrafo ricco con editor Tiptap WYSIWYG |
| **List** | Lista puntata/numerata |
| **Description List** | Lista definizioni (dl/dt/dd) |
| **Text Mask** | Testo con maschera immagine/video |

### Immagini e Media (14)
| Tile | Descrizione |
|------|-------------|
| **Image** | Immagine con lazy-load, srcset, lightbox, link |
| **Gallery** | Galleria foto in griglia con lightbox |
| **Pro Gallery** | Galleria avanzata con 5 preset bordi puzzle (classic, zigzag, onda, castello, abeti) |
| **Slideshow** | Slideshow immagini con transizioni |
| **Carousel** | Carousel immagini/contenuto |
| **Image Compare** | Before/after con slider |
| **Shattered Image** | Effetto immagine frammentata |
| **Lightbox** | Modale lightbox |
| **Video** | Embed YouTube, Vimeo, self-hosted |
| **Video Playlist** | Playlist video |
| **PDF Viewer** | Viewer PDF: flipbook, pagina singola, doppia, scroll continuo |
| **PDF Pro** | PDF avanzato con annotazioni |
| **Audio** | Player audio |
| **Lottie** | Animazioni Lottie (LottieFiles) |

### Pulsanti e Interazione (6)
| Tile | Descrizione |
|------|-------------|
| **Button** | Pulsante singolo (filled, outline, ghost) |
| **Toggle Button** | Pulsante toggle |
| **Icon** | Icona singola (libreria + custom SVG) |
| **Icon Box** | Icona + titolo + descrizione |
| **Icon List** | Lista con icone personalizzate |
| **Social** | Pulsanti social network |

### Layout Avanzati (14)
| Tile | Descrizione |
|------|-------------|
| **Overlay** | Contenuto con overlay modale |
| **Overlay Grid** | Griglia con overlay |
| **Overlay Slider** | Slider con overlay |
| **Panel** | Pannello collassabile |
| **Panel Slider** | Slider di pannelli |
| **Accordion** | Fisarmonica espandibile |
| **Switcher** | Tab navigation |
| **Switcher Panel** | Pannello switchabile |
| **Popover** | Tooltip popover |
| **Popup** | Modal popup |
| **Offcanvas** | Drawer laterale |
| **Marquee** | Testo/contenuto scorrevole |
| **Flip Card** | Carta con flip animato |
| **Link in Bio** | Layout link-tree |

### Visualizzazione Dati (7)
| Tile | Descrizione |
|------|-------------|
| **Table** | Tabella HTML completa |
| **Pricing** | Card listino prezzi |
| **Price List** | Listino dettagliato |
| **Progress** | Barra di progresso |
| **Progress Tracker** | Tracker multi-step |
| **Counter** | Contatore numerico animato |
| **Counter Circle** | Contatore circolare (gauge) |

### Grafici e Timeline (3)
| Tile | Descrizione |
|------|-------------|
| **Chart** | Grafici Chart.js (bar, line, pie, doughnut, radar) |
| **Timeline** | Timeline verticale/orizzontale |
| **Star Rating** | Valutazione a stelle |

### Hero e Banner (2)
| Tile | Descrizione |
|------|-------------|
| **Hero** | Banner full-width con background (color, gradient, image, video), overlay, CTA doppio, tipografia titolo professionale |
| **Page Title Bar** | Barra titolo pagina con breadcrumb |

### Navigazione (11)
| Tile | Descrizione |
|------|-------------|
| **Nav** | Navbar responsive |
| **Nav Menu** | Menu WordPress personalizzato |
| **Mega Menu** | Menu mega-dropdown con pannelli |
| **Sub Nav** | Sottomenu |
| **Breadcrumbs** | Percorso breadcrumb |
| **Menu Anchor** | Ancoraggio menu (scroll to ID) |
| **Pagination** | Paginazione archivi |
| **Language Switcher** | Switcher lingue (integrazione Olo Lang) |
| **Site Logo** | Logo sito responsive |
| **Dark Mode** | Toggle dark/light mode |
| **To Top** | Pulsante torna su |

### Contenuto Post (6)
| Tile | Descrizione |
|------|-------------|
| **Post Grid** | Griglia articoli con filtri |
| **Post Meta** | Data, autore, categoria |
| **Post Navigation** | Prev/Next articoli |
| **Related Posts** | Articoli correlati |
| **Author Box** | Box autore |
| **Reading Time** | Tempo di lettura stimato |

### Elementi Speciali (15)
| Tile | Descrizione |
|------|-------------|
| **Alert** | Box notifica (info, success, warning, error) |
| **Quotation** | Citazione blockquote |
| **Testimonial** | Card testimonianza |
| **Team** | Card membro team |
| **Code** | Blocco codice con syntax highlighting |
| **HTML** | HTML/CSS/JS raw |
| **Shortcode** | Esecuzione shortcode WordPress |
| **Template Embed** | Includi altro template Olobuild |
| **Countdown** | Timer countdown |
| **News Ticker** | Ticker notizie scorrevole |
| **Hotspot** | Immagine con punti interattivi |
| **Table of Contents** | Indice automatico da headings |
| **Views Counter** | Contatore visite |
| **WP Comments** | Commenti WordPress |
| **Tag Cloud** | Cloud tag/categorie |

### Elementi Creativi (7)
| Tile | Descrizione |
|------|-------------|
| **Text Path** | Testo su percorso SVG |
| **Blend Text** | Testo con blend mode |
| **Shape Divider** | Divisore forma SVG |
| **Scroll Progress** | Barra progresso scroll |
| **Animated Heading** | Heading con effetti animati |
| **Portfolio** | Griglia portfolio filtrata |
| **Pro Slider** | Slider avanzato con transition |

### Mappe (2)
| Tile | Descrizione |
|------|-------------|
| **Map** | Google Maps embed |
| **OSM Map** | OpenStreetMap |

### Form (3)
| Tile | Descrizione |
|------|-------------|
| **Form** | Form builder completo (contact, newsletter) |
| **Login Form** | Form login WordPress |
| **Search** | Form ricerca sito |

### Social e Embed (4)
| Tile | Descrizione |
|------|-------------|
| **Instagram** | Feed Instagram |
| **Facebook Page** | Embed pagina Facebook |
| **Twitter Feed** | Timeline Twitter |
| **Soundcloud** | Player Soundcloud |

### WooCommerce (32 tile)
*(Sezione dedicata sotto)*

### Servizi e Booking (20 tile)
*(Specifici per strutture ricettive — Olo Booking integration)*

### OloRoom (18 tile Vue)
*(Gestione stanze/prenotazioni — componenti frontend)*

---

## Sistema Template

Olobuild gestisce **l'intero sito** tramite template assegnabili:

### Tipi di Template

| Tipo | Descrizione | Assegnazione |
|------|-------------|-------------|
| **Page** | Template per singola pagina/post | Via meta box nell'editor |
| **Header** | Testata globale del sito | Olobuild > Header |
| **Footer** | Piè di pagina globale | Olobuild > Footer |
| **Single** | Template per singolo post type | Olobuild > Single > {post_type} |
| **Archive** | Template per archivio post type | Olobuild > Archive > {post_type} |
| **404** | Pagina errore 404 | Olobuild > 404 |
| **Search** | Pagina risultati ricerca | Olobuild > Search |
| **WooCommerce** | Override pagine shop | Olobuild > WooCommerce |

### Template Conditions

Ogni template può avere **condizioni di visualizzazione** multiple:

- Mostra su **post specifico** (pagina X, articolo Y)
- Mostra su **post type** (tutti gli articoli, tutti i prodotti)
- Mostra su **categoria/tag** specifica
- Mostra per **ruolo utente** (admin, editor, subscriber)
- Mostra in **intervallo date** (promozioni temporanee)
- Logica **AND/OR** tra condizioni
- **Priorità** — il template con priorità più alta vince

### Revisioni

Ogni salvataggio crea una **revisione** nel database. Dalla cronologia revisioni è possibile:
- Visualizzare ogni versione precedente
- **Rollback** a qualsiasi punto nel tempo
- Confrontare le differenze

### Template Library

Libreria di template pre-costruiti:
- **Template built-in** forniti con il plugin
- **Template utente** salvati dall'utente (sezioni, pagine intere)
- Importa/esporta template come **JSON**

---

## Design System Globale

### Colori Globali

Definisci una palette colori coerente per tutto il sito:

| Token CSS | Uso |
|-----------|-----|
| `--olo-color-primary` | Colore principale (brand) |
| `--olo-color-secondary` | Colore secondario |
| `--olo-color-muted` | Sfondi neutri |
| `--olo-color-success` | Feedback positivo |
| `--olo-color-warning` | Avvisi |
| `--olo-color-danger` | Errori |
| `--olo-color-text` | Testo principale |
| `--olo-color-border` | Bordi |
| `--olo-color-link` | Link |

Ogni tile eredita automaticamente i colori globali. Override specifici possibili per singolo tile.

### Tipografia Globale

- **Font family** principale e secondario
- **Dimensioni H1–H6** con valori responsive
- **Line-height** e **font-weight** globali
- **Upload font custom** (WOFF2/WOFF) senza dipendenze esterne

### Design Presets

Preset design completi applicabili con un click:
- **Light** — sfondo chiaro, testo scuro
- **Dark** — sfondo scuro, testo chiaro
- **Custom** — salva e riusa i tuoi preset

### Dark Mode

Sistema dark mode integrato con:
- Varianti colori automatiche
- Toggle dark mode tile
- Rispetto preferenze sistema (`prefers-color-scheme`)

---

## Contenuto Dinamico

Ogni campo di ogni tile può essere collegato a una **sorgente dati dinamica**:

### Sorgenti Dati Disponibili

| Sorgente | Campi |
|----------|-------|
| **Post corrente** | Titolo, contenuto, estratto, immagine in evidenza, permalink, data, autore |
| **Custom Field** | Qualsiasi campo personalizzato del post |
| **ACF** | Tutti i tipi campo ACF (testo, immagine, repeater, option page) |
| **Tassonomia** | Categorie, tag, tassonomie custom |
| **Autore** | Nome, bio, avatar, email, sito |
| **Utente corrente** | Nome, ruolo, avatar, meta |
| **Sito** | Nome sito, descrizione, URL, anno corrente |
| **Data/Ora** | Data corrente, ora, giorno settimana |
| **Request** | Parametri URL, query string |
| **Archivio** | Titolo archivio, descrizione, conteggio |
| **WooCommerce** | Prezzo, stock, SKU, categorie prodotto |
| **Menu** | Voci menu WordPress |
| **Media** | Attributi allegati |
| **Shortcode** | Output di qualsiasi shortcode |
| **Cookie** | Valore cookie specifico |
| **Navigazione post** | Post precedente/successivo |

### Come Usarlo

1. Seleziona un tile
2. Accanto al campo, clicca l'icona **binding dinamico** (fulmine)
3. Scegli sorgente e campo
4. Il valore viene popolato automaticamente dal contesto della pagina

---

## Integrazione WooCommerce

**32 tile dedicati** per costruire ogni aspetto del negozio:

### Pagine Shop

| Tile | Funzione |
|------|----------|
| **Woo Products** | Griglia prodotti con filtri, ordinamento, paginazione |
| **Woo Product Filter** | Filtri avanzati (prezzo, categoria, attributi) |
| **Woo Categories** | Griglia categorie prodotto |
| **Woo Sale Badge** | Badge sconto |
| **Woo Recently Viewed** | Prodotti visti di recente |
| **Woo Comparison** | Tabella confronto prodotti |

### Pagina Prodotto Singolo

| Tile | Funzione |
|------|----------|
| **Woo Product Image** | Immagine principale prodotto |
| **Woo Product Gallery Slider** | Galleria prodotto con slider |
| **Woo Product Title** | Titolo prodotto |
| **Woo Product Description** | Descrizione completa |
| **Woo Price** | Prezzo (normale, scontato, variazioni) |
| **Woo Add to Cart** | Pulsante aggiungi al carrello |
| **Woo Product Meta** | SKU, categorie, tag |
| **Woo Product Tabs** | Tab (descrizione, info aggiuntive, recensioni) |
| **Woo Rating** | Stelle valutazione |
| **Woo Product Stock** | Stato disponibilità |
| **Woo Product Navigation** | Prodotto precedente/successivo |
| **Woo Related** | Prodotti correlati |
| **Woo Upsells** | Prodotti suggeriti (upsell) |
| **Woo Cross Sells** | Cross-sell |
| **Woo Quickview** | Anteprima rapida modale |
| **Woo Product Bundle** | Bundle prodotti |

### Carrello e Checkout

| Tile | Funzione |
|------|----------|
| **Woo Cart** | Pagina carrello completa |
| **Woo Mini Cart** | Widget mini-carrello (header) |
| **Woo Checkout** | Checkout standard |
| **Woo Checkout Multistep** | Checkout multi-step (dati > spedizione > pagamento) |
| **Woo Notices** | Notifiche carrello/checkout |

### Account e Ordini

| Tile | Funzione |
|------|----------|
| **Woo My Account** | Dashboard account cliente |
| **Woo Order Tracking** | Tracciamento ordini |
| **Woo Wishlist** | Lista desideri |
| **Woo Breadcrumbs** | Breadcrumb WooCommerce |

---

## Ricerca Media Stock

Pagina dedicata (**Olobuild > Ricerca Media**) per cercare e importare media gratuiti:

| Provider | Tipo | Licenza |
|----------|------|---------|
| **Unsplash** | Foto | Unsplash License (gratuita commerciale) |
| **Pexels** | Foto + Video | Pexels License (gratuita commerciale) |
| **Pixabay** | Foto + Video | Pixabay License (gratuita commerciale) |
| **Openverse** | Foto | Creative Commons (varie) |
| **Freesound** | Audio | Creative Commons (varie) |
| **LottieFiles** | Animazioni | Lottie License |

**Funzionamento:**
1. Cerca per parole chiave
2. Filtra per orientamento, dimensione, durata (video)
3. Anteprima nel modale
4. **Un click per importare** direttamente nella Media Library di WordPress

---

## Assistente AI

Integrazione con **Claude (Anthropic)** e **DALL-E (OpenAI)**:

### Funzionalità

| Endpoint | Cosa fa |
|----------|---------|
| **Genera Testo** | Crea contenuti (titoli, paragrafi, descrizioni) |
| **Migliora Testo** | Riscrive e migliora testo esistente |
| **Traduci** | Traduzione in qualsiasi lingua |
| **Genera Immagine** | Crea immagini da prompt (DALL-E) |
| **Genera Layout** | Suggerisce struttura pagina |
| **Suggerisci Stile** | Propone combinazioni colori/font |
| **Genera Alt Text** | Alt text SEO per immagini |
| **Genera CSS** | Scrive CSS custom |

### Configurazione
Inserire le API key in **Olobuild > Impostazioni > AI**:
- **Anthropic API Key** — per funzioni testo/layout/stile
- **OpenAI API Key** — per generazione immagini (opzionale)

---

## Form Builder

Il tile **Form** permette di creare form contatto senza plugin esterni.

### Caratteristiche

- **Campi**: testo, email, textarea, select, checkbox, radio, file upload
- **Validazione**: lato client + lato server
- **Sicurezza**: token HMAC con scadenza 12 ore
- **Rate limiting**: limite submission per IP (anti-spam)
- **Upload file**: validazione MIME type e dimensione massima
- **Notifiche email**: al proprietario del sito + risposta automatica all'utente
- **Database submission**: storico completo in admin
- **Export CSV**: esporta tutte le submission

### Gestione Submission
In **Olobuild > Form Submissions**:
- Lista tutte le submission ricevute
- Filtra per form, data, stato (letta/non letta)
- Elimina singole o in blocco
- Esporta in CSV

---

## Performance e SEO

### Critical CSS Automatico
- Genera CSS critico per il contenuto above-the-fold di ogni pagina
- Cache per template ID
- Migliora significativamente il **Largest Contentful Paint (LCP)**
- Rigenerazione in blocco + purge cache

### Asset Optimizer
- Minificazione CSS/JS
- Lazy-load immagini e risorse
- Caricamento condizionale (solo i CSS/JS dei tile usati nella pagina)

### SEO Head
- Meta tag personalizzabili per pagina (title, description)
- Open Graph (og:title, og:description, og:image)
- Twitter Card
- Canonical URL

### Performance Hints
- Suggerimenti automatici per migliorare le performance
- Analisi peso pagina e richieste HTTP

---

## Funzionalità Avanzate

### White Label
Personalizza il branding per rivendita:
- Nome plugin personalizzato
- Logo custom
- Colori interfaccia
- Nascondi crediti

### Maintenance Mode
Pagina manutenzione con:
- Template Olobuild personalizzabile
- Accesso admin preservato
- Attivazione/disattivazione da admin

### Cookie Consent
Banner consenso cookie integrato:
- Personalizzazione testo e colori
- Blocco script prima del consenso
- Conforme GDPR

### Analytics
Integrazione Google Analytics:
- GA4 tracking code
- Inserimento automatico in head

### A/B Testing
Test varianti per template:
- Crea variante A e B
- Split traffic automatico
- Confronta metriche

### Role Manager
Gestisci chi può accedere al builder:
- Permessi per ruolo WordPress
- Accesso granulare a funzionalità specifiche

### Custom Code
Inserisci snippet personalizzati:
- **Head** — CSS custom, meta tag, script
- **Body (apertura)** — tracking pixel, widget
- **Footer** — script, chat widget

### Custom Fonts
Carica font personalizzati senza dipendenze esterne:
- Upload WOFF2/WOFF
- Disponibili immediatamente nel font picker
- Nessuna richiesta a Google Fonts (privacy GDPR)

### Import/Export Sito
- Esporta l'intera configurazione del sito (template, stili, impostazioni)
- Importa su un altro sito WordPress
- Utile per migrazione o staging

---

## Confronto Tecnico con i Competitor

Confronto puramente tecnico sulle capacità di costruzione sito. Nessun riferimento a prezzo, ecosistema, community, documentazione o maturità del prodotto.

---

### 1. Architettura e Stack

| Aspetto | Olobuild | Elementor Pro | YOOtheme Pro | Avada |
|---|---|---|---|---|
| **Framework builder** | Vue.js 3 + Pinia | React 18 | Vue.js 2 | jQuery + Backbone |
| **Bundler** | Vite 5 | webpack | webpack | webpack |
| **Framework CSS frontend** | UIkit 3 | Proprio (Swiper, eSwiper) | UIkit 3 | Bootstrap 5 + Fusion |
| **Editor testo ricco** | Tiptap (ProseMirror) | Proprio (inline editing) | Proprio (inline editing) | TinyMCE |
| **State management** | Pinia (reactive store) | Redux-like custom | Vuex | Custom pub/sub |
| **API backend** | WP REST API (namespace dedicato) | WP REST API + Ajax | WP REST API + Ajax | Ajax custom |
| **Database** | 3 tabelle custom (template, revisioni, form) | Post type + postmeta | Post type + postmeta | Post type + postmeta + 3 tabelle |
| **Rendering frontend** | PHP server-side (HTML puro) | PHP server-side (div wrapper) | PHP server-side (HTML puro) | PHP server-side (shortcode-based) |

**Note tecniche:**
- Olobuild e YOOtheme condividono UIkit 3 come framework frontend, ma Olobuild usa Vue 3 (Composition API) mentre YOOtheme è fermo a Vue 2 (Options API)
- Elementor genera markup con molti wrapper `<div>` (nesting profondo); Olobuild e YOOtheme producono HTML più pulito
- Avada usa ancora jQuery come dipendenza principale nel builder — stack più datato rispetto ai framework reattivi

---

### 2. Sistema di Layout

| Aspetto | Olobuild | Elementor Pro | YOOtheme Pro | Avada |
|---|---|---|---|---|
| **Modello layout** | Griglia CSS tile (free placement) | Sezione > Colonna > Widget | Sezione > Riga > Colonna | Container > Colonna > Elemento |
| **Nesting** | Illimitato (grid dentro grid) | 1 livello inner section (2 con Container) | 1 livello nesting | 1 livello nesting |
| **CSS Grid nativo** | Si (base del sistema) | Si (Container, opt-in) | No (Flexbox) | Si (Container, recente) |
| **Drag & drop libero** | Si (posizione in griglia) | No (sequenziale in colonna) | No (sequenziale in riga) | No (sequenziale) |
| **Span colonne/righe** | Si (per cella) | No | No | No |
| **Resize visuale cella** | Si | Si (resize colonna %) | Si (resize colonna %) | Si (resize colonna %) |

**Differenza chiave:** Olobuild posiziona i tile su una **griglia 2D** dove ogni cella può occupare N colonne e N righe. Gli altri builder sono **sequenziali**: gli elementi si impilano uno dopo l'altro dentro colonne a larghezza fissa. Il modello a griglia è più flessibile per layout asimmetrici, ma richiede più consapevolezza nella gestione dei breakpoint.

---

### 3. Responsive Design

| Aspetto | Olobuild | Elementor Pro | YOOtheme Pro | Avada |
|---|---|---|---|---|
| **Breakpoint** | 6 fissi | 7 (3 default + 4 custom) | 3 (S, M, L) | 6 fissi |
| **Breakpoint custom** | No | Si (qualsiasi px) | No | No |
| **Override per breakpoint** | Ogni campo con icona responsive | Ogni campo con icona responsive | Ogni campo con icona responsive | Ogni campo con icona responsive |
| **Preview device** | Si (6 viewport) | Si (7 viewport) | Si (3 viewport) | Si (6 viewport) |
| **Visibilità per device** | Si | Si | Si | Si |
| **Ordine colonne per device** | Si (CSS Grid order) | Si (column order) | Si | Si |

**Punto debole Olobuild:** Elementor permette breakpoint custom a qualsiasi valore px — utile per progetti con specifiche precise. Olobuild ha 6 breakpoint fissi, che coprono i casi comuni ma non sono personalizzabili.

**Punto debole YOOtheme:** Solo 3 breakpoint (small, medium, large) — per layout complessi su tablet landscape vs portrait serve workaround CSS.

---

### 4. Elementi Nativi — Confronto per Categoria

Conteggio degli elementi **inclusi nel prodotto base** senza addon di terze parti.

| Categoria | Olobuild | Elementor Pro | YOOtheme Pro | Avada |
|---|:---:|:---:|:---:|:---:|
| Layout & Struttura | 5 | 4 | 4 | 5 |
| Testo & Titoli | 6 | 4 | 3 | 4 |
| Immagini & Media | 14 | 8 | 6 | 7 |
| Video & Audio | 4 | 2 | 1 | 2 |
| Navigazione & Menu | 11 | 5 | 4 | 6 |
| Pulsanti & CTA | 3 | 2 | 1 | 2 |
| Icone & Social | 4 | 4 | 3 | 4 |
| Layout avanzati (accordion, tab, popup...) | 14 | 8 | 6 | 8 |
| Dati & Grafici (tabelle, progress, chart...) | 10 | 4 | 2 | 5 |
| Contenuto post (meta, nav, related...) | 6 | 6 | 4 | 5 |
| Form | 3 | 2 | 0 | 1 |
| WooCommerce | 32 | 18 | 12 | 20 |
| Elementi creativi (text-path, mask, blend...) | 7 | 5 | 2 | 3 |
| Elementi speciali (alert, code, HTML, shortcode...) | 15 | 10 | 5 | 8 |
| Settore verticale (booking, room, service) | 38 | 0 | 0 | 0 |
| **Totale** | **172** | **~82** | **~53** | **~80** |

**Nota importante:** I 38 tile verticali (service, booking, oloroom) sono specifici per il settore ricettivo e non hanno equivalente diretto negli altri builder. Se si escludono, il confronto diventa **134 vs ~82 vs ~53 vs ~80** — comunque nettamente superiore.

**Nota su Elementor:** Il conteggio esclude addon di terze parti (Crocoblock, Essential Addons, etc.) che portano Elementor a 300+ widget. Ma si tratta di prodotti separati a pagamento.

---

### 5. WooCommerce — Profondità Integrazione

| Funzionalità | Olobuild | Elementor Pro | YOOtheme Pro | Avada |
|---|:---:|:---:|:---:|:---:|
| **Griglia prodotti con filtri** | Si | Si | Si | Si |
| **Pagina prodotto personalizzabile** | Si (16 tile dedicati) | Si (12 widget) | Si (layout builder) | Si (layout builder) |
| **Carrello custom** | Si | Si | No (usa WC default) | Si |
| **Checkout custom** | Si | Si | No | Si |
| **Checkout multi-step** | Si (nativo) | Si (nativo) | No | No |
| **Mini cart (header)** | Si | Si | No | Si |
| **Filtri prodotto avanzati** | Si (prezzo, categoria, attributi) | Si | No | Si |
| **Quick view modale** | Si | No (serve addon) | No | No |
| **Wishlist** | Si | No (serve addon) | No | No |
| **Confronto prodotti** | Si | No (serve addon) | No | No |
| **Bundle prodotti** | Si | No | No | No |
| **Order tracking** | Si | No | No | No |
| **Prodotti visti di recente** | Si | No (serve addon) | No | No |
| **Navigazione prev/next prodotto** | Si | Si | No | No |
| **Badge sconto custom** | Si | Si | No | Si |
| **Account utente custom** | Si | Si | No | Si |

**Olobuild** ha l'integrazione WooCommerce più profonda con 32 tile che coprono wishlist, confronto, bundle, quick view e order tracking — funzionalità che negli altri builder richiedono plugin aggiuntivi (WooCommerce Wishlist, YITH plugins, etc.).

**YOOtheme Pro** ha l'integrazione WooCommerce più limitata: permette di personalizzare la griglia prodotti e la pagina prodotto, ma carrello, checkout e account usano i template WC default.

---

### 6. Contenuto Dinamico

| Sorgente dati | Olobuild | Elementor Pro | YOOtheme Pro | Avada |
|---|:---:|:---:|:---:|:---:|
| Post corrente (titolo, contenuto, estratto, immagine) | Si | Si | Si | Si |
| Custom fields (postmeta) | Si | Si | Si | Si |
| ACF (tutti i tipi campo) | Si | Si | Si | Si |
| ACF Repeater | Si | Si (Loop) | Si | No |
| ACF Option Page | Si | Si | Si | No |
| Tassonomie (categorie, tag, custom) | Si | Si | Si | Si |
| Autore (nome, bio, avatar) | Si | Si | Si | Si |
| Utente corrente | Si | Si | No | Si |
| Informazioni sito (nome, URL, descrizione) | Si | Si | Si | Si |
| Data/Ora corrente | Si | Si | No | Si |
| Parametri URL / query string | Si | Si | No | No |
| Cookie | Si | No | No | No |
| Shortcode (output come valore dinamico) | Si | Si | No | No |
| Navigazione post (prev/next) | Si | Si | No | No |
| WooCommerce (prezzo, stock, SKU) | Si | Si | Si | Si |
| Menu WordPress | Si | No | Si | No |
| **Sorgenti totali** | **16** | **~12** | **~9** | **~9** |

**Olobuild** ha il sistema dinamico più esteso (16 sorgenti), con sorgenti uniche come cookie e parametri URL. **Elementor** è secondo con ~12 sorgenti. **YOOtheme** e **Avada** coprono i casi principali ma mancano sorgenti avanzate.

---

### 7. Rendering e Performance Frontend

| Aspetto | Olobuild | Elementor Pro | YOOtheme Pro | Avada |
|---|---|---|---|---|
| **Markup generato** | HTML semantico + classi UIkit | `<div>` wrapper multipli | HTML semantico + classi UIkit | `<div>` wrapper + classi Fusion |
| **CSS framework** | UIkit 3 (~30 KB gzip, selective) | Proprio (~80 KB gzip) | UIkit 3 (~30 KB gzip) | Bootstrap + Fusion (~60 KB gzip) |
| **JS framework frontend** | UIkit JS (~20 KB gzip, selective) | Swiper + proprio (~100 KB gzip) | UIkit JS (~20 KB gzip) | jQuery + Fusion (~80 KB gzip) |
| **Caricamento condizionale asset** | Si (solo CSS/JS dei tile usati) | Parziale (carica base + usati) | Si (solo componenti usati) | Parziale (carica Fusion + usati) |
| **Critical CSS automatico** | Si (generazione + cache per template) | No | No | No |
| **CSS inline** | Stili per tile inline nell'HTML | Stili in `<style>` nel `<head>` | Stili in `<style>` + inline | Stili in `<style>` nel `<head>` |
| **Lazy load immagini** | Si (nativo browser + UIkit) | Si (proprio + IntersectionObserver) | Si (UIkit) | Si (proprio) |

**Nota onesta:** Il critical CSS automatico è un vantaggio reale di Olobuild per i Core Web Vitals. Tuttavia, soluzioni esterne come WP Rocket o Autoptimize offrono la stessa funzionalità per qualsiasi builder. Il vantaggio è nell'integrazione nativa (zero configurazione, cache per template ID).

Elementor genera più DOM nodes per via dei wrapper `<div class="elementor-element">` su ogni widget, ma questo ha il vantaggio di un sistema di styling più prevedibile.

---

### 8. Builder UX — Strumenti di Editing

| Funzionalità | Olobuild | Elementor Pro | YOOtheme Pro | Avada |
|---|:---:|:---:|:---:|:---:|
| **Undo/Redo** | Si (storico illimitato) | Si (storico limitato) | Si | Si |
| **Autosave** | Si | Si | Si | Si |
| **Revisioni con rollback** | Si (tabella dedicata) | Si (revisioni WP) | Si (revisioni WP) | Si (revisioni WP) |
| **Copia/Incolla tra pagine** | Si | Si | Si | Si |
| **Context menu (tasto destro)** | Si | Si | No | No |
| **Finder (ricerca elemento)** | Si (Ctrl+F) | Si (Ctrl+E) | No | No |
| **Albero struttura** | Si (sidebar con show/hide/lock) | Si (Navigator) | Si (albero semplice) | No |
| **Inline editing testo** | No (editing nell'inspector) | Si (click diretto sul canvas) | Si (click diretto sul canvas) | Parziale |
| **AI integrato** | Si (Claude testo + DALL-E immagini) | Si (AI proprietario) | No | Si (beta) |
| **Stock media integrati** | Si (5 provider: Unsplash, Pexels, Pixabay, Openverse, Freesound) | No | No | No |
| **Scorciatoie tastiera** | Si (15+ shortcut) | Si (20+ shortcut) | Limitate | Limitate |
| **Global widgets (riusabili)** | Si | Si | No | Si (Library) |
| **Design presets** | Si (built-in + custom) | No (serve Starter Templates) | Si (Style Library) | Si (prebuilts) |

**Punto debole Olobuild:** Nessun inline editing — il testo si modifica sempre nell'inspector laterale, non cliccando direttamente sul canvas. Elementor e YOOtheme permettono di cliccare sul titolo e digitare direttamente. Questo rallenta il workflow di editing testuale.

**Punto forte Olobuild:** 5 librerie media stock integrate direttamente nel builder. In nessun altro builder puoi cercare "montagna" e importare una foto da Unsplash/Pexels/Pixabay senza uscire dall'editor.

---

### 9. Sistema Ombre — Esempio di Profondità Controllo

Un esempio concreto del livello di controllo offerto da Olobuild su un singolo aspetto stilistico:

| Controllo ombra | Olobuild | Elementor Pro | YOOtheme Pro | Avada |
|---|:---:|:---:|:---:|:---:|
| **Preset ombra (sm/md/lg)** | Si | Si | Si | Si |
| **Ombra custom (H, V, Blur, Spread, Color)** | Si (su tutti i tile) | Si | No (solo preset) | Si |
| **Ombra interna (inset)** | Si | Si | No | Si |
| **Ombra hover** | Si | Si | No | Si |
| **Ombra per elemento interno** (es. card dentro grid) | Si (prefix separato: card_shadow, widget_shadow) | Parziale | No | Parziale |

YOOtheme Pro offre solo preset ombra senza possibilità di personalizzazione dei valori — filosofia "semplicità sopra tutto".

---

### 10. Template System

| Aspetto | Olobuild | Elementor Pro | YOOtheme Pro | Avada |
|---|:---:|:---:|:---:|:---:|
| **Header builder** | Si | Si | Si | Si |
| **Footer builder** | Si | Si | Si | Si |
| **Single post type** | Si | Si | Si | Si |
| **Archive template** | Si | Si | Si | No (usa opzioni tema) |
| **404 template** | Si | Si | No | Si |
| **Search results template** | Si | Si | No | Si |
| **WooCommerce template override** | Si | Si | Parziale | Si |
| **Condizioni di visualizzazione** | Si (AND/OR, priorità) | Si (AND/OR) | Si (semplice) | Si (Layout Builder) |
| **Condizione per ruolo utente** | Si | Si | No | Si |
| **Condizione per data/ora** | Si | No | No | No |
| **Template come shortcode** | Si (`[olo_template id=""]`) | Si | No | No |
| **Query Loop (WP_Query custom)** | Si | Si (Loop Builder) | Si (Source) | No |

**Olobuild** e **Elementor** hanno il template system più completo. **YOOtheme** gestisce header/footer/single ma non 404 e search separatamente. **Avada** non ha un vero archive template builder — gestisce gli archivi tramite opzioni tema.

---

### 11. Funzionalità Integrata vs Plugin Esterni

Confronto di cosa è **incluso nativamente** e cosa richiede plugin aggiuntivi per essere ottenuto:

| Funzionalità | Olobuild | Elementor Pro | YOOtheme Pro | Avada |
|---|---|---|---|---|
| **Form builder** | Integrato | Integrato | Serve plugin (CF7, WPForms...) | Integrato (Avada Form) |
| **Popup/Modal** | Integrato | Integrato (Popup Builder) | Serve plugin | Serve plugin |
| **Ricerca media stock** | 5 provider integrati | Non disponibile | Non disponibile | Non disponibile |
| **AI content** | Integrato (Claude + DALL-E) | Integrato (Elementor AI) | Non disponibile | Beta |
| **Cookie consent** | Integrato | Serve plugin (CookieYes...) | Serve plugin | Serve plugin |
| **Critical CSS** | Integrato | Serve plugin (WP Rocket...) | Serve plugin | Serve plugin |
| **A/B testing** | Integrato | Serve plugin (Nelio A/B...) | Serve plugin | Serve plugin |
| **SEO meta tags** | Integrato (base) | Serve plugin (Yoast, Rank Math) | Serve plugin | Serve plugin |
| **Analytics tracking** | Integrato (GA4) | Serve plugin | Serve plugin | Serve plugin |
| **Custom fonts upload** | Integrato | Integrato | Integrato | Integrato |
| **White label** | Integrato | Solo piano Agency | Non disponibile | Integrato |
| **Maintenance mode** | Integrato | Integrato | Serve plugin | Serve plugin |
| **PDF Viewer** | Integrato (flipbook, scroll, doppia pagina) | Serve plugin | Non disponibile | Serve plugin |
| **Chart/Grafici** | Integrato (Chart.js) | Serve addon | Non disponibile | Serve addon |
| **Wishlist WooCommerce** | Integrato | Serve addon (YITH) | Non disponibile | Serve addon |
| **Confronto prodotti** | Integrato | Serve addon (YITH) | Non disponibile | Non disponibile |
| **Checkout multi-step** | Integrato | Integrato | Non disponibile | Non disponibile |

**Olobuild** integra nativamente funzionalità che negli altri builder richiedono 5-8 plugin aggiuntivi. Questo riduce conflitti tra plugin, semplifica gli aggiornamenti e mantiene coerenza nell'interfaccia.

Il rovescio della medaglia: ogni funzionalità integrata è meno specializzata della versione standalone. Il form builder di Olobuild è funzionale ma non raggiunge WPForms Pro. Il cookie consent è base rispetto a CookieYes. La SEO è basilare rispetto a Rank Math.

---

### 12. Riepilogo Punti di Forza e Debolezza (tecnici)

#### Dove Olobuild è tecnicamente superiore

- **Numero elementi nativi** — 168 (134 escludendo verticali) vs 53-82 dei competitor
- **WooCommerce** — 32 tile con wishlist, confronto, bundle, quickview nativi
- **Media stock** — unico builder con 5 provider integrati
- **Griglia 2D** — posizionamento libero su CSS Grid, non sequenziale
- **Nesting illimitato** — grid dentro grid senza limiti
- **Stack moderno** — Vue 3 + Vite 5 + Pinia (compilazione più veloce, reattività migliore)
- **Funzionalità integrate** — form, popup, cookie, analytics, critical CSS, A/B test, PDF viewer, chart senza plugin esterni
- **Contenuto dinamico** — 16 sorgenti (incluse cookie e parametri URL)

#### Dove Olobuild è tecnicamente inferiore

- **Nessun inline editing** — il testo si modifica nell'inspector, non sul canvas
- **Breakpoint fissi** — 6 breakpoint non personalizzabili (Elementor ne permette custom)
- **Nessun motion design avanzato** — Elementor ha Scroll Effects, Mouse Effects, Entrance Animations con timeline. Olobuild ha AOS (scroll reveal) e animazioni base
- **Nessun theme builder visuale** — non c'è un'interfaccia grafica per mappare "questo template va su questi post" (si fa da opzioni, non drag & drop)
- **Nessun editing multi-pagina** — si lavora su un template alla volta, non c'è un flusso "sito intero"
- **Responsive manuale** — la griglia 2D richiede più attenzione manuale per i breakpoint rispetto al sistema colonna che "collassa" naturalmente

#### Dove sono tutti alla pari

- Header/footer builder, revisioni, undo/redo, autosave
- Custom fonts upload, contenuto dinamico base (post, ACF, tassonomie)
- Lazy load, copia/incolla, import/export template

---

## Glossario

| Termine | Significato |
|---------|-----------|
| **Tile** | Singolo elemento/blocco nel builder (equivalente di "widget" in Elementor) |
| **Section** | Contenitore di primo livello che ospita la griglia |
| **Grid** | Griglia CSS responsive che contiene i tile |
| **Cell** | Cella della griglia che ospita un tile |
| **Inspector** | Pannello laterale destro per modificare le proprietà del tile selezionato |
| **Breakpoint** | Punto di rottura responsivo (desktop, tablet, mobile...) |
| **Template** | Layout salvato (pagina, header, footer, single, archive...) |
| **Dynamic binding** | Collegamento di un campo a una sorgente dati che si aggiorna automaticamente |
| **Design token** | Variabile CSS globale (colore, font, spaziatura) condivisa da tutti i tile |
| **Critical CSS** | CSS minimo necessario per il rendering above-the-fold, iniettato inline |

---

*Olobuild v2.7.2 — Page builder professionale per WordPress*
