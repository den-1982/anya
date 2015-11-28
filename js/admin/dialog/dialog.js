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
			resizable:false,
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
											'<div class="dialog-content"></div>'+
										'</div>'+
										'<div class="dialog-buttons"></div>'+
										'<a class="dialog-close"></a>'+
									'</div>'+
								'</div>');
				
				// resizable
				if (settings.resizable)
				dialogBox.find('.dialog').addClass('resizable');
				
				// video
				if (settings.video)
					dialogBox.find('.dialog').addClass('dialog-video');
				
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
				settings.create.call(_this);
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
				// callback
				$(this).trigger('dialogClose');
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