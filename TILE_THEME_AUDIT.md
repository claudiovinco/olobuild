# Audit pixel-perfect temi OLOtheme (blueprint ↔ homepage.json)

Verifica 1-a-1 di ogni tema contro il suo blueprint HTML/CSS. Stato finale sessione 2026-06-07 (OLO_VERSION 1.4.62).
Metodo: lettura blueprint+CSS+homepage.json (Explore agent in parallelo) → divergenze reali → fix JSON.

## SCOPERTA CHIAVE
La ricostruzione di massa precedente è di **alta qualità** dove le tile mappano pulito. I problemi
erano MIRATI, non diffusi. Convenzioni di mapping corrette (NON sono bug):
- HoverList **indice** (num/titolo/cat/anno) → `worklist` (giusto). `hoverlist` SOLO per pastiglie-colore (bloom).
- Hero "showcase" pannello stat al posto di immagine = adattamento **image-free** accettato (no foto reali nei blueprint).
- CardGrid → `team` SOLO se sono persone reali (verificato caso per caso: velour/loft/contour/pulse/vitalis/sterling = persone ✓).
- Zone interattive difficili (ContrastChecker/PaletteHarmony/StepSequencer/RegionMap/FlowDiagram/MatchFixtures/SpinViewer) statiche = accettabile.
- "Pricing" che nel blueprint è solo header+CTA (vitalis) → cta-banner corretto.

## FIX APPLICATI QUESTA SESSIONE (7 temi)
- **linea** → cta-banner finale sostituito da `newsletter` "The quiet list" (form email).
- **mercato** → cta-banner finale sostituito da `newsletter` "Get first dibs".
- **vinea** → ProductGrid: `pricelist` → `product-cards` (card vino, gradiente colore, annata, tipo, prezzo).
- **carrello** → CategoryRail: `trust-strip` → `overlaygrid` (6 tessere categoria); countdown banner: +eyebrow "Maker's Week", sottotitolo corretto.
- **aurora** → rimossa striscia `overlaygrid` ridondante dopo l'hero (non nel blueprint); flusso hero→CategoryTiles ripristinato.
- **pasaje** → ProductGallery: `trust-strip` (pill testo) → `workgrid` masonry 6 ambienti.
- **meridian** → fix typo finder heading (backtick → apostrofo): "What's the decision?".

## VERIFICATI OK (nessun fix necessario / solo cosmetici minori non bloccanti)
Beauty: bloom (GOLD), atelier, lumen, velour.
Creative/Artist: canvas, mono, prisma, vela, kiln, soundwave(zone audio statiche).
Finance: capital-row, ledger (£ coerente; € del projector è incoerenza del blueprint), sterling.
E-commerce: fieldco.
Tech: circuit, datafold, forge, nimbus, relayos, synapse (zone difficili statiche).
Media: dispatch, frame, gazette, signal.
Home: hearth, loft, maison, terra.
Food: brewline, honeycomb, saffron, tavola, verde.
Health: cadence, contour, pulse, verdano, vitalis.
Travel: fjordline, voyage, wander.
Wedding/Events: fiori, vows.

## RIFINITURE BACKLOG — FATTE (workflow orchestrato + verifica avversariale, v1.4.63)
- tavola: gallery workgrid columns 3→4, items_gap→12; builder card_bg→#fbf5ec, sezione bg→#f4e9dc (paper/cream coppia blueprint).
- verde: gallery workgrid 3→4; RIMOSSA trust-strip annuncio ridondante (era dup dell'AnnouncementBar/header); showcase sourcing (vd-hero-split-18) popolato (era vuoto → box vuoto).
- fiori: step-timeline show_timeline true (timeline visiva linea+pallini).
- dispatch: hero-split rimosso CTA "Read full story" (blueprint solo byline); sezione business bg #ebe7dd→#f4f1ea (paper).
- terra: cta-banner finale cta_url #care→#shop.
- honeycomb: Cardamom Bun show_badge→false (badge "GONE BY 10" non nel blueprint); builder intro backtick→apostrofo; caption "flour & hands".
- datafold: theme.json google_fonts +IBM Plex Mono.
- wander: workgrid meta "[giorni] · [luogo] · prezzo" (verbatim); **iconlist ROTTO (chiavi title/description) → convertito in info-cards** (icona+titolo+desc, 3 voci).
- kiln: hotspots panel_label svuotato; workgrid label/title alla copy lowercase del blueprint; hero "kept" italic.
- maison: hotspots aspect 16/10→16/9.
- sterling: info-cards icona "Planning" calculator→layers (glifo blueprint).
- carrello: overlaygrid "Art & Prints"→"Art & prints" (casing blueprint).
- linea: newsletter max_width 560→440, btn_hover_bg→#cfb088 (camel-l).
- fjordline: section-header "Why" corsivo ristretto a "local guides," (inline).

### Falsi positivi confermati (LASCIATI, NON sono bug):
- meridian: i section-header con accento italic hanno gia' headline_inline:true → rendono inline (es. "Outcomes, not *slideware*") = blueprint. Il verificatore non lo aveva considerato.
- mercato: la sezione Lookbook "Made to be used daily" ESISTE (mc-hero-split-28) — il verificatore l'aveva mancata.
- cadence: BeforeAfter resta info-cards (imgcompare inutile senza foto); rimosso solo il footer-tag inventato.
- hero "showcase" pannello-stat image-free, headline_lines hero-split su riga singola = limiti/convenzioni accettati.

## TILE NUOVE DEDICATE — CREATE (v1.4.64)
3 tile nuove (4 file ciascuna: config JS + classe PHP + Vue WYSIWYG + registrazione manuale in class-olo-builder.php righe ~2985/3193 + import/map in TileBase.vue). Build OK, php -l pulito, smoke-test render sul server OK (no `&&`). Review avversariale superata (beforeafter match; categoryrail/tripfinder parità Vue allineata con chevron/hover/focus/snap via prop + `<style scoped>`).
- **categoryrail** (`Olo_CategoryRail_Tile`) — rail orizzontale drag-scroll di tessere categoria (immagine+overlay+titolo+sottotitolo, link). Runtime drag pointer inline (no `&&`). → cablato in **carrello** (CategoryRail, sostituisce overlaygrid) + **frame** (PhotoRail), **loft** (ProjectShowcase), **voyage** (StoryRail) — tutti rail `data-hscroll` del blueprint, ex-workgrid. NB: i ProductRail con prezzo (fieldco/terra) restano `product-cards` (contenuto > layout).
- **beforeafter** (`Olo_BeforeAfter_Tile`) — griglia card "prova": coppia media affiancati + etichette Before/After + didascalia. Statica (no JS). → cablato in **cadence** "The proof" (sostituisce info-cards).
- **tripfinder** (`Olo_TripFinder_Tile`) — barra ricerca/prenotazione: N campi (label+select nativo) + bottone. → cablato in **fjordline** (Destination/When/Activity), **pasaje** (Check in/out/Guests, booking bar cream), **wander** (Where/When/Trip type, sezione nuova dopo hero + hero showcase off).

## DEPLOY
OLO_VERSION 1.4.65. Deploy mosaic: olobuild.php + includes + assets/js/builder.js + assets/data/themes. 50/50 JSON validati; php -l pulito.
**DA REIMPORTARE dal pannello** (per le nuove tile): carrello, cadence, fjordline, pasaje, wander, frame, loft, voyage.
**DA REIMPORTARE dal pannello** i temi toccati per applicare i NODI: (turno 1) linea, mercato, vinea, carrello, aurora, pasaje, meridian; (turno 2) tavola, verde, fiori, dispatch, terra, honeycomb, datafold, wander, kiln, maison, sterling, cadence, fjordline (+linea/carrello ritoccati).
