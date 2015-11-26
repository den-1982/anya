// запретить enter для inputs
// запретить enter для inputs
$(document).on('keypress', 'input[type="text"], input[type="password"]', function(e){
	if(e.keyCode == 13) return false;
});

/* MASK
-------------------------------------------*/
;$(function(){
	$.mask.definitions['$']='[0-9,.]';
	$.mask.definitions['~']='[1-9]';
	$('[data-mask="price"]').mask("$?$$$$$$$$$$$",{ placeholder:"" });
	$('[data-mask="phone"]').mask("(999) 999-99-99",{ placeholder:"_" });
});


/* SELECT (styler)
-------------------------------------------*/
$(function(){
	setTimeout(function(){
		$('[data-styler=""]').styler({
			//singleSelectzIndex:1,
			onFormStyled:function(){
				$('[data-styler=""]').trigger('refresh');
			}
		});
	}, 100);
});

/* OWL CARUSEL
-------------------------------------------*/
$(function(){
	$('[data-owlCarusel]').each(function(){
		var autoPlay = false;
		if ($(this).attr('data-owlCarusel')) autoPlay = true;
		$(this).owlCarousel({
			lazyLoad : true,
			autoPlay : autoPlay,
			stopOnHover : true
		});
	});
});


/* TOP MENU (SCROLL)
-------------------------------------------*/
$(function(){
	var box = $('[data-menu="fix"]'),
		size = box.offset().top;

	$(document).scroll(function(){
		size < $(this).scrollTop() ? box.addClass('fix') : box.removeClass('fix')
	}).trigger('scroll');
});


/* DROP-DOWN (MENU)
-------------------------------------------*/
$(document).on('mouseenter', '[data-dropdown] > li', function(){
	clearTimeout($(this).data('time'));
	
}).on('mouseleave', '[data-dropdown] > li', function(){
	var _this = $(this);
	_this.data('time', setTimeout(function(){_this.removeClass('activ')}, 30));
	
}).on('click', '[data-dropdown] > li', function(e){
	var _this = $(this),
		w = _this.width(),
		parentLeft = e.currentTarget.offsetLeft;

	_this.find('.nib').css({left:parentLeft+(w/2)+'px'});
	_this.addClass('activ');
});
// $(function(){
	// $('.menu a').bind('transitionend webkitTransitionEnd oTransitionEnd MSTransitionEnd', function(){
		// $('[data-dropdown] > li').trigger('click');
	// });
// });


/* CART
-------------------------------------------*/
$(function(){
	var w = $(window),
		d = $(document),
		b = $(document.body),
		minWidth = 630,
		CART_BODY = $('[data-cart-body="middle"]'),
		CART_BOTTOM = $('[data-cart-body="bottom"]'),
		COUNT_ITEMS = $('[data-cart="count-items"]'),
		CART_TOTAL = $('[data-cart="cart-total"]'),
		box = $('[data-cart="bottom"]'),
		toggle = $('[data-cart="toggle"]'),
		helper = $('<div>').height(box.height()).insertBefore(box);
	
	/* CART-TOGGLE(bottom) & SCROLL FIX */
	d.scroll(function(){	
		if (box.next().offset().top < (d.scrollTop() + $(window).height())){
			box.removeClass('fix');
			helper.css({display:'none'});
		}else{
			box.addClass('fix');
			helper.css({display:'block'});
		}
	}).trigger('scroll');
	
	/* TOGGLE (show cart-bottom) */
	d.on('click.cart-bottom', '[data-cart="toggle"]', _toggle);
	function _toggle(e){
		if ( $(window).width() < minWidth) return;
		e.preventDefault();
		box.toggleClass('activ');
		helper.height(box.height());
		
		d.trigger('scroll');
	}
	
	/* RESIZE */
	w.resize(function(){
		d.unbind('.cart-bottom');
		
		if ($(this).width() > minWidth){ 
			d.on('click.cart-bottom', '[data-cart="toggle"]', _toggle);
		}else{
			if (box.hasClass('activ')){
				box.removeClass('activ');
				helper.height(box.height());
			}
		}
	});
	
	/* ADD CART */
	d.on('submit','[data-bind="add-cart"]', function(e){
		e.preventDefault();
		
		var load = $('<div>').addClass('load');
		b.append(load);
				
		$.ajax({
			url: "/cart/add",
			type: "POST",
			data: $(this).serialize(),
			dataType: "json",
			error:function(data){
				window.location.reload(false);
			},
			success:function(data){
				load.remove();
				
				/* Новай html корзины + делаем новую карусель */
				CART_BOTTOM.html(data.html_bottom).find('[data-cart-bt="owlCarusel"]').owlCarousel({singleItem:true});
				
				/* подложка (чтоб не прыгала) */
				helper.height(box.height());
				
				COUNT_ITEMS.html(data.cnt_items);
				CART_TOTAL.html(data.cart_total);
				
				/* открывваем нижнюю корзину */
				if ( ! box.hasClass('activ')){
					if ( $(window).width() < minWidth) return;
					toggle.trigger('click');
				}
			}
		});
	});
	
	/* UPDATE CART */
	$(document).on('submit', '[data-bind="cart"]', function(e){
		e.preventDefault();
		
		var load = $('<div>').addClass('load');
		b.append(load);
		
		$.post('', $(this).serialize()+'&update_cart_ajax=', function(data){
			load.remove();
			CART_BOTTOM.html(data.html_bottom).find('[data-cart-bt="owlCarusel"]').owlCarousel({singleItem:true});
			
			/* подложка (чтоб не прыгала) */
			helper.height(box.height());
			
			COUNT_ITEMS.html(data.cnt_items);
			CART_TOTAL.html(data.cart_total);
			
			/* если 0 шт. убрать форму Оформление заказа */
			data.cnt_items ? $('[data-cart="items"]').replaceWith(data.html) : CART_BODY.html(data.html);		
		}, 'json');
	});

	/* DELETE CART */
	d.on('click', '[data-cart-delete]', function(e){
		e.preventDefault();
		
		var load = $('<div>').addClass('load');
		b.append(load);
		
		$.post('/cart', {del: $(this).attr('data-cart-delete')}, function(data){
			load.remove();
			/* Новай html корзины bottom + делаем новую карусель*/
			CART_BOTTOM.html(data.html_bottom).find('[data-cart-bt="owlCarusel"]').owlCarousel({singleItem:true});
			
			/* подложка (чтоб не прыгала) */
			helper.height(box.height());

			COUNT_ITEMS.html(data.cnt_items);
			CART_TOTAL.html(data.cart_total);
			
			/* если 0 шт. убрать форму Оформление заказа */
			data.cnt_items ? $('[data-cart="items"]').replaceWith(data.html) : CART_BODY.html(data.html);
		}, 'json');
	});
	
	/* BUTTON QUANT (количество) */
	d.on('click','[data-quantity-button]', function(e){
		e.preventDefault();
		
		var cnt, 
			q = $(this).siblings('[data-quantity]');
		
		if ( $(this).attr('data-quantity-button') == 'plus'){
			cnt = parseInt( q.val().replace(/[^0-9]/g, '') ) + 1;
		}else{
			cnt = parseInt( q.val().replace(/[^0-9]/g, '') ) - 1;
		}
		q.val(isNaN(cnt) ? 1 : (cnt <= 0 ? 1 : cnt));
		
	}).on('blur','[data-quantity]', function(e){
		var cnt = parseInt( $(this).val().replace(/[^0-9]/g, '') );
		$(this).val(isNaN(cnt) ? 1 : (cnt <= 0 ? 1 : cnt));
	});
});


/* DELETE HISTORY (view)
-------------------------------------------*/
$(document).on('click', '[data-history="clear"]', function(e){
	e.preventDefault();
	var l = $('<div>').addClass('load'),
		_this = $(this);
	$(document.body).append(l);
	$.post('',{'remove-viewed':''},function(data){
		l.remove();
		_this.parents('[data-history="box"]').hide(500,function(){
			$(this).remove();
		});
	});
});


/* USER
-------------------------------------------*/
$(function(){
	var D;
	var User = {
		auth:function(e){
			if (e) e.preventDefault();

			var html = $('<form method="post" action="/user/login" data-bind="submit-login">'+
							'<div class="form-group">'+
								'<label>моб.Телефон:</label>'+
								'<input class="form-control" data-mask="phone-1" type="text" value="" name="phone" placeholder="">'+
								'<div class="form-error"></div>'+
							'</div>'+
							'<div class="form-group">'+
								'<label>Пароль:</label>'+
								'<input class="form-control" type="password" value="" name="password">'+
								'<div class="form-error"></div>'+
							'</div>'+
							'<div class="form-group">'+
								'<table style="width:100%;">'+
									'<tbody>'+
										'<tr>'+
											'<td>'+
												'<a href="/user/recover" data-user="recover" class="form-link">Забыли пароль?</a> '+
												'&nbsp;&nbsp;&nbsp;'+
												'<a href="/user/add" class="form-link">Регистрация</a> '+
											'</td>'+
											'<td class="right">'+
												'<input type="hidden" name="auth" value="">'+
												'<button name="auth" type="submit" class="btn btn-pink">Войти</button>'+
											'</td>'+
										'</tr>'+
									'</tbody>'+
								'</table>'+
							'</div>'+
						'</form>');
			
			D = $(html).dialog({
				width:'450px',
				height:'auto',
				title:'Вход в личный кабинет'
			});

			D.data('dialog').dialog.find('[data-mask="phone-1"]').mask("(999) 999-99-99",{ placeholder:"_" }).trigger('focus');
			D.data('dialog').dialog.find('[data-bind="submit-login"]').on('submit', function(e){
				e.preventDefault();
				D.dialog('load');
				$.post('/user/login', $(this).serialize(), function(data){
					if (data == 1) window.location = '/user';
					D.dialog('endLoad');
				});
				
				return false;
			});
		},
		
		recover:function(e){
			e.preventDefault();
			
			if ( ! D){
				D = $('<div class=""></div>').dialog({
					width:'450px',
					height:'auto',
					title:'Восстановление пароля'
				});
			}
			
			var html = $('<form method="post" action="/user/recover" data-form="recover">'+
							'<div class="form-group">'+
								'<label>Введите Ваш мобильный телефон:</label>'+
								'<input class="form-control" data-mask="phone-2" type="text" placeholder="" name="phone" value="">'+
								'<div class="form-error"></div>'+
							'</div>'+
							'<div class="form-pole" style="text-align:right;">'+
								'<input type="hidden" name="recover" value="">'+
								'<button name="recover" type="submit" class="btn btn-pink">Восстановить</button>'+
							'</div>'+
						'</form>');
			
			D.dialog('content', html).dialog('title', 'Восстановление пароля');
			
			D.data('dialog').dialog.find('[data-mask="phone-2"]').mask("(999) 999-99-99",{ placeholder:"_" }).trigger('focus');
			D.data('dialog').dialog.find('[data-form="recover"]').bind('submit.user', function(e){
				D.dialog('load');
				$.post('/user/recover', $(this).serialize(), function(data){
					D.dialog('endLoad');
					if ( ! data.error){
						D.dialog('content', data.text).dialog('position');
					}
				}, 'json');
				return false;
			});
		},
		
		_getHash:function(){
			return $.trim(window.location.hash.replace(/#/g,''));
		},
		
		init:function(){
			// HASH
			if (User._getHash() == 'auth') User.auth();
			if (User._getHash() == 'recover') User.recover();
			
			$(document).on('click', '[data-user="auth"]', User.auth);
			$(document).on('click', '[data-user="recover"]', User.recover);
		}
	}
	
	User.init();
});


/* GET DISCOUNT
-------------------------------------------*/
;$(document).on('click', '[data-bind="get-discounts"]', function(e){
	e.preventDefault();
	
	var D = $('<div class=""></div>').dialog({
			width:'600px',
			maxHeight:'600px',
			title:'Условия программы лояльности'
		}).dialog('load');
	
	$.post('/biznes-predlojenie', {getdiscounts:''},function(data){
		data = $(data).length == 1 ? $(data) : $('<div>').html(data);
		D.dialog('content', data).dialog('endLoad');
	}, 'json');
});


/* YANDEX API MAP
-------------------------------------------*/
function YaApiMap(callback){
	if (window.ymaps){
		callback();
		return;
	}
	
	var a = document.createElement('script'); 
		a.type = 'text/javascript'; 
		a.async = true;     
		a.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'api-maps.yandex.ru/2.1/?lang=ru_RU';  
		var s = document.getElementsByTagName('script')[0]; 
		s.parentNode.insertBefore(a, s);
	
	a.onload = callback;
}

/* GET NOVA-POSHTA(button)
-------------------------------------------*/
$(document).on('click', '[data-search="novaposhta"]', function(e){
	e.preventDefault();
	var html = $('<form data-novaposhta="form" action="/novaposhta" method="post">'+
					'<div class="form-group">'+
						'<label>Номер накладной:</label>'+
						'<input class="form-control" type="text" name="getNovaPoshta" value="">'+
					'</div>'+
					'<div class="form-group">'+
						'<button class="btn btn-white" type="submit">отследить</button>'+
					'</div>'+
					'<div class="box-result" data-novaposhta="result">'+
				'</form>');
	
	var D = html.dialog({
		width:'370px',
		height:'auto',
		title:'Отследить заказ НОВА ПОШТА:',
		drag:true
	});
});


/* GET NOVA-POSHTA
-------------------------------------------*/
$(document).on('submit', '[data-novaposhta="form"]', function(e){
	e.preventDefault();
	var l = $('<div>').addClass('load');
	$(document.body).append(l);
	$.ajax({
		url: "/novaposhta",
		type: "POST",
		data: $(this).serialize(),
		dataType: "json",
		error:function(data){
			l.remove();
			alert('Ошибка');
		},
		success:function(data){
			l.remove();
			data = $(data);
			data.find('a').each(function(){
				$(this).attr('href', 'http://novaposhta.ua'+$(this).attr('href')).attr('target','_blank');
			});
			$('[data-novaposhta="result"]').html(data);
		}
	});
});

/* NUMBER FORMAT (helper)
-------------------------------------------*/
function number_format(number, decimals, dec_point, thousands_sep){
	var i, j, kw, kd, km;

	// input sanitation & defaults
	if ( isNaN(decimals = Math.abs(decimals)) ) decimals = 2;
	if ( dec_point == undefined ) dec_point = ",";
	if ( thousands_sep == undefined ) thousands_sep = ".";
	
	i = parseInt(number = (+number || 0).toFixed(decimals)) + "";

	if ( (j = i.length) > 3 ){
		j = j % 3;
	}else{
		j = 0;
	}

	km = (j ? i.substr(0, j) + thousands_sep : "");
	kw = i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands_sep);
	// kd = (decimals ? dec_point + Math.abs(number - i).toFixed(decimals).slice(2) : "");
	kd = (decimals ? dec_point + Math.abs(number - i).toFixed(decimals).replace(/-/, 0).slice(2) : "");

	return km + kw + kd;
}


/* DIALOG
-------------------------------------------*/
;(function($){
	
	var w = $(window),
		d = $(document),
		index = 1,
		defaults = {
			video:false,
			content:'',
			title:'',
			width:'95%',
			height:'95%',
			maxWidth:'95%',
			maxHeight:'95%',
			drag:false,
			create:function(){}
		};
	
	var methods = {
		init:function(options){

			var settings;
			
			// если пустой объект
			return this.length ? this.each(_create) : _create.apply($('<div>'));

			function _create(){
				settings = $.extend({}, defaults, settings, options);

				var _this = $(this),
					data = _this.data('dialog'),
					html = this.outerHTML;

				// INIT есле уже есть
				if (data) return this;

				var dialogBox = $('<div data-dialog-box="'+index+'">'+
									'<div class="dialog-front-layer" style="z-index:'+index+'"></div>'+
									'<div class="dialog" style="z-index:'+index+'">'+
										'<div class="dialog-title-box">'+
											'<div class="dialog-title">Test</div>'+
										'</div>'+
										'<div class="dialog-content-box">'+
											'<div class="dialog-content '+(settings.video ? ' dialog-video' : '')+'"></div>'+
										'</div>'+
										'<div class="dialog-buttons"></div>'+
										'<a class="dialog-close"></a>'+
									'</div>'+
								'</div>');
							
				_this.data('dialog',{
					dialog:dialogBox,
					index:index++
				});
				
				// SIZE (параметры размера)
				dialogBox.find('.dialog').css({
					width:settings.width,
					height:settings.height,
					maxWidth:settings.maxWidth,
					maxHeight:settings.maxHeight
				});
				
				// CONTENT
				methods.content.call(_this, html);
				
				// TITLE
				methods.title.call(_this, settings.title);
				
				// APPEND
				$(document.body).append(dialogBox);
				methods.position.apply(_this);

				// CLOSE
				dialogBox.find('.dialog-close, .dialog-front-layer').click(function(){
					methods.close.apply(_this);
				});
				
				// DRAG
				methods.drag.call(_this, settings.drag);
				
				// CALLBACKS
				settings.create.call(_this.data('dialog'));
				// d.trigger('create.dialog');
				
				return this;
			};
		},
		
		content:function(html){
			this.data('dialog').dialog.find('.dialog-content').html(html);
			return this;
		},
		
		title:function(html){
			this.data('dialog').dialog.find('.dialog-title').html(html);
			return this;
		},
		
		position:function(){
			var wh  = w.height(),
				ww  = w.width(),
				scr = d.scrollTop(),
				dialog = this.data('dialog').dialog.find('.dialog'),
				bh  = dialog.height(),
				bw  = dialog.width();

			dialog.css({
				top:(wh-bh)/2+scr+'px',
				left:(ww-bw)/2+'px'
			}).animate({opacity:1}, 200);
			
			return this;
		},
		
		drag:function(){
			var startX,
				startY,
				box = this.data('dialog').dialog.find('.dialog'),
				t = box.find('.dialog-title-box');
			
			if (arguments[0] != true){
				w.unbind('.dialog.drag');
				t.unbind('.dialog.drag');
				return this;
			}
			
			// если ставить на document - все окна двигаются одновремен.
			t.on('mousedown.dialog.drag touchstart.dialog.drag', function(e){
				startX = e.originalEvent.pageX - box.offset().left;
				startY = e.originalEvent.pageY - box.offset().top;
						
				w.on('mousemove.dialog.drag touchmove.dialog.drag', function(e){	
					box.offset({
						left: e.originalEvent.pageX - startX,
						top: e.originalEvent.pageY - startY
					});
				});
			}).on('mouseup.dialog.drag touchend.dialog.drag',function(){
				w.unbind('mousemove.dialog.drag touchmove.dialog.drag');
			});

			return this;
		},

		close:function(){
			return this.each(function(){
				var _this = $(this),
					data = _this.data('dialog');
					
				data.dialog.remove();
				_this.removeData('dialog');
			});
		},
		
		load:function(){
			this.data('dialog').dialog.find('.dialog').addClass('dialog-load');
			return this;
		},
		
		endLoad:function(){
			this.data('dialog').dialog.find('.dialog').removeClass('dialog-load');
			return this;
		}
	};
	
	$.fn.dialog = function(method){
		if (methods[method]){
			return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
		} else if (typeof method === 'object' || !method){
			return methods.init.apply(this, arguments);
		}else{
			$.error('Метод ' +  method + ' не существует в jQuery.dialog');
		}
	}

})(jQuery);
