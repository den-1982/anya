/*
*	ANYA
*/

/* IMAGE ONERROR
---------------------------------------------------------------------------*/
$(function(){
	$('img').error(function(){
		//this.src = '/img/i_admin/loading_mini.gif';
	});
});

/* MASK
---------------------------------------------------------------------------*/
;$(function(){
	$.mask.definitions['$']='[0-9,.]';
	$.mask.definitions['~']='[1-9]';
	$.mask.definitions['@']='[0-9,a-z,_,-]';
	$('[data-mask="price"]').mask("$?$$$$$$$$$$$",{ placeholder:"" });
	$('[data-mask="code"]').mask("99999",{ placeholder:"_" });
	//$('[data-mask="phone"]').mask("+3 8(0~9) 999-99-99",{ placeholder:"_" });
	$('[data-mask="_phone"]').mask("+3 8(999) 999-99-99",{ placeholder:"_" });
	$('[data-mask="not-space"]').mask("@?@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@",{ placeholder:" " });
	 
});

/* DATEPICKER
---------------------------------------------------------------------------*/
;$(function(){
	$('[data-datepicker]').datepicker({
		dateFormat: 'yy-mm-dd',
		showOn:"both"
	});
});

/* SELECT-UI
---------------------------------------------------------------------------*/
;$(function(){
	$('[data-select]')
	.each(function(){
		$(this).prop('value', $(this).find('option[selected]:last').val());
	})
	.selectmenu({
		width:'auto',
		change:function(){
			if ($(this).attr('data-select') == 'auto-submit'){
				this.form.submit()
			}else{}
		}
	});
});

/* SORTABLE-UI
---------------------------------------------------------------------------*/
;$(function(){
	$('[data-sortable="body"]').sortable({
		items:'> *:not(.not-sortable)',
		sort:function(e, ui) {
			ui.helper.find('td').addClass('pale-grey');
		},
		handle:'[data-sortable="handler"]',
		activate:function(e, ui){
			ui.placeholder.eq(0).css({height:ui.helper.eq(0).height() + 'px'});
			ui.placeholder.eq(0).find('td').each(function(i){
				ui.helper.eq(0).find('td').eq(i).css({width: $(this).outerWidth() + 'px'})
			});
		}
	}).bind('sortupdate', function(e, ui){
		var _this = $(this);
		_this.find('[data-sortable="order"]').each(function(i){
			this.value = i;
		});
		_this.parents('table').find('[data-sortable="send-order"]').addClass('activ');
	});
	
	// BUTTON ORDER
	$('[data-sortable="send-order"]').click(function(e){
		e.preventDefault();
		var _this = $(this);
		
		if ( ! _this.hasClass('activ')) return;
		
		var order = _this.parents('table').find('[data-sortable="order"], [data-sortable="id"]');
		$(document.body).append($('<div>').addClass('load'));

		$.post('', order.serialize(), function(data){
			location.reload(false);
		});
	});
});

/* DELETE
---------------------------------------------------------------------------*/
$(document).on('click', '[data-delete]', function(e){
	e.preventDefault();
	
	var _this = $(this),
		confirm = $('<div class="fm-confirm">'+
						'Удалить '+ _this.attr('data-delete') +'?'+
						'<br>'+
						'<div class="fm-buttons-box">'+
							'<button class="fm-buttons white" data-confirm="cancel">Отмена</button>'+
							'<button class="fm-buttons red" data-confirm="agree">Удалить</button>'+
						'</div>'+
					'</div>');

	confirm.dialog({
		width:'300px',
		height:'auto',
		title:'Удаление',
		drag:true
	});
	
	confirm.data('dialog').dialog.find('[data-confirm="cancel"]').click(function(e){
		confirm.dialog('close');
	});
	
	confirm.data('dialog').dialog.find('[data-confirm="agree"]').click(function(e){
		confirm.dialog('close');
		
		// POST
		if (_this.attr('data-post')){
			$.post(_this.attr('href'), {delete:_this.attr('data-post')}, function(data){
				if ( ! data.error) 
					return location.reload(true);
				
				// Внимание
				return $(data.error).dialog({
					width:'300px',
					height:'auto',
					title:'Внимание',
					drag:true
				});
				
			}, 'json');
			
			return;
		}
		
		// GET
		location.href = _this.attr('href');
	});
});


/* TOGGLE BOOKMARK
---------------------------------------------------------------------------*/
;$(function(){
	var d = $(document);
	
	d.on('click.bookmark', '.bookmark-toggle', function(e){
		var _this = $(this);
		
		_this.siblings('.bookmark-toggle').removeClass('activ');
		_this.addClass('activ');
		
		var bookmark = $('[data-id="'+_this.attr('data-name')+'"]');
		bookmark.siblings('.activ').removeClass('activ');
		bookmark.addClass('activ');
	});
	
	if (location.hash){
		var hash = location.hash.replace('#', '');
		$('[data-name="'+hash+'"]').trigger('click.bookmark');
	}
});

/* TOGGLE VISIBILITY / HIT / NEW
---------------------------------------------------------------------------*/
$(document).on('click', '[data-bind="toggle"]', function(e){
	var 
	_this = $(this),
	l = $('<div class="load"></div>');
	
	$(document.body).append(l);
	
	$.post('', {
		toggle: _this.attr('data-column'), 
		id: _this.attr('data-id'), 
		activ: (+_this.hasClass('activ'))
	}, function(data){
		l.remove();
		
		_this.toggleClass('activ');
		
		// меняем значение (дублирование) INPUT ???
		_this.siblings('.visibility').val(((+_this.hasClass('activ')) == 1 ? 0 : 1));
	}, 'json');
});

/* SCROLL THEAD (работает только для одного экземпляра!!)
---------------------------------------------------------------------------*/
;$(function(){
	var 
	w = $(window),
	d = $(document),
	el = $('[data-scroll="head"] > thead').eq(0),
	elOffset,
	clon,
	clon2, // подложка (чтоб не схлоаывалась таблица при отсуствии tr - при поиске)
	S = {
		create:function(e){
			clon2.find('td').removeAttr('style');
			
			el.find('td').each(function(i){
				var _width = $(this).outerWidth()+'px';

				clon.find('td').eq(i).css({width:_width});
				clon2.find('td').eq(i).css({width:_width});
			});
			
			clon.css({
				position: 'fixed',
				top: 0,
				marginLeft: S.margin,
				opacity: 0.9,
				visibility: 'hidden',
				zIndex: 100
			});
		},
		scroll:function(e){
			if (elOffset < d.scrollTop()){
				clon.css({visibility:'visible'});
			}else{
				clon.css({visibility:'hidden'});
			}
		},
		init:function(){
			S.margin = (navigator.userAgent.search(/firefox/i) != -1 ? '-1px' : 0);

			if ( ! el.length) return;
			
			elOffset = el.offset().top;
			clon = el.clone(true).insertBefore(el);
			
			clon2 = $('<tr class="not-sortable"></tr>').append(
				el.clone().find('td').html('').removeAttr('class')
			).appendTo($('[data-scroll="head"]').find('tbody'));

			w.on('scroll', S.scroll)
			.on('resize', S.create)
			.trigger('resize');		
		}
	}
	
	w.load(S.init);
});
/*
;$(function(){
	var S = {
		scroll:function(e){
			if (S.offset < $(document).scrollTop()){
				S.clon.css({visibility:'visible'});
			}else{
				S.clon.css({visibility:'hidden'});
			}
		},
		resize:function(e){
			S.h.find('td').each(function(i){
				S.clon.find('td').eq(i).css({width:$(this).outerWidth() + 'px'});
			});
			S.clon.css({
				position:'fixed',
				top:0,
				marginLeft:S.browser,
				opacity:0.9,
				visibility:'hidden',
				zIndex:100
			});
		},
		init:function(){
			S.browser = (navigator.userAgent.search(/firefox/i) != -1 ? '-1px' : 0);
			//S.h = $('[data-scroll="head"]').find('thead').eq(0);
			S.h = $('[data-scroll="head"] > thead').eq(0);

			if ( ! S.h.length) return;

			S.clon = S.h.clone(true);
			S.offset = S.h.offset().top;
			
			S.resize();
			
			S.clon.insertBefore(S.h);
			
			$(window).scroll(S.scroll).resize(S.resize);		
		}
	}
	$(window).load(S.init);
});
*/

/* AP
---------------------------------------------------------------------------*/
var AP = {
	init:function(path, data, callback, dataType){
		if ( ! window.FormData) return;
		AP.form = new FormData();
		
		if (data.nodeName == "FORM"){
			data = data.elements;
			for (var i = 0; i < data.length; i++){
				// проверка checkbox
				if (data[i].type == "checkbox" && !data[i].checked) continue;
				
				// проверка на disabled
				if (data[i].disabled) continue;
				
				AP.form.append(data[i].name, (data[i].type == 'file' ? data[i].files[0] : data[i].value));
			}
		}else{
			for (var i in data)	AP.form.append(i, data[i]);
		}
		
		$.ajax({
			type: "POST",
			url: path,
			data: AP.form,
			dataType:dataType||'html',
			processData: false,
			contentType: false,
			success: callback,
			error:callback
		});
	}
}

/* READ
---------------------------------------------------------------------------*/
var Read = {
	init:function(file, callback){
		if ( ! file)return;
		if ( !window.File && !window.FileReader && !window.FileList && !window.Blob){
			callback(0);
		}
		var reader = new FileReader();
		reader.onload = function(e){
			var img = new Image();
			img.onload = function(){
				callback({src:e.target.result,width:img.width,height:img.height});
			}
			img.src = e.target.result;
		}
		reader.onerror = function(e){
			callback(0);
		}
		
		reader.readAsDataURL(file)
	}
}

/* YANDEX API MAP
---------------------------------------------------------------------------*/
function YaApiMap(callback){
	if (window.ymaps){
		ymaps.ready(callback);
		return;
	}
	
	var a = document.createElement('script'); 
	a.type = 'text/javascript'; 
	a.async = true;     
	a.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'api-maps.yandex.ru/2.1/?lang=ru_RU';  
	var s = document.getElementsByTagName('script')[0]; 
	s.parentNode.insertBefore(a, s);
	
	a.onload = function(){
		ymaps.ready(callback);
	};
}

/* FM
---------------------------------------------------------------------------*/
$(document).on('click.filemanager', '.FM-overview', function(e){
	e.preventDefault();
	$(this).FM();
}).on('click.filemanager', '.FM-clear', function(e){
	e.preventDefault();
	var _this = $(this);
	_this.parent().find('img').attr('src','/img/i/loading_mini.gif');
	_this.parent().find('input').val('');
}).on('dblclick.filemanager', '.mce-combobox input.mce-textbox', function(e){
	$(this).FM();
});

/* NUMBER_FORMAT (help)
---------------------------------------------------------------------------*/
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
	//kd = (decimals ? dec_point + Math.abs(number - i).toFixed(decimals).slice(2) : "");
	kd = (decimals ? dec_point + Math.abs(number - i).toFixed(decimals).replace(/-/, 0).slice(2) : "");


	return km + kw + kd;
}

/* UPLOAD
---------------------------------------------------------------------------*/
$(document).on('change', '[data-button-file="image"]', function(e){
	var _this = $(this).parents('[data-preload="box"]').find('[data-preload="image"]'); 
	
	Read.init(e.target.files[0], function(data){
		if ( ! data) return;
		_this.attr('src', data.src);
	});
	return;
});












