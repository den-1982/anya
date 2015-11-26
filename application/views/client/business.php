<div class="body">
	<h1 class="h1-style"><span><?=$h1?></span></h1>
	
	<div class="row">
		<div class="col-2">
			<div style="padding:20px;">
				<form action="" method="post" data-bind="callback">
					<div class="form-group">
						<label><b class="c-red">*</b> Ваше имя:</label>
						<input class="form-control" type="text" name="name" value="" placeholder="">
						<div class="form-error"></div>
					</div>
					
					<div class="form-group">
						<label>Страна.:</label>
						<input class="form-control" type="text" name="country" value="" placeholder="">
						<div class="form-error"></div>
					</div>
					
					<div class="form-group">
						<label>Город.:</label>
						<input class="form-control" type="text" name="city" value="" placeholder="">
						<div class="form-error"></div>
					</div>
					
					<div class="form-group">
						<label><b class="c-red">*</b> Моб.телефон:</label>
						<input class="form-control" data-mask="phone" type="text" name="phone" value="" placeholder="">
						<div class="form-error"></div>
					</div>
					
					<div class="form-group">
						<label><b class="c-red">*</b> E-mail:</label>
						<input class="form-control" type="text" name="email" value="" placeholder="">
						<div class="form-error"></div>
					</div>
					
					<div class="form-group">
						<label>Род деятельности:</label>
						<input class="form-control" type="text" name="subject" value="" placeholder="">
						<div class="form-error"></div>
					</div>	
					
					<div class="form-group">
						<label>
							Ваше представление о сотрудничестве или как бы вы
							<br>
							желали сотрудничать с Crystalline:
						</label>
						<textarea class="form-control-textarea" name="message"></textarea>
						<div class="form-error"></div>
					</div>
					
					<div class="form-group">
						<input type="hidden" name="sendBusiness" value="">
						<button class="btn btn-pink" type="submit" name="sendBusiness" value="">Отправить сообщение</button>
					</div>
				</form>
				<script> // CALLBACK
				$(document).on('submit','[data-bind="callback"]',function(e){
					e.preventDefault();
					var load = $('<div>').addClass('load').css({position:'static',backgroundColor:'transparent'}).height($(this).height()); 
						
					$(this).replaceWith(load);

					$.post('', $(this).serialize(), function(data){
						load.replaceWith('<h4 style="padding:50px;text-align:center;">Сообщение отправлено.</h4>');
					}, 'json');
				});
				</script>
			</div>
		</div>
		<div class="col-2">
			<div style="padding: 35px 20px 20px 20px; font-family: Open Sans Condensed, sans-serif, arial; font-size: 20px;">
				<p>Компания &laquo;Crystalline&raquo; является прямым поставщиком страз и фурнитуры от известных мировых компаний. Мы заинтересованы в сотрудничестве с дилерами, частными предпринимателями и оптовыми покупателями.</p>
				<p>Нашим партнерам мы предлагаем гибкие цены, информационную поддержку, своевременные поставки продукции в необходимых объемах.</p>
				<p>Давайте зарабатывать вместе!</p>
			
				<?=$text?>
			</div>
		</div>
	</div>
	
	<!-- SPAM -->
	<div class="row">
		<div class="col-1">
			<div class="spam"><?=$spam?></div>
		</div>
	</div>

</div><!-- END BODY -->
