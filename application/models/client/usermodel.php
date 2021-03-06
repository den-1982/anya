<?php
class userModel extends CI_Model
{
////////////////////////////////////////////////////////////// LOGOUT_USER
	public function logoutUser()
	{
		$this->session->unset_userdata('user');
		setcookie('user', '', time()-1000, '/', '.'.$_SERVER['SERVER_NAME']);	# для всех доменов
		setcookie('user', '', time()-1000);										# удалить если нет поддоменов!!
		
		return;
	}
////////////////////////////////////////////////////////////// AUTH USER	
	public function authUser($phone = '', $password = '')
	{
		$number		= preg_replace("/[^0-9]/u", '', $phone);	
		$password	= mysql_real_escape_string($password);

		$user = $this->db->query('SELECT * FROM users WHERE number = "'.$number.'" AND pass LIKE "'.$password.'"')->row();
		if (isset($user->id)){
			$this->session->set_userdata('user', $user->id);
			return 1;
		}
		
		return 0;
	}
////////////////////////////////////////////////////////////// GET USER
	public function getUser()
	{
		$id = abs((int)$this->session->userdata('user'));
		$user = $this->db->query('SELECT 
										u.*, 
										d.percent AS discount, 
										d.name AS discount_name,
										(SELECT cityRu FROM novaposhta n WHERE n.city_id = u.city LIMIT 1) AS np_city_name,
										(SELECT addressRu FROM novaposhta n WHERE n.wareId = u.wareId LIMIT 1) AS np_address_ru
									FROM users u
									LEFT JOIN discount d ON d.id = u.discount_id
								WHERE u.id = "'.$id.'"')->row();

		if ( ! $user) return array();
		
		# сумма, количество заказов
		$orders = $this->db->query('SELECT * FROM orders WHERE status = 1 AND id_user = "'.$id.'"')->result();
		$sum = 0;
		foreach ($orders as $order){
			$sum += $order->discount ? round($order->sum - $order->sum * $order->discount / 100 , 2, PHP_ROUND_HALF_UP) : $order->sum;
		}
		
		$user->sum_orders = $sum;
		$user->count_orders = count($orders);
		
		
		return $user;
	}
////////////////////////////////////////////////////////////// GET USER OF DISCOUNT CART
	public function checkCartDiscount($id_cart_discount = '')
	{
		$id_cart_discount = abs((int)$id_cart_discount);
		
		$user = $this->db->query('SELECT * FROM users WHERE user_cart_discount = "'.$id_cart_discount.'"')->row();
		if ($user) $this->session->set_userdata('user', $user->id);

		return $user;
	}
////////////////////////////////////////////////////////////// GET_ORDERS
	public function getOrders($id = 0)
	{
		$id = abs((int)$id);
		
		$orders = array();
		$orders = $this->db->query('SELECT * FROM orders WHERE id_user = '.$id.' ORDER BY `date` DESC')->result();
		foreach ($orders as $k){
			$k->date = date('d-m-Y H:i', $k->date);
			$k->status = $k->status == 0 ? 'ПРИНЯТ' : 'ВЫПОЛНЕНО'; 
		}
		
		return $orders;
	}
////////////////////////////////////////////////////////////// GET_ORDER
	public function getHistoryOrder($id = 0) // история заказов
	{
		$id = abs((int)$id);
		
		return $this->db->query('SELECT oi.*, p.id AS pid, p.name AS pname, p.url FROM order_items oi 
									LEFT JOIN products p ON oi.id_product = p.id 
								WHERE oi.id_order = '.$id)->result();
	}
////////////////////////////////////////////////////////////// ADD_USERS
	public function addUser()
	{
		$error = array();
		
		# NAME
		$post['name'] = isset($_POST['name']) ? clean($_POST['name'], true, true) : '';
		$post['name'] = mb_convert_case($post['name'], MB_CASE_TITLE, "UTF-8");
		if ( ! $post['name']) 
			$error['name'] = 'Поле обязательно для заполнения!';
		
		
		# EMAIL
		$post['email'] = isset($_POST['email']) ? clean($_POST['email'], true, true) : '';
		if ( ! $post['email']) 
			$error['email'] = 'Поле Email обязательно для заполнения!';
		
		if ( ! filter_var($post['email'], FILTER_VALIDATE_EMAIL))  
			$error['email'] = 'Некорректный e-mail!';
		
		# есть ли такой email в db
		if ($this->db->query('SELECT * FROM users WHERE email LIKE "'.$post['email'].'"')->row()) 
			$error['email'] = 'Пользователь с такой почтой уже зарегистрирован.';
		

		# PHONE
		$post['phone']  = isset($_POST['phone']) ? clean($_POST['phone'], true, true) : '';
		$post['number'] = preg_replace("/[^0-9]/u", '', $post['phone']);
		if ( ! $post['number']) 
			$error['phone'] = 'Поле обязательно для заполнения!';
		
		# есть ли такой phone в db
		if ($this->db->query('SELECT * FROM users WHERE number LIKE "'.$post['number'].'"')->row()) 
			$error['phone'] = 'Пользователь с таким номером телефона уже зарегистрирован.';
		
		
		# НОВАЯ ПОЧТА city/wareId
		$post['city']	= isset($_POST['city'])		? abs((int)$_POST['city'])	: 0;
		$post['wareId'] = isset($_POST['wareId'])	? abs((int)$_POST['wareId']): 0;
		
		# PASS
		$post['pass']			= isset($_POST['pass'])			? $_POST['pass'] : '';
		$post['confirm_pass']	= isset($_POST['confirm_pass'])	? $_POST['confirm_pass'] : '';
		
		if ($post['pass'] != $post['confirm_pass']) 
			$error['pass'] = 'Значение Пароль не совпадает со значением Подтверждение!';
		
		# CAPTCHA
		$post['captcha'] = isset($_POST['captcha']) ? $_POST['captcha'] : '';
		$captcha = $this->session->userdata('captcha');
		if ($post['captcha'] != $captcha['word']) 
			$error['captcha'] = 'Не правильный код протекции';
		
		# DISCOUNT
		$post['discount'] = 0;
		
		# TOKEN
		$token = '';
		
		# поле не должно привышать размер 45 символа
		if (mb_strlen($post['name']) > 45)
			$error['name'] = 'Поле должно иметь не больше 45 символов';
		
		# поле не должно привышать размер 45 символа
		if (mb_strlen($post['email']) > 99) 
			$error['email'] = 'Поле должно иметь не больше 100 символов';
		
		# поле не должно привышать размер 45 символа
		if (mb_strlen($post['phone']) > 45) 
			$error['phone'] = 'Поле должно иметь не больше 20 символов';
		
		# поле не должно привышать размер 20 символа
		if (mb_strlen($post['pass']) > 40) 
			$error['pass'] = 'Пароль должно иметь не больше 40 символов';
		
		
		### если есть ошибки вызодим!!!!!!!!!!!
		if (count($error)) return $error;
		

		# присвоить минимальную скидку
		//$d = $this->db->query('SELECT * FROM discount WHERE percent = (SELECT MIN(percent) FROM discount)')->row();
		//$post['discount'] = isset($d->id) ? $d->id : 0;
		
		$this->db->query('INSERT INTO users (name, email, phone, number, city, wareId, pass, discount, token) 
			VALUES(
				"'.$post['name'].'", 
				"'.$post['email'].'", 
				"'.$post['phone'].'", 
				"'.$post['number'].'", 
				"'.$post['city'].'",
				"'.$post['wareId'].'",
				"'.$post['pass'].'",
				"'.$post['discount'].'",
				"'.$token.'"
			)');
		
		### если все ок - аутификация
		$user = $this->db->query('SELECT * 
										FROM users 
									WHERE number = "'.$number.'" 
									AND pass LIKE "'.$password.'"')->row();
		if (isset($user->id))
			$this->session->set_userdata('user', $user->id);
		
		
		return $error;
	}
////////////////////////////////////////////////////////////// EDIT_USER
	public function editUser()
	{
		$error = array();
		
		# USER
		if ( ! $user = $this->userModel->getUser())
			$error['token'] = 'Ошибка!';
		
		# id USER
		$post['id_user'] = $user->id;
		
		# NAME
		$post['name'] = isset($_POST['name']) ? clean($_POST['name'], true, true) : '';
		$post['name'] = mb_convert_case($post['name'], MB_CASE_TITLE, "UTF-8");
		if ( ! $post['name']) 
			$error['name'] = 'Поле обязательно для заполнения!';

		
		
		# EMAIL
		$post['email'] = isset($_POST['email']) ? clean($_POST['email'], true, true) : '';
		if ( ! $post['email']) 
			$error['email'] = 'Поле Email обязательно для заполнения!';
		
		if ( ! filter_var($post['email'], FILTER_VALIDATE_EMAIL))  
			$error['email'] = 'Некорректный e-mail!';
		
		# есть ли такой email в db
		if ($this->db->query('SELECT * FROM users WHERE email = "'.$post['email'].'" AND id <> "'.$post['id_user'].'"')->row()) 
			$error['email'] = 'Пользователь с такой почтой уже зарегистрирован.';
		
		
		# PHONE
		$post['phone']  = isset($_POST['phone']) ? clean($_POST['phone'], true, true) : '';
		$post['number'] = preg_replace("/[^0-9]/u", '', $post['phone']);
		if ( ! $post['number']) 
			$error['phone'] = 'Поле обязательно для заполнения!';
		
		# есть ли такой phone в db
		if ($this->db->query('SELECT * FROM users WHERE number = "'.$post['number'].'" AND id <> "'.$post['id_user'].'" ')->row()) 
			$error['phone'] = 'Пользователь с таким номером телефона уже зарегистрирован.';
		
		
		# НОВАЯ ПОЧТА city/wareId
		$post['city']	= isset($_POST['city'])		? abs((int)$_POST['city'])	: 0;
		$post['wareId'] = isset($_POST['wareId'])	? abs((int)$_POST['wareId']): 0;

		
		# поле не должно привышать размер 45 символа
		if (mb_strlen($post['name']) > 45)
			$error['name'] = 'Поле должно иметь не больше 45 символов';
		
		# поле не должно привышать размер 45 символа
		if (mb_strlen($post['email']) > 99) 
			$error['email'] = 'Поле должно иметь не больше 100 символов';
		
		# поле не должно привышать размер 45 символа
		if (mb_strlen($post['phone']) > 45) 
			$error['phone'] = 'Поле должно иметь не больше 20 символов';

		
		# если есть ошибки вызодим!!!!!!!!!!!
		if (count($error)) return $error;
		
		# EDIT USER
		$this->db->query('UPDATE users SET
										name	= "'.$post['name'].'",
										email	= "'.$post['email'].'",
										phone	= "'.$post['phone'].'",
										number	= "'.$post['number'].'",
										city	= "'.$post['city'].'",
										wareId	= "'.$post['wareId'].'",
										token	= ""
									WHERE id = "'.$user->id.'"');
		
		return $error;
	}
////////////////////////////////////////////////////////////// RECOVER_PASSWORD
	public function editUserPass()	
	{
		$error = array();
		
		# USER
		if ( ! $user = $this->userModel->getUser())
			$error['token'] = 'Ошибка!';
		
		# ID/PASS USER
		$id_user = $user->id;
		$password = $user->pass;
		
		$post['oldpassword']	= isset($_POST['oldpassword'])	? $_POST['oldpassword']	: '';
		$post['password']		= isset($_POST['password'])		? $_POST['password']	: '';
		$post['confirm']		= isset($_POST['confirm'])		? $_POST['confirm']		: '';
		
		# проверка старого пароля
		if ($password != $post['oldpassword']) 
			$error['oldpassword'] = 'Неверный старый пароль!';
		
		# пароль не должен привышать размер 40 символа
		if (mb_strlen($post['password']) > 40) 
			$error['password'] = 'Пароль должен иметь не больше 20 символов';
		
		# неверное подтверждение пароля
		if ($post['password'] != $post['confirm']) 
			$error['confirm'] = 'Неверное подтверждение пароля';
		
		#### если есть ошибки вызодим!!!!!!!!!!! 
		if ($error) return $error;
		
		$this->db->query('UPDATE users SET pass = "'.$post['password'].'" WHERE id = "'.$id_user.'"');
		
		return $error;
	}
////////////////////////////////////////////////////////////// RECOVER_PASSWORD
	public function recoverPassword()
	{ 
		$response = array('error'=>1, 'text'=>'');
		
		$phone = isset($_POST['phone']) ? $_POST['phone'] : '';
		$number = preg_replace("/[^0-9]/u", '', $phone);
		
		$user = $this->db->query('SELECT * FROM users WHERE number = "'.$number.'"')->row();
		
		# если нет такого USER, то выходим или если нет EMAIL, то выходим
		if ( ! isset($user->email)){
			echo json_encode($response);
			exit;
		}
		
		# если все ОК, делаем ошибку 0
		$response['error'] = 0;

		$msg = '
		<div>
		Здравствуйте!
		<br>
		На сайте '.strtoupper($_SERVER['SERVER_NAME']).' создан запрос на
		восстановление пароля для Вашего
		аккаунта.
		<br>
		Ваш пароль для входа: '.$user->pass;
		
		# письмо клиенту
		$to			= $user->email;
		$tema		= 'Востановление пароля';	
		$headers	= "From: ".strtoupper($_SERVER['SERVER_NAME'])." <admin@".strtoupper($_SERVER['SERVER_NAME']).">\r\n";
		$headers	.= "Content-type: text/html; charset=\"utf-8\"";
		mail($to, $tema, $msg, $headers);
		
		# ответ на сайт
		# скрытие email (ha***@ua.fm)*/
		$data= explode('@', $user->email);
		$_a = isset($data[0]) ? $data[0] : '';
		$_b = isset($data[1]) ? $data[1] : '';
		$_r = '';
		for ($i=0; $i < strlen($_a); $i++){
			if($i > 1) 
				$_r .= '*';
			else
				$_r .= $_a{$i};
		}
		$email = $_r.'@'.$_b;
		
		$response['text'] = '<div style="text-align:center;font-size:20px;line-height:30px;">На ваш адрес '.$email.' выслан пароль.</div>';
		
		echo json_encode($response);
		exit;
	}
////////////////////////////////////////////////////////////// _TRANSLIT
	private function _translit($str)
	{
		$arr = array(
			"A"=>"А","B"=>"В","C"=>"С","E"=>"Е","K"=>"К","M"=>"М","O"=>"О","P"=>"Р","T"=>"Т",
			"a"=>"а","b"=>"в","c"=>"с","e"=>"е","k"=>"к","m"=>"м","o"=>"о","p"=>"р","t"=>"т"
		);
		
		return strtr($str,$arr);
	}
	
}


