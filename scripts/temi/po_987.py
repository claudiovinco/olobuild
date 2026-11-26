# -*- coding: utf-8 -*-
"""Traduzioni della 1.4.987 (10 nov 2026): l'andamento del movimento a scorrimento del Pro Slider e il cursore atomo.
Aggiunge le voci che mancano ai .po di de_DE, en_GB, en_US, es_ES, fr_FR e ricompila i .mo. Si può rilanciare.
uso: python po_987.py
"""
import os
import subprocess
import sys

ROOT = os.path.normpath(os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', '..'))
LANG = os.path.join(ROOT, 'languages')
MSGFMT = os.path.join(os.path.dirname(sys.executable), 'Tools', 'i18n', 'msgfmt.py')

# italiano: (tedesco, inglese, spagnolo, francese)
V = {
    'Andamento': ('Verlauf', 'Pacing', 'Ritmo', 'Allure'),
    'Morbido fra le tappe': ('Weich zwischen den Etappen', 'Smooth between stops', 'Suave entre etapas',
                             'Doux entre les étapes'),
    'Costante (zoom uniforme)': ('Gleichmäßig (stetiger Zoom)', 'Constant (even zoom)', 'Constante (zoom uniforme)',
                                 'Constant (zoom régulier)'),
    'Morbido: fra una tappa e l’altra il livello parte e arriva piano. Costante: va a velocità uniforme e la scala cresce '
    'in proporzione, così uno zoom che attraversa più tappe non rallenta a ogni tappa.': (
        'Weich: zwischen zwei Etappen fährt die Ebene langsam an und kommt langsam an. Gleichmäßig: sie bewegt sich mit '
        'konstanter Geschwindigkeit und die Skalierung wächst proportional, so wird ein Zoom über mehrere Etappen nicht an '
        'jeder Etappe langsamer.',
        'Smooth: between one stop and the next the layer eases in and out. Constant: it moves at an even speed and the scale '
        'grows in proportion, so a zoom across several stops does not slow down at each one.',
        'Suave: entre una etapa y otra la capa arranca y llega despacio. Constante: va a velocidad uniforme y la escala crece '
        'en proporción, así un zoom que cruza varias etapas no frena en cada una.',
        'Doux : entre deux étapes le calque démarre et arrive lentement. Constant : il avance à vitesse régulière et '
        'l’échelle croît en proportion, ainsi un zoom qui traverse plusieurs étapes ne ralentit pas à chacune.'),
    # 1.4.988: la bolla dell'utente della chat col colore del testo su accento
    'Testo su accento (CTA e bolla utente)': ('Text auf Akzent (CTA und Nutzerblase)', 'Text on accent (CTA and user bubble)',
                                              'Texto sobre acento (CTA y burbuja del usuario)',
                                              'Texte sur accent (CTA et bulle de l’utilisateur)'),
    # 1.4.988: le schede prodotto a due colonne sul telefono
    'Colonne sul telefono': ('Spalten auf dem Smartphone', 'Columns on phones', 'Columnas en el móvil',
                             'Colonnes sur téléphone'),
    'Sotto i 700 px. Due vanno bene per schede brevi (copertine, prodotti col solo nome e il prezzo).': (
        'Unter 700 px. Zwei passen zu kurzen Karten (Cover, Produkte nur mit Name und Preis).',
        'Below 700 px. Two suit short cards (covers, products with just a name and a price).',
        'Por debajo de 700 px. Dos van bien para fichas breves (portadas, productos con solo nombre y precio).',
        'Sous 700 px. Deux conviennent aux fiches courtes (couvertures, produits avec seulement le nom et le prix).'),
    'Atomo (elettroni che girano, fotone al clic)': ('Atom (kreisende Elektronen, Photon beim Klick)',
                                                     'Atom (orbiting electrons, a photon on click)',
                                                     'Átomo (electrones que giran, fotón al clic)',
                                                     'Atome (électrons en orbite, photon au clic)'),
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
            nuove.append('\n#: OLObuild 1.4.987\nmsgid "%s"\nmsgstr "%s"\n' % (esc(it), esc(tr[i])))
        if nuove:
            with open(po, 'a', encoding='utf-8', newline='\n') as f:
                f.write(''.join(nuove))
        subprocess.run([sys.executable, MSGFMT, '-o', po[:-3] + '.mo', po], check=True)
        print(loc, 'voci nuove:', len(nuove))


if __name__ == '__main__':
    main()
