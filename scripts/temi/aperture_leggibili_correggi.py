# -*- coding: utf-8 -*-
"""Corregge nei dati dei temi l'apertura (Slider Pro) al telefono, dalle misure di aperture_leggibili.py:
1. testi sotto la testata: il gruppo di testi e pulsanti della metà alta della slide scende tutto insieme quanto basta
   (12 px sotto la testata), così le distanze fra occhiello, titolo e sottotitolo restano quelle;
2. testi piccoli: paragrafi (≥ 40 caratteri) almeno 14 px reali, gli altri testi e i pulsanti almeno 11 px.
Scrive solo `responsive.mobile` (y, fontSize) dei livelli; il computer, il notebook e il tablet non cambiano.
uso: python correggi_aperture.py misure.json [--prova] [tema …]"""
import glob
import json
import math
import os
import sys

MIS = json.load(open(sys.argv[1], encoding='utf-8'))
PROVA = '--prova' in sys.argv
BP = 'tablet' if '--tablet' in sys.argv else 'mobile'
SOLO_TESTATA = '--solo-testata' in sys.argv
SOLO = set(a for a in sys.argv[2:] if not a.startswith('--'))
RADICE = 'T:/TECNICA/olobuild/assets/data/themes'
CATENA = ('mobile', 'tablet', 'notebook')
SALTA = {'sonnambulo', 'scroscio'}   # la «Z» del sonno attraversa la testata apposta; Scroscio è già corretto a mano


def efficace(l, k, da=None):
    for bp in CATENA[CATENA.index(da or BP):]:
        v = ((l.get('responsive') or {}).get(bp) or {}).get(k)
        if v is not None and v != '':
            return v
    return l.get(k)


def scrivi(l, k, v):
    """Il valore per lo schermo che si corregge; sul tablet, il telefono che lo ereditava tiene quello di prima."""
    r = l.setdefault('responsive', {})
    if BP == 'tablet' and k not in (r.get('mobile') or {}):
        prima = efficace(l, k, 'mobile')
        if prima is not None:
            r.setdefault('mobile', {})[k] = prima
    r.setdefault(BP, {})[k] = v


def livelli(dati):
    """id → livello, per tutti gli slider della pagina."""
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


def apri(path):
    testo = open(path, encoding='utf-8').read()
    d = json.loads(testo)
    for modo in ({'separators': (',', ':')}, {'indent': 2}, {'indent': 1}, {'indent': '\t'}):
        if json.dumps(d, ensure_ascii=False, **modo) == testo.rstrip('\n'):
            return d, modo, testo.endswith('\n')
    return d, None, False


tot = {}
for tema, v in sorted(MIS.items()):
    if (SOLO and tema not in SOLO) or tema in SALTA or 'misure' not in v or v['misure'].get('nessuno'):
        continue
    m = v['misure']
    ch, H = m['ch'], m.get('testata') or 0
    file_tema = sorted(glob.glob(os.path.join(RADICE, tema, '*.json')))
    if not file_tema:
        print(tema, 'non è un tema di OLObuild (cartella mancante)')
        continue
    aperti = {}
    for f in file_tema:
        if os.path.basename(f) == 'theme.json':
            continue
        try:
            d, modo, acapo = apri(f)
        except Exception:
            continue
        L = livelli(d)
        if L:
            aperti[f] = (d, modo, acapo, L)
    def trova(lid):
        for f, (d, modo, acapo, L) in aperti.items():
            if lid in L:
                return f, L[lid]
        return None, None
    cambi = []
    for si, liv in enumerate(m['slides']):
        vivi = [l for l in liv if l['b'] > 0 and l['y'] < ch and l.get('id')]
        # 1 · la testata
        # il primo gruppo dall'alto: i livelli in fila finché lo stacco fra uno e il successivo resta sotto il 12%
        # dell'altezza (in Ciak le scritte del ciak disegnato a metà slide non fanno parte del gruppo)
        alto = []
        for l in sorted(vivi, key=lambda z: z['y']):
            if alto and l['y'] - max(z['y'] for z in alto) > ch * 0.12:
                break
            alto.append(l)
        if H and alto and min(l['y'] for l in alto) < H + 4:
            delta = (H + 12 - min(l['y'] for l in alto)) / ch * 100
            for l in alto:
                f, dl = trova(l['id'])
                if not dl:
                    continue
                y = float(efficace(dl, 'y') or 0)
                nuovo = round(y + delta, 1)
                scrivi(dl, 'y', nuovo)
                cambi.append((f, 's%d %s y %s → %s' % (si + 1, l['t'][:24], y, nuovo)))
        # 2 · i caratteri
        for l in (() if SOLO_TESTATA else vivi):
            f, dl = trova(l['id'])
            if not dl:
                continue
            maiuscolo = str(efficace(dl, 'textTransform') or '') == 'uppercase'
            M = 14 if (not l['btn'] and l['lung'] >= 40 and not maiuscolo) else 11
            if l['fs'] >= M - 0.05 or not l.get('sc'):
                continue
            serve = math.ceil(M / l['sc'])
            att = float(efficace(dl, 'fontSize') or l['fsd'])
            if serve > att:
                scrivi(dl, 'fontSize', serve)
                cambi.append((f, 's%d %s carattere %s → %s (%.1f → %.1f px)' % (si + 1, l['t'][:24], att, serve, l['fs'], serve * l['sc'])))
    if not cambi:
        continue
    tot[tema] = len(cambi)
    print('==', tema, 'testata', H, 'altezza', ch)
    for f, c in cambi:
        print('  ', os.path.basename(f), c)
    if PROVA:
        continue
    for f, (d, modo, acapo, L) in aperti.items():
        if not any(ff == f for ff, _ in cambi):
            continue
        if modo is None:
            print('   !! formato del file non riconosciuto, non scritto:', f)
            continue
        open(f, 'w', encoding='utf-8', newline='').write(json.dumps(d, ensure_ascii=False, **modo) + ('\n' if acapo else ''))
print('temi cambiati', len(tot), 'livelli', sum(tot.values()))
