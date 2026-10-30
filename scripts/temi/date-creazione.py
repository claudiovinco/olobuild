# -*- coding: utf-8 -*-
"""Scrive assets/data/temi-creati.json: la data di creazione di ogni tema (id → data ISO), con cui la Vetrina temi e
il selettore dei temi li mettono in ordine, dal più recente (richiesta dell’utente, 30 ottobre 2026).

La data è quella in cui il theme.json del tema è entrato nel repository (git, seguendo i nomi cambiati): per i temi di
OLObuild nel repository di OLObuild, per quelli di OLObooking nel suo. Un tema non ancora committato prende la data di
nascita della sua cartella. Le date già scritte nel file non cambiano: si aggiungono solo i temi nuovi.
uso: python date-creazione.py
"""
import datetime
import glob
import json
import os
import subprocess

ROOT = os.path.normpath(os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', '..'))
OLOBOOKING = os.path.normpath(os.path.join(ROOT, '..', 'olo-booking'))
OUT = os.path.join(ROOT, 'assets', 'data', 'temi-creati.json')
DOPO_IL_TRASLOCO = '2026-10-27'


def da_git(repo, rel):
    r = subprocess.run(['git', '-C', repo, 'log', '--follow', '--diff-filter=A', '--format=%aI', '--', rel],
                       capture_output=True, text=True)
    righe = [x for x in r.stdout.split('\n') if x.strip()]
    return righe[-1] if righe else ''


def nascita(path):
    return datetime.datetime.fromtimestamp(os.stat(path).st_ctime).astimezone().isoformat(timespec='seconds')


def main():
    date = json.load(open(OUT, encoding='utf-8')) if os.path.exists(OUT) else {}
    sorgenti = [(ROOT, f) for f in glob.glob(os.path.join(ROOT, 'assets', 'data', 'themes', '*', 'theme.json'))]
    sorgenti += [(OLOBOOKING, f) for f in glob.glob(os.path.join(OLOBOOKING, 'themes', '*', 'theme.json'))]
    nuovi = 0
    for repo, f in sorgenti:
        tid = json.load(open(f, encoding='utf-8')).get('id') or os.path.basename(os.path.dirname(f))
        if tid in date:
            continue
        rel = os.path.relpath(f, repo).replace('\\', '/')
        g, c = da_git(repo, rel), nascita(f)
        # La nascita della cartella è più precisa del commit (più temi entrano in un commit solo), ma vale solo per i
        # temi nati dopo il trasloco dei progetti sul disco T: (26 ottobre 2026): prima, la copia l’ha cambiata a tutti.
        date[tid] = c if (not g or (DOPO_IL_TRASLOCO <= c < g)) else g
        nuovi += 1
    ordinati = dict(sorted(date.items(), key=lambda kv: (kv[1], kv[0])))
    with open(OUT, 'w', encoding='utf-8', newline='\n') as fh:
        json.dump(ordinati, fh, ensure_ascii=False, indent=1)
        fh.write('\n')
    print(len(ordinati), 'temi,', nuovi, 'nuovi')


if __name__ == '__main__':
    main()
