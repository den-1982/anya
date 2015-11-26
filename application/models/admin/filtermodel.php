<?php
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// ADMIN
class filterModel extends CI_Model
{
	public function getFilter($id = 0)
	{
		$id = abs((int)$id);
		$filter = $this->db->query('SELECT * FROM filter WHERE id = "'.$id.'"')->row();
		
		if ( ! $filter) return array();
		
		# IMAGE
		$filter->cache = preg_replace('/(.+)(\/.+)$/', '${1}/_cache_${2}', $filter->image);
		
		# ITEMS
		$filter->items = $this->db->query('SELECT * FROM filter_item WHERE id_filter = "'.$filter->id.'" ORDER BY `order` ASC')->result();
		foreach ($filter->items as $k){
			$k->cache = preg_replace('/(.+)(\/.+)$/', '${1}/_cache_${2}', $k->image);
		}
		
		
		# CATEGORIES (категории к котрым привязан фильтр)
		$categories = $this->db->query('SELECT * FROM category_filter WHERE id_filter = '.$filter->id)->result();
		
		$filter->categories = array();
		foreach ($categories as $category){
			$filter->categories[] = $category->category_id;
		}


		return $filter;
	}
	
	public function getFilters()
	{
		$filters = $this->db->query('SELECT * FROM filter ORDER BY `order` ASC')->result();
	
		foreach ($filters as $filter){
			$filter->cache = preg_replace('/(.+)(\/.+)$/', '${1}/_cache_${2}', $filter->image);
			$filter->category = array();
			$filter->items = $this->db->query('SELECT * 
													FROM filter_item 
												WHERE id_filter = "'.$filter->id.'" 
													ORDER BY `order` ASC')->result();
		}

		return $filters;
	}
	
	public function getFiltersOfCategory($category_id = 0)
	{
		$category_id = abs((int)$category_id);
		
		$filters = $this->db->query('SELECT * 
										FROM filter f 
										LEFT JOIN category_filter cf ON f.id = cf.id_filter
									WHERE cf.category_id = "'.$category_id.'" AND pricing = 0 
										ORDER BY f.order ASC')->result();
		
		foreach ($filters as $filter){
			$filter->items = $this->db->query('SELECT * 
													FROM filter_item 
												WHERE id_filter = "'.$filter->id.'" 
													ORDER BY `order` ASC')->result();
		}
		
		return $filters;
	}
	
	public function getFilterItemPricing()
	{
		# значения фильтров которые используются в формировании цены
		return $this->db->query('SELECT * 
									FROM filter_item 
								WHERE id_filter IN(
													SELECT id FROM filter WHERE pricing = 1
												)
									ORDER BY `order` ASC')->result();
	}
	
	public function addFilter()
	{
		$name		= isset($_POST['name'])				? clean($_POST['name'], true, true)  : '';
		$image		= isset($_POST['image'])			? clean($_POST['image'], false, true)  : '';
		$pricing	= isset($_POST['filter_pricing'])	? (int)(boolean)$_POST['filter_pricing'] : 0;
		
		$this->db->query('INSERT INTO filter (name, image, pricing) 
							VALUES(
								"'.$name.'",
								"'.$image.'",
								"'.$pricing.'"
							)');
		$id_filter = $this->db->query('SELECT MAX(id) AS id FROM filter')->row()->id;
		
		# значения фильтра
		if (isset($_POST['filter_item_name']['insert'])) foreach ($_POST['filter_item_name']['insert'] as $k=>$v){
			$_name	= isset($_POST['filter_item_name']['insert'][$k])	? clean($_POST['filter_item_name']['insert'][$k], true, true) : '';
			$_image	= isset($_POST['filter_item_image']['insert'][$k])	? clean($_POST['filter_item_image']['insert'][$k], true, true) : '';
			$_prefix= isset($_POST['filter_item_prefix']['insert'][$k])	? clean($_POST['filter_item_prefix']['insert'][$k], true, true) : '';
			$_order	= isset($_POST['filter_item_order']['insert'][$k])	? abs((int)$_POST['filter_item_order']['insert'][$k]) : 0;
				
			if ( ! mb_strlen($_name)) continue;
			
			$this->db->query('INSERT INTO filter_item (id_filter, name, image, prefix, `order`) 
								VALUES(
									"'.$id_filter.'", 
									"'.$_name.'", 
									"'.$_image.'", 
									"'.$_prefix.'", 
									"'.$_order.'"
								)');
		}
		
		# присоединить фильтр к категории
		if (isset($_POST['category_id'])) foreach ($_POST['category_id'] as $k){
			$category_id = abs((int)$k);
			$this->db->query('INSERT INTO category_filter(category_id, id_filter) 
								VALUES(
									"'.$category_id.'", 
									"'.$id_filter.'"
								)');
			
		}
	}
	
	public function updateFilter()
	{
		$id_filter	= isset($_POST['id'])				? abs((int)$_POST['id']): 0;
		$name		= isset($_POST['name'])				? clean($_POST['name'], true, true)  : 'empty';
		$image		= isset($_POST['image'])			? clean($_POST['image'], false, true)  : '';
		$pricing	= isset($_POST['filter_pricing'])	? (int)(boolean)$_POST['filter_pricing'] : 0;
		
		$this->db->query('UPDATE filter 
							SET name = "'.$name.'", 
								image = "'.$image.'",
								pricing = "'.$pricing.'"
							WHERE id = "'.$id_filter.'"');
		
		# удаляем старые значения
		$this->db->query('DELETE FROM filter_item WHERE id_filter = "'.$id_filter.'"');
		# редактируем старые
		if (isset($_POST['filter_item_name']['update'])) foreach ($_POST['filter_item_name']['update'] as $k=>$v){
			$_id	= abs((int)$k);
			$_name	= isset($_POST['filter_item_name']['update'][$k])	? clean($_POST['filter_item_name']['update'][$k], true, true) : '';
			$_image	= isset($_POST['filter_item_image']['update'][$k])	? clean($_POST['filter_item_image']['update'][$k], true, true) : '';
			$_prefix= isset($_POST['filter_item_prefix']['update'][$k])	? clean($_POST['filter_item_prefix']['update'][$k], true, true) : '';
			$_order	= isset($_POST['filter_item_order']['update'][$k])	? abs((int)$_POST['filter_item_order']['update'][$k]) : 0;
				
			if ( ! mb_strlen($_name)) continue;
			
			$this->db->query('INSERT INTO filter_item (id, id_filter, name, image, prefix, `order`) 
								VALUES(
									"'.$_id.'",
									"'.$id_filter.'", 
									"'.$_name.'", 
									"'.$_image.'", 
									"'.$_prefix.'", 
									"'.$_order.'"
								)');
		}
		
		# добавляем новые
		if (isset($_POST['filter_item_name']['insert'])) foreach ($_POST['filter_item_name']['insert'] as $k=>$v){
			$_name	= isset($_POST['filter_item_name']['insert'][$k])	? clean($_POST['filter_item_name']['insert'][$k], true, true) : '';
			$_image	= isset($_POST['filter_item_image']['insert'][$k])	? clean($_POST['filter_item_image']['insert'][$k], true, true) : '';
			$_prefix= isset($_POST['filter_item_prefix']['insert'][$k])	? clean($_POST['filter_item_prefix']['insert'][$k], true, true) : '';
			$_order	= isset($_POST['filter_item_order']['insert'][$k])	? abs((int)$_POST['filter_item_order']['insert'][$k]) : 0;
				
			if ( ! mb_strlen($_name)) continue;
			
			$this->db->query('INSERT INTO filter_item (id_filter, name, image, prefix, `order`) 
								VALUES(
									"'.$id_filter.'", 
									"'.$_name.'", 
									"'.$_image.'", 
									"'.$_prefix.'", 
									"'.$_order.'"
								)');
			
		}
		
		# присоединить фильтр к категории
		$this->db->query('DELETE FROM category_filter WHERE id_filter = '.$id_filter);
		if (isset($_POST['category_id'])) foreach($_POST['category_id'] as $k){
			$category_id = abs((int)$k);
			$this->db->query('INSERT INTO category_filter(category_id, id_filter) 
								VALUES(
									"'.$category_id.'", 
									"'.$id_filter.'"
								)');
		}
		
		return;
	}
	
	public function setOrderFilter()
	{
		for ($i = 0, $cnt = count($_POST['filter_order']); $i < $cnt; $i++){
			$id		= isset($_POST['filter_id'][$i])	? abs((int)$_POST['filter_id'][$i]) : 0;
			$order	= isset($_POST['filter_order'][$i])	? abs((int)$_POST['filter_order'][$i]) : 0;
			
			$this->db->query('UPDATE filter SET `order` = "'.$order.'" WHERE id = "'.$id.'"');
		}
		return;
	}

	public function setVisibilityFilter()
	{
		$id		= isset($_POST['id']) ? abs((int)$_POST['id']): 0;
		$activ	= (int)$_POST['activ'] ? 0 : 1;
		
		$this->db->query('UPDATE filter SET visibility = "'.$activ.'" WHERE id = "'.$id.'"');
		
		return $activ;
	}
	
	public function deleteFilter($id = 0)
	{
		$id = abs((int)$id);
		$this->db->query('DELETE FROM filter WHERE id = "'.$id.'"');
	}
	
}