# -*- coding: utf-8 -*-
"""Traduzioni della 1.4.981–984 (10 nov 2026): la categoria «Artigiani e mestieri», le zone dei dieci temi dei mestieri,
i comandi nuovi del Pro Slider (stati e livello da pulire) e i dieci cursori. Aggiunge le voci che mancano ai .po di
de_DE, en_GB, en_US, es_ES, fr_FR e ricompila i .mo (msgfmt di Python). Si può rilanciare.
uso: python po_981.py
"""
import os
import subprocess
import sys

ROOT = os.path.normpath(os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', '..'))
LANG = os.path.join(ROOT, 'languages')
MSGFMT = os.path.join(os.path.dirname(sys.executable), 'Tools', 'i18n', 'msgfmt.py')

# italiano: (tedesco, inglese, spagnolo, francese)
V = {
    'Artigiani e mestieri': ('Handwerk und Berufe', 'Trades and crafts', 'Oficios y artesanos', 'Artisans et métiers'),
    'È saltata la luce': ('Der Strom ist weg', 'The power has tripped', 'Se ha ido la luz', 'Plus de courant'),
    'La perdita da riparare': ('Das Leck, das man repariert', 'The leak to fix', 'La fuga que reparar', 'La fuite à réparer'),
    'La prima mano, la seconda': ('Der erste Anstrich, der zweite', 'The first coat, then the second', 'La primera mano, la segunda',
                                  'La première couche, la seconde'),
    'Il muro che si alza': ('Die Mauer, die wächst', 'The wall that rises', 'El muro que se levanta', 'Le mur qui monte'),
    'La tavola tagliata': ('Das zersägte Brett', 'The plank that gets cut', 'El tablón cortado', 'La planche sciée'),
    'Un colpo per gesto': ('Ein Schlag pro Geste', 'One blow per gesture', 'Un golpe por gesto', 'Un coup par geste'),
    'La spia accesa': ('Die Warnleuchte brennt', 'The warning light is on', 'El testigo encendido', 'Le voyant allumé'),
    'Puliscimi': ('Mach mich sauber', 'Clean me', 'Límpiame', 'Nettoie-moi'),
    'L’erba che cresce': ('Das Gras, das wächst', 'The grass that grows', 'La hierba que crece', 'L’herbe qui pousse'),
    'Rompilo pure': ('Schlag ruhig zu', 'Go on, break it', 'Rómpelo, adelante', 'Vas-y, casse-le'),
    # Pro Slider: gli stati
    'Imposta uno stato': ('Einen Zustand setzen', 'Set a state', 'Fijar un estado', 'Définir un état'),
    'Stato successivo (a giro)': ('Nächster Zustand (im Kreis)', 'Next state (in a loop)', 'Estado siguiente (en bucle)',
                                  'État suivant (en boucle)'),
    'Nome dello stato': ('Name des Zustands', 'State name', 'Nombre del estado', 'Nom de l’état'),
    'Stati': ('Zustände', 'States', 'Estados', 'États'),
    'Uno stato è un nome con un valore (luce = accesa). I livelli con «Visibile solo in uno stato» si mostrano quando lo stato ha '
    'uno dei loro valori. Il valore all’avvio si scrive nelle impostazioni dello slider, «Stati all’avvio».': (
        'Ein Zustand ist ein Name mit einem Wert (Licht = an). Ebenen mit «Nur in einem Zustand sichtbar» erscheinen, wenn der '
        'Zustand einen ihrer Werte hat. Der Startwert steht in den Slider-Einstellungen unter «Zustände beim Start».',
        'A state is a name with a value (light = on). Layers with “Visible only in a state” show up when the state has one of '
        'their values. The starting value goes in the slider settings, “States at start”.',
        'Un estado es un nombre con un valor (luz = encendida). Las capas con «Visible solo en un estado» aparecen cuando el '
        'estado tiene uno de sus valores. El valor inicial se escribe en los ajustes del slider, «Estados al inicio».',
        'Un état est un nom avec une valeur (lumière = allumée). Les calques « Visible seulement dans un état » apparaissent '
        'quand l’état a l’une de leurs valeurs. La valeur de départ se règle dans le slider, « États au départ ».'),
    'Colore dello stato': ('Farbe des Zustands', 'State colour', 'Color del estado', 'Couleur de l’état'),
    'Facoltativo: il colore va nella variabile --mps-st-nome, che i colori dei livelli possono usare, per esempio '
    'var(--mps-st-tinta, #ffffff). Il cambio di colore è sfumato.': (
        'Optional: Die Farbe geht in die Variable --mps-st-name, die die Farben der Ebenen verwenden können, zum Beispiel '
        'var(--mps-st-tinta, #ffffff). Der Farbwechsel ist weich.',
        'Optional: the colour goes into the --mps-st-name variable, which layer colours can use, for example '
        'var(--mps-st-tinta, #ffffff). The colour change fades.',
        'Opcional: el color va a la variable --mps-st-nombre, que los colores de las capas pueden usar, por ejemplo '
        'var(--mps-st-tinta, #ffffff). El cambio de color es suave.',
        'Facultatif : la couleur va dans la variable --mps-st-nom, que les couleurs des calques peuvent utiliser, par exemple '
        'var(--mps-st-tinta, #ffffff). Le changement de couleur est fondu.'),
    'Valori, in ordine': ('Werte, der Reihe nach', 'Values, in order', 'Valores, en orden', 'Valeurs, dans l’ordre'),
    'Separati da virgole: a ogni clic lo stato passa al valore dopo (1, 2, 3, 4). Due valori fanno un interruttore.': (
        'Durch Kommas getrennt: Bei jedem Klick geht der Zustand zum nächsten Wert (1, 2, 3, 4). Zwei Werte ergeben einen Schalter.',
        'Separated by commas: each click moves the state to the next value (1, 2, 3, 4). Two values make a switch.',
        'Separados por comas: cada clic pasa el estado al valor siguiente (1, 2, 3, 4). Dos valores hacen un interruptor.',
        'Séparées par des virgules : chaque clic fait passer l’état à la valeur suivante (1, 2, 3, 4). Deux valeurs font un interrupteur.'),
    'Stato successivo': ('Nächster Zustand', 'Next state', 'Estado siguiente', 'État suivant'),
    'Dall’ultimo valore ricomincia': ('Nach dem letzten Wert von vorn', 'Starts again after the last value',
                                     'Tras el último valor vuelve a empezar', 'Recommence après la dernière valeur'),
    'Nome per chi usa la tastiera o un lettore di schermo': ('Name für Tastatur und Screenreader', 'Name for keyboard and screen reader users',
                                                            'Nombre para teclado y lectores de pantalla', 'Nom pour le clavier et les lecteurs d’écran'),
    'Accendi la luce': ('Licht einschalten', 'Turn the light on', 'Enciende la luz', 'Allume la lumière'),
    'Visibile solo in uno stato': ('Nur in einem Zustand sichtbar', 'Visible only in a state', 'Visible solo en un estado',
                                   'Visible seulement dans un état'),
    'Il livello si mostra quando lo stato ha uno dei valori scritti (separati da virgole) e si nasconde negli altri casi. Con il '
    'punto esclamativo davanti vale il contrario: «!accesa» = finché non è accesa. Lo stato lo cambiano i livelli con l’azione '
    '«Imposta uno stato» o «Stato successivo».': (
        'Die Ebene erscheint, wenn der Zustand einen der geschriebenen Werte hat (durch Kommas getrennt), sonst verschwindet sie. Mit '
        'einem Ausrufezeichen davor gilt das Gegenteil: «!an» = solange es nicht an ist. Den Zustand ändern Ebenen mit der Aktion '
        '«Einen Zustand setzen» oder «Nächster Zustand».',
        'The layer shows when the state has one of the values written (separated by commas) and hides otherwise. With an '
        'exclamation mark in front it is the opposite: “!on” = as long as it is not on. Layers with the “Set a state” or '
        '“Next state” action change the state.',
        'La capa aparece cuando el estado tiene uno de los valores escritos (separados por comas) y se oculta en los demás casos. '
        'Con el signo de exclamación delante vale lo contrario: «!encendida» = mientras no esté encendida. El estado lo cambian '
        'las capas con la acción «Fijar un estado» o «Estado siguiente».',
        'Le calque apparaît quand l’état a l’une des valeurs écrites (séparées par des virgules) et se cache sinon. Avec un point '
        'd’exclamation devant, c’est l’inverse : « !allumée » = tant qu’elle n’est pas allumée. L’état est changé par les '
        'calques ayant l’action « Définir un état » ou « État suivant ».'),
    'Come compare': ('Wie es erscheint', 'How it appears', 'Cómo aparece', 'Comment il apparaît'),
    'Sale dal basso': ('Steigt von unten', 'Rises from below', 'Sube desde abajo', 'Monte du bas'),
    'Scende dall’alto': ('Kommt von oben', 'Drops from above', 'Baja desde arriba', 'Descend du haut'),
    'Si ingrandisce': ('Wird größer', 'Grows', 'Se agranda', 'S’agrandit'),
    'Subito, senza effetto': ('Sofort, ohne Effekt', 'At once, no effect', 'Enseguida, sin efecto', 'Tout de suite, sans effet'),
    # Pro Slider: il livello da pulire
    'Da pulire col puntatore': ('Mit dem Zeiger zu putzen', 'Wipe clean with the pointer', 'Para limpiar con el puntero',
                                'À nettoyer avec le pointeur'),
    'L’immagine copre quello che c’è sotto e si cancella passandoci sopra col mouse (col dito sul telefono). Pulita la parte '
    'scelta, il resto svanisce e lo stato scritto (nome=valore) cambia: così compaiono i livelli di «dopo».': (
        'Das Bild deckt ab, was darunter liegt, und verschwindet, wo man mit der Maus darüberfährt (am Telefon mit dem Finger). Ist '
        'der gewählte Anteil sauber, verschwindet der Rest und der geschriebene Zustand (Name=Wert) ändert sich: So erscheinen die '
        'Ebenen für «danach».',
        'The image covers what is underneath and wipes away where the mouse passes over it (a finger on the phone). Once the '
        'chosen share is clean, the rest fades and the state written (name=value) changes: the “after” layers appear.',
        'La imagen cubre lo que hay debajo y se borra al pasar el ratón por encima (el dedo en el teléfono). Limpia la parte '
        'elegida, el resto desaparece y el estado escrito (nombre=valor) cambia: así aparecen las capas de «después».',
        'L’image couvre ce qui est dessous et s’efface là où passe la souris (le doigt sur le téléphone). Une fois la part choisie '
        'nettoyée, le reste disparaît et l’état écrit (nom=valeur) change : les calques « après » apparaissent.'),
    'Pulito a': ('Sauber bei', 'Clean at', 'Limpio al', 'Propre à'),
    'Colore sotto l’immagine': ('Farbe unter dem Bild', 'Colour under the image', 'Color bajo la imagen', 'Couleur sous l’image'),
    'Stato quando è pulito': ('Zustand, wenn sauber', 'State when clean', 'Estado cuando está limpio', 'État une fois propre'),
    'Stati all’avvio': ('Zustände beim Start', 'States at start', 'Estados al inicio', 'États au départ'),
    'I livelli possono cambiare al clic: l’azione «Imposta uno stato» o «Stato successivo» dà un valore a uno stato (luce = accesa) '
    'e i livelli con «Visibile solo in uno stato» si mostrano o si nascondono. Qui il valore di partenza di ogni stato, nome=valore '
    'separati da punto e virgola; uno stato non scritto parte vuoto.': (
        'Ebenen können sich beim Klick ändern: Die Aktion «Einen Zustand setzen» oder «Nächster Zustand» gibt einem Zustand einen '
        'Wert (Licht = an), und die Ebenen mit «Nur in einem Zustand sichtbar» erscheinen oder verschwinden. Hier der Startwert jedes '
        'Zustands, Name=Wert durch Semikolon getrennt; ein nicht genannter Zustand beginnt leer.',
        'Layers can change on click: the “Set a state” or “Next state” action gives a state a value (light = on) and layers with '
        '“Visible only in a state” show or hide. Here the starting value of each state, name=value separated by semicolons; a '
        'state not written here starts empty.',
        'Las capas pueden cambiar al hacer clic: la acción «Fijar un estado» o «Estado siguiente» da un valor a un estado '
        '(luz = encendida) y las capas con «Visible solo en un estado» aparecen o se ocultan. Aquí el valor inicial de cada estado, '
        'nombre=valor separados por punto y coma; un estado no escrito empieza vacío.',
        'Les calques peuvent changer au clic : l’action « Définir un état » ou « État suivant » donne une valeur à un état '
        '(lumière = allumée) et les calques « Visible seulement dans un état » apparaissent ou se cachent. Ici la valeur de départ '
        'de chaque état, nom=valeur séparés par des points-virgules ; un état non écrit part vide.'),
    # i cursori
    'Arco elettrico (la scarica salta sui link)': ('Lichtbogen (der Funke springt auf die Links)', 'Electric arc (the spark jumps to links)',
                                                   'Arco eléctrico (la descarga salta a los enlaces)', 'Arc électrique (l’étincelle saute sur les liens)'),
    'Gocciolio (la goccia cade e schizza)': ('Tropfen (fällt und spritzt)', 'Drip (the drop falls and splashes)',
                                             'Goteo (la gota cae y salpica)', 'Goutte-à-goutte (la goutte tombe et éclabousse)'),
    'Rullo (lascia una pennellata)': ('Farbrolle (hinterlässt einen Strich)', 'Paint roller (leaves a stroke)',
                                      'Rodillo (deja una pincelada)', 'Rouleau (laisse une trace)'),
    'Livella (la bolla torna in mezzo)': ('Wasserwaage (die Blase kehrt in die Mitte zurück)', 'Spirit level (the bubble returns to centre)',
                                          'Nivel (la burbuja vuelve al centro)', 'Niveau (la bulle revient au centre)'),
    'Trucioli (la pialla stacca i ricci)': ('Hobelspäne (der Hobel löst Locken)', 'Shavings (the plane curls them off)',
                                            'Virutas (el cepillo levanta rizos)', 'Copeaux (le rabot les fait boucler)'),
    'Martello (al clic batte e fa scintille)': ('Hammer (schlägt beim Klick und sprüht Funken)', 'Hammer (strikes on click, with sparks)',
                                                'Martillo (golpea al clic y saca chispas)', 'Marteau (frappe au clic et fait des étincelles)'),
    'Bullone (si avvita sui link)': ('Schraube (dreht sich auf den Links fest)', 'Bolt (screws in on links)',
                                     'Tornillo (se enrosca en los enlaces)', 'Boulon (se visse sur les liens)'),
    'Scovolo (lascia la fuliggine)': ('Kaminbürste (hinterlässt Ruß)', 'Chimney brush (leaves soot)', 'Escobillón (deja hollín)',
                                      'Hérisson de ramoneur (laisse de la suie)'),
    'Irrigatore (spruzza a ventaglio)': ('Rasensprenger (sprüht im Fächer)', 'Sprinkler (sprays in a fan)', 'Aspersor (rocía en abanico)',
                                         'Arroseur (asperge en éventail)'),
    'Tagliavetro (graffia e crepa)': ('Glasschneider (ritzt und lässt springen)', 'Glass cutter (scratches and cracks)',
                                      'Cortavidrios (raya y agrieta)', 'Coupe-verre (raye et fêle)'),
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
            nuove.append('\n#: OLObuild 1.4.981\nmsgid "%s"\nmsgstr "%s"\n' % (esc(it), esc(tr[i])))
        if nuove:
            with open(po, 'a', encoding='utf-8', newline='\n') as f:
                f.write(''.join(nuove))
        subprocess.run([sys.executable, MSGFMT, '-o', po[:-3] + '.mo', po], check=True)
        print(loc, 'voci nuove:', len(nuove))


if __name__ == '__main__':
    main()
