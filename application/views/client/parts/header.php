<!DOCTYPE html>
<html>
<head>
	<title><?=htmlspecialchars($title)?></title>
	<meta name="description" content="<?=htmlspecialchars($metadesc)?>">
	<meta name="keywords" content="<?=htmlspecialchars($metakey)?>">
	<meta name="robots" content="index, follow">
	<meta name="revisit-after" content="7 days">
	<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
	<meta content="width=device-width, initial-scale=1.0" name="viewport">
	
	<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
	<link type="text/css" href="/css/client/style.css" rel="stylesheet">
	<link type="text/css" href="/css/client/responsive.css" rel="stylesheet">
	<link type="text/css" href="/css/client/slideshow/slideshow.css" rel="stylesheet">
	<link type="text/css" href="/css/client/lightbox/lightbox.css" rel="stylesheet">
	<link type="text/css" href="/css/client/formstyler/jquery.formstyler.css" rel="stylesheet">
	<link type="text/css" href="/css/client/carusel/owl.carousel.css" rel="stylesheet">
	
	<!--[if lt IE 9]>
	<script>
	var e = ("article,aside,figcaption,figure,footer,header,hgroup,nav,section,time").split(',');
	for (var i = 0; i < e.length; i++) document.createElement(e[i]);
	</script>
	<![endif]-->
	
	<script src="/js/client/jquery.1.9.1.js" type="text/javascript"></script>
	<script src="/js/client/slideshow/slideshow.min.js" type="text/javascript"></script>
	<script src="/js/client/lightbox/lightbox.min.js" type="text/javascript"></script>
	<script src="/js/client/mask/mask.js" type="text/javascript"></script>
	<script src="/js/client/formstyler/jquery.formstyler.min.js" type="text/javascript"></script>
	<script src="/js/client/carusel/owl.carousel.min.js" type="text/javascript"></script>
	<script src="/js/client/functions.js" type="text/javascript"></script>
	
	<?=$settings->analitics?>
</head>
<body oncopy="return false">

	<div class="wrapper">
		<header class="header">
			<div class="nav">
				<div class="row">
					<a class="logo" href="/" title="Интернет - магазин Crystalline">
						<img src="/img/i/logo.png" alt="crystalline">
					</a>

					<div class="h-left">
						<div class="phones-box">
							<?php 
							$phones = array_chunk($settings->phone, ceil(count($settings->phone)/2)); 
							foreach ($phones as $phone):?>
							<ul class="phones clearfix">
								<?php foreach ($phone as $n):?>
								<li>
									<a href="tel:+<?=preg_replace('/[^0-9]/', '', $n)?>"><?=$n?></a>
								</li>
								<?php endforeach;?>
							</ul>
							<?php endforeach;?>
						</div>
					</div>

					<div class="h-right">
						<ul class="details">
							<?php if ($user):?>
							<li>
								<a class="details-link" href="/user"><b><?=$user->phone?></b></a>
							</li>
							<li><span class="details-link">&bull;</span></li>
							<li>
								<a class="details-link activ" href="/user/logout">Выход</a>
							</li>
							<?php else:?>
							<li>
								<a class="details-link" data-user="auth" href="/user/login">Вход</a>
							</li>
							<li><span class="details-link">&bull;</span></li>
							<li>
								<a class="details-link" href="/user/add">Регистрация</a>
							</li>
							<?php endif;?>
							<li><span class="details-link">&bull;</span></li>
							<li>
								<a class="details-link" href="/cart">Корзина (<span data-cart="count-items"><?=$cart['cnt_items']?></span>)</a>
							</li>
						</ul>
						
						<div class="search-box">
							<div class="search-box-layer">
								<form action="/search">
									<input class="form-control form-control-search" type="text" name="w" value="" placeholder="поиск">
									<button class="form-search-button" type="submit" name=""></button>
								</form>
							</div>
						</div>
					</div>
				</div>

				<div class="backing-menu">
					<nav class="menu-box" data-menu="fix">
						<ul class="menu" data-dropdown="">
							<li>
								<a href="/" title="Главная">Главная</a>
							</li>
							<li>
								<span data-dropdown=""  title="Продукция">Продукция</span>
								<?php if (isset($categories[0])):?>
								<i class="nib"></i>
								<div class="child child-category">
								<?php $uls = array_chunk($categories[0], ceil(count($categories[0])/4));?>
								<?php foreach($uls as $ul):?>
									<ul>
									<?php foreach($ul as $category):?>
										<li>
											<a href="<?=$category->_url?>" title="<?=htmlspecialchars($category->name)?>">
												<span>
													<img alt="<?=htmlspecialchars($category->name)?>" src="<?=htmlspecialchars($category->mini_image)?>">
												</span>
												<?php if ($category->discount*1):?>
												<i title="скидка <?=$category->discount*1?> %">&minus; <?=$category->discount*1?> %</i>
												<?php endif;?>
												<?=$category->name?>
											</a>
										</li>
									<?php endforeach;?>
									</ul>
								<?php endforeach;?>
								</div>
								<?php endif;?>
							</li>
							<li>
								<a href="/about-us" title="О нас">О нас</a>
							</li>
							<li>
								<a href="/biznes-predlojenie/" title="Бизнес предложение">Бизнес предложение</a>
							</li>
							<li>
								<a href="/oplata-i-dostavka/" title="Оплата и доставка">Оплата и доставка</a>
							</li>
							<li>
								<a href="/reviews" title="Отзывы">Отзывы</a>
							</li>
							<li>
								<a href="/contacts/" title="Контакты">Контакты</a>
							</li>
							<li>
								<a href="javascript:void(0)" data-search="novaposhta" title="Отседить заказ НОВАЯ ПОЧТА">Трек КОД</a>
							</li>
							
							<li>
								<a class="colors" data-dropdown="" href="javascript:void(0)" title="Подбор по цвету">
									<img src="/img/i/colors.png" alt="">
								</a>
								<i class="nib"></i>
								<div class="child child-colors">
									<ul>
									<?php foreach($filter_items_color as $k):?>
										<li>
											<a href="<?=$k->_url?>" title="<?=htmlspecialchars($k->name)?>">
												<img alt="<?=htmlspecialchars($k->name)?>" src="<?=htmlspecialchars($k->image)?>">
											</a>
										</li>
									<?php endforeach;?>
									</ul>
								</div>
							</li>
						</ul>
					</nav>
				</div>
			</div>
			
			<div class="nav-mobil">
				<ul class="menu-mobile">
					<li class="mm-group">
						<span class="icon-mm icon-mm-menu" data-nav-group="toggle" onclick="$('.nav-group').toggleClass('activ');"></span>
					</li>
					<li class="mm-user">
						<a class="icon-mm icon-mm-user" href="/user"></a>
					</li>
					<li class="mm-logo">
						<a class="logo-mini" href="/">
							<img src="/img/i/logo.png" alt="crystalline">
						</a>
					</li>
					<li class="mm-search">
						<span class="icon-mm icon-mm-search"></span>
					</li>
					<li class="mm-cart">
						<a class="icon-mm icon-mm-cart" href="/cart"></a>
					</li>
				</ul>
				
				<div class="nav-group">
					<ul class="nav-group-list">
						<li>
							<a href="/" title="Главная">Главная</a>
						</li>
						<li>
							<span class="has-child" title="Продукция">Продукция</span>
							<?php if (isset($categories[0])):?>
							<ul class="nav-group-child">
							<?php foreach($categories[0] as $category):?>
								<li>
									<a class="<?=isset($categories[$category->id]) ? 'has-child' : '';?>" href="<?=htmlspecialchars($category->_url)?>" title="<?=htmlspecialchars($category->name)?>">
										<?=$category->name?>
									</a>
									<?php if (isset($categories[$category->id])):?>
									<ul class="nav-group-child">
									<?php foreach($categories[$category->id] as $child):?>
										<li>
											<a href="<?=htmlspecialchars($child->_url)?>" title="<?=htmlspecialchars($child->name)?>"><?=$child->name?></a>
										</li>
									<?php endforeach;?>
									</ul>
									<?php endif;?>
								</li>
							<?php endforeach;?>
							</ul>
							<?php endif;?>
						</li>
						<li>
							<a href="/about-us" title="О нас">О нас</a>
						</li>
						<li>
							<a href="/biznes-predlojenie/" title="Бизнес предложение">Бизнес предложение</a>
						</li>
						<li>
							<a href="/oplata-i-dostavka/" title="Оплата и доставка">Оплата и доставка</a>
						</li>
						<li>
							<a href="/reviews" title="Отзывы">Отзывы</a>
						</li>
						<li>
							<a href="/contacts/" title="Контакты">Контакты</a>
						</li>
						<li>
							<a href="javascript:void(0)" data-search="novaposhta" title="Отседить заказ НОВАЯ ПОЧТА">Трек КОД</a>
						</li>
						<li>
							<span class="has-child" title="Продукция">Подбор по цвету</span>
							<?php if (isset($categories[0])):?>
							<ul class="nav-group-child">
							<?php foreach($filter_items_color as $k):?>
								<li>
									<a href="<?=htmlspecialchars($k->_url)?>" title="<?=htmlspecialchars($k->name)?>">
										<?=$k->name?>
									</a>
								</li>
							<?php endforeach;?>
							</ul>
							<?php endif;?>
						</li>
					</ul>
					<script>
					$('.nav-group-list .has-child').click(function(e){
						e.preventDefault();
						$(this).toggleClass('activ');
					});
					</script>
				</div>
			</div>

		</header><!-- END HEADER-->