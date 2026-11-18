# -*- coding: utf-8 -*-
"""I media dei tre temi «pazzi» (18 nov 2026): Capriola (acrobatica aerea), Abbaglio (museo delle illusioni), Girandola
(fuochi d'artificio). Foto e video di Pexels, preparati come gli altri temi da mestieri_media.py (foto col lato lungo di
1920 px, video di 14 secondi senza audio con la copertina).

Uso: python tre_media.py <cartella dei media> <ricerche.json>
"""
import json
import os
import sys
from concurrent.futures import ThreadPoolExecutor

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
import mestieri_media as MM  # noqa: E402

FOTO = {
    'capriola': [29715464, 29715462, 29715417, 23638608, 30131258, 15362819, 29515839, 13425675, 5930281, 15362813, 35017410,
                 37070994, 36146672, 1366838, 10899160, 33317816, 4323299, 4999856, 4999499, 37343220, 18353907, 20064293,
                 703008, 3763696, 28679253, 38244823, 19138965],
    'abbaglio': [27404269, 4243357, 36643476, 7523946, 6810936, 39240990, 6208392, 30144056, 2889489, 7724506, 12891172,
                 36040448, 1502800, 2157888, 33797649, 19898100, 69947, 18403797, 36770103, 30435051, 28648089, 16011285,
                 15062110],
    'girandola': [1503520, 27051702, 266497, 6942352, 949592, 27068360, 35497602, 1317364, 37094397, 3256800, 34408491,
                  13086763, 34408521, 34408502, 15822284, 36513643, 6069425, 27517288, 40101766, 19561041, 17545544,
                  40866289, 20519799, 29081017, 13110256, 15549004, 34416999],
}
# id: secondo da cui partire
VIDEO = {
    'capriola': {8396591: 4, 5027046: 0, 10039643: 0, 8396596: 6, 6571883: 3, 5026177: 0},
    'abbaglio': {35666409: 0, 5968482: 0, 31710277: 0, 35667980: 0, 8111746: 3, 34677875: 0, 9614020: 4},
    'girandola': {18686281: 0, 17737412: 10, 38736128: 0, 9665450: 0, 27806811: 2, 28469346: 0, 7685044: 0},
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
