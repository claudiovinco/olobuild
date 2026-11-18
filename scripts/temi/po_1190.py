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
    # 1.4.1207 (18 nov 2026): «tile» anche nella pagina vuota e nella finestra d'inserimento, le (i) del menu Nuovo
    # Template, la spiegazione di «Attiva» del template singolo
    'Inserisci una tile o una riga': ('Eine Tile oder eine Zeile einfügen', 'Insert a tile or a row', 'Insertar un tile o una fila', 'Insérer une tile ou une rangée'),
    'Nessuna tile trovata': ('Keine Tile gefunden', 'No tiles found', 'No se ha encontrado ningún tile', 'Aucune tile trouvée'),
    'Nuova tile': ('Neue Tile', 'New tile', 'Nuevo tile', 'Nouvelle tile'),
    'Cerca una tile': ('Tile suchen', 'Search a tile', 'Buscar un tile', 'Chercher une tile'),
    'Aggiungi una tile o scegli un layout per iniziare': ('Füge eine Tile hinzu oder wähle ein Layout, um zu beginnen', 'Add a tile or choose a layout to get started', 'Añade un tile o elige un diseño para empezar', 'Ajoute une tile ou choisis une mise en page pour commencer'),
    'Aggiungi una tile': ('Tile hinzufügen', 'Add a tile', 'Añadir un tile', 'Ajouter une tile'),
    'Le pagine e le parti di tutto il sito.': ('Die Seiten und die Teile der ganzen Website.', 'The pages and the parts of the whole site.', 'Las páginas y las partes de todo el sitio.', 'Les pages et les parties de tout le site.'),
    'Pagina: una pagina col suo indirizzo, come Home o Contatti. «Pubblica» la mette sul sito.': ('Seite: eine Seite mit eigener Adresse, wie Startseite oder Kontakt. „Veröffentlichen“ bringt sie auf die Website.', 'Page: a page with its own address, like Home or Contact. “Publish” puts it on the site.', 'Página: una página con su dirección, como Inicio o Contacto. «Publicar» la pone en el sitio.', 'Page : une page avec son adresse, comme Accueil ou Contact. « Publier » la met sur le site.'),
    'Header e Footer: la testata e il piè di pagina di tutte le pagine, dopo «Attiva».': ('Header und Footer: Kopf- und Fußbereich aller Seiten, nach „Aktivieren“.', 'Header and Footer: the header and footer of every page, after “Activate”.', 'Header y Footer: la cabecera y el pie de todas las páginas, después de «Activar».', 'Header et Footer : l’en-tête et le pied de page de toutes les pages, après « Activer ».'),
    'Mega Panel: il pannello grande che si apre da una voce del menu.': ('Mega Panel: das große Panel, das sich von einem Menüpunkt aus öffnet.', 'Mega Panel: the large panel that opens from a menu item.', 'Mega Panel: el panel grande que se abre desde un elemento del menú.', 'Mega Panel : le grand panneau qui s’ouvre depuis un élément du menu.'),
    'Widget: un blocco da riusare dentro altre tile, come le schede o la fisarmonica.': ('Widget: ein Block, den du in anderen Tiles wiederverwendest, etwa in Tabs oder im Akkordeon.', 'Widget: a block to reuse inside other tiles, such as tabs or the accordion.', 'Widget: un bloque para reutilizar dentro de otros tiles, como las pestañas o el acordeón.', 'Widget : un bloc à réutiliser dans d’autres tiles, comme les onglets ou l’accordéon.'),
    '404: la pagina che compare quando un indirizzo non esiste.': ('404: die Seite, die erscheint, wenn eine Adresse nicht existiert.', '404: the page shown when an address doesn’t exist.', '404: la página que aparece cuando una dirección no existe.', '404 : la page qui s’affiche quand une adresse n’existe pas.'),
    'La pagina di ogni contenuto di un tipo: un solo disegno per tutti gli articoli, tutti i prodotti, tutti gli eventi.': ('Die Seite jedes Inhalts eines Typs: ein einziger Entwurf für alle Beiträge, alle Produkte, alle Veranstaltungen.', 'The page of every item of a type: one design for all posts, all products, all events.', 'La página de cada contenido de un tipo: un solo diseño para todas las entradas, todos los productos, todos los eventos.', 'La page de chaque contenu d’un type : une seule mise en page pour tous les articles, tous les produits, tous les événements.'),
    'Col fulmine ogni parte prende i dati del contenuto aperto. Dopo «Pubblica», «Attiva» lo usa sul sito.': ('Mit dem Blitz übernimmt jeder Teil die Daten des geöffneten Inhalts. Nach „Veröffentlichen“ setzt „Aktivieren“ es auf der Website ein.', 'With the lightning bolt each part takes the data of the open item. After “Publish”, “Activate” uses it on the site.', 'Con el rayo cada parte toma los datos del contenido abierto. Después de «Publicar», «Activar» lo usa en el sitio.', 'Avec l’éclair, chaque partie prend les données du contenu ouvert. Après « Publier », « Activer » l’utilise sur le site.'),
    "La pagina che elenca i contenuti di un tipo, per esempio /eventi/: una griglia con «Segui l'archivio aperto» mostra quelli della pagina.": ('Die Seite, die die Inhalte eines Typs auflistet, zum Beispiel /eventi/: Ein Raster mit „Dem geöffneten Archiv folgen“ zeigt die Inhalte dieser Seite.', 'The page that lists the items of a type, for example /eventi/: a grid with “Follow the open archive” shows the ones on that page.', 'La página que enumera los contenidos de un tipo, por ejemplo /eventi/: una cuadrícula con «Seguir el archivo abierto» muestra los de esa página.', 'La page qui liste les contenus d’un type, par exemple /eventi/ : une grille avec « Suivre l’archive ouverte » affiche ceux de la page.'),
    '«Tutti gli archivi» vale per gli elenchi che non hanno un template loro: categorie, tag, autori, date.': ('„Alle Archive“ gilt für die Listen ohne eigenes Template: Kategorien, Schlagwörter, Autoren, Daten.', '“All archives” applies to the lists without a template of their own: categories, tags, authors, dates.', '«Todos los archivos» vale para las listas que no tienen una plantilla propia: categorías, etiquetas, autores, fechas.', '« Toutes les archives » vaut pour les listes qui n’ont pas de modèle à elles : catégories, étiquettes, auteurs, dates.'),
    'Il sito usa questo template per la pagina di ogni contenuto': ('Die Website nutzt dieses Template für die Seite jedes Inhalts', 'The site uses this template for the page of every item', 'El sitio usa esta plantilla para la página de cada contenido', 'Le site utilise ce modèle pour la page de chaque contenu'),
    'Le voci sono tante quanti i tipi di contenuto: ogni tipo che crei per la Redazione (Impostazioni › Tipi di contenuto) aggiunge qui la sua.': ('Es gibt so viele Einträge wie Inhaltstypen: Jeder Typ, den du für die Redaktion anlegst (Einstellungen › Inhaltstypen), fügt hier seinen hinzu.', 'There are as many items as content types: every type you create for the Newsroom (Settings › Content types) adds its own here.', 'Hay tantas opciones como tipos de contenido: cada tipo que creas para la Redacción (Configuración › Tipos de contenido) añade aquí la suya.', 'Il y a autant d’entrées que de types de contenu : chaque type que tu crées pour la Rédaction (Paramètres › Types de contenu) ajoute ici la sienne.'),
    'Anche qui, ogni tipo di contenuto che crei per la Redazione aggiunge la sua voce.': ('Auch hier fügt jeder Inhaltstyp, den du für die Redaktion anlegst, seinen Eintrag hinzu.', 'Here too, every content type you create for the Newsroom adds its own item.', 'También aquí, cada tipo de contenido que creas para la Redacción añade su opción.', 'Ici aussi, chaque type de contenu que tu crées pour la Rédaction ajoute son entrée.'),
    'Cerca una tile da inserire…': ('Tile zum Einfügen suchen…', 'Search a tile to insert…', 'Buscar un tile para insertar…', 'Chercher une tile à insérer…'),
    'Cerca una tile da aggiungere o già nel canvas…': ('Tile zum Hinzufügen oder im Canvas suchen…', 'Search a tile to add or already on the canvas…', 'Buscar un tile para añadir o ya en el lienzo…', 'Chercher une tile à ajouter ou déjà sur le canevas…'),
    'Nessun risultato per': ('Keine Ergebnisse für', 'No results for', 'Ningún resultado para', 'Aucun résultat pour'),
    'Scrivi il nome di una tile da aggiungere o già nel canvas': ('Gib den Namen einer Tile ein, die du hinzufügen willst oder die schon im Canvas ist', 'Type the name of a tile to add or already on the canvas', 'Escribe el nombre de un tile para añadir o ya en el lienzo', 'Écris le nom d’une tile à ajouter ou déjà sur le canevas'),
    'Copia una tile dal canvas': ('Eine Tile aus dem Canvas kopieren', 'Copy a tile from the canvas', 'Copiar un tile del lienzo', 'Copier une tile du canevas'),
    'Tile nel canvas': ('Tiles im Canvas', 'Tiles on the canvas', 'Tiles en el lienzo', 'Tiles sur le canevas'),
    'Usa questo template per la pagina di ogni contenuto': ('Dieses Template für die Seite jedes Inhalts verwenden', 'Use this template for the page of every item', 'Usar esta plantilla para la página de cada contenido', 'Utiliser ce modèle pour la page de chaque contenu'),
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
    '(campo)': ('(Feld)', '(field)', '(campo)', '(champ)'),
    'Data di pubblicazione': ('Veröffentlichungsdatum', 'Publication date', 'Fecha de publicación', 'Date de publication'),
    'Dall’ultima data': ('Vom letzten Datum', 'From the latest date', 'Desde la última fecha', 'De la dernière date'),
    'Dalla prima data': ('Vom ersten Datum', 'From the earliest date', 'Desde la primera fecha', 'De la première date'),
    'Dall’ora più tarda': ('Von der spätesten Uhrzeit', 'From the latest time', 'Desde la hora más tardía', 'De l’heure la plus tardive'),
    'Dall’ora più presto': ('Von der frühesten Uhrzeit', 'From the earliest time', 'Desde la hora más temprana', 'De l’heure la plus tôt'),
    'Tipo di contenuto': ('Inhaltstyp', 'Content type', 'Tipo de contenido', 'Type de contenu'),
    'Anteprima con': ('Vorschau mit', 'Preview with', 'Vista previa con', 'Aperçu avec'),
    'Il più recente': ('Der neueste', 'The most recent', 'El más reciente', 'Le plus récent'),
    'Il contenuto che il canvas usa per mostrare il template: cambialo per vedere lo stesso disegno coi dati di un altro. Sul sito ogni pagina usa il suo.': ('Der Inhalt, mit dem die Arbeitsfläche die Vorlage zeigt: Wechsle ihn, um dasselbe Design mit den Daten eines anderen zu sehen. Auf der Website verwendet jede Seite ihren eigenen.', 'The content the canvas uses to show the template: change it to see the same design with another one’s data. On the site each page uses its own.', 'El contenido que el lienzo usa para mostrar la plantilla: cámbialo para ver el mismo diseño con los datos de otro. En el sitio cada página usa el suyo.', 'Le contenu que le canevas utilise pour afficher le modèle : change-le pour voir le même design avec les données d’un autre. Sur le site, chaque page utilise le sien.'),
    'Filtra per categoria o tag': ('Nach Kategorie oder Schlagwort filtern', 'Filter by category or tag', 'Filtrar por categoría o etiqueta', 'Filtrer par catégorie ou étiquette'),
    '&#9889; Sorgente dinamica accesa: le voci vengono dalla scelta qui sopra': ('&#9889; Dynamische Quelle aktiv: Die Einträge kommen aus der Auswahl oben', '&#9889; Dynamic source on: the items come from the choice above', '&#9889; Fuente dinámica activa: los elementos vienen de la elección de arriba', '&#9889; Source dynamique activée : les éléments viennent du choix ci-dessus'),
    'nessun tipo': ('kein Typ', 'no type', 'ningún tipo', 'aucun type'),
    # 1.4.1216: lo sfondo dal contenuto (il fulmine su foto, video e galleria dello Sfondo)
    'Scelte a mano': ('Von Hand ausgewählt', 'Chosen by hand', 'Elegidas a mano', 'Choisies à la main'),
    'Col fulmine lo sfondo prende la foto dal contenuto che la pagina mostra: in un template Single, l\'immagine in evidenza o un campo foto di ogni evento, notizia o prodotto. Se il contenuto non ne ha, resta la foto scelta a mano.': ('Mit dem Blitz nimmt der Hintergrund das Foto aus dem Inhalt, den die Seite zeigt: in einer Single-Vorlage das Beitragsbild oder ein Fotofeld jeder Veranstaltung, Nachricht oder jedes Produkts. Hat der Inhalt keines, bleibt das von Hand gewählte Foto.', 'With the lightning bolt the background takes the photo from the content the page shows: in a Single template, the featured image or a photo field of each event, news item or product. If the content has none, the photo chosen by hand stays.', 'Con el rayo el fondo toma la foto del contenido que muestra la página: en una plantilla Single, la imagen destacada o un campo de foto de cada evento, noticia o producto. Si el contenido no tiene, queda la foto elegida a mano.', 'Avec l’éclair, le fond prend la photo du contenu que la page affiche : dans un modèle Single, l’image mise en avant ou un champ photo de chaque événement, actualité ou produit. Si le contenu n’en a pas, la photo choisie à la main reste.'),
    'Col fulmine lo sfondo prende il video dal contenuto che la pagina mostra, per esempio il trailer di ogni evento. Serve un file video della libreria: i link di YouTube e Vimeo vanno nella tile Video. Se il contenuto non ne ha, resta il video scelto a mano.': ('Mit dem Blitz nimmt der Hintergrund das Video aus dem Inhalt, den die Seite zeigt, zum Beispiel den Trailer jeder Veranstaltung. Es braucht eine Videodatei aus der Mediathek: Links von YouTube und Vimeo gehören in die Video-Kachel. Hat der Inhalt keines, bleibt das von Hand gewählte Video.', 'With the lightning bolt the background takes the video from the content the page shows, for example the trailer of each event. It needs a video file from the library: YouTube and Vimeo links go in the Video tile. If the content has none, the video chosen by hand stays.', 'Con el rayo el fondo toma el vídeo del contenido que muestra la página, por ejemplo el tráiler de cada evento. Hace falta un archivo de vídeo de la biblioteca: los enlaces de YouTube y Vimeo van en el tile Vídeo. Si el contenido no tiene, queda el vídeo elegido a mano.', 'Avec l’éclair, le fond prend la vidéo du contenu que la page affiche, par exemple la bande-annonce de chaque événement. Il faut un fichier vidéo de la médiathèque : les liens YouTube et Vimeo vont dans la tuile Vidéo. Si le contenu n’en a pas, la vidéo choisie à la main reste.'),
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
