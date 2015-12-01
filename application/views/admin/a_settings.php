<div class="body">

	<h1 class="title"><?=$h1?></h1>
	
	<div id="nav" class="clearfix">
		<div class="fleft"></div>
		<div class="fright">
			<a style="margin-left:5px;" class="button orange" data-form-apply="">Применить</a>
		</div>
	</div>
	
	<form id="form" action="<?=$path?>" method="POST" enctype="multipart/form-data">
		
		<div class="toggle-box">
			<a class="bookmark-toggle activ" data-name="manager" href="#manager">Менеджер</a>
			<a class="bookmark-toggle" data-name="discount" href="#discount">Скидки</a>
			<a class="bookmark-toggle" data-name="analitics" href="#analitics">Analitics</a>
			<a class="bookmark-toggle" data-name="admin" href="#admin">Админ</a>
			<!--<a class="bookmark-toggle" data-name="sizes" href="#sizes">Размеры изоб.</a>-->
			<!--<a class="bookmark-toggle" data-name="price-list" href="#price-list">Прайс-лист</a>-->
			<!--<a class="bookmark-toggle" data-name="export" href="#export">Экспорт / Импорт</a>-->
		</div>
		
		<div class="bookmark" data-id="sizes">
			<table class="table-1">
				<thead>
					<tr>
						<td class="small">№</td>
						<td>Размер (px)</td>
						<td class="small">
							<a class="link_add" data-sizeImage="add"></a>
						</td>
					</tr>
				</thead>
				<tbody data-sizeImage="items">
				<?php if (is_array($settings->sizes)):?>
				<?php $i=1; foreach($settings->sizes as $size):?>
					<tr data-sizeImage="item">
						<td><?=$i++;?></td>
						<td class="left">
							<input class="inf" type="text" data-mask="int" name="image_size[]" value="<?=$size?>">
						</td>
						<td>
							<a class="link_del" data-sizeImage="delete" title="Удалить размер"></a>
						</td>
					</tr>
				<?php endforeach;?>
				<?php endif;?>
				</tbody>
			</table>
		</div>

		<div class="bookmark activ" data-id="manager">
			<table class="table-1">
				<tbody>
					<tr>
						<td class="small right">
							<b>Менеджеры:</b>
						</td>
						<td>
							<table class="table-1">
								<thead>
									<tr>
										<td class="small"></td>
										<td class="small">Фото</td>
										<td>Имя</td>
										<td>Должность</td>
										<td>Телефон</td>
										<td>E-mail</td>
										<td>Skype</td>
										<td class="small">
											<a class="link_add" data-managers="add" title="добавить менеджера"></a>
										</td>
									</tr>
								</thead>
								<tbody data-sortable="body" data-managers="box">
								<?php foreach ($settings->managers as $manager):?>
									<tr data-managers="item">
										<td>
											<span class="icon-reorder handler" data-sortable="handler"></span>
											<input type="hidden" data-sortable="order" name="manager_order[update][<?=$manager->id?>]" value="<?=$manager->order?>">
										</td>
										<td class="nowrap">
											<div class="FM-image-box" data-box-image="filter-item" <?=!$manager->image ? 'style="display:none;"' : '';?>>
												<div class="i">
													<img class="FM-image" src="<?=htmlspecialchars($manager->image)?>">
												</div>
												<input type="hidden" name="manager_image[update][<?=$manager->id?>]" value="<?=htmlspecialchars($manager->image)?>">
												<br><a href="javascript:void(0)" class="FM-overview">обзор</a> | <a href="javascript:void(0)" class="FM-clear">очистить</a>
											</div>
											<?php if ( ! $manager->image):?>
											<a href="javascript:void(0)" data-bind="add-image-filter-item">+ добавить</a>
											<?php endif;?>
										</td>
										<td>
											<input class="inf" type="text" name="manager_name[update][<?=$manager->id?>]" value="<?=htmlspecialchars($manager->name)?>">
										</td>
										<td>
											<input class="inf" type="text" name="manager_position[update][<?=$manager->id?>]" value="<?=htmlspecialchars($manager->position)?>">
										</td>
										<td>
											<input class="inf" data-mask="phone" name="manager_phone[update][<?=$manager->id?>]" type="text" value="<?=htmlspecialchars($manager->phone)?>">
										</td>
										<td>
											<input class="inf" type="text" name="manager_email[update][<?=$manager->id?>]" value="<?=htmlspecialchars($manager->email)?>">
										</td>
										<td>
											<input class="inf" type="text" name="manager_skype[update][<?=$manager->id?>]" value="<?=htmlspecialchars($manager->skype)?>">
										</td>
										<td>
											<a class="link_del" data-managers="delete" title="удалить"></a>
										</td>
									</tr>
								<?php endforeach;?>
								</tbody>
							</table>
						</td>
					</tr>
					<tr>
						<td class="right">
							<b>Менеджер:</b>
						</td>
						<td>
							<input class="inf" type="text" name="manager" value="<?=htmlspecialchars($settings->manager)?>">
						</td>
					</tr>
					<tr>
						<td class="right">
							<b>Skype:</b>
						</td>
						<td>
							<input class="inf" type="text" name="skype" value="<?=htmlspecialchars($settings->skype)?>">
						</td>
					</tr>
					<tr>
						<td class="right">
							<b>E-mail:</b>
						</td>
						<td>
							<input class="inf" type="text" name="email" value="<?=htmlspecialchars($settings->email)?>">
						</td>
					</tr>
					
					<tr>
						<td class="right nowrap">
							<b>Соц. сети:</b>
						</td>
						<td>
							<table class="table-1">
								<thead>
									<tr>
										<td class="small"></td>
										<td class="small"><b>Соц.сеть</b></td>
										<td><b>URL</b></td>
										<td class="small">
											<a class="link_add" data-social="add" title="добавить социальную сеть"></a>
										</td>
									</tr>
								</thead>
								<tbody data-sortable="body" data-social="box">
								<?php if (is_array($settings->social)):?>
								<?php foreach ($settings->social as $_name=>$_link):?>
									<tr data-social="item">
										<td class="small">
											<span class="icon-reorder handler" data-sortable="handler"></span>
										</td>
										<td>
											<select data-select="" name="social_name[]">
												<option value="0" selected> - выбрать -</option>
												<?php foreach ($socials as $i):?>
												<option value="<?=$i->code?>" <?=($i->code == $_name) ? ' selected' : '';?>><?=$i->name?></option>
												<?php endforeach;?>
											</select>
										</td>
										<td>
											<input class="inf" type="text" name="social_link[]" value="<?=htmlspecialchars($_link)?>">
										</td>
										<td class="small">
											<a class="link_del" data-social="delete" title="удалить"></a>
										</td>
									</tr>
								<?php endforeach;?>
								<?php endif;?>
								</tbody>
							</table>
						</td>
					</tr>
					
					<tr>
						<td class="right">
							<b>Телефон:</b>
						</td>
						<td>
							<table class="table-1">
								<thead>
									<tr>
										<td class="small"></td>
										<td><b>Номер</b></td>
										<td class="small">
											<a class="link_add" data-phone="add" title="добавить телефон"></a>
										</td>
									</tr>
								</thead>
								<tbody data-sortable="body" data-phone="box">
								<?php if (is_array($settings->phone)):?>
								<?php foreach($settings->phone as $phone):?>
									<tr data-phone="item">
										<td class="small">
											<span class="icon-reorder handler" data-sortable="handler"></span>
										</td>
										<td>
											<input class="inf" data-mask="_phone" type="text" name="phone[]" value="<?=htmlspecialchars($phone)?>">
										</td>
										<td class="small">
											<a class="link_del" data-phone="delete" title="удалить телефон"></a>
										</td>
									</tr>
								<?php endforeach;?>
								<?php endif;?>
								</tbody>
							</table>
						</td>
					</tr>
					<tr>
						<td class="right">
							<b>Адрес:</b>
						</td>
						<td>
							<table class="table-1">
								<tr>
									<td class="small right"><b>Страна:</b></td>
									<td class="left">
										<input class="inf" type="text" name="country" value="<?=htmlspecialchars($settings->country)?>">
									</td>
								</tr>
								<tr>
									<td class="right"><b>Город:</b></td>
									<td class="left">
										<input class="inf" type="text" name="city" value="<?=htmlspecialchars($settings->city)?>">
									</td>
								</tr>
								<tr>
									<td class="right"><b>Ул./дом:</b></td>
									<td class="left">
										<input class="inf" type="text" name="address" value="<?=htmlspecialchars($settings->address)?>">
									</td>
								</tr>
								<tr>
									<td class="right"><b>Индекс:</b></td>
									<td class="left">
										<input class="inf" type="text" name="postal_code" value="<?=htmlspecialchars($settings->postal_code)?>">
									</td>
								</tr>
								<tr>
									<td class="right"><b>Карта:</b></td>
									<td class="left">
										<table class="table-1">
											<tr>
												<td class="small">
													<div style="height:100px;">
														<img id="ya-map-image" src="<?=htmlspecialchars($settings->map)?>" alt="">
													</div>
													<input class="inf" type="hidden" name="map" value="<?=htmlspecialchars($settings->map)?>">
													<input class="inf" type="hidden" name="coordinates" value="<?=htmlspecialchars($settings->coordinates)?>">
												</td>
												<td class="left">
													<a class="button blue" 
														data-bind="choose-map" 
														data-address="<?=htmlspecialchars($settings->country)?> <?=htmlspecialchars($settings->city)?> <?=htmlspecialchars($settings->address)?>">выбрать</a>
													<a class="button orange" data-bind="clean-map">очистить</a>
												</td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		
		<div class="bookmark" data-id="discount">
			<table class="table-1">
				<thead>
					<tr>
						<td class="small">№</td>
						<td>Название скидки</td>
						<td>Процент (%)</td>
						<td class="small">
							<a class="link_add" data-discount="add"></a>
						</td>
					</tr>
				</thead>
				<tbody data-sortable="body" data-discount="box" class="sortable discounts">
				<?php foreach ($settings->discounts as $discount):?>
					<tr data-discount="item">
						<td>
							<span class="icon-reorder handler" data-sortable="handler"></span>
							<input class="inf" data-sortable="order" type="hidden" name="discount_order[update][<?=$discount->id?>]" value="<?=$discount->order?>">
						</td>
						<td class="left">
							<input class="inf" type="text" name="discount_name[update][<?=$discount->id?>]" value="<?=htmlspecialchars($discount->name)?>">
						</td>
						<td class="small">
							<input class="inf" data-mask="price" type="text" name="discount_percent[update][<?=$discount->id?>]" value="<?=$discount->percent?>">
						</td>
						<td>
							<a class="link_del" data-discount="delete"></a>
						</td>
					</tr>
				<?php endforeach;?>
				</tbody>
			</table>
		</div>
		
		<div class="bookmark" data-id="analitics">
			<table class="table-1">
				<tbody>
					<tr>
						<td class="small nowrap right">
							<b>Google Analitics:</b>
						</td>
						<td>
							<textarea style="height:100px;" class="inf" name="analitics"><?=$settings->analitics?></textarea>
						</td>
					</tr>
					<tr>
						<td class="small nowrap right">
							<b>Yandex Metrica:</b>
						</td>
						<td>
							<textarea style="height:100px;" class="inf" name="metrica"><?=$settings->metrica?></textarea>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		
		<div class="bookmark" data-id="admin">
			<table class="table-1">
				<tbody>
					<tr>
						<td class="small nowrap right">
							<b>Логин:</b>
						</td>
						<td class="left">
							<input class="inf" type="text" data-admin="login" name="admin[login]" value="<?=$admin->login?>" disabled>
						</td>
						<td class="small">
							<a class="link_edit" data-bind="enabled"></a>
						</dt>
					</tr>
					<tr>
						<td class="small nowrap right">
							<b>Пароль:</b>
						</td>
						<td class="left">
							<input class="inf" type="text" data-admin="password" name="admin[password]" value="" disabled>
						</td>
						<td class="small">
							<a class="link_edit" data-bind="enabled"></a>
						</dt>
					</tr>
					<tr>
						<td class="small nowrap right">
							<b>E-mail:</b>
						</td>
						<td class="left">
							<input class="inf" type="text" data-admin="email" name="admin[email]" value="<?=$admin->email?>" disabled>
						</td>
						<td class="small">
							<a class="link_edit" data-bind="enabled"></a>
						</dt>
					</tr>
				</tbody>
			</table>
		</div>

		<input type="hidden" name="edit" value="">
	</form>
	
</div><!-- END BODY -->

<script> // APPLY
$(document).on('click', '[data-form-apply]', function(e){
	$(document.body).append($('<div>').addClass('load'));
	
	if (tinyMCE && tinyMCE.editors){
		$(tinyMCE.editors).each(function(){
			$(this.getElement()).html(this.getContent());
		});
	}
	
	AP.init('', $('#form')[0], function(){	
		location.reload(true);
	});
	return false;	
});
</script>

<script> // EDIT ADMIN
$(function(){
	$('input[data-admin]').prop('disabled', true);
	$('[data-bind="enabled"]').on('click', function(e){
		e.preventDefault();
		var _this = $(this).parents('tr').find('[data-admin]');
		_this.attr('disabled') ? _this.removeAttr('disabled') : _this.attr('disabled', 'disabled');
	});
});
</script>

<script> // MANAGERS
;$(function(){
	var M = {
		create:function(){
			var html = $('<tr data-managers="item" class="new-tr">'+
							'<td>'+
								'<span class="icon-reorder handler" data-sortable="handler"></span>'+
								'<input type="hidden" data-sortable="order" name="manager_order[insert][]" value="">'+
							'</td>'+
							'<td class="nowrap">'+
								'<div class="FM-image-box" data-box-image="filter-item" style="display:none;">'+
									'<div class="i">'+
										'<img class="FM-image" src="">'+
									'</div>'+
									'<input type="hidden" name="manager_image[insert][]" value="">'+
									'<br><a href="javascript:void(0)" class="FM-overview">обзор</a> | <a href="javascript:void(0)" class="FM-clear">очистить</a>'+
								'</div>'+
								'<a href="javascript:void(0)" data-bind="add-image-filter-item">+ добавить</a>'+
							'</td>'+
							'<td>'+
								'<input class="inf" type="text" name="manager_name[insert][]" value="">'+
							'</td>'+
							'<td>'+
								'<input class="inf" type="text" name="manager_position[insert][]" value="">'+
							'</td>'+
							'<td>'+
								'<input class="inf" data-mask="phone" name="manager_phone[insert][]" type="text" value="">'+
							'</td>'+
							'<td>'+
								'<input class="inf" type="text" name="manager_email[insert][]" value="">'+
							'</td>'+
							'<td>'+
								'<input class="inf" type="text" name="manager_skype[insert][]" value="">'+
							'</td>'+
							'<td>'+
								'<a class="link_del" data-managers="delete" title="удалить"></a>'+
							'</td>'+
						'</tr>');
						
			$('[data-managers="box"]').prepend(html).trigger('sortupdate');
			html.find('[data-mask="phone"]').mask("+3 8(0~9) 999-99-99",{ placeholder:"_" });
		},
		showBoxImage:function(e){
			e.preventDefault();
			$(this).parent().find('[data-box-image="filter-item"]').show(200);
			$(this).remove();
		},
		init:function(){
			$(document).on('click', '[data-managers="add"]', M.create)
			.on('click', '[data-bind="add-image-filter-item"]', M.showBoxImage)
			.on('click','[data-managers="delete"]',function(){
				$(this).parents('[data-managers="item"]').hide(200,function(){
					$(this).remove();
				});
			});
		}
	}
	M.init();
});
</script>

<script> // SOCIAL
;$(function(){
	var S = {
		create:function(){
			var html = 
			'<tr data-social="item" class="new-tr">'+
				'<td class="small">'+
					'<span class="icon-reorder handler" data-sortable="handler"></span>'+
				'</td>'+
				'<td class="right">'+
					'<select data-select="" name="social_name[]">'+
						'<option value=""> - Выбрать - </option>';
						$(S.socials).each(function(){
							html += 
							'<option value="'+this.code+'">'+this.name+'</option>';
						});
						html +=
						'</select>'+
					'</td>'+
				'<td>'+
					'<input class="inf" type="text" name="social_link[]" value="">'+
				'</td>'+
				'<td class="small">'+
					'<a class="link_del" data-social="delete" title="удалить телефон"></a>'+
				'</td>'+
			'</tr>';
			html = $(html);		
			
			$('[data-social="box"]').prepend(html);
			html.find('[data-select]').selectmenu({width:'auto'});
		},
		init:function(socials){
			try{
				S.socials = $.parseJSON(socials);
			}catch(e){}
			
			$(document).on('click','[data-social="add"]', S.create)
			.on('click','[data-social="delete"]',function(){
				$(this).parents('[data-social="item"]').hide(200,function(){
					$(this).remove();
				});
			});
		}
	}
	S.init('<?=isset($socials) ? json_encode($socials) : null?>');
});
</script>

<script> // PHONE
;$(function(){
	var F = {
		create:function(){
			var html = $(
			'<tr data-phone="item" class="new-tr">'+
				'<td class="small">'+
					'<span class="icon-reorder handler"  data-sortable="handler"></span>'+
				'</td>'+
				'<td>'+
					'<input class="inf" data-mask="_phone" type="text" name="phone[]" value="">'+
				'</td>'+
				'<td class="small">'+
					'<a class="link_del"  data-phone="delete" title="удалить телефон"></a>'+
				'</td>'+
			'</tr>');
						
			$('[data-phone="box"]').prepend(html);
			html.find('[data-mask="_phone"]').mask("+3 8(999) 999-99-99",{ placeholder:"_" });
		},
		init:function(){
			$(document).on('click', '[data-phone="add"]', F.create)
			.on('click','[data-phone="delete"]',function(){
				$(this).parents('[data-phone="item"]').hide(200,function(){
					$(this).remove();
				});
			});
		}
	}
	F.init();
});
</script>

<script> // DISCOUNT
;$(function(){
	var A = {
		create:function(){
			var html = $(
			'<tr data-discount="item" class="new-tr">'+
				'<td>'+
					'<span class="icon-reorder handler" data-sortable="handler"></span>'+
					'<input class="inf" data-sortable="order" type="hidden" name="discount_order[insert][]" value="">'+
				'</td>'+
				'<td class="left">'+
					'<input class="inf" type="text" name="discount_name[insert][]" value="">'+
				'</td>'+
				'<td class="small">'+
					'<input class="inf" data-mask="price" type="text" name="discount_percent[insert][]" value="">'+
				'</td>'+
				'<td>'+
					'<a class="link_del" data-discount="delete"></a>'+
				'</td>'+
			'</tr>');
		
			$('[data-discount="box"]').prepend(html);
			html.find('[data-mask="price"]').mask("$?$$$$$$$$$$$",{ placeholder:"" });
		},
		init:function(){
			$(document).on('click','[data-discount="add"]', A.create)
			.on('click','[data-discount="delete"]',function(){
				$(this).parents('[data-discount="item"]').hide(200,function(){
					$(this).remove();
				});
			});
		}
	}
	A.init();
});
</script>

<script> // SIZES IMAGE
;$(function(){
	var A = {
		create:function(){
			var html = $(
			'<tr data-sizeImage="item" class="new-tr">'+
				'<td></td>'+
				'<td class="left">'+
					'<input class="inf" type="text" data-mask="int" name="image_size[]" value="">'+
				'</td>'+
				'<td>'+
					'<a class="link_del" data-sizeImage="delete" title="Удалить размер"></a>'+
				'</td>'+
			'</tr>');
		
			$('[data-sizeImage="items"]').prepend(html);
			html.find('[data-mask="int"]').mask("9?999999999999",{ placeholder:""});
		},
		init:function(){
			$(document).on('click', '[data-sizeImage="add"]', A.create)
			.on('click', '[data-sizeImage="delete"]', function(){
				$(this).parents('[data-sizeImage="item"]').hide(200,function(){
					$(this).remove();
				});
			});
		}
	}
	A.init();
});
</script>

<script> // YAMAP
;$(function(){
	var image = $('#ya-map-image')
		inputMap = $('input[name="map"]'),
		inputCoordinates = $('input[name="coordinates"]');
	
	$('[data-bind="clean-map"]').on('click', function(e){
		e.preventDefault();
		image.attr('src', '');
		inputMap.val('');
		inputCoordinates.val('');
	});
	
	$('[data-bind="choose-map"]').on('click', function(e){
		e.preventDefault();
		
		var address = $(this).data('address') || 'Украина';
		
		var D = $('<div id="YaMapBox" style="height:100%;"></div>').dialog({
			title: 'Карта',
			type: 'map'
		});
		
		YaApiMap(function(){
			ymaps.geocode(address).then(function(data){
				var map = new ymaps.Map($('#YaMapBox')[0], {
					center:data.geoObjects.get(0).geometry.getCoordinates(),
					zoom:6,
					controls: ['typeSelector', 'zoomControl']
				});
				map.behaviors.disable('scrollZoom'); 
				$('[class $= "copyrights-pane"]').remove();
				
				// Создаем экземпляр класса ymaps.control.SearchControl
				var mySearchControl = new ymaps.control.SearchControl({
					options: {
						noPlacemark: true
					}
				})
				// Результаты поиска будем помещать в коллекцию.
				,mySearchResults = new ymaps.GeoObjectCollection(null, {
					hintContentLayout: ymaps.templateLayoutFactory.createClass('$[properties.name]')
				});

				map.controls.add(mySearchControl);
				map.geoObjects.add(mySearchResults);
				// При клике по найденному объекту метка становится красной.
				mySearchResults.events.add('click', function (e) {
					
					var coord = e.get('target').geometry.getCoordinates(),
						src = 'http://static-maps.yandex.ru/1.x/?ll='+coord[1]+','+coord[0]+'&size=600,250&z=16&l=map&pt='+coord[1]+','+coord[0]+',pm2rdl';
					
					inputCoordinates.val(coord);
					inputMap.val(src);
					image.attr('src', src);

					e.get('target').options.set('preset', 'islands#redIcon');
				});
				
				// Выбранный результат.
				mySearchControl.events.add('resultselect', function (e) {
					var index = e.get('index');
					mySearchControl.getResult(index).then(function (res) {
					   mySearchResults.add(res);
					});
				}).add('submit', function (e) {
					mySearchResults.removeAll();
				});
			});
		});
		
	});
});
</script>

