# -*- coding: utf-8 -*-
"""Traduzioni della 1.4.1120 (16 nov 2026): Barra Annuncio, etichette senza parentesi e zone dello Stile (episodio 38).
Aggiunge le voci che mancano ai .po di de_DE, en_GB, en_US, es_ES, fr_FR e ricompila i .mo. Si può rilanciare.
uso: python po_1097.py
"""
import os
import subprocess
import sys

ROOT = os.path.normpath(os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', '..'))
LANG = os.path.join(ROOT, 'languages')
MSGFMT = os.path.join(os.path.dirname(sys.executable), 'Tools', 'i18n', 'msgfmt.py')
VERSIONE = 'OLObuild 1.4.1120'

# italiano: (tedesco, inglese, spagnolo, francese)
V = {
    'Parte evidenziata': ('Hervorgehobener Teil', 'Highlighted part', 'Parte destacada', 'Partie mise en avant'),
    'Le parole dopo il testo, nel colore d’accento: la novità, l’offerta, la data.': ('Die Wörter nach dem Text, in der Akzentfarbe: die Neuheit, das Angebot, das Datum.', 'The words after the text, in the accent colour: the news, the offer, the date.', 'Las palabras tras el texto, en el color de acento: la novedad, la oferta, la fecha.', 'Les mots après le texte, dans la couleur d’accent : la nouveauté, l’offre, la date.'),
    'Facoltativo: con un link tutta la barra si può cliccare.': ('Optional: Mit einem Link ist die ganze Leiste anklickbar.', 'Optional: with a link the whole bar is clickable.', 'Opcional: con un enlace toda la barra se puede pulsar.', 'Facultatif : avec un lien toute la barre est cliquable.'),
    'Si può chiudere': ('Schließbar', 'Can be closed', 'Se puede cerrar', 'Peut être fermée'),
    'Una × a destra: chi la chiude non la rivede su quel dispositivo, finché il testo non cambia.': ('Ein × rechts: Wer sie schließt, sieht sie auf dem Gerät nicht wieder, bis sich der Text ändert.', 'An × on the right: whoever closes it won’t see it again on that device until the text changes.', 'Una × a la derecha: quien la cierra no la vuelve a ver en ese dispositivo hasta que cambia el texto.', 'Une × à droite : qui la ferme ne la revoit plus sur cet appareil tant que le texte ne change pas.'),
    'Colore di fondo': ('Hintergrundfarbe', 'Background colour', 'Color de fondo', 'Couleur de fond'),
    'Il colore della parte evidenziata. Vuoto: il primario del sito.': ('Die Farbe des hervorgehobenen Teils. Leer: die Primärfarbe der Website.', 'The colour of the highlighted part. Empty: the site’s primary colour.', 'El color de la parte destacada. Vacío: el primario del sitio.', 'La couleur de la partie mise en avant. Vide : la couleur primaire du site.'),
    'Filo sotto': ('Linie unten', 'Bottom line', 'Línea inferior', 'Filet en bas'),
    'Una riga sottile lungo il bordo inferiore, per staccare la barra dal menu. 0: nessuna.': ('Eine feine Linie am unteren Rand, um die Leiste vom Menü abzusetzen. 0: keine.', 'A thin line along the bottom edge, to set the bar apart from the menu. 0: none.', 'Una línea fina a lo largo del borde inferior, para separar la barra del menú. 0: ninguna.', 'Une fine ligne le long du bord inférieur, pour détacher la barre du menu. 0 : aucune.'),
    'Colore del filo': ('Farbe der Linie', 'Line colour', 'Color de la línea', 'Couleur du filet'),
    'Spedizione gratuita sopra i 50 € · ': ('Kostenloser Versand ab 50 € · ', 'Free shipping over €50 · ', 'Envío gratuito a partir de 50 € · ', 'Livraison gratuite dès 50 € · '),
    'Scopri la nuova collezione': ('Entdecke die neue Kollektion', 'Discover the new collection', 'Descubre la nueva colección', 'Découvrez la nouvelle collection'),
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
