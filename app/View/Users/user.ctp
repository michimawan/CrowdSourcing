<div class='profile'>
	<div class='img left col-xs-4'>
		<?php echo $this->Html->image('user.png', array('alt' => 'user', 'class'=>'img-rounded img-responsive')); ?>
	</div>
	<div class='information left col-xs-6'>
		<h1>Selamat datang, <?php echo $users[0]['User']['nick_name']; ?></h1>
		<h3>Total Labeling: <?php echo $users[0]['User']['total_label']; ?></h3>
		<?php
		echo $this->Html->link('beri label',
			array('controller' => 'Statuses', 'action'=>'labeling', $users[0]['User']['id']),
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
			foreach ($datas as $labels) {
			?>
			<tr>
				<td><?php echo $i; ?></td>
				<td><?php echo $labels['KomentarStatus']['Status']['teks_status']; ?></td>
				<td><?php echo $labels['KomentarStatus']['komentar']; ?></td>
				<td><?php echo $labels['TabelLabel']['waktu_melabel']; ?></td>
				<td><?php echo $labels['TabelLabel']['nama_label'] ?></td>
				<td>
					<?php 
					echo $this->Html->link(
						'edit',
						array('controller' => 'Statuses', 'action' => 'edit', $labels['KomentarStatus']['id_komentar'], $labels['TabelLabel']['id_label']),
						array('class' => 'btn btn-primary btn-sm')
					);
					?>
				</td>
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
</div>

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