# -*- coding: utf-8 -*-
"""I piè di pagina meno uguali (14 nov 2026, dall'analisi di somiglianze.py). 125 temi su 198 avevano tre soli piedi:
102 «tre colonne + il nome gigante in fondo + la barra» (72 col nome scritto, 30 col logo) e 23 «quattro colonne + barra».

Piede col nome gigante → quattro disposizioni, a turno nell'ordine di creazione:
  · fondo    il nome gigante in fondo (com'era)
  · cima     il nome gigante sopra le colonne
  · sinistra il nome, più piccolo, in testa alla prima colonna (40 · 30 · 30), niente riga a sé
  · centro   il nome al centro, sotto il menu in riga, poi indirizzo e orari in due colonne
Piede a quattro colonne → «uguali» (com'era) o «largo» (la colonna del marchio al 40%).
Solo spostamenti, larghezze, allineamenti e grandezza del nome: le tile e i testi restano quelli.
Le scelte in piedi_scelte.json; base.write_theme le riapplica. uso: python piedi.py [--scrivi]"""
import copy
import json
import os
import sys

QUI = os.path.dirname(os.path.abspath(__file__))
sys.path.insert(0, QUI)
TEMI = os.path.normpath(os.path.join(QUI, '..', '..', 'assets', 'data', 'themes'))
SCELTE = os.path.join(QUI, 'piedi_scelte.json')
GIRO_NOME = ['fondo', 'cima', 'sinistra', 'centro']
GIRO_QUATTRO = ['uguali', 'largo']


def tipi(col):
    return [c.get('type') for c in col.get('children') or []]


def forma(sez):
    rr = sez.get('children') or []
    if len(rr) == 3:
        r0, r1, r2 = rr
        c0 = r0.get('children') or []
        if len(c0) == 3 and 'iconlist' in tipi(c0[0]) and tipi(c0[1]) == ['headline', 'nav'] \
                and len(r1.get('children') or []) == 1 and tipi(r1['children'][0]) in (['headline'], ['image']) \
                and len(r2.get('children') or []) == 2:
            return 'nome'
    if len(rr) == 2:
        c0 = rr[0].get('children') or []
        if len(c0) == 4 and all(tipi(c)[:1] == ['headline'] for c in c0) and sum(1 for c in c0 if 'nav' in tipi(c)) >= 1:
            return 'quattro'
    return None


def nome_piccolo(tile):
    s = tile.setdefault('settings', {})
    if tile.get('type') == 'headline':
        s['heading_font_size'] = 64
        s['heading_font_size_tablet'] = '52'
        s['heading_font_size_mobile'] = '44'
        s['heading_letter_spacing'] = -1
    else:
        s['max_width'] = '240'
    tile.setdefault('style', {})['margin_bottom'] = 22


def nome_centrato(tile, grande=True):
    s = tile.setdefault('settings', {})
    if tile.get('type') == 'headline':
        s['alignment'] = 'center'
        if not grande:
            s['heading_font_size'] = min(int(s.get('heading_font_size') or 96), 96)
    else:
        s['image_alignment'] = 'center'
        s['object_position'] = 'center center'
        s['max_width'] = str(min(int(s.get('max_width') or 360), 360))


def applica(sez, variante):
    import strutture as S
    f = forma(sez)
    if f == 'nome':
        r0, r1, r2 = sez['children']
        nome = r1['children'][0]['children'][0]
        if variante == 'cima':
            r1.setdefault('style', {})['margin_top'] = 0
            r1['style']['margin_bottom'] = 56
            sez['children'] = [r1, r0, r2]
        elif variante == 'sinistra':
            nome_piccolo(nome)
            c0 = r0['children'][0]
            c0['children'] = [nome] + c0['children']
            _larghezze_pct(r0, [40, 30, 30])
            sez['children'] = [r0, r2]
        elif variante == 'centro':
            nome_centrato(nome, grande=False)
            r1.setdefault('style', {})['margin_top'] = 0
            c0, c1, c2 = r0['children']
            nav = [x for x in c1['children'] if x.get('type') == 'nav'][0]
            ns = nav.setdefault('settings', {})
            ns['direction'], ns['alignment'] = 'horizontal', 'center'
            rnav = S.riga_da(r1, r1['id'] + '-menu', [S.colonna(r1['id'] + '-menu-c', '1-1', [nav])], ['1-1'], 28)
            r0['children'] = [c0, c2]
            S.larghezze(r0, ['1-2', '1-2'])
            r0.setdefault('style', {})['margin_top'] = 48
            sez['children'] = [r1, rnav, r0, r2]
        return True
    if f == 'quattro' and variante == 'largo':
        _larghezze_pct(sez['children'][0], [40, 20, 20, 20])
        return True
    return f is not None


def _larghezze_pct(riga, pct):
    st = riga.setdefault('settings', {})
    st['layout'] = 'custom'
    st['custom_widths'] = ','.join(str(p) for p in pct)
    mappa = {40: '2-5', 30: '3-10', 20: '1-5', 25: '1-4', 50: '1-2'}
    for c, p in zip(riga['children'], pct):
        c.setdefault('settings', {})['width_medium'] = mappa.get(p, '1-4')


def applica_tema(tid, nome_file, built):
    """Per base.write_theme."""
    if nome_file != 'footer.json':
        return
    v = (json.load(open(SCELTE, encoding='utf-8')) if os.path.exists(SCELTE) else {}).get(tid)
    c = built['content'] if isinstance(built, dict) and 'content' in built else built
    if v and isinstance(c, list) and c:
        applica(c[0], v)


def main():
    sys.stdout.reconfigure(encoding='utf-8')
    scrivi = '--scrivi' in sys.argv
    date = json.load(open(os.path.join(TEMI, '..', 'temi-creati.json'), encoding='utf-8'))
    scelte = json.load(open(SCELTE, encoding='utf-8')) if os.path.exists(SCELTE) else {}
    giri = {'nome': 0, 'quattro': 0}
    conta = {}
    for tid in sorted(os.listdir(TEMI), key=lambda x: (date.get(x, ''), x)):
        p = os.path.join(TEMI, tid, 'footer.json')
        if not os.path.exists(p) or tid in scelte:
            continue
        testo = open(p, encoding='utf-8').read()
        j = json.loads(testo)
        c = j['content'] if isinstance(j, dict) and 'content' in j else j
        f = forma(c[0]) if isinstance(c, list) and c else None
        if not f:
            continue
        giro = GIRO_NOME if f == 'nome' else GIRO_QUATTRO
        v = giro[giri[f] % len(giro)]
        giri[f] += 1
        scelte[tid] = v
        conta[f + ':' + v] = conta.get(f + ':' + v, 0) + 1
        if scrivi and v not in ('fondo', 'uguali'):
            applica(c[0], v)
            compatto = not testo.lstrip().startswith('{\n') and not testo.lstrip().startswith('[\n')
            open(p, 'w', encoding='utf-8', newline='\n').write(
                json.dumps(j, ensure_ascii=False, separators=(',', ':')) if compatto else json.dumps(j, ensure_ascii=False, indent=1))
    for k in sorted(conta):
        print('%-18s %d' % (k, conta[k]))
    if scrivi:
        json.dump(scelte, open(SCELTE, 'w', encoding='utf-8'), ensure_ascii=False, indent=0)
    print('(scritto)' if scrivi else '(prova)')


if __name__ == '__main__':
    main()
