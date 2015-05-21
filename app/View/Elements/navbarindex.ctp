<div class="navbar navbar-default navbar-fixed-top">
  	<div class="container">
    	<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
				<span class="icon-bar"></span>
			</button>
			<!-- menampilkan judul dr web -->
			<div>
				<span class="navbar-brand">Crowd Sourcing Facebook</span>
			</div>
		</div>

		<div class="collapse navbar-collapse">
			<ul class="nav navbar-nav right">
				<?php
					if($this->Auth->user())
						echo $this->Html->link( "Logout",   array('controller' => 'Users', 'action'=>'logout') ); 
					else 	
						/*
						echo $this->Html->link($this->Html->image(array(
						    "alt" => "Signin with Google",
						    'url' => array('action'=>'social_login', 'Google')),
							array('class' => 'img-rounded img-responsive', 'escape' => false);
						*/
						
						echo $this->Html->image("login-google.jpg", array(
						    "alt" => "Signin with Google",
						    'url' => array('action'=>'social_login', 'Google'),

						));
						
				?>
				<!--
				<li><a class="login" href="'.$authUrl.'"><?php echo $this->Html->image('sign-in-button.png', array('alt' => 'Login with G+')); ?></a></li>
				-->
			</ul>
		</div>
  	</div>
</div>