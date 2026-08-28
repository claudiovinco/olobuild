# Audit qualità tile OloBuild — report & decisioni
_2026-06-25 · 226 tile (config inspector `src/config/elements/*.js`, esclusi gli helper `_*`) · olobuild 1.4.292_

> **UPDATE (v1.4.293) — finding #6/§decisione 5 RISOLTO:** colori hardcoded tokenizzati. 77 config, **308 sostituzioni, 0 hex grezzi** rimasti nei `defaults` (ognuno → `var(--olo-color-<ruolo>, <seed>)`, mappatura fedele per ruolo). Eccezioni lasciate di proposito: `rgba()` scrim/ombre, `smearhero.smear_palette` (lista CSV), help-`<div>` di `table`. Build OK, verificato a video. ⏳ da deployare.

## Come è stato fatto (3 fonti incrociate)
1. **Deterministica** (sorgente): estratti default JS e metadati campi di tutte le 226 tile via esbuild → calcolo esatto di dropdown senza opzioni, numerici senza slider, lunghezza/qualità segnaposto, emoji.
2. **Giudizio** (workflow multi-agente, 30 agenti): per ogni tile contrasto, parità di render, coerenza `regoletiles1`, sintesi caratteristiche + dedup.
3. **Live** (mosaic.clod.eu): render reale di ogni tile col renderer PHP di produzione (`render_node_public`) + galleria visiva su sfondo neutro → **https://mosaic.clod.eu/olo-tile-audit/**

I finding degli agenti sono stati **verificati avversarialmente**: dove deterministica e agenti divergevano ho risolto sul codice o sul render live (vedi note). Nessuna modifica al codice è stata fatta — questo è un report di decisione.

---

## Esito in una riga
**Catalogo maturo e di buona qualità.** 226 tile: **119 ok / 105 minori / 2 maggiori**. Sul render live: **0 fatal, 0 tile completamente vuote**. I problemi reali sono pochi e mirati; il grosso del lavoro residuo è *igiene catalogo* (orfani + doppioni) e *coerenza* (token colore, copy localizzato).

---

## 1) Campi a discesa — completi ✅ (verificato)
**0 dropdown rotti su 226.** Unico sospetto deterministico (`progallery → layout`, select senza `options`) risolto: usa `optionsFn(settings)`, risolto da `BuilderInspector.vue:2417` in `options` dinamiche a runtime. Tutti gli altri `select/multi_pills/icon-select` hanno `options` o `optionsSource`.

## 2) Campi numerici → slider + rotella — in ottimo stato (verificato)
Regola reale (`InspectorField` + `NumberScrubber`): slider+rotella compaiono solo per `range`/`number`/`unit`/`spacing` con **min E max** finiti. Stato:
- **La stragrande maggioranza è corretta** (la migrazione `NumberScrubber` ha pagato).
- ~21 `range/number` senza min&max, ma **quasi tutti legittimamente illimitati** (prezzo, indici dinamici, secondi-video, valori-asse `chart`, config-di-slider `projector/scaler/mixer`) → corretto lasciarli senza slider.
- **Da rivedere (minori, migliorano la UX)**: `timezone.work_start/work_end/input_value` (ore → `min:0 max:23`), `timezone.items[].offset` (`-12..14`), `hotspots.items[].x/y` (percentuali → `0..100`); campi `unit` con min ma **senza max** (`announcementbar.letter_spacing`, `gallery.img_height`, `shatteredimage.height`, `svganimator.max_width`) → lo slider non ha fondo-scala.

## 3) Segnaposto — sensati, **non** esagerati ✅, ma 2 stonature
- **Lunghezza**: nessuna stringa default > 280 caratteri. 7 sottotitoli/standfirst lunghetti (134–217: `scrubtext.lead` 217, `featuredstory.standfirst` 178, `glowgallery.subhead` 168, `producthero/searchhero.subhead`, `presencegrid.ticker` — quest'ultimo è un ticker, ok). → **minore**, accorciabili.
- **Qualità (finding reale)**: default segnaposto-deboli **"Titolo Provvisorio"/"Sottotitolo provvisorio"** su `hero`, `content`, `iconbox`, `panel` (e `buildermock`, che però è una mock). Sotto lo standard OloBuild → sostituire con copy demo curata. _(Vedi screenshot galleria: tile `hero`/`content`.)_
- **Copy non localizzata**: diverse tile da blueprint hanno default in **inglese** (`chathero`, `announcementbar`, `audiohero`, `featuredstory`, ecc.). Funzionano ma stonano in un ecosistema IT → da localizzare (coerenza i18n, minore).

## 4) Contrasto — buono ovunque (verificato a video)
Galleria su sfondo neutro forzato: **nessun bianco-su-bianco / testo invisibile reale**. Le tile che ereditano i colori (hero/content/iconbox/panel) hanno testo scuro → leggibili su pagina chiara. I dark-hero (`chathero`, `featuredstory`, eventi…) portano il **proprio** sfondo scuro → contrasto eccellente. Unico flag da sorgente: `chathero.sub_color` `#776e92` su `#140e22` (grigio-viola su scuro) — **borderline AA per testo piccolo**, da misurare; a video resta leggibile.
> Nota metodo: un primo tentativo di galleria mostrava i dark-hero "invisibili" — era un **bug del mio banco di prova** (CSS `.olo-section{background:transparent}` che azzerava lo sfondo proprio), non delle tile. Corretto.

## 5) Parità di render — verificata sul renderer reale (corregge i finding grezzi)
Render live di tutte le 226: **0 fatal, 0 output completamente vuoto**. Distinzioni importanti:
- **Orfani reali — `olo_room_*` (10 tile)**: nessun renderer PHP (né in olobuild né in olo-booking; alias `class-tile-aliases.php` non attivi) → sul **frontend rendono solo un guscio vuoto (126 byte)**. Hanno componente Vue (preview nel canvas) ma sito vuoto. Sono tile booking legacy in attesa di migrazione `ac-*`. → **decidere: deprecare dal picker o cablare il renderer**.
- **`offcanvas`, `revealbox`, `hiddenpop`**: nessuna classe PHP dedicata né componente canvas → guscio vuoto a default (sono container "reveal": forse corretti **con figli**, da confermare). → **verificare l'intento**.
- **12 tile senza componente canvas Vue** (`asciiviz, goo, leaderboard, particlefx, physicsbin, presencegrid, scratchfx, scrollscrub, stackscroll, variablespecimen, revealbox, buildermock`): nel **builder** mostrano `ExternalTilePlaceholder`, ma **sul sito rendono benissimo** (verificato). Sono per lo più effetti pesanti → placeholder-in-canvas è una scelta accettabile (eventuale miglioria UX builder, non un difetto).
- **`authorbox`**: l'agente lo dava "senza Vue" → **falso positivo**, è mappato in `TileBase.vue:466`.
- **`woo_*` (31)**: dinamiche, richiedono WooCommerce + contesto prodotto → non valutabili in galleria statica (atteso).

## 6) Coerenza `regoletiles1` — il finding sistemico più grosso
- **Colori hardcoded (hex) nei default: 85 tile / 317 occorrenze** invece dei token `var(--olo-color-*)`. Peggiori: `step-timeline`(14), `producthero`/`searchhero`(10), `featuredstory`/`northquoteslider`(9), `audiohero`/`chathero`/`hero-split`/`lookbookmixer`/`proslider`(8). **Molti sono palette-demo da blueprint intenzionali** → serve giudizio caso per caso: convertire i ruoli (primario/UI grigi) a token, lasciare le palette d'autore. → **decisione di prodotto** (vedi CLAUDE.md: "mai hardcodare hex").
- **Emoji**: 1 sola reale (`trust-strip` 🇮🇹 nel testo demo); `list` usa glifi nelle label di scelta; `smearhero` una freccia ↑ intenzionale → trascurabili.

---

## 7) Inventario & doppioni (anti-ridondanza pre-produzione)
Inventario completo per-tile: **`INVENTARIO_TILE_2026-06-25.md`** (tipo, categoria, Vue/PHP, qualità, capacità, sintesi).

**0 duplicati identici**, ma **16 sovrapposizioni forti** da decidere prima della produzione (candidati a consolidamento con preset, mantenendo i dati compatibili):

| Famiglia | Tile sovrapposte | Proposta |
|---|---|---|
| PDF | `pdfviewer` · `pdfpro` | `pdfpro` è superset → tieni 1, l'altra come preset/alias |
| Galleria immagini | `gallery` · `progallery` (+`glowgallery`/`lightbox`) | `progallery` è quasi-superset → unifica con preset "base" |
| Slider immagini | `slideshow` · `overlayslider` · `panelslider` · `carousel` | una "Slider" con modalità; deprecare le più deboli |
| Slider citazioni | `testimonial` (layout carousel) · `northquoteslider` | assorbi `northquoteslider` come preset (è dormiente) |
| Griglie di card | `grid` · `overlaygrid` · `info-cards` · `product-cards` · `showcasegrid` | consolida overlay/masonry in `grid`; tieni distinte solo le firme reali |
| Hero SaaS scuro | `chathero` · `producthero` · `northvideohero` · `studiohero` | base comune + preset per il "mockup" (chat/browser/video/infografica) |
| Hotspot su immagine | `hotspot` · `hotspots` · `popover` | una tile con opzione "con/senza immagine" |
| Popup/flottanti | `popup` · `hiddenpop` · `floatingpanel` · `offcanvas` | `popup` è il più ricco → assorbi `hiddenpop` |
| Menu/header | `megamenu` · `navmenu` · `mobilebar` | chiarire confine header-completo vs menu-da-WP |
| Checkout Woo | `woo_checkout` · `woo_checkout_multistep` | flag `layout: single|multistep` |
| Breadcrumb | `breadcrumbs` · `woo_breadcrumbs` | coerente con famiglia woo_* (sorgente diversa) — tieni |
| Griglie relazionali Woo | `woo_related`·`woo_upsells`·`woo_cross_sells`·`woo_recently_viewed`·`woo_product_bundle` | stessa UI, query diversa → 1 tile con parametro relazione |
| Immagine prodotto Woo | `woo_product_image` · `woo_product_gallery_slider` | unifica con flag slider |
| Divisori forma | `shapedivider` · `spacer` · `divider` | chiarire ruoli (spacer include già forme) |
| Atomi sala (booking) | `olo_room_info/description/contacts/pricing` | stessa shell, campo diverso → 1 atomo parametrico |
| Calendario sala | `olo_room_calendar` · `olo_room_availability` | unificare "disponibilità sala" |

Altre **43 famiglie "vicine-ma-distinte"** (es. i vari hero d'archetipo, finder/tripfinder, counter/countercircle/countdown) → **tenere**, ma con anteprime/etichette chiare nel picker per evitare scelte casuali (la famiglia hero è la più affollata).

---

## Decisioni proposte (in ordine di priorità)
1. **Orfani `olo_room_*` (10)** — decidere: nascondere dal picker finché non c'è il renderer, oppure cablare l'alias booking. _(impatto: utente che le inserisce vede pagina vuota)_
2. **Verificare `offcanvas`/`revealbox`/`hiddenpop`** — confermare che funzionino come container con figli; altrimenti completarne il renderer.
3. **Sostituire i segnaposto "Provvisorio"** (`hero`, `content`, `iconbox`, `panel`) con copy demo di qualità + **localizzare** la copy inglese da blueprint.
4. **Consolidamento doppioni** (le 16 famiglie sopra) — decisione di prodotto, da fare prima della produzione; tutti hanno contratto-dati preservabile via preset/alias.
5. **Token colore** — pianificare la bonifica progressiva dei 317 hex hardcoded verso `var(--olo-color-*)` (almeno i ruoli/primario), tenendo le palette d'autore.
6. **Micro-fix slider numerici** (timezone ore, hotspots x/y %, `unit` senza max) — additivi, a basso rischio.
7. **(facoltativo) UX builder** — componente canvas per gli effetti pesanti oggi su placeholder.

_Dati grezzi a supporto: `tmp_audit_tiles.json` (226 finding), `tmp_audit_dedup.json` (59 cluster), `tmp_tile_fields.json`, `tmp_tile_defaults.json`._
