<?php
class settingsModel extends CI_Model
{
	public function getSettings()
	{
		$settings = $this->db->query('SELECT * FROM settings')->row();
		$settings->phone = unserialize($settings->phone);
		$settings->social = unserialize($settings->social);
		$settings->discounts = $this->db->query('SELECT * FROM discount ORDER BY `order` ASC')->result();
		$settings->managers = $this->db->query('SELECT * FROM managers ORDER BY `order` ASC')->result();
		
		return $settings;
	}
}