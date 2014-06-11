clear
echo '
  Reloading schema:
';

#CACHE CLEAR
sudo php app/console cache:clear --no-warmup
sudo chmod 777 -R app/cache/ app/logs/
sudo rm -rf app/cache/* app/logs/*

#SCHEMA AND FIXTURES
php app/console doctrine:schema:drop --force --em="em_lock"
php app/console doctrine:schema:create --em="em_lock"

#CACHE CLEAR
sudo php app/console cache:clear --no-warmup
sudo chmod 777 -R app/cache/ app/logs/
sudo rm -rf app/cache/* app/logs/*
