<?php
class clientModel extends CI_Model
{
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// CALLBACK
	public function callback() # обратный звонок
	{
		$name    = isset($_POST['name']) ? trim(strip_tags($_POST['name'])) : '';
		$email   = isset($_POST['email']) ? trim(strip_tags($_POST['email'])) : '';
		$phone   = isset($_POST['phone']) ? trim(strip_tags($_POST['phone'])) : '';
		$message = isset($_POST['message']) ? trim(strip_tags($_POST['message'])) : '';
		
		if(!$name || !$email || !$message)return;
		
		$settings = $this->db->query('SELECT * FROM settings')->row();
		$manager_email = isset($settings->email) ? $settings->email : ''; 
		
		$msg = '<html>
					<head>
					  <title>Обратная связь '.strtoupper($_SERVER['SERVER_NAME']).'</title>
					</head>
					<body>
						<h2 style="text-align:center; font-weight:normal;color:#fff;background:#53afea;margin:0;">Обратная связь '.strtoupper($_SERVER['SERVER_NAME']).'</h2>
						<table width="100%" cellpadding="4" cellspacing="0" style="border-collapse:collapse;font-size:14px;">
							<tr style="background:#eee;">
								<td width="150px" align="center" style="border:1px solid #ccc;"><small>Имя</small></td>
								<td width="150px" align="center" style="border:1px solid #ccc;"><small>e-mail</small></td>
								<td width="150px" align="center" style="border:1px solid #ccc;"><small>телефон</small></td>
								<td align="center" style="border:1px solid #ccc;"><small>Сообщение</small></td>
							</tr>
							<tr>
								<td valign="top" align="center" style="border:1px solid #ccc;">'.$name.'</td>
								<td valign="top" align="center" style="border:1px solid #ccc;">'.$email.'</td>
								<td valign="top" align="center" style="border:1px solid #ccc;">'.$phone.'</td>
								<td valign="top" style="font-size:12px;border:1px solid #ccc;">'.$message.'</td>
							</tr>
						</table>';		
		
		// $this->email->clear();
		// $this->email->from('admin@crystalline.in.ua', strtoupper($_SERVER['SERVER_NAME']));
		// $this->email->to($manager_email);
		// $this->email->subject("Обратная связь");
		// $this->email->message($msg);
		// $this->email->send();
		
		$to			= $manager_email;
		$tema		= 'Обратная связь';	
		$headers	= "From: ".strtoupper($_SERVER['SERVER_NAME'])." <webmaster@".strtoupper($_SERVER['SERVER_NAME']).">\r\n";
		$headers	.= "Content-type: text/html; charset=\"utf-8\"";
		mail($to, $tema, $msg, $headers);
	}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// BUSINESS
	public function sendBusiness() # бизнес предложение
	{
		$name    = isset($_POST['name']) ? trim(strip_tags($_POST['name'])) : '';
		$country = isset($_POST['country']) ? trim(strip_tags($_POST['country'])) : '';
		$city    = isset($_POST['city']) ? trim(strip_tags($_POST['city'])) : '';
		$subject = isset($_POST['subject']) ? trim(strip_tags($_POST['subject'])) : '';
		$email   = isset($_POST['email']) ? trim(strip_tags($_POST['email'])) : '';
		$phone   = isset($_POST['phone']) ? trim(strip_tags($_POST['phone'])) : '';
		$message = isset($_POST['message']) ? trim(strip_tags($_POST['message'])) : '';
		
		if(!$name || !$email)return;
		
		$settings = $this->db->query('SELECT * FROM settings')->row();

		$msg = '<html>
					<head>
					  <title>Бизнес предложение '.strtoupper($_SERVER['SERVER_NAME']).'</title>
					</head>
					<body>
						<h2 style="text-align:center; font-weight:normal;color:#fff;background:#53afea;margin:0;">Бизнес предложение '.strtoupper($_SERVER['SERVER_NAME']).'</h2>
						<table width="100%" cellpadding="4" cellspacing="0" style="border-collapse:collapse;font-size:14px;">
							<tr>
								<td  width="150px" valign="top" align="center" style="background:#eee;border:1px solid #ccc;text-align:right;">Имя: </td>
								<td style="border:1px solid #ccc;">'.$name.'</td>
							</tr>
							<tr>
								<td  width="150px" valign="top" align="center" style="background:#eee;border:1px solid #ccc;text-align:right;">Страна: </td>
								<td style="border:1px solid #ccc;">'.$country.'</td>
							</tr>
							<tr>
								<td  width="150px" valign="top" align="center" style="background:#eee;border:1px solid #ccc;text-align:right;">Город: </td>
								<td style="border:1px solid #ccc;">'.$city.'</td>
							</tr>
							<tr>
								<td  width="150px" valign="top" align="center" style="background:#eee;border:1px solid #ccc;text-align:right;">Род деятельности: </td>
								<td style="border:1px solid #ccc;">'.$subject.'</td>
							</tr>
							<tr>
								<td  width="150px" valign="top" align="center" style="background:#eee;border:1px solid #ccc;text-align:right;">e-mail: </td>
								<td style="border:1px solid #ccc;">'.$email.'</td>
							</tr>
							<tr>
								<td  width="150px" valign="top" align="center" style="background:#eee;border:1px solid #ccc;text-align:right;">телефон: </td>
								<td style="border:1px solid #ccc;">'.$phone.'</td>
							</tr>
							<tr>
								<td  width="150px" valign="top" align="center" style="background:#eee;border:1px solid #ccc;text-align:right;">сообщение: </td>
								<td style="border:1px solid #ccc;">'.$message.'</td>
							</tr>
						</table>';
		
		$manager_email = isset($settings->email) ? $settings->email : '';
		
		// $this->email->clear();
		// $this->email->from('admin@crystalline.in.ua', strtoupper($_SERVER['SERVER_NAME']));
		// $this->email->to($manager_email);
		// $this->email->subject("Бизнес предложение");
		// $this->email->message($msg);
		// $this->email->send();
		
		$to			= $manager_email;
		$tema		= 'Бизнес предложение';	
		$headers	= "From: ".strtoupper($_SERVER['SERVER_NAME'])." <webmaster@".strtoupper($_SERVER['SERVER_NAME']).">\r\n";
		$headers	.= "Content-type: text/html; charset=\"utf-8\"";
		mail($to, $tema, $msg, $headers);
	}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// DISCOUNTS
	public function getDiscounts() # условия скидок (программа лояльности)
	{
		$html = '';
		//$res = $this->pageModel->getPageSystem('biznes');
		$res = $this->pageModel->getSystemPage('biznes');
		if ( ! $res) return $html;
		
		$html = $res->text;
		
		$html .= '
			<br>
			Просим обращаться по тел:
			<br>';
			foreach ($this->data['settings']->phone as $phone){
				$html .= '
				&nbsp;&nbsp;&nbsp;&nbsp;'.$phone.'<br>';
			}
			if ($this->data['settings']->email){
				$html .= '
				<br>или на e-mail: <a href="mailto:'.$this->data['settings']->email.'">'.$this->data['settings']->email.'</a>';
			}
		return $html;
	}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// UNDER_ORDER
	public function sendUnderOrder() # товар под заказ
	{
		$product = isset($_POST['id']) ? abs((int)$_POST['id']) : 0;
		if ( ! $product) return 1;
		
		$product = $this->db->query('SELECT * FROM products WHERE id = '.$product)->row();
		if ( ! $product) return 1;

		$name    = isset($_POST['name']) ? trim(strip_tags($_POST['name'])) : '';
		$email   = isset($_POST['email']) ? trim(strip_tags($_POST['email'])) : '';
		$phone   = isset($_POST['phone']) ? trim(strip_tags($_POST['phone'])) : '';
		$message = isset($_POST['message']) ? trim(strip_tags($_POST['message'])) : '';
		
		$settings = $this->db->query('SELECT * FROM settings')->row();
		$manager_email = isset($settings->email) ? $settings->email : ''; 
		
		$msg = '<html>
					<head>
					  <title>Товар под заказ '.strtoupper($_SERVER['SERVER_NAME']).'</title>
					</head>
					<body>
						<h2 style="text-align:center; font-weight:normal;color:#fff;background:#53afea;margin:0;">Товар под заказ '.strtoupper($_SERVER['SERVER_NAME']).'</h2>
						<table width="100%" cellpadding="4" cellspacing="0" style="border-collapse:collapse;font-size:14px;">
							<tr style="background:#eee;">
								<td width="150px" align="center" style="border:1px solid #ccc;"><small>Имя</small></td>
								<td width="150px" align="center" style="border:1px solid #ccc;"><small>e-mail</small></td>
								<td width="150px" align="center" style="border:1px solid #ccc;"><small>телефон</small></td>
								<td align="center" style="border:1px solid #ccc;"><small>Сообщение</small></td>
								<td align="center" style="border:1px solid #ccc;"><small>товар</small></td>
							</tr>
							<tr>
								<td valign="top" align="center" style="border:1px solid #ccc;">'.$name.'</td>
								<td valign="top" align="center" style="border:1px solid #ccc;">'.$email.'</td>
								<td valign="top" align="center" style="border:1px solid #ccc;">'.$phone.'</td>
								<td valign="top" style="font-size:12px;border:1px solid #ccc;">'.$message.'</td>
								<td valign="top" style="font-size:12px;border:1px solid #ccc;">
									<a href="http://crystalline.in.ua/'.$product->name.'/p'.$product->id.'/">'.$product->name.'</a>
								</td>
							</tr>
						</table>';
		$msg .= '<table>
			<tr>
				<td style="width:.1%;">
					<img width="100" style="width:100px;" src="'.SERVER.'/img/products/'.$product->id.'/'.$product->id.'.jpg" alt="">
				</td>
				<td>
					<a href="'.SERVER.'/'.$product->name.'/p'.$product->id.'/">'.$product->h1.'</a>
				</td>
			</tr>
		</table>';
		
		// $this->email->clear();
		// $this->email->from('admin@crystalline.in.ua', strtoupper($_SERVER['SERVER_NAME']));
		// $this->email->to($manager_email);
		// $this->email->subject("Товар под заказ");
		// $this->email->message($msg);
		// $this->email->send();
		
		$to			= $manager_email;
		$tema		= 'Товар под заказ';	
		$headers	= "From: ".strtoupper($_SERVER['SERVER_NAME'])." <webmaster@".strtoupper($_SERVER['SERVER_NAME']).">\r\n";
		$headers	.= "Content-type: text/html; charset=\"utf-8\"";
		mail($to, $tema, $msg, $headers);
	}
	
}