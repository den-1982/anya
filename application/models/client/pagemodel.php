<?php
class pageModel extends CI_Model
{
////////////////////////////////////////////////////////// SYSTEM PAGE
	public function getSystemPage($type = '')
	{
		$type = clean($type, true, true);
		$page = $this->db->query('SELECT * 
									FROM system_page sp
									LEFT JOIN system_page_description spd ON spd.system_page_id = sp.id
								WHERE sp.type = "'.$type.'"')->row();
		
		if ( ! $page) return array();
		$page->slider = $this->db->query('SELECT * FROM system_page_slider WHERE system_page_id = "'.$page->id.'"')->result();

		return $page; 
	}	
		
////////////////////////////////////////////////////////// PAGE	
	public function getPages()
	{
		return $this->db->query('SELECT p.*,
										pd.name,
										pd.h1,
										pd.metadesc,
										CONCAT("/", p.url, "/a", p.id) AS _url
										FROM page p
										LEFT JOIN page_description pd ON pd.page_id = p.id 
									WHERE p.visibility = 1 
										ORDER BY p.order ASC')->result();
	}
	
	public function getPage($url = '')
	{
		$url = clean($url, true, true);
		$page = $this->db->query('SELECT * 
										FROM page p
										LEFT JOIN page_description pd ON pd.page_id = p.id
									WHERE p.url = "'.$url.'" AND p.visibility = 1')->row(); 
		
		if ( ! $page) return array();
		
		$page->slider = $this->db->query('SELECT * FROM page_slider WHERE page_id = "'.$page->id.'"')->result();
		
		return $page;
	}
	
	public function sortPages($pages = array()) 
	{
		$data = array();
		foreach ($pages as $page) {
			$data[$page->parent][$page->id] = $page;
		}
		return $data;
	}
	
	public function getPageById($id = 0)
	{
		$id = abs((int)$id);
		$page = $this->db->query('SELECT * 
										FROM page p
										LEFT JOIN page_description pd ON pd.page_id = p.id
									WHERE p.id = "'.$id.'" AND p.visibility = 1')->row(); 
		if ( ! $page) return array();
		
		$page->slider = $this->db->query('SELECT * FROM page_slider WHERE page_id = "'.$page->id.'"')->result();
		
		return $page;
	}
	
}