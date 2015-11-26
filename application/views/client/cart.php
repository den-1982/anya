<div class="body">
	
	<h1 class="h1-style"><span><?=$h1?></span></h1>
	
	<?php if ($cart['cnt_items']):?>
	<div data-cart-body="middle">
		<div class="row">
			<div class="col-1">
				<form method="post" action="" data-bind="cart">
					
					<?=$cart['html']?>
					
					<input type="hidden" name="update_cart" value="">
				</form>
			</div>
		</div>

		<div class="row">
			<h2 class="h1-style"><span>Оформление заказа</span></h2>
			<div class="col-3">
				<div class="box-callback">
					<form action="" method="post" data-bind="checkout">

						<div class="form-group">
							<label>Ваша накопительная карта:</label>
							<input class="form-control" type="text" data-bind="user_cart_discount" name="user_cart_discount" value="<?=isset($user->user_cart_discount) ? $user->user_cart_discount : '';?>" placeholder="">
						</div>
						
						<!--
						<div class="form-group">
							<label>Подарочный сертификат:</label>
							<input class="form-control" type="text" name="certificate_code" value="" placeholder="">
						</div>
						-->
						
						<div class="form-group">
							<label>Способ оплаты:</label>
							<select data-styler="" name="payment">
								<option value="0" selected>Наличными</option>
								<option value="1">Наложным платежом</option>
								<option value="2">Оплата на карточку Приват Банка</option>
							</select>
						</div>
						
						<div class="form-group">
							<label>Способ доставки:</label>
							<select data-styler="" name="delivery">
								<option value="0" selected>Самовывоз</option>
								<option value="1">Новая Почта</option>
							</select>
						</div>
						
						<!-- НОВА ПОШТА -->
						<div class="form-group" data-novaposhta="map-mobil" style="display:none;"></div>
						
						<div class="form-group" data-novaposhta="city" style="display:none;">
							<label>Город:</label>
							<select data-styler="" data-placeholder="" data-search="true" data-bind="getWarenListNovaPoshta" name="city">
								<option value="0" selected></option>
								<?php foreach ($cityNP['response'] as $k):?>
								<option value="<?=$k->city_id?>" <?=isset($user->city) && $user->city == $k->city_id ? ' selected' : '';?>><?=$k->cityRu?></option>
								<?php endforeach;?>
							</select>
						</div>
						
						<div class="form-group" data-novaposhta="office" style="display:none;">
							<label>№ отделения:</label>
							<select data-styler="" data-placeholder="" data-bind="getOfficeNovaPoshta" name="office">
								<option value="0" selected></option>
								<?php foreach ($warenListNP['response'] as $k):?>
								<option value="<?=$k->wareId?>" <?=isset($user->wareId) && $user->wareId == $k->wareId ? ' selected' : '';?>><?=$k->addressRu?></option>
								<?php endforeach;?>
							</select>
						</div>
						
							
						<div class="form-group">
							<label><b class="c-red">*</b> Ваше ФИО:</label>
							<input class="form-control" type="text" name="name" value="<?=isset($user->name) ? $user->name : '';?>" placeholder="">
							<div class="form-error"></div>
						</div>
						
						<div class="form-group">
							<label><b class="c-red">*</b> E-mail:</label>
							<input class="form-control" type="text" name="mail" value="<?=isset($user->email) ? $user->email : '';?>" placeholder="">
							<div class="form-error"></div>
						</div>
						
						<div class="form-group">
							<label><b class="c-red">*</b> Моб.телефон:</label>
							<input class="form-control" data-mask="phone" type="text" name="phone" value="<?=isset($user->phone) ? $user->phone : '';?>" placeholder="">
							<div class="form-error"></div>
						</div>
						
						<div class="form-group">
							<label>Комментарий к заказу:</label>
							<textarea class="form-control-textarea" name="message"></textarea>
							<div class="form-error"></div>
						</div>
						
						<?php if ( ! $user): //добавить USER в БД?>
						<div class="form-group">
							<label>
								<input type="checkbox" name="adduser" value="1">
								Зарегистрируйтесь для участия в программе лояльности.
							</label>
						</div>
						<?php endif;/////////////////////////////////////?>
						
						<div class="form-group">
							<input type="hidden" name="checkout" value="">
							<button class="btn btn-pink" name="checkout" value="">Заказать</button>
						</div>
					</form>
				</div>
			</div>
			
			<div class="col-3x2">
				<div data-novaposhta="map" style="display:none;">
					<div id="mapBox"></div>
				</div>
			</div>
			
		</div>
	</div>
	
	<?php else:?>
	
	<div style="padding:10px;">
		<?php if ($this->session->userdata('check')):?>
		<div style="padding:40px;text-align:center;font-size:22px;"><?=$this->session->userdata('check')?></div>
		<?php $this->session->unset_userdata('check');?>
		<?php endif;?>
		
		<div class="cart-empty">
			<div class="cart-empty-text">Ваша корзина пуста.</div>
			<a class="btn btn-white" href="/">&larr; продолжить покупки</a>
		</div>

	</div>
	
	<?php endif;?>

</div><!-- END BODY -->



<script> // FORM
$(function(){
	var A = {
		change:function(){
			if($(this).find('option:selected').val() == 1){
				$('#lastname, #city, #warehouse').show(200)
			}else{
				$('#lastname, #city, #warehouse').hide(200)
			}
		},
		submit:function(){
			var flag,
				name = $('input[name=name]'),
				phone = $('input[name=phone]'),
				mail = $('input[name=mail]');
			
			$('.form-error').html('');
			
			if ( ! $.trim(name.val())){
				name.siblings('.form-error').html('поле необходимо заполнить');
				flag = true;
			}

			if ( ! $.trim(mail.val())){
				mail.siblings('.form-error').html('поле необходимо заполнить');
				flag = true;
			}
			
			if ( ! $.trim(phone.val())){
				phone.siblings('.form-error').html('поле необходимо заполнить');
				flag = true;
			}
			
			if (flag) return false;
			
			/* очищаем историю localStorage
			--------------------------------------------------------- */
			$(window).unbind('unload');
			localStorage.removeItem('form');
		},
		
		init:function(user){
			$(document).on('submit','[data-bind="checkout"]', A.submit);
			$(document).on('change','select[name=delivery]', A.change);

			
			/* localStorage
			--------------------------------------------------------------------------------- */
			// сохраняем данные
			$(window).bind('unload', function(){
				if ( !user && window.localStorage)
					localStorage['form'] = $.toJSON($('form[data-bind="checkout"]').eq(0).serializeArray());
			});
			
			// проверить USER, если нет тогда использовать localStorage
			if ( !user && window.localStorage && localStorage['form']){
				var q = $.parseJSON(localStorage['form']);
				
				if ( ! q){
					localStorage.removeItem('form'); 
					return;
				}
				
				var form = $('form[data-bind="checkout"]').eq(0),
					el;
				$(q).each(function(){
					// TEXTAREA | SELECT | INPUT (TYPE: text\checkbox\radio)
					el = form.find('[name='+this.name+']').eq(0);
					
					if (el[0].tagName == 'TEXTAREA'){
						el.val(this.value);
					}
					
					if (el[0].tagName == 'INPUT'){
						if (el[0].type == 'checkbox' || el[0].type == 'radio'){
							// пока ненадо
							//el.val(this.value);
						}else{
							el.val(this.value);
						}
					}
					
					if (el[0].tagName == 'SELECT'){
						el.find('option[value='+this.value+']').prop('selected', true).trigger('change');
					}
				});
			}
			
			/* end localStorage
			---------------------------------------------------------------------------------*/
		}
	}
	
	A.init(<?=(bool)$user?>);
});
</script>

<script> // PLUS \ MINUS
$(document).on('click', '[data-quantity-button="minus"], [data-quantity-button="plus"], [data-quantity="box"]', function(e){
	e.preventDefault();
	$(this).parents('[data-cart-item]').find('[data-btn="update"]').show(500);
});
</script>

<script> // VALID DISCOUNT CART
$('[data-bind="user_cart_discount"]').mask('999999',{ 
	placeholder:"*",
	completed: function(){
		$.post('', {check_cart_discount:this.val()}, function(data){
			console.log(data);
			if (data) window.location.reload(true);
		}, 'json');
	}
});
</script>
	
<script> // NOVA POSHTA
$(function(){
	var d = $(document),
		w = $(window),
		box = $('#mapBox'),
		blocks = $('[data-novaposhta]'),
		mapBoxDesktop = blocks.filter('[data-novaposhta="map"]'),
		mapBoxMobil = blocks.filter('[data-novaposhta="map-mobil"]'),
		cityName = '',
		listNP,	// все отделения
		flag = false, // выбрана NP или нет
		map,
		Placemark,
		ukraine = [49.1782,31.5398],
		_aj = function(params, callback){
			$.ajax({
				url: "/novaposhta",
				type: "POST",
				data: params,
				dataType: "json",
				error:function(data){alert('Ошибка')},
				success:callback
			});
		};

	// CHANGE НОВАЯ ПОШТА
	d.on('change.np', 'select[name=delivery]', function(e){
		// если не выбрали НОВАЯ ПОШТА
		if ($(this).prop('value') != 1){
			blocks.hide(200);
			flag = false;
			return;
		}else{
			flag = true;
		}
		
		// Проверка: подходящая ширина браузера?
		blocks.show(200);
		w.trigger('resize');

		// загрузить API если его нет
		if ( ! map) YaApiMap(_createMap);
	});
					
	// CHANGE CITY
	d.on('change.np', 'select[data-bind="getWarenListNovaPoshta"]', function(e){
		var cityId = $(this).val();
		cityName = $(this).find(':selected').html();
		
		// карта - показать город
		if (map) ymaps.geocode('Украина,'+cityName).then(function(data){
			map.setCenter(data.geoObjects.get(0).geometry.getCoordinates(), (cityName?11:6), {checkZoomRange:true,duration:2000});
		});
		
		// формируем (офисы данного горада)
		var select = $('select[data-bind="getOfficeNovaPoshta"]').html($('<option value="0"></option>'));
		if (listNP.response[cityId]){
			$.each(listNP.response[cityId], function(){
				select.append($('<option>',{value:this.wareId}).data('coordinates',[this.y, this.x]).html(this.addressRu));
			});
		}

		select.trigger('refresh');
	});
							
	// CHANGE OFFICE
	d.on('change.np', 'select[data-bind="getOfficeNovaPoshta"]', function(e){
		var c = $(this).find(':selected').data('coordinates');
		
		if ( ! map) return;
		
		if (c && c[0] && c[1]){
			map.setCenter(c, 16, {checkZoomRange:false,duration:1000});
		}else{
			ymaps.geocode('Украина,'+cityName).then(function(data){
				map.setCenter(data.geoObjects.get(0).geometry.getCoordinates(), (cityName?11:6), {checkZoomRange:true,duration:2000});
			});
		}
	});
	
	// CLICK MAP OFFICE
	d.on('click.np', '[data-bind="change-office"]', function(e){
		e.preventDefault();

		var _this = $(this),
			officeId = _this.attr('data-office-id') || 0,
			cityId= _this.attr('data-city-id') || 0;
			
			cityName = _this.attr('data-city-name') || '';
		
		var select = $('select[data-bind="getOfficeNovaPoshta"]').html($('<option>',{value:0}));
		$.each(listNP.response[cityId], function(i){
			var option = $('<option>',{value:this.wareId})
				.data('coordinates',[this.y, this.x])
				.html(this.addressRu)
				.prop('selected', (this.wareId == officeId ? true : false));
			
			select.append(option);
		});
		
		// выбираем офис
		select.trigger('refresh');
		
		// выбираем город
		$('select[data-bind="getWarenListNovaPoshta"]').find('option[value="'+cityId+'"]').prop('selected', true).parent().trigger('refresh');
		
		// закрыть БАЛУН
		map.balloon.close();
	});
	
	// RESIZE
	w.on('resize.np', function (){
		if ( ! flag) return;

		// matchMedia - узнать @media screen
		if (window.matchMedia){
			if (window.matchMedia('(max-width: 767px)').matches){
				mapBoxDesktop.css({display:'none'});
				box.appendTo(mapBoxMobil.css({display:'block'}));
			}else{
				box.appendTo(mapBoxDesktop.css({display:'block'}));
				mapBoxMobil.css({display:'none'});
			}
		// другой способ (not matchMedia. IE-9 ...)
		}else{
			if (w.width() < 751){
				mapBoxDesktop.css({display:'none'});
				box.appendTo(mapBoxMobil.css({display:'block'}));
			}else{
				box.appendTo(mapBoxDesktop.css({display:'block'}));
				mapBoxMobil.css({display:'none'});
			}
		}
		
	});
	
	// CREATE MAP
	function _createMap(){
		ymaps.ready(function(){
			map = new ymaps.Map(box[0], {
				center:ukraine,
				zoom:6,
				controls: ['typeSelector', 'zoomControl']
			});
			
			// запрещаем ZOOM
			map.behaviors.disable('scrollZoom');
			
			// убираем copyrights
			$('[class $= "copyrights-pane"]').remove();
			
			// кластер
			map.clusterer = new ymaps.Clusterer({clusterDisableClickZoom: false, preset:'twirl#blueClusterIcons'});
			
			// все отделения
			listNP ? _create(listNP) : _aj({getAllWarenList:''}, _create);
			
			function _create(data){
				listNP = data;
				
				$.each(data['response'], function(i){
					$.each(this, function(i){
						var data = this;

						if (!data.y || !data.x) return;
						
						var str = '<div class="maps-baloon-infobox">'+
									'<p class="city"><span>'+data.cityRu+'</span></p>'+
									'<p class="number"><span>Отделение № '+data.number+'</span></p>'+
									'<p class="title">'+data.addressRu+'</span></p>'+
									'<p class="phone"><span><b>Клиентская поддержка:</b> +38 '+data.phone+'</span></p>'+
									'<p class="restriction '+(data.max_weight_allowed >= 30?'':'unlimited')+'">'+
										'<b>&bull;&nbsp;</b>'+
										'<span>'+(data.max_weight_allowed >= 30 ? 'до 30 кг' : 'без ограничений')+'</span>'+
									'</p>'+
									'<p class="right">'+
										'<a href="javascript:void(0)" data-city-name="'+data.cityRu+'" data-city-id="'+data.city_id+'" data-office-id="'+data.wareId+'" class="btn btn-pink" data-bind="change-office">выбрать</a>'+
									'</p>'+
								'</div>';
						
						var Placemark = new ymaps.GeoObject({
							geometry: {
								type: "Point", 
								coordinates:[data.y, data.x]
							},
							properties: {
								balloonContentBody: str,
								iconContent: data.number,
								hintContent: "Отделение № " + data.number
							}
						},
						{
							//preset: (data.max_weight_allowed === '30' ? 'islands#redIcon' : 'islands#greenIcon')
							preset: (data.max_weight_allowed === '30' ? 'islands#redStretchyIcon' : 'islands#greenStretchyIcon')
						});

						Placemark.events.add('click', function (e) {
							//map.setCenter(Placemark.geometry.getCoordinates(),16,{checkZoomRange:true,duration:1000});
							map.setCenter(Placemark.geometry.getCoordinates(), 16);
						});
						
						map.clusterer.add(Placemark);
					});
				});
				
				map.geoObjects.add(map.clusterer);
			}
		});
	}
});
</script>








