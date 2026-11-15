# -*- coding: utf-8 -*-
"""Nomi e foto delle persone che non tornavano (verifica del 13 nov 2026 su 291 coppie nome-foto in 98 temi: «foto di
uomo ma didascalia che indica nome femminile»). Corregge i JSON dei temi (sulla struttura: la voce con quel nome) e,
dove c'è, il generatore, così una rigenerazione non riporta l'errore. Si può rilanciare: la seconda volta non cambia
niente. uso: python patch_persone_2026-11-13.py
"""
import glob
import json
import os
import sys

sys.stdout.reconfigure(encoding='utf-8')
QUI = os.path.dirname(os.path.abspath(__file__))
TEMI = os.path.normpath(os.path.join(QUI, '..', '..', 'assets', 'data', 'themes'))
LIB = '{{OLOBUILD_LIBRARY}}/media/'

# tema, nome attuale → campi nuovi della voce (name, role, bio, foto)
VOCI = [
    ('a-cipolla', 'Paolo Zanella', {'name': 'Paola Zanella', 'role': 'Sarta'}),
    ('cerniera', 'Dario Benvenuti', {'name': 'Daria Benvenuti', 'role': 'Fondatrice',
                                     'bio': ('Pellettiere, figlio di pellettieri', 'Pellettiera, figlia di pellettieri')}),
    ('fuori-registro', 'Giulia Testoni', {'name': 'Giulio Testoni'}),
    ('gomitolo', 'Tobia Garella', {'name': 'Teresa Garella'}),
    ('lastra', 'Paola Gribaudo', {'name': 'Paolo Gribaudo', 'role': 'Fondatore',
                                  'bio': ('Ingegnera dei materiali', 'Ingegnere dei materiali')}),
    ('lastra', 'Irene Bosio', {'name': 'Ivano Bosio'}),
    ('luce-rossa', 'Elia Brandolin', {'name': 'Elisa Brandolin'}),
    ('madia', 'Silvia, il vino', {'name': 'Silvio, il vino'}),
    ('rovescio', 'Samir Haddad', {'name': 'Samira Haddad', 'role': 'Sarta'}),
    ('ventaglio', 'Giuseppe Ferrante', {'name': 'Giuseppina Ferrante'}),
    ('raspa', 'Tommaso Rigon', {'name': 'Tamara Rigon'}),
    ('raspa', 'Aiko Tanaka', {'name': 'Akio Tanaka'}),
    ('spaiato', 'Ahmed Saleh', {'name': 'Amira Saleh'}),
    # foto che cambiano
    ('elastan', 'Chiara Bassi', {'foto': 'pexels-30246193.jpg'}),
    ('elastan', 'Leon Weber', {'foto': 'pexels-20615473.jpg'}),
    ('elastan', 'Nia Johnson', {'foto': 'pexels-17428611.jpg', 'bio': ('Ballerina: prova i capi nella spaccata.',
                                                                         'Cestista: prova i capi in partita.')}),
    ('morsetto', 'Sara Bonetti', {'foto': 'pexels-8487400.jpg'}),
    ('sirena', 'Samuele Tonon', {'foto': 'pexels-8543365.jpg'}),
    ('fenice-calcio', 'Daniela Ferri', {'foto': 'pexels-14171771.jpg'}),
    ('fenice-calcio', 'Giulia Rinaldi', {'foto': 'pexels-33607365.jpg'}),
    ('bagnasciuga', 'Marta e Luca', {'foto': 'pexels-36729524.jpg'}),
]
# testi fuori dalle voci (stringhe esatte nei JSON del tema)
TESTI = [('madia', 'Silvia fa la Malvasia', 'Silvio fa la Malvasia')]
# i generatori: (file, vecchio, nuovo)
GEN = [
    ('gen_a_cipolla.py', "'Paolo Zanella', 'Sarto'", "'Paola Zanella', 'Sarta'"),
    ('gen_cerniera.py', "'Dario Benvenuti', 'Fondatore', 'Pellettiere, figlio di pellettieri",
     "'Daria Benvenuti', 'Fondatrice', 'Pellettiera, figlia di pellettieri"),
    ('gen_fuori_registro.py', "'Giulia Testoni'", "'Giulio Testoni'"),
    ('gen_gomitolo.py', "'Tobia Garella'", "'Teresa Garella'"),
    ('gen_lastra.py', "'Paola Gribaudo', 'Fondatrice', 'Ingegnera dei materiali",
     "'Paolo Gribaudo', 'Fondatore', 'Ingegnere dei materiali"),
    ('gen_lastra.py', "'Irene Bosio'", "'Ivano Bosio'"),
    ('gen_luce_rossa.py', "'Elia Brandolin'", "'Elisa Brandolin'"),
    ('gen_madia.py', "'Silvia, il vino'", "'Silvio, il vino'"),
    ('gen_madia.py', 'Silvia fa la Malvasia', 'Silvio fa la Malvasia'),
    ('gen_rovescio.py', "'Samir Haddad', 'Sarto'", "'Samira Haddad', 'Sarta'"),
    ('gen_ventaglio.py', "'Giuseppe Ferrante'", "'Giuseppina Ferrante'"),
    ('gen_raspa.py', "'Tommaso Rigon'", "'Tamara Rigon'"),
    ('gen_raspa.py', "'Aiko Tanaka'", "'Akio Tanaka'"),
    ('gen_spaiato.py', "'Ahmed Saleh'", "'Amira Saleh'"),
    ('gen_elastan.py', "(ATLE[0], 'Chiara Bassi'", "(ATLE[1], 'Chiara Bassi'"),
    ('gen_elastan.py', "(ATLE[2], 'Leon Weber'", "(ATLE[0], 'Leon Weber'"),
    ('gen_elastan.py', "(ATLE[4], 'Nia Johnson', 'Prove', 'Ballerina: prova i capi nella spaccata.'",
     "(ATLE[3], 'Nia Johnson', 'Prove', 'Cestista: prova i capi in partita.'"),
    ('gen_morsetto.py', "(DONNA_BLU, 'Sara Bonetti'", "('pexels-8487400.jpg', 'Sara Bonetti'"),
    ('gen_sirena.py', "('pexels-6519852.jpg', 'Samuele Tonon'", "('pexels-8543365.jpg', 'Samuele Tonon'"),
    ('gen_bagnasciuga.py', "la frutta dal mercato.', 'pexels-25391594.jpg'),", "la frutta dal mercato.', 'pexels-36729524.jpg'),"),
]
# le foto nuove entrano nell'insieme dei media dei generatori (write_theme controlla che ogni file sia fra i media)
MEDIA_GEN = {'gen_morsetto.py': 'pexels-8487400.jpg', 'gen_sirena.py': 'pexels-8543365.jpg',
             'gen_bagnasciuga.py': 'pexels-36729524.jpg'}
CAMPI_FOTO = ('image', 'photo', 'avatar', 'foto', 'image_url', 'media_image', 'avatar_url')
CAMPI_NOME = ('name', 'title')


def correggi_voce(o, nome, nuovo, conta):
    if isinstance(o, dict):
        if any(o.get(k) == nome for k in CAMPI_NOME):
            for k in CAMPI_NOME:
                if o.get(k) == nome and 'name' in nuovo:
                    o[k] = nuovo['name']
            if 'role' in nuovo and 'role' in o:
                o['role'] = nuovo['role']
            if 'bio' in nuovo:
                for k in ('bio', 'description', 'text'):
                    if isinstance(o.get(k), str) and nuovo['bio'][0] in o[k]:
                        o[k] = o[k].replace(nuovo['bio'][0], nuovo['bio'][1])
            if 'foto' in nuovo:
                for k in CAMPI_FOTO:
                    if isinstance(o.get(k), str) and '/media/' in o[k]:
                        o[k] = LIB + nuovo['foto']
            conta[0] += 1
        for v in o.values():
            correggi_voce(v, nome, nuovo, conta)
    elif isinstance(o, list):
        for v in o:
            correggi_voce(v, nome, nuovo, conta)


def main():
    for tema, nome, nuovo in VOCI:
        n = [0]
        for f in sorted(glob.glob(os.path.join(TEMI, tema, '*.json'))):
            if f.endswith('theme.json'):
                continue
            prima = open(f, encoding='utf-8').read()
            j = json.loads(prima)
            correggi_voce(j, nome, nuovo, n)
            dopo = json.dumps(j, ensure_ascii=False, separators=(',', ':'))
            for t, a, b in TESTI:
                if t == tema:
                    dopo = dopo.replace(a, b)
            if dopo != prima:
                open(f, 'w', encoding='utf-8', newline='\n').write(dopo)
        print('%-14s %-18s voci corrette: %d' % (tema, nome, n[0]))
    for f, a, b in GEN:
        p = os.path.join(QUI, f)
        if not os.path.exists(p):
            print('  (manca il generatore %s)' % f)
            continue
        s = open(p, encoding='utf-8').read()
        if a in s:
            s = s.replace(a, b)
            if f in MEDIA_GEN and "MEDIA |= {'%s'}" % MEDIA_GEN[f] not in s:
                i = s.index('\nMEDIA = ')
                j = s.index('\n', i + 1)
                s = s[:j + 1] + "MEDIA |= {'%s'}  # foto sostituita il 13 nov 2026 (patch_persone_2026-11-13.py)\n" % MEDIA_GEN[f] + s[j + 1:]
            open(p, 'w', encoding='utf-8', newline='\n').write(s)
            print('  generatore %s: corretto' % f)
        elif b in s:
            print('  generatore %s: già corretto' % f)
        else:
            print('  generatore %s: NON trovato «%s»' % (f, a[:50]))


if __name__ == '__main__':
    main()
