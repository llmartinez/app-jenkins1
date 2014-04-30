clear
echo '
  Reloading schema:
';

php app/console doctrine:schema:drop --force --em="em_lock"
php app/console doctrine:schema:create --em="em_lock"
