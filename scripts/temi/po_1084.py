# -*- coding: utf-8 -*-
"""Traduzioni della 1.4.1084 (16 nov 2026): Grafico, controlli in ordine e stili onesti (episodio 26).
Aggiunge le voci che mancano ai .po di de_DE, en_GB, en_US, es_ES, fr_FR e ricompila i .mo. Si può rilanciare.
uso: python po_1084.py
"""
import os
import subprocess
import sys

ROOT = os.path.normpath(os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', '..'))
LANG = os.path.join(ROOT, 'languages')
MSGFMT = os.path.join(os.path.dirname(sys.executable), 'Tools', 'i18n', 'msgfmt.py')
VERSIONE = 'OLObuild 1.4.1084'

# italiano: (tedesco, inglese, spagnolo, francese)
V = {
    'Nome della serie': ('Name der Reihe', 'Series name', 'Nombre de la serie', 'Nom de la série'),
    'Mostrato nella legenda': ('In der Legende angezeigt', 'Shown in the legend', 'Se muestra en la leyenda', 'Affiché dans la légende'),
    'Il nome dei valori, che la legenda e il fumetto mostrano accanto al colore: per esempio «kWh prodotti».': ('Der Name der Werte, den Legende und Sprechblase neben der Farbe zeigen: zum Beispiel „erzeugte kWh“.', 'The name of the values, shown by the legend and the tooltip next to the colour: for example “kWh produced”.', 'El nombre de los valores, que la leyenda y el globo muestran junto al color: por ejemplo «kWh producidos».', 'Le nom des valeurs, que la légende et l’infobulle affichent à côté de la couleur : par exemple « kWh produits ».'),
    'Legenda e fumetto': ('Legende und Sprechblase', 'Legend and tooltip', 'Leyenda y globo', 'Légende et infobulle'),
    'Fumetto al passaggio': ('Sprechblase beim Überfahren', 'Tooltip on hover', 'Globo al pasar', 'Infobulle au survol'),
    'Passando sopra un dato (o toccandolo, sul telefono) un fumetto ne mostra il valore.': ('Beim Überfahren eines Werts (oder Antippen auf dem Handy) zeigt eine Sprechblase seinen Wert.', 'Hovering over a data point (or tapping it on a phone) shows its value in a tooltip.', 'Al pasar sobre un dato (o tocarlo en el móvil) un globo muestra su valor.', 'Au survol d’une donnée (ou en la touchant sur le téléphone) une infobulle affiche sa valeur.'),
    'Le barre crescono, le linee si disegnano quando il grafico entra nello schermo.': ('Balken wachsen, Linien zeichnen sich, wenn das Diagramm ins Bild kommt.', 'Bars grow and lines draw themselves when the chart comes into view.', 'Las barras crecen y las líneas se dibujan cuando el gráfico entra en la pantalla.', 'Les barres grandissent et les lignes se dessinent quand le graphique entre à l’écran.'),
    'Scala e assi': ('Skala und Achsen', 'Scale and axes', 'Escala y ejes', 'Échelle et axes'),
    'Passo della scala': ('Skalenschritt', 'Scale step', 'Paso de la escala', 'Pas de l’échelle'),
    'Nome asse delle etichette': ('Name der Beschriftungsachse', 'Label axis name', 'Nombre del eje de etiquetas', 'Nom de l’axe des étiquettes'),
    'Nome asse dei valori': ('Name der Werteachse', 'Value axis name', 'Nombre del eje de valores', 'Nom de l’axe des valeurs'),
    'Cambia insieme sfondo, griglia, testi, fumetto e forma delle barre, coi colori del sito. I colori dei dati restano i tuoi (in fondo, «Ogni dato»).': ('Ändert Hintergrund, Raster, Texte, Sprechblase und Balkenform zusammen, mit den Farben der Website. Die Farben der Werte bleiben deine (unten, „Jeder Wert“).', 'Changes background, grid, text, tooltip and bar shape together, with the site colours. The data colours stay yours (at the bottom, “Each data point”).', 'Cambia a la vez fondo, cuadrícula, textos, globo y forma de las barras, con los colores del sitio. Los colores de los datos siguen siendo tuyos (abajo, «Cada dato»).', 'Change ensemble fond, grille, textes, infobulle et forme des barres, avec les couleurs du site. Les couleurs des données restent les tiennes (en bas, « Chaque donnée »).'),
    'Colore dei bordi': ('Farbe der Ränder', 'Border colour', 'Color de los bordes', 'Couleur des bordures'),
    'Un colore solo per il bordo di tutti i dati. Vuoto: ogni dato ha il suo.': ('Eine Farbe für den Rand aller Werte. Leer: jeder Wert hat seine eigene.', 'One colour for the border of every data point. Empty: each keeps its own.', 'Un solo color para el borde de todos los datos. Vacío: cada dato tiene el suyo.', 'Une seule couleur pour la bordure de toutes les données. Vide : chacune garde la sienne.'),
    'Quanto della sua colonna occupa ogni barra: 1 = tutta.': ('Wie viel seiner Spalte jeder Balken einnimmt: 1 = alles.', 'How much of its column each bar fills: 1 = all of it.', 'Cuánto de su columna ocupa cada barra: 1 = toda.', 'Quelle part de sa colonne occupe chaque barre : 1 = toute.'),
    'Larghezza colonne': ('Spaltenbreite', 'Column width', 'Ancho de columnas', 'Largeur des colonnes'),
    'Quanto dello spazio di ogni etichetta occupa la sua colonna: più basso, più aria fra le barre.': ('Wie viel vom Platz jeder Beschriftung ihre Spalte einnimmt: niedriger, mehr Luft zwischen den Balken.', 'How much of each label’s space its column fills: lower means more room between bars.', 'Cuánto del espacio de cada etiqueta ocupa su columna: más bajo, más aire entre las barras.', 'Quelle part de l’espace de chaque étiquette occupe sa colonne : plus bas, plus d’air entre les barres.'),
    'Raggio punti al passaggio': ('Punktradius beim Überfahren', 'Point radius on hover', 'Radio de puntos al pasar', 'Rayon des points au survol'),
    'Foro della ciambella': ('Loch des Rings', 'Doughnut hole', 'Agujero de la rosquilla', 'Trou de l’anneau'),
    'Griglia verticale': ('Vertikales Raster', 'Vertical grid', 'Cuadrícula vertical', 'Grille verticale'),
    'Griglia orizzontale': ('Horizontales Raster', 'Horizontal grid', 'Cuadrícula horizontal', 'Grille horizontale'),
    'Linea dell’asse orizzontale': ('Linie der horizontalen Achse', 'Horizontal axis line', 'Línea del eje horizontal', 'Ligne de l’axe horizontal'),
    'Linea dell’asse verticale': ('Linie der vertikalen Achse', 'Vertical axis line', 'Línea del eje vertical', 'Ligne de l’axe vertical'),
    'Colore linee degli assi': ('Farbe der Achsenlinien', 'Axis line colour', 'Color de las líneas de los ejes', 'Couleur des lignes des axes'),
    'Bordo fumetto': ('Rand der Sprechblase', 'Tooltip border', 'Borde del globo', 'Bordure de l’infobulle'),
    'Raggio fumetto': ('Radius der Sprechblase', 'Tooltip radius', 'Radio del globo', 'Rayon de l’infobulle'),
    'Padding fumetto': ('Innenabstand der Sprechblase', 'Tooltip padding', 'Relleno del globo', 'Marge intérieure de l’infobulle'),
    'Barre verticali': ('Vertikale Balken', 'Vertical bars', 'Barras verticales', 'Barres verticales'),
    'Barre orizzontali': ('Horizontale Balken', 'Horizontal bars', 'Barras horizontales', 'Barres horizontales'),
    'Ogni dato': ('Jeder Wert', 'Each data point', 'Cada dato', 'Chaque donnée'),
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
