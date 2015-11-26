<?php
# Сброс скидки по таймеру

mysql_connect('localhost', 'host10079', 'AGJLkkFk');
mysql_select_db('crystalline');

date_default_timezone_set('Europe/Kiev'); 
$dt = time();
mysql_query('UPDATE product_prices 
				SET discount = 0 
			WHERE id_product IN(
								SELECT id 
									FROM products 
								WHERE end_discount <> 0 AND end_discount < "'.$dt.'"
							)');
							
mysql_query('UPDATE products 
				SET discount = 0, 
					end_discount = 0 
			WHERE end_discount <> 0 AND end_discount < "'.$dt.'"');

# TEST
// $file = __DIR__ . '/test.txt';
// $data = "TEST ". date('Y-M-D h:m:s', time())."\n $file";
// file_put_contents($file, $data, FILE_APPEND);

// /d01/www/anna/data/www/crystalline.in.ua/application/controllers
// # /usr/bin/php /d01/www/anna/data/www/crystalline.in.ua/cron.php
// exit;