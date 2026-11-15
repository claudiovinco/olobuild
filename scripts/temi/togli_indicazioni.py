# -*- coding: utf-8 -*-
"""Toglie dai temi le frasi che spiegano l'effetto prima che si veda («Scorrete: il ventaglio si apre…», «Gira qui ↓»,
«Passate su una pianta: la foto si affaccia»). L'utente, 13 nov 2026: «nei temi hai messo indicazioni ovunque, anche
troppe, togli la sorpresa a chi visita, eliminale».

Dove: i testi editoriali (occhielli e sottotitoli dei Section header, Text block, i livelli di testo dello Slider Pro,
sottotitolo e invito a scorrere della Hero, sottotitolo della barra del titolo, didascalie delle Carte da girare, piede
delle tappe). Restano i suggerimenti DENTRO le tile (la lente, il gratta e scopri, il tour, la ghiera): servono a usarle.
Una frase d'istruzione si toglie intera; se sta dopo i due punti si toglie solo quella parte («Un soggiorno arredato
quest'autunno: toccate i numeri.» → «Un soggiorno arredato quest'autunno.»). Un testo che resta vuoto: il livello o la
tile si tolgono, l'occhiello si spegne.

uso: python togli_indicazioni.py [--scrivi] [tema …]   (senza --scrivi stampa soltanto)"""
import json
import os
import re
import sys

sys.stdout.reconfigure(encoding='utf-8')
TEMI = os.path.normpath(os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', '..', 'assets', 'data', 'themes'))
VERBI = (r'Scorrete|Scorri|Scendete|Scendi|Gira qui|Girate|Gira la|Gira le|Tira su|Tirate|Passate|Passateci|Passa sopra|'
         r'Passa su|Passa il|Trascinate|Trascina|Toccate|Toccatel[oa]|Tocca|Cliccate|Clicca|Muovete|Muovi|Grattate|Gratta con|'
         r'Premete|premete|Clic per|Sul telefono basta|Le frecce, o un dito')
# una frase d'istruzione: comincia (dopo l'inizio, un tag, un punto o i due punti) con uno dei verbi e arriva al punto
FRASE = re.compile(r'(?P<prima>^|(?<=>)|(?<=[.!?»])\s+|\s*:\s+)(?P<frase>(?:' + VERBI + r')\b[^.!?<↓→]*(?:[.!?↓→]+|(?=<)|$))',
                   re.I)
CAMPI = {('section-header', 'tagline_text'), ('section-header', 'text'), ('text', 'content'), ('text-block', 'content'),
         ('hero', 'subtitle'), ('hero', 'scroll_hint'), ('pagetitlebar', 'subtitle'), ('flipcard', 'front_description'),
         ('step-timeline', 'footer_label'), ('scrollscrub', 'kicker'), ('iconlist', 'text'), ('projector', 'intro')}


SOLO_ISTRUZIONE = re.compile(r'(^|(?<=>)|(?<=[.!?])\s+)(?:Trascinate|Trascina)\s*,?\s*o lasciate scorrere\s*:\s*(\w)')


def pulisci(v):
    # «Trascinate o lasciate scorrere: sono i lavori di questo mese.» → «Sono i lavori di questo mese.»
    v2 = SOLO_ISTRUZIONE.sub(lambda m: m.group(1) + m.group(2).upper(), v)
    if v2 != v:
        return pulisci(v2) if FRASE.search(v2) else v2
    if not FRASE.search(v):
        return v  # niente da togliere: il testo resta com'è, spazi compresi (le coordinate della scacchiera, «REC  00:00»)

    def via(m):
        # dopo i due punti: resta la frase di prima, chiusa col punto
        return '.' if ':' in m.group('prima') else ('' if m.group('prima').strip() == '' and m.start() == 0 else
                                                     (' ' if m.group('prima').strip() == '' and m.group('prima') else ''))
    n = FRASE.sub(via, v)
    n = re.sub(r'\s+([.,;])', r'\1', n)
    n = re.sub(r'\.\.+', '.', n)
    n = re.sub(r'(<p>)\s+', r'\1', n)
    n = re.sub(r'\s+(</p>)', r'\1', n)
    n = re.sub(r'<p>\s*</p>', '', n)
    n = re.sub(r'  +', ' ', n).strip()
    if re.match(r'^(<br\s*/?>)', n):
        return ''  # l'istruzione andava a capo («Scorrete:<br>si apre il sipario»): via tutto
    return n


def vuoto(v):
    return not re.sub(r'<[^>]+>|&nbsp;|\s', '', v or '')


def tratta(nodo, cambi, togli):
    if isinstance(nodo, list):
        tieni = []
        for x in nodo:
            if isinstance(x, dict) and togli(x):
                cambi.append(('via', x.get('type') or 'livello', (x.get('content') or x.get('settings', {}).get('content') or '')[:80]))
                continue
            tratta(x, cambi, togli)
            tieni.append(x)
        nodo[:] = tieni
    elif isinstance(nodo, dict):
        for v in nodo.values():
            tratta(v, cambi, togli)


def applica(j, cambi, tid=None):
    def togli(x):
        tipo = x.get('type')
        # livelli di testo dello Slider Pro (hanno «content» e niente settings)
        if tipo == 'text' and 'settings' not in x and isinstance(x.get('content'), str):
            n = pulisci(x['content'])
            if n != x['content']:
                if vuoto(n):
                    return True
                cambi.append(('testo', 'livello', x['content'][:70] + ' → ' + n[:70]))
                x['content'] = n
            return False
        s = x.get('settings')
        if not isinstance(s, dict) or not tipo:
            return False
        for (t, k) in CAMPI:
            if t == tipo and isinstance(s.get(k), str) and s[k]:
                n = pulisci(s[k])
                if n != s[k]:
                    cambi.append((k, tipo, s[k][:70] + ' → ' + (n[:70] or '∅')))
                    s[k] = n
                    if vuoto(n):
                        if tipo == 'text-block':
                            return True
                        if k == 'tagline_text' and 'tagline_show' in s:
                            s['tagline_show'] = False
        return False
    tratta(j, cambi, togli)
    titoli(j, cambi, tid)


# Titoli di sezione (Section header, righe del titolo) che spiegavano il gesto: riscritti per dire COSA si vede.
# Restano gli inviti a passare in negozio o in bottega (Clorofilla, Concia: contatti, non un effetto).
TITOLI = {
    'Scorrete:|si mettono da soli.': ['Strato su strato,', 'si mettono da soli.'],
    'Tocca un piatto,|scopri chi è.': ['Ogni piatto', 'ha la sua mano.'],
    'Tocca,|e si apre.': ['Una porta', 'che si apre.'],
    'Tocca un punto,|entri in sala.': ['Dentro', 'la sala.'],
    'Girate le piastrelle:|dietro il prezzo.': ['Le piastrelle', 'e il loro prezzo.'],
    'Trascinate:|è lo stesso capo.': ['Aperta o chiusa,', 'resta lei.'],
    'Girate le carte:|dietro ci sono i numeri.': ['Le case', 'e i loro numeri.'],
    'Tocca le stelle|che vedi a occhio nudo.': ['Le stelle', 'che vedi a occhio nudo.'],
    'Girate le schede:|dietro c’è il carattere.': ['Un nome,', 'un carattere.'],
    'Tocca|il punto.': ['Il punto', 'giusto.'],
    'Trascinate|la riga.': ['La stessa parete,', 'prima e dopo.'],
    'Trascinate:|è lo stesso tessuto.': ['Teso o a riposo,', 'è lo stesso tessuto.'],
    'Passate sopra:|la forma diventa scarpa.': ['Dalla forma', 'alla scarpa.'],
    'Trascina,|e guarda la differenza.': ['La differenza', 'si vede.'],
    'Trascinate:|prima e dopo il registro.': ['Prima e dopo', 'il registro.'],
    'Trascina la foto:|sei già dentro.': ['Una foto', 'dove sei già dentro.'],
    'Trascina|la maniglia.': ['Com’era,', 'com’è.'],
    'Trascina|sulle lettere.': ['Lettere', 'che rispondono.'],
    'Toccate i punti,|vi diciamo come si fa.': ['Ogni punto,', 'un passaggio del lavoro.'],
    'Toccate|i punti.': ['Dentro', 'la giacca.'],
    'Girate|la ghiera.': ['La ghiera', 'e la sua luce.'],
    'Girate le carte:|dietro i numeri.': ['Le ville', 'e i loro numeri.'],
    'Trascinate la linea:|dal grezzo al lucido.': ['Dal grezzo', 'al lucido.'],
    'Trascinate:|si vede la differenza.': ['Com’era,', 'e com’è adesso.'],
    'Passateci sopra:|dietro c’è il come.': ['Dietro ogni cosa,', 'c’è il come.'],
    'Grattate il crudo:|sotto c’è il lavato.': ['Il crudo,', 'e sotto il lavato.'],
    'Trascinate:|è lo stesso paio.': ['Crudo o lavato,', 'è lo stesso paio.'],
    'Trascinate il filo:|è lo stesso capo.': ['Dritto e rovescio,', 'lo stesso capo.'],
    'Trascinate:|dal modello all’abito.': ['Dal modello', 'all’abito.'],
    'Scorrete:|si rimette insieme.': ['Pezzo per pezzo,', 'si rimette insieme.'],
    'Trascinate:|il vetro nuovo.': ['Il vetro rotto,', 'il vetro nuovo.'],
    'Tocca un punto,|ti diciamo cosa fa.': ['Ogni punto', 'ha il suo compito.'],
    'Gira la ghiera.|Scegli cosa ti serve.': ['Una ghiera,', 'quello che ti serve.'],
    'Girate la carta:|dietro c’è il compagno.': ['Ogni calzino', 'ha il suo compagno.'],
    'Passa sopra,|si apre la scena.': ['Si apre', 'la scena.'],
    'Passate la lente|sulla trama.': ['Da vicino,', 'la trama.'],
    'Trascinate:|non si vede la differenza.': ['Il primo lavaggio', 'e il centesimo.'],
    # «da girare», «girateli», «grattate» sopra le carte che si girano e il gratta e scopri
    'Quattro bicchieri|da girare.': ['Ogni bicchiere', 'ha il suo gin.'],
    'Quattro colpi|da girare.': ['Dietro ogni colpo,', 'un segreto.'],
    'Girateli:|dietro c’è il come.': ['Davanti il cosa,', 'dietro il come.'],
    'Sei pizze|da girare.': ['Ogni pizza', 'ha la sua storia.'],
    'Otto montature,|da girare.': ['Una montatura', 'per ogni viso.'],
    'Prima di decidere,|grattate.': ['Prima di decidere,', 'il colore vero.'],
    'Quattro attività|da girare.': ['Le attività', 'della giornata.'],
    'Sei tagli|da girare.': ['Ogni taglio', 'ha la sua testa.'],
    'Quattro capi|da girare.': ['Ogni capo', 'si porta in due modi.'],
    'Quattro dei sei,|girateli.': ['Visti', 'da vicino.'],
    # col «tu»: «Gratta», «Gira», «Sfoglia» (seconda ricerca, 13 nov 2026)
    'Gratta|e scopri.': ['Sotto la carta,', 'il flash del giorno.'],
    'Sfoglia|l’anno.': ['Un anno', 'di menu.'],
    'Gratta,|è per te.': ['Un regalo', 'per te.'],
    'Gira il cubo,|cambia epoca.': ['Ogni faccia,', 'un’epoca.'],
    'Gratta|la nebbia.': ['Dietro', 'la nebbia.'],
    'Gira,|e annusa con gli occhi.': ['Annusare', 'con gli occhi.'],
    'Gratta|la tazzina.': ['Una tazzina', 'a sorpresa.'],
    'Scegli il tuo look,|poi trascina.': ['Lo stesso scatto,', 'con un altro look.'],
    'Quattro gesti|da girare.':['I gesti', 'della parete.'],
}


# Lo stesso titolo copiato in tanti temi: uno per tema, legato alla sua trovata (13 nov 2026: «Nei negozi che lo
# vendono.» era identico nella pagina dei negozi di tutti i 20 temi di moda).
TITOLI_TEMA = {
    'Nei negozi che lo vendono.': {
        'a-cipolla': 'Gli strati, da provare in negozio.', 'cangiante': 'Il colore che cambia, dal vivo.',
        'cerniera': 'La prima zip si apre qui.', 'combinazione': 'Le valigette, da aprire in negozio.',
        'elastan': 'Da provare in movimento.', 'falcata': 'Il primo passo, in negozio.',
        'fuori-registro': 'Le stampe, viste da vicino.', 'fuorimisura': 'Una taglia sola, in questi negozi.',
        'gomitolo': 'La lana, da toccare.', 'lastra': 'Le giacche, aperte dal vivo.',
        'luce-rossa': 'La seta, da annodare in negozio.', 'raspa': 'Il lavato, alla luce del giorno.',
        'rovescio': 'Al dritto e al rovescio, in negozio.', 'sbieco': 'Il taglio, da provare addosso.',
        'scroscio': 'Al riparo, in questi negozi.', 'sonnambulo': 'Da provare, anche a occhi chiusi.',
        'spaiato': 'Il compagno si trova qui.', 'stender': 'Appesi, in questi negozi.',
        'trentagradi': 'Le basi, da toccare con mano.', 'ventaglio': 'Il ventaglio si apre in negozio.',
    },
}


# Le code dopo un puntino che dicono il gesto (14 nov 2026, seconda ricerca: anche a metà frase): sul fronte delle carte
# che si girano («La ricetta · girate la carta»), negli slider dei negozi («Il passo · scorrete»), sui punti da toccare.
CODA = re.compile(r'\s*·\s*(?:girate la carta|scorrete|continua a scorrere\s*↓?|tocca i punti|tocca un dolce)\s*$', re.I)
# Frasi che spiegavano l'effetto, riscritte (pezzo vecchio → pezzo nuovo, dentro qualunque testo)
FRASI = {
    'La modella cammina solo se scorrete. Risalite, e torna indietro.': 'Tacco, forma e cuoio pensati per camminare, non solo per sfilare.',
    'Come il filo di questo sito: arriva quando scorrete, torna nel gomitolo quando ve ne andate.': 'Un filo solo, dal gomitolo alla maglia.',
    'Scorrete: ogni strato copre quello di prima': 'Ogni strato copre quello di prima',
    'Toccate i numeri': 'Le misure, una per una',
    'Scegliete il rullino e trascinate il divisore: a sinistra lo scatto, a destra lo sviluppo.': 'Rullino per rullino: a sinistra lo scatto, a destra lo sviluppo.',
    'Le carte si impilano mentre scorri.': 'Una sull’altra, come sul tavolo dell’atelier.',
    'Le card si impilano mentre scorrete.': 'Una sull’altra, passaggio dopo passaggio.',
    'Gratta la card con il dito o con il mouse: il codice lo usi quando apri il conto.': 'Sotto la card c’è un codice: lo usi quando apri il conto.',
    'Scegli una finitura e passa sopra la foto: con la lente vedi la grana, le velature, il segno della spatola.': 'Da vicino si vedono la grana, le velature, il segno della spatola.',
    'Passa o tocca un settore per vedere la vista dal posto.': 'Per ogni settore, la vista dal posto.',
    'passa o tocca un settore della sala per vedere i prezzi e la vista da lì.': 'per ogni settore della sala i prezzi e la vista da lì.',
    'Ogni maniglia si trascina: a sinistra': 'A sinistra',
}
# testi interi (un livello dello Slider Pro con una parola sola): solo se il campo è esattamente questo
INTERI = {'scorrendo.': 'al buio.'}
CAMPI_TESTO = ('content', 'text', 'subtitle', 'tagline_text', 'heading', 'front_description', 'description')


def frasi(j, cambi):
    def w(n):
        if isinstance(n, dict):
            for k, v in list(n.items()):
                if k in CAMPI_TESTO and isinstance(v, str) and v:
                    nuovo = v.replace('\xa0', ' ') if any(a in v.replace('\xa0', ' ') for a in FRASI) else v
                    for a, b in FRASI.items():
                        nuovo = nuovo.replace(a, b)
                    if k in ('content', 'front_description') and CODA.search(re.sub(r'<[^>]+>', '', nuovo)):
                        nuovo = CODA.sub('', re.sub(r'</p>\s*$', '', nuovo)) + ('</p>' if nuovo.rstrip().endswith('</p>') else '')
                        nuovo = re.sub(r'^\s*·\s*$', '', nuovo)
                    if nuovo.strip() in INTERI:
                        nuovo = INTERI[nuovo.strip()]
                    if nuovo != v:
                        cambi.append(('frase', k, v[:60] + ' → ' + nuovo[:60]))
                        n[k] = nuovo
                elif isinstance(v, (dict, list)):
                    w(v)
        elif isinstance(n, list):
            for v in n:
                w(v)
    w(j)


# Voci di elenco (Icon list) che spiegavano il gesto accanto alla tile: testo intero vecchio → nuovo.
VOCI = {
    '<strong>Trascina</strong> · la vista gira tutt’intorno': '<strong>Tutt’intorno</strong> · la vista gira a 360 gradi',
    '<strong>Avvicinati</strong> · con due dita, o con Ctrl e la rotellina': '<strong>Da vicino</strong> · i dettagli, nitidi',
    '<strong>Tocca i cerchi corallo</strong> · misure e materiali': '<strong>I cerchi corallo</strong> · misure e materiali',
}


def voci(j, cambi):
    def w(n):
        if isinstance(n, dict):
            s = n.get('settings') if isinstance(n.get('settings'), dict) else {}
            if n.get('type') == 'iconlist':
                for it in s.get('items') or []:
                    chiave = str(it.get('text') or '').replace('\xa0', ' ') if isinstance(it, dict) else ''
                    if chiave in VOCI:
                        cambi.append(('voce', 'iconlist', chiave[:60] + ' → ' + VOCI[chiave][:60]))
                        it['text'] = VOCI[chiave]
            for v in n.values():
                if isinstance(v, (dict, list)):
                    w(v)
        elif isinstance(n, list):
            for v in n:
                w(v)
    w(j)


def titoli(j, cambi, tid=None):
    voci(j, cambi)
    frasi(j, cambi)
    def w(n):
        if isinstance(n, dict):
            hl = n.get('headline_lines')
            if isinstance(hl, list) and hl and all(isinstance(x, dict) for x in hl):
                # lo spazio fisso (\xa0, contro la parola sola in fondo alla riga) conta come uno spazio
                chiave = '|'.join(str(x.get('text') or '') for x in hl).replace('\xa0', ' ')
                nuovo = TITOLI.get(chiave)
                if not nuovo and tid and (TITOLI_TEMA.get(chiave) or {}).get(tid):
                    v = TITOLI_TEMA[chiave][tid]
                    nuovo = list(v) if isinstance(v, (list, tuple)) else [v]
                if not nuovo and tid:
                    # i titoli in serie «Quattro X, una Y.» (titoli_numerici.py, 14 nov 2026)
                    import titoli_numerici
                    nuovo = titoli_numerici.RISCRITTI.get(tid, {}).get('|'.join(x.strip() for x in chiave.split('|')))
                if nuovo and len(nuovo) == len(hl):
                    cambi.append(('titolo', 'section-header', ' '.join(x['text'] for x in hl) + ' → ' + ' '.join(nuovo)))
                    for x, t in zip(hl, nuovo):
                        x['text'] = t
            for v in n.values():
                if isinstance(v, (dict, list)):
                    w(v)
        elif isinstance(n, list):
            for v in n:
                w(v)
    w(j)


def main():
    scrivi = '--scrivi' in sys.argv
    temi = [a for a in sys.argv[1:] if not a.startswith('--')] or sorted(os.listdir(TEMI))
    tot = 0
    for t in temi:
        d = os.path.join(TEMI, t)
        for f in sorted(os.listdir(d)):
            if not f.endswith('.json') or f == 'theme.json':
                continue
            p = os.path.join(d, f)
            testo = open(p, encoding='utf-8').read()
            j = json.loads(testo)
            cambi = []
            applica(j, cambi, t)
            if cambi:
                tot += len(cambi)
                for c in cambi:
                    print('%s/%s · %s · %s · %s' % (t, f, c[1], c[0], c[2]))
                if scrivi:
                    compatto = not testo.lstrip().startswith('{\n') and not testo.lstrip().startswith('[\n')
                    open(p, 'w', encoding='utf-8', newline='\n').write(
                        json.dumps(j, ensure_ascii=False, separators=(',', ':')) if compatto else json.dumps(j, ensure_ascii=False, indent=1))
    print('modifiche:', tot, '(scritte)' if scrivi else '(prova: niente scritto)')


if __name__ == '__main__':
    main()
