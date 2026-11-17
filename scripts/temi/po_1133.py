# -*- coding: utf-8 -*-
"""Traduzioni della 1.4.1133 (16 nov 2026): Contatore: stili onesti, zone dello Stile, velo (episodio 44).
Aggiunge le voci che mancano ai .po di de_DE, en_GB, en_US, es_ES, fr_FR e ricompila i .mo. Si può rilanciare.
uso: python po_1097.py
"""
import os
import subprocess
import sys

ROOT = os.path.normpath(os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', '..'))
LANG = os.path.join(ROOT, 'languages')
MSGFMT = os.path.join(os.path.dirname(sys.executable), 'Tools', 'i18n', 'msgfmt.py')
VERSIONE = 'OLObuild 1.4.1133'

# italiano: (tedesco, inglese, spagnolo, francese)
V = {
    'Semplice': ('Einfach', 'Simple', 'Sencillo', 'Simple'),
    'Riquadro tenue': ('Zarter Kasten', 'Soft box', 'Recuadro suave', 'Encadré doux'),
    'Scheda': ('Karte', 'Card', 'Tarjeta', 'Carte'),
    'Pieno': ('Voll', 'Solid', 'Lleno', 'Plein'),
    'Scuro': ('Dunkel', 'Dark', 'Oscuro', 'Sombre'),
    'Cambia insieme sfondo, testi, raggio e spazi, coi colori del sito. Poi ogni controllo qui sotto si regola da solo.': ('Ändert Hintergrund, Texte, Radius und Abstände zusammen, mit den Farben der Website. Danach lässt sich jedes Steuerelement darunter einzeln anpassen.', 'Changes background, texts, radius and spacing together, with the site’s colours. Then each control below can be adjusted on its own.', 'Cambia a la vez fondo, textos, radio y espacios, con los colores del sitio. Luego cada control de abajo se ajusta por separado.', 'Change ensemble fond, textes, rayon et espaces, avec les couleurs du site. Ensuite chaque réglage ci-dessous s’ajuste seul.'),
    'Immagine, video, gradiente o colore dietro il numero.': ('Bild, Video, Verlauf oder Farbe hinter der Zahl.', 'Image, video, gradient or colour behind the number.', 'Imagen, vídeo, degradado o color detrás del número.', 'Image, vidéo, dégradé ou couleur derrière le nombre.'),
    'Velo scuro sul media': ('Dunkler Schleier über dem Medium', 'Dark veil over the media', 'Velo oscuro sobre el medio', 'Voile sombre sur le média'),
    'Colore del velo': ('Farbe des Schleiers', 'Veil colour', 'Color del velo', 'Couleur du voile'),
    'Opacità del velo': ('Deckkraft des Schleiers', 'Veil opacity', 'Opacidad del velo', 'Opacité du voile'),
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
