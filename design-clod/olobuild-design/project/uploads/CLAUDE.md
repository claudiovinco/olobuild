# Olobuild

## Cos'è
Page builder WordPress professionale olonico con sistema a griglia (tile drag & drop).

## Stack
- **Backend**: PHP 7.4+ (plugin WordPress), REST API namespace `olo/v1`
- **Frontend**: Vue.js 3, Pinia, vuedraggable, Vite 5, Tailwind CSS (prefix `mb-`), SASS
- **DB**: 2 tabelle WordPress (`olo_templates`, `olo_revisions`)

## Naming
- PHP classes: `Olo_*` (es. `Olo_Builder`, `Olo_Database`)
- Constants: `OLO_*` (es. `OLO_VERSION`, `OLO_PATH`)
- CSS frontend: classi `olo-*` (es. `.olo-template`, `.olo-section`)
- WP options: `olo_*` (es. `olo_active_header`, `olo_styles`)
- JS global: `oloData`
- Text domain: `olobuilder`
- Shortcode: `[olo_template]` (+ backward compat `[mosaic_template]`)

## Struttura
```
mosaic-builder.php          → Entry point plugin WP (filename = slug WP)
includes/                   → Classi PHP (olo-builder, database, rest-api, tile-manager, etc.)
includes/tiles/             → Tile classes (base + 50 elementi)
src/                        → Vue.js source
src/stores/                 → Pinia stores (builder, tiles)
src/config/elements/        → Definizioni JSON inspector per ogni elemento
src/config/elementRegistry.js → Auto-discovery elementi via import.meta.glob
src/components/Builder/     → Toolbar, Sidebar, Canvas, Inspector, StructureTree
src/components/Grid/        → OlobuilderGrid, GridCell
src/components/Tiles/       → Componenti Vue per ogni tile
src/composables/            → useDragDrop, useHistory, etc.
src/assets/styles/          → main.scss
assets/                     → Build output (js + css) + vendor (UIkit)
templates/                  → builder-page.php
database/                   → schema.sql
```

## Note build
- `npx` non funziona su questo sistema (node non nel PATH di cmd.exe)
- Usare: `node node_modules/vite/bin/vite.js build`
- Version bump obbligatorio dopo modifiche JS/CSS: `OLO_VERSION` in mosaic-builder.php

## Regole
- Tailwind prefix: `mb-` (evita conflitti con WordPress)
- Colori primary via CSS custom property `var(--olo-color-primary)` — mai hardcodare hex blu
- Mai toccare siti WordPress in produzione

---

## 📚 Knowledge base OLOtheme — Vault Obsidian

Questo plugin fa parte dell'ecosistema **OLOtheme**. Standard tecnici cross-prodotto, decisioni architetturali (ADR), playbook operativi e info sui prodotti fratelli (OLObooking, OLOlang, OLOtour, OLOtutor, OLOcalendar) sono documentati nel vault Obsidian:

- **Path**: `D:\TECNICA\OLOtheme-Vault\`
- **Entry point**: `00 - Home.md` (MOC dashboard navigabile)
- **Setup**: 2026-05-02

### Quando consultare il vault

| Situazione | Cosa leggere |
|---|---|
| Convenzioni naming PHP/CSS/JS/options | `03 - Standard OLOtheme/Naming conventions.md` |
| Build & deploy (Vite, version bump, prod mode, atomicità) | `03 - Standard OLOtheme/Build & deploy.md` |
| REST API conventions (`olo/v1`, nonce, capabilities) | `03 - Standard OLOtheme/REST API conventions.md` |
| i18n + integrazione OLOlang | `03 - Standard OLOtheme/i18n standard.md` |
| Vue 3 / Pinia / auto-discovery patterns | `03 - Standard OLOtheme/Vue Pinia patterns.md` |
| DB schema / tabelle custom | `03 - Standard OLOtheme/Database conventions.md` |
| **Regole comportamentali generali** | `03 - Standard OLOtheme/Regole operative core.md` |
| Decisioni architetturali (ADR-001..005) | `04 - Architettura/ADR/` |
| Playbook (release, sync prod↔repo, lint PHP, setup nuovo prodotto) | `05 - Playbook/` |
| Glossario di dominio (~70 termini) | `06 - Glossario/Glossario di dominio.md` |
| Info su altri prodotti OLOtheme | `02 - Prodotti/` |
| Identità brand (claim, palette, target) | `01 - Brand & Strategia/Identità OLOtheme.md` |

### ADR rilevanti per OLObuild

- `ADR-003 OloBuild tile system come base UI` — perché OLObuild è la base UI cross-prodotto OLOtheme
- `ADR-002 REST custom no GraphQL` — namespace `olo/v1`

### Playbook diretti

- `05 - Playbook/Aggiungere un tile OloBuild.md` — pattern 2-file + auto-discovery + standard di qualità
- `05 - Playbook/Pubblicare un release.md`
- `05 - Playbook/Lint PHP dopo sed.md`

### ⚠️ Lettura selettiva

NON leggere tutto il vault — apri solo i file specifici rilevanti alla task corrente. 58 file = ~80k token totali.

### Aggiornamento del vault

Quando completi uno sprint/refactor su questo plugin: chiedi *"aggiorna il vault con questo sprint"* — verrà creata una sessione in `07 - Sessioni & Log/` + aggiornati MOC e roadmap collegati.
