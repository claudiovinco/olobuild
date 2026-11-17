# -*- coding: utf-8 -*-
"""Traduzioni della 1.4.1108 (16 nov 2026): Manifesto, il testo con l'editor e le etichette in italiano (episodio 34).
Aggiunge le voci che mancano ai .po di de_DE, en_GB, en_US, es_ES, fr_FR e ricompila i .mo. Si può rilanciare.
uso: python po_1097.py
"""
import os
import subprocess
import sys

ROOT = os.path.normpath(os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', '..'))
LANG = os.path.join(ROOT, 'languages')
MSGFMT = os.path.join(os.path.dirname(sys.executable), 'Tools', 'i18n', 'msgfmt.py')
VERSIONE = 'OLObuild 1.4.1108'

# italiano: (tedesco, inglese, spagnolo, francese)
V = {
    'Manifesto': ('Manifest', 'Manifesto', 'Manifiesto', 'Manifeste'),
    'Le parole in corsivo prendono il colore d’accento e restano dritte. Per andare a capo: Maiusc + Invio.': ('Kursive Wörter nehmen die Akzentfarbe an und bleiben gerade. Für einen Zeilenumbruch: Umschalt + Eingabe.', 'Words in italics take the accent colour and stay upright. To start a new line: Shift + Enter.', 'Las palabras en cursiva toman el color de acento y quedan rectas. Para saltar de línea: Mayús + Intro.', 'Les mots en italique prennent la couleur d’accent et restent droits. Pour aller à la ligne : Maj + Entrée.'),
    'Paragrafo': ('Absatz', 'Paragraph', 'Párrafo', 'Paragraphe'),
    'Mostra il paragrafo': ('Absatz anzeigen', 'Show the paragraph', 'Mostrar el párrafo', 'Afficher le paragraphe'),
    'Allo scorrimento': ('Beim Scrollen', 'On scroll', 'Al desplazar', 'Au défilement'),
    'Le parole si accendono': ('Die Wörter leuchten auf', 'Words light up', 'Las palabras se encienden', 'Les mots s’allument'),
    'Le parole partono spente e si accendono una alla volta mentre la pagina scorre. Chi nel sistema ha chiesto meno movimento le vede tutte accese.': ('Die Wörter beginnen gedimmt und leuchten nacheinander auf, während die Seite scrollt. Wer im System weniger Bewegung eingestellt hat, sieht sie alle hell.', 'The words start dimmed and light up one at a time as the page scrolls. Anyone who asked the system for less motion sees them all lit.', 'Las palabras empiezan apagadas y se encienden una a una mientras la página se desplaza. Quien pidió menos movimiento en el sistema las ve todas encendidas.', 'Les mots partent éteints et s’allument un à un pendant que la page défile. Qui a demandé moins de mouvement au système les voit tous allumés.'),
    'Colore d’accento': ('Akzentfarbe', 'Accent colour', 'Color de acento', 'Couleur d’accent'),
    'Le parole in corsivo del testo. Vuoto: il primario del sito.': ('Die kursiven Wörter des Textes. Leer: die Primärfarbe der Website.', 'The words in italics in the text. Empty: the site’s primary colour.', 'Las palabras en cursiva del texto. Vacío: el primario del sitio.', 'Les mots en italique du texte. Vide : la couleur primaire du site.'),
    'Parole spente': ('Gedimmte Wörter', 'Dimmed words', 'Palabras apagadas', 'Mots éteints'),
    'Quanto si vedono le parole non ancora accese.': ('Wie sichtbar die noch nicht aufleuchtenden Wörter sind.', 'How visible the words not yet lit are.', 'Cuánto se ven las palabras aún no encendidas.', 'À quel point on voit les mots pas encore allumés.'),
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
