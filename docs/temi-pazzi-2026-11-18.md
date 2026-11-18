# I cinque temi «pazzi» — quaderno di lavoro (18 nov 2026)

Richiesta dell'utente: «cerca di creare altri 5 temi pazzi, che sfuggono alla logica classica dei siti web, esempi: un
video che si perde sullo sfondo, svelando qualcos'altro, un sito che si muove in orizzontale ma un po a destra e un po a
sinistra (…) e che magari sembri procedere a scala, un po in giu e un po in orizzontale cambiando spesso la direzione. O
un sito che mentre scorre ruota su se stesso di qualche grado, proseguendo a cerchio. Usa tile poco usate, originali, così
ne testi anche il funzionamento, più ne usi, più ne verifichi. Segnati tutto».

| Tema | Settore | La trovata | Generatore |
|---|---|---|---|
| Tornanti | Noleggio (e-bike a Bormio) | il sito va a tornanti: a destra, giù, a sinistra, giù | `scripts/temi/gen_tornanti.py` |
| Ruota Libera | Associazioni (ciclofficina a Padova) | le sezioni sul bordo di una ruota che gira | `scripts/temi/gen_ruota_libera.py` |
| Persiana | Immobiliare (Lucca) | il video della facciata si perde sullo sfondo e scopre le stanze | `scripts/temi/gen_persiana.py` |
| Vetrino | Salute (laboratorio a Parma) | lo zoom che non finisce: persona, pelle, cellula, molecola | `scripts/temi/gen_vetrino.py` |
| Doppio Senso | Studi e professioni (traduttori a Trieste) | le due metà della pagina scorrono in senso contrario | `scripts/temi/gen_doppio_senso.py` |

Media: `scripts/temi/cinque_media.py` (Pexels), disegni `cinque_disegni.py`, loghi `cinque_loghi.py`.

## 1. Che cosa sapeva già fare il builder, e che cosa ho aggiunto

**Verificato prima di cominciare** (il motore delle sezioni orizzontali, `trait-olobuild-renderer-page.php`):
- Le sezioni di seguito con «Cover orizzontale» o «Reveal orizzontale» diventano un gruppo alto una schermata: scorrendo
  la vista passa dall'una all'altra, **sempre da destra**. Non esistevano il verso da sinistra né la «scala».
- Il parallasse delle Avanzate (spostamento, rotazione, scala, opacità, sfocatura) vale già per sezioni, righe, colonne e
  tile: due colonne che scorrono in senso contrario si fanno così, senza codice nuovo.
- Lo Slider Pro con lo scorrimento fissato (`scrollTimeline`) e il movimento a tappe dei livelli (`scrollMove`) basta per
  il video che si allontana e per lo zoom continuo. Gli spostamenti sono in px della tela del computer (1440) e sulle tele
  più strette si riducono da soli: le tappe di un livello che esiste solo sul telefono vanno scritte × 2,25 (1440 / 640).

**Aggiunto (1.4.1232):**
- **«Arriva da»** (Sezione → Effetto sticky, con «Cover orizzontale»): destra, sinistra, sotto, sopra. Con direzioni
  diverse il gruppo diventa un *percorso*: i pannelli stanno su un piano, ognuno accanto al precedente dalla parte scelta,
  e la vista ci si sposta sopra frenando a ogni pannello come su un gradino. «Sopra» fa salire la vista mentre scorri in
  giù. Pagina di prova: `olotheme.com/olobuild/prova-percorso/` (noindex, da togliere alla fine).
- **«Ruota»** (nuovo Effetto sticky) con **«Angolo»** (6–90°): le sezioni sul bordo di una ruota enorme col perno sotto
  lo schermo; scorrendo la ruota gira e la sezione di turno arriva dritta in cima, le vicine si vedono inclinate ai lati.
  Destra/Sinistra di «Arriva da» scelgono il verso. Con «riduci il movimento» le sezioni restano una sotto l'altra.
- Al telefono tutti e due restano orizzontali se ogni sezione sta in una schermata (la regola che c'era già).

## 2. Difetti trovati strada facendo, e corretti

1. **Parole fuori dalla colonna** (1.4.1231): una parola lunga in un titolo grande usciva dalla colonna sopra quella
   accanto («Ti richiamiamo» di Capriola sul modulo). `olo-parole-intere.js` rimpiccioliva solo le parole spezzate a metà
   riga; ora anche quelle che escono senza andare a capo (`sborda()`). La Section Header ha `min-width:0` sulle caselle
   della griglia (una casella di griglia si allarga quanto la sua parola più lunga).
2. **Titoli misurati col carattere sbagliato** (1.4.1233): lo script segnava come «visto» un titolo misurato col carattere
   di riserva, più stretto; quando arrivava quello vero il titolo usciva lo stesso (Bialbero, Controtempo, Ring a 100 px).
   Ora all'arrivo dei caratteri si rimisura tutto.
3. **Popup Nascosto** (1.4.1235): il trigger a scorrimento restava sempre acceso, anche con la sequenza di tasti; un popup
   «nascosto» si apriva da solo a metà pagina. Nuovo interruttore «Si apre scorrendo»: spento, si apre solo con la
   sequenza.
4. **Step Timeline**: il campione copiato dai generatori aveva una colonna sola e le foto in 5:4 a tutta larghezza (ogni
   passo alto una schermata). Nel tema: tre colonne, foto 4:3. La tile va bene.
5. **Pallini Cover** in testata: si agganciano al primo gruppo orizzontale della pagina; i nomi delle fermate valgono per
   tutte le pagine, quindi in un tema con gruppi diversi vanno lasciati vuoti.
6. **Finestre senza i colori del tema** (1.4.1236): Popup, Popup Nascosto e il menu laterale del Nav Menu sono finestre
   UIkit, che le sposta in fondo al body, fuori dal `.olo-template`. Dove i colori del tema stanno sul template e non
   sulla radice (le demo, le pagine con uno stile proprio) prendevano il fondo bianco e il pulsante blu di serie. Ora
   hanno la classe `olo-tokens`, che porta gli stessi valori fuori dal template (la convenzione c'era già per le tile
   fisse).
7. **Hotspots senza foto** (1.4.1237): il pannello dei segnaposto era un riquadro tratteggiato di prova con la scritta
   «SCENE»: i segnaposto indicavano il vuoto. Aggiunti Foto, testo alternativo e punto focale (nello Stile).
8. **Contatore Visite** (non corretto, da fare): conta le visite del post della pagina, quindi nelle demo dei temi resta
   sempre a 0, e il numero si scrive quando la pagina si disegna: con la cache a pagina intera conta solo quando la cache
   si rifà. Andrebbe contato con una chiamata a parte (REST) e mostrato via script. Tolto da Ruota Libera.
9. **Corsivo finto nei titoli** (1.4.1238): `titoli_stile.py` sceglieva per alcuni temi la seconda riga «in corsivo» anche
   se il carattere dei titoli il corsivo non lo ha (Bricolage Grotesque, Sora…): il browser la inclinava finta. Ora sceglie
   il corsivo solo se il carattere è in `styles.google_fonts_italic`. I temi già scritti con il corsivo finto restano com'erano
   finché non si rigenerano (da decidere se rigenerarli).
10. **Evo Notes** (1.4.1239) non è una tile da mettere in una sezione: è un livello di note per tutta la pagina (un pulsante
    fisso accende dei numeri agganciati alle sezioni per `html_id`, ognuno apre «prima → ora»). Il pulsante stava a destra,
    sopra «torna in cima»: aggiunto «Lato del pulsante».
11. **Switcher Panel**: il fondo del pannello di serie è il bianco (`panel_bg` = light), non il fondo del tema: nei temi
    va impostato. La foto grande in alto (`hero_image`) è fissa, non cambia con la voce.
12. **Masked Video Hero, Chat Hero, Step Timeline, Terminale**: nati dai blueprint, hanno i testi di prova in inglese nei
    valori di serie: in un tema vanno scritti tutti. Il Terminale ha anche una riga piccola sotto i pulsanti («Prova i
    prodotti su siti demo reali. Apri la demo →», `small_text` / `small_link_text`): se non serve va svuotata.
13. **Colonne in senso contrario impilate** (1.4.1240, nel tema): il parallasse delle Avanzate non ha valori per
    dispositivo (c'è solo «Disattiva su mobile», che non si usa). Sotto i 960 px le due colonne vanno una sotto l'altra e
    continuano a muoversi in senso contrario: la seconda saliva sulla prima, e la colonna che parte più in alto finiva sul
    titolo anche al computer. In Doppio Senso la riga si tiene in cima lo spazio della corsa (`margin_top` = corsa) e la
    seconda colonna, sotto i 960 px, il suo (`margin_top_tablet/mobile` = 1,3 × corsa). Misurato lungo tutto lo
    scorrimento: mai sovrapposte, al telefono si avvicinano da 476 px a pochi px (il movimento contrario si vede).
14. **Lo strumento delle anteprime** (`theme-previews.py`, copia nello scratchpad): nello scatto della pagina intera i
    pannelli del percorso e della ruota restavano dove li aveva messi lo script (uno sopra l'altro), il menu a scomparsa del
    telefono finiva a metà pagina e lo Slider Pro, preso il cambio di misura per un ridimensionamento, rifaceva gli
    ingressi dei livelli (la strada di Tornanti trasparente). Ora prima dello scatto i pannelli tornano in colonna, gli
    elementi fissi fuori dalla finestra si tolgono e lo stato di ogni livello si blocca com'è sullo schermo. Riportato
    nell'originale `T:\TECNICA\olo-booking\bin\theme-previews.py` (non committato).

15. **Cerchi allungati nello Slider Pro** (1.4.1241): con lo scorrimento fissato, in una finestra più alta del 16:9 la scena
    allunga in altezza i livelli che toccano i due bordi della tela (giusto per un video o una foto a tutto campo). Le
    lenti di Vetrino, cerchi più grandi della tela, diventavano ovali: 0,9 a 1440 × 900, 0,75 in 4:3. Ora un livello con
    gli angoli tondi almeno quanto metà del lato corto resta com'è, al centro della composizione (`cerchio()` in
    `scenaAPieno`, `olo-proslider.js`). Misurato dopo: 37440 × 37440 nelle tre finestre.
16. **Livelli solo per il telefono** (1.4.1243, segnalato dall'utente): in Ruota Libera, al computer, le lettere della
    scritta sul fianco della ruota giravano accanto al logo. Le scritte del telefono erano livelli a parte, «nascosti» al
    computer con un riquadro di 0,1 × 0,1 % in alto a sinistra, perché lo Slider Pro sapeva nascondere un livello solo sul
    notebook, sul tablet o sul telefono. Ora c'è «Visibile su Desktop» anche sul computer, e la visibilità si calcola
    formato per formato nella sua fascia, come la eredita l'editor (prima il display:none del tablet valeva anche per il
    telefono, e «Visibile su Mobile» si vedeva nell'editor ma non sul sito). Corretti anche Tornanti e il velo del
    telefono di tutti i temi (`kit.veli`); nei 4335 livelli dei temi nessuno cambia aspetto.
17. **Testi dell'apertura sotto la testata, al telefono** (1.4.1244): l'occhiello di Ruota Libera e «Italiano» di Doppio
    Senso finivano sotto la testata sovrapposta (alta 85 px). Controllo: `scratchpad/cinque/sotto_testata.py`. Al
    telefono le scritte sul copertone di Ruota Libera uscivano dallo schermo: più corte e a ±27°.

## 3. Le tile poco usate messe alla prova

| Tile | Usi prima | Dove | Esito |
|---|---|---|---|
| Pallini Cover (coverdots) | 0 | Tornanti, testata | funziona: segue il gruppo, salta alla fermata al clic |
| Cime (layerscape) | 2 | Tornanti, home | funziona: strati, sole che tramonta, nebbia, stelle |
| Counter Circle | 6 | Tornanti, home | funziona: i cerchi si riempiono entrando |
| Barra Scroll | 1 | Tornanti, home | funziona |
| Step Timeline | 1 | Tornanti, Le bici | va impostata a mano (colonne, proporzione): vedi sopra |
| Configuratore (preventivo) | — | Tornanti, Prenota | funziona |
| Popover | 0 | Ruota Libera, home | funziona: segnaposto sulla foto, la scheda al passaggio e al clic |
| Ghiera | 3 | Ruota Libera, home | funziona: si gira, il pannello cambia con la tacca |
| Cesto Fisico (physicsbin) | 0 | Ruota Libera, home | funziona coi pezzi disegnati (SVG a disco: l'immagine riempie la forma, lo sfondo non si disegna) |
| Lucchetto | 3 | Ruota Libera, home | funziona: rotelle, indizio, codice premio |
| Popup Nascosto | 0 | Ruota Libera, home | funziona dopo le correzioni 3 e 6: si apre scrivendo «drin», coi coriandoli |
| Tab a Icone | 0 | Ruota Libera, L'officina | funziona |
| Contatore Visite | 0 | — | difetto 8: tolto |
| Hotspots | 0 | Persiana, home | funziona dopo la correzione 7 |
| Mixer | 0 | Persiana, home | funziona; un campione chiarissimo sparisce sulla scheda chiara |
| Switcher Panel | 0 | Persiana, home | funziona (vedi 11) |
| Masked Video Hero | 0 | Persiana, Vendi | funziona |
| Pannello flottante | 2 | Persiana, home | funziona col pulsante («button»): figli dentro il pannello |
| Slideshow | 1 | Persiana, Le case | funziona |
| Terminale (terminalhero) | 0 | Vetrino, home | funziona: scrive le frasi una alla volta, immagini ai lati |
| Evo Notes | 0 | Vetrino, home | funziona come livello di note (vedi 10) |
| Avviso (alert) | 0 | Vetrino, Prepararsi | funziona |
| Indice (toc) + Tempo di lettura | 1 + 1 | Vetrino, Prepararsi | funzionano: l'indice si costruisce dai titoli della pagina |
| Chat Hero | 0 | Doppio Senso, home | funziona |
| Pulsante Toggle | 1 | Doppio Senso, home | funziona: la sezione `in-english` passa da 0 a 790 px |
| Blend Text | 6 | Doppio Senso, home | funziona in negativo sopra la foto |
| Parallasse delle colonne (Avanzate) | — | Doppio Senso, home e I servizi | funziona; con le colonne impilate serve lo spazio della corsa (vedi 13) |
| Percorso «Arriva da» + Pallini Cover | nuovo | Tornanti, tutte le pagine | funziona a 1440 e a 390 |
| Ruota | nuovo | Ruota Libera, tutte le pagine | funziona a 1440 e a 390 |

## 4. Le aperture

- **Tornanti**: la strada a tornanti disegnata (SVG) sopra il video della valle, la bici (livello immagine) ne segue i
  tratti con quattordici tappe in px della tela; la quota passa da 1225 a 2757 m.
- **Ruota Libera**: una ruota di bici (SVG 1000 × 1000) spunta dal fondo e gira (rotazione a scorrimento); tre scritte sul
  fianco del copertone girano con lei: il perno è il centro della ruota, scritto in percentuale del riquadro della scritta
  (`transform-origin: 50% 2077%`), così vale su ogni tela.
- **Persiana**: il livello video della facciata copre la tela e scorrendo va da scala 1 a 0,05 salendo di lato; dietro, il
  video del salotto (sfondo della diapositiva).
- **Vetrino**: cinque livelli, volto e quattro lenti tonde (riquadri quadrati con gli angoli tondi al massimo), con
  l'andamento «costante» (la scala cresce in proporzione): ogni lente compare minuscola dentro la precedente.
- **Doppio Senso**: le due metà (video della penna e della città riflessa) scorrono in senso opposto; «Doppio» scende e
  «Senso» sale fino alla stessa riga. Sul telefono gli spostamenti valgono il 44 %: i due titoli partono più vicini.
## 5. Com'è finita

- 1.4.1240: i ritocchi del giro (Doppio Senso, Vetrino, Tornanti). 1.4.1241: i cerchi dello Slider Pro. 1.4.1242: i cinque
  temi nella Vetrina e nel selettore, con la schermata del computer, quella del telefono e il video 4:3 (v1 di tutto,
  nella libreria `olobuild-library/themes/<id>/`). Verificato dal vivo: tutti e cinque nella Vetrina, ognuno col suo
  bollino e il suo video.
- Giro finale di tutte le pagine a 1440 e a 390 per i cinque temi, senza errori nella console.
- Pagina di prova `prova-percorso` tolta dal sottosito (404).
- 1.4.1242 col treno su tutti i siti (commit 4525b0d7); 1.4.1243–1245 (commit cd9150a6) col difetto 16 e 17 e le anteprime v2 di Ruota Libera e del telefono di Doppio Senso.
- Da fare: il Contatore Visite (difetto 8); decidere se rigenerare i temi già scritti col corsivo finto (difetto 9).
