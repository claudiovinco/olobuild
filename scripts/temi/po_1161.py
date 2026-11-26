# -*- coding: utf-8 -*-
"""Traduzioni della 1.4.1161 (17 nov 2026): l'editor del Pro Slider in italiano («Slide», «Livelli globali»,
«Schermo:», il nome del livello e dello schermo, «Profondità parallasse», «Visibile su …»).
Aggiunge le voci che mancano ai .po di de_DE, en_GB, en_US, es_ES, fr_FR e ricompila i .mo. Si può rilanciare.
uso: python po_1161.py
"""
import os
import subprocess
import sys

ROOT = os.path.normpath(os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', '..'))
LANG = os.path.join(ROOT, 'languages')
MSGFMT = os.path.join(os.path.dirname(sys.executable), 'Tools', 'i18n', 'msgfmt.py')
VERSIONE = 'OLObuild 1.4.1161'

# italiano: (tedesco, inglese, spagnolo, francese)
V = {
    'Slide': ('Folien', 'Slides', 'Diapositivas', 'Diapositives'),
    'Esporta le slide': ('Folien exportieren', 'Export slides', 'Exportar las diapositivas', 'Exporter les diapositives'),
    'Importa le slide': ('Folien importieren', 'Import slides', 'Importar las diapositivas', 'Importer les diapositives'),
    'Editor del Pro Slider': ('Slider-Pro-Editor', 'Pro Slider editor', 'Editor del Pro Slider', 'Éditeur du Pro Slider'),
    'Livelli globali': ('Globale Ebenen', 'Global layers', 'Capas globales', 'Calques globaux'),
    'Nessun livello globale': ('Keine globale Ebene', 'No global layers', 'Ninguna capa global', 'Aucun calque global'),
    'Schermo:': ('Bildschirm:', 'Screen:', 'Pantalla:', 'Écran :'),
    'Profondità parallasse': ('Parallax-Tiefe', 'Parallax depth', 'Profundidad del paralaje', 'Profondeur de parallaxe'),
    'Valori per': ('Werte für', 'Values for', 'Valores para', 'Valeurs pour'),
    '— quelli non impostati ereditano dallo schermo più grande': ('— nicht gesetzte Werte übernehmen die des größeren Bildschirms', '— unset values inherit from the larger screen', '— los que no se fijan heredan de la pantalla más grande', '— les valeurs non définies héritent de l’écran plus grand'),
    "Nascosto all'avvio (lo mostra un altro livello)": ('Beim Start ausgeblendet (eine andere Ebene blendet es ein)', 'Hidden at start (another layer shows it)', 'Oculto al inicio (lo muestra otra capa)', 'Masqué au départ (un autre calque l’affiche)'),
    'Mostra o nasconde un livello': ('Blendet eine Ebene ein oder aus', 'Shows or hides a layer', 'Muestra u oculta una capa', 'Affiche ou masque un calque'),
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
