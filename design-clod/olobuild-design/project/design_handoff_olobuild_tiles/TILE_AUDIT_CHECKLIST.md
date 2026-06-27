# Tile Audit Checklist — processo ripetibile su TUTTE le 240 tile

Claude Code deve passare ogni `*Tile.vue` attraverso questa checklist, applicando
`DESIGN_LANGUAGE.md`. Obiettivo: belle **e** coerenti come famiglia, a chiavi salvate
invariate.

## Per OGNI tile — checklist (10 punti)

```
Tile: <Nome>Tile.vue
[ ] 1. COLORE   — nessun hex hardcoded; usa resolveColor + token (GLOBAL/SYSTEM)
[ ] 2. SPAZI    — padding/gap dalla scala SPACE (no valori arbitrari)
[ ] 3. RAGGIO   — un solo raggio dalla scala RADIUS, coerente
[ ] 4. OMBRA    — livello dalla scala (none/sm/md/lg); default sensato
[ ] 5. TYPE     — ruoli e dimensioni dalla scala; line-height/peso corretti
[ ] 6. ICONE    — SVG del set, mai emoji; dimensione/stroke coerenti
[ ] 7. STATI    — hover discreto + focus-visible + transizione da token
[ ] 8. MEDIA    — aspect-ratio + object-fit; placeholder elegante
[ ] 9. DEFAULT  — contenuto/colore di default curato e realistico ("bello appena inserito")
[ ] 10. A11Y    — elemento semantico, contrasto AA, aria-label, hit-area ≥44px
+ box-model via useBoxModel; default da fonte unica (buildDefaults)
+ NON cambiare le chiavi salvate
```

Per ogni punto non conforme: annota severità (🔴 rompe coerenza/brand · 🟡 estetica · 🟢 nice-to-have)
e applica il fix minimo. Output per tile: diff + nota di 1 riga sul "prima/dopo".

## Ordine di lavorazione per categoria (batch coerenti)

Lavorare per categoria fa emergere e correggere le incoerenze *tra* tile sorelle.

| Categoria | Tile (esempi) | Focus design |
|---|---|---|
| **Testo** | Headline, Animatedheading, Blendtext, Content, DescList, Breadcrumbs, Code, Html | scala tipografica, ritmo, wrap |
| **Azioni** | Button, CtaBanner, Darkmode | colori token, stati hover/focus, a11y semantica |
| **Feedback** | Alert | semantici via token, icone SVG, soft derivata |
| **Card / Feature** | Iconbox, Hostcard, Authorbox, Flipcard, Hotspot, Floatingpanel | raggio+ombra coerenti, padding, gerarchia |
| **Dati / Numeri** | Counter, Countercircle, Countdown, Chart, Calendar, EventList | allineamento numerico, colori serie da token |
| **Media** | Gallery, Carousel, Audio, Facebookpage | aspect-ratio, object-fit, placeholder |
| **Layout** | Hero, HeroSplit, Grid, Column, Divider, Fragment | spaziatura, max-width, allineamento contenuti |
| **Interattive** | Form, Accordion, Icontabs, Booking, BookingPicker, AppointmentGrid, Hiddenpop | stati input, focus, struttura, a11y |

(Le restanti tile ricadono per analogia in una di queste categorie.)

## Definition of Done per categoria
- Tutte le tile della categoria condividono scala spazi, raggio, lingua d'ombra, stile icone, stati.
- Inserite "vuote" appaiono già piacevoli e riconoscibilmente della stessa famiglia.
- Nessun hex hardcoded; box-model via composable; default da fonte unica; chiavi salvate intatte.
- Pass a11y (semantica, contrasto, focus, aria, hit-area).

## Riferimenti
- `DESIGN_LANGUAGE.md` — le regole
- `oloTileDefaults.js` — token (GLOBAL/SYSTEM), SPACE/RADIUS, resolveColor, contrastOn, TILE_DEFAULTS
- `useBoxModel.js` — radius/padding/margin
- `TOKEN_MAPPING.md` — come i colori si legano ai globali del cliente
- `BUTTON_EXAMPLE.md` — esempio completo di refactor di una tile
