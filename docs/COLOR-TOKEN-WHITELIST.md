# Campagna hex → token: classificazione e whitelist

> v1.4.389 (2026-08-28). Esito del censimento contestuale degli hex "nudi" nei render
> tile (PHP `includes/tiles/` + Vue `src/components/Tiles/`), strumento:
> `scratchpad/scan-colors.cjs` + `ctx-colors.cjs` (riusabili).

## Cosa è stato tokenizzato (1.4.389)

**Gruppo D — CSS dei preset nelle preview Vue** (~16 occorrenze in 5 file):
`#e1474f` nudo e derivati arancio storico (`rgba(232,98,42,…)`, `#fdf2ec`, `#f07a80`)
negli stati attivi/hover dei preset di AccordionTile, PopupTile, SwitcherPanelTile,
PostMetaTile, SitemapTile → `var(--olo-color-primary, #e1474f)` e
`color-mix(in srgb, var(--olo-color-primary, #e1474f) N%, transparent|white)`.
Motivo: il PHP frontend segue i token, la preview Vue no → divergenza di resa
quando il cliente cambia primario. Fallback identici ⇒ resa invariata a default.

## Whitelist — NON tokenizzare (casi intenzionali o impossibili)

1. **Fallback firma nelle tile a tema** — `var(--olo-color-primary, #firma)` con
   fallback ≠ #e1474f (77 occ.: masthead #cf2e2e, audiohero #27e0a3, glowhero
   #f4a23b, OLOX #c6f24e/#c8ff3c, …). Il token del cliente vince SEMPRE quando
   definito; il fallback è il colore di design della tile nei contesti token-less
   (thumbnail, anteprime standalone). Intenzionale: non normalizzare.
2. **Commenti** — "(era #e1474f off-brand)" e simili: documentazione della
   campagna TOKEN-FIRST già fatta, non codice.
3. **Default dei settings colore** (accordion, switcher, chart items, hostcard…):
   cambiare il default cambia le tile nuove — decisione da prendere tile per tile
   in una sessione dedicata, non in batch. (Molte tile moderne usano già `''` ⇒
   token via resolveColor: è quello il pattern di arrivo.)
4. **Runtime non-CSS** — `var()` NON risolve dentro: canvas 2D `fillStyle`
   (chart, smear), SVG in data-URI (marker mappa `safe_hex(…, '#e1474f')`),
   `wp_json_encode` verso librerie JS. Il fallback hex resta necessario.
5. **Dati demo/contenuto** — palette confetti (particlefx), item demo dei chart,
   swatch demo di studiohero ('Primario' #e1474f), value di input color nei form.
6. **Chrome del builder** (`src/components/Builder/*`): usa `--olo-ui-accent`
   (#e8622a) e i propri neutri — MAI i token cliente `--olo-color-*`
   (vedi memoria: chrome accent locale).

## Residuo aperto (P2, sessioni dedicate)

- Default settings → `''` token-first (punto 3), tile per tile con migrazione elastica.
- Hex neutri di libreria nei .vue (grigi tailwind-like #0f172a/#475569/… nei preset):
  valutare mappatura sui token neutri del tema, gruppo per gruppo.
- I conteggi grezzi (~2.300 "nudi") includono tutte le voci di whitelist sopra:
  il numero azionabile reale è molto più piccolo — ricalcolare con gli scanner
  dopo ogni batch.
