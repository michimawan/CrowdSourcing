<?php

	echo $this->Html->link('lihat daftar status', array('action' => 'index'), array('class' => 'btn btn-primary back'));

	$i = 1;
	foreach ($status as $data) {
	?>
	<div class="jumbotron">
		
		<p><?php echo $data['Status']['nama_pembuat'];?></p>
		<h3><?php echo $data['Status']['teks_status'];?></h3>

	</div>

	<h3>Komentar:</h3>
		<?php
		foreach ($komentars as $komen) {
		?>
		<div class="comment">
			<h5><?php echo $komen['KomentarStatus']['nama_pembuat']; ?></h5>
			<p><?php echo $komen['KomentarStatus']['komentar']; ?></p>
			<div class='clear'></div>
			
			<div class="dispnone" id="labels<?php echo $komen['KomentarStatus']['id_komentar'];?>" >
				<ul id="summary<?php echo $komen['KomentarStatus']['id_komentar'];?>" class="list-group col-xs-4">
					<li class='list-group-item'>
						<span class='badge'></span>
						<h4 class='list-group-item-heading'>Positif</h4>
						<p class='dispnone'></p>
					</li>
					<li class='list-group-item'>
						<span class='badge'></span>
						<h4 class='list-group-item-heading'>Netral</h4>
						<p class='dispnone'></p>
					</li>
					<li class='list-group-item'>
						<span class='badge'></span>
						<h4 class='list-group-item-heading'>Negatif</h4>
						<p class='dispnone'></p>
					</li>
				</ul>
				<ul class="nav nav-pills" role="tablist">
				  	<li role="presentation"><a href="#">Positif<span class="badge">42</span></a></li>
				  	<li role="presentation"><a href="#">Netral<span class="badge">0</span></a></li>
				  	<li role="presentation"><a href="#">Negatif<span class="badge">3</span></a></li>
				</ul>
				<div class='positip jumbotron'>
					<p>
						coco
					</p>
				</div>
			</div>

			<div class='clear'></div>
			<?php
			echo $this->Html->link('lihat label', '#',
				array('class' => 'detail btn btn-info', 'id'=> $komen['KomentarStatus']['id_komentar'])
			);
			?>
			
		</div>
		<div class='clear'></div>
		<?php
		}
		?>
<?php 
	}
	?>
	<script type="text/javascript">
		$(document).ready(function(){
	<?php
	echo $this->element('detailkomentar');
	?>
	});
	</script>

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