# Template CLOD folle

_Started 2026-06-10 21:43 UTC_

---

## User

rendi ancora più folle e creativo il template clod - evoluzione. Ricordati che andrà costruito con le tile di olobuild

## Assistant

_[tool: list_files]_

_[tool: read_file]_

_[tool: read_file]_

_[tool: read_file]_

_[tool: read_file]_

_[tool: read_file]_

_[tool: copy_files]_

_[tool: write_file]_

_[tool: write_file]_

_[tool: str_replace_edit]_

_[tool: done]_

_[tool: fork_verifier_agent]_

Fatto — ho creato **Clod — Evoluzione (supertemplate) v2.html** (la v1 resta intatta) con un layer "sala di regia spinta", caricato da due file separati (`clod/evo-fx.css` + `clod/evo-fx.js`):

1. **Grana pellicola** animata su tutta la pagina (chrome di tema)
2. **HUD mirino** che segue il cursore con coordinate + nome della sezione corrente
3. **Timecode di scroll** nella nav (la pagina è "un film" da 90s a 25fps) + hairline lime di progresso
4. **Hero**: "VISUAL" entra lettera per lettera con blur+rotazione
5. **Manifesto**: si accende parola per parola mentre scorri
6. **Servizi**: un piccolo monitor "● STILL" segue il cursore mostrando numero e nome del servizio
7. **Strip e reel**: skew da velocità — la striscia ondeggia con lo scroll, i fotogrammi si inclinano col drag
8. **CTA**: "Parliamone" fa l'onda sotto il mouse (le lettere sollevate diventano lime) e la mail è magnetica
9. Nuova annotazione **Evoluzione · 06 "Sala di regia"** nel layer motivazioni

**Per OLObuild**: ogni effetto è una IIFE autonoma → 1–3 sono chrome/script di tema, 4–8 mappano 1:1 su opzioni delle singole tile (Hero, Testo, Lista, Marquee, Galleria, CTA). Tutto è dietro `prefers-reduced-motion` e `pointer:fine`, quindi su mobile/accessibilità degrada pulito.

Se qualcosa risulta *troppo* folle (la grana o il mirino sono i candidati), si spegne rimuovendo una riga.

## User

mi crei la pagina ricerca media più in linea con una grafica moderna? https://sand01.olotheme.com/wp-admin/admin.php?page=olo-media-search

## Assistant

_[tool: github_get_tree]_

_[tool: github_import_files]_

_[tool: read_file]_

_[tool: list_files]_

_[tool: read_file]_

_[tool: read_file]_

_[tool: read_file]_

_[tool: read_file]_

_[tool: read_file]_

_[tool: list_files]_

_[tool: read_file]_

_[tool: grep]_

_[tool: copy_files]_

_[tool: write_file]_

_[tool: write_file]_

_[tool: write_file]_

_[tool: write_file]_

_[tool: write_file]_

_[tool: show_html]_

_[tool: show_html]_

_[tool: str_replace_edit]_

_[tool: str_replace_edit]_

_[tool: done]_

_[tool: fork_verifier_agent]_

Ecco il redesign della pagina **Ricerca Media** in `design_handoff_olobuild_mediasearch/prototype/` — stesso linguaggio cockpit di Dashboard/Template (tokens verde+navy, shell WP, top bar). Cosa cambia rispetto alla pagina attuale:

- **Pannello di ricerca unico**: segmented control Foto/Video/Foto 360/Video 360/Audio con icone, chip provider con stato (il pill disabilitato — Pixabay — mostra lucchetto + tooltip "API key mancante"), campo grande con focus ring e ricerche rapide suggerite
- **Filtri contestuali** a pill compatte (orientamento/dimensione/min px per foto; durata con preset per video e audio) + "Azzera filtri"
- **Risultati foto a masonry** con overlay hover (autore, dimensioni, anteprima, Importa); video con badge durata e play; HDRI con badge "HDRI 360" e tag; **audio come righe con waveform** che si "accende" durante il play simulato
- **Street View**: pannello dedicato con input, zoom, timeline date e import del panorama
- Skeleton shimmer in caricamento, stato "N risultati · via Unsplash", paginazione con ellissi, modal anteprima con metadati e licenza
- Flusso import con stati Importazione… → Importato ✓

Tutto è mock (nessuna chiamata reale; immagini picsum come placeholder) ma i contratti restano quelli veri: stessi provider, stessi filtri per provider, stessi endpoint REST nel commento di handoff. Se vuoi, preparo anche il README di handoff per Claude Code o varianti (es. provider come tab verticali, o ricerca unificata multi-provider).

