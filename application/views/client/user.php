<div class="body">
	<h1 class="h1-style"><span><?=$h1?></span></h1>
	
	<!--############################################################### CRUMBS-->
	<ul class="crumbs">
		<li itemtype="http://data-vocabulary.org/Breadcrumb" itemscope="">
			<a itemprop="url" href="<?=SERVER?>">
				<span itemprop="title">Главная</span>
			</a>
		</li>
		<li> / </li>
		<?php $last = array_pop($crumbs);?>
		<?php foreach ($crumbs as $k):?>
		<li  itemtype="http://data-vocabulary.org/Breadcrumb" itemscope="">
			<a itemprop="url" href="<?=$k['_url']?>">
				<span itemprop="title"><?=$k['name']?></span>
			</a>
		</li>
		<li> / </li>
		<?php endforeach;?>
		<li><?=$last['name']?></li>
	</ul>
	<!--############################################################### END CRUMBS-->
	
	<div class="row">
		<div class="col-2">
			<table class="table text">
				<tr>
					<td>Ваше ФИО:</td>
					<td><b><?=$user->name;?></b></td>
				</tr>
				<tr>
					<td>E-mail:</td>
					<td><?=$user->email;?></td>
				</tr>
				<tr>
					<td>Телефон:</td>
					<td><?=$user->phone;?></td>
				</tr>
				<tr>
					<td>Город (Новая Почта):</td>
					<td><?=$user->np_city_name ? $user->np_city_name : ' - '?></td>
				</tr>
				<tr>
					<td>Отделение (Новая Почта):</td>
					<td><?=$user->np_address_ru ? $user->np_address_ru : ' - '?></td>
				</tr>
				<tr>
					<td></td>
					<td>
						<a class="btn btn-white" href="/user/edit">Изменить данные</a>
						<a class="btn btn-white" href="/user/password">Сменить пароль</a>
					</td>
				</tr>
			</table>
		</div>
		<div class="col-2">
			<style>
			.box-grey{
				padding:20px;
				background:rgba(245, 245, 245, 0.5);
			}
			</style>
			<div class="box-grey">
				<table class="table text">
					<tr>
						<td>
							Выполнено заказов (<b><?=$user->count_orders?></b>) на сумму:
							<br>
							<b><?=number_format($user->sum_orders, 2, ',', "'")?> грн.</b>
							<br>
							<br>
						</td>
						<td style="padding-left:20px;"><a class="underline" data-getorders="">История заказов</a></td>
					</tr>
					<tr>
						<td>Скидка по программе лояльности <b><?=$user->discount*1?>%</b></td>
						<td style="padding-left:20px;"><a class="underline"  data-bind="get-discounts">Условия программы лояльности</a></td>
					</tr>
				</table>
			</div>
		</div>
	</div>
	
	<div class="row">
		<div class="col-2"></div>
		<div class="col-2">
			<style>
			
			</style>
			<div class="box-grey">
				<h4>Отследить заказ НОВА ПОШТА:</h4>
				<form data-novaposhta="form" action="/novaposhta" method="post">
					<div class="form-group">
						<label>Номер накладной:</label>
						<input class="form-control" type="text" name="getNovaPoshta" value="">
					</div>
					<div class="form-group">
						<button class="btn btn-white" type="submit">отследить</button>
					</div>
				</form>
				<div class="box-result" data-novaposhta="result"></div>
			</div>
		</div>
	</div>
</div><!-- END BODY -->
		

<script> // GET ORDERS
$(document).on('click', '[data-getorders]', function(e){
	var D = $().dialog({
		width:'600px',
		maxHeight:'300px',
		title:'История заказов',
		drag:true
	}).dialog('load');
	
	$.post('/user', {getorders:''}, function(data){
		var html = '<div>'+
				'<style>.orders tr:nth-child(even){background:#eee;}</style>'+
				'<table class="table orders">'+
					'<tr>'+
						'<td class="small"><b>ID</b></td>'+
						'<td><b>Статус</b></td>'+
						'<td><b>Создан</b></td>'+
						'<td><b>Сумма</b></td>'+
						'<td><b>скидка</b></td>'+
						'<td><b>итого</b></td>'+
						'<td class="small"></td>'+
					'</tr>';
		$.each(data, function(){
			html += '<tr>'+
						'<td>'+this.id+'</td>'+
						'<td>'+this.status+'</td>'+
						'<td>'+this.date+'</td>'+
						'<td class="nowrap">'+(number_format(this.sum, 2, ',', "'"))+'грн.</td>'+
						'<td>'+this.discount*1+'%.</td>'+
						'<td class="nowrap">'+number_format((this.discount*1 ? (this.sum - this.sum * this.discount / 100) : this.sum), 2, ',', "'")+' грн.</td>'+
						'<td><a data-getorder="'+this.id+'" href="#getorder">cмотреть</a></td>'+
					'</tr>';
		});
		html += '</table>'+
			'<div>';
		
		D.dialog('content', $(html)).dialog('position').dialog('endLoad');
		D.data('dialog').dialog.find('[data-getorder]').on('click', function(e){
			e.preventDefault();
			var DD = $('<div class=""></div>').dialog({
				width:'600px',
				height:'auto',
				title:'Информация о заказе "'+ $(this).attr('data-getorder') +'"',
				drag:true
			}).dialog('load');
			
			$.post('/user', {gethistoryorder: $(this).attr('data-getorder')}, function(data){
				var html = '<div class="jspContainer" style="overflow-y: auto;max-height:350px;">'+
						'<style>.orders tr:nth-child(even){background:#f9f9f9;}</style>'+
						'<table class="table orders">'+
							'<tr>'+
								'<td class="small">№</td>'+
								'<td class="small"></td>'+
								'<td>название</td>'+
								'<td class="small">Размер</td>'+
								'<td class="small">Опт/роз</td>'+
								'<td class="small">Цена</td>'+
								'<td class="small">кол.</td>'+
								'<td class="small">Сумма</td>'+
							'</tr>';
				
				$.each(data, function(i){
					html += '<tr>'+
								'<td>'+(i+1)+'</td>'+
								'<td class="small"><img width="50"  src="/img/products/'+this.id_product+'/'+this.id_product+'_82_82.jpg"></td>'+
								'<td class="left"><a href="/'+this.url+'/p'+this.pid+'/">'+this.pname+'</a></td>'+
								'<td class="">'+this.size+'</td>'+
								'<td class="">'+(+this.type?'опт.':'роз.')+'</td>'+
								'<td class="center">'+this.price+'</td>'+
								'<td class="">'+this.quantity+'</td>'+
								'<td class="">'+(number_format(this.subtotal, 2, ',', "'"))+'</td>'+
							'</tr>';
				});
				
				html += '</table>'+
					'</div>';
				
				DD.dialog('content', $(html)).dialog('endLoad');
			}, 'json');
		});
		
		
	}, 'json');
});
</script>












