# HANDOFF — Tile speciali OLObuild (per Claude Code)

Pacchetto di consegna per implementare/estendere i **tile speciali** dei 50 temi dimostrativi OLOtheme,
in vista della conversione in template OLObuild reali.

> **Dove aprire Claude Code:** sul **repo del plugin OLObuild** (quello con `src/config/elements/`,
> `Olo_Tile_Base`, gli helper). Copia *questa cartella* dentro il repo (es. in `_olotheme-handoff/`)
> e usala come materiale di specifica/riferimento.

---

## Contenuto del pacchetto

```
handoff-tile-speciali/
├── HANDOFF.md                          ← questo file (prompt + istruzioni)
├── spec/
│   ├── PER-CLAUDE-CODE-tile-speciali.md   ← SPEC PRINCIPALE: classificazione + schede + checklist
│   ├── PER-CLAUDE-CODE-tile-mancanti.md   ← lacune pagina di lancio (companion)
│   ├── OLOBUILD-SCHEDA.md                  ← architettura/vocabolario del builder
│   └── tile-speciali/                      ← 9 screenshot di riferimento
└── temi/
    ├── shared/  +  image-slot.js           ← runtime condiviso (reveal/counter/inspector)
    └── NN-tema-*.html                       ← i temi con effetti da implementare (runtime in fondo al file)
```

Ogni tema HTML contiene, in fondo, lo **snippet runtime già funzionante** dell'effetto (con fallback):
è il punto di partenza da portare nel runner del tile, **non riscrivere a memoria**.

---

## Come lavorare (riassunto)

1. Leggi `spec/PER-CLAUDE-CODE-tile-speciali.md` (spec) e `spec/OLOBUILD-SCHEDA.md` (architettura).
2. Studia **un tile esistente del repo come modello** (consigliato: il **Marquee** → `marquee.js` config +
   `Olo_Marquee_Tile`), per replicarne esattamente stile/struttura.
3. Lavora **un tile alla volta** (config JS + classe PHP + runtime), poi STOP per review.
4. Applica sempre il **contratto** (spec §2) e chiudi con la **checklist** (spec §6).

Ordine consigliato (max riuso, min rischio):
1. **Bucket B** (1 opzione su tile maturi): VelocitySkew → BlendText-Spotlight → Viewer360-object →
   HiddenPop-keyseq → SpotlightFX → preset Parallax (ScrollAssembly/VinylSpin/FillFX).
2. **Bucket C “economici”** (CSS, poco JS): StackScroll → CRTOverlay → GooBackground/Aurora.
3. **Bucket C “ingegneristici”**: ScrollScrub → PhysicsBin → ScratchFX → ParticleFX → ASCIIViz →
   VariableSpecimen → PresenceGrid → Leaderboard.

---

## PROMPT 1 — master (incolla a inizio sessione)

```
Sei uno sviluppatore senior del plugin WordPress "OLObuild" (Vue 3 + Pinia + Vite, backend PHP,
tile auto-discovered). Lavoriamo per convertire 50 temi dimostrativi HTML in template OLObuild reali.
Prima dobbiamo implementare/estendere i "tile speciali" che realizzano gli effetti wow.

CONTESTO (leggi prima di toccare codice):
- _olotheme-handoff/spec/PER-CLAUDE-CODE-tile-speciali.md  ← spec + classificazione + schede + checklist
- _olotheme-handoff/spec/OLOBUILD-SCHEDA.md                ← architettura
- studia un tile esistente come modello: src/config/elements/marquee.js + la classe Olo_Marquee_Tile
  (replica ESATTAMENTE questo stile: naming, struttura config, render).

REGOLE NON NEGOZIABILI (il "contratto", §2 della spec):
- ogni valore = campo editor con default + responsive 6 breakpoint; niente hardcode
- CSS/markup/id-di-filtri-e-canvas scoped sull'UID istanza (olo-<name>-<id>); N istanze sulla stessa
  pagina non devono interferire
- render PHP = stato base già visibile (SSR); il JS arricchisce ed è idempotente
- prefers-reduced-motion, touch/pointer (effetti cursore off su hover:none), fallback no-JS
- riusa gli helper esistenti (Olo_Tile_Utils, Olo_Text_Effects, motore animazioni/parallax);
  NON reimplementare glitch/scramble/parallax: esistono già
- additivo: non rompere chiavi salvate né tile esistenti

MODO DI LAVORARE:
- un tile alla volta, end-to-end (config JS + classe PHP + runtime), poi STOP per review
- lo snippet runtime di riferimento è in fondo al file _olotheme-handoff/temi/NN-tema-*.html indicato
  nella scheda: portalo nel runner, non riscriverlo a memoria
- alla fine di ogni tile, compila la checklist §6 della spec

Conferma di aver letto la spec e lo studio del Marquee, poi aspetta che ti dia il PRIMO tile.
```

## PROMPT 2 — per-tile (template)

```
Implementa il tile «<NOME>» — scheda nella spec (§4, famiglia <X>), bucket <A/B/C>.
Reference runtime: temi/<NN-tema-xxx.html>. Screenshot: spec/tile-speciali/<file>.png.
Segui i campi editor elencati nella scheda. Quando hai finito, mostrami i file creati/modificati
e la checklist §6 compilata. Non passare al tile successivo.
```

## PROMPT 3 — primo tile consigliato (rischio minimo, già compilato)

```
Implementa «Marquee · VelocitySkew» — spec §4 famiglia B, bucket B (estende Marquee).
Reference: temi/64-tema-pastificio.html (blocco "velocity-skew marquee").
Aggiungi SOLO i campi baseSpeed, scrollBoost, maxSkew, damping al config del Marquee esistente e il
ramo di runtime corrispondente; direzione/pausa-hover/mask restano quelli attuali. reduced-motion →
skew 0. Mostrami il diff e la checklist §6.
```

---

## Mappa effetto → tema di riferimento (per i prompt per-tile)

| Tile | Bucket | Tema reference | Screenshot |
|---|:--:|---|---|
| Marquee · VelocitySkew | B | 64-tema-pastificio.html | 64-stack-cards.png |
| BlendText · Spotlight | B | 63-tema-risograph.html | 63-risograph-flashlight.png |
| Viewer360 · object-spin | B | 65-tema-ceramica.html | 65-ceramica-360.png |
| HiddenPop · key-sequence (Konami) | B | 60-tema-community-gamer.html | 60-gamer-glitch.png |
| SpotlightFX | B | 43-tema-gioielleria.html | — |
| Parallax presets (ScrollAssembly/VinylSpin/FillFX) | B | 56-/59-/55-tema-*.html | — |
| Section · StackScroll | C | 64-tema-pastificio.html | 64-stack-cards.png |
| CRTOverlay | C | 60-tema-community-gamer.html | 60-gamer-glitch.png |
| GooBackground / Aurora | C | 61-tema-profumeria.html (45/48 blob) | 61-profumeria-goo.png |
| Section · ScrollScrub (pin orizz.) | C | 62-tema-libreria-indie.html (35) | 62-shelf-pinned.png |
| PhysicsBin | C | 69-tema-toy-store.html | 69-toy-physics.png |
| ScratchFX | C | 44-/45-tema-*.html | — |
| ParticleFX (preset) | C | 36/42/49/50-tema-*.html | — |
| ASCIIViz | C | 67-tema-radio-notturna.html | 67-radio-ascii-play.png |
| VariableSpecimen + TextPath spin | C/B | 66-tema-type-foundry.html | 66-type-morph.png |
| WaterDisplacement | C | 68-tema-terme-spa.html | — |
| MagneticCursor (feature tema) | C | 60-tema-community-gamer.html | 60-gamer-glitch.png |
| PresenceGrid | C | 60-tema-community-gamer.html | — |
| Leaderboard | C | 60-tema-community-gamer.html | — |

> La tabella completa con i 3 bucket e i tile A “già coperti” è in `spec/PER-CLAUDE-CODE-tile-speciali.md` §3.
