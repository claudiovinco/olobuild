# -*- coding: utf-8 -*-
"""Trova nei temi le foto e i video «velati» (regola dell'utente del 31 ott 2026: MAI media in filigrana sotto un velo
chiaro o sotto un velo scuro che li spegne; o il media si vede, o non si mette).

Guarda gli Sfondi (sezioni, righe, colonne, tile: foto, video, gallerie) con la Sovrapposizione, le diapositive dello
Slider Pro e i livelli-velo (forme sfumate grandi sopra lo sfondo della diapositiva). Un velo è «chiaro» se il suo colore
ha luminanza > 0,5, «pesante» se scuro e piatto oltre la soglia. Una Sovrapposizione sfumata da un lato (overlay_fade)
non conta: vela solo dove c'è il testo.

uso: python media_velati.py [tema ...]       (senza nomi: tutti i temi)
"""
import json
import os
import re
import sys

sys.stdout.reconfigure(encoding='utf-8')
DIR = os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', '..', 'assets', 'data', 'themes')
# % oltre cui un velo scuro piatto spegne il media: 50 di regola, fino a 62 dove il testo sta in alto e la sfumatura dal
# basso non basta (deciso coi contrasti misurati, 31 ott 2026)
SCURO_MAX = 62
CHIARO_MAX = 25         # % oltre cui un velo chiaro sbianca il media
TOKEN = {'primary': 'primary', 'primary-contrast': 'primary_contrast', 'secondary': 'secondary', 'accent': 'accent',
         'dark': 'dark', 'light': 'light', 'muted': 'muted', 'text': 'text', 'text-muted': 'text_muted',
         'background': 'background', 'border': 'border'}


def hex_rgb(h):
    h = h.strip().lstrip('#')
    if len(h) == 3:
        h = ''.join(c * 2 for c in h)
    return tuple(int(h[i:i + 2], 16) / 255 for i in (0, 2, 4))


def lum(rgb):
    f = lambda c: c / 12.92 if c <= 0.03928 else ((c + 0.055) / 1.055) ** 2.4
    r, g, b = (f(c) for c in rgb)
    return 0.2126 * r + 0.7152 * g + 0.0722 * b


def colore(s, pal):
    """(rgb, frazione di opacità dentro il colore) o None."""
    if not isinstance(s, str) or not s:
        return None
    s = s.strip()
    m = re.match(r'color-mix\(in srgb,\s*(.+?)\s+(\d+(?:\.\d+)?)%,\s*(transparent|.+)\)$', s)
    if m:
        base = colore(m.group(1), pal)
        if not base:
            return None
        k = float(m.group(2)) / 100
        if m.group(3) == 'transparent':
            return base[0], base[1] * k
        altro = colore(m.group(3), pal)
        if not altro:
            return base
        return tuple(a * k + b * (1 - k) for a, b in zip(base[0], altro[0])), 1.0
    m = re.match(r'var\(--olo-color-([a-z-]+)', s)
    if m:
        h = pal.get(TOKEN.get(m.group(1), m.group(1).replace('-', '_')))
        return (hex_rgb(h), 1.0) if h else None
    if s.startswith('#'):
        return hex_rgb(s), 1.0
    m = re.match(r'rgba?\(([\d.]+),\s*([\d.]+),\s*([\d.]+)(?:,\s*([\d.]+))?', s)
    if m:
        return tuple(float(m.group(i)) / 255 for i in (1, 2, 3)), float(m.group(4) or 1)
    return None


def palette(tema):
    try:
        j = json.load(open(os.path.join(DIR, tema, 'theme.json'), encoding='utf-8'))
    except (OSError, ValueError):
        return {}
    st = j.get('styles') or {}
    return st.get('colors') or j.get('colors') or {}


def giudica(rgb_op, opacita):
    """'chiaro' / 'pesante' / None per un velo di colore rgb_op (rgb, alfa interno) all'opacità % data."""
    if not rgb_op:
        return None, 0
    rgb, alfa = rgb_op
    eff = opacita * alfa
    if lum(rgb) > 0.5:
        return ('chiaro' if eff > CHIARO_MAX else None), eff
    return ('pesante' if eff > SCURO_MAX else None), eff


def scansiona(tema):
    pal = palette(tema)
    trovati = []

    def visita(o, dove):
        if isinstance(o, dict):
            # Sfondo con media e Sovrapposizione
            if o.get('type') in ('image', 'video', 'gallery') and 'overlay_opacity' in o \
                    and (o.get('image_url') or o.get('video_url') or o.get('gallery_images')) and not o.get('overlay_fade'):
                tipo, eff = giudica(colore(o.get('overlay_color') or '#000000', pal), float(o.get('overlay_opacity') or 0))
                if tipo:
                    trovati.append((dove, 'sfondo ' + o['type'], tipo, round(eff)))
            # diapositive dello Slider Pro
            if isinstance(o.get('slides'), list) and 'transition' in o:
                for k, sl in enumerate(o['slides']):
                    b = sl.get('background') or {}
                    if b.get('type') in ('image', 'video'):
                        tipo, eff = giudica(colore(b.get('overlay') or '#000000', pal), float(b.get('overlayOpacity') or 0) * 100)
                        if tipo:
                            trovati.append((dove + ' › diapositiva %d' % (k + 1), 'slider ' + b['type'], tipo, round(eff)))
                    for L in sl.get('layers') or []:
                        g = L.get('shapeGradient')
                        if L.get('type') == 'shape' and isinstance(g, dict) and isinstance(L.get('width'), (int, float)) \
                                and isinstance(L.get('height'), (int, float)) and L['width'] * L['height'] >= 2500:
                            tipo, eff = giudica(colore(g.get('from'), pal), 100)
                            if tipo == 'chiaro':
                                trovati.append((dove + ' › diapositiva %d' % (k + 1), 'velo ' + str(L.get('id')), tipo, round(eff)))
            for k, v in o.items():
                visita(v, dove)
        elif isinstance(o, list):
            for v in o:
                visita(v, dove)

    for f in sorted(os.listdir(os.path.join(DIR, tema))):
        if f.endswith('.json') and f != 'theme.json':
            try:
                visita(json.load(open(os.path.join(DIR, tema, f), encoding='utf-8')), f[:-5])
            except ValueError:
                pass
    return trovati


if __name__ == '__main__':
    temi = sys.argv[1:] or sorted(d for d in os.listdir(DIR) if os.path.isdir(os.path.join(DIR, d)))
    tot = 0
    per_tema = {}
    for t in temi:
        r = scansiona(t)
        if r:
            per_tema[t] = r
            tot += len(r)
    for t, r in per_tema.items():
        print('%s (%d)' % (t, len(r)))
        for dove, cosa, tipo, eff in r:
            print('   %-34s %-22s %-8s %d%%' % (dove[:34], cosa[:22], tipo, eff))
    print('---- %d velati in %d temi su %d' % (tot, len(per_tema), len(temi)))
