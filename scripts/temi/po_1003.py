# -*- coding: utf-8 -*-
"""Traduzioni della 1.4.1003 (13 nov 2026): la colonna «Prima quando le colonne vanno in pila».
Aggiunge le voci che mancano ai .po di de_DE, en_GB, en_US, es_ES, fr_FR e ricompila i .mo. Si può rilanciare.
uso: python po_1003.py
"""
import os
import subprocess
import sys

ROOT = os.path.normpath(os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', '..'))
LANG = os.path.join(ROOT, 'languages')
MSGFMT = os.path.join(os.path.dirname(sys.executable), 'Tools', 'i18n', 'msgfmt.py')

# italiano: (tedesco, inglese, spagnolo, francese)
V = {
    'Prima quando le colonne vanno in pila': (
        'Zuerst, wenn die Spalten untereinander stehen', 'First when the columns stack', 'Primero cuando las columnas se apilan',
        'En premier quand les colonnes s’empilent'),
    'Quando le colonne vanno una sotto l’altra (sul telefono, e sul tablet se la colonna non ha una larghezza per il tablet), questa viene per prima. Utile per un titolo o un testo che sul computer sta a destra.': (
        'Wenn die Spalten untereinander stehen (auf dem Handy, und auf dem Tablet, wenn die Spalte keine Tablet-Breite hat), kommt diese zuerst. Nützlich für einen Titel oder Text, der auf dem Computer rechts steht.',
        'When the columns stack one below the other (on phones, and on tablets if the column has no tablet width), this one comes first. Useful for a title or text that sits on the right on desktop.',
        'Cuando las columnas se colocan una debajo de otra (en el móvil, y en la tableta si la columna no tiene un ancho para tableta), esta va primero. Útil para un título o un texto que en el ordenador está a la derecha.',
        'Quand les colonnes passent l’une sous l’autre (sur téléphone, et sur tablette si la colonne n’a pas de largeur pour tablette), celle-ci vient en premier. Utile pour un titre ou un texte placé à droite sur ordinateur.'),
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
            nuove.append('\n#: OLObuild 1.4.1003\nmsgid "%s"\nmsgstr "%s"\n' % (esc(it), esc(tr[i])))
        if nuove:
            with open(po, 'a', encoding='utf-8', newline='\n') as f:
                f.write(''.join(nuove))
        subprocess.run([sys.executable, MSGFMT, '-o', po[:-3] + '.mo', po], check=True)
        print(loc, 'voci nuove:', len(nuove))


if __name__ == '__main__':
    main()
