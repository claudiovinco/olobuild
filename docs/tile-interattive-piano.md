# Tile «Interattivo»: analisi e piano di recupero

Richiesta utente, 5 ott 2026: «analizzare la situazione di tutte le tile "interattivo", tante sembrano
senza senso ma magari con delle modifiche si possono recuperare per farle usare veramente dai
costruttori di siti web».

Metodo: per ognuna delle 34 tile visibili nella categoria sono stati letti il renderer PHP (che è anche
l'anteprima del builder) e il config dell'inspector, contando l'uso nei temi (`assets/data/themes`).
Domande: cosa fa per il visitatore, se funziona davvero (controlli che non agiscono, risultati
inventati, pulsanti che non portano da nessuna parte), che contenuto di partenza ha, se serve a chi
costruisce un sito qualunque, cosa farne. I difetti più gravi sono stati ricontrollati a mano.

## Il quadro in breve

- **Una tile non esiste**: Off-Canvas è in palette con 15 controlli ma non ha un renderer
  (`class-offcanvas-tile.php` manca): sulla pagina non esce niente.
- **Molte sono nate da un tema e non sono state rese generiche**: testi di un hotel, una palestra,
  una gelateria, il redesign di Clod, il sito di OLOtheme; in tre casi in inglese; in uno con un
  marchio reale (HeyConad nei Tab a Icone).
- **Molte simulano un'azione che non avviene**: il Builder ha «Aggiungi al carrello» che porta a `#`,
  Availability dà un verdetto che non va da nessuna parte, gli Hotspots dei temi dicono «Shop the
  kit» senza link, il Popup promette la newsletter senza modulo.
- **Errori che il visitatore vede**: Timezone sbaglia di un'ora per metà anno (niente ora legale),
  Scaler somma grammi e pezzi, Mixer conta come nero ogni colore scritto come token (3 temi su 5),
  Timeline mette «In arrivo» su una tappa del 2019, il Popover nasce trasparente.
- **Controlli che non agiscono** in quasi metà delle tile: «Intensità effetto» non è letta da nessun
  renderer (6 tile), bagliore e prompt del titolo puntano a classi che il markup non ha (6 tile), menu
  «Stile» che scrivono chiavi mai lette (Cesto Fisico, Popover, Pulsante Toggle, Pannello).
- **Il canvas del builder non mostra le tile che caricano librerie proprie**: il Grafico (Chart.js) e
  le tile OLOX non si vedono mentre si modifica, perché l'iframe non carica quegli script.
- **Doppioni**: tre tile per i marker su immagine, tre per le schede, quattro slider, due popup, tre
  «calcolatori» a slider.
- **Le frequenze dei popup non funzionano**: Popup e Popup Nascosto ricordano «già visto» con un id
  che cambia a ogni pagina, quindi «una volta per sessione» e «una volta per sempre» non limitano niente.

## Tile per tile

Utilità = per un costruttore di siti qualunque. Proposta: **tenere** (correggere i difetti),
**fondere** in un'altra, **trasformare** in uno strumento vero, **nascondere** dalla palette (le pagine
che la usano continuano a funzionare).

| Tile | Cosa fa | Stato | Utilità | Proposta |
|---|---|---|---|---|
| Fisarmonica | domande e risposte, schema FAQ | funziona; Ombra e font del titolo non agiscono; default che parlano di Olobuild | alta | tenere |
| Finder | chip → scheda risultato (scritta a mano) | funziona; due controlli Bordo in conflitto | alta (18 temi) | tenere; in futuro chip = categorie del sito |
| Grafico | grafici Chart.js | non si vede nel canvas; colori a token letti male (colori sbagliati); tooltip errato su barre orizzontali e radar | alta | tenere, riparare per primo |
| Popup | modale da pulsante o automatica | frequenza finta; bordo, 3 regole e ombra personalizzata che non agiscono; default «newsletter» senza modulo | alta | tenere, assorbe Popup Nascosto |
| Overlay Grid | griglia di card foto con testo sopra | nastrino senza fondo nelle tile nuove; colori fissi; testi da hotel | alta (21 temi) | tenere, spostare in Gallerie |
| Overlay Slider | slider con testo sopra | colonne per tablet e telefono ignorate; preset «testo sotto» illeggibile | alta | tenere, slider unico |
| Timeline | timeline ricca, 5 layout | stati «Fatto/In corso/In arrivo» inventati dallo scroll; segnaposto visibile al pubblico; default sulla storia di Olobuild | alta (6 temi) | tenere |
| Switcher | schede UIkit | 2 controlli e 2 effetti che non agiscono; contenuto solo testo | alta | tenere come tile «Schede» unica |
| Hotspot | pin su immagine con fumetto | icona mostrata come testo; alt vuoto; segnaposto visibile al pubblico; preset senza effetto | media | tenere come tile marker unica |
| Builder | righe +/− con totale | il totale non va da nessuna parte; prezzi arrotondati e all'americana | media (9 temi) | trasformare: preventivo/configuratore che invia la scelta (modulo, WhatsApp, mail, carrello) |
| Projector | slider → valore calcolato | numeri all'americana; valuta messa anche sulle notti | media (6 temi) | trasformare: «Calcolatore a slider» generico |
| Reveal Box | due facce, la seconda al passaggio | bordo e padding non agiscono; solo mouse, niente tastiera | media | tenere, assorbe Flip Card |
| Pannello flottante | contenitore fisso sullo schermo | in modalità pulsante non si modifica nel canvas; la X in modalità «sempre» lo chiude per sempre | media | tenere |
| Dark Mode Toggle | chiaro/scuro del sito | nei temi classici al ricaricamento torna chiaro; icone scelte ignorate | media | tenere |
| Barra Scroll | progresso di lettura | percentuale illeggibile; colore di base invisibile; sotto la barra admin | media | tenere |
| Pulsante Toggle | mostra/nasconde una sezione | nel canvas la sezione bersaglio sparisce e non si modifica; preset che non agiscono | media | tenere |
| Switcher Panel | schede con immagine grande | animazione «scale» inesistente; testi da ristorante, link a `#` | media | fondere in Switcher |
| Tab a Icone | pillola di icone + scheda | marchio reale nei default; link solo dentro il testo | media | fondere in Switcher |
| Panel Slider | carosello di card | Bordo card senza effetto; colonne per dispositivo ignorate | media | fondere in Overlay Slider |
| Pannello | card con immagine e testo | «Giustificato» scartato; preset senza effetto; non è interattiva | media | spostare tra le card |
| Popover | pin su immagine con fumetto | il fumetto nasce trasparente; preset che cambiano solo l'ombra | media | fondere in Hotspot |
| Gratta e Scopri | pellicola da grattare | il premio si legge nel sorgente; nessun codice | bassa | trasformare in «Coupon da grattare» (codice + copia) o nascondere |
| Availability | griglia giorni × fasce → verdetto | il verdetto non porta a niente; testi in inglese da palestra | bassa | trasformare in quiz a punteggio con un link per esito, o nascondere |
| Scaler | porzioni → quantità | totale sbagliato (somma unità diverse) | bassa | fondere nel Calcolatore a slider |
| Timezone | ore nelle città | niente ora legale: un'ora sbagliata per metà anno; città in inglese | bassa | riscrivere con i fusi veri o nascondere |
| Popup Nascosto | popup a un punto della pagina, uscita, sequenza di tasti | frequenza finta; uno scroll veloce lo salta | bassa | fondere in Popup |
| Hotspots | marker su un pannello astratto, senza immagine | nessun link; azioni finte nei temi | bassa (5 temi) | fondere in Hotspot |
| Mixer | campioni → colore medio | i colori a token contano come nero (3 temi su 5); non produce niente di usabile | bassa (5 temi) | correggere per i temi, nascondere |
| Pallini Cover | pallini per lo scorrimento orizzontale | invisibile nel canvas; serve solo a un motore usato da una pagina | bassa | nascondere (o spostare in Navigazione) |
| Evo Notes | note numerate sulle sezioni | default del redesign di Clod; su altre pagine i marker cadono a caso | nessuna | nascondere |
| Cesto Fisico | oggetti che cadono e si lanciano | menu Stile e Ombra che non agiscono | nessuna (giocattolo) | nascondere |
| Scena minigioco | 13 scene: Forza 4, quiz, radar… | nel canvas è un riquadro vuoto (CSS e JS OLOX non caricati); testi del sito OLOtheme | nessuna fuori da olotheme.com | nascondere |
| Off-Canvas | — | **non esiste**: nessun renderer | nessuna | nascondere subito; il cassetto laterale lo fa il Pannello flottante |
| Ricerca con filtri | barra di ricerca | **resa vera nella 1.4.505** | alta | fatto |

## La Scena minigioco

È il pezzo «giocabile» del sito olotheme.com: 13 scene (Forza 4 contro il computer, «acchiappa gli
imprevisti», indovina la lingua, radar, oblò 360°, quiz sui prodotti OLO, un modulo contatti che apre
una mail) e 6 vetrine animate. I testi parlano di OLOtheme («Costruisci il sito perfetto», Trento,
info@olotheme.com) e non si possono cambiare, tranne poche righe. Le usano solo le pagine di
olotheme.com (7 scene nella pagina «Experience» e una vetrina per prodotto).

Nel builder appare come un riquadro rosa vuoto con tre righe di testo perché il canvas non carica il
CSS e il JS delle tile OLOX (vale per tutte e 18), e anche caricandoli il gioco partirebbe una volta
sola e il builder ne intercetterebbe i clic.

A un costruttore di siti qualunque non serve: un gioco a tema non si adatta a un'altra attività, e
renderlo generico vorrebbe dire riscriverlo. Proposta: **nasconderla dalla palette** (olotheme.com
continua a funzionare) e, a parte, far caricare gli asset OLOX nel canvas, così chi modifica
olotheme.com vede le sue pagine. Delle idee dentro la scena, due meritano una tile generica: il quiz a
punteggio (vedi Availability) e il modulo «a frase» («Ciao, sono ___ e vorrei ___»), che starebbe
meglio come stile del modulo contatti.

## Piano a ondate

**I1 — Togliere ciò che inganna** (una versione, rischio basso) — **FATTA nella 1.4.506-507** (5 ott
2026): provata nel browser su mosaic (popup una volta per sessione; grafico disegnato anche senza
Chart.js precaricato, come nel canvas; Timezone giusta a ottobre e a gennaio simulato, Mumbai alla
mezz'ora; Mixer con i colori del tema). La chiave stabile è `Olobuild_Tile_Base::chiave_stabile()`,
dall'id del nodo che il renderer imposta in `$nodo_in_resa` prima di `render()`.
- `hidden` per Off-Canvas, Scena minigioco, Evo Notes, Cesto Fisico, Pallini Cover: spariscono dalla
  palette, le pagine che le usano restano uguali.
- Popup e Popup Nascosto: id stabile (quello della tile) per ricordare «già visto», così le frequenze
  funzionano.
- Grafico: Chart.js caricato nel canvas, colori a token risolti prima di disegnare, tooltip corretto.
- Timezone: ora legale vera (fusi IANA con `Intl`), oppure `hidden` finché non è riscritta.
- Mixer: legge il colore calcolato dal browser (i temi che lo usano tornano giusti).

**I2 — Correggere le tile utili** (una versione per gruppo)
- Fisarmonica, Finder, Popup, Overlay Grid, Overlay Slider, Timeline, Switcher, Hotspot, Reveal Box,
  Pannello flottante, Dark Mode, Barra Scroll, Pulsante Toggle: i difetti delle schede sopra, testi di
  partenza neutri in italiano, colori a token, via i controlli che non agiscono.

**I3 — Una tile per funzione** (fusioni, con migrazione dei dati salvati)
- Marker su immagine: Hotspot ← Popover, Hotspots.
- Schede: Switcher ← Switcher Panel, Tab a Icone.
- Slider: Overlay Slider ← Panel Slider (poi Carousel e Slideshow, già nel piano
  `audit_results/MIGLIORIE_OLOBUILD_2026-09.md`).
- Popup ← Popup Nascosto (attivazioni «a questo punto della pagina» e «sequenza di tasti»).
- Calcolatore a slider ← Projector, Scaler.

**I4 — Trasformare in strumenti veri**
- Builder → **Preventivo**: la scelta arriva a un modulo, a WhatsApp, a una mail o al carrello.
- Availability → **Quiz a punteggio**: domande, punteggio, un esito con il suo link.
- Gratta e Scopri → **Coupon da grattare**: codice, pulsante copia, ricorda se è già grattato.

Ogni ondata: stesse regole delle altre (chiavi salvate invariate, banco su mosaic, prova d'uso nel
browser, olotutor.com allineato).
