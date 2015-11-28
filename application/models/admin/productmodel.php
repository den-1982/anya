<?php
class productModel extends CI_Model
{
	public function getProducts($category_id = 0)
	{
		$category_id = abs((int)$category_id);
		$products = $this->db->query('SELECT p.*,
											pd.name
										FROM product p
										LEFT JOIN product_description pd ON pd.product_id = p.id
									WHERE p.category_id = "'.$category_id.'" 
										GROUP BY p.id
										ORDER BY p.order ASC')->result();
		
		foreach ($products as $product){
			$product->cache = preg_replace('/(.+)(\/.+)$/', '${1}/_cache_${2}', $product->image);
		}
		
		return $products;
	}
	
	public function getProduct($id = 0)
	{
		$id = abs((int)$id);
		$product = $this->db->query('SELECT *,
											CONCAT("/", p.url, "/p", id) AS _url
										FROM product p
										LEFT JOIN product_description pd ON pd.product_id = p.id
									WHERE p.id = "'.$id.'"')->row();
		if ( ! $product) return array();
		
		# IMAGE
		$product->cache = preg_replace('/(.+)(\/.+)$/', '${1}/_cache_${2}', $product->image);
		
		# IMAGES
		$product->images = $this->db->query('SELECT * FROM product_images WHERE product_id = "'.$product->id.'"')->result();
		foreach ($product->images as $k){
			$k->cache = preg_replace('/(.+)(\/.+)$/', '${1}/_cache_${2}', $k->image);
		}
		
		# VIDEO
		$product->video = $this->db->query('SELECT * FROM product_video WHERE product_id = "'.$product->id.'"')->result();
		
		# RELATED
		$product->related = $this->db->query('SELECT p.id, pd.name, p.image
													FROM product_related pr 
													LEFT JOIN product p ON pr.related_id = p.id 
													LEFT JOIN product_description pd ON pd.product_id = p.id 
												WHERE p.id <> "'.$product->id.'" AND pr.product_id = "'.$product->id.'"')->result();
		foreach ($product->related as $k){
			$k->cache = preg_replace('/(.+)(\/.+)$/', '${1}/_cache_${2}', $k->image);
		}
		
		
		# MANUFACTURER
		$product->manufacturer = $this->db->query('SELECT * FROM manufacturer WHERE id = "'.$product->manufacturer_id.'"')->row();
		
		# FILTER
		$product->filter_items = array();
		$_filter_items = $this->db->query('SELECT filter_item_id FROM product_filter_item WHERE product_id = "'.$product->id.'"')->result();
		foreach ($_filter_items as $item){
			$product->filter_items[] = $item->filter_item_id;
		}
		unset($_filter_items);
		
		# PRICES
		$product->prices = $this->db->query('SELECT * FROM product_prices WHERE product_id = "'.$product->id.'"')->result();
		
		
		return $product;
	}
	
	public function addProduct()
	{
		$category_id	= isset($_POST['category_id'])		? abs((int)$_POST['category_id']) : 0;
		$manufacturer_id= isset($_POST['manufacturer_id'])	? abs((int)$_POST['manufacturer_id']) : 0;
		$image			= isset($_POST['image'])			? clean($_POST['image'], true, true) : '';
		$url			= isset($_POST['url'])				? translit(mb_strtolower($_POST['url'])) : '';
		$url			= mb_strlen($url) ? $url : time();

		$price			= isset($_POST['price'])			? abs(str_replace(',', '.', $_POST['price'])*1) : 0;
		$price_usa		= isset($_POST['price_usa'])		? abs(str_replace(',', '.', $_POST['price_usa'])*1) : 0;
		$discount		= isset($_POST['price-discount'])	? abs(str_replace(',', '.', $_POST['price-discount'])*1) : 0;
		$end_discount	= isset($_POST['end-discount'])		? strtotime($_POST['end-discount']) : 0;
		$end_discount	= $end_discount ? $end_discount : 0;
		
		
		# COURSE курс доллара данной категории
		$course = $this->db->query('SELECT course FROM category WHERE id = "'.$category_id.'"')->row();
		$course = isset($course->course) ? $course->course : 0;
		# если есть цена в $ создаем цену в ua
		$price = ( ! $price) ? $price_usa * $course : $price;
		
		$this->db->query('INSERT INTO product (category_id, image, url, price, price_usa, discount, end_discount, manufacturer_id) 
							   VALUES (
								"'.$category_id.'",
								"'.$image.'", 
								"'.$url.'", 
								"'.$price.'", 
								"'.$price_usa.'", 
								"'.$discount.'", 
								"'.$end_discount.'",
								"'.$manufacturer_id.'"
							)');
		
		# ID PRODUCT
		$id = $this->db->query('SELECT MAX(id) AS id FROM product LIMIT 1')->row()->id;
		
		# DESCRIPTION
		$h1			= isset($_POST['h1'])		? clean($_POST['h1'], true, true) : '';
		$name		= isset($_POST['name'])		? clean($_POST['name'], true, true) : '';
		$title		= isset($_POST['title'])	? clean($_POST['title'], true, true) : '';
		$metadesc	= isset($_POST['metadesc'])	? clean($_POST['metadesc'], true, true) : '';
		$metakey	= isset($_POST['metakey'])	? clean($_POST['metakey'], true, true) : '';
		$text		= isset($_POST['text'])		? clean($_POST['text'], false, true) : '';
		$spam       = isset($_POST['spam'])		? clean($_POST['spam'], false, true) : '';
		
		$this->db->query('INSERT INTO product_description (product_id, h1, name, title, metadesc, metakey, text, spam) 
							   VALUES (
								"'.$id.'", 
								"'.$h1.'", 
								"'.$name.'", 
								"'.$title.'", 
								"'.$metadesc.'", 
								"'.$metakey.'", 
								"'.$text.'", 
								"'.$spam.'"
							)');
		
		
		# FILTER_ITEM (id_filter_item) значения фильтров
		if (isset($_POST['product']['filter_item']['id'])) foreach ($_POST['product']['filter_item']['id'] as $k=>$v){
				$p['filter_item_id'] = isset($_POST['product']['filter_item']['id'][$k]) ? abs((int)$_POST['product']['filter_item']['id'][$k]) : 0; 
				
				$this->db->query('INSERT INTO product_filter_item (product_id, filter_item_id) 
									VALUES(
										"'.$id.'", 
										"'.$p['filter_item_id'].'"
									)');
		}
		
		# PRICES добавляем список цен
		if (isset($_POST['product']['prices']['filter_item_id'])) foreach ($_POST['product']['prices']['filter_item_id'] as $k=>$v){
			$p['filter_item_id'] = isset($_POST['product']['prices']['filter_item_id'][$k]) ? abs((int)$_POST['product']['prices']['filter_item_id'][$k]) : 0; 
			
			# добавляем в product_filter_item т.к. это значение фильтра
			$this->db->query('INSERT INTO product_filter_item (product_id, filter_item_id) 
								VALUES(
									"'.$id.'", 
									"'.$p['filter_item_id'].'"
								)');
			
			
			$p['cnt_opt'] = isset($_POST['product']['prices']['cnt_opt'][$k]) ? clean($_POST['product']['prices']['cnt_opt'][$k], true, true) : 0;
			$p['cnt_roz'] = isset($_POST['product']['prices']['cnt_roz'][$k]) ? clean($_POST['product']['prices']['cnt_roz'][$k], true, true) : 0;
			
			$p['usa_opt'] = isset($_POST['product']['prices']['usa_opt'][$k]) ? abs((float)$_POST['product']['prices']['usa_opt'][$k]) : 0;
			$p['usa_roz'] = isset($_POST['product']['prices']['usa_roz'][$k]) ? abs((float)$_POST['product']['prices']['usa_roz'][$k]) : 0;
			
			$p['opt'] = isset($_POST['product']['prices']['opt'][$k]) ? abs((float)$_POST['product']['prices']['opt'][$k]) : 0;
			$p['roz'] = isset($_POST['product']['prices']['roz'][$k]) ? abs((float)$_POST['product']['prices']['roz'][$k]) : 0;
			
			$p['discount'] = isset($_POST['product']['prices']['discount'][$k]) ? abs((float)$_POST['product']['prices']['discount'][$k]) : 0;
			
			# если цены в грн. не указаны
			$p['opt'] = !$p['opt'] ? $p['usa_opt'] * $course : $p['opt'];
			$p['roz'] = !$p['roz'] ? $p['usa_roz'] * $course : $p['roz'];
			
			if ( ! $p['filter_item_id']) continue;

			$this->db->query('INSERT INTO product_prices (product_id, filter_item_id, cnt_opt, cnt_roz, opt, roz, usa_opt, usa_roz, discount) 
								VALUES (
									"'.$id.'", 
									"'.$p['filter_item_id'].'", 
									"'.$p['cnt_opt'].'", 
									"'.$p['cnt_roz'].'", 
									"'.$p['opt'].'", 
									"'.$p['roz'].'", 
									"'.$p['usa_opt'].'", 
									"'.$p['usa_roz'].'", 
									"'.$p['discount'].'"
								)');
		}
		
		# RELATED
		if (isset($_POST['product']['related']['id'])) foreach ($_POST['product']['related']['id'] as $k=>$v){
				$p['related_id'] = isset($_POST['product']['related']['id'][$k]) ? abs((int)$_POST['product']['related']['id'][$k]): 0;
				
				# если это тот же товар
				if ($p['related_id'] == $id) continue;
				
				$this->db->query('INSERT INTO product_related (product_id, related_id) 
									VALUES(
										"'.$id.'",
										"'.$p['related_id'].'"
									)');
		}

		# IMAGES
		if (isset($_POST['product']['images']['image'])) foreach ($_POST['product']['images']['image'] as $k=>$v){
			$p['image'] = isset($_POST['product']['images']['image'][$k]) ? clean($_POST['product']['images']['image'][$k], false, true): '';
			$p['alt']	= isset($_POST['product']['images']['alt'][$k]) ? clean($_POST['product']['images']['alt'][$k], true, true): '';
			
			if ( ! $p['image']) continue;
			
			$this->db->query('INSERT INTO product_images (product_id, image, alt) 
								VALUES(
									"'.$id.'", 
									"'.$p['image'].'",
									"'.$p['alt'].'"
								)');
		}
		
		# VIDEO
		if (isset($_POST['product']['video']['image'])) foreach ($_POST['product']['video']['image'] as $k=>$v){
			$p['image'] = isset($_POST['product']['video']['image'][$k])	? clean($_POST['product']['video']['image'][$k], true, true): '';
			$p['url']	= isset($_POST['product']['video']['url'][$k])		? clean($_POST['product']['video']['url'][$k], true, true): '';
			$p['video']	= isset($_POST['product']['video']['video'][$k])	? clean($_POST['product']['video']['video'][$k], true, true): '';
			$p['name']	= isset($_POST['product']['video']['name'][$k])		? clean($_POST['product']['video']['name'][$k], true, true): '';
			$p['text']	= isset($_POST['product']['video']['text'][$k])		? clean($_POST['product']['video']['text'][$k], true, true): '';
			
			if ( ! $p['video']) continue;
			
			$this->db->query('INSERT INTO product_video (product_id, image, url, video, name, text) 
								VALUES(
									"'.$id.'", 
									"'.$p['image'].'",
									"'.$p['url'].'",
									"'.$p['video'].'",
									"'.$p['name'].'",
									"'.$p['text'].'"
								)');
		}

		return;
	}
	
	public function updateProduct()
	{
		$id				= isset($_POST['id'])				? abs((int)$_POST['id']) : 0;
		$category_id	= isset($_POST['category_id'])		? abs((int)$_POST['category_id']) : 0;
		$manufacturer_id= isset($_POST['manufacturer_id'])	? abs((int)$_POST['manufacturer_id']) : 0;
		$image			= isset($_POST['image'])			? clean($_POST['image'], true, true) : '';
		$url			= isset($_POST['url'])				? translit(mb_strtolower($_POST['url'])) : '';
		$url			= mb_strlen($url) ? $url : time();

		$price			= isset($_POST['price'])			? abs(str_replace(',', '.', $_POST['price'])*1) : 0;
		$price_usa		= isset($_POST['price_usa'])		? abs(str_replace(',', '.', $_POST['price_usa'])*1) : 0;
		$discount		= isset($_POST['price-discount'])	? abs(str_replace(',', '.', $_POST['price-discount'])*1) : 0;
		$end_discount	= isset($_POST['end-discount'])		? strtotime($_POST['end-discount']) : 0;
		$end_discount	= $end_discount ? $end_discount : 0;
		
		
		# COURSE курс доллара данной категории
		$course = $this->db->query('SELECT course FROM category WHERE id = "'.$category_id.'"')->row();
		$course = isset($course->course) ? $course->course : 0;
		# если есть цена в $ создаем цену в ua
		$price = ( ! $price) ? $price_usa * $course : $price;
		
		$this->db->query('UPDATE product SET
								category_id = "'.$category_id.'",
								image = "'.$image.'", 
								url = "'.$url.'", 
								price = "'.$price.'", 
								price_usa = "'.$price_usa.'", 
								discount = "'.$discount.'", 
								end_discount = "'.$end_discount.'",
								manufacturer_id = "'.$manufacturer_id.'"
							WHERE id = "'.$id.'"');
		
		# DESCRIPTION
		$h1			= isset($_POST['h1'])		? clean($_POST['h1'], true, true) : '';
		$name		= isset($_POST['name'])		? clean($_POST['name'], true, true) : '';
		$title		= isset($_POST['title'])	? clean($_POST['title'], true, true) : '';
		$metadesc	= isset($_POST['metadesc'])	? clean($_POST['metadesc'], true, true) : '';
		$metakey	= isset($_POST['metakey'])	? clean($_POST['metakey'], true, true) : '';
		$text		= isset($_POST['text'])		? clean($_POST['text'], false, true) : '';
		$spam       = isset($_POST['spam'])		? clean($_POST['spam'], false, true) : '';
		
		$this->db->query('DELETE FROM product_description WHERE product_id = "'.$id.'"');
		$this->db->query('INSERT INTO product_description (product_id, h1, name, title, metadesc, metakey, text, spam) 
							   VALUES (
								"'.$id.'", 
								"'.$h1.'", 
								"'.$name.'", 
								"'.$title.'", 
								"'.$metadesc.'", 
								"'.$metakey.'", 
								"'.$text.'", 
								"'.$spam.'"
							)');
		
		
		# FILTER_ITEM (id_filter_item) значения фильтров
		$this->db->query('DELETE FROM product_filter_item WHERE product_id = "'.$id.'"');
		if (isset($_POST['product']['filter_item']['id'])) foreach ($_POST['product']['filter_item']['id'] as $k=>$v){
				$p['filter_item_id'] = isset($_POST['product']['filter_item']['id'][$k]) ? abs((int)$_POST['product']['filter_item']['id'][$k]) : 0; 
				
				$this->db->query('INSERT IGNORE INTO product_filter_item (product_id, filter_item_id) 
									VALUES(
										"'.$id.'", 
										"'.$p['filter_item_id'].'"
									)');
		}
		
		# PRICES добавляем список цен
		$this->db->query('DELETE FROM product_prices WHERE product_id = "'.$id.'"');
		if (isset($_POST['product']['prices']['filter_item_id'])) foreach ($_POST['product']['prices']['filter_item_id'] as $k=>$v){
			$p['filter_item_id'] = isset($_POST['product']['prices']['filter_item_id'][$k]) ? abs((int)$_POST['product']['prices']['filter_item_id'][$k]) : 0; 
			
			# добавляем в product_filter_item т.к. это значение фильтра
			$this->db->query('INSERT IGNORE INTO product_filter_item (product_id, filter_item_id) 
								VALUES(
									"'.$id.'", 
									"'.$p['filter_item_id'].'"
								)');
			
			
			$p['cnt_opt'] = isset($_POST['product']['prices']['cnt_opt'][$k]) ? clean($_POST['product']['prices']['cnt_opt'][$k], true, true) : 0;
			$p['cnt_roz'] = isset($_POST['product']['prices']['cnt_roz'][$k]) ? clean($_POST['product']['prices']['cnt_roz'][$k], true, true) : 0;
			
			$p['usa_opt'] = isset($_POST['product']['prices']['usa_opt'][$k]) ? abs((float)$_POST['product']['prices']['usa_opt'][$k]) : 0;
			$p['usa_roz'] = isset($_POST['product']['prices']['usa_roz'][$k]) ? abs((float)$_POST['product']['prices']['usa_roz'][$k]) : 0;
			
			$p['opt'] = isset($_POST['product']['prices']['opt'][$k]) ? abs((float)$_POST['product']['prices']['opt'][$k]) : 0;
			$p['roz'] = isset($_POST['product']['prices']['roz'][$k]) ? abs((float)$_POST['product']['prices']['roz'][$k]) : 0;
			
			$p['discount'] = isset($_POST['product']['prices']['discount'][$k]) ? abs((float)$_POST['product']['prices']['discount'][$k]) : 0;
			
			# если цены в грн. не указаны
			$p['opt'] = !$p['opt'] ? $p['usa_opt'] * $course : $p['opt'];
			$p['roz'] = !$p['roz'] ? $p['usa_roz'] * $course : $p['roz'];
			
			if ( ! $p['filter_item_id']) continue;

			$this->db->query('INSERT INTO product_prices (product_id, filter_item_id, cnt_opt, cnt_roz, opt, roz, usa_opt, usa_roz, discount) 
								VALUES (
									"'.$id.'", 
									"'.$p['filter_item_id'].'", 
									"'.$p['cnt_opt'].'", 
									"'.$p['cnt_roz'].'", 
									"'.$p['opt'].'", 
									"'.$p['roz'].'", 
									"'.$p['usa_opt'].'", 
									"'.$p['usa_roz'].'", 
									"'.$p['discount'].'"
								)');
		}
		
		# RELATED
		$this->db->query('DELETE FROM product_related WHERE product_id = "'.$id.'"');
		if (isset($_POST['product']['related']['id'])) foreach ($_POST['product']['related']['id'] as $k=>$v){
				$p['related_id'] = isset($_POST['product']['related']['id'][$k]) ? abs((int)$_POST['product']['related']['id'][$k]): 0;
				
				# если это тот же товар
				if ($p['related_id'] == $id) continue;
				
				$this->db->query('INSERT IGNORE INTO product_related (product_id, related_id) 
									VALUES(
										"'.$id.'",
										"'.$p['related_id'].'"
									)');
		}

		# IMAGES
		$this->db->query('DELETE FROM product_images WHERE product_id = "'.$id.'"');
		if (isset($_POST['product']['images']['image'])) foreach ($_POST['product']['images']['image'] as $k=>$v){
			$p['image'] = isset($_POST['product']['images']['image'][$k]) ? clean($_POST['product']['images']['image'][$k], false, true): '';
			$p['alt']	= isset($_POST['product']['images']['alt'][$k]) ? clean($_POST['product']['images']['alt'][$k], true, true): '';
			
			if ( ! $p['image']) continue;
			
			$this->db->query('INSERT INTO product_images (product_id, image, alt) 
								VALUES(
									"'.$id.'", 
									"'.$p['image'].'",
									"'.$p['alt'].'"
								)');
		}
		
		# VIDEO
		$this->db->query('DELETE FROM product_video WHERE product_id = "'.$id.'"');
		if (isset($_POST['product']['video']['image'])) foreach ($_POST['product']['video']['image'] as $k=>$v){
			$p['image'] = isset($_POST['product']['video']['image'][$k])	? clean($_POST['product']['video']['image'][$k], true, true): '';
			$p['url']	= isset($_POST['product']['video']['url'][$k])		? clean($_POST['product']['video']['url'][$k], true, true): '';
			$p['video']	= isset($_POST['product']['video']['video'][$k])	? clean($_POST['product']['video']['video'][$k], true, true): '';
			$p['name']	= isset($_POST['product']['video']['name'][$k])		? clean($_POST['product']['video']['name'][$k], true, true): '';
			$p['text']	= isset($_POST['product']['video']['text'][$k])		? clean($_POST['product']['video']['text'][$k], true, true): '';
			
			if ( ! $p['video']) continue;
			
			$this->db->query('INSERT INTO product_video (product_id, image, url, video, name, text) 
								VALUES(
									"'.$id.'", 
									"'.$p['image'].'",
									"'.$p['url'].'",
									"'.$p['video'].'",
									"'.$p['name'].'",
									"'.$p['text'].'"
								)');
		}

		
		return;
	}
	
	public function setNewProduct()
	{
		$id		= isset($_POST['id']) ? abs((int)$_POST['id']): 0;
		$activ	= (int)$_POST['activ'] ? 0 : 1;
		
		$this->db->query('UPDATE product SET new = "'.$activ.'" WHERE id = "'.$id.'"');
		
		return $activ;
	}
	
	public function setHitProduct()
	{
		$id		= isset($_POST['id']) ? abs((int)$_POST['id']): 0;
		$activ	= (int)$_POST['activ'] ? 0 : 1;
		
		$this->db->query('UPDATE product SET hit = "'.$activ.'" WHERE id = "'.$id.'"');
		
		return $activ;
	}
	
	public function sortOrderProduct()
	{
		if (isset($_POST['product_order'])) foreach ($_POST['product_order'] as $k=>$v){
			$product_id		= isset($_POST['product_id'][$k])		? abs((int)$_POST['product_id'][$k]) : 0;
			$product_order	= isset($_POST['product_order'][$k])	? abs((int)$_POST['product_order'][$k]) : 0;
			
			$this->db->query('UPDATE product SET `order` = "'.$product_order.'" WHERE id = "'.$product_id.'"');
		}
		
		return;
	}
	
	public function setVisibilityProduct()
	{
		$id		= isset($_POST['id']) ? abs((int)$_POST['id']): 0;
		$activ	= (int)$_POST['activ'] ? 0 : 1;
		
		$this->db->query('UPDATE product SET visibility = "'.$activ.'" WHERE id = "'.$id.'"');
		
		return $activ;
	}
	
	public function delProduct($id = 0)
	{
		$id = abs((int)$id);
		$this->db->query('DELETE FROM product WHERE id = "'.$id.'"');

		return 0;
	}
	
}