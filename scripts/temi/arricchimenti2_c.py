# -*- coding: utf-8 -*-
"""Arricchimenti della seconda passata (14 nov 2026), terzo blocco: da Rewind Vintage a Volo Leggero."""

C = {
    'rewind-vintage': {
        'vendi': [
            ('passi', 'Vendere da noi', 'Dalle foto', 'al pagamento.', [
                ('01', 'Le foto', 'Due foto del capo e dell’etichetta, con le misure.'),
                ('02', 'La valutazione', 'Entro tre giorni ti diciamo se lo prendiamo e a quanto.'),
                ('03', 'La consegna', 'In negozio o col corriere, pagato da noi.'),
                ('04', 'Il pagamento', 'Subito in buono, o in contanti quando il capo è venduto.')]),
            ('carte', 'Cosa cerchiamo', 'I capi', 'che prendiamo volentieri.', [
                ('pixabay-4021609.jpg', 'Anni Sessanta', 'Righe e colori', 'Abiti dritti, righe larghe e colori pieni, in buono stato.'),
                ('unsplash-vAOV6fSUSlU.jpg', 'Anni Settanta', 'Pois e occhiali', 'Abiti a pois, camicie con il collo grande e accessori.'),
                ('unsplash-dt1FIq5yX_c.jpg', 'Anni Ottanta', 'Spalline', 'Giacche con le spalle, giubbotti e tutto quello che brilla.'),
                ('unsplash-miYI75ynzGg.jpg', 'Anni Novanta', 'Jeans e felpe', 'Jeans a vita alta, felpe dei college e scarpe da ginnastica.')])]},

    'rifugio-larice': {
        'prenota': [
            ('schede', 'Al rifugio', 'Come si dorme', 'a 2.104 metri.', [
                ('01', '', 'Le camerate', '', 'Letti a castello con coperte e cuscino: porta il sacco lenzuolo.', 'bed'),
                ('02', '', 'La cena', '', 'Alle 19, un menu unico di montagna, con la zuppa e il dolce.', 'utensils'),
                ('03', '', 'Il silenzio', '', 'Dalle 22 si spegne la luce: la sveglia la dà il sole.', 'moon')]),
            ('frase', 'Qui sopra il tempo si misura in passi e in nuvole. Il resto aspetta a valle.', 'Il gestore del Rifugio Larice')]},

    'riga-di-comando': {
        'prezzi': [
            ('faq', 'I piani', 'Le domande', 'prima di pagare.', [
                ('Il piano gratuito ha limiti di tempo?', 'No: tre siti, per sempre, con il certificato e i backup.'),
                ('Posso pagare con la fattura?', 'Sì, in euro, con fattura elettronica.'),
                ('E se cambio idea?', 'Il piano si disdice da terminale, con un comando. Il mese in corso resta attivo.')]),
            ('frase', 'Un comando per pubblicare, uno per tornare indietro. Il resto del tempo lo usi per scrivere codice.',
             'Riga di Comando')]},

    'ruggine': {
        'tour': [
            ('faq', 'Ai concerti', 'Le domande', 'prima di venire.', [
                ('A che ora si comincia?', 'Le porte aprono alle 20, il gruppo di apertura alle 21, noi verso le 22.'),
                ('I biglietti si trovano anche alla cassa?', 'Se la data non è esaurita sì, ma online costano meno.'),
                ('C’è il banchetto dei dischi?', 'Sempre: vinili, magliette e il nuovo album in prevendita.')])],
        'musica': [
            ('righe', 'I dischi', 'Quattro album,', 'dal garage al palco.', [
                ('2014', 'Ferro', 'Primo album', 'autoprodotto'),
                ('2017', 'Lamiera', 'Secondo album', 'vinile'),
                ('2021', 'Officina', 'Terzo album', 'registrato dal vivo'),
                ('2026', 'Ossido', 'Nuovo album', 'in uscita')], '@musica'),
            ('frase', 'Un disco nostro si suona forte, in macchina, coi finestrini giù.', 'Ruggine')],
        'contatti': [
            ('schede', 'Per scriverci', 'Chi risponde', 'a cosa.', [
                ('01', '', 'Concerti', '', 'Per portarci a suonare: l’agenzia che segue il tour.', 'calendar'),
                ('02', '', 'Stampa', '', 'Interviste, foto e accrediti per i concerti.', 'mic'),
                ('03', '', 'Negozio', '', 'Vinili, magliette e spedizioni.', 'package')])]},

    'sali-argento': {
        'archivio': [
            ('carte', 'L’archivio', 'Ritratti,', 'uno per volta.', [
                ('pexels-photo-15583830.jpeg', 'In città', 'Ritratto', 'Una mattina per strada, con la luce del porto alle spalle.'),
                ('pexels-photo-36828104.jpeg', 'Un sorriso', 'Ritratto', 'In studio, con la luce di una finestra sola.'),
                ('pexels-photo-8960946.jpeg', 'Al lavoro', 'Ritratto', 'Nel cantiere, col casco: la persona nel suo posto.'),
                ('pixabay-3352770.jpg', 'Contro il muro', 'Ritratto', 'Un muro di colore e un’ora di pazienza.')]),
            ('frase', 'Un ritratto riesce quando la persona smette di posare. Di solito succede dopo la ventesima foto.',
             'La fotografa di Sali d’Argento')],
        'studio': [
            ('passi', 'Una seduta', 'Come va', 'un ritratto in studio.', [
                ('01', 'Il caffè', 'Mezz’ora per conoscerci, senza macchina fotografica.'),
                ('02', 'La luce', 'Una finestra, un pannello e poche prove.'),
                ('03', 'Gli scatti', 'Un rullino in pellicola e qualche foto in digitale.'),
                ('04', 'I provini', 'Una settimana dopo, si scelgono insieme le foto da stampare.')])],
        'contatti': [
            ('schede', 'I servizi', 'Cosa fotografo', 'più spesso.', [
                ('01', '', 'Ritratti', '', 'Per sé, per il lavoro, per regalarlo a qualcuno.', 'camera'),
                ('02', '', 'Luoghi', '', 'Case, botteghe e paesaggi del Carso e della costa.', 'map'),
                ('03', '', 'Stampe', '', 'In camera oscura, su carta baritata, firmate e numerate.', 'image')])]},

    'sartoria-ferro': {
        'metodo': [
            ('carte', 'Il lavoro', 'Dalle misure', 'alla giacca.', [
                ('pexels-6766284.jpg', 'Le misure', 'Venti misure', 'Prese in piedi e seduti, perché la giacca stia bene anche a tavola.'),
                ('pexels-7775460.jpg', 'Il cartamodello', 'Col gesso', 'Disegnato sul tessuto e tagliato a mano, su misura per una persona sola.'),
                ('pexels-36356684.jpg', 'Le cuciture', 'A mano', 'La spalla, il collo e le asole cuciti col filo di seta.'),
                ('pexels-6765656.jpg', 'La consegna', 'Finita', 'L’ultima prova e la giacca che esce dalla bottega sulle spalle del cliente.')])],
        'tessuti': [
            ('frase', 'Il tessuto si sceglie toccandolo: la mano sa prima degli occhi quale giacca diventerà.', 'Gennaro, sarto')],
        'appuntamento': [
            ('passi', 'Il primo appuntamento', 'Cosa succede', 'in bottega.', [
                ('01', 'La chiacchierata', 'Cosa vi serve e quando la indosserete.'),
                ('02', 'I tessuti', 'Si guardano e si toccano i campionari, con calma.'),
                ('03', 'Le misure', 'Le prime misure, e la data della prova.')])]},

    'scudo': {
        'contatti': [
            ('passi', 'Dopo la chiamata', 'Come iniziamo', 'a proteggervi.', [
                ('01', 'Il controllo', 'Una mattina per guardare computer, rete e posta.'),
                ('02', 'Il rapporto', 'Le falle trovate, in ordine di gravità e senza gergo.'),
                ('03', 'Le correzioni', 'Le sistemiamo noi, o insieme al vostro tecnico.'),
                ('04', 'La sorveglianza', 'La sala di controllo vi guarda giorno e notte.')]),
            ('frase', 'Le piccole imprese non vengono attaccate perché sono importanti, ma perché sono facili. Il nostro lavoro è '
             'renderle difficili.', 'Scudo')]},

    'solco-agritech': {
        'prezzi': [
            ('schede', 'Nel prezzo', 'Cosa comprende', 'ogni piano.', [
                ('01', '', 'Le sonde', '', 'Installate da noi nel terreno, con la manutenzione.', 'cpu'),
                ('02', '', 'Il drone', '', 'Un volo al mese sui campi, con la mappa delle piante che soffrono.', 'send'),
                ('03', '', 'Il consiglio', '', 'Un agronomo che vi dice quando irrigare e quanto.', 'sprout')]),
            ('faq', 'I piani', 'Le domande', 'di chi coltiva.', [
                ('Quanto dura il contratto?', 'Una stagione, da marzo a ottobre; poi si rinnova se vi è servito.'),
                ('Serve internet nei campi?', 'No: le sonde trasmettono via radio fino alla centralina.'),
                ('Si può provare su un campo solo?', 'Sì, il primo anno anche su un ettaro.')])],
        'contatti': [
            ('frase', 'Lo stesso campo, irrigato quando serve e non quando lo dice il calendario, dà più raccolto con meno acqua.',
             'Gli agronomi di Solco')]},

    'sorriso-clinic': {
        'prenota': [
            ('passi', 'La prima visita', 'Come si svolge', 'il primo appuntamento.', [
                ('01', 'L’accoglienza', 'Una scheda sulla salute e due parole su cosa vi porta da noi.'),
                ('02', 'La visita', 'Il dentista guarda, fotografa e, se serve, fa una radiografia.'),
                ('03', 'Il piano', 'Le cure possibili, con i tempi e il preventivo scritto.')]),
            ('frase', 'Il primo appuntamento non ha trapani: si guarda, si parla e si decide insieme.', 'Lo studio Sorriso')]},

    'studio-assi': {
        'progetti': [
            ('righe', 'L’indice', 'I progetti', 'dello studio.', [
                ('2025', 'Casa sul Calisio', 'Residenza', 'Trento'),
                ('2024', 'Scuola di Gardolo', 'Pubblico', 'Trento'),
                ('2024', 'Biblioteca di valle', 'Pubblico', 'Val di Non'),
                ('2023', 'Uffici in centro', 'Lavoro', 'Trento'),
                ('2022', 'Masi di sopra', 'Recupero', 'Val di Fiemme')], '@progetti'),
            ('frase', 'Costruiamo poco e bene: niente che non serva, niente che non duri.', 'Studio Assi')],
        'contatti': [
            ('passi', 'Il primo incontro', 'Dal terreno', 'al progetto.', [
                ('01', 'Il sopralluogo', 'Un giro sul terreno o nella casa da rifare, con il sole e il vento.'),
                ('02', 'Lo studio', 'Le norme, i costi e due idee disegnate a mano.'),
                ('03', 'Il progetto', 'Il disegno definitivo, con il preventivo dell’impresa.')])]},

    'studio-quiete': {
        'percorsi': [
            ('schede', 'I percorsi', 'Tre modi', 'di cominciare.', [
                ('01', '', 'Individuale', '', 'Un’ora a settimana, per tutto il tempo che serve.', 'user'),
                ('02', '', 'Di coppia', '', 'Per ritrovare le parole quando si sono perse.', 'users'),
                ('03', '', 'Online', '', 'Le stesse sedute in videochiamata, da una stanza tranquilla.', 'monitor')]),
            ('faq', 'Prima di cominciare', 'Le domande', 'di chi esita.', [
                ('Come capisco se è il momento?', 'Se ci state pensando, il primo incontro serve proprio a capirlo.'),
                ('Quanto dura un percorso?', 'Dipende: alcuni mesi per un problema preciso, di più per un cambiamento profondo.'),
                ('È tutto riservato?', 'Sì, sempre: lo prevede il codice deontologico.')])],
        'contatti': [
            ('frase', 'Il primo passo è il più difficile, e lo avete già fatto arrivando fin qui.', 'Studio Quiete'),
            ('passi', 'Il primo incontro', 'Come funziona', 'il primo colloquio.', [
                ('01', 'Il messaggio', 'Due righe, o una telefonata: rispondiamo entro un giorno.'),
                ('02', 'L’incontro', 'Cinquanta minuti per raccontare e conoscersi.'),
                ('03', 'La decisione', 'Si decide insieme se e come continuare.')])]},

    'synthetica': {
        'contatti': [
            ('schede', 'Dopo il primo messaggio', 'Cosa succede', 'nelle prime due settimane.', [
                ('01', '', 'La chiamata', '', 'Mezz’ora per capire quali documenti e quali domande avete.', 'phone'),
                ('02', '', 'La prova', '', 'Una settimana con i vostri documenti veri, in un ambiente separato.', 'flask-conical'),
                ('03', '', 'Il preventivo', '', 'Il piano giusto per le persone che lo useranno.', 'file-text')]),
            ('frase', 'I vostri documenti restano vostri: non li usiamo per allenare nessun modello.', 'Synthetica')]},

    'tacet': {
        'opere': [
            ('frase', 'Una scultura si guarda girandoci attorno: per questo la galleria si visita su appuntamento, con il tempo che serve.',
             'La galleria Tacet'),
            ('faq', 'Acquistare un’opera', 'Le domande', 'dei collezionisti.', [
                ('Le opere hanno il certificato?', 'Sì, firmato dall’artista, con la scheda dei materiali.'),
                ('Spedite all’estero?', 'Sì, con un trasportatore d’arte e l’assicurazione.'),
                ('Si può pagare a rate?', 'Per le opere sopra una certa cifra, sì: chiedete.')])],
        'visione': [
            ('passi', 'La visione privata', 'Come si svolge', 'la visita.', [
                ('01', 'La richiesta', 'Ci scrivete le opere che volete vedere e un giorno.'),
                ('02', 'La visita', 'Un’ora in galleria, solo per voi, con l’opera illuminata come a casa.'),
                ('03', 'La prova', 'Per alcune opere, una settimana a casa vostra prima di decidere.')])]},

    'teatro-darsena': {
        'stagione': [
            ('righe', 'In scena', 'I prossimi', 'spettacoli.', [
                ('Nov', 'Amleto', 'Prosa', 'sala grande'),
                ('Dic', 'Lo schiaccianoci', 'Danza', 'sala grande'),
                ('Gen', 'La locandiera', 'Prosa', 'sala grande'),
                ('Feb', 'Concerto al porto', 'Musica', 'foyer')], '@biglietti'),
            ('frase', 'Il sipario si alza a pochi metri dal mare: a volte, nei silenzi, si sentono le navi.', 'Teatro Darsena')],
        'biglietti': [
            ('schede', 'I biglietti', 'Tre modi', 'per entrare.', [
                ('01', '', 'Online', '', 'Si sceglie il posto sulla pianta della sala e si stampa il biglietto.', 'ticket'),
                ('02', '', 'In biglietteria', '', 'Al molo, dal martedì al sabato.', 'map-pin'),
                ('03', '', 'Abbonamento', '', 'Sei spettacoli a scelta, con il posto fisso.', 'star')])]},

    'tostatura-nove': {
        'caffe': [
            ('carte', 'Il caffè', 'Dal sacco', 'alla tazza.', [
                ('pexels-12293641.jpg', 'Il verde', 'In magazzino', 'Sacchi di caffè verde da piccole fattorie: ogni lotto ha il suo nome.'),
                ('pexels-4820811.jpg', 'La tostatura', 'Il giovedì', 'Piccoli lotti, tostati a mano: il colore lo decide l’orecchio.'),
                ('pexels-11494420.jpg', 'Il filtro', 'Al bancone', 'Le origini più delicate, preparate a mano per sentirle bene.'),
                ('pexels-5151354.jpg', 'Il latte', 'Per la colazione', 'Le miscele per il cappuccino, con la foglia disegnata.')])],
        'corsi': [
            ('schede', 'I corsi', 'Tre corsi', 'per chi ama il caffè.', [
                ('01', '', 'Moka e filtro', '', 'Come prepararlo bene a casa, con quello che hai.', 'coffee'),
                ('02', '', 'Assaggio', '', 'Riconoscere le origini e le tostature, una tazza alla volta.', 'sparkles'),
                ('03', '', 'Barista', '', 'Macchina, macinatura e latte: due sabati da dietro il bancone.', 'award')])],
        'contatti': [
            ('frase', 'Un caffè buono non ha bisogno di zucchero: ha bisogno di essere fresco.', 'Tostatura Nove')]},

    'vinile-club': {
        'eventi': [
            ('carte', 'In negozio', 'Le sere', 'del club.', [
                ('pexels-9789246.jpg', 'Dj set', 'Il venerdì', 'Un dj diverso ogni settimana, solo vinili.'),
                ('pexels-8860130.jpg', 'Il mercatino', 'Una domenica al mese', 'Dischi usati da scambiare e comprare.'),
                ('pexels-6862365.jpg', 'Ascolto', 'Il mercoledì', 'Un album appena uscito, ascoltato tutto, in cuffia o ad alto volume.'),
                ('pexels-26315454.jpg', 'Le riparazioni', 'Il sabato', 'Giradischi e puntine da sistemare, al bancone.')])]},

    'volo-leggero': {
        'voli': [
            ('carte', 'Il volo', 'Una mattina', 'in cielo.', [
                ('pexels-13474921.jpg', 'Il gonfiaggio', 'Prima dell’alba', 'Il pallone si gonfia sul prato: si aiuta a tenerlo, se si vuole.'),
                ('pexels-99551.jpg', 'Il decollo', 'All’alba', 'Si sale piano, senza scosse: la terra si allontana in silenzio.'),
                ('pexels-8067732.jpg', 'In volo', 'Un’ora', 'Sopra la valle, con la nebbia che si scioglie sotto la cesta.'),
                ('pexels-5893022.jpg', 'L’atterraggio', 'Il brindisi', 'Si atterra in un campo e si brinda come vuole la tradizione.')])],
        'contatti': [
            ('frase', 'Lassù non si sente il vento, perché si va con lui. È la cosa che stupisce di più chi vola la prima volta.',
             'Il pilota di Volo Leggero')]},
}
