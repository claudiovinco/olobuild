# -*- coding: utf-8 -*-
"""
Le scritte del manuale e della Guida della bacheca nei .po di de_DE, en_GB, en_US, es_ES, fr_FR (+ .mo): la tile
«Indice argomenti», «Navigazione articoli», la rotta pubblica degli argomenti e la Guida (class-guida.php), che
finora uscivano in italiano in ogni lingua. Si può rilanciare: aggiunge solo le voci che mancano.
"""
import os
import subprocess
import sys

VERSIONE = '1.4.1299'
LANG = r'T:\TECNICA\olobuild\languages'
MSGFMT = os.path.join(os.path.dirname(sys.executable), 'Tools', 'i18n', 'msgfmt.py')

# italiano: (de, en, es, fr)
V = {
    # tile Indice argomenti e rotta pubblica
    'Argomenti': ('Themen', 'Topics', 'Temas', 'Sujets'),
    'Cerca negli argomenti': ('In den Themen suchen', 'Search the topics', 'Buscar en los temas', 'Rechercher dans les sujets'),
    'Nessun argomento con queste parole.': ('Kein Thema mit diesen Wörtern.', 'No topic with these words.', 'Ningún tema con estas palabras.', 'Aucun sujet avec ces mots.'),
    'Sottoargomenti di «%s»': ('Unterthemen von „%s“', 'Subtopics of “%s”', 'Subtemas de «%s»', 'Sous-sujets de « %s »'),
    'Ancora nessun argomento: scrivili nella Redazione.': ('Noch keine Themen: Schreib sie in der Redaktion.', 'No topics yet: write them in the Newsroom.', 'Todavía no hay temas: escríbelos en la Redacción.', 'Pas encore de sujets : écris-les dans la Rédaction.'),
    'Argomento non trovato.': ('Thema nicht gefunden.', 'Topic not found.', 'Tema no encontrado.', 'Sujet introuvable.'),
    # Navigazione articoli
    'Navigazione articoli': ('Beitragsnavigation', 'Post navigation', 'Navegación de artículos', 'Navigation des articles'),
    'Lunghezza titolo': ('Titellänge', 'Title length', 'Longitud del título', 'Longueur du titre'),
    'Colore hover': ('Hover-Farbe', 'Hover colour', 'Color hover', 'Couleur au survol'),
    'Arrotondamento': ('Abrundung', 'Rounding', 'Redondeo', 'Arrondi'),
    # Guida della bacheca
    'Primi passi': ('Erste Schritte', 'Getting started', 'Primeros pasos', 'Premiers pas'),
    'Dalla bacheca alla prima pagina pubblicata: crea, componi con le tile, salva e pubblica.': (
        'Vom Dashboard zur ersten veröffentlichten Seite: anlegen, mit Tiles gestalten, speichern und veröffentlichen.',
        'From the dashboard to your first published page: create, build with tiles, save and publish.',
        'Del panel a la primera página publicada: crea, compón con las tiles, guarda y publica.',
        'Du tableau de bord à la première page publiée : crée, compose avec les tiles, enregistre et publie.'),
    'Pagine, header, footer e blocchi: dove si trovano, a cosa si applicano, revisioni e import.': (
        'Seiten, Header, Footer und Blöcke: wo sie sind, wofür sie gelten, Revisionen und Import.',
        'Pages, headers, footers and blocks: where to find them, what they apply to, revisions and import.',
        'Páginas, cabeceras, pies y bloques: dónde están, a qué se aplican, revisiones e importación.',
        'Pages, en-têtes, pieds de page et blocs : où ils se trouvent, à quoi ils s’appliquent, révisions et import.'),
    'Titolo e descrizione per i motori di ricerca, anteprime social, dati strutturati, redirect e 404.': (
        'Titel und Beschreibung für Suchmaschinen, Social-Vorschauen, strukturierte Daten, Weiterleitungen und 404.',
        'Title and description for search engines, social previews, structured data, redirects and 404s.',
        'Título y descripción para los buscadores, vistas previas en redes, datos estructurados, redirecciones y 404.',
        'Titre et description pour les moteurs de recherche, aperçus sociaux, données structurées, redirections et 404.'),
    'Cosa fa Olobuild da solo per pagine leggere, e cosa conviene fare a te.': (
        'Was Olobuild von selbst für leichte Seiten tut, und was du besser selbst machst.',
        'What Olobuild does on its own to keep pages light, and what’s worth doing yourself.',
        'Lo que Olobuild hace solo para que las páginas sean ligeras, y lo que te conviene hacer a ti.',
        'Ce qu’Olobuild fait tout seul pour des pages légères, et ce qu’il vaut mieux faire toi-même.'),
    'Tutte le guide': ('Alle Anleitungen', 'All guides', 'Todas las guías', 'Tous les guides'),
    'Manuale di OLObuild': ('Handbuch von OLObuild', 'OLObuild manual', 'Manual de OLObuild', 'Manuel d’OLObuild'),
    'Il manuale si legge dal sito di OLObuild, che ora non risponde. Riprova tra poco: intanto le guide brevi del plugin funzionano anche senza rete.': (
        'Das Handbuch kommt von der Website von OLObuild, die gerade nicht antwortet. Versuch es gleich noch einmal: Die kurzen Anleitungen des Plugins funktionieren auch ohne Netz.',
        'The manual is read from the OLObuild website, which isn’t responding right now. Try again shortly: meanwhile the plugin’s short guides work offline too.',
        'El manual se lee desde el sitio de OLObuild, que ahora no responde. Vuelve a intentarlo dentro de poco: mientras tanto, las guías breves del plugin funcionan también sin conexión.',
        'Le manuel se lit depuis le site d’OLObuild, qui ne répond pas pour le moment. Réessaie dans un instant : en attendant, les guides courts de l’extension fonctionnent aussi hors ligne.'),
    'Il manuale è in preparazione. Intanto le guide brevi del plugin spiegano le cose principali.': (
        'Das Handbuch ist in Vorbereitung. Bis dahin erklären die kurzen Anleitungen des Plugins das Wichtigste.',
        'The manual is being prepared. Meanwhile the plugin’s short guides explain the main things.',
        'El manual está en preparación. Mientras tanto, las guías breves del plugin explican lo principal.',
        'Le manuel est en préparation. En attendant, les guides courts de l’extension expliquent l’essentiel.'),
    'Vai alle guide': ('Zu den Anleitungen', 'Go to the guides', 'Ir a las guías', 'Aller aux guides'),
    'Tutto quello che serve sapere, un argomento alla volta, coi video del canale. È lo stesso manuale del sito di OLObuild e si aggiorna da solo.': (
        'Alles, was man wissen muss, ein Thema nach dem anderen, mit den Videos des Kanals. Es ist dasselbe Handbuch wie auf der Website von OLObuild und aktualisiert sich von selbst.',
        'Everything you need to know, one topic at a time, with the channel’s videos. It is the same manual as on the OLObuild website, and it updates itself.',
        'Todo lo que hay que saber, un tema cada vez, con los vídeos del canal. Es el mismo manual del sitio de OLObuild y se actualiza solo.',
        'Tout ce qu’il faut savoir, un sujet à la fois, avec les vidéos de la chaîne. C’est le même manuel que sur le site d’OLObuild, et il se met à jour tout seul.'),
    'Apri il manuale sul sito': ('Handbuch auf der Website öffnen', 'Open the manual on the website', 'Abrir el manual en el sitio', 'Ouvrir le manuel sur le site'),
    'Apri sul sito': ('Auf der Website öffnen', 'Open on the website', 'Abrir en el sitio', 'Ouvrir sur le site'),
    'Video: %s': ('Video: %s', 'Video: %s', 'Vídeo: %s', 'Vidéo : %s'),
    'In questa sezione': ('In diesem Abschnitt', 'In this section', 'En esta sección', 'Dans cette section'),
    'Altri argomenti': ('Weitere Themen', 'More topics', 'Otros temas', 'Autres sujets'),
    'Descrivi il problema: cosa hai fatto, cosa ti aspettavi, cosa è successo.': (
        'Beschreib das Problem: was du gemacht hast, was du erwartet hast, was passiert ist.',
        'Describe the problem: what you did, what you expected, what happened.',
        'Describe el problema: qué hiciste, qué esperabas, qué pasó.',
        'Décris le problème : ce que tu as fait, ce que tu attendais, ce qui s’est passé.'),
    'Tema:': ('Theme:', 'Theme:', 'Tema:', 'Thème :'),
    'Supporto %1$s %2$s': ('Support %1$s %2$s', 'Support %1$s %2$s', 'Soporte %1$s %2$s', 'Assistance %1$s %2$s'),
    'Guide': ('Anleitungen', 'Guides', 'Guías', 'Guides'),
    'Scrivi al supporto': ('An den Support schreiben', 'Write to support', 'Escribir al soporte', 'Écrire à l’assistance'),
    'Le guide di %s, scritte per la versione installata. Ognuna si legge in pochi minuti e rimanda alle schermate che descrive.': (
        'Die Anleitungen von %s, geschrieben für die installierte Version. Jede ist in wenigen Minuten gelesen und führt zu den Bildschirmen, die sie beschreibt.',
        'The %s guides, written for the installed version. Each one takes a few minutes to read and links to the screens it describes.',
        'Las guías de %s, escritas para la versión instalada. Cada una se lee en pocos minutos y lleva a las pantallas que describe.',
        'Les guides de %s, écrits pour la version installée. Chacun se lit en quelques minutes et renvoie aux écrans qu’il décrit.'),
    'Tutto quello che serve sapere, un argomento alla volta, coi video del canale. Si legge dal sito di OLObuild e si aggiorna da solo.': (
        'Alles, was man wissen muss, ein Thema nach dem anderen, mit den Videos des Kanals. Es kommt von der Website von OLObuild und aktualisiert sich von selbst.',
        'Everything you need to know, one topic at a time, with the channel’s videos. It is read from the OLObuild website and updates itself.',
        'Todo lo que hay que saber, un tema cada vez, con los vídeos del canal. Se lee desde el sitio de OLObuild y se actualiza solo.',
        'Tout ce qu’il faut savoir, un sujet à la fois, avec les vidéos de la chaîne. Il se lit depuis le site d’OLObuild et se met à jour tout seul.'),
    'Argomenti e sottoargomenti': ('Themen und Unterthemen', 'Topics and subtopics', 'Temas y subtemas', 'Sujets et sous-sujets'),
    'Non trovi la risposta?': ('Keine Antwort gefunden?', 'Can’t find the answer?', '¿No encuentras la respuesta?', 'Tu ne trouves pas la réponse ?'),
    'Scritta per %1$s %2$s': ('Geschrieben für %1$s %2$s', 'Written for %1$s %2$s', 'Escrita para %1$s %2$s', 'Écrit pour %1$s %2$s'),
    'Altre guide': ('Weitere Anleitungen', 'More guides', 'Otras guías', 'Autres guides'),
    'Guida precedente': ('Vorherige Anleitung', 'Previous guide', 'Guía anterior', 'Guide précédent'),
    'Guida successiva': ('Nächste Anleitung', 'Next guide', 'Guía siguiente', 'Guide suivant'),
    'Installata: %s': ('Installiert: %s', 'Installed: %s', 'Instalada: %s', 'Installée : %s'),
    'Cosa cambia in ogni versione, dalla più recente.': ('Was sich in jeder Version ändert, von der neuesten an.', 'What changes in each version, newest first.', 'Qué cambia en cada versión, desde la más reciente.', 'Ce qui change dans chaque version, de la plus récente à la plus ancienne.'),
    'Il registro delle novità non è incluso in questa installazione.': ('Das Änderungsprotokoll ist in dieser Installation nicht enthalten.', 'The changelog isn’t included in this installation.', 'El registro de novedades no está incluido en esta instalación.', 'Le journal des nouveautés n’est pas inclus dans cette installation.'),
    'Scrivi al supporto. L\'email parte già con la versione di %s, WordPress e PHP, così possiamo rispondere senza chiedertele.': (
        'Schreib an den Support. Die E-Mail enthält schon die Version von %s, WordPress und PHP, damit wir antworten können, ohne danach zu fragen.',
        'Write to support. The email already includes the %s, WordPress and PHP versions, so we can reply without asking you for them.',
        'Escribe al soporte. El email ya sale con la versión de %s, WordPress y PHP, así podemos responder sin pedírtelas.',
        'Écris à l’assistance. L’e-mail part déjà avec la version de %s, WordPress et PHP, pour qu’on puisse répondre sans te les demander.'),
}
# plurale (_n): italiano singolare, plurale -> (de, en, es, fr) come coppie
P = {
    ('%d minuto di lettura', '%d minuti di lettura'): (
        ('%d Minute Lesezeit', '%d Minuten Lesezeit'),
        ('%d minute of reading', '%d minutes of reading'),
        ('%d minuto de lectura', '%d minutos de lectura'),
        ('%d minute de lecture', '%d minutes de lecture')),
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
        for (s, pl), tr in P.items():
            if '\nmsgid "%s"\nmsgid_plural' % esc(s) in testo:
                continue
            nuove.append('\n#: %s\nmsgid "%s"\nmsgid_plural "%s"\nmsgstr[0] "%s"\nmsgstr[1] "%s"\n' % (VERSIONE, esc(s), esc(pl), esc(tr[i][0]), esc(tr[i][1])))
        if nuove:
            with open(po, 'a', encoding='utf-8', newline='\n') as f:
                f.write(''.join(nuove))
        subprocess.run([sys.executable, MSGFMT, '-o', po[:-3] + '.mo', po], check=True)
        print(loc, 'voci nuove:', len(nuove))


if __name__ == '__main__':
    main()
