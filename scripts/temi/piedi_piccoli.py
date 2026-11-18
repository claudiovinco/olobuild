# -*- coding: utf-8 -*-
"""Il marchio del piè di pagina piccolo e classico, in cima alla prima colonna.

18 nov 2026, utente: «i loghi dei footer li fai sempre enormi, falli più classici, piccoli», e poi, visti a 1440 px:
«che occupino solo una parte della prima colonna del footer… controlla TUTTI i temi».
- Il marchio che stava in una riga tutta sua (il nome o il logo gigante in fondo, in cima o al centro: le disposizioni di
  piedi.py) passa in testa alla prima colonna della riga delle colonne, e la sua riga sparisce.
- Il logo in immagine è alto come una testata, circa 40 px: la larghezza viene dalla sua proporzione, fra 56 e 200 px,
  CON l'unità («220» senza px era una dichiarazione che il browser scartava: il logo si allargava a 1200 px).
- Il nome scritto va al massimo a 32 px (28 sul tablet, 26 al telefono), con la spaziatura delle lettere di un nome
  piccolo; allineato come il testo della colonna che lo accoglie.
piedi.applica_tema lo richiama a ogni generazione; da solo sistema i piè di pagina già scritti.
uso: python piedi_piccoli.py [id,id…]
"""
import glob
import json
import os
import re
import sys
import unicodedata

QUI = os.path.dirname(os.path.abspath(__file__))
TEMI = os.path.normpath(os.path.join(QUI, '..', '..', 'assets', 'data', 'themes'))
NOME_PX, NOME_TABLET, NOME_TEL = 32, 28, 26
LOGO_H, LOGO_MIN, LOGO_MAX = 40, 56, 200
STACCO = 20  # lo spazio sotto il marchio, prima del testo della colonna


def _piano(t):
    t = unicodedata.normalize('NFKD', re.sub('<[^>]+>', '', str(t or ''))).encode('ascii', 'ignore').decode().lower()
    return re.sub('[^a-z0-9]', '', t)


def _logo(t):
    return t.get('type') == 'image' and 'logo' in str((t.get('settings') or {}).get('image_url', ''))


def _nome(t, nome_tema):
    if t.get('type') != 'headline':
        return False
    s = t.get('settings') or {}
    if not nome_tema:
        return float(s.get('heading_font_size') or 0) >= 26
    a, b = _piano(s.get('heading')), _piano(nome_tema)
    return bool(a) and (a in b or b in a or a[:5] == b[:5])


def larghezza_logo(proporzione):
    try:
        w, h = [float(x) for x in str(proporzione).replace(':', '/').split('/')]
        r = w / h if h else 4
    except ValueError:
        r = 4
    return '%dpx' % int(max(LOGO_MIN, min(LOGO_MAX, round(LOGO_H * r))))


def _allineamento(col):
    for t in col.get('children') or []:
        s = t.get('settings') or {}
        a = s.get('text_align') or s.get('alignment') or ''
        if t.get('type') in ('text-block', 'iconlist', 'nav', 'social') and a:
            return 'center' if a == 'center' else 'right' if a == 'right' else 'left'
    return 'left'


def misura(t, alline=None):
    """Il marchio alla misura classica. Restituisce True se ha cambiato qualcosa."""
    s = t.setdefault('settings', {})
    prima = json.dumps(s, sort_keys=True)
    if t.get('type') == 'image':
        s['max_width'] = larghezza_logo(s.get('aspect_ratio_custom') or '4/1')
        s['image_width'] = '100%'
        if alline:
            s['image_alignment'] = alline
            s['object_position'] = alline + ' center' if alline != 'center' else 'center center'
    else:
        s['heading_font_size'] = min(NOME_PX, int(float(s.get('heading_font_size') or NOME_PX)))
        # le misure per tablet e telefono solo se c'erano già: questo titolo può essere il CAMPIONE da cui base.py copia
        # ogni titolo nuovo (acqua-viva-footer-5), e chiavi aggiunte qui finirebbero su tutti («Le pagine» a 28 px sul tablet)
        for chiave, tetto in (('heading_font_size_tablet', NOME_TABLET), ('heading_font_size_mobile', NOME_TEL)):
            if str(s.get(chiave) or '').strip():
                s[chiave] = str(min(tetto, s['heading_font_size'], int(float(s[chiave]))))
        if float(s.get('heading_letter_spacing') or 0) < -1:
            s['heading_letter_spacing'] = -1
        if alline:
            s['alignment'] = alline
    return json.dumps(s, sort_keys=True) != prima


def _riga_marchio(r, nome_tema):
    cols = r.get('children') or []
    if len(cols) != 1:
        return None
    tt = cols[0].get('children') or []
    if len(tt) == 1 and (_logo(tt[0]) or _nome(tt[0], nome_tema)):
        return tt[0]
    return None


def _nella_sezione(sez, nome_tema):
    righe = sez.get('children') or []
    marchi = [(r, _riga_marchio(r, nome_tema)) for r in righe]
    marchi = [(r, t) for r, t in marchi if t]
    fatti = 0
    if marchi:
        multi = [r for r in righe if len(r.get('children') or []) >= 2 and not any(r is m for m, _ in marchi)]
        if multi:
            # la riga delle colonne: la prima con almeno due colonne (la barra in fondo solo se è l'unica)
            dest = multi[0]
            col = next((c for c in dest['children'] if c.get('children')), dest['children'][0])
            for r, t in reversed(marchi):
                alline = _allineamento(col)
                misura(t, alline)
                st = t.setdefault('style', {})
                st.pop('margin_top', None)
                st['margin_bottom'] = STACCO
                col.setdefault('children', []).insert(0, t)
                sez['children'] = [x for x in sez['children'] if x is not r]
                fatti += 1
    # il marchio già dentro una colonna: solo la misura
    for r in sez.get('children') or []:
        for c in r.get('children') or []:
            for k, t in enumerate(c.get('children') or []):
                if _logo(t) or (k == 0 and _nome(t, nome_tema) and float((t.get('settings') or {}).get('heading_font_size') or 0) > NOME_PX):
                    if misura(t):
                        fatti += 1
    return fatti


def rimpicciolisci(nodi, nome_tema=None):
    """Il marchio del piè di pagina in cima alla prima colonna, alla misura classica. Restituisce quanti ne ha cambiati."""
    return sum(_nella_sezione(sez, nome_tema) for sez in nodi if isinstance(sez, dict))


def nome_di(tid):
    p = os.path.join(TEMI, tid, 'theme.json')
    try:
        return json.load(open(p, encoding='utf-8')).get('name')
    except (OSError, ValueError):
        return None


def main():
    sys.stdout.reconfigure(encoding='utf-8')
    scelti = sys.argv[1].split(',') if len(sys.argv) > 1 else None
    tot = 0
    for f in sorted(glob.glob(os.path.join(TEMI, '*', 'footer.json'))):
        tid = os.path.basename(os.path.dirname(f))
        if scelti and tid not in scelti:
            continue
        raw = open(f, encoding='utf-8').read()
        d = json.loads(raw)
        n = rimpicciolisci(d['content'] if isinstance(d, dict) else d, nome_di(tid))
        if n:
            compatto = not raw.lstrip().startswith('{\n') and not raw.lstrip().startswith('[\n')
            open(f, 'w', encoding='utf-8', newline='\n').write(
                json.dumps(d, ensure_ascii=False, separators=(',', ':')) if compatto else json.dumps(d, ensure_ascii=False, indent=1))
            tot += 1
    print('piè di pagina cambiati:', tot)


if __name__ == '__main__':
    main()
