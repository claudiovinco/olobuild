=== OLOsecurity ===
Contributors: claudiovinco
Tags: security, firewall, two-factor, malware scanner, brute force
Requires at least: 5.9
Tested up to: 7.0
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

La barriera di sicurezza WordPress di OLOtheme: integrità file, scanner webshell, anti brute-force, blocklist automatica e 2FA nativa.

== Description ==

OLOsecurity è una prima barriera di sicurezza integrata e leggera, mirata ai vettori di compromissione più comuni di WordPress:

* **Integrità del core** — confronto con i checksum ufficiali di wordpress.org: file modificati, mancanti o PHP estranei dentro wp-admin/wp-includes.
* **Integrità di plugin e temi** — impronta aggregata per componente legata alla versione: file cambiati senza un aggiornamento = firma tipica di un'iniezione.
* **Scanner webshell** — scansione di uploads e mu-plugins (PHP eseguibili, doppie estensioni, firme note) con quarantena e ripristino.
* **Registro attività** — chi/cosa/quando: login, utenti e ruoli, plugin e temi, opzioni critiche. Export CSV.
* **Anti brute-force** — lockout temporaneo per IP, blocklist permanente IP/CIDR (403 sito intero, non evadibile via header) ed escalation automatica dei recidivi.
* **Verifica in due passaggi (2FA)** — TOTP con qualsiasi app authenticator (QR code), codice via email, recovery codes monouso, "ricorda questo browser", obbligo opzionale per gli amministratori.
* **Monitor di configurazione** — baseline di opzioni critiche, amministratori e ruoli: siteurl/home dirottati, admin nuovi, registrazioni aperte.
* **Hardening opzionale** — security header, CSP report-only con raccolta violazioni, stop user-enumeration, XML-RPC off, Application Passwords off.
* **Avvisi** — email e webhook (Slack/Discord/Mattermost) sui rilevamenti ad alta severità.

Gli header proxy (X-Forwarded-For, CF-Connecting-IP) vengono creduti solo da proxy fidati (Cloudflare, reti locali, voci configurate): nessuno può falsificare il proprio IP per evadere i blocchi.

Se è attivo un altro plugin 2FA dedicato, la 2FA di OLOsecurity si disattiva da sola per non interferire. OLOsecurity convive con OLObuild: se entrambi sono presenti, il plugin standalone ha la precedenza sul modulo bundled.

== Installation ==

1. Carica la cartella `olosecurity` in `/wp-content/plugins/` o installa dallo ZIP.
2. Attiva il plugin.
3. Trovi tutto nel menu **OLOsecurity** (Stato / Registro / Impostazioni).

== Frequently Asked Questions ==

= Mi sono chiuso fuori (blocklist o 2FA): come rientro? =

Via wp-cli: `wp option delete olo_sec_blocklist` per la blocklist, `wp user meta delete <id> olo_2fa` per la 2FA di un utente.

= Serve un'app particolare per la 2FA? =

No: qualunque app authenticator standard (Google Authenticator, Microsoft Authenticator, Authy, FreeOTP, Bitwarden, 1Password…). In alternativa c'è il codice via email.

== Changelog ==

= 1.0.0 =
* Prima release standalone: tutti i moduli OLOsecurity estratti da OLObuild.
