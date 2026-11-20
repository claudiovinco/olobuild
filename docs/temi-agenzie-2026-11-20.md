# Cinque temi per agenzie di comunicazione (20 nov 2026)

Richiesta dell'utente: «altri 5 temi, tutti strani, tutti con movimentazioni creative, per agenzie di comunicazione, con
tile particolari, senza i pattern ripetitivi usati spesso: chi guarda deve pensare "l'ha fatto un designer", non "è fatto
con un'IA generica". Stupisci tutti!»

Ogni tema ha una trovata che è un comando del builder (passaggio fra le sezioni, cursore, movimento a scorrimento dello
Slider Pro), un'apertura diversa da tutte le altre e tile che nei 211 temi precedenti non c'erano o quasi.

| Tema | Agenzia | Apertura (Slider Pro a scorrimento) | Passaggio delle sezioni | Cursore |
|---|---|---|---|---|
| **Affisso** | affissioni, Napoli | cartellone a otto prismi che girano tre volte sopra Napoli di notte | Prismi | Colla |
| **Diaframma** | produzione video, Roma | le lamelle si aprono sul set, il numero f scende da f/22 a f/1.4 | Diaframma | Ciak |
| **Pollice** | agenzia social, Bologna | un telefono gira un reel, spuntano le notifiche, salgono i cuori | Mazzo di carte (da sotto) | Cuore |
| **Strappo** | art direction, Milano | tre manifesti si strappano uno dopo l'altro sopra il Duomo | Strappo | Taglierino |
| **Torchio** | stampa e design editoriale, Verona | il rullo nero e poi il rosso stampano le righe sul foglio | Rullo | Timbro |

Demo: `https://olotheme.com/olobuild/demo/<id>/`.

## 1. Il motore (1.4.1284–1286)

**Cinque passaggi nuovi** (Sezioni → Effetto sticky), nel gruppo impilato di `trait-olobuild-renderer-page.php`:
- *Prismi*: la sezione si divide in strisce verticali che girano una dopo l'altra (clip-path a strisce e ombra a
  gradiente), destra o sinistra per il verso.
- *Diaframma*: sette lamelle (SVG evenodd) si chiudono sulla sezione e si riaprono sulla successiva; il colore delle
  lamelle è «Colore» della sezione.
- *Mazzo di carte*: la sezione vola via come una carta scartata col pollice (destra → a sinistra, sotto → in alto).
- *Strappo*: bordo frastagliato e lembo che si ripiega; il retro della carta è «Colore».
- *Rullo*: un rullo inchiostrato passa e la sezione dopo compare dove è passato; «Colore» è il rullo.

**Cinque cursori** in `olo-cursori.js` (Avanzate → Effetti mouse): Colla (la pennellessa stende una striscia che
asciuga), Ciak (batte al clic e conta le riprese), Cuore (si riempie sui link, al clic salgono i cuori), Taglierino
(tenendo premuto taglia la pagina), Timbro (al clic lascia una lettera inchiostrata).

## 2. Difetti trovati strada facendo

1. **Il carattere dei livelli dello Slider Pro** (1.4.1289): un livello col carattere «del tema»
   (`var(--olo-font-family-heading)`, `…-mono`) finiva fra apici (`font-family:'var(…)'`) e il browser lo scartava: il
   testo restava nel carattere di sistema. Succedeva anche nei temi precedenti, coi testi mono delle aperture. Ora un
   ruolo del tema esce senza apici e non si cerca fra i caratteri da scaricare.
2. **Classi che si scontrano**: il disegno dello strappo e il cilindro del rullo avevano lo stesso nome della classe del
   gruppo (`olo-h-strappo`, `olo-h-rullo`) e lo rendevano assoluto e invisibile: rinominati `olo-h-lembo`,
   `olo-h-cilindro`.
3. **Il manifesto con lo strappo già visibile**: le due metà a riposo mostravano l'anima bianca della carta. Sopra c'è il
   manifesto intero, che sparisce nell'istante in cui si strappa.
4. **Il taglierino selezionava il testo** (1.4.1293): tenendo premuto per tagliare, il browser evidenziava i titoli sotto
   la lama. Ora, fuori da campi, pulsanti e link, mentre si taglia la selezione è spenta.
5. **Corsivi finti in due tile** (nei temi): Calcolatore a slider e Finder mettono sempre in corsivo la parola in `<em>`
   del titolo, anche coi caratteri che il corsivo non ce l'hanno (Archivo Black, Gabarito): lì il titolo è senza `<em>`.
   Da decidere: un interruttore «Corsivo dell'accento» nelle due tile, come nel Titolo.
6. **Testi da rifare dopo il primo giro**: titolo di «I formati» troppo lungo in Archivo Black; la Step Timeline di
   Diaframma su una colonna (quattro foto enormi in fila) → quattro colonne; il nome e la presentazione del Link in Bio
   chiari sulla scheda chiara; il testo sul cerchio di Strappo che si accavallava; le righe di Torchio al telefono.

## 3. Le tile poco usate messe alla prova

| Tile | Temi prima | Dove | Esito |
|---|---|---|---|
| Link in Bio | 0 | Pollice, pagina «Link» | funziona |
| PDF Pro | 0 | Torchio, home e Lavori (il campionario vero, 8 pagine A5) | funziona, si sfoglia |
| Cartellone | 4 | Affisso, home (le uscite del 2027, formati come generi) | funziona; sottotitolo e titolo vanno messi dritti (il campione era in corsivo) |
| News Ticker | 5 | Affisso, home | funziona in «marquee» |
| Gratta e Scopri | 8 | Affisso, home (il manifesto vecchio da grattare) | funziona con la copertura a immagine |
| Calcolatore a slider | 13 | Affisso, I formati (con la lista scalata) | funziona |
| Scorrimento orizzontale | 7 | Affisso, Campagne | funziona, col dondolio |
| Video Playlist | 3 | Diaframma, home (lo showreel) | funziona coi video della libreria |
| Pellicole | 3 | Diaframma, home (cinque look sulla stessa foto) | funziona |
| Montaggio | 3 | Diaframma, Lavori | funziona |
| Film Reel | 4 | Diaframma, Lavori | funziona |
| Schedule | 8 | Diaframma, Il set (il foglio di chiamata) | funziona |
| Griglia Presenze | 5 | Pollice, home (chi risponde adesso) | funziona |
| Scorrimento verticale | 3 | Pollice, home | funziona |
| Finder | 7 | Pollice, home (che social fa per te) | funziona |
| Grafico, Counter Circle | 17, 7 | Pollice, Casi | funziona |
| Shattered Image | 7 | Strappo, home (sei colonne) | funziona |
| Blend Text | 7 | Strappo, home | funziona |
| Category Rail | 3 | Strappo, Progetti | funziona |
| Reveal Box | 6 | Strappo, Progetti | funziona |
| Testo su tracciato | 15 | Strappo, Studio (il timbro che gira) | funziona |
| Variazione testo | 2 | Torchio, home (Literata, peso e corpo ottico) | funziona |
| Preventivo | 3 | Torchio, La stamperia | funziona |
| Carosello, Overlay Slider | 3, 3 | Torchio, Lavori | funzionano |

Niente Section Header nei titoli delle sezioni (c'è in tutti i 211 temi): occhiello, titolo e testo, impaginati ogni
volta in modo diverso (`scripts/temi/agenzie.py`).

## 4. Le aperture

- **Affisso**: il pannello 2:1 del cartellone, otto prismi per tre facce (24 livelli JPEG ritagliati da tre manifesti
  disegnati con Chrome nei caratteri del tema); ogni prisma gira con `ry` (A da 0 a -90°, B da 90° a 0), uno dopo l'altro
  a 1,8 punti di distanza, due volte. Struttura, lampade e luce delle lampade a parte; un pannello e una struttura solo
  per il telefono.
- **Diaframma**: le lamelle sono un SVG 1600 × 900 col foro al centro, su un livello del 120 %; si ingrandisce fino a 19
  volte e gira di 84° a velocità costante. Il numero f in basso a destra (l'angolo che si scopre per ultimo).
- **Pollice**: telefono (SVG col vetro vuoto) e sotto il livello video verticale; le notifiche sono livelli di testo
  bianchi con l'ombra, che crescono da 0,6 a 1; cinque cuori (icona) salgono e svaniscono.
- **Strappo**: tre manifesti disegnati con Chrome (computer e telefono), ognuno in due PNG col bordo frastagliato e
  l'anima bianca (`agenzie_disegni.strappa`); le metà volano ai lati ruotando, a 25 punti l'uno dall'altro.
- **Torchio**: il foglio (forma) sul video della macchina; il rullo scorre a velocità costante e ogni riga ha la maschera
  destra (`mr`) che si apre nel tratto in cui il rullo passa sopra le righe; poi il rullo rosso col secondo colore.

## 5. Strumenti

`scripts/temi/agenzie_media.py` (foto e video da Pexels), `agenzie_loghi.py`, `agenzie_disegni.py` (cartellone, facce,
lamelle, telefono, manifesti strappati, rulli), `agenzie_pdf.py` (il campionario di Torchio), `agenzie.py` (pezzi comuni),
`gen_affisso.py`, `gen_diaframma.py`, `gen_pollice.py`, `gen_strappo.py`, `gen_torchio.py`.

## 6. Com'è finita

- 1.4.1284–1286: passaggi e cursori; 1.4.1287–1292: i cinque temi «in lavorazione» (solo sottosito), le correzioni.
- 1.4.1293: i cinque temi nella Vetrina e nel selettore, con la schermata del computer, quella del telefono e il video
  640 × 480 di 10 secondi (v1 di tutto, in `olobuild-library/themes/<id>/`); il taglierino che non seleziona più il testo;
  i contrasti corretti (rosso della testina del Montaggio, pomodoro delle scritte piccole e dei passaggi del mouse di
  Pollice, magenta di Strappo scurito a #B0124F). Lo scatto fermo di Torchio è col foglio già stampato.
- Giro di tutte le pagine a 1440 e a 390 (40 pagine) senza errori nella console; contrasti misurati su tutte (restano
  solo testi bianchi sopra foto che lo strumento confronta col fondo della pagina); nessun corsivo finto; i cinque cursori
  provati col mouse; `audit-ui-standard` senza regressioni.
- Strumento delle anteprime: la copia nello scratchpad nasconde i disegni dei passaggi nuovi e rimette intere le sezioni
  (opacità, raggio, `-webkit-clip-path`); da riportare in `olo-booking/bin/theme-previews.py`.
- Pagina di prova `prova-passaggi-2` tolta. Niente committato, niente treno.
