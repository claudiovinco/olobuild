# -*- coding: utf-8 -*-
"""Traduzioni della 1.4.1130 (16 nov 2026): Barra progresso: stili onesti, raggio, unità (episodio 43).
Aggiunge le voci che mancano ai .po di de_DE, en_GB, en_US, es_ES, fr_FR e ricompila i .mo. Si può rilanciare.
uso: python po_1097.py
"""
import os
import subprocess
import sys

ROOT = os.path.normpath(os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', '..'))
LANG = os.path.join(ROOT, 'languages')
MSGFMT = os.path.join(os.path.dirname(sys.executable), 'Tools', 'i18n', 'msgfmt.py')
VERSIONE = 'OLObuild 1.4.1130'

# italiano: (tedesco, inglese, spagnolo, francese)
V = {
    'Barre': ('Balken', 'Bars', 'Barras', 'Barres'),
    'Una per riga, scritta «Etichetta|valore», col valore da 0 a 100: «Clienti soddisfatti|98».': ('Eine pro Zeile, geschrieben «Bezeichnung|Wert», mit dem Wert von 0 bis 100: «Zufriedene Kunden|98».', 'One per line, written «Label|value», with the value from 0 to 100: «Happy customers|98».', 'Una por línea, escrita «Etiqueta|valor», con el valor de 0 a 100: «Clientes satisfechos|98».', 'Une par ligne, écrite « Libellé|valeur », avec la valeur de 0 à 100 : « Clients satisfaits|98 ».'),
    'Il testo al centro della barra o del cerchio. Se è vuoto, nel cerchio compare la percentuale; nella barra la percentuale sta in testa con «Mostra percentuale», e dentro la barra con lo stile «Spessa».': ('Der Text in der Mitte des Balkens oder Kreises. Wenn leer, erscheint im Kreis der Prozentwert; beim Balken steht der Prozentwert mit «Prozent anzeigen» oben, und mit dem Stil «Dick» im Balken.', 'The text in the middle of the bar or circle. If empty, the circle shows the percentage; on the bar the percentage sits above with «Show percentage», and inside the bar with the «Thick» style.', 'El texto en el centro de la barra o del círculo. Si está vacío, en el círculo aparece el porcentaje; en la barra el porcentaje va arriba con «Mostrar porcentaje», y dentro de la barra con el estilo «Gruesa».', 'Le texte au centre de la barre ou du cercle. S’il est vide, le cercle affiche le pourcentage ; sur la barre le pourcentage est au-dessus avec « Afficher le pourcentage », et dans la barre avec le style « Épaisse ».'),
    'Le barre e i cerchi si riempiono quando entrano nello schermo.': ('Balken und Kreise füllen sich, wenn sie ins Bild kommen.', 'Bars and circles fill up when they come into view.', 'Las barras y los círculos se llenan cuando entran en pantalla.', 'Les barres et les cercles se remplissent quand ils entrent à l’écran.'),
    'Le percentuali salgono da zero insieme alle barre.': ('Die Prozentwerte steigen zusammen mit den Balken von null an.', 'The percentages rise from zero along with the bars.', 'Los porcentajes suben desde cero junto con las barras.', 'Les pourcentages montent depuis zéro avec les barres.'),
    'Sottile': ('Schmal', 'Thin', 'Fina', 'Fine'),
    'Classica': ('Klassisch', 'Classic', 'Clásica', 'Classique'),
    'Spessa': ('Dick', 'Thick', 'Gruesa', 'Épaisse'),
    'Sfumata': ('Verlauf', 'Gradient', 'Degradada', 'Dégradée'),
    'Su fondo scuro': ('Auf dunklem Grund', 'On a dark background', 'Sobre fondo oscuro', 'Sur fond sombre'),
    'Cambia insieme altezza, raggio e colori, coi colori del sito. Poi ogni controllo qui sotto si regola da solo.': ('Ändert Höhe, Radius und Farben zusammen, mit den Farben der Website. Danach lässt sich jedes Steuerelement darunter einzeln anpassen.', 'Changes height, radius and colours together, with the site’s colours. Then each control below can be adjusted on its own.', 'Cambia a la vez altura, radio y colores, con los colores del sitio. Luego cada control de abajo se ajusta por separado.', 'Change ensemble hauteur, rayon et couleurs, avec les couleurs du site. Ensuite chaque réglage ci-dessous s’ajuste seul.'),
    'Colore della traccia': ('Farbe der Spur', 'Track colour', 'Color de la pista', 'Couleur de la piste'),
    "La parte vuota dietro la barra, o l'anello dietro il cerchio.": ('Der leere Teil hinter dem Balken oder der Ring hinter dem Kreis.', 'The empty part behind the bar, or the ring behind the circle.', 'La parte vacía detrás de la barra, o el anillo detrás del círculo.', 'La partie vide derrière la barre, ou l’anneau derrière le cercle.'),
    'Barre o cerchi': ('Balken oder Kreise', 'Bars or circles', 'Barras o círculos', 'Barres ou cercles'),
    'Cerchi': ('Kreise', 'Circles', 'Círculos', 'Cercles'),
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
