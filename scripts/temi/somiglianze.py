# -*- coding: utf-8 -*-
"""Somiglianze grafiche fra i temi (14 nov 2026). L'utente: «avvia una analisi approfondita alla ricerca di somiglianze
grafiche troppo evidenti tra tutti i temi per migliorare l'unicità dei singoli temi».

Per ogni tema un'impronta in più dimensioni, poi i gruppi di temi con la stessa impronta:
  testata · piede · apertura (dove stanno occhiello, titolo, pulsanti) · titoli di sezione (occhiello, righe, corsivo)
  · caratteri · colori · pulsanti · cursore · tile più usate.
uso: python somiglianze.py [quanti gruppi per dimensione]"""
import colorsys
import json
import os
import re
import sys
from collections import Counter, defaultdict

QUI = os.path.dirname(os.path.abspath(__file__))
TEMI = os.path.normpath(os.path.join(QUI, '..', '..', 'assets', 'data', 'themes'))
PRIVATI = {'clod-evoluzione', 'tutor-clod'}


def carica(tid, f):
    p = os.path.join(TEMI, tid, f)
    if not os.path.exists(p):
        return None
    j = json.load(open(p, encoding='utf-8'))
    return j['content'] if isinstance(j, dict) and 'content' in j else j


def trova(n, tipo):
    if isinstance(n, dict):
        if n.get('type') == tipo:
            return n
        for v in n.values():
            r = trova(v, tipo)
            if r:
                return r
    elif isinstance(n, list):
        for v in n:
            r = trova(v, tipo)
            if r:
                return r
    return None


def tutte(n, tipo, out=None):
    out = [] if out is None else out
    if isinstance(n, dict):
        if n.get('type') == tipo:
            out.append(n)
        for v in n.values():
            if isinstance(v, (dict, list)):
                tutte(v, tipo, out)
    elif isinstance(n, list):
        for v in n:
            tutte(v, tipo, out)
    return out


def scheletro(n):
    if not isinstance(n, dict):
        return ''
    t = n.get('type')
    if t in ('section', 'row', 'column', 'inner-columns', 'inner-column'):
        s = n.get('settings') or {}
        w = s.get('width_medium', '') if t in ('column', 'inner-column') else ''
        return '%s%s(%s)' % (t[0].upper(), w, ','.join(scheletro(c) for c in n.get('children') or []))
    return t or ''


def font(s):
    m = re.match(r"\s*'?([^',]+)'?", s or '')
    return m.group(1).strip() if m else ''


def tono(hexc):
    try:
        h = hexc.lstrip('#')
        r, g, b = (int(h[i:i + 2], 16) / 255 for i in (0, 2, 4))
    except Exception:
        return None, None, None
    hh, ll, ss = colorsys.rgb_to_hls(r, g, b)
    return hh * 360, ll, ss


def apertura(tid, home):
    """La disposizione del testo nell'apertura: tile, e per lo Slider Pro dove stanno occhiello, titolo, pulsanti."""
    if not home:
        return 'vuota'
    s0 = home[0]
    ps = trova(s0, 'proslider')
    if not ps:
        h = trova(s0, 'hero') or trova(s0, 'hero-split')
        return 'tile:' + (h.get('type') if h else (scheletro(s0)[:40]))
    sl = ((ps.get('settings') or {}).get('slides') or [{}])[0]
    testi = [l for l in sl.get('layers') or [] if l.get('type') in ('text', None) and isinstance(l.get('content'), str)
             and l.get('content').strip() and float(l.get('x', 0) or 0) > -100]
    if not testi:
        return 'slider:senza-testo'
    tit = max(testi, key=lambda l: float(l.get('fontSize') or 0))
    occ = [l for l in testi if l is not tit and (l.get('textTransform') == 'uppercase' and float(l.get('fontSize') or 99) <= 15)]
    bott = [l for l in sl.get('layers') or [] if l.get('type') == 'button' and float(l.get('x', 0) or 0) > -100]

    def zona(l):
        x, y = float(l.get('x') or 0), float(l.get('y') or 0)
        return ('sx' if x < 30 else 'cx' if x < 55 else 'dx') + '-' + ('alto' if y < 33 else 'mezzo' if y < 62 else 'basso')
    al = tit.get('textAlign') or 'left'
    sig = 'tit:%s/%s' % (zona(tit), al)
    if occ:
        sig += ' occ:' + zona(occ[0])
    if bott:
        sig += ' bott:' + zona(bott[0])
    return sig


def stile_titoli(pagine):
    """Lo stile più frequente dei titoli di sezione del tema."""
    c = Counter()
    for p in pagine:
        for sh in tutte(p, 'section-header'):
            s = sh.get('settings') or {}
            hl = s.get('headline_lines') or []
            occh = 'occ:' + ('mono' if 'mono' in str(s.get('eyebrow_font_family')) else str(s.get('eyebrow_font_family') or '-')) \
                if s.get('eyebrow_show', True) and s.get('eyebrow_text') else 'occ:no'
            due = len(hl) >= 2
            corsivo = due and isinstance(hl[1], dict) and bool(hl[1].get('italic'))
            col2 = due and isinstance(hl[1], dict) and hl[1].get('color') != (hl[0].get('color') if isinstance(hl[0], dict) else None)
            c['%s · %s righe · %s · %s · %s' % (occh, len(hl), 'corsivo' if corsivo else 'dritto', '2colori' if col2 else '1colore',
                                                 s.get('layout') or 'stack')] += 1
    return c.most_common(1)[0][0] if c else '-'


def main():
    sys.stdout.reconfigure(encoding='utf-8')
    quanti = int(sys.argv[1]) if len(sys.argv) > 1 else 8
    D = defaultdict(dict)
    tile_temi = Counter()
    for tid in sorted(os.listdir(TEMI)):
        if tid in PRIVATI:
            continue
        th = json.load(open(os.path.join(TEMI, tid, 'theme.json'), encoding='utf-8'))
        st = th.get('styles') or {}
        ty, co, bu = st.get('typography') or {}, st.get('colors') or {}, st.get('buttons') or {}
        # testata
        head = carica(tid, 'header.json')
        mm = trova(head, 'megamenu')
        if mm:
            m = mm.get('settings') or {}
            D['testata'][tid] = 'menu:%s logo:%s modo:%s hamb:%s hover:%s maiusc:%s topbar:%s' % (
                m.get('layout'), m.get('logo_position'), m.get('header_mode'), m.get('hamburger_style'), m.get('hover_effect'),
                m.get('text_transform'), bool(m.get('topbar_enabled')))
        else:
            D['testata'][tid] = 'altra:' + (scheletro(head[0])[:60] if head else '-')
        # piede
        foot = carica(tid, 'footer.json')
        D['piede'][tid] = ' / '.join(scheletro(s) for s in foot or [])[:140]
        # apertura
        pagine = []
        for slug, pg in (th.get('pages') or {}).items():
            tpl = (th.get('templates') or {}).get(pg.get('template', slug)) or {}
            c = carica(tid, tpl.get('file', '')) if tpl.get('file') else None
            if c is not None:
                pagine.append(c)
                if slug == 'home':
                    D['apertura'][tid] = apertura(tid, c)
        D['titoli di sezione'][tid] = stile_titoli(pagine)
        D['caratteri'][tid] = '%s + %s' % (font(ty.get('font_family_heading')), font(ty.get('font_family')))
        h, l, s = tono(co.get('primary', ''))
        _, lb, _ = tono(co.get('background', ''))
        if h is not None and lb is not None:
            D['colori'][tid] = '%s · primario %s%s' % ('scuro' if lb < .35 else 'chiaro', ['rosso', 'arancio', 'giallo', 'verde', 'verde',
                                                                                         'turchese', 'azzurro', 'blu', 'viola', 'magenta',
                                                                                         'rosa', 'rosso'][int(h // 30) % 12],
                                                       '' if s > .25 else ' (grigio)')
        r = str(bu.get('border_radius', bu.get('radius', '')))
        D['pulsanti'][tid] = 'raggio %s · %s · spaziatura %s' % (r, bu.get('text_transform', '-'), bu.get('letter_spacing', '-'))
        D['cursore'][tid] = str((th.get('cursor') or {}).get('style', '-'))
        usate = set()
        for p in pagine:
            def w(n):
                if isinstance(n, dict):
                    if n.get('type') and n['type'] not in ('section', 'row', 'column', 'inner-columns', 'inner-column'):
                        usate.add(n['type'])
                    for v in n.values():
                        if isinstance(v, (dict, list)):
                            w(v)
                elif isinstance(n, list):
                    for v in n:
                        w(v)
            w(p)
        tile_temi.update(usate)
    n = len(D['caratteri'])
    print('%d temi\n' % n)
    for dim, m in D.items():
        g = defaultdict(list)
        for t, v in m.items():
            g[v].append(t)
        top = sorted(g.items(), key=lambda x: -len(x[1]))[:quanti]
        print('══ %s: %d impronte diverse su %d temi' % (dim.upper(), len(g), len(m)))
        for v, ts in top:
            print('  %3d  %s' % (len(ts), v[:150]))
            if len(ts) <= 12:
                print('        ' + ', '.join(ts))
        print()
    print('══ TILE NEI TEMI (in quanti temi compare)')
    for t, k in tile_temi.most_common(30):
        print('  %3d  %s' % (k, t))


if __name__ == '__main__':
    main()
