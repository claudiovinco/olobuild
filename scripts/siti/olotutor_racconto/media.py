# -*- coding: utf-8 -*-
"""Le immagini di olotutor.com per il tema «OLOtutor — Il racconto» (7 nov 2026).

Prese tutte dalla Libreria media e dalla cartella uploads/vetrina di olotutor.com (sola lettura) e convertite per la
libreria remota di OLObuild (olotheme.com/olobuild-library/media/) col prefisso «otr-»: foto e schermate in webp (lato
lungo al massimo 1600 px), loghi in png, grafiche in svg, la nota di sintesi in pdf.

Lasciate fuori, di proposito: i ritratti 12, 20, 26 e 55 (selfie di persone vere usati come avatar di prova, e una foto
Getty di un personaggio pubblico, che non si può riusare) e il ritratto in bianco e nero 1/8 (una persona vera: va
messa solo se lo chiede chi c'è ritratto).

Uso: python media.py <cartella delle immagini scaricate> → scrive in ./media/ (non serve rilanciarlo se c'è già).
"""
import os
import shutil
import sys

from PIL import Image

QUI = os.path.dirname(os.path.abspath(__file__))
OUT = os.path.join(QUI, 'media')

# sorgente (nome come scaricato: percorso di uploads con «_» al posto di «/») → nome nella libreria
FOTO = {
    # schermate della piattaforma (classe di prova con nomi inventati)
    '2026_11_olotutor-studente-home.webp': 'otr-studente-home.webp',
    '2026_11_olotutor-studente-ripasso.webp': 'otr-studente-ripasso.webp',
    '2026_11_olotutor-studente-caselle.webp': 'otr-studente-caselle.webp',
    '2026_11_olotutor-studente-aula.webp': 'otr-studente-aula.webp',
    '2026_11_olotutor-docente-regia.webp': 'otr-docente-regia.webp',
    '2026_11_olotutor-docente-giri.webp': 'otr-docente-giri.webp',
    '2026_11_olotutor-docente-certezza.webp': 'otr-docente-certezza.webp',
    '2026_11_olotutor-docente-registro.webp': 'otr-docente-registro.webp',
    '2026_11_olotutor-docente-strumenti.webp': 'otr-docente-strumenti.webp',
    '2026_11_olotutor-scuola-medie.webp': 'otr-scuola-medie.webp',
    '2026_11_olotutor-scuola-registro.webp': 'otr-scuola-registro.webp',
    '2026_11_olotutor-scuola-come-va.webp': 'otr-scuola-come-va.webp',
    '2026_09_classe-studente-home-classe-comunicazione.jpg': 'otr-studente-classe.webp',
    '2026_09_studente-stat2.jpg': 'otr-studente-statistiche.webp',
    '2026_09_carte-ripetizione.jpg': 'otr-carta-ripasso.webp',
    '2026_09_portale.png': 'otr-portale.webp',
    '2026_09_palazzo.jpg': 'otr-piantine.webp',
    # giochi e strumenti d'aula
    '2026_09_gioco-showdown.jpg': 'otr-gioco-showdown.webp',
    '2026_09_showdown-prof.jpg': 'otr-regia-showdown.webp',
    '2026_09_ruota-gioco-alunno.jpg': 'otr-gioco-ruota.webp',
    '2026_09_ruota-gioco-prof.jpg': 'otr-regia-ruota.webp',
    '2026_09_memory.jpg': 'otr-gioco-memory.webp',
    '2026_09_memory-test.jpg': 'otr-gioco-memory-test.webp',
    '2026_09_impiccato.jpg': 'otr-gioco-impiccato.webp',
    '2026_09_cruciverba.jpg': 'otr-gioco-cruciverba.webp',
    '2026_09_type-race.jpg': 'otr-gioco-type-race.webp',
    '2026_09_Linea-del-tempo.jpg': 'otr-gioco-linea-del-tempo.webp',
    '2026_09_milionario-studente.png': 'otr-gioco-milionario.webp',
    '2026_09_battleroyale.jpg': 'otr-gioco-battle-royale.webp',
    '2026_09_caccia-tesoro.jpg': 'otr-gioco-caccia-al-tesoro.webp',
    '2026_09_conquista.png': 'otr-gioco-conquista.webp',
    '2026_09_eliminazione.png': 'otr-gioco-eliminazione.webp',
    '2026_09_sfida-fotografica.jpg': 'otr-gioco-sfida-fotografica.webp',
    '2026_09_tabellone-caselle.jpg': 'otr-gioco-tabellone.webp',
    # foto d'aula e di studio
    '2026_09_aula-1.png': 'otr-aula-1.webp',
    '2026_09_aula-2.png': 'otr-aula-2.webp',
    '2026_09_aula-3.png': 'otr-aula-3.webp',
    '2026_09_classe-5.jpg': 'otr-classe-prof.webp',
    '2026_09_classe-6.jpg': 'otr-classe-lezione.webp',
    '2026_09_foto01.png': 'otr-classe-calma.webp',
    '2026_09_foto2.png': 'otr-classe-codice.webp',
    '2026_09_foto3.png': 'otr-mani-alzate.webp',
    '2026_09_Gemini_Generated_Image_4ehcbi4ehcbi4ehc.jpg': 'otr-cartoncini.webp',
    '2026_09_Gemini_Generated_Image_kl4nt5kl4nt5kl4n.jpg': 'otr-docente-scrivania.webp',
    '2026_09_Gemini_Generated_Image_nirpzpnirpzpnirp.jpg': 'otr-studentessa-parco.webp',
    '2026_09_Gemini_Generated_Image_og0whtog0whtog0w.jpg': 'otr-studentessa-telefono.webp',
    '2026_09_Gemini_Generated_Image_2g8wn32g8wn32g8w.jpg': 'otr-telefono-sera.webp',
    '2026_09_corsonuovo-foto.jpg': 'otr-sfida-in-citta.webp',
    '2026_09_devops2.webp': 'otr-studio-al-bar.webp',
    # il corso d'esempio «L'intelligenza artificiale in ufficio»
    '2026_08_corso-mod-1-Elenco-w.jpg': 'otr-corso-elenco.webp',
    '2026_08_corso-mod-2-Tessere.jpg': 'otr-corso-tessere.webp',
    '2026_08_corso-mod-3-Percorso-w.jpg': 'otr-corso-percorso.webp',
    '2026_08_corso-mod-4-Tabellone-w.jpg': 'otr-corso-tabellone.webp',
    '2026_08_ai-in-azienda-livello-avanzato-cover.jpg': 'otr-corso-copertina.webp',
    '2026_08_flux_krea_00006_.png': 'otr-lezione-robot.webp',
    '2026_08_flux_krea_00053_.png': 'otr-lezione-dati.webp',
    '2026_08_flux_krea_00054_.png': 'otr-lezione-analista.webp',
    '2026_08_flux_krea_00063_.png': 'otr-lezione-squadra.webp',
    '2026_08_flux_krea_00065_.png': 'otr-lezione-tavolo.webp',
    '2026_08_flux_krea_00068_.png': 'otr-lezione-server.webp',
    '2026_08_flux_krea_00104_.png': 'otr-lezione-presentazione.webp',
    '2026_08_social4-1.jpg': 'otr-lezione-social.webp',
    # gli esplosi del corso di Informatica
    '2026_09_Gemini_Generated_Image_excdglexcdglexcd.jpg': 'otr-esploso-portatile.webp',
    '2026_09_Gemini_Generated_Image_fk4x99fk4x99fk4x.jpg': 'otr-esploso-pc.webp',
    '2026_09_Gemini_Generated_Image_pgbtkhpgbtkhpgbt.jpg': 'otr-esploso-scheda.webp',
}
LOGHI = {
    '2026_08_olotutor-1000-orizz.png': 'otr-logo.png',             # verde e grigio, per i fondi chiari
    '2026_09_olotutor-1000-orizz-w.png': 'otr-logo-w.png',         # verde e bianco, per la lavagna
    '2026_09_olotutor-1000-orizz-ww.png': 'otr-logo-ww.png',       # tutto bianco
    '2026_09_olotutor-1000-q.png': 'otr-logo-q.png',               # quadrato verde e grigio
    '2026_09_olotutor-500-q-w.png': 'otr-logo-q-w.png',            # quadrato verde e bianco
}
SVG = ['cartoncini', 'grid-bl', 'grid-tl-grigio', 'grid-tr', 'hero-navy-bg', 'istituto-griglia', 'nonancora-punti',
       'num-cerchi-navy', 'num-cerchi', 'onesta-cerchi', 'passi-01', 'passi-02', 'passi-03', 'registro-connettore',
       'std-navy-bg', 'std-pillole', 'std-punti', 'std-trattini', 'tesi-cerchi', 'voto-01', 'voto-02', 'voto-03', 'voto-04']
PDF = {'2026_09_nota-di-sintesi.pdf': 'otr-nota-di-sintesi.pdf'}

TUTTI = set(FOTO.values()) | set(LOGHI.values()) | {'otr-%s.svg' % s for s in SVG} | set(PDF.values())


def prepara(src):
    os.makedirs(OUT, exist_ok=True)
    for a, b in FOTO.items():
        dest = os.path.join(OUT, b)
        if os.path.exists(dest):
            continue
        im = Image.open(os.path.join(src, a))
        im = im.convert('RGB')
        im.thumbnail((1600, 1600), Image.LANCZOS)
        im.save(dest, 'WEBP', quality=82, method=6)
    for a, b in LOGHI.items():
        shutil.copyfile(os.path.join(src, a), os.path.join(OUT, b))
    for s in SVG:
        shutil.copyfile(os.path.join(src, 'vetrina_%s.svg' % s), os.path.join(OUT, 'otr-%s.svg' % s))
    for a, b in PDF.items():
        shutil.copyfile(os.path.join(src, a), os.path.join(OUT, b))
    mancano = [f for f in TUTTI if not os.path.exists(os.path.join(OUT, f))]
    assert not mancano, mancano
    print(len(TUTTI), 'file in', OUT)


if __name__ == '__main__':
    prepara(sys.argv[1])
