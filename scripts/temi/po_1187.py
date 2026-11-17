# -*- coding: utf-8 -*-
"""Traduzioni della 1.4.1187 (17 nov 2026): i link dinamici — le sorgenti e i campi indirizzo del pannello dei collegamenti.
Aggiunge le voci che mancano ai .po di de_DE, en_GB, en_US, es_ES, fr_FR e ricompila i .mo. Si può rilanciare.
uso: python po_1187.py
"""
import os
import subprocess
import sys

ROOT = os.path.normpath(os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', '..'))
LANG = os.path.join(ROOT, 'languages')
MSGFMT = os.path.join(os.path.dirname(sys.executable), 'Tools', 'i18n', 'msgfmt.py')
VERSIONE = 'OLObuild 1.4.1187'

# italiano: (tedesco, inglese, spagnolo, francese)
V = {
    'Link della categoria': ('Link zur Kategorie', 'Category link', 'Enlace de la categoría', 'Lien de la catégorie'),
    'URL file': ('Datei-URL', 'File URL', 'URL del archivo', 'URL du fichier'),
    'URL corrente': ('Aktuelle URL', 'Current URL', 'URL actual', 'URL actuelle'),
    'Pagina di provenienza': ('Herkunftsseite', 'Referring page', 'Página de procedencia', 'Page de provenance'),
    'URL post precedente': ('URL des vorherigen Beitrags', 'Previous post URL', 'URL de la entrada anterior', 'URL de l’article précédent'),
    'URL post successivo': ('URL des nächsten Beitrags', 'Next post URL', 'URL de la entrada siguiente', 'URL de l’article suivant'),
    'Titolo post precedente': ('Titel des vorherigen Beitrags', 'Previous post title', 'Título de la entrada anterior', 'Titre de l’article précédent'),
    'Titolo post successivo': ('Titel des nächsten Beitrags', 'Next post title', 'Título de la entrada siguiente', 'Titre de l’article suivant'),
    'URL prodotto': ('Produkt-URL', 'Product URL', 'URL del producto', 'URL du produit'),
    'Navigazione post': ('Beitragsnavigation', 'Post navigation', 'Navegación de entradas', 'Navigation des articles'),
    'Media (immagine in evidenza)': ('Medien (Beitragsbild)', 'Media (featured image)', 'Medios (imagen destacada)', 'Médias (image mise en avant)'),
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
