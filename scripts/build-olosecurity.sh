#!/bin/bash
# Genera il pacchetto del plugin standalone OLOsecurity dalla fonte condivisa.
# I moduli vivono UNA volta sola in includes/class-security-*.php (bundlati in
# OLObuild); questo script li impacchetta con il main file olosecurity/ e swappa
# il text domain. Uso: bash scripts/build-olosecurity.sh
set -e
cd "$(dirname "$0")/.."

VER=$(grep -oE "OLOSEC_VERSION', '[0-9.]+" olosecurity/olosecurity.php | grep -oE '[0-9.]+$')
OUT=build/olosecurity

rm -rf "$OUT"
mkdir -p "$OUT/includes" "$OUT/assets/vendor/qrcode" "$OUT/languages"

cp olosecurity/olosecurity.php olosecurity/readme.txt "$OUT/"
cp includes/class-security-audit.php \
   includes/class-security-config-monitor.php \
   includes/class-security-components.php \
   includes/class-security-login.php \
   includes/class-security-hardening.php \
   includes/class-security-twofactor.php \
   includes/class-security-sentinel.php "$OUT/includes/"
cp assets/vendor/qrcode/qrcode.min.js assets/vendor/qrcode/LICENSE "$OUT/assets/vendor/qrcode/"
[ -f LICENSE ] && cp LICENSE "$OUT/"

# Text domain del pacchetto standalone: 'olobuild' → 'olosecurity' (solo i18n).
sed -i "s/'olobuild' )/'olosecurity' )/g" "$OUT"/includes/*.php

cd build
rm -f "olosecurity-$VER.zip"
if command -v zip >/dev/null 2>&1; then
  zip -qr "olosecurity-$VER.zip" olosecurity
  echo "OK build/olosecurity-$VER.zip"
else
  # ⚠️ NON usare Compress-Archive di Windows: scrive i path con backslash e su
  # Linux WordPress non riconosce la struttura. Senza `zip` lascia la cartella
  # pronta: zippala su un host Linux (es. `cd build && zip -r olosecurity-$VER.zip olosecurity`).
  echo "zip non disponibile in locale: cartella pronta in build/olosecurity/ (zippa su Linux per wp.org)"
fi
