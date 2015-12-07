<div class="body">

	<h1 class="h1-style"><span><?=$h1?></span></h1>
	
	<?php if($partnerships):?>
	<style>
	.box-partnerships{
		margin:20px 0;
		padding:0 0 20px 0;
		border-bottom:1px dotted #ccc;
		overflow:hidden;
	}
	.box-partnerships-left{
		float:left;
		width:200px;
		height:200px;
		margin:0 10px 10px 0;
	}
	.box-partnerships-left img{
		max-width:100%;
		max-height:100%;
	}
	.box-partnerships-right{
		margin:0 0 0 220px;
	}
	.box-partnerships-link{
		display:block;
		margin:0 0 20px;
		font-size:22px;
	}
	</style>
	
	<div class="row">
		<div class="col-1">
		<?php foreach($partnerships as $k):?>
			<div class="box-partnerships">
				<div class="box-partnerships-left">
					<img src="<?=$k->image?>" alt="">
				</div>
				<div class="box-partnerships-right">
					<a class="box-partnerships-link" class="" name="part_<?=$k->id?>" rel="nofollow" target="_blank" href="<?=$k->url?>"><?=$k->name?></a>
					<?=$k->text?>
				</div>
			</div>
		<?php endforeach;?>
		</div>
	</div>
	<?php endif;?>
	
</div><!-- END BODY -->