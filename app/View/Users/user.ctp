<div class='profile'>
	<div class='img left col-xs-4'>
		<img src="<?php echo $this->Auth->user()['picture']?>" class='img-rounded img-responsive'>
		
	</div>
	<div class='information left col-xs-6'>
		<h1>Selamat datang, <?php echo $this->Auth->user()['display_name']; ?></h1>
		<h3>Total Labeling: <?php echo $users[0]['User']['total_label'];?></h3>
		<?php

		if($tambahlabel == true)
			echo "<h4>Anda sudah melabeli semua komentar yang disediakan !</h4>";
		else if($canlabel == false)
			echo "<h4>Tidak ada komentar yang bisa dilabeli lagi !</h4>";
		else 
			echo $this->Html->link('beri label',
				array('controller' => 'Statuses', 'action'=>'labeling', $this->Auth->user()['social_network_id']),
				array('class' => 'btn btn-primary')
			);
		?>
	</div>
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
			<td></td>
		</thead>
		<tbody>
			<?php
			$i = 1;
			if($datas == null){
				?>
			<tr>
				<td colspan='6'><center>Belum ada komentar yang dilabeli</center></td>
			</tr>
				<?php
			}
			else {
			foreach ($datas as $labels) {
			?>
			<tr>
				<td><?php echo $i; ?></td>
				<td><?php echo $labels['Komentar']['Status']['teks_status']; ?></td>
				<td><?php echo $labels['Komentar']['komentar']; ?></td>
				<td><?php echo $labels['Label']['waktu_melabel']; ?></td>
				<td><?php echo $labels['Label']['nama_label'] ?></td>
				<td>
					<?php 
					if($lockstate == 'false')
					echo $this->Html->link(
						'edit',
						array('controller' => 'Statuses', 'action' => 'edit', $labels['Komentar']['id_komentar'], $labels['Label']['id_label']),
						array('class' => 'btn btn-primary btn-sm')
					);
					?>
				</td>
			</tr>
			<?php
			$i++;
			}
			}
			?>
		</tbody>
	</table>
</div>

<div class="paging">
	<?php 
		echo $this->Paginator->prev() .'  '. $this->Paginator->numbers(array('before'=>false, 'after'=>false,'separator'=> false)) .'  '. $this->Paginator->next();
	?>
</div>

<?php
	if(isset($this->params->query['limit']))
		$limit = $this->params->query['limit'];
	else $limit = 5;
	$options = array(5 => '5', 10 => '10', 25 => '25', 50 => '50', 100 => '100');
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
