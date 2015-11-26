<div class="body">

<!--ALL-->
	<?php if($act == 'all'):?>
	<h1 class="title"><?=$h1?></h1>
	
	<div class="nav clearfix">
		<div class="fleft"></div>
		<div class="fright">
			<a class="button green" style="margin-left:5px;" title="добавить" href="<?=$path?>?add&parent=<?=$parent?>">добавить</a>
		</div>
	</div>
	
	<table class="table-1" data-scroll="head">
		<thead>
			<tr>
				<td class="small">№</td>
				<td class="small">
					<a data-sortable="send-order" class="sendOrder" title="применить сортировку"></a>
				</td>
				<td>Название</td>
				<td class="small">Тип</td>
				<td class="small">URL</td>
				<td class="small">visible</td>
				<td class="small"></td>
				<td class="small"></td>
			</tr>
		</thead>
		<tbody data-sortable="body">
			<?php $i=1; foreach($pages as $k):?>
			<tr>
				<td><?=$i++?></td>
				<td>
					<span class="handler" data-sortable="handler"></span>
					<input data-sortable="id" type="hidden" name="page_id[]" value="<?=$k->id?>">
					<input data-sortable="order" type="hidden" name="page_order[]" value="<?=$k->order?>">
				</td>
				<td class="left"><a href="<?=$path?>?update=<?=$k->id?>"><?=$k->name?></a></td>
				<td class="left"><?=$k->type ? $k->type : ''?></td>
				<td class="left nowrap"><?=$k->url?></td>
				<td>
					<a class="toggle <?=$k->visibility == 1 ? ' activ' : '' ?>" data-column="visibility" data-id="<?=$k->id?>"></a>
				</td>
				<td><a title="редактировать" class="link_edit" href="<?=$path?>?update=<?=$k->id?>"></a></td>
				<td>
					<a title="удалить" class="link_del" data-delete="<?=htmlspecialchars($k->name)?>" href="<?=$path?>?parent=<?=$parent?>&delete=<?=$k->id?>"></a>
				</td>
			</tr>
			<?php endforeach;?>
		</tbody>
	</table>
	<?php endif;?>
	
	
	
	
	
<!--ADD-->	
	<?php if($act == 'add'):?>
	<h1 class="title"><?=$h1?></h1>
	
	<div class="nav clearfix">
		<div class="fleft"></div>
		<div class="fright">
			<a style="margin-left:5px;" class="button blue" onclick="$('#form').submit()" >Сохранить</a>
			<a style="margin-left:5px;" class="button black" href="<?=$path?>?parent=<?=$parent?>">Отмена</a>
		</div>
	</div>
	
	<form id="form" action="<?=$path?>" method="POST" enctype="multipart/form-data">
		<div class="toggle-box">
			<a class="bookmark-toggle activ" data-name="data" href="#data">Общие</a>
			<a class="bookmark-toggle" data-name="slider" href="#slider">Слайдер</a>
		</div>
		
		<div class="bookmark" data-id="slider">
			<table class="table-1">
				<thead>
					<tr>
						<td class="small"></td>
						<td class="small"></td>
						<td>Ссылка</td>
						<td class="small">
							<a class="link_add" data-slider="add" title="добавить"></a>
						</td>
					</tr>
				</thead>
				<tbody data-sortable="body" data-slider="box"></tbody>
			</table>
		</div>
		
		<div class="bookmark activ" data-id="data">
			<table class="table-1">
				<tr>
					<td class="small right"><b class="c-red">Тип:</b></td>
					<td class="left">
						<select data-select="" name="type">
							<option value="0" selected> - Выбрать (новость \ стсатья) - </option>
							<option value="news">новость</option>
							<option value="articles">статья</option>
						</select>
					</td>
				</tr>
				<tr>
					<td class="right"><b>Название:</b></td>
					<td class="left"><input class="inf" type="text" name="name" value=""></td>
				</tr>
				<tr>
					<td class="right"><b>H1:</b></td>
					<td class="left"><input class="inf" type="text" name="h1" value=""></td>
				</tr>
				<tr>
					<td class="right"><b>Title:</b></td>
					<td class="left"><input class="inf" type="text" name="title" value=""></td>
				</tr>
				<tr>
					<td class="right"><b>Description:</b></td>
					<td class="left"><textarea class="inf" style="height:70px;" name="metadesc" ></textarea></td>
				</tr>
				<tr>
					<td class="right"><b>Keywords:</b></td>
					<td class="left"><textarea class="inf" style="height:70px;" name="metakey" ></textarea></td>
				</tr>
				<tr>
					<td class="right"><b>СПАМ:</b></td>
					<td class="left"><textarea class="inf" style="height:70px;" name="spam" ></textarea></td>
				</tr>
				<tr>
					<td class="right"><b>URL:</b></td>
					<td class="left"><input class="inf" id="add_url" type="text" name="url" value=""></td>
				</tr>
				<tr>
					<td class="right"><b>Изображение:</b></td>
					<td class="left">
						<table class="table-1">
							<tr data-preload="box">
								<td class="small">
									<div class="image">
										<img data-preload="image" src="/">
									</div>
								</td>
								<td class="left">
									<a class="button green" href="javascript:void(0)">
										загрузить
										<input class="upload" data-button-file="image" type="file" name="image" value="">
									</a>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td class="right"><b>Текст:</b></td>
					<td class="left"><textarea class="tiny" rows="30" style="width:100%;" name="text"></textarea></td>
				</tr>
			</table>
		</div>
		
		<input type="hidden" name="add" value="">
	</form>
	<?php endif;?>
	
	
	
	
	
<!--UPDATE-->
	<?php if($act == 'update'):?>
	<h1 class="title"><?=$h1?></h1>
	
	<div class="nav clearfix">
		<div class="fleft"></div>
		<div class="fright">
			<a style="margin-left:5px;" class="button orange" data-form-apply="" >Применить</a>
			<a style="margin-left:5px;" class="button blue" onclick="$('#form').submit()" >Сохранить</a>
			<a style="margin-left:5px;" class="button black" href="<?=$path?>?parent=<?=$parent?>">Отмена</a>
		</div>
	</div>
	
	<form id="form" action="<?=$path?>" method="POST" enctype="multipart/form-data">
		<div class="toggle-box">
			<a class="bookmark-toggle activ" data-name="data" href="#data">Общие</a>
			<a class="bookmark-toggle" data-name="slider" href="#slider">Слайдер</a>
		</div>
		
		<div class="bookmark" data-id="slider">
			<table class="table-1">
				<thead>
					<tr>
						<td class="small"></td>
						<td class="small"></td>
						<td>Ссылка</td>
						<td class="small">
							<a class="link_add" data-slider="add" title="добавить"></a>
						</td>
					</tr>
				</thead>
				<tbody data-sortable="body" data-slider="box">
				<?php foreach($page->slider as $k):?>
					<tr data-slider="item">
						<td>
							<span class="handler" data-sortable="handler"></span>
							<input data-sortable="order" type="hidden" name="slider_order[]" value="<?=$k->order?>">
						</td>
						<td>
							<div class="FM-image-box">
								<div class="i">
									<img class="FM-image" src="<?=$k->cache?>">
								</div>
								<input type="hidden" name="slider_image[]" value="<?=$k->img?>">
								<br><a href="/" class="FM-overview">обзор</a> | <a href="/" class="FM-clear">очистить</a>
							</div>
						</td>
						<td class="left">
							<input class="inf" type="text" name="slider_link[]" value="<?=htmlspecialchars($k->link)?>">
						</td>
						<td>
							<a class="link_del" data-slider="delete" title="удалить"></a>
						</td>
					</tr>
				<?php endforeach;?>
				</tbody>
			</table>
		</div>
		
		<div class="bookmark activ" data-id="data">
			<table class="table-1">
				<tr>
					<td class="small right"><b class="c-red">Тип:</b></td>
					<td class="left">
						<select data-select="" name="type">
							<option value="0" selected> - Выбрать (новость \ стсатья) - </option>
							<option value="news" <?=$page->type == 'news' ? 'selected' : '';?>>Новость</option>
							<option value="articles" <?=$page->type == 'articles' ? 'selected' : '';?>>Статья</option>
						</select>
					</td>
				</tr>
				<tr>
					<td class="right"><b>Название:</b></td>
					<td class="left"><input class="inf" type="text" name="name" value="<?=htmlspecialchars($page->name)?>"></td>
				</tr>
				<tr>
					<td class="right"><b>H1:</b></td>
					<td class="left"><input class="inf" type="text" name="h1" value="<?=htmlspecialchars($page->h1)?>"></td>
				</tr>
				<tr>
					<td class="right"><b>Title:</b></td>
					<td class="left"><input class="inf" type="text" name="title" value="<?=htmlspecialchars($page->title)?>"></td>
				</tr>
				<tr>
					<td class="right"><b>Description:</b></td>
					<td class="left"><textarea class="inf" style="height:70px;" name="metadesc"><?=$page->metadesc?></textarea></td>
				</tr>
				<tr>
					<td class="right"><b>Keywords:</b></td>
					<td class="left"><textarea class="inf" style="height:70px;" name="metakey"><?=$page->metakey?></textarea></td>
				</tr>
				<tr>
					<td class="right"><b>СПАМ:</b></td>
					<td class="left"><textarea class="inf" style="height:70px;" name="spam"><?=$page->spam?></textarea></td>
				</tr>
				<tr>
					<td class="right"><b>URL:</b></td>
					<td class="left"><input class="inf" id="add_url" type="text" name="url" value="<?=htmlspecialchars($page->url)?>"></td>
				</tr>
				
				<tr>
					<td class="right"><b>Изображение:</b></td>
					<td class="left">
						<table class="table-1">
							<tr data-preload="box">
								<td class="small">
									<div class="image">
										<img data-preload="image" src="/img/news-articles/<?=$page->id?>/<?=$page->id?>_82_82.jpg">
									</div>
								</td>
								<td class="left">
									<a class="button green" href="javascript:void(0)">
										загрузить
										<input class="upload" data-button-file="image" type="file" name="image" value="">
									</a>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td class="right"><b>Текст:</b></td>
					<td class="left"><textarea class="tiny" rows="30" name="text"><?=$page->text?></textarea></td>
				</tr>
			</table>
		</div>

		<input type="hidden" name="id" value="<?=$page->id?>">
		<input type="hidden" name="edit" value="">
	</form>
	<?php endif;?>

</div><!-- END BODY -->


<script>// APPLY
$(function(){
	$(document).on('click', '[data-form-apply]', function(e){
		$(document.body).append($('<div>').addClass('_load'));
		
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

<script>// SLIDER
$(function(){
	var A = {
		create:function(){
			var html = $('<tr data-slider="item" class="new-tr">'+
							'<td>'+
								'<span class="handler" data-sortable="handler"></span>'+
								'<input data-sortable="order" type="hidden" name="slider_order[]" value="">'+
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
			$(document).on('click','[data-slider="add"]', A.create);
			$(document).on('click','[data-slider="delete"]',function(){
				$(this).parents('[data-slider="item"]').hide(200,function(){
					$(this).remove();
					if ( ! $('[data-slider="item"]').length){A.create();}
				});
			});
			
			if ( ! $('[data-slider="item"]').length) {A.create();}
		}
	}
	A.init();
});
</script>