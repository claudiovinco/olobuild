# -*- coding: utf-8 -*-
"""I media dei dieci temi dei mestieri (10 nov 2026): foto e video di Pexels (licenza libera), scaricati e preparati come
quelli degli altri temi — foto pexels-<id>.jpg col lato lungo di 1920 px, video pexels-video-<id>.mp4 di 14 secondi al
massimo, 1920 px, senza audio, con la copertina pexels-video-<id>.jpg.

Uso: python mestieri_media.py <cartella> [risultati.json]
     (risultati.json = le ricerche fatte dal sottosito, con gli indirizzi dei file; rifà solo quello che manca)
Poi i file vanno nella libreria remota (olotheme.com/olobuild-library/media), dove i temi li leggono.
"""
import json
import os
import subprocess
import sys
import urllib.request
from concurrent.futures import ThreadPoolExecutor

from PIL import Image

FOTO = {
    'morsetto': [9679179, 32497160, 20500461, 17843269, 8961701, 8486929, 28950842, 257736, 5767595, 296322, 4074955,
                 543351, 11236658, 20713462, 29091470, 9875418, 6961122, 4254164, 17828637, 17536106, 5691588, 38171148, 8488029],
    'sifone': [7600830, 34938441, 6196229, 6419128, 29226620, 39885416, 28169591, 3721272, 28178448, 2918, 6123308, 5506046,
               7937300, 7859953, 35868666, 7005268, 10827349, 3616761, 861414, 12644994, 20046689, 36344878, 32588548],
    'due-mani': [7218683, 36153946, 16734519, 5493662, 31544574, 5583126, 1669754, 994164, 10533141, 5798984, 5853925,
                 15013713, 1887946, 5137779, 40088359, 39836710, 8239800, 7166648, 5799059, 28158034, 7031599, 6474341],
    'filo-a-piombo': [30640160, 10383588, 38561974, 19688828, 8488008, 8482823, 4981810, 14982351, 17391841, 1101125,
                      6205564, 38561968, 5691612, 27531965, 19145511, 1631226, 37362298, 17325347, 13003058, 6251067,
                      5853931, 8586035],
    'truciolo': [8447888, 8447892, 8961341, 5466229, 6790936, 32357250, 37663438, 5691541, 5711903, 5974251, 6791544,
                 4403500, 6790748, 6394655, 7109998, 31755424, 11105148, 139306, 131664, 12278569, 6790066],
    'incudine': [334032, 38156404, 40815798, 33739127, 14966843, 37226044, 37579453, 36531902, 5845892, 3997247, 37278119,
                 10025348, 36164065, 618151, 5845951, 6804260, 32185454, 36780441, 8125961, 7540035, 15507814, 38951991,
                 38092597],
    'cric': [7807035, 7006697, 6870324, 8985967, 4489761, 4116170, 8985517, 8986138, 27256501, 17552590, 29198149,
             34277926, 30470930, 3642618, 11455717, 65623, 7006675],
    'fuliggine': [12341265, 34020199, 36009570, 39501972, 38097885, 17191633, 22711618, 13075346, 11569721, 36027487,
                  5551173, 35160053, 9877234, 6375887, 6747098, 14545484, 39626992, 36803100, 5213535, 4148362, 8827814,
                  7946836, 15122602],
    'rastrello': [6231775, 24595772, 7509492, 27176779, 4975395, 38936344, 38673906, 4162011, 37554739, 6728925, 39625998,
                  36713682, 38936334, 9204, 32427440, 24446088, 6711591, 31585417, 34017020, 38115055, 36580480],
    'scheggia': [7219182, 7219180, 7219184, 7219183, 7219181, 7219172, 7219189, 5691516, 5691531, 5691507, 5691504,
                 5768107, 5691503, 7005476, 7018243, 27359993, 11861957, 37320179, 9966703, 15205623, 15156848, 6768434],
}
# video: id → secondo da cui partire
VIDEO = {
    'morsetto': {853788: 0, 6079427: 0, 10664446: 0, 5391403: 0, 853772: 0, 6079417: 1},
    'sifone': {5698926: 0, 11593539: 0, 9890450: 0, 7584762: 2, 7647713: 0, 5571839: 0, 32711364: 0},
    'due-mani': {6473946: 2, 9823147: 0, 5799490: 0, 6474155: 0, 6474260: 0, 6474085: 0},
    'filo-a-piombo': {30037263: 6, 34572321: 0, 37500519: 0, 15990535: 0, 5092252: 0, 34572319: 0},
    'truciolo': {35170034: 3, 20660631: 4, 5972632: 0, 5895456: 0, 5973239: 0, 4480557: 0},
    'incudine': {4186969: 0, 5846389: 0, 5846379: 0, 5846589: 0, 5846383: 2, 31638148: 0, 38103748: 0},
    'cric': {15270404: 2, 8987272: 4, 6870344: 0, 6870347: 0, 14514790: 0, 9174928: 0, 18101989: 0},
    'fuliggine': {13081949: 0, 35504492: 0, 35636667: 0, 40576579: 0, 10482174: 0, 10799308: 0},
    'rastrello': {37618414: 0, 29393757: 0, 37733806: 0, 3078390: 0, 4153438: 2, 20521968: 0, 4207865: 0},
    'scheggia': {7219271: 2, 7219278: 4, 7219266: 2, 6175387: 0, 6175379: 0, 32078278: 0, 39623153: 0},
}
LATO = 1920


def scarica(url, dest):
    if os.path.exists(dest):
        return
    req = urllib.request.Request(url, headers={'User-Agent': 'Mozilla/5.0'})
    with urllib.request.urlopen(req, timeout=300) as r, open(dest + '.part', 'wb') as f:
        f.write(r.read())
    os.replace(dest + '.part', dest)


def foto(out, voce):
    dest = os.path.join(out, 'pexels-%d.jpg' % voce['id'])
    if os.path.exists(dest):
        return
    grezza = dest + '.orig'
    scarica(voce['src'] + '?auto=compress&cs=tinysrgb&w=2400', grezza)
    im = Image.open(grezza).convert('RGB')
    im.thumbnail((LATO, LATO), Image.LANCZOS)
    im.save(dest, 'JPEG', quality=82, progressive=True, optimize=True)
    os.remove(grezza)


def video(out, voce, da):
    mp4 = os.path.join(out, 'pexels-video-%d.mp4' % voce['id'])
    jpg = mp4[:-4] + '.jpg'
    if not os.path.exists(mp4):
        # il file più vicino a 1920 di larghezza (non sotto i 1280)
        f = sorted(voce['f'], key=lambda x: (abs(x[0] - 1920), -x[0]))[0]
        grezzo = mp4 + '.grezzo.mp4'
        scarica(f[2], grezzo)
        subprocess.run(['ffmpeg', '-v', 'error', '-y', '-ss', str(da), '-i', grezzo, '-t', '14', '-an',
                        '-vf', "scale='min(1920,iw)':-2", '-c:v', 'libx264', '-preset', 'slow', '-crf', '26',
                        '-pix_fmt', 'yuv420p', '-movflags', '+faststart', mp4], check=True)
        os.remove(grezzo)
    if not os.path.exists(jpg):
        subprocess.run(['ffmpeg', '-v', 'error', '-y', '-ss', '0.5', '-i', mp4, '-frames:v', '1',
                        '-pix_fmt', 'yuvj420p', '-strict', 'unofficial', '-q:v', '3', jpg], check=True)


def main():
    out = sys.argv[1]
    ris = json.load(open(sys.argv[2] if len(sys.argv) > 2 else os.path.join(out, '..', 'risultati.json'), encoding='utf-8'))
    os.makedirs(out, exist_ok=True)
    lavori = []
    for tema, ids in FOTO.items():
        voci = {v['id']: v for v in ris[tema]['foto']}
        for i in ids:
            lavori.append(('f', voci[i], None))
    for tema, ids in VIDEO.items():
        voci = {v['id']: v for v in ris[tema]['video']}
        for i, da in ids.items():
            lavori.append(('v', voci[i], da))
    fatti = 0
    with ThreadPoolExecutor(6) as ex:
        for _ in ex.map(lambda l: foto(out, l[1]) if l[0] == 'f' else video(out, l[1], l[2]), lavori):
            fatti += 1
    print('media pronti:', fatti)


if __name__ == '__main__':
    main()
