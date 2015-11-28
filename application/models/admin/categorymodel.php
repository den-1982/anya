<?php
class categoryModel extends CI_Model
{
	public function getCategories()
	{	
		return $this->db->query('SELECT c.*,
										cd.name,
										(SELECT COUNT(*) FROM category WHERE parent = c.id) AS cnt_childs 
									FROM category c
									LEFT JOIN category_description cd ON cd.category_id = c.id 
										GROUP BY c.id
										ORDER BY `order` ASC')->result();
	}
	
	public function getCategory($id = 0)
	{
		$id = abs((int)$id);
		$category =  $this->db->query('SELECT * 
											FROM category c
											LEFT JOIN category_description cd ON cd.category_id = c.id
										WHERE c.id = '.$id)->row();
		
		if ( ! $category) return array();
		
		# IMAGE
		$category->cache = preg_replace('/(.+)(\/.+)$/', '${1}/_cache_${2}', $category->image);
		
		# SLIDER
		$category->slider = $this->db->query('SELECT * FROM category_slider WHERE category_id = "'.$category->id.'" ORDER BY `order` ASC')->result();
		foreach ($category->slider as $k){
			$k->cache = preg_replace('/(.+)(\/.+)$/', '${1}/_cache_${2}', $k->image);
		}
		
		return $category;
	}
	
	public function sortCategories($categories = array())
	{
		$data = array();
		foreach ($categories as $category) {
			$data[$category->parent][$category->id] = $category;
		}
		return $data;
	}
	
	public function addCategory()
	{
		$parent		= isset($_POST['parent'])	? abs((int)$_POST['parent']) : 0;
		$image		= isset($_POST['image'])	? clean($_POST['image'], true, true) : '';
		$discount 	= isset($_POST['discount'])	? abs((float)$_POST['discount']) : 0;
		$course		= isset($_POST['course'])	? abs((float)$_POST['course']) : 0;
		$url		= isset($_POST['url'])		? translit(mb_strtolower($_POST['url'])) : '';
		$url		= mb_strlen($url) ? $url : time();
		
		$this->db->query('INSERT INTO category (parent, image, url, discount, course) 
							   VALUES (
								"'.$parent.'",
								"'.$image.'",
								"'.$url.'", 
								"'.$discount.'", 
								"'.$course.'"
							)');
		
		# ID CATEGORY
		$id = $this->db->query('SELECT MAX(id) AS id FROM category LIMIT 1')->row()->id;
		
		# DESCRIPTION
		$h1			= isset($_POST['h1'])		? clean($_POST['h1'], false, true)		: '';
		$name       = isset($_POST['name'])		? clean($_POST['name'], true, true)		: 'empty';
		$title      = isset($_POST['title'])	? clean($_POST['title'], true, true)	: '';
		$metadesc   = isset($_POST['metadesc'])	? clean($_POST['metadesc'], true, true)	: '';
		$metakey    = isset($_POST['metakey'])	? clean($_POST['metakey'], true, true)	: '';
		$text       = isset($_POST['text'])		? clean($_POST['text'], false, true)	: '';
		$spam       = isset($_POST['spam'])		? clean($_POST['spam'], true, true)		: '';
		
		$this->db->query('INSERT INTO category_description (category_id, h1, name, title, metadesc, metakey, text, spam) 
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

		# SLIDER
		if (isset($_POST['slider']['image'])) foreach ($_POST['slider']['image'] as $k=>$v){
			$image	= isset($_POST['slider']['image'][$k])	? clean($_POST['slider']['image'][$k], true, true)	: '';
			$link	= isset($_POST['slider']['link'][$k])	? clean($_POST['slider']['link'][$k], true, true)	: '';
			$h1		= isset($_POST['slider']['h1'][$k])		? clean($_POST['slider']['h1'][$k], true, true)		: '';
			$text	= isset($_POST['slider']['text'][$k])	? clean($_POST['slider']['text'][$k], true, true)	: '';
			$order	= isset($_POST['slider']['order'][$k])	? abs((int)$_POST['slider']['order'][$k])			: 0;
			
			if ( ! $image) continue;
			
			$this->db->query('INSERT INTO category_slider (category_id, image, link, h1, text, `order`) 
								VALUES(
									"'.$id.'", 
									"'.$image.'", 
									"'.$link.'", 
									"'.$h1.'", 
									"'.$text.'", 
									"'.$order.'"
								)');
		}
		

		return;
	}
	
	public function updateCategory()
	{
		$id			= isset($_POST['id']) 		? abs((int)$_POST['id']) : 0;
		$parent		= isset($_POST['parent'])	? abs((int)$_POST['parent']) : 0;
		$image		= isset($_POST['image'])	? clean($_POST['image'], true, true) : '';
		$discount 	= isset($_POST['discount'])	? abs((float)$_POST['discount']) : 0;
		$course		= isset($_POST['course'])	? abs((float)$_POST['course']) : 0;
		$url		= isset($_POST['url'])		? translit(mb_strtolower($_POST['url'])) : '';
		$url		= mb_strlen($url) ? $url : time();

		$this->db->query('UPDATE category SET
								parent = "'.$parent.'",
								image = "'.$image.'",
								url = "'.$url.'", 
								discount = "'.$discount.'", 
								course = "'.$course.'"
							WHERE id = "'.$id.'"');
		
		# DESCRIPTION 
		$h1			= isset($_POST['h1'])		? clean($_POST['h1'], false, true)		: '';
		$name       = isset($_POST['name'])		? clean($_POST['name'], true, true)		: 'empty';
		$title      = isset($_POST['title'])	? clean($_POST['title'], true, true)	: '';
		$metadesc   = isset($_POST['metadesc'])	? clean($_POST['metadesc'], true, true)	: '';
		$metakey    = isset($_POST['metakey'])	? clean($_POST['metakey'], true, true)	: '';
		$text       = isset($_POST['text'])		? clean($_POST['text'], false, true)	: '';
		$spam       = isset($_POST['spam'])		? clean($_POST['spam'], true, true)		: '';
		
		$this->db->query('DELETE FROM category_description WHERE category_id = "'.$id.'"');
		$this->db->query('INSERT INTO category_description (category_id, h1, name, title, metadesc, metakey, text, spam) 
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

		# SLIDER
		$this->db->query('DELETE FROM category_slider WHERE category_id = "'.$id.'"');
		if (isset($_POST['slider']['image'])) foreach ($_POST['slider']['image'] as $k=>$v){
			$image	= isset($_POST['slider']['image'][$k])	? clean($_POST['slider']['image'][$k], true, true)	: '';
			$link	= isset($_POST['slider']['link'][$k])	? clean($_POST['slider']['link'][$k], true, true)	: '';
			$h1		= isset($_POST['slider']['h1'][$k])		? clean($_POST['slider']['h1'][$k], true, true)		: '';
			$text	= isset($_POST['slider']['text'][$k])	? clean($_POST['slider']['text'][$k], true, true)	: '';
			$order	= isset($_POST['slider']['order'][$k])	? abs((int)$_POST['slider']['order'][$k])			: 0;
			
			if ( ! $image) continue;
			
			$this->db->query('INSERT INTO category_slider (category_id, image, link, h1, text, `order`) 
								VALUES(
									"'.$id.'", 
									"'.$image.'", 
									"'.$link.'", 
									"'.$h1.'", 
									"'.$text.'", 
									"'.$order.'"
								)');
		}
		
		# пересчитать цены товаров по курсу
		/*
		if ($course*1){
			# выбираем продукты где есть несколько цен
			$products = $this->db->query('SELECT p.id, 
												p.name, 
												(SELECT COUNT(*) FROM product_prices WHERE id_product = p.id) AS cnt 
											FROM products p
										WHERE p.parent = '.$id.'
											HAVING cnt > 0')->result(); 							
			
			foreach ($products as $product){
				$this->db->query('UPDATE product_prices 
											SET 
												opt = usa_opt * '.$course.', 
												roz = usa_roz * '.$course.' 
											WHERE id_product = '.$product->id);
			}

			# выбираем все продукты
			$this->db->query('UPDATE products SET price = price_usa * '.$course.' WHERE parent = '.$id);
		}
		*/
		
		return;
	}
	
	public function setVisibilityCategory()
	{
		$id		= isset($_POST['id'])	? abs((int)$_POST['id']) : 0;
		$activ	= (int)$_POST['activ']	? 0 : 1;
		
		$this->db->query('UPDATE category SET visibility = "'.$activ.'" WHERE id = "'.$id.'"');
		
		return $activ;
	}
	
	public function sortOrderCategory()
	{
		if (isset($_POST['category_order'])) for ($i = 0, $cnt = count($_POST['category_order']); $i < $cnt; $i++){
			$category_id	= isset($_POST['category_id'][$i])		? abs((int)$_POST['category_id'][$i]) : 0;
			$category_order	= isset($_POST['category_order'][$i])	? abs((int)$_POST['category_order'][$i]) : 0;
			
			$this->db->query('UPDATE category SET `order` = "'.$category_order.'" WHERE id = "'.$category_id.'"');
		}
		
		return;
	}
	
	public function delCategory($id = 0)
	{
		$id = abs((int)$id);

		$categories	= $this->db->query('SELECT COUNT(*) AS cnt1 FROM category WHERE parent = "'.$id.'"')->row()->cnt1;
		$products	= $this->db->query('SELECT COUNT(*) AS cnt2 FROM products WHERE parent = "'.$id.'"')->row()->cnt2;
		
		# Запрет на удалени
		if ($categories || $products){
			$html = 
			'<div>
				<p><b>Категория не может быть удалена!</b></p>
				<p>
					Категория имеет:
					<br>
					вложеные категории - ('.$categories.') шт.
					<br>
					прикрепленные товары - ('.$products.') шт.
				</p>
			</div>';
			
			return $response = array(
				'error' => preg_replace('/\s+/', ' ', $html)
			);
		}
		
		# Удаление
		$this->db->query('DELETE FROM category WHERE id = "'.$id.'"');
		
		return 0;
	}
	
}