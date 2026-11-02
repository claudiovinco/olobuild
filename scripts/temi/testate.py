# -*- coding: utf-8 -*-
"""Le testate dei temi in un posto solo (1.4.916): il simbolo del logo, il menu mobile e l'effetto delle voci.

applica(id, s) sistema le impostazioni del Mega Menu di un tema. La chiamano kit.testata e negozio.testata quando un tema
si rigenera, e patch_testate.py la passa su header.json di tutti i temi (anche quelli fatti a mano o da altri script):
così una rigenerazione non perde le scelte.

- Logo: niente più pallino. Ogni tema ha il suo simbolo (LOGHI) in una forma che cambia da un tema all'altro (FORME):
  pieno nel primario, ad anello, ad arco, a foglia, esagono, rombo, ciottolo, o il simbolo da solo più grande.
- Menu mobile: dodici ricette (RICETTE) assegnate nell'ordine di creazione dei temi, così due temi vicini nella Vetrina
  non si ripetono: entra da destra, da sinistra, dall'alto, dal basso, a tutto schermo con animazioni diverse; il
  disegno delle voci, un decoro dietro e il piede sempre coi social, in forme diverse.
- Barra: l'effetto delle voci cambia solo dove era quello di serie (sottolineato o nessuno).
"""
import json
import os

QUI = os.path.dirname(os.path.abspath(__file__))
TEMI = os.path.normpath(os.path.join(QUI, '..', '..', 'assets', 'data', 'themes'))
CREATI = os.path.normpath(os.path.join(QUI, '..', '..', 'assets', 'data', 'temi-creati.json'))

# Il simbolo di ogni tema (icone Lucide del selettore). Chi aveva già un simbolo lo tiene.
LOGHI = {
    'acqua-viva': 'droplets', 'aerostato': 'balloon', 'ago-e-ancora': 'anchor', 'anima': 'music-3', 'argilla': 'amphora',
    'arnia': 'hexagon', 'arrocco': 'chess-rook', 'bacca-nera': 'cherry', 'bagnasciuga': 'ice-cream-cone',
    'baita-stua': 'flame-kindling', 'bandeja': 'target', 'bialbero': 'car-front', 'bolina': 'ship-wheel',
    'ca-notturna': 'moon-star', 'calce-pigmento': 'paint-roller', 'calice': 'wine', 'cantiere-aperto': 'hard-hat',
    'cantina-vallerose': 'grape', 'capitonne': 'sofa', 'carambola': 'circle-dot', 'carico-libero': 'dumbbell',
    'casa-scogliera': 'sailboat', 'cementina': 'grid-2x2', 'chiavistello': 'key', 'ciak': 'clapperboard',
    'cielo-buio': 'telescope', 'circo-pappagallo': 'tent', 'clorofilla': 'leafy-green', 'codice-ragazzi': 'code-xml',
    'concia': 'briefcase', 'conto-chiaro': 'calculator', 'controtempo': 'music-4', 'cordata': 'mountain-snow',
    'cornicione': 'pizza', 'crocevia': 'compass', 'cromo': 'aperture', 'cuccia': 'paw-print', 'davanzale': 'lamp-floor',
    'denim-district': 'scissors', 'diottria': 'glasses', 'doppia-mandata': 'key-round', 'falda': 'umbrella',
    'fenice-calcio': 'trophy', 'fermento': 'beer', 'ferraris-avvocati': 'scale', 'fiori-d-arancio': 'flower',
    'focolare': 'heater', 'foglia-lenta': 'coffee', 'fondente': 'candy', 'fonderia-mella': 'flame', 'forma': 'hammer',
    'fornace': 'flask-round', 'fotogramma': 'film', 'fulmine': 'zap', 'fuoco-lento': 'chef-hat', 'fuoco-studio': 'flame',
    'fuorigiri': 'gauge', 'giardino-di-elsa': 'sprout', 'giroscopio': 'rotate-3d', 'girotondo': 'baby',
    'giulia-marchi': 'pen-tool', 'giunto': 'cog', 'glifo': 'type', 'halden-classic': 'car', 'il-gufo': 'bird',
    'il-pozzo': 'music', 'inchiostro-riso': 'layers', 'insegna': 'signpost', 'kaamos': 'snowflake', 'kemet': 'pyramid',
    'lamiere-astico': 'anvil', 'lanterne': 'hand-heart', 'leva-consulenza': 'triangle', 'linea-reno': 'git-commit-horizontal',
    'lumen-analytics': 'chart-column', 'lunga-posa': 'focus', 'lungolago': 'waves-horizontal', 'madia': 'wheat',
    'maison-blanche': 'gem', 'mano-libera': 'scissors-line-dashed', 'mare-pulito': 'fish', 'margini': 'feather',
    'marmi-lizza': 'mountain', 'meridiana': 'sun-medium', 'meriggio-energia': 'sun', 'mezza-delle-mura': 'medal',
    'mezzanotte': 'martini', 'moneta': 'wallet', 'nido-coworking': 'wifi', 'nove-lune': 'moon', 'nove-posti': 'fish-symbol',
    'nutrire': 'apple', 'officine-quota': 'crosshair', 'ombra-riserbo': 'eye', 'onde-corte': 'audio-waveform',
    'ora-blu': 'flask-conical', 'orbita-bassa': 'satellite', 'orlo': 'shirt', 'oro-fino': 'crown', 'orto-vivo': 'carrot',
    'osteria-fumo': 'flame', 'pagine-sparse': 'book-open', 'parla-mondo': 'languages', 'passo-lento': 'map',
    'pastificio-sorgiva': 'wheat', 'pennino': 'pen-tool', 'penombra': 'lamp-ceiling', 'pianta-libera': 'land-plot',
    'pista-nera': 'cable-car', 'pixel-club': 'joystick', 'presa-diretta': 'mic', 'profondo': 'shell', 'radice': 'salad',
    'raggiera': 'bike', 'respiro': 'wind', 'rewind-vintage': 'cassette-tape', 'rifugio-larice': 'tree-pine',
    'riga-di-comando': 'square-terminal', 'ring': 'hand-fist', 'ristretto': 'coffee', 'ruggine': 'guitar',
    'rugiada': 'flower-2', 'sali-argento': 'camera', 'sartoria-ferro': 'ruler', 'scappamento': 'watch', 'scheda-madre': 'cpu',
    'scia': 'ship', 'scudo': 'shield-check', 'settimo-asse': 'bot', 'siero': 'droplet', 'sillage': 'spray-can',
    'sincope': 'piano', 'sirena': 'siren', 'soffio': 'person-standing', 'soglia': 'frame', 'solco-agritech': 'tractor',
    'solleone': 'image', 'sorriso-clinic': 'smile', 'spiedo': 'flame', 'staffa': 'magnet', 'strada-bianca': 'caravan',
    'strapiombo': 'hand', 'strato': 'printer', 'studio-assi': 'drafting-compass', 'studio-quiete': 'leaf',
    'suola': 'footprints', 'synthetica': 'sparkles', 'tacet': 'shapes', 'tavola-errante': 'utensils', 'teatro-darsena': 'drama',
    'teatro-seta': 'theater', 'telaio': 'wrench', 'tepidario': 'bath', 'tessiture-navetta': 'spool', 'tostatura-nove': 'bean',
    'tracolla': 'shopping-bag', 'tratteggio': 'paintbrush', 'trottola': 'blocks', 'undici-ulivi': 'trees',
    'varco-nove': 'container', 'velluto': 'sparkle', 'venatura': 'axe', 'ventiquattro': 'video', 'vertebra': 'bone',
    'vinile-club': 'disc-3', 'volo-leggero': 'sunrise', 'zolla': 'shovel', 'zucchero-a-velo': 'cake-slice',
}

# La disposizione della testata dei negozi del kit (negozio.testata): otto forme, perché non siano tutte uguali.
NEGOZI = {
    'crocevia': 'classica', 'tracolla': 'in-linea', 'spiedo': 'in-linea', 'orlo': 'logo-al-centro',
    'velluto': 'logo-al-centro', 'davanzale': 'menu-diviso', 'falda': 'menu-diviso',
    'suola': 'ricerca-in-alto', 'ristretto': 'scura', 'carambola': 'scura', 'siero': 'due-piani', 'pennino': 'due-piani',
    'calice': 'due-piani', 'madia': 'barra-colorata', 'trottola': 'barra-colorata',
}

P, SU = 'var(--olo-color-primary)', 'var(--olo-color-primary-contrast)'
# Le forme del simbolo, in giro: due temi vicini non hanno la stessa.
FORME = ['circle', 'arch', 'ring', 'leaf', 'rounded', 'hexagon', 'nudo', 'blob', 'square', 'diamond']

# Le ricette del menu mobile. Le chiavi sono quelle del Mega Menu; 'piede_testo' è il «Testo in fondo».
RICETTE = [
    # 0 · pannello da destra, voci da rivista, il nome in verticale dietro
    dict(mobile_style='offcanvas', offcanvas_fullscreen=False, mobile_slide_direction='left', mobile_side='right',
         mobile_look='editoriale', mobile_deco='nome', mobile_foot_look='riga', social_style='outline',
         menu_items_animation='slide-right', hamburger_style='squeeze', hamburger_box='none', piede_testo='Seguici'),
    # 1 · tutto schermo che si apre a cerchio dall'hamburger, voci al centro, un cerchio di colore
    dict(mobile_style='fullscreen', fullscreen_animation='circular', mobile_look='centrato', mobile_deco='cerchio',
         mobile_foot_look='fascia', social_style='circle', menu_items_animation='fade-up', hamburger_style='morph',
         hamburger_box='circle', piede_testo='Seguici'),
    # 2 · tutto schermo che scorre da sinistra, voci a pillole in fila, trama di punti, piede nel colore d'accento
    dict(mobile_style='fullscreen', fullscreen_animation='slide-left', mobile_look='pillole', mobile_deco='punti',
         mobile_foot_look='accento', social_style='rounded', menu_items_animation='scale', hamburger_style='collapse',
         hamburger_box='none', piede_testo='Seguici'),
    # 3 · pannello da sinistra a schede, trama di punti, piede a scheda
    dict(mobile_style='offcanvas', offcanvas_fullscreen=False, mobile_slide_direction='left', mobile_side='left',
         mobile_look='schede', mobile_deco='punti', mobile_foot_look='scheda', social_style='brand',
         menu_items_animation='slide-left', hamburger_style='arrow', hamburger_box='none', piede_testo='Restiamo in contatto'),
    # 4 · tutto schermo da destra, indice coi numeri a contorno, il simbolo grande dietro, social per nome
    dict(mobile_style='fullscreen', fullscreen_animation='slide-right', mobile_look='indice', mobile_deco='simbolo',
         mobile_foot_look='nomi', social_style='plain', menu_items_animation='fade-down', hamburger_style='elastic',
         hamburger_box='square', piede_testo='Ci trovi anche su'),
    # 5 · pannello che scende dall'alto con la testata colorata, piede nel colore d'accento
    dict(mobile_style='offcanvas', offcanvas_fullscreen=False, mobile_slide_direction='top', mobile_side='left',
         mobile_look='fascia', mobile_deco='', mobile_foot_look='accento', social_style='outline',
         menu_items_animation='fade-down', hamburger_style='rotate', hamburger_box='none', piede_testo='Seguici sui social'),
    # 6 · foglio che sale dal basso, pillole, social a pillola col nome
    dict(mobile_style='offcanvas', offcanvas_fullscreen=False, mobile_slide_direction='bottom', mobile_side='left',
         mobile_look='pillole', mobile_deco='', mobile_foot_look='riga', social_style='pill',
         menu_items_animation='fade-up', hamburger_style='dot-grid', hamburger_box='pill', piede_testo=''),
    # 7 · tutto schermo a sipario, voci da rivista, trama di punti, piede a scheda
    dict(mobile_style='fullscreen', fullscreen_animation='curtain', mobile_look='editoriale', mobile_deco='punti',
         mobile_foot_look='scheda', social_style='square', menu_items_animation='blur', hamburger_style='classic',
         hamburger_box='none', hamburger_label='Menu', piede_testo='Restiamo in contatto'),
    # 8 · pannello laterale che diventa tutto schermo da sinistra, schede, il nome in verticale
    dict(mobile_style='offcanvas', offcanvas_fullscreen=True, fullscreen_animation='slide-left', mobile_look='schede',
         mobile_deco='nome', mobile_foot_look='fascia', social_style='circle', menu_items_animation='slide-left',
         hamburger_style='magnetic', hamburger_box='none', piede_testo='Seguici'),
    # 9 · tutto schermo pieno nel colore primario, che entra in diagonale; voci al centro e il simbolo grande
    dict(mobile_style='fullscreen', fullscreen_animation='diagonal', mobile_look='centrato', mobile_deco='simbolo',
         mobile_foot_look='nomi', social_style='plain', menu_items_animation='scale', hamburger_style='squeeze',
         hamburger_box='circle', pieno=True, piede_testo='Ci trovi anche su'),
    # 10 · pannello da destra con la testata colorata e un cerchio di colore, social per nome
    dict(mobile_style='offcanvas', offcanvas_fullscreen=False, mobile_slide_direction='left', mobile_side='right',
         mobile_look='fascia', mobile_deco='cerchio', mobile_foot_look='nomi', social_style='outline',
         menu_items_animation='slide-right', hamburger_style='minimal', hamburger_box='none', piede_testo='Ci trovi anche su'),
    # 11 · tutto schermo che sale dal basso, indice, cerchio di colore, social nei colori dei marchi
    dict(mobile_style='fullscreen', fullscreen_animation='slide-up', mobile_look='indice', mobile_deco='cerchio',
         mobile_foot_look='riga', social_style='brand', menu_items_animation='fade-up', hamburger_style='collapse',
         hamburger_box='pill', piede_testo='Seguici'),
]

# Temi coi titoli in un carattere molto stretto (Six Caps, Big Shoulders, Teko…): le voci del pannello più grandi.
STRETTI = {'margini': 1.7, 'fermento': 1.25, 'fotogramma': 1.25, 'rifugio-larice': 1.2, 'lamiere-astico': 1.2,
           'carico-libero': 1.2}

# Effetti delle voci della barra (solo quelli che non mettono il testo su un fondo pieno).
EFFETTI = ['underline', 'dot', 'overline', 'highlight', 'double-line', 'fill-up']

# I social del piede: chi ne ha già tiene i suoi; agli altri Instagram, Facebook e un terzo secondo il mestiere.
TERZO = {
    'Ristorazione': 'tiktok', 'Negozi online': 'pinterest', 'Tecnologia': 'linkedin', 'Studi e professioni': 'linkedin',
    'Industria e manifattura': 'linkedin', 'Eventi e musica': 'youtube', 'Creativi e portfolio': 'youtube',
    'Moda e bellezza': 'tiktok', 'Casa e design': 'pinterest', 'Sport e benessere': 'youtube',
}
SOCIAL = ['facebook', 'instagram', 'x', 'linkedin', 'youtube', 'tiktok', 'pinterest', 'whatsapp']

_ordine = None
# I temi la cui testata è già passata di qui in questo giro (kit, negozi, Fulmine): write_theme non la ripassa.
FATTI = set()


def indice(tid):
    """La posizione del tema nell'ordine di creazione (quello della Vetrina); un tema non ancora in elenco va in coda."""
    global _ordine
    if _ordine is None:
        try:
            date = json.load(open(CREATI, encoding='utf-8'))
        except (OSError, ValueError):
            date = {}
        _ordine = [k for k, _ in sorted(date.items(), key=lambda kv: (kv[1], kv[0]))]
    if tid in _ordine:
        return _ordine.index(tid)
    return len(_ordine) + sum(map(ord, tid)) % 12


def categoria(tid):
    try:
        return json.load(open(os.path.join(TEMI, tid, 'theme.json'), encoding='utf-8')).get('category', '')
    except (OSError, ValueError):
        return ''


def simbolo(tid, s, i=None):
    """Il simbolo del logo testuale: via il pallino, il simbolo del tema nella sua forma."""
    FATTI.add(tid)
    if not (s.get('logo_text') or '').strip() or s.get('logo_image') or (s.get('logo_crest') or '').strip():
        return s
    i = indice(tid) if i is None else i
    nome = LOGHI.get(tid) or s.get('logo_mark') or 'sparkle'
    forma = FORME[i % len(FORME)]
    try:
        testo = int(float(s.get('logo_text_size') or 20))
    except ValueError:
        testo = 20
    s.update({'logo_dot': False, 'logo_mark': nome})
    if forma == 'nudo':
        s.update({'logo_mark_bg': '', 'logo_mark_color': P, 'logo_mark_size': max(22, min(32, testo + 4)),
                  'logo_mark_stroke': 1.5})
    elif forma == 'ring':
        s.update({'logo_mark_bg': P, 'logo_mark_color': P, 'logo_mark_shape': 'ring',
                  'logo_mark_size': max(16, min(22, testo - 3)), 'logo_mark_stroke': 1.75})
    else:
        s.update({'logo_mark_bg': P, 'logo_mark_color': SU, 'logo_mark_shape': forma,
                  'logo_mark_size': max(15, min(21, testo - 4 if forma in ('diamond', 'hexagon') else testo - 3)),
                  'logo_mark_stroke': 2})
    return s


def mobile(tid, s, i=None):
    """Il menu mobile del tema secondo la sua ricetta, coi social sempre nel piede."""
    FATTI.add(tid)
    i = indice(tid) if i is None else i
    r = dict(RICETTE[i % len(RICETTE)])
    pieno = r.pop('pieno', False)
    testo = r.pop('piede_testo', '')
    r.setdefault('hamburger_label', '')
    s.update(r)
    s.update({'mobile_numbers': False, 'social_in_mobile': True, 'social_mobile_pos': 'bottom',
              'mobile_footer_text': testo, 'mobile_footer_cta_text': '', 'mobile_footer_cta_url': '',
              'mob_separator_style': 'line', 'menu_items_stagger': '70'})
    if pieno:
        s.update({'mobile_bg': P, 'mobile_text_color': SU, 'mobile_accent_color': SU, 'social_color': ''})
    # Le misure delle voci secondo il disegno: a schede e a pillole stanno strette, a tutto schermo respirano.
    try:
        grande = int(float(s.get('mobile_link_size') or 0)) or 34
    except ValueError:
        grande = 34
    grande = max(26, min(38, grande))
    look, stile = r['mobile_look'], r['mobile_style']
    if stile == 'fullscreen' or r.get('offcanvas_fullscreen'):
        s['mobile_link_size'] = str({'pillole': 22, 'schede': 24}.get(look, grande))
        s['mobile_font_size'] = str({'pillole': 18, 'schede': 22}.get(look, 26))
    else:
        s['mobile_font_size'] = str({'pillole': 17, 'schede': 19, 'fascia': 20}.get(look, 20))
        s['mobile_link_size'] = s['mobile_font_size']
    k = STRETTI.get(tid, 1)
    if k != 1:
        s['mobile_link_size'] = str(min(56, int(round(int(s['mobile_link_size']) * k))))
        s['mobile_font_size'] = str(min(40, int(round(int(s['mobile_font_size']) * k))))
    if stile == 'offcanvas' and not r.get('offcanvas_fullscreen') and r.get('mobile_slide_direction') == 'left':
        s['mobile_panel_width'] = 340
    # social: almeno tre
    if sum(1 for k in SOCIAL if (s.get('social_' + k) or '').strip()) < 2:
        terzo = TERZO.get(categoria(tid), 'whatsapp')
        for k in ('instagram', 'facebook', terzo):
            if not (s.get('social_' + k) or '').strip():
                s['social_' + k] = '#'
    s.setdefault('social_size', '18')
    return s


def barra(tid, s, i=None):
    """L'effetto delle voci della barra, solo dove era quello di serie."""
    FATTI.add(tid)
    i = indice(tid) if i is None else i
    if (s.get('hover_effect') or 'none') in ('underline', 'none'):
        s['hover_effect'] = EFFETTI[i % len(EFFETTI)]
        if s['hover_effect'] in ('underline', 'overline', 'double-line'):
            s['hover_effect_height'] = 2
    return s


def applica(tid, s, logo=True):
    """Tutte le scelte della testata del tema `tid` sulle impostazioni `s` del Mega Menu (le cambia e le restituisce)."""
    i = indice(tid)
    if logo:
        simbolo(tid, s, i)
    mobile(tid, s, i)
    barra(tid, s, i)
    return s
