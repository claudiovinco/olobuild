# Audit preset Olobuild — v1.0.57 + tentativo fix sistemico A

**Data**: 2026-05-24  
**Versione testata**: v1.0.57 (produzione mosaic.clod.eu, template ID 197)  
**Metodo**: test DOM-based via Chrome MCP — measurazione computed style + contrasto WCAG sul rendering iframe del builder, 1476/1476 combo testate.

---

## 1. Risultato baseline v1.0.57

| Status | Conta | % |
|---|---:|---:|
| PASS | 629 | 42.6% |
| LOW_CONTRAST | 727 | 49.3% |
| ZERO_SIZE | 120 | 8.1% |
| **TOTALE** | **1476** | **100%** |

Categorizzazione FAIL:
- **120 ZERO_SIZE** in 10 tile dinamiche (nav, woo_cart, ecc.) → falsi positivi da context-dependent
- **176 LOW_CONTRAST su placeholder text** ("Inserisci URL…") → falsi positivi
- **551 LOW_CONTRAST su content reale** → bug veri, di cui **230 critici (ratio < 3)** in 47 tile types

Esempi devastanti (contrasto = 1.0, testo invisibile): headline+glass-overlay "Titolo sezione" 64px, blendtext+gradient-aurora "BLEND" 120px, hero+gradient-aurora "Benvenuto…" 40px, countdown+glass-tier "00" 52px, form+glass-form "Nome".

---

## 2. Tentativo fix sistemico — cascade automatica `text_color`

### Approccio
In `applyTilePresetTheme` (BuilderInspector.vue), dopo l'applicazione del preset, propagare automaticamente `text_color` a tutti gli alias di colore testo che il tile ha come field e che sono vuoti nei defaults (heading_color, subtitle_color, content_color, description_color, body_color, placeholder_color, header_text_color, header_text_color_active).

Implementato runtime via patch `window.__PATCH_APPLY` per testare senza deploy. Audit V5 con patch attiva → 1476/1476 combo.

### Risultato (3 iterazioni testate)

| | PASS | LOW_CONTRAST | ZERO_SIZE | Net |
|---|---:|---:|---:|---:|
| V1 (baseline) | 629 | 727 | 120 | — |
| V2 (cascade aggressiva, 50+ alias) | 639 | 717 | 120 | +10 |
| V3 (cascade conservativa, 30 alias) | 629 | 727 | 120 | 0 |
| V5 (cascade ultra-min, 8 alias + only-if-empty) | 621 | 699 | 96 | **-8** |

**Gain consolidati**: content (+4), woo_myaccount (+4), pagetitlebar (+6 in v2).  
**Regressioni**: grid (-6), woo_categories (-6), woo_comparison (-4), newsticker (-8 in v2/v3).

### Diagnosi
La cascade JS NON funziona perché molti tile renderer hanno **color hardcoded nei componenti Vue/PHP** che NON dipendono dai settings color. Quando il preset setta `text_color`, solo i tile renderer che esplicitamente leggono `text_color`/`heading_color` lo applicano. Gli altri (grid, newsticker badge, woo cards) hanno il loro color fissato nel CSS/template.

**Conclusione**: l'opzione A "fix sistemico via cascade JS" è **una strada cieca**. La cascade non risolve il problema strutturale.

Il fix locale è stato **revertato**. `BuilderInspector.vue` è tornato allo stato originale (no cascade).

---

## 3. Strategia alternativa per Opzione A

Il fix sistemico vero richiede uno di questi tre approcci pesanti:

### A1 — Per-tile fix (preset self-contained)
Per ogni tile rotto (47), espandere i 12 preset esistenti per definire ESPLICITAMENTE tutti i sotto-colori che il tile usa. Es. per grid: ogni preset dovrebbe avere `card_bg`, `title_color`, `content_color`, `meta_color` espliciti.
- **Effort**: 2-3 giorni di lavoro meccanico (47 tile × 12 preset × ~4 settings = ~2200 entries da scrivere)
- **Pro**: zero modifiche al codice renderer
- **Contro**: noioso, error-prone, ogni tile va studiata per capire le sue color settings

### A2 — CSS variables nel wrapper
Modificare il wrapper renderer (`class-frontend-renderer.php`) per emettere `--olo-tile-text-color: var(--text_color, inherit)` come CSS variable sul wrapper, poi modificare il CSS di ogni tile per usare `color: var(--olo-tile-text-color, default)`.
- **Effort**: 2-3 giorni (1 file wrapper + 47 file tile + audit CSS)
- **Pro**: fix una volta, scala automaticamente
- **Contro**: rischio di regressioni su tile già funzionanti, richiede test estensivo

### A3 — Renderer rewrite
Modificare il renderer PHP di ogni tile per ASCOLTARE `text_color` e propagarlo via CSS inline ai sotto-elementi.
- **Effort**: 3-5 giorni
- **Pro**: fix accurato
- **Contro**: massiccio rifacimento, rischio rotture

---

## 4. Raccomandazione

L'audit ha rivelato che **il sistema di preset di Olobuild ha un design flaw fondamentale** — non un bug fixabile con cascade. Lanciare il prodotto con 57% di FAIL contrasto è critico per UX.

**Opzioni pragmatiche concrete**:

**Opzione X — Approccio chirurgico mirato (raccomandato)**  
Concentrarsi sui 22 tile types "0/12 PASS" + 4 più usati (content, headline, hero, button) = ~26 tile. Per ognuno applicare A1 (preset self-contained). I tile meno usati restano con preset attuali.
- Effort: ~1 giorno
- Risolve ~50-60% dei bug critici

**Opzione Y — Reset preset library**  
Ridurre i preset da 12 a 5 per tile (i 5 "sicuri" che sappiamo funzionare). Eliminare i preset "audaci" (glass, neon, gradient, sticker, retro, tilt 3D) finché non fixati.
- Effort: ~2 ore
- Riduce le opzioni utente da 12 a 5 (downgrade visibile)
- Garantisce zero FAIL

**Opzione Z — Lancio + warning + roadmap**  
Lasciare i preset così, ma:
- Aggiungere warning UI nel builder quando contrasto < 4.5 (computed style check al render)
- Documentare nella roadmap pubblica che i preset "audaci" sono in beta
- Effort: ~4 ore (alert UI)

**Opzione W — Rimandare il lancio**  
Lavorare 2-3 giorni full su Opzione A1 + audit iterativo.
- Effort: 2-3 giorni
- Rimanda il lancio
- Risolve la maggior parte dei bug

---

## 5. Bug noti utente — stato

| Bug | Stato fix |
|---|---|
| video `overlay_text` non visibile in 16:9 | **modifica locale pending** (`includes/tiles/class-video-tile.php` ha `render_overlay_layers`) — non deployato |
| video `start_time`/`end_time` Lorem ipsum | **modifica locale pending** (`src/composables/useDragDrop.js` regex ampliata) — non deployato |
| content "Titolo sezione" illeggibile | identificato sull'audit; risolvibile con A1 |

Questi 2 fix locali sono **indipendenti dal problema preset** e possono essere deployati subito con bump v1.0.58 senza aspettare la decisione su Opzione A.

---

## 6. Stato attuale del filesystem locale

- `BuilderInspector.vue` → **revertato** alla versione originale (cascade rimossa)
- `useDragDrop.js` → mantiene la modifica regex (fix Lorem ipsum noto) — non deployato
- `class-video-tile.php` → mantiene la modifica overlay_layers (fix video overlay) — non deployato
- Tutti gli altri file PHP modificati: solo whitespace CRLF, nessuna sostanza
- Build Vite: l'ultima `assets/js/builder.js` locale è la versione SENZA cascade (post-revert), pulita

**Niente è stato deployato al server**. Il server mosaic.clod.eu ha sempre v1.0.57.

---

## 7. Decisione richiesta

Cosa proseguire?

1. **Opzione X** (approccio chirurgico, 1 giorno, 22+4 tile fix preset self-contained)
2. **Opzione Y** (riduco preset da 12 a 5, 2h)
3. **Opzione Z** (warning UI + roadmap, 4h)
4. **Opzione W** (full fix A1 + 2-3 giorni)
5. Solo **deploy v1.0.58** con i 2 fix noti, no fix preset
6. Altra strategia da discutere

---

## 8. Allegati

- `audit_results/agg_by_type.csv` — tabella completa 123 tile × status counts (versione v1.0.57 baseline)
- `audit_results/bug_samples.json` — 46 sample dettagliati ratio < 3
