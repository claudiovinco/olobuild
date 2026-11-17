# -*- coding: utf-8 -*-
"""Traduzioni della 1.4.1190 (17 nov 2026): i nomi chiari delle fonti dinamiche e il cartellino del template singolo.
Aggiunge le voci che mancano ai .po di de_DE, en_GB, en_US, es_ES, fr_FR e ricompila i .mo. Si può rilanciare.
uso: python po_1187.py
"""
import os
import subprocess
import sys

ROOT = os.path.normpath(os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', '..'))
LANG = os.path.join(ROOT, 'languages')
MSGFMT = os.path.join(os.path.dirname(sys.executable), 'Tools', 'i18n', 'msgfmt.py')
VERSIONE = 'OLObuild 1.4.1190'

# italiano: (tedesco, inglese, spagnolo, francese)
V = {
    'Contenuto della pagina': ('Inhalt der Seite', 'Page content', 'Contenido de la página', 'Contenu de la page'),
    'Riassunto': ('Kurzfassung', 'Excerpt', 'Resumen', 'Résumé'),
    'Testo': ('Text', 'Text', 'Texto', 'Texte'),
    'Link del contenuto': ('Link zum Inhalt', 'Content link', 'Enlace del contenido', 'Lien du contenu'),
    'Contenuto precedente e successivo': ('Vorheriger und nächster Inhalt', 'Previous and next content', 'Contenido anterior y siguiente', 'Contenu précédent et suivant'),
    'Titolo del precedente': ('Titel des vorherigen', 'Previous title', 'Título del anterior', 'Titre du précédent'),
    'Link del precedente': ('Link zum vorherigen', 'Previous link', 'Enlace del anterior', 'Lien du précédent'),
    'Titolo del successivo': ('Titel des nächsten', 'Next title', 'Título del siguiente', 'Titre du suivant'),
    'Link del successivo': ('Link zum nächsten', 'Next link', 'Enlace del siguiente', 'Lien du suivant'),
    'Prima categoria': ('Erste Kategorie', 'First category', 'Primera categoría', 'Première catégorie'),
    'Singolo': ('Einzelansicht', 'Single', 'Individual', 'Individuel'),
    'Contenuti': ('Inhalte', 'Content', 'Contenidos', 'Contenus'),
    'Tipo di contenuto': ('Inhaltstyp', 'Content type', 'Tipo de contenido', 'Type de contenu'),
    'Anteprima con': ('Vorschau mit', 'Preview with', 'Vista previa con', 'Aperçu avec'),
    'Il più recente': ('Der neueste', 'The most recent', 'El más reciente', 'Le plus récent'),
    'Il contenuto che il canvas usa per mostrare il template: cambialo per vedere lo stesso disegno coi dati di un altro. Sul sito ogni pagina usa il suo.': ('Der Inhalt, mit dem die Arbeitsfläche die Vorlage zeigt: Wechsle ihn, um dasselbe Design mit den Daten eines anderen zu sehen. Auf der Website verwendet jede Seite ihren eigenen.', 'The content the canvas uses to show the template: change it to see the same design with another one’s data. On the site each page uses its own.', 'El contenido que el lienzo usa para mostrar la plantilla: cámbialo para ver el mismo diseño con los datos de otro. En el sitio cada página usa el suyo.', 'Le contenu que le canevas utilise pour afficher le modèle : change-le pour voir le même design avec les données d’un autre. Sur le site, chaque page utilise le sien.'),
    'Filtra per categoria o tag': ('Nach Kategorie oder Schlagwort filtern', 'Filter by category or tag', 'Filtrar por categoría o etiqueta', 'Filtrer par catégorie ou étiquette'),
    '&#9889; Sorgente dinamica accesa: le voci vengono dai contenuti scelti qui sopra': ('&#9889; Dynamische Quelle aktiv: Die Einträge kommen aus den oben gewählten Inhalten', '&#9889; Dynamic source on: the items come from the content chosen above', '&#9889; Fuente dinámica activa: los elementos vienen de los contenidos elegidos arriba', '&#9889; Source dynamique activée : les éléments viennent des contenus choisis ci-dessus'),
    'nessun tipo': ('kein Typ', 'no type', 'ningún tipo', 'aucun type'),
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
