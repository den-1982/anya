<div class="body">
	<h1 class="h1-style"><span><?=$h1?></span></h1>
	
	
	<!--  CATECORIES -->
	<?php if ($search_categories):?>
	<h3 class="attetion"><span>Категории</span></h3>
	<ul class="category-list">
	<?php foreach($search_categories as $category):?>
		<li>
			<a class="category" href="<?=htmlspecialchars($category->_url)?>" title="<?=htmlspecialchars($category->name)?>">
				<img src="<?=htmlspecialchars($category->image)?>" alt="<?=htmlspecialchars($category->name)?>">
				<?php if ($category->discount*1):?>
				<span class="category-discount" title="скидка <?=$category->discount*1?> %"> &minus; <?=$category->discount*1?> %</span>
				<?php endif;?>
				<span class="category-text"><?=$category->name?></span>
				<span class="category-overlay"></span>
			</a>
		</li>
	<?php endforeach;?>
	</ul>
	<?php endif;?>
	<!--  END CATECORIES -->
	
	<!-- PRODUCTS-->
	<?php if ($search_products):?>
	<h3 class="attetion blue"><span>Товары</span></h3>
	<ul class="goods-list">
		<?php foreach($search_products as $product):?>
		<li>
			<a class="goods<?=$product->hit ? ' hit' : ($product->new ? ' new' : '') ;?>" href="<?=htmlspecialchars($product->_url)?>" title="<?=htmlspecialchars($product->name)?>">
				<span class="goods-image">
					<?php if ($product->discount*1):?>
					<i title="скидка на <?=htmlspecialchars($product->name)?> <?=$product->discount*1?> %"> &minus; <?=$product->discount*1?> %</i>
					<?php endif;?>
					<img src="<?=htmlspecialchars($product->image)?>" alt="<?=htmlspecialchars($product->name)?>">
				</span>
				<span class="goods-name"><?=$product->name?></span>
			</a>
		</li>
		<?php endforeach;?>
	</ul>
	<?php endif;?>
	<!-- END PRODUCTS-->
	
	
</div><!-- END BODY -->