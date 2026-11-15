# -*- coding: utf-8 -*-
"""Traduzioni della 1.4.1010 (14 nov 2026): nel Cartellone «Solo i passati (lo storico)» e «Dal più recente».
Aggiunge le voci che mancano ai .po di de_DE, en_GB, en_US, es_ES, fr_FR e ricompila i .mo. Si può rilanciare.
uso: python po_1010.py
"""
import os
import subprocess
import sys

ROOT = os.path.normpath(os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', '..'))
LANG = os.path.join(ROOT, 'languages')
MSGFMT = os.path.join(os.path.dirname(sys.executable), 'Tools', 'i18n', 'msgfmt.py')

# italiano: (tedesco, inglese, spagnolo, francese)
V = {
    'Dal più recente': ('Neueste zuerst', 'Most recent first', 'Del más reciente', 'Du plus récent'),
    'L’ordine al contrario: in cima l’ultimo appuntamento. Con «Solo i passati» fa lo storico, dall’ultimo andato in scena al più vecchio.': (
        'Umgekehrte Reihenfolge: der letzte Termin steht oben. Mit „Nur vergangene“ wird daraus das Archiv, vom zuletzt gespielten bis zum ältesten.',
        'Reverse order: the latest event comes first. With “Past only” it becomes the archive, from the most recent performance to the oldest.',
        'Orden inverso: arriba el último evento. Con «Solo los pasados» se convierte en el archivo, del último representado al más antiguo.',
        'Ordre inversé : le dernier rendez-vous en haut. Avec « Seulement les passés », cela devient l’archive, du dernier joué au plus ancien.'),
    'Solo i passati (lo storico)': ('Nur vergangene (Archiv)', 'Past only (archive)', 'Solo los pasados (archivo)', 'Seulement les passés (archive)'),
    'Con «Solo i passati» la tile mostra solo gli appuntamenti già andati in scena, non attenuati: è lo storico, e si aggiorna da solo coi giorni.': (
        'Mit „Nur vergangene“ zeigt die Kachel nur bereits gespielte Termine, nicht abgeschwächt: das ist das Archiv, und es aktualisiert sich mit den Tagen von selbst.',
        'With “Past only” the tile shows only events that have already taken place, not faded: it is the archive, and it updates itself as the days go by.',
        'Con «Solo los pasados» la tile muestra solo los eventos ya representados, sin atenuar: es el archivo, y se actualiza solo con los días.',
        'Avec « Seulement les passés », la tuile ne montre que les rendez-vous déjà joués, sans les estomper : c’est l’archive, et elle se met à jour toute seule au fil des jours.'),
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
            nuove.append('\n#: OLObuild 1.4.1010\nmsgid "%s"\nmsgstr "%s"\n' % (esc(it), esc(tr[i])))
        if nuove:
            with open(po, 'a', encoding='utf-8', newline='\n') as f:
                f.write(''.join(nuove))
        subprocess.run([sys.executable, MSGFMT, '-o', po[:-3] + '.mo', po], check=True)
        print(loc, 'voci nuove:', len(nuove))


if __name__ == '__main__':
    main()
