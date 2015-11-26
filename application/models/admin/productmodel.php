<?php
class productModel extends CI_Model
{
	public function getProducts($parent = 0)
	{
		$parent = abs((int)$parent);
		return $this->db->query('SELECT *,
										CONCAT("/img/products/", id, "/", id, "_82_82.jpg") AS image
									FROM products 
								WHERE parent = '.$parent.' 
									ORDER BY `order` ASC')->result();
	}
	
	public function getProduct($id = 0)
	{
		$product = $this->db->query('SELECT *,
											CONCAT("/", url, "/p", id) AS _url,
											CONCAT("/img/products/", id, "/", id, "_82_82.jpg") AS image
										FROM products 
									WHERE id = '.$id)->row();
		if ( ! $product) return array();
		
		# IMAGES
		$product->images = $this->db->query('SELECT * FROM product_images WHERE id_product = '.$product->id)->result();
		foreach ($product->images as $k){
			$k->cache = preg_replace('/(.+)(\/.+)$/', '${1}/_cache_${2}', $k->url);
		}
		
		# RELATED
		$product->related = $this->db->query('SELECT p.id, p.name 
													FROM product_related pr 
													LEFT JOIN products p ON pr.child_product = p.id 
												WHERE p.id <> '.$product->id.' AND pr.parent_product = '.$product->id)->result();
		
		# PRICE список цена / размер (OLD)
		// $product->prices = $this->db->query('SELECT * FROM prices WHERE id_product = ' . $product->id)->result();

		# FILTER (NEW)
		$product->filter_item = array();
		$_filter_items = $this->db->query('SELECT id_filter_item FROM product_filter_item WHERE id_product = '.$product->id)->result();
		foreach ($_filter_items as $item){
			$product->filter_item[] = $item->id_filter_item;
		}
		
		# PRICES (NEW)
		$product->prices = $this->db->query('SELECT * FROM product_prices WHERE id_product = '.$product->id)->result();
		
		return $product;
	}
	
	public function addProduct()
	{
		$parent		= isset($_POST['parent']) ? abs((int)$_POST['parent']) : 0;
		$h1			= isset($_POST['h1']) ? clean($_POST['h1'], true, true) : '';
		$name		= isset($_POST['name']) ? clean($_POST['name'], true, true) : '';
		$title		= isset($_POST['title']) ? clean($_POST['title'], true, true) : '';
		$metadesc	= isset($_POST['metadesc']) ? clean($_POST['metadesc'], true, true) : '';
		$metakey	= isset($_POST['metakey']) ? clean($_POST['metakey'], true, true) : '';
		$text		= isset($_POST['text']) ? clean($_POST['text'], false, true) : '';
		$spam       = isset($_POST['spam']) ? clean($_POST['spam'], false, true) : '';
		$manufacturer = isset($_POST['manufacturer']) ? abs((int)$_POST['manufacturer']) : 0;
		$date 		= time();
		
		$url		= translit(mb_strtolower($_POST['url']));
		$url		= mb_strlen($url) ? $url : time();
		
		$price 		= isset($_POST['price']) ? abs(str_replace(',', '.', $_POST['price'])*1) : 0;
		$price_usa	= isset($_POST['price_usa']) ? abs(str_replace(',', '.', $_POST['price_usa'])*1) : 0;
		$discount	= isset($_POST['price-discount']) ? abs(str_replace(',', '.', $_POST['price-discount'])*1) : 0;
		$end_discount = isset($_POST['end-discount']) ? strtotime($_POST['end-discount']) : 0;
		$end_discount = $end_discount ? $end_discount : 0;
		
		
		# COURSE курс доллара данной категории
		$course = $this->db->query('SELECT course FROM category WHERE id = '.$parent)->row();
		$course = isset($course->course) ? $course->course : 0;
		# если есть цена в $ создаем цену в ua
		$price = !$price ? $price_usa * $course : $price;
		
		$this->db->query('INSERT INTO products (parent, h1, name, title, metadesc, metakey, text, spam, url, price, price_usa, discount, end_discount, manufacturer, `date`) 
							   VALUES (
								"'.$parent.'", 
								"'.$h1.'", 
								"'.$name.'", 
								"'.$title.'", 
								"'.$metadesc.'", 
								"'.$metakey.'", 
								"'.$text.'", 
								"'.$spam.'", 
								"'.$url.'", 
								"'.$price.'", 
								"'.$price_usa.'", 
								"'.$discount.'", 
								"'.$end_discount.'",
								"'.$manufacturer.'", 
								"'.$date.'"
							)');
		
		$id = $this->db->query('SELECT MAX(id) AS id FROM products LIMIT 1')->row()->id;
		
		# FILTER_ITEM (id_filter_item) значения фильтров
		if (isset($_POST['id_filter_item'])){
			foreach ($_POST['id_filter_item'] as $k){
				$id_filter_item = abs((int)$k);
				
				$this->db->query('INSERT INTO product_filter_item (id_product, id_filter_item) 
									VALUES(
										"'.$id.'", 
										"'.$id_filter_item.'"
									)');
			}
		}
		
		# PRICES добавляем список цен
		if (isset($_POST['id_filter_item_price'])){
			foreach ($_POST['id_filter_item_price'] as $k=>$v){
				$id_filter_item	= isset($_POST['id_filter_item_price'][$k]) ? abs((int)$_POST['id_filter_item_price'][$k]) : 0;
				
				# добавляем в product_filter_item т.к. это значение фильтра
				$this->db->query('INSERT INTO product_filter_item (id_product, id_filter_item) 
									VALUES(
										"'.$id.'", 
										"'.$id_filter_item.'"
									)');
				
				$cnt_opt  = isset($_POST['cnt_opt'][$k]) ? clean($_POST['cnt_opt'][$k], true, true) : 0;
				$cnt_roz  = isset($_POST['cnt_roz'][$k]) ? clean($_POST['cnt_roz'][$k], true, true) : 0;
				
				$usa_opt  = isset($_POST['usa_opt'][$k]) ? abs((float)$_POST['usa_opt'][$k]) : 0;
				$usa_roz  = isset($_POST['usa_roz'][$k]) ? abs((float)$_POST['usa_roz'][$k]) : 0;
				
				$opt  = isset($_POST['opt'][$k]) ? abs((float)$_POST['opt'][$k]) : 0;
				$roz  = isset($_POST['roz'][$k]) ? abs((float)$_POST['roz'][$k]) : 0;
				
				# если цены в грн. не указаны
				$opt = !$opt ? $usa_opt * $course : $opt;
				$roz = !$roz ? $usa_roz * $course : $roz;

				$discount = isset($_POST['discount'][$k]) ? abs((float)$_POST['discount'][$k]) : 0;
				
				if ( ! $id_filter_item) continue;

				$this->db->query('INSERT INTO product_prices (id_product, id_filter_item, cnt_opt, cnt_roz, opt, roz, usa_opt, usa_roz, discount) 
									VALUES (
										"'.$id.'", 
										"'.$id_filter_item.'", 
										"'.$cnt_opt.'", 
										"'.$cnt_roz.'", 
										"'.$opt.'", 
										"'.$roz.'", 
										"'.$usa_opt.'", 
										"'.$usa_roz.'", 
										"'.$discount.'"
									)');
			}
		}
		
		
		
		# RELATED сопутствующие товары
		if (isset($_POST['related'])){
			foreach ($_POST['related'] as $k=>$v){
				$v = abs((int)trim($v));
				# если это тот же товар
				if ($v == $id) continue;
				
				$this->db->query('INSERT INTO product_related (parent_product, child_product) 
									VALUES(
										"'.$id.'",
										"'.$v.'"
									)');
			}
		}
		
		
		
		# IMAGES добавляем изображения
		foreach ($_POST['product_image'] as &$k){
			$url = clean($k, true, true);
			if ( ! $url) continue;
			$this->db->query('INSERT INTO product_images (id_product, url) 
								VALUES(
									"'.$id.'", 
									"'.$url.'"
								)');
		}
		
		# IMAGE
		$dir = ROOT.'/img/products/'.$id.'/';
		if ( ! is_dir($dir)){ mkdir($dir, 0755, true);}
		
		if (isset($_FILES['image'])){
			$img = $_FILES['image'];
			if ($img['error'] == 0){
				
				$i		= $dir.$id.'.jpg';
				$i82	= $dir.$id.'_82_82.jpg';
				$i150	= $dir.$id.'_150_150.jpg';
				$i300	= $dir.$id.'_300_300.jpg';

				add_watermark_image($img['tmp_name'], $i);
				
				$this->my_imagemagic->resize_square($img['tmp_name'], $i300, 300);
				add_watermark_image($i300, $i300);
				
				$this->my_imagemagic->resize_square($img['tmp_name'], $i150, 150);
				add_watermark_image($i150, $i150);
				
				$this->my_imagemagic->resize_square($img['tmp_name'], $i82, 82);
			}
		}
		
		return;
	}
	
	public function updateProduct()
	{
		$id			= isset($_POST['id']) ? abs((int)$_POST['id']) : 0;
		$parent		= isset($_POST['parent']) ? abs((int)$_POST['parent']) : 0;
		$h1			= isset($_POST['h1']) ? clean($_POST['h1'], true, true) : '';
		$name		= isset($_POST['name']) ? clean($_POST['name'], true, true) : '';
		$title		= isset($_POST['title']) ? clean($_POST['title'], true, true) : '';
		$metadesc	= isset($_POST['metadesc']) ? clean($_POST['metadesc'], true, true) : '';
		$metakey	= isset($_POST['metakey']) ? clean($_POST['metakey'], true, true) : '';
		$text		= isset($_POST['text']) ? clean($_POST['text'], false, true) : '';
		$spam       = isset($_POST['spam']) ? clean($_POST['spam'], false, true) : '';
		$manufacturer = isset($_POST['manufacturer']) ? abs((int)$_POST['manufacturer']) : 0;
		
		$url		= translit(mb_strtolower($_POST['url']));
		$url		= mb_strlen($url) ? $url : time();
		
		$price 		= isset($_POST['price']) ? abs((float) str_replace(',', '.', $_POST['price'])) : 0;
		$price_usa	= isset($_POST['price_usa']) ? abs((float) str_replace(',', '.', $_POST['price_usa'])) : 0;
		$discount	= isset($_POST['price-discount']) ? abs((float) str_replace(',', '.', $_POST['price-discount'])) : 0;
		$end_discount = isset($_POST['end-discount']) ? strtotime($_POST['end-discount']) : 0;
		$end_discount = $end_discount ? $end_discount : 0;
		
		# COURSE курс доллара данной категории
		$course = $this->db->query('SELECT course FROM category WHERE id = '.$parent)->row();
		$course = isset($course->course) ? $course->course : 0;
		$price = !$price ? $price_usa * $course : $price;
		
		$res = $this->db->query('UPDATE products 
									SET 
										parent	= "'.$parent.'",
										h1		= "'.$h1.'",
										name	= "'.$name.'", 
										title	= "'.$title.'", 
										metadesc= "'.$metadesc.'", 
										metakey	= "'.$metakey.'", 
										text	= "'.$text.'",
										spam	= "'.$spam.'",
										url		= "'.$url.'",
										price	= "'.$price.'",
										price_usa = "'.$price_usa.'",
										discount = "'.$discount.'",
										end_discount = "'.$end_discount.'",
										manufacturer = "'.$manufacturer.'"
									WHERE id = '.$id);
		
		# FILTER_ITEM (id_filter_item) значения фильтров
		$this->db->query('DELETE FROM product_filter_item WHERE id_product = '.$id);
		if (isset($_POST['id_filter_item'])){
			foreach ($_POST['id_filter_item'] as $k){
				$id_filter_item = abs((int)$k);
				
				$this->db->query('INSERT INTO product_filter_item (id_product, id_filter_item) 
									VALUES(
										"'.$id.'", 
										"'.$id_filter_item.'"
									)');
			}
		}
		
		# PRICES добавляем список цен
		$this->db->query('DELETE FROM product_prices WHERE id_product = '. $id);
		if (isset($_POST['id_filter_item_price'])){
			foreach ($_POST['id_filter_item_price'] as $k=>$v){
				$id_filter_item	= isset($_POST['id_filter_item_price'][$k]) ? abs((int)$_POST['id_filter_item_price'][$k]) : 0;
				
				# добавляем в product_filter_item т.к. это значение фильтра
				$this->db->query('INSERT INTO product_filter_item (id_product, id_filter_item) 
									VALUES(
										"'.$id.'", 
										"'.$id_filter_item.'"
									)');
				
				$cnt_opt  = isset($_POST['cnt_opt'][$k]) ? clean($_POST['cnt_opt'][$k], true, true) : 0;
				$cnt_roz  = isset($_POST['cnt_roz'][$k]) ? clean($_POST['cnt_roz'][$k], true, true) : 0;
				
				$usa_opt  = isset($_POST['usa_opt'][$k]) ? abs((float)$_POST['usa_opt'][$k]) : 0;
				$usa_roz  = isset($_POST['usa_roz'][$k]) ? abs((float)$_POST['usa_roz'][$k]) : 0;
				
				$opt  = isset($_POST['opt'][$k]) ? abs((float)$_POST['opt'][$k]) : 0;
				$roz  = isset($_POST['roz'][$k]) ? abs((float)$_POST['roz'][$k]) : 0;
				
				# если цены в грн. не указаны
				$opt = !$opt ? $usa_opt * $course : $opt;
				$roz = !$roz ? $usa_roz * $course : $roz;

				$discount = isset($_POST['discount'][$k]) ? abs((float)$_POST['discount'][$k]) : 0;
				
				//if ( ! $id_filter_item) continue;

				$this->db->query('INSERT INTO product_prices (id_product, id_filter_item, cnt_opt, cnt_roz, opt, roz, usa_opt, usa_roz, discount) 
									VALUES (
										"'.$id.'", 
										"'.$id_filter_item.'", 
										"'.$cnt_opt.'", 
										"'.$cnt_roz.'", 
										"'.$opt.'", 
										"'.$roz.'", 
										"'.$usa_opt.'", 
										"'.$usa_roz.'", 
										"'.$discount.'"
									)');
			}
		}
		
		# RELATED сопутствующие товары
		$this->db->query('DELETE FROM product_related WHERE parent_product = '.$id);
		if (isset($_POST['related'])){
			foreach ($_POST['related'] as $k=>$v){
				$v = abs((int)trim($v));
				# если это тот же товар
				if ($v == $id) continue;
				
				$this->db->query('INSERT INTO product_related (parent_product, child_product) 
									VALUES(
										"'.$id.'",
										"'.$v.'"
									)');
			}
		}
		
		# IMAGES перезаписываем адреса изображений
		$this->db->query('DELETE FROM product_images WHERE id_product = '.$id);
		foreach ($_POST['product_image'] as &$k){
			$url = clean($k, true, true);
			if ( ! $url) continue;
			$this->db->query('INSERT INTO product_images (id_product, url) 
									VALUES(
										"'.$id.'", 
										"'.$url.'"
									)');
		}							
		
		# IMAGE
		$dir = ROOT.'/img/products/'.$id.'/';
		if ( ! is_dir($dir)){ mkdir($dir, 0755, true);}
		
		if (isset($_FILES['image'])){
			$img = $_FILES['image'];
			if ($img['error'] == 0){
				$i		= $dir.$id.'.jpg';
				$i82	= $dir.$id.'_82_82.jpg';
				$i150	= $dir.$id.'_150_150.jpg';
				$i300	= $dir.$id.'_300_300.jpg';
				
				add_watermark_image($img['tmp_name'], $i);
				
				$this->my_imagemagic->resize_square($img['tmp_name'], $i300, 300);
				add_watermark_image($i300, $i300);
				
				$this->my_imagemagic->resize_square($img['tmp_name'], $i150, 150);
				add_watermark_image($i150, $i150);
				
				$this->my_imagemagic->resize_square($img['tmp_name'], $i82, 82);
			}
		}

		return;
	}
	
	public function setNewProduct()
	{
		$id		= isset($_POST['id']) ? abs((int)$_POST['id']): 0;
		$activ	= (int)$_POST['activ'] ? 0 : 1;
		
		$this->db->query('UPDATE products SET new = '.$activ.' WHERE id = '.$id);
		
		return $activ;
	}
	
	public function setHitProduct()
	{
		$id		= isset($_POST['id']) ? abs((int)$_POST['id']): 0;
		$activ	= (int)$_POST['activ'] ? 0 : 1;
		
		$this->db->query('UPDATE products SET hit = '.$activ.' WHERE id = '.$id);
		
		return $activ;
	}
	
	public function sortOrderProduct()
	{
		if (isset($_POST['product_order'])){
			for ($i = 0, $cnt = count($_POST['product_order']); $i < $cnt; $i++){
				$product_id = isset($_POST['product_id'][$i]) ? abs((int)$_POST['product_id'][$i]) : 0;
				$product_order = isset($_POST['product_order'][$i]) ? abs((int)$_POST['product_order'][$i]) : 0;
				
				$this->db->query('UPDATE products SET `order` = '.$product_order.' WHERE id = '.$product_id);
			}
		}
		
		return;
	}
	
	public function setVisibilityProduct()
	{
		$id		= isset($_POST['id']) ? abs((int)$_POST['id']): 0;
		$activ	= (int)$_POST['activ'] ? 0 : 1;
		
		$this->db->query('UPDATE products SET visibility = '.$activ.' WHERE id = '.$id);
		
		return $activ;
	}
	
	public function delProduct($id = 0)
	{
		$id = abs((int)$id);
		$this->db->query('DELETE FROM products WHERE id = '.$id);
		
		$dir = ROOT.'/img/products/'.$id;
		
		if (is_dir($dir)){
			delDir($dir);
		}

		return 0;
	}
	
}


