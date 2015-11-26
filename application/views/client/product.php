<div class="body">
	
	<h1 class="h1-style"><span><?=$h1?></span></h1>
	
	<!-- CRUMBS-->
	<ul class="crumbs">
		<li itemtype="http://data-vocabulary.org/Breadcrumb" itemscope="">
			<a itemprop="url" href="<?=SERVER?>">
				<span itemprop="title">Главная</span>
			</a>
		</li>
		<li> / </li>
		<?php $last = array_pop($crumbs);?>
		<?php foreach ($crumbs as $k):?>
		<li  itemtype="http://data-vocabulary.org/Breadcrumb" itemscope="">
			<a itemprop="url" href="<?=htmlspecialchars($k['_url'])?>">
				<span itemprop="title"><?=$k['name']?></span>
			</a>
		</li>
		<li> / </li>
		<?php endforeach;?>
		<li itemprop="url"><span itemprop="title"><?=$last['name']?></span></li>
	</ul>
	<!-- END CRUMBS-->
	

	<div class="row text">
		<div class="pp-block-image">
			<div class="pp-image">
				<a data-lightbox="roadtrip" href="<?=htmlspecialchars($product->image)?>" target="_blank" title="<?=htmlspecialchars($product->name)?>">
					<img src="<?=htmlspecialchars($product->image)?>" alt="<?=htmlspecialchars($product->name)?>">
				</a>
			</div>

			<?php if($product->images):?>
			<ul class="pp-images">
				<?php foreach($product->images as $k=>$v):?>
				<li>
					<a data-lightbox="roadtrip" class="images" href="<?=htmlspecialchars($v->url)?>" target="_blank" title="<?=htmlspecialchars($product->name)?>">
						<img src="<?=htmlspecialchars($v->mini)?>" alt="<?=htmlspecialchars($product->name)?>">
					</a>
				</li>
				<?php endforeach;?>
			</ul>
			<?php endif;?>
		</div>
		

		<div class="pp-block-price">
			<div class="pole">
				<h2><?=$product->name?></h2>
			</div>
			
		<!-- Если есть размеры -->
		<?php if (isset($product->prices) && $product->prices):?>
			<form action="/cart/add" method="post" data-bind="add-cart">
				<div class="pole">
					РАЗМЕР:&nbsp
					<div class="inline-block">
						<select data-styler="" data-product-size="" name="size">
							<option value="0" selected>Выбрать размер</option>
							<?php foreach($product->prices as $k):?>
							<option value="<?=$k->id_filter_item?>">
								<?=$k->name?>: <?=$k->prefix?> (<?=$k->cnt_opt?>/<?=$k->cnt_roz?>  шт.)
							</option>
							<?php endforeach;?>
						</select>
					</div>

					<a style="margin:0 0 0 10px;" class="btn btn-white" data-bind="get-size-map" data-parent="<?=$product->parent?>" href="javascript:void(0)">карта размеров</a>
					<!--<a style="margin:0 0 0 10px;" class="btn btn-white" data-bind="get-price" href="/getprice">прайс лист</a>-->
				</div>

				<div class="pole" data-attention="">
					<img src="/img/i/attention.gif" alt=""> для отображения цены, выберете размер
				</div>

				<div class="pole" data-under-order="" style="display:none;">
					<button class="btn btn-white" data-bind="under-order" data-id="<?=$product->id?>" data-name="<?=$product->name?>">заказать</button>
					или
					<a class="btn btn-white" data-waitlist="{id_product:<?=$product->id?>}">
						<span class="icons-envelope"></span>
						<span>Сообщить когда появится</span>
					</a>
				</div>

				<div class="pole" data-box-quatntity="" style="display:none;">
					<div class="pole">
						Цена: 
						<span class="pp-price" data-product-price=""></span>
					</div>

					<div class="pole">
						<div class="pp-product-packing">
							Упаковка:
							<br>
							<label data-type="opt"><input data-packing="opt" type="radio" name="type" value="1">
								опт. 
								<span data-packing-cnt="opt"></span>
							</label>
							<br>
							<label data-type="roz"><input data-packing="roz" type="radio" name="type" value="0" checked>
								розница 
								<span data-packing-cnt="roz"></span>
							</label>
						</div>
					</div>

					<div class="pole">
						Количество:
						<br>
						<div class="quant">
							<button class="quant-button minus" data-quantity-button="minus"><span>&minus;</span></button>
							<input  class="quant-box" data-quantity="box" type="text" name="quantity" value="1" autocomplete="off">
							<button class="quant-button plus" data-quantity-button="plus"><span>&plus;</span></button>
						</div>
					</div>
					
					<div class="pole">
						<input type="hidden" name="add-cart" value="">
						<input type="hidden" name="id" value="<?=$product->id?>">
						<button class="btn btn-pink" id="add" data-id="<?=$product->id?>" type="submit">в корзину</button>
					</div>
				</div>
			</form>
			
		<!-- ELSE IF -->
		<?php elseif ($product->price*1):?>
			<form action="/cart/add" method="post" data-bind="add-cart">
				<?php 
				$user_discount 		= isset($user->discount) ? $user->discount*1 : 0;
				$product_discount 	= $product->discount*1;
				$_discount = $product_discount > $user_discount ? $product_discount : $user_discount;
				
				if ($_discount):
				?>
				<div class="pole" data-discount-info="">
					<?php if ($product_discount > $user_discount):?>
					<div class="pp-discount">Скидка &minus;<?=$_discount?>%</div>
					<?php else:?>
					<div>Ваша персональная скидка <b class="c-red"> &minus;<?=$_discount?>%</b></div>
					<?php endif;?>
				</div>
				<?php endif;?>
				
				<div class="pole"  data-box-quatntity="">
					<div class="pole">
						Цена:
						<?php if ($_discount):?>
						<span class="pp-old-price"  data-product-oldprice="" id="old-price"><?=number_format($product->price, 2, ',', "'")?> грн.</span>
						<span class="pp-price"  data-product-price=""><?=number_format(round($product->price - ($product->price * $_discount / 100), 2), 2, ',', "'")?>  грн.</span>
						<?php else:?>
						<span class="pp-price" data-product-price=""><?=number_format($product->price, 2, ',', "'")?> грн.</span>
						<?php endif;?>
					</div>
					
					<div class="pole">
						Количество:
						<br>
						<div class="quant">
							<button class="quant-button minus" data-quantity-button="minus"><span>&minus;</span></button>
							<input  class="quant-box" data-quantity="box" type="text" name="quantity" value="1" autocomplete="off">
							<button class="quant-button plus" data-quantity-button="plus"><span>&plus;</span></button>
						</div>
					</div>
					
					<div class="pole">
							<input type="hidden" name="add-cart" value="">
							<input type="hidden" name="id" value="<?=$product->id?>">
						<button class="btn btn-pink" id="add" data-id="<?=$product->id?>"  type="submit" >в корзину</button>
					</div>
				</div>
			</form>
			
		<!--ELSE -->
		<?php else:?>
			
			<div class="pole">
				<span style="font-size:22px;">ПОД ЗАКАЗ</span>
			</div>
			
			<div class="pole" data-under-order="">
				<button data-bind="under-order" data-id="<?=$product->id?>" data-name="<?=$product->name?>" class="btn btn-white">заказать</button>
				или
				<a class="btn btn-white" data-waitlist="{id_product:<?=$product->id?>}">
					<span class="icons-envelope"></span>
					<span>Сообщить когда появится</span>
				</a>
			</div>
		<?php endif;?>
		<!-- END IF -->
			
			<!-- Если есть производитель -->
			<?php if($product->manufacturer):?>
			<div class="pole right">
				<div class="pp-manufacturer">
					<img style="max-height:40px;" src="/img/manufacturer/<?=$product->manufacturer->id?>/<?=$product->manufacturer->id?>.jpg" alt="производитель">
					<br>
					<?=$product->manufacturer->name?>
				</div>
			</div>
			<?php endif;?>
			<!-- END -->
			
		</div>
	</div><!-- END ROW -->
	
	<div class="row text">
		<div class="pp-block-description">
			<div class="pole">
				<h3>Описание товара:</h3>
				<div style="overflow:hidden;">
					<?=$product->text?>
				</div>
			</div>
		</div>
	</div>
	
	
	<!-- PRODUCTS RELATED-->
	<?php if ($product->related):?>
	<div class="row">
		<div class="col-1">
			<h3 class="attetion blue"><span>СОПУТСТВУЮЩИЕ ТОВАРЫ</span></h3>
			
			<div class="owl-carousel" data-owlCarusel="auto">
			<?php foreach ($product->related as $k=>$v):?>
				<div class="item">
					<a class="goods<?=$v->hit ? ' hit' : ($v->new ? ' new' : '') ;?>" href="<?=htmlspecialchars($v->_url)?>" title="<?=htmlspecialchars($v->name)?>">
						<span class="goods-image">
							<?php if ($v->discount*1):?>
							<i title="скидка на <?=htmlspecialchars($v->name)?> <?=$v->discount*1?> %"> &minus; <?=$v->discount*1?> %</i>
							<?php endif;?>
							<img class="lazyOwl" data-src="<?=htmlspecialchars($v->image)?>" alt="<?=htmlspecialchars($v->name)?>">
						</span>
						<span class="goods-name"><?=$v->name?></span>
					</a>
				</div>
			<?php endforeach;?>
			</div>
			
		</div>
	</div>
	<?php endif;?>
	
	
	<!-- VIEWED PRODUCT -->
	<?php if ($product_viewed):?>
	<div class="row" data-history="box">
		<div class="col-1">
			<h3 class="attetion purple"><span>ВЫ СМОТРЕЛИ</span><a class="attetion-link" data-history="clear" href="javascript:void(0)" title="очистить историю"></a></h3>
			
			<div class="owl-carousel" data-owlCarusel="auto">
			<?php foreach ($product_viewed as $k=>$v):?>
				<div class="item">
					<a class="goods<?=$v->hit ? ' hit' : ($v->new ? ' new' : '') ;?>" href="<?=htmlspecialchars($v->_url)?>" title="<?=htmlspecialchars($v->name)?>">
						<span class="goods-image">
							<?php if ($v->discount*1):?>
							<i title="скидка на <?=htmlspecialchars($v->name)?> <?=$v->discount*1?> %"> &minus; <?=$v->discount*1?> %</i>
							<?php endif;?>
							<img class="lazyOwl" data-src="<?=htmlspecialchars($v->image)?>" alt="<?=htmlspecialchars($v->name)?>">
						</span>
						<span class="goods-name"><?=$v->name?></span>
					</a>
				</div>
			<?php endforeach;?>
			</div>
			
		</div>
	</div>
	<?php endif;?>
	
	
	<!-- PRODUCT DISCOUNT -->
	<?php if ($products_discount):?>
	<div class="row">
		<div class="col-1">
			<h3 class="attetion"><span>АКЦИОННЫЕ ТОВАРЫ</span></h3>
			
			<div class="owl-carousel" data-owlCarusel="auto">
			<?php foreach ($products_discount as $k=>$v):?>
				<div class="item">
					<a class="goods<?=$v->hit ? ' hit' : ($v->new ? ' new' : '') ;?>" href="<?=htmlspecialchars($v->_url)?>" title="<?=htmlspecialchars($v->name)?>">
						<span class="goods-image">
							<i title="скидка на <?=htmlspecialchars($v->name)?> <?=$v->discount*1?> %"> &minus; <?=$v->discount*1?> %</i>
							<img class="lazyOwl" data-src="<?=htmlspecialchars($v->image)?>" alt="<?=htmlspecialchars($v->name)?>">
						</span>
						<span class="goods-name"><?=$v->name?></span>
					</a>
				</div>
			<?php endforeach;?>
			</div>
			
		</div>
	</div>
	<?php endif;?>
	
	
	<div class="row">
		<div class="col-1">
			<div class="spam"><?=$spam?></div>
		</div>
	</div>
	
</div><!-- END BODY -->


<script> // размер, опт\роз, количество, положит в корзину
$(function(){
	var A = {
		_size:function(e){
			e.preventDefault();
			
			// подсказка
			$('[data-attention]').css({display:'none'});
			// кнопка (под заказ)
			$('[data-under-order]').css({display:'none'});
			// удаляем старую цену на всяк случай
			$('[data-product-oldprice], [data-discount-info]').remove();
			// по умалчанию показуем все (потом скроются те, у которых нет цены)
			$('[data-type]').css({display:'inline-block'});
			// сбрасываем количество
			$('[data-quantity="box"]').val(1);
			
			
			// выбран. размер
			var size = +$(this).prop('value');
			
			if (size && A.sizes[size]){
				// если есть цена
				var x = A.sizes[size];
			
				// определить какая цена есть из двух(опт\роз)
				$('[data-packing-cnt="opt"]').html(x.cnt_opt);
				$('[data-packing-cnt="roz"]').html(x.cnt_roz);
				if (!+x.opt){
					$('[data-type=opt]').css({display:'none'});
					$('[data-packing=roz]').prop('checked',1);
				}
				if (!+x.roz){
					$('[data-type=roz]').css({display:'none'});
					$('[data-packing=opt]').prop('checked',1);
				}
				
				// определяем тип (опт\роз)
				var type = +$('[data-packing]:checked').val() == 0 ? 'roz' : 'opt',
					price = +x[type],
					product_discount = x.discount *1,
					_discount = product_discount > A.user_discount ? product_discount : A.user_discount; // наибольшая скидка

				if (price > 0){
					// data-product-price
					// DISCOUNT проверка, есть ли скидка на товар
					if (_discount > 0){
						// добавляем старую цену
						$('<span>').attr('data-product-oldprice', '').addClass('pp-old-price').html(number_format(price, 2, ',', "'") +' грн.').insertBefore('[data-product-price]');
							
						// новая цена
						$('[data-product-price]').html(number_format((price - (price * _discount / 100)), 2, ',', "'") +' грн.');
						
						// значок СКИДКА
						var __all = $('<div>').addClass('pp-discount').html('Скидка &minus;'+_discount+'%');
						var __user = $('<div>').addClass('pp-discount_').html('Ваша персональная скидка <b class="c-red"> &minus;'+_discount+'%</b>');
						$('<div>').addClass('pole').attr('data-discount-info', '').html(
							(product_discount > A.user_discount ? __all : __user)
						).insertBefore('[data-box-quatntity]');
						
					}else{
						$('[data-product-price]').html(number_format(price, 2, ',', "'") +' грн.');
					}
					
					$('[data-box-quatntity]').show(200);
					$('[data-under-order]').css({display:'none'});
				}else{
					// если цена 0 - (форма под заказ)
					$('[data-box-quatntity]').hide(200);
					$('[data-under-order]').css({display:'block'});
					// сообщить когда появится
					$('[data-waitlist]').attr('data-waitlist', '{id_product:'+A.id+',id_size:'+size+',id_color:0}');
				}	
			}else{
				$('[data-attention]').css({display:'block'});
				$('[data-box-quatntity]').hide(200);
			}
		},
		
		init:function(data, id_product, user_discount){
			A.user_discount = user_discount *1;
			A.id = id_product;
			A.sizes = {};
			try {
				$.each($.parseJSON(data), function(){
					A.sizes[this.id_filter_item] = this;
				});
			}catch(e){}

			// EVENTS
			$(document).on('change','[data-product-size]', A._size);
			$(document).on('change', '[data-packing]', function(){
				$('[data-product-size]').trigger('change');
			});
			
			// сброс значения SELECT
			$('[data-product-size]').prop('selectedIndex', 0); 

			// если один размер (выбираем его)
			if ($('[data-product-size] option').length == 2){
				$('[data-product-size]').prop('selectedIndex',1).trigger('change');
			}
			
			// если есть хеш (кнопка редактирование в корзине) #size=10;packing=2
			if (location.hash){
				var params = {},
					hash = window.location.hash.replace(/[#\s+]/g, '').split(';');

				for (var i in hash){	
					var q = hash[i].split('=');
					params[q[0]||''] = q[1]||0;
				}

				if (params['packing']) $('[data-packing][value="'+params['packing']+'"]').prop('checked', true);
				if (params['size']) $('[value="'+params['size']+'"]', '[data-product-size]').prop('selected', true).trigger('change');
			}
		}
	}

	A.init(
		'<?=json_encode(isset($product->prices) ? $product->prices : array())?>', 
		<?=$product->id?>,
		<?=json_encode(isset($user->discount) ? $user->discount * 1 : 0)?>
	);	
});
</script>

<script> // SIZE MAP окно с размерами (SS4, SS30)
$(document).on('click', '[data-bind="get-size-map"]', function(e){
	e.preventDefault();

	var D = $().dialog({
		width:'600px',
		height:'auto',
		title:'Карта размеров',
		drag:true
	}).dialog('load');
	
	$.post('',{getSizeMap:'', id_category: $(this).attr('data-parent')}, function(data){
		var html = '<table class="table nt-2">'+
						'<thead>'+
							'<tr>'+
								'<td class="center">Код размера</td>'+
								'<td class="center">Размер мм.</td>'+
								'<td class="center">Количество шт. в уп. (опт)</td>'+
								'<td class="center">Количество шт. в уп. (розница)</td>'+
							'</tr>'+
						'</thead>'+
						'<tbody>';
			$.each(data, function(){
					html += '<tr>'+
								'<td class="center">'+
									''+this.name+
								'</td>'+
								'<td class="center">'+
									''+this.prefix+
								'</td>'+
								'<td class="center">'+
									''+this.cnt_opt+
								'</td>'+
								'<td class="center">'+
									''+this.cnt_roz+
								'</td>'+
							'</tr>';
			});
				html += '</tbody>'+
					'</table>';
		
		if ( ! D.data('dialog')) return;
		D.dialog('content', $(html)).dialog('endLoad').dialog('position');
	}, 'json');
});
</script>

<script> // для кнопки "заказать" (если нет цены)
;$(function(){
	$(document).on('click.sendunderorder', '[data-bind="under-order"]', function(e){
		e.preventDefault();

		var id = $(this).attr('data-id'),
			name = $(this).attr('data-name'),
			html = $('<form data-bind="sendunderorder">'+
						'<div class="form-group">'+
							'<label><b class="c-red">*</b> Ваше имя:</label>'+
							'<input class="form-control" type="text" name="name" value="'+user.name+'" placeholder="">'+
							'<div class="form-error"></div>'+
						'</div>'+
						'<div class="form-group">'+
							'<label><b class="c-red">*</b> Моб.телефон:</label>'+
							'<input class="form-control" data-mask="phone" type="text" name="phone" value="'+user.phone+'" placeholder="">'+
							'<div class="form-error"></div>'+
						'</div>'+
						'<div class="form-group">'+
							'<label><b class="c-red">*</b> E-mail:</label>'+
							'<input class="form-control" type="text" name="email" value="'+user.email+'" placeholder="">'+
							'<div class="form-error"></div>'+
						'</div>'+
						'<div class="form-group">'+
							'<label>Сообщение:</label>'+
							'<textarea class="form-control-textarea" name="message"></textarea>'+
							'<div class="form-error"></div>'+
						'</div>'+
						'<div class="form-group">'+
							'<input type="hidden" name="id" value="'+id+'">'+
							'<input type="hidden" name="sendunderorder" value="">'+
							'<button class="btn btn-pink" type="submit" name="sendunderorder" value="">Отправить сообщение</button>'+
						'</div>'+
					'</form>');
		
		
		var D = html.dialog({
			width:'600px',
			height:'auto',
			title:'Товар под заказ "'+name+'"'
		});
		
		D.data('dialog').dialog.find('[data-mask="phone"]').mask("(999) 999-99-99",{ placeholder:"_" });
		D.data('dialog').dialog.find('[data-bind="sendunderorder"]').on('submit.sendunderorder', function(e){
			e.preventDefault();
			D.dialog('load');
			$.post('', $(this).serialize(), function(data){
				if ( ! D.data('dialog')) return;
				D.dialog('endLoad').dialog('content', '<h2 class="center">Ваш запрос принят</h2>').dialog('position');
			}, 'json');
		});
		
	});

	var user = $.parseJSON('<?=json_encode(array(
		'name'=> isset($user->name) ? $user->name : '',
		'email'=>isset($user->email) ? $user->email : '',
		'phone'=>isset($user->phone) ? $user->phone : ''
	))?>');
});
</script>

<script> // WAITLIST кнопка (сообщить когда появится)
;$(document).on('click', '[data-waitlist]', function(e){
	e.preventDefault();
	
	var data = $.evalJSON($(this).attr('data-waitlist')||'{}'),
		html = $('<form data-bind="towaitlist">'+
					'<div class="form-group">Введите свой email и нажмите кнопу, чтобы подписаться на уведомление о появлении товара в выбранном размере.</div>'+
					'<div class="form-group">'+
						'<label>Ваш email:</label>'+
						'<input class="form-control" type="text" name="email" value="" placeholder="">'+
					'</div>'+
					'<div class="form-group">'+
						'<input type="hidden" name="towaitlist" value="">'+
						'<input type="hidden" name="id_product" value="'+ (data.id_product||0) +'">'+
						'<input type="hidden" name="id_size" value="'+ (data.id_size||0) +'">'+
						'<input type="hidden" name="id_color" value="'+ (data.id_color||0) +'">'+
						'<button class="btn btn-pink" type="submit">Уведомить по E-mail</button>'+
					'</div>'+
				'</form>');
	
	var D = html.dialog({
		width:'400px',
		height:'auto',
		drag:true,
		title:'Сообщить когда появится товар'
	});
	
	D.data('dialog').dialog.find('[data-bind="towaitlist"]').submit(function(e){
		e.preventDefault();

		D.dialog('load');
		$.post('', $(this).serialize(), function(data){
			D.dialog('close');
		},'json');
	});
});
</script>













