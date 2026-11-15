# -*- coding: utf-8 -*-
"""Le sezioni scritte per le pagine scarne dei temi (13 nov 2026), lette da arricchisci.py.

SPEC[tema] = {'pagine': {pagina: [sezione, …]}, 'pagine_nuove': {…}, 'media': […]}
Sezioni: ('faq', occhiello, riga1, riga2, [(domanda, risposta)], nota) · ('passi', occhiello, riga1, riga2,
[(numero, titolo, testo)], sottotitolo) · ('numeri', [(valore, etichetta)], (occhiello, riga1, riga2)) ·
('citazione', testo, autore, ruolo) · ('foto', [(file, alt)], (occhiello, riga1, riga2)) · ('schede', occhiello, riga1,
riga2, [(contatore, etichetta, titolo, accento, testo, icona)]) · ('invito', occhiello, riga1, riga2, testo,
[(testo, '@pagina' o url, 'pieno'|'filo')]) · ('split', foto, alt, occhiello, riga1, riga2, testo, (pulsante, link, tipo)).
I fatti (indirizzi, prezzi, orari) sono quelli che il tema scrive già.
"""

SPEC = {
    'acqua-viva': {'pagine': {
        'trattamenti': [
            ('passi', 'Il giorno alle terme', 'Come si svolge', 'un trattamento.', [
                ('01', 'L’arrivo', 'Accappatoio, telo e una tisana alla salvia mentre ti spieghiamo il percorso.'),
                ('02', 'La vasca', 'Mezz’ora nell’acqua a 38 gradi: scioglie i muscoli prima delle mani.'),
                ('03', 'Il trattamento', 'Nella stanza di pietra, con l’olio delle nostre colline scaldato al momento.'),
                ('04', 'Il riposo', 'La sala del camino, un’altra tisana e nessuna fretta di andare via.')],
             'Ogni trattamento comprende un’ora in vasca e la tisana: il tempo del giorno è tutto tuo.'),
            ('citazione', 'Siamo arrivati stanchi morti dal lavoro e siamo ripartiti senza voglia di parlare. Nel senso '
             'migliore: il rituale di coppia vale il viaggio.', 'Marta e Andrea', 'da Firenze, ottobre 2026')],
        'prenota': [
            ('faq', 'Prima di venire', 'Le domande', 'di chi prenota.', [
                ('Devo portare l’accappatoio?', 'No: accappatoio, telo e ciabatte sono compresi nell’ingresso. Porta solo il costume.'),
                ('Posso venire senza trattamento?', 'Sì, l’ingresso alle terme si prenota anche da solo: tutte le vasche, la grotta '
                 'e le tisane senza limiti.'),
                ('Ci sono limiti di età?', 'Le vasche sono aperte dai 14 anni. La grotta di vapore è sconsigliata in gravidanza.'),
                ('Se piove?', 'Le vasche sono all’aperto e l’acqua è a 38 gradi: sotto la pioggia è ancora più bello. Con i '
                 'temporali chiudiamo le vasche esterne e restano la grotta e la sala del camino.')],
             'Per tutto il resto chiama lo 0577 123 456: rispondiamo dalle 9 alle 20.')]}},

    'argilla': {'pagine': {'spedizioni': [
        ('passi', 'Dal forno a casa tua', 'Come imballiamo', 'un pezzo fragile.', [
            ('01', 'Il controllo', 'Ogni pezzo si guarda controluce: una crepa fine non parte, resta in bottega come seconda scelta.'),
            ('02', 'La carta', 'Due giri di carta di giornale e uno di carta velina: niente plastica a bolle.'),
            ('03', 'La scatola doppia', 'Una scatola dentro l’altra, con quattro dita di trucioli di legno in mezzo.'),
            ('04', 'Il corriere', 'Parte dal martedì al venerdì, mai prima del fine settimana: così non resta in magazzino.')]),
        ('citazione', 'Le ciotole blu sono arrivate a Berlino in quattro giorni, avvolte come regali. Una era venuta '
         'un po’ storta: mi hanno detto che è normale, ed è la mia preferita.', 'Lena', 'ordine da Berlino, settembre 2026')]}},

    'cantiere-aperto': {'pagine': {
        'lavori': [
            ('numeri', [('140', 'cantieri chiusi dal 2014'), ('10', 'settimane per un appartamento'), ('0', 'penali pagate'),
                        ('1', 'referente per tutto')], ('I numeri', 'Lavori finiti,', 'non promessi.')),
            ('invito', 'Il prossimo', 'Il prossimo prima e dopo', 'può essere casa tua.',
             'Il sopralluogo è gratuito: veniamo a vedere, misuriamo e in una settimana hai il preventivo scritto con la data '
             'di consegna.', [('Chiedi il sopralluogo', '@preventivo', 'pieno'), ('Come lavoriamo', '@come', 'filo')])],
        'come': [
            ('passi', 'Le fasi', 'Dal sopralluogo', 'alle chiavi.', [
                ('01', 'Sopralluogo', 'Un’ora in casa tua: misure, foto e quello che vorresti cambiare.'),
                ('02', 'Progetto e prezzo', 'Pianta, rendering e preventivo voce per voce, con la data di consegna.'),
                ('03', 'Permessi', 'CILA o SCIA e le pratiche per le detrazioni le prepara il nostro geometra.'),
                ('04', 'Cantiere e consegna', 'Una squadra sola, il cantiere da seguire online, le chiavi nel giorno scritto.')])],
        'preventivo': [
            ('faq', 'Prima del sopralluogo', 'Le domande', 'sul preventivo.', [
                ('Il sopralluogo è davvero gratis?', 'Sì, a Padova e provincia. Non ti impegna a niente e il preventivo resta tuo.'),
                ('Quanto è preciso il preventivo?', 'È voce per voce, con le quantità. Cambia solo se cambi tu il progetto, o '
                 'per gli imprevisti, che hanno una voce a parte.'),
                ('Come si paga?', 'Un acconto alla firma, poi a stati di avanzamento e il saldo alla consegna delle chiavi.'),
                ('Seguite anche le detrazioni?', 'Sì: le pratiche per i bonus edilizi le prepara il nostro geometra, comprese nel prezzo.')],
             'Oppure chiama lo 049 123 4567: ti richiamiamo entro un giorno lavorativo.')]}},

    'casa-scogliera': {'pagine': {'prenota': [
        ('faq', 'Prima di partire', 'Le cose', 'da sapere.', [
            ('A che ora si arriva?', 'Le camere sono pronte dalle 15. Se arrivi prima, lasci la valigia e scendi subito alla caletta.'),
            ('Si arriva in macchina?', 'Fino al cancello sì, e c’è il posto auto. Gli ultimi cinquanta metri sono una scala sugli scogli: '
             'le valigie le portiamo noi.'),
            ('Posso portare il cane?', 'Sì, nella camera Pineta, che ha il giardino. Chiedici prima, così prepariamo la ciotola.'),
            ('E se devo cancellare?', 'Fino a quattordici giorni prima rimborsiamo tutto. Dopo teniamo la prima notte.')],
         'Rispondiamo entro sera, anche su WhatsApp.'),
        ('foto', [('pexels-16436925.jpg', 'La camera Faro'), ('pexels-24807130.jpg', 'La camera Scoglio'),
                  ('pexels-34854573.jpg', 'Il bagno della camera Pineta')], ('Le camere', 'Tre camere,', 'un solo mare.'))]}},

    'codice-ragazzi': {
        'pagine_nuove': {'il-laboratorio': {'titolo': 'Il laboratorio', 'menu': 'Il laboratorio', 'occhiello': 'Via Santa Maria 44, Pisa',
                                            'titolo_righe': ['Il laboratorio,', 'dove si costruisce.'],
                                            'sotto': 'Un’aula con dodici postazioni, un banco per saldare e un angolo per far volare i droni.'}},
        'pagine': {
            'corsi': [
                ('passi', 'Durante l’anno', 'Da ottobre', 'alla fiera di maggio.', [
                    ('Ott', 'Si comincia', 'La lezione di prova, i gruppi per età e il primo programma che funziona.'),
                    ('Dic', 'Il primo progetto', 'Un gioco o un robot semplice, da portare a casa per le vacanze.'),
                    ('Mar', 'Il progetto grande', 'Ognuno sceglie il suo: il drone, il gioco completo, la stazione meteo.'),
                    ('Mag', 'La fiera', 'Un pomeriggio aperto a famiglie e amici: si mostra, si spiega, si gioca.')]),
                ('faq', 'I corsi', 'Quale corso', 'per mio figlio?', [
                    ('Ha 8 anni e non ha mai programmato.', 'Primi codici: Scratch e piccoli robot, si impara giocando.'),
                    ('Ha 12 anni e smonta tutto.', 'Robotica: Python e Arduino, e il drone da costruire che resta a lui.'),
                    ('Ha 14 anni e gioca tutto il giorno.', 'Videogiochi: Godot e pixel art, e un gioco completo pubblicato online.'),
                    ('Non sappiamo scegliere.', 'Venite alla lezione di prova del sabato: dopo un’ora e mezza lo sa lui.')])],
            'iscrizioni': [
                ('faq', 'Prima di iscriversi', 'Le domande', 'sulle iscrizioni.', [
                    ('Quanto costa la lezione di prova?', 'Niente. Dura un’ora e mezza, il sabato mattina, e il computer lo diamo noi.'),
                    ('Si può cominciare a metà anno?', 'Sì, finché ci sono posti nel gruppo: gli insegnanti aiutano a recuperare.'),
                    ('Ci sono sconti per fratelli?', 'Sì, il secondo figlio paga il corso con il 20% di sconto.'),
                    ('Come si paga?', 'In due rate, a ottobre e a febbraio, con bonifico o in segreteria.')],
                 'Scrivici a ciao@codiceragazzi.it: rispondiamo entro un giorno.')],
            'il-laboratorio': [
                ('split', 'pexels-7869085.jpg', 'Ragazzi al lavoro sui computer del laboratorio', 'Il posto',
                 'Un computer a testa,', 'nessuno guarda e basta.',
                 'Dodici postazioni, un proiettore, una stampante 3D e un banco con i saldatori e le lenti. Le scatole dei '
                 'pezzi stanno sugli scaffali in vista: chi finisce prima può prendere un sensore e provarlo.',
                 ('Prova una lezione', '@iscrizioni', 'pieno')),
                ('passi', 'Una lezione', 'Novanta minuti,', 'in quattro tempi.', [
                    ('01', 'La sfida', 'Si parte da un problema: far girare un motore, far saltare un personaggio, '
                                       'accendere un led quando fa buio.'),
                    ('02', 'Il codice', 'Ognuno scrive il suo, con gli insegnanti che girano fra i banchi.'),
                    ('03', 'La prova', 'Si prova davvero: se il drone non vola si torna al codice, senza drammi.'),
                    ('04', 'La demo', 'Gli ultimi dieci minuti si mostra agli altri cosa si è fatto, anche se non funziona.')]),
                ('numeri', [('12', 'postazioni'), ('8', 'ragazzi per gruppo'), ('2', 'insegnanti in aula'), ('1', 'fiera a maggio')]),
                ('faq', 'I genitori chiedono', 'Le domande', 'dei genitori.', [
                    ('Serve sapere già programmare?', 'No. Al corso Primi codici si parte da Scratch, a blocchi: basta saper leggere.'),
                    ('Il computer lo portiamo noi?', 'No, in aula c’è un computer a testa. A casa basta un portatile qualsiasi per '
                     'giocare con i programmi fatti in classe.'),
                    ('E se perde una lezione?', 'Il materiale è online e il sabato mattina c’è un’ora di recupero aperta a tutti.'),
                    ('Cosa resta a fine anno?', 'Il progetto: il gioco pubblicato, il programma o il drone, che al corso di '
                     'robotica resta al ragazzo.')])]}},

    'cromo': {'pagine': {'contatti': [
        ('faq', 'Prima di scriverci', 'Quanto tempo,', 'quanto costa.', [
            ('Quanto ci vuole per un video?', 'Uno spot di trenta secondi richiede da quattro a sei settimane, dal brief alla consegna. '
             'Una sigla animata, tre.'),
            ('Qual è il budget minimo?', 'Per un logo animato si parte da 2.500 €, per uno spot in 3D da 12.000 €. Il preventivo '
             'è scritto e non cambia se non cambia il progetto.'),
            ('In che formati consegnate?', 'Tutti quelli che servono: 16:9 per lo schermo, 9:16 e 1:1 per i social, con e senza testi.'),
            ('La musica è vostra?', 'La componiamo noi, con i due musicisti dello studio: i diritti sono compresi.')],
         'Il campanello è quello col fulmine, in via del Pigneto 88.')]}},

    'denim-district': {'pagine': {'shop': [
        ('schede', 'Come lo riconosci', 'Un buon jeans', 'si vede dall’orlo.', [
            ('14', 'once', 'Denim', ' pesante', 'Il tessuto giapponese da 14 once: rigido i primi giorni, poi prende la tua forma.', 'layers'),
            ('01', 'cimosa', 'La cimosa', ' rossa', 'Il bordo chiuso del telaio a navetta: si vede risvoltando l’orlo.', 'scissors'),
            ('Cu', 'bottoni', 'Rame', ' vero', 'Bottoni e rivetti in rame, che con gli anni scuriscono insieme al tessuto.', 'gem'),
            ('10′', 'orli', 'Orlo', ' in negozio', 'In via Po li accorciamo in dieci minuti, mentre bevi un caffè.', 'ruler')]),
        ('faq', 'Taglie e resi', 'Le domande', 'prima di comprare.', [
            ('Che taglia prendo?', 'La tua solita: il denim crudo si allarga di mezza taglia in vita nelle prime settimane.'),
            ('Si restringe lavandolo?', 'Il primo lavaggio, in acqua fredda, lo accorcia di due centimetri. Per questo l’orlo lo '
             'facciamo dopo, se vuoi.'),
            ('Posso renderlo?', 'Sì, entro trenta giorni, se non è stato orlato né lavato.'),
            ('Spedite?', 'In tutta Italia in due giorni, gratis sopra i 100 €.')])]}},

    'fenice-calcio': {'pagine': {'squadra': [
        ('numeri', [('22', 'giocatrici'), ('3', 'allenatrici'), ('1998', 'l’anno della prima squadra'), ('5', 'nazionalità')],
         ('La rosa', 'Una squadra,', 'ventidue storie.')),
        ('citazione', 'Ci alleniamo la sera dopo il lavoro e la scuola, tre volte alla settimana. La domenica, quando entriamo '
         'in campo, nessuno si ricorda della stanchezza.', 'La capitana', 'Fenice Calcio, Serie C femminile')]}},

    'ferraris-avvocati': {'pagine': {
        'aree': [
            ('passi', 'Come lavoriamo', 'Dal primo colloquio', 'alla soluzione.', [
                ('01', 'Il colloquio', 'Un’ora per ascoltare e leggere i documenti: 90 €, scalati dal preventivo.'),
                ('02', 'Il preventivo', 'Scritto, con le fasi e le spese prevedibili. Niente sorprese a fine pratica.'),
                ('03', 'La strada', 'Prima proviamo l’accordo; se non basta, andiamo in tribunale preparati.'),
                ('04', 'La chiusura', 'Una relazione finale e i documenti in ordine, anche per gli anni a venire.')]),
            ('faq', 'Le domande', 'Quello che', 'ci chiedono subito.', [
                ('Quanto dura una separazione consensuale?', 'Da tre a sei mesi, a seconda del tribunale e dell’accordo fra le parti.'),
                ('Mi hanno licenziato: entro quando devo muovermi?', 'L’impugnazione va fatta entro sessanta giorni dalla lettera: '
                 'chiamaci subito.'),
                ('Seguite anche le imprese?', 'Sì, per contratti, recupero crediti e costituzione di società.'),
                ('Il gratuito patrocinio?', 'Sì, per famiglia e lavoro, se ci sono i requisiti di reddito: lo verifichiamo al colloquio.')])],
        'studio': [
            ('citazione', 'Mi hanno spiegato ogni passaggio prima di farlo, e il preventivo scritto all’inizio è stato quello che '
             'ho pagato alla fine. In un momento difficile, non è poco.', 'Una cliente', 'separazione consensuale, 2025'),
            ('foto', [('pexels-28295548.jpg', 'Il palazzo di via Manzoni'), ('pexels-8112180.jpg', 'La firma di un accordo'),
                      ('pexels-7876092.jpg', 'Un colloquio in sala riunioni')], ('Via Manzoni 31', 'Il terzo piano,', 'da trent’anni.'))]}},

    'fuoco-studio': {'pagine': {
        'lavori': [
            ('numeri', [('2012', 'l’anno del primo fuoco'), ('9', 'persone'), ('120', 'marchi disegnati'), ('3', 'strade per ogni progetto')]),
            ('invito', 'Il prossimo', 'Il prossimo lavoro', 'è il tuo?', 'Raccontaci il progetto in cinque righe: ti rispondiamo in '
             'due giorni con un’idea su come lo faremmo, e quanto costerebbe.',
             [('Scrivici', '@contatti', 'pieno'), ('Lo studio', '@studio', 'filo')])],
        'contatti': [
            ('faq', 'Prima di scriverci', 'Le domande', 'che ci fanno tutti.', [
                ('Quanto costa un marchio?', 'Un marchio con le sue regole d’uso parte da 6.000 €. Un sito, da 9.000 €. Il preventivo è '
                 'su una pagina sola.'),
                ('Quanto ci mettete?', 'Da sei a dieci settimane, con una revisione ogni venerdì.'),
                ('Lavorate anche fuori Milano?', 'Sì, in tutta Italia e in Svizzera: le riunioni si fanno a distanza, la prima di persona.'),
                ('Fate anche solo il sito?', 'Sì, ma prima guardiamo il marchio: se non regge, lo diciamo.')])]}},

    'giardino-di-elsa': {'pagine': {
        'progetti': [
            ('passi', 'Come nasce un giardino', 'Quattro stagioni,', 'poi cresce da solo.', [
                ('01', 'Il sopralluogo', 'Guardiamo il sole, il vento e la terra. Gratis in tutta la Riviera dei Fiori.'),
                ('02', 'Il disegno', 'Una pianta a mano e l’elenco delle piante, tutte del nostro vivaio.'),
                ('03', 'La piantagione', 'In autunno, quando la terra è ancora calda e le piogge fanno il resto.'),
                ('04', 'La cura', 'Due visite il primo anno, poi il giardino chiede poca acqua e poco lavoro.')]),
            ('citazione', 'Il terrazzo era una distesa di cemento al sole. Adesso c’è l’ombra del glicine e lo annaffio una volta '
             'alla settimana, d’estate.', 'Paolo', 'terrazzo a Sanremo, 2024')],
        'vivaio': [
            ('faq', 'Al vivaio', 'Le domande', 'sulle piante.', [
                ('Spedite le piante?', 'In Liguria e Piemonte le portiamo noi il giovedì. Altrove spediamo solo le aromatiche, in vasetti.'),
                ('Che terra serve?', 'Quella che hai, quasi sempre: le piante mediterranee amano i terreni poveri e ben drenati.'),
                ('Quanta acqua?', 'Il primo anno una volta alla settimana. Poi, per olivi e aromatiche, quasi niente.'),
                ('Si possono vedere le piante grandi?', 'Sì, al vivaio di Strada San Pietro, dal martedì alla domenica.')])],
        'contatti': [
            ('numeri', [('300', 'metri sul mare'), ('400', 'varietà in vivaio'), ('60', 'giardini progettati'), ('0', 'sopralluoghi a pagamento')])]}},

    'giulia-marchi': {'pagine': {'contatti': [
        ('faq', 'Prima di scrivermi', 'Le domande', 'che mi fanno tutti.', [
            ('Quanto costa un sito?', 'Da 4.500 € per un sito di presentazione, da 7.000 € per un negozio. Il prezzo è scritto nella '
             'proposta, prima di cominciare.'),
            ('Quanto tempo serve?', 'Sei settimane, in cinque passi. Ogni passo finisce con qualcosa che puoi vedere.'),
            ('I testi li scrivi tu?', 'Li scriviamo insieme: io faccio le domande, tu racconti, poi li sistemo.'),
            ('E dopo la consegna?', 'Un’ora di formazione per aggiornarlo da sola e tre mesi di assistenza compresi.')],
         'Oppure un caffè in via Mascarella 12.')]}},

    'il-gufo': {'pagine': {
        'letture': [
            ('passi', 'Come si partecipa', 'Prenota,', 'poi vieni a leggere.', [
                ('01', 'Scegli', 'Le letture del sabato alle 11 o il club del giovedì alle 18, per età.'),
                ('02', 'Scrivici', 'Una telefonata o un messaggio: i posti sono quindici, per stare comodi sui cuscini.'),
                ('03', 'Vieni', 'Dieci minuti prima, così chi è timido ha il tempo di scegliere il suo posto.'),
                ('04', 'Porta un amico', 'La volta dopo, se vuoi: le storie si leggono meglio in tanti.')],
             'Tutto gratis: basta prenotare.'),
            ('foto', [('pexels-13458680.jpg', 'Una lettura ad alta voce in libreria'), ('pexels-16229173.jpg', 'Gli scaffali dei '
                       'libri per ragazzi'), ('pexels-7105617.jpg', 'Un laboratorio di disegno')],
             ('Il sabato mattina', 'Cuscini, storie', 'e mani alzate.'))],
        'consigli': [
            ('schede', 'Per età', 'Il libro giusto', 'al momento giusto.', [
                ('0–5', 'anni', 'Albi', ' illustrati', 'Pagine grandi, poche parole e tante cose da indicare col dito.', 'baby'),
                ('6–9', 'anni', 'Prime', ' letture', 'Capitoli corti, caratteri grandi e un mistero da risolvere entro sera.', 'book-open'),
                ('10–13', 'anni', 'Romanzi', ' d’avventura', 'Mappe, viaggi e amicizie che resistono a tutto.', 'compass'),
                ('14+', 'anni', 'Storie', ' vere', 'I libri che fanno discutere, quelli da cui si esce un po’ diversi.', 'sparkles')]),
            ('citazione', 'Mia figlia odiava leggere. Al Gufo le hanno dato un fumetto, poi un libro con le mappe, poi un altro. '
             'Adesso legge sotto le coperte con la torcia.', 'Chiara', 'mamma di una lettrice di dieci anni')]}},

    'il-pozzo': {'pagine': {
        'club': [
            ('citazione', 'Ho suonato in club molto più grandi, ma al Pozzo il pubblico è a un metro dal piano e ascolta in '
             'silenzio. Torno ogni anno.', 'Un pianista ospite', 'venerdì di ottobre, 2025'),
            ('foto', [('pexels-11789976.jpg', 'Il sassofono del giovedì'), ('pexels-21547198.jpg', 'Un musicista sul palco'),
                      ('pexels-9419224.jpg', 'La sala durante un concerto')], ('La cantina', 'Settanta posti,', 'un pianoforte.'))],
        'prenota': [
            ('faq', 'Prima di venire', 'Le domande', 'sulla serata.', [
                ('Si cena?', 'Taglieri, formaggi umbri e il dolce della casa. Non è un ristorante: si mangia ascoltando.'),
                ('Quanto costa l’ingresso?', 'Dipende dalla sera: la jam del martedì è libera, gli ospiti del venerdì 18 €. La consumazione '
                 'è a parte.'),
                ('Posso arrivare a concerto iniziato?', 'Sì, ma entri fra un brano e l’altro: durante i pezzi la porta resta chiusa.'),
                ('Si può portare uno strumento?', 'Il martedì, alla jam session, sì: è fatta apposta.')],
             'Per i gruppi oltre le otto persone chiama lo 075 123 4567.')]}},

    'inchiostro-riso': {'pagine': {'contatti': [
        ('passi', 'Dal file alla carta', 'Come lavoriamo', 'una tiratura.', [
            ('01', 'Il file', 'Ci mandi il file o l’idea: ti diciamo come separarlo in un colore per passaggio.'),
            ('02', 'La prova', 'Una copia di prova, fotografata e mandata per email prima di stampare il resto.'),
            ('03', 'La stampa', 'Un colore alla volta, con un giorno fra un passaggio e l’altro perché l’inchiostro asciughi.'),
            ('04', 'Il ritiro', 'In vico Lungo Gelso, o spedito in un tubo di cartone riciclato.')],
         'Il preventivo arriva in giornata.')]}},

    'maison-blanche': {'pagine': {'atelier': [
        ('passi', 'Sei mesi, tre prove', 'Come nasce', 'il tuo abito.', [
            ('01', 'Il primo incontro', 'Un’ora in atelier: le tue idee, le foto che hai salvato, gli abiti da provare.'),
            ('02', 'La prova in tela', 'Il modello cucito in tela di cotone, per decidere scollo, maniche e strascico.'),
            ('03', 'La prova nel tessuto', 'L’abito vero, imbastito: si fissano le misure e il velo.'),
            ('04', 'L’ultima prova', 'Due settimane prima delle nozze, con le scarpe del giorno. Poi lo stiriamo e te lo consegniamo.')]),
        ('citazione', 'Sono entrata convinta di volere il pizzo e sono uscita con il crêpe. Nadia ha capito prima di me cosa mi '
         'stava bene, e ha avuto ragione.', 'Una sposa', 'giugno 2026, sul lago di Como')]}},

    'margini': {'pagine': {
        'numeri': [
            ('citazione', 'Margini è una delle poche riviste che leggo dall’inizio alla fine, anche le pagine che non mi '
             'interessano. Soprattutto quelle.', 'Un lettore', 'abbonato dal numero 4'),
            ('invito', 'Abbonati', 'Quattro numeri', 'a casa tua.', 'L’abbonamento di carta comprende tutto l’archivio digitale dal '
             '2018 e un numero arretrato in regalo, a scelta.', [('Abbonati', '@abbonati', 'pieno')])],
        'abbonati': [
            ('faq', 'Abbonamenti', 'Le domande', 'prima di abbonarti.', [
                ('Quando arriva il primo numero?', 'Il prossimo in uscita, in una busta di carta. Se lo vuoi subito, ti spediamo '
                 'quello in edicola.'),
                ('Posso regalarlo?', 'Sì: scegli «regalo» e scrivi l’indirizzo di chi lo riceve. Mandiamo noi un biglietto.'),
                ('Spedite all’estero?', 'In Europa sì, con 12 € in più all’anno per la posta.'),
                ('Come disdico?', 'Quando vuoi, con una email: l’abbonamento non si rinnova da solo.')])]}},

    'mezza-delle-mura': {'pagine': {'classifiche': [
        ('numeri', [('4.611', 'arrivati nel 2026'), ('1:02:41', 'record maschile'), ('1:11:08', 'record femminile'),
                    ('318', 'società')], ('I record', 'Ventuno chilometri,', 'tempi da battere.')),
        ('invito', 'Edizione 2027', 'Il tuo nome', 'in classifica.', 'Domenica 22 marzo 2027, partenza alle 9:30 dal Sentierone. '
         'I pettorali sono 5.000: il primo lotto costa meno.', [('Iscriviti', '@iscrizioni', 'pieno'), ('Il percorso', '@percorso', 'filo')])]}},

    'mezzanotte': {'pagine': {
        'cocktail': [
            ('citazione', 'Ho chiesto qualcosa di amaro, con un po’ di fumo. Mi hanno portato un bicchiere che non era in carta: '
             'adesso lo ordino ogni volta.', 'Un cliente del giovedì', 'serata vinili'),
            ('invito', 'Stasera', 'Il tavolo', 'è tuo fino alle 22.', 'Prenota un tavolo per l’aperitivo o per il dopocena: dopo le '
             '22 si entra senza prenotazione, fino alle 2.', [('Prenota', '@prenota', 'pieno'), ('Le serate', '@serate', 'filo')])],
        'prenota': [
            ('faq', 'Prima di venire', 'Le domande', 'della notte.', [
                ('C’è un dress code?', 'No. Basta che ti senta bene: la luce è bassa e nessuno ti guarda le scarpe.'),
                ('Si mangia?', 'Taglieri, ostriche il venerdì e piccoli piatti dalla cucina fino a mezzanotte.'),
                ('Fate feste private?', 'Sì, fino a ottanta persone: compleanni, lauree, cene aziendali. Scrivici cosa hai in mente.'),
                ('Ci sono analcolici?', 'Cinque in carta, e il banco ne inventa uno su misura se ce lo chiedi.')])]}},

    'moneta': {'pagine': {
        'prezzi': [
            ('faq', 'Costi chiari', 'Le domande', 'sui prezzi.', [
                ('Davvero zero canone?', 'Sì, con il piano Base non paghi niente al mese, né per il conto né per la carta.'),
                ('Come funziona il 2% indietro?', 'Su ogni spesa con la carta nei supermercati ti torna il 2%, ogni lunedì, nel salvadanaio.'),
                ('Posso cambiare piano?', 'Quando vuoi, dall’app: il nuovo vale dal mese dopo, senza costi.'),
                ('I prelievi costano?', 'Con il Base sono gratis i primi tre al mese in Europa; con Plus e Nera, sempre.')])],
        'aiuto': [
            ('schede', 'Sicurezza', 'I tuoi soldi', 'sono al sicuro.', [
                ('1', 'tocco', 'Blocca', ' la carta', 'Dall’app, in un secondo. E la sblocchi quando la ritrovi.', 'lock'),
                ('24', 'ore', 'Notifiche', ' immediate', 'Ogni pagamento ti arriva sul telefono mentre succede.', 'bell'),
                ('100k', '€', 'Garanzia', ' sui depositi', 'I soldi sul conto sono garantiti fino a 100.000 euro.', 'shield-check'),
                ('3D', 'secure', 'Acquisti', ' online', 'Ogni pagamento online si conferma con l’impronta o il volto.', 'scan-face')])]}},

    'nido-coworking': {'pagine': {'contatti': [
        ('faq', 'Prima di venire', 'Le domande', 'di chi arriva.', [
            ('Il giorno di prova costa?', 'No: una giornata in open space, il caffè e il pranzo del venerdì, se capiti di venerdì.'),
            ('Serve un contratto lungo?', 'No, si disdice con un mese di preavviso. I prezzi sono mensili e senza vincoli.'),
            ('Posso ricevere la posta qui?', 'Sì, con la sede legale o il recapito: la reception la tiene e ti avvisa.'),
            ('Come entro di notte?', 'Con il telefono: lo stesso badge apre il portone, il piano e la tua sala, 24 ore su 24.')],
         'Via Irnerio 12, dieci minuti a piedi dalla stazione.')]}},

    'nutrire': {'pagine': {
        'metodo': [
            ('numeri', [('4', 'passi'), ('2', 'settimane fra un controllo e l’altro'), ('0', 'divieti scritti'), ('600', 'atleti seguiti')]),
            ('citazione', 'Pensavo di dover rinunciare a tutto prima della maratona. Ho cambiato tre cose nella mia settimana e ho '
             'corso sei minuti più veloce, senza crisi al trentesimo chilometro.', 'Luca', 'maratoneta, Verona')],
        'percorsi': [
            ('faq', 'I percorsi', 'Le domande', 'prima di cominciare.', [
                ('Quale percorso fa per me?', 'Ripartire se vuoi cambiare abitudini; Gara se hai una data segnata; Stagione se ti '
                 'alleni con un allenatore tutto l’anno.'),
                ('Le visite si fanno anche online?', 'Sì, in videochiamata. La prima, con le misure, meglio in studio.'),
                ('Servono integratori?', 'Quasi mai. Se servono, li scegliamo insieme e solo per il tempo necessario.'),
                ('Lavorate con il mio allenatore?', 'Sì: nel percorso Stagione ci sentiamo con lui per periodizzare i pasti.')])],
        'contatti': [
            ('passi', 'La prima visita', 'Un’ora', 'per conoscerci.', [
                ('01', 'Racconti', 'Come ti alleni, come mangi, come dormi, cosa vuoi ottenere.'),
                ('02', 'Misuriamo', 'Peso, plicometria e circonferenze: dati, non giudizi.'),
                ('03', 'Decidiamo', 'Il percorso giusto e la prima cosa da cambiare già da domani.')])]}},

    'orto-vivo': {'pagine': {'contatti': [
        ('faq', 'Prima di venire', 'Le domande', 'del bistrot.', [
            ('Il menù è tutto vegetale?', 'Sì, tutto dall’orto di Borgo Panigale. Uova e formaggi solo nei piatti dove c’è scritto.'),
            ('Ci sono piatti senza glutine?', 'Ogni giorno almeno metà del menù: chiedici al momento dell’ordine.'),
            ('Si può prenotare a pranzo?', 'Sì, dal martedì al sabato. Le ciotole da portare via non si prenotano: sono pronte in sette minuti.'),
            ('Fate cene private?', 'Il lunedì, a bistrot chiuso, fino a trenta persone, con il menù del raccolto.')],
         'Via del Pratello 41, Bologna.')]}},

    'parla-mondo': {
        'pagine_nuove': {'metodo': {'titolo': 'Il metodo', 'menu': 'Il metodo', 'occhiello': 'Via Sparano 120, Bari',
                                    'titolo_righe': ['Si impara', 'parlando.'],
                                    'sotto': 'Poca grammatica alla lavagna, tante conversazioni vere, insegnanti madrelingua.'}},
        'pagine': {
            'corsi': [
                ('schede', 'I livelli', 'Da dove parti,', 'dove arrivi.', [
                    ('A1–A2', 'base', 'Primi', ' passi', 'Presentarsi, ordinare, chiedere la strada. Dopo tre mesi una conversazione semplice.', 'sprout'),
                    ('B1–B2', 'intermedio', 'Parlare', ' davvero', 'Lavoro, viaggi, film senza sottotitoli: la lingua diventa tua.', 'message-circle'),
                    ('C1–C2', 'avanzato', 'Sfumature', '', 'Le battute, i registri, la scrittura formale. Per chi lavora con la lingua.', 'sparkles'),
                    ('Esami', 'certificati', 'IELTS', ' e gli altri', 'DELE, DELF, JLPT: simulazioni d’esame e iscrizione comprese.', 'award')]),
                ('faq', 'I corsi', 'Le domande', 'prima di iscriversi.', [
                    ('Come scopro il mio livello?', 'Con il test di livello, gratis: venti minuti online e un colloquio con un insegnante.'),
                    ('Posso passare dall’aula all’online?', 'Sì, anche lezione per lezione: il gruppo resta lo stesso.'),
                    ('Quanto dura un corso di gruppo?', 'Tre mesi, due lezioni a settimana. Poi si passa al livello dopo.'),
                    ('La prima lezione è gratis?', 'Sempre, in tutti i corsi.')])],
            'contatti': [
                ('numeri', [('5', 'lingue'), ('6', 'persone per gruppo'), ('12', 'insegnanti madrelingua'), ('94%', 'promossi agli esami')])],
            'metodo': [
                ('passi', 'Il percorso', 'Dal test', 'alla prima conversazione.', [
                    ('01', 'Il test', 'Venti minuti online e un colloquio: sai da che livello partire.'),
                    ('02', 'Il gruppo', 'Sei persone al massimo, dello stesso livello, in aula o in videochiamata.'),
                    ('03', 'La conversazione', 'Ogni lezione comincia parlando: la grammatica arriva quando serve.'),
                    ('04', 'Il traguardo', 'Un esame, un colloquio di lavoro o un viaggio: lo decidi tu.')]),
                ('split', 'pexels-6503100.jpg', 'Una lezione di conversazione in aula', 'In aula',
                 'Insegnanti madrelingua,', 'da ogni fuso orario.',
                 'Insegniamo con persone che la lingua la parlano da quando sono nate: inglesi, spagnoli, francesi, tedeschi e '
                 'giapponesi. Con l’orologio delle lezioni trovi l’ora giusta anche con chi abita dall’altra parte del mondo.',
                 ('Fai il test di livello', '@contatti', 'pieno')),
                ('citazione', 'Ho fatto il DELE B2 dopo un anno di corso, ma la soddisfazione vera è stata litigare in spagnolo con '
                 'un tassista a Madrid. E vincere.', 'Francesca', 'studentessa di spagnolo'),
                ('invito', 'La prima lezione', 'La prima lezione', 'è sempre gratis.', 'Scegli la lingua, fai il test di livello e '
                 'prova una lezione nel gruppo giusto per te.', [('Scrivici', '@contatti', 'pieno'), ('I corsi', '@corsi', 'filo')])]}},

    'passo-lento': {'pagine': {'contatti': [
        ('faq', 'Prima di partire', 'Le domande', 'di chi viaggia lento.', [
            ('Devo essere allenato?', 'No: le tappe a piedi sono di dieci-quindici chilometri, su sentieri facili. Il quaderno '
             'indica sempre l’alternativa in treno o in battello.'),
            ('I bagagli chi li porta?', 'Viaggiano da soli, da un albergo all’altro: tu cammini con lo zaino della giornata.'),
            ('Posso venire da solo?', 'Sì, metà dei viaggiatori arriva da sola. La camera singola costa un supplemento, '
             'oppure ti abbiniamo a un’altra persona.'),
            ('E se piove?', 'Si cammina lo stesso, con la mantella: i paesi sotto la pioggia sono più belli. Con l’allerta, '
             'la guida sceglie il treno.')],
         'Siamo a Torino, vicino a Porta Nuova: rispondiamo entro un giorno.')]}},

    'rewind-vintage': {'pagine': {'arrivi': [
        ('passi', 'Prima della vetrina', 'Come arriva', 'un pezzo da noi.', [
            ('01', 'La scelta', 'Su cento capi che ci portano, ne teniamo quindici: quelli che vale la pena rimettere in giro.'),
            ('02', 'Il lavaggio', 'A mano o a secco, a seconda del tessuto. La pelle si nutre con la cera d’api.'),
            ('03', 'La riparazione', 'Bottoni originali, cuciture rifatte, cerniere d’epoca quando le troviamo.'),
            ('04', 'Il giovedì', 'Alle 16 escono in negozio, con il cartellino dell’anno e la storia, quando la sappiamo.')]),
        ('citazione', 'Ho trovato la giacca di pelle che mio padre aveva negli anni Settanta, uguale. L’ho provata davanti a lui: '
         'si è messo a ridere e poi si è commosso.', 'Matteo', 'cliente dal 2015')]}},

    'rifugio-larice': {'pagine': {'rifugio': [
        ('numeri', [('2.104', 'metri'), ('1962', 'l’anno degli alpini'), ('34', 'posti letto'), ('3', 'generazioni')],
         ('Il rifugio', 'Sopra le nuvole,', 'in numeri.')),
        ('foto', [('pexels-16543351.jpg', 'Lo strudel della cucina del rifugio'), ('pexels-18074507.jpg', 'Una baita lungo il sentiero'),
                  ('pexels-34741634.jpg', 'I cartelli dei sentieri')], ('Dentro e fuori', 'Lo strudel,', 'poi i sentieri.'))]}},

    'riga-di-comando': {'pagine': {'changelog': [
        ('numeri', [('2.4', 'versione attuale'), ('48', 'versioni pubblicate'), ('99,98%', 'disponibilità 2025'), ('100', 'versioni '
                    'per tornare indietro')]),
        ('invito', 'Prova', 'La prossima versione', 'la scrivi tu.', 'riga è open source: apri una issue, proponi una modifica o '
         'leggi come si comincia nella documentazione.', [('La documentazione', '@docs', 'pieno'), ('I prezzi', '@prezzi', 'filo')])]}},

    'ruggine': {'pagine': {
        'tour': [
            ('numeri', [('22', 'date'), ('3', 'mesi in giro'), ('200', 'concerti dal 2014'), ('1', 'furgone')],
             ('Ferro e sale tour', 'Ventidue sere,', 'a volume pieno.')),
            ('invito', 'Prima del concerto', 'Il vinile arancione', 'è in prevendita.', 'Dieci pezzi registrati in una notte '
             'nell’ex acciaieria di via Narni. Lo trovi anche al banchetto, dopo il concerto.',
             [('Ascolta il disco', '@musica', 'pieno')])],
        'contatti': [
            ('faq', 'Per organizzatori', 'Le domande', 'di chi ci chiama.', [
                ('Avete una scheda tecnica?', 'Sì: palco minimo sei metri per quattro, quattro monitor, la batteria la portiamo noi.'),
                ('Suonate anche ai festival estivi?', 'Sì, da giugno a settembre: scrivici entro marzo per le date dell’estate.'),
                ('Fate interviste?', 'Volentieri, anche in radio: scrivi a booking@ruggine.band.'),
                ('Venite anche al sud?', 'Sì: nel tour di primavera ci sono Napoli, Bari e Palermo.')])]}},

    'sali-argento': {'pagine': {'contatti': [
        ('faq', 'Prima di scrivermi', 'Le domande', 'che mi fanno spesso.', [
            ('Le stampe si possono comprare?', 'Sì, tutte quelle dell’archivio, in tiratura di dieci copie, firmate e numerate.'),
            ('Quanto costa un ritratto?', 'Una sessione di due ore in studio o in città, con dieci stampe a mano, da 380 €.'),
            ('Fotografi anche a colori?', 'Sì, su pellicola anche a colori. Ma il bianco e nero resta il mio modo di guardare.'),
            ('Quanto ci vuole?', 'Le stampe a mano richiedono tre settimane: sviluppo, provini e ingrandimento.')],
         'La camera oscura è in via del Ponte 4, a Trieste.')]}},

    'sartoria-ferro': {'pagine': {
        'tessuti': [
            ('schede', 'I tessuti', 'Quattro famiglie,', 'tutte scelte a mano.', [
                ('B', 'Biella', 'Lane', ' fresche', 'Fresco lana e flanella per abiti da città, dalle tessiture del Biellese.', 'layers'),
                ('IE', 'Irlanda', 'Lini', ' d’estate', 'Lino irlandese per le giacche sfoderate di giugno e luglio.', 'sun'),
                ('SC', 'Scozia', 'Cashmere', ' e tweed', 'Per i cappotti doppiopetto e le giacche da campagna.', 'snowflake'),
                ('IT', 'Como', 'Cotoni', ' per camicie', 'Popeline e oxford tessuti a Como, per camicie con il collo a scelta.', 'shirt')])],
        'appuntamento': [
            ('faq', 'Prima di venire', 'Le domande', 'di chi entra in bottega.', [
                ('Quanto dura il primo appuntamento?', 'Un’ora: si prendono le trentadue misure e si sceglie il tessuto, con un caffè.'),
                ('Quanto tempo serve per una giacca?', 'Sei settimane, con tre prove. Per le cerimonie, prenota con tre mesi di anticipo.'),
                ('Venite a domicilio?', 'Per la prima misura sì, a Napoli. Le prove si fanno in bottega, alla luce giusta.'),
                ('I ritocchi si pagano?', 'Sui capi cuciti da noi no, per sempre.')],
             'Via dei Mille 18, Napoli, solo su appuntamento.')]}},

    'scudo': {'pagine': {
        'servizi': [
            ('passi', 'Il test di intrusione', 'Due settimane', 'da attaccanti.', [
                ('01', 'Il perimetro', 'Decidiamo insieme cosa si può attaccare e quando: niente sorprese in produzione.'),
                ('02', 'L’attacco', 'Rete, sito e persone: proviamo le strade che userebbero i criminali.'),
                ('03', 'Il rapporto', 'In italiano, con le falle in ordine di gravità e come chiuderle.'),
                ('04', 'La verifica', 'Dopo le correzioni ripassiamo, gratis, per controllare che siano chiuse davvero.')]),
            ('numeri', [('15′', 'per intervenire'), ('24/7', 'sala di controllo'), ('27001', 'certificazione ISO'), ('320', 'imprese protette')])],
        'incidente': [
            ('numeri', [('800 123 456', 'numero verde, sempre'), ('15′', 'per richiamarti'), ('24/7', 'anche a Natale')],
             ('Sotto attacco', 'Una squadra sveglia,', 'giorno e notte.'))],
        'contatti': [
            ('faq', 'Prima di chiamarci', 'Le domande', 'delle piccole imprese.', [
                ('Siamo in dieci: ci serve davvero?', 'Sì: otto attacchi su dieci colpiscono imprese sotto i cinquanta dipendenti, '
                 'proprio perché sono meno protette.'),
                ('Quanto costa un test di intrusione?', 'Per una piccola impresa da 3.800 €, con il rapporto e la verifica finale.'),
                ('Dobbiamo fermare il lavoro?', 'No: gli attacchi controllati si concordano e non bloccano nessuno.'),
                ('E la formazione?', 'Corsi di un’ora e finte truffe via email: funziona anche con chi «non ha tempo».')])]}},

    'solco-agritech': {'pagine': {'contatti': [
        ('faq', 'Il sopralluogo', 'Le domande', 'di chi coltiva.', [
            ('Quanto costa il sopralluogo?', 'Niente. Veniamo a vedere il campo e ti diciamo quante sonde servono e quanto risparmi.'),
            ('Le sonde vanno tolte per arare?', 'Sì, si sfilano in cinque minuti e si rimettono dopo la lavorazione.'),
            ('Funziona dove non c’è campo?', 'Le sonde parlano via radio con la stazione del podere: basta un punto con il segnale.'),
            ('Quanto dura la batteria?', 'Tre stagioni. La cambiamo noi, compresa nel prezzo.')],
         'Progettato a Reggio Emilia, con gli agricoltori.')]}},

    'sorriso-clinic': {'pagine': {
        'team': [
            ('numeri', [('4', 'medici'), ('3', 'igieniste'), ('2001', 'l’anno dello studio'), ('11', 'specialità')],
             ('Il team', 'Le persone', 'dietro il sorriso.')),
            ('citazione', 'Avevo paura del dentista da quando ero bambina. Mi hanno spiegato tutto prima di cominciare e mi hanno '
             'fatto fermare ogni volta che alzavo la mano.', 'Giulia', 'paziente dal 2023')],
        'prenota': [
            ('faq', 'Prima della visita', 'Le domande', 'di chi prenota.', [
                ('Quanto costa la prima visita?', '40 €, con la panoramica digitale e il preventivo scritto.'),
                ('Si può pagare a rate?', 'Sì, senza interessi, fino a ventiquattro mesi per i trattamenti più lunghi.'),
                ('Ricevete anche il sabato?', 'Sì, il sabato mattina, e dal lunedì al venerdì fino alle 20.'),
                ('Seguite anche i bambini?', 'Sì, dai tre anni, con la pedodontista e una sala d’attesa con i giochi.')],
             'Via Mazzini 20, Verona: rispondiamo entro un’ora.')]}},

    'studio-assi': {'pagine': {
        'studio': [
            ('passi', 'Il metodo', 'Cinque righe,', 'cinque passi.', [
                ('01', 'Il sopralluogo', 'Prima di disegnare guardiamo il terreno, il sole e le case intorno.'),
                ('02', 'Il foglio A4', 'La prima idea sta su un foglio: se non ci sta, è troppo complicata.'),
                ('03', 'Il modello', 'Un plastico in legno, da girare in mano davanti a te.'),
                ('04', 'Il cantiere', 'Giulia lo segue ogni settimana, con le foto e i conti aggiornati.')]),
            ('citazione', 'Volevamo una casa grande. Ci hanno convinto a farla più piccola, con più luce. Ogni mattina li ringraziamo.',
             'La famiglia della Casa sul Caldonazzo', 'Levico, 2026')],
        'contatti': [
            ('faq', 'Il primo incontro', 'Le domande', 'prima di costruire.', [
                ('Quanto costa il progetto?', 'Una percentuale sul costo dell’opera, scritta nel contratto prima di cominciare. Il '
                 'primo incontro è gratuito.'),
                ('Seguite anche le pratiche?', 'Sì: permessi, catasto e contributi per il risparmio energetico.'),
                ('Progettate case in legno?', 'Sì, con aziende del Trentino: più della metà delle nostre case sono in legno.'),
                ('Lavorate fuori dal Trentino?', 'Fra le Dolomiti e il lago di Garda, e qualche volta più lontano, se il progetto ci piace.')])]}},

    'synthetica': {
        'pagine_nuove': {'sicurezza': {'titolo': 'Sicurezza', 'menu': 'Sicurezza', 'occhiello': 'I tuoi documenti restano tuoi',
                                       'titolo_righe': ['Nessun dato', 'esce dal tuo server.'],
                                       'sotto': 'Synthetica si installa nella tua rete. Lì legge, lì risponde, lì dimentica quello che deve dimenticare.'}},
        'pagine': {
            'contatti': [
                ('faq', 'Prima della call', 'Le domande', 'di chi valuta.', [
                    ('Dove si installa?', 'Sul vostro server o nel vostro cloud privato. Una macchina con una GPU basta per cento persone.'),
                    ('Quanto ci vuole per partire?', 'Due settimane: installazione, indicizzazione dei documenti e una giornata di prova.'),
                    ('Che documenti legge?', 'PDF, Word, email e scansioni: anche i contratti firmati a mano.'),
                    ('Quanto costa?', 'A persona, al mese o all’anno: i prezzi sono sulla home, la prova sul vostro server è gratuita.')])],
            'sicurezza': [
                ('schede', 'Come proteggiamo', 'Quattro regole,', 'senza eccezioni.', [
                    ('01', 'locale', 'Sul tuo', ' server', 'Il modello gira nella tua rete: i documenti non vanno su server di altri.', 'server'),
                    ('02', 'cifrato', 'Indice', ' cifrato', 'L’indice dei documenti è cifrato, e la chiave resta a voi.', 'lock'),
                    ('03', 'permessi', 'Chi vede', ' cosa', 'Ognuno trova solo i documenti che potrebbe già aprire: rispettiamo i permessi.', 'users'),
                    ('04', 'registro', 'Tutto', ' tracciato', 'Ogni domanda e ogni risposta restano nel registro, per gli audit.', 'file-text')]),
                ('passi', 'L’installazione', 'Due settimane,', 'dal contratto alla prima risposta.', [
                    ('01', 'La call', 'Mezz’ora per capire i vostri archivi e chi lo userà.'),
                    ('02', 'Il server', 'Installiamo Synthetica nella vostra rete, con il vostro reparto IT.'),
                    ('03', 'L’indice', 'Legge i documenti una volta sola: per centomila pagine serve una notte.'),
                    ('04', 'La prova', 'Una giornata con le vostre domande vere, prima di decidere.')]),
                ('faq', 'Sicurezza', 'Le domande', 'del reparto IT.', [
                    ('Synthetica impara dai nostri documenti?', 'No: li legge per rispondere, ma il modello non viene riaddestrato.'),
                    ('Serve una connessione a internet?', 'No, funziona anche in una rete chiusa. Gli aggiornamenti si installano a mano.'),
                    ('Siete conformi al GDPR?', 'Sì: i dati restano da voi e il registro permette di rispondere a ogni richiesta.'),
                    ('Chi può vedere le domande?', 'Solo gli amministratori che scegliete voi, nel registro.')]),
                ('invito', 'Parliamone', 'Una prova', 'sul tuo server.', 'Una call di mezz’ora, poi due settimane per vederlo '
                 'rispondere sui vostri documenti. Senza impegno.', [('Parliamone', '@contatti', 'pieno'), ('Il prodotto', '@prodotto', 'filo')])]}},

    'teatro-darsena': {'pagine': {'teatro': [
        ('numeri', [('300', 'posti'), ('1998', 'l’anno del magazzino'), ('8', 'spettacoli a stagione'), ('40', 'cittadini nel coro di Medea')],
         ('Il teatro', 'Un magazzino,', 'trecento posti.')),
        ('foto', [('pexels-4718641.jpg', 'La sala del teatro'), ('pexels-6895798.jpg', 'Una prova sul palco'),
                  ('pexels-23092130.jpg', 'Il porto dietro le quinte')], ('Dietro le quinte', 'Il palco al centro,', 'il porto dietro.'))]}},

    'tostatura-nove': {'pagine': {'contatti': [
        ('faq', 'Prima di ordinare', 'Le domande', 'sul caffè.', [
            ('In grani o macinato?', 'In grani, se puoi: dura di più. Se lo vuoi macinato, scegli il metodo e lo maciniamo il giorno della spedizione.'),
            ('Quanto dura un pacco?', 'Al meglio per un mese dalla tostatura. Sul pacco c’è la data del giovedì in cui l’abbiamo tostato.'),
            ('Spedite in tutta Italia?', 'Sì, in due giorni, gratis sopra i 30 €. In Europa in quattro.'),
            ('Fate caffè per bar e uffici?', 'Sì, con la macchina in comodato: scrivici quante tazzine fate al giorno.')],
         'Via del Molo 9, Trieste: il caffè si beve in piedi.')]}},

    'vinile-club': {'pagine': {
        'dischi': [
            ('passi', 'L’usato', 'Come valutiamo', 'i tuoi dischi.', [
                ('01', 'Porta i dischi', 'Dal martedì al venerdì, senza appuntamento, fino a cento alla volta.'),
                ('02', 'Li ascoltiamo', 'Ogni disco sul giradischi del bancone: graffi, salti, stato della copertina.'),
                ('03', 'Ti facciamo il prezzo', 'Disco per disco, davanti a te. Le prime stampe le controlliamo sulla matrice.'),
                ('04', 'Ti paghiamo', 'Subito in contanti, oppure il 30% in più in buono da spendere da noi.')]),
            ('citazione', 'Cercavo una prima stampa di un disco di jazz da dieci anni. L’hanno trovata, me l’hanno fatta ascoltare '
             'al bancone e mi hanno anche fatto lo sconto.', 'Giorgio', 'collezionista, Bologna')],
        'eventi': [
            ('faq', 'Gli eventi', 'Le domande', 'prima di venire.', [
                ('Gli eventi costano?', 'No, l’ingresso è sempre libero. I posti a sedere sono trenta: chi arriva prima li prende.'),
                ('A che ora cominciano?', 'Alle 19, in negozio chiuso al pubblico, e finiscono alle 21.'),
                ('Posso suonare da voi?', 'Sì, se fai uscire un disco: scrivici con un link, organizziamo un acustico fra gli scaffali.'),
                ('Il mercatino è aperto a tutti?', 'Sì, una domenica al mese: chi vuole vendere i suoi dischi prenota un banco.')])]}},

    'volo-leggero': {'pagine': {
        'regala': [
            ('passi', 'Il buono regalo', 'Come funziona', 'il regalo.', [
                ('01', 'Componi', 'Scegli il volo e gli extra: colazione, foto e video, una bottiglia di Barolo.'),
                ('02', 'Ricevi', 'Il buono in PDF subito, o in una scatola spedita gratis.'),
                ('03', 'Prenota', 'Chi lo riceve sceglie la data, entro un anno, da aprile a novembre.'),
                ('04', 'Vola', 'All’alba, sopra le Langhe, con il brindisi all’atterraggio.')]),
            ('faq', 'Il regalo', 'Le domande', 'su chi regala.', [
                ('Il buono scade?', 'Dopo un anno. Se il meteo fa saltare il volo, lo spostiamo senza costi, anche oltre la scadenza.'),
                ('Si può regalare a due persone?', 'Sì: componi il volo condiviso per due, o il volo per due con la cesta solo per voi.'),
                ('C’è un limite di età?', 'Dai sei anni in su, accompagnati. Non ci sono limiti massimi.'),
                ('Posso cambiare il nome sul buono?', 'Sì, con una email, fino al giorno prima del volo.')])],
        'contatti': [
            ('faq', 'Prima del volo', 'Le domande', 'di chi vola.', [
                ('A che ora si parte?', 'All’alba: il ritrovo è un’ora prima del sorgere del sole, vicino ad Alba. Si rientra per le nove.'),
                ('Cosa mi metto?', 'Scarpe comode e una giacca: in quota fa fresco, ma il bruciatore scalda.'),
                ('E se c’è vento?', 'Si vola solo con il vento sotto i dieci chilometri orari. La sera prima ti chiama il pilota.'),
                ('Si ha paura in alto?', 'Quasi mai: la cesta sale piano e non c’è vuoto sotto i piedi. Chi ha paura di solito si emoziona.')],
             'Ufficio ad Alba: risponde sempre un pilota.')]}},
}
