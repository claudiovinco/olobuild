# -*- coding: utf-8 -*-
"""Come si prova: una classe su quaranta, tre passi, cosa è provato e cosa no, il modulo."""
from comune import (K, Tpl, mix, radius, solid, img, occ, tit, txt, mano, btn, bottoni, foto, schermata, sez, due, griglia,
                    scheda, MANO, CARTA, INK, TENUE, LAV, VERDE, ONV, GIALLO, GESSO, CIELO, FILO, ROSSO, VERDE_G, GESSO_T,
                    GESSO_F, LAV2, CARTA2, MET, NOTA, MAIL, SITO)
from p_metodo import RIF

m = K.mob


def apertura(t):
    L = K.livello
    liv = [
        L('p-occ', contenuto='Prima di adottare', x=6, y=14, w=40, size=14, peso='700', colore=GIALLO, letterSpacing=2,
          textTransform='uppercase', ritardo=100, responsive={'mobile': {'fontSize': m(11), 'x': 6, 'y': 2.4, 'width': 88}}),
        L('p-t1', contenuto='Come si prova', tag='h1', x=6, y=18.5, w=46, size=80, peso='700', colore=GESSO, fontFamily='Lexend',
          lineHeight=1.0, letterSpacing=-2, entra='maskFromBottom', ritardo=250, durata=1000,
          responsive={'mobile': {'fontSize': m(40), 'x': 6, 'y': 4.3, 'width': 88}}),
        L('p-t2', contenuto='Si comincia da una classe.', tag='p', x=6, y=30.5, w=46, size=40, peso='700', colore=GIALLO,
          fontFamily='Lexend', lineHeight=1.05, entra='maskFromBottom', ritardo=450, durata=1000,
          responsive={'mobile': {'fontSize': m(20), 'x': 6, 'y': 9.2, 'width': 88}}),
        L('p-s1', contenuto='Non serve aprire un progetto: si sceglie la classe e la materia e si comincia, e il resto '
          'dell\'istituto non se ne accorge.', x=6, y=40, w=40, size=19, peso='400', colore=GESSO, lineHeight=1.5, ritardo=650,
          responsive={'mobile': {'fontSize': m(14), 'x': 6, 'y': 12.6, 'width': 88}}),
        L('p-s2', contenuto='Una classe, un corso già in calendario, e il tempo di un\'unità di apprendimento. Non serve una '
          'delibera, non serve un piano triennale, non serve spiegarlo a nessuno finché non c\'è qualcosa da dire.', x=6, y=51,
          w=40, size=16, peso='400', colore=GESSO_T, lineHeight=1.55, ritardo=800,
          responsive={'mobile': {'fontSize': m(13), 'x': 6, 'y': 21, 'width': 88}}),
        L('p-b1', tipo='button', contenuto='Scrivici', x=6, y=67, w='auto', size=16, peso='700', colore=INK, bgColor=GIALLO,
          padding=17, borderRadius=12, buttonUrl='#scrivici', ritardo=1000, entra='fadeIn',
          hover={'bgColor': GESSO, 'duration': 250}, responsive={'mobile': {'x': 6, 'y': 33, 'fontSize': m(13)}}),
        L('p-b2', tipo='button', contenuto='Scarica la nota di sintesi', x=16.5, y=67, w='auto', size=16, peso='700',
          colore=GESSO, bgColor='transparent', padding=17, borderRadius=12, borderWidth=2, borderColor=mix(GESSO, 45),
          buttonUrl=NOTA, buttonTarget='_blank', ritardo=1100, entra='fadeIn', hover={'bgColor': mix(GESSO, 12), 'duration': 250},
          responsive={'mobile': {'x': 32, 'y': 33, 'fontSize': m(13)}}),  # 7 nov: più spazio dopo «Scrivici»
        L('p-quaranta', contenuto='Quaranta classi in un istituto medio.', x=52.6, y=9, w=42.4, size=17, peso='700',
          colore=GESSO_T, entra='fadeIn', ritardo=500,
          responsive={'mobile': {'x': 5.1, 'y': 40, 'width': 89.8, 'fontSize': m(14)}}),
    ]
    # le quaranta classi: 8 per 5; la 2A (seconda riga, quinta colonna) si accende, le altre si spengono
    CW, CGX = 4.6, 0.8
    CH, CGY = 66 / 810 * 100, 11.5 / 810 * 100
    X0, Y0 = 52.6, 15.0
    TCW, TCGX = 10.0, 1.4
    TCH, TCGY = 64 / 1400 * 100, 9 / 1400 * 100
    TX0, TY0 = 5.1, 43.0
    scelta = (1, 4)
    for r in range(5):
        for c in range(8):
            x, y = round(X0 + c * (CW + CGX), 2), round(Y0 + r * (CH + CGY), 2)
            tx, ty = round(TX0 + c * (TCW + TCGX), 2), round(TY0 + r * (TCH + TCGY), 2)
            k = r * 8 + c
            spegni = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'o': 100}, {'p': 12 + (k % 7), 'o': 100},
                                                          {'p': 34 + (k % 5), 'o': 28}, {'p': 100, 'o': 28}]}
            liv.append(L('p-cl%d' % k, tipo='shape', x=x, y=y, w=CW, h=CH, bgColor=mix(GESSO, 12), borderRadius=10,
                         borderWidth=1, borderColor=mix(GESSO, 20), entra='zoomIn', ritardo=300 + k * 18, durata=500,
                         scrollMove=None if (r, c) == scelta else spegni,
                         responsive={'mobile': {'x': tx, 'y': ty, 'width': TCW, 'height': TCH}}))
            if (r, c) == scelta:
                sx, sy, stx, sty = x, y, tx, ty
    accendi = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'o': 0, 's': 0.6}, {'p': 26, 'o': 0, 's': 0.6},
                                                   {'p': 36, 'o': 100, 's': 1.25}, {'p': 42, 'o': 100, 's': 1},
                                                   {'p': 100, 'o': 100, 's': 1}]}
    liv += [
        L('p-2a', tipo='shape', x=sx, y=sy, w=CW, h=CH, bgColor=GIALLO, borderRadius=10, entra='fadeIn', ritardo=0,
          boxShadow={'x': 0, 'y': 10, 'blur': 30, 'spread': -6, 'color': mix(GIALLO, 60)}, scrollMove=accendi,
          responsive={'mobile': {'x': stx, 'y': sty, 'width': TCW, 'height': TCH}}),
        L('p-2a-t', contenuto='2A', x=sx, y=round(sy + CH / 2 - 1.6, 2), w=CW, size=22, peso='800', colore=INK,
          textAlign='center', fontFamily='Lexend', lineHeight=1.0, entra='fadeIn', ritardo=0, scrollMove=accendi,
          responsive={'mobile': {'x': stx, 'y': round(sty + TCH / 2 - 1.0, 2), 'width': TCW, 'fontSize': m(15)}}),
        L('p-una', contenuto='La prova ne occupa una.', x=52.6, y=65.5, w=42.4, size=34, peso='700', colore=GIALLO,
          fontFamily='Lexend', lineHeight=1.1, entra='fadeIn', ritardo=0,
          scrollMove={'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'o': 0, 'y': 16}, {'p': 44, 'o': 0, 'y': 16},
                                                          {'p': 54, 'o': 100, 'y': 0}, {'p': 100, 'o': 100, 'y': 0}]},
          responsive={'mobile': {'x': 5.1, 'y': 70.5, 'width': 89.8, 'fontSize': m(22)}}),
    ]
    for k, (s, x, tx) in enumerate([('1 classe', 52.6, 5.1), ('1 corso', 61.4, 30), ('0 progetti', 69.6, 53.5)]):
        da = 60 + k * 7
        liv.append(L('p-num%d' % k, contenuto=s, x=x, y=76, w='auto', size=17, peso='700', colore=GESSO,
                     bgColor=mix(GESSO, 10), padding=12, borderRadius=999, borderWidth=1, borderColor=mix(GESSO, 30),
                     entra='fadeIn', ritardo=0,
                     scrollMove={'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'o': 0, 's': 0.8}, {'p': da, 'o': 0, 's': 0.8},
                                                                     {'p': da + 6, 'o': 100, 's': 1}, {'p': 100, 'o': 100, 's': 1}]},
                     responsive={'mobile': {'x': tx, 'y': 76.5, 'fontSize': m(13)}}))
    sfondo = {'type': 'image', 'image': img('otr-std-navy-bg.svg'), 'video': '', 'color': '', 'overlay': LAV,
              'overlayOpacity': 0, 'kenBurns': False}
    sl = [K.diapositiva('p-1', sfondo, liv, durata=600000)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', showArrows=False,
                       showDots=False, showTabs=False, showProgressBar=False, autoplay=False, parallax=False,
                       scrollTimeline=True, scrollTimelineDistance=1800, scrollStep=False,
                       heightMobile={'mode': 'px', 'value': 1400}, globalBackground={'type': 'color', 'color': LAV})


def passi(t):
    voci = [
        ('01', 'Il punto di partenza', 'otr-passi-01.svg', 'Senza un progetto', 'Una classe', 'e un corso',
         'Si comincia da lì. Serve un docente disposto a provarci, e niente da approvare.', '1', 'docente basta', '→ poi'),
        ('02', 'Le attività', 'otr-passi-02.svg', 'Il funzionamento è lo stesso di una classe intera', 'Su un gruppo',
         'ristretto', 'Le carte di ripasso, il doppio giro di voto, le medie, i punti e i badge: tutto gira su otto persone come '
         'girerebbe su venticinque. Per una partita dal vivo con più di cinquanta persone serve il servizio per il tempo reale.',
         '8', 'come 25', '→ poi'),
        ('03', 'Il resto', 'otr-passi-03.svg', 'Finché non si decide', 'Il resto dell\'istituto', 'aspetta',
         'Niente da annunciare in collegio, niente da spiegare alle famiglie, nessun registro da migrare.', '0',
         'registri da migrare', ''),
    ]
    st = t.classic('step-timeline', {'items': [{
        'counter': a, 'tag_text': b, 'tag_dot_color': VERDE, 'media_type': 'image', 'media_image': img(c), 'media_label': '',
        'media_content': '', 'media_bg': CIELO, 'media_color': VERDE, 'pre_title': d, 'title': e, 'title_accent': f,
        'title_accent_italic': False, 'title_after': '', 'title_after_italic': False, 'description': g, 'footer_value': h,
        'footer_label': i, 'separator_text': j} for a, b, c, d, e, f, g, h, i, j in voci],
        'timeline_line_color': FILO, 'timeline_dot_color': VERDE, 'counter_font_family': 'heading', 'counter_size': 72,
        'counter_color': VERDE, 'counter_italic': False, 'counter_weight': '800', 'tag_color': TENUE,
        'media_aspect_ratio': '336/189', 'media_shadow': 'md', 'pre_title_color': VERDE, 'title_font_family': 'heading',
        'title_size': 30, 'title_weight': '700', 'title_color': INK, 'title_accent_color': VERDE, 'description_size': 16,
        'description_color': TENUE, 'footer_icon': 'users', 'footer_value_color': INK, 'footer_label_color': TENUE,
        'separator_color': VERDE, 'show_separator': True, 'columns': 3, 'gap': 40})
    return sez(t, [
        due(t, [occ(t, 'Tre passi'), tit(t, 'Si restringe il campo, <em>non si riduce il metodo</em>', size=52)],
            [txt(t, 'Ogni passo funziona come funzionerebbe su tutta la scuola: cambia il numero di persone, non il modo in cui '
                    'il programma lavora.')], peso=(55, 45), valign='end'),
        t.one([st], style={'margin_top': 40}),
    ], fondo=CARTA, pad=(120, 120))


def provato(t):
    def lista(voci, icona, fondo, col):
        return t.classic('iconlist', {'items': [{'icon': icona, 'text': v, 'link': '', 'color': ''} for v in voci],
                                      'icon_color': col, 'icon_size': '14', 'text_color': GESSO, 'text_size': '19', 'gap': '14',
                                      'icon_shape': 'circle', 'icon_bg_color': fondo, 'divider': False, 'layout': 'vertical'})
    si = [t.classic('badge', {'text': 'Provato', 'variant': 'solid', 'bg_color': VERDE, 'text_color': GESSO, 'alignment': 'left'}),
          t.one([lista(['Il conto dei punti', 'La raccolta delle risposte', 'Sessioni vere, in aula'], 'check', VERDE, GESSO)],
                anim=None, style={'margin_top': 18})]
    no = [t.classic('badge', {'text': 'Non ancora', 'variant': 'solid', 'bg_color': GESSO, 'text_color': INK, 'alignment': 'left'}),
          t.one([lista(['Un anno completo', 'Una scuola intera', 'Trecento ragazzi insieme'], 'circle-dashed', GESSO_F, GESSO)],
                anim=None, style={'margin_top': 18})]
    return sez(t, [
        due(t, [occ(t, 'Cosa è provato, e cosa no', col=GIALLO),
                tit(t, 'Il conto dei punti e la raccolta delle risposte sono provati su sessioni vere. <em>Non è ancora successo '
                       'che una scuola facesse una giornata intera così</em>, con venticinque ragazzi in classe.', size=34,
                    col=GESSO, acc=GIALLO, tag='h2'),
                mano(t, 'lo diciamo prima, invece di farlo scoprire dopo.', col=VERDE_G, size=30, pad_bottom=0)],
            [foto(t, 'otr-mani-alzate.webp', 'Studentesse che alzano la mano in un\'aula di informatica', ratio='4/3',
                  pos='center 40%', r=24, tilt=True)], peso=(56, 44), valign='center'),
        griglia(t, [si, no], n=2, gap=20, stili=scheda(LAV2, r=24, pad=(32, 32, 32, 32), bordo=GESSO_F)),
    ], fondo=LAV, pad=(120, 120))


def dieci(t):
    hl = t.classic('hoverlist', {
        'items': [{'name': r[0], 'sub': r[2], 'desc': '', 'image': '', 'color': VERDE} for r in RIF],
        'lead_mode': 'swatch', 'swatch_size': 14, 'swatch_shape': 'circle', 'mono_font_family': "'Atkinson Hyperlegible', sans-serif", 'number_color': VERDE, 'number_hover_color': INK, 'name_font_family': 'heading',
        'name_color': INK, 'name_hover_color': VERDE, 'name_size': 22, 'name_uppercase': False, 'sub_color': TENUE,
        'sub_hover_color': INK, 'sub_size': 15, 'sub_uppercase': False, 'row_padding_y': 18, 'hover_indent': 16,
        'row_bg_color': '', 'hover_bg': mix(VERDE, 7), 'line_color': FILO, 'peek': False})
    return sez(t, [
        due(t, [occ(t, 'Dieci riferimenti'), tit(t, 'Dove vengono <em>applicati</em>', size=52),
                txt(t, 'Per ogni metodo qui sotto c\'è scritto che cosa fa dentro OLOtutor.'),
                btn(t, 'Il metodo, per esteso', MET, 'filo')],
            [hl], peso=(36, 64), valign='start'),
    ], fondo=CARTA2, pad=(120, 120))


def scrivici(t):
    campi = [K.campo('text', 'Nome e cognome', 'nome', True, '1-2'),
             K.campo('email', 'Email', 'email', True, '1-2'),
             K.campo('text', 'Scuola o ente', 'scuola', True, '1-2'),
             K.campo('text', 'Classe', 'classe', False, '1-2', segnaposto='per esempio 2A'),
             K.campo('text', 'Il corso che avete in mente', 'corso', True, '1-2'),
             K.campo('text', 'Quante ore potete dedicargli', 'ore', False, '1-2'),
             K.campo('textarea', 'Qualcosa da aggiungere', 'messaggio', False, '1-1')]
    modulo = K.modulo(t, campi, 'Prova con una classe — OLOtutor', 'Chiedi un piano di prova',
                      'Grazie: vi rispondiamo con un piano di prova, non con un preventivo.')
    modulo['settings'].update({'label_transform': 'none', 'label_letter_spacing': '0', 'label_size': '14', 'label_weight': '700',
                               'input_bg': GESSO, 'submit_font_size': '16', 'submit_text_transform': 'none',
                               'submit_letter_spacing': '0', 'submit_radius': 12})
    contatti = t.classic('iconlist', {'items': [{'icon': 'mail', 'text': MAIL, 'link': 'mailto:' + MAIL, 'color': ''},
                                                {'icon': 'file-text', 'text': 'Scarica la nota di sintesi (PDF)', 'link': NOTA,
                                                 'color': ''}],
                                      'icon_color': ONV, 'icon_size': '16', 'text_color': INK, 'text_size': '18', 'gap': '14',
                                      'icon_shape': 'circle', 'icon_bg_color': VERDE, 'divider': False, 'layout': 'vertical'})
    if SITO == 'com':
        # olotutor.com: niente modulo (la pagina privacy del sito è in bozza), la mail e la nota, come prima
        modulo = t.classic('cta-banner', {
            'headline': 'Scrivici', 'headline_accent': '', 'headline_accent_italic': False,
            'subtitle': 'Quale corso avete in mente e quante ore potete dedicargli: la risposta conterrà un piano di prova, '
                        'non un preventivo.', 'cta_text': MAIL, 'cta_url': 'mailto:' + MAIL, 'cta_target': '_self',
            'cta2_text': 'Scarica la nota di sintesi', 'cta2_url': NOTA, 'cta2_target': '_blank', 'cta2_bg': 'transparent',
            'cta2_color': GESSO, 'cta2_border': mix(GESSO, 55), 'bg': {'type': 'solid', 'color': LAV},
            'text_color': GESSO, 'accent_color': GIALLO, 'subtitle_color': GESSO_T, 'cta_bg': GIALLO, 'cta_bg_hover': GESSO,
            'cta_color': INK, 'cta_color_hover': INK, 'cta_radius': radius(12), 'cta_radius_hover': radius(12),
            'cta_size': 17, 'cta_padding': {'top': 17, 'right': 28, 'bottom': 17, 'left': 28},
            'headline_font_family': 'heading', 'headline_size': 52, 'headline_weight': '700', 'subtitle_size': 18,
            'layout': 'stack', 'gap': 24, 'vertical_align': 'center', 'banner_radius': radius(26),
            'banner_radius_hover': radius(26), 'banner_padding': {'top': 56, 'right': 44, 'bottom': 56, 'left': 44}})
        modulo['style'] = {}
    return sez(t, [
        due(t, [occ(t, 'Scrivici'), tit(t, 'Si comincia <em>da una classe</em>.', size=60),
                txt(t, 'Scrivici quale corso avete in mente e quante ore potete dedicargli: la risposta conterrà un piano di '
                       'prova, non un preventivo.', pad=(0, 0, 22, 0)),
                contatti,
                mano(t, 'una classe, un corso, un\'unità di apprendimento.', col=VERDE, size=28, pad_bottom=0)],
            [t.row([t.col([modulo], style=None if SITO == 'com' else scheda(GESSO, r=26, pad=(36, 36, 36, 36), bordo=FILO,
                                                                         ombra=True))], anim='fade')],
            peso=(42, 58), valign='start'),
    ], fondo=CIELO, pad=(120, 120), html_id='scrivici')


def build():
    t = Tpl('otr', 'prova')
    return [
        K.apertura(t, apertura(t)),
        passi(t),
        provato(t),
        dieci(t),
        scrivici(t),
    ]
