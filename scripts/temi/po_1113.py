# -*- coding: utf-8 -*-
"""Traduzioni della 1.4.1113 (16 nov 2026): Ricerca Live, etichette in italiano e zone dello Stile (episodio 36).
Aggiunge le voci che mancano ai .po di de_DE, en_GB, en_US, es_ES, fr_FR e ricompila i .mo. Si può rilanciare.
uso: python po_1097.py
"""
import os
import subprocess
import sys

ROOT = os.path.normpath(os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', '..'))
LANG = os.path.join(ROOT, 'languages')
MSGFMT = os.path.join(os.path.dirname(sys.executable), 'Tools', 'i18n', 'msgfmt.py')
VERSIONE = 'OLObuild 1.4.1113'

# italiano: (tedesco, inglese, spagnolo, francese)
V = {
    'Testo nel campo': ('Text im Feld', 'Text in the field', 'Texto en el campo', 'Texte dans le champ'),
    'Il suggerimento grigio che si vede nel campo vuoto.': ('Der graue Hinweis, der im leeren Feld steht.', 'The grey hint shown in the empty field.', 'La sugerencia gris que se ve en el campo vacío.', 'L’indication grise visible dans le champ vide.'),
    'Suggerimenti che si alternano': ('Wechselnde Hinweise', 'Rotating hints', 'Sugerencias que se alternan', 'Indications qui alternent'),
    'Suggerimenti': ('Hinweise', 'Hints', 'Sugerencias', 'Indications'),
    'Uno per riga: si scrivono e si cancellano a turno nel campo vuoto.': ('Einer pro Zeile: Sie werden im leeren Feld abwechselnd geschrieben und gelöscht.', 'One per line: they are typed and erased in turn in the empty field.', 'Una por línea: se escriben y se borran por turnos en el campo vacío.', 'Une par ligne : elles s’écrivent et s’effacent à tour de rôle dans le champ vide.'),
    'Comportamento': ('Verhalten', 'Behaviour', 'Comportamiento', 'Comportement'),
    'Estesa': ('Ausgeklappt', 'Expanded', 'Extendida', 'Étendue'),
    'Compatta': ('Kompakt', 'Compact', 'Compacta', 'Compacte'),
    'Finestra': ('Fenster', 'Window', 'Ventana', 'Fenêtre'),
    'In linea': ('Inline', 'Inline', 'En línea', 'En ligne'),
    'Estesa: il campo sempre visibile. Compatta: solo la lente, il campo si apre al clic. Finestra: la lente apre una finestra al centro dello schermo. In linea: il campo senza riquadro, per un mega menu o una colonna laterale.': ('Ausgeklappt: Das Feld ist immer sichtbar. Kompakt: nur die Lupe, das Feld öffnet sich beim Klick. Fenster: Die Lupe öffnet ein Fenster in der Bildschirmmitte. Inline: das Feld ohne Rahmen, für ein Mega-Menü oder eine Seitenspalte.', 'Expanded: the field is always visible. Compact: only the magnifier, the field opens on click. Window: the magnifier opens a window in the centre of the screen. Inline: the field without a box, for a mega menu or a sidebar.', 'Extendida: el campo siempre visible. Compacta: solo la lupa, el campo se abre al hacer clic. Ventana: la lupa abre una ventana en el centro de la pantalla. En línea: el campo sin recuadro, para un mega menú o una columna lateral.', 'Étendue : le champ toujours visible. Compacte : seulement la loupe, le champ s’ouvre au clic. Fenêtre : la loupe ouvre une fenêtre au centre de l’écran. En ligne : le champ sans cadre, pour un méga menu ou une colonne latérale.'),
    'Quante lettere servono prima che parta la ricerca.': ('Wie viele Buchstaben nötig sind, bevor die Suche startet.', 'How many letters are needed before the search starts.', 'Cuántas letras hacen falta antes de que empiece la búsqueda.', 'Combien de lettres il faut avant que la recherche démarre.'),
    'Attesa': ('Wartezeit', 'Wait', 'Espera', 'Attente'),
    'Quanto aspetta dopo l’ultima lettera prima di cercare: evita una ricerca a ogni tasto.': ('Wie lange nach dem letzten Buchstaben gewartet wird, bevor gesucht wird: vermeidet eine Suche bei jedem Tastendruck.', 'How long it waits after the last letter before searching: avoids a search at every key.', 'Cuánto espera tras la última letra antes de buscar: evita una búsqueda a cada tecla.', 'Combien de temps il attend après la dernière lettre avant de chercher : évite une recherche à chaque touche.'),
    'Risultati al massimo': ('Höchstens Ergebnisse', 'Results at most', 'Resultados como máximo', 'Résultats au maximum'),
    'Pagina di tutti i risultati': ('Seite mit allen Ergebnissen', 'All results page', 'Página de todos los resultados', 'Page de tous les résultats'),
    'In fondo ai risultati, un collegamento a questa pagina con la ricerca fatta.': ('Unter den Ergebnissen ein Link zu dieser Seite mit der durchgeführten Suche.', 'Below the results, a link to this page with the search made.', 'Debajo de los resultados, un enlace a esta página con la búsqueda hecha.', 'Sous les résultats, un lien vers cette page avec la recherche effectuée.'),
    'Testo del collegamento': ('Linktext', 'Link text', 'Texto del enlace', 'Texte du lien'),
    'Testo se non trova niente': ('Text, wenn nichts gefunden wird', 'Text when nothing is found', 'Texto si no encuentra nada', 'Texte si rien n’est trouvé'),
    'Termini': ('Begriffe', 'Terms', 'Términos', 'Termes'),
    'Gli slug dei termini, separati da virgola.': ('Die Slugs der Begriffe, durch Komma getrennt.', 'The term slugs, separated by commas.', 'Los slugs de los términos, separados por comas.', 'Les slugs des termes, séparés par des virgules.'),
    'Parole da escludere': ('Auszuschließende Wörter', 'Words to exclude', 'Palabras que excluir', 'Mots à exclure'),
    'I contenuti che le contengono non escono fra i risultati. Separate da virgola.': ('Inhalte, die sie enthalten, erscheinen nicht in den Ergebnissen. Durch Komma getrennt.', 'Content containing them does not appear in the results. Separated by commas.', 'Los contenidos que las contienen no salen en los resultados. Separadas por comas.', 'Les contenus qui les contiennent n’apparaissent pas dans les résultats. Séparés par des virgules.'),
    'Campo': ('Feld', 'Field', 'Campo', 'Champ'),
    'Titolo del risultato': ('Titel des Ergebnisses', 'Result title', 'Título del resultado', 'Titre du résultat'),
    'Lente': ('Lupe', 'Magnifier', 'Lupa', 'Loupe'),
    'Sfondo campo': ('Feldhintergrund', 'Field background', 'Fondo del campo', 'Fond du champ'),
    'Bordo mentre si scrive': ('Rahmen beim Schreiben', 'Border while typing', 'Borde mientras se escribe', 'Bordure pendant la saisie'),
    'Il bordo del campo quando ci si clicca dentro. Vuoto: il primario del sito.': ('Der Rahmen des Felds, wenn man hineinklickt. Leer: die Primärfarbe der Website.', 'The field border when you click into it. Empty: the site’s primary colour.', 'El borde del campo cuando se hace clic dentro. Vacío: el primario del sitio.', 'La bordure du champ quand on clique dedans. Vide : la couleur primaire du site.'),
    'Altezza campo': ('Feldhöhe', 'Field height', 'Altura del campo', 'Hauteur du champ'),
    'Raggio campo': ('Radius Feld', 'Field radius', 'Radio del campo', 'Rayon du champ'),
    'Il pulsante con la sola lente (Compatta e Finestra). Vuoto = lo sfondo del campo.': ('Der Button nur mit der Lupe (Kompakt und Fenster). Leer = der Hintergrund des Felds.', 'The button with only the magnifier (Compact and Window). Empty = the field background.', 'El botón solo con la lupa (Compacta y Ventana). Vacío = el fondo del campo.', 'Le bouton avec la seule loupe (Compacte et Fenêtre). Vide = le fond du champ.'),
    'Colore lente': ('Farbe der Lupe', 'Magnifier colour', 'Color de la lupa', 'Couleur de la loupe'),
    'Larghezza finestra': ('Fensterbreite', 'Window width', 'Ancho de la ventana', 'Largeur de la fenêtre'),
    'Colore dietro la finestra': ('Farbe hinter dem Fenster', 'Colour behind the window', 'Color detrás de la ventana', 'Couleur derrière la fenêtre'),
    'Sfondo risultati': ('Hintergrund der Ergebnisse', 'Results background', 'Fondo de los resultados', 'Fond des résultats'),
    'Bordo risultati': ('Rahmen der Ergebnisse', 'Results border', 'Borde de los resultados', 'Bordure des résultats'),
    'Raggio risultati': ('Radius der Ergebnisse', 'Results radius', 'Radio de los resultados', 'Rayon des résultats'),
    'Sfondo voce al passaggio': ('Hintergrund des Eintrags beim Überfahren', 'Item background on hover', 'Fondo del elemento al pasar', 'Fond de l’élément au survol'),
    'Altezza massima risultati': ('Maximale Höhe der Ergebnisse', 'Maximum results height', 'Altura máxima de los resultados', 'Hauteur maximale des résultats'),
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
