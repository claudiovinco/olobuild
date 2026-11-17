# -*- coding: utf-8 -*-
"""Traduzioni della 1.4.1093 (16 nov 2026): Schede prodotto, nomi in italiano e controlli in ordine (episodio 29).
Aggiunge le voci che mancano ai .po di de_DE, en_GB, en_US, es_ES, fr_FR e ricompila i .mo. Si può rilanciare.
uso: python po_1093.py
"""
import os
import subprocess
import sys

ROOT = os.path.normpath(os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', '..'))
LANG = os.path.join(ROOT, 'languages')
MSGFMT = os.path.join(os.path.dirname(sys.executable), 'Tools', 'i18n', 'msgfmt.py')
VERSIONE = 'OLObuild 1.4.1093'

# italiano: (tedesco, inglese, spagnolo, francese)
V = {
    'Schede prodotto': ('Produktkarten', 'Product cards', 'Fichas de producto', 'Fiches produit'),
    'Sfondo / media in alto': ('Hintergrund / Medien oben', 'Top background / media', 'Fondo / medio arriba', 'Fond / média en haut'),
    'La metà alta della scheda: la foto del prodotto, o una tinta o una sfumatura con la lettera o il logo sopra.': ('Die obere Hälfte der Karte: das Produktfoto oder eine Farbe bzw. ein Verlauf mit dem Buchstaben oder dem Logo darüber.', 'The top half of the card: the product photo, or a colour or gradient with the letter or the logo on top.', 'La mitad superior de la ficha: la foto del producto, o un color o un degradado con la letra o el logo encima.', 'La moitié haute de la fiche : la photo du produit, ou une teinte ou un dégradé avec la lettre ou le logo dessus.'),
    'Una lettera grande sopra lo sfondo in alto, come un monogramma. Vuota: niente.': ('Ein großer Buchstabe über dem oberen Hintergrund, wie ein Monogramm. Leer: nichts.', 'A large letter over the top background, like a monogram. Empty: nothing.', 'Una letra grande sobre el fondo de arriba, como un monograma. Vacía: nada.', 'Une grande lettre sur le fond du haut, comme un monogramme. Vide : rien.'),
    'Al posto della lettera.': ('Anstelle des Buchstabens.', 'Instead of the letter.', 'En lugar de la letra.', 'À la place de la lettre.'),
    'Scritta in alto': ('Text oben', 'Top caption', 'Texto arriba', 'Texte en haut'),
    'Una piccola scritta a filo, sopra la foto o la lettera.': ('Ein kleiner Umrisstext über dem Foto oder dem Buchstaben.', 'A small outlined caption over the photo or the letter.', 'Un pequeño texto de contorno sobre la foto o la letra.', 'Un petit texte au trait, sur la photo ou la lettre.'),
    'Marca o categoria': ('Marke oder Kategorie', 'Brand or category', 'Marca o categoría', 'Marque ou catégorie'),
    'Mostra il badge': ('Badge anzeigen', 'Show the badge', 'Mostrar la insignia', 'Afficher le badge'),
    'Attaccata al titolo, nel colore della marca e, se vuoi, in corsivo.': ('Direkt am Titel, in der Markenfarbe und auf Wunsch kursiv.', 'Attached to the title, in the brand colour and, if you like, in italics.', 'Pegada al título, en el color de la marca y, si quieres, en cursiva.', 'Accolée au titre, dans la couleur de la marque et, si tu veux, en italique.'),
    'Mostra la scritta in alto': ('Text oben anzeigen', 'Show the top caption', 'Mostrar el texto de arriba', 'Afficher le texte en haut'),
    'Freccia accanto al pulsante': ('Pfeil neben dem Button', 'Arrow next to the button', 'Flecha junto al botón', 'Flèche à côté du bouton'),
    'Al passaggio': ('Beim Überfahren', 'On hover', 'Al pasar', 'Au survol'),
    'Colore scritta in alto': ('Farbe des Textes oben', 'Top caption colour', 'Color del texto de arriba', 'Couleur du texte en haut'),
    'Marca': ('Marke', 'Brand', 'Marca', 'Marque'),
    'Proporzioni in alto': ('Seitenverhältnis oben', 'Top aspect ratio', 'Proporciones arriba', 'Proportions en haut'),
    'Padding in alto': ('Innenabstand oben', 'Top padding', 'Relleno arriba', 'Marge intérieure en haut'),
    'Ogni scheda': ('Jede Karte', 'Each card', 'Cada ficha', 'Chaque fiche'),
    'Colore marca': ('Markenfarbe', 'Brand colour', 'Color de la marca', 'Couleur de la marque'),
    'Al massimo: se le schede resterebbero più strette di 220 px, in una colonna stretta della pagina, la griglia ne mette meno da sola.': ('Höchstens: Wären die Karten schmaler als 220 px, in einer schmalen Spalte der Seite, setzt das Raster selbst weniger.', 'At most: if the cards would be narrower than 220 px, in a narrow column of the page, the grid uses fewer by itself.', 'Como máximo: si las fichas quedaran más estrechas de 220 px, en una columna estrecha de la página, la cuadrícula pone menos sola.', 'Au maximum : si les fiches devaient être plus étroites que 220 px, dans une colonne étroite de la page, la grille en met moins d’elle-même.'),
    'Seconda parte in corsivo': ('Zweiter Teil kursiv', 'Second part in italics', 'Segunda parte en cursiva', 'Seconde partie en italique'),
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
