		<div class="cbt" data-cart="bottom">
			<div class="cbt-head">
				<div class="cbt-head-left">
					<span>
						Условия программы лояльности от 1000 грн. 
						<a class="show-discounts" data-bind="get-discounts" href="javascript:void(0)">Смотреть детали.</a>
					</span>
				</div>
				<div class="cbt-toggle" data-cart="toggle">
					<a class="cbt-toggle-open" href="/cart">Корзина</a>
					<a class="cbt-toggle-close" href="/cart">Закрыть</a>
				</div>
				<div class="cbt-head-right">
					<span class="inline-block">Моя корзина: товаров (<span data-cart="count-items"><?=$cart['cnt_items']?></span>) / </span>
					<span class="inline-block">скидка: <span data-cart="discount"><?=isset($user->discount) ? $user->discount*1 : 0?></span> % / </span>
					<span class="inline-block">Итого: <span data-cart="cart-total"><?=$cart['cart_total']?></span> грн. </span>
				</div>
			</div>

			<div class="cbt-body" data-cart-body="bottom"><?=$cart['html_bottom']?></div>
			
			<script>
			$(function(){
				$('[data-cart-bt="owlCarusel"]').owlCarousel({singleItem:true});
			});
			</script>
		</div><!-- END CART BOTTOM-->
		
		<footer class="footer">
			<div class="row">
				<ul class="navbar-footer">
					<?php if (isset($pages[0])) foreach ($pages[0] as $k):?>
					<li>
						<a class="has-child" href="<?=htmlspecialchars($k->_url)?>"><?=$k->name?></a>
						<?php if (isset($pages[$k->id])):?>
						<ul class="navbar-child">
							<?php foreach ($pages[$k->id] as $i):?>
							<li>
								<a class="" href="<?=htmlspecialchars($i->_url)?>"><?=$i->name?></a>
							</li>
							<?php endforeach;?>
						</ul>
						<?php endif;?>
					</li>
					<?php endforeach;?>	

					<li>
						<a class="has-child" href="/partners/">Наши партнеры</a>
						<ul class="navbar-child">
							<?php foreach($partners as $k):?>
							<li>
								<a class="" href="<?=htmlspecialchars($k->_url)?>"><?=$k->name?></a>
							</li>
							<?php endforeach;?>
						</ul>
					</li>
					<li>
						<span class="has-child">Ищите нас</span>
						<ul class="navbar-child">
							<li>
							<?php foreach($settings->social as $k=>$v):?>
								<a class="soc soc-<?=$k?>" href="<?=$v?>" target="_blank"></a>
							<?php endforeach;?>
							</li>
						</ul>
					</li>
				</ul>
				<script>
				$('.navbar-footer .has-child').click(function(e){
					e.preventDefault();
					$(this).toggleClass('activ');
				});
				</script>
				
				<div class="copy">
					<span>&copy; 2012 - <?=date('Y', time())?> Интернет-магазин «Crystalline»</span>
				</div>
				
			</div>
		</footer><!-- END FOOTER-->
		
	</div><!-- END WRAPPER-->
</body>
</html>