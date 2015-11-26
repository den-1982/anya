<div class="body"><!-- ALL USERS -->	<?php if ($act == 'all'):?>	<h1><?=$h1?></h1>		<div class="nav clearfix">		<div class="fleft">			<form>				<select data-select="auto-submit" name="sort" style="display:none;">					<option value="0" <?=$sort == 0 ? 'selected' : '';?>>по общей сумме</option>					<option value="1" <?=$sort == 1 ? 'selected' : '';?>>по количеству заказов</option>					<option value="2" <?=$sort == 2 ? 'selected' : '';?>>по алфавиту</option>						</select>			</form>		</div>		<div class="fright">			<a style="margin-left:5px;" class="button green" href="<?=$path?>?add">Добавить клиента</a>		</div>	</div>		<table class="table-1" data-scroll="head">		<thead>			<tr>				<td class="small">№</td>				<td class="small">Cart</td>				<td class="">ФИО</td>				<td class="small nowrap">Общая сумма покупок</td>				<td class="small">Скидка %</td>				<td class="small">e-mail</td>				<td class="small">Телефон</td>				<td class="small"></td>				<td class="small"></td>				<td class="small"></td>			</tr>		</thead>		<thead>			<tr>				<td></td>				<td>					<input class="inf" data-search-input="cart" type="text" value="">				</td>				<td>					<input class="inf" data-search-input="name" type="text" value="">				</td>				<td></td>				<td></td>				<td>					<input class="inf" data-search-input="email" type="text" value="">				</td>				<td>					<input class="inf" data-search-input="phone" data-mask="_phone" type="text" value="">				</td>				<td></td>				<td></td>				<td></td>			</tr>		</thead>		<tbody>		<?php $i=1; foreach($users as $user):?>			<tr data-search-row="">				<td><?=$i++;?></td>				<td data-search-col="cart"><?=$user->user_cart_discount;?></td>				<td class="left" data-search-col="name">					<?=$user->name?>					<span class="c-grey">(заказов: <?=$user->allcount?>)</span>				</td>				<td class="left nowrap"><?=number_format($user->allsumm, 2, ",", "'")?> грн.</td>				<td class="left">					<select data-select="" data-discount="<?=$user->id?>">						<option value="0"> - Выбрать скидку - </option>						<?php foreach ($discounts as $item):?>						<option value="<?=$item->id?>" <?=$item->id == $user->discount? ' selected' : '';?>><?=$item->name.' - '.(int)$item->percent?>%</option>						<?php endforeach;?>					</select>				</td>				<td class="left nowrap" data-search-col="email"><?=$user->email?></td>				<td class="left nowrap" data-search-col="phone"><?=$user->phone?></td>				<td>					<a class="button blue" href="/admin/orders/user/<?=$user->id?>" title="История заказов <?=htmlspecialchars($user->name)?>">история</a>				</td>				<td>					<a class="link_edit" title="Редактировать данные клиента" href="/admin/user?edit=<?=$user->id?>"></a>				</td>				<td>					<a class="link_del" data-delete="клиента <b><?=htmlspecialchars($user->name)?></b>" href="<?=$path?>?delete=<?=$user->id?>" title="удалить клиента"></a>				</td>			</tr>		<?php endforeach;?>		</tbody>	</table>	<?php endif;?>		<?php if ($act == 'add'):?>	<h1><?=$h1?></h1>		<div class="nav clearfix">		<div class="fleft"></div>		<div class="fright">			<a style="margin-left:5px;" class="button blue" onclick="$('#form').submit()" >Сохранить</a>			<a style="margin-left:5px;" class="button black" href="<?=$path?>?parent=<?=$parent?>">Отмена</a>		</div>	</div>		<form id="form" action="<?=$path?>" method="POST" enctype="multipart/form-data">		<div class="toggle-box">			<a class="bookmark-toggle activ" data-name="data" href="#data">Общие</a>		</div>				<div class="bookmark activ" data-id="data">			<table class="table-1">				<tr>					<td class="small right"><b>Ф.И.О.:</b></td>					<td class="left">						<input class="inf" type="text" name="name" value="">					</td>				</tr>				<tr>					<td class="right"><b>E-mail:</b></td>					<td class="left">						<input class="inf" type="text" name="email" value="">					</td>				</tr>				<tr>					<td class="right"><b>Телефон:</b></td>					<td class="left">						<input class="inf" data-mask="_phone" type="text" name="phone" value="">					</td>				</tr>				<tr>					<td class="right"><b>Скидка:</b></td>					<td class="left">						<select data-select="" name="discount">							<option value="0"> - Выбрать скидку - </option>							<?php foreach ($discounts as $item):?>							<option value="<?=$item->id?>"><?=$item->name.' - '.(int)$item->percent?>%</option>							<?php endforeach;?>						</select>					</td>				</tr>				<tr>					<td class="right nowrap"><b>Карта клиента:</b></td>					<td class="left">						<table class="table-1">							<thead>								<tr>									<td>Код карты</td>									<td>Скидка (%)</td>								</tr>							</thead>							<tbody>								<tr>									<td>										<input class="inf" data-mask="not-space" type="text" name="user_cart_discount" value="">									</td>									<td>										<input class="inf" data-mask="price" type="text" name="user_cart_percent" value="">									</td>								</tr>							</tbody>						</table>					</td>				</tr>				<tr>					<td class="right nowrap"><b>Новая Почта:</b></td>					<td class="left">						<table class="table-1">							<thead>								<tr>									<td>Город</td>									<td>Отделение</td>									<td></td>								</tr>							</thead>							<tbody>								<tr>									<td class="small">										<select data-select="" name="city" data-np="getWarenListNovaPoshta">											<option value="0"> - Выбрать город - </option>											<?php foreach ($cities['response'] as $city):?>											<option value="<?=$city->city_id?>"><?=$city->cityRu?></option>											<?php endforeach;?>										</select>									</td>									<td class="small">										<select data-select="" name="wareId" data-np="warenList">											<option value="0"> - Выбрать офис - </option>										</select>									</td>									<td></td>								</tr>							</tbody>						</table>					</td>				</tr>			</table>		</div>				<input type="hidden" name="add" value="">	</form>	<?php endif;?>			<?php if ($act == 'edit'):?>	<h1><?=$h1?></h1>		<div class="nav clearfix">		<div class="fleft"></div>		<div class="fright">			<a style="margin-left:5px;" class="button orange" data-form-apply="" >Применить</a>			<a style="margin-left:5px;" class="button blue" onclick="$('#form').submit()" >Сохранить</a>			<a style="margin-left:5px;" class="button black" href="<?=$path?>?parent=<?=$parent?>">Отмена</a>		</div>	</div>		<form id="form" action="<?=$path?>" method="POST" enctype="multipart/form-data">		<div class="toggle-box">			<a class="bookmark-toggle activ" data-name="data" href="#data">Общие</a>		</div>				<div class="bookmark activ" data-id="data">			<table class="table-1">				<tr>					<td class="small right"><b>Ф.И.О.:</b></td>					<td class="left">						<input class="inf" type="text" name="name" value="<?=htmlspecialchars($user->name)?>">					</td>				</tr>				<tr>					<td class="right"><b>E-mail:</b></td>					<td class="left">						<input class="inf" type="text" name="email" value="<?=htmlspecialchars($user->email)?>">					</td>				</tr>				<tr>					<td class="right"><b>Телефон:</b></td>					<td class="left">						<input class="inf" data-mask="_phone" type="text" name="phone" value="<?=$user->phone?>">					</td>				</tr>				<tr>					<td class="right"><b>Скидка:</b></td>					<td class="left">						<select data-select="" name="discount">							<option value="0"> - Выбрать скидку - </option>							<?php foreach ($discounts as $item):?>							<option value="<?=$item->id?>" <?=$item->id == $user->discount? ' selected' : '';?>><?=$item->name.' - '.(int)$item->percent?>%</option>							<?php endforeach;?>						</select>					</td>				</tr>				<tr>					<td class="right nowrap"><b>Карта клиента:</b></td>					<td class="left">						<table class="table-1">							<thead>								<tr>									<td>Код карты</td>									<td>Скидка (%)</td>								</tr>							</thead>							<tbody>								<tr>									<td>										<input class="inf" data-mask="not-space" type="text" name="user_cart_discount" value="<?=$user->user_cart_discount?>">									</td>									<td>										<input class="inf" data-mask="price" type="text" name="user_cart_percent" value="<?=$user->user_cart_percent?>">									</td>								</tr>							</tbody>						</table>					</td>				</tr>				<tr>					<td class="right nowrap"><b>Новая Почта:</b></td>					<td class="left">						<table class="table-1">							<thead>								<tr>									<td>Город</td>									<td>Отделение</td>									<td></td>								</tr>							</thead>							<tbody>								<tr>									<td class="small">										<select data-select="" name="city" data-np="getWarenListNovaPoshta">											<option value="0"> - Выбрать город - </option>											<?php foreach ($cities['response'] as $city):?>											<option value="<?=$city->city_id?>" <?=$user->city == $city->city_id ? ' selected' : '';?>><?=$city->cityRu?></option>											<?php endforeach;?>										</select>									</td>									<td class="small">										<select data-select="" name="wareId" data-np="warenList">											<option value="0"> - Выбрать офис - </option>											<?php foreach ($warenList['response'] as $ware):?>											<option value="<?=$ware->wareId?>" <?=$user->wareId == $ware->wareId ? ' selected' : '';?>><?=$ware->addressRu?></option>											<?php endforeach;?>										</select>									</td>									<td></td>								</tr>							</tbody>						</table>					</td>				</tr>			</table>		</div>				<input type="hidden" name="id" value="<?=$user->id?>">		<input type="hidden" name="edit" value="">	</form>	<?php endif;?>	</div><!-- END BODY --><script> // APPLY$(function(){	$(document).on('click', '[data-form-apply]', function(e){		$(document.body).append($('<div>').addClass('_load'));				if (tinyMCE && tinyMCE.editors){			$(tinyMCE.editors).each(function(){				$(this.getElement()).html(this.getContent());			});		}				AP.init('', $('#form')[0], function(){				location.reload(true);		});		return false;			});});</script><script> // SEARCH;$(function(){	var 	rows = $('[data-search-row]'),	inp = $('[data-search-input]');		inp.val('').on('keyup', function(){		var _this = $(this),			_val = _this.val(),			_name = _this.attr('data-search-input');				// для телефона		if (_name == 'phone'){			_val = _val.replace(/[^0-9]*/g, '');		}				rows.find('[data-search-col="'+_name+'"]').each(function(){			var text = $.trim(this.textContent),				row = $(this).parents('[data-search-row]');						// для телефона			if (_name == 'phone'){				text = text.replace(/[^0-9]*/g, '');			}						if ( text.match(new RegExp('^'+ _val + '.*?', 'i')) ){				row.css({display:'table-row'});			}else{				row.css({display:'none'});			}		});	});});</script><script> // NOVA-POSHTA $('[data-np="getWarenListNovaPoshta"]').on( "selectmenuchange", function(){	var l = $('<div>').addClass('load');	$(document.body).append(l);	$.post('',{getWarenListNovaPoshta:$(this).val()},function(data){		var warenList = $('[data-np="warenList"]').html('<option value="0"> - Выбрать офис - </option>');		$(data.response).each(function(){			warenList.append('<option value="'+ this.wareId +'">'+ this.addressRu +'</option>');		});		warenList.selectmenu('refresh');				l.remove();	}, 'json');	});</script><script> // DISCOUNT $('[data-discount]').on( "selectmenuchange", function(e, ui){	var l = $('<div>').addClass('load');	$(document.body).append(l);		var user = $(this).attr('data-discount');	var discount = $(this).find('option:selected').val();		$.post('', {setdiscount:'', id_discount:discount, id_user:user}, function(data){		l.remove();	});});</script>