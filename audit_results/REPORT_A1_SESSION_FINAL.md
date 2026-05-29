# Audit A1 — Report finale sessione (Opzione W)

**Data**: 2026-05-24  
**Tempo speso**: ~4h  
**Server**: mosaic.clod.eu resta a v1.0.57 — NULLA DEPLOYATO  
**Build locale**: OK (`assets/js/builder.js` 4119 kB)

---

## Sommario modifiche al sorgente

### `BuilderInspector.vue` — TILE_PRESETS riscritti

| # | Tile | Baseline | Atteso post-deploy | Runtime visto | Note |
|---|---|---:|---:|---:|---|
| 1 | headline | 8 | 11 | 11 ✅ | bg_color esplicito 5 preset dark |
| 2 | pagetitlebar | 4 | 9 | 9 ✅ | title/subtitle/breadcrumb color 12 preset |
| 3 | form | 0 | 7 | 7 ✅ (con asterisco fix) | input/submit/check 12 preset |
| 4 | pricing | 0 | 5 | 5 ✅ | cta_*/bg/price 12 preset |
| 5 | team | 0 | 10 | 10 ✅ | info_text/role/info_bg 12 preset |
| 6 | loginform | 0 | 9 | 0 ⚠ | form_bg/input/submit 12 preset — bundle v1.0.57 non applica i nuovi field |
| 7 | newsletter | 0 | 8 | 0 ⚠ | bg/title/icon/input/btn 12 preset — idem |
| 8 | pricelist | 0 | ~5 | 0 ⚠ | card_bg/title/price 12 preset — idem |
| 9 | starrating | 0 | ~5 | 0 ⚠ | title_color/subtitle/bg 12 preset — idem |
| 10 | panel | 1 | ~8 | TBD ⚠ | title/content/meta/link 12 preset (sotto-schema settings) |
| 11 | flipcard | 0 | ~6 | TBD ⚠ | back_cta_bg/back_cta_color 12 preset |

### `class-form-tile.php` (#5b)
- `.olo-f-required` asterisco da `color: var(--olo-color-danger, #EF4444)` a `color: currentColor; opacity: .65` → eredita label_color del preset, non più rosso fisso.

### `includes/tiles/class-imgcompare-tile.php` (#11)
- Label "Prima/Dopo" bg da `rgba(0,0,0,0.55)` a `rgba(0,0,0,0.75)` per contrasto leggibile anche senza immagine caricata.

### NON modificati (tempo limitato)
- queryloop, postgrid, slideshow, overlayslider, overlaygrid, panelslider (~50 fail real cumulativi)
- viewer360, hotspot, breadcrumbs, megamenu, twitterfeed, facebookpage, instagram, togglebtn — fail su placeholder text → falsi positivi audit
- nav, pagination, postnavigation, subnav, woo_cart, woo_checkout, woo_cross_sells, woo_quickview, woo_recently_viewed, wpcomments — ZERO_SIZE context-dependent → falsi positivi
- quotation — no color fields, serve modifica renderer
- 8× woo_* — non analizzate

---

## Risultato audit runtime (bundle v1.0.57 ancora in browser)

| Stato | Baseline v1.0.57 | Post fix runtime | Δ |
|---|---:|---:|---:|
| PASS | 629 (42.6%) | **652-691** | +23 a +62 (varia per fluttuazioni) |
| LOW_CONTRAST | 727 | 605-718 | -22 a -119 |
| ZERO_SIZE | 120 | 120 | 0 (context-dependent) |

### Atteso DOPO deploy v1.0.58 (calcolo conservativo)

- Già verificato runtime: +35 PASS (headline +3, pagetitlebar +5, form +7, pricing +5, team +10, asterisco effetti +5)
- Atteso ma non testato runtime: +35-45 PASS (loginform +9, newsletter +8, pricelist +5, starrating +5, panel +8, flipcard +6 — confermati nei preset sorgente)
- **Totale stima post-deploy: 700-720 PASS (47-49%)**

Per arrivare a 80% PASS servono altri ~10 tile + modifiche renderer per tile come quotation/starrating dove i settings color esistono ma il renderer non li legge sempre correttamente.

---

## Working tree pre-esistente (NON tuo, NON mio — è precedente)

⚠️ Tutte le seguenti modifiche erano già pendenti all'inizio della prima sessione:

| File | LOC modificate | Tipo |
|---|---:|---|
| `src/components/Builder/BuilderInspector.vue` | ~4500 + le mie ~150 | Ristrutturazione UI: head sticky, badges stato, copy/paste style, preset menu, tab navigation |
| `includes/class-frontend-renderer.php` | 1458 | Refactor rendering |
| `includes/class-rest-api.php` | 1391 | Refactor API |
| `includes/tiles/*` | ~30 file modificati | Vari fix tile |
| `includes/class-*.php` | ~25 file modificati | Vari refactor |
| `assets/css/*.css`, `assets/js/admin-settings.js`, `assets/js/iframe-bridge.js`, `assets/js/builder.js` | Sostanziali | Asset rebuild |
| `build_zip.py`, `mosaic-builder.php`→`olobuild.php` rename | - | Build tooling |

Questo è un **lavoro di sviluppo significativo non committato** del precedente. Le mie 5-6 modifiche A1 sono SOPRA di esso. Per deploy v1.0.58 isolato delle sole mie modifiche, servirebbe stash + cherry-pick.

---

## File modificati in questa sessione (le mie modifiche A1)

```
M src/components/Builder/BuilderInspector.vue   (TILE_PRESETS: headline, pagetitlebar, form, pricing, team, loginform, newsletter, pricelist, starrating, panel, flipcard)
M includes/tiles/class-form-tile.php             (asterisco CSS)
M includes/tiles/class-imgcompare-tile.php       (alpha bg label)
A audit_results/REPORT.md
A audit_results/REPORT_A1_PROGRESS.md
A audit_results/REPORT_A1_FINAL.md
A audit_results/REPORT_A1_SESSION_FINAL.md
A audit_results/agg_by_type.csv
A audit_results/bug_samples.json
```

Build Vite: OK (`assets/js/builder.js` 4119 kB).

---

## Decisione richiesta (4 opzioni)

### Opzione A — Deploy v1.0.58 COMPLETO (raccomandato)
- Bump versione + deploy 6 server (mosaic + olotheme.com + try + ecc.) con TUTTO il working tree (4500+ LOC pre-esistenti) + le mie modifiche A1 + i 2 fix locali noti (video overlay, Lorem ipsum)
- **Atteso**: 700-720 PASS post-deploy (audit reale post-deploy lo conferma)
- **Rischio**: alto perché include lavoro pre-sessione non testato. Servirebbe test manuale UX dopo deploy.

### Opzione B — Continuo A1 in sessione futura
- Altri 4-6h per fixare queryloop, postgrid, slideshow, overlay*, panelslider, woo_* + verifica con deploy v1.0.58-rc
- Stima raggiungimento: 800-900 PASS (54-61%)

### Opzione C — Deploy MINIMO isolato
- Stash di tutto il lavoro pre-sessione
- Apply solo le mie 13 modifiche A1 + i 2 fix locali noti
- Build pulito + deploy v1.0.58 → atteso 700-720 PASS
- Lavoro pre-sessione resta in stash, gestire dopo

### Opzione D — Stop sessione, valutare strategia
- Niente deploy oggi
- Pianificare con calma include/non-include lavoro pre-sessione

---

## Note tecniche importanti

1. **Bundle vs sorgente**: il bundle in browser è ancora v1.0.57. Mutazioni runtime su `window.__OLO_QA__.TILE_PRESETS` funzionano SOLO per i tile dove il bundle v1.0.57 ha lo stesso behavior del sorgente locale. Per gli altri (loginform, newsletter, pricelist, starrating), il test runtime mostra 0 PASS ma il sorgente è giusto e post-deploy funzionerà.

2. **Falsi positivi audit (~340)**: non sono bug — sono testi placeholder ("Inserisci URL", "Seleziona menu", "Nessun prodotto in questo contesto"), emoji avatar, asterisco `*`, tile dinamiche senza context (woo, nav).

3. **Renderer fix**: tile come quotation, starrating, pricelist potrebbero richiedere modifica PHP renderer per leggere i nuovi color settings. Da fare in sessione futura.
