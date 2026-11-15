# -*- coding: utf-8 -*-
"""Home meno uguali (13 nov 2026). L'utente: «troppo comodo rifare tutto il sito uguale, dovresti sforzarti per dare linee
grafiche diverse tra loro, strutture creative». 28 temi del 31 ottobre e del 1° novembre avevano la stessa home, nello
stesso ordine: apertura · introduzione · elenco con la foto che si affaccia (su fondo pieno) · fascia video · foto con
testo · testimonianza o elenco · fascia video.

Per ogni tema del gruppo:
- l'ORDINE delle sezioni (quattro, scelte perché due fondi pieni o due fasce video non stiano mai vicini);
- l'ELENCO principale (la Hover list): resta, o diventa un mosaico di foto col titolo sopra (Overlay grid), una galleria
  a nastro con le didascalie (Pro gallery), carte che si girano (Flip card), righe alternate foto e testo.
Le scelte stanno in home_scelte.json; base.write_theme le riapplica quando si rigenera un tema.
uso: python varia_home.py [--scrivi] [tema …]"""
import json
import os
import re
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
sys.stdout.reconfigure(encoding='utf-8')
from base import Tpl  # noqa: E402
import pezzi_nov as P  # noqa: E402
from arricchisci import kit_del_tema, media_del_tema, TEMI  # noqa: E402

QUI = os.path.dirname(os.path.abspath(__file__))
SCELTE = os.path.join(QUI, 'home_scelte.json')
IGN = {'section', 'row', 'column', 'inner-columns', 'inner-column', 'headline', 'text-block', 'button', 'image', 'section-header',
       'spacer', 'divider', 'text', 'shape', 'shapedivider', 'none', 'icon'}
ORDINI = [[0, 1, 2, 3, 4, 5, 6], [0, 1, 3, 2, 4, 5, 6], [0, 2, 1, 3, 5, 4, 6], [0, 1, 4, 2, 3, 5, 6]]
ELENCHI = ['mosaico', 'galleria', 'carte', 'file', 'righe']
FASCE = ['lato', 'centro', 'destra']


def firma(sez):
    tipi = []

    def w(n):
        if isinstance(n, dict):
            ty = n.get('type')
            if ty and ty not in IGN and ty not in tipi:
                tipi.append(ty)
            for c in n.get('children') or []:
                w(c)
    w(sez)
    return '+'.join(sorted(tipi)) or 'testo'


def fondo(sez):
    b = (sez.get('settings') or {}).get('bg')
    return (b.get('type') or '-') if isinstance(b, dict) else '-'


# il ritmo che si ripeteva: apertura · semplice · fondo pieno · video · semplice · fondo pieno · video
RITMO = ['*', '-', 'solid', 'video', '-', 'solid', 'video']


def nel_gruppo(contenuto):
    f = [fondo(s) for s in contenuto]
    return len(f) == 7 and all(a == '*' or a == b for a, b in zip(RITMO, f))


def file_media(url):
    m = re.search(r'/media/([^"?#]+)$', url or '')
    return m.group(1) if m else ''


def voci_hover(hl):
    return [{'nome': it.get('name', ''), 'sotto': it.get('sub', ''), 'testo': re.sub(r'</?p>', '', it.get('desc', '') or ''),
             'foto': file_media(it.get('image')), 'link': it.get('link_url') or ''} for it in hl['settings'].get('items') or []]


def mosaico(K, t, vv):
    C = K.c
    n = len(vv)
    return [t.one([t.classic('overlaygrid', {
        'items': [{'image': K.img(v['foto']), 'title': v['nome'], 'subtitle': v['sotto'] or v['testo'][:90], 'link': v['link'],
                   'ribbon': ''} for v in vv],
        'columns': str(n if n <= 4 else 3), 'columns_tablet': '2', 'columns_mobile': '1', 'gap': 'small', 'height': '460',
        'image_ratio': '3/4', 'image_fit': 'cover', 'object_position': 'center center', 'match_height': True,
        'layout_mode': 'uniform', 'overlay_position': 'bottom', 'overlay_horizontal': 'left', 'overlay_style': 'overlay-primary',
        'overlay_padding': 'medium', 'title_size': 'h3', 'hover_effect': 'zoom', 'hover_overlay': 'always', 'shadow': 'none',
        'preset': 'custom', 'item_radius': K.r, 'overlay_color': 'color-mix(in srgb, var(--olo-color-dark) 62%, transparent)',
        'overlay_gradient': True, 'title_color': C['chiaro'], 'title_weight': K.peso, 'subtitle_color': C['chiaro'],
        'subtitle_size': 14, 'show_cta': False}, style={})])]


def galleria(K, t, vv):
    return [t.one([t.classic('progallery', {
        'images': [{'url': K.img(v['foto']), 'alt': v['nome'], 'caption': v['nome'], 'subtitle': v['sotto'], 'text': ''} for v in vv],
        'layout': 'strip_coverflow', 'layout_family': 'strip', 'columns': 4, 'gap': 14, 'img_height': '460px',
        'aspect_ratio': 'auto', 'object_fit': 'cover', 'object_position': 'center center', 'thumb_radius': K.r,
        'mobile_columns': 1, 'filmstrip_item_width': 340, 'filmstrip_center_zoom': 1.1, 'filmstrip_side_tilt': 30,
        'filmstrip_autoplay': True, 'filmstrip_speed': 4, 'filmstrip_dots': 'lines', 'filmstrip_dots_color': K.c['acc'],
        'hover_caption': 'always', 'hover_caption_bg': 'color-mix(in srgb, var(--olo-color-dark) 70%, transparent)',
        'hover_caption_color': K.c['chiaro'], 'show_caption': True, 'lightbox': True, 'preset': 'custom'}, style={})])]


def carte(K, t, vv):
    righe = []
    n = len(vv)
    col = 4 if n in (4, 8) else 3
    for i in range(0, n, col):
        g = vv[i:i + col]
        c2 = col if len(g) == col else (len(g) if len(g) in (3, 4) else col)
        r = P.carte(K, t, [(v['foto'], v['nome'], v['sotto'], '<p>%s</p>' % v['testo']) for v in g], altezza='420', colonne=c2)
        if i:
            r['style'] = dict(r.get('style') or {}, margin_top=18)
        righe.append(r)
    return righe


def file_alt(K, t, vv):
    C = K.c
    righe = []
    for k, v in enumerate(vv):
        testo = [K.label(t, v['sotto'], color=C['acc']), K.headline(t, v['nome'], size=40, tag='h3', pad_bottom=12),
                 K.text(t, '<p>%s</p>' % v['testo'], size=17)]
        if v['link']:
            testo.append(K.button(t, 'Scopri', v['link'], pad_top=14))
        cols = [t.col([K.image(t, v['foto'], v['nome'], ratio='4/3')], wm='1-2', ws='1-1'), t.col(testo, wm='1-2', ws='1-1')]
        if k % 2:
            cols.reverse()
        righe.append(t.row(cols, layout='50-50', gap=56, valign='center', style={'margin_top': 0 if k == 0 else 56}))
    return righe


def fascia_di_lato(sez, lato):
    """La fascia video con la frase: invece che centrata in basso, a sinistra o a destra, con la sfumatura che entra da
    quel lato e il testo allineato."""
    bg = (sez.get('settings') or {}).get('bg')
    if not isinstance(bg, dict):
        return
    bg['overlay_fade'], bg['overlay_fade_reach'] = ('left' if lato == 'lato' else 'right'), 72
    st = sez.setdefault('style', {})
    st['padding_top'], st['padding_bottom'] = 220, 220
    for r in sez.get('children') or []:
        cols = r.get('children') or []
        if r.get('type') == 'row' and len(cols) == 3 and not cols[0].get('children') and not cols[2].get('children'):
            pieno, vuoto = cols[1], cols[0]
            pieno.setdefault('settings', {})['width_medium'] = '1-2'
            vuoto.setdefault('settings', {})['width_medium'] = '1-2'
            r['children'] = [pieno, vuoto] if lato == 'lato' else [vuoto, pieno]
            r['settings']['custom_widths'] = '50,50'
            for tile in pieno.get('children') or []:
                ts = tile.get('settings') or {}
                if 'alignment' in ts:
                    ts['alignment'] = 'left'
                if 'text_align' in ts:
                    ts['text_align'] = 'left'
            return


COSTRUISCI = {'mosaico': mosaico, 'galleria': galleria, 'carte': carte, 'file': file_alt}


def cambia_elenco(K, t, sez, elenco):
    """Nella sezione dell'elenco: la riga che ha la Hover list (da sola) diventa le righe nuove; se la Hover list sta in una
    colonna con altro, solo mosaico o galleria (sono una tile sola)."""
    if elenco == 'righe':
        return

    def riga_con(n):
        ch = n.get('children') or []
        return (n.get('type') == 'row' and len(ch) == 1 and len(ch[0].get('children') or []) == 1
                and ch[0]['children'][0].get('type') == 'hoverlist')

    figli = sez.get('children') or []
    for i, r in enumerate(figli):
        if riga_con(r):
            vv = voci_hover(r['children'][0]['children'][0])
            if not all(v['foto'] for v in vv):
                return
            figli[i:i + 1] = COSTRUISCI[elenco](K, t, vv)
            return
    # la Hover list dentro una colonna con altre tile: si sostituisce la tile
    def w(n):
        if isinstance(n, dict):
            ch = n.get('children') or []
            for j, c in enumerate(ch):
                if c.get('type') == 'hoverlist':
                    vv = voci_hover(c)
                    if all(v['foto'] for v in vv):
                        nuovo = (mosaico if elenco in ('mosaico', 'carte') else galleria)(K, t, vv)[0]
                        ch[j] = nuovo['children'][0]['children'][0]
                    return True
                if w(c):
                    return True
        return False
    w(sez)


def applica_home(K, tid, contenuto, scelta):
    ordine, elenco = scelta[0], scelta[1]
    fascia = scelta[2] if len(scelta) > 2 else 'centro'
    t = Tpl(tid, 'home-vh')
    for i in (2, 4):
        if '"type": "hoverlist"' in json.dumps(contenuto[i]):
            cambia_elenco(K, t, contenuto[i], elenco)
            break
    if fascia != 'centro':
        fascia_di_lato(contenuto[3], fascia)
    contenuto[:] = [contenuto[i] for i in ORDINI[ordine]]


def applica_tema(theme, pagina, built, media):
    """Per base.write_theme."""
    if pagina != 'home':
        return
    scelte = json.load(open(SCELTE, encoding='utf-8')) if os.path.exists(SCELTE) else {}
    sc = scelte.get(theme['id'])
    if not sc:
        return
    c = built['content'] if isinstance(built, dict) and 'content' in built else built
    if nel_gruppo(c):
        applica_home(kit_del_tema(theme['id'], theme, media), theme['id'], c, sc)


def main():
    scrivi = '--scrivi' in sys.argv
    scelti = [a for a in sys.argv[1:] if not a.startswith('--')]
    date = json.load(open(os.path.join(TEMI, '..', 'temi-creati.json'), encoding='utf-8'))
    gruppo = []
    for tid in sorted(os.listdir(TEMI), key=lambda x: (date.get(x, ''), x)):
        th = json.load(open(os.path.join(TEMI, tid, 'theme.json'), encoding='utf-8'))
        h = th['pages'].get('home') or {}
        p = os.path.join(TEMI, tid, th['templates'][h.get('template', 'home')]['file'])
        j = json.load(open(p, encoding='utf-8'))
        c = j['content'] if isinstance(j, dict) and 'content' in j else j
        if nel_gruppo(c):
            gruppo.append((tid, th, p, j, c))
    scelte = json.load(open(SCELTE, encoding='utf-8')) if os.path.exists(SCELTE) else {}
    prima = None
    for k, (tid, th, p, j, c) in enumerate(gruppo):
        sc = scelte.get(tid)
        if not sc:
            # combinazioni diverse a turno: ordine, elenco e fascia cambiano rispetto al tema di prima
            sc = [k % len(ORDINI), ELENCHI[(k * 2 + k // len(ELENCHI)) % len(ELENCHI)], FASCE[k % len(FASCE)]]
            if prima and (sc[0] == prima[0] or sc[1] == prima[1]):
                sc = [(sc[0] + 1) % len(ORDINI), ELENCHI[(ELENCHI.index(sc[1]) + 1) % len(ELENCHI)], sc[2]]
            scelte[tid] = sc
        prima = sc
        if scelti and tid not in scelti:
            continue
        print('%-18s ordine %d · elenco %-8s · fascia %s' % (tid, sc[0], sc[1], sc[2] if len(sc) > 2 else 'centro'))
        if scrivi:
            applica_home(kit_del_tema(tid, th, media_del_tema(os.path.dirname(p))), tid, c, sc)
            open(p, 'w', encoding='utf-8', newline='\n').write(json.dumps(j, ensure_ascii=False, separators=(',', ':')))
    if scrivi:
        json.dump(scelte, open(SCELTE, 'w', encoding='utf-8'), ensure_ascii=False, indent=0)
    print(len(gruppo), 'home nel gruppo', '(scritte)' if scrivi else '(prova)')


if __name__ == '__main__':
    main()
