# -*- coding: utf-8 -*-
"""Traduzioni della 1.4.1117 (16 nov 2026): Newsletter, titolo e privacy con l’editor, stili onesti, etichette in italiano (episodio 37).
Aggiunge le voci che mancano ai .po di de_DE, en_GB, en_US, es_ES, fr_FR e ricompila i .mo. Si può rilanciare.
uso: python po_1097.py
"""
import os
import subprocess
import sys

ROOT = os.path.normpath(os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', '..'))
LANG = os.path.join(ROOT, 'languages')
MSGFMT = os.path.join(os.path.dirname(sys.executable), 'Tools', 'i18n', 'msgfmt.py')
VERSIONE = 'OLObuild 1.4.1117'

# italiano: (tedesco, inglese, spagnolo, francese)
V = {
    'Testo nel campo nome': ('Text im Namensfeld', 'Text in the name field', 'Texto en el campo nombre', 'Texte dans le champ nom'),
    'Testo nel campo email': ('Text im E-Mail-Feld', 'Text in the email field', 'Texto en el campo email', 'Texte dans le champ e-mail'),
    'Testo privacy': ('Datenschutztext', 'Privacy text', 'Texto de privacidad', 'Texte de confidentialité'),
    'La riga sotto il modulo, col collegamento alla pagina della privacy: seleziona le parole e premi il pulsante del collegamento.': ('Die Zeile unter dem Formular, mit dem Link zur Datenschutzseite: Wörter markieren und den Link-Button drücken.', 'The line under the form, with the link to the privacy page: select the words and press the link button.', 'La línea bajo el formulario, con el enlace a la página de privacidad: selecciona las palabras y pulsa el botón de enlace.', 'La ligne sous le formulaire, avec le lien vers la page de confidentialité : sélectionnez les mots et appuyez sur le bouton du lien.'),
    'Dopo l’iscrizione': ('Nach der Anmeldung', 'After signing up', 'Después de suscribirse', 'Après l’inscription'),
    'Messaggio': ('Nachricht', 'Message', 'Mensaje', 'Message'),
    'Pagina dopo l’iscrizione': ('Seite nach der Anmeldung', 'Page after signing up', 'Página después de suscribirse', 'Page après l’inscription'),
    'Vuota: resta sulla pagina e mostra il messaggio.': ('Leer: bleibt auf der Seite und zeigt die Nachricht.', 'Empty: stays on the page and shows the message.', 'Vacía: se queda en la página y muestra el mensaje.', 'Vide : reste sur la page et affiche le message.'),
    'Contenuto riservato': ('Geschützter Inhalt', 'Locked content', 'Contenido reservado', 'Contenu réservé'),
    'Blocca il contenuto che segue': ('Folgenden Inhalt sperren', 'Lock the content that follows', 'Bloquear el contenido que sigue', 'Bloquer le contenu qui suit'),
    'Messaggio sul contenuto bloccato': ('Nachricht auf dem gesperrten Inhalt', 'Message on the locked content', 'Mensaje sobre el contenido bloqueado', 'Message sur le contenu bloqué'),
    'Contro lo spam': ('Gegen Spam', 'Against spam', 'Contra el spam', 'Contre le spam'),
    'Campo trappola': ('Fallenfeld', 'Trap field', 'Campo trampa', 'Champ piège'),
    'Un campo invisibile che le persone non vedono e i programmi automatici riempiono: chi lo riempie non viene iscritto. Consigliato.': ('Ein unsichtbares Feld, das Menschen nicht sehen und automatische Programme ausfüllen: Wer es ausfüllt, wird nicht angemeldet. Empfohlen.', 'An invisible field people don’t see and bots fill in: whoever fills it is not subscribed. Recommended.', 'Un campo invisible que las personas no ven y los programas automáticos rellenan: quien lo rellena no se suscribe. Recomendado.', 'Un champ invisible que les personnes ne voient pas et que les robots remplissent : qui le remplit n’est pas inscrit. Recommandé.'),
    'Chiaro': ('Hell', 'Light', 'Claro', 'Clair'),
    'Scheda': ('Karte', 'Card', 'Ficha', 'Fiche'),
    'Scuro': ('Dunkel', 'Dark', 'Oscuro', 'Sombre'),
    'Tinta': ('Farbe', 'Tint', 'Tinta', 'Teinte'),
    'Linea': ('Linie', 'Line', 'Línea', 'Ligne'),
    'Cambia insieme fondo, testi, campi e pulsante, coi colori del sito. Poi ogni controllo qui sotto si regola da solo.': ('Ändert Hintergrund, Texte, Felder und Button zusammen, mit den Farben der Website. Danach lässt sich jedes Steuerelement darunter einzeln einstellen.', 'Changes background, texts, fields and button together, with the site’s colours. Then each control below can be adjusted on its own.', 'Cambia a la vez fondo, textos, campos y botón, con los colores del sitio. Luego cada control de abajo se ajusta por separado.', 'Change ensemble fond, textes, champs et bouton, avec les couleurs du site. Ensuite chaque réglage ci-dessous s’ajuste seul.'),
    'Come compare il messaggio': ('Wie die Nachricht erscheint', 'How the message appears', 'Cómo aparece el mensaje', 'Comment le message apparaît'),
    'Dissolvenza': ('Einblenden', 'Fade', 'Fundido', 'Fondu'),
    'Spunta animata': ('Animiertes Häkchen', 'Animated tick', 'Marca animada', 'Coche animée'),
    'Larghezza massima': ('Maximale Breite', 'Maximum width', 'Ancho máximo', 'Largeur maximale'),
    'Colore d’accento del titolo': ('Akzentfarbe des Titels', 'Title accent colour', 'Color de acento del título', 'Couleur d’accent du titre'),
    'Le parole in corsivo del titolo. Vuoto: il primario del sito.': ('Die kursiven Wörter des Titels. Leer: die Primärfarbe der Website.', 'The italic words in the title. Empty: the site’s primary colour.', 'Las palabras en cursiva del título. Vacío: el primario del sitio.', 'Les mots en italique du titre. Vide : la couleur primaire du site.'),
    'Campi': ('Felder', 'Fields', 'Campos', 'Champs'),
    'Sfondo campi': ('Feldhintergrund', 'Fields background', 'Fondo de los campos', 'Fond des champs'),
    'Colore testo campi': ('Textfarbe der Felder', 'Fields text colour', 'Color del texto de los campos', 'Couleur du texte des champs'),
    'Colore del suggerimento': ('Farbe des Hinweises', 'Hint colour', 'Color de la sugerencia', 'Couleur de l’indication'),
    'Bordo campi': ('Rahmen der Felder', 'Fields border', 'Borde de los campos', 'Bordure des champs'),
    'Raggio campi': ('Radius der Felder', 'Fields radius', 'Radio de los campos', 'Rayon des champs'),
    'Altezza campi': ('Höhe der Felder', 'Fields height', 'Altura de los campos', 'Hauteur des champs'),
    'Messaggi e privacy': ('Nachrichten und Datenschutz', 'Messages and privacy', 'Mensajes y privacidad', 'Messages et confidentialité'),
    'Sfondo messaggio di conferma': ('Hintergrund der Bestätigung', 'Confirmation message background', 'Fondo del mensaje de confirmación', 'Fond du message de confirmation'),
    'Colore messaggio di conferma': ('Farbe der Bestätigung', 'Confirmation message colour', 'Color del mensaje de confirmación', 'Couleur du message de confirmation'),
    'Sfondo messaggio d’errore': ('Hintergrund der Fehlermeldung', 'Error message background', 'Fondo del mensaje de error', 'Fond du message d’erreur'),
    'Colore messaggio d’errore': ('Farbe der Fehlermeldung', 'Error message colour', 'Color del mensaje de error', 'Couleur du message d’erreur'),
    'Modulo': ('Formular', 'Form', 'Formulario', 'Formulaire'),
    'In riga': ('In einer Zeile', 'In a row', 'En fila', 'En ligne'),
    'In colonna': ('In einer Spalte', 'In a column', 'En columna', 'En colonne'),
    'Essenziale': ('Schlicht', 'Essential', 'Esencial', 'Essentiel'),
    'In riga: campi e pulsante uno accanto all’altro. In colonna: uno sotto l’altro. Essenziale: solo l’email e il pulsante, senza titolo né icona.': ('In einer Zeile: Felder und Button nebeneinander. In einer Spalte: untereinander. Schlicht: nur E-Mail und Button, ohne Titel und Symbol.', 'In a row: fields and button side by side. In a column: one under the other. Essential: only the email and the button, no title or icon.', 'En fila: campos y botón uno al lado del otro. En columna: uno debajo del otro. Esencial: solo el email y el botón, sin título ni icono.', 'En ligne : champs et bouton côte à côte. En colonne : l’un sous l’autre. Essentiel : seulement l’e-mail et le bouton, sans titre ni icône.'),
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
