# -*- coding: utf-8 -*-
"""Traduzioni della 1.4.993 (13 nov 2026): «Scuoti» e le molecole del Cesto fisico.
Aggiunge le voci che mancano ai .po di de_DE, en_GB, en_US, es_ES, fr_FR e ricompila i .mo. Si può rilanciare.
uso: python po_993.py
"""
import os
import subprocess
import sys

ROOT = os.path.normpath(os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', '..'))
LANG = os.path.join(ROOT, 'languages')
MSGFMT = os.path.join(os.path.dirname(sys.executable), 'Tools', 'i18n', 'msgfmt.py')

# italiano: (tedesco, inglese, spagnolo, francese)
V = {
    'Scuoti': ('Schütteln', 'Shake', 'Agitar', 'Secouer'),
    'Scuoti e molecole': ('Schütteln und Moleküle', 'Shake and molecules', 'Agitar y moléculas', 'Secouer et molécules'),
    'Pulsante «Scuoti»': ('Schaltfläche «Schütteln»', '“Shake” button', 'Botón «Agitar»', 'Bouton « Secouer »'),
    'Un pulsante nel cesto lancia in aria gli oggetti. Sul telefono basta scuotere il telefono: su iPhone dopo il primo tocco sul pulsante, che chiede il permesso.': (
        'Eine Schaltfläche im Korb wirft die Objekte in die Luft. Auf dem Handy genügt es, das Handy zu schütteln: auf dem iPhone nach dem ersten Tippen auf die Schaltfläche, die um Erlaubnis bittet.',
        'A button in the bin throws the objects into the air. On a phone, just shake the phone: on iPhone after the first tap on the button, which asks for permission.',
        'Un botón en la cesta lanza los objetos al aire. En el móvil basta con agitar el móvil: en iPhone después del primer toque en el botón, que pide permiso.',
        'Un bouton dans le bac lance les objets en l’air. Sur le téléphone, il suffit de secouer le téléphone : sur iPhone après le premier appui sur le bouton, qui demande l’autorisation.'),
    'Scritta del pulsante': ('Beschriftung der Schaltfläche', 'Button label', 'Texto del botón', 'Texte du bouton'),
    'Vuota: «Scuoti».': ('Leer: «Schütteln».', 'Empty: “Shake”.', 'Vacío: «Agitar».', 'Vide : « Secouer ».'),
    'Gli elementi si legano': ('Die Elemente verbinden sich', 'The elements bond', 'Los elementos se enlazan', 'Les éléments se lient'),
    'Gli oggetti col simbolo di un elemento chimico (H, O, C, Na, Cl…) si legano quando si toccano, secondo la valenza, e sopra ogni molecola compare la formula. Scuotendo, i legami si rompono e si riformano in un altro modo. Gli altri oggetti restano liberi.': (
        'Objekte mit dem Symbol eines chemischen Elements (H, O, C, Na, Cl…) verbinden sich bei Berührung entsprechend ihrer Wertigkeit, und über jedem Molekül erscheint die Formel. Beim Schütteln brechen die Bindungen und bilden sich anders neu. Die anderen Objekte bleiben frei.',
        'Objects with the symbol of a chemical element (H, O, C, Na, Cl…) bond when they touch, according to their valence, and the formula appears above each molecule. Shaking breaks the bonds and they form again in a different way. The other objects stay free.',
        'Los objetos con el símbolo de un elemento químico (H, O, C, Na, Cl…) se enlazan al tocarse, según su valencia, y sobre cada molécula aparece la fórmula. Al agitar, los enlaces se rompen y se vuelven a formar de otra manera. Los demás objetos quedan libres.',
        'Les objets portant le symbole d’un élément chimique (H, O, C, Na, Cl…) se lient quand ils se touchent, selon leur valence, et la formule apparaît au-dessus de chaque molécule. En secouant, les liaisons se rompent et se reforment autrement. Les autres objets restent libres.'),
    'Nomi delle molecole': ('Namen der Moleküle', 'Molecule names', 'Nombres de las moléculas', 'Noms des molécules'),
    'Molecola': ('Molekül', 'Molecule', 'Molécula', 'Molécule'),
    'Formula': ('Formel', 'Formula', 'Fórmula', 'Formule'),
    'Nome': ('Name', 'Name', 'Nombre', 'Nom'),
    'Come la scriveresti: H2O, NaOH, Sr(OH)2. Vale per ogni molecola con gli stessi atomi.': (
        'So, wie du sie schreiben würdest: H2O, NaOH, Sr(OH)2. Gilt für jedes Molekül mit denselben Atomen.',
        'As you would write it: H2O, NaOH, Sr(OH)2. It applies to every molecule with the same atoms.',
        'Como la escribirías: H2O, NaOH, Sr(OH)2. Vale para cada molécula con los mismos átomos.',
        'Comme tu l’écrirais : H2O, NaOH, Sr(OH)2. Elle vaut pour chaque molécule ayant les mêmes atomes.'),
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
            nuove.append('\n#: OLObuild 1.4.993\nmsgid "%s"\nmsgstr "%s"\n' % (esc(it), esc(tr[i])))
        if nuove:
            with open(po, 'a', encoding='utf-8', newline='\n') as f:
                f.write(''.join(nuove))
        subprocess.run([sys.executable, MSGFMT, '-o', po[:-3] + '.mo', po], check=True)
        print(loc, 'voci nuove:', len(nuove))


if __name__ == '__main__':
    main()
