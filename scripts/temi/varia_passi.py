# -*- coding: utf-8 -*-
"""Toglie la tile «Step timeline» (le tappe numerate con la foto, il titolo con l'accento e il dato col simbolo) dai temi
dove era diventata la firma di tutti: 57 temi la avevano uguale, e l'utente (13 nov 2026): «questa tile l'hai usata troppo
spesso, è troppo riconoscibile, non voglio che i commenti siano "a parte l'hero, sono tutti temi uguali"».

Gli stessi contenuti (foto, numero, etichetta, titolo con accento, testo, dato) diventano, a turno fra i temi e mai
quella che il tema usa già:
- «file»   righe alternate, la foto da una parte e il testo dall'altra (Immagine + Headline + Text block);
- «carte»  carte che si girano: la foto davanti, il racconto e il dato dietro (Flip card);
- «righe»  righe grandi con la foto che si affaccia al passaggio (Hover list);
- «fila»   una fila di foto e sotto i passaggi numerati (Immagine + Process steps);
- «colonne» una foto sola, larga, e sotto i passaggi in colonne col numero grande (Immagine + Headline + Text block).
Resta solo dove è usata in un altro modo (Leva Consulenza: quattro schede da un passaggio).

Le righe nuove hanno id «<tema>-<pagina>-vp…». uso: python varia_passi.py [--scrivi] [tema …]"""
import json
import os
import re
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
sys.stdout.reconfigure(encoding='utf-8')
from base import Tpl  # noqa: E402
import pezzi_nov as P  # noqa: E402
from arricchisci import kit_del_tema, media_del_tema, TEMI  # noqa: E402

DATE = json.load(open(os.path.join(TEMI, '..', 'temi-creati.json'), encoding='utf-8'))
TIPO = {'carte': 'flipcard', 'righe': 'hoverlist', 'fila': 'process-steps', 'file': None, 'colonne': None}
# le file alternate due volte: sono le più libere (foto, titolo, testo in righe che si scambiano)
GIRO = ['file', 'colonne', 'carte', 'righe', 'file', 'fila']
TIENI = {'leva-consulenza'}


def file_media(url):
    m = re.search(r'/media/([^"?#]+)$', url or '')
    return m.group(1) if m else ''


def voci(st):
    out = []
    for it in st['settings'].get('items') or []:
        tit = (it.get('title') or '').strip()
        acc = (it.get('title_accent') or '').strip()
        dopo = (it.get('title_after') or '').strip()
        out.append({'n': str(it.get('counter') or '').strip(), 'tag': (it.get('tag_text') or '').strip(),
                    'foto': file_media(it.get('media_image')), 'pre': (it.get('pre_title') or '').strip(),
                    'titolo': tit, 'acc': acc, 'dopo': dopo,
                    'testo': re.sub(r'</?p>', '', it.get('description') or '').strip(),
                    'val': str(it.get('footer_value') or '').strip(), 'lab': (it.get('footer_label') or '').strip()})
    return out


def dato(v):
    return ('%s %s' % (v['val'], v['lab'])).strip()


def titolo_pieno(v, em=True):
    a = ('<em>%s</em>' % v['acc']) if (em and v['acc']) else v['acc']
    return ' '.join(x for x in (v['titolo'], a, v['dopo']) if x)


def file_alternate(K, t, vv):
    C = K.c
    righe = []
    for k, v in enumerate(vv):
        testo = [K.label(t, ' · '.join(x for x in (v['n'], v['tag'] or v['pre']) if x), color=C['acc']),
                 K.headline(t, titolo_pieno(v), size=44, tag='h3', pad_bottom=14),
                 K.text(t, '<p>%s</p>' % v['testo'], size=17)]
        if dato(v):
            testo.append(K.label(t, dato(v), color=C['tenue'], pad_top=8))
        foto = K.image(t, v['foto'], titolo_pieno(v, em=False), ratio='4/3') if v['foto'] else K.text(t, '')
        cols = [t.col([foto], wm='1-2', ws='1-1'), t.col(testo, wm='1-2', ws='1-1')]
        if k % 2:
            cols.reverse()
        righe.append(t.row(cols, layout='50-50', gap=64, valign='center', style={'margin_top': 0 if k == 0 else 72}))
    return righe


def carte_girano(K, t, vv):
    n = len(vv)
    col = 4 if n in (4, 8) else 3
    righe = []
    for i in range(0, n, col):
        gruppo = vv[i:i + col]
        if len(gruppo) < col:
            col = len(gruppo) if len(gruppo) in (3, 4) else col
        dietro = lambda v: '<p>%s</p>%s' % (v['testo'], ('<p><strong>%s</strong></p>' % dato(v)) if dato(v) else '')
        r = P.carte(K, t, [(v['foto'], titolo_pieno(v, em=False), ' · '.join(x for x in (v['n'], v['tag'] or v['pre']) if x),
                            dietro(v)) for v in gruppo], altezza='420', colonne=col)
        if i:
            r['settings'] = dict(r.get('settings') or {})
            r['style'] = dict(r.get('style') or {}, margin_top=18)
        righe.append(r)
    return righe


def righe_foto(K, t, vv):
    el = P.elenco_hover(K, t, [(titolo_pieno(v, em=False), ' · '.join(x for x in (v['tag'] or v['pre'], dato(v)) if x), v['testo'],
                                v['foto']) for v in vv], '', size=46)
    for k, it in enumerate(el['settings']['items']):
        it['number'] = vv[k]['n'].zfill(2) if vv[k]['n'].isdigit() else vv[k]['n']
        it['link_url'] = ''
    return [t.one([el])]


def fila_passi(K, t, vv):
    foto = [(v['foto'], titolo_pieno(v, em=False)) for v in vv if v['foto']][:4]
    righe = []
    if len(foto) >= 2:
        righe.append(P.fila_foto(K, t, foto, ratio='4/5'))
    # la Descrizione dei Passaggi è testo semplice (un <strong> si vedeva scritto, 15 nov 2026): il dato del passo («2 ore»,
    # «90 giorni prima») va nell'etichetta accanto al numero, che è già la riga in evidenza
    ps = P.passi(K, t, [(' · '.join(x for x in (v['n'], dato(v)) if x), titolo_pieno(v, em=False), v['testo'])
                        for v in vv], colonne=min(4, len(vv)))
    righe.append(t.row([t.col([ps], wm='1-1', ws='1-1')], layout='100', style={'margin_top': 40}))
    return righe


def colonne_numeri(K, t, vv):
    C = K.c
    foto = next((v for v in vv if v['foto']), None)
    righe = []
    if foto:
        righe.append(t.row([t.col([K.image(t, foto['foto'], titolo_pieno(foto, em=False), ratio='21/9')], wm='1-1', ws='1-1')],
                           layout='100'))
    n = len(vv)
    per = 3 if n in (3, 6, 5) else (4 if n in (4, 8) else 2)
    w = {2: '1-2', 3: '1-3', 4: '1-4'}[per]
    lay = {2: '50-50', 3: '33-33-33', 4: '25-25-25-25'}[per]
    for i in range(0, n, per):
        cols = []
        for v in vv[i:i + per]:
            blocco = [K.headline(t, v['n'] or '·', size=76, tag='p', pad_bottom=4, color=C['acc']),
                      K.label(t, v['tag'] or v['pre'], color=C['tenue']),
                      K.headline(t, titolo_pieno(v), size=28, tag='h3', pad_bottom=10),
                      K.text(t, '<p>%s</p>' % v['testo'], size=16)]
            if dato(v):
                blocco.append(K.label(t, dato(v), color=C['acc'], pad_top=6))
            cols.append(t.col(blocco, wm=w, ws='1-1'))
        righe.append(t.row(cols, layout=lay, gap=40, valign='start', style={'margin_top': 48}))
    return righe


COSTRUISCI = {'colonne': colonne_numeri, 'file': file_alternate, 'carte': carte_girano, 'righe': righe_foto, 'fila': fila_passi}


def tipi_del_tema(d):
    tipi = set()
    for f in os.listdir(d):
        if f.endswith('.json') and f != 'theme.json':
            tipi |= set(re.findall(r'"type":"([a-z0-9-]+)"', open(os.path.join(d, f), encoding='utf-8').read()))
    return tipi


SCELTE = os.path.join(os.path.dirname(os.path.abspath(__file__)), 'passi_scelte.json')


def applica_tema(theme, pagina, built, media):
    """Per base.write_theme: un tema rigenerato ritrova la disposizione scelta per i suoi passaggi (passi_scelte.json)."""
    scelte = json.load(open(SCELTE, encoding='utf-8')) if os.path.exists(SCELTE) else {}
    qui = (scelte.get(theme['id']) or {}).get(pagina)
    if not qui:
        return
    K = kit_del_tema(theme['id'], theme, media)
    t = Tpl(theme['id'], pagina + '-vp')

    def cambia(nodi):
        out = []
        for n in nodi:
            ch = n.get('children') or []
            if (n.get('type') == 'row' and len(ch) == 1 and len(ch[0].get('children') or []) == 1
                    and ch[0]['children'][0].get('type') == 'step-timeline'):
                vv = voci(ch[0]['children'][0])
                q = qui if not (qui in ('carte', 'righe', 'fila') and not all(v['foto'] for v in vv)) else 'colonne'
                out.extend(COSTRUISCI[q](K, t, vv))
                continue
            if ch:
                n['children'] = cambia(ch)
            out.append(n)
        return out
    if isinstance(built, dict) and 'content' in built:
        built['content'] = cambia(built['content'])
    elif isinstance(built, list):
        built[:] = cambia(built)


def main():
    scrivi = '--scrivi' in sys.argv
    scelti = [a for a in sys.argv[1:] if not a.startswith('--')]
    temi = []
    for tid in sorted(os.listdir(TEMI), key=lambda x: (DATE.get(x, ''), x)):
        d = os.path.join(TEMI, tid)
        if tid in TIENI or (scelti and tid not in scelti):
            continue
        if any('"step-timeline"' in open(os.path.join(d, f), encoding='utf-8').read()
               for f in os.listdir(d) if f.endswith('.json') and f != 'theme.json'):
            temi.append(tid)
    ultima, k = None, 0
    for tid in temi:
        d = os.path.join(TEMI, tid)
        tipi = tipi_del_tema(d)
        # la disposizione: a turno, mai quella che il tema usa già, mai uguale al tema creato prima
        scelta = None
        for j in range(len(GIRO)):
            cand = GIRO[(k + j) % len(GIRO)]
            if cand != ultima and (TIPO[cand] is None or TIPO[cand] not in tipi):
                scelta = cand
                k = (k + j + 1) % len(GIRO)
                break
        scelta = scelta or 'file'
        ultima = scelta
        th = json.load(open(os.path.join(d, 'theme.json'), encoding='utf-8'))
        K = kit_del_tema(tid, th, media_del_tema(d))
        for f in sorted(os.listdir(d)):
            if not f.endswith('.json') or f == 'theme.json':
                continue
            p = os.path.join(d, f)
            testo = open(p, encoding='utf-8').read()
            if '"step-timeline"' not in testo:
                continue
            j = json.loads(testo)
            pagina = f[:-5]
            t = Tpl(tid, pagina + '-vp')

            def cambia(nodi):
                out = []
                for n in nodi:
                    ch = n.get('children') or []
                    if (n.get('type') == 'row' and len(ch) == 1 and len(ch[0].get('children') or []) == 1
                            and ch[0]['children'][0].get('type') == 'step-timeline'):
                        vv = voci(ch[0]['children'][0])
                        qui = scelta
                        if qui in ('carte', 'righe', 'fila') and not all(v['foto'] for v in vv):
                            qui = 'colonne'  # carte e righe vogliono una foto per passaggio
                        nuove = COSTRUISCI[qui](K, t, vv)
                        print('%-18s %-22s %-7s %d passi → %d righe' % (tid, f, qui, len(vv), len(nuove)))
                        out.extend(nuove)
                        continue
                    if ch:
                        n['children'] = cambia(ch)
                    out.append(n)
                return out
            if isinstance(j, dict) and 'content' in j:
                j['content'] = cambia(j['content'])
            else:
                j = cambia(j)
            if scrivi:
                open(p, 'w', encoding='utf-8', newline='\n').write(json.dumps(j, ensure_ascii=False, separators=(',', ':')))
    print(len(temi), 'temi', '(scritti)' if scrivi else '(prova)')


if __name__ == '__main__':
    main()
