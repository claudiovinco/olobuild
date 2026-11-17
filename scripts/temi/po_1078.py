# -*- coding: utf-8 -*-
"""Traduzioni della 1.4.1078 (16 nov 2026): Card impilate nello standard (episodio 23 di «Una tile in un minuto»).
Aggiunge le voci che mancano ai .po di de_DE, en_GB, en_US, es_ES, fr_FR e ricompila i .mo. Si può rilanciare.
uso: python po_1078.py
"""
import os
import subprocess
import sys

ROOT = os.path.normpath(os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', '..'))
LANG = os.path.join(ROOT, 'languages')
MSGFMT = os.path.join(os.path.dirname(sys.executable), 'Tools', 'i18n', 'msgfmt.py')
VERSIONE = 'OLObuild 1.4.1078'

# italiano: (tedesco, inglese, spagnolo, francese)
V = {
    'Card impilate': ('Gestapelte Karten', 'Stacked cards', 'Tarjetas apiladas', 'Cartes empilées'),
    'Occhiello': ('Dachzeile', 'Eyebrow', 'Antetítulo', 'Surtitre'),
    'Una parola sopra il titolo, in maiuscolo: «Qualità», «Ascolto».': (
        'Ein Wort über dem Titel, in Großbuchstaben: „Qualität“, „Zuhören“.',
        'A word above the title, in capitals: “Quality”, “Listening”.',
        'Una palabra sobre el título, en mayúsculas: «Calidad», «Escucha».',
        'Un mot au-dessus du titre, en majuscules : « Qualité », « Écoute ».'),
    'Seconda parte del titolo': ('Zweiter Teil des Titels', 'Second part of the title', 'Segunda parte del título', 'Seconde partie du titre'),
    'Continua il titolo in un tono più tenue: «Curiamo» + «ogni dettaglio».': (
        'Setzt den Titel in einem zarteren Ton fort: „Wir pflegen“ + „jedes Detail“.',
        'Continues the title in a softer tone: “We care for” + “every detail”.',
        'Continúa el título en un tono más suave: «Cuidamos» + «cada detalle».',
        'Prolonge le titre dans un ton plus doux : « Nous soignons » + « chaque détail ».'),
    'Scritta senza immagine': ('Text ohne Bild', 'Text without image', 'Texto sin imagen', 'Texte sans image'),
    'Compare nel riquadro dell’immagine finché non ne scegli una.': (
        'Erscheint im Bildfeld, bis du ein Bild auswählst.',
        'Shown in the image box until you choose an image.',
        'Aparece en el recuadro de la imagen hasta que eliges una.',
        'Apparaît dans le cadre de l’image tant que tu n’en choisis pas une.'),
    'Mostra il numero': ('Nummer anzeigen', 'Show the number', 'Mostrar el número', 'Afficher le numéro'),
    'Il numero progressivo sopra ogni card: 01, 02, 03…': (
        'Die laufende Nummer über jeder Karte: 01, 02, 03 …', 'The running number above each card: 01, 02, 03…',
        'El número correlativo sobre cada tarjeta: 01, 02, 03…', 'Le numéro d’ordre au-dessus de chaque carte : 01, 02, 03…'),
    'Impilamento': ('Stapeln', 'Stacking', 'Apilamiento', 'Empilement'),
    'Distanza dall’alto': ('Abstand von oben', 'Distance from the top', 'Distancia desde arriba', 'Distance depuis le haut'),
    'A che altezza della finestra si ferma la prima card mentre scorri.': (
        'In welcher Höhe des Fensters die erste Karte beim Scrollen stehen bleibt.',
        'How far down the window the first card stops while you scroll.',
        'A qué altura de la ventana se detiene la primera tarjeta mientras te desplazas.',
        'À quelle hauteur de la fenêtre la première carte s’arrête pendant le défilement.'),
    'Scalino fra le card': ('Stufe zwischen den Karten', 'Step between cards', 'Escalón entre tarjetas', 'Marche entre les cartes'),
    'Ogni card si ferma un po’ più in basso della precedente, così della pila resta visibile il bordo.': (
        'Jede Karte hält etwas tiefer als die vorherige, so bleibt der Rand des Stapels sichtbar.',
        'Each card stops a little lower than the previous one, so the edge of the stack stays visible.',
        'Cada tarjeta se detiene un poco más abajo que la anterior, así el borde de la pila sigue visible.',
        'Chaque carte s’arrête un peu plus bas que la précédente, ainsi le bord de la pile reste visible.'),
    'Rimpicciolisci le card coperte': ('Verdeckte Karten verkleinern', 'Shrink the covered cards', 'Reducir las tarjetas cubiertas', 'Réduire les cartes couvertes'),
    'Le card già coperte si riducono un poco, per dare profondità. Chi ha chiesto al sistema meno animazioni vede le card una sotto l’altra.': (
        'Bereits verdeckte Karten werden etwas kleiner, das gibt Tiefe. Wer im System weniger Animationen eingestellt hat, sieht die Karten untereinander.',
        'Cards already covered shrink a little, to add depth. Anyone who asked their system for less motion sees the cards one below the other.',
        'Las tarjetas ya cubiertas se reducen un poco, para dar profundidad. Quien pidió al sistema menos animaciones ve las tarjetas una debajo de otra.',
        'Les cartes déjà couvertes rétrécissent un peu, pour donner de la profondeur. Qui a demandé moins d’animations au système voit les cartes l’une sous l’autre.'),
    'Quanto rimpicciolire': ('Wie stark verkleinern', 'How much to shrink', 'Cuánto reducir', 'De combien réduire'),
    'Per ogni card che sale sopra.': ('Für jede Karte, die sich darüberlegt.', 'For each card that slides over it.', 'Por cada tarjeta que sube encima.', 'Pour chaque carte qui passe par-dessus.'),
    'Sfondo card': ('Kartenhintergrund', 'Card background', 'Fondo de la tarjeta', 'Fond de la carte'),
    'Per le card senza un colore proprio (lo scegli card per card qui sotto, in «Card»). Vuoto: la superficie del tema.': (
        'Für Karten ohne eigene Farbe (die wählst du Karte für Karte unten unter „Karte“). Leer: die Fläche des Themes.',
        'For cards without their own colour (you choose it card by card below, under “Card”). Empty: the theme surface.',
        'Para las tarjetas sin color propio (lo eliges tarjeta por tarjeta abajo, en «Tarjeta»). Vacío: la superficie del tema.',
        'Pour les cartes sans couleur propre (tu la choisis carte par carte plus bas, dans « Carte »). Vide : la surface du thème.'),
    'Colore testo card': ('Textfarbe der Karte', 'Card text colour', 'Color del texto de la tarjeta', 'Couleur du texte de la carte'),
    'Per le card senza un colore del testo proprio. Vuoto: il testo del tema.': (
        'Für Karten ohne eigene Textfarbe. Leer: der Text des Themes.',
        'For cards without their own text colour. Empty: the theme text.',
        'Para las tarjetas sin color de texto propio. Vacío: el texto del tema.',
        'Pour les cartes sans couleur de texte propre. Vide : le texte du thème.'),
    'Colore numero': ('Farbe der Nummer', 'Number colour', 'Color del número', 'Couleur du numéro'),
    'Vuoto: il colore principale del tema.': ('Leer: die Hauptfarbe des Themes.', 'Empty: the theme’s main colour.', 'Vacío: el color principal del tema.', 'Vide : la couleur principale du thème.'),
    'Titolo grande': ('Großer Titel', 'Large title', 'Título grande', 'Grand titre'),
    'Il carattere dei titoli del sito e una misura più grande.': (
        'Die Titelschrift der Website und eine größere Größe.', 'The site’s heading typeface and a larger size.',
        'La tipografía de los títulos del sitio y un tamaño mayor.', 'La police des titres du site et une taille plus grande.'),
    'Raggio card': ('Kartenradius', 'Card radius', 'Radio de la tarjeta', 'Rayon de la carte'),
    'Lo stesso per tutte le immagini delle card.': ('Derselbe für alle Kartenbilder.', 'The same for all card images.', 'El mismo para todas las imágenes de las tarjetas.', 'Le même pour toutes les images des cartes.'),
    'In una colonna stretta e sul telefono l’immagine va sotto il testo.': (
        'In einer schmalen Spalte und auf dem Handy steht das Bild unter dem Text.',
        'In a narrow column and on phones the image goes below the text.',
        'En una columna estrecha y en el móvil la imagen va debajo del texto.',
        'Dans une colonne étroite et sur téléphone, l’image passe sous le texte.'),
    'Gap card': ('Kartenabstand', 'Card gap', 'Espacio entre tarjetas', 'Écart entre les cartes'),
    'Colore testo': ('Textfarbe', 'Text colour', 'Color del texto', 'Couleur du texte'),
    'Ogni card': ('Jede Karte', 'Each card', 'Cada tarjeta', 'Chaque carte'),
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
