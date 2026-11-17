# -*- coding: utf-8 -*-
"""Traduzioni della 1.4.1163 (17 nov 2026): «Pubblicato» chiede conferma prima di rimettere la pagina in bozza.
Aggiunge le voci che mancano ai .po di de_DE, en_GB, en_US, es_ES, fr_FR e ricompila i .mo. Si può rilanciare.
uso: python po_1163.py
"""
import os
import subprocess
import sys

ROOT = os.path.normpath(os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', '..'))
LANG = os.path.join(ROOT, 'languages')
MSGFMT = os.path.join(os.path.dirname(sys.executable), 'Tools', 'i18n', 'msgfmt.py')
VERSIONE = 'OLObuild 1.4.1163'

# italiano: (tedesco, inglese, spagnolo, francese)
V = {
    'Rimettere la pagina in bozza? Il sito non la mostrerà più finché non la pubblichi di nuovo.': ('Die Seite wieder zum Entwurf machen? Die Website zeigt sie nicht mehr, bis du sie erneut veröffentlichst.', 'Turn the page back into a draft? The site will stop showing it until you publish it again.', '¿Volver a poner la página en borrador? El sitio dejará de mostrarla hasta que la publiques de nuevo.', 'Remettre la page en brouillon ? Le site ne l’affichera plus tant que vous ne la publierez pas à nouveau.'),
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
