# Tile Design Language — regole di "bello & coerente" per TUTTE le tile

Questo è lo standard visivo che **ogni** `*Tile.vue` deve rispettare. Lo scopo è che le
240 tile sembrino **una sola famiglia**, non 240 micro-stili diversi. Claude Code deve
applicare queste regole a ogni tile (vedi `TILE_AUDIT_CHECKLIST.md` per il processo).

Fondamenta già pronte nel pacchetto: token (`oloTileDefaults.js` + `tokens-brand.css`),
normalizzazione box-model (`useBoxModel.js`), mappatura colori (`TOKEN_MAPPING.md`).

---

## 1. Colore — solo via token, mai hex grezzi
- **Primario/secondario/accento/testo** → ruoli GLOBALI del cliente (`resolveColor(userVal, GLOBAL.x)`).
- **Neutri e superfici** → `SYSTEM` (surface, surface-alt, border, text-soft).
- **Semantici** (info/success/warning/error) → ruoli globali; soft derivata via color-mix.
- **on-primary** → `contrastOn()` (mai testo bianco fisso su primari chiari).
- ❌ Nessun `#6366F1`, `#DBEAFE`, `#d1d5db`… hardcoded nei componenti.

> **Chrome del builder ≠ contenuto delle tile.** I controlli dell'inspector
> (slider, link, device switch, toggle) usano l'accento CHROME del prodotto OLObuild —
> **arancio `#e8622a` + navy**, fisso, identità del builder. Il colore *delle tile* (e dei
> loro contenuti, incl. il colore di un bordo) usa invece i token del cliente. Quindi:
> `#e8622a` nel chrome dell'editor è CORRETTO; `#e8622a` come colore di una tile è da
> sostituire con un token. Non "correggere" il chrome arancio dell'inspector.

## 2. Spaziatura — scala 8pt condivisa (`SPACE`)
- Padding interni per "taglia" di tile: compatta `12`, standard `16–24`, ariosa `32–48`.
- Gap tra elementi figli sempre dalla scala (`8/12/16`), mai valori arbitrari (`13px`, `7px`).
- Ritmo verticale coerente: stessa distanza titolo→corpo→azione tra tile sorelle.

## 3. Raggio — scala `RADIUS`, UNO per tile
- `sm 6` (chip/badge) · `md 10` (default tile/bottoni/card) · `lg 14` (contenitori grandi) · `pill`.
- Una tile usa **un** raggio coerente su tutti i suoi angoli/elementi, non 6 px qua e 8 là.

## 4. Elevazione — 4 livelli, uso disciplinato
- `none` statico a filo · `sm` card/bottoni a riposo · `md` hover/elementi sollevati · `lg` overlay/popover.
- Ombre **morbide e a bassa opacità** (no nero pieno). Default interattivo = `sm`, hover → `md`.

## 5. Tipografia — dalla scala globale, ruoli chiari
- Title / subtitle / body / caption con dimensioni dalla scala (non `font-size` random).
- Line-height: titoli `1.1–1.2`, corpo `1.5–1.6`. Peso titoli `600–700`, corpo `400–500`.
- `text-wrap: balance` sui titoli, `pretty` sui paragrafi. Mai testo < 12px nel front-end.

## 6. Icone — set SVG, mai emoji
- Solo `iconsSvg`/set del progetto. Stroke uniforme, dimensione coerente (16/20/24).
- Tinta semantica via `currentColor`. ❌ `ℹ️ ✅ ⚠️ ❌` come default.

## 7. Stati — completi e coerenti
- **Hover** discreto (lift `translateY(-1px)` + ombra `md`, o tint), durata dal token transizione.
- **Focus-visible** SEMPRE: anello `0 0 0 3px color-mix(primary 30%)`. (a11y, non opzionale)
- **Disabled/loading** previsti dove ha senso. Transizioni uniformi (stesso easing/durata).

## 8. Immagini & media
- Aspect-ratio espliciti (16:9, 4:3, 1:1), `object-fit: cover`, mai stretch.
- Placeholder elegante (non bianco vuoto): superficie neutra + icona media tenue.

## 9. Contenuto di default — curato e realistico
- Default **belli appena inseriti**: copy reale e breve in italiano, colori dai token, immagini placeholder coerenti.
- ❌ niente "Lorem ipsum", niente emoji, niente colori a caso. Una tile inserita deve sembrare *progettata*.

## 10. Accessibilità — baseline non negoziabile
- Elementi semantici (`<a>`/`<button>`/heading corretti), non `<span>` cliccabili.
- Contrasto testo ≥ AA (usare `contrastOn`); `aria-label` su controlli icona; hit-area ≥ 44px.

---

## Coerenza tra tile (la parte che le fa sembrare "una famiglia")
Due tile della stessa categoria devono condividere: **stessa scala di padding/gap, stesso
raggio, stessa lingua d'ombra, stesso trattamento di titolo/sottotitolo, stesso stile icone,
stessi stati hover/focus.** Se la Iconbox e la Authorbox hanno card con raggi e ombre diversi,
è un difetto di coerenza anche se ognuna è "carina" da sola.
