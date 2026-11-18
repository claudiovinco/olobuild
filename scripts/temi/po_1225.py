# -*- coding: utf-8 -*-
"""Traduzioni della 1.4.1225 (18 nov 2026): i nomi dei tre cursori nuovi (cerchio, ipnosi, miccia) dei temi Capriola,
Abbaglio e Girandola. Aggiunge le voci che mancano ai .po di de_DE, en_GB, en_US, es_ES, fr_FR e ricompila i .mo.
Si può rilanciare. uso: python po_1225.py
"""
import os
import subprocess
import sys

ROOT = os.path.normpath(os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', '..'))
LANG = os.path.join(ROOT, 'languages')
MSGFMT = os.path.join(os.path.dirname(sys.executable), 'Tools', 'i18n', 'msgfmt.py')
VERSIONE = 'OLObuild 1.4.1225'

# italiano: (tedesco, inglese, spagnolo, francese)
V = {
    'Cerchio aereo (dondola, capriola sui link)': ('Luftring (schwingt, Überschlag auf Links)', 'Aerial hoop (swings, flips on links)',
                                                   'Aro aéreo (se balancea, voltereta en los enlaces)', 'Cerceau aérien (se balance, culbute sur les liens)'),
    'Ipnosi (la spirale che gira)': ('Hypnose (die drehende Spirale)', 'Hypnosis (the spinning spiral)', 'Hipnosis (la espiral que gira)',
                                     'Hypnose (la spirale qui tourne)'),
    'Miccia (faville, fuoco d’artificio al clic)': ('Zündschnur (Funken, Feuerwerk beim Klick)', 'Fuse (sparks, fireworks on click)',
                                                    'Mecha (chispas, fuegos artificiales al hacer clic)', 'Mèche (étincelles, feu d’artifice au clic)'),
}
FILE = {'de_DE': 0, 'en_GB': 1, 'en_US': 1, 'es_ES': 2, 'fr_FR': 3}


def esc(s):
    return s.replace('\\', '\\\\').replace('"', '\\"')


def main():
    for loc, i in FILE.items():
        po = os.path.join(LANG, 'olobuild-%s.po' % loc)
        testo = open(po, encoding='utf-8').read()
        nuove = []
        for it, tr in V.items():
            if '\nmsgid "%s"\n' % esc(it) in testo:
                continue
            nuove.append('\n#: %s\nmsgid "%s"\nmsgstr "%s"\n' % (VERSIONE, esc(it), esc(tr[i])))
        if nuove:
            with open(po, 'a', encoding='utf-8', newline='\n') as f:
                f.write(''.join(nuove))
        subprocess.run([sys.executable, MSGFMT, '-o', po[:-3] + '.mo', po], check=True)
        print(loc, 'voci nuove:', len(nuove))


if __name__ == '__main__':
    main()
