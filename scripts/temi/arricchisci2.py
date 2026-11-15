# -*- coding: utf-8 -*-
"""Seconda passata sulle pagine scarne (14 nov 2026). L'utente: «arricchisci i temi piccoli, quelli di poche pagine, ma
le stesse pagine risultano quasi vuote, tutti i temi devono sembrare pronti per l'utilizzo appena dopo l'acquisto».
Le pagine interne con due sezioni (o tre ma poche parole) ricevono una o due sezioni scritte per quell'attività
(arricchimenti2.py). Per non riempire cento temi con gli stessi otto blocchi, oltre ai tipi di arricchisci.py ce ne
sono altri sette, fatti con tile diverse:
  ('listino', occhiello, riga1, riga2, [(voce, descrizione, prezzo)], nota)           → Pricelist
  ('righe', occhiello, riga1, riga2, [(numero o data, titolo, categoria, destra)], '@pagina') → Worklist
  ('carte', occhiello, riga1, riga2, [(foto, titolo, davanti, dietro)])                → Flip card (3 o 4)
  ('pila', occhiello, riga1, riga2, [(occhiello, titolo, accento, testo, foto)])      → Stack scroll
  ('dettagli', foto, alt, occhiello, riga1, riga2, [(voce, valore)])                  → foto + Description list
  ('orari', occhiello, riga1, riga2, [(voce, valore)], nota)                          → Description list centrata
  ('frase', testo, autore)                                                             → Quotation grande
Le foto sono SOLO quelle che il tema usa già, con la descrizione che hanno: la foto dice quello che la sezione racconta.
Le sezioni nuove hanno gli id «<tema>-<pagina>-y<n>» (quelle della 1.4.996 sono «-x»): si tolgono e si rimettono
rilanciando. Dopo l'aggiunta la pagina ripassa la catena di base.write_theme (indicazioni, linee grafiche, strutture,
gallerie, voce dei titoli), così le sezioni nuove prendono la linea del tema.
uso: python arricchisci2.py [tema …]"""
import json
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
sys.stdout.reconfigure(encoding='utf-8')
from base import Tpl, solid  # noqa: E402
import pezzi_nov as P  # noqa: E402
import arricchisci as A  # noqa: E402
from arricchimenti2 import SPEC2  # noqa: E402

TEMI = A.TEMI
CHIUDE = ('"form"', '"cta-banner"', '"osmmap"')


def sechead(K, t, occ, r1, r2, size=52):
    return K.sechead(t, occ, [r1, r2], size=size, size_mobile=36, layout='stack')


def costruisci(K, t, s, fondo):
    C = K.c
    bg = solid(C['sala']) if fondo else None
    stile = {'padding_top': 100, 'padding_bottom': 100}
    tipo = s[0]
    if tipo == 'listino':
        _, occ, r1, r2, voci = s[:5]
        nota = s[5] if len(s) > 5 else ''
        lis = P.listino(K, t, [(a, b, c, '', '') for a, b, c in voci], immagini=False)
        sin = [sechead(K, t, occ, r1, r2)] + ([K.text(t, nota, size=16)] if nota else [])
        return t.section([t.row([t.col(sin, wm='2-5', ws='1-1'), t.col([lis], wm='3-5', ws='1-1')], layout='custom',
                                custom='40,60', gap=64, valign='start')], bg=bg, padding='large', width='large', style=stile)
    if tipo == 'righe':
        _, occ, r1, r2, voci, dove = s[:6]
        el = P.righe(K, t, voci, A.link(dove), titolo=30)
        el['settings']['title_size_mobile'] = '20'  # sul telefono i titoli larghi andavano a capo a ogni parola
        return t.section([t.one([sechead(K, t, occ, r1, r2, size=56)]), t.one([el])],
                         bg=bg, padding='large', width='large', style=stile)
    if tipo == 'carte':
        _, occ, r1, r2, voci = s[:5]
        return t.section([t.one([sechead(K, t, occ, r1, r2, size=56)]), P.carte(K, t, voci, colonne=4 if len(voci) == 4 else 3)],
                         bg=bg, padding='large', width='large', style=stile)
    if tipo == 'pila':
        _, occ, r1, r2, voci = s[:5]
        pila = P.pila(K, t, [(e, ti, ac, tx, f or next(iter(K.media))) for e, ti, ac, tx, f in voci], altezza=380)
        for card, v in zip(pila['settings']['cards'], voci):
            if not v[4]:
                card['media'] = ''  # carta di solo testo: niente foto a caso
        if not any(v[4] for v in voci):
            pila['settings']['media_position'] = 'none'  # senza foto restava a destra un riquadro grigio vuoto
        return t.section([t.one([sechead(K, t, occ, r1, r2, size=56)]), t.one([pila])],
                         bg=bg, padding='large', width='large', style=stile)
    if tipo == 'dettagli':
        _, foto, alt, occ, r1, r2, voci = s[:7]
        return t.section([t.row([t.col([K.image(t, foto, alt, ratio='4/5')], wm='2-5', ws='1-1'),
                                 t.col([sechead(K, t, occ, r1, r2), K.specs(t, voci)], wm='3-5', ws='1-1')], layout='custom',
                                custom='42,58', gap=64, valign='center')], bg=bg, padding='large', width='large', style=stile)
    if tipo == 'orari':
        _, occ, r1, r2, voci = s[:5]
        nota = s[5] if len(s) > 5 else ''
        dentro = [sechead(K, t, occ, r1, r2), K.specs(t, voci)] + ([K.text(t, nota, size=15)] if nota else [])
        return t.section([K.centered(t, dentro)], bg=bg, padding='large', width='large', style=stile)
    if tipo == 'frase':
        _, testo, autore = s[:3]
        q = P.citazione(K, t, testo, autore, size='40')
        if K.display:
            q['settings'].update({'content_font_family': 'body', 'content_font_size': '30'})
        return t.section([K.centered(t, [q])], bg=bg, padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110})
    sez = A.costruisci(K, t, s, fondo)
    if tipo == 'citazione':
        # la firma nel colore del testo: nel tenue, sul fondo «sala», restava a 4,4:1 (misura del 14 nov 2026)
        def firma(n):
            if isinstance(n, dict):
                if n.get('type') == 'testimonial':
                    n['settings']['author_color'] = 'var(--olo-color-text)'
                for v in n.get('children') or []:
                    firma(v)
        firma(sez)
    return sez


def ripassa(tid, nome_file, built, theme, media):
    """La catena di base.write_theme sulle pagine toccate."""
    import togli_indicazioni
    import linee_grafiche
    import strutture
    import gallerie
    import titoli_stile
    import varia_passi
    togli_indicazioni.applica(built, [], tid)
    varia_passi.applica_tema(theme, nome_file[:-5], built, media)
    linee_grafiche.applica_tema(tid, nome_file, built)
    strutture.applica_tema(tid, nome_file, built)
    gallerie.applica_tema(tid, nome_file, built)
    titoli_stile.applica_tema(tid, nome_file, built)


def applica(tid, spec):
    d = os.path.join(TEMI, tid)
    th = json.load(open(os.path.join(d, 'theme.json'), encoding='utf-8'))
    media = A.media_del_tema(d)
    K = A.kit_del_tema(tid, th, media)
    K.media = media
    totale = 0
    for pagina, sezioni in spec.items():
        nome_file = th['templates'][th['pages'][pagina].get('template', pagina)]['file']
        f = os.path.join(d, nome_file)
        j = json.load(open(f, encoding='utf-8'))
        contenuto = j['content'] if isinstance(j, dict) and 'content' in j else j
        pref = '%s-%s-y' % (tid, pagina)
        contenuto[:] = [n for n in contenuto if not str(n.get('id', '')).startswith(pref)]
        # prima del modulo o dell'invito finale, se la pagina chiude così; se no in fondo
        dove = len(contenuto)
        if contenuto and any(c in json.dumps(contenuto[-1]) for c in CHIUDE) and len(contenuto) > 1:
            dove = len(contenuto) - 1
        t = Tpl(tid, pagina + '-y')
        fondo = not A.ha_fondo(contenuto[dove - 1]) if dove else False
        nuove = []
        for s in sezioni:
            nuove.append(costruisci(K, t, s, fondo))
            fondo = not fondo
        contenuto[dove:dove] = nuove
        totale += len(nuove)
        ripassa(tid, nome_file, j, th, media)
        open(f, 'w', encoding='utf-8', newline='\n').write(json.dumps(j, ensure_ascii=False, separators=(',', ':')))
    return totale


def main():
    temi = sys.argv[1:] or sorted(SPEC2)
    tot = 0
    for tid in temi:
        n = applica(tid, SPEC2[tid])
        tot += n
        print('%-20s %d sezioni nuove' % (tid, n))
    print('totale', tot)


if __name__ == '__main__':
    main()
