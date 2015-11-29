<div class="body">

<!--ALL-->
	<?php if ($act == 'all'):?>
	<h1 class="title"><?=$h1?></h1>
	
	<div class="nav">
		<div class="fleft"></div>
		<div class="fright">
			<a class="button green" title="добавить" href="<?=$path?>?add">добавить</a>
		</div>
	</div>
	
	<table class="table-1" data-scroll="head">
		<thead>
			<tr>
				<td class="small">№</td>
				<td class="small">
					<a class="icon-save send-order" data-sortable="send-order" title="применить сортировку"></a>
				</td>
				<td class="small"></td>
				<td>Название</td>
				<td class="small">URL</td>
				<td class="small">visible</td>
				<td class="small"></td>
				<td class="small"></td>
			</tr>
		</thead>
		<tbody data-sortable="body">
			<?php $i=1; foreach($partners as $k):?>
			<tr>
				<td><?=$i++?></td>
				<td>
					<span class="icon-reorder handler" data-sortable="handler"></span>
					<input data-sortable="id" type="hidden" name="partner_id[]" value="<?=$k->id?>">
					<input data-sortable="order" type="hidden" name="partner_order[]" value="<?=$k->order?>">
				</td>
				<td>
					<span class="image">
						<img src="<?=htmlspecialchars($k->cache)?>" alt="Image">
					</span>
				</td>
				<td class="left">
					<a href="<?=$path?>?update=<?=$k->id?>"><?=$k->name?></a>
				</td>
				<td class="left"><?=$k->url?></td>
				
				
				<td>
					<a class="toggle icon-eye <?=$k->visibility == 1 ? ' activ' : ''?>" data-bind="toggle" data-column="visibility" data-id="<?=$k->id?>" title="<?=$k->visibility == 1 ? 'скрыть' : 'показать ' ?> на сайте"></a>
				</td>
				<td>	
					<a class="link_edit" href="<?=$path?>?parent=<?=$parent?>&update=<?=$k->id?>" title="редактировать"></a>
				</td>
				<td>
					<a class="link_del" data-delete="<?=htmlspecialchars($k->name)?>" href="<?=$path?>?parent=<?=$parent?>&delete=<?=$k->id?>" title="удалить"></a>
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
		</div>
		
		<div class="bookmark activ" data-id="data">
			<table class="table-1">
				<tr>
					<td class="right small"><b>Название:</b></td>
					<td class="left">
						<input class="inf" type="text" name="name" value="">
					</td>
				</tr>
				<tr>
					<td class="right"><b>H1:</b></td>
					<td class="left">
						<input class="inf" type="text" name="h1" value="">
					</td>
				</tr>
				<tr>
					<td class="right"><b>Title:</b></td>
					<td class="left">
						<input class="inf" type="text" name="title" value="">
					</td>
				</tr>
				<tr>
					<td class="right"><b>Description:</b></td>
					<td class="left">
						<textarea class="inf" style="height:70px;" name="metadesc"></textarea>
					</td>
				</tr>
				<tr>
					<td class="right"><b>Keywords:</b></td>
					<td class="left">
						<textarea class="inf" style="height:70px;" name="metakey"></textarea>
					</td>
				</tr>
				<tr>
					<td class="right"><b>СПАМ:</b></td>
					<td class="left">
						<textarea class="inf" style="height:70px;" name="spam"></textarea>
					</td>
				</tr>
				<tr>
					<td class="right"><b>Ссылка:</b></td>
					<td class="left">
						<input class="inf" type="text" name="url" value="">
					</td>
				</tr>
				<tr>
					<td class="right"><b>Изображение:</b></td>
					<td class="left">
						<div class="FM-image-box">
							<div class="i">
								<img class="FM-image" src="/img/i/loading_mini.gif" alt="Image">
							</div>
							<input type="hidden" name="image" value="">
							<br><a href="javascript:void(0)" class="FM-overview">обзор</a> | <a href="javascript:void(0)" class="FM-clear">очистить</a>
						</div>
					</td>
				</tr>
				<tr>
					<td class="right"><b>Текст:</b></td>
					<td class="left">
						<textarea class="tiny" rows="30" name="text"></textarea>
					</td>
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
		</div>
		
		<div class="bookmark activ" data-id="data">
			<table class="table-1">
				<tr>
					<td class="right small"><b>Название:</b></td>
					<td class="left">
						<input class="inf" type="text" name="name" value="<?=htmlspecialchars($partner->name)?>">
					</td>
				</tr>
				<tr>
					<td class="right"><b>H1:</b></td>
					<td class="left">
						<input class="inf" type="text" name="h1" value="<?=htmlspecialchars($partner->h1)?>">
					</td>
				</tr>
				<tr>
					<td class="right"><b>Title:</b></td>
					<td class="left">
						<input class="inf" type="text" name="title" value="<?=htmlspecialchars($partner->title)?>">
					</td>
				</tr>
				<tr>
					<td class="right"><b>Description:</b></td>
					<td class="left">
						<textarea class="inf" style="height:70px;" name="metadesc"><?=$partner->metadesc?></textarea>
					</td>
				</tr>
				<tr>
					<td class="right"><b>Keywords:</b></td>
					<td class="left">
						<textarea class="inf" style="height:70px;" name="metakey"><?=$partner->metakey?></textarea>
					</td>
				</tr>
				<tr>
					<td class="right"><b>СПАМ:</b></td>
					<td class="left">
						<textarea class="inf" style="height:70px;" name="spam"><?=$partner->spam?></textarea>
					</td>
				</tr>
				<tr>
					<td class="right"><b>Ссылка:</b></td>
					<td class="left">
						<input class="inf" type="text" name="url" value="<?=htmlspecialchars($partner->url)?>">
					</td>
				</tr>
				<tr>
					<td class="right"><b>Изображение:</b></td>
					<td class="left">
						<div class="FM-image-box">
							<div class="i">
								<img class="FM-image" src="<?=htmlspecialchars($partner->cache)?>" alt="Image">
							</div>
							<input type="hidden" name="image" value="<?=htmlspecialchars($partner->image)?>">
							<br><a href="javascript:void(0)" class="FM-overview">обзор</a> | <a href="javascript:void(0)" class="FM-clear">очистить</a>
						</div>
					</td>
				</tr>
				<tr>
					<td class="right"><b>Текст:</b></td>
					<td class="left">
						<textarea class="tiny" rows="30" name="text"><?=$partner->text?></textarea>
					</td>
				</tr>
			</table>
		</div>
		
		<input type="hidden" name="id" value="<?=$partner->id?>">
		<input type="hidden" name="edit" value="">
	</form>
	<?php endif;?>

</div><!-- END BODY-->

<script> // APPLY
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