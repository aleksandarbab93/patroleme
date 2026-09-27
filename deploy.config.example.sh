#!/bin/bash
# Kopiraj ovaj fajl u deploy.config.sh i popuni svojim podacima.
# deploy.config.sh se NE verzioniše (u .gitignore je) jer sadrži pristupne
# podatke serveru.

# Iz Cloudways panela: Server → Master Credentials, ili Application →
# Access Details ako koristiš aplikacijski (ne master) nalog.
SSH_USER="tvoje_ssh_korisnicko_ime"
SSH_HOST="tvoj.server.ip.ili.hostname"
SSH_PORT="22"

# Putanja na serveru do KORIJENA aplikacije (roditelj foldera public_html).
# Na Cloudways to je obično:
#   /home/master/applications/xxxxxxxxxx/
# Vidi u Cloudways panelu: Application → Access Details → "Application Path"
REMOTE_PATH="/home/master/applications/xxxxxxxxxx"
