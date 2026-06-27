# Handoff: OLObuild — Gestione Template

## Overview
Redesign della pagina **Gestione Template** del plugin OLObuild dentro WordPress (`wp-admin/admin.php?page=olobuild-templates`). Sostituisce la lista a tile vuote (preview placeholder grigi tutti uguali) con una griglia moderna di card che **anteprime tipizzate inline**, filtri rapidi per tipo, ricerca, ordinamento, vista lista alternativa, badge stato/tipo, hover-actions e split-button "Nuovo Template" raggruppato per categoria.

Riusa lo **shell della Dashboard** (sidebar WP collassata in app mode + topbar OLObuild + breadcrumb) — handoff separato `design_handoff_olobuild_dashboard`.

## About the Design Files
Prototipo React/HTML in `prototype/` — riferimento di look & behavior. Reimplementare nel codebase del plugin (React + REST API o blade-style PHP), **non copiare 1:1**.

## Fidelity
**High-fidelity** su layout, tipografia, spaziature, stati hover, comportamento filtri/sort/view-toggle. Dati mock — sostituire con la query CPT esistente.

## Layout pagina

```
┌─ App-back strip (52px) ────────────────────────────────┐
├─ Topbar OLObuild (52px) ───────────────────────────────┤
├─ Page header ──────────────────────────────────────────┤
│  Titolo "Gestione Template" + meta counter            │
│  + [Importa] [Nuovo Template ▾]                        │
├─ Sub-nav tabs ─────────────────────────────────────────┤
│  Salvati (96) · Website (28) · Popups (4)             │
├─ Toolbar ──────────────────────────────────────────────┤
│  [Tutti 128] [Pagine 47] [Header 5] … chip filtri tipo│
│  + Ricerca + Sort + Toggle griglia/lista              │
├─ Body ─────────────────────────────────────────────────┤
│  Grid auto-fill min(260px) — card template            │
│  oppure tabella lista                                  │
└────────────────────────────────────────────────────────┘
```

## Componenti

### A. Page header (`.tpl-header`)
- H1 22/700 "Gestione Template"
- Counter inline: `128 totali · 3 attivi · 6 bozze` (12px muted, separatori `·`)
- Spacer
- **Bottone "Importa"** secondario (icona upload + label)
- **Split-button "Nuovo Template"** primario rosso brand:
  - Lato sinistro: `+ Nuovo Template` (azione default → apre modale "Nuova Pagina")
  - Lato destro: chevron → dropdown
  - Dropdown 280px wide, due gruppi:
    - **Standard**: Pagina · Header · Footer · Mega Panel · Widget · 404
    - **Template Single** (CPT-specific): Articoli · Prodotti · Locations · Strutture · Corsi · Eventi
  - Ogni voce ha icon-box 28px colorato (color = `TPL_TYPE_META[t].color`) + label + chevron destro

### B. Sub-nav tabs (`.tpl-subnav`)
- 3 tab: Salvati · Website · Popups
- Underline 2px brand su attivo
- Counter pill 9px a destra del label
- Stato URL: `?view=saved|website|popups`

### C. Toolbar (`.tpl-toolbar`)
Riga sticky sotto sub-nav:
- **Chip filtri tipo** orizzontali scrollabili: `Tutti 128`, `Pagine 47`, `Header 5`, `Footer 4`, `Single 38`, `Mega Panel 6`, `Widget 22`, `404 1`. Chip attivo = bg `bg-muted`, border darker, dot colorato del tipo.
- Spacer
- **Search input** (180px) "Cerca template…"
- **Sort dropdown**: Più recenti / Più vecchi / A-Z / Z-A / Più usati
- **View toggle** segmentato: griglia | lista

### D. Card template (`.tpl-card`)
Grid `repeat(auto-fill, minmax(260px, 1fr))`, gap 14.

```
┌─────────────────────────────┐
│ [PAGINA]      [Attivo]     │  ← badge tipo TL + status TR
│                             │
│   ANTEPRIMA SHAPE INLINE    │  ← .preview, height ~140
│   (grad fondo + barre/grid) │
│                             │
│ ─────────────────────────── │
│ Titolo template             │
│ #shortcode-179      [···]   │  ← shortcode pill click-to-copy
│ 12 elementi · 09 mag        │
└─────────────────────────────┘
```

- **Card outer**: bg `#fff`, border light, radius 10, overflow hidden, `.15s` transition. Hover: bordo brand-200 + lift -2px + shadow-sm + actions visibili.
- **Badge tipo** (top-left, absolute): pill 9px uppercase, bg colorato + fg bianco. Map color → token: primary=red, blue=#3b82f6, slate=#64748b, purple=#a855f7, amber=#f59e0b, violet=#7e22ce, red=#ef4444.
- **Badge status** (top-right): "Attivo" verde solid se `attivo:true`, "Bozza" ambra outline se `status:draft`. Niente badge se published normale.
- **Anteprima inline** (`.preview`): height ~140, rendering shape diverso per `preview` kind:
  - `header`: barra dark 30% top con nav-dots + bars sotto
  - `footer`: bars sopra + grid dark 35% bottom 4-col
  - `widget`: pill bianco centrato su gradient brand-100/50
  - `split`: 2 colonne (square colorato + bars di testo)
  - `hero+grid`: hero rettangolo grande sopra + grid 3 cards sotto
  - `grid`: grid 2x2 di rettangoli
  - `long`: bars verticali multiple (article)
  - `empty`: testo muted "0 elementi" centrato
- **Footer card** (`.tpl-foot`): titolo 13/600, riga `#shortcode-{id}` 11px mono cliccabile (copia in clipboard, micro-toast "copiato"), riga meta `{n} elementi · {date}`.
- **Hover actions** (top center, absolute, opacity 0→1): icon-buttons 28px su pill bianca con shadow — Modifica · Duplica · Elimina · Esporta.

### E. Vista lista (`.tpl-list`)
Tabella alternativa quando view=list:
- Header: Tipo · Titolo · Stato · Elementi · Modificato · Azioni
- Row 48px, mini-thumb 56×36 inline a sinistra del titolo
- Badge tipo come pill
- Stato come dot colorato + label
- Azioni icon-only allineate a destra

## Interactions

- **Filtro tipo**: click chip → filtra `TPL_LIST` per `type === chip.id`. Chip "Tutti" resetta. Stato URL `?type=...`.
- **Search**: filtro live full-text su `title + id`.
- **Sort**: applica comparator. Stato URL `?sort=...`.
- **View toggle**: stato locale + persist localStorage.
- **Card click**: → editor (`/wp-admin/post.php?action=edit&post=${id}`).
- **Shortcode click**: copy in clipboard, toast "Shortcode copiato".
- **Split-button main click**: azione default (= prima voce dropdown). Click chevron: apre dropdown. Click voce: → wizard "nuovo template" pre-popolato con type.
- **Card hover actions**:
  - Modifica → editor
  - Duplica → POST `/olobuild/v1/templates/{id}/duplicate` → refresh list + toast "Duplicato"
  - Elimina → confirm modal → DELETE → refresh + toast
  - Esporta → download JSON

## State

```js
{
  view:       'saved' | 'website' | 'popups',  // sub-nav
  type:       'all' | 'page' | 'header' | …,    // chip
  search:     string,
  sort:       'newest' | 'oldest' | 'az' | 'za' | 'used',
  layout:     'grid' | 'list',                  // persist localStorage
  newDropdownOpen: boolean,
}
```

## Design tokens
Riusa `tokens.css` + `brand.css` (override rosso `#e1474f`). Specifici della pagina:
- **Tipi color map** (`TPL_TYPE_META`): primary→`#e1474f`, blue→`#3b82f6`, slate→`#64748b`, purple→`#a855f7`, amber→`#f59e0b`, violet→`#7e22ce`, red→`#ef4444`.
- **Card preview gradient bg**: gradient soft del color del tipo (es. `linear-gradient(135deg, #fde2e4, #fff 70%)`).
- **Status colors**: published (default, no badge) · draft → ambra · attivo → verde solid pill.

## REST endpoints da implementare

```
GET    /olobuild/v1/templates?view=saved&type=page&q=…&sort=newest&page=1
       → { items: [...], total, byType: {...} }
POST   /olobuild/v1/templates                     # create
GET    /olobuild/v1/templates/:id
PUT    /olobuild/v1/templates/:id
DELETE /olobuild/v1/templates/:id
POST   /olobuild/v1/templates/:id/duplicate
GET    /olobuild/v1/templates/:id/export
POST   /olobuild/v1/templates/import
```

`item` shape:
```ts
{
  id: number;
  title: string;
  type: 'page'|'header'|'footer'|'single'|'mega'|'widget'|'404';
  status: 'published'|'draft';
  attivo?: boolean;             // header/footer/single attivo per il sito
  singleType?: string;          // CPT slug per type=single
  elements: number;             // count widget interni
  date: string;                 // ISO
  thumbUrl?: string;            // se presente, sostituisce shape inline
  preview?: 'hero+grid'|'split'|'long'|'empty'|'header'|'footer'|'widget'|'grid';
}
```

Quando il backend ha screenshot reali (`thumbUrl`), la card sostituisce l'anteprima shape inline con `<img>`. Le shape inline sono il **fallback** quando lo screenshot non è ancora generato (rendering CPT in headless puppeteer è la soluzione corretta lato server).

## Files

```
prototype/
├── OLObuild - Gestione Template.html
├── tokens.css                         # design tokens base
├── brand.css                          # override brand → red
├── icons.jsx                          # icon set base editor
└── home/
│   ├── styles.css                     # WP shell + topbar
│   ├── icons.jsx                      # icone aggiuntive
│   ├── data.js                        # mock dashboard (per HomeTopBar)
│   ├── wp-shell.jsx                   # WPShell + AppBackStrip
│   └── dashboard.jsx                  # contiene HomeTopBar/AppBackStrip
└── templates/
    ├── styles.css                     # tutti gli stili specifici page
    ├── data.js                        # TPL_LIST + TPL_TYPES + TPL_NEW_OPTIONS
    └── templates.jsx                  # TplPage + TplCard + TplPreview
```

## Note implementative

- **Preview shape inline** (componente `TplPreview`) è una stop-gap visuale — se il backend genera screenshot, conviene rimuoverla e usare solo `<img src={thumbUrl}>`. Nel frattempo è ottima perché distingue **a colpo d'occhio** un Header da un Footer da un Widget.
- **Counter tipo** mostrato sul chip va aggiornato lato server con la stessa query filtrata, non ricontato lato client (per liste >1k template diventa lento).
- **Split-button**: l'evento click sul lato sinistro deve **sempre** triggerare l'azione default; il chevron apre il dropdown — separare gli `onClick` per evitare che il click sul chevron triggeri anche l'azione default (`event.stopPropagation()`).
- **Vista lista** è la stessa vista del CPT WP nativo arricchita — può essere implementata come `WP_List_Table` custom server-side o mantenuta client-side (se REST già pagina).
- I **type "Single"** in OLObuild sono template legati a CPT. Nel dropdown "Nuovo" lo split per CPT (Articoli, Prodotti, Locations, ecc.) deve essere **dinamico**: leggere i CPT registrati e mostrarli tutti — non hardcoded.
- **Persist layout** (grid/list) per utente in `user-meta` (`olobuild_templates_layout`), così l'utente non lo ri-sceglie ad ogni accesso.
