<?php
	$userid = $this->Auth->user()['social_network_id'];
	$usernm = $this->Auth->user()['email'];
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
			<h5><?php echo $komen['Komentar']['nama_pembuat']; ?></h5>
			<p><?php echo $komen['Komentar']['komentar']; ?></p>
			<div class='clear'></div>

		</div>
		<div class='clear'></div>
		<div class='response'>
		<?php
		}

		echo "<div class='col-xs-4'>";
		echo $this->Form->postLink('Positif', 
			array('action'=>'labeling', $userid, $usernm, $data['Komentar']['id_komentar'], $data['Komentar']['id_status'], 'positif'),
			array('class'=> 'btn btn-primary auto', 'confirm' => 'anda yakin dengan pilihan ini?')
		);
		echo "</div>";
		
		echo "<div class='col-xs-4'>";
		echo $this->Form->postLink('Netral', 
			array('action'=>'labeling', $userid, $usernm, $data['Komentar']['id_komentar'], $data['Komentar']['id_status'], 'netral'),
			array('class'=> 'btn btn-warning auto', 'confirm' => 'anda yakin dengan pilihan ini?')
		);
		echo "</div>";
		
		echo "<div class='col-xs-4'>";
		echo $this->Form->postLink('Negatif', 
			array('action'=>'labeling', $userid, $usernm, $data['Komentar']['id_komentar'], $data['Komentar']['id_status'], 'negatif'),
			array('class'=> 'btn btn-danger auto', 'confirm' => 'anda yakin dengan pilihan ini?')
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
