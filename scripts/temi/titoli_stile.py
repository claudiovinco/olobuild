# -*- coding: utf-8 -*-
"""Lo stile dei titoli di sezione, tema per tema (14 nov 2026, dall'analisi di somiglianze.py). 133 temi su 198 avevano lo
stesso titolo di sezione: occhiello in monospazio maiuscolo, titolo su due righe, la seconda di un altro colore. Era la
firma più riconoscibile: due temi diversi si somigliavano appena si scorreva.

Ogni tema prende una combinazione sua, la stessa su tutte le sue pagine (è la sua voce):
  · occhiello  monospazio · carattere del testo · carattere dei titoli · nessun occhiello (solo profilo essenziale)
  · separatore niente · lineetta · pallino · barra · freccia
  · righe      una sotto l'altra · di seguito
  · seconda riga di un altro colore (com'era) · stesso colore · stesso colore in corsivo
La scelta parte dal profilo (linee_grafiche.json) e dal nome del tema (sempre la stessa, senza file di scelte).
Solo impostazioni del Section header: i testi restano quelli. base.write_theme la riapplica.
uso: python titoli_stile.py [--scrivi] [tema …]"""
import json
import os
import sys
import zlib

QUI = os.path.dirname(os.path.abspath(__file__))
TEMI = os.path.normpath(os.path.join(QUI, '..', '..', 'assets', 'data', 'themes'))
PROFILI = os.path.join(QUI, 'linee_grafiche.json')
FONT = {'editoriale': ['mono', 'body'], 'morbida': ['body', 'heading'], 'geometrica': ['mono', 'heading'],
        'essenziale': ['body', 'mono'], 'rivista': ['heading', 'mono'], 'tecnica': ['mono', 'body']}
SEPARATORI = ['', '— ', '· ', '/ ', '› ']
RIGA2 = ['colore', 'tinta', 'corsivo']


def voce(tid, profilo):
    h = zlib.crc32(tid.encode('utf-8'))
    v = {'font': FONT.get(profilo, ['mono', 'body'])[h % 2], 'sep': SEPARATORI[(h // 2) % 5],
         'inline': (h // 10) % 3 == 0, 'riga2': RIGA2[(h // 30) % 3], 'occhiello': True}
    if profilo == 'essenziale' and (h // 90) % 2 == 0:
        v['occhiello'] = False
    return v


def applica_sh(s, v):
    s['eyebrow_font_family'] = v['font']
    s['eyebrow_separator'] = v['sep']
    if not v['occhiello']:
        s['eyebrow_show'] = False
    hl = s.get('headline_lines')
    if isinstance(hl, list) and len(hl) >= 2 and all(isinstance(x, dict) for x in hl):
        s['headline_inline'] = v['inline']
        if v['riga2'] in ('tinta', 'corsivo'):
            for x in hl[1:]:
                x['color'] = hl[0].get('color', x.get('color'))
                x['italic'] = v['riga2'] == 'corsivo'


MONO = ('Mono', 'Code', 'Courier')
# caratteri da titolo che a 13 px in maiuscolo non si leggono (strettissimi, larghissimi, a pennello): lì il testo
DISPLAY = {'Six Caps', 'Syncopate', 'Anton', 'Bebas Neue', 'Big Shoulders Display', 'Oswald', 'Orbitron', 'Krona One', 'Syne',
           'Unbounded', 'Shrikhand', 'Righteous', 'Caveat Brush', 'Saira Condensed', 'Chakra Petch', 'Italiana', 'Darker Grotesque',
           'Monoton', 'Bungee', 'Rubik Mono One', 'Climate Crisis', 'Major Mono Display', 'Limelight', 'Abril Fatface'}


def caratteri(tid):
    import re
    th = json.load(open(os.path.join(TEMI, tid, 'theme.json'), encoding='utf-8'))
    ty = (th.get('styles') or {}).get('typography') or {}
    nome = lambda v: (re.match(r"\s*['\"]?([^'\",]+)", v or '') or [None, ''])[1].strip()
    return nome(ty.get('font_family')), nome(ty.get('font_family_heading'))


def corsivo_vero(tid):
    try:
        th = json.load(open(os.path.join(TEMI, tid, 'theme.json'), encoding='utf-8'))
    except (OSError, ValueError):
        return True
    st = th.get('styles') or {}
    titoli = caratteri(tid)[1]
    return bool(titoli) and titoli in (st.get('google_fonts_italic') or [])


def occhiello_apertura(tid, built, v):
    """L'occhiello dell'apertura (Pro Slider della prima sezione: il testo piccolo in maiuscolo in alto) prende la voce del
    tema: nei temi che non usano il monospazio, il carattere del testo o dei titoli."""
    if v['font'] == 'mono':
        return
    corpo, titoli = caratteri(tid)
    fam = titoli if v['font'] == 'heading' and titoli and titoli not in DISPLAY else corpo
    if not fam:
        return
    c = built['content'] if isinstance(built, dict) and 'content' in built else built
    if not isinstance(c, list) or not c or '"proslider"' not in json.dumps(c[0]):
        return

    def w(n):
        if isinstance(n, dict):
            if n.get('type') == 'proslider':
                for sl in (n.get('settings') or {}).get('slides') or []:
                    for l in sl.get('layers') or []:
                        f = str(l.get('fontFamily') or '')
                        if l.get('textTransform') == 'uppercase' and float(l.get('fontSize') or 99) <= 15                                 and float(l.get('y') or 0) < 25 and (any(m in f for m in MONO) or not f):
                            l['fontFamily'] = fam
                return
            for x in n.values():
                if isinstance(x, (dict, list)):
                    w(x)
        elif isinstance(n, list):
            for x in n:
                w(x)
    w(c[0])


def applica_tema(tid, nome_file, built, conta=None):
    """Per base.write_theme."""
    if nome_file in ('header.json', 'footer.json', 'theme.json'):
        return
    pr = (json.load(open(PROFILI, encoding='utf-8')) if os.path.exists(PROFILI) else {}).get(tid)
    v = voce(tid, pr)
    # «in corsivo» solo se il carattere dei titoli ha il corsivo caricato (styles.google_fonts_italic): se no il browser
    # lo inclina finto, storto (18 nov 2026, Ruota Libera in Bricolage Grotesque). Allora resta dello stesso colore.
    if v['riga2'] == 'corsivo' and not corsivo_vero(tid):
        v = dict(v, riga2='tinta')
    occhiello_apertura(tid, built, v)

    def w(n):
        if isinstance(n, dict):
            if n.get('type') == 'section-header' and isinstance(n.get('settings'), dict):
                applica_sh(n['settings'], v)
                if conta is not None:
                    conta[0] += 1
            for x in n.values():
                if isinstance(x, (dict, list)):
                    w(x)
        elif isinstance(n, list):
            for x in n:
                w(x)
    w(built)


def main():
    sys.stdout.reconfigure(encoding='utf-8')
    scrivi = '--scrivi' in sys.argv
    temi = [a for a in sys.argv[1:] if not a.startswith('--')] or sorted(os.listdir(TEMI))
    profili = json.load(open(PROFILI, encoding='utf-8'))
    from collections import Counter
    combo = Counter()
    tot = [0]
    for t in temi:
        v = voce(t, profili.get(t))
        combo['%s %s %s %s %s' % (v['font'], repr(v['sep']), 'seguito' if v['inline'] else 'righe', v['riga2'], '' if v['occhiello'] else 'senza-occhiello')] += 1
        d = os.path.join(TEMI, t)
        for f in sorted(os.listdir(d)):
            if not f.endswith('.json') or f in ('theme.json', 'header.json', 'footer.json'):
                continue
            p = os.path.join(d, f)
            testo = open(p, encoding='utf-8').read()
            if '"section-header"' not in testo:
                continue
            j = json.loads(testo)
            applica_tema(t, f, j, tot)
            if scrivi:
                compatto = not testo.lstrip().startswith('{\n') and not testo.lstrip().startswith('[\n')
                open(p, 'w', encoding='utf-8', newline='\n').write(
                    json.dumps(j, ensure_ascii=False, separators=(',', ':')) if compatto else json.dumps(j, ensure_ascii=False, indent=1))
    print(len(combo), 'combinazioni su', len(temi), 'temi; la più usata:', combo.most_common(3))
    print(tot[0], 'titoli di sezione', '(scritti)' if scrivi else '(prova)')


if __name__ == '__main__':
    main()
