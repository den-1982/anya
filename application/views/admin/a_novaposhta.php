<div class="body">

<!--ALL-->
	<?php if ($act == 'all'):?>
	<h1 class="title"><?=$h1?></h1>
	
	<div class="nav clearfix">
		<div class="fleft"></div>
		<div class="fright">
			<form id="form" action="" method="POST" enctype="multipart/form-data">
				<a class="button blue" style="margin-left:5px;" data-bind="refresh-db-np">Обновить базу данных</a>
				<input type="hidden" name="edit" value="">
			</form>
		</div>
	</div>
	
	<div class="toggle-box">
		<a class="bookmark-toggle activ" data-name="data" href="#data">Общие</a>
	</div>
	
	<div class="bookmark activ" data-id="data">
		<table class="table-1" data-scroll="head">
			<tr>
				<td class="small"><b>Города</b>:</td>
				<td class="left">
					<select data-select="" name="city" data-np="getWarenListNovaPoshta">
						<option value="0"> - Выбрать - </option>
						<?php foreach ($cities['response'] as $city):?>
						<option value="<?=$city->city_id?>"><?=$city->cityRu?></option>
						<?php endforeach;?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="small"><b>Отделения</b>:</td>
				<td class="left">
					<select data-select="" name="wareId" data-np="warenList">
						<option value="0"> - Выбрать - </option>
					</select>
				</td>
			</tr>
		</table>
	</div>
	<?php endif;?>

</div><!--END ID=BODY-->

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

<script> // NOVA-POSHTA 
$('[data-np="getWarenListNovaPoshta"]').on( "selectmenuchange", function(){
	var l = $('<div>').addClass('load');
	$(document.body).append(l);
	$.post('',{getWarenListNovaPoshta:$(this).val()},function(data){
		var warenList = $('[data-np="warenList"]').html('<option value="0"> - Выбрать офис - </option>');

		$(data.response).each(function(){
			warenList.append('<option value="'+ this.wareId +'">'+ this.addressRu +'</option>');
		});
		warenList.selectmenu('refresh');
		
		l.remove();
	}, 'json');
	
});
</script>

<script> // REFRESH DB NOVA-POSHTA 
$('[data-bind="refresh-db-np"]').on( "click", function(){
	var l = $('<div>').addClass('load');
	$(document.body).append(l);
	
	$.post('', {refresh:''}, function(data){
		if (data){
			$('<p>'+data+'</p>').dialog({
				height:'auto',
				width:600,
				drag: true,
				title:'Ошибка'
			});
		}else{
			window.location.reload(true);
		}
		
		l.remove();
	}, 'json');
	
});
</script>

