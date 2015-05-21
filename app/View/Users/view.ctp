<?php
	$positif = $negatif = $netral = $sum = 0;
	foreach ($chart as $labels) {
		if($labels['TabelLabel']['nama_label'] == 'positif')
			$positif++;
		else if($labels['TabelLabel']['nama_label'] == 'negatif')
			$negatif++;
		else if($labels['TabelLabel']['nama_label'] == 'netral')
			$netral++;

		$sum++;
	}


	$positif = $positif / $sum * 100;
	$negatif = $negatif / $sum * 100;
	$netral = $netral / $sum * 100;
	echo $this->Html->link('Kembali', array('action' => 'index'), array('class' => 'btn btn-primary back'));
	?>
	<div class='hidden' id='dataPoint' attribute="<?php echo $positif.'-'.$negatif.'-'.$netral; ?>"></div>
	<?php
	echo $this->element('chart');
?>

<div class='profile'>
	<div class='img left col-xs-4'>
		<img src="<?php echo $users[0]['User']['picture']?>" class='img-rounded img-responsive'>
		<?php //echo $this->Html->image('user.png', array('alt' => 'user', 'class'=>'img-rounded img-responsive')); ?>
	</div>
	<div class='information left col-xs-3'>
		<h1><?php echo $users[0]['User']['display_name']; ?></h1>
		<h3>Total Labeling: <?php echo $users[0]['User']['total_label']; ?></h3>
	</div>
	<div id="chart" style="height: 300px; width: 40%;" class='right'></div>
	<div class='clear'></div>
</div>
<div class='history'>
	<table class='table table-hover'>
		<thead>
			<td>No</td>
			<td>Status</td>
			<td>Komentar</td>
			<td>Waktu Pelabelan</td>
			<td>Label</td>
		</thead>
		<tbody>
			<?php
			$i = 1;
			foreach ($datas as $labels) {
			?>
			<tr>
				<td><?php echo $i; ?></td>
				<td><?php echo $labels['KomentarStatus']['Status']['teks_status']; ?></td>
				<td><?php echo $labels['KomentarStatus']['komentar']; ?></td>
				<td><?php echo $labels['TabelLabel']['waktu_melabel']; ?></td>
				<td><?php echo $labels['TabelLabel']['nama_label'] ?></td>

			</tr>
			<?php
			$i++;
			}
			?>
		</tbody>
	</table>
</div>

<div class="paging">
	<?php 
		echo $this->Paginator->prev() .'  '. $this->Paginator->numbers(array('before'=>false, 'after'=>false,'separator'=> false)) .'  '. $this->Paginator->next();
	?>
	<?php
	if(isset($this->params->query['limit']))
		$limit = $this->params->query['limit'];
	else $limit = 5;
	$options = array(1=>'1', 5 => '5', 10 => '10', 25 => '25', 50 => '50', 100 => '100');
	echo $this->Form->create(array('type' => 'get', 'class' => 'pagingnumber'));
	echo $this->Form->select('limit', $options, array(
		'value' => $limit,
		'default' => 5,
		'empty' => false,
		'onChange' => 'this.form.submit();',
		'name' => 'limit'
		)
	);
	echo $this->Form->end();
?>
</div>

