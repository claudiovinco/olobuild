# -*- coding: utf-8 -*-
"""Titoli in serie «Quattro X, una Y.» (14 nov 2026). 475 titoli di sezione su 2554 cominciavano con un numero, e 91 temi
ne avevano più di due: la stessa formula ripetuta faceva sembrare i temi scritti con lo stesso stampo. In ogni tema
restano i due titoli numerici più forti (i numeri veri dell'attività); gli altri dicono la stessa cosa in un'altra forma,
variata da tema a tema (niente «Tutta la collezione, …» o «Una famiglia, …» in cinque temi).
Le righe restano due, con i loro colori e corsivi: cambia solo il testo. togli_indicazioni.titoli() li applica, anche
quando base.write_theme rigenera un tema. Chiave: id del tema → righe originali (senza spazi ai bordi) unite da «|»."""

RISCRITTI = {
    'ago-e-ancora': {'Cinque passi,|nessuna fretta.': ['Dalla prima idea', 'all’ultimo ritocco.']},
    'arnia': {
        'Quattro passi,|un anno di api.': ['Un alveare tuo,', 'dalla primavera al miele.'],
        'Sei postazioni,|dai castagni ai prati.': ['Dai castagni', 'ai prati d’alta quota.'],
        'Cinque fioriture,|cinque colori.': ['Ogni fioritura', 'ha il suo colore.'],
    },
    'bacca-nera': {'Tre persone,|un alambicco.': ['Le persone', 'dell’alambicco.']},
    'baita-stua': {
        'Venti passi nella neve,|e c’è la sauna.': ['Fuori, nella neve,', 'c’è la sauna.'],
        'Due stagioni,|due valli diverse.': ['D’inverno e d’estate,', 'una valle diversa.'],
        'Sei giornate|da ricordare.': ['Le giornate', 'da ricordare.'],
        'Due righe,|e la baita è vostra.': ['Scriveteci le date,', 'la baita è vostra.'],
    },
    'bandeja': {'Tre maestri,|otto campi.': ['I maestri', 'che vi aspettano in campo.']},
    'bolina': {'Tre corsi,|tre mari diversi.': ['Dal lago', 'al mare aperto.']},
    'cangiante': {
        'Quattro capi,|nessun colore fisso.': ['La collezione', 'che non sta ferma.'],
        'Due righe,|rispondiamo noi.': ['Una domanda?', 'Rispondiamo noi.'],
    },
    'capitonne': {'Tre generazioni,|gli stessi attrezzi.': ['Dal nonno al nipote,', 'gli stessi attrezzi.']},
    'carico-libero': {
        'Tre accessi,|un prezzo al mese.': ['Un prezzo al mese,', 'l’accesso che vi serve.'],
        'Sei dei diciotto,|da conoscere.': ['I coach', 'da conoscere.'],
        'Tre domande,|poi ti chiamiamo.': ['Prenota la prova,', 'poi ti chiamiamo.'],
    },
    'cementina': {'Tre generazioni,|una pressa.': ['La pressa', 'passata di padre in figlio.']},
    'cerniera': {'Otto capi,|ventitré zip.': ['Zip per zip,', 'la collezione.']},
    'chiavistello': {'Tre agenti,|tutti nati qui.': ['Gli agenti,', 'tutti nati qui.']},
    'clorofilla': {
        'Quattro passi,|una pianta salva.': ['Dalla foglia gialla', 'alla pianta salva.'],
        'Cinque da cui|cominciare.': ['Per chi comincia,', 'le piante giuste.'],
    },
    'cric': {
        'Otto ponti|in corso Francia.': ['L’officina', 'in corso Francia.'],
        'Tre auto,|tre spie spente.': ['Le spie', 'tornate spente.'],
    },
    'cromo': {'Cinque passi,|nessuna sorpresa.': ['Dal brief', 'alla consegna.']},
    'cuccia': {
        'Quattro passi,|senza fretta.': ['L’adozione,', 'senza fretta.'],
        'Due righe su di voi,|al resto pensiamo noi.': ['Raccontateci di voi,', 'al resto pensiamo noi.'],
    },
    'diottria': {
        'Tre momenti,|venti minuti.': ['L’esame,', 'un momento alla volta.'],
        'Quattro passi,|un viso.': ['Dal viso', 'alla montatura.'],
    },
    'doppia-mandata': {
        'Quattro casi,|una porta sola.': ['I casi', 'dietro la porta.'],
        'Tre passi,|e la porta si chiude.': ['Scegliete la sera,', 'la porta si chiude.'],
        'Cinque regole|che in partita non vi diciamo.': ['Le regole', 'che in partita non vi diciamo.'],
    },
    'due-mani': {
        'Tre case,|tre colori.': ['Le pareti', 'di chi ci ha chiamato.'],
        'Sette pittori,|tre li conoscete qui.': ['Chi viene', 'a dipingere da voi.'],
    },
    'elastan': {'Dodici capi,|quattro vie.': ['La collezione,', 'tesa in quattro vie.']},
    'fermento': {
        'Tre persone,|otto tini.': ['Fra i tini,', 'chi fa la birra.'],
        'Sei spine,|tavoli di botte.': ['Le spine', 'e i tavoli di botte.'],
    },
    'foglia-lenta': {
        'Sei tè da cui|cominciare.': ['Per la prima volta', 'in sala.'],
        'Dodici tavoli,|la luce del canale.': ['La sala', 'sulla luce del canale.'],
    },
    'fondente': {
        'Sei passaggi,|tutti qui.': ['Dalla fava alla tavoletta,', 'tutto qui.'],
        'Tre mani|sul marmo.': ['Le mani', 'sul marmo.'],
    },
    'fonderia-mella': {
        'Tre generazioni,|lo stesso forno acceso.': ['Lo stesso forno,', 'mai spento.'],
        'Quattro leghe,|scelte per ogni progetto.': ['Le leghe,', 'scelte per ogni progetto.'],
        'Tre mestieri,|lo stesso metallo.': ['Lo stesso metallo', 'per tutti i settori.'],
        'Sei passaggi,|una sola fonderia.': ['Dal disegno al getto,', 'una sola fonderia.'],
        'Quattro norme,|verificate ogni anno.': ['Le certificazioni,', 'verificate ogni anno.'],
    },
    'forma': {
        'Quattro modelli,|il tuo piede.': ['I modelli,', 'sul tuo piede.'],
        'Tre appuntamenti,|il resto lo facciamo noi.': ['Voi venite in bottega,', 'il resto lo facciamo noi.'],
    },
    'fornace': {
        'Tre tempi,|una notte di riposo.': ['Dal fuoco', 'alla notte di riposo.'],
        'Tre mani|davanti al fuoco.': ['Davanti al fuoco,', 'ogni giorno.'],
    },
    'fuori-registro': {
        'Otto stampe,|tre colori.': ['Le stampe,', 'a tre colori.'],
        'Tre passate|di racla.': ['Una passata di racla', 'per ogni colore.'],
    },
    'fuorigiri': {
        'Due righe,|poi un caffè.': ['Scrivici,', 'poi un caffè.'],
        'Quattro passi,|una moto sola.': ['Dal progetto al collaudo,', 'una moto sola.'],
    },
    'fuorimisura': {'Otto capi,|una taglia.': ['Tutti i capi,', 'una taglia sola.']},
    'giroscopio': {
        'Cinque mestieri,|lo stesso sguardo.': ['Dal ristorante al museo,', 'lo stesso sguardo.'],
        'Due ore in casa,|due giorni al link.': ['Una mattina in casa,', 'poi il link.'],
        'Quattro domande,|poi ti richiamiamo.': ['Qualche domanda,', 'poi ti richiamiamo.'],
        'Dodici tour,|cinque settori.': ['I tour,', 'settore per settore.'],
        'Quattro luoghi,|un giro solo.': ['Un giro solo,', 'da un luogo all’altro.'],
        'Quattro gesti,|nessuna istruzione.': ['Si usa', 'senza istruzioni.'],
    },
    'girotondo': {'Quattro posti|per crescere.': ['Gli spazi', 'per crescere.']},
    'giunto': {
        'Due righe sulla postazione,|veniamo noi.': ['Raccontateci la postazione,', 'veniamo noi.'],
        'Quattro lavori|che un robot fa bene.': ['I lavori', 'che un robot fa bene.'],
    },
    'gomitolo': {
        'Tre fili,|una maglia.': ['Dal filo', 'alla maglia.'],
        'Otto maglie,|otto gomitoli.': ['Le maglie,', 'e i loro gomitoli.'],
    },
    'halden-classic': {'Sei curiosità|dall’archivio.': ['Curiosità', 'dall’archivio.']},
    'il-pozzo': {'Tre generazioni|dietro al banco.': ['Dietro al banco,', 'di padre in figlio.']},
    'inchiostro-riso': {'Tre mattine|per cominciare.': ['Le mattine', 'per cominciare.']},
    'insegna': {
        'Sei cose|che non dovete più ricordare.': ['Le cose', 'che non dovete più ricordare.'],
        'Tre piani,|o su chiamata.': ['Un piano fisso,', 'o su chiamata.'],
        'Sei marchi,|sei modi di durare.': ['Marchi', 'fatti per durare.'],
        'Quattro tempi,|sempre gli stessi.': ['Ogni lavoro,', 'con gli stessi tempi.'],
    },
    'kemet': {
        'Cinque domande,|prima di entrare.': ['Qualche domanda,', 'prima di entrare.'],
        'Sette oggetti|da non perdere.': ['Gli oggetti', 'da non perdere.'],
        'Tre sale,|tre domande.': ['Sala dopo sala,', 'una domanda.'],
    },
    'lamiere-astico': {'Quattro norme,|controllate ogni anno.': ['Le certificazioni,', 'controllate ogni anno.']},
    'lastra': {'Otto giacche,|ogni strato scritto.': ['Ogni giacca', 'con gli strati scritti.']},
    'leva-consulenza': {
        'Cinque aziende,|cinque punti giusti.': ['Il punto giusto,', 'azienda per azienda.'],
        'Sei settori,|gli stessi nodi.': ['Gli stessi nodi,', 'in ogni settore.'],
        'Quattro leve,|una per ogni nodo.': ['Una leva', 'per ogni nodo.'],
        'Quattro passi,|sempre gli stessi.': ['Ogni volta', 'gli stessi passi.'],
        'Due settimane,|a prezzo fisso.': ['La diagnosi,', 'a prezzo fisso.'],
        'Quattro passi,|due minuti.': ['Scriveteci:', 'bastano due minuti.'],
        'Quattro fasi,|nero su bianco.': ['Nero su bianco,', 'fase per fase.'],
    },
    'linea-reno': {
        'Tre modi|di non fermarsi.': ['Contratti', 'per non fermarsi.'],
        'Tre mestieri,|tre modi di pulire una macchina.': ['Ogni settore', 'pulisce la macchina a modo suo.'],
    },
    'luce-rossa': {
        'Cinque pellicole,|un foulard.': ['Le pellicole,', 'sullo stesso foulard.'],
        'Quattro foulard|da cominciare.': ['I foulard', 'di questa stagione.'],
    },
    'lunga-posa': {
        'Sei serate,|sei quartieri.': ['Le serate,', 'quartiere per quartiere.'],
        'Otto foto,|otto serate.': ['Una foto', 'per ogni serata.'],
        'Sei lavori,|sei luci diverse.': ['I lavori scelti,', 'ognuno con la sua luce.'],
        'Quattro passi,|nessuna fretta.': ['Come lavoro,', 'senza fretta.'],
        'Sei modi|di farvi fotografare.': ['I modi', 'di farvi fotografare.'],
        'Quattro serie,|una luce sola.': ['Le serie,', 'con una luce sola.'],
        'Sei matrimoni,|sei luci diverse.': ['Dai matrimoni,', 'una luce diversa ogni volta.'],
        'Tre modi,|prezzi chiusi.': ['Quanto costa,', 'a prezzi chiusi.'],
    },
    'madia': {
        'Otto prodotti,|una ricetta, il giovedì.': ['La box del mese,', 'con la ricetta del giovedì.'],
        'Trenta famiglie,|fra pianura e Appennino.': ['I produttori,', 'fra pianura e Appennino.'],
    },
    'mano-libera': {
        'Tre poltrone,|tre mani.': ['I barbieri', 'della bottega.'],
        'Quattro tempi,|mezz’ora.': ['Il taglio,', 'in mezz’ora.'],
        'Tre tempi,|venti minuti.': ['La rasatura,', 'con calma.'],
        'Quattro cose,|non di più.': ['Da portare a casa,', 'non di più.'],
    },
    'marmi-lizza': {'Dodici lastre,|quattro marmi.': ['Le lastre', 'in magazzino.']},
    'meriggio-energia': {'Quattro passi,|dalla piazza all’incentivo.': ['Dalla piazza', 'all’incentivo.']},
    'morsetto': {'Tre case,|tre sere salvate.': ['La sera', 'che abbiamo salvato.']},
    'nido-coworking': {
        'Quattro modi per|non lavorare da soli.': ['Una rete per', 'non lavorare da soli.'],
        'Cinque livelli,|un solo badge.': ['Tutti i piani,', 'un solo badge.'],
        'Tre sale,|nessun cavo da cercare.': ['Le sale,', 'senza un cavo da cercare.'],
    },
    'nove-posti': {
        'Cinque momenti,|un solo ritmo.': ['La sera,', 'con un solo ritmo.'],
        'Tre persone,|nove ospiti.': ['Dietro il banco,', 'tre paia di mani.'],
        'Quattro cose|da sapere.': ['Le cose', 'da sapere.'],
    },
    'officine-quota': {
        'Quattro reparti,|un solo collaudo.': ['Ogni reparto,', 'un solo collaudo.'],
        'Quattro mestieri|che non perdonano.': ['I mestieri', 'che non perdonano.'],
        'Quattro momenti,|un solo rapporto.': ['Il controllo,', 'in un solo rapporto.'],
        'Quattro norme,|verificate ogni anno.': ['Le norme,', 'verificate ogni anno.'],
    },
    'ombra-riserbo': {
        'Tre stanze|in corso Genova.': ['L’agenzia,', 'in corso Genova.'],
        'Quattro passi,|sempre gli stessi.': ['Come lavoriamo,', 'un fascicolo alla volta.'],
    },
    'ora-blu': {'Cinque passaggi,|otto settimane.': ['Dall’idea al flacone,', 'con calma.']},
    'orbita-bassa': {
        'Quattro modi|di guardare giù.': ['A che cosa serve', 'guardare giù.'],
        'Sei numeri|dalla stessa foto.': ['Dalla stessa foto,', 'tutti i numeri.'],
    },
    'osteria-fumo': {'Quattro piatti|da non perdere': ['I piatti', 'da non perdere']},
    'passo-lento': {'Quattro passi|e si parte.': ['Si prenota,', 'e si parte.']},
    'presa-diretta': {
        'Quattro stanze,|un suono.': ['Le stanze,', 'un suono solo.'],
        'Tre orecchie|in regia.': ['In regia,', 'orecchie allenate.'],
        'Tre modi|di lavorare.': ['In studio,', 'come preferite.'],
    },
    'radice': {
        'Quattro passi,|voi vi sedete.': ['Noi cuciniamo,', 'voi vi sedete.'],
        'Due persone,|o trenta.': ['Una cena a due,', 'o per trenta.'],
        'Quattro carte,|quattro piatti veri.': ['Le ricette,', 'piatti veri.'],
        'Otto persone,|una stagione.': ['I corsi,', 'una stagione intera.'],
    },
    'raspa': {'Otto capi,|otto lavaggi.': ['Un capo', 'per ogni lavaggio.']},
    'rastrello': {
        'Dieci giardinieri,|tre li conoscete qui.': ['Il vostro giardino,', 'nelle loro mani.'],
        'Tre giardini,|tutto l’anno.': ['Chi ci chiama,', 'tutto l’anno.'],
    },
    'respiro': {'Due righe bastano,|il resto lo diremo a voce.': ['Poche righe bastano,', 'il resto lo diremo a voce.']},
    'rovescio': {'Due righe,|rispondiamo noi.': ['Per ogni domanda,', 'ci siamo noi.']},
    'sartoria-ferro': {
        'Quattro attrezzi,|nessuna fretta.': ['Gli attrezzi', 'delle misure.'],
        'Quattro famiglie,|tutte scelte a mano.': ['I tessuti,', 'tutti scelti a mano.'],
    },
    'sbieco': {
        'Otto abiti,|tutti di sbieco.': ['Tagliati di sbieco,', 'dal primo all’ultimo.'],
        'Quattro abiti,|quattro sere.': ['Gli abiti,', 'indossati una sera.'],
    },
    'scappamento': {
        'Cinque lavori,|tutti fatti qui.': ['I lavori,', 'tutti fatti qui.'],
        'Tre lenti,|un tornio.': ['Al tornio,', 'con la lente.'],
    },
    'scheda-madre': {
        'Tre passi,|poi ci pensiamo noi.': ['Prenota,', 'poi ci pensiamo noi.'],
        'Sette scelte,|un prezzo chiaro.': ['Scelta per scelta,', 'un prezzo chiaro.'],
        'Quattro pezzi,|spiegati semplice.': ['I pezzi che contano,', 'spiegati semplice.'],
        'Sei computer,|pronti da portare via.': ['I computer', 'pronti da portare via.'],
    },
    'scia': {'Tre generazioni|di barche da lago.': ['Barche da lago', 'da sempre.']},
    'scroscio': {
        'Tre strati,|zero gocce.': ['Giacche e stivali,', 'nemmeno una goccia.'],
        'Tre strati,|una prova.': ['Ogni strato', 'passa la prova.'],
    },
    'scudo': {'Tre modi|di dormire tranquilli.': ['Servizi', 'per dormire tranquilli.']},
    'settimo-asse': {
        'Sei tappe,|un solo referente.': ['Dal sopralluogo al collaudo,', 'un solo referente.'],
        'Sei tappe,|nessuna sorpresa.': ['Le tappe,', 'senza sorprese.'],
    },
    'sifone': {
        'Due righe,|e vi richiamiamo.': ['Lasciate il numero,', 'vi richiamiamo.'],
        'Sei idraulici,|tre li conoscete qui.': ['Chi bussa', 'alla vostra porta.'],
        'Tre case,|tutte asciutte.': ['Case', 'tornate all’asciutto.'],
    },
    'sincope': {'Otto serate,|una per disco.': ['Le prossime sere,', 'una per disco.']},
    'soglia': {'Due di loro,|al lavoro.': ['In galleria,', 'al lavoro.']},
    'solleone': {
        'Due righe|e ci sentiamo.': ['La vostra data,', 'e ci sentiamo.'],
        'Quattro passi,|zero pensieri.': ['Dalla data alle foto,', 'zero pensieri.'],
    },
    'stender': {
        'Quattro cotoni,|un lino.': ['I cotoni,', 'e un lino.'],
        'Quattro camicie|da cominciare.': ['Le camicie', 'appena cucite.'],
    },
    'strapiombo': {'Tre istruttori,|tutte le pareti.': ['Gli istruttori,', 'su tutte le pareti.']},
    'strato': {
        'Due righe sul pezzo,|al resto pensiamo noi.': ['Mandateci il disegno,', 'pensiamo noi al resto.'],
        'Quattro mestieri|sotto un tetto.': ['Sotto un tetto,', 'tutti i mestieri del pezzo.'],
    },
    'studio-assi': {'Cinque righe,|cinque passi.': ['Le regole,', 'una dopo l’altra.']},
    'synthetica': {
        'Quattro passi,|un pomeriggio.': ['Un pomeriggio,', 'ed è pronto.'],
        'Due settimane,|dal contratto alla prima risposta.': ['Dal contratto', 'alla prima risposta.'],
    },
    'tavola-errante': {
        'Otto tavole,|da giugno a ottobre.': ['Le tavole,', 'da giugno a ottobre.'],
        'Cinque portate,|un luogo.': ['Il menu,', 'fatto col luogo.'],
    },
    'teatro-seta': {'Tre modi|di abbonarsi.': ['Gli abbonamenti', 'della stagione.']},
    'telaio': {
        'Quattro passi,|una bici sola.': ['Il percorso', 'di una bici sola.'],
        'Due ore,|in officina.': ['La misurazione,', 'in officina.'],
        'Quattro famiglie,|tutte su misura.': ['I telai,', 'tutti su misura.'],
    },
    'tessiture-navetta': {
        'Tre cose,|tutte da toccare.': ['Nel campionario,', 'tutto da toccare.'],
        'Quattro certificati,|rinnovati ogni anno.': ['I certificati,', 'rinnovati ogni anno.'],
    },
    'trentagradi': {
        'Quattro basi,|per sempre.': ['Basi', 'che durano.'],
        'Otto basi,|cento lavaggi l’una.': ['Le basi,', 'a prova di cento lavaggi.'],
        'Tre cose|che lo fanno durare.': ['Quello che', 'lo fa durare.'],
    },
    'truciolo': {
        'Due righe,|e una foto se c’è.': ['Il mobile da fare?', 'Mandate una foto, se c’è.'],
        'Tre case,|tre mobili.': ['Mobili', 'entrati in casa.'],
    },
    'undici-ulivi': {
        'Quattro materiali,|tutti da qui intorno.': ['I materiali,', 'tutti da qui intorno.'],
        'Quattro modi|di stare qui.': ['I modi', 'di stare qui.'],
    },
    'varco-nove': {
        'Quattro mestieri,|un polo solo.': ['Un polo solo', 'per tutta la filiera.'],
        'Quattro mestieri,|si comincia affiancati.': ['Posizioni aperte:', 'si comincia affiancati.'],
        'Quattro passi,|il curriculum alla fine.': ['Candidarsi:', 'il curriculum alla fine.'],
    },
    'venatura': {
        'Tre generazioni,|una pialla.': ['La pialla', 'del nonno.'],
        'Cinque passi,|una sola persona.': ['Dal disegno al mobile,', 'una sola persona.'],
    },
    'vertebra': {'Tre sale|e la palestra.': ['Le stanze', 'e la palestra.']},
    'zolla': {'Tre volontarie,|e un trattore piccolo.': ['Le volontarie,', 'e un trattore piccolo.']},
}
