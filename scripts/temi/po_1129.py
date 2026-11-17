# -*- coding: utf-8 -*-
"""Traduzioni della 1.4.1129 (16 nov 2026): Quiz a punteggio: la tipografia del titolo e della domanda (episodio 42).
Aggiunge le voci che mancano ai .po di de_DE, en_GB, en_US, es_ES, fr_FR e ricompila i .mo. Si può rilanciare.
uso: python po_1097.py
"""
import os
import subprocess
import sys

ROOT = os.path.normpath(os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', '..'))
LANG = os.path.join(ROOT, 'languages')
MSGFMT = os.path.join(os.path.dirname(sys.executable), 'Tools', 'i18n', 'msgfmt.py')
VERSIONE = 'OLObuild 1.4.1129'

# italiano: (tedesco, inglese, spagnolo, francese)
V = {
    'Risposte': ('Antworten', 'Answers', 'Respuestas', 'Réponses'),
    'Una per riga, scritta «Risposta|punti»: per esempio «Spesso|3». Senza punti la risposta vale 0.': ('Eine pro Zeile, geschrieben «Antwort|Punkte»: zum Beispiel «Oft|3». Ohne Punkte zählt die Antwort 0.', 'One per line, written «Answer|points»: for example «Often|3». Without points the answer is worth 0.', 'Una por línea, escrita «Respuesta|puntos»: por ejemplo «A menudo|3». Sin puntos la respuesta vale 0.', 'Une par ligne, écrite « Réponse|points » : par exemple « Souvent|3 ». Sans points la réponse vaut 0.'),
    'Domanda': ('Frage', 'Question', 'Pregunta', 'Question'),
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
