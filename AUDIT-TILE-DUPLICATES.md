# Audit duplicazione tile FREE — analisi similarità

> Generato 2026-05-02 via `_audit-tile-duplicates.cjs`. Misura overlap tra coppie di tile per identificare candidati a fusione o deprecazione.

## Metodologia

Per ogni coppia di tile (i, j) calcolato uno score 0-100% combinando:
- **Jaccard sui set di keys** dei fields inspector (peso 50%) — quanto i due tile espongono gli stessi controlli
- **Jaccard lessicale** su name+type+icon (peso 30%) — quanto i due tile si chiamano in modo simile
- **Match categoria** (peso 20%) — bonus se appartengono alla stessa categoria

Soglia interpretativa:
- **≥ 70%**: candidati FORTI a fusione/deprecazione
- **50-69%**: parziali sovrapposizioni — valutare caso per caso
- **30-49%**: similitudini di concetto — di solito ok mantenere distinte
- **< 30%**: tile veramente distinte

## Stats

| Soglia | Coppie |
|---|---:|
| ≥ 70% (fusione probabile) | **0** |
| 50-69% (parziali) | 2 |
| 30-49% (concetti vicini) | 63 |
| Totale coppie analizzate | 4095 |

## 🟡 Sovrapposizioni parziali (50-69%)

| Tile A | Tile B | Score | Keys | Lex | Cat |
|---|---|---:|---:|---:|---:|
| overlayslider | overlaygrid | 59% | 66% | 22% | ✓ |
| list | iconlist | 51% | 37% | 43% | ✓ |

## Top 30 coppie (≥30%)

| # | Tile A | Tile B | Score | Keys | Lex | Cat |
|---|---|---|---:|---:|---:|---:|
| 1 | overlayslider | overlaygrid | 59% | 66% | 22% | ✓ |
| 2 | list | iconlist | 51% | 37% | 43% | ✓ |
| 3 | instagram | twitterfeed | 49% | 46% | 20% | ✓ |
| 4 | nav | subnav | 49% | 45% | 20% | ✓ |
| 5 | sharebuttons | social | 49% | 40% | 29% | ✓ |
| 6 | nav | navmenu | 47% | 9% | 75% | ✓ |
| 7 | list | desclist | 45% | 25% | 43% | ✓ |
| 8 | panel | panelslider | 45% | 33% | 29% | ✓ |
| 9 | gallery | lightbox | 44% | 12% | 60% | ✓ |
| 10 | code | html | 44% | 0% | 80% | ✓ |
| 11 | audio | soundcloud | 43% | 0% | 75% | ✓ |
| 12 | panelslider | overlayslider | 42% | 30% | 22% | ✓ |
| 13 | subnav | pagination | 41% | 18% | 40% | ✓ |
| 14 | readingtime | viewscounter | 39% | 29% | 14% | ✓ |
| 15 | row | inner-columns | 38% | 18% | 30% | ✓ |
| 16 | overlay | soundcloud | 38% | 16% | 33% | ✓ |
| 17 | switcher | switcherpanel | 37% | 21% | 22% | ✓ |
| 18 | panel | switcherpanel | 37% | 8% | 43% | ✓ |
| 19 | iconlist | desclist | 37% | 21% | 20% | ✓ |
| 20 | audio | overlay | 36% | 8% | 40% | ✓ |
| 21 | facebookpage | twitterfeed | 36% | 24% | 14% | ✓ |
| 22 | panel | icontabs | 35% | 14% | 25% | ✓ |
| 23 | panel | popover | 34% | 8% | 33% | ✓ |
| 24 | pagination | search | 34% | 18% | 17% | ✓ |
| 25 | audio | lightbox | 34% | 4% | 40% | ✓ |
| 26 | authorbox | wpcomments | 34% | 14% | 22% | ✓ |
| 27 | navmenu | megamenu | 33% | 10% | 29% | ✓ |
| 28 | paymentbuttons | pricing | 33% | 6% | 33% | ✓ |
| 29 | postgrid | relatedposts | 33% | 13% | 22% | ✓ |
| 30 | nav | pagination | 32% | 15% | 17% | ✓ |

## Conclusioni operative

### Verdetto per coppia top (analisi semantica manuale)

| Coppia | Score | Giudizio | Azione consigliata |
|---|---:|---|---|
| **overlayslider ↔ overlaygrid** | 59% | Use case distinti (slider one-at-time vs grid all-visible) | **Tenere distinte** |
| **list ↔ iconlist** | 51% | Verifica approfondita 2026-05-02: iconlist ha icon picker libero (vs 10 preset di list), icon shapes circle/square/rounded, layout horizontal, divider tra voci — feature legittime e distinte | **Tenere distinte** (decisione finale) |
| **nav ↔ subnav** | 49% | Lex altissima (75%) ma keys distinte (20%). Use case veri: main vs sub | **Tenere distinte** |
| **instagram ↔ twitterfeed** | 49% | Provider diversi, embed legittimi separati | **Tenere distinte** |
| **sharebuttons ↔ social** | 49% | sharebuttons = condivisione URL corrente; social = link account social. Funzioni opposte | **Tenere distinte** |
| **nav ↔ navmenu** | 47% | nav = lista link manuale; navmenu = menu WP. Lex 75% confonde | **Tenere distinte** ma rinominare per chiarezza UX (es. `nav` → `linklist`) |
| **list ↔ desclist** | 45% | desclist = definition list HTML semantica diversa | **Tenere distinte** |
| **panel ↔ panelslider** | 45% | Cardinalità diversa (singolo vs slider) | **Tenere distinte** |
| **gallery ↔ lightbox** | 44% | Funzioni complementari, non sovrapposte | **Tenere distinte** |
| **code ↔ html** | 44% | code = display syntax-highlighted (es. snippet); html = embed raw HTML | **Tenere distinte** |
| **audio ↔ soundcloud** | 43% | audio = player HTML5 locale; soundcloud = embed | **Tenere distinte** |
| **panelslider ↔ overlayslider** | 42% | Pannelli vs overlay — visual distinti | **Tenere distinte** |
| **switcher ↔ switcherpanel** | 37% | switcher = container; switcherpanel = singolo panel del switcher (parent-child) | **Tenere distinte** (relazione strutturale) |
| **readingtime ↔ viewscounter** | 39% | Entrambi meta blog ma metriche diverse | **Tenere distinte** |
| **row ↔ inner-columns** | 38% | row = top-level; inner-columns = nested. Pattern coscienti | **Tenere distinte** |

### Riassunto

- **0 duplicati critici** che richiedano deprecazione immediata
- **1 refactor candidato concreto**: `iconlist` da assorbire in `list` con flag `show_icons` (-1 tile, +0 funzionalità perse). Stima impatto: nessun template legacy si rompe se manteniamo l'alias.
- **Le altre similarità sono "concettuali"** (stessa categoria) o lessicali (nomi simili) ma le keys e le funzioni sono distinte. Mantenere come sono.

### Possibile rinaming per chiarezza UX (no fusione)

- `nav` → `linklist` o `linknav` (per distinguerlo nettamente da `navmenu` WordPress)

## Note metodologiche

- L'algoritmo è euristico, non semantico. Due tile con overlap 60% di keys ma funzioni opposte (es. `hide_X` vs `show_X`) appaiono simili.
- Tile con < 5 fields tendono a "matchare" troppo facilmente con altre piccole tile.
- Conferma sempre con ispezione manuale prima di fondere/deprecare.
- Categorie identiche danno bonus 20% — alcune similarità sono "infrastrutturali" (es. due tile della categoria 'navigation' condividono campi navigazionali standard).
