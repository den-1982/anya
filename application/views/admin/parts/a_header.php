<!DOCTYPE html>
<html>
<head>
	<title>Админ::<?=$h1?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<link rel="stylesheet" type="text/css" href="/css/admin/style.css">
	<link rel="stylesheet" type="text/css" href="/css/admin/fonts/font.awesome/font-awesome.css">
	
	<link rel="stylesheet" type="text/css" href="/css/admin/jquery/jquery-ui.1.11.0.css">
	<link rel="stylesheet" type="text/css" href="/css/admin/lightbox/lightbox.css">
	<link rel="stylesheet" type="text/css" href="/css/admin/dialog/dialog.css">
	<link rel="stylesheet" type="text/css" href="/css/admin/fm/fm.css">
	
	<script type="text/javascript" src="/js/admin/jquery/jquery.1.10.2.js"></script>
	<script type="text/javascript" src="/js/admin/jquery/jquery-ui.1.11.0.js"></script>
	<script type="text/javascript" src="/js/admin/tinymce/tinymce.min.js"></script>
	<script type="text/javascript" src="/js/admin/lightbox/lightbox.min.js"></script>
	<script type="text/javascript" src="/js/admin/mask/mask.js"></script>
	<script type="text/javascript" src="/js/admin/dialog/dialog.js"></script>
	<script type="text/javascript" src="/js/admin/fm/fm.js"></script>
	<script type="text/javascript" src="/js/admin/functions.js"></script>
</head>
<body>
	<div class="wrapper">
	
		<div class="header">
		
			<ul class="menu">
				<li>
					<a class="<?=$action == 'user'?'activ':'';?>" href="/admin/user"><i class="icon-group"></i>Клиенты</a>
				</li>
				<li>
					<span class="has-child"><i class="icon-shopping-cart"></i>Магазин</span>
					<ul>
						<li>
							<a class="<?=$action == 'category'?'activ':'';?>" href="/admin/category">Категории</a>
						</li>
						<li>
							<a class="<?=$action == 'products'?'activ':'';?>" href="/admin/products">Продукты</a>
						</li>
						<li>
							<a class="<?=$action == 'filter'?'activ':'';?>" href="/admin/filter">Фильтр</a>
						</li>
						<li>
							<a class="<?=$action == 'manufacturer'?'activ':'';?>" href="/admin/manufacturer">Производители</a>
						</li>
					</ul>
				</li>
				<li>
					<span class="has-child"><i class="icon-sitemap"></i>Страницы</span>
					<ul>
						<li>
							<a class="<?=$action == 'home'?'activ':'';?>" href="/admin/home">Главная</a>
						</li>
						<li>
							<a class="<?=$action == 'about'?'activ':'';?>" href="/admin/about">О нас</a>
						</li>
						<li>
							<a class="<?=$action == 'oplata'?'activ':'';?>" href="/admin/oplata">Оплата и доставка</a>
						</li>
						<li>
							<a class="<?=$action == 'biznes'?'activ':'';?>" href="/admin/biznes">Бизнес предложение</a>
						</li>
						<li>
							<a class="<?=$action == 'partnerships'?'activ':'';?>" href="/admin/partnerships">Партнеры</a>
						</li>
						<li>
							<a class="<?=$action == 'pages'?'activ':'';?>" href="/admin/pages">Новостм \ Статьи</a>
						</li>
					</ul>
				</li>
				<!--
				<li>
					<span class="has-child">Разное</span>
					<ul>
						<li>
							<a class="<?=$action == 'carts'?'activ':'';?>" href="/admin/carts">Карты-скидка</a>
						</li>
						<li>
							<a class="<?=$action == 'newsletter'?'activ':'';?>" href="/admin/newsletter">Рассылка</a>
						</li>
						<li>
							<a class="<?=$action == 'novaposhta'?'activ':'';?>" href="/admin/novaposhta">Новая почта</a>
						</li>
					</ul>
				</li>
				-->
				<li>
					<a class="<?=$action == 'settings'?'activ':'';?>" href="/admin/settings"><i class="icon-cogs"></i>Настройки</a>
				</li>
				<li>
					<a class="FM-overview" href="#"><i class="icon-picture"></i>Файлы</a>
				</li>
				
				<li class="logout">
					<a href="/admin/?logout" title="Выход"><i class="icon-signout"></i></a>
				</li>
				<li class="site">
					<a href="/" target="_blank" title="Перейти на сайт"><i class="icon-globe"></i></a>
				</li>
			</ul>
			
			<ul class="menu-info">
				<li>
					<a class="<?=$new_count_orders ? 'activ ' : ''?>" href="/admin">Заказы (<?=$new_count_orders?>)</a>
				</li>
				<li>
					<a class="<?=$new_count_comments ? 'activ ' : ''?>" href="/admin/reviews">Отзывы (<?=$new_count_comments?>)</a>
				</li>
				<li>
					<a class="<?=$count_waitlist ? 'activ ' : ''?>" href="/admin/waitlist">Ждут товаров (<?=$count_waitlist?>)</a>
				</li>
			</ul>
		</div>