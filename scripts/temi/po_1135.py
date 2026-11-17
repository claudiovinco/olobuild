# -*- coding: utf-8 -*-
"""Traduzioni della 1.4.1135 (16 nov 2026): Cartellone in una colonna stretta, il genere da un menu (episodio 45).
Aggiunge le voci che mancano ai .po di de_DE, en_GB, en_US, es_ES, fr_FR e ricompila i .mo. Si può rilanciare.
uso: python po_1097.py
"""
import os
import subprocess
import sys

ROOT = os.path.normpath(os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', '..'))
LANG = os.path.join(ROOT, 'languages')
MSGFMT = os.path.join(os.path.dirname(sys.executable), 'Tools', 'i18n', 'msgfmt.py')
VERSIONE = 'OLObuild 1.4.1135'

# italiano: (tedesco, inglese, spagnolo, francese)
V = {
    'Diventa il filtro e prende il suo colore. I generi si aggiungono nella sezione Generi.': ('Wird zum Filter und erhält seine Farbe. Genres werden im Abschnitt Genres hinzugefügt.', 'It becomes the filter and takes its colour. Genres are added in the Genres section.', 'Se convierte en el filtro y toma su color. Los géneros se añaden en la sección Géneros.', 'Il devient le filtre et prend sa couleur. Les genres s’ajoutent dans la section Genres.'),
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
