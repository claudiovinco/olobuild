# -*- coding: utf-8 -*-
"""Traduzioni della 1.4.1087 (16 nov 2026): Grafico, la legenda e il suo padding (episodio 26).
Aggiunge le voci che mancano ai .po di de_DE, en_GB, en_US, es_ES, fr_FR e ricompila i .mo. Si può rilanciare.
uso: python po_1087.py
"""
import os
import subprocess
import sys

ROOT = os.path.normpath(os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', '..'))
LANG = os.path.join(ROOT, 'languages')
MSGFMT = os.path.join(os.path.dirname(sys.executable), 'Tools', 'i18n', 'msgfmt.py')
VERSIONE = 'OLObuild 1.4.1087'

# italiano: (tedesco, inglese, spagnolo, francese)
V = {
    'Lo spazio attorno a ogni voce della legenda.': ('Der Abstand um jeden Eintrag der Legende.', 'The space around each legend item.', 'El espacio alrededor de cada elemento de la leyenda.', 'L’espace autour de chaque élément de la légende.'),
    'Torte, ciambelle e aree polari elencano i dati coi loro colori. Barre, linee e radar mostrano il nome della serie: senza nome la legenda non compare.': ('Torten-, Ring- und Polardiagramme listen die Werte mit ihren Farben auf. Balken, Linien und Radar zeigen den Namen der Reihe: ohne Namen erscheint keine Legende.', 'Pie, doughnut and polar area charts list the data with their colours. Bars, lines and radar show the series name: without a name there is no legend.', 'Tartas, rosquillas y áreas polares enumeran los datos con sus colores. Barras, líneas y radar muestran el nombre de la serie: sin nombre no aparece la leyenda.', 'Secteurs, anneaux et aires polaires listent les données avec leurs couleurs. Barres, lignes et radar affichent le nom de la série : sans nom, pas de légende.'),
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
