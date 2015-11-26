<?php
class pageModel extends CI_Model
{
	
	public function getPageSystem($type = '')
	{
		$type = mysql_real_escape_string($type);
		$page = $this->db->query('SELECT * FROM pages_system WHERE type = "'.$type.'"')->row();
		
		if ( ! $page) return array();
		$page->slider = $this->db->query('SELECT * FROM pages_system_slider WHERE pages_system_id = "'.$page->id.'"')->result();

		return $page; 
	}	
		
		
	public function getPages()
	{
		return $this->db->query('SELECT *,
										CONCAT("/", type, "/", url) AS _url,
										CONCAT("/img/news-articles/", id, "/", id,  ".jpg") AS image
										FROM pages 
									WHERE visibility = 1 
										ORDER BY `order` ASC')->result();
	}
	
	public function getPage($url = '')
	{
		$url = mysql_real_escape_string($url);
		$page = $this->db->query('SELECT * FROM pages WHERE type = "0" AND url = "'.$url.'" AND visibility = 1')->row(); 
		
		if ( ! $page) return array();
		
		$page->slider = $this->db->query('SELECT * FROM slider_pages WHERE parent = "'.$page->id.'"')->result();
		
		return $page;
	}
	
	public function getPageById($id = 0)
	{
		$id = abs((int)$id);
		$page = $this->db->query('SELECT * FROM pages WHERE id = "'.$id.'" AND visibility = 1')->row();
		
		if ( ! $page) return array();
		
		$page->slider = $this->db->query('SELECT * FROM slider_pages WHERE parent = "'.$page->id.'"')->result();
		
		return $page;
	}
	
	
	public function getNews($url = '')
	{
		$url = mysql_real_escape_string($url);
		$news =  $this->db->query('SELECT * 
										FROM pages 
									WHERE type = "news" AND url = "'.$url.'" AND visibility = 1')->row();
		
		if ( ! $news) return array();
		
		$news->slider = $this->db->query('SELECT * FROM slider_pages WHERE parent = "'.$news->id.'"')->result();
		
		return $news;
	}
	
	public function getArticles($url = '')
	{
		$url = mysql_real_escape_string($url);
		$articles = $this->db->query('SELECT * 
										FROM pages 
									WHERE type = "articles" AND url = "'.$url.'" AND visibility = 1')->row();
		
		if ( ! $articles) return array();
		
		$articles->slider = $this->db->query('SELECT * FROM slider_pages WHERE parent = "'.$articles->id.'"')->result();
		
		return $articles;
	}
}


























