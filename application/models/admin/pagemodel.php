<?php
class pageModel extends CI_Model
{
	public function getSystemPage($type = '')
	{
		$type = clean($type, true, true);
		
		$page = $this->db->query('SELECT * 
										FROM system_page sp
										LEFT JOIN system_page_description spd ON spd.system_page_id = sp.id
									WHERE sp.type = "'.$type.'"')->row();

		if ( ! $page) return array();
		
		# IMAGE
		$page->cache = preg_replace('/(.+)(\/.+)$/', '${1}/_cache_${2}', $page->image);
		
		# SLIDER
		$page->slider = $this->db->query('SELECT * FROM system_page_slider WHERE system_page_id = "'.$page->id.'"')->result();
		foreach ($page->slider as $k){
			$k->cache = preg_replace('/(.+)(\/.+)$/', '${1}/_cache_${2}', $k->image);
		}
		
		return $page; 
	}
	
	public function editSystemPage()
	{
		$id		= isset($_POST['id'])		? abs((int)$_POST['id']) : 0;
		$image	= isset($_POST['image'])	? clean($_POST['image'], true, true) : '';
		
		$this->db->query('UPDATE system_page SET 
											image = "'.$image.'"
										WHERE id = "'.$id.'"');
		
		
		# DESCRIPTION
		$h1			= isset($_POST['h1'])		? clean($_POST['h1'], true, true) : '';
		$name		= isset($_POST['name']) 	? clean($_POST['name'], true, true) : '';
		$title		= isset($_POST['title'])	? clean($_POST['title'], true, true) : '';
		$metadesc	= isset($_POST['metadesc'])	? clean($_POST['metadesc'], true, true) : '';
		$metakey	= isset($_POST['metakey'])	? clean($_POST['metakey'], true, true) : '';
		$text		= isset($_POST['text'])		? clean($_POST['text'], false, true) : '';
		$spam       = isset($_POST['spam'])		? clean($_POST['spam'], true, true) : '';
		
		$this->db->query('DELETE FROM system_page_description WHERE system_page_id = "'.$id.'"');
		$this->db->query('INSERT INTO system_page_description (system_page_id, h1, name, title, metadesc, metakey, text, spam) 
							VALUES(
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
		$this->db->query('DELETE FROM system_page_slider WHERE system_page_id = "'.$id.'"');
		if (isset($_POST['slider']['image'])) foreach ($_POST['slider']['image'] as $k=>$v){
			$p['image']	= isset($_POST['slider']['image'][$k])	? clean($_POST['slider']['image'][$k], true, true) : '';
			$p['link']	= isset($_POST['slider']['link'][$k])	? clean($_POST['slider']['link'][$k], true, true) : '';
			$p['h1']	= isset($_POST['slider']['h1'][$k])		? clean($_POST['slider']['h1'][$k], true, true) : '';
			$p['text']	= isset($_POST['slider']['text'][$k])	? clean($_POST['slider']['text'][$k], false, true) : '';

			if ( ! $p['image']) continue;
			
			$this->db->query('INSERT INTO system_page_slider (system_page_id, image, h1, text, link) 
								VALUES(
									"'.$id.'",
									"'.$p['image'].'", 
									"'.$p['h1'].'", 
									"'.$p['text'].'", 
									"'.$p['link'].'"
								)');
			
		}

		return;
	}
	

////////////////////////////////////////////////////////////// PAGE
	public function sortPages($pages = array())
	{
		$data = array();
		foreach ($pages as $page) {
			$data[$page->parent][$page->id] = $page;
		}
		
		return $data;
	}
	
	public function getPages()
	{
		return $this->db->query('SELECT p.*, 
										pd.name,
										(SELECT COUNT(*) FROM page WHERE parent = p.id) AS cnt_childs 
									FROM page p
									LEFT JOIN page_description pd ON pd.page_id = p.id 
									GROUP BY p.id
									ORDER BY p.order ASC')->result();
	}
	
	public function getPage($id = 0)
	{
		$id = abs((int)$id);
		
		$page = $this->db->query('SELECT *
									FROM page p
									LEFT JOIN page_description pd ON pd.page_id = p.id
								WHERE p.id = "'.$id.'"')->row();
		
		if ( ! $page) return array();
		
		# IMAGE
		$page->cache = preg_replace('/(.+)(\/.+)$/', '${1}/_cache_${2}', $page->image);
		
		# SLIDER
		$page->slider = $this->db->query('SELECT * FROM page_slider WHERE page_id = "'.$page->id.'" ORDER BY `order` ASC')->result();
		foreach ($page->slider as $k){
			$k->cache = preg_replace('/(.+)(\/.+)$/', '${1}/_cache_${2}', $k->image);
		}
		
		return $page; 
	}
	
	public function addPage()
	{	
		$parent		= isset($_POST['parent'])	? abs((int)$_POST['parent']) : 0;
		$image		= isset($_POST['image'])	? clean($_POST['image'], true, true) : '';
		$url		= isset($_POST['url'])		? translit(mb_strtolower($_POST['url'])) : '';
		$url		= mb_strlen($url) ? $url : time();
		
		$this->db->query('INSERT INTO page (parent, image, url) 
							   VALUES (
								"'.$parent.'",
								"'.$image.'",
								"'.$url.'"
							)');
		
		# ID PAGE
		$id = $this->db->query('SELECT MAX(id) AS id FROM page LIMIT 1')->row()->id;
		
		# DESCRIPTION
		$h1			= isset($_POST['h1'])		? clean($_POST['h1'], false, true)		: '';
		$name       = isset($_POST['name'])		? clean($_POST['name'], true, true)		: 'empty';
		$title      = isset($_POST['title'])	? clean($_POST['title'], true, true)	: '';
		$metadesc   = isset($_POST['metadesc'])	? clean($_POST['metadesc'], true, true)	: '';
		$metakey    = isset($_POST['metakey'])	? clean($_POST['metakey'], true, true)	: '';
		$text       = isset($_POST['text'])		? clean($_POST['text'], false, true)	: '';
		$spam       = isset($_POST['spam'])		? clean($_POST['spam'], true, true)		: '';
		
		$this->db->query('INSERT INTO page_description (page_id, h1, name, title, metadesc, metakey, text, spam) 
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
			
			$this->db->query('INSERT INTO page_slider (page_id, image, link, h1, text, `order`) 
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
	
	public function updatePage()
	{
		$id			= isset($_POST['id'])		? abs((int)$_POST['id']) : 0;
		$parent		= isset($_POST['parent'])	? abs((int)$_POST['parent']) : 0;
		$image		= isset($_POST['image'])	? clean($_POST['image'], true, true) : '';
		$url		= isset($_POST['url'])		? translit(mb_strtolower($_POST['url'])) : '';
		$url		= mb_strlen($url) ? $url : time();
		
		$this->db->query('UPDATE page SET
								parent = "'.$parent.'",
								image = "'.$image.'",
								url = "'.$url.'"
							WHERE id = "'.$id.'"');

		# DESCRIPTION
		$h1			= isset($_POST['h1'])		? clean($_POST['h1'], false, true)		: '';
		$name       = isset($_POST['name'])		? clean($_POST['name'], true, true)		: 'empty';
		$title      = isset($_POST['title'])	? clean($_POST['title'], true, true)	: '';
		$metadesc   = isset($_POST['metadesc'])	? clean($_POST['metadesc'], true, true)	: '';
		$metakey    = isset($_POST['metakey'])	? clean($_POST['metakey'], true, true)	: '';
		$text       = isset($_POST['text'])		? clean($_POST['text'], false, true)	: '';
		$spam       = isset($_POST['spam'])		? clean($_POST['spam'], true, true)		: '';
		
		$this->db->query('DELETE FROM page_description WHERE page_id = "'.$id.'"');
		$this->db->query('INSERT INTO page_description (page_id, h1, name, title, metadesc, metakey, text, spam) 
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
		$this->db->query('DELETE FROM page_slider WHERE page_id = "'.$id.'"');
		if (isset($_POST['slider']['image'])) foreach ($_POST['slider']['image'] as $k=>$v){
			$image	= isset($_POST['slider']['image'][$k])	? clean($_POST['slider']['image'][$k], true, true)	: '';
			$link	= isset($_POST['slider']['link'][$k])	? clean($_POST['slider']['link'][$k], true, true)	: '';
			$h1		= isset($_POST['slider']['h1'][$k])		? clean($_POST['slider']['h1'][$k], true, true)		: '';
			$text	= isset($_POST['slider']['text'][$k])	? clean($_POST['slider']['text'][$k], true, true)	: '';
			$order	= isset($_POST['slider']['order'][$k])	? abs((int)$_POST['slider']['order'][$k])			: 0;
			
			if ( ! $image) continue;
			
			$this->db->query('INSERT INTO page_slider (page_id, image, link, h1, text, `order`) 
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
	
	public function sortOrderPages()
	{
		if (isset($_POST['page_order'])) for ($i = 0, $cnt = count($_POST['page_order']); $i < $cnt; $i++){
			$page_id	= isset($_POST['page_id'][$i])		? abs((int)$_POST['page_id'][$i]) : 0;
			$page_order	= isset($_POST['page_order'][$i])	? abs((int)$_POST['page_order'][$i]) : 0;
			
			$this->db->query('UPDATE page SET `order` = "'.$page_order.'" WHERE id = "'.$page_id.'"');
		}
		
		return;
	}
	
	public function setVisibilityPage()
	{
		$id		= isset($_POST['id']) ? abs((int)$_POST['id']) : 0;
		$activ	= (int)$_POST['activ'] ? 0 : 1;
		
		$this->db->query('UPDATE page SET visibility = "'.$activ.'" WHERE id = "'.$id.'"');
		
		return $activ;
	}
	
	public function delPage($id = 0)
	{
		$id = abs((int)$id);

		$childs	= $this->db->query('SELECT COUNT(*) AS cnt FROM page WHERE parent = "'.$id.'"')->row()->cnt;
		
		# Запрет на удалени
		if ($childs){
			$html = 
			'<div>
				<p><b>Страница не может быть удалена!</b></p>
				<p>
					Страница имеет - '.$childs.' вложеных страниц.
				</p>
			</div>';
			
			return $response = array(
				'error' => preg_replace('/\s+/', ' ', $html)
			);
		}
		
		# Удаление
		$this->db->query('DELETE FROM page WHERE id = "'.$id.'"');
		
		return 0;
	}

}