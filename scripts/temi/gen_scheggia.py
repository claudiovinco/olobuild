# -*- coding: utf-8 -*-
"""Genera il tema «Scheggia» (id scheggia): una vetreria di Treviso — vetri e serramenti, vetrocamera, box doccia su
misura, specchi, parapetti e vetrate; il vetro rotto sostituito in giornata.

La trovata: «rompilo pure». L'apertura è dietro una lastra di vetro (riflessi, un filo di sfocatura): ogni clic la
colpisce. Al primo una stella di crepe, al secondo le crepe si allargano, al terzo la lastra va in frantumi e i pezzi
cadono; al quarto scende la lastra nuova, pulita: «In giornata». È lo «Stato successivo» dello Slider Pro (vetro =
crepa1, crepa2, rotto, nuovo), coi disegni delle crepe e dei frantumi che ripartono a ogni colpo.

Tema chiaro e freddo: bianco vetro, il petrolio dei vetri spessi, il rosso del bollino «fragile». Titoli in Sora, testo in
Albert Sans, dati in Source Code Pro. Cursore: tagliavetro (graffia e crepa).
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, mix, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import mestieri as X  # noqa: E402
import pezzi_nov as P  # noqa: E402

ID = 'scheggia'
NAME = 'Scheggia'

HEX = {
    'primary': '#0F6E74', 'primary_contrast': '#FFFFFF', 'secondary': '#4F737C', 'secondary_contrast': '#FFFFFF',
    'accent': '#C8462A', 'dark': '#0B1A1E', 'light': '#FFFFFF', 'muted': '#E3ECEE', 'muted_contrast': '#102027',
    'text': '#102027', 'text_muted': '#4A6068', 'background': '#F2F6F7', 'border': '#C9D8DC', 'link': '#0F6E74',
}
HOME, VETRI, VETRERIA, PREVENTIVO = '{{PAGINA:home}}', '{{PAGINA:i-vetri}}', '{{PAGINA:la-vetreria}}', '{{PAGINA:preventivo}}'
MAIL, PHONE, PHONE_TEL = 'vetreria@scheggia.example', '0422 000 360', 'tel:+390422000360'
LAT, LNG = '45.6669', '12.2430'

V_BOTTEGA, V_TAGLIO, V_CERCHIO, V_FINESTRA, V_ROTTO, V_CHIUDE, V_SCHEGGE = (
    'pexels-video-7219271', 'pexels-video-7219278', 'pexels-video-7219266', 'pexels-video-6175387', 'pexels-video-6175379',
    'pexels-video-32078278', 'pexels-video-39623153')
VIDEOS = [V_BOTTEGA, V_TAGLIO, V_CERCHIO, V_FINESTRA, V_ROTTO, V_CHIUDE, V_SCHEGGE]
f = lambda *n: ['pexels-%s.jpg' % x for x in n]
(TAGLIO_1, TAGLIO_2, TAGLIO_3, TAGLIO_4, TAGLIO_5, TAGLIO_6, TAGLIO_7, POSA_FINESTRA, FINESTRA_TELAIO, FINESTRE_STANZA, FINESTRA,
 TRAPANO_FINESTRA, POSA_2, DOCCIA_1, DOCCIA_2, DOCCIA_3, FACCIATA_CURVA, GRATTACIELO, VETRI_ROTTI, CREPE, BUCO, ELENA) = f(
    7219182, 7219180, 7219184, 7219183, 7219181, 7219172, 7219189, 5691516, 5691531, 5691507, 5691504, 5768107, 5691503, 7005476,
    7018243, 27359993, 11861957, 37320179, 9966703, 15205623, 15156848, 6768434)
PHOTOS = [TAGLIO_1, TAGLIO_2, TAGLIO_3, TAGLIO_4, TAGLIO_5, TAGLIO_6, TAGLIO_7, POSA_FINESTRA, FINESTRA_TELAIO, FINESTRE_STANZA,
          FINESTRA, TRAPANO_FINESTRA, POSA_2, DOCCIA_1, DOCCIA_2, DOCCIA_3, FACCIATA_CURVA, GRATTACIELO, VETRI_ROTTI, CREPE, BUCO, ELENA]
D = {k: 'scheggia-%s.svg' % k for k in ('riflessi', 'crepa-1', 'crepa-2', 'frantumi', 'lampo', 'colpo-1', 'colpo-2')}
LOGHI = ['scheggia-logo-chiaro.png', 'scheggia-logo-scuro.png']
MEDIA = set(PHOTOS) | set(D.values()) | set(LOGHI) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=8, peso_titoli='600', corsivo=False, maiuscole_pulsanti=False, tema_chiaro=True,
        accento_su_media='#9EE3E6')
C = K.c
CHIARO = C['chiaro']
FONT_T = 'Sora'
VOCI = [('map-pin', 'Viale della Repubblica 88 · Treviso', PREVENTIVO + '#zone'), ('phone', PHONE, PHONE_TEL),
        ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return X.testata(K, Tpl(ID, 'header'), ID, NAME, 200, scuro=False, telefono=PHONE, tel=PHONE_TEL)


def build_footer():
    return X.piede(K, Tpl(ID, 'footer'), ID, '679/122', 'Scheggia: vetreria a Treviso dal 1968. Vetri e serramenti, box doccia, '
                   'specchi, parapetti. Il vetro rotto lo sostituiamo in giornata.',
                   [('I vetri', VETRI), ('La vetreria', VETRERIA), ('Preventivo', PREVENTIVO), ('Privacy', '/privacy-policy/')],
                   'Il vetro rotto', 'Lunedì — sabato dalle 7:30<br>In giornata se chiamate entro le 11', VOCI,
                   '© 2026 Scheggia srl · Treviso · P.IVA 00000000000', [('instagram', 'https://instagram.com'),
                                                                        ('facebook', 'https://facebook.com')], scuro=False)


# ── L'apertura: rompilo pure ──
TESTI = [('intero', 'Rompilo pure.', 'Tanto lo rifacciamo in giornata. Cliccate sul vetro.'),
         ('crepa1', 'Ancora.', 'Una crepa sola si può ancora lasciare. Noi no: cliccate.'),
         ('crepa2', 'Più forte.', 'Adesso è da cambiare. Un ultimo colpo.'),
         ('rotto', 'Ecco fatto.', 'Pulito, misurato, tagliato. Cliccate: arriva la lastra nuova.'),
         ('nuovo', 'In giornata.', 'Vetri, vetrocamera, docce e specchi a Treviso: se chiamate entro le undici, la sera è a posto.')]


def apertura(t):
    m = K.mob
    st = lambda vale, eff='nessuno', dur=500, rit=0: {'nome': 'vetro', 'vale': vale, 'effetto': eff, 'durata': dur, 'ritardo': rit}
    piena = dict(x=0, y=0, w=100, h=100)
    piena_m = {'x': 0, 'y': 0, 'width': 100, 'height': 100}

    def disegno(lid, nome, stato, **kw):
        return K.livello(lid, tipo='image', imageSrc=K.img(D[nome]), imageAlt='', objectFit='cover', entra='fadeIn', ritardo=0,
                         durata=200, stato=stato, responsive={'mobile': dict(piena_m)}, **dict(piena, **kw))
    def colpo(lid, nome, vale, stato):
        doppia = {'x': -50, 'y': -50, 'width': 200, 'height': 200}
        return K.livello(lid, tipo='image', imageSrc=K.img(D[nome]), imageAlt='', objectFit='cover', entra='fadeIn', ritardo=0,
                         durata=200, stato=stato, x=-50, y=-50, w=200, h=200, responsive={'mobile': dict(doppia)},
                         customCSS='translate:var(--mps-clic-x-%s,0px) var(--mps-clic-y-%s,0px);pointer-events:none;' % (vale, vale))
    lastra = dict(tipo='shape', bgColor='rgba(225,243,247,.10)', entra='fadeIn', ritardo=0, durata=600,
                  shapeGradient={'angle': 135, 'from': 'rgba(255,255,255,.20)', 'to': 'rgba(255,255,255,.03)', 'fromStop': 0},
                  customCSS='box-shadow:inset 0 0 0 16px rgba(225,243,247,.10),inset 0 0 160px rgba(255,255,255,.08);',
                  responsive={'mobile': dict(piena_m)}, **piena)
    L = K.veli('sc-velo', 'basso', 42, 66, colore=C['nero'], forza=90, mobile_da=52) + [
        K.livello('sc-lastra', stato=st('!rotto,nuovo'), **lastra),
        disegno('sc-riflessi', 'riflessi', st('!rotto,nuovo')),
        # le crepe nascono dove si colpisce (15 nov 2026): ogni colpo ha la sua stella, su una tela doppia centrata che
        # si sposta nel punto del clic (--mps-clic-x/y-<stato> dello Slider Pro); la prima resta quando arriva la seconda
        colpo('sc-crepa1', 'colpo-1', 'crepa1', st('crepa1,crepa2')),
        colpo('sc-crepa2', 'colpo-2', 'crepa2', st('crepa2')),
        disegno('sc-lampo1', 'lampo', st('crepa1')),
        disegno('sc-lampo2', 'lampo', st('crepa2')),
        disegno('sc-lampo3', 'lampo', st('rotto')),
        disegno('sc-frantumi', 'frantumi', st('rotto')),
        K.livello('sc-nuova', stato=st('nuovo', 'giu', 1100), **lastra),
        disegno('sc-riflessi2', 'riflessi', st('nuovo', 'giu', 1100)),
        # dove si colpisce: tutto il vetro (trasparente, sopra i disegni e sotto i testi)
        K.livello('sc-colpo', tipo='shape', bgColor='transparent', entra='fadeIn', ritardo=0, durata=200, stato=st('!nuovo'),
                  action={'type': 'cycleState', 'target': 'vetro', 'values': 'crepa1,crepa2,rotto,nuovo', 'loop': False,
                          'label': 'Colpisci il vetro'}, customCSS='--mps-attivo:transparent;cursor:crosshair;',
                  responsive={'mobile': dict(piena_m)}, **piena),
        K.livello('sc-o', contenuto='Scheggia · vetreria · Treviso, dal 1968', x=6, y=56, w=50, size=13, peso='500',
                  colore=mix(CHIARO, 86), letterSpacing=3, textTransform='uppercase', fontFamily='Source Code Pro', ritardo=200,
                  responsive={'mobile': {'fontSize': m(10), 'x': 6, 'y': 62, 'width': 88}}),
    ]
    for k, (vale, titolo, testo) in enumerate(TESTI):
        L += [
            K.livello('sc-t-' + vale, contenuto=titolo, tag='h1' if k == 0 else 'h2', x=6, y=60, w=62, size=104, peso='700',
                      colore=CHIARO, fontFamily=FONT_T, lineHeight=1.0, entra='maskFromBottom' if k == 0 else 'fadeIn',
                      ritardo=300 if k == 0 else 0, durata=800, stato=st(vale, 'su' if k else 'fade', 450, 120 if k else 0),
                      responsive={'mobile': {'fontSize': m(36), 'x': 6, 'y': 65, 'width': 88}}),
            K.livello('sc-s-' + vale, contenuto=testo, x=6, y=74, w=40, size=18, peso='400', colore=mix(CHIARO, 92), lineHeight=1.5,
                      ritardo=600 if k == 0 else 0, stato=st(vale, 'su' if k else 'fade', 450, 220 if k else 0),
                      responsive={'mobile': {'fontSize': m(14), 'x': 6, 'y': 70.5, 'width': 86}}),
        ]
    L += [
        K.livello('sc-b1', tipo='button', contenuto='Il vetro rotto', x=60, y=74, w='auto', size=15, peso='600', colore='#102027',
                  bgColor='#9EE3E6', padding=18, borderRadius=8, buttonUrl=PHONE_TEL, entra='fadeIn', ritardo=0,
                  stato=st('nuovo', 'su', 500, 500), hover={'bgColor': '#FFFFFF', 'duration': 250},
                  responsive={'mobile': {'x': 6, 'y': 80, 'fontSize': m(13)}}),
        K.livello('sc-b2', tipo='button', contenuto='Un preventivo', x=74, y=74, w='auto', size=15, peso='600', colore=CHIARO,
                  bgColor='transparent', padding=18, borderRadius=8, borderWidth=1, borderColor=mix(CHIARO, 65), buttonUrl=PREVENTIVO,
                  entra='fadeIn', ritardo=0, stato=st('nuovo', 'su', 500, 600), hover={'bgColor': mix(CHIARO, 16), 'duration': 250},
                  responsive={'mobile': {'x': 50, 'y': 80, 'fontSize': m(13)}}),
        K.livello('sc-r', tipo='button', contenuto='Rompilo di nuovo', x=86, y=88, w='auto', size=12, peso='500', colore=mix(CHIARO, 78),
                  bgColor='transparent', padding=8, borderRadius=8, buttonUrl='#', entra='fadeIn', ritardo=0, stato=st('nuovo', 'fade', 500, 1400),
                  action={'type': 'setState', 'target': 'vetro', 'value': 'intero', 'label': 'Rimetti il vetro da rompere'},
                  hover={'color': CHIARO, 'duration': 200}, responsive={'mobile': {'x': 6, 'y': 90, 'fontSize': m(11)}}),
    ]
    sl = [K.diapositiva('sc-1', K.sfondo_video(V_BOTTEGA, overlay=0.2, colore=C['nero']), L, durata=600000)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', showArrows=False, showDots=False,
                       showTabs=False, showProgressBar=False, autoplay=False, parallax=False, stati='vetro=intero',
                       heightMobile={'mode': 'px', 'value': 1400})


def frantumi(t, foto, preset='shards'):
    """La foto in pezzi che si ricompongono scorrendo (Shattered image)."""
    return t.classic('shatteredimage', {'image_url': K.img(foto), 'preset': preset, 'gap': 4, 'height': '460px',
                                        'image_ratio': '16/9', 'image_position': 'center center', 'gap_color': C['fondo'],
                                        'scroll_reveal': True, 'scroll_reveal_stagger': 140, 'scroll_reveal_duration': 700,
                                        'border_radius_outer': {'tl': 8, 'tr': 8, 'br': 8, 'bl': 8}})


def schede(t, voci):
    """Le schede coi numeri e le icone, coi titoli più piccoli: «Vetrocamera» a 56 px usciva dalla scheda."""
    s = P.schede_icone(K, t, voci, fondo=C['sala'])
    s['settings']['title_size'] = 38
    return s


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Scheggia · Treviso, dal 1968', ['Il vetro', 'si taglia sul posto.'], size=72, layout='stack',
                                size_mobile=42)], wm='3-5', ws='1-1'),
               t.col([K.text(t, 'Siamo una vetreria con il furgone attrezzato: misuriamo, tagliamo e posiamo, anche sullo stesso '
                                'giro. Vetri di finestre e vetrine, vetrocamera che isolano, box doccia su misura, specchi e '
                                'parapetti di sicurezza.', size=17),
                      K.button(t, 'I vetri', VETRI, pad_top=18)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=72, valign='end'),
        t.one([X.fiducia(K, t, [('clock', 'Vetro rotto <b>in giornata</b>'), ('ruler', 'Misure <b>al millimetro</b>'),
                                ('shield-check', 'Vetri <b>di sicurezza</b>'), ('thermometer', 'Vetrocamera <b>che isolano</b>')])],
              style={'margin_top': 56}),
    ], padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 100}))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'In frantumi', ['Scorrete:', 'si rimette insieme.'], size=56, layout='stack', size_mobile=36),
                      K.text(t, 'Un vetro rotto non si incolla: si cambia. Ma la finestra di prima, quella sì, ce la ricordiamo.')],
                     wm='1-3', ws='1-1'),
               t.col([frantumi(t, FINESTRE_STANZA)], wm='2-3', ws='1-1')], layout='custom', custom='34,66', gap=56, valign='center'),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'Cosa facciamo', ['Dalla finestra', 'alla doccia.'], size=60)]),
        t.one([schede(t, [
            ('01', 'in giornata', 'Vetri rotti', '', 'Finestre, porte, vetrine: chiamate entro le undici, la sera è fatto.', 'clock'),
            ('02', 'isolano', 'Vetrocamera', '', 'Doppi e tripli, basso emissivi: d’inverno meno freddo, d’estate meno caldo.', 'thermometer'),
            ('03', 'su misura', 'Box doccia', '', 'Il vetro temperato tagliato sul vostro bagno, anche nei sottotetti.', 'bath'),
            ('04', 'sicurezza', 'Parapetti', '', 'Vetri stratificati per scale, terrazzi e soppalchi.', 'shield-check')])]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(K.video_frase(t, V_TAGLIO, 'Il taglio', 'Una riga, <em>un colpo secco.</em>',
                             'Il diamante incide, la mano spezza: un vetro si taglia così da cent’anni, e il bordo si leviga dopo.', overlay=18))
    out.append(X.prima_dopo(K, t, [
        (BUCO, FINESTRA, 'Prima', 'Dopo', 'La finestra di via Roma', 'Un pallone, alle dieci di mattina. Alle cinque il vetro nuovo, '
         'vetrocamera.'),
        (CREPE, DOCCIA_1, 'Prima', 'Dopo', 'La doccia di Villorba', 'Il vecchio box crepato, il nuovo in vetro temperato da 8 mm.')],
        'Prima e dopo', ['Trascinate:', 'il vetro nuovo.']))
    # 15 nov 2026: il diario del furgone, gli ultimi lavori uno per riga (inventati, senza date: non invecchiano)
    out.append(t.section([
        t.one([K.sechead(t, 'Gli ultimi lavori', ['Dove è passato', 'il furgone.'], size=56)]),
        t.one([P.righe(K, t, [
            ('01', 'La vetrina del bar in piazza dei Signori', 'Vetro antisfondamento · Treviso centro', 'in giornata'),
            ('02', 'Le finestre di una casa colonica', 'Vetrocamera basso emissivo · Quinto di Treviso', 'due giorni'),
            ('03', 'Il box doccia nel sottotetto', 'Temperato da 8 mm, sulla sagoma · Preganziol', 'un giorno'),
            ('04', 'Lo specchio della palestra', 'Sei metri di specchio a parete · Villorba', 'mezza giornata'),
            ('05', 'Il parapetto del terrazzo', 'Stratificato 6+6 · Silea', 'un giorno')], PREVENTIVO, titolo=28)]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(X.recensioni(K, t, [
        ('Il vetro della vetrina rotto il sabato mattina: il sabato sera era a posto. Il lunedì ho aperto come sempre.', 'Nadia', 'Treviso centro'),
        ('Il box doccia nel sottotetto, con la parete storta: l’hanno tagliato sulla sagoma, perfetto.', 'Michele', 'Preganziol'),
        ('Abbiamo cambiato tutti i vetri con la vetrocamera: la sala adesso si scalda in metà tempo.', 'Laura e Giorgio', 'Silea')],
        'Chi ci ha chiamato', ['Tre vetri,', 'nessuna scheggia.'], fondo=False))
    return out


def build_vetri():
    t = Tpl(ID, 'i-vetri')
    out = [K.intro(t, 'I vetri', ['Finestre, docce,', 'specchi, parapetti.'],
                   'Il vetro giusto per ogni posto: temperato, stratificato, vetrocamera, satinato. E quanto costa.',
                   bg=K.video_bg(V_FINESTRA, overlay=24), size=92)]
    out.append(t.section([t.one([P.pila(K, t, [
        ('Finestre', 'La vetrocamera', 'che isola', 'Doppio o triplo vetro basso emissivo, montato nel vostro telaio.', FINESTRA_TELAIO),
        ('Docce', 'Il box', 'su misura', 'Vetro temperato da 6 o 8 mm, trasparente o satinato, coi profili in nero o in acciaio.', DOCCIA_2),
        ('Specchi', 'Lo specchio', 'a parete', 'Tagliato su misura, col filo lucido, anche retroilluminato.', DOCCIA_3),
        ('Sicurezza', 'Il parapetto', 'stratificato', 'Due lastre e una pellicola: se si rompe, resta al suo posto.', GRATTACIELO)],
        altezza=420)])], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Il listino', ['Il prezzo', 'al metro quadro.'], size=56, layout='stack', size_mobile=36),
                      K.text(t, 'IVA compresa, posa inclusa a Treviso e comuni vicini. Il preventivo esatto dopo le misure, gratis.')],
                     wm='1-3', ws='1-1'),
               t.col([P.listino(K, t, [
                   ('Vetro semplice 4 mm', 'Per finestre interne e quadri', '48 €/m²', '', ''),
                   ('Vetrocamera 4-16-4', 'Basso emissivo, col gas argon', '135 €/m²', '', 'il più chiesto'),
                   ('Stratificato 3+3', 'Di sicurezza, per porte e parapetti', '110 €/m²', '', ''),
                   ('Box doccia', 'Su misura, vetro temperato 8 mm', 'da 690 €', '', ''),
                   ('Uscita per vetro rotto', 'In giornata, la messa in sicurezza compresa', '60 €', '', '')], immagini=False)],
                     wm='2-3', ws='1-1')],
              layout='custom', custom='34,66', gap=64, valign='start'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(t.section([t.one([X.certificati(K, t, [
        ('UNI EN 12150', 'Vetro temperato', 'Box doccia, porte e arredi in vetro temperato di sicurezza', 'shield-check'),
        ('UNI EN 14449', 'Stratificato', 'Parapetti e vetrate stratificate di sicurezza', 'layers'),
        ('UNI EN 1279', 'Vetrocamera', 'Vetrate isolanti con la marcatura CE', 'thermometer')])])],
        padding='large', width='large', style={'padding_top': 100, 'padding_bottom': 100}))
    out.append(K.domande(t, 'Sul vetro,', 'le domande di tutti.', [
        ('Si rompe il vetro della finestra: cosa faccio?', 'Non toccate le schegge e chiamateci: veniamo a mettere in sicurezza e a '
         'misurare, e se chiamate entro le undici il vetro nuovo arriva la sera.'),
        ('La vetrocamera si può mettere nel telaio vecchio?', 'Quasi sempre sì: allarghiamo la battuta e cambiamo i fermavetri.'),
        ('Temperato o stratificato?', 'Il temperato si rompe in pezzetti che non tagliano (docce, porte); lo stratificato resta in piedi '
         '(parapetti, vetrine).'),
    ], 'sc-faq'))
    return out


def build_vetreria():
    t = Tpl(ID, 'la-vetreria')
    out = [K.intro(t, 'La vetreria', ['Dal 1968,', 'col diamante in tasca.'],
                   'Il nonno tagliava vetri per le finestre di Treviso ricostruita. Il tagliavetro è ancora quello; il resto è cambiato.',
                   bg=K.video_bg(V_CERCHIO, overlay=26), size=92)]
    out.append(t.section([
        t.one([K.sechead(t, 'Come lavoriamo', ['Misurare,', 'tagliare, posare.'], size=56)]),
        t.one([P.tappe(K, t, [
            ('1', 'le misure', POSA_2, 'Misurare', 'Al millimetro', 'sul posto', 'Col laser e la sagoma, anche per le pareti storte.', '1', 'ora'),
            ('2', 'il taglio', TAGLIO_6, 'Tagliare', 'Il diamante', 'e la mola', 'In bottega o sul furgone, il filo levigato a mano.', '1', 'giorno'),
            ('3', 'la posa', POSA_FINESTRA, 'Posare', 'Il vetro', 'al suo posto', 'Siliconi, fermavetri, e le impronte pulite prima di andare.',
             '2', 'ore')], icona='ruler')]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'Il banco', ['Il vetro', 'da vicino.'], size=56)]),
        P.fila_foto(K, t, [(TAGLIO_1, 'Il taglio in cerchio'), (TAGLIO_2, 'La riga col diamante'), (TAGLIO_3, 'La lastra sul banco'),
                           (TAGLIO_7, 'Il tagliavetro')], ratio='3/4'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.row([t.col([K.image(t, ELENA, 'Elena al banco delle vetrate, con le maniche rosse', ratio='4/5', position='center 30%')],
                     wm='2-5', ws='1-1'),
               t.col([P.citazione(K, t, 'Il vetro non perdona: se sbagli il colpo, ricominci da capo. Per questo lo tagliamo due volte, '
                                        'una con la testa e una col diamante.', 'Elena Zanatta, vetraia', size='38')], wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=72, valign='center'),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(K.pannello(t, V_CHIUDE, 'Le finestre', 'Più calde, <em>senza cambiare il telaio.</em>',
                          'La vetrocamera nel vostro telaio di legno: si scalda meno e si sente meno la strada.',
                          'Un preventivo', PREVENTIVO + '#scrivi', video=True))
    return out


def build_preventivo():
    t = Tpl(ID, 'preventivo')
    out = [K.intro(t, 'Il preventivo', ['Il vetro giusto,', 'il prezzo prima.'],
                   'Componete il lavoro per un’idea del prezzo; poi veniamo a misurare, gratis.',
                   bg=K.video_bg(V_SCHEGGE, overlay=30), size=92)]
    conf = X.preventivo(K, t, [
        ('lavoro', 'Il lavoro', 'uno', True, 'Indicativo, posa compresa a Treviso e comuni vicini.'),
        ('vetro', 'Il vetro', 'uno', True, ''),
        ('extra', 'In più', 'piu', False, '')], [
        ('finestre', 'lavoro', 'Vetri di 4 finestre', 'Circa 6 m² in tutto.', 290, '', True),
        ('doccia', 'lavoro', 'Box doccia', 'Su misura, una porta e un fisso.', 690, 'il più chiesto', False),
        ('parapetto', 'lavoro', 'Parapetto di 4 m', 'Con i morsetti in acciaio.', 1850, '', False),
        ('semplice', 'vetro', 'Semplice o temperato', '', 0, '', True),
        ('camera', 'vetro', 'Vetrocamera basso emissivo', '', 520, '', False),
        ('strati', 'vetro', 'Stratificato di sicurezza', '', 380, '', False),
        ('satinato', 'extra', 'Satinato', 'Per bagni e porte.', 90, '', False),
        ('anticalcare', 'extra', 'Trattamento anticalcare', 'Per le docce.', 70, '', False),
        ('smaltimento', 'extra', 'Smaltimento del vecchio', '', 30, '', False)],
        'Il vostro vetro', PREVENTIVO + '?lavoro={configurazione}#scrivi', 'IVA compresa · misure gratis')
    out.append(X.sezione_preventivo(K, t, 'Componete il lavoro', ['Scegliete,', 'il totale si fa da sé.'], '', conf))
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('tel', 'Telefono', 'telefono', True),
             K.campo('email', 'Email', 'email'), K.campo('text', 'Comune', 'comune'),
             K.campo('text', 'Il lavoro', 'lavoro', larghezza='1-1'),
             K.campo('textarea', 'Misure e foto, se le avete', 'messaggio', larghezza='1-1')]
    out.append(K.sezione_modulo(t, 'scrivi', 'Scriveteci', ['Veniamo a misurare,', 'gratis.'],
                                'Per il vetro rotto chiamate: il modulo lo leggiamo entro sera.', VOCI, campi, 'Scheggia — preventivo',
                                'Invia', 'Grazie! Vi richiamiamo entro un giorno.'))
    out.append(X.zone(K, t, 'Dove arriviamo', ['Treviso', 'e la Marca.'], 'Per i vetri rotti, in giornata se chiamate entro le 11.',
                      [('Treviso', 'in giornata'), ('Villorba, Silea, Preganziol', 'in giornata'), ('Castelfranco', 'in giornata'),
                       ('Conegliano', 'il giorno dopo'), ('Mestre', 'il giorno dopo'), ('Montebelluna', 'in giornata')],
                      LAT, LNG, 'Scheggia · la vetreria', False, HEX['primary']))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · vetro rotto', color=K.acc_media, align='center'),
                       K.headline(t, 'Questa pagina <em>è in frantumi.</em>', size=84, tag='h1', align='center', pad_bottom=20,
                                  color=CHIARO, accento=K.acc_media),
                       K.text(t, 'Raccogliamo i pezzi e torniamo dall’inizio.', size=17, align='center', color=mix(CHIARO, 90)),
                       K.button(t, 'Torna alla home', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_ROTTO, overlay=45), padding='xlarge', style={'padding_top': 220, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, X.CATEGORIA, 'Rompilo pure',
    ['artigiani', 'vetraio', 'vetreria', 'serramenti', 'box doccia', 'chiaro', 'slider pro', 'interattivo'],
    'Una vetreria a Treviso: l’apertura è dietro una lastra di vetro e ogni clic la colpisce; una crepa, due, poi i frantumi '
    'che cadono, e scende la lastra nuova: «In giornata». Poi la foto in pezzi che si ricompone scorrendo, i vetri col listino, '
    'le norme, i lavori prima e dopo e il preventivo da comporre.',
    [('I vetri', VETRI), ('La vetreria', VETRERIA), ('Preventivo', PREVENTIVO)],
    [('home', 'Home', None), ('i-vetri', 'I vetri', 'i-vetri'), ('la-vetreria', 'La vetreria', 'la-vetreria'),
     ('preventivo', 'Preventivo', 'preventivo')],
    HEX, '\'Albert Sans\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Sora\', system-ui, sans-serif',
    '\'Source Code Pro\', ui-monospace, Menlo, monospace', ['Albert Sans', 'Sora', 'Source Code Pro'], corsivo_di=[],
    peso_titoli='600', raggio=8, grana=0, titoli_line='1.0', titoli_spazio='-0.03em')
THEME['cursor']['style'] = 'diamante'
THEME['cursor']['ring_color'] = '#3E8C95'
THEME['cursor']['dot_color'] = HEX['primary']


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'i-vetri.json': build_vetri(), 'la-vetreria.json': build_vetreria(), 'preventivo.json': build_preventivo(),
                        '404.json': build_404()}, MEDIA)


if __name__ == '__main__':
    main()
