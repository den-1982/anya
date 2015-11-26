<?php
class cartsModel extends CI_Model
{
	
	public function getCarts()
	{
		return $this->db->query('SELECT * FROM discount_carts')->result();
	}
	
	public function getCart($code = '')
	{
		$code = mysql_real_escape_string(trim($code));
		return $this->db->query('SELECT * FROM discount_carts WHERE code = "'.$code.'"')->row();
	}
	
	public function addCart()
	{
		$error['response'] = array();
		
		$code		= isset($_POST['code']) ? mysql_real_escape_string(trim($_POST['code'])) : '';
		$percent	= isset($_POST['percent']) ? abs(str_replace(',', '.', $_POST['percent'])*1) : '';
		
		if ( ! $code) $error['response'][] = 'Не указан номер карты!';
		if ( ! $percent ) $error['response'][] = 'Не указана скидка!';
		
		# проверка
		$res = $this->db->query('SELECT * FROM discount_carts WHERE code = "'.$code.'"')->row();
		if ( $res ) $error['response'][] = 'Уже есть такой номер карты!';
		
		if ( $error['response'] ) return $error;
		
		$this->db->query('INSERT INTO discount_carts (code, percent) 
							VALUES(
								"'.$code.'",
								"'.$percent.'"
							)');
		return $error;
	}
	
	public function editCart()
	{
		$error['response'] = array();
		
		$code = isset($_POST['code']) ? mysql_real_escape_string(trim($_POST['code'])) : '';
		$percent = isset($_POST['percent']) ? abs(str_replace(',', '.', $_POST['percent'])*1) : '';
		
		if ( ! $percent ) $error['response'][] = 'Не указана скидка!';
		
		if ( $error['response'] ) return $error;

		$this->db->query('UPDATE discount_carts 
							SET percent = "'.$percent.'"
						WHERE code = "'.$code.'"');
	
		return $error;
	}

	public function delCart($code = '')
	{
		$code = mysql_real_escape_string(trim($code));
		$this->db->query('DELETE FROM discount_carts WHERE code = "'.$code.'"');
		return 0;
	}
	
}