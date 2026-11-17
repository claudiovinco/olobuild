# -*- coding: utf-8 -*-
"""Traduzioni della 1.4.1111 (16 nov 2026): Calcolatore a slider, titolo con l'editor, lista facoltativa, zone dello Stile (episodio 35).
Aggiunge le voci che mancano ai .po di de_DE, en_GB, en_US, es_ES, fr_FR e ricompila i .mo. Si può rilanciare.
uso: python po_1097.py
"""
import os
import subprocess
import sys

ROOT = os.path.normpath(os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', '..'))
LANG = os.path.join(ROOT, 'languages')
MSGFMT = os.path.join(os.path.dirname(sys.executable), 'Tools', 'i18n', 'msgfmt.py')
VERSIONE = 'OLObuild 1.4.1111'

# italiano: (tedesco, inglese, spagnolo, francese)
V = {
    'Le parole in corsivo prendono il colore d’accento.': ('Kursive Wörter nehmen die Akzentfarbe an.', 'Words in italics take the accent colour.', 'Las palabras en cursiva toman el color de acento.', 'Les mots en italique prennent la couleur d’accent.'),
    'Fattore': ('Faktor', 'Factor', 'Factor', 'Facteur'),
    'Lista che scala': ('Mitwachsende Liste', 'Scaling list', 'Lista que escala', 'Liste qui s’adapte'),
    'Il colore di cursore, occhiello, parole in corsivo del titolo e risultato.': ('Die Farbe von Regler, Dachzeile, kursiven Wörtern des Titels und Ergebnis.', 'The colour of the slider, eyebrow, italic words in the title and result.', 'El color del cursor, antetítulo, palabras en cursiva del título y resultado.', 'La couleur du curseur, du surtitre, des mots en italique du titre et du résultat.'),
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
