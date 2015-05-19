<?php
	$userid = $this->params['pass'][0];
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
			array('action'=>'labeling', $userid, $data['KomentarStatus']['id_komentar'], $data['KomentarStatus']['id_status']),
			array('class'=> 'btn btn-primary auto')
		);
		echo "</div>";
		
		echo "<div class='col-xs-4'>";
		echo $this->Form->postLink('Netral', 
			array('action'=>'labeling', $userid, $data['KomentarStatus']['id_komentar'], $data['KomentarStatus']['id_status']),
			array('class'=> 'btn btn-warning auto')
		);
		echo "</div>";
		
		echo "<div class='col-xs-4'>";
		echo $this->Form->postLink('Negatif', 
			array('action'=>'labeling', $userid, $data['KomentarStatus']['id_komentar'], $data['KomentarStatus']['id_status']),
			array('class'=> 'btn btn-danger auto')
		);
		echo "</div>";

		
		?>
		</div>
		<?php
		echo "<div class='finish'>";
		echo "<div class='col-xs-12'>";
		echo $this->Form->postLink('Selesai', 
			array('controller' => 'users','action'=>'user', $userid),
			array('class'=> 'btn btn-success btn-lg ')
		);
		echo "</div>";
		echo "</div>";
		?>
<?php 
	}
	?>