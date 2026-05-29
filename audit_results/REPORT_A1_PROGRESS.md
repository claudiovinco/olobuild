# Audit A1 — Progresso fix preset self-contained

**Data**: 2026-05-24, sessione 2  
**Branch lavorativa**: opzione W (full fix A1, 2-3 giorni budget)

## Lavoro completato in questa sessione (~2h)

### Modifiche al sorgente (`BuilderInspector.vue` + `class-form-tile.php`)

| Tile | Baseline PASS | Dopo fix | Delta |
|---|---:|---:|---:|
| headline | 8/12 | 11/12 | **+3** |
| pagetitlebar | 4/12 | 9/12 | **+5** |
| form | 0/12 | 7/12 | **+7** |
| pricing | 0/12 | 5/12 | **+5** |

**Cumulativo tile fixati**: +20 PASS  
**Effetti collaterali positivi**: +42 PASS in altri tile (probabili side-effect del fix asterisco form, eredità CSS, ecc.)

### Totale audit completo

| Stato | Baseline V1 | Audit finale | Delta |
|---|---:|---:|---:|
| PASS | 629/1476 (42.6%) | **691/1476 (46.8%)** | **+62** |
| LOW_CONTRAST | 727 | 665 | -62 |
| ZERO_SIZE | 120 | 120 | 0 |

### Dettaglio modifiche al sorgente

1. **headline** (`BuilderInspector.vue` ~2452-2464): aggiunto `bg_color` a 5 preset (glass-overlay, neon-glow, gradient-aurora, retro-terminal, tilt-3d) per evitare testo bianco invisibile su sfondo default chiaro.

2. **pagetitlebar** (`BuilderInspector.vue` ~2520-2522): preset esplosi da `...BASE_THEME_PRESETS` a 12 entries con `title_color`, `subtitle_color`, `breadcrumb_color` per ogni preset.

3. **form** (`BuilderInspector.vue` ~4750-4763): preset arricchiti con `bg_color`, `input_color`, `submit_bg`, `submit_color`, `check_accent_color`, `check_bg`, `check_border_color` per ognuno dei 12.

4. **pricing** (`BuilderInspector.vue` ~4386-4399): preset arricchiti con `cta_bg_color`, `cta_text_color`, `cta_hover_bg_color`, `bg_color`, `text_color`, `price_color`.

5. **class-form-tile.php** (~263): `.olo-f-required` asterisco da `color: var(--olo-color-danger, #EF4444)` a `color: currentColor; opacity: .65` — eredita il colore del label corrente, evita rosso illeggibile su giallo (brutalist-stamp) o altri preset.

## Stato build

- Vite build: **OK** (`assets/js/builder.js` 4119.78 kB, 27.79s)
- File pronti per deploy: `assets/js/builder.js`, `assets/css/*` (se modificate), `includes/tiles/class-form-tile.php`
- **Niente è stato deployato sul server** mosaic.clod.eu (resta v1.0.57)

## Tile NON ancora fixati (per sessioni future)

Tile con 0/12 PASS rimasti (~20 tile):
- `flipcard`, `hotspot`, `imgcompare`, `loginform`, `megamenu`, `newsletter`, `pricelist`, `progresstracker`, `quotation`, `starrating`, `team`, `viewer360`, `woo_checkout_multistep`, `woo_minicart`, `woo_order_tracking`, `woo_product_filter`, `woo_products`, `woo_wishlist`

Falsi positivi audit (NON da fixare):
- `blendtext`, `textmask`, `textpath` (effetto mix-blend-mode / text-mask, sempre visibili visivamente)
- `togglebtn`, `facebookpage`, `twitterfeed`, `instagram`, `megamenu` (placeholder text "Inserisci URL" / "Seleziona menu" — l'utente sostituirà)
- `video`, `viewer360`, `imgcompare` (placeholder text "Inserisci un URL …" — utente sostituirà)
- `nav`, `pagination`, `postnavigation`, `subnav`, `woo_cart`, `woo_checkout`, `woo_cross_sells`, `woo_quickview`, `woo_recently_viewed`, `wpcomments` (ZERO_SIZE per context-dependent rendering)

Stima realistica fix manuale: ~15-20 min per tile (config + preset rewrite + test) = ~5-7h aggiuntive per i 20 tile rimasti.

## Working tree status

⚠️ **NON IGNORARE**: il working tree contiene modifiche massicce pre-esistenti non legate ai miei fix:
- `BuilderInspector.vue`: 4582 LOC modificate (ristrutturazione UI: badges, copy/paste style, preset menu)
- `class-frontend-renderer.php`: 1458 LOC modificate
- `class-rest-api.php`: 1391 LOC modificate
- ~50 altri file con modifiche significative

**Prima del deploy v1.0.58 dovrai decidere**:
- Includi tutte le modifiche pre-esistenti? (deploy massiccio, alto rischio)
- Vuoi che isoli SOLO le mie modifiche A1 + i 2 fix noti?
- O fai commit selettivo prima del deploy?

## Decisione richiesta

1. **Continuo A1**: altri 5-7h per fixare i 20 tile rimasti (sessione separata)
2. **Stop e deploy v1.0.58**: bump versione, deploy quanto fatto (+62 PASS) + i 2 fix noti (video overlay, Lorem ipsum)
3. **Deploy minimal**: solo i fix sicuri (headline + pagetitlebar + form + pricing + form asterisco) + 2 fix noti, NO altre modifiche pendenti
4. **Hold**: niente deploy oggi, valutare strategia
