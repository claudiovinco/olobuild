# -*- coding: utf-8 -*-
"""Traduzioni della 1.4.1012 (15 nov 2026): Specimen variabile «Pressione», Gratta e scopri al passaggio / pennello
morbido / copertura che ricresce, condizione «Mesi dell'anno», Schede prodotto «Seconda foto al passaggio».
Aggiunge le voci che mancano ai .po di de_DE, en_GB, en_US, es_ES, fr_FR e ricompila i .mo. Si può rilanciare.
uso: python po_1012.py
"""
import os
import subprocess
import sys

ROOT = os.path.normpath(os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', '..'))
LANG = os.path.join(ROOT, 'languages')
MSGFMT = os.path.join(os.path.dirname(sys.executable), 'Tools', 'i18n', 'msgfmt.py')

# italiano: (tedesco, inglese, spagnolo, francese)
V = {
    'Pressione: le lettere si tendono vicino al puntatore': (
        'Druck: Die Buchstaben dehnen sich in der Nähe des Zeigers', 'Pressure: letters stretch near the pointer',
        'Presión: las letras se estiran cerca del puntero', 'Pression : les lettres s’étirent près du pointeur'),
    'Con «Pressione» ogni lettera va verso il massimo dei suoi assi quando il puntatore (o il dito, scorrendo di lato) le passa vicino, e torna al valore di partenza con una molla. Il valore di partenza è quello «Predefinito» di ogni asse: per un effetto «elastico» tienilo basso (per esempio larghezza 60 su 50–150).': (
        'Mit „Druck“ geht jeder Buchstabe zum Höchstwert seiner Achsen, wenn der Zeiger (oder der Finger, seitlich gewischt) in seine Nähe kommt, und kehrt mit einer Feder zum Ausgangswert zurück. Der Ausgangswert ist der „Standard“ jeder Achse: Für einen elastischen Effekt niedrig halten (zum Beispiel Breite 60 bei 50–150).',
        'With “Pressure” each letter moves towards the maximum of its axes when the pointer (or a finger swiping sideways) passes near it, and springs back to its starting value. The starting value is each axis’s “Default”: keep it low for an elastic effect (for example width 60 on 50–150).',
        'Con «Presión» cada letra va hacia el máximo de sus ejes cuando el puntero (o el dedo, deslizando de lado) pasa cerca, y vuelve al valor inicial con un muelle. El valor inicial es el «Predeterminado» de cada eje: para un efecto elástico, mantenlo bajo (por ejemplo ancho 60 en 50–150).',
        'Avec « Pression », chaque lettre va vers le maximum de ses axes quand le pointeur (ou le doigt, glissé sur le côté) passe près d’elle, et revient à sa valeur de départ avec un ressort. La valeur de départ est la « Par défaut » de chaque axe : gardez-la basse pour un effet élastique (par exemple largeur 60 sur 50–150).'),
    'Quando nessuno interagisce, gli assi oscillano dolcemente (con «Pressione», un\'onda attraversa la parola). Rispetta prefers-reduced-motion (resta fermo).': (
        'Wenn niemand interagiert, schwingen die Achsen sanft (bei „Druck“ läuft eine Welle durch das Wort). Respektiert prefers-reduced-motion (bleibt still).',
        'When nobody interacts, the axes sway gently (with “Pressure”, a wave runs through the word). Respects prefers-reduced-motion (stays still).',
        'Cuando nadie interactúa, los ejes oscilan suavemente (con «Presión», una ola recorre la palabra). Respeta prefers-reduced-motion (se queda quieto).',
        'Quand personne n’interagit, les axes ondulent doucement (avec « Pression », une vague traverse le mot). Respecte prefers-reduced-motion (reste immobile).'),
    'Gratta al passaggio del mouse': (
        'Beim Überfahren mit der Maus rubbeln', 'Scratch on mouse hover', 'Rascar al pasar el ratón', 'Gratter au survol de la souris'),
    'Col mouse basta passare sopra, senza tenere premuto. Sul telefono si gratta trascinando il dito, come sempre.': (
        'Mit der Maus genügt das Überfahren, ohne zu drücken. Auf dem Handy rubbelt man wie immer mit dem Finger.',
        'With a mouse, just pass over it without holding the button. On phones you scratch by dragging a finger, as always.',
        'Con el ratón basta pasar por encima, sin mantener pulsado. En el móvil se rasca arrastrando el dedo, como siempre.',
        'À la souris, il suffit de passer dessus sans appuyer. Sur téléphone on gratte en faisant glisser le doigt, comme toujours.'),
    'Pennello morbido': ('Weicher Pinsel', 'Soft brush', 'Pincel suave', 'Pinceau doux'),
    'Il tratto ha i bordi sfumati e scopre poco a poco: ogni passaggio schiarisce un po\' di più, come una mano sul velluto o sul vetro appannato.': (
        'Der Strich hat weiche Ränder und legt nach und nach frei: Jeder Durchgang hellt etwas mehr auf, wie eine Hand auf Samt oder auf beschlagenem Glas.',
        'The stroke has soft edges and reveals little by little: each pass lightens a bit more, like a hand on velvet or on fogged glass.',
        'El trazo tiene bordes difuminados y descubre poco a poco: cada pasada aclara un poco más, como una mano sobre terciopelo o sobre un cristal empañado.',
        'Le trait a des bords flous et découvre peu à peu : chaque passage éclaircit un peu plus, comme une main sur du velours ou sur une vitre embuée.'),
    'La copertura ricresce in': ('Die Abdeckung wächst nach in', 'The cover grows back in', 'La cobertura vuelve a crecer en',
                                 'La couverture repousse en'),
    'Dopo l\'ultimo tocco la copertura torna piano piano piena in questi secondi (il pelo del velluto che si rialza, il vetro che si appanna di nuovo). 0 = resta grattata. Con la ricrescita non c\'è lo scoprimento automatico.': (
        'Nach der letzten Berührung wird die Abdeckung in diesen Sekunden langsam wieder voll (der Samtflor richtet sich auf, das Glas beschlägt wieder). 0 = bleibt freigerubbelt. Mit dem Nachwachsen gibt es kein automatisches Aufdecken.',
        'After the last touch the cover slowly fills back in over these seconds (the velvet pile rising again, the glass fogging up again). 0 = stays scratched. With regrowth there is no automatic reveal.',
        'Tras el último toque la cobertura vuelve poco a poco a llenarse en estos segundos (el pelo del terciopelo que se levanta, el cristal que se empaña de nuevo). 0 = queda rascada. Con el crecimiento no hay descubrimiento automático.',
        'Après le dernier toucher, la couverture redevient pleine peu à peu en ces secondes (le poil du velours qui se relève, la vitre qui s’embue de nouveau). 0 = reste grattée. Avec la repousse, pas de découverte automatique.'),
    'Mesi dell\'anno': ('Monate des Jahres', 'Months of the year', 'Meses del año', 'Mois de l’année'),
    'Dal mese': ('Ab Monat', 'From month', 'Desde el mes', 'Du mois'),
    'Al mese': ('Bis Monat', 'To month', 'Hasta el mes', 'Au mois'),
    'Dal mese (cond. 2)': ('Ab Monat (Bed. 2)', 'From month (cond. 2)', 'Desde el mes (cond. 2)', 'Du mois (cond. 2)'),
    'Al mese (cond. 2)': ('Bis Monat (Bed. 2)', 'To month (cond. 2)', 'Hasta el mes (cond. 2)', 'Au mois (cond. 2)'),
    'Da questo mese all\'altro, compresi. Anche a cavallo d\'anno: da novembre a febbraio. Con un solo mese, lo stesso nei due campi.': (
        'Von diesem Monat bis zum anderen, einschließlich. Auch über den Jahreswechsel: von November bis Februar. Für einen einzelnen Monat denselben in beiden Feldern.',
        'From this month to the other, inclusive. Also across the new year: from November to February. For a single month, the same in both fields.',
        'De este mes al otro, incluidos. También a caballo entre dos años: de noviembre a febrero. Para un solo mes, el mismo en los dos campos.',
        'De ce mois à l’autre, inclus. Aussi à cheval sur deux années : de novembre à février. Pour un seul mois, le même dans les deux champs.'),
    'Gennaio': ('Januar', 'January', 'Enero', 'Janvier'), 'Febbraio': ('Februar', 'February', 'Febrero', 'Février'),
    'Marzo': ('März', 'March', 'Marzo', 'Mars'), 'Aprile': ('April', 'April', 'Abril', 'Avril'),
    'Maggio': ('Mai', 'May', 'Mayo', 'Mai'), 'Giugno': ('Juni', 'June', 'Junio', 'Juin'),
    'Luglio': ('Juli', 'July', 'Julio', 'Juillet'), 'Agosto': ('August', 'August', 'Agosto', 'Août'),
    'Settembre': ('September', 'September', 'Septiembre', 'Septembre'), 'Ottobre': ('Oktober', 'October', 'Octubre', 'Octobre'),
    'Novembre': ('November', 'November', 'Noviembre', 'Novembre'), 'Dicembre': ('Dezember', 'December', 'Diciembre', 'Décembre'),
    'Seconda foto al passaggio': ('Zweites Foto beim Überfahren', 'Second photo on hover', 'Segunda foto al pasar el ratón',
                                  'Seconde photo au survol'),
    'Col mouse sopra la scheda, la foto lascia il posto a questa (un altro lato del prodotto, il dettaglio, l\'abbinamento). Sul telefono resta la prima.': (
        'Mit der Maus über der Karte weicht das Foto diesem (eine andere Seite des Produkts, das Detail, die Kombination). Auf dem Handy bleibt das erste.',
        'With the mouse over the card, the photo gives way to this one (another side of the product, the detail, the pairing). On phones the first one stays.',
        'Con el ratón sobre la tarjeta, la foto deja paso a esta (otro lado del producto, el detalle, la combinación). En el móvil queda la primera.',
        'Souris sur la carte, la photo laisse la place à celle-ci (un autre côté du produit, le détail, l’association). Sur téléphone, la première reste.'),
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
            nuove.append('\n#: OLObuild 1.4.1012\nmsgid "%s"\nmsgstr "%s"\n' % (esc(it), esc(tr[i])))
        if nuove:
            with open(po, 'a', encoding='utf-8', newline='\n') as f:
                f.write(''.join(nuove))
        subprocess.run([sys.executable, MSGFMT, '-o', po[:-3] + '.mo', po], check=True)
        print(loc, 'voci nuove:', len(nuove))


if __name__ == '__main__':
    main()
