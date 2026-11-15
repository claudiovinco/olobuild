# -*- coding: utf-8 -*-
"""Arricchimenti della seconda passata (14 nov 2026), primo blocco: da Acqua Viva a Il Pozzo. Formati in arricchisci2.py."""

A = {
    'acqua-viva': {
        'acque': [
            ('schede', 'Le tre acque', 'Ogni vasca', 'ha la sua temperatura.', [
                ('38°', 'gradi', 'La vasca grande', '', 'All’aperto, con la valle davanti: l’acqua della sorgente come esce dalla roccia.', 'sun'),
                ('34°', 'gradi', 'La vasca salata', '', 'Più leggera e piena di sali: si galleggia senza fare niente.', 'droplets'),
                ('16°', 'gradi', 'La vasca fredda', '', 'Pochi secondi dopo il vapore: la pelle si chiude e si riparte.', 'snowflake')]),
            ('frase', 'L’acqua arriva da sola, calda, dalla stessa roccia da cui la prendevano i romani. Noi le abbiamo solo '
             'costruito attorno un posto per stare in silenzio.', 'La famiglia che tiene le terme')],
        'prenota': [
            ('schede', 'Cosa mettere in borsa', 'Quello che serve', 'e quello che c’è già.', [
                ('01', '', 'Il costume', '', 'L’unica cosa da portare: tutto il resto è compreso nell’ingresso.', 'shirt'),
                ('02', '', 'Accappatoio e telo', '', 'Li trovi piegati nell’armadietto, con le ciabatte di spugna.', 'check'),
                ('03', '', 'Le tisane', '', 'Salvia, malva e rosmarino, calde a ogni ora nella sala del camino.', 'coffee')])]},

    'ago-e-ancora': {
        'prenota': [
            ('righe', 'Chi c’è in studio', 'Quattro mani', 'e i loro stili.', [
                ('01', 'Teo Falleni', 'Tradizionale e old school', 'linee piene'),
                ('02', 'Nadia Bracci', 'Botanico e fine line', 'aghi sottili'),
                ('03', 'Iris Conti', 'Lettering e calligrafia', 'su misura'),
                ('04', 'Bruno Gori', 'Giapponese e grandi pezzi', 'più sedute')], '@prenota')]},

    'argilla': {
        'laboratorio': [
            ('passi', 'Dalla terra al forno', 'Cinque giorni', 'per una ciotola.', [
                ('01', 'Il tornio', 'La palla d’argilla si centra a mano e si apre col pollice: in dieci minuti è una ciotola, ma molle.'),
                ('02', 'L’asciugatura', 'Tre giorni all’ombra, girata ogni sera: se asciuga in fretta si crepa.'),
                ('03', 'Lo smalto', 'Immersa nello smalto oliva o sabbia, col pollice che lascia il segno sul piede.'),
                ('04', 'Il forno', 'A milleduecento gradi, poi il forno si raffredda chiuso fino al mattino.')]),
            ('faq', 'Il laboratorio', 'Le domande', 'di chi viene a provare.', [
                ('Serve saper fare qualcosa?', 'No. La prima sera si impara a centrare l’argilla, ed è già tanto.'),
                ('Mi porto a casa quello che faccio?', 'Sì, dopo la cottura: lo smaltiamo noi e lo trovi pronto in bottega la settimana dopo.'),
                ('Come mi vesto?', 'Con qualcosa che si può sporcare. I grembiuli li abbiamo noi.')])]},

    'cantiere-aperto': {
        'come': [
            ('pila', 'Il cantiere', 'Come lo seguiamo', 'giorno per giorno.', [
                ('Prima', 'Il programma', 'scritto', '<p>Le settimane del cantiere su un foglio solo: chi entra, quando e per fare cosa.</p>', ''),
                ('Durante', 'Le foto', 'della sera', '<p>Ogni sera il capocantiere manda tre foto del lavoro del giorno. Se qualcosa slitta, lo sai subito.</p>', ''),
                ('Alla fine', 'La pulizia', 'e le chiavi', '<p>L’impresa di pulizie passa prima della consegna: entri in una casa finita, non in un cantiere.</p>', '')])],
        'preventivo': [
            ('schede', 'Nel preventivo', 'Cosa trovi', 'scritto nero su bianco.', [
                ('01', '', 'Le voci, una per una', '', 'Demolizioni, impianti, muri, pavimenti, finiture: con le quantità e il prezzo di ognuna.', 'file-text'),
                ('02', '', 'La data di consegna', '', 'Con la penale scritta, se tardiamo noi.', 'calendar'),
                ('03', '', 'Gli imprevisti', '', 'Una voce a parte, che usiamo solo se serve e ti mostriamo prima di usarla.', 'shield-check')])]},

    'cantina-vallerose': {
        'vini': [
            ('listino', 'La carta', 'I vini', 'di questa vendemmia.', [
                ('Valpolicella Classico', 'Fresco, ciliegia e pepe: il vino di tutti i giorni.', 'in cantina'),
                ('Ripasso Superiore', 'Ripassato sulle vinacce dell’Amarone, più pieno e morbido.', 'in cantina'),
                ('Amarone Classico', 'Uve appassite sui graticci fino a gennaio, poi tre anni in botte.', 'in cantina'),
                ('Recioto', 'Il vino dolce delle feste, da bere col pandoro o da solo.', 'in cantina')],
             'Si comprano in cantina dopo la degustazione, o si prenotano scrivendoci.'),
            ('dettagli', 'pexels-2946621.jpg', 'Le botti di rovere in cantina', 'In botte', 'Il tempo', 'che serve.', [
                ('Valpolicella', 'acciaio, pochi mesi'), ('Ripasso', 'botte grande, un anno'), ('Amarone', 'botte grande, tre anni'),
                ('Recioto', 'barrique, un anno')])],
        'cantina': [
            ('carte', 'L’anno in vigna', 'Quattro stagioni', 'di lavoro.', [
                ('pexels-6049433.jpg', 'L’inverno', 'La potatura', 'Si taglia a mano, pianta per pianta, lasciando i tralci che daranno l’uva.'),
                ('pexels-39351.jpg', 'L’estate', 'Il verde', 'Si tolgono le foglie attorno ai grappoli perché prendano il sole.'),
                ('pexels-39351.jpg', 'L’autunno', 'La vendemmia', 'In cassetta, a mano, nelle mattine fresche: le uve migliori vanno ad appassire.'),
                ('pexels-2946621.jpg', 'Il riposo', 'In cantina', 'Il vino nuovo si travasa e si aspetta: ogni annata ha i suoi tempi.')])],
        'visite': [
            ('passi', 'La visita', 'Un’ora e mezza', 'fra vigna e cantina.', [
                ('01', 'La vigna', 'Un giro fra i filari sopra la cantina, con la valle sotto.'),
                ('02', 'I graticci', 'La stanza dove l’uva dell’Amarone appassisce fino a gennaio.'),
                ('03', 'Le botti', 'La cantina sotto terra, fresca tutto l’anno.'),
                ('04', 'La degustazione', 'Quattro vini col pane e il formaggio della valle.')])]},

    'casa-scogliera': {
        'camere': [
            ('righe', 'Le camere', 'Sei stanze,', 'sei viste diverse.', [
                ('01', 'La Scogliera', 'Terrazza sul mare', 'due persone'),
                ('02', 'Il Faro', 'Vista sul promontorio', 'due persone'),
                ('03', 'La Grotta', 'Volte in pietra, fresca d’estate', 'due persone'),
                ('04', 'Il Giardino', 'Al piano terra, fra i limoni', 'tre persone'),
                ('05', 'La Vela', 'Sotto il tetto, con l’abbaino', 'due persone'),
                ('06', 'La Famiglia', 'Due stanze comunicanti', 'quattro persone')], '@camere')],
        'esperienze': [
            ('pila', 'Le giornate', 'Da fare', 'senza fretta.', [
                ('Mattina', 'In barca', 'alle grotte', '<p>Il pescatore del paese porta fuori quattro persone alla volta, prima che arrivi il vento.</p>', ''),
                ('Pomeriggio', 'Il sentiero', 'degli dei', '<p>Due ore a piedi sopra il mare, con la merenda nello zaino che ti prepariamo noi.</p>', ''),
                ('Sera', 'La cena', 'in terrazza', '<p>Pesce del giorno e vino della costiera, sul tavolo lungo davanti al tramonto.</p>', '')]),
            ('citazione', 'Ci hanno prenotato la barca la sera prima, e alle otto eravamo soli in una grotta azzurra. Il resto '
             'della vacanza l’abbiamo passato a raccontarlo.', 'Giulia e Paolo', 'luna di miele, giugno')]},

    'circo-pappagallo': {
        'corsi': [
            ('carte', 'Le discipline', 'Quattro modi', 'di stare in pista.', [
                ('pexels-8895422.jpg', 'Giocoleria', 'Dai 6 anni', 'Palline, clave e diabolo: si comincia con due palline e si finisce a passarsele in coppia.'),
                ('pexels-19880215.jpg', 'Aerea', 'Dagli 8 anni', 'Tessuti e trapezio a un metro da terra, sempre sopra i materassi e con l’istruttore accanto.'),
                ('pexels-30218076.jpg', 'Clown', 'Dai 5 anni', 'Facce, cadute finte e il naso rosso: si impara a far ridere senza dire una parola.'),
                ('pexels-17944662.jpg', 'Equilibrio', 'Dai 7 anni', 'Filo basso, rulla e monociclo: l’equilibrio arriva prima di quanto si pensa.')])],
        'spettacoli': [
            ('passi', 'Il saggio', 'Come arriviamo', 'al grande giorno.', [
                ('01', 'Il numero', 'A marzo ogni gruppo sceglie il suo numero con l’istruttore.'),
                ('02', 'Le prove', 'Ad aprile si prova sotto il tendone, con la musica e le luci.'),
                ('03', 'I costumi', 'A maggio li cuciamo insieme ai genitori, il sabato mattina.'),
                ('04', 'In pista', 'Il 30 maggio, al parco: due spettacoli, uno al pomeriggio e uno alla sera.')]),
            ('frase', 'Il primo inchino davanti a trecento persone non se lo scorda nessuno: né i bambini, né i genitori in '
             'prima fila.', 'Gli istruttori del Circo Pappagallo')],
        'feste': [
            ('schede', 'La festa', 'Cosa c’è', 'nel pomeriggio.', [
                ('01', '', 'Il clown', '', 'Arriva in costume e tiene la festa per due ore, con i giochi e le gag.', 'smile'),
                ('02', '', 'Il laboratorio', '', 'I bambini provano le palline e il piatto cinese, e se li portano a casa.', 'sparkles'),
                ('03', '', 'Il piccolo spettacolo', '', 'Alla fine i bambini fanno il loro numero davanti ai genitori.', 'star')])]},

    'codice-ragazzi': {
        'iscrizioni': [
            ('carte', 'I corsi', 'Tre percorsi,', 'secondo l’età.', [
                ('pexels-3401402.jpg', 'Primi passi', '8 — 10 anni', 'Si programma a blocchi: giochi, storie animate e il primo robot che segue una linea.'),
                ('pexels-7868834.jpg', 'Robotica', '11 — 13 anni', 'Si monta un robot vero, con i sensori, e lo si programma in Python.'),
                ('pexels-30720501.jpg', 'Maker', '14 — 16 anni', 'Stampa 3D, circuiti e un progetto tutto loro da presentare alla fine dell’anno.')]),
            ('citazione', 'Mio figlio ha smesso di chiedere il telefono il giovedì: è il giorno del corso, e torna a casa '
             'raccontando cosa ha fatto fare al suo robot.', 'Chiara', 'mamma di Pietro, 12 anni')]},

    'conto-chiaro': {
        'come': [
            ('pila', 'Un anno con noi', 'Cosa succede', 'mese per mese.', [
                ('Gennaio', 'Il conto', 'dell’anno', '<p>Ti diciamo quanto metterai da parte per le tasse, mese per mese, così a giugno non ci sono sorprese.</p>', ''),
                ('Giugno', 'La dichiarazione', 'pronta', '<p>La prepariamo noi con i dati che hai già caricato. Tu leggi, chiedi, firmi.</p>', ''),
                ('Ogni mese', 'Le fatture', 'controllate', '<p>Quelle che emetti e quelle che ricevi: se manca qualcosa, ti scriviamo prima noi.</p>', '')]),
            ('faq', 'Il servizio', 'Le domande', 'di chi apre la partita IVA.', [
                ('Posso passare da voi a metà anno?', 'Sì: chiediamo noi i documenti al commercialista di prima e pensiamo al resto.'),
                ('Chi mi risponde?', 'Sempre la stessa persona, in chat o al telefono, entro un giorno lavorativo.'),
                ('Serve venire in studio?', 'No, si fa tutto online. Se preferisci vederci, a Milano ci trovi.')])],
        'contatti': [
            ('schede', 'Per scriverci', 'Tre modi', 'per una risposta.', [
                ('01', '', 'La chat', '', 'Dall’area clienti, con la persona che segue la tua partita IVA.', 'message-circle'),
                ('02', '', 'La telefonata', '', 'Prenoti un quarto d’ora quando ti va, anche la sera.', 'phone'),
                ('03', '', 'Lo studio', '', 'A Milano, su appuntamento, per chi preferisce guardarsi in faccia.', 'map-pin')])]},

    'cromo': {
        'lavori': [
            ('righe', 'Gli ultimi lavori', 'Spot, titoli', 'e animazioni.', [
                ('01', 'La sigla del festival', 'Titoli animati', '40 secondi'),
                ('02', 'Il lancio dell’app', 'Spot in 3D', '30 secondi'),
                ('03', 'Il marchio che respira', 'Logo animato', '6 secondi'),
                ('04', 'La mappa del museo', 'Installazione', 'su tre schermi')], '@lavori')],
        'studio': [
            ('schede', 'Lo studio', 'Tre mestieri', 'sotto lo stesso tetto.', [
                ('01', '', 'Animazione 2D', '', 'Personaggi, forme e titoli, disegnati e mossi a mano fotogramma per fotogramma.', 'pen-tool'),
                ('02', '', '3D e luce', '', 'Oggetti, prodotti e spazi che non esistono ancora, illuminati come in uno studio vero.', 'box'),
                ('03', '', 'Suono', '', 'Musica e rumori fatti per l’animazione, non presi da un archivio.', 'music')]),
            ('frase', 'Un’animazione funziona quando nessuno si accorge che è un’animazione: si guarda e basta.', 'Lo studio Cromo')],
        'contatti': [
            ('passi', 'Dopo il primo messaggio', 'Cosa succede', 'nella prima settimana.', [
                ('01', 'La chiamata', 'Mezz’ora per capire cosa serve, dove andrà e per quando.'),
                ('02', 'La proposta', 'Due idee con le immagini di riferimento, il tempo e il prezzo.'),
                ('03', 'Lo storyboard', 'Le scene disegnate, prima di animare un solo fotogramma.')])]},

    'fenice-calcio': {
        'squadra': [
            ('carte', 'In campo', 'Quattro reparti,', 'una squadra.', [
                ('pexels-36862551.jpg', 'Le attaccanti', 'Davanti', 'Velocità sulla fascia e il tiro da fuori area: sono loro a firmare i gol.'),
                ('pexels-32366474.jpg', 'Il centrocampo', 'In mezzo', 'Corrono più di tutte: recuperano palla e la fanno arrivare davanti.'),
                ('pexels-36677269.jpg', 'La difesa', 'Dietro', 'Di testa non passa niente: la difesa meno battuta del girone.'),
                ('pexels-32266265.jpg', 'Le portiere', 'Fra i pali', 'Due portiere che si alternano, e si allenano tre volte a settimana.')])],
        'partite': [
            ('frase', 'Al campo del Rione 14 la domenica non vengono solo i genitori: vengono i vicini, i ragazzi della scuola '
             'calcio e chi si è innamorato di questa squadra.', 'La società'),
            ('faq', 'Allo stadio', 'Le domande', 'di chi viene a vederci.', [
                ('Quanto costa il biglietto?', 'Le partite in casa sono gratis: si entra dal cancello di via del Rione.'),
                ('A che ora si gioca?', 'La domenica alle 15, salvo anticipi che scriviamo nel calendario.'),
                ('Si può venire con i bambini?', 'Certo: c’è il prato dietro la porta e, a fine partita, le foto con le giocatrici.')])],
        'scuola': [
            ('schede', 'La scuola calcio', 'Tre gruppi,', 'per età.', [
                ('5 — 8', 'anni', 'Primi calci', '', 'Due allenamenti a settimana, tutto col pallone e niente classifiche.', 'smile'),
                ('9 — 12', 'anni', 'Esordienti', '', 'Le prime partite a sette e un torneo a fine stagione.', 'users'),
                ('13 — 16', 'anni', 'Giovanissime', '', 'Il campionato regionale e gli allenamenti con la prima squadra.', 'award')])]},

    'ferraris-avvocati': {
        'contatti': [
            ('passi', 'Il primo colloquio', 'Come si svolge', 'l’incontro.', [
                ('01', 'La telefonata', 'Ci racconti in breve il problema: ti diciamo subito se è materia nostra.'),
                ('02', 'I documenti', 'Ti chiediamo solo quelli che servono, da mandare prima per email.'),
                ('03', 'Il colloquio', 'Un’ora in studio: ti spieghiamo le strade possibili, con i tempi e i costi.'),
                ('04', 'Il preventivo', 'Scritto, prima di cominciare, come prevede la legge.')]),
            ('frase', 'Prima di parlare di cause, parliamo di quello che vuole ottenere chi ci viene a trovare. Spesso si '
             'arriva più in fretta con una lettera che con un giudice.', 'L’avvocato Ferraris')]},

    'fuoco-studio': {
        'lavori': [
            ('righe', 'L’archivio', 'Gli ultimi', 'otto progetti.', [
                ('2026', 'Il marchio del birrificio', 'Identità', 'Milano'),
                ('2026', 'Il sito della fondazione', 'Digitale', 'Torino'),
                ('2025', 'La campagna d’autunno', 'Campagne', 'Bergamo'),
                ('2025', 'Il packaging del caffè', 'Packaging', 'Napoli'),
                ('2025', 'La mostra sui manifesti', 'Allestimento', 'Milano')], '@lavori')],
        'studio': [
            ('schede', 'Lo studio', 'Come lavoriamo', 'con chi ci sceglie.', [
                ('01', '', 'Un gruppo piccolo', '', 'Le persone che incontri al primo appuntamento sono quelle che fanno il lavoro.', 'users'),
                ('02', '', 'Poche idee, forti', '', 'Non dieci proposte: due, e una che difendiamo fino in fondo.', 'flame'),
                ('03', '', 'Fino al lunedì', '', 'Un marchio deve funzionare anche sul furgone e sulla fattura, non solo nella presentazione.', 'check')]),
            ('frase', 'Un buon marchio si nota. Uno ottimo si ricorda anche il lunedì mattina.', 'Fuoco Studio')],
        'contatti': [
            ('passi', 'Dopo il primo messaggio', 'I primi', 'quindici giorni.', [
                ('01', 'Il caffè', 'Un’ora in studio o al telefono per capire il problema, non per vendere.'),
                ('02', 'La proposta', 'Obiettivi, tempi e prezzo in due pagine.'),
                ('03', 'Il workshop', 'Mezza giornata con il vostro gruppo: si parte da lì.')])]},

    'giardino-di-elsa': {
        'vivaio': [
            ('carte', 'Nel vivaio', 'Le piante', 'che reggono la Riviera.', [
                ('pexels-36713221.jpg', 'Gli olivi', 'Da mezzo secolo in su', 'Trapiantati con la zolla intera: attecchiscono anche in un cortile.'),
                ('pexels-27397124.jpg', 'Le siepi', 'Pittosporo e mirto', 'Reggono il vento salato e si potano due volte l’anno.'),
                ('pexels-37487180.jpg', 'L’orto', 'In cassetta', 'Aromatiche e ortaggi di stagione, già cresciuti e pronti da mettere a terra.'),
                ('pexels-4947117.jpg', 'I vasi', 'Per i terrazzi', 'Agrumi, gelsomini e succulente che vivono bene al sole pieno.')])],
        'contatti': [
            ('passi', 'Il progetto', 'Dal primo giro', 'al primo fiore.', [
                ('01', 'Il sopralluogo', 'Guardiamo la terra, il sole e il vento, e ascoltiamo cosa vorresti.'),
                ('02', 'Il disegno', 'Una pianta del giardino a mano, con le piante e le stagioni.'),
                ('03', 'La messa a dimora', 'Le piante arrivano dal nostro vivaio, la squadra è la nostra.'),
                ('04', 'La cura', 'Un anno di visite, perché il giardino attecchisca come deve.')]),
            ('citazione', 'Avevamo un cortile di cemento e un olivo malato. Adesso a maggio mangiamo fuori tutte le sere.',
             'Roberta', 'Sanremo')]},

    'giulia-marchi': {
        'progetti': [
            ('righe', 'Alcuni progetti', 'Siti per chi fa', 'cose belle.', [
                ('01', 'Atelier Neve', 'Sito e prenotazioni', '6 settimane'),
                ('02', 'Studio Ferri', 'Identità e sito', '4 settimane'),
                ('03', 'Ceramiche Riva', 'Negozio online', '8 settimane'),
                ('04', 'Libreria del Portico', 'Sito ed eventi', '5 settimane')], '@progetti'),
            ('frase', 'Il sito nuovo ci ha portato meno telefonate per chiedere gli orari e più prenotazioni vere.',
             'Atelier Neve, Bologna')],
        'metodo': [
            ('schede', 'Cosa ricevi', 'Alla fine', 'del lavoro.', [
                ('01', '', 'Il sito', '', 'Fatto su misura, veloce, che si aggiorna da soli senza chiamarmi.', 'monitor'),
                ('02', '', 'Una guida', '', 'Venti minuti di video per cambiare testi, foto e orari.', 'book-open'),
                ('03', '', 'Tre mesi con me', '', 'Per le domande dei primi tempi e i piccoli ritocchi.', 'message-circle')])],
        'contatti': [
            ('passi', 'Dopo il primo messaggio', 'Come cominciamo', 'a lavorare.', [
                ('01', 'Una chiamata', 'Mezz’ora per conoscerci e capire cosa serve davvero.'),
                ('02', 'Il preventivo', 'Una pagina con il lavoro, i tempi e il prezzo fisso.'),
                ('03', 'Il calendario', 'Le date delle consegne, prima di cominciare.')])]},

    'glifo': {
        'licenze': [
            ('listino', 'Le licenze', 'Quale licenza', 'ti serve.', [
                ('Desktop', 'Per stampare, impaginare e progettare sul tuo computer.', 'per persona'),
                ('Web', 'Per usare il carattere su un sito, in base alle visite.', 'per sito'),
                ('App', 'Per inserirlo dentro un’applicazione o un videogioco.', 'per app'),
                ('Marchio', 'Per disegnarci un logo da registrare.', 'su richiesta')],
             'Ogni licenza è per sempre: si paga una volta e si usa senza scadenza.'),
            ('frase', 'Un carattere è una voce: si sceglie per come suona quando nessuno lo legge ad alta voce.', 'Glifo, Torino')]},

    'il-gufo': {
        'contatti': [
            ('carte', 'In libreria', 'Le cose', 'che facciamo ogni settimana.', [
                ('pexels-7105617.jpg', 'Lettura ad alta voce', 'Il sabato mattina', 'Mezz’ora di storie per i più piccoli, sui cuscini in fondo alla libreria.'),
                ('pexels-13458680.jpg', 'Il club dei lettori', 'Un giovedì al mese', 'Per i ragazzi dai 10 anni: un libro, una merenda e tante opinioni.'),
                ('pexels-16229173.jpg', 'Disegno', 'La domenica', 'Un illustratore ospite e i bambini che disegnano la storia appena letta.'),
                ('pexels-34708262.jpg', 'Consigli su misura', 'Quando volete', 'Ci dite l’età e cosa gli piace: troviamo il libro giusto.')]),
            ('faq', 'Prima di venire', 'Le domande', 'dei genitori.', [
                ('Si può prenotare un libro?', 'Sì, anche per telefono: lo teniamo da parte una settimana.'),
                ('Le letture sono gratis?', 'Sì, basta arrivare puntuali: i cuscini sono quindici.'),
                ('Fate i pacchi regalo?', 'Sempre, con la carta disegnata da noi.')])]},

    'il-pozzo': {
        'programma': [
            ('righe', 'La settimana', 'Chi suona', 'sul piccolo palco.', [
                ('Mar', 'Jam session', 'Aperta a tutti', 'dalle 21:30'),
                ('Mer', 'Trio di casa', 'Standard', 'dalle 21:30'),
                ('Gio', 'Ospite della settimana', 'Concerto', 'dalle 22'),
                ('Ven', 'Big band in cantina', 'Swing', 'dalle 22'),
                ('Sab', 'Voce e pianoforte', 'Concerto', 'dalle 22')], '@prenota'),
            ('frase', 'In cantina ci stanno settanta persone e un pianoforte. È piccolo apposta: qui il jazz si sente respirare.',
             'Il Pozzo, Perugia')],
        'prenota': [
            ('schede', 'La serata', 'Come funziona', 'un tavolo al Pozzo.', [
                ('01', '', 'Il tavolo', '', 'Si prenota fino alle 18 del giorno stesso; dopo, i posti al bancone restano liberi.', 'calendar'),
                ('02', '', 'La consumazione', '', 'Il concerto è compreso nel primo drink: niente biglietto a parte.', 'wine'),
                ('03', '', 'Il silenzio', '', 'Durante i pezzi si parla piano: chi suona è a due metri.', 'music')])]},
}
