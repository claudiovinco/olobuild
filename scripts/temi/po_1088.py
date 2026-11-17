# -*- coding: utf-8 -*-
"""Traduzioni della 1.4.1088 (16 nov 2026): Tabella, i dati in una finestra a griglia, stili onesti e controlli in ordine (episodio 27).
Aggiunge le voci che mancano ai .po di de_DE, en_GB, en_US, es_ES, fr_FR e ricompila i .mo. Si può rilanciare.
uso: python po_1088.py
"""
import os
import subprocess
import sys

ROOT = os.path.normpath(os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', '..'))
LANG = os.path.join(ROOT, 'languages')
MSGFMT = os.path.join(os.path.dirname(sys.executable), 'Tools', 'i18n', 'msgfmt.py')
VERSIONE = 'OLObuild 1.4.1088'

# italiano: (tedesco, inglese, spagnolo, francese)
V = {
    'La prima riga dei Dati fa da intestazione: ha il suo sfondo e il suo carattere, e sul telefono dà il nome alle celle.': ('Die erste Datenzeile ist die Kopfzeile: mit eigenem Hintergrund und eigener Schrift; auf dem Handy benennt sie die Zellen.', 'The first row of the data is the header: it has its own background and font, and on phones it names the cells.', 'La primera fila de los datos es el encabezado: tiene su fondo y su tipografía, y en el móvil da nombre a las celdas.', 'La première ligne des données sert d’en-tête : elle a son fond et sa police, et sur téléphone elle nomme les cellules.'),
    'Prima colonna come titolo di riga': ('Erste Spalte als Zeilentitel', 'First column as row title', 'Primera columna como título de fila', 'Première colonne comme titre de ligne'),
    'La prima cella di ogni riga diventa il titolo della riga, in grassetto; nelle schede del telefono è il titolo della scheda.': ('Die erste Zelle jeder Zeile wird zum fetten Zeilentitel; in den Karten auf dem Handy ist sie der Kartentitel.', 'The first cell of each row becomes the row title, in bold; in the phone cards it is the card title.', 'La primera celda de cada fila se vuelve el título de la fila, en negrita; en las tarjetas del móvil es el título de la tarjeta.', 'La première cellule de chaque ligne devient le titre de la ligne, en gras ; dans les fiches du téléphone c’est le titre de la fiche.'),
    'A righe': ('Gestreift', 'Striped', 'A rayas', 'Rayée'),
    'Cambia insieme intestazione, righe, linee e caratteri, coi colori del sito. Poi ogni controllo qui sotto si regola da solo.': ('Ändert Kopfzeile, Zeilen, Linien und Schriften zusammen, mit den Farben der Website. Danach lässt sich jedes Steuerelement einzeln anpassen.', 'Changes header, rows, lines and fonts together, with the site colours. Then each control below can be adjusted on its own.', 'Cambia a la vez encabezado, filas, líneas y tipografías, con los colores del sitio. Luego cada control de abajo se ajusta por separado.', 'Change ensemble en-tête, lignes, filets et polices, avec les couleurs du site. Ensuite chaque réglage ci-dessous se règle seul.'),
    'Sfondo righe alternate': ('Hintergrund abwechselnder Zeilen', 'Alternate row background', 'Fondo de filas alternas', 'Fond des lignes alternées'),
    'Linee fra le righe': ('Linien zwischen den Zeilen', 'Lines between rows', 'Líneas entre filas', 'Filets entre les lignes'),
    'Le linee fra le righe e, sul telefono, il bordo delle schede.': ('Die Linien zwischen den Zeilen und, auf dem Handy, der Rand der Karten.', 'The lines between rows and, on phones, the border of the cards.', 'Las líneas entre filas y, en el móvil, el borde de las tarjetas.', 'Les filets entre les lignes et, sur téléphone, la bordure des fiches.'),
    'Evidenzia la riga al passaggio': ('Zeile beim Überfahren hervorheben', 'Highlight the row on hover', 'Resaltar la fila al pasar', 'Surligner la ligne au survol'),
    'Meno spazio attorno al testo delle celle e un carattere un po’ più piccolo: per tabelle lunghe.': ('Weniger Abstand um den Zelltext und eine etwas kleinere Schrift: für lange Tabellen.', 'Less space around the cell text and a slightly smaller font: for long tables.', 'Menos espacio alrededor del texto de las celdas y una letra algo más pequeña: para tablas largas.', 'Moins d’espace autour du texte des cellules et une police un peu plus petite : pour les longs tableaux.'),
    'Sul telefono': ('Auf dem Handy', 'On phones', 'En el móvil', 'Sur téléphone'),
    'Scorre di lato': ('Seitlich scrollen', 'Scrolls sideways', 'Se desplaza de lado', 'Défile sur le côté'),
    'Una scheda per riga': ('Eine Karte pro Zeile', 'One card per row', 'Una tarjeta por fila', 'Une fiche par ligne'),
    'Scorre di lato: la tabella resta com’è e si scorre col dito. Una scheda per riga: ogni riga diventa una scheda, coi nomi dell’intestazione accanto ai valori.': ('Seitlich scrollen: die Tabelle bleibt, wie sie ist, und wird mit dem Finger verschoben. Eine Karte pro Zeile: jede Zeile wird zu einer Karte, mit den Namen der Kopfzeile neben den Werten.', 'Scrolls sideways: the table stays as it is and is moved with a finger. One card per row: each row becomes a card, with the header names next to the values.', 'Se desplaza de lado: la tabla queda como está y se mueve con el dedo. Una tarjeta por fila: cada fila se vuelve una tarjeta, con los nombres del encabezado junto a los valores.', 'Défile sur le côté : le tableau reste tel quel et se fait glisser au doigt. Une fiche par ligne : chaque ligne devient une fiche, avec les noms de l’en-tête à côté des valeurs.'),
    'Modifica la tabella': ('Tabelle bearbeiten', 'Edit the table', 'Editar la tabla', 'Modifier le tableau'),
    'Dati della tabella': ('Tabellendaten', 'Table data', 'Datos de la tabla', 'Données du tableau'),
    'Scrivi nelle celle: la pagina si aggiorna mentre scrivi.': ('Schreib in die Zellen: die Seite aktualisiert sich beim Schreiben.', 'Type in the cells: the page updates as you type.', 'Escribe en las celdas: la página se actualiza mientras escribes.', 'Écris dans les cellules : la page se met à jour pendant que tu écris.'),
    'Tab passa alla cella dopo, Invio a quella sotto (alla fine aggiunge una riga).': ('Tab geht zur nächsten Zelle, Enter zur Zelle darunter (am Ende kommt eine Zeile dazu).', 'Tab moves to the next cell, Enter to the one below (at the end it adds a row).', 'Tab pasa a la celda siguiente, Intro a la de abajo (al final añade una fila).', 'Tab passe à la cellule suivante, Entrée à celle du dessous (à la fin il ajoute une ligne).'),
    'Sopra ogni colonna scegli l’allineamento; la × toglie la colonna o la riga.': ('Über jeder Spalte wählst du die Ausrichtung; das × entfernt die Spalte oder die Zeile.', 'Above each column choose the alignment; the × removes the column or the row.', 'Sobre cada columna eliges la alineación; la × quita la columna o la fila.', 'Au-dessus de chaque colonne choisis l’alignement ; le × retire la colonne ou la ligne.'),
    'Allineamento colonna': ('Spaltenausrichtung', 'Column alignment', 'Alineación de la columna', 'Alignement de la colonne'),
    'Togli la colonna': ('Spalte entfernen', 'Remove the column', 'Quitar la columna', 'Retirer la colonne'),
    'Testa': ('Kopf', 'Header', 'Cabecera', 'En-tête'),
    'Togli la riga': ('Zeile entfernen', 'Remove the row', 'Quitar la fila', 'Retirer la ligne'),
    'colonna': ('Spalte', 'column', 'columna', 'colonne'),
    '+ Riga': ('+ Zeile', '+ Row', '+ Fila', '+ Ligne'),
    '+ Colonna': ('+ Spalte', '+ Column', '+ Columna', '+ Colonne'),
    '1 riga': ('1 Zeile', '1 row', '1 fila', '1 ligne'),
    '%d righe': ('%d Zeilen', '%d rows', '%d filas', '%d lignes'),
    '%d colonne': ('%d Spalten', '%d columns', '%d columnas', '%d colonnes'),
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
