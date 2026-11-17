# -*- coding: utf-8 -*-
"""Traduzioni della 1.4.1147 (17 nov 2026): Specimen Variabile con stili onesti e zone standard (episodio 48).
Aggiunge le voci che mancano ai .po di de_DE, en_GB, en_US, es_ES, fr_FR e ricompila i .mo. Si può rilanciare.
uso: python po_1147.py
"""
import os
import subprocess
import sys

ROOT = os.path.normpath(os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', '..'))
LANG = os.path.join(ROOT, 'languages')
MSGFMT = os.path.join(os.path.dirname(sys.executable), 'Tools', 'i18n', 'msgfmt.py')
VERSIONE = 'OLObuild 1.4.1155'

# italiano: (tedesco, inglese, spagnolo, francese)
V = {
    'Lettera, parola o breve frase mostrata in grande. Si scrive anche direttamente nel canvas.': ('Buchstabe, Wort oder kurzer Satz in groß. Lässt sich auch direkt im Canvas schreiben.', 'A letter, word or short phrase shown large. You can also type it straight on the canvas.', 'Letra, palabra o frase breve mostrada en grande. También se escribe directamente en el lienzo.', 'Lettre, mot ou courte phrase affichés en grand. Se saisit aussi directement dans le canevas.'),
    'Variazione testo': ('Textvariation', 'Text variation', 'Variación de texto', 'Variation de texte'),
    'Nome': ('Name', 'Name', 'Nombre', 'Nom'),
    'Sigla': ('Kürzel', 'Tag', 'Sigla', 'Sigle'),
    'Le quattro lettere dell’asse nel font: wght peso, wdth larghezza, slnt inclinazione, ital corsivo, opsz misura ottica; gli assi propri del font sono in maiuscolo (CASL, MONO, GRAD).': ('Die vier Buchstaben der Achse in der Schrift: wght Stärke, wdth Breite, slnt Neigung, ital kursiv, opsz optische Größe; eigene Achsen der Schrift stehen in Großbuchstaben (CASL, MONO, GRAD).', 'The four letters of the axis in the font: wght weight, wdth width, slnt slant, ital italic, opsz optical size; the font’s own axes are in capitals (CASL, MONO, GRAD).', 'Las cuatro letras del eje en la fuente: wght peso, wdth anchura, slnt inclinación, ital cursiva, opsz tamaño óptico; los ejes propios de la fuente van en mayúsculas (CASL, MONO, GRAD).', 'Les quatre lettres de l’axe dans la police : wght graisse, wdth largeur, slnt inclinaison, ital italique, opsz taille optique ; les axes propres à la police sont en majuscules (CASL, MONO, GRAD).'),
    'Trascina la lettera': ('Buchstaben ziehen', 'Drag the letter', 'Arrastra la letra', 'Faire glisser la lettre'),
    'Solo cursori': ('Nur Schieberegler', 'Sliders only', 'Solo controles deslizantes', 'Curseurs seulement'),
    'Trascina e cursori': ('Ziehen und Schieberegler', 'Drag and sliders', 'Arrastrar y controles', 'Glisser et curseurs'),
    'Pressione': ('Druck', 'Pressure', 'Presión', 'Pression'),
    '«Trascina»: in orizzontale si muove il primo asse, in verticale il secondo. «Pressione»: ogni lettera va verso il massimo dei suoi assi quando il puntatore (o il dito, scorrendo di lato) le passa vicino, e torna al valore iniziale con una molla; per un effetto elastico tieni basso il valore iniziale (per esempio larghezza 60 su 50–150).': ('«Ziehen»: waagerecht bewegt sich die erste Achse, senkrecht die zweite. «Druck»: jeder Buchstabe geht zum Maximum seiner Achsen, wenn der Zeiger (oder der Finger, seitlich wischend) nahe kommt, und federt zum Startwert zurück; für einen elastischen Effekt den Startwert niedrig halten (z. B. Breite 60 bei 50–150).', '«Drag»: horizontally you move the first axis, vertically the second. «Pressure»: each letter goes towards the maximum of its axes when the pointer (or a finger swiping sideways) passes close, and springs back to its starting value; for an elastic effect keep the starting value low (for example width 60 on 50–150).', '«Arrastrar»: en horizontal se mueve el primer eje, en vertical el segundo. «Presión»: cada letra va hacia el máximo de sus ejes cuando el puntero (o el dedo, deslizando de lado) pasa cerca, y vuelve al valor inicial con un muelle; para un efecto elástico mantén bajo el valor inicial (por ejemplo anchura 60 sobre 50–150).', '«Glisser» : à l’horizontale bouge le premier axe, à la verticale le second. «Pression» : chaque lettre va vers le maximum de ses axes quand le pointeur (ou le doigt, en glissant de côté) passe près, puis revient à sa valeur de départ avec un ressort ; pour un effet élastique gardez une valeur de départ basse (par exemple largeur 60 sur 50–150).'),
    'Suggerimento': ('Hinweis', 'Hint', 'Sugerencia', 'Indication'),
    'Si muove da solo a riposo': ('Bewegt sich im Ruhezustand von selbst', 'Moves by itself when idle', 'Se mueve solo en reposo', 'Bouge tout seul au repos'),
    'Quando nessuno la tocca, gli assi oscillano piano (con «Pressione» un’onda attraversa la parola). Chi ha chiesto meno movimento al sistema la vede ferma.': ('Wenn niemand sie berührt, schwingen die Achsen sanft (mit «Druck» läuft eine Welle durch das Wort). Wer im System weniger Bewegung gewählt hat, sieht sie still.', 'When nobody touches it, the axes sway gently (with «Pressure» a wave runs through the word). People who asked their system for less motion see it still.', 'Cuando nadie la toca, los ejes oscilan despacio (con «Presión» una onda recorre la palabra). Quien pidió menos movimiento al sistema la ve quieta.', 'Quand personne ne la touche, les axes oscillent doucement (avec «Pression» une vague traverse le mot). Qui a demandé moins de mouvement au système la voit immobile.'),
    'Durata di un’onda': ('Dauer einer Welle', 'Length of one wave', 'Duración de una onda', 'Durée d’une vague'),
    'Mostra i valori': ('Werte anzeigen', 'Show the values', 'Mostrar los valores', 'Afficher les valeurs'),
    'Pieno': ('Voll', 'Solid', 'Lleno', 'Plein'),
    'I cursori e i valori.': ('Die Schieberegler und die Werte.', 'The sliders and the values.', 'Los controles deslizantes y los valores.', 'Les curseurs et les valeurs.'),
    'Un font variabile, per esempio Recursive o Roboto Flex: solo i font variabili cambiano davvero coi cursori. Vuoto: il carattere del tema.': ('Eine variable Schrift, zum Beispiel Recursive oder Roboto Flex: nur variable Schriften ändern sich wirklich mit den Reglern. Leer: die Schrift des Themes.', 'A variable font, for example Recursive or Roboto Flex: only variable fonts really change with the sliders. Empty: the theme’s font.', 'Una fuente variable, por ejemplo Recursive o Roboto Flex: solo las fuentes variables cambian de verdad con los controles. Vacío: la fuente del tema.', 'Une police variable, par exemple Recursive ou Roboto Flex : seules les polices variables changent vraiment avec les curseurs. Vide : la police du thème.'),
    'Peso di riserva': ('Ersatzstärke', 'Fallback weight', 'Peso de reserva', 'Graisse de secours'),
    'Il peso fisso che si vede se il browser non sa variare il font.': ('Die feste Stärke, die erscheint, wenn der Browser die Schrift nicht variieren kann.', 'The fixed weight shown if the browser cannot vary the font.', 'El peso fijo que se ve si el navegador no sabe variar la fuente.', 'La graisse fixe affichée si le navigateur ne sait pas faire varier la police.'),
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
