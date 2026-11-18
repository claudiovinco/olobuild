# -*- coding: utf-8 -*-
"""I filtri della Vetrina (1.4.1218): scrive in ogni theme.json `filtri` = {gesto, carattere}.

- gesto: come si muove l'effetto del tema, dal piano della campagna «Un sito, un effetto» (piano.json): scorri, clic,
  mouse, fermo (si muove da solo). Un tema nuovo che non è nel piano resta senza gesto finché non lo si aggiunge.
- carattere: il carattere dei titoli, con le grazie, senza grazie o decorativo (display, condensati estremi, script,
  monospaziati, pixel), dalla tabella qui sotto.
Il fondo (scuro o chiaro) e il colore li ricava la Vetrina dal tema stesso. base.write_theme conserva `filtri`.
uso: python filtri_vetrina.py
"""
import glob
import json
import os
import re

QUI = os.path.dirname(os.path.abspath(__file__))
TEMI = os.path.normpath(os.path.join(QUI, '..', '..', 'assets', 'data', 'themes'))
PIANO = r'T:\TECNICA\SOCIAL\un-sito-un-effetto\sorgente\piano.json'

GRAZIE = {'Fraunces', 'Bodoni Moda', 'DM Serif Display', 'Cormorant Garamond', 'Playfair Display', 'Cormorant', 'Instrument Serif',
          'Young Serif', 'Marcellus', 'Newsreader', 'Gloock', 'Noto Serif Display', 'Spectral', 'Crimson Pro', 'Libre Caslon Display',
          'Cinzel', 'Gilda Display', 'Zilla Slab', 'Italiana', 'Petrona', 'Besley', 'Brygada 1918', 'Abril Fatface', 'Alegreya',
          'Alfa Slab One', 'Hedvig Letters Serif', 'Libre Baskerville', 'Shippori Mincho', 'Lora', 'Bellefair', 'DM Serif Text',
          'EB Garamond', 'Literata', 'Ibarra Real Nova', 'Playfair', 'Vollkorn', 'Libre Bodoni', 'Yeseva One', 'Castoro'}
DECORATIVI = {'Big Shoulders Display', 'Fredoka', 'Anton', 'Chakra Petch', 'Unbounded', 'Grenze Gotisch', 'Sofia Sans Extra Condensed',
              'Lilita One', 'Orbitron', 'Krona One', 'Special Elite', 'Anybody', 'Saira Condensed', 'Lexend Zetta', 'Baloo 2',
              'Caveat Brush', 'Teko', 'Six Caps', 'Bebas Neue', 'Bungee', 'Shrikhand', 'Press Start 2P', 'Righteous', 'JetBrains Mono',
              'Oswald', 'Oxanium', 'Michroma', 'Calistoga', 'Syncopate', 'Saira Stencil One', 'Archivo Black', 'Barlow Condensed',
              'Recursive', 'Syne'}


def famiglia(t):
    ty = t.get('styles', {}).get('typography', {})
    h = ty.get('font_family_heading') or ''
    return re.split(r"[,']", h.strip("'\" "))[0].strip("'\" ")


def main():
    piano = json.load(open(PIANO, encoding='utf-8')) if os.path.exists(PIANO) else {}
    senza_gesto, conta = [], {}
    for f in sorted(glob.glob(os.path.join(TEMI, '*', 'theme.json'))):
        t = json.load(open(f, encoding='utf-8'))
        fam = famiglia(t)
        car = 'grazie' if fam in GRAZIE else ('decorativo' if fam in DECORATIVI else 'senza')
        p = piano.get(t['id'], {})
        gesto = p.get('gesto', '') if p else ''
        if gesto not in ('scorri', 'clic', 'mouse', 'fermo'):
            gesto = 'fermo' if p else ''
        if not gesto:
            senza_gesto.append(t['id'])
        filtri = {'gesto': gesto, 'carattere': car} if gesto else {'carattere': car}
        if t.get('filtri') != filtri:
            t['filtri'] = filtri
            with open(f, 'w', encoding='utf-8', newline='\n') as fh:
                fh.write(json.dumps(t, ensure_ascii=False, separators=(',', ':')))
        conta[(gesto, car)] = conta.get((gesto, car), 0) + 1
    print('temi:', sum(conta.values()), '· senza gesto:', senza_gesto)
    for k, v in sorted(conta.items()):
        print(' ', k, v)


if __name__ == '__main__':
    main()
