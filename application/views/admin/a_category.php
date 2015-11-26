<div class="body">

<!--ALL-->
	<?php if($act == 'all'):?>
	<h1 class="title"><?=$h1?></h1>

	<div class="nav">
		<div class="fleft">
			<?php if($crumbs):?>
			<div class="crumbs">
				<a href="<?=$path?>">Категории</a>
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
			<td class="small">№</td>
			<td class="small">
				<a class="icon-save send-order" data-sortable="send-order" title="применить сортировку"></a>
			</td>
			<td>название</td>
			<td class="small">URL</td>
			<td class="small">visible</td>
			<td class="small"></td>
			<td class="small"></td>
		</thead>
		<tbody data-sortable="body">
		<?php if (isset($categories[$parent])):?>
		<?php $i=1; foreach($categories[$parent] as $category):?>
		<tr>
			<td><?=$i++?></td>
			<td>
				<span class="icon-reorder handler" data-sortable="handler"></span>
				<input data-sortable="id" type="hidden" name="category_id[]" value="<?=$category->id?>">
				<input data-sortable="order" type="hidden" name="category_order[]" value="<?=$category->order?>">
			</td>
			<td class="left">
				<a href="<?=$path?>?parent=<?=$category->id?>"><?=$category->name?></a>
				<?=$category->cnt_childs ? '<span class="c-grey">&rarr; ('.$category->cnt_childs.')</span>' : '';?>
			</td>
			<td class="left nowrap"><?=$category->url?></td>
			<td>
				<a class="toggle icon-eye <?=$category->visibility == 1 ? ' activ' : ''?>" data-bind="toggle" data-column="visibility" data-id="<?=$category->id?>" title="<?=$category->visibility == 1 ? 'скрыть' : 'показать ' ?> на сайте"></a>
			</td>
			<td>	
				<a title="редактировать" class="link_edit" href="<?=$path?>?parent=<?=$parent?>&update=<?=$category->id?>"></a>
			</td>
			<td>
				<a class="link_del" data-post="<?=$category->id?>" data-delete="<?=htmlspecialchars($category->name)?>" href="<?=$path?>?parent=<?=$parent?>&delete=<?=$category->id?>" title="удалить"></a>
			</td>
		</tr>
		<?php endforeach;?>
		<?php endif;?>
		</tbody>
	</table>
	<?php endif;?>
	
	
	
<!--ADD-->		
	<?php if($act == 'add'):?>
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
					<td class="small right">
						<b class="c-red">Связь:</b>
					</td>
					<td class="left">
						<select data-select="" name="parent">
							<option value="0"> - Выбрать категорию - </option>
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
					<td class="left"><textarea class="inf" style="height:70px;" name="metadesc"></textarea></td>
				</tr>
				<tr>
					<td class="right"><b>Keywords:</b></td>
					<td class="left"><textarea class="inf" style="height:70px;" name="metakey"></textarea></td>
				</tr>
				<tr>
					<td class="right"><b>СПАМ:</b></td>
					<td class="left"><textarea class="inf" style="height:70px;" name="spam"></textarea></td>
				</tr>
				<tr>
					<td class="right"><b>Ссылка:</b></td>
					<td class="left"><input class="inf" type="text" name="url" value=""></td>
				</tr>
				<tr>
					<td class="right"><b style="color:green;">Курс($):</b></td>
					<td class="left"><input class="inf min" data-mask="price" type="text" name="course" value="0"></td>
				</tr>
				<tr>
					<td class="right"><b style="color:red;">Скидка:</b></td>
					<td class="left"><input class="inf min" data-mask="price" type="text" name="discount" value="0"></td>
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
					<td class="left">
						<textarea class="tiny" style="width:100%;" rows="30" name="text"></textarea>
					</td>
				</tr>
			</table>
		</div>
		
		<input type="hidden" name="add" value="">
	</form>
	<?php endif;?>
	
	
	
<!--UPDATE-->
	<?php if($act == 'update'):?>
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
				<?php foreach($category->slider as $k=>$v):?>
					<tr data-slider="item">
						<td>
							<span class="icon-reorder handler" data-sortable="handler"></span>
							<input data-sortable="order" type="hidden" name="slider[order][]" value="<?=$v->order?>">
						</td>
						<td>
							<div class="FM-image-box">
								<div class="i">
									<img class="FM-image" src="<?=$v->cache?>">
								</div>
								<input type="hidden" name="slider[image][]" value="<?=htmlspecialchars($v->image)?>">
								<br><a href="/" class="FM-overview">обзор</a> | <a href="/" class="FM-clear">очистить</a>
							</div>
						</td>
						<td class="left">
							<input class="inf" type="text" name="slider[link][]" value="<?=htmlspecialchars($v->link)?>">
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
							<option value="0"> - Выбрать категорию - </option>
							<?php foreach($parents as $k):?>
							<option <?=$category->parent == $k['id'] ? 'selected' : ''?>  value="<?=$k['id']?>"><?=$k['name']?></option>
							<?php endforeach;?>
						</select>
					</td>
				</tr>
				<tr>
					<td class="right"><b>Название:</b></td>
					<td class="left">
						<input class="inf" type="text" name="name" value="<?=htmlspecialchars($category->name)?>">
					</td>
				</tr>
				<tr>
					<td class="right"><b>H1:</b></td>
					<td class="left">
						<input class="inf" type="text" name="h1" value="<?=htmlspecialchars($category->h1)?>">
					</td>
				</tr>
				<tr>
					<td class="right"><b>Title:</b></td>
					<td class="left">
						<input class="inf" type="text" name="title" value="<?=htmlspecialchars($category->title)?>">
					</td>
				</tr>
				<tr>
					<td class="right"><b>Description:</b></td>
					<td class="left">
						<textarea class="inf" style="height:70px;" name="metadesc"><?=$category->metadesc?></textarea>
					</td>
				</tr>
				<tr>
					<td class="right"><b>Keywords:</b></td>
					<td class="left"><textarea class="inf" style="height:70px;" name="metakey"><?=$category->metakey?></textarea></td>
				</tr>
				<tr>
					<td class="right"><b>СПАМ:</b></td>
					<td class="left">
						<textarea class="inf" style="height:70px;" name="spam"><?=$category->spam?></textarea>
					</td>
				</tr>
				<tr>
					<td class="right"><b>Ссылка:</b></td>
					<td class="left">
						<input class="inf" type="text" name="url" value="<?=htmlspecialchars($category->url)?>">
					</td>
				</tr>
				<tr>
					<td class="right"><b class="c-green">Курс($):</b></td>
					<td class="left">
						<input class="inf min" data-mask="price" type="text" name="course" value="<?=$category->course?>">
					</td>
				</tr><tr>
					<td class="right"><b class="c-red">Скидка:</b></td>
					<td class="left">
						<input class="inf min" data-mask="price" type="text" name="discount" value="<?=$category->discount?>">
					</td>
				</tr>
				<tr>
					<td class="right"><b>Изображение:</b></td>
					<td class="left">
						<div class="FM-image-box">
							<div class="i">
								<img class="FM-image" src="<?=htmlspecialchars($category->cache)?>">
							</div>
							<input type="hidden" name="image" value="<?=htmlspecialchars($category->image)?>">
							<br><a href="javascript:void(0)" class="FM-overview">обзор</a> | <a href="javascript:void(0)" class="FM-clear">очистить</a>
						</div>
					</td>
				</tr>
				<tr>
					<td class="right"><b>Текст:</b></td>
					<td class="left">
						<textarea class="tiny" rows="30" style="width:100%;" name="text"><?=$category->text?></textarea>
					</td>
				</tr>
			</table>
		</div>

		<input type="hidden" name="id" value="<?=$category->id?>">
		<input type="hidden" name="edit" value="">
	</form>
	<?php endif;?>

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
