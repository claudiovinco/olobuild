# -*- coding: utf-8 -*-
"""I manifesti di Strappo, seconda versione (21 nov 2026, utente: «il carattere di questo tema sembra allungato in
verticale»).

Perché: nell'apertura la tela del Pro Slider al computer e al tablet è alta quanto lo schermo, non 16:9, e i manifesti
(immagini 1440 × 810) erano stesi con object-fit «fill»: a 1440 × 900 il carattere si allungava dell'11 %, a 1024 × 768
del 33 %, sul tablet in verticale più del doppio. Al telefono la tela ha le proporzioni del disegno (640 × 1400) e lì
non cambia niente.

Come: ogni pezzo (manifesto intero e le due metà) ora è grande quanto la tela, le metà trasparenti fuori dalla loro parte,
e nel tema stanno tutti a x 0, larghi 100, con object-fit «cover»: si ritagliano allo stesso modo e lo strappo resta
allineato a ogni proporzione. Le scritte stanno in una zona sicura che resta in vista:
- computer (1440 × 810): x 15–85 %, y 12,5–87,5 % → schermi da 5:4 a 21:9;
- tablet (1100 × 1100, il formato fino a 1024 px, in verticale e in orizzontale): x 17–83 %, y 27–73 %.
Nomi nuovi (strappo2-*), i vecchi restano nella libreria.

Uso: python strappo_manifesti_2.py <cartella d'uscita>   (scarica da sola la foto del secondo manifesto)
"""
import asyncio
import os
import sys
import urllib.request

OUT = os.path.abspath(sys.argv[1] if len(sys.argv) > 1 else '_strappo2')
os.makedirs(OUT, exist_ok=True)
FOTO = 'pexels-19780748.jpg'
if not os.path.exists(os.path.join(OUT, FOTO)):
    urllib.request.urlretrieve('https://olotheme.com/olobuild-library/media/' + FOTO, os.path.join(OUT, FOTO))
sys.argv = [sys.argv[0], OUT]          # agenzie_disegni legge la cartella dei media da qui
sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
import agenzie_disegni as D  # noqa: E402
from PIL import Image  # noqa: E402
from playwright.async_api import async_playwright  # noqa: E402


def manifesti(formato):
    if formato == 'pc':         # 1440 × 810, zona sicura x 216–1224, y 101–709
        return [
            ('background:#EFEBE3;color:#141414',
             '<div class="sm" style="position:absolute;left:228px;top:150px;font-size:18px;color:#C8145F">Art direction · Milano · dal 2011</div>'
             '<div class="big" style="position:absolute;left:214px;top:196px;font-size:290px">Strappo</div>'
             '<div style="position:absolute;left:228px;top:604px;width:470px;height:2px;background:#141414"></div>'
             '<div class="sm" style="position:absolute;left:228px;top:628px;font-size:16px">Campagne · identità · fotografia · set</div>'
             '<div class="big" style="position:absolute;right:226px;top:572px;font-size:112px;color:#C8145F">N.° 1</div>'),
            ('background:#C8145F;color:#fff',
             '<img src="FOTO2" style="position:absolute;right:218px;top:136px;width:350px;height:470px;object-fit:cover;transform:rotate(3deg)">'
             '<div class="sm" style="position:absolute;left:228px;top:150px;font-size:18px">Le regole</div>'
             '<div class="big" style="position:absolute;left:222px;top:204px;font-size:160px">Prima<br>si imparano,</div>'
             '<div class="sm" style="position:absolute;left:228px;top:640px;font-size:16px">N.° 2 · Via Tortona 31</div>'),
            ('background:#141414;color:#EFEBE3',
             '<div class="big" style="position:absolute;left:222px;top:200px;font-size:226px">poi si<br>strappano<span style="color:#F0287A">.</span></div>'
             '<div class="sm" style="position:absolute;right:228px;top:150px;font-size:18px;color:#F7C948">N.° 3</div>'),
        ]
    return [                    # 1100 × 1100, zona sicura x 187–913, y 297–803
        ('background:#EFEBE3;color:#141414',
         '<div class="sm" style="position:absolute;left:200px;top:312px;font-size:17px;color:#C8145F">Art direction · Milano · dal 2011</div>'
         '<div class="big" style="position:absolute;left:188px;top:350px;font-size:200px">Strappo</div>'
         '<div style="position:absolute;left:200px;top:650px;width:340px;height:2px;background:#141414"></div>'
         '<div class="sm" style="position:absolute;left:200px;top:672px;font-size:15px;line-height:1.6">Campagne · identità<br>fotografia · set</div>'
         '<div class="big" style="position:absolute;right:196px;top:676px;font-size:92px;color:#C8145F">N.° 1</div>'),
        ('background:#C8145F;color:#fff',
         '<div class="sm" style="position:absolute;left:200px;top:312px;font-size:17px">Le regole</div>'
         '<div class="big" style="position:absolute;left:194px;top:352px;font-size:112px">Prima<br>si imparano,</div>'
         '<img src="FOTO2" style="position:absolute;right:200px;top:574px;width:270px;height:210px;object-fit:cover;transform:rotate(3deg)">'
         '<div class="sm" style="position:absolute;left:200px;top:760px;font-size:15px">N.° 2 · Via Tortona 31</div>'),
        ('background:#141414;color:#EFEBE3',
         '<div class="sm" style="position:absolute;right:200px;top:312px;font-size:17px;color:#F7C948">N.° 3</div>'
         '<div class="big" style="position:absolute;left:194px;top:360px;font-size:160px">poi si<br>strappano<span style="color:#F0287A">.</span></div>'),
    ]


async def main():
    tmp = os.path.join(OUT, '_tmp.png')
    collage = D.foto64(FOTO)
    async with async_playwright() as p:
        b = await p.chromium.launch()
        pg = await b.new_page(device_scale_factor=1)
        for formato, (w, h), suf in (('pc', (1440, 810), ''), ('tablet', (1100, 1100), '-q')):
            for n, (fondo, corpo) in enumerate(manifesti(formato), 1):
                im = await D.disegna(pg, D.manifesto_html(corpo.replace('FOTO2', collage), fondo, w, h), w, h, tmp)
                im.save(os.path.join(OUT, 'strappo2-%d%s.webp' % (n, suf)), quality=88, method=6)
                # le metà a tutta tela: trasparenti fuori dalla loro parte, così si ritagliano come il manifesto intero
                for (meta, bbox), lato in zip(D.strappa(im, n * 7 + (5 if suf else 0)), 'sd'):
                    piena = Image.new('RGBA', (w, h), (0, 0, 0, 0))
                    piena.paste(meta, bbox[:2])
                    nome = 'strappo2-%d%s-%s.webp' % (n, suf, lato)
                    piena.save(os.path.join(OUT, nome), quality=88, method=6)
                    print(nome, (w, h), os.path.getsize(os.path.join(OUT, nome)) // 1024, 'kB')
        await b.close()
    os.remove(tmp)


if __name__ == '__main__':
    asyncio.run(main())
