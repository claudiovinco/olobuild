#!/usr/bin/env bash
# banco.sh — il banco L1 del sistema compatto (passo 0.1) in UN comando, su mosaic.
#
# Si lancia dalla copia di scripts/golden sul server (es. /root/olo-golden), MAI dalla
# cartella del plugin: scripts/ non fa parte del pacchetto deployato.
#
#   bash banco.sh <fase> [riferimento]   rende tutti i template + le pagine dei temi in
#                                        snap/<fase>; con un riferimento li confronta
#                                        (uscita ≠ 0 se diversi)
#   bash banco.sh confronta <A> <B>      solo il confronto di due snapshot
#   bash banco.sh censimento             il censimento di sola lettura → snap/censimento-<data>.json
#   bash banco.sh elenco                 le chiavi che il banco renderebbe
#
# <fase> e <riferimento> sono nomi dentro $SNAP (o percorsi). Esempi:
#   bash banco.sh baseline-1.4.480
#   bash banco.sh prova-B baseline-1.4.480            # due giri sulla stessa versione: IDENTICI
#   ORDINE=inverso bash banco.sh prova-C baseline-1.4.480   # isolamento per processo
#
# Variabili (predefiniti di mosaic):
#   WP_PATH=/var/www/wordpress   WP_BIN=/usr/local/bin/wp   PHP_BIN=php   MEM=512M
#   SNAP=<cartella di banco.sh>/snap   SRC=all (db|themes|all, anche per il censimento)
#   ORDINE= (inverso)   JS= (default-js.json per il censimento)
#   SOLO= (chiavi separate da virgola: snapshot parziale con _solo.txt, il confronto con un
#         riferimento completo guarda solo quelle chiavi)
#   ACCETTA= (chiavi fatali note, separate da virgola: confronta.sh le ammette se in A
#            erano già fatali; senza, ogni chiave non resa in B dà uscita ≠ 0)
set -euo pipefail

DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
WP_PATH=${WP_PATH:-/var/www/wordpress}
WP_BIN=${WP_BIN:-/usr/local/bin/wp}
PHP_BIN=${PHP_BIN:-php}
MEM=${MEM:-512M}
SNAP=${SNAP:-$DIR/snap}
SRC=${SRC:-all}
ORDINE=${ORDINE:-}
SOLO=${SOLO:-}
JS=${JS:-}

uso() { awk 'NR > 1 && /^#/ { sub(/^# ?/, ""); print; next } NR > 1 { exit }' "${BASH_SOURCE[0]}"; exit 2; }
[ $# -ge 1 ] || uso

WP=( "$PHP_BIN" -d "memory_limit=$MEM" "$WP_BIN" "--path=$WP_PATH" "--require=$DIR/golden-stub.php" )
if [ "$(id -u)" = "0" ]; then WP+=( --allow-root ); fi

cartella() { case "$1" in /*|./*|../*) printf '%s' "$1" ;; *) printf '%s/%s' "$SNAP" "$1" ;; esac; }

case "$1" in
  -h|--help|help) uso ;;
  confronta)
    [ $# -eq 3 ] || uso
    exec bash "$DIR/confronta.sh" "$(cartella "$2")" "$(cartella "$3")"
    ;;
  elenco)
    exec "${WP[@]}" eval-file "$DIR/render-golden.php" "src=$SRC" elenco=1
    ;;
  censimento)
    mkdir -p "$SNAP"
    args=( "out=$SNAP/censimento-$(date +%F).json" "src=$SRC" )
    [ -n "$JS" ] && args+=( "js=$JS" )
    exec "${WP[@]}" eval-file "$DIR/censimento.php" "${args[@]}"
    ;;
esac

FASE=$1
RIF=${2:-}
case "$FASE" in *[!A-Za-z0-9._-]*) echo "Nome di fase non valido: $FASE (lettere, cifre, . _ -)" >&2; exit 2 ;; esac
OUT=$(cartella "$FASE")
if [ -e "$OUT/_summary.tsv" ]; then
  echo "$OUT esiste già: le fasi non si sovrascrivono." >&2
  exit 2
fi
if [ -n "$RIF" ] && [ ! -f "$(cartella "$RIF")/_summary.tsv" ]; then
  echo "Riferimento non trovato: $(cartella "$RIF")" >&2
  exit 2
fi
mkdir -p "$SNAP"

args=( "src=$SRC" "out=$OUT" "mem=$MEM" )
[ -n "$ORDINE" ] && args+=( "ordine=$ORDINE" )
[ -n "$SOLO" ] && args+=( "solo=$SOLO" )
"${WP[@]}" eval-file "$DIR/render-golden.php" "${args[@]}"

if [ -n "$RIF" ]; then
  echo
  echo "── confronto con $RIF"
  bash "$DIR/confronta.sh" "$(cartella "$RIF")" "$OUT"
fi
