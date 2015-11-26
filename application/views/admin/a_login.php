<!DOCTYPE html>
<html>
<head>
	<title>Админ::login</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="/css/admin/style.css">
	<link rel="stylesheet" type="text/css" href="/css/admin/fonts/font.awesome/font-awesome.css">
	<link rel="stylesheet" type="text/css" href="/css/admin/dialog/dialog.css">
	<script type="text/javascript" src="/js/admin/jquery/jquery.1.10.2.js"></script>
	<script type="text/javascript" src="/js/admin/dialog/dialog.js"></script>
</head>
<body>
<div class="wrap-login">

	<div class="box-login">
		<h2>Login</h2>
		<form id="form" method="POST" action="">
			<div class="form-group">
				<label>Login:</label>
				<input class="inf" type="text" name="login" value="">
			</div>
			<div class="form-group">
				<label>Password:</label>
				<input class="inf" type="password" name="password" value="">
			</div>
			
			<div class="form-group table">
				<div class="cell">
					<button class="button blue" type="submit" name="" value="">Войти</button> 
				</div>
				<div class="cell right">
					<a id="recover" href="#">Забыли пароль?</a>
				</div>
			</div>
		</form>
	</div>
	
</div>

<div class="footer-login">
	<a href="/"><?=$_SERVER['SERVER_NAME']?></a> 
</div>

<script>
$('#recover').on('click', function(e){
	e.preventDefault();
	
	var html =
	'<form>'+
		'<div class="form-group"></div>'+
		'<div class="form-group">'+
			'<label>Введите свой Email:</label>'+
			'<input class="inf" type="text" name="email" value="">'+
		'</div>'+
		'<div class="form-group right">'+
			'<input type="hidden" name="recover" value="">'+
			'<button class="button blue" type="submit" name="recover" value="">Восстановить</button> '+
		'</div>'+
	'</form>';
	
	var D = $(html).dialog({
		title:'Восстановление пароля',
		width:'400px',
		height:'auto'
	});
	
	D.data('dialog').dialog.find('form').on('submit', function(e){
		e.preventDefault();
		
		var l = $('<div class="load"></div>');
		$(document.body).append(l);
		
		$.post('', $(this).serialize(), function(data){
			l.remove();
			
			D.dialog('content', data.response);

		}, 'json');
	});
});
</script>

</body>
</html>