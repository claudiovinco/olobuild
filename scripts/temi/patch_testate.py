# -*- coding: utf-8 -*-
"""Passa testate.applica() sul Mega Menu di header.json dei temi indicati (o di tutti), nel formato in cui il file era.

uso: python patch_testate.py [tema ...]      senza temi: tutti quelli che hanno un header.json
I negozi del kit (negozio.py) rifanno la testata da sé quando si rigenerano: qui ricevono solo il menu mobile."""
import json
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
import testate  # noqa: E402


def menu(o):
    if isinstance(o, dict):
        if o.get('type') == 'megamenu':
            yield o
        for v in o.values():
            yield from menu(v)
    elif isinstance(o, list):
        for v in o:
            yield from menu(v)


def patch(tid):
    f = os.path.join(testate.TEMI, tid, 'header.json')
    if not os.path.exists(f):
        return 0
    testo = open(f, encoding='utf-8').read()
    dati = json.loads(testo)
    n = 0
    for m in menu(dati):
        testate.applica(tid, m.setdefault('settings', {}))
        n += 1
    if n:
        compatto = '": ' not in testo[:400]
        ascii_ = '\\u' in testo and all(ord(ch) < 128 for ch in testo)
        nuovo = json.dumps(dati, ensure_ascii=ascii_, separators=(',', ':') if compatto else None)
        if testo.endswith('\n'):
            nuovo += '\n'
        if nuovo != testo:
            open(f, 'w', encoding='utf-8', newline='').write(nuovo)
    return n


if __name__ == '__main__':
    temi = sys.argv[1:] or sorted(d for d in os.listdir(testate.TEMI) if os.path.isdir(os.path.join(testate.TEMI, d)))
    for t in temi:
        n = patch(t)
        print(t, n if n else '-')
