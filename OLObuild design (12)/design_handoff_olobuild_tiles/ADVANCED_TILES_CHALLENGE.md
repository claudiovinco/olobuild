# Sfida Claude Code — tile interattive avanzate + refactor intenso

Brief operativo per mettere **Claude Code** sotto sforzo sul set tile di OLObuild.
Due fronti: **(A+B) costruire tile nuove e insolite**, **(C) rifattorizzare in profondità**
le tile esistenti. Tutto resta dependency-free, token-first, con chiavi salvate stabili.

> **Proto vivi:** `olothemes/fx.js` (motore vanilla) + i demo OLOthemes. Le 4 tile della
> Parte A **esistono già funzionanti** lì dentro — Claude Code le porta a tile OLObuild
> (registry + Vue), non le inventa da zero.
> **Vedi anche:** `ZONE_TILES_SPEC.md` (Finder/Builder/Mixer/Projector), `BUTTON_EXAMPLE.md`
> (modello di refactor), `DESIGN_LANGUAGE.md` (10 regole + CHROME vs CONTENUTO),
> `TOKEN_MAPPING.md` (come nascono le `--olo-color-*`).

Regole valide ovunque (ripasso): un solo controllo colore `zone_accent` (gli altri derivati
via `contrastOn`/`color-mix`), `border_radius`+`tile_padding` via `useBoxModel`, default da
`buildDefaults('<type>')`, slider con classe `.fx-range` (riempimento via `--pct`), stati
hover/attivo/`:focus-visible`/vuoto/`prefers-reduced-motion`, controlli reali + `aria-*`,
output con `aria-live="polite"`, target ≥44px. **Le tile sono CONTENUTO** → token del cliente,
mai l'arancio della chrome.

---

# PARTE A — 4 tile nuove "zona interattiva avanzata" (proto già pronti in fx.js)

Categoria *Interattive*. Per ognuna è indicato il **contratto data-attribute reale** in `fx.js`
(quello da replicare/whitelistare nel renderer), i **campi inspector**, le **chiavi salvate**.

## A1 · TypeTester — specimen variabile editabile dal vivo
*Demo: Mono.* Slider d'asse pilotano un campione di testo **editabile** (contenteditable).

| Campo inspector | Controllo | Chiave | Default |
|---|---|---|---|
| Testo campione | Text (multilinea) | `specimen` | "Typography is a voice you can see." |
| Font | Select (font del sito) | `font_family` | display del tema |
| Assi attivi | Repeater | `axes[]` | size/weight/tracking/leading |
| Colore zona | Color/ruolo | `zone_accent` | `''` → text/ink |

**`axes[]`**: `{ name:"font-size"|"font-weight"|"letter-spacing"|"line-height", min, max, step, value }`.
**Mapping unità (da `setupTypeTester`)**: `font-size`→`px`; `letter-spacing`→`value/100 em`;
`line-height`→`value/100` (unitless); `font-weight`→numerico (font variabile).
**Reference:** `[data-type-tester]` › `[data-tt-specimen][contenteditable]`,
`input.fx-range[data-tt-axis="<css-prop>"]`, readout `[data-tt-val="<css-prop>"]`.
⚠️ Richiede un **font variabile** (servi `wght@…..…` su Google Fonts) o la tile limita lo slope
del weight ai pesi disponibili.

## A2 · RouteScrubber — scrub lungo una sequenza di tappe
*Demo: Voyage.* Trascini un handle (o tocchi una tappa) e il pannello corrispondente fa il cross-fade.

| Campo | Controllo | Chiave | Default |
|---|---|---|---|
| Titolo / sottotitolo | Text | `heading`, `sub` | '' |
| Tappe | Repeater | `stops[]` | 4–6 |
| Mostra rail tappe | Toggle | `show_stops` | `true` |
| Colore zona | Color/ruolo | `zone_accent` | `''` → primary |

**`stops[]`**: `{ label, sub, media:{id,alt}, kicker, title, stand, body }`.
**Reference:** `[data-scrub]` › `input.fx-range[data-scrub-input]` (min 0, max N−1, step 1),
`[data-scrub-panel]` ×N, `[data-scrub-go="i"]` (bottoni tappa). Pannello attivo = `.show`
(la tile ne decide il `display`, come Finder). Prima tappa attiva al load.

## A3 · TimezonePlanner — slider 24h → orologi multi-città + verdetto
*Demo: Relay OS.* Lo slider fissa l'ora nella città base; ogni città ricalcola il proprio orario
e si colora per fascia (lavoro/sveglio/notte), con un verdetto di sovrapposizione.

| Campo | Controllo | Chiave | Default |
|---|---|---|---|
| Titolo / intro | Text | `heading`, `intro` | '' |
| Città | Repeater | `cities[]` | 3–5 |
| Città base | Select (indice) | `base` | `0` |
| Ora iniziale | Number 0–23 | `value` | 9 |
| Colore zona | Color/ruolo | `zone_accent` | `''` → primary |

**`cities[]`**: `{ name, utc_offset }` (es. −8, 0, 1, 8; ammessi mezzi fusi tipo 5.5).
**Logica (da `setupTimezone`)**: `utc = sliderHour − baseOffset`; per ogni città
`local = ((round(utc+offset) % 24)+24) % 24`; fascia `work` 9–18, `ok` 7–9/18–22, altrimenti
`sleep`; verdetto = quante città in `work`.
**Reference:** `[data-timezone]` › `input.fx-range[data-tz-input]`, `[data-tz-city][data-offset]`
(prima = base) con `[data-tz-clock]`; `[data-tz-base]`, `[data-tz-verdict][data-ok]`; stato su
`[data-state="work|ok|sleep"]`. ⚠️ Niente DST: dichiararlo "indicativo".

## A4 · BakersCalc — peso farina → ricetta scalata (baker's %)
*Demo: Honeycomb.* Slider sul peso farina (=100%): ogni ingrediente si ricalcola in grammi dalla
sua percentuale, più il totale impasto. (Generalizzabile: "Calcolatore proporzioni" — ratio base.)

| Campo | Controllo | Chiave | Default |
|---|---|---|---|
| Titolo / nota | Text | `heading`, `note` | '' |
| Base (min/max/step/iniz.) | Number ×4 | `base_min/max/step/value` | 300/1500/25/1000 |
| Etichetta base / unità | Text | `base_label`, `unit` | "Farina" / "g" |
| Ingredienti | Repeater | `ings[]` | water 78 · levain 20 · salt 2 |
| Colore zona | Color/ruolo | `zone_accent` | `''` → primary |

**`ings[]`**: `{ name, pct }`. **Logica (da `setupBakers`)**: `g = round(base*pct/100)`;
`total = base + Σ g`. **Reference:** `[data-bakers]` › `input.fx-range[data-bk-input]`,
`[data-bk-ing][data-pct]` con `[data-bk-out]`; `[data-bk-flour]` (valore base), `[data-bk-total]`.

---

# PARTE B — 8 tile "stretch" (TUTTE E 8 COSTRUITE come zone live)

Concept rari, oltre i pattern soliti. **Tutte e 8 sono implementate e funzionanti** in
`olothemes/fx.js` + montate in un theme reale (proto vivi → Claude Code le porta a tile OLObuild).
Stessa disciplina (token-first, a11y, `.fx-range` dove c'è uno slider, DoD).

| # | Tile | Stato · proto | Meccanica (1 riga) | Contratto fx.js |
|---|------|---------------|--------------------|-----------------|
| B1 | **PaletteHarmony** | ✅ **Prisma** `#palette-gen` | seed → analoghi/complement/triad/tetrad/mono (HSL), hex click-to-copy | `[data-palette]` › `[data-pal-seed]`, `[data-pal-scheme]`, `[data-pal-out]` (genera `.pal-sw` con `--c`/`--on`) |
| B2 | **FloorPlanPicker** | ✅ **Saffron** `#table` | tocchi tavoli su pianta posizionata → tavoli+posti+nomi; taken disabilitati | `[data-floorplan]` › `[data-fp-table data-cap data-name data-taken]`, out `[data-fp-tables]`/`[data-fp-seats]`/`[data-fp-list]` |
| B3 | **StepSequencer** | ✅ **Soundwave** `#beat` | griglia tracce×16, Play → loop Web Audio sintetizzato + BPM | `[data-seq data-steps]` › `[data-seq-play]`, `.fx-range[data-seq-bpm]`+`[data-seq-bpm-val]`, `[data-seq-row data-sound]`›`[data-seq-cell]` |
| B4 | **SpinViewer (360)** | ✅ **Mercato** `#spin` | drag/arrow/step ruota un prodotto su N frame; readout angolo+frame, dot orbitante, progress | `[data-spin data-frames]` › `[data-spin-stage]`, `[data-spin-obj]` (usa `--f`/`--deg`), `[data-spin-angle]`/`[data-spin-frame]`/`[data-spin-dot]`/`[data-spin-prog]`, `[data-spin-prev/next]` |
| B5 | **ContrastChecker** | ✅ **Forge** `#contrast` | testo/sfondo → rapporto WCAG live + badge AA/AAA/AA-Large | `[data-contrast]` › `[data-ct-fg]`, `[data-ct-bg]`, `[data-ct-preview]`, `[data-ct-ratio]`, `[data-ct-badge=aa|aaa|aa-lg]`›`[data-ct-state]` |
| B6 | **LookbookMixer** | ✅ **Bloom** `#routine` | per-slot prev/next compone un look/routine + totale live | `[data-lookbook data-currency]` › `[data-lb-slot]`(`[data-lb-prev/next]`,`[data-lb-name/price/sw]`,`[data-lb-opt data-name data-price data-color]`), `[data-lb-total]` |
| B7 | **RecipeScaler** | ✅ **Tavola** `#recipe` | stepper porzioni → quantità riscalate; `data-frac` usa ¼½⅓⅔¾, resto arrotonda | `[data-recipe data-base data-min data-max]` › `[data-rc-dec/inc/count]`, `[data-rc-ing data-qty data-unit data-frac]`›`[data-rc-out]` |
| B8 | **AvailabilityHeat** | ✅ **Cadence** `#availability` | griglia giorno×fascia → tally + track consigliata + giorno più scelto | `[data-heat]` › `[data-heat-cell data-day]`, out `[data-heat-count]`/`[data-heat-plan]`/`[data-heat-day]` |

Note di implementazione trasversali (come implementate):
- **B1/B5** = matematica colore pura in fx.js (`hexToRgb`/`rgbToHsl`/`hslToHex`/`relLum`): deterministiche, zero asset.
- **B3** = **Web Audio**: AudioContext creato SOLO al gesto Play; kick = osc sine sweep, snare/hat/clap = noise buffer filtrato; nessun file audio.
- **B2/B6/B8** = pattern **selezione/composizione** (toggle `.sel`/`.on` o prev-next per slot). Per la tile OLObuild: `id` voce stabili (UUID), non da label.
- **B4** = nel demo il prodotto è un placeholder con sheen che scorre (`--f`) + leggero `rotateY`; nella tile vera `frames[]` = immagini sprite caricate (drag→indice frame). Niente SVG disegnato a mano.
- Tutti gli slider usano la classe condivisa **`.fx-range`** (riempimento `--pct`, colori `--fx-zone-*`).

---

# PARTE C — modifica intensa delle tile esistenti (backlog prioritizzato)

Estende la "Checklist di propagazione" del `BUTTON_EXAMPLE.md` a TUTTO il set. Lavorare **per
batch/categoria**, un PR per batch, diff verificabile, **chiavi salvate invariate**.

### Batch 1 — Token & default (la base, sblocca tutto)
- [ ] **Sweep token-first**: eliminare ogni hex hardcoded nelle tile (`#6366F1`, `#1e87f0`,
      `#e8622a`, `#e1474f`, grigi fissi) → `resolveColor(userValue, TOKENS.*)`.
- [ ] **`buildDefaults('<type>')` per OGNI tile**: fonte unica, niente default ridichiarati nel
      componente (oggi divergono da registry). Aggiungere i tipi mancanti a `oloTileDefaults.js`.
- [ ] **on-primary calcolato**: lo store scrive `--olo-color-on-primary` via `contrastOn` quando
      cambia il primario; le tile leggono quello.

### Batch 2 — Box-model & layout
- [ ] **`useBoxModel` ovunque**: normalizzare `border_radius` (numero|oggetto) e `tile_padding`
      (+ legacy `padding_x/y`) — togliere il parsing a mano dai componenti.
- [ ] **Responsive via device switch** (StyleBoxStack globale): spaziature e n° colonne
      responsive per le tile di layout (Hero, Grid, CtaBanner, CardGrid).
- [x] **FieldBoxShadow** ✅ **design pronto + contratto verificato sul repo** — `REFERENCE_shadow-control.html`
      + `prototype/FieldBoxShadow.vue` (rimpiazzo **drop-in** di `src/components/Builder/fields/FieldBoxShadow.vue`).
      Contratto REALE (da `_styleFieldsBase.js` → mapping `shadow_block`), invariato:
      `style.shadow` = `'none|sm|md|lg|xl|custom'` (preset, reso dallo **StyleEffectsStack**) +
      `style.shadow_custom` = oggetto **`{ h, v, blur, spread, color, inset }`** (questo componente),
      hover su `style.hover.shadow` / `style.hover.shadow_custom`. Default `{h:0,v:4,blur:10,spread:0,color:'rgba(0,0,0,0.15)',inset:false}`,
      colore via `FieldColor`. Da fare: (1) sostituire il `.vue` esistente con il redesign (stesse props/emit → zero migrazione);
      (2) portare la **scala segmented + chip elevazione** nel renderer dello StyleEffectsStack per il preset `style.shadow` (vedi colonna "Coerente" del reference).

### Batch 3 — Accessibilità (pass AA)
- [ ] Elementi semantici reali (`<a>`/`<button>`/`<input>`), non `<span>` cliccabili.
- [ ] `aria-pressed`/`aria-selected`/`aria-live`, `:focus-visible` visibile, target ≥44px.
- [ ] **Avviso contrasto AA nell'editor** quando il colore scelto non passa sul fondo.

### Batch 4 — Contenuto & coerenza
- [ ] **Emoji → icone SVG** del set (default e contenuti demo).
- [ ] **Repeater key stabili (UUID)** su GlobalColorsPanel e su tutte le tile con liste
      (Finder/Builder/Mixer + B2/B6/B8): rinominare una label non rompe lo stato. Vedi
      `TOKEN_MAPPING.md §fragilità`.
- [ ] Stati vuoti curati (placeholder) per ogni tile con media/lista.

### Batch 5 — Nuove categorie & redesign (da memoria progetto)
### Parte A (le 4 tile zona base) + le 7 stretch → ora tutte hanno proto vivo
- [ ] Registrare in *Interattive*: **Finder, Builder, Mixer, Projector** (ZONE_TILES_SPEC) +
      **TypeTester, RouteScrubber, TimezonePlanner, BakersCalc** + **PaletteHarmony, FloorPlanPicker,
      StepSequencer, ContrastChecker, LookbookMixer, RecipeScaler, AvailabilityHeat, SpinViewer** (Parte B, 8/8 live).
- [ ] Redesign categoria **Media**: Gallery, Carousel, Chart. **Layout**: Hero, Grid, CtaBanner.
- [ ] Aggiornare megamenu/preset (`megamenu/`) con i nuovi tipi.

---

# Definition of Done globale

Una tile/PR è "fatta" quando:
1. Inserita → **bella appena messa** (palette = token cliente, raggio/padding da scala, micro-ombra).
2. **Una sola fonte** di default; **chiavi salvate invariate** (nessuna migrazione dati).
3. Box-model via `useBoxModel`; un solo controllo colore (`zone_accent`), resto derivato.
4. Stati completi (hover/attivo/focus-visible/vuoto/reduced-motion) e **a11y AA**.
5. Logica numerica robusta: check `== null` (0 e "" validi), nessun `|| default` sui numeri.
6. Diff verificabile contro il proto in `fx.js` / demo OLOthemes.

## Come verificare le interazioni
Gli screenshot html-to-image **non** riflettono lo stato dopo i click/drag né le animazioni di
entrata. Verificare lo **stato calcolato via JS** (classList, `getComputedStyle`, textContent
degli output) — non solo a colpo d'occhio. Per le anteprime, forzare reveal e disattivare le
animazioni di entrata.
