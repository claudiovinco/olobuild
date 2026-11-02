# -*- coding: utf-8 -*-
"""Passa numeri.applica() sulla home dei temi indicati (o di tutti quelli in numeri.NUMERI), nel formato in cui il file
era. uso: python patch_numeri.py [tema ...]"""
import json
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
import numeri  # noqa: E402

TEMI = 'T:/TECNICA/olobuild/assets/data/themes'


def patch(tid):
    f = os.path.join(TEMI, tid, 'home.json')
    if not os.path.exists(f):
        return None
    testo = open(f, encoding='utf-8').read()
    dati = json.loads(testo)
    sezioni = dati['content'] if isinstance(dati, dict) else dati
    n = numeri.applica(tid, sezioni)
    compatto = '": ' not in testo[:400]
    ascii_ = '\\u' in testo and all(ord(ch) < 128 for ch in testo)
    nuovo = json.dumps(dati, ensure_ascii=ascii_, separators=(',', ':') if compatto else None)
    if testo.endswith('\n'):
        nuovo += '\n'
    if nuovo != testo:
        open(f, 'w', encoding='utf-8', newline='').write(nuovo)
    return n


if __name__ == '__main__':
    sys.stdout.reconfigure(encoding='utf-8')
    for t in (sys.argv[1:] or sorted(numeri.NUMERI)):
        a = numeri.NUMERI.get(t)
        print('%-20s %s → %s' % (t, a if isinstance(a, (str, list)) else '%s, %s' % a, patch(t)))
