# -*- coding: utf-8 -*-
"""I passaggi fra le sezioni nei temi che non ne avevano (21 nov 2026, utente: «inserisci in almeno la metà dei siti fatti
delle transizioni creative di sezioni, specialmente nelle più vecchie»).

Per ogni tema: il passaggio scelto sul suo carattere (la pagina che si sfoglia per chi vive di carta, i canali per radio,
TV e giochi, l'ascensore o il sipario con la foto per teatri, palazzi e porte, i prismi per cartelloni e numeri, il
diaframma per foto e sguardo, il mazzo per carte e campioni, lo strappo per manifesti e cantieri, il rullo per inchiostri,
stoffe e impasti, la ruota per viaggi e cose che girano, il percorso a tappe per chi va avanti) e le sezioni della home
(indici da 0, consecutive) misurate abbastanza basse da stare in uno schermo a 1440 px: una sezione più alta il motore la
lascerebbe comunque in verticale (dalla 1.4.1326 a ogni larghezza), mai tagliata.

Si rilancia senza danni: prima toglie dalle sezioni della home le chiavi sticky, poi rimette quelle scelte qui.
⚠️ Rigenerando un tema con il suo gen_<id>.py i passaggi spariscono: rilanciare questo script.
Uso: python scripts/temi/passaggi_temi.py [tema ...]   (senza nomi: tutti quelli in SCELTE)
"""
import json
import os
import sys

D = os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', '..', 'assets', 'data', 'themes')
CHIAVI = ('sticky_effect', 'sticky_h_dir', 'sticky_h_align', 'sticky_colore', 'sticky_etichetta', 'sticky_ruota_gradi',
          'sticky_ante', 'sticky_ante_foto', 'sticky_ante_video')
LIB = '{{OLOBUILD_LIBRARY}}/media/'

# tema: (passaggio, [indici delle sezioni della home])
SCELTE = {
    'acqua-viva': ('percorso', [4, 5, 6]),
    'cantiere-aperto': ('strappo', [2, 3]),
    'cantina-vallerose': ('pagina', [3, 4, 5]),
    'casa-scogliera': ('ruota', [5, 6]),
    'circo-pappagallo': ('sipario', [3, 4, 5]),
    'codice-ragazzi': ('canali', [1, 2]),
    'conto-chiaro': ('prismi', [1, 2, 3]),
    'cromo': ('prismi', [6, 7]),
    'denim-district': ('strappo', [5, 6]),
    'fenice-calcio': ('mazzo', [3, 4]),
    'ferraris-avvocati': ('pagina', [3, 4, 5]),
    'fuoco-studio': ('diaframma', [5, 6, 7]),
    'giardino-di-elsa': ('percorso', [2, 3, 4]),
    'giulia-marchi': ('diaframma', [6, 7]),
    'glifo': ('rullo', [3, 4, 5]),
    'il-gufo': ('pagina', [2, 3]),
    'il-pozzo': ('ascensore-giu', [4, 5]),
    'inchiostro-riso': ('rullo', [6, 7]),
    'lumen-analytics': ('prismi', [7, 8, 9]),
    'maison-blanche': ('sipario', [5, 6]),
    'mare-pulito': ('ruota', [1, 2]),
    'margini': ('pagina', [4, 5]),
    'mezza-delle-mura': ('mazzo', [4, 5, 6]),
    'mezzanotte': ('canali', [2, 3, 4]),
    'moneta': ('prismi', [2, 3, 4]),
    'nido-coworking': ('ascensore', [6, 7]),
    'nutrire': ('mazzo', [1, 2, 3]),
    'oro-fino': ('diaframma', [2, 3]),
    'orto-vivo': ('percorso', [2, 3, 4]),
    'osteria-fumo': ('pagina', [8, 9, 10]),
    'parla-mondo': ('mazzo', [3, 4, 5]),
    'passo-lento': ('ruota', [6, 7, 8]),
    'pixel-club': ('canali', [2, 3]),
    'rewind-vintage': ('canali', [5, 6]),
    'rifugio-larice': ('percorso-su', [1, 2]),
    'riga-di-comando': ('prismi', [2, 3, 4]),
    'ruggine': ('strappo', [1, 2, 3]),
    'sali-argento': ('diaframma', [2, 3]),
    'sartoria-ferro': ('mazzo', [5, 6, 7]),
    'scudo': ('ascensore', [2, 3, 4]),
    'solco-agritech': ('percorso', [5, 6]),
    'sorriso-clinic': ('diaframma', [6, 7]),
    'studio-quiete': ('pagina', [6, 7]),
    'synthetica': ('canali', [8, 9, 10]),
    'teatro-darsena': ('sipario', [3, 4, 5]),
    'tostatura-nove': ('rullo', [4, 5, 6]),
    'vinile-club': ('ruota', [1, 2, 3]),
    'volo-leggero': ('percorso-su', [1, 2, 3]),
    'kemet': ('pagina', [1, 2]),
    'teatro-seta': ('sipario-video', [7, 8]),
    'officine-quota': ('ascensore', [4, 5, 6]),
    'fonderia-mella': ('strappo', [4, 5]),
    'settimo-asse': ('prismi', [3, 4, 5]),
    'lamiere-astico': ('prismi', [2, 3, 4]),
    'marmi-lizza': ('diaframma', [4, 5]),
    'tessiture-navetta': ('rullo', [3, 4, 5]),
    'varco-nove': ('ascensore', [4, 5, 6]),
    'doppia-mandata': ('canali', [8, 9]),
    'leva-consulenza': ('pagina', [1, 2, 3]),
    'scheda-madre': ('canali', [3, 4]),
    'carico-libero': ('mazzo', [9, 10]),
    'baita-stua': ('percorso-su', [7, 8]),
    'bagnasciuga': ('ruota', [8, 9]),
    'raggiera': ('diaframma', [4, 5, 6]),
    'undici-ulivi': ('pagina', [6, 7]),
    'ora-blu': ('mazzo', [5, 6, 7]),
    'scia': ('percorso', [6, 7, 8]),
    'fulmine': ('strappo', [6, 7, 8]),
    'tacet': ('canali', [7, 8]),
    'tratteggio': ('rullo', [5, 6]),
    'cielo-buio': ('diaframma', [6, 7, 8]),
    'orlo': ('mazzo', [9, 10]),
    'siero': ('diaframma', [5, 6]),
    'madia': ('pagina', [8, 9]),
    'crocevia': ('percorso', [3, 4, 5]),
    'tracolla': ('mazzo', [8, 9, 10]),
    'velluto': ('sipario-video', [9, 10]),
    'suola': ('percorso', [9, 10]),
    'ristretto': ('rullo', [9, 10]),
    'carambola': ('mazzo', [8, 9, 10]),
    'spiedo': ('strappo', [5, 6]),
    'pennino': ('pagina', [3, 4]),
    'falda': ('percorso-su', [1, 2]),
    'fuliggine': ('strappo', [5, 6]),  # 27 nov 2026: la citazione si strappa come carta sulle testimonianze
    'trottola': ('ruota', [8, 9, 10]),
    'calice': ('diaframma', [9, 10]),
    'rovescio': ('pagina', [5, 6]),
    'cerniera': ('ascensore', [6, 7]),
    'sonnambulo': ('canali', [3, 4, 5]),
    'gomitolo': ('rullo', [7, 8]),
    'raspa': ('strappo', [6, 7, 8]),
    'stender': ('mazzo', [6, 7, 8]),
    'fuori-registro': ('rullo', [8, 9, 10]),
    'luce-rossa': ('diaframma', [6, 7, 8]),
    'lastra': ('prismi', [6, 7]),
    'sbieco': ('strappo', [5, 6, 7]),
    'falcata': ('percorso', [1, 2, 3]),
    'combinazione': ('ascensore', [7, 8]),
    'ventaglio': ('ruota', [6, 7]),  # la 5 ha i panni stesi (Pro Slider fermo allo scorrimento): fuori dal gruppo
    'elastan': ('mazzo', [7, 8, 9]),
    'morsetto': ('prismi', [5, 6, 7]),
    'truciolo': ('strappo', [1, 2, 3]),
    'persiana': ('prismi', [1, 2]),
    'vetrino': ('diaframma', [4, 5, 6]),
    'doppio-senso': ('pagina', [7, 8]),
}

# Altri passaggi della home oltre al gruppo (27 nov 2026, utente: «usa almeno una transizione da sotto di sezione, quella
# che scorrendo si alza come provenisse da dietro quella che si sposta»): tema → {indice: chiavi della sezione}.
# «Reveal»: la sezione aspetta ferma sotto la precedente, che si alza e la scopre. La precedente deve avere uno sfondo
# pieno, se no la sezione che aspetta si vede attraverso fin dall'inizio; l'ombra (Stile) la stacca da quella sotto.
ALTRI = {
    'ventaglio': {1: {'bg': {'type': 'solid', 'color': 'var(--olo-color-background)'}, 'bg_scope': 'section',
                      'style': {'shadow': 'lg'}},
                  2: {'sticky_effect': 'reveal'}},
}

# il sipario: la foto (e il video) divisi fra le due ante
SIPARIO = {
    'circo-pappagallo': ('pexels-17944662.jpg', ''),
    'maison-blanche': ('pexels-8459374.jpg', ''),
    'teatro-darsena': ('pexels-18501410.jpg', ''),
    'teatro-seta': ('pexels-video-5124245-copertina.jpg', 'pexels-video-5124245.mp4'),
    'velluto': ('pexels-video-7677239.jpg', 'pexels-video-7677239.mp4'),
}


def scuro(tema):
    """Il tema ha lo sfondo scuro? (luminanza del colore «background» della sua palette sotto 0,2)"""
    try:
        c = json.load(open(os.path.join(D, tema, 'theme.json'), encoding='utf-8'))['styles']['colors']['background']
        r, g, b = (int(c.lstrip('#')[i:i + 2], 16) / 255 for i in (0, 2, 4))
    except (OSError, KeyError, ValueError):
        return False
    lin = [x / 12.92 if x <= 0.03928 else ((x + 0.055) / 1.055) ** 2.4 for x in (r, g, b)]
    return 0.2126 * lin[0] + 0.7152 * lin[1] + 0.0722 * lin[2] < 0.2


def pari(tema):
    return sum(map(ord, tema)) % 2 == 0


def impostazioni(tipo, k, tema):
    """Le chiavi della sezione k (0 = la prima del gruppo) per il passaggio scelto."""
    s = {'sticky_h_align': 'center'}
    alterna = 'destra' if pari(tema) else 'sotto'
    if tipo == 'pagina':
        s.update(sticky_effect='pagina', sticky_h_dir=alterna, sticky_colore='var(--olo-color-background)')
    elif tipo == 'canali':
        s.update(sticky_effect='canali', sticky_colore='#7CFF6B')
    elif tipo in ('ascensore', 'ascensore-giu', 'sipario', 'sipario-video'):
        s.update(sticky_effect='ascensore', sticky_h_dir='sotto' if tipo == 'ascensore-giu' else 'sopra',
                 sticky_colore='var(--olo-color-secondary)')
        if tipo.startswith('sipario') and k == 0 and tema in SIPARIO:
            foto, video = SIPARIO[tema]
            s.update(sticky_ante='video' if video else 'foto', sticky_ante_foto=LIB + foto)
            if video:
                s['sticky_ante_video'] = LIB + video
    elif tipo == 'prismi':
        s.update(sticky_effect='prismi', sticky_h_dir='destra' if pari(tema) else 'sinistra')
    elif tipo == 'diaframma':
        # sui temi scuri lamelle scure su fondo scuro = schermo nero a metà passaggio: lì il colore principale del tema
        s.update(sticky_effect='diaframma', sticky_colore='var(--olo-color-primary)' if scuro(tema) else 'var(--olo-color-dark)')
    elif tipo == 'mazzo':
        s.update(sticky_effect='mazzo', sticky_h_dir=alterna)
    elif tipo == 'strappo':
        # il retro del manifesto: sui temi scuri chiaro, come la carta vera (scuro su scuro lo strappo non si vedeva)
        s.update(sticky_effect='strappo', sticky_h_dir=alterna,
                 sticky_colore='var(--olo-color-light)' if scuro(tema) else 'var(--olo-color-muted)')
    elif tipo == 'rullo':
        s.update(sticky_effect='rullo', sticky_h_dir=alterna, sticky_colore='var(--olo-color-primary)')
    elif tipo == 'ruota':
        s.update(sticky_effect='ruota', sticky_ruota_gradi=22, sticky_h_dir='destra' if pari(tema) else 'sinistra')
    elif tipo in ('percorso', 'percorso-su'):
        giro = ['sotto', 'destra'] if tipo == 'percorso' else ['sopra', 'destra']
        s.update(sticky_effect='cover-h', sticky_h_dir=giro[(k - 1) % 2] if k else 'destra')
    else:
        raise ValueError(tipo)
    return s


def applica(tema):
    tipo, indici = SCELTE[tema]
    f = os.path.join(D, tema, 'home.json')
    testo = open(f, encoding='utf-8').read()
    home = json.loads(testo)
    sez = home if isinstance(home, list) else home['content']
    for s in sez:
        st = s.setdefault('settings', {})
        for c in CHIAVI:
            st.pop(c, None)
    for k, i in enumerate(indici):
        assert sez[i].get('type') == 'section', (tema, i)
        sez[i]['settings'].update(impostazioni(tipo, k, tema))
    for i, chiavi in ALTRI.get(tema, {}).items():
        assert sez[i].get('type') == 'section', (tema, i)
        chiavi = dict(chiavi)
        sez[i].setdefault('style', {}).update(chiavi.pop('style', {}))  # l'ombra sotto la sezione che si alza: Stile
        sez[i]['settings'].update(chiavi)
    nuovo = json.dumps(home, ensure_ascii=False, separators=(',', ':'))
    if nuovo != testo:
        open(f, 'w', encoding='utf-8', newline='').write(nuovo)
        return True
    return False


if __name__ == '__main__':
    temi = sys.argv[1:] or list(SCELTE)
    cambiati = [t for t in temi if applica(t)]
    print(len(cambiati), 'temi cambiati su', len(temi))
