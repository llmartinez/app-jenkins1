clear
echo '
  Reloading schema "ad-service-test" and fixtures: 
';

php app/console doctrine:schema:drop --force
php app/console doctrine:schema:create 
php app/console d:f:l
