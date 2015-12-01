<?php
class productModel extends CI_Model
{	
	public function getProduct($id = 0)
	{
		$id = abs((int)$id);
		$product = $this->db->query('SELECT *, 
											CONCAT("/", p.url, "/", "p", p.id, "/") AS _url
										FROM product p
										LEFT JOIN product_description pd ON pd.product_id = p.id
									WHERE p.visibility = 1 AND p.id = "'.$id.'"')->row();
		
		if ( ! $product) return array();
		
		# MANUFACTURER
		$product->manufacturer = $this->db->query('SELECT * 
														FROM manufacturer m
														LEFT JOIN manufacturer_description md ON md.manufacturer_id = m.id
													WHERE m.id = "'.$product->manufacturer_id.'"')->row();
		
		# IMAGES
		$product->images= $this->db->query('SELECT * FROM product_images WHERE product_id = "'.$product->id.'"')->result();
		foreach ($product->images as $image){
			$image->cache = preg_replace('/(.*)(\/.+)$/', '$1/_cache_$2', $image->image);
		}
		
		# RELATED сопутствующие товары
		$product->related = $this->db->query('SELECT p.*,
													pd.name,
													pd.h1,
													CONCAT("/", p.url, "/", "p", p.id, "/") AS _url
											FROM product p 
											LEFT JOIN product_description pd ON pd.product_id = p.id
										WHERE p.id IN(
											SELECT related_id FROM product_related WHERE product_id = "'.$product->id.'"
										)')->result();
		
		# DISCOUNT CATEGORY (%) если в категории выставлена скидка, то добавляем в товары product->discount = category->discount
		$category_discount = $this->db->query('SELECT discount FROM category WHERE id = "'.$product->category_id.'"')->row();
		$category_discount = isset($category_discount->discount) ? $category_discount->discount : 0;
		
		# DISCOUNT скидка для обычной цены
		$product->discount = $category_discount*1 ?  $category_discount : $product->discount;
		
		# PRICES
		$product->prices = $this->db->query('SELECT 
													pp.filter_item_id,
													pp.cnt_opt,
													pp.cnt_roz,
													pp.opt,
													pp.roz,
													pp.discount,
													fi.name,
													fi.prefix,
													fi.image
												FROM product_prices pp 
												LEFT JOIN filter_item fi ON fi.id = pp.filter_item_id
											WHERE pp.product_id = "'.$id.'"
												ORDER BY pp.order ASC')->result();
												
		$arr = array();
		foreach ($product->prices as $price){
			# добавляем скидку для размера
			$price->discount = $category_discount * 1 ? $category_discount : $price->discount;
			$arr[$price->filter_item_id] = $price;
		}	
		$product->prices = $arr;
		unset($arr);
		
		# FILTERS (без ценообразующих фильтров)
		$product->filters = array();
		# фильтры и значения фильтра  НАДО ДОПИСАТЬ ???!!!!
		/*
		$filter_items = $this->db->query('SELECT f.id AS id_filter, 
												f.name AS name_filter, 
												fi.*
											FROM filter_item fi
											LEFT JOIN filter f ON f.id = fi.id_filter
											LEFT JOIN product_filter_item pfi ON fi.id = pfi.id_filter_item
										WHERE f.visibility = 1 AND f.pricing = 0 AND pfi.product_id = "'.$product->id.'"')->result();
		*/
		
		return $product;
	}
	
	public function getProducts($category_id = 0, $filter = array())
	{
		$category_id = abs((int)$category_id);
		$size = isset($filter['size']) ? $filter['size'] : 0;

		if ( $size ){
			$products = $this->db->query('SELECT p.*, 
												pd.name,
												pd.h1,
												CONCAT("/", p.url, "/", "p", p.id, "/") AS _url
											FROM product p 
											LEFT JOIN product_description pd ON pd.product_id = p.id
											LEFT JOIN product_prices pp ON pp.product_id = p.id 
										WHERE p.visibility = 1 
											AND p.category_id = "'.$category_id.'" 
											AND pp.filter_item_id = "'.$size.'"
										GROUP BY p.id
										ORDER BY p.order ASC')->result();
		}else{
			$products = $this->db->query('SELECT p.*,
												pd.name,
												pd.h1,
												CONCAT("/", p.url, "/", "p", p.id, "/") AS _url
											FROM product p
											LEFT JOIN product_description pd ON pd.product_id = p.id
										WHERE p.visibility = 1 AND p.category_id = "'.$category_id.'" 
											ORDER BY p.order ASC')->result();
		}
		
		return $products;
	}
	
	# продукты категорий со скидками
	public function getProductsDiscount()
	{
		return $this->db->query('SELECT p.*, 
										pd.name,
										pd.h1,
										c.discount, 
										CONCAT("/", p.url, "/", "p", p.id, "/") AS _url
									FROM product p
									LEFT JOIN product_description pd ON pd.product_id = p.id
									LEFT JOIN category c ON c.id = p.category_id
								WHERE p.visibility = 1 AND c.discount <> 0 
									ORDER BY RAND()')->result();
	}
	
	# просмотренные продукты
	public function getViewedProducts($not = 0)
	{
		$not = abs((int)$not);
		$viewed = $this->session->userdata('viewed');
		$ids = $viewed && is_array($viewed) ? array_keys($viewed) : array(0);
		$ids = implode($ids, ',');
		
		return $this->db->query('SELECT p.*,
										pd.name,
										pd.h1,
										c.discount, 
										CONCAT("/", p.url, "/", "p", p.id, "/") AS _url									
									FROM product p
									LEFT JOIN product_description pd ON pd.product_id = p.id
									LEFT JOIN category c ON c.id = p.category_id
								WHERE p.visibility = 1 AND p.id IN('.$ids.') AND p.id <> "'.$not.'"
									ORDER BY FIND_IN_SET(p.id, "'.$ids.'") DESC')->result();
	}
	
	public function searchProducts($word = '') 
	{
		$word = clean($word, true, true);
		
		if (mb_strlen($word) < 3) return array();
		
		$products = $this->db->query('SELECT p.*,
											pd.name,
											pd.h1,
											CONCAT("/", p.url, "/", "p", p.id, "/") AS _url
										FROM product p
										LEFT JOIN product_description pd ON pd.product_id = p.id
									WHERE p.visibility = 1 AND p.name LIKE "%'.$word.'%"
										ORDER BY p.order ASC')->result();
		
		return $products;
	}
	
	# сообщить когда появится товар
	public function towaitlist()
	{
		$id_product = isset($_POST['id_product'])	? abs((int)$_POST['id_product']) : 0;
		$id_color 	= isset($_POST['id_color'])		? abs((int)$_POST['id_color']) : 0;
		$id_size 	= isset($_POST['id_size'])		? abs((int)$_POST['id_size']) : 0;
		$email 		= isset($_POST['email'])		? $_POST['email'] : '';
		$date 		= time();

		$email = preg_match("/^([a-z0-9_\.-]+)@([a-z0-9_\.-]+)$/", trim($email)) ? $email : '';
		
		if ( !$email || !$id_product) return 0;

		# чтоб не забили БД (защита от дебилов)
		// $cnt = $this->db->query('SELECT COUNT(*) AS cnt FROM product_waitlist')->row()->cnt;
		// if ($cnt > 100000){
			// log_message('error', 'Таблица waitlist переполнена: 100000 записей');
		// }
		
		# получить данные о товаре
		$product = $this->productModel->getProduct($id_product);
		
		if ( ! $product) return 0;
		
		# запись в БД 
		$this->db->query('INSERT INTO product_waitlist (products_id, id_size, id_color, email, `date`) 
							VALUES(
								"'.$id_product.'",
								"'.$id_size.'",
								"'.$id_color.'",
								"'.$email.'",
								"'.$date.'"
							)');
		
		
		# отправить письмо админу
		$settings = $this->settingsModel->getSettings();

		$msg = '
		<html>
			<head>
			  <title>Сообщить когда появится товар - '. strtoupper($_SERVER['SERVER_NAME']) .'</title>
			</head>
			<body>
				<h2 style="text-align:center; font-weight:normal;color:#fff;background:#53afea;margin:20px 0 0 0;">СООБЩИТЬ КОГДА ПОЯВИТСЯ</h2>
				<table width="100%" cellpadding="4" cellspacing="0" style="border-collapse:collapse;font-size:14px;text-align:center;">
					<tr style="background:#eee;">
						<td width="1x" style="border:1px solid #ccc"></td>
						<td style="border:1px solid #ccc;">Наименование</small></td>
						<td style="border:1px solid #ccc;"><small>Размер</small></td>
						<td style="border:1px solid #ccc;"><small>E-mail</small></td>
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
						<td style="border:1px solid #ccc;">'. (isset($product->prices[$id_size]) ? $product->prices[$id_size]->name .' '.$product->prices[$id_size]->prefix : '') .'</td>
						<td style="border:1px solid #ccc;">'.$email.'</td>
					</tr>
				</table>
			</body>
		</html>';

		# отправить письмо админу
		/*
		$this->email->clear();
		$this->email->from('admin@crystalline.in.ua', strtoupper($_SERVER['SERVER_NAME']));
		$this->email->to($manager_email);
		$this->email->subject("Сообщить когда появится товар");
		$this->email->message($msg);
		$this->email->send();
		*/
		
		$settings = $this->db->query('SELECT * FROM settings')->row();
		$manager_email = isset($settings->email) ? $settings->email : ''; 
		
		$to			= $manager_email;
		$tema		= 'Сообщить когда появится товар';	
		$headers	= "From: ".strtoupper($_SERVER['SERVER_NAME'])." <admin@".strtolower($_SERVER['SERVER_NAME']).">\r\n";
		$headers	.= "Content-type: text/html; charset=\"utf-8\"";
		mail($to, $tema, $msg, $headers);
		
		return 0;
	}
	
}