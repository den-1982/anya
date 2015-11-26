<div class="body">

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
				<td style="width:200px;">значения</td>
				<!--<td class="small">тип</td>-->
				<td class="small">visible</td>
				<td class="small"></td>
				<td class="small"></td>
			</tr>
		</thead>
		<tbody data-sortable="body">
			<?php $j=1; foreach($filters as $filter):?>
			<tr>
				<td><?=$j++?></td>
				<td>
					<span class="handler" data-sortable="handler"></span>
					<input type="hidden" data-sortable="id" name="filter_id[]" value="<?=$filter->id?>">
					<input type="hidden" data-sortable="order" name="filter_order[]" value="<?=$filter->order?>">
				</td>
				<td class="left"><?=$filter->name?></td>
				<td class="left">
					<?php 
						$_length = count($filter->items);
						$_shear = array_slice($filter->items, 0, 6);
						$_end = $_length - 6;
						$_res = array();
						foreach ($_shear as $i){$_res[] = $i->name;}
						echo implode($_res, ' &bull; ');
						if ($_end > 0){ echo '<span style="color:#bbb;">... еще '.$_end.' шт.</span>';}
					?>
				</td>
				<!--
				<td>
					<select class="select _type" data-id="<?=$filter->id?>" style="display:none;">
					<?php foreach ($filter_type as $type):?>
						<option value="<?=$type?>" <?=$filter->type == $type ? ' selected' : ''?>><?=$type?></option>
					<?php endforeach;?>
					</select>
				</td>
				-->
				<td class="small">
					<a class="toggle <?=$filter->visibility == 1 ? ' activ' : '' ?>" data-column="visibility" data-id="<?=$filter->id?>" title="<?=$filter->visibility == 1 ? 'скрыть' : 'показать ' ?> на сайте"></a>
				</td>
				<td>
					<a title="редактировать" class="link_edit" href="<?=$path?>?parent=<?=$parent?>&update=<?=$filter->id?>"></a>
				</td>
				<td>
					<a title="удалить" class="link_del" data-delete="<?=htmlspecialchars($filter->name)?>" href="<?=$path?>?parent=<?=$parent?>&delete=<?=$filter->id?>"></a>
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
		<table class="table-1">
			<tr>
				<td class="small right"><b>Название:</b></td>
				<td class="left"><input class="inf" type="text" name="name" value=""></td>
			</tr>
			<tr>
				<td class="right"><b>Ценообразующий:</b></td>
				<td class="left">
					<select data-select="" name="filter_pricing">
						<option value="0" selected>нет</option>
						<option value="1">да</option>
					</select>
				</td>
			</tr>
			<tr>
				<td class="right"><b>Изображение:</b></td>
				<td class="left">
					<div class="FM-image-box">
						<div class="i">
							<img class="FM-image" src="/">
						</div>
						<input type="hidden" name="image" value="">
						<br><a href="javascript:void(0)" class="FM-overview">обзор</a> | <a href="javascript:void(0)" class="FM-clear">очистить</a>
					</div>
				</td>
			</tr>
			<!--
			<tr>
				<td class="right"><b>Тип:</b></td>
				<td class="left">
					<select data-select="" name="filter_type">
					<?php foreach($filter_type as $type):?>
						<option value="<?=$type?>"><?=$type?></option>
					<?php endforeach;?>
					</select>
				</td>
			</tr>
			-->
			<tr>
				<td class="right"><b>Категории:</b><br>(где будет применятся фильтр)</td>
				<td class="left">
					<div class="box-parents">
						<div class="parents">
						<?php foreach ($categories as $category):?>
							<label class="level-<?=$category['level']; ?>">
								<?php echo str_repeat('<i>&mdash;</i>', $category['level']); ?>
								<input type="checkbox" name="id_category[]" value="<?=$category['id'];?>">
								<?=$category['_name']; ?>
							</label>
						<?php endforeach; ?>
						</div>
						<div class="select-all">
							<label>
								<input type="checkbox" onclick="$(this).parents('.box-parents').find('input').prop('checked', this.checked);"> 
								выбрать все
							</label>
						</div>
					</div>
				</td>
			</tr>
			<tr>
				<td class="right"><b>Значение фильтра:</b></td>
				<td class="left">
					<table class="table-1">
						<thead>
							<tr>
								<td class="small"></td>
								<td class="small">Изображение</td>
								<td><span title="обязательго для заполнения"><i class="c-red" >*</i> Название</span></td>
								<td>Префикс</td>
								<td class="small">
									<a class="link_add" data-filter-item="add" title="добавить"></a>
								</td>
							</tr>
						</thead>
						<tbody data-sortable="body" data-filter-item="box"></tbody>
					</table>
				</td>
			</tr>
		</table>
		
		<input type="hidden" name="add" value="">
	</form>

	<?php endif;?>
	
	
	
<!--UPDATE-->	
	<?php if($act == 'update'):?>
	<h1 class="title"><?=$h1?></h1>
	
	<div class="nav clearfix">
		<div class="fleft"></div>
		<div class="fright">
			<a style="margin-left:5px;" class="button orange" data-form-apply="">Применить</a>
			<a style="margin-left:5px;" class="button blue" onclick="$('#form').submit()" >Сохранить</a>
			<a style="margin-left:5px;" class="button black" href="<?=$path?>?parent=<?=$parent?>">Отмена</a>
		</div>
	</div>
	
	<form id="form" action="<?=$path?>" method="POST" enctype="multipart/form-data">
		<table class="table-1">
			<tr>
				<td class="small right"><b>Название:</b></td>
				<td class="left"><input class="inf" type="text" name="name" value="<?=htmlspecialchars($filter->name)?>"></td>
			</tr>
			<tr>
				<td class="right"><b>Ценообразующий:</b></td>
				<td class="left">
					<select data-select="" name="filter_pricing">
						<option value="0" selected>нет</option>
						<option value="1" <?=$filter->pricing ? ' selected': '';?>>да</option>
					</select>
				</td>
			</tr>
			<tr>
				<td class="right"><b>Изображение:</b></td>
				<td class="left">
					<div class="FM-image-box">
						<div class="i">
							<img class="FM-image" src="<?=htmlspecialchars($filter->image)?>">
						</div>
						<input type="hidden" name="image" value="<?=htmlspecialchars($filter->image)?>">
						<br><a href="javascript:void(0)" class="FM-overview">обзор</a> | <a href="javascript:void(0)" class="FM-clear">очистить</a>
					</div>
				</td>
			</tr>
			<!--
			<tr>
				<td class="right"><b>Тип:</b></td>
				<td class="left">
					<select data-select="" name="filter_type">
					<?php foreach($filter_type as $type):?>
						<option value="<?=$type?>" <?=$filter->type == $type ? ' selected': '';?>><?=$type?></option>
					<?php endforeach;?>
					</select>
				</td>
			</tr>
			-->
			<tr>
				<td class="right"><b>Категории:</b><br>(где будет применятся фильтр)</td>
				<td class="left">
					<div class="box-parents">
						<div class="parents">
						<?php foreach ($categories as $category):?>
							<label class="level-<?=$category['level']; ?>">
								<?=str_repeat('<i>&mdash;</i>', $category['level']);?>
								<input type="checkbox" name="id_category[]" value="<?=$category['id']; ?>"<?=in_array($category['id'], $filter->categories) ? ' checked="checked"' : '';?>>
								<?=$category['_name']; ?>
							</label>
						<?php endforeach; ?>
						</div>
						<div class="select-all">
							<label>
								<input type="checkbox" onclick="$(this).parents('.box-parents').find('input').prop('checked', this.checked);"> 
								выбрать все
							</label>
						</div>
					</div>
				</td>
			</tr>
			<tr>
				<td class="right"><b>Значение фильтра:</b></td>
				<td class="left">
					<table class="table-1">
						<thead>
							<tr>
								<td class="small"></td>
								<td class="small">Изображение</td>
								<td><span title="обязательго для заполнения"><i class="c-red" >*</i> Название</span></td>
								<td>Префикс</td>
								<td class="small">
									<a class="link_add" data-filter-item="add" title="добавить"></a>
								</td>
							</tr>
						</thead>
						<tbody data-sortable="body" data-filter-item="box">
						<?php foreach($filter->items as $item):?>
							<tr data-filter-item="item">
								<td>
									<span class="handler" data-sortable="handler"></span>
									<input type="hidden" data-sortable="order" name="filter_item_order[update][<?=$item->id?>]" value="<?=$item->order?>">
								</td>
								<td>
									<div class="FM-image-box" data-box-image="filter-item" <?=!$item->image ? 'style="display:none;"' : '';?>>
										<div class="i">
											<img class="FM-image" src="<?=htmlspecialchars($item->image)?>">
										</div>
										<input type="hidden" name="filter_item_image[update][<?=$item->id?>]" value="<?=htmlspecialchars($item->image)?>">
										<br><a href="javascript:void(0)" class="FM-overview">обзор</a> | <a href="javascript:void(0)" class="FM-clear">очистить</a>
									</div>
									<?php if ( ! $item->image):?>
									<a href="javascript:void(0)" data-bind="add-image-filter-item">+ добавить</a>
									<?php endif;?>
								</td>
								<td class="left">
									<input class="inf" name="filter_item_name[update][<?=$item->id?>]" value="<?=htmlspecialchars($item->name)?>">
								</td>
								<td class="left">
									<input class="inf" name="filter_item_prefix[update][<?=$item->id?>]" value="<?=htmlspecialchars($item->prefix)?>">
								</td>
								<td>
									<a class="link_del" data-filter-item="delete" title="удалить"></a>
								</td>
							</tr>
						<?php endforeach;?>
						</tbody>
					</table>
				</td>
			</tr>
		</table>

		<input type="hidden" name="id" value="<?=$filter->id?>">
		<input type="hidden" name="edit" value="">
	</form>
	<?php endif;?>
	
</div><!--CENTER-->

<script> // APPLY
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
</script>

<script> // CHANGE TYPE
// $(function(){
	// $('._type').selectmenu({
		// change:function(e, ui){
			// var d = $('<div>').addClass('_load');
			// $('body').append(d);
			// $.post('', {changeType:'', id:$(this).attr('data-id'), type:$(this).val()}, function(){
				// d.remove();
			// });;
		// }
	// });
// });
</script>

<script> // ADD ITEM FILTER
$(function(){
	var A = {
		create:function(){
			var html = $('<tr data-filter-item="item" class="new-tr">'+
							'<td class="small">'+
								'<span class="handler" data-sortable="handler"></span>'+
								'<input type="hidden" data-sortable="order" name="filter_item_order[insert][]" value="">'+
							'</td>'+
							'<td>'+
								'<div class="FM-image-box" data-box-image="filter-item" style="display:none;">'+
									'<div class="i">'+
										'<img class="FM-image" src="/">'+
									'</div>'+
									'<input type="hidden" name="filter_item_image[insert][]" value="">'+
									'<br><a href="javascript:void(0)" class="FM-overview">обзор</a> | <a href="javascript:void(0)" class="FM-clear">очистить</a>'+
								'</div>'+
								'<a href="javascript:void(0)" data-bind="add-image-filter-item">+ добавить</a>'+
							'</td>'+
							'<td class="left">'+
								'<input class="inf" name="filter_item_name[insert][]" value="">'+
							'</td>'+
							'<td class="left">'+
								'<input class="inf" name="filter_item_prefix[insert][]" value="">'+
							'</td>'+
							'<td>'+
								'<a class="link_del" data-filter-item="delete" title="удалить"></a>'+
							'</td>'+
						'</tr>');
			
			
			$('[data-filter-item="box"]').prepend(html).trigger('sortupdate');
			setTimeout(function(){html.removeClass('new-tr')}, 20);
		},
		showBoxImage:function(e){
			e.preventDefault();
			$(this).parent().find('[data-box-image="filter-item"]').show(200);
			$(this).remove();
		},
		init:function(){
			$(document).on('click','[data-filter-item="add"]', A.create);
			$(document).on('click', '[data-bind="add-image-filter-item"]', A.showBoxImage);
			$(document).on('click','[data-filter-item="delete"]',function(){
				$(this).parents('[data-filter-item="item"]').hide(200, function(){
					$(this).remove();
					if ( ! $('[data-filter-item="item"]').length){A.create();}
				});
			});
			
			if ( ! $('[data-filter-item="item"]').length) {A.create();}
		}
	}
	A.init();
});
</script>

