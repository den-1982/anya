		<h1><?=$h1?></h1>

		<table class="tab-size">
			<tr>
				<td><b>Код размера</b></td>
				<td><b>Размер в мм</b></td>
				<td><b>Количество шт. в уп. (опт)</b></td>
				<td><b>Количество шт. в уп. (розница)</b></td>
			</tr>
			<?php foreach($sizes as $size):?>
			<tr>
				<td>
					<img src="<?=$size->image?>">
				</td>
				<td><?=$size->size?></td>
				<td><?=$size->count_opt?></td>
				<td><?=$size->count_roz?></td>
			</tr>
			<?php endforeach;?>
		</table>

	</div>
</div>