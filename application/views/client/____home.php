<div class="body">

	<?php if ($home->slider):?>
	<div data-camera="home">
		<?php foreach ($home->slider as $k):?>
		<div data-src="<?=htmlspecialchars($k->image)?>" data-thumb="<?=htmlspecialchars($k->image)?>" data-link="<?=htmlspecialchars($k->link)?>">
			<?php if ($k->h1):?>
			<div class="camera_caption fadeFromBottom">
				<h2><?=htmlspecialchars($k->h1)?><br>&bull;</h2>
				<em><?=htmlspecialchars($k->text)?></em>
			</div>
			<?php endif;?>
		</div>
		<?php endforeach;?>
		<?php if (count($home->slider) == 1):?>
		<div data-src="<?=htmlspecialchars($k->image)?>" data-thumb="<?=htmlspecialchars($k->image)?>" data-link="<?=htmlspecialchars($k->link)?>"></div>
		<?php endif;?>
	</div>
	<script>
	$(function(){
		$('[data-camera="home"]').camera({
			//height				: "500px",
			height				: "38%",
			fx					: "simpleFade",
			loader				: "none",
			navigation			: false,
			navigationHover		: true,
			pagination			: false
		});
	});
	</script>
	<?php endif;?>


	<h1 class="h1-style"><span><?=$h1?></span></h1>


	<!--  CATECORIES -->
	<?php if (isset($categories[0])):?>
	<ul class="category-list">
	<?php foreach($categories[0] as $category):?>
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



	<!-- PRODUCT DISCOUNT -->
	<?php if ($products_discount):?>
	<div class="row">
		<div class="col-1">
			<h3 class="attetion"><span>АКЦИОННЫЕ ТОВАРЫ</span></h3>
			<div class="owl-carousel" data-owlCarusel="auto">
			<?php foreach ($products_discount as $k=>$v):?>
				<div class="item">
					<a class="goods<?=$v->hit ? ' hit' : ($v->new ? ' new' : '') ;?>" href="<?=htmlspecialchars($v->_url)?>" title="<?=htmlspecialchars($v->name)?>">
						<span class="goods-image">
							<i title="скидка на <?=htmlspecialchars($v->name)?> <?=$v->discount*1?> %"> &minus; <?=$v->discount*1?> %</i>
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

	
	<!-- NEWS / ARTICLES -->
	<div class="row">
		<div class="col-2">
			<h3 class="h1-style"><span><a href="/news">Новости</a></span></h3>
			<div data-camera="news">
				<?php 
				$i = 0; 
				foreach($pages as $k):?>
				<?php if($i > 3) break;?>
				<?php if($k->type != 'news') continue; ?>
				<div data-src="<?=htmlspecialchars($k->image)?>" data-thumb="<?=htmlspecialchars($k->image)?>" data-link="<?=htmlspecialchars($k->_url)?>">
					<?php if ($k->h1):?>
					<div class="camera_caption fadeFromBottom">
						<h2><?=htmlspecialchars($k->h1)?><br>&bull;</h2>
						<em><?=htmlspecialchars($k->metadesc)?></em>
					</div>
					<?php endif;?>
				</div>
				<?php 
				$i++;
				endforeach;?>
				<?php if (count($pages) == 1):?>
				<div data-src="<?=htmlspecialchars($k->image)?>" data-thumb="<?=htmlspecialchars($k->image)?>" data-link="<?=htmlspecialchars($k->_url)?>"></div>
				<?php endif;?>
			</div>
			<script>
			$(function(){
				$('[data-camera="news"]').camera({
					height:"400px",
					fx:'simpleFade',
					loader				: 'none',
					navigation			: false,
					navigationHover		: true,
					pagination			: false
				});
			});
			</script>
		</div>

		<div class="col-2">
			<h3 class="h1-style"><span><a href="/articles">Статьи</a></span></h3>
			<div data-camera="articles">
				<?php foreach($pages as $k):?>
				<?php if ($k->type != 'articles') continue; ?>
				<div data-src="<?=htmlspecialchars($k->image)?>" data-thumb<?=htmlspecialchars($k->image)?>" data-link="<?=htmlspecialchars($k->_url)?>">
					<?php if ($k->h1):?>
					<div class="camera_caption fadeFromBottom">
						<h2><?=htmlspecialchars($k->h1)?><br>&bull;</h2>
						<em><?=htmlspecialchars($k->metadesc)?></em>
					</div>
					<?php endif;?>
				</div>
				<?php endforeach;?>
				<?php if (count($pages) == 1):?>
				<div data-src="<?=htmlspecialchars($k->image)?>" data-thumb="<?=htmlspecialchars($k->image)?>" data-link="<?=htmlspecialchars($k->_url)?>"></div>
				<?php endif;?>
			</div>
			<script>
			$(function(){
				$('[data-camera="articles"]').camera({
					height:"400px",
					fx:'simpleFade',
					loader				: 'none',
					navigation			: false,
					navigationHover		: true,
					pagination			: false
				});
			});
			</script>
		</div>

	</div>

	<!-- SPAM -->
	<div class="row">
		<div class="col-1">
			<div class="spam"><?=$spam?></div>
		</div>
	</div>

</div><!-- END BODY -->



