<?php
class userModel extends CI_Model
{
	public function getUser($id = 0)
	{
		$id = abs((int)$id);
		$user = $this->db->query('SELECT * FROM users WHERE id = '.$id)->row();
		
		if ( ! $user) return array();
		
		# ->->-> $this->ordersModel->getOrdersOfUser();
		$user->orders = $this->db->query('SELECT o.*, u.id AS uid, u.name AS uname 
												FROM orders o 
												LEFT JOIN users u ON u.id = o.id_user 
											WHERE o.id_user = '.$user->id.' 
												ORDER BY o.date DESC')->result();
		
		return $user;
	}
	
	public function setUserDiscount($id = 0, $id_discount = 0)
	{
		$id_user		= isset($_POST['id_user']) ? abs((int)$_POST['id_user']) : 0;
		$id_discount	= isset($_POST['id_discount']) ? abs((int)$_POST['id_discount']) : 0;
		
		$this->db->query('UPDATE users SET discount = '.$id_discount.' WHERE id = '.$id_user);
		return;
	}

	public function getUsers()
	{
		$sort = isset($_GET['sort']) ? (int)$_GET['sort'] : 0;
		$__sort__ = '';
		switch($sort){
			case 2: 
				$__sort__ = ' ORDER BY name ASC';
				break;
			case 1: 
				$__sort__ = ' ORDER BY allcount DESC';
				break;
			case 0:
				$__sort__ = ' ORDER BY allsumm DESC';
				break;
			default:
				$__sort__ = ' ORDER BY allsumm DESC';
				break;
		}
		
		$users = $this->db->query('SELECT *, 
							(SELECT SUM(sum) FROM orders o WHERE o.id_user = u.id) AS allsumm, 
							(SELECT COUNT(*) FROM orders o WHERE o.id_user = u.id) AS allcount 
						FROM users u 
						'. $__sort__)->result();
		
		return $users;
	}
	
	public function addUser()
	{
		$error = array();

		$post['name'] = isset($_POST['name']) ? preg_replace("/\s+/", ' ', $_POST['name']) : '';
		$post['name'] = mb_convert_case($post['name'], MB_CASE_TITLE, "UTF-8");
		
		# проверка EMAIL
		$post['email'] = isset($_POST['email']) ? $_POST['email'] : '';
		// if ( ! filter_var($post['email'], FILTER_VALIDATE_EMAIL))  $error['email'] = 'Некорректный e-mail!';
		# есть ли такой EMAIL в бвзе
		// $res = $this->db->query('SELECT * FROM users WHERE email = "'.$post['email'].'"')->row();
		// if ( $res ) $error['email'] = 'Пользователь с такой почтой уже зарегистрирован.';
		
		# проверка PHONE
		$post['phone']  = isset($_POST['phone']) ? trim($_POST['phone']) : '';
		$post['number'] = preg_replace("/[^0-9]/u", '', $post['phone']);
		// if ( ! $post['number']) $error['phone'] = 'Поле Мобильный телефон обязательно для заполнения!';
		# есть ли такой PHONE в бвзе
		// $res = $this->db->query('SELECT * FROM users WHERE number = "'.$post['number'].'"')->row();
		// if ( $res ) $error['phone'] = 'Пользователь с таким номером мобильного телефона уже зарегистрирован.';
		
		
		# проверка CITY
		$post['city'] = isset($_POST['city']) ? abs((int)$_POST['city']): 0;
		$post['wareId'] = isset($_POST['wareId']) ? abs((int)$_POST['wareId']): 0;
		
		# скидочный талон
		$post['user_cart_discount'] = isset($_POST['user_cart_discount']) ? trim($_POST['user_cart_discount']) : '';
		$post['user_cart_percent'] = isset($_POST['user_cart_percent']) ? abs(str_replace(',', '.', $_POST['user_cart_percent'])*1): 0;
		
		# ID discount
		$post['discount'] = isset($_POST['discount']) ? abs((int)$_POST['discount']): 0;
		
		if ($error) return $error;
		
		$this->db->query('INSERT INTO users (name, email, phone, number, city, wareId, discount, user_cart_discount, user_cart_percent)
							VALUES(
								"'.$post['name'].'",
								"'.$post['email'].'",
								"'.$post['phone'].'",
								"'.$post['number'].'",
								"'.$post['city'].'",
								"'.$post['wareId'].'",
								"'.$post['discount'].'",
								"'.$post['user_cart_discount'].'",
								"'.$post['user_cart_percent'].'"
							)');
							
		return $error;
	}
	
	public function editUser()
	{
		$error = array();
		
		$post['id']	= isset($_POST['id']) ? abs((int)$_POST['id']) : 0;

		$post['name'] = isset($_POST['name']) ? preg_replace("/\s+/", ' ', $_POST['name']) : '';
		$post['name'] = mb_convert_case($post['name'], MB_CASE_TITLE, "UTF-8");
		
		# проверка EMAIL
		$post['email'] = isset($_POST['email']) ? $_POST['email'] : '';
		// if ( ! filter_var($post['email'], FILTER_VALIDATE_EMAIL))  $error['email'] = 'Некорректный e-mail!';
		# есть ли такой EMAIL в бвзе
		// $res = $this->db->query('SELECT * FROM users WHERE email = "'.$post['email'].'" AND id <> '.$post['id'])->row();
		// if ( $res ) $error['email'] = 'Пользователь с такой почтой уже зарегистрирован.';
		
		# проверка PHONE
		$post['phone']  = isset($_POST['phone']) ? trim($_POST['phone']) : '';
		$post['number'] = preg_replace("/[^0-9]/u", '', $post['phone']);
		// if ( ! $post['number']) $error['phone'] = 'Поле Мобильный телефон обязательно для заполнения!';
		# есть ли такой PHONE в бвзе
		// $res = $this->db->query('SELECT * FROM users WHERE number = "'.$post['number'].'" AND id <> '.$post['id'])->row();
		// if ( $res ) $error['phone'] = 'Пользователь с таким номером мобильного телефона уже зарегистрирован.';
		
		
		# проверка CITY
		$post['city'] = isset($_POST['city']) ? abs((int)$_POST['city']): 0;
		$post['wareId'] = isset($_POST['wareId']) ? abs((int)$_POST['wareId']): 0;
		
		# скидочный талон
		$post['user_cart_discount'] = isset($_POST['user_cart_discount']) ? trim($_POST['user_cart_discount']) : '';
		$post['user_cart_percent'] = isset($_POST['user_cart_percent']) ? abs(str_replace(',', '.', $_POST['user_cart_percent'])*1): 0;
		
		# ID discount
		$post['discount'] = isset($_POST['discount']) ? abs((int)$_POST['discount']): 0;
		
		if ($error) return $error;
		
		$this->db->query('UPDATE users SET
								name = "'.$post['name'].'", 
								email = "'.$post['email'].'", 
								phone = "'.$post['phone'].'", 
								number = "'.$post['number'].'", 
								city = "'.$post['city'].'",
								wareId = "'.$post['wareId'].'",
								discount = "'.$post['discount'].'",
								user_cart_discount = "'.$post['user_cart_discount'].'",
								user_cart_percent = "'.$post['user_cart_percent'].'"
							WHERE id = '.$post['id']);
		
		return $error;
	}
	
	public function delUsers($id = 0)
	{
		$id = abs((int)$id);
		$this->db->query('DELETE FROM users WHERE id = '.$id);
		return 0;
	}
	
}