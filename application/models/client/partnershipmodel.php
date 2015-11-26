<?php
class partnershipModel extends CI_Model
{
	# партнеры
	public function getPartnerships(){
		return $this->db->query('SELECT * FROM partnerships WHERE visibility = 1 ORDER BY `order`')->result();
	}
}