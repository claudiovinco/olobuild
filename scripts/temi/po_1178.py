# -*- coding: utf-8 -*-
"""Traduzioni della 1.4.1178 (17 nov 2026): Mappa del mondo, l'«Inquadratura» per regione (Europa, Nord America…).
Aggiunge le voci che mancano ai .po di de_DE, en_GB, en_US, es_ES, fr_FR e ricompila i .mo. Si può rilanciare.
uso: python po_1178.py
"""
import os
import subprocess
import sys

ROOT = os.path.normpath(os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', '..'))
LANG = os.path.join(ROOT, 'languages')
MSGFMT = os.path.join(os.path.dirname(sys.executable), 'Tools', 'i18n', 'msgfmt.py')
VERSIONE = 'OLObuild 1.4.1178'

# italiano: (tedesco, inglese, spagnolo, francese)
V = {
    'Europa': ('Europa', 'Europe', 'Europa', 'Europe'),
    'Nord America': ('Nordamerika', 'North America', 'América del Norte', 'Amérique du Nord'),
    'Sud America': ('Südamerika', 'South America', 'América del Sur', 'Amérique du Sud'),
    'Africa': ('Afrika', 'Africa', 'África', 'Afrique'),
    'Medio Oriente': ('Naher Osten', 'Middle East', 'Oriente Medio', 'Moyen-Orient'),
    'Asia': ('Asien', 'Asia', 'Asia', 'Asie'),
    'Oceania': ('Ozeanien', 'Oceania', 'Oceanía', 'Océanie'),
    'Con «Solo i paesi colorati» la cartina si stringe attorno agli stati colorati, con un po’ di margine: per chi ha cantato o lavorato in pochi paesi vicini. Con una regione si stringe su quella parte di mondo: gli stati colorati fuori restano fuori vista, ma la legenda li conta.': (
        'Mit „Nur die farbigen Länder“ zieht sich die Karte mit etwas Rand um die farbigen Länder zusammen: für alle, die in wenigen benachbarten Ländern gesungen oder gearbeitet haben. Mit einer Region zieht sie sich auf diesen Teil der Welt zusammen: Farbige Länder außerhalb bleiben außer Sicht, die Legende zählt sie aber mit.',
        'With “Only the coloured countries” the map closes in on the coloured countries, with a little margin: for anyone who has sung or worked in a few neighbouring countries. With a region it closes in on that part of the world: coloured countries outside it stay out of view, but the legend still counts them.',
        'Con «Solo los países coloreados» el mapa se ciñe a los países coloreados, con un poco de margen: para quien ha cantado o trabajado en pocos países vecinos. Con una región se ciñe a esa parte del mundo: los países coloreados que quedan fuera no se ven, pero la leyenda los cuenta.',
        'Avec « Seulement les pays colorés » la carte se resserre autour des pays colorés, avec un peu de marge : pour qui a chanté ou travaillé dans quelques pays voisins. Avec une région elle se resserre sur cette partie du monde : les pays colorés en dehors restent hors de vue, mais la légende les compte.'),
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
