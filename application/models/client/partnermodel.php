<?php
class partnerModel extends CI_Model
{
	# партнеры
	public function getPartner(){
		return $this->db->query('SELECT * FROM partner WHERE visibility = 1 ORDER BY `order`')->result();
	}
}