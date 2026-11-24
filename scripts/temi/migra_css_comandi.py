# -*- coding: utf-8 -*-
"""I JSON dei temi: il CSS su misura dei livelli dello Slider Pro passa nei comandi (css_comandi.converti, 1.4.1430).

Cambia solo i livelli degli Slider Pro, scrive i JSON nello stesso formato (ensure_ascii=False, separatori compatti,
senza a capo finale). Mai rigenerare i temi coi gen_*.py per questo: i JSON hanno ritocchi fatti dopo.

    python scripts/temi/migra_css_comandi.py          → mostra cosa cambierebbe
    python scripts/temi/migra_css_comandi.py scrivi   → lo scrive
"""
import glob, io, json, os, sys
sys.stdout.reconfigure(encoding='utf-8')
QUI = os.path.dirname(os.path.abspath(__file__))
sys.path.insert(0, QUI)
from css_comandi import converti, _dichiarazioni  # noqa: E402

SCRIVI = len(sys.argv) > 1 and sys.argv[1] == 'scrivi'
prima = dopo = livelli = file_cambiati = 0
resto = {}
for p in sorted(glob.glob(os.path.join(QUI, '..', '..', 'assets', 'data', 'themes', '*', '*.json'))):
    raw = io.open(p, encoding='utf-8').read()
    try:
        d = json.loads(raw)
    except Exception:
        continue
    if json.dumps(d, ensure_ascii=False, separators=(',', ':')) != raw.rstrip('\n'):
        continue  # un file in un altro formato: non lo tocco
    cambiato = [False]

    def giro(o):
        global prima, dopo, livelli
        if isinstance(o, dict):
            if o.get('type') == 'proslider' and isinstance(o.get('settings'), dict):
                s = o['settings']
                for ls in [sl.get('layers') or [] for sl in s.get('slides') or []] + [s.get('globalLayers') or []]:
                    for l in ls:
                        css = str(l.get('customCSS') or '').strip()
                        if not css:
                            continue
                        n0 = len(_dichiarazioni(css))
                        converti(l)
                        n1 = len(_dichiarazioni(l.get('customCSS', '')))
                        prima += n0
                        dopo += n1
                        if n1 != n0:
                            livelli += 1
                            cambiato[0] = True
                        for k, v in _dichiarazioni(l.get('customCSS', '')):
                            resto[k] = resto.get(k, 0) + 1
                return
            for v in o.values():
                giro(v)
        elif isinstance(o, list):
            for x in o:
                giro(x)
    giro(d)
    if cambiato[0]:
        file_cambiati += 1
        if SCRIVI:
            io.open(p, 'w', encoding='utf-8', newline='').write(json.dumps(d, ensure_ascii=False, separators=(',', ':')))
print('dichiarazioni nel CSS su misura: %d → %d · livelli cambiati %d · file %d%s' % (prima, dopo, livelli, file_cambiati, '' if SCRIVI else ' (prova)'))
print('restano:', dict(sorted(resto.items(), key=lambda x: -x[1])))
