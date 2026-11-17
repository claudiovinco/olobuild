# -*- coding: utf-8 -*-
"""Traduzioni della 1.4.1102 (16 nov 2026): Particelle, etichette senza parentesi né «Preset» (episodio 32).
Aggiunge le voci che mancano ai .po di de_DE, en_GB, en_US, es_ES, fr_FR e ricompila i .mo. Si può rilanciare.
uso: python po_1097.py
"""
import os
import subprocess
import sys

ROOT = os.path.normpath(os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', '..'))
LANG = os.path.join(ROOT, 'languages')
MSGFMT = os.path.join(os.path.dirname(sys.executable), 'Tools', 'i18n', 'msgfmt.py')
VERSIONE = 'OLObuild 1.4.1102'

# italiano: (tedesco, inglese, spagnolo, francese)
V = {
    'Particelle': ('Partikel', 'Particles', 'Partículas', 'Particules'),
    'Sfondo della sezione': ('Hintergrund des Abschnitts', 'Section background', 'Fondo de la sección', 'Fond de la section'),
    'Tutta la pagina': ('Ganze Seite', 'Whole page', 'Toda la página', 'Toute la page'),
    'Sopra il contenuto': ('Über dem Inhalt', 'Over the content', 'Sobre el contenido', 'Au-dessus du contenu'),
    "Sfondo della sezione: le particelle stanno dietro a ciò che la sezione mostra. Tutta la pagina: le particelle vivono su tutto il documento e scorrono con esso, dietro al contenuto, senza mai bloccare i click. Sopra il contenuto: le particelle passano davanti a ciò che la sezione mostra (anche a uno sfondo disegnato da una tile, come quello di un'apertura), senza bloccare i click.": ('Hintergrund des Abschnitts: Die Partikel liegen hinter dem, was der Abschnitt zeigt. Ganze Seite: Die Partikel leben auf dem ganzen Dokument und scrollen mit, hinter dem Inhalt, ohne je Klicks zu blockieren. Über dem Inhalt: Die Partikel ziehen vor dem vorbei, was der Abschnitt zeigt (auch vor einem von einer Kachel gezeichneten Hintergrund, etwa dem eines Openers), ohne Klicks zu blockieren.', 'Section background: the particles sit behind what the section shows. Whole page: the particles live on the whole document and scroll with it, behind the content, never blocking clicks. Over the content: the particles pass in front of what the section shows (even a background drawn by a tile, like a hero’s), without blocking clicks.', 'Fondo de la sección: las partículas quedan detrás de lo que muestra la sección. Toda la página: las partículas viven en todo el documento y se desplazan con él, detrás del contenido, sin bloquear nunca los clics. Sobre el contenido: las partículas pasan delante de lo que muestra la sección (también de un fondo dibujado por un tile, como el de una portada), sin bloquear los clics.', 'Fond de la section : les particules restent derrière ce que montre la section. Toute la page : les particules vivent sur tout le document et défilent avec lui, derrière le contenu, sans jamais bloquer les clics. Au-dessus du contenu : les particules passent devant ce que montre la section (même un fond dessiné par une tuile, comme celui d’une ouverture), sans bloquer les clics.'),
    'Tipo': ('Typ', 'Type', 'Tipo', 'Type'),
    'Petali': ('Blütenblätter', 'Petals', 'Pétalos', 'Pétales'),
    'Bolle': ('Blasen', 'Bubbles', 'Burbujas', 'Bulles'),
    'Stelle': ('Sterne', 'Stars', 'Estrellas', 'Étoiles'),
    'Coriandoli': ('Konfetti', 'Confetti', 'Confeti', 'Confettis'),
    'Braci': ('Glut', 'Embers', 'Brasas', 'Braises'),
    'Scintille': ('Funken', 'Sparks', 'Chispas', 'Étincelles'),
    'Petali, neve, pioggia e palloni cadono; bolle e braci salgono; le stelle restano sospese; i coriandoli scoppiano una volta; le scintille schizzano da un punto e ricadono.': ('Blütenblätter, Schnee, Regen und Bälle fallen; Blasen und Glut steigen auf; Sterne schweben; Konfetti platzt einmal; Funken sprühen von einem Punkt und fallen zurück.', 'Petals, snow, rain and balls fall; bubbles and embers rise; stars stay suspended; confetti bursts once; sparks shoot from a point and fall back.', 'Pétalos, nieve, lluvia y balones caen; burbujas y brasas suben; las estrellas quedan suspendidas; el confeti estalla una vez; las chispas saltan desde un punto y vuelven a caer.', 'Pétales, neige, pluie et ballons tombent ; bulles et braises montent ; les étoiles restent suspendues ; les confettis éclatent une fois ; les étincelles jaillissent d’un point et retombent.'),
    'Vento': ('Wind', 'Wind', 'Viento', 'Vent'),
    'Quanto le particelle derivano di lato mentre si muovono.': ('Wie stark die Partikel beim Bewegen seitlich abdriften.', 'How much the particles drift sideways as they move.', 'Cuánto se desvían de lado las partículas mientras se mueven.', 'À quel point les particules dérivent sur le côté en bougeant.'),
    'Gravità': ('Schwerkraft', 'Gravity', 'Gravedad', 'Gravité'),
    'Distanza delle linee': ('Abstand der Linien', 'Line distance', 'Distancia de las líneas', 'Distance des lignes'),
    'Dimensione particelle': ('Partikelgröße', 'Particle size', 'Tamaño de las partículas', 'Taille des particules'),
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
