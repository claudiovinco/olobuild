# -*- coding: utf-8 -*-
"""Traduzioni della 1.4.1118 (16 nov 2026): Newsletter, etichette che non si tagliano (episodio 37).
Aggiunge le voci che mancano ai .po di de_DE, en_GB, en_US, es_ES, fr_FR e ricompila i .mo. Si può rilanciare.
uso: python po_1097.py
"""
import os
import subprocess
import sys

ROOT = os.path.normpath(os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', '..'))
LANG = os.path.join(ROOT, 'languages')
MSGFMT = os.path.join(os.path.dirname(sys.executable), 'Tools', 'i18n', 'msgfmt.py')
VERSIONE = 'OLObuild 1.4.1118'

# italiano: (tedesco, inglese, spagnolo, francese)
V = {
    'Sopra il titolo': ('Über dem Titel', 'Above the title', 'Sobre el título', 'Au-dessus du titre'),
    'Pagina': ('Seite', 'Page', 'Página', 'Page'),
    'La pagina dove va chi si è appena iscritto. Vuota: resta qui e vede il messaggio.': ('Die Seite, auf die frisch Angemeldete gelangen. Leer: bleibt hier und sieht die Nachricht.', 'The page new subscribers go to. Empty: they stay here and see the message.', 'La página a la que va quien acaba de suscribirse. Vacía: se queda aquí y ve el mensaje.', 'La page où va qui vient de s’inscrire. Vide : reste ici et voit le message.'),
    'Suggerimento': ('Hinweis', 'Hint', 'Sugerencia', 'Indication'),
    'Il testo grigio nei campi vuoti.': ('Der graue Text in leeren Feldern.', 'The grey text in empty fields.', 'El texto gris en los campos vacíos.', 'Le texte gris dans les champs vides.'),
    'Sfondo conferma': ('Hintergrund Bestätigung', 'Confirmation background', 'Fondo confirmación', 'Fond confirmation'),
    'Testo conferma': ('Text Bestätigung', 'Confirmation text', 'Texto confirmación', 'Texte confirmation'),
    'Sfondo errore': ('Hintergrund Fehler', 'Error background', 'Fondo error', 'Fond erreur'),
    'Testo errore': ('Text Fehler', 'Error text', 'Texto error', 'Texte erreur'),
    'Animazione': ('Animation', 'Animation', 'Animación', 'Animation'),
    'Come compare il messaggio di conferma dopo l’iscrizione.': ('Wie die Bestätigung nach der Anmeldung erscheint.', 'How the confirmation message appears after signing up.', 'Cómo aparece el mensaje de confirmación tras suscribirse.', 'Comment le message de confirmation apparaît après l’inscription.'),
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
