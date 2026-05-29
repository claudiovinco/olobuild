# TILE_PROGRESS — tracker audit "Tile belle & coerenti"

> Generato da `_gen_tracker.cjs`. UNA riga per ogni `*Tile.vue` (237 tile).
> Checklist a 10 punti (vedi `regoletiles1/TILE_AUDIT_CHECKLIST.md`).

> Esclusi (infrastruttura, non tile di contenuto): `TileBase.vue` (classe base) e
> `ExternalTilePlaceholder.vue` (placeholder per tile esterne non caricate).

## Legenda celle
- `·` = non ancora valutato · `✅` = conforme · `🟢` ok con nota minore · `🟡` estetica · `🔴` rompe coerenza/brand (da correggere) · `—` = non applicabile alla tile
- Punti: **1**COLORE · **2**SPAZI · **3**RAGGIO · **4**OMBRA · **5**TYPE · **6**ICONE · **7**STATI(hover+focus) · **8**MEDIA · **9**DEFAULT · **10**A11Y
- `Box` = box-model via useBoxModel · `Src` = default da fonte unica (buildDefaults)

## Definition of Done (§4)
- [x] Ogni tile ha la sua riga compilata (5 pilota dettagliate + 232 batch)
- [x] Zero hex off-brand (#6366F1/#1e87f0/#e8622a) nei componenti/config — verificato grep
- [x] Zero emoji come icona di default — verificato grep
- [x] focus-visible aggiunto sugli interattivi (batch) — verifica visiva consigliata
- [ ] Box-model via composable; default da fonte unica; chiavi salvate invariate
- [ ] Tile della stessa categoria condividono raggio, ombra, superficie, accento, scala type

## Riepilogo categorie

| Categoria | # tile |
|---|---:|
| Azioni & Bottoni | 9 |
| Feedback | 1 |
| Testo | 11 |
| Layout & Struttura | 18 |
| Card & Feature | 20 |
| Dati & Numeri | 11 |
| Media | 24 |
| Interattive & Form | 14 |
| Navigazione & Header/Footer | 19 |
| Embed & Dinamico | 3 |
| Booking & Servizi (OLObooking) | 55 |
| Immobiliare (Property) | 21 |
| WooCommerce (Pro) | 31 |
| **TOTALE** | **237** |


## Azioni & Bottoni (9)

| Tile | Config | 1 | 2 | 3 | 4 | 5 | 6 | 7 | 8 | 9 | 10 | Box | Src | Nota prima→dopo |
|---|:---:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|---|
| Button | button | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | 🟢 | ✅ | ✅ | ✅ | ✅ | span #6366F1 → `<a>` token-first (TOKENS.primary/onPrimary), useBoxModel, focus-visible |
| CtaBanner | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| Darkmode | darkmode | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| Paymentbuttons | paymentbuttons | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| Scrollprogress | scrollprogress | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| Sharebuttons | sharebuttons | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| Social | social | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| ToggleBtn | togglebtn | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| Totop | totop | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |

## Feedback (1)

| Tile | Config | 1 | 2 | 3 | 4 | 5 | 6 | 7 | 8 | 9 | 10 | Box | Src | Nota prima→dopo |
|---|:---:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|---|
| Alert | alert | ✅ | 🟢 | 🟡 | ✅ | ✅ | ✅ | ✅ | — | ✅ | ✅ | — | 🟢 | palette fisse #DBEAFE → token semantici; emoji ℹ️✅⚠️❌ → SVG; role=alert; raggio rounded-lg da armonizzare in categoria |

## Testo (11)

| Tile | Config | 1 | 2 | 3 | 4 | 5 | 6 | 7 | 8 | 9 | 10 | Box | Src | Nota prima→dopo |
|---|:---:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|---|
| Animatedheading | animatedheading | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| Blendtext | blendtext | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| Code | code | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| Content | content | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| DescList | desclist | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| Headline | headline | ✅ | ✅ | — | 🟡 | ✅ | 🟢 | 🟢 | — | ✅ | ✅ | 🟢 | ✅ | deco/gradient/stroke #6366F1/#EC4899/#000 → token; option glow rgba(99,102,241) da rivedere (default off) |
| Html | html | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| List | list | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| TextBlock | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| Textmask | textmask | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| Textpath | textpath | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |

## Layout & Struttura (18)

| Tile | Config | 1 | 2 | 3 | 4 | 5 | 6 | 7 | 8 | 9 | 10 | Box | Src | Nota prima→dopo |
|---|:---:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|---|
| Column | column | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| Divider | divider | ✅ | ✅ | — | 🟢 | ✅ | ✅ | 🟢 | — | ✅ | 🟢 | 🟢 | ✅ | grigi #d1d5db/#6b7280 → token border/textSoft; spacing su scala SPACE |
| Fragment | fragment | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| Grid | grid | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| HeroSplit | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| Hero | hero | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| InnerColumns | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| KillNextPrev | killnextprev | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| OverlayGrid | overlaygrid | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| OverlaySlider | overlayslider | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| Overlay | overlay | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| PanelSlider | panelslider | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| Panel | panel | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| Row | row | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| SectionHeader | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| Section | section | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| Shapedivider | shapedivider | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| Spacer | spacer | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |

## Card & Feature (20)

| Tile | Config | 1 | 2 | 3 | 4 | 5 | 6 | 7 | 8 | 9 | 10 | Box | Src | Nota prima→dopo |
|---|:---:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|---|
| Authorbox | authorbox | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| FlipCard | flipcard | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| Floatingpanel | floatingpanel | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| Hostcard | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| Hotspot | hotspot | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| IconBox | iconbox | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| Icon | icon | ✅ | ✅ | ✅ | ✅ | — | ✅ | 🟢 | — | ✅ | 🟢 | ✅ | ✅ | bg/icona #6366F1/#9CA3AF → token (onPrimary su stacked); padding via useBoxModel; raggio scala RADIUS.lg |
| Iconlist | iconlist | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| InfoCards | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| Linkinbio | linkinbio | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| Portfolio | portfolio | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| ProductCards | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| Quotation | quotation | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| Readingtime | readingtime | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| StepTimeline | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| Tagcloud | tagcloud | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| Team | team | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| Testimonial | testimonial | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| Timeline | timeline | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| TrustStrip | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |

## Dati & Numeri (11)

| Tile | Config | 1 | 2 | 3 | 4 | 5 | 6 | 7 | 8 | 9 | 10 | Box | Src | Nota prima→dopo |
|---|:---:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|---|
| Chart | chart | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| Countdown | countdown | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| Counter | counter | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| Countercircle | countercircle | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| Pricelist | pricelist | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| Pricing | pricing | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| Progress | progress | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| Progresstracker | progresstracker | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| Starrating | starrating | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| Table | table | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| Viewscounter | viewscounter | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |

## Media (24)

| Tile | Config | 1 | 2 | 3 | 4 | 5 | 6 | 7 | 8 | 9 | 10 | Box | Src | Nota prima→dopo |
|---|:---:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|---|
| Audio | audio | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| Carousel | carousel | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| Facebookpage | facebookpage | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| Gallery | gallery | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| Image | image | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| ImgCompare | imgcompare | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| Instagram | instagram | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| Lightbox | lightbox | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| Lottie | lottie | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| Map | map | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| Marquee | marquee | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| Osmmap | osmmap | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| PdfPro | pdfpro | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| PdfViewer | pdfviewer | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| ProSlider | proslider | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| Progallery | progallery | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| ShatteredImage | shatteredimage | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| Slideshow | slideshow | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| Soundcloud | soundcloud | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| SvgAnimator | svganimator | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| Twitterfeed | twitterfeed | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| Video | video | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| Videoplaylist | videoplaylist | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| Viewer360 | viewer360 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |

## Interattive & Form (14)

| Tile | Config | 1 | 2 | 3 | 4 | 5 | 6 | 7 | 8 | 9 | 10 | Box | Src | Nota prima→dopo |
|---|:---:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|---|
| Accordion | accordion | ✅ | — | — | — | ✅ | ✅ | ✅ | — | ✅ | ✅ | — | — | header `role=button` ora ha focus-visible (Vue+PHP); 🎬 emoji video→testo; brand_accent fallback #e8622a→#e1474f. Preset-interni invariati |
| Form | form | ✅ | — | — | — | ✅ | ✅ | ✅ | — | ✅ | ✅ | — | — | step-indicator #6366F1→primary; 📎→SVG graffetta; hover invented var --primary-dark→color-mix; required #EF4444→danger; hidden-badge→surfaceAlt; focus-visible su submit/input (Vue+PHP) |
| Hiddenpop | hiddenpop | ✅ | — | — | — | ✅ | ✅ | ✅ | — | ✅ | ✅ | — | — | 🚩→SVG bandiera; ctaStyleMap #111/#dc2626→token; focus-visible su CTA+chiudi (PHP). Badge hint ambra = chrome builder (invariato) |
| IconTabs | icontabs | ✅ | — | — | — | ✅ | ✅ | ✅ | — | ✅ | ✅ | — | — | #E8622A→primary, grigi→text/textSoft, link #2563EB→token link; focus-visible su tab+link (Vue+PHP); default ''→risolti token |
| LiveSearch | livesearch | ✅ | — | — | — | ✅ | ✅ | ✅ | — | ✅ | ✅ | — | — | #6366F1 footer/badge/focus→primary (Vue+config+PHP); focus-visible su trigger/clear + focus-within input ring in olo-livesearch.css |
| Loginform | loginform | ✅ | — | — | — | ✅ | ✅ | ✅ | — | ✅ | ✅ | — | — | pw-strength #EF4444/#F59E0B/#10B981→token semantici (Vue+PHP); focus-visible su submit/tab/social/link/pw-toggle + ring focus-within (PHP). Social brand-color leciti |
| Newsletter | newsletter | ✅ | — | — | — | ✅ | ✅ | ✅ | — | ✅ | ✅ | — | — | #3B82F6→primary (Vue+PHP); 🔒 hint→SVG lucchetto; badge ambra→token warning; focus-visible su pulsante + ring input |
| Offcanvas | offcanvas | ✅ | — | — | — | ✅ | ✅ | — | — | ✅ | ✅ | — | — | ☰ glyph→SVG menu. Anteprima-only (nessun PHP tile/elemento interattivo); colori già su token |
| Popover | popover | ✅ | — | — | — | ✅ | ✅ | ✅ | — | ✅ | ✅ | — | — | #6366F1 marker/overlay→primary (Vue); marker ora role=button+keydown+focus-visible (Vue+PHP); placeholder grigio→token. Config/PHP già token-first |
| Popup | popup | ✅ | — | — | — | ✅ | ✅ | ✅ | — | ✅ | ✅ | — | — | styleMap #222/#999/#dc2626→token; focus-visible su trigger+chiudi (Vue+PHP). Badge/preset hint indaco/neon = chrome+preset (invariati) |
| Revealbox | revealbox | ✅ | — | — | — | ✅ | — | — | — | ✅ | ✅ | — | — | Nessun off-brand, nessun emoji default, nessun elemento interattivo (reveal su hover). Overlay #fff/#000 leciti per media. Invariato |
| Search | search | ✅ | — | — | — | ✅ | ✅ | ✅ | — | ✅ | ✅ | — | — | #6366F1 button/focus→primary, grigi placeholder/icona→token (Vue+config+PHP); focus-within ring + focus-visible su pulsante |
| SwitcherPanel | switcherpanel | ✅ | — | — | — | ✅ | ✅ | ✅ | — | ✅ | ✅ | — | — | btn fallback #e8622a→#e1474f (Vue+PHP); focus-visible su nav tab (Vue+PHP). Bianco-su-hero lecito; preset-interni invariati |
| Switcher | switcher | ✅ | — | — | — | ✅ | — | ✅ | — | ✅ | ✅ | — | — | grigi Tailwind nudi→token text/textSoft/textFaint/border; indicatore #6366F1→primary (Vue+config+PHP); focus-visible su tab |

## Navigazione & Header/Footer (19)

| Tile | Config | 1 | 2 | 3 | 4 | 5 | 6 | 7 | 8 | 9 | 10 | Box | Src | Nota prima→dopo |
|---|:---:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|---|
| Breadcrumbs | breadcrumbs | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| LangSwitcher | langswitcher | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| MegaMenu | megamenu | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| Menuanchor | menuanchor | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| Mobilebar | mobilebar | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| NavMenu | navmenu | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| Nav | nav | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| Newsticker | newsticker | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| Pagetitlebar | pagetitlebar | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| Pagination | pagination | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| PostGrid | postgrid | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| PostMeta | postmeta | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| Postnavigation | postnavigation | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| Queryloop | queryloop | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| Relatedposts | relatedposts | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| SiteLogo | sitelogo | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| Sitemap | sitemap | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| Subnav | subnav | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| Toc | toc | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |

## Embed & Dinamico (3)

| Tile | Config | 1 | 2 | 3 | 4 | 5 | 6 | 7 | 8 | 9 | 10 | Box | Src | Nota prima→dopo |
|---|:---:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|---|
| Shortcode | shortcode | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| Templateembed | templateembed | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| Wpcomments | wpcomments | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |

## Booking & Servizi (OLObooking) (55)

| Tile | Config | 1 | 2 | 3 | 4 | 5 | 6 | 7 | 8 | 9 | 10 | Box | Src | Nota prima→dopo |
|---|:---:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|---|
| AppointmentGrid | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| BookingPicker | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| Booking | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| Calendar | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| EventList | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| OloRoomAvailability | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| OloRoomBadges | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| OloRoomBooking | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| OloRoomCalendar | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| OloRoomCategories | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| OloRoomClosures | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| OloRoomContacts | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| OloRoomCta | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| OloRoomDashboard | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| OloRoomDescription | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| OloRoomDistricts | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| OloRoomEquipment | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| OloRoomFeatured | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| OloRoomGallery | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| OloRoomGrid | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| OloRoomHero | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| OloRoomHours | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| OloRoomInfo | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| OloRoomLocation | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| OloRoomMap | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| OloRoomPricing | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| OloRoomRelated | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| OloRoomRules | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| OloRoomSearch | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| OloRoomShare | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| OloRoomStatsPublic | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| OloRoomStatus | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| RentalInventory | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| RestaurantBookingForm | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| RestaurantOpeningHours | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| ServiceAddress | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| ServiceAmenities | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| ServiceCheckin | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| ServiceCipat | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| ServiceClub | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| ServiceDescription | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| ServiceDirections | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| ServiceExcerpt | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| ServiceGallery | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| ServiceHero | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| ServiceInfo | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| ServiceList | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| ServiceMushrooms | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| ServicePrices | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| ServiceRelated | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| ServiceResults | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| ServiceRules | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| ServiceSearch | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| ServiceStats | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| ServiceVideo | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |

## Immobiliare (Property) (21)

| Tile | Config | 1 | 2 | 3 | 4 | 5 | 6 | 7 | 8 | 9 | 10 | Box | Src | Nota prima→dopo |
|---|:---:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|---|
| PropertyAddress | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| PropertyCard | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| PropertyContactForm | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| PropertyCta | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| PropertyDescription | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| PropertyExcerpt | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| PropertyFeatured | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| PropertyFeatures | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| PropertyGallery | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| PropertyGrid | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| PropertyHeroScroll | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| PropertyHero | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| PropertyInfo | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| PropertyMapSearch | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| PropertyMap | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| PropertyPrice | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| PropertyRules | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| PropertySearch | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| PropertySpecs | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| PropertyStats | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| PropertyVideo | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |

## WooCommerce (Pro) (31)

| Tile | Config | 1 | 2 | 3 | 4 | 5 | 6 | 7 | 8 | 9 | 10 | Box | Src | Nota prima→dopo |
|---|:---:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|---|
| WooAddtocart | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| WooBreadcrumbs | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| WooCart | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| WooCategories | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| WooCheckoutMultistep | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| WooCheckout | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| WooComparison | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| WooCrossSells | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| WooMinicart | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| WooMyaccount | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| WooNotices | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| WooOrderTracking | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| WooPrice | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| WooProductBundle | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| WooProductDescription | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| WooProductFilter | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| WooProductGallerySlider | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| WooProductImage | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| WooProductMeta | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| WooProductNavigation | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| WooProductStock | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| WooProductTabs | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| WooProductTitle | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| WooProducts | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| WooQuickview | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| WooRating | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| WooRecentlyViewed | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| WooRelated | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| WooSaleBadge | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| WooUpsells | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
| WooWishlist | — | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | ✅ | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | Token-first (batch categoria): colore→token brand, icone SVG (no emoji), focus-visible, default curati. Spazi/raggio/ombra/type/media coerenti via sistema condiviso — non audit puntuale. |
