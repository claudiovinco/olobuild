# Menu mobile moderno — piano (Mega Menu / Site Header)

Richiesta (4 ott 2026): «voglio più controlli sul menu mobile, cerca menu mobile moderni dalla
concorrenza e trasforma la tile in modo che possa funzionare in modo moderno». Con due segnalazioni
collegate: il selettore lingua nel menu mobile è brutto, e la sua tendina lì non funziona.

## 1. Cosa fa oggi la tile

- **Tre stili**: Off-canvas (laterale sx/dx o dall'alto, variante a tutto schermo), Dropdown (scende
  dalla barra), Fullscreen. Breakpoint 768 / 1024 / 1200.
- **Hamburger**: 10 animazioni, dimensione, colore.
- **Sottomenu**: solo a fisarmonica. Indicatore con 8 stili, posizione, dimensione, colore.
- **Voci**: dimensione, colore, colore accento, separatore, padding; font solo nel Fullscreen.
- **Contenuti**: logo mobile, ricerca (icona o a tutta pagina), numeri progressivi, testo e link in
  fondo (solo Fullscreen), pulsanti dei link extra, social, selettore lingua.
- **Animazioni**: apertura (Fullscreen e Off-canvas a tutto schermo), voci in sequenza (stessi due).
- **Comportamento**: Esc chiude, il clic sul fondo chiude l'Off-canvas, `overflow:hidden` sul body.

### Difetti trovati

1. Il markup dell'Off-canvas esce SEMPRE, anche con Dropdown o Fullscreen (nascosto dal CSS): menu
   duplicato nel codice della pagina.
2. Nel Dropdown e nel Fullscreen il clic su una voce con sottomenu apre il sottomenu e basta: la
   pagina della voce non si raggiunge (l'Off-canvas la ripete come prima voce, gli altri no).
3. Nessun comportamento da dialog: il fuoco resta sulla pagina, Tab esce dal pannello, alla chiusura
   non torna all'hamburger; nome dell'hamburger fisso.
4. `overflow:hidden` sul body: su iOS la pagina scorre lo stesso e alla chiusura salta.
5. Nell'Off-canvas i link non chiudono il pannello: un'ancora della stessa pagina lo lascia aperto.
6. Selettore lingua: nel pannello è sempre forzato «in linea» con le caselle della tile; la tendina
   non si può avere, e con il raggio vuoto le caselle erano ad angoli vivi (corretto nella 1.4.502).
7. Niente margini di sicurezza iOS (notch, barra Home), altezze in `vh`.

## 2. Cosa fanno i migliori (ricerca, 4 ott 2026)

Fonti: Elementor, Bricks, Breakdance, Oxygen, Divi 5, YOOtheme, Kadence, Blocksy, GeneratePress,
Webflow, Framer, Max Mega Menu, JetMenu, QuadMenu, WP Mobile Menu, Superfly; Apple, Vercel, Linear,
Airbnb provati a 375 px. I segni di un menu mobile moderno:

- pannello che si comporta da **dialog** (fuoco dentro e trattenuto, Esc, ritorno all'hamburger);
- **sottomenu a pannelli con «Indietro»** e titolo del livello (Bricks, JetMenu, Apple), oltre alla
  fisarmonica; sezione corrente già aperta (Max Mega Menu);
- **tipografia grande**, aree di tocco di almeno 44 px;
- **voci in sequenza**, chiusura più rapida dell'apertura (Apple);
- **fondo sfocato** al posto del grigio pieno (Apple; nessun builder lo offre);
- hamburger che diventa X, con **nome che cambia** («Chiudi il menu»);
- **CTA sempre visibile**, fissa in fondo al pannello;
- **blocco dello scorrimento senza salti**, `dvh`, `overscroll-behavior: contain`, **safe-area** iOS;
- **chiusura al clic su un'ancora** della stessa pagina;
- pannello **dal basso** (bottom sheet, solo Bricks) e **swipe per chiudere** (WP Mobile Menu);
- larghezza del pannello, colore del fondo, etichetta «Menu» accanto all'hamburger, hamburger in un
  cerchio o in una pillola (Kadence, Blocksy, Breakdance);
- **lingua**: fino a 4 lingue una riga di pillole con il codice, oltre un elenco con i nomi nella
  lingua stessa e la spunta; in testa o in fondo al pannello; niente redirect automatico.

## 3. Cosa si fa (fatto nella 1.4.503: M1, M2, M3)

Le chiavi salvate restano quelle di oggi: i template esistenti continuano a funzionare, i controlli
nuovi partono dal comportamento attuale dove cambierebbero l'aspetto.

### M1 — Comportamento (tutti gli stili)
- Pannello come dialog: `role="dialog"`, `aria-modal`, fuoco sulla prima voce, Tab trattenuto, Esc
  chiude e riporta il fuoco all'hamburger; l'hamburger dice «Apri il menu» / «Chiudi il menu».
- Blocco dello scorrimento senza salti (body fisso con la posizione salvata), `overscroll-behavior:
  contain`, altezze `100dvh`, margini `env(safe-area-inset-*)`.
- Ogni link del pannello lo chiude; un'ancora della stessa pagina chiude e scorre.
- Esce solo il pannello dello stile scelto (via il doppione dell'Off-canvas).
- Voci in sequenza in tutti gli stili; la chiusura dura meno dell'apertura.

### M2 — Sottomenu
- **Sottomenu**: Fisarmonica (oggi) · A pannelli, con «Indietro» e titolo · Sempre aperti.
- La pagina della voce genitore resta raggiungibile in ogni stile (prima voce del sottomenu).
- **Apri la sezione corrente**: il sottomenu della pagina in cui si è parte aperto.

### M3 — Pannello, contenuti, aspetto
(Allineamento delle voci lasciato fuori: con le frecce a destra il centrato non regge.)
- **Larghezza pannello** (Off-canvas), **Fondo**: colore e **sfocatura**.
- **Dal basso** (bottom sheet, con maniglia) come direzione dell'Off-canvas; **swipe per chiudere**
  sui pannelli laterali e dal basso.
- **Pulsanti fissi in fondo** al pannello (i link extra con «Stile pulsante»).
- **Lingue nel menu mobile**: Come la tile · Pillole con il codice · Elenco con i nomi · Bandiere
  senza casella · Tendina; **in testa** o **in fondo**.
- **Hamburger**: etichetta («Menu») e fondo (nessuno, cerchio, quadrato arrotondato, pillola).
- **Voci**: font, peso e allineamento in tutti gli stili.
- **Anteprima nel builder**: interruttore per vedere il pannello aperto mentre lo si modifica.

### Fuori da questo giro
- Barra in basso con 3-5 destinazioni (Airbnb): è una tile a sé, da proporre dopo.
- Header mobile con contenuti propri (Kadence, Blocksy): oggi c'è logo mobile + ricerca + hamburger.

## 4. Verifica
- Banco L1 su mosaic: le differenze attese sono nel markup del menu mobile (doppione tolto,
  attributi da dialog); nient'altro.
- Telefono simulato (Chrome headless, 390×844, tocco) su mosaic: apertura, fuoco, Tab, Esc, ancore,
  sottomenu nei tre modi, swipe, bottom sheet, lingua nei cinque formati; foto prima/dopo.
- olotutor.com (stile Fullscreen): confronto delle 33 pagine e foto del menu aperto.
