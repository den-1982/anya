<?php
class categoryModel extends CI_Model
{
	public function getCategory($id = 0)
	{
		$id = abs((int)$id);
		$category =  $this->db->query('SELECT *, 
											CONCAT("/", c.url, "/", "c", c.id, "/") AS _url
										FROM category c 
										LEFT JOIN category_description cd ON cd.category_id = c.id
									WHERE c.id = "'.$id.'"')->row();
		
		if ( ! $category) return array();
		
		$category->slider = $this->db->query('SELECT * FROM category_slider WHERE category_id = "'.$category->id.'" ORDER BY `order` ASC')->result();
		
		return $category;
	}
	
	public function getCategories()
	{
		$categories = $this->db->query('SELECT c.*, 
												cd.name,
												cd.h1,
												CONCAT("/", c.url, "/", "c", c.id, "/") AS _url
											FROM category c
											LEFT JOIN category_description cd ON cd.category_id = c.id
										WHERE c.visibility = 1 
											ORDER BY c.order ASC')->result();
		foreach ($categories as $k){
			$k->cache = preg_replace('/(.+)(\/.+)$/', '${1}/_cache_${2}', $k->image);
		}
		
		
		return $categories;
	}
	
	public function sortCategories($categories = array()) 
	{
		$data = array();
		foreach ($categories as $category) {
			$data[$category->parent][$category->id] = $category;
		}
		return $data;
	}
	
	public function searchCategories($word = '') 
	{
		$word = clean($word, true, true);
		
		if (mb_strlen($word) < 3) return array();

		$categories = $this->db->query('SELECT *, 
												CONCAT("/", url, "/", "c", id, "/") AS _url
											FROM category c
											LEFT JOIN category_description cd ON cd.category_id = c.id
										WHERE c.visibility = 1 AND c.name LIKE "%'.$word.'%"
											ORDER BY c.order ASC')->result();
		
		foreach ($categories as $k){
			$k->cache = preg_replace('/(.+)(\/.+)$/', '${1}/_cache_${2}', $k->image);
		}
		
		return $categories;
	}
}