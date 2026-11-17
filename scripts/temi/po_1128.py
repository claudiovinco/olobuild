# -*- coding: utf-8 -*-
"""Traduzioni della 1.4.1128 (16 nov 2026): Tracciamento spedizione in una colonna stretta (episodio 41).
Aggiunge le voci che mancano ai .po di de_DE, en_GB, en_US, es_ES, fr_FR e ricompila i .mo. Si può rilanciare.
uso: python po_1097.py
"""
import os
import subprocess
import sys

ROOT = os.path.normpath(os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', '..'))
LANG = os.path.join(ROOT, 'languages')
MSGFMT = os.path.join(os.path.dirname(sys.executable), 'Tools', 'i18n', 'msgfmt.py')
VERSIONE = 'OLObuild 1.4.1128'

# italiano: (tedesco, inglese, spagnolo, francese)
V = {
    'In riga: quando la tile è stretta (il telefono, una colonna stretta) il pulsante va comunque sotto, largo quanto il campo.': ('In einer Reihe: Wenn die Kachel schmal ist (Smartphone, schmale Spalte), rückt die Schaltfläche trotzdem darunter, so breit wie das Feld.', 'In a row: when the tile is narrow (a phone, a narrow column) the button still moves below, as wide as the field.', 'En fila: cuando la tile es estrecha (el móvil, una columna estrecha) el botón pasa igualmente debajo, tan ancho como el campo.', 'En ligne : quand la tuile est étroite (le téléphone, une colonne étroite) le bouton passe quand même dessous, aussi large que le champ.'),
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
