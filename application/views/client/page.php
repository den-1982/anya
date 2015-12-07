<div class="body">
	
	<?php if (isset($page->slider) && $page->slider):?>
	<div data-camera="page">
		<?php foreach ($page->slider as $k):?>
		<div data-src="<?=htmlspecialchars($k->image)?>" data-thumb="<?=htmlspecialchars($k->image)?>" data-link="<?=htmlspecialchars($k->link)?>"></div>
		<?php endforeach;?>
	</div>
	<script>
	$(function(){
		$('[data-camera="page"]').camera({
			height				: "410px",
			fx					: 'simpleFade',
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
			<a itemprop="url" href="<?=$k['_url']?>">
				<span itemprop="title"><?=$k['name']?></span>
			</a>
		</li>
		<li> / </li>
		<?php endforeach;?>
		<li itemprop="url"><span itemprop="title"><?=$last['name']?></span></li>
	</ul>
	<!-- END CRUMBS-->
	
	<div class="row">
		<div class="col-1">
			<?php if (isset($pages[$page->id])) foreach ($pages[$page->id] as $i):?>
			<div style="overflow:hidden;height:140px;margin:0 0 20px 0;border-bottom:1px dotted #E6F6FF;">
				<div style="overflow:hidden;">
					<div style="width:150px;float:left;margin:0 10px 5px 0;">
						<a href="<?=htmlspecialchars($i->_url)?>">
							<img src="<?=htmlspecialchars($i->image)?>" alt="<?=htmlspecialchars($i->name)?>">
						</a>
					</div>
					<div style="padding:0 0 10px 0;">
						<div style="padding:0 0 10px;">
							<a style="font-size:17px;" href="<?=htmlspecialchars($i->_url)?>"><?=$i->name?></a>
						</div>
						<?=$i->metadesc?>
					</div>
				</div>
			</div>
			<?php endforeach;?>
		</div>
	</div>
	
	
	<div class="row">
		<div class="col-1">
			<?=$text?>
		</div>
	</div>
	
	<div class="row">
		<div class="col-1">
			<div class="spam"><?=$spam?></div>
		</div>
	</div>

</div><!-- END BODY -->