clear
echo ' This operation works on "/etc/php5/apache2/php.ini
 '

echo ' - Go to "max_execution_time" to control the time of the query execution 
         (line 386 aprox.), default 30   -> -1 for import data
 '

echo ' - Go to "memory_limit" to control the memory spended in the query 
         (line 407 aprox.), default 128M -> -1 for import data
 '

sudo gedit /etc/php5/apache2/php.ini  

