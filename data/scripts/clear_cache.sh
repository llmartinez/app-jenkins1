clear
echo '
  Clearing cache & dev files:
';

sudo php app/console cache:clear --no-warmup
sudo chmod 777 -R app/cache/ app/logs/
sudo rm -rf app/cache/* app/logs/*
