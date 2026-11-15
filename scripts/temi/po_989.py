# -*- coding: utf-8 -*-
"""Traduzioni della 1.4.989 (11 nov 2026): la scritta del Cesto fisico che si cambia.
Aggiunge le voci che mancano ai .po di de_DE, en_GB, en_US, es_ES, fr_FR e ricompila i .mo. Si può rilanciare.
uso: python po_989.py
"""
import os
import subprocess
import sys

ROOT = os.path.normpath(os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', '..'))
LANG = os.path.join(ROOT, 'languages')
MSGFMT = os.path.join(os.path.dirname(sys.executable), 'Tools', 'i18n', 'msgfmt.py')

# italiano: (tedesco, inglese, spagnolo, francese)
V = {
    'Scritta sul cesto': ('Schriftzug auf dem Korb', 'Label on the bin', 'Texto en la cesta', 'Texte sur le bac'),
    'La scritta in alto a sinistra. Vuota: «↯ trascina & lancia». Se la scrivi compare così com’è, senza il simbolo.': (
        'Der Schriftzug oben links. Leer: «↯ ziehen & werfen». Wenn du ihn schreibst, erscheint er genau so, ohne Symbol.',
        'The label at the top left. Empty: “↯ drag & throw”. If you write it, it appears exactly as written, without the symbol.',
        'El texto arriba a la izquierda. Vacío: «↯ arrastra y lanza». Si lo escribes aparece tal cual, sin el símbolo.',
        'Le texte en haut à gauche. Vide : « ↯ glisse et lance ». Si tu l’écris, il apparaît tel quel, sans le symbole.'),
    # la scritta storica e l'etichetta del cesto per chi usa un lettore di schermo: non erano tradotte
    'trascina & lancia': ('ziehen & werfen', 'drag & throw', 'arrastra y lanza', 'glisse et lance'),
    'Cesto interattivo: trascina e lancia i giocattoli': (
        'Interaktiver Korb: Spielzeuge ziehen und werfen', 'Interactive bin: drag and throw the toys',
        'Cesta interactiva: arrastra y lanza los juguetes', 'Bac interactif : glisse et lance les jouets'),
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
            nuove.append('\n#: OLObuild 1.4.989\nmsgid "%s"\nmsgstr "%s"\n' % (esc(it), esc(tr[i])))
        if nuove:
            with open(po, 'a', encoding='utf-8', newline='\n') as f:
                f.write(''.join(nuove))
        subprocess.run([sys.executable, MSGFMT, '-o', po[:-3] + '.mo', po], check=True)
        print(loc, 'voci nuove:', len(nuove))


if __name__ == '__main__':
    main()
