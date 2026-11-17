# -*- coding: utf-8 -*-
"""Le scritte t('…') dei file indicati che mancano nei .po (le scritte nuove di una rifinitura, da tradurre).

  python scripts/temi/po_mancanti.py src/config/elements/accordion.js src/config/elements/_shared.js
  python scripts/temi/po_mancanti.py --json …   → stampa un dizionario pronto da riempire per po_<versione>.py

Guarda olobuild-en_US.po (le cinque lingue si aggiornano insieme con lo script della versione).
"""
import io
import json
import os
import re
import sys

sys.stdout.reconfigure(encoding='utf-8')
ROOT = os.path.normpath(os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', '..'))
PO = os.path.join(ROOT, 'languages', 'olobuild-en_US.po')


def msgids():
    testo = io.open(PO, encoding='utf-8').read()
    out = set()
    for m in re.finditer(r'^msgid "((?:[^"\\]|\\.)*)"', testo, re.M):
        out.add(m.group(1).replace('\\"', '"').replace('\\\\', '\\'))
    return out


def scritte(file):
    s = io.open(file, encoding='utf-8').read()
    out = []
    for m in re.finditer(r"""\bt\(\s*'((?:[^'\\]|\\.)*)'\s*[,)]""", s):
        out.append(m.group(1).replace("\\'", "'"))
    for m in re.finditer(r'''\bt\(\s*"((?:[^"\\]|\\.)*)"\s*[,)]''', s):
        out.append(m.group(1).replace('\\"', '"'))
    return out


def main():
    args = [a for a in sys.argv[1:] if not a.startswith('--')]
    noti = msgids()
    mancano = []
    for f in args:
        for x in scritte(os.path.join(ROOT, f) if not os.path.isabs(f) else f):
            if x not in noti and x not in mancano:
                mancano.append(x)
    if '--json' in sys.argv:
        print(json.dumps({m: ['', '', '', ''] for m in mancano}, ensure_ascii=False, indent=1))
    else:
        for m in mancano:
            print(m)
        print('—', len(mancano), 'da tradurre')


if __name__ == '__main__':
    main()
