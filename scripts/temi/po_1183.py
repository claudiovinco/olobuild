# -*- coding: utf-8 -*-
"""Traduzioni della 1.4.1183 (17 nov 2026): Mappa del mondo, il «Colore del mare».
Aggiunge le voci che mancano ai .po di de_DE, en_GB, en_US, es_ES, fr_FR e ricompila i .mo. Si può rilanciare.
uso: python po_1183.py
"""
import os
import subprocess
import sys

ROOT = os.path.normpath(os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', '..'))
LANG = os.path.join(ROOT, 'languages')
MSGFMT = os.path.join(os.path.dirname(sys.executable), 'Tools', 'i18n', 'msgfmt.py')
VERSIONE = 'OLObuild 1.4.1183'

# italiano: (tedesco, inglese, spagnolo, francese)
V = {
    'Colore del mare': ('Farbe des Meeres', 'Sea colour', 'Color del mar', 'Couleur de la mer'),
    'Il fondo della cartina, per esempio un azzurro. Vuoto: niente mare, la cartina sta sullo sfondo della pagina.': (
        'Der Hintergrund der Karte, zum Beispiel ein Hellblau. Leer: kein Meer, die Karte liegt auf dem Hintergrund der Seite.',
        'The background of the map, for example a light blue. Empty: no sea, the map sits on the page background.',
        'El fondo del mapa, por ejemplo un azul claro. Vacío: sin mar, el mapa queda sobre el fondo de la página.',
        'Le fond de la carte, par exemple un bleu clair. Vide : pas de mer, la carte repose sur le fond de la page.'),
    'Vuoto: trasparenti, si vede lo sfondo; col mare colorato diventano chiari.': (
        'Leer: transparent, man sieht den Hintergrund; mit farbigem Meer werden sie hell.',
        'Empty: transparent, the background shows through; with a coloured sea they turn light.',
        'Vacío: transparentes, se ve el fondo; con el mar coloreado se vuelven claros.',
        'Vide : transparents, on voit le fond ; avec une mer colorée ils deviennent clairs.'),
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
