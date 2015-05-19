<header>
<h3>Daftar Siswa</h3>
</header>
<?php
debug($datas);
?>

<?php
echo $this->Html->link('Kembali', array('controller'=>'statuses','action'=>'index'), array('class'=>'btn btn-default'));
?>
<!-- 
<table>
	<thead>
		<tr>
			<td></td>
			<td>NIM</td>
			<td>Nama Lengkap</td>
			<td>Prodi</td>
		</tr>
	</thead>
	<tbody>
	<?php
/*	foreach($datas as $data){
	?>
	<tr>
		<td><?php echo $this->Html->link('Sunting', array('action'=>'edit', $data['Student']['id'])); 

		echo $this->Form->postLink(' | Hapus', 
									array('action'=>'delete', $data['Student']['id']),
									array('confirm'=>'apakah yakin mau hapus', $data['Student']['nama'])
									);

		echo $this->Html->link(' | Lihat Siswa', array('action'=>'see', $data['Prodi']['id']));
		echo $this->Html->link(' | Detil', array('action'=>'detil', $data['Student']['id']));
		?></td>
		<td><?= $this->Html->link($data['Student']['nim'],'#',
							array('class'=>'anim btn btn-warning', 'nim'=> $data['Student']['nim'])); ?>
		</td>
		<td><?php echo $data['Student']['nama']; ?></td>
		<td><?php echo $data['Prodi']['nama']; ?></td>
	</tr>
	<?php
	}
*/
	?>

	</tbody>
</table>
<div class="paging">
	<?php
//	echo $this->Paginator->prev().''.$this->Paginator->numbers().''.$this->Paginator->next();
	?>
</div>

<div id="infodetil" style="margin-top: 40px; padding: 60px 20px; background-color: #31D0B5;">
	[Detil Siswa]
</div>

-->