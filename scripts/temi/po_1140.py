# -*- coding: utf-8 -*-
"""Traduzioni della 1.4.1140 (17 nov 2026): Reveal Box con le zone come la Flip card, il pannello Sfondo in italiano (episodio 46).
Aggiunge le voci che mancano ai .po di de_DE, en_GB, en_US, es_ES, fr_FR e ricompila i .mo. Si può rilanciare.
uso: python po_1097.py
"""
import os
import subprocess
import sys

ROOT = os.path.normpath(os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', '..'))
LANG = os.path.join(ROOT, 'languages')
MSGFMT = os.path.join(os.path.dirname(sys.executable), 'Tools', 'i18n', 'msgfmt.py')
VERSIONE = 'OLObuild 1.4.1140'

# italiano: (tedesco, inglese, spagnolo, francese)
V = {
    'Dissolvenza incrociata': ('Überblendung', 'Crossfade', 'Fundido cruzado', 'Fondu enchaîné'),
    'Giro': ('Drehung', 'Flip', 'Giro', 'Retournement'),
    'Zona visibile': ('Sichtbarer Bereich', 'Visible area', 'Zona visible', 'Zone visible'),
    'Zona rivelata': ('Aufgedeckter Bereich', 'Revealed area', 'Zona revelada', 'Zone révélée'),
    'Si ingrandisce e svanisce': ('Vergrößert sich und verblasst', 'Grows and fades', 'Se agranda y desaparece', 'S’agrandit et disparaît'),
    'Si rimpicciolisce e svanisce': ('Verkleinert sich und verblasst', 'Shrinks and fades', 'Se encoge y desaparece', 'Rétrécit et disparaît'),
    'Ruota e svanisce': ('Dreht sich und verblasst', 'Turns and fades', 'Gira y desaparece', 'Tourne et disparaît'),
    'La zona rivelata compare al passaggio del mouse, col tocco sul telefono (un altro tocco la richiude) e col tasto Tab da tastiera.': ('Der aufgedeckte Bereich erscheint beim Überfahren mit der Maus, beim Tippen auf dem Handy (erneutes Tippen schließt ihn) und mit der Tab-Taste.', 'The revealed area appears on mouse hover, on tap on phones (another tap closes it) and with the Tab key.', 'La zona revelada aparece al pasar el ratón, al tocar en el móvil (otro toque la cierra) y con la tecla Tab.', 'La zone révélée apparaît au survol de la souris, au toucher sur le téléphone (un autre toucher la referme) et avec la touche Tab.'),
    'Di quanto scorre la zona visibile. Vuoto o 0: tutta l’altezza del riquadro (a sinistra e a destra, tutta la larghezza).': ('Wie weit der sichtbare Bereich gleitet. Leer oder 0: die ganze Höhe des Kastens (nach links und rechts die ganze Breite).', 'How far the visible area slides. Empty or 0: the full height of the box (left and right, the full width).', 'Cuánto se desliza la zona visible. Vacío o 0: toda la altura del recuadro (a izquierda y derecha, todo el ancho).', 'De combien glisse la zone visible. Vide ou 0 : toute la hauteur du cadre (à gauche et à droite, toute la largeur).'),
    'Rallenta alla fine': ('Bremst am Ende ab', 'Slows down at the end', 'Frena al final', 'Ralentit à la fin'),
    'Fluido': ('Fließend', 'Smooth', 'Fluido', 'Fluide'),
    'Costante': ('Gleichmäßig', 'Steady', 'Constante', 'Constant'),
    'Sfondo comune': ('Gemeinsamer Hintergrund', 'Shared background', 'Fondo común', 'Fond commun'),
    'Dietro tutte e due le zone: si vede dove una zona non ha uno sfondo suo.': ('Hinter beiden Bereichen: sichtbar, wo ein Bereich keinen eigenen Hintergrund hat.', 'Behind both areas: it shows where an area has no background of its own.', 'Detrás de las dos zonas: se ve donde una zona no tiene fondo propio.', 'Derrière les deux zones : il se voit là où une zone n’a pas de fond à elle.'),
    'Testo della zona visibile': ('Text des sichtbaren Bereichs', 'Visible area text', 'Texto de la zona visible', 'Texte de la zone visible'),
    'Testo della zona rivelata': ('Text des aufgedeckten Bereichs', 'Revealed area text', 'Texto de la zona revelada', 'Texte de la zone révélée'),
    'In proporzione: 1 = 20 px.': ('Im Verhältnis: 1 = 20 px.', 'As a ratio: 1 = 20 px.', 'En proporción: 1 = 20 px.', 'En proportion : 1 = 20 px.'),
    'Più è bassa, più il giro è profondo e vicino.': ('Je niedriger, desto tiefer und näher die Drehung.', 'The lower it is, the deeper and closer the turn.', 'Cuanto más baja, más profundo y cercano es el giro.', 'Plus elle est basse, plus la rotation est profonde et proche.'),
    '<h3>La cena in terrazza</h3><p>Il venerdì e il sabato sera</p>': ('<h3>Abendessen auf der Terrasse</h3><p>Freitag- und Samstagabend</p>', '<h3>Dinner on the terrace</h3><p>Friday and Saturday evenings</p>', '<h3>Cena en la terraza</h3><p>Viernes y sábado por la noche</p>', '<h3>Dîner en terrasse</h3><p>Le vendredi et le samedi soir</p>'),
    '<h3>Menu degustazione · 45 €</h3><p>Cinque portate con i vini abbinati, dalle 20. Prenota entro il giovedì.</p>': ('<h3>Degustationsmenü · 45 €</h3><p>Fünf Gänge mit passenden Weinen, ab 20 Uhr. Reservierung bis Donnerstag.</p>', '<h3>Tasting menu · €45</h3><p>Five courses with matching wines, from 8 pm. Book by Thursday.</p>', '<h3>Menú degustación · 45 €</h3><p>Cinco platos con vinos maridados, desde las 20. Reserva antes del jueves.</p>', '<h3>Menu dégustation · 45 €</h3><p>Cinq plats avec les vins assortis, dès 20 h. Réservez avant jeudi.</p>'),
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
