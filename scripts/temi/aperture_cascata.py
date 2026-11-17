# -*- coding: utf-8 -*-
"""Seconda passata sulle aperture al telefono, dalle misure fatte DOPO la prima correzione (aperture_leggibili.py): nel primo
gruppo di testi in alto, se un testo che non è un titolo (sotto i 30 px reali: occhiello, paragrafo) tocca il livello
sotto e si sovrappongono in orizzontale, quel livello e tutti quelli dopo nel gruppo scendono quanto serve (8 px di
stacco). Succede con gli occhielli lunghi che, ingranditi, vanno su due righe. I titoli a righe strette restano come
sono (sono disegnati così). Scrive `responsive.mobile.y`.
uso: python cascata.py misure_dopo.json [--prova] [tema …]"""
import glob
import json
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
MIS = json.load(open(sys.argv[1], encoding='utf-8'))
PROVA = '--prova' in sys.argv
SOLO = set(a for a in sys.argv[2:] if not a.startswith('--'))
RADICE = 'T:/TECNICA/olobuild/assets/data/themes'
SALTA = {'sonnambulo', 'scroscio', 'fuori-registro', 'gomitolo', 'cuccia', 'fuorimisura', 'pista-nera', 'orbita-bassa'}
CATENA = ('mobile', 'tablet', 'notebook')


def efficace(l, k):
    for bp in CATENA:
        v = ((l.get('responsive') or {}).get(bp) or {}).get(k)
        if v is not None and v != '':
            return v
    return l.get(k)


def livelli(dati):
    out = {}

    def giro(x):
        if isinstance(x, dict):
            if x.get('type') == 'proslider':
                for sl in (x.get('settings') or {}).get('slides') or []:
                    for l in sl.get('layers') or []:
                        if l.get('id'):
                            out[l['id']] = l
            for v in x.values():
                giro(v)
        elif isinstance(x, list):
            for v in x:
                giro(v)
    giro(dati)
    return out


tot = 0
for tema, v in sorted(MIS.items()):
    if (SOLO and tema not in SOLO) or tema in SALTA or 'misure' not in v or v['misure'].get('nessuno'):
        continue
    m = v['misure']
    ch, cw = m['ch'], m['cw']
    f = os.path.join(RADICE, tema, 'home.json')
    if not os.path.exists(f):
        continue
    testo = open(f, encoding='utf-8').read()
    d = json.loads(testo)
    if json.dumps(d, ensure_ascii=False, separators=(',', ':')) != testo.rstrip('\n'):
        print(tema, '!! formato non riconosciuto')
        continue
    L = livelli(d)
    cambi = []
    for si, liv in enumerate(m['slides']):
        vivi = sorted([l for l in liv if l['b'] > 0 and l['y'] < ch and l.get('id') in L], key=lambda z: z['y'])
        gruppo = []
        for l in vivi:
            if gruppo and l['y'] - max(z['y'] for z in gruppo) > ch * 0.12:
                break
            gruppo.append(dict(l))
        spinta = 0.0
        for i in range(1, len(gruppo)):
            b = gruppo[i]
            b['y'] += spinta
            b['b'] += spinta
            for a in gruppo[:i]:
                if a['fs'] >= 30:
                    continue
                orizz = min(cw - a['dx'], cw - b['dx']) - max(a['sx'], b['sx'])
                # un pulsante si misura sulla scritta: il suo riquadro sporge di ~8 px sopra, quindi chiede 16 px
                stacco = 16 if (b['btn'] and not a['btn']) else 8
                if orizz > 10 and b['y'] < a['b'] + stacco - 2 and b['y'] > a['y']:
                    serve = a['b'] + stacco - b['y']
                    spinta += serve
                    b['y'] += serve
                    b['b'] += serve
            if spinta:
                dl = L[b['id']]
                y = float(efficace(dl, 'y') or 0)
                nuovo = round(y + spinta / ch * 100, 1)
                dl.setdefault('responsive', {}).setdefault('mobile', {})['y'] = nuovo
                cambi.append('s%d %s y %s -> %s' % (si + 1, b['t'][:26], y, nuovo))
    if cambi:
        tot += 1
        print('==', tema)
        for c in cambi:
            print('  ', c)
        if not PROVA:
            open(f, 'w', encoding='utf-8', newline='').write(json.dumps(d, ensure_ascii=False, separators=(',', ':')) + ('\n' if testo.endswith('\n') else ''))
print('temi', tot)
