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
			<a class="bookmark-toggle activ" data-name="data" href="#data">Общие</a>
			<a class="bookmark-toggle" data-name="slider" href="#slider">Слайдер</a>
		</div>

		<div class="bookmark activ" data-id="data">
			<table class="table-1">
				<tbody>
					<tr>
						<td class="small nowrap right">
							<b>H1:</b>
						</td>
						<td>
							<input class="inf" type="text" name="h1" value="<?=$about->h1?>">
						</td>
					</tr>
					<tr>
						<td class="small nowrap right">
							<b>Title:</b>
						</td>
						<td>
							<textarea style="height:100px;" class="inf" name="title"><?=$about->title?></textarea>
						</td>
					</tr>
					<tr>
						<td class="small nowrap right">
							<b>Metadesc:</b>
						</td>
						<td>
							<textarea style="height:100px;" class="inf" name="metadesc"><?=$about->metadesc?></textarea>
						</td>
					</tr>
					<tr>
						<td class="small nowrap right">
							<b>Metakey:</b>
						</td>
						<td>
							<textarea style="height:100px;" class="inf" name="metakey"><?=$about->metakey?></textarea>
						</td>
					</tr>
					<tr>
						<td class="small nowrap right">
							<b>Spam:</b>
						</td>
						<td>
							<textarea style="height:70px;" class="inf" name="spam"><?=$about->spam?></textarea>
						</td>
					</tr>
					<tr>
						<td class="small nowrap right">
							<b>Text:</b>
						</td>
						<td>
							<textarea class="tiny" style="height:300px;" name="text"><?=$about->text?></textarea>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		
		<div class="bookmark" data-id="slider">
			<table class="table-1">
				<thead>
					<tr>
						<td class="small">№</td>
						<td class="small"></td>
						<td>H1-заголовок</td>
						<td>Текст</td>
						<td>Ссылка (URL)</td>
						<td class="small">
							<a class="link_add" data-slider="add" title="добавить"></a>
						</td>
					</tr>
				</thead>
				<tbody data-sortable="body" data-slider="box">
				<?php foreach ($about->slider as $slide):?>
					<tr data-slider="item">
						<td>
							<span data-sortable="handler"class="handler"></span>
						</td>
						<td>
							<div class="FM-image-box" style="">
								<div class="i">
									<img class="FM-image" src="<?=$slide->image ? $slide->image : '/';?>">
								</div>
								<input type="hidden" name="slider_image[]" value="<?=$slide->image?>">
								<br>
								<a href="/" class="FM-overview">обзор</a> | <a href="/" class="FM-clear">очистить</a>
							</div>
						</td>
						<td class="left">
							<textarea class="inf" style="height:70px;" name="slider_h1[]"><?=$slide->h1?></textarea>
						</td>
						<td class="left">
							<textarea class="inf" style="height:70px;" name="slider_text[]"><?=$slide->text?></textarea>
						</td>
						<td class="left">
							<input class="inf" type="text" name="slider_link[]" value="<?=htmlspecialchars($slide->link)?>">
						</td>
						<td>
							<a class="link_del" data-slider="delete" title="удалить"></a>
						</td>
					</tr>
					<?php endforeach;?>
				</tbody>
			</table>
		</div>
		
		<input type="hidden" name="id" value="<?=$about->id?>">
		<input type="hidden" name="edit" value="">
	</form>
	
</div><!-- END BODY -->

<script> // APPLY
$(function(){
	$(document).on('click', '[data-form-apply]', function(e){
		e.preventDefault();
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
});
</script>


<script> // SLIDER (BANER)
;$(function(){
	var S = {
		create:function(){
			var html = $('<tr data-slider="item" class="new-tr">'+
							'<td>'+
								'<span class="handler" data-sortable="handler"></span>'+
							'</td>'+
							'<td>'+
								'<div class="FM-image-box">'+
									'<div class="i">'+
										'<img class="FM-image" src="/img/i_admin/loading_mini.gif">'+
									'</div>'+
									'<input type="hidden" name="slider_image[]" value="">'+
									'<br><a href="/" class="FM-overview">обзор</a> | <a href="/" class="FM-clear">очистить</a>'+
								'</div>'+
							'</td>'+
							'<td class="left">'+
								'<textarea class="inf" style="height:70px;" name="slider_h1[]"></textarea>'+
							'</td>'+
							'<td class="left">'+
								'<textarea class="inf" style="height:70px;" name="slider_text[]"></textarea>'+
							'</td>'+
							'<td class="left">'+
								'<input class="inf" type="text" name="slider_link[]" value="">'+
							'</td>'+
							'<td>'+
								'<a class="link_del" data-slider="delete" title="удалить"></a>'+
							'</td>'+
						'</tr>');
			
			$('[data-slider="box"]').prepend(html);
			setTimeout(function(){html.removeClass('new-tr')}, 20);
		},
		init:function(){
			$(document).on('click','[data-slider="add"]', S.create);
			$(document).on('click','[data-slider="delete"]', function(){
				$(this).parents('[data-slider="item"]').hide(200,function(){
					$(this).remove();
					if ( ! $('[data-slider="item"]').length){S.create();}
				});
			});
			
			if ( ! $('[data-slider="item"]').length){S.create();}
		}
	}
	S.init();
});
</script>




