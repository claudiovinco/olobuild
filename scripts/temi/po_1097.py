# -*- coding: utf-8 -*-
"""Traduzioni della 1.4.1097 (16 nov 2026): Overlay Grid, etichette in italiano, zone e stili onesti (episodio 31).
Aggiunge le voci che mancano ai .po di de_DE, en_GB, en_US, es_ES, fr_FR e ricompila i .mo. Si può rilanciare.
uso: python po_1097.py
"""
import os
import subprocess
import sys

ROOT = os.path.normpath(os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', '..'))
LANG = os.path.join(ROOT, 'languages')
MSGFMT = os.path.join(os.path.dirname(sys.executable), 'Tools', 'i18n', 'msgfmt.py')
VERSIONE = 'OLObuild 1.4.1097'

# italiano: (tedesco, inglese, spagnolo, francese)
V = {
    'Immagine: la foto col testo sopra. Testo, Icona e Grafica: una card piena col suo colore, senza foto.': ('Bild: das Foto mit dem Text darüber. Text, Symbol und Grafik: eine volle Karte in ihrer Farbe, ohne Foto.', 'Image: the photo with the text over it. Text, Icon and Graphic: a solid card in its colour, without a photo.', 'Imagen: la foto con el texto encima. Texto, Icono y Gráfico: una tarjeta llena con su color, sin foto.', 'Image : la photo avec le texte dessus. Texte, Icône et Graphique : une carte pleine de sa couleur, sans photo.'),
    'Seconda immagine al passaggio': ('Zweites Bild beim Überfahren', 'Second image on hover', 'Segunda imagen al pasar', 'Seconde image au survol'),
    'Il paragrafo sotto il titolo, nelle card senza foto.': ('Der Absatz unter dem Titel, in Karten ohne Foto.', 'The paragraph under the title, in cards without a photo.', 'El párrafo bajo el título, en las tarjetas sin foto.', 'Le paragraphe sous le titre, dans les cartes sans photo.'),
    'Una scritta nell’angolo della card: «Nuovo», «Esaurito», «Ultimi posti».': ('Ein Text in der Ecke der Karte: „Neu“, „Ausverkauft“, „Letzte Plätze“.', 'A label in the corner of the card: “New”, “Sold out”, “Last places”.', 'Un texto en la esquina de la tarjeta: «Nuevo», «Agotado», «Últimas plazas».', 'Un texte dans le coin de la carte : « Nouveau », « Épuisé », « Dernières places ».'),
    'Cella alta': ('Hohe Zelle', 'Tall cell', 'Celda alta', 'Cellule haute'),
    'Nel mosaico occupa due righe.': ('Im Mosaik belegt sie zwei Zeilen.', 'In the mosaic it takes two rows.', 'En el mosaico ocupa dos filas.', 'Dans la mosaïque elle occupe deux lignes.'),
    'Cella larga': ('Breite Zelle', 'Wide cell', 'Celda ancha', 'Cellule large'),
    'Nel mosaico occupa due colonne.': ('Im Mosaik belegt sie zwei Spalten.', 'In the mosaic it takes two columns.', 'En el mosaico ocupa dos columnas.', 'Dans la mosaïque elle occupe deux colonnes.'),
    'Il testo': ('Der Text', 'The text', 'El texto', 'Le texte'),
    'Compare': ('Erscheint', 'Fades in', 'Aparece', 'Apparaît'),
    'Entra da sinistra': ('Kommt von links', 'Enters from the left', 'Entra por la izquierda', 'Entre par la gauche'),
    'Entra da destra': ('Kommt von rechts', 'Enters from the right', 'Entra por la derecha', 'Entre par la droite'),
    'Sempre visibile, o solo quando il puntatore passa sulla card. Sul telefono, dove non si passa sopra, il testo è sempre visibile.': ('Immer sichtbar oder nur, wenn der Mauszeiger über die Karte fährt. Auf dem Handy, wo man nicht darüberfährt, ist der Text immer sichtbar.', 'Always visible, or only when the pointer is over the card. On phones, where there is no hover, the text is always visible.', 'Siempre visible, o solo cuando el puntero pasa por la tarjeta. En el móvil, donde no se pasa por encima, el texto siempre se ve.', 'Toujours visible, ou seulement quand le pointeur passe sur la carte. Sur téléphone, où l’on ne survole pas, le texte est toujours visible.'),
    'Mostra il pulsante': ('Button anzeigen', 'Show the button', 'Mostrar el botón', 'Afficher le bouton'),
    'Lo stesso pulsante su ogni card, che porta al suo link.': ('Derselbe Button auf jeder Karte, der zu ihrem Link führt.', 'The same button on every card, leading to its link.', 'El mismo botón en cada tarjeta, que lleva a su enlace.', 'Le même bouton sur chaque carte, qui mène à son lien.'),
    'Cambia insieme velo, testo, raggio, ombra ed effetto al passaggio, coi colori del sito. Poi ogni controllo qui sotto si regola da solo.': ('Ändert Schleier, Text, Radius, Schatten und Hover-Effekt zusammen, mit den Farben der Website. Danach lässt sich jedes Steuerelement einzeln anpassen.', 'Changes veil, text, radius, shadow and hover effect together, with the site colours. Then each control below can be adjusted on its own.', 'Cambia a la vez velo, texto, radio, sombra y efecto al pasar, con los colores del sitio. Luego cada control de abajo se ajusta por separado.', 'Change ensemble voile, texte, rayon, ombre et effet au survol, avec les couleurs du site. Ensuite chaque réglage ci-dessous se règle seul.'),
    'Colore del velo': ('Farbe des Schleiers', 'Veil colour', 'Color del velo', 'Couleur du voile'),
    'Il fondo sotto il testo delle card con la foto.': ('Der Hintergrund unter dem Text der Karten mit Foto.', 'The background under the text of cards with a photo.', 'El fondo bajo el texto de las tarjetas con foto.', 'Le fond sous le texte des cartes avec photo.'),
    'Velo sfumato': ('Verlaufsschleier', 'Gradient veil', 'Velo degradado', 'Voile dégradé'),
    'Trasparente in alto e pieno in basso, dove sta il testo: la foto resta libera.': ('Oben transparent und unten voll, wo der Text steht: das Foto bleibt frei.', 'Transparent at the top and solid at the bottom, where the text is: the photo stays clear.', 'Transparente arriba y lleno abajo, donde está el texto: la foto queda libre.', 'Transparent en haut et plein en bas, là où se trouve le texte : la photo reste dégagée.'),
    'Effetto sull’immagine': ('Effekt auf dem Bild', 'Image effect', 'Efecto en la imagen', 'Effet sur l’image'),
    'Zoom e rotazione': ('Zoom und Drehung', 'Zoom and rotation', 'Zoom y rotación', 'Zoom et rotation'),
    'Più luminosa': ('Heller', 'Brighter', 'Más luminosa', 'Plus lumineuse'),
    'Dal grigio al colore': ('Von Grau zu Farbe', 'From grey to colour', 'Del gris al color', 'Du gris à la couleur'),
    'Da sfocata a nitida': ('Von unscharf zu scharf', 'From blurred to sharp', 'De desenfocada a nítida', 'De floue à nette'),
    'Al passaggio del puntatore sulla card.': ('Wenn der Mauszeiger über die Karte fährt.', 'When the pointer is over the card.', 'Al pasar el puntero por la tarjeta.', 'Au passage du pointeur sur la carte.'),
    'Grandezza del titolo': ('Titelgröße', 'Title size', 'Tamaño del título', 'Taille du titre'),
    'Grandissimo (H1)': ('Sehr groß (H1)', 'Very large (H1)', 'Muy grande (H1)', 'Très grand (H1)'),
    'Grande (H2)': ('Groß (H2)', 'Large (H2)', 'Grande (H2)', 'Grand (H2)'),
    'Medio (H3)': ('Mittel (H3)', 'Medium (H3)', 'Mediano (H3)', 'Moyen (H3)'),
    'Piccolo (H4)': ('Klein (H4)', 'Small (H4)', 'Pequeño (H4)', 'Petit (H4)'),
    'Punto focale delle immagini (uguale per tutte). Predefinito: il centro.': ('Fokuspunkt der Bilder (für alle gleich). Standard: die Mitte.', 'Focal point of the images (the same for all). Default: the centre.', 'Punto focal de las imágenes (igual para todas). Predeterminado: el centro.', 'Point focal des images (le même pour toutes). Par défaut : le centre.'),
    'Padding del testo': ('Innenabstand des Textes', 'Text padding', 'Relleno del texto', 'Marge intérieure du texte'),
    'Nel mosaico le card con «Cella alta» o «Cella larga» occupano due righe o due colonne; l’altezza diventa quella di una riga.': ('Im Mosaik belegen Karten mit „Hohe Zelle“ oder „Breite Zelle“ zwei Zeilen oder zwei Spalten; die Höhe wird zur Höhe einer Zeile.', 'In the mosaic, cards with “Tall cell” or “Wide cell” take two rows or two columns; the height becomes that of one row.', 'En el mosaico, las tarjetas con «Celda alta» o «Celda ancha» ocupan dos filas o dos columnas; la altura pasa a ser la de una fila.', 'Dans la mosaïque, les cartes avec « Cellule haute » ou « Cellule large » occupent deux lignes ou deux colonnes ; la hauteur devient celle d’une ligne.'),
    'Colonne sul tablet orizzontale': ('Spalten auf dem Tablet quer', 'Columns on landscape tablet', 'Columnas en tableta horizontal', 'Colonnes sur tablette paysage'),
    'Colonne sul tablet': ('Spalten auf dem Tablet', 'Columns on tablet', 'Columnas en tableta', 'Colonnes sur tablette'),
    'Come il telefono': ('Wie das Handy', 'Like the phone', 'Como el móvil', 'Comme le téléphone'),
    'Su tutta la card': ('Über die ganze Karte', 'Over the whole card', 'Sobre toda la tarjeta', 'Sur toute la carte'),
    'Stile del pulsante': ('Button-Stil', 'Button style', 'Estilo del botón', 'Style du bouton'),
    'Con freccia': ('Mit Pfeil', 'With arrow', 'Con flecha', 'Avec flèche'),
    'Vuoto: il colore primario del sito.': ('Leer: die Primärfarbe der Website.', 'Empty: the site’s primary colour.', 'Vacío: el color primario del sitio.', 'Vide : la couleur principale du site.'),
    'Testo nastro': ('Bandtext', 'Ribbon text', 'Texto de la cinta', 'Texte du ruban'),
    'Vuoto: il colore che contrasta col primario.': ('Leer: die Kontrastfarbe zur Primärfarbe.', 'Empty: the colour that contrasts with the primary.', 'Vacío: el color que contrasta con el primario.', 'Vide : la couleur qui contraste avec la principale.'),
    'Posizione nastro': ('Bandposition', 'Ribbon position', 'Posición de la cinta', 'Position du ruban'),
    'Ogni elemento': ('Jedes Element', 'Each item', 'Cada elemento', 'Chaque élément'),
    'Lo sfondo della card senza foto. Vuoto: il colore scuro del sito.': ('Der Hintergrund der Karte ohne Foto. Leer: die dunkle Farbe der Website.', 'The background of the card without a photo. Empty: the site’s dark colour.', 'El fondo de la tarjeta sin foto. Vacío: el color oscuro del sitio.', 'Le fond de la carte sans photo. Vide : la couleur sombre du site.'),
    'Solo per questa card. Vuoto: quello scelto in Testo.': ('Nur für diese Karte. Leer: der unter Text gewählte.', 'For this card only. Empty: the one chosen in Text.', 'Solo para esta tarjeta. Vacío: el elegido en Texto.', 'Pour cette carte seulement. Vide : celui choisi dans Texte.'),
    'Nastro': ('Band', 'Ribbon', 'Cinta', 'Ruban'),
    'Minimo': ('Minimal', 'Minimal', 'Mínimo', 'Minimal'),
    'Rivista': ('Magazin', 'Magazine', 'Revista', 'Magazine'),
    'Vetro': ('Glas', 'Glass', 'Vidrio', 'Verre'),
    'Mosaico': ('Mosaik', 'Mosaic', 'Mosaico', 'Mosaïque'),
    'Sale dal basso': ('Steigt von unten auf', 'Rises from below', 'Sube desde abajo', 'Monte du bas'),
    'Scende dall’alto': ('Kommt von oben', 'Comes down from above', 'Baja desde arriba', 'Descend du haut'),
    'Posizione del testo': ('Position des Textes', 'Text position', 'Posición del texto', 'Position du texte'),
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
