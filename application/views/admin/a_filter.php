<div class="body">

	<?php if($act == 'all'):?>
	<h1 class="title"><?=$h1?></h1>
	
	<div class="nav">
		<div class="fleft"></div>
		<div class="fright">
			<a class="button green" href="<?=$path?>?add&parent=<?=$parent?>" title="добавить">добавить</a>
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
				<td style="width:200px;">Значения</td>
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
					<span class="icon-reorder handler" data-sortable="handler"></span>
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
				<td>
					<a class="toggle icon-eye <?=$filter->visibility == 1 ? ' activ' : ''?>" data-bind="toggle" data-column="visibility" data-id="<?=$filter->id?>" title="<?=$filter->visibility == 1 ? 'скрыть' : 'показать ' ?> на сайте"></a>
				</td>
				<td>	
					<a class="link_edit" href="<?=$path?>?parent=<?=$parent?>&update=<?=$filter->id?>" title="редактировать"></a>
				</td>
				<td>
					<a class="link_del" data-delete="<?=htmlspecialchars($filter->name)?>" href="<?=$path?>?parent=<?=$parent?>&delete=<?=$filter->id?>" title="удалить"></a>
				</td>
			</tr>
			<?php endforeach;?>
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
		</div>
		
		<div class="bookmark activ" data-id="data">
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
				<tr>
					<td class="right"><b>Категории:</b><br>(где будет применятся фильтр)</td>
					<td class="left">
						<div class="box-parents">
							<div class="parents">
							<?php foreach ($categories as $category):?>
								<label class="level-<?=$category['level']; ?>">
									<?php echo str_repeat('<i>&mdash;</i>', $category['level']); ?>
									<input type="checkbox" name="category_id[]" value="<?=$category['id'];?>">
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
			<a class="button orange" data-form-apply="">Применить</a>
			<a class="button blue" onclick="$('#form').submit()" >Сохранить</a>
			<a class="button black" href="<?=$path?>?parent=<?=$parent?>">Отмена</a>
		</div>
	</div>
	
	<form id="form" action="<?=$path?>" method="POST" enctype="multipart/form-data">
		<div class="toggle-box">
			<a class="bookmark-toggle activ" data-name="data" href="#data">Общие</a>
		</div>
		
		<div class="bookmark activ" data-id="data">
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
								<img class="FM-image" src="<?=htmlspecialchars($filter->cache)?>">
							</div>
							<input type="hidden" name="image" value="<?=htmlspecialchars($filter->image)?>">
							<br><a href="javascript:void(0)" class="FM-overview">обзор</a> | <a href="javascript:void(0)" class="FM-clear">очистить</a>
						</div>
					</td>
				</tr>
				<tr>
					<td class="right"><b>Категории:</b><br>(где будет применятся фильтр)</td>
					<td class="left">
						<div class="box-parents">
							<div class="parents">
							<?php foreach ($categories as $category):?>
								<label class="level-<?=$category['level']; ?>">
									<?=str_repeat('<i>&mdash;</i>', $category['level']);?>
									<input type="checkbox" name="category_id[]" value="<?=$category['id']; ?>"<?=in_array($category['id'], $filter->categories) ? ' checked="checked"' : '';?>>
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
										<span class="icon-reorder handler" data-sortable="handler"></span>
										<input type="hidden" data-sortable="order" name="filter_item_order[update][<?=$item->id?>]" value="<?=$item->order?>">
									</td>
									<td>
										<div class="FM-image-box" data-box-image="filter-item" <?=!$item->image ? 'style="display:none;"' : '';?>>
											<div class="i">
												<img class="FM-image" src="<?=htmlspecialchars($item->cache)?>">
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
		</div>
		
		<input type="hidden" name="id" value="<?=$filter->id?>">
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

<script> // ADD ITEM FILTER
$(function(){
	var A = {
		create:function(){
			var html = $(
			'<tr data-filter-item="item" class="new-tr">'+
				'<td class="small">'+
					'<span class="icon-reorder handler" data-sortable="handler"></span>'+
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
			$(document).on('click','[data-filter-item="add"]', A.create)
			.on('click', '[data-bind="add-image-filter-item"]', A.showBoxImage)
			.on('click','[data-filter-item="delete"]',function(){
				$(this).parents('[data-filter-item="item"]').hide(200, function(){
					$(this).remove();
				});
			});
		}
	}
	A.init();
});
</script>

