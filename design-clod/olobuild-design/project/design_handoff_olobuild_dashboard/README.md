# Handoff: OLObuild — Homepage / Dashboard

## Overview
Redesign della **homepage interna** del plugin OLObuild dentro WordPress (la pagina che si apre cliccando "Olobuild" nel menu admin). Sostituisce la griglia piatta di tile con un **cockpit moderno**: hero contestuale, KPI, attività recenti, azioni rapide, gestione raggruppata, pannello risorse collassabile a destra. Usa una **"app mode"** che riduce la sidebar WP a 52px icon-only, dando la sensazione di essere dentro un'applicazione e recuperando spazio orizzontale.

## About the Design Files
I file in `prototype/` sono **prototipi React/HTML** di riferimento — look & behavior finiti, **non codice di produzione**. Vanno **ricreati nell'ambiente del plugin OLObuild**: tipicamente React (in linea con Gutenberg) caricato via `wp_enqueue_script` + REST API per i dati. Le voci di menu WP sono già renderizzate da PHP (`add_menu_page` / `add_submenu_page`) — l'unica cosa da cambiare lato PHP è registrare la dashboard come pagina top-level e iniettare il container React.

## Fidelity
**High-fidelity**. Colori, tipografia, spaziature, ombre, transizioni e stati hover sono finali. Tutti i numeri/contenuti sono mock — sostituire con dati reali via REST.

## Schermi / Viste

### Layout generale (3 zone verticali a tutta altezza)
1. **Sidebar WP** (52px in app mode, 220px classic) — sfondo `#1d2327`, voci icon-only con tooltip hover
2. **Strip "Torna a WordPress"** (solo in app mode) — barra dark 32px con link blu `#72aee6` + chip `App mode` verde brand
3. **Top bar OLObuild** (52px) — logo + chip versione + breadcrumb + ricerca globale + icone notifiche/help/profilo
4. **Main area** — split `1fr 360px` (rail 56px in stato collapsed)

### A. Banner notifiche
Compare solo se rilevante (aggiornamento disponibile, licenza in scadenza, ecc.). Bg `linear-gradient(90deg,#fffbeb,#fef3c7)`, border `#fde68a`, radius 10. Icona warn `#d97706` + testo `#92400e` + bottone CTA arancione `#d97706` + close.

### B. Hero contestuale (`.hero`)
**Scopo**: portare l'utente direttamente al lavoro in corso.
- Grid `1.4fr 1fr`, bg bianco, border-radius 14, min-height 280, **flex-shrink: 0** (importante).
- **Sinistra**: sfondo `linear-gradient(135deg,#f0f7ec 0%,#fff 60%)` con radial accent verde. Eyebrow "CIAO MARCO, BUON LAVORO" 11px uppercase verde-scuro. H1 26px/700 con il nome della pagina in arancione brand. Sub 14px muted. Meta-row con dominio + n° pagine + versione. CTA group: pri `#4a8c2a` "Apri editor" + sec bianco "Vedi sito live" + sec "Nuova pagina".
- **Destra**: sfondo `#1d2327`, mini-browser mockup con barra dot mac + url + body con preview live del sito (gradient verde + nav + h2 + cta). Pill "live" verde pulsante in alto a destra.

### C. KPI strip (`.kpi-strip`)
Grid 4 colonne, gap 10. Card padding 14/16, radius 12. Icona tonda 22px in box `bg-muted`, valore 26px/700, delta 11px. Toni: `up` = verde, `warn` = ambra. Hover translateY(-1px).

KPI mostrati: Pagine pubblicate · Template attivi · Invii form (7gg) · Avvisi.

### D. Continua dove avevi lasciato (`.recent-strip`)
Grid `repeat(auto-fill, minmax(180px, 1fr))`, gap 10. Card con thumb 84px (gradient o screenshot), pill stato `live`/`bozza` in alto a sinistra, body con titolo 12/600 + meta `tipo · tempo`. Hover bordo verde + lift -2px.

### E. Azioni rapide (`.quick-row`)
Grid 4 colonne. Card 16px padding, icon-box gradient 38px (4 toni: primary verde, info blu, purple, neutral slate), titolo + hint, freccia che appare al hover.

### F. Gestione (`.manage-grid`)
Grid `repeat(auto-fill, minmax(220px, 1fr))`. Tile orizzontale: icon-box 36px colorato + label + hint + pin button (visibile al hover, attivo = verde brand). Pin sposta i preferiti in cima (sort stabile). Badge numerico opzionale rosso in alto a destra (es. "128" su Invii Form).

12 voci: Gestione Template · Configurazione · Ricerca Media · Invii Form · Analytics · Cookie Consent · SEO · Redirect & 404 · Performance · Strumenti · WooCommerce · Popup Globali.

### G. Sistema (`.system-row`)
Chip 99px-rounded, bg bianco, border light, font 12. Voci raramente usate: White Label · Import/Export · Permessi · Submissions · Log · Licenza.

### H. Centro risorse (rail destra, `.home-rail`)
Width 360px (default), 56px collapsed. Toggle in alto.

**Espansa** — 3 sezioni:
- **Cosa c'è di nuovo** — changelog timeline con dot brand, 3 release più recenti, tag colorati `novità` (verde) / `fix` (blu), bullet list 11px.
- **Impara OLObuild** — 4 card video orizzontali 64px thumb gradient + emoji + titolo + durata.
- **Aiuto & supporto** — grid 2x2 di link: Documentazione · Apri ticket · Community · Roadmap.

**Collassata** — solo 4 icone con dot-new: rocket (changelog) · play (tutorial) · question (docs) · bell (notifiche).

## Interactions & Behavior
- **Pin tile gestione**: stato locale, persistere in `wp_options` o user-meta. Sort stabile.
- **Toggle rail**: stato `collapsed: bool`, transition `grid-template-columns .25s cubic-bezier(.4,0,.2,1)`. Salvare in localStorage o user-meta.
- **App mode toggle**: prop `appMode` di `WPShell`. In app mode: sidebar 52px, sotto-menu Olobuild nascosto (la dashboard è l'hub), strip "Torna a WordPress" sopra topbar. Per tornare alla classica si naviga a un'altra pagina admin WP — il sotto-menu nativo riapparirà perché generato da PHP.
- **Banner update**: dismiss locale, ri-mostra a ogni nuova versione.
- **Search globale ⌘K**: target ideale è una palette che cerca pagine + template + impostazioni + ultime modifiche.
- **Hover tooltip sidebar collassata**: tooltip dark 6px radius posizionato a `left: calc(100% + 6px)`, opacity 0 → 1 in 120ms.

## State necessario
- `appMode: bool` (default true, persist user-meta)
- `railCollapsed: bool` (persist localStorage)
- `pinnedTiles: string[]` (persist user-meta)
- `dismissedBanners: { [version]: true }`
- `bannerVersion: string` (server-side latest)
- Dati live (REST endpoints da implementare):
  - `GET /olobuild/v1/dashboard/kpis` → `{ pages, templates, formSubmissions7d, alerts }`
  - `GET /olobuild/v1/dashboard/recent?limit=6` → `[{ id, title, type, modifiedAt, thumbnail, status }]`
  - `GET /olobuild/v1/dashboard/changelog?limit=3`
  - `GET /olobuild/v1/dashboard/notifications`

## Design Tokens
Tutti in `prototype/tokens.css` (estratti dal design system Olo Tutor).

**Brand**: primary verde `#4a8c2a`, primary-bright `#3fa23f`, primary-dark `#2d722d`, primary-50 `#f0f7ec`, primary-100 `#dcefd2`, primary-200 `#b9dfaa`.

**WP admin**: bg `#1d2327`, border `#2c3338`, link `#72aee6`, text muted `#c3c4c7`, text light `#8c8f94`, active blue `#2271b1`.

**Toni manage tile** (gradient interni 36px box): orange `#f97316` · slate `#1f2937` · purple `#a855f7` · emerald `#10b981` · blue `#3b82f6` · amber `#f59e0b` · cyan `#06b6d4` · red `#ef4444` · yellow `#eab308` · slate-500 `#64748b` · violet `#7e22ce` · sky `#0ea5e9`.

**KPI**: success `#22c55e` su soft `#dcfce7` · warning `#f59e0b` su soft `#fef3c7`.

**Type**: Work Sans 400/500/600/700. Scala 9-10-11-12-13-14-26 (hero h1).

**Spacing**: 4/6/8/10/12/14/16/20/24px.

**Radius**: 4 (interni) · 6/7 (chip/control) · 8 (card piccola) · 10 (recent/banner) · 12 (kpi/quick) · 14 (hero) · 99 (pill).

**Shadows**: `--ot-shadow-xs` per card piatte, `--ot-shadow-sm` per hover.

**Transition**: `all .15s` (hover), `.25s cubic-bezier(.4,0,.2,1)` (collapse rail).

## Assets
- `assets/olobuild-horizontal.png` — logo orizzontale (rosso `#e1474f` "olo" + grigio `#555` "build") usato in topbar
- `assets/olobuild-square.png` — logo quadrato per icona menu WP (filtrato `brightness(0) invert(1)` per renderlo bianco su sfondo dark)
- **Icone**: Lucide-style stroke 1.7, 24×24 viewBox. `prototype/home/icons.jsx` definisce ~30 icone aggiuntive (`fileText`, `edit`, `upload`, `chart`, `cookie`, `redirect`, `wrench`, `tag`, `users`, `inbox`, `key`, `bell`, `rocket`, `trendUp`, `trendFlat`, `warn`, `pin2`, `external`, `globe`, `arrow`, `panelRight`, `collapse`, `dot3`, `question`, `play`, `user`, `pinFill`). Nel codebase target sostituire con `lucide-react` (raccomandato).

## Files

```
prototype/
├── OLObuild - Dashboard redesign.html  # entry point
├── tokens.css                            # design tokens (Olo Tutor)
├── icons.jsx                             # icon set base (editor)
└── home/
    ├── styles.css                        # tutto il CSS della dashboard + WP shell
    ├── data.js                           # mock data (KPI, recent, manage, changelog, learn)
    ├── icons.jsx                         # icone aggiuntive HomeIcon
    ├── wp-shell.jsx                      # WPShell (sidebar WP + app-mode)
    └── dashboard.jsx                     # tutti i componenti della dashboard
```

## Note implementative
- L'**app mode** dovrebbe essere il default per gli utenti che entrano da Olobuild → registrare la pagina admin con un body class `olobuild-app-mode` e nascondere `#adminmenuwrap` dei sotto-elementi via CSS quando attivo. Prevedere un'opzione utente "Modalità classica WordPress" per chi preferisce.
- I **dati KPI** devono essere cacheati lato server (transient 5-15 min) per evitare query pesanti a ogni caricamento.
- La **search ⌘K** è il punto in cui ha più senso investire: deve trovare pagine, template, voci di impostazioni e azioni — pensare un'architettura indicizzata, non una grep al volo.
- Il **pannello laterale collassabile** è critico per monitor 13"/14": il default per width <= 1366px dovrebbe essere `collapsed: true`.
- L'**hero** richiede `flex-shrink: 0` esplicito perché vive in un flex column container — senza di esso collassa a height 0 in alcuni browser.
