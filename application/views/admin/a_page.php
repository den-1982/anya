<div class="body">

<!--ALL-->
	<?php if ($act == 'all'):?>
	<h1 class="title"><?=$h1?></h1>
	
	<div class="nav">
		<div class="fleft">
			<?php if ($crumbs):?>
			<div class="crumbs">
				<a href="<?=$path?>">Страницы</a>
				<?php $last = array_pop($crumbs);?>
				<?php foreach($crumbs as $k):?>
					::
					<a href="<?=$path?>?parent=<?=$k['id']?>"><?=$k['name']?></a>
				<?php endforeach;?>
					:: <span><?=$last['name']?></span>
			</div>
			<?php endif;?>
		</div>
		<div class="fright">
			<a class="button green" href="<?=$path?>?add&parent=<?=$parent?>" title="добавить">Добавить</a>
		</div>
	</div>
	
	<table class="table-1" data-scroll="head">
		<thead>
			<tr>
				<td class="small">№</td>
				<td class="small">
					<a class="icon-save send-order" data-sortable="send-order" title="применить сортировку"></a>
				</td>
				<td>Название</td>
				<td class="small">URL</td>
				<td class="small">visible</td>
				<td class="small"></td>
				<td class="small"></td>
			</tr>
		</thead>
		<tbody data-sortable="body">
			<?php $i=1; if (isset($pages[$parent])) foreach ($pages[$parent] as $k):?>
			<tr>
				<td><?=$i++?></td>
				<td>
					<span class="icon-reorder handler" data-sortable="handler"></span>
					<input data-sortable="id" type="hidden" name="page_id[]" value="<?=$k->id?>">
					<input data-sortable="order" type="hidden" name="page_order[]" value="<?=$k->order?>">
				</td>
				<td class="left">
					<a href="<?=$path?>?parent=<?=$k->id?>"><?=$k->name?></a>
					<?=$k->cnt_childs ? '<span class="c-grey">&rarr; ('.$k->cnt_childs.')</span>' : '';?>
				</td>
				<td class="left nowrap"><?=$k->url?></td>
				<td>
					<a class="toggle icon-eye <?=$k->visibility == 1 ? ' activ' : ''?>" data-bind="toggle" data-column="visibility" data-id="<?=$k->id?>" title="<?=$k->visibility == 1 ? 'скрыть' : 'показать ' ?> на сайте"></a>
				</td>
				<td>	
					<a class="link_edit" href="<?=$path?>?parent=<?=$parent?>&update=<?=$k->id?>" title="редактировать"></a>
				</td>
				<td>
					<a class="link_del" data-post="<?=$k->id?>" data-delete="<?=htmlspecialchars($k->name)?>" href="<?=$path?>?parent=<?=$parent?>&delete=<?=$k->id?>" title="удалить"></a>
				</td>
			</tr>
			<?php endforeach;?>
		</tbody>
	</table>
	<?php endif;?>
	
	
	
<!--ADD-->	
	<?php if ($act == 'add'):?>
	<h1 class="title"><?=$h1?></h1>
	
	<div class="nav">
		<div class="fleft"></div>
		<div class="fright">
			<a class="button blue" onclick="$('#form').submit()" >Сохранить</a>
			<a class="button black" href="<?=$path?>?parent=<?=$parent?>">Отмена</a>
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
					<td class="right small">
						<b class="c-red">Связь:</b>
					</td>
					<td class="left">
						<select data-select="" name="parent">
							<option value="0"> - Выбрать - </option>
							<?php foreach($parents as $k):?>
							<option <?=$parent == $k['id'] ? 'selected' : ''?>  value="<?=$k['id']?>"><?=$k['name']?></option>
							<?php endforeach;?>
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
						<div class="FM-image-box">
							<div class="i">
								<img class="FM-image" src="/img/i/loading_mini.gif">
							</div>
							<input type="hidden" name="image" value="">
							<br><a href="javascript:void(0)" class="FM-overview">обзор</a> | <a href="javascript:void(0)" class="FM-clear">очистить</a>
						</div>
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
	<?php if ($act == 'update'):?>
	<h1 class="title"><?=$h1?></h1>
	
	<div class="nav">
		<div class="fleft"></div>
		<div class="fright">
			<a class="button orange" data-form-apply="" >Применить</a>
			<a class="button blue" onclick="$('#form').submit()" >Сохранить</a>
			<a class="button black" href="<?=$path?>?parent=<?=$parent?>">Отмена</a>
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
							<span class="icon-reorder handler" data-sortable="handler"></span>
							<input data-sortable="order" type="hidden" name="slider[order][]" value="<?=$k->order?>">
						</td>
						<td>
							<div class="FM-image-box">
								<div class="i">
									<img class="FM-image" src="<?=$k->cache?>">
								</div>
								<input type="hidden" name="slider[image][]" value="<?=$k->image?>">
								<br><a href="/" class="FM-overview">обзор</a> | <a href="/" class="FM-clear">очистить</a>
							</div>
						</td>
						<td class="left">
							<input class="inf" type="text" name="slider[link][]" value="<?=htmlspecialchars($k->link)?>">
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
					<td class="small right"><b class="c-red">Связь:</b></td>
					<td class="left">
						<select data-select="" name="parent">
							<option value="0"> - Выбрать - </option>
							<?php foreach($parents as $k):?>
							<option <?=$page->parent == $k['id'] ? 'selected' : ''?>  value="<?=$k['id']?>"><?=$k['name']?></option>
							<?php endforeach;?>
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
						<div class="FM-image-box">
							<div class="i">
								<img class="FM-image" src="<?=htmlspecialchars($page->cache)?>" alt="Image">
							</div>
							<input type="hidden" name="image" value="<?=htmlspecialchars($page->image)?>">
							<br><a href="javascript:void(0)" class="FM-overview">обзор</a> | <a href="javascript:void(0)" class="FM-clear">очистить</a>
						</div>
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
$(document).on('click', '[data-form-apply]', function(e){
	$(document.body).append('<div class="load"></div>');
	
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

<script> // SLIDER
$(function(){
	var A = {
		create:function(){
			var html = $(
			'<tr data-slider="item" class="new-tr">'+
				'<td>'+
					'<span class="icon-reorder handler" data-sortable="handler"></span>'+
					'<input data-sortable="order" type="hidden" name="slider[order][]" value="">'+
				'</td>'+
				'<td>'+
					'<div class="FM-image-box">'+
						'<div class="i">'+
							'<img class="FM-image" src="/img/i/loading_mini.gif">'+
						'</div>'+
						'<input type="hidden" name="slider[image][]" value="">'+
						'<br><a href="/" class="FM-overview">обзор</a> | <a href="/" class="FM-clear">очистить</a>'+
					'</div>'+
				'</td>'+
				'<td class="left">'+
					'<input class="inf" type="text" name="slider[link][]" value="">'+
				'</td>'+
				'<td>'+
					'<a class="link_del" data-slider="delete" title="удалить"></a>'+
				'</td>'+
			'</tr>');
						
			$('[data-slider="box"]').prepend(html);
			setTimeout(function(){html.removeClass('new-tr')},20);
		},
		init:function(){
			$(document).on('click','[data-slider="add"]', A.create)
			.on('click','[data-slider="delete"]',function(){
				$(this).parents('[data-slider="item"]').hide(200,function(){
					$(this).remove();
				});
			});
		}
	}
	A.init();
});
</script>