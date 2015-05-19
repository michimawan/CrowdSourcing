<?php
	$i = 1;
	foreach ($datas as $data) {
	?>
	<div class="jumbotron">
		
		<p><?php echo $data['Status']['nama_pembuat'];?></p>
		<h3><?php echo $data['Status']['teks_status'];?></h3>

	</div>

	<h3>Komentar:</h3>
		<?php
		foreach ($datas as $komen) {
		?>
		<div class="comment">
			<h5><?php echo $komen['KomentarStatus']['nama_pembuat']; ?></h5>
			<p><?php echo $komen['KomentarStatus']['komentar']; ?></p>
			<div class='clear'></div>

		</div>
		<div class='clear'></div>
		<div class='response'>
		<?php
		}

		echo "<div class='col-xs-4'>";
		echo $this->Form->postLink('Positif', 
			array('action'=>'edit', $labels['TabelLabel']['id_label'], 'positif'),
			array('class'=> 'btn btn-primary auto ')
		);
		echo "</div>";
		
		echo "<div class='col-xs-4'>";
		echo $this->Form->postLink('Netral', 
			array('action'=>'edit', $labels['TabelLabel']['id_label'], 'netral'),
			array('class'=> 'btn btn-warning auto')
		);
		echo "</div>";
		
		echo "<div class='col-xs-4'>";
		echo $this->Form->postLink('Negatif', 
			array('action'=>'edit', $labels['TabelLabel']['id_label'], 'negatif'),
			array('class'=> 'btn btn-danger auto')
		);
		echo "</div>";

		
		?>
		</div>
		<div class='dispnone' id='selected' attr="<?php echo $labels['TabelLabel']['nama_label']?>"></div>
<?php 
	}
	?>
	<script type="text/javascript">
		$(document).ready(function(){
			selectedlabel();
	<?php
	echo $this->element('selectedlabel');
	?>
	});
	</script>