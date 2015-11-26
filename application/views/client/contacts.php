<div class="body">
	<h1 class="h1-style"><span><?=$h1?></span></h1>
	
	<?php if ($settings->address):?>
	<div class="row">
		<div class="col-1" data-yandex-map="box" style="height:300px;"></div>
	</div>
	<script>
	$(function(){
		var a = document.createElement('script'); 
		a.type = 'text/javascript'; 
		a.async = true;     
		a.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'api-maps.yandex.ru/2.1/?lang=ru_RU';  
		var s = document.getElementsByTagName('script')[0]; 
		s.parentNode.insertBefore(a, s);
		
		a.onload = function(){
			ymaps.ready(function(){
				ymaps.geocode('Украина, <?=$settings->address?>').then(function(data){
					map = new ymaps.Map($('[data-yandex-map="box"]')[0], {
						center:data.geoObjects.get(0).geometry.getCoordinates(),
						zoom:17,
						controls: ['typeSelector', 'zoomControl']
					});
					// запретить ZOOM (мышкой)
					map.behaviors.disable('scrollZoom'); 
					
					$('[class $= "copyrights-pane"]').remove();
				
					var Placemark = new ymaps.GeoObject(
						{
							geometry: {
								type: "Point", 
								coordinates:data.geoObjects.get(0).geometry.getCoordinates()
							},
							properties: {
								//balloonContentBody: 'dsads',
								iconContent: 'CRYSTALLINE.IN.UA',
								hintContent: data.geoObjects.get(0).properties.get('name')
							}
						},
						{
							preset: 'islands#pinkStretchyIcon'
						}
					);
					Placemark.events.add('click', function (e) {
						map.setCenter(Placemark.geometry.getCoordinates(),17,{checkZoomRange:true,duration:1000});
					});
					
					map.geoObjects.add(Placemark);
				});
			});
		};
	});
	</script>
	<?php endif;?>
	

	<style>
	.manager{
		height:300px;
		padding:0 0 20px;
		margin:0 0 20px 0;
		overflow:hidden;
	}
	.manager-image{
		float:left;
		min-height:1px;
		width:40%;
		margin-right:15px;
	}
	.manager-desc{
		overflow:hidden;
	}
	.manager-desc > div{
		padding:0 0 5px;
		font-size:20px;
	}
	.manager-desc > div.manager-name{
		font-size:34px;
	}
	</style>
	<?php if ($settings->managers):?>
	<div class="row">
		<h2 class="center">Менеджеры:</h2>
		<?php foreach ($settings->managers as $manager):?>
		<div class="col-3">
			<div class="manager">
				<div class="manager-image">
					<img src="<?=htmlspecialchars($manager->image)?>" alt="">
				</div>
				<div class="manager-desc">
					<div class="manager-name"><?=$manager->name?></div>
					<div><b><?=$manager->position?></b></div>
					<div>Тел.: <a href="tel:<?=preg_replace('/[^0-9+]/', '', $manager->phone)?>"><?=$manager->phone?></a></div>
					<div>Email: <a href="mailto:<?=htmlspecialchars($manager->email)?>"><?=$manager->email?></a></div>
					<div>Skype: <a href="skype:<?=htmlspecialchars($manager->skype)?>"><?=$manager->skype?></a></div>
				</div>
			</div>
		</div>
		<?php endforeach;?>
	</div>
	<?php endif;?>
	
	<br><br>
	<div class="row">
		<div class="col-2">
			<style>
			.information{
				font-family: Open Sans Condensed, sans-serif, arial;
				font-size:20px;
			}
			</style>
			<table class="table information">
				<tr>
					<td class="small nowrap left bold">Название:</td>
					<td>Интернет - магазин "Crystalline"</td>
				</tr>
				
				<?php if ($settings->manager):?>
				<tr>
					<td class="small nowrap left bold">Контактное лицо:</td>
					<td><?=$settings->manager?></td>
				</tr>
				<?php endif;?>
				
				<?php if ($settings->email):?>
				<tr>
					<td class="small nowrap left bold">Email:</td>
					<td><a href="mailto:<?=htmlspecialchars($settings->email)?>"><?=$settings->email?></a></td>
				</tr>
				<?php endif;?>
				
				<?php if ($settings->skype):?>
				<tr>
					<td class="small nowrap left bold">Skype:</td>
					<td><a href="skype:<?=htmlspecialchars($settings->skype)?>"><?=$settings->skype?></a></td>
				</tr>
				<?php endif;?>
				
				<tr>
					<td class="small nowrap left bold">Сайт:</td>
					<td>crystalline.in.ua</td>
				</tr>
				
				<?php if ($settings->address):?>
				<tr>
					<td class="small nowrap left bold">Адрес:</td>
					<td><?=$settings->address?></td>
				</tr>
				<?php endif;?>
				
				<tr>
					<td class="small nowrap left top bold">Телефон:</td>
					<td>
					<?php foreach ($settings->phone as $phone):?>
						<a href="tel:+<?=preg_replace('/[^0-9]/', '', $phone)?>"><?=$phone?></a>
						<br>
					<?php endforeach;?>
					</td>
				</tr>
			</table>
		</div>
		<div class="col-2">
			<h2 style="margin-top:0;">Обратная связь:</h2>
			<form action="" method="post" data-bind="callback">
				<div class="form-group">
					<label><b class="c-red">*</b> Ваше ФИО:</label>
					<input class="form-control<?=isset($error['name'])?' error':'';?>" type="text" name="name" value="<?=isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '';?>" placeholder="">
					<div class="form-error"><?=isset($error['name']) ? $error['name'] : '';?></div>
				</div>
				
				<div class="form-group">
					<label><b class="c-red">*</b> E-mail:</label>
					<input class="form-control<?=isset($error['email'])?' error':'';?>" type="text" name="email" value="<?=isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '';?>" placeholder="">
					<div class="form-error"><?=isset($error['email']) ? $error['email'] : '';?></div>
				</div>
				
				
				<div class="form-group">
					<label><b class="c-red">*</b> Моб.телефон:</label>
					<input class="form-control<?=isset($error['phone'])?' error':'';?>" data-mask="phone" type="text" name="phone" value="<?=isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : '';?>" placeholder="">
					<div class="form-error"><?=isset($error['phone']) ? $error['phone'] : '';?></div>
				</div>
				
				
				<div class="form-group">
					<label>Сообщение:</label>
					<textarea class="form-control-textarea" name="message"></textarea>
					<div class="form-error"></div>
				</div>
				
				<div class="form-group">
					<input type="hidden" name="callback" value="">
					<button class="btn btn-pink" type="submit" name="callback" value="">Отправить сообщение</button>
				</div>

			</form>
		</div>
	</div>

</div><!-- END COONTAINER -->

<script> // CALLBACK
$(document).on('submit','[data-bind="callback"]',function(e){
	e.preventDefault();
	var load = $('<div>').addClass('load').css({position:'static',backgroundColor:'transparent'}).height($(this).height()); 
		
	$(this).replaceWith(load);

	$.post('', $(this).serialize(), function(data){
		load.replaceWith('<h4 style="padding:50px;text-align:center;">Сообщение отправлено.</h4>');
	}, 'json');
});
</script>



