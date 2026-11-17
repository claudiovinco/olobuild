# -*- coding: utf-8 -*-
"""Traduzioni della 1.4.1095 (16 nov 2026): Schede prodotto, la spiegazione giusta della seconda parte del titolo.
Aggiunge le voci che mancano ai .po di de_DE, en_GB, en_US, es_ES, fr_FR e ricompila i .mo. Si può rilanciare.
uso: python po_1095.py
"""
import os
import subprocess
import sys

ROOT = os.path.normpath(os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', '..'))
LANG = os.path.join(ROOT, 'languages')
MSGFMT = os.path.join(os.path.dirname(sys.executable), 'Tools', 'i18n', 'msgfmt.py')
VERSIONE = 'OLObuild 1.4.1095'

# italiano: (tedesco, inglese, spagnolo, francese)
V = {
    'Striscia garanzie': ('Garantieleiste', 'Trust strip', 'Franja de garantías', 'Bandeau de garanties'),
    'Con le pillole, al posto dell’icona: il marchio di un pagamento, di un corriere, di una certificazione.': ('Bei den Pillen anstelle des Symbols: das Logo einer Zahlungsart, eines Kuriers, einer Zertifizierung.', 'With pills, instead of the icon: the logo of a payment method, a courier, a certification.', 'Con las píldoras, en lugar del icono: el logo de un pago, de un mensajero, de una certificación.', 'Avec les pilules, à la place de l’icône : le logo d’un paiement, d’un transporteur, d’une certification.'),
    'Con le pillole, una piccola etichetta a destra del testo: «Nuovo», «Gratis».': ('Bei den Pillen ein kleines Etikett rechts vom Text: „Neu“, „Gratis“.', 'With pills, a small label to the right of the text: “New”, “Free”.', 'Con las píldoras, una pequeña etiqueta a la derecha del texto: «Nuevo», «Gratis».', 'Avec les pilules, une petite étiquette à droite du texte : « Nouveau », « Gratuit ».'),
    'In riga: icona e testo, con un separatore fra le voci. Pillole: ogni voce in una capsula di vetro, col logo al posto dell’icona e un badge.': ('In einer Reihe: Symbol und Text, mit einem Trenner zwischen den Einträgen. Pillen: jeder Eintrag in einer Glaskapsel, mit dem Logo statt des Symbols und einem Badge.', 'In a row: icon and text, with a separator between items. Pills: each item in a glass capsule, with the logo instead of the icon and a badge.', 'En fila: icono y texto, con un separador entre elementos. Píldoras: cada elemento en una cápsula de vidrio, con el logo en lugar del icono y una insignia.', 'En ligne : icône et texte, avec un séparateur entre les éléments. Pilules : chaque élément dans une capsule de verre, avec le logo à la place de l’icône et un badge.'),
    'In riga': ('In einer Reihe', 'In a row', 'En fila', 'En ligne'),
    'Pillole': ('Pillen', 'Pills', 'Píldoras', 'Pilules'),
    'Sfondo pillola': ('Pillenhintergrund', 'Pill background', 'Fondo de la píldora', 'Fond de la pilule'),
    '· Punto': ('· Punkt', '· Dot', '· Punto', '· Point'),
    '• Pallino': ('• Kreis', '• Bullet', '• Viñeta', '• Puce'),
    '| Barra': ('| Strich', '| Bar', '| Barra', '| Barre'),
    '— Lineetta': ('— Gedankenstrich', '— Dash', '— Raya', '— Tiret'),
    'Su più righe': ('Mehrzeilig', 'On several lines', 'En varias líneas', 'Sur plusieurs lignes'),
    'Forza una riga: le voci non vanno mai a capo, e se non ci stanno la striscia scorre di lato.': ('Eine Zeile erzwingen: Die Einträge brechen nie um, und wenn sie nicht passen, scrollt die Leiste seitlich.', 'Force one line: items never wrap, and if they don’t fit the strip scrolls sideways.', 'Forzar una línea: los elementos nunca bajan de línea y, si no caben, la franja se desplaza de lado.', 'Forcer une ligne : les éléments ne passent jamais à la ligne, et s’ils ne tiennent pas le bandeau défile sur le côté.'),
    'Ogni voce': ('Jeder Eintrag', 'Each item', 'Cada elemento', 'Chaque élément'),
    'Attaccata al titolo e, se vuoi, in corsivo (Stile → Ogni scheda).': ('Direkt am Titel und auf Wunsch kursiv (Stil → Jede Karte).', 'Attached to the title and, if you like, in italics (Style → Each card).', 'Pegada al título y, si quieres, en cursiva (Estilo → Cada ficha).', 'Accolée au titre et, si tu veux, en italique (Style → Chaque fiche).'),
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
