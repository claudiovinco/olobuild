# -*- coding: utf-8 -*-
"""Pezzi comuni dei cinque temi delle agenzie di comunicazione (20 nov 2026): Affisso, Diaframma, Pollice, Strappo,
Torchio. Niente titoli di sezione a due righe colorate (il Section Header c'è in tutti i 211 temi): qui i titoli sono
occhiello + titolo + testo, impaginati ogni volta in modo diverso; il resto sono tile poco usate.
"""
from base import mix, radius, solid  # noqa: F401


def testa(K, t, occhiello, titolo, testo=None, size=64, align='left', colore=None, colore_occhiello=None, colore_testo=None,
          tag='h2', sizes=None, pad=18, accento=None, maiuscolo=False, testo_size=17):
    """Occhiello, titolo e (se c'è) il testo: una lista di tile."""
    out = []
    if occhiello:
        out.append(K.label(t, occhiello, color=colore_occhiello or K.c['acc'], align=align))
    out.append(K.headline(t, titolo, size=size, tag=tag, align=align, pad_bottom=pad, color=colore, sizes=sizes, accento=accento,
                          maiuscolo=maiuscolo))
    if testo:
        out.append(K.text(t, testo, size=testo_size, align=align, color=colore_testo))
    return out


def scorri(tappe, inizio=0, fine=100, costante=False):
    """Il movimento a scorrimento di un livello del Pro Slider: tappe = [(p, {proprietà})]."""
    o = {'inizio': inizio, 'fine': fine, 'tappe': [dict(p=p, **v) for p, v in tappe]}
    if costante:
        o['andamento'] = 'costante'  # la chiave che leggono editor e PHP (c: true non la leggeva nessuno)
    return o


def appare(da, a, via_da=None, via_a=None, dy=16):
    """Un testo che entra (da → a) e, se serve, esce (via_da → via_a), salendo di dy px."""
    tp = [(0, {'o': 0, 'y': dy}), (da, {'o': 0, 'y': dy}), (a, {'o': 100, 'y': 0})]
    if via_da is not None:
        tp += [(via_da, {'o': 100, 'y': 0}), (via_a, {'o': 0, 'y': -dy}), (100, {'o': 0, 'y': -dy})]
    else:
        tp += [(100, {'o': 100, 'y': 0})]
    if da <= 0:
        tp = [(0, {'o': 100, 'y': 0})] + tp[3:]
    return scorri(tp)


def scheda(colore, pad=(40, 44, 38, 44), r=0):
    return {'bg': solid(colore), 'padding_top': pad[0], 'padding_right': pad[1], 'padding_bottom': pad[2], 'padding_left': pad[3],
            'border_radius': radius(r) if r else 0}
