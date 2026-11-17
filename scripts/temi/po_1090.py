# -*- coding: utf-8 -*-
"""Traduzioni della 1.4.1090 (16 nov 2026): Work List, controlli in ordine (episodio 28).
Aggiunge le voci che mancano ai .po di de_DE, en_GB, en_US, es_ES, fr_FR e ricompila i .mo. Si può rilanciare.
uso: python po_1090.py
"""
import os
import subprocess
import sys

ROOT = os.path.normpath(os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', '..'))
LANG = os.path.join(ROOT, 'languages')
MSGFMT = os.path.join(os.path.dirname(sys.executable), 'Tools', 'i18n', 'msgfmt.py')
VERSIONE = 'OLObuild 1.4.1090'

# italiano: (tedesco, inglese, spagnolo, francese)
V = {
    'Il numero o la sigla davanti al titolo: 01, A1, ★. Vuoto: niente.': ('Die Nummer oder das Kürzel vor dem Titel: 01, A1, ★. Leer: nichts.', 'The number or code before the title: 01, A1, ★. Empty: nothing.', 'El número o la sigla delante del título: 01, A1, ★. Vacío: nada.', 'Le numéro ou le sigle devant le titre : 01, A1, ★. Vide : rien.'),
    'Con un link la riga intera porta lì.': ('Mit einem Link führt die ganze Zeile dorthin.', 'With a link the whole row goes there.', 'Con un enlace toda la fila lleva allí.', 'Avec un lien toute la ligne y mène.'),
    'Sfondo al passaggio': ('Hintergrund beim Überfahren', 'Background on hover', 'Fondo al pasar', 'Fond au survol'),
    'Carattere dei dettagli': ('Schrift der Details', 'Details font', 'Tipografía de los detalles', 'Police des détails'),
    'Il carattere di numero, categoria e anno. Vuoto: quello monospaziato del sito.': ('Die Schrift von Nummer, Kategorie und Jahr. Leer: die Monospace-Schrift der Website.', 'The font of number, category and year. Empty: the site’s monospaced font.', 'La tipografía del número, la categoría y el año. Vacío: la monoespaciada del sitio.', 'La police du numéro, de la catégorie et de l’année. Vide : la police à chasse fixe du site.'),
    'Rientro al passaggio': ('Einzug beim Überfahren', 'Indent on hover', 'Sangría al pasar', 'Retrait au survol'),
    'Di quanto la riga rientra quando ci si passa sopra.': ('Wie weit die Zeile beim Überfahren einrückt.', 'How far the row indents when hovered.', 'Cuánto entra la fila al pasar por encima.', 'De combien la ligne se décale au survol.'),
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
