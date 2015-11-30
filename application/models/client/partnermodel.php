<?php
class partnerModel extends CI_Model
{
	public function getPartner($id = 0){
		$id = abs((int) $id);
		return $this->db->query('SELECT * 
									FROM partner p
									LEFT JOIN partner_description pd ON pd.partner_id = p.id 
								WHERE p.visibility = 1 AND id = "'.$id.'"')->result();
	}
	
	public function getPartners(){
		return $this->db->query('SELECT p.*,
										pd.name,
										pd.h1,
										CONCAT("/partners/", p.url, "/", "s", p.id, "/") AS _url
									FROM partner p
									LEFT JOIN partner_description pd ON pd.partner_id = p.id 
								WHERE p.visibility = 1 
									ORDER BY p.order')->result();
	}
}