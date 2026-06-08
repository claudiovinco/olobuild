# SPEC — Tile pronte & coerenti per comporre gli ultimi 50 template · OLObuild

> Documento auto-contenuto per Claude Code. Obiettivo: portare il set tile a una **coerenza
> da "una sola famiglia"** così che i **50 template** finali si compongano su una base solida.
> Repo: `claudiovinco/olobuild` · branch `main` · Vue 3 + Pinia + PHP · pacchetto regole: `regoletiles1/`.

---

## 0) Realtà di partenza (da audit reali nel repo, NON inventati)
- **Il set tile è COMPLETO.** `AUDIT-TILE-DUPLICATES.md`: **0 duplicati critici**, nessuna tile da
  creare. → "tile mancanti" = nessuna. Il lavoro è di **rifinitura/coerenza**, non di nuove tile.
- **91 tile FREE, tutte ≥ 80% compliant, 32 al 100%** (`AUDIT-TILE-COMPLIANCE.md`).
  → restano **~59 tile sotto il 100%** da portare a 100% (gap ricorrente **D2**; poi **D1**, **F3**).
- Due interventi **strutturali OPZIONALI** (no fusione obbligatoria):
  - `iconlist` assorbibile in `list` con flag `show_icons` (−1 tile, 0 feature perse, tenere alias).
  - `nav` → rinominare in `linklist`/`linknav` per non confonderlo con `navmenu` (menu WP).

## 1) Fase A — portare ogni tile a 100% coerenza (prerequisito ai template)
Per OGNI tile applica il protocollo del pacchetto (già in `regoletiles1/`, incluso in questo zip):
- **`START_HERE.md`** — protocollo operativo (enumerare tutte le tile, tracker, DoD, guardrail).
- **`DESIGN_LANGUAGE.md`** — le 10 regole + nota **CHROME vs CONTENUTO**.
- **`TILE_AUDIT_CHECKLIST.md`** — checklist 10 punti + 8 categorie (lavorare a batch per categoria).
- **`TOKEN_MAPPING.md`** — come nascono i `--olo-color-*` dal GlobalColorsPanel (non inventarne).
- **`prototype/`** — `oloTileDefaults.js` (token GLOBAL/SYSTEM, `resolveColor`, `contrastOn`,
  `SPACE`/`RADIUS`, `TILE_DEFAULTS`), `useBoxModel.js`, `tokens-brand.css`, e i controlli
  inspector ridisegnati `FieldBorder.vue` + **`FieldBoxShadow.vue`** (vedi `SPEC_fieldboxshadow.md`).

**Sorgenti di verità in-repo da incrociare:**
- `AUDIT-TILE-COMPLIANCE.md` → matrice per-tile (quali criteri ✗ su ciascuna).
- `TILE_PROGRESS.md` → tracker ufficiale dell'avanzamento (aggiornalo man mano).
- Legenda criteri A/B/D/F: vault `OLOtheme-Vault → Standard tile moderno`. **D2 è il gap più diffuso**
  (✗ anche su tile altrimenti perfette): chiarisci cosa misura dal vault e chiudilo in modo sistematico.

**Le ~59 tile sotto il 100% (worklist, da `AUDIT-TILE-COMPLIANCE.md`):**
readingtime, table, spacer, divider, navmenu, sitemap, postmeta, wpcomments, progress, darkmode,
marquee, audio, lightbox, togglebtn, pagination, search, relatedposts, countdown, osmmap,
switcherpanel, overlayslider, overlaygrid, postnavigation, popup, column, fragment, panel, content,
video, accordion, panelslider, popover, breadcrumbs, totop, pricing, pricelist, hero, templateembed,
timeline, grid, button, iconbox, counter, inner-columns, code, html, killnextprev, facebookpage,
quotation, sharebuttons, switcher, instagram, twitterfeed, scrollprogress, icon, starrating, list,
pagetitlebar, overlay.

> Ordine consigliato: prima le tile ad **alta presenza nei template** (hero, grid, button, iconbox,
> counter, content, panel, accordion, pricing, timeline, gallery/list), poi il resto. Lavora a
> **batch per categoria** (un PR per categoria), diff verificabile, **chiavi salvate INVARIATE**.

**Per ogni tile — Definition of Done (sintesi regoletiles1):**
- [ ] Colori solo via token (`resolveColor`, primario rosso brand) — zero hex hardcoded (eliminare
      i 4 primari storici `#6366F1`/`#1e87f0`/`#e8622a`/`#e1474f` nel CONTENUTO; l'arancio resta solo CHROME).
- [ ] Box-model via `useBoxModel`; spazi da `SPACE` (8pt); un solo `RADIUS`; una sola lingua d'ombra
      (usa il nuovo `FieldBoxShadow`/scala `shadow`).
- [ ] Default da **fonte unica** (`oloTileDefaults`/`buildDefaults`) — niente default duplicati config↔componente.
- [ ] Icone dal set SVG (mai emoji); `focus-visible` su ogni interattivo; target ≥ 44px.
- [ ] Stato vuoto curato; **bella appena inserita**.
- [ ] **Coerenza render Vue (canvas) == PHP (frontend)**; chiavi salvate invariate.
- [ ] Dopo le modifiche: build `node node_modules/vite/bin/vite.js build` + bump `OLO_VERSION`; aggiorna `TILE_PROGRESS.md`.

## 2) Fase B — comporre i 50 template sul set coerente
Solo DOPO che le tile della categoria sono a 100%. Linee guida di composizione:
- **Sistema, non one-off:** ogni template riusa gli stessi pattern di sezione (hero, feature-split,
  card-grid, stats, pricing, CTA, footer) con ritmo coerente; max 1–2 background per template.
- **Token-first end-to-end:** i template impostano i ruoli colore globali (GlobalColorsPanel) e le
  tile li seguono; nessun hex nel template.
- **Spaziatura/raggio/ombra dalle scale condivise** (SPACE/RADIUS + scala `shadow`): un template =
  una famiglia visiva.
- **Responsive** via device switch globale (StyleBoxStack): verifica desktop/tablet/mobile.
- **Salvataggio:** i template vivono in `olo_templates` (DB) — vedi `includes/` + `templates/`;
  rispetta lo schema esistente. Se esiste già un elenco/roadmap dei 50 template, è in
  `TILE_PROGRESS.md` o nei doc di repo: **leggilo prima** e non re-inventare l'elenco.
- **Archetipi consigliati** (se l'elenco non è fissato), per coprire i verticali: Landing prodotto,
  SaaS, Agenzia/Portfolio, E-commerce/Shop, Ristorante/Food, Eventi, Blog/Magazine, Corporate,
  Local business, Coming-soon. Per ciascuno: home + 1–2 pagine interne, comporte con le tile a 100%.

## 3) Guardrail trasversali (sempre)
- **Chiavi salvate INVARIATE** (margin_*, padding_*, border_radius, hover.*, shadow/shadow_custom, …):
  i template legacy devono continuare a rendere identici.
- **CHROME (builder) ≠ CONTENUTO (sito):** l'arancio `#e8622a` resta solo nella chrome dell'inspector;
  il colore delle tile/template = token cliente.
- **Mai toccare siti WordPress in produzione.** Build + bump `OLO_VERSION` dopo modifiche JS/CSS.

## 4) Ordine operativo consigliato
1. Implementa `FieldBoxShadow` (vedi `SPEC_fieldboxshadow.md`) → la "lingua d'ombra" è pronta per tutte.
2. Fase A a batch per categoria: porta le ~59 tile a 100% (alta-presenza prima), aggiornando `TILE_PROGRESS.md`.
3. (Opz.) refactor `iconlist→list` + rename `nav→linklist` con alias.
4. Fase B: componi i 50 template per archetipo sul set coerente; verifica responsive + render Vue==PHP.
