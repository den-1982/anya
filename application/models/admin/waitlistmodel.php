<?php
class waitlistModel extends CI_Model
{	
	public function getCountWaitList()
	{
		$cnt = $this->db->query('SELECT product_id, id_size
									FROM product_waitlist 
								GROUP BY product_id, id_size')->result();
		
		return count($cnt);
	}
	
	public function getWaitList()
	{
		$products = $this->db->query('SELECT pw.id_size,
											pd.name,
											p.id,
											fi.name AS sizeName,
											fi.prefix AS sizePrefix
									FROM product_waitlist pw 
									LEFT JOIN product p ON p.id = pw.product_id
									LEFT JOIN product_description pd ON p.id = pd.product_id
									LEFT JOIN filter_item fi ON fi.id = pw.id_size
								GROUP BY pw.product_id, pw.id_size
									ORDER BY pw.product_id ASC')->result();
		
		return $products;
	}

	public function sendEmailWaitList()
	{
		
		exit;
		
		# NEWSLATTER (сообщить если появился товар) ****************************************************************
		//log_message('error', '+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ START NEWSLATTER');
		
		# Вариант-1
		$rowWlProductsNoSize = $this->db->query('SELECT products_id FROM waitlist WHERE id_size = 0 GROUP BY products_id')->result();
		$rowWlProductsHaveSize = $this->db->query('SELECT products_id, id_size FROM waitlist WHERE id_size <> 0 GROUP BY products_id, id_size')->result();
		
		$wlProductsNoSize = array();
		foreach ($rowWlProductsNoSize as $i){
			$wlProductsNoSize[$i->products_id] = $i;
		}
		
		$wlProductsHaveSize = array();
		foreach ($rowWlProductsHaveSize as $i){
			$wlProductsHaveSize[$i->products_id][] = $i->id_size;
		}
		
		echo '<pre>';
		print_r($wlProductsNoSize);

		print_r($wlProductsHaveSize);
		exit;
		
		
		
		# Вариант-2
		$rowWlProducts = $this->db->query('SELECT products_id, id_size FROM waitlist GROUP BY products_id, id_size')->result();
		
		$wlProducts = array();
		foreach ($rowWlProducts as $i){
			$wlProducts[$i->products_id][] = $i->id_size;
		}

		#
		$ids_wlProduct = array_keys($wlProducts);
	
		// echo '<pre>';
		// print_r($ids_wlProduct);
		// exit;
		/*
		(
			[0] => 23
			[1] => 40
			[2] => 62
			[3] => 174
			[4] => 241
		)
		*/

		
		// $wl = $this->db->query('SELECT w.*,
										// fi.name AS size_name,
										// fi.prefix AS size_prefix
								// FROM waitlist w
								// LEFT JOIN filter_item fi ON fi.id = w.id_size
							// WHERE w.products_id = "'.$product->id.'" AND w.id_size IN('.implode(',', $ids_size).')
								// GROUP BY w.id_size, w.email')->result();

		// echo '<pre>';
		// print_r($wl);
		exit;

		
		# товар
		$product = $this->productModel->getProduct($id);
		log_message('error', $product->id);
		if ($product){
			# цены - размеры
			$prices = $this->db->query('SELECT * 
											FROM product_prices 
										WHERE id_product = "'.$product->id.'" AND (opt <> 0 OR roz <> 0)')->result();
			# MORE PRICES
			if ($prices){
				log_message('error', 'товар с несколькоми ценами');
				
				$ids_size = array();
				foreach ($prices as $item){
					$ids_size[] = $item->id_filter_item;
				}
				$ids_size = $ids_size ? $ids_size : 0;

				$wl = $this->db->query('SELECT w.*,
												fi.name AS size_name,
												fi.prefix AS size_prefix
										FROM waitlist w
										LEFT JOIN filter_item fi ON fi.id = w.id_size
									WHERE w.products_id = "'.$product->id.'" AND w.id_size IN('.implode(',', $ids_size).')
										GROUP BY w.id_size, w.email')->result();
				
				$a = array();
				foreach ($wl as $i){
					$a[$i->email][$i->id_size] = $i;
				}
				
				echo '<pre>';
				print_r($a);
				exit;
				/*
				<pre>Array
				(
					[aaaa@asd.asd] => Array
						(
							[5] => stdClass Object
								(
									[id] => 14
									[products_id] => 23
									[id_size] => 5
									[id_color] => 0
									[email] => aaaa@asd.asd
									[date] => 1438333548
									[size_name] => SS12
									[size_prefix] => 3.0-3.2 мм
								)

							[9] => stdClass Object
								(
									[id] => 16
									[products_id] => 23
									[id_size] => 9
									[id_color] => 0
									[email] => aaaa@asd.asd
									[date] => 1438333557
									[size_name] => SS34
									[size_prefix] => 7.2-7.4 мм
								)

						)

					[hamet@ua.fm] => Array
						(
							[5] => stdClass Object
								(
									[id] => 1
									[products_id] => 23
									[id_size] => 5
									[id_color] => 0
									[email] => hamet@ua.fm
									[date] => 1438327804
									[size_name] => SS12
									[size_prefix] => 3.0-3.2 мм
								)

							[9] => stdClass Object
								(
									[id] => 2
									[products_id] => 23
									[id_size] => 9
									[id_color] => 0
									[email] => hamet@ua.fm
									[date] => 1438327810
									[size_name] => SS34
									[size_prefix] => 7.2-7.4 мм
								)

						)

				)
				*/

				foreach ($wl as $item){
					log_message('error', $item->email);
					
					$tema = strtoupper($_SERVER['SERVER_NAME']) . "Товар в наличии";
					$headers = "From:  ". strtoupper($_SERVER['SERVER_NAME']) ." <admin@crystalline.in.ua>\r\n";
					$headers .= "Content-type: text/html; charset=\"utf-8\"";
					$msg = '
					<html>
						<head>
						  <title>Заказ товаров '. strtoupper($_SERVER['SERVER_NAME']) .'</title>
						</head>
						<body>
						Здравствуйте,
						<br>
						Вы подписывались на уведомление о появлении товара нашего магазина в наличии.
						<br>
						Ниже товар, на который вы подписывались и который появился в наличии:
						<br>
						<br>
						<table width="100%" cellpadding="4" cellspacing="0" style="border-collapse:collapse;font-size:14px;text-align:center;">
							<tr style="background:#eee;">
								<td width="1x" style="border:1px solid #ccc"></td>
								<td style="border:1px solid #ccc;">Наименование</td>
								<td style="border:1px solid #ccc;">Размер</td>
								<td style="border:1px solid #ccc;">Ссылка</td>
							</tr>
							<tr>
								<td style="border:1px solid #ccc;">
									<a href="http://'.$_SERVER['SERVER_NAME'].$product->_url.'">
										<img style="width:50px;" src="http://'.$_SERVER['SERVER_NAME'].$product->image.'" alt="">
									</a>
								</td>
								<td style="border:1px solid #ccc; text-align:left;">
									<a href="http://'.$_SERVER['SERVER_NAME'].$product->_url.'">'.$product->name.'</a>
								</td>
								<td style="border:1px solid #ccc;">';
								foreach ($product->filter_item as $fi){
									if ($fi->id_filter_item != $item->id_size) continue;
									
									$msg .= ' '.$product->prices[$id_size]->name.' ';
									//'. (isset($product->prices[$id_size]) ? $product->prices[$id_size]->name .' '.$product->prices[$id_size]->prefix : '') .'
								}
								$msg .='
								</td>
								<td style="border:1px solid #ccc;">'.$email.'</td>
							</tr>
						</table>
						<br>
						Надеемся, что эта информация была вам полезна.
						<br>
						Приятных покупок!
						</body
					</html>';
					
					echo $msg;
					exit;
					
					mail($item->email, $tema, $msg, $headers);
				}
				
				# удаляем старые записи
				// $this->db->query('DELETE FROM waitlist WHERE products_id = "'.$product->id.'" AND id_size IN('.implode(',', $ids_size_wait).')');
			
			# ONE PRICE
			}
			else if ($price){
				log_message('error', 'товар с одной ценой');
				$wl = $this->db->query('SELECT * 
										FROM waitlist 
									WHERE products_id = "'.$product->id.'" AND id_size = 0
										GROUP BY email')->result();
				
				$tema = strtoupper($_SERVER['SERVER_NAME']) . "Товар в наличии";
				$headers = "From:  ". strtoupper($_SERVER['SERVER_NAME']) ." <admin@crystalline.in.ua>\r\n";
				$headers .= "Content-type: text/html; charset=\"utf-8\"";
				$msg = '
				<html>
					<head>
					  <title>Заказ товаров '. strtoupper($_SERVER['SERVER_NAME']) .'</title>
					</head>
					<body>
					Здравствуйте,
					<br>
					Вы подписывались на уведомление о появлении товара нашего магазина в наличии.
					<br>
					Ниже товар, на который вы подписывались и который появился в наличии:
					<br>
					<br>
					<table width="100%" cellpadding="4" cellspacing="0" style="border-collapse:collapse;font-size:14px;text-align:center;">
						<tr style="background:#eee;">
							<td width="1x" style="border:1px solid #ccc"></td>
							<td style="border:1px solid #ccc;">Наименование</small></td>
							<td style="border:1px solid #ccc;"><small>Ссылка</small></td>
						</tr>
						<tr>
							<td style="border:1px solid #ccc;">
								<a href="http://'.$_SERVER['SERVER_NAME'].$product->_url.'">
									<img style="width:50px;" src="http://'.$_SERVER['SERVER_NAME'].$product->image.'" alt="">
								</a>
							</td>
							<td style="border:1px solid #ccc; text-align:left;">
								<a href="http://'.$_SERVER['SERVER_NAME'].$product->_url.'">'.$product->name.'</a>
							</td>
							<td style="border:1px solid #ccc;"><a href="http://'.$_SERVER['SERVER_NAME'].$product->_url.'">http://'.$_SERVER['SERVER_NAME'].$product->_url.'</a></td>
						</tr>
					</table>
					<br>
					Надеемся, что эта информация была вам полезна.
					<br>
					Приятных покупок!
					</body
				</html>';
					
				foreach ($wl as $item){
					log_message('error', $item->email);
					mail($item->email, $tema, $msg, $headers);
				}
				
				# удаляем старые записи
				$this->db->query('DELETE FROM waitlist WHERE products_id = "'.$product->id.'"');
			}
		}# END NEWSLATTER

		return;
	}
	
	public function delWaitList($product_id = 0, $id_size = 0)
	{
		$product_id = (int)$product_id;
		$id_size = (int)$id_size;
		
		$this->db->query('DELETE FROM waitlist 
							WHERE products_id = "'.$product_id.'" AND id_size = "'.$id_size.'"');
	}

}


