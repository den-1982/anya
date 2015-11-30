<?php
class filterModel extends CI_Model
{
	public function getFilterItemSize($id_category = 0)
	{
		$id_category = abs((int)$id_category);
		# выбрать размеры какие есть в категории
		return $this->db->query('SELECT fi.*,
										pp.cnt_opt,
										pp.cnt_roz
									FROM filter_item fi 
									LEFT JOIN product_prices pp ON fi.id = pp.id_filter_item 
									LEFT JOIN products p ON pp.id_product = p.id	
								WHERE p.parent = ' . $id_category.'
									GROUP BY fi.id
									ORDER BY fi.order
								')->result();
	}
	
	public function getFilters()
	{
		$res = $this->db->query('SELECT * FROM filter')->result();
		$filters = array();
		foreach ($res as $filter){
			$filters[$filter->id] = $filter;
		}
		unset($res);
		
		return $filters;
	}
	
	# не используется
	public function parseUrlFilters($url = '')
	{
		$data = array();
		if ( ! $url) return $data;
		
		$filters = explode(';', $url);
		
		foreach ($filters as $filter){
			# $a[0] - id_filter, $a[1] - id_filter_item
			$a = explode('=', $filter); 
			if ( !isset($a[0]) || !isset($a[1]) ){ return 'error';}
			
			# только одно значение одного фильтра (типа переключатель)
			$b =  explode(',', $a[1]);
			$data[$a[0]] = array($b[0]);

			# несколько значений одного фильтра (типа чекбокс)
			// $data[$a[0]] = explode(',', $a[1]);
		}

		return $data;
	}
	
	# не используется
	public function getFiltersOfCategory($id_category = 0, $products = array(), $selected = array(), $path = '')
	{
		$id_category = abs((int)$id_category);
		
		# фильтры выбранной категории
		$filters = $this->db->query('SELECT * FROM filter f 
										LEFT JOIN category_filter cf ON f.id = cf.id_filter
									WHERE cf.id_category = '.$id_category.' AND f.visibility = 1 
										ORDER BY f.order ASC')->result();
		# сортируем по ключу id фильтра
		$arr = array();
		foreach ($filters as $filter){$arr[$filter->id] = $filter;}
		$filters = $arr;
		unset($arr);
		
		# id выбранных товаров
		$ids = array(0);
		foreach ($products as $product){$ids[] = $product->id;}
		unset($products);

		# все товары данной категории (без учета фильтров)
		$allProducts = $this->db->query('SELECT * FROM product WHERE id_category = '.$id_category)->result();
		$allIds = array(0);
		foreach ($allProducts as $p){$allIds[] = $p->id;}
		unset($allProducts);
		
		# значения выбранных фильтров и подсчет количество товаров данного значения (cnt)
		foreach ($filters as $filter){
			# по умолчанию
			$filter->items = array();
			$filter->activ = 0;
			
			# тип фиксации, тип формы
			if ($filter->id == 1 || $filter->id == 2){
				$filter->items = $this->db->query(
					'SELECT *
						FROM filter_item 
					WHERE id_filter = '.$filter->id.' 
						AND id IN(SELECT id_filter_item FROM product_filter_item WHERE id_product IN('.implode(',', $allIds).'))
						ORDER BY `order` ASC'
				)->result();
			}
			
			# размер
			if ($filter->id == 3){
				$filter->items = $this->db->query(
					'SELECT *
						FROM filter_item 
					WHERE id_filter = 3 
						AND id IN(SELECT id_filter_item FROM product_filter_item WHERE id_product IN('.implode(',', $ids).'))
						ORDER BY `order` ASC'
				)->result();
			}
			
			foreach ($filter->items as $item){
				if (isset($selected[$filter->id]) && in_array($item->id, $selected[$filter->id], true)){
					$item->activ = 1;
					$filter->activ = 1;
				}else{
					$item->activ = 0;
				}
				//$item->activ = isset($selected[$filter->id]) && in_array($item->id, $selected[$filter->id], true) ? 1 : 0;
				
				$item->url = $path . 'filter/';
				
				# url для значения фильтра
				$arr = array();  
				
				# копия
				$s = $selected; 
				
				# добавляем id - значения по умолчанию
				$s[$filter->id][0] = $item->id;
				
				# для одного значения фильтра
				foreach ($s as $k=>$v){
					if( ($filter->id == 1 || $filter->id == 2) && $k == 3) continue;
					
					if ($item->activ && $s[$filter->id][0] == $item->id){
						continue;
					}else{
						$arr[$k] = $k.'='.$v[0];
					}
				}
				/*
				foreach ($s as $k=>$v){
					if( ($filter->id == 1 || $filter->id == 2) && $k == 3) continue;
					
					if ($item->activ && $s[$filter->id][0] == $item->id){
						continue;
					}else{
						$arr[$k] = $k.'='.$v[0];
					}
				}
				*/

				sort($arr, SORT_NUMERIC);
				$item->url .= implode(';', $arr) . ($arr?'/':'');
			}
		}
		
		
		# не показывать размер если не выбрана форма или тип фикс. (если есть форма и тип фикс.)
		if ( isset($filters[3]) && (isset($filters[2]) && !$filters[2]->activ)){
			unset($filters[3]);
		}
			
		return $filters;
	}
	
	# не используется
	public function getSort($selected = array(), $path = '')
	{
		# сортировка продуктов
		$sort = array(
			'hit' => (object)array(
				'id' => 'hit',
				'activ' => 0,
				'name' =>'Хит',
				'type' => 'checkbox',
				'url' => ''
			),
			'new' => (object)array(
				'id' => 'new',
				'activ' => 0,
				'name' =>'Новинка',
				'type' => 'checkbox',
				'url' => ''
			)
		);

		# формируем URL
		foreach ($sort as $item){
			$s = $selected;
			if (isset($s['sort'][0]) && $s['sort'][0] == $item->id){
				$item->activ = 1;
				unset($s['sort']);
			}else{
				unset($s['sort']);
				$s['sort'][] = $item->id;
			}
			
			$arr = array();
			foreach ($s as $k=>$v){
				$arr[] =  $k.'='.implode(',', $v);
			}
			
			sort($arr, SORT_NUMERIC);
			$item->url = $path .'filter/'. implode(';', $arr).($arr?'/':'');
		}
		
		return $sort;
	}
		
}