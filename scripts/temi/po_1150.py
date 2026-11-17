# -*- coding: utf-8 -*-
"""Traduzioni della 1.4.1150 (17 nov 2026): Viewer 360° con nomi in italiano, stili veri e zone standard (episodio 49).
Aggiunge le voci che mancano ai .po di de_DE, en_GB, en_US, es_ES, fr_FR e ricompila i .mo. Si può rilanciare.
uso: python po_1150.py
"""
import os
import subprocess
import sys

ROOT = os.path.normpath(os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', '..'))
LANG = os.path.join(ROOT, 'languages')
MSGFMT = os.path.join(os.path.dirname(sys.executable), 'Tools', 'i18n', 'msgfmt.py')
VERSIONE = 'OLObuild 1.4.1157'

# italiano: (tedesco, inglese, spagnolo, francese)
V = {
    'Panorama a 360°': ('360°-Panorama', '360° panorama', 'Panorama de 360°', 'Panorama à 360°'),
    'Oggetto girevole, da una foto': ('Drehbares Objekt, aus einem Foto', 'Spinning object, from one photo', 'Objeto giratorio, de una foto', 'Objet tournant, à partir d’une photo'),
    'Oggetto girevole, da una sequenza di foto': ('Drehbares Objekt, aus einer Fotoserie', 'Spinning object, from a photo sequence', 'Objeto giratorio, de una secuencia de fotos', 'Objet tournant, à partir d’une suite de photos'),
    'Panorama: una foto o un video equirettangolare (largo il doppio dell’altezza), dentro cui ci si guarda attorno. Oggetto girevole: un prodotto che ruota su sé stesso trascinandolo, con l’inerzia al rilascio; da una foto sola (la foto ruota) o dalla sequenza delle foto scattate girandogli intorno.': ('Panorama: ein equirektangulares Foto oder Video (doppelt so breit wie hoch), in dem man sich umsieht. Drehbares Objekt: ein Produkt, das sich beim Ziehen um sich selbst dreht und beim Loslassen nachläuft; aus einem einzigen Foto (das Foto dreht sich) oder aus der Serie der Fotos, die rundherum aufgenommen wurden.', 'Panorama: an equirectangular photo or video (twice as wide as it is tall) that you look around in. Spinning object: a product that turns on itself as you drag it, with inertia when released; from a single photo (the photo rotates) or from the sequence of photos taken walking around it.', 'Panorama: una foto o un vídeo equirrectangular (el doble de ancho que de alto) dentro del cual se mira alrededor. Objeto giratorio: un producto que gira sobre sí mismo al arrastrarlo, con inercia al soltarlo; de una sola foto (la foto gira) o de la secuencia de fotos tomadas a su alrededor.', 'Panorama : une photo ou une vidéo équirectangulaire (deux fois plus large que haute) dans laquelle on regarde autour de soi. Objet tournant : un produit qui tourne sur lui-même quand on le fait glisser, avec de l’inertie au relâchement ; à partir d’une seule photo (la photo tourne) ou de la suite de photos prises en tournant autour.'),
    'Foto a 360°': ('360°-Foto', '360° photo', 'Foto de 360°', 'Photo à 360°'),
    'Video a 360°': ('360°-Video', '360° video', 'Vídeo de 360°', 'Vidéo à 360°'),
    'Una foto equirettangolare, larga il doppio dell’altezza: quella che fanno le fotocamere a 360° e molti telefoni in modalità panorama sferico.': ('Ein equirektangulares Foto, doppelt so breit wie hoch: so wie es 360°-Kameras und viele Handys im Kugelpanorama-Modus aufnehmen.', 'An equirectangular photo, twice as wide as it is tall: the kind 360° cameras and many phones in spherical panorama mode take.', 'Una foto equirrectangular, el doble de ancha que de alta: la que hacen las cámaras de 360° y muchos móviles en modo panorama esférico.', 'Une photo équirectangulaire, deux fois plus large que haute : celle que font les caméras à 360° et beaucoup de téléphones en mode panorama sphérique.'),
    'Foto dell’oggetto': ('Foto des Objekts', 'Photo of the object', 'Foto del objeto', 'Photo de l’objet'),
    'Foto della rotazione': ('Fotos der Drehung', 'Rotation photos', 'Fotos de la rotación', 'Photos de la rotation'),
    'In ordine, scattate girando intorno all’oggetto alla stessa distanza: più sono, più il giro è fluido (24–36 vanno bene).': ('Der Reihe nach, im gleichen Abstand rund um das Objekt aufgenommen: je mehr, desto flüssiger die Drehung (24–36 sind gut).', 'In order, taken walking around the object at the same distance: the more there are, the smoother the spin (24–36 work well).', 'En orden, tomadas girando alrededor del objeto a la misma distancia: cuantas más, más fluido el giro (24–36 van bien).', 'Dans l’ordre, prises en tournant autour de l’objet à la même distance : plus il y en a, plus la rotation est fluide (24–36, c’est bien).'),
    'Velocità della rotazione': ('Drehgeschwindigkeit', 'Rotation speed', 'Velocidad de rotación', 'Vitesse de rotation'),
    'Si trascina col mouse': ('Mit der Maus ziehen', 'Drag with the mouse', 'Se arrastra con el ratón', 'Se fait glisser à la souris'),
    'Si trascina col dito': ('Mit dem Finger ziehen', 'Drag with a finger', 'Se arrastra con el dedo', 'Se fait glisser du doigt'),
    'Sensibilità del trascinamento': ('Ziehempfindlichkeit', 'Drag sensitivity', 'Sensibilidad del arrastre', 'Sensibilité du glisser'),
    'Quanto continua a girare dopo averlo lasciato: vicino a 1 gira a lungo, più in basso si ferma subito.': ('Wie lange es nach dem Loslassen weiterdreht: nahe 1 dreht es lange, darunter hält es sofort an.', 'How long it keeps spinning after you let go: close to 1 it spins for a long time, lower it stops at once.', 'Cuánto sigue girando después de soltarlo: cerca de 1 gira mucho, más abajo se para enseguida.', 'Combien de temps il continue de tourner une fois lâché : près de 1 il tourne longtemps, plus bas il s’arrête tout de suite.'),
    'Si orienta muovendo il telefono': ('Richtet sich nach der Bewegung des Handys', 'Turns as you move the phone', 'Se orienta moviendo el móvil', 'S’oriente en bougeant le téléphone'),
    'Mostra l’angolo': ('Winkel anzeigen', 'Show the angle', 'Mostrar el ángulo', 'Afficher l’angle'),
    'Barra dei pulsanti': ('Schaltflächenleiste', 'Button bar', 'Barra de botones', 'Barre de boutons'),
    'Rotazione orizzontale': ('Horizontale Drehung', 'Horizontal rotation', 'Rotación horizontal', 'Rotation horizontale'),
    'Inclinazione verticale': ('Vertikale Neigung', 'Vertical tilt', 'Inclinación vertical', 'Inclinaison verticale'),
    'Quanto si vede del panorama: più è piccolo, più la vista è ravvicinata. Il minimo e il massimo limitano lo zoom di chi guarda.': ('Wie viel vom Panorama zu sehen ist: je kleiner, desto näher die Ansicht. Minimum und Maximum begrenzen den Zoom der Betrachter.', 'How much of the panorama you see: the smaller it is, the closer the view. The minimum and maximum limit the viewer’s zoom.', 'Cuánto se ve del panorama: cuanto más pequeño, más cercana la vista. El mínimo y el máximo limitan el zoom de quien mira.', 'Ce que l’on voit du panorama : plus c’est petit, plus la vue est rapprochée. Le minimum et le maximum limitent le zoom de qui regarde.'),
    'Campo visivo minimo': ('Minimales Sichtfeld', 'Minimum field of view', 'Campo de visión mínimo', 'Champ de vision minimum'),
    'Campo visivo massimo': ('Maximales Sichtfeld', 'Maximum field of view', 'Campo de visión máximo', 'Champ de vision maximum'),
    'Scegli nel Contenuto una foto o un video a 360°: equirettangolare, largo il doppio dell’altezza.': ('Wähle im Inhalt ein 360°-Foto oder -Video: equirektangulär, doppelt so breit wie hoch.', 'Choose a 360° photo or video in the Content tab: equirectangular, twice as wide as it is tall.', 'Elige en el Contenido una foto o un vídeo de 360°: equirrectangular, el doble de ancho que de alto.', 'Choisissez dans le Contenu une photo ou une vidéo à 360° : équirectangulaire, deux fois plus large que haute.'),
    'Nome del primo colore': ('Name der ersten Farbe', 'Name of the first colour', 'Nombre del primer color', 'Nom de la première couleur'),
    'Nome del secondo colore': ('Name der zweiten Farbe', 'Name of the second colour', 'Nombre del segundo color', 'Nom de la deuxième couleur'),
    'Il colore degli stati cliccati una volta.': ('Die Farbe der einmal angeklickten Länder.', 'The colour of countries clicked once.', 'El color de los países pulsados una vez.', 'La couleur des pays cliqués une fois.'),
    'Il colore degli stati cliccati due volte.': ('Die Farbe der zweimal angeklickten Länder.', 'The colour of countries clicked twice.', 'El color de los países pulsados dos veces.', 'La couleur des pays cliqués deux fois.'),
    'Vetrina su fondo chiaro': ('Vitrine auf hellem Grund', 'Showcase on a light background', 'Escaparate sobre fondo claro', 'Vitrine sur fond clair'),
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
