<?php
class Client_lib
{
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////__CALLBACK
	public function sendMail()
	{
		$CI =& get_instance();

		$name  = substr(htmlspecialchars(trim($_POST['name'])), 0, 100);
		$email = substr(htmlspecialchars(trim($_POST['email'])), 0, 100);
		$text  = substr(htmlspecialchars(trim($_POST['text'])), 0, 1000);
		
		

		$to = $CI->data['info']->email;

		if(empty($name)){ $name = 'неизвестно';}
		if(empty($email)){$email = 'неизвестно';}
		if(empty($text)){ return;}

		$tema    = "Письмо от www.sketch.dp.ua";
		$headers  = "From: sketch.dp.ua <sketch@dp.ua>\r\n";
		$headers .= "Content-type: text/html; charset=\"utf-8\"";

		$msg = '<html>
					<head>
					  <title>Сообщение сайта '.$_SERVER['SERVER_NAME'].'</title>
					</head>
					<body>
						<h2 style="text-align:center; font-weight:normal;color:#fff;background:#53afea;margin:0;">sketch.dp.ua</h2>
						<table width="100%" cellpadding="4" cellspacing="0" style="border-collapse:collapse;font-size:14px;">
							<tr style="background:#eee;">
								<td width="150px" align="center" style="border:1px solid #ccc"><small>Имя</small></td>
								<td width="150px" align="center" style="border:1px solid #ccc"><small>Телефон / e-mail</small></td>
								<td align="center" style="border:1px solid #ccc"><small>Сообщение</small></td>
							</tr>
							<tr>
								<td valign="top" align="center" style="border:1px solid #ccc">'.$name.'</td>
								<td valign="top" align="center" style="border:1px solid #ccc">'.$email.'</td>
								<td valign="top" style="font-size:12px;border:1px solid #ccc">'.$text.'</td>
							</tr>
						</table>';
		mail($to, $tema, $msg, $headers);
	}

}//END
?>
