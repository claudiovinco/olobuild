# -*- coding: utf-8 -*-
"""Passaggi (process-steps) con «<strong>dato</strong>» in fondo alla Descrizione, che è testo semplice: il marcatore si
vedeva scritto sul sito (utente, 15 nov 2026, Orbita bassa). Il dato passa nell'etichetta accanto al numero
(«T−90 · 90 giorni prima»), come fa ora varia_passi.fila_passi. Corregge i JSON dei temi sul posto, senza rigenerarli
(la rigenerazione toglierebbe le sezioni di arricchisci2.py). Si può rilanciare: senza marcatori non cambia niente.
uso: python patch_passi_strong_2026-11-15.py"""
import glob
import json
import os
import re
import sys

sys.stdout.reconfigure(encoding='utf-8')
ROOT = os.path.normpath(os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', '..'))
FINE = re.compile(r'\s*<strong>(.*?)</strong>\s*$')
TAG = re.compile(r'</?(strong|em|b|i)>')
toccati = {}


def walk(n, tema):
    st = n.get('settings') or {}
    if n.get('type') == 'process-steps':
        for it in st.get('items') or []:
            d = str(it.get('description') or '')
            m = FINE.search(d)
            if m:
                dato = TAG.sub('', m.group(1)).strip()
                it['description'] = d[:m.start()].rstrip()
                it['number'] = ' · '.join(x for x in (str(it.get('number') or '').strip(), dato) if x)
                toccati[tema] = toccati.get(tema, 0) + 1
            elif TAG.search(d):
                it['description'] = TAG.sub('', d)
                toccati[tema] = toccati.get(tema, 0) + 1
        # l'etichetta lunga non sta nel tondo: come linee_grafiche, resta scritta
        if st.get('number_style') in ('circle', 'outline') and max([len(str(it.get('number') or '')) for it in (st.get('items') or [])] or [0]) > 3:
            st['number_style'] = 'plain'
            toccati[tema] = toccati.get(tema, 0) + 1
    for c in n.get('children') or []:
        walk(c, tema)


for f in sorted(glob.glob(os.path.join(ROOT, 'assets', 'data', 'themes', '*', '*.json'))):
    if f.endswith('theme.json'):
        continue
    tema = os.path.basename(os.path.dirname(f))
    prima = sum(toccati.values())
    d = json.load(open(f, encoding='utf-8'))
    for n in (d['content'] if isinstance(d, dict) else d):
        walk(n, tema)
    if sum(toccati.values()) != prima:
        with open(f, 'w', encoding='utf-8', newline='\n') as fh:
            fh.write(json.dumps(d, ensure_ascii=False, separators=(',', ':')))
        print('corretto', os.path.relpath(f, ROOT))
print('voci corrette:', toccati)
