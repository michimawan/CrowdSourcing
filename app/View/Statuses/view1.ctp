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
				<ul id="summary<?php echo $komen['KomentarStatus']['id_komentar'];?>" class="list-group col-xs-2">

				</ul>
				
				<div class='expand col-xs-1' >
					<button type="button" class="btn btn-default" attr="<?php echo $komen['KomentarStatus']['id_komentar'];?>">
						<span class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span>
					</button>
				</div>
				<div id="detailLabels<?php echo $komen['KomentarStatus']['id_komentar'];?>" class='dispnone col-xs-4 list-group'></div>
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
	echo $this->element('detailkomentar2');
	?>
	});
	</script>

<div class="paging">
	<?php 
		echo $this->Paginator->prev() .'  '. $this->Paginator->numbers(array('before'=>false, 'after'=>false,'separator'=> false)) .'  '. $this->Paginator->next();
	?>
</div>