<div class="body">

<!--ALL-->
	<?php if ($act == 'all'):?>
	<h1 class="title"><?=$h1?></h1>
	
	<div class="nav">
		<div class="fleft"></div>
		<div class="fright">
			<form id="form" action="" method="POST" enctype="multipart/form-data">
				<a class="button blue" data-form-apply="">Сделать сверку и сообщить о появлениии товаров</a>
				<input type="hidden" name="edit" value="">
			</form>
		</div>
	</div>
	
	
	<table class="table-1" data-scroll="head">
		<thead>
			<tr>
				<td class="small">№</td>
				<td class="small"></td>
				<td>Название</td>
				<td class="small">Размер</td>
				<td class="small"></td>
			</tr>
		</thead>
		<tbody data-sortable="body">
			<?php $i=1; foreach($waitListProducts as $k):?>
			<tr>
				<td><?=$i++?></td>
				<td>
					<a class="image" href="<?=htmlspecialchars($k->image)?>">
						<img onerror="this.src = '/img/i/loading_mini.gif'" src="<?=htmlspecialchars($k->image)?>">
					</a>
				</td>
				<td class="left">
					<a href="/admin/products?update=<?=$k->id?>"><?=$k->name?></a>
				</td>
				<td class="nowrap"><b><?=$k->sizeName?></b> <?=$k->sizePrefix?></td>
				<td>
					<a title="удалить" class="link_del" data-delete="Уведомление о появлении <?=htmlspecialchars($k->name)?>" href="<?=$path?>?delete=<?=$k->id?>&id_size=<?=$k->id_size?>"></a>
				</td>
			</tr>
			<?php endforeach;?>
		</tbody>
	</table>
	<?php endif;?>

</div><!--END ID=BODY-->

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