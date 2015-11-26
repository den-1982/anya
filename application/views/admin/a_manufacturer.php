<div class="body">

<!--ALL-->
	<?php if($act == 'all'):?>
	<h1><?=$h1?></h1>
	
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
				<td class="small"></td>
				<td>Название</td>
				<td class="small">visible</td>
				<td class="small"></td>
				<td class="small"></td>
			</tr>
		</thead>
		<tbody data-sortable="body">
			<?php $i=1; foreach($manufacturer as $k):?>
			<tr>
				<td><?=$i++?></td>
				<td>
					<span class="handler" data-sortable="handler"></span>
					<input data-sortable="id" type="hidden" name="manufacturer_id[]" value="<?=$k->id?>">
					<input data-sortable="order" type="hidden" name="manufacturer_order[]" value="<?=$k->order?>">
				</td>
				<td>
					<span class="image">
						<img onerror="this.src = '/img/i/loading_mini.gif'" src="/img/manufacturer/<?=$k->id?>/<?=$k->id?>.jpg" width="82" height="82">
					</span>
				</td>
				<td class="left"><a href="<?=$path?>?update=<?=$k->id?>"><?=$k->name?></a></td>
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
	<h1><?=$h1?></h1>
	
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
		</div>
		
		<div class="bookmark activ" data-id="data">
			<table class="table-1">
				<tr>
					<td class="small right"><b>Название:</b></td>
					<td class="left"><input class="inf" type="text" name="name" value=""></td>
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
			</table>
		</div>
		
		<input type="hidden" name="add" value="">
	</form>
	<?php endif;?>
	
	
	
	
	
<!--UPDATE-->
	<?php if($act == 'update'):?>
	<h1><?=$h1?></h1>
	
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
		</div>
		
		<div class="bookmark activ" data-id="data">
			<table class="table-1">
				<tr>
					<td class="small right"><b>Название:</b></td>
					<td class="left"><input class="inf" type="text" name="name" value="<?=htmlspecialchars($manufacturer->name)?>"></td>
				</tr>
				<tr>
					<td class="right"><b>Изображение:</b></td>
					<td class="left">
						<table class="table-1">
							<tr data-preload="box">
								<td class="small">
									<div class="image">
										<img data-preload="image" src="/img/manufacturer/<?=$manufacturer->id?>/<?=$manufacturer->id?>.jpg?">
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
			</table>
		</div>
		
		<input type="hidden" name="id" value="<?=$manufacturer->id?>">
		<input type="hidden" name="edit" value="">
	</form>
	<?php endif;?>

</div><!-- END BODY -->

<script> // APPLY
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
