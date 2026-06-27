# Mega Menu Tile Variations

_Started 2026-06-05 19:15 UTC_

---

## User

ora leggi le caratteristiche del tile Mega Menu, impara ad usarlo a fondo (lo trovi su github) e proponi variazioni e implementazioni che ritieni necessarie

## Assistant

_[tool: github_list_repos]_

Leggo il repo per trovare il tile Mega Menu e capirne struttura, props e logica.Cerco i file relativi al Mega Menu nel repo.

_[tool: github_get_tree]_

_[tool: grep]_

_[tool: github_get_tree]_

The grep was on my local project. Let me find the actual Mega Menu tile in the GitHub repo's Tiles folder.

_[tool: github_get_tree]_

_[tool: github_get_tree]_

The offset didn't work as expected. Let me grep the repo tree for the menu-related files directly.

_[tool: github_get_tree]_

_[tool: github_get_tree]_

Il tree tronca a 40 voci. Cerco il file del Mega Menu tramite il registry dei tile nel config.

_[tool: github_read_file]_

_[tool: github_read_file]_

Provo a leggere direttamente i file probabili del Mega Menu.

_[tool: github_read_file]_

Ottimo, ho la config completa. Ora leggo il componente Vue che renderizza il tile per capire markup e logica.

_[tool: github_get_tree]_

_[tool: github_read_file]_

_[tool: grep]_

Provo nomi alternativi per il componente.

_[tool: list_files]_

Ho ora una comprensione completa del tile Mega Menu — config (`megamenu.js`) e componente (`MegaMenuTile.vue`). Esamino il contesto del progetto per capire in che formato consegnare le proposte.

_[tool: read_file]_

_[tool: read_file]_

Progetto ricco con un pattern chiaro di documenti "Redesign". Leggo i token brand e un redesign di riferimento per allinearmi al linguaggio visivo.

_[tool: read_file]_

Ora guardo un documento redesign esistente per allinearmi alla struttura/scaffold che usi.

_[tool: update_todos]_

Ho studiato a fondo il tile: config (`megamenu.js`, ~130 campi, 12 preset, 14 hover, 10 hamburger, 8 animazioni pannello, topbar, social, mobile) e il componente `MegaMenuTile.vue` (rendering, logica preset/mega, hamburger SVG, animazioni). Ho anche il linguaggio visivo dei tuoi documenti redesign e i token brand.

Una scoperta chiave emerge subito dal codice: **i 12 preset sono dichiarati in config ma `MegaMenuTile.vue` non legge mai `s.preset`** → non cambiano nulla a video. Questa è l'implementazione più importante da proporre. Costruisco un documento focalizzato.Costruisco il documento. Comincio con la base, l'anatomia e la sezione "finding".

_[tool: write_file]_

_[tool: show_html]_

_[tool: show_html]_

Masthead e stats sono ottimi. Vediamo l'hero anatomico con i pin.

_[tool: save_screenshot]_

_[tool: save_screenshot]_

_[tool: str_replace_edit]_

Faithful e bello. Sistemo l'icona Instagram (rende come quadrato pieno) e proseguo aggiungendo finding banner + gallery preset. Prima il fix icona:

_[tool: str_replace_edit]_

Ora aggiungo il CSS per le varianti preset, poi le sezioni finding + gallery. Inserisco il CSS:

_[tool: str_replace_edit]_

Ora inserisco il finding banner, la gallery delle variazioni e la tabella preset dopo l'anatomia:

_[tool: str_replace_edit]_

Correggo un refuso CSS nel brutalist stage:Vediamo la gallery preset:

_[tool: save_screenshot]_

_[tool: save_screenshot]_

_[tool: eval_js]_

L'icona IG sembra ancora un quadrato pieno. Verifico il rendering reale:

_[tool: eval_js]_

_[tool: show_html]_

_[tool: save_screenshot]_

_[tool: save_screenshot]_

Magazine e Glass sono splendidi. Noto due cose da sistemare: i titoli preset ("Glass Bar") vanno a capo, e il logo "Il Costruttore" wrappa. Vedo neon/brutalist poi sistemo in blocco.

_[tool: str_replace_edit]_

_[tool: str_replace_edit]_

Brutalist è perfetto. Sistemo i titoli che vanno a capo e i loghi:

_[tool: str_replace_edit]_

Aggiungo la sezione finale delle implementazioni tecniche (i gap reali trovati nel codice), una breve FAQ e il footer.

_[tool: update_todos]_

Vediamo le sezioni implementazioni + minimal/clean preset per controllo finale.

_[tool: save_screenshot]_

The recipe table reads beautifully. Let me check the implementation cards section.

_[tool: save_screenshot]_

_[tool: grep]_

Le sezioni 04/05 sembrano non comparire. Verifico:

_[tool: show_html]_

Il contenuto c'è — l'iframe non si era ricaricato. Ricarico e controllo:

_[tool: save_screenshot]_

_[tool: save_screenshot]_

Le card implementazioni con i code block sono ottime. Controllo l'inizio gallery (Modern Clean + Minimal Line) che non avevo ancora visto:

_[tool: save_screenshot]_

Modern Clean e Minimal Line sono splendidi. Il sovrapporsi nome/chip nell'header è FOUT del font (catturato a metà caricamento). Riconfermo con delay maggiore:

_[tool: eval_js]_

Persiste anche dopo il caricamento font — è un bug di layout reale. Ispeziono:

_[tool: eval_js]_

_[tool: get_webview_logs]_

Confermato: nessuna sovrapposizione reale — `nameRight=159`, `chipLeft=171`, gap pulito di 12px. L'apparente overlap era un artefatto dello screenshot (font di fallback nel capture). Il DOM live con Instrument Serif è corretto. Controllo console e chiudo.Console pulita. Chiudo consegnando il file all'utente.

_[tool: done]_

_[tool: update_todos]_

_[tool: fork_verifier_agent]_

Fatto. Ho letto a fondo il Mega Menu (`megamenu.js` + `MegaMenuTile.vue`) e consegnato **OLObuild - Mega Menu deep-dive.html**.

**Cosa contiene:**
- **Anatomia** — header live etichettato (top bar, logo, voci+chevron, tools, mega-pannello con colonna promo).
- **Scoperta chiave dal codice** — i **12 preset sono dichiarati in config ma `MegaMenuTile.vue` non legge mai `s.preset`**: oggi non cambiano nulla.
- **6 variazioni rese vive** come *ricette di token* (nessuna chiave nuova): Modern Clean, Minimal Line, Magazine Bold (con ticker), Glass Bar, Neon Strip, Brutalist Block.
- **Tabella delle 12 ricette** preset → chiavi.
- **6 implementazioni necessarie**, dalla più importante: cablare i preset come ricette (con snippet); **bug `mega_mode`** (config offre `auto`/`css-class`, il componente controlla `'all'` inesistente e non gestisce `css-class`); default hardcoded → token; IA inspector per 130 campi; colonna promo nel pannello; ricerca `expand`.

**Note:**
- Tutto è "contenuto tile" (token cliente, primario rosso); il chrome arancio dell'inspector resta invariato, come da regola.
- Le proposte rispettano il vincolo **chiavi salvate invariate** — pronte da girare a Claude Code.

Vuoi che ne ricavi una scheda handoff (`MEGAMENU_PRESETS.md` + `megamenuPresets.js`) nello stile di `design_handoff_olobuild_tiles/`, o che renda anche i preset mancanti (Gradient/Sticker/Retro/Tilt)?

## User

sì, e implementa gli esempi di menu a 30, sii creativo al massimo, osa e stupiscimi con le variazioni

## Assistant

_[tool: update_todos]_

Adoro questa direzione. Faccio due cose: il **pacchetto handoff** (`megamenuPresets.js` + `MEGAMENU_PRESETS.md`) e una **nuova galleria con 30 variazioni live**, audaci e strutturalmente diverse (non solo colori: split/stacked logo, top bar, pannello aperto, ricerca overlay, e-commerce con carrello…).

Pianifico e costruisco la galleria con un renderer data-driven (struttura DOM fedele a `MegaMenuTile.vue`, ogni variante è una ricetta di chiavi reali).Costruisco la galleria. Prima lo shell + CSS base + classi effetto:

_[tool: write_file]_

_[tool: write_file]_

Ora il renderer + le 30 ricette creative:

_[tool: str_replace_edit]_

Aggiorno il renderer per applicare `cls2` (effetti CTA secondari) e poi visualizzo:

_[tool: show_html]_

_[tool: save_screenshot]_

La pagina rende nel browser (parser HTML tollerante); l'errore è solo nel serializzatore SVG dello screenshot inline. Uso save_screenshot (cattura reale):

_[tool: str_replace_edit]_

Trovato il bug: i font tra doppi apici nelle CSS-var (`--mm-link-font:"Fraunces"`) finiscono dentro `style="…"` e rompono il serializzatore. Uso apici singoli:

_[tool: save_screenshot]_

_[tool: grep]_

Cerco altri font tra doppi apici nelle vars:

_[tool: str_replace_edit]_

Le var sono a posto; probabilmente l'iframe usa il JS in cache. Forzo un cache-bust sul tag script:

_[tool: show_html]_

_[tool: save_screenshot]_

_[tool: save_screenshot]_

Rende benissimo. Modern Clean e Compact App ottimi (il taglio nome/chip è il solito artefatto del font nello screenshot, non nel browser). Scorro per rivedere le altre famiglie:

_[tool: save_screenshot]_

Cinema Amber è cinematografico. Continuo verso neon/aurora/dev/midnight:

_[tool: save_screenshot]_

Neon e Aurora Glass sono spettacolari. Continuo:

_[tool: str_replace_edit]_

Midnight Lux ha una collisione: il logo centrato in assoluto si sovrappone a "Boutique". Lo correggo (logo a sinistra, nav centrata) — il logo centrato vero lo tengo per Split Nav (25) che ha la struttura giusta:

_[tool: save_screenshot]_

_[tool: save_screenshot]_

Editoriali ottimi. Vedo lusso/architettura/kraft e poi famiglia D (pop) ed E (strutture):

_[tool: save_screenshot]_

Lusso, Architecture e Serif Display sono raffinatissimi. Avanzo alla famiglia pop (D) e strutture (E):

_[tool: save_screenshot]_

Pop fantastici. Vedo Color Block, Festival, Retro, Brutalist:

_[tool: save_screenshot]_

Color Block e Festival (inclinato + glass + gradiente) sono audaci. Vedo Retro/Brutalist e la famiglia E strutturale:

_[tool: save_screenshot]_

Brutalist perfetto. Ora le strutture speciali (split, stacked, glass-photo):

_[tool: save_screenshot]_

Stacked e Glass-photo ottimi. Verifico Split Nav (logo centrato tra le voci) e poi e-commerce/pannello/overlay:

_[tool: str_replace_edit]_

Split Nav: il logo non è centrato (resta a sinistra). Lo correggo dando flex:1 a entrambe le liste così il logo va al centro:

_[tool: str_replace_edit]_

_[tool: str_replace_edit]_

Aggiorno la chip di Split Nav (la CTA non è più resa nella struttura split):Ora le ultime tre strutturali — e-commerce, pannello aperto, search overlay:

_[tool: save_screenshot]_

_[tool: save_screenshot]_

E-commerce perfetto (topbar promo, search expand, carrello con badge). Vedo il pannello aperto e la search overlay finale:

_[tool: str_replace_edit]_

Search Overlay è ottimo. Due fix sul "Mega Panel Open": il pannello assoluto sfora e si sovrappone alla card successiva — riservo spazio verticale nello stage:

_[tool: save_screenshot]_

_[tool: str_replace_edit]_

Il pannello è ancora tagliato — l'iframe ha in cache la versione `?v=2` del JS, quindi le ultime modifiche non sono caricate. Bumpo a `?v=3` e ricarico:

_[tool: show_html]_

_[tool: save_screenshot]_

_[tool: eval_js]_

Il pannello aperto ora è completo: 3 colonne con descrizioni + card promo. Galleria a 30 finita e ricca. (Il logo "Olobuild" che tocca "Home" è solo il fallback font nello screenshot — verifico velocemente che nel DOM live non ci sia sovrapposizione.)

_[tool: write_file]_

Confermato: gap di 20px nel DOM live, nessuna sovrapposizione (è solo il font di fallback nello screenshot). Galleria completa. Ora il pacchetto handoff: il recipe map cablabile e il README.

_[tool: write_file]_

_[tool: update_todos]_

Tutto pronto. Consegno la galleria all'utente e controllo la console.

_[tool: done]_

_[tool: update_todos]_

_[tool: fork_verifier_agent]_

Fatto. Ho consegnato **OLObuild - Mega Menu 30 variazioni.html** + il pacchetto handoff.

**30 header live, in 5 famiglie** — tutte una sola tile, ognuna una *ricetta di chiavi reali* (struttura DOM fedele a `MegaMenuTile.vue`):
- **A · Chiari & professionali** — Modern Clean, Compact App, Corporate Navy, Tech SaaS, Pharma Trust, Editorial Hairline
- **B · Scuri & cinematici** — Cinema Amber, Neon Strip, Aurora Glass, News Dark Ticker, Dev Docs Mono, Midnight Lux
- **C · Editoriali & raffinati** — Magazine Bold, Minimal Line, Luxury Gold, Architecture, Serif Display, Kraft Eco
- **D · Espressivi & pop** — Sticker Tape, Playful Pastel, Color Block, Festival Gradient, Retro Terminal, Brutalist Block
- **E · Strutture & overlay** — Split Nav, Stacked Center, Glass on Photo, E-commerce (topbar+carrello), **Mega Panel aperto dal vivo**, Search Overlay

Spaziano davvero: glass su gradiente aurora, neon glow + glitch, brutalismo con ombra dura, oro letterspaced, barra inclinata da festival, terminale fosforo, logo split/stacked, e-commerce con badge carrello.

**Pacchetto handoff** in `design_handoff_olobuild_tiles/megamenu/`:
- `megamenuPresets.js` — recipe map cablabile dei 12 preset ufficiali (token-first, chiavi invariate)
- `MEGAMENU_PRESETS.md` — problema preset + i 6 interventi + indice delle 30 variazioni + mappa anatomica

**Nota tecnica:** nei miei screenshot il logo serif a volte "tocca" la prima voce — è solo il font di fallback nella cattura; ho verificato nel DOM live che c'è un gap pulito di 20px. Nel browser dell'utente (Instrument Serif caricato) è corretto.

Vuoi che renda anche i preset mancanti come card extra, o che aggiunga un toggle "apri pannello" / "vista mobile" interattivo a ciascuna variante?

## User

sì e se ritieni di prendere altri spunti, fallo, crea manu strani e particolari usando tutto quello che di più creativo riesci a recuperare

## Assistant

_[tool: str_replace_edit]_

Sì — andiamo in territorio sperimentale. Aggiungo una **Famiglia F · Sperimentali & inattesi**: menu strani e particolari che spingono la tile in posti sorprendenti (command palette, marquee, departure board, Memphis, notebook scritto a mano, Bauhaus, Y2K aqua, tag cloud, mega-mosaico di immagini). Prima i font e il CSS degli effetti:

_[tool: str_replace_edit]_

_[tool: str_replace_edit]_

Ora estendo il renderer (numerazione, strutture command/marquee/tagcloud) e aggiungo la Famiglia F con 10 menu sperimentali:

_[tool: str_replace_edit]_

_[tool: str_replace_edit]_

_[tool: str_replace_edit]_

Aggiungo la famiglia F e le 10 ricette sperimentali:

_[tool: str_replace_edit]_

_[tool: str_replace_edit]_

Applico la classe stage al renderer (serve per il pattern Memphis) e bumpo la cache:

_[tool: str_replace_edit]_

Aggiorno masthead (40 variazioni), cache-bust e footer:

_[tool: str_replace_edit]_

_[tool: str_replace_edit]_

_[tool: show_html]_

_[tool: get_webview_logs]_

Console pulita. Vediamo le nuove sperimentali — command palette, marquee, index, departure board:

_[tool: save_screenshot]_

_[tool: eval_js]_

Marquee ottimo. La Command Palette però non mostra il campo di ricerca centrale né il dropdown. Indago:

_[tool: str_replace_edit]_

Trovato: ho dimenticato `struct:'command'` su F1 e `struct:'marquee'` su F2 (renderizzano la struttura default). Aggiungo:

_[tool: str_replace_edit]_

_[tool: show_html]_

_[tool: save_screenshot]_

_[tool: str_replace_edit]_

Command Palette ora mostra input + dropdown, ma il dropdown è tagliato dalla card. Riservo spazio come per il pannello:

_[tool: str_replace_edit]_

_[tool: show_html]_

Vediamo Index, Departure Board, Memphis, Notebook:

_[tool: save_screenshot]_

_[tool: save_screenshot]_

Index e Departure Board sono perfetti. Vedo Memphis, Notebook, Bauhaus:

_[tool: save_screenshot]_

Memphis e Notebook sono deliziosi (Notebook ha le voci un po' attaccate — sistemo il gap). Vedo Bauhaus, Y2K, Tag Cloud, Mosaic:

_[tool: save_screenshot]_

Bauhaus e Y2K splendidi. Vedo gli ultimi due — Tag Cloud e Mosaic Mega:

_[tool: str_replace_edit]_

Tag Cloud e Mosaic Mega chiudono in bellezza. Unico ritocco: allargo il gap del Notebook (voci un po' attaccate in Caveat):

_[tool: str_replace_edit]_

Aggiorno il README handoff con la famiglia F e il conteggio, poi bumpo la cache:

_[tool: str_replace_edit]_

_[tool: show_html]_

_[tool: eval_js]_

40 variazioni in 6 famiglie, tutte presenti. Consegno e verifico.

_[tool: get_webview_logs]_

_[tool: done]_

_[tool: fork_verifier_agent]_

Fatto — la galleria è ora a **40 variazioni in 6 famiglie**.

Ho aggiunto la **Famiglia F · Sperimentali & inattesi** — i menu strani e particolari che hai chiesto, ognuno reinterpreta una zona reale della tile:

- **Command Palette** — il menu *diventa* ricerca (⌘K + dropdown risultati)
- **Marquee Statement** — la nav scorre come un ticker, font Bungee
- **Editorial Index** — voci numerate 01–05, da indice di rivista
- **Departure Board** — celle split-flap mono ambra su nero, da aeroporto
- **Memphis 80s** — bordo spesso + ombra dura, confetti geometrici
- **Notebook Hand** — scritto a mano (Caveat), righe da quaderno, underline tratteggiato
- **Bauhaus Blocks** — cerchio+quadrato primari, blocco attivo pieno
- **Y2K Aqua** — barra gel acqua, bottone glossy anni 2000
- **Tag Cloud** — voci a nuvola, dimensioni variabili, alcune a pillola
- **Mosaic Mega** — pannello aperto come **mosaico di immagini** + card drop

Per restare onesto col rigore del progetto, le voci che richiedono un filo di CSS oltre l'inspector sono marcate **`+frontend`** — così in fase di handoff si capisce subito quali capacità varrebbe la pena portare dentro `MegaMenuTile.vue`.

**Aggiornati anche:** masthead (40 variazioni), e il README `design_handoff_olobuild_tiles/megamenu/MEGAMENU_PRESETS.md` con la famiglia F e la nota su cosa estendere nel render.

Se una di queste ti accende particolarmente (es. Command Palette o Mosaic Mega), posso renderla un preset ufficiale completo con tutte le chiavi mappate e lo snippet di render per Claude Code.

## User

crea i template di tutti questi megamenu, pronti da comunicare a olobuild

## Assistant

_[tool: snip]_

_[tool: snip]_

_[tool: snip]_

Creo i **template completi** di tutte le 40 variazioni, espressi con le **chiavi reali** di `megamenu.js` — pronti da importare/comunicare a OLObuild. Prima libero un po' di contesto dalle iterazioni già consegnate.Rileggo il file delle gallery per allineare nomi e ricette ai template, e i default canonici già scritti:

_[tool: read_file]_

_[tool: write_file]_

Ora costruisco il file dei template — tutti e 40, con le chiavi reali di `megamenu.js`, valori nei formati corretti (spacing `{top,right,bottom,left}`, radius `{tl,tr,br,bl}`), token-first, e per gli sperimentali la nota `frontend` con l'estensione richiesta.

_[tool: run_script]_

Genero anche una versione **JSON** (importabile senza eseguire JS) dai template, espandendo gli helper:

_[tool: run_script]_

Verifico che gli helper siano espansi correttamente nel JSON (radius/spacing come oggetti):

_[tool: str_replace_edit]_

Bug nell'helper `R`: con un solo argomento `tr` resta `undefined` e sparisce dal JSON (manca l'angolo top-right). Lo correggo e rigenero:

_[tool: str_replace_edit]_

Stesso bug nell'helper di `megamenuPresets.js` — lo allineo, poi rigenero il JSON:

_[tool: run_script]_

_[tool: run_script]_

Ora genero un **README dei template** (indice dei 40 + rollup delle estensioni frontend) direttamente dai dati, per la massima accuratezza:

_[tool: grep]_

26/40 è troppo: avevo segnato come "frontend" anche cose ottenibili con `typography_preset` (che è una chiave reale). Pulisco le note per essere onesto verso OLObuild. Leggo le stringhe esatte:

_[tool: run_script]_

Pulisco le note in modo programmatico (per riga, così evito problemi di apici), poi rivalido e rigenero JSON + README:

_[tool: run_script]_

17 estensioni frontend reali — onesto. Rigenero JSON e README dai dati aggiornati:

_[tool: list_files]_

Verifico la cartella handoff completa e la consegno:

_[tool: present_fs_item_for_download]_

Fatto. Ho creato i **template completi di tutti i 40 megamenu**, pronti da comunicare a OLObuild, in `design_handoff_olobuild_tiles/megamenu/`:

- **`megamenuTemplates.js`** — i 40 template come `settings` con le **chiavi reali** di `megamenu.js` (formati corretti: spacing `{top,right,bottom,left}`, radius `{tl,tr,br,bl}`), token-first. Importabile.
- **`megamenu-templates.json`** — stessa cosa, valori già espansi, per import diretto senza eseguire JS.
- **`README_TEMPLATES.md`** — indice dei 40 (id, preset base, n° chiavi) + come applicarli + rollup onesto delle **17 estensioni frontend** davvero necessarie (le altre 23 si ottengono già con le chiavi esistenti; i font particolari usano `typography_preset`).
- `megamenuPresets.js` + `MEGAMENU_PRESETS.md` — i 12 preset ufficiali + il deep-dive (già consegnati).

Ogni template ha `preset` base, `notes` in italiano e, dove serve, `frontend` con l'estensione da implementare. Tutto rispetta il vincolo **chiavi salvate invariate**.

Se vuoi, posso aggiungere un piccolo `apply()`/snippet PHP-side o un file di import nel formato esatto che il plugin si aspetta per i "Template pannelli" — dimmi come li importa OLObuild e lo allineo.

## User

allego il file che hai generato a claude code, scrivimi il prompt per lui

## Assistant

_[tool: snip]_

Ecco il prompt pronto da incollare a Claude Code (allega i file della cartella `design_handoff_olobuild_tiles/megamenu/`):

---

```
Repo: claudiovinco/olobuild (branch main) — page builder WordPress, Vue 3 + Pinia + PHP.
Tile interessata: Mega Menu → src/config/elements/megamenu.js e src/components/Tiles/MegaMenuTile.vue

Ti allego il pacchetto handoff (cartella megamenu/):
- README_TEMPLATES.md ........ leggi PRIMA questo: indice + come applicare + estensioni frontend
- megamenuTemplates.js ........ 40 template (settings con chiavi reali). FONTE DA IMPORTARE
- megamenu-templates.json ..... stessi dati, valori espansi (per import senza JS)
- megamenuPresets.js .......... ricette dei 12 preset ufficiali (modern-clean … tilt-bar)
- MEGAMENU_PRESETS.md ......... deep-dive: problema preset + 6 interventi + mappa anatomica

VINCOLI NON NEGOZIABILI
1. CHIAVI SALVATE INVARIATE: non rinominare/rimuovere nessuna chiave di megamenu.js
   (margin_*, padding_*, panel_*, border_radius {tl,tr,br,bl}, spacing {top,right,bottom,left}, hover.*, ecc.).
2. TOKEN-FIRST: colori brand via var(--olo-color-*); hex grezzi solo per superfici "decise"
   (bar scure, oro, neon). Niente nuovi hex hardcoded come "primario" delle tile.
3. CHROME ≠ CONTENUTO: l'arancio #e8622a dell'inspector resta com'è (identità del builder).
   I template riguardano il CONTENUTO della tile, che usa i token del cliente.
4. Ogni valore applicato deve restare SOVRASCRIVIBILE dal cliente nell'inspector (il preset/
   template è un punto di partenza, non un lucchetto).

TASK, in quest'ordine
A) BUG da correggere subito in MegaMenuTile.vue → previewItems():
   mega_mode oggi controlla 'auto' e 'all' (valore inesistente) e NON gestisce 'css-class'.
   Allinea al contratto della config: 'auto' (voci con figli) + 'css-class'
   (item.classes.includes('mega-menu')). Vedi MEGAMENU_PRESETS.md §2.

B) CABLARE I PRESET: oggi il componente non legge mai s.preset.
   - Aggiungi src/config/megamenuPresets.js (dal file allegato).
   - Nello store builder, al cambio di settings.preset applica la ricetta:
       Object.assign(settings, structuredClone(MEGAMENU_PRESETS[id] || {}))
     ('custom' = non tocca nulla). Verifica che cambiando preset l'anteprima cambi davvero.

C) TEMPLATE (40): aggiungi src/config/megamenuTemplates.js (dal file allegato) ed esponili
   nel selettore "Template pannelli"/preset del Mega Menu (campo type: 'megapanel-map' o un
   nuovo selettore template, scegli la sede coerente con l'UI esistente). Applicazione =
   stesso pattern di B. Se il plugin importa via JSON/PHP, usa megamenu-templates.json.

D) DEFAULT HARDCODED → TOKEN in megamenu.js: topbar_bg '#1F2937', topbar_text_color '#9CA3AF',
   mobile_bg '#1e1e2e', topbar_right_cta_color '#FFFFFF' → legali ai ruoli cliente
   (dark/light/text) con resolveColor + fallback. Vedi MEGAMENU_PRESETS.md §2 tab #3.

E) ESTENSIONI FRONTEND (17/40, marcate `frontend` nei template e in README_TEMPLATES.md).
   NON obbligatorie per far funzionare i template, ma servono per la resa piena.
   Implementa SOLO queste tre per priorità (le altre lasciale annotate come TODO):
   1. ricerca search_style 'overlay'/'command' (campo a barra + dropdown risultati);
   2. colonna "immagini/promo" nel megapanel-map (griglia thumbnail + card in evidenza);
   3. badge per-voce (es. "NEW") da classe CSS della voce menu WP.

OUTPUT ATTESO
- Diff per file toccato, con commit separati per A / B / C / D / E.
- Una nota finale: quali template sono resi al 100% e quali restano "con TODO frontend".
- Nessuna regressione sulle chiavi salvate (testa salva→ricarica di un Mega Menu esistente).

Procedi: parti da A e B (massimo impatto), poi C, poi D, poi E. Chiedi prima di introdurre
qualunque chiave nuova.
```

---

Vuoi che lo adatti se OLObuild importa i template in un formato specifico (es. tabella WP/JSON via PHP), o che aggiunga uno step di test automatici?

