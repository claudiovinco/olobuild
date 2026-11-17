# -*- coding: utf-8 -*-
"""Traduzioni della 1.4.1080 (16 nov 2026): Hotspot nello standard, stili onesti (episodio 24 di «Una tile in un minuto»).
Aggiunge le voci che mancano ai .po di de_DE, en_GB, en_US, es_ES, fr_FR e ricompila i .mo. Si può rilanciare.
uso: python po_1080.py
"""
import os
import subprocess
import sys

ROOT = os.path.normpath(os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', '..'))
LANG = os.path.join(ROOT, 'languages')
MSGFMT = os.path.join(os.path.dirname(sys.executable), 'Tools', 'i18n', 'msgfmt.py')
VERSIONE = 'OLObuild 1.4.1080'

# italiano: (tedesco, inglese, spagnolo, francese)
V = {
    'Da sinistra': ('Von links', 'From the left', 'Desde la izquierda', 'Depuis la gauche'),
    'Dove sta il marker sull’immagine: 0 è il bordo sinistro, 100 il destro.': (
        'Wo der Marker auf dem Bild sitzt: 0 ist der linke Rand, 100 der rechte.',
        'Where the marker sits on the image: 0 is the left edge, 100 the right.',
        'Dónde está el marcador en la imagen: 0 es el borde izquierdo, 100 el derecho.',
        'Où se trouve le marqueur sur l’image : 0 est le bord gauche, 100 le droit.'),
    'Dall’alto': ('Von oben', 'From the top', 'Desde arriba', 'Depuis le haut'),
    '0 è il bordo in alto, 100 quello in basso.': ('0 ist der obere Rand, 100 der untere.', '0 is the top edge, 100 the bottom one.', '0 es el borde superior, 100 el inferior.', '0 est le bord du haut, 100 celui du bas.'),
    'Foto nel fumetto': ('Foto in der Sprechblase', 'Photo in the bubble', 'Foto en el bocadillo', 'Photo dans la bulle'),
    'Numeri': ('Zahlen', 'Numbers', 'Números', 'Numéros'),
    'Punti': ('Punkte', 'Dots', 'Puntos', 'Points'),
    'Icone': ('Icons', 'Icons', 'Iconos', 'Icônes'),
    'Scuro': ('Dunkel', 'Dark', 'Oscuro', 'Sombre'),
    'Vetro': ('Glas', 'Glass', 'Vidrio', 'Verre'),
    'Editoriale': ('Redaktionell', 'Editorial', 'Editorial', 'Éditorial'),
    'Cambia insieme marker, fumetto e raggio dell’immagine, coi colori del sito. Poi ogni controllo qui sotto si regola da solo.': (
        'Ändert Marker, Sprechblase und Bildradius zusammen, mit den Farben der Website. Danach lässt sich jede Einstellung unten einzeln anpassen.',
        'Changes the markers, the bubble and the image radius together, with the site colours. Then each control below can be adjusted on its own.',
        'Cambia a la vez marcadores, bocadillo y radio de la imagen, con los colores del sitio. Después cada control de abajo se ajusta por separado.',
        'Change ensemble les marqueurs, la bulle et le rayon de l’image, avec les couleurs du site. Ensuite chaque réglage ci-dessous se modifie seul.'),
    'Marker': ('Marker', 'Markers', 'Marcadores', 'Marqueurs'),
    'Forma del marker': ('Form des Markers', 'Marker shape', 'Forma del marcador', 'Forme du marqueur'),
    'Vuoto: il colore principale del sito.': ('Leer: die Hauptfarbe der Website.', 'Empty: the site’s main colour.', 'Vacío: el color principal del sitio.', 'Vide : la couleur principale du site.'),
    'Marker che pulsano': ('Pulsierende Marker', 'Pulsing markers', 'Marcadores que laten', 'Marqueurs qui pulsent'),
    'Un anello si allarga attorno a ogni marker, per dire che si può toccare.': (
        'Ein Ring breitet sich um jeden Marker aus und zeigt, dass man ihn antippen kann.',
        'A ring spreads around each marker, to show it can be tapped.',
        'Un anillo se expande alrededor de cada marcador, para indicar que se puede tocar.',
        'Un anneau s’élargit autour de chaque marqueur, pour montrer qu’on peut le toucher.'),
    'Fumetto': ('Sprechblase', 'Bubble', 'Bocadillo', 'Bulle'),
    'Sfondo fumetto': ('Hintergrund der Sprechblase', 'Bubble background', 'Fondo del bocadillo', 'Fond de la bulle'),
    'Vuoto: la superficie del sito. Con un colore semitrasparente il fumetto diventa vetro smerigliato.': (
        'Leer: die Fläche der Website. Mit einer halbtransparenten Farbe wird die Sprechblase zu Milchglas.',
        'Empty: the site surface. With a semi-transparent colour the bubble becomes frosted glass.',
        'Vacío: la superficie del sitio. Con un color semitransparente el bocadillo se vuelve vidrio esmerilado.',
        'Vide : la surface du site. Avec une couleur semi-transparente, la bulle devient du verre dépoli.'),
    'Colore testo fumetto': ('Textfarbe der Sprechblase', 'Bubble text colour', 'Color del texto del bocadillo', 'Couleur du texte de la bulle'),
    'Larghezza fumetto': ('Breite der Sprechblase', 'Bubble width', 'Ancho del bocadillo', 'Largeur de la bulle'),
    'Dove si apre il fumetto': ('Wo sich die Sprechblase öffnet', 'Where the bubble opens', 'Dónde se abre el bocadillo', 'Où s’ouvre la bulle'),
    'Posizione del fumetto': ('Position der Sprechblase', 'Bubble position', 'Posición del bocadillo', 'Position de la bulle'),
    'A sinistra': ('Links', 'On the left', 'A la izquierda', 'À gauche'),
    'A destra': ('Rechts', 'On the right', 'A la derecha', 'À droite'),
    'Se non ci sta, il fumetto si sposta o si gira da solo per restare nello schermo.': (
        'Wenn sie nicht passt, verschiebt oder dreht sich die Sprechblase von selbst, um im Bildschirm zu bleiben.',
        'If it doesn’t fit, the bubble moves or flips by itself to stay on screen.',
        'Si no cabe, el bocadillo se mueve o se gira solo para quedarse en la pantalla.',
        'Si elle ne tient pas, la bulle se déplace ou se retourne d’elle-même pour rester à l’écran.'),
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
