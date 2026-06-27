# Progetto design — OLObuild (memoria di lavoro)

Stato del lavoro di design per il plugin **OLObuild** (page builder WordPress, repo GitHub
`claudiovinco/olobuild`, stack Vue 3 + Pinia + PHP). Questo file dà il contesto a una
nuova chat senza ripartire da zero.

## Obiettivo corrente
Rendere i controlli dell'inspector e le **240 tile** di OLObuild **belli e coerenti**, e
consegnare a Claude Code un pacchetto operativo che applichi le regole a tutto.

## Pacchetto handoff (cartella `design_handoff_olobuild_tiles/`)
È la fonte di verità, scaricato dall'utente in `D:\TECNICA\olobuild\regoletiles1\`.
- `START_HERE.md` — protocollo operativo per Claude Code (enumerare TUTTE le tile, tracker
  `TILE_PROGRESS.md`, checklist per tile, Definition of Done, guardrail). + `PROMPT.txt`.
- `DESIGN_LANGUAGE.md` — 10 regole "bello & coerente". Include la nota **CHROME vs CONTENUTO**.
- `TILE_AUDIT_CHECKLIST.md` — checklist 10 punti + 8 categorie per batch.
- `TOKEN_MAPPING.md` — come i colori si legano ai colori globali del cliente.
- `BUTTON_EXAMPLE.md` — refactor completo d'esempio.
- `CLAUDE.md` — versione integrata col CLAUDE.md reale del repo (da mettere in root repo).
- `prototype/` — `oloTileDefaults.js` (GLOBAL/SYSTEM token, resolveColor, contrastOn,
  SPACE/RADIUS, TILE_DEFAULTS), `useBoxModel.js`, `tokens-brand.css`, `FieldBorder.vue`.
- `REFERENCE_*.html` — riferimenti visivi: card, data, interactive, border-control.

## Decisioni chiave già prese
- **Primario tile = rosso brand `#e1474f`** (seed del GlobalColorsPanel; cliente può
  sovrascrivere → le tile seguono).
- **GlobalColorsPanel**: 6 ruoli (primary/secondary/accent/dark/light/text), id da label
  (fragile → stabilizzare). Secondario `#16263d` navy, accento `#f4a23b` ambra.
- **Semantici** (info/success/warning/error) personalizzabili dal cliente; soft via color-mix.
- **on-primary** calcolato per contrasto (`contrastOn`).
- **CHROME del builder ≠ contenuto tile**: inspector (slider/link/device/toggle) = arancio
  `#e8622a` + navy, FISSO (identità prodotto). Colore delle tile = token cliente.
- Nel codice convivono 4 "primari" storici nelle tile da unificare: `#6366F1`, `#1e87f0`,
  `#e8622a`, `#e1474f`. (Nelle TILE vanno a token; nel CHROME l'arancio resta.)
- Vincolo costante: **chiavi salvate INVARIATE** (margin_*, padding_*, border_radius, hover.*…).

## Già consegnato (controlli inspector)
- `FieldBox` (margine/padding/raggio/spessore) — implementato dall'utente nel repo.
- `StyleBoxStack` (pannello "Spazi & Bordi" con switch device globale) — implementato.
- `FieldBorder` ridisegnato coerente (reference + Vue).
- `FieldBoxShadow` ridisegnato coerente: contratto VERIFICATO sul repo (drop-in di
  `src/components/Builder/fields/FieldBoxShadow.vue`). Chiavi reali invariate: `style.shadow`
  (preset none/sm/md/lg/xl/custom, reso dallo StyleEffectsStack) + `style.shadow_custom` =
  `{h,v,blur,spread,color,inset}` (questo componente), hover su `style.hover.shadow*`, colore via FieldColor.
  Deliverable: `design_handoff_olobuild_tiles/REFERENCE_shadow-control.html` + `prototype/FieldBoxShadow.vue`.
  Da implementare lato repo: rimpiazzare il .vue (zero migrazione) + scala segmented nello StyleEffectsStack.

## Possibili prossimi passi
- Altri redesign di categoria tile (Media: Gallery/Carousel/Chart; Layout: Hero/Grid/CtaBanner).
- Verifiche sui diff prodotti da Claude Code (l'utente può incollare screenshot).

## Come riprendere
Leggi questo file + `design_handoff_olobuild_tiles/START_HERE.md`. Per il codice reale usa
i GitHub tools sul repo `claudiovinco/olobuild` (branch `main`).

---

# Progetto parallelo: OLOthemes (50 theme front-end)
Strand separato dal lavoro tile/inspector qui sopra. **Stato: completo** — 50 theme live in
`OLOthemes - Gallery.html`, ognuno home + pagina interna, + 36 "zone interattive" su misura
(Mixer/Builder/Finder/Projector) nel motore condiviso `olothemes/fx.js`.
**Per riprendere OLOthemes leggi `olothemes/OLOTHEMES_MEMORY.md`** (convenzioni, API delle zone,
bug già corretti, elenco theme/zone, workflow di verifica).

