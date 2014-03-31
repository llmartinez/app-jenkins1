clear
echo '
  Reloading schema and fixtures: 
';

php app/console doctrine:schema:drop --force
php app/console doctrine:schema:create 
php app/console d:f:l
