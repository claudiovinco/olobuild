# -*- coding: utf-8 -*-
"""Le STRUTTURE del tema (13 nov 2026), dopo le linee grafiche. L'utente: «troppo comodo rifare tutto il sito uguale,
dovresti sforzarti per dare linee grafiche diverse tra loro, strutture creative». Il conteggio degli scheletri di sezione
di tutti i 198 temi diceva che alcune disposizioni erano dappertutto, sempre uguali:
  · la fascia video con la frase al centro (368 volte, 160 temi);
  · le domande frequenti col titolo a sinistra e la fisarmonica a destra (127 volte, 116 temi);
  · i contatti col testo a sinistra e il modulo a destra (118 volte, 104 temi);
  · l'indirizzo a sinistra e la mappa a destra (81 temi);
  · la fila di quattro foto uguali (53 volte, 49 temi);
  · l'apertura di sezione col titolo largo a sinistra e il testo stretto a destra (353 volte, 122 temi).
Qui ogni PROFILO di linee_grafiche.json (editoriale, morbida, geometrica, essenziale, rivista, tecnica) le dispone a modo
suo, sempre lo stesso dentro un tema (è la sua linea), diverso fra i temi. La fascia video sceglie centro, sinistra o
destra per ogni fascia (dal titolo), così anche dentro un tema non sono tutte uguali.
Solo spostamenti, larghezze e allineamenti: i testi, le foto e le chiavi dei dati restano quelli.
uso: python strutture.py [--scrivi] [tema …]   ·   base.write_theme la riapplica a ogni rigenerazione."""
import copy
import json
import os
import re
import sys
import zlib

QUI = os.path.dirname(os.path.abspath(__file__))
sys.path.insert(0, QUI)
TEMI = os.path.normpath(os.path.join(QUI, '..', '..', 'assets', 'data', 'themes'))
PROFILI = os.path.join(QUI, 'linee_grafiche.json')

FAQ = {'morbida': 'centro', 'geometrica': 'inverti', 'essenziale': 'sopra', 'rivista': 'inverti'}
CONTATTI = {'morbida': 'inverti', 'geometrica': 'inverti', 'tecnica': 'inverti'}
MAPPA = {'editoriale': 'inverti', 'essenziale': 'inverti', 'rivista': 'inverti'}
FOTO4 = {'morbida': 'sfalsata', 'geometrica': 'tre', 'essenziale': 'nastro', 'rivista': 'sfalsata', 'tecnica': 'quadrate'}
INTRO = {'morbida': 'centro', 'geometrica': 'stretta', 'essenziale': 'meta', 'rivista': 'larga'}
FASCE = ['centro', 'lato', 'destra']


def tipi(col):
    return [c.get('type') for c in col.get('children') or []]


def colonna(id_, larghezza, figli):
    return {'id': id_, 'type': 'column', 'settings': {'width_medium': larghezza, 'width_small': '1-1'}, 'style': {},
            'advanced': {}, 'children': figli}


def larghezze(riga, ws):
    """ws: frazioni per colonna ('1-3', '2-3'…), nello stesso ordine dei figli."""
    st = riga.setdefault('settings', {})
    st['layout'] = 'custom' if len(ws) > 1 else '100'
    if len(ws) > 1:
        # la somma deve fare 100 esatto: 66.68 + 33.33 = 100.01 mandava a capo la seconda colonna
        pc = [round(int(w.split('-')[0]) * 100 / int(w.split('-')[1]), 2) for w in ws]
        pc[-1] = round(100 - sum(pc[:-1]), 2)
        st['custom_widths'] = ','.join(('%g' % x) for x in pc)
    else:
        st.pop('custom_widths', None)
    for c, w in zip(riga['children'], ws):
        c.setdefault('settings', {})['width_medium'] = w


def riga_da(modello, id_, figli, ws, margine=0):
    r = {k: copy.deepcopy(v) for k, v in modello.items() if k != 'children'}
    r['id'] = id_
    r['children'] = figli
    larghezze(r, ws)
    if margine:
        r.setdefault('style', {})['margin_top'] = margine
    return r


def centrata(riga, figli_centro, id_):
    """Una riga 1-6 · 2-3 · 1-6 col contenuto al centro (come la fascia video)."""
    return riga_da(riga, id_, [colonna(id_ + '-a', '1-6', []), colonna(id_ + '-b', '2-3', figli_centro),
                               colonna(id_ + '-c', '1-6', [])], ['1-6', '2-3', '1-6'])


def una_riga(sez):
    rr = sez.get('children') or []
    return rr[0] if len(rr) == 1 and rr[0].get('type') == 'row' else None


def faq(sez, v):
    r = una_riga(sez)
    if not r or [tipi(c) for c in r['children']] != [['section-header'], ['accordion']]:
        return False
    sx, dx = r['children']
    sh = sx['children'][0]['settings']
    if v == 'inverti':
        r['children'] = [dx, sx]
        larghezze(r, ['2-3', '1-3'])
    elif v == 'centro':
        sh['layout'] = 'center'
        sez['children'] = [centrata(r, sx['children'], r['id'] + '-t'),
                           dict(centrata(r, dx['children'], r['id'] + '-f'), style={'margin_top': 40})]
    elif v == 'sopra':
        if (sh.get('subtitle') or sh.get('text') or '').strip():
            sh['layout'] = 'split'
        sez['children'] = [riga_da(r, r['id'] + '-t', [colonna(r['id'] + '-ta', '1-1', sx['children'])], ['1-1']),
                           riga_da(r, r['id'] + '-f', [colonna(r['id'] + '-fa', '1-1', dx['children'])], ['1-1'], 36)]
    return True


def contatti(sez, v):
    r = una_riga(sez)
    if not r or len(r['children']) != 2:
        return False
    a, b = (tipi(c) for c in r['children'])
    if not ('iconlist' in a and a[0] == 'section-header' and b == ['form']):
        return False
    if v == 'inverti':
        r['children'].reverse()
        larghezze(r, ['3-5', '2-5'])
    return True


def mappa(sez, v):
    r = una_riga(sez)
    if not r or len(r['children']) != 2:
        return False
    a, b = (tipi(c) for c in r['children'])
    if not ('desclist' in a and b == ['osmmap']):
        return False
    if v == 'inverti':
        r['children'].reverse()
        larghezze(r, ['1-2', '1-2'])
    return True


def foto4(sez, v):
    rr = sez.get('children') or []
    if len(rr) != 2 or [tipi(c) for c in rr[1].get('children') or []] != [['image']] * 4:
        return False
    r = rr[1]
    imm = [c['children'][0] for c in r['children']]
    if v == 'sfalsata':
        for k in (1, 3):
            imm[k].setdefault('style', {})['margin_top'] = 56
    elif v == 'tre':
        r['children'] = r['children'][:3]
        larghezze(r, ['1-3', '1-3', '1-3'])
        for i in imm[:3]:
            i['settings']['aspect_ratio'], i['settings']['aspect_ratio_custom'] = 'custom', '1/1'
    elif v == 'nastro':
        r['settings']['gap'] = '0'
        for i in imm:
            i['settings']['aspect_ratio'], i['settings']['aspect_ratio_custom'] = 'custom', '3/5'
            i['settings']['border_radius'] = {'tl': 0, 'tr': 0, 'br': 0, 'bl': 0}
    elif v == 'quadrate':
        for i in imm:
            i['settings']['aspect_ratio'], i['settings']['aspect_ratio_custom'] = 'custom', '1/1'
    return True


def intro(sez, v):
    r = una_riga(sez)
    if not r or len(r['children']) != 2:
        return False
    sx, dx = r['children']
    if tipi(sx) != ['section-header'] or tipi(dx) != ['text-block'] \
            or (sx.get('settings') or {}).get('width_medium') != '3-5' or (dx.get('settings') or {}).get('width_medium') != '2-5':
        return False
    if v == 'centro':
        sx['children'][0]['settings']['layout'] = 'center'
        tb = dx['children'][0]
        tb['settings']['text_align'] = 'center'
        tb.setdefault('style', {})['margin_top'] = 20
        sez['children'] = [centrata(r, sx['children'] + dx['children'], r['id'] + '-c')]
    elif v == 'meta':
        larghezze(r, ['1-2', '1-2'])
    elif v == 'stretta':
        larghezze(r, ['2-5', '3-5'])
    elif v == 'larga':
        larghezze(r, ['2-3', '1-3'])
    return True


def ordine_in_pila(sez):
    """Le righe invertite (domande, modulo, mappa a sinistra): sul telefono il titolo e il testo restano PRIMA, con
    «Prima quando le colonne vanno in pila» sulla colonna di destra (OLObuild 1.4.1003)."""
    for r in sez.get('children') or []:
        cc = r.get('children') or []
        if r.get('type') != 'row' or len(cc) != 2:
            continue
        a, b = tipi(cc[0]), tipi(cc[1])
        if (a == ['accordion'] and b == ['section-header']) or (a == ['form'] and b[:1] == ['section-header'] and 'iconlist' in b)                 or (a == ['osmmap'] and 'desclist' in b):
            cc[1].setdefault('settings', {})['primo_in_pila'] = True


def titolo_di(sez):
    return re.sub(r'<[^>]+>', '', json.dumps(sez, ensure_ascii=False))[:400]


def fascia(tid, sez):
    import varia_ordine as VO
    if VO.fondo(sez) != 'video' or not VO.riga_fascia(sez):
        return False
    scelta = FASCE[zlib.crc32((tid + titolo_di(sez)).encode('utf-8')) % len(FASCE)]
    VO.fascia(sez, scelta)
    return True


def profilo_di(tid):
    return (json.load(open(PROFILI, encoding='utf-8')) if os.path.exists(PROFILI) else {}).get(tid)


def applica_tema(tid, nome_file, built, conta=None):
    """Per base.write_theme (dopo linee_grafiche)."""
    if nome_file in ('header.json', 'footer.json', 'theme.json'):
        return
    pr = profilo_di(tid)
    c = built['content'] if isinstance(built, dict) and 'content' in built else built
    if not pr or not isinstance(c, list):
        return
    for s in c:
        if not isinstance(s, dict) or s.get('type') != 'section':
            continue
        for nome, fn, tab in (('faq', faq, FAQ), ('contatti', contatti, CONTATTI), ('mappa', mappa, MAPPA),
                              ('foto4', foto4, FOTO4), ('intro', intro, INTRO)):
            v = tab.get(pr)
            if v and fn(s, v):
                if conta is not None:
                    conta[nome + ':' + v] = conta.get(nome + ':' + v, 0) + 1
                break
        else:
            if fascia(tid, s) and conta is not None:
                conta['fascia'] = conta.get('fascia', 0) + 1
        ordine_in_pila(s)


def main():
    sys.stdout.reconfigure(encoding='utf-8')
    scrivi = '--scrivi' in sys.argv
    temi = [a for a in sys.argv[1:] if not a.startswith('--')] or sorted(os.listdir(TEMI))
    conta = {}
    for t in temi:
        d = os.path.join(TEMI, t)
        for f in sorted(os.listdir(d)):
            if not f.endswith('.json'):
                continue
            p = os.path.join(d, f)
            testo = open(p, encoding='utf-8').read()
            j = json.loads(testo)
            prima = json.dumps(j, sort_keys=True)
            applica_tema(t, f, j, conta)
            if json.dumps(j, sort_keys=True) != prima and scrivi:
                compatto = not testo.lstrip().startswith('{\n') and not testo.lstrip().startswith('[\n')
                open(p, 'w', encoding='utf-8', newline='\n').write(
                    json.dumps(j, ensure_ascii=False, separators=(',', ':')) if compatto else json.dumps(j, ensure_ascii=False, indent=1))
    for k in sorted(conta):
        print('%-22s %d' % (k, conta[k]))
    print('(scritto)' if scrivi else '(prova)')


if __name__ == '__main__':
    main()
