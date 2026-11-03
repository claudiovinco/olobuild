# -*- coding: utf-8 -*-
"""Censimento dei temi di una tornata: di che tile sono fatti e cosa hanno chiesto di nuovo a OLObuild.

Per ogni tema: la trovata (la «zona» di theme.json), i tipi di tile usati con quanti ALTRI temi li usano già (una tile
usata da pochi temi si segnala come rara, una ritirata dalla palette come nascosta, una che a HEAD non esiste come nata
in questa tornata), e le opzioni nuove della versione indicata che il tema usa davvero. Controlla anche che nei
template non ci sia CSS personalizzato (custom_css) né tile HTML, e raccoglie le proprietà scritte nel «CSS del livello»
dei livelli dello Slider Pro.

uso: python censimento_tornata.py [--md] [id …]   (senza id: i dieci temi del 3 nov 2026, seconda tornata)
"""
import json
import os
import re
import subprocess
import sys

QUI = os.path.dirname(os.path.abspath(__file__))
ROOT = os.path.normpath(os.path.join(QUI, '..', '..'))
THEMES = os.path.join(ROOT, 'assets', 'data', 'themes')
ELEMENTI = os.path.join(ROOT, 'src', 'config', 'elements')

TORNATA = ['raspa', 'stender', 'fuori-registro', 'luce-rossa', 'lastra', 'sbieco', 'falcata', 'combinazione', 'ventaglio', 'elastan']
VERSIONE = '1.4.959'
CURSORI_NUOVI = ['raspa', 'gruccia', 'registro', 'lanterna', 'scanner', 'forbici', 'flash', 'quadrante', 'ventaglio', 'molla']
STRUTTURA = ('section', 'row', 'column')
RARA = 3  # sotto questo numero di altri temi la tile si segnala come rara


def nodi(path):
    with open(path, encoding='utf-8') as fh:
        d = json.load(fh)
    return (d.get('content') or []) if isinstance(d, dict) else d


def cammina(lista, fn):
    for n in lista:
        if isinstance(n, dict):
            fn(n)
            cammina(n.get('children') or [], fn)


def tile_del_tema(tid):
    usate = {}
    d = os.path.join(THEMES, tid)
    for f in sorted(os.listdir(d)):
        if f.endswith('.json') and f not in ('theme.json', 'tiles-manifest.json'):
            def conta(n):
                if n.get('type') not in STRUTTURA:
                    usate[n['type']] = usate.get(n['type'], 0) + 1
            cammina(nodi(os.path.join(d, f)), conta)
    return usate


def diffusione(esclusi):
    """Quanti temi (fuori da `esclusi`) usano ogni tipo di tile."""
    quanti = {}
    for tid in sorted(os.listdir(THEMES)):
        if tid in esclusi or not os.path.isdir(os.path.join(THEMES, tid)):
            continue
        try:
            for tipo in tile_del_tema(tid):
                quanti[tipo] = quanti.get(tipo, 0) + 1
        except (ValueError, KeyError):
            continue
    return quanti


def nascosta(tipo):
    p = os.path.join(ELEMENTI, tipo + '.js')
    return os.path.exists(p) and re.search(r'^\s*hidden:\s*true', open(p, encoding='utf-8').read(), re.M) is not None


def esisteva_a_head(tipo):
    r = subprocess.run(['git', '-C', ROOT, 'cat-file', '-e', 'HEAD:src/config/elements/%s.js' % tipo], capture_output=True)
    return r.returncode == 0


def novita(tid):
    """Le opzioni nuove della versione che il tema usa, e il CSS dei livelli dello Slider Pro."""
    trovate, css, problemi = set(), set(), []
    d = os.path.join(THEMES, tid)
    for f in sorted(os.listdir(d)):
        if not f.endswith('.json') or f in ('theme.json', 'tiles-manifest.json'):
            continue

        def guarda(n):
            s = n.get('settings') or {}
            if 'custom_css' in s or 'custom_css' in (n.get('advanced') or {}):
                problemi.append('%s: custom_css in %s' % (f, n.get('id')))
            if n.get('type') == 'html':
                problemi.append('%s: tile HTML %s' % (f, n.get('id')))
            if n.get('type') == 'scrollscrub' and s.get('swing'):
                trovate.add('ScrollScrub · Dondolano scorrendo')
            if n.get('type') == 'proslider':
                for sl in s.get('slides') or []:
                    if (sl.get('background') or {}).get('videoScroll'):
                        trovate.add('Slider Pro · video che segue lo scorrimento')
                    for L in (sl.get('layers') or []) + (s.get('globalLayers') or []):
                        for t in ((L.get('scrollMove') or {}).get('tappe') or []):
                            if 'sx' in t or 'sy' in t:
                                trovate.add('Slider Pro · allunga in orizzontale/verticale')
                        for k in ('backdropContrast', 'backdropSaturate', 'backdropInvert', 'backdropHueRotate'):
                            if k in L and L[k] not in (0, 100):
                                trovate.add('Slider Pro · filtri dietro il livello (%s)' % k[8:].lower())
                        for decl in (L.get('customCSS') or '').split(';'):
                            if ':' in decl:
                                css.add(decl.split(':')[0].strip())
        cammina(nodi(os.path.join(d, f)), guarda)
    tema = json.load(open(os.path.join(d, 'theme.json'), encoding='utf-8'))
    stile = (tema.get('cursor') or {}).get('style', '')
    if stile in CURSORI_NUOVI:
        trovate.add('Cursore «%s»' % stile)
    return tema, sorted(trovate), sorted(css), problemi


def main():
    md = '--md' in sys.argv
    temi = [a for a in sys.argv[1:] if not a.startswith('--')] or TORNATA
    sys.stdout.reconfigure(encoding='utf-8')
    quanti = diffusione(set(TORNATA) | set(temi))
    righe, segnalate, tutte = [], [], set()
    for tid in temi:
        usate = tile_del_tema(tid)
        tema, nuove, css, problemi = novita(tid)
        voci = []
        for tipo in sorted(usate):
            tutte.add(tipo)
            n = quanti.get(tipo, 0)
            nota = ''
            if not esisteva_a_head(tipo):
                nota = 'NATA IN QUESTA TORNATA'
            elif nascosta(tipo):
                nota = 'ritirata dalla palette'
            elif n < RARA:
                nota = 'rara'
            if nota:
                segnalate.append('%s: %s (%s, %d altri temi)' % (tid, tipo, nota, n))
            voci.append('%s (%d)%s' % (tipo, n, ' ⚠ ' + nota if nota else ''))
        righe.append((tema.get('name', tid), tema.get('zone', ''), voci, nuove, css, problemi))
    if md:
        print('| Tema | Trovata | Tile usate (in quanti altri temi) | Aggiunte a OLObuild %s |' % VERSIONE)
        print('|---|---|---|---|')
        for nome, zona, voci, nuove, css, problemi in righe:
            print('| %s | %s | %s | %s |' % (nome, zona, ', '.join(voci), '; '.join(nuove) or '—'))
    else:
        for nome, zona, voci, nuove, css, problemi in righe:
            print('== %s — %s' % (nome, zona))
            print('   tile (%d): %s' % (len(voci), ', '.join(voci)))
            print('   aggiunte %s: %s' % (VERSIONE, '; '.join(nuove) or 'nessuna'))
            print('   CSS dei livelli: %s' % (', '.join(css) or 'nessuno'))
            for p in problemi:
                print('   PROBLEMA: ' + p)
    print()
    print('Tipi di tile usati in tutto: %d. Segnalate: %d.' % (len(tutte), len(segnalate)))
    for s in segnalate:
        print('  - ' + s)
    if any(r[5] for r in righe):
        sys.exit(1)


if __name__ == '__main__':
    main()
