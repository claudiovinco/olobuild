# Olobuild

## Cos'è
Page builder WordPress professionale olonico con sistema a griglia (tile drag & drop).

## Stack
- **Backend**: PHP 7.4+ (plugin WordPress), REST API namespace `olo/v1`
- **Frontend**: Vue.js 3, Pinia, vuedraggable, Vite 5, Tailwind CSS (prefix `mb-`), SASS
- **DB**: 2 tabelle WordPress (`olo_templates`, `olo_revisions`)

## Naming
- PHP classes: `Olo_*` (es. `Olo_Builder`, `Olo_Database`)
- Constants: `OLOBUILD_*` (es. `OLOBUILD_VERSION`, `OLOBUILD_PATH`, `OLOBUILD_URL`)
- CSS frontend: classi `olo-*` (es. `.olo-template`, `.olo-section`)
- WP options: `olo_*` (es. `olo_active_header`, `olo_styles`)
- JS global: `oloData`
- Text domain: `olobuilder`
- Shortcode: `[olo_template]` (+ backward compat `[mosaic_template]`)

## Struttura
```
olobuild.php                → Entry point plugin WP (filename = slug WP)
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
- Version bump obbligatorio dopo modifiche JS/CSS: `OLOBUILD_VERSION` in olobuild.php

## 🧭 Uniformità dei controlli (inspector)
Un tipo di controllo per famiglia, in TUTTE le tile e anche dentro i repeater:
padding/margine → `type:'spacing'` (4 lati) · raggio → `type:'border-radius'` (4 angoli)
· bordo → `type:'border'` (4 lati + stile + colore + hover) · tipografia → `type:'typography'`
(key-mapped, chiavi invariate) · font → `type:'font-family'` · icone → `type:'icon'`.

- **Controllo di regressione**: `node scripts/audit-ui-standard.mjs` (con `--list` il dettaglio).
  Le soglie stanno in `scripts/ui-standard-baseline.json` e sono a **0**: non vanno alzate. Fanno
  eccezione le `fantasma-*`, nate col debito censito (23 set 2026): possono solo scendere.
- **Ponte legacy** (`src/config/fieldLegacyBridge.js`): un controllo composito montato su una
  tile che salva ancora chiavi piatte si INIZIALIZZA da quelle (`legacyKeys`) e le tiene in
  SINCRONIA a ogni modifica → nessuna migrazione dati, i renderer non ancora aggiornati
  continuano a rendere corretto. Per i bordi salvati come sola stringa colore basta
  `type:'border'` + `legacyWidth: 1`.
- **Renderer**: PHP `Olobuild_Tile_Utils::spacing_sides()/sides_css()/border_css()/border_color()/css_len()`
  — gemelli JS in `src/composables/useBoxModel.js` (`toSpacingSides`, `sidesCss`, `toBorderStyle`,
  `borderColorOf`, `cssLen`). Accettano SEMPRE sia il formato nuovo sia quello storico.
- **Repeater**: `ContentItemsEditor` delega a `InspectorField` ogni tipo che non rende
  nativamente → un controllo nuovo nasce disponibile in entrambi i posti.
- **Condizioni**: un solo valutatore, `src/utils/fieldCondition.js` (`op`/`operator`, alias
  eq/neq/ne/in/not-in/empty/notEmpty/gt/lt…, compositi ridotti alla somma dei lati).
- **Ombra** → `type:'box-shadow'` (FieldBoxShadow: X, Y, sfocatura, estensione, colore, interna,
  con anteprima) · ombra del testo → `type:'text-shadow'`. Il gruppo condiviso `shadowField`
  di `_shared.js` (importato da 109 tile) monta il preset + QUESTO controllo: le sei chiavi
  storiche `shadow_h/v/blur/spread/color/inset` restano scritte dal ponte legacy.
- **Il nome delle cose** (glossario): la stessa famiglia si chiama sempre allo stesso modo —
  **Raggio · Padding · Gap · Ombra · Durata** — seguita dal qualificatore (`Raggio card`).
  Vince la forma già più diffusa, non una preferenza: prima c'erano 103 nomi per il raggio.
- **L'unità sta nel CONTROLLO, non nell'etichetta**: `range` e `number` la mostrano accanto al
  numero (`src/utils/fieldLabel.js` la stacca dalla coda dell'etichetta e la passa al campo).
  Per `spacing`, che nei 4 riquadri non la mostra, l'etichetta la tiene: non toglierla.
- **Un controllo che non fa niente è peggio di un controllo che manca.** Se una tile offre una
  proprietà, un renderer deve leggerla: 54 tile mostravano «Ombra» senza disegnarla
  (`Olobuild_Frontend_Renderer::tile_ombra_non_resa()` + gemello `OMBRA_SUL_WRAPPER` in
  `GridCell.vue`; l'audit fallisce se i due elenchi divergono dal codice).
  ⚠️ Ombra su wrapper CON sfondo → `box-shadow`; wrapper trasparente → `filter: drop-shadow`,
  che segue la sagoma visibile. Un box-shadow lì disegnerebbe un rettangolo attorno al vuoto.
- ⚠️ Gli helper (`spacing_css`, `sides_css`, `border_radius`) **restituiscono già l'unità**:
  aggiungere `px` nel template produce `16pxpx` e la dichiarazione viene scartata in silenzio.
- **Raggio in hover** (toggle Normale/Hover del campo Raggio, `withHover`): PHP
  `Olobuild_Tile_Utils::radius_hover()` / `radius_hover_rules()` leggono `{key}_hover` e
  `{key}_hover_duration` (default 300 ms, come l'inspector). La transizione si **aggiunge** a
  quella dell'elemento: in CSS vince UNA sola `transition`, e se sullo stesso elemento c'è il
  Bordo in hover la regola del raggio va emessa DOPO riprendendone la transizione
  (`transizione_di( $border_hover_css )`). Immagini e badge dentro una card cambiano al
  passaggio sulla card (non ricevono `:hover` da sé). «Azzera» sul Raggio salva '' = nessun
  hover: un oggetto a zeri per il PHP è «angoli vivi in hover».
- ⚠️ **Anteprima del builder** (`useIframeBridge.js`): una modifica arrivata mentre una
  richiesta di render è in volo NON va scartata (si ricorda e si rifà dopo, `dopoRichiesta()`);
  il render completo registra come reso ciò che ha INVIATO. Scartarla lasciava il canvas a
  metà parola e sembrava un «limite di caratteri» (23 set 2026).

### Zone di controllo del tab Stile (standard, pilota: badge 1.4.467)
- **Due blocchi, con intestazione**: **Elemento** (ciò che la tile disegna, `settings`) e
  **Contenitore** (il riquadro della tile nella griglia, `style`). Raggio, Bordo, Ombra esistono in
  entrambi: senza l'intestazione non si capiva su cosa agissero.
- **Zone dell'elemento**, sempre in quest'ordine e con questi nomi, solo quelle che servono:
  **Aspetto** (variante/preset, colori, ombra) · **Testo** (stile tipografico + un controllo
  tipografia per testo) · **Forma** (raggio, padding, dimensioni) · **Disposizione** (allineamento,
  colonne, gap). Tile a più parti (section header, card): una zona per parte (Occhiello, Titolo…),
  poi Disposizione.
- **Tile atomiche** (badge, pulsante, icona, divisore, spaziatore, interruttore): contenitore sempre
  trasparente → nell'inspector Layout, Spazi (margine/padding), **Sfondo** ed Effetti senza ombra né
  filtro sfondo; il PHP scarta il resto in ogni stato e dispositivo (`stile_contenitore_atomico()`).
  Elenco unico: `ATOMIC_TILE_TYPES` (useBackgroundStyle.js) = `$ATOMIC_TILES` (audit `atomiche-elenco`).
- ⚠️⚠️ **Contenuto o Stile: ogni proprietà ha UNA casa** (utente, 23 set 2026: «il corsivo a volte
  nel contenuto e a volte nello stile… UNIFORMA una volta per tutte»). **Contenuto** = cosa la
  tile dice e mostra: testi, media principali (foto, video, copertina, «Sfondo / media»), link,
  voci dei ripetitori, dati, comportamento (autoplay, trigger, «Comportamento…»), visibilità
  («Mostra…»). **Stile** = come appare: colori, tipografia e corsivo/maiuscolo, sfondi veri
  («Sfondo card», «Sfondo voce»), bordi, raggi, ombre, opacità, spazi, dimensioni, proporzioni,
  colonne, allineamento, disposizione, varianti e preset visivi. Le **voci dei ripetitori**
  tengono nel Contenuto solo ciò che dicono; il loro stile sta nello Stile in uno **specchio**
  `{ key: '<stesso ripetitore>', type: 'content-items', etichettaDa, miniaturaDa, itemFields: [solo
  stile] }` (StyleFieldsRenderer → ContentItemsEditor `strutturaFissa`: stesse voci, niente
  aggiungi/elimina/riordina). Audit `stile-nel-contenuto` = 0 (eccezioni motivate in ESCLUSI_C).
  **In quale zona dello Stile**: 1) quella della STESSA PARTE, se il suo nome è contenuto nel nome
  della sezione di provenienza («Stile pulsante» ⊂ «Pulsante invio», «Legenda — stile» ⊂ «Legenda»)
  — mai una seconda zona per la stessa parte; 2) allineamento/posizione/colonne/gap/layout → la
  zona di disposizione che la tile ha già, se no «Disposizione» in fondo; 3) una sezione che
  nomina una parte senza zona → zona con quel nome; 4) il resto per famiglia (Aspetto · Testo ·
  Forma), riusando la zona della famiglia che la tile ha già.
- **Condizione di sezione**: la `condition`/`show` di un separatore vale per TUTTA la sezione, nel
  Contenuto, nello Stile e nelle voci dei ripetitori (`isSectionVisible()` in `fieldCondition.js`,
  dal 1.4.479: prima nessun tab la leggeva). ⚠️ Un campo che vale anche in altri casi NON va sotto un
  separatore condizionato, sparirebbe con la sezione: lo Zoom del servizio dinamico della mappa stava
  sotto «Marker» (solo indirizzo singolo), il Punto focale di scrollscrub sotto «Sovraimpressione»
  (solo padding 0). Spostando campi fuori da una sezione condizionata la condizione va sul campo
  (array = AND). Una `show()` che va in errore mostra il campo invece di bloccare l'inspector.
- **Bordo in hover**: lo stato Hover non ancora impostato parte dal bordo normale (InspectorField),
  altrimenti scegliere il solo colore salvava lati a 0 e in hover il bordo spariva. Lati a 0 espliciti
  = bordo tolto in hover (FieldBorder non ha lo stile «nessuno»). Le chiavi hover storiche salvate
  come sola stringa colore (`submit_hover_border_color`, `card_border_hover_color`) accettano anche
  il bordo intero: PHP `build_border_hover_props()` / `Olobuild_Tile_Utils::border_color()`.
- ⚠️⚠️ **Lo Sfondo è PARTE COMUNE di tutte le tile: MAI toglierlo, MAI ridurne i tipi.** Stesso
  componente (BackgroundControls, tutti i tipi: tinta, gradiente, generativi, immagine, video,
  galleria, sovrapposizione), stesso posto nel tab e nella barra. Se il contenitore non può
  disegnarlo, lo disegna l'elemento: `sfondo_elemento()` in Olobuild_Tile_Base (livelli sotto il
  contenuto). Errore del 23 set 2026: tolto alle atomiche perché «non agiva» — andava fatto agire.
- **Niente controlli fantasma** (audit `fantasma-*`): menu «Stile» senza preset registrati, toggle
  Hover su chiavi mai lette, selettore del dispositivo su valori mai letti (in PHP:
  `css_per_dispositivo()` di Olobuild_Tile_Base), «Bordo» ed «Effetti testo» condivisi mai resi. Per
  ognuno si sceglie: farlo funzionare (PHP + gemello Vue) o toglierlo. Un campo che dipende da un
  altro si nasconde quando non agisce (`condition`: «Posizione icona» senza icona).
- **Il nome dice cosa fa**: «Colore sfondo» sul badge era il colore da cui la variante ricava la
  pillola (Soft = 12% di sfondo, 22% di bordo), non lo sfondo — e ha tratto in inganno chi l'ha usato.
- ⚠️ La `description` di un campo **in linea** (select, colore, testo, interruttore, numero…:
  `INLINE_COMPACT`/`INLINE_FILL` in InspectorField) **non viene mostrata**: la spiegazione va nella
  label o nelle voci della select. ~206 descrizioni sono oggi invisibili (lotto da decidere).

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
- Dopo le modifiche: build (`node node_modules/vite/bin/vite.js build`) + bump `OLOBUILD_VERSION`.

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
