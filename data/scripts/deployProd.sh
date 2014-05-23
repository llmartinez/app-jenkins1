clear
echo '
  Reloading schema and fixtures:
';

#CACHE CLEAR
sudo php app/console cache:clear --no-warmup
sudo chmod 777 -R app/cache/ app/logs/
sudo rm -rf app/cache/* app/logs/*

#SCHEMA AND FIXTURES
php app/console doctrine:schema:drop --force
php app/console doctrine:schema:create
php app/console d:f:l --fixtures='src/Adservice/UtilBundle/DataFixtures/ORM/PROD'
