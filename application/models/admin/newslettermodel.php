<?php
class newsletterModel extends CI_Model
{	
	public function sendNewsLetter()
	{
		$settings	= $this->data['settings'];
		$tema		= isset($_POST['tema']) ? clean($_POST['tema'], true, false) : '';
		$message	= isset($_POST['message']) ? $_POST['message'] : '';
		
		# EDIT IMG message
		$server = 'http://'.$_SERVER['SERVER_NAME'];
		$message = preg_replace('/(<img.*src=").*(\/.*")(.*>)/iU', "$1". $server ."$2 style=\"max-width:100%;\" $3", $message);

		# all users
		$users = $this->db->query('SELECT * FROM users')->result();
		
		# email
		$tema = strtoupper($_SERVER['SERVER_NAME']) ." - ". $tema;
		$headers = "From:  ". strtoupper($_SERVER['SERVER_NAME']) ." <admin@crystalline.in.ua>\r\n";
		$headers .= "Content-type: text/html; charset=\"utf-8\"";

		$path = ROOT.'/letters/';
		
		foreach ($users as $user){
$msg = '<html>
	<head>
	  <title>'.strtoupper($_SERVER['SERVER_NAME']).' - '.$tema.'</title>
	</head>
	<body>
		<h2>Уважаемый(ая) '.$user->name.'!</h2>
		'.$message.'
		</br>
		</br>
		</br>
		Вы получили это письмо, так как зарегистрированы на сайте <a href="http://'. $_SERVER['SERVER_NAME'] .'" target="_blank">'. $_SERVER['SERVER_NAME'] .'</a>
		</br>
		</br>
		Интернет-магазин страз и хрустального декора «Crystalline»
		</br>';
		
		foreach ($settings->phone as $phone){
			$msg .= $phone.'</br>';
		}
		
		$msg .= '
		www.crystalline.in.ua
		<br>
		Блестящий выбор - Crystalline!
	</body>
</html>';

			# TMP
			//file_put_contents($path.$user->email.'.html', $msg);
			// mail('hamet@ua.fm', $tema, $msg, $headers);
			// exit;
			
			mail($user->email, $tema, $msg, $headers);
		}
		
	}

}


