# OLOthemes — working memory (for a new chat)

Collezione di **50 theme front-end originali** per OLObuild, ispirati ai generi YOOtheme Pro
ma con testi/foto/palette/nomi originali. Tutto è **fatto e verificato**. Questo file dà il
contesto a una chat nuova senza ripartire da zero.

## Stato: COMPLETO
- **50/50 theme live** nella vetrina `OLOthemes - Gallery.html` (contatori a "50 live").
- Ogni theme = **home + 1 pagina interna** + un CSS dedicato in `olothemes/<nome>.css`.
- **36 "zone interattive" su misura** aggiunte (vedi sotto), tutte verificate, 0 errori console.

## File chiave
- `OLOthemes - Gallery.html` — vetrina 50 card, filtri Topic/Type, hero "Fifty themes. One builder.".
  Per marcare live una card: sostituire il blocco `.tprev--ph` con
  `<a class="tprev" href="FILE"><img src="previews/NOME.jpg">…</a>` (+ ribbon Live) e aggiornare i 2 contatori.
- `olothemes/theme.js` — shared: reveal-on-scroll (html.reveal-on + [data-reveal]), counters,
  marquee [data-marquee], mobile menu, parallax.
- `olothemes/fx.js` — shared **"wow" layer + motore zone interattive** (vedi API sotto).
- `OLOtheme - <Nome> (<Categoria>).html` + pagina interna `OLOtheme - <Nome> - <Pagina>.html`.
- `previews/NOME.jpg` — screenshot card vetrina.

## Convenzioni (rispettarle sempre)
- Palette agganciata ai token OLObuild (primario rosso `#e1474f`, navy, ambra) ma ogni theme ha
  la sua palette = swatch in vetrina. Ogni CSS definisce `:root{--…}` + `--disp`/`--sans`.
- **Font distinto per theme** (Google Fonts). Niente Inter/Roboto/Arial.
- Immagini = **placeholder a strisce** `.media[data-label="…"]` (mai SVG disegnati a mano).
- Ogni sezione mappata a una tile via `data-olo-tile="NomeTile"`; pattern non esistenti nelle tile
  OLObuild marcati con `data-olo-note="NEW tile candidate: …"`.
- Prefisso classi per theme (es. Kiln=`kl-`, Vinea=`vn-`, Sterling=`st-`…). NB: Vela e Vélour
  usano entrambi `vl-` ma caricano CSS separati → nessun conflitto.
- HTML canonico, attributi tra virgolette, niente self-close su non-void.

## fx.js — "wow" layer (auto se `<body data-fx>` + pointer fine + motion ok)
- **cursore custom** (dot+ring, mix-blend-mode:exclusion, ring cresce a 80px su hover) — html.fx-cursor-on
- `[data-magnetic]`, `[data-tilt]`, `[data-spotlight]` (+ helper `.fx-spot`), `[data-peek]`
- **menu creativo fullscreen**: trigger `.fx-menu-trigger[data-menu-toggle="#id"]` + `[data-menu]`/`.fx-menu`
  (var-driven: `--fx-menu-bg/-fg/-accent/-font/-weight/-glow`)
- altri opt-in: `[data-hotspot]`/`[data-dot]`, `[data-hscroll]`, `[data-countdown]` (+ `[data-cd]`), `[data-smear]`

## fx.js — 4 widget ZONE INTERATTIVE (data-attribute driven, ereditano la palette via CSS vars)
Var di stile comuni sulla sezione: `--fx-zone-accent`, `--fx-zone-on`, `--fx-zone-line`, `--fx-zone-bg`, `--fx-zone-thumb`.

1. **Finder** (consiglia con un tap): `[data-finder]` con bottoni `.fxf-opts > [data-finder-opt="key"]`
   e pannelli `[data-finder-res="key"]`. Primo attivo di default. `.show` NON forza display →
   il theme può rendere la card `display:flex/grid`.
2. **Builder** (somma live): `[data-builder data-currency="€" data-cap="N"]`; item
   `[data-bld-item data-n="0" data-price="x"]` con `.fxb-step` ( `[data-bld-dec]`/`[data-bld-count]`/`[data-bld-inc]` );
   output `[data-bld-total]`, `[data-bld-items]`; item attivi prendono `.on`; `data-cap` opz. limita la quantità.
3. **Mixer** (fonde swatch → preview): `[data-mixer data-max="3" data-empty="…"]` con
   `.fxm-swatches > [data-mix="#hex" data-mix-name="Nome"]`, **preview `[data-mix-preview]` DENTRO il mixer**,
   nome scelto in `[data-mix-out]`. Blend = media RGB (no color-mix → robusto).
4. **Projector** (slider → valore): `[data-project data-rate="0.06" data-years="10" data-currency="€"]`,
   input `[data-project-input]`, output `[data-project-out]`, opz. `[data-project-contrib]`.
   `rate=0` ⇒ lineare (units×years): usato per cost/tax/ore. `data-currency=""` ⇒ nessun simbolo.

## fx.js — 4 widget ZONE AVANZATE (sessione 2, novel/rari) — slider con classe `.fx-range`
5. **TypeTester** (`setupTypeTester`): `[data-type-tester]` › `[data-tt-specimen][contenteditable]`,
   `input.fx-range[data-tt-axis="font-size|font-weight|letter-spacing|line-height"]`, readout `[data-tt-val="<prop>"]`.
   Unità: size→px, letter-spacing→value/100 em, line-height→value/100, weight→numerico (FONT VARIABILE richiesto).
6. **RouteScrubber** (`setupScrub`): `[data-scrub]` › `input.fx-range[data-scrub-input]` (0..N−1), `[data-scrub-panel]`×N,
   `[data-scrub-go="i"]` (bottoni tappa). Pannello attivo `.show` (display deciso dal tema, come Finder). CSS pannelli in fx.js.
7. **TimezonePlanner** (`setupTimezone`): `[data-timezone]` › `[data-tz-input]` (0-23), `[data-tz-city][data-offset]`
   (1ª = base) con `[data-tz-clock]`; `[data-tz-base]`, `[data-tz-verdict][data-ok]`; stato `[data-state=work|ok|sleep]`.
   `local=((round(utc+off)%24)+24)%24`, utc=slider−baseOffset. Niente DST (indicativo).
8. **BakersCalc** (`setupBakers`): `[data-bakers]` › `[data-bk-input]` (peso base), `[data-bk-ing][data-pct]` con
   `[data-bk-out]`; `[data-bk-flour]` (valore base), `[data-bk-total]`. `g=round(base*pct/100)`, `total=base+Σg`.
- **`.fx-range`** = stile slider condiviso (riempimento via `--pct`, usa `--fx-zone-*`); usato da questi 4 + lo si può riusare.

### Bug motore già corretti (NON reintrodurre)
- I default numerici NON usano `|| default` (0 è falsy): `data-rate/-years` e `data-currency=""` gestiti con
  controllo `==null`. Altrimenti rate=0 / currency vuota fallivano.
- `setupCursor`: guard `e.target.closest` (eventi sintetici senza target elemento).

## Le 36 zone (theme → tipo)
- **Mixer**: Kiln(glaze) · Canvas(paint) · Vélour(hair-melt) · Loft(materiali) · Prisma(brand palette)
- **Builder**: Honeycomb(box6) · Carrello(cart) · Tavola(table) · Verde(bowl) · Brewline(coffee box) ·
  Mercato(basket) · Field&Co(kit) · Circuit(plan add-ons) · Verdano(matchday)
- **Finder**: Vinea · Terra · Meridian · Lumen · Pulse · Maison · Vitalis · Cadence · Linea · Pasaje ·
  Relay OS · Saffron · Fjordline · Atelier · Vela · Hearth
- **Projector**: Sterling(compound) · Nimbus(cost,rate0) · Ledger(set-aside,rate0) · Synapse(ore,rate0,cur"") · Capital Row(fund)

### Aggiornamento sessione (zone aggiunte)
- **Contour** (Health, era l'unico gap senza zona) → **Finder** "Where's your body today?" (4 opzioni → The Reset/Foundations/One to One/Reformer twice). `co-find*` in contour.css.
- **Carrello** (E-commerce) → **gift Finder** "Who are you shopping for?" (partner/friend/home/self) IN AGGIUNTA al cart Builder → pagina **2 zone**. `ca-find*` + `.ca-find-head`.
- **Fjordline** (Travel) → **budget Projector** "What should I budget?" (nights×€295, rate0, currency="" col € statico nel markup) IN AGGIUNTA al trip Finder → pagina **2 zone**. `fj-proj*`.
- Pattern coverage: Finder ora anche in E-commerce, Projector anche in Travel. Totale theme-con-zona: **36** (Finder 18 conteggi · Builder 9 · Mixer 5 · Projector 6, con Carrello/Fjordline doppie).

### Già interattivi (lasciati invariati): Bloom(shade-finder), Forge(⌘K palette), Soundwave(EQ player),
Aurora(countdown), DataFold(charts), Wander(TripFinder), Vows & Fiori (Countdown+RSVP).
### Content-first, NESSUNA zona (sarebbe slop): Mono, Voyage, Gazette, Dispatch, Signal, Frame.

## Retrofit fx.js
I 50 file: i theme nuovi (batch 4+) avevano già `fx.js`+`data-fx`. Gli 11 originali + batch 1–3
sono stati **retrofittati** (aggiunto `<script src="olothemes/fx.js"></script>` prima di theme.js +
`data-fx` sul body) man mano. Per sapere chi ha fx.js: `grep "olothemes/fx.js"`.
NB: `run_script`/saveFile NON accetta nomi file con `&` → usare gli strumenti file normali per quei file.

## Workflow di verifica (importante)
- Lo screenshot html-to-image **NON riflette stati toggled** (finder/mixer/projector dopo click) → verificare
  con il **verifier via eval_js** (leggere stato calcolato), non con screenshot.
- Per le anteprime: iniettare `html.reveal-on [data-reveal]{opacity:1!important;transform:none!important}`
  e (per vedere una sezione) spostarla in cima al body, perché la cattura parte dall'alto.

## Fatto questa sessione (oltre alle zone qui sopra)
- **Badge vetrina**: `themes/gallery.js` ora ha la mappa `ZONES` (theme→tipo zona), inietta un
  pill `.zone-tag` (top-right, glass + pallino ambra) sulle 36 card con zona, e gestisce una
  **nuova riga filtro "Zones"** (All/Interactive/Finder/Builder/Mixer/Projector) via `data-zone`.
  CSS `.zone-tag` in `themes/gallery.css`. Multi-zona mostrate come "Builder · Finder".
- **Spec 4 tile**: `design_handoff_olobuild_tiles/ZONE_TILES_SPEC.md` — Finder/Builder/Mixer/Projector
  come tile OLObuild (campi inspector, chiavi salvate, token-first, repeater, DoD), agganciata a
  `BUTTON_EXAMPLE.md`. Proto live = `olothemes/fx.js`.

## Sessione 2 — zone avanzate (novel/rare) + sfida Claude Code
- **4 tile interattive uniche** costruite (vedi contratti fx.js #5–8):
  - **Mono** (Creative) → **TypeTester** (specimen variabile editabile). NB: Mono NON aveva fx.js → aggiunto
    `<script src="olothemes/fx.js">` (SENZA `data-fx`, per non attivare il cursore custom). DM Sans import esteso a `wght@9..40,200..800` (variabile). `.mo-type*`.
  - **Voyage** (Travel journal, era content-first) → **RouteScrubber** "Follow the route" (5 tappe Carretera Austral). `.vy-scrub*`.
  - **Relay OS** (Tech) → **TimezonePlanner** (4 città SF/London/Berlin/Singapore) IN AGGIUNTA al recipe Finder → 2 zone. `.ro-tz*`.
  - **Honeycomb** (bakery) → **BakersCalc** (idratazione sourdough 78/20/2) IN AGGIUNTA al box Builder → 2 zone. `.hc-bk*`.
- **Vetrina**: `ZONES` in gallery.js aggiornata → **38 theme con badge** (Mono "Type tester", Voyage "Route",
  Relay "Finder · Time zones", Honeycomb "Builder · Baker's %"). Filtro Interactive=38. Etichette multi-parola OK
  (il filtro per pattern resta su finder/builder/mixer/projector).
- **Brief sfida**: `design_handoff_olobuild_tiles/ADVANCED_TILES_CHALLENGE.md` — Parte A (le 4 sopra come tile OLObuild),
  Parte B (8 tile "stretch" da inventare: PaletteHarmony, FloorPlanPicker, StepSequencer/WebAudio, SpinViewer360,
  ContrastChecker, LookbookMixer, RecipeScaler, AvailabilityHeat), Parte C (refactor intenso a batch: token-first,
  useBoxModel, a11y AA, FieldBoxShadow, repeater key UUID, redesign categorie). Aggancia BUTTON_EXAMPLE/ZONE_TILES_SPEC.

## Sessione 3 — 7 zone "stretch" avanzate (Parte B del brief) costruite e verificate
Tutte nel motore `fx.js` (vanilla), slider classe `.fx-range`. Helper colore in fx.js:
`hexToRgb/rgbToHex/rgbToHsl/hslToHex/relLum`. **Verificate live** (vedi nota cache sotto):
- **Soundwave** (Artist) `#beat` → **StepSequencer** (Web Audio): griglia 4 tracce×16 step, Play→loop
  sintetizzato (kick osc sweep; snare/hat/clap = noise buffer filtrato), BPM 70-170. AudioContext SOLO al Play. `.sw-seq*`.
- **Prisma** (agency) `#palette-gen` → **PaletteHarmony**: seed+harmony → 5 swatch HSL, click-to-copy. 2ª zona (oltre Mixer). `.pr-pal*`+`.pal-sw`.
- **Forge** (dev) `#contrast` → **ContrastChecker**: WCAG ratio live + badge AA/AAA/AA-Large. `.fg-ct*`.
- **Tavola** (trattoria) `#recipe` → **RecipeScaler**: porzioni→quantità, `data-frac`→¼½⅓⅔¾, resto arrotonda g/ml. 2ª zona (oltre Builder). `.tv-rc*`.
- **Cadence** (coach) `#availability` → **AvailabilityHeat**: griglia 7giorni×3fasce→tally+track(Reset/Build/Peak)+giorno top. 2ª zona (oltre Finder). `.cd-heat*`.
- **Saffron** (restaurant) `#table` → **FloorPlanPicker**: tavoli posizionati→prenotazione (tavoli+posti+nomi), taken disabilitati. 2ª zona (oltre Finder). `.sf-fp*`.
- **Bloom** (beauty) `#routine` → **LookbookMixer**: 4 slot prev/next compongono routine + totale €. `.bl-lb*` (+`[data-lb-opt]{display:none}`).
- **Mercato** (concept store) `#spin` → **SpinViewer (360)**: drag/arrow/step ruota un prodotto su 24 frame; readout angolo+frame, dot orbitante, progress. 2ª zona (oltre Builder). `.mc-spin*` (`setupSpin`). Demo = placeholder con sheen scorrevole + rotateY; tile vera = sprite frames.
- **Vetrina** `gallery.js`: `ZONES` → **41 theme con badge** (38 + Soundwave "Sequencer", Forge "Contrast", Bloom "Routine";
  Mercato ora "Builder · 360°");
  Prisma "Mixer · Palette", Tavola "Builder · Recipe", Cadence "Finder · Availability", Saffron "Finder · Floor plan").
- **Brief** `ADVANCED_TILES_CHALLENGE.md` Parte B aggiornata: 7/8 costruite (manca solo B4 SpinViewer, serve sprite).

### ⚠️ Nota cache preview (IMPORTANTE per verifiche future)
Dopo aver editato `fx.js`, l'iframe del preview può servire un **blob STALE** del vecchio fx.js
(handlers non attaccati, `.fx-range` assente, slider blu nativo). NON è un bug del codice.
Verifica reale: `new Function(src)` su fx.js (parse OK = codice sano) **oppure** iniettare nel
preview uno script `olothemes/fx.js?cb=Date.now()` e poi testare. Il verifier (contesto fresco) carica sempre la versione corrente.

## Possibili prossimi passi (opzionali)
- **Tutte e 8 le tile stretch (Parte B) sono fatte.** Resta solo, eventualmente, rigenerare
  `previews/*.jpg` dei theme con sezione nuova (12 theme toccati in 3 sessioni).
- Oppure passare al lavoro core: refactor tile esistenti (Parte C del brief) o iniziare l'integrazione in OLObuild.
