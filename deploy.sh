#!/bin/bash
# Šalje web/ folder na Cloudways server preko rsync-a preko SSH-a.
#
# Prvi put: kopiraj deploy.config.example.sh u deploy.config.sh i popuni
# svojim podacima (SSH_USER, SSH_HOST, REMOTE_PATH iz Cloudways panela).
#
# Svaki sljedeći put: samo pokreni  ./deploy.sh
#
# Šta radi:
#   1. Šalje sve fajlove iz web/ NA server (osim data/ baze — ona ostaje
#      netaknuta na serveru, da ne prepiše sadržaj koji si već unio kroz CMS)
#   2. Ne briše ništa što je ručno dodato na serveru van ovog seta fajlova
#      (npr. patrole.me.apk koji uploaduješ posebno)

set -e
cd "$(dirname "$0")"

if [ ! -f deploy.config.sh ]; then
  echo "GREŠKA: nema deploy.config.sh"
  echo "Kopiraj deploy.config.example.sh u deploy.config.sh i popuni podatke."
  exit 1
fi
source deploy.config.sh

echo "==> Šaljem web/ na $SSH_USER@$SSH_HOST:$REMOTE_PATH"
echo "    (data/ baza na serveru NIJE dirana — sadržaj iz CMS-a ostaje)"

rsync -avz --progress \
  -e "ssh -p $SSH_PORT" \
  --exclude 'data/*.sqlite' \
  --exclude 'deploy.config.sh' \
  --exclude 'deploy.sh' \
  --exclude 'deploy.config.example.sh' \
  --exclude 'router.php' \
  --exclude '.DS_Store' \
  ./ "$SSH_USER@$SSH_HOST:$REMOTE_PATH/"

echo ""
echo "==> Gotovo. Provjeri sajt: otvori patrole.me u browseru."
echo "    Ako je ovo PRVI deploy, uloguj se preko SSH i pokreni:"
echo "      cd $REMOTE_PATH && php create-admin.php TVOJE_IME TVOJA_LOZINKA"
