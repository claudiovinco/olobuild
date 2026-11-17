# -*- coding: utf-8 -*-
"""Traduzioni della 1.4.1076 (16 nov 2026): la configurazione iniziale (Olobuild_Setup_Wizard) più chiara.
Aggiunge le voci che mancano ai .po di de_DE, en_GB, en_US, es_ES, fr_FR e ricompila i .mo. Si può rilanciare.
uso: python po_1076.py
"""
import os
import subprocess
import sys

ROOT = os.path.normpath(os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', '..'))
LANG = os.path.join(ROOT, 'languages')
MSGFMT = os.path.join(os.path.dirname(sys.executable), 'Tools', 'i18n', 'msgfmt.py')
VERSIONE = 'OLObuild 1.4.1076'

# italiano: (tedesco, inglese, spagnolo, francese)
V = {
    'Hello Olobuild è installato ma non attivo. È il tema leggero fatto apposta per Olobuild: non aggiunge stili suoi.': (
        'Hello Olobuild ist installiert, aber nicht aktiv. Es ist das schlanke Theme, das eigens für Olobuild gemacht ist: Es bringt keine eigenen Stile mit.',
        'Hello Olobuild is installed but not active. It is the lightweight theme made for Olobuild: it adds no styles of its own.',
        'Hello Olobuild está instalado pero no activo. Es el tema ligero hecho a propósito para Olobuild: no añade estilos propios.',
        'Hello Olobuild est installé mais pas actif. C’est le thème léger conçu pour Olobuild : il n’ajoute aucun style.'),
    'Testata, pagine e piè di pagina li disegna Olobuild: al tema WordPress resta solo da lasciargli spazio. Installiamo <strong>Hello Olobuild</strong>, il tema leggero fatto apposta.': (
        'Kopfbereich, Seiten und Fußbereich gestaltet Olobuild: Das WordPress-Theme muss ihm nur Platz lassen. Wir installieren <strong>Hello Olobuild</strong>, das schlanke Theme, das eigens dafür gemacht ist.',
        'Olobuild designs the header, the pages and the footer: the WordPress theme only has to make room for it. Let’s install <strong>Hello Olobuild</strong>, the lightweight theme made for the job.',
        'La cabecera, las páginas y el pie los diseña Olobuild: al tema de WordPress solo le toca dejarle sitio. Instalamos <strong>Hello Olobuild</strong>, el tema ligero hecho a propósito.',
        'L’en-tête, les pages et le pied de page, c’est Olobuild qui les dessine : le thème WordPress n’a qu’à lui laisser la place. Installons <strong>Hello Olobuild</strong>, le thème léger conçu pour cela.'),
    'Ogni tema è un sito completo: testata, piè di pagina, pagine e menu. Dopo cambi tutto dal builder.': (
        'Jedes Theme ist eine komplette Website: Kopfbereich, Fußbereich, Seiten und Menü. Danach änderst du alles im Builder.',
        'Each theme is a complete site: header, footer, pages and menu. Afterwards you change everything in the builder.',
        'Cada tema es un sitio completo: cabecera, pie, páginas y menú. Después lo cambias todo desde el builder.',
        'Chaque thème est un site complet : en-tête, pied de page, pages et menu. Ensuite, tu modifies tout dans le builder.'),
    'Scegli un tema dall’elenco': (
        'Wähle ein Theme aus der Liste', 'Choose a theme from the list', 'Elige un tema de la lista', 'Choisis un thème dans la liste'),
    'Tutto pronto!': ('Alles bereit!', 'All set!', '¡Todo listo!', 'Tout est prêt !'),
    'Creo le pagine, il menu, la testata e il piè di pagina, e copio le foto nella Libreria media…': (
        'Ich erstelle die Seiten, das Menü, den Kopf- und den Fußbereich und kopiere die Fotos in die Mediathek …',
        'Creating the pages, the menu, the header and the footer, and copying the photos to the Media Library…',
        'Creo las páginas, el menú, la cabecera y el pie, y copio las fotos en la Biblioteca de medios…',
        'Je crée les pages, le menu, l’en-tête et le pied de page, et je copie les photos dans la Médiathèque…'),
    'Importa «%s»': ('„%s“ importieren', 'Import “%s”', 'Importar «%s»', 'Importer « %s »'),
    'Parti da zero': ('Bei null anfangen', 'Start from scratch', 'Empezar de cero', 'Partir de zéro'),
    'Il tema «%s» è nel tuo sito: pagine, menu, testata e piè di pagina sono già al loro posto. Testi, foto e colori li cambi dal builder.': (
        'Das Theme „%s“ ist auf deiner Website: Seiten, Menü, Kopf- und Fußbereich sind schon an ihrem Platz. Texte, Fotos und Farben änderst du im Builder.',
        'The “%s” theme is on your site: pages, menu, header and footer are already in place. You change texts, photos and colours in the builder.',
        'El tema «%s» está en tu sitio: páginas, menú, cabecera y pie ya están en su sitio. Textos, fotos y colores los cambias desde el builder.',
        'Le thème « %s » est sur ton site : pages, menu, en-tête et pied de page sont déjà en place. Textes, photos et couleurs se modifient dans le builder.'),
    # la procedura ridisegnata: colonna dei passi, rete OLOtheme, riepilogo
    '%s — Configurazione iniziale': ('%s – Ersteinrichtung', '%s — Initial setup', '%s — Configuración inicial', '%s — Configuration initiale'),
    'Configurazione iniziale': ('Ersteinrichtung', 'Initial setup', 'Configuración inicial', 'Configuration initiale'),
    'Pochi passi, un paio di minuti. Tutto si cambia anche dopo.': (
        'Wenige Schritte, ein paar Minuten. Alles lässt sich später noch ändern.',
        'A few steps, a couple of minutes. You can change everything later.',
        'Pocos pasos, un par de minutos. Todo se puede cambiar después.',
        'Quelques étapes, deux minutes. Tout se modifie aussi après.'),
    'Tema WordPress': ('WordPress-Theme', 'WordPress theme', 'Tema de WordPress', 'Thème WordPress'),
    'Design': ('Design', 'Design', 'Diseño', 'Design'),
    'Un tema pronto': ('Ein fertiges Theme', 'A ready-made theme', 'Un tema listo', 'Un thème prêt'),
    'Rete OLOtheme': ('OLOtheme-Netzwerk', 'OLOtheme network', 'Red OLOtheme', 'Réseau OLOtheme'),
    'Altri software': ('Weitere Software', 'More software', 'Otros programas', 'Autres logiciels'),
    'Pronto': ('Fertig', 'Done', 'Listo', 'Prêt'),
    'Il tuo sito': ('Deine Website', 'Your site', 'Tu sitio', 'Ton site'),
    'Passo %1$d di %2$d': ('Schritt %1$d von %2$d', 'Step %1$d of %2$d', 'Paso %1$d de %2$d', 'Étape %1$d sur %2$d'),
    'Il tema WordPress': ('Das WordPress-Theme', 'The WordPress theme', 'El tema de WordPress', 'Le thème WordPress'),
    'Hello Olobuild è già attivo: WordPress lascia tutto lo spazio a Olobuild.': (
        'Hello Olobuild ist schon aktiv: WordPress überlässt Olobuild den ganzen Platz.',
        'Hello Olobuild is already active: WordPress leaves all the room to Olobuild.',
        'Hello Olobuild ya está activo: WordPress deja todo el espacio a Olobuild.',
        'Hello Olobuild est déjà actif : WordPress laisse toute la place à Olobuild.'),
    'Continua': ('Weiter', 'Continue', 'Continuar', 'Continuer'),
    'Tieni il tema attuale': ('Aktuelles Theme behalten', 'Keep the current theme', 'Mantener el tema actual', 'Garder le thème actuel'),
    'Hello Olobuild: solo la cornice': ('Hello Olobuild: nur der Rahmen', 'Hello Olobuild: just the frame', 'Hello Olobuild: solo el marco', 'Hello Olobuild : juste le cadre'),
    'Testata': ('Kopfbereich', 'Header', 'Cabecera', 'En-tête'),
    'Pagine': ('Seiten', 'Pages', 'Páginas', 'Pages'),
    'Piè di pagina': ('Fußbereich', 'Footer', 'Pie de página', 'Pied de page'),
    'Lo disegna %s': ('Gestaltet von %s', 'Designed by %s', 'Lo diseña %s', 'Dessiné par %s'),
    'Scegli un design': ('Wähle ein Design', 'Choose a design', 'Elige un diseño', 'Choisis un design'),
    'Importo «%s»': ('„%s“ wird importiert', 'Importing “%s”', 'Importando «%s»', 'Import de « %s »'),
    'Ti serve altro?': ('Brauchst du noch etwas?', 'Need anything else?', '¿Necesitas algo más?', 'Il te faut autre chose ?'),
    'Olobuild fa parte della rete OLOtheme: software che lavorano insieme sullo stesso sito, con lo stesso stile. Segna quelli che ti servono.': (
        'Olobuild gehört zum OLOtheme-Netzwerk: Software, die auf derselben Website im selben Stil zusammenarbeitet. Markiere, was du brauchst.',
        'Olobuild is part of the OLOtheme network: software that works together on the same site, in the same style. Tick the ones you need.',
        'Olobuild forma parte de la red OLOtheme: programas que trabajan juntos en el mismo sitio, con el mismo estilo. Marca los que necesitas.',
        'Olobuild fait partie du réseau OLOtheme : des logiciels qui travaillent ensemble sur le même site, dans le même style. Coche ceux dont tu as besoin.'),
    'Lingue': ('Sprachen', 'Languages', 'Idiomas', 'Langues'),
    'Il sito in più lingue: pagine, menu e testi tradotti da un pannello solo.': (
        'Die Website in mehreren Sprachen: Seiten, Menüs und Texte übersetzt in einem einzigen Bereich.',
        'Your site in several languages: pages, menus and texts translated from a single panel.',
        'El sitio en varios idiomas: páginas, menús y textos traducidos desde un solo panel.',
        'Le site en plusieurs langues : pages, menus et textes traduits depuis un seul panneau.'),
    'Sicurezza': ('Sicherheit', 'Security', 'Seguridad', 'Sécurité'),
    'Accessi protetti, verifica in due passaggi, controllo dei file e un firewall leggero, con i dati sul tuo sito.': (
        'Geschützte Anmeldungen, Zwei-Faktor-Prüfung, Dateikontrolle und eine schlanke Firewall, mit den Daten auf deiner Website.',
        'Protected logins, two-step verification, file checks and a lightweight firewall, with the data on your own site.',
        'Accesos protegidos, verificación en dos pasos, control de archivos y un cortafuegos ligero, con los datos en tu sitio.',
        'Connexions protégées, vérification en deux étapes, contrôle des fichiers et un pare-feu léger, avec les données sur ton site.'),
    'Prenotazioni': ('Buchungen', 'Bookings', 'Reservas', 'Réservations'),
    'Camere, tavoli, appuntamenti, eventi, noleggi e visite agli immobili, con calendario e pagamenti.': (
        'Zimmer, Tische, Termine, Veranstaltungen, Vermietungen und Immobilienbesichtigungen, mit Kalender und Zahlungen.',
        'Rooms, tables, appointments, events, rentals and property viewings, with calendar and payments.',
        'Habitaciones, mesas, citas, eventos, alquileres y visitas a inmuebles, con calendario y pagos.',
        'Chambres, tables, rendez-vous, événements, locations et visites de biens, avec calendrier et paiements.'),
    'Tour virtuali': ('Virtuelle Touren', 'Virtual tours', 'Tours virtuales', 'Visites virtuelles'),
    'Tour a 360° e ambienti 3D da visitare, anche col visore e con le visite guidate dal vivo.': (
        '360°-Touren und begehbare 3D-Räume, auch mit VR-Brille und mit Live-Führungen.',
        '360° tours and 3D spaces to explore, also with a headset and with live guided visits.',
        'Tours de 360° y espacios 3D para recorrer, también con visor y con visitas guiadas en directo.',
        'Visites à 360° et espaces 3D à parcourir, aussi avec un casque et avec des visites guidées en direct.'),
    'Corsi online': ('Online-Kurse', 'Online courses', 'Cursos online', 'Cours en ligne'),
    'Corsi e lezioni con giochi, quiz, badge e certificati, tutto dentro WordPress.': (
        'Kurse und Lektionen mit Spielen, Quiz, Abzeichen und Zertifikaten, alles in WordPress.',
        'Courses and lessons with games, quizzes, badges and certificates, all inside WordPress.',
        'Cursos y lecciones con juegos, cuestionarios, insignias y certificados, todo dentro de WordPress.',
        'Cours et leçons avec jeux, quiz, badges et certificats, le tout dans WordPress.'),
    'Già attivo': ('Bereits aktiv', 'Already active', 'Ya activo', 'Déjà actif'),
    'Installato': ('Installiert', 'Installed', 'Instalado', 'Installé'),
    'Scopri': ('Entdecken', 'Learn more', 'Descubrir', 'Découvrir'),
    'Per ora la tua scelta resta segnata qui: quando i software della rete si potranno scaricare da Olobuild, li troverai pronti da installare.': (
        'Vorerst bleibt deine Auswahl hier vermerkt: Sobald sich die Software des Netzwerks über Olobuild herunterladen lässt, findest du sie bereit zur Installation.',
        'For now your choice is noted here: when the network’s software can be downloaded from Olobuild, you’ll find it ready to install.',
        'Por ahora tu elección queda anotada aquí: cuando los programas de la red se puedan descargar desde Olobuild, los encontrarás listos para instalar.',
        'Pour l’instant ton choix reste noté ici : quand les logiciels du réseau pourront se télécharger depuis Olobuild, tu les trouveras prêts à installer.'),
    'Personalizza la testata': ('Kopfbereich anpassen', 'Customise the header', 'Personaliza la cabecera', 'Personnalise l’en-tête'),
    'Personalizza il piè di pagina': ('Fußbereich anpassen', 'Customise the footer', 'Personaliza el pie de página', 'Personnalise le pied de page'),
    'Vedi il sito': ('Website ansehen', 'View the site', 'Ver el sitio', 'Voir le site'),
    'Apri %s': ('%s öffnen', 'Open %s', 'Abrir %s', 'Ouvrir %s'),
    'Hello Olobuild è il tema di WordPress': ('Hello Olobuild ist das WordPress-Theme', 'Hello Olobuild is the WordPress theme', 'Hello Olobuild es el tema de WordPress', 'Hello Olobuild est le thème WordPress'),
    'Il tema «%s»: pagine, menu, testata e piè di pagina': (
        'Das Theme „%s“: Seiten, Menü, Kopf- und Fußbereich', 'The “%s” theme: pages, menu, header and footer',
        'El tema «%s»: páginas, menú, cabecera y pie', 'Le thème « %s » : pages, menu, en-tête et pied de page'),
    'Segnati per dopo: %s': ('Für später vermerkt: %s', 'Noted for later: %s', 'Anotados para después: %s', 'Notés pour plus tard : %s'),
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
