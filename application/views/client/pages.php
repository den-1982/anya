<div class="body">
	
	<?php if (isset($page->slider) && $page->slider):?>
	<div data-camera="page">
		<?php foreach ($page->slider as $k):?>
		<div data-src="<?=$k->img?>" data-thumb="<?=$k->img?>" data-link="<?=$k->link?>"></div>
		<?php endforeach;?>
		<?php if (count($page->slider) == 1):?>
		<div data-src="<?=$k->img?>" data-thumb="<?=$k->img?>" data-link="<?=$k->link?>"></div>
		<?php endif;?>
	</div>
	<script>
	$(function(){
		$('[data-camera="page"]').camera({
			height				:"410px",
			fx					:'simpleFade',
			loader				: 'none',	
			navigation			: false,
			navigationHover		: true,
			pagination			: false
		});
	});
	</script>
	<?php endif;?>
	
	
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
			<a itemprop="url" href="<?=$k['url']?>">
				<span itemprop="title"><?=$k['name']?></span>
			</a>
		</li>
		<li> / </li>
		<?php endforeach;?>
		<li><?=$last['name']?></li>
	</ul>
	<!-- END CRUMBS-->
	
<?php if (isset($page) && $page):?>
		
	<!-- TEXT -->
	<div class="row">
		<div class="col-1">
			<?=$text?>
		</div>
	</div>
	
	<!-- SPAM -->
	<div class="row">
		<div class="col-1">
			<div class="spam"><?=$spam?></div>
		</div>
	</div>
	
<?php else:?>
	
	<div class="row">
		<div class="col-1">
			<?php foreach($pages as $i):?>
			<?php if($i->type != $type) continue;?>
			<div style="overflow:hidden;height:140px;margin:0 0 20px 0;border-bottom:1px dotted #E6F6FF;">
				<div style="overflow:hidden;">
					<div style="padding:0 0 5px;"><?=date('Y-m-d', $i->date)?></div>
					<div style="width:150px;float:left;margin:0 10px 5px 0;">
						<a href="/<?=$i->type?>/<?=$i->url?>">
							<img src="/img/news-articles/<?=$i->id?>/<?=$i->id?>_82_82.jpg" alt="">
						</a>
					</div>
					<div style="padding:0 0 10px 0;">
						<div style="padding:0 0 10px;">
							<a style="font-size:17px;" href="/<?=$i->type?>/<?=$i->url?>"><?=$i->name?></a>
						</div>
						<?=$i->metadesc?>
					</div>
				</div>
			</div>
			<?php endforeach;?>
		</div>
	</div>
	
<?php endif;?>
	
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
							<i title="скидка на <?=htmlspecialchars($v->name)?> <?=$v->discount*1?> %"> &minus; <?=$v->discount*1?> %</i>
							<img class="lazyOwl" data-src="/img/products/<?=$v->id?>/<?=$v->id?>_150_150.jpg" alt="<?=htmlspecialchars($v->name)?>">
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
	
</div><!-- END BODY -->






