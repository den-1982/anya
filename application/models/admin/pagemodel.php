<?php
class pageModel extends CI_Model
{
	public function getPageSystem($type = '')
	{
		$type = mysql_real_escape_string($type);
		
		$page = $this->db->query('SELECT * FROM pages_system WHERE type = "'.$type.'"')->row();
		
		if ( ! $page) return array();
		
		$page->slider = $this->db->query('SELECT * FROM pages_system_slider WHERE pages_system_id = "'.$page->id.'"')->result();
		foreach ($page->slider as $k){
			$k->cache = preg_replace('/(.+)(\/.+)$/', '${1}/_cache_${2}', $k->image);
		}

		return $page; 
	}
	
	public function updateHomePage()
	{
		$id			= isset($_POST['id']) ? abs((int)$_POST['id']) : 0;
		$h1			= isset($_POST['h1']) ? clean($_POST['h1'], true, true) : '';
		$name		= isset($_POST['name']) ? clean($_POST['name'], true, true) : '';
		$title		= isset($_POST['title']) ? clean($_POST['title'], true, true) : '';
		$metadesc	= isset($_POST['metadesc']) ? clean($_POST['metadesc'], true, true) : '';
		$metakey	= isset($_POST['metakey']) ? clean($_POST['metakey'], true, true) : '';
		$text		= isset($_POST['text']) ? clean($_POST['text'], false, true) : '';
		$spam       = isset($_POST['spam']) ? clean($_POST['spam'], true, true) : '';
		
		$this->db->query('UPDATE pages_system SET 
											h1 = "'.$h1.'",
											title = "'.$title.'",
											metadesc = "'.$metadesc.'",
											metakey = "'.$metakey.'",
											text = "'.$text.'",
											spam = "'.$spam.'"
										WHERE id = "'.$id.'"');
		
		# SLIDER
		$this->db->query('DELETE FROM pages_system_slider WHERE pages_system_id = '.$id);
		if (isset($_POST['slider_image'])){
			foreach ($_POST['slider_image'] as $k=>$v){
				
				$order = $k;
				$image	= isset($_POST['slider_image'][$k])	? clean($_POST['slider_image'][$k], true, true) : '';
				$link	= isset($_POST['slider_link'][$k])	? clean($_POST['slider_link'][$k], true, true) : '';
				$h1		= isset($_POST['slider_h1'][$k])	? clean($_POST['slider_h1'][$k], true, true) : '';
				$text	= isset($_POST['slider_text'][$k])	? clean($_POST['slider_text'][$k], false, true) : '';

				if ( ! $image) continue;
				
				$this->db->query('INSERT INTO pages_system_slider (pages_system_id, image, h1, text, link) 
									VALUES(
										"'.$id.'",
										"'.$image.'", 
										"'.$h1.'", 
										"'.$text.'", 
										"'.$link.'"
									)');
			}
		}

		return;
	}
	
	public function updateAboutPage()
	{
		$id			= isset($_POST['id']) ? abs((int)$_POST['id']) : 0;
		$h1			= isset($_POST['h1']) ? clean($_POST['h1'], true, true) : '';
		$name		= isset($_POST['name']) ? clean($_POST['name'], true, true) : '';
		$title		= isset($_POST['title']) ? clean($_POST['title'], true, true) : '';
		$metadesc	= isset($_POST['metadesc']) ? clean($_POST['metadesc'], true, true) : '';
		$metakey	= isset($_POST['metakey']) ? clean($_POST['metakey'], true, true) : '';
		$text		= isset($_POST['text']) ? clean($_POST['text'], false, true) : '';
		$spam       = isset($_POST['spam']) ? clean($_POST['spam'], true, true) : '';
		
		$this->db->query('UPDATE pages_system SET 
											h1 = "'.$h1.'",
											title = "'.$title.'",
											metadesc = "'.$metadesc.'",
											metakey = "'.$metakey.'",
											text = "'.$text.'",
											spam = "'.$spam.'"
										WHERE id = "'.$id.'"');
		
		# SLIDER
		$this->db->query('DELETE FROM pages_system_slider WHERE pages_system_id = '.$id);
		if (isset($_POST['slider_image'])){
			foreach ($_POST['slider_image'] as $k=>$v){
				
				$order = $k;
				$image	= isset($_POST['slider_image'][$k])	? clean($_POST['slider_image'][$k], true, true) : '';
				$link	= isset($_POST['slider_link'][$k])	? clean($_POST['slider_link'][$k], true, true) : '';
				$h1		= isset($_POST['slider_h1'][$k])	? clean($_POST['slider_h1'][$k], true, true) : '';
				$text	= isset($_POST['slider_text'][$k])	? clean($_POST['slider_text'][$k], false, true) : '';

				if ( ! $image) continue;
				
				$this->db->query('INSERT INTO pages_system_slider (pages_system_id, image, h1, text, link) 
									VALUES(
										"'.$id.'",
										"'.$image.'", 
										"'.$h1.'", 
										"'.$text.'", 
										"'.$link.'"
									)');
			}
		}

		return;
	}
	
	public function updateOplataPage()
	{
		$id			= isset($_POST['id']) ? abs((int)$_POST['id']) : 0;
		$h1			= isset($_POST['h1']) ? clean($_POST['h1'], true, true) : '';
		$name		= isset($_POST['name']) ? clean($_POST['name'], true, true) : '';
		$title		= isset($_POST['title']) ? clean($_POST['title'], true, true) : '';
		$metadesc	= isset($_POST['metadesc']) ? clean($_POST['metadesc'], true, true) : '';
		$metakey	= isset($_POST['metakey']) ? clean($_POST['metakey'], true, true) : '';
		$text		= isset($_POST['text']) ? clean($_POST['text'], false, true) : '';
		$spam       = isset($_POST['spam']) ? clean($_POST['spam'], true, true) : '';
		
		$this->db->query('UPDATE pages_system SET 
											h1 = "'.$h1.'",
											title = "'.$title.'",
											metadesc = "'.$metadesc.'",
											metakey = "'.$metakey.'",
											text = "'.$text.'",
											spam = "'.$spam.'"
										WHERE id = "'.$id.'"');
		
		# SLIDER
		$this->db->query('DELETE FROM pages_system_slider WHERE pages_system_id = '.$id);
		if (isset($_POST['slider_image'])){
			foreach ($_POST['slider_image'] as $k=>$v){
				
				$order = $k;
				$image	= isset($_POST['slider_image'][$k])	? clean($_POST['slider_image'][$k], true, true) : '';
				$link	= isset($_POST['slider_link'][$k])	? clean($_POST['slider_link'][$k], true, true) : '';
				$h1		= isset($_POST['slider_h1'][$k])	? clean($_POST['slider_h1'][$k], true, true) : '';
				$text	= isset($_POST['slider_text'][$k])	? clean($_POST['slider_text'][$k], false, true) : '';

				if ( ! $image) continue;
				
				$this->db->query('INSERT INTO pages_system_slider (pages_system_id, image, h1, text, link) 
									VALUES(
										"'.$id.'",
										"'.$image.'", 
										"'.$h1.'", 
										"'.$text.'", 
										"'.$link.'"
									)');
			}
		}
		
		return;
	}
	
	public function updateBiznesPage()
	{
		$id			= isset($_POST['id']) ? abs((int)$_POST['id']) : 0;
		$h1			= isset($_POST['h1']) ? clean($_POST['h1'], true, true) : '';
		$name		= isset($_POST['name']) ? clean($_POST['name'], true, true) : '';
		$title		= isset($_POST['title']) ? clean($_POST['title'], true, true) : '';
		$metadesc	= isset($_POST['metadesc']) ? clean($_POST['metadesc'], true, true) : '';
		$metakey	= isset($_POST['metakey']) ? clean($_POST['metakey'], true, true) : '';
		$text		= isset($_POST['text']) ? clean($_POST['text'], false, true) : '';
		$spam       = isset($_POST['spam']) ? clean($_POST['spam'], true, true) : '';
		
		$this->db->query('UPDATE pages_system SET 
											h1 = "'.$h1.'",
											title = "'.$title.'",
											metadesc = "'.$metadesc.'",
											metakey = "'.$metakey.'",
											text = "'.$text.'",
											spam = "'.$spam.'"
										WHERE id = "'.$id.'"');
		
		# SLIDER
		$this->db->query('DELETE FROM pages_system_slider WHERE pages_system_id = '.$id);
		if (isset($_POST['slider_image'])){
			foreach ($_POST['slider_image'] as $k=>$v){
				
				$order = $k;
				$image	= isset($_POST['slider_image'][$k])	? clean($_POST['slider_image'][$k], true, true) : '';
				$link	= isset($_POST['slider_link'][$k])	? clean($_POST['slider_link'][$k], true, true) : '';
				$h1		= isset($_POST['slider_h1'][$k])	? clean($_POST['slider_h1'][$k], true, true) : '';
				$text	= isset($_POST['slider_text'][$k])	? clean($_POST['slider_text'][$k], false, true) : '';

				if ( ! $image) continue;
				
				$this->db->query('INSERT INTO pages_system_slider (pages_system_id, image, h1, text, link) 
									VALUES(
										"'.$id.'",
										"'.$image.'", 
										"'.$h1.'", 
										"'.$text.'", 
										"'.$link.'"
									)');
			}
		}
		
		return;
	}
	
	
	public function getPages()
	{
		return $this->db->query('SELECT * FROM pages ORDER BY `order` ASC')->result();
	}
	
	public function getPage($id = 0)
	{
		$id = abs((int)$id);
		
		$page = $this->db->query('SELECT * FROM pages WHERE id = '.$id)->row();
		
		if ( ! $page) return array();
		
		$page->slider = $this->db->query('SELECT * FROM slider_pages WHERE parent = '.$page->id.' ORDER BY `order` ASC')->result();
		foreach ($page->slider as $k){
			$k->cache = preg_replace('/(.+)(\/.+)$/', '${1}/_cache_${2}', $k->img);
		}
		
		return $page; 
	}
	
	public function addPage()
	{	
		$h1			= mysql_real_escape_string(trim($_POST['h1']));
		$name       = mysql_real_escape_string(trim($_POST['name']));
		$title      = mysql_real_escape_string(trim($_POST['title']));
		$metadesc   = mysql_real_escape_string(trim($_POST['metadesc']));
		$metakey    = mysql_real_escape_string(trim($_POST['metakey']));
		$text       = mysql_real_escape_string(trim($_POST['text']));
		$spam       = mysql_real_escape_string(trim($_POST['spam']));
		
		$date		= time();
		$type		= trim($_POST['type']);
		
		$url		= translit(mb_strtolower($_POST['url']));
		$url		= mb_strlen($url) ? $url : time();
		$name		= mb_strlen($name) ? $name : 'empty';
		
		$this->db->query('INSERT INTO pages (h1, name, title, metadesc, metakey, text, spam, `date`, url, type) 
							   VALUES ("'.$h1.'", "'.$name.'", "'.$title.'", "'.$metadesc.'", "'.$metakey.'", "'.$text.'", "'.$spam.'", '.$date.', "'.$url.'", "'.$type.'")');
		
		$id = $this->db->query('SELECT MAX(id) AS id FROM pages LIMIT 1')->row()->id;
		
		# SLIDER
		if (isset($_POST['slider_image'])){
			for ($i = 0; $i < count($_POST['slider_image']); $i++){
				$img	= isset($_POST['slider_image'][$i]) ? mysql_real_escape_string(trim($_POST['slider_image'][$i])) : '';
				$link	= isset($_POST['slider_link'][$i]) ? mysql_real_escape_string(trim($_POST['slider_link'][$i])) : '';
				$order	= isset($_POST['slider_order'][$i]) ? (int)$_POST['slider_order'][$i] : 0;
				if ( ! $img) continue;
				$this->db->query('INSERT INTO slider_pages (parent, img, link, `order`) VALUES('.$id.', "'.$img.'", "'.$link.'", '.$order.')');
			}
		}
		
		# IMAGE
		$dir = ROOT.'/img/news-articles/'.$id.'/';
		if ( ! is_dir($dir)){ mkdir($dir, 0755, true);}
		
		if (isset($_FILES['image'])){
			$img = $_FILES['image'];
			if ($img['error'] == 0){
				$i		= $dir.$id.'.jpg';
				$i82	= $dir.$id.'_82_82.jpg';

				$this->my_imagemagic->upload($img['tmp_name'], $i);
				$this->my_imagemagic->resize_square($img['tmp_name'], $i82, 82);
			}
		}
		
		return;
	}
	
	public function updatePage()
	{
		$id			= abs((int)$_POST['id']);
		$h1			= mysql_real_escape_string(trim($_POST['h1']));
		$name		= mysql_real_escape_string(trim($_POST['name']));
		$title		= mysql_real_escape_string(trim($_POST['title']));
		$metadesc	= mysql_real_escape_string(trim($_POST['metadesc']));
		$metakey	= mysql_real_escape_string(trim($_POST['metakey']));
		$text		= mysql_real_escape_string(trim($_POST['text']));
		$spam       = mysql_real_escape_string(trim($_POST['spam']));
		
		$type		= trim($_POST['type']);
		
		$url		= translit(mb_strtolower($_POST['url']));
		$url		= mb_strlen($url) ? $url : time();
		$name		= mb_strlen($name) ? $name : 'empty';
		
		$res = $this->db->query('UPDATE pages SET 
												h1		= "'.$h1.'",
												name	= "'.$name.'", 
												title	= "'.$title.'", 
												metadesc= "'.$metadesc.'", 
												metakey	= "'.$metakey.'", 
												text	= "'.$text.'", 
												spam	= "'.$spam.'", 
												url		= "'.$url.'",
												type    = "'.$type.'"
											WHERE id = '.$id);
		
		
		# SLIDER
		# удаляем старые значения
		$this->db->query('DELETE FROM slider_pages WHERE parent = '.$id);

		if (isset($_POST['slider_image'])){
			for ($i = 0; $i < count($_POST['slider_image']); $i++){
				$img	= isset($_POST['slider_image'][$i]) ? mysql_real_escape_string(trim($_POST['slider_image'][$i])) : '';
				$link	= isset($_POST['slider_link'][$i]) ? mysql_real_escape_string(trim($_POST['slider_link'][$i])) : '';
				$order	= isset($_POST['slider_order'][$i]) ? (int)$_POST['slider_order'][$i] : 0;
				if ( ! $img) continue;
				$this->db->query('INSERT INTO slider_pages (parent, img, link, `order`) VALUES('.$id.', "'.$img.'", "'.$link.'", '.$order.')');
			}
		}
		
		
		# IMAGE
		$dir = ROOT.'/img/news-articles/'.$id.'/';
		if ( ! is_dir($dir)){ mkdir($dir, 0755, true);}
		
		if (isset($_FILES['image'])){
			$img = $_FILES['image'];
			if ($img['error'] == 0){
				$i		= $dir.$id.'.jpg';
				$i82	= $dir.$id.'_82_82.jpg';

				$this->my_imagemagic->upload($img['tmp_name'], $i);
				$this->my_imagemagic->resize_square($img['tmp_name'], $i82, 82);
			}
		}
		
		return;
	}
	
	public function sortOrderPages()
	{
		if (isset($_POST['page_order'])){
			for ($i = 0, $cnt = count($_POST['page_order']); $i < $cnt; $i++){
				$page_id = isset($_POST['page_id'][$i]) ? abs((int)$_POST['page_id'][$i]) : 0;
				$page_order = isset($_POST['page_order'][$i]) ? abs((int)$_POST['page_order'][$i]) : 0;
				
				$this->db->query('UPDATE pages SET `order` = '.$page_order.' WHERE id = '.$page_id);
			}
		}
		
		return;
	}
	
	public function setVisibilityPage()
	{
		$id		= isset($_POST['id']) ? abs((int)$_POST['id']) : 0;
		$activ	= (int)$_POST['activ'] ? 0 : 1;
		
		$this->db->query('UPDATE pages SET visibility = '.$activ.' WHERE id = '.$id);
		
		return $activ;
	}
	
	public function delPage($id = 0)
	{
		$id = abs((int) $id);
		$this->db->query('DELETE FROM pages WHERE id = '.$id);
		
		$dir = ROOT.'/img/news-articles/'.$id;
		
		if (is_dir($dir)){
			delDir($dir);
		}
		
		return 0;
	}

}