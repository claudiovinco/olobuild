# -*- coding: utf-8 -*-
"""Traduzioni della 1.4.1082 (16 nov 2026): Configuratore, il gruppo di un'opzione si sceglie da un menu (episodio 25).
Aggiunge le voci che mancano ai .po di de_DE, en_GB, en_US, es_ES, fr_FR e ricompila i .mo. Si può rilanciare.
uso: python po_1082.py
"""
import os
import subprocess
import sys

ROOT = os.path.normpath(os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', '..'))
LANG = os.path.join(ROOT, 'languages')
MSGFMT = os.path.join(os.path.dirname(sys.executable), 'Tools', 'i18n', 'msgfmt.py')
VERSIONE = 'OLObuild 1.4.1082'

# italiano: (tedesco, inglese, spagnolo, francese)
V = {
    'Un gruppo per ogni scelta da fare: il processore, la memoria, i servizi. Ogni opzione sceglie il suo gruppo dal menu Gruppo.': (
        'Eine Gruppe für jede Entscheidung: Prozessor, Speicher, Dienste. Jede Option wählt ihre Gruppe im Menü Gruppe.',
        'One group for each choice to make: processor, memory, services. Each option picks its group from the Group menu.',
        'Un grupo por cada elección: el procesador, la memoria, los servicios. Cada opción elige su grupo en el menú Grupo.',
        'Un groupe pour chaque choix : le processeur, la mémoire, les services. Chaque option choisit son groupe dans le menu Groupe.'),
    'Il nome tecnico del gruppo, una parola senza spazi (per esempio cpu): serve ai collegamenti e ai moduli. Vuoto o automatico (ci-…): vale il titolo.': (
        'Der technische Name der Gruppe, ein Wort ohne Leerzeichen (zum Beispiel cpu): für Links und Formulare. Leer oder automatisch (ci-…): es gilt der Titel.',
        'The group’s technical name, one word without spaces (for example cpu): used by links and forms. Empty or automatic (ci-…): the title is used.',
        'El nombre técnico del grupo, una palabra sin espacios (por ejemplo cpu): sirve a los enlaces y a los formularios. Vacío o automático (ci-…): vale el título.',
        'Le nom technique du groupe, un mot sans espaces (par exemple cpu) : il sert aux liens et aux formulaires. Vide ou automatique (ci-…) : le titre fait foi.'),
    '— Scegli il gruppo —': ('— Gruppe wählen —', '— Choose the group —', '— Elige el grupo —', '— Choisis le groupe —'),
    '— Scegli —': ('— Wählen —', '— Choose —', '— Elige —', '— Choisis —'),
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
