<?php
class categoryModel extends CI_Model
{
	public function getCategory($id = 0)
	{
		$id = abs((int)$id);
		$category =  $this->db->query('SELECT *, 
												CONCAT("/", url, "/", "c", id, "/") AS _url,
												CONCAT("/img/categories/", id, "/", id, ".jpg") AS image
									FROM category 
								WHERE id = '.$id)->row();
		
		if ( ! $category) return array();
		
		$category->slider = $this->db->query('SELECT * FROM slider_category WHERE parent = '.$category->id.' ORDER BY `order` ASC')->result();
		
		return $category;
	}
	
	public function getCategories()
	{
		$categories = $this->db->query('SELECT *, 
												CONCAT("/", url, "/", "c", id, "/") AS _url,
												CONCAT("/img/categories/", id, "/", id, ".jpg") AS image,
												CONCAT("/img/categories/", id, "/", id, "_82_82.jpg") AS mini_image
											FROM category 
										WHERE visibility = 1 
											ORDER BY `order` ASC')->result();
											
		foreach ($categories as $category){
			$category->url = $category->url.'/c'.$category->id.'/';
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
		$word = trim(preg_replace('/\s+/', ' ', $word));
		
		if (mb_strlen($word) < 3) return array();
		
		$word = mysql_real_escape_string($word);
		
		$categories = $this->db->query('SELECT *, 
												CONCAT("/", url, "/", "c", id, "/") AS _url,
												CONCAT("/img/categories/", id, "/", id, ".jpg") AS image
											FROM category 
										WHERE visibility = 1 AND name LIKE "%'.$word.'%"
											ORDER BY `order` ASC')->result();
		
		return $categories;
	}
}