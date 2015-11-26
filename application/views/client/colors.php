<div class="body">

	<h1 class="h1-style"><span><?=$h1?></span></h1>
	
	<!-- CRUMBS-->
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
	<!-- END CRUMBS-->
	

	<!-- FILTER -->
	<?php if (isset($filter_items_size) && $filter_items_size):?>
	<div class="" style="padding:20px 0;width:250px;margin:0 auto;">
		<form>
			<div class="form-group">
				<label>Фильтр размеров:</label>
				<select data-styler="" data-placeholder="" name="size" onchange="$(this).parents('form').submit()">
					<option value="0" selected>&nbsp;</option>
					<?php foreach($filter_items_size as $size):?>
					<option value="<?=$size->id?>" <?=$filter['size'] == $size->id ? 'selected' : '';?>><?=$size->name?>: <?=$size->prefix?></option>
					<?php endforeach;?>
				</select>
			</div>
		</form>
	</div>
	<?php endif;?>
	<!-- END FILTER-->
	
	
	<div class="row">
		<div class="col-1">
		</div>
	</div>
	
	
	<!-- PRODUCTS-->
	<?php if ($products):?>
	<ul class="goods-list">
		<?php foreach($products as $product):?>
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
	
	
	
	
	<!-- PRODUCT DISCOUNT -->
	<?php if ($products_discount):?>
		<div class="row">
		<div class="col-1">
			<h3 class="attetion"><span>АКЦИОННЫЕ ТОВАРЫ</span></h3>
			
			<div class="owl-carousel" data-owlCarusel="auto">
			<?php foreach ($products_discount as $k=>$v):?>
				<div class="item">
					<a class="goods<?=$v->hit ? ' hit' : ($v->new ? ' new' : '') ;?>" href="<?=$v->_url?>" title="<?=htmlspecialchars($v->name)?>">
						<span class="goods-image">
							<i title="скидка на <?=htmlspecialchars($v->name)?> <?=$v->discount*1?> %">  &minus; <?=$v->discount*1?> %</i>
							<img class="lazyOwl" data-src="<?=htmlspecialchars($v->image)?>" alt="<?=htmlspecialchars($v->name)?>">
						</span>
						<span class="goods-name"><?=$v->name?></span>
					</a>
				</div>
			<?php endforeach;?>
			</div>
			
		</div>
	</div>
	<?php endif;?>
	<!-- END PRODUCT DISCOUNT -->
	

	
	<!-- SPAM -->
	<div class="row">
		<div class="col-1">
			<div class="spam"><?=$spam?></div>
		</div>
	</div>
	
</div><!-- END BODY -->

















