<?php
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// ADMIN
class settingsModel extends CI_Model
{
	static $settingsData;
	
	public function getSettings()
	{
		if (self::$settingsData) return self::$settingsData;
		
		$settings = $this->db->query('SELECT * FROM settings')->row();
		
		$settings->phone		= unserialize($settings->phone);
		$settings->discounts	= $this->db->query('SELECT * FROM discount ORDER BY `order` ASC')->result();
		$settings->social 		= unserialize($settings->social);
		$settings->managers		= $this->db->query('SELECT * FROM managers ORDER BY `order` ASC')->result();
		$settings->sizes 		= unserialize($settings->sizes);
		
		self::$settingsData = $settings;
		
		return $settings;
	}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// EDIT SETTINGS
	public function editSettings()
	{
		$data['manager']	= isset($_POST['manager']) ? clean($_POST['manager'], true, true) : '';
		$data['skype']		= isset($_POST['skype']) ? clean($_POST['skype'], true, true) : '';
		$data['email']		= isset($_POST['email']) ? clean($_POST['email'], true, true) : '';
		
		$data['analitics']	= isset($_POST['analitics']) ? clean($_POST['analitics'], false, true) : '';
		$data['metrica']	= isset($_POST['metrica']) ? clean($_POST['metrica'], false, true) : '';
		
		# PHONE
		$phones	= isset($_POST['phone']) && is_array($_POST['phone']) ? $_POST['phone'] : array();
		$data['phone'] = array();
		foreach ($phones as &$phone){
			if ($phone = trim(preg_replace('/[^0-9\-\+\(\)\s]/iu', '', $phone)))
				$data['phone'][] = preg_replace('/\s+/', ' ', $phone);
		}
		$data['phone'] = clean(serialize($data['phone']), false,true);
		
		# FAX
		$data['fax'] = isset($_POST['fax']) ? $_POST['fax'] : '';
		$data['fax'] = trim(preg_replace('/[^0-9\-\+\(\)\s]/iu', '', $data['fax']));
		$data['fax'] = preg_replace('/\s+/', ' ', $data['fax']);
		
		# SOCIAL - соц. сети
		$data['social'] = array();
		if (isset($_POST['social_name'])){
			for ($i = 0, $cnt = count($_POST['social_name']); $i < $cnt; $i++){

				$name = isset($_POST['social_name'][$i]) ? trim($_POST['social_name'][$i]) : '';
				$link = isset($_POST['social_link'][$i]) ? trim($_POST['social_link'][$i]) : '';
				
				if (!$name || !$link) continue;
				
				$data['social'][$name] = $link;
			}
		}
		$data['social'] = clean(serialize($data['social']), false, true);
		
		
		# ADRESS
		$data['country']	= isset($_POST['country'])		? clean($_POST['country'], false, true) : '';
		$data['city']		= isset($_POST['city'])			? clean($_POST['city'], false, true) : '';
		$data['address']	= isset($_POST['address'])		? clean($_POST['address'], false, true) : '';
		$data['postal_code']= isset($_POST['postal_code'])	? clean($_POST['postal_code'], false, true) : '';
		$data['map']		= isset($_POST['map'])			? clean($_POST['map'], false, true) : '';
		$data['coordinates']= isset($_POST['coordinates'])	? clean($_POST['coordinates'], false, true) : '';
		
		# SETTINGS
		$this->db->query('UPDATE settings SET 
							manager = "'.$data['manager'].'",
							skype = "'.$data['skype'].'",
							email = "'.$data['email'].'",
							phone = "'.$data['phone'].'",
							fax = "'.$data['fax'].'",
							social = "'.$data['social'].'",
							
							country = "'.$data['country'].'",
							city = "'.$data['city'].'",
							address = "'.$data['address'].'",
							postal_code = "'.$data['postal_code'].'",
							map = "'.$data['map'].'",
							coordinates = "'.$data['coordinates'].'",
							
							analitics = "'.$data['analitics'].'",
							metrica = "'.$data['metrica'].'"
						');

		# DISCOUNTS СКИДКИ
		$this->db->query('DELETE FROM discount');
		if (isset($_POST['discount_name']['update'])) foreach ($_POST['discount_name']['update'] as $k=>$v){
				$id			= abs((int)$k);
				$name		= isset($_POST['discount_name']['update'][$k])		? clean($_POST['discount_name']['update'][$k], true, true) : '';
				$percent	= isset($_POST['discount_percent']['update'][$k])	? abs((float) str_replace(',', '.', $_POST['discount_percent']['update'][$k])) : '';
				$order		= isset($_POST['discount_order']['update'][$k])		? abs((int)$_POST['discount_order']['update'][$k]) : 0;

				if ( ! $percent) continue;
				
				$this->db->query('INSERT INTO discount (id, name, percent, `order`) 
									VALUES(
										"'.$id.'", 
										"'.$name.'", 
										"'.$percent.'", 
										"'.$order.'"
									)');
			
		}
		if (isset($_POST['discount_name']['insert'])) foreach ($_POST['discount_name']['insert'] as $k=>$v){
				$name		= isset($_POST['discount_name']['insert'][$k])		? clean($_POST['discount_name']['insert'][$k], true, true) : '';
				$percent	= isset($_POST['discount_percent']['insert'][$k])	? abs((float) str_replace(',', '.', $_POST['discount_percent']['insert'][$k])) : '';
				$order		= isset($_POST['discount_order']['insert'][$k])		? abs((int)$_POST['discount_order']['insert'][$k]) : 0;
				
				if ( ! $percent) continue;
				
				$this->db->query('INSERT INTO discount (name, percent, `order`) 
									VALUES(
										"'.$name.'", 
										"'.$percent.'", 
										"'.$order.'"
									)');
		}
		

		# MANAGERS
		$this->db->query('DELETE FROM managers');
		if (isset($_POST['manager_name']['update'])){
			foreach ($_POST['manager_name']['update'] as $k=>$v){ 
				$id 	= abs((int)$k);
				$name	= isset($_POST['manager_name']['update'][$k]) ? mb_convert_case($_POST['manager_name']['update'][$k], MB_CASE_TITLE, "UTF-8") : '';
				$position = isset($_POST['manager_position']['update'][$k]) ? clean($_POST['manager_position']['update'][$k], true, true) : '';
				$image	= isset($_POST['manager_image']['update'][$k]) ? clean($_POST['manager_image']['update'][$k], false, true) : '';
				$phone	= isset($_POST['manager_phone']['update'][$k]) ? clean($_POST['manager_phone']['update'][$k], true, true) : '';
				$email	= isset($_POST['manager_email']['update'][$k]) ? clean($_POST['manager_email']['update'][$k], true, true) : '';
				$skype	= isset($_POST['manager_skype']['update'][$k]) ? clean($_POST['manager_skype']['update'][$k], true, true) : '';
				$order	= isset($_POST['manager_order']['update'][$k]) ? abs((int)$_POST['manager_order']['update'][$k]) : 0;
				
				$name = trim(preg_replace("/\s+/", ' ', $name));
				$name = clean($name, true, true);
				
				if ( ! $name) continue;
				
				$this->db->query('INSERT INTO managers (id, name, position, image, phone, email, skype, `order`) 
									VALUES(
										"'.$id.'",
										"'.$name.'",
										"'.$position.'",
										"'.$image.'",
										"'.$phone.'",
										"'.$email.'",
										"'.$skype.'",
										"'.$order.'"
									)');
			}
		}
		if (isset($_POST['manager_name']['insert'])){
			foreach ($_POST['manager_name']['insert'] as $k=>$v){
				$name	= isset($_POST['manager_name']['insert'][$k]) ? mb_convert_case($_POST['manager_name']['insert'][$k], MB_CASE_TITLE, "UTF-8") : '';
				$position = isset($_POST['manager_position']['insert'][$k]) ? clean($_POST['manager_position']['insert'][$k], true, true) : '';
				$image	= isset($_POST['manager_image']['insert'][$k]) ? clean($_POST['manager_image']['insert'][$k], false, true) : '';
				$phone	= isset($_POST['manager_phone']['insert'][$k]) ? clean($_POST['manager_phone']['insert'][$k], true, true) : '';
				$email	= isset($_POST['manager_email']['insert'][$k]) ? clean($_POST['manager_email']['insert'][$k], true, true) : '';
				$skype	= isset($_POST['manager_skype']['insert'][$k]) ? clean($_POST['manager_skype']['insert'][$k], true, true) : '';
				$order	= isset($_POST['manager_order']['insert'][$k]) ? abs((int)$_POST['manager_order']['insert'][$k]) : 0;
				
				$name = trim(preg_replace("/\s+/", ' ', $name));
				$name = clean($name, true, true);
				
				if ( ! $name) continue;
				
				$this->db->query('INSERT INTO managers (name, position, image, phone, email, skype, `order`) 
									VALUES(
										"'.$name.'",
										"'.$position.'",
										"'.$image.'",
										"'.$phone.'",
										"'.$email.'",
										"'.$skype.'",
										"'.$order.'"
									)');
			}
		}
		
		# SIZEZ - размеры изображений для filesModel
		$sizes = array();
		$this->db->query('UPDATE settings SET sizes = "'. serialize($sizes) .'"');
		if (isset($_POST['image_size'])){
			foreach ($_POST['image_size'] as $size){
				$size = abs((int)$size);
				if ( $size ) $sizes[] = $size;
			}
			
			$this->db->query('UPDATE settings SET sizes = "'. serialize($sizes) .'"');
		}
		
		
		# ADMIN редактирование админ данных
		if (isset($_POST['admin']['login'])){
			$_login = isset($_POST['admin']['login']) ? clean($_POST['admin']['login'], false, true) : '';
			
			$this->db->query('UPDATE admin SET login = "'.$_login.'" ');
		}
		if (isset($_POST['admin']['password'])){
			$_password = isset($_POST['admin']['password']) ? $_POST['admin']['password'] : '';
			
			$this->db->query('UPDATE admin SET password = "'.sha1($_password).'" ');
		}
		if (isset($_POST['admin']['email'])){		
			$_email = isset($_POST['admin']['email']) ? clean($_POST['admin']['email'], true, true) : '';
			
			$this->db->query('UPDATE admin SET email = "'.$_email.'" ');
		}
		

		return;
	}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////// GET SOCIAL
	public function getSocial()
	{	
		return $this->db->query('SELECT * FROM social')->result();
	}
}