# -*- coding: utf-8 -*-
"""Dopo aver rigenerato un tema col generatore, rimette nella cartella del tema i JSON di HEAD e vi innesta SOLO le
posizioni dei pulsanti affiancati dello Slider Pro (x e x di notebook/tablet/telefono) prese dal tema rigenerato:
il resto della rigenerazione (testate ritoccate a mano, kit cambiato nel frattempo) non passa.
Con --anche id,id porta anche i valori del telefono (responsive.mobile) di quei livelli.
uso: python innesta_pulsanti.py [--anche id,id] tema [tema …]"""
import glob
import json
import os
import subprocess
import sys

ROOT = 'T:/TECNICA/olobuild'
num = lambda v: isinstance(v, (int, float))
FORMATI = ('notebook', 'tablet', 'mobile')


def gruppi(o, out):
    if isinstance(o, dict):
        if o.get('type') == 'proslider':
            st = o.get('settings') or {}
            for s in st.get('slides') or []:
                out.append(s.get('layers') or [])
            out.append(st.get('globalLayers') or [])
        for v in o.values():
            gruppi(v, out)
    elif isinstance(o, list):
        for v in o:
            gruppi(v, out)
    return out


def coppie(L):
    bt = [x for x in L if x.get('type') == 'button' and num(x.get('x')) and num(x.get('y'))]
    ids = set()
    for a in bt:
        for b in bt:
            if b is not a and abs(a['y'] - b['y']) <= 3 and 0 < b['x'] - a['x'] <= 30:
                ids |= {a.get('id'), b.get('id')}
    return ids


args = sys.argv[1:]
ANCHE = set()
if args and args[0] == '--anche':
    ANCHE = set(args[1].split(','))
    args = args[2:]
for tid in args:
    for f in sorted(glob.glob('%s/assets/data/themes/%s/*.json' % (ROOT, tid))):
        rel = os.path.relpath(f, ROOT).replace(os.sep, '/')
        r = subprocess.run(['git', '-C', ROOT, 'show', 'HEAD:' + rel], capture_output=True, encoding='utf-8')
        if r.returncode:
            continue
        testo = r.stdout
        vecchio = json.loads(testo)
        assert json.dumps(vecchio, ensure_ascii=False, separators=(',', ':')) == testo, 'il formato non torna: ' + rel
        nuovo = json.load(open(f, encoding='utf-8'))
        rigen = {L.get('id'): L for g in gruppi(nuovo, []) for L in g}
        cambi = []
        for g in gruppi(vecchio, []):
            for L in g:
                if L.get('id') in ANCHE and L['id'] in rigen:
                    rm = (rigen[L['id']].get('responsive') or {}).get('mobile')
                    if rm != (L.get('responsive') or {}).get('mobile'):
                        cambi.append('%s.mobile %s→%s' % (L['id'], (L.get('responsive') or {}).get('mobile'), rm))
                        L.setdefault('responsive', {})['mobile'] = rm
                if L.get('id') not in coppie(g) or L.get('id') not in rigen:
                    continue
                R = rigen[L['id']]
                if R.get('x') != L.get('x'):
                    cambi.append('%s.x %s→%s' % (L['id'], L.get('x'), R.get('x')))
                    L['x'] = R['x']
                for k in FORMATI:
                    rx = ((R.get('responsive') or {}).get(k) or {}).get('x')
                    lx = ((L.get('responsive') or {}).get(k) or {}).get('x')
                    if rx != lx:
                        cambi.append('%s.%s.x %s→%s' % (L['id'], k, lx, rx))
                        ov = L.setdefault('responsive', {}).setdefault(k, {})
                        if rx is None:
                            ov.pop('x', None)
                        else:
                            ov['x'] = rx
        with open(f, 'w', encoding='utf-8', newline='\n') as fh:
            fh.write(json.dumps(vecchio, ensure_ascii=False, separators=(',', ':')))
        for c in cambi:
            print('%-16s %-14s %s' % (tid, os.path.basename(f), c))
