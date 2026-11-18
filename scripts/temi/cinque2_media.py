# -*- coding: utf-8 -*-
"""I media dei cinque temi «pazzi» della sera del 18 nov 2026 (utente: «hai fatto un ottimo lavoro con i siti pazzi, cerca
di farne altri 5»): Dorso (legatoria, il sito che si sfoglia), Lanterna (escape room, il sito al buio con la torcia),
Zapping (cineclub, il sito che cambia canale), Torre (B&B in una torre, il sito-ascensore), Scontrino (salumeria, il sito
che esce dalla stampante). Foto e video di Pexels, preparati come gli altri temi da mestieri_media.py (foto col lato lungo
di 1920 px, video di 14 secondi senza audio con la copertina).

Uso: python cinque2_media.py <cartella dei media> <ricerche.json>
"""
import json
import os
import sys
from concurrent.futures import ThreadPoolExecutor

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
import mestieri_media as MM  # noqa: E402

FOTO = {
    'dorso': [30560214, 16476327, 38963532, 35098073, 6620988, 6620959, 34870740, 37384667, 31422601, 6621007, 13923801,
              36472452, 8391471, 7657377, 37100698, 38075658, 8327822, 8327809, 36134863, 35985299, 35985306, 30243820,
              803929, 33729878, 38495381, 3826645, 6149064, 190295, 13061431, 36093623],
    'lanterna': [32846085, 35109032, 23961764, 30226733, 5951636, 5427442, 5427434, 6613803, 16827795, 36575138, 33388008,
                 37602341, 31943789, 4402272, 33039807, 30247330, 9554231, 5795580, 37771422, 12493594, 11971015, 36268895,
                 4005032, 37576109, 8910376, 37362875, 10583683, 14436272, 10728163, 28143581, 29452615, 30335237, 32822220],
    'zapping': [38149593, 333984, 33805706, 16707770, 9681642, 6977377, 40072321, 15573679, 7991266, 7991579, 7991130,
                8263369, 7513412, 19374140, 34084878, 5826828, 10599804, 12341046, 157541, 32559667, 27307265, 8024438,
                8652476, 39863137, 33040847, 28708294, 6762236, 390089, 5202959, 14770618, 8875613, 9140600, 25857614,
                31593645],
    'torre': [13000472, 36483990, 14100906, 28572715, 16829822, 14939069, 16829835, 38487588, 13255066, 33040051, 38127108,
              24877216, 3770158, 24877214, 31296628, 15699339, 38183822, 7163651, 15692775, 28054852, 11888487, 28962508,
              12604872, 14098680, 36782825, 34307858, 3771067, 23319072, 16026075, 36714675, 39656, 27348400, 19064311,
              15111268, 14515698, 4773222, 9476486, 13809245, 30538865, 13013514, 2134224, 31387487],
    'scontrino': [11686671, 35474462, 35023463, 30811288, 19585058, 14278936, 17302464, 37679129, 35823638, 36595947,
                  36781148, 24252376, 3593425, 33575864, 13079969, 39213896, 4039607, 8601595, 39777115, 9755391, 4959907,
                  29575250, 29575239, 29575236, 6287316, 9807689, 6223218, 34674788, 8475169, 8469437, 7296399, 34037769,
                  18823977, 3693280, 6287519],
}
# id: secondo da cui partire
VIDEO = {
    'dorso': {9490310: 0, 40999847: 0, 11769112: 0, 34740247: 0, 7417543: 0, 6219901: 4},
    'lanterna': {6256795: 2, 19405094: 0, 6814635: 6, 6227435: 2, 6980925: 4, 4768550: 0},
    'zapping': {6976087: 2, 15069622: 0, 7986770: 2, 856593: 2, 10599677: 6, 10177084: 8, 7988642: 0},
    'torre': {26921525: 2, 26921522: 0, 12999690: 0, 36746030: 4, 10156960: 4, 7507815: 10},
    'scontrino': {37326360: 4, 855237: 2, 7424471: 0, 6658176: 2, 34631457: 6, 5240825: 0},
}


def main():
    out, ris = sys.argv[1], json.load(open(sys.argv[2], encoding='utf-8'))
    os.makedirs(out, exist_ok=True)
    lavori, mancano = [], []
    for tema, ids in FOTO.items():
        voci = {v['id']: v for v in ris[tema]['foto']}
        for i in ids:
            (lavori.append(('f', voci[i], None)) if i in voci else mancano.append((tema, 'foto', i)))
    for tema, ids in VIDEO.items():
        voci = {v['id']: v for v in ris[tema]['video']}
        for i, da in ids.items():
            (lavori.append(('v', voci[i], da)) if i in voci else mancano.append((tema, 'video', i)))
    if mancano:
        print('NON nelle ricerche:', mancano)
    errori = []

    def uno(l):
        try:
            MM.foto(out, l[1]) if l[0] == 'f' else MM.video(out, l[1], l[2])
        except Exception as e:  # noqa: BLE001
            errori.append((l[1]['id'], str(e)[:120]))
    with ThreadPoolExecutor(6) as ex:
        list(ex.map(uno, lavori))
    print('media pronti:', len(lavori) - len(errori), 'su', len(lavori), 'errori:', errori)


if __name__ == '__main__':
    main()
