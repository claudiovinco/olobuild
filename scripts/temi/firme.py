# -*- coding: utf-8 -*-
"""Gli effetti «firma» dei quindici temi scuri con lo Slider Pro (30 ottobre 2026): oltre all'apertura, ogni tema ha i
suoi effetti che lo fanno riconoscere, tutti con i controlli nativi di OLObuild (niente CSS a mano):

- ingresso: l'animazione d'ingresso delle righe (al posto del «slide-up» comune);
- testo: l'effetto sui titoli grandi (le frasi delle fasce video e dei pannelli, la 404);
- immagini: tilt 3D col mouse, zoom o messa a fuoco allo scorrimento (Avanzate);
- divisore: una sagoma (Shape divider) che entra in cima alla fascia video della home, del colore della sezione prima;
- bordo: un bordo che gira (gradient-spin) o sfumato sull'elemento chiave del tema.

Uso nei generatori: write_theme(THEME, firme.applica(ID, {...}), MEDIA).
"""
import copy

from base import SAMPLES

P, A, S, L, BG, TX = ('var(--olo-color-primary)', 'var(--olo-color-accent)', 'var(--olo-color-secondary)',
                      'var(--olo-color-light)', 'var(--olo-color-background)', 'var(--olo-color-text)')


def _mix(c1, pct, c2):
    return 'color-mix(in srgb, %s %d%%, %s)' % (c1, pct, c2)


FIRME = {
    'cielo-buio': dict(ingresso='blur-in', testo='reveal-letter', immagini='fuoco', forma='hill'),
    'cordata': dict(ingresso='zoom-in-up', testo='underline-grow', immagini='tilt', forma='peak'),
    'lanterne': dict(ingresso='fade-up-big', testo='highlight-grow', testo_colori=(_mix(P, 35, 'transparent'), ''),
                     immagini='zoom', forma='skyline'),
    'kaamos': dict(ingresso='blur-zoom', testo='gradient-anim', testo_colori=(P, S), immagini='fuoco', forma='mountains'),
    'ca-notturna': dict(ingresso='curtain-reveal', testo='reveal-letter', immagini='tilt', forma='ripples'),
    'fotogramma': dict(ingresso='flip-x', testo='typewriter', immagini='zoom', forma='teeth'),
    'arrocco': dict(ingresso='flip-y', testo='reveal-word', immagini='tilt', forma='steps',
                    bordo=('pricing', 'popolare', 'gradient-spin', P, A)),
    'profondo': dict(ingresso='fade-up', testo='wave', immagini='fuoco', forma='wave'),
    'ring': dict(ingresso='lightspeed-left', testo='glitch', immagini='zoom', forma='zigzag',
                 bordo=('pricing', 'popolare', 'gradient-spin', P, A)),
    'sillage': dict(ingresso='blur-in', testo='gradient-anim', testo_colori=(P, L), immagini='tilt', forma='curve'),
    'forma': dict(ingresso='skew-in', testo='underline-grow', immagini='tilt', forma='swoosh'),
    'calce-pigmento': dict(ingresso='back-in-left', testo='gradient-anim', testo_colori=(P, _mix(S, 45, L)),
                           immagini='zoom', forma='brush'),
    'ombra-riserbo': dict(ingresso='zoom-in-down', testo='scramble', testo_colori=(P, TX), immagini='fuoco',
                          forma='slope', bordo=('flipcard', 'tutte', 'gradient', P, A)),
    'meridiana': dict(ingresso='fade-up', testo='reveal-word', immagini='zoom', forma='tilt',
                      bordo=('projector', 'tutte', 'gradient-spin', P, A)),
    'focolare': dict(ingresso='zoom-in-up', testo='gradient-anim', testo_colori=(P, A), immagini='zoom', forma='valley'),
    # i sei del 30 ottobre (tre chiari e tre scuri)
    'zucchero-a-velo': dict(dosato=2, ingresso='zoom-in', testo='underline-grow', testo_colori=(P, ''),
                            immagini='tilt', forma='drips'),
    'soffio': dict(dosato=2, ingresso='fade', testo='reveal-word', immagini='fuoco', forma='clouds'),
    'fiori-d-arancio': dict(dosato=2, ingresso='fade-down-big', testo='reveal-letter', immagini='zoom', forma='wave2'),
    'fermento': dict(dosato=2, ingresso='jack-in-box', testo='wave', immagini='zoom', forma='bubbles',
                     bordo=('pricing', 'popolare', 'gradient-spin', P, A)),
    'bialbero': dict(dosato=2, ingresso='lightspeed-right', testo='typewriter', immagini='tilt', forma='chevron'),
    'halden-classic': dict(dosato=2, ingresso='zoom-out', testo='underline-grow', testo_colori=(A, ''), immagini='zoom',
                           forma='notch'),
    'controtempo': dict(dosato=1, ingresso='roll-in', testo='gradient-anim', testo_colori=(P, S), immagini='zoom', forma='dinamica'),
    # i quindici del 31 ottobre
    'nove-posti': dict(dosato=2, ingresso='fade-up', testo='reveal-letter', immagini='zoom', forma='ripples'),
    'diottria': dict(dosato=2, ingresso='blur-in', testo='scramble', testo_colori=(P, TX), immagini='fuoco', forma='curve'),
    'cornicione': dict(dosato=2, ingresso='zoom-in-up', testo='wave', immagini='zoom', forma='wave'),
    'orbita-bassa': dict(dosato=2, ingresso='zoom-in', testo='gradient-anim', testo_colori=(P, A), immagini='tilt', forma='tilt'),
    'bacca-nera': dict(dosato=2, ingresso='blur-zoom', testo='reveal-word', immagini='zoom', forma='mountains'),
    'bandeja': dict(dosato=2, ingresso='back-in-left', testo='underline-grow', testo_colori=(A, ''), immagini='tilt', forma='zigzag'),
    'rugiada': dict(dosato=2, ingresso='curtain-reveal', testo='reveal-word', immagini='zoom', forma='ripples'),
    'presa-diretta': dict(dosato=2, ingresso='flip-x', testo='glitch', immagini='tilt', forma='steps'),
    'anima': dict(dosato=2, ingresso='fade-up-big', testo='reveal-letter', immagini='tilt', forma='curve'),
    'fondente': dict(dosato=2, ingresso='zoom-in-up', testo='gradient-anim', testo_colori=(P, A), immagini='zoom', forma='drips'),
    'mano-libera': dict(dosato=2, ingresso='flip-y', testo='underline-grow', testo_colori=(P, ''), immagini='tilt', forma='steps'),
    'girotondo': dict(dosato=2, ingresso='zoom-in', testo='wave', immagini='tilt', forma='clouds'),
    'strapiombo': dict(dosato=2, ingresso='zoom-in-up', testo='underline-grow', testo_colori=(P, ''), immagini='zoom', forma='peak'),
    'fornace': dict(dosato=2, ingresso='blur-in', testo='gradient-anim', testo_colori=(P, A), immagini='fuoco', forma='wave'),
    'scappamento': dict(dosato=2, ingresso='zoom-in-down', testo='reveal-word', immagini='tilt', forma='teeth'),
    # Insegna rifatta il 31 ott 2026: l'effetto forte è il cubo (e la piega nei Lavori), il resto sobrio.
    'insegna': dict(dosato=1, ingresso='blur-in', testo='none', immagini='', forma=''),
}


def _immagine(adv, tipo):
    if tipo == 'tilt':
        adv.update({'mouse_tilt': True, 'mouse_tilt_intensity': 8})
    elif tipo == 'zoom':
        adv.update({'scroll_effect_scale': True, 'scroll_scale_start': 88, 'scroll_scale_end': 100})
    elif tipo == 'fuoco':
        adv.update({'scroll_effect_blur': True, 'scroll_blur_start': 8, 'scroll_blur_end': 0})


def _testo(s, f):
    c1, c2 = f.get('testo_colori', ('', ''))
    s.update({'text_effect': f['testo'], 'text_effect_target': 'heading', 'text_effect_speed': '45',
              'text_effect_delay': '0', 'text_effect_loop': False, 'text_effect_cursor': f['testo'] == 'typewriter',
              'text_effect_cursor_char': '|', 'text_effect_color': c1, 'text_effect_color_to': c2,
              'text_effect_phrases': '', 'text_effect_pause': '1500'})


def _ha_titolo(n):
    return n.get('type') == 'section-header' or any(_ha_titolo(c) for c in n.get('children') or [])


def _nodo(n, f, conto=None):
    if isinstance(n, list):
        for x in n:
            _nodo(x, f, conto)
        return
    tipo, s = n.get('type'), n.get('settings') or {}
    if tipo == 'row' and s.get('entrance_animation') == 'slide-up':
        # Ingresso dosato (utente, 30 ott 2026, su Controtempo: «lo usa ovunque, non va bene, deve essere dosato in
        # meno zone o stufa»): quello del tema solo sui titoli di sezione, al massimo `dosato` per pagina; le altre
        # righe entrano con un fade-up discreto.
        if f.get('dosato'):
            if conto is not None and conto[0] < f['dosato'] and _ha_titolo(n):
                conto[0] += 1
                s['entrance_animation'] = f['ingresso']
            else:
                s['entrance_animation'] = 'fade-up'
        else:
            s['entrance_animation'] = f['ingresso']
    elif tipo == 'headline' and f.get('testo') not in (None, '', 'none') and s.get('tag') in ('h1', 'h2') \
            and int(s.get('heading_font_size') or 0) >= 56:
        _testo(s, f)
    elif tipo == 'image':
        n.setdefault('advanced', {})
        _immagine(n['advanced'], f['immagini'])
    b = f.get('bordo')
    if b and tipo == b[0] and (b[1] == 'tutte' or s.get('is_popular')):
        s['border'] = {'top': 2, 'right': 2, 'bottom': 2, 'left': 2, 'linked': True, 'style': 'solid', 'color': b[3]}
        s.update({'border_effect': b[2], 'border_effect_color2': b[4], 'border_effect_speed': 6,
                  'border_effect_angle': 135, 'border_effect_intensity': 'medium'})
    for c in n.get('children') or []:
        _nodo(c, f, conto)


def _sfondo(sez):
    bg = (sez.get('settings') or {}).get('bg') or {}
    return bg.get('color') if bg.get('type') == 'solid' and bg.get('color') else BG


def _divisore(tid, sezioni, forma):
    """La sagoma in cima alla prima sezione a tinta unita che segue una sezione d'altro colore (non l'apertura), del
    colore di quella sopra: sembra che la sezione sopra «morda» quella sotto, a tutta larghezza. Non sulle fasce video:
    lì il contenitore interno è posizionato e la sagoma finirebbe dentro il padding, in mezzo al video."""
    for i in range(2, len(sezioni)):
        s = sezioni[i].get('settings') or {}
        if (s.get('bg') or {}).get('type') == 'solid' and _sfondo(sezioni[i]) != _sfondo(sezioni[i - 1]) \
                and ((sezioni[i - 1].get('settings') or {}).get('bg') or {}).get('type') in (None, 'solid'):
            # La tinta della sezione sopra velata col colore del tema: nei temi scuri fondo e sezione sono troppo vicini
            # perché la sagoma si veda da sola.
            colore = _mix(P, 28, _sfondo(sezioni[i - 1]))
            d = copy.deepcopy(SAMPLES['shapedivider']['settings'])
            d.update({'shape': forma, 'position': 'top', 'flip_horizontal': False, 'flip_vertical': False, 'width': '100',
                      'height': 64, 'color': colore, 'z_index': '2', 'responsive_height_tablet': '48',
                      'responsive_height_mobile': '32'})
            nodo = {'id': '%s-home-divisore' % tid, 'type': 'shapedivider', 'settings': d, 'style': {}, 'advanced': {}}
            # Una riga sua, senza animazione d'ingresso: la trasformazione dell'ingresso farebbe della riga il riferimento
            # del posizionamento assoluto, e la sagoma finirebbe in mezzo al video invece che sul bordo della sezione
            # (come fa Acqua Viva, che la tiene in una riga a parte).
            colonna = {'id': '%s-home-divisore-col' % tid, 'type': 'column', 'settings': {}, 'style': {}, 'advanced': {},
                       'children': [nodo]}
            riga = {'id': '%s-home-divisore-riga' % tid, 'type': 'row', 'settings': {'layout': '100', 'stack_mobile': True},
                    'style': {}, 'advanced': {}, 'children': [colonna]}
            sezioni[i]['children'].insert(0, riga)
            return


def applica(tid, builds):
    f = FIRME.get(tid)
    if not f:
        return builds
    for nome, b in builds.items():
        if nome in ('header.json', 'footer.json'):
            continue
        contenuto = b['content'] if isinstance(b, dict) else b
        _nodo(contenuto, f, [0])
        if nome == 'home.json' and f.get('forma'):
            _divisore(tid, contenuto, f['forma'])
    return builds
