# -*- coding: utf-8 -*-
"""Traduzioni della 1.4.1489 (28 nov 2026): nel Cartellone «Mese all'apertura», col primo mese dell'elenco già scelto.
Aggiunge le voci che mancano ai .po di de_DE, en_GB, en_US, es_ES, fr_FR e ricompila i .mo. Si può rilanciare.
uso: python po_1489.py
"""
import os
import subprocess
import sys

ROOT = os.path.normpath(os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', '..'))
LANG = os.path.join(ROOT, 'languages')
MSGFMT = os.path.join(os.path.dirname(sys.executable), 'Tools', 'i18n', 'msgfmt.py')

# italiano: (tedesco, inglese, spagnolo, francese)
V = {
    'Mese all’apertura': ('Monat beim Öffnen', 'Month on opening', 'Mes al abrir', 'Mois à l’ouverture'),
    'Il primo mese dell’elenco': ('Erster Monat der Liste', 'First month in the list', 'Primer mes de la lista',
                                  'Premier mois de la liste'),
    'Con «Il primo mese dell’elenco» la pagina si apre su un mese solo: il primo in arrivo o, con «Dal più recente», l’ultimo andato in scena. Gli altri mesi restano a un tocco; senza JavaScript si vedono tutti gli appuntamenti.': (
        'Mit „Erster Monat der Liste“ öffnet sich die Seite mit nur einem Monat: dem ersten kommenden oder, mit „Neueste zuerst“, dem zuletzt gespielten. Die anderen Monate sind einen Klick entfernt; ohne JavaScript sind alle Termine zu sehen.',
        'With “First month in the list” the page opens on a single month: the first one coming up or, with “Most recent first”, the last one already performed. The other months are one tap away; without JavaScript every event is shown.',
        'Con «Primer mes de la lista» la página se abre en un solo mes: el primero que llega o, con «Del más reciente», el último ya representado. Los demás meses quedan a un toque; sin JavaScript se ven todos los eventos.',
        'Avec « Premier mois de la liste », la page s’ouvre sur un seul mois : le premier à venir ou, avec « Du plus récent », le dernier déjà joué. Les autres mois restent à portée de clic ; sans JavaScript, tous les rendez-vous sont visibles.'),
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
            nuove.append('\n#: OLObuild 1.4.1489\nmsgid "%s"\nmsgstr "%s"\n' % (esc(it), esc(tr[i])))
        if nuove:
            with open(po, 'a', encoding='utf-8', newline='\n') as f:
                f.write(''.join(nuove))
        subprocess.run([sys.executable, MSGFMT, '-o', po[:-3] + '.mo', po], check=True)
        print(loc, 'voci nuove:', len(nuove))


if __name__ == '__main__':
    main()
