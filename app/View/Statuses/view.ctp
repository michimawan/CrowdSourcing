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
		if($komen['Komentar']['status'] == 'lengkap'){
			echo "<div class='comment lengkap'>";
		} else {
			echo "<div class='comment'>";
		}
		?>
			<h5><?php echo $komen['Komentar']['nama_pembuat']; ?></h5>
			<p><?php echo $komen['Komentar']['komentar']; ?></p>
			<div class='clear'></div>
			<?php
				$pos = $net = $neg = 0;
				$arrs[] = Array();
				$arrs['pos'] = $arrs['net'] = $arrs['neg']  = "";
				foreach ($komen['Label'] as $data) {
					if($data['nama_label'] == 'positif'){
						$pos++;
						if(strlen($arrs['pos']) == 0)
							$arrs['pos'] = $arrs['pos'].$data['username_pelabel'];
						else 
							$arrs['pos'] .= "<br>".$data['username_pelabel'];
					}
					else if($data['nama_label'] == 'netral'){
						$net++;
						if(strlen($arrs['net']) == 0)
							$arrs['net'] = $arrs['net'].$data['username_pelabel'];
						else 
							$arrs['net'] .= "<br>".$data['username_pelabel'];
					}
					else {
						$neg++;
						if(strlen($arrs['neg']) == 0)
							$arrs['neg'] = $arrs['neg'].$data['username_pelabel'];
						else 
							$arrs['neg'] .= "<br>".$data['username_pelabel'];
					} 
				}

			?>
			<div id="labels<?php echo $komen['Komentar']['id_komentar'];?>" >
				<ul class="nav nav-pills" role="tablist">
				  	<li role="presentation"><a href="javascript:void(0)">Sudah dilabeli<span class="badge"><?php echo $komen['Komentar']['jml_label']."x";?></span></a></li>
				  	
				  	<?php if($maxlabel - $komen['Komentar']['jml_label'] > 0){ ?>
				  	<li role="presentation"><a href="javascript:void(0)">kurang<span class="badge"><?php echo ($maxlabel - $komen['Komentar']['jml_label'])."x";?></span></a></li>
				  	<?php } ?>

				  	<li role="presentation" attr="pos-<?php echo $komen['Komentar']['id_komentar'];?>"><a href="javascript:void(0)">Positif<span class="badge"><?php echo $pos;?></span></a></li>
				  	<li role="presentation" attr="net-<?php echo $komen['Komentar']['id_komentar'];?>"><a href="javascript:void(0)">Netral<span class="badge"><?php echo $net;?></span></a></li>
				  	<li role="presentation" attr="neg-<?php echo $komen['Komentar']['id_komentar'];?>"><a href="javascript:void(0)">Negatif<span class="badge"><?php echo $neg;?></span></a></li>
				  	
				  	
				  	<li role="presentation"><a href="javascript:void(0)">Status:<span class="badge"><?php echo $komen['Komentar']['status'];?></span></a></li>
				</ul>

				<div class="panel panel-default xs3 dispnone" id="pos-<?php echo $komen['Komentar']['id_komentar'];?>">
					<div class="panel-heading">Positif</div>
					<div class="panel-body">
				    	<p><?php echo $arrs['pos']?></p>
					</div>
				</div>

				<div class="panel panel-default xs3 dispnone" id="net-<?php echo $komen['Komentar']['id_komentar'];?>">
					<div class="panel-heading">Netral</div>
					<div class="panel-body">
				    	<p><?php echo $arrs['net']?></p>
					</div>
				</div>

				<div class="panel panel-default xs3 dispnone" id="neg-<?php echo $komen['Komentar']['id_komentar'];?>">
					<div class="panel-heading">Negatif</div>
					<div class="panel-body">
				    	<p><?php echo $arrs['neg']?></p>
					</div>
				</div>
			</div>

			<div class='clear'></div>	
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
