<?php
if($this->Auth->user() == null);
else ;

$main = '';
$users = '';
$statuses= '';
$logouturl= $this->Html->url(array('controller'=>'users', 'action'=>'logout'));
$usersurl= $this->Html->url(array('controller'=>'users', 'action'=>'index'));
$statusesurl= $this->Html->url(array('controller'=>'statuses', 'action'=>'index'));

if($menu === 'main') {
	$main = 'class="active"';
}
else if($menu === 'users') {
	$users= 'class="active"';
}
else if ($menu === 'statuses') {
	$statuses= 'class="active"';
} 
?>


<div class="navbar navbar-default navbar-fixed-top">
  	<div class="container">
    	<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<div>
				<span class="navbar-brand">Crowd Sourcing Facebook</span>
			</div>
		</div>

		<div id='navbar' class="collapse navbar-collapse">
			<ul class="nav navbar-nav">
				<?php if($this->Auth->user()){?>
				<li <?php echo $users; ?>><a href="<?php echo $usersurl; ?>">Home</a></li>
				<?php } ?>
				<!-- <li <?php echo $users; ?>><a href="<?php echo $usersurl; ?>">User</a></li> -->
				<?php if($this->Auth->user()['role']=='admin'){?>
				<li <?php echo $statuses; ?>><a href="<?php echo $statusesurl; ?>">Status</a></li>
				<?php } ?>
			</ul>

			<ul class="nav navbar-nav navbar-right">
				
				<li id='navbar-login'>
					<?php if($this->Auth->user()){?>
					<a href="<?php echo $logouturl; ?>" class='logout'><span>Logout</span></a>
					<?php } else { 
						
						echo $this->Html->link(
							$this->Html->image("login-google.jpg", array("alt" => "Signin with Google")),
						    array('action'=>'social_login', 'Google'),
						    array('escape' => false, "class" => "coco")
						);
						
						/*
						echo $this->Html->image("login-google.jpg", array(
						    "alt" => "Signin with Google",
						    'url' => array('action'=>'social_login', 'Google'),
						    'class'=> 'lala'
						));
						*/
					} ?>
				</li>
			</ul>
		</div>
  	</div>
</div>