<?php
class manufacturerModel extends CI_Model
{
	public function getManufacturers()
	{
		$manufacturers = $this->db->query('SELECT m.*,
													md.name
												FROM manufacturer m
												LEFT JOIN manufacturer_description md ON md.manufacturer_id = m.id
												GROUP BY m.id
												ORDER BY m.order ASC')->result();
		
		foreach ($manufacturers as $manufacturer){
			$manufacturer->cache = preg_replace('/(.+)(\/.+)$/', '${1}/_cache_${2}', $manufacturer->image);
		}
		
		return $manufacturers;
	}
	
	public function getManufacturer($id = 0)
	{
		$id = abs((int)$id);
		
		$manufacturer = $this->db->query('SELECT *
												FROM manufacturer m
												LEFT JOIN manufacturer_description md ON md.manufacturer_id = m.id
											WHERE m.id = "'.$id.'"')->row();
												
		if ( ! $manufacturer) return array();
		
		$manufacturer->cache = preg_replace('/(.+)(\/.+)$/', '${1}/_cache_${2}', $manufacturer->image);
		
		return $manufacturer;
	}
	
	public function addManufacturer()
	{
		$image		= isset($_POST['image'])	? clean($_POST['image'], true, true) : '';
		$url		= isset($_POST['url'])		? translit(mb_strtolower($_POST['url'])) : '';
		$url		= mb_strlen($url) ? $url : time();
		
		$this->db->query('INSERT INTO manufacturer (image, url) 
							   VALUES (
								"'.$image.'",
								"'.$url.'"
							)');
		
		# ID MANUFACTURER
		$id = $this->db->query('SELECT MAX(id) AS id FROM manufacturer LIMIT 1')->row()->id;
		
		# DESCRIPTION
		$h1			= isset($_POST['h1'])		? clean($_POST['h1'], false, true)		: '';
		$name       = isset($_POST['name'])		? clean($_POST['name'], true, true)		: 'empty';
		$title      = isset($_POST['title'])	? clean($_POST['title'], true, true)	: '';
		$metadesc   = isset($_POST['metadesc'])	? clean($_POST['metadesc'], true, true)	: '';
		$metakey    = isset($_POST['metakey'])	? clean($_POST['metakey'], true, true)	: '';
		$text       = isset($_POST['text'])		? clean($_POST['text'], false, true)	: '';
		$spam       = isset($_POST['spam'])		? clean($_POST['spam'], true, true)		: '';
		
		$this->db->query('INSERT INTO manufacturer_description (manufacturer_id, h1, name, title, metadesc, metakey, text, spam) 
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
		
		return;
	}
	
	public function updateManufacturer()
	{
		$id			= isset($_POST['id'])		? abs((int)$_POST['id']) : 0;
		$image		= isset($_POST['image'])	? clean($_POST['image'], true, true) : '';
		$url		= isset($_POST['url'])		? translit(mb_strtolower($_POST['url'])) : '';
		$url		= mb_strlen($url) ? $url : time();
		
		$this->db->query('UPDATE manufacturer SET
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
		
		$this->db->query('DELETE FROM manufacturer_description WHERE manufacturer_id = "'.$id.'"');
		$this->db->query('INSERT INTO manufacturer_description (manufacturer_id, h1, name, title, metadesc, metakey, text, spam) 
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
		
		return;
	}
	
	public function setVisibilityManufacturer(){
		$id		= isset($_POST['id']) ? abs((int)$_POST['id']) : 0;
		$activ	= (int)$_POST['activ'] ? 0 : 1;
		
		$this->db->query('UPDATE manufacturer SET visibility = "'.$activ.'" WHERE id = "'.$id.'"');
		
		return $activ;
	}
	
	public function sortOrderManufacturer()
	{
		if (isset($_POST['manufacturer_order'])) for ($i = 0, $cnt = count($_POST['manufacturer_order']); $i < $cnt; $i++){
			$manufacturer_id	= isset($_POST['manufacturer_id'][$i])		? abs((int)$_POST['manufacturer_id'][$i]) : 0;
			$manufacturer_order	= isset($_POST['manufacturer_order'][$i])	? abs((int)$_POST['manufacturer_order'][$i]) : 0;
			
			$this->db->query('UPDATE manufacturer SET `order` = "'.$manufacturer_order.'" WHERE id = "'.$manufacturer_id.'"');
		}
		
		return;
	}

	public function delManufacturer($id = 0)
	{
		$id = abs((int)$id);
		$this->db->query('DELETE FROM manufacturer WHERE id = "'.$id.'"');
		
		return 0;
	}
	
}