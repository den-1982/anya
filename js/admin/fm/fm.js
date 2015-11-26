;(function($){
	var D,
		html,
		target,
		URL = '/admin/filesmanager';
	
	var methods = {
		init:function(){
			
			target = typeof this == 'object' ? $(this) : null;
			
			if ( ! $.fn.dialog) return;

			D = $().dialog({
				width:'800px',
				height:'400px',
				title:'Менеджер файлов',
				drag:true,
				resizable:true 
			}).dialog('load').one('dialogClose', methods.exit);
			
			methods.create();
		},
		
		create:function(){
			html = $('<div class="fm-box">'+
							'<div class="fm-box-head">'+
								'<a class="fm-nav fm-icons fm-icons-add-folder" data-bind="add-folder" title="Создать папку"></a>'+
								'<a class="fm-nav fm-icons fm-icons-del-folder" data-bind="del-folder" title="Удалить папку"></a>'+
								'<a class="fm-nav fm-icons fm-icons-add-image" title="Загрузить изображения">'+
									'<input class="fm-input-files" data-bind="add-image" type="file" value="" multiple>'+
								'</a>'+
								'<a class="fm-nav fm-icons fm-icons-delete-image" data-bind="del-image" title="Удалить изображение"></a>'+
							'</div>'+
							'<div class="fm-box-content">'+
								'<div class="fm-folders-box"></div>'+
								'<div class="fm-images-box"></div>'+
							'</div>'+
						'</div>');
			
			D.dialog('content', html);

			/* EVENTS *********************************************************/
			html.on('click.fm', '[data-bind="add-folder"]', methods.addFolder)
			.on('click.fm', '[data-bind="del-folder"]', methods.delFolder)
			.on('change.fm', '[data-bind="add-image"]', methods.addImage)
			.on('click.fm', '[data-bind="del-image"]', methods.delImage);

			// клик по узлу дерева
			html.on('click.fm', '.fm-folders-box .has-child > span', function(){
				$(this).parent().toggleClass('open');
			});
			
			// клик по папке
			html.on('click.fm', '.fm-folder', methods.getImages);
			
			// клик по изображению
			html.on('click.fm', '.fm-images-box a', function(e){
				var _this = $(this).toggleClass('selected');
				html.find('.fm-images-box a').not(_this).removeAttr('class');
			}).on('dblclick.fm', '.fm-images-box a', function(e){
				if ( ! target) return;
				var _this = $(this);
				
				// TINYMCE
				if (target[0].nodeName == 'INPUT'){
					target.val(_this.data('file')).focus();
				}else{
					target.parent().find('img').attr('src', _this.data('cache'));
					target.parent().find('input').val(_this.data('file'));
				}
				
				D.dialog('close');
			});
			
			// CREATE дерево каталога
			methods.getTree(function(data){
				html.find('.fm-folders-box').html(methods.createTree([data]));
				D.dialog('endLoad');
			});
		},
		
		getTree:function(callback){
			$.post(URL, {action:'getTree'}, callback, 'json');
		},
		
		createTree:function(data, ul){
			ul = $('<ul>');
			for (var i=0; i < data.length; i++){
				var li = $('<li '+ (data[i].child.length ? 'class="has-child"' : '') +'>'+
							'<span></span>'+
							'<a class="fm-folder">'+ data[i].name +'</a>'+
						'</li>');
				
				li.find('a').data('path', data[i].path).data('name', data[i].name);
				ul.append(li);
				
				/* recursion */
				if (data[i].child.length) li.append(methods.createTree(data[i].child, ul));
			}
			
			return ul;
		},
		
		addFolder:function(){
			var selected = html.find('.fm-folder.selected').eq(0);

			if ( ! selected.length) return;
			
			var prompt = $('<div class="fm-prompt">'+
								'Введите название папки:'+
								'<div class="">'+
									'<input class="fm-input-text" type="text" data-prompt="input" value="">'+
								'</div>'+
								'<div class="fm-buttons-box">'+
									'<button class="fm-buttons white" data-prompt="add">Добавить</button>'+
								'</div>'+
							'</div>');

			prompt.dialog({
				width:'300px',
				height:'auto',
				title:'Добавить папку',
				drag:true
			});
			
			prompt.data('dialog').dialog.find('[data-prompt="add"]').click(function(e){
				var nameFolder = prompt.data('dialog').dialog.find('[data-prompt="input"]').val();
				nameFolder = $.trim(nameFolder);
				prompt.dialog('close');
				
				if ( ! nameFolder) return;
				
				D.dialog('load');
				$.post(URL, {action:'addDir', name:nameFolder, path:selected.data('path')}, function(data){
					methods.getTree(function(data){
						html.find('.fm-folders-box').html(methods.createTree([data]));
						D.dialog('endLoad');
					});
				}, 'json');
			});
		},
		
		delFolder:function(){
			var selected = html.find('.fm-folder.selected').eq(0);

			if ( ! selected.length) return;
			
			var confirm = $('<div class="fm-confirm">'+
								'Удалить папку <b>"'+selected.data('name')+'"</b> и все содержимое?'+
								'<br>'+
								'<div class="fm-buttons-box">'+
									'<button class="fm-buttons white" data-confirm="cancel">Отмена</button>'+
									'<button class="fm-buttons red" data-confirm="agree">Удалить</button>'+
								'</div>'+
							'</div>');

			confirm.dialog({
				width:'300px',
				height:'auto',
				title:'Удалить папку ?',
				drag:true
			});
			
			confirm.data('dialog').dialog.find('[data-confirm="cancel"]').click(function(e){
				confirm.dialog('close');
			});
			
			confirm.data('dialog').dialog.find('[data-confirm="agree"]').click(function(e){
				confirm.dialog('close');
				
				D.dialog('load');
				$.post(URL, {action:'delDir', path:selected.data('path')}, function(data){
					D.dialog('endLoad');
					
					methods.getTree(function(data){
						html.find('.fm-folders-box').html(methods.createTree([data]));
						D.dialog('endLoad');
					});
				}, 'json');
			});
		},

		getImages:function(e){
			e.preventDefault();
			
			var _this = $(this);
			
			html.find('.fm-folder').removeClass('selected');
			_this.addClass('selected');

			D.dialog('load');
			
			$.post(URL, {action:'getImage', path:_this.data('path')}, function(data){
				var div = $('<div>');
				$(data).each(function(){
					div.append(methods.createImage(this));
				});
				html.find('.fm-images-box').html(div);
				
				D.dialog('endLoad');
			}, 'json');
		},
		
		createImage:function(data){
			return $(
			'<a>'+
				'<img src="'+ data.cache +'">'+
				'<br>'+
				'<span>'+ data.name +'</span>'+
				'<br>'+ data.size +
			'</a>').data('file',data.path).data('cache',data.cache);
		},
	
		addImage:function(){
			var selected = html.find('.fm-folder.selected').eq(0),
				path = selected.data('path'),
				files = Array.prototype.slice.call(this.files),
				watermark = 0;
			
			if ( !selected.length || !files.length) return;
			
			upload();
			
			/*
			// watermark
			var confirm = $(
			'<div class="fm-confirm">'+
				'Применить водяной знак?'+
				'<br>'+
				'<div class="fm-buttons-box">'+
					'<button class="fm-buttons white" data-confirm="cancel">Нет</button>'+
					'<button class="fm-buttons white" data-confirm="agree">Да</button>'+
				'</div>'+
			'</div>');

			confirm.dialog({
				width:'300px',
				height:'auto',
				title:'Внимание!',
				drag:true
			}).bind('dialogClose', function(){
				// upload();
			});
			confirm.data('dialog').dialog.find('[data-confirm="cancel"]').click(function(e){
				confirm.dialog('close');
				watermark = 0;
				upload();
			});
			confirm.data('dialog').dialog.find('[data-confirm="agree"]').click(function(e){
				confirm.dialog('close');
				watermark = 1;
				upload();
			});
			*/

			function upload(){
				D.dialog('load');
				
				var form = new FormData();
				form.append('action', 'addImage');
				form.append('path', path);
				form.append('image', files[0]);
				form.append('watermark', watermark);
				
				$.ajax({
					type: "POST",
					url: URL,
					data: form,
					processData: false,
					contentType: false,
					dataType:'json',
					success: function(data){
						html.find('.fm-images-box div').prepend(methods.createImage(data));
						files.shift();
						
						files.length ? upload() : D.dialog('endLoad');
					}
				});
			};
		},
		
		delImage:function(){
			var img = html.find('.fm-images-box a.selected').eq(0);
			if ( ! img.length) return;
			
			D.dialog('load');
			$.post(URL, {action:'delImage', path:img.data('file')}, function(data){
				D.dialog('endLoad');
				img.animate({opacity:0}, 500, function(){
					$(this).remove();
				});
			});
		},
		
		exit:function(){
			D = null;
			html = null;
			target = null;
		}
	};

	$.FM = $.fn.FM = function(method){
		return methods['init'].apply(this, arguments); 
	};
	
})(jQuery);
