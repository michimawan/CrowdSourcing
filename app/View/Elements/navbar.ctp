<?php
$main = '';
$users = '';
$statuses= '';
$komentarstatuses= '';
$mainurl= $this->Html->url(array('controller'=>'mains', 'action'=>'index'));
$usersurl= $this->Html->url(array('controller'=>'users', 'action'=>'index'));
$statusesurl= $this->Html->url(array('controller'=>'statuses', 'action'=>'index'));

if($menu === 'main') {
	$main = 'class="active"';
	$mainurl = '#';
}
else if($menu === 'users') {
	$users= 'class="active"';
	$usersurl='#';
}
else if ($menu === 'statuses') {
	$statuses= 'class="active"';
	$statusesurl='#';
} 
?>

<div class="navbar navbar-default navbar-fixed-top">
  	<div class="container">
    	<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<!-- menampilkan judul dr web -->
			<div>
				<span class="navbar-brand">Crowd Sourcing Facebook</span>
			</div>
		</div>

		<div class="collapse navbar-collapse">
			<ul class="nav navbar-nav">
				<li <?php echo $main; ?>><a href="<?php echo $mainurl; ?>">Home</a></li>
				<li <?php echo $users; ?>><a href="<?php echo $usersurl; ?>">User</a></li>
				<li <?php echo $statuses; ?>><a href="<?php echo $statusesurl; ?>">Status</a></li>
			</ul>
		</div>
  	</div>
</div>