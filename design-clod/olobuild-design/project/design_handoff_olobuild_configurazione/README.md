# Handoff: OLObuild — Pagina Configurazione (redesign)

## Overview

Redesign della pagina **Configurazione** del plugin WordPress OLObuild (URL `admin.php?page=olobuilder-settings`). La pagina originale era una lista verticale piatta di 6 tab — questo redesign la trasforma nell'**hub centrale di tutte le impostazioni globali del sito**, riorganizzato in **4 gruppi semantici con 12 voci** (più 1 future "Soon").

### Decisioni di information architecture

Regola guida adottata: **"set-once-applies-everywhere"** vive in Configurazione; **"do-something / look-at-data"** vive nel dashboard genitore (`?page=olobuild`).

Da questa regola sono nate due mosse strutturali:

1. **Migrazioni dal dashboard genitore** — 5 voci che erano card del dashboard ma sono impostazioni globali tornano qui:
   - SEO globale (ex card "SEO" in GESTIONE)
   - Cookie Consent & GDPR (ex card "Cookie Consent" in GESTIONE)
   - Performance & Cache (ex card "Performance" in GESTIONE)
   - White Label (ex pulsante "White Label" in SISTEMA)
   - Permessi & Ruoli (ex pulsante "Permessi & Ruoli" in SISTEMA)

2. **Rinominazione** — l'ex tab "API & Integrazioni" diventa **"Stock media"** perché di fatto conteneva solo chiavi API stock media; il nome generico era fuorviante.

Risultato: l'utente cerca le impostazioni in **un solo posto**, e il dashboard diventa un vero centro operativo con solo card di azione/dato.

## About the Design Files

I file in questo bundle sono **mockup React/HTML di riferimento** — prototipi che mostrano look & behavior finali, non codice di produzione da incollare. Vanno **ricostruiti nell'ambiente di OLObuild** (probabilmente PHP/WordPress admin + il framework JS che il plugin già usa) seguendo i pattern e le librerie del codebase esistente.

Il prototipo è scritto in **React + Babel inline + CSS plain** per essere navigabile da browser senza build, ma la logica e l'IA si traducono in qualunque stack: l'importante è preservare gerarchia, raggruppamenti, comportamenti e token visivi documentati qui sotto.

## Fidelity

**High-fidelity.** Layout, spaziature, tipografia, palette, comportamenti interattivi (selezione tab, sidebar attiva, save bar dirty, segmented control) sono definitivi. Le copy in italiano sono il riferimento di tono di voce.

I campi all'interno dei tab **migrati** (SEO, Cookie, Performance, White Label, Permessi) sono inferiti — vanno confrontati con i campi esistenti delle relative pagine del dashboard genitore prima del rilascio.

## Layout

### Shell generale (`.cfg-root`)

Grid 264px sidebar / 1fr content, con topbar (56px) sopra e save bar (64px) sotto:

```
┌──────────────────────────────────────────────────────────┐
│  TOPBAR  (56px)                                          │
├────────────┬─────────────────────────────────────────────┤
│            │                                             │
│  SIDEBAR   │   CONTENT                                   │
│  264px     │   padding 32px 40px 40px                    │
│            │   max-width container, overflow-y auto      │
│            │                                             │
├────────────┴─────────────────────────────────────────────┤
│  SAVE BAR  (64px, sticky)                                │
└──────────────────────────────────────────────────────────┘
```

Il root ha `overflow: hidden` — solo content e sidebar scrollano internamente.

### Topbar (`.cfg-topbar`)

Sticky, `#fff`, border-bottom `#e6e8ee`. Da sx a dx:
- **Brand** — quadrato 26×26 rosso (#e1474f) + scritta `olo` (navy 800) `build` (red 800), letter-spacing -.03em, font-size 18px.
- **Breadcrumb** — separato dal brand da border-left + padding-left 24px; testo 13px muted; ultimo nodo bold navy. Pattern: `Dashboard / Configurazione / [tab attivo]`.
- **Spacer flex:1**.
- **Search globale** — pill `#f6f7fa` con border `#e6e8ee`, padding 7×12px, width 320px. Placeholder "Cerca un'impostazione…" + kbd `⌘ K` a destra.
- **Doc link + Anteprima sito** — testo 13px muted con icona Lucide 14px, padding 7×12 hover bg `#f6f7fa`.

### Sidebar (`.cfg-sidebar`)

**Variant Console** (la direzione scelta): background `#0f172a` (navy), texts on dark, accent rosso `#e1474f` per item attivo.

- **Side search** — pill semi-trasparente `rgba(255,255,255,.06)` con border `rgba(255,255,255,.08)`, padding 8×10, font 13px.
- **Gruppi** — header uppercase 10px letter-spacing .12em color `rgba(255,255,255,.4)` + count badge a dx. Padding 10×12 6 12.
- **Item** — grid `28px 1fr auto` gap 8, padding 8×10, margin 1×4, border-radius 8. Icona Lucide 14px in un quadrato 26×26 con bg semi-trasparente. Label 13.5px medium. Chevron a dx muted.
- **Item attivo** — bg `#e1474f`, color `#fff`, icona con bg `rgba(255,255,255,.18)`.
- **Item "Soon"** — color `rgba(255,255,255,.35)`, cursor not-allowed, pill `SOON` muted.
- **Item "MIGRATO"** — pill arancione `↓` con bg `rgba(245,158,11,.15)` color `#fbbf24`.
- **Footer** — licenza con dot ok verde + versione mono 11px.

### Content (`.cfg-content`)

Padding 32 40 40. Ogni tab inizia con:

- **Page head** — h1 sans 28px/700 letter-spacing -.02em (navy), con `em` red per la parte enfatica (es. "Palette _colori_"). Sotto: paragrafo 14px muted, max-width 60ch. A destra: head-actions (button secondary o status pill).
- Bordo `1px solid #e6e8ee` con margin-bottom 28 padding-bottom 24.

Poi una sequenza di **card** (`.cfg-card`) — bg `#fff`, border `#e6e8ee`, border-radius 14, margin-bottom 16:

- **Card head** — padding 18×22, border-bottom soft. Head icon quadrata 36×36 radius 9, bg `#f6f7fa`. h3 15px/600 navy. Sotto p 13px muted.
- **Card body** — padding 22 (o 16×22 con classe `.tight`).

### Form rows (`.cfg-row`)

Grid `220px 1fr` gap 32, padding 14 0, divider bottom soft:
- **Label col** — label 13px/600 navy, hint 12px muted line-height 1.5.
- **Control col** — il controllo riempie la larghezza.

### Controlli — anatomia

- **`.cfg-input`** — bg `#fff`, border `#e6e8ee`, border-radius 8, padding 8×12, font 13.5px. Focus: border `#e1474f` + shadow `0 0 0 3px #fef2f3`. Varianti: `.mono`, `.with-prefix`, `.with-suffix`, `.password` (con icona occhio).
- **`.cfg-select`** — stesso del input + chevron interno.
- **`.cfg-textarea`** — padding 10×12, mono 12.5px, min-height 88, align-items flex-start.
- **`.cfg-segment`** — pill `#f6f7fa` border `#e6e8ee` radius 8, padding 3, gap 2; buttons 6×12 600/12.5. Stato `is-on`: bg `#fff` shadow xs.
- **`.cfg-switch`** — 36×20 bg `#cbd5e1`. On: bg `#e1474f`. Knob 16×16 con shadow.
- **`.cfg-slider`** — track 6 alto bg `#f6f7fa`, fill `#e1474f`, knob 14 bordo 2 `#e1474f`. Value mono 12.5 tabular-nums.
- **`.cfg-pill`** — uppercase 11/700 letter-spacing .04em padding 4×8 radius 5. Varianti `.ok` (green soft), `.warn` (amber soft), `.off` (gray), `.new` (red soft).

### Bottoni

- **`.cfg-btn-primary`** — bg `#e1474f`, text white, shadow `0 1px 2px rgba(15,23,42,.08), inset 0 -1px 0 rgba(0,0,0,.12)`, hover `#c8323a`.
- **`.cfg-btn-secondary`** — bg `#fff`, border `#e6e8ee`, text navy.
- **`.cfg-btn-ghost`** — transparent, text muted.
- **`.cfg-btn-danger`** — text red, border red soft.
- **`.cfg-btn-icon`** — 32×32 quadrato.
- Tutti: padding 8×14, radius 8, font 600/13, icona 14×14.

### Save bar

64px alto, sticky bottom, bg `#fff` border-top:
- **Meta** — sx, 12.5px muted. Se `dirty` mostra pill arancione "● Modifiche non salvate" prima di "Ultimo salvataggio _data_".
- **Actions** — dx: ghost "Annulla modifiche", secondary "Anteprima", primary "Salva impostazioni".

## Screens / Views

Tutti i tab hanno la stessa shell (topbar + sidebar + savebar). Cambia solo il content. La sidebar resta a sx con la voce attiva evidenziata.

### Gruppo DESIGN

#### 1. Stili & Preset (`presets`)
- Page head + 2 azioni (Esporta preset, Crea preset).
- Card "Preset disponibili": segmented (Sistema | Personalizzati | Marketplace), grid 4 colonne di card preset. Ogni card 14px padding, border 1px (active 2px red + 0 0 0 4px red-soft shadow), 4 swatch 54px alti, nome 14/600, tag 12 muted, pill "✓ Attivo" in alto a dx.
- Card "Comportamento dei preset": switch sovrascrivi modifiche manuali, switch crea snapshot, select modalità anteprima.

#### 2. Palette colori (`colori`)
- Page head + 2 azioni (Importa da Coolors, Genera con AI).
- Card "Colori del brand" con pill status `AA Verificato`: lista 6 colori (Primary, Secondary, Tertiary, Success, Warning, Danger) — riga grid `56px 1fr 220px 36px`. Swatch 56×56 + nome/ruolo + input hex mono + chevron. Bottone tratteggiato "+ Aggiungi colore custom".
- Card "Scala neutri": grid 7 colonne di swatch 64 alti con label tier (50, 100, … 950) mono sotto.
- Card "Modalità dark": switch on/off + select strategia inversione.

#### 3. Tipografia (`tipografia`)
- Card "Coppia di font": 2 card grid (display vs body), ognuna con preview grande (sample text) + select font + pill stati (italic, weights disponibili).
- Card "Scala tipografica": dimensione base, ratio scala (Major Third etc.), slider interlinea, preview live con 5 livelli (H1, H2, H3, body, small).

#### 4. Spaziature & layout (soon)
Placeholder — disegnare dopo conferma utente.

#### 5. Breakpoint responsive (`responsive`)
- Card "Visualizzazione scala": barra colorata 60px con 5 zone (Mobile, Tablet, Laptop, Desktop, XL) + tick mono sotto (0 / 576 / 768 / 992 / 1200 / 1440 / ∞).
- Card "Breakpoint configurati": lista riordinabile, ogni riga grid `28 36 1fr 100 100 80 36`. Drag handle + icona device + nome + input min/max + switch default + X.
- Card "Comportamento avanzato": segmented mobile-first/desktop-first + input container width + slider gutter.

### Gruppo SEO & PRIVACY

#### 6. SEO globale (`seo`) — MIGRATO
- Banner arancio "MIGRATO" in alto.
- Card "Default site-wide": title pattern (input mono con `{page} {sep} {site}`), separatore (select), meta description default (textarea con counter caratteri), lingua sito, robots default (segmented).
- Card "Open Graph & Twitter Card": OG image preview 160×84 + Sostituisci, Twitter handle, card type segmented.
- Card "Sitemap & schema.org": switch sitemap XML, select tipo organizzazione (Hotel, Restaurant…), switch auto-ping.

#### 7. Cookie Consent & GDPR (`cookie`) — MIGRATO
- Banner MIGRATO.
- Card "Stato e modalità": switch banner attivo, segmented (Opt-in | Opt-out | Solo notifica), switch blocca script, input re-richiedi mesi.
- Card "Categorie di cookie": 4 categorie (Strettamente necessari, Funzionali, Analytics, Marketing) con dot colorato, descrizione, contatore cookie, switch on/off (Necessari sempre on disabled).
- Card "Copy del banner": tab multilingua (segmented bandiere IT/EN), input titolo, textarea testo, 3 input per CTA (Accetta/Solo essenziali/Personalizza), segmented posizione (4 opzioni).

#### 8. Performance & Cache (`performance`) — MIGRATO
- Banner MIGRATO + pill "Score 96/100" + bottone "Svuota tutto" in head actions.
- Card "Stato cache": 4 stat box (Pagine cachate, Hit rate, Spazio, Risparmio banda) con cifra grande Instrument Serif 32px.
- Card "Cache delle pagine": switch + TTL select + switch utenti loggati + textarea esclusioni URL mono.
- Card "Ottimizzazione media": switch WebP, segmented lazy loading, slider qualità, switch srcset.
- Card "Minify & combine": 4 switch (HTML, CSS, JS, defer JS).

### Gruppo PRESTAZIONI & SERVIZI

#### 9. AI Assistant (`ai`) — NEW (con badge)
- Pill "Provider connesso" in head.
- Card "Provider": segmented (OpenAI | Anthropic | Mistral | Self-hosted), input API key con eye reveal, select modello, input budget mensile.
- Card "Comportamento": select lingua, slider temperatura, segmented tono di voce, textarea istruzioni di sistema con counter.
- Card "Utilizzo questo mese": 4 stat box.

#### 10. Stock media (`stockmedia`) — ex "API & Integrazioni"
- Bottone "Come ottenere le chiavi" in head.
- Card "Provider connessi": 4 righe (Unsplash, Pexels, Pixabay, Icons8) — grid `48 1fr 280 110 36`. Avatar lettera + nome/desc + input chiave mono + pill stato + chevron.
- Card "Comportamento default": select provider preferito, switch scarica in locale, switch ottimizza al download.

### Gruppo TEAM & BRAND

#### 11. White Label (`whitelabel`) — MIGRATO
- Pill "Licenza Agency" in head.
- Card "Identità del plugin": input nome plugin, input nome agenzia, due upload logo (chiaro/scuro) con preview 48×48 + bottoni, input URL sito agenzia.
- Card "Visibilità": switch nascondi Powered by, switch nascondi changelog, switch+input link documentazione custom.

#### 12. Permessi & Ruoli (`permessi`) — MIGRATO
- Bottone "Crea ruolo custom" in head.
- Card "Matrice permessi": tabella 9 permessi × 5 ruoli (Admin, Editor, Author, Contributor, Cliente). Header con conteggio utenti per ruolo + pill CUSTOM. Celle: check 22×22 red-soft se permesso ON, X grigio se OFF. Click per toggle.
- Card "Opzioni avanzate": switch lock template Header/Footer, switch lock stili globali, switch sandbox contributors.

## Interactions & Behavior

### Sidebar
- Click su una voce → cambia il tab content. State locale `activeId`.
- Le voci "Soon" hanno cursor: not-allowed e on click non fanno nulla.
- Le voci "MIGRATO" sono attivabili normalmente; il banner all'interno della pagina segnala la provenienza.

### Tab content
- Ogni cambio di tab fa scroll a top del content area.
- Le form (input, textarea, select, switch, segmented, slider) sono **controlled components**: ogni cambio aggiorna lo stato e attiva il flag "dirty" della save bar.

### Save bar
- Stato `dirty` mostra pill arancio "● Modifiche non salvate".
- "Annulla modifiche" → conferma + ripristina last-saved state.
- "Anteprima" → apre live preview con valori non salvati applicati (modal o tab nuova).
- "Salva impostazioni" → POST a backend, on success rimuove dirty + aggiorna "Ultimo salvataggio _ora_".

### Search globale (⌘ K)
Non implementata nel mockup, ma il pattern è: pop-up command palette che cerca cross-tab nei label dei campi e nelle stringhe di hint, mostra risultati con breadcrumb del tab di appartenenza.

## State Management

```ts
type CfgState = {
  activeTabId: string;          // 'presets' | 'colori' | ...
  values: Record<string, any>;  // raw form values, key = field id
  initialValues: typeof values; // for "Annulla modifiche"
  dirty: boolean;
  lastSavedAt: string;          // ISO timestamp
  licenseStatus: 'agency' | 'pro' | 'free';
  isSaving: boolean;
  saveError: string | null;
};
```

Persistence: REST endpoint WordPress (`/wp-json/olobuild/v1/settings`) GET per inizializzare, POST per salvare. Considerare debounce o save manuale (la save bar è esplicita — niente auto-save).

## Data fetching requirements

- **Load** settings on mount.
- **Stock media** tab: validare le API keys async on save.
- **Performance** tab: leggere stato cache reale (hit rate, spazio occupato) — endpoint dedicato.
- **AI Assistant** tab: leggere stats utilizzo da API del provider (richiede separazione concerns).
- **Permessi**: leggere ruoli WP esistenti + custom roles.

## Design Tokens

```css
/* PALETTE BRAND (override su tokens.css base) */
--c-red:        #e1474f;
--c-red-dark:   #c8323a;
--c-red-soft:   #fef2f3;
--c-red-soft-2: #fde2e4;
--c-navy:       #0f172a;
--c-navy-2:     #1e293b;
--c-navy-3:     #334155;
--c-cream:      #faf7f2;
--c-line:       #e6e8ee;
--c-line-soft:  #eef0f4;
--c-bg:         #f6f7fa;
--c-text:       #1e293b;
--c-text-mute:  #5e6a7a;
--c-text-faint: #94a3b8;

/* SEMANTIC */
--c-success:      #15803d;
--c-success-soft: #dcfce7;
--c-warning:      #b45309;
--c-warning-soft: #fef3c7;

/* TYPOGRAPHY */
--c-sans:    "Work Sans", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
--c-display: "Instrument Serif", "Playfair Display", Georgia, serif;
--c-mono:    "JetBrains Mono", ui-monospace, SFMono-Regular, monospace;

/* Type scale: 11/12/13/13.5/14/14.5/15/18/22/28/32/48/54 px
   Page H1 ha solo SANS 28/700 (non display) in Variant Console.
   I numeri grandi nelle stat box usano Instrument Serif 32. */

/* SPACING (8pt grid) */
--space-1: 4px; --space-2: 8px; --space-3: 12px; --space-4: 16px;
--space-5: 20px; --space-6: 24px; --space-8: 32px; --space-10: 40px;

/* RADII */
xs:4 sm:6 md:8 lg:10 xl:12 2xl:14 3xl:18 full:9999

/* SHADOWS */
xs:  0 1px 2px rgba(15,23,42,.05)
sm:  0 1px 2px rgba(0,0,0,.04)
focus: 0 0 0 3px var(--c-red-soft)

/* TRANSITIONS */
all: .12s ease  (hover/focus changes)
```

## Assets

- **Logo OLObuild** — il prototipo usa un placeholder SVG (cerchio rosso). Sostituire con `/assets/olobuild-square.svg` esistente nel codebase.
- **Icone** — set Lucide React inline (~26 icone). In produzione: usare `lucide-react` o equivalente già nel codebase. Lista icone usate: `Search, ChevR, ChevD, Palette, Layers, Type, Devices, Sparkles, Plug, Image, Mail, Bar, Key, Refresh, Gauge, Download, Code, Bug, Wrench, Save, Book, Eye, EyeOff, Plus, Check, X, Undo, Drop, Logo`.

## Files

```
prototype/
├── index.html              # Entry point — apri in browser per vedere il design
├── tokens.css              # Design system base (font, palette neutri, semantic)
├── brand.css               # Override brand OLObuild (red primary)
├── settings.css            # Stili specifici della pagina Configurazione
├── settings-shell.jsx      # Topbar, Sidebar, Savebar + IA_GROUPS
└── settings-tabs.jsx       # I 13 componenti tab (12 attivi + 1 stub futuro)
```

**Come testare il prototipo:**
1. Apri `prototype/index.html` in un browser moderno (no build necessario).
2. Clicca le voci della sidebar per navigare tra i 12 tab.
3. Tutti i controlli (input, switch, segmented, slider) sono presentazionali — non persistono valori.

**Per implementare nel codebase target** servono i 3 file principali (`settings.css` + `settings-shell.jsx` + `settings-tabs.jsx`) da riadattare al framework in uso. `tokens.css` e `brand.css` sono il design system base, da fondere con quello del codebase.

## Note implementative

1. **WordPress admin chrome** — il prototipo NON include la sidebar di WP admin (è la chrome che WordPress disegna automaticamente intorno alla pagina `?page=…`). Il redesign occupa l'area `#wpbody`. Assicurarsi che la topbar custom non vada in conflitto con la admin bar di WP (offset top 32px in desktop, 46px in mobile).

2. **Migrazioni** — i 5 tab MIGRATO (SEO, Cookie, Performance, White Label, Permessi) richiedono il **decommissionamento** delle relative pagine standalone nel dashboard genitore. Pianificare:
   - Redirect 301 dalle URL vecchie (`?page=olo-performance`, `?page=olo-cookie`, ecc.) alla nuova `?page=olobuilder-settings&tab=<id>`.
   - Notifica visiva per gli utenti che cliccano sulle card del dashboard parente nel periodo di transizione.
   - Mantenere retrocompatibilità delle option keys in WP options table (i campi salvati cambiano "pagina" ma non chiavi).

3. **Campi inferiti** — i campi dei 5 tab migrati sono basati su pattern comuni del dominio (SEO, GDPR, cache, ecc.) e sulle descrizioni delle card del dashboard genitore. Confrontare con i campi delle versioni esistenti e allineare prima del rilascio.

4. **Search ⌘K** — non implementata. Pattern suggerito: command palette (libreria `cmdk` o equivalente) che indicizza tutti i campi al mount, mostra risultati con breadcrumb del tab di appartenenza, click → naviga al tab + scroll-to + highlight del campo.

5. **License gating** — alcuni tab/feature richiedono licenza Agency (es. White Label). Mostrare un overlay "Upgrade to Agency" anziché disattivare l'item della sidebar — UX più chiara.

6. **Responsive** — il prototipo è ottimizzato per desktop ≥1280. Sotto, considerare:
   - Sidebar collassabile in drawer (hamburger in topbar).
   - Form rows da grid `220 1fr` a stack verticale `label sopra / control sotto`.
   - Save bar resta sticky bottom su tutti i breakpoint.

7. **Accessibilità** — tutti i form controls necessitano `<label>` con `for` corretto. Lo switch è custom div nel mockup ma in produzione usare `<input type="checkbox" role="switch">`. Focus trap nei modal (Anteprima preset, Crea ruolo custom).
