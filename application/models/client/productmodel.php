<?php
class productModel extends CI_Model
{	
	public function getProduct($id = 0)
	{
		$id = abs((int)$id);
		$product = $this->db->query('SELECT *, 
											CONCAT("/", p.url, "/", "p", p.id, "/") AS _url,
											CONCAT("/img/products/", p.id, "/", p.id, ".jpg") AS image
										FROM products p
									WHERE p.visibility = 1 AND p.id = ' . $id)->row();
		
		if ( ! $product) return array();
		
		# MANUFACTURER
		$product->manufacturer = $this->db->query('SELECT * FROM manufacturer WHERE id = '.$product->manufacturer)->row();
		
		# IMAGES
		$product->images= $this->db->query('SELECT * FROM product_images WHERE id_product = '.$product->id)->result();
		foreach ($product->images as $image){
			$image->mini = preg_replace('/(.*)(\/.+)$/', '$1/_cache_$2', $image->url);
		}
		
		# RELATED сопутствующие товары
		$product->related = $this->db->query('SELECT p.id, 
													p.name, 
													p.new, 
													p.hit, 
													p.url,
													p.discount,
													CONCAT("/", p.url, "/", "p", p.id, "/") AS _url,
													CONCAT("/img/products/", p.id, "/", p.id, "_150_150.jpg") AS image
											FROM product_related pr 
												LEFT JOIN products p ON pr.child_product = p.id 
											WHERE p.id <> '.$product->id.' AND pr.parent_product = '.$product->id)->result();
											
		
		
		
		# DISCOUNT CATEGORY (%) если в категории выставлена скидка, то добавляем в товары product->discount = category->discount
		$category_discount = $this->db->query('SELECT discount FROM category WHERE id = '.$product->parent)->row()->discount;
		
		# DISCOUNT скидка для обычной цены
		$product->discount = $category_discount*1 ?  $category_discount : $product->discount;
		
		
		# PRICES (NEW)
		$product->prices = $this->db->query('SELECT 
													pp.id_filter_item,
													pp.cnt_opt,
													pp.cnt_roz,
													pp.opt,
													pp.roz,
													pp.discount,
													fi.name,
													fi.prefix,
													fi.image
												FROM product_prices pp 
												LEFT JOIN filter_item fi ON pp.id_filter_item = fi.id
											WHERE pp.id_product = '.$id)->result();
												
		$arr = array();
		foreach ($product->prices as $price){
			# добавляем скидку для размера
			$price->discount = $category_discount * 1 ? $category_discount : $price->discount;
			$arr[$price->id_filter_item] = $price;
		}	
		$product->prices = $arr;
		unset($arr);
		
		# FILTERS (без ценообразующих фильтров)
		/*
		$product->filters = array();
		# фильтры и значения фильтра
		$filter_items = $this->db->query('SELECT f.id AS id_filter, 
												f.name AS name_filter, 
												fi.*
											FROM filter_item fi
												LEFT JOIN filter f ON f.id = fi.id_filter
												LEFT JOIN product_filter_item pfi ON fi.id = pfi.id_filter_item
											WHERE f.visibility = 1 AND f.pricing = 0 AND pfi.id_product = '.$product->id)->result();
		*/
		
		return $product;
	}
	
	public function getProducts($parent = 0, $filter = array())
	{
		$size = isset($filter['size']) ? $filter['size'] : 0;
		$parent = abs((int)$parent);
		
		if ($size){
			$products = $this->db->query('SELECT *, 
												CONCAT("/", url, "/", "p", id, "/") AS _url,
												CONCAT("/img/products/", id, "/", id, "_150_150.jpg") AS image
											FROM products p 
											LEFT JOIN product_prices pp ON pp.id_product = p.id 
										WHERE p.visibility = 1 AND p.parent = '.$parent.' AND pp.id_filter_item = '.$size.'
											ORDER BY p.order ASC')->result();
		}else{
			$products = $this->db->query('SELECT *, 
												CONCAT("/", url, "/", "p", id, "/") AS _url,
												CONCAT("/img/products/", id, "/", id, "_150_150.jpg") AS image
											FROM products 
										WHERE visibility = 1 AND parent = '.$parent.' ORDER BY `order` ASC')->result();
		}
		
		return $products;
	}
	
	# COLORS
	public function getProductsByColor($id_filter_item = 0)
	{
		$id_filter_item = abs((int)$id_filter_item);
		
		$products = $this->db->query('SELECT p.*, 
											CONCAT("/", p.url, "/", "p", p.id, "/") AS _url,
											CONCAT("/img/products/", p.id, "/", p.id, "_150_150.jpg") AS image
										FROM products p 
										LEFT JOIN product_filter_item pfi ON pfi.id_product = p.id  
									WHERE p.visibility = 1 AND pfi.id_filter_item = "'.$id_filter_item.'"
										ORDER BY p.order ASC')->result();

		
		return $products;
	}
	
	# продукты категорий со скидками
	public function getProductsDiscount()
	{
		$products = $this->db->query('SELECT p.id, 
											p.name, 
											p.new, 
											p.hit, 
											p.url, 
											c.discount, 
											CONCAT("/", p.url, "/", "p", p.id, "/") AS _url,
											CONCAT("/img/products/", p.id, "/", p.id, "_150_150.jpg") AS image
										FROM products p
											LEFT JOIN category c ON c.id = p.parent
									WHERE p.visibility = 1 AND c.discount <> 0 ORDER BY RAND()')->result();
		
		return $products;
	}
	
	# просмотренные продукты
	public function getViewedProducts($not = 0)
	{
		$not = abs((int)$not);
		$viewed = $this->session->userdata('viewed');
		$ids = $viewed && is_array($viewed) ? array_keys($viewed) : array(0);
		$ids = implode($ids, ',');
		
		return $this->db->query('SELECT p.id, 
										p.name, 
										p.hit, 
										p.new, 
										c.discount, 
										CONCAT("/", p.url, "/", "p", p.id, "/") AS _url,
										CONCAT("/img/products/", p.id, "/", p.id, "_150_150.jpg") AS image										
									FROM products p
										LEFT JOIN category c ON c.id = p.parent
								WHERE p.visibility = 1 AND p.id IN('.$ids.') AND p.id <> '.$not.'
									ORDER BY FIND_IN_SET(p.id, "'.$ids.'") DESC')->result();
	}
	
	public function searchProducts($word = '') 
	{
		$word = trim(preg_replace('/\s+/', ' ', $word));
		
		if (mb_strlen($word) < 3) return array();
		
		$word = mysql_real_escape_string($word);
		
		$products = $this->db->query('SELECT *, 
											CONCAT("/", url, "/", "p", id, "/") AS _url,
											CONCAT("/img/products/", id, "/", id, "_150_150.jpg") AS image
										FROM products 
									WHERE visibility = 1 AND name LIKE "%'.$word.'%"
										ORDER BY `order` ASC')->result();
		
		return $products;
	}
	
	# сообщить когда появится товар
	public function towaitlist()
	{
		$id_product = isset($_POST['id_product']) ? abs((int)$_POST['id_product']) : 0;
		$id_color 	= isset($_POST['id_color']) ? abs((int)$_POST['id_color']) : 0;
		$id_size 	= isset($_POST['id_size']) ? abs((int)$_POST['id_size']) : 0;
		$email 		= isset($_POST['email']) ? $_POST['email'] : '';
		$date 		= time();

		$email = preg_match("/^([a-z0-9_\.-]+)@([a-z0-9_\.-]+)$/", trim($email)) ? $email : '';
		
		if (!$email || !$id_product) return 0;

		# чтоб не забили БД (защита от дебилов)
		$cnt = $this->db->query('SELECT COUNT(*) AS cnt FROM waitlist')->row()->cnt;
		if ($cnt > 100000){
			log_message('error', 'Таблица waitlist переполнена: 100000 записей');
		}
		
		# получить данные о товаре
		$product = $this->productModel->getProduct($id_product);
		
		# проверка ???
		if ( ! $product){
			return 0;
		}
		
		# запись в БД 
		$this->db->query('INSERT INTO waitlist (products_id, id_size, id_color, email, `date`) 
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
				</table>';

		# отправить письмо админу
		
		$this->email->clear();
		$this->email->from('admin@crystalline.in.ua', strtoupper($_SERVER['SERVER_NAME']));
		$this->email->to($manager_email);
		$this->email->subject("Сообщить когда появится товар");
		$this->email->message($msg);
		$this->email->send();
		
		return 0;
	}
}