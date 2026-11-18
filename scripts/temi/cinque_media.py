# -*- coding: utf-8 -*-
"""I media dei cinque temi «pazzi» del 18 nov 2026 (utente: «cerca di creare altri 5 temi pazzi, che sfuggono alla logica
classica dei siti web»): Persiana (immobiliare, il video che si perde sullo sfondo), Tornanti (noleggio e-bike, il sito a
tornanti), Ruota Libera (ciclofficina, il sito che gira come una ruota), Vetrino (laboratorio analisi, lo zoom che non
finisce), Doppio Senso (traduttori, le due colonne in senso contrario). Foto e video di Pexels, preparati come gli altri
temi da mestieri_media.py (foto col lato lungo di 1920 px, video di 14 secondi senza audio con la copertina).

Uso: python cinque_media.py <cartella dei media> <ricerche.json>
"""
import json
import os
import sys
from concurrent.futures import ThreadPoolExecutor

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
import mestieri_media as MM  # noqa: E402

FOTO = {
    'persiana': [6062808, 19466870, 14059296, 30475946, 17373417, 3284980, 6077368, 9826456, 5998120, 7166645, 9757618,
                 6903157, 28272314, 27313203, 14965658, 19981282, 29094497, 7599735, 7045921, 4703, 4554377, 7203720],
    'tornanti': [28607405, 39504408, 29065651, 39504409, 35755654, 31204618, 39853415, 11163071, 19411352, 8724665,
                 36440179, 38745614, 25961180, 2870243, 19138765, 33865627, 27786306, 138903, 4406319, 12781856, 5807940,
                 31510086],
    'ruota-libera': [34185192, 35305055, 31452612, 28100861, 16884264, 31714310, 5803351, 11890954, 13122870, 36398681,
                     5911706, 8446101, 6568953, 6647025, 4530854, 4530876, 32403277, 8545563, 13246520, 8504320, 24917427,
                     19037122, 10016886, 210881, 1061137],
    'vetrino': [37494545, 7479630, 8932609, 36372301, 29859866, 36372296, 25626509, 13014236, 35967917, 2440264, 8442568,
                15290006, 4040557, 4047146, 9574483, 8442509, 8851362, 3735786, 35645506, 6285376, 4031435, 8940509, 7722849],
    'doppio-senso': [226612, 40025134, 8369211, 13745412, 16009564, 135129, 2422280, 8518732, 7964543, 34423686, 35522712,
                     5185091, 8866731, 19130627, 7681947, 5993641, 4065407, 1921957, 15873606, 2833379, 36681344, 12717154,
                     18592481, 2476595, 12796422, 256455, 1061130, 17548877],
}
# id: secondo da cui partire
VIDEO = {
    'persiana': {39272229: 0, 7118332: 2, 29808195: 2, 10238036: 0, 7614417: 0, 5823595: 0},
    'tornanti': {13526128: 0, 28650445: 2, 29887183: 0, 6420515: 0, 39927521: 4, 32730916: 0},
    'ruota-libera': {5228664: 0, 33325059: 6, 11798539: 0, 40301261: 0, 37655314: 4, 3069096: 0},
    'vetrino': {4121129: 2, 29851224: 0, 35637540: 0, 11218535: 6, 8852382: 0, 5944685: 0, 7298045: 0},
    'doppio-senso': {7581246: 0, 6951461: 0, 853946: 0, 7685212: 4, 40180706: 2, 5210762: 2},
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
