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
- **Due bundle**: oltre a `builder.js` esiste `assets/js/theme-picker.js` (selettore temi
  condiviso `src/theme-picker/`, usato dal modale del builder E dal setup wizard). Dopo
  modifiche al picker buildare ANCHE: `node node_modules/vite/bin/vite.js build --config vite.picker.config.js`
- Version bump obbligatorio dopo modifiche JS/CSS: `OLO_VERSION` in mosaic-builder.php

## Regole
- Tailwind prefix: `mb-` (evita conflitti con WordPress)
- **Colori solo via token** `var(--olo-color-*)` + `resolveColor()` — **mai hardcodare hex**.
  Attenzione: nel codice convivono 4 "primari" storici da eliminare (`#6366F1` indaco,
  `#1e87f0` blu, `#e8622a` arancio, `#e1474f`); il primario unico è il rosso brand
  `#e1474f` via `--olo-color-primary`. Vedi sezione *Tile — design coerente*.
- Mai toccare siti WordPress in produzione

## 🎨 Tile — design coerente (pacchetto `regoletiles1`)
Obiettivo permanente: le tile devono essere **belle e coerenti** come una sola famiglia.
Quando tocchi una qualsiasi tile — sia il render **Vue** (`src/components/Tiles/*Tile.vue`),
sia il render **PHP frontend** (`includes/tiles/`), sia il config inspector
(`src/config/elements/*.js`) — applica le regole del pacchetto:

- **Entry point / protocollo completo**: `D:\TECNICA\olobuild\regoletiles1\START_HERE.md`
- **Le 10 regole**: `…\regoletiles1\DESIGN_LANGUAGE.md`
- **Checklist per tile + categorie**: `…\regoletiles1\TILE_AUDIT_CHECKLIST.md`
- **Token e colori globali del cliente**: `…\regoletiles1\TOKEN_MAPPING.md`
- **Strumenti** (`…\regoletiles1\prototype\`): `oloTileDefaults.js` (token GLOBAL/SYSTEM,
  `resolveColor`, `contrastOn`, `SPACE`/`RADIUS`, `TILE_DEFAULTS`), `useBoxModel.js`,
  `tokens-brand.css`
- **Riferimenti visivi** del risultato atteso: `REFERENCE_card-category.html`,
  `REFERENCE_data-category.html`, `REFERENCE_interactive-category.html`

Regole sempre attive (sintesi):
- Colori solo via token (GLOBAL = ruoli cliente / SYSTEM = fissi) + `resolveColor()`; primario rosso brand.
- Box-model via `useBoxModel`; **default da fonte unica** (no default duplicati config↔componente).
- Icone dal set SVG, **mai emoji**; **focus-visible** su ogni elemento interattivo.
- Scale condivise `SPACE` (8pt) e `RADIUS`: una tile = un raggio, una lingua d'ombra.
- **Chiavi salvate INVARIATE** (margin_*, padding_*, border_radius, hover.*, ecc.): cambia
  la UI/resa, non il formato dei dati. I template esistenti devono continuare a funzionare.
- Non inventare nomi `--olo-color-*` che il `GlobalColorsPanel` non genera (vedi TOKEN_MAPPING).
- Coerenza render: lo stesso aspetto va garantito sia in Vue (canvas) sia in PHP (frontend).
- Dopo le modifiche: build (`node node_modules/vite/bin/vite.js build`) + bump `OLO_VERSION`.

> Anche creando una **nuova** tile (vedi playbook *Aggiungere un tile OloBuild*), applica
> da subito queste regole: nasce già bella e coerente, default token-first curati.

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
