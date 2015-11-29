<?php
class partnerModel extends CI_Model
{	
	public function getPartners()
	{	
		$partners = $this->db->query('SELECT p.*,
											pd.name
										FROM partner p
										LEFT JOIN partner_description pd ON pd.partner_id = p.id 
											GROUP BY p.id
											ORDER BY p.order ASC')->result();
		foreach ($partners as $k){
			$k->cache = preg_replace('/(.+)(\/.+)$/', '${1}/_cache_${2}', $k->image);
		}
		
		return $partners;
	}
	
	public function getPartner($id = 0)
	{
		$id = abs((int)$id);
		$partner =  $this->db->query('SELECT * 
											FROM partner p
											LEFT JOIN partner_description cd ON cd.partner_id = p.id
										WHERE p.id = "'.$id.'"')->row();
		
		if ( ! $partner) return array();
		
		# IMAGE
		$partner->cache = preg_replace('/(.+)(\/.+)$/', '${1}/_cache_${2}', $partner->image);

		return $partner;
	}
	
	
	public function addPartner()
	{
		$image		= isset($_POST['image'])	? clean($_POST['image'], true, true) : '';
		$url		= isset($_POST['url'])		? translit(mb_strtolower($_POST['url'])) : '';
		$url		= mb_strlen($url) ? $url : time();
		
		$this->db->query('INSERT INTO partner (image, url) 
							   VALUES (
								"'.$image.'",
								"'.$url.'"
							)');
		
		# ID PARTNER
		$id = $this->db->query('SELECT MAX(id) AS id FROM partner LIMIT 1')->row()->id;
		
		# DESCRIPTION
		$h1			= isset($_POST['h1'])		? clean($_POST['h1'], false, true)		: '';
		$name       = isset($_POST['name'])		? clean($_POST['name'], true, true)		: 'empty';
		$title      = isset($_POST['title'])	? clean($_POST['title'], true, true)	: '';
		$metadesc   = isset($_POST['metadesc'])	? clean($_POST['metadesc'], true, true)	: '';
		$metakey    = isset($_POST['metakey'])	? clean($_POST['metakey'], true, true)	: '';
		$text       = isset($_POST['text'])		? clean($_POST['text'], false, true)	: '';
		$spam       = isset($_POST['spam'])		? clean($_POST['spam'], true, true)		: '';
		
		$this->db->query('INSERT INTO partner_description (partner_id, h1, name, title, metadesc, metakey, text, spam) 
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
	
	public function updatePartner()
	{
		$id			= isset($_POST['id'])	? abs((int)$_POST['id']) : 0;
		$image		= isset($_POST['image'])? clean($_POST['image'], true, true) : '';
		$url		= isset($_POST['url'])	? translit(mb_strtolower($_POST['url'])) : '';
		$url		= mb_strlen($url) ? $url : time();
		
		$this->db->query('UPDATE partner SET
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
		
		$this->db->query('DELETE FROM partner_description WHERE partner_id = "'.$id.'"');
		$this->db->query('INSERT INTO partner_description (partner_id, h1, name, title, metadesc, metakey, text, spam) 
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
	
	public function sortOrderPartner()
	{
		if (isset($_POST['partner_order'])) for ($i = 0, $cnt = count($_POST['partner_order']); $i < $cnt; $i++){
			$partner_id		= isset($_POST['partner_id'][$i])	? abs((int)$_POST['partner_id'][$i]) : 0;
			$partner_order	= isset($_POST['partner_order'][$i])? abs((int)$_POST['partner_order'][$i]) : 0;
			
			$this->db->query('UPDATE partner SET `order` = "'.$partner_order.'" WHERE id = "'.$partner_id.'"');
		}
		
		return;
	}
	
	public function setVisibilityPartner()
	{
		$id		= isset($_POST['id']) ? abs((int)$_POST['id']): 0;
		$activ	= (int)$_POST['activ'] ? 0 : 1;
		
		$this->db->query('UPDATE partner SET visibility = "'.$activ.'" WHERE id = "'.$id.'"');
		
		return $activ;
	}
	
	public function delPartner($id = 0)
	{
		$id = abs((int)$id);
		$this->db->query('DELETE FROM partner WHERE id = "'.$id.'"');
		return 0;
	}

}