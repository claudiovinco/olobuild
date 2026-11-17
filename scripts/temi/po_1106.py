# -*- coding: utf-8 -*-
"""Traduzioni della 1.4.1106 (16 nov 2026): Testo su tracciato, etichette pulite, carattere e peso del testo (episodio 33).
Aggiunge le voci che mancano ai .po di de_DE, en_GB, en_US, es_ES, fr_FR e ricompila i .mo. Si può rilanciare.
uso: python po_1097.py
"""
import os
import subprocess
import sys

ROOT = os.path.normpath(os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', '..'))
LANG = os.path.join(ROOT, 'languages')
MSGFMT = os.path.join(os.path.dirname(sys.executable), 'Tools', 'i18n', 'msgfmt.py')
VERSIONE = 'OLObuild 1.4.1106'

# italiano: (tedesco, inglese, spagnolo, francese)
V = {
    'Movimento': ('Bewegung', 'Motion', 'Movimiento', 'Mouvement'),
    'Una volta: il testo entra dalla fine del tracciato quando la tile si vede, e si ferma. Continuo: la frase si ripete e scorre senza fine. Chi nel sistema ha chiesto meno movimento lo vede fermo.': ('Einmal: Der Text kommt vom Ende des Pfads herein, wenn die Kachel sichtbar wird, und bleibt stehen. Fortlaufend: Der Satz wiederholt sich und läuft endlos. Wer im System weniger Bewegung eingestellt hat, sieht ihn still.', 'Once: the text comes in from the end of the path when the tile is in view, and stops. Continuous: the sentence repeats and scrolls forever. Anyone who asked the system for less motion sees it still.', 'Una vez: el texto entra desde el final del trazado cuando el tile se ve, y se para. Continuo: la frase se repite y se desplaza sin fin. Quien pidió menos movimiento en el sistema lo ve quieto.', 'Une fois : le texte entre par la fin du tracé quand la tuile est visible, puis s’arrête. Continu : la phrase se répète et défile sans fin. Qui a demandé moins de mouvement au système le voit immobile.'),
    'Durata': ('Dauer', 'Duration', 'Duración', 'Durée'),
    'Rotazione': ('Drehung', 'Rotation', 'Rotación', 'Rotation'),
    'Ruota tutto': ('Alles drehen', 'Rotate everything', 'Girar todo', 'Tout faire tourner'),
    'Il testo gira in continuazione col suo tracciato, come un timbro ad anello: col Cerchio è perfetto. Chi nel sistema ha chiesto meno movimento lo vede fermo.': ('Der Text dreht sich mit seinem Pfad ununterbrochen, wie ein Ringstempel: mit dem Kreis ist es perfekt. Wer im System weniger Bewegung eingestellt hat, sieht ihn still.', 'The text keeps turning with its path, like a ring stamp: perfect with the Circle. Anyone who asked the system for less motion sees it still.', 'El texto gira sin parar con su trazado, como un sello en anillo: con el Círculo es perfecto. Quien pidió menos movimiento en el sistema lo ve quieto.', 'Le texte tourne sans arrêt avec son tracé, comme un tampon en anneau : avec le Cercle c’est parfait. Qui a demandé moins de mouvement au système le voit immobile.'),
    'Durata di un giro': ('Dauer einer Umdrehung', 'Duration of one turn', 'Duración de una vuelta', 'Durée d’un tour'),
    'Tracciato': ('Pfad', 'Path', 'Trazado', 'Tracé'),
    'Percorso SVG': ('SVG-Pfad', 'SVG path', 'Trazado SVG', 'Tracé SVG'),
    'Il tracciato in formato SVG, cioè l’attributo «d» di un path, in un riquadro largo 300 e alto 100: per esempio M 0 50 Q 150 0 300 50.': ('Der Pfad im SVG-Format, also das Attribut „d“ eines Pfads, in einem Feld 300 breit und 100 hoch: zum Beispiel M 0 50 Q 150 0 300 50.', 'The path in SVG format, i.e. the “d” attribute of a path, in a box 300 wide and 100 high: for example M 0 50 Q 150 0 300 50.', 'El trazado en formato SVG, es decir el atributo «d» de un path, en un recuadro de 300 de ancho y 100 de alto: por ejemplo M 0 50 Q 150 0 300 50.', 'Le tracé au format SVG, c’est-à-dire l’attribut « d » d’un path, dans un cadre de 300 de large et 100 de haut : par exemple M 0 50 Q 150 0 300 50.'),
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
