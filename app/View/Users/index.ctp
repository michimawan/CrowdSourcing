<?php
	$positif = $negatif = $netral = $sum = 0;
	foreach ($labels as $label) {
		
		if($label['TabelLabel']['nama_label'] == 'positif')
			$positif++;
		else if($label['TabelLabel']['nama_label'] == 'negatif')
			$negatif++;
		else if($label['TabelLabel']['nama_label'] == 'netral')
			$netral++;

		$sum++;
	}

	
	$price = $json->price;
	$banyaklabel = $json->n;

	$total = $banyakkomen * $banyaklabel;
	$positif = $positif / $total * 100;
	$negatif = $negatif / $total * 100;
	$netral = $netral / $total * 100;
	$belum = ($total - $sum) / $total * 100;
?>
	<div class='hidden' id='dataPoint' attribute="<?php echo $positif.'-'.$negatif.'-'.$netral.'-'.$belum.'-'.$banyaklabel.'-'.$banyakkomen; ?>"></div>
	
	<?php
	echo $this->element('chartadmin');
	?>

<div class='profile'>
	<div class='img col-xs-6'>
		<img src="<?php echo $this->Auth->user()['picture']?>" class='img-rounded img-responsive'>
		<?php //echo $this->Html->image('user.png', array('alt' => 'user', 'class'=>'img-rounded img-responsive')); ?>
	</div>
	<div class='setting right col-xs-2'>
		
		<?php echo $this->Html->image('setting.png', array('alt' => 'setting')); ?>
		<div class='settings dispnone'>
			  	<div class="form-group">
			    	<label for="hargaperlabel">Harga per Label</label>
			    	<input type="number" class="form-control" id="hargaperlabel" placeholder="Harga per Label" value="<?php echo $price; ?>">
			  	</div>
			  	<div class="form-group">
			    	<label for="labelperkomen">Label per Komentar</label>
			    	<input type="number" class="form-control" id="labelperkomen" placeholder="Label per Komentar" value="<?php echo $banyaklabel; ?>">
			  	</div>
			  	<div>
			  		<button class="btn btn-default submit right">Submit</button>
			  	</div>
		</div>
	</div>
	<div class='information col-xs-6'>
		<h1>Selamat datang, <?php echo $admin[0]['User']['display_name']; ?></h1>
		<h3>Data terlabel: <?php echo $sum; ?></h3>
		<h3>Data belum terlabel: <?php echo $total-$sum; ?></h3>
		<?php 
		if(isset($lockstate)){
			if($lockstate == 'false')
				echo $this->Html->link("Non-aktifkan Pelabelan !", array('action'=>'setLock', 'true'), array('class' => 'btn btn-primary'));
			else if($lockstate == 'true')
				echo $this->Html->link("Aktifkan Pelabelan !", array('action'=>'setLock', 'false'), array('class' => 'btn btn-primary'));
		
		?>
		<br><br>
		<?php
		echo $this->Html->link("Export Semua Komentar (.csv)", array('controller' => 'statuses','action'=>'expKomentar'), array('class' => 'btn btn-primary'));
		}
		?>

	</div>
	<div class='clear'></div>
	<div id="chart" style="height: 400px; width: 100%;"></div>
	<div class='clear'></div>
</div>
<div class='history'>
	<table class='table table-hover'>
		<thead>
			<td>No</td>
			<td>Username</td>
			<td>Nama Panggilan</td>
			<td>Jumlah Label</td>
			<td>Price</td>
			<td>Pengaturan</td>
			<td></td>
		</thead>
		<tbody>
			<?php
			$i = 1;
			foreach ($datas as $data) {
		
			?>
			<tr>
				<td><?php echo $i; ?></td>
				<td><?php echo $data['User']['email']; ?></td>
				<td><?php echo $data['User']['display_name']; ?></td>
				<td><?php echo $data['User']['total_label']; ?></td>
				<td class='payment' prices="<?php echo $price?>"><?php echo $data['User']['total_label'] * $price; ?></td>
				<td>
				<?php
                if( $data['User']['status'] != 0){
                    echo $this->Html->link("De-Activate", array('action'=>'useroff', $data['User']['email']));}else{
                    echo $this->Html->link("Re-Activate", array('action'=>'activate', $data['User']['email']));
                    }
                ?>
                </td>
				<td>
					<?php 
						echo $this->Html->link('Detail', array('action' => 'view', $data['User']['id']), array('class' => 'btn btn-info')); 
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


<script type="text/javascript">
	$(document).ready(function(){
	<?php
	echo $this->element('indexjs');
	?>
	});
</script>