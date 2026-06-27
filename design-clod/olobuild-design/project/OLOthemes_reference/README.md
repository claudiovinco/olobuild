# OLOthemes — 50 temi di riferimento (per Claude Code)

Bundle **auto-navigabile offline**: 50 temi front-end originali (home + 1 pagina interna),
pensati come **reference visivo e di codice** per il lavoro sui template/tile di OLObuild.

## Come sfogliarli
Apri **`OLOthemes - Gallery.html`** in un browser (doppio click). È l'indice: 50 card con
filtri per Topic / Type / Zones. Da lì clicchi un tema → home → link alle pagine interne.
Tutto funziona in locale: i link sono relativi, nessun server necessario.

## Struttura
```
OLOthemes - Gallery.html        ← INDICE (entry point)
OLOtheme - <Nome> (<Categoria>).html   ← home di ogni tema (50)
OLOtheme - <Nome> - <Pagina>.html      ← pagina interna di ogni tema
olothemes/                      ← un CSS per tema (es. forge.css) + motore condiviso
   <tema>.css                   ← design system del tema (token, tipo, componenti)
   fx.js                        ← layer interazioni condiviso (reveal + zone interattive)
   theme.js                     ← nav/mobile/reveal di base
themes/                         ← gallery.css + gallery.js (indice)
previews/                       ← anteprime jpg usate dalle card della gallery
```

## A cosa serve come reference
- **Lingua visiva di qualità**: ogni tema è una "famiglia" coerente (palette token, scala
  tipografica, spaziatura, raggio, ombra, componenti). Utile come metro per portare le tile
  e i template OLObuild allo stesso livello.
- **Pattern di sezione** riusabili (hero, feature-split, card-grid, stats, pricing, CTA, footer)
  e **zone interattive** (Finder, Builder, Mixer, Projector + StepSequencer, PaletteHarmony,
  ContrastChecker, RecipeScaler, AvailabilityHeat, FloorPlanPicker, LookbookMixer, TypeTester,
  RouteScrubber, TimezonePlanner, BakersCalc, SpinViewer) — 16 pattern, motore in `olothemes/fx.js`.
- **CSS leggibile per tema**: in `olothemes/<tema>.css` trovi token e componenti commentati.

## Nota per "guarda qui" (uso con Claude Code)
Claude Code lavora sul repo `claudiovinco/olobuild` e NON vede il progetto di design.
Per dargli una destinazione stabile da consultare: metti questa cartella accanto al pacchetto
regole, es. `D:\TECNICA\olobuild\regoletiles1\OLOthemes_reference\`, e nel prompt indica:
"reference visivo dei temi: apri `regoletiles1/OLOthemes_reference/OLOthemes - Gallery.html`".
NB: è SOLO reference d'ispirazione/qualità — i temi sono HTML/CSS statici, non tile OLObuild;
le chiavi salvate e le regole restano quelle di `regoletiles1/` (token-first, useBoxModel, ecc.).
