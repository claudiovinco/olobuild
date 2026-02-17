# Olobuilder

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
