# -*- coding: utf-8 -*-
"""Arricchisce le pagine scarne dei temi più vecchi (verifica del 13 nov 2026: 40 temi con pagine interne fatte solo
di titolo e un blocco). A ogni pagina aggiunge in fondo una o due sezioni scritte per quell'attività (arricchimenti.py),
fatte col kit: domande, passi, numeri, citazione, foto, schede, invito. I colori e i caratteri sono i token del tema,
quindi le sezioni nuove prendono l'aspetto del tema; le foto sono quelle che il tema usa già.
Può anche aggiungere una pagina nuova (theme.json: pagina, template, voce di menu prima dell'ultima).

Le sezioni nuove hanno gli id «<tema>-<pagina>-x<n>»: rilanciando, quelle vecchie si tolgono e si rimettono, quindi si può
correggere un testo e rilanciare. uso: python arricchisci.py [tema …]
"""
import json
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
sys.stdout.reconfigure(encoding='utf-8')
from base import Tpl, mix, solid  # noqa: E402
from kit import Kit  # noqa: E402
import pezzi_nov as P  # noqa: E402
from arricchimenti import SPEC  # noqa: E402

# caratteri da titolo che non reggono una frase lunga (strettissimi, larghissimi, in maiuscolo, a pennello): lì la
# citazione e le domande usano il carattere del testo
DISPLAY = {'Six Caps', 'Syncopate', 'Anton', 'Bebas Neue', 'Big Shoulders Display', 'Oswald', 'Orbitron', 'Krona One', 'Syne',
           'Unbounded', 'Shrikhand', 'Righteous', 'Caveat Brush', 'Saira Condensed', 'Chakra Petch', 'Italiana', 'Darker Grotesque'}
TEMI = os.path.normpath(os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', '..', 'assets', 'data', 'themes'))


def chiaro(hexc):
    h = hexc.lstrip('#')
    r, g, b = (int(h[i:i + 2], 16) / 255.0 for i in (0, 2, 4))
    return 0.2126 * r + 0.7152 * g + 0.0722 * b > 0.5


def _lum(hexc):
    h = hexc.lstrip('#')
    v = []
    for i in (0, 2, 4):
        c = int(h[i:i + 2], 16) / 255.0
        v.append(c / 12.92 if c <= 0.03928 else ((c + 0.055) / 1.055) ** 2.4)
    return 0.2126 * v[0] + 0.7152 * v[1] + 0.0722 * v[2]


def contrasto(a, b):
    x, y = sorted((_lum(a), _lum(b)), reverse=True)
    return (x + 0.05) / (y + 0.05)


def _mescola(a, b, p):
    """color-mix(in srgb, a p%, b): media dei valori sRGB."""
    a, b = a.lstrip('#'), b.lstrip('#')
    return '#' + ''.join('%02x' % round(int(a[i:i + 2], 16) * p + int(b[i:i + 2], 16) * (1 - p)) for i in (0, 2, 4))


def _esadecimale(v):
    return isinstance(v, str) and v.startswith('#') and len(v) == 7


def accento_leggibile(colori):
    """L'accento dei testi piccoli (occhielli, numeri dei passi, parola in corsivo) deve reggere 4,5:1 sul fondo e sulla
    sala del tema (misura del 13 nov 2026: giallo e rosa chiari erano a 1,2–2,9). Primario quasi leggibile → lo stesso
    primario un poco scurito verso il colore del testo; primario lontano → il colore dei link, se si legge; se no il
    primario scurito quanto basta. Restituisce None se il primario va già bene."""
    prim, testo = colori.get('primary'), colori.get('text')
    fondi = [c for c in (colori.get('background'), colori.get('muted')) if _esadecimale(c)]
    if not (_esadecimale(prim) and _esadecimale(testo) and fondi):
        return None
    leggi = lambda c: min(contrasto(c, f) for f in fondi)
    if leggi(prim) >= 4.5:
        return None
    link = colori.get('link')
    if leggi(prim) < 3.5 and _esadecimale(link) and link.lower() != prim.lower() and leggi(link) >= 4.4:
        return 'var(--olo-color-link)'
    for p in range(95, 0, -5):
        if leggi(_mescola(prim, testo, p / 100.0)) >= 4.6:
            return mix('var(--olo-color-primary)', p, 'var(--olo-color-text)')
    return 'var(--olo-color-text)'


def kit_del_tema(tid, th, media):
    st = th['styles']
    peso = str(st['typography'].get('font_weight_heading') or '600')
    try:
        raggio = int(str(st['layout'].get('border_radius', '0')).replace('px', '') or 0)
    except ValueError:
        raggio = 0
    maiusc = str(st.get('buttons', {}).get('text_transform', 'uppercase')) == 'uppercase'
    K = Kit(tid, media, raggio=raggio, peso_titoli=peso, corsivo=True, maiuscole_pulsanti=maiusc,
            tema_chiaro=chiaro(st['colors'].get('background', '#ffffff')))
    titoli = str(st['typography'].get('font_family_heading', '')).split(',')[0].strip(' "')
    K.display = titoli in DISPLAY or st['typography'].get('heading_text_transform') == 'uppercase'
    # i pulsanti restano nel primario (col suo colore di contrasto), i testi prendono l'accento leggibile
    K.acc_pulsanti = K.c['acc']
    alt = accento_leggibile(st['colors'])
    if alt:
        K.c['acc'] = alt
    return K


def bottone(K, t, etichetta, url, tipo):
    salva = K.c['acc']
    K.c['acc'] = K.acc_pulsanti
    try:
        return K.button(t, etichetta, link(url), kind=tipo, pad_top=18)
    finally:
        K.c['acc'] = salva


def link(v):
    if v.startswith('@'):
        return '{{PAGINA:%s}}' % v[1:]
    return v


def costruisci(K, t, s, fondo):
    """Una sezione dallo spec: (tipo, campi…)."""
    C = K.c
    bg = solid(C['sala']) if fondo else None
    tipo = s[0]
    if tipo == 'faq':
        _, occ, r1, r2, coppie = s[:5]
        nota = s[5] if len(s) > 5 else ''
        sin = [K.sechead(t, occ, [r1, r2], size=48, layout='stack', size_mobile=34)]
        if nota:
            sin.append(K.text(t, nota, size=16))
        acc = K.faq(t, coppie, t.key)
        if K.display:
            acc['settings'].update({'header_font_family': 'var(--olo-font-family)', 'header_font_size': 19, 'header_font_weight': '600'})
        return t.section([t.row([t.col(sin, wm='1-3', ws='1-1'), t.col([acc], wm='2-3', ws='1-1')],
                                layout='custom', custom='33,67', gap=64, valign='start')],
                         bg=bg, padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110})
    if tipo == 'passi':
        _, occ, r1, r2, voci = s[:5]
        tag = s[5] if len(s) > 5 else ''
        return t.section([t.one([K.sechead(t, occ, [r1, r2], size=56, size_mobile=36, tagline=tag)]),
                          t.one([P.passi(K, t, voci, colonne=min(4, len(voci)))])],
                         bg=bg, padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 100})
    if tipo == 'numeri':
        _, voci = s[:2]
        titolo = s[2] if len(s) > 2 else None
        figli = []
        if titolo:
            figli.append(t.one([K.sechead(t, titolo[0], [titolo[1], titolo[2]], size=48, size_mobile=34)]))
        n = K.numeri(t, voci, colonne=len(voci), size=56)
        n['settings']['forma'] = 'banda'
        figli.append(t.one([n]))
        return t.section(figli, bg=bg, padding='large', width='large', style={'padding_top': 90, 'padding_bottom': 90})
    if tipo == 'citazione':
        _, testo, autore = s[:3]
        cit = P.testimonianza(K, t, testo, autore, s[3] if len(s) > 3 else '', size=34)
        if K.display:
            cit['settings'].update({'quote_font': 'body', 'quote_size': 26})
        return t.section([K.centered(t, [cit])],
                         bg=bg, padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110})
    if tipo == 'foto':
        _, voci = s[:2]
        titolo = s[2] if len(s) > 2 else None
        figli = []
        if titolo:
            figli.append(t.one([K.sechead(t, titolo[0], [titolo[1], titolo[2]], size=48, size_mobile=34)]))
        figli.append(P.fila_foto(K, t, voci, ratio='4/3' if len(voci) > 2 else '3/2'))
        return t.section(figli, bg=bg, padding='large', width='large', style={'padding_top': 100, 'padding_bottom': 100})
    if tipo == 'schede':
        _, occ, r1, r2, voci = s[:5]
        sc = P.schede_icone(K, t, voci, colonne=min(4, len(voci)), fondo=C['fondo'] if fondo else C['sala'])
        sc['settings']['title_size'] = 34
        return t.section([t.one([K.sechead(t, occ, [r1, r2], size=56, size_mobile=36)]), t.one([sc])],
                         bg=bg, padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 100})
    if tipo == 'invito':
        _, occ, r1, r2, testo, bottoni = s[:6]
        pul = [bottone(K, t, a, b, k) for a, b, k in bottoni]
        riga = pul[0] if len(pul) == 1 else t.row([t.col([p], wm='auto', ws='auto') for p in pul], layout='auto', gap=12,
                                                      anim=None, stack=False)
        return t.section([t.row([t.col([K.sechead(t, occ, [r1, r2], size=56, size_mobile=36)], wm='1-2', ws='1-1'),
                                 t.col([K.text(t, testo, size=17), riga] if len(pul) > 1 else [K.text(t, testo, size=17), riga],
                                       wm='1-2', ws='1-1')], layout='50-50', gap=56, valign='end')],
                         bg=bg, padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110})
    if tipo == 'split':
        _, foto, alt, occ, r1, r2, testo = s[:7]
        bott = s[7] if len(s) > 7 else None
        testo_col = [K.sechead(t, occ, [r1, r2], size=52, size_mobile=36), K.text(t, testo, size=17)]
        if bott:
            testo_col.append(bottone(K, t, bott[0], bott[1], bott[2] if len(bott) > 2 else 'filo'))
        return t.section([t.row([t.col([K.image(t, foto, alt, ratio='4/5')], wm='2-5', ws='1-1'),
                                 t.col(testo_col, wm='3-5', ws='1-1')], layout='custom', custom='42,58', gap=64,
                                valign='center')],
                         bg=bg, padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110})
    raise ValueError(tipo)


def ha_fondo(sez):
    bg = (sez.get('settings') or {}).get('bg')
    return isinstance(bg, dict) and bg.get('type') not in (None, 'none')


def media_del_tema(d):
    import re
    m = set()
    for f in os.listdir(d):
        if f.endswith('.json'):
            m |= set(re.findall(r'\{\{OLOBUILD_LIBRARY\}\}/media/([^"\\?#]+)', open(os.path.join(d, f), encoding='utf-8').read()))
    return m


def applica(tid, spec):
    d = os.path.join(TEMI, tid)
    thp = os.path.join(d, 'theme.json')
    th = json.load(open(thp, encoding='utf-8'))
    media = media_del_tema(d)
    K = kit_del_tema(tid, th, media)
    # pagine nuove
    for key, info in (spec.get('pagine_nuove') or {}).items():
        if key not in th['pages']:
            th['pages'][key] = {'title': info['titolo'], 'template': key, 'slug': key}
            th['templates'][key] = {'file': key + '.json', 'type': 'page', 'title': '%s — %s' % (th['name'], info['titolo'])}
            voci = th['menu']['items']
            voci.insert(max(0, len(voci) - 1) if info.get('prima_ultima', True) else len(voci),
                        {'title': info['menu'], 'url': '{{PAGINA:%s}}' % key})
        f = os.path.join(d, key + '.json')
        if not os.path.exists(f):
            t0 = Tpl(tid, key)
            titolo = t0.section([t0.one([K.sechead(t0, info['occhiello'], info['titolo_righe'], size=96, size_mobile=46,
                                                   tagline=info.get('sotto', ''))])],
                                padding='large', width='large', style={'padding_top': 200, 'padding_bottom': 60})
            open(f, 'w', encoding='utf-8', newline='\n').write(json.dumps({'settings': {}, 'content': [titolo]},
                                                                       ensure_ascii=False, separators=(',', ':')))
    open(thp, 'w', encoding='utf-8', newline='\n').write(json.dumps(th, ensure_ascii=False, separators=(',', ':')))
    totale = 0
    for pagina, sezioni in spec['pagine'].items():
        f = os.path.join(d, th['templates'][th['pages'][pagina].get('template', pagina)]['file'])
        j = json.load(open(f, encoding='utf-8'))
        contenuto = j['content'] if isinstance(j, dict) and 'content' in j else j
        pref = '%s-%s-x' % (tid, pagina)
        contenuto[:] = [n for n in contenuto if not str(n.get('id', '')).startswith(pref)]
        t = Tpl(tid, pagina + '-x')
        fondo = not ha_fondo(contenuto[-1]) if contenuto else False
        # le foto dello spec devono essere fra i media del tema (o dichiarate nello spec)
        K.media = media | set(spec.get('media', []))
        nuove = []
        for s in sezioni:
            nuove.append(costruisci(K, t, s, fondo))
            fondo = not fondo
        contenuto.extend(nuove)
        totale += len(nuove)
        if isinstance(j, dict) and 'content' in j:
            j['content'] = contenuto
        else:
            j = contenuto
        open(f, 'w', encoding='utf-8', newline='\n').write(json.dumps(j, ensure_ascii=False, separators=(',', ':')))
    return totale


def main():
    temi = sys.argv[1:] or sorted(SPEC)
    for tid in temi:
        n = applica(tid, SPEC[tid])
        print('%-20s %d sezioni nuove' % (tid, n))


if __name__ == '__main__':
    main()
