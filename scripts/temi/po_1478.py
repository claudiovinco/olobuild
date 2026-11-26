# -*- coding: utf-8 -*-
"""
La Vetrina dei temi e gli effetti dei temi nuovi nei .po di de_DE, en_GB, en_US, es_ES, fr_FR (+ .mo), 26 nov 2026.
I filtri «Affina» della tile themeshowcase (1.4.1218), le categorie e gli effetti («zone» dei theme.json) dei temi
dal 18 nov in poi, e quelli dei temi di OLObooking, uscivano in italiano in ogni lingua: `olobuild_t()` legge le
stringhe globali di OLOlang e poi questi cataloghi, e qui non c'erano. Le traduzioni sono le stesse caricate il 21 nov
come stringhe globali su olotheme.com/olobuild e olotheme.com/olobooking (sito-2026/lingue/traduzioni-vetrina.json).
Si può rilanciare: aggiunge solo le voci che mancano. Per trovarle: un tema nuovo col suo effetto va aggiunto qui
(o in uno script come questo), se no la Vetrina dei siti in lingua straniera lo mostra in italiano.
"""
import os
import subprocess
import sys

VERSIONE = '1.4.1478'
LANG = r'T:\TECNICA\olobuild\languages'
MSGFMT = os.path.join(os.path.dirname(sys.executable), 'Tools', 'i18n', 'msgfmt.py')

# italiano: (de, en, es, fr)
V = {
    # filtri «Affina» della Vetrina
    'Affina la ricerca': ('Suche verfeinern', 'Refine your search', 'Afinar la búsqueda', 'Affiner la recherche'),
    'Tutti gli effetti': ('Alle Effekte', 'All effects', 'Todos los efectos', 'Tous les effets'),
    'Si muove scorrendo': ('Bewegt sich beim Scrollen', 'Moves as you scroll', 'Se mueve al desplazarte', 'Bouge au défilement'),
    'Cambia al clic': ('Ändert sich per Klick', 'Changes on click', 'Cambia al hacer clic', 'Change au clic'),
    'Si muove da solo': ('Bewegt sich von selbst', 'Moves on its own', 'Se mueve solo', 'Bouge tout seul'),
    'Fondo': ('Hintergrund', 'Background', 'Fondo', 'Fond'),
    'Chiaro e scuro': ('Hell und dunkel', 'Light and dark', 'Claro y oscuro', 'Clair et sombre'),
    'Tutti i caratteri': ('Alle Schriften', 'All typefaces', 'Todas las tipografías', 'Toutes les polices'),
    'Con le grazie': ('Mit Serifen', 'Serif', 'Con serifa', 'Avec empattements'),
    'Senza grazie': ('Ohne Serifen', 'Sans serif', 'Sin serifa', 'Sans empattements'),
    'Decorativo': ('Dekorativ', 'Decorative', 'Decorativa', 'Décoratif'),
    'Tutti i colori': ('Alle Farben', 'All colors', 'Todos los colores', 'Toutes les couleurs'),
    'Blu': ('Blau', 'Blue', 'Azul', 'Bleu'),
    'Rosa': ('Rosa', 'Pink', 'Rosa', 'Rose'),
    'Neutro': ('Neutral', 'Neutral', 'Neutro', 'Neutre'),
    # categorie dei temi
    'Salute e cura': ('Gesundheit & Pflege', 'Health & care', 'Salud y cuidado', 'Santé et soins'),
    'Immobiliare': ('Immobilien', 'Real estate', 'Inmobiliaria', 'Immobilier'),
    'Noleggio': ('Vermietung', 'Rentals', 'Alquiler', 'Location'),
    # effetti dei temi («zone» dei theme.json)
    'Il sito che si stampa': ('Die Website, die sich selbst druckt', 'The site that prints itself', 'El sitio que se imprime', 'Le site qui s’imprime'),
    'Il sito-manifesto': ('Die Plakat-Website', 'The poster site', 'El sitio-cartel', 'Le site-affiche'),
    'Il sito-telefono': ('Die Handy-Website', 'The phone site', 'El sitio-teléfono', 'Le site-téléphone'),
    'Il sito-obiettivo': ('Die Objektiv-Website', 'The lens site', 'El sitio-objetivo', 'Le site-objectif'),
    'Il sito-cartellone': ('Die Plakatwand-Website', 'The billboard site', 'El sitio-valla', 'Le site-panneau'),
    'Il sito-ascensore': ('Die Aufzug-Website', 'The lift site', 'El sitio-ascensor', 'Le site-ascenseur'),
    'Il sito che cambia canale': ('Die Website, die den Kanal wechselt', 'The site that changes channel', 'El sitio que cambia de canal', 'Le site qui change de chaîne'),
    'Il sito al buio': ('Die Website im Dunkeln', 'The site in the dark', 'El sitio a oscuras', 'Le site dans le noir'),
    'Il sito che si sfoglia': ('Die Website zum Durchblättern', 'The site you leaf through', 'El sitio que se hojea', 'Le site qui se feuillette'),
    'Il sito nei due sensi': ('Die Website in beide Richtungen', 'The two-way site', 'El sitio de doble sentido', 'Le site à double sens'),
    'Lo zoom che non finisce': ('Der Zoom ohne Ende', 'The never-ending zoom', 'El zoom que no acaba', 'Le zoom sans fin'),
    'La facciata che si perde': ('Die Fassade, die sich verliert', 'The façade that fades away', 'La fachada que se pierde', 'La façade qui se perd'),
    'Il sito che gira': ('Die Website, die sich dreht', 'The site that spins', 'El sitio que gira', 'Le site qui tourne'),
    'Il sito a tornanti': ('Die Serpentinen-Website', 'The hairpin-bend site', 'El sitio en curvas de herradura', 'Le site en lacets'),
    'La parola che parte': ('Das Wort, das losfliegt', 'The word that takes off', 'La palabra que despega', 'Le mot qui s’envole'),
    'Si legge solo da qui': ('Nur von hier aus lesbar', 'Readable only from here', 'Solo se lee desde aquí', 'Lisible seulement d’ici'),
    'Il mondo a testa in giù': ('Die Welt steht kopf', 'The world upside down', 'El mundo al revés', 'Le monde à l’envers'),
    'Tagliata sottile': ('Hauchdünn geschnitten', 'Thinly sliced', 'En lonchas finas', 'Tranché finement'),
    # effetti dei temi di OLObooking
    'Le visite prenotabili': ('Untersuchungen online buchen', 'Check-ups you can book', 'Las consultas que se reservan', 'Les consultations à réserver'),
    'I corsi prenotabili': ('Kurse online buchen', 'Classes you can book', 'Las clases que se reservan', 'Les cours à réserver'),
    'Auto a noleggio': ('Autovermietung', 'Car rental', 'Alquiler de coches', 'Location de voitures'),
    'Il cielo in calendario': ('Der Himmel im Kalender', 'The sky on the calendar', 'El cielo en el calendario', 'Le ciel au calendrier'),
    'Il tavolo, dal vivo': ('Der Tisch, live', 'The table, live', 'La mesa, en directo', 'La table, en direct'),
    'Case in vendita e in affitto': ('Häuser zum Kauf und zur Miete', 'Homes for sale and for rent', 'Casas en venta y en alquiler', 'Maisons à vendre et à louer'),
    'La poltrona libera, dal vivo': ('Der freie Stuhl, live', 'The free chair, live', 'El sillón libre, en directo', 'Le fauteuil libre, en direct'),
    'Video e parole che cambiano': ('Video und Worte, die sich ändern', 'Video and changing words', 'Vídeo y palabras que cambian', 'Vidéo et mots qui changent'),
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
            if not testo.endswith('\n'):
                nuove[0] = '\n' + nuove[0]
            with open(po, 'a', encoding='utf-8', newline='\n') as f:
                f.write(''.join(nuove))
        subprocess.run([sys.executable, MSGFMT, '-o', po[:-3] + '.mo', po], check=True)
        print(loc, 'voci nuove:', len(nuove))


if __name__ == '__main__':
    main()
