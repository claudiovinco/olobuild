# -*- coding: utf-8 -*-
"""Gli stili (preset) di una tile coi colori della palette al posto dei colori scritti a mano.

  python scripts/stili-coi-token.py <tipo> [--prova]

Riscrive, nel blocco della tile in src/config/tilePresets.js, ogni colore esadecimale o rgba() col token che fa lo
stesso lavoro, secondo il RUOLO della chiave: uno sfondo bianco diventa la superficie, un testo bianco il colore chiaro
(il testo sopra il fondo scuro), un testo scuro il colore del testo, un grigio medio il testo attenuato, un grigio
chiaro il bordo, gli arancio/indaco/blu/rossi storici il primario, le tinte pallide del primario un color-mix del
primario sulla superficie; le trasparenze tengono la loro percentuale con color-mix. I colori che non sa ricondurre a
un ruolo li lascia e li elenca: si guardano a mano. Con --prova stampa i cambi senza scrivere.

Perché (CLAUDE.md, «Colori solo via token»): uno stile scritto in esadecimale resta bianco su un tema scuro e arancio
su un sito rosso; coi token segue la palette del sito.
"""
import io
import re
import sys

sys.stdout.reconfigure(encoding='utf-8')

FILE = 'src/config/tilePresets.js'

BIANCHI = {'#f4efe7', '#fffbeb', '#fef9f0', '#fdfaf5', '#fff', '#ffffff', '#fefefe', '#fdfcfa', '#fafafa', '#f9fafb', '#f8fafc', '#fcfcfc', '#fffefa', '#fffdf8'}
QUASI_BIANCHI = {'#fef9c3', '#fef3c7', '#f3f4f6', '#f1f5f9', '#f6f7f9', '#f5f5f5', '#eef2f7', '#f4f4f5', '#f5f5f4', '#f7f7f7', '#f0f0f0', '#efefef', '#fafaf9'}
SCURI = {'#1a1a2e', '#92400e', '#211f1c', '#312e81', '#78350f', '#1e1b4b', '#0c0c0c', '#000', '#000000', '#0f172a', '#111827', '#1f2937', '#1e293b', '#111', '#111111', '#1a1a1a', '#14161c', '#0a0f1c',
         '#18181b', '#09090b', '#0b0b0b', '#121212', '#171717', '#0a0a0a', '#222', '#222222', '#1f2328', '#1c1917', '#0c0a09'}
ATTENUATI = {'#c6a888', '#475569', '#64748b', '#6b7280', '#374151', '#4b5563', '#9ca3af', '#888', '#888888', '#555', '#555555', '#94a3b8',
             '#666', '#666666', '#777', '#777777', '#999', '#999999', '#71717a', '#52525b', '#334155', '#57534e', '#78716c', '#a1a1aa'}
BORDI = {'#e5e7eb', '#e2e8f0', '#d1d5db', '#ececec', '#e4e4e7', '#ddd', '#dddddd', '#e5e5e5', '#eeeeee', '#eee', '#cbd5e1', '#d4d4d8', '#e7e5e4'}
PRIMARI = {'#bd5b38', '#b04217', '#e1474f', '#e8622a', '#ff6a2a', '#6366f1', '#1e87f0', '#ef4444', '#4f46e5', '#3b82f6', '#2563eb', '#f97316',
           '#dc2626', '#c62828', '#e74c3c', '#ff5722', '#fb923c', '#ea580c', '#7c3aed', '#8b5cf6', '#0ea5e9', '#06b6d4'}
TINTE_PRIMARIO = {'#fdf2ec', '#fef2f2', '#eef2ff', '#fff7ed', '#fff1f2', '#fdecea', '#ffe4e6', '#e0e7ff', '#dbeafe', '#fee2e2', '#ffedd5'}
ORI = {'#fde047', '#fbbf24', '#f59e0b', '#facc15', '#eab308', '#ffc107'}
ACCENTI = {'#ff00ff', '#f0f', '#00ffff', '#0ff', '#a855f7', '#22d3ee', '#d946ef', '#ec4899', '#f0abfc', '#c084fc'}
PRIMARI_SCURI = {'#0284c7', '#6d28d9', '#c2410c', '#b91c1c', '#1d4ed8', '#4338ca', '#be123c', '#9a3412', '#c73d44', '#b8323a'}
PASTELLI = {'#86efac', '#a5f3fc', '#fce7f3', '#fbcfe8', '#bae6fd', '#ddd6fe', '#fde68a', '#bbf7d0', '#c7d2fe'}
VERDI = {'#10b981', '#22c55e', '#16a34a', '#00ff8c', '#4ade80', '#059669'}

SFONDO = re.compile(r'(^|_)(bg|background|fill|surface|track|card|panel|box|overlay|backdrop|base)(_|$)|bg_', re.I)
BORDO = re.compile(r'border|divider|separator|outline|stroke|(^|_)line(_|$)|rule', re.I)
TESTO = re.compile(r'text|color|title|heading|label|desc|icon|price|number|name|quote|author|role|font|ink|glyph|value|link|accent|handle|arrow|dot|star|check|bullet|marker', re.I)


def ruolo(chiave):
    k = chiave.lower()
    if 'shadow' in k or 'glow' in k:
        return 'ombra'
    if BORDO.search(k):
        return 'bordo'
    if SFONDO.search(k) and not re.search(r'text|title|label|desc', k):
        return 'sfondo'
    if TESTO.search(k):
        return 'testo'
    return '?'


def mescola(token, alfa):
    pc = round(float(alfa) * 100)
    if pc >= 100:
        return token
    return 'color-mix(in srgb, %s %d%%, transparent)' % (token, pc)


def converti(chiave, valore):
    v = valore.strip().lower()
    r = ruolo(chiave)
    m = re.fullmatch(r'rgba?\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)\s*(?:,\s*([\d.]+)\s*)?\)', v)
    if m:
        rr, gg, bb, a = int(m[1]), int(m[2]), int(m[3]), m[4] or '1'
        hexv = '#%02x%02x%02x' % (rr, gg, bb)
        base = converti(chiave, hexv)
        if base is None:
            return None
        return mescola(base, a) if float(a) < 1 else base
    if not re.fullmatch(r'#[0-9a-f]{3}([0-9a-f]{3})?', v):
        return None
    if r == 'ombra':
        if v in PRIMARI:
            return 'var(--olo-color-primary)'
        return 'var(--olo-color-dark)' if (v in SCURI or v in ATTENUATI or v in BORDI) else ('var(--olo-color-light)' if v in BIANCHI else None)
    if v in BIANCHI:
        return {'sfondo': 'var(--olo-color-surface)', 'testo': 'var(--olo-color-light)', 'bordo': 'var(--olo-color-surface)'}.get(r)
    if v in QUASI_BIANCHI:
        return {'sfondo': 'var(--olo-color-surface-alt)', 'testo': 'var(--olo-color-light)', 'bordo': 'var(--olo-color-border)'}.get(r)
    if v in SCURI:
        return {'sfondo': 'var(--olo-color-dark)', 'testo': 'var(--olo-color-text)', 'bordo': 'var(--olo-color-dark)'}.get(r)
    if v in ATTENUATI:
        return {'sfondo': 'var(--olo-color-surface-alt)', 'testo': 'var(--olo-color-text-muted)', 'bordo': 'var(--olo-color-border)'}.get(r)
    if v in BORDI:
        return {'sfondo': 'var(--olo-color-surface-alt)', 'testo': 'var(--olo-color-text-muted)', 'bordo': 'var(--olo-color-border)'}.get(r)
    if v in PRIMARI:
        return 'var(--olo-color-primary)'
    if v in PASTELLI:
        return {'sfondo': 'color-mix(in srgb, var(--olo-color-accent) 18%, var(--olo-color-surface))',
                'testo': 'color-mix(in srgb, var(--olo-color-accent) 35%, var(--olo-color-light))',
                'bordo': 'color-mix(in srgb, var(--olo-color-accent) 40%, transparent)'}.get(r)
    if v in PRIMARI_SCURI:
        return 'color-mix(in srgb, var(--olo-color-primary) 82%, black)'
    if v in TINTE_PRIMARIO:
        return 'color-mix(in srgb, var(--olo-color-primary) 8%, var(--olo-color-surface))'
    if v in ORI:
        return 'var(--olo-color-warning)'
    if v in ACCENTI:
        return 'var(--olo-color-accent)'
    if v in VERDI:
        return 'var(--olo-color-success)'
    return None


def blocco(s, tipo):
    m = re.search(r"\n  '?%s'?: \{" % re.escape(tipo), s)
    if not m:
        raise SystemExit('nessuno stile per «%s» in %s' % (tipo, FILE))
    i = s.index('{', m.start())
    d = 0
    for j in range(i, len(s)):
        if s[j] == '{':
            d += 1
        elif s[j] == '}':
            d -= 1
            if d == 0:
                return i, j + 1
    raise SystemExit('blocco non chiuso')


def main():
    tipo = sys.argv[1]
    prova = '--prova' in sys.argv
    s = io.open(FILE, encoding='utf-8').read()
    i, j = blocco(s, tipo)
    b = s[i:j]
    cambi, restano = [], []
    coppia = re.compile(r"(\b[\w]+)(\s*:\s*)'((?:#[0-9a-fA-F]{3,8})|(?:rgba?\([^)']*\)))'")

    def sost(mm):
        chiave, sep, valore = mm.group(1), mm.group(2), mm.group(3)
        nuovo = converti(chiave, valore)
        if nuovo is None:
            restano.append('%s: %s (%s)' % (chiave, valore, ruolo(chiave)))
            return mm.group(0)
        cambi.append('%s: %s → %s' % (chiave, valore, nuovo))
        return "%s%s'%s'" % (chiave, sep, nuovo)
    nb = coppia.sub(sost, b)
    print('%s: %d colori nei token, %d da guardare' % (tipo, len(cambi), len(restano)))
    for r in sorted(set(restano)):
        print('  RESTA', r)
    if prova:
        for c in cambi[:400]:
            print('  ', c)
        return
    io.open(FILE, 'w', encoding='utf-8', newline='\n').write(s[:i] + nb + s[j:])


if __name__ == '__main__':
    main()
