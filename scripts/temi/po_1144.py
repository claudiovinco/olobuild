# -*- coding: utf-8 -*-
"""Traduzioni della 1.4.1144 (17 nov 2026): Titolo animato con stili onesti e zone standard (episodio 47).
Aggiunge le voci che mancano ai .po di de_DE, en_GB, en_US, es_ES, fr_FR e ricompila i .mo. Si può rilanciare.
uso: python po_1097.py
"""
import os
import subprocess
import sys

ROOT = os.path.normpath(os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', '..'))
LANG = os.path.join(ROOT, 'languages')
MSGFMT = os.path.join(os.path.dirname(sys.executable), 'Tools', 'i18n', 'msgfmt.py')
VERSIONE = 'OLObuild 1.4.1144'

# italiano: (tedesco, inglese, spagnolo, francese)
V = {
    'Una per riga: si alternano nello stesso punto, che resta largo quanto la più lunga, così il titolo non cambia forma.': ('Eines pro Zeile: Sie wechseln sich an derselben Stelle ab, die so breit bleibt wie das längste, damit die Überschrift ihre Form nicht ändert.', 'One per line: they take turns in the same spot, which stays as wide as the longest, so the heading keeps its shape.', 'Una por línea: se alternan en el mismo sitio, que queda tan ancho como la más larga, así el título no cambia de forma.', 'Un par ligne : ils alternent au même endroit, qui reste aussi large que le plus long, et le titre ne change pas de forme.'),
    'Ritmo della digitazione': ('Tipptempo', 'Typing pace', 'Ritmo de escritura', 'Rythme de frappe'),
    'Il tempo di ogni lettera: più è basso, più scrive in fretta. Cancella al doppio della velocità.': ('Die Zeit pro Buchstabe: je niedriger, desto schneller wird getippt. Gelöscht wird doppelt so schnell.', 'The time for each letter: the lower it is, the faster it types. It deletes at twice the speed.', 'El tiempo de cada letra: cuanto más bajo, más rápido escribe. Borra al doble de velocidad.', 'Le temps de chaque lettre : plus il est bas, plus la frappe est rapide. L’effacement va deux fois plus vite.'),
    'Pausa fra le parole': ('Pause zwischen den Wörtern', 'Pause between words', 'Pausa entre palabras', 'Pause entre les mots'),
    'Evidenziatore': ('Textmarker', 'Highlighter', 'Rotulador', 'Surligneur'),
    'Cerchiato': ('Eingekreist', 'Circled', 'Rodeado', 'Entouré'),
    'Sobrio': ('Schlicht', 'Understated', 'Sobrio', 'Sobre'),
    'Parola animata': ('Animiertes Wort', 'Animated word', 'Palabra animada', 'Mot animé'),
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
