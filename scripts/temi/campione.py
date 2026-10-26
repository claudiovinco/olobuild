# -*- coding: utf-8 -*-
"""Mostra le impostazioni di una tile come la usano i temi: uso python campione.py <tipo> [tema] [n]
(senza tema: il primo tema che la usa; n = quante occorrenze stampare)."""
import json, os, sys
ROOT = os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', '..', 'assets', 'data', 'themes')
tipo = sys.argv[1]
tema = sys.argv[2] if len(sys.argv) > 2 and sys.argv[2] != '-' else None
quanti = int(sys.argv[3]) if len(sys.argv) > 3 else 1
sys.stdout.reconfigure(encoding='utf-8')
trovati = 0
for t in sorted(os.listdir(ROOT)):
    if tema and t != tema:
        continue
    for f in sorted(os.listdir(os.path.join(ROOT, t))):
        if not f.endswith('.json') or f == 'theme.json':
            continue
        def walk(n):
            global trovati
            if trovati >= quanti:
                return
            if n['type'] == tipo:
                trovati += 1
                print('== %s/%s %s' % (t, f, n['id']))
                print(json.dumps({'settings': n['settings'], 'style': n.get('style'), 'advanced': n.get('advanced')}, ensure_ascii=False))
            for c in n.get('children') or []:
                walk(c)
        for n in json.load(open(os.path.join(ROOT, t, f), encoding='utf-8')):
            walk(n)
