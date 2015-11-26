<div class="content">
	
	<!-- ##################################################################### CRUMBS-->
	<div class="crumbs">
	<?php if(count($crumbs)):?>
		<span  itemtype="http://data-vocabulary.org/Breadcrumb" itemscope="">
			<a itemprop="url" href="http://<?=SERVER?>">
				<span itemprop="title">Главная</span>
			</a>
		</span>
		&nbsp;/&nbsp;
		<?php $last = array_pop($crumbs);?>
		<?php foreach($crumbs as $k):?>
		<span  itemtype="http://data-vocabulary.org/Breadcrumb" itemscope="">
			<a itemprop="url" href="<?=$k['_url']?>">
				<span itemprop="title"><?=$k['name']?></span>
			</a>
		</span>
		&nbsp;/&nbsp;
	<?php endforeach;?>
		<span><?=$last['name']?></span>
	<?php endif;?>
	</div>
	<!-- ##################################################################### END CRUMBS-->
		
	<h1><?=$h1?></h1>
		
	<?php if ($this->session->userdata('recover')):?>
		<p>Новый пароль выслан на ваш E-mail</p>
		<?php $this->session->unset_userdata('recover');?>
	<?php else:?>
	<div class="form-box">
		<form method="post">
			<div class="form-pole">
				Ваш e-mail
				<br>
				<input type="text" name="email" value="">
			</div>
			
			<div class="form-pole">
				повторите символы
				<br>
				<table>
					<tr>
						<td>
							<input type="text" name="captcha" id="captcha" value="">
						</td>
						<td>
							<?=$captcha['image'];?>
						</td>
					</tr>
				</table>
				<div style="color:#ff0000;"><?=isset($error['captcha'])?$error['captcha']:'';?></div>
				<script>
				// CAPS для captcha
				$(function(){
					function valid(data){
						data = data.substr(0,6);
						data = data.toUpperCase();
						data = data.replace(/[^A-Z0-9]/, '');
						return data;
					}
					$('#captcha').focus(function(){
						$(this).val(valid($(this).val()));
					}).keyup(function(){
						$(this).val(valid($(this).val()));
					});
				});
				</script>
			</div>
			
			<div style="text-align:right;" class="form-pole">
				<br>
				<button class="btn" name="recover"><span>восстановить</span></button>
			</div>
			
		</form>
	</div>
	<?php endif;?>

</div>